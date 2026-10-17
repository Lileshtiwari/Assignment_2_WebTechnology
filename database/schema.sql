-- KPRIET Student Admission and Information Management System
-- U21CS501 Web Technology — Assignment II
-- MySQL Schema v1.0
-- Compatible with PHP (Q2) and Java Servlets (Q3)

-- Create and select database
CREATE DATABASE IF NOT EXISTS kpriet_admission
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE kpriet_admission;

-- ----------------------------------------------------------------
-- TABLE: admin_users
-- Stores administrator accounts for the PHP admin panel (Q2)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(80)  NOT NULL UNIQUE,
  email         VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,          -- bcrypt hash
  full_name     VARCHAR(150) NOT NULL,
  is_active     TINYINT(1)   NOT NULL DEFAULT 1,
  created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_admin_username (username),
  INDEX idx_admin_email    (email)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- TABLE: students
-- Core student records shared between PHP (Q2) and Java (Q3)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS students (
  id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name           VARCHAR(150) NOT NULL,
  roll_number         VARCHAR(30)  NOT NULL UNIQUE,
  email               VARCHAR(255) NOT NULL UNIQUE,
  phone               VARCHAR(20)  NOT NULL,
  alt_phone           VARCHAR(20)  DEFAULT NULL,
  date_of_birth       DATE         DEFAULT NULL,
  gender              ENUM('male','female','other','prefer-not') DEFAULT NULL,
  nationality         VARCHAR(80)  DEFAULT 'Indian',
  address             TEXT         DEFAULT NULL,
  city                VARCHAR(100) DEFAULT NULL,
  pincode             VARCHAR(10)  DEFAULT NULL,
  -- Academic
  sslc_school         VARCHAR(200) DEFAULT NULL,
  sslc_year           SMALLINT     DEFAULT NULL,
  sslc_percentage     DECIMAL(5,2) DEFAULT NULL,
  hsc_school          VARCHAR(200) DEFAULT NULL,
  hsc_year            SMALLINT     DEFAULT NULL,
  hsc_percentage      DECIMAL(5,2) DEFAULT NULL,
  hsc_stream          VARCHAR(50)  DEFAULT NULL,
  maths_percentage    DECIMAL(5,2) DEFAULT NULL,
  -- Course
  course              VARCHAR(100) NOT NULL,
  alternative_course  VARCHAR(100) DEFAULT NULL,
  admission_category  VARCHAR(50)  DEFAULT 'general',
  -- Portal account (hashed)
  portal_password_hash VARCHAR(255) DEFAULT NULL,
  -- Metadata
  admission_status    ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_student_email       (email),
  INDEX idx_student_roll        (roll_number),
  INDEX idx_student_course      (course),
  INDEX idx_student_status      (admission_status)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- TABLE: student_files
-- Metadata for uploaded documents and profile images
-- Shared between PHP (Q2) and Java (Q3)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS student_files (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id        INT UNSIGNED NOT NULL,
  original_filename VARCHAR(255) NOT NULL,       -- as uploaded by the user
  stored_filename   VARCHAR(255) NOT NULL UNIQUE, -- UUID-based safe name
  relative_path     VARCHAR(500) NOT NULL,        -- relative to UPLOAD_BASE_DIR
  mime_type         VARCHAR(120) NOT NULL,
  file_size         INT UNSIGNED NOT NULL,        -- bytes
  document_category ENUM(
    'profile_photo',
    'id_proof',
    'sslc_certificate',
    'hsc_certificate',
    'other_certificate'
  ) NOT NULL DEFAULT 'other_certificate',
  uploaded_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_files_student
    FOREIGN KEY (student_id)
    REFERENCES students(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  INDEX idx_files_student   (student_id),
  INDEX idx_files_category  (document_category)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- Verify tables created correctly
-- ----------------------------------------------------------------
SHOW TABLES;
