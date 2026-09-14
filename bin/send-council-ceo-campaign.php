#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Mail-merge sender for the council Chief Executive accountability campaign.
 *
 * Sends one personalised email per row in data/council-ceo-roster.csv (32 councils) via
 * the Resend batch API, and writes the result straight into council_contacts on the
 * PRODUCTION database (via campaign_db()) — the same table /council-replies reads from.
 * Re-running this script is safe: rows with outreach_sent_at already set are skipped,
 * so only not-yet-sent councils are retried.
 *
 * RUN THIS ONLY FROM THE COMMAND LINE. There is no web trigger for this script on
 * purpose — a send to real council Chief Executives must never be one accidental page
 * load away.
 *
 * Usage:
 *   php bin/send-council-ceo-campaign.php --dry-run
 *     Renders the first few emails to STDOUT. Sends nothing, touches no DB rows.
 *
 *   php bin/send-council-ceo-campaign.php --test-to=you@example.com --test-count=5
 *     Sends 5 real merge-personalised emails (using 5 real roster rows) but all
 *     addressed to --test-to instead of the real Chief Executive. Subject is prefixed
 *     "[TEST]" so it's unmistakable in an inbox. Never touches the DB.
 *
 *   php bin/send-council-ceo-campaign.php
 *     A real send to every council not yet marked as sent (32 total). Asks for
 *     interactive confirmation first unless --yes is also passed.
 *
 * Flags:
 *   --dry-run          Render only, no network calls, no DB writes.
 *   --test-to=EMAIL     Redirect every send in this run to EMAIL (implies test mode).
 *   --test-count=N      How many real roster rows to use when testing (default 5).
 *   --limit=N           Cap how many real recipients to send to this run.
 *   --yes               Skip the interactive "type SEND" confirmation prompt.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("This script is for command-line use only.\n");
}

require_once __DIR__ . '/../includes/bootstrap.php';

const ROSTER_CSV_PATH   = __DIR__ . '/../data/council-ceo-roster.csv';
const RESEND_BATCH_URL  = 'https://api.resend.com/emails/batch';
const BATCH_SIZE        = 100;

// ─── Argument parsing ────────────────────────────────────────────────────────

$opts = getopt('', ['dry-run', 'yes', 'test-to:', 'test-count:', 'limit:']);

$dryRun      = array_key_exists('dry-run', $opts);
$skipConfirm = array_key_exists('yes', $opts);
$testTo      = isset($opts['test-to']) ? trim((string) $opts['test-to']) : null;
$testCount   = isset($opts['test-count']) ? max(1, (int) $opts['test-count']) : 5;
$limit       = isset($opts['limit']) ? max(1, (int) $opts['limit']) : null;

if ($testTo !== null && !filter_var($testTo, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "--test-to is not a valid email address.\n");
    exit(1);
}

// ─── Email template ──────────────────────────────────────────────────────────

// render_greeting()/render_subject()/render_text_body()/render_html_body() live in
// includes/campaign-templates/council-ceo-accountability.php — shared with
// council-replies.php, which renders the same template publicly.

require_once __DIR__ . '/../includes/campaign-templates/council-ceo-accountability.php';

// ─── Roster loading ──────────────────────────────────────────────────────────

function load_roster(string $path): array
{
    if (!is_readable($path)) {
        fwrite(STDERR, "Cannot read roster CSV at $path\n");
        exit(1);
    }

    $fh = fopen($path, 'r');
    if ($fh === false) {
        fwrite(STDERR, "Failed to open roster CSV.\n");
        exit(1);
    }

    $header = fgetcsv($fh, 0, ',', '"', '\\');
    $headerCount = count($header);
    $rows = [];

    while (($line = fgetcsv($fh, 0, ',', '"', '\\')) !== false) {
        if (count($line) < $headerCount) {
            continue;
        }
        if (count($line) > $headerCount) {
            $overflow = array_splice($line, $headerCount - 1);
            $line[] = implode(',', $overflow);
        }
        $row = array_combine($header, $line);
        if (trim((string) ($row['ceo_email'] ?? '')) === '') {
            continue;
        }
        $rows[] = $row;
    }

    fclose($fh);
    return $rows;
}

