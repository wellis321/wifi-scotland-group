<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/files.php');
    exit;
}

if (!csrf_validate($_POST['csrf_token'] ?? null)) {
    flash_set('admin_err', 'Invalid form token. Please try again.');
    header('Location: /admin/files.php');
    exit;
}

$rawReturn = trim((string) ($_POST['return_to'] ?? ''));
$returnTo  = preg_match('#^/admin/[a-zA-Z0-9\-_./]*(\?[a-zA-Z0-9=&%\-_.]*)?$#', $rawReturn)
    ? $rawReturn
    : '/admin/files.php';

$uploadErr = $_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE;

if ($uploadErr !== UPLOAD_ERR_OK) {
    $msg = match ($uploadErr) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File too large (server limit).',
        UPLOAD_ERR_NO_FILE                         => 'No file was selected.',
        UPLOAD_ERR_PARTIAL                         => 'Upload was interrupted — please try again.',
        default                                    => 'Upload failed (error ' . $uploadErr . ').',
    };
    flash_set('admin_err', $msg);
    header('Location: ' . $returnTo);
    exit;
}

const MAX_FILE_BYTES = 20 * 1024 * 1024; // 20 MB

if ($_FILES['file']['size'] > MAX_FILE_BYTES) {
    flash_set('admin_err', 'File too large. Maximum is 20 MB.');
    header('Location: ' . $returnTo);
    exit;
}

/* ── MIME-type + extension whitelist ── */
$allowed = [
    'application/pdf'                                                          => 'pdf',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
    'application/msword'                                                       => 'doc',
    'application/vnd.oasis.opendocument.text'                                 => 'odt',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'       => 'xlsx',
    'application/vnd.ms-excel'                                                 => 'xls',
    'text/plain'                                                               => 'txt',
    'text/csv'                                                                 => 'csv',
    'application/csv'                                                          => 'csv',
];

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($_FILES['file']['tmp_name']);

/* DOCX/XLSX are ZIP archives internally — finfo may return application/zip */
if ($mime === 'application/zip') {
    $origExt = strtolower(pathinfo((string) $_FILES['file']['name'], PATHINFO_EXTENSION));
    $mime = match ($origExt) {
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        default => $mime,
    };
}

if (!array_key_exists($mime, $allowed)) {
    flash_set('admin_err', 'File type not allowed. Accepted: PDF, Word (docx/doc), Excel (xlsx), ODF, CSV, plain text.');
    header('Location: ' . $returnTo);
    exit;
}

$ext = $allowed[$mime];

/* ── Sanitise filename ── */
$baseName = pathinfo((string) $_FILES['file']['name'], PATHINFO_FILENAME);
$safeName = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $baseName), '-'));
if ($safeName === '') $safeName = 'document';

$filename = $safeName . '.' . $ext;
$filesDir = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'files';

if (!is_dir($filesDir)) {
    mkdir($filesDir, 0755, true);
}

$dest = $filesDir . DIRECTORY_SEPARATOR . $filename;

if (file_exists($dest)) {
    $filename = $safeName . '-' . date('YmdHis') . '.' . $ext;
    $dest     = $filesDir . DIRECTORY_SEPARATOR . $filename;
}

if (!move_uploaded_file($_FILES['file']['tmp_name'], $dest)) {
    flash_set('admin_err', 'Could not save the file. Check that the files/ directory is writable.');
    header('Location: ' . $returnTo);
    exit;
}

flash_set('admin_ok', 'File uploaded: ' . $filename);
header('Location: ' . $returnTo);
exit;
