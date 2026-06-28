<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminSection = 'groups';

$item  = null;
$isNew = true;
$id    = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0 && db_available()) {
    $s = db()->prepare('SELECT * FROM local_groups WHERE id = :id LIMIT 1');
    $s->execute(['id' => $id]);
    $item = $s->fetch() ?: null;
    if ($item) $isNew = false;
}

$adminTitle = $isNew ? 'New group' : 'Edit group: ' . ($item['council_area'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        flash_set('admin_err', 'Invalid form token.');
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    if (isset($_POST['_delete']) && !$isNew && $item) {
        db()->prepare('DELETE FROM local_groups WHERE id = :id')->execute(['id' => $item['id']]);
        flash_set('admin_ok', 'Group deleted.');
        header('Location: /admin/groups.php');
        exit;
    }

    $f = [
        'council_area'  => trim((string) ($_POST['council_area'] ?? '')),
        'slug'          => trim((string) ($_POST['slug'] ?? '')),
        'council_code'  => trim((string) ($_POST['council_code'] ?? '')) ?: null,
        'tagline'       => trim((string) ($_POST['tagline'] ?? '')) ?: null,
        'description'   => trim((string) ($_POST['description'] ?? '')) ?: null,
        'contact_name'  => trim((string) ($_POST['contact_name'] ?? '')) ?: null,
        'contact_email' => trim((string) ($_POST['contact_email'] ?? '')) ?: null,
        'social_url'    => trim((string) ($_POST['social_url'] ?? '')) ?: null,
        'status'        => in_array($_POST['status'] ?? '', ['active','forming','seeking_organiser']) ? $_POST['status'] : 'forming',
    ];

    $errors = [];
    if ($f['council_area'] === '') $errors[] = 'Council area is required.';
    if ($f['slug'] === '')         $errors[] = 'Slug is required.';
    if ($f['contact_email'] !== null && !filter_var($f['contact_email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';

    if (empty($errors)) {
        if ($isNew) {
            db()->prepare(
                'INSERT INTO local_groups (slug,council_area,council_code,tagline,description,contact_name,contact_email,social_url,status) VALUES (:slug,:ca,:cc,:tl,:desc,:cn,:ce,:su,:st)'
            )->execute(['slug'=>$f['slug'],'ca'=>$f['council_area'],'cc'=>$f['council_code'],'tl'=>$f['tagline'],'desc'=>$f['description'],'cn'=>$f['contact_name'],'ce'=>$f['contact_email'],'su'=>$f['social_url'],'st'=>$f['status']]);
            $newId = (int) db()->lastInsertId();
            flash_set('admin_ok', 'Group created.');
            header('Location: /admin/group-edit.php?id=' . $newId);
        } else {
            db()->prepare(
                'UPDATE local_groups SET slug=:slug,council_area=:ca,council_code=:cc,tagline=:tl,description=:desc,contact_name=:cn,contact_email=:ce,social_url=:su,status=:st WHERE id=:id'
            )->execute(['slug'=>$f['slug'],'ca'=>$f['council_area'],'cc'=>$f['council_code'],'tl'=>$f['tagline'],'desc'=>$f['description'],'cn'=>$f['contact_name'],'ce'=>$f['contact_email'],'su'=>$f['social_url'],'st'=>$f['status'],'id'=>$item['id']]);
            flash_set('admin_ok', 'Group saved.');
            header('Location: /admin/group-edit.php?id=' . $item['id']);
        }
        exit;
    }

    flash_set('admin_err', implode(' ', $errors));
    $item = array_merge($item ?? [], $f);
}

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title"><?= e($adminTitle) ?></h1>
    <?php if (!$isNew): ?>
        <a class="admin-btn-sm" href="/group.php?slug=<?= e(rawurlencode((string)($item['slug']??''))) ?>" target="_blank" rel="noopener">View live</a>
        <a class="admin-btn-sm" href="/admin/events.php?group_id=<?= (int)($item['id']??0) ?>">Manage events</a>
    <?php endif; ?>
</div>

<form class="admin-form" method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div class="admin-field">
            <label for="council_area">Council area</label>
            <input id="title" name="council_area" type="text" required value="<?= e((string)($item['council_area']??'')) ?>" placeholder="e.g. Dundee City">
            <p class="admin-hint">Used as the page heading. Match official council name where possible.</p>
        </div>
        <div class="admin-field">
            <label for="slug">Slug (URL)</label>
            <input id="slug" name="slug" type="text" required value="<?= e((string)($item['slug']??'')) ?>">
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div class="admin-field">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="forming" <?= ($item['status']??'')==='forming'?'selected':'' ?>>Forming</option>
                <option value="active" <?= ($item['status']??'')==='active'?'selected':'' ?>>Active</option>
                <option value="seeking_organiser" <?= ($item['status']??'')==='seeking_organiser'?'selected':'' ?>>Needs an organiser</option>
            </select>
        </div>
        <div class="admin-field">
            <label for="council_code">Council code <span style="font-weight:400;text-transform:none">(optional)</span></label>
            <input id="council_code" name="council_code" type="text" value="<?= e((string)($item['council_code']??'')) ?>" placeholder="e.g. S12000042">
            <p class="admin-hint">Scottish council code — links to the WiFi map.</p>
        </div>
    </div>

    <div class="admin-field">
        <label for="tagline">Tagline <span style="font-weight:400;text-transform:none">(optional, one line)</span></label>
        <input id="tagline" name="tagline" type="text" maxlength="255" value="<?= e((string)($item['tagline']??'')) ?>">
    </div>

    <div class="admin-field">
        <label for="description">About this group <span style="font-weight:400;text-transform:none">(optional — HTML supported)</span></label>
        <textarea id="description" name="description"><?= e((string)($item['description']??'')) ?></textarea>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div class="admin-field">
            <label for="contact_name">Contact name <span style="font-weight:400;text-transform:none">(optional)</span></label>
            <input id="contact_name" name="contact_name" type="text" value="<?= e((string)($item['contact_name']??'')) ?>">
        </div>
        <div class="admin-field">
            <label for="contact_email">Contact email <span style="font-weight:400;text-transform:none">(optional)</span></label>
            <input id="contact_email" name="contact_email" type="email" value="<?= e((string)($item['contact_email']??'')) ?>">
        </div>
    </div>

    <div class="admin-field">
        <label for="social_url">Social / group URL <span style="font-weight:400;text-transform:none">(optional)</span></label>
        <input id="social_url" name="social_url" type="url" value="<?= e((string)($item['social_url']??'')) ?>" placeholder="https://...">
    </div>

    <div class="admin-form-actions">
        <button class="btn btn-primary" type="submit"><?= $isNew ? 'Create group' : 'Save changes' ?></button>
        <a class="btn btn-ghost" href="/admin/groups.php">Cancel</a>
        <?php if (!$isNew): ?>
            <div class="admin-delete-zone">
                <button class="btn" style="background:rgba(226,85,64,0.1);color:#7a2f24" type="submit" name="_delete" value="1"
                    data-confirm="Delete this group and all its events?">Delete group</button>
            </div>
        <?php endif; ?>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
