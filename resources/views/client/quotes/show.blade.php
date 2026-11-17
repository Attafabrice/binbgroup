@extends('layouts.client')
@section('title', 'Devis '.$q->reference)

@section('content')
@php
    use App\Enums\QuoteStatus as S;
    $expired = $q->status === S::Envoye && $q->isExpired();
    $open = $q->status === S::Envoye && ! $expired;
@endphp

<div class="card" style="margin-bottom:1rem">
    <div class="actions" style="justify-content:space-between">
        <div>
            @if ($expired) <span class="badge badge-warning">Expiré</span> @else <x-status-badge :status="$q->status" /> @endif
            <strong>{{ $q->title }}</strong> · {{ $q->service?->name }}
        </div>
        <a class="btn btn-ghost" href="{{ route('client.quotes.index') }}">← Retour</a>
    </div>
    <p class="muted" style="margin-top:.5rem">
        Envoyé le {{ $q->sent_at->format('d/m/Y') }} · valable jusqu'au {{ $q->valid_until?->format('d/m/Y') }}
    </p>
    @if ($q->notes) <p style="margin-top:.5rem">{{ $q->notes }}</p> @endif
</div>

<div class="card" style="margin-bottom:1rem">
    <h3>Détail chiffré</h3>
    <table class="table">
        <tr><th>Désignation</th><th>Qté</th><th>Prix unitaire</th><th>Total</th></tr>
        @foreach ($q->items as $i)
        <tr>
            <td>{{ $i->designation }}</td>
            <td>{{ rtrim(rtrim($i->quantity, '0'), '.') }} {{ $i->unit }}</td>
            <td>{{ \App\Models\Quote::money($i->unit_price) }}</td>
            <td>{{ \App\Models\Quote::money($i->total) }}</td>
        </tr>
        @endforeach
    </table>
    <div class="totals">
        <p>Sous-total HT : <strong>{{ \App\Models\Quote::money($q->subtotal) }}</strong></p>
        <p>TVA ({{ rtrim(rtrim($q->tax_rate, '0'), '.') }} %) : <strong>{{ \App\Models\Quote::money($q->tax_amount) }}</strong></p>
        <p>Total TTC : <strong>{{ \App\Models\Quote::money($q->total) }}</strong></p>
    </div>
</div>

@if ($open)
<div class="card">
    <h3>Votre réponse</h3>
    <div class="grid-2">
        <form method="POST" action="{{ route('client.quotes.accept', $q) }}">
            @csrf
            <p class="muted" style="margin-bottom:.75rem">En acceptant, votre projet est créé et notre équipe prend contact avec vous.</p>
            <button class="btn btn-success" onclick="return confirm('Accepter ce devis de {{ \App\Models\Quote::money($q->total) }} TTC ?')">Accepter le devis</button>
        </form>
        <form method="POST" action="{{ route('client.quotes.refuse', $q) }}">
            @csrf
            <div class="form-group">
                <label>Motif du refus (facultatif)</label>
                <textarea name="reason" rows="2">{{ old('reason') }}</textarea>
                @error('reason') <p class="error">{{ $message }}</p> @enderror
            </div>
            <button class="btn btn-danger" onclick="return confirm('Refuser ce devis ?')">Refuser</button>
        </form>
    </div>
</div>
@elseif ($expired)
<div class="card"><p>Ce devis a expiré. Contactez-nous pour en obtenir un nouveau.</p></div>
@elseif ($q->status === S::Accepte)
<div class="card">
    <p>Accepté le {{ $q->accepted_at?->format('d/m/Y') }}
        @if ($q->accepted_by) (enregistré par votre conseiller) @endif</p>
    @if ($q->project) <p><a href="{{ route('client.projects.show', $q->project) }}">Voir mon projet {{ $q->project->reference }}</a></p> @endif
</div>
@elseif ($q->status === S::Refuse)
<div class="card"><p>Vous avez refusé ce devis le {{ $q->refused_at?->format('d/m/Y') }}.</p></div>
@endif
@endsection