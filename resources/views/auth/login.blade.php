@extends('layouts.auth')

@section('title', 'Connexion')
@section('auth-title', 'Connexion')
@section('auth-subtitle', 'Heureux de vous revoir parmi nous.')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="vous@exemple.com"
            >
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Mot de passe --}}
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
            >
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Options --}}
        <div class="auth-links">
            <label class="check" style="margin: 0;">
                <input type="checkbox" name="remember" id="remember">
                Se souvenir de moi
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            @endif
        </div>

        {{-- Soumettre --}}
        <button type="submit" class="btn btn-primary" style="margin-top: 1.5rem;">
            Se connecter
        </button>

        {{-- Inscription --}}
        @if (Route::has('register'))
            <p class="auth-signup">
                Pas encore de compte ?
                <a href="{{ route('register') }}">Créer un compte</a>
            </p>
        @endif
    </form>
@endsection