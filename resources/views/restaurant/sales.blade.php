@extends('template.master')
@section('title', 'Restaurant — Ventes')
@section('content')

@include('restaurant.partials.hub-styles')

@php
    $maxDaily = $dailyRevenue->max('revenue') ?: 1;
    $maxMonthly = $monthlyRevenue->max('revenue') ?: 1;
    $maxCategory = $salesByCategory->max('revenue') ?: 1;
    $moisFr = ['', 'janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];
@endphp

<div class="rs-page">

    <div class="rs-header rs-animate">
        <div>
            <h1 class="rs-header-title"><i class="fas fa-chart-line"></i> Ventes du restaurant</h1>
            <p class="rs-header-sub">
                Chiffre d'affaires des commandes servies ou payées — les commandes annulées ne comptent pas
            </p>
        </div>
        <div class="rs-header-actions">
            <a href="{{ route('restaurant.index') }}" class="rs-btn rs-btn-ghost">
                <i class="fas fa-arrow-left"></i> Hub
            </a>
            <a href="{{ route('restaurant.orders') }}" class="rs-btn rs-btn-ghost">
                <i class="fas fa-receipt"></i> Commandes
            </a>
            <button class="rs-btn rs-btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimer
            </button>
        </div>
    </div>

    {{-- ════════ CHIFFRES CLÉS ════════ --}}
    <div class="rs-stats rs-animate">
        <div class="rs-stat rs-stat--revenue">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">CA aujourd'hui</div>
                    <div class="rs-stat-value is-money">{{ Helper::formatCFA($totals['today']) }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-calendar-day"></i></div>
            </div>
            <div class="rs-stat-meta"><i class="fas fa-utensils me-1"></i> service du jour</div>
        </div>

        <div class="rs-stat rs-stat--orders">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">CA du mois</div>
                    <div class="rs-stat-value is-money">{{ Helper::formatCFA($totals['month']) }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-calendar"></i></div>
            </div>
            <div class="rs-stat-meta"><i class="fas fa-clock me-1"></i> {{ now()->locale('fr')->isoFormat('MMMM YYYY') }}</div>
        </div>

        <div class="rs-stat rs-stat--tables">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">CA total</div>
                    <div class="rs-stat-value is-money">{{ Helper::formatCFA($totals['revenue']) }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-sack-dollar"></i></div>
            </div>
            <div class="rs-stat-meta"><i class="fas fa-receipt me-1"></i> {{ $totals['orders'] }} commande(s)</div>
        </div>

        <div class="rs-stat rs-stat--open">
            <div class="rs-stat-top">
                <div>
                    <div class="rs-stat-label">Panier moyen</div>
                    <div class="rs-stat-value is-money">{{ Helper::formatCFA($totals['average']) }}</div>
                </div>
                <div class="rs-stat-icon"><i class="fas fa-scale-balanced"></i></div>
            </div>
            <div class="rs-stat-meta"><i class="fas fa-chart-simple me-1"></i> par commande</div>
        </div>
    </div>

    <div class="rs-grid rs-grid-2 rs-animate">
        <div>
            {{-- ════════ 7 DERNIERS JOURS ════════ --}}
            <div class="rs-panel">
                <div class="rs-panel-head">
                    <h2 class="rs-panel-title"><i class="fas fa-calendar-week"></i> 7 derniers jours</h2>
                </div>
                <div class="rs-panel-body">
                    @forelse($dailyRevenue as $day)
                        @php $pct = max(3, round($day->revenue / $maxDaily * 100)); @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="font-size: .86rem; font-weight: 600;">
                                    {{ \Carbon\Carbon::parse($day->date)->locale('fr')->isoFormat('ddd D MMM') }}
                                </span>
                                <span style="font-size: .86rem;">
                                    <span class="rs-money">{{ Helper::formatCFA($day->revenue) }}</span>
                                    <span class="rs-rank-meta">· {{ $day->nb_orders }} cmd</span>
                                </span>
                            </div>
                            <div style="height: 9px; background: var(--rs-brown-100); border-radius: 999px; overflow: hidden;">
                                <div style="width: {{ $pct }}%; height: 100%; background: var(--rs-brown-500); border-radius: 999px;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="rs-empty">
                            <i class="fas fa-chart-column"></i>
                            <h4>Aucune vente cette semaine</h4>
                            <p>Les commandes servies ou payées alimenteront ce graphique.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ════════ 12 DERNIERS MOIS ════════ --}}
            <div class="rs-panel">
                <div class="rs-panel-head">
                    <h2 class="rs-panel-title"><i class="fas fa-chart-column"></i> 12 derniers mois</h2>
                </div>
                <div class="rs-panel-body is-flush">
                    @if($monthlyRevenue->isEmpty())
                        <div class="rs-empty">
                            <i class="fas fa-chart-column"></i>
                            <h4>Pas encore d'historique mensuel</h4>
                            <p>Le suivi se construit au fil des mois.</p>
                        </div>
                    @else
                        <table class="rs-table">
                            <thead>
                                <tr>
                                    <th>Mois</th>
                                    <th>Commandes</th>
                                    <th class="text-end">Chiffre d'affaires</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyRevenue as $month)
                                    <tr>
                                        <td>{{ $moisFr[$month->month] }} {{ $month->year }}</td>
                                        <td>{{ $month->nb_orders }}</td>
                                        <td class="text-end rs-money">{{ Helper::formatCFA($month->revenue) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <div>
            {{-- ════════ TOP PLATS ════════ --}}
            <div class="rs-panel">
                <div class="rs-panel-head">
                    <h2 class="rs-panel-title"><i class="fas fa-ranking-star"></i> Top 10 des plats</h2>
                </div>
                <div class="rs-panel-body">
                    @forelse($topItems as $index => $item)
                        <div class="rs-rank">
                            <div class="rs-rank-pos">{{ $index + 1 }}</div>
                            <div class="rs-rank-body">
                                <div class="rs-rank-name">{{ $item->menu->name ?? 'Plat retiré de la carte' }}</div>
                                <div class="rs-rank-meta">
                                    {{ $item->total_qty }} vendu(s) · {{ Helper::formatCFA($item->total_revenue) }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rs-empty">
                            <i class="fas fa-utensils"></i>
                            <h4>Aucune vente enregistrée</h4>
                            <p>Le classement apparaîtra dès la première commande servie.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ════════ PAR CATÉGORIE ════════ --}}
            <div class="rs-panel">
                <div class="rs-panel-head">
                    <h2 class="rs-panel-title"><i class="fas fa-tags"></i> Ventes par catégorie</h2>
                </div>
                <div class="rs-panel-body">
                    @forelse($salesByCategory as $category => $line)
                        @php $pct = max(3, round($line['revenue'] / $maxCategory * 100)); @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="font-size: .86rem; font-weight: 600;">{{ $category }}</span>
                                <span style="font-size: .86rem;">
                                    <span class="rs-money">{{ Helper::formatCFA($line['revenue']) }}</span>
                                    <span class="rs-rank-meta">· {{ $line['qty'] }} article(s)</span>
                                </span>
                            </div>
                            <div style="height: 9px; background: var(--rs-brown-100); border-radius: 999px; overflow: hidden;">
                                <div style="width: {{ $pct }}%; height: 100%; background: var(--rs-brown-400); border-radius: 999px;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="rs-empty">
                            <i class="fas fa-tags"></i>
                            <h4>Aucune donnée par catégorie</h4>
                            <p>Les ventes se répartiront ici par section de la carte.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .rs-header-actions, .sidebar, .navbar, footer { display: none !important; }
    .rs-page { background: #fff; padding: 0; }
}
</style>
@endsection
