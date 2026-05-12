<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$pageTitle = 'Get involved';
$pageDescription = 'Practical ways to support better connectivity in your community—without needing to be a network engineer.';
$currentNav = 'involved';

$pageOgImage = image_asset('card-community.jpg');
$pageOgImageAlt = 'People meeting together around a laptop—representing community organising.';

require_once dirname(__DIR__) . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Get involved</h1>
        <p>You do not need to know how fibre cabinets work to make a difference. Campaigns move when people translate technical programmes into questions neighbours can recognise.</p>
    </div>
</header>

<div class="section">
    <div class="wrap prose">
        <figure class="page-lede">
            <img src="<?= e(image_asset('card-community.jpg')) ?>" width="1200" height="800" alt="Group discussion with a laptop open on a table." decoding="async" loading="lazy">
            <figcaption>Start with the places people already gather: libraries, community centres, tenants’ meetings, parent groups.</figcaption>
        </figure>
        <h2>Start where you live</h2>
        <ul>
            <li><strong>Map the gaps gently.</strong> Collect anonymised examples: “our tenement has one provider”, “mobile signal drops at the school gate”. Pair stories with postcodes only if people consent.</li>
            <li><strong>Use official sources.</strong> When you write to a councillor, include links to Scottish Government connectivity pages and any local digital strategy PDFs so requests are easy to forward.</li>
            <li><strong>Visit libraries and community centres.</strong> Many digital inclusion programmes meet people where they already go. Ask what timetables look like and whether transport or childcare is a barrier.</li>
        </ul>

        <h2>Show up in democratic spaces</h2>
        <ul>
            <li>Attend <strong>community council</strong> meetings when digital items appear—or ask for a short standing update on connectivity.</li>
            <li>Submit questions to <strong>full council</strong> or scrutiny committees when budget lines touch digital infrastructure or inclusion contracts.</li>
            <li>Connect with tenants’ organisations and trades unions where <strong>remote work policies</strong> assume home broadband people may not have.</li>
        </ul>

        <h2>Help <?= e(SITE_BRAND) ?> directly</h2>
        <p>We welcome volunteers for research, plain-language editing, accessibility passes on the site, and local “open calls” where we crowdsource broken links or confusing leaflets.</p>
        <p><a class="btn btn-primary" href="/join.php">Join the group</a> <a class="btn btn-ghost" href="/contact.php">Contact us</a></p>

        <div class="callout">
            <p><strong>Safety note:</strong> Do not intercept neighbours’ traffic or tamper with street cabinets. Community networking is powerful when it is legal, consensual, and transparent—see our <a href="/global-spotlight.php">Global spotlight</a> for examples with public documentation.</p>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
