<?php

namespace App\Filament\Client\Resources\QuoteRequests\Pages;

use App\Filament\Client\Resources\QuoteRequests\QuoteRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuoteRequests extends ListRecords
{
    protected static string $resource = QuoteRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nouvelle demande')->icon('heroicon-o-plus'),
        ];
    }
}