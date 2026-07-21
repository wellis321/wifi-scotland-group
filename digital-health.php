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

            <p>The people most affected are often those who can least afford to be:</p>

            <div class="stat-strip">
                <div class="stat-item">
                    <span class="stat-value">8m</span>
                    <span class="stat-label">people in the UK lack the digital skills to make meaningful use of online services</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">77%</span>
                    <span class="stat-label">of the digitally excluded are over 65</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">69%</span>
                    <span class="stat-label">of the digitally excluded live with a disability or impairment</span>
                </div>
            </div>
            <p class="meta">Source: <a href="https://www.goodthingsfoundation.org/policy-and-research/research-and-evidence/research-2024/digital-nation"<?= external_link_attrs('https://www.goodthingsfoundation.org/policy-and-research/research-and-evidence/research-2024/digital-nation') ?>>Good Things Foundation, Digital Nation report</a>. Separately, <a href="https://www.ofcom.org.uk/phones-and-broadband/bills-and-charges/pricing-and-consumer-engagement"<?= external_link_attrs('https://www.ofcom.org.uk/phones-and-broadband/bills-and-charges/pricing-and-consumer-engagement') ?>>Ofcom estimates</a> millions of UK households struggle to afford their mobile phone bill.</p>

            <p>
                The WHO has been saying this for years — its <a href="https://www.who.int/publications/i/item/10665-279505"<?= external_link_attrs('https://www.who.int/publications/i/item/10665-279505') ?>>2018 resolution</a> urged member states to ensure digital health tools reach the most vulnerable. A more recent report puts it plainly:
            </p>

            <div class="pull-quote">
                <p>"People with poor health and living with a disability, older people, migrants and those with a lower socioeconomic status are struggling the most in accessing these tools."</p>
                <cite><a href="https://www.who.int/europe/news/item/17-03-2026-digital-health-equity-gaps-remain--but-solutions-are-becoming-clearer--new-report-shows"<?= external_link_attrs('https://www.who.int/europe/news/item/17-03-2026-digital-health-equity-gaps-remain--but-solutions-are-becoming-clearer--new-report-shows') ?>>WHO Europe, March 2026</a></cite>
            </div>

            <h2>Loneliness and isolation</h2>
            <p>
                Being cut off from the internet is, for many people, being cut off from other people too. A <a href="https://link.springer.com/article/10.1186/s12877-025-06337-2"<?= external_link_attrs('https://link.springer.com/article/10.1186/s12877-025-06337-2') ?>>2025 study</a> drawing on 87,256 observations across China, the US, and the UK found a consistent link between digital exclusion and loneliness in older adults. That matters because loneliness itself is dangerous — the US <a href="https://nap.nationalacademies.org/read/25663"<?= external_link_attrs('https://nap.nationalacademies.org/read/25663') ?>>National Academies of Sciences, Engineering, and Medicine</a> has found that social isolation significantly raises mortality risk, rivalling smoking and obesity, and estimates around one in four community-dwelling Americans over 65 are socially isolated. That isolation research isn't specific to digital exclusion — WIRES draws the connection.
            </p>
            <p class="meta">Further reading: <a href="https://thenhsalliance.org/resources/assessing-digital-inclusion-in-the-nhs-the-nhs-app"<?= external_link_attrs('https://thenhsalliance.org/resources/assessing-digital-inclusion-in-the-nhs-the-nhs-app') ?>>Assessing digital inclusion in the NHS</a> (NHS Alliance, 2026).</p>

            <h2>What COVID-19 showed us</h2>
            <p>
                In the US, counties with higher digital exclusion saw higher case rates, higher death rates, and lower vaccination take-up, because <a href="https://pmc.ncbi.nlm.nih.gov/articles/PMC9283607/"<?= external_link_attrs('https://pmc.ncbi.nlm.nih.gov/articles/PMC9283607/') ?>>booking a vaccine or accessing virtual care</a> assumed internet access many didn't have.
            </p>
            <p>
                In the UK, working-class students were roughly half as likely as middle-class students to access lessons during school closures, and <a href="https://pmc.ncbi.nlm.nih.gov/articles/PMC9645341/"<?= external_link_attrs('https://pmc.ncbi.nlm.nih.gov/articles/PMC9645341/') ?>>mental health worsening</a> among those without computer access was "greatly pronounced" by comparison.
            </p>
            <p class="meta">Further reading: <a href="https://www.goodthingsfoundation.org/policy-and-research/research-and-evidence/research-2024/health-inequalities-digital-exclusion"<?= external_link_attrs('https://www.goodthingsfoundation.org/policy-and-research/research-and-evidence/research-2024/health-inequalities-digital-exclusion') ?>>Health inequalities and digital exclusion</a> (Good Things Foundation, 2024).</p>

            <h2>NHS services going digital</h2>
            <p>
                Appointment booking, test results, repeat prescriptions, mental health self-referral — NHS services are increasingly online, in Scotland and across the UK. Every shift that isn't paired with a non-digital alternative means people without reliable internet get a lower standard of care. That's not a future risk — it's the current experience of millions.
            </p>

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
