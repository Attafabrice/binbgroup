<?php

namespace App\Filament\Client\Resources\QuoteRequests;

use App\Enums\FieldType;
use App\Filament\Client\Resources\QuoteRequests\Pages\CreateQuoteRequest;
use App\Filament\Client\Resources\QuoteRequests\Pages\ListQuoteRequests;
use App\Filament\Client\Resources\QuoteRequests\Pages\ViewQuoteRequest;
use Filament\Actions\CreateAction;
use App\Models\QuoteRequest;
use App\Models\Service;
use App\Models\ServiceField;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Infolists\Components\RepeatableEntry;

class QuoteRequestResource extends Resource
{
    protected static ?string $model = QuoteRequest::class;

    protected static ?string $slug = 'demandes';

    protected static ?string $modelLabel = 'demande de devis';

    protected static ?string $pluralModelLabel = 'mes demandes';

    protected static ?string $navigationLabel = 'Mes demandes';

    protected static ?string $recordTitleAttribute = 'reference';
    
    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    // --- Droits : défini ici, indépendant des permissions de l'admin ---
    public static function canViewAny(): bool{
        return auth()->user()?->isClient() ?? false;
    }

    public static function canCreate(): bool{
        return (auth()->user()?->isClient() ?? false) && auth()->user()->client()->exists();
    }

    public static function canView(Model $record): bool{
        return $record->client_id === auth()->user()?->client?->id;
    }

    public static function canEdit(Model $record): bool{
        return false;
    }

    public static function canDelete(Model $record): bool{
        return false;
    }

    public static function canDeleteAny(): bool{
        return false;
    }

    // Un client ne voit que ses propres demandes
    public static function getEloquentQuery(): Builder{
        return parent::getEloquentQuery()->where('client_id', auth()->user()?->client?->id ?? 0);
    }

    public static function form(Schema $schema): Schema{
        return $schema->components([
            Select::make('service_id')
                ->label('Service souhaité')
                ->options(fn () => Service::active()->pluck('name', 'id'))
                ->default(fn () => Service::active()->whereKey(request()->query('service'))->value('id'))
                ->required()
                ->live()
                ->afterStateUpdated(fn (Set $set) => $set('answers', []))
                ->columnSpanFull(),

            Section::make('Informations sur votre projet')
                ->visible(fn (Get $get): bool => filled($get('service_id')))
                ->schema(fn (Get $get): array => self::dynamicFields($get('service_id')))
                ->columns(2)
                ->columnSpanFull(),

            Textarea::make('message')
                ->label('Précisions complémentaires')
                ->rows(4)
                ->maxLength(3000)
                ->columnSpanFull(),

            FileUpload::make('attachments')
                ->label('Autres documents (facultatif)')
                ->multiple()
                ->maxFiles(5)
                ->disk('local')
                ->directory('quote-requests/'.now()->format('Y/m'))
                ->visibility('private')
                ->getUploadedFileNameForStorageUsing(self::storageName())
                ->maxSize(10240)
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                ->helperText('Plans, photos, titre foncier… 5 fichiers maximum, 10 Mo chacun.')
                ->columnSpanFull(),
        ]);
    }

    /** @return array<int, \Filament\Schemas\Components\Component> */
    private static function dynamicFields(mixed $serviceId): array{
        if (blank($serviceId)) {
            return [];
        }

        return Service::find($serviceId)?->fields
            ->map(fn (ServiceField $f) => self::fieldComponent($f))
            ->all() ?? [];
    }

    private static function fieldComponent(ServiceField $f){
        $name = 'answers.'.$f->name;

        $component = match ($f->type) {
            FieldType::Textarea => Textarea::make($name)->rows(3)->columnSpanFull(),
            FieldType::Number => TextInput::make($name)->numeric(),
            FieldType::Date => DatePicker::make($name),
            FieldType::Select => Select::make($name)->options(
                collect($f->options ?? [])->mapWithKeys(fn ($o) => [$o => $o])->all()
            ),
            FieldType::File => FileUpload::make($name)
                ->disk('local')
                ->directory('quote-requests/'.now()->format('Y/m'))
                ->visibility('private')
                ->getUploadedFileNameForStorageUsing(self::storageName())
                ->maxSize(10240)
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                ->helperText('PDF, JPG, PNG ou WEBP, 10 Mo maximum.')
                ->columnSpanFull(),
                    };
        return $component->label($f->label)->required($f->is_required);
    }

    public static function infolist(Schema $schema): Schema{
        return $schema->components([
            TextEntry::make('reference')->label('Référence'),
            TextEntry::make('status')->label('Statut')->badge(),
            TextEntry::make('service.name')->label('Service'),
            TextEntry::make('created_at')->label('Envoyée le')->dateTime('d/m/Y à H:i'),
            TextEntry::make('answers')
                ->label('Détails de la demande')
                ->state(function (QuoteRequest $record): array {
                    $labels = $record->service->fields->pluck('label', 'name');

                    return collect($record->answers ?? [])
                        ->map(fn ($value, $key) => ($labels[$key] ?? $key).' : '.(is_array($value) ? implode(', ', $value) : $value))
                        ->values()
                        ->all();
                })
                ->listWithLineBreaks()
                ->placeholder('Aucun détail')
                ->columnSpanFull(),
            TextEntry::make('message')->label('Précisions')->placeholder('Aucune')->columnSpanFull(),
            TextEntry::make('decision_note')
                ->label('Message de notre équipe')
                ->visible(fn (QuoteRequest $record): bool => filled($record->decision_note))
                ->columnSpanFull(),
                
            Section::make('Documents joints')
                ->schema([
                    RepeatableEntry::make('visibleDocuments')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('title')->label('Document'),
                            TextEntry::make('original_name')
                                ->label('Fichier')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->url(fn ($record): string => route('files.document', $record))
                                ->openUrlInNewTab(),
                        ])
                        ->columns(2),
                ])
                ->visible(fn (QuoteRequest $record): bool => $record->visibleDocuments()->exists())
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table{
        return $table
            ->columns([
                TextColumn::make('reference')->label('Référence')->searchable(),
                TextColumn::make('service.name')->label('Service'),
                TextColumn::make('status')->label('Statut')->badge(),
                TextColumn::make('created_at')->label('Envoyée le')->dateTime('d/m/Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([ViewAction::make()->label('Voir')])
            ->emptyStateHeading('Aucune demande pour le moment')
            ->emptyStateDescription('Décrivez votre projet pour recevoir un devis.')
            ->emptyStateActions([
                CreateAction::make()->label('Nouvelle demande'),
            ]);
            
    }

    public static function getPages(): array{
        return [
            'index' => ListQuoteRequests::route('/'),
            'create' => CreateQuoteRequest::route('/create'),
            'view' => ViewQuoteRequest::route('/{record}'),
        ];
    }

    private static function storageName(): \Closure{
        return fn (TemporaryUploadedFile $file): string => Str::uuid().'_'
            .(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'fichier')
            .'.'.strtolower($file->getClientOriginalExtension());
    }
}