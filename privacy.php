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
            <li><strong>Technical logs:</strong> your web hosting provider may also record connection details—including IP addresses—as a standard part of running any website.</li>
        </ul>

        <h2>Why we collect it</h2>
        <p>We use sign-ups to coordinate volunteers and send occasional campaign updates. We use contact messages to respond to corrections, partnerships, and local stories.</p>

        <h2>Consent</h2>
        <p>The join form requires an explicit consent checkbox linked to this page. We do not intend to buy marketing lists or share your details with unrelated third parties.</p>

        <h2>Retention and security</h2>
        <p>We haven't yet set formal retention limits—how long we keep your data is something we'll document as the campaign develops. As a rule, contact messages will be deleted once handled, and sign-ups will be moved to a secure mailing tool when one is set up.</p>

        <h2>Your rights</h2>
        <p>Depending on where you live, you may have rights to access, correct, or delete personal data we hold. If you'd like us to remove your details, please use the <a href="/contact.php">contact form</a> and we'll action it promptly.</p>

        <h2>Cookies</h2>
        <p>This site uses a temporary security cookie that protects our forms from abuse. It is deleted when you close your browser. We do not set advertising or tracking cookies.</p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
