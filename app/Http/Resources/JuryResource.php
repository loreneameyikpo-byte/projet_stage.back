<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JuryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_jury,
            'membres' => $this->membres->map(fn ($membre) => [
                'id_utilisateur' => $membre->id_utilisateur,
                'nom' => $membre->nom,
                'prenom' => $membre->prenom,
                'role_jury' => $membre->pivot->role_jury,
                'note_saisie' => $membre->pivot->note_saisie,
            ]),
        ];
    }
}