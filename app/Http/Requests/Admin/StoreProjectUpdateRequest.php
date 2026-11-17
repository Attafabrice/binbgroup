<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectUpdateRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     * L'autorisation est gérée en amont par la Policy.
     */
    public function authorize(): bool{
        return true;
    }

    /**
     * Règles de validation pour la création / mise à jour d'une actualité de projet.
     */
    public function rules(): array{
        return [
            'title'           => ['required', 'string', 'max:255'],
            'content'         => ['nullable', 'string', 'max:5000'],
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
            'title.required'           => 'Le titre de l\'actualité est obligatoire.',
            'title.max'                => 'Le titre ne peut pas dépasser 255 caractères.',
            'content.max'              => 'Le contenu ne peut pas dépasser 5000 caractères.',
            'project_step_id.exists'   => 'L\'étape sélectionnée n\'appartient pas à ce projet.',
        ];
    }
}