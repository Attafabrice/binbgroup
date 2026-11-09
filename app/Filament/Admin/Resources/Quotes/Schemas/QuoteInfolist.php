<?php

namespace App\Filament\Admin\Resources\Quotes\Schemas;

use App\Filament\Admin\Resources\Projects\ProjectResource;
use App\Filament\Admin\Resources\QuoteRequests\QuoteRequestResource;
use App\Models\Quote;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuoteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Devis')
                ->schema([
                    TextEntry::make('reference')->label('Référence'),
                    TextEntry::make('status')->label('Statut')->badge(),
                    TextEntry::make('title')->label('Objet'),
                    TextEntry::make('client.contact_name')
                        ->label('Client')
                        ->formatStateUsing(fn ($state, Quote $record): string => $record->client->display_name),
                    TextEntry::make('service.name')->label('Service')->placeholder('—'),
                    TextEntry::make('quoteRequest.reference')
                        ->label('Demande d’origine')
                        ->placeholder('Création directe')
                        ->url(fn (Quote $record): ?string => $record->quote_request_id
                            ? QuoteRequestResource::getUrl('view', ['record' => $record->quote_request_id])
                            : null),
                    TextEntry::make('valid_until')->label('Valable jusqu’au')->date('d/m/Y')->placeholder('—'),
                    TextEntry::make('sent_at')->label('Envoyé le')->dateTime('d/m/Y à H:i')->placeholder('Non envoyé'),
                    TextEntry::make('accepted_at')->label('Accepté le')->dateTime('d/m/Y à H:i')->placeholder('—'),
                    TextEntry::make('creator.name')->label('Créé par')->placeholder('—'),
                ])
                ->columns(3)
                ->columnSpanFull(),

            Section::make('Lignes du devis')
                ->schema([
                    RepeatableEntry::make('items')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('designation')->label('Désignation')->columnSpan(2),
                            TextEntry::make('quantity')
                                ->label('Quantité')
                                ->formatStateUsing(fn ($state) => rtrim(rtrim((string) $state, '0'), '.')),
                            TextEntry::make('unit')->label('Unité')->placeholder('—'),
                            TextEntry::make('unit_price')->label('Prix unitaire')->formatStateUsing(fn ($state) => Quote::money($state)),
                            TextEntry::make('total')->label('Montant')->formatStateUsing(fn ($state) => Quote::money($state)),
                        ])
                        ->columns(6),
                ])
                ->columnSpanFull(),

            Section::make('Montants')
                ->schema([
                    TextEntry::make('subtotal')->label('Total HT')->formatStateUsing(fn ($state) => Quote::money($state)),
                    TextEntry::make('tax_amount')
                        ->label(fn (Quote $record): string => 'TVA ('.$record->tax_rate.' %)')
                        ->formatStateUsing(fn ($state) => Quote::money($state)),
                    TextEntry::make('total')->label('Total TTC')->weight('bold')->formatStateUsing(fn ($state) => Quote::money($state)),
                    TextEntry::make('notes')->label('Conditions et remarques')->placeholder('Aucune')->columnSpanFull(),
                ])
                ->columns(3)
                ->columnSpanFull(),

            Section::make('Projet')
                ->schema([
                    TextEntry::make('project.reference')
                        ->label('Projet créé')
                        ->url(fn (Quote $record): string => ProjectResource::getUrl('view', ['record' => $record->project])),
                ])
                ->visible(fn (Quote $record): bool => $record->project()->exists())
                ->columnSpanFull(),
        ]);
    }
}