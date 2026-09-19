<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_samesite', 'Lax');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

$root = dirname(__DIR__);
define('PROJECT_ROOT', $root);
if (!defined('SITE_BRAND')) {
    define('SITE_BRAND', 'WIRES');
}

require_once __DIR__ . '/env.php';
load_env_file($root . DIRECTORY_SEPARATOR . '.env');

/**
 * Read an env var if set (including empty string). Returns null if absent.
 */
function env_raw(string $key): ?string
{
    if (array_key_exists($key, $_ENV)) {
        return (string) $_ENV[$key];
    }
    $v = getenv($key);
    if ($v !== false) {
        return (string) $v;
    }
    return null;
}

$dbPassword = env_raw('DB_PASSWORD');
if ($dbPassword === null) {
    $dbPassword = env_raw('DB_PASS');
}
if ($dbPassword === null) {
    $dbPassword = '';
}

/** @var array{db: array<string, mixed>, app: array<string, mixed>} $GLOBALS['app_config'] */
$GLOBALS['app_config'] = [
    'db' => [
        'host' => env_raw('DB_HOST') ?? '127.0.0.1',
        'port' => (int) (($p = env_raw('DB_PORT')) !== null && $p !== '' ? $p : '3306'),
        'name' => env_raw('DB_NAME') ?? 'wire_scotland',
        'user' => env_raw('DB_USER') ?? 'root',
        'pass' => $dbPassword,
        'charset' => env_raw('DB_CHARSET') ?? 'utf8mb4',
    ],
    'app' => [
        'env' => env_raw('APP_ENV') ?? 'production',
        'base_url' => env_raw('APP_BASE_URL') ?? '',
    ],
];

function app_config(): array
{
    return $GLOBALS['app_config'];
}

function base_url(string $path = ''): string
{
    $base = rtrim((string) app_config()['app']['base_url'], '/');
    $path = ltrim($path, '/');
    if ($base === '') {
        return $path === '' ? '/' : '/' . $path;
    }
    return $path === '' ? $base . '/' : $base . '/' . $path;
}

/** Public image path (files live under /images/ at web root). */
function image_asset(string $filename): string
{
    if ($filename === '' || preg_match('/[^A-Za-z0-9._-]/', $filename)) {
        throw new InvalidArgumentException('Invalid image filename');
    }

    return '/images/' . $filename;
}

/**
 * A CSS/JS path with a cache-busting ?v= based on the file's own mtime, so a browser
 * that cached an old copy before a deploy picks up the new one immediately rather than
 * serving stale content until its cache naturally expires (bit us once already, on
 * wifi-map.js after a data/behaviour change).
 */
function asset_url(string $path): string
{
    $file = PROJECT_ROOT . $path;
    $version = is_file($file) ? (string) filemtime($file) : (string) time();
    return $path . '?v=' . $version;
}

/** Absolute URL when `APP_BASE_URL` is set; for Open Graph etc. */
function absolute_url_for_path(string $path): ?string
{
    $base = rtrim((string) app_config()['app']['base_url'], '/');
    if ($base === '') {
        return null;
    }

    return $base . '/' . ltrim($path, '/');
}

/**
 * For `<a href="...">`: add target and rel when the URL is off-site (http/https or protocol-relative).
 */
function external_link_attrs(string $href): string
{
    $href = trim($href);
    if ($href === '' || str_starts_with($href, '#') || str_starts_with($href, '/')) {
        return '';
    }
    if (preg_match('#^(mailto:|tel:)#i', $href)) {
        return '';
    }
    if (!preg_match('#^(https?:)?//#i', $href)) {
        return '';
    }

    $absolute = str_starts_with($href, '//') ? 'https:' . $href : $href;
    $host = parse_url($absolute, PHP_URL_HOST);
    if (!is_string($host) || $host === '') {
        return '';
    }

    $candidates = [];
    if (!empty($_SERVER['HTTP_HOST'])) {
        $candidates[] = (string) $_SERVER['HTTP_HOST'];
    }
    $base = (string) app_config()['app']['base_url'];
    if ($base !== '') {
        $h = parse_url($base, PHP_URL_HOST);
        if (is_string($h) && $h !== '') {
            $candidates[] = $h;
        }
    }

    foreach ($candidates as $h) {
        if (strcasecmp($host, $h) === 0) {
            return '';
        }
    }

    return ' target="_blank" rel="noopener noreferrer"';
}

