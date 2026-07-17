<?php

namespace App\Http\Requests\Projet;

use Illuminate\Foundation\Http\FormRequest;

class DeposerVersionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $projet = $this->route('projet');

        return $this->user()?->id_utilisateur === $projet->id_utilisateur
            && $projet->statut === 'corrections_demandees';
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

            if ($projet->statut !== 'corrections_demandees') {
                $validator->errors()->add(
                    'statut',
                    'Une nouvelle version ne peut être déposée que si des corrections ont été demandées (statut actuel : '.$projet->statut.').'
                );
            }
        });
    }

     public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $projet = $this->route('projet');

            if ($projet->statut !== 'corrections_demandees') {
                $validator->errors()->add(
                    'statut',
                    'Une nouvelle version ne peut être déposée que si des corrections ont été demandées (statut actuel : '.$projet->statut.').'
                );
            }
        });
    }
}