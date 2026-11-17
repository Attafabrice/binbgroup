<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectPhotoRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     * L'autorisation est gérée en amont par la Policy.
     */
    public function authorize(): bool{
        return true;
    }

    /**
     * Règles de validation pour la mise à jour d'une photo de projet.
     */
    public function rules(): array{
        return [
            'caption'         => ['nullable', 'string', 'max:255'],
            'project_step_id' => [
                'nullable',
                Rule::exists('project_steps', 'id')->where('project_id', $this->route('project')->id),
            ],
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation.
     */
    public function messages(): array{
        return [
            'caption.max'              => 'La légende ne peut pas dépasser 255 caractères.',
            'project_step_id.exists'   => 'L\'étape sélectionnée n\'appartient pas à ce projet.',
        ];
    }
}