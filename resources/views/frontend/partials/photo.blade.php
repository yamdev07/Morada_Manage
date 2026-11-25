{{--
    Photo du site vitrine, tolérante aux fichiers absents.

    Si l'image existe dans /public, elle est affichée ; sinon on rend une
    plaque sobre aux couleurs de la maison (jamais un « image non disponible »).
    Le jour où la photo est déposée au bon chemin, elle apparaît toute seule.

    Paramètres :
      $src    string|array  chemin relatif à /public, ou URL absolue, ou liste de candidats
      $alt    string        texte alternatif (obligatoire pour l'accessibilité)
      $ratio  string        classe de proportion : m-ratio-43 (défaut), m-ratio-169, m-ratio-34, m-ratio-1, m-ratio-23
      $class  string        classes additionnelles sur l'enveloppe
      $zoom   bool          zoom au survol (défaut true)
      $label  string        légende affichée sur la plaque de remplacement
      $eager  bool          désactive le chargement différé (héros uniquement)
--}}
@php
    $candidates = is_array($src ?? null) ? $src : [$src ?? null];

    $resolved = null;
    foreach ($candidates as $candidate) {
        if (! $candidate) {
            continue;
        }

        // URL absolue : on fait confiance à la source distante
        if (Str::startsWith($candidate, ['http://', 'https://', '//'])) {
            $resolved = $candidate;
            break;
        }

        $relative = ltrim($candidate, '/');
        if (is_file(public_path($relative))) {
            $resolved = asset($relative);
            break;
        }
    }

    $wrapperClass = trim(
        'm-photo '
        .($ratio ?? 'm-ratio-43').' '
        .(($zoom ?? true) ? 'm-photo-zoom ' : '')
        .($class ?? '')
    );
@endphp

<div class="{{ $wrapperClass }}">
    @if($resolved)
        <img src="{{ $resolved }}"
             alt="{{ $alt ?? '' }}"
             @if($eager ?? false) fetchpriority="high" @else loading="lazy" decoding="async" @endif>
    @else
        <div class="m-photo-fallback" role="img" aria-label="{{ $alt ?? 'Photographie à venir' }}">
            <span>{{ $label ?? ($alt ?? 'Morada Lodge') }}</span>
            <small>Photographie à venir</small>
            <span class="m-fallback-marks" aria-hidden="true"><b></b><b></b><b></b></span>
        </div>
    @endif
</div>
