<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminSection = 'schemes';

$item  = null;
$isNew = true;
$id    = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0 && db_available()) {
    $s = db()->prepare('SELECT * FROM schemes WHERE id = :id LIMIT 1');
    $s->execute(['id' => $id]);
    $item = $s->fetch() ?: null;
    if ($item) $isNew = false;
}

$adminTitle = $isNew ? 'New scheme' : 'Edit scheme';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        flash_set('admin_err', 'Invalid form token.');
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    if (isset($_POST['_delete']) && !$isNew && $item) {
        db()->prepare('DELETE FROM schemes WHERE id = :id')->execute(['id' => $item['id']]);
        flash_set('admin_ok', 'Scheme deleted.');
        header('Location: /admin/schemes.php');
        exit;
    }

    $f = [
        'slug'          => trim((string) ($_POST['slug'] ?? '')),
        'name'          => trim((string) ($_POST['name'] ?? '')),
        'summary'       => trim((string) ($_POST['summary'] ?? '')),
        'who_for'       => trim((string) ($_POST['who_for'] ?? '')),
        'what_you_get'  => trim((string) ($_POST['what_you_get'] ?? '')),
        'how_to_apply'  => trim((string) ($_POST['how_to_apply'] ?? '')),
        'url'           => trim((string) ($_POST['url'] ?? '')),
        'source_label'  => trim((string) ($_POST['source_label'] ?? '')),
        'updated_month' => trim((string) ($_POST['updated_month'] ?? '')),
        'status'        => in_array($_POST['status']??'', ['active','check','ended']) ? $_POST['status'] : 'active',
        'scope'         => in_array($_POST['scope']??'', ['uk','scotland']) ? $_POST['scope'] : 'uk',
        'note'          => trim((string) ($_POST['note'] ?? '')) ?: null,
        'sort_order'    => (int) ($_POST['sort_order'] ?? 0),
    ];

    $errors = [];
    if ($f['name'] === '')         $errors[] = 'Name is required.';
    if ($f['slug'] === '')         $errors[] = 'Slug is required.';
    if ($f['summary'] === '')      $errors[] = 'Summary is required.';
    if ($f['who_for'] === '')      $errors[] = 'Who it\'s for is required.';
    if ($f['what_you_get'] === '') $errors[] = 'What you get is required.';
    if ($f['how_to_apply'] === '') $errors[] = 'How to apply is required.';
    if ($f['url'] === '')          $errors[] = 'Official URL is required.';
    if ($f['source_label'] === '') $errors[] = 'Source label is required.';

    if (empty($errors)) {
        $params = $f + ($isNew ? [] : ['id' => $item['id']]);
        if ($isNew) {
            db()->prepare(
                'INSERT INTO schemes (slug,name,summary,who_for,what_you_get,how_to_apply,url,source_label,updated_month,status,scope,note,sort_order) VALUES (:slug,:name,:summary,:who_for,:what_you_get,:how_to_apply,:url,:source_label,:updated_month,:status,:scope,:note,:sort_order)'
            )->execute($params);
            $newId = (int) db()->lastInsertId();
            flash_set('admin_ok', 'Scheme created.');
            header('Location: /admin/scheme-edit.php?id=' . $newId);
        } else {
            db()->prepare(
                'UPDATE schemes SET slug=:slug,name=:name,summary=:summary,who_for=:who_for,what_you_get=:what_you_get,how_to_apply=:how_to_apply,url=:url,source_label=:source_label,updated_month=:updated_month,status=:status,scope=:scope,note=:note,sort_order=:sort_order WHERE id=:id'
            )->execute($params);
            flash_set('admin_ok', 'Scheme saved.');
            header('Location: /admin/scheme-edit.php?id=' . $item['id']);
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
</div>

<form class="admin-form" method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div class="admin-field">
        <label for="title">Name</label>
        <input id="title" name="name" type="text" required value="<?= e((string)($item['name']??'')) ?>">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
        <div class="admin-field">
            <label for="slug">Slug</label>
            <input id="slug" name="slug" type="text" required value="<?= e((string)($item['slug']??'')) ?>">
        </div>
        <div class="admin-field">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="active" <?= ($item['status']??'')==='active'?'selected':'' ?>>Currently active</option>
                <option value="check" <?= ($item['status']??'')==='check'?'selected':'' ?>>Check current status</option>
                <option value="ended" <?= ($item['status']??'')==='ended'?'selected':'' ?>>Ended</option>
            </select>
        </div>
        <div class="admin-field">
            <label for="scope">Scope</label>
            <select id="scope" name="scope">
                <option value="uk" <?= ($item['scope']??'')==='uk'?'selected':'' ?>>UK-wide</option>
                <option value="scotland" <?= ($item['scope']??'')==='scotland'?'selected':'' ?>>Scotland only</option>
            </select>
        </div>
    </div>

    <div class="admin-field">
        <label for="summary">Summary <span style="font-weight:400;text-transform:none">(1–2 sentences shown in listing)</span></label>
        <textarea id="summary" name="summary" required><?= e((string)($item['summary']??'')) ?></textarea>
    </div>

    <div class="admin-field">
        <label for="who_for">Who it's for</label>
        <textarea id="who_for" name="who_for" required><?= e((string)($item['who_for']??'')) ?></textarea>
    </div>

    <div class="admin-field">
        <label for="what_you_get">What you get</label>
        <textarea id="what_you_get" name="what_you_get" required><?= e((string)($item['what_you_get']??'')) ?></textarea>
    </div>

    <div class="admin-field">
        <label for="how_to_apply">How to apply / find out more</label>
        <textarea id="how_to_apply" name="how_to_apply" required><?= e((string)($item['how_to_apply']??'')) ?></textarea>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1rem">
        <div class="admin-field">
            <label for="url">Official URL</label>
            <input id="url" name="url" type="url" required value="<?= e((string)($item['url']??'')) ?>">
        </div>
        <div class="admin-field">
            <label for="source_label">Link label</label>
            <input id="source_label" name="source_label" type="text" required value="<?= e((string)($item['source_label']??'')) ?>" placeholder="e.g. Ofcom: social tariffs guide">
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div class="admin-field">
            <label for="updated_month">Last verified (YYYY-MM)</label>
            <input id="updated_month" name="updated_month" type="month" value="<?= e((string)($item['updated_month']??'')) ?>">
            <p class="admin-hint">Update this whenever you check the scheme is still current.</p>
        </div>
        <div class="admin-field">
            <label for="sort_order">Sort order</label>
            <input id="sort_order" name="sort_order" type="number" min="0" value="<?= (int)($item['sort_order']??0) ?>">
            <p class="admin-hint">Lower numbers appear first (within the same verified month).</p>
        </div>
    </div>

    <div class="admin-field">
        <label for="note">Caveat / note <span style="font-weight:400;text-transform:none">(optional — shown highlighted)</span></label>
        <textarea id="note" name="note"><?= e((string)($item['note']??'')) ?></textarea>
    </div>

    <div class="admin-form-actions">
        <button class="btn btn-primary" type="submit"><?= $isNew ? 'Add scheme' : 'Save changes' ?></button>
        <a class="btn btn-ghost" href="/admin/schemes.php">Cancel</a>
        <?php if (!$isNew): ?>
            <div class="admin-delete-zone">
                <button class="btn" style="background:rgba(226,85,64,0.1);color:#7a2f24" type="submit" name="_delete" value="1"
                    data-confirm="Delete this scheme?">Delete scheme</button>
            </div>
        <?php endif; ?>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
