<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/correspondence.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminSection = 'correspondence';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$entry = null;
if ($id > 0 && db_available()) {
    $s = db()->prepare('SELECT * FROM correspondence_log WHERE id = :id LIMIT 1');
    $s->execute(['id' => $id]);
    $entry = $s->fetch() ?: null;
}

if ($id > 0 && !$entry) {
    flash_set('admin_err', 'That entry could not be found.');
    header('Location: /admin/correspondence.php');
    exit;
}

$isNew = $entry === null;
$adminTitle = $isNew ? 'Add correspondence' : 'Correspondence — ' . $entry['person'];
$errors = [];

$form = [
    'occurred_on'  => (string) ($entry['occurred_on'] ?? date('Y-m-d')),
    'direction'    => (string) ($entry['direction'] ?? 'in'),
    'person'       => (string) ($entry['person'] ?? ''),
    'organisation' => (string) ($entry['organisation'] ?? ''),
    'category'     => (string) ($entry['category'] ?? 'other'),
    'email'        => (string) ($entry['email'] ?? ''),
    'subject'      => (string) ($entry['subject'] ?? ''),
    'summary'      => (string) ($entry['summary'] ?? ''),
    'body_text'    => (string) ($entry['body_text'] ?? ''),
    'action_taken' => (string) ($entry['action_taken'] ?? ''),
    'status'       => (string) ($entry['status'] ?? 'done'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        flash_set('admin_err', 'Invalid form token.');
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    if (!$isNew && ($_POST['action'] ?? '') === 'delete') {
        db()->prepare('DELETE FROM correspondence_log WHERE id = :id')->execute(['id' => $entry['id']]);
        flash_set('admin_ok', 'Entry deleted.');
        header('Location: /admin/correspondence.php');
        exit;
    }

    foreach (array_keys($form) as $field) {
        // The text of an imported email is the record of what was said, so it is not editable.
        if ($field === 'body_text' && !$isNew && $entry['message_id'] !== null) {
            continue;
        }
        $form[$field] = trim((string) ($_POST[$field] ?? ''));
    }
    $form['email'] = strtolower($form['email']);

    if ($form['person'] === '') {
        $errors[] = 'Say who this was with.';
    }
    if (DateTime::createFromFormat('Y-m-d', $form['occurred_on']) === false) {
        $errors[] = 'Enter a valid date.';
    }
    if (!in_array($form['direction'], ['in', 'out'], true)) {
        $form['direction'] = 'in';
    }
    if (!isset(CORRESPONDENCE_CATEGORIES[$form['category']])) {
        $form['category'] = 'other';
    }
    if (!isset(CORRESPONDENCE_STATUSES[$form['status']])) {
        $form['status'] = 'review';
    }

    if (!$errors) {
        $params = array_map(static fn(string $v): ?string => $v === '' ? null : $v, $form);
        if ($isNew) {
            db()->prepare(
                'INSERT INTO correspondence_log (occurred_on, direction, person, organisation, category, email, subject, summary, body_text, action_taken, status)
                 VALUES (:occurred_on, :direction, :person, :organisation, :category, :email, :subject, :summary, :body_text, :action_taken, :status)'
            )->execute($params);
            flash_set('admin_ok', 'Entry added.');
        } else {
            db()->prepare(
                'UPDATE correspondence_log SET occurred_on = :occurred_on, direction = :direction, person = :person, organisation = :organisation,
                    category = :category, email = :email, subject = :subject, summary = :summary, body_text = :body_text,
                    action_taken = :action_taken, status = :status
                 WHERE id = :id'
            )->execute($params + ['id' => $entry['id']]);
            flash_set('admin_ok', 'Entry saved.');
        }
        header('Location: /admin/correspondence.php');
        exit;
    }
}

// Everything else logged with the same person, oldest first, so the entry reads in context.
$thread = [];
if (!$isNew && !empty($entry['email'])) {
    $t = db()->prepare('SELECT id, occurred_on, direction, subject, summary FROM correspondence_log WHERE email = :email AND id <> :id ORDER BY occurred_on, id');
    $t->execute(['email' => $entry['email'], 'id' => $entry['id']]);
    $thread = $t->fetchAll();
}

$imported = !$isNew && $entry['message_id'] !== null;

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title"><?= e($adminTitle) ?></h1>
    <a class="admin-btn-sm" href="/admin/correspondence.php">&larr; All correspondence</a>
</div>

<?php if ($errors): ?>
    <div class="admin-flash admin-flash--err"><?= e(implode(' ', $errors)) ?></div>
<?php endif; ?>

<?php if ($isNew): ?>
    <p class="meta" style="margin-bottom:1.25rem">Emails are added automatically. Use this for anything that isn&rsquo;t in the mailbox: a phone call, a meeting, a message through the contact form, a letter.</p>
<?php else: ?>
    <p class="meta" style="margin-bottom:1.25rem">
        <?= $entry['direction'] === 'in' ? 'Received from' : 'Sent to' ?> <?= e($entry['person']) ?>
        <?php if (!empty($entry['email'])): ?>&middot; <?= e($entry['email']) ?><?php endif; ?>
        &middot; <?= e(format_date((string) $entry['occurred_on'])) ?>
    </p>
<?php endif; ?>

<?php if ($imported): ?>
    <div style="border:1px solid var(--line);border-radius:var(--radius);padding:1rem 1.25rem;margin-bottom:1.5rem;background:#fff">
        <p class="admin-hint" style="margin-top:0;text-transform:uppercase;letter-spacing:0.05em;font-size:0.72rem"><?= $entry['direction'] === 'in' ? 'Email received' : 'Email sent' ?></p>
        <p style="font-weight:700;margin:0 0 0.6rem"><?= e((string) $entry['subject']) ?></p>
        <div style="white-space:pre-wrap;font-size:0.88rem;line-height:1.55;color:var(--muted);max-height:420px;overflow-y:auto"><?= e((string) $entry['body_text']) ?></div>
    </div>
<?php endif; ?>

<?php if ($thread): ?>
    <div style="margin-bottom:1.5rem">
        <p class="admin-hint" style="text-transform:uppercase;letter-spacing:0.05em;font-size:0.72rem">Also logged with this person</p>
        <ul style="margin:0.4rem 0 0;padding-left:1.1rem;font-size:0.9rem;line-height:1.6">
            <?php foreach ($thread as $t): ?>
                <li>
                    <a class="admin-link" href="/admin/correspondence-edit.php?id=<?= (int) $t['id'] ?>"><?= e(format_date((string) $t['occurred_on'])) ?>, <?= $t['direction'] === 'in' ? 'received' : 'sent' ?></a>
                    <span class="meta">&mdash; <?= e((string) ($t['summary'] ?: $t['subject'])) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form class="admin-form" method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div class="admin-field">
        <label for="summary">Summary <span style="font-weight:400;text-transform:none">(one or two lines: what was said)</span></label>
        <textarea id="summary" name="summary" rows="3"><?= e($form['summary']) ?></textarea>
    </div>

    <div class="admin-field">
        <label for="action_taken">What we did about it</label>
        <textarea id="action_taken" name="action_taken" rows="3" placeholder="e.g. Updated the council's tracker entry and replied to thank them."><?= e($form['action_taken']) ?></textarea>
        <p class="admin-hint">Note here anything they asked us not to do, such as not quoting or naming them.</p>
    </div>

    <div class="admin-field">
        <label for="status">Status</label>
        <select id="status" name="status">
            <?php foreach (CORRESPONDENCE_STATUSES as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $form['status'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
        <p class="admin-hint">&ldquo;Action needed&rdquo; keeps it on the list of things outstanding.</p>
    </div>

    <div class="admin-field">
        <label for="person">Who</label>
        <input id="person" name="person" type="text" maxlength="160" required value="<?= e($form['person']) ?>">
    </div>

    <div class="admin-field">
        <label for="organisation">Role or organisation</label>
        <input id="organisation" name="organisation" type="text" maxlength="200" value="<?= e($form['organisation']) ?>">
    </div>

    <div class="admin-field">
        <label for="category">Type</label>
        <select id="category" name="category">
            <?php foreach (CORRESPONDENCE_CATEGORIES as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $form['category'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="admin-field">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" maxlength="255" value="<?= e($form['email']) ?>">
        <p class="admin-hint">Entries with the same email are shown together as one conversation.</p>
    </div>

    <div class="admin-field">
        <label for="occurred_on">Date</label>
        <input id="occurred_on" name="occurred_on" type="date" required value="<?= e($form['occurred_on']) ?>">
    </div>

    <div class="admin-field">
        <label for="direction">Direction</label>
        <select id="direction" name="direction">
            <option value="in" <?= $form['direction'] === 'in' ? 'selected' : '' ?>>Received</option>
            <option value="out" <?= $form['direction'] === 'out' ? 'selected' : '' ?>>Sent</option>
        </select>
    </div>

    <div class="admin-field">
        <label for="subject">Subject</label>
        <input id="subject" name="subject" type="text" maxlength="255" value="<?= e($form['subject']) ?>">
    </div>

    <?php if (!$imported): ?>
        <div class="admin-field">
            <label for="body_text">Full text or notes <span style="font-weight:400;text-transform:none">(optional)</span></label>
            <textarea id="body_text" name="body_text" rows="8"><?= e($form['body_text']) ?></textarea>
        </div>
    <?php endif; ?>

    <div class="admin-form-actions">
        <button class="btn btn-primary" type="submit">Save</button>
        <a class="btn btn-ghost" href="/admin/correspondence.php">Cancel</a>
    </div>
</form>

<?php if (!$isNew): ?>
    <form method="post" style="margin-top:2rem" onsubmit="return confirm('Delete this entry from the log? The email itself stays in the mailbox.');">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="delete">
        <button class="admin-link admin-link--danger" type="submit" style="background:none;border:0;cursor:pointer">Delete this entry</button>
        <?php if ($imported): ?>
            <p class="admin-hint">An email deleted here is imported again if it is less than a week old.</p>
        <?php endif; ?>
    </form>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
