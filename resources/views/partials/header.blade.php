<header class="header" id="header">
    <div class="container header-inner">
        <a href="{{ route('home') }}#accueil" class="logo" aria-label="BinB' group, retour à l'accueil">
            <svg class="logo-mark"><use href="#logo-mark"/></svg>
            <span class="logo-text">
                <span class="logo-name">BinB' <span>group</span></span>
            </span>
        </a>

        <nav class="nav" id="nav" aria-label="Navigation principale">
            <ul class="nav-list">
                <li><a class="nav-link" href="{{ route('home') }}#accueil">Accueil</a></li>
                <li><a class="nav-link" href="{{ route('home') }}#a-propos">À propos</a></li>
                <li><a class="nav-link" href="{{ route('home') }}#services">Services</a></li>
                <li><a class="nav-link" href="{{ route('home') }}#realisations">Réalisations</a></li>
                <li><a class="nav-link" href="{{ route('home') }}#equipe">Équipe</a></li>

                @auth
                    @php
                        $u = auth()->user();
                        $home = $u->isStaff() ? route('admin.dashboard') : route('client.dashboard');
                        $label = $u->isStaff() ? 'Administration' : 'Mon espace';
                    @endphp
                    <li>
                        <a class="nav-link nav-account" href="{{ $home }}" title="{{ $label }}" aria-label="{{ $label }}">
                            @if ($u->avatar_url)
                                <img class="avatar" src="{{ $u->avatar_url }}" alt="">
                            @else
                                <span class="avatar" aria-hidden="true">{{ $u->initials }}</span>
                            @endif
                            <span class="nav-account-label">{{ $label }}</span>
                        </a>
                    </li>
                @else
                    <li><a class="nav-link" href="{{ route('login') }}">Connexion</a></li>
                    <li><a class="nav-link" href="{{ route('register') }}">S'inscrire</a></li>
                @endauth
            </ul>
            <a href="{{ route('home') }}#contact" class="btn btn-primary nav-cta">Nous contacter</a>
        </nav>

        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav" aria-label="Ouvrir le menu">
            <span></span>
        </button>
    </div>
</header>