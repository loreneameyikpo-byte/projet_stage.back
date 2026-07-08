<?php

namespace App\Http\Requests\Presentation;

use Illuminate\Foundation\Http\FormRequest;

class PlanifierPresentationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role?->libelle, ['administrateur', 'super_administrateur'], true);
    }

    public function rules(): array
    {
        return [
            'id_projet' => ['required', 'uuid', 'exists:projets,id_projet'],
            'date_presentation' => ['required', 'date', 'after:today'],
            'heure_presentation' => ['required', 'date_format:H:i'],
            'id_salle' => ['required', 'uuid', 'exists:salles,id_salle'],
            'libelle' => ['nullable', 'string', 'max:255'],
            'membres' => ['required', 'array', 'min:1'],
            'membres.*.id_utilisateur' => ['required', 'uuid', 'exists:utilisateurs,id_utilisateur'],
            'membres.*.role_jury' => ['required', 'string', 'in:president,rapporteur,membre'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_presentation.after' => 'La date de présentation doit être future.',
            'membres.required' => 'Le jury doit comporter au moins un membre.',
        ];
    }
}