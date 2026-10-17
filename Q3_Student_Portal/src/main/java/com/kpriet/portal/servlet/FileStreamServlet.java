package com.kpriet.portal.servlet;

import com.kpriet.portal.dao.StudentDAO;
import com.kpriet.portal.model.StudentFile;

import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.net.URLEncoder;
import java.nio.charset.StandardCharsets;

/**
 * File streaming servlet for authorized student access to profile photos and documents.
 * Protects documents from unauthorized access and enforces session validation.
 */
@WebServlet(name = "FileStreamServlet", urlPatterns = {"/file"})
public class FileStreamServlet extends HttpServlet {

    private final StudentDAO studentDAO = new StudentDAO();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        HttpSession session = req.getSession(false);
        if (session == null || session.getAttribute("studentId") == null) {
            resp.sendError(HttpServletResponse.SC_UNAUTHORIZED, "Please log in to access this file.");
            return;
        }

        int loggedInStudentId = (Integer) session.getAttribute("studentId");
        String fileIdParam = req.getParameter("id");
        if (fileIdParam == null) {
            resp.sendError(HttpServletResponse.SC_BAD_REQUEST, "File ID missing.");
            return;
        }

        int fileId;
        try {
            fileId = Integer.parseInt(fileIdParam);
        } catch (NumberFormatException e) {
            resp.sendError(HttpServletResponse.SC_BAD_REQUEST, "Invalid file ID.");
            return;
        }

        StudentFile fileRecord = studentDAO.getStudentFileById(fileId);
        if (fileRecord == null) {
            resp.sendError(HttpServletResponse.SC_NOT_FOUND, "Requested file record not found.");
            return;
        }

        // Strict authorization check: file must belong to currently logged in student
        if (fileRecord.getStudentId() != loggedInStudentId) {
            resp.sendError(HttpServletResponse.SC_FORBIDDEN, "Access denied. You can only view your own documents.");
            return;
        }

        // Locate physical file on server
        // Uploads are stored in Q2_College_Admin/uploads/
        String appBase = getServletContext().getRealPath("/");
        File targetFile = findUploadFile(appBase, fileRecord.getRelativePath());

        if (targetFile == null || !targetFile.exists() || !targetFile.isFile()) {
            resp.sendError(HttpServletResponse.SC_NOT_FOUND, "Physical file not found on disk.");
            return;
        }

        // Set response headers
        String mime = fileRecord.getMimeType();
        if (mime == null || mime.isEmpty()) {
            mime = getServletContext().getMimeType(targetFile.getName());
            if (mime == null) mime = "application/octet-stream";
        }

        boolean isDownload = "1".equals(req.getParameter("download"));
        String disposition = (isDownload || !mime.startsWith("image/") && !mime.equals("application/pdf"))
                ? "attachment"
                : "inline";

        String encodedName = URLEncoder.encode(fileRecord.getOriginalFilename(), StandardCharsets.UTF_8.name())
                .replace("+", "%20");

        resp.setContentType(mime);
        resp.setContentLengthLong(targetFile.length());
        resp.setHeader("Content-Disposition", disposition + "; filename=\"" + encodedName + "\"");
        resp.setHeader("Cache-Control", "private, max-age=3600");

        // Stream file content
        try (FileInputStream in = new FileInputStream(targetFile);
             OutputStream out = resp.getOutputStream()) {
            byte[] buffer = new byte[8192];
            int bytesRead;
            while ((bytesRead = in.read(buffer)) != -1) {
                out.write(buffer, 0, bytesRead);
            }
            out.flush();
        }
    }

    /**
     * Resolves the upload file path checking common project root locations.
     */
    private File findUploadFile(String appBase, String relativePath) {
        String cleanRel = relativePath.replace('\\', '/');

        // Check relative to webapp directory or adjacent Q2_College_Admin directory
        File[] candidateBases = {
            new File(appBase, "../../../Q2_College_Admin/uploads/" + cleanRel),
            new File(appBase, "../../Q2_College_Admin/uploads/" + cleanRel),
            new File(appBase, "../Q2_College_Admin/uploads/" + cleanRel),
            new File("c:/Users/Karunuesh/OneDrive - Amrita vishwa vidyapeetham/Lilesh/SEM 5/Assignment_2_WebTechnology/Q2_College_Admin/uploads/" + cleanRel),
            new File(System.getProperty("user.dir") + "/Q2_College_Admin/uploads/" + cleanRel)
        };

        for (File candidate : candidateBases) {
            try {
                if (candidate.exists() && candidate.isFile()) {
                    return candidate;
                }
            } catch (Exception ignored) {}
        }

        return candidateBases[3]; // Default fallback
    }
}
