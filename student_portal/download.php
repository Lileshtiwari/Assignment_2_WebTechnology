<?php
/* KPRIET STUDENT PORTAL FILE DOWNLOAD */

require_once dirname(__DIR__) . '/Q2_College_Admin/config/auth.php';
require_once dirname(__DIR__) . '/Q2_College_Admin/config/database.php';
ensure_session();

if (empty($_SESSION['student_id'])) {
    http_response_code(403);
    die('Access denied. Please log in.');
}

$fileId = (int)($_GET['id'] ?? 0);
if ($fileId <= 0) {
    http_response_code(404);
    die('File not found.');
}

$pdo = db();
$stmt = $pdo->prepare('SELECT * FROM student_files WHERE id = ? AND student_id = ? LIMIT 1');
$stmt->execute([$fileId, (int)$_SESSION['student_id']]);
$file = $stmt->fetch();

if (!$file) {
    http_response_code(404);
    die('File not found or access denied.');
}

$baseReal = realpath(UPLOAD_BASE_DIR);
$fullPath = realpath(UPLOAD_BASE_DIR . $file['relative_path']);
if (!$fullPath || !$baseReal || stripos($fullPath, $baseReal) !== 0 || !is_file($fullPath)) {
    http_response_code(404);
    die('File does not exist on disk.');
}

$disposition = (isset($_GET['view']) && $_GET['view'] === '1') ? 'inline' : 'attachment';

header('Content-Type: ' . $file['mime_type']);
header('Content-Length: ' . filesize($fullPath));
header('Content-Disposition: ' . $disposition . '; filename="' . basename($file['original_filename']) . '"');
header('Cache-Control: private, max-age=3600');
header('X-Content-Type-Options: nosniff');

readfile($fullPath);
exit;
