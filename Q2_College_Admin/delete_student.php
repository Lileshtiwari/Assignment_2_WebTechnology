<?php
/* Q2 DELETE STUDENT */

require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/upload_config.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    flash_set('error', 'Invalid student ID specified.');
    redirect('students.php');
}

$pdo = db();

// Fetch student details
$stmt = $pdo->prepare('SELECT id, full_name, roll_number FROM students WHERE id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    flash_set('error', 'Student not found.');
    redirect('students.php');
}

// Fetch all uploaded files for cleanup from storage
$fstmt = $pdo->prepare('SELECT relative_path FROM student_files WHERE student_id = ?');
$fstmt->execute([$id]);
$files = $fstmt->fetchAll();

foreach ($files as $f) {
    delete_upload($f['relative_path']);
}

// Delete student directory if empty
$studentDir = UPLOAD_BASE_DIR . "student_{$id}/";
if (is_dir($studentDir)) {
    @rmdir($studentDir);
}

// Delete student record (CASCADE will also remove database records from student_files)
$delStmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
$delStmt->execute([$id]);

flash_set('success', 'Student "' . $student['full_name'] . '" (' . $student['roll_number'] . ') and all associated records deleted.');
redirect('students.php');
