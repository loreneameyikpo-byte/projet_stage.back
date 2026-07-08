<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ObservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_observation,
            'contenu' => $this->contenu,
            'date' => $this->date?->format('Y-m-d'),
            'auteur' => [
                'id' => $this->auteur?->id_utilisateur,
                'nom' => $this->auteur?->nom,
                'prenom' => $this->auteur?->prenom,
            ],
        ];
    }
}