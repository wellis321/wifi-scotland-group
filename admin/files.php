<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

$adminTitle   = 'Files';
$adminSection = 'files';

$filesDir = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'files';
$files    = [];

if (is_dir($filesDir)) {
    foreach (scandir($filesDir) as $f) {
        if ($f === '.' || $f === '..' || str_starts_with($f, '.')) continue;
        $path = $filesDir . DIRECTORY_SEPARATOR . $f;
        if (!is_file($path)) continue;
        $files[] = [
            'filename' => $f,
            'ext'      => strtolower(pathinfo($f, PATHINFO_EXTENSION)),
            'size'     => filesize($path),
            'modified' => filemtime($path),
        ];
    }
    usort($files, fn($a, $b) => $b['modified'] - $a['modified']);
}

$extLabel = [
    'pdf'  => 'PDF',
    'docx' => 'Word',
    'doc'  => 'Word',
    'odt'  => 'ODF',
    'xlsx' => 'Excel',
    'xls'  => 'Excel',
    'csv'  => 'CSV',
    'txt'  => 'Text',
];

$extColor = [
    'pdf'  => '#c0392b',
    'docx' => '#2980b9', 'doc' => '#2980b9',
    'xlsx' => '#27ae60', 'xls' => '#27ae60',
    'csv'  => '#27ae60',
    'odt'  => '#8e44ad',
    'txt'  => '#7f8c8d',
];

require_once __DIR__ . '/includes/admin_header.php';
?>
<div class="admin-page-header">
    <h1 class="admin-page-title">Files</h1>
</div>

<div class="admin-form" style="margin-bottom:1.5rem">
    <p class="admin-section-title" style="margin:0 0 1rem">Upload a file</p>
    <form method="post" action="/admin/upload-file.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="return_to" value="/admin/files.php">
        <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap">
            <input type="file" name="file"
                   accept=".pdf,.doc,.docx,.odt,.xls,.xlsx,.csv,.txt"
                   style="font:inherit;font-size:0.92rem" required>
            <button class="btn btn-primary" type="submit">Upload</button>
            <span class="admin-hint">PDF, Word, Excel, CSV, plain text &middot; max 20 MB</span>
        </div>
    </form>
</div>

<?php if (empty($files)): ?>
    <div class="admin-table-wrap">
        <p class="admin-empty">No files uploaded yet.</p>
    </div>
<?php else: ?>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr><th>File</th><th>Type</th><th>Size</th><th>Uploaded</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($files as $f): ?>
                <tr>
                    <td>
                        <span class="file-type-badge" style="background:<?= e($extColor[$f['ext']] ?? '#95a5a6') ?>">
                            <?= e($extLabel[$f['ext']] ?? strtoupper($f['ext'])) ?>
                        </span>
                        <strong><?= e($f['filename']) ?></strong>
                    </td>
                    <td class="meta"><?= e(strtoupper($f['ext'])) ?></td>
                    <td class="meta"><?= e(number_format($f['size'] / 1024, 0)) ?> KB</td>
                    <td class="meta"><?= date('d M Y', $f['modified']) ?></td>
                    <td class="col-actions">
                        <a class="admin-link" href="/files/<?= e(rawurlencode($f['filename'])) ?>" target="_blank" rel="noopener">Download</a>
                        <button class="admin-link file-copy-btn" data-filename="<?= e($f['filename']) ?>">Copy link</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="meta" style="margin-top:0.75rem"><?= count($files) ?> file<?= count($files) !== 1 ? 's' : '' ?> in /files/</p>
<?php endif; ?>

<script>
document.querySelectorAll('.file-copy-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var name = this.dataset.filename;
        var url  = window.location.origin + '/files/' + encodeURIComponent(name);
        navigator.clipboard.writeText(url).then(function () {
            btn.textContent = 'Copied!';
            setTimeout(function () { btn.textContent = 'Copy link'; }, 1800);
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
