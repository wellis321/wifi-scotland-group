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
    define('SITE_BRAND', 'The WIFI Scotland Group');
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
        'name' => env_raw('DB_NAME') ?? 'wifi_group',
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

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
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
