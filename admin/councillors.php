<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'Individual councillors';
$adminSection = 'councillors';

$csvPath = dirname(__DIR__) . '/data/councillors-roster.csv';
$rows = [];
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
            $rows[] = array_combine($header, $line);
        }
        fclose($fh);
    }
}

$counts = [];
foreach ($rows as $r) {
    $c = $r['confidence'] ?? 'unknown';
    $counts[$c] = ($counts[$c] ?? 0) + 1;
}
arsort($counts);

$pillClass = static function (string $confidence): string {
    return match (true) {
        $confidence === 'confirmed' => 'pill--active',
        in_array($confidence, ['pattern-low', 'unconfirmed', 'flagged'], true) => 'pill--seeking',
        default => 'pill--forming',
    };
};

$councilFilter = trim((string) ($_GET['council'] ?? ''));
$confidenceFilter = trim((string) ($_GET['confidence'] ?? ''));
$search = trim((string) ($_GET['q'] ?? ''));

$councilOptions = array_values(array_unique(array_column($rows, 'council')));
sort($councilOptions);

$filtered = array_filter($rows, static function (array $r) use ($councilFilter, $confidenceFilter, $search): bool {
    if ($councilFilter !== '' && $r['council'] !== $councilFilter) return false;
    if ($confidenceFilter !== '' && $r['confidence'] !== $confidenceFilter) return false;
    if ($search !== '' && stripos($r['full_name'] . ' ' . $r['email'], $search) === false) return false;
    return true;
});

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">Individual councillors</h1>
</div>

<p class="meta" style="margin-bottom:1.25rem">Compiled roster of individual councillors across all 32 Scottish councils, sourced from council rosters and verified where possible. This is a working export for the mail-merge send, not a database table — edit <code>data/councillors-roster.csv</code> directly to make corrections.</p>

<div class="admin-stats" style="grid-template-columns:repeat(auto-fit,minmax(140px,1fr));margin-bottom:1.75rem">
    <div class="admin-stat">
        <span class="admin-stat-value"><?= count($rows) ?></span>
        <span class="admin-stat-label">Total rows</span>
    </div>
    <?php foreach ($counts as $label => $count): ?>
    <div class="admin-stat">
        <span class="admin-stat-value"><?= (int) $count ?></span>
        <span class="admin-stat-label"><?= e(ucfirst(str_replace('-', ' ', $label))) ?></span>
    </div>
    <?php endforeach; ?>
</div>

<form method="get" class="admin-filter-bar" style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.25rem">
    <select name="council" onchange="this.form.submit()">
        <option value="">All councils</option>
        <?php foreach ($councilOptions as $council): ?>
            <option value="<?= e($council) ?>" <?= $councilFilter === $council ? 'selected' : '' ?>><?= e($council) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="confidence" onchange="this.form.submit()">
        <option value="">All confidence levels</option>
        <?php foreach (array_keys($counts) as $label): ?>
            <option value="<?= e($label) ?>" <?= $confidenceFilter === $label ? 'selected' : '' ?>><?= e(ucfirst(str_replace('-', ' ', $label))) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="text" name="q" placeholder="Search name or email" value="<?= e($search) ?>">
    <button type="submit" class="btn btn-secondary">Filter</button>
    <?php if ($councilFilter !== '' || $confidenceFilter !== '' || $search !== ''): ?>
        <a class="admin-link" href="/admin/councillors.php">Clear filters</a>
    <?php endif; ?>
</form>

<?php if (empty($rows)): ?>
    <div class="admin-table-wrap"><p class="admin-empty">No roster file found at <code>data/councillors-roster.csv</code>.</p></div>
<?php else: ?>
    <p class="meta" style="margin-bottom:0.75rem"><?= count($filtered) ?> of <?= count($rows) ?> shown</p>
    <div class="admin-table-wrap">
        <table class="admin-table" style="table-layout:fixed;min-width:980px">
            <colgroup>
                <col style="width:110px">
                <col style="width:150px">
                <col style="width:170px">
                <col style="width:200px">
                <col style="width:110px">
                <col style="width:240px">
            </colgroup>
            <thead><tr><th>Council</th><th>Name</th><th>Ward</th><th>Email</th><th>Confidence</th><th>Notes</th></tr></thead>
            <tbody>
            <?php foreach ($filtered as $r): ?>
                <tr>
                    <td><?= e($r['council']) ?></td>
                    <td><strong><?= e($r['full_name']) ?></strong></td>
                    <td class="meta"><?= e($r['ward'] ?? '') ?></td>
                    <td class="meta" style="word-break:break-word"><?= e($r['email'] ?? '') ?></td>
                    <td><span class="pill <?= $pillClass($r['confidence'] ?? '') ?>"><?= e(str_replace('-', ' ', $r['confidence'] ?? '')) ?></span></td>
                    <td class="meta"><?= e($r['notes'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
