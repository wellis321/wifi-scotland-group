<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Local groups';
$pageDescription = 'Find a WIFI Scotland Group in your council area, or start one. Local groups map gaps, raise questions at council meetings, and give neighbours somewhere to turn.';
$currentNav = 'groups';

$groups = [];
$dbOk = db_available();
if ($dbOk) {
    try {
        $stmt = db()->query(
            'SELECT id, slug, council_area, tagline, status FROM local_groups ORDER BY council_area ASC'
        );
        $groups = $stmt->fetchAll();
    } catch (Throwable) {
        $groups = [];
        $dbOk = false;
    }
}

$pageOgImage = image_asset('card-community.jpg');
$pageOgImageAlt = 'People meeting around a table—representing local campaign groups across Scotland.';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Local groups</h1>
        <p>Every national campaign is made of local people. Find a group in your council area—or start one.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">

        <?php if (!$dbOk): ?>
            <div class="callout prose">
                <p><strong>Database not connected.</strong> Local groups are stored in the database. Once configured, this page will list groups across Scotland.</p>
            </div>

        <?php elseif (empty($groups)): ?>
            <div class="groups-empty">
                <p class="section-intro">No local groups are listed yet—Scotland is waiting for people like you to start one. It takes as few as two or three people and a first conversation.</p>
                <a class="btn btn-primary btn-lg" href="/start-a-group.php">How to start a group &rarr;</a>
            </div>

        <?php else: ?>
            <p class="section-intro">Groups are run by local volunteers. Contact a group directly or <a href="/start-a-group.php">start one</a> if your area is not listed.</p>
            <div class="card-grid cols-3">
                <?php foreach ($groups as $g): ?>
                    <?php
                    $statusLabel = match($g['status']) {
                        'active'            => 'Active',
                        'forming'           => 'Forming',
                        'seeking_organiser' => 'Needs an organiser',
                        default             => ''
                    };
                    $statusClass = match($g['status']) {
                        'active'            => 'pill pill--active',
                        'seeking_organiser' => 'pill pill--seeking',
                        default             => 'pill pill--forming'
                    };
                    ?>
                    <article class="card">
                        <span class="<?= e($statusClass) ?>"><?= e($statusLabel) ?></span>
                        <h3><?= e($g['council_area']) ?></h3>
                        <?php if (!empty($g['tagline'])): ?>
                            <p><?= e($g['tagline']) ?></p>
                        <?php endif; ?>
                        <p class="card-cta">
                            <a class="btn btn-primary" href="<?= e('/group.php?slug=' . rawurlencode((string) $g['slug'])) ?>">Visit group page</a>
                        </p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="callout prose" style="margin-top: 2.5rem">
            <p><strong>Don't see your area?</strong> <a href="/start-a-group.php">Find out how to start a local group</a>—we'll support you and list your group here once you're up and running.</p>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
