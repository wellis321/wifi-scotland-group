<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/campaign-templates/msp-accountability.php';
require_once __DIR__ . '/includes/campaign-templates/mp-accountability.php';

const MSP_CAMPAIGN_SLUG = 'msp-accountability-2026-09';
const MSP_TARGET_COUNT  = 129;
const MP_CAMPAIGN_SLUG  = 'mp-accountability-2026-09';
const MP_TARGET_COUNT   = 57;
const COUNCIL_TARGET_COUNT = 32;
const COUNCILLOR_TARGET_COUNT = 1208;

$pageTitle       = "We're writing to everyone who can act on this";
$pageDescription = 'WIRES is writing to every councillor, council Chief Executive, council Leader, MSP, and Scottish MP — asking them to back connectivity as essential infrastructure. Here is the full picture.';
$currentNav      = 'accountabilitycampaign';

$pageOgImage    = image_asset('card-community.jpg');
$pageOgImageAlt = 'Person writing at a table — representing constituent and campaign contact with elected representatives.';

$sidebarRelated = [
    ['href' => '/councillor-statements', 'label' => 'Councillor statements'],
    ['href' => '/council-replies',       'label' => 'Council replies'],
    ['href' => '/accountability',        'label' => 'Who is acting?'],
];

// Rendered from the exact same template code the real senders use.
$mspLetterPreview = ['name' => "[MSP's name]", 'role' => 'MSP for [constituency] (Constituency)'];
$mspLetterSubject = \Wires\MspTemplate\render_subject($mspLetterPreview);
$mspLetterBody    = \Wires\MspTemplate\render_text_body($mspLetterPreview);

$mpLetterPreview = ['name' => "[MP's name]", 'constituency' => '[constituency]'];
$mpLetterSubject = \Wires\MpTemplate\render_subject($mpLetterPreview);
$mpLetterBody    = \Wires\MpTemplate\render_text_body($mpLetterPreview);

