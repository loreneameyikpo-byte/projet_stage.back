<?php

namespace App\Http\Requests\Paiement;

use Illuminate\Foundation\Http\FormRequest;

class InitierPaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        $projet = $this->route('projet');

        return $this->user()?->id_utilisateur === $projet->id_utilisateur;
    }

    public function rules(): array
    {
        return [
            'numero_telephone' => ['required', 'string', 'regex:/^(90|91|92|93|96|97|98|99)[0-9]{6}$/'],
            'methode' => ['required', 'in:flooz,tmoney'],
        ];
    }

    public function messages(): array
    {
        return [
            'numero_telephone.regex' => 'Le numéro doit être un numéro togolais valide (ex: 90123456).',
        ];
    }
}