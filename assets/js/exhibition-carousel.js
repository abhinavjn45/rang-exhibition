/**
 * Exhibition Details Carousel
 * Handles carousel navigation, drag/swipe functionality, and indicator dots
 */

class ExhibitionCarousel {
    constructor() {
        this.wrapper = document.getElementById('carouselWrapper');
        this.prevBtn = document.getElementById('carouselPrev');
        this.nextBtn = document.getElementById('carouselNext');
        this.slides = document.querySelectorAll('.carousel-slide');
        this.dots = document.querySelectorAll('.carousel-dot');
        
        if (!this.wrapper || this.slides.length === 0) return;
        
        this.currentIndex = 0;
        this.isDragging = false;
        this.startX = 0;
        this.currentX = 0;
        this.threshold = 50; // Minimum drag distance in pixels
        
        this.init();
    }
    
    init() {
        // Prev/Next button events
        this.prevBtn?.addEventListener('click', () => this.prevSlide());
        this.nextBtn?.addEventListener('click', () => this.nextSlide());
        
        // Indicator dots events
        this.dots.forEach(dot => {
            dot.addEventListener('click', (e) => {
                const slideIndex = parseInt(e.target.getAttribute('data-slide'));
                this.goToSlide(slideIndex);
            });
        });
        
        // Drag/Swipe events
        this.wrapper.addEventListener('mousedown', (e) => this.startDrag(e));
        this.wrapper.addEventListener('mousemove', (e) => this.drag(e));
        this.wrapper.addEventListener('mouseup', () => this.endDrag());
        this.wrapper.addEventListener('mouseleave', () => this.endDrag());
        
        // Touch events for mobile
        this.wrapper.addEventListener('touchstart', (e) => this.startDrag(e));
        this.wrapper.addEventListener('touchmove', (e) => this.drag(e));
        this.wrapper.addEventListener('touchend', () => this.endDrag());
    }
    
    startDrag(e) {
        this.isDragging = true;
        this.startX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
        this.currentX = this.startX;
        this.wrapper.classList.add('dragging');
    }
    
    drag(e) {
        if (!this.isDragging) return;
        
        this.currentX = e.type.includes('mouse') ? e.clientX : e.touches[0].clientX;
    }
    
    endDrag() {
        if (!this.isDragging) return;
        
        this.isDragging = false;
        this.wrapper.classList.remove('dragging');
        
        const diff = this.startX - this.currentX;
        
        // Swipe right - go to previous slide
        if (diff < -this.threshold) {
            this.prevSlide();
        }
        // Swipe left - go to next slide
        else if (diff > this.threshold) {
            this.nextSlide();
        }
    }
    
    nextSlide() {
        this.goToSlide((this.currentIndex + 1) % this.slides.length);
    }
    
    prevSlide() {
        this.goToSlide((this.currentIndex - 1 + this.slides.length) % this.slides.length);
    }
    
    goToSlide(index) {
        // Update current index
        this.currentIndex = index;
        
        // Update active slide
        this.slides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.add('active');
            } else {
                slide.classList.remove('active');
            }
        });
        
        // Update active dot
        this.dots.forEach((dot, i) => {
            if (i === index) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }
}

// Initialize carousel when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new ExhibitionCarousel();
});
