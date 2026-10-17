package com.kpriet.portal.dao;

import com.kpriet.portal.model.Student;
import com.kpriet.portal.model.StudentFile;
import com.kpriet.portal.util.DBConnection;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

/**
 * Data Access Object for students and student files using JDBC.
 */
public class StudentDAO {

    /**
     * Find a student by roll number or email address.
     */
    public Student findByRollOrEmail(String identifier) {
        String sql = "SELECT * FROM students WHERE roll_number = ? OR email = ? LIMIT 1";
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql)) {

            ps.setString(1, identifier.trim());
            ps.setString(2, identifier.trim());

            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return mapStudent(rs);
                }
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return null;
    }

    /**
     * Find student by ID.
     */
    public Student findById(int id) {
        String sql = "SELECT * FROM students WHERE id = ? LIMIT 1";
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql)) {

            ps.setInt(1, id);

            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return mapStudent(rs);
                }
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return null;
    }

    /**
     * Get all uploaded files for a student.
     */
    public List<StudentFile> getStudentFiles(int studentId) {
        List<StudentFile> files = new ArrayList<>();
        String sql = "SELECT * FROM student_files WHERE student_id = ? ORDER BY document_category, uploaded_at DESC";

        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql)) {

            ps.setInt(1, studentId);

            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    files.add(mapStudentFile(rs));
                }
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return files;
    }

    /**
     * Get single file by ID.
     */
    public StudentFile getStudentFileById(int fileId) {
        String sql = "SELECT * FROM student_files WHERE id = ? LIMIT 1";
        try (Connection conn = DBConnection.getConnection();
             PreparedStatement ps = conn.prepareStatement(sql)) {

            ps.setInt(1, fileId);

            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return mapStudentFile(rs);
                }
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return null;
    }

    /**
     * Helper to map ResultSet row to Student object.
     */
    private Student mapStudent(ResultSet rs) throws SQLException {
        Student s = new Student();
        s.setId(rs.getInt("id"));
        s.setFullName(rs.getString("full_name"));
        s.setRollNumber(rs.getString("roll_number"));
        s.setEmail(rs.getString("email"));
        s.setPhone(rs.getString("phone"));
        s.setAltPhone(rs.getString("alt_phone"));
        s.setDateOfBirth(rs.getDate("date_of_birth"));
        s.setGender(rs.getString("gender"));
        s.setNationality(rs.getString("nationality"));
        s.setAddress(rs.getString("address"));
        s.setCity(rs.getString("city"));
        s.setPincode(rs.getString("pincode"));

        s.setSslcSchool(rs.getString("sslc_school"));
        int sslcYear = rs.getInt("sslc_year");
        s.setSslcYear(rs.wasNull() ? null : sslcYear);
        s.setSslcPercentage(rs.getBigDecimal("sslc_percentage"));

        s.setHscSchool(rs.getString("hsc_school"));
        int hscYear = rs.getInt("hsc_year");
        s.setHscYear(rs.wasNull() ? null : hscYear);
        s.setHscPercentage(rs.getBigDecimal("hsc_percentage"));
        s.setHscStream(rs.getString("hsc_stream"));
        s.setMathsPercentage(rs.getBigDecimal("maths_percentage"));

        s.setCourse(rs.getString("course"));
        s.setAlternativeCourse(rs.getString("alternative_course"));
        s.setAdmissionCategory(rs.getString("admission_category"));
        s.setAdmissionStatus(rs.getString("admission_status"));
        s.setPortalPasswordHash(rs.getString("portal_password_hash"));

        s.setCreatedAt(rs.getTimestamp("created_at"));
        s.setUpdatedAt(rs.getTimestamp("updated_at"));
        return s;
    }

    /**
     * Helper to map ResultSet row to StudentFile object.
     */
    private StudentFile mapStudentFile(ResultSet rs) throws SQLException {
        StudentFile f = new StudentFile();
        f.setId(rs.getInt("id"));
        f.setStudentId(rs.getInt("student_id"));
        f.setOriginalFilename(rs.getString("original_filename"));
        f.setStoredFilename(rs.getString("stored_filename"));
        f.setRelativePath(rs.getString("relative_path"));
        f.setMimeType(rs.getString("mime_type"));
        f.setFileSize(rs.getLong("file_size"));
        f.setDocumentCategory(rs.getString("document_category"));
        f.setUploadedAt(rs.getTimestamp("uploaded_at"));
        return f;
    }
}
