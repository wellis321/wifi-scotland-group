<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Digital exclusion and health';
$pageDescription = 'Evidence on how being offline affects physical and mental health — from the WHO, COVID-19 research, NHS data, and peer-reviewed studies on loneliness and isolation.';
$currentNav      = 'whymatters';

$pageOgImage    = image_asset('card-community.jpg');
$pageOgImageAlt = 'People together — representing the health and social consequences of digital exclusion.';

$sidebarRelated = [
    ['href' => '/why-it-matters',  'label' => 'Why connectivity matters'],
    ['href' => '/beyond-broadband','label' => 'Beyond broadband — the full stack of barriers'],
    ['href' => '/get-help',        'label' => 'Help getting online'],
    ['href' => '/resources',       'label' => 'Resources & references'],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Digital exclusion and health</h1>
        <p>Being offline is not simply inconvenient — it has documented consequences for physical and mental health. As public services, healthcare booking, and social connection move online, digital exclusion increasingly determines health outcomes.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">
        <div class="prose">

            <div class="pull-quote">
                <p>"People with poor health and living with a disability, older people, migrants and those with a lower socioeconomic status are struggling the most in accessing digital health tools."</p>
                <cite><a href="https://www.who.int/europe/news/item/17-03-2026-digital-health-equity-gaps-remain--but-solutions-are-becoming-clearer--new-report-shows"<?= external_link_attrs('https://www.who.int/europe/news/item/17-03-2026-digital-health-equity-gaps-remain--but-solutions-are-becoming-clearer--new-report-shows') ?>>WHO Europe, March 2026</a></cite>
            </div>

            <p>
                The World Health Organisation passed a formal resolution in 2018 urging member states to ensure digital health tools reach the most vulnerable — framing digital access as a condition of equitable healthcare, not an optional extra.
                <a href="https://thenhsalliance.org/resources/assessing-digital-inclusion-in-the-nhs-the-nhs-app"<?= external_link_attrs('https://thenhsalliance.org/resources/assessing-digital-inclusion-in-the-nhs-the-nhs-app') ?>>The NHS Confederation has found</a> that <strong>2.4 million UK households struggle to afford mobile contracts</strong> and around <strong>8 million people lack the skills</strong> to make meaningful use of online services — with 77% of the digitally excluded being over 65 and 69% living with a disability or impairment.
            </p>

            <div class="stat-strip">
                <div class="stat-item">
                    <span class="stat-value">2.4m</span>
                    <span class="stat-label">UK households struggle to afford mobile contracts (NHS Confederation)</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">77%</span>
                    <span class="stat-label">of the digitally excluded are over 65 (NHS Confederation)</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">69%</span>
                    <span class="stat-label">of the digitally excluded live with a disability or impairment</span>
                </div>
            </div>

            <h2>Loneliness and isolation</h2>
            <p>
                A 2025 study drawing on 87,256 observations across China, the US, and the UK found a consistent association between digital exclusion and loneliness in older adults. Social isolation driven by digital exclusion carries health risks comparable to smoking and obesity in terms of mortality — an assessment from the National Academies of Sciences, based on approximately one in four community-dwelling Americans aged 65 and over being socially isolated.
            </p>
            <ul>
                <li><a href="https://link.springer.com/article/10.1186/s12877-025-06337-2"<?= external_link_attrs('https://link.springer.com/article/10.1186/s12877-025-06337-2') ?>>Digital exclusion and loneliness in older adults</a> (BMC Geriatrics, 2025 — 87,256 observations across three countries)</li>
                <li><a href="https://thenhsalliance.org/resources/assessing-digital-inclusion-in-the-nhs-the-nhs-app"<?= external_link_attrs('https://thenhsalliance.org/resources/assessing-digital-inclusion-in-the-nhs-the-nhs-app') ?>>Assessing digital inclusion in the NHS</a> (NHS Alliance / NHS Confederation, 2026)</li>
            </ul>

            <h2>What COVID-19 showed us</h2>
            <p>
                The pandemic made the health consequences of digital exclusion visible and measurable. US research found that counties with higher rates of digital exclusion experienced higher COVID-19 case rates, death rates, and lower vaccination take-up — because vaccine booking, public health information, and access to virtual care all required internet access that many did not have.
            </p>
            <p>
                In the UK, working-class students were roughly half as likely as middle-class students to access live or recorded lessons during school closures. Mental health worsening among those without computer access was "greatly pronounced" compared to those who had it — a finding that holds across age groups.
            </p>
            <ul>
                <li><a href="https://pmc.ncbi.nlm.nih.gov/articles/PMC9283607/"<?= external_link_attrs('https://pmc.ncbi.nlm.nih.gov/articles/PMC9283607/') ?>>Disconnected in a pandemic: digital exclusion and COVID-19 outcomes</a> (PMC/NIH)</li>
                <li><a href="https://pmc.ncbi.nlm.nih.gov/articles/PMC9645341/"<?= external_link_attrs('https://pmc.ncbi.nlm.nih.gov/articles/PMC9645341/') ?>>Digital exclusion and mental health during COVID-19</a> (PMC/NIH)</li>
                <li><a href="https://www.goodthingsfoundation.org/policy-and-research/research-and-evidence/research-2024/health-inequalities-digital-exclusion"<?= external_link_attrs('https://www.goodthingsfoundation.org/policy-and-research/research-and-evidence/research-2024/health-inequalities-digital-exclusion') ?>>Health inequalities and digital exclusion</a> (Good Things Foundation, 2024)</li>
            </ul>

            <h2>NHS services going digital</h2>
            <p>
                In Scotland and across the UK, NHS services are increasingly delivered online or via apps — appointment booking, test results, repeat prescription requests, mental health self-referral, and telehealth consultations. Each shift online that is not accompanied by a non-digital alternative means that people without reliable internet access receive a lower standard of care. This is not an abstract future risk: it is the current experience of millions of people.
            </p>
            <ul>
                <li><a href="https://www.who.int/publications/i/item/10665-279505"<?= external_link_attrs('https://www.who.int/publications/i/item/10665-279505') ?>>WHO resolution WHA71.7 on digital health (2018)</a> — urging member states to ensure digital health is equitable</li>
            </ul>

            <div class="pull-quote">
                <p>"Digital exclusion is not a second-order problem. For millions of people it is a health problem, a care problem, and a rights problem."</p>
                <cite>WIRES campaign position</cite>
            </div>

            <p class="meta">Sources current as of mid-2025. If a link breaks or a study is updated, <a href="/contact">let us know</a>.</p>

        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div>
    </div>
</div>

<?php
$ctaHeading = 'Turn evidence into action';
$ctaBody    = 'Join WIRES to hear about consultations, events, and ways to raise these issues locally.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
