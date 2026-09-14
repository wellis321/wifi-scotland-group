<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminSection = 'councillor-campaign';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$send = null;
if ($id > 0 && db_available()) {
    $s = db()->prepare('SELECT * FROM councillor_campaign_sends WHERE id = :id LIMIT 1');
    $s->execute(['id' => $id]);
    $send = $s->fetch() ?: null;
}

if (!$send) {
    flash_set('admin_err', 'That send record could not be found.');
    header('Location: /admin/councillor-campaign.php');
    exit;
}

$adminTitle = 'Log reply — ' . $send['full_name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        flash_set('admin_err', 'Invalid form token.');
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    $repliedAt  = trim((string) ($_POST['replied_at'] ?? '')) ?: null;
    $replyNotes = trim((string) ($_POST['reply_notes'] ?? '')) ?: null;

    db()->prepare('UPDATE councillor_campaign_sends SET replied_at = :replied_at, reply_notes = :reply_notes WHERE id = :id')
        ->execute(['replied_at' => $repliedAt, 'reply_notes' => $replyNotes, 'id' => $send['id']]);

    flash_set('admin_ok', 'Reply logged.');
    header('Location: /admin/councillor-campaign.php?campaign=' . urlencode($send['campaign_slug']));
    exit;
}

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title"><?= e($adminTitle) ?></h1>
</div>

<p class="meta" style="margin-bottom:1.25rem">
    <?= e($send['council_area']) ?> &middot; <?= e($send['email']) ?> &middot; sent <?= e(format_date(substr((string) $send['sent_at'], 0, 10))) ?>
</p>

<?php if (!empty($send['subject']) || !empty($send['body_text'])): ?>
    <div style="border:1px solid var(--line);border-radius:var(--radius);padding:1rem 1.25rem;margin-bottom:1.5rem;background:#fff">
        <p class="admin-hint" style="margin-top:0;text-transform:uppercase;letter-spacing:0.05em;font-size:0.72rem">Message sent</p>
        <p style="font-weight:700;margin:0 0 0.6rem"><?= e((string) ($send['subject'] ?? '')) ?></p>
        <div style="white-space:pre-wrap;font-size:0.88rem;line-height:1.55;color:var(--muted);max-height:320px;overflow-y:auto"><?= e((string) ($send['body_text'] ?? '')) ?></div>
    </div>
<?php else: ?>
    <p class="admin-hint" style="margin-bottom:1.5rem">Sent before message history was tracked — exact copy not recorded for this one.</p>
<?php endif; ?>

<form class="admin-form" method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div class="admin-field">
        <label for="replied_at">Reply received on</label>
        <input id="replied_at" name="replied_at" type="date" value="<?= e((string) ($send['replied_at'] ?? '')) ?>">
        <p class="admin-hint">Leave blank to mark as not yet replied.</p>
    </div>

    <div class="admin-field">
        <label for="reply_notes">Notes <span style="font-weight:400;text-transform:none">(internal only — not shown publicly anywhere)</span></label>
        <textarea id="reply_notes" name="reply_notes" placeholder="e.g. Supportive reply, agreed to a quotable line: '...'"><?= e((string) ($send['reply_notes'] ?? '')) ?></textarea>
    </div>

    <div class="admin-form-actions">
        <button class="btn btn-primary" type="submit">Save</button>
        <a class="btn btn-ghost" href="/admin/councillor-campaign.php?campaign=<?= e($send['campaign_slug']) ?>">Cancel</a>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
