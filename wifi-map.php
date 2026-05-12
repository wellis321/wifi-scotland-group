<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'WiFi map: Scotland by council area';
$pageDescription = 'Interactive map of Scottish local authority areas with illustrative connectivity statistics—click an area to explore. Official figures should replace demo data when sourced.';
$currentNav = 'wifimap';

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
        <h1>WiFi map</h1>
        <p>
            Explore Scottish council areas on the map below. Shading reflects an <strong>illustrative</strong> connectivity index for UI development only.
            When you have Ofcom, R100, or council open data, replace <code>data/wifi-area-stats.json</code> or extend <code>api/wifi-map-stats.php</code> to join authoritative figures.
        </p>
    </div>
</header>

<div class="section section--flush">
    <div class="wrap">
        <div class="map-frame map-frame--wifi">
            <div class="map-toolbar">
                <div>
                    <strong>Scotland: 32 local authorities</strong>
                    <div class="map-toolbar-hint">Hover for the area name • click for illustrative stats • scroll-wheel zoom is off—use +/− or pinch</div>
                </div>
                <div class="map-legend" aria-label="Choropleth legend">
                    <span class="map-legend-label">Connectivity (demo)</span>
                    <span class="map-swatch choro choro-1" title="1 — lowest (illustrative)"></span>
                    <span class="map-swatch choro choro-2"></span>
                    <span class="map-swatch choro choro-3"></span>
                    <span class="map-swatch choro choro-4"></span>
                    <span class="map-swatch choro choro-5" title="5 — highest (illustrative)"></span>
                </div>
            </div>
            <div class="map-body">
                <div
                    id="wifi-scotland-map"
                    class="map-canvas"
                    data-geojson="/data/scotland-council-areas.min.geojson"
                    data-stats="/api/wifi-map-stats.php"
                    role="img"
                    aria-label="Interactive map of Scotland local authority areas"
                ></div>
                <aside class="map-overlay map-overlay--list" aria-label="Council list">
                    <div class="map-overlay-card">
                        <div class="map-overlay-title">
                            <strong>Areas</strong>
                            <span class="map-toolbar-hint">Hover to highlight</span>
                        </div>
                        <div id="wifi-map-overlay-list" class="map-overlay-list" role="list"></div>
                    </div>
                </aside>
                <aside class="map-overlay map-overlay--detail" aria-label="Selected area statistics" aria-live="polite">
                    <div class="map-overlay-card map-overlay-card--detail">
                        <div class="map-overlay-title">
                            <strong id="wifi-map-detail-title">Select an area</strong>
                        </div>
                        <div id="wifi-map-detail-body" class="wifi-map-detail-body">
                            <p class="wifi-map-detail-intro">Click the map or choose a council from the list. Numbers shown here are placeholders until official datasets are connected.</p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <div class="prose prose--after-map">
            <h2>Next steps for real data</h2>
            <ol>
                <li>Join council boundaries (already keyed by <code>properties.code</code> such as <code>S12000033</code>) to your chosen open dataset.</li>
                <li>Keep provenance and update dates in the JSON <code>meta</code> object returned by the API.</li>
                <li>If you aggregate <code>member_signups.locality</code>, normalise spellings or use postcode districts before mapping to councils.</li>
            </ol>
            <p class="meta">Basemap © <a href="https://www.openstreetmap.org/copyright"<?= external_link_attrs('https://www.openstreetmap.org/copyright') ?>>OpenStreetMap</a> contributors. Boundary GeoJSON is used for display only.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
