#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Copies real emails from the hello@wires.org.uk Inbox and Sent folders into the
 * correspondence log shown at /admin/correspondence.php. Also runs automatically at
 * the end of bin/check-campaign-replies.php.
 *
 * Usage:
 *   php bin/log-correspondence.php              Import the last 7 days.
 *   php bin/log-correspondence.php --days=40    Look further back.
 *   php bin/log-correspondence.php --dry-run    Show what would be logged, write nothing.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("This script is for command-line use only.\n");
}

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/correspondence.php';

$dryRun = in_array('--dry-run', $argv, true);
$days = 7;
foreach ($argv as $arg) {
    if (preg_match('/^--days=(\d+)$/', $arg, $m)) {
        $days = max(1, (int) $m[1]);
    }
}

if (!campaign_db_available()) {
    fwrite(STDERR, "Campaign database is not reachable.\n");
    exit(1);
}

$count = import_correspondence($days, $dryRun, static fn(string $line) => fwrite(STDOUT, $line . "\n"));

fwrite(STDOUT, sprintf("\n%s %d entr%s from the last %d days.\n", $dryRun ? '[dry-run] Would log' : 'Logged', $count, $count === 1 ? 'y' : 'ies', $days));