// ─── Resend API ──────────────────────────────────────────────────────────────

function resend_send_batch(string $apiKey, array $emails): array
{
    $ch = curl_init(RESEND_BATCH_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS => json_encode($emails, JSON_UNESCAPED_SLASHES),
        CURLOPT_TIMEOUT => 30,
    ]);

    $responseBody = curl_exec($ch);
    $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError    = curl_error($ch);

    if ($responseBody === false) {
        return ['ok' => false, 'error' => "cURL error: $curlError", 'ids' => []];
    }

    $decoded = json_decode((string) $responseBody, true);

    if ($httpCode < 200 || $httpCode >= 300) {
        $message = is_array($decoded) && isset($decoded['message'])
            ? (string) $decoded['message']
            : "HTTP $httpCode: $responseBody";
        return ['ok' => false, 'error' => $message, 'ids' => []];
    }

    $ids = [];
    if (is_array($decoded) && isset($decoded['data']) && is_array($decoded['data'])) {
        foreach ($decoded['data'] as $item) {
            $ids[] = is_array($item) && isset($item['id']) ? (string) $item['id'] : null;
        }
    }

    return ['ok' => true, 'error' => null, 'ids' => $ids];
}

// ─── DB logging ──────────────────────────────────────────────────────────────

function already_sent_councils(): array
{
    $stmt = campaign_db()->query(
        'SELECT council_area FROM council_contacts WHERE outreach_sent_at IS NOT NULL'
    );
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * The CEO letter says "we're writing in parallel to all councillors" — only true for
 * councils the councillor campaign has actually reached. Keeps the two campaigns in
 * sync: as more daily councillor batches go out, more councils become eligible here.
 */
function councils_reached_by_councillor_campaign(): array
{
    $stmt = campaign_db()->query(
        "SELECT DISTINCT council_area FROM councillor_campaign_sends
         WHERE campaign_slug = 'councillor-public-statement-2026-09' AND status = 'sent'"
    );
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function log_send_result(array $row, string $subject, string $bodyText): void
{
    $stmt = campaign_db()->prepare(
        'UPDATE council_contacts
         SET outreach_sent_at = CURRENT_DATE, ceo_name = ?, ceo_email = ?, subject = ?, body_text = ?
         WHERE council_area = ?'
    );
    $stmt->execute([$row['ceo_name'], $row['ceo_email'], $subject, $bodyText, $row['council_area']]);
}

// ─── Main ────────────────────────────────────────────────────────────────────

$fromName  = env_raw('CAMPAIGN_FROM_NAME') ?: 'WIRES';
$fromEmail = env_raw('CAMPAIGN_FROM_EMAIL') ?: 'hello@wires.org.uk';
$from      = "$fromName <$fromEmail>";
$apiKey    = env_raw('RESEND_API_KEY');

$roster = load_roster(ROSTER_CSV_PATH);
fwrite(STDERR, sprintf("Loaded %d roster rows with an email.\n", count($roster)));

$isTestMode = $testTo !== null;

// Only consider councils the councillor campaign has actually reached — the letter
// itself says "we're writing in parallel to all councillors", so that has to be true
// for this specific council at send time, not just true campaign-wide.
$reachedCouncils = null;
if (campaign_db_available()) {
    $reachedCouncils = councils_reached_by_councillor_campaign();
    $roster = array_values(array_filter(
        $roster,
        fn(array $row) => in_array($row['council_area'], $reachedCouncils, true)
    ));
    fwrite(STDERR, sprintf(
        "%d council(s) already reached by the councillor campaign — restricting to those.\n",
        count($reachedCouncils)
    ));
} else {
    fwrite(STDERR, "Warning: campaign database unavailable, cannot check councillor-campaign progress — showing unfiltered roster.\n");
}

if ($dryRun) {
    $sample = array_slice($roster, 0, min(3, count($roster)));
    foreach ($sample as $row) {
        fwrite(STDOUT, str_repeat('=', 70) . "\n");
        fwrite(STDOUT, "TO: {$row['ceo_name']} <{$row['ceo_email']}> ({$row['council_area']}) [confidence: {$row['confidence']}]\n");
        fwrite(STDOUT, "FROM: $from\n");
        fwrite(STDOUT, "SUBJECT: " . render_subject($row) . "\n\n");
        fwrite(STDOUT, render_text_body($row) . "\n");
    }
    fwrite(STDOUT, str_repeat('=', 70) . "\n");
    fwrite(STDOUT, sprintf("[dry-run] Would send to %d recipients. Nothing sent, nothing logged.\n", count($roster)));
    exit(0);
}

if ($apiKey === null || $apiKey === '') {
    fwrite(STDERR, "RESEND_API_KEY is not set in .env — cannot send.\n");
    exit(1);
}

if ($isTestMode) {
    $recipients = array_slice($roster, 0, min($testCount, count($roster)));
    fwrite(STDERR, sprintf("[test mode] %d real merge rows, all redirected to %s\n", count($recipients), $testTo));
} else {
    if (!campaign_db_available()) {
        fwrite(STDERR, "Campaign database is not reachable — refusing to send without send-tracking available.\n");
        exit(1);
    }
    $alreadySent = already_sent_councils();
    $recipients = array_values(array_filter(
        $roster,
        fn(array $row) => !in_array($row['council_area'], $alreadySent, true)
    ));
    fwrite(STDERR, sprintf(
        "%d council(s) already sent; %d remaining.\n",
        count($alreadySent),
        count($recipients)
    ));
    if ($limit !== null) {
        $recipients = array_slice($recipients, 0, $limit);
        fwrite(STDERR, sprintf("--limit=%d applied: sending to %d this run.\n", $limit, count($recipients)));
    }
}

if (empty($recipients)) {
    fwrite(STDOUT, "Nothing to send — no matching recipients.\n");
    exit(0);
}

if (!$skipConfirm) {
    fwrite(STDERR, sprintf(
        "\nAbout to send %d %s email(s) as %s.\nType SEND to continue, anything else to abort: ",
        count($recipients),
        $isTestMode ? '[TEST]' : 'REAL',
        $from
    ));
    $confirmation = trim((string) fgets(STDIN));
    if ($confirmation !== 'SEND') {
        fwrite(STDERR, "Aborted — nothing sent.\n");
        exit(1);
    }
}

$sentCount = 0;
$failedCount = 0;

foreach (array_chunk($recipients, BATCH_SIZE) as $batch) {
    $payload = [];
    $renderedSubjects = [];
    $renderedTexts = [];
    foreach ($batch as $i => $row) {
        $subject = render_subject($row);
        $text = render_text_body($row);
        $renderedSubjects[$i] = $subject; // exact subject actually sent, for logging — before any [TEST] prefix
        $renderedTexts[$i] = $text;
        if ($isTestMode) {
            $subject = "[TEST - would go to {$row['ceo_name']}, {$row['ceo_email']}] $subject";
        }
        $payload[] = [
            'from' => $from,
            'to' => [$isTestMode ? $testTo : $row['ceo_email']],
            'subject' => $subject,
            'html' => render_html_body($row),
            'text' => $text,
        ];
    }

    $result = resend_send_batch($apiKey, $payload);

    foreach ($batch as $i => $row) {
        if ($result['ok']) {
            if (!$isTestMode) {
                log_send_result($row, $renderedSubjects[$i], $renderedTexts[$i]);
            }
            $sentCount++;
            fwrite(STDOUT, "sent  {$row['ceo_name']} <{$row['ceo_email']}> ({$row['council_area']})\n");
        } else {
            $failedCount++;
            fwrite(STDOUT, "FAILED {$row['ceo_name']} <{$row['ceo_email']}> ({$row['council_area']}) — {$result['error']}\n");
        }
    }
}

fwrite(STDOUT, sprintf("\nDone. Sent: %d. Failed: %d.\n", $sentCount, $failedCount));
if ($failedCount > 0 && !$isTestMode) {
    fwrite(STDOUT, "Re-run the same command to retry failed sends — successful ones weren't touched.\n");
}
