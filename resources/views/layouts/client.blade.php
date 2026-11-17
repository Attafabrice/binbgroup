<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mon espace') · BinB' group</title>
    <link rel="stylesheet" href="{{ asset('css/client.css') }}">
</head>
<body class="client-body">

<input type="checkbox" id="c-menu-toggle" class="c-menu-toggle" hidden>

<aside class="c-sidebar">
    <a href="{{ route('home') }}" class="c-brand">BinB' <span>group</span></a>

    <nav class="c-nav" aria-label="Espace client">
        @include('client._nav')
    </nav>

    <div class="c-sidebar-foot">
        <div class="c-user-name">{{ auth()->user()->name }}</div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-ghost" type="submit">Déconnexion</button>
        </form>
    </div>
</aside>

<label for="c-menu-toggle" class="c-overlay"></label>

<div class="c-content">
    <header class="c-topbar">
        <label for="c-menu-toggle" class="c-burger" aria-label="Ouvrir le menu">☰</label>
        <h1 class="c-title">@yield('title')</h1>
    </header>

    <main class="c-main">
        @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if (session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

        @yield('content')
    </main>

    <footer class="c-footer">© {{ date('Y') }} BinB' group</footer>
</div>

</body>
</html>