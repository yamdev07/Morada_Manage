{{-- Détail d'une commande : utilisé dans la modale (AJAX) et dans la page dédiée --}}
@php
    $paymentLabels = [
        'cash' => 'Espèces',
        'card' => 'Carte bancaire',
        'mobile' => 'Mobile money',
        'room_charge' => 'Sur la note de la chambre',
        'online' => 'En ligne',
    ];
@endphp

<div class="order-details">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Client &amp; destination</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th width="42%">Client</th>
                            <td>{{ $order->client_name }}</td>
                        </tr>
                        @if($order->customer && $order->customer->phone)
                            <tr>
                                <th>Téléphone</th>
                                <td>{{ $order->customer->phone }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Type</th>
                            <td>{{ $order->type_label }}</td>
                        </tr>
                        <tr>
                            <th>Destination</th>
                            <td>{{ $order->destination }}</td>
                        </tr>
                        <tr>
                            <th>Prise le</th>
                            <td>{{ $order->created_at->format('d/m/Y à H:i') }}</td>
                        </tr>
                        @if($order->createdBy)
                            <tr>
                                <th>Par</th>
                                <td>{{ $order->createdBy->name }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Récapitulatif</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th width="42%">Numéro</th>
                            <td><strong>{{ $order->order_number }}</strong></td>
                        </tr>
                        <tr>
                            <th>Statut</th>
                            <td>
                                <span class="badge bg-{{ $order->status_color }}">{{ $order->status_label }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Total</th>
                            <td class="h5 mb-0" style="color: #8b4513;">{{ $order->formatted_total }}</td>
                        </tr>
                        @if($order->payment_method)
                            <tr>
                                <th>Paiement</th>
                                <td>{{ $paymentLabels[$order->payment_method] ?? $order->payment_method }}</td>
                            </tr>
                        @endif
                        @if($order->served_at)
                            <tr>
                                <th>Servie à</th>
                                <td>{{ $order->served_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endif
                        @if($order->paid_at)
                            <tr>
                                <th>Payée le</th>
                                <td>{{ $order->paid_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-list-alt me-2"></i>Articles commandés</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Plat</th>
                            <th class="text-center">Prix unitaire</th>
                            <th class="text-center">Qté</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->items as $item)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($item->menu && $item->menu->image)
                                            <img src="{{ asset('storage/'.$item->menu->image) }}"
                                                 alt="{{ $item->menu->name }}" class="rounded"
                                                 style="width: 46px; height: 46px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <strong>{{ $item->menu->name ?? 'Plat retiré de la carte' }}</strong>
                                            @if($item->menu && $item->menu->trashed())
                                                <span class="badge bg-secondary ms-1">retiré</span>
                                            @endif
                                            @if($item->menu && $item->menu->description)
                                                <br><small class="text-muted">{{ Str::limit($item->menu->description, 60) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">{{ Helper::formatCFA($item->price) }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">{{ Helper::formatCFA($item->price * $item->quantity) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Aucun article.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-active">
                            <th colspan="3" class="text-end">Total à payer</th>
                            <th class="text-end h5 mb-0">{{ $order->formatted_total }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @if($order->notes)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes</h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $order->notes }}</p>
            </div>
        </div>
    @endif
</div>

<style>
.order-details .card { border: 1px solid #f0e3d4; }
.order-details .card-header { background-color: #fcf8f3; border-bottom: 1px solid #f0e3d4; padding: .7rem 1.1rem; }
.order-details .table th { font-weight: 600; color: #4e3024; }
</style>
