<?php

namespace App\Http\Requests\Projet;

use Illuminate\Foundation\Http\FormRequest;

class DeposerVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $projet = $this->route('projet');

        \Log::info('DEBUG authorize', [
        'user_id' => $this->user()?->id_utilisateur,
        'projet_id_utilisateur' => $projet->id_utilisateur,
        'projet_statut' => $projet->statut,
        'match_user' => $this->user()?->id_utilisateur === $projet->id_utilisateur,
        'match_statut' => $projet->statut === 'corrections',
    ]);


        return $this->user()?->id_utilisateur === $projet->id_utilisateur
            && $projet->statut === 'corrections';
    }

    public function rules(): array
    {
        return [
            'rapport_pdf' => ['required', 'file', 'mimes:pdf', 'max:20480'],
            'depot_github' => ['nullable', 'url', 'starts_with:https://github.com'],
        ];
    }

    public function messages(): array
    {
        return [
            'rapport_pdf.mimes' => 'Le rapport doit être un fichier PDF.',
            'rapport_pdf.max' => 'Le rapport ne doit pas dépasser 20 Mo.',
        ];
    }

     public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $projet = $this->route('projet');

            if ($projet->statut !== 'corrections') {
                $validator->errors()->add(
                    'statut',
                    'Une nouvelle version ne peut être déposée que si des corrections ont été demandées (statut actuel : '.$projet->statut.').'
                );
            }
        });
    }
}