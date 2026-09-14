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

/** Aberdeen City's CE post is interim, mid-recruitment — address the office, not a name that may change within days. */
function render_greeting(array $row): string
{
    if ($row['council_area'] === 'Aberdeen City') {
        return "Chief Executive's Office";
    }
    return $row['ceo_name'];
}

function render_subject(array $row): string
{
    return "Is {$row['council_area']}'s digital service design leaving people behind?";
}

function render_text_body(array $row): string
{
    $council  = $row['council_area'];
    $greeting = render_greeting($row);
    $replyBy  = reply_by_date();

    return <<<TXT
Dear {$greeting},

We treat roads and water as essential infrastructure — something no one is
quietly left to go without. Connectivity deserves the same status, and Audit
Scotland's August 2024 review found no clear delivery plan for tackling
digital exclusion in Scotland, and no one named as accountable for producing
one.

The numbers are moving the wrong way: Citizens Advice Scotland recorded a 59%
rise in digitally excluded clients in two years (1,932 to 3,065), and one in
six Scottish adults lack basic digital skills — which is exactly why a
non-digital route for essential services isn't a nice-to-have.

We're WIRES, campaigning for connectivity to be treated as essential
infrastructure across all 32 council areas, including {$council}. We're
writing in parallel to all councillors, asking them to speak publicly on
this — we wanted council leadership to have the same visibility into where
things stand operationally. Rather than a general ask of you, we'd like to
know where {$council} stands on four practical questions — about day-to-day
practice, not just policy:

1. Staff and service design — Are staff trained to recognise when someone
   can't easily get online, and does every essential council service still
   have a working non-digital route, so no resident is cut off by skills,
   hardware, or address?

2. Affordability and access — What is {$council} doing to promote the
   discounted broadband social tariff (only around one in twelve eligible
   households currently use it), and what local options — public wifi
   points, community networks, library wifi — exist for residents who can't
   otherwise afford a home connection?

3. Built for unreliable connections — Do council digital services,
   especially anything residents rely on for e-learning, benefits, or
   housing, avoid unnecessary streaming and data costs and use reasonable
   file sizes? And if a connection drops mid-task, is that work saved
   automatically — for residents and staff alike — rather than lost?

4. Ownership — Audit Scotland's own review quoted Accounts Commission member
   Nichola Brown saying councils "must be clearer about how they will reduce
   digital exclusion in their local area." Is there a published digital
   inclusion action plan for {$council}, with someone named as accountable
   for it?

Could you reply by {$replyBy} — either with a short statement we can quote
publicly, or a summary of where {$council} stands, so we record it
accurately?

See what we've found so far, council-by-council:
https://wires.org.uk/council-replies

Thank you for your time,
William Ellis
WIRES — wires.org.uk

If you'd rather not hear from us again, just reply and say so.
TXT;
}

function render_html_body(array $row): string
{
    $council  = e($row['council_area']);
    $greeting = e(render_greeting($row));
    $replyBy  = e(reply_by_date());

    return <<<HTML
<div style="font-family:-apple-system,Helvetica,Arial,sans-serif;font-size:15px;line-height:1.6;color:#1a1a1a;max-width:600px">
<p>Dear {$greeting},</p>
<p>We treat roads and water as essential infrastructure — something no one is quietly left to go without. Connectivity deserves the same status, and Audit Scotland's August 2024 review found no clear delivery plan for tackling digital exclusion in Scotland, and no one named as accountable for producing one.</p>
<p>The numbers are moving the wrong way: Citizens Advice Scotland recorded a 59% rise in digitally excluded clients in two years (1,932 to 3,065), and one in six Scottish adults lack basic digital skills — which is exactly why a non-digital route for essential services isn't a nice-to-have.</p>
<p>We're WIRES, campaigning for connectivity to be treated as essential infrastructure across all 32 council areas, including {$council}. We're writing in parallel to all councillors, asking them to speak publicly on this — we wanted council leadership to have the same visibility into where things stand operationally. Rather than a general ask of you, we'd like to know where {$council} stands on four practical questions — about day-to-day practice, not just policy:</p>
<ol>
<li><strong>Staff and service design</strong> — Are staff trained to recognise when someone can't easily get online, and does every essential council service still have a working non-digital route, so no resident is cut off by skills, hardware, or address?</li>
<li><strong>Affordability and access</strong> — What is {$council} doing to promote the discounted broadband social tariff (only around one in twelve eligible households currently use it), and what local options — public wifi points, community networks, library wifi — exist for residents who can't otherwise afford a home connection?</li>
<li><strong>Built for unreliable connections</strong> — Do council digital services, especially anything residents rely on for e-learning, benefits, or housing, avoid unnecessary streaming and data costs and use reasonable file sizes? And if a connection drops mid-task, is that work saved automatically — for residents and staff alike — rather than lost?</li>
<li><strong>Ownership</strong> — Audit Scotland's own review quoted Accounts Commission member Nichola Brown saying councils "must be clearer about how they will reduce digital exclusion in their local area." Is there a published digital inclusion action plan for {$council}, with someone named as accountable for it?</li>
</ol>
<p><strong>Could you reply by {$replyBy}</strong> — either with a short statement we can quote publicly, or a summary of where {$council} stands, so we record it accurately?</p>
<p><a href="https://wires.org.uk/council-replies">See what we've found so far, council-by-council &rarr;</a></p>
<p>Thank you for your time,<br>William Ellis<br>WIRES — <a href="https://wires.org.uk">wires.org.uk</a></p>
<p style="font-size:13px;color:#666">If you'd rather not hear from us again, just reply and say so.</p>
</div>
HTML;
}

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
