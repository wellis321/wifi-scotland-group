<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Sign up your organisation';
$pageDescription = 'Add your organisation to the WIRES coalition — publicly committing to the principle that affordable, reliable internet access is essential infrastructure in Scotland.';
$currentNav      = 'supporters';

$orgTypes = [
    'Housing association or housing co-op',
    'Trade union or staff organisation',
    'Charity or voluntary organisation',
    'Community group or residents association',
    'Faith community',
    'Educational institution',
    'Health or social care organisation',
    'Library or cultural service',
    'Business or social enterprise',
    'Council, public body or statutory service',
    'Other',
];

$submitted = false;
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $f = [
            'org_name'      => trim((string) ($_POST['org_name']      ?? '')),
            'org_type'      => trim((string) ($_POST['org_type']      ?? '')) ?: null,
            'org_url'       => trim((string) ($_POST['org_url']       ?? '')) ?: null,
            'location'      => trim((string) ($_POST['location']      ?? '')) ?: null,
            'contact_name'  => trim((string) ($_POST['contact_name']  ?? '')),
            'contact_email' => trim((string) ($_POST['contact_email'] ?? '')),
            'why_joining'   => trim((string) ($_POST['why_joining']   ?? '')) ?: null,
            'consent_public'=> isset($_POST['consent_public']) ? 1 : 0,
        ];

        if ($f['org_name'] === '')      $errors[] = 'Organisation name is required.';
        if ($f['contact_name'] === '')  $errors[] = 'Contact name is required.';
        if ($f['contact_email'] === '' || !filter_var($f['contact_email'], FILTER_VALIDATE_EMAIL))
                                        $errors[] = 'A valid contact email is required.';
        if (!$f['consent_public'])      $errors[] = 'Please confirm consent to appear in the public directory.';
        if ($f['org_url'] !== null && !filter_var($f['org_url'], FILTER_VALIDATE_URL))
                                        $errors[] = 'Website URL must be a valid URL (include https://).';

        if (empty($errors)) {
            if (db_available()) {
                try {
                    db()->prepare(
                        'INSERT INTO org_supporters (org_name, org_type, org_url, location, contact_name, contact_email, why_joining, consent_public)
                         VALUES (:org_name, :org_type, :org_url, :location, :contact_name, :contact_email, :why_joining, :consent_public)'
                    )->execute($f);
                    $submitted = true;

                    /* Notify admin */
                    $notifyTo = env_raw('NOTIFY_EMAIL') ?? '';
                    if ($notifyTo !== '' && filter_var($notifyTo, FILTER_VALIDATE_EMAIL)) {
                        $host = parse_url((string) app_config()['app']['base_url'], PHP_URL_HOST)
                            ?: (string) ($_SERVER['HTTP_HOST'] ?? 'wires.org.uk');
                        mail(
                            $notifyTo,
                            '[WIRES] New organisational supporter: ' . $f['org_name'],
                            implode("\n", [
                                'New organisational supporter awaiting approval',
                                str_repeat('-', 50),
                                'Organisation: ' . $f['org_name'],
                                'Type: ' . ($f['org_type'] ?? '—'),
                                'Location: ' . ($f['location'] ?? '—'),
                                'Website: ' . ($f['org_url'] ?? '—'),
                                'Contact: ' . $f['contact_name'] . ' <' . $f['contact_email'] . '>',
                                '',
                                'Why joining:',
                                $f['why_joining'] ?? '(not provided)',
                                '',
                                'Approve at: ' . page_url('admin/org-supporters.php'),
                            ]),
                            implode("\r\n", [
                                'From: noreply@' . $host,
                                'Reply-To: ' . $f['contact_email'],
                                'Content-Type: text/plain; charset=UTF-8',
                            ])
                        );
                    }
                } catch (Throwable) {
                    $errors[] = 'Something went wrong saving your signup. Please try again.';
                }
            } else {
                $errors[] = 'The database is not available right now. Please try again later or contact us directly.';
            }
        }
    }
}

