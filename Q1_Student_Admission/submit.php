<?php
/* KPRIET PUBLIC STUDENT ADMISSION SUBMISSION ENDPOINT */

header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/Q2_College_Admin/config/auth.php';
require_once dirname(__DIR__) . '/Q2_College_Admin/config/upload_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
    exit;
}

try {
    $pdo = db();

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name']  ?? '');
    $fullName  = trim($firstName . ' ' . $lastName);
    if ($fullName === '' && !empty($_POST['full_name'])) {
        $fullName = trim($_POST['full_name']);
    }

    $rollNumber = strtoupper(trim($_POST['roll_number'] ?? ''));
    $email      = trim($_POST['email'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $altPhone   = trim($_POST['alt_phone'] ?? '');
    $dob        = trim($_POST['date_of_birth'] ?? '');
    $gender     = trim($_POST['gender'] ?? '');

    /* Nationality handling */
    $nationality = trim($_POST['nationality'] ?? 'Indian');
    if (!empty($_POST['other_nationality']) && (isset($_POST['nationalitySelect']) && $_POST['nationalitySelect'] === 'Other' || $nationality === 'Other')) {
        $nationality = trim($_POST['other_nationality']);
    }

    $address   = trim($_POST['address'] ?? '');
    $city      = trim($_POST['city'] ?? '');
    $pincode   = trim($_POST['pincode'] ?? '');

    $sslcSchool  = trim($_POST['sslc_school'] ?? '');
    $sslcYear    = !empty($_POST['sslc_year']) ? (int)$_POST['sslc_year'] : null;
    $sslcPercent = !empty($_POST['sslc_percentage']) ? (float)$_POST['sslc_percentage'] : null;

    $hscSchool   = trim($_POST['hsc_school'] ?? '');
    $hscYear     = !empty($_POST['hsc_year']) ? (int)$_POST['hsc_year'] : null;
    $hscPercent  = !empty($_POST['hsc_percentage']) ? (float)$_POST['hsc_percentage'] : null;
    $hscStream   = trim($_POST['hsc_stream'] ?? '');
    $mathsPercent = !empty($_POST['maths_percentage']) ? (float)$_POST['maths_percentage'] : null;

    $course            = trim($_POST['course'] ?? '');
    $alternativeCourse = trim($_POST['alternative_course'] ?? '');
    $category          = trim($_POST['admission_category'] ?? 'general');
    $password          = $_POST['password'] ?? ($_POST['portal_password'] ?? '');
    $declaration       = !empty($_POST['declaration']) || !empty($_POST['declaration_accepted']);

    /* Validation */
    if ($fullName === '') {
        throw new Exception('Full Name is required.');
    }
    if ($rollNumber === '' || !preg_match('/^[A-Za-z0-9]{4,20}$/', $rollNumber)) {
        throw new Exception('A valid Roll Number (4–20 alphanumeric characters) is required.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('A valid email address is required.');
    }
    if (!preg_match('/^[6-9][0-9]{9}$/', $phone)) {
        throw new Exception('A valid 10-digit mobile number is required.');
    }
    if ($course === '') {
        throw new Exception('Preferred course selection is required.');
    }
    if (!$declaration) {
        throw new Exception('You must accept the declaration to submit the application.');
    }

    /* Check duplicates */
    $stmtChk = $pdo->prepare('SELECT id FROM students WHERE roll_number = ? LIMIT 1');
    $stmtChk->execute([$rollNumber]);
    if ($stmtChk->fetch()) {
        throw new Exception("The roll number '{$rollNumber}' is already registered.");
    }

    $stmtChkMail = $pdo->prepare('SELECT id FROM students WHERE email = ? LIMIT 1');
    $stmtChkMail->execute([$email]);
    if ($stmtChkMail->fetch()) {
        throw new Exception("The email '{$email}' is already registered.");
    }

    /* Password hash */
    $passwordHash = null;
    if ($password !== '') {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    } else {
        $passwordHash = password_hash('Student@123', PASSWORD_BCRYPT);
    }

    /* Insert student */
    $stmtInsert = $pdo->prepare(
        'INSERT INTO students
         (full_name, roll_number, email, phone, alt_phone, date_of_birth, gender,
          nationality, address, city, pincode,
          sslc_school, sslc_year, sslc_percentage,
          hsc_school, hsc_year, hsc_percentage, hsc_stream, maths_percentage,
          course, alternative_course, admission_category, admission_status, portal_password_hash)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
    );
    $stmtInsert->execute([
        $fullName, $rollNumber, $email, $phone, $altPhone ?: null,
        $dob ?: null, $gender ?: null, $nationality ?: 'Indian',
        $address ?: null, $city ?: null, $pincode ?: null,
        $sslcSchool ?: null, $sslcYear, $sslcPercent,
        $hscSchool ?: null, $hscYear, $hscPercent,
        $hscStream ?: null, $mathsPercent,
        $course, $alternativeCourse ?: null,
        $category, 'pending', $passwordHash
    ]);

    $studentId = (int)$pdo->lastInsertId();

    /* Process document uploads if present */
    $uploadMappings = [
        'profile_photo'    => ['profile_photo', 'profilePhoto'],
        'id_proof'         => ['id_proof', 'idProof'],
        'sslc_certificate' => ['sslc_certificate', 'sslcCert'],
        'hsc_certificate'  => ['hsc_certificate', 'hscCert'],
    ];

    foreach ($uploadMappings as $category => $candidateKeys) {
        $uploadedFile = null;
        foreach ($candidateKeys as $key) {
            if (!empty($_FILES[$key]['name']) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                $uploadedFile = $_FILES[$key];
                break;
            }
        }

        if ($uploadedFile) {
            $uploadResult = handle_upload($uploadedFile, $category, "student_{$studentId}/");
            if ($uploadResult['ok']) {
                save_file_metadata($pdo, $studentId, $uploadResult);
            }
        }
    }

    echo json_encode([
        'success'     => true,
        'student_id'  => $studentId,
        'roll_number' => $rollNumber,
        'full_name'   => $fullName,
        'message'     => 'Application submitted successfully!'
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
