@extends('frontend.layouts.master')

@section('title', 'Morada Lodge — Refuge de luxe à Covè, Bénin')
@section('meta_description', 'Bungalows, suites et chambres d\'exception à Covè. Restaurant, piscine et nature préservée : Morada Lodge réinvente l\'hospitalité béninoise.')

@section('content')

@php
    // Les sections illustrées basculent sur une variante typographique
    // tant que la photo correspondante n'a pas été déposée.
    $heroPhoto       = Helper::photo(['img/morada/hero.jpg', 'img/accueil.jpg']);
    $storyPhoto      = Helper::photo(['img/morada/lodge-01.jpg', 'img/morada/entree.jpg']);
    $storyPhotoB     = Helper::photo('img/morada/lodge-02.jpg');
    $storyPhotoC     = Helper::photo('img/morada/lodge-03.jpg');
    $restaurantPhoto = Helper::photo(['img/morada/restaurant.jpg', 'img/morada/exp-restaurant.jpg']);
    $closingPhoto    = Helper::photo(['img/morada/entree.jpg', 'img/accueil.jpg']);

    $gallery = collect([
        'img/morada/galerie-01.jpg', 'img/morada/galerie-02.jpg', 'img/morada/galerie-03.jpg',
        'img/morada/galerie-04.jpg', 'img/morada/galerie-05.jpg', 'img/morada/galerie-06.jpg',
    ])->map(fn ($p) => Helper::photo($p))->filter()->values();
@endphp

{{-- ══════════════════ HÉROS ══════════════════ --}}
<section class="m-hero">
    <div class="m-hero-media">
        @if($heroPhoto)
            <img src="{{ $heroPhoto }}" alt="Morada Lodge, Covè" fetchpriority="high">
        @endif
    </div>
    <div class="m-hero-scrim"></div>

    <div class="m-hero-body">
        <div class="m-shell-wide">
            <div class="m-hero-kicker">
                <span><i class="fas fa-location-dot"></i> Covè · République du Bénin</span>
                <span class="m-hero-rule"></span>
                <span><i class="fas fa-star"></i> Hébergement &amp; restauration</span>
                <span class="m-hero-rule"></span>
                <span><i class="fas fa-clock"></i> Réception 24 h/24</span>
            </div>

            <h1 data-words>Le luxe a trouvé <em>son refuge</em></h1>

            <p class="m-hero-sub">
                Entre jardins, terrasses privées et silence de la nature, Morada Lodge
                conjugue l'hospitalité béninoise et le raffinement d'une adresse rare.
            </p>

            <div class="m-hero-actions">
                <a href="{{ route('frontend.reservation') }}" class="m-btn m-btn-gold">
                    Réserver votre séjour <i class="fas fa-arrow-right"></i>
                </a>
                <a href="{{ route('frontend.rooms') }}" class="m-btn m-btn-light">
                    Découvrir les chambres
                </a>
            </div>
        </div>
    </div>

    <div class="m-scroll-cue" aria-hidden="true">
        Défiler
        <span></span>
    </div>
</section>

{{-- ══════════════════ RECHERCHE DE DISPONIBILITÉ ══════════════════ --}}
<div class="m-shell" style="position:relative;z-index:5">
    <div class="m-booking" data-reveal="fade">
        <form action="{{ route('frontend.rooms') }}" method="GET">
            <div>
                <label for="hb-in">Arrivée</label>
                <input type="date" id="hb-in" name="check_in"
                       min="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}">
            </div>
            <div>
                <label for="hb-out">Départ</label>
                <input type="date" id="hb-out" name="check_out"
                       min="{{ now()->addDay()->format('Y-m-d') }}" value="{{ now()->addDay()->format('Y-m-d') }}">
            </div>
            <div>
                <label for="hb-guests">Voyageurs</label>
                <select id="hb-guests" name="capacity">
                    <option value="">Peu importe</option>
                    @for($i = 1; $i <= 6; $i++)
                        <option value="{{ $i }}">{{ $i }} {{ $i > 1 ? 'personnes' : 'personne' }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="hb-budget">Budget par nuit</label>
                <select id="hb-budget" name="price_range">
                    <option value="">Tous les tarifs</option>
                    <option value="0-50000">moins de 50 000 FCFA</option>
                    <option value="50000-100000">50 000 — 100 000 FCFA</option>
                    <option value="100000-150000">100 000 — 150 000 FCFA</option>
                    <option value="200000+">plus de 200 000 FCFA</option>
                </select>
            </div>
            <div class="m-booking-submit">
                <button type="submit" class="m-btn">
                    Vérifier <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════ MANIFESTE ══════════════════ --}}
