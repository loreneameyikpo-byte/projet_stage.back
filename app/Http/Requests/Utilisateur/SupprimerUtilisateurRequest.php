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
                return;
            }

            if ($utilisateurCible->id_utilisateur === $this->user()->id_utilisateur) {
                $validator->errors()->add(
                    'utilisateur',
                    'Vous ne pouvez pas supprimer votre propre compte.'
                );
                return;
            }
            match ($roleCible) {
                'etudiant' => $this->verifierEtudiant($validator, $utilisateurCible),
                'encadreur' => $this->verifierEncadreur($validator, $utilisateurCible),
                'jury_externe' => $this->verifierJuryExterne($validator, $utilisateurCible),
                default => null,
            };
        });
    }

    private function verifierEtudiant($validator, $utilisateur): void
    {
        $nbProjets = $utilisateur->projets()->count();

        if ($nbProjets > 0) {
            $validator->errors()->add(
                'utilisateur',
                "Impossible de supprimer cet étudiant : il a {$nbProjets} projet(s) associé(s)."
            );
        }
    }

    private function verifierEncadreur($validator, $utilisateur): void
    {
        $nbProjetsEncadres = $utilisateur->projetsEncadres()->count();
        $nbJurys = $utilisateur->jurys()->count();

        if ($nbProjetsEncadres > 0) {
            $validator->errors()->add(
                'utilisateur',
                "Impossible de supprimer cet encadreur : il encadre actuellement {$nbProjetsEncadres} projet(s)."
            );
            return;
        }

        if ($nbJurys > 0) {
            $validator->errors()->add(
                'utilisateur',
                "Impossible de supprimer cet encadreur : il est membre de {$nbJurys} jury(s)."
            );
        }
    }

    private function verifierJuryExterne($validator, $utilisateur): void
    {
        $nbJurys = $utilisateur->jurys()->count();

        if ($nbJurys > 0) {
            $validator->errors()->add(
                'utilisateur',
                "Impossible de supprimer ce membre : il est affecté à {$nbJurys} jury(s)."
            );
        }
    }
}
    