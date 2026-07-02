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

$pageOgImage    = image_asset('hero-laptop-home.jpg');
$pageOgImageAlt = 'Person using a laptop in a home workspace—illustrating everyday reliance on connectivity.';

/* Preload the hero image so it's the LCP candidate the browser fetches immediately */
$pageExtraHead = '<link rel="preload" href="/images/hero-laptop-home.jpg" as="image">';

require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="wrap hero-grid">
        <div class="hero-copy">
            <p class="hero-kicker">Web Infrastructure Rights for Everyone in Scotland</p>
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
                <picture>
                    <source srcset="/images/hero-laptop-home.webp" type="image/webp">
                    <img
                        src="<?= e(image_asset('hero-laptop-home.jpg')) ?>"
                        width="1600"
                        height="1067"
                        alt="<?= e($pageOgImageAlt) ?>"
                        decoding="sync"
                        fetchpriority="high"
                        class="hero-media-img"
                    >
                </picture>
            </figure>
        </div>
    </div>
</section>

<section class="statement-band" aria-label="Campaign statement">
    <div class="wrap">
        <div class="statement-band-inner">
            <p class="statement-band-quote">
                "When you can't get online, you can't fully participate.<br>
                That's not <strong>personal failure</strong>&nbsp;— it's a <strong>policy failure</strong>."
            </p>
            <div>
                <a class="btn btn-outline-light btn-lg" href="/why-it-matters.php">See the evidence</a>
            </div>
        </div>
        <div class="statement-band-stats" role="region" aria-label="Key statistics">
            <div class="statement-stat stat-anim">
                <span class="statement-stat__value" data-count-target="12" data-count-prefix="1 in ">1 in 12</span>
                <span class="statement-stat__label">of qualifying households ever use the social tariff they're owed</span>
            </div>
            <div class="statement-stat stat-anim">
                <span class="statement-stat__value" data-count-target="6.2" data-count-suffix="m" data-count-decimals="1">6.2m</span>
                <span class="statement-stat__label">UK households on Universal Credit qualify for a social tariff right now</span>
            </div>
            <div class="statement-stat stat-anim">
                <span class="statement-stat__value" data-count-target="55" data-count-suffix="%">55%</span>
                <span class="statement-stat__label">of people on benefits have never heard that social tariffs exist</span>
            </div>
        </div>
        <p class="statement-band-source">Source: Ofcom 2024–25 &middot; <a href="/get-help.php">Find out if you qualify</a></p>
    </div>
</section>

<section class="audit-feature" aria-labelledby="audit-feature-heading">
    <div class="wrap audit-feature-inner">
        <div class="audit-feature-content">
            <p class="audit-feature-kicker">
                <span class="audit-feature-badge">Audit Scotland · August 2024</span>
            </p>
            <h2 id="audit-feature-heading" class="audit-feature-heading">Scotland's "No-One Left Behind" commitment has no action plan and no named leader</h2>
            <p class="audit-feature-body">
                Scotland's own public spending watchdog examined whether the national digital inclusion commitment is being delivered. It found no clear plan, unclear accountability, and momentum stalling since the pandemic. It called for a response by end of 2024/25. None has been published.
            </p>
            <div class="audit-feature-actions">
                <a class="btn btn-primary btn-sm" href="/write-to-councillor#cosla-letter">Write to COSLA &rarr;</a>
                <a class="btn btn-ghost btn-sm" href="/accountability">Accountability tracker</a>
                <a class="btn btn-ghost btn-sm" href="https://audit.scot/uploads/2024-08/nr_240822_tackling_digital_exclusion.pdf" target="_blank" rel="noopener noreferrer">Audit Scotland report (PDF)</a>
            </div>
        </div>
        <div class="audit-feature-aside" aria-hidden="true">
            <p class="audit-feature-quote">"No clear action plan exists for reducing digital exclusion — and it is unclear who is responsible."</p>
            <p class="audit-feature-cite">Audit Scotland, <em>Tackling Digital Exclusion</em></p>
        </div>
    </div>
</section>

<section class="paths-section" aria-labelledby="paths-heading">
    <div class="wrap">
        <p class="paths-kicker">Find your way in</p>
        <h2 id="paths-heading" class="paths-heading">This campaign serves everyone affected</h2>
        <div class="paths-grid" role="list">
            <a class="path-card fade-up" href="/why-it-matters.php" role="listitem" data-delay="0">
                <p class="path-label">Residents</p>
                <h3 class="path-heading">Understand your options</h3>
                <p class="path-body">Learn what schemes exist, what you're entitled to, and how poor connectivity harms people through no fault of their own.</p>
                <span class="path-link" aria-hidden="true">Why it matters →</span>
            </a>
            <a class="path-card fade-up" href="/get-involved.php" role="listitem" data-delay="80">
                <p class="path-label">Organisers</p>
                <h3 class="path-heading">Get active with us</h3>
                <p class="path-body">Find practical ways to campaign, raise questions at council level, and connect with others working on digital inclusion in Scotland.</p>
                <span class="path-link" aria-hidden="true">Get involved →</span>
            </a>
            <a class="path-card fade-up" href="/scotland.php" role="listitem" data-delay="160">
                <p class="path-label">Researchers &amp; press</p>
                <h3 class="path-heading">Verify the evidence</h3>
                <p class="path-body">Policy documents, official programmes, and cited sources—everything linked so you can check claims and go deeper.</p>
                <span class="path-link" aria-hidden="true">Scotland policy →</span>
            </a>
        </div>
    </div>
</section>

<section class="arguments-section" aria-labelledby="arguments-heading">
    <div class="wrap">
        <h2 id="arguments-heading">Our case for change</h2>
        <ol class="arguments-list">
            <li class="argument-item fade-up">
                <div class="argument-text">
                    <span class="argument-number" aria-hidden="true">01</span>
                    <h3>Access shapes participation</h3>
                    <p>Work, learning, health services, and democracy increasingly assume you can get online reliably. When connectivity is unstable or unaffordable, people are excluded from ordinary expectations—not through personal failure, but structural neglect.</p>
                </div>
                <figure class="argument-image" aria-hidden="true">
                    <img src="<?= e(image_asset('card-community.jpg')) ?>" width="1200" height="800" alt="" decoding="async" loading="lazy">
                </figure>
            </li>
            <li class="argument-item fade-up">
                <div class="argument-text">
                    <span class="argument-number" aria-hidden="true">02</span>
                    <h3>Public money should mean public clarity</h3>
                    <p>Significant public investment flows into broadband and digital inclusion programmes. People deserve a clear path to the help that exists—not complicated applications, confusing criteria, or announcements buried so deep that the people who need them most never hear about them.</p>
                </div>
                <figure class="argument-image" aria-hidden="true">
                    <img src="<?= e(image_asset('glasses-clarity.jpg')) ?>" width="1200" height="956" alt="" decoding="async" loading="lazy">
                </figure>
            </li>
            <li class="argument-item fade-up">
                <div class="argument-text">
                    <span class="argument-number" aria-hidden="true">03</span>
                    <h3>Communities show a different way is possible</h3>
                    <p>Cooperative and non-profit network projects worldwide—from Guifi.net in Catalonia to NYC Mesh—demonstrate that connectivity can be transparent, neighbourhood-scale, and governed in the public interest. Scotland can learn from these models.</p>
                </div>
                <figure class="argument-image" aria-hidden="true">
                    <img src="<?= e(image_asset('about-team.jpg')) ?>" width="1200" height="800" alt="" decoding="async" loading="lazy">
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
