<?php

namespace App\Filament\Admin\Resources\Quotes\Tables;

use App\Enums\QuoteStatus;
use App\Models\Quote;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuotesTable
{
    public static function configure(Table $table): Table{
        return $table
            ->columns([
                TextColumn::make('reference')->label('Référence')->searchable(),
                TextColumn::make('client.contact_name')
                    ->label('Client')
                    ->formatStateUsing(fn ($state, Quote $record): string => $record->client->display_name)
                    ->searchable(),
                TextColumn::make('title')->label('Objet')->limit(40),
                TextColumn::make('total')->label('Total TTC')->formatStateUsing(fn ($state) => Quote::money($state))->sortable(),
                TextColumn::make('status')->label('Statut')->badge(),
                TextColumn::make('quoteRequest.reference')->label('Demande')->placeholder('Directe')->toggleable(),
                TextColumn::make('sent_at')->label('Envoyé le')->dateTime('d/m/Y')->placeholder('—')->toggleable(),
                TextColumn::make('created_at')->label('Créé le')->dateTime('d/m/Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(QuoteStatus::class),
            ])
            ->recordActions([
                ViewAction::make()->label('Ouvrir'),
                EditAction::make()->label('Modifier'),
            ])
            ->emptyStateHeading('Aucun devis');
    }
}