<footer class="footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a href="{{ route('home') }}#accueil" class="logo" aria-label="BinB' group, retour à l'accueil">
                <svg class="logo-mark"><use href="#logo-mark"/></svg>
                <span class="logo-text">
                    <span class="logo-name">BinB' <span>group</span></span>
                    <span class="logo-tagline">Immobilier | Import-Export | E-commerce</span>
                </span>
            </a>
            <p>Entreprise ivoirienne de construction, de travaux publics, de maintenance informatique et de services aux entreprises.</p>
        </div>

        <nav class="footer-column" aria-label="Navigation secondaire">
            <h2 class="footer-title">Navigation</h2>
            <ul>
                <li><a href="{{ route('home') }}#accueil">Accueil</a></li>
                <li><a href="{{ route('home') }}#a-propos">À propos</a></li>
                <li><a href="{{ route('home') }}#services">Services</a></li>
                <li><a href="{{ route('home') }}#realisations">Réalisations</a></li>
                <li><a href="{{ route('home') }}#contact">Contact</a></li>
            </ul>
        </nav>

        <div class="footer-column">
            <h2 class="footer-title">Services</h2>
            <ul>
            @foreach ($services->take(5) as $service)
                <li><a href="{{ route('home') }}#services" data-tab="tab-{{ $service->slug }}">{{ $service->name }}</a></li>
            @endforeach
        </ul>
        </div>

        <div class="footer-column">
            <h2 class="footer-title">Contact</h2>
            <ul>
                <li><a href="mailto:contact@binbgroup.com">contact@binbgroup.com</a></li>
                <li>Cocody 2 Plateaux, Abidjan</li>
                <li>Lun – Ven, 09h00 – 17h00</li>
            </ul>
        </div>
    </div>

    <div class="container">
        <div class="footer-bottom">
            <p>&copy; <span id="year">2026</span> BinB' group. Tous droits réservés.</p>
            <a href="{{ route('home') }}#accueil" class="footer-top">
                Haut de page
                <svg class="icon"><use href="#icon-arrow-up"/></svg>
            </a>
        </div>
    </div>
</footer>
