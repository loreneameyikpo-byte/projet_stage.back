<?php

namespace App\Http\Requests\Utilisateur;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModifierUtilisateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        $roleConnecte = $this->user()?->role?->libelle;

        if (! in_array($roleConnecte, ['administrateur', 'super_administrateur'], true)) {
            return false;
        }

        $utilisateurCible = $this->route('utilisateur');
        $roleCibleActuel = $utilisateurCible->role?->libelle;

        // Un administrateur ne peut pas modifier un compte administrateur ou super administrateur.
        if (in_array($roleCibleActuel, ['administrateur', 'super_administrateur'], true)) {
            return $roleConnecte === 'super_administrateur';
        }

        return true;
    }

    public function rules(): array
    {
        $utilisateur = $this->route('utilisateur');

        return [
            'nom' => ['sometimes', 'string', 'max:255'],
            'prenom' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', Rule::unique('utilisateurs', 'email')->ignore($utilisateur->id_utilisateur, 'id_utilisateur')],
            'id_promotion' => ['nullable', 'uuid', 'exists:promotions,id_promotion'],
            'id_filiere' => ['nullable', 'uuid', 'exists:filieres,id_filiere'],
            'id_specialite' => ['nullable', 'uuid', 'exists:specialites,id_specialite'],
            'contacts' => ['nullable', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
        ];
    }
}