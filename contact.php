<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/tip_crypto.php';

$pageTitle = 'Contact';
$pageDescription = 'Reach ' . SITE_BRAND . ' with corrections, partnership ideas, or local stories about connectivity.';
$currentNav = 'contact';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['contact_loaded'] = time();
    $_SESSION['tip_loaded'] = time();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        flash_set('contact', 'That submission could not be verified. Please try again.');
        header('Location: /contact');
        exit;
    }

    /* Bot checks */
    $isBot = ($_POST['website'] ?? '') !== ''
          || (time() - (int) ($_SESSION['contact_loaded'] ?? 0)) < 3;

    if ($isBot) {
        flash_set('contact_ok', 'Message received. A volunteer will read it as soon as they can.');
        header('Location: /contact');
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
    } else {
        $domain = substr($email, strrpos($email, '@') + 1);
        if (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
            $errors[] = 'That email address doesn\'t look valid — the domain doesn\'t appear to exist.';
        }
    }
    if ($subject === '' || mb_strlen($subject) > 200) {
        $errors[] = 'Please add a short subject (up to 200 characters).';
    }
    if ($body === '' || mb_strlen($body) > 8000) {
        $errors[] = 'Please write a message (up to 8000 characters).';
    }

    if ($errors) {
        flash_set('contact', implode(' ', $errors));
        header('Location: /contact');
        exit;
    }

    if (!db_available()) {
        flash_set('contact', 'The database is not available—your message was not stored. Please email us once the site is configured, or try again later.');
        header('Location: /contact');
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

    header('Location: /contact');
    exit;
}

$flashErr = flash_take('contact');
$flashOk = flash_take('contact_ok');
$tipFlashErr = flash_take('tip');
$tipFlashOk = flash_take('tip_ok');
$tipOpenOnLoad = $tipFlashErr !== null || $tipFlashOk !== null;

$pageOgImage = image_asset('card-community.jpg');
$pageOgImageAlt = 'People collaborating with a laptop—representing community outreach.';

$pageExtraScripts = '<script src="/js/tip-form.js" defer></script>';

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

        <form class="forms" method="post" action="/contact" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="form-hp" aria-hidden="true">
                <label for="website_c">Website</label>
                <input id="website_c" name="website" type="text" tabindex="-1" autocomplete="off">
            </div>

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
            <figcaption>This form stores messages in our volunteer database — fine for corrections, press enquiries, and most stories. <?= tip_form_enabled() ? 'For anything sensitive, use the <a href="#tip">confidential tip form</a> below instead.' : '' ?></figcaption>
        </figure>
    </div>
</div>

<?php if (tip_form_enabled()): ?>
<div class="section alt" id="tip">
    <div class="wrap wrap--content">
        <div class="tip-section" data-tip-form data-tip-pubkey="<?= e(tip_public_key_hex()) ?>">
            <button type="button" class="tip-toggle" data-tip-toggle aria-expanded="<?= $tipOpenOnLoad ? 'true' : 'false' ?>" aria-controls="tip-panel">
                <span class="tip-toggle__icon" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
                <span>
                    <span class="tip-toggle__title">Send a confidential tip instead</span>
                    <span class="tip-toggle__sub">For sensitive information — encrypted in your browser before it's sent</span>
                </span>
            </button>
            <div class="tip-panel" data-tip-panel id="tip-panel" <?= $tipOpenOnLoad ? '' : 'hidden' ?>>
                <p>Your message is encrypted on your device before it leaves your browser. Nobody can read it without the offline private key — not us, not our host, not anyone who might gain access to the database. There's no name or email field; if you want a reply, include a way to reach you inside the message itself.</p>
                <p class="tip-panel__caveat"><strong>This encrypts what you say, not who you are.</strong> Ordinary web metadata — your IP address, timestamp — is still visible to our hosting provider, the same as on any website. If hiding your identity matters as much as your message, use a dedicated anonymous tip service rather than this form.</p>

                <?php if ($tipFlashOk): ?>
                    <p class="flash ok" role="status"><?= e($tipFlashOk) ?></p>
                <?php endif; ?>
                <?php if ($tipFlashErr): ?>
                    <p class="flash err" role="alert"><?= e($tipFlashErr) ?></p>
                <?php endif; ?>

                <form class="forms" method="post" action="/tip" data-tip-form-el novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="ciphertext" data-tip-ciphertext value="">
                    <div class="form-hp" aria-hidden="true">
                        <label for="website_t">Website</label>
                        <input id="website_t" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="form-row">
                        <label for="tip_message">Your message</label>
                        <textarea id="tip_message" data-tip-message maxlength="4000" rows="8" placeholder="What you want us to know. Include a way to reach you here if you'd like a reply."></textarea>
                    </div>

                    <p class="tip-status" data-tip-status>Encryption loads once you open this form.</p>
                    <button class="btn btn-primary" type="submit" data-tip-submit>Encrypt and send</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
