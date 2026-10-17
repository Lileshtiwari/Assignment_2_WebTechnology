package com.kpriet.portal.servlet;

import com.kpriet.portal.dao.StudentDAO;
import com.kpriet.portal.model.Student;
import com.kpriet.portal.util.PasswordUtil;

import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import javax.servlet.http.HttpSession;
import java.io.IOException;

/**
 * Handles student authentication and session establishment.
 */
@WebServlet(name = "LoginServlet", urlPatterns = {"/login"})
public class LoginServlet extends HttpServlet {

    private final StudentDAO studentDAO = new StudentDAO();

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        HttpSession session = req.getSession(false);
        if (session != null && session.getAttribute("studentId") != null) {
            resp.sendRedirect(req.getContextPath() + "/profile");
            return;
        }
        req.getRequestDispatcher("/login.jsp").forward(req, resp);
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp)
            throws ServletException, IOException {
        String identifier = req.getParameter("identifier");
        String password = req.getParameter("password");

        if (identifier == null || identifier.trim().isEmpty() || password == null || password.trim().isEmpty()) {
            req.setAttribute("errorMessage", "Please provide both Roll Number / Email and Password.");
            req.getRequestDispatcher("/login.jsp").forward(req, resp);
            return;
        }

        Student student = studentDAO.findByRollOrEmail(identifier.trim());

        if (student == null) {
            req.setAttribute("errorMessage", "No student account found with the provided Roll Number or Email.");
            req.getRequestDispatcher("/login.jsp").forward(req, resp);
            return;
        }

        if (student.getPortalPasswordHash() == null || student.getPortalPasswordHash().isEmpty()) {
            req.setAttribute("errorMessage", "Portal access has not been activated for your account. Please contact college admin.");
            req.getRequestDispatcher("/login.jsp").forward(req, resp);
            return;
        }

        boolean valid = PasswordUtil.checkPassword(password, student.getPortalPasswordHash());
        if (!valid) {
            req.setAttribute("errorMessage", "Invalid password. Please check your credentials.");
            req.getRequestDispatcher("/login.jsp").forward(req, resp);
            return;
        }

        // Authentication successful: establish session
        HttpSession session = req.getSession(true);
        session.setAttribute("studentId", student.getId());
        session.setAttribute("studentName", student.getFullName());
        session.setAttribute("rollNumber", student.getRollNumber());
        session.setAttribute("studentEmail", student.getEmail());

        resp.sendRedirect(req.getContextPath() + "/profile");
    }
}
