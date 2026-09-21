#!/usr/bin/env php
<?php

declare(strict_types=1);

use function Wires\MspTemplate\render_subject;
use function Wires\MspTemplate\render_text_body;
use function Wires\MspTemplate\render_html_body;

/**
 * Mail-merge sender for the MSP accountability campaign.
 *
 * Sends one personalised email per row in data/msp-roster.csv (129 MSPs, all sourced
 * directly from parliament.scot's own current-members listing — no third-party
 * aggregators), via the Resend batch API. Every send is logged to msp_campaign_sends
 * so re-running this script is safe — already-sent rows are skipped, only rows that
 * previously failed are retried.
 *
 * RUN THIS ONLY FROM THE COMMAND LINE. There is no web trigger for this script on
 * purpose — a mass send to real elected officials must never be one accidental page
 * load away.
 *
 * Usage:
 *   php bin/send-msp-campaign.php --dry-run
 *     Renders the first few emails to STDOUT. Sends nothing, touches no DB rows.
 *
 *   php bin/send-msp-campaign.php --test-to=you@example.com --test-count=5
 *     Sends 5 real merge-personalised emails (using 5 real roster rows) but all
 *     addressed to --test-to instead of the real MSP. Subject is prefixed "[TEST]"
 *     so it's unmistakable in an inbox. Never touches the DB.
 *
 *   php bin/send-msp-campaign.php --limit=20
 *     A real send, capped at the first 20 not-yet-sent MSPs. Asks for interactive
 *     confirmation first unless --yes is also passed.
 *
 *   php bin/send-msp-campaign.php
 *     A real send to every not-yet-sent MSP (129 total). Asks for interactive
 *     confirmation first unless --yes is also passed.
 *
 * Flags:
 *   --dry-run          Render only, no network calls, no DB writes.
 *   --test-to=EMAIL     Redirect every send in this run to EMAIL (implies test mode).
 *   --test-count=N      How many real roster rows to use when testing (default 5).
 *   --limit=N           Cap how many real recipients to send to this run.
 *   --yes               Skip the interactive "type SEND" confirmation prompt.
 *   --campaign=SLUG      Override the campaign_slug used for dedup (default below).
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("This script is for command-line use only.\n");
}

require_once __DIR__ . '/../includes/bootstrap.php';

const DEFAULT_CAMPAIGN_SLUG = 'msp-accountability-2026-09';
const ROSTER_CSV_PATH       = __DIR__ . '/../data/msp-roster.csv';
const RESEND_BATCH_URL      = 'https://api.resend.com/emails/batch';
const BATCH_SIZE            = 100;
const SECONDS_BETWEEN_BATCHES = 1;

// ─── Argument parsing ────────────────────────────────────────────────────────

$opts = getopt('', ['dry-run', 'yes', 'test-to:', 'test-count:', 'limit:', 'campaign:']);

$dryRun       = array_key_exists('dry-run', $opts);
$skipConfirm  = array_key_exists('yes', $opts);
$testTo       = isset($opts['test-to']) ? trim((string) $opts['test-to']) : null;
$testCount    = isset($opts['test-count']) ? max(1, (int) $opts['test-count']) : 5;
$limit        = isset($opts['limit']) ? max(1, (int) $opts['limit']) : null;
$campaignSlug = isset($opts['campaign']) ? trim((string) $opts['campaign']) : DEFAULT_CAMPAIGN_SLUG;

if ($testTo !== null && !filter_var($testTo, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "--test-to is not a valid email address.\n");
    exit(1);
}

// ─── Email template ──────────────────────────────────────────────────────────
// render_subject()/render_text_body()/render_html_body() live in
// includes/campaign-templates/msp-accountability.php, under the Wires\MspTemplate
// namespace (so this file and send-mp-campaign.php can coexist without a name clash
// when both templates are rendered on the same page, e.g. accountability-campaign.php).

require_once __DIR__ . '/../includes/campaign-templates/msp-accountability.php';

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
        if (trim((string) ($row['email'] ?? '')) === '') {
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

function already_sent_emails(string $campaignSlug): array
{
    $stmt = campaign_db()->prepare(
        'SELECT email FROM msp_campaign_sends WHERE campaign_slug = ? AND status = ?'
    );
    $stmt->execute([$campaignSlug, 'sent']);
    return array_map('strtolower', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

function log_send_result(string $campaignSlug, array $row, string $status, ?string $resendId, ?string $error, string $subject, string $bodyText): void
{
    $stmt = campaign_db()->prepare(
        'INSERT INTO msp_campaign_sends
            (campaign_slug, full_name, party, role, email, status, resend_id, error_message, subject, body_text)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            status = VALUES(status),
            resend_id = VALUES(resend_id),
            error_message = VALUES(error_message),
            subject = VALUES(subject),
            body_text = VALUES(body_text),
            sent_at = CURRENT_TIMESTAMP'
    );
    $stmt->execute([$campaignSlug, $row['name'], $row['party'], $row['role'], $row['email'], $status, $resendId, $error, $subject, $bodyText]);
}

// ─── Main ────────────────────────────────────────────────────────────────────

$fromName  = env_raw('CAMPAIGN_FROM_NAME') ?: 'WIRES';
$fromEmail = env_raw('CAMPAIGN_FROM_EMAIL') ?: 'hello@wires.org.uk';
$from      = "$fromName <$fromEmail>";
$apiKey    = env_raw('RESEND_API_KEY');

$roster = load_roster(ROSTER_CSV_PATH);
fwrite(STDERR, sprintf("Loaded %d roster rows with an email.\n", count($roster)));

$isTestMode = $testTo !== null;

if ($dryRun) {
    $sample = array_slice($roster, 0, min(3, count($roster)));
    foreach ($sample as $row) {
        fwrite(STDOUT, str_repeat('=', 70) . "\n");
        fwrite(STDOUT, "TO: {$row['name']} <{$row['email']}> ({$row['role']})\n");
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
    $alreadySent = already_sent_emails($campaignSlug);
    $doNotContact = do_not_contact_emails();
    $recipients = array_values(array_filter(
        $roster,
        fn(array $row) => !in_array(strtolower($row['email']), $alreadySent, true)
            && !in_array(strtolower($row['email']), $doNotContact, true)
    ));
    fwrite(STDERR, sprintf(
        "%d already sent for campaign '%s'; %d on the do-not-contact list; %d remaining.\n",
        count($alreadySent),
        $campaignSlug,
        count($doNotContact),
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
            $subject = "[TEST - would go to {$row['name']}, {$row['email']}] $subject";
        }
        $payload[] = [
            'from' => $from,
            'to' => [$isTestMode ? $testTo : $row['email']],
            'subject' => $subject,
            'html' => render_html_body($row),
            'text' => $text,
        ];
    }

    $result = resend_send_batch($apiKey, $payload);

    foreach ($batch as $i => $row) {
        if ($result['ok']) {
            $resendId = $result['ids'][$i] ?? null;
            if (!$isTestMode) {
                log_send_result($campaignSlug, $row, 'sent', $resendId, null, $renderedSubjects[$i], $renderedTexts[$i]);
            }
            $sentCount++;
            fwrite(STDOUT, "sent  {$row['name']} <{$row['email']}> ({$row['role']})\n");
        } else {
            if (!$isTestMode) {
                log_send_result($campaignSlug, $row, 'failed', null, $result['error'], $renderedSubjects[$i], $renderedTexts[$i]);
            }
            $failedCount++;
            fwrite(STDOUT, "FAILED {$row['name']} <{$row['email']}> — {$result['error']}\n");
        }
    }

    if (count($recipients) > BATCH_SIZE) {
        sleep(SECONDS_BETWEEN_BATCHES);
    }
}

fwrite(STDOUT, sprintf("\nDone. Sent: %d. Failed: %d.\n", $sentCount, $failedCount));
if ($failedCount > 0 && !$isTestMode) {
    fwrite(STDOUT, "Re-run the same command to retry failed sends — successful ones will be skipped.\n");
}
