<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'Local groups';
$adminSection = 'groups';

$groups = db_available()
    ? db()->query('SELECT g.id, g.slug, g.council_area, g.status, COUNT(e.id) AS event_count FROM local_groups g LEFT JOIN group_events e ON e.group_id = g.id GROUP BY g.id ORDER BY g.council_area')->fetchAll()
    : [];

$statusLabel = ['active' => 'Active', 'forming' => 'Forming', 'seeking_organiser' => 'Needs organiser'];

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">Local groups</h1>
    <a class="btn btn-primary" href="/admin/group-edit.php">+ New group</a>
</div>

<?php if (empty($groups)): ?>
    <div class="admin-table-wrap"><p class="admin-empty">No groups yet. <a href="/admin/group-edit.php">Add the first one.</a></p></div>
<?php else: ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Council area</th><th>Status</th><th>Events</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($groups as $g): ?>
                <tr>
                    <td><strong><?= e((string) $g['council_area']) ?></strong></td>
                    <td><?= e($statusLabel[$g['status']] ?? $g['status']) ?></td>
                    <td><?= (int) $g['event_count'] ?></td>
                    <td class="col-actions">
                        <a class="admin-link" href="/admin/group-edit.php?id=<?= (int) $g['id'] ?>">Edit</a>
                        <a class="admin-link" href="/admin/events.php?group_id=<?= (int) $g['id'] ?>">Events</a>
                        <a class="admin-link" href="/group.php?slug=<?= e(rawurlencode((string) $g['slug'])) ?>" target="_blank" rel="noopener">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
