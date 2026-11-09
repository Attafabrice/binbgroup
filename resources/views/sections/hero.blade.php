<section class="hero" id="accueil">
    <picture>
        <source media="(max-width: 768px)" srcset="{{ asset('assets/images/hero-mobile.webp') }}">
        <img class="hero-image" src="{{ asset('assets/images/hero.webp') }}" alt="" width="1920" height="1080" fetchpriority="high">
    </picture>

    <div class="container hero-content">
        <p class="hero-eyebrow">Bienvenue chez BinB' group</p>
        <h1 class="hero-title">Votre partenaire pour <span class="highlight">vos projets immobiliers</span> en Côte&nbsp;d'Ivoire</h1>
        <p class="hero-text">
            Professionnel ou particulier, nous vous accompagnons de l'étude de faisabilité à la réalisation :
            planification, division des terrains et obtention des autorisations nécessaires.
            Nous transformons vos rêves en réalité.
        </p>
        <div class="hero-actions">
            <a href="{{ route('home') }}#contact" class="btn btn-primary">
                Contactez-nous
                <svg class="icon"><use href="#icon-arrow-right"/></svg>
            </a>
            <a href="{{ route('quote.create') }}" class="btn btn-outline-light">Découvrir nos services</a>
        </div>
    </div>
</section>
