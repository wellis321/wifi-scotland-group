<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Connectivity as a public good';
$pageDescription = 'A Scotland-rooted campaign for affordable, reliable internet access—backed by evidence and community action.';
$currentNav = 'home';

$latestNews = [];
if (db_available()) {
    try {
        $stmt = db()->query(
            'SELECT title, slug, summary, published_at FROM news_items ORDER BY published_at DESC, id DESC LIMIT 3'
        );
        $latestNews = $stmt->fetchAll();
    } catch (Throwable) {
        $latestNews = [];
    }
}

$pageOgImage = image_asset('hero-laptop-home.jpg');
$pageOgImageAlt = 'Person using a laptop in a home workspace—illustrating everyday reliance on connectivity.';

require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="wrap hero-grid">
        <div class="hero-copy">
            <p class="hero-kicker">Scotland · digital rights · community networks</p>
            <h1>Internet access should be treated like the essential infrastructure it already is.</h1>
            <p class="hero-lede">
                <?= e(SITE_BRAND) ?> campaigns for dignified, affordable connectivity in homes and
                neighbourhoods—tracking what Scottish Government, councils, and communities are
                doing to close the gap between policy promises and everyday life online.
            </p>
            <div class="hero-actions">
                <a class="btn btn-primary btn-lg" href="/join.php">Join the campaign</a>
                <a class="btn btn-ghost" href="/why-it-matters.php">Why it matters</a>
            </div>
        </div>
        <div class="hero-side">
            <figure class="hero-media">
                <img
                    src="<?= e(image_asset('hero-laptop-home.jpg')) ?>"
                    width="1600"
                    height="1067"
                    alt="<?= e($pageOgImageAlt) ?>"
                    decoding="async"
                    fetchpriority="high"
                    class="hero-media-img"
                >
            </figure>
        </div>
    </div>
</section>

<section class="statement-band" aria-label="Campaign statement">
    <div class="wrap statement-band-inner">
        <p class="statement-band-quote">
            "When you can't get online, you can't fully participate.<br>
            That's not <strong>personal failure</strong>&nbsp;— it's a <strong>policy failure</strong>."
        </p>
        <div>
            <a class="btn btn-outline-light btn-lg" href="/why-it-matters.php">See the evidence</a>
        </div>
    </div>
</section>

<section class="paths-section" aria-labelledby="paths-heading">
    <div class="wrap">
        <p class="paths-kicker">Find your way in</p>
        <h2 id="paths-heading" class="paths-heading">This campaign serves everyone affected</h2>
        <div class="paths-grid" role="list">
            <div class="path-card" role="listitem">
                <p class="path-label">Residents</p>
                <h3 class="path-heading">Understand your options</h3>
                <p class="path-body">Learn what schemes exist, what you're entitled to, and how poor connectivity harms people through no fault of their own.</p>
                <a class="path-link" href="/why-it-matters.php">Why it matters</a>
            </div>
            <div class="path-card" role="listitem">
                <p class="path-label">Organisers</p>
                <h3 class="path-heading">Get active with us</h3>
                <p class="path-body">Find practical ways to campaign, raise questions at council level, and connect with others working on digital inclusion in Scotland.</p>
                <a class="path-link" href="/get-involved.php">Get involved</a>
            </div>
            <div class="path-card" role="listitem">
                <p class="path-label">Researchers &amp; press</p>
                <h3 class="path-heading">Verify the evidence</h3>
                <p class="path-body">Policy documents, official programmes, and cited sources—everything linked so you can check claims and go deeper.</p>
                <a class="path-link" href="/scotland.php">Scotland policy</a>
            </div>
        </div>
    </div>
</section>

<section class="arguments-section" aria-labelledby="arguments-heading">
    <div class="wrap">
        <h2 id="arguments-heading">What we argue</h2>
        <ol class="arguments-list">
            <li class="argument-item">
                <div class="argument-text">
                    <span class="argument-number" aria-hidden="true">01</span>
                    <h3>Access shapes participation</h3>
                    <p>Work, learning, health services, and democracy increasingly assume you can get online reliably. When connectivity is unstable or unaffordable, people are excluded from ordinary expectations—not through personal failure, but structural neglect.</p>
                </div>
                <figure class="argument-image" aria-hidden="true">
                    <img src="<?= e(image_asset('card-community.jpg')) ?>" width="1200" height="800" alt="" decoding="async" loading="lazy">
                </figure>
            </li>
            <li class="argument-item">
                <div class="argument-text">
                    <span class="argument-number" aria-hidden="true">02</span>
                    <h3>Public money should mean public clarity</h3>
                    <p>Significant public investment flows into broadband and digital inclusion programmes. People deserve a clear path to the help that exists—not complicated applications, confusing criteria, or announcements buried so deep that the people who need them most never hear about them.</p>
                </div>
                <figure class="argument-image" aria-hidden="true">
                    <img src="<?= e(image_asset('scotland-landscape.jpg')) ?>" width="1200" height="800" alt="" decoding="async" loading="lazy">
                </figure>
            </li>
            <li class="argument-item">
                <div class="argument-text">
                    <span class="argument-number" aria-hidden="true">03</span>
                    <h3>Communities show a different way is possible</h3>
                    <p>Cooperative and non-profit network projects worldwide—from Guifi.net in Catalonia to NYC Mesh—demonstrate that connectivity can be transparent, neighbourhood-scale, and governed in the public interest. Scotland can learn from these models.</p>
                </div>
                <figure class="argument-image" aria-hidden="true">
                    <img src="<?= e(image_asset('card-global-network.jpg')) ?>" width="1200" height="800" alt="" decoding="async" loading="lazy">
                </figure>
            </li>
        </ol>
    </div>
</section>

<section class="section alt" aria-labelledby="news-heading">
    <div class="wrap">
        <h2 id="news-heading">Latest from the campaign</h2>
        <p class="section-intro">Editorial notes and pointers to official updates. For primary statistics, always check the sources linked on our Scotland and Resources pages.</p>
        <?php if ($latestNews): ?>
            <div class="home-news-grid">
                <?php foreach ($latestNews as $row): ?>
                    <article class="home-news-item">
                        <p class="meta"><time datetime="<?= e((string) $row['published_at']) ?>"><?= e(format_date((string) $row['published_at'])) ?></time></p>
                        <h3><a href="<?= e('/news-item.php?slug=' . rawurlencode((string) $row['slug'])) ?>"><?= e((string) $row['title']) ?></a></h3>
                        <?php if (!empty($row['summary'])): ?>
                            <p><?= e((string) $row['summary']) ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
            <p style="margin-top: 1.75rem"><a class="btn btn-ghost" href="/news.php">All news &rarr;</a></p>
        <?php else: ?>
            <div class="callout">
                <p><strong>Database not connected.</strong> Import <code>schema.sql</code> and configure <code>.env</code> to show starter articles here.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="join-band" aria-labelledby="join-band-heading">
    <div class="wrap join-band-inner">
        <div>
            <h2 id="join-band-heading">Ready to get involved?</h2>
            <p>Join the mailing list and we'll let you know about events, consultations, and ways to take action. No spam—this is a volunteer campaign.</p>
        </div>
        <a class="btn btn-lg" href="/join.php">Join the campaign &rarr;</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
