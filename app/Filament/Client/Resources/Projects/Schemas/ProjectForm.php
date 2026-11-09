<?php

namespace App\Filament\Client\Resources\Projects\Schemas;

use App\Enums\ProjectStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('reference')
                    ->required(),
                Select::make('client_id')
                    ->relationship('client', 'id')
                    ->required(),
                Select::make('service_id')
                    ->relationship('service', 'name')
                    ->required(),
                Select::make('quote_id')
                    ->relationship('quote', 'title'),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('location'),
                Select::make('status')
                    ->options(ProjectStatus::class)
                    ->default('planifie')
                    ->required(),
                TextInput::make('progress')
                    ->required()
                    ->numeric()
                    ->default(0),
                DatePicker::make('start_date'),
                DatePicker::make('planned_end_date'),
                DatePicker::make('actual_end_date'),
                Select::make('manager_id')
                    ->relationship('manager', 'name'),
            ]);
    }
}
