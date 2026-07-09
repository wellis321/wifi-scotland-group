<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Figures & sources — the fact-check log';
$pageDescription = 'Every statistic used across ' . SITE_BRAND . ', with the primary source it comes from and the date it was last checked.';
$currentNav      = 'figures';

$pageOgImage    = image_asset('card-fibre.jpg');
$pageOgImageAlt = 'Fibre-optic cables close-up — representing the evidence behind the campaign.';

$sidebarRelated = [
    ['href' => '/resources.php',      'label' => 'Resources & references'],
    ['href' => '/why-it-matters.php', 'label' => 'Why it matters'],
    ['href' => '/accountability.php', 'label' => 'Who is acting?'],
];

/*
 * One row per figure used somewhere on the site.
 *   claim    – the figure/statistic as stated on the site
 *   used_on  – array of ['label' => page name, 'href' => path]
 *   source   – publisher name
 *   url      – link to the primary source
 *   date     – publication date of the source (not the date we checked it)
 *   note     – any caveat worth flagging
 */
$figureGroups = [
    'Social tariffs & broadband affordability' => [
        [
            'claim'   => '1 in 12 qualifying UK households (≈532,000 of 6.2m) use a social tariff',
            'used_on' => [['label' => 'Home', 'href' => '/'], ['label' => 'Why it matters', 'href' => '/why-it-matters'], ['label' => 'Get help', 'href' => '/get-help'], ['label' => 'Who is acting?', 'href' => '/accountability']],
            'source'  => 'Ofcom — Pricing and consumer engagement 2025',
            'url'     => 'https://www.ofcom.org.uk/phones-and-broadband/bills-and-charges/pricing-and-consumer-engagement',
            'date'    => 'Feb 2026 (survey data Jun 2025)',
            'note'    => '8.6% is the precise figure; "1 in 12" is a fair rounding.',
        ],
        [
            'claim'   => '70% of people on benefits have never heard that social tariffs exist',
            'used_on' => [['label' => 'Home', 'href' => '/'], ['label' => 'Why it matters', 'href' => '/why-it-matters'], ['label' => 'Who is acting?', 'href' => '/accountability']],
            'source'  => 'Ofcom — Pricing and consumer engagement 2025',
            'url'     => 'https://www.ofcom.org.uk/phones-and-broadband/bills-and-charges/pricing-and-consumer-engagement',
            'date'    => 'Feb 2026 (survey data Oct 2025)',
            'note'    => 'Corrected from a previously-published 55% — that number didn\'t match any Ofcom awareness-tracking wave. Ofcom\'s own series has moved 84% → 69% → 53% → 69% → 70% since 2022, so re-check this one every time Ofcom publishes.',
        ],
        [
            'claim'   => 'A social tariff can save an eligible household around £200 a year',
            'used_on' => [['label' => 'Why it matters', 'href' => '/why-it-matters']],
            'source'  => 'Ofcom — Pricing and consumer engagement 2025',
            'url'     => 'https://www.ofcom.org.uk/phones-and-broadband/bills-and-charges/pricing-and-consumer-engagement',
            'date'    => 'Feb 2026',
            'note'    => 'Corrected from a previously-published "£12/month overpay" figure, which was not a real Ofcom statistic.',
        ],
        [
            'claim'   => 'UK Gigabit Broadband Voucher Scheme: up to £3,500 (business) / £1,500 (residential)',
            'used_on' => [['label' => 'Get help', 'href' => '/get-help']],
            'source'  => 'Building Digital UK / GOV.UK',
            'url'     => 'https://www.gov.uk/government/publications/gigabit-broadband-voucher-scheme-information/gigabit-broadband-voucher-scheme-information',
            'date'    => 'Checked Jul 2026',
            'note'    => 'Corrected from a previously-published £4,500 business figure — £4,500 is the combined/group-project cap, not the per-business voucher. These values change periodically; re-check before quoting.',
        ],
    ],
    'Scottish broadband coverage (R100)' => [
        [
            'claim'   => 'R100 is a £697m programme; over 100,000 premises connected',
            'used_on' => [['label' => 'Get help', 'href' => '/get-help']],
            'source'  => 'Scottish Government — Reaching 100%',
            'url'     => 'https://digitalconnectivity.campaign.gov.scot/about-r100',
            'date'    => 'Checked Jul 2026',
            'note'    => 'Premises-connected count moves roughly monthly as Openreach builds — treat any static number as a snapshot, not a live figure.',
        ],
        [
            'claim'   => 'R100 full build completion expected March 2028',
            'used_on' => [['label' => 'Get help', 'href' => '/get-help'], ['label' => 'Beyond broadband', 'href' => '/beyond-broadband']],
            'source'  => 'Scottish Government — Reaching 100%',
            'url'     => 'https://digitalconnectivity.campaign.gov.scot/about-r100',
            'date'    => 'Checked Jul 2026',
            'note'    => 'Has slipped from an original 2021 target before — treat as the current official position, not a guarantee.',
        ],
        [
            'claim'   => '15.1% of Scottish residents do not use fixed broadband at all',
            'used_on' => [['label' => 'Beyond broadband', 'href' => '/beyond-broadband']],
            'source'  => 'Scottish Government analysis of Ofcom Technology Tracker 2024 data',
            'url'     => 'https://www.gov.scot/publications/towards-scottish-minimum-digital-living-standard-interim-report/pages/3/',
            'date'    => '2025',
            'note'    => 'Not a headline number Ofcom itself publishes in prose — it\'s a cross-tab the Scottish Government derived from Ofcom\'s raw data. Cite the gov.scot report, not "Ofcom" alone.',
        ],
    ],
    'Digital skills & who is excluded' => [
        [
            'claim'   => '15% of Scotland\'s adults (roughly 1 in 6) lack foundational digital skills',
            'used_on' => [['label' => 'Beyond broadband', 'href' => '/beyond-broadband'], ['label' => 'Who is acting?', 'href' => '/accountability']],
            'source'  => 'Audit Scotland, Tackling Digital Exclusion (Aug 2024); restated by Scottish Government (2025)',
            'url'     => 'https://audit.scot/uploads/2024-08/nr_240822_tackling_digital_exclusion.pdf',
            'date'    => 'Aug 2024',
            'note'    => 'This is one statistic, not two — earlier drafts of this site cited "15%" and "1 in 6" separately as if from different sources.',
        ],
        [
            'claim'   => '2.8 million people in the UK are entirely offline; 27% are "narrow" internet users',
            'used_on' => [['label' => 'For organisations', 'href' => '/for-organisations']],
            'source'  => 'Ofcom — Digital Adoption and Digital Disadvantage Today',
            'url'     => 'https://www.ofcom.org.uk/internet-based-services/technology/digital-adoption-and-digital-disadvantage-today-what-has-changed-and-what-barriers-remain',
            'date'    => '2024',
            'note'    => 'Corrected from a previously-published "1.6 million offline / 23% struggle" — those numbers don\'t appear on Ofcom\'s page. Down from 13% of the population offline pre-pandemic to 5% now.',
        ],
        [
            'claim'   => 'Around 10 million UK workers lack essential digital skills for work',
            'used_on' => [['label' => 'For organisations', 'href' => '/for-organisations']],
            'source'  => 'Lloyds Bank — Essential Digital Skills 2025',
            'url'     => 'https://www.lloydsbankinggroup.com/assets/pdfs/who-we-are/what-we-do/financial-wellbeing/lloyd-essential-digital-skills-2025.pdf',
            'date'    => '2025',
            'note'    => 'Previously mis-attributed to the same Ofcom page as the offline figures above — it\'s a separate Lloyds dataset.',
        ],
        [
            'claim'   => '8 million people lack the skills to make meaningful use of online services; 77% of the digitally excluded are over 65; 69% live with a disability or impairment',
            'used_on' => [['label' => 'Digital health', 'href' => '/digital-health'], ['label' => 'Beyond broadband', 'href' => '/beyond-broadband']],
            'source'  => 'Good Things Foundation — Digital Nation',
            'url'     => 'https://www.goodthingsfoundation.org/policy-and-research/research-and-evidence/research-2024/digital-nation',
            'date'    => '2025',
            'note'    => 'Corrected the source link — it previously pointed to an NHS Alliance page that doesn\'t contain these figures.',
        ],
    ],
    'Health & wellbeing evidence' => [
        [
            'claim'   => '"People with poor health and living with a disability, older people, migrants and those with a lower socioeconomic status are struggling the most in accessing these tools."',
            'used_on' => [['label' => 'Digital health', 'href' => '/digital-health']],
            'source'  => 'WHO Europe',
            'url'     => 'https://www.who.int/europe/news/item/17-03-2026-digital-health-equity-gaps-remain--but-solutions-are-becoming-clearer--new-report-shows',
            'date'    => '17 Mar 2026',
            'note'    => 'Verified genuine — corrected one word ("these tools", not "digital health tools") to match the exact published wording.',
        ],
        [
            'claim'   => 'WHO resolution WHA71.7 (2018) urges member states to make digital health equitable',
            'used_on' => [['label' => 'Digital health', 'href' => '/digital-health']],
            'source'  => 'World Health Organization',
            'url'     => 'https://www.who.int/publications/i/item/10665-279505',
            'date'    => '26 May 2018',
            'note'    => 'The resolution is broader than digital-health equity alone (it covers digital health strategy generally) — our framing is a fair summary, not a direct quote.',
        ],
        [
            'claim'   => '2025 study of 87,256 observations across China, the US and the UK links digital exclusion to loneliness in older adults',
            'used_on' => [['label' => 'Digital health', 'href' => '/digital-health']],
            'source'  => 'BMC Geriatrics',
            'url'     => 'https://link.springer.com/article/10.1186/s12877-025-06337-2',
            'date'    => '26 Aug 2025',
            'note'    => 'Confirmed: 39,190 participants, 87,256 observations, all three countries, consistent association found.',
        ],
        [
            'claim'   => 'Social isolation carries mortality risk comparable to smoking and obesity; ~1 in 4 community-dwelling Americans 65+ are socially isolated',
            'used_on' => [['label' => 'Digital health', 'href' => '/digital-health']],
            'source'  => 'National Academies of Sciences, Engineering, and Medicine',
            'url'     => 'https://nap.nationalacademies.org/read/25663',
            'date'    => '2020',
            'note'    => 'This is a general isolation/mortality finding, not specific to digital exclusion — WIRES draws that connection, NASEM does not make it directly.',
        ],
        [
            'claim'   => 'US counties with higher digital exclusion had higher COVID-19 case rates, death rates, and lower vaccination take-up',
            'used_on' => [['label' => 'Digital health', 'href' => '/digital-health']],
            'source'  => 'PMC / National Institutes of Health',
            'url'     => 'https://pmc.ncbi.nlm.nih.gov/articles/PMC9283607/',
            'date'    => '2022',
            'note'    => 'Confirmed: counties with >40% disconnection had ~3x the death rate of counties with 0–10% disconnection. US-only study; association, not proven causation.',
        ],
    ],
    'AI and digital-first services' => [
        [
            'claim'   => '"Digitally excluded people would be poorly represented in AI training datasets, amplifying their marginalisation."',
            'used_on' => [['label' => 'For organisations', 'href' => '/for-organisations']],
            'source'  => 'House of Lords Communications and Digital Committee, Digital Exclusion (HL Paper 219)',
            'url'     => 'https://committees.parliament.uk/publications/40969/documents/199591/default/',
            'date'    => '29 Jun 2023',
            'note'    => 'Confirmed genuine, close paraphrase of the committee\'s findings.',
        ],
        [
            'claim'   => '"A growing class of people who cannot reliably access, use or benefit from digital services essential to modern life."',
            'used_on' => [['label' => 'For organisations', 'href' => '/for-organisations']],
            'source'  => 'THINK Digital Partners',
            'url'     => 'https://www.thinkdigitalpartners.com/news/2026/02/24/digital-inclusion-in-the-age-of-ai/',
            'date'    => '24 Feb 2026',
            'note'    => 'Verified genuine and near-verbatim.',
        ],
        [
            'claim'   => 'WCAG 2.2 AA is the current accessibility standard for UK public sector websites',
            'used_on' => [['label' => 'For organisations', 'href' => '/for-organisations']],
            'source'  => 'GOV.UK accessibility guidance / Public Sector Bodies Accessibility Regulations 2018',
            'url'     => 'https://www.gov.uk/guidance/accessibility-requirements-for-public-sector-websites-and-apps',
            'date'    => 'Checked Jul 2026',
            'note'    => 'The 2018 regulations themselves just say "WCAG, as amended from time to time" — 2.2 AA is the version current GOV.UK guidance names.',
        ],
    ],
    'Glasgow, Scottish councils & Audit Scotland' => [
        [
            'claim'   => '65% of households in Glasgow\'s social rented housing do not use home broadband; 32 RSLs, covering 75% of social housing stock, back the strategy',
            'used_on' => [['label' => 'Scotland policy', 'href' => '/scotland'], ['label' => 'Who is acting?', 'href' => '/accountability']],
            'source'  => 'Glasgow City Council — Digital Housing Strategy 2022–2028',
            'url'     => 'https://glasgow.gov.uk/media/1717/Glasgow-s-Digital-Housing-Strategy/pdf/Glasgows_Digital_Housing_Strategy.pdf',
            'date'    => 'Nov 2022',
            'note'    => 'Confirmed in the council\'s own strategy document.',
        ],
        [
            'claim'   => 'Connecting Scotland supported more than 60,000 households during the pandemic',
            'used_on' => [['label' => 'Why WIRES exists', 'href' => '/landscape']],
            'source'  => 'Scottish Government — Connecting Scotland: phase 2 evaluation',
            'url'     => 'https://www.gov.scot/publications/connecting-scotland-phase-2-evaluation/',
            'date'    => 'Nov 2022',
            'note'    => 'Corrected from "61,000 people" — the official evaluation counts households, not individuals, and the actual number of people reached is higher.',
        ],
    ],
    'Homelessness & housing emergency' => [
        [
            'claim'   => 'A household becomes homeless in Scotland every 15 minutes; 39 children become homeless every day; households in temporary accommodation are at their highest level since the series began in 2002',
            'used_on' => [['label' => 'Why it matters', 'href' => '/why-it-matters']],
            'source'  => 'Scottish Government homelessness statistics, analysed by Shelter Scotland',
            'url'     => 'https://scotland.shelter.org.uk/housing_policy/homelessness_in_scotland/',
            'date'    => 'Update to 30 Sep 2025',
            'note'    => 'These are live, twice-yearly updated figures — re-check against the current Scottish Government release before quoting in anything formal.',
        ],
        [
            'claim'   => 'Scottish Parliament declared a housing emergency on 15 May 2024',
            'used_on' => [['label' => 'Why it matters', 'href' => '/why-it-matters']],
            'source'  => 'Scottish Government',
            'url'     => 'https://www.gov.scot/publications/tackling-scotlands-housing-emergency/',
            'date'    => '15 May 2024',
            'note'    => 'Confirmed.',
        ],
    ],
    'Historical & background statistics' => [
        [
            'claim'   => 'In Jan–Feb 2020, 76% of GB adults used internet banking (up from 30% in 2007); 87% had shopped online in the prior 12 months; 80% of single-adult 65+ households had a connection',
            'used_on' => [['label' => 'Why it matters', 'href' => '/why-it-matters']],
            'source'  => 'ONS — Internet access: households and individuals',
            'url'     => 'https://www.ons.gov.uk/peoplepopulationandcommunity/householdcharacteristics/homeinternetandsocialmediausage/bulletins/internetaccesshouseholdsandindividuals/latest',
            'date'    => '2020 (discontinued May 2023)',
            'note'    => 'This ONS series was discontinued; ONS points readers to Ofcom\'s Technology Tracker instead. These are now historical, not current, figures — the site frames them that way.',
        ],
        [
            'claim'   => 'Scottish Gaelic\'s digital text corpus is around 150 million words, versus 4 billion for Basque',
            'used_on' => [['label' => 'Beyond broadband', 'href' => '/beyond-broadband']],
            'source'  => 'CivTech Scotland',
            'url'     => 'https://www.civtech.scot/civtech-11-challenge-2-data-sparsity-gaelic-language',
            'date'    => 'Checked Jul 2026',
            'note'    => 'CivTech states these figures but doesn\'t footnote its own source — treat as a secondary citation, not a primary dataset.',
        ],
    ],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Figures &amp; sources</h1>
        <p>Every statistic used across <?= e(SITE_BRAND) ?>, with the primary source behind it. We checked every figure on this page against its original publication in July 2026 — corrections made during that check are noted inline. Numbers move; check the linked source before quoting anything in a briefing, council question, or press enquiry.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">
        <div class="prose">

            <div class="callout">
                <p class="callout__eyebrow">How to use this page</p>
                <p>Each row shows a figure as it appears on the site, which page(s) use it, the primary source, and a caveat if one exists. If you're re-checking the site's accuracy, this is the list to work through — set a reminder to review it every few months, since regulator and government figures move on their own schedule regardless of ours.</p>
            </div>

            <?php foreach ($figureGroups as $groupName => $rows): ?>
                <h2><?= e($groupName) ?></h2>
                <div class="figure-log">
                    <?php foreach ($rows as $row): ?>
                        <div class="figure-log__item">
                            <p class="figure-log__claim"><?= e($row['claim']) ?></p>
                            <p class="figure-log__meta">
                                <span class="figure-log__source">
                                    <a href="<?= e($row['url']) ?>"<?= external_link_attrs($row['url']) ?>><?= e($row['source']) ?></a>
                                </span>
                                <span class="figure-log__date"><?= e($row['date']) ?></span>
                            </p>
                            <p class="figure-log__used-on">
                                Used on:
                                <?php foreach ($row['used_on'] as $i => $page): ?><?= $i > 0 ? ' · ' : ' ' ?><a href="<?= e($page['href']) ?>"><?= e($page['label']) ?></a><?php endforeach; ?>
                            </p>
                            <?php if (!empty($row['note'])): ?>
                                <p class="figure-log__note"><?= e($row['note']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <div class="info-card" style="margin-top:2rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Spot something wrong?</h2>
                    <p class="info-card__sub">Tell us and we'll fix it</p>
                </div>
                <div class="info-card__body">
                    <p>If a figure here is out of date, a link is broken, or you think we've misread a source, <a href="/contact.php">get in touch</a>. Getting this right matters more than any individual number looking impressive.</p>
                </div>
            </div>

            <p class="meta">This page does not duplicate every link in <a href="/resources.php">Resources &amp; references</a> — that page is the general reading list; this one traces specific figures back to specific sources.</p>

        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div><!-- /page-layout -->
    </div>
</div>

<?php
$ctaHeading = 'Something missing?';
$ctaBody    = 'If you know of a source we should add or a figure that needs updating, tell us via the contact form.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
