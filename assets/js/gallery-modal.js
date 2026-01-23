/**
 * Gallery Modal Viewer
 * Displays full-screen gallery with navigation
 */

class GalleryModalViewer {
    constructor() {
        this.currentIndex = 0;
        this.galleryItems = [];
        this.imageBaseDir = '';
        this.init();
    }

    init() {
        // Get all gallery items
        this.galleryItems = Array.from(document.querySelectorAll('[data-gallery-index]'));
        
        if (this.galleryItems.length === 0) return;

        // Derive base image directory from the first item's image src
        const firstImg = this.galleryItems[0].querySelector('.gallery-img');
        if (firstImg) {
            const src = firstImg.src;
            // e.g., https://.../assets/uploads/images/gallery/IMG_123.jpg -> keep up to last '/'
            const lastSlash = src.lastIndexOf('/');
            this.imageBaseDir = lastSlash !== -1 ? src.substring(0, lastSlash + 1) : '';
        }

        // Create modal HTML
        this.createModal();

        // Add click handlers to gallery items
        this.galleryItems.forEach((item, index) => {
            const zoomBtn = item.querySelector('.gallery-zoom');
            if (zoomBtn) {
                zoomBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.openModal(index);
                });
            }
            // Also allow clicking the image directly
            const img = item.querySelector('.gallery-img');
            if (img) {
                img.addEventListener('click', () => {
                    this.openModal(index);
                });
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            const modal = document.getElementById('galleryModalViewer');
            if (!modal || !modal.classList.contains('active')) return;

            if (e.key === 'ArrowLeft') this.prevImage();
            if (e.key === 'ArrowRight') this.nextImage();
            if (e.key === 'Escape') this.closeModal();
        });
    }

    createModal() {
        const modal = document.createElement('div');
        modal.id = 'galleryModalViewer';
        modal.className = 'gallery-modal-viewer';
        modal.innerHTML = `
            <div class="gallery-modal-content">
                <div class="gallery-modal-header">
                    <h3 class="gallery-modal-title" id="galleryTitle">Image Title</h3>
                    <button class="gallery-modal-close" id="galleryModalClose" aria-label="Close gallery">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="gallery-modal-image-container">
                    <img id="galleryModalImage" class="gallery-modal-image" src="" alt="Gallery Image">
                    <button class="gallery-modal-nav gallery-modal-prev" id="galleryPrev" aria-label="Previous image">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="gallery-modal-nav gallery-modal-next" id="galleryNext" aria-label="Next image">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <div class="gallery-modal-thumbnails" id="galleryThumbnails"></div>

                <div class="gallery-modal-footer">
                    <span class="gallery-modal-counter"><span id="galleryCounter">1</span> / <span id="galleryTotal">1</span></span>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Event listeners
        document.getElementById('galleryModalClose').addEventListener('click', () => this.closeModal());
        document.getElementById('galleryPrev').addEventListener('click', () => this.prevImage());
        document.getElementById('galleryNext').addEventListener('click', () => this.nextImage());
        modal.addEventListener('click', (e) => {
            if (e.target === modal) this.closeModal();
        });

        // Create thumbnails
        this.createThumbnails();
        document.getElementById('galleryTotal').textContent = this.galleryItems.length;
    }

    createThumbnails() {
        const container = document.getElementById('galleryThumbnails');
        container.innerHTML = '';

        this.galleryItems.forEach((item, index) => {
            const imageName = item.dataset.imageName;
            const imageTitle = item.dataset.imageTitle;

            const thumb = document.createElement('button');
            thumb.type = 'button';
            thumb.className = 'gallery-modal-thumbnail';
            thumb.dataset.index = index;
            const thumbSrc = `${this.imageBaseDir}${imageName}`;
            thumb.innerHTML = `
                <img src="${thumbSrc}" alt="${imageTitle}" loading="lazy">
            `;

            thumb.addEventListener('click', () => this.openModal(index));
            container.appendChild(thumb);
        });
    }

    openModal(index) {
        this.currentIndex = index;
        const modal = document.getElementById('galleryModalViewer');
        
        if (!modal) return;

        // Update image and title
        const item = this.galleryItems[index];
        const imageName = item.dataset.imageName;
        const imageTitle = item.dataset.imageTitle;

        const fullSrc = `${this.imageBaseDir}${imageName}`;
        const imgEl = document.getElementById('galleryModalImage');
        imgEl.src = fullSrc;
        document.getElementById('galleryTitle').textContent = imageTitle || 'Gallery Image';
        document.getElementById('galleryCounter').textContent = index + 1;

        // Update thumbnail active state
        document.querySelectorAll('.gallery-modal-thumbnail').forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });

        // Update navigation button states
        document.getElementById('galleryPrev').disabled = index === 0;
        document.getElementById('galleryNext').disabled = index === this.galleryItems.length - 1;

        // Show modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    closeModal() {
        const modal = document.getElementById('galleryModalViewer');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    nextImage() {
        if (this.currentIndex < this.galleryItems.length - 1) {
            this.openModal(this.currentIndex + 1);
        }
    }

    prevImage() {
        if (this.currentIndex > 0) {
            this.openModal(this.currentIndex - 1);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new GalleryModalViewer();
});
