<?php

namespace App\Http\Controllers;

use App\Models\Presentation;
use App\Models\Projet;
use App\Models\Promotion;
use App\Models\Utilisateur;

use Illuminate\Http\JsonResponse;

class StatsAdminController extends Controller
{
    /**
     * Statistiques consolidées pour le tableau de bord du super administrateur.
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
     */
    public function dashboard(): JsonResponse
    {
        $totalEtudiants = Utilisateur::whereHas('role', fn ($q) => $q->where('libelle', 'etudiant'))->count();
        $totalEncadreurs = Utilisateur::whereHas('role', fn ($q) => $q->where('libelle', 'encadreur'))->count();
        $totalJuryExterne = Utilisateur::whereHas('role', fn ($q) => $q->where('libelle', 'jury_externe'))->count();
        $totalProjets = Projet::count();

        $repartitionProjets = Projet::selectRaw('statut, count(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        $projetsRecents = Projet::with(['etudiant', 'derniereVersion'])
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