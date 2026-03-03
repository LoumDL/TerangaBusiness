<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description'   => ['required', 'string', 'max:255'],
            'montant'        => ['required', 'numeric', 'min:1'],
            'justificatif'   => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.required'  => 'La description est obligatoire.',
            'montant.required'      => 'Le montant est obligatoire.',
            'montant.numeric'       => 'Le montant doit être un nombre.',
            'montant.min'           => 'Le montant doit être supérieur à 0.',
            'justificatif.required' => 'Le justificatif est obligatoire.',
            'justificatif.file'     => 'Le justificatif doit être un fichier.',
            'justificatif.mimes'    => 'Le justificatif doit être un PDF, JPG ou PNG.',
            'justificatif.max'      => 'Le justificatif ne doit pas dépasser 5MB.',
        ];
    }
}
