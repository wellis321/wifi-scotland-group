<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Contact';
$pageDescription = 'Reach ' . SITE_BRAND . ' with corrections, partnership ideas, or local stories about connectivity.';
$currentNav = 'contact';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        flash_set('contact', 'That submission could not be verified. Please try again.');
        header('Location: /contact.php');
        exit;
    }

    $name = trim((string) ($_POST['full_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $subject = trim((string) ($_POST['subject'] ?? ''));
    $body = trim((string) ($_POST['body'] ?? ''));

    $errors = [];
    if ($name === '' || mb_strlen($name) > 160) {
        $errors[] = 'Please enter your name.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if ($subject === '' || mb_strlen($subject) > 200) {
        $errors[] = 'Please add a short subject (up to 200 characters).';
    }
    if ($body === '' || mb_strlen($body) > 8000) {
        $errors[] = 'Please write a message (up to 8000 characters).';
    }

    if ($errors) {
        flash_set('contact', implode(' ', $errors));
        header('Location: /contact.php');
        exit;
    }

    if (!db_available()) {
        flash_set('contact', 'The database is not available—your message was not stored. Please email us once the site is configured, or try again later.');
        header('Location: /contact.php');
        exit;
    }

    try {
        $stmt = db()->prepare(
            'INSERT INTO contact_messages (full_name, email, subject, body) VALUES (:full_name, :email, :subject, :body)'
        );
        $stmt->execute([
            'full_name' => $name,
            'email' => $email,
            'subject' => $subject,
            'body' => $body,
        ]);
        flash_set('contact_ok', 'Message received. A volunteer will read it as soon as they can.');

        $notifyTo = env_raw('NOTIFY_EMAIL') ?? '';
        if ($notifyTo !== '' && filter_var($notifyTo, FILTER_VALIDATE_EMAIL)) {
            $host = parse_url((string) app_config()['app']['base_url'], PHP_URL_HOST)
                ?: (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
            $safeName = str_replace(["\r", "\n"], '', $name);
            mail(
                $notifyTo,
                '[' . SITE_BRAND . '] New contact: ' . mb_substr($subject, 0, 100),
                implode("\n", [
                    'New message from ' . $safeName . ' <' . $email . '>',
                    str_repeat('-', 50),
                    'Subject: ' . $subject,
                    '',
                    $body,
                ]),
                implode("\r\n", [
                    'From: noreply@' . $host,
                    'Reply-To: ' . $email,
                    'Content-Type: text/plain; charset=UTF-8',
                ])
            );
        }
    } catch (Throwable) {
        flash_set('contact', 'Something went wrong saving your message. Please try again later.');
    }

    header('Location: /contact.php');
    exit;
}

$flashErr = flash_take('contact');
$flashOk = flash_take('contact_ok');

$pageOgImage = image_asset('card-community.jpg');
$pageOgImageAlt = 'People collaborating with a laptop—representing community outreach.';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Contact</h1>
        <p>Use this form for corrections, partnership ideas, press enquiries, or local stories. This is a volunteer inbox—please allow several days at busy times.</p>
    </div>
</header>

<div class="section">
    <div class="wrap join-grid">
        <div>
        <?php if ($flashOk): ?>
            <p class="flash ok" role="status"><?= e($flashOk) ?></p>
        <?php endif; ?>
        <?php if ($flashErr): ?>
            <p class="flash err" role="alert"><?= e($flashErr) ?></p>
        <?php endif; ?>

        <form class="forms" method="post" action="/contact.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <div class="form-row">
                <label for="full_name">Full name</label>
                <input id="full_name" name="full_name" required maxlength="160" autocomplete="name">
            </div>

            <div class="form-row">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" required maxlength="255" autocomplete="email">
            </div>

            <div class="form-row">
                <label for="subject">Subject</label>
                <input id="subject" name="subject" required maxlength="200">
            </div>

            <div class="form-row">
                <label for="body">Message</label>
                <textarea id="body" name="body" required maxlength="8000"></textarea>
            </div>

            <button class="btn btn-primary" type="submit">Send</button>
        </form>
        </div>
        <figure class="join-aside">
            <img src="<?= e(image_asset('card-community.jpg')) ?>" width="1200" height="800" alt="" decoding="async" loading="lazy">
            <figcaption>For sensitive whistle-blowing, consider an encrypted channel once we publish one; this form stores messages in our volunteer database.</figcaption>
        </figure>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