/** @return PDO */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $c = app_config()['db'];
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $c['host'],
        (int) $c['port'],
        $c['name'],
        $c['charset'] ?? 'utf8mb4'
    );

    $pdo = new PDO($dsn, (string) $c['user'], (string) $c['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function db_available(): bool
{
    try {
        db();
        return true;
    } catch (Throwable) {
        return false;
    }
}

/**
 * Dedicated DB connection for the councillor-campaign automation scripts
 * (bin/send-councillor-campaign.php, bin/check-campaign-replies.php), so real send/reply
 * data lands on the production DB rather than whichever DB_* config this machine happens
 * to be running under. Falls back to db()'s connection when CAMPAIGN_DB_HOST is unset,
 * so local testing without production credentials still works unchanged.
 */
function campaign_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = env_raw('CAMPAIGN_DB_HOST');
    if ($host === null || $host === '') {
        return db();
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $host,
        (int) (env_raw('CAMPAIGN_DB_PORT') ?: '3306'),
        env_raw('CAMPAIGN_DB_NAME') ?? '',
        env_raw('CAMPAIGN_DB_CHARSET') ?? 'utf8mb4'
    );

    $pdo = new PDO($dsn, env_raw('CAMPAIGN_DB_USER') ?? '', env_raw('CAMPAIGN_DB_PASSWORD') ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function campaign_db_available(): bool
{
    try {
        campaign_db();
        return true;
    } catch (Throwable) {
        return false;
    }
}

/**
 * Lowercased emails of everyone who has asked not to be contacted again — checked
 * before every campaign send, independent of campaign_slug, so an opt-out sticks
 * across every future campaign rather than just retries of the one they replied to.
 */
function do_not_contact_emails(): array
{
    $stmt = campaign_db()->query('SELECT email FROM do_not_contact');
    return array_map('strtolower', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

/**
 * The CEO and Leader letters both say "we're writing in parallel to all councillors" —
 * only true for councils the councillor campaign has actually reached. Keeps all three
 * campaigns in sync: as more daily councillor batches go out, more councils become
 * eligible for the institutional letters.
 */
function councils_reached_by_councillor_campaign(): array
{
    $stmt = campaign_db()->query(
        "SELECT DISTINCT council_area FROM councillor_campaign_sends
         WHERE campaign_slug = 'councillor-public-statement-2026-09' AND status = 'sent'"
    );
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Reply-by date, roughly N weeks out from whenever this actually sends — campaigns often
 * run across several days (Resend's daily quota), so this is computed fresh each run
 * rather than a fixed string. Not exact business-day counting, just nudged off a weekend.
 */
function reply_by_date(int $weeksOut = 8): string
{
    $date = new DateTime("+{$weeksOut} weeks");
    $weekday = (int) $date->format('N'); // 1=Mon ... 6=Sat, 7=Sun
    if ($weekday === 6) {
        $date->modify('+2 days');
    } elseif ($weekday === 7) {
        $date->modify('+1 day');
    }
    return $date->format('l j F');
}

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

/**
 * Truncate at a word boundary for SEO-facing tags (<title>, meta description)
 * so long editorial headlines/summaries don't get cut mid-word in search results.
 * On-page headings and Open Graph tags use the untruncated original text.
 */
function seo_truncate(string $text, int $maxLength): string
{
    if (mb_strlen($text) <= $maxLength) {
        return $text;
    }
    $truncated = mb_substr($text, 0, $maxLength - 1);
    $lastSpace = mb_strrpos($truncated, ' ');
    if ($lastSpace !== false) {
        $truncated = mb_substr($truncated, 0, $lastSpace);
    }
    return rtrim($truncated) . '…';
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['_csrf'];
}

function csrf_validate(mixed $token): bool
{
    if (!isset($_SESSION['_csrf']) || !is_string($token)) {
        return false;
    }
    return hash_equals((string) $_SESSION['_csrf'], $token);
}

/**
 * Absolute URL for any path — uses APP_BASE_URL when set, otherwise
 * constructs from the current request (scheme + HTTP_HOST).
 * Safe for use in share links, canonical tags, and JSON-LD.
 */
function page_url(string $path = ''): string
{
    $base = rtrim((string) app_config()['app']['base_url'], '/');
    if ($base === '') {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $base   = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'wires.org.uk');
    }
    return $base . '/' . ltrim($path, '/');
}

/** Format a SQL date string (YYYY-MM-DD) as "1 January 2026". Returns the raw string on parse failure. */
function format_date(string $sqlDate): string
{
    $ts = strtotime($sqlDate);
    return $ts !== false ? date('j F Y', $ts) : $sqlDate;
}

function flash_set(string $key, string $message): void
{
    $_SESSION['_flash'][$key] = $message;
}

function flash_take(string $key): ?string
{
    if (!isset($_SESSION['_flash'][$key])) {
        return null;
    }
    $m = (string) $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);
    return $m;
}
