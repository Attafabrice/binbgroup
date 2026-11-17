@extends('layouts.client')
@section('title', 'Mes devis')

@section('content')
<div class="card">
<table class="table">
    <tr><th>Référence</th><th>Objet</th><th>Total TTC</th><th>Valable jusqu'au</th><th>Statut</th><th></th></tr>
    @forelse ($quotes as $q)
    <tr>
        <td>{{ $q->reference }}</td>
        <td>{{ $q->title }}</td>
        <td>{{ \App\Models\Quote::money($q->total) }}</td>
        <td>{{ $q->valid_until?->format('d/m/Y') }}</td>
        <td>
            @if ($q->status === \App\Enums\QuoteStatus::Envoye && $q->isExpired())
                <span class="badge badge-warning">Expiré</span>
            @else
                <x-status-badge :status="$q->status" />
            @endif
        </td>
        <td><a class="btn btn-ghost" href="{{ route('client.quotes.show', $q) }}">Consulter</a></td>
    </tr>
    @empty
    <tr><td colspan="6" class="muted">Aucun devis pour le moment. Ils apparaîtront ici dès que notre équipe vous les enverra.</td></tr>
    @endforelse
</table>
</div>
<div style="margin-top:1rem">{{ $quotes->links() }}</div>
@endsection