<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Resources & references';
$pageDescription = 'Primary sources and regulators cited across ' . SITE_BRAND . '—bookmark this page for verification.';
$currentNav = 'resources';

$pageOgImage = image_asset('card-fibre.jpg');
$pageOgImageAlt = 'Network cabling in a rack—symbolising the physical layer behind policy debates.';

$sidebarRelated = [
    ['href' => '/scotland.php',       'label' => 'Scotland policy'],
    ['href' => '/why-it-matters.php', 'label' => 'Why it matters'],
    ['href' => '/get-help.php',       'label' => 'Help getting online'],
    ['href' => '/news.php',           'label' => 'Campaign news'],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Resources &amp; references</h1>
        <p>Official and primary links we rely on when summarising policy. Start here before quoting figures in a community briefing, council question, or media enquiry.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">
        <div class="prose">
            <img class="page-hero-img" src="<?= e(image_asset('card-fibre.jpg')) ?>" width="1200" height="800"
                 alt="Fibre-optic cables in a patch panel." decoding="async" loading="lazy">
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
            <li><a href="https://guifi.net/"<?= external_link_attrs('https://guifi.net/') ?>>Guifi.net</a> — community network, Catalonia</li>
            <li><a href="https://www.nycmesh.net/"<?= external_link_attrs('https://www.nycmesh.net/') ?>>NYC Mesh</a> — volunteer mesh, New York City</li>
            <li><a href="https://www.librarieswithoutborders.org/ideasbox/"<?= external_link_attrs('https://www.librarieswithoutborders.org/ideasbox/') ?>>Libraries Without Borders — Ideas Box</a> — offline-first learning spaces</li>
            <li><a href="https://freifunk.net/en/"<?= external_link_attrs('https://freifunk.net/en/') ?>>Freifunk</a> — decentralised community wireless, Germany</li>
        </ol>

        <h2>Community networks in Scotland</h2>
        <p>Proof that community-owned and community-managed connectivity exists in Scotland right now — not as a future possibility, but as a working reality in some of our most remote areas.</p>
        <ol class="ref-list">
            <li><a href="https://www.hebnet.co.uk/"<?= external_link_attrs('https://www.hebnet.co.uk/') ?>>HebNet</a> — community internet for the Small Isles (Canna, Rum, Eigg, Muck) and Knoydart</li>
            <li><a href="https://www.ispreview.co.uk/index.php/2026/01/wireless-isp-highland-community-broadband-set-to-close-in-april-2026.html"<?= external_link_attrs('https://www.ispreview.co.uk/index.php/2026/01/wireless-isp-highland-community-broadband-set-to-close-in-april-2026.html') ?>>Highland Community Broadband</a> — served Ullapool 2017–2026; <strong>closed April 2026</strong> due to rising operational costs. A case study in why community networks need policy support to survive.</li>
            <li><a href="https://www.hie.co.uk/"<?= external_link_attrs('https://www.hie.co.uk/') ?>>Highlands and Islands Enterprise (HIE)</a> — administers Community Broadband Scotland funding; pilots in Applecross, Colonsay, Tomintoul and others</li>
            <li><a href="https://b4rn.org.uk/"<?= external_link_attrs('https://b4rn.org.uk/') ?>>B4RN</a> — Broadband for the Rural North; UK benchmark for community cooperative broadband; Lancashire</li>
        </ol>

        <h2>Providers taking ethics seriously</h2>
        <p>Not a recommendation list — but these UK providers hold B Corp certification or have community ownership as a core part of their model, not an afterthought. Worth knowing about when making the argument that the dominant commercial model is a choice, not an inevitability.</p>
        <ol class="ref-list">
            <li><a href="https://www.idnet.com/"<?= external_link_attrs('https://www.idnet.com/') ?>>IDNet</a> — UK's first B Corp certified telecommunications company</li>
            <li><a href="https://www.zen.co.uk/"<?= external_link_attrs('https://www.zen.co.uk/') ?>>Zen Internet</a> — B Corp certified; consumer and business broadband</li>
            <li><a href="https://cuckoo.co/"<?= external_link_attrs('https://cuckoo.co/') ?>>Cuckoo</a> — B Corp certified; now part of Octopus Energy Group</li>
            <li><a href="https://wildanet.com/"<?= external_link_attrs('https://wildanet.com/') ?>>Wildanet</a> — first UK AltNet to achieve B Corp; rural gigabit in Cornwall</li>
            <li><a href="https://www.connexin.co.uk/"<?= external_link_attrs('https://www.connexin.co.uk/') ?>>Connexin</a> — B Corp certified full fibre provider</li>
        </ol>
        <p class="meta">B Corp status is independently verified but can change — check each provider's current certification on the <a href="https://www.bcorporation.net/en-us/find-a-b-corp/"<?= external_link_attrs('https://www.bcorporation.net/en-us/find-a-b-corp/') ?>>B Lab directory</a>. Listing here is not an endorsement.</p>

        <p><?= e(SITE_BRAND) ?> content may summarise these sources; numbers and eligibility rules can change. Always confirm details on the live pages before relying on them in casework.</p>
        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div><!-- /page-layout -->
    </div>
</div>

<?php
$ctaHeading = 'Something missing?';
$ctaBody    = 'If you know of a primary source we should add, tell us via the contact form.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
