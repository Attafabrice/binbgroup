@extends('layouts.admin')
@section('title', $member->exists ? 'Membre : '.$member->name : 'Nouveau membre')
@section('nav') @include('admin._nav') @endsection

@section('content')
<p style="margin-bottom:1rem"><a class="btn btn-ghost" href="{{ route('admin.team.index') }}">← Retour</a></p>

<form method="POST" class="card" style="margin-bottom:1rem"
      action="{{ $member->exists ? route('admin.team.update', $member) : route('admin.team.store') }}">
    @csrf
    @if ($member->exists) @method('PUT') @endif
    <h3>Informations</h3>
    <div class="grid-2">
        <div class="form-group"><label>Nom</label>
            <input type="text" name="name" value="{{ old('name', $member->name) }}" required>
            @error('name') <p class="error">{{ $message }}</p> @enderror</div>
        <div class="form-group"><label>Email (identifiant de connexion)</label>
            <input type="email" name="email" value="{{ old('email', $member->email) }}" @disabled($member->exists) required>
            @error('email') <p class="error">{{ $message }}</p> @enderror</div>
        <div class="form-group"><label>Téléphone</label>
            <input type="text" name="phone" value="{{ old('phone', $member->phone) }}">
            @error('phone') <p class="error">{{ $message }}</p> @enderror</div>
        <div class="form-group"><label>Rôle</label>
            <select name="role" required>
                <option value="">— choisir —</option>
                @foreach ($roles as $r)
                    <option value="{{ $r }}" @selected(old('role', $member->roles->first()?->name) === $r)>{{ $r }}</option>
                @endforeach
            </select>
            @error('role') <p class="error">{{ $message }}</p> @enderror</div>
    </div>
    @unless ($member->exists) <p class="muted" style="margin-bottom:1rem">Aucun mot de passe à saisir : un email lui permet de choisir le sien.</p> @endunless
    <button class="btn btn-primary">{{ $member->exists ? 'Enregistrer' : 'Créer et envoyer l’invitation' }}</button>
</form>

@if ($member->exists)
<div class="card actions">
    @can('manageAccount', $member)
    <form method="POST" action="{{ route('admin.team.toggle', $member) }}" onsubmit="return confirm('Confirmer ?')">
        @csrf<button class="btn {{ $member->is_active ? 'btn-danger' : 'btn-success' }}">{{ $member->is_active ? 'Suspendre' : 'Réactiver' }}</button>
    </form>
    <form method="POST" action="{{ route('admin.team.invite', $member) }}" onsubmit="return confirm('Envoyer un nouveau lien ?')">
        @csrf<button class="btn btn-ghost">Renvoyer le lien d'accès</button>
    </form>
    @endcan
    @can('delete', $member)
    <form method="POST" action="{{ route('admin.team.destroy', $member) }}" onsubmit="return confirm('Supprimer ce compte ?')">
        @csrf @method('DELETE')<button class="btn btn-ghost">Supprimer</button>
    </form>
    @endcan
</div>
@endif
@endsection