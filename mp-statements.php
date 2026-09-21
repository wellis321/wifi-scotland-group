<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/campaign-templates/mp-accountability.php';

const CAMPAIGN_SLUG = 'mp-accountability-2026-09';
const TARGET_COUNT  = 57;

// Rendered from the exact same template code the real sender uses, so this can never
// drift out of sync with what was actually sent — placeholder values only, not a real row.
$letterPreview = ['name' => "[MP's name]", 'constituency' => '[constituency]'];
$letterSubject = \Wires\MpTemplate\render_subject($letterPreview);
$letterBody    = \Wires\MpTemplate\render_text_body($letterPreview);

$pageTitle       = 'Have you heard from your MP?';
$pageDescription = 'WIRES is writing to every Scottish MP at Westminster — 57 people — asking them to press on reserved telecoms and connectivity matters. Here is our progress, and what they\'ve said back.';
$currentNav      = 'mpstatements';

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

$sentCount    = 0;
$repliedCount = 0;
$quotes       = [];
$byParty      = [];

if (db_available()) {
    try {
        $stmt = db()->prepare(
            "SELECT full_name, party, constituency, replied_at, public_quote
             FROM mp_campaign_sends WHERE campaign_slug = ? AND status = 'sent'
             ORDER BY party ASC, full_name ASC"
        );
        $stmt->execute([CAMPAIGN_SLUG]);
        $rows = $stmt->fetchAll();

        $sentCount = count($rows);
        foreach ($rows as $r) {
            $replied = !empty($r['replied_at']);
            if ($replied) $repliedCount++;
            if (!empty($r['public_quote'])) {
                $quotes[] = ['name' => $r['full_name'], 'role' => $r['constituency'], 'quote' => $r['public_quote']];
            }
            $byParty[$r['party']][] = ['name' => $r['full_name'], 'role' => $r['constituency'], 'replied' => $replied];
        }
    } catch (Throwable) {}
}

$isComplete = $sentCount >= TARGET_COUNT;

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Have you heard from your MP?</h1>
        <p>Digital inclusion policy itself is devolved — but telecoms regulation, Ofcom's Universal Service Obligation, and UK-wide funding programmes like Project Gigabit and the Shared Rural Network are reserved to Westminster. So WIRES is writing to all 57 Scottish MPs, asking them to press on those specifically. This page tracks our progress and any public statements we've been given permission to share.</p>
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
                        We've now written to every Scottish MP at Westminster — <strong><?= $sentCount ?></strong> people. This updates directly from our sending log, and we'll keep adding public statements here as MPs reply.
                    <?php else: ?>
                        <strong><?= $sentCount ?> of <?= TARGET_COUNT ?></strong> MPs have been emailed so far. This page updates directly from our sending log.
                    <?php endif; ?>
                </p>
            </div>

            <div class="stat-strip">
                <div class="stat-item">
                    <span class="stat-value"><?= $sentCount ?>/<?= TARGET_COUNT ?></span>
                    <span class="stat-label">MPs emailed</span>
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
                    <p class="meta">Every MP gets this same letter with their own name and constituency merged in — rendered here from the exact same code that sends it, not a separate copy.</p>
                </div>
            </details>

            <?php if (!empty($byParty)): ?>
                <h2>Did your MP reply?</h2>
                <p class="meta">Search by name or party to see who we've written to so far, and whether they've replied.</p>

                <div class="callout" style="margin-bottom:1.25rem">
                    <label for="mp-search" style="font-weight:700;display:block;margin-bottom:0.5rem">Search MPs</label>
                    <input type="search" id="mp-search" class="councillor-search-input" placeholder="e.g. a name or party" autocomplete="off">
                    <p class="meta" id="mp-search-count" style="margin:0.5rem 0 0"></p>
                </div>

                <div id="mp-list">
                    <?php foreach ($byParty as $party => $mps):
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
                <h2>Did your MP reply?</h2>
                <p>Sending is about to begin — check back soon to see who's been contacted.</p>
            <?php endif; ?>

            <?php if (!empty($quotes)): ?>
                <h2>What MPs are saying</h2>
                <p class="meta">Only shown here when an MP has been happy for their reply to be shared publicly — most replies are logged privately.</p>
                <div class="figure-log">
                    <?php foreach ($quotes as $q): ?>
                        <div class="figure-log__item">
                            <p class="figure-log__claim">&ldquo;<?= e($q['quote']) ?>&rdquo;</p>
                            <p class="figure-log__meta"><?= e($q['name']) ?>, <?= e($q['role']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <h2>What MPs are saying</h2>
                <p>No public statements to share yet — still waiting on replies. Check back soon.</p>
            <?php endif; ?>

            <div class="info-card" style="margin-top:2rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Are you an MP who got this email?</h2>
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
$ctaBody    = 'Join WIRES and we\'ll keep you updated as MPs reply — and let you know how to add your own voice.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
