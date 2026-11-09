<?php

namespace App\Filament\Client\Widgets;

use App\Enums\ProjectStatus;
use App\Enums\QuoteRequestStatus;
use App\Enums\QuoteStatus;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array{
        $client = auth()->user()?->client;
        return [
            Stat::make('Projets en cours', $client?->projects()->where('status', ProjectStatus::EnCours)->count() ?? 0)
                ->description('Suivez leur avancement'),
            Stat::make('Demandes en traitement', $client?->quoteRequests()
                ->whereIn('status', [QuoteRequestStatus::Nouvelle, QuoteRequestStatus::EnCours])->count() ?? 0)
                ->description('En attente de notre réponse'),
            Stat::make('Devis à consulter', $client?->quotes()->where('status', QuoteStatus::Envoye)->count() ?? 0)
                ->description('Reçus de notre équipe'),
        ];
    }
}