<?php

namespace App\Http\Requests\Projet;

use Illuminate\Foundation\Http\FormRequest;

class SupprimerProjetRequest extends FormRequest
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
            $projet = $this->route('projet');

            if ($projet->presentation()->exists()) {
                $validator->errors()->add(
                    'projet',
                    'Impossible de supprimer ce projet : une soutenance y est déjà associée.'
                );
            }
        });
    }
}