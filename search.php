<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$q       = trim((string) ($_GET['q'] ?? ''));
$results = [];
$dbOk    = false;

if ($q !== '' && strlen($q) >= 2) {
    /* Search news articles */
    if (db_available()) {
        try {
            $dbOk = true;
            $stmt = db()->prepare(
                'SELECT title, slug, summary, published_at
                 FROM news_items
                 WHERE title LIKE :q OR summary LIKE :q
                 ORDER BY published_at DESC
                 LIMIT 20'
            );
            $stmt->execute(['q' => '%' . $q . '%']);
            foreach ($stmt->fetchAll() as $row) {
                $results[] = [
                    'title'   => (string) $row['title'],
                    'url'     => '/news-item?slug=' . rawurlencode((string) $row['slug']),
                    'excerpt' => (string) ($row['summary'] ?? ''),
                    'type'    => 'News',
                ];
            }
        } catch (Throwable) {}
    }

    /* Search static pages by keyword match */
    $pages = [
        ['title' => 'Help getting online',               'url' => '/get-help',        'excerpt' => 'Schemes and programmes that can help people in Scotland get connected or pay less for broadband.', 'keywords' => ['help','broadband','schemes','social tariff','online','cost']],
        ['title' => 'Why home connectivity matters',     'url' => '/why-it-matters',  'excerpt' => 'Evidence on why reliable home internet matters and who is most affected by digital exclusion.', 'keywords' => ['evidence','exclusion','digital','why','matters','connectivity']],
        ['title' => 'Digital exclusion and health',      'url' => '/digital-health',  'excerpt' => 'How being offline affects physical and mental health — WHO evidence, COVID-19 research, NHS data.', 'keywords' => ['health','wellbeing','who','nhs','covid','mental','loneliness']],
        ['title' => 'Beyond broadband',                  'url' => '/beyond-broadband','excerpt' => 'Devices, design, authentication, digital skills, and language — the full stack of exclusion.', 'keywords' => ['devices','skills','language','design','authentication','barriers']],
        ['title' => 'Scotland policy and programmes',    'url' => '/scotland',        'excerpt' => 'Scottish Government connectivity programmes, R100, local authority action, and Glasgow\'s Digital Housing Strategy showing 65% of social rented households don\'t use home broadband.', 'keywords' => ['scotland','policy','r100','government','council','programme','glasgow','housing','digital strategy']],
        ['title' => 'Who is acting on digital exclusion?','url' => '/accountability', 'excerpt' => 'Which Scottish councils — including Glasgow — have a digital inclusion strategy, and which bodies are not delivering.', 'keywords' => ['council','accountability','strategy','acting','tracker','glasgow','cosla','ofcom','regulator']],
        ['title' => 'Scottish connectivity stories',     'url' => '/scotland-stories','excerpt' => 'Eight evidence-led Scottish stories including Glasgow\'s Digital Housing Strategy, R100, library Wi‑Fi, and community broadband projects, with links to primary sources.', 'keywords' => ['scotland','stories','evidence','community','broadband','glasgow','housing','library']],
        ['title' => 'Homelessness and digital exclusion','url' => '/why-it-matters#homelessness', 'excerpt' => 'People without a fixed address face specific barriers that most schemes cannot help with. Glasgow has Scotland\'s highest rate of homelessness.', 'keywords' => ['homeless','temporary','address','housing','shelter','glasgow']],
        ['title' => 'Write to your councillor',          'url' => '/write-to-councillor','excerpt' => 'A ready-to-use template letter about broadband access for your local councillor.', 'keywords' => ['councillor','letter','council','write','template','contact']],
        ['title' => 'Local groups',                      'url' => '/groups',          'excerpt' => 'Find or start a WIRES local group in your council area.', 'keywords' => ['group','local','community','start','area']],
        ['title' => 'Why WIRES exists',                  'url' => '/landscape',       'excerpt' => 'How WIRES is different from other organisations working on digital inclusion in Scotland.', 'keywords' => ['wires','different','landscape','scvo','organisation','why']],
        ['title' => 'Get involved',                      'url' => '/get-involved',    'excerpt' => 'Practical ways to take action on connectivity in your community.', 'keywords' => ['involved','action','volunteer','campaign','community']],
        ['title' => 'Join WIRES',                        'url' => '/join',            'excerpt' => 'Sign up to hear about events, consultations, and ways to take action.', 'keywords' => ['join','sign up','mailing list','member']],
        ['title' => 'Organisational supporters',         'url' => '/supporters',      'excerpt' => 'Organisations that have publicly committed to supporting WIRES.', 'keywords' => ['supporters','organisations','badge','support']],
        ['title' => 'Sign up your organisation',         'url' => '/join-as-organisation','excerpt' => 'Publicly commit to web infrastructure rights and get supporter assets.', 'keywords' => ['organisation','sign up','badge','supporter']],
        ['title' => 'Global spotlight',                  'url' => '/global-spotlight','excerpt' => 'International community network examples — NYC Mesh, Guifi.net, B4RN and more.', 'keywords' => ['global','international','community','network','mesh','b4rn']],
        ['title' => 'Scottish connectivity stories',     'url' => '/scotland-stories','excerpt' => 'Eight evidence-led Scottish stories with links to primary sources.', 'keywords' => ['scotland','stories','evidence','community','broadband']],
        ['title' => 'Resources and references',          'url' => '/resources',       'excerpt' => 'Primary sources and official links cited across the WIRES campaign.', 'keywords' => ['resources','references','sources','ofcom','ons']],
    ];

    $qLower = strtolower($q);
    foreach ($pages as $page) {
        $matched = str_contains(strtolower($page['title']),   $qLower)
                || str_contains(strtolower($page['excerpt']), $qLower);

        if (!$matched) {
            foreach ($page['keywords'] as $kw) {
                if (str_contains($qLower, $kw) || str_contains($kw, $qLower)) {
                    $matched = true;
                    break;
                }
            }
        }

        if ($matched) {
            $results[] = [
                'title'   => $page['title'],
                'url'     => $page['url'],
                'excerpt' => $page['excerpt'],
                'type'    => 'Page',
            ];
        }
    }
}

$pageTitle       = $q !== '' ? 'Search results for "' . $q . '"' : 'Search';
$pageDescription = 'Search the WIRES website for information about digital inclusion, broadband access, and connectivity rights in Scotland.';
$currentNav      = '';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Search WIRES</h1>
    </div>
</header>

<div class="section">
    <div class="wrap" style="max-width:760px">

        <form action="/search" method="get" role="search">
            <div class="form-row" style="display:flex;gap:0.75rem">
                <input id="q" name="q" type="search" required
                       value="<?= e($q) ?>"
                       placeholder="e.g. social tariffs, Glasgow, health..."
                       aria-label="Search WIRES"
                       autocomplete="off"
                       style="flex:1">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>

        <?php if ($q !== '' && strlen($q) < 2): ?>
            <p style="margin-top:1.5rem;color:var(--muted)">Please enter at least two characters.</p>

        <?php elseif ($q !== '' && empty($results)): ?>
            <p style="margin-top:1.5rem">No results found for <strong><?= e($q) ?></strong>.</p>
            <p style="color:var(--muted)">Try a shorter or different term, or <a href="/contact">contact us</a> if you can't find what you need.</p>

        <?php elseif (!empty($results)): ?>
            <p class="meta" style="margin-top:1.5rem"><?= count($results) ?> result<?= count($results) !== 1 ? 's' : '' ?> for <strong><?= e($q) ?></strong></p>

            <div class="news-list" style="margin-top:1rem">
                <?php foreach ($results as $r): ?>
                <article>
                    <span class="pill pill--forming" style="margin-bottom:0.35rem"><?= e($r['type']) ?></span>
                    <h2><a href="<?= e($r['url']) ?>"><?= e($r['title']) ?></a></h2>
                    <?php if (!empty($r['excerpt'])): ?>
                        <p><?= e($r['excerpt']) ?></p>
                    <?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>

        <?php elseif ($q === ''): ?>
            <div style="margin-top:2rem">
                <p style="color:var(--muted)">Enter a search term above to find pages, articles, and resources on the WIRES site.</p>
                <p style="color:var(--muted)">Looking for something specific? Try: <a href="/get-help">help getting online</a>, <a href="/why-it-matters">why it matters</a>, <a href="/scotland">Scotland policy</a>, or <a href="/news">news</a>.</p>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
