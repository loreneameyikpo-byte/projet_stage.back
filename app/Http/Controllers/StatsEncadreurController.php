<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsEncadreurController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $encadreur = $request->user();
        $projets = $encadreur->projetsEncadres()->with('etudiant', 'derniereVersion')->get();

        return response()->json([
            'specialite' => $encadreur->specialite?->libelle,
            'total_projets' => $projets->count(),
            'en_attente' => $projets->whereIn('statut', ['en_attente', 'corrections'])->count(),
            'valides' => $projets->where('statut', 'valide')->count(),
            'soutenances_a_venir' => $encadreur->jurys()
                ->whereHas('presentation', fn ($q) => $q->where('date_presentation', '>=', now()->toDateString()))
                ->count(),
            'projets_recents' => $projets->sortByDesc('created_at')->take(5)->values()->map(fn ($p) => [
                'id' => $p->id_projet,
                'etudiant' => trim("{$p->etudiant?->prenom} {$p->etudiant?->nom}"),
                'titre' => $p->titre,
                'statut' => $p->statut,
                'derniere_version' => $p->derniereVersion?->numero_version,
            ]),
        ]);
    }
}