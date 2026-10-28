@extends('template.master')
@section('title', 'Restaurant — Hub')
@section('content')

@include('restaurant.partials.hub-styles')

<div class="rs-page">

    {{-- ════════ EN-TÊTE ════════ --}}
    <div class="rs-header rs-animate">
        <div>
            <h1 class="rs-header-title">
                <i class="fas fa-utensils"></i> Restaurant
            </h1>
            <p class="rs-header-sub">
                {{ ucfirst($today->locale('fr')->isoFormat('dddd D MMMM YYYY')) }}
                — service, commandes et réservations de table
            </p>
        </div>
        <div class="rs-header-actions">
            <a href="{{ route('restaurant.orders') }}?new=1" class="rs-btn rs-btn-primary">
                <i class="fas fa-plus"></i> Nouvelle commande
            </a>
            <a href="{{ route('restaurant.reservations') }}" class="rs-btn rs-btn-ghost">
                <i class="fas fa-calendar-check"></i> Réservations
            </a>
            <a href="{{ route('restaurant.menus') }}" class="rs-btn rs-btn-ghost">
                <i class="fas fa-book-open"></i> La carte
            </a>
            <a href="{{ route('restaurant.sales') }}" class="rs-btn rs-btn-ghost">
                <i class="fas fa-chart-line"></i> Ventes
            </a>
        </div>
    </div>

    {{-- ════════ STATISTIQUES ════════ --}}
    <div class="rs-stats rs-animate">
        <div class="rs-stat rs-stat--orders">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">Commandes du jour</div>
                    <div class="rs-stat-value">{{ $stats['orders_today'] }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-receipt"></i></div>
            </div>
            <div class="rs-stat-meta">
                <i class="fas fa-clock me-1"></i> {{ $stats['open_orders'] }} encore en cours
            </div>
        </div>

        <div class="rs-stat rs-stat--open">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">En cuisine</div>
                    <div class="rs-stat-value">{{ $stats['open_orders'] }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-fire-burner"></i></div>
            </div>
            <div class="rs-stat-meta">
                <i class="fas fa-utensils me-1"></i> à préparer ou à servir
            </div>
        </div>

        <div class="rs-stat rs-stat--revenue">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">Encaissé aujourd'hui</div>
                    <div class="rs-stat-value is-money">{{ Helper::formatCFA($stats['revenue_today']) }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-cash-register"></i></div>
            </div>
            <div class="rs-stat-meta">
                <i class="fas fa-check-circle me-1"></i> commandes payées
            </div>
        </div>

        <div class="rs-stat rs-stat--tables">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">Tables réservées</div>
                    <div class="rs-stat-value">{{ $stats['reservations_today'] }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-calendar-check"></i></div>
            </div>
            <div class="rs-stat-meta">
                @if($stats['pending_reservations'] > 0)
                    <i class="fas fa-exclamation-circle me-1 text-warning"></i>
                    {{ $stats['pending_reservations'] }} à confirmer
                @else
                    <i class="fas fa-check-circle me-1"></i> tout est confirmé
                @endif
            </div>
        </div>
    </div>

    {{-- ════════ SERVICE EN COURS + RÉSERVATIONS ════════ --}}
    <div class="rs-grid rs-grid-2 rs-animate">

        {{-- Colonne gauche : commandes ouvertes --}}
        <div>
            <div class="rs-panel">
                <div class="rs-panel-head">
                    <h2 class="rs-panel-title">
                        <i class="fas fa-fire-burner"></i> Commandes en cours
                    </h2>
                    <a href="{{ route('restaurant.orders') }}" class="rs-btn rs-btn-ghost rs-btn-sm">
                        Toutes les commandes <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="rs-panel-body is-flush">
                    @forelse($openOrders as $order)
                        <div class="rs-ticket" data-order="{{ $order->id }}">
                            <div class="rs-ticket-time">
                                <strong>{{ $order->created_at->format('H:i') }}</strong>
                                <span>{{ $order->created_at->diffForHumans(null, true) }}</span>
                            </div>
                            <div class="rs-ticket-body">
                                <div class="rs-ticket-title">
                                    <span class="rs-num">{{ $order->order_number }}</span>
                                    <span class="rs-badge rs-badge--{{ $order->status_color }}">
                                        {{ $order->status_label }}
                                    </span>
                                    <span class="rs-badge rs-badge--secondary">{{ $order->type_label }}</span>
                                </div>
                                <div class="rs-ticket-meta">
                                    <i class="fas fa-location-dot me-1"></i>{{ $order->destination }}
                                    @if($order->customer)
                                        · <i class="fas fa-user me-1"></i>{{ $order->customer->name }}
                                    @endif
                                    · <span class="rs-money">{{ $order->formatted_total }}</span>
                                </div>
                                <div class="rs-ticket-items">
                                    @foreach($order->items as $item)
                                        {{ $item->quantity }}× {{ $item->menu->name ?? 'Plat retiré' }}@if(! $loop->last), @endif
                                    @endforeach
                                </div>
                                @if($order->notes)
                                    <div class="rs-ticket-meta">
                                        <i class="fas fa-sticky-note me-1"></i>{{ $order->notes }}
                                    </div>
                                @endif
                                <div class="rs-ticket-actions">
                                    @if($order->status === 'pending')
                                        <button class="rs-btn rs-btn-primary rs-btn-sm js-status"
                                                data-id="{{ $order->id }}" data-status="preparing">
                                            <i class="fas fa-fire"></i> En préparation
                                        </button>
                                    @endif
                                    @if($order->status === 'preparing')
                                        <button class="rs-btn rs-btn-primary rs-btn-sm js-status"
                                                data-id="{{ $order->id }}" data-status="delivered">
                                            <i class="fas fa-bell-concierge"></i> Servie
                                        </button>
                                    @endif
                                    <a href="{{ route('restaurant.orders.show', $order->id) }}"
                                       class="rs-btn rs-btn-ghost rs-btn-sm">
                                        <i class="fas fa-eye"></i> Détail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rs-empty">
                            <i class="fas fa-mug-hot"></i>
                            <h4>Aucune commande en cours</h4>
                            <p>La cuisine est à jour : tout a été servi.</p>
                            <a href="{{ route('restaurant.orders') }}?new=1" class="rs-btn rs-btn-primary">
                                <i class="fas fa-plus"></i> Prendre une commande
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rs-panel">
                <div class="rs-panel-head">
                    <h2 class="rs-panel-title"><i class="fas fa-clock-rotate-left"></i> Dernières commandes clôturées</h2>
                </div>
                <div class="rs-panel-body is-flush">
                    @if($recentOrders->isEmpty())
                        <div class="rs-empty">
                            <i class="fas fa-receipt"></i>
                            <h4>Pas encore d'historique</h4>
                            <p>Les commandes servies, payées ou annulées apparaîtront ici.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="rs-table">
                                <thead>
                                    <tr>
                                        <th>N°</th>
                                        <th>Destination</th>
                                        <th>Statut</th>
                                        <th class="text-end">Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td class="rs-num">{{ $order->order_number }}</td>
                                            <td>
                                                {{ $order->destination }}
                                                <div class="rs-ticket-meta">{{ $order->created_at->format('d/m H:i') }}</div>
                                            </td>
                                            <td>
                                                <span class="rs-badge rs-badge--{{ $order->status_color }}">
                                                    {{ $order->status_label }}
                                                </span>
                                            </td>
                                            <td class="text-end rs-money">{{ $order->formatted_total }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('restaurant.orders.show', $order->id) }}"
                                                   class="rs-btn rs-btn-ghost rs-btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Colonne droite : réservations + top plats --}}
        <div>
            <div class="rs-panel">
                <div class="rs-panel-head">
                    <h2 class="rs-panel-title"><i class="fas fa-calendar-day"></i> Tables du jour</h2>
                    <a href="{{ route('restaurant.reservations') }}" class="rs-btn rs-btn-ghost rs-btn-sm">
                        Gérer <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="rs-panel-body is-flush">
                    @forelse($todayReservations as $reservation)
                        <div class="rs-ticket">
                            <div class="rs-ticket-time">
                                <strong>{{ $reservation->formatted_time }}</strong>
                                <span>{{ $reservation->persons }} pers.</span>
                            </div>
                            <div class="rs-ticket-body">
                                <div class="rs-ticket-title">
                                    {{ $reservation->name }}
                                    <span class="rs-badge rs-badge--{{ $reservation->status_color }}">
                                        {{ $reservation->status_label }}
                                    </span>
                                </div>
                                <div class="rs-ticket-meta">
                                    <i class="fas fa-phone me-1"></i>{{ $reservation->phone }}
                                    @if($reservation->table_type)
                                        · {{ $reservation->table_type }}
                                    @endif
                                </div>
                                @if($reservation->status === 'pending')
                                    <div class="rs-ticket-actions">
                                        <button class="rs-btn rs-btn-primary rs-btn-sm js-reservation"
                                                data-id="{{ $reservation->id }}" data-status="confirmed">
                                            <i class="fas fa-check"></i> Confirmer
                                        </button>
                                    </div>
                                @elseif($reservation->status === 'confirmed')
                                    <div class="rs-ticket-actions">
                                        <button class="rs-btn rs-btn-ghost rs-btn-sm js-reservation"
                                                data-id="{{ $reservation->id }}" data-status="seated">
                                            <i class="fas fa-chair"></i> Installer
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rs-empty">
                            <i class="fas fa-calendar-day"></i>
                            <h4>Aucune table réservée aujourd'hui</h4>
                            <p>Les demandes reçues depuis le site apparaissent ici.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @if($upcomingReservations->isNotEmpty())
                <div class="rs-panel">
                    <div class="rs-panel-head">
                        <h2 class="rs-panel-title"><i class="fas fa-calendar-plus"></i> Prochains jours</h2>
                    </div>
                    <div class="rs-panel-body is-flush">
                        <table class="rs-table">
                            <tbody>
                                @foreach($upcomingReservations as $reservation)
                                    <tr>
                                        <td>
                                            <strong>{{ $reservation->reservation_date->format('d/m') }}</strong>
                                            à {{ $reservation->formatted_time }}
                                        </td>
                                        <td>
                                            {{ $reservation->name }}
                                            <div class="rs-ticket-meta">{{ $reservation->persons }} personne(s)</div>
                                        </td>
                                        <td class="text-end">
                                            <span class="rs-badge rs-badge--{{ $reservation->status_color }}">
                                                {{ $reservation->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="rs-panel">
                <div class="rs-panel-head">
                    <h2 class="rs-panel-title"><i class="fas fa-ranking-star"></i> Plats les plus commandés</h2>
                    <span class="rs-ticket-meta">ce mois-ci</span>
                </div>
                <div class="rs-panel-body">
                    @forelse($topMenus as $index => $line)
                        <div class="rs-rank">
                            <div class="rs-rank-pos">{{ $index + 1 }}</div>
                            <div class="rs-rank-body">
                                <div class="rs-rank-name">{{ $line->menu->name ?? 'Plat retiré de la carte' }}</div>
                                <div class="rs-rank-meta">
                                    {{ $line->quantity_sold }} vendu(s) · {{ Helper::formatCFA($line->revenue) }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rs-empty">
                            <i class="fas fa-chart-simple"></i>
                            <h4>Pas encore de ventes ce mois-ci</h4>
                            <p>Le classement se construit dès la première commande.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ════════ ACCÈS RAPIDES ════════ --}}
    <div class="rs-quick rs-animate">
        <a href="{{ route('restaurant.orders') }}" class="rs-quick-item">
            <div class="rs-quick-icon"><i class="fas fa-receipt"></i></div>
            <div class="rs-quick-text">
                <strong>Commandes</strong>
                <span>Historique et encaissement</span>
            </div>
        </a>
        <a href="{{ route('restaurant.reservations') }}" class="rs-quick-item">
            <div class="rs-quick-icon"><i class="fas fa-calendar-check"></i></div>
            <div class="rs-quick-text">
                <strong>Réservations</strong>
                <span>{{ $stats['pending_reservations'] }} en attente</span>
            </div>
        </a>
        <a href="{{ route('restaurant.menus') }}" class="rs-quick-item">
            <div class="rs-quick-icon"><i class="fas fa-book-open"></i></div>
            <div class="rs-quick-text">
                <strong>La carte</strong>
                <span>{{ $stats['menus_total'] }} plats · {{ $stats['menus_unavailable'] }} épuisé(s)</span>
            </div>
        </a>
        <a href="{{ route('restaurant.sales') }}" class="rs-quick-item">
            <div class="rs-quick-icon"><i class="fas fa-chart-line"></i></div>
            <div class="rs-quick-text">
                <strong>Ventes</strong>
                <span>Chiffre d'affaires et top plats</span>
            </div>
        </a>
        <a href="{{ route('frontend.restaurant') }}" target="_blank" class="rs-quick-item">
            <div class="rs-quick-icon"><i class="fas fa-globe"></i></div>
            <div class="rs-quick-text">
                <strong>Carte publique</strong>
                <span>Voir la page du site</span>
            </div>
        </a>
    </div>
</div>
@endsection

@section('footer')
<script>
$(function () {
    const csrf = '{{ csrf_token() }}';

    function notify(icon, message) {
        if (window.Swal) {
            Swal.fire({ icon: icon, title: message, timer: 1600, showConfirmButton: false });
        }
    }

    // Avancer une commande dans le service sans quitter le hub
    $('.js-status').on('click', function () {
        const $btn = $(this).prop('disabled', true);

        $.ajax({
            url: '{{ url('restaurant/orders') }}/' + $btn.data('id'),
            type: 'PUT',
            data: { _token: csrf, status: $btn.data('status') },
            headers: { 'Accept': 'application/json' },
            success: function (res) {
                notify('success', res.message || 'Commande mise à jour.');
                setTimeout(() => location.reload(), 700);
            },
            error: function () {
                $btn.prop('disabled', false);
                notify('error', 'La mise à jour a échoué.');
            }
        });
    });

    // Confirmer / installer une réservation
    $('.js-reservation').on('click', function () {
        const $btn = $(this).prop('disabled', true);

        $.ajax({
            url: '{{ url('restaurant/reservations') }}/' + $btn.data('id'),
            type: 'PUT',
            data: { _token: csrf, status: $btn.data('status') },
            headers: { 'Accept': 'application/json' },
            success: function (res) {
                notify('success', res.message || 'Réservation mise à jour.');
                setTimeout(() => location.reload(), 700);
            },
            error: function () {
                $btn.prop('disabled', false);
                notify('error', 'La mise à jour a échoué.');
            }
        });
    });
});
</script>
@endsection
