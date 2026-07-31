<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

http_response_code(404);

$pageTitle       = 'Page not found';
$pageDescription = 'That page does not exist on the WIRES website — here is where to go instead.';
$pageRobots      = 'noindex, follow';
$currentNav      = '';

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>That page doesn't exist.</h1>
        <p>The link might be out of date, or the page may have moved. Here's where to go instead.</p>
    </div>
</header>

<div class="section">
    <div class="wrap">
        <div class="wrap--content">

            <form action="/search" method="get" role="search" style="margin-bottom:2.5rem">
                <div class="form-row" style="display:flex;gap:0.75rem">
                    <input id="q" name="q" type="search" required
                           placeholder="Search WIRES — e.g. social tariffs, Glasgow, health..."
                           aria-label="Search WIRES"
                           autocomplete="off"
                           style="flex:1">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>

            <div class="card-grid cols-2">
                <div class="info-card" style="margin-bottom:0">
                    <div class="info-card__header">
                        <h2 class="info-card__heading">Need help getting online?</h2>
                        <p class="info-card__sub">Schemes and programmes that can help</p>
                    </div>
                    <div class="info-card__body">
                        <p>Social tariffs, free SIM cards, and other real schemes that can lower your broadband costs or get you connected.</p>
                        <p><a class="btn btn-primary btn-sm" href="/get-help">See all schemes &rarr;</a></p>
                    </div>
                </div>
                <div class="info-card" style="margin-bottom:0">
                    <div class="info-card__header">
                        <h2 class="info-card__heading">Why connectivity matters</h2>
                        <p class="info-card__sub">The evidence behind this campaign</p>
                    </div>
                    <div class="info-card__body">
                        <p>Evidence from UK and Scottish sources on everyday reliance on the internet and who is most affected.</p>
                        <p><a class="btn btn-ghost btn-sm" href="/why-it-matters">Read the evidence &rarr;</a></p>
                    </div>
                </div>
            </div>

            <div class="callout" style="margin-top:2.5rem">
                <p class="callout__eyebrow">Found a broken link?</p>
                <p>Figures and page addresses change. If you followed a link from somewhere else on the web and landed here, <a href="/contact">tell us</a> so we can fix it.</p>
            </div>

            <p style="margin-top:2rem"><a class="btn btn-ghost" href="/">&larr; Back to the homepage</a></p>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
