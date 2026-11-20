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
                {{-- Cloche de notifications --}}
                @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                <a href="{{ route('admin.notifications.index') }}" class="bell" title="Notifications">
                    🔔<span class="dot" id="dot-notif" @if(!$unread) hidden @endif>{{ $unread }}</span>
                </a>

                {{-- Profil --}}
                <a href="{{ route('admin.profile.edit') }}" class="topbar-profile">
                    @if (auth()->user()->avatar_url)
                        <img class="avatar" src="{{ auth()->user()->avatar_url }}" alt="">
                    @else
                        <span class="avatar">{{ auth()->user()->initials }}</span>
                    @endif
                    {{ auth()->user()->name }}
                </a>

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

{{-- Notifications en quasi temps réel : polling AJAX toutes les 30 s --}}
<script>
(() => {
    const url = @json(route('admin.notifications.summary'));
    const set = (id, n) => {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = n;
        el.hidden = !n;
    };
    let last = null;

    async function poll() {
        if (document.hidden) return; // onglet inactif : on ne sollicite pas le serveur
        try {
            const r = await fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
            if (!r.ok) return; // session expirée : on s'arrête sans bruit
            const d = await r.json();
            set('dot-notif', d.notifications);
            set('dot-msg', d.messages);
            if (last !== null && d.notifications > last) {
                document.title = '(' + d.notifications + ') ' + document.title.replace(/^\(\d+\) /, '');
            }
            last = d.notifications;
        } catch (e) { /* réseau coupé : on réessaie au prochain cycle */ }
    }

    setInterval(poll, 30000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) poll(); });
})();
</script>
</body>
</html>