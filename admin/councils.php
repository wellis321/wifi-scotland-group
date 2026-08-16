<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'Councillor outreach';
$adminSection = 'councils';

$councils = db_available()
    ? db()->query('SELECT id, council_area, directory_url, contact_method, councillor_count, status, notes, outreach_sent_at, outreach_replied_at FROM council_contacts ORDER BY council_area ASC')->fetchAll()
    : [];

$counts = ['confirmed' => 0, 'check' => 0, 'blocked' => 0];
$sentCount = 0;
$repliedCount = 0;
foreach ($councils as $c) {
    if (isset($counts[$c['status']])) $counts[$c['status']]++;
    if (!empty($c['outreach_sent_at'])) $sentCount++;
    if (!empty($c['outreach_replied_at'])) $repliedCount++;
}

$statusLabel = ['confirmed' => 'Confirmed', 'check' => 'Needs check', 'blocked' => 'No direct email'];
$statusPill  = ['confirmed' => 'pill--active', 'check' => 'pill--forming', 'blocked' => 'pill--seeking'];

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">Councillor outreach</h1>
    <a class="btn btn-primary" href="/admin/council-edit.php">+ Add council</a>
</div>

<p class="meta" style="margin-bottom:1.25rem">Contact routes for reaching councillors across all 32 Scottish councils, for accountability outreach. Update an entry as soon as you verify or fix a contact route — this is the live source of truth, so keep it current rather than relying on a one-off export.</p>

<div class="admin-stats" style="grid-template-columns:repeat(auto-fit,minmax(140px,1fr));margin-bottom:1.75rem">
    <div class="admin-stat">
        <span class="admin-stat-value"><?= count($councils) ?></span>
        <span class="admin-stat-label">Councils tracked</span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-value"><?= $counts['confirmed'] ?></span>
        <span class="admin-stat-label">Confirmed</span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-value"><?= $counts['check'] ?></span>
        <span class="admin-stat-label">Needs check</span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-value"><?= $counts['blocked'] ?></span>
        <span class="admin-stat-label">No direct email</span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-value"><?= $sentCount ?>/32</span>
        <span class="admin-stat-label">Letters sent</span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-value"><?= $repliedCount ?></span>
        <span class="admin-stat-label">Replied</span>
    </div>
</div>

<p class="meta" style="margin-bottom:1.25rem">Sent/replied status here drives the public <a href="/council-replies" target="_blank">Council replies</a> transparency page — keep it current as letters go out and responses come in.</p>

<?php if (empty($councils)): ?>
    <div class="admin-table-wrap"><p class="admin-empty">No council contacts yet. <a href="/admin/council-edit.php">Add one.</a></p></div>
<?php else: ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Council</th><th>Contact method</th><th>Status</th><th>Outreach</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($councils as $c):
                if (!empty($c['outreach_replied_at'])) {
                    $outreach = 'Replied ' . format_date((string) $c['outreach_replied_at']);
                } elseif (!empty($c['outreach_sent_at'])) {
                    $outreach = 'Sent ' . format_date((string) $c['outreach_sent_at']) . ', awaiting reply';
                } else {
                    $outreach = 'Not sent yet';
                }
            ?>
                <tr>
                    <td><strong><?= e((string) $c['council_area']) ?></strong></td>
                    <td class="meta"><?= e((string) ($c['contact_method'] ?? '—')) ?></td>
                    <td><span class="pill <?= e($statusPill[$c['status']] ?? '') ?>"><?= e($statusLabel[$c['status']] ?? $c['status']) ?></span></td>
                    <td class="meta"><?= e($outreach) ?></td>
                    <td class="col-actions">
                        <a class="admin-link" href="/admin/council-edit.php?id=<?= (int) $c['id'] ?>">Edit</a>
                        <?php if (!empty($c['directory_url'])): ?>
                            <a class="admin-link" href="<?= e((string) $c['directory_url']) ?>" target="_blank" rel="noopener">Directory</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="meta" style="margin-top:0.75rem">32 of 32 Scottish councils tracked — the full list matches the council areas shown on the <a href="/wifi-map" target="_blank">WiFi map</a>, so none can be silently missed.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
