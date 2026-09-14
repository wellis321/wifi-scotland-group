<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'Councillor campaign sends';
$adminSection = 'councillor-campaign';

// Target size: how many confirmed roster rows the campaign could ever reach.
$csvPath = dirname(__DIR__) . '/data/councillors-roster.csv';
$targetCount = 0;
if (is_readable($csvPath)) {
    $fh = fopen($csvPath, 'r');
    if ($fh !== false) {
        $header = fgetcsv($fh, 0, ',', '"', '\\');
        $headerCount = count($header);
        while (($line = fgetcsv($fh, 0, ',', '"', '\\')) !== false) {
            if (count($line) < $headerCount) continue;
            if (count($line) > $headerCount) {
                $overflow = array_splice($line, $headerCount - 1);
                $line[] = implode(',', $overflow);
            }
            $row = array_combine($header, $line);
            if (($row['confidence'] ?? '') === 'confirmed' && trim((string) ($row['email'] ?? '')) !== '') {
                $targetCount++;
            }
        }
        fclose($fh);
    }
}

$campaigns = db_available()
    ? db()->query('SELECT DISTINCT campaign_slug FROM councillor_campaign_sends ORDER BY campaign_slug DESC')->fetchAll(PDO::FETCH_COLUMN)
    : [];

$campaignFilter = trim((string) ($_GET['campaign'] ?? ($campaigns[0] ?? '')));
$councilFilter  = trim((string) ($_GET['council'] ?? ''));
$statusFilter   = trim((string) ($_GET['status'] ?? ''));
$repliedFilter  = trim((string) ($_GET['replied'] ?? ''));
$search         = trim((string) ($_GET['q'] ?? ''));

$rows = [];
$councilOptions = [];
if (db_available() && $campaignFilter !== '') {
    $stmt = db()->prepare(
        'SELECT id, full_name, council_area, email, status, resend_id, error_message, sent_at, replied_at, reply_notes
         FROM councillor_campaign_sends WHERE campaign_slug = ? ORDER BY council_area ASC, full_name ASC'
    );
    $stmt->execute([$campaignFilter]);
    $allRows = $stmt->fetchAll();
    $councilOptions = array_values(array_unique(array_column($allRows, 'council_area')));
    sort($councilOptions);

    $rows = array_filter($allRows, function (array $r) use ($councilFilter, $statusFilter, $repliedFilter, $search): bool {
        if ($councilFilter !== '' && $r['council_area'] !== $councilFilter) return false;
        if ($statusFilter !== '' && $r['status'] !== $statusFilter) return false;
        if ($repliedFilter === 'yes' && empty($r['replied_at'])) return false;
        if ($repliedFilter === 'no' && !empty($r['replied_at'])) return false;
        if ($search !== '' && stripos($r['full_name'] . ' ' . $r['email'], $search) === false) return false;
        return true;
    });
}

$sentCount    = 0;
$failedCount  = 0;
$repliedCount = 0;
foreach ($rows as $r) {
    if ($r['status'] === 'sent') $sentCount++;
    if ($r['status'] === 'failed') $failedCount++;
    if (!empty($r['replied_at'])) $repliedCount++;
}
$totalMatched = count($rows);

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">Councillor campaign sends</h1>
</div>

<p class="meta" style="margin-bottom:1.25rem">Read-only log of individual mail-merge sends from <code>bin/send-councillor-campaign.php</code> — the script writes here itself, this page is for monitoring and logging replies only. Replies aren't captured automatically (no inbound-email integration); when you read one in the <code>hello@wires.org.uk</code> inbox, log it here manually.</p>

<?php if (empty($campaigns)): ?>
    <div class="admin-table-wrap"><p class="admin-empty">No campaign sends logged yet. Run <code>bin/send-councillor-campaign.php</code> from the command line first.</p></div>
<?php else: ?>

<div class="admin-stats" style="grid-template-columns:repeat(auto-fit,minmax(140px,1fr));margin-bottom:1.75rem">
    <div class="admin-stat">
        <span class="admin-stat-value"><?= $targetCount ?></span>
        <span class="admin-stat-label">Confirmed roster target</span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-value"><?= $sentCount ?></span>
        <span class="admin-stat-label">Sent (this filter)</span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-value"><?= $failedCount ?></span>
        <span class="admin-stat-label">Failed (this filter)</span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-value"><?= $repliedCount ?></span>
        <span class="admin-stat-label">Replied (this filter)</span>
    </div>
</div>

<form method="get" class="admin-filter-bar" style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.25rem">
    <select name="campaign" onchange="this.form.submit()">
        <?php foreach ($campaigns as $c): ?>
            <option value="<?= e($c) ?>" <?= $campaignFilter === $c ? 'selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="council" onchange="this.form.submit()">
        <option value="">All councils</option>
        <?php foreach ($councilOptions as $council): ?>
            <option value="<?= e($council) ?>" <?= $councilFilter === $council ? 'selected' : '' ?>><?= e($council) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="status" onchange="this.form.submit()">
        <option value="">Sent + failed</option>
        <option value="sent" <?= $statusFilter === 'sent' ? 'selected' : '' ?>>Sent only</option>
        <option value="failed" <?= $statusFilter === 'failed' ? 'selected' : '' ?>>Failed only</option>
    </select>
    <select name="replied" onchange="this.form.submit()">
        <option value="">Replied + not replied</option>
        <option value="yes" <?= $repliedFilter === 'yes' ? 'selected' : '' ?>>Replied only</option>
        <option value="no" <?= $repliedFilter === 'no' ? 'selected' : '' ?>>Not replied yet</option>
    </select>
    <input type="text" name="q" placeholder="Search name or email" value="<?= e($search) ?>">
    <button type="submit" class="btn btn-secondary">Filter</button>
    <?php if ($councilFilter !== '' || $statusFilter !== '' || $repliedFilter !== '' || $search !== ''): ?>
        <a class="admin-link" href="/admin/councillor-campaign.php?campaign=<?= e($campaignFilter) ?>">Clear filters</a>
    <?php endif; ?>
</form>

<?php if (empty($rows)): ?>
    <div class="admin-table-wrap"><p class="admin-empty">No sends match this filter.</p></div>
<?php else: ?>
    <p class="meta" style="margin-bottom:0.75rem"><?= $totalMatched ?> shown</p>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Council</th><th>Name</th><th>Email</th><th>Status</th><th>Sent</th><th>Reply</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= e($r['council_area']) ?></td>
                    <td><strong><?= e($r['full_name']) ?></strong></td>
                    <td class="meta"><?= e($r['email']) ?></td>
                    <td><span class="pill <?= $r['status'] === 'sent' ? 'pill--active' : 'pill--seeking' ?>"><?= e($r['status']) ?></span></td>
                    <td class="meta"><?= e(format_date(substr((string) $r['sent_at'], 0, 10))) ?></td>
                    <td class="meta">
                        <?php if (!empty($r['replied_at'])): ?>
                            Replied <?= e(format_date((string) $r['replied_at'])) ?>
                        <?php else: ?>
                            &mdash;
                        <?php endif; ?>
                    </td>
                    <td class="col-actions">
                        <a class="admin-link" href="/admin/councillor-campaign-reply.php?id=<?= (int) $r['id'] ?>">Log reply</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
