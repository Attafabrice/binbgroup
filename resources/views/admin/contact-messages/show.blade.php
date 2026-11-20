@extends('layouts.admin')

@section('title', 'Message de '.$m->name)
@section('nav') @include('admin._nav') @endsection

@section('content')
<div style="max-width: 900px; margin: 0 auto;">

    {{-- ========================================== --}}
    {{-- En-tête de l'expéditeur                   --}}
    {{-- ========================================== --}}
    <div class="card" style="margin-bottom: 1.5rem;">
        <div class="actions" style="justify-content: space-between;">
            <div>
                <strong style="font-size: 1.1rem;">{{ $m->name }}</strong>
                <p class="muted" style="margin: 0.25rem 0 0; font-size: 0.85rem;">
                    <i class="far fa-clock" style="margin-right: 0.35rem;"></i>
                    Reçu le {{ $m->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            <a class="btn btn-ghost" href="{{ route('admin.contact-messages.index') }}">
                <i class="fas fa-arrow-left" style="margin-right: 0.35rem;"></i> Retour
            </a>
        </div>
        
        <div class="muted" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--c-border); display: flex; gap: 1.5rem; flex-wrap: wrap; font-size: 0.9rem;">
            <span>
                <i class="far fa-envelope" style="margin-right: 0.35rem;"></i>
                <a href="mailto:{{ $m->email }}" style="color: inherit; text-decoration: none;">{{ $m->email }}</a>
            </span>
            @if ($m->phone)
                <span>
                    <i class="fas fa-phone-alt" style="margin-right: 0.35rem;"></i>
                    <a href="tel:{{ $m->phone }}" style="color: inherit; text-decoration: none;">{{ $m->phone }}</a>
                </span>
            @endif
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- Contenu du message                        --}}
    {{-- ========================================== --}}
    <div class="card" style="margin-bottom: 1.5rem;">
        @if ($m->subject)
            <h3 style="margin-bottom: 1rem; font-size: 1.2rem; color: var(--c-text); border-bottom: 1px solid var(--c-border); padding-bottom: 0.75rem;">
                {{ $m->subject }}
            </h3>
        @endif
        <p style="white-space: pre-line; line-height: 1.7; color: var(--c-text); margin: 0;">{{ $m->message }}</p>
    </div>

    {{-- ========================================== --}}
    {{-- Actions                                   --}}
    {{-- ========================================== --}}
    <div class="card actions" style="justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div class="actions" style="gap: 0.75rem;">
            <a class="btn btn-primary" href="mailto:{{ $m->email }}?subject={{ rawurlencode('Re: '.($m->subject ?: 'Votre message à BinB group')) }}">
                <i class="fas fa-reply" style="margin-right: 0.35rem;"></i> Répondre par email
            </a>

            @can('delete', $m)
                <form method="POST" action="{{ route('admin.contact-messages.destroy', $m) }}" onsubmit="return confirm('Supprimer ce message ?')" style="margin: 0;">
                    @csrf 
                    @method('DELETE')
                    <button class="btn btn-ghost" type="submit" style="color: var(--c-danger); border-color: var(--c-danger);">
                        <i class="fas fa-trash-alt" style="margin-right: 0.35rem;"></i> Supprimer
                    </button>
                </form>
            @endcan
        </div>

        @can('update', $m)
            <div style="margin-left: auto;">
                @if ($m->handled_at)
                    <span class="muted" style="font-size: 0.85rem;">
                        <i class="fas fa-check-circle" style="color: var(--c-success); margin-right: 0.35rem;"></i>
                        Traité le {{ $m->handled_at->format('d/m/Y H:i') }} par <strong>{{ $m->handler?->name }}</strong>
                    </span>
                @else
                    <form method="POST" action="{{ route('admin.contact-messages.handle', $m) }}" style="margin: 0;">
                        @csrf
                        <button class="btn btn-ghost" type="submit">
                            <i class="fas fa-check" style="margin-right: 0.35rem;"></i> Marquer comme traité
                        </button>
                    </form>
                @endif
            </div>
        @endcan
    </div>

</div>
@endsection