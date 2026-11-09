<?php

namespace App\Filament\Client\Resources\QuoteRequests\Schemas;

use App\Enums\QuoteRequestStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class QuoteRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('reference')
                    ->required(),
                TextInput::make('token')
                    ->required(),
                Select::make('service_id')
                    ->relationship('service', 'name')
                    ->required(),
                Select::make('client_id')
                    ->relationship('client', 'id'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
                Textarea::make('message')
                    ->columnSpanFull(),
                TextInput::make('answers'),
                Select::make('status')
                    ->options(QuoteRequestStatus::class)
                    ->default('nouvelle')
                    ->required(),
                TextInput::make('assigned_to')
                    ->numeric(),
                TextInput::make('ip_address'),
            ]);
    }
}
