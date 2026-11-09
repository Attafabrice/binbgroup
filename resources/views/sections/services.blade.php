<section class="section section-light" id="services">
    <div class="container">
        <header class="section-header" data-reveal>
            <p class="section-label">Nos services</p>
            <h2 class="section-title">La satisfaction du client, avant tout, par la qualité de nos prestations</h2>
        </header>

        @if ($services->isNotEmpty())
            <div class="services" data-reveal>
                <div class="services-tabs" role="tablist" aria-label="Nos services">
                    @foreach ($services as $service)
                        <button class="service-tab" type="button" role="tab" id="tab-{{ $service->slug }}" aria-controls="panel-{{ $service->slug }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            <span class="service-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $service->name }}
                        </button>
                    @endforeach
                </div>

                <div class="service-panels">
                    @foreach ($services as $service)
                        <article class="service-panel {{ $loop->first ? 'is-active' : '' }}" id="panel-{{ $service->slug }}" role="tabpanel" aria-labelledby="tab-{{ $service->slug }}">
                            @if ($service->image)
                                <img class="service-image" src="{{ asset('assets/images/'.$service->image) }}" alt="{{ $service->name }}" width="1000" height="750" loading="lazy">
                            @endif
                            <div class="service-body">
                                <h3 class="service-title">{{ $service->name }}</h3>
                                @if ($service->lead)
                                    <p class="service-lead">{{ $service->lead }}</p>
                                @endif
                                @if ($service->description)
                                    <p>{{ $service->description }}</p>
                                @endif
                                <div class="service-actions">
                                    <a href="{{ route('quote.service', $service->slug) }}" class="btn btn-primary">
                                        Demander un devis
                                        <svg class="icon"><use href="#icon-arrow-right"/></svg>
                                    </a>
                                    <a href="{{ route('home') }}#contact" class="btn btn-outline" data-service="{{ $service->name }}">
                                        Prenons contact
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @else
            <p>Nos services seront bientôt disponibles.</p>
        @endif
    </div>
</section>
