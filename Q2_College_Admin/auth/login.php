<?php
/* Q2 ADMIN LOGIN */

require_once dirname(__DIR__) . '/config/auth.php';
ensure_session();

/* Redirect if already logged in */
if (!empty($_SESSION['admin_id'])) {
    redirect('../dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password']       ?? '';

    if ($username === '') $errors[] = 'Username is required.';
    if ($password === '') $errors[] = 'Password is required.';

    if (empty($errors)) {
        $pdo  = db();
        $stmt = $pdo->prepare('SELECT id, username, password_hash, full_name, is_active FROM admin_users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            $errors[] = 'Invalid username or password.';
        } elseif (!$admin['is_active']) {
            $errors[] = 'This account has been deactivated.';
        } else {
            /* Regenerate session ID to prevent fixation */
            session_regenerate_id(true);
            $_SESSION['admin_id']       = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name']     = $admin['full_name'];

            $redirect = $_GET['redirect'] ?? '../dashboard.php';
            /* Validate redirect to prevent open redirect */
            if (!preg_match('/^\/Q2_College_Admin\//', $redirect)) {
                $redirect = '../dashboard.php';
            }
            redirect($redirect);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login &mdash; KPRIET</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" type="image/png" href="../../kprietLogo.png">
  <link rel="stylesheet" href="../../assets/css/shared.css">
  <link rel="stylesheet" href="../../style.css">
</head>
<body class="login-body">
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <img src="../../kprietLogo.png" alt="KPRIET Logo" class="login-logo">
        <h1>Admin Login</h1>
        <p class="login-subtitle">College Administration &amp; Student Information System</p>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert alert--error" role="alert">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <ul>
            <?php foreach ($errors as $e): ?>
              <li><?= h($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="" novalidate>
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" class="form-control"
                 value="<?= h($_POST['username'] ?? '') ?>"
                 placeholder="Enter admin username"
                 autocomplete="username" required autofocus>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-with-toggle">
            <input type="password" id="password" name="password" class="form-control"
                   placeholder="Enter password"
                   autocomplete="current-password" required>
            <button type="button" class="toggle-password" id="togglePassword" aria-label="Show password">
              <svg class="icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="icon-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;margin-top:0.5rem" id="loginBtn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          Sign In
        </button>
      </form>

      <div class="login-footer">
        <a href="../../index.html">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
          Return to Main Site
        </a>
      </div>
    </div>
  </div>
  <script>
    /* Password toggle */
    const btn = document.getElementById('togglePassword');
    const inp = document.getElementById('password');
    if (btn && inp) {
      btn.addEventListener('click', () => {
        const show = inp.type === 'password';
        inp.type = show ? 'text' : 'password';
        btn.querySelector('.icon-eye').style.display     = show ? 'none' : '';
        btn.querySelector('.icon-eye-off').style.display = show ? ''     : 'none';
      });
    }
  </script>
</body>
</html>
