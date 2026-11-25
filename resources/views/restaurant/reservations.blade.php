@extends('template.master')
@section('title', 'Restaurant — Réservations de table')
@section('content')

@include('restaurant.partials.hub-styles')

<div class="rs-page">

    <div class="rs-header rs-animate">
        <div>
            <h1 class="rs-header-title"><i class="fas fa-calendar-check"></i> Réservations de table</h1>
            <p class="rs-header-sub">
                Demandes reçues depuis le site et réservations prises au comptoir
            </p>
        </div>
        <div class="rs-header-actions">
            <a href="{{ route('restaurant.index') }}" class="rs-btn rs-btn-ghost">
                <i class="fas fa-arrow-left"></i> Hub
            </a>
            <button class="rs-btn rs-btn-primary" data-bs-toggle="modal" data-bs-target="#newReservationModal">
                <i class="fas fa-plus"></i> Nouvelle réservation
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
        <div class="rs-stat rs-stat--tables">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">Tables aujourd'hui</div>
                    <div class="rs-stat-value">{{ $stats['today'] }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-calendar-day"></i></div>
            </div>
            <div class="rs-stat-meta"><i class="fas fa-users me-1"></i> {{ $stats['covers_today'] }} couvert(s)</div>
        </div>

        <div class="rs-stat rs-stat--open">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">À confirmer</div>
                    <div class="rs-stat-value">{{ $stats['pending'] }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-hourglass-half"></i></div>
            </div>
            <div class="rs-stat-meta"><i class="fas fa-globe me-1"></i> demandes du site</div>
        </div>

        <div class="rs-stat rs-stat--revenue">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">Confirmées à venir</div>
                    <div class="rs-stat-value">{{ $stats['confirmed'] }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-circle-check"></i></div>
            </div>
            <div class="rs-stat-meta"><i class="fas fa-calendar me-1"></i> aujourd'hui et après</div>
        </div>

        <div class="rs-stat rs-stat--orders">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">Total enregistrées</div>
                    <div class="rs-stat-value">{{ $reservations->total() }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-list"></i></div>
            </div>
            <div class="rs-stat-meta"><i class="fas fa-clock-rotate-left me-1"></i> historique complet</div>
        </div>
    </div>

    {{-- ════════ FILTRES ════════ --}}
    <div class="rs-panel rs-animate">
        <div class="rs-panel-body">
            <form method="GET" action="{{ route('restaurant.reservations') }}" class="rs-filters">
                <div class="rs-field" style="flex: 2 1 220px;">
                    <label for="search">Rechercher</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Nom ou téléphone…">
                </div>
                <div class="rs-field">
                    <label for="status">Statut</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tous les statuts</option>
                        @foreach(\App\Models\RestaurantReservation::STATUSES as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
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
                        @if(request()->hasAny(['search', 'status', 'date']))
                            <a href="{{ route('restaurant.reservations') }}" class="rs-btn rs-btn-ghost">
                                <i class="fas fa-xmark"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════ LISTE ════════ --}}
    <div class="rs-panel rs-animate">
        <div class="rs-panel-head">
            <h2 class="rs-panel-title"><i class="fas fa-list"></i> Réservations</h2>
        </div>
        <div class="rs-panel-body is-flush">
            @if($reservations->isEmpty())
                <div class="rs-empty">
                    <i class="fas fa-calendar-xmark"></i>
                    <h4>Aucune réservation</h4>
                    <p>
                        @if(request()->hasAny(['search', 'status', 'date']))
                            Aucune réservation ne correspond à ces filtres.
                        @else
                            Les demandes envoyées depuis le site apparaîtront ici automatiquement.
                        @endif
                    </p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="rs-table">
                        <thead>
                            <tr>
                                <th>Date &amp; heure</th>
                                <th>Client</th>
                                <th>Couverts</th>
                                <th>Table</th>
                                <th>Origine</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $reservation)
                                <tr>
                                    <td>
                                        <strong>{{ $reservation->reservation_date->format('d/m/Y') }}</strong>
                                        <div class="rs-ticket-meta">{{ $reservation->formatted_time }}</div>
                                    </td>
                                    <td>
                                        <strong>{{ $reservation->name }}</strong>
                                        <div class="rs-ticket-meta">
                                            <i class="fas fa-phone me-1"></i>{{ $reservation->phone }}
                                            @if($reservation->email)
                                                <br><i class="fas fa-envelope me-1"></i>{{ $reservation->email }}
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $reservation->persons }}</td>
                                    <td>{{ $reservation->table_type ?: '—' }}</td>
                                    <td>
                                        <span class="rs-badge rs-badge--secondary">
                                            {{ $reservation->source === 'admin' ? 'Comptoir' : 'Site web' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="rs-badge rs-badge--{{ $reservation->status_color }}">
                                            {{ $reservation->status_label }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end flex-wrap">
                                            @if($reservation->status === 'pending')
                                                <button class="rs-btn rs-btn-primary rs-btn-sm js-reservation"
                                                        data-id="{{ $reservation->id }}" data-status="confirmed">
                                                    <i class="fas fa-check"></i> Confirmer
                                                </button>
                                            @endif
                                            @if($reservation->status === 'confirmed')
                                                <button class="rs-btn rs-btn-ghost rs-btn-sm js-reservation"
                                                        data-id="{{ $reservation->id }}" data-status="seated">
                                                    <i class="fas fa-chair"></i> Installer
                                                </button>
                                            @endif
                                            @if($reservation->status === 'seated')
                                                <button class="rs-btn rs-btn-ghost rs-btn-sm js-reservation"
                                                        data-id="{{ $reservation->id }}" data-status="completed">
                                                    <i class="fas fa-flag-checkered"></i> Terminer
                                                </button>
                                            @endif
                                            @if(! in_array($reservation->status, ['cancelled', 'completed', 'no_show']))
                                                <button class="rs-btn rs-btn-ghost rs-btn-sm js-reservation"
                                                        data-id="{{ $reservation->id }}" data-status="cancelled"
                                                        data-confirm="1">
                                                    <i class="fas fa-xmark"></i>
                                                </button>
                                            @endif
                                            @if($reservation->notes)
                                                <button class="rs-btn rs-btn-ghost rs-btn-sm"
                                                        data-bs-toggle="popover" data-bs-trigger="focus"
                                                        data-bs-content="{{ $reservation->notes }}" title="Note">
                                                    <i class="fas fa-sticky-note"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($reservations->hasPages())
                    <div class="rs-pagination">{{ $reservations->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- ════════ MODALE : NOUVELLE RÉSERVATION ════════ --}}
<div class="modal fade" id="newReservationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Nouvelle réservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form method="POST" action="{{ route('restaurant.reservations.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nom du client *</label>
                            <input type="text" class="form-control" name="name" required maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone *</label>
                            <input type="text" class="form-control" name="phone" required maxlength="30">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nombre de personnes *</label>
                            <input type="number" class="form-control" name="persons" min="1" max="50" value="2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date *</label>
                            <input type="date" class="form-control" name="reservation_date"
                                   value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Heure *</label>
                            <input type="time" class="form-control" name="reservation_time" value="19:30" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type de table</label>
                            <select class="form-select" name="table_type">
                                <option value="">Sans préférence</option>
                                <option value="Intérieur">Intérieur</option>
                                <option value="Terrasse">Terrasse</option>
                                <option value="Vue jardin">Vue jardin</option>
                                <option value="Privatisée">Privatisée</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3" maxlength="1000"
                                      placeholder="Allergies, occasion particulière…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="rs-btn rs-btn-ghost" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="rs-btn rs-btn-primary">
                        <i class="fas fa-floppy-disk"></i> Enregistrer
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

    $('[data-bs-toggle="popover"]').each(function () {
        new bootstrap.Popover(this);
    });

    $('.js-reservation').on('click', function () {
        const $btn = $(this);
        const status = $btn.data('status');

        const run = () => {
            $btn.prop('disabled', true);
            $.ajax({
                url: '{{ url('restaurant/reservations') }}/' + $btn.data('id'),
                type: 'PUT',
                data: { _token: csrf, status: status },
                headers: { 'Accept': 'application/json' },
                success: function (res) {
                    Swal.fire({ icon: 'success', title: res.message, timer: 1400, showConfirmButton: false })
                        .then(() => location.reload());
                },
                error: function () {
                    $btn.prop('disabled', false);
                    Swal.fire('Erreur', "La mise à jour a échoué.", 'error');
                }
            });
        };

        if ($btn.data('confirm')) {
            Swal.fire({
                title: 'Annuler cette réservation ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, annuler',
                cancelButtonText: 'Retour',
                reverseButtons: true
            }).then((result) => { if (result.isConfirmed) run(); });
        } else {
            run();
        }
    });
});
</script>
@endsection
