<?php

namespace App\Http\Requests\Reference;

use Illuminate\Foundation\Http\FormRequest;


class SalleRequest extends FormRequest
{

    public function authorize(): bool
    {
        return in_array($this->user()?->role?->libelle, ['administrateur', 'super_administrateur'], true);
     
   }
   
    public function rules(): array
    {
        return [
            'numero' => ['required', 'string', 'max:50'],
            'libelle' => ['nullable', 'string', 'max:255'],
        ];
    }
    
}