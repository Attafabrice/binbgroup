<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectPhotosRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     * L'autorisation est gérée en amont par la Policy.
     */
    public function authorize(): bool{
        return true;
    }

    /**
     * Règles de validation pour l'ajout de photos à un projet.
     */
    public function rules(): array{
        return [
            'photos'          => ['required', 'array', 'max:10'],
            'photos.*'        => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
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
            'photos.required'          => 'Veuillez sélectionner au moins une photo.',
            'photos.array'             => 'Le format des photos envoyées est invalide.',
            'photos.max'               => 'Vous ne pouvez pas envoyer plus de 10 photos à la fois.',
            'photos.*.image'           => 'Chaque fichier doit être une image.',
            'photos.*.mimes'           => 'Les images doivent être au format JPG, PNG ou WEBP.',
            'photos.*.max'             => 'Chaque image ne doit pas dépasser 10 Mo.',
            'caption.max'              => 'La légende ne peut pas dépasser 255 caractères.',
            'project_step_id.exists'   => 'L\'étape sélectionnée n\'appartient pas à ce projet.',
        ];
    }
}