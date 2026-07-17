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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $projet = Projet::find($this->input('id_projet'));

            if (! $projet) {
                return; // déjà signalé par la règle exists sur id_projet
            }

            if ($projet->statut !== 'valide') {
                $validator->errors()->add(
                    'id_projet',
                    'Seul un projet au statut "validé" peut être planifié.'
                );
            }

            if ($projet->presentation()->exists()) {
                $validator->errors()->add(
                    'id_projet',
                    'Ce projet a déjà une présentation planifiée.'
                );
            }

            $dateP = $this->input('date_presentation');
            $heureP = $this->input('heure_presentation');
            $idSalle = $this->input('id_salle');

            if ($dateP && $heureP && $idSalle) {
                $conflitSalle = Presentation::where('id_salle', $idSalle)
                    ->where('date_presentation', $dateP)
                    ->where('heure_presentation', $heureP)
                    ->exists();

                if ($conflitSalle) {
                    $validator->errors()->add(
                        'id_salle',
                        'Cette salle est déjà réservée sur ce créneau.'
                    );
                }
            }

            $membres = $this->input('membres', []);

            if ($dateP && $heureP && ! empty($membres)) {
                $idsMembres = collect($membres)->pluck('id_utilisateur');

                $conflitJury = Jury::whereHas('presentation', function ($q) use ($dateP, $heureP) {
                    $q->where('date_presentation', $dateP)
                      ->where('heure_presentation', $heureP);
                })->whereHas('membres', function ($q) use ($idsMembres) {
                    $q->whereIn('utilisateurs.id_utilisateur', $idsMembres);
                })->exists();

                if ($conflitJury) {
                    $validator->errors()->add(
                        'membres',
                        'Un ou plusieurs membres du jury sont déjà mobilisés sur ce créneau.'
                    );
                }
            }
        });
    }
}