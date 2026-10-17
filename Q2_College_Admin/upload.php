<?php
/* Q2 STUDENT DOCUMENT UPLOAD & MANAGEMENT */

$pageTitle = 'Upload Documents';
$activeNav = 'students';

require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/upload_config.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    flash_set('error', 'Invalid student ID specified.');
    redirect('students.php');
}

$pdo = db();
$stmt = $pdo->prepare('SELECT id, full_name, roll_number, course FROM students WHERE id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    flash_set('error', 'Student not found.');
    redirect('students.php');
}

$errors = [];

/* Handle document upload */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    $category = trim($_POST['document_category'] ?? '');
    $validCategories = ['profile_photo', 'id_proof', 'sslc_certificate', 'hsc_certificate', 'other_certificate'];

    if (!in_array($category, $validCategories, true)) {
        $errors[] = 'Invalid document category selected.';
    }

    if (empty($_FILES['document_file']['name'])) {
        $errors[] = 'Please select a file to upload.';
    }

    if (empty($errors)) {
        $uploadResult = handle_upload($_FILES['document_file'], $category, "student_{$id}/");
        if ($uploadResult['ok']) {
            // If category is profile_photo, delete previous profile photo
            if ($category === 'profile_photo') {
                $oldPhoto = $pdo->prepare("SELECT id, relative_path FROM student_files WHERE student_id = ? AND document_category = 'profile_photo'");
                $oldPhoto->execute([$id]);
                while ($old = $oldPhoto->fetch()) {
                    delete_upload($old['relative_path']);
                    $pdo->prepare('DELETE FROM student_files WHERE id = ?')->execute([$old['id']]);
                }
            }

            save_file_metadata($pdo, $id, $uploadResult);
            flash_set('success', 'Document uploaded successfully: ' . h($uploadResult['original_filename']));
            redirect("upload.php?id={$id}");
        } else {
            $errors[] = $uploadResult['error'];
        }
    }
}

/* Handle file deletion */
if (isset($_GET['delete_file'])) {
    $fid = (int)$_GET['delete_file'];
    $fstmt = $pdo->prepare('SELECT * FROM student_files WHERE id = ? AND student_id = ?');
    $fstmt->execute([$fid, $id]);
    $frow = $fstmt->fetch();
    if ($frow) {
        delete_upload($frow['relative_path']);
        $pdo->prepare('DELETE FROM student_files WHERE id = ?')->execute([$fid]);
        flash_set('success', 'File deleted: ' . h($frow['original_filename']));
        redirect("upload.php?id={$id}");
    }
}

/* Fetch existing files */
$fstmt = $pdo->prepare('SELECT * FROM student_files WHERE student_id = ? ORDER BY uploaded_at DESC');
$fstmt->execute([$id]);
$files = $fstmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
  <div class="container admin-container">
    <div class="page-header">
      <div>
        <h1>Upload Documents</h1>
        <p class="page-subtitle">Manage files for <strong><?= h($student['full_name']) ?></strong> &mdash; <code><?= h($student['roll_number']) ?></code></p>
      </div>
      <div style="display:flex;gap:0.75rem">
        <a href="view_student.php?id=<?= $id ?>" class="btn btn-outline-primary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          View Student Profile
        </a>
        <a href="students.php" class="btn btn-outline-primary">All Students</a>
      </div>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert--error" role="alert">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div><strong>Upload Error:</strong>
          <ul><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul>
        </div>
      </div>
    <?php endif; ?>

    <div class="view-grid">
      <!-- LEFT: Upload Form -->
      <div>
        <div class="admin-card">
          <div class="detail-section-heading">Upload New Document</div>
          <form method="post" action="" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="action" value="upload">

            <div class="form-group">
              <label for="document_category">Document Category <span class="required">*</span></label>
              <select id="document_category" name="document_category" class="form-control" required>
                <option value="">Select Category</option>
                <option value="profile_photo">Profile Photo (JPG / PNG)</option>
                <option value="id_proof">Government ID Proof (Aadhaar / Passport)</option>
                <option value="sslc_certificate">SSLC Mark Sheet</option>
                <option value="hsc_certificate">HSC Mark Sheet</option>
                <option value="other_certificate">Other Certificate / Transfer Certificate</option>
              </select>
            </div>

            <div class="form-group">
              <label for="document_file">Select File <span class="required">*</span></label>
              <input type="file" id="document_file" name="document_file" class="form-control"
                     accept=".pdf,.jpg,.jpeg,.png" required>
              <small style="color:var(--color-text-muted);font-size:0.78rem">
                Max 2 MB per file. Accepted: PDF, JPG, PNG.
              </small>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:0.75rem" id="submitUploadBtn">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              Upload Document
            </button>
          </form>
        </div>
      </div>

      <!-- RIGHT: Existing Files List -->
      <div>
        <div class="admin-card">
          <div class="detail-section-heading">Uploaded Documents (<?= count($files) ?>)</div>

          <?php if (empty($files)): ?>
            <div class="empty-state" style="padding:2.5rem 1rem">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
              <p>No documents uploaded yet for this student.</p>
            </div>
          <?php else: ?>
            <div class="file-list">
              <?php foreach ($files as $f): ?>
                <div class="file-item">
                  <div class="file-item__icon">
                    <?php if (str_starts_with($f['mime_type'], 'image/')): ?>
                      <img src="download.php?path=<?= urlencode($f['relative_path']) ?>&type=image"
                           alt="<?= h($f['original_filename']) ?>" class="file-thumb">
                    <?php else: ?>
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <?php endif; ?>
                  </div>
                  <div class="file-item__info">
                    <span class="file-item__name"><?= h($f['original_filename']) ?></span>
                    <span class="file-item__meta">
                      <strong><?= h(str_replace('_',' ',ucfirst($f['document_category']))) ?></strong> &bull;
                      <?= h(number_format($f['file_size']/1024, 1)) ?> KB &bull;
                      <?= h(date('d M Y, h:i A', strtotime($f['uploaded_at']))) ?>
                    </span>
                  </div>
                  <div class="file-item__actions">
                    <a href="download.php?id=<?= (int)$f['id'] ?>" class="action-btn action-btn--view" title="Download / View"
                       target="_blank" rel="noopener noreferrer">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </a>
                    <a href="?id=<?= $id ?>&delete_file=<?= (int)$f['id'] ?>"
                       class="action-btn action-btn--delete" title="Delete file"
                       onclick="return confirm('Are you sure you want to delete <?= h(addslashes($f['original_filename'])) ?>?')">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
<?php require __DIR__ . '/includes/footer.php'; ?>
