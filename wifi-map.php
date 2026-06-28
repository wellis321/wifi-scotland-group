<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle       = 'Connectivity across Scotland';
$pageDescription = 'Explore broadband connectivity across Scotland\'s 32 council areas. Click any area to see how it compares — and find official sources for your local data.';
$currentNav      = 'wifimap';

$pageExtraHead = <<<'HTML'
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
HTML;

$pageExtraScripts = <<<'HTML'
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="/js/wifi-map.js" defer></script>
HTML;

require_once __DIR__ . '/includes/header.php';
?>
<header class="page-header">
    <div class="wrap">
        <h1>Connectivity across Scotland</h1>
        <p>
            Explore Scotland's 32 council areas and see how broadband coverage and connectivity vary across the country.
            The shading shows an indicative connectivity index — darker means better connected.
            Click any area to explore further and find official sources for your council.
        </p>
    </div>
</header>

<div class="section section--flush">
    <div class="wrap">
        <div class="map-frame map-frame--wifi">
            <div class="map-toolbar">
                <div>
                    <strong>Scotland: 32 council areas</strong>
                    <div class="map-toolbar-hint">Hover over an area to see its name &bull; click to explore &bull; use +/&minus; buttons or pinch to zoom</div>
                </div>
                <div class="map-legend" aria-label="Connectivity legend">
                    <span class="map-legend-label">Connectivity</span>
                    <span class="map-swatch choro choro-1" title="Lower connectivity"></span>
                    <span class="map-swatch choro choro-2"></span>
                    <span class="map-swatch choro choro-3"></span>
                    <span class="map-swatch choro choro-4"></span>
                    <span class="map-swatch choro choro-5" title="Higher connectivity"></span>
                </div>
            </div>
            <div class="map-body">
                <div
                    id="wifi-scotland-map"
                    class="map-canvas"
                    data-geojson="/data/scotland-council-areas.min.geojson"
                    data-stats="/api/wifi-map-stats.php"
                    role="img"
                    aria-label="Interactive map of Scotland showing connectivity by council area"
                ></div>
                <aside class="map-overlay map-overlay--list" aria-label="Council area list">
                    <div class="map-overlay-card">
                        <div class="map-overlay-title">
                            <strong>Areas</strong>
                            <span class="map-toolbar-hint">Hover to highlight</span>
                        </div>
                        <div id="wifi-map-overlay-list" class="map-overlay-list" role="list"></div>
                    </div>
                </aside>
                <aside class="map-overlay map-overlay--detail" aria-label="Selected area information" aria-live="polite">
                    <div class="map-overlay-card map-overlay-card--detail">
                        <div class="map-overlay-title">
                            <strong id="wifi-map-detail-title">Select an area</strong>
                        </div>
                        <div id="wifi-map-detail-body" class="wifi-map-detail-body">
                            <p class="wifi-map-detail-intro">Click the map or choose a council from the list to see connectivity information for that area.</p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <div class="prose prose--after-map">
            <h2>Find official data for your area</h2>
            <p>The map gives an indicative picture. For authoritative coverage data and programme information specific to your council area, use these official sources:</p>
            <ul>
                <li>
                    <a href="https://www.ofcom.org.uk/research-and-data/telecoms-research/connected-nations"<?= external_link_attrs('https://www.ofcom.org.uk/research-and-data/telecoms-research/connected-nations') ?>>Ofcom Connected Nations</a>
                    — the official regulator's coverage maps and statistics, updated annually. Search by postcode to see what speeds are available at your address.
                </li>
                <li>
                    <a href="https://digitalconnectivity.campaign.gov.scot/"<?= external_link_attrs('https://digitalconnectivity.campaign.gov.scot/') ?>>Reaching 100% (R100) — Scottish Government</a>
                    — check whether your area is included in Scotland's superfast broadband programme and when build is expected.
                </li>
                <li>
                    <a href="https://checker.ofcom.org.uk/"<?= external_link_attrs('https://checker.ofcom.org.uk/') ?>>Ofcom broadband checker</a>
                    — enter your postcode to see what broadband speeds and providers are available at your home or business.
                </li>
                <li>
                    <a href="https://www.gov.scot/collections/scottish-household-survey/"<?= external_link_attrs('https://www.gov.scot/collections/scottish-household-survey/') ?>>Scottish Household Survey</a>
                    — includes questions on internet access and confidence in some years; useful for local authority comparisons.
                </li>
            </ul>
            <div class="callout" style="margin-top:1.5rem">
                <p><strong>Something missing or wrong in your area?</strong> If your local connectivity situation does not match what official maps show, that gap is exactly what this campaign needs to hear about. <a href="/contact.php">Tell us</a> and we will follow it up.</p>
            </div>
            <p class="meta">Basemap &copy; <a href="https://www.openstreetmap.org/copyright"<?= external_link_attrs('https://www.openstreetmap.org/copyright') ?>>OpenStreetMap</a> contributors. Council boundary data used for display purposes only.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
