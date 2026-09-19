#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * One-off sender for the stakeholder notification — a courtesy heads-up to sympathetic
 * sector organisations about the accountability campaign, not an ongoing mail-merge.
 *
 * Sends one email per row in data/stakeholder-contacts.csv via the Resend batch API,
 * logged to stakeholder_notifications on the PRODUCTION database (via campaign_db()).
 * Re-running is safe — already-sent rows are skipped.
 *
 * RUN THIS ONLY FROM THE COMMAND LINE.
 *
 * Usage:
 *   php bin/send-stakeholder-notifications.php --dry-run
 *   php bin/send-stakeholder-notifications.php --test-to=you@example.com --test-count=3
 *   php bin/send-stakeholder-notifications.php
 *     A real send to every not-yet-sent organisation. Asks for interactive
 *     confirmation first unless --yes is also passed.
 *
 * Flags:
 *   --dry-run          Render only, no network calls, no DB writes.
 *   --test-to=EMAIL     Redirect every send in this run to EMAIL (implies test mode).
 *   --test-count=N      How many real rows to use when testing (default 3).
 *   --yes               Skip the interactive "type SEND" confirmation prompt.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("This script is for command-line use only.\n");
}

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/campaign-templates/stakeholder-notification.php';

const ROSTER_CSV_PATH  = __DIR__ . '/../data/stakeholder-contacts.csv';
const RESEND_BATCH_URL = 'https://api.resend.com/emails/batch';

$opts = getopt('', ['dry-run', 'yes', 'test-to:', 'test-count:']);

$dryRun      = array_key_exists('dry-run', $opts);
$skipConfirm = array_key_exists('yes', $opts);
$testTo      = isset($opts['test-to']) ? trim((string) $opts['test-to']) : null;
$testCount   = isset($opts['test-count']) ? max(1, (int) $opts['test-count']) : 3;

if ($testTo !== null && !filter_var($testTo, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "--test-to is not a valid email address.\n");
    exit(1);
}

function load_roster(string $path): array
{
    if (!is_readable($path)) {
        fwrite(STDERR, "Cannot read roster CSV at $path\n");
        exit(1);
    }
    $fh = fopen($path, 'r');
    $header = fgetcsv($fh, 0, ',', '"', '\\');
    $headerCount = count($header);
    $rows = [];
    while (($line = fgetcsv($fh, 0, ',', '"', '\\')) !== false) {
        if (count($line) < $headerCount) continue;
        if (count($line) > $headerCount) {
            $overflow = array_splice($line, $headerCount - 1);
            $line[] = implode(',', $overflow);
        }
        $row = array_combine($header, $line);
        $email = trim((string) ($row['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            continue; // skips "form only" rows with no real address
        }
        $rows[] = $row;
    }
    fclose($fh);
    return $rows;
}

function resend_send_batch(string $apiKey, array $emails): array
{
    $ch = curl_init(RESEND_BATCH_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $apiKey, 'Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($emails, JSON_UNESCAPED_SLASHES),
        CURLOPT_TIMEOUT => 30,
    ]);
    $responseBody = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    if ($responseBody === false) {
        return ['ok' => false, 'error' => "cURL error: $curlError", 'ids' => []];
    }
    $decoded = json_decode((string) $responseBody, true);
    if ($httpCode < 200 || $httpCode >= 300) {
        $message = is_array($decoded) && isset($decoded['message']) ? (string) $decoded['message'] : "HTTP $httpCode: $responseBody";
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

function already_sent_emails(): array
{
    $stmt = campaign_db()->query('SELECT email FROM stakeholder_notifications WHERE status = "sent"');
    return array_map('strtolower', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

function log_send_result(array $row, string $status, ?string $resendId, ?string $error, string $subject, string $bodyText): void
{
    $stmt = campaign_db()->prepare(
        'INSERT INTO stakeholder_notifications (organisation, contact_type, email, status, resend_id, error_message, subject, body_text)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE status = VALUES(status), resend_id = VALUES(resend_id), error_message = VALUES(error_message),
            subject = VALUES(subject), body_text = VALUES(body_text), sent_at = CURRENT_TIMESTAMP'
    );
    $stmt->execute([$row['organisation'], $row['contact_type'], $row['email'], $status, $resendId, $error, $subject, $bodyText]);
}

$fromName  = env_raw('CAMPAIGN_FROM_NAME') ?: 'WIRES';
$fromEmail = env_raw('CAMPAIGN_FROM_EMAIL') ?: 'hello@wires.org.uk';
$from      = "$fromName <$fromEmail>";
$apiKey    = env_raw('RESEND_API_KEY');

$roster = load_roster(ROSTER_CSV_PATH);
fwrite(STDERR, sprintf("Loaded %d roster rows with a usable email (form-only rows skipped).\n", count($roster)));

$isTestMode = $testTo !== null;

if ($dryRun) {
    foreach ($roster as $row) {
        fwrite(STDOUT, str_repeat('=', 70) . "\n");
        fwrite(STDOUT, "TO: {$row['organisation']} <{$row['email']}> ({$row['contact_type']})\n");
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
    $alreadySent = already_sent_emails();
    $doNotContact = do_not_contact_emails();
    $recipients = array_values(array_filter(
        $roster,
        fn(array $row) => !in_array(strtolower($row['email']), $alreadySent, true)
            && !in_array(strtolower($row['email']), $doNotContact, true)
    ));
    fwrite(STDERR, sprintf(
        "%d already sent; %d on the do-not-contact list; %d remaining.\n",
        count($alreadySent), count($doNotContact), count($recipients)
    ));
}

if (empty($recipients)) {
    fwrite(STDOUT, "Nothing to send — no matching recipients.\n");
    exit(0);
}

if (!$skipConfirm) {
    fwrite(STDERR, sprintf(
        "\nAbout to send %d %s email(s) as %s.\nType SEND to continue, anything else to abort: ",
        count($recipients), $isTestMode ? '[TEST]' : 'REAL', $from
    ));
    $confirmation = trim((string) fgets(STDIN));
    if ($confirmation !== 'SEND') {
        fwrite(STDERR, "Aborted — nothing sent.\n");
        exit(1);
    }
}

$payload = [];
$renderedSubjects = [];
$renderedTexts = [];
foreach ($recipients as $i => $row) {
    $subject = render_subject($row);
    $text = render_text_body($row);
    $renderedSubjects[$i] = $subject;
    $renderedTexts[$i] = $text;
    if ($isTestMode) {
        $subject = "[TEST - would go to {$row['organisation']}, {$row['email']}] $subject";
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

$sentCount = 0;
$failedCount = 0;
foreach ($recipients as $i => $row) {
    if ($result['ok']) {
        $resendId = $result['ids'][$i] ?? null;
        if (!$isTestMode) {
            log_send_result($row, 'sent', $resendId, null, $renderedSubjects[$i], $renderedTexts[$i]);
        }
        $sentCount++;
        fwrite(STDOUT, "sent  {$row['organisation']} <{$row['email']}>\n");
    } else {
        if (!$isTestMode) {
            log_send_result($row, 'failed', null, $result['error'], $renderedSubjects[$i], $renderedTexts[$i]);
        }
        $failedCount++;
        fwrite(STDOUT, "FAILED {$row['organisation']} <{$row['email']}> — {$result['error']}\n");
    }
}

fwrite(STDOUT, sprintf("\nDone. Sent: %d. Failed: %d.\n", $sentCount, $failedCount));
