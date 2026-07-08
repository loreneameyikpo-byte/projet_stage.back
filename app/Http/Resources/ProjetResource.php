<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_projet,
            'titre' => $this->titre,
            'description' => $this->description,
            'statut' => $this->statut,
            'etudiant' => [
                'id' => $this->etudiant?->id_utilisateur,
                'nom' => $this->etudiant?->nom,
                'prenom' => $this->etudiant?->prenom,
            ],
            'encadreur' => $this->when($this->id_encadreur, [
                'id' => $this->encadreur?->id_utilisateur,
                'nom' => $this->encadreur?->nom,
                'prenom' => $this->encadreur?->prenom,
            ]),
            'derniere_version' => new VersionProjetResource($this->whenLoaded('derniereVersion')),
            'versions' => VersionProjetResource::collection($this->whenLoaded('versions')),
            'observations' => ObservationResource::collection($this->whenLoaded('observations')),
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}