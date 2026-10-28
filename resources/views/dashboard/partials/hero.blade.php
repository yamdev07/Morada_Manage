{{-- ══════════════════════════════════════════════════════════
     Bandeau d'ouverture : occupation, semaine, plan des chambres
     ══════════════════════════════════════════════════════════ --}}
@php
    $taux = (int) round($stats['occupancyRate'] ?? 0);
    $circonference = 2 * M_PI * 70;          // rayon 70
    $remplissage = $circonference * (1 - $taux / 100);

    $maxSemaine = max(1, collect($weekOccupancy ?? [])->max('taux'));
@endphp

<section class="mh-hero">

    {{-- ─── Anneau d'occupation ─── --}}
    <div class="mh-ring-card">
        {{--
            Les dimensions et le remplissage sont ecrits en attributs, pas
            seulement en CSS : si la feuille de styles ne charge pas, l'anneau
            reste un petit disque correct au lieu d'envahir la page.
        --}}
        <div class="mh-ring-wrap" style="position:relative;width:190px;height:190px">
            <svg viewBox="0 0 160 160" class="mh-ring" role="img"
                 width="190" height="190" style="max-width:190px;max-height:190px"
                 aria-label="Taux d'occupation : {{ $taux }} pour cent">
                <circle cx="80" cy="80" r="70" class="mh-ring-track"
                        fill="none" stroke="#f1eae0" stroke-width="14"/>
                <circle cx="80" cy="80" r="70" class="mh-ring-fill"
                        fill="none" stroke="#8b4513" stroke-width="14" stroke-linecap="round"
                        transform="rotate(-90 80 80)"
                        style="stroke-dasharray: {{ $circonference }}; stroke-dashoffset: {{ $remplissage }};"/>
            </svg>
            <div class="mh-ring-center">
                <div class="mh-ring-value">{{ $taux }}<span>%</span></div>
                <div class="mh-ring-label">Occupation</div>
            </div>
        </div>

        <div class="mh-ring-legend">
            <a href="{{ route('room.index') }}?status=occupied" class="mh-leg">
                <span class="mh-leg-dot is-occupied"></span>
                <span class="mh-leg-num">{{ $stats['occupiedRooms'] ?? 0 }}</span>
                <span class="mh-leg-txt">occupées</span>
            </a>
            <a href="{{ route('room.index') }}?status=available" class="mh-leg">
                <span class="mh-leg-dot is-free"></span>
                <span class="mh-leg-num">{{ $stats['availableRooms'] ?? 0 }}</span>
                <span class="mh-leg-txt">libres</span>
            </a>
        </div>
    </div>

    {{-- ─── Courbe des 7 derniers jours ─── --}}
    <div class="mh-week-card">
        <div class="mh-card-top">
            <div>
                <h3 class="mh-card-title">Occupation de la semaine</h3>
                <p class="mh-card-sub">7 derniers jours · {{ $stats['totalRooms'] ?? 0 }} chambres</p>
            </div>
            <a href="{{ route('reports.index') }}" class="mh-link">
                Rapports <i class="fas fa-arrow-right fa-xs"></i>
            </a>
        </div>

        <div class="mh-bars">
            @foreach($weekOccupancy ?? [] as $jour)
                @php $hauteur = max(4, round($jour['taux'] / $maxSemaine * 100)); @endphp
                <div class="mh-bar-col" title="{{ $jour['date'] }} — {{ $jour['taux'] }}% ({{ $jour['nb'] }} chambres)">
                    <div class="mh-bar-value">{{ $jour['taux'] }}%</div>
                    <div class="mh-bar-track">
                        <div class="mh-bar-fill {{ $loop->last ? 'is-today' : '' }}"
                             style="height: {{ $hauteur }}%"></div>
                    </div>
                    <div class="mh-bar-day">{{ $jour['jour'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ─── Plan des chambres ─── --}}
    <div class="mh-map-card">
        <div class="mh-card-top">
            <div>
                <h3 class="mh-card-title">Plan des chambres</h3>
                <p class="mh-card-sub">{{ ($roomMap ?? collect())->count() }} chambres · état en direct</p>
            </div>
            <div class="mh-map-legend">
                <span><i class="mh-leg-dot is-free"></i> libre</span>
                <span><i class="mh-leg-dot is-occupied"></i> occupée</span>
                <span><i class="mh-leg-dot is-care"></i> service</span>
            </div>
        </div>

        @if(($roomMap ?? collect())->isEmpty())
            <div class="mh-empty">
                <i class="fas fa-bed"></i>
                <p>Aucune chambre enregistrée.</p>
                <a href="{{ route('room.create') }}" class="mh-link">Ajouter une chambre <i class="fas fa-arrow-right fa-xs"></i></a>
            </div>
        @else
            <div class="mh-map">
                @foreach($roomMap as $chambre)
                    @php
                        if ($chambre['occupee']) {
                            $classe = 'is-occupied';
                            $info = $chambre['client'].' · départ '.$chambre['depart'];
                        } elseif ((int) $chambre['statusId'] === 1) {
                            $classe = 'is-free';
                            $info = 'Libre';
                        } else {
                            $classe = 'is-care';
                            $info = 'Nettoyage ou maintenance';
                        }
                    @endphp

                    <a href="{{ route('room.index') }}" class="mh-room {{ $classe }}"
                       title="Chambre {{ $chambre['number'] }} — {{ $info }}">
                        <span class="mh-room-num">{{ $chambre['number'] }}</span>
                        @if($chambre['occupee'])
                            <span class="mh-room-dot"></span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>

</section>
