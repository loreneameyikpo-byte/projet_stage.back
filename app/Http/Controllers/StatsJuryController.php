<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsJuryController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $utilisateur = $request->user();
        $jurys = $utilisateur->jurys()->with('presentation.projet.etudiant', 'presentation.salle')->get();

        $aVenir = $jurys->filter(fn ($j) => $j->presentation && $j->presentation->date_presentation?->isFuture());
        $notees = $jurys->filter(fn ($j) => $j->pivot->note_saisie !== null);

        return response()->json([
            'specialite' => $utilisateur->specialite?->libelle,
            'total_soutenances' => $jurys->count(),
            'a_venir' => $aVenir->count(),
            'notees' => $notees->count(),
            'en_attente_notation' => $jurys->count() - $notees->count(),
            'presentations' => $jurys->map(fn ($j) => [
                'id_jury' => $j->id_jury,
                'projet_titre' => $j->presentation?->projet?->titre,
                'etudiant' => trim("{$j->presentation?->projet?->etudiant?->prenom} {$j->presentation?->projet?->etudiant?->nom}"),
                'date_presentation' => $j->presentation?->date_presentation?->format('Y-m-d'),
                'heure_presentation' => $j->presentation?->heure_presentation,
                'salle' => $j->presentation?->salle?->numero,
                'role_jury' => $j->pivot->role_jury,
                'note_saisie' => $j->pivot->note_saisie,
                'commentaire' => $j->pivot->commentaire,
            ])->values(),
        ]);
    }
}