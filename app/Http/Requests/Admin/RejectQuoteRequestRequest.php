<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RejectQuoteRequestRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     * L'autorisation réelle (qui a le droit de refuser) est gérée en amont
     * par la Policy ou le contrôleur.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation appliquées aux données de la requête.
     */
    public function rules(): array
    {
        return [
            // Le motif est obligatoire, doit être une chaîne de caractères
            // et ne peut pas dépasser 1000 caractères.
            'reason' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation.
     */
    public function messages(): array
    {
        return [
            'reason.required' => 'Le motif du refus est obligatoire.',
        ];
    }
}