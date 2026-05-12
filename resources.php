<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Resources & references';
$pageDescription = 'Primary sources and regulators cited across ' . SITE_BRAND . '—bookmark this page for verification.';
$currentNav = 'resources';

$pageOgImage = image_asset('card-fibre.jpg');
$pageOgImageAlt = 'Network cabling in a rack—symbolising the physical layer behind policy debates.';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Resources &amp; references</h1>
        <p>Official and primary links we rely on when summarising policy. If you are preparing a community briefing, start here rather than with unattributed social posts.</p>
    </div>
</header>

<div class="section">
    <div class="wrap prose">
        <figure class="page-lede">
            <img src="<?= e(image_asset('card-fibre.jpg')) ?>" width="1200" height="800" alt="Fibre-optic cables in a patch panel." decoding="async" loading="lazy">
            <figcaption>Physical infrastructure, regulation, and inclusion programmes are linked—use the official sources below before quoting figures in meetings.</figcaption>
        </figure>
        <h2>Scottish Government</h2>
        <ol class="ref-list">
            <li><a href="https://www.gov.scot/policies/digital/broadband-and-connectivity/"<?= external_link_attrs('https://www.gov.scot/policies/digital/broadband-and-connectivity/') ?>>Broadband and connectivity policy hub</a></li>
            <li><a href="https://digitalconnectivity.campaign.gov.scot/about-r100"<?= external_link_attrs('https://digitalconnectivity.campaign.gov.scot/about-r100') ?>>Reaching 100% (R100) public hub</a></li>
            <li><a href="https://www.gov.scot/publications/digital-strategy-scotland-vision-statement/"<?= external_link_attrs('https://www.gov.scot/publications/digital-strategy-scotland-vision-statement/') ?>>Digital strategy for Scotland: vision statement</a></li>
            <li><a href="https://www.gov.scot/publications/digital-strategy-scotland-sustainable-digital-public-services-delivery-plan-2025-2028/"<?= external_link_attrs('https://www.gov.scot/publications/digital-strategy-scotland-sustainable-digital-public-services-delivery-plan-2025-2028/') ?>>Digital public services delivery plan 2025–2028</a></li>
        </ol>

        <h2>Local government</h2>
        <ol class="ref-list">
            <li><a href="https://www.cosla.gov.uk/about-cosla/our-teams/digital-office"<?= external_link_attrs('https://www.cosla.gov.uk/about-cosla/our-teams/digital-office') ?>>COSLA Digital Office</a></li>
        </ol>

        <h2>UK regulators and reserved matters</h2>
        <ol class="ref-list">
            <li><a href="https://www.ofcom.org.uk/"<?= external_link_attrs('https://www.ofcom.org.uk/') ?>>Ofcom</a> — coverage, quality, and affordability research (search the site for current reports).</li>
        </ol>

        <h2>Global spotlight primaries</h2>
        <ol class="ref-list">
            <li><a href="https://guifi.net/"<?= external_link_attrs('https://guifi.net/') ?>>Guifi.net</a></li>
            <li><a href="https://www.nycmesh.net/"<?= external_link_attrs('https://www.nycmesh.net/') ?>>NYC Mesh</a></li>
            <li><a href="https://www.librarieswithoutborders.org/ideasbox/"<?= external_link_attrs('https://www.librarieswithoutborders.org/ideasbox/') ?>>Libraries Without Borders — Ideas Box</a></li>
            <li><a href="https://freifunk.net/en/"<?= external_link_attrs('https://freifunk.net/en/') ?>>Freifunk</a></li>
        </ol>

        <p><?= e(SITE_BRAND) ?> content may summarise these sources; numbers and eligibility rules can change. Always confirm details on the live pages before relying on them in casework.</p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
