<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Why home connectivity matters';
$pageDescription = 'Evidence from UK and Scottish sources on everyday reliance on the internet, harms of being under-connected, and who is most affected—not legal advice.';
$currentNav = 'whymatters';

$pageOgImage    = image_asset('card-community.jpg');
$pageOgImageAlt = 'People at a community meeting — representing the human impact of digital exclusion.';

$sidebarRelated = [
    ['href' => '/digital-health',  'label' => 'Digital exclusion & health'],
    ['href' => '/beyond-broadband','label' => 'Beyond broadband'],
    ['href' => '/landscape',       'label' => 'Why WIRES exists'],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Why home connectivity matters</h1>
        <p>
            Evidence from UK and Scottish sources on everyday reliance on the internet, the harm of being offline or under-connected, and who is most affected.
        </p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">
        <div class="prose">
        <p class="meta">A note before you read: figures on this page come from official UK and Scottish sources, but surveys run on different years and different questions. Where a number is older or the series has changed, we say so once, briefly — we don't want caveats getting in the way of the point.</p>

        <div class="campaign-statement-card" aria-hidden="true">
            <p class="campaign-statement-card__line1">Internet access.</p>
            <p class="campaign-statement-card__line2">A right,<br>not a privilege.</p>
        </div>

        <h2>Internet access is a right, not a privilege</h2>
        <p>
            Life admin has moved online, whether or not your connection is ready for it. Public services, employers, schools, banks, and landlords now assume you can bank, apply, and upload from home — on a connection stable enough for forms, video calls, and file uploads.
        </p>
        <p>Here's how much everyday life already assumes you're online:</p>

        <div class="stat-strip">
            <div class="stat-item">
                <span class="stat-value">76%</span>
                <span class="stat-label">of adults used internet banking in 2020 — up from just 30% in 2007</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">87%</span>
                <span class="stat-label">had shopped online in the past year</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">80%</span>
                <span class="stat-label">of households with someone over 65 were connected</span>
            </div>
        </div>
        <p class="meta">Source: <a href="https://www.ons.gov.uk/peoplepopulationandcommunity/householdcharacteristics/homeinternetandsocialmediausage/bulletins/internetaccesshouseholdsandindividuals/latest"<?= external_link_attrs('https://www.ons.gov.uk/peoplepopulationandcommunity/householdcharacteristics/homeinternetandsocialmediausage/bulletins/internetaccesshouseholdsandindividuals/latest') ?>>ONS, Internet access — households and individuals, Great Britain</a>. This was the last bulletin before the series was discontinued in 2023, so treat these as a snapshot of a fast-moving trend rather than today's numbers.</p>

        <p>
            Scotland's own <a href="https://www.gov.scot/publications/digital-strategy-scotland-vision-statement/"<?= external_link_attrs('https://www.gov.scot/publications/digital-strategy-scotland-vision-statement/') ?>>Digital Strategy</a> recognises this: it treats connectivity as one part — alongside skills and good design — of making public services genuinely inclusive. The <a href="https://www.gov.scot/publications/digital-strategy-scotland-sustainable-digital-public-services-delivery-plan-2025-2028/"<?= external_link_attrs('https://www.gov.scot/publications/digital-strategy-scotland-sustainable-digital-public-services-delivery-plan-2025-2028/') ?>>2025–2028 delivery plan</a> sets out how that's meant to happen in practice.
        </p>

        <div class="pull-quote">
            <p>"When you can't get online, you can't fully participate. That's not personal failure — it's a policy failure."</p>
            <cite>WIRES campaign position</cite>
        </div>

        <h2>What "under-connected" actually looks like</h2>
        <p>
            It's not a lifestyle choice or a "digital detox." Being under-connected usually means one of a few things: relying on a single expensive mobile bundle, sharing someone else's connection, or losing hours to dropped calls and failed uploads.
        </p>
        <p>Cost is the biggest barrier, and it's a solvable one:</p>

        <div class="stat-strip">
            <div class="stat-item">
                <span class="stat-value">1 in 12</span>
                <span class="stat-label">households entitled to a social tariff are actually using one — 532,000 of 6.2 million eligible</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">70%</span>
                <span class="stat-label">of people on benefits have never heard that social tariffs exist</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">£200</span>
                <span class="stat-label">a year an eligible household could typically save by switching</span>
            </div>
        </div>
        <p class="meta">Source: <a href="https://www.ofcom.org.uk/phones-and-broadband/bills-and-charges/pricing-and-consumer-engagement"<?= external_link_attrs('https://www.ofcom.org.uk/phones-and-broadband/bills-and-charges/pricing-and-consumer-engagement') ?>>Ofcom, Pricing and consumer engagement 2025</a>, published February 2026.</p>

        <p>
            That gap between entitlement and uptake is itself a policy failure. A scheme nobody knows exists is a scheme that doesn't work.
        </p>

        <p>
            Charities that support people through debt and consumer problems see this pressure constantly. <a href="https://www.citizensadvice.org.uk/consumer/internet-and-phone/internet-broadband/if-you-cant-afford-your-broadband-bill-or-top-up/"<?= external_link_attrs('https://www.citizensadvice.org.uk/consumer/internet-and-phone/internet-broadband/if-you-cant-afford-your-broadband-bill-or-top-up/') ?>>Citizens Advice</a> publishes practical guidance for England and Wales, <a href="https://www.cas.org.uk/"<?= external_link_attrs('https://www.cas.org.uk/') ?>>Citizens Advice Scotland</a> covers the issues where Scotland's rules differ, and the <a href="https://www.goodthingsfoundation.org/"<?= external_link_attrs('https://www.goodthingsfoundation.org/') ?>>Good Things Foundation</a> tracks digital inclusion needs UK-wide.
        </p>

        <p>
            For the bigger picture — coverage, quality of service, and affordability at national scale — Ofcom's <a href="https://www.ofcom.org.uk/research-and-data/multi-sector-research/infrastructure-research/connected-nations"<?= external_link_attrs('https://www.ofcom.org.uk/research-and-data/multi-sector-research/infrastructure-research/connected-nations') ?>>Connected Nations</a> research is the authoritative source. Figures there update each release, so we link to the hub rather than quoting a number that will soon be out of date.
        </p>

        <h2>Who is most affected</h2>
        <p>
            Access and confidence online are uneven — by age, income, disability, and geography. The same 2020 ONS data that showed 80% of over-65 households connected also showed that figure was rising but still lagging behind other household types.
        </p>
        <p>
            For Scotland-specific numbers, the <a href="https://www.gov.scot/collections/scottish-household-survey/"<?= external_link_attrs('https://www.gov.scot/collections/scottish-household-survey/') ?>>Scottish Household Survey</a> is the primary official source, including internet use in years where it's part of the questionnaire. It sits alongside — not in place of — Ofcom's coverage data.
        </p>
        <p>
            Rural and island communities are a clear case in point: Ofcom's coverage and quality research consistently shows weaker fixed and mobile signal there than in urban centres. For current maps and figures, <a href="https://www.ofcom.org.uk/research-and-data/multi-sector-research/infrastructure-research/connected-nations"<?= external_link_attrs('https://www.ofcom.org.uk/research-and-data/multi-sector-research/infrastructure-research/connected-nations') ?>>Connected Nations</a> is the place to look.
        </p>

        <h2 id="homelessness">People without a fixed address are locked out by design</h2>

        <div class="info-card">
            <div class="info-card__header">
                <h3 class="info-card__heading">People without a fixed address</h3>
                <p class="info-card__sub">A barrier most digital inclusion schemes ignore</p>
            </div>
            <div class="info-card__body">
                <p>Social tariffs, broadband vouchers, fixed-line contracts — nearly every scheme built to help with connectivity costs requires a permanent home address to apply. For people who are homeless, in temporary accommodation, or moving between hostels and B&amp;Bs, that makes these schemes unavailable, no matter how clearly they'd otherwise qualify.</p>
                <p><a class="btn btn-ghost btn-sm" href="/get-help.php">See what help is available &rarr;</a></p>
            </div>
        </div>

        <p>This isn't a small edge case. In Scotland:</p>

        <div class="stat-strip">
            <div class="stat-item">
                <span class="stat-value">15<span style="font-size:1.25rem;font-weight:700"> min</span></span>
                <span class="stat-label">A household becomes homeless in Scotland</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">39</span>
                <span class="stat-label">Children become homeless in Scotland every day</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">2024</span>
                <span class="stat-label">Scotland declared a housing emergency</span>
            </div>
        </div>

        <p>
            The Scottish Parliament declared a housing emergency on <strong>15 May 2024</strong>. Households in temporary accommodation are at their highest level since records began in 2002,
            and homelessness has kept rising since the emergency was declared.
        </p>
        <p class="meta">Sources: <a href="https://www.gov.scot/publications/tackling-scotlands-housing-emergency/"<?= external_link_attrs('https://www.gov.scot/publications/tackling-scotlands-housing-emergency/') ?>>Scottish Government — Tackling Scotland's Housing Emergency</a> · <a href="https://www.gov.scot/publications/homelessness-in-scotland-update-to-30-september-2025/"<?= external_link_attrs('https://www.gov.scot/publications/homelessness-in-scotland-update-to-30-september-2025/') ?>>Homelessness in Scotland: update to 30 September 2025</a> · <a href="https://scotland.shelter.org.uk/housing_policy/homelessness_in_scotland/"<?= external_link_attrs('https://scotland.shelter.org.uk/housing_policy/homelessness_in_scotland/') ?>>Shelter Scotland analysis</a>. These are updated twice yearly — figures here reflect the most recent release at time of writing.</p>

        <p>
            Without internet access, it's harder to do the things that get someone back on their feet:
        </p>
        <ul>
            <li>Apply for housing, benefits, or emergency support — as more of these move to digital-only</li>
            <li>Stay in contact with support workers, family, and legal representatives</li>
            <li>Search for work or access training</li>
            <li>Reach mental health support or telehealth appointments</li>
            <li>Simply charge a device — rarely mentioned, but often the first problem people hit</li>
        </ul>

        <p>This isn't just about money. It's about systems that were designed assuming everyone has a front door and a plug socket.</p>

        <p>
            Some organisations fill the gap:
            <a href="https://scotland.shelter.org.uk/"<?= external_link_attrs('https://scotland.shelter.org.uk/') ?>>Shelter Scotland</a>,
            <a href="https://www.glasgowcitymission.com/"<?= external_link_attrs('https://www.glasgowcitymission.com/') ?>>Glasgow City Mission</a>,
            and <a href="https://www.crisis.org.uk/"<?= external_link_attrs('https://www.crisis.org.uk/') ?>>Crisis</a>
            run libraries, day centres, and hostels that are often the only consistent access point people have.
            The <a href="https://www.goodthingsfoundation.org/our-services/national-databank/"<?= external_link_attrs('https://www.goodthingsfoundation.org/our-services/national-databank/') ?>>National Databank</a>
            — free SIM cards distributed through community organisations — is one of the few schemes that doesn't require a fixed address; ask at a local foodbank, library, or day centre.
            The Scottish Government's <a href="https://www.gov.scot/policies/homelessness/"<?= external_link_attrs('https://www.gov.scot/policies/homelessness/') ?>>homelessness policy pages</a> set out the wider statutory picture.
        </p>

        <p>
            <strong>If you work with people experiencing homelessness and can tell us what digital access looks like in practice in your area, <a href="/contact.php">get in touch</a>.</strong> Local evidence is what makes this issue visible in policy discussions.
        </p>

        <h2>Want to go deeper?</h2>
        <div class="card-grid cols-2" style="margin:0 0 2.5rem">
            <a class="icon-card" href="/digital-health" style="text-decoration:none">
                <div class="icon-card-body">
                    <span class="pill">Research</span>
                    <h2>Digital exclusion and health</h2>
                    <p>WHO evidence, COVID-19 research, NHS data, and studies on loneliness and isolation — the documented health cost of being offline.</p>
                    <p style="margin-top:auto;padding-top:0.75rem"><span class="btn btn-primary btn-sm">Read the evidence &rarr;</span></p>
                </div>
            </a>
            <a class="icon-card" href="/beyond-broadband" style="text-decoration:none">
                <div class="icon-card-body">
                    <span class="pill">Policy</span>
                    <h2>Beyond broadband</h2>
                    <p>Devices, outdated software, authentication barriers, digital skills, and language — the rest of the picture that connectivity alone can't fix.</p>
                    <p style="margin-top:auto;padding-top:0.75rem"><span class="btn btn-primary btn-sm">Read more &rarr;</span></p>
                </div>
            </a>
        </div>

        <h2>A note on the data behind this page</h2>
        <p>Official statistics are essential, but each source measures something slightly different:</p>
        <ul>
            <li>
                <strong>Household surveys</strong> (ONS, the Scottish Household Survey, Ofcom's consumer trackers) capture what people report about their own behaviour and equipment. They can miss informal sharing, hidden hotspots, or precarious pay-as-you-go use.
            </li>
            <li>
                <strong>Coverage and network statistics</strong> (Ofcom, national build programmes) measure infrastructure availability and performance — which isn't the same as affordability, digital skills, or trust in online services.
            </li>
            <li>
                <strong>Series change over time.</strong> ONS retired its internet-access bulletin in 2023 and now points readers to Ofcom's Technology Tracker for overlapping topics. If you're comparing figures across years, check the methodology notes first — see the <a href="https://www.ons.gov.uk/peoplepopulationandcommunity/householdcharacteristics/homeinternetandsocialmediausage"<?= external_link_attrs('https://www.ons.gov.uk/peoplepopulationandcommunity/householdcharacteristics/homeinternetandsocialmediausage') ?>>ONS topic hub</a> for the underlying releases.
            </li>
        </ul>

        <div class="pull-quote">
            <p>"Availability is not the same as affordability, digital skills, or trust in online services."</p>
            <cite>Ofcom, Connected Nations methodology note</cite>
        </div>

        <p class="meta">Figures and programme rules change. If a link breaks or a series gets renamed, <a href="/contact.php">tell us</a> so we can fix it.</p>

        <div class="info-card">
            <div class="info-card__header">
                <h3 class="info-card__heading">Where to go next</h3>
                <p class="info-card__sub">More on this site</p>
            </div>
            <div class="info-card__body">
                <ul class="sidebar-nav" style="margin:0">
                    <li><a href="/scotland.php">Scotland policy &amp; programmes</a></li>
                    <li><a href="/get-involved.php">Get involved — turn evidence into local questions</a></li>
                    <li><a href="/resources.php">Resources — primary sources we cite</a></li>
                    <li><a href="/get-help.php">Help getting online — practical schemes</a></li>
                </ul>
            </div>
        </div>
        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div><!-- /page-layout -->
    </div>
</div>

<?php
$ctaHeading = 'Turn evidence into action';
$ctaBody    = 'Join WIRES to hear about consultations, events, and ways to raise these issues locally.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
