<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'Media';
$adminSection = 'media';

$imgDir = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'images';
$images = [];
if (is_dir($imgDir)) {
    foreach (scandir($imgDir) as $f) {
        if (preg_match('/\.(jpe?g|png|webp|gif)$/i', $f)) {
            $images[] = [
                'filename' => $f,
                'size'     => filesize($imgDir . DIRECTORY_SEPARATOR . $f),
                'modified' => filemtime($imgDir . DIRECTORY_SEPARATOR . $f),
            ];
        }
    }
    usort($images, fn($a, $b) => $b['modified'] - $a['modified']);
}

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">Media library</h1>
</div>

<div class="admin-form" style="margin-bottom:1.5rem">
    <p class="admin-section-title" style="margin:0 0 1rem">Upload an image</p>
    <form method="post" action="/admin/upload.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="return_to" value="/admin/media.php">
        <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap">
            <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
                   style="font:inherit;font-size:0.92rem" required>
            <button class="btn btn-primary" type="submit">Upload</button>
            <span class="admin-hint">JPEG, PNG, WebP or GIF &middot; max 5 MB</span>
        </div>
    </form>
</div>

<?php if (empty($images)): ?>
    <div class="admin-table-wrap">
        <p class="admin-empty">No images yet. Upload one above.</p>
    </div>
<?php else: ?>
    <div class="media-grid">
        <?php foreach ($images as $img): ?>
            <div class="media-card">
                <div class="media-thumb">
                    <img src="/images/<?= e($img['filename']) ?>" alt="<?= e($img['filename']) ?>">
                </div>
                <div class="media-info">
                    <p class="media-filename" title="<?= e($img['filename']) ?>"><?= e($img['filename']) ?></p>
                    <p class="media-meta"><?= e(number_format($img['size'] / 1024, 0)) ?> KB &middot; <?= date('d M Y', $img['modified']) ?></p>
                </div>
                <div class="media-actions">
                    <a class="admin-link" href="/images/<?= e(rawurlencode($img['filename'])) ?>" target="_blank" rel="noopener">View</a>
                    <button class="admin-link admin-link--danger media-copy-btn"
                            data-filename="<?= e($img['filename']) ?>"
                            title="Copy filename to clipboard">Copy name</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="meta" style="margin-top:1rem">
        <?= count($images) ?> image<?= count($images) !== 1 ? 's' : '' ?> in /images/
    </p>
<?php endif; ?>

<script>
document.querySelectorAll('.media-copy-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var name = this.dataset.filename;
        navigator.clipboard.writeText(name).then(function () {
            btn.textContent = 'Copied!';
            setTimeout(function () { btn.textContent = 'Copy name'; }, 1800);
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
