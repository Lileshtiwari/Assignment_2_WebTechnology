<?php
/* KPRIET STUDENT PORTAL LOGIN */

require_once dirname(__DIR__) . '/Q2_College_Admin/config/auth.php';
ensure_session();

/* Redirect if already logged in */
if (!empty($_SESSION['student_id'])) {
    header('Location: profile.php');
    exit;
}

$error = null;
$info  = null;

if (isset($_GET['logout']) && $_GET['logout'] === '1') {
    $info = 'You have been successfully logged out.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';

    if ($identifier === '' || $password === '') {
        $error = 'Please enter both your Roll Number / Email and your password.';
    } else {
        $pdo = db();
        $stmt = $pdo->prepare('SELECT id, full_name, roll_number, email, portal_password_hash, admission_status FROM students WHERE roll_number = ? OR email = ? LIMIT 1');
        $stmt->execute([$identifier, $identifier]);
        $student = $stmt->fetch();

        if (!$student || empty($student['portal_password_hash']) || !password_verify($password, $student['portal_password_hash'])) {
            $error = 'Invalid roll number/email or portal password.';
        } else {
            session_regenerate_id(true);
            $_SESSION['student_id']     = $student['id'];
            $_SESSION['student_name']   = $student['full_name'];
            $_SESSION['student_roll']   = $student['roll_number'];
            $_SESSION['student_email']  = $student['email'];
            $_SESSION['student_status'] = $student['admission_status'];

            header('Location: profile.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Portal Login &mdash; KPRIET</title>
  <link rel="icon" type="image/png" href="../kprietLogo.png">
  <link rel="stylesheet" href="../assets/css/shared.css">
  <link rel="stylesheet" href="../style.css">
</head>
<body class="login-body">

  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <a href="../index.html" style="text-decoration:none;display:inline-block">
          <img src="../kprietLogo.png" alt="KPRIET Logo" class="login-logo">
        </a>
        <h1>Student Portal Login</h1>
        <p class="login-subtitle">Student Profile &amp; Academic Information System</p>
      </div>

      <?php if ($error !== null): ?>
        <div class="alert alert--error" role="alert">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <div><?= h($error) ?></div>
        </div>
      <?php endif; ?>

      <?php if ($info !== null): ?>
        <div class="flash flash--success" style="margin-bottom:1.25rem;border-radius:var(--radius-sm)">
          <div style="display:flex;align-items:center;gap:0.5rem">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span><?= h($info) ?></span>
          </div>
        </div>
      <?php endif; ?>

      <form method="post" action="" novalidate>
        <div class="form-group">
          <label for="identifier">Roll Number or Email <span class="required">*</span></label>
          <input type="text" id="identifier" name="identifier" class="form-control"
                 value="<?= h($_POST['identifier'] ?? '') ?>"
                 placeholder="e.g. 21CS001 or student@example.com"
                 autocomplete="username" required autofocus>
        </div>

        <div class="form-group">
          <label for="password">Portal Password <span class="required">*</span></label>
          <div class="input-with-toggle">
            <input type="password" id="password" name="password" class="form-control"
                   placeholder="Enter portal password"
                   autocomplete="current-password" required>
            <button type="button" class="toggle-password" id="togglePassword" aria-label="Show password">
              <svg class="icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="icon-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;margin-top:1rem" id="studentLoginBtn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          Sign In to Portal
        </button>

        <div class="login-footer">
          <a href="../index.html">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Main Site
          </a>
        </div>
      </form>
    </div>
  </div>

  <script>
    const toggleBtn = document.getElementById('togglePassword');
    const pwdInput = document.getElementById('password');
    if (toggleBtn && pwdInput) {
      toggleBtn.addEventListener('click', () => {
        const isPassword = pwdInput.type === 'password';
        pwdInput.type = isPassword ? 'text' : 'password';
        const eye = toggleBtn.querySelector('.icon-eye');
        const eyeOff = toggleBtn.querySelector('.icon-eye-off');
        if (eye && eyeOff) {
          eye.style.display = isPassword ? 'none' : 'block';
          eyeOff.style.display = isPassword ? 'block' : 'none';
        }
      });
    }
  </script>
</body>
</html>
