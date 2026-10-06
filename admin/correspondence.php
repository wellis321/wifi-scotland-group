<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/correspondence.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'Correspondence';
$adminSection = 'correspondence';

$categoryFilter  = (string) ($_GET['category'] ?? '');
$statusFilter    = (string) ($_GET['status'] ?? '');
$directionFilter = (string) ($_GET['direction'] ?? '');
$search          = trim((string) ($_GET['q'] ?? ''));

$all = [];
if (db_available()) {
    try {
        $all = db()->query(
            'SELECT id, occurred_on, direction, person, organisation, category, email, subject, summary, action_taken, status
             FROM correspondence_log ORDER BY occurred_on DESC, id DESC'
        )->fetchAll();
    } catch (Throwable) {
    }
}

$rows = array_filter($all, static function (array $r) use ($categoryFilter, $statusFilter, $directionFilter, $search): bool {
    if ($categoryFilter !== '' && $r['category'] !== $categoryFilter) return false;
    if ($statusFilter !== '' && $r['status'] !== $statusFilter) return false;
    if ($directionFilter !== '' && $r['direction'] !== $directionFilter) return false;
    if ($search !== '') {
        $haystack = implode(' ', [$r['person'], $r['organisation'], $r['email'], $r['subject'], $r['summary'], $r['action_taken']]);
        if (stripos($haystack, $search) === false) return false;
    }
    return true;
});

$countBy = static fn(string $key, string $value): int => count(array_filter($all, static fn(array $r) => $r[$key] === $value));
$people  = count(array_unique(array_filter(array_column($all, 'email'))));
$filtered = $categoryFilter !== '' || $statusFilter !== '' || $directionFilter !== '' || $search !== '';

$statusPill = ['review' => 'pill pill--seeking', 'open' => 'pill pill--forming', 'done' => 'pill pill--active'];

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">Correspondence</h1>
    <a class="admin-btn-sm" href="/admin/correspondence-edit.php">Add an entry</a>
</div>

<p class="meta" style="margin-bottom:1.25rem">A lasting record of real emails in and out: who it was, what was said, what we did about it, and whether anything is still outstanding. Automatic replies are left out. New emails from the <code>hello@wires.org.uk</code> Inbox and Sent folders are added every few hours as &ldquo;To review&rdquo;; open one to add a summary. Calls and meetings can be added by hand.</p>

<div class="admin-stats" style="grid-template-columns:repeat(auto-fit,minmax(140px,1fr));margin-bottom:1.75rem">
    <div class="admin-stat">
        <span class="admin-stat-value"><?= count($all) ?></span>
        <span class="admin-stat-label">Entries</span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-value"><?= $people ?></span>
        <span class="admin-stat-label">People</span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-value"><?= $countBy('status', 'review') ?></span>
        <span class="admin-stat-label">To review</span>
    </div>
    <div class="admin-stat">
        <span class="admin-stat-value"><?= $countBy('status', 'open') ?></span>
        <span class="admin-stat-label">Action needed</span>
    </div>
</div>

<form method="get" class="admin-filter-bar" style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.25rem">
    <select name="status" onchange="this.form.submit()">
        <option value="">Any status</option>
        <?php foreach (CORRESPONDENCE_STATUSES as $value => $label): ?>
            <option value="<?= e($value) ?>" <?= $statusFilter === $value ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="category" onchange="this.form.submit()">
        <option value="">Everyone</option>
        <?php foreach (CORRESPONDENCE_CATEGORIES as $value => $label): ?>
            <option value="<?= e($value) ?>" <?= $categoryFilter === $value ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="direction" onchange="this.form.submit()">
        <option value="">Received + sent</option>
        <option value="in" <?= $directionFilter === 'in' ? 'selected' : '' ?>>Received</option>
        <option value="out" <?= $directionFilter === 'out' ? 'selected' : '' ?>>Sent</option>
    </select>
    <input type="text" name="q" placeholder="Search name, subject or summary" value="<?= e($search) ?>">
    <button type="submit" class="btn btn-secondary">Filter</button>
    <?php if ($filtered): ?>
        <a class="admin-link" href="/admin/correspondence.php">Clear filters</a>
    <?php endif; ?>
</form>

<?php if (empty($all)): ?>
    <div class="admin-table-wrap"><p class="admin-empty">Nothing logged yet. Entries appear here once <code>bin/log-correspondence.php</code> has run, or you can add one by hand.</p></div>
<?php elseif (empty($rows)): ?>
    <div class="admin-table-wrap"><p class="admin-empty">No entries match this filter.</p></div>
<?php else: ?>
    <p class="meta" style="margin-bottom:0.75rem"><?= count($rows) ?> shown</p>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead><tr><th>Date</th><th></th><th>Who</th><th>Summary</th><th>Status</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td class="meta" style="white-space:nowrap"><?= e(format_date((string) $r['occurred_on'])) ?></td>
                    <td class="meta" style="white-space:nowrap"><?= $r['direction'] === 'in' ? 'Received' : 'Sent' ?></td>
                    <td>
                        <strong><?= e($r['person']) ?></strong>
                        <div class="meta"><?= e((string) ($r['organisation'] ?: CORRESPONDENCE_CATEGORIES[$r['category']])) ?></div>
                    </td>
                    <td>
                        <?php if (!empty($r['summary'])): ?>
                            <?= e($r['summary']) ?>
                        <?php else: ?>
                            <span class="meta"><?= e((string) $r['subject']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($r['action_taken'])): ?>
                            <div class="meta" style="margin-top:0.3rem"><strong>What we did:</strong> <?= e($r['action_taken']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><span class="<?= e($statusPill[$r['status']]) ?>" style="white-space:nowrap"><?= e(CORRESPONDENCE_STATUSES[$r['status']]) ?></span></td>
                    <td class="col-actions"><a class="admin-link" href="/admin/correspondence-edit.php?id=<?= (int) $r['id'] ?>">Open</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
