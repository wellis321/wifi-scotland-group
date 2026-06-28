<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Image credits';
$pageDescription = 'Licences and sources for photographs used on this website.';
$currentNav = '';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Image credits</h1>
        <p>We host optimised copies under <code>/images/</code> for performance. Originals are from <a href="https://unsplash.com/"<?= external_link_attrs('https://unsplash.com/') ?>>Unsplash</a> and may be used under the <a href="https://unsplash.com/license"<?= external_link_attrs('https://unsplash.com/license') ?>>Unsplash Licence</a> (free to use with optional attribution).</p>
    </div>
</header>

<div class="section">
    <div class="wrap prose">
        <p>To locate the original on Unsplash, open <a href="https://unsplash.com/"<?= external_link_attrs('https://unsplash.com/') ?>>unsplash.com</a> and paste the photo reference (the long id after <code>photo-</code> in the table below) into search, or use your search engine with <code>site:unsplash.com</code> plus that id.</p>

        <div class="table-scroll">
        <table class="credits-table">
            <thead>
                <tr>
                    <th scope="col">File on this site</th>
                    <th scope="col">Unsplash photo reference</th>
                    <th scope="col">Used on</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>hero-laptop-home.jpg</code></td>
                    <td><code>photo-1516321318423-f06f85e504b3</code></td>
                    <td>Home hero, Open Graph default</td>
                </tr>
                <tr>
                    <td><code>card-fibre.jpg</code></td>
                    <td><code>photo-1558618666-fcd25c85cd64</code></td>
                    <td>Home “Scotland” card, news banner</td>
                </tr>
                <tr>
                    <td><code>card-community.jpg</code></td>
                    <td><code>photo-1531482615713-2afd69097998</code></td>
                    <td>Home “Councils &amp; communities” card, Get involved, Contact</td>
                </tr>
                <tr>
                    <td><code>card-global-network.jpg</code></td>
                    <td><code>photo-1451187580459-43490279c0fa</code></td>
                    <td>Home “Worldwide” card, Global spotlight, news articles (sharing image)</td>
                </tr>
                <tr>
                    <td><code>about-team.jpg</code></td>
                    <td><code>photo-1522071820081-009f0129c71c</code></td>
                    <td>About</td>
                </tr>
                <tr>
                    <td><code>scotland-landscape.jpg</code></td>
                    <td><code>photo-1506905925346-21bda4d32df4</code></td>
                    <td>Scotland policy page</td>
                </tr>
                <tr>
                    <td><code>glasses-clarity.jpg</code></td>
                    <td><a href="https://unsplash.com/photos/kqguzgvYrtM"<?= external_link_attrs('https://unsplash.com/photos/kqguzgvYrtM') ?>>photo-1512099053734-e6767b535838</a></td>
                    <td>Homepage — "Public money should mean public clarity" argument</td>
                </tr>
            </tbody>
        </table>
        </div>

        <p>If you replace these files, update this page so visitors and mirrors stay aligned with the licence you choose.</p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
