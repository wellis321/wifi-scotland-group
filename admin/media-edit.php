<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminSection = 'media';

/* ── Helpers ── */

function img_load(string $path): GdImage|false
{
    [,,$type] = getimagesize($path);
    return match ($type) {
        IMAGETYPE_JPEG => imagecreatefromjpeg($path),
        IMAGETYPE_PNG  => imagecreatefrompng($path),
        IMAGETYPE_WEBP => imagecreatefromwebp($path),
        IMAGETYPE_GIF  => imagecreatefromgif($path),
        default        => false,
    };
}

function img_save(GdImage $img, string $path, int $type): bool
{
    return match ($type) {
        IMAGETYPE_JPEG => imagejpeg($img, $path, 85),
        IMAGETYPE_PNG  => imagepng($img, $path, 6),
        IMAGETYPE_WEBP => imagewebp($img, $path, 85),
        IMAGETYPE_GIF  => imagegif($img, $path),
        default        => false,
    };
}

function regen_webp(string $imgPath): void
{
    $webpPath = preg_replace('/\.(jpe?g|png|gif)$/i', '.webp', $imgPath);
    if ($webpPath === $imgPath) return; // already webp
    $img = img_load($imgPath);
    if ($img) {
        imagewebp($img, $webpPath, 85);
        imagedestroy($img);
    }
}

function load_meta(): array
{
    $path = PROJECT_ROOT . '/data/image-metadata.json';
    $raw  = is_readable($path) ? file_get_contents($path) : '{}';
    return json_decode((string) $raw, true) ?: [];
}

function save_meta(array $meta): void
{
    file_put_contents(
        PROJECT_ROOT . '/data/image-metadata.json',
        json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    );
}

/* ── Validate requested file ── */

$filename = trim((string) ($_GET['file'] ?? ''));
if ($filename === '' || preg_match('/[^A-Za-z0-9._-]/', $filename)) {
    flash_set('admin_err', 'Invalid filename.');
    header('Location: /admin/media.php');
    exit;
}

$imgDir  = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'images';
$imgPath = $imgDir . DIRECTORY_SEPARATOR . $filename;

if (!is_file($imgPath)) {
    flash_set('admin_err', 'File not found.');
    header('Location: /admin/media.php');
    exit;
}

[$origW, $origH, $imgType] = getimagesize($imgPath);

