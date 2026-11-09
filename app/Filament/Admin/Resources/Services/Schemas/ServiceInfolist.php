<?php

namespace App\Filament\Admin\Resources\Services\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ServiceInfolist {
    
    public static function configure(Schema $schema): Schema{
        return $schema->components([
            TextEntry::make('name')->label('Service'),
            IconEntry::make('is_active')->label('Actif')->boolean(),
            TextEntry::make('description')->label('Description')->placeholder('Aucune')->columnSpanFull(),
        ]);
    }
}