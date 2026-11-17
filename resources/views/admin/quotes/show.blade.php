@extends('layouts.admin')
@section('title', 'Devis '.$q->reference)
@section('nav') @include('admin._nav') @endsection

@section('content')
@php
    use App\Enums\QuoteStatus as S;
    $draft = ! $q->isLocked();
@endphp

<div class="card" style="margin-bottom:1rem">
    <div class="actions" style="justify-content:space-between">
        <div>
            <x-status-badge :status="$q->status" />
            <strong>{{ $q->client?->display_name }}</strong> · {{ $q->service?->name }}
            @if ($q->quoteRequest)
                · <a href="{{ route('admin.quote-requests.show', $q->quoteRequest) }}">{{ $q->quoteRequest->reference }}</a>
            @endif
        </div>
        <a class="btn btn-ghost" href="{{ route('admin.quotes.index') }}">← Retour</a>
    </div>
</div>

<div class="card" style="margin-bottom:1rem">
    <h3>Informations</h3>
    @if ($draft)
        @can('update', $q)
        <form method="POST" action="{{ route('admin.quotes.update', $q) }}">
            @csrf @method('PUT')
            <div class="grid-2">
                <div class="form-group"><label>Objet</label>
                    <input type="text" name="title" value="{{ old('title', $q->title) }}" required>
                    @error('title') <p class="error">{{ $message }}</p> @enderror</div>
                <div class="form-group"><label>Valable jusqu'au</label>
                    <input type="date" name="valid_until" value="{{ old('valid_until', $q->valid_until?->format('Y-m-d')) }}" required>
                    @error('valid_until') <p class="error">{{ $message }}</p> @enderror</div>
                <div class="form-group"><label>TVA (%)</label>
                    <input type="number" step="0.01" name="tax_rate" value="{{ old('tax_rate', $q->tax_rate) }}" required>
                    @error('tax_rate') <p class="error">{{ $message }}</p> @enderror</div>
                <div class="form-group"><label>Notes</label>
                    <textarea name="notes" rows="2">{{ old('notes', $q->notes) }}</textarea></div>
            </div>
            <button class="btn btn-primary">Enregistrer</button>
        </form>
        @endcan
    @else
        <p><strong>{{ $q->title }}</strong></p>
        <p class="muted">Valable jusqu'au {{ $q->valid_until?->format('d/m/Y') }} · envoyé le {{ $q->sent_at?->format('d/m/Y H:i') }}</p>
        @if ($q->notes) <p>{{ $q->notes }}</p> @endif
    @endif
</div>

<div class="card" style="margin-bottom:1rem">
    <h3>Lignes</h3>
    <table class="table">
        <tr><th>Désignation</th><th>Qté</th><th>Prix unitaire</th><th>Total</th><th></th></tr>
        @forelse ($q->items as $i)
        <tr>
            <td>{{ $i->designation }}</td>
            <td>{{ rtrim(rtrim($i->quantity, '0'), '.') }} {{ $i->unit }}</td>
            <td>{{ \App\Models\Quote::money($i->unit_price) }}</td>
            <td>{{ \App\Models\Quote::money($i->total) }}</td>
            <td>
                @if ($draft) @can('update', $q)
                <form method="POST" action="{{ route('admin.quotes.items.destroy', [$q, $i]) }}" onsubmit="return confirm('Supprimer cette ligne ?')">
                    @csrf @method('DELETE')<button class="btn btn-ghost">×</button>
                </form>
                @endcan @endif
            </td>
        </tr>
        @empty
        <tr><td colspan="5" class="muted">Aucune ligne.</td></tr>
        @endforelse
    </table>

    <div class="totals">
        <p>Sous-total HT : <strong>{{ \App\Models\Quote::money($q->subtotal) }}</strong></p>
        <p>TVA ({{ rtrim(rtrim($q->tax_rate, '0'), '.') }} %) : <strong>{{ \App\Models\Quote::money($q->tax_amount) }}</strong></p>
        <p>Total TTC : <strong>{{ \App\Models\Quote::money($q->total) }}</strong></p>
    </div>

    @if ($draft) @can('update', $q)
    <form method="POST" action="{{ route('admin.quotes.items.store', $q) }}" class="inline-form" style="margin-top:1rem">
        @csrf
        <input type="text" name="designation" placeholder="Désignation" value="{{ old('designation') }}" required>
        <input type="number" step="0.01" name="quantity" placeholder="Qté" value="{{ old('quantity', 1) }}" required style="width:90px">
        <input type="text" name="unit" placeholder="Unité (m², forfait...)" value="{{ old('unit') }}" style="width:150px">
        <input type="number" name="unit_price" placeholder="Prix unitaire" value="{{ old('unit_price') }}" required style="width:140px">
        <button class="btn btn-primary">Ajouter</button>
    </form>
    @foreach (['designation', 'quantity', 'unit', 'unit_price'] as $f) @error($f) <p class="error">{{ $message }}</p> @enderror @endforeach
    @endcan @endif
</div>

@if ($q->status === S::Accepte)
<div class="card" style="margin-bottom:1rem">
    <h3>Acceptation</h3>
    <p>Le {{ $q->accepted_at?->format('d/m/Y H:i') }}
       @if ($q->accepted_by) par {{ $q->acceptedBy?->name }} (hors ligne : {{ $q->acceptance_channel }}) @else par le client (portail) @endif</p>
    @if ($q->acceptance_note) <p class="muted">{{ $q->acceptance_note }}</p> @endif
    @if ($q->project) <p>Projet : {{ $q->project->reference }}</p> @endif
</div>
@endif

@if ($q->status === S::Refuse)
<div class="card" style="margin-bottom:1rem">
    <h3>Refus</h3>
    <p>Le {{ $q->refused_at?->format('d/m/Y H:i') }}</p>
    @if ($q->refusal_reason) <p class="muted">{{ $q->refusal_reason }}</p> @endif
</div>
@endif

@if ($draft)
    @can('send', $q)
    <div class="card">
        <h3>Envoi</h3>
        <p class="muted">L'envoi fige le devis et prévient le client par email.</p>
        <form method="POST" action="{{ route('admin.quotes.send', $q) }}">
            @csrf
            <button class="btn btn-primary" onclick="return confirm('Envoyer ce devis ? Il ne sera plus modifiable.')">Envoyer au client</button>
        </form>
    </div>
    @endcan
@elseif ($q->status === S::Envoye && ! $q->isExpired())
    @can('acceptOffline', $q)
    <div class="card">
        <h3>Enregistrer une acceptation hors ligne</h3>
        <form method="POST" action="{{ route('admin.quotes.accept-offline', $q) }}" enctype="multipart/form-data">
            @csrf
            <div class="grid-2">
                <div class="form-group"><label>Canal</label>
                    <select name="channel" required>
                        <option value="phone">Téléphone</option><option value="whatsapp">WhatsApp</option>
                        <option value="paper">Papier signé</option><option value="email">Email</option>
                    </select></div>
                <div class="form-group"><label>Justificatif (facultatif)</label><input type="file" name="proof"></div>
            </div>
            <div class="form-group"><label>Précisions (date, interlocuteur)</label>
                <textarea name="note" rows="2" required>{{ old('note') }}</textarea>
                @error('note') <p class="error">{{ $message }}</p> @enderror</div>
            <button class="btn btn-primary" onclick="return confirm('Enregistrer l\'acceptation et créer le projet ?')">Enregistrer</button>
        </form>
    </div>
    @endcan
@endif
@endsection