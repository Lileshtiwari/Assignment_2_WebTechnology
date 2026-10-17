<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<%@ page import="com.kpriet.portal.model.Student" %>
<%@ page import="com.kpriet.portal.model.StudentFile" %>
<%@ page import="java.util.List" %>
<%@ page import="java.text.SimpleDateFormat" %>
<%
    Student student = (Student) request.getAttribute("student");
    if (student == null) {
        response.sendRedirect(request.getContextPath() + "/login");
        return;
    }
    List<StudentFile> files = (List<StudentFile>) request.getAttribute("files");
    StudentFile profilePhoto = (StudentFile) request.getAttribute("profilePhoto");

    SimpleDateFormat dateFormat = new SimpleDateFormat("dd MMM yyyy");
    SimpleDateFormat timeFormat = new SimpleDateFormat("dd MMM yyyy, hh:mm a");
%>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><%= student.getFullName() %> &mdash; Student Profile | KPRIET</title>
  <link rel="icon" type="image/png" href="../../../../kprietLogo.png">
  <link rel="stylesheet" href="../../../../assets/css/shared.css">
  <link rel="stylesheet" href="../../../../style.css">
  <style>
    .student-badge-pill {
      display: inline-block;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #1e40af;
      background: #dbeafe;
      padding: 0.2rem 0.6rem;
      border-radius: 9999px;
      margin-left: 0.5rem;
    }
  </style>
