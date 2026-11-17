@extends('layouts.admin')
@section('title', 'Devis')
@section('nav') @include('admin._nav') @endsection

@section('content')
<form method="GET" class="card inline-form" style="margin-bottom:1rem">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Référence">
    <select name="status">
        <option value="">Tous les statuts</option>
        @foreach ($statuses as $s)
            <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->getLabel() }}</option>
        @endforeach
    </select>
    <button class="btn btn-primary">Filtrer</button>
    <a class="btn btn-ghost" href="{{ route('admin.quotes.index') }}">Réinitialiser</a>
</form>

<div class="card">
<table class="table">
    <tr><th>Référence</th><th>Client</th><th>Service</th><th>Total TTC</th><th>Statut</th><th>Valable jusqu'au</th><th></th></tr>
    @forelse ($quotes as $q)
    <tr>
        <td>{{ $q->reference }}</td>
        <td>{{ $q->client?->display_name }}</td>
        <td>{{ $q->service?->name }}</td>
        <td>{{ \App\Models\Quote::money($q->total) }}</td>
        <td><x-status-badge :status="$q->status" /></td>
        <td>{{ $q->valid_until?->format('d/m/Y') }}</td>
        <td><a class="btn btn-ghost" href="{{ route('admin.quotes.show', $q) }}">Voir</a></td>
    </tr>
    @empty
    <tr><td colspan="7">Aucun devis.</td></tr>
    @endforelse
</table>
</div>
<div style="margin-top:1rem">{{ $quotes->links() }}</div>
@endsection