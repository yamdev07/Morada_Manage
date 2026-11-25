@extends('template.master')
@section('title', $menu ? 'Restaurant — Modifier un plat' : 'Restaurant — Ajouter un plat')
@section('content')

@include('restaurant.partials.hub-styles')

@php
    $isEdit = (bool) $menu;
    $action = $isEdit ? route('restaurant.menus.update', $menu->id) : route('restaurant.menus.store');
@endphp

<div class="rs-page">

    <div class="rs-header rs-animate">
        <div>
            <h1 class="rs-header-title">
                <i class="fas fa-{{ $isEdit ? 'pen' : 'plus' }}"></i>
                {{ $isEdit ? 'Modifier un plat' : 'Ajouter un plat' }}
            </h1>
            <p class="rs-header-sub">
                {{ $isEdit ? 'Les changements sont visibles immédiatement sur le site public.' : 'Le plat sera ajouté à la carte du restaurant et au site public.' }}
            </p>
        </div>
        <div class="rs-header-actions">
            <a href="{{ route('restaurant.menus') }}" class="rs-btn rs-btn-ghost">
                <i class="fas fa-arrow-left"></i> Retour à la carte
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rs-animate">
            <strong>Le formulaire contient des erreurs :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rs-grid rs-grid-2 rs-animate">
        <div>
            <div class="rs-panel">
                <div class="rs-panel-head">
                    <h2 class="rs-panel-title"><i class="fas fa-utensils"></i> Informations du plat</h2>
                </div>
                <div class="rs-panel-body">
                    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" id="menuForm">
                        @csrf
                        @if($isEdit)
                            @method('PUT')
                        @endif

                        <div class="rs-field mb-3" style="min-width: 100%;">
                            <label for="name">Nom du plat *</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   maxlength="255"
                                   value="{{ old('name', $menu->name ?? '') }}"
                                   placeholder="Ex. Amiwo au poulet">
                        </div>

                        <div class="d-flex gap-3 flex-wrap mb-3">
                            <div class="rs-field" style="flex: 1 1 200px;">
                                <label for="category_id">Catégorie *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Choisir…</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ (int) old('category_id', $menu->category_id ?? 0) === $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($categories->isEmpty())
                                    <small class="text-muted">
                                        <a href="{{ route('restaurant.categories') }}">Créez d'abord une catégorie</a>.
                                    </small>
                                @endif
                            </div>
                            <div class="rs-field" style="flex: 1 1 200px;">
                                <label for="price">Prix (FCFA) *</label>
                                <input type="number" class="form-control" id="price" name="price"
                                       min="0" step="50" required
                                       value="{{ old('price', $menu ? (int) $menu->price : '') }}"
                                       placeholder="Ex. 5000">
                                <small class="text-muted">0 = affiché « Sur commande » sur le site.</small>
                            </div>
                        </div>

                        <div class="rs-field mb-3" style="min-width: 100%;">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                      maxlength="1000"
                                      placeholder="Courte description affichée sur le site…">{{ old('description', $menu->description ?? '') }}</textarea>
                        </div>

                        <div class="rs-field mb-3" style="min-width: 100%;">
                            <label for="image">Photo du plat</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="text-muted">JPG ou PNG, 2 Mo maximum. Facultatif.</small>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" role="switch"
                                   id="is_available" name="is_available" value="1"
                                   {{ old('is_available', $menu->is_available ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_available">
                                Disponible à la commande
                            </label>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="rs-btn rs-btn-primary">
                                <i class="fas fa-floppy-disk"></i>
                                {{ $isEdit ? 'Enregistrer les modifications' : 'Ajouter à la carte' }}
                            </button>
                            <a href="{{ route('restaurant.menus') }}" class="rs-btn rs-btn-ghost">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="rs-panel">
                <div class="rs-panel-head">
                    <h2 class="rs-panel-title"><i class="fas fa-eye"></i> Aperçu</h2>
                </div>
                <div class="rs-panel-body">
                    <div class="rs-menu-card" style="max-width: 320px; margin: 0 auto;">
                        <div class="rs-menu-thumb">
                            <img id="previewImage"
                                 src="{{ $menu && $menu->image_url ? $menu->image_url : '' }}"
                                 alt="Aperçu"
                                 style="{{ $menu && $menu->image_url ? '' : 'display:none;' }}">
                            <div class="rs-menu-placeholder" id="previewPlaceholder"
                                 style="{{ $menu && $menu->image_url ? 'display:none;' : '' }}">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <span class="rs-menu-price" id="previewPrice">
                                {{ $menu ? $menu->price_label : 'Sur commande' }}
                            </span>
                        </div>
                        <div class="rs-menu-body">
                            <h3 class="rs-menu-name" id="previewName">{{ $menu->name ?? 'Nom du plat' }}</h3>
                            <p class="rs-menu-desc" id="previewDesc">
                                {{ $menu->description ?? 'La description apparaîtra ici.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rs-panel">
                <div class="rs-panel-head">
                    <h2 class="rs-panel-title"><i class="fas fa-circle-info"></i> La carte en bref</h2>
                </div>
                <div class="rs-panel-body">
                    <div class="rs-rank">
                        <div class="rs-rank-pos"><i class="fas fa-list"></i></div>
                        <div class="rs-rank-body">
                            <div class="rs-rank-name">{{ $totalMenus }} plat(s) à la carte</div>
                            <div class="rs-rank-meta">toutes catégories confondues</div>
                        </div>
                    </div>
                    @if($lastAdded)
                        <div class="rs-rank">
                            <div class="rs-rank-pos"><i class="fas fa-clock"></i></div>
                            <div class="rs-rank-body">
                                <div class="rs-rank-name">Dernier ajout : {{ $lastAdded->name }}</div>
                                <div class="rs-rank-meta">{{ $lastAdded->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
<script>
$(function () {
    // Aperçu en direct pendant la saisie
    $('#name').on('input', function () {
        $('#previewName').text($(this).val() || 'Nom du plat');
    });

    $('#description').on('input', function () {
        $('#previewDesc').text($(this).val() || 'La description apparaîtra ici.');
    });

    $('#price').on('input', function () {
        const value = parseInt($(this).val(), 10);
        $('#previewPrice').text(
            (isNaN(value) || value <= 0)
                ? 'Sur commande'
                : value.toLocaleString('fr-FR').replace(/ | /g, ' ') + ' FCFA'
        );
    });

    $('#image').on('change', function (event) {
        const file = event.target.files[0];
        if (! file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            $('#previewImage').attr('src', e.target.result).show();
            $('#previewPlaceholder').hide();
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection
