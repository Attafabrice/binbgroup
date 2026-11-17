<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     * L'autorisation est gérée en amont par la Policy.
     */
    public function authorize(): bool{
        return true;
    }

    /**
     * Règles de validation pour la création / mise à jour d'un service.
     */
    public function rules(): array{
        $id = $this->route('service')?->id;
        return [
            'name'        => ['required', 'string', 'max:255', Rule::unique('services', 'name')->ignore($id)],
            'lead'        => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation.
     */
    public function messages(): array{
        return [
            'name.required' => 'Le nom du service est obligatoire.',
            'name.unique'   => 'Ce nom de service est déjà utilisé.',
            'image.image'   => 'Le fichier doit être une image.',
            'image.mimes'   => 'L\'image doit être au format JPG, PNG ou WEBP.',
            'image.max'     => 'L\'image ne doit pas dépasser 3 Mo.',
        ];
    }
}