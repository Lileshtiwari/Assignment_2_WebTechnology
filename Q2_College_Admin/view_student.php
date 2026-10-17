<?php
/* Q2 VIEW STUDENT DETAILS */

$pageTitle = 'Student Details';
$activeNav = 'students';

require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/upload_config.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { flash_set('error', 'Invalid student ID.'); redirect('students.php'); }

$pdo  = db();
$stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) { flash_set('error', 'Student not found.'); redirect('students.php'); }

/* Handle inline file delete before rendering */
if (isset($_GET['delete_file'])) {
    $fid   = (int)$_GET['delete_file'];
    $fstmt = $pdo->prepare('SELECT * FROM student_files WHERE id = ? AND student_id = ?');
    $fstmt->execute([$fid, $id]);
    $frow  = $fstmt->fetch();
    if ($frow) {
        delete_upload($frow['relative_path']);
        $pdo->prepare('DELETE FROM student_files WHERE id = ?')->execute([$fid]);
        flash_set('success', 'File deleted.');
        redirect("view_student.php?id=$id");
    }
}

/* Fetch uploaded files */
$files = $pdo->prepare('SELECT * FROM student_files WHERE student_id = ? ORDER BY document_category, uploaded_at DESC');
$files->execute([$id]);
$fileRows = $files->fetchAll();

/* Group files by category */
$filesByCategory = [];
foreach ($fileRows as $f) {
    $filesByCategory[$f['document_category']][] = $f;
}

