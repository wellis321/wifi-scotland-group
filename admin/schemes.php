<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'Help schemes';
$adminSection = 'schemes';

$schemes = db_available()
    ? db()->query('SELECT id, name, status, scope, updated_month FROM schemes ORDER BY updated_month DESC, sort_order ASC')->fetchAll()
    : [];

$statusLabel = ['active' => 'Active', 'check' => 'Check status', 'ended' => 'Ended'];
$scopeLabel  = ['uk' => 'UK-wide', 'scotland' => 'Scotland only'];

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">Help schemes</h1>
    <a class="btn btn-primary" href="/admin/scheme-edit.php">+ New scheme</a>
</div>

<?php if (empty($schemes)): ?>
    <div class="admin-table-wrap"><p class="admin-empty">No schemes yet. <a href="/admin/scheme-edit.php">Add one.</a></p></div>
<?php else: ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Name</th><th>Status</th><th>Scope</th><th>Verified</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($schemes as $s): ?>
                <tr>
                    <td><strong><?= e((string) $s['name']) ?></strong></td>
                    <td><?= e($statusLabel[$s['status']] ?? $s['status']) ?></td>
                    <td><?= e($scopeLabel[$s['scope']] ?? $s['scope']) ?></td>
                    <td class="meta"><?= e((string) $s['updated_month']) ?></td>
                    <td class="col-actions">
                        <a class="admin-link" href="/admin/scheme-edit.php?id=<?= (int) $s['id'] ?>">Edit</a>
                        <a class="admin-link" href="/get-help.php#<?= e(rawurlencode((string)$s['id'])) ?>" target="_blank" rel="noopener">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="meta" style="margin-top:0.75rem">Schemes are shown on the public <a href="/get-help.php" target="_blank">Help getting online</a> page, ordered by verified date.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
