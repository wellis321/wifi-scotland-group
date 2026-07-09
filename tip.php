<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/tip_crypto.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /contact#tip');
    exit;
}

if (!tip_form_enabled()) {
    flash_set('tip', 'The confidential tip form is not currently available.');
    header('Location: /contact#tip');
    exit;
}

if (!csrf_validate($_POST['csrf_token'] ?? null)) {
    flash_set('tip', 'That submission could not be verified. Please try again.');
    header('Location: /contact#tip');
    exit;
}

/* Bot checks — same pattern as the main contact form. This only filters
   automated spam; it has no bearing on the confidentiality of real tips. */
$isBot = ($_POST['website'] ?? '') !== ''
      || (time() - (int) ($_SESSION['tip_loaded'] ?? 0)) < 3;

if ($isBot) {
    flash_set('tip_ok', 'Tip received.');
    header('Location: /contact#tip');
    exit;
}

$ciphertext = trim((string) ($_POST['ciphertext'] ?? ''));

/* Sanity-check shape only — the server cannot and should not know what's inside.
   30,000 base64 chars comfortably covers the 4,000-character textarea limit even
   in the worst case (every character 4 bytes of UTF-8), plus sealed-box overhead. */
if ($ciphertext === '' || strlen($ciphertext) > 30000 || !preg_match('/^[A-Za-z0-9+\/=]+$/', $ciphertext)) {
    flash_set('tip', 'That did not look like an encrypted submission. If your browser blocked scripts, encryption could not run — please try again, or use the regular contact form for non-sensitive messages.');
    header('Location: /contact#tip');
    exit;
}

if (!db_available()) {
    flash_set('tip', 'The database is not available — your tip was not stored. Please try again later.');
    header('Location: /contact#tip');
    exit;
}

try {
    $stmt = db()->prepare('INSERT INTO secure_tips (ciphertext) VALUES (:ciphertext)');
    $stmt->execute(['ciphertext' => $ciphertext]);
    flash_set('tip_ok', 'Tip received, encrypted. Only someone with the offline private key can read it.');

    $notifyTo = env_raw('NOTIFY_EMAIL') ?? '';
    if ($notifyTo !== '' && filter_var($notifyTo, FILTER_VALIDATE_EMAIL)) {
        $host = parse_url((string) app_config()['app']['base_url'], PHP_URL_HOST)
            ?: (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
        /* Deliberately no content in this notification — the server never has
           the plaintext to include, and the point is not to create a plaintext
           copy of a confidential tip in an inbox. */
        mail(
            $notifyTo,
            '[' . SITE_BRAND . '] A new confidential tip was submitted',
            "A new encrypted tip has been submitted and stored. Log in to /admin/tips.php to view and decrypt it offline.\n",
            implode("\r\n", [
                'From: noreply@' . $host,
                'Content-Type: text/plain; charset=UTF-8',
            ])
        );
    }
} catch (Throwable) {
    flash_set('tip', 'Something went wrong saving your tip. Please try again later.');
}

header('Location: /contact#tip');
exit;
