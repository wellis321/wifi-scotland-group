<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once dirname(__DIR__) . '/includes/tip_crypto.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'Confidential tips';
$adminSection = 'tips';

/* Mark a tip as read */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_validate($_POST['csrf_token'] ?? null)) {
    $id     = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($id > 0 && in_array($action, ['read', 'unread'], true) && db_available()) {
        try {
            db()->prepare('UPDATE secure_tips SET is_read = :v WHERE id = :id')
                ->execute(['v' => $action === 'read' ? 1 : 0, 'id' => $id]);
        } catch (Throwable) {}
    }
    header('Location: /admin/tips.php');
    exit;
}

$tips = [];
if (db_available()) {
    try {
        $tips = db()->query(
            'SELECT id, ciphertext, created_at, is_read FROM secure_tips ORDER BY created_at DESC'
        )->fetchAll();
    } catch (Throwable) {}
}

$unread = count(array_filter($tips, fn($t) => !(int) $t['is_read']));

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">
        Confidential tips
        <?php if ($unread > 0): ?>
            <span style="font-size:0.85rem;font-weight:700;background:var(--signal);color:#fff;padding:0.15rem 0.55rem;border-radius:999px;margin-left:0.5rem"><?= $unread ?> unread</span>
        <?php else: ?>
            <span style="font-size:1rem;font-weight:600;color:var(--muted)">(<?= count($tips) ?>)</span>
        <?php endif; ?>
    </h1>
    <a class="admin-btn-sm" href="/contact#tip" target="_blank" rel="noopener">View tip form</a>
</div>

<?php if (!tip_form_enabled()): ?>
    <div class="admin-table-wrap">
        <p class="admin-empty">TIP_PUBLIC_KEY is not set — the tip form is currently hidden on the site, but any previously submitted ciphertext below is still readable with your offline private key.</p>
    </div>
<?php endif; ?>

<div class="admin-form" style="margin-bottom:1.5rem;padding:1.25rem 1.5rem">
    <p style="margin:0 0 0.5rem;font-weight:700">These are encrypted. This page cannot decrypt them, on purpose.</p>
    <p style="margin:0;color:var(--muted);font-size:0.9rem;line-height:1.6">
        The server and database only ever hold ciphertext — the private key needed to read a tip is not stored here or anywhere in this codebase.
        To read one: copy its ciphertext below, then decrypt it <strong>offline</strong>, on a machine that holds the private key, using
        <code>bin/decrypt-tip.php</code> from the repo. Never paste the private key into this admin panel, an env var on the server, or anywhere on Hostinger.
    </p>
</div>

<?php if (empty($tips)): ?>
    <div class="admin-table-wrap">
        <p class="admin-empty">No tips yet.</p>
    </div>
<?php else: ?>
    <div style="display:flex;flex-direction:column;gap:1rem">
        <?php foreach ($tips as $tip):
            $read = (int) $tip['is_read'];
        ?>
        <div class="admin-form" style="padding:1.35rem 1.5rem;<?= !$read ? 'border-left:3px solid var(--signal)' : 'border-left:3px solid var(--line)' ?>">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:0.75rem">
                <div>
                    <strong>Tip #<?= (int) $tip['id'] ?></strong>
                    <?php if (!$read): ?>
                        <span class="pill pill--seeking" style="margin-left:0.5rem">Unread</span>
                    <?php endif; ?>
                </div>
                <p class="meta" style="margin:0;white-space:nowrap"><?= e(format_date(substr((string) $tip['created_at'], 0, 10))) ?></p>
            </div>
            <textarea readonly rows="3" style="width:100%;font-family:monospace;font-size:0.8rem;color:var(--muted);background:var(--paper);border:1px solid var(--line);border-radius:8px;padding:0.6rem;resize:vertical" onclick="this.select()"><?= e((string) $tip['ciphertext']) ?></textarea>
            <div style="margin-top:1rem;display:flex;gap:0.5rem">
                <form method="post" style="margin:0">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= (int) $tip['id'] ?>">
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
