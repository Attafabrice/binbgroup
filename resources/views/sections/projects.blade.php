<section class="section section-light" id="realisations">
    <div class="container">
        <header class="section-header" data-reveal>
            <p class="section-label">Nos réalisations</p>
            <h2 class="section-title">Un aperçu de nos chantiers</h2>
        </header>

        <div class="filters" role="group" aria-label="Filtrer les réalisations" data-reveal>
            <button class="filter-btn is-active" type="button" data-filter="all" aria-pressed="true">Tout</button>
            <button class="filter-btn" type="button" data-filter="amenagement" aria-pressed="false">Aménagement</button>
            <button class="filter-btn" type="button" data-filter="batiment" aria-pressed="false">Bâtiment &amp; Maison</button>
            <button class="filter-btn" type="button" data-filter="route" aria-pressed="false">Route &amp; Passage</button>
        </div>

        <div class="projects">
            <article class="project" data-category="batiment" data-reveal>
                <a class="project-link" href="{{ asset('assets/images/project-maison.webp') }}" data-lightbox="projects">
                    <img src="{{ asset('assets/images/project-maison-thumb.webp') }}" alt="Maison individuelle à étage" width="445" height="445" loading="lazy">
                    <svg class="icon"><use href="#icon-plus"/></svg>
                </a>
                <p class="project-category">Bâtiment &amp; Maison</p>
                <h3 class="project-title">Construction de maison</h3>
            </article>
            <article class="project" data-category="route" data-reveal>
                <a class="project-link" href="{{ asset('assets/images/project-route.webp') }}" data-lightbox="projects">
                    <img src="{{ asset('assets/images/project-route-thumb.webp') }}" alt="Ouverture d'une route en latérite" width="600" height="600" loading="lazy">
                    <svg class="icon"><use href="#icon-plus"/></svg>
                </a>
                <p class="project-category">Route &amp; Passage</p>
                <h3 class="project-title">Construction de route</h3>
            </article>
            <article class="project" data-category="batiment" data-reveal>
                <a class="project-link" href="{{ asset('assets/images/project-batiment.webp') }}" data-lightbox="projects">
                    <img src="{{ asset('assets/images/project-batiment-thumb.webp') }}" alt="Immeuble en cours de construction" width="600" height="600" loading="lazy">
                    <svg class="icon"><use href="#icon-plus"/></svg>
                </a>
                <p class="project-category">Bâtiment &amp; Maison</p>
                <h3 class="project-title">Construction de bâtiment</h3>
            </article>
            <article class="project" data-category="amenagement" data-reveal>
                <a class="project-link" href="{{ asset('assets/images/project-jardin.webp') }}" data-lightbox="projects">
                    <img src="{{ asset('assets/images/project-jardin-thumb.webp') }}" alt="Piscine et jardin aménagés" width="600" height="600" loading="lazy">
                    <svg class="icon"><use href="#icon-plus"/></svg>
                </a>
                <p class="project-category">Aménagement</p>
                <h3 class="project-title">Jardin &amp; piscine</h3>
            </article>
        </div>
    </div>
</section>
