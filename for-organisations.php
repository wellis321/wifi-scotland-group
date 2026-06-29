<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Guidance for organisations — designing services that work for everyone';
$pageDescription = 'Evidence-based guidance for Scottish organisations on digital inclusion — including the risk of AI adoption widening exclusion, published standards to follow, and practical principles to apply.';
$currentNav      = 'fororganisations';

$pageOgImage    = image_asset('about-team.jpg');
$pageOgImageAlt = 'People working together — representing organisations taking digital inclusion seriously.';

$sidebarRelated = [
    ['href' => '/join-as-organisation', 'label' => 'Sign up as a supporter'],
    ['href' => '/supporters',           'label' => 'Organisational supporters'],
    ['href' => '/beyond-broadband',     'label' => 'Beyond broadband — the full stack of barriers'],
    ['href' => '/get-help',             'label' => 'Help getting online'],
    ['href' => '/accountability',       'label' => 'Who is acting?'],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Designing services that work for everyone</h1>
        <p>Guidance for Scottish organisations on digital inclusion — including what the evidence says about AI adoption, the published standards that apply to you, and practical steps you can take right now.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">
        <div class="prose">

            <div class="campaign-statement-card" aria-hidden="true">
                <p class="campaign-statement-card__line1">A growing risk</p>
                <p class="campaign-statement-card__line2">Digital-first<br>can mean<br>people-last.</p>
            </div>

            <p>
                Across Scotland, organisations are adopting AI chatbots, app-based services, and digital-only pathways to modernise and reduce costs. Many are doing so without considering who gets left behind. The evidence is clear: digital adoption without deliberate inclusion design creates new barriers for the people who already face the most.
            </p>

            <h2>The AI problem — named and cited</h2>

            <div class="pull-quote">
                <p>"Digitally excluded people would be poorly represented in AI training datasets, amplifying their marginalisation. The UK's AI ambitions are undermined by digital exclusion."</p>
                <cite><a href="https://publications.parliament.uk/pa/ld5803/ldselect/ldcomm/219/219.pdf"<?= external_link_attrs('https://publications.parliament.uk/pa/ld5803/ldselect/ldcomm/219/219.pdf') ?>>House of Lords Communications and Digital Committee, Digital Exclusion (June 2023)</a></cite>
            </div>

            <p>
                The House of Lords went further: organisations adopting machine learning "would further disadvantage digitally-excluded groups" because the systems learn from existing digital users — which systematically excludes the people who couldn't access digital services in the first place. An AI customer service tool trained on confident digital users will be optimised for them, not for the person who most needs help.
            </p>

            <p>
                THINK Digital Partners named the compounding risk directly in February 2026: as AI-enabled services become the norm, those already excluded face "a growing class of people who cannot reliably access, use or benefit from digital services essential to modern life." Every AI capability added without an inclusive fallback is a new barrier installed.
            </p>

            <ul>
                <li><a href="https://lordslibrary.parliament.uk/digital-exclusion-in-the-uk-communications-and-digital-committee-report/"<?= external_link_attrs('https://lordslibrary.parliament.uk/digital-exclusion-in-the-uk-communications-and-digital-committee-report/') ?>>House of Lords Library: Digital Exclusion report summary</a></li>
                <li><a href="https://www.thinkdigitalpartners.com/news/2026/02/24/digital-inclusion-in-the-age-of-ai/"<?= external_link_attrs('https://www.thinkdigitalpartners.com/news/2026/02/24/digital-inclusion-in-the-age-of-ai/') ?>>THINK Digital Partners: Digital Inclusion in the Age of AI (February 2026)</a></li>
            </ul>

            <h2>Who is digitally excluded — and it may not be who you think</h2>
            <p>
                Ofcom's 2024 research challenges the assumption that digital exclusion is primarily an issue for older people. The remaining offline population is significantly skewed toward <strong>younger people facing financial barriers</strong>. 1.6 million people in the UK are still entirely offline. 23% of the population struggles with online services. 10 million workers lack essential digital skills.
            </p>
            <p>
                Digital exclusion is shaped by income, disability, device age, data costs, digital skills, language, and authentication barriers — not simply age. Any service design that assumes "our users are digitally confident" is making an assumption that the evidence does not support.
            </p>
            <ul>
                <li><a href="https://www.ofcom.org.uk/internet-based-services/technology/digital-adoption-and-digital-disadvantage-today-what-has-changed-and-what-barriers-remain"<?= external_link_attrs('https://www.ofcom.org.uk/internet-based-services/technology/digital-adoption-and-digital-disadvantage-today-what-has-changed-and-what-barriers-remain') ?>>Ofcom: Digital Adoption and Digital Disadvantage Today (2024)</a></li>
                <li><a href="https://www.goodthingsfoundation.org/dam/jcr:c1da40c1-5247-499e-a627-f273a3a1de55/GoodThings_DigitalInclusionDatasets_2024.pdf"<?= external_link_attrs('https://www.goodthingsfoundation.org/dam/jcr:c1da40c1-5247-499e-a627-f273a3a1de55/GoodThings_DigitalInclusionDatasets_2024.pdf') ?>>Good Things Foundation: What the Main UK Datasets Tell Us (September 2024, PDF)</a></li>
            </ul>

            <h2>Seven practical principles</h2>
            <p>These are drawn from the NHS England framework, the UK Government's Digital Inclusion Action Plan, the GOV.UK Service Standard, and the evidence above.</p>

            <ol class="step-list">
                <li class="step-item">
                    <div>
                        <h3>Never digital-only — always maintain a non-digital pathway</h3>
                        <p>The single most prominent theme in responses to the UK Government's Digital Inclusion Action Plan was this: phone and face-to-face access must remain available alongside digital services. NHS England's Inclusive Digital Healthcare framework is explicit: "digital approaches must be complementary to non-digital services." When you add a chatbot, an app, or an online portal, ask who this replaces for rather than supplements.</p>
                    </div>
                </li>
                <li class="step-item">
                    <div>
                        <h3>Test with digitally excluded users — not just confident ones</h3>
                        <p>The GOV.UK Service Standard requires teams to research with users who struggle with digital services and provide assisted digital support. If your user testing sample does not include people with low digital skills, people on basic mobile contracts, and people without smartphones, you are designing for the already-included. Actively recruit these users to your testing sessions — not as an afterthought, but from the start.</p>
                    </div>
                </li>
                <li class="step-item">
                    <div>
                        <h3>Do not require a smartphone for authentication</h3>
                        <p>Two-factor authentication via a smartphone app is standard security practice. It is also a lock-out for older people on basic handsets, people in temporary accommodation, and anyone whose device is too old to run the required app. Offer SMS codes (not just app-based authentication), phone call verification, or staff-assisted authentication as alternatives. Universal Credit itself has created barriers here — do not replicate the problem.</p>
                    </div>
                </li>
                <li class="step-item">
                    <div>
                        <h3>Consider the cost of data poverty</h3>
                        <p>An app that requires a download of 50MB, runs regular background syncs, or streams video to explain features has a financial cost that your service design team likely never sees. People managing on 1–2GB mobile data plans per month will either not download your app or will ration every interaction. Design for data poverty: optimise app size, avoid autoplay, offer low-data modes, and explain data usage clearly before download.</p>
                    </div>
                </li>
                <li class="step-item">
                    <div>
                        <h3>Do not assume the latest operating system</h3>
                        <p>Setting a minimum OS requirement that excludes devices two or three years old is a design decision — and it is rarely made with the user in mind. Research confirms these choices "particularly trouble users who may not have the ability to switch to a newer device." Understand what devices your users actually have, not what devices you would like them to have. There is no regulatory requirement forcing organisations to consider this — which is exactly why you should do it deliberately.</p>
                    </div>
                </li>
                <li class="step-item">
                    <div>
                        <h3>Use plain, human language — especially in AI-generated content</h3>
                        <p>AI-generated text tends toward formal, complex language. It regularly uses passive voice, abstract nouns, and long sentence structures. For people with lower literacy, cognitive impairments, or English as a second language, this is a barrier to understanding. If you are using AI to generate content, correspondence, or service responses, audit it specifically for plain language — do not assume the model's default output is appropriate for your audience.</p>
                    </div>
                </li>
                <li class="step-item">
                    <div>
                        <h3>Meet WCAG 2.2 AA as a minimum — and go further</h3>
                        <p>Web Content Accessibility Guidelines 2.2 Level AA is the legal baseline for public sector organisations in the UK. It covers screen readers, keyboard navigation, colour contrast, and much more. But WCAG compliance does not guarantee your service works for digitally excluded users — it is a floor, not a ceiling. Accessibility and digital inclusion are related but distinct. Treat WCAG 2.2 as the start of a conversation, not the end of your obligations.</p>
                    </div>
                </li>
            </ol>

            <h2>Published standards that apply to your organisation</h2>

            <div class="community-net-list">
                <div class="community-net-item">
                    <div class="community-net-meta"><span class="pill">NHS England</span></div>
                    <h3 class="community-net-name">Inclusive Digital Healthcare Framework</h3>
                    <p>The most explicit published standard on maintaining non-digital alternatives. Requires organisations to identify when users lack capability, provide personalised alternative approaches, and maintain non-digital support alongside any digital offering. Applicable beyond health — sets the benchmark for any organisation delivering essential services.</p>
                    <a class="community-net-link" href="https://www.england.nhs.uk/long-read/inclusive-digital-healthcare-a-framework-for-nhs-action-on-digital-inclusion/"<?= external_link_attrs('https://www.england.nhs.uk/long-read/inclusive-digital-healthcare-a-framework-for-nhs-action-on-digital-inclusion/') ?>>NHS England: Inclusive Digital Healthcare Framework &rarr;</a>
                </div>
                <div class="community-net-item">
                    <div class="community-net-meta"><span class="pill">UK Government</span></div>
                    <h3 class="community-net-name">Digital Inclusion Action Plan</h3>
                    <p>The UK Government's first cross-departmental digital inclusion plan (February 2025), committing to update the GOV.UK Service Manual to require non-digital channels remain accessible alongside digital services. Named priority groups: lower-income households, older people, disabled people, unemployed people, young people.</p>
                    <a class="community-net-link" href="https://www.gov.uk/government/publications/digital-inclusion-action-plan-first-steps/digital-inclusion-action-plan-first-steps"<?= external_link_attrs('https://www.gov.uk/government/publications/digital-inclusion-action-plan-first-steps/digital-inclusion-action-plan-first-steps') ?>>DSIT: Digital Inclusion Action Plan: First Steps (February 2025) &rarr;</a>
                </div>
                <div class="community-net-item">
                    <div class="community-net-meta"><span class="pill">Scotland</span></div>
                    <h3 class="community-net-name">Scotland's Digital Inclusion Charter</h3>
                    <p>A cross-sector pledge framework refreshed in December 2024. Signatory commitments include understanding the digital inclusion needs of your audience, committing organisational resources, and delivering against a plan. Pairs with the SCVO Digital Inclusion Roadmap.</p>
                    <a class="community-net-link" href="https://digitalinclusion.scot/"<?= external_link_attrs('https://digitalinclusion.scot/') ?>>Scotland's Digital Inclusion Charter &rarr;</a>
                    &nbsp;&middot;&nbsp;
                    <a class="community-net-link" href="https://scvo.scot/p/86534/2023/11/22/making-digital-inclusion-everyones-responsibility-a-roadmap-for-scotland"<?= external_link_attrs('https://scvo.scot/p/86534/2023/11/22/making-digital-inclusion-everyones-responsibility-a-roadmap-for-scotland') ?>>SCVO Digital Inclusion Roadmap &rarr;</a>
                </div>
            </div>

            <div class="info-card" style="margin-top:2rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Tell us what you're doing</h2>
                    <p class="info-card__sub">Your experience matters to the campaign</p>
                </div>
                <div class="info-card__body">
                    <p>If your organisation is grappling with these challenges — or has found approaches that work — WIRES wants to hear about it. Real examples from Scottish organisations are what make the campaign's evidence base credible and hard to dismiss.</p>
                    <p>
                        <a class="btn btn-primary btn-sm" href="/join-as-organisation">Sign up as an organisational supporter &rarr;</a>
                        <a class="btn btn-ghost btn-sm" href="/contact" style="margin-left:0.5rem">Get in touch</a>
                    </p>
                </div>
            </div>

            <p class="meta" style="margin-top:2rem">Sources on this page are from published reports, parliamentary committees, and statutory bodies. If you know of guidance we have missed, <a href="/contact">tell us</a>.</p>

        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div>
    </div>
</div>

<?php
$ctaHeading = 'Back this with your organisation';
$ctaBody    = 'Join the WIRES coalition and publicly commit to the principle that services should work for everyone — not just the digitally confident.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
