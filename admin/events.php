<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'Events';
$adminSection = 'events';

$filterGroupId = isset($_GET['group_id']) ? (int) $_GET['group_id'] : 0;
$filterGroup   = null;

$events = [];
if (db_available()) {
    if ($filterGroupId > 0) {
        $s = db()->prepare('SELECT * FROM local_groups WHERE id = :id LIMIT 1');
        $s->execute(['id' => $filterGroupId]);
        $filterGroup = $s->fetch() ?: null;

        $s = db()->prepare(
            'SELECT e.*, g.council_area FROM group_events e JOIN local_groups g ON g.id = e.group_id WHERE e.group_id = :gid ORDER BY e.event_date ASC'
        );
        $s->execute(['gid' => $filterGroupId]);
    } else {
        $s = db()->query(
            'SELECT e.*, g.council_area FROM group_events e JOIN local_groups g ON g.id = e.group_id ORDER BY e.event_date ASC'
        );
    }
    $events = $s->fetchAll();
}

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">
        Events<?php if ($filterGroup): ?> — <?= e((string) $filterGroup['council_area']) ?><?php endif; ?>
    </h1>
    <a class="btn btn-primary" href="/admin/event-edit.php<?= $filterGroupId ? '?group_id=' . $filterGroupId : '' ?>">+ New event</a>
</div>

<?php if ($filterGroupId): ?>
    <p style="margin-bottom:1rem"><a class="admin-link" href="/admin/events.php">← All events</a> &nbsp;|&nbsp; <a class="admin-link" href="/admin/group-edit.php?id=<?= $filterGroupId ?>">Edit group</a></p>
<?php endif; ?>

<?php if (empty($events)): ?>
    <div class="admin-table-wrap"><p class="admin-empty">No events<?= $filterGroup ? ' for this group' : '' ?> yet. <a href="/admin/event-edit.php<?= $filterGroupId ? '?group_id='.$filterGroupId : '' ?>">Add one.</a></p></div>
<?php else: ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><?php if (!$filterGroupId): ?><th>Group</th><?php endif; ?><th>Title</th><th>Date</th><th>Location</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($events as $ev): ?>
                <tr>
                    <?php if (!$filterGroupId): ?><td><?= e((string) $ev['council_area']) ?></td><?php endif; ?>
                    <td><strong><?= e((string) $ev['title']) ?></strong></td>
                    <td class="meta"><?= e(format_date((string) $ev['event_date'])) ?><?= $ev['event_time'] ? ', ' . e((string) $ev['event_time']) : '' ?></td>
                    <td class="meta"><?= e((string) ($ev['location_text'] ?? '—')) ?></td>
                    <td class="col-actions">
                        <a class="admin-link" href="/admin/event-edit.php?id=<?= (int) $ev['id'] ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
