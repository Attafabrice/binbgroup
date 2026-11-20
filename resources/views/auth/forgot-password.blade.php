@extends('layouts.auth')

@section('title', 'Mot de passe oublié')
@section('auth-title', 'Mot de passe oublié')
@section('auth-subtitle', 'Saisissez votre email, nous vous envoyons un lien.')

@section('content')
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn btn-primary">Envoyer le lien</button>
        <p class="auth-signup"><a href="{{ route('login') }}">Retour à la connexion</a></p>
    </form>
@endsection