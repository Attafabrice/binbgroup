<?php

namespace App\Filament\Admin\Resources\Services\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StepTemplatesRelationManager extends RelationManager
{
    protected static string $relationship = 'stepTemplates';

    protected static ?string $title = 'Étapes types du projet';

    protected static ?string $modelLabel = 'étape';

    protected static ?string $pluralModelLabel = 'étapes';

    protected static ?string $recordTitleAttribute = 'name';

    public function isReadOnly(): bool{
        return ! auth()->user()?->can('Update:Service');
    }

    public function form(Schema $schema): Schema{
        return $schema->components([
            TextInput::make('name')
                ->label('Nom de l’étape')
                ->required()
                ->maxLength(255),
            TextInput::make('weight')
                ->label('Poids')
                ->helperText('Poids relatif dans l’avancement : une étape de poids 2 compte double.')
                ->numeric()
                ->minValue(1)
                ->maxValue(100)
                ->default(1)
                ->required(),
        ]);
    }

    public function table(Table $table): Table{
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->label('Étape'),
                TextColumn::make('weight')->label('Poids'),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}