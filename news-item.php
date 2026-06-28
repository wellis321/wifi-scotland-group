<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$slug = isset($_GET['slug']) ? (string) $_GET['slug'] : '';
$article = null;

if ($slug !== '' && db_available()) {
    $stmt = db()->prepare('SELECT title, slug, summary, body, published_at FROM news_items WHERE slug = :slug LIMIT 1');
    $stmt->execute(['slug' => $slug]);
    $article = $stmt->fetch() ?: null;
}

if (!$article) {
    http_response_code(404);
    $pageTitle = 'Article not found';
    $pageDescription = 'That news item could not be loaded.';
    $currentNav = 'news';
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
            <p><a href="/news.php">Back to news</a></p>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    return;
}

$pageTitle = (string) $article['title'];
$pageDescription = (string) ($article['summary'] ?? $pageTitle);
$currentNav = 'news';
$pageOgImage = image_asset('card-global-network.jpg');
$pageOgImageAlt = 'Abstract view of Earth and networks—editorial sharing image for news.';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <p class="meta"><time datetime="<?= e((string) $article['published_at']) ?>"><?= e(format_date((string) $article['published_at'])) ?></time></p>
        <h1><?= e((string) $article['title']) ?></h1>
        <?php if (!empty($article['summary'])): ?>
            <p><?= e((string) $article['summary']) ?></p>
        <?php endif; ?>
    </div>
</header>

<div class="section">
    <div class="wrap prose">
        <?= $article['body'] /* trusted HTML from our own DB seed / admins; escape if opening to untrusted authors */ ?>
        <p><a href="/news.php">&larr; All news</a></p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
