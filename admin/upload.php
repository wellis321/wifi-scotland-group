<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/media.php');
    exit;
}

if (!csrf_validate($_POST['csrf_token'] ?? null)) {
    flash_set('admin_err', 'Invalid form token. Please try again.');
    header('Location: /admin/media.php');
    exit;
}

/* Only redirect back to admin pages */
$rawReturn  = trim((string) ($_POST['return_to'] ?? ''));
$returnTo   = preg_match('#^/admin/[a-zA-Z0-9\-_./]*(\?[a-zA-Z0-9=&%\-_.]*)?$#', $rawReturn)
    ? $rawReturn
    : '/admin/media.php';

/* ── Check a file was actually sent ── */
$uploadErr = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;

if ($uploadErr !== UPLOAD_ERR_OK) {
    $msg = match ($uploadErr) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'The file is too large. Maximum allowed is 5 MB.',
        UPLOAD_ERR_NO_FILE                         => 'No file was selected.',
        UPLOAD_ERR_PARTIAL                         => 'The upload was interrupted — please try again.',
        default                                    => 'Upload failed (error ' . $uploadErr . ').',
    };
    flash_set('admin_err', $msg);
    header('Location: ' . $returnTo);
    exit;
}

/* ── Size check ── */
const MAX_UPLOAD_BYTES = 5 * 1024 * 1024;

if ($_FILES['image']['size'] > MAX_UPLOAD_BYTES) {
    flash_set('admin_err', 'File too large. Maximum size is 5 MB.');
    header('Location: ' . $returnTo);
    exit;
}

/* ── MIME-type check (reads the actual file, not the browser claim) ── */
$allowedMimes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
];

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($_FILES['image']['tmp_name']);

if (!array_key_exists($mime, $allowedMimes)) {
    flash_set('admin_err', 'Only JPEG, PNG, WebP, and GIF images are allowed.');
    header('Location: ' . $returnTo);
    exit;
}

$ext = $allowedMimes[$mime];

/* ── Sanitise the original filename ── */
$baseName = pathinfo((string) $_FILES['image']['name'], PATHINFO_FILENAME);
$safeName = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $baseName), '-'));
if ($safeName === '') {
    $safeName = 'image';
}

$filename = $safeName . '.' . $ext;
$imgDir   = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'images';
$dest     = $imgDir . DIRECTORY_SEPARATOR . $filename;

/* ── Don't overwrite existing files ── */
if (file_exists($dest)) {
    $filename = $safeName . '-' . date('YmdHis') . '.' . $ext;
    $dest     = $imgDir . DIRECTORY_SEPARATOR . $filename;
}

if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
    flash_set('admin_err', 'Could not save the file. Check that the images/ directory is writable.');
    header('Location: ' . $returnTo);
    exit;
}

flash_set('admin_ok', 'Image uploaded: ' . $filename);
header('Location: ' . $returnTo);
exit;
