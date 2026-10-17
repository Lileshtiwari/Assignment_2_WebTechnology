package com.kpriet.portal.servlet;

import com.kpriet.portal.dao.StudentDAO;
import com.kpriet.portal.model.Student;
import com.kpriet.portal.model.StudentFile;

import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import java.io.IOException;
import java.util.List;

/**
 * Controller servlet for viewing authenticated student profile and uploaded documents.
 */
@WebServlet(name = "ProfileServlet", urlPatterns = {"/profile"})
public class ProfileServlet extends HttpServlet {

    private final StudentDAO studentDAO = new StudentDAO();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        HttpSession session = req.getSession(false);
        if (session == null || session.getAttribute("studentId") == null) {
            resp.sendRedirect(req.getContextPath() + "/login.jsp");
            return;
        }

        int studentId = (Integer) session.getAttribute("studentId");
        Student student = studentDAO.findById(studentId);

        if (student == null) {
            session.invalidate();
            resp.sendRedirect(req.getContextPath() + "/login.jsp?error=notfound");
            return;
        }

        List<StudentFile> files = studentDAO.getStudentFiles(studentId);
        StudentFile profilePhoto = null;
        for (StudentFile f : files) {
            if ("profile_photo".equalsIgnoreCase(f.getDocumentCategory())) {
                profilePhoto = f;
                break;
            }
        }

        req.setAttribute("student", student);
        req.setAttribute("files", files);
        req.setAttribute("profilePhoto", profilePhoto);

        req.getRequestDispatcher("/profile.jsp").forward(req, resp);
    }
}
