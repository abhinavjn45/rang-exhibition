// Venue Location Modal behavior
(function() {
  document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('venueMapModal');
    const closeBtn = document.getElementById('closeVenueModal');
    const overlay = modal ? modal.querySelector('.venue-modal-overlay') : null;
    const openBtn = document.getElementById('openVenueMapBtn');

    if (!modal) return;

    function openModal(e) {
      if (e) e.preventDefault();
      modal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      modal.classList.remove('active');
      document.body.style.overflow = '';
    }

    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (overlay) overlay.addEventListener('click', closeModal);

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && modal.classList.contains('active')) {
        closeModal();
      }
    });
  });
})();
