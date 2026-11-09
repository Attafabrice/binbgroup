<?php

namespace App\Filament\Admin\Resources\Clients\Schemas;

use App\Enums\ClientType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema{
        return $schema
            ->components([
                // Select::make('user_id')
                //     ->relationship('user', 'name'),
                Select::make('type')
                    ->options(ClientType::class)
                    ->default('particulier')
                    ->required(),
                TextInput::make('company_name'),
                TextInput::make('contact_name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                TextInput::make('address'),
                TextInput::make('city'),
                TextInput::make('rccm'),
                TextInput::make('ncc'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
