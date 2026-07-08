<?php

namespace App\Http\Requests\Projet;

use Illuminate\Foundation\Http\FormRequest;

class ValiderProjetRequest extends FormRequest
{
    public function authorize(): bool
    {
        $projet = $this->route('projet');

        return $this->user()?->role?->libelle === 'encadreur'
            && $this->user()->id_utilisateur === $projet->id_encadreur;
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:valider,corriger'],
            'observation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'decision.in' => 'La décision doit être "valider" ou "corriger".',
            'observation.required' => 'Une observation est obligatoire, que le projet soit validé ou renvoyé en correction.',
        ];
    }
}