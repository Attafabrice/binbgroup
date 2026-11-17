<?php

namespace App\Http\Requests;

use App\Enums\ClientType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { 
        return true; 
    }

    public function rules(): array{
        return [
            'type'         => ['required', Rule::enum(ClientType::class)],
            'company_name' => ['required_if:type,entreprise', 'nullable', 'string', 'max:255'],
            'name'         => ['required', 'string', 'max:255'],
            'phone'        => ['required', 'regex:/^\+?[0-9 ]{8,20}$/'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email'], // inclut les comptes supprimés (soft delete)
            'password'     => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
            'website'      => ['prohibited'], // honeypot
        ];
    }

    public function messages(): array{
        return [
            'company_name.required_if' => 'Le nom de l’entreprise est obligatoire.',
            'phone.regex'              => 'Numéro invalide. Exemple : 07 00 00 00 00',
            'email.unique'             => 'Cet email est déjà utilisé.',
            'website.prohibited'       => 'Inscription impossible.',
            'terms.accepted' => 'Vous devez accepter les conditions d’utilisation.',
        ];
    }
}