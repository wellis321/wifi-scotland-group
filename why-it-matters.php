<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Why home connectivity matters';
$pageDescription = 'Evidence from UK and Scottish sources on everyday reliance on the internet, harms of being under-connected, and who is most affected—not legal advice.';
$currentNav = 'whymatters';

$pageOgImage = image_asset('card-fibre.jpg');
$pageOgImageAlt = 'Fibre-optic cables in a patch panel—symbolising the physical layer behind home broadband.';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Why home connectivity matters</h1>
        <p>
            Most people experience <strong>fixed broadband</strong> at home through <strong>Wi‑Fi</strong>—a wireless link inside the building—not as a public right-of-way through streets.
            This page summarises what credible UK and Scottish sources say about <em>why</em> reliable residential internet matters, what goes wrong when access is missing or too thin, and who bears the brunt.
            It is campaigning context, <strong>not legal advice</strong>; always check live official pages before quoting figures in casework.
        </p>
    </div>
</header>

<div class="section">
    <div class="wrap prose">
        <figure class="page-lede">
            <img src="<?= e(image_asset('card-fibre.jpg')) ?>" width="1200" height="800" alt="Fibre-optic cables in a patch panel." decoding="async" loading="lazy">
            <figcaption>Policy debates often mix up the “pipe” into the home, the router, and the Wi‑Fi your devices use—we keep that distinction clear when we talk about rights and roll-out.</figcaption>
        </figure>

        <h2>Why people need residential internet today</h2>
        <p>
            Public services, employers, schools, banks, and landlords increasingly assume households can complete tasks online on a connection that is stable enough for forms, video, and uploads.
            Scotland’s <strong>Digital Strategy</strong> explicitly ties inclusive digital public services to wider digital inclusion—connectivity is part of that picture, alongside skills and design.
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
            UK-wide survey evidence illustrates how routine “life admin” has shifted online. The Office for National Statistics (ONS) reported that in <strong>January–February 2020</strong>, <strong>76%</strong> of adults in Great Britain used internet banking (up from <strong>30%</strong> in 2007) and <strong>87%</strong> had shopped online in the last 12 months.
            Those percentages come from the discontinued annual bulletin below—use it for historical context and follow the notice on that page for where similar data now lives.
        </p>
        <p>
            <a href="https://www.ons.gov.uk/peoplepopulationandcommunity/householdcharacteristics/homeinternetandsocialmediausage/bulletins/internetaccesshouseholdsandindividuals/latest"<?= external_link_attrs('https://www.ons.gov.uk/peoplepopulationandcommunity/householdcharacteristics/homeinternetandsocialmediausage/bulletins/internetaccesshouseholdsandindividuals/latest') ?>>Internet access – households and individuals, Great Britain</a> (ONS; last bulletin <strong>2020</strong>, with discontinuation notice dated <strong>May 2023</strong>)
        </p>

        <h2>Harms of being offline or under-connected</h2>
        <p>
            “Under-connected” often means relying on a single expensive mobile bundle, sharing someone else’s connection, or losing hours to dropped calls and failed uploads—not a romantic “digital detox.”
            Charities that support people through debt and consumer problems regularly describe broadband and mobile costs as pressure points during cost-of-living stress.
        </p>
        <ul>
            <li>
                <a href="https://www.citizensadvice.org.uk/consumer/internet-and-phone/internet-broadband/if-you-cant-afford-your-broadband-bill-or-top-up/"<?= external_link_attrs('https://www.citizensadvice.org.uk/consumer/internet-and-phone/internet-broadband/if-you-cant-afford-your-broadband-bill-or-top-up/') ?>>If you can’t afford your broadband bill or top up</a>
                (Citizens Advice, England and Wales—practical guidance, not a substitute for local advice if you need casework)
            </li>
            <li>
                <a href="https://www.cas.org.uk/"<?= external_link_attrs('https://www.cas.org.uk/') ?>>Citizens Advice Scotland</a>
                —local bureaux cover consumer and benefits issues where Scotland’s rules differ.
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
            <a href="https://www.ofcom.org.uk/"<?= external_link_attrs('https://www.ofcom.org.uk/') ?>>Ofcom home</a> (search for affordability and “Technology Tracker” if a link moves)
        </p>

        <h2>Who is disproportionately affected</h2>
        <p>
            Survey data consistently show that internet access and confidence are uneven by age, income, disability, and geography. The same ONS <strong>2020</strong> bulletin noted that internet connections in households with <strong>one adult aged 65 and over</strong> had risen but remained lower than other household types (<strong>80%</strong> with a connection in that group, in that survey period—see tables on the ONS page for full definitions).
        </p>
        <p>
            For <strong>Scotland-specific</strong> household and social statistics at national scale, the Scottish Government’s <strong>Scottish Household Survey</strong> is the primary official source—<strong>including internet use where that topic appears in a given year’s questionnaire</strong>. It does not replace regulator coverage data (for example Ofcom’s maps and metrics). Headline percentages vary by year and question wording.
        </p>
        <p>
            <a href="https://www.gov.scot/collections/scottish-household-survey/"<?= external_link_attrs('https://www.gov.scot/collections/scottish-household-survey/') ?>>Scottish Household Survey</a> (Scottish Government)
        </p>
        <p>
            Ofcom’s coverage and quality work is especially relevant where <strong>rural and island</strong> communities face weaker fixed or mobile signals than urban centres—again, use the latest report for maps and metrics rather than second-hand figures.
        </p>

        <h2>What official data does—and does not—measure</h2>
        <ul>
            <li>
                <strong>Household surveys</strong> (ONS, Scottish Household Survey, Ofcom consumer trackers) capture <em>people’s reported behaviour and equipment</em>. They can miss “hidden” sharing, informal hotspots, or precarious pay-as-you-go use.
            </li>
            <li>
                <strong>Coverage and network statistics</strong> (Ofcom, build programmes) focus on <em>infrastructure availability and performance</em>. Availability is not the same as affordability, digital skills, or trust in online services.
            </li>
            <li>
                <strong>Series change over time.</strong> The ONS notice on its internet-access bulletin (May 2023) points readers toward Ofcom’s <strong>Technology Tracker</strong> for overlapping topics—always read the methodology sheet when comparing years.
            </li>
        </ul>
        <p>
            Topic hub for ONS internet-related releases and datasets:
            <a href="https://www.ons.gov.uk/peoplepopulationandcommunity/householdcharacteristics/homeinternetandsocialmediausage"<?= external_link_attrs('https://www.ons.gov.uk/peoplepopulationandcommunity/householdcharacteristics/homeinternetandsocialmediausage') ?>>Home internet and social media usage</a> (ONS).
        </p>

        <div class="callout">
            <p><strong>Next steps on this site</strong></p>
            <ul>
                <li><a href="/scotland.php">Scotland</a> — programmes, strategy links, and how to read official connectivity pages.</li>
                <li><a href="/get-involved.php">Get involved</a> — ideas for turning evidence into local questions and solidarity.</li>
                <li><a href="/resources.php">Resources</a> — bookmark list of primary sources we cite elsewhere.</li>
            </ul>
        </div>

        <p class="meta">Figures and programme rules change. If an external link breaks or a series is renamed, tell us via <a href="/contact.php">Contact</a> so we can update this page.</p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
