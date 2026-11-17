<?php

namespace App\Http\Controllers\Admin;

use App\Enums\QuoteRequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AcceptQuoteRequestRequest;
use App\Http\Requests\Admin\RejectQuoteRequestRequest;
use App\Models\QuoteRequest;
use App\Services\QuoteRequestService;
use DomainException;
use Illuminate\Http\Request;

class QuoteRequestController extends Controller
{
    public function __construct(private QuoteRequestService $service) {}

    public function index(Request $request){
        $this->authorize('viewAny', QuoteRequest::class);

        $requests = QuoteRequest::with(['service', 'client', 'assignee'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('q'), fn ($q) => $q->where(function ($q) use ($request) {
                $term = '%'.$request->q.'%';
                $q->where('reference', 'like', $term)->orWhere('name', 'like', $term);
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.quote-requests.index', [
            'requests' => $requests,
            'statuses' => QuoteRequestStatus::cases(),
        ]);
    }

    public function show(QuoteRequest $quoteRequest){
        $this->authorize('view', $quoteRequest);
        $quoteRequest->load(['service.fields', 'client', 'assignee', 'reviewer', 'documents', 'quotes']);

        $labels = $quoteRequest->service->fields->pluck('label', 'name');
        $answers = collect($quoteRequest->answers ?? [])->map(fn ($value, $key) => [
            'label' => $labels[$key] ?? $key,
            'value' => is_array($value) ? implode(', ', $value) : $value,
        ]);

        return view('admin.quote-requests.show', [
            'r' => $quoteRequest,
            'answers' => $answers,
        ]);
    }

    public function take(QuoteRequest $quoteRequest){
        $this->authorize('take', $quoteRequest);
        return $this->run(fn () => $this->service->take($quoteRequest, auth()->user()), 'Demande prise en charge.');
    }

    public function accept(AcceptQuoteRequestRequest $request, QuoteRequest $quoteRequest){
        $this->authorize('accept', $quoteRequest);

        try {
            $mailed = $this->service->accept($quoteRequest, auth()->user(), $request->validated('note'));
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $mailed
            ? 'Demande validée. Un devis brouillon a été créé et le client a été prévenu.'
            : 'Demande validée, devis brouillon créé. Email non envoyé au client.');
    }

    public function reject(RejectQuoteRequestRequest $request, QuoteRequest $quoteRequest){
        $this->authorize('reject', $quoteRequest);

        try {
            $mailed = $this->service->reject($quoteRequest, auth()->user(), $request->validated('reason'));
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $mailed
            ? 'Demande refusée. Le client a été prévenu.'
            : 'Demande refusée. Email non envoyé au client.');
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