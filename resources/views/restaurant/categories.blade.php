@extends('template.master')
@section('title', 'Restaurant — Catégories de la carte')
@section('content')

@include('restaurant.partials.hub-styles')

<div class="rs-page">

    <div class="rs-header rs-animate">
        <div>
            <h1 class="rs-header-title"><i class="fas fa-tags"></i> Catégories de la carte</h1>
            <p class="rs-header-sub">
                Les sections du menu, dans l'ordre où elles apparaissent sur le site
            </p>
        </div>
        <div class="rs-header-actions">
            <a href="{{ route('restaurant.menus') }}" class="rs-btn rs-btn-ghost">
                <i class="fas fa-arrow-left"></i> La carte
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rs-animate">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger rs-animate">
            <ul class="mb-0">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="rs-grid rs-grid-2 rs-animate">
        <div>
            <div class="rs-panel">
                <div class="rs-panel-head">
                    <h2 class="rs-panel-title"><i class="fas fa-list"></i> {{ $categories->count() }} catégorie(s)</h2>
                </div>
                <div class="rs-panel-body is-flush">
                    @if($categories->isEmpty())
                        <div class="rs-empty">
                            <i class="fas fa-tags"></i>
                            <h4>Aucune catégorie</h4>
                            <p>Créez au moins une catégorie pour pouvoir classer les plats.</p>
                        </div>
                    @else
                        <table class="rs-table">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">Ordre</th>
                                    <th>Nom</th>
                                    <th>Plats</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td class="rs-num">{{ $category->sort_order }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('restaurant.categories.update', $category->id) }}"
                                                  class="d-flex gap-2 align-items-center">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name" value="{{ $category->name }}"
                                                       class="form-control form-control-sm" required maxlength="255">
                                                <input type="number" name="sort_order" value="{{ $category->sort_order }}"
                                                       class="form-control form-control-sm" min="0"
                                                       style="width: 80px;" title="Ordre d'affichage">
                                                <button type="submit" class="rs-btn rs-btn-ghost rs-btn-sm" title="Enregistrer">
                                                    <i class="fas fa-floppy-disk"></i>
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="{{ route('restaurant.menus') }}?category={{ $category->id }}"
                                               class="rs-badge rs-badge--secondary text-decoration-none">
                                                {{ $category->menus_count }} plat(s)
                                            </a>
                                        </td>
                                        <td class="text-end">
                                            <form method="POST" action="{{ route('restaurant.categories.destroy', $category->id) }}"
                                                  class="js-delete-category d-inline"
                                                  data-name="{{ $category->name }}" data-count="{{ $category->menus_count }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rs-btn rs-btn-ghost rs-btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="rs-panel">
                <div class="rs-panel-head">
                    <h2 class="rs-panel-title"><i class="fas fa-plus"></i> Nouvelle catégorie</h2>
                </div>
                <div class="rs-panel-body">
                    <form method="POST" action="{{ route('restaurant.categories.store') }}">
                        @csrf
                        <div class="rs-field mb-3" style="min-width: 100%;">
                            <label for="name">Nom *</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   maxlength="255" placeholder="Ex. Grillades &amp; Fruits de Mer">
                        </div>
                        <div class="rs-field mb-3" style="min-width: 100%;">
                            <label for="sort_order">Ordre d'affichage</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" min="0"
                                   placeholder="Laisser vide pour placer à la fin">
                        </div>
                        <button type="submit" class="rs-btn rs-btn-primary">
                            <i class="fas fa-plus"></i> Ajouter
                        </button>
                    </form>
                </div>
            </div>

            <div class="rs-panel">
                <div class="rs-panel-head">
                    <h2 class="rs-panel-title"><i class="fas fa-circle-info"></i> À savoir</h2>
                </div>
                <div class="rs-panel-body">
                    <p class="mb-2" style="font-size: .88rem;">
                        L'ordre défini ici est celui des sections du menu sur le site public.
                    </p>
                    <p class="mb-0" style="font-size: .88rem;">
                        Supprimer une catégorie ne supprime pas ses plats : ils passent simplement
                        en « sans catégorie » et n'apparaissent plus sur le site tant qu'ils ne sont
                        pas reclassés.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
<script>
$(function () {
    $('.js-delete-category').on('submit', function (e) {
        e.preventDefault();
        const $form = $(this);
        const count = parseInt($form.data('count'), 10) || 0;

        Swal.fire({
            title: 'Supprimer « ' + $form.data('name') + ' » ?',
            text: count > 0
                ? count + ' plat(s) passeront en « sans catégorie ».'
                : 'Cette catégorie est vide.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) $form.off('submit').trigger('submit');
        });
    });
});
</script>
@endsection
