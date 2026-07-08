<?php

namespace App\Http\Requests\Utilisateur;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModifierUtilisateurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role?->libelle, ['administrateur', 'super_administrateur'], true);
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