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

     public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $projet = $this->route('projet');
            $statutsAutorises = ['en_attente', 'corrections'];

            if (! in_array($projet->statut, $statutsAutorises, true)) {
                $validator->errors()->add(
                    'statut',
                    'Ce projet ne peut plus être validé ou corrigé (statut actuel : '.$projet->statut.').'
                );
            }
        });
    }
}