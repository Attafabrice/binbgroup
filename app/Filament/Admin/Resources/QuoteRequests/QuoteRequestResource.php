<?php

namespace App\Filament\Admin\Resources\QuoteRequests;

use App\Enums\QuoteRequestStatus;
use App\Filament\Admin\Resources\QuoteRequests\Pages\ListQuoteRequests;
use App\Filament\Admin\Resources\QuoteRequests\Pages\ViewQuoteRequest;
use App\Filament\Admin\Resources\QuoteRequests\Schemas\QuoteRequestInfolist;
use App\Filament\Admin\Resources\QuoteRequests\Tables\QuoteRequestsTable;
use App\Models\QuoteRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;

    protected static ?string $modelLabel = 'demande de devis';

    protected static ?string $pluralModelLabel = 'demandes de devis';

    protected static ?string $recordTitleAttribute = 'reference';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static ?int $navigationSort = 10;

    // Les demandes viennent des clients : le staff ne les crée pas
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = QuoteRequest::where('status', QuoteRequestStatus::Nouvelle)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['service', 'client']);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuoteRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuoteRequestsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuoteRequests::route('/'),
            'view' => ViewQuoteRequest::route('/{record}'),
        ];
    }
}