<?php

namespace App\Http\Requests\Presentation;

use App\Models\Jury;
use App\Models\Presentation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ModifierPresentationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array($this->user()?->role?->libelle, ['administrateur', 'super_administrateur'], true);
    }

    public function rules(): array{
        return[
            'date_presentation' => ['required', 'date'],
            'heure_presentation' => ['required', 'date_format:H:i'],
            'id_salle' => ['required', 'uuid', 'exists:salles,id_salle'],
            'libelle' => ['nullable', 'string', 'max:255'],
            'membres' => ['required', 'array', 'min:1'],
            'membres.*.id_utilisateur' => ['required', 'uuid', 'exists:utilisateurs,id_utilisateur'],
            'membres.*.role_jury' => ['required', 'string', 'in:president,rapporteur,membre'],
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            /** @var Presentation $presentation */
            $presentation = $this->route('presentation');

            $dateP = $this->input('date_presentation');
            $heureP = $this->input('heure_presentation');
            $idSalle = $this->input('id_salle');

            if ($dateP && $heureP && $idSalle) {
                $conflitSalle = Presentation::where('id_salle', $idSalle)
                    ->where('date_presentation', $dateP)
                    ->where('heure_presentation', $heureP)
                    ->where('id_presentation', '!=', $presentation->id_presentation)
                    ->exists();

                if ($conflitSalle) {
                    $validator->errors()->add('id_salle', 'Cette salle est déjà réservée sur ce créneau.');
                }
            }

            $membres = $this->input('membres', []);

            if ($dateP && $heureP && ! empty($membres)) {
                $idsMembres = collect($membres)->pluck('id_utilisateur');

                $conflitJury = Jury::whereHas('presentation', function ($q) use ($dateP, $heureP, $presentation) {
                    $q->where('date_presentation', $dateP)
                      ->where('heure_presentation', $heureP)
                      ->where('id_presentation', '!=', $presentation->id_presentation);
                })->whereHas('membres', function ($q) use ($idsMembres) {
                    $q->whereIn('utilisateurs.id_utilisateur', $idsMembres);
                })->exists();

                if ($conflitJury) {
                    $validator->errors()->add('membres', 'Un ou plusieurs membres du jury sont déjà mobilisés sur ce créneau.');
                }
            }
        });
    }

    

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
