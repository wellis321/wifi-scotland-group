<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Figures & sources — the fact-check log';
$pageDescription = 'Every statistic used across ' . SITE_BRAND . ', with the primary source it comes from and the date it was last checked.';
$currentNav      = 'figures';

$pageOgImage    = image_asset('card-fibre.jpg');
$pageOgImageAlt = 'Fibre-optic cables close-up — representing the evidence behind the campaign.';

$sidebarRelated = [
    ['href' => '/resources',      'label' => 'Resources & references'],
    ['href' => '/why-it-matters', 'label' => 'Why it matters'],
    ['href' => '/accountability', 'label' => 'Who is acting?'],
];

require_once __DIR__ . '/includes/figures_data.php';
$figureGroups = load_figures();

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Figures &amp; sources</h1>
        <p>Every statistic used across <?= e(SITE_BRAND) ?>, with the primary source behind it. We checked every figure on this page against its original publication in July 2026 — corrections made during that check are noted inline. Numbers move; check the linked source before quoting anything in a briefing, council question, or press enquiry.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">
        <div class="prose">

            <div class="callout">
                <p class="callout__eyebrow">How to use this page</p>
                <p>Each row shows a figure as it appears on the site, which page(s) use it, the primary source, and a caveat if one exists. If you're re-checking the site's accuracy, this is the list to work through — set a reminder to review it every few months, since regulator and government figures move on their own schedule regardless of ours. Entries marked as corrected also appear on our <a href="/corrections">Corrections</a> page.</p>
            </div>

            <?php foreach ($figureGroups as $groupName => $rows): ?>
                <h2><?= e($groupName) ?></h2>
                <div class="figure-log">
                    <?php foreach ($rows as $row): ?>
                        <div class="figure-log__item">
                            <p class="figure-log__claim"><?= e($row['claim']) ?></p>
                            <p class="figure-log__meta">
                                <span class="figure-log__source">
                                    <a href="<?= e($row['url']) ?>"<?= external_link_attrs($row['url']) ?>><?= e($row['source']) ?></a>
                                </span>
                                <span class="figure-log__date"><?= e($row['date']) ?></span>
                            </p>
                            <p class="figure-log__used-on">
                                Used on:
                                <?php foreach ($row['used_on'] as $i => $page): ?><?= $i > 0 ? ' · ' : ' ' ?><a href="<?= e($page['href']) ?>"><?= e($page['label']) ?></a><?php endforeach; ?>
                            </p>
                            <?php if (!empty($row['note'])): ?>
                                <p class="figure-log__note"><?= e($row['note']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <div class="info-card" style="margin-top:2rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Spot something wrong?</h2>
                    <p class="info-card__sub">Tell us and we'll fix it</p>
                </div>
                <div class="info-card__body">
                    <p>If a figure here is out of date, a link is broken, or you think we've misread a source, <a href="/contact">get in touch</a>. Getting this right matters more than any individual number looking impressive.</p>
                </div>
            </div>

            <p class="meta">This page does not duplicate every link in <a href="/resources">Resources &amp; references</a> — that page is the general reading list; this one traces specific figures back to specific sources.</p>

        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div><!-- /page-layout -->
    </div>
</div>

<?php
$ctaHeading = 'Something missing?';
$ctaBody    = 'If you know of a source we should add or a figure that needs updating, tell us via the contact form.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
