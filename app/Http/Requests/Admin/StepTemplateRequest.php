<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StepTemplateRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     * L'autorisation est gérée en amont par la Policy.
     */
    public function authorize(): bool{
        return true;
    }

    /**
     * Règles de validation pour la création / mise à jour d'un modèle d'étape.
     */
    public function rules(): array{
        return [
            'name'   => ['required', 'string', 'max:255'],
            'weight' => ['required', 'integer', 'between:1,100'],
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation.
     */
    public function messages(): array{
        return [
            'name.required'   => 'Le nom de l\'étape est obligatoire.',
            'weight.required' => 'Le poids de l\'étape est obligatoire.',
            'weight.integer'  => 'Le poids doit être un nombre entier.',
            'weight.between'  => 'Le poids doit être compris entre 1 et 100.',
        ];
    }
}