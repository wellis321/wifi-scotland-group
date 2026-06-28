<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Terms of use';
$pageDescription = 'How you may use content from ' . SITE_BRAND . ', what we are and are not responsible for, and our approach to third-party links.';
$currentNav      = '';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Terms of use</h1>
        <p>Plain-language terms for a volunteer-run campaign site. Last updated <?= date('F Y') ?>.</p>
    </div>
</header>

<div class="section">
    <div class="wrap prose">

        <h2>What this site is</h2>
        <p>
            <?= e(SITE_BRAND) ?> (WIRES) is a volunteer-led campaign for affordable, reliable internet access as essential public infrastructure in Scotland.
            This website is a campaign and information resource. It is not a commercial service, a government body, or a regulated advice provider.
        </p>

        <h2>Using our content</h2>
        <p>
            Content published here — articles, summaries, and campaign commentary — may be shared and quoted freely for non-commercial purposes,
            provided you attribute it to <?= e(SITE_BRAND) ?> and link back to the original page where possible.
            We ask that you do not present our summaries as official government or regulatory statements — always link to the primary sources we cite.
        </p>
        <p>
            Images on this site are sourced from Unsplash under their free-to-use licence. See our <a href="/credits.php">image credits page</a> for details.
        </p>

        <h2>Accuracy and updates</h2>
        <p>
            We aim to keep information accurate and link to primary sources, but programme rules, funding amounts, and eligibility criteria change.
            We are a volunteer organisation and cannot guarantee that every page reflects the most current position.
            <strong>Always verify figures and eligibility on the official pages we link to</strong> before acting on them — especially for vouchers,
            broadband contracts, or anything with financial or legal implications.
        </p>

        <h2>Not professional advice</h2>
        <p>
            Nothing on this site constitutes legal, financial, or technical advice. If you need help with a specific broadband contract, billing dispute,
            or consumer rights matter, please contact <a href="https://www.citizensadvice.org.uk/"<?= external_link_attrs('https://www.citizensadvice.org.uk/') ?>>Citizens Advice</a>,
            <a href="https://www.cas.org.uk/"<?= external_link_attrs('https://www.cas.org.uk/') ?>>Citizens Advice Scotland</a>, or your broadband provider directly.
        </p>

        <h2>Third-party links</h2>
        <p>
            We link to government, regulator, charity, and community organisation websites to help you verify what we say.
            We are not responsible for the content, availability, or accuracy of those external sites.
            Links to external sites do not imply endorsement of everything those organisations do.
        </p>

        <h2>Copyright</h2>
        <p>
            &copy; <?= date('Y') ?> <?= e(SITE_BRAND) ?>. Original campaign content on this site is the work of WIRES volunteers.
            Scottish Government, Ofcom, and other official documents linked from this site remain the copyright of their respective publishers.
        </p>

        <h2>Contact</h2>
        <p>
            If you have a question about these terms, or want to report an error or broken link, please use our <a href="/contact.php">contact form</a>.
        </p>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
