<?php

namespace App\Http\Requests\Utilisateur;

use Illuminate\Foundation\Http\FormRequest;

class SupprimerUtilisateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role?->libelle, ['administrateur', 'super_administrateur'], true);
    }

    public function rules(): array
    {
        return [];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $roleConnecte = $this->user()?->role?->libelle;
            $utilisateurCible = $this->route('utilisateur');
            $roleCible = $utilisateurCible->role?->libelle;

            $rolesReserves = ['administrateur', 'super_administrateur'];

            if (in_array($roleCible, $rolesReserves, true) && $roleConnecte !== 'super_administrateur') {
                $validator->errors()->add(
                    'utilisateur',
                    'Seul le super administrateur peut supprimer un compte administrateur ou super administrateur.'
                );
            }

            if ($utilisateurCible->id_utilisateur === $this->user()->id_utilisateur) {
                $validator->errors()->add(
                    'utilisateur',
                    'Vous ne pouvez pas supprimer votre propre compte.'
                );
            }
        });
    }
}