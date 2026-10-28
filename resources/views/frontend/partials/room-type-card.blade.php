{{-- Carte d'une catégorie de chambre : nom, tarif, capacité et description viennent du back-office --}}
@php
    $rooms = $type->rooms;
    $price = $type->display_price;

    $imageRoom = $rooms->first(fn ($r) => $r->relationLoaded('images') && $r->images->isNotEmpty());
    $photo = $imageRoom ? $imageRoom->first_image_url : null;
    // Repli : une photo déposée au nom de la catégorie, sinon la plaque maison
    $photoCandidates = array_filter([$photo, 'img/morada/'.Str::slug($type->name).'.jpg']);

    if ($rooms->isNotEmpty()) {
        $minCap = $rooms->min('capacity');
        $maxCap = $rooms->max('capacity');
        $capacity = $minCap == $maxCap ? $minCap : $minCap.'-'.$maxCap;
    } else {
        $capacity = $type->capacity ?: 1;
    }

    $specs = collect($type->amenities_list)->filter()->take(3)->values();
    if ($specs->count() < 3 && $type->bed_type) {
        $specs->push(($type->bed_count > 1 ? $type->bed_count.' lits ' : 'Lit ').$type->bed_type);
    }
    if ($specs->count() < 3 && $type->size) {
        $specs->push($type->size.' m²');
    }
    $specs = $specs->take(3);

    $bookRoom = $rooms->firstWhere('room_status_id', \App\Models\Room::STATUS_AVAILABLE);
    $bookUrl = route('frontend.reservation').'?'.http_build_query(array_filter([
        'room' => $type->name,
        'room_type' => $type->name,
        'price' => (int) $price,
        'room_id' => $bookRoom?->id,
    ]));
@endphp

<article class="m-room" data-reveal data-reveal-delay="{{ $delay ?? 0 }}">
    <div style="position:relative">
        @include('frontend.partials.photo', [
            'src' => $photoCandidates,
            'alt' => $type->name,
            'ratio' => 'm-ratio-43',
            'label' => $type->name,
        ])
        @if(! empty($badge))
            <span class="m-tag">{{ $badge }}</span>
        @endif
    </div>

    <div class="m-room-body">
        <h3>{{ $type->name }}</h3>
        <div class="m-room-meta">
            <i class="fas fa-user-group" style="color:var(--m-gold)"></i> {{ $capacity }} personnes
            @if($rooms->isNotEmpty())
                &nbsp;·&nbsp; {{ $rooms->count() }} {{ Str::plural('chambre', $rooms->count()) }}
            @endif
        </div>

        @if($type->information)
            <p class="m-room-desc">{{ Str::limit(strip_tags($type->information), 135) }}</p>
        @endif

        @if($specs->isNotEmpty())
            <ul class="m-room-feats">
                @foreach($specs as $spec)
                    <li><i class="fas fa-diamond"></i> <span>{{ $spec }}</span></li>
                @endforeach
            </ul>
        @endif

        <div class="m-room-foot">
            <div class="m-price">
                {{ $type->has_price_range ? 'dès ' : '' }}{{ number_format($price, 0, ',', ' ') }}
                <small>FCFA / nuit</small>
            </div>
            <a href="{{ $bookUrl }}" class="m-link">
                Réserver <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</article>
