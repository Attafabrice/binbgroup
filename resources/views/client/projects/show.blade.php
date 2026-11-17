@extends('layouts.client')
@section('title', 'Projet '.$p->reference)

@section('content')
<div class="card" style="margin-bottom:1rem">
    <div class="actions" style="justify-content:space-between">
        <div><x-status-badge :status="$p->status" /> <strong>{{ $p->title }}</strong> · {{ $p->service?->name }}</div>
        <a class="btn btn-ghost" href="{{ route('client.projects.index') }}">← Retour</a>
    </div>
    <div class="progress" style="margin-top:.75rem"><span style="width:{{ $p->progress }}%"></span></div>
    <p class="muted">Avancement : {{ $p->progress }} %
        @if ($p->start_date) · début {{ $p->start_date->format('d/m/Y') }} @endif
        @if ($p->planned_end_date) · fin prévue {{ $p->planned_end_date->format('d/m/Y') }} @endif
        @if ($p->location) · {{ $p->location }} @endif
    </p>
    @if ($p->description) <p style="margin-top:.5rem">{{ $p->description }}</p> @endif
</div>

<div class="card" style="margin-bottom:1rem">
    <h3>Étapes</h3>
    <table class="table">
        <tr><th>Étape</th><th>Statut</th><th>Date prévue</th></tr>
        @forelse ($p->visibleSteps as $s)
        <tr>
            <td>{{ $s->name }}</td>
            <td><x-status-badge :status="$s->status" /></td>
            <td>{{ $s->status === \App\Enums\StepStatus::Termine ? 'Terminée le '.$s->completed_at?->format('d/m/Y') : ($s->planned_date?->format('d/m/Y') ?? '—') }}</td>
        </tr>
        @empty
        <tr><td colspan="3" class="muted">Les étapes seront publiées prochainement.</td></tr>
        @endforelse
    </table>
</div>

@if ($p->visibleUpdates->isNotEmpty())
<div class="card" style="margin-bottom:1rem">
    <h3>Journal du chantier</h3>
    @foreach ($p->visibleUpdates as $u)
    <div class="entry">
        <strong>{{ $u->title }}</strong>
        <span class="muted"> · {{ $u->created_at->format('d/m/Y') }}@if ($u->step) · {{ $u->step->name }}@endif</span>
        @if ($u->content) <p>{{ $u->content }}</p> @endif
    </div>
    @endforeach
</div>
@endif

@if ($p->visiblePhotos->isNotEmpty())
<div class="card" style="margin-bottom:1rem">
    <h3>Photos</h3>
    <div class="photo-grid">
        @foreach ($p->visiblePhotos as $ph)
        <div class="photo">
            <a href="{{ route('files.photo', $ph) }}" target="_blank"><img src="{{ route('files.photo', $ph) }}" alt="{{ $ph->caption }}" loading="lazy"></a>
            @if ($ph->caption) <p class="muted">{{ $ph->caption }}</p> @endif
        </div>
        @endforeach
    </div>
</div>
@endif

@if ($p->quote)
<div class="card" style="margin-bottom:1rem">
    <h3>Devis accepté {{ $p->quote->reference }}</h3>
    <table class="table">
        <tr><th>Désignation</th><th>Qté</th><th>Prix unitaire</th><th>Total</th></tr>
        @foreach ($p->quote->items as $i)
        <tr>
            <td>{{ $i->designation }}</td>
            <td>{{ rtrim(rtrim($i->quantity, '0'), '.') }} {{ $i->unit }}</td>
            <td>{{ \App\Models\Quote::money($i->unit_price) }}</td>
            <td>{{ \App\Models\Quote::money($i->total) }}</td>
        </tr>
        @endforeach
    </table>
    <div class="totals">
        <p>Sous-total HT : <strong>{{ \App\Models\Quote::money($p->quote->subtotal) }}</strong></p>
        <p>TVA ({{ rtrim(rtrim($p->quote->tax_rate, '0'), '.') }} %) : <strong>{{ \App\Models\Quote::money($p->quote->tax_amount) }}</strong></p>
        <p>Total TTC : <strong>{{ \App\Models\Quote::money($p->quote->total) }}</strong></p>
    </div>
    @if ($p->quote->quoteRequest?->visibleDocuments->isNotEmpty())
        <h3 style="margin-top:1rem">Pièces de votre demande</h3>
        @foreach ($p->quote->quoteRequest->visibleDocuments as $d)
            <p><a href="{{ route('files.document', $d) }}" target="_blank">{{ $d->title }} — {{ $d->original_name }}</a></p>
        @endforeach
    @endif
</div>
@endif
@endsection