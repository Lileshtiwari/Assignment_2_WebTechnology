<?php
/* Q2 DATABASE CONFIGURATION */

/* Optional local configuration override (git-ignored) */
if (file_exists(__DIR__ . '/database.local.php')) {
    require_once __DIR__ . '/database.local.php';
}

if (!defined('DB_HOST'))    define('DB_HOST',    getenv('DB_HOST') ?: 'localhost');
if (!defined('DB_PORT'))    define('DB_PORT',    getenv('DB_PORT') ?: '3306');
if (!defined('DB_NAME'))    define('DB_NAME',    getenv('DB_NAME') ?: 'kpriet_admission');
if (!defined('DB_USER'))    define('DB_USER',    getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS'))    define('DB_PASS',    getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'root');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

/* UPLOAD PATHS */
if (!defined('UPLOAD_BASE_DIR')) define('UPLOAD_BASE_DIR', dirname(__DIR__) . '/uploads/');
if (!defined('UPLOAD_MAX_MB'))   define('UPLOAD_MAX_MB',   2);
if (!defined('UPLOAD_MAX_BYTES')) define('UPLOAD_MAX_BYTES', UPLOAD_MAX_MB * 1024 * 1024);
if (!defined('UPLOAD_ALLOWED_EXTENSIONS')) define('UPLOAD_ALLOWED_EXTENSIONS', ['jpg','jpeg','png','pdf']);
if (!defined('UPLOAD_ALLOWED_MIMES')) define('UPLOAD_ALLOWED_MIMES', [
    'image/jpeg',
    'image/pjpeg',
    'image/png',
    'image/x-png',
    'image/webp',
    'application/pdf',
    'application/x-pdf',
    'application/acrobat',
    'applications/vnd.pdf',
    'text/pdf',
    'application/octet-stream',
]);

/* Create upload directory if it doesn't exist */
if (!is_dir(UPLOAD_BASE_DIR)) {
    mkdir(UPLOAD_BASE_DIR, 0755, true);
}
