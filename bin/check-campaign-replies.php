#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Checks the hello@wires.org.uk inbox (via the Hostinger Mail API) for replies from
 * anyone any campaign has emailed — councillors, MSPs, MPs, or stakeholder
 * notifications — and auto-logs them against whichever table they came from.
 *
 * Matching is by sender address only — a reply's From: address has to match an email
 * already logged as sent, with no real reply logged yet, in one of the tables below.
 * Out-of-office and "your email has been received" messages (see is_auto_reply()) are
 * logged with reply_is_auto = 1 and the person stays watched for a genuine reply:
 *   - councillor_campaign_sends
 *   - msp_campaign_sends
 *   - mp_campaign_sends
 *   - stakeholder_notifications
 * Not thread-aware (no In-Reply-To checking), which is a deliberate simplification:
 * good enough to catch real replies, not meant to be exact.
 *
 * RUN THIS ONLY FROM THE COMMAND LINE.
 *
 * Usage:
 *   php bin/check-campaign-replies.php            Check inbox, auto-log matches.
 *   php bin/check-campaign-replies.php --dry-run   Show what would be logged, write nothing.
 *   php bin/check-campaign-replies.php --days=21   Look further back than the default 4 days.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("This script is for command-line use only.\n");
}

require_once __DIR__ . '/../includes/bootstrap.php';

const MAIL_API_BASE        = 'https://api.mail.hostinger.com';
const MAILBOX_RESOURCE_ID  = 'ACc3439df9a45aede1974574892aef'; // hello@wires.org.uk
const LOOKBACK_DAYS        = 4; // overlap window so a slow/missed run never loses a reply
const REPLY_NOTE_MAX_CHARS = 2000;

$dryRun = in_array('--dry-run', $argv, true);

$lookbackDays = LOOKBACK_DAYS;
foreach ($argv as $arg) {
    if (preg_match('/^--days=(\d+)$/', $arg, $m)) {
        $lookbackDays = max(1, (int) $m[1]);
    }
}

$token = env_raw('HOSTINGER_MAIL_API_TOKEN');
if ($token === null || $token === '') {
    fwrite(STDERR, "HOSTINGER_MAIL_API_TOKEN is not set in .env — cannot check replies.\n");
    exit(1);
}

if (!campaign_db_available()) {
    fwrite(STDERR, "Campaign database is not reachable.\n");
    exit(1);
}

// ─── Who are we waiting to hear back from? ───────────────────────────────────
// Pulled from every table a reply could land against, each tagged with its own
// source table + a display label, so one pass over the inbox covers all of them.

// People who've only sent an automatic acknowledgement stay on the watch list, so a
// real reply that arrives days later still gets picked up.
$sources = [
    'councillor_campaign_sends' => "CONCAT(full_name, ' (', council_area, ')')",
    'msp_campaign_sends'        => "CONCAT(full_name, ' (', COALESCE(role, ''), ')')",
    'mp_campaign_sends'         => "CONCAT(full_name, ' (', COALESCE(constituency, ''), ')')",
    'stakeholder_notifications' => 'organisation',
];

$pending = [];

foreach ($sources as $table => $labelSql) {
    $stmt = campaign_db()->query(
        "SELECT id, email, replied_at, reply_notes, $labelSql AS label
         FROM $table WHERE status = 'sent' AND (replied_at IS NULL OR reply_is_auto = 1)"
    );
    foreach ($stmt->fetchAll() as $row) {
        $pending[strtolower($row['email'])] = [
            'table'        => $table,
            'id'           => $row['id'],
            'email'        => $row['email'],
            'label'        => $row['label'],
            'has_auto_ack' => $row['replied_at'] !== null,
            'notes'        => (string) $row['reply_notes'],
        ];
    }
}

if (empty($pending)) {
    fwrite(STDOUT, "Nothing pending a reply — every sent email already has a real reply logged (or nothing's been sent yet).\n");
    exit(0);
}

fwrite(STDERR, sprintf("Watching for replies from %d people who haven't replied yet.\n", count($pending)));

// ─── Fetch recent inbox messages ─────────────────────────────────────────────

