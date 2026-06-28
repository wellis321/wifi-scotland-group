<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/scotland_positive_stories.php';

$pageTitle = 'Scotland: positive connectivity stories';
$pageDescription = 'Eight verifiable Scottish stories on broadband roll-out, inclusion programmes, libraries, and local strategy—with primary source links.';
$currentNav = 'scotlandstories';

$stories = load_scotland_positive_stories();

$pageOgImage = image_asset('scotland-landscape.jpg');
$pageOgImageAlt = 'Sunlit Scottish landscape—backdrop for curated connectivity success stories with official sources.';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Scotland: positive connectivity stories</h1>
        <p>
            Eight real examples from Scotland—national programmes, community broadband projects, library networks, and council plans. Each one links to the original source so you can check the details for yourself before quoting figures.
        </p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <?php if (!$stories): ?>
            <div class="callout prose">
                <p><strong>This list is temporarily unavailable.</strong> Please try again later or <a href="/contact.php">get in touch</a> if the problem persists.</p>
            </div>
        <?php else: ?>
            <p class="section-intro">
                These entries are curated third-party sources with links to the original publishers—not campaign news posts from this site.
                For editorials and updates published here, see <a href="/news.php">News</a>.
            </p>
            <div class="card-grid cols-2 scotland-story-grid">
                <?php foreach ($stories as $story): ?>
                    <article class="card scotland-story-card">
                        <p class="meta"><?= e($story['when'] !== '' ? $story['when'] : 'Date on source') ?></p>
                        <h3 class="scotland-story-card__title"><?= e($story['title']) ?></h3>
                        <p class="scotland-story-card__summary"><?= e($story['summary']) ?></p>
                        <div class="scotland-story-card__links">
                            <a href="<?= e($story['primary_url']) ?>"<?= external_link_attrs($story['primary_url']) ?>><?= e($story['primary_label']) ?></a>
                            <?php if (!empty($story['secondary_url']) && !empty($story['secondary_label'])): ?>
                                <span class="scotland-story-card__also" aria-hidden="true">·</span>
                                <a href="<?= e($story['secondary_url']) ?>"<?= external_link_attrs($story['secondary_url']) ?>><?= e($story['secondary_label']) ?></a>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($story['caveat'])): ?>
                            <p class="scotland-story-card__caveat"><strong>Note:</strong> <?= e($story['caveat']) ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="callout prose scotland-stories-footer-callout">
            <p>Policy context and official programme hubs remain on <a href="/scotland.php">Scotland: government &amp; councils</a>. Suggest additions or corrections via <a href="/contact.php">Contact</a>.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
