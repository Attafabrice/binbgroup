<?php

namespace App\Filament\Admin\Resources\Services\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServicesTable
{
    public static function configure(Table $table): Table{
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Image')
                    ->imageHeight(48),
                TextColumn::make('name')->label('Service')->searchable(),
                TextColumn::make('fields_count')->counts('fields')->label('Champs'),
                TextColumn::make('step_templates_count')->counts('stepTemplates')->label('Étapes types'),
                TextColumn::make('quote_requests_count')->counts('quoteRequests')->label('Demandes'),
                IconColumn::make('is_active')->label('Actif')->boolean(),
                TextColumn::make('slug')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('position')
            ->reorderable('position')
            ->filters([
                TernaryFilter::make('is_active')->label('Actif'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}