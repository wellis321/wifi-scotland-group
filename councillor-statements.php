<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/campaign-templates/councillor-public-statement.php';

const CAMPAIGN_SLUG = 'councillor-public-statement-2026-09';

// Rendered from the exact same template code the real sender uses, so this can never
// drift out of sync with what was actually sent — placeholder values only, not a real row.
$letterPreview = [
    'council'   => '[your council]',
    'full_name' => "[your councillor's name]",
];
$letterSubject = render_subject($letterPreview);
$letterBody    = render_text_body($letterPreview);

$pageTitle       = 'Have you heard from your councillor?';
$pageDescription = 'WIRES is writing to every councillor in Scotland — over 1,200 people — asking them to publicly back connectivity as essential infrastructure. Here is our progress, and what they\'ve said back.';
$currentNav      = 'councillorstatements';

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

// Target size: confirmed, emailable rows in the live roster (not the 32-council figure —
// this campaign writes to individual elected councillors, roughly 1,200 of them).
$csvPath = __DIR__ . '/data/councillors-roster.csv';
$targetCount = 0;
if (is_readable($csvPath)) {
    $fh = fopen($csvPath, 'r');
    if ($fh !== false) {
        $header = fgetcsv($fh, 0, ',', '"', '\\');
        $headerCount = count($header);
        while (($line = fgetcsv($fh, 0, ',', '"', '\\')) !== false) {
            if (count($line) < $headerCount) continue;
            if (count($line) > $headerCount) {
                $overflow = array_splice($line, $headerCount - 1);
                $line[] = implode(',', $overflow);
            }
            $row = array_combine($header, $line);
            if (($row['confidence'] ?? '') === 'confirmed' && trim((string) ($row['email'] ?? '')) !== '') {
                $targetCount++;
            }
        }
        fclose($fh);
    }
}

$sentCount      = 0;
$repliedCount   = 0;
$councilsReached = 0;
$firstSentDate  = null;
$quotes         = [];
$byCouncil      = [];

if (db_available()) {
    try {
        // Names + reply status only — never email addresses on the public list.
        $stmt = db()->prepare(
            "SELECT council_area, full_name, replied_at, public_quote
             FROM councillor_campaign_sends WHERE campaign_slug = ? AND status = 'sent'
             ORDER BY council_area ASC, full_name ASC"
        );
        $stmt->execute([CAMPAIGN_SLUG]);
        $rows = $stmt->fetchAll();

        $sentCount    = count($rows);
        $councilSet   = [];
        foreach ($rows as $r) {
            $councilSet[$r['council_area']] = true;
            $replied = !empty($r['replied_at']);
            if ($replied) $repliedCount++;
            if (!empty($r['public_quote'])) {
                $quotes[] = ['name' => $r['full_name'], 'council' => $r['council_area'], 'quote' => $r['public_quote']];
            }
            $byCouncil[$r['council_area']][] = ['name' => $r['full_name'], 'replied' => $replied];
        }
        $councilsReached = count($councilSet);

        $dateStmt = db()->prepare(
            "SELECT MIN(sent_at) AS first_sent FROM councillor_campaign_sends WHERE campaign_slug = ? AND status = 'sent'"
        );
        $dateStmt->execute([CAMPAIGN_SLUG]);
        $firstSentDate = $dateStmt->fetch()['first_sent'] ?? null;
    } catch (Throwable) {}
}

