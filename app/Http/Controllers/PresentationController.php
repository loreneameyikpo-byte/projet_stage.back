<?php

namespace App\Http\Controllers;

use App\Http\Requests\Presentation\PlanifierPresentationRequest;
use App\Http\Resources\PresentationResource;
use App\Models\Jury;
use App\Models\Presentation;
use App\Models\Projet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresentationController extends Controller
{
    /**
     * Liste des présentations, filtrée selon le rôle.
     */
    public function index(Request $request): JsonResponse
    {
        $utilisateur = $request->user();
        $role = $utilisateur->role?->libelle;

        $query = Presentation::with(['etudiant', 'projet', 'salle', 'jury.membres']);

        $query = match ($role) {
            'etudiant' => $query->where('id_utilisateur', $utilisateur->id_utilisateur),
            'encadreur', 'jury_externe' => $query->whereHas(
                'jury.membres',
                fn ($q) => $q->where('utilisateurs.id_utilisateur', $utilisateur->id_utilisateur)
            ),
            default => $query,
        };

        return response()->json([
            'presentations' => PresentationResource::collection($query->orderBy('date_presentation')->get()),
        ]);
    }

    /**
     * UC3 - Planifier une présentation : vérifie la disponibilité de la salle,
     * crée la présentation, le jury et affecte ses membres.
     */
    public function store(PlanifierPresentationRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $projet = Projet::findOrFail($validated['id_projet']);

        $presentation = DB::transaction(function () use ($validated, $projet) {
            $presentation = Presentation::create([
                'date_presentation' => $validated['date_presentation'],
                'heure_presentation' => $validated['heure_presentation'],
                'libelle' => $validated['libelle'] ?? null,
                'id_utilisateur' => $projet->id_utilisateur,
                'id_projet' => $projet->id_projet,
                'id_salle' => $validated['id_salle'],
            ]);

            $jury = Jury::create([
                'id_presentation' => $presentation->id_presentation,
            ]);

            foreach ($validated['membres'] as $membre) {
                $jury->membres()->attach($membre['id_utilisateur'], [
                    'role_jury' => $membre['role_jury'],
                ]);
            }

            $projet->update(['statut' => 'presentation_planifiee']);

            return $presentation;
        });

        return response()->json([
            'message' => 'Présentation planifiée avec succès.',
            'presentation' => new PresentationResource(
                $presentation->load(['etudiant', 'projet', 'salle', 'jury.membres'])
            ),
        ], 201);
    }

    public function show(Presentation $presentation): JsonResponse
    {
        return response()->json([
            'presentation' => new PresentationResource(
                $presentation->load(['etudiant', 'projet', 'salle', 'jury.membres'])
            ),
        ]);
    }


    /**
     * Vérifie si un créneau (salle + date + heure) est disponible,
     * sans créer la présentation — utilisé par le bouton "Vérifier la disponibilité".
     */
    public function verifierDisponibilite(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date_presentation' => ['required', 'date'],
            'heure_presentation' => ['required', 'date_format:H:i'],
            'id_salle' => ['required', 'uuid', 'exists:salles,id_salle'],
        ]);

        $conflit = Presentation::where('id_salle', $validated['id_salle'])
            ->where('date_presentation', $validated['date_presentation'])
            ->where('heure_presentation', $validated['heure_presentation'])
            ->exists();

        return response()->json([
            'disponible' => ! $conflit,
            'message' => $conflit
                ? 'Cette salle est déjà réservée sur ce créneau.'
                : 'Ce créneau est disponible.',
        ]);
    }

    /**
     * Annule une soutenance planifiée : supprime la présentation et son jury,
     * et remet le projet au statut "validé" pour permettre une nouvelle planification.
     */
    public function annuler(Presentation $presentation): JsonResponse
    {
        if ($presentation->date_presentation->isPast()) {
            return response()->json([
                'message' => 'Impossible d\'annuler une soutenance déjà passée.',
            ], 422);
        }

        DB::transaction(function () use ($presentation) {
            $presentation->projet->update(['statut' => 'valide']);
            $presentation->jury?->delete(); // cascade sur jury_utilisateur
            $presentation->delete();
        });

        return response()->json(['message' => 'Soutenance annulée avec succès.']);
    }
}