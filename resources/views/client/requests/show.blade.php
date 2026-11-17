@extends('layouts.client')

@section('title', 'Demande '.$r->reference)
@section('nav') @include('client._nav') @endsection

@section('content')
<div style="max-width: 900px; margin: 0 auto;">

    {{-- ========================================== --}}
    {{-- En-tête de la demande                      --}}
    {{-- ========================================== --}}
    <div class="card" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="margin-bottom: 0.5rem;">
                <x-status-badge :status="$r->status" />
            </div>
            <h2 style="margin: 0 0 0.25rem; font-size: 1.3rem; color: var(--c-text);">
                {{ $r->service?->name }}
            </h2>
            <p class="muted" style="margin: 0; font-size: 0.9rem;">
                <i class="fas fa-calendar-alt" style="margin-right: 0.35rem;"></i>
                Envoyée le {{ $r->created_at->format('d/m/Y à H:i') }}
            </p>
        </div>
        <a class="btn btn-ghost" href="{{ route('client.requests.index') }}">
            <i class="fas fa-arrow-left" style="margin-right: 0.35rem;"></i> Retour
        </a>
    </div>

    {{-- ========================================== --}}
    {{-- Détails de la demande                      --}}
    {{-- ========================================== --}}
    <div class="card" style="margin-bottom: 1.5rem;">
        <h3 style="margin-bottom: 1rem; font-size: 1.1rem; color: var(--c-text); border-bottom: 1px solid var(--c-border); padding-bottom: 0.75rem;">
            <i class="fas fa-info-circle" style="color: var(--c-primary); margin-right: 0.5rem;"></i>
            Détails de la demande
        </h3>
        
        <div style="display: grid; gap: 0.75rem;">
            @forelse ($answers as $a)
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; padding: 0.5rem 0; border-bottom: 1px dashed var(--c-border);">
                    <span class="muted" style="min-width: 180px; font-weight: 500;">{{ $a['label'] }}</span>
                    <span style="flex: 1; color: var(--c-text);">{{ $a['value'] }}</span>
                </div>
            @empty
                <p class="muted" style="margin: 0;">Aucun détail.</p>
            @endforelse
        </div>

        @if ($r->message)
            <div style="margin-top: 1.5rem; background: var(--c-bg); padding: 1rem; border-radius: var(--radius);">
                <p class="muted" style="margin: 0 0 0.5rem; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em;">Précisions</p>
                <p style="margin: 0; white-space: pre-line;">{{ $r->message }}</p>
            </div>
        @endif
    </div>

    {{-- ========================================== --}}
    {{-- Documents joints                           --}}
    {{-- ========================================== --}}
    @if ($r->visibleDocuments->isNotEmpty())
    <div class="card" style="margin-bottom: 1.5rem;">
        <h3 style="margin-bottom: 1rem; font-size: 1.1rem; color: var(--c-text); border-bottom: 1px solid var(--c-border); padding-bottom: 0.75rem;">
            <i class="fas fa-paperclip" style="color: var(--c-primary); margin-right: 0.5rem;"></i>
            Documents joints
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            @foreach ($r->visibleDocuments as $d)
                <a href="{{ route('files.document', $d) }}" target="_blank" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: var(--c-bg); border-radius: var(--radius); text-decoration: none; color: var(--c-text); transition: background 0.15s;">
                    <i class="fas fa-file-alt" style="color: var(--c-primary); font-size: 1.2rem;"></i>
                    <div>
                        <div style="font-weight: 500;">{{ $d->title }}</div>
                        <div class="muted" style="font-size: 0.8rem;">{{ $d->original_name }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ========================================== --}}
    {{-- Message de l'équipe                        --}}
    {{-- ========================================== --}}
    @if ($r->decision_note)
    <div class="card" style="border-left: 4px solid var(--c-primary);">
        <h3 style="margin-bottom: 0.75rem; font-size: 1.1rem; color: var(--c-text);">
            <i class="fas fa-comment-dots" style="color: var(--c-primary); margin-right: 0.5rem;"></i>
            Message de notre équipe
        </h3>
        <p style="margin: 0; color: var(--c-text); line-height: 1.6;">{{ $r->decision_note }}</p>
    </div>
    @endif

</div>
@endsection