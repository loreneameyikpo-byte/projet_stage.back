<?php

namespace App\Http\Requests\Projet;

use Illuminate\Foundation\Http\FormRequest;

class AffecterEncadreurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role?->libelle, ['administrateur', 'super_administrateur'], true);
    }

    public function rules(): array
    {
        return [
            'id_encadreur' => ['required', 'uuid', 'exists:utilisateurs,id_utilisateur'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_encadreur.exists' => 'Cet encadreur n\'existe pas.',
        ];
    }

    /**
     * Vérifie, après validation de base, que l'utilisateur désigné a bien le rôle encadreur.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $utilisateur = \App\Models\Utilisateur::find($this->input('id_encadreur'));

            if ($utilisateur && $utilisateur->role?->libelle !== 'encadreur') {
                $validator->errors()->add('id_encadreur', 'L\'utilisateur désigné doit avoir le rôle encadreur.');
            }
        });
    }
}