<?php

namespace App\Filament\Admin\Resources\Services;

use App\Filament\Admin\Resources\Services\Pages\CreateService;
use App\Filament\Admin\Resources\Services\Pages\EditService;
use App\Filament\Admin\Resources\Services\Pages\ListServices;
use App\Filament\Admin\Resources\Services\Pages\ViewService;
use App\Filament\Admin\Resources\Services\RelationManagers\FieldsRelationManager;
use App\Filament\Admin\Resources\Services\RelationManagers\StepTemplatesRelationManager;
use App\Filament\Admin\Resources\Services\Schemas\ServiceForm;
use App\Filament\Admin\Resources\Services\Schemas\ServiceInfolist;
use App\Filament\Admin\Resources\Services\Tables\ServicesTable;
use App\Models\Service;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $modelLabel = 'service';

    protected static ?string $pluralModelLabel = 'services';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema{
        return ServiceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema{
        return ServiceInfolist::configure($schema);
    }

    public static function table(Table $table): Table{
        return ServicesTable::configure($table);
    }

    public static function getRelations(): array{
        return [
            FieldsRelationManager::class,
            StepTemplatesRelationManager::class,
        ];
    }

    public static function getPages(): array{
        return [
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'view' => ViewService::route('/{record}'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}