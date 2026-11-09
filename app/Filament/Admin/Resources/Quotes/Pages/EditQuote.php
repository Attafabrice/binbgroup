<?php

namespace App\Filament\Admin\Resources\Quotes\Pages;

use App\Filament\Admin\Resources\Quotes\QuoteResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array{
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    // Retour sur la fiche : les totaux recalculés y sont affichés
    protected function getRedirectUrl(): string{
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}