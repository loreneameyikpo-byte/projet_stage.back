<?php

namespace App\Http\Controllers;

use App\Http\Requests\Projet\DeposerVersionRequest;
use App\Http\Requests\Projet\SoumettreProjetRequest;
use App\Http\Requests\Projet\ValiderProjetRequest;
use App\Http\Resources\ProjetResource;
use App\Models\Observation;
use App\Models\Projet;
use App\Models\VersionProjet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjetController extends Controller
{
    /**
     * UC1 - Liste des projets, filtrée selon le rôle de l'utilisateur connecté.
     */
    public function index(Request $request): JsonResponse
    {
        $utilisateur = $request->user();
        $role = $utilisateur->role?->libelle;

        $query = Projet::with(['etudiant', 'encadreur', 'derniereVersion']);

        $query = match ($role) {
            'etudiant' => $query->where('id_utilisateur', $utilisateur->id_utilisateur),
            'encadreur' => $query->where('id_encadreur', $utilisateur->id_utilisateur),
            default => $query, // administrateur / super_administrateur voient tout
        };

        return response()->json([
            'projets' => ProjetResource::collection($query->latest()->get()),
        ]);
    }

    /**
     * UC1 - Soumettre un nouveau projet.
     */
    public function store(SoumettreProjetRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $projet = DB::transaction(function () use ($validated, $request) {
            $projet = Projet::create([
                'titre' => $validated['titre'],
                'description' => $validated['description'],
                'statut' => 'en_attente_validation',
                'id_utilisateur' => $request->user()->id_utilisateur,
                'id_encadreur' => null,
            ]);

            $cheminPdf = $request->file('rapport_pdf')->store('rapports', 'public');

            VersionProjet::create([
                'numero_version' => 1,
                'rapport_pdf' => $cheminPdf,
                'depot_github' => $validated['depot_github'] ?? null,
                'date_depot' => now(),
                'statut_version' => 'en_attente',
                'id_projet' => $projet->id_projet,
            ]);

            return $projet;
        });

        return response()->json([
            'message' => 'Projet soumis avec succès.',
            'projet' => new ProjetResource($projet->load(['etudiant', 'derniereVersion'])),
        ], 201);
    }

    /**
     * Affiche le détail d'un projet, avec historique complet.
     */
    public function show(Request $request, Projet $projet): JsonResponse
    {
        $this->authorize('view', $projet);

        return response()->json([
            'projet' => new ProjetResource(
                $projet->load(['etudiant', 'encadreur', 'versions', 'observations.auteur', 'derniereVersion'])
            ),
        ]);
    }

    /**
     * UC1 (extension) - Déposer une nouvelle version après corrections demandées.
     */
    public function deposerVersion(DeposerVersionRequest $request, Projet $projet): JsonResponse
    {
        $validated = $request->validated();

        $projet = DB::transaction(function () use ($validated, $request, $projet) {
            $derniereVersion = $projet->versions()->max('numero_version') ?? 0;

            $cheminPdf = $request->file('rapport_pdf')->store('rapports', 'public');

            VersionProjet::create([
                'numero_version' => $derniereVersion + 1,
                'rapport_pdf' => $cheminPdf,
                'depot_github' => $validated['depot_github'] ?? $projet->derniereVersion?->depot_github,
                'date_depot' => now(),
                'statut_version' => 'en_attente',
                'id_projet' => $projet->id_projet,
            ]);

            $projet->update(['statut' => 'en_attente_validation']);

            return $projet;
        });

        return response()->json([
            'message' => 'Nouvelle version déposée avec succès.',
            'projet' => new ProjetResource($projet->load(['derniereVersion', 'versions'])),
        ]);
    }

    /**
     * UC2 - L'encadreur valide le projet ou demande des corrections.
     */
    public function valider(ValiderProjetRequest $request, Projet $projet): JsonResponse
    {
        $validated = $request->validated();

        $projet = DB::transaction(function () use ($validated, $request, $projet) {
            $derniereVersion = $projet->derniereVersion;

            Observation::create([
                'contenu' => $validated['observation'],
                'date' => now()->toDateString(),
                'id_utilisateur' => $request->user()->id_utilisateur,
                'id_projet' => $projet->id_projet,
            ]);

            if ($validated['decision'] === 'valider') {
                $derniereVersion?->update(['statut_version' => 'validee']);
                $projet->update(['statut' => 'valide']);
            } else {
                $derniereVersion?->update(['statut_version' => 'corrections_demandees']);
                $projet->update(['statut' => 'corrections_demandees']);
            }

            return $projet;
        });

        return response()->json([
            'message' => $validated['decision'] === 'valider'
                ? 'Projet validé avec succès.'
                : 'Corrections demandées à l\'étudiant.',
            'projet' => new ProjetResource($projet->load(['derniereVersion', 'observations.auteur'])),
        ]);
    }
}