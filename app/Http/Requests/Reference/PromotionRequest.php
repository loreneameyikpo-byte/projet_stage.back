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
            'annee' => ['required', 'digits:4'],
            'id_niveau' => ['required', 'uuid', 'exists:niveaux,id_niveau'],
        ];
    }
}