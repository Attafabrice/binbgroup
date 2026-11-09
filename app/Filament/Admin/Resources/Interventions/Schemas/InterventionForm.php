<?php

namespace App\Filament\Admin\Resources\Interventions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class InterventionForm
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
                Select::make('project_id')
                    ->relationship('project', 'title'),
                Select::make('service_id')
                    ->relationship('service', 'name'),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('type')
                    ->required()
                    ->default('corrective'),
                TextInput::make('priority')
                    ->required()
                    ->default('normale'),
                TextInput::make('status')
                    ->required()
                    ->default('planifiee'),
                DateTimePicker::make('scheduled_at'),
                DateTimePicker::make('completed_at'),
                Select::make('technician_id')
                    ->relationship('technician', 'name'),
                Textarea::make('report')
                    ->columnSpanFull(),
            ]);
    }
}
