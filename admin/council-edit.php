<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminSection = 'councils';

/** The 32 Scottish council areas — matches data/scotland-council-areas.min.geojson used on wifi-map.php. */
const COUNCIL_AREAS = [
    'Aberdeen City', 'Aberdeenshire', 'Angus', 'Argyll and Bute', 'City of Edinburgh',
    'Clackmannanshire', 'Dumfries and Galloway', 'Dundee City', 'East Ayrshire',
    'East Dunbartonshire', 'East Lothian', 'East Renfrewshire', 'Eilean Siar', 'Falkirk',
    'Fife', 'Glasgow City', 'Highland', 'Inverclyde', 'Midlothian', 'Moray',
    'North Ayrshire', 'North Lanarkshire', 'Orkney Islands', 'Perth and Kinross',
    'Renfrewshire', 'Scottish Borders', 'Shetland Islands', 'South Ayrshire',
    'South Lanarkshire', 'Stirling', 'West Dunbartonshire', 'West Lothian',
];

$item  = null;
$isNew = true;
$id    = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0 && db_available()) {
    $s = db()->prepare('SELECT * FROM council_contacts WHERE id = :id LIMIT 1');
    $s->execute(['id' => $id]);
    $item = $s->fetch() ?: null;
    if ($item) $isNew = false;
}

$adminTitle = $isNew ? 'New council contact' : 'Edit council contact';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        flash_set('admin_err', 'Invalid form token.');
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    if (isset($_POST['_delete']) && !$isNew && $item) {
        db()->prepare('DELETE FROM council_contacts WHERE id = :id')->execute(['id' => $item['id']]);
        flash_set('admin_ok', 'Council contact deleted.');
        header('Location: /admin/councils.php');
        exit;
    }

    $f = [
        'council_area'      => trim((string) ($_POST['council_area'] ?? '')),
        'directory_url'     => trim((string) ($_POST['directory_url'] ?? '')) ?: null,
        'contact_method'    => trim((string) ($_POST['contact_method'] ?? '')) ?: null,
        'councillor_count'  => trim((string) ($_POST['councillor_count'] ?? '')) ?: null,
        'status'            => in_array($_POST['status'] ?? '', ['confirmed', 'check', 'blocked'], true) ? $_POST['status'] : 'check',
        'notes'             => trim((string) ($_POST['notes'] ?? '')) ?: null,
    ];

    $errors = [];
    if ($f['council_area'] === '' || !in_array($f['council_area'], COUNCIL_AREAS, true)) {
        $errors[] = 'Choose one of the 32 official council areas.';
    }

    if (empty($errors)) {
        $params = $f + ($isNew ? [] : ['id' => $item['id']]);
        try {
            if ($isNew) {
                db()->prepare(
                    'INSERT INTO council_contacts (council_area, directory_url, contact_method, councillor_count, status, notes) VALUES (:council_area, :directory_url, :contact_method, :councillor_count, :status, :notes)'
                )->execute($params);
                $newId = (int) db()->lastInsertId();
                flash_set('admin_ok', 'Council contact added.');
                header('Location: /admin/council-edit.php?id=' . $newId);
            } else {
                db()->prepare(
                    'UPDATE council_contacts SET council_area=:council_area, directory_url=:directory_url, contact_method=:contact_method, councillor_count=:councillor_count, status=:status, notes=:notes WHERE id=:id'
                )->execute($params);
                flash_set('admin_ok', 'Council contact saved.');
                header('Location: /admin/council-edit.php?id=' . $item['id']);
            }
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'That council already has an entry — edit it instead of adding a new one.';
            } else {
                throw $e;
            }
        }
    }

    flash_set('admin_err', implode(' ', $errors));
    $item = array_merge($item ?? [], $f);
}

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title"><?= e($adminTitle) ?></h1>
</div>

<form class="admin-form" method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1rem">
        <div class="admin-field">
            <label for="council_area">Council area</label>
            <select id="council_area" name="council_area" required>
                <option value="">Choose a council&hellip;</option>
                <?php foreach (COUNCIL_AREAS as $area): ?>
                    <option value="<?= e($area) ?>" <?= ($item['council_area'] ?? '') === $area ? 'selected' : '' ?>><?= e($area) ?></option>
                <?php endforeach; ?>
            </select>
            <p class="admin-hint">Fixed list of Scotland's 32 councils — matches the wifi-map council areas, so nothing can be typo'd or duplicated.</p>
        </div>
        <div class="admin-field">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="confirmed" <?= ($item['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed — working contact</option>
                <option value="check" <?= ($item['status'] ?? '') === 'check' ? 'selected' : '' ?>>Needs a check</option>
                <option value="blocked" <?= ($item['status'] ?? '') === 'blocked' ? 'selected' : '' ?>>No direct email found</option>
            </select>
        </div>
    </div>

    <div class="admin-field">
        <label for="directory_url">Councillor directory URL</label>
        <input id="directory_url" name="directory_url" type="url" value="<?= e((string) ($item['directory_url'] ?? '')) ?>" placeholder="https://www.council.gov.uk/councillors">
        <p class="admin-hint">The council's own official page listing all its current councillors.</p>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1rem">
        <div class="admin-field">
            <label for="contact_method">Contact method / email pattern</label>
            <input id="contact_method" name="contact_method" type="text" value="<?= e((string) ($item['contact_method'] ?? '')) ?>" placeholder="e.g. firstname.lastname@council.gov.uk">
        </div>
        <div class="admin-field">
            <label for="councillor_count">Councillor count</label>
            <input id="councillor_count" name="councillor_count" type="text" value="<?= e((string) ($item['councillor_count'] ?? '')) ?>" placeholder="e.g. ~45">
        </div>
    </div>

    <div class="admin-field">
        <label for="notes">Notes</label>
        <textarea id="notes" name="notes"><?= e((string) ($item['notes'] ?? '')) ?></textarea>
    </div>

    <?php if (!$isNew && !empty($item['updated_at'])): ?>
        <p class="admin-hint">Last updated <?= e(format_date((string) $item['updated_at'])) ?>.</p>
    <?php endif; ?>

    <div class="admin-form-actions">
        <button class="btn btn-primary" type="submit"><?= $isNew ? 'Add council' : 'Save changes' ?></button>
        <a class="btn btn-ghost" href="/admin/councils.php">Cancel</a>
        <?php if (!$isNew): ?>
            <div class="admin-delete-zone">
                <button class="btn" style="background:rgba(226,85,64,0.1);color:#7a2f24" type="submit" name="_delete" value="1"
                    data-confirm="Delete this council's contact entry?">Delete entry</button>
            </div>
        <?php endif; ?>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
