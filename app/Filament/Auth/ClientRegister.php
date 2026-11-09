<?php

namespace App\Filament\Auth;

use App\Models\Client;
use App\Models\User;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClientRegister extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')
                ->label('Vous êtes')
                ->options([
                    'particulier' => 'Un particulier',
                    'entreprise' => 'Une entreprise',
                ])
                ->default('particulier')
                ->required()
                ->live(),
            TextInput::make('company_name')
                ->label('Nom de l’entreprise')
                ->maxLength(255)
                ->visible(fn (Get $get): bool => $get('type') === 'entreprise')
                ->required(fn (Get $get): bool => $get('type') === 'entreprise'),
            $this->getNameFormComponent()->label('Nom et prénoms'),
            TextInput::make('phone')
                ->label('Téléphone')
                ->tel()
                ->required()
                ->maxLength(30)
                ->regex('/^\+?[0-9 ]{8,20}$/')
                ->validationMessages(['regex' => 'Numéro invalide. Exemple : 07 00 00 00 00'])
                ->helperText('Exemple : 07 00 00 00 00'),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
            // Champ piège anti-robots : invisible pour un humain
            TextInput::make('website')
                ->label('Site web')
                ->extraAttributes(['style' => 'position:absolute;left:-9999px', 'aria-hidden' => 'true'])
                ->extraInputAttributes(['tabindex' => '-1', 'autocomplete' => 'off']),
        ]);
    }

    protected function handleRegistration(array $data): Model
    {
        if (filled($data['website'] ?? null)) {
            throw ValidationException::withMessages(['data.email' => 'Inscription impossible.']);
        }

        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => $data['password'], // déjà haché par le champ Filament
                'is_active' => true,
            ]);

            $user->assignRole('client');

            Client::create([
                'user_id' => $user->id,
                'type' => $data['type'],
                'company_name' => $data['type'] === 'entreprise' ? $data['company_name'] : null,
                'contact_name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);

            return $user;
        });
    }
}