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
<body class="admin-body">
<header class="admin-header">
    <div class="admin-header-inner">
        <a class="admin-brand" href="/admin/">WIRES <span>Admin</span></a>
        <nav class="admin-nav" aria-label="Admin">
            <a href="/admin/" class="admin-nav-link <?= ($adminSection ?? '') === 'dashboard' ? 'is-active' : '' ?>">Dashboard</a>
            <a href="/admin/news.php" class="admin-nav-link <?= ($adminSection ?? '') === 'news' ? 'is-active' : '' ?>">News</a>
            <a href="/admin/groups.php" class="admin-nav-link <?= ($adminSection ?? '') === 'groups' ? 'is-active' : '' ?>">Groups</a>
            <a href="/admin/events.php" class="admin-nav-link <?= ($adminSection ?? '') === 'events' ? 'is-active' : '' ?>">Events</a>
            <a href="/admin/schemes.php" class="admin-nav-link <?= ($adminSection ?? '') === 'schemes' ? 'is-active' : '' ?>">Schemes</a>
            <a href="/admin/media.php" class="admin-nav-link <?= ($adminSection ?? '') === 'media' ? 'is-active' : '' ?>">Media</a>
            <a href="/admin/files.php" class="admin-nav-link <?= ($adminSection ?? '') === 'files' ? 'is-active' : '' ?>">Files</a>
            <a href="/admin/org-supporters.php" class="admin-nav-link <?= ($adminSection ?? '') === 'orgsupporters' ? 'is-active' : '' ?>">Supporters</a>
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
