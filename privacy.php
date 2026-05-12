<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Privacy';
$pageDescription = 'How ' . SITE_BRAND . ' handles information submitted through this website.';
$currentNav = '';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Privacy</h1>
        <p>Plain-language notes for a volunteer-run campaign site. This is not legal advice; organisations with formal compliance duties should seek independent counsel.</p>
    </div>
</header>

<div class="section">
    <div class="wrap prose">
        <h2>What we collect</h2>
        <ul>
            <li><strong>Join form:</strong> name, email, optional locality and interests text, plus a timestamp—stored in the <code>member_signups</code> table when the database is configured.</li>
            <li><strong>Contact form:</strong> name, email, subject, message body, and timestamp—stored in <code>contact_messages</code>.</li>
            <li><strong>Technical logs:</strong> your hosting provider or reverse proxy may log IP addresses independently of this application.</li>
        </ul>

        <h2>Why we collect it</h2>
        <p>We use sign-ups to coordinate volunteers and send occasional campaign updates. We use contact messages to respond to corrections, partnerships, and local stories.</p>

        <h2>Consent</h2>
        <p>The join form requires an explicit consent checkbox linked to this page. We do not intend to buy marketing lists or share your details with unrelated third parties.</p>

        <h2>Retention and security</h2>
        <p>Retention periods should be decided by the people operating this deployment (for example, deleting contact messages after they are handled, or exporting sign-ups to a secure mailing tool). At minimum, keep database credentials out of version control (for example in a gitignored <code>.env</code> file or host-managed environment variables) and keep server software patched.</p>

        <h2>Your rights</h2>
        <p>Depending on jurisdiction, you may have rights to access, correct, or delete personal data we hold. Because this build stores email addresses, a process for verified deletion requests should exist in production—use the contact form once operational.</p>

        <h2>Cookies</h2>
        <p>This starter site uses a PHP session cookie for CSRF protection on forms. It does not set third-party advertising cookies.</p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
