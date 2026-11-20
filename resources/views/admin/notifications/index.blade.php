@extends('layouts.admin')

@section('title', 'Notifications')
@section('nav') @include('admin._nav') @endsection

@section('content')
<div style="max-width: 900px; margin: 0 auto;">

    {{-- En-tête avec bouton --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <h2 style="margin: 0; font-size: 1.25rem; color: var(--c-text);">
            <i class="fas fa-bell" style="color: var(--c-primary); margin-right: 0.5rem;"></i>
            Mes notifications
        </h2>
        
        <form method="POST" action="{{ route('admin.notifications.read-all') }}">
            @csrf
            <button class="btn btn-ghost" type="submit" style="font-size: 0.85rem;">
                <i class="fas fa-check-double" style="margin-right: 0.35rem;"></i> Tout marquer comme lu
            </button>
        </form>
    </div>

    {{-- Liste des notifications --}}
    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="table" style="margin: 0;">
            <tbody>
                @forelse ($items as $n)
                <tr @class(['unread' => $n->unread()]) style="border-bottom: 1px solid var(--c-border); {{ $n->unread() ? 'background: #f0f7ff; border-left: 3px solid var(--c-primary);' : '' }}">
                    <td style="padding: 1rem; width: 40px; text-align: center; vertical-align: top;">
                        @if ($n->unread())
                            <i class="fas fa-circle" style="color: var(--c-primary); font-size: 0.5rem;"></i>
                        @else
                            <i class="far fa-circle" style="color: var(--c-border); font-size: 0.5rem;"></i>
                        @endif
                    </td>
                    <td style="padding: 1rem;">
                        <a href="{{ route('admin.notifications.open', $n->id) }}" style="text-decoration: none; color: var(--c-text); font-weight: {{ $n->unread() ? '600' : '500' }}; display: block; margin-bottom: 0.25rem;">
                            {{ $n->data['title'] }}
                        </a>
                        <span class="muted" style="font-size: 0.9rem; line-height: 1.4;">
                            {{ $n->data['body'] }}
                        </span>
                    </td>
                    <td class="muted" style="padding: 1rem; text-align: right; white-space: nowrap; vertical-align: top; font-size: 0.85rem;">
                        <i class="far fa-clock" style="margin-right: 0.25rem;"></i>
                        {{ $n->created_at->diffForHumans() }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="padding: 3rem 1rem; text-align: center; color: var(--c-muted);">
                        <i class="fas fa-bell-slash" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.4;"></i>
                        Aucune notification pour le moment.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="margin-top: 1.5rem;">
        {{ $items->links() }}
    </div>

</div>
@endsection