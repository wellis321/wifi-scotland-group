<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'News & updates';
$pageDescription = 'Campaign notes, explainers, and pointers to official connectivity updates in Scotland.';
$currentNav      = 'news';

$items = [];
$dbOk  = db_available();
if ($dbOk) {
    try {
        $items = db()->query(
            'SELECT title, slug, summary, published_at, image_filename FROM news_items ORDER BY published_at DESC, id DESC'
        )->fetchAll();
    } catch (Throwable) {
        $items = [];
        $dbOk  = false;
    }
}

$featured  = $items[0] ?? null;
$remaining = array_slice($items, 1);

$pageOgImage    = image_asset('card-fibre.jpg');
$pageOgImageAlt = 'Fibre-optic cables close-up — representing physical internet infrastructure.';

$sidebarRelated = [
    ['href' => '/scotland.php',       'label' => 'Scotland policy'],
    ['href' => '/why-it-matters.php', 'label' => 'Why it matters'],
    ['href' => '/resources.php',      'label' => 'Resources & references'],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>News &amp; updates</h1>
        <p>Editorial pieces from WIRES plus pointers to consultations, council decisions, and programme changes.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">

            <div><!-- main column -->

            <?php if (!$dbOk): ?>
                <div class="callout prose">
                    <p><strong>Connect the database</strong> to load articles. Import <code>schema.sql</code> and configure your <code>.env</code> — see the README.</p>
                </div>

            <?php elseif (!$items): ?>
                <p class="section-intro">No articles yet. Add rows to the <code>news_items</code> table or re-import <code>schema.sql</code>.</p>

            <?php else: ?>

                <?php if ($featured):
                    $featHasImg = !empty($featured['image_filename']);
                    $featTs     = strtotime((string) $featured['published_at']);
                ?>
                <article class="featured-article">
                    <?php if ($featHasImg): ?>
                    <figure class="featured-article-img" aria-hidden="true">
                        <img src="<?= e(image_asset((string) $featured['image_filename'])) ?>" width="1200" height="800" alt="" decoding="async" loading="eager">
                    </figure>
                    <?php else: ?>
                    <div class="featured-article-img featured-article-img--datestamp" aria-hidden="true">
                        <div class="news-datestamp">
                            <span class="news-datestamp__day"><?= $featTs ? date('j', $featTs) : '' ?></span>
                            <span class="news-datestamp__month"><?= $featTs ? date('M', $featTs) : '' ?></span>
                            <span class="news-datestamp__year"><?= $featTs ? date('Y', $featTs) : '' ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="featured-article-body">
                        <p class="meta"><time datetime="<?= e((string) $featured['published_at']) ?>"><?= e(format_date((string) $featured['published_at'])) ?></time></p>
                        <h2><a href="<?= e('/news-item.php?slug=' . rawurlencode((string) $featured['slug'])) ?>"><?= e((string) $featured['title']) ?></a></h2>
                        <?php if (!empty($featured['summary'])): ?>
                            <p><?= e((string) $featured['summary']) ?></p>
                        <?php endif; ?>
                        <a class="btn btn-primary" href="<?= e('/news-item.php?slug=' . rawurlencode((string) $featured['slug'])) ?>">Read article &rarr;</a>
                    </div>
                </article>
                <?php endif; ?>

                <?php if ($remaining): ?>
                    <h2 style="font-family:var(--font-display);font-size:1.35rem;font-weight:800;margin:0 0 1.25rem;color:var(--ink)">More articles</h2>
                    <div class="news-card-grid">
                    <?php foreach ($remaining as $row):
                        $hasImg = !empty($row['image_filename']);
                        $ts     = strtotime((string) $row['published_at']);
                    ?>
                        <article class="news-card">
                            <?php if ($hasImg): ?>
                            <div class="news-card-thumb" aria-hidden="true">
                                <img src="<?= e(image_asset((string) $row['image_filename'])) ?>" width="600" height="400" alt="" decoding="async" loading="lazy">
                            </div>
                            <?php else: ?>
                            <div class="news-card-thumb news-card-thumb--datestamp" aria-hidden="true">
                                <div class="news-datestamp">
                                    <span class="news-datestamp__day"><?= $ts ? date('j', $ts) : '' ?></span>
                                    <span class="news-datestamp__month"><?= $ts ? date('M', $ts) : '' ?></span>
                                    <span class="news-datestamp__year"><?= $ts ? date('Y', $ts) : '' ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="news-card-body">
                                <p class="meta"><time datetime="<?= e((string) $row['published_at']) ?>"><?= e(format_date((string) $row['published_at'])) ?></time></p>
                                <h3><a href="<?= e('/news-item.php?slug=' . rawurlencode((string) $row['slug'])) ?>"><?= e((string) $row['title']) ?></a></h3>
                                <?php if (!empty($row['summary'])): ?>
                                    <p><?= e((string) $row['summary']) ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

            </div><!-- /main column -->

            <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div>
    </div>
</div>

<?php
$ctaHeading = 'Stay in the loop';
$ctaBody    = 'Join the mailing list for campaign updates, event notices, and new articles.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
