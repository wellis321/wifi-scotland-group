<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminSection = 'news';

$item   = null;
$isNew  = true;
$slug   = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';

if ($slug !== '' && db_available()) {
    // Try with all columns first; fall back to core columns if newer ones don't exist yet
    try {
        $s = db()->prepare(
            'SELECT id, title, slug, summary, body, published_at, group_id, image_filename FROM news_items WHERE slug = :slug LIMIT 1'
        );
        $s->execute(['slug' => $slug]);
        $item = $s->fetch() ?: null;
    } catch (Throwable) {
        try {
            $s = db()->prepare(
                'SELECT id, title, slug, summary, body, published_at FROM news_items WHERE slug = :slug LIMIT 1'
            );
            $s->execute(['slug' => $slug]);
            $item = $s->fetch() ?: null;
            flash_set('admin_err', 'Some columns are missing from the database. Run the ALTER TABLE migrations in phpMyAdmin — see schema.sql for the commands.');
        } catch (Throwable) {
            $item = null;
        }
    }
    if ($item) $isNew = false;
}

$adminTitle = $isNew ? 'New article' : 'Edit article';

$availableImages = [];
$imgDir = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'images';
if (is_dir($imgDir)) {
    foreach (scandir($imgDir) as $f) {
        if (preg_match('/\.(jpe?g|png|webp|gif)$/i', $f)) {
            $availableImages[] = $f;
        }
    }
}

$groups = db_available()
    ? db()->query('SELECT id, council_area FROM local_groups ORDER BY council_area')->fetchAll()
    : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        flash_set('admin_err', 'Invalid form token. Please try again.');
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    /* Handle delete */
    if (isset($_POST['_delete']) && !$isNew && $item) {
        db()->prepare('DELETE FROM news_items WHERE id = :id')->execute(['id' => $item['id']]);
        flash_set('admin_ok', 'Article deleted.');
        header('Location: /admin/news.php');
        exit;
    }

    $title      = trim((string) ($_POST['title'] ?? ''));
    $newSlug    = trim((string) ($_POST['slug'] ?? ''));
    $summary    = trim((string) ($_POST['summary'] ?? ''));
    $body       = trim((string) ($_POST['body'] ?? ''));
    $pubDate    = trim((string) ($_POST['published_at'] ?? date('Y-m-d')));
    $groupId    = ($_POST['group_id'] ?? '') !== '' ? (int) $_POST['group_id'] : null;
    $imageFile  = trim((string) ($_POST['image_filename'] ?? '')) ?: null;

    $errors = [];
    if ($title === '')   $errors[] = 'Title is required.';
    if ($newSlug === '') $errors[] = 'Slug is required.';
    if ($body === '')    $errors[] = 'Body is required.';
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $pubDate)) $errors[] = 'Invalid date.';

    if (empty($errors)) {
        if ($isNew) {
            db()->prepare(
                'INSERT INTO news_items (title, slug, summary, body, published_at, group_id, image_filename) VALUES (:title,:slug,:summary,:body,:pub,:gid,:img)'
            )->execute(['title'=>$title,'slug'=>$newSlug,'summary'=>$summary,'body'=>$body,'pub'=>$pubDate,'gid'=>$groupId,'img'=>$imageFile]);
            flash_set('admin_ok', 'Article published.');
        } else {
            db()->prepare(
                'UPDATE news_items SET title=:title,slug=:slug,summary=:summary,body=:body,published_at=:pub,group_id=:gid,image_filename=:img WHERE id=:id'
            )->execute(['title'=>$title,'slug'=>$newSlug,'summary'=>$summary,'body'=>$body,'pub'=>$pubDate,'gid'=>$groupId,'img'=>$imageFile,'id'=>$item['id']]);
            flash_set('admin_ok', 'Article saved.');
        }
        header('Location: /admin/news-edit.php?slug=' . rawurlencode($newSlug));
        exit;
    }

    flash_set('admin_err', implode(' ', $errors));
    $item = array_merge($item ?? [], compact('title','summary','body','pubDate','groupId','imageFile') + ['slug'=>$newSlug,'published_at'=>$pubDate,'group_id'=>$groupId,'image_filename'=>$imageFile]);
}

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title"><?= e($adminTitle) ?></h1>
    <?php if (!$isNew): ?>
        <a class="admin-btn-sm" href="/news-item.php?slug=<?= e(rawurlencode($slug)) ?>" target="_blank" rel="noopener">View live</a>
    <?php endif; ?>
</div>

