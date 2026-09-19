<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Global spotlight';
$pageDescription = 'Positive international examples of community connectivity — from mesh networks to mobile cultural centres.';
$currentNav      = 'global';

$pageOgImage    = image_asset('card-global-network.jpg');
$pageOgImageAlt = 'Illuminated view of Earth from space — symbolising global networks and shared infrastructure.';

$sidebarRelated = [
    ['href' => '/scotland',      'label' => 'Scotland policy'],
    ['href' => '/why-it-matters','label' => 'Why it matters'],
    ['href' => '/resources',     'label' => 'Resources & references'],
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
    [
        'name'    => 'Rhizomatica',
        'sub'     => 'Community-owned cellular networks, Oaxaca, Mexico',
        'img'     => 'card-community.jpg',
        'imgAlt'  => 'Community group around a table — representing the indigenous-led cooperative behind Rhizomatica\'s networks.',
        'body'    => 'Rhizomatica helped indigenous and rural communities in Oaxaca build and legally operate their own cellular networks, deploying Mexico\'s first independent GSM network in 2014. The cooperative that grew out of that work, Telecomunicaciones Indígenas Comunitarias, holds Mexico\'s first-ever telecoms concessions granted specifically for indigenous social use — now covering 14 communities and 63 localities across five states.',
        'url'     => 'https://www.rhizomatica.org/',
        'urlLabel'=> 'rhizomatica.org',
    ],
    [
        'name'    => 'Zenzeleni Networks',
        'sub'     => 'Cooperative-owned ISP, Eastern Cape, South Africa',
        'img'     => 'about-team.jpg',
        'imgAlt'  => 'Group of people working together — representing Zenzeleni\'s cooperative ownership model.',
        'body'    => 'South Africa\'s first cooperative-owned internet service provider, built and run by residents of rural Mankosi and neighbouring Eastern Cape villages. Zenzeleni keeps telecoms spending inside the community rather than sending it to national operators, and has been recognised internationally, including as a runner-up in Mozilla\'s Equal Rating Innovation Challenge.',
        'url'     => 'https://zenzeleni.net/',
        'urlLabel'=> 'zenzeleni.net',
    ],
    [
        'name'    => 'Sarantaporo.gr',
        'sub'     => 'Volunteer wireless network, rural Greece',
        'img'     => 'scotland-landscape.jpg',
        'imgAlt'  => 'Rolling rural landscape — representing the mountain villages Sarantaporo.gr connects.',
        'body'    => 'Built and maintained by volunteers, Sarantaporo.gr brings connectivity to mountain villages in Greece\'s Elassona municipality that commercial providers never reached, pairing the network with digital-literacy training so it strengthens the community rather than just supplying a signal. The project won a European Broadband Award in 2019 and placed second in the IEEE Connecting the Unconnected Challenge in 2022.',
        'url'     => 'https://www.sarantaporo.gr/en/',
        'urlLabel'=> 'sarantaporo.gr',
    ],
    [
        'name'    => 'Ninux',
        'sub'     => 'Long-running mesh network, Italy',
        'img'     => 'card-global-network.jpg',
        'imgAlt'  => 'Digital network globe — representing Ninux\'s mesh of interconnected nodes across Italy.',
        'body'    => 'One of Europe\'s oldest and largest wireless mesh networks, started in Rome in 2001 by students and hackers building their own alternative to commercial providers. Ninux has grown into one of the world\'s largest community networks by active node count, with local "Ninux islands" running across several Italian regions, and has been an experimental member of Rome\'s NaMeX internet exchange since 2013.',
        'url'     => 'https://en.wikipedia.org/wiki/Ninux',
        'urlLabel'=> 'Ninux (Wikipedia)',
    ],
    [
        'name'    => 'AlterMundi / LibreRouter',
        'sub'     => 'Open-source mesh hardware, Argentina',
        'img'     => 'card-fibre.jpg',
        'imgAlt'  => 'Close-up of fibre-optic cabling — representing the open-hardware routers AlterMundi builds for community networks.',
        'body'    => 'AlterMundi builds LibreRouter, an open-source router designed specifically for community networks rather than adapted from commercial hardware — built to be affordable, locally repairable, and deployable without a background in networking. Prototypes have been used by community networks in Argentina, Mexico, Canada, and Spain, developed hand-in-hand with groups including Guifi.net and Ninux.',
        'url'     => 'https://www.altermundi.net/',
        'urlLabel'=> 'altermundi.net',
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
                <p>We highlight these projects because their documentation is public and their aims align with our values. Listing here is not an exhaustive survey of the field — suggest additions via <a href="/contact">Contact</a>.</p>
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
                        <span class="pill pill--forming" style="margin-left:0.4rem">Sold to Scotnet, May 2026</span>
                    </div>
                    <h3 class="community-net-name">Highland Community Broadband</h3>
                    <p>The volunteer-run CIC served Ullapool and surrounding areas from 2017 until rising costs — backhaul, legal fees, equipment maintenance — made continuing as a community organisation unviable, announced in January 2026. Rather than going dark, HCB sold the network to Scotnet, who bought it in May 2026 and have been upgrading and expanding it since, now branded Wester Ross Broadband. The volunteer-run model's financial fragility is still a real lesson — but this is a story of a network changing hands and continuing, not one that left residents without service.</p>
                    <a class="community-net-link" href="https://www.scotnet.co.uk/rural-broadband/wester-ross"<?= external_link_attrs('https://www.scotnet.co.uk/rural-broadband/wester-ross') ?>>Scotnet: Wester Ross broadband &rarr;</a>
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
        $sidebarRelated[] = ['href' => '/get-involved', 'label' => 'Get involved locally'];
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
