#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Mail-merge sender for the councillor public-statement campaign.
 *
 * Sends one personalised email per confirmed row in data/councillors-roster.csv,
 * via the Resend batch API. Every send is logged to councillor_campaign_sends so
 * re-running this script is safe — already-sent rows are skipped, only rows that
 * previously failed are retried.
 *
 * RUN THIS ONLY FROM THE COMMAND LINE. There is no web trigger for this script
 * on purpose — a mass send to real elected officials must never be one accidental
 * page load away.
 *
 * Usage:
 *   php bin/send-councillor-campaign.php --dry-run
 *     Renders the first few emails to STDOUT. Sends nothing, touches no DB rows.
 *
 *   php bin/send-councillor-campaign.php --test-to=you@example.com --test-count=5
 *     Sends 5 real merge-personalised emails (using 5 real roster rows) but all
 *     addressed to --test-to instead of the real councillor. Subject is prefixed
 *     "[TEST]" so it's unmistakable in an inbox. Never touches the DB.
 *
 *   php bin/send-councillor-campaign.php --limit=20
 *     A real send, capped at the first 20 confirmed, not-yet-sent councillors.
 *     Asks for interactive confirmation first unless --yes is also passed.
 *
 *   php bin/send-councillor-campaign.php
 *     A real send to every confirmed, not-yet-sent councillor in the roster
 *     (currently ~1,200 people). Asks for interactive confirmation first
 *     unless --yes is also passed.
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

const DEFAULT_CAMPAIGN_SLUG = 'councillor-public-statement-2026-09';
const ROSTER_CSV_PATH       = __DIR__ . '/../data/councillors-roster.csv';
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

/**
 * Reply-by date, roughly 8 weeks out from whenever this actually sends — the
 * campaign runs across several days (100/day Resend quota), so this is computed
 * fresh each run rather than a fixed string. Not exact business-day counting,
 * just nudged off a weekend if it happens to land on one.
 */
function reply_by_date(): string
{
    $date = new DateTime('+8 weeks');
    $weekday = (int) $date->format('N'); // 1=Mon ... 6=Sat, 7=Sun
    if ($weekday === 6) {
        $date->modify('+2 days');
    } elseif ($weekday === 7) {
        $date->modify('+1 day');
    }
    return $date->format('l j F');
}

function render_subject(array $row): string
{
    return "Will you back reliable connectivity for {$row['council']}?";
}

function render_text_body(array $row): string
{
    $council = $row['council'];
    $name    = $row['full_name'];
    $replyBy = reply_by_date();

    return <<<TXT
Dear Councillor {$name},

We treat roads and water as essential infrastructure — something no one is
quietly left to go without. Connectivity deserves the same status, and right
now Scotland doesn't give it that.

Audit Scotland's August 2024 review found no clear delivery plan for tackling
digital exclusion, and no one named as accountable for producing one. Since
then, the Scottish Government has published a Digital Strategy vision
statement and an initial delivery plan (November 2025) — but that plan covers
digital public services, not digital exclusion, and the vision statement's
own Performance Framework is still described as "being refreshed." As SCVO
noted last year, Scotland is still waiting for "strategy, delivery plan and
visible leadership" on digital exclusion.

Meanwhile the numbers are moving the wrong way:
- Citizens Advice Scotland recorded a 59% rise in digitally excluded clients
  in two years (1,932 to 3,065)
- One in six Scottish adults lack basic digital skills
- Only around one in twelve eligible households use the discounted broadband
  social tariff — most have never heard it exists

We're WIRES, campaigning for connectivity to be treated as essential
infrastructure across all 32 council areas, including {$council}. We're
asking councillors to say publicly that everyone in their ward deserves
reliable, affordable connectivity — and that someone needs to be named
responsible for closing this gap.

Could you reply by {$replyBy} with either:
1. A short line we can quote, publicly attributed to you, or
2. Where {$council} already stands, so we record it accurately

See what we've found so far, council-by-council: https://wires.org.uk/accountability

Thank you for your time,
William Ellis
WIRES — wires.org.uk

If you'd rather not hear from us again, just reply and say so.
TXT;
}

