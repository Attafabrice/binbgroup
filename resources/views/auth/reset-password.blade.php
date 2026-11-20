@extends('layouts.auth')

@section('title', 'Nouveau mot de passe')
@section('auth-title', 'Nouveau mot de passe')
@section('auth-subtitle', 'Choisissez un mot de passe d’au moins 8 caractères.')

@section('content')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="username">
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label for="password">Nouveau mot de passe</label>
            <input id="password" type="password" name="password" required autofocus autocomplete="new-password">
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirmer</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        </div>
        <button type="submit" class="btn btn-primary">Modifier le mot de passe</button>
    </form>
@endsection