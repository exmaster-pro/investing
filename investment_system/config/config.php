<?php
declare(strict_types=1);

/*
 * Copy this file to config/local.php and put real credentials there.
 * Never commit local.php or publish your Gmail App Password.
 */
define('APP_NAME', 'Investment Portal');
define('APP_URL', 'http://localhost/investment_system');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'investment_system');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP default; change if your MySQL root has a password.
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your-sender@gmail.com');
define('MAIL_PASSWORD', 'replace-with-google-app-password');
define('MAIL_FROM', 'your-sender@gmail.com');
define('MAIL_FROM_NAME', APP_NAME);
define('OTP_TTL_MINUTES', 10);
define('SESSION_NAME', 'investment_portal_session');

date_default_timezone_set('Africa/Nairobi');
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_name(SESSION_NAME);
    session_start();
}

function app_url(string $path = ''): string {
    return rtrim(APP_URL, '/') . ($path !== '' ? '/' . ltrim($path, '/') : '');
}
function e(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function redirect(string $path): never {
    header('Location: ' . app_url($path));
    exit;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    $sent = (string)($_POST['csrf_token'] ?? '');
    if ($sent === '' || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Your session expired or the form was invalid. Go back and try again.');
    }
}
function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}
function get_flash(): ?array {
    $value = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($value) ? $value : null;
}
function require_user(): array {
    if (empty($_SESSION['user_id'])) {
        flash('warning', 'Please sign in first.');
        redirect('login.php');
    }
    return ['id' => (int)$_SESSION['user_id'], 'name' => (string)($_SESSION['user_name'] ?? '')];
}
function require_admin(): array {
    if (empty($_SESSION['admin_id'])) {
        flash('warning', 'Administrator sign-in required.');
        redirect('admin/login.php');
    }
    return ['id' => (int)$_SESSION['admin_id'], 'name' => (string)($_SESSION['admin_name'] ?? 'Administrator')];
}
function is_post(): bool { return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'; }
