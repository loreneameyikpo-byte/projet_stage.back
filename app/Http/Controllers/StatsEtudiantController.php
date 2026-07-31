<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsEtudiantController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $utilisateur = $request->user();
        $projet = $utilisateur->projets()
            ->with(['versions', 'derniereVersion', 'encadreur', 'presentation.salle', 'presentation.jury.membres', 'paiements'])
            ->latest()
            ->first();

        $paiementReussi = $projet?->paiements->firstWhere('statut', 'reussi');
        $tarif = $utilisateur->promotion?->niveau?->tarif;

        return response()->json([
            'promotion' => $utilisateur->promotion?->intitule,
            'projet' => $projet ? [
                'id' => $projet->id_projet,
                'titre' => $projet->titre,
                'description' => $projet->description,
                'statut' => $projet->statut,
                'depot_github' => (bool) $projet->derniereVersion?->depot_github,
                'nb_versions' => $projet->versions->count(),
                'presentation' => $projet->presentation ? [
                    'date_presentation' => $projet->presentation->date_presentation?->format('Y-m-d'),
                    'heure_presentation' => $projet->presentation->heure_presentation,
                    'salle' => $projet->presentation->salle?->numero,
                    'salle_libelle' => $projet->presentation->salle?->libelle,
                    'jury_noms' => $projet->presentation->jury?->membres->pluck('nom')->join(', '),
                ] : null,
                'paiement' => [
                    'statut' => $paiementReussi ? 'reussi' : 'en_attente',
                    'montant' => $paiementReussi?->montant ?? $tarif?->montant ?? 0,
                ],
            ] : null,
        ]);
    }
}