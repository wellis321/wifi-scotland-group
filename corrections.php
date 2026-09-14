<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/figures_data.php';

$pageTitle       = 'Corrections';
$pageDescription = 'Every correction made to a figure or claim on ' . SITE_BRAND . ', with what changed and why. If we get something wrong, it goes here — not just quietly fixed.';
$currentNav      = 'corrections';

$pageOgImage    = image_asset('card-fibre.jpg');
$pageOgImageAlt = 'Fibre-optic cables close-up — representing the evidence behind the campaign.';

$sidebarRelated = [
    ['href' => '/figures',        'label' => 'Figures & sources'],
    ['href' => '/resources',      'label' => 'Resources & references'],
    ['href' => '/contact',        'label' => 'Report an error'],
];

/*
 * Auto-derived from includes/figures_data.php — any figure whose note starts
 * with "Corrected" appears here automatically. To log a new correction, edit
 * the relevant entry's 'note' field there (and set 'corrected_on' to today's
 * date, 'YYYY-MM-DD') — nothing needs to be duplicated here.
 */
$corrections = [];
foreach (load_figures() as $groupName => $rows) {
    foreach ($rows as $row) {
        if (!empty($row['note']) && str_starts_with($row['note'], 'Corrected')) {
            $row['group'] = $groupName;
            $corrections[] = $row;
        }
    }
}

// Most recently corrected first — 'corrected_on' is when the fix was made, distinct
// from 'date' (which is the underlying source/claim's own date and can be older).
usort($corrections, static fn($a, $b) => strcmp((string) ($b['corrected_on'] ?? ''), (string) ($a['corrected_on'] ?? '')));

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Corrections</h1>
        <p>We publish a lot of statistics. Numbers move, sources get misread, and errors happen — what matters is what we do when we find one. Every correction made to a figure on this site is listed below: what it said, what it says now, and why. Nothing here is quietly fixed.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">
        <div class="prose">

            <div class="callout">
                <p class="callout__eyebrow">How this page works</p>
                <p>This list is generated directly from our <a href="/figures">Figures &amp; sources</a> fact-check log — every figure whose entry notes a correction appears here automatically, so there's no separate step where one could quietly go missing. <?= count($corrections) ?> correction<?= count($corrections) === 1 ? '' : 's' ?> logged so far, out of 35 figures we track, most recent first.</p>
            </div>

            <?php if (empty($corrections)): ?>
                <p>No corrections logged yet.</p>
            <?php else: ?>
                <?php if (count($corrections) > 6): ?>
                <div class="callout" style="margin-bottom:1.25rem">
                    <label for="corrections-search" style="font-weight:700;display:block;margin-bottom:0.5rem">Search corrections</label>
                    <input type="search" id="corrections-search" class="councillor-search-input" placeholder="Search by topic or keyword" autocomplete="off">
                    <p class="meta" id="corrections-search-count" style="margin:0.5rem 0 0"></p>
                </div>
                <?php endif; ?>
                <div class="figure-log" id="corrections-list">
                    <?php foreach ($corrections as $row): ?>
                        <div class="figure-log__item" data-search="<?= e(strtolower($row['group'] . ' ' . $row['claim'] . ' ' . $row['note'])) ?>">
                            <p class="meta" style="margin-bottom:0.35rem"><?= e($row['group']) ?></p>
                            <p class="figure-log__claim">Now reads: &ldquo;<?= e($row['claim']) ?>&rdquo;</p>
                            <p class="figure-log__note"><?= e($row['note']) ?></p>
                            <p class="figure-log__meta">
                                <span class="figure-log__source">
                                    <a href="<?= e($row['url']) ?>"<?= external_link_attrs($row['url']) ?>><?= e($row['source']) ?></a>
                                </span>
                                <span class="figure-log__date">Source dated <?= e($row['date']) ?></span>
                                <?php if (!empty($row['corrected_on'])): ?>
                                    <span class="figure-log__date">&middot; Corrected <?= e(format_date($row['corrected_on'])) ?></span>
                                <?php endif; ?>
                            </p>
                            <p class="figure-log__used-on">
                                Used on:
                                <?php foreach ($row['used_on'] as $i => $page): ?><?= $i > 0 ? ' · ' : ' ' ?><a href="<?= e($page['href']) ?>"><?= e($page['label']) ?></a><?php endforeach; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($corrections) > 6): ?>
                <script>
                (function () {
                    var input = document.getElementById('corrections-search');
                    var count = document.getElementById('corrections-search-count');
                    var items = document.querySelectorAll('#corrections-list .figure-log__item');
                    if (!input) return;
                    input.addEventListener('input', function () {
                        var q = input.value.trim().toLowerCase();
                        var visible = 0;
                        items.forEach(function (item) {
                            var match = q === '' || (item.dataset.search || '').indexOf(q) !== -1;
                            item.hidden = !match;
                            if (match) visible++;
                        });
                        count.textContent = q === '' ? '' : visible + ' match' + (visible === 1 ? '' : 'es') + ' for "' + input.value.trim() + '"';
                    });
                })();
                </script>
                <?php endif; ?>
            <?php endif; ?>

            <div class="info-card" style="margin-top:2rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Spot something wrong?</h2>
                    <p class="info-card__sub">Tell us and we'll fix it — then log it here</p>
                </div>
                <div class="info-card__body">
                    <p>If a figure on the site is out of date, misattributed, or just doesn't match its source, <a href="/contact">get in touch</a>. We'd rather hear it from you than from someone quoting us incorrectly.</p>
                </div>
            </div>

        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div><!-- /page-layout -->
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
