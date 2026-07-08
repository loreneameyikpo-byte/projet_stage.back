<?php

namespace App\Policies;

use App\Models\Projet;
use App\Models\Utilisateur;

class ProjetPolicy
{
    public function view(Utilisateur $utilisateur, Projet $projet): bool
    {
        $role = $utilisateur->role?->libelle;

        return match ($role) {
            'etudiant' => $utilisateur->id_utilisateur === $projet->id_utilisateur,
            'encadreur' => $utilisateur->id_utilisateur === $projet->id_encadreur,
            'administrateur', 'super_administrateur' => true,
            default => false,
        };
    }

    public function update(Utilisateur $utilisateur, Projet $projet): bool
    {
        return $utilisateur->id_utilisateur === $projet->id_utilisateur
            && in_array($projet->statut, ['en_attente', 'corrections'], true);
    }
}