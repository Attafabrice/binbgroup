/* ==========================================================================
   BinB' group — Interactions
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    initHeader();
    initMobileNav();
    initActiveLinks();
    initReveal();
    initServiceTabs();
    initProjectFilters();
    initReadMore();
    initLightbox();
    initContactForm();

    document.getElementById('year').textContent = new Date().getFullYear();
});


/* En-tête : fond blanc au scroll + bouton retour en haut
   ========================================================================== */

function initHeader() {
    const header = document.getElementById('header');
    const backToTop = document.querySelector('.back-to-top');

    const update = () => {
        header.classList.toggle('is-scrolled', window.scrollY > 20);
        backToTop.classList.toggle('is-visible', window.scrollY > 600);
    };

    update();
    window.addEventListener('scroll', update, { passive: true });
}


/* Menu mobile
   ========================================================================== */

function initMobileNav() {
    const header = document.getElementById('header');
    const toggle = document.querySelector('.nav-toggle');
    const nav = document.getElementById('nav');

    const setOpen = (open) => {
        header.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', open);
        toggle.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
        document.body.classList.toggle('no-scroll', open);
    };

    toggle.addEventListener('click', () => {
        setOpen(!header.classList.contains('is-open'));
    });

    // Fermer après un clic sur un lien
    nav.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => setOpen(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && header.classList.contains('is-open')) {
            setOpen(false);
            toggle.focus();
        }
    });

    // Repasser en affichage bureau referme le menu
    window.matchMedia('(min-width: 1025px)').addEventListener('change', (event) => {
        if (event.matches) setOpen(false);
    });
}


/* Lien de navigation actif selon la section visible
   ========================================================================== */

function initActiveLinks() {
    const links = document.querySelectorAll('.nav-link');
    const sections = [...links]
        .map((link) => (link.hash ? document.querySelector(link.hash) : null))
        .filter(Boolean);

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            links.forEach((link) => {
                link.classList.toggle('is-active', link.hash === '#' + entry.target.id);
            });
        });
    }, { rootMargin: '-45% 0px -50% 0px' });

    sections.forEach((section) => observer.observe(section));
}


/* Apparition des éléments au scroll
   ========================================================================== */

function initReveal() {
    const elements = document.querySelectorAll('[data-reveal]');

    // Léger décalage entre les éléments d'une même grille
    elements.forEach((element) => {
        const siblings = [...element.parentElement.children].filter((child) => child.hasAttribute('data-reveal'));
        const index = siblings.indexOf(element);
        if (index > 0) element.style.transitionDelay = `${index * 90}ms`;
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

    elements.forEach((element) => observer.observe(element));
}


/* Onglets des services
   ========================================================================== */

function initServiceTabs() {
    const tabs = [...document.querySelectorAll('.service-tab')];

    const activate = (tab, focus = false) => {
        tabs.forEach((item) => {
            const selected = item === tab;
            item.setAttribute('aria-selected', selected);
            item.tabIndex = selected ? 0 : -1;
            document.getElementById(item.getAttribute('aria-controls')).classList.toggle('is-active', selected);
        });

        if (focus) tab.focus();

        // Sur mobile, les onglets défilent horizontalement : on centre l'onglet actif
        const list = tab.parentElement;
        list.scrollTo({ left: tab.offsetLeft - (list.clientWidth - tab.offsetWidth) / 2, behavior: 'smooth' });
    };

    tabs.forEach((tab, index) => {
        tab.tabIndex = index === 0 ? 0 : -1;

        tab.addEventListener('click', () => activate(tab));

        // Navigation au clavier entre les onglets
        tab.addEventListener('keydown', (event) => {
            const keys = {
                ArrowDown: index + 1,
                ArrowRight: index + 1,
                ArrowUp: index - 1,
                ArrowLeft: index - 1,
                Home: 0,
                End: tabs.length - 1,
            };
            if (!(event.key in keys)) return;

            event.preventDefault();
            const next = (keys[event.key] + tabs.length) % tabs.length;
            activate(tabs[next], true);
        });
    });

    // Liens du pied de page qui ouvrent un service précis
    document.querySelectorAll('[data-tab]').forEach((link) => {
        link.addEventListener('click', () => {
            activate(document.getElementById(link.dataset.tab));
        });
    });
}


/* Filtres des réalisations
   ========================================================================== */

function initProjectFilters() {
    const buttons = document.querySelectorAll('.filter-btn');
    const projects = document.querySelectorAll('.project');

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            const filter = button.dataset.filter;

            buttons.forEach((item) => {
                item.classList.toggle('is-active', item === button);
                item.setAttribute('aria-pressed', item === button);
            });

            projects.forEach((project) => {
                const visible = filter === 'all' || project.dataset.category === filter;
                project.hidden = !visible;

                // Relance l'animation d'apparition
                project.classList.remove('is-filtered-in');
                if (visible) {
                    void project.offsetWidth;
                    project.classList.add('is-filtered-in');
                }
            });
        });
    });
}


