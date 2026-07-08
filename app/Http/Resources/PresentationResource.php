<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PresentationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_presentation,
            'date_presentation' => $this->date_presentation?->format('Y-m-d'),
            'heure_presentation' => $this->heure_presentation,
            'libelle' => $this->libelle,
            'note_finale' => $this->note_finale,
            'etudiant' => [
                'id' => $this->etudiant?->id_utilisateur,
                'nom' => $this->etudiant?->nom,
                'prenom' => $this->etudiant?->prenom,
            ],
            'projet' => [
                'id' => $this->projet?->id_projet,
                'titre' => $this->projet?->titre,
            ],
            'salle' => [
                'id' => $this->salle?->id_salle,
                'numero' => $this->salle?->numero,
                'libelle' => $this->salle?->libelle,
            ],
            'jury' => new JuryResource($this->whenLoaded('jury')),
        ];
    }
}