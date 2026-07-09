<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/schemes_data.php';

$pageTitle = 'Help getting online';
$pageDescription = 'Schemes and programmes that can help people in Scotland get connected or pay less for broadband—with links to official sources so you can check current eligibility.';
$currentNav = 'gethelp';

// DB primary source; PHP array fallback when DB is unavailable
$schemes = [];
if (db_available()) {
    try {
        $schemes = db()->query(
            'SELECT * FROM schemes WHERE status != \'ended\' ORDER BY updated_month DESC, sort_order ASC'
        )->fetchAll();
    } catch (Throwable) {
        $schemes = [];
    }
}
if (empty($schemes)) {
    $schemes = load_schemes();
}

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Help getting online</h1>
        <p>These are real schemes that can help people in Scotland get connected or pay less. We link to official sources—always check there for current eligibility and amounts, as these schemes change.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">

        <div class="info-card">
            <div class="info-card__header">
                <h2 class="info-card__heading">No fixed address?</h2>
                <p class="info-card__sub">Important — most schemes on this page will not apply</p>
            </div>
            <div class="info-card__body">
                <p>Most broadband schemes listed here require a permanent home address and a contract. They are not available to people who are homeless or in temporary accommodation, regardless of income.</p>
                <p>The <strong>National Databank</strong> — free SIM cards with data — is one exception. It is distributed through foodbanks, hostels, day centres, and libraries with no address required. Ask at a local <a href="https://scotland.shelter.org.uk/get_help"<?= external_link_attrs('https://scotland.shelter.org.uk/get_help') ?>>Shelter Scotland</a> office, foodbank, or community centre whether they carry them.</p>
                <p><strong>Jobcentre Plus</strong> branches across Scotland have free computers, Wi-Fi, and digital mentors available Monday to Friday, 10am–3pm — for job searching, Universal Credit management, and general internet access. No appointment needed for device use.</p>
                <p><a class="btn btn-ghost btn-sm" href="/why-it-matters.php#homelessness">Homelessness &amp; digital exclusion &rarr;</a></p>
            </div>
        </div>

        <div class="callout" style="margin-bottom:2rem">
            <p class="callout__eyebrow">On social tariffs</p>
            <p>Only around 532,000 of the 6.2 million Universal Credit households that qualify for a cheaper broadband deal actually use one — that's roughly 1 in 12. Around 7 in 10 people on benefits have never heard that social tariffs exist. When you apply, you don't need to provide paper proof — your broadband provider can verify your Universal Credit claim automatically through the DWP system. Just tell them you're on Universal Credit and ask for their social tariff.</p>
        </div>

        <p class="section-intro">Shown most recently updated first. If you know of a scheme we have missed, <a href="/contact.php">let us know</a>.</p>

        <?php if (empty($schemes)): ?>
            <div class="callout prose">
                <p>No schemes listed yet. <a href="/contact.php">Contact us</a> if you know of funding or programmes we should add.</p>
            </div>
        <?php else: ?>
            <div class="scheme-list">
                <?php foreach ($schemes as $s): ?>
                    <?php
                    $statusLabel = match($s['status']) {
                        'active' => 'Currently active',
                        'check'  => 'Check current status',
                        'ended'  => 'Ended',
                        default  => '',
                    };
                    $statusClass = match($s['status']) {
                        'active' => 'pill pill--active',
                        'ended'  => 'pill pill--seeking',
                        default  => 'pill pill--forming',
                    };
                    $scopeLabel = $s['scope'] === 'scotland' ? 'Scotland only' : 'UK-wide';
                    $updatedFormatted = '';
                    if (!empty($s['updated'])) {
                        $ts = strtotime($s['updated'] . '-01');
                        $updatedFormatted = $ts !== false ? date('F Y', $ts) : $s['updated'];
                    }
                    ?>
                    <article class="scheme-card" id="<?= e($s['slug']) ?>">
                        <div class="scheme-card-header">
                            <div class="scheme-badges">
                                <span class="<?= e($statusClass) ?>"><?= e($statusLabel) ?></span>
                                <span class="pill pill--forming"><?= e($scopeLabel) ?></span>
                            </div>
                            <?php if ($updatedFormatted): ?>
                                <p class="meta">Verified <?= e($updatedFormatted) ?></p>
                            <?php endif; ?>
                        </div>

                        <h2 class="scheme-name"><?= e($s['name']) ?></h2>
                        <p class="scheme-summary"><?= e($s['summary']) ?></p>

                        <dl class="scheme-details">
                            <div class="scheme-detail-row">
                                <dt>Who it's for</dt>
                                <dd><?= e((string)($s['who_for'] ?? $s['who'] ?? '')) ?></dd>
                            </div>
                            <div class="scheme-detail-row">
                                <dt>What you get</dt>
                                <dd><?= e((string)($s['what_you_get'] ?? $s['what'] ?? '')) ?></dd>
                            </div>
                            <div class="scheme-detail-row">
                                <dt>How to apply</dt>
                                <dd><?= e((string)($s['how_to_apply'] ?? $s['how'] ?? '')) ?></dd>
                            </div>
                        </dl>

                        <?php if (!empty($s['note'])): ?>
                            <div class="scheme-note">
                                <p><strong>Note:</strong> <?= e($s['note']) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="scheme-footer">
                            <a class="btn btn-primary" href="<?= e($s['url']) ?>"<?= external_link_attrs($s['url']) ?>>
                                <?= e($s['source_label']) ?> &rarr;
                            </a>
                            <?php
                            $shareUrl   = page_url('get-help.php#' . rawurlencode((string) $s['slug']));
                            $shareTitle = (string) $s['name'];
                            $shareCompact = true;
                            require __DIR__ . '/includes/share.php';
                            ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="info-card" style="margin-top:3rem">
            <div class="info-card__header">
                <h2 class="info-card__heading">Know of a scheme we've missed?</h2>
                <p class="info-card__sub">Help us keep this list current</p>
            </div>
            <div class="info-card__body">
                <p>Funding programmes come and go. If there's something that should be on this page — local authority schemes, housing association offers, charity programmes — let us know and we'll add it.</p>
                <p><a class="btn btn-ghost btn-sm" href="/contact.php">Tell us about it &rarr;</a></p>
            </div>
        </div>

    </div>
</div>

<div class="section alt" aria-labelledby="local-support-heading">
    <div class="wrap prose">
        <h2 id="local-support-heading">Local support in your area</h2>
        <p>Some of these schemes are delivered through local organisations—libraries, community centres, housing associations, and charities. Your local WIRES group may know what's available near you, or be able to help you find out.</p>
        <p>
            <a class="btn btn-primary" href="/groups.php">Find a local WIRES group</a>
            <a class="btn btn-ghost" href="/contact.php">Ask us directly</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
