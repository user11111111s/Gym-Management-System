<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    $isHttps =
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function regenerate_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        start_secure_session();
    }

    session_regenerate_id(true);
}

function destroy_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        start_secure_session();
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'] ?? '',
                'secure' => (bool) $params['secure'],
                'httponly' => (bool) $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]
        );
    }

    session_destroy();
}

function require_post_request(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Method Not Allowed');
    }
}


function csrf_token(): string
{
    start_secure_session();

    if (
        !isset($_SESSION['csrf_token']) ||
        !is_string($_SESSION['csrf_token']) ||
        $_SESSION['csrf_token'] === ''
    ) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function require_csrf_token(): void
{
    start_secure_session();

    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($sessionToken) ||
        !is_string($submittedToken) ||
        $sessionToken === '' ||
        $submittedToken === '' ||
        !hash_equals($sessionToken, $submittedToken)
    ) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }
}

function require_admin(string $loginPath = '../Login/index.php'): void
{
    start_secure_session();

    if (
        ($_SESSION['role'] ?? '') !== 'admin' ||
        !isset($_SESSION['admin_id']) ||
        !is_numeric($_SESSION['admin_id'])
    ) {
        header('Location: ' . $loginPath);
        exit();
    }
}

function require_admin_json(): void
{
    start_secure_session();

    if (
        ($_SESSION['role'] ?? '') !== 'admin' ||
        !isset($_SESSION['admin_id']) ||
        !is_numeric($_SESSION['admin_id'])
    ) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'status' => 'error',
            'message' => 'Administrator authentication required.'
        ]);

        exit();
    }
}