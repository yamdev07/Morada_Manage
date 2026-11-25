/* ============================================================
   MORADA LODGE — interactions du site vitrine
   Sans dépendance : IntersectionObserver + quelques écouteurs.
   Tout est optionnel : si un élément n'existe pas, on passe.
   ============================================================ */
(function () {
    'use strict';

    document.documentElement.classList.remove('no-js');

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ─── 1. Révélation au défilement ─────────────────────── */
    function initReveal() {
        var items = document.querySelectorAll('[data-reveal], [data-aos]');
        if (!items.length) return;

        if (reduceMotion || !('IntersectionObserver' in window)) {
            items.forEach(function (el) { el.classList.add('is-in'); });
            return;
        }

        // A partir d'ici seulement, on autorise le masquage initial :
        // si quoi que ce soit a echoue avant, la page est restee visible.
        document.documentElement.classList.add('reveal-on');

        // Filet de securite : rien ne doit rester invisible, quoi qu'il arrive.
        setTimeout(function () {
            document.querySelectorAll('[data-reveal]:not(.is-in), [data-aos]:not(.is-in)')
                .forEach(function (el) { el.classList.add('is-in'); });
        }, 4000);

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;

                var el = entry.target;
                // data-aos-delay existe déjà dans les vues : on le réutilise
                var delay = el.dataset.revealDelay || el.dataset.aosDelay || 0;

                setTimeout(function () { el.classList.add('is-in'); }, parseInt(delay, 10) || 0);
                observer.unobserve(el);
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

        items.forEach(function (el) { observer.observe(el); });
    }

    /* ─── 2. En-tête : transparent sur le héros, plein ensuite ─ */
    function initHeader() {
        var header = document.querySelector('.m-header');
        if (!header) return;

        var hero = document.querySelector('.m-hero, .m-pagehead');
        var lastY = window.scrollY;

        function update() {
            var y = window.scrollY;
            // Seuil : bas du héros, sinon 80px sur les pages sans héros
            var threshold = hero ? hero.offsetHeight - 120 : 80;

            if (y > threshold) {
                header.classList.add('is-solid');
                header.classList.remove('is-transparent');
            } else {
                header.classList.add('is-transparent');
                header.classList.remove('is-solid');
            }

            // Masquer en descendant, réafficher en remontant (hors sommet)
            if (y > threshold + 180 && y > lastY) {
                header.classList.add('is-hidden');
            } else {
                header.classList.remove('is-hidden');
            }

            lastY = y;
        }

        update();
        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);
    }

    /* ─── 3. Menu mobile plein écran ──────────────────────── */
    function initDrawer() {
        var drawer = document.querySelector('.m-drawer');
        var open = document.querySelector('.m-burger');
        var close = document.querySelector('.m-drawer-close');
        if (!drawer || !open) return;

        function setOpen(state) {
            drawer.classList.toggle('is-open', state);
            document.body.style.overflow = state ? 'hidden' : '';
            open.setAttribute('aria-expanded', state ? 'true' : 'false');

            // Entrée en cascade des liens
            drawer.querySelectorAll('a').forEach(function (link, i) {
                link.style.transitionDelay = state ? (0.12 + i * 0.06) + 's' : '0s';
            });
        }

        open.addEventListener('click', function () { setOpen(true); });
        if (close) close.addEventListener('click', function () { setOpen(false); });

        drawer.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () { setOpen(false); });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') setOpen(false);
        });
    }

    /* ─── 4. Titre du héros mot à mot ─────────────────────── */
    function initHeroWords() {
        var target = document.querySelector('[data-words]');
        if (!target || reduceMotion) return;

        // On préserve <em> : on découpe chaque nœud texte en mots
        var html = '';
        var index = 0;

        Array.prototype.forEach.call(target.childNodes, function (node) {
            if (node.nodeType === 3) {
                node.textContent.split(/(\s+)/).forEach(function (chunk) {
                    if (!chunk.trim()) { html += chunk; return; }
                    html += wrap(chunk, index++);
                });
            } else if (node.nodeType === 1) {
                var tag = node.tagName.toLowerCase();
                var inner = '';
                node.textContent.split(/(\s+)/).forEach(function (chunk) {
                    if (!chunk.trim()) { inner += chunk; return; }
                    inner += wrap(chunk, index++);
                });
                html += '<' + tag + '>' + inner + '</' + tag + '>';
            }
        });

        function wrap(word, i) {
            return '<span class="m-word"><span style="animation-delay:' +
                (0.25 + i * 0.075).toFixed(2) + 's">' + word + '</span></span>';
        }

        target.innerHTML = html;
    }

    /* ─── 5. Compteurs ────────────────────────────────────── */
    function initCounters() {
        var counters = document.querySelectorAll('[data-count]');
        if (!counters.length) return;

        if (reduceMotion || !('IntersectionObserver' in window)) {
            counters.forEach(function (el) { el.textContent = formatNum(el.dataset.count); });
            return;
        }

        function formatNum(value) {
            return Number(value).toLocaleString('fr-FR').replace(/ | /g, ' ');
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;

                var el = entry.target;
                var target = parseFloat(el.dataset.count) || 0;
                var duration = 1500;
                var start = performance.now();

                function step(now) {
                    var progress = Math.min((now - start) / duration, 1);
                    // easeOutExpo
                    var eased = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                    el.textContent = formatNum(Math.round(target * eased));
                    if (progress < 1) requestAnimationFrame(step);
                }

                requestAnimationFrame(step);
                observer.unobserve(el);
            });
        }, { threshold: 0.5 });

        counters.forEach(function (el) { observer.observe(el); });
    }

    /* ─── 6. Témoignages ──────────────────────────────────── */
    function initQuotes() {
        var root = document.querySelector('[data-quotes]');
        if (!root) return;

        var slides = root.querySelectorAll('[data-quote]');
        var dots = root.querySelectorAll('.m-dots button');
        if (slides.length < 2) return;

        var current = 0;
        var timer;

        function show(index) {
            current = (index + slides.length) % slides.length;
            slides.forEach(function (slide, i) {
                slide.style.display = i === current ? '' : 'none';
                slide.classList.toggle('is-in', i === current);
            });
            dots.forEach(function (dot, i) {
                dot.classList.toggle('is-active', i === current);
            });
        }

        function autoplay() {
            clearInterval(timer);
            timer = setInterval(function () { show(current + 1); }, 6500);
        }

        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () { show(i); autoplay(); });
        });

        show(0);
        autoplay();
    }

    /* ─── 7. Retour en haut ───────────────────────────────── */
    function initBackToTop() {
        var button = document.querySelector('.m-top');
        if (!button) return;

        window.addEventListener('scroll', function () {
            button.classList.toggle('is-shown', window.scrollY > 600);
        }, { passive: true });

        button.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
        });
    }

    /* ─── 8. Écran de chargement ──────────────────────────── */
    function initLoader() {
        var loader = document.querySelector('.m-loader');
        if (!loader) return;

        function hide() {
            loader.classList.add('is-done');
            setTimeout(function () { loader.remove(); }, 800);
        }

        if (document.readyState === 'complete') {
            setTimeout(hide, 250);
        } else {
            window.addEventListener('load', function () { setTimeout(hide, 350); });
            // Filet de sécurité : jamais bloquer la page
            setTimeout(hide, 3500);
        }
    }

    /* ─── 9. Lien de navigation actif ─────────────────────── */
    function initActiveNav() {
        var path = window.location.pathname.replace(/\/$/, '') || '/';

        document.querySelectorAll('.m-nav a, .m-drawer a').forEach(function (link) {
            var href = link.getAttribute('href');
            if (!href || href.charAt(0) === '#') return;

            try {
                var linkPath = new URL(href, window.location.origin).pathname.replace(/\/$/, '') || '/';
                if (linkPath === path) link.classList.add('is-active');
            } catch (e) { /* href non analysable : on ignore */ }
        });
    }

    /* ─── 10. Bandeau défilant : duplication pour boucler ─── */
    function initMarquee() {
        document.querySelectorAll('.m-marquee-track').forEach(function (track) {
            track.innerHTML += track.innerHTML;
        });
    }

    /* ─── 11. Parallaxe douce ─────────────────────────────── */
    function initParallax() {
        var items = document.querySelectorAll('[data-parallax]');
        if (!items.length || reduceMotion) return;

        var ticking = false;

        function update() {
            items.forEach(function (el) {
                var rect = el.getBoundingClientRect();
                if (rect.bottom < 0 || rect.top > window.innerHeight) return;

                var speed = parseFloat(el.dataset.parallax) || 0.12;
                var offset = (rect.top + rect.height / 2 - window.innerHeight / 2) * speed;
                el.style.transform = 'translate3d(0,' + offset.toFixed(1) + 'px,0)';
            });
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) { requestAnimationFrame(update); ticking = true; }
        }, { passive: true });

        update();
    }

    /* ─── Démarrage ───────────────────────────────────────── */
    function start() {
        initLoader();
        initHeader();
        initDrawer();
        initHeroWords();
        initReveal();
        initCounters();
        initQuotes();
        initBackToTop();
        initActiveNav();
        initMarquee();
        initParallax();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
