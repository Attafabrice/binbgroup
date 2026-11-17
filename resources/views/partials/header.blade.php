<header class="header" id="header">
    <div class="container header-inner">
        <a href="{{ route('home') }}#accueil" class="logo" aria-label="BinB' group, retour à l'accueil">
            <svg class="logo-mark"><use href="#logo-mark"/></svg>
            <span class="logo-text">
                <span class="logo-name">BinB' <span>group</span></span>
                <!-- <span class="logo-tagline">Immobilier | Import-Export | E-commerce</span> -->
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
                    @php $u = auth()->user(); @endphp
                    <li>
                        <a class="nav-link" href="{{ $u->isStaff() ? route('admin.dashboard') : route('client.dashboard') }}">
                            {{ $u->isStaff() ? 'Administration' : 'Mon espace' }}
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
