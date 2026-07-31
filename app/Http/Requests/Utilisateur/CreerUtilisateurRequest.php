<?php

namespace App\Http\Requests\Utilisateur;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CreerUtilisateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        $roleConnecte = $this->user()?->role?->libelle;

        if (! in_array($roleConnecte, ['administrateur', 'super_administrateur'], true)) {
            return false;
        }

        // Seul le super administrateur peut créer un compte administrateur ou super administrateur.
        $roleCible = Role::find($this->input('id_role'))?->libelle;

        if (in_array($roleCible, ['administrateur', 'super_administrateur'], true)) {
            return $roleConnecte === 'super_administrateur';
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:utilisateurs,email'],
           // 'password' => ['required', Password::min(8)],
            'id_role' => ['required', 'uuid', 'exists:roles,id_role'],
            'id_promotion' => ['nullable', 'uuid', 'exists:promotions,id_promotion'],
            'id_filiere' => ['nullable', 'uuid', 'exists:filieres,id_filiere'],
            'id_specialite' => ['nullable', 'uuid', 'exists:specialites,id_specialite'],
            'contacts' => ['nullable', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Cette adresse email est déjà utilisée.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $roleConnecte = $this->user()?->role?->libelle;
            $roleCible = Role::find($this->input('id_role'))?->libelle;

            $rolesReserves = ['administrateur', 'super_administrateur'];

            if (in_array($roleCible, $rolesReserves, true) && $roleConnecte !== 'super_administrateur') {
                $validator->errors()->add(
                    'id_role',
                    'Seul le super administrateur peut créer un compte administrateur ou super administrateur.'
                );
            }
        });
    }
}