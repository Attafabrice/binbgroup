@extends('layouts.auth')

@section('title', 'Créer un compte')
@section('auth-title', 'Créer un compte')
@section('auth-subtitle', 'Rejoignez BinB Group et démarrez votre projet.')

@push('styles')
<style>
    /* Grille pour le formulaire d'inscription */
    .register-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    /* Par défaut, tous les champs prennent 2 colonnes (toute la largeur) */
    .register-grid > .form-group {
        grid-column: span 2;
    }
    
    /* Sur mobile, on repasse sur une seule colonne */
    @media (max-width: 576px) {
        .register-grid {
            grid-template-columns: 1fr;
        }
        .register-grid > .form-group {
            grid-column: span 1;
        }
    }
</style>
@endpush

@section('content')
    <form method="POST" action="{{ route('register') }}" class="register-grid">
        @csrf

        {{-- Type de compte (pleine largeur) --}}
        <div class="form-group">
            <label for="type">Vous êtes</label>
            <select id="type" name="type" required>
                @foreach ($types as $t)
                    <option value="{{ $t->value }}" @selected(old('type', 'particulier') === $t->value)>
                        {{ $t->value === 'particulier' ? 'Un particulier' : 'Une entreprise' }}
                    </option>
                @endforeach
            </select>
            @error('type') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Nom de l'entreprise (pleine largeur, caché si particulier) --}}
        <div class="form-group" id="company-group">
            <label for="company_name">Nom de l'entreprise</label>
            <input
                id="company_name"
                type="text"
                name="company_name"
                value="{{ old('company_name') }}"
                autocomplete="organization"
                placeholder="Ex : BinB Group SARL"
            >
            @error('company_name') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Nom complet (1/2 largeur) --}}
        <div class="form-group" style="grid-column: span 1;">
            <label for="name">Nom et prénoms</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Jean Dupont"
            >
            @error('name') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Téléphone (1/2 largeur) --}}
        <div class="form-group" style="grid-column: span 1;">
            <label for="phone">Téléphone</label>
            <input
                id="phone"
                type="tel"
                name="phone"
                value="{{ old('phone') }}"
                required
                autocomplete="tel"
                inputmode="tel"
                placeholder="07 00 00 00 00"
            >
            @error('phone') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Adresse e-mail (pleine largeur) --}}
        <div class="form-group">
            <label for="email">Adresse e-mail</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="vous@exemple.com"
            >
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Mot de passe (1/2 largeur) --}}
        <div class="form-group" style="grid-column: span 1;">
            <label for="password">Mot de passe</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                minlength="8"
                placeholder="••••••••"
            >
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Confirmation du mot de passe (1/2 largeur) --}}
        <div class="form-group" style="grid-column: span 1;">
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                minlength="8"
                placeholder="••••••••"
            >
            @error('password_confirmation') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Honeypot (invisible) --}}
        <div class="hp" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>
        @error('website') <div class="error">{{ $message }}</div> @enderror

        {{-- Conditions d'utilisation (pleine largeur) --}}
        <div class="form-group" style="margin-top: 0.5rem; margin-bottom: 0.5rem;">
            <label class="check" style="font-weight: normal;">
                <input type="checkbox" name="terms" id="terms" value="1" required @checked(old('terms'))>
                <span>
                    J'accepte les
                    <a href="#" style="color: var(--c-primary); text-decoration: none;">conditions d'utilisation</a>
                    et la politique de confidentialité.
                </span>
            </label>
            @error('terms') <div class="error">{{ $message }}</div> @enderror
        </div>

        {{-- Bouton Soumettre (pleine largeur) --}}
        <div class="form-group" style="margin-top: 0;">
            <button type="submit" class="btn btn-primary">
                Créer mon compte
            </button>
        </div>

        {{-- Lien vers la connexion --}}
        <p class="auth-signup" style="grid-column: span 2; margin-top: 0;">
            Vous avez déjà un compte ?
            <a href="{{ route('login') }}">Se connecter</a>
        </p>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const type    = document.getElementById('type');
            const group   = document.getElementById('company-group');
            const company = document.getElementById('company_name');

            function sync() {
                const on = type.value === 'entreprise';
                group.style.display = on ? '' : 'none';
                company.required = on;
                if (!on) company.value = '';
            }

            type.addEventListener('change', sync);
            sync();
        });
    </script>
@endsection