<form class="admin-form" method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div class="admin-field">
        <label for="title">Title</label>
        <input id="title" name="title" type="text" required value="<?= e((string) ($item['title'] ?? '')) ?>">
    </div>

    <div class="admin-field">
        <label for="slug">Slug <span style="font-weight:400;text-transform:none">(URL)</span></label>
        <input id="slug" name="slug" type="text" required value="<?= e((string) ($item['slug'] ?? '')) ?>">
        <p class="admin-hint">Auto-generated from title. Changing the slug on a published article will break existing links.</p>
    </div>

    <div class="admin-field">
        <label for="summary">Summary <span style="font-weight:400;text-transform:none">(shown in listings — optional)</span></label>
        <textarea id="summary" name="summary" maxlength="500"><?= e((string) ($item['summary'] ?? '')) ?></textarea>
    </div>

    <div class="admin-field">
        <label for="body">Body</label>
        <textarea id="body" name="body" class="tall" required><?= e((string) ($item['body'] ?? '')) ?></textarea>
        <p class="admin-hint">HTML supported: &lt;p&gt; &lt;strong&gt; &lt;em&gt; &lt;a href="..."&gt; &lt;ul&gt; &lt;li&gt; &lt;h2&gt; — Use the image panel below to insert inline images.</p>
    </div>

    <?php if (!empty($availableImages)): ?>
    <div class="inserter-panel">
        <p class="inserter-label">Insert image into body — click position to insert at cursor</p>
        <div class="inserter-grid">
            <?php foreach ($availableImages as $img): ?>
                <div class="inserter-item">
                    <img class="inserter-thumb" src="/images/<?= e($img) ?>" alt="">
                    <p class="inserter-name"><?= e($img) ?></p>
                    <div class="inserter-btns">
                        <button type="button" class="inserter-btn" data-img="<?= e($img) ?>" data-pos="left">← Float left</button>
                        <button type="button" class="inserter-btn" data-img="<?= e($img) ?>" data-pos="right">Float right →</button>
                        <button type="button" class="inserter-btn" data-img="<?= e($img) ?>" data-pos="full">Full width</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
    (function () {
        function insertAtCursor(el, text) {
            var start = el.selectionStart, end = el.selectionEnd;
            el.value = el.value.slice(0, start) + text + el.value.slice(end);
            el.selectionStart = el.selectionEnd = start + text.length;
            el.focus();
        }
        document.querySelectorAll('.inserter-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var img = this.dataset.img;
                var pos = this.dataset.pos;
                var html = '\n<figure class="article-img article-img--' + pos + '">\n'
                         + '  <img src="/images/' + img + '" alt="">\n'
                         + '  <figcaption>Caption text — edit or delete this line</figcaption>\n'
                         + '</figure>\n';
                insertAtCursor(document.getElementById('body'), html);
            });
        });
    })();
    </script>
    <?php endif; ?>


    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem">
        <div class="admin-field">
            <label for="published_at">Published date</label>
            <input id="published_at" name="published_at" type="date" required value="<?= e((string) ($item['published_at'] ?? date('Y-m-d'))) ?>">
        </div>
        <div class="admin-field">
            <label for="image_filename">Banner image</label>
            <select id="image_filename" name="image_filename">
                <option value="">— Default (card-global-network.jpg) —</option>
                <?php foreach ($availableImages as $img): ?>
                    <option value="<?= e($img) ?>" <?= ($item['image_filename'] ?? '') === $img ? 'selected' : '' ?>><?= e($img) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="admin-field">
            <label for="group_id">Local group <span style="font-weight:400;text-transform:none">(optional)</span></label>
            <select id="group_id" name="group_id">
                <option value="">— None —</option>
                <?php foreach ($groups as $g): ?>
                    <option value="<?= e((string)$g['id']) ?>" <?= ((int)($item['group_id'] ?? 0)) === (int)$g['id'] ? 'selected' : '' ?>><?= e($g['council_area']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="admin-form-actions">
        <button class="btn btn-primary" type="submit"><?= $isNew ? 'Publish' : 'Save changes' ?></button>
        <a class="btn btn-ghost" href="/admin/news.php">Cancel</a>
        <?php if (!$isNew): ?>
            <div class="admin-delete-zone">
                <button class="btn" style="background:rgba(226,85,64,0.1);color:#7a2f24" type="submit" name="_delete" value="1"
                    data-confirm="Delete this article? This cannot be undone.">Delete article</button>
            </div>
        <?php endif; ?>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
