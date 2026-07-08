<?php

namespace App\Http\Requests\Projet;

use Illuminate\Foundation\Http\FormRequest;

class SoumettreProjetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role?->libelle === 'etudiant';
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'rapport_pdf' => ['required', 'file', 'mimes:pdf', 'max:20480'], // 20 Mo max
            'depot_github' => ['nullable', 'url', 'starts_with:https://github.com'],
        ];
    }

    public function messages(): array
    {
        return [
            'rapport_pdf.mimes' => 'Le rapport doit être un fichier PDF.',
            'rapport_pdf.max' => 'Le rapport ne doit pas dépasser 20 Mo.',
            'depot_github.starts_with' => 'Le lien doit être une URL GitHub valide.',
        ];
    }
}