<?php

namespace App\Http\Controllers\Client;

use App\Enums\{ProjectStatus, QuoteRequestStatus, QuoteStatus};
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __invoke(){
        $client = auth()->user()->client ?? abort(403, 'Aucune fiche client rattachée à ce compte.');

        return view('client.dashboard', [
            'projects' => $client->projects()->where('status', ProjectStatus::EnCours)->count(),
            'requests' => $client->quoteRequests()->whereIn('status', [QuoteRequestStatus::Nouvelle, QuoteRequestStatus::EnCours])->count(),
            'quotes'   => $client->quotes()->where('status', QuoteStatus::Envoye)->whereNotNull('sent_at')->count(),
        ]);
    }
}