<section class="m-section">
    <div class="m-shell">
        <div class="m-split" style="align-items:flex-start">
            <div data-reveal="left">
                <span class="m-eyebrow">L'art de recevoir</span>
                <p class="m-pull">
                    Nous n'avons pas construit un hôtel.<br>
                    Nous avons ouvert <em>une maison</em>.
                </p>
            </div>

            <div data-reveal="right">
                <p class="m-lead" style="margin-bottom:22px">
                    Niché à Covè, Morada Lodge se vit comme une demeure familiale : chaque
                    bungalow ouvre sur le jardin, chaque repas raconte un terroir, et chaque
                    arrivée se fait sans formalité inutile.
                </p>
                <p style="color:var(--m-muted);margin-bottom:32px">
                    Nos équipes vivent sur place et connaissent la région par cœur. Elles
                    organisent vos excursions, vos dîners et vos moments de calme avec la
                    même attention — qu'il s'agisse d'une nuit ou d'un mois.
                </p>

                <a href="{{ route('frontend.services') }}" class="m-link">
                    Nos expériences <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        {{-- Chiffres : toujours présents, jamais dépendants d'une photo --}}
        <div class="row g-0" style="margin-top:clamp(48px,6vw,86px);border-top:1px solid var(--m-line)">
            @php
                $figures = [
                    ['24 h/24', 'Réception et service'],
                    ['100 %', 'Cuisine faite maison'],
                    ['4,8/5', 'Note des voyageurs'],
                    ['2 min', 'Pour réserver en ligne'],
                ];
            @endphp
            @foreach($figures as $i => $figure)
                <div class="col-lg-3 col-6" data-reveal data-reveal-delay="{{ $i * 90 }}"
                     style="border-bottom:1px solid var(--m-line);{{ $i % 4 !== 3 ? 'border-right:1px solid var(--m-line);' : '' }}">
                    <div class="m-figure">
                        <div class="m-figure-num">{{ $figure[0] }}</div>
                        <div class="m-figure-label">{{ $figure[1] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════ BANDEAU ══════════════════ --}}
<div class="m-marquee" aria-hidden="true">
    <div class="m-marquee-track">
        <span>Bungalows au jardin</span>
        <span>Table béninoise</span>
        <span>Piscine &amp; détente</span>
        <span>Suites panoramiques</span>
        <span>Nature préservée</span>
        <span>Accueil 24 h/24</span>
    </div>
</div>

{{-- ══════════════════ CHAMBRES ══════════════════ --}}
<section class="m-section m-cream m-inset" id="chambres">
    <div class="m-shell">
        <div style="text-align:center;margin-bottom:clamp(40px,5vw,72px)" data-reveal>
            <span class="m-eyebrow is-center">Nos hébergements</span>
            <h2 class="m-title">Chambres &amp; <em>Suites d'exception</em></h2>
            <p class="m-lead" style="margin-inline:auto">
                Tarifs et disponibilités mis à jour en temps réel depuis notre réception.
            </p>
        </div>

        <div class="row g-4">
            @php $prices = $featuredTypes->pluck('display_price'); @endphp

            @forelse($featuredTypes as $type)
                @php
                    if ($featuredTypes->count() > 1 && $type->display_price == $prices->max()) {
                        $badge = 'Prestige';
                    } elseif ($featuredTypes->count() > 1 && $type->display_price == $prices->min()) {
                        $badge = 'Essentiel';
                    } else {
                        $badge = 'Signature';
                    }
                @endphp

                <div class="col-lg-4 col-md-6">
                    @include('frontend.partials.room-type-card', [
                        'type' => $type,
                        'badge' => $badge,
                        'delay' => $loop->index * 120,
                    ])
                </div>
            @empty
                <div class="col-12">
                    <div style="text-align:center;padding:60px 20px;color:var(--m-muted)">
                        <i class="fas fa-bed fa-2x" style="color:var(--m-sand);margin-bottom:16px"></i>
                        <p>Nos chambres seront bientôt disponibles à la réservation en ligne.</p>
                        <a href="{{ route('frontend.contact') }}" class="m-link">Nous contacter <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            @endforelse
        </div>

        @if($featuredTypes->isNotEmpty())
            <div style="text-align:center;margin-top:clamp(36px,4vw,60px)" data-reveal>
                <a href="{{ route('frontend.rooms') }}" class="m-btn m-btn-ghost">
                    Voir toutes les chambres <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        @endif
    </div>
</section>

{{-- ══════════════════ EXPÉRIENCES (sans photo) ══════════════════ --}}
<section class="m-section" id="experiences">
    <div class="m-shell">
        <div class="m-split" style="align-items:flex-end;margin-bottom:clamp(36px,4vw,60px)">
            <div data-reveal="left">
                <span class="m-eyebrow">À vivre sur place</span>
                <h2 class="m-title">Le séjour ne s'arrête pas <em>à la chambre</em></h2>
            </div>
            <div data-reveal="right" style="justify-self:end">
                <a href="{{ route('frontend.services') }}" class="m-link">
                    Toutes les expériences <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>

        @php
            $experiences = [
                ['icon' => 'fa-utensils',   'title' => 'Table du terroir',    'text' => "Amiwo, poisson braisé, grillades au feu de bois : une cuisine généreuse servie en terrasse.", 'url' => route('frontend.restaurant')],
                ['icon' => 'fa-water',      'title' => 'Piscine & détente',   'text' => "Un bassin bordé de transats, à l'ombre des manguiers, pour les après-midis sans programme.", 'url' => route('frontend.services')],
                ['icon' => 'fa-mountain-sun','title' => 'Nature & excursions','text' => "Balades guidées, villages et artisans de la vallée du Zou, au rythme que vous choisissez.", 'url' => route('frontend.services')],
                ['icon' => 'fa-champagne-glasses','title' => 'Événements privés','text' => "Mariages, séminaires et retrouvailles dans un cadre entièrement privatisable.", 'url' => route('frontend.contact')],
            ];
        @endphp

        <div class="row g-4">
            @foreach($experiences as $i => $exp)
                <div class="col-lg-3 col-md-6">
                    <a href="{{ $exp['url'] }}" class="m-card" data-reveal data-reveal-delay="{{ $i * 110 }}">
                        <span class="m-card-num">0{{ $i + 1 }}</span>
                        <span class="m-card-icon"><i class="fas {{ $exp['icon'] }}"></i></span>
                        <h3>{{ $exp['title'] }}</h3>
                        <p>{{ $exp['text'] }}</p>
                        <span class="m-card-more">Découvrir <i class="fas fa-arrow-right"></i></span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════ RESTAURANT ══════════════════ --}}
