<?php

namespace App\Http\Controllers\Admin;

use App\Enums\QuoteStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\{AcceptOfflineRequest, StoreQuoteItemRequest, UpdateQuoteRequest};
use App\Models\{Quote, QuoteItem};
use App\Services\QuoteService;
use DomainException;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function __construct(private QuoteService $service) {}

    public function index(Request $request){
        $this->authorize('viewAny', Quote::class);

        $quotes = Quote::with(['client', 'service'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('q'), fn ($q) => $q->where('reference', 'like', '%'.$request->q.'%'))
            ->latest()->paginate(15)->withQueryString();
        return view('admin.quotes.index', ['quotes' => $quotes, 'statuses' => QuoteStatus::cases()]);
    }

    public function show(Quote $quote){
        $this->authorize('view', $quote);
        $quote->load(['client', 'service', 'items', 'quoteRequest', 'project', 'acceptedBy']);

        return view('admin.quotes.show', ['q' => $quote]);
    }

    public function update(UpdateQuoteRequest $request, Quote $quote){
        $this->authorize('update', $quote);

        return $this->run(function () use ($request, $quote) {
            $quote->update($request->validated());
            $quote->recalculate(); // le taux de TVA a pu changer
        }, 'Devis mis à jour.');
    }

    public function addItem(StoreQuoteItemRequest $request, Quote $quote){
        $this->authorize('update', $quote);

        return $this->run(function () use ($request, $quote) {
            $quote->items()->create([
                ...$request->validated(),
                'position' => ($quote->items()->max('position') ?? 0) + 1,
            ]);
        }, 'Ligne ajoutée.');
    }

    public function removeItem(Quote $quote, QuoteItem $item){
        $this->authorize('update', $quote);
        abort_unless($item->quote_id === $quote->id, 404);

        return $this->run(fn () => $item->delete(), 'Ligne supprimée.');
    }

    public function send(Quote $quote){
        $this->authorize('send', $quote);

        try {
            $mailed = $this->service->send($quote);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $mailed
            ? 'Devis envoyé au client. Il est maintenant figé.'
            : 'Devis envoyé et figé, mais l’email n’est pas parti.');
    }

    public function acceptOffline(AcceptOfflineRequest $request, Quote $quote){
        $this->authorize('acceptOffline', $quote);
        $proof = $request->file('proof')?->store('quote-proofs', 'local'); // disque privé

        try {
            $this->service->accept($quote, auth()->user(), $request->validated('channel'), $request->validated('note'), $proof);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', 'Acceptation enregistrée. Le projet a été créé.');
    }

    private function run(\Closure $action, string $ok){
        try {
            $action();
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', $ok);
    }
}