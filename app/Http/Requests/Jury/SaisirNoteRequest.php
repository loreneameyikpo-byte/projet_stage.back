<?php

namespace App\Http\Requests\Jury;

use Illuminate\Foundation\Http\FormRequest;

class SaisirNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $jury = $this->route('jury');

        return $jury->membres()
            ->where('utilisateurs.id_utilisateur', $this->user()?->id_utilisateur)
            ->exists();
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'numeric', 'min:0', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.min' => 'La note ne peut pas être négative.',
            'note.max' => 'La note ne peut pas dépasser 20.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $jury = $this->route('jury');
            $datePresentation = $jury->presentation?->date_presentation;

            if ($datePresentation && $datePresentation->isFuture()) {
                $validator->errors()->add(
                    'presentation',
                    'Impossible de saisir une note avant la date de la soutenance.'
                );
            }
        });
    }
}