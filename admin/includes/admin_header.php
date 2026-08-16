<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($adminTitle ?? 'Admin') ?> — WIRES Admin</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Source+Sans+3:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/site.css">
    <link rel="stylesheet" href="/admin/admin.css">
</head>
<?php
$adminNavStructure = [
    ['type' => 'link',  'id' => 'dashboard', 'href' => '/admin/', 'label' => 'Dashboard'],
    [
        'type' => 'group', 'id' => 'inbox', 'label' => 'Inbox',
        'items' => [
            ['id' => 'members',  'href' => '/admin/members.php',  'label' => 'Members'],
            ['id' => 'messages', 'href' => '/admin/messages.php', 'label' => 'Messages'],
            ['id' => 'tips',     'href' => '/admin/tips.php',     'label' => 'Tips'],
        ],
    ],
    [
        'type' => 'group', 'id' => 'content', 'label' => 'Content',
        'items' => [
            ['id' => 'news',          'href' => '/admin/news.php',           'label' => 'News'],
            ['id' => 'groups',        'href' => '/admin/groups.php',         'label' => 'Groups'],
            ['id' => 'events',        'href' => '/admin/events.php',         'label' => 'Events'],
            ['id' => 'schemes',       'href' => '/admin/schemes.php',        'label' => 'Schemes'],
            ['id' => 'orgsupporters', 'href' => '/admin/org-supporters.php', 'label' => 'Supporters'],
        ],
    ],
    ['type' => 'link', 'id' => 'councils', 'href' => '/admin/councils.php', 'label' => 'Councils'],
    [
        'type' => 'group', 'id' => 'media', 'label' => 'Media',
        'items' => [
            ['id' => 'media', 'href' => '/admin/media.php', 'label' => 'Media'],
            ['id' => 'files', 'href' => '/admin/files.php', 'label' => 'Files'],
        ],
    ],
];

/** True if $adminSection matches an item inside this group. */
$adminGroupIsActive = static function (array $group) use ($adminSection): bool {
    foreach ($group['items'] as $item) {
        if (($item['id'] ?? '') === ($adminSection ?? '')) return true;
    }
    return false;
};
?>
<body class="admin-body">
<header class="admin-header">
    <div class="admin-header-inner">
        <a class="admin-brand" href="/admin/">WIRES <span>Admin</span></a>
        <nav class="admin-nav" aria-label="Admin">
            <?php foreach ($adminNavStructure as $entry): ?>
                <?php if ($entry['type'] === 'link'): ?>
                    <a href="<?= e($entry['href']) ?>" class="admin-nav-link <?= ($adminSection ?? '') === $entry['id'] ? 'is-active' : '' ?>"><?= e($entry['label']) ?></a>
                <?php else:
                    $groupActive = $adminGroupIsActive($entry);
                ?>
                    <div class="admin-nav-group">
                        <button type="button" class="admin-nav-link admin-nav-group-trigger <?= $groupActive ? 'is-active' : '' ?>">
                            <?= e($entry['label']) ?>
                            <span class="admin-nav-group-chevron" aria-hidden="true"></span>
                        </button>
                        <div class="admin-nav-dropdown">
                            <?php foreach ($entry['items'] as $item): ?>
                                <a href="<?= e($item['href']) ?>" class="admin-nav-dropdown-link <?= ($adminSection ?? '') === $item['id'] ? 'is-active' : '' ?>"><?= e($item['label']) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
        <div class="admin-header-actions">
            <a href="/" target="_blank" rel="noopener" class="admin-btn-sm">View site</a>
            <a href="/admin/logout.php" class="admin-btn-sm admin-btn-sm--ghost">Log out</a>
        </div>
    </div>
</header>
<main id="admin-main">
<div class="admin-main">
<div class="admin-content">
<?php $f = flash_take('admin_ok'); if ($f): ?><div class="admin-flash admin-flash--ok" role="status"><?= e($f) ?></div><?php endif; ?>
<?php $f = flash_take('admin_err'); if ($f): ?><div class="admin-flash admin-flash--err" role="alert"><?= e($f) ?></div><?php endif; ?>