function mail_api_request(string $token, string $method, string $path, ?array $body = null): array
{
    $ch = curl_init(MAIL_API_BASE . $path);
    $headers = [
        'Authorization: Bearer ' . $token,
        'Accept: application/json',
    ];
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_TIMEOUT => 30,
    ];
    if ($body !== null) {
        $headers[] = 'Content-Type: application/json';
        $opts[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_SLASHES);
    }
    $opts[CURLOPT_HTTPHEADER] = $headers;
    curl_setopt_array($ch, $opts);

    $responseBody = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);

    if ($responseBody === false) {
        return ['ok' => false, 'error' => "cURL error: $curlError"];
    }
    $decoded = json_decode((string) $responseBody, true);
    if ($httpCode < 200 || $httpCode >= 300) {
        $message = is_array($decoded) && isset($decoded['message']) ? (string) $decoded['message'] : "HTTP $httpCode";
        return ['ok' => false, 'error' => $message];
    }
    return ['ok' => true, 'data' => $decoded];
}

// The Mail API's `since` filter only accepts a plain Y-m-d date, not a full timestamp.
$since = (new DateTime('-' . $lookbackDays . ' days'))->format('Y-m-d');

$messages = [];
$page = 1;
do {
    $searchPath = '/api/v1/mailboxes/' . MAILBOX_RESOURCE_ID . '/folders/' . rawurlencode('INBOX') . "/messages/search?perPage=100&page=$page&sort=-date";
    $result = mail_api_request($token, 'POST', $searchPath, ['since' => $since]);
    if (!$result['ok']) {
        fwrite(STDERR, "Failed to search inbox: {$result['error']}\n");
        exit(1);
    }
    $messages = array_merge($messages, $result['data']['data'] ?? []);
    $totalPages = (int) ($result['data']['pagination']['totalPages'] ?? 1);
    $page++;
} while ($page <= $totalPages);

fwrite(STDERR, sprintf("Found %d inbox message(s) in the last %d days.\n", count($messages), $lookbackDays));

// ─── Match senders against pending replies ───────────────────────────────────

$loggedCount = 0;

foreach ($messages as $msg) {
    $fromAddress = strtolower((string) ($msg['from']['address'] ?? ''));
    if ($fromAddress === '' || !isset($pending[$fromAddress])) {
        continue;
    }

    $row = $pending[$fromAddress];
    $uid = (int) ($msg['uid'] ?? 0);
    $subject = (string) ($msg['subject'] ?? '(no subject)');
    $repliedDate = isset($msg['date']) ? substr((string) $msg['date'], 0, 10) : date('Y-m-d');
    $messageId = (string) ($msg['messageId'] ?? '');

    // Already filed (by an earlier run, or by hand) — leave it alone, so a manual
    // "this is only an acknowledgement" decision isn't overwritten by a later sweep.
    if ($messageId !== '' && str_contains($row['notes'], $messageId)) {
        continue;
    }

    $snippet = '';
    if ($uid > 0) {
        $textPath = '/api/v1/mailboxes/' . MAILBOX_RESOURCE_ID . '/folders/' . rawurlencode('INBOX') . '/messages/' . $uid . '/text';
        $textResult = mail_api_request($token, 'GET', $textPath);
        if ($textResult['ok']) {
            $snippet = trim((string) ($textResult['data']['data']['text'] ?? ''));
            if (mb_strlen($snippet) > REPLY_NOTE_MAX_CHARS) {
                $snippet = mb_substr($snippet, 0, REPLY_NOTE_MAX_CHARS) . '…';
            }
        }
    }

    $isAuto = is_auto_reply($subject, $snippet);

    // Already have an acknowledgement on file — another one adds nothing.
    if ($isAuto && $row['has_auto_ack']) {
        continue;
    }

    $note = "Auto-detected reply — subject: \"$subject\"\n\n" . $snippet
        . ($messageId !== '' ? "\n\n[message-id: $messageId]" : '');
    $kind = $isAuto ? 'ACK  ' : 'REPLY';

    fwrite(STDOUT, "$kind  {$row['label']} <{$row['email']}> [{$row['table']}] — $repliedDate\n");

    if (!$dryRun) {
        campaign_db()->prepare(
            "UPDATE {$row['table']} SET replied_at = :replied_at, reply_is_auto = :is_auto, reply_notes = :reply_notes WHERE id = :id"
        )->execute(['replied_at' => $repliedDate, 'is_auto' => $isAuto ? 1 : 0, 'reply_notes' => $note, 'id' => $row['id']]);
    }

    if ($isAuto) {
        $pending[$fromAddress]['has_auto_ack'] = true;
    } else {
        // A real reply closes this person out; ignore any further messages from them this run.
        unset($pending[$fromAddress]);
    }

    $loggedCount++;
}

fwrite(STDOUT, sprintf(
    "\n%s %d repl%s.\n",
    $dryRun ? '[dry-run] Would log' : 'Logged',
    $loggedCount,
    $loggedCount === 1 ? 'y' : 'ies'
));