$sidebarRelated = [
    ['href' => '/supporters.php',  'label' => 'View all supporters'],
    ['href' => '/join.php',        'label' => 'Join as an individual'],
    ['href' => '/get-involved.php','label' => 'Get involved'],
    ['href' => '/contact.php',     'label' => 'Contact WIRES'],
];

require_once __DIR__ . '/includes/header.php';

/* Badge HTML — absolute URL for the copyable embed code, relative for on-page preview */
$logoUrlAbs = page_url('images/logo.png');   // absolute — for the code supporters copy
$logoUrlRel = image_asset('logo.png');        // relative — for the preview on this page
$supportersUrl = page_url('supporters');

/* Badge 1: standalone logo */
$badgeDark     = '<a href="' . $supportersUrl . '" target="_blank" rel="noopener noreferrer" style="display:inline-block;text-decoration:none">
  <img src="' . $logoUrlAbs . '" alt="We support WIRES — Web Infrastructure Rights for Everyone in Scotland" width="160" height="87" style="display:block;border:none">
</a>';
$badgeDarkPreview = '<a href="' . $supportersUrl . '" target="_blank" rel="noopener noreferrer" style="display:inline-block;text-decoration:none">
  <img src="' . e($logoUrlRel) . '" alt="We support WIRES — Web Infrastructure Rights for Everyone in Scotland" width="160" height="87" style="display:block;border:none">
</a>';

/* Badge 2: logo on dark card */
$badgeLight     = '<a href="' . $supportersUrl . '" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:12px;padding:12px 16px 12px 12px;background:#0c1117;text-decoration:none;border-radius:10px;font-family:sans-serif;max-width:340px">
  <img src="' . $logoUrlAbs . '" alt="WIRES" width="64" height="35" style="display:block;flex-shrink:0;border:none">
  <span style="color:#fff;font-size:12px;font-weight:600;line-height:1.5">Proud supporter of<br><strong style="font-size:14px;letter-spacing:-.01em">WIRES Scotland</strong></span>
</a>';
$badgeLightPreview = '<a href="' . $supportersUrl . '" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:12px;padding:12px 16px 12px 12px;background:#0c1117;text-decoration:none;border-radius:10px;font-family:sans-serif;max-width:340px">
  <img src="' . e($logoUrlRel) . '" alt="WIRES" width="64" height="35" style="display:block;flex-shrink:0;border:none">
  <span style="color:#fff;font-size:12px;font-weight:600;line-height:1.5">Proud supporter of<br><strong style="font-size:14px;letter-spacing:-.01em">WIRES Scotland</strong></span>
</a>';
?>

