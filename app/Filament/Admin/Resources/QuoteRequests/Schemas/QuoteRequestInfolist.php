<?php

namespace App\Filament\Admin\Resources\QuoteRequests\Schemas;

use App\Enums\FieldType;
use App\Filament\Admin\Resources\Clients\ClientResource;
use App\Filament\Admin\Resources\Quotes\QuoteResource;
use App\Models\Quote;
use App\Models\QuoteRequest;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteRequestInfolist
{
    public static function configure(Schema $schema): Schema{
        return $schema->components([
            Section::make('Demande')
                ->schema([
                    TextEntry::make('reference')->label('Référence'),
                    TextEntry::make('status')->label('Statut')->badge(),
                    TextEntry::make('service.name')->label('Service'),
                    TextEntry::make('created_at')->label('Reçue le')->dateTime('d/m/Y à H:i'),
                    TextEntry::make('assignee.name')->label('Pris en charge par')->placeholder('Personne'),
                    TextEntry::make('reviewed_at')->label('Décision le')->dateTime('d/m/Y à H:i')->placeholder('En attente'),
                    TextEntry::make('reviewer.name')->label('Décidée par')->placeholder('—'),
                    TextEntry::make('decision_note')->label('Message envoyé au client')->placeholder('—')->columnSpanFull(),
                ])
                ->columns(3)
                ->columnSpanFull(),

            Section::make('Client')
                ->schema([
                    TextEntry::make('client.display_name')
                        ->label('Nom')
                        ->url(fn (QuoteRequest $record): ?string => $record->client_id
                            ? ClientResource::getUrl('view', ['record' => $record->client_id])
                            : null),
                    TextEntry::make('email')->label('Email')->copyable(),
                    TextEntry::make('phone')->label('Téléphone')->copyable(),
                ])
                ->columns(3)
                ->columnSpanFull(),

            Section::make('Détails du projet')
                ->schema([
                    TextEntry::make('answers')
                        ->hiddenLabel()
                        ->state(function (QuoteRequest $record): array {
                            $fields = $record->service->fields;
                            $labels = $fields->pluck('label', 'name');
                            $files = $fields->where('type', FieldType::File)->pluck('name')->all();

                            return collect($record->answers ?? [])
                                ->except($files) // les fichiers sont listés dans « Pièces jointes »
                                ->map(fn ($value, $key) => ($labels[$key] ?? $key).' : '.(is_array($value) ? implode(', ', $value) : $value))
                                ->values()
                                ->all();
                        })
                        ->listWithLineBreaks()
                        ->placeholder('Aucun détail'),
                    TextEntry::make('message')->label('Précisions du client')->placeholder('Aucune'),
                ])
                ->columnSpanFull(),

            Section::make('Pièces jointes')
                ->schema([
                    RepeatableEntry::make('documents')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('title')->label('Pièce demandée'),
                            TextEntry::make('original_name')
                                ->label('Fichier')
                                ->icon('heroicon-o-paper-clip')
                                ->url(fn ($record): string => route('files.document', $record))
                                ->openUrlInNewTab(),
                        ])
                        ->columns(2),
                ])
                ->visible(fn (QuoteRequest $record): bool => $record->documents()->exists())
                ->columnSpanFull(),
            Section::make('Devis')
                ->schema([
                    RepeatableEntry::make('quotes')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('reference')
                                ->label('Devis')
                                ->url(fn (Quote $record): string => QuoteResource::getUrl('view', ['record' => $record])),
                            TextEntry::make('status')->label('Statut')->badge(),
                            TextEntry::make('total')->label('Total')->formatStateUsing(fn ($state) => Quote::money($state)),
                        ])
                        ->columns(3),
                ])
                ->visible(fn (QuoteRequest $record): bool => $record->quotes()->exists())
                ->columnSpanFull(),
        ]);
    }
}