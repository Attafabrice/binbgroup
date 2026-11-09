<?php

namespace App\Filament\Admin\Resources\Quotes;

use App\Filament\Admin\Resources\Quotes\Pages\CreateQuote;
use App\Filament\Admin\Resources\Quotes\Pages\EditQuote;
use App\Filament\Admin\Resources\Quotes\Pages\ListQuotes;
use App\Filament\Admin\Resources\Quotes\Pages\ViewQuote;
use App\Filament\Admin\Resources\Quotes\Schemas\QuoteForm;
use App\Filament\Admin\Resources\Quotes\Schemas\QuoteInfolist;
use App\Filament\Admin\Resources\Quotes\Tables\QuotesTable;
use App\Models\Quote;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $modelLabel = 'devis';

    protected static ?string $pluralModelLabel = 'devis';

    protected static ?string $recordTitleAttribute = 'reference';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?int $navigationSort = 20;

    // Un devis envoyé est figé : ni modification ni suppression
    public static function canEdit(Model $record): bool
    {
        return parent::canEdit($record) && ! $record->isLocked();
    }

    public static function canDelete(Model $record): bool
    {
        return parent::canDelete($record) && ! $record->isLocked();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['client', 'service', 'quoteRequest']);
    }

    public static function form(Schema $schema): Schema{
        return QuoteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema{
        return QuoteInfolist::configure($schema);
    }

    public static function table(Table $table): Table{
        return QuotesTable::configure($table);
    }

    public static function getPages(): array{
        return [
            'index' => ListQuotes::route('/'),
            'create' => CreateQuote::route('/create'),
            'view' => ViewQuote::route('/{record}'),
            'edit' => EditQuote::route('/{record}/edit'),
        ];
    }
}