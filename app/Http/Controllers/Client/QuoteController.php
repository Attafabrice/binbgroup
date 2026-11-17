<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Client, Quote};
use App\Services\QuoteService;
use DomainException;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function __construct(private QuoteService $service) {}

    private function client(): Client
    {
        return auth()->user()->client ?? abort(403, 'Aucune fiche client rattachée à ce compte.');
    }

    /** Jamais de brouillon, jamais le devis d'un autre client. */
    private function owned(Quote $quote): Quote
    {
        abort_unless($quote->client_id === $this->client()->id && $quote->sent_at !== null, 404);
        return $quote;
    }

    public function index()
    {
        $quotes = Quote::with('service')
            ->where('client_id', $this->client()->id)
            ->whereNotNull('sent_at')
            ->latest('sent_at')->paginate(10);

        return view('client.quotes.index', compact('quotes'));
    }

    public function show(Quote $quote){
        $this->owned($quote)->load(['service', 'items', 'project']);
        return view('client.quotes.show', ['q' => $quote]);
    }

    public function accept(Quote $quote){
        $this->owned($quote);

        try {
            $project = $this->service->accept($quote, null, 'portal');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('client.projects.show', $project)
            ->with('success', 'Devis accepté. Votre projet est créé, nous vous contactons très bientôt.');
    }

    public function refuse(Request $request, Quote $quote){
        $this->owned($quote);
        $data = $request->validate(['reason' => ['nullable', 'string', 'max:1000']]);

        try {
            $this->service->refuse($quote, $data['reason'] ?? null);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('client.quotes.show', $quote)->with('success', 'Devis refusé.');
    }
}