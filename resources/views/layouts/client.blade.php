<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0f1b2d">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Mon espace') · BinB' group</title>
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
    @stack('styles')
</head>
<body class="client-body">

<a href="#contenu" class="c-skip">Aller au contenu</a>

<input type="checkbox" id="c-menu-toggle" class="c-menu-toggle" hidden>

<aside class="c-sidebar" id="c-sidebar">
    <a href="{{ route('home') }}" class="c-brand">BinB' <span>group</span></a>

    <nav class="c-nav" aria-label="Espace client">
        @include('client._nav')
    </nav>

    <div class="c-sidebar-foot">
        <a href="{{ route('client.profile.edit') }}" class="c-user" title="Mon profil">
            @if (auth()->user()->avatar_url)
                <img class="avatar" src="{{ auth()->user()->avatar_url }}" alt="">
            @else
                <span class="avatar" aria-hidden="true">{{ auth()->user()->initials }}</span>
            @endif
            <span class="c-user-name">{{ auth()->user()->name }}</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-ghost" type="submit">Déconnexion</button>
        </form>
    </div>
</aside>

<label for="c-menu-toggle" class="c-overlay" aria-hidden="true"></label>

<div class="c-content">
    <header class="c-topbar">
        <label for="c-menu-toggle" class="c-burger" role="button" tabindex="0" aria-label="Ouvrir le menu" aria-controls="c-sidebar">☰</label>
        <h1 class="c-title">@yield('title')</h1>
    </header>

    <main class="c-main" id="contenu">
        @if (session('success')) <div class="alert alert-success" role="status">{{ session('success') }}</div> @endif
        @if (session('error'))   <div class="alert alert-danger" role="alert">{{ session('error') }}</div> @endif

        @yield('content')
    </main>

    <footer class="c-footer">© {{ date('Y') }} BinB' group</footer>
</div>

<script>
    // Menu mobile : Échap ferme, Entrée/Espace ouvre au clavier
    (() => {
        const t = document.getElementById('c-menu-toggle');
        const b = document.querySelector('.c-burger');
        document.addEventListener('keydown', e => { if (e.key === 'Escape') t.checked = false; });
        b.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); t.checked = !t.checked; } });
    })();
</script>
@stack('scripts')
</body>
</html>