/* Bouton « Lire plus »
   ========================================================================== */

function initReadMore() {
    document.querySelectorAll('.read-more').forEach((button) => {
        const content = document.getElementById(button.getAttribute('aria-controls'));
        const label = button.querySelector('span');

        button.addEventListener('click', () => {
            const open = button.getAttribute('aria-expanded') !== 'true';
            button.setAttribute('aria-expanded', open);
            content.classList.toggle('is-open', open);
            label.textContent = open ? 'Réduire' : 'Lire plus';
        });
    });
}


/* Visionneuse d'images
   ========================================================================== */

function initLightbox() {
    const lightbox = document.getElementById('lightbox');
    const image = lightbox.querySelector('.lightbox-image');
    const caption = lightbox.querySelector('.lightbox-caption');
    const prev = lightbox.querySelector('.lightbox-prev');
    const next = lightbox.querySelector('.lightbox-next');

    let group = [];
    let current = 0;

    const show = (index) => {
        current = (index + group.length) % group.length;
        const link = group[current];
        const alt = link.querySelector('img').alt;

        image.src = link.href;
        image.alt = alt;
        caption.textContent = alt;
    };

    document.querySelectorAll('[data-lightbox]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();

            // Seules les images visibles du même groupe sont parcourues
            group = [...document.querySelectorAll(`[data-lightbox="${link.dataset.lightbox}"]`)]
                .filter((item) => !item.closest('[hidden]'));

            prev.hidden = next.hidden = group.length < 2;
            show(group.indexOf(link));
            lightbox.showModal();
        });
    });

    prev.addEventListener('click', () => show(current - 1));
    next.addEventListener('click', () => show(current + 1));
    lightbox.querySelector('.lightbox-close').addEventListener('click', () => lightbox.close());

    // Clic en dehors de l'image
    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) lightbox.close();
    });

    lightbox.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') show(current - 1);
        if (event.key === 'ArrowRight') show(current + 1);
    });
}


/* Formulaire de contact
   Sans serveur, le message est préparé dans la messagerie du visiteur.
   ========================================================================== */

function initContactForm() {
    const form = document.getElementById('contact-form');
    const status = form.querySelector('.form-status');
    const serviceSelect = form.querySelector('#service');

    // « Prenons contact » pré-remplit le service concerné
    document.querySelectorAll('[data-service]').forEach((link) => {
        link.addEventListener('click', () => {
            serviceSelect.value = link.dataset.service;
        });
    });

    form.querySelectorAll('input, textarea').forEach((field) => {
        field.addEventListener('input', () => {
            field.closest('.form-field').classList.remove('has-error');
        });
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        const invalidFields = [...form.querySelectorAll('input, textarea')].filter((field) => !field.checkValidity());

        form.querySelectorAll('.form-field').forEach((field) => field.classList.remove('has-error'));
        invalidFields.forEach((field) => field.closest('.form-field').classList.add('has-error'));

        if (invalidFields.length) {
            status.className = 'form-status is-error';
            status.textContent = 'Merci de renseigner votre nom, une adresse email valide et votre message.';
            invalidFields[0].focus();
            return;
        }

        const data = new FormData(form);
        const subject = `Demande de contact${data.get('service') ? ' – ' + data.get('service') : ''}`;
        const body = [
            `Nom : ${data.get('name')}`,
            `Email : ${data.get('email')}`,
            `Téléphone : ${data.get('phone') || '-'}`,
            '',
            data.get('message'),
        ].join('\n');

        window.location.href = `mailto:contact@binbgroup.com?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;

        status.className = 'form-status is-success';
        status.textContent = 'Merci ! Votre messagerie va s\'ouvrir pour finaliser l\'envoi.';
        form.reset();
    });
}