function render_html_body(array $row): string
{
    $council = e($row['council']);
    $name    = e($row['full_name']);
    $replyBy = e(reply_by_date());

    return <<<HTML
<div style="font-family:-apple-system,Helvetica,Arial,sans-serif;font-size:15px;line-height:1.6;color:#1a1a1a;max-width:600px">
<p>Dear Councillor {$name},</p>
<p>We treat roads and water as essential infrastructure — something no one is quietly left to go without. Connectivity deserves the same status, and right now Scotland doesn't give it that.</p>
<p>Audit Scotland's August 2024 review found no clear delivery plan for tackling digital exclusion, and no one named as accountable for producing one. Since then, the Scottish Government has published a Digital Strategy vision statement and an initial delivery plan (November 2025) — but that plan covers digital public services, not digital exclusion, and the vision statement's own Performance Framework is still described as "being refreshed." As SCVO noted last year, Scotland is still waiting for "strategy, delivery plan and visible leadership" on digital exclusion.</p>
<p>Meanwhile the numbers are moving the wrong way:</p>
<ul>
<li>Citizens Advice Scotland recorded a 59% rise in digitally excluded clients in two years (1,932 to 3,065)</li>
<li>One in six Scottish adults lack basic digital skills</li>
<li>Only around one in twelve eligible households use the discounted broadband social tariff — most have never heard it exists</li>
</ul>
<p>We're WIRES, campaigning for connectivity to be treated as essential infrastructure across all 32 council areas, including {$council}. We're asking councillors to say publicly that everyone in their ward deserves reliable, affordable connectivity — and that someone needs to be named responsible for closing this gap.</p>
<p><strong>Could you reply by {$replyBy} with either:</strong></p>
<ol>
<li>A short line we can quote, publicly attributed to you, or</li>
<li>Where {$council} already stands, so we record it accurately</li>
</ol>
<p><a href="https://wires.org.uk/accountability">See what we've found so far, council-by-council &rarr;</a></p>
<p>Thank you for your time,<br>William Ellis<br>WIRES — <a href="https://wires.org.uk">wires.org.uk</a></p>
<p style="font-size:13px;color:#666">If you'd rather not hear from us again, just reply and say so.</p>
</div>
HTML;
}

// ─── Roster loading (mirrors admin/councillors.php's CSV parsing) ───────────

function load_confirmed_roster(string $path): array
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
        if (($row['confidence'] ?? '') !== 'confirmed') {
            continue;
        }
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
        'SELECT email FROM councillor_campaign_sends WHERE campaign_slug = ? AND status = ?'
    );
    $stmt->execute([$campaignSlug, 'sent']);
    return array_map('strtolower', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

function log_send_result(string $campaignSlug, array $row, string $status, ?string $resendId, ?string $error, string $subject, string $bodyText): void
{
    $stmt = campaign_db()->prepare(
        'INSERT INTO councillor_campaign_sends
            (campaign_slug, full_name, council_area, email, status, resend_id, error_message, subject, body_text)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE
            status = VALUES(status),
            resend_id = VALUES(resend_id),
            error_message = VALUES(error_message),
            subject = VALUES(subject),
            body_text = VALUES(body_text),
            sent_at = CURRENT_TIMESTAMP'
    );
    $stmt->execute([$campaignSlug, $row['full_name'], $row['council'], $row['email'], $status, $resendId, $error, $subject, $bodyText]);
}

// ─── Main ────────────────────────────────────────────────────────────────────

$fromName  = env_raw('CAMPAIGN_FROM_NAME') ?: 'WIRES';
$fromEmail = env_raw('CAMPAIGN_FROM_EMAIL') ?: 'hello@wires.org.uk';
$from      = "$fromName <$fromEmail>";
$apiKey    = env_raw('RESEND_API_KEY');

$roster = load_confirmed_roster(ROSTER_CSV_PATH);
fwrite(STDERR, sprintf("Loaded %d confirmed roster rows with an email.\n", count($roster)));

$isTestMode = $testTo !== null;

if ($dryRun) {
    $sample = array_slice($roster, 0, min(3, count($roster)));
    foreach ($sample as $row) {
        fwrite(STDOUT, str_repeat('=', 70) . "\n");
        fwrite(STDOUT, "TO: {$row['full_name']} <{$row['email']}> ({$row['council']})\n");
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
    $recipients = array_values(array_filter(
        $roster,
        fn(array $row) => !in_array(strtolower($row['email']), $alreadySent, true)
    ));
    fwrite(STDERR, sprintf(
        "%d already sent for campaign '%s'; %d remaining.\n",
        count($alreadySent),
        $campaignSlug,
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
            $subject = "[TEST - would go to {$row['full_name']}, {$row['email']}] $subject";
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
            fwrite(STDOUT, "sent  {$row['full_name']} <{$row['email']}> ({$row['council']})\n");
        } else {
            if (!$isTestMode) {
                log_send_result($campaignSlug, $row, 'failed', null, $result['error'], $renderedSubjects[$i], $renderedTexts[$i]);
            }
            $failedCount++;
            fwrite(STDOUT, "FAILED {$row['full_name']} <{$row['email']}> — {$result['error']}\n");
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
