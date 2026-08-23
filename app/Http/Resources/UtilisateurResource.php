<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UtilisateurResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_utilisateur,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'contacts' => $this->contacts,
            'adresse' => $this->adresse,
            'role' => $this->role?->libelle,
            'promotion' => $this->when($this->id_promotion, [
                'id' => $this->promotion?->id_promotion,
                'annee_debut' => $this->promotion?->annee_debut,
                'annee_fin' => $this->promotion?->annee_fin,
                'intitule' => $this->promotion?->intitule,
            ]),
            'filiere' => $this->filiere?->libelle,
            'specialite' => $this->specialite?->libelle,
            'actif' => $this->actif,
            'mot_de_passe_a_changer' => $this->mot_de_passe_a_changer,
            // Nom complet de l'admin qui a créé ce compte (null si créé
            // autrement, ex. le tout premier super admin).
            'cree_par' => $this->createur
                ? trim("{$this->createur->prenom} {$this->createur->nom}")
                : null,
            'created_at' => $this->created_at?->format('Y-m-d'),
        ];
    }
}