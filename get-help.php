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

                        <p class="scheme-source">
                            <a class="btn btn-primary" href="<?= e($s['url']) ?>"<?= external_link_attrs($s['url']) ?>>
                                <?= e($s['source_label']) ?> &rarr;
                            </a>
                        </p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="callout prose" style="margin-top: 3rem">
            <p><strong>Know of a scheme we've missed?</strong> Funding programmes come and go. If there's something that should be on this page—local authority schemes, housing association offers, charity programmes—<a href="/contact.php">tell us</a> and we'll add it.</p>
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
