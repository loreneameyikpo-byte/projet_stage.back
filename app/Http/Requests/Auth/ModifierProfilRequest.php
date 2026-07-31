<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModifierProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // utilisateur déjà authentifié, modifie son propre profil
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('utilisateurs', 'email')->ignore($this->user()->id_utilisateur, 'id_utilisateur')],
            'contacts' => ['nullable', 'string', 'regex:/^\+[1-9]\d{6,14}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'contacts.regex' => 'Le numéro doit être au format international (ex: +22890112345).',
        ];
    }
}