<section class="m-section m-dark m-grain m-inset" id="table" style="overflow:hidden">
    <div class="m-shell">
        @if($restaurantPhoto)
            {{-- Variante illustrée --}}
            <div class="m-split">
                <div data-reveal="left">
                    @include('frontend.partials.photo', [
                        'src' => $restaurantPhoto,
                        'alt' => 'Le restaurant de Morada Lodge',
                        'ratio' => 'm-ratio-43',
                    ])
                </div>
                <div data-reveal="right">
                    @include('frontend.partials.home-restaurant-text')
                </div>
            </div>
        @else
            {{-- Variante typographique : la carte parle à la place de la photo --}}
            <div class="m-split" style="align-items:flex-start">
                <div data-reveal="left">
                    @include('frontend.partials.home-restaurant-text')
                </div>

                <div data-reveal="right">
                    <div class="m-eyebrow" style="margin-bottom:24px">Quelques signatures</div>
                    <dl class="m-specs" style="grid-template-columns:1fr">
                        @php
                            $signatures = \App\Models\Menu::available()
                                ->where('price', '>', 0)
                                ->orderByDesc('price')
                                ->take(5)
                                ->get();
                        @endphp

                        @forelse($signatures as $dish)
                            <div class="m-spec">
                                <dt>{{ $dish->name }}</dt>
                                <dd>{{ $dish->formatted_price }}</dd>
                            </div>
                        @empty
                            <div class="m-spec"><dt>Amiwo au poulet</dt><dd>5 000 FCFA</dd></div>
                            <div class="m-spec"><dt>Poisson braisé</dt><dd>4 500 FCFA</dd></div>
                            <div class="m-spec"><dt>Brochette de bœuf</dt><dd>4 000 FCFA</dd></div>
                        @endforelse
                    </dl>
                </div>
            </div>
        @endif
    </div>
