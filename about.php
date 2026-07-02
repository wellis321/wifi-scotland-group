<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'About the campaign';
$pageDescription = 'What ' . SITE_BRAND . ' stands for, how we work, and how we relate to public policy in Scotland.';
$currentNav      = 'about';

$pageOgImage    = image_asset('about-team.jpg');
$pageOgImageAlt = 'People collaborating around a table—symbolising volunteer campaign work.';

$sidebarRelated = [
    ['href' => '/get-involved.php',  'label' => 'Get involved'],
    ['href' => '/groups.php',        'label' => 'Local groups'],
    ['href' => '/global-spotlight.php', 'label' => 'Global spotlight'],
    ['href' => '/contact.php',       'label' => 'Contact us'],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>About <?= e(SITE_BRAND) ?></h1>
        <p>Web Infrastructure Rights for Everyone in Scotland. We campaign for affordable, reliable connectivity to be treated as essential public infrastructure.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">

        <div class="prose">
            <img class="page-hero-img" src="<?= e(image_asset('about-team.jpg')) ?>" width="1200" height="800"
                 alt="Team members collaborating at a shared desk." decoding="async" loading="lazy">

            <div class="pull-quote">
                <p>"Campaigns win when people translate policy into questions their neighbours recognise."</p>
            </div>

            <h2>Our starting point</h2>
            <p>
                Being "offline" today rarely means being disconnected from technology; it more often means relying on expensive mobile data, borrowing neighbours' Wi‑Fi,
                or travelling for a reliable signal. That unevenness maps onto existing inequalities—between urban and rural communities, renters and owners, and households with
                different levels of disposable income.
            </p>
            <p>
                <?= e(SITE_BRAND) ?> exists to <strong>name that friction in public</strong>, celebrate work that reduces it, and push for policy and practice that treat internet access as
                part of the basic fabric of life in Scotland—without losing sight of positive experiments internationally.
            </p>

            <figure class="article-img article-img--right">
                <img src="<?= e(image_asset('card-community.jpg')) ?>" width="1200" height="800"
                     alt="Community group meeting around a table with a laptop." decoding="async" loading="lazy">
                <figcaption>Start with the places people already gather.</figcaption>
            </figure>

            <h2>What we do (and do not do)</h2>
            <ul>
                <li><strong>Explain and signpost</strong> official Scottish and UK connectivity programmes in plain language, with links to primary documents.</li>
                <li><strong>Publish campaign news</strong> and short explainers grounded in verifiable sources—avoiding invented statistics.</li>
                <li><strong>Support local groups</strong> across Scotland's 32 council areas to map gaps, attend meetings, and raise questions.</li>
                <li><strong>Collect sign-ups</strong> from people who want to stay involved; we store only what you submit through our forms.</li>
                <li><strong>We do not provide individual tech support</strong> for home broadband faults; we point to providers, councils, and official schemes where appropriate.</li>
            </ul>

            <h2>Independence and tone</h2>
            <p>
                We are non-party and independent. Endorsement of a specific technology or provider is not the goal: <strong>governance, affordability, transparency, and dignity</strong> are.
                Where commercial networks deliver good outcomes, that is welcome; where community networks fill gaps, that deserves attention too.
            </p>

            <div class="info-card">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Have a correction or a local story?</h2>
                    <p class="info-card__sub">We want to hear it</p>
                </div>
                <div class="info-card__body">
                    <p>Especially if official maps or leaflets do not match what your street experiences. Include links to council or government pages where possible so we can follow up carefully.</p>
                    <p><a class="btn btn-ghost btn-sm" href="/contact.php">Use the contact form &rarr;</a></p>
                </div>
            </div>

            <p>
                <a class="btn btn-primary" href="/join.php">Join the campaign</a>
                <a class="btn btn-ghost" href="/get-involved.php" style="margin-left:0.5rem">Get involved</a>
            </p>
        </div>

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div>
    </div>
</div>

<?php
$ctaHeading = 'Join the campaign';
$ctaBody    = 'WIRES is volunteer-led. Whether you have five minutes or five hours a week, there\'s a way to help.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
