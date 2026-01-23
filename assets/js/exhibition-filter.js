/**
 * Exhibition filter and load-more logic for cities page.
 */

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchExhibition');
    const citySelect = document.getElementById('citySelect');
    const statusSelect = document.getElementById('statusSelect');
    const resetButton = document.querySelector('.search-field.reset-wrapper button');
    const exhibitionCount = document.getElementById('exhibitionCount');
    const loadMoreBtn = document.getElementById('loadMoreExhibitions');
    const grid = document.querySelector('.exhibition-list-grid');

    if (!grid) return;

    const cards = Array.from(grid.querySelectorAll('.event-card'));
    const totalExhibitions = cards.length;
    const PAGE_SIZE = 8;
    let visibleLimit = PAGE_SIZE;

    function cardStatus(card) {
        const badge = card.querySelector('.event-badge');
        if (!badge) return '';
        if (badge.classList.contains('badge-upcoming') || badge.classList.contains('event-badge-upcoming')) return 'upcoming';
        if (badge.classList.contains('badge-ongoing') || badge.classList.contains('event-badge-ongoing')) return 'ongoing';
        if (badge.classList.contains('badge-featured') || badge.classList.contains('event-badge-featured')) return 'featured';
        if (badge.classList.contains('badge-filling-fast')) return 'filling-fast';
        if (badge.classList.contains('badge-soldout')) return 'soldout';
        return '';
    }

    function applyFilters() {
        const searchTerm = (searchInput?.value || '').toLowerCase().trim();
        const selectedCity = (citySelect?.value || '').toLowerCase().trim();
        const selectedStatus = (statusSelect?.value || '').toLowerCase().trim();

        const matched = [];

        cards.forEach(card => {
            let show = true;

            if (searchTerm) {
                const name = card.querySelector('.event-name')?.textContent.toLowerCase() || '';
                if (!name.includes(searchTerm)) show = false;
            }

            if (show && selectedCity) {
                const cityAttr = (card.dataset.city || '').toLowerCase();
                const venue = card.querySelector('.event-venue')?.textContent.toLowerCase() || '';
                if (!(cityAttr.includes(selectedCity) || venue.includes(selectedCity))) show = false;
            }

            if (show && selectedStatus) {
                const status = cardStatus(card);
                if (status !== selectedStatus) show = false;
            }

            if (show) matched.push(card);
        });

        // Reset visible limit on new filter to show first page of results
        visibleLimit = Math.max(PAGE_SIZE, matched.length ? PAGE_SIZE : 0);

        cards.forEach(card => {
            card.style.display = 'none';
        });

        let visibleCount = 0;
        matched.forEach((card, idx) => {
            if (idx < visibleLimit) {
                card.style.display = 'flex';
                visibleCount += 1;
            }
        });

        updateCount(visibleCount, matched.length);
        toggleLoadMore(matched.length > visibleLimit, matched.length);
    }

    function loadMore() {
        const filteredCards = cards.filter(card => card.style.display !== 'none' || card.style.display === 'none'); // placeholder to reuse logic
        // We need to re-filter to know current matches
        const searchTerm = (searchInput?.value || '').toLowerCase().trim();
        const selectedCity = (citySelect?.value || '').toLowerCase().trim();
        const selectedStatus = (statusSelect?.value || '').toLowerCase().trim();

        const matched = cards.filter(card => {
            let show = true;
            if (searchTerm) {
                const name = card.querySelector('.event-name')?.textContent.toLowerCase() || '';
                if (!name.includes(searchTerm)) show = false;
            }
            if (show && selectedCity) {
                const cityAttr = (card.dataset.city || '').toLowerCase();
                const venue = card.querySelector('.event-venue')?.textContent.toLowerCase() || '';
                if (!(cityAttr.includes(selectedCity) || venue.includes(selectedCity))) show = false;
            }
            if (show && selectedStatus) {
                const status = cardStatus(card);
                if (status !== selectedStatus) show = false;
            }
            return show;
        });

        visibleLimit += PAGE_SIZE;

        let visibleCount = 0;
        cards.forEach(card => {
            card.style.display = 'none';
        });

        matched.forEach((card, idx) => {
            if (idx < visibleLimit) {
                card.style.display = 'flex';
                visibleCount += 1;
            }
        });

        updateCount(visibleCount, matched.length);
        toggleLoadMore(matched.length > visibleLimit, matched.length);
    }

    function toggleLoadMore(show, totalMatched) {
        if (!loadMoreBtn) return;
        if (totalMatched === 0) {
            loadMoreBtn.style.display = 'none';
            return;
        }
        loadMoreBtn.style.display = show ? 'inline-flex' : 'none';
    }

    function updateCount(visible, total) {
        if (exhibitionCount) {
            exhibitionCount.textContent = `Showing ${visible} of ${total || totalExhibitions} Exhibitions`;
        }
    }

    // Event bindings
    searchInput?.addEventListener('input', applyFilters);
    citySelect?.addEventListener('change', applyFilters);
    statusSelect?.addEventListener('change', applyFilters);

    resetButton?.addEventListener('click', function (e) {
        e.preventDefault();
        if (searchInput) searchInput.value = '';
        if (citySelect) citySelect.selectedIndex = 0;
        if (statusSelect) statusSelect.selectedIndex = 0;
        visibleLimit = PAGE_SIZE;
        applyFilters();
    });

    loadMoreBtn?.addEventListener('click', function (e) {
        e.preventDefault();
        loadMore();
    });

    // Initialize
    applyFilters();
});