require __DIR__ . '/includes/header.php';
?>
  <div class="container admin-container">
    <div class="page-header">
      <div>
        <h1><?= h($student['full_name']) ?></h1>
        <p class="page-subtitle">Roll No: <strong><?= h($student['roll_number']) ?></strong> &mdash; <?= h($student['course']) ?></p>
      </div>
      <div style="display:flex;gap:0.75rem;flex-wrap:wrap">
        <a href="edit_student.php?id=<?= $id ?>" class="btn btn-primary" id="editStudentBtn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Edit
        </a>
        <a href="upload.php?id=<?= $id ?>" class="btn btn-outline-primary" id="uploadDocBtn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          Upload Document
        </a>
        <a href="students.php" class="btn btn-outline-primary">Back</a>
      </div>
    </div>

    <div class="view-grid">
      <!-- LEFT: Profile -->
      <div class="view-sidebar">
        <div class="admin-card profile-card">
          <?php
          $profilePhoto = $filesByCategory['profile_photo'][0] ?? null;
          ?>
          <?php if ($profilePhoto): ?>
            <img src="download.php?path=<?= urlencode($profilePhoto['relative_path']) ?>&type=image"
                 alt="<?= h($student['full_name']) ?> profile photo"
                 class="profile-photo" id="profilePhoto">
          <?php else: ?>
            <div class="profile-photo-placeholder" aria-label="No profile photo uploaded">
              <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <span>No photo</span>
            </div>
          <?php endif; ?>
          <h3><?= h($student['full_name']) ?></h3>
          <code><?= h($student['roll_number']) ?></code>
          <span class="badge badge-status badge-status--<?= h($student['admission_status']) ?> badge-lg">
            <?= h(ucfirst($student['admission_status'])) ?>
          </span>
          <div class="profile-meta">
            <span><?= h($student['course']) ?></span>
            <span><?= h($student['email']) ?></span>
            <span><?= h($student['phone']) ?></span>
          </div>
        </div>
      </div>

      <!-- RIGHT: Details -->
      <div class="view-main">
        <!-- Personal -->
        <div class="admin-card">
          <div class="detail-section-heading">Personal Information</div>
          <dl class="detail-grid">
            <dt>Full Name</dt>       <dd><?= h($student['full_name']) ?></dd>
            <dt>Date of Birth</dt>   <dd><?= $student['date_of_birth'] ? h(date('d M Y', strtotime($student['date_of_birth']))) : '—' ?></dd>
            <dt>Gender</dt>          <dd><?= h(ucfirst($student['gender'] ?? '—')) ?></dd>
            <dt>Nationality</dt>     <dd><?= h($student['nationality'] ?? '—') ?></dd>
            <dt>Address</dt>         <dd><?= h($student['address'] ?? '—') ?></dd>
            <dt>City</dt>            <dd><?= h($student['city'] ?? '—') ?></dd>
            <dt>PIN Code</dt>        <dd><?= h($student['pincode'] ?? '—') ?></dd>
            <dt>Alt. Phone</dt>      <dd><?= h($student['alt_phone'] ?? '—') ?></dd>
          </dl>
        </div>

        <!-- Academic -->
        <div class="admin-card" style="margin-top:1.25rem">
          <div class="detail-section-heading">Academic Qualifications</div>
          <dl class="detail-grid">
            <dt>SSLC School</dt>     <dd><?= h($student['sslc_school'] ?? '—') ?></dd>
            <dt>SSLC Year</dt>       <dd><?= h($student['sslc_year'] ?? '—') ?></dd>
            <dt>SSLC %</dt>          <dd><?= $student['sslc_percentage'] !== null ? h($student['sslc_percentage']).'%' : '—' ?></dd>
            <dt>HSC School</dt>      <dd><?= h($student['hsc_school'] ?? '—') ?></dd>
            <dt>HSC Year</dt>        <dd><?= h($student['hsc_year'] ?? '—') ?></dd>
            <dt>HSC %</dt>           <dd><?= $student['hsc_percentage'] !== null ? h($student['hsc_percentage']).'%' : '—' ?></dd>
            <dt>HSC Stream</dt>      <dd><?= h(strtoupper($student['hsc_stream'] ?? '—')) ?></dd>
            <dt>Maths %</dt>         <dd><?= $student['maths_percentage'] !== null ? h($student['maths_percentage']).'%' : '—' ?></dd>
          </dl>
        </div>

        <!-- Course -->
        <div class="admin-card" style="margin-top:1.25rem">
          <div class="detail-section-heading">Course and Admission</div>
          <dl class="detail-grid">
            <dt>Course</dt>               <dd><?= h($student['course']) ?></dd>
            <dt>Alternative Course</dt>   <dd><?= h($student['alternative_course'] ?? '—') ?></dd>
            <dt>Category</dt>             <dd><?= h(strtoupper($student['admission_category'] ?? '—')) ?></dd>
            <dt>Status</dt>               <dd><span class="badge badge-status badge-status--<?= h($student['admission_status']) ?>"><?= h(ucfirst($student['admission_status'])) ?></span></dd>
            <dt>Applied On</dt>           <dd><?= h(date('d M Y, h:i A', strtotime($student['created_at']))) ?></dd>
            <dt>Last Updated</dt>         <dd><?= h(date('d M Y, h:i A', strtotime($student['updated_at']))) ?></dd>
          </dl>
        </div>

        <!-- Documents -->
        <div class="admin-card" style="margin-top:1.25rem" id="documents">
          <div class="admin-card__header">
            <div class="detail-section-heading" style="margin:0">Uploaded Documents</div>
            <a href="upload.php?id=<?= $id ?>" class="btn btn-outline-primary btn-sm">Upload More</a>
          </div>
          <?php if (empty($fileRows)): ?>
            <div class="empty-state" style="padding:2rem 0">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
              <p>No documents uploaded yet. <a href="upload.php?id=<?= $id ?>">Upload now.</a></p>
            </div>
          <?php else: ?>
            <div class="file-list">
              <?php foreach ($fileRows as $f): ?>
                <div class="file-item" id="file-<?= (int)$f['id'] ?>">
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
                      <?= h(str_replace('_',' ',ucfirst($f['document_category']))) ?> &bull;
                      <?= h(number_format($f['file_size']/1024, 1)) ?> KB &bull;
                      <?= h(date('d M Y', strtotime($f['uploaded_at']))) ?>
                    </span>
                  </div>
                  <div class="file-item__actions">
                    <a href="download.php?id=<?= (int)$f['id'] ?>" class="action-btn action-btn--view" title="Download / View"
                       target="_blank" rel="noopener noreferrer">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    </a>
                    <a href="?id=<?= $id ?>&delete_file=<?= (int)$f['id'] ?>"
                       class="action-btn action-btn--delete" title="Delete file"
                       onclick="return confirm('Delete this file?')">
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

<?php
require __DIR__ . '/includes/footer.php';
?>
