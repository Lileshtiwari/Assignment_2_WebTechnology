<?php
/* Q2 ADMIN DASHBOARD */

$pageTitle = 'Dashboard';
$activeNav = 'dashboard';

require_once __DIR__ . '/config/auth.php';
require_admin();

$pdo = db();

/* Summary statistics */
$stats = [
    'total'    => (int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn(),
    'approved' => (int)$pdo->query("SELECT COUNT(*) FROM students WHERE admission_status = 'approved'")->fetchColumn(),
    'pending'  => (int)$pdo->query("SELECT COUNT(*) FROM students WHERE admission_status = 'pending'")->fetchColumn(),
    'files'    => (int)$pdo->query('SELECT COUNT(*) FROM student_files')->fetchColumn(),
];

/* Recent students */
$recent = $pdo->query(
    'SELECT id, full_name, roll_number, course, admission_status, created_at
     FROM students ORDER BY created_at DESC LIMIT 8'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>
  <div class="container admin-container">

    <div class="page-header">
      <div>
        <h1>Dashboard</h1>
        <p class="page-subtitle">Welcome back, <?= h(current_admin()['full_name']) ?>. Here is a summary of the admission portal.</p>
      </div>
      <a href="add_student.php" class="btn btn-primary" id="addStudentBtn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Student
      </a>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--blue">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div>
          <div class="stat-card__num"><?= $stats['total'] ?></div>
          <div class="stat-card__label">Total Students</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--green">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <div>
          <div class="stat-card__num"><?= $stats['approved'] ?></div>
          <div class="stat-card__label">Approved</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--orange">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
          <div class="stat-card__num"><?= $stats['pending'] ?></div>
          <div class="stat-card__label">Pending Review</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card__icon stat-card__icon--purple">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
        </div>
        <div>
          <div class="stat-card__num"><?= $stats['files'] ?></div>
          <div class="stat-card__label">Uploaded Files</div>
        </div>
      </div>
    </div>

    <!-- RECENT STUDENTS TABLE -->
    <div class="admin-card">
      <div class="admin-card__header">
        <h2>Recent Applications</h2>
        <a href="students.php" class="btn btn-outline-primary btn-sm">View All</a>
      </div>
      <?php if (empty($recent)): ?>
        <div class="empty-state">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          <p>No student records found. <a href="add_student.php">Add the first student.</a></p>
        </div>
      <?php else: ?>
        <div class="table-wrapper">
          <table class="data-table" role="table">
            <thead>
              <tr>
                <th scope="col">Name</th>
                <th scope="col">Roll Number</th>
                <th scope="col">Course</th>
                <th scope="col">Status</th>
                <th scope="col">Applied On</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent as $s): ?>
                <tr>
                  <td><strong><?= h($s['full_name']) ?></strong></td>
                  <td><code><?= h($s['roll_number']) ?></code></td>
                  <td><?= h($s['course']) ?></td>
                  <td>
                    <span class="badge badge-status badge-status--<?= h($s['admission_status']) ?>">
                      <?= h(ucfirst($s['admission_status'])) ?>
                    </span>
                  </td>
                  <td><?= h(date('d M Y', strtotime($s['created_at']))) ?></td>
                  <td class="actions">
                    <a href="view_student.php?id=<?= (int)$s['id'] ?>" class="action-btn action-btn--view" title="View student">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <a href="edit_student.php?id=<?= (int)$s['id'] ?>" class="action-btn action-btn--edit" title="Edit student">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <a href="delete_student.php?id=<?= (int)$s['id'] ?>" class="action-btn action-btn--delete" title="Delete student"
                       onclick="return confirm('Delete <?= h(addslashes($s['full_name'])) ?>? This cannot be undone.')">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </div>
<?php require __DIR__ . '/includes/footer.php'; ?>