<header class="page-header">
    <div class="wrap">
        <h1>Sign up your organisation</h1>
        <p>Publicly commit to the principle that affordable, reliable internet access is essential infrastructure — and join the growing coalition of Scottish organisations saying so.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">

        <div class="prose">

        <?php if ($submitted): ?>

            <div class="info-card" style="margin-bottom:2rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Thank you — you're signed up</h2>
                    <p class="info-card__sub">We'll review and add you to the public directory shortly</p>
                </div>
                <div class="info-card__body">
                    <p>We review all signups before listing them publicly — usually within a few days. We'll get in touch if we have any questions.</p>
                    <p>In the meantime, here are your supporter assets to use straight away.</p>
                </div>
            </div>

        <?php else: ?>

            <div class="info-card" style="margin-bottom:2rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">What you're committing to</h2>
                    <p class="info-card__sub">A values statement, not a contractual obligation</p>
                </div>
                <div class="info-card__body">
                    <p>By signing up, your organisation is publicly stating that it supports the principle that affordable, reliable internet access is essential infrastructure — and that Scotland should treat it accordingly.</p>
                    <p>We ask only that you are genuine in your support and that you help spread the word when you can. There is no fee, no legal commitment, and no minimum activity required.</p>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="callout callout--alert" role="alert" style="margin-bottom:1.5rem">
                    <p class="callout__eyebrow">Please fix the following</p>
                    <ul style="margin:0.5rem 0 0">
                        <?php foreach ($errors as $err): ?>
                            <li><?= e($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <h2>About your organisation</h2>

                <div class="form-row">
                    <label for="org_name">Organisation name</label>
                    <input id="org_name" name="org_name" type="text" required maxlength="220"
                           value="<?= e((string) ($_POST['org_name'] ?? '')) ?>">
                </div>

                <div class="form-row">
                    <label for="org_type">Type of organisation</label>
                    <select id="org_type" name="org_type">
                        <option value="">— Select —</option>
                        <?php foreach ($orgTypes as $t): ?>
                            <option value="<?= e($t) ?>" <?= ($_POST['org_type'] ?? '') === $t ? 'selected' : '' ?>><?= e($t) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <label for="location">Location <span style="font-weight:400">(optional)</span></label>
                    <input id="location" name="location" type="text" maxlength="120"
                           placeholder="e.g. Dundee, Highland, Glasgow"
                           value="<?= e((string) ($_POST['location'] ?? '')) ?>">
                </div>

                <div class="form-row">
                    <label for="org_url">Website <span style="font-weight:400">(optional)</span></label>
                    <input id="org_url" name="org_url" type="url" maxlength="500"
                           placeholder="https://..."
                           value="<?= e((string) ($_POST['org_url'] ?? '')) ?>">
                </div>

                <div class="form-row">
                    <label for="why_joining">Why your organisation is signing up <span style="font-weight:400">(optional — shown on your public listing)</span></label>
                    <textarea id="why_joining" name="why_joining" maxlength="500"
                              placeholder="e.g. As a housing association, we see the daily impact of digital exclusion on our tenants..."><?= e((string) ($_POST['why_joining'] ?? '')) ?></textarea>
                    <p class="form-hint">Keep it brief — one or two sentences. This appears as a quote beside your organisation's name.</p>
                </div>

                <h2>Your contact details</h2>
                <p style="color:var(--muted);font-size:0.95rem;margin-top:-0.5rem">Not shown publicly — only used if we need to follow up.</p>

                <div class="form-row">
                    <label for="contact_name">Your name</label>
                    <input id="contact_name" name="contact_name" type="text" required maxlength="160"
                           autocomplete="name"
                           value="<?= e((string) ($_POST['contact_name'] ?? '')) ?>">
                </div>

                <div class="form-row">
                    <label for="contact_email">Your email</label>
                    <input id="contact_email" name="contact_email" type="email" required maxlength="255"
                           autocomplete="email"
                           value="<?= e((string) ($_POST['contact_email'] ?? '')) ?>">
                </div>

                <div class="form-row checkbox" style="margin-top:1.25rem">
                    <input id="consent_public" name="consent_public" type="checkbox" value="1"
                           required <?= !empty($_POST['consent_public']) ? 'checked' : '' ?>>
                    <label for="consent_public">I confirm that I am authorised to sign this commitment on behalf of my organisation, and I consent to our organisation's name and details appearing in the public supporters directory.</label>
                </div>

                <button class="btn btn-primary" type="submit" style="margin-top:1.5rem">Sign the commitment</button>
            </form>

        <?php endif; ?>

            <!-- Supporter toolkit — shown always (before and after submission) -->
            <h2 style="margin-top:3rem">Supporter toolkit</h2>
            <p>Use these assets to tell your members, partners, and community that you support WIRES.</p>

            <div class="toolkit-section">
                <h3 class="toolkit-heading">Website badge</h3>
                <p>Copy and paste one of these into your website, email footer, or newsletter. The logo image is served from wires.org.uk so it stays up to date automatically.</p>

                <p class="toolkit-sublabel">Standalone logo (works on any background)</p>
                <div class="badge-preview">
                    <?= $badgeDarkPreview ?>
                </div>
                <div class="badge-code-wrap">
                    <textarea class="badge-code" readonly aria-label="Standalone logo badge HTML"><?= htmlspecialchars($badgeDark, ENT_QUOTES, 'UTF-8') ?></textarea>
                    <button class="btn btn-ghost btn-sm badge-copy-btn" data-target="badge-dark">Copy code</button>
                    <span id="badge-dark" style="display:none"><?= htmlspecialchars($badgeDark, ENT_QUOTES, 'UTF-8') ?></span>
                </div>

                <p class="toolkit-sublabel" style="margin-top:1.5rem">Dark card badge (email footers &amp; sidebars)</p>
                <div class="badge-preview badge-preview--dark">
                    <?= $badgeLightPreview ?>
                </div>
                <div class="badge-code-wrap">
                    <textarea class="badge-code" readonly aria-label="Dark card badge HTML"><?= htmlspecialchars($badgeLight, ENT_QUOTES, 'UTF-8') ?></textarea>
                    <button class="btn btn-ghost btn-sm badge-copy-btn" data-target="badge-light">Copy code</button>
                    <span id="badge-light" style="display:none"><?= htmlspecialchars($badgeLight, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>

            <div class="toolkit-section">
                <h3 class="toolkit-heading">Social media</h3>
                <p>Ready-to-post messages for your channels. Update the date or add your own context.</p>
                <div class="social-template">
                    <p class="social-template__text">[Organisation name] is proud to support @WIRESScotland — the campaign for web infrastructure rights for everyone in Scotland. Affordable, reliable internet is essential infrastructure. Find out more: <?= e(page_url()) ?></p>
                    <button class="btn btn-ghost btn-sm social-copy-btn">Copy message</button>
                </div>
                <div class="social-template" style="margin-top:0.75rem">
                    <p class="social-template__text">We believe connectivity is a right, not a luxury. That's why [Organisation name] supports WIRES — the campaign pushing for affordable broadband to be treated as essential infrastructure across Scotland. <?= e(page_url('supporters.php')) ?> #WIRESScotland #DigitalInclusion</p>
                    <button class="btn btn-ghost btn-sm social-copy-btn">Copy message</button>
                </div>
            </div>

            <div class="toolkit-section">
                <h3 class="toolkit-heading">For your members and service users</h3>
                <p>Share these pages with the people you work with:</p>
                <ul>
                    <li><strong><a href="/get-help.php">Help getting online</a></strong> — schemes and programmes that can lower broadband costs or provide free SIM cards</li>
                    <li><strong><a href="/write-to-councillor.php">Write to your councillor</a></strong> — a ready-to-use template letter asking local councils to act</li>
                    <li><strong><a href="/why-it-matters.php">Why it matters</a></strong> — evidence from UK and Scottish sources on the impact of digital exclusion</li>
                </ul>
                <p class="meta">Printable leaflets are in development. <a href="/contact.php">Get in touch</a> if you need materials for a specific event or campaign activity.</p>
            </div>

        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div>
    </div>
</div>

<script>
(function () {
    /* Badge copy buttons */
    document.querySelectorAll('.badge-copy-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = this.dataset.target;
            var text = document.getElementById(targetId)
                ? document.getElementById(targetId).textContent
                : '';
            navigator.clipboard.writeText(text).then(function () {
                btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = 'Copy code'; }, 2200);
            });
        });
    });

    /* Social message copy buttons */
    document.querySelectorAll('.social-copy-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var text = this.closest('.social-template')
                .querySelector('.social-template__text').textContent.trim();
            navigator.clipboard.writeText(text).then(function () {
                btn.textContent = 'Copied!';
                setTimeout(function () { btn.textContent = 'Copy message'; }, 2200);
            });
        });
    });
})();
</script>

<?php
$ctaHeading = 'Spread the word';
$ctaBody    = 'The more organisations that publicly back this campaign, the harder it is for policymakers to ignore.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
