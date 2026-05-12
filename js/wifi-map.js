(() => {
  'use strict';

  const mapEl = document.getElementById('wifi-scotland-map');
  if (!mapEl) return;

  const Lref = window.L;
  if (!Lref) return;

  const L = Lref;

  const GEOJSON_URL = mapEl.getAttribute('data-geojson') || '/data/scotland-council-areas.min.geojson';
  const STATS_URL = mapEl.getAttribute('data-stats') || '/api/wifi-map-stats.php';

  const detailTitle = document.getElementById('wifi-map-detail-title');
  const detailBody = document.getElementById('wifi-map-detail-body');
  const overlayList = document.getElementById('wifi-map-overlay-list');

  const boundsExcludeNames = new Set(['Shetland Islands', 'Orkney Islands']);

  const choroColors = [
    'rgba(31, 122, 107, 0.14)',
    'rgba(31, 122, 107, 0.22)',
    'rgba(31, 122, 107, 0.30)',
    'rgba(31, 122, 107, 0.38)',
    'rgba(31, 122, 107, 0.48)',
  ];

  const borderColor = 'rgba(20, 92, 80, 0.85)';

  const baseStyleForIndex = (idx) => ({
    color: borderColor,
    weight: 1,
    opacity: 1,
    fillColor: choroColors[Math.max(0, Math.min(4, idx - 1))] || choroColors[2],
    fillOpacity: 1,
  });

  const hoverStyle = {
    weight: 2,
    color: 'rgba(12, 17, 23, 0.9)',
  };

  const focusStyleForIndex = (idx) => ({
    ...baseStyleForIndex(idx),
    weight: 2.5,
    color: 'rgba(12, 17, 23, 1)',
  });

  const escapeHtml = (s) =>
    String(s ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');

  const map = L.map(mapEl, {
    zoomControl: true,
    scrollWheelZoom: false,
    dragging: true,
    tap: true,
  });

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
    maxZoom: 11,
  }).addTo(map);

  let geoLayer = null;
  let focusedLayer = null;
  let overlayHoverLayer = null;
  const layersByCode = new Map();

  const defaultIndex = 3;

  const connectivityIndexForLayer = (layer) => {
    const code = layer?.feature?.properties?.code;
    const row = code ? statsByCode[code] : null;
    const n = row && typeof row.connectivity_index === 'number' ? row.connectivity_index : defaultIndex;
    if (n >= 1 && n <= 5) return n;
    return defaultIndex;
  };

  const setDetail = (name, code, row) => {
    if (detailTitle) detailTitle.textContent = name || 'Area';
    if (!detailBody) return;

    if (!row) {
      detailBody.innerHTML =
        '<p class="wifi-map-detail-intro">No statistics row for this area yet. Add an entry keyed by <code>' +
        escapeHtml(code) +
        '</code> in <code>data/wifi-area-stats.json</code>.</p>';
      return;
    }

    const lines = [
      '<dl class="wifi-map-dl">',
      '<div><dt>Connectivity index (demo)</dt><dd><strong>' +
        escapeHtml(String(row.connectivity_index ?? '—')) +
        '</strong> — ' +
        escapeHtml(row.connectivity_label || '') +
        '</dd></div>',
      '<div><dt>Median download (Mbps, illustrative)</dt><dd>' + escapeHtml(String(row.median_download_mbps_illustrative ?? '—')) + '</dd></div>',
      '<div><dt>Ultrafast premises % (illustrative)</dt><dd>' + escapeHtml(String(row.ultrafast_premises_pct_illustrative ?? '—')) + '</dd></div>',
      '<div><dt>Affordability pressure (illustrative)</dt><dd>' + escapeHtml(row.affordability_pressure || '—') + '</dd></div>',
      '<div><dt>As-of date</dt><dd>' + escapeHtml(row.last_updated || '—') + '</dd></div>',
      '</dl>',
      '<p class="wifi-map-detail-foot">Figures are not official. Use <a href="https://www.ofcom.org.uk/research-and-data/multi-sector-research/infrastructure-research" rel="noopener noreferrer" target="_blank">Ofcom</a> and programme sites for authoritative metrics.</p>',
    ];

    detailBody.innerHTML = lines.join('');
  };

  const setFocus = (layer) => {
    if (focusedLayer && focusedLayer !== layer) {
      const prevIdx = connectivityIndexForLayer(focusedLayer);
      focusedLayer.setStyle(baseStyleForIndex(prevIdx));
    }
    focusedLayer = layer;
    if (focusedLayer) {
      const idx = connectivityIndexForLayer(focusedLayer);
      focusedLayer.setStyle(focusStyleForIndex(idx));
      focusedLayer.bringToFront?.();
      const name = focusedLayer.feature?.properties?.name || 'Unknown';
      const code = focusedLayer.feature?.properties?.code || '';
      setDetail(name, code, statsByCode[code] || null);
    }
  };

  const setOverlayHover = (layer) => {
    if (overlayHoverLayer && overlayHoverLayer !== focusedLayer) {
      const i = connectivityIndexForLayer(overlayHoverLayer);
      overlayHoverLayer.setStyle(baseStyleForIndex(i));
      overlayHoverLayer.closeTooltip?.();
    }
    overlayHoverLayer = layer;
    if (overlayHoverLayer && overlayHoverLayer !== focusedLayer) {
      Object.assign(overlayHoverLayer.options, hoverStyle);
      overlayHoverLayer.setStyle({ ...baseStyleForIndex(connectivityIndexForLayer(overlayHoverLayer)), ...hoverStyle });
      overlayHoverLayer.bringToFront?.();
      overlayHoverLayer.openTooltip?.();
    }
  };

  let statsByCode = {};

  Promise.all([
    fetch(GEOJSON_URL).then((r) => {
      if (!r.ok) throw new Error('geojson ' + r.status);
      return r.json();
    }),
    fetch(STATS_URL).then((r) => {
      if (!r.ok) throw new Error('stats ' + r.status);
      return r.json();
    }),
  ])
    .then(([geojson, statsPayload]) => {
      statsByCode = statsPayload.by_code && typeof statsPayload.by_code === 'object' ? statsPayload.by_code : {};

      const councils = [];

      geoLayer = L.geoJSON(geojson, {
        style: (feat) => baseStyleForIndex(connectivityIndexForLayer({ feature: feat })),
        onEachFeature: (feature, lyr) => {
          const name = feature?.properties?.name || 'Unknown area';
          const code = feature?.properties?.code || '';
          lyr.bindTooltip(name, { sticky: true, direction: 'auto' });
          if (code) layersByCode.set(code, lyr);
          councils.push({ name, code });

          lyr.on('mouseover', () => {
            if (lyr !== focusedLayer) {
              Object.assign(lyr.options, hoverStyle);
              lyr.setStyle({ ...baseStyleForIndex(connectivityIndexForLayer(lyr)), ...hoverStyle });
            }
          });
          lyr.on('mouseout', () => {
            if (lyr !== focusedLayer && lyr !== overlayHoverLayer) {
              lyr.setStyle(baseStyleForIndex(connectivityIndexForLayer(lyr)));
            }
          });
          lyr.on('click', () => {
            setFocus(lyr);
            try {
              map.fitBounds(lyr.getBounds(), { maxZoom: 10, padding: [24, 24] });
            } catch {
              /* ignore */
            }
          });
        },
      }).addTo(map);

      const targetBounds = L.latLngBounds([]);
      geoLayer.eachLayer((lyr) => {
        const n = lyr?.feature?.properties?.name;
        if (n && boundsExcludeNames.has(n)) return;
        if (lyr.getBounds) targetBounds.extend(lyr.getBounds());
      });

      map.fitBounds(targetBounds.isValid() ? targetBounds : geoLayer.getBounds(), {
        paddingTopLeft: [18, 110],
        paddingBottomRight: [18, 28],
        animate: false,
      });
      map.setZoom(Math.min(map.getZoom() + 1, 10));
      map.panBy([0, -70], { animate: false });

      if (overlayList) {
        councils
          .filter((c) => c.name && c.code)
          .sort((a, b) => a.name.localeCompare(b.name, 'en-GB'))
          .forEach((c) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'map-overlay-item';
            btn.textContent = c.name;
            btn.setAttribute('role', 'listitem');
            btn.setAttribute('data-code', c.code);

            const lyr = layersByCode.get(c.code);
            const highlight = () => setOverlayHover(lyr || null);
            const clear = () => setOverlayHover(null);

            btn.addEventListener('mouseenter', highlight);
            btn.addEventListener('mouseleave', clear);
            btn.addEventListener('focus', highlight);
            btn.addEventListener('blur', clear);
            btn.addEventListener('click', () => {
              if (!lyr) return;
              setFocus(lyr);
              try {
                map.fitBounds(lyr.getBounds(), { maxZoom: 10, padding: [24, 24] });
              } catch {
                /* ignore */
              }
              lyr.openTooltip?.();
            });

            overlayList.appendChild(btn);
          });
      }
    })
    .catch(() => {
      mapEl.innerHTML =
        '<div class="map-error">Map could not load. Ensure the site is served over HTTP(S) (not as a raw file) and that <code>data/scotland-council-areas.min.geojson</code> and <code>api/wifi-map-stats.php</code> are reachable.</div>';
    });
})();
