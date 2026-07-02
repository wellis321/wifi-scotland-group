(function () {
    'use strict';

    // Bail immediately if user prefers reduced motion — CSS already collapses
    // transitions, but this prevents adding .js to <html> so elements stay visible.
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    // Signal to CSS that JS-driven animation is active
    document.documentElement.classList.add('js');

    /* ── Easing ──────────────────────────────────────────────────────────── */
    function easeOutQuart(t) {
        return 1 - Math.pow(1 - t, 4);
    }

    /* ── Count-up ────────────────────────────────────────────────────────── */
    function animateCount(el) {
        var target   = parseFloat(el.dataset.countTarget);
        var prefix   = el.dataset.countPrefix  || '';
        var suffix   = el.dataset.countSuffix  || '';
        var decimals = parseInt(el.dataset.countDecimals || '0', 10);
        var duration = 900;
        var startTs  = null;

        function tick(ts) {
            if (!startTs) startTs = ts;
            var progress = Math.min((ts - startTs) / duration, 1);
            var value    = easeOutQuart(progress) * target;
            el.textContent = prefix + value.toFixed(decimals) + suffix;
            if (progress < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
    }

    /* ── Statement band stats — staggered fade-up + count-up ────────────── */
    var statsContainer = document.querySelector('.statement-band-stats');
    if (statsContainer) {
        var statsObs = new IntersectionObserver(function (entries) {
            if (!entries[0].isIntersecting) return;
            var statEls = statsContainer.querySelectorAll('.statement-stat');
            statEls.forEach(function (stat, i) {
                setTimeout(function () {
                    stat.classList.add('is-visible');
                    var countEl = stat.querySelector('[data-count-target]');
                    // Start count-up just after the fade begins
                    if (countEl) setTimeout(function () { animateCount(countEl); }, 60);
                }, i * 130);
            });
            statsObs.unobserve(statsContainer);
        }, { threshold: 0.3 });
        statsObs.observe(statsContainer);
    }

    /* ── Generic fade-up (path cards, argument items) ────────────────────── */
    var fadeEls = document.querySelectorAll('.fade-up');
    if (!fadeEls.length) return;

    var fadeObs = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            var el    = entry.target;
            var delay = parseInt(el.dataset.delay || '0', 10);
            setTimeout(function () { el.classList.add('is-visible'); }, delay);
            fadeObs.unobserve(el);
        });
    }, {
        threshold: 0.12,
        rootMargin: '0px 0px -60px 0px'
    });

    fadeEls.forEach(function (el) { fadeObs.observe(el); });
})();
