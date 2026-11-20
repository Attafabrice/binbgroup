@extends('layouts.admin')
@section('title', 'Utilisateurs')
@section('nav') @include('admin._nav') @endsection

@section('content')
<form method="GET" class="card inline-form" style="margin-bottom:1rem">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Nom ou email">
    <select name="type">
        <option value="">Tous les comptes</option>
        <option value="staff" @selected(request('type') === 'staff')>Équipe</option>
        <option value="client" @selected(request('type') === 'client')>Clients</option>
        <option value="none" @selected(request('type') === 'none')>Sans rôle</option>
    </select>
    <select name="status">
        <option value="">Tous statuts</option>
        <option value="active" @selected(request('status') === 'active')>Actifs</option>
        <option value="suspended" @selected(request('status') === 'suspended')>Suspendus</option>
    </select>
    <button class="btn btn-primary">Filtrer</button>
    <a class="btn btn-ghost" href="{{ route('admin.users.index') }}">Réinitialiser</a>
</form>

<div class="card">
<table class="table">
    <tr><th>Compte</th><th>Rôle</th><th>Statut</th><th>Dernière connexion</th><th></th></tr>
    @forelse ($users as $u)
    <tr>
        <td>
            @if ($u->avatar_url) <img class="avatar" src="{{ $u->avatar_url }}" alt=""> @else <span class="avatar">{{ $u->initials }}</span> @endif
            <strong>{{ $u->name }}</strong><br><span class="muted">{{ $u->email }}</span>
        </td>
        <td>{{ $u->getRoleNames()->implode(', ') ?: '—' }}</td>
        <td><span class="badge {{ $u->is_active ? 'badge-success' : 'badge-danger' }}">{{ $u->is_active ? 'Actif' : 'Suspendu' }}</span></td>
        <td class="muted">{{ $u->last_login_at?->format('d/m/Y H:i') ?? 'jamais' }}</td>
        <td class="actions">
            @if ($u->client) <a class="btn btn-ghost" href="{{ route('admin.clients.show', $u->client) }}">Fiche client</a> @endif
            @can('manageAccount', $u)
                @if ($accounts->canManage(auth()->user(), $u))
                <form method="POST" action="{{ route('admin.users.toggle', $u) }}" onsubmit="return confirm('{{ $u->is_active ? 'Suspendre' : 'Réactiver' }} ce compte ?')">
                    @csrf<button class="btn btn-ghost">{{ $u->is_active ? 'Suspendre' : 'Réactiver' }}</button>
                </form>
                <form method="POST" action="{{ route('admin.users.reset', $u) }}" onsubmit="return confirm('Envoyer un lien de réinitialisation ?')">
                    @csrf<button class="btn btn-ghost">Lien mot de passe</button>
                </form>
                @endif
            @endcan
        </td>
    </tr>
    @empty
    <tr><td colspan="5" class="muted">Aucun compte.</td></tr>
    @endforelse
</table>
</div>
<div style="margin-top:1rem">{{ $users->links() }}</div>
@endsection