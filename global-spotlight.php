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
        'img'     => 'scotland-landscape.jpg',
        'imgAlt'  => 'Landscape — representing rural connectivity challenges that community networks can address.',
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
                <p><strong>Attribution:</strong> We highlight these projects because their documentation is public and their aims align with our values. Listing here is not an exhaustive survey of the field — suggest additions via <a href="/contact.php">Contact</a>.</p>
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
