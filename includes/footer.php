<?php

declare(strict_types=1);
?>
</main>
<footer class="site-footer">
    <div class="wrap footer-grid">
        <div>
            <p class="footer-lead"><strong><?= e(SITE_BRAND) ?></strong> — Web Infrastructure Rights for Everyone in Scotland — is a volunteer-led campaign for dignified, affordable connectivity.</p>
            <p class="footer-small">We are independent and non-party; we signpost official sources and amplify community voices.</p>
        </div>
        <div>
            <h2 class="footer-heading">Explore</h2>
            <ul class="footer-links">
                <li><a href="/privacy.php">Privacy</a></li>
                <li><a href="/credits.php">Image credits</a></li>
                <li><a href="/resources.php">Resources &amp; references</a></li>
                <li><a href="/contact.php">Contact</a></li>
            </ul>
        </div>
    </div>
    <div class="wrap footer-meta">
        <p>&copy; <?= date('Y') ?> <?= e(SITE_BRAND) ?> &mdash; Web Infrastructure Rights for Everyone in Scotland. Volunteer-led and independent.</p>
        <p class="footer-legal">
            <a href="/privacy.php">Privacy</a>
            <span aria-hidden="true">&middot;</span>
            <a href="/terms.php">Terms of use</a>
            <span aria-hidden="true">&middot;</span>
            <a href="/credits.php">Image credits</a>
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
</body>
</html>
