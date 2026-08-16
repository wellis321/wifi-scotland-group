<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Council replies — did your council answer?';
$pageDescription = 'WIRES wrote to every one of Scotland\'s 32 councils asking whether they have a digital inclusion action plan. Here is who replied, and what they said.';
$currentNav      = 'councilreplies';

$pageOgImage    = image_asset('card-community.jpg');
$pageOgImageAlt = 'Person writing at a table — representing constituent and campaign contact with elected representatives.';

$sidebarRelated = [
    ['href' => '/write-to-councillor', 'label' => 'Write to your councillor'],
    ['href' => '/accountability',      'label' => 'Who is acting?'],
    ['href' => '/figures',             'label' => 'Figures & sources'],
];

$councils = [];
if (db_available()) {
    try {
        $councils = db()->query(
            'SELECT council_area, directory_url, outreach_sent_at, outreach_replied_at, reply_summary
             FROM council_contacts ORDER BY council_area ASC'
        )->fetchAll();
    } catch (Throwable) {}
}

$sent    = array_filter($councils, static fn($c) => !empty($c['outreach_sent_at']));
$replied = array_filter($councils, static fn($c) => !empty($c['outreach_replied_at']));

usort($sent, static function ($a, $b) {
    // Replied councils first, then most recently sent
    $aReplied = !empty($a['outreach_replied_at']);
    $bReplied = !empty($b['outreach_replied_at']);
    if ($aReplied !== $bReplied) return $bReplied <=> $aReplied;
    return strcmp((string) $b['outreach_sent_at'], (string) $a['outreach_sent_at']);
});

$notYetSent = array_filter($councils, static fn($c) => empty($c['outreach_sent_at']));

$total        = count($councils) ?: 32;
$sentCount    = count($sent);
$repliedCount = count($replied);
$replyRate    = $sentCount > 0 ? round(($repliedCount / $sentCount) * 100) : 0;

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Council replies</h1>
        <p>In 2026, WIRES wrote to every councillor in Scotland's 32 councils, asking three specific questions: has your council published a digital inclusion action plan, what are you doing to promote social tariffs, and will you press COSLA and the Scottish Government for a national plan. This page tracks what happened next — every reply, and every silence.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">
        <div class="prose">

            <div class="callout">
                <p class="callout__eyebrow">How this page works</p>
                <p>
                    <?php if ($sentCount === 0): ?>
                        Letters are going out to all 32 councils. As each one is sent and replies come in, this page updates directly from our outreach log — nothing here is curated after the fact.
                    <?php else: ?>
                        <strong><?= $sentCount ?> of <?= $total ?></strong> councils have been written to.
                        <strong><?= $repliedCount ?></strong> replied<?= $sentCount > 0 ? " ({$replyRate}% response rate)" : '' ?>.
                        This updates directly from our outreach log — a council appears here the moment we send it a letter, whether or not it ever answers.
                    <?php endif; ?>
                </p>
            </div>

            <div class="stat-strip">
                <div class="stat-item">
                    <span class="stat-value"><?= $sentCount ?>/<?= $total ?></span>
                    <span class="stat-label">councils contacted</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= $repliedCount ?></span>
                    <span class="stat-label">replied so far</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= $sentCount > 0 ? $replyRate . '%' : '—' ?></span>
                    <span class="stat-label">response rate</span>
                </div>
            </div>

            <?php if (empty($sent)): ?>
                <p>No letters logged as sent yet — check back soon.</p>
            <?php else: ?>
                <h2>Contacted so far</h2>
                <div class="figure-log">
                    <?php foreach ($sent as $c): ?>
                        <div class="figure-log__item">
                            <p class="figure-log__claim">
                                <?= e($c['council_area']) ?>
                                <?php if (!empty($c['outreach_replied_at'])): ?>
                                    <span class="pill pill--active" style="margin-left:0.5rem">Replied</span>
                                <?php else: ?>
                                    <span class="pill pill--forming" style="margin-left:0.5rem">Awaiting reply</span>
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($c['reply_summary'])): ?>
                                <p class="figure-log__note"><?= e($c['reply_summary']) ?></p>
                            <?php endif; ?>
                            <p class="figure-log__meta">
                                <span class="figure-log__date">Sent <?= e(format_date((string) $c['outreach_sent_at'])) ?></span>
                                <?php if (!empty($c['outreach_replied_at'])): ?>
                                    <span class="figure-log__date">&middot; Replied <?= e(format_date((string) $c['outreach_replied_at'])) ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($notYetSent)): ?>
                <h2 style="margin-top:2.5rem">Not yet contacted</h2>
                <p class="meta">These councils are on the list but haven't been written to yet: <?= e(implode(', ', array_map(static fn($c) => $c['council_area'], $notYetSent))) ?>.</p>
            <?php endif; ?>

            <div class="info-card" style="margin-top:2rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">Heard back from your council?</h2>
                    <p class="info-card__sub">Or want to add your own voice</p>
                </div>
                <div class="info-card__body">
                    <p>If your council has responded to a constituent letter about digital inclusion, or if you'd like to send your own, <a href="/write-to-councillor">use our template letter</a> or <a href="/contact">tell us what you heard</a> — resident replies help us track councils faster than we can alone.</p>
                </div>
            </div>

        </div><!-- /prose -->

        <?php require __DIR__ . '/includes/sidebar-campaign.php'; ?>

        </div><!-- /page-layout -->
    </div>
</div>

<?php
$ctaHeading = 'Help us track more councils';
$ctaBody    = 'Join WIRES and we\'ll keep you updated as replies come in — and let you know when it\'s worth following up.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