</section>

{{-- ══════════════════ GALERIE (seulement si des photos existent) ══════════════════ --}}
@if($gallery->isNotEmpty())
    <section class="m-section">
        <div class="m-shell">
            <div style="text-align:center;margin-bottom:clamp(36px,4vw,60px)" data-reveal>
                <span class="m-eyebrow is-center">En images</span>
                <h2 class="m-title">Un avant-goût <em>de Morada</em></h2>
            </div>

            <div class="m-mosaic">
                @foreach($gallery as $i => $shot)
                    <div data-reveal="zoom" data-reveal-delay="{{ $i * 80 }}">
                        @include('frontend.partials.photo', [
                            'src' => $shot,
                            'alt' => 'Morada Lodge',
                            'ratio' => $i === 0 ? 'm-ratio-1' : 'm-ratio-43',
                        ])
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- ══════════════════ SÉJOURNER : LES ÉTAPES ══════════════════ --}}
<section class="m-section m-cream" id="etapes">
    <div class="m-shell">
        <div style="text-align:center;margin-bottom:clamp(40px,5vw,70px)" data-reveal>
            <span class="m-eyebrow is-center">Comment ça se passe</span>
            <h2 class="m-title">Réserver chez nous, <em>en trois gestes</em></h2>
        </div>

        <div class="m-steps">
            @php
                $steps = [
                    ['Étape 01', 'Vous choisissez', "Indiquez vos dates et le nombre de voyageurs : seules les chambres réellement libres s'affichent."],
                    ['Étape 02', 'Nous confirmons', "Un membre de l'équipe vous rappelle dans la journée pour valider et préparer votre arrivée."],
                    ['Étape 03', 'Vous arrivez', "Accueil à toute heure, clés remises sur place, et le reste du séjour s'organise avec vous."],
                ];
            @endphp

            @foreach($steps as $i => $step)
                <div class="m-step" data-reveal data-reveal-delay="{{ $i * 130 }}">
                    <div class="m-step-num">{{ $step[0] }}</div>
                    <h3>{{ $step[1] }}</h3>
                    <p>{{ $step[2] }}</p>
                </div>
            @endforeach
        </div>

        <div style="text-align:center;margin-top:clamp(40px,5vw,64px)" data-reveal>
            <a href="{{ route('frontend.reservation') }}" class="m-btn">
                Commencer ma réservation <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

{{-- ══════════════════ TÉMOIGNAGES ══════════════════ --}}
<section class="m-section m-dark m-grain m-inset" id="avis" data-quotes>
    <div class="m-shell">
        <div class="m-quote">
            <div class="m-quote-mark">&ldquo;</div>

            <div data-quote>
                <p>
                    Nous cherchions un endroit calme pour trois nuits, nous y sommes restés
                    une semaine. Le bungalow ouvre directement sur le jardin et l'équipe
                    anticipe absolument tout.
                </p>
                <div class="m-quote-author">Aline K. &middot; Cotonou</div>
            </div>

            <div data-quote style="display:none">
                <p>
                    La meilleure table que nous ayons trouvée dans la région. Le poisson
                    braisé vaut à lui seul le détour, et le service en terrasse au coucher
                    du soleil est inoubliable.
                </p>
                <div class="m-quote-author">Marc D. &middot; Paris</div>
            </div>

            <div data-quote style="display:none">
                <p>
                    Séminaire d'entreprise organisé de A à Z par le lodge. Rien à redire :
                    hébergement, repas, salle de travail. Nous reviendrons l'an prochain.
                </p>
                <div class="m-quote-author">Fatou S. &middot; Porto-Novo</div>
            </div>

            <div class="m-dots">
                <button type="button" class="is-active" aria-label="Témoignage 1"></button>
                <button type="button" aria-label="Témoignage 2"></button>
                <button type="button" aria-label="Témoignage 3"></button>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════ PRESTATIONS & ACCÈS ══════════════════ --}}
