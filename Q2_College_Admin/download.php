<?php
/* Q2 SECURE FILE DOWNLOAD & PREVIEW HANDLER */

require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/upload_config.php';
require_admin();

$pdo = db();
$fileRecord = null;
$filePath = null;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM student_files WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $fileRecord = $stmt->fetch();
    if ($fileRecord) {
        $filePath = UPLOAD_BASE_DIR . $fileRecord['relative_path'];
    }
} elseif (isset($_GET['path'])) {
    $rel = trim($_GET['path']);
    // Normalize path and prevent directory traversal
    $rel = str_replace(['..', '\\'], ['', '/'], $rel);
    $rel = ltrim($rel, '/');
    $filePath = UPLOAD_BASE_DIR . $rel;

    $stmt = $pdo->prepare('SELECT * FROM student_files WHERE relative_path = ? LIMIT 1');
    $stmt->execute([$rel]);
    $fileRecord = $stmt->fetch();
}

// Security verification against path traversal
$baseReal = realpath(UPLOAD_BASE_DIR);
$fileReal = $filePath ? realpath($filePath) : false;

if (!$fileReal || !$baseReal || strpos($fileReal, $baseReal) !== 0 || !is_file($fileReal)) {
    // If it was an image request, return transparent 1x1 or 404
    if (isset($_GET['type']) && $_GET['type'] === 'image') {
        http_response_code(404);
        header('Content-Type: image/svg+xml');
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><rect fill="#e2e8f0" width="100" height="100"/><text fill="#94a3b8" x="50%" y="50%" text-anchor="middle" dy=".3em" font-family="sans-serif" font-size="12">Not Found</text></svg>';
        exit;
    }
    http_response_code(404);
    die('File not found or access denied.');
}

// Determine MIME type and original filename
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $fileRecord['mime_type'] ?? $finfo->file($fileReal);
$filename = $fileRecord['original_filename'] ?? basename($fileReal);
$filesize = filesize($fileReal);

// Disposition: inline for images and PDFs, attachment for force download
$isDownload = isset($_GET['download']) && $_GET['download'] === '1';
$disposition = ($isDownload || !in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf']))
    ? 'attachment'
    : 'inline';

// Send security and caching headers
header('X-Content-Type-Options: nosniff');
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . $filesize);
header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($filename) . '"');
header('Cache-Control: private, max-age=3600');

// Stream file to output
readfile($fileReal);
exit;
