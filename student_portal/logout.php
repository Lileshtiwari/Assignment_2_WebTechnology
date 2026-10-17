<?php
/* KPRIET STUDENT PORTAL LOGOUT */

require_once dirname(__DIR__) . '/Q2_College_Admin/config/auth.php';
ensure_session();

unset(
    $_SESSION['student_id'],
    $_SESSION['student_name'],
    $_SESSION['student_roll'],
    $_SESSION['student_email'],
    $_SESSION['student_status']
);

header('Location: login.php?logout=1');
exit;
