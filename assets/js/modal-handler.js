/**
 * Generic Modal Handler
 * Handles opening/closing of modal dialogs triggered by .search-mobile-trigger buttons
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get all mobile trigger buttons
    const modalTriggers = document.querySelectorAll('.search-mobile-trigger');
    
    modalTriggers.forEach(trigger => {
        const modalId = trigger.getAttribute('aria-controls');
        if (!modalId) return;
        
        const modal = document.getElementById(modalId);
        if (!modal) return;
        
        const closeBtn = modal.querySelector('.vendors-filter-close, .modal-close, [data-modal-close]');
        
        // Open modal
        trigger.addEventListener('click', function() {
            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        });
        
        // Close modal via close button
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modal.classList.remove('active');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            });
        }
        
        // Close modal on backdrop click
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('active');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
        });
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                modal.classList.remove('active');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
        });
    });
});