/* ── Handle POST actions ── */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        flash_set('admin_err', 'Invalid token.');
        header('Location: /admin/media-edit.php?file=' . rawurlencode($filename));
        exit;
    }

    $action = $_POST['action'] ?? '';

    /* Save alt text */
    if ($action === 'meta') {
        $meta = load_meta();
        $meta[$filename] = [
            'alt'         => trim((string) ($_POST['alt'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
        ];
        save_meta($meta);
        flash_set('admin_ok', 'Alt text saved.');
        header('Location: /admin/media-edit.php?file=' . rawurlencode($filename));
        exit;
    }

    /* Resize */
    if ($action === 'resize') {
        $maxW = max(1, (int) ($_POST['max_width']  ?? 1200));
        $maxH = max(1, (int) ($_POST['max_height'] ?? 1200));

        if ($maxW >= $origW && $maxH >= $origH) {
            flash_set('admin_err', 'New size must be smaller than the current size (' . $origW . '×' . $origH . 'px).');
        } else {
            $ratio = min($maxW / $origW, $maxH / $origH);
            $newW  = (int) round($origW * $ratio);
            $newH  = (int) round($origH * $ratio);

            $src = img_load($imgPath);
            if ($src) {
                $dst = imagecreatetruecolor($newW, $newH);
                if ($imgType === IMAGETYPE_PNG) {
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                }
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
                img_save($dst, $imgPath, $imgType);
                imagedestroy($src);
                imagedestroy($dst);
                regen_webp($imgPath);
                flash_set('admin_ok', 'Resized to ' . $newW . '×' . $newH . 'px.');
            } else {
                flash_set('admin_err', 'Could not open image for resizing.');
            }
        }
        header('Location: /admin/media-edit.php?file=' . rawurlencode($filename));
        exit;
    }

    /* Crop */
    if ($action === 'crop') {
        $cx = (int) ($_POST['crop_x'] ?? 0);
        $cy = (int) ($_POST['crop_y'] ?? 0);
        $cw = max(1, (int) ($_POST['crop_w'] ?? $origW));
        $ch = max(1, (int) ($_POST['crop_h'] ?? $origH));

        $cx = max(0, min($cx, $origW - 1));
        $cy = max(0, min($cy, $origH - 1));
        $cw = min($cw, $origW - $cx);
        $ch = min($ch, $origH - $cy);

        $src = img_load($imgPath);
        if ($src) {
            $dst = imagecrop($src, ['x' => $cx, 'y' => $cy, 'width' => $cw, 'height' => $ch]);
            if ($dst) {
                img_save($dst, $imgPath, $imgType);
                imagedestroy($dst);
                regen_webp($imgPath);
                flash_set('admin_ok', 'Cropped to ' . $cw . '×' . $ch . 'px.');
            } else {
                flash_set('admin_err', 'Crop failed — check the values and try again.');
            }
            imagedestroy($src);
        } else {
            flash_set('admin_err', 'Could not open image for cropping.');
        }
        header('Location: /admin/media-edit.php?file=' . rawurlencode($filename));
        exit;
    }

    /* Rename */
    if ($action === 'rename') {
        $newName = trim((string) ($_POST['new_filename'] ?? ''));
        $newExt  = pathinfo($filename, PATHINFO_EXTENSION);
        $newBase = pathinfo($newName, PATHINFO_FILENAME);

        $safeName = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $newBase));
        $safeName = trim($safeName, '-');
        $newFilename = $safeName . '.' . $newExt;
        $newPath     = $imgDir . DIRECTORY_SEPARATOR . $newFilename;

        if ($safeName === '') {
            flash_set('admin_err', 'Invalid filename.');
        } elseif ($newFilename !== $filename && file_exists($newPath)) {
            flash_set('admin_err', 'A file with that name already exists.');
        } else {
            rename($imgPath, $newPath);
            /* Rename WebP too */
            $oldWebp = preg_replace('/\.(jpe?g|png|gif)$/i', '.webp', $imgPath);
            $newWebp = preg_replace('/\.(jpe?g|png|gif)$/i', '.webp', $newPath);
            if (is_file((string) $oldWebp)) rename((string) $oldWebp, (string) $newWebp);
            /* Move metadata */
            $meta = load_meta();
            if (isset($meta[$filename])) {
                $meta[$newFilename] = $meta[$filename];
                unset($meta[$filename]);
                save_meta($meta);
            }
            flash_set('admin_ok', 'Renamed to ' . $newFilename);
            header('Location: /admin/media-edit.php?file=' . rawurlencode($newFilename));
            exit;
        }
        header('Location: /admin/media-edit.php?file=' . rawurlencode($filename));
        exit;
    }

    /* Delete */
    if ($action === 'delete') {
        $webpPath = preg_replace('/\.(jpe?g|png|gif)$/i', '.webp', $imgPath);
        unlink($imgPath);
        if (is_file((string) $webpPath)) unlink((string) $webpPath);
        $meta = load_meta();
        unset($meta[$filename]);
        save_meta($meta);
        flash_set('admin_ok', $filename . ' deleted.');
        header('Location: /admin/media.php');
        exit;
    }
}

/* ── Load metadata and render ── */

$meta     = load_meta();
$imgMeta  = $meta[$filename] ?? ['alt' => '', 'description' => ''];
$fileSize = round(filesize($imgPath) / 1024);
[$curW, $curH] = getimagesize($imgPath);

$adminTitle = 'Edit image: ' . $filename;

