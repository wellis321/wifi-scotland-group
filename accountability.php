<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Who is acting on digital exclusion in Scotland?';
$pageDescription = 'A tracker of which Scottish councils have a published digital inclusion strategy, which bodies have a mandate to act and what they have delivered, and where accountability is missing.';
$currentNav      = 'accountability';

$pageOgImage    = image_asset('scotland-landscape.jpg');
$pageOgImageAlt = 'Scotland — representing the councils and bodies that should be tackling digital exclusion.';

/*
 * Status values:
 *   'strategy'  — Published standalone digital inclusion/housing/connectivity strategy
 *   'plan'      — Referenced in an existing plan (LHS, corporate plan, etc.) but not standalone
 *   'none'      — Searched; no visible strategy or plan found
 *   'unknown'   — Not yet verified — help us find out
 *
 * Last checked: date string shown to visitors
 */
$councils = [
    ['name' => 'Aberdeen City',         'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Aberdeenshire',         'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Angus',                 'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Argyll and Bute',       'status' => 'plan',
     'note' => 'GigaPlus Argyll: community-owned infrastructure serving Colonsay, Mull, Iona, Jura and other Inner Hebrides islands, developed with Community Broadband Scotland funding. Infrastructure-focused — no standalone digital exclusion strategy found.',
     'url' => 'https://www.hie.co.uk/',
     'checked' => 'June 2026'],
    ['name' => 'Clackmannanshire',      'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Dumfries and Galloway', 'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Dundee City',           'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'East Ayrshire',         'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'East Dunbartonshire',   'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'East Lothian',          'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'East Renfrewshire',     'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'City of Edinburgh',     'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Falkirk',               'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Fife',                  'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Glasgow City',          'status' => 'strategy',
     'note' => 'Scotland\'s first Digital Housing Strategy 2022–2028. Evidence base found 65% of social rented households do not use home broadband. Backed by 32 RSLs covering 75% of stock. No standalone evaluation published yet.',
     'url' => 'https://www.glasgow.gov.uk/article/2692/Glasgow-s-Digital-Housing-Strategy-to-improve-housing-services-and-tackle-digital-exclusion',
     'checked' => 'June 2026'],
    ['name' => 'Highland',              'status' => 'plan',
     'note' => 'HIE and Community Broadband Scotland have supported pilots in the area including Applecross. Note: Highland Community Broadband (Ullapool) closed April 2026 after nine years due to rising costs — a significant loss. No standalone council digital inclusion strategy identified.',
     'url' => 'https://www.ispreview.co.uk/index.php/2026/01/wireless-isp-highland-community-broadband-set-to-close-in-april-2026.html',
     'checked' => 'June 2026'],
    ['name' => 'Inverclyde',            'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Midlothian',            'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Moray',                 'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Na h-Eileanan Siar',    'status' => 'unknown', 'note' => 'Western Isles — community broadband models exist via HIE.', 'url' => '', 'checked' => ''],
    ['name' => 'North Ayrshire',        'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'North Lanarkshire',     'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Orkney Islands',        'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Perth and Kinross',     'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Renfrewshire',          'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Scottish Borders',      'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Shetland Islands',      'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'South Ayrshire',        'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'South Lanarkshire',     'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'Stirling',              'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'West Dunbartonshire',   'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
    ['name' => 'West Lothian',          'status' => 'unknown', 'note' => '',  'url' => '', 'checked' => ''],
];

$statusConfig = [
    'strategy' => ['label' => 'Strategy published',      'class' => 'pill pill--active'],
    'plan'     => ['label' => 'Referenced in plan',      'class' => 'pill pill--forming'],
    'none'     => ['label' => 'No visible strategy',     'class' => 'pill pill--seeking'],
    'unknown'  => ['label' => 'Not yet verified',        'class' => 'pill pill--forming'],
];

$counts = array_count_values(array_column($councils, 'status'));

$sidebarRelated = [
    ['href' => '/scotland',        'label' => 'Scotland policy'],
    ['href' => '/landscape',       'label' => 'Why WIRES exists'],
    ['href' => '/get-involved',    'label' => 'Get involved'],
    ['href' => '/contact',         'label' => 'Tell us what you know'],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Who is acting on digital exclusion?</h1>
        <p>A tracker of which Scottish councils have published a digital inclusion strategy, which bodies have a mandate to act and what they have delivered, and where accountability is missing. We update this as we find out more — <a href="/contact">tell us</a> if you know something we've missed.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">
        <div class="prose">

            <div class="stat-strip">
                <a class="stat-item stat-item--link" href="#councils-verified">
                    <span class="stat-value"><?= $counts['strategy'] ?? 0 ?></span>
                    <span class="stat-label">council<?= ($counts['strategy'] ?? 0) !== 1 ? 's' : '' ?> with a published strategy</span>
                </a>
                <a class="stat-item stat-item--link" href="#councils-verified">
                    <span class="stat-value"><?= $counts['plan'] ?? 0 ?></span>
                    <span class="stat-label">with digital inclusion referenced in existing plans</span>
                </a>
                <a class="stat-item stat-item--link" href="#councils-verified">
                    <span class="stat-value"><?= $counts['none'] ?? 0 ?></span>
                    <span class="stat-label">with no visible strategy found</span>
                </a>
                <a class="stat-item stat-item--link" href="#councils-unverified">
                    <span class="stat-value"><?= $counts['unknown'] ?? 0 ?></span>
                    <span class="stat-label">not yet verified — help us find out</span>
                </a>
            </div>

            <div class="callout" style="margin-bottom:2rem">
                <p><strong>Methodology:</strong> We search council websites, Local Housing Strategies, corporate plans, and published strategy documents. "No visible strategy" means we searched and found nothing — not that nothing exists. If your council has something we've missed, <a href="/contact">tell us</a>. This tracker is a live document.</p>
            </div>

            <h2>Bodies with a mandate</h2>
            <p>These organisations have either a formal mandate or public funding to address digital exclusion in Scotland. This is what we know about what they have actually delivered.</p>

            <div class="community-net-list" style="margin-bottom:2.5rem">
                <div class="community-net-item">
                    <div class="community-net-meta">
                        <span class="pill pill--forming">Scottish Government backed</span>
                        <span class="pill pill--seeking" style="margin-left:0.4rem">Accountability gap</span>
                    </div>
                    <h3 class="community-net-name">Digital Inclusion Alliance Scotland</h3>
                    <p>A multi-sector body with Scottish Government backing intended to coordinate digital inclusion activity across Scotland. The SCVO has described its early work as a "talking shop" with no clear lines of accountability. No published action plan, delivery framework, or outcome metrics have been identified.</p>
                    <a class="community-net-link" href="https://www.scvo.scot/policy-campaigning-research/digital/digital-inclusion-alliance"<?= external_link_attrs('https://www.scvo.scot/policy-campaigning-research/digital/digital-inclusion-alliance') ?>>SCVO on the Digital Inclusion Alliance &rarr;</a>
                </div>

                <div class="community-net-item">
                    <div class="community-net-meta">
                        <span class="pill pill--forming">Local government body</span>
                        <span class="pill pill--seeking" style="margin-left:0.4rem">Audit Scotland: no clear action plan</span>
                    </div>
                    <h3 class="community-net-name">COSLA Digital Office</h3>
                    <p>
                        COSLA's Digital Office co-owns the national Digital Strategy for Scotland (jointly with Scottish Government), which commits under "No-One Left Behind" to world-leading digital inclusion. The strategy also commits to "Digital Education and Skills" and "An Ethical Digital Nation."
                    </p>
                    <p>
                        In August 2024, Audit Scotland published <em>Tackling Digital Exclusion</em> — an independent assessment that was direct in its findings: roughly 1 in 6 Scottish adults (15%) lack the foundation digital skills needed for everyday life — the same figure the Scottish Government cites elsewhere as "15% lack foundational digital competencies"; momentum has stalled and leadership has weakened since the pandemic-era Connecting Scotland programme; <strong>there is no clear action plan for reducing exclusion, and it is unclear who is responsible</strong> across Scottish Government, local government, and the third sector. The Digital Inclusion Alliance had made slow progress and governance groups met infrequently. Audit Scotland called on Scottish Government and COSLA to develop a clear action plan with defined leadership by end of 2024/25.
                    </p>
                    <p>
                        COSLA's own internal paper (LD/24/025, May 2024) acknowledged the strategy needed refreshing and flagged reduced funding to local areas as a risk to delivery. The Third Force News summarised the situation as: <em>"No leadership, no momentum: Scottish Government has failed to act on digital exclusion."</em>
                    </p>
                    <p>
                        Note: a separate body — the <strong>Digital Office for Scottish Local Government</strong> (digitaloffice.scot) — is hosted at COSLA but operates independently, focused on digital transformation and data maturity within councils. It is distinct from COSLA's own Digital Office function.
                    </p>
                    <a class="community-net-link" href="https://audit.scot/uploads/2024-08/nr_240822_tackling_digital_exclusion.pdf"<?= external_link_attrs('https://audit.scot/uploads/2024-08/nr_240822_tackling_digital_exclusion.pdf') ?>>Audit Scotland: Tackling Digital Exclusion (August 2024, PDF) &rarr;</a>
                    &nbsp;&middot;&nbsp;
                    <a class="community-net-link" href="https://audit.scot/news/clearer-leadership-and-focus-needed-to-tackle-digital-exclusion"<?= external_link_attrs('https://audit.scot/news/clearer-leadership-and-focus-needed-to-tackle-digital-exclusion') ?>>Audit Scotland news release &rarr;</a>
                    &nbsp;&middot;&nbsp;
                    <a class="community-net-link" href="https://www.cosla.gov.uk/about-cosla/our-teams/digital-office"<?= external_link_attrs('https://www.cosla.gov.uk/about-cosla/our-teams/digital-office') ?>>COSLA Digital Office &rarr;</a>
                </div>

                <div class="community-net-item">
                    <div class="community-net-meta">
                        <span class="pill pill--forming">Scottish Government</span>
                        <span class="pill pill--seeking" style="margin-left:0.4rem">Strategy stalled</span>
                    </div>
                    <h3 class="community-net-name">Scottish Government — digital inclusion</h3>
                    <p>The Scottish Government declared a housing emergency in 2024. Connecting Scotland — its flagship digital inclusion programme — has had no updated national strategy and no published delivery plan since its Covid-era phase. The SCVO has described digital inclusion work in Scotland as "under-resourced, undervalued, and increasingly stretched." No successor programme has been announced.</p>
                    <a class="community-net-link" href="https://www.gov.scot/policies/digital/"<?= external_link_attrs('https://www.gov.scot/policies/digital/') ?>>Scottish Government: digital policy &rarr;</a>
                </div>

                <div class="community-net-item">
                    <div class="community-net-meta">
                        <span class="pill pill--forming">Regulator</span>
                        <span class="pill pill--forming" style="margin-left:0.4rem">Social tariff powers under-used</span>
                    </div>
                    <h3 class="community-net-name">Ofcom</h3>
                    <p>Ofcom has the power to require ISPs to offer social tariffs and to monitor take-up. Only 1 in 12 eligible households currently use the social tariff they are entitled to, and 70% of people on benefits have never heard of them. Ofcom has published data on this but has not used regulatory powers to mandate active promotion by providers.</p>
                    <a class="community-net-link" href="https://www.ofcom.org.uk/phones-and-broadband/saving-money/social-tariffs"<?= external_link_attrs('https://www.ofcom.org.uk/phones-and-broadband/saving-money/social-tariffs') ?>>Ofcom: social tariffs &rarr;</a>
                </div>

                <div class="community-net-item">
                    <div class="community-net-meta">
                        <span class="pill pill--forming">Housing regulator</span>
                        <span class="pill pill--forming" style="margin-left:0.4rem">No digital inclusion requirement</span>
                    </div>
                    <h3 class="community-net-name">Scottish Housing Regulator</h3>
                    <p>The Scottish Housing Regulator oversees RSLs and local council housing. It does not currently require RSLs to report on digital inclusion or tenant connectivity as part of regulatory performance reporting — meaning the scale of digital exclusion among social housing tenants remains largely unmeasured at a national level.</p>
                    <a class="community-net-link" href="https://www.scottishhousingregulator.gov.uk/"<?= external_link_attrs('https://www.scottishhousingregulator.gov.uk/') ?>>Scottish Housing Regulator &rarr;</a>
                </div>
            </div>

            <h2>The 32 Scottish councils</h2>
            <p>Scotland has 32 local authorities. Each has responsibility for housing, community development, and local services — and each could act on digital exclusion within its area. Below is what we have verified so far.</p>

            <?php
            $verified   = array_filter($councils, fn($c) => $c['status'] !== 'unknown');
            $unverified = array_filter($councils, fn($c) => $c['status'] === 'unknown');
            ?>

            <?php if (!empty($verified)): ?>
            <div class="council-list" id="councils-verified">
                <?php foreach ($verified as $c):
                    $sc = $statusConfig[$c['status']] ?? $statusConfig['unknown'];
                ?>
                <div class="council-entry" data-status="<?= e($c['status']) ?>">
                    <div class="council-entry__head">
                        <strong class="council-entry__name"><?= e($c['name']) ?></strong>
                        <span class="<?= e($sc['class']) ?>"><?= e($sc['label']) ?></span>
                        <?php if (!empty($c['checked'])): ?>
                            <span class="council-entry__date">Checked <?= e($c['checked']) ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($c['note'])): ?>
                        <p class="council-entry__note"><?= e($c['note']) ?></p>
                        <?php if (!empty($c['url'])): ?>
                            <a class="btn btn-ghost btn-sm" href="<?= e($c['url']) ?>"<?= external_link_attrs($c['url']) ?>>View source &rarr;</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($unverified)): ?>
            <h3 style="font-family:var(--font-display);font-size:1.1rem;font-weight:800;margin:2rem 0 0.75rem">Not yet verified — help us find out</h3>
            <p style="color:var(--muted);font-size:0.92rem;margin-bottom:1rem">We haven't been able to confirm what, if anything, these councils have in place. If you live or work in one of these areas, <a href="/contact">check and tell us</a>.</p>
            <div class="council-unknown-grid" id="councils-unverified">
                <?php foreach ($unverified as $c): ?>
                <div class="council-unknown-item">
                    <strong><?= e($c['name']) ?></strong>
                    <?php if (!empty($c['note'])): ?>
                        <span class="council-unknown-note"><?= e($c['note']) ?></span>
                    <?php endif; ?>
                    <a href="/contact" class="council-unknown-link">Tell us</a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="info-card" style="margin-top:2rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Help us complete this tracker</h2>
                    <p class="info-card__sub">31 councils still need verification</p>
                </div>
                <div class="info-card__body">
                    <p>If you live or work in any of the unverified council areas, you can help. Check your council's website for a digital strategy, digital inclusion plan, or any reference to digital access in its Local Housing Strategy or corporate plan. Then <a href="/contact">tell us what you find</a> — including a link if there is one.</p>
                    <p>If your council has no plan, that itself is worth recording. A formal absence is accountability information.</p>
                    <p><a class="btn btn-primary btn-sm" href="/contact">Send us what you know &rarr;</a></p>
                </div>
            </div>

            <p class="meta" style="margin-top:1.5rem">This tracker was last updated June 2026. Status labels reflect what WIRES has verified — not a comprehensive audit. Councils may have plans not easily findable online; equally, plans that exist on paper do not guarantee action. <a href="/contact">Corrections and additions welcome</a>.</p>

        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div>
    </div>
</div>

<?php
$ctaHeading = 'Accountability requires attention';
$ctaBody    = 'Join WIRES and help us track what councils and bodies are actually doing — not just what they say.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
