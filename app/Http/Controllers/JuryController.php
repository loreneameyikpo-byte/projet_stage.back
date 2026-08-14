<?php

namespace App\Http\Controllers;

use App\Http\Requests\Jury\SaisirNoteRequest;
use App\Http\Resources\JuryResource;
use App\Models\Jury;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JuryController extends Controller
{


    /**
     * Détail d'une soutenance pour un membre du jury (interne ou externe),
     * limité aux jurys dont l'utilisateur connecté fait partie.
     */
    public function show(Request $request, Jury $jury): JsonResponse
    {
        $utilisateur = $request->user();

        $jury->load([
            'presentation.projet.etudiant.promotion',
            'presentation.projet.etudiant.specialite',
            'presentation.salle',
            'membres',
        ]);

        $monPivot = $jury->membres->firstWhere('id_utilisateur', $utilisateur->id_utilisateur);
        abort_unless($monPivot !== null, 403, "Vous ne faites pas partie de ce jury.");
 
        return response()->json([
            'id_jury' => $jury->id_jury,
            'date_presentation' => $jury->presentation?->date_presentation?->format('Y-m-d'),
            'heure_presentation' => $jury->presentation?->heure_presentation,
            'statut' => $jury->presentation?->statut,
            'salle' => [
                'numero' => $jury->presentation?->salle?->numero,
                'libelle' => $jury->presentation?->salle?->libelle,
            ],
            'etudiant' => [
                'prenom' => $jury->presentation?->projet?->etudiant?->prenom,
                'nom' => $jury->presentation?->projet?->etudiant?->nom,
                'email' => $jury->presentation?->projet?->etudiant?->email,
                'telephone' => $jury->presentation?->projet?->etudiant?->contacts,
                'promotion' => $jury->presentation?->projet?->etudiant?->promotion?->intitule,
                'specialite' => $jury->presentation?->projet?->etudiant?->specialite?->libelle,
            ],
            'projet' => [
                'titre' => $jury->presentation?->projet?->titre,
                'description' => $jury->presentation?->projet?->description,
                'depot_github' => $jury->presentation?->projet?->depot_github,
                'rapport_pdf' => $jury->presentation?->projet?->rapport_pdf,
            ],
            'jury' => [
                'membres' => $jury->membres->map(fn ($m) => [
                    'id_utilisateur' => $m->id_utilisateur,
                    'nom' => $m->nom,
                    'prenom' => $m->prenom,
                    'role_jury' => $m->pivot->role_jury,
                    'note_saisie' => $m->pivot->note_saisie,
                    'est_utilisateur_courant' => $m->id_utilisateur === $utilisateur->id_utilisateur,
                ]),
            ],
        ]);
    }
    /**
     * Saisie de la note par un membre du jury (interne ou externe),
     * pour sa propre participation uniquement.
     */
    public function saisirNote(SaisirNoteRequest $request, Jury $jury): JsonResponse
    {
        $validated = $request->validated();
        $utilisateur = $request->user();

        DB::transaction(function () use ($jury, $utilisateur, $validated) {
            $jury->membres()->updateExistingPivot($utilisateur->id_utilisateur, [
                'note_saisie' => $validated['note'],
            ]);

            $noteFinale = $jury->calculerNoteFinale();

            if ($noteFinale !== null) {
                $jury->presentation->update(['note_finale' => $noteFinale]);
                $jury->presentation->projet?->update(['statut' => 'presente']);
            }
        });

        $jury->load('presentation.projet.etudiant');
        NotificationService::notifierSuperAdmins(
            'note_saisie',
            "{$utilisateur->prenom} {$utilisateur->nom} a saisi une note pour la soutenance de {$jury->presentation?->projet?->etudiant?->prenom} {$jury->presentation?->projet?->etudiant?->nom}.",
            '/admin/presentations'
        );

        return response()->json([
            'message' => 'Note enregistrée avec succès.',
            'jury' => new JuryResource($jury->fresh(['membres'])),
        ]);
    }
}