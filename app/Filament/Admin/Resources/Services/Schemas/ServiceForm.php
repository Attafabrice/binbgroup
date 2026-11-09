<?php

namespace App\Filament\Admin\Resources\Services\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nom du service')
                ->required()
                ->maxLength(255),
           Select::make('image')
                ->label('Image')
                ->allowHtml()
                ->options(fn () => collect(glob(public_path('assets/images/service-*.webp')))
                    ->sort()
                    ->mapWithKeys(fn ($path) => [
                        basename($path) => '<div style="display:flex;align-items:center;gap:.75rem">'
                            .'<img src="'.e(asset('assets/images/'.basename($path))).'" style="height:2.5rem;width:3.5rem;object-fit:cover;border-radius:.25rem" alt="">'
                            .'<span>'.e(basename($path)).'</span></div>',
                    ]))
                ->searchable()
                ->helperText('Choisissez l’image affichée sur le site pour ce service.'),
            TextInput::make('lead')
                ->label('Phrase d’accroche')
                ->maxLength(255)
                ->columnSpanFull(),
            Textarea::make('description')
                ->label('Description')
                ->rows(4)
                ->columnSpanFull(),
            Toggle::make('is_active')
                ->label('Visible sur le site')
                ->default(true),
        ]);
    }
}