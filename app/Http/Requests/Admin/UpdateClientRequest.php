<?php

namespace App\Http\Requests\Admin;

use App\Enums\ClientType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
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
     * Règles de validation pour la mise à jour d'un client.
     */
    public function rules(): array{
        $client = $this->route('client');

        return [
            'contact_name' => ['required', 'string', 'max:255'],
            'company_name' => [
                Rule::requiredIf($client->type === ClientType::Entreprise),
                'nullable',
                'string',
                'max:255',
            ],
            'phone'        => ['required', 'regex:/^\+?[0-9 ]{8,20}$/'],
            'email'        => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($client->user_id),
            ],
            'address'      => ['nullable', 'string', 'max:255'],
            'city'         => ['nullable', 'string', 'max:100'],
            'rccm'         => ['nullable', 'string', 'max:50'],
            'ncc'          => ['nullable', 'string', 'max:50'],
            'notes'        => ['nullable', 'string', 'max:3000'],
        ];
    }

    /**
     * Messages personnalisés pour les erreurs de validation.
     */
    public function messages(): array{
        return [
            'phone.regex'  => 'Numéro invalide. Exemple : 07 00 00 00 00',
            'email.unique' => 'Cet email est déjà utilisé par un compte.',
        ];
    }
}