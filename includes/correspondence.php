<?php

declare(strict_types=1);

/**
 * Correspondence log: shared labels for /admin/correspondence*.php, and the importer
 * that copies real (non-automatic) emails from the hello@wires.org.uk mailbox into
 * correspondence_log. Requires includes/bootstrap.php.
 */

const CORRESPONDENCE_OWN_ADDRESS = 'hello@wires.org.uk';
const CORRESPONDENCE_MAILBOX_ID  = 'ACc3439df9a45aede1974574892aef';
const CORRESPONDENCE_BODY_MAX    = 20000;

const CORRESPONDENCE_CATEGORIES = [
    'councillor'  => 'Councillor',
    'council'     => 'Council',
    'msp'         => 'MSP',
    'mp'          => 'MP',
    'stakeholder' => 'Stakeholder',
    'regulator'   => 'Regulator',
    'public'      => 'Member of the public',
    'other'       => 'Other',
];

const CORRESPONDENCE_STATUSES = [
    'review' => 'To review',
    'open'   => 'Action needed',
    'done'   => 'Done',
];

function correspondence_mail_api(string $token, string $method, string $path, ?array $body = null): ?array
{
    $ch = curl_init('https://api.mail.hostinger.com' . $path);
    $headers = ['Authorization: Bearer ' . $token, 'Accept: application/json'];
    $opts = [CURLOPT_RETURNTRANSFER => true, CURLOPT_CUSTOMREQUEST => $method, CURLOPT_TIMEOUT => 30];
    if ($body !== null) {
        $headers[] = 'Content-Type: application/json';
        $opts[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_SLASHES);
    }
    $opts[CURLOPT_HTTPHEADER] = $headers;
    curl_setopt_array($ch, $opts);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($response === false || $code < 200 || $code >= 300) {
        return null;
    }
    $decoded = json_decode((string) $response, true);
    return is_array($decoded) ? $decoded : null;
}

/**
 * Everyone a campaign has written to, keyed by lowercased email, so an imported email
 * can be labelled with who the person is rather than just their address.
 */
function correspondence_known_people(PDO $pdo): array
{
    $people = [];
    $sources = [
        ["SELECT email, full_name AS person, CONCAT('Councillor, ', council_area) AS organisation FROM councillor_campaign_sends", 'councillor'],
        ["SELECT email, full_name AS person, role AS organisation FROM msp_campaign_sends", 'msp'],
        ["SELECT email, full_name AS person, CONCAT('MP for ', constituency) AS organisation FROM mp_campaign_sends", 'mp'],
        ["SELECT email, organisation AS person, organisation FROM stakeholder_notifications", 'stakeholder'],
        ["SELECT ceo_email AS email, ceo_name AS person, CONCAT('Chief Executive, ', council_area) AS organisation FROM council_contacts WHERE ceo_email IS NOT NULL", 'council'],
        ["SELECT leader_email AS email, leader_name AS person, CONCAT('Leader, ', council_area) AS organisation FROM council_contacts WHERE leader_email IS NOT NULL", 'council'],
    ];
    foreach ($sources as [$sql, $category]) {
        try {
            foreach ($pdo->query($sql)->fetchAll() as $row) {
                $email = strtolower(trim((string) $row['email']));
                if ($email !== '') {
                    $people[$email] = ['category' => $category, 'person' => (string) $row['person'], 'organisation' => (string) $row['organisation']];
                }
            }
        } catch (Throwable) {
        }
    }
    return $people;
}

function correspondence_category_for_domain(string $email): string
{
    $domain = strtolower(substr((string) strrchr($email, '@'), 1));
    return match (true) {
        in_array($domain, ['cma.gov.uk', 'ofcom.org.uk'], true) => 'regulator',
        $domain === 'parliament.scot'                           => 'msp',
        $domain === 'parliament.uk'                             => 'mp',
        str_ends_with($domain, '.gov.uk')                       => 'council',
        default                                                 => 'other',
    };
}

/**
 * Copies real emails from the last $days days into correspondence_log, as 'review'
 * entries for a person to summarise. Skips anything already logged (by Message-ID),
 * automatic replies, and contact-form notifications (those live in /admin/messages.php,
 * and can contain personal detail that shouldn't be duplicated here).
 * Returns the number of entries added (or that would be added, on a dry run).
 */
