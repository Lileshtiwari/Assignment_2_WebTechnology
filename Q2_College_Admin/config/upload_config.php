<?php
/* Q2 UPLOAD CONFIGURATION AND HELPERS */

require_once __DIR__ . '/database.php';

/**
 * Handle a single file upload.
 * Returns ['ok'=>true,'stored_filename'=>...,'relative_path'=>...,'mime_type'=>...,'file_size'=>...]
 * or      ['ok'=>false,'error'=>'message']
 */
function handle_upload(array $fileArray, string $category, string $subdir = ''): array {
    if ($fileArray['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => upload_error_message($fileArray['error'])];
    }

    /* Size check */
    if ($fileArray['size'] > UPLOAD_MAX_BYTES) {
        return ['ok' => false, 'error' => 'File exceeds maximum allowed size of ' . UPLOAD_MAX_MB . ' MB.'];
    }

    /* Extension check */
    $originalName = $fileArray['name'];
    $ext          = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($ext, UPLOAD_ALLOWED_EXTENSIONS, true)) {
        return ['ok' => false, 'error' => 'File type not allowed. Accepted: ' . implode(', ', UPLOAD_ALLOWED_EXTENSIONS) . '.'];
    }

    /* MIME check using finfo */
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($fileArray['tmp_name']);
    if (!in_array($mimeType, UPLOAD_ALLOWED_MIMES, true)) {
        return ['ok' => false, 'error' => 'File MIME type not allowed: ' . $mimeType];
    }

    /* Generate unique stored filename to prevent collisions */
    $storedFilename = sprintf('%s_%s.%s', uniqid('kp_', true), bin2hex(random_bytes(4)), $ext);

    /* Build target directory */
    $targetDir  = UPLOAD_BASE_DIR . ($subdir ? rtrim($subdir, '/') . '/' : '');
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $targetPath   = $targetDir . $storedFilename;
    $relativePath = ($subdir ? rtrim($subdir, '/') . '/' : '') . $storedFilename;

    if (!move_uploaded_file($fileArray['tmp_name'], $targetPath)) {
        return ['ok' => false, 'error' => 'Failed to move uploaded file. Check directory permissions.'];
    }

    return [
        'ok'               => true,
        'original_filename' => $originalName,
        'stored_filename'  => $storedFilename,
        'relative_path'    => $relativePath,
        'mime_type'        => $mimeType,
        'file_size'        => $fileArray['size'],
        'document_category' => $category,
    ];
}

/* Save file metadata to student_files table */
function save_file_metadata(PDO $pdo, int $studentId, array $upload): int {
    $stmt = $pdo->prepare(
        'INSERT INTO student_files
         (student_id, original_filename, stored_filename, relative_path, mime_type, file_size, document_category)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $studentId,
        $upload['original_filename'],
        $upload['stored_filename'],
        $upload['relative_path'],
        $upload['mime_type'],
        $upload['file_size'],
        $upload['document_category'],
    ]);
    return (int)$pdo->lastInsertId();
}

/* Return human-readable upload error */
function upload_error_message(int $code): string {
    return match($code) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File exceeds the maximum allowed size.',
        UPLOAD_ERR_PARTIAL  => 'File was only partially uploaded. Please try again.',
        UPLOAD_ERR_NO_FILE  => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Server temporary directory is missing.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the upload.',
        default               => 'An unknown upload error occurred.',
    };
}

/* Delete a stored file safely */
function delete_upload(string $relativePath): bool {
    $fullPath = UPLOAD_BASE_DIR . $relativePath;
    /* Prevent path traversal */
    $realBase = realpath(UPLOAD_BASE_DIR);
    $realFile = realpath($fullPath);
    if ($realFile === false || strpos($realFile, $realBase) !== 0) return false;
    return file_exists($realFile) ? unlink($realFile) : true;
}
