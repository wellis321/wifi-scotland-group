<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Global spotlight';
$pageDescription = 'Positive international examples of community connectivity—from mesh networks to mobile cultural centres.';
$currentNav = 'global';

$pageOgImage = image_asset('card-global-network.jpg');
$pageOgImageAlt = 'Illuminated view of Earth from space—symbolising global networks and shared infrastructure.';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Global spotlight</h1>
        <p>Scotland does not have a monopoly on good ideas. These projects show what it looks like when communities own and run their own networks—built around people, not profit. Descriptions are short; follow the links to go deeper.</p>
    </div>
</header>

<div class="section">
    <div class="wrap prose">
        <figure class="page-lede">
            <img src="<?= e(image_asset('card-global-network.jpg')) ?>" width="1400" height="933" alt="Digital globe visual suggesting worldwide data networks." decoding="async" loading="lazy">
            <figcaption>Community networks worldwide show that connectivity can be designed around participation—not only consumption.</figcaption>
        </figure>
        <h2>NYC Mesh — volunteer-led mesh in New York City</h2>
        <p>
            <strong>NYC Mesh</strong> is a non-profit, volunteer-driven network building neighbourhood links across New York City. Their public materials emphasise community ownership, donations rather than extractive pricing, and a documented approach to extending the mesh responsibly.
        </p>
        <p><a href="https://www.nycmesh.net/"<?= external_link_attrs('https://www.nycmesh.net/') ?>>nycmesh.net</a></p>

        <h2>Libraries Without Borders — Ideas Box and offline-first tools</h2>
        <p>
            <strong>Bibliothèques Sans Frontières / Libraries Without Borders</strong> deploys <em>Ideas Box</em> mobile cultural and learning spaces in humanitarian and underserved contexts, combining satellite connectivity with books, devices, and facilitation.
            They also develop offline-first approaches (such as the Ideas Cube and related platforms) for places where continuous internet is unrealistic—an important reminder that “access” is not only fibre to the home.
        </p>
        <p><a href="https://www.librarieswithoutborders.org/ideasbox/"<?= external_link_attrs('https://www.librarieswithoutborders.org/ideasbox/') ?>>Ideas Box (Libraries Without Borders)</a></p>

        <h2>Freifunk — decentralised community wireless (Germany and neighbouring regions)</h2>
        <p>
            The <strong>Freifunk</strong> movement supports autonomous community wireless networks with a strong emphasis on political education alongside technical build-out. Individual communities publish their own nodes and policies; treat Freifunk as a family of projects rather than one centralised organisation.
        </p>
        <p><a href="https://freifunk.net/en/"<?= external_link_attrs('https://freifunk.net/en/') ?>>freifunk.net</a></p>

        <h2>Guifi.net — community network, Catalonia and beyond</h2>
        <p>
            <strong>Guifi.net</strong> is a long-running, user-governed network often cited in research on community infrastructure. It grew from wireless links in rural Catalonia into a broader commons-based model with clear governance documentation and a foundation supporting operations.
            It is a useful reference when asking what “open and neutral” network commitments can look like in practice.
        </p>
        <p><a href="https://guifi.net/"<?= external_link_attrs('https://guifi.net/') ?>>guifi.net</a> · <a href="https://fundacio.guifi.net/"<?= external_link_attrs('https://fundacio.guifi.net/') ?>>Guifi.net Foundation</a></p>

        <div class="callout">
            <p><strong>Attribution note:</strong> We highlight these projects because their documentation is public and their aims align with our values. Listing here is not an exhaustive survey of the field; suggest additions via <a href="/contact.php">Contact</a>.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