$remaining = max(0, $targetCount - $sentCount);
$isComplete = $targetCount > 0 && $sentCount >= $targetCount;

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Have you heard from your councillor?</h1>
        <p>Starting <?= $firstSentDate ? e(format_date(substr((string) $firstSentDate, 0, 10))) : 'September 2026' ?>, WIRES began writing to every councillor in Scotland — <?= $targetCount > 0 ? (string) $targetCount : 'over 1,200' ?> people across all 32 council areas — asking them to say publicly that everyone in their ward deserves reliable, affordable connectivity. This page tracks our progress and any public statements we've been given permission to share.</p>
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
                        We've now written to every confirmed councillor on our list — <strong><?= $sentCount ?></strong> people across <strong><?= $councilsReached ?></strong> council areas. This updates directly from our sending log, and we'll keep adding public statements here as councillors reply.
                    <?php else: ?>
                        <strong><?= $sentCount ?> of <?= $targetCount ?: '~1,200' ?></strong> councillors have been emailed so far, across <strong><?= $councilsReached ?></strong> of Scotland's 32 council areas. We're sending in daily batches and expect to finish reaching everyone within the next few days. This page updates directly from our sending log.
                    <?php endif; ?>
                </p>
            </div>

            <div class="stat-strip">
                <div class="stat-item">
                    <span class="stat-value"><?= $sentCount ?>/<?= $targetCount ?: '?' ?></span>
                    <span class="stat-label">councillors emailed</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= $councilsReached ?>/32</span>
                    <span class="stat-label">councils reached</span>
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
                    <p class="meta">Every councillor gets this same letter with their own name and council area merged in — rendered here from the exact same code that sends it, not a separate copy.</p>
                </div>
            </details>

            <?php if (!empty($byCouncil)): ?>
                <h2>Did your councillor reply?</h2>
                <p class="meta">Search by name or council area to see who we've written to so far, and whether they've replied.</p>

                <div class="callout" style="margin-bottom:1.25rem">
                    <label for="councillor-search" style="font-weight:700;display:block;margin-bottom:0.5rem">Search councillors</label>
                    <input type="search" id="councillor-search" class="councillor-search-input" placeholder="e.g. Glasgow, or a councillor's name" autocomplete="off">
                    <p class="meta" id="councillor-search-count" style="margin:0.5rem 0 0"></p>
                </div>

                <div id="councillor-list">
                    <?php foreach ($byCouncil as $council => $people):
                        $councilReplied = count(array_filter($people, static fn($p) => $p['replied']));
                        ?>
                        <details class="councillor-council-group" data-council="<?= e(strtolower($council)) ?>">
                            <summary>
                                <span><?= e($council) ?></span>
                                <span class="councillor-council-group__count">
                                    <?= count($people) ?> contacted<?= $councilReplied > 0 ? ', ' . $councilReplied . ' replied' : '' ?>
                                </span>
                            </summary>
                            <ul class="councillor-name-list">
                                <?php foreach ($people as $p): ?>
                                    <li data-name="<?= e(strtolower($p['name'])) ?>">
                                        <span><?= e($p['name']) ?></span>
                                        <?php if ($p['replied']): ?>
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
                    var input = document.getElementById('councillor-search');
                    var count = document.getElementById('councillor-search-count');
                    var groups = document.querySelectorAll('.councillor-council-group');
                    if (!input || !groups.length) return;

                    input.addEventListener('input', function () {
                        var q = input.value.trim().toLowerCase();
                        var totalVisible = 0;

                        groups.forEach(function (group) {
                            var councilMatch = q !== '' && (group.dataset.council || '').indexOf(q) !== -1;
                            var items = group.querySelectorAll('li');
                            var anyItemMatch = false;

                            items.forEach(function (li) {
                                var match = q === '' || councilMatch || (li.dataset.name || '').indexOf(q) !== -1;
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
            <?php endif; ?>

            <?php if (!empty($quotes)): ?>
                <h2>What councillors are saying</h2>
                <p class="meta">Only shown here when a councillor has been happy for their reply to be shared publicly — most replies are logged privately.</p>
                <div class="figure-log">
                    <?php foreach ($quotes as $q): ?>
                        <div class="figure-log__item">
                            <p class="figure-log__claim">&ldquo;<?= e($q['quote']) ?>&rdquo;</p>
                            <p class="figure-log__meta"><?= e($q['name']) ?>, <?= e($q['council']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <h2>What councillors are saying</h2>
                <p>No public statements to share yet — still waiting on replies. Check back soon.</p>
            <?php endif; ?>

            <div class="info-card" style="margin-top:2rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Are you a councillor who got this email?</h2>
                    <p class="info-card__sub">We'd like to hear from you</p>
                </div>
                <div class="info-card__body">
                    <p>Reply to the email directly, or <a href="/contact">get in touch</a> with a line we can quote, or to tell us where your council already stands on connectivity. We record every reply — public statements are only shared here with your say-so.</p>
                </div>
            </div>

        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div><!-- /page-layout -->
    </div>
</div>

<?php
$ctaHeading = 'Help us follow up';
$ctaBody    = 'Join WIRES and we\'ll keep you updated as councillors reply — and let you know how to add your own voice.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
