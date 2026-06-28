<?php

declare(strict_types=1);

function admin_is_logged_in(): bool
{
    return !empty($_SESSION['wires_admin_auth']);
}

function require_admin(): void
{
    if (!admin_is_logged_in()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function admin_check_credentials(string $username, string $password): bool
{
    $envUser = env_raw('ADMIN_USERNAME') ?? 'admin';
    $envPass = env_raw('ADMIN_PASSWORD') ?? '';

    if ($envPass === '') {
        return false;
    }

    return hash_equals($envUser, $username) && hash_equals($envPass, $password);
}

function admin_login_rate_ok(): bool
{
    $lockUntil = (int) ($_SESSION['admin_lock_until'] ?? 0);
    if ($lockUntil > time()) {
        return false;
    }
    return true;
}

function admin_record_failure(): void
{
    $attempts = (int) ($_SESSION['admin_attempts'] ?? 0) + 1;
    $_SESSION['admin_attempts'] = $attempts;
    if ($attempts >= 5) {
        $_SESSION['admin_lock_until'] = time() + 900;
        $_SESSION['admin_attempts'] = 0;
    }
}

function admin_clear_rate_limit(): void
{
    unset($_SESSION['admin_attempts'], $_SESSION['admin_lock_until']);
}

function admin_lockout_seconds(): int
{
    $until = (int) ($_SESSION['admin_lock_until'] ?? 0);
    return max(0, $until - time());
}
