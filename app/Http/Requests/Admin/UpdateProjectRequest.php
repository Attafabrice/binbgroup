<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     * L'autorisation est gérée en amont par la Policy.
     */
    public function authorize(): bool{
        return true;
    }

    /**
     * Règles de validation pour la mise à jour d'un projet.
     */
    public function rules(): array{
        return [
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:5000'],
            'location'         => ['nullable', 'string', 'max:255'],
            'status'           => ['required', Rule::enum(ProjectStatus::class)],
            'manager_id'       => ['nullable', 'exists:users,id'],
            'start_date'       => ['nullable', 'date'],
            'planned_end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'actual_end_date'  => ['nullable', 'date'],
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation.
     */
    public function messages(): array{
        return [
            'title.required'            => 'Le titre du projet est obligatoire.',
            'status.required'           => 'Le statut du projet est obligatoire.',
            'status.enum'               => 'Le statut sélectionné est invalide.',
            'manager_id.exists'         => 'Le chef de projet sélectionné n\'existe pas.',
            'planned_end_date.after_or_equal' => 'La date de fin prévue doit être postérieure ou égale à la date de début.',
        ];
    }
}