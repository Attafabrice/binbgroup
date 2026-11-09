<?php

namespace App\Filament\Client\Resources\Projects;

use App\Filament\Client\Resources\Projects\Pages\ListProjects;
use App\Filament\Client\Resources\Projects\Pages\ViewProject;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $slug = 'Projets';

    protected static ?string $modelLabel = 'Projet';

    protected static ?string $pluralModelLabel = 'Mes Projets';

    protected static ?string $navigationLabel = 'Mes projets';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?int $navigationSort = 1;

    // Lecture seule, réservée au client propriétaire
    public static function canViewAny(): bool{
        return auth()->user()?->isClient() ?? false;
    }

    public static function canCreate(): bool{
        return false;
    }

    public static function canView(Model $record): bool{
        return $record->client_id === auth()->user()?->client?->id;
    }

    public static function canEdit(Model $record): bool{
        return false;
    }

    public static function canDelete(Model $record): bool{
        return false;
    }

    public static function canDeleteAny(): bool{
        return false;
    }

    public static function getEloquentQuery(): Builder{
        return parent::getEloquentQuery()->where('client_id', auth()->user()?->client?->id ?? 0);
    }

    public static function infolist(Schema $schema): Schema{
        return $schema->components([
            Section::make('Votre projet')
                ->schema([
                    TextEntry::make('reference')->label('Référence'),
                    TextEntry::make('title')->label('Projet'),
                    TextEntry::make('service.name')->label('Service'),
                    TextEntry::make('status')->label('Statut')->badge(),
                    TextEntry::make('location')->label('Lieu')->placeholder('Non précisé'),
                    TextEntry::make('start_date')->label('Début')->date('d/m/Y')->placeholder('À définir'),
                    TextEntry::make('planned_end_date')->label('Fin prévue')->date('d/m/Y')->placeholder('À définir'),
                    TextEntry::make('actual_end_date')->label('Livré le')->date('d/m/Y')->placeholder('—'),
                    TextEntry::make('description')->label('Description')->placeholder('Aucune')->columnSpanFull(),
                ])
                ->columns(4)
                ->columnSpanFull(),

            Section::make('Avancement')
                ->schema([
                    ViewEntry::make('progress')->view('client.progress-bar')->hiddenLabel(),
                ])
                ->columnSpanFull(),

            Section::make('Étapes du projet')
                ->schema([
                    RepeatableEntry::make('visibleSteps')
                        ->hiddenLabel()
                        ->placeholder('Les étapes seront publiées prochainement.')
                        ->schema([
                            TextEntry::make('name')->label('Étape'),
                            TextEntry::make('status')->label('État')->badge(),
                            TextEntry::make('planned_date')->label('Prévue le')->date('d/m/Y')->placeholder('—'),
                            TextEntry::make('completed_at')->label('Terminée le')->date('d/m/Y')->placeholder('—'),
                        ])
                        ->columns(4),
                ])
                ->columnSpanFull(),

            Section::make('Photos du chantier')
                ->schema([
                    RepeatableEntry::make('visiblePhotos')
                        ->hiddenLabel()
                        ->schema([
                            ImageEntry::make('url')
                                ->hiddenLabel()
                                ->imageHeight(180)
                                ->checkFileExistence(false),
                            TextEntry::make('caption')->hiddenLabel()->placeholder(''),
                        ])
                        ->grid(3),
                ])
                ->visible(fn (Project $record): bool => $record->visiblePhotos()->exists())
                ->columnSpanFull(),

            Section::make('Journal du chantier')
                ->schema([
                    RepeatableEntry::make('visibleUpdates')
                        ->hiddenLabel()
                        ->placeholder('Aucune actualité pour le moment.')
                        ->schema([
                            TextEntry::make('title')->label('Actualité')->weight('bold'),
                            TextEntry::make('created_at')->label('Le')->dateTime('d/m/Y à H:i'),
                            TextEntry::make('content')->hiddenLabel()->placeholder('')->columnSpanFull(),
                        ])
                        ->columns(2),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')->label('Référence')->searchable(),
                TextColumn::make('title')->label('Projet')->searchable(),
                TextColumn::make('service.name')->label('Service'),
                TextColumn::make('status')->label('Statut')->badge(),
                TextColumn::make('progress')
                    ->label('Avancement')
                    ->suffix(' %')
                    ->badge()
                    ->color(fn ($state): string => $state >= 100 ? 'success' : ($state > 0 ? 'warning' : 'gray')),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([ViewAction::make()->label('Voir')])
            ->emptyStateHeading('Aucun projet pour le moment')
            ->emptyStateDescription('Votre projet apparaîtra ici dès qu’il sera lancé.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'view' => ViewProject::route('/{record}'),
        ];
    }
}