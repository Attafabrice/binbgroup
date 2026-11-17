@extends('layouts.client')

@section('title', 'Mes demandes')
@section('nav') @include('client._nav') @endsection

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2 style="font-size: 1.25rem; margin: 0; color: var(--c-text);">Historique de mes demandes</h2>
    <a class="btn btn-primary" href="{{ route('client.requests.create') }}">
        <i class="fas fa-plus" style="margin-right: 0.5rem;"></i> Nouvelle demande
    </a>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table class="table" style="margin: 0;">
            <thead>
                <tr style="background: var(--c-bg);">
                    <th style="padding: 1rem; border-bottom: 1px solid var(--c-border);">Référence</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--c-border);">Service</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--c-border);">Statut</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--c-border);">Envoyée le</th>
                    <th style="padding: 1rem; border-bottom: 1px solid var(--c-border);"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($requests as $r)
                <tr style="border-bottom: 1px solid var(--c-border);">
                    <td style="padding: 1rem; font-weight: 600;">{{ $r->reference }}</td>
                    <td style="padding: 1rem;">{{ $r->service?->name }}</td>
                    <td style="padding: 1rem;"><x-status-badge :status="$r->status" /></td>
                    <td style="padding: 1rem; color: var(--c-muted);">{{ $r->created_at->format('d/m/Y') }}</td>
                    <td style="padding: 1rem; text-align: right;">
                        <a class="btn btn-ghost" href="{{ route('client.requests.show', $r) }}" style="font-size: 0.85rem;">
                            Voir <i class="fas fa-arrow-right" style="margin-left: 0.25rem;"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 3rem 1rem; text-align: center; color: var(--c-muted);">
                        <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                        Aucune demande pour le moment.<br>
                        <span style="font-size: 0.9rem;">Décrivez votre projet pour recevoir un devis.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 1.5rem;">
    {{ $requests->links() }}
</div>
@endsection