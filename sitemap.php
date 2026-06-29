<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex');

$base = rtrim((string) app_config()['app']['base_url'], '/');
if ($base === '') {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $base   = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'wires.org.uk');
}

$today = date('Y-m-d');

/* Static pages */
$static = [
    ['/',                     '1.0', 'weekly'],
    ['/about',                '0.8', 'monthly'],
    ['/landscape',            '0.8', 'monthly'],
    ['/why-it-matters',       '0.8', 'monthly'],
    ['/digital-health',       '0.8', 'monthly'],
    ['/beyond-broadband',     '0.8', 'monthly'],
    ['/scotland',             '0.8', 'monthly'],
    ['/accountability',       '0.9', 'weekly'],
    ['/scotland-stories',     '0.7', 'monthly'],
    ['/global-spotlight',     '0.7', 'monthly'],
    ['/get-help',             '0.9', 'weekly'],
    ['/wifi-map',             '0.7', 'monthly'],
    ['/news',                 '0.9', 'daily'],
    ['/resources',            '0.6', 'monthly'],
    ['/groups',               '0.8', 'weekly'],
    ['/start-a-group',        '0.7', 'monthly'],
    ['/get-involved',         '0.7', 'monthly'],
    ['/write-to-councillor',  '0.8', 'monthly'],
    ['/supporters',           '0.8', 'weekly'],
    ['/join-as-organisation', '0.8', 'monthly'],
    ['/join',                 '0.8', 'monthly'],
    ['/contact',              '0.6', 'monthly'],
    ['/search',               '0.3', 'monthly'],
    ['/privacy',              '0.3', 'yearly'],
];

/* Dynamic: news articles */
$articles = [];
if (db_available()) {
    try {
        $articles = db()->query(
            'SELECT slug, published_at FROM news_items ORDER BY published_at DESC'
        )->fetchAll();
    } catch (Throwable) {}
}

/* Dynamic: local group pages */
$groups = [];
if (db_available()) {
    try {
        $groups = db()->query(
            'SELECT slug, created_at FROM local_groups ORDER BY created_at DESC'
        )->fetchAll();
    } catch (Throwable) {}
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($static as [$path, $priority, $freq]): ?>
    <url>
        <loc><?= e($base . $path) ?></loc>
        <lastmod><?= e($today) ?></lastmod>
        <changefreq><?= e($freq) ?></changefreq>
        <priority><?= e($priority) ?></priority>
    </url>
<?php endforeach; ?>
<?php foreach ($articles as $row): ?>
    <url>
        <loc><?= e($base . '/news-item.php?slug=' . rawurlencode((string) $row['slug'])) ?></loc>
        <lastmod><?= e((string) $row['published_at']) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
<?php endforeach; ?>
<?php foreach ($groups as $row): ?>
    <url>
        <loc><?= e($base . '/group.php?slug=' . rawurlencode((string) $row['slug'])) ?></loc>
        <lastmod><?= e(substr((string) $row['created_at'], 0, 10)) ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
<?php endforeach; ?>
</urlset>
