<?php

declare(strict_types=1);

/**
 * JSON endpoint for council-area WiFi / connectivity statistics used by `wifi-map.php`.
 * Data is read from `data/wifi-area-stats.json` (illustrative until official metrics are wired in).
 */

require_once __DIR__ . '/../includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=300');

$path = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'wifi-area-stats.json';

if (!is_readable($path)) {
    http_response_code(503);
    echo json_encode([
        'error' => 'wifi_stats_unavailable',
        'message' => 'Add data/wifi-area-stats.json on the server, or set up a pipeline to generate it.',
        'meta' => [
            'data_quality' => 'missing',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents($path);
if ($raw === false) {
    http_response_code(503);
    echo json_encode(['error' => 'wifi_stats_read_failed'], JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    $decoded = json_decode($raw, false, 512, JSON_THROW_ON_ERROR);
} catch (JsonException) {
    http_response_code(500);
    echo json_encode(['error' => 'wifi_stats_invalid_json'], JSON_UNESCAPED_SLASHES);
    exit;
}

$flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
if (isset($_GET['pretty']) && $_GET['pretty'] === '1') {
    $flags |= JSON_PRETTY_PRINT;
}

echo json_encode($decoded, $flags);
