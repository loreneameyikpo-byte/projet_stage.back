<?php

namespace App\Http\Requests\Reference;

use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'libelle' => ['required', 'string', 'max:255'],
            'id_niveau' => ['required', 'uuid', 'exists:niveaux,id_niveau'],
            'annee_debut' => ['required', 'integer', 'min:2000', 'max:2100'],
            'annee_fin' => ['required', 'integer', 'gt:annee_debut'],
        ];
    }

    public function messages(): array
    {
        return [
            'libelle.required' => 'Le libellé de la promotion est obligatoire.',
            'id_niveau.required' => 'Veuillez sélectionner un niveau.',
            'id_niveau.exists' => 'Le niveau sélectionné est invalide.',
            'annee_debut.required' => "L'année de début est obligatoire.",
            'annee_fin.required' => "L'année de fin est obligatoire.",
            'annee_fin.gt' => "L'année de fin doit être postérieure à l'année de début.",
        ];
    }

    /**
     * Contrôle métier : une promotion dure exactement 3 ans.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->annee_debut === null || $this->annee_fin === null) {
                return;
            }
            if (! is_numeric($this->annee_debut) || ! is_numeric($this->annee_fin)) {
                return;
            }

            $duree = (int) $this->annee_fin - (int) $this->annee_debut;

            if ($duree !== 3) {
                $validator->errors()->add(
                    'annee_fin',
                    "Une promotion doit durer exactement 3 ans. L'écart actuel est de {$duree} an(s). Merci de corriger l'année de fin."
                );
            }
        });
    }
}