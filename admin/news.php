<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'News';
$adminSection = 'news';

$items = [];
if (db_available()) {
    try {
        $items = db()->query(
            'SELECT id, title, slug, published_at, group_id FROM news_items ORDER BY published_at DESC, id DESC'
        )->fetchAll();
    } catch (Throwable) {
        // group_id column may not exist yet on this install — fall back without it
        try {
            $items = db()->query(
                'SELECT id, title, slug, published_at, NULL AS group_id FROM news_items ORDER BY published_at DESC, id DESC'
            )->fetchAll();
            flash_set('admin_err', 'Heads up: the group_id column is missing from news_items. Run the ALTER TABLE migration in phpMyAdmin (see schema.sql comments).');
        } catch (Throwable) {
            flash_set('admin_err', 'Could not load articles from the database.');
        }
    }
}

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">News</h1>
    <a class="btn btn-primary" href="/admin/news-edit.php">+ New article</a>
</div>

<?php if (empty($items)): ?>
    <div class="admin-table-wrap"><p class="admin-empty">No articles yet. <a href="/admin/news-edit.php">Write the first one.</a></p></div>
<?php else: ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Title</th><th>Published</th><th>Group</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($items as $row): ?>
                <tr>
                    <td><strong><?= e((string) $row['title']) ?></strong></td>
                    <td class="meta"><?= e(format_date((string) $row['published_at'])) ?></td>
                    <td class="meta"><?= $row['group_id'] ? '#' . e((string) $row['group_id']) : '—' ?></td>
                    <td class="col-actions">
                        <a class="admin-link" href="/admin/news-edit.php?slug=<?= e(rawurlencode((string) $row['slug'])) ?>">Edit</a>
                        <a class="admin-link" href="/news-item.php?slug=<?= e(rawurlencode((string) $row['slug'])) ?>" target="_blank" rel="noopener">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
