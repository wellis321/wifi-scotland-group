<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/campaign-templates/council-ceo-accountability.php';

// Rendered from the exact same template code the real sender uses, so this can never
// drift out of sync with what was actually sent — placeholder values only, not a real row.
$letterPreview = [
    'council_area' => '[your council]',
    'ceo_name'     => "[Chief Executive's name]",
];
$letterSubject = render_subject($letterPreview);
$letterBody    = render_text_body($letterPreview);

$pageTitle       = 'Council replies — did your council answer?';
$pageDescription = 'WIRES wrote to the Chief Executive and Leader of every one of Scotland\'s 32 councils asking four practical questions about digital exclusion. Here is who replied, and what they said.';
$currentNav      = 'councilreplies';

$pageOgImage    = image_asset('card-community.jpg');
$pageOgImageAlt = 'Person writing at a table — representing constituent and campaign contact with elected representatives.';

$sidebarRelated = [
    ['href' => '/accountability-campaign', 'label' => 'Full campaign overview'],
    ['href' => '/councillor-statements', 'label' => 'Councillor statements'],
    ['href' => '/msp-statements',        'label' => 'MSP statements'],
    ['href' => '/mp-statements',         'label' => 'MP statements'],
    ['href' => '/accountability',        'label' => 'Who is acting?'],
];

$councils = [];
if (db_available()) {
    try {
        $councils = db()->query(
            'SELECT council_area, directory_url, outreach_sent_at, outreach_replied_at, reply_summary,
                    ceo_name, leader_name, leader_sent_at, leader_replied_at, leader_reply_summary
             FROM council_contacts ORDER BY council_area ASC'
        )->fetchAll();
    } catch (Throwable) {}
}

// "Contacted" means the CEO or the Leader (or both) has been written to — either
// channel counts as institutional contact with that council.
$sent    = array_filter($councils, static fn($c) => !empty($c['outreach_sent_at']) || !empty($c['leader_sent_at']));
$replied = array_filter($councils, static fn($c) => !empty($c['outreach_replied_at']) || !empty($c['leader_replied_at']));

usort($sent, static function ($a, $b) {
    // Replied councils first, then most recently sent
    $aReplied = !empty($a['outreach_replied_at']) || !empty($a['leader_replied_at']);
    $bReplied = !empty($b['outreach_replied_at']) || !empty($b['leader_replied_at']);
    if ($aReplied !== $bReplied) return $bReplied <=> $aReplied;
    $aDate = max((string) $a['outreach_sent_at'], (string) $a['leader_sent_at']);
    $bDate = max((string) $b['outreach_sent_at'], (string) $b['leader_sent_at']);
    return strcmp($bDate, $aDate);
});

$notYetSent = array_filter($councils, static fn($c) => empty($c['outreach_sent_at']) && empty($c['leader_sent_at']));

$total        = count($councils) ?: 32;
$sentCount    = count($sent);
$repliedCount = count($replied);
$replyRate    = $sentCount > 0 ? round(($repliedCount / $sentCount) * 100) : 0;

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Council replies</h1>
        <p>WIRES is writing to the Chief Executive and the Leader of each of Scotland's 32 councils, asking four practical questions: how staff and services support people who can't easily get online, what's being done on affordability and hardware barriers, whether services are built to survive a dropped connection without losing anyone's work, and who's named accountable for a published digital inclusion action plan. This page tracks what happens next — every reply, and every silence.</p>
        <p class="meta">This is a separate effort from our <a href="/councillor-statements">individual councillor campaign</a>, which asks councillors personally to back connectivity as essential infrastructure. This page is specifically about councils' own accountability.</p>
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

            <details class="letter-preview">
                <summary>Read the letter we sent</summary>
                <div class="letter-preview__body">
                    <p class="letter-preview__subject"><strong>Subject:</strong> <?= e($letterSubject) ?></p>
                    <pre class="letter-preview__text"><?= e($letterBody) ?></pre>
                    <p class="meta">Every Chief Executive and every Leader gets this same letter, addressed to them by name — rendered here from the exact same code that sends it, not a separate copy.</p>
                </div>
            </details>

            <?php if (empty($sent)): ?>
                <p>No letters logged as sent yet — check back soon.</p>
            <?php else: ?>
                <h2>Contacted so far</h2>
                <div class="figure-log">
                    <?php foreach ($sent as $c):
                        $ceoReplied    = !empty($c['outreach_replied_at']);
                        $leaderReplied = !empty($c['leader_replied_at']);
                        $anyReplied    = $ceoReplied || $leaderReplied;
                        ?>
                        <div class="figure-log__item">
                            <p class="figure-log__claim">
                                <?= e($c['council_area']) ?>
                                <?php if ($anyReplied): ?>
                                    <span class="pill pill--active" style="margin-left:0.5rem">Replied</span>
                                <?php else: ?>
                                    <span class="pill pill--forming" style="margin-left:0.5rem">Awaiting reply</span>
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($c['reply_summary'])): ?>
                                <p class="figure-log__note"><strong>Chief Executive:</strong> <?= e($c['reply_summary']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($c['leader_reply_summary'])): ?>
                                <p class="figure-log__note"><strong>Leader:</strong> <?= e($c['leader_reply_summary']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($c['ceo_name'])): ?>
                                <p class="figure-log__meta">
                                    <span class="figure-log__date">Chief Executive <?= e($c['ceo_name']) ?> — sent <?= e(format_date((string) $c['outreach_sent_at'])) ?></span>
                                    <?php if ($ceoReplied): ?>
                                        <span class="figure-log__date">&middot; Replied <?= e(format_date((string) $c['outreach_replied_at'])) ?></span>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($c['leader_name'])): ?>
                                <p class="figure-log__meta">
                                    <span class="figure-log__date">Leader <?= e($c['leader_name']) ?> — sent <?= e(format_date((string) $c['leader_sent_at'])) ?></span>
                                    <?php if ($leaderReplied): ?>
                                        <span class="figure-log__date">&middot; Replied <?= e(format_date((string) $c['leader_replied_at'])) ?></span>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
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
