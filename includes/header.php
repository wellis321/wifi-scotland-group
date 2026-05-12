<?php

declare(strict_types=1);

/** @var string $pageTitle */
/** @var string $pageDescription */
/** @var string $currentNav */
/** @var string|null $pageOgImage Web path e.g. /images/hero.jpg */
/** @var string $pageOgImageAlt */

$pageTitle = $pageTitle ?? SITE_BRAND;
$pageDescription = $pageDescription ?? 'Campaigning for affordable, reliable connectivity as essential infrastructure in Scotland and beyond.';
$currentNav = $currentNav ?? '';
$pageOgImage = $pageOgImage ?? null;
$pageOgImageAlt = $pageOgImageAlt ?? '';

$navStructure = [
    [
        'type' => 'link',
        'id' => 'home',
        'href' => '/index.php',
        'label' => 'Home',
    ],
    [
        'type' => 'group',
        'id' => 'policy',
        'label' => 'Policy',
        'items' => [
            ['id' => 'scotland', 'href' => '/scotland.php', 'label' => 'Scotland'],
            ['id' => 'news', 'href' => '/news.php', 'label' => 'News'],
            ['id' => 'resources', 'href' => '/resources.php', 'label' => 'Resources'],
        ],
    ],
    [
        'type' => 'group',
        'id' => 'takepart',
        'label' => 'Take part',
        'items' => [
            ['id' => 'about', 'href' => '/about.php', 'label' => 'About'],
            ['id' => 'involved', 'href' => '/get-involved.php', 'label' => 'Get involved'],
            ['id' => 'join', 'href' => '/join.php', 'label' => 'Join'],
            ['id' => 'global', 'href' => '/global-spotlight.php', 'label' => 'Global spotlight'],
        ],
    ],
    [
        'type' => 'link',
        'id' => 'contact',
        'href' => '/contact.php',
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
    <?php
    if ($pageOgImage !== null) {
        $ogAbs = absolute_url_for_path($pageOgImage);
        if ($ogAbs !== null) {
            ?>
    <meta property="og:image" content="<?= e($ogAbs) ?>">
            <?php
            if ($pageOgImageAlt !== '') {
                ?>
    <meta property="og:image:alt" content="<?= e($pageOgImageAlt) ?>">
                <?php
            }
            ?>
    <meta name="twitter:card" content="summary_large_image">
            <?php
        }
    }
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,700;1,9..144,500&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/site.css">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header">
    <div class="wrap header-inner">
        <a class="brand" href="/index.php">
            <span class="brand-mark" aria-hidden="true"></span>
            <span class="brand-text">The WIFI <span>Scotland Group</span></span>
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
        <a class="btn btn-header" href="/join.php">Join the group</a>
    </div>
</header>
<main id="main">
