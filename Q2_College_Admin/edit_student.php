<?php
/* Q2 EDIT STUDENT */

$pageTitle = 'Edit Student';
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
$stmt = $pdo->prepare('SELECT * FROM students WHERE id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    flash_set('error', 'Student not found.');
    redirect('students.php');
}

$errors = [];
$values = $student; // Default form values to existing student data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* Collect and sanitise inputs */
    $values = [
        'full_name'          => trim($_POST['full_name']          ?? ''),
        'roll_number'        => trim($_POST['roll_number']        ?? ''),
        'email'              => trim($_POST['email']              ?? ''),
        'phone'              => trim($_POST['phone']              ?? ''),
        'alt_phone'          => trim($_POST['alt_phone']          ?? ''),
        'date_of_birth'      => trim($_POST['date_of_birth']      ?? ''),
        'gender'             => trim($_POST['gender']             ?? ''),
        'nationality'        => trim($_POST['nationality']        ?? 'Indian'),
        'address'            => trim($_POST['address']            ?? ''),
        'city'               => trim($_POST['city']               ?? ''),
        'pincode'            => trim($_POST['pincode']            ?? ''),
        'sslc_school'        => trim($_POST['sslc_school']        ?? ''),
        'sslc_year'          => trim($_POST['sslc_year']          ?? ''),
        'sslc_percentage'    => trim($_POST['sslc_percentage']    ?? ''),
        'hsc_school'         => trim($_POST['hsc_school']         ?? ''),
        'hsc_year'           => trim($_POST['hsc_year']           ?? ''),
        'hsc_percentage'     => trim($_POST['hsc_percentage']     ?? ''),
        'hsc_stream'         => trim($_POST['hsc_stream']         ?? ''),
        'maths_percentage'   => trim($_POST['maths_percentage']   ?? ''),
        'course'             => trim($_POST['course']             ?? ''),
        'alternative_course' => trim($_POST['alternative_course'] ?? ''),
        'admission_category' => trim($_POST['admission_category'] ?? 'general'),
        'admission_status'   => trim($_POST['admission_status']   ?? 'pending'),
        'portal_password'    => $_POST['portal_password']         ?? '',
    ];

    /* Server-side validation */
    if ($values['full_name'] === '')                       $errors['full_name']   = 'Full name is required.';
    if (!preg_match('/^[A-Za-z0-9]{4,20}$/', $values['roll_number']))
                                                           $errors['roll_number'] = 'Roll number must be 4–20 alphanumeric characters.';
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) $errors['email']    = 'Enter a valid email address.';
    if (!preg_match('/^[6-9][0-9]{9}$/', $values['phone']))   $errors['phone']    = 'Enter a valid 10-digit mobile number.';
    if ($values['course'] === '')                          $errors['course']      = 'Course selection is required.';
    if ($values['sslc_school'] === '')                     $errors['sslc_school'] = 'SSLC school name is required.';
    if ($values['hsc_school']  === '')                     $errors['hsc_school']  = 'HSC school name is required.';
    if ($values['hsc_stream']  === '')                     $errors['hsc_stream']  = 'HSC stream is required.';

    /* Check uniqueness excluding this student */
    if (empty($errors['roll_number'])) {
        $chk = $pdo->prepare('SELECT id FROM students WHERE roll_number = ? AND id != ?');
        $chk->execute([$values['roll_number'], $id]);
        if ($chk->fetch()) $errors['roll_number'] = 'This roll number is already used by another student.';
    }
    if (empty($errors['email'])) {
        $chk = $pdo->prepare('SELECT id FROM students WHERE email = ? AND id != ?');
        $chk->execute([$values['email'], $id]);
        if ($chk->fetch()) $errors['email'] = 'This email is already registered by another student.';
    }

    /* Optional password update */
    $newPasswordHash = null;
    if ($values['portal_password'] !== '') {
        if (strlen($values['portal_password']) < 8) {
            $errors['portal_password'] = 'Password must be at least 8 characters.';
        } else {
            $newPasswordHash = password_hash($values['portal_password'], PASSWORD_BCRYPT);
        }
    }

    if (empty($errors)) {
        if ($newPasswordHash !== null) {
            $stmt = $pdo->prepare(
                'UPDATE students SET
                 full_name = ?, roll_number = ?, email = ?, phone = ?, alt_phone = ?,
                 date_of_birth = ?, gender = ?, nationality = ?, address = ?, city = ?, pincode = ?,
                 sslc_school = ?, sslc_year = ?, sslc_percentage = ?,
                 hsc_school = ?, hsc_year = ?, hsc_percentage = ?, hsc_stream = ?, maths_percentage = ?,
                 course = ?, alternative_course = ?, admission_category = ?, admission_status = ?,
                 portal_password_hash = ?
                 WHERE id = ?'
            );
            $stmt->execute([
                $values['full_name'], $values['roll_number'], $values['email'],
                $values['phone'], $values['alt_phone'] ?: null,
                $values['date_of_birth'] ?: null, $values['gender'] ?: null,
                $values['nationality'], $values['address'], $values['city'], $values['pincode'],
                $values['sslc_school'], $values['sslc_year'] ?: null, $values['sslc_percentage'] ?: null,
                $values['hsc_school'], $values['hsc_year'] ?: null, $values['hsc_percentage'] ?: null,
                $values['hsc_stream'], $values['maths_percentage'] ?: null,
                $values['course'], $values['alternative_course'] ?: null,
                $values['admission_category'], $values['admission_status'],
                $newPasswordHash, $id
            ]);
        } else {
            $stmt = $pdo->prepare(
                'UPDATE students SET
                 full_name = ?, roll_number = ?, email = ?, phone = ?, alt_phone = ?,
                 date_of_birth = ?, gender = ?, nationality = ?, address = ?, city = ?, pincode = ?,
                 sslc_school = ?, sslc_year = ?, sslc_percentage = ?,
                 hsc_school = ?, hsc_year = ?, hsc_percentage = ?, hsc_stream = ?, maths_percentage = ?,
                 course = ?, alternative_course = ?, admission_category = ?, admission_status = ?
                 WHERE id = ?'
            );
            $stmt->execute([
                $values['full_name'], $values['roll_number'], $values['email'],
                $values['phone'], $values['alt_phone'] ?: null,
                $values['date_of_birth'] ?: null, $values['gender'] ?: null,
                $values['nationality'], $values['address'], $values['city'], $values['pincode'],
                $values['sslc_school'], $values['sslc_year'] ?: null, $values['sslc_percentage'] ?: null,
                $values['hsc_school'], $values['hsc_year'] ?: null, $values['hsc_percentage'] ?: null,
                $values['hsc_stream'], $values['maths_percentage'] ?: null,
                $values['course'], $values['alternative_course'] ?: null,
                $values['admission_category'], $values['admission_status'],
                $id
            ]);
        }

        /* Check if new profile photo was uploaded */
        if (!empty($_FILES['profile_photo']['name'])) {
            $result = handle_upload($_FILES['profile_photo'], 'profile_photo', "student_{$id}/");
            if ($result['ok']) {
                // Delete previous profile photo if one existed
                $oldPhoto = $pdo->prepare("SELECT id, relative_path FROM student_files WHERE student_id = ? AND document_category = 'profile_photo'");
                $oldPhoto->execute([$id]);
                while ($old = $oldPhoto->fetch()) {
                    delete_upload($old['relative_path']);
                    $pdo->prepare('DELETE FROM student_files WHERE id = ?')->execute([$old['id']]);
                }
                save_file_metadata($pdo, $id, $result);
            } else {
                flash_set('error', 'Profile photo upload failed: ' . $result['error']);
                redirect("edit_student.php?id={$id}");
            }
        }

        flash_set('success', 'Student details updated successfully.');
        redirect("view_student.php?id={$id}");
    }
}

