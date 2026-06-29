<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'Members';
$adminSection = 'members';

$members = [];
if (db_available()) {
    try {
        $members = db()->query(
            'SELECT id, full_name, email, locality, interests, consent, created_at
             FROM member_signups
             ORDER BY created_at DESC'
        )->fetchAll();
    } catch (Throwable) {}
}

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">Members <span style="font-size:1rem;font-weight:600;color:var(--muted)">(<?= count($members) ?>)</span></h1>
    <a class="admin-btn-sm" href="/join" target="_blank" rel="noopener">View join form</a>
</div>

<?php if (empty($members)): ?>
    <div class="admin-table-wrap">
        <p class="admin-empty">No sign-ups yet.</p>
    </div>
<?php else: ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Locality</th>
                    <th>Skills / interests</th>
                    <th>Consent</th>
                    <th>Signed up</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($members as $m): ?>
                <tr>
                    <td><strong><?= e((string) $m['full_name']) ?></strong></td>
                    <td><a href="mailto:<?= e((string) $m['email']) ?>"><?= e((string) $m['email']) ?></a></td>
                    <td class="meta"><?= e((string) ($m['locality'] ?? '—')) ?></td>
                    <td style="max-width:260px;font-size:0.85rem;color:var(--muted)"><?= e((string) ($m['interests'] ?? '—')) ?></td>
                    <td><?= $m['consent'] ? '<span class="pill pill--active">Yes</span>' : '<span class="pill pill--seeking">No</span>' ?></td>
                    <td class="meta"><?= e(format_date(substr((string) $m['created_at'], 0, 10))) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="meta" style="margin-top:0.75rem">
        Export: copy the table above, or run
        <code style="font-size:0.8rem">SELECT * FROM member_signups ORDER BY created_at DESC;</code>
        in phpMyAdmin to download as CSV.
    </p>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
