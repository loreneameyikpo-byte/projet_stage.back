<?php

namespace App\Http\Requests\Utilisateur;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CreerUtilisateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role?->libelle, ['administrateur', 'super_administrateur'], true);
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:utilisateurs,email'],
            'password' => ['required', Password::min(8)],
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
}