$pageExtraHead = '
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" crossorigin="anonymous">
';

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title"><?= e($filename) ?></h1>
    <a class="admin-btn-sm" href="/admin/media.php">&larr; Media library</a>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">

    <!-- Left: image preview + crop -->
    <div>
        <div style="background:#f0f1f3;border:1px solid var(--line);border-radius:var(--radius);overflow:hidden;margin-bottom:1rem">
            <img id="crop-target" src="/images/<?= e(rawurlencode($filename)) ?>?t=<?= time() ?>"
                 style="max-width:100%;display:block" alt="<?= e($imgMeta['alt']) ?>">
        </div>
        <p class="meta"><?= e($filename) ?> &middot; <?= $curW ?>×<?= $curH ?>px &middot; <?= $fileSize ?> KB</p>

        <!-- Crop controls -->
        <div class="admin-form" style="margin-top:1rem">
            <p class="admin-section-title" style="margin:0 0 0.75rem">Crop image</p>
            <p style="font-size:0.85rem;color:var(--muted);margin:0 0 1rem">Drag the box on the image to select the area to keep, then click Apply crop.</p>
            <form method="post" id="crop-form">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="crop">
                <input type="hidden" name="crop_x" id="crop_x" value="0">
                <input type="hidden" name="crop_y" id="crop_y" value="0">
                <input type="hidden" name="crop_w" id="crop_w" value="<?= $curW ?>">
                <input type="hidden" name="crop_h" id="crop_h" value="<?= $curH ?>">
                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:center">
                    <span style="font-size:0.85rem;color:var(--muted)" id="crop-info">Select area on image above</span>
                    <button class="btn btn-primary btn-sm" type="submit" id="crop-submit" disabled>Apply crop</button>
                    <button class="btn btn-ghost btn-sm" type="button" id="crop-reset">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right: settings panel -->
    <div style="display:flex;flex-direction:column;gap:1rem">

        <!-- Alt text -->
        <div class="admin-form">
            <p class="admin-section-title" style="margin:0 0 0.75rem">Alt text &amp; description</p>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="meta">
                <div class="admin-field">
                    <label for="alt">Alt text</label>
                    <input id="alt" name="alt" type="text" value="<?= e($imgMeta['alt']) ?>" placeholder="Describe the image for screen readers">
                </div>
                <div class="admin-field">
                    <label for="description">Caption / description <span style="font-weight:400;text-transform:none">(optional)</span></label>
                    <textarea id="description" name="description" rows="2"><?= e($imgMeta['description']) ?></textarea>
                </div>
                <button class="btn btn-primary btn-sm" type="submit">Save</button>
            </form>
        </div>

        <!-- Resize -->
        <div class="admin-form">
            <p class="admin-section-title" style="margin:0 0 0.25rem">Resize</p>
            <p style="font-size:0.82rem;color:var(--muted);margin:0 0 0.75rem">Current: <?= $curW ?>×<?= $curH ?>px. Aspect ratio is preserved.</p>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="resize">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;margin-bottom:0.75rem">
                    <div class="admin-field" style="margin:0">
                        <label for="max_width">Max width px</label>
                        <input id="max_width" name="max_width" type="number" min="10" max="<?= $curW ?>" value="<?= min(800, $curW) ?>">
                    </div>
                    <div class="admin-field" style="margin:0">
                        <label for="max_height">Max height px</label>
                        <input id="max_height" name="max_height" type="number" min="10" max="<?= $curH ?>" value="<?= min(600, $curH) ?>">
                    </div>
                </div>
                <button class="btn btn-primary btn-sm" type="submit">Resize image</button>
            </form>
        </div>

        <!-- Rename -->
        <div class="admin-form">
            <p class="admin-section-title" style="margin:0 0 0.75rem">Rename</p>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="rename">
                <div class="admin-field">
                    <label for="new_filename">New name <span style="font-weight:400;text-transform:none">(.<?= e(pathinfo($filename, PATHINFO_EXTENSION)) ?> kept)</span></label>
                    <input id="new_filename" name="new_filename" type="text"
                           value="<?= e(pathinfo($filename, PATHINFO_FILENAME)) ?>"
                           placeholder="new-name-here">
                </div>
                <button class="btn btn-primary btn-sm" type="submit">Rename</button>
            </form>
        </div>

        <!-- Delete -->
        <div class="admin-form" style="border-color:rgba(226,85,64,0.3)">
            <p class="admin-section-title" style="margin:0 0 0.5rem;color:#7a2f24">Delete image</p>
            <p style="font-size:0.82rem;color:var(--muted);margin:0 0 0.75rem">This also deletes the WebP version. Any pages using this image will show a broken image.</p>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete">
                <button class="btn btn-sm" type="submit"
                    style="background:rgba(226,85,64,0.1);color:#7a2f24"
                    data-confirm="Delete <?= e($filename) ?> permanently? This cannot be undone.">
                    Delete image
                </button>
            </form>
        </div>

    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js" crossorigin="anonymous"></script>
<script>
(function () {
    var img     = document.getElementById('crop-target');
    var xInput  = document.getElementById('crop_x');
    var yInput  = document.getElementById('crop_y');
    var wInput  = document.getElementById('crop_w');
    var hInput  = document.getElementById('crop_h');
    var info    = document.getElementById('crop-info');
    var submit  = document.getElementById('crop-submit');
    var reset   = document.getElementById('crop-reset');

    var cropper = new Cropper(img, {
        viewMode: 1,
        autoCropArea: 0,
        movable: false,
        zoomable: false,
        rotatable: false,
        scalable: false,
        ready: function () {
            cropper.clear(); /* start with no selection */
        },
        crop: function (e) {
            var d = e.detail;
            xInput.value = Math.round(d.x);
            yInput.value = Math.round(d.y);
            wInput.value = Math.round(d.width);
            hInput.value = Math.round(d.height);
            info.textContent = Math.round(d.width) + '×' + Math.round(d.height) + 'px at (' + Math.round(d.x) + ', ' + Math.round(d.y) + ')';
            submit.disabled = d.width < 1 || d.height < 1;
        }
    });

    reset.addEventListener('click', function () {
        cropper.clear();
        submit.disabled = true;
        info.textContent = 'Select area on image above';
    });
})();
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
