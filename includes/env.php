<?php

declare(strict_types=1);

/**
 * Load KEY=value pairs from a .env file into the process environment.
 * Missing or unreadable file is ignored (no error).
 * Does not overwrite variables already visible to getenv() (immutable-style).
 */
function load_env_file(string $path): void
{
    if (!is_readable($path)) {
        return;
    }

    $raw = file_get_contents($path);
    if ($raw === false) {
        return;
    }

    if (str_starts_with($raw, "\xEF\xBB\xBF")) {
        $raw = substr($raw, 3);
    }

    $lines = preg_split("/\r\n|\n|\r/", $raw) ?: [];

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        if (str_starts_with($line, 'export ')) {
            $line = trim(substr($line, 7));
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
        }

        $eq = strpos($line, '=');
        if ($eq === false) {
            continue;
        }

        $key = trim(substr($line, 0, $eq));
        if ($key === '' || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) {
            continue;
        }

        if (getenv($key) !== false) {
            continue;
        }

        $value = trim(substr($line, $eq + 1));
        $len = strlen($value);
        if ($len >= 2) {
            $q = $value[0];
            if (($q === '"' || $q === "'") && $value[$len - 1] === $q) {
                $value = substr($value, 1, -1);
            }
        }

        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}
