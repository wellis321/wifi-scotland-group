<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'About the campaign';
$pageDescription = 'What ' . SITE_BRAND . ' stands for, how we work, and how we relate to public policy in Scotland.';
$currentNav = 'about';

$pageOgImage = image_asset('about-team.jpg');
$pageOgImageAlt = 'People collaborating around a table—symbolising volunteer campaign work.';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>About <?= e(SITE_BRAND) ?></h1>
        <p>We campaign for affordable, reliable connectivity to be understood and delivered as essential infrastructure—especially where market roll-out alone leaves people behind.</p>
    </div>
</header>

<div class="section">
    <div class="wrap prose">
        <figure class="page-lede">
            <img src="<?= e(image_asset('about-team.jpg')) ?>" width="1200" height="800" alt="Team members collaborating at a shared desk." decoding="async" loading="lazy">
            <figcaption>Volunteer energy matters: campaigns win when people translate policy into questions their neighbours recognise.</figcaption>
        </figure>
        <h2>Our starting point</h2>
        <p>
            Being “offline” today rarely means being disconnected from technology; it more often means relying on expensive mobile data, borrowing neighbours’ Wi‑Fi,
            or travelling for a reliable signal. That unevenness maps onto existing inequalities—between urban and rural communities, renters and owners, and households with
            different levels of disposable income.
        </p>
        <p>
            <?= e(SITE_BRAND) ?> exists to <strong>name that friction in public</strong>, celebrate work that reduces it, and push for policy and practice that treat internet access as
            part of the basic fabric of life in Scotland—without losing sight of positive experiments internationally.
        </p>

        <h2>What we do (and do not do)</h2>
        <ul>
            <li><strong>Explain and signpost</strong> official Scottish and UK connectivity programmes in plain language, with links to primary documents.</li>
            <li><strong>Publish campaign news</strong> and short explainers grounded in verifiable sources—avoiding invented statistics.</li>
            <li><strong>Collect sign-ups</strong> from people who want to stay involved; we store only what you submit through our forms.</li>
            <li><strong>We do not provide individual tech support</strong> for home broadband faults; we point to providers, councils, and official schemes where appropriate.</li>
        </ul>

        <h2>Independence and tone</h2>
        <p>
            We are non-party and independent. Endorsement of a specific technology or provider is not the goal: <strong>governance, affordability, transparency, and dignity</strong> are.
            Where commercial networks deliver good outcomes, that is welcome; where community networks fill gaps, that deserves attention too.
        </p>

        <div class="callout">
            <p><strong>Have a correction or a local story?</strong> We want to hear it—especially if official maps or leaflets do not match what your street experiences.
                Use the <a href="/contact.php">contact form</a> and, where possible, include links to council or government pages so we can follow up carefully.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
