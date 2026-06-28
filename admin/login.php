<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';

if (admin_is_logged_in()) {
    header('Location: /admin/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $error = 'Invalid form submission. Please try again.';
    } elseif (!admin_login_rate_ok()) {
        $remaining = (int) ceil(admin_lockout_seconds() / 60);
        $error = "Too many failed attempts. Please wait {$remaining} minute(s) before trying again.";
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if (admin_check_credentials($username, $password)) {
            admin_clear_rate_limit();
            session_regenerate_id(true);
            $_SESSION['wires_admin_auth'] = true;
            header('Location: /admin/');
            exit;
        } else {
            admin_record_failure();
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in — WIRES Admin</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Source+Sans+3:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/site.css">
    <link rel="stylesheet" href="/admin/admin.css">
</head>
<body class="admin-body">
<div class="admin-login-wrap">
    <div class="admin-login-card">
        <p class="admin-login-brand">WIRES</p>
        <p class="admin-login-sub">Admin panel — log in to continue</p>

        <?php if ($error): ?>
            <div class="admin-flash admin-flash--err" role="alert"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if (env_raw('ADMIN_PASSWORD') === '' || env_raw('ADMIN_PASSWORD') === null): ?>
            <div class="admin-flash admin-flash--err" role="alert">
                <strong>Admin password not set.</strong> Add <code>ADMIN_USERNAME</code> and <code>ADMIN_PASSWORD</code> to your <code>.env</code> file to enable login.
            </div>
        <?php endif; ?>

        <form method="post" action="/admin/login.php">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="admin-field">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" required autocomplete="username"
                       value="<?= e((string) ($_POST['username'] ?? '')) ?>">
            </div>
            <div class="admin-field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required autocomplete="current-password">
            </div>
            <button class="btn btn-primary" type="submit" style="width:100%;margin-top:0.5rem">Log in</button>
        </form>
        <p style="margin-top:1.5rem;font-size:0.82rem;color:var(--muted);text-align:center">
            <a href="/">← Back to WIRES</a>
        </p>
    </div>
</div>
</body>
</html>
