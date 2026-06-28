<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Why home connectivity matters';
$pageDescription = 'Evidence from UK and Scottish sources on everyday reliance on the internet, harms of being under-connected, and who is most affected—not legal advice.';
$currentNav = 'whymatters';

$pageOgImage    = image_asset('card-community.jpg');
$pageOgImageAlt = 'People at a community meeting — representing the human impact of digital exclusion.';

$sidebarRelated = [
    ['href' => '/get-help.php',   'label' => 'Help getting online'],
    ['href' => '/scotland.php',   'label' => 'Scotland policy'],
    ['href' => '/resources.php',  'label' => 'Resources & references'],
    ['href' => '/news.php',       'label' => 'Latest news'],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Why home connectivity matters</h1>
        <p>
            Evidence from UK and Scottish sources on everyday reliance on the internet, the harm of being offline or under-connected, and who is most affected.
            Campaigning context only — always check live official pages before quoting figures.
        </p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">
        <div class="prose">
        <div class="campaign-statement-card" aria-hidden="true">
            <p class="campaign-statement-card__line1">Internet access.</p>
            <p class="campaign-statement-card__line2">A right,<br>not a privilege.</p>
        </div>

        <div class="stat-strip">
            <div class="stat-item">
                <span class="stat-value">76%</span>
                <span class="stat-label">adults use online banking (ONS 2020)</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">87%</span>
                <span class="stat-label">shopped online in the prior 12 months</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">80%</span>
                <span class="stat-label">of over-65 households connected (that survey)</span>
            </div>
        </div>

        <div class="pull-quote">
            <p>"When you can't get online, you can't fully participate. That's not personal failure — it's a policy failure."</p>
            <cite>WIRES campaign position</cite>
        </div>

        <h2>Why people need residential internet today</h2>
        <p>
            Public services, employers, schools, banks, and landlords increasingly assume households can complete tasks online on a connection that is stable enough for forms, video, and uploads.
            Scotland's <strong>Digital Strategy</strong> explicitly ties inclusive digital public services to wider digital inclusion—connectivity is part of that picture, alongside skills and design.
        </p>
        <ul>
            <li>
                <a href="https://www.gov.scot/publications/digital-strategy-scotland-vision-statement/"<?= external_link_attrs('https://www.gov.scot/publications/digital-strategy-scotland-vision-statement/') ?>>Digital strategy for Scotland: vision statement</a>
                (Scottish Government)
            </li>
            <li>
                <a href="https://www.gov.scot/publications/digital-strategy-scotland-sustainable-digital-public-services-delivery-plan-2025-2028/"<?= external_link_attrs('https://www.gov.scot/publications/digital-strategy-scotland-sustainable-digital-public-services-delivery-plan-2025-2028/') ?>>Sustainable digital public services: delivery plan 2025–2028</a>
                (Scottish Government)
            </li>
        </ul>
        <p>
            UK-wide survey evidence illustrates how routine "life admin" has shifted online. The Office for National Statistics (ONS) reported that in <strong>January–February 2020</strong>, <strong>76%</strong> of adults in Great Britain used internet banking (up from <strong>30%</strong> in 2007) and <strong>87%</strong> had shopped online in the last 12 months.
            Those percentages come from the discontinued annual bulletin below—use it for historical context and follow the notice on that page for where similar data now lives.
        </p>
        <p>
            <a href="https://www.ons.gov.uk/peoplepopulationandcommunity/householdcharacteristics/homeinternetandsocialmediausage/bulletins/internetaccesshouseholdsandindividuals/latest"<?= external_link_attrs('https://www.ons.gov.uk/peoplepopulationandcommunity/householdcharacteristics/homeinternetandsocialmediausage/bulletins/internetaccesshouseholdsandindividuals/latest') ?>>Internet access – households and individuals, Great Britain</a> (ONS; last bulletin <strong>2020</strong>, with discontinuation notice dated <strong>May 2023</strong>)
        </p>

        <h2>Harms of being offline or under-connected</h2>
        <p>
            "Under-connected" often means relying on a single expensive mobile bundle, sharing someone else's connection, or losing hours to dropped calls and failed uploads—not a romantic "digital detox."
            Charities that support people through debt and consumer problems regularly describe broadband and mobile costs as pressure points during cost-of-living stress.
        </p>
        <ul>
            <li>
                <a href="https://www.citizensadvice.org.uk/consumer/internet-and-phone/internet-broadband/if-you-cant-afford-your-broadband-bill-or-top-up/"<?= external_link_attrs('https://www.citizensadvice.org.uk/consumer/internet-and-phone/internet-broadband/if-you-cant-afford-your-broadband-bill-or-top-up/') ?>>If you can't afford your broadband bill or top up</a>
                (Citizens Advice, England and Wales—practical guidance, not a substitute for local advice if you need help with a specific situation)
            </li>
            <li>
                <a href="https://www.cas.org.uk/"<?= external_link_attrs('https://www.cas.org.uk/') ?>>Citizens Advice Scotland</a>
                —local bureaux cover consumer and benefits issues where Scotland's rules differ.
            </li>
            <li>
                <a href="https://www.goodthingsfoundation.org/"<?= external_link_attrs('https://www.goodthingsfoundation.org/') ?>>Good Things Foundation</a>
                —UK charity focused on digital inclusion; useful for programme framing and evidence of support needs (always read their primary reports for numbers they publish).
            </li>
        </ul>
        <p>
            Regulator-led research is the right place for <strong>affordability stress</strong>, <strong>quality of service</strong>, and <strong>geographic coverage</strong> at scale. Ofcom publishes <strong>Connected Nations</strong> updates and related consumer research—headline premises and coverage statistics change each release, so we link to the hub rather than freezing a percentage here.
        </p>
        <p>
            <a href="https://www.ofcom.org.uk/research-and-data/multi-sector-research/infrastructure-research/connected-nations"<?= external_link_attrs('https://www.ofcom.org.uk/research-and-data/multi-sector-research/infrastructure-research/connected-nations') ?>>Connected Nations</a> (Ofcom)
            ·
            <a href="https://www.ofcom.org.uk/"<?= external_link_attrs('https://www.ofcom.org.uk/') ?>>Ofcom home</a> (search for affordability and "Technology Tracker" if a link moves)
        </p>

        <h2>Who is disproportionately affected</h2>
        <p>
            Survey data consistently show that internet access and confidence are uneven by age, income, disability, and geography. The same ONS <strong>2020</strong> bulletin noted that internet connections in households with <strong>one adult aged 65 and over</strong> had risen but remained lower than other household types (<strong>80%</strong> with a connection in that group, in that survey period—see tables on the ONS page for full definitions).
        </p>
        <p>
            For <strong>Scotland-specific</strong> household and social statistics at national scale, the Scottish Government's <strong>Scottish Household Survey</strong> is the primary official source—<strong>including internet use where that topic appears in a given year's questionnaire</strong>. It does not replace regulator coverage data (for example Ofcom's maps and metrics). Headline percentages vary by year and question wording.
        </p>
        <p>
            <a href="https://www.gov.scot/collections/scottish-household-survey/"<?= external_link_attrs('https://www.gov.scot/collections/scottish-household-survey/') ?>>Scottish Household Survey</a> (Scottish Government)
        </p>
        <p>
            Ofcom's coverage and quality work is especially relevant where <strong>rural and island</strong> communities face weaker fixed or mobile signals than urban centres—again, use the latest report for maps and metrics rather than second-hand figures.
        </p>

        <h2 id="homelessness">Homelessness, temporary accommodation, and digital exclusion</h2>

        <div class="info-card">
            <div class="info-card__header">
                <h3 class="info-card__heading">People without a fixed address</h3>
                <p class="info-card__sub">A barrier most digital inclusion schemes ignore</p>
            </div>
            <div class="info-card__body">
                <p>Social tariffs, broadband vouchers, and fixed-line contracts all require a permanent home address to apply. For people who are homeless, in temporary accommodation, or moving between hostels and B&amp;Bs, these schemes are simply unavailable—regardless of eligibility on any other ground.</p>
                <p><a class="btn btn-ghost btn-sm" href="/get-help.php">See what help is available &rarr;</a></p>
            </div>
        </div>

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
            Scotland is in a declared housing emergency. Households in temporary accommodation have reached their highest level in the recorded time series going back to 2002,
            and homelessness has continued to rise since the emergency was declared.
            People experiencing homelessness face digital exclusion not just through lack of money,
            but through the design of the systems that are supposed to help them. Without internet access it is harder to:
        </p>
        <ul>
            <li>Apply for housing, benefits, or emergency support online (as more services move to digital-only)</li>
            <li>Keep in contact with support workers, family, and legal representatives</li>
            <li>Search for work or access training</li>
            <li>Access mental health resources or telehealth appointments</li>
            <li>Charge devices—a barrier that is rarely mentioned but often the first problem in practice</li>
        </ul>

        <p>
            Public libraries, day centres, and hostels run by organisations like
            <a href="https://scotland.shelter.org.uk/"<?= external_link_attrs('https://scotland.shelter.org.uk/') ?>>Shelter Scotland</a>
            and the
            <a href="https://www.glasgowcitymission.com/"<?= external_link_attrs('https://www.glasgowcitymission.com/') ?>>Glasgow City Mission</a>
            often provide the only consistent access points for people in this situation.
            The <a href="https://www.goodthingsfoundation.org/our-services/national-databank/"<?= external_link_attrs('https://www.goodthingsfoundation.org/our-services/national-databank/') ?>>National Databank</a>
            (free SIM cards distributed through community organisations) is one of the few schemes that does not require a fixed address—ask at a local foodbank, library, or day centre.
        </p>

        <p>
            If you work with people experiencing homelessness and want to share what digital access looks like in practice in your area, please
            <a href="/contact.php">get in touch</a>. Local evidence is what makes this issue visible in policy discussions.
        </p>

        <ul>
            <li><a href="https://scotland.shelter.org.uk/"<?= external_link_attrs('https://scotland.shelter.org.uk/') ?>>Shelter Scotland</a> — housing rights, emergency advice, and homelessness support</li>
            <li><a href="https://www.crisis.org.uk/"<?= external_link_attrs('https://www.crisis.org.uk/') ?>>Crisis</a> — national charity working to end homelessness; has published on digital access</li>
            <li><a href="https://www.gov.scot/policies/homelessness/"<?= external_link_attrs('https://www.gov.scot/policies/homelessness/') ?>>Scottish Government: homelessness policy</a></li>
        </ul>

        <h2>What official data does—and does not—measure</h2>
        <ul>
            <li>
                <strong>Household surveys</strong> (ONS, Scottish Household Survey, Ofcom consumer trackers) capture <em>people's reported behaviour and equipment</em>. They can miss "hidden" sharing, informal hotspots, or precarious pay-as-you-go use.
            </li>
            <li>
                <strong>Coverage and network statistics</strong> (Ofcom, build programmes) focus on <em>infrastructure availability and performance</em>. Availability is not the same as affordability, digital skills, or trust in online services.
            </li>
            <li>
                <strong>Series change over time.</strong> The ONS notice on its internet-access bulletin (May 2023) points readers toward Ofcom's <strong>Technology Tracker</strong> for overlapping topics—always read the methodology sheet when comparing years.
            </li>
        </ul>
        <p>
            Topic hub for ONS internet-related releases and datasets:
            <a href="https://www.ons.gov.uk/peoplepopulationandcommunity/householdcharacteristics/homeinternetandsocialmediausage"<?= external_link_attrs('https://www.ons.gov.uk/peoplepopulationandcommunity/householdcharacteristics/homeinternetandsocialmediausage') ?>>Home internet and social media usage</a> (ONS).
        </p>

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

        <div class="pull-quote">
            <p>"Availability is not the same as affordability, digital skills, or trust in online services."</p>
            <cite>Ofcom Connected Nations methodology note</cite>
        </div>

        <p class="meta">Figures and programme rules change. If an external link breaks or a series is renamed, tell us via <a href="/contact.php">Contact</a> so we can update this page.</p>
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
