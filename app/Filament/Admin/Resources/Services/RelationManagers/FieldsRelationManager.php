<?php

namespace App\Filament\Admin\Resources\Services\RelationManagers;

use App\Enums\FieldType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FieldsRelationManager extends RelationManager {
    protected static string $relationship = 'fields';

    protected static ?string $title = 'Champs du formulaire de devis';

    protected static ?string $modelLabel = 'champ';

    protected static ?string $pluralModelLabel = 'champs';

    protected static ?string $recordTitleAttribute = 'label';

    public function isReadOnly(): bool{
        return ! auth()->user()?->can('Update:Service');
    }

    public function form(Schema $schema): Schema{
        return $schema->components([
            TextInput::make('label')
                ->label('Question posée au client')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Select::make('type')
                ->label('Type de champ')
                ->options(FieldType::class)
                ->required()
                ->live(),
            Toggle::make('is_required')
                ->label('Obligatoire')
                ->inline(false),
            TagsInput::make('options')
                ->label('Choix proposés')
                ->helperText('Tapez un choix puis Entrée.')
                ->visible(fn (Get $get): bool => in_array($get('type'), [FieldType::Select, FieldType::Select->value], true))
                ->required(fn (Get $get): bool => in_array($get('type'), [FieldType::Select, FieldType::Select->value], true))
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table{
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label')->label('Question'),
                TextColumn::make('type')->label('Type')->badge(),
                IconColumn::make('is_required')->label('Obligatoire')->boolean(),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}