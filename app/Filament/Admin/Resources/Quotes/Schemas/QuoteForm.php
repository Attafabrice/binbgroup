<?php

namespace App\Filament\Admin\Resources\Quotes\Schemas;

use App\Models\Client;
use App\Models\Quote;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class QuoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informations du devis')
                ->description('Attention : une fois envoyé au client, ce devis ne pourra plus être modifié. Vérifiez les lignes et les montants avant l’envoi. En cas d’erreur, il faudra créer un nouveau devis.')
                ->schema([
                    Select::make('client_id')
                        ->label('Client')
                        ->relationship('client', 'contact_name')
                        ->getOptionLabelFromRecordUsing(fn (Client $record): string => $record->display_name)
                        ->searchable(['contact_name', 'company_name'])
                        ->preload()
                        ->required()
                        ->disabled(fn (?Quote $record): bool => filled($record?->quote_request_id))
                        ->dehydrated(),
                    Select::make('service_id')
                        ->label('Service')
                        ->relationship('service', 'name')
                        ->required()
                        ->disabled(fn (?Quote $record): bool => filled($record?->quote_request_id))
                        ->dehydrated(),
                    TextInput::make('title')
                        ->label('Objet du devis')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    DatePicker::make('valid_until')
                        ->label('Valable jusqu’au')
                        ->default(now()->addDays(30))
                        ->minDate(now()),
                    Select::make('tax_rate')
                        ->label('TVA')
                        ->options([0 => 'Sans TVA', 18 => 'TVA 18 %'])
                        ->default(0)
                        ->required()
                        ->live(),
                    Textarea::make('notes')
                        ->label('Conditions et remarques (affichées sur le devis)')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make('Lignes du devis')
                ->schema([
                    Repeater::make('items')
                        ->relationship('items')
                        ->orderColumn('position')
                        ->hiddenLabel()
                        ->addActionLabel('Ajouter une ligne')
                        ->defaultItems(1)
                        ->minItems(1)
                        ->schema([
                            TextInput::make('designation')
                                ->label('Désignation')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(3),
                            TextInput::make('quantity')
                                ->label('Quantité')
                                ->numeric()
                                ->default(1)
                                ->minValue(0.01)
                                ->required()
                                ->live(onBlur: true),
                            TextInput::make('unit')
                                ->label('Unité')
                                ->placeholder('m², u, forfait')
                                ->maxLength(20),
                            TextInput::make('unit_price')
                                ->label('Prix unitaire')
                                ->numeric()
                                ->integer()
                                ->minValue(0)
                                ->suffix('FCFA')
                                ->required()
                                ->live(onBlur: true),
                        ])
                        ->columns(6),

                    TextEntry::make('totals_preview')
                        ->label('Récapitulatif')
                        ->state(function (Get $get): string {
                            $subtotal = (int) collect($get('items') ?? [])->sum(
                                fn ($line) => round((float) ($line['quantity'] ?? 0) * (int) ($line['unit_price'] ?? 0))
                            );
                            $tax = (int) round($subtotal * (int) ($get('tax_rate') ?? 0) / 100);

                            return 'Total HT : '.Quote::money($subtotal)
                                .'  |  TVA : '.Quote::money($tax)
                                .'  |  Total TTC : '.Quote::money($subtotal + $tax);
                        }),
                ])
                ->columnSpanFull(),
        ]);
    }
}