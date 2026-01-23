// Exhibition page venue upload modal functionality
(function() {
  document.addEventListener('DOMContentLoaded', function() {
    const uploadModal = document.getElementById('uploadModal');
    const openUploadModalBtn = document.getElementById('openUploadModal');
    const openUploadModalBtn1 = document.getElementById('openUploadModal1');
    const closeUploadModalBtn = document.getElementById('closeUploadModal');
    const uploadModalOverlay = document.getElementById('uploadModalOverlay');

    function openUploadModal(e) {
      if (e) e.preventDefault();
      if (!uploadModal) return;
      uploadModal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    function closeUploadModal() {
      if (!uploadModal) return;
      uploadModal.classList.remove('active');
      document.body.style.overflow = '';
    }

    if (openUploadModalBtn) openUploadModalBtn.addEventListener('click', openUploadModal);
    if (openUploadModalBtn1) openUploadModalBtn1.addEventListener('click', openUploadModal);
    if (closeUploadModalBtn) closeUploadModalBtn.addEventListener('click', closeUploadModal);
    if (uploadModalOverlay) uploadModalOverlay.addEventListener('click', closeUploadModal);

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && uploadModal && uploadModal.classList.contains('active')) {
        closeUploadModal();
      }
    });
  });
})();
