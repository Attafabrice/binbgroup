@extends('layouts.admin')
@section('title', 'Demande '.$r->reference)
@section('nav') @include('admin._nav') @endsection

@section('content')
@php
    use App\Enums\QuoteRequestStatus as S;
    $open = in_array($r->status, [S::Nouvelle, S::EnCours], true);
@endphp

<div class="card" style="margin-bottom:1rem">
    <div class="actions" style="justify-content:space-between">
        <div>
            <x-status-badge :status="$r->status" />
            <strong>{{ $r->service?->name }}</strong> · reçue le {{ $r->created_at->format('d/m/Y à H:i') }}
        </div>
        <a class="btn btn-ghost" href="{{ route('admin.quote-requests.index') }}">← Retour</a>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <h3>Client</h3>
        <p><strong>{{ $r->client?->display_name ?? $r->name }}</strong></p>
        <p>{{ $r->email }}</p>
        <p>{{ $r->phone }}</p>
        <p class="muted">Assignée à : {{ $r->assignee?->name ?? '—' }}</p>
    </div>

    <div class="card">
        <h3>Détails de la demande</h3>
        @forelse ($answers as $a)
            <p><span class="muted">{{ $a['label'] }} :</span> {{ $a['value'] }}</p>
        @empty
            <p class="muted">Aucun détail.</p>
        @endforelse
        @if ($r->message)
            <p style="margin-top:.75rem"><span class="muted">Précisions :</span><br>{{ $r->message }}</p>
        @endif
    </div>
</div>

<div class="card" style="margin-top:1rem">
    <h3>Documents joints</h3>
    @forelse ($r->documents as $d)
        <p><a href="{{ route('files.document', $d) }}" target="_blank">{{ $d->title }} — {{ $d->original_name }}</a></p>
    @empty
        <p class="muted">Aucun document.</p>
    @endforelse
</div>

@if ($r->decision_note)
<div class="card" style="margin-top:1rem">
    <h3>Décision</h3>
    <p>{{ $r->decision_note }}</p>
    <p class="muted">Par {{ $r->reviewer?->name }} le {{ $r->reviewed_at?->format('d/m/Y H:i') }}</p>
</div>
@endif

@if ($r->quotes->isNotEmpty())
<div class="card" style="margin-top:1rem">
    <h3>Devis liés</h3>
    @foreach ($r->quotes as $q)
        <p>{{ $q->reference }} : <x-status-badge :status="$q->status" /></p>
    @endforeach
</div>
@endif

@if ($open)
<div class="card" style="margin-top:1rem">
    <h3>Actions</h3>

    @can('take', $r)
        @if ($r->status === S::Nouvelle)
            <form method="POST" action="{{ route('admin.quote-requests.take', $r) }}" style="margin-bottom:1rem">
                @csrf
                <button class="btn btn-ghost">Prendre en charge</button>
            </form>
        @endif
    @endcan

    <div class="grid-2">
        @can('accept', $r)
        <form method="POST" action="{{ route('admin.quote-requests.accept', $r) }}">
            @csrf
            <div class="form-group">
                <label>Message au client (facultatif)</label>
                <textarea name="note" rows="3">{{ old('note') }}</textarea>
            </div>
            <button class="btn btn-primary" onclick="return confirm('Valider cette demande ? Un devis brouillon sera créé.')">Valider</button>
        </form>
        @endcan

        @can('reject', $r)
        <form method="POST" action="{{ route('admin.quote-requests.reject', $r) }}">
            @csrf
            <div class="form-group">
                <label>Motif du refus (obligatoire)</label>
                <textarea name="reason" rows="3" required>{{ old('reason') }}</textarea>
                @error('reason') <p class="error">{{ $message }}</p> @enderror
            </div>
            <button class="btn btn-danger" onclick="return confirm('Refuser cette demande ?')">Refuser</button>
        </form>
        @endcan
    </div>
</div>
@endif
@endsection