<section class="m-section">
    <div class="m-shell m-split" style="align-items:flex-start">
        <div data-reveal="left">
            <span class="m-eyebrow">Bon à savoir</span>
            <h2 class="m-title" style="font-size:var(--m-h3);margin-bottom:26px">Les détails pratiques</h2>

            <dl class="m-specs" style="grid-template-columns:1fr">
                <div class="m-spec"><dt>Arrivée</dt><dd>à partir de 14 h</dd></div>
                <div class="m-spec"><dt>Départ</dt><dd>jusqu'à 12 h</dd></div>
                <div class="m-spec"><dt>Petit-déjeuner</dt><dd>jusqu'à 11 h</dd></div>
                <div class="m-spec"><dt>Wi-Fi</dt><dd>gratuit, tout le site</dd></div>
                <div class="m-spec"><dt>Parking</dt><dd>gratuit et gardé</dd></div>
                <div class="m-spec"><dt>Animaux</dt><dd>sur demande</dd></div>
            </dl>
        </div>

        <div data-reveal="right">
            <span class="m-eyebrow">Nous rejoindre</span>
            <h2 class="m-title" style="font-size:var(--m-h3);margin-bottom:22px">Covè, vallée du Zou</h2>

            @if($closingPhoto)
                @include('frontend.partials.photo', [
                    'src' => $closingPhoto,
                    'alt' => 'Morada Lodge, Covè',
                    'ratio' => 'm-ratio-169',
                ])
            @endif

            <div style="display:grid;gap:14px;margin-top:24px;font-size:.95rem;color:var(--m-muted)">
                <div><i class="fas fa-location-dot me-2" style="color:var(--m-gold)"></i> Covè, République du Bénin</div>
                <div>
                    <i class="fas fa-phone me-2" style="color:var(--m-gold)"></i>
                    <a href="tel:+2290167836481" style="color:inherit;text-decoration:none">+229 01 67 83 64 81</a>
                </div>
                <div>
                    <i class="fas fa-envelope me-2" style="color:var(--m-gold)"></i>
                    <a href="mailto:admin@moradalodge.com" style="color:inherit;text-decoration:none">admin@moradalodge.com</a>
                </div>
            </div>

            <div style="margin-top:26px">
                <a href="{{ route('frontend.contact') }}" class="m-link">
                    Itinéraire &amp; contact <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════ APPEL FINAL ══════════════════ --}}
<section class="m-band m-grain m-inset" style="margin-bottom:clamp(40px,6vw,90px)">
    <div class="m-shell" data-reveal>
        <span class="m-eyebrow is-center" style="color:var(--m-gold-soft)">Il ne manque que vous</span>
        <h2 class="m-title" style="color:#fff">Votre chambre vous <em>attend à Covè</em></h2>
        <p class="m-lead" style="margin:0 auto 34px;color:rgba(255,255,255,.78)">
            Réservez en ligne en moins de deux minutes, ou appelez-nous : nous répondons
            à toute heure et organisons votre arrivée.
        </p>

        <div style="display:flex;flex-wrap:wrap;gap:14px;justify-content:center">
            <a href="{{ route('frontend.reservation') }}" class="m-btn m-btn-gold">
                Réserver maintenant <i class="fas fa-arrow-right"></i>
            </a>
            <a href="tel:+2290167836481" class="m-btn m-btn-light">
                <i class="fas fa-phone"></i> +229 01 67 83 64 81
            </a>
        </div>
    </div>
</section>

@endsection
