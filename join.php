<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Join the group';
$pageDescription = 'Sign up to hear about events, consultations, and volunteer opportunities from ' . SITE_BRAND . '.';
$currentNav = 'join';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        flash_set('join', 'That submission could not be verified. Please try again.');
        header('Location: /join.php');
        exit;
    }

    $name = trim((string) ($_POST['full_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $locality = trim((string) ($_POST['locality'] ?? ''));
    $interests = trim((string) ($_POST['interests'] ?? ''));
    $consent = isset($_POST['consent']) && $_POST['consent'] === '1';

    $errors = [];
    if ($name === '' || mb_strlen($name) > 160) {
        $errors[] = 'Please enter your name (up to 160 characters).';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (!$consent) {
        $errors[] = 'Please confirm you agree to the privacy notice before we store your details.';
    }

    if ($errors) {
        flash_set('join', implode(' ', $errors));
        header('Location: /join.php');
        exit;
    }

    if (!db_available()) {
        flash_set('join', 'The database is not available right now. Your details were not saved—please try again later or use the contact form.');
        header('Location: /join.php');
        exit;
    }

    try {
        $stmt = db()->prepare(
            'INSERT INTO member_signups (full_name, email, locality, interests, consent) VALUES (:full_name, :email, :locality, :interests, :consent)'
        );
        $stmt->execute([
            'full_name' => $name,
            'email' => $email,
            'locality' => $locality === '' ? null : $locality,
            'interests' => $interests === '' ? null : $interests,
            'consent' => $consent ? 1 : 0,
        ]);
        flash_set('join_ok', 'Thanks—you are on the list. We will only email you about this campaign and related digital inclusion work.');
    } catch (Throwable) {
        flash_set('join', 'Something went wrong saving your signup. Please try again in a little while.');
    }

    header('Location: /join.php');
    exit;
}

$flashErr = flash_take('join');
$flashOk = flash_take('join_ok');

$pageOgImage = image_asset('card-community.jpg');
$pageOgImageAlt = 'Community meeting with a laptop—symbolising people joining the campaign.';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Join <?= e(SITE_BRAND) ?></h1>
        <p>Leave your details to hear about meet-ups, consultation deadlines, and volunteer shifts. You can unsubscribe any time—see our <a href="/privacy.php">privacy page</a> for how we handle data.</p>
    </div>
</header>

<div class="section">
    <div class="wrap join-grid">
        <div>
        <?php if ($flashOk): ?>
            <p class="flash ok"><?= e($flashOk) ?></p>
        <?php endif; ?>
        <?php if ($flashErr): ?>
            <p class="flash err"><?= e($flashErr) ?></p>
        <?php endif; ?>

        <form class="forms" method="post" action="/join.php" novalidate>
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
                <label for="locality">Locality (optional)</label>
                <input id="locality" name="locality" maxlength="120" placeholder="e.g. Dundee, Highland, Glasgow tenement district">
                <p class="form-hint">Helps us invite you to nearby activities when we run them.</p>
            </div>

            <div class="form-row">
                <label for="interests">Skills or interests (optional)</label>
                <textarea id="interests" name="interests" maxlength="2000" placeholder="Writing, design, community organising, accessibility…"></textarea>
            </div>

            <div class="form-row checkbox">
                <input id="consent" name="consent" type="checkbox" value="1" required>
                <label for="consent">I agree to <?= e(SITE_BRAND) ?> storing this information for campaign purposes as described on the privacy page.</label>
            </div>

            <button class="btn btn-primary" type="submit">Join</button>
        </form>
        </div>
        <figure class="join-aside">
            <img src="<?= e(image_asset('card-community.jpg')) ?>" width="1200" height="800" alt="" decoding="async" loading="lazy">
            <figcaption>We keep sign-up light: tell us what you care about and we will match volunteer tasks when they appear.</figcaption>
        </figure>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
