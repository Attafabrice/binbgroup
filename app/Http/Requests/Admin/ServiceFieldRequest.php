<?php

namespace App\Http\Requests\Admin;

use App\Enums\FieldType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceFieldRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     * L'autorisation est gérée en amont par la Policy.
     */
    public function authorize(): bool{
        return true;
    }

    /**
     * Règles de validation pour l'ajout / mise à jour d'un champ de formulaire.
     */
    public function rules(): array{
        return [
            'label'   => ['required', 'string', 'max:255'],
            'type'    => ['required', Rule::enum(FieldType::class)],
            'options' => ['required_if:type,select', 'nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation.
     */
    public function messages(): array{
        return [
            'label.required'      => 'Le libellé du champ est obligatoire.',
            'type.required'       => 'Le type de champ est obligatoire.',
            'type.enum'           => 'Le type de champ sélectionné est invalide.',
            'options.required_if' => 'Les options sont obligatoires lorsque le type est "select".',
        ];
    }
}