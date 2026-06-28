<?php

declare(strict_types=1);

/** @var string $pageTitle */
/** @var string $pageDescription */
/** @var string $currentNav */
/** @var string|null $pageOgImage Web path e.g. /images/hero.jpg */
/** @var string $pageOgImageAlt */

$pageTitle       = $pageTitle       ?? SITE_BRAND;
$pageDescription = $pageDescription ?? 'Web Infrastructure Rights for Everyone in Scotland — campaigning for affordable, reliable connectivity as essential public infrastructure.';
$currentNav      = $currentNav      ?? '';
$pageOgImage     = $pageOgImage     ?? null;
$pageOgImageAlt  = $pageOgImageAlt  ?? '';
$pageOgType      = $pageOgType      ?? 'website';

$_canonicalUri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
// Normalise /index.php → /
if ($_canonicalUri === '/index.php' || str_starts_with($_canonicalUri, '/index.php?')) {
    $_canonicalUri = '/';
}
// Strip .php extension for canonical (matches the clean URL redirect)
$_canonicalUri = preg_replace('/\.php(?=\?|$)/', '', $_canonicalUri);
$canonicalUrl  = page_url(ltrim($_canonicalUri, '/'));
$ogImageAbs   = $pageOgImage !== null ? absolute_url_for_path($pageOgImage) : null;

$navStructure = [
    [
        'type' => 'link',
        'id' => 'home',
        'href' => '/',
        'label' => 'Home',
    ],
    [
        'type' => 'group',
        'id' => 'policy',
        'label' => 'Policy',
        'items' => [
            ['id' => 'gethelp', 'href' => '/get-help', 'label' => 'Help getting online'],
            ['id' => 'scotland', 'href' => '/scotland', 'label' => 'Scotland'],
            ['id' => 'scotlandstories', 'href' => '/scotland-stories', 'label' => 'Scottish stories'],
            ['id' => 'wifimap', 'href' => '/wifi-map', 'label' => 'WiFi map'],
            ['id' => 'whymatters', 'href' => '/why-it-matters', 'label' => 'Why it matters'],
            ['id' => 'news', 'href' => '/news', 'label' => 'News'],
            ['id' => 'resources', 'href' => '/resources', 'label' => 'Resources'],
        ],
    ],
    [
        'type' => 'group',
        'id' => 'takepart',
        'label' => 'Take part',
        'items' => [
            ['id' => 'about', 'href' => '/about', 'label' => 'About'],
            ['id' => 'involved', 'href' => '/get-involved', 'label' => 'Get involved'],
            ['id' => 'groups', 'href' => '/groups', 'label' => 'Local groups'],
            ['id' => 'startgroup', 'href' => '/start-a-group', 'label' => 'Start a group'],
            ['id' => 'supporters', 'href' => '/supporters', 'label' => 'Supporters'],
            ['id' => 'join', 'href' => '/join', 'label' => 'Join'],
            ['id' => 'global', 'href' => '/global-spotlight', 'label' => 'Global spotlight'],
        ],
    ],
    [
        'type' => 'link',
        'id' => 'contact',
        'href' => '/contact',
        'label' => 'Contact',
    ],
];

/** True if $currentNav matches a page inside this group (for styling). */
$navGroupIsActive = static function (array $group) use ($currentNav): bool {
    if (($group['type'] ?? '') !== 'group') {
        return false;
    }
    foreach ($group['items'] as $item) {
        if (($item['id'] ?? '') === $currentNav) {
            return true;
        }
    }

    return false;
};
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> · <?= e(SITE_BRAND) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">

    <!-- Open Graph -->
    <meta property="og:site_name" content="<?= e(SITE_BRAND) ?>">
    <meta property="og:type"      content="<?= e($pageOgType) ?>">
    <meta property="og:title"     content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:url"       content="<?= e($canonicalUrl) ?>">
    <?php if ($ogImageAbs !== null): ?>
    <meta property="og:image"     content="<?= e($ogImageAbs) ?>">
    <?php if ($pageOgImageAlt !== ''): ?>
    <meta property="og:image:alt" content="<?= e($pageOgImageAlt) ?>">
    <?php endif; ?>
    <?php endif; ?>

    <!-- Twitter / X card -->
    <meta name="twitter:card"        content="<?= $ogImageAbs !== null ? 'summary_large_image' : 'summary' ?>">
    <meta name="twitter:title"       content="<?= e($pageTitle) ?>">
    <meta name="twitter:description" content="<?= e($pageDescription) ?>">
    <?php if ($ogImageAbs !== null): ?>
    <meta name="twitter:image"       content="<?= e($ogImageAbs) ?>">
    <?php endif; ?>

    <!-- JSON-LD: Organisation (every page) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?= e(SITE_BRAND) ?>",
        "alternateName": "Web Infrastructure Rights for Everyone in Scotland",
        "url": "<?= e(page_url()) ?>",
        "description": "<?= e($pageDescription) ?>"
    }
    </script>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Source+Sans+3:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/site.css">
    <?= $pageExtraHead ?? '' ?>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
    <div class="wrap header-inner">
        <a class="brand" href="/">
            <img class="brand-logo"
                 src="/images/logo.png"
                 alt="WIRES — Web Infrastructure Rights for Everyone in Scotland"
                 width="400" height="218"
                 loading="eager"
                 decoding="async">
        </a>
        <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>
            <span class="nav-toggle-bar"></span>
            <span class="nav-toggle-label">Menu</span>
        </button>
        <nav class="site-nav" id="site-nav" data-site-nav aria-label="Primary">
            <ul class="nav-primary">
                <?php foreach ($navStructure as $entry): ?>
                    <?php if (($entry['type'] ?? '') === 'link'): ?>
                        <li>
                            <a class="nav-link" href="<?= e($entry['href']) ?>"<?= $currentNav === ($entry['id'] ?? '') ? ' aria-current="page"' : '' ?>><?= e($entry['label']) ?></a>
                        </li>
                    <?php elseif (($entry['type'] ?? '') === 'group'):
                        $gid = (string) ($entry['id'] ?? 'group');
                        $subId = 'nav-sub-' . preg_replace('/[^a-z0-9-]/i', '', $gid);
                        $groupActive = $navGroupIsActive($entry);
                        ?>
                        <li class="nav-group<?= $groupActive ? ' nav-group--active' : '' ?>" data-nav-group>
                            <button
                                type="button"
                                class="nav-group-trigger"
                                aria-expanded="false"
                                aria-haspopup="true"
                                aria-controls="<?= e($subId) ?>"
                                id="nav-trigger-<?= e($gid) ?>"
                                data-nav-group-toggle
                            >
                                <?= e($entry['label']) ?>
                                <span class="nav-group-chevron" aria-hidden="true"></span>
                            </button>
                            <ul class="nav-sub" id="<?= e($subId) ?>" role="list" aria-labelledby="nav-trigger-<?= e($gid) ?>">
                                <?php foreach ($entry['items'] as $item): ?>
                                    <li>
                                        <a class="nav-sub-link" href="<?= e($item['href']) ?>"<?= $currentNav === ($item['id'] ?? '') ? ' aria-current="page"' : '' ?>><?= e($item['label']) ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </nav>
        <a class="btn btn-header" href="/join">Join the group</a>
    </div>
</header>
<main id="main">
