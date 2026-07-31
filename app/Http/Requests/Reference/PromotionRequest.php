<?php
 
namespace App\Http\Requests\Reference;

use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role?->libelle, ['administrateur', 'super_administrateur'], true);
    }

    public function rules(): array
    {
        return [
            'annee' => ['required', 'string', 'regex:/^\d{4}-\d{4}$/', 'max:9'],
            'id_niveau' => ['required', 'uuid', 'exists:niveaux,id_niveau'],
        ];
    }
    public function messages(): array
    {
        return [
            
            'annee.regex' => 'L\'année académique doit être au format "YYYY-YYYY".',
            
        ];
    }
     public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $annee = $this->input('annee');

            if (! $annee || ! preg_match('/^(\d{4})-(\d{4})$/', $annee, $matches)) {
                return; // déjà signalé par la règle regex ci-dessus
            }

            $premiereAnnee = (int) $matches[1];
            $secondeAnnee = (int) $matches[2];

            if ($secondeAnnee !== $premiereAnnee + 1) {
                $validator->errors()->add(
                    'annee',
                    'L\'année académique doit couvrir deux années consécutives (ex: 2025-2026, pas 2025-2027 ou 2026-2025).'
                );
            }
        });
      }
  
}