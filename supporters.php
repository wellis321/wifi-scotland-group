<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Organisational supporters';
$pageDescription = 'Organisations across Scotland that have publicly committed to supporting WIRES — the campaign for web infrastructure rights for everyone.';
$currentNav      = 'supporters';

$orgs = [];
$dbOk = db_available();
if ($dbOk) {
    try {
        $orgs = db()->query(
            "SELECT org_name, org_type, org_url, location, why_joining
             FROM org_supporters
             WHERE status = 'approved' AND consent_public = 1
             ORDER BY org_name ASC"
        )->fetchAll();
    } catch (Throwable) {
        $orgs = [];
    }
}

$pageOgImage    = image_asset('card-community.jpg');
$pageOgImageAlt = 'People gathered together — representing the coalition of organisations supporting WIRES.';

$sidebarRelated = [
    ['href' => '/join-as-organisation.php', 'label' => 'Join as an organisation'],
    ['href' => '/join.php',                 'label' => 'Join as an individual'],
    ['href' => '/get-involved.php',         'label' => 'Get involved'],
    ['href' => '/contact.php',              'label' => 'Contact WIRES'],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Organisational supporters</h1>
        <p>These organisations have publicly committed to supporting WIRES — the campaign for web infrastructure rights for everyone in Scotland. Together they represent housing associations, community groups, trade unions, charities, and civic organisations who believe connectivity is essential infrastructure.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">

        <div>

            <div class="campaign-statement-card" aria-hidden="true" style="margin-bottom:2.5rem">
                <p class="campaign-statement-card__line1">A coalition for</p>
                <p class="campaign-statement-card__line2">connectivity<br>as a right.</p>
            </div>

            <?php if (!$dbOk): ?>
                <div class="callout prose">
                    <p>The supporters list requires a database connection to display.</p>
                </div>
            <?php elseif (empty($orgs)): ?>
                <div class="info-card">
                    <div class="info-card__header">
                        <h2 class="info-card__heading">Be the first to sign</h2>
                        <p class="info-card__sub">The coalition is just getting started</p>
                    </div>
                    <div class="info-card__body">
                        <p>No organisations are listed yet — we're building the coalition now. If your organisation supports the principle that affordable, reliable internet is essential infrastructure, we'd love to hear from you.</p>
                        <p><a class="btn btn-primary btn-sm" href="/join-as-organisation.php">Sign up your organisation &rarr;</a></p>
                    </div>
                </div>
            <?php else: ?>
                <p class="section-intro"><?= count($orgs) ?> organisation<?= count($orgs) !== 1 ? 's' : '' ?> have signed the commitment.</p>
                <div class="supporter-grid">
                    <?php foreach ($orgs as $org): ?>
                    <article class="supporter-card">
                        <?php if (!empty($org['org_type'])): ?>
                            <span class="pill"><?= e($org['org_type']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($org['org_url'])): ?>
                            <h3><a href="<?= e($org['org_url']) ?>"<?= external_link_attrs($org['org_url']) ?>><?= e($org['org_name']) ?></a></h3>
                        <?php else: ?>
                            <h3><?= e($org['org_name']) ?></h3>
                        <?php endif; ?>
                        <?php if (!empty($org['location'])): ?>
                            <p class="supporter-location"><?= e($org['location']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($org['why_joining'])): ?>
                            <blockquote class="supporter-quote"><?= e($org['why_joining']) ?></blockquote>
                        <?php endif; ?>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="info-card" style="margin-top:2.5rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Add your organisation</h2>
                    <p class="info-card__sub">Takes about two minutes</p>
                </div>
                <div class="info-card__body">
                    <p>Any organisation — housing association, trade union, charity, community group, faith community, school, or business — can sign the WIRES commitment. You'll get a badge for your website and materials for your members.</p>
                    <p><a class="btn btn-primary btn-sm" href="/join-as-organisation.php">Sign up your organisation &rarr;</a></p>
                </div>
            </div>

        </div>

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
