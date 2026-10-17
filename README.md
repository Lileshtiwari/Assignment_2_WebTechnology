# 🎓 KPRIET &mdash; Student Admission & Information Management System

<p align="center">
  <img src="kprietLogo.png" alt="KPR Institute of Engineering and Technology Logo" width="340">
</p>

<p align="center">
  <strong>KPR Institute of Engineering and Technology, Coimbatore</strong><br>
  <em>Autonomous Institution &bull; Affiliated to Anna University</em>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
  <img src="https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white" alt="HTML5">
  <img src="https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white" alt="CSS3">
  <img src="https://img.shields.io/badge/Apache-XAMPP-D22128?style=for-the-badge&logo=apache&logoColor=white" alt="Apache">
  <img src="https://img.shields.io/badge/Security-Bcrypt%20%26%20Prepared%20Statements-2ea44f?style=for-the-badge&logo=security&logoColor=white" alt="Security">
</p>

---

## ⚡ Tech Stack Overview

| Layer | Technologies & Tools | Key Capabilities |
| :--- | :--- | :--- |
| **Frontend UI/UX** | ![HTML5](https://img.shields.io/badge/-HTML5-E34F26?style=flat-square&logo=html5&logoColor=white) ![CSS3](https://img.shields.io/badge/-CSS3-1572B6?style=flat-square&logo=css3&logoColor=white) ![JavaScript](https://img.shields.io/badge/-JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black) | Multi-step interactive admission wizard, real-time client-side validation, password strength meter, drag-and-drop file uploaders, dynamic nationality switcher, responsive CSS custom property design system. |
| **Backend Engineering** | ![PHP](https://img.shields.io/badge/-PHP%208.2-777BB4?style=flat-square&logo=php&logoColor=white) ![PDO](https://img.shields.io/badge/-PHP%20PDO-8892BF?style=flat-square) ![Java](https://img.shields.io/badge/-Java%20Servlets-007396?style=flat-square&logo=java&logoColor=white) | RESTful AJAX endpoints, parameterized SQL queries, secure file uploads with MIME-type verification, Bcrypt password hashing (`PASSWORD_BCRYPT`), session fixation prevention (`session_regenerate_id`). |
| **Database & Storage** | ![MySQL](https://img.shields.io/badge/-MySQL%20InnoDB-4479A1?style=flat-square&logo=mysql&logoColor=white) | Relational schema (`admin_users`, `students`, `student_files`), foreign keys with cascade rules, indexed search attributes, isolated disk storage for uploads. |
| **Server & Environment** | ![Apache](https://img.shields.io/badge/-Apache%20HTTPD-D22128?style=flat-square&logo=apache&logoColor=white) ![XAMPP](https://img.shields.io/badge/-XAMPP-FB7A24?style=flat-square&logo=xampp&logoColor=white) | Local Apache environment, cross-browser compatibility, zero third-party framework runtime dependencies. |

---

## 🚀 Portals & Core Modules

### 1. Main Institutional Portal (`index.html`)
- **Central Landing Page**: Official gateway showcasing college accreditation, academic programmes, and instant navigation.
- **Unified Navigation**: Direct access to Admission, Administration, and Student Portal.

### 2. Student Admission Portal (`Q1_Student_Admission/`)
- **7-Stage Progressive Form**:
  1. *Personal Details* (Legal name, DOB, gender, dynamic nationality selector with dedicated foreign nationality input).
  2. *Contact Information* (Validated email, Indian mobile number pattern, residential address, PIN code).
  3. *Academic Qualifications* (10th SSLC and 12th HSC schools, passing years, percentages, stream selection, Mathematics cutoff score).
  4. *Course Preferences* (Undergraduate programmes, alternative choices, admission categories).
  5. *Portal Password Setup* (Interactive strength meter with security hint checklist).
  6. *Document Uploads* (Profile photo, ID proof, marksheets with client and server-side size/extension validation).
  7. *Declaration & Submission* (Mandatory terms acceptance and automated AJAX persistence).

### 3. College Admin Panel (`Q2_College_Admin/`)
- **Administrative Authentication**: Secure session-based login with password hashing.
- **Analytics Dashboard**: Real-time counter metrics for total applications, approved students, pending reviews, and uploaded documents.
- **Student Database Management (CRUD)**:
  - Add new student records manually.
  - View comprehensive student profiles with uploaded certificates.
  - Edit student details, admission categories, and approval statuses (`Pending`, `Approved`, `Rejected`).
  - Delete records with confirmation safeguards.
- **Search & Filtering**: Search by name, roll number, or email; filter by engineering department and admission status.

### 4. Student Profile Portal (`student_portal/`)
- **Student Authentication**: Secure sign-in using Roll Number or Email paired with student portal password.
- **Student Profile View**: Inspection of personal information, academic scores, and allotted engineering course.
- **Document Hub**: Authenticated downloading and streaming of uploaded mark sheets and identity cards.

---

## 📁 Repository Structure

```
Assignment_2_WebTechnology/
│
├── index.html                     # Main portal landing page
├── style.css                      # Landing page presentation styles
├── kprietLogo.png                 # Official KPRIET banner emblem
├── Registration.jpg               # Campus illustration asset
├── README.md                      # Comprehensive project documentation
├── .gitignore                     # Git ignore rules (protects credentials & uploads)
│
├── assets/
│   └── css/
│       └── shared.css             # Unified CSS design system & typography tokens
│
├── database/
│   ├── schema.sql                 # MySQL relational database DDL schema
│   └── seed.sql                   # Initial test seed records (admin & students)
│
├── Q1_Student_Admission/          # Admission Module
│   ├── index.html                 # Multi-step admission form
│   ├── script.js                  # Real-time client-side validator & AJAX submit
│   ├── style.css                  # Form layout and step wizard styles
│   └── submit.php                 # Backend submission & document upload handler
│
├── Q2_College_Admin/              # Administration Suite
│   ├── dashboard.php              # Metrics and recent applications overview
│   ├── students.php               # Search, filter, and paginate student records
│   ├── add_student.php            # Admin manual student registration
│   ├── view_student.php           # Comprehensive student profile view
│   ├── edit_student.php           # Update student records and status
│   ├── delete_student.php         # Student record deletion handler
│   ├── upload.php                 # Document upload manager
│   ├── download.php               # Authenticated file streaming endpoint
│   ├── auth/
│   │   ├── login.php              # Administrator sign-in page
│   │   └── logout.php             # Session termination script
│   ├── config/
│   │   ├── database.php           # Database connection & env config
│   │   ├── database.example.php   # Template for local credential overrides
│   │   ├── auth.php               # Session verification & CSRF/XSS helpers
│   │   └── upload_config.php      # File upload validation rules
│   ├── includes/
│   │   ├── header.php             # Admin layout header
│   │   └── footer.php             # Admin layout footer
│   └── uploads/                   # Stored student documents (git-ignored)
│       └── .gitkeep
│
└── student_portal/                # Student Services Suite
    ├── index.php                  # Entry redirector
    ├── login.php                  # Student sign-in page
    ├── profile.php                # Student dashboard & document access
    ├── download.php               # Student document retrieval handler
    └── logout.php                 # Student session logout
```

---

## ⚙️ Installation & Running Locally

### Step 1: Start XAMPP
1. Launch the **XAMPP Control Panel**.
2. Start both **Apache** and **MySQL**.

### Step 2: Import the Database
1. Open **phpMyAdmin** in your browser (`http://localhost/phpmyadmin` or `http://localhost:8080/phpmyadmin`).
2. Run the SQL script from `database/schema.sql` to create database `kpriet_admission`.
3. Run `database/seed.sql` to populate sample admin and student accounts.

### Step 3: Configure Database Credentials
Database settings are located in [`Q2_College_Admin/config/database.php`](Q2_College_Admin/config/database.php).
- By default, it connects to MySQL on port `3306` with user `root`.
- **For GitHub Safety**: To specify custom database passwords locally without exposing them on GitHub, copy `Q2_College_Admin/config/database.example.php` to `Q2_College_Admin/config/database.local.php`. Git automatically ignores `*.local.php` files.

---

## 🔑 Access URLs & Default Credentials

> **Note**: If your local Apache server is listening on port **8080**, use `http://localhost:8080/`. If default port **80**, use `http://localhost/`.

| Portal | URL | Credentials |
| :--- | :--- | :--- |
| **Main Site** | [`http://localhost:8080/Assignment_2_WebTechnology/index.html`](http://localhost:8080/Assignment_2_WebTechnology/index.html) | *Public access* |
| **Admission Form** | [`http://localhost:8080/Assignment_2_WebTechnology/Q1_Student_Admission/index.html`](http://localhost:8080/Assignment_2_WebTechnology/Q1_Student_Admission/index.html) | *Public access* |
| **Admin Panel** | [`http://localhost:8080/Assignment_2_WebTechnology/Q2_College_Admin/auth/login.php`](http://localhost:8080/Assignment_2_WebTechnology/Q2_College_Admin/auth/login.php) | **User:** `admin`<br>**Password:** `Admin@KPRIET1` |
| **Student Portal** | [`http://localhost:8080/Assignment_2_WebTechnology/student_portal/login.php`](http://localhost:8080/Assignment_2_WebTechnology/student_portal/login.php) | **Roll:** `21CS001`<br>**Password:** `Student@123` |

---

## 🔒 Security Best Practices Implemented

- **Prepared Statements**: All database operations use PDO prepared queries with parameterized values to eliminate SQL Injection.
- **Bcrypt Password Storage**: Passwords are saved as cryptographic one-way hashes via PHP's `password_hash()` and verified with `password_verify()`.
- **Session Protection**: Sessions enforce `HttpOnly` and `SameSite=Lax` cookies with `session_regenerate_id(true)` upon successful authentication.
- **XSS Sanitization**: User outputs are HTML-escaped with `htmlspecialchars(..., ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')`.
- **Git Secrets Isolation**:
  - Uploaded student certificates and sensitive profile pictures in `uploads/` are excluded from version control via `.gitignore`.
  - Database local configuration overrides (`database.local.php` and `.env`) are ignored to prevent credential leakage on GitHub.

---

<p align="center">
  &copy; 2026 KPR Institute of Engineering and Technology, Coimbatore. All rights reserved.
</p>
