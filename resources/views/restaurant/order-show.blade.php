@extends('template.master')
@section('title', 'Commande '.$order->order_number)
@section('content')

@include('restaurant.partials.hub-styles')

<div class="rs-page">

    <div class="rs-header rs-animate">
        <div>
            <h1 class="rs-header-title">
                <i class="fas fa-receipt"></i> Commande {{ $order->order_number }}
            </h1>
            <p class="rs-header-sub">
                {{ $order->type_label }} · {{ $order->destination }} ·
                {{ $order->created_at->format('d/m/Y à H:i') }}
            </p>
        </div>
        <div class="rs-header-actions">
            <a href="{{ route('restaurant.orders') }}" class="rs-btn rs-btn-ghost">
                <i class="fas fa-arrow-left"></i> Commandes
            </a>
            <button class="rs-btn rs-btn-ghost" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rs-animate">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rs-animate">{{ session('error') }}</div>
    @endif

    {{-- Suivi du service --}}
    <div class="rs-panel rs-animate">
        <div class="rs-panel-head">
            <h2 class="rs-panel-title"><i class="fas fa-route"></i> Suivi</h2>
            <span class="rs-badge rs-badge--{{ $order->status_color }}">{{ $order->status_label }}</span>
        </div>
        <div class="rs-panel-body">
            <div class="d-flex flex-wrap gap-2">
                @if($order->is_editable)
                    @foreach(['pending' => 'En attente', 'preparing' => 'En préparation', 'delivered' => 'Servie'] as $key => $label)
                        <form method="POST" action="{{ route('restaurant.orders.update', $order->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="{{ $key }}">
                            <button type="submit"
                                    class="rs-btn {{ $order->status === $key ? 'rs-btn-primary' : 'rs-btn-ghost' }}"
                                    {{ $order->status === $key ? 'disabled' : '' }}>
                                {{ $label }}
                            </button>
                        </form>
                    @endforeach

                    <form method="POST" action="{{ route('restaurant.orders.update', $order->id) }}"
                          class="d-flex gap-2 align-items-center">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="paid">
                        <select name="payment_method" class="form-select form-select-sm" style="width: auto;">
                            <option value="cash">Espèces</option>
                            <option value="card">Carte bancaire</option>
                            <option value="mobile">Mobile money</option>
                            <option value="room_charge">Sur la note de la chambre</option>
                        </select>
                        <button type="submit" class="rs-btn rs-btn-primary">
                            <i class="fas fa-cash-register"></i> Encaisser
                        </button>
                    </form>
                @else
                    <p class="mb-0 text-muted">
                        Cette commande est clôturée ({{ strtolower($order->status_label) }}) : elle n'est plus modifiable.
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="rs-panel rs-animate">
        <div class="rs-panel-body">
            @include('restaurant.partials.order-details', ['order' => $order])
        </div>
    </div>
</div>
@endsection