function import_correspondence(int $days, bool $dryRun, ?callable $report = null): int
{
    $token = env_raw('HOSTINGER_MAIL_API_TOKEN');
    if ($token === null || $token === '') {
        return 0;
    }
    $pdo = campaign_db();
    $known = correspondence_known_people($pdo);
    $logged = array_flip($pdo->query('SELECT message_id FROM correspondence_log WHERE message_id IS NOT NULL')->fetchAll(PDO::FETCH_COLUMN));
    // How each address was last logged, so a sent email (whose "To" header is often just an
    // address) carries the same name, role and type as the rest of that conversation.
    $seen = [];
    foreach ($pdo->query('SELECT email, person, organisation, category FROM correspondence_log WHERE email IS NOT NULL AND person <> email ORDER BY id') as $prior) {
        $seen[(string) $prior['email']] = ['person' => (string) $prior['person'], 'organisation' => $prior['organisation'], 'category' => (string) $prior['category']];
    }
    $since = (new DateTime("-$days days"))->format('Y-m-d');
    $rows = [];

    foreach (['INBOX', 'INBOX.Sent'] as $folder) {
        $base = '/api/v1/mailboxes/' . CORRESPONDENCE_MAILBOX_ID . '/folders/' . rawurlencode($folder) . '/messages';
        $page = 1;
        do {
            $result = correspondence_mail_api($token, 'POST', "$base/search?perPage=100&page=$page&sort=-date", ['since' => $since]);
            $messages = $result['data'] ?? [];
            $totalPages = (int) ($result['pagination']['totalPages'] ?? 1);

            foreach ($messages as $msg) {
                $messageId = (string) ($msg['messageId'] ?? '');
                if ($messageId === '' || isset($logged[$messageId])) {
                    continue;
                }
                $from = strtolower((string) ($msg['from']['address'] ?? ''));
                $subject = (string) ($msg['subject'] ?? '');
                if ($from === '' || str_starts_with($from, 'noreply@') || preg_match('/^\s*(re:\s*)?test\s*$/i', $subject) === 1) {
                    continue;
                }

                $outbound = $folder === 'INBOX.Sent' || $from === CORRESPONDENCE_OWN_ADDRESS;
                $other = $outbound ? ($msg['to'][0] ?? []) : ($msg['from'] ?? []);
                $otherEmail = strtolower((string) ($other['address'] ?? ''));
                if ($otherEmail === '' || $otherEmail === CORRESPONDENCE_OWN_ADDRESS) {
                    continue;
                }

                // The subject alone identifies most automatic replies — no need to fetch those.
                if (!$outbound && is_auto_reply($subject, '')) {
                    continue;
                }
                $text = correspondence_mail_api($token, 'GET', "$base/" . (int) $msg['uid'] . '/text');
                $body = trim((string) ($text['data']['text'] ?? ''));
                if (!$outbound && is_auto_reply($subject, $body)) {
                    continue;
                }
                if (mb_strlen($body) > CORRESPONDENCE_BODY_MAX) {
                    $body = mb_substr($body, 0, CORRESPONDENCE_BODY_MAX) . '…';
                }

                $who = $known[$otherEmail] ?? null;
                $headerName = trim((string) ($other['name'] ?? ''));
                // Campaign records hold a clean name ("Imogen Walker"); mailbox headers often
                // don't ("WALKER, Imogen (MP)"). Stakeholders are logged by organisation only,
                // so there the header name is the person.
                $person = ($who !== null && $who['category'] !== 'stakeholder')
                    ? $who['person']
                    : ($headerName ?: ($seen[$otherEmail]['person'] ?? $who['person'] ?? $otherEmail));
                $row = [
                    'occurred_on'  => substr((string) ($msg['date'] ?? date('Y-m-d')), 0, 10),
                    'direction'    => $outbound ? 'out' : 'in',
                    'person'       => mb_substr($person, 0, 160),
                    'organisation' => $seen[$otherEmail]['organisation'] ?? $who['organisation'] ?? null,
                    'category'     => $seen[$otherEmail]['category'] ?? $who['category'] ?? correspondence_category_for_domain($otherEmail),
                    'email'        => $otherEmail,
                    'subject'      => mb_substr($subject, 0, 255),
                    'body_text'    => $body,
                    'status'       => 'review',
                    'message_id'   => mb_substr($messageId, 0, 255),
                ];
                if ($report) {
                    $report(sprintf('%s  %s  %-3s %s — %s', $dryRun ? 'WOULD LOG' : 'LOGGED', $row['occurred_on'], strtoupper($row['direction']), $row['person'], $row['subject']));
                }
                $rows[] = $row;
                $logged[$messageId] = true;
                if ($row['person'] !== $otherEmail) {
                    $seen[$otherEmail] ??= ['person' => $row['person'], 'organisation' => $row['organisation'], 'category' => $row['category']];
                }
            }
            $page++;
        } while ($page <= $totalPages);
    }

    if (!$dryRun && $rows) {
        // The mail API calls above can outlast the remote database's idle timeout.
        $insert = campaign_db(true)->prepare(
            'INSERT IGNORE INTO correspondence_log (occurred_on, direction, person, organisation, category, email, subject, body_text, status, message_id)
             VALUES (:occurred_on, :direction, :person, :organisation, :category, :email, :subject, :body_text, :status, :message_id)'
        );
        foreach ($rows as $row) {
            $insert->execute($row);
        }
    }
    return count($rows);
}
