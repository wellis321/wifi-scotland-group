<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Beyond broadband — the full stack of digital exclusion';
$pageDescription = 'Digital exclusion is not just about connectivity. Devices, design choices, authentication, skills, and language all create separate layers of exclusion that broadband alone cannot fix.';
$currentNav      = 'whymatters';

$pageOgImage    = image_asset('card-fibre.jpg');
$pageOgImageAlt = 'Cables and infrastructure — representing the layers of digital access beyond broadband.';

$sidebarRelated = [
    ['href' => '/why-it-matters', 'label' => 'Why connectivity matters'],
    ['href' => '/digital-health', 'label' => 'Digital exclusion and health'],
    ['href' => '/get-help',       'label' => 'Help getting online'],
    ['href' => '/get-involved',   'label' => 'Get involved'],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Beyond broadband</h1>
        <p>Digital exclusion is usually framed as a connectivity problem — you either have internet access or you don't. That framing misses most of the picture. There is a full stack of barriers, and being excluded from any layer means being excluded from services, opportunities, and rights that others take for granted.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">
        <div class="prose">

            <p>Here's how far the problem extends beyond "do you have broadband":</p>

            <div class="stat-strip">
                <div class="stat-item">
                    <span class="stat-value">15%</span>
                    <span class="stat-label">of Scotland's adults lack foundational digital skills — roughly 1 in 6 (Audit Scotland 2024; Scottish Government 2025)</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">15.1%</span>
                    <span class="stat-label">of Scottish residents do not use fixed broadband at all (Scottish Government analysis of Ofcom Technology Tracker 2024 data)</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">8m</span>
                    <span class="stat-label">UK adults lack the skills to make meaningful use of online services (Good Things Foundation)</span>
                </div>
            </div>

            <p>
                The standard measure of digital inclusion — does the household have a broadband connection? — is necessary but nowhere near sufficient. Access to a reliable connection is the starting point, not the whole story.
            </p>

            <h2>1. No device</h2>
            <p>
                Having a broadband connection in your building means nothing without a working device to use it on. Device cost is consistently identified as the top digital inclusion barrier globally. For people on the lowest incomes, a new smartphone can represent weeks of disposable income — and a laptop more still. Refurbished device programmes and library lending schemes exist but are patchwork, under-resourced, and geographically uneven.
            </p>

            <h2>2. An outdated device</h2>
            <p>
                Even if someone has a device, it may be too old to run the app or access the service. Developers routinely set minimum operating system requirements that exclude devices only a few years old — a design choice that falls hardest on people in poverty, older people, and those for whom a replacement phone or laptop is simply unaffordable. There is no regulatory requirement on developers to consider the devices their excluded users actually hold.
            </p>
            <p class="meta">Sources: <a href="https://arxiv.org/abs/2311.00984"<?= external_link_attrs('https://arxiv.org/abs/2311.00984') ?>>Unveiling Inclusiveness-Related User Feedback in Mobile Applications</a> (arxiv, 2023) · <a href="https://www.ncbi.nlm.nih.gov/pmc/articles/PMC12350549/"<?= external_link_attrs('https://www.ncbi.nlm.nih.gov/pmc/articles/PMC12350549/') ?>>Optimizing mobile app design for older adults: a systematic review of age-friendly design</a> (PMC, 2025).</p>

            <h2>3. Authentication designed for the already-connected</h2>
            <p>
                Two-factor authentication — where a service texts a code to your smartphone or asks you to open an authenticator app — is now standard security practice. It is also an effective lock-out for anyone without a suitable smartphone: people in temporary accommodation, older people on basic handsets, and those whose devices are too old to run the required app. Government services, banks, and Universal Credit itself require forms of digital authentication that assume the user already has a modern device and a mobile number. The security system designed to protect users can be the thing that prevents them from getting help at all.
            </p>

            <h2>4. Digital skills as a distinct barrier</h2>
            <p>
                Having access and having the skills to use it are not the same thing. Scotland's own <a href="https://www.gov.scot/publications/towards-scottish-minimum-digital-living-standard-interim-report/pages/3/"<?= external_link_attrs('https://www.gov.scot/publications/towards-scottish-minimum-digital-living-standard-interim-report/pages/3/') ?>>Minimum Digital Living Standard interim report</a> (2025) identifies "relative exclusion" — people who are online but whose limited digital skills and lack of support leave them effectively unable to navigate the services they need. Complex form interfaces, unintuitive navigation, CAPTCHA systems, password requirements, and account management processes all assume a level of digital familiarity that <strong>15% of Scottish adults do not have</strong>.
            </p>
            <p>
                The practical consequences are significant: Universal Credit managed online, GP appointment booking via an app, tax returns filed digitally, and job applications submitted through portals that assume you know how to create an account and upload a document.
            </p>

            <h2>5. Language</h2>
            <p>
                Digital services in Scotland default to English. For Gaelic speakers, refugees, migrants, and others whose first language is not English, the barriers compound: not just navigating an unfamiliar system, but doing so in a second language, often without translation, often without the option of interpretation. Forms that cannot be completed in your language effectively exclude you regardless of your internet connection or device.
            </p>
            <p>
                Gaelic faces a specific structural challenge: its <a href="https://www.civtech.scot/civtech-11-challenge-2-data-sparsity-gaelic-language"<?= external_link_attrs('https://www.civtech.scot/civtech-11-challenge-2-data-sparsity-gaelic-language') ?>>digital corpus</a> — the body of text available for language tools and AI systems to learn from — is around <strong>150 million words</strong>, compared to <strong>4 billion for Basque</strong>. This "data sparsity" problem means Gaelic speakers are systematically excluded from the benefits of digital language tools that speakers of better-resourced languages can access, despite the Scottish Government's own <a href="https://www.gov.scot/publications/scottish-governments-gaelic-language-plan-2022-2027/pages/6/"<?= external_link_attrs('https://www.gov.scot/publications/scottish-governments-gaelic-language-plan-2022-2027/pages/6/') ?>>Gaelic Language Plan 2022–2027</a> committing to digital support.
            </p>
            <p class="meta">Further reading: <a href="https://www.digital.govt.nz/dmsdocument/196~digital-inclusion-user-insights-former-refugees-and-migrants-with-english-as-a-second-language/html"<?= external_link_attrs('https://www.digital.govt.nz/dmsdocument/196~digital-inclusion-user-insights-former-refugees-and-migrants-with-english-as-a-second-language/html') ?>>Digital inclusion for refugees and migrants</a> (New Zealand Digital Government — applicable to Scottish context).</p>

            <div class="pull-quote">
                <p>"Connectivity is the entry point, not the destination. You can have electricity coming into the building and still be sitting in the dark."</p>
                <cite>WIRES campaign position</cite>
            </div>

            <div class="info-card">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Why this matters for policy</h2>
                    <p class="info-card__sub">Broadband programmes alone are not enough</p>
                </div>
                <div class="info-card__body">
                    <p>Broadband programmes alone are not enough — R100 and the Gigabit Voucher Scheme fix infrastructure, but don't touch devices, skills, authentication, or language. A campaign that only argues for better broadband coverage is arguing for a necessary but insufficient condition. WIRES argues for the whole stack.</p>
                    <p><a class="btn btn-ghost btn-sm" href="/landscape">How WIRES is different &rarr;</a></p>
                </div>
            </div>

            <p class="meta">Sources current as of mid-2025. If a link breaks or new research is published, <a href="/contact">let us know</a>.</p>

        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div>
    </div>
</div>

<?php
$ctaHeading = 'Help us make this argument';
$ctaBody    = 'Join WIRES and support the campaign for connectivity rights that goes beyond broadband.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
