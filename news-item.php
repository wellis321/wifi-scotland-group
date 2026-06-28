<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$slug    = isset($_GET['slug']) ? (string) $_GET['slug'] : '';
$article = null;

if ($slug !== '' && db_available()) {
    try {
        $stmt = db()->prepare(
            'SELECT title, slug, summary, body, published_at, image_filename FROM news_items WHERE slug = :slug LIMIT 1'
        );
        $stmt->execute(['slug' => $slug]);
        $article = $stmt->fetch() ?: null;
    } catch (Throwable) {}
}

if (!$article) {
    http_response_code(404);
    $pageTitle       = 'Article not found';
    $pageDescription = 'That news item could not be loaded.';
    $currentNav      = 'news';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <header class="page-header">
        <div class="wrap">
            <h1>Not found</h1>
            <p>We could not find that article. It may have been removed, or the database may not be connected.</p>
        </div>
    </header>
    <div class="section">
        <div class="wrap prose">
            <p><a href="/news.php">&larr; Back to news</a></p>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    return;
}

/* Fetch other recent articles for sidebar (excluding this one) */
$otherArticles = [];
if (db_available()) {
    try {
        $s = db()->prepare(
            'SELECT title, slug, published_at FROM news_items
             WHERE slug != :slug ORDER BY published_at DESC, id DESC LIMIT 4'
        );
        $s->execute(['slug' => $slug]);
        $otherArticles = $s->fetchAll();
    } catch (Throwable) {}
}

$pageTitle       = (string) $article['title'];
$pageDescription = (string) ($article['summary'] ?? $pageTitle);
$currentNav      = 'news';
$pageOgType      = 'article';

/* Only use a specific uploaded image — never fall back to a generic stock photo */
$hasImage    = !empty($article['image_filename']);
$imageFile   = $hasImage ? (string) $article['image_filename'] : null;
$pageOgImage = $hasImage ? image_asset($imageFile) : image_asset('card-community.jpg');
$pageOgImageAlt = $hasImage
    ? 'Image for: ' . (string) $article['title']
    : 'WIRES — Web Infrastructure Rights for Everyone in Scotland';

$articleJsonLd = json_encode([
    '@context'      => 'https://schema.org',
    '@type'         => 'Article',
    'headline'      => $article['title'],
    'description'   => $article['summary'] ?? '',
    'datePublished' => $article['published_at'],
    'image'         => $hasImage ? absolute_url_for_path($pageOgImage) : null,
    'publisher'     => [
        '@type' => 'Organization',
        'name'  => SITE_BRAND,
        'url'   => page_url(),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$pageExtraHead = '<script type="application/ld+json">' . $articleJsonLd . '</script>';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <p class="meta">
            <a href="/news.php">&larr; News</a>
            <span aria-hidden="true"> &middot; </span>
            <time datetime="<?= e((string) $article['published_at']) ?>"><?= e(format_date((string) $article['published_at'])) ?></time>
        </p>
        <h1><?= e((string) $article['title']) ?></h1>
        <?php if (!empty($article['summary'])): ?>
            <p><?= e((string) $article['summary']) ?></p>
        <?php endif; ?>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">

            <div class="prose">

                <?php if ($hasImage): ?>
                    <img class="page-hero-img"
                         src="<?= e(image_asset($imageFile)) ?>"
                         width="1200" height="800"
                         alt=""
                         decoding="async"
                         loading="lazy">
                <?php endif; ?>

                <?= $article['body'] /* trusted HTML — admin-authored only */ ?>

                <?php
                $shareUrl     = page_url('news-item.php?slug=' . rawurlencode((string) $article['slug']));
                $shareTitle   = (string) $article['title'];
                $shareCompact = false;
                require __DIR__ . '/includes/share.php';
                ?>

                <p style="margin-top:1.5rem"><a href="/news.php">&larr; All news</a></p>
            </div>

            <!-- Article sidebar -->
            <aside class="page-sidebar" aria-label="Article sidebar">

                <div class="sidebar-card sidebar-card--highlight">
                    <h3>Join WIRES</h3>
                    <p>Get updates on events, consultations, and local actions in Scotland.</p>
                    <a class="btn btn-lg" href="/join.php"
                       style="background:#fff;color:var(--ink);width:100%;justify-content:center;text-align:center">
                        Join the campaign
                    </a>
                </div>

                <?php if (!empty($otherArticles)): ?>
                <div class="sidebar-card">
                    <h3>More from WIRES</h3>
                    <ul class="sidebar-nav">
                        <?php foreach ($otherArticles as $row): ?>
                            <li>
                                <a href="<?= e('/news-item.php?slug=' . rawurlencode((string) $row['slug'])) ?>">
                                    <?= e((string) $row['title']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p style="margin-top:0.75rem;margin-bottom:0">
                        <a href="/news.php" style="font-size:0.85rem;font-weight:600">All news &rarr;</a>
                    </p>
                </div>
                <?php endif; ?>

                <div class="sidebar-card sidebar-card--accent">
                    <h3>Need help getting online?</h3>
                    <p>We list schemes that can lower your broadband costs or get you connected.</p>
                    <a class="btn btn-ghost"
                       href="/get-help.php"
                       style="width:100%;justify-content:center;text-align:center">
                        See all schemes
                    </a>
                </div>

                <div class="sidebar-card">
                    <h3>Get involved</h3>
                    <ul class="sidebar-nav">
                        <li><a href="/get-involved.php">Take action locally</a></li>
                        <li><a href="/groups.php">Find a local group</a></li>
                        <li><a href="/contact.php">Contact WIRES</a></li>
                    </ul>
                </div>

            </aside>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