// Fetch current profile photo if available
$photoStmt = $pdo->prepare("SELECT relative_path FROM student_files WHERE student_id = ? AND document_category = 'profile_photo' LIMIT 1");
$photoStmt->execute([$id]);
$currentPhoto = $photoStmt->fetchColumn();

require __DIR__ . '/includes/header.php';
?>
  <div class="container admin-container">
    <div class="page-header">
      <div>
        <h1>Edit Student</h1>
        <p class="page-subtitle">Update records for <strong><?= h($student['full_name']) ?></strong> (<?= h($student['roll_number']) ?>)</p>
      </div>
      <div style="display:flex;gap:0.75rem">
        <a href="view_student.php?id=<?= $id ?>" class="btn btn-outline-primary">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          View Profile
        </a>
        <a href="upload.php?id=<?= $id ?>" class="btn btn-secondary btn-sm">
          Manage Documents
        </a>
      </div>
    </div>

    <?php if (!empty($errors)): ?>
      <div class="alert alert--error" role="alert">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div><strong>Please correct the following errors:</strong>
          <ul><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul>
        </div>
      </div>
    <?php endif; ?>

    <div class="admin-card">
      <form method="post" action="" enctype="multipart/form-data" novalidate>

        <!-- PERSONAL DETAILS -->
        <div class="form-section-heading">Personal Details</div>
        <div class="form-grid-2">
          <div class="form-group">
            <label for="full_name">Full Name <span class="required">*</span></label>
            <input type="text" id="full_name" name="full_name" class="form-control <?= isset($errors['full_name']) ? 'error' : '' ?>"
                   value="<?= h($values['full_name'] ?? '') ?>" required>
            <?php if (isset($errors['full_name'])): ?><span class="error-msg visible"><?= h($errors['full_name']) ?></span><?php endif; ?>
          </div>
          <div class="form-group">
            <label for="roll_number">Roll Number <span class="required">*</span></label>
            <input type="text" id="roll_number" name="roll_number" class="form-control <?= isset($errors['roll_number']) ? 'error' : '' ?>"
                   value="<?= h($values['roll_number'] ?? '') ?>" required>
            <?php if (isset($errors['roll_number'])): ?><span class="error-msg visible"><?= h($errors['roll_number']) ?></span><?php endif; ?>
          </div>
        </div>
        <div class="form-grid-2">
          <div class="form-group">
            <label for="email">Email Address <span class="required">*</span></label>
            <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'error' : '' ?>"
                   value="<?= h($values['email'] ?? '') ?>" required>
            <?php if (isset($errors['email'])): ?><span class="error-msg visible"><?= h($errors['email']) ?></span><?php endif; ?>
          </div>
          <div class="form-group">
            <label for="phone">Phone <span class="required">*</span></label>
            <input type="tel" id="phone" name="phone" class="form-control <?= isset($errors['phone']) ? 'error' : '' ?>"
                   value="<?= h($values['phone'] ?? '') ?>" required>
            <?php if (isset($errors['phone'])): ?><span class="error-msg visible"><?= h($errors['phone']) ?></span><?php endif; ?>
          </div>
        </div>
        <div class="form-grid-3">
          <div class="form-group">
            <label for="date_of_birth">Date of Birth</label>
            <input type="date" id="date_of_birth" name="date_of_birth" class="form-control"
                   value="<?= h($values['date_of_birth'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" class="form-control">
              <option value="">Select</option>
              <?php foreach (['male'=>'Male','female'=>'Female','other'=>'Other','prefer-not'=>'Prefer not to say'] as $v=>$l): ?>
                <option value="<?= $v ?>" <?= ($values['gender'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="city">City</label>
            <input type="text" id="city" name="city" class="form-control" value="<?= h($values['city'] ?? '') ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="address">Address</label>
          <textarea id="address" name="address" class="form-control" rows="2"><?= h($values['address'] ?? '') ?></textarea>
        </div>

        <!-- ACADEMIC -->
        <div class="form-section-heading">Academic Qualifications</div>
        <div class="form-grid-3">
          <div class="form-group">
            <label for="sslc_school">SSLC School <span class="required">*</span></label>
            <input type="text" id="sslc_school" name="sslc_school" class="form-control <?= isset($errors['sslc_school']) ? 'error' : '' ?>"
                   value="<?= h($values['sslc_school'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label for="sslc_year">SSLC Year</label>
            <input type="number" id="sslc_year" name="sslc_year" class="form-control" min="2000" max="2030"
                   value="<?= h($values['sslc_year'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="sslc_percentage">SSLC %</label>
            <input type="number" id="sslc_percentage" name="sslc_percentage" class="form-control" min="0" max="100" step="0.01"
                   value="<?= h($values['sslc_percentage'] ?? '') ?>">
          </div>
        </div>
        <div class="form-grid-3">
          <div class="form-group">
            <label for="hsc_school">HSC School <span class="required">*</span></label>
            <input type="text" id="hsc_school" name="hsc_school" class="form-control <?= isset($errors['hsc_school']) ? 'error' : '' ?>"
                   value="<?= h($values['hsc_school'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label for="hsc_year">HSC Year</label>
            <input type="number" id="hsc_year" name="hsc_year" class="form-control" min="2000" max="2030"
                   value="<?= h($values['hsc_year'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label for="hsc_percentage">HSC %</label>
            <input type="number" id="hsc_percentage" name="hsc_percentage" class="form-control" min="0" max="100" step="0.01"
                   value="<?= h($values['hsc_percentage'] ?? '') ?>">
          </div>
        </div>
        <div class="form-grid-2">
          <div class="form-group">
            <label for="hsc_stream">HSC Stream <span class="required">*</span></label>
            <select id="hsc_stream" name="hsc_stream" class="form-control <?= isset($errors['hsc_stream']) ? 'error' : '' ?>">
              <option value="">Select stream</option>
              <?php foreach (['pcm'=>'Physics, Chemistry, Maths','pcb'=>'Physics, Chemistry, Biology','pcmb'=>'PCM + Biology','commerce'=>'Commerce','arts'=>'Arts / Humanities'] as $v=>$l): ?>
                <option value="<?= $v ?>" <?= ($values['hsc_stream'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="maths_percentage">Maths %</label>
            <input type="number" id="maths_percentage" name="maths_percentage" class="form-control" min="0" max="100" step="0.01"
                   value="<?= h($values['maths_percentage'] ?? '') ?>">
          </div>
        </div>

        <!-- COURSE & STATUS -->
        <div class="form-section-heading">Course and Admission</div>
        <div class="form-grid-2">
          <div class="form-group">
            <label for="course">Course <span class="required">*</span></label>
            <select id="course" name="course" class="form-control <?= isset($errors['course']) ? 'error' : '' ?>" required>
              <option value="">Select course</option>
              <?php
              $courseList = ['B.E. Computer Science and Engineering','B.E. Electronics and Communication Engineering','B.E. Electrical and Electronics Engineering','B.E. Mechanical Engineering','B.E. Civil Engineering','B.E. Artificial Intelligence and Data Science','B.Tech. Information Technology','B.Tech. Biotechnology','M.E. Computer Science and Engineering','M.E. VLSI Design','M.E. CAD / CAM','M.B.A.','M.C.A.'];
              foreach ($courseList as $c):
              ?>
                <option value="<?= h($c) ?>" <?= ($values['course'] ?? '') === $c ? 'selected' : '' ?>><?= h($c) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="admission_status">Admission Status</label>
            <select id="admission_status" name="admission_status" class="form-control">
              <?php foreach (['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $v=>$l): ?>
                <option value="<?= $v ?>" <?= ($values['admission_status'] ?? 'pending') === $v ? 'selected' : '' ?>><?= $l ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- PORTAL PASSWORD -->
        <div class="form-section-heading">Student Portal Account</div>
        <div class="form-group" style="max-width:400px">
          <label for="portal_password">Change Portal Password</label>
          <input type="password" id="portal_password" name="portal_password" class="form-control <?= isset($errors['portal_password']) ? 'error' : '' ?>"
                 placeholder="Leave blank to keep existing password" autocomplete="new-password">
          <?php if (isset($errors['portal_password'])): ?><span class="error-msg visible"><?= h($errors['portal_password']) ?></span><?php endif; ?>
          <small style="color:var(--color-text-muted);font-size:0.78rem">Only fill this if you want to change the student's password.</small>
        </div>

        <!-- PROFILE PHOTO -->
        <div class="form-section-heading">Profile Photo</div>
        <div class="form-group" style="max-width:500px">
          <?php if ($currentPhoto): ?>
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:0.75rem">
              <img src="download.php?path=<?= urlencode($currentPhoto) ?>&type=image" alt="Current Photo" class="student-thumb" style="width:54px;height:54px;border-radius:50%">
              <span style="font-size:0.875rem;color:var(--color-text-muted)">Current profile photo active. Choose a new file to replace it.</span>
            </div>
          <?php endif; ?>
          <label for="profile_photo">Upload New Photo</label>
          <input type="file" id="profile_photo" name="profile_photo" class="form-control" accept=".jpg,.jpeg,.png">
          <small style="color:var(--color-text-muted);font-size:0.78rem">Max 2 MB. Accepted formats: JPG, PNG</small>
        </div>

        <div class="form-actions">
          <a href="view_student.php?id=<?= $id ?>" class="btn btn-outline-primary">Cancel</a>
          <button type="submit" class="btn btn-primary" id="updateStudentBtn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Update Student
          </button>
        </div>

      </form>
    </div>
  </div>
<?php require __DIR__ . '/includes/footer.php'; ?>
