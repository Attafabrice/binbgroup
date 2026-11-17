@extends('layouts.admin')
@section('title', 'Demandes de devis')
@section('nav') @include('admin._nav') @endsection

@section('content')
<form method="GET" class="card inline-form" style="margin-bottom:1rem">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Référence ou nom">
    <select name="status">
        <option value="">Tous les statuts</option>
        @foreach ($statuses as $s)
            <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->getLabel() }}</option>
        @endforeach
    </select>
    <button class="btn btn-primary">Filtrer</button>
    <a class="btn btn-ghost" href="{{ route('admin.quote-requests.index') }}">Réinitialiser</a>
</form>

<div class="card">
<table class="table">
    <tr><th>Référence</th><th>Client</th><th>Service</th><th>Statut</th><th>Assignée à</th><th>Reçue le</th><th></th></tr>
    @forelse ($requests as $r)
    <tr>
        <td>{{ $r->reference }}</td>
        <td>{{ $r->client?->display_name ?? $r->name }}</td>
        <td>{{ $r->service?->name }}</td>
        <td><x-status-badge :status="$r->status" /></td>
        <td>{{ $r->assignee?->name ?? '—' }}</td>
        <td>{{ $r->created_at->format('d/m/Y') }}</td>
        <td><a class="btn btn-ghost" href="{{ route('admin.quote-requests.show', $r) }}">Voir</a></td>
    </tr>
    @empty
    <tr><td colspan="7">Aucune demande.</td></tr>
    @endforelse
</table>
</div>
<div style="margin-top:1rem">{{ $requests->links() }}</div>
@endsection