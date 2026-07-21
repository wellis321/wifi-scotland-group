<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Global spotlight';
$pageDescription = 'Positive international examples of community connectivity — from mesh networks to mobile cultural centres.';
$currentNav      = 'global';

$pageOgImage    = image_asset('card-global-network.jpg');
$pageOgImageAlt = 'Illuminated view of Earth from space — symbolising global networks and shared infrastructure.';

$sidebarRelated = [
    ['href' => '/scotland.php',      'label' => 'Scotland policy'],
    ['href' => '/why-it-matters.php','label' => 'Why it matters'],
    ['href' => '/resources.php',     'label' => 'Resources & references'],
];

$projects = [
    [
        'name'    => 'NYC Mesh',
        'sub'     => 'Volunteer-led mesh, New York City',
        'img'     => 'card-global-network.jpg',
        'imgAlt'  => 'Digital network globe — representing NYC Mesh\'s distributed model.',
        'body'    => 'A non-profit, volunteer-driven network building neighbourhood links across New York City. Their public materials emphasise community ownership, donations rather than extractive pricing, and a documented approach to extending the mesh responsibly.',
        'url'     => 'https://www.nycmesh.net/',
        'urlLabel'=> 'nycmesh.net',
    ],
    [
        'name'    => 'Libraries Without Borders',
        'sub'     => 'Ideas Box — offline-first tools',
        'img'     => 'card-community.jpg',
        'imgAlt'  => 'Community group around a table — representing the Ideas Box facilitated model.',
        'body'    => 'Bibliothèques Sans Frontières deploys Ideas Box mobile cultural and learning spaces in humanitarian and underserved contexts, combining satellite connectivity with books, devices, and facilitation. They also develop offline-first approaches for places where continuous internet is unrealistic.',
        'url'     => 'https://www.librarieswithoutborders.org/ideasbox/',
        'urlLabel'=> 'Ideas Box (Libraries Without Borders)',
    ],
    [
        'name'    => 'Freifunk',
        'sub'     => 'Decentralised community wireless, Germany',
        'img'     => 'card-fibre.jpg',
        'imgAlt'  => 'Fibre-optic cables — representing Freifunk\'s community-built infrastructure.',
        'body'    => 'The Freifunk movement supports autonomous community wireless networks with a strong emphasis on political education alongside technical build-out. Individual communities publish their own nodes and policies — treat Freifunk as a family of projects rather than one centralised organisation.',
        'url'     => 'https://freifunk.net/en/',
        'urlLabel'=> 'freifunk.net',
    ],
    [
        'name'    => 'Guifi.net',
        'sub'     => 'Community network, Catalonia and beyond',
        'img'     => 'about-team.jpg',
        'imgAlt'  => 'People collaborating — representing Guifi.net\'s user-governed community model.',
        'body'    => 'A long-running, user-governed network often cited in research on community infrastructure. It grew from wireless links in rural Catalonia into a broader commons-based model with clear governance documentation and a foundation supporting operations. A useful reference when asking what "open and neutral" network commitments can look like in practice.',
        'url'     => 'https://guifi.net/',
        'urlLabel'=> 'guifi.net',
    ],
];

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Global spotlight</h1>
        <p>Scotland does not have a monopoly on good ideas. These projects show what it looks like when communities own and run their own networks — built around people, not profit.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="page-layout" style="padding-top:0">

        <div><!-- main column -->
            <img class="page-hero-img" src="<?= e(image_asset('card-global-network.jpg')) ?>" width="1400" height="933"
                 alt="Digital globe visual suggesting worldwide data networks." decoding="async" loading="lazy">

            <div class="pull-quote">
                <p>"Community networks worldwide show that connectivity can be designed around participation — not only consumption."</p>
                <cite>WIRES campaign position</cite>
            </div>

            <div class="card-grid cols-2" style="margin-top:2rem">
                <?php foreach ($projects as $p): ?>
                <article class="icon-card">
                    <div class="icon-card-img">
                        <img src="<?= e(image_asset($p['img'])) ?>" width="1200" height="800"
                             alt="<?= e($p['imgAlt']) ?>" decoding="async" loading="lazy">
                    </div>
                    <div class="icon-card-body">
                        <span class="pill"><?= e($p['sub']) ?></span>
                        <h2><?= e($p['name']) ?></h2>
                        <p><?= e($p['body']) ?></p>
                        <p style="margin-top:auto;padding-top:0.75rem">
                            <a class="btn btn-primary" href="<?= e($p['url']) ?>"<?= external_link_attrs($p['url']) ?>>
                                <?= e($p['urlLabel']) ?> &rarr;
                            </a>
                        </p>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <div class="callout" style="margin-top:2.5rem">
                <p class="callout__eyebrow">Attribution</p>
                <p>We highlight these projects because their documentation is public and their aims align with our values. Listing here is not an exhaustive survey of the field — suggest additions via <a href="/contact.php">Contact</a>.</p>
            </div>

            <h2 id="scotland-networks" style="margin-top:3.5rem">Closer to home: Scotland</h2>
            <p>The models above are global proof points. But Scotland already has community-owned and community-managed networks of its own — evidence that the alternative is not hypothetical.</p>

            <div class="community-net-list">

                <div class="community-net-item">
                    <div class="community-net-meta">
                        <span class="pill">Small Isles &amp; Knoydart</span>
                    </div>
                    <h3 class="community-net-name">HebNet</h3>
                    <p>Community internet service providing superfast broadband to Canna, Rum, Eigg, Muck, and Knoydart — some of Scotland's most remote communities — via microwave and FTTP. A genuinely community-managed network, not a commercial provider serving a difficult market.</p>
                    <a class="community-net-link" href="https://www.hebnet.co.uk/"<?= external_link_attrs('https://www.hebnet.co.uk/') ?>>hebnet.co.uk &rarr;</a>
                </div>

                <div class="community-net-item">
                    <div class="community-net-meta">
                        <span class="pill">Inner Hebrides</span>
                    </div>
                    <h3 class="community-net-name">GigaPlus Argyll</h3>
                    <p>Community-owned infrastructure serving Colonsay, Mull, Iona, Jura, and neighbouring islands, developed with Community Broadband Scotland funding from Highlands and Islands Enterprise. Demonstrates that islands left behind by commercial roll-out can build their own solution.</p>
                    <a class="community-net-link" href="https://www.hie.co.uk/our-work/projects-and-research/connecting-our-communities/"<?= external_link_attrs('https://www.hie.co.uk/our-work/projects-and-research/connecting-our-communities/') ?>>Highlands and Islands Enterprise: community connectivity &rarr;</a>
                </div>

                <div class="community-net-item">
                    <div class="community-net-meta">
                        <span class="pill">Highland</span>
                        <span class="pill pill--seeking" style="margin-left:0.4rem">Closed April 2026</span>
                    </div>
                    <h3 class="community-net-name">Highland Community Broadband</h3>
                    <p>Served Ullapool and surrounding areas from 2017 until April 2026, when rising costs — backhaul, legal fees, equipment maintenance — made the service unviable. The closure is a significant example of how community networks can be undermined by cost structures that commercial providers can absorb but volunteer-run organisations cannot. Its nine-year run showed what community connectivity can achieve; its closure shows what happens without structural policy support.</p>
                    <a class="community-net-link" href="https://www.ispreview.co.uk/index.php/2026/01/wireless-isp-highland-community-broadband-set-to-close-in-april-2026.html"<?= external_link_attrs('https://www.ispreview.co.uk/index.php/2026/01/wireless-isp-highland-community-broadband-set-to-close-in-april-2026.html') ?>>ISPreview: closure announcement (January 2026) &rarr;</a>
                </div>

                <div class="community-net-item">
                    <div class="community-net-meta">
                        <span class="pill">Rural Scotland — multiple locations</span>
                    </div>
                    <h3 class="community-net-name">Community Broadband Scotland</h3>
                    <p>A Scottish Government and HIE programme that has funded community-led broadband pilots in Applecross, Colonsay, Tomintoul, and other areas. The programme provides capital and technical support for communities that want to build and run their own connectivity rather than wait for commercial providers.</p>
                    <a class="community-net-link" href="https://www.hie.co.uk/"<?= external_link_attrs('https://www.hie.co.uk/') ?>>Highlands and Islands Enterprise &rarr;</a>
                </div>

            </div>

            <div class="pull-quote" style="margin-top:2rem">
                <p>"Scotland has community-owned networks. The question is whether they remain the exception or become the expectation."</p>
                <cite>WIRES campaign position</cite>
            </div>

            <div class="info-card" style="margin-top:2rem">
                <div class="info-card__header">
                    <h2 class="info-card__heading">B4RN — the UK benchmark</h2>
                    <p class="info-card__sub">Lancashire, England — the model Scotland should study</p>
                </div>
                <div class="info-card__body">
                    <p>Broadband for the Rural North (B4RN) is a community cooperative in rural Lancashire that delivers gigabit-capable full-fibre broadband to areas commercial providers ignored. Built largely by volunteer labour, governed by members, and structured so that profits stay in the community. It is widely cited as proof that community ownership of broadband infrastructure is viable at scale.</p>
                    <p><a class="btn btn-ghost btn-sm" href="https://b4rn.org.uk/"<?= external_link_attrs('https://b4rn.org.uk/') ?>>b4rn.org.uk &rarr;</a></p>
                </div>
            </div>
        </div>

        <?php
        $sidebarRelated[] = ['href' => '/get-involved.php', 'label' => 'Get involved locally'];
        require __DIR__ . '/includes/sidebar-campaign.php';
        ?>

        </div>
    </div>
</div>

<?php
$ctaHeading = 'Bring these ideas to Scotland';
$ctaBody    = 'Join WIRES and help connect the dots between international models and local action.';
require __DIR__ . '/includes/cta-join.php';
require_once __DIR__ . '/includes/footer.php';
?>
