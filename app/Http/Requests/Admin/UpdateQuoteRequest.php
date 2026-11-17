<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuoteRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     * L'autorisation est gérée en amont par la Policy.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour la mise à jour d'un devis.
     */
    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'valid_until' => ['required', 'date', 'after:today'],
            'tax_rate'    => ['required', 'numeric', 'between:0,100'],
            'notes'       => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation.
     */
    public function messages(): array
    {
        return [
            'title.required'       => 'Le titre du devis est obligatoire.',
            'valid_until.required' => 'La date de validité est obligatoire.',
            'valid_until.after'    => 'La date de validité doit être dans le futur.',
            'tax_rate.required'    => 'Le taux de TVA est obligatoire.',
            'tax_rate.between'     => 'Le taux de TVA doit être compris entre 0 et 100.',
        ];
    }
}