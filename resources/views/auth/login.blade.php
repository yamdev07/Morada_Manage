@extends('template.auth')
@section('title', 'Connexion — Morada Lodge')
@section('content')

<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ══════════════════════════════════════════════
   CONNEXION — charte Morada Lodge
   ══════════════════════════════════════════════ */
:root {
    --lg-ink:      #1b1310;
    --lg-espresso: #2a1a10;
    --lg-bark:     #46291a;
    --lg-clay:     #8b4513;
    --lg-gold:     #c99a3f;
    --lg-gold-soft:#e3c489;
    --lg-sand:     #e9dcc7;
    --lg-cream:    #faf5ec;
    --lg-ivory:    #fffdf9;
    --lg-muted:    #7a6655;
    --lg-line:     rgba(139, 69, 19, .16);
    --lg-ease:     cubic-bezier(.22, 1, .36, 1);
}

body {
    font-family: 'Inter', system-ui, sans-serif;
    background: var(--lg-cream) !important;
    margin: 0;
    min-height: 100vh;
}

/* Les décors du gabarit d'origine ne servent plus */
.auth-bg-animation, .auth-shape { display: none !important; }
.auth-container {
    min-height: 100vh;
    display: grid;
    place-items: center;
    padding: 28px 18px;
    background:
        radial-gradient(ellipse 60% 50% at 12% 10%, rgba(201,154,63,.14), transparent 60%),
        radial-gradient(ellipse 50% 50% at 92% 92%, rgba(139,69,19,.10), transparent 60%),
        var(--lg-cream);
}

/* ── Carte ────────────────────────────────── */
.lg-card {
    width: min(1060px, 100%);
    display: grid;
    grid-template-columns: 1.05fr 1fr;
    background: var(--lg-ivory);
    border-radius: 34px;
    overflow: hidden;
    box-shadow: 0 50px 110px -40px rgba(42, 26, 16, .45);
    animation: lgUp .8s var(--lg-ease) both;
}
@keyframes lgUp { from { opacity: 0; transform: translateY(26px); } }

@media (max-width: 900px) {
    .lg-card { grid-template-columns: 1fr; border-radius: 26px; }
    .lg-side { display: none; }
}

/* ── Panneau gauche ───────────────────────── */
.lg-side {
    position: relative;
    padding: 56px 48px;
    color: var(--lg-sand);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background:
        radial-gradient(ellipse 70% 55% at 22% 14%, rgba(201,154,63,.26), transparent 62%),
        linear-gradient(165deg, var(--lg-bark) 0%, var(--lg-espresso) 58%, #241610 100%);
    overflow: hidden;
}
/* Filet doré intérieur, comme sur les plaques du site */
.lg-side::before {
    content: '';
    position: absolute;
    inset: 20px;
    border: 1px solid rgba(201, 154, 63, .34);
    border-radius: 20px;
    pointer-events: none;
}

.lg-brand { position: relative; }
.lg-logo {
    width: 76px; height: 76px;
    border-radius: 20px;
    object-fit: cover;
    background: var(--lg-ivory);
    padding: 7px;
    box-shadow: 0 16px 36px -14px rgba(0,0,0,.5);
}
.lg-name {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 2.5rem;
    font-weight: 600;
    letter-spacing: .02em;
    color: var(--lg-ivory);
    margin: 24px 0 8px;
    line-height: 1.05;
}
.lg-slogan {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-style: italic;
    font-size: 1.1rem;
    color: var(--lg-gold-soft);
    margin: 0;
}
.lg-place {
    font-size: .64rem;
    letter-spacing: .3em;
    text-transform: uppercase;
    color: rgba(233, 220, 199, .6);
    margin-top: 18px;
}

.lg-features { list-style: none; padding: 0; margin: 44px 0 0; position: relative; }
.lg-features li {
    display: flex;
    gap: 17px;
    align-items: flex-start;
    padding: 17px 0;
    border-top: 1px solid rgba(233, 220, 199, .14);
}
.lg-features li:last-child { border-bottom: 1px solid rgba(233, 220, 199, .14); }
.lg-features i {
    flex: 0 0 38px;
    height: 38px;
    display: grid;
    place-items: center;
    border-radius: 50%;
    background: rgba(201, 154, 63, .16);
    color: var(--lg-gold-soft);
    font-size: .88rem;
}
.lg-features strong {
    display: block;
    font-size: .96rem;
    font-weight: 600;
    color: var(--lg-ivory);
    margin-bottom: 2px;
}
.lg-features small { font-size: .83rem; color: rgba(233, 220, 199, .66); }

.lg-back {
    position: relative;
    margin-top: 40px;
    font-size: .74rem;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: rgba(233, 220, 199, .72);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: color .3s var(--lg-ease), gap .3s var(--lg-ease);
}
.lg-back:hover { color: var(--lg-gold-soft); gap: 15px; }

/* ── Panneau droit ────────────────────────── */
.lg-form { padding: clamp(40px, 5vw, 62px) clamp(28px, 4.5vw, 58px); display: flex; flex-direction: column; justify-content: center; }

.lg-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 11px;
    font-size: .66rem;
    font-weight: 600;
    letter-spacing: .24em;
    text-transform: uppercase;
    color: var(--lg-clay);
    margin-bottom: 14px;
}
.lg-eyebrow::before { content: ''; width: 28px; height: 1px; background: currentColor; opacity: .6; }

.lg-title {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: clamp(2.1rem, 3.4vw, 2.9rem);
    font-weight: 500;
    color: var(--lg-espresso);
    margin: 0 0 10px;
    line-height: 1.08;
}
.lg-title em { font-style: italic; color: var(--lg-clay); }
.lg-sub { color: var(--lg-muted); font-size: .95rem; margin: 0 0 34px; }

.lg-field { margin-bottom: 22px; }
.lg-field label {
    display: block;
    font-size: .66rem;
    font-weight: 600;
    letter-spacing: .2em;
    text-transform: uppercase;
    color: var(--lg-muted);
    margin-bottom: 9px;
}
.lg-input-wrap { position: relative; }
.lg-input-wrap > i {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--lg-sand);
    font-size: .92rem;
    transition: color .3s var(--lg-ease);
    pointer-events: none;
}
/* Le champ mot de passe laisse la place au bouton d'affichage */
.lg-field input[type="password"],
.lg-field input.lg-has-toggle {
    padding-right: 54px !important;
}

/* Bouton afficher / masquer */
.lg-eye {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 38px;
    height: 38px;
    border: 0;
    border-radius: 50%;
    background: transparent;
    color: var(--lg-muted);
    cursor: pointer;
    display: grid;
    place-items: center;
    font-size: .95rem;
    transition: color .25s var(--lg-ease), background .25s var(--lg-ease);
}
.lg-eye:hover { color: var(--lg-clay); background: rgba(139, 69, 19, .07); }
.lg-eye:focus-visible {
    outline: none;
    color: var(--lg-clay);
    box-shadow: 0 0 0 3px rgba(201, 154, 63, .3);
}
.lg-eye.is-on { color: var(--lg-clay); }

.lg-field input[type="email"],
.lg-field input[type="password"],
.lg-field input[type="text"].lg-has-toggle {
    width: 100%;
    border: 1px solid var(--lg-line);
    border-radius: 14px;
    background: var(--lg-cream);
    padding: 16px 18px 16px 48px;
    font-family: 'Inter', sans-serif;
    font-size: .97rem;
    color: var(--lg-ink);
    transition: border-color .3s var(--lg-ease), background .3s var(--lg-ease), box-shadow .3s var(--lg-ease);
}
.lg-field input::placeholder { color: #b8a695; }
.lg-field input:focus {
    outline: none;
    border-color: var(--lg-gold);
    background: #fff;
    box-shadow: 0 0 0 4px rgba(201, 154, 63, .15);
}
.lg-field input:focus + i, .lg-input-wrap:focus-within > i { color: var(--lg-clay); }

/* Le remplissage automatique du navigateur ne doit pas virer au bleu */
.lg-field input:-webkit-autofill,
.lg-field input:-webkit-autofill:hover,
.lg-field input:-webkit-autofill:focus {
    -webkit-text-fill-color: var(--lg-ink);
    -webkit-box-shadow: 0 0 0 1000px var(--lg-cream) inset;
    transition: background-color 9999s ease-in-out 0s;
}

.lg-error {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 9px;
    font-size: .84rem;
    color: #b91c1c;
}

.lg-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin: 4px 0 30px;
}
.lg-check { display: inline-flex; align-items: center; gap: 10px; cursor: pointer; font-size: .9rem; color: var(--lg-muted); }
.lg-check input {
    width: 18px; height: 18px;
    accent-color: var(--lg-clay);
    cursor: pointer;
}
.lg-forgot {
    font-size: .84rem;
    color: var(--lg-clay);
    text-decoration: none;
    position: relative;
    padding-bottom: 3px;
}
.lg-forgot::after {
    content: ''; position: absolute; left: 0; bottom: 0;
    width: 100%; height: 1px; background: currentColor;
    transform: scaleX(0); transform-origin: right;
    transition: transform .4s var(--lg-ease);
}
.lg-forgot:hover { color: var(--lg-espresso); }
.lg-forgot:hover::after { transform: scaleX(1); transform-origin: left; }

.lg-submit {
    position: relative;
    width: 100%;
    border: 0;
    border-radius: 999px;
    padding: 18px 26px;
    background: var(--lg-clay);
    color: #fff;
    font-family: 'Inter', sans-serif;
    font-size: .82rem;
    font-weight: 600;
    letter-spacing: .14em;
    text-transform: uppercase;
    cursor: pointer;
    overflow: hidden;
    isolation: isolate;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    transition: transform .4s var(--lg-ease), box-shadow .4s var(--lg-ease);
}
.lg-submit::before {
    content: '';
    position: absolute; inset: 0; z-index: -1;
    background: var(--lg-espresso);
    transform: translateY(101%);
    transition: transform .5s var(--lg-ease);
}
.lg-submit:hover { transform: translateY(-2px); box-shadow: 0 18px 34px -16px rgba(42,26,16,.6); }
.lg-submit:hover::before { transform: translateY(0); }
.lg-submit:disabled { opacity: .72; cursor: wait; transform: none; }

.lg-alert {
    border-radius: 14px;
    padding: 14px 18px;
    font-size: .9rem;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 11px;
}
.lg-alert-error { background: #fdecec; color: #b91c1c; }
.lg-alert-ok { background: #e9f7ee; color: #15803d; }

.lg-foot {
    margin-top: 32px;
    text-align: center;
    font-size: .8rem;
    color: var(--lg-muted);
}
.lg-foot a { color: var(--lg-clay); text-decoration: none; }
.lg-foot a:hover { text-decoration: underline; }

.lg-mobile-brand { display: none; text-align: center; margin-bottom: 26px; }
@media (max-width: 900px) {
    .lg-mobile-brand { display: block; }
    .lg-mobile-brand img { width: 62px; height: 62px; border-radius: 16px; object-fit: cover; }
    .lg-mobile-brand div {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.5rem; color: var(--lg-espresso); margin-top: 10px;
    }
}
</style>

<div class="auth-container">
    <div class="lg-card">

        {{-- ══════════ PANNEAU GAUCHE ══════════ --}}
        <aside class="lg-side">
            <div class="lg-brand">
                <img src="{{ asset('img/logo/logo_ancien.jpg') }}" alt="Morada Lodge" class="lg-logo">
                <h1 class="lg-name">Morada Lodge</h1>
                <p class="lg-slogan">Votre oasis de tranquillité et de saveurs</p>
                <div class="lg-place">Covè &middot; République du Bénin</div>
            </div>

            <ul class="lg-features">
                <li>
                    <i class="fas fa-water"></i>
                    <div>
                        <strong>Piscine privée</strong>
                        <small>Détente absolue dans un cadre idyllique</small>
                    </div>
                </li>
                <li>
                    <i class="fas fa-utensils"></i>
                    <div>
                        <strong>Gastronomie</strong>
                        <small>Cuisine raffinée, locale et internationale</small>
                    </div>
                </li>
                <li>
                    <i class="fas fa-concierge-bell"></i>
                    <div>
                        <strong>Service attentionné</strong>
                        <small>Une équipe disponible 24 h/24</small>
                    </div>
                </li>
            </ul>

            <a href="{{ route('frontend.home') }}" class="lg-back">
                <i class="fas fa-arrow-left"></i> Retour au site
            </a>
        </aside>

        {{-- ══════════ FORMULAIRE ══════════ --}}
        <div class="lg-form">
            <div class="lg-mobile-brand">
                <img src="{{ asset('img/logo/logo_ancien.jpg') }}" alt="Morada Lodge">
                <div>Morada Lodge</div>
            </div>

            <span class="lg-eyebrow">Espace équipe</span>
            <h2 class="lg-title">Bon retour <em>parmi nous</em></h2>
            <p class="lg-sub">Connectez-vous pour accéder à votre tableau de bord.</p>

            @if(session('error'))
                <div class="lg-alert lg-alert-error">
                    <i class="fas fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="lg-alert lg-alert-ok">
                    <i class="fas fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form id="form-login" action="/login" method="POST">
                @csrf

                <div class="lg-field">
                    <label for="email">Adresse email</label>
                    <div class="lg-input-wrap">
                        <input type="email" id="email" name="email"
                               placeholder="vous@moradalodge.com"
                               value="{{ old('email') }}" required autofocus
                               autocomplete="username">
                        <i class="fas fa-envelope"></i>
                    </div>
                    @error('email')
                        <div class="lg-error">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="lg-field">
                    <label for="password">Mot de passe</label>
                    <div class="lg-input-wrap">
                        <input type="password" id="password" name="password"
                               class="lg-has-toggle"
                               placeholder="Votre mot de passe" required
                               autocomplete="current-password">
                        <i class="fas fa-lock"></i>
                        <button type="button" class="lg-eye" id="toggle-password"
                                aria-label="Afficher le mot de passe" aria-pressed="false">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="lg-error">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="lg-row">
                    <label class="lg-check">
                        <input type="checkbox" id="remember" name="remember">
                        <span>Se souvenir de moi</span>
                    </label>
                    <a href="/forgot-password" class="lg-forgot">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="lg-submit">
                    <span>Se connecter</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <div class="lg-foot">
                Un souci de connexion ?
                <a href="{{ route('frontend.contact') }}">Contactez la direction</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Afficher / masquer le mot de passe ──
    var champ = document.getElementById('password');
    var oeil = document.getElementById('toggle-password');

    if (champ && oeil) {
        oeil.addEventListener('click', function () {
            var visible = champ.type === 'text';

            champ.type = visible ? 'password' : 'text';
            oeil.classList.toggle('is-on', !visible);
            oeil.setAttribute('aria-pressed', visible ? 'false' : 'true');
            oeil.setAttribute('aria-label', visible ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
            oeil.querySelector('i').className = visible ? 'fas fa-eye' : 'fas fa-eye-slash';

            // On rend la main au champ, curseur a la fin
            champ.focus();
            var n = champ.value.length;
            try { champ.setSelectionRange(n, n); } catch (e) { /* type non concerne */ }
        });
    }

    var form = document.getElementById('form-login');
    if (!form) return;

    var bouton = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function () {
        // Le navigateur a deja verifie les champs requis : on signale juste l'attente
        bouton.disabled = true;
        bouton.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> <span>Connexion…</span>';

        // Filet de securite : si la page reste affichee, le bouton redevient utilisable
        setTimeout(function () {
            bouton.disabled = false;
            bouton.innerHTML = '<span>Se connecter</span> <i class="fas fa-arrow-right"></i>';
        }, 8000);
    });
});
</script>

@endsection
