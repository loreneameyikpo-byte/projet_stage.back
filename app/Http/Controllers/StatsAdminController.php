<?php

namespace App\Http\Controllers;

use App\Models\Presentation;
use App\Models\Projet;
use App\Models\Promotion;
use App\Models\Utilisateur;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsAdminController extends Controller
{
    /**
     * Statistiques consolidées pour le tableau de bord du super administrateur.
     * Vue GLOBALE : toute la plateforme, sans filtrage par créateur.
     */
    public function global(): JsonResponse
    {
        $totalUtilisateurs = Utilisateur::count();
        $totalEtudiants = Utilisateur::whereHas('role', fn ($q) => $q->where('libelle', 'etudiant'))->count();
        $totalProjets = Projet::count();
        $totalSoutenances = Presentation::count();
        $totalAdministrateurs = Utilisateur::whereHas('role', fn ($q) => $q->where('libelle', 'administrateur'))->count();

        $comparaisonParPromotion = Promotion::with('niveau')
            ->get()
            ->map(function ($promotion) {
                $etudiantsIds = $promotion->utilisateurs()->pluck('id_utilisateur');

                return [
                    'intitule' => $promotion->intitule,
                    'nb_etudiants' => $etudiantsIds->count(),
                    'nb_projets' => Projet::whereIn('id_utilisateur', $etudiantsIds)->count(),
                ];
            })
            ->filter(fn ($p) => $p['nb_etudiants'] > 0)
            ->values();

        $repartitionProjets = Projet::selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        return response()->json([
            'total_utilisateurs' => $totalUtilisateurs,
            'total_etudiants' => $totalEtudiants,
            'total_projets' => $totalProjets,
            'total_soutenances' => $totalSoutenances,
            'total_administrateurs' => $totalAdministrateurs,
            'comparaison_par_promotion' => $comparaisonParPromotion,
            'repartition_projets' => [
                'en_attente' => $repartitionProjets['en_attente'] ?? 0,
                'corrections' => $repartitionProjets['corrections'] ?? 0,
                'valide' => $repartitionProjets['valide'] ?? 0,
                'presentation_planifiee' => $repartitionProjets['presentation_planifiee'] ?? 0,
                'presente' => $repartitionProjets['presente'] ?? 0,
            ],
        ]);
    }

    /**
     * Statistiques pour le tableau de bord de l'administrateur (vue standard).
     * Vue PERSONNELLE : limitée aux étudiants/encadreurs/jury externes que
     * CET administrateur a lui-même créés, et aux projets de ces étudiants.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $idAdmin = $request->user()->id_utilisateur;

        $totalEtudiants = Utilisateur::whereHas('role', fn ($q) => $q->where('libelle', 'etudiant'))
            ->where('cree_par', $idAdmin)
            ->count();

        $totalEncadreurs = Utilisateur::whereHas('role', fn ($q) => $q->where('libelle', 'encadreur'))
            ->where('cree_par', $idAdmin)
            ->count();

        $totalJuryExterne = Utilisateur::whereHas('role', fn ($q) => $q->where('libelle', 'jury_externe'))
            ->where('cree_par', $idAdmin)
            ->count();

        // Les projets n'ont pas de "créateur" propre : on les rattache aux
        // étudiants que cet admin a créés.
        $etudiantsIds = Utilisateur::whereHas('role', fn ($q) => $q->where('libelle', 'etudiant'))
            ->where('cree_par', $idAdmin)
            ->pluck('id_utilisateur');

        $totalProjets = Projet::whereIn('id_utilisateur', $etudiantsIds)->count();

        $repartitionProjets = Projet::whereIn('id_utilisateur', $etudiantsIds)
            ->selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        $projetsRecents = Projet::whereIn('id_utilisateur', $etudiantsIds)
            ->with(['etudiant', 'derniereVersion'])
            ->latest()
            ->take(4)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id_projet,
                'titre' => $p->titre,
                'etudiant' => trim("{$p->etudiant?->prenom} {$p->etudiant?->nom}"),
                'promotion' => $p->etudiant?->promotion?->intitule,
                'statut' => $p->statut,
            ]);

        return response()->json([
            'total_etudiants' => $totalEtudiants,
            'total_encadreurs' => $totalEncadreurs,
            'total_projets' => $totalProjets,
            'total_jury_externe' => $totalJuryExterne,
            'repartition_projets' => [
                'en_attente' => $repartitionProjets['en_attente'] ?? 0,
                'corrections' => $repartitionProjets['corrections'] ?? 0,
                'valide' => $repartitionProjets['valide'] ?? 0,
                'presentation_planifiee' => $repartitionProjets['presentation_planifiee'] ?? 0,
                'presente' => $repartitionProjets['presente'] ?? 0,
            ],
            'projets_recents' => $projetsRecents,
        ]);
    }
}