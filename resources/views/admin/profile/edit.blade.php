@extends('layouts.admin')
@section('title', 'Mon profil')
@section('nav') @include('admin._nav') @endsection

@section('content')
<div class="card avatar-card" style="margin-bottom:1rem">
    @if ($user->avatar_url)
        <img class="avatar avatar-lg" src="{{ $user->avatar_url }}" alt="Ma photo">
    @else
        <span class="avatar avatar-lg">{{ $user->initials }}</span>
    @endif
    <div>
        <h3>Photo de profil</h3>
        <form method="POST" action="{{ route('admin.profile.avatar') }}" enctype="multipart/form-data" class="inline-form">
            @csrf
            <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" required>
            <button class="btn btn-primary">Envoyer</button>
        </form>
        <p class="muted">JPG, PNG ou WEBP, 2 Mo maximum.</p>
        @error('avatar') <p class="error">{{ $message }}</p> @enderror
        @if ($user->avatar_path)
        <form method="POST" action="{{ route('admin.profile.avatar.remove') }}" style="margin-top:.5rem">
            @csrf @method('DELETE')<button class="btn btn-ghost">Supprimer ma photo</button>
        </form>
        @endif
    </div>
</div>

<form method="POST" action="{{ route('admin.profile.update') }}" class="card" style="margin-bottom:1rem">
    @csrf @method('PUT')
    <h3>Mes informations</h3>
    <p class="muted" style="margin-bottom:1rem">Rôle : {{ $user->getRoleNames()->implode(', ') }}</p>
    <div class="grid-2">
        <div class="form-group"><label>Nom</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name') <p class="error">{{ $message }}</p> @enderror</div>
        <div class="form-group"><label>Téléphone</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}">
            @error('phone') <p class="error">{{ $message }}</p> @enderror</div>
        <div class="form-group"><label>Email (identifiant de connexion)</label>
            <input type="email" value="{{ $user->email }}" disabled></div>
    </div>
    <button class="btn btn-primary">Enregistrer</button>
</form>

<form method="POST" action="{{ route('admin.profile.password') }}" class="card">
    @csrf @method('PUT')
    <h3>Changer mon mot de passe</h3>
    <div class="grid-2">
        <div class="form-group"><label>Mot de passe actuel</label>
            <input type="password" name="current_password" autocomplete="current-password" required>
            @if ($errors->password->has('current_password')) <p class="error">{{ $errors->password->first('current_password') }}</p> @endif</div>
        <div></div>
        <div class="form-group"><label>Nouveau mot de passe (8 caractères min.)</label>
            <input type="password" name="password" autocomplete="new-password" required>
            @if ($errors->password->has('password')) <p class="error">{{ $errors->password->first('password') }}</p> @endif</div>
        <div class="form-group"><label>Confirmer</label>
            <input type="password" name="password_confirmation" autocomplete="new-password" required></div>
    </div>
    <button class="btn btn-primary">Modifier le mot de passe</button>
</form>
@endsection