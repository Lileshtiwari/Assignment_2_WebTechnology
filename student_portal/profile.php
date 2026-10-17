<?php
/* KPRIET STUDENT PROFILE PORTAL */

require_once dirname(__DIR__) . '/Q2_College_Admin/config/auth.php';
ensure_session();

if (empty($_SESSION['student_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = db();
$stmt = $pdo->prepare('SELECT * FROM students WHERE id = ? LIMIT 1');
$stmt->execute([(int)$_SESSION['student_id']]);
$student = $stmt->fetch();

if (!$student) {
    unset($_SESSION['student_id']);
    header('Location: login.php');
    exit;
}

/* Fetch uploaded documents */
$stmtFiles = $pdo->prepare('SELECT * FROM student_files WHERE student_id = ? ORDER BY uploaded_at DESC');
$stmtFiles->execute([(int)$student['id']]);
$files = $stmtFiles->fetchAll();

/* Identify profile photo if any */
$profilePhoto = null;
foreach ($files as $f) {
    if ($f['document_category'] === 'profile_photo') {
        $profilePhoto = $f;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($student['full_name']) ?> &mdash; Student Profile | KPRIET</title>
  <link rel="icon" type="image/png" href="../kprietLogo.png">
  <link rel="stylesheet" href="../assets/css/shared.css">
  <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">

  <!-- TOP HEADER -->
  <header class="site-header admin-header" role="banner">
    <div class="nav-inner">
      <a href="../index.html" class="nav-logo" aria-label="KPRIET Home">
        <img src="../kprietLogo.png" alt="KPRIET Logo">
      </a>
      <nav class="nav-links" id="navLinks" role="navigation" aria-label="Student portal navigation">
        <a href="../index.html">Main Site</a>
        <a href="profile.php" class="active">My Profile</a>
        <a href="#documents">My Documents</a>
      </nav>
      <div class="nav-actions">
        <span class="admin-name">
          <?= h($student['full_name']) ?> (<?= h($student['roll_number']) ?>)
        </span>
        <a href="logout.php" class="btn btn-secondary btn-sm" id="logoutBtn">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Sign Out
        </a>
      </div>
      <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <main class="admin-main" role="main">
    <div class="container admin-container">

      <div class="page-header">
        <div>
          <h1><?= h($student['full_name']) ?></h1>
          <p class="page-subtitle">
            Roll Number: <strong><?= h($student['roll_number']) ?></strong> &bull;
            Department: <strong><?= h($student['course']) ?></strong>
          </p>
        </div>
        <div>
          <span class="badge-status badge-status--<?= h($student['admission_status']) ?> badge-lg">
            Status: <?= h(strtoupper($student['admission_status'])) ?>
          </span>
        </div>
      </div>

      <div class="view-grid">
        <!-- SIDEBAR -->
        <div class="view-sidebar">
          <div class="admin-card profile-card">
            <?php if ($profilePhoto): ?>
              <img src="download.php?id=<?= (int)$profilePhoto['id'] ?>&view=1"
                   alt="Profile Photo of <?= h($student['full_name']) ?>"
                   class="profile-photo" id="profilePhoto">
            <?php else: ?>
              <div class="profile-photo-placeholder" aria-label="No photo uploaded">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </div>
            <?php endif; ?>

            <h3 style="font-size:1.15rem;margin-bottom:0.25rem"><?= h($student['full_name']) ?></h3>
            <p style="font-size:0.85rem;color:var(--color-text-muted);margin-bottom:1rem"><code><?= h($student['roll_number']) ?></code></p>

            <div style="border-top:1px solid var(--color-border-light);padding-top:1rem;text-align:left;font-size:0.85rem">
              <div style="margin-bottom:0.6rem">
                <span style="color:var(--color-text-muted);display:block;font-size:0.75rem">Email Address</span>
                <strong><?= h($student['email']) ?></strong>
              </div>
              <div style="margin-bottom:0.6rem">
                <span style="color:var(--color-text-muted);display:block;font-size:0.75rem">Phone Number</span>
                <strong><?= h($student['phone']) ?></strong>
              </div>
              <div>
                <span style="color:var(--color-text-muted);display:block;font-size:0.75rem">Admission Category</span>
                <strong style="text-transform:capitalize"><?= h($student['admission_category']) ?></strong>
              </div>
            </div>
          </div>
        </div>

        <!-- MAIN DETAILS -->
        <div class="view-main">

          <!-- PERSONAL INFO -->
          <div class="admin-card">
            <div class="detail-section-heading">Personal Information</div>
            <dl class="detail-grid">
              <dt>Full Name</dt>
              <dd><?= h($student['full_name']) ?></dd>

              <dt>Roll Number</dt>
              <dd><code><?= h($student['roll_number']) ?></code></dd>

              <dt>Date of Birth</dt>
              <dd><?= $student['date_of_birth'] ? h(date('d F Y', strtotime($student['date_of_birth']))) : '&mdash;' ?></dd>

              <dt>Gender</dt>
              <dd style="text-transform:capitalize"><?= h($student['gender'] ?? '&mdash;') ?></dd>

              <dt>Nationality</dt>
              <dd><?= h($student['nationality'] ?? 'Indian') ?></dd>

              <dt>Residential Address</dt>
              <dd><?= nl2br(h($student['address'] ?? '&mdash;')) ?></dd>

              <dt>City / PIN Code</dt>
              <dd><?= h($student['city'] ?? '') ?> <?= $student['pincode'] ? '&mdash; ' . h($student['pincode']) : '' ?></dd>
            </dl>
          </div>

          <!-- ACADEMIC QUALIFICATIONS -->
          <div class="admin-card" style="margin-top:1.5rem">
            <div class="detail-section-heading">Academic Qualifications</div>
            <dl class="detail-grid">
              <dt>10th School</dt>
              <dd><?= h($student['sslc_school'] ?? '&mdash;') ?></dd>

              <dt>10th Year / Score</dt>
              <dd><?= h($student['sslc_year'] ?? '&mdash;') ?> &bull; <?= $student['sslc_percentage'] !== null ? h($student['sslc_percentage']) . '%' : '&mdash;' ?></dd>

              <dt>12th School</dt>
              <dd><?= h($student['hsc_school'] ?? '&mdash;') ?></dd>

              <dt>12th Stream / Year</dt>
              <dd style="text-transform:uppercase"><?= h($student['hsc_stream'] ?? '&mdash;') ?> &bull; <?= h($student['hsc_year'] ?? '&mdash;') ?></dd>

              <dt>12th Percentage</dt>
              <dd><?= $student['hsc_percentage'] !== null ? h($student['hsc_percentage']) . '%' : '&mdash;' ?></dd>

              <dt>Maths Score</dt>
              <dd><?= $student['maths_percentage'] !== null ? h($student['maths_percentage']) . '%' : '&mdash;' ?></dd>
            </dl>
          </div>

          <!-- COURSE DETAILS -->
          <div class="admin-card" style="margin-top:1.5rem">
            <div class="detail-section-heading">Course and Enrolment</div>
            <dl class="detail-grid">
              <dt>Allocated Course</dt>
              <dd><strong><?= h($student['course']) ?></strong></dd>

              <dt>Alternative Choice</dt>
              <dd><?= h($student['alternative_course'] ?? '&mdash;') ?></dd>

              <dt>Admission Status</dt>
              <dd>
                <span class="badge-status badge-status--<?= h($student['admission_status']) ?>">
                  <?= h(ucfirst($student['admission_status'])) ?>
                </span>
              </dd>

              <dt>Applied On</dt>
              <dd><?= h(date('d M Y, h:i A', strtotime($student['created_at']))) ?></dd>
            </dl>
          </div>

          <!-- UPLOADED DOCUMENTS -->
          <div class="admin-card" style="margin-top:1.5rem" id="documents">
            <div class="admin-card__header">
              <div class="detail-section-heading" style="margin:0">Uploaded Documents</div>
            </div>

            <?php if (empty($files)): ?>
              <div class="empty-state" style="padding:2rem 0">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <p>No documents uploaded yet.</p>
              </div>
            <?php else: ?>
              <div class="file-list">
                <?php foreach ($files as $f): ?>
                  <div class="file-item">
                    <div class="file-item__icon">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div class="file-item__info">
                      <span class="file-item__name"><?= h($f['original_filename']) ?></span>
                      <span class="file-item__meta">
                        <?= h(ucwords(str_replace('_', ' ', $f['document_category']))) ?> &bull;
                        <?= number_format($f['file_size'] / 1024, 1) ?> KB &bull;
                        <?= h(date('d M Y', strtotime($f['uploaded_at']))) ?>
                      </span>
                    </div>
                    <div class="file-item__actions">
                      <a href="download.php?id=<?= (int)$f['id'] ?>" class="btn btn-outline-primary btn-sm">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download
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
  </main>

  <footer class="admin-footer" role="contentinfo">
    <div class="container">
      <span>KPR Institute of Engineering and Technology &mdash; Student Portal</span>
      <span>Autonomous Institution &bull; Affiliated to Anna University</span>
    </div>
  </footer>

  <script>
    const toggle = document.getElementById('navToggle');
    const links  = document.getElementById('navLinks');
    if (toggle && links) {
      toggle.addEventListener('click', () => {
        const open = links.classList.toggle('open');
        toggle.setAttribute('aria-expanded', open);
      });
    }
  </script>
</body>
</html>