$councillorSent = 0;
$councillorReplied = 0;
$councilCeoSent = 0;
$councilLeaderSent = 0;
$councilsReached = 0;
$councilReplied = 0;
$mspSent = 0;
$mspReplied = 0;
$mspByParty = [];
$mspQuotes = [];
$mpSent = 0;
$mpReplied = 0;
$mpByParty = [];
$mpQuotes = [];

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
            "SELECT SUM(outreach_sent_at IS NOT NULL) AS ceo_sent,
                    SUM(leader_sent_at IS NOT NULL) AS leader_sent,
                    SUM(outreach_sent_at IS NOT NULL OR leader_sent_at IS NOT NULL) AS reached,
                    SUM(outreach_replied_at IS NOT NULL) AS ceo_replied,
                    SUM(leader_replied_at IS NOT NULL) AS leader_replied
             FROM council_contacts"
        )->fetch();
        $councilCeoSent    = (int) ($row['ceo_sent'] ?? 0);
        $councilLeaderSent = (int) ($row['leader_sent'] ?? 0);
        $councilsReached   = (int) ($row['reached'] ?? 0);
        $councilReplied    = (int) ($row['ceo_replied'] ?? 0) + (int) ($row['leader_replied'] ?? 0);

        $mspRows = $pdo->prepare(
            "SELECT full_name, party, role, replied_at, public_quote
             FROM msp_campaign_sends WHERE campaign_slug = ? AND status = 'sent'
             ORDER BY party ASC, full_name ASC"
        );
        $mspRows->execute([MSP_CAMPAIGN_SLUG]);
        $mspRows = $mspRows->fetchAll();

        $mspSent = count($mspRows);
        foreach ($mspRows as $m) {
            if (!empty($m['replied_at'])) $mspReplied++;
            if (!empty($m['public_quote'])) {
                $mspQuotes[] = ['name' => $m['full_name'], 'role' => $m['role'], 'quote' => $m['public_quote']];
            }
            $mspByParty[$m['party']][] = ['name' => $m['full_name'], 'role' => $m['role'], 'replied' => !empty($m['replied_at'])];
        }

        $mpRows = $pdo->prepare(
            "SELECT full_name, party, constituency, replied_at, public_quote
             FROM mp_campaign_sends WHERE campaign_slug = ? AND status = 'sent'
             ORDER BY party ASC, full_name ASC"
        );
        $mpRows->execute([MP_CAMPAIGN_SLUG]);
        $mpRows = $mpRows->fetchAll();

        $mpSent = count($mpRows);
        foreach ($mpRows as $m) {
            if (!empty($m['replied_at'])) $mpReplied++;
            if (!empty($m['public_quote'])) {
                $mpQuotes[] = ['name' => $m['full_name'], 'role' => $m['constituency'], 'quote' => $m['public_quote']];
            }
            $mpByParty[$m['party']][] = ['name' => $m['full_name'], 'role' => $m['constituency'], 'replied' => !empty($m['replied_at'])];
        }
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

            <div class="stat-strip">
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

            <h2 style="margin-top:2.5rem" id="msps">MSPs at Holyrood</h2>
            <p>The gap Audit Scotland identified is a devolved, national one — so we're also writing to all 129 MSPs, asking them to raise it in Parliament: a written question to the Scottish Government, public backing, and committee scrutiny where relevant.</p>

            <details class="letter-preview">
                <summary>Read the letter we sent MSPs</summary>
                <div class="letter-preview__body">
                    <p class="letter-preview__subject"><strong>Subject:</strong> <?= e($mspLetterSubject) ?></p>
                    <pre class="letter-preview__text"><?= e($mspLetterBody) ?></pre>
                    <p class="meta">Every MSP gets this same letter with their own name and constituency or region merged in — rendered here from the exact same code that sends it.</p>
                </div>
            </details>

            <div class="stat-strip">
                <div class="stat-item">
                    <span class="stat-value"><?= $mspSent ?>/<?= MSP_TARGET_COUNT ?></span>
                    <span class="stat-label">MSPs emailed</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= $mspReplied ?></span>
                    <span class="stat-label">replies logged</span>
                </div>
            </div>

            <?php if (!empty($mspByParty)): ?>
                <h3 style="margin-top:2rem">Did your MSP reply?</h3>
                <p class="meta">Search by name to see who we've written to so far, and whether they've replied.</p>

                <div class="callout" style="margin-bottom:1.25rem">
                    <label for="msp-search" style="font-weight:700;display:block;margin-bottom:0.5rem">Search MSPs</label>
                    <input type="search" id="msp-search" class="councillor-search-input" placeholder="e.g. a name or party" autocomplete="off">
                    <p class="meta" id="msp-search-count" style="margin:0.5rem 0 0"></p>
                </div>

                <div id="msp-list">
                    <?php foreach ($mspByParty as $party => $msps):
                        $partyReplied = count(array_filter($msps, static fn($m) => $m['replied']));
                        ?>
                        <details class="councillor-council-group" data-council="<?= e(strtolower($party)) ?>">
                            <summary>
                                <span><?= e($party) ?></span>
                                <span class="councillor-council-group__count">
                                    <?= count($msps) ?> contacted<?= $partyReplied > 0 ? ', ' . $partyReplied . ' replied' : '' ?>
                                </span>
                            </summary>
                            <ul class="councillor-name-list">
                                <?php foreach ($msps as $m): ?>
                                    <li data-name="<?= e(strtolower($m['name'])) ?>">
                                        <span><?= e($m['name']) ?> <span class="meta">— <?= e($m['role']) ?></span></span>
                                        <?php if ($m['replied']): ?>
                                            <span class="pill pill--active">Replied</span>
                                        <?php else: ?>
                                            <span class="pill pill--forming">Awaiting reply</span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </details>
                    <?php endforeach; ?>
                </div>

                <script>
                (function () {
                    var input = document.getElementById('msp-search');
                    var count = document.getElementById('msp-search-count');
                    var groups = document.querySelectorAll('#msp-list .councillor-council-group');
                    if (!input || !groups.length) return;

                    input.addEventListener('input', function () {
                        var q = input.value.trim().toLowerCase();
                        var totalVisible = 0;

                        groups.forEach(function (group) {
                            var partyMatch = q !== '' && (group.dataset.council || '').indexOf(q) !== -1;
                            var items = group.querySelectorAll('li');
                            var anyItemMatch = false;

                            items.forEach(function (li) {
                                var match = q === '' || partyMatch || (li.dataset.name || '').indexOf(q) !== -1;
                                li.hidden = !match;
                                if (match) {
                                    anyItemMatch = true;
                                    totalVisible++;
                                }
                            });

                            if (q === '') {
                                group.hidden = false;
                                group.open = false;
                            } else {
                                group.hidden = !anyItemMatch;
                                group.open = anyItemMatch;
                            }
                        });

                        count.textContent = q === ''
                            ? ''
                            : totalVisible + ' match' + (totalVisible === 1 ? '' : 'es') + ' for "' + input.value.trim() + '"';
                    });
                })();
                </script>
            <?php else: ?>
                <p>Sending is about to begin — check back soon to see who's been contacted.</p>
            <?php endif; ?>

            <?php if (!empty($mspQuotes)): ?>
                <h3 style="margin-top:2rem">What MSPs are saying</h3>
                <p class="meta">Only shown here when an MSP has been happy for their reply to be shared publicly.</p>
                <div class="figure-log">
                    <?php foreach ($mspQuotes as $q): ?>
                        <div class="figure-log__item">
                            <p class="figure-log__claim">&ldquo;<?= e($q['quote']) ?>&rdquo;</p>
                            <p class="figure-log__meta"><?= e($q['name']) ?>, <?= e($q['role']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h2 style="margin-top:2.5rem" id="mps">MPs at Westminster</h2>
            <p>Digital inclusion policy itself is devolved — but telecoms regulation, Ofcom's Universal Service Obligation, and UK-wide funding programmes like Project Gigabit and the Shared Rural Network are reserved to Westminster. So we're also writing to all 57 Scottish MPs, asking them to press on those specifically.</p>

            <details class="letter-preview">
                <summary>Read the letter we sent MPs</summary>
                <div class="letter-preview__body">
                    <p class="letter-preview__subject"><strong>Subject:</strong> <?= e($mpLetterSubject) ?></p>
                    <pre class="letter-preview__text"><?= e($mpLetterBody) ?></pre>
                    <p class="meta">Every MP gets this same letter with their own name and constituency merged in — rendered here from the exact same code that sends it.</p>
                </div>
            </details>

            <div class="stat-strip">
                <div class="stat-item">
                    <span class="stat-value"><?= $mpSent ?>/<?= MP_TARGET_COUNT ?></span>
                    <span class="stat-label">MPs emailed</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= $mpReplied ?></span>
                    <span class="stat-label">replies logged</span>
                </div>
            </div>

            <?php if (!empty($mpByParty)): ?>
                <h3 style="margin-top:2rem">Did your MP reply?</h3>
                <p class="meta">Search by name to see who we've written to so far, and whether they've replied.</p>

                <div class="callout" style="margin-bottom:1.25rem">
                    <label for="mp-search" style="font-weight:700;display:block;margin-bottom:0.5rem">Search MPs</label>
                    <input type="search" id="mp-search" class="councillor-search-input" placeholder="e.g. a name or party" autocomplete="off">
                    <p class="meta" id="mp-search-count" style="margin:0.5rem 0 0"></p>
                </div>

                <div id="mp-list">
                    <?php foreach ($mpByParty as $party => $mps):
                        $partyReplied = count(array_filter($mps, static fn($m) => $m['replied']));
                        ?>
                        <details class="councillor-council-group" data-council="<?= e(strtolower($party)) ?>">
                            <summary>
                                <span><?= e($party) ?></span>
                                <span class="councillor-council-group__count">
                                    <?= count($mps) ?> contacted<?= $partyReplied > 0 ? ', ' . $partyReplied . ' replied' : '' ?>
                                </span>
                            </summary>
                            <ul class="councillor-name-list">
                                <?php foreach ($mps as $m): ?>
                                    <li data-name="<?= e(strtolower($m['name'])) ?>">
                                        <span><?= e($m['name']) ?> <span class="meta">— <?= e($m['role']) ?></span></span>
                                        <?php if ($m['replied']): ?>
                                            <span class="pill pill--active">Replied</span>
                                        <?php else: ?>
                                            <span class="pill pill--forming">Awaiting reply</span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </details>
                    <?php endforeach; ?>
                </div>

                <script>
                (function () {
                    var input = document.getElementById('mp-search');
                    var count = document.getElementById('mp-search-count');
                    var groups = document.querySelectorAll('#mp-list .councillor-council-group');
                    if (!input || !groups.length) return;

                    input.addEventListener('input', function () {
                        var q = input.value.trim().toLowerCase();
                        var totalVisible = 0;

                        groups.forEach(function (group) {
                            var partyMatch = q !== '' && (group.dataset.council || '').indexOf(q) !== -1;
                            var items = group.querySelectorAll('li');
                            var anyItemMatch = false;

                            items.forEach(function (li) {
                                var match = q === '' || partyMatch || (li.dataset.name || '').indexOf(q) !== -1;
                                li.hidden = !match;
                                if (match) {
                                    anyItemMatch = true;
                                    totalVisible++;
                                }
                            });

                            if (q === '') {
                                group.hidden = false;
                                group.open = false;
                            } else {
                                group.hidden = !anyItemMatch;
                                group.open = anyItemMatch;
                            }
                        });

                        count.textContent = q === ''
                            ? ''
                            : totalVisible + ' match' + (totalVisible === 1 ? '' : 'es') + ' for "' + input.value.trim() + '"';
                    });
                })();
                </script>
            <?php else: ?>
                <p>Sending is about to begin — check back soon to see who's been contacted.</p>
            <?php endif; ?>

            <?php if (!empty($mpQuotes)): ?>
                <h3 style="margin-top:2rem">What MPs are saying</h3>
                <p class="meta">Only shown here when an MP has been happy for their reply to be shared publicly.</p>
                <div class="figure-log">
                    <?php foreach ($mpQuotes as $q): ?>
                        <div class="figure-log__item">
                            <p class="figure-log__claim">&ldquo;<?= e($q['quote']) ?>&rdquo;</p>
                            <p class="figure-log__meta"><?= e($q['name']) ?>, <?= e($q['role']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

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
