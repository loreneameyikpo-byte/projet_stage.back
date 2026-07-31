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
                'promotion' => $this->etudiant?->promotion?->intitule,
            ],
            'encadreur' => $this->when($this->id_encadreur, [
                'id' => $this->encadreur?->id_utilisateur,
                'nom' => $this->encadreur?->nom,
                'prenom' => $this->encadreur?->prenom,
            ]),
            'derniere_version' => new VersionProjetResource($this->whenLoaded('derniereVersion')),
            'versions' => VersionProjetResource::collection($this->whenLoaded('versions')),
            'observations' => ObservationResource::collection($this->whenLoaded('observations')),
            'presentation' => $this->whenLoaded('presentation', fn () => $this->presentation ? [
                'date_presentation' => $this->presentation->date_presentation?->format('l d F Y'),
                'heure_presentation' => $this->presentation->heure_presentation,
                'salle' => $this->presentation->salle?->numero,
                'salle_libelle' => $this->presentation->salle?->libelle,
                'jury' => $this->presentation->jury?->membres->map(fn ($m) => [
                    'id' => $m->id_utilisateur,
                    'nom' => $m->nom,
                    'prenom' => $m->prenom,
                    'role_jury' => $m->pivot->role_jury,
                ]),
            ] : null),
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}