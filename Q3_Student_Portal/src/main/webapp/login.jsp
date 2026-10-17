<%@ page contentType="text/html;charset=UTF-8" language="java" %>
<%
    String error = (String) request.getAttribute("errorMessage");
    if (error == null && "1".equals(request.getParameter("logout"))) {
        request.setAttribute("infoMessage", "You have been successfully logged out.");
    }
    String info = (String) request.getAttribute("infoMessage");
%>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Portal Login &mdash; KPRIET</title>
  <link rel="icon" type="image/png" href="../../../../kprietLogo.png">
  <link rel="stylesheet" href="../../../../assets/css/shared.css">
  <link rel="stylesheet" href="../../../../style.css">
  <style>
    .student-login-box {
      background: white;
      border: 1px solid var(--color-border);
      border-radius: var(--radius-lg);
      padding: 2.5rem;
      max-width: 440px;
      margin: 3.5rem auto;
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.06), 0 8px 10px -6px rgba(0,0,0,0.04);
    }
    .student-badge-pill {
      display: inline-block;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #1e40af;
      background: #dbeafe;
      padding: 0.25rem 0.65rem;
      border-radius: 9999px;
      margin-bottom: 0.75rem;
    }
  </style>
</head>
<body class="login-body">

  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <a href="../../../../index.html" style="text-decoration:none;display:inline-block">
          <img src="../../../../kprietLogo.png" alt="KPRIET Logo" class="login-logo" style="width:56px;height:56px;object-fit:contain">
        </a>
        <div style="margin-top:0.5rem">
          <span class="student-badge-pill">Q3 &bull; Student Profile Portal</span>
        </div>
        <h1 style="font-size:1.5rem;font-weight:700;color:var(--color-text-title);margin-top:0.25rem">Student Portal Login</h1>
        <p style="font-size:0.875rem;color:var(--color-text-muted)">KPR Institute of Engineering and Technology</p>
      </div>

      <% if (error != null) { %>
        <div class="alert alert--error" role="alert" style="margin-bottom:1.25rem">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <div><%= error %></div>
        </div>
      <% } %>

      <% if (info != null) { %>
        <div class="flash flash--success" style="margin-bottom:1.25rem;border-radius:var(--radius-sm)">
          <div style="display:flex;align-items:center;gap:0.5rem">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span><%= info %></span>
          </div>
        </div>
      <% } %>

      <form method="post" action="login" novalidate>
        <div class="form-group">
          <label for="identifier">Roll Number or Email <span class="required">*</span></label>
          <input type="text" id="identifier" name="identifier" class="form-control"
                 placeholder="e.g. 21CS001 or student@example.com"
                 autocomplete="username" required autofocus>
          <small style="color:var(--color-text-muted);font-size:0.75rem">Demo accounts from seed data: <code>21CS001</code> / <code>21EC002</code> / <code>21ME003</code></small>
        </div>

        <div class="form-group" style="margin-top:1rem">
          <label for="password">Portal Password <span class="required">*</span></label>
          <div class="input-with-toggle">
            <input type="password" id="password" name="password" class="form-control"
                   placeholder="Enter portal password"
                   autocomplete="current-password" required>
            <button type="button" class="toggle-password" id="togglePassword" aria-label="Show password">
              <svg class="icon-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="icon-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
          <small style="color:var(--color-text-muted);font-size:0.75rem">Default demo password: <code>Student@123</code></small>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;margin-top:1.25rem" id="studentLoginBtn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
          Sign In to Portal
        </button>

        <div style="margin-top:1.5rem;text-align:center;font-size:0.85rem;color:var(--color-text-muted)">
          <a href="../../../../index.html" style="color:var(--color-primary);text-decoration:none">
            &larr; Back to KPRIET Main Portal
          </a>
        </div>
      </form>
    </div>
  </div>

  <script>
    const toggleBtn = document.getElementById('togglePassword');
    const pwdInput = document.getElementById('password');
    if (toggleBtn && pwdInput) {
      toggleBtn.addEventListener('click', () => {
        const isPassword = pwdInput.type === 'password';
        pwdInput.type = isPassword ? 'text' : 'password';
        const eye = toggleBtn.querySelector('.icon-eye');
        const eyeOff = toggleBtn.querySelector('.icon-eye-off');
        if (eye && eyeOff) {
          eye.style.display = isPassword ? 'none' : 'block';
          eyeOff.style.display = isPassword ? 'block' : 'none';
        }
      });
    }
  </script>
</body>
</html>
