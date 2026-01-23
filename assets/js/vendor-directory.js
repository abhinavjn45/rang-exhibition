/**
 * Vendor Directory - Search/Filter Handler
 * Handles vendor search and filtering functionality
 * Note: Modal open/close is handled by modal-handler.js
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get modal elements
    const modal = document.getElementById('vendorsFilterModal');
    const modalTrigger = document.querySelector('.search-mobile-trigger');
    const applyBtn = document.querySelector('.btn-filter-apply');
    const applyMobileBtn = document.getElementById('applyMobileFilters');
    const clearBtn = document.querySelector('.btn-filter-clear');
    const clearMobileBtn = document.getElementById('clearFilters');

    // Get filter elements
    const searchInput = document.getElementById('searchInput');
    const cityFilter = document.getElementById('cityFilter');
    const categoryFilter = document.getElementById('categoryFilter');
    const applyFiltersBtn = document.getElementById('applyFilters');

    // Mobile filter elements
    const modalSearchInput = document.getElementById('modalVendorSearch');
    const modalCityFilter = document.getElementById('modalCityFilter');
    const modalCategoryFilter = document.getElementById('modalCategoryFilter');

    if (!modal || !modalTrigger) return;

    /**
     * Sync desktop values to mobile modal before opening
     */
    modalTrigger.addEventListener('click', function() {
        if (searchInput && modalSearchInput) {
            modalSearchInput.value = searchInput.value;
        }
        if (cityFilter && modalCityFilter) {
            modalCityFilter.value = cityFilter.value;
        }
        if (categoryFilter && modalCategoryFilter) {
            modalCategoryFilter.value = categoryFilter.value;
        }
    });

    /**
     * Apply Filters - Redirect with query parameters
     */
    function applyFilters(isMobile = false) {
        const params = new URLSearchParams();
        
        // Get values from appropriate inputs (desktop or mobile)
        const search = isMobile ? modalSearchInput?.value.trim() : searchInput?.value.trim();
        const city = isMobile ? modalCityFilter?.value : cityFilter?.value;
        const category = isMobile ? modalCategoryFilter?.value : categoryFilter?.value;

        // Build query string
        if (search) params.set('search', search);
        if (city) params.set('city', city);
        if (category) params.set('category', category);

        // Reset to page 1 when applying new filters
        params.set('page', '1');

        // Redirect with filters
        const queryString = params.toString();
        window.location.href = window.location.pathname + (queryString ? '?' + queryString : '');
    }

    /**
     * Clear Filters
     */
    function clearFilters(isMobile = false) {
        if (isMobile) {
            if (modalSearchInput) modalSearchInput.value = '';
            if (modalCityFilter) modalCityFilter.value = '';
            if (modalCategoryFilter) modalCategoryFilter.value = '';
        } else {
            if (searchInput) searchInput.value = '';
            if (cityFilter) cityFilter.value = '';
            if (categoryFilter) categoryFilter.value = '';
        }
        // Redirect to clean URL (no filters)
        window.location.href = window.location.pathname;
    }

    /**
     * Event Listeners
     */

    // Desktop apply button
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            applyFilters(false);
        });
    }

    // Mobile apply button
    if (applyMobileBtn) {
        applyMobileBtn.addEventListener('click', function() {
            applyFilters(true);
            // Close modal (handled by modal-handler.js event)
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
        });
    }

    // Desktop clear button (add if needed in future)
    // Mobile clear button
    if (clearMobileBtn) {
        clearMobileBtn.addEventListener('click', function() {
            clearFilters(true);
        });
    }

    // Allow Enter key to trigger search from desktop input
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters(false);
            }
        });
    }

    // Allow Enter key to trigger search from mobile input
    if (modalSearchInput) {
        modalSearchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters(true);
                closeModal();
            }
        });
    }
});