</head>
<body class="admin-body">

  <!-- TOP HEADER -->
  <header class="site-header admin-header" role="banner">
    <div class="nav-inner">
      <a href="../../../../index.html" class="nav-logo" aria-label="KPRIET Home">
        <img src="../../../../kprietLogo.png" alt="KPR Institute Logo" width="40" height="40">
        <div class="nav-logo-text">
          <span class="inst-name">KPRIET</span>
          <span class="inst-sub">Student Portal</span>
        </div>
      </a>
      <nav class="nav-links" id="navLinks" role="navigation" aria-label="Student portal navigation">
        <a href="../../../../index.html">Main Site</a>
        <a href="profile" class="active">My Profile</a>
        <a href="#documents">My Documents</a>
      </nav>
      <div class="nav-actions">
        <span class="admin-name" style="color:var(--color-text);font-weight:600">
          <%= student.getFullName() %> (<%= student.getRollNumber() %>)
        </span>
        <a href="logout" class="btn btn-secondary btn-sm" id="logoutBtn">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Sign Out
        </a>
      </div>
      <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <main class="admin-main" role="main">
    <div class="container admin-container">

      <div class="page-header">
        <div>
          <div style="display:flex;align-items:center;gap:0.5rem">
            <h1><%= student.getFullName() %></h1>
            <span class="student-badge-pill">Q3 Java / JDBC</span>
          </div>
          <p class="page-subtitle">
            Roll Number: <strong><%= student.getRollNumber() %></strong> &bull;
            Department: <strong><%= student.getCourse() %></strong>
          </p>
        </div>
        <div style="display:flex;gap:0.75rem;align-items:center">
          <span class="badge badge-status badge-status--<%= student.getAdmissionStatus() %> badge-lg">
            Status: <%= student.getAdmissionStatus().toUpperCase() %>
          </span>
        </div>
      </div>

      <div class="view-grid">
        <!-- SIDEBAR -->
        <div class="view-sidebar">
          <div class="admin-card profile-card">
            <% if (profilePhoto != null) { %>
              <img src="file?id=<%= profilePhoto.getId() %>"
                   alt="<%= student.getFullName() %>"
                   class="profile-photo" id="profilePhotoImg">
            <% } else { %>
              <div class="profile-photo-placeholder" aria-label="No photo uploaded">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span>No Photo</span>
              </div>
            <% } %>

            <h3><%= student.getFullName() %></h3>
            <code><%= student.getRollNumber() %></code>
            <span class="badge badge-status badge-status--<%= student.getAdmissionStatus() %>" style="margin-top:0.5rem">
              <%= student.getAdmissionStatus().toUpperCase() %>
            </span>

            <div class="profile-meta" style="margin-top:1.25rem;text-align:left;font-size:0.875rem">
              <div style="margin-bottom:0.4rem"><strong>Course:</strong> <%= student.getCourse() %></div>
              <div style="margin-bottom:0.4rem"><strong>Email:</strong> <%= student.getEmail() %></div>
              <div style="margin-bottom:0.4rem"><strong>Phone:</strong> <%= student.getPhone() %></div>
              <div><strong>Category:</strong> <%= student.getAdmissionCategory() != null ? student.getAdmissionCategory().toUpperCase() : "GENERAL" %></div>
            </div>
          </div>
        </div>

        <!-- MAIN DETAILS -->
        <div class="view-main">
          <!-- PERSONAL -->
          <div class="admin-card">
            <div class="detail-section-heading">Personal Information</div>
            <dl class="detail-grid">
              <dt>Full Name</dt>       <dd><%= student.getFullName() %></dd>
              <dt>Date of Birth</dt>   <dd><%= student.getDateOfBirth() != null ? dateFormat.format(student.getDateOfBirth()) : "&mdash;" %></dd>
              <dt>Gender</dt>          <dd><%= student.getGender() != null ? student.getGender().toUpperCase() : "&mdash;" %></dd>
              <dt>Nationality</dt>     <dd><%= student.getNationality() != null ? student.getNationality() : "Indian" %></dd>
              <dt>Address</dt>         <dd><%= student.getAddress() != null ? student.getAddress() : "&mdash;" %></dd>
              <dt>City / District</dt> <dd><%= student.getCity() != null ? student.getCity() : "&mdash;" %></dd>
              <dt>PIN Code</dt>        <dd><%= student.getPincode() != null ? student.getPincode() : "&mdash;" %></dd>
              <dt>Alternate Phone</dt> <dd><%= student.getAltPhone() != null ? student.getAltPhone() : "&mdash;" %></dd>
            </dl>
          </div>

          <!-- ACADEMIC -->
          <div class="admin-card" style="margin-top:1.25rem">
            <div class="detail-section-heading">Academic Qualifications</div>
            <dl class="detail-grid">
              <dt>SSLC School</dt>     <dd><%= student.getSslcSchool() != null ? student.getSslcSchool() : "&mdash;" %></dd>
              <dt>SSLC Year</dt>       <dd><%= student.getSslcYear() != null ? student.getSslcYear() : "&mdash;" %></dd>
              <dt>SSLC Marks</dt>      <dd><%= student.getSslcPercentage() != null ? student.getSslcPercentage() + "%" : "&mdash;" %></dd>
              <dt>HSC School</dt>      <dd><%= student.getHscSchool() != null ? student.getHscSchool() : "&mdash;" %></dd>
              <dt>HSC Year</dt>        <dd><%= student.getHscYear() != null ? student.getHscYear() : "&mdash;" %></dd>
              <dt>HSC Marks</dt>       <dd><%= student.getHscPercentage() != null ? student.getHscPercentage() + "%" : "&mdash;" %></dd>
              <dt>HSC Stream</dt>      <dd><%= student.getHscStream() != null ? student.getHscStream().toUpperCase() : "&mdash;" %></dd>
              <dt>Maths Marks</dt>     <dd><%= student.getMathsPercentage() != null ? student.getMathsPercentage() + "%" : "&mdash;" %></dd>
            </dl>
          </div>

          <!-- COURSE -->
          <div class="admin-card" style="margin-top:1.25rem">
            <div class="detail-section-heading">Admission and Enrolment Details</div>
            <dl class="detail-grid">
              <dt>Selected Programme</dt>    <dd><%= student.getCourse() %></dd>
              <dt>Alternative Choice</dt>    <dd><%= student.getAlternativeCourse() != null ? student.getAlternativeCourse() : "&mdash;" %></dd>
              <dt>Admission Category</dt>    <dd><%= student.getAdmissionCategory() != null ? student.getAdmissionCategory().toUpperCase() : "GENERAL" %></dd>
              <dt>Registration Date</dt>     <dd><%= student.getCreatedAt() != null ? timeFormat.format(student.getCreatedAt()) : "&mdash;" %></dd>
              <dt>Last Updated</dt>          <dd><%= student.getUpdatedAt() != null ? timeFormat.format(student.getUpdatedAt()) : "&mdash;" %></dd>
            </dl>
          </div>

          <!-- DOCUMENTS -->
          <div class="admin-card" style="margin-top:1.25rem" id="documents">
            <div class="admin-card__header">
              <div class="detail-section-heading" style="margin:0">Official Uploaded Documents</div>
              <span style="font-size:0.85rem;color:var(--color-text-muted)">Securely retrieved via Java Servlet File Streaming</span>
            </div>

            <% if (files == null || files.isEmpty()) { %>
              <div class="empty-state" style="padding:2rem 0">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>
                <p>No documents uploaded yet for this account.</p>
              </div>
            <% } else { %>
              <div class="file-list">
                <% for (StudentFile f : files) { %>
                  <div class="file-item">
                    <div class="file-item__icon">
                      <% if (f.getMimeType() != null && f.getMimeType().startsWith("image/")) { %>
                        <img src="file?id=<%= f.getId() %>" alt="<%= f.getOriginalFilename() %>" class="file-thumb">
                      <% } else { %>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                      <% } %>
                    </div>
                    <div class="file-item__info">
                      <span class="file-item__name"><%= f.getOriginalFilename() %></span>
                      <span class="file-item__meta">
                        <strong><%= f.getDocumentCategory().replace("_", " ").toUpperCase() %></strong> &bull;
                        <%= f.getFormattedSize() %> &bull;
                        Uploaded <%= f.getUploadedAt() != null ? dateFormat.format(f.getUploadedAt()) : "" %>
                      </span>
                    </div>
                    <div class="file-item__actions">
                      <a href="file?id=<%= f.getId() %>" class="btn btn-outline-primary btn-sm"
                         target="_blank" rel="noopener noreferrer" title="View Document">
                        View
                      </a>
                      <a href="file?id=<%= f.getId() %>&download=1" class="btn btn-primary btn-sm" title="Download Document">
                        Download
                      </a>
                    </div>
                  </div>
                <% } %>
              </div>
            <% } %>
          </div>

        </div>
      </div>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="admin-footer" role="contentinfo">
    <div class="container">
      <span>KPR Institute of Engineering and Technology &mdash; Student Profile Portal</span>
      <span>U21CS501 Web Technology Assignment II &mdash; Q3: Java Servlets / JSP / JDBC</span>
    </div>
  </footer>

  <script>
    const toggle = document.getElementById('navToggle');
    const links  = document.getElementById('navLinks');
    if (toggle && links) {
      toggle.addEventListener('click', () => {
        const open = links.classList.toggle('open');
        toggle.setAttribute('aria-expanded', open);
      });
    }
  </script>
</body>
</html>
