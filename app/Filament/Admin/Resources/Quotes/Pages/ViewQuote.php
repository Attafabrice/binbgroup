<?php

namespace App\Filament\Admin\Resources\Quotes\Pages;

use App\Enums\QuoteStatus;
use App\Filament\Admin\Resources\Projects\ProjectResource;
use App\Filament\Admin\Resources\Quotes\QuoteResource;
use DomainException;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewQuote extends ViewRecord
{
    protected static string $resource = QuoteResource::class;

    protected function canManage(): bool{
        return auth()->user()?->can('Update:Quote') ?? false;
    }

    protected function getHeaderActions(): array{
        return [
            EditAction::make(),

            Action::make('send')
                ->label('Envoyer au client')
                ->icon('heroicon-o-paper-airplane')
                ->visible(fn () => $this->canManage() && $this->record->status === QuoteStatus::Brouillon)
                ->requiresConfirmation()
                ->modalHeading('Envoyer ce devis au client ?')
                ->modalDescription('Une fois envoyé, le devis est figé : plus aucune modification, ni des lignes ni des montants.')
                ->modalSubmitActionLabel('Oui, envoyer')
                ->action(function () {
                    try {
                        $this->record->send();
                    } catch (DomainException $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();

                        return;
                    }

                    $this->record->refresh();
                    Notification::make()->title('Devis envoyé et figé')->success()->send();
                }),

            Action::make('accept')
                ->label('Devis accepté : créer le projet')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn () => $this->canManage() && $this->record->status === QuoteStatus::Envoye)
                ->requiresConfirmation()
                ->modalHeading('Le client a accepté ce devis ?')
                ->modalDescription('Un projet sera créé avec les étapes types du service.')
                ->modalSubmitActionLabel('Créer le projet')
                ->action(function () {
                    try {
                        $project = $this->record->accept();
                    } catch (DomainException $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();

                        return;
                    }

                    $this->record->refresh();

                    Notification::make()
                        ->title('Projet '.$project->reference.' créé')
                        ->success()
                        ->actions([
                            Action::make('openProject')
                                ->label('Ouvrir le projet')
                                ->url(ProjectResource::getUrl('view', ['record' => $project])),
                        ])
                        ->send();
                }),

            Action::make('refuse')
                ->label('Devis refusé')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->canManage() && $this->record->status === QuoteStatus::Envoye)
                ->requiresConfirmation()
                ->modalHeading('Le client a refusé ce devis ?')
                ->action(function () {
                    try {
                        $this->record->refuse();
                    } catch (DomainException $e) {
                        Notification::make()->title($e->getMessage())->danger()->send();

                        return;
                    }

                    $this->record->refresh();
                    Notification::make()->title('Devis marqué comme refusé')->success()->send();
                }),
        ];
    }
}