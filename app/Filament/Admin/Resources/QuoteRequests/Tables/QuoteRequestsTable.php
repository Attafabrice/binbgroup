<?php

namespace App\Filament\Admin\Resources\QuoteRequests\Tables;

use App\Enums\QuoteRequestStatus;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuoteRequestsTable
{
    public static function configure(Table $table): Table{
        return $table
            ->columns([
                TextColumn::make('reference')->label('Référence')->searchable(),
                TextColumn::make('name')->label('Client')->searchable(),
                TextColumn::make('service.name')->label('Service'),
                TextColumn::make('status')->label('Statut')->badge(),
                TextColumn::make('created_at')->label('Reçue le')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('reviewed_at')->label('Décidée le')->dateTime('d/m/Y')->placeholder('—')->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->label('Statut')->options(QuoteRequestStatus::class),
                SelectFilter::make('service')->label('Service')->relationship('service', 'name'),
            ])
            ->recordActions([
                ViewAction::make()->label('Étudier'),
            ])
            ->emptyStateHeading('Aucune demande reçue');
    }
}