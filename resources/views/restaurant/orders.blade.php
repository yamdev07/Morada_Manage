@extends('template.master')
@section('title', 'Restaurant — Commandes')
@section('content')

@include('restaurant.partials.hub-styles')

<div class="rs-page">

    <div class="rs-header rs-animate">
        <div>
            <h1 class="rs-header-title"><i class="fas fa-receipt"></i> Commandes</h1>
            <p class="rs-header-sub">Prise de commande, suivi en cuisine et encaissement</p>
        </div>
        <div class="rs-header-actions">
            <a href="{{ route('restaurant.index') }}" class="rs-btn rs-btn-ghost">
                <i class="fas fa-arrow-left"></i> Hub
            </a>
            <a href="{{ route('restaurant.sales') }}" class="rs-btn rs-btn-ghost">
                <i class="fas fa-chart-line"></i> Ventes
            </a>
            <button class="rs-btn rs-btn-primary" data-bs-toggle="modal" data-bs-target="#newOrderModal">
                <i class="fas fa-plus"></i> Nouvelle commande
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rs-animate">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rs-animate">{{ session('error') }}</div>
    @endif

    {{-- ════════ STATISTIQUES ════════ --}}
    <div class="rs-stats rs-animate">
        <div class="rs-stat rs-stat--open">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">En attente</div>
                    <div class="rs-stat-value">{{ $pendingOrders }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-hourglass-half"></i></div>
            </div>
            <div class="rs-stat-meta"><i class="fas fa-fire me-1"></i> {{ $preparingOrders }} en préparation</div>
        </div>

        <div class="rs-stat rs-stat--orders">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">Servies</div>
                    <div class="rs-stat-value">{{ $deliveredOrders }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-bell-concierge"></i></div>
            </div>
            <div class="rs-stat-meta"><i class="fas fa-hand-holding-dollar me-1"></i> en attente de paiement</div>
        </div>

        <div class="rs-stat rs-stat--revenue">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">Encaissé aujourd'hui</div>
                    <div class="rs-stat-value is-money">{{ Helper::formatCFA($todayRevenue) }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-cash-register"></i></div>
            </div>
            <div class="rs-stat-meta"><i class="fas fa-check-circle me-1"></i> commandes payées</div>
        </div>

        <div class="rs-stat rs-stat--tables">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">Ce mois-ci</div>
                    <div class="rs-stat-value">{{ $monthlyOrders }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-calendar"></i></div>
            </div>
            <div class="rs-stat-meta"><i class="fas fa-chart-line me-1"></i> commandes enregistrées</div>
        </div>
    </div>

    {{-- ════════ FILTRES ════════ --}}
    <div class="rs-panel rs-animate">
        <div class="rs-panel-body">
            <form method="GET" action="{{ route('restaurant.orders') }}" class="rs-filters">
                <div class="rs-field" style="flex: 2 1 220px;">
                    <label for="search">Rechercher</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="N° de commande, table ou client…">
                </div>
                <div class="rs-field">
                    <label for="status">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        @foreach(\App\Models\RestaurantOrder::STATUSES as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="rs-field">
                    <label for="type">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">Tous les types</option>
                        @foreach(\App\Models\RestaurantOrder::TYPES as $key => $label)
                            <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="rs-field">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
                </div>
                <div class="rs-field" style="flex: 0 0 auto; min-width: auto;">
                    <label>&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="rs-btn rs-btn-primary"><i class="fas fa-filter"></i> Filtrer</button>
                        @if(request()->hasAny(['search', 'status', 'type', 'date']))
                            <a href="{{ route('restaurant.orders') }}" class="rs-btn rs-btn-ghost">
                                <i class="fas fa-xmark"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════ LISTE DES COMMANDES ════════ --}}
    <div class="rs-panel rs-animate">
        <div class="rs-panel-head">
            <h2 class="rs-panel-title"><i class="fas fa-list"></i> {{ $orders->total() }} commande(s)</h2>
        </div>
        <div class="rs-panel-body is-flush">
            @if($orders->isEmpty())
                <div class="rs-empty">
                    <i class="fas fa-receipt"></i>
                    <h4>Aucune commande</h4>
                    <p>
                        @if(request()->hasAny(['search', 'status', 'type', 'date']))
                            Aucune commande ne correspond à ces filtres.
                        @else
                            Enregistrez la première commande du restaurant.
                        @endif
                    </p>
                    <button class="rs-btn rs-btn-primary" data-bs-toggle="modal" data-bs-target="#newOrderModal">
                        <i class="fas fa-plus"></i> Nouvelle commande
                    </button>
                </div>
            @else
                <div class="table-responsive">
                    <table class="rs-table">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Destination</th>
                                <th>Client</th>
                                <th>Articles</th>
                                <th class="text-end">Total</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <span class="rs-num">{{ $order->order_number }}</span>
                                        <div class="rs-ticket-meta">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td>
                                        {{ $order->destination }}
                                        <div class="rs-ticket-meta">{{ $order->type_label }}</div>
                                    </td>
                                    <td>
                                        <span class="js-client-name" data-id="{{ $order->id }}">{{ $order->client_name }}</span>
                                        @if(! $order->customer_id)
                                            <button class="btn btn-sm btn-link p-0 ms-1 js-rename"
                                                    data-id="{{ $order->id }}"
                                                    data-name="{{ $order->guest_name }}" title="Nommer le client">
                                                <i class="fas fa-pen fa-xs"></i>
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="rs-btn rs-btn-ghost rs-btn-sm js-view" data-id="{{ $order->id }}">
                                            <i class="fas fa-list-ul"></i> {{ $order->items->count() }} article(s)
                                        </button>
                                    </td>
                                    <td class="text-end rs-money">{{ $order->formatted_total }}</td>
                                    <td>
                                        <span class="rs-badge rs-badge--{{ $order->status_color }}">
                                            {{ $order->status_label }}
                                        </span>
                                        @if($order->paid_at)
                                            <div class="rs-ticket-meta">payée {{ $order->paid_at->format('H:i') }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end flex-wrap">
                                            @if($order->status === 'pending')
                                                <button class="rs-btn rs-btn-ghost rs-btn-sm js-status"
                                                        data-id="{{ $order->id }}" data-status="preparing" title="En préparation">
                                                    <i class="fas fa-fire"></i>
                                                </button>
                                            @endif
                                            @if($order->status === 'preparing')
                                                <button class="rs-btn rs-btn-ghost rs-btn-sm js-status"
                                                        data-id="{{ $order->id }}" data-status="delivered" title="Servie">
                                                    <i class="fas fa-bell-concierge"></i>
                                                </button>
                                            @endif
                                            @if($order->is_editable)
                                                <button class="rs-btn rs-btn-primary rs-btn-sm js-pay"
                                                        data-id="{{ $order->id }}"
                                                        data-total="{{ $order->formatted_total }}"
                                                        data-method="{{ $order->payment_method }}">
                                                    <i class="fas fa-cash-register"></i> Encaisser
                                                </button>
                                                <button class="rs-btn rs-btn-ghost rs-btn-sm js-cancel"
                                                        data-id="{{ $order->id }}" title="Annuler">
                                                    <i class="fas fa-xmark"></i>
                                                </button>
                                            @endif
                                            <a href="{{ route('restaurant.orders.show', $order->id) }}"
                                               class="rs-btn rs-btn-ghost rs-btn-sm" title="Détail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('restaurant.orders.invoice', $order->id) }}"
                                               target="_blank" class="rs-btn rs-btn-ghost rs-btn-sm" title="Facture">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="rs-pagination">{{ $orders->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- ════════ MODALE : DÉTAIL D'UNE COMMANDE ════════ --}}
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-receipt me-2"></i>Détail de la commande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body" id="orderDetailsBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-secondary"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ════════ MODALE : NOUVELLE COMMANDE ════════ --}}
<div class="modal fade" id="newOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Nouvelle commande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="POST" action="{{ route('restaurant.orders.store') }}" id="newOrderForm">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Type de commande *</label>
                            <select class="form-select" name="order_type" id="orderType" required>
                                @foreach(\App\Models\RestaurantOrder::TYPES as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 js-field-table">
                            <label class="form-label">Numéro de table</label>
                            <input type="text" class="form-control" name="table_number" id="tableNumber"
                                   maxlength="20" placeholder="Ex. 12">
                        </div>

                        <div class="col-md-4 js-field-room" style="display: none;">
                            <label class="form-label">Chambre occupée</label>
                            <select class="form-select" name="room_id" id="roomSelect">
                                <option value="">Sélectionner une chambre…</option>
                                @foreach($occupiedRooms as $room)
                                    <option value="{{ $room['room_id'] }}"
                                            data-customer="{{ $room['customer_id'] }}">
                                        Chambre {{ $room['room_number'] }} — {{ $room['customer_name'] }}
                                    </option>
                                @endforeach
                            </select>
                            @if($occupiedRooms->isEmpty())
                                <small class="text-muted">Aucune chambre occupée actuellement.</small>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Client</label>
                            <select class="form-select" name="customer_id" id="customerSelect">
                                <option value="">Client de passage</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nom du client de passage</label>
                            <input type="text" class="form-control" name="guest_name" maxlength="255"
                                   placeholder="Si le client n'a pas de fiche">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Mode de paiement prévu</label>
                            <select class="form-select" name="payment_method">
                                <option value="">À définir</option>
                                <option value="cash">Espèces</option>
                                <option value="card">Carte bancaire</option>
                                <option value="mobile">Mobile money</option>
                                <option value="room_charge">Sur la note de la chambre</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3">
                        {{-- Sélection des plats --}}
                        <div class="col-lg-6">
                            <label class="form-label">Ajouter un plat</label>
                            <div class="input-group mb-2">
                                <select class="form-select" id="menuSelect">
                                    <option value="">Choisir un plat…</option>
                                    @foreach($menus->groupBy(fn ($menu) => $menu->category->name ?? 'Sans catégorie') as $category => $group)
                                        <optgroup label="{{ $category }}">
                                            @foreach($group as $menu)
                                                <option value="{{ $menu->id }}"
                                                        data-name="{{ $menu->name }}"
                                                        data-price="{{ $menu->price }}">
                                                    {{ $menu->name }} — {{ $menu->formatted_price }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <input type="number" class="form-control" id="menuQty" value="1" min="1" max="50"
                                       style="max-width: 90px;">
                                <button type="button" class="rs-btn rs-btn-primary" id="addItem">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            @if($menus->isEmpty())
                                <div class="alert alert-warning mb-0">
                                    Aucun plat disponible à la carte.
                                    <a href="{{ route('restaurant.menus') }}">Ajouter des plats</a>.
                                </div>
                            @endif

                            <label class="form-label mt-3">Notes pour la cuisine</label>
                            <textarea class="form-control" name="notes" rows="3" maxlength="1000"
                                      placeholder="Sans piment, allergie…"></textarea>
                        </div>

                        {{-- Panier --}}
                        <div class="col-lg-6">
                            <label class="form-label">Commande en cours</label>
                            <div class="table-responsive border rounded">
                                <table class="table table-sm mb-0" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th>Plat</th>
                                            <th style="width: 90px;">Qté</th>
                                            <th class="text-end">Total</th>
                                            <th style="width: 44px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="emptyCart">
                                            <td colspan="4" class="text-center text-muted py-3">
                                                Aucun plat ajouté
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 p-3 rounded"
                                 style="background: var(--rs-brown-100);">
                                <strong>Total</strong>
                                <strong id="orderTotal" style="font-size: 1.2rem; color: var(--rs-brown-500);">
                                    0 FCFA
                                </strong>
                            </div>
                            <input type="hidden" name="items" id="itemsInput">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="rs-btn rs-btn-ghost" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="rs-btn rs-btn-primary" id="submitOrder">
                        <i class="fas fa-floppy-disk"></i> Enregistrer la commande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('footer')
<script>
$(function () {
    const csrf = '{{ csrf_token() }}';
    let items = [];

    // ── Panier ─────────────────────────────────
    function formatCFA(value) {
        return Math.round(value).toLocaleString('fr-FR').replace(/ | /g, ' ') + ' FCFA';
    }

    function renderItems() {
        const $tbody = $('#itemsTable tbody');
        $tbody.find('tr:not(#emptyCart)').remove();

        if (items.length === 0) {
            $('#emptyCart').show();
        } else {
            $('#emptyCart').hide();
            items.forEach(function (item, index) {
                $tbody.append(
                    '<tr>' +
                        '<td>' + $('<div>').text(item.name).html() + '</td>' +
                        '<td><input type="number" class="form-control form-control-sm js-qty" ' +
                            'data-index="' + index + '" value="' + item.quantity + '" min="1" max="50"></td>' +
                        '<td class="text-end">' + formatCFA(item.price * item.quantity) + '</td>' +
                        '<td class="text-end"><button type="button" class="btn btn-sm btn-link text-danger js-remove" ' +
                            'data-index="' + index + '"><i class="fas fa-trash"></i></button></td>' +
                    '</tr>'
                );
            });
        }

        const total = items.reduce((sum, item) => sum + item.price * item.quantity, 0);
        $('#orderTotal').text(formatCFA(total));
        $('#itemsInput').val(JSON.stringify(items.map(i => ({ menu_id: i.menu_id, quantity: i.quantity }))));
    }

    $('#addItem').on('click', function () {
        const $option = $('#menuSelect option:selected');
        const menuId = $('#menuSelect').val();
        if (! menuId) return;

        const quantity = Math.max(1, parseInt($('#menuQty').val(), 10) || 1);
        const existing = items.find(i => i.menu_id == menuId);

        if (existing) {
            existing.quantity += quantity;
        } else {
            items.push({
                menu_id: parseInt(menuId, 10),
                name: $option.data('name'),
                price: parseFloat($option.data('price')),
                quantity: quantity
            });
        }

        $('#menuSelect').val('');
        $('#menuQty').val(1);
        renderItems();
    });

    $(document).on('input', '.js-qty', function () {
        const index = $(this).data('index');
        items[index].quantity = Math.max(1, parseInt($(this).val(), 10) || 1);
        renderItems();
    });

    $(document).on('click', '.js-remove', function () {
        items.splice($(this).data('index'), 1);
        renderItems();
    });

    // ── Type de commande : afficher les bons champs ──
    $('#orderType').on('change', function () {
        const type = $(this).val();
        $('.js-field-room').toggle(type === 'room_service');
        $('.js-field-table').toggle(type === 'dining');

        if (type !== 'room_service') $('#roomSelect').val('');
        if (type !== 'dining') $('#tableNumber').val('');
    }).trigger('change');

    // La chambre choisie désigne déjà son occupant
    $('#roomSelect').on('change', function () {
        const customerId = $('option:selected', this).data('customer');
        if (customerId) $('#customerSelect').val(customerId);
    });

    $('#newOrderForm').on('submit', function (e) {
        if (items.length === 0) {
            e.preventDefault();
            Swal.fire('Commande vide', 'Ajoutez au moins un plat avant d\'enregistrer.', 'warning');
            return false;
        }
        $('#submitOrder').prop('disabled', true);
    });

    // Ouverture directe depuis le hub (?new=1)
    if (new URLSearchParams(window.location.search).get('new')) {
        new bootstrap.Modal(document.getElementById('newOrderModal')).show();
    }

    // ── Détail d'une commande ──────────────────
    $('.js-view').on('click', function () {
        const id = $(this).data('id');
        const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
        $('#orderDetailsBody').html('<div class="text-center py-4"><div class="spinner-border text-secondary"></div></div>');
        modal.show();

        $.ajax({
            url: '{{ url('restaurant/orders') }}/' + id,
            headers: { 'Accept': 'application/json' },
            success: function (res) { $('#orderDetailsBody').html(res.html); },
            error: function () {
                $('#orderDetailsBody').html('<div class="alert alert-danger mb-0">Impossible de charger la commande.</div>');
            }
        });
    });

    // ── Changement de statut ───────────────────
    function updateStatus(id, status, extra) {
        return $.ajax({
            url: '{{ url('restaurant/orders') }}/' + id,
            type: 'PUT',
            data: Object.assign({ _token: csrf, status: status }, extra || {}),
            headers: { 'Accept': 'application/json' }
        });
    }

    $('.js-status').on('click', function () {
        const $btn = $(this).prop('disabled', true);
        updateStatus($btn.data('id'), $btn.data('status'))
            .done(function (res) {
                Swal.fire({ icon: 'success', title: res.message, timer: 1400, showConfirmButton: false })
                    .then(() => location.reload());
            })
            .fail(function () {
                $btn.prop('disabled', false);
                Swal.fire('Erreur', 'La mise à jour a échoué.', 'error');
            });
    });

    // ── Encaissement ───────────────────────────
    $('.js-pay').on('click', function () {
        const $btn = $(this);

        Swal.fire({
            title: 'Encaisser ' + $btn.data('total') + ' ?',
            input: 'select',
            inputOptions: {
                cash: 'Espèces',
                card: 'Carte bancaire',
                mobile: 'Mobile money',
                room_charge: 'Sur la note de la chambre'
            },
            inputValue: $btn.data('method') || 'cash',
            showCancelButton: true,
            confirmButtonText: 'Encaisser',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {
            if (! result.isConfirmed) return;

            updateStatus($btn.data('id'), 'paid', { payment_method: result.value })
                .done(function (res) {
                    Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                })
                .fail(function () { Swal.fire('Erreur', "L'encaissement a échoué.", 'error'); });
        });
    });

    // ── Nommer un client de passage ────────────
    $('.js-rename').on('click', function () {
        const $btn = $(this);

        Swal.fire({
            title: 'Nom du client',
            input: 'text',
            inputValue: $btn.data('name') || '',
            showCancelButton: true,
            confirmButtonText: 'Enregistrer',
            cancelButtonText: 'Annuler',
            reverseButtons: true
        }).then((result) => {
            if (! result.isConfirmed || ! result.value) return;

            $.ajax({
                url: '{{ url('restaurant/orders') }}/' + $btn.data('id') + '/guest-name',
                type: 'PUT',
                data: { _token: csrf, guest_name: result.value },
                headers: { 'Accept': 'application/json' },
                success: function (res) {
                    $('.js-client-name[data-id="' + $btn.data('id') + '"]').text(res.guest_name);
                    $btn.data('name', res.guest_name);
                    Swal.fire({ icon: 'success', title: res.message, timer: 1300, showConfirmButton: false });
                },
                error: function () { Swal.fire('Erreur', "Le nom n'a pas pu être enregistré.", 'error'); }
            });
        });
    });

    // ── Annulation ─────────────────────────────
    $('.js-cancel').on('click', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Annuler cette commande ?',
            text: 'Cette action est définitive.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, annuler',
            cancelButtonText: 'Retour',
            reverseButtons: true
        }).then((result) => {
            if (! result.isConfirmed) return;

            $.ajax({
                url: '{{ url('restaurant/orders') }}/' + id + '/cancel',
                type: 'PUT',
                data: { _token: csrf },
                headers: { 'Accept': 'application/json' },
                success: function (res) {
                    Swal.fire({ icon: 'success', title: res.message, timer: 1400, showConfirmButton: false })
                        .then(() => location.reload());
                },
                error: function (xhr) {
                    const message = xhr.responseJSON?.message || "L'annulation a échoué.";
                    Swal.fire('Erreur', message, 'error');
                }
            });
        });
    });
});
</script>
@endsection
