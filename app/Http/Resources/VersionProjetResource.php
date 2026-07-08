<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VersionProjetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_version,
            'numero_version' => $this->numero_version,
            'rapport_pdf' => $this->rapport_pdf ? asset('storage/'.$this->rapport_pdf) : null,
            'depot_github' => $this->depot_github,
            'date_depot' => $this->date_depot?->format('Y-m-d H:i'),
            'statut_version' => $this->statut_version,
        ];
    }
}