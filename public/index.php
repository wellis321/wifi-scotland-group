<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

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

require_once dirname(__DIR__) . '/includes/header.php';
?>
<section class="hero">
    <div class="wrap hero-grid">
        <div class="hero-copy">
            <p class="hero-kicker">Scotland · digital inclusion · community networks</p>
            <h1>Internet access should be treated like the essential infrastructure it already is.</h1>
            <p class="hero-lede">
                <?= e(SITE_BRAND) ?> campaigns for dignified, affordable connectivity in homes and neighbourhoods—while tracking what Scottish Government,
                councils, and communities are doing to close the gap between policy promises and everyday life online.
            </p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="/join.php">Join the mailing list</a>
                <a class="btn btn-ghost" href="/scotland.php">Scotland policy snapshot</a>
                <a class="btn btn-ghost" href="/global-spotlight.php">Global spotlight</a>
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
            <aside class="hero-card" aria-labelledby="hero-card-title">
                <h2 id="hero-card-title">Three things we believe</h2>
                <ul class="hero-list">
                    <li><strong>Access shapes participation.</strong> Work, learning, services, and democracy increasingly assume you can get online reliably.</li>
                    <li><strong>Public money should mean public clarity.</strong> People deserve plain-language routes to schemes, vouchers, and support.</li>
                    <li><strong>Communities innovate.</strong> Cooperative and non-profit models worldwide show different ways to own and govern networks.</li>
                </ul>
            </aside>
        </div>
    </div>
</section>

<section class="cta-global" aria-labelledby="cta-global-title">
    <div class="wrap cta-global-inner">
        <div class="cta-global-copy">
            <p class="cta-global-kicker">Beyond Scotland</p>
            <h2 id="cta-global-title">Community-led connectivity matters here too</h2>
            <p class="cta-global-lede">
                Volunteers and cooperatives around the world are proving that access can be transparent, neighbourhood-scale, and governed in the public interest—not only sold as a consumer product.
                <strong> Global spotlight</strong> is where we collect those stories with links you can verify.
            </p>
        </div>
        <div class="cta-global-action">
            <a class="btn btn-primary btn-lg" href="/global-spotlight.php">Explore Global spotlight</a>
            <p class="cta-global-note">Guifi.net, NYC Mesh, Libraries Without Borders, Freifunk, and more.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="wrap">
        <h2>Why this matters now</h2>
        <p class="section-intro">
            “Digital” is not a separate life—it is how shifts are swapped, forms are submitted, and children access homework. When connectivity is unstable or unaffordable,
            the friction is social: people are excluded from ordinary expectations through no fault of their own. Our campaign exists to keep that truth visible in Scotland
            and to learn from solidarity infrastructure elsewhere.
        </p>
        <div class="card-grid cols-3">
            <article class="card card-has-image">
                <div class="card-image">
                    <img src="<?= e(image_asset('card-fibre.jpg')) ?>" width="1200" height="800" alt="" decoding="async" loading="lazy">
                </div>
                <div class="card-body">
                    <span class="pill">Scotland</span>
                    <h3>Policy you can read for yourself</h3>
                    <p>We summarise official programmes and strategies, with links back to Scottish Government and partner pages so you can verify claims and drill into detail.</p>
                    <p class="card-cta"><a class="btn btn-primary" href="/scotland.php">Scotland policy &amp; councils</a></p>
                </div>
            </article>
            <article class="card card-has-image">
                <div class="card-image">
                    <img src="<?= e(image_asset('card-community.jpg')) ?>" width="1200" height="800" alt="" decoding="async" loading="lazy">
                </div>
                <div class="card-body">
                    <span class="pill">Councils &amp; communities</span>
                    <h3>Local reality checks</h3>
                    <p>Roll-out maps and voucher schemes only work when people know they exist. We highlight practical ways to get involved and ask better questions locally.</p>
                    <p class="card-cta"><a class="btn btn-primary" href="/get-involved.php">Get involved</a></p>
                </div>
            </article>
            <article class="card card-has-image">
                <div class="card-image">
                    <img src="<?= e(image_asset('card-global-network.jpg')) ?>" width="1200" height="800" alt="" decoding="async" loading="lazy">
                </div>
                <div class="card-body">
                    <span class="pill">Worldwide</span>
                    <h3>Global spotlight</h3>
                    <p>From mesh volunteers to mobile cultural centres, we showcase projects that treat connectivity as shared infrastructure—not only a consumer product.</p>
                    <p class="card-cta"><a class="btn btn-primary" href="/global-spotlight.php">Read Global spotlight</a></p>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="section alt">
    <div class="wrap">
        <h2>Latest updates</h2>
        <p class="section-intro">Short editorial notes from the campaign. For official statistics and programme updates, always check the sources linked on our Scotland and Resources pages.</p>
        <?php if ($latestNews): ?>
            <div class="news-list">
                <?php foreach ($latestNews as $row): ?>
                    <article>
                        <p class="meta"><?= e((string) $row['published_at']) ?></p>
                        <h2><a href="<?= e('/news-item.php?slug=' . rawurlencode((string) $row['slug'])) ?>"><?= e((string) $row['title']) ?></a></h2>
                        <?php if (!empty($row['summary'])): ?>
                            <p><?= e((string) $row['summary']) ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
            <p><a class="btn btn-ghost" href="/news.php">All news</a></p>
        <?php else: ?>
            <div class="callout">
                <p><strong>Database not connected.</strong> Import <code>schema.sql</code> and set your database credentials in <code>.env</code> (see <code>.env.example</code> and the README) to show starter articles here.</p>
            </div>
            <p><a class="btn btn-ghost" href="/news.php">News page</a></p>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="wrap prose">
        <h2>Independent, evidence-friendly, action-oriented</h2>
        <p>
            <?= e(SITE_BRAND) ?> is a campaigning site in the spirit of modern civic organisations: fast to read, serious about sources, and oriented toward what people can do next.
            We are not a government service—when you need eligibility checks for vouchers or faults with your line, official channels remain authoritative.
        </p>
        <p>
            If you want to help shape this project—writing, design, mapping council meetings, or translating jargon into community FAQs—start on
            <a href="/get-involved.php">Get involved</a> and say hello via <a href="/contact.php">Contact</a>.
        </p>
    </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
