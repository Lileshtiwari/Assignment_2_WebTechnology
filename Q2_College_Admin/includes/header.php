<?php
/* Q2 SHARED PHP HEADER — included by all admin pages */

require_once dirname(__DIR__) . '/config/auth.php';

$admin = current_admin();
$flash = flash_get();

$pageTitle    = $pageTitle ?? 'Admin Panel';
$activeNav    = $activeNav ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($pageTitle) ?> &mdash; KPRIET Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="icon" type="image/png" href="../kprietLogo.png">
  <link rel="stylesheet" href="../assets/css/shared.css">
  <link rel="stylesheet" href="../style.css">
</head>
<body class="admin-body">

  <!-- ADMIN HEADER -->
  <header class="site-header admin-header" role="banner">
    <div class="nav-inner">
      <a href="../index.html" class="nav-logo" aria-label="KPRIET Home">
        <img src="../kprietLogo.png" alt="KPR Institute of Engineering and Technology Logo">
      </a>
      <nav class="nav-links" id="navLinks" role="navigation" aria-label="Admin navigation">
        <a href="../index.html">Main Site</a>
        <a href="dashboard.php"   class="<?= $activeNav==='dashboard'?'active':'' ?>">Dashboard</a>
        <a href="students.php"    class="<?= $activeNav==='students' ?'active':'' ?>">Students</a>
        <a href="add_student.php" class="<?= $activeNav==='add'      ?'active':'' ?>">Add Student</a>
      </nav>
      <div class="nav-actions">
        <?php if ($admin): ?>
          <span class="admin-name"><?= h($admin['full_name']) ?></span>
          <a href="auth/logout.php" class="btn btn-secondary btn-sm">Sign Out</a>
        <?php endif; ?>
      </div>
      <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <!-- FLASH MESSAGE -->
  <?php if ($flash): ?>
    <div class="flash flash--<?= h($flash['type']) ?>" role="alert" aria-live="polite">
      <div class="container">
        <?php if ($flash['type'] === 'success'): ?>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <?php else: ?>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <?php endif; ?>
        <span><?= h($flash['message']) ?></span>
      </div>
    </div>
  <?php endif; ?>

  <main class="admin-main" role="main">
