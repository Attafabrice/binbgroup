@extends('layouts.admin')
@section('title', 'Clients')
@section('nav') @include('admin._nav') @endsection

@section('content')
<form method="GET" class="card inline-form" style="margin-bottom:1rem">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom, email, téléphone">
    <select name="type">
        <option value="">Tous types</option>
        @foreach ($types as $t) <option value="{{ $t->value }}" @selected(request('type') === $t->value)>{{ ucfirst($t->value) }}</option> @endforeach
    </select>
    <select name="status">
        <option value="">Tous comptes</option>
        <option value="active" @selected(request('status') === 'active')>Actifs</option>
        <option value="suspended" @selected(request('status') === 'suspended')>Suspendus</option>
    </select>
    <button class="btn btn-primary">Filtrer</button>
    <a class="btn btn-ghost" href="{{ route('admin.clients.index') }}">Réinitialiser</a>
</form>

<div class="card">
<table class="table">
    <tr><th>Client</th><th>Contact</th><th>Type</th><th>Demandes</th><th>Devis</th><th>Projets</th><th>Compte</th><th></th></tr>
    @forelse ($clients as $c)
    <tr>
        <td>
            @if ($c->user?->avatar_url) <img class="avatar" src="{{ $c->user->avatar_url }}" alt=""> @endif
            {{ $c->display_name }}
        </td>
        <td>{{ $c->email }}<br><span class="muted">{{ $c->phone }}</span></td>
        <td>{{ ucfirst($c->type->value) }}</td>
        <td>{{ $c->quote_requests_count }}</td>
        <td>{{ $c->quotes_count }}</td>
        <td>{{ $c->projects_count }}</td>
        <td>
            @if (! $c->user) <span class="badge badge-gray">Sans compte</span>
            @elseif ($c->user->is_active) <span class="badge badge-success">Actif</span>
            @else <span class="badge badge-danger">Suspendu</span> @endif
        </td>
        <td><a class="btn btn-ghost" href="{{ route('admin.clients.show', $c) }}">Voir</a></td>
    </tr>
    @empty
    <tr><td colspan="8" class="muted">Aucun client.</td></tr>
    @endforelse
</table>
</div>
<div style="margin-top:1rem">{{ $clients->links() }}</div>
@endsection