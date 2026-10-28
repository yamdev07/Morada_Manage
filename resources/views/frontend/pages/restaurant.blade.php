@extends('frontend.layouts.master')

@section('title', 'Gastronomie - Morada Lodge')

@section('content')

    {{-- ══════════ EN-TÊTE DE PAGE ══════════ --}}
    <section class="m-pagehead">
        <div class="m-hero-media">
            @php
                $headPhoto = collect(['img/morada/head-restaurant.jpg', 'img/morada/restaurant.jpg', 'img/morada/exp-restaurant.jpg'])->first(fn ($p) => is_file(public_path($p)));
            @endphp
            @if($headPhoto)
                <img src="{{ asset($headPhoto) }}" alt="Gastronomie" fetchpriority="high">
            @endif
        </div>
        <div class="m-hero-scrim"></div>

        <div class="m-shell" style="position:relative;z-index:2;padding-top:90px">
            <span class="m-eyebrow is-center">La table</span>
            <h1>Gastronomie</h1>
            <p class="m-lead" style="margin-inline:auto;color:rgba(255,255,255,.82)">Saveurs du Bénin et échappées du monde, préparées chaque jour avec les produits du marché de Covè.</p>

            <div class="m-crumb" style="margin-top:22px">
                <a href="{{ route('frontend.home') }}">Accueil</a>
                <span style="margin:0 10px;color:var(--m-gold)">/</span>Restaurant
            </div>
        </div>
    </section>


    <!-- Présentation -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <h2 class="mb-4" style="font-family: Arial, sans-serif; font-weight: normal; color: black; font-size: 1.6rem;">Notre restaurant vous invite à un voyage culinaire exceptionnel sous une majestueuse paillote face à la piscine. Découvrez nos spécialités locales revisitées et notre cuisine internationale raffinée.</h2>
                    
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #8B4513; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-leaf fa-lg" style="color: white;"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-1" style="font-family: Arial, sans-serif; font-weight: bold; color: black;">Cuisine Locale Authentique</h5>
                                        <p class="mb-0" style="font-family: Arial, sans-serif; font-weight: normal; color: black;">Amiwo, Atassi, MAN au Télibo revisités avec passion</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #8B4513; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-glass-martini-alt fa-lg" style="color: white;"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-1" style="font-family: Arial, sans-serif; font-weight: bold; color: black;">Bar & Cocktails</h5>
                                        <p class="mb-0" style="font-family: Arial, sans-serif; font-weight: normal; color: black;">Cocktails signature et boissons rafraîchissantes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #8B4513; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-drumstick-bite fa-lg" style="color: white;"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-1" style="font-family: Arial, sans-serif; font-weight: bold; color: black;">Grillades & Fruits de Mer</h5>
                                        <p class="mb-0" style="font-family: Arial, sans-serif; font-weight: normal; color: black;">Brochettes, poissons braisés, gambas sautées</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div style="width: 45px; height: 45px; border-radius: 50%; background-color: #8B4513; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-clock fa-lg" style="color: white;"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-1" style="font-family: Arial, sans-serif; font-weight: bold; color: black;">Service Continu</h5>
                                        <p class="mb-0" style="font-family: Arial, sans-serif; font-weight: normal; color: black;">Ouvert de 7h à 23h tous les jours</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="#menu" class="btn" style="display: none;">
                                Voir le menu
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="{{ asset('img/room/Photo11.jpeg') }}" 
                         alt="Notre restaurant" 
                         class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Carte du Restaurant (alimentée depuis le back-office) -->
    <section class="py-5" id="menu" style="background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="card border-0 shadow-xl" style="border-radius: 20px; overflow: hidden; background: white;">

                        <div class="text-center p-4" style="background: linear-gradient(135deg, #8B4513 0%, #A0522D 100%); color: white;">
                            <h2 class="mb-0" style="font-family: Arial, sans-serif; font-weight: bold; font-size: 2rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                                <i class="fas fa-utensils me-3"></i>MENU
                            </h2>
                        </div>

                        <div class="card-body p-0">
                            {{-- Sections de la carte, dans l'ordre défini au back-office --}}
                            @foreach($categories as $category)
                                <div class="p-4" style="border-bottom: 1px solid #f0f0f0;">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="me-3">
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #8B4513; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas {{ $category->icon }}" style="color: white;"></i>
                                            </div>
                                        </div>
                                        <h3 class="mb-0" style="font-family: Arial, sans-serif; font-weight: bold; color: #8B4513; font-size: 1.4rem;">
                                            {{ $category->name }}
                                        </h3>
                                    </div>

                                    <div class="row">
                                        @foreach($category->menus as $menu)
                                            <div class="col-md-6 mb-3">
                                                <div class="d-flex justify-content-between align-items-start p-3" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #8B4513;">
                                                    <div class="pe-3">
                                                        <h5 class="mb-1" style="font-family: Arial, sans-serif; font-weight: bold; color: black; font-size: 1rem;">
                                                            {{ $menu->name }}
                                                        </h5>
                                                        @if($menu->description)
                                                            <p class="mb-0" style="font-family: Arial, sans-serif; color: #666; font-size: 0.9rem;">
                                                                {{ $menu->description }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <span class="badge" style="background-color: {{ $menu->price > 0 ? '#8B4513' : '#6c757d' }}; color: white; padding: 8px 12px; border-radius: 20px; font-weight: bold; white-space: nowrap;">
                                                        {{ $menu->price_label }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            {{-- Plats pas encore classés : ils restent visibles plutôt que de disparaître --}}
                            @if($uncategorized->isNotEmpty())
                                <div class="p-4" style="border-bottom: 1px solid #f0f0f0;">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="me-3">
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #8B4513; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-utensils" style="color: white;"></i>
                                            </div>
                                        </div>
                                        <h3 class="mb-0" style="font-family: Arial, sans-serif; font-weight: bold; color: #8B4513; font-size: 1.4rem;">
                                            Autres suggestions
                                        </h3>
                                    </div>

                                    <div class="row">
                                        @foreach($uncategorized as $menu)
                                            <div class="col-md-6 mb-3">
                                                <div class="d-flex justify-content-between align-items-start p-3" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #8B4513;">
                                                    <div class="pe-3">
                                                        <h5 class="mb-1" style="font-family: Arial, sans-serif; font-weight: bold; color: black; font-size: 1rem;">
                                                            {{ $menu->name }}
                                                        </h5>
                                                        @if($menu->description)
                                                            <p class="mb-0" style="font-family: Arial, sans-serif; color: #666; font-size: 0.9rem;">
                                                                {{ $menu->description }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <span class="badge" style="background-color: {{ $menu->price > 0 ? '#8B4513' : '#6c757d' }}; color: white; padding: 8px 12px; border-radius: 20px; font-weight: bold; white-space: nowrap;">
                                                        {{ $menu->price_label }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($categories->isEmpty() && $uncategorized->isEmpty())
                                <div class="p-5 text-center">
                                    <i class="fas fa-utensils fa-3x mb-3" style="color: #d2b48c;"></i>
                                    <h4 style="color: #8B4513; font-weight: bold;">Notre carte arrive bientôt</h4>
                                    <p class="text-muted mb-0">
                                        Contactez-nous pour connaître les plats du jour et nos suggestions du chef.
                                    </p>
                                </div>
                            @endif


                            <div class="p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="me-3">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #8B4513; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-music" style="color: white;"></i>
                                        </div>
                                    </div>
                                    <h3 class="mb-0" style="font-family: Arial, sans-serif; font-weight: bold; color: #8B4513; font-size: 1.4rem;">Ambiance festive</h3>
                                </div>
                                <div class="p-3" style="background-color: #fafafa; border-radius: 10px; border-left: 4px solid #8B4513;">
                                    <p class="mb-0" style="font-family: Arial, sans-serif; color: #666; font-size: 0.95rem;">
                                        Profitez d'un barbecue ou d'un chili entre amis autour de la piscine,
                                        dans une ambiance conviviale et musicale.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Réservation de table -->
    <section class="py-5" id="reserver-table">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="text-center mb-4">
                        <h2 style="font-family: Arial, sans-serif; font-weight: bold; color: #8B4513;">
                            Réserver une table
                        </h2>
                        <p class="text-muted">
                            Indiquez-nous votre créneau : nous vous rappelons pour confirmer.
                        </p>
                    </div>

                    <div class="card border-0 shadow" style="border-radius: 18px;">
                        <div class="card-body p-4">
                            <div id="tableReservationAlert"></div>

                            <form id="tableReservationForm">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="tr-name">Nom complet *</label>
                                        <input type="text" class="form-control" id="tr-name" name="name" required maxlength="255">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="tr-phone">Téléphone *</label>
                                        <input type="text" class="form-control" id="tr-phone" name="phone" required maxlength="30">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="tr-email">Email</label>
                                        <input type="email" class="form-control" id="tr-email" name="email" maxlength="255">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="tr-persons">Nombre de personnes *</label>
                                        <input type="number" class="form-control" id="tr-persons" name="persons"
                                               min="1" max="20" value="2" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="tr-date">Date *</label>
                                        <input type="date" class="form-control" id="tr-date" name="date"
                                               min="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="tr-time">Heure *</label>
                                        <input type="time" class="form-control" id="tr-time" name="time" value="19:30" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="tr-table">Type de table</label>
                                        <select class="form-select" id="tr-table" name="table_type">
                                            <option value="">Sans préférence</option>
                                            <option value="Intérieur">Intérieur</option>
                                            <option value="Terrasse">Terrasse</option>
                                            <option value="Vue jardin">Vue jardin</option>
                                            <option value="Privatisée">Privatisée</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label" for="tr-notes">Demandes particulières</label>
                                        <textarea class="form-control" id="tr-notes" name="notes" rows="3"
                                                  maxlength="1000" placeholder="Allergies, occasion spéciale…"></textarea>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-lg px-5" id="tr-submit"
                                                style="background-color: #8B4513; color: white; border-radius: 30px;">
                                            <i class="fas fa-calendar-check me-2"></i>Envoyer ma demande
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @endsection

    @push('scripts')
    <script>
    $(document).ready(function() {
        // Réservation de table : envoi sans quitter la page
        $('#tableReservationForm').on('submit', function (e) {
            e.preventDefault();

            const $form = $(this);
            const $button = $('#tr-submit').prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin me-2"></i>Envoi…');

            $.ajax({
                url: '{{ route('restaurant.reservation.store') }}',
                type: 'POST',
                data: $form.serialize(),
                headers: { 'Accept': 'application/json' },
                success: function (res) {
                    $('#tableReservationAlert').html(
                        '<div class="alert alert-success">' + res.message + '</div>'
                    );
                    $form[0].reset();
                },
                error: function (xhr) {
                    let message = "Une erreur est survenue. Veuillez réessayer.";
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    $('#tableReservationAlert').html('<div class="alert alert-danger">' + message + '</div>');
                },
                complete: function () {
                    $button.prop('disabled', false)
                        .html('<i class="fas fa-calendar-check me-2"></i>Envoyer ma demande');
                }
            });
        });

        // Filtrage des menus
        $('.category-filter').click(function() {
            $('.category-filter').removeClass('active');
            $(this).addClass('active');
            
            const category = $(this).data('category');
            
            if (category === 'all') {
                $('.menu-item').show();
            } else {
                $('.menu-item').hide();
                $(`.menu-item[data-category="${category}"]`).show();
            }
        });
    });
    </script>
    @endpush