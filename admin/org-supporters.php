<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'Organisational supporters';
$adminSection = 'orgsupporters';

/* Handle approve / reject */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validate($_POST['csrf_token'] ?? null)) {
    $id     = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($id > 0 && in_array($action, ['approved', 'rejected', 'pending'], true) && db_available()) {
        try {
            db()->prepare('UPDATE org_supporters SET status = :s WHERE id = :id')
                ->execute(['s' => $action, 'id' => $id]);
            flash_set('admin_ok', 'Status updated.');
        } catch (Throwable) {
            flash_set('admin_err', 'Could not update status.');
        }
    }
    header('Location: /admin/org-supporters.php');
    exit;
}

$filter = $_GET['status'] ?? 'pending';
if (!in_array($filter, ['pending', 'approved', 'rejected', 'all'], true)) {
    $filter = 'pending';
}

$orgs = [];
if (db_available()) {
    try {
        if ($filter === 'all') {
            $orgs = db()->query('SELECT * FROM org_supporters ORDER BY created_at DESC')->fetchAll();
        } else {
            $s = db()->prepare('SELECT * FROM org_supporters WHERE status = :s ORDER BY created_at DESC');
            $s->execute(['s' => $filter]);
            $orgs = $s->fetchAll();
        }
    } catch (Throwable) {}
}

$counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
if (db_available()) {
    try {
        $rows = db()->query("SELECT status, COUNT(*) AS n FROM org_supporters GROUP BY status")->fetchAll();
        foreach ($rows as $r) {
            $counts[$r['status']] = (int) $r['n'];
        }
    } catch (Throwable) {}
}

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">Organisational supporters</h1>
    <a class="admin-btn-sm" href="/supporters.php" target="_blank" rel="noopener">View public page</a>
</div>

<!-- Filter tabs -->
<div style="display:flex;gap:0.5rem;margin-bottom:1.5rem;flex-wrap:wrap">
    <?php foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'all' => 'All'] as $k => $label): ?>
        <a href="/admin/org-supporters.php?status=<?= e($k) ?>"
           class="btn <?= $filter === $k ? 'btn-primary' : 'btn-ghost' ?>"
           style="padding:0.35rem 0.9rem;font-size:0.85rem">
            <?= e($label) ?><?= isset($counts[$k]) ? ' (' . $counts[$k] . ')' : '' ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if (empty($orgs)): ?>
    <div class="admin-table-wrap">
        <p class="admin-empty">No <?= $filter === 'all' ? '' : $filter ?> signups yet.</p>
    </div>
<?php else: ?>
    <div style="display:flex;flex-direction:column;gap:1rem">
        <?php foreach ($orgs as $org): ?>
        <div class="admin-form" style="padding:1.5rem">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap">
                <div>
                    <strong style="font-size:1.05rem"><?= e($org['org_name']) ?></strong>
                    <?php if ($org['org_type']): ?>
                        <span class="pill pill--forming" style="margin-left:0.5rem"><?= e($org['org_type']) ?></span>
                    <?php endif; ?>
                    <?php if ($org['status'] === 'approved'): ?>
                        <span class="pill pill--active" style="margin-left:0.25rem">Approved</span>
                    <?php elseif ($org['status'] === 'rejected'): ?>
                        <span class="pill pill--seeking" style="margin-left:0.25rem">Rejected</span>
                    <?php else: ?>
                        <span class="pill pill--forming" style="margin-left:0.25rem">Pending</span>
                    <?php endif; ?>
                </div>
                <p class="meta" style="margin:0"><?= e(format_date(substr((string) $org['created_at'], 0, 10))) ?></p>
            </div>

            <dl style="margin:0.85rem 0 0;display:grid;grid-template-columns:7rem 1fr;gap:0.3rem 1rem;font-size:0.9rem">
                <?php if ($org['location']): ?><dt style="color:var(--muted);font-weight:700">Location</dt><dd><?= e($org['location']) ?></dd><?php endif; ?>
                <?php if ($org['org_url']): ?><dt style="color:var(--muted);font-weight:700">Website</dt><dd><a href="<?= e($org['org_url']) ?>" target="_blank" rel="noopener"><?= e($org['org_url']) ?></a></dd><?php endif; ?>
                <dt style="color:var(--muted);font-weight:700">Contact</dt><dd><?= e($org['contact_name']) ?> &lt;<a href="mailto:<?= e($org['contact_email']) ?>"><?= e($org['contact_email']) ?></a>&gt;</dd>
                <?php if ($org['why_joining']): ?><dt style="color:var(--muted);font-weight:700">Statement</dt><dd style="font-style:italic"><?= e($org['why_joining']) ?></dd><?php endif; ?>
            </dl>

            <form method="post" style="display:flex;gap:0.5rem;margin-top:1.25rem;flex-wrap:wrap">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= (int) $org['id'] ?>">
                <?php if ($org['status'] !== 'approved'): ?>
                    <button class="btn btn-primary" type="submit" name="action" value="approved" style="font-size:0.85rem;padding:0.3rem 0.9rem">Approve &amp; publish</button>
                <?php endif; ?>
                <?php if ($org['status'] !== 'rejected'): ?>
                    <button class="btn" type="submit" name="action" value="rejected" style="font-size:0.85rem;padding:0.3rem 0.9rem;background:rgba(226,85,64,.1);color:#7a2f24"
                        data-confirm="Reject this organisation?">Reject</button>
                <?php endif; ?>
                <?php if ($org['status'] !== 'pending'): ?>
                    <button class="btn btn-ghost" type="submit" name="action" value="pending" style="font-size:0.85rem;padding:0.3rem 0.9rem">Reset to pending</button>
                <?php endif; ?>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
