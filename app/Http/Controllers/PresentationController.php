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
use Illuminate\Validation\ValidationException;

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

        if ($projet->statut !== 'valide') {
            throw ValidationException::withMessages([
                'id_projet' => 'Seul un projet au statut "validé" peut être planifié.',
            ]);
        }

        if ($projet->presentation()->exists()) {
            throw ValidationException::withMessages([
                'id_projet' => 'Ce projet a déjà une présentation planifiée.',
            ]);
        }

        // Vérification de la disponibilité de la salle sur ce créneau
        $conflitSalle = Presentation::where('id_salle', $validated['id_salle'])
            ->where('date_presentation', $validated['date_presentation'])
            ->where('heure_presentation', $validated['heure_presentation'])
            ->exists();

        if ($conflitSalle) {
            throw ValidationException::withMessages([
                'id_salle' => 'Cette salle est déjà réservée sur ce créneau.',
            ]);
        }

        // Vérification que les membres du jury sont libres sur ce créneau
        $idsMembres = collect($validated['membres'])->pluck('id_utilisateur');
        $conflitJury = Jury::whereHas('presentation', function ($q) use ($validated) {
            $q->where('date_presentation', $validated['date_presentation'])
              ->where('heure_presentation', $validated['heure_presentation']);
        })->whereHas('membres', function ($q) use ($idsMembres) {
            $q->whereIn('utilisateurs.id_utilisateur', $idsMembres);
        })->exists();

        if ($conflitJury) {
            throw ValidationException::withMessages([
                'membres' => 'Un ou plusieurs membres du jury sont déjà mobilisés sur ce créneau.',
            ]);
        }

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
}