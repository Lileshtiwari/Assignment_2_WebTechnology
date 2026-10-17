<?php
/* Q2 SESSION AUTHENTICATION HELPERS */

require_once __DIR__ . '/database.php';

/* Return singleton PDO connection */
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            /* Do not expose connection details in production */
            error_log('DB connection failed: ' . $e->getMessage());
            die('<p style="font-family:sans-serif;color:#c53030;padding:2rem">Database connection failed. Check configuration and ensure MySQL is running.</p>');
        }
    }
    return $pdo;
}

/* Start session if not already started */
function ensure_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'use_strict_mode' => true,
        ]);
    }
}

/* Require authenticated admin — redirect if not logged in */
function require_admin(): void {
    ensure_session();
    if (empty($_SESSION['admin_id'])) {
        $isAuthDir = (basename(dirname($_SERVER['SCRIPT_FILENAME'] ?? '')) === 'auth');
        $loginPath = $isAuthDir ? 'login.php' : 'auth/login.php';
        header('Location: ' . $loginPath . '?redirect=' . urlencode($_SERVER['REQUEST_URI'] ?? ''));
        exit;
    }
}

/* Return currently logged-in admin data (or null) */
function current_admin(): ?array {
    ensure_session();
    if (empty($_SESSION['admin_id'])) return null;
    return [
        'id'        => $_SESSION['admin_id'],
        'username'  => $_SESSION['admin_username'] ?? '',
        'full_name' => $_SESSION['admin_name']     ?? '',
    ];
}

/* Escape HTML output */
function h(mixed $val): string {
    return htmlspecialchars((string)$val, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* Redirect helper */
function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

/* Flash message helpers */
function flash_set(string $type, string $message): void {
    ensure_session();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array {
    ensure_session();
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}
