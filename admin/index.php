<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'Dashboard';
$adminSection = 'dashboard';

$counts = ['news' => 0, 'groups' => 0, 'events' => 0, 'schemes' => 0, 'orgs' => 0];
$recentNews = [];

if (db_available()) {
    try {
        foreach (['news_items' => 'news', 'local_groups' => 'groups', 'group_events' => 'events', 'schemes' => 'schemes', 'org_supporters' => 'orgs'] as $table => $key) {
            $counts[$key] = (int) db()->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
        }
        $recentNews = db()->query(
            'SELECT title, slug, published_at FROM news_items ORDER BY published_at DESC, id DESC LIMIT 5'
        )->fetchAll();
    } catch (Throwable) {}
}

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">Dashboard</h1>
</div>

<div class="admin-stats">
    <a class="admin-stat" href="/admin/news.php">
        <span class="admin-stat-value"><?= $counts['news'] ?></span>
        <span class="admin-stat-label">News items</span>
    </a>
    <a class="admin-stat" href="/admin/groups.php">
        <span class="admin-stat-value"><?= $counts['groups'] ?></span>
        <span class="admin-stat-label">Local groups</span>
    </a>
    <a class="admin-stat" href="/admin/events.php">
        <span class="admin-stat-value"><?= $counts['events'] ?></span>
        <span class="admin-stat-label">Events</span>
    </a>
    <a class="admin-stat" href="/admin/schemes.php">
        <span class="admin-stat-value"><?= $counts['schemes'] ?></span>
        <span class="admin-stat-label">Help schemes</span>
    </a>
    <a class="admin-stat" href="/admin/org-supporters.php">
        <span class="admin-stat-value"><?= $counts['orgs'] ?></span>
        <span class="admin-stat-label">Org supporters</span>
    </a>
</div>

<p class="admin-section-title">Quick actions</p>
<p style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:2rem">
    <a class="btn btn-primary" href="/admin/news-edit.php">+ New article</a>
    <a class="btn btn-ghost" href="/admin/group-edit.php">+ New group</a>
    <a class="btn btn-ghost" href="/admin/event-edit.php">+ New event</a>
    <a class="btn btn-ghost" href="/admin/scheme-edit.php">+ New scheme</a>
</p>

<?php if ($recentNews): ?>
    <p class="admin-section-title">Recent news</p>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Title</th><th>Date</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($recentNews as $row): ?>
                <tr>
                    <td><?= e((string) $row['title']) ?></td>
                    <td class="meta"><?= e(format_date((string) $row['published_at'])) ?></td>
                    <td class="col-actions">
                        <a class="admin-link" href="/admin/news-edit.php?slug=<?= e(rawurlencode((string) $row['slug'])) ?>">Edit</a>
                        <a class="admin-link" href="<?= e('/news-item.php?slug=' . rawurlencode((string) $row['slug'])) ?>" target="_blank" rel="noopener">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
