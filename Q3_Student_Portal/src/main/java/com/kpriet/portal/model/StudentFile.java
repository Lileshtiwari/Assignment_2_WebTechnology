package com.kpriet.portal.model;

import java.io.Serializable;
import java.sql.Timestamp;

/**
 * StudentFile JavaBean matching the shared `student_files` MySQL table.
 */
public class StudentFile implements Serializable {
    private static final long serialVersionUID = 1L;

    private int id;
    private int studentId;
    private String originalFilename;
    private String storedFilename;
    private String relativePath;
    private String mimeType;
    private long fileSize;
    private String documentCategory;
    private Timestamp uploadedAt;

    public StudentFile() {}

    public int getId() { return id; }
    public void setId(int id) { this.id = id; }

    public int getStudentId() { return studentId; }
    public void setStudentId(int studentId) { this.studentId = studentId; }

    public String getOriginalFilename() { return originalFilename; }
    public void setOriginalFilename(String originalFilename) { this.originalFilename = originalFilename; }

    public String getStoredFilename() { return storedFilename; }
    public void setStoredFilename(String storedFilename) { this.storedFilename = storedFilename; }

    public String getRelativePath() { return relativePath; }
    public void setRelativePath(String relativePath) { this.relativePath = relativePath; }

    public String getMimeType() { return mimeType; }
    public void setMimeType(String mimeType) { this.mimeType = mimeType; }

    public long getFileSize() { return fileSize; }
    public void setFileSize(long fileSize) { this.fileSize = fileSize; }

    public String getDocumentCategory() { return documentCategory; }
    public void setDocumentCategory(String documentCategory) { this.documentCategory = documentCategory; }

    public Timestamp getUploadedAt() { return uploadedAt; }
    public void setUploadedAt(Timestamp uploadedAt) { this.uploadedAt = uploadedAt; }

    /**
     * Return human-friendly formatted file size in KB.
     */
    public String getFormattedSize() {
        return String.format("%.1f KB", fileSize / 1024.0);
    }
}
