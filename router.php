<?php

/**
 * Local development router for `php -S 127.0.0.1:8080 router.php`
 *
 * Replicates the .htaccess clean-URL rewrites so that /about works
 * the same locally as it does on Hostinger (Apache + mod_rewrite).
 *
 * DO NOT deploy this file to production — it is only for local dev.
 */

$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$root = __DIR__;

/* 1. Serve real files and directories as-is (CSS, images, JS, etc.) */
if ($uri !== '/' && file_exists($root . $uri) && !is_dir($root . $uri)) {
    return false;
}

/* 2. Map /index or / → index.php */
if ($uri === '/' || $uri === '/index') {
    require $root . '/index.php';
    return true;
}

/* 3. Map clean URL → .php file (e.g. /about → about.php) */
$phpFile = $root . $uri . '.php';
if (file_exists($phpFile)) {
    require $phpFile;
    return true;
}

/* 4. Try the URI as-is with .php already present (e.g. /about.php) */
$directFile = $root . $uri;
if (file_exists($directFile)) {
    require $directFile;
    return true;
}

/* 5. Nothing matched — let PHP return 404 */
http_response_code(404);
echo '404 Not Found';
return true;
