<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Why WIRES exists';
$pageDescription = 'Digital inclusion work in Scotland is under-resourced and fragmented. WIRES fills a specific gap: an independent, public-facing campaign that holds government to account and speaks directly to residents.';
$currentNav      = 'landscape';

$pageOgImage    = image_asset('card-community.jpg');
$pageOgImageAlt = 'People in a community meeting — representing the public-facing campaign work WIRES does.';

$sidebarRelated = [
    ['href' => '/about',        'label' => 'About WIRES'],
    ['href' => '/get-involved', 'label' => 'Get involved'],
    ['href' => '/supporters',   'label' => 'Organisational supporters'],
    ['href' => '/scotland',     'label' => 'Scotland policy'],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Why WIRES exists</h1>
        <p>There are organisations working on digital inclusion in Scotland — and they do important work. But none of them are quite what WIRES is. This page explains the landscape and where we fit within it.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">

        <div class="prose">

            <div class="campaign-statement-card" aria-hidden="true">
                <p class="campaign-statement-card__line1">The gap</p>
                <p class="campaign-statement-card__line2">An independent<br>public campaign.</p>
            </div>

            <div class="pull-quote">
                <p>"Digital inclusion work in Scotland is under-resourced, undervalued, and increasingly stretched."</p>
                <cite>Scottish Council for Voluntary Organisations (SCVO)</cite>
            </div>

            <p>
                That is the vacuum WIRES is stepping into — not duplicating what exists, but doing something that doesn't.
            </p>

            <h2>What already exists</h2>
            <p>Scotland has a number of bodies working on digital inclusion. It's worth being clear about what they do — and what they don't.</p>

            <h3>Government and official programmes</h3>

            <div class="community-net-list">
                <div class="community-net-item">
                    <div class="community-net-meta"><span class="pill">Scottish Government</span></div>
                    <h3 class="community-net-name">Connecting Scotland</h3>
                    <p>A government programme launched during Covid-19 that provided devices and internet connections to digitally excluded households, supporting over 61,000 people during the pandemic. Since then, momentum has stalled — there is no updated national strategy, no published delivery plan, and no clarity on who is responsible for what comes next.</p>
                    <a class="community-net-link" href="https://connecting.scot/"<?= external_link_attrs('https://connecting.scot/') ?>>connecting.scot &rarr;</a>
                </div>
                <div class="community-net-item">
                    <div class="community-net-meta"><span class="pill">Scottish Government backed</span></div>
                    <h3 class="community-net-name">Digital Inclusion Alliance Scotland</h3>
                    <p>A newly formed multi-sector body with Scottish Government backing, intended to coordinate digital inclusion activity across Scotland. The SCVO has described its early work as a "talking shop" with no clear lines of accountability — a concern that reflects a wider pattern of convening without committing.</p>
                    <a class="community-net-link" href="https://www.scvo.scot/policy-campaigning-research/digital/digital-inclusion-alliance"<?= external_link_attrs('https://www.scvo.scot/policy-campaigning-research/digital/digital-inclusion-alliance') ?>>Digital Inclusion Alliance (via SCVO) &rarr;</a>
                </div>
            </div>

            <h3 style="margin-top:2rem">Voluntary sector and charters</h3>

            <div class="community-net-list">
                <div class="community-net-item">
                    <div class="community-net-meta"><span class="pill">Sector infrastructure</span></div>
                    <h3 class="community-net-name">SCVO — Scottish Council for Voluntary Organisations</h3>
                    <p>The most active critical voice on digital inclusion in Scotland, publishing strong research and lobbying hard for action. But SCVO speaks to the voluntary sector — charities, community organisations, and public bodies. It is sector infrastructure, not a public-facing campaign. Most people in Scotland who are digitally excluded will never interact with SCVO directly.</p>
                    <a class="community-net-link" href="https://www.scvo.scot/policy-campaigning-research/digital"<?= external_link_attrs('https://www.scvo.scot/policy-campaigning-research/digital') ?>>SCVO: digital inclusion &rarr;</a>
                </div>
                <div class="community-net-item">
                    <div class="community-net-meta"><span class="pill">Membership scheme</span></div>
                    <h3 class="community-net-name">Scotland's Digital Inclusion Charter</h3>
                    <p>A charter organisations can sign to commit to digital inclusion principles — running for over a decade. It is a membership and accreditation mechanism, not a campaign. Signing the charter does not mean holding government to account, organising residents, or amplifying community voices.</p>
                    <a class="community-net-link" href="https://www.digitalinclusion.scot/"<?= external_link_attrs('https://www.digitalinclusion.scot/') ?>>digitalinclusion.scot &rarr;</a>
                </div>
                <div class="community-net-item">
                    <div class="community-net-meta"><span class="pill">UK-wide</span></div>
                    <h3 class="community-net-name">Digital Poverty Alliance</h3>
                    <p>A strong UK-wide body producing research and advocacy on digital poverty, with some Scottish activity including Highland projects. Headquartered in England; their mandate is national and their primary relationships are with UK government. Valuable as a research source and ally, but not a Scotland-specific public campaign.</p>
                    <a class="community-net-link" href="https://digitalpovertyalliance.org/"<?= external_link_attrs('https://digitalpovertyalliance.org/') ?>>digitalpovertyalliance.org &rarr;</a>
                </div>
            </div>

            <h2 style="margin-top:2.5rem">What WIRES does differently</h2>

            <div class="info-card" style="margin-bottom:1.5rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">We speak directly to residents</h2>
                    <p class="info-card__sub">Not to charities. Not to officials. To people.</p>
                </div>
                <div class="info-card__body">
                    <p>Every other body in this landscape speaks primarily to sector professionals, policymakers, or member organisations. WIRES is built for the resident who doesn't know what a social tariff is, the organiser who wants to raise connectivity at their community council, and the journalist who needs a credible campaign to quote.</p>
                </div>
            </div>

            <div class="info-card" style="margin-bottom:1.5rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">We are independent and non-party</h2>
                    <p class="info-card__sub">No government funding. No charter. No membership obligations.</p>
                </div>
                <div class="info-card__body">
                    <p>Connecting Scotland cannot criticise the Scottish Government — it is the Scottish Government. The Digital Inclusion Alliance has to maintain relationships with its funders. The Charter depends on keeping its signatories. WIRES has no such constraints. We can name what isn't working, without managing a relationship with the people responsible for it.</p>
                </div>
            </div>

            <div class="info-card" style="margin-bottom:1.5rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">We are rooted in local groups</h2>
                    <p class="info-card__sub">National voice, local presence.</p>
                </div>
                <div class="info-card__body">
                    <p>We are building a network of local WIRES groups across Scotland's 32 council areas — people who attend community council meetings, map gaps in their area, and feed local evidence back into national advocacy. No other organisation in this space does this.</p>
                    <p><a class="btn btn-ghost btn-sm" href="/groups">Find or start a local group &rarr;</a></p>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card__header">
                    <h2 class="info-card__heading">We argue it is a rights issue</h2>
                    <p class="info-card__sub">Not a charity appeal. Not a digital skills programme.</p>
                </div>
                <div class="info-card__body">
                    <p>Most digital inclusion work is framed around skills, access, and support — helping people who are "left behind" to catch up. WIRES frames it differently: connectivity is infrastructure, like roads or water, and being excluded from it is a structural failure, not a personal one. That framing changes what questions get asked and who is held responsible.</p>
                </div>
            </div>

            <div class="pull-quote" style="margin-top:2.5rem">
                <p>"You're not duplicating anyone. You're filling a real gap."</p>
            </div>

            <p>
                We work <em>alongside</em> SCVO, the Digital Poverty Alliance, and the organisations signed up to the Charter — sharing evidence, amplifying their research, and supporting their calls for action. But WIRES does something none of them do: campaign directly to the Scottish public, independently, from a rights-based position, with local groups on the ground.
            </p>

            <div class="callout">
                <p><strong>Want to be part of it?</strong> If your organisation supports this approach, <a href="/join-as-organisation">sign up as an organisational supporter</a>. If you're an individual, <a href="/join">join the mailing list</a>.</p>
            </div>

        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div>
    </div>
</div>

<?php
$ctaHeading = 'Help us fill the gap';
$ctaBody    = 'WIRES is volunteer-led. The more people and organisations that back it publicly, the harder the argument for connectivity rights is to ignore.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
