<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'Contact messages';
$adminSection = 'messages';

/* Mark a message as read */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validate($_POST['csrf_token'] ?? null)) {
    $id     = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($id > 0 && in_array($action, ['read', 'unread'], true) && db_available()) {
        try {
            /* Add a read column if it doesn't exist yet — safe to run repeatedly */
            db()->query("ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS is_read TINYINT(1) NOT NULL DEFAULT 0");
            db()->prepare('UPDATE contact_messages SET is_read = :v WHERE id = :id')
                ->execute(['v' => $action === 'read' ? 1 : 0, 'id' => $id]);
        } catch (Throwable) {}
    }
    header('Location: /admin/messages.php');
    exit;
}

$messages = [];
if (db_available()) {
    try {
        /* Try with is_read column; fall back without it */
        try {
            $messages = db()->query(
                'SELECT id, full_name, email, subject, body, created_at, is_read
                 FROM contact_messages ORDER BY created_at DESC'
            )->fetchAll();
        } catch (Throwable) {
            $messages = db()->query(
                'SELECT id, full_name, email, subject, body, created_at, 0 AS is_read
                 FROM contact_messages ORDER BY created_at DESC'
            )->fetchAll();
        }
    } catch (Throwable) {}
}

$unread = count(array_filter($messages, fn($m) => !(int)$m['is_read']));

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">
        Messages
        <?php if ($unread > 0): ?>
            <span style="font-size:0.85rem;font-weight:700;background:var(--signal);color:#fff;padding:0.15rem 0.55rem;border-radius:999px;margin-left:0.5rem"><?= $unread ?> unread</span>
        <?php else: ?>
            <span style="font-size:1rem;font-weight:600;color:var(--muted)">(<?= count($messages) ?>)</span>
        <?php endif; ?>
    </h1>
    <a class="admin-btn-sm" href="/contact" target="_blank" rel="noopener">View contact form</a>
</div>

<?php if (empty($messages)): ?>
    <div class="admin-table-wrap">
        <p class="admin-empty">No messages yet.</p>
    </div>
<?php else: ?>
    <div style="display:flex;flex-direction:column;gap:1rem">
        <?php foreach ($messages as $msg):
            $read = (int) $msg['is_read'];
        ?>
        <div class="admin-form" style="padding:1.35rem 1.5rem;<?= !$read ? 'border-left:3px solid var(--signal)' : 'border-left:3px solid var(--line)' ?>">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:0.75rem">
                <div>
                    <strong><?= e((string) $msg['full_name']) ?></strong>
                    — <a href="mailto:<?= e((string) $msg['email']) ?>"><?= e((string) $msg['email']) ?></a>
                    <?php if (!$read): ?>
                        <span class="pill pill--seeking" style="margin-left:0.5rem">Unread</span>
                    <?php endif; ?>
                </div>
                <p class="meta" style="margin:0;white-space:nowrap"><?= e(format_date(substr((string) $msg['created_at'], 0, 10))) ?></p>
            </div>
            <p style="font-weight:700;margin:0 0 0.5rem;font-size:0.95rem"><?= e((string) $msg['subject']) ?></p>
            <p style="margin:0;color:var(--muted);font-size:0.9rem;line-height:1.65;white-space:pre-wrap"><?= e((string) $msg['body']) ?></p>
            <div style="margin-top:1rem;display:flex;gap:0.5rem">
                <a class="btn btn-primary btn-sm" href="mailto:<?= e((string) $msg['email']) ?>?subject=Re: <?= e(rawurlencode((string) $msg['subject'])) ?>">Reply by email</a>
                <form method="post" style="margin:0">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= (int) $msg['id'] ?>">
                    <button class="btn btn-ghost btn-sm" type="submit" name="action" value="<?= $read ? 'unread' : 'read' ?>">
                        Mark as <?= $read ? 'unread' : 'read' ?>
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
