<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

const COUNCIL_TARGET_COUNT = 32;
const COUNCILLOR_TARGET_COUNT = 1208;
const MSP_TARGET_COUNT  = 129;
const MP_TARGET_COUNT   = 57;

$pageTitle       = "We're writing to everyone who can act on this";
$pageDescription = 'WIRES is writing to every councillor, council Chief Executive, council Leader, MSP, and Scottish MP — asking them to back connectivity as essential infrastructure. Here is the full picture.';
$currentNav      = 'accountabilitycampaign';

$pageOgImage    = image_asset('card-community.jpg');
$pageOgImageAlt = 'Person writing at a table — representing constituent and campaign contact with elected representatives.';

$sidebarRelated = [
    ['href' => '/accountability-campaign', 'label' => 'Full campaign overview'],
    ['href' => '/councillor-statements',   'label' => 'Councillor statements'],
    ['href' => '/council-replies',         'label' => 'Council replies'],
    ['href' => '/msp-statements',          'label' => 'MSP statements'],
    ['href' => '/mp-statements',           'label' => 'MP statements'],
    ['href' => '/accountability',          'label' => 'Who is acting?'],
];

$councillorSent = 0;
$councillorReplied = 0;
$councilsReached = 0;
$councilReplied = 0;
$mspSent = 0;
$mspReplied = 0;
$mpSent = 0;
$mpReplied = 0;

if (db_available()) {
    try {
        $pdo = db();

        $row = $pdo->query(
            "SELECT COUNT(*) AS sent, SUM(replied_at IS NOT NULL) AS replied
             FROM councillor_campaign_sends WHERE campaign_slug = 'councillor-public-statement-2026-09' AND status = 'sent'"
        )->fetch();
        $councillorSent    = (int) ($row['sent'] ?? 0);
        $councillorReplied = (int) ($row['replied'] ?? 0);

        $row = $pdo->query(
            "SELECT SUM(outreach_sent_at IS NOT NULL OR leader_sent_at IS NOT NULL) AS reached,
                    SUM(outreach_replied_at IS NOT NULL) AS ceo_replied,
                    SUM(leader_replied_at IS NOT NULL) AS leader_replied
             FROM council_contacts"
        )->fetch();
        $councilsReached = (int) ($row['reached'] ?? 0);
        $councilReplied  = (int) ($row['ceo_replied'] ?? 0) + (int) ($row['leader_replied'] ?? 0);

        $row = $pdo->query(
            "SELECT COUNT(*) AS sent, SUM(replied_at IS NOT NULL) AS replied
             FROM msp_campaign_sends WHERE campaign_slug = 'msp-accountability-2026-09' AND status = 'sent'"
        )->fetch();
        $mspSent    = (int) ($row['sent'] ?? 0);
        $mspReplied = (int) ($row['replied'] ?? 0);

        $row = $pdo->query(
            "SELECT COUNT(*) AS sent, SUM(replied_at IS NOT NULL) AS replied
             FROM mp_campaign_sends WHERE campaign_slug = 'mp-accountability-2026-09' AND status = 'sent'"
        )->fetch();
        $mpSent    = (int) ($row['sent'] ?? 0);
        $mpReplied = (int) ($row['replied'] ?? 0);
    } catch (Throwable) {}
}

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>We're writing to everyone who can act on this</h1>
        <p>Audit Scotland found no plan and no one accountable for closing Scotland's digital exclusion gap. Rather than wait, we're writing directly to everyone with a lever to pull: every councillor, every council Chief Executive, every council Leader, every MSP at Holyrood, and every Scottish MP at Westminster — over 1,450 people in total. This page is the full picture of that effort.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">
        <div class="prose">

            <div class="stat-strip stat-strip--balanced">
                <div class="stat-item">
                    <span class="stat-value"><?= $councillorSent ?>/<?= COUNCILLOR_TARGET_COUNT ?></span>
                    <span class="stat-label">councillors emailed</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= $councilsReached ?>/<?= COUNCIL_TARGET_COUNT ?></span>
                    <span class="stat-label">councils reached (CEO or Leader)</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= $mspSent ?>/<?= MSP_TARGET_COUNT ?></span>
                    <span class="stat-label">MSPs emailed (Holyrood)</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= $mpSent ?>/<?= MP_TARGET_COUNT ?></span>
                    <span class="stat-label">MPs emailed (Westminster)</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= $councillorReplied + $councilReplied + $mspReplied + $mpReplied ?></span>
                    <span class="stat-label">replies logged so far</span>
                </div>
            </div>

            <h2>Individual councillors</h2>
            <p>We're writing to every one of Scotland's ~1,200 confirmed councillors, asking them to say publicly that everyone in their ward deserves reliable, affordable connectivity.</p>
            <p><a class="btn btn-ghost btn-sm" href="/councillor-statements">Full councillor tracker &rarr;</a></p>

            <h2 style="margin-top:2.5rem">Councils: Chief Executives and Leaders</h2>
            <p>Separately, we've written to the Chief Executive and the political Leader of every one of Scotland's 32 councils, asking four practical questions about staff training, affordability, service resilience, and who's accountable for a published digital inclusion plan.</p>
            <p><a class="btn btn-ghost btn-sm" href="/council-replies">Full council tracker &rarr;</a></p>

            <h2 style="margin-top:2.5rem">MSPs at Holyrood</h2>
            <p>The gap Audit Scotland identified is a devolved, national one — so we're also writing to all 129 MSPs, asking them to raise it in Parliament: a written question to the Scottish Government, public backing, and committee scrutiny where relevant.</p>
            <p><a class="btn btn-ghost btn-sm" href="/msp-statements">Full MSP tracker &rarr;</a></p>

            <h2 style="margin-top:2.5rem">MPs at Westminster</h2>
            <p>Digital inclusion policy itself is devolved — but telecoms regulation, Ofcom's Universal Service Obligation, and UK-wide funding programmes like Project Gigabit and the Shared Rural Network are reserved to Westminster. So we're also writing to all 57 Scottish MPs, asking them to press on those specifically.</p>
            <p><a class="btn btn-ghost btn-sm" href="/mp-statements">Full MP tracker &rarr;</a></p>

            <div class="info-card" style="margin-top:2.5rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Are you one of the people we've written to?</h2>
                    <p class="info-card__sub">We'd like to hear from you</p>
                </div>
                <div class="info-card__body">
                    <p>Reply to the email directly, or <a href="/contact">get in touch</a> with a line we can quote, or to tell us where things stand. We record every reply — public statements are only shared here with your say-so.</p>
                </div>
            </div>

        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div><!-- /page-layout -->
    </div>
</div>

<?php
$ctaHeading = 'Help us follow up';
$ctaBody    = 'Join WIRES and we\'ll keep you updated as replies come in from every corner of this campaign.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
