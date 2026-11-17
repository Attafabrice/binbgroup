<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Back-office') · BinB' group</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <div class="brand">BinB' group</div>
        <nav>@yield('nav')</nav>
    </aside>

    <div class="main">
        <header class="topbar">
            <strong>@yield('title')</strong>
            <div class="topbar-user">
                {{ auth()->user()->name }}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-ghost" type="submit">Déconnexion</button>
                </form>
            </div>
        </header>

        <main class="content">
            @if (session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
            @if (session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>