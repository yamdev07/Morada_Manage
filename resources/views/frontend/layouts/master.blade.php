<!DOCTYPE html>
<html lang="fr" class="no-js">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Morada Lodge — refuge de luxe au cœur de la nature, à Covè au Bénin. Bungalows, suites, restaurant et piscine.')">
    <meta name="theme-color" content="#2a1a10">
    <title>@yield('title', 'Morada Lodge — Refuge de luxe au Bénin')</title>

    {{-- Partage sur les réseaux --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Morada Lodge">
    <meta property="og:title" content="@yield('title', 'Morada Lodge — Refuge de luxe au Bénin')">
    <meta property="og:description" content="@yield('meta_description', 'Un refuge de luxe au cœur de la nature béninoise, à Covè.')">
    <meta property="og:image" content="{{ asset('img/accueil.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    {{-- Polices --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">

    {{-- Bootstrap 5 (conservé : les pages intérieures s'appuient dessus) --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Styles maison : le système de design passe en dernier --}}
    @php
        // Empreinte de version : le navigateur recharge la feuille des qu'elle change
        $cssV = @filemtime(public_path('css/morada.css')) ?: 1;
        $cssLegacyV = @filemtime(public_path('css/morada-lodge.css')) ?: 1;
        $jsV = @filemtime(public_path('js/morada.js')) ?: 1;
    @endphp
    <link rel="stylesheet" href="{{ asset('css/morada-lodge.css') }}?v={{ $cssLegacyV }}">
    <link rel="stylesheet" href="{{ asset('css/morada.css') }}?v={{ $cssV }}">

    <style>
        /* Variables héritées : conservées pour les pages non encore refondues */
        :root {
            --primary-brown: #8b4513;
            --secondary-brown: #a0522d;
            --accent-gold: #c99a3f;
            --dark-brown: #46291a;
            --light-brown: #f5e6d3;
            --warm-beige: #faf5ec;

            --primary-color: var(--primary-brown);
            --secondary-color: var(--secondary-brown);
            --light-color: var(--warm-beige);
            --dark-color: var(--dark-brown);
            --accent-color: var(--light-brown);
        }

        .btn-primary-custom { background-color: var(--primary-color); border-color: var(--primary-color); color: #fff; }
        .btn-primary-custom:hover { background-color: var(--dark-brown); border-color: var(--dark-brown); color: #fff; }
        .btn-outline-primary-custom { color: var(--primary-color); border-color: var(--primary-color); }
        .btn-outline-primary-custom:hover { background-color: var(--primary-color); color: #fff; }
        .text-primary-custom { color: var(--primary-color) !important; }
        .bg-primary-custom { background-color: var(--primary-color) !important; }
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-brown);
            box-shadow: 0 0 0 .2rem rgba(160, 82, 45, .18);
        }
        /* Les pages intérieures gardent leur décalage sous l'en-tête fixe */
        .m-offset { padding-top: 104px; }
    </style>

    @stack('styles')
</head>
<body class="m-site">

    {{-- ══════════ ÉCRAN DE CHARGEMENT ══════════ --}}
    <div class="m-loader">
        <div class="m-loader-mark">
            <span>Morada Lodge</span>
            <small>Covè &middot; Bénin</small>
            <div class="m-loader-bar"></div>
        </div>
    </div>

    {{-- ══════════ EN-TÊTE ══════════ --}}
    <header class="m-header is-transparent">
        <div class="m-shell-wide m-header-inner">
            <a class="m-brand" href="{{ route('frontend.home') }}">
                <img src="{{ asset('img/logo/logo_ancien.jpg') }}" alt="Morada Lodge">
                <span class="m-brand-text">
                    <span class="m-brand-name">Morada Lodge</span>
                    <span class="m-brand-tag">Covè &middot; Bénin</span>
                </span>
            </a>

            <nav class="m-nav" aria-label="Navigation principale">
                <a href="{{ route('frontend.home') }}">Accueil</a>
                <a href="{{ route('frontend.rooms') }}">Chambres &amp; Suites</a>
                <a href="{{ route('frontend.restaurant') }}">Restaurant</a>
                <a href="{{ route('frontend.services') }}">Expériences</a>
                <a href="{{ route('frontend.contact') }}">Contact</a>
            </nav>

            <div class="m-header-cta">
                @auth
                    <a href="{{ route('dashboard.index') }}" class="m-btn m-btn-ghost">
                        <i class="fas fa-gauge-high"></i> Tableau de bord
                    </a>
                @else
                    <a href="{{ route('login') }}" class="m-btn m-btn-ghost">Connexion</a>
                @endauth

                <a href="{{ route('frontend.reservation') }}" class="m-btn">
                    Réserver <i class="fas fa-arrow-right"></i>
                </a>

                <button class="m-burger" type="button" aria-label="Ouvrir le menu" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    {{-- ══════════ MENU MOBILE ══════════ --}}
    <div class="m-drawer" role="dialog" aria-modal="true" aria-label="Menu">
        <button class="m-drawer-close" type="button" aria-label="Fermer le menu">
            <i class="fas fa-xmark"></i>
        </button>

        <a href="{{ route('frontend.home') }}">Accueil</a>
        <a href="{{ route('frontend.rooms') }}">Chambres &amp; Suites</a>
        <a href="{{ route('frontend.restaurant') }}">Restaurant</a>
        <a href="{{ route('frontend.services') }}">Expériences</a>
        <a href="{{ route('frontend.contact') }}">Contact</a>
        <a href="{{ route('frontend.reservation') }}">Réserver</a>

        <div class="m-drawer-foot">
            <div>+229 01 67 83 64 81</div>
            <div>Covè &middot; République du Bénin</div>
        </div>
    </div>

    {{-- ══════════ CONTENU ══════════ --}}
    <main>
        @yield('content')
    </main>

    {{-- ══════════ PIED DE PAGE ══════════ --}}
    <footer class="m-footer">
        <div class="m-shell">
            <div class="m-footer-grid">
                <div>
                    <h4 style="font-size:1.7rem;margin-bottom:14px;">Morada Lodge</h4>
                    <p style="max-width:40ch;font-size:.94rem;">
                        Un refuge où l'hospitalité béninoise rencontre le raffinement :
                        bungalows au jardin, suites panoramiques, table généreuse et
                        silence de la nature.
                    </p>
                    <div class="m-social">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div>
                    <div class="m-footer-title">Découvrir</div>
                    <ul>
                        <li><a href="{{ route('frontend.rooms') }}">Chambres &amp; Suites</a></li>
                        <li><a href="{{ route('frontend.restaurant') }}">Restaurant</a></li>
                        <li><a href="{{ route('frontend.services') }}">Expériences</a></li>
                        <li><a href="{{ route('frontend.contact') }}">Nous écrire</a></li>
                    </ul>
                </div>

                <div>
                    <div class="m-footer-title">Séjour</div>
                    <ul>
                        <li><a href="{{ route('frontend.reservation') }}">Réserver une chambre</a></li>
                        <li><a href="{{ route('frontend.restaurant') }}#reserver-table">Réserver une table</a></li>
                        <li><a href="{{ route('frontend.rooms') }}">Disponibilités</a></li>
                        <li><a href="{{ route('login') }}">Espace équipe</a></li>
                    </ul>
                </div>

                <div>
                    <div class="m-footer-title">Nous trouver</div>
                    <ul>
                        <li><i class="fas fa-location-dot me-2" style="color:var(--m-gold)"></i> Covè, République du Bénin</li>
                        <li><a href="tel:+2290167836481"><i class="fas fa-phone me-2" style="color:var(--m-gold)"></i> +229 01 67 83 64 81</a></li>
                        <li><a href="mailto:admin@moradalodge.com"><i class="fas fa-envelope me-2" style="color:var(--m-gold)"></i> admin@moradalodge.com</a></li>
                        <li><i class="fas fa-clock me-2" style="color:var(--m-gold)"></i> Réception ouverte 24h/24</li>
                    </ul>
                </div>
            </div>

            <div class="m-footer-bottom">
                <span>&copy; {{ date('Y') }} Morada Lodge — Covè, Bénin. Tous droits réservés.</span>
                <span>Fait avec soin au Bénin</span>
            </div>
        </div>
    </footer>

    <button class="m-top" type="button" aria-label="Revenir en haut">
        <i class="fas fa-arrow-up"></i>
    </button>

    <a class="m-whatsapp" href="https://wa.me/2290167836481" target="_blank" rel="noopener" aria-label="Nous écrire sur WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/morada.js') }}?v={{ $jsV }}"></script>

    @stack('scripts')
</body>
</html>
