<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$pageTitle = 'News & updates';
$pageDescription = 'Campaign notes, explainers, and pointers to official connectivity updates in Scotland.';
$currentNav = 'news';

$items = [];
$dbOk = db_available();
if ($dbOk) {
    try {
        $items = db()->query(
            'SELECT title, slug, summary, published_at FROM news_items ORDER BY published_at DESC, id DESC'
        )->fetchAll();
    } catch (Throwable) {
        $items = [];
        $dbOk = false;
    }
}

$pageOgImage = image_asset('card-fibre.jpg');
$pageOgImageAlt = 'Fibre-optic cables close-up—representing physical internet infrastructure.';

require_once dirname(__DIR__) . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>News &amp; updates</h1>
        <p>Editorial pieces from <?= e(SITE_BRAND) ?> plus (over time) pointers to consultations, council decisions, and major programme changes.</p>
    </div>
</header>

<figure class="page-banner" aria-hidden="true">
    <img src="<?= e(image_asset('card-fibre.jpg')) ?>" width="1200" height="800" alt="" decoding="async" loading="lazy">
</figure>

<div class="section">
    <div class="wrap">
        <?php if (!$dbOk): ?>
            <div class="callout prose">
                <p><strong>Connect the database</strong> to load starter articles. Import <code>schema.sql</code> and configure your <code>.env</code> file—see the README.</p>
            </div>
        <?php elseif (!$items): ?>
            <p class="section-intro">No articles yet. Add rows to the <code>news_items</code> table or re-import <code>schema.sql</code>.</p>
        <?php else: ?>
            <div class="news-list">
                <?php foreach ($items as $row): ?>
                    <article>
                        <p class="meta"><?= e((string) $row['published_at']) ?></p>
                        <h2><a href="<?= e('/news-item.php?slug=' . rawurlencode((string) $row['slug'])) ?>"><?= e((string) $row['title']) ?></a></h2>
                        <?php if (!empty($row['summary'])): ?>
                            <p><?= e((string) $row['summary']) ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
