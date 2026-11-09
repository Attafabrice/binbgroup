<?php

namespace App\Filament\Admin\Resources\QuoteRequests\Pages;

use App\Enums\QuoteRequestStatus;
use App\Filament\Admin\Resources\QuoteRequests\QuoteRequestResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Admin\Resources\Quotes\QuoteResource;
use DomainException;

class ViewQuoteRequest extends ViewRecord
{
    protected static string $resource = QuoteRequestResource::class;

    protected function canDecide(): bool{
        return auth()->user()?->can('Update:QuoteRequest') ?? false;
    }

    protected function isOpen(array $statuses): bool{
        return in_array($this->record->status, $statuses, true);
    }

    protected function getHeaderActions(): array{
        return [
            Action::make('takeCharge')
                ->label('Prendre en charge')
                ->icon('heroicon-o-hand-raised')
                ->color('warning')
                ->visible(fn () => $this->canDecide() && $this->isOpen([QuoteRequestStatus::Nouvelle]))
                ->action(function () {
                    $this->record->startReview();
                    $this->record->refresh();

                    Notification::make()->title('Demande prise en charge')->success()->send();
                }),

            Action::make('accept')
                ->label('Valider la demande')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->canDecide() && $this->isOpen([QuoteRequestStatus::Nouvelle, QuoteRequestStatus::EnCours]))
                ->modalHeading('Valider cette demande ?')
                ->modalDescription('Un devis brouillon sera créé dans la table « Devis » et le client sera informé par email.')
                ->modalSubmitActionLabel('Valider et informer le client')
                ->schema([
                    Textarea::make('note')
                        ->label('Message pour le client (facultatif)')
                        ->rows(3)
                        ->maxLength(1000),
                ])
                ->action(function (array $data) {
                    try {
                        $sent = $this->record->accept($data['note'] ?? null);
                    } catch (DomainException $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();

                        return;
                    }

                    $this->record->refresh();
                    $quote = $this->record->quotes()->latest('id')->first();

                    $notification = Notification::make()
                        ->title($sent ? 'Demande validée, client informé par email' : 'Demande validée, mais l’email n’a pas pu être envoyé')
                        ->body('Un devis brouillon a été créé.')
                        ->actions([
                            Action::make('openQuote')
                                ->label('Préparer le devis')
                                ->url(QuoteResource::getUrl('edit', ['record' => $quote])),
                        ]);

                    $sent ? $notification->success() : $notification->warning();
                    $notification->send();
                }),
                
            Action::make('reject')
                ->label('Refuser')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->canDecide() && $this->isOpen([QuoteRequestStatus::Nouvelle, QuoteRequestStatus::EnCours]))
                ->modalHeading('Refuser cette demande ?')
                ->modalDescription('Le motif sera envoyé au client par email.')
                ->modalSubmitActionLabel('Refuser et informer le client')
                ->schema([
                    Textarea::make('reason')
                        ->label('Motif du refus')
                        ->required()
                        ->minLength(10)
                        ->rows(3)
                        ->maxLength(1000),
                ])
                ->action(function (array $data) {
                    $sent = $this->record->reject($data['reason']);
                    $this->record->refresh();

                    $sent
                        ? Notification::make()->title('Demande refusée, client informé par email')->success()->send()
                        : Notification::make()->title('Demande refusée, mais l’email n’a pas pu être envoyé')
                            ->body('Vérifiez l’adresse email du client et la configuration des emails.')->warning()->send();
                }),
        ];
    }
}