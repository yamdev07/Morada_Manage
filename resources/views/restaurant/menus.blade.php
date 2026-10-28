@extends('template.master')
@section('title', 'Restaurant — La carte')
@section('content')

@include('restaurant.partials.hub-styles')

@php
    $canManage = in_array(auth()->user()->role, ['Super', 'Admin', 'Receptionist']);
@endphp

<div class="rs-page">

    <div class="rs-header rs-animate">
        <div>
            <h1 class="rs-header-title"><i class="fas fa-book-open"></i> La carte</h1>
            <p class="rs-header-sub">
                {{ $menus->total() }} plat(s) enregistré(s) — ce sont ces plats et ces prix qui s'affichent sur le site
            </p>
        </div>
        <div class="rs-header-actions">
            <a href="{{ route('restaurant.index') }}" class="rs-btn rs-btn-ghost">
                <i class="fas fa-arrow-left"></i> Hub
            </a>
            @if($canManage)
                <a href="{{ route('restaurant.categories') }}" class="rs-btn rs-btn-ghost">
                    <i class="fas fa-tags"></i> Catégories
                </a>
                <a href="{{ route('restaurant.menus.create') }}" class="rs-btn rs-btn-primary">
                    <i class="fas fa-plus"></i> Ajouter un plat
                </a>
            @endif
        </div>
    </div>

    {{-- ════════ FILTRES ════════ --}}
    <div class="rs-panel rs-animate">
        <div class="rs-panel-body">
            <form method="GET" action="{{ route('restaurant.menus') }}" class="rs-filters">
                <div class="rs-field" style="flex: 2 1 240px;">
                    <label for="search">Rechercher</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Nom ou description du plat…">
                </div>
                <div class="rs-field">
                    <label for="category">Catégorie</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Toutes ({{ $categories->sum('menus_count') }})</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ (int) request('category') === $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->menus_count }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="rs-field">
                    <label for="availability">Disponibilité</label>
                    <select class="form-select" id="availability" name="availability">
                        <option value="">Tous les plats</option>
                        <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>
                            Disponibles
                        </option>
                        <option value="unavailable" {{ request('availability') === 'unavailable' ? 'selected' : '' }}>
                            Épuisés
                        </option>
                    </select>
                </div>
                <div class="rs-field" style="flex: 0 0 auto; min-width: auto;">
                    <label>&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="rs-btn rs-btn-primary">
                            <i class="fas fa-filter"></i> Filtrer
                        </button>
                        @if(request()->hasAny(['search', 'category', 'availability']))
                            <a href="{{ route('restaurant.menus') }}" class="rs-btn rs-btn-ghost">
                                <i class="fas fa-xmark"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════ GRILLE DES PLATS ════════ --}}
    @if($menus->isEmpty())
        <div class="rs-panel rs-animate">
            <div class="rs-empty">
                <i class="fas fa-utensils"></i>
                <h4>Aucun plat ne correspond</h4>
                <p>
                    @if(request()->hasAny(['search', 'category', 'availability']))
                        Aucun plat ne correspond à ces filtres.
                    @else
                        La carte est vide : les plats ajoutés ici s'afficheront sur le site public.
                    @endif
                </p>
                @if($canManage)
                    <a href="{{ route('restaurant.menus.create') }}" class="rs-btn rs-btn-primary">
                        <i class="fas fa-plus"></i> Ajouter le premier plat
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="rs-menu-grid rs-animate">
            @foreach($menus as $menu)
                <div class="rs-menu-card {{ $menu->is_available ? '' : 'is-out' }}" data-menu="{{ $menu->id }}">
                    <div class="rs-menu-thumb">
                        @if($menu->image_url)
                            <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div class="rs-menu-placeholder" style="display:none;"><i class="fas fa-utensils"></i></div>
                        @else
                            <div class="rs-menu-placeholder"><i class="fas fa-utensils"></i></div>
                        @endif
                        <span class="rs-menu-cat rs-badge rs-badge--secondary">{{ $menu->category_label }}</span>
                        <span class="rs-menu-price">{{ $menu->price_label }}</span>
                    </div>
                    <div class="rs-menu-body">
                        <h3 class="rs-menu-name">{{ $menu->name }}</h3>
                        <p class="rs-menu-desc">
                            {{ $menu->description ? Str::limit($menu->description, 95) : 'Aucune description.' }}
                        </p>

                        <div class="mb-2">
                            <span class="rs-badge rs-badge--{{ $menu->is_available ? 'success' : 'danger' }} js-availability-badge">
                                <i class="fas fa-{{ $menu->is_available ? 'circle-check' : 'circle-xmark' }}"></i>
                                {{ $menu->is_available ? 'Disponible' : 'Épuisé' }}
                            </span>
                        </div>

                        @if($canManage)
                            <div class="rs-menu-actions">
                                <button class="rs-btn rs-btn-ghost rs-btn-sm js-toggle" data-id="{{ $menu->id }}">
                                    <i class="fas fa-rotate"></i>
                                    {{ $menu->is_available ? 'Marquer épuisé' : 'Remettre' }}
                                </button>
                                <a href="{{ route('restaurant.menus.edit', $menu->id) }}"
                                   class="rs-btn rs-btn-ghost rs-btn-sm">
                                    <i class="fas fa-pen"></i> Modifier
                                </a>
                                <button class="rs-btn rs-btn-ghost rs-btn-sm js-delete"
                                        data-id="{{ $menu->id }}" data-name="{{ $menu->name }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($menus->hasPages())
            <div class="rs-pagination rs-animate">
                {{ $menus->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

@section('footer')
<script>
$(function () {
    const csrf = '{{ csrf_token() }}';

    // Disponible / épuisé
    $('.js-toggle').on('click', function () {
        const $btn = $(this).prop('disabled', true);

        $.ajax({
            url: '{{ url('restaurant/menus') }}/' + $btn.data('id') + '/availability',
            type: 'PATCH',
            data: { _token: csrf },
            headers: { 'Accept': 'application/json' },
            success: function (res) {
                if (window.Swal) {
                    Swal.fire({ icon: 'success', title: res.message, timer: 1400, showConfirmButton: false });
                }
                setTimeout(() => location.reload(), 600);
            },
            error: function () {
                $btn.prop('disabled', false);
                if (window.Swal) {
                    Swal.fire('Erreur', "Le changement n'a pas pu être enregistré.", 'error');
                }
            }
        });
    });

    // Retirer un plat de la carte (suppression douce : l'historique reste intact)
    $('.js-delete').on('click', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');

        Swal.fire({
            title: 'Retirer « ' + name + ' » ?',
            text: "Le plat disparaît de la carte. Les commandes passées gardent leur détail.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, retirer',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {
            if (! result.isConfirmed) return;

            $.ajax({
                url: '{{ url('restaurant/menus') }}/' + id,
                type: 'DELETE',
                data: { _token: csrf },
                headers: { 'Accept': 'application/json' },
                success: function () {
                    Swal.fire({ icon: 'success', title: 'Plat retiré.', timer: 1400, showConfirmButton: false })
                        .then(() => location.reload());
                },
                error: function () {
                    Swal.fire('Erreur', "La suppression a échoué.", 'error');
                }
            });
        });
    });
});
</script>
@endsection
