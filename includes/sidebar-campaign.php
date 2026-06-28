<?php
/**
 * Default campaign sidebar.
 * Optional: set $sidebarSection ('policy'|'takepart'|'') to highlight related nav.
 * Optional: set $sidebarRelated = [['label'=>'...','href'=>'...'], ...]
 */
declare(strict_types=1);

$sidebarSection = $sidebarSection ?? '';
$sidebarRelated = $sidebarRelated ?? [];

// Latest news (up to 3) — graceful if DB unavailable
$sidebarNews = [];
if (db_available()) {
    try {
        $sidebarNews = db()->query(
            'SELECT title, slug, published_at FROM news_items ORDER BY published_at DESC, id DESC LIMIT 3'
        )->fetchAll();
    } catch (Throwable) {}
}
?>
<aside class="page-sidebar" aria-label="Sidebar">

    <!-- Join CTA -->
    <div class="sidebar-card sidebar-card--highlight">
        <h3>Join WIRES</h3>
        <p>Get updates on events, consultations, and local actions. Volunteer-run, no spam.</p>
        <a class="btn btn-lg" href="/join.php" style="background:#fff;color:var(--ink);width:100%;justify-content:center;text-align:center">Join the campaign</a>
    </div>

    <?php if (!empty($sidebarRelated)): ?>
    <!-- Related pages -->
    <div class="sidebar-card">
        <h3>On this section</h3>
        <ul class="sidebar-nav">
            <?php foreach ($sidebarRelated as $r): ?>
                <li><a href="<?= e($r['href']) ?>"><?= e($r['label']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Help getting online -->
    <div class="sidebar-card sidebar-card--accent">
        <h3>Need help getting online?</h3>
        <p>We list schemes and programmes that can lower your broadband costs or get you connected.</p>
        <a class="btn btn-ghost" href="/get-help.php" style="width:100%;justify-content:center;text-align:center">See all schemes</a>
    </div>

    <?php if (!empty($sidebarNews)): ?>
    <!-- Latest news -->
    <div class="sidebar-card">
        <h3>Latest from WIRES</h3>
        <ul class="sidebar-nav">
            <?php foreach ($sidebarNews as $row): ?>
                <li>
                    <a href="<?= e('/news-item.php?slug=' . rawurlencode((string) $row['slug'])) ?>">
                        <?= e((string) $row['title']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <p style="margin-top:0.75rem;margin-bottom:0"><a href="/news.php" style="font-size:0.85rem;font-weight:600">All news &rarr;</a></p>
    </div>
    <?php endif; ?>

    <!-- Local groups -->
    <div class="sidebar-card">
        <h3>Local groups</h3>
        <p>Find or start a WIRES group in your council area.</p>
        <ul class="sidebar-nav">
            <li><a href="/groups.php">Find a local group</a></li>
            <li><a href="/start-a-group.php">Start a group</a></li>
        </ul>
    </div>

</aside>
