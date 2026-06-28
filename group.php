<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
$group = null;
$events = [];
$localNews = [];

if ($slug !== '' && db_available()) {
    try {
        $stmt = db()->prepare(
            'SELECT * FROM local_groups WHERE slug = :slug LIMIT 1'
        );
        $stmt->execute(['slug' => $slug]);
        $group = $stmt->fetch() ?: null;

        if ($group) {
            $evStmt = db()->prepare(
                'SELECT * FROM group_events WHERE group_id = :gid AND event_date >= CURDATE() ORDER BY event_date ASC LIMIT 10'
            );
            $evStmt->execute(['gid' => $group['id']]);
            $events = $evStmt->fetchAll();

            $newsStmt = db()->prepare(
                'SELECT title, slug, summary, published_at FROM news_items WHERE group_id = :gid ORDER BY published_at DESC LIMIT 5'
            );
            $newsStmt->execute(['gid' => $group['id']]);
            $localNews = $newsStmt->fetchAll();
        }
    } catch (Throwable) {
        $group = null;
    }
}

if (!$group) {
    http_response_code(404);
    $pageTitle = 'Group not found';
    $pageDescription = 'That local group could not be found.';
    $currentNav = 'groups';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <header class="page-header">
        <div class="wrap">
            <h1>Group not found</h1>
            <p>We could not find that local group. It may not exist yet, or the database may not be connected.</p>
        </div>
    </header>
    <div class="section">
        <div class="wrap prose">
            <p><a href="/groups.php">&larr; All local groups</a></p>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    return;
}

$statusLabel = match($group['status']) {
    'active'            => 'Active',
    'forming'           => 'Forming',
    'seeking_organiser' => 'Needs an organiser',
    default             => ''
};
$statusClass = match($group['status']) {
    'active'            => 'pill pill--active',
    'seeking_organiser' => 'pill pill--seeking',
    default             => 'pill pill--forming'
};

$pageTitle = e($group['council_area']) . ' — Local group';
$pageDescription = !empty($group['tagline'])
    ? (string) $group['tagline']
    : 'WIRES local group for ' . (string) $group['council_area'] . '.';
$currentNav = 'groups';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <p class="meta"><a href="/groups.php">&larr; All local groups</a></p>
        <span class="<?= e($statusClass) ?>"><?= e($statusLabel) ?></span>
        <h1><?= e((string) $group['council_area']) ?></h1>
        <?php if (!empty($group['tagline'])): ?>
            <p><?= e((string) $group['tagline']) ?></p>
        <?php endif; ?>
    </div>
</header>

<?php if (!empty($group['description'])): ?>
<div class="section">
    <div class="wrap prose">
        <?= $group['description'] /* trusted HTML — admin-authored only */ ?>
    </div>
</div>
<?php endif; ?>

<?php if ($events): ?>
<div class="section alt" aria-labelledby="events-heading">
    <div class="wrap">
        <h2 id="events-heading">Upcoming events</h2>
        <ul class="event-list">
            <?php foreach ($events as $ev): ?>
                <?php
                $ts = strtotime((string) $ev['event_date']);
                $day = $ts !== false ? date('j', $ts) : '';
                $month = $ts !== false ? date('M', $ts) : '';
                ?>
                <li class="event-item">
                    <div class="event-date-badge" aria-hidden="true">
                        <span class="event-day"><?= e($day) ?></span>
                        <span class="event-month"><?= e($month) ?></span>
                    </div>
                    <div class="event-body">
                        <h3><?= e((string) $ev['title']) ?></h3>
                        <?php if (!empty($ev['event_time']) || !empty($ev['location_text'])): ?>
                            <p>
                                <?php if (!empty($ev['event_time'])): ?>
                                    <time datetime="<?= e((string) $ev['event_date']) ?>"><?= e(format_date((string) $ev['event_date'])) ?>, <?= e((string) $ev['event_time']) ?></time>
                                <?php else: ?>
                                    <time datetime="<?= e((string) $ev['event_date']) ?>"><?= e(format_date((string) $ev['event_date'])) ?></time>
                                <?php endif; ?>
                                <?php if (!empty($ev['location_text'])): ?>
                                    &mdash; <?= e((string) $ev['location_text']) ?>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($ev['description'])): ?>
                            <p><?= e((string) $ev['description']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($ev['online_url'])): ?>
                            <a href="<?= e((string) $ev['online_url']) ?>"<?= external_link_attrs((string) $ev['online_url']) ?>>Join online</a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php else: ?>
<div class="section alt">
    <div class="wrap">
        <h2>Upcoming events</h2>
        <p class="section-intro">No events listed yet. <a href="/contact.php">Get in touch</a> if you'd like to suggest or publicise one.</p>
    </div>
</div>
<?php endif; ?>

<?php if ($localNews): ?>
<div class="section" aria-labelledby="local-news-heading">
    <div class="wrap">
        <h2 id="local-news-heading">Local news &amp; updates</h2>
        <div class="news-list">
            <?php foreach ($localNews as $row): ?>
                <article>
                    <p class="meta"><time datetime="<?= e((string) $row['published_at']) ?>"><?= e(format_date((string) $row['published_at'])) ?></time></p>
                    <h2><a href="<?= e('/news-item.php?slug=' . rawurlencode((string) $row['slug'])) ?>"><?= e((string) $row['title']) ?></a></h2>
                    <?php if (!empty($row['summary'])): ?>
                        <p><?= e((string) $row['summary']) ?></p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="section">
    <div class="wrap">
        <div class="group-help-callout">
            <h2>Help getting online in <?= e((string) $group['council_area']) ?></h2>
            <p>There are national schemes that can help people in this area get connected or pay less—including social tariffs for people on benefits, free SIM cards, and rural broadband programmes.</p>
            <a class="btn btn-primary" href="/get-help.php">See all schemes &rarr;</a>
        </div>
    </div>
</div>

<div class="section" aria-labelledby="contact-heading">
    <div class="wrap prose">
        <h2 id="contact-heading">Get in touch with this group</h2>
        <?php if (!empty($group['contact_name']) || !empty($group['contact_email'])): ?>
            <div class="group-meta">
                <?php if (!empty($group['contact_name'])): ?>
                    <span><strong>Local contact:</strong> <?= e((string) $group['contact_name']) ?></span>
                <?php endif; ?>
                <?php if (!empty($group['contact_email'])): ?>
                    <span><a href="mailto:<?= e((string) $group['contact_email']) ?>"><?= e((string) $group['contact_email']) ?></a></span>
                <?php endif; ?>
                <?php if (!empty($group['social_url'])): ?>
                    <span><a href="<?= e((string) $group['social_url']) ?>"<?= external_link_attrs((string) $group['social_url']) ?>>Group social page</a></span>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>This group is still getting started. Use the main contact form below and mention <?= e((string) $group['council_area']) ?> in your message—we'll pass it on.</p>
        <?php endif; ?>
        <p>
            <a class="btn btn-primary" href="/contact.php">Contact via the campaign</a>
            <a class="btn btn-ghost" href="/start-a-group.php">About starting a group</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
