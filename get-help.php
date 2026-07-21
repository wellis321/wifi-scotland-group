<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/schemes_data.php';

$pageTitle = 'Help getting online';
$pageDescription = 'Schemes and programmes that can help people in Scotland get connected or pay less for broadband—with links to official sources so you can check current eligibility.';
$currentNav = 'gethelp';

$sidebarRelated = [
    ['href' => '/why-it-matters.php', 'label' => 'Why it matters'],
    ['href' => '/scotland.php',       'label' => 'Scotland policy'],
    ['href' => '/get-involved.php',   'label' => 'Get involved'],
    ['href' => '/resources.php',      'label' => 'Resources & references'],
];

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

// Manual pins: social tariffs and the National Databank lead (biggest impact,
// most people qualify), Connecting Scotland trails (paused for redesign,
// least actionable right now). Everything else keeps its normal
// updated_month/sort_order position.
$pinOrder = ['social-tariffs' => 0, 'national-databank' => 1];
$pinFirst = [];
$pinLast  = [];
$rest     = [];
foreach ($schemes as $s) {
    $slug = $s['slug'] ?? '';
    if (isset($pinOrder[$slug])) {
        $pinFirst[$pinOrder[$slug]] = $s;
    } elseif ($slug === 'connecting-scotland') {
        $pinLast[] = $s;
    } else {
        $rest[] = $s;
    }
}
ksort($pinFirst);
$schemes = [...$pinFirst, ...$rest, ...$pinLast];

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
        <div class="page-layout" style="padding-top:0">

        <div>

        <div class="info-card">
            <div class="info-card__header">
                <h2 class="info-card__heading">No fixed address doesn't mean no options</h2>
                <p class="info-card__sub">Two things work without one — here's where to start</p>
            </div>
            <div class="info-card__body">
                <p>Most schemes on this page assume a permanent address and a contract. That's a gap in how they were designed, not a reflection of what you're entitled to — and it shuts out people who are homeless or in temporary accommodation, regardless of income.</p>
                <p>The <strong>National Databank</strong> — free SIM cards with data — works without an address. It is distributed through foodbanks, hostels, day centres, and libraries. Ask at a local <a href="https://scotland.shelter.org.uk/get_help"<?= external_link_attrs('https://scotland.shelter.org.uk/get_help') ?>>Shelter Scotland</a> office, foodbank, or community centre whether they carry them.</p>
                <p><strong>Jobcentre Plus</strong> branches across Scotland have free computers and Wi-Fi for job searching, Universal Credit management, and general internet access — no appointment needed for device use. In-person digital support is also available; hours vary by branch, so ask at reception.</p>
                <p><a class="btn btn-ghost btn-sm" href="/why-it-matters.php#homelessness">Homelessness &amp; digital exclusion &rarr;</a></p>
            </div>
        </div>

        <div class="callout" style="margin-bottom:2rem">
            <p class="callout__eyebrow">Applying is easier than you'd think</p>
            <p>You don't need to provide paper proof to get a social tariff — your broadband provider can verify your Universal Credit claim automatically through the DWP system. Just tell them you're on Universal Credit and ask for their social tariff.</p>
        </div>

        <div class="info-card" style="margin-bottom:2rem">
            <div class="info-card__header">
                <h2 class="info-card__heading">Prefer to talk to a real person?</h2>
                <p class="info-card__sub">Free, confidential, one-to-one help applying</p>
            </div>
            <div class="info-card__body">
                <p><a href="https://www.cas.org.uk/get-connected"<?= external_link_attrs('https://www.cas.org.uk/get-connected') ?>>Citizens Advice Scotland's Get Connected campaign</a> exists for exactly this: bureaux across Scotland helping people check what they qualify for and apply for a cheaper deal, in person or on the phone — you don't need to already understand the system.</p>
                <p><a class="btn btn-ghost btn-sm" href="https://www.cas.org.uk/bureaux"<?= external_link_attrs('https://www.cas.org.uk/bureaux') ?>>Find your local bureau &rarr;</a></p>
            </div>
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

        </div><!-- /main column -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div><!-- /page-layout -->
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
