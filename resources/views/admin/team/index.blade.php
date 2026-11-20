@extends('layouts.admin')
@section('title', 'Équipe')
@section('nav') @include('admin._nav') @endsection

@section('content')
<div class="actions" style="margin-bottom:1rem;justify-content:space-between">
    <form method="GET" class="inline-form">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom ou email">
        <button class="btn btn-ghost">Rechercher</button>
    </form>
    @can('create', App\Models\User::class)
        <a class="btn btn-primary" href="{{ route('admin.team.create') }}">Ajouter un membre</a>
    @endcan
</div>

<div class="card">
<table class="table">
    <tr><th>Membre</th><th>Rôle</th><th>Statut</th><th>Dernière connexion</th><th></th></tr>
    @forelse ($members as $m)
    <tr>
        <td>
            @if ($m->avatar_url) <img class="avatar" src="{{ $m->avatar_url }}" alt=""> @else <span class="avatar">{{ $m->initials }}</span> @endif
            <strong>{{ $m->name }}</strong><br><span class="muted">{{ $m->email }}</span>
        </td>
        <td>{{ $m->getRoleNames()->implode(', ') ?: '—' }}</td>
        <td><span class="badge {{ $m->is_active ? 'badge-success' : 'badge-danger' }}">{{ $m->is_active ? 'Actif' : 'Suspendu' }}</span></td>
        <td class="muted">{{ $m->last_login_at?->format('d/m/Y H:i') ?? 'invitation en attente' }}</td>
        <td>@if (auth()->id() !== $m->id) <a class="btn btn-ghost" href="{{ route('admin.team.edit', $m) }}">Gérer</a> @else <span class="muted">Vous</span> @endif</td>
    </tr>
    @empty
    <tr><td colspan="5" class="muted">Aucun membre.</td></tr>
    @endforelse
</table>
</div>
<div style="margin-top:1rem">{{ $members->links() }}</div>
@endsection