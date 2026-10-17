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
    $password          = $_POST['password'] ?? '';
    $declaration       = isset($_POST['declaration']);

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
    $fileMap = [
        'profilePhoto' => 'profile_photo',
        'idProof'      => 'id_proof',
        'sslcCert'     => 'sslc_certificate',
        'hscCert'      => 'hsc_certificate',
    ];

    foreach ($fileMap as $inputKey => $category) {
        if (!empty($_FILES[$inputKey]['name']) && $_FILES[$inputKey]['error'] === UPLOAD_ERR_OK) {
            $f = $_FILES[$inputKey];
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, UPLOAD_ALLOWED_EXTENSIONS, true)) continue;
            if ($f['size'] > UPLOAD_MAX_BYTES) continue;

            $subDir = 'student_' . $studentId . '/';
            $targetDir = UPLOAD_BASE_DIR . $subDir;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $storedName = sprintf('%08d_%s_%s.%s', $studentId, $category, bin2hex(random_bytes(6)), $ext);
            $destination = $targetDir . $storedName;

            if (move_uploaded_file($f['tmp_name'], $destination)) {
                $fStmt = $pdo->prepare(
                    'INSERT INTO student_files (student_id, original_filename, stored_filename, relative_path, mime_type, file_size, document_category)
                     VALUES (?, ?, ?, ?, ?, ?, ?)'
                );
                $fStmt->execute([
                    $studentId,
                    basename($f['name']),
                    $storedName,
                    $subDir . $storedName,
                    $f['type'] ?: 'application/octet-stream',
                    $f['size'],
                    $category
                ]);
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
