<?php
/* Q2 STUDENT LIST WITH SEARCH AND PAGINATION */

$pageTitle = 'Students';
$activeNav = 'students';

require_once __DIR__ . '/config/auth.php';
require_admin();

$pdo = db();

/* Search / filter params */
$search   = trim($_GET['q']      ?? '');
$course   = trim($_GET['course'] ?? '');
$status   = trim($_GET['status'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 12;

/* Build WHERE clause */
$where  = [];
$params = [];

if ($search !== '') {
    $where[]  = '(s.full_name LIKE ? OR s.roll_number LIKE ? OR s.email LIKE ?)';
    $like     = '%' . $search . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
}
if ($course !== '') {
    $where[]  = 's.course = ?';
    $params[] = $course;
}
if (in_array($status, ['pending','approved','rejected'], true)) {
    $where[]  = 's.admission_status = ?';
    $params[] = $status;
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* Total count */
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM students s $whereSQL");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));
$page  = min($page, $pages);
$offset = ($page - 1) * $perPage;

/* Fetch students */
$stmt = $pdo->prepare(
    "SELECT s.id, s.full_name, s.roll_number, s.email, s.phone,
            s.course, s.admission_status, s.created_at,
            (SELECT sf.relative_path FROM student_files sf
             WHERE sf.student_id = s.id AND sf.document_category = 'profile_photo'
             LIMIT 1) AS profile_photo
     FROM students s
     $whereSQL
     ORDER BY s.created_at DESC
     LIMIT $perPage OFFSET $offset"
);
$stmt->execute($params);
$students = $stmt->fetchAll();

/* Course list for filter dropdown */
$courses = $pdo->query('SELECT DISTINCT course FROM students ORDER BY course')->fetchAll(PDO::FETCH_COLUMN);

require __DIR__ . '/includes/header.php';
?>
  <div class="container admin-container">

    <div class="page-header">
      <div>
        <h1>Students</h1>
        <p class="page-subtitle"><?= $total ?> student<?= $total !== 1 ? 's' : '' ?> found<?= ($search || $course || $status) ? ' (filtered)' : '' ?>.</p>
      </div>
      <a href="add_student.php" class="btn btn-primary" id="addStudentBtn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Student
      </a>
    </div>

    <!-- SEARCH & FILTER -->
    <form method="get" action="" class="filter-bar" role="search" aria-label="Filter students">
      <div class="filter-bar__search">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="search" name="q" id="searchQ" class="form-control" value="<?= h($search) ?>"
               placeholder="Search by name, roll number or email..." aria-label="Search students">
      </div>
      <select name="course" id="filterCourse" class="form-control filter-select" aria-label="Filter by course">
        <option value="">All Courses</option>
        <?php foreach ($courses as $c): ?>
          <option value="<?= h($c) ?>" <?= $course === $c ? 'selected' : '' ?>><?= h($c) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="status" id="filterStatus" class="form-control filter-select" aria-label="Filter by status">
        <option value="">All Statuses</option>
        <option value="pending"  <?= $status === 'pending'  ? 'selected' : '' ?>>Pending</option>
        <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
        <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
      </select>
      <button type="submit" class="btn btn-primary" id="searchBtn">Search</button>
      <?php if ($search || $course || $status): ?>
        <a href="students.php" class="btn btn-outline-primary" id="clearFilterBtn">Clear</a>
      <?php endif; ?>
    </form>

    <!-- STUDENT TABLE -->
    <div class="admin-card">
      <?php if (empty($students)): ?>
        <div class="empty-state">
          <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
          <p>No students match your search. <a href="students.php">Clear filters</a> or <a href="add_student.php">add a student</a>.</p>
        </div>
      <?php else: ?>
        <div class="table-wrapper">
          <table class="data-table" role="table">
            <thead>
              <tr>
                <th scope="col">Photo</th>
                <th scope="col">Name</th>
                <th scope="col">Roll No.</th>
                <th scope="col">Email</th>
                <th scope="col">Course</th>
                <th scope="col">Status</th>
                <th scope="col">Applied</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($students as $s): ?>
                <tr>
                  <td>
                    <?php if ($s['profile_photo']): ?>
                      <img src="download.php?path=<?= urlencode($s['profile_photo']) ?>&type=image"
                           alt="<?= h($s['full_name']) ?>" class="student-thumb">
                    <?php else: ?>
                      <div class="student-thumb-placeholder" aria-label="No photo">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td><strong><?= h($s['full_name']) ?></strong></td>
                  <td><code><?= h($s['roll_number']) ?></code></td>
                  <td><?= h($s['email']) ?></td>
                  <td class="course-cell"><?= h($s['course']) ?></td>
                  <td><span class="badge badge-status badge-status--<?= h($s['admission_status']) ?>"><?= h(ucfirst($s['admission_status'])) ?></span></td>
                  <td><?= h(date('d M Y', strtotime($s['created_at']))) ?></td>
                  <td class="actions">
                    <a href="view_student.php?id=<?= (int)$s['id'] ?>" class="action-btn action-btn--view" title="View">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                    <a href="edit_student.php?id=<?= (int)$s['id'] ?>" class="action-btn action-btn--edit" title="Edit">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <a href="delete_student.php?id=<?= (int)$s['id'] ?>"
                       class="action-btn action-btn--delete" title="Delete"
                       onclick="return confirm('Delete <?= h(addslashes($s['full_name'])) ?>?')">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- PAGINATION -->
        <?php if ($pages > 1): ?>
          <nav class="pagination" aria-label="Student list pagination">
            <?php
              $qp = http_build_query(array_filter(['q'=>$search,'course'=>$course,'status'=>$status]));
              $qp = $qp ? '&' . $qp : '';
            ?>
            <?php if ($page > 1): ?>
              <a href="?page=<?= $page - 1 ?><?= $qp ?>" class="page-btn" aria-label="Previous page">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
              </a>
            <?php endif; ?>
            <?php for ($i = max(1,$page-2); $i <= min($pages,$page+2); $i++): ?>
              <a href="?page=<?= $i ?><?= $qp ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>" aria-label="Page <?= $i ?>" <?= $i === $page ? 'aria-current="page"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $pages): ?>
              <a href="?page=<?= $page + 1 ?><?= $qp ?>" class="page-btn" aria-label="Next page">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
              </a>
            <?php endif; ?>
          </nav>
        <?php endif; ?>
      <?php endif; ?>
    </div>

  </div>
<?php require __DIR__ . '/includes/footer.php'; ?>
