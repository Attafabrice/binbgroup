<section class="section contact" id="contact">
    <div class="container contact-grid">
        <div class="contact-info" data-reveal>
            <p class="section-label">Contactez-nous</p>
            <h2 class="section-title">Un projet&nbsp;? Une question&nbsp;?</h2>
            <p>Laissez-nous un message, nous vous recontacterons dans les plus brefs délais.</p>

            <ul class="contact-list">
                <li>
                    <svg class="icon"><use href="#icon-mail"/></svg>
                    <div>
                        <span class="contact-label">Email</span>
                        <a href="mailto:contact@binbgroup.com">contact@binbgroup.com</a>
                    </div>
                </li>
                <li>
                    <svg class="icon"><use href="#icon-map-pin"/></svg>
                    <div>
                        <span class="contact-label">Adresse</span>
                        Cocody 2 Plateaux, Abidjan
                    </div>
                </li>
                <li>
                    <svg class="icon"><use href="#icon-clock"/></svg>
                    <div>
                        <span class="contact-label">Horaires</span>
                        Lundi – Vendredi, 09h00 – 17h00
                    </div>
                </li>
            </ul>
        </div>

        <form class="contact-form" id="contact-form" action="mailto:contact@binbgroup.com" method="post" enctype="text/plain" novalidate data-reveal>
            <div class="form-row">
                <div class="form-field">
                    <label for="name">Nom complet</label>
                    <input type="text" id="name" name="name" autocomplete="name" required>
                </div>
                <div class="form-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" autocomplete="email" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-field">
                    <label for="phone">Téléphone <span>(facultatif)</span></label>
                    <input type="tel" id="phone" name="phone" autocomplete="tel">
                </div>
                <div class="form-field">
                    <label for="service">Service concerné</label>
                    <select id="service" name="service">
                    <option value="">Choisir un service</option>
                    @foreach ($services as $service)
                        <option>{{ $service->name }}</option>
                    @endforeach
                    <option>Autre demande</option>
                </select>
                </div>
            </div>
            <div class="form-field">
                <label for="message">Message</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            <button class="btn btn-primary" type="submit">
                Envoyer le message
                <svg class="icon"><use href="#icon-arrow-right"/></svg>
            </button>
            <p class="form-status" role="status" aria-live="polite"></p>
        </form>
    </div>
</section>
