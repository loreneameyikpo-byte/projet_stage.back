<?php

namespace App\Http\Requests\Jury;

use Illuminate\Foundation\Http\FormRequest;

class SaisirNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'numeric', 'min:0', 'max:20'],
            'commentaire' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required' => 'Veuillez saisir une note.',
            'note.numeric' => 'La note doit être un nombre.',
            'note.min' => 'La note ne peut pas être inférieure à 0.',
            'note.max' => 'La note ne peut pas dépasser 20.',
            'commentaire.required' => 'Un commentaire est obligatoire pour justifier votre évaluation.',
            'commentaire.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
        ];
    }
}