// Common mobile navigation toggle
(function() {
  document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.getElementById('mobileToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    if (mobileToggle && mobileMenu) {
      mobileToggle.addEventListener('click', function() {
        mobileMenu.classList.toggle('active');
      });
      mobileMenu.querySelectorAll('a').forEach(function(link) {
        link.addEventListener('click', function() {
          mobileMenu.classList.remove('active');
        });
      });
    }
  });
})();
