@extends('frontend.layouts.master')

@section('title', 'Nos expériences — Morada Lodge')
@section('meta_description', 'Piscine, table du terroir, excursions dans la vallée du Zou, événements privés : tout ce que Morada Lodge met à votre disposition à Covè.')

@section('content')

    {{-- ══════════ EN-TÊTE DE PAGE ══════════ --}}
    <section class="m-pagehead">
        <div class="m-hero-media">
            @php
                $headPhoto = collect(['img/morada/head-services.jpg', 'img/morada/exp-piscine.jpg', 'img/accueil.jpg'])
                    ->first(fn ($p) => is_file(public_path($p)));
            @endphp
            @if($headPhoto)
                <img src="{{ asset($headPhoto) }}" alt="Nos expériences" fetchpriority="high">
            @endif
        </div>
        <div class="m-hero-scrim"></div>

        <div class="m-shell" style="position:relative;z-index:2;padding-top:90px">
            <span class="m-eyebrow is-center">À vivre sur place</span>
            <h1>Nos expériences</h1>
            <p class="m-lead" style="margin-inline:auto;color:rgba(255,255,255,.82)">
                Au-delà de la chambre : la table, la piscine, la nature et les rencontres.
            </p>

            <div class="m-crumb" style="margin-top:22px">
                <a href="{{ route('frontend.home') }}">Accueil</a>
                <span style="margin:0 10px;color:var(--m-gold)">/</span>Expériences
            </div>
        </div>
    </section>

    {{-- ══════════ LES TEMPS FORTS ══════════ --}}
    <section class="m-section">
        <div class="m-shell">
            <div style="text-align:center;margin-bottom:clamp(40px,5vw,72px)" data-reveal>
                <span class="m-eyebrow is-center">Les temps forts</span>
                <h2 class="m-title">Quatre façons de <em>profiter du lodge</em></h2>
            </div>

            @php
                $highlights = [
                    [
                        'title' => 'La piscine',
                        'text' => "Un bassin bordé de transats, à l'ombre des manguiers. Serviettes fournies, service de boissons en bord de bassin, ouvert du lever au coucher du soleil.",
                        'photo' => 'img/morada/exp-piscine.jpg',
                        'tags' => ['Transats & parasols', 'Service au bassin', 'Ouvert toute l\'année'],
                    ],
                    [
                        'title' => 'La table du terroir',
                        'text' => "Amiwo, atassi, poisson braisé au feu de bois et grillades : notre chef cuisine ce que le marché de Covè donne le matin même.",
                        'photo' => 'img/morada/exp-restaurant.jpg',
                        'tags' => ['Petit-déjeuner jusqu\'à 11 h', 'Service en chambre', 'Table privatisable'],
                    ],
                    [
                        'title' => 'Nature & excursions',
                        'text' => "Balades guidées dans la vallée du Zou, rencontre des artisans, marchés locaux. Nos équipes vivent ici et organisent tout sur demande.",
                        'photo' => 'img/morada/exp-nature.jpg',
                        'tags' => ['Guides locaux', 'Sur mesure', 'Demi-journée ou journée'],
                    ],
                    [
                        'title' => 'Événements privés',
                        'text' => "Mariages, séminaires, anniversaires : le lodge se privatise entièrement, hébergement et restauration compris.",
                        'photo' => 'img/morada/exp-evenements.jpg',
                        'tags' => ['Privatisation totale', 'Salle de travail', 'Devis sous 48 h'],
                    ],
                ];
            @endphp

            @foreach($highlights as $i => $item)
                <div class="m-split" style="margin-bottom:clamp(44px,6vw,96px)">
                    <div data-reveal="{{ $i % 2 === 0 ? 'left' : 'right' }}"
                         style="{{ $i % 2 === 0 ? '' : 'order:2' }}">
                        @include('frontend.partials.photo', [
                            'src' => $item['photo'],
                            'alt' => $item['title'],
                            'ratio' => 'm-ratio-43',
                            'label' => $item['title'],
                        ])
                    </div>

                    <div data-reveal="{{ $i % 2 === 0 ? 'right' : 'left' }}"
                         style="{{ $i % 2 === 0 ? '' : 'order:1' }}">
                        <span class="m-eyebrow">0{{ $i + 1 }}</span>
                        <h3 class="m-title" style="font-size:var(--m-h3)">{{ $item['title'] }}</h3>
                        <p class="m-lead" style="margin-bottom:22px">{{ $item['text'] }}</p>

                        <ul class="m-room-feats">
                            @foreach($item['tags'] as $tag)
                                <li><i class="fas fa-diamond"></i> <span>{{ $tag }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ══════════ SERVICES INCLUS ══════════ --}}
    <section class="m-section m-cream">
        <div class="m-shell">
            <div style="text-align:center;margin-bottom:clamp(36px,4vw,64px)" data-reveal>
                <span class="m-eyebrow is-center">Toujours compris</span>
                <h2 class="m-title">Les petites attentions <em>qui changent tout</em></h2>
            </div>

            @php
                $services = [
                    ['icon' => 'fa-wifi', 'title' => 'Wi-Fi haut débit', 'text' => 'Gratuit et couvrant tout l\'établissement, chambres comprises.'],
                    ['icon' => 'fa-car', 'title' => 'Parking sécurisé', 'text' => 'Stationnement gratuit, gardiennage 24 h/24.'],
                    ['icon' => 'fa-concierge-bell', 'title' => 'Conciergerie', 'text' => 'Taxis, excursions, réservations : une équipe disponible à toute heure.'],
                    ['icon' => 'fa-snowflake', 'title' => 'Climatisation', 'text' => 'Chaque chambre dispose de la climatisation et d\'un ventilateur.'],
                    ['icon' => 'fa-leaf', 'title' => 'Démarche responsable', 'text' => 'Produits locaux, tri des déchets, consommation d\'eau maîtrisée.'],
                    ['icon' => 'fa-shield-halved', 'title' => 'Sécurité', 'text' => 'Site clos, veille permanente et coffre disponible à la réception.'],
                ];
            @endphp

            <div class="row g-4">
                @foreach($services as $i => $service)
                    <div class="col-lg-4 col-md-6">
                        <div data-reveal data-reveal-delay="{{ $i * 90 }}"
                             style="background:var(--m-ivory);padding:34px 30px;height:100%;box-shadow:var(--m-shadow);border-top:2px solid var(--m-gold)">
                            <i class="fas {{ $service['icon'] }}"
                               style="font-size:1.5rem;color:var(--m-clay);margin-bottom:18px;display:block"></i>
                            <h3 style="font-size:1.22rem;margin-bottom:8px">{{ $service['title'] }}</h3>
                            <p style="margin:0;font-size:.93rem;color:var(--m-muted)">{{ $service['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ══════════ APPEL À L'ACTION ══════════ --}}
    <section class="m-section m-dark">
        <div class="m-shell" style="text-align:center">
            <div data-reveal>
                <span class="m-eyebrow is-center">Prêt à venir ?</span>
                <h2 class="m-title">Composons ensemble <em>votre séjour</em></h2>
                <p class="m-lead" style="margin:0 auto 32px">
                    Dites-nous ce que vous aimez, nous nous occupons du reste — de la chambre
                    à la table, en passant par les excursions.
                </p>

                <div style="display:flex;flex-wrap:wrap;gap:14px;justify-content:center">
                    <a href="{{ route('frontend.reservation') }}" class="m-btn m-btn-gold">
                        Réserver <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('frontend.contact') }}" class="m-btn m-btn-light">
                        Nous écrire
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection
