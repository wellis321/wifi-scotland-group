<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminSection = 'events';

$item  = null;
$isNew = true;
$id    = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$preGroupId = isset($_GET['group_id']) ? (int) $_GET['group_id'] : 0;

if ($id > 0 && db_available()) {
    $s = db()->prepare('SELECT * FROM group_events WHERE id = :id LIMIT 1');
    $s->execute(['id' => $id]);
    $item = $s->fetch() ?: null;
    if ($item) $isNew = false;
}

$adminTitle = $isNew ? 'New event' : 'Edit event';

$groups = db_available()
    ? db()->query('SELECT id, council_area FROM local_groups ORDER BY council_area')->fetchAll()
    : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        flash_set('admin_err', 'Invalid form token.');
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    if (isset($_POST['_delete']) && !$isNew && $item) {
        db()->prepare('DELETE FROM group_events WHERE id = :id')->execute(['id' => $item['id']]);
        flash_set('admin_ok', 'Event deleted.');
        header('Location: /admin/events.php?group_id=' . (int) $item['group_id']);
        exit;
    }

    $f = [
        'group_id'      => (int) ($_POST['group_id'] ?? 0),
        'title'         => trim((string) ($_POST['title'] ?? '')),
        'description'   => trim((string) ($_POST['description'] ?? '')) ?: null,
        'event_date'    => trim((string) ($_POST['event_date'] ?? '')),
        'event_time'    => trim((string) ($_POST['event_time'] ?? '')) ?: null,
        'location_text' => trim((string) ($_POST['location_text'] ?? '')) ?: null,
        'online_url'    => trim((string) ($_POST['online_url'] ?? '')) ?: null,
    ];

    $errors = [];
    if ($f['group_id'] === 0)  $errors[] = 'Please select a group.';
    if ($f['title'] === '')    $errors[] = 'Title is required.';
    if ($f['event_date'] === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $f['event_date'])) $errors[] = 'Valid date required.';

    if (empty($errors)) {
        if ($isNew) {
            db()->prepare(
                'INSERT INTO group_events (group_id,title,description,event_date,event_time,location_text,online_url) VALUES (:gid,:title,:desc,:date,:time,:loc,:url)'
            )->execute(['gid'=>$f['group_id'],'title'=>$f['title'],'desc'=>$f['description'],'date'=>$f['event_date'],'time'=>$f['event_time'],'loc'=>$f['location_text'],'url'=>$f['online_url']]);
            $newId = (int) db()->lastInsertId();
            flash_set('admin_ok', 'Event created.');
            header('Location: /admin/event-edit.php?id=' . $newId);
        } else {
            db()->prepare(
                'UPDATE group_events SET group_id=:gid,title=:title,description=:desc,event_date=:date,event_time=:time,location_text=:loc,online_url=:url WHERE id=:id'
            )->execute(['gid'=>$f['group_id'],'title'=>$f['title'],'desc'=>$f['description'],'date'=>$f['event_date'],'time'=>$f['event_time'],'loc'=>$f['location_text'],'url'=>$f['online_url'],'id'=>$item['id']]);
            flash_set('admin_ok', 'Event saved.');
            header('Location: /admin/event-edit.php?id=' . $item['id']);
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
        <label for="group_id">Group</label>
        <select id="group_id" name="group_id" required>
            <option value="">— Select a group —</option>
            <?php foreach ($groups as $g):
                $sel = ((int)($item['group_id']??$preGroupId)) === (int)$g['id'] ? 'selected' : ''; ?>
                <option value="<?= (int)$g['id'] ?>" <?= $sel ?>><?= e($g['council_area']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="admin-field">
        <label for="title">Event title</label>
        <input id="title" name="title" type="text" required value="<?= e((string)($item['title']??'')) ?>">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div class="admin-field">
            <label for="event_date">Date</label>
            <input id="event_date" name="event_date" type="date" required value="<?= e((string)($item['event_date']??'')) ?>">
        </div>
        <div class="admin-field">
            <label for="event_time">Time <span style="font-weight:400;text-transform:none">(optional)</span></label>
            <input id="event_time" name="event_time" type="text" value="<?= e((string)($item['event_time']??'')) ?>" placeholder="e.g. 7:00pm">
        </div>
    </div>

    <div class="admin-field">
        <label for="location_text">Location <span style="font-weight:400;text-transform:none">(optional)</span></label>
        <input id="location_text" name="location_text" type="text" value="<?= e((string)($item['location_text']??'')) ?>" placeholder="e.g. Dundee Central Library, 1 The Waterfront">
    </div>

    <div class="admin-field">
        <label for="online_url">Online link <span style="font-weight:400;text-transform:none">(optional — for virtual or hybrid events)</span></label>
        <input id="online_url" name="online_url" type="url" value="<?= e((string)($item['online_url']??'')) ?>" placeholder="https://...">
    </div>

    <div class="admin-field">
        <label for="description">Description <span style="font-weight:400;text-transform:none">(optional)</span></label>
        <textarea id="description" name="description"><?= e((string)($item['description']??'')) ?></textarea>
    </div>

    <div class="admin-form-actions">
        <button class="btn btn-primary" type="submit"><?= $isNew ? 'Create event' : 'Save changes' ?></button>
        <a class="btn btn-ghost" href="/admin/events.php<?= $preGroupId ? '?group_id='.$preGroupId : '' ?>">Cancel</a>
        <?php if (!$isNew): ?>
            <div class="admin-delete-zone">
                <button class="btn" style="background:rgba(226,85,64,0.1);color:#7a2f24" type="submit" name="_delete" value="1"
                    data-confirm="Delete this event?">Delete event</button>
            </div>
        <?php endif; ?>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
