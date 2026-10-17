  </main><!-- /.admin-main -->

  <!-- ADMIN FOOTER -->
  <footer class="admin-footer" role="contentinfo">
    <div class="container">
      <span>KPR Institute of Engineering and Technology &mdash; Admin Panel</span>
      <span>College Administration and Information System</span>
    </div>
  </footer>

  <script>
    /* Mobile nav toggle */
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
