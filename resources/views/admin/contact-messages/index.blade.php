@extends('layouts.admin')

@section('title', 'Messages de contact')
@section('nav') @include('admin._nav') @endsection

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">

    {{-- ========================================== --}}
    {{-- Filtres                                   --}}
    {{-- ========================================== --}}
    <div class="actions" style="margin-bottom: 1.5rem; gap: 0.5rem;">
        <a class="btn {{ request('filter') === null ? 'btn-primary' : 'btn-ghost' }}" href="{{ route('admin.contact-messages.index') }}">
            Tous
        </a>
        <a class="btn {{ request('filter') === 'unread' ? 'btn-primary' : 'btn-ghost' }}" href="{{ route('admin.contact-messages.index', ['filter' => 'unread']) }}">
            Non lus
        </a>
        <a class="btn {{ request('filter') === 'todo' ? 'btn-primary' : 'btn-ghost' }}" href="{{ route('admin.contact-messages.index', ['filter' => 'todo']) }}">
            À traiter
        </a>
    </div>

    {{-- ========================================== --}}
    {{-- Tableau                                   --}}
    {{-- ========================================== --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="table" style="margin: 0;">
                <thead>
                    <tr style="background: var(--c-bg);">
                        <th style="padding: 1rem; border-bottom: 1px solid var(--c-border);">Reçu le</th>
                        <th style="padding: 1rem; border-bottom: 1px solid var(--c-border);">De</th>
                        <th style="padding: 1rem; border-bottom: 1px solid var(--c-border);">Sujet</th>
                        <th style="padding: 1rem; border-bottom: 1px solid var(--c-border);">Statut</th>
                        <th style="padding: 1rem; border-bottom: 1px solid var(--c-border);"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($messages as $m)
                    <tr @class(['unread' => ! $m->read_at]) style="border-bottom: 1px solid var(--c-border);">
                        <td style="padding: 1rem; color: var(--c-muted); font-size: 0.9rem; white-space: nowrap;">
                            {{ $m->created_at->format('d/m/Y') }}<br>
                            <span style="font-size: 0.8rem;">{{ $m->created_at->format('H:i') }}</span>
                        </td>
                        <td style="padding: 1rem;">
                            <strong style="color: var(--c-text);">{{ $m->name }}</strong><br>
                            <span class="muted" style="font-size: 0.85rem;">{{ $m->email }}</span>
                        </td>
                        <td style="padding: 1rem; color: var(--c-text);">
                            {{ $m->subject ?: str($m->message)->limit(60) }}
                        </td>
                        <td style="padding: 1rem;">
                            @if ($m->handled_at) 
                                <span class="badge badge-success">Traité</span>
                            @elseif (! $m->read_at) 
                                <span class="badge badge-primary">Nouveau</span>
                            @else 
                                <span class="badge badge-warning">À traiter</span> 
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            <a class="btn btn-ghost" href="{{ route('admin.contact-messages.show', $m) }}" style="font-size: 0.85rem;">
                                Ouvrir <i class="fas fa-arrow-right" style="margin-left: 0.25rem;"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 3rem 1rem; text-align: center; color: var(--c-muted);">
                            <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.4;"></i>
                            Aucun message.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- Pagination                                --}}
    {{-- ========================================== --}}
    <div style="margin-top: 1.5rem;">
        {{ $messages->links() }}
    </div>

</div>
@endsection