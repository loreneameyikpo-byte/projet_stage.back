<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class ChangerMotDePasseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // utilisateur déjà authentifié via le middleware auth:sanctum
    }

    public function rules(): array
    {
        return [
            'mot_de_passe_actuel' => ['required', 'string'],
            'mot_de_passe' => ['required', Password::min(8), 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'mot_de_passe.confirmed' => 'La confirmation ne correspond pas au nouveau mot de passe.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (! Hash::check($this->input('mot_de_passe_actuel'), $this->user()->mot_de_passe)) {
                $validator->errors()->add('mot_de_passe_actuel', 'Le mot de passe actuel est incorrect.');
            }
        });
    }
}