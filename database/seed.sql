-- KPRIET Database Seed Data
-- Run AFTER schema.sql
-- Creates one demo admin and sample students for testing

USE kpriet_admission;

-- ----------------------------------------------------------------
-- Demo admin account
-- Username: admin | Password: Admin@KPRIET1
-- Hash generated with PHP: password_hash('Admin@KPRIET1', PASSWORD_BCRYPT)
-- Change password immediately after first use in a real deployment.
-- ----------------------------------------------------------------
INSERT INTO admin_users (username, email, password_hash, full_name)
VALUES (
  'admin',
  'admin@kpriet.ac.in',
  '$2y$10$1Cdiaqozs8zE4X7PcMXaDuDdUrnLevcHd8RZGefWgKffmjFrBn3wm',
  'KPRIET Administrator'
)
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- ----------------------------------------------------------------
-- Sample student records
-- ----------------------------------------------------------------
INSERT INTO students
  (full_name, roll_number, email, phone, date_of_birth, gender, nationality,
   address, city, pincode,
   sslc_school, sslc_year, sslc_percentage,
   hsc_school,  hsc_year,  hsc_percentage, hsc_stream, maths_percentage,
   course, admission_category, admission_status,
   portal_password_hash)
VALUES
  ('Arjun Kumar',    '21CS001', 'arjun.kumar@example.com',   '9876543210',
   '2005-03-15', 'male',   'Indian',
   '12 Gandhi Nagar, Coimbatore', 'Coimbatore', '641001',
   'Government Higher Secondary School', 2021, 92.40,
   'Sri Ramakrishna Hr. Sec. School',   2023, 89.20, 'pcm', 95.00,
   'B.E. Computer Science and Engineering', 'general', 'approved',
   '$2y$10$qRHtHPhdiXqvhreTkHnAt.MZMlNJeyd4N4MNEmcu2P.el79chipxm'),

  ('Priya Lakshmi',  '21EC002', 'priya.lakshmi@example.com', '9876543211',
   '2005-07-22', 'female', 'Indian',
   '45 Bharathi Street, Tiruppur', 'Tiruppur', '641602',
   'St. Joseph Matric School', 2021, 96.00,
   'Kongu Matric Hr. Sec. School', 2023, 93.50, 'pcmb', 98.00,
   'B.E. Electronics and Communication Engineering', 'obc', 'approved',
   '$2y$10$qRHtHPhdiXqvhreTkHnAt.MZMlNJeyd4N4MNEmcu2P.el79chipxm'),

  ('Ravi Shankar',   '21ME003', 'ravi.shankar@example.com',  '9876543212',
   '2004-11-10', 'male',   'Indian',
   '78 Nehru Road, Erode', 'Erode', '638001',
   'Government Boys Hr. Sec. School', 2020, 85.00,
   'PSG Polytechnic College', 2022, 81.00, 'pcm', 80.00,
   'B.E. Mechanical Engineering', 'bc', 'pending',
   '$2y$10$qRHtHPhdiXqvhreTkHnAt.MZMlNJeyd4N4MNEmcu2P.el79chipxm')
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- ----------------------------------------------------------------
-- Note: student_files rows are inserted by the PHP upload module.
-- No binary file data is stored in the database.
-- File metadata (relative_path, stored_filename) references the
-- uploads directory on the server.
-- ----------------------------------------------------------------
SELECT CONCAT('Seed completed. Students: ', COUNT(*)) AS info FROM students;
