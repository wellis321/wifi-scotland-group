<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Get involved';
$pageDescription = 'Practical ways to support better connectivity in your community—without needing to be a technical expert.';
$currentNav      = 'involved';

$pageOgImage    = image_asset('card-community.jpg');
$pageOgImageAlt = 'People meeting together around a laptop—representing community organising.';

$sidebarRelated = [
    ['href' => '/groups.php',        'label' => 'Local groups'],
    ['href' => '/start-a-group.php', 'label' => 'Start a group'],
    ['href' => '/join.php',          'label' => 'Join WIRES'],
    ['href' => '/contact.php',       'label' => 'Contact us'],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Get involved</h1>
        <p>You do not need to know how fibre cabinets work to make a difference. Campaigns move when people translate technical programmes into questions their neighbours can recognise.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">

        <div class="prose">
            <img class="page-hero-img" src="<?= e(image_asset('card-community.jpg')) ?>" width="1200" height="800"
                 alt="Group discussion with a laptop open on a table." decoding="async" loading="lazy">

            <div class="pull-quote">
                <p>"Start with the places people already gather: libraries, community centres, tenants' meetings, parent groups."</p>
            </div>

            <h2>Six ways to take action</h2>

            <ol class="step-list">
                <li class="step-item">
                    <div>
                        <h3>Map the gaps in your area</h3>
                        <p>Collect examples from neighbours: "our tenement only has one provider", "mobile signal drops at the school gate". Pair stories with postcodes only if people consent. Local evidence is what changes what officials say.</p>
                    </div>
                </li>
                <li class="step-item">
                    <div>
                        <h3>Attend community council meetings</h3>
                        <p>Ask for a standing agenda item on connectivity. Community councils have formal routes to local authorities — and attendance shapes what gets raised. You don't need to speak; showing up sends a signal.</p>
                    </div>
                </li>
                <li class="step-item">
                    <div>
                        <h3>Write to your councillor</h3>
                        <p>We've written a template letter you can personalise in about two minutes. It asks specific, sourced questions about R100, social tariffs, and local digital inclusion — the kind of question that's hard to ignore.</p>
                        <p><a class="btn btn-ghost btn-sm" href="/write-to-councillor.php">Use the template letter &rarr;</a></p>
                    </div>
                </li>
                <li class="step-item">
                    <div>
                        <h3>Show up in democratic spaces</h3>
                        <p>Submit questions to full council or scrutiny committees when budget lines touch digital infrastructure or inclusion contracts. Connect with tenants' organisations where remote work policies assume broadband people may not have.</p>
                    </div>
                </li>
                <li class="step-item">
                    <div>
                        <h3>Visit libraries and community centres</h3>
                        <p>Many digital inclusion programmes meet people where they already go. Ask what timetables look like and whether transport or childcare is a barrier to participation.</p>
                    </div>
                </li>
                <li class="step-item">
                    <div>
                        <h3>Help WIRES directly</h3>
                        <p>We welcome volunteers for research, plain-language editing, accessibility reviews, and local "open calls" where we crowdsource broken links or confusing council leaflets. Any time you have — five minutes or five hours a week — is useful.</p>
                    </div>
                </li>
            </ol>

            <div class="campaign-statement-card" aria-hidden="true" style="margin-top:2.5rem">
                <p class="campaign-statement-card__line1">Every national campaign</p>
                <p class="campaign-statement-card__line2">is made of<br>local people.</p>
            </div>

            <h2>Start or join a local group</h2>
            <p>
                WIRES local groups map their council area, attend meetings, and give neighbours somewhere to turn. If your area doesn't have one yet, starting one takes as few as two or three people and a first conversation.
            </p>
            <p style="margin-bottom:2rem">
                <a class="btn btn-primary" href="/groups.php">Find a local group</a>
                <a class="btn btn-ghost" href="/start-a-group.php" style="margin-left:0.5rem">Start one</a>
            </p>

            <div class="callout">
                <p><strong>Safety note:</strong> Do not intercept neighbours' traffic or tamper with street cabinets. Community networking is powerful when it is legal, consensual, and transparent — see our <a href="/global-spotlight.php">Global spotlight</a> for examples with public documentation.</p>
            </div>
        </div>

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div>
    </div>
</div>

<?php
$ctaHeading = 'Ready to start?';
$ctaBody    = 'Join WIRES and we\'ll connect you with others working on connectivity in Scotland.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
