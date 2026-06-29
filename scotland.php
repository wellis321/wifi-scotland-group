<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Scotland: government & councils';
$pageDescription = 'High-level summaries of Scottish connectivity programmes and local-government digital work—with links to official sources.';
$currentNav = 'scotland';

$pageOgImage    = image_asset('card-community.jpg');
$pageOgImageAlt = 'People in a community meeting — representing local voices in Scotland connectivity policy.';

$sidebarRelated = [
    ['href' => '/scotland-stories.php', 'label' => 'Scottish stories'],
    ['href' => '/wifi-map.php',         'label' => 'WiFi map by council area'],
    ['href' => '/why-it-matters.php',   'label' => 'Why it matters'],
    ['href' => '/get-help.php',         'label' => 'Help getting online'],
    ['href' => '/resources.php',        'label' => 'Resources and references'],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Scotland: policy, programmes &amp; councils</h1>
        <p>Scotland's digital and connectivity landscape spans Scottish Government investment, UK-wide schemes delivered in Scotland, and local authority services. This page points you to authoritative pages for numbers and eligibility rules.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">
        <div class="prose">
        <div class="campaign-statement-card" aria-hidden="true">
            <p class="campaign-statement-card__line1">Scotland</p>
            <p class="campaign-statement-card__line2">Public investment<br>should mean<br>public clarity.</p>
        </div>
        <h2>Scottish Government: broadband and connectivity</h2>
        <p>
            The Scottish Government maintains a dedicated policy area for <strong>broadband and connectivity</strong>, describing how public investment, commercial build,
            voucher schemes, and mobile programmes fit together. That hub is the best place to start when you want programme descriptions in the government's own words.
        </p>
        <p>
            <a href="https://www.gov.scot/policies/digital/broadband-and-connectivity/"<?= external_link_attrs('https://www.gov.scot/policies/digital/broadband-and-connectivity/') ?>>Broadband and connectivity (gov.scot)</a>
        </p>

        <h2>Reaching 100% (R100) and related delivery</h2>
        <p>
            <strong>Reaching 100% (R100)</strong> is the flagship Scottish programme associated with extending access where commercial build has been uneconomic.
            Public-facing descriptions emphasise contracts, voucher support, and collaboration with industry; timelines and premises counts change as build progresses,
            so we do not freeze figures here—use the programme site and official publications for current metrics.
        </p>
        <ul>
            <li><a href="https://digitalconnectivity.campaign.gov.scot/about-r100"<?= external_link_attrs('https://digitalconnectivity.campaign.gov.scot/about-r100') ?>>About Reaching 100% (Scottish Government campaign hub)</a></li>
            <li><a href="https://www.gov.scot/publications/reaching-100-superfast-broadband/"<?= external_link_attrs('https://www.gov.scot/publications/reaching-100-superfast-broadband/') ?>>Reaching 100% publications list (gov.scot)</a></li>
        </ul>

        <h2>Digital strategy and public services</h2>
        <p>
            Scotland's refreshed <strong>Digital Strategy</strong> frames how digital tools, data, and public services should develop together—with emphasis on inclusion,
            skills, and sustainable digital public services. The vision statement and the 2025–2028 delivery plan are useful when asking how connectivity relates to wider
            public-service reform, not only to consumer broadband marketing.
        </p>
        <ul>
            <li><a href="https://www.gov.scot/publications/digital-strategy-scotland-vision-statement/"<?= external_link_attrs('https://www.gov.scot/publications/digital-strategy-scotland-vision-statement/') ?>>Digital strategy for Scotland: vision statement</a></li>
            <li><a href="https://www.gov.scot/publications/digital-strategy-scotland-sustainable-digital-public-services-delivery-plan-2025-2028/"<?= external_link_attrs('https://www.gov.scot/publications/digital-strategy-scotland-sustainable-digital-public-services-delivery-plan-2025-2028/') ?>>Delivery plan 2025–2028</a></li>
        </ul>

        <h2>Local government: COSLA's Digital Office</h2>
        <p>
            Councils deliver many of the services people experience first-hand: libraries, education, housing-related support, and local economic development.
            The <strong>Convention of Scottish Local Authorities (COSLA)</strong> hosts a Digital Office that supports collaboration across councils on digital priorities,
            including inclusion and service design. When you want to understand “what councils are doing together,” that office is a sensible first reference.
        </p>
        <p>
            <a href="https://www.cosla.gov.uk/about-cosla/our-teams/digital-office"<?= external_link_attrs('https://www.cosla.gov.uk/about-cosla/our-teams/digital-office') ?>>COSLA Digital Office</a>
        </p>

        <h2>UK-wide context (Scotland included)</h2>
        <p>
            Some infrastructure funding and regulatory frameworks affecting Scotland originate at UK level—<strong>Project Gigabit</strong> style procurements and
            <strong>Ofcom</strong> reporting on coverage and quality are examples. When a policy question touches reserved matters, we still note it here because households
            experience outcomes as a single lived reality, even where responsibilities are split.
        </p>
        <ul>
            <li><a href="https://www.ofcom.org.uk/"<?= external_link_attrs('https://www.ofcom.org.uk/') ?>>Ofcom (UK communications regulator)</a> — search for coverage and affordability research.</li>
        </ul>

        <h2>Local authority action: Glasgow</h2>

        <p>
            Glasgow City Council published Scotland's first <strong>Digital Housing Strategy 2022–2028</strong> — the only local authority strategy to address both digital housing services and digital exclusion together. Its own evidence base produced a striking finding:
        </p>

        <div class="stat-strip">
            <div class="stat-item">
                <span class="stat-value">65%</span>
                <span class="stat-label">of households in Glasgow's social rented housing do not use home broadband</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">32</span>
                <span class="stat-label">RSLs backing the strategy, covering 75% of the city's social housing stock</span>
            </div>
        </div>

        <p>
            The strategy identifies affordability, lack of digital skills, and reluctance to transact online as the three primary barriers. Crucially, it includes a written safeguard requiring that traditional service methods — face-to-face and telephone — continue alongside digital channels, preventing digital-only design from excluding tenants.
        </p>
        <p>
            Progress is being reported through annual Local Housing Strategy reviews. A standalone evaluation of the Digital Housing Strategy has not yet been published — which means the accountability picture is still incomplete. The intent is documented; the outcomes are not yet independently verified.
        </p>
        <ul>
            <li>
                <a href="https://www.glasgow.gov.uk/article/2692/Glasgow-s-Digital-Housing-Strategy-to-improve-housing-services-and-tackle-digital-exclusion"<?= external_link_attrs('https://www.glasgow.gov.uk/article/2692/Glasgow-s-Digital-Housing-Strategy-to-improve-housing-services-and-tackle-digital-exclusion') ?>>Glasgow's Digital Housing Strategy — Glasgow City Council</a>
            </li>
            <li>
                <a href="https://glasgow.gov.uk/media/1717/Glasgow-s-Digital-Housing-Strategy/pdf/Glasgows_Digital_Housing_Strategy.pdf"<?= external_link_attrs('https://glasgow.gov.uk/media/1717/Glasgow-s-Digital-Housing-Strategy/pdf/Glasgows_Digital_Housing_Strategy.pdf') ?>>Full strategy document (PDF)</a>
            </li>
            <li>
                <a href="https://glasgow.gov.uk/media/10878/Appendix-LHS-2023-2028-Annual-Review-Report-Year-One-2023-2024/pdf/Appendix_LHS_23-28_Annual_Review_Report_-_Year_One_2023_2024.pdf"<?= external_link_attrs('https://glasgow.gov.uk/media/10878/Appendix-LHS-2023-2028-Annual-Review-Report-Year-One-2023-2024/pdf/Appendix_LHS_23-28_Annual_Review_Report_-_Year_One_2023_2024.pdf') ?>>Local Housing Strategy Annual Review 2023–2024 (PDF)</a>
            </li>
        </ul>

        <div class="callout">
            <p><strong>Campaign question to ask in Glasgow:</strong> The strategy commits to published outcomes and annual monitoring. Ask your local councillor or RSL what specific digital inclusion outcomes have been achieved since 2022, and where the data is publicly available.</p>
        </div>

        <h2>How to use this page as an activist</h2>
        <ul>
            <li>Pair official programme links with <strong>local stories</strong> from your community council, tenants' union, or school parent group.</li>
            <li>Ask candidates and officers how voucher pathways are advertised in libraries, GP surgeries, and housing offices—not only online.</li>
            <li>Request updates in accessible formats for residents who are not confident with PDFs or web forms.</li>
        </ul>

        <div class="pull-quote">
            <p>"Pair official programme links with local stories — that combination is what makes a question in a council chamber hard to ignore."</p>
        </div>

        <p class="meta">We aim to refresh links when government URLs change. If you spot a broken link, please tell us via <a href="/contact.php">Contact</a>.</p>
        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div><!-- /page-layout -->
    </div>
</div>

<?php
$ctaHeading = 'Translate policy into local action';
$ctaBody    = 'Join WIRES to connect with others who are asking these questions in their communities.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
