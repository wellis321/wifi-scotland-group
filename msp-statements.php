<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/campaign-templates/msp-accountability.php';

const CAMPAIGN_SLUG = 'msp-accountability-2026-09';
const TARGET_COUNT  = 129;

// Rendered from the exact same template code the real sender uses, so this can never
// drift out of sync with what was actually sent — placeholder values only, not a real row.
$letterPreview = ['name' => "[MSP's name]", 'role' => 'MSP for [constituency] (Constituency)'];
$letterSubject = \Wires\MspTemplate\render_subject($letterPreview);
$letterBody    = \Wires\MspTemplate\render_text_body($letterPreview);

$pageTitle       = 'Have you heard from your MSP?';
$pageDescription = 'WIRES is writing to every MSP at Holyrood — 129 people — asking them to raise Scotland\'s missing digital exclusion plan in Parliament. Here is our progress, and what they\'ve said back.';
$currentNav      = 'mspstatements';

$pageOgImage    = image_asset('card-community.jpg');
$pageOgImageAlt = 'Person writing at a table — representing constituent and campaign contact with elected representatives.';

$sidebarRelated = [
    ['href' => '/accountability-campaign', 'label' => 'Full campaign overview'],
    ['href' => '/councillor-statements',   'label' => 'Councillor statements'],
    ['href' => '/council-replies',         'label' => 'Council replies'],
    ['href' => '/mp-statements',           'label' => 'MP statements'],
];

$sentCount    = 0;
$repliedCount = 0;
$quotes       = [];
$byParty      = [];

if (db_available()) {
    try {
        $stmt = db()->prepare(
            "SELECT full_name, party, role, replied_at, public_quote
             FROM msp_campaign_sends WHERE campaign_slug = ? AND status = 'sent'
             ORDER BY party ASC, full_name ASC"
        );
        $stmt->execute([CAMPAIGN_SLUG]);
        $rows = $stmt->fetchAll();

        $sentCount = count($rows);
        foreach ($rows as $r) {
            $replied = !empty($r['replied_at']);
            if ($replied) $repliedCount++;
            if (!empty($r['public_quote'])) {
                $quotes[] = ['name' => $r['full_name'], 'role' => $r['role'], 'quote' => $r['public_quote']];
            }
            $byParty[$r['party']][] = ['name' => $r['full_name'], 'role' => $r['role'], 'replied' => $replied];
        }
    } catch (Throwable) {}
}

$isComplete = $sentCount >= TARGET_COUNT;

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Have you heard from your MSP?</h1>
        <p>The gap Audit Scotland identified is a devolved, national one — so WIRES is writing to every one of Scotland's 129 MSPs, asking them to raise it in Parliament: a written question to the Scottish Government, public backing, and committee scrutiny where relevant. This page tracks our progress and any public statements we've been given permission to share.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">
        <div class="prose">

            <div class="callout">
                <p class="callout__eyebrow">How this page works</p>
                <p>
                    <?php if ($isComplete): ?>
                        We've now written to every MSP at Holyrood — <strong><?= $sentCount ?></strong> people. This updates directly from our sending log, and we'll keep adding public statements here as MSPs reply.
                    <?php else: ?>
                        <strong><?= $sentCount ?> of <?= TARGET_COUNT ?></strong> MSPs have been emailed so far. This page updates directly from our sending log.
                    <?php endif; ?>
                </p>
            </div>

            <div class="stat-strip">
                <div class="stat-item">
                    <span class="stat-value"><?= $sentCount ?>/<?= TARGET_COUNT ?></span>
                    <span class="stat-label">MSPs emailed</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= $repliedCount ?></span>
                    <span class="stat-label">replies logged</span>
                </div>
            </div>

            <details class="letter-preview">
                <summary>Read the letter we sent</summary>
                <div class="letter-preview__body">
                    <p class="letter-preview__subject"><strong>Subject:</strong> <?= e($letterSubject) ?></p>
                    <pre class="letter-preview__text"><?= e($letterBody) ?></pre>
                    <p class="meta">Every MSP gets this same letter with their own name and constituency or region merged in — rendered here from the exact same code that sends it, not a separate copy.</p>
                </div>
            </details>

            <?php if (!empty($byParty)): ?>
                <h2>Did your MSP reply?</h2>
                <p class="meta">Search by name or party to see who we've written to so far, and whether they've replied.</p>

                <div class="callout" style="margin-bottom:1.25rem">
                    <label for="msp-search" style="font-weight:700;display:block;margin-bottom:0.5rem">Search MSPs</label>
                    <input type="search" id="msp-search" class="councillor-search-input" placeholder="e.g. a name or party" autocomplete="off">
                    <p class="meta" id="msp-search-count" style="margin:0.5rem 0 0"></p>
                </div>

                <div id="msp-list">
                    <?php foreach ($byParty as $party => $msps):
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
                <h2>Did your MSP reply?</h2>
                <p>Sending is about to begin — check back soon to see who's been contacted.</p>
            <?php endif; ?>

            <?php if (!empty($quotes)): ?>
                <h2>What MSPs are saying</h2>
                <p class="meta">Only shown here when an MSP has been happy for their reply to be shared publicly — most replies are logged privately.</p>
                <div class="figure-log">
                    <?php foreach ($quotes as $q): ?>
                        <div class="figure-log__item">
                            <p class="figure-log__claim">&ldquo;<?= e($q['quote']) ?>&rdquo;</p>
                            <p class="figure-log__meta"><?= e($q['name']) ?>, <?= e($q['role']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <h2>What MSPs are saying</h2>
                <p>No public statements to share yet — still waiting on replies. Check back soon.</p>
            <?php endif; ?>

            <div class="info-card" style="margin-top:2rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Are you an MSP who got this email?</h2>
                    <p class="info-card__sub">We'd like to hear from you</p>
                </div>
                <div class="info-card__body">
                    <p>Reply to the email directly, or <a href="/contact">get in touch</a> with a line we can quote, or to tell us what action you've taken. We record every reply — public statements are only shared here with your say-so.</p>
                </div>
            </div>

        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div><!-- /page-layout -->
    </div>
</div>

<?php
$ctaHeading = 'Help us follow up';
$ctaBody    = 'Join WIRES and we\'ll keep you updated as MSPs reply — and let you know how to add your own voice.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
