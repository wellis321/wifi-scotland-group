<?php

declare(strict_types=1);
?>
</main>
<footer class="site-footer">
    <div class="wrap footer-main">
        <a href="/" class="footer-logo-link" aria-label="WIRES home">
            <div class="footer-logo-circle">
                <picture>
                    <source srcset="/images/logo.webp" type="image/webp">
                    <img src="/images/logo.png"
                         alt="WIRES — Web Infrastructure Rights for Everyone in Scotland"
                         width="400" height="218"
                         loading="lazy"
                         decoding="async">
                </picture>
            </div>
        </a>
        <div class="footer-text">
            <p class="footer-lead"><strong><?= e(SITE_BRAND) ?></strong> is a volunteer-led campaign for dignified, affordable connectivity in Scotland.</p>
            <p class="footer-small">We are independent and non-party; we signpost official sources and amplify community voices.</p>
        </div>
        <div class="footer-nav-cols">
            <div>
                <h2 class="footer-heading">The campaign</h2>
                <ul class="footer-links">
                    <li><a href="/why-it-matters">Why it matters</a></li>
                    <li><a href="/get-help">Get help online</a></li>
                    <li><a href="/get-involved">Get involved</a></li>
                    <li><a href="/accountability">Who is acting?</a></li>
                </ul>
            </div>
            <div>
                <h2 class="footer-heading">Evidence &amp; info</h2>
                <ul class="footer-links">
                    <li><a href="/scotland">Scotland policy</a></li>
                    <li><a href="/resources">Resources</a></li>
                    <li><a href="/wifi-map">WiFi map</a></li>
                    <li><a href="/news">News</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="wrap footer-meta">
        <p>&copy; <?= date('Y') ?> <?= e(SITE_BRAND) ?> &mdash; Volunteer-led and independent.</p>
        <p class="footer-legal">
            <a href="/privacy">Privacy</a>
            <span aria-hidden="true">&middot;</span>
            <a href="/terms">Terms of use</a>
            <span aria-hidden="true">&middot;</span>
            <a href="/credits">Image credits</a>
        </p>
    </div>
</footer>
<script>
(function () {
    var btn = document.querySelector('[data-nav-toggle]');
    var nav = document.querySelector('[data-site-nav]');
    var mq = window.matchMedia('(min-width: 901px)');
    var groups = document.querySelectorAll('[data-nav-group]');

    function closeAllSubmenus() {
        groups.forEach(function (g) {
            g.classList.remove('is-open');
            var t = g.querySelector('[data-nav-group-toggle]');
            if (t) t.setAttribute('aria-expanded', 'false');
        });
    }

    if (btn && nav) {
        btn.addEventListener('click', function () {
            var open = nav.classList.toggle('is-open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            if (!open) closeAllSubmenus();
        });

        nav.addEventListener('click', function (e) {
            if (mq.matches) return;
            if (e.target.closest('a.nav-link') || e.target.closest('a.nav-sub-link')) {
                nav.classList.remove('is-open');
                btn.setAttribute('aria-expanded', 'false');
                closeAllSubmenus();
            }
        });
    }

    groups.forEach(function (group) {
        var toggle = group.querySelector('[data-nav-group-toggle]');
        if (!toggle) return;

        group.addEventListener('mouseenter', function () {
            if (mq.matches) toggle.setAttribute('aria-expanded', 'true');
        });
        group.addEventListener('mouseleave', function () {
            if (mq.matches) toggle.setAttribute('aria-expanded', 'false');
        });
        group.addEventListener('focusin', function () {
            if (mq.matches) toggle.setAttribute('aria-expanded', 'true');
        });
        group.addEventListener('focusout', function (e) {
            if (!mq.matches) return;
            if (!group.contains(e.relatedTarget)) toggle.setAttribute('aria-expanded', 'false');
        });

        toggle.addEventListener('click', function (e) {
            if (mq.matches) return;
            e.preventDefault();
            var willOpen = !group.classList.contains('is-open');
            groups.forEach(function (other) {
                if (other !== group) {
                    other.classList.remove('is-open');
                    var ot = other.querySelector('[data-nav-group-toggle]');
                    if (ot) ot.setAttribute('aria-expanded', 'false');
                }
            });
            group.classList.toggle('is-open', willOpen);
            toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });
    });

    mq.addEventListener('change', function () {
        closeAllSubmenus();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAllSubmenus();
    });
})();
</script>
<?= $pageExtraScripts ?? '' ?>

<!-- Cookie notice -->
<div id="wires-cookie-notice" class="cookie-notice" role="dialog" aria-label="Cookie notice" aria-live="polite" hidden>
    <div class="cookie-notice__inner wrap">
        <p class="cookie-notice__text">
            This site uses a single security cookie to keep forms safe — it stores no personal data and is deleted when you close your browser.
            We don't use advertising or tracking cookies.
            <a href="/privacy.php">Privacy notice</a>
        </p>
        <button class="btn btn-sm cookie-notice__btn" id="wires-cookie-ok" type="button">Got it</button>
    </div>
</div>
<script>
(function () {
    var notice = document.getElementById('wires-cookie-notice');
    var btn    = document.getElementById('wires-cookie-ok');
    if (!notice) return;

    function setCookie(name, days) {
        var expires = new Date(Date.now() + days * 864e5).toUTCString();
        document.cookie = name + '=1; expires=' + expires + '; path=/; SameSite=Lax';
    }

    function hasCookie(name) {
        return document.cookie.split('; ').some(function (c) {
            return c.split('=')[0] === name;
        });
    }

    if (!hasCookie('wires_ack')) {
        notice.removeAttribute('hidden');
        notice.focus();
    }

    if (btn) {
        btn.addEventListener('click', function () {
            setCookie('wires_ack', 365);
            notice.setAttribute('hidden', '');
        });
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !notice.hasAttribute('hidden')) {
            setCookie('wires_ack', 365);
            notice.setAttribute('hidden', '');
        }
    });
})();
</script>
<script src="/js/animations.js"></script>
</body>
</html>
