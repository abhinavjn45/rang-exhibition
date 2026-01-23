/* ============================================
   Exhibition Details Page - JavaScript
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Carousel
    initCarousel();
    
    // Initialize FAQ Accordion
    initFAQ();
});

/* ============================================
   Carousel Functionality
   ============================================ */

let currentSlide = 0;
let carouselInterval;

function initCarousel() {
    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.indicator');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    // Auto-advance carousel
    startCarouselAutoplay();

    // Manual navigation
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            clearInterval(carouselInterval);
            showSlide(currentSlide - 1);
            startCarouselAutoplay();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            clearInterval(carouselInterval);
            showSlide(currentSlide + 1);
            startCarouselAutoplay();
        });
    }

    // Indicator navigation
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            clearInterval(carouselInterval);
            currentSlide = index;
            updateCarousel();
            startCarouselAutoplay();
        });
    });

    // Keyboard navigation
    document.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') {
            clearInterval(carouselInterval);
            showSlide(currentSlide - 1);
            startCarouselAutoplay();
        } else if (event.key === 'ArrowRight') {
            clearInterval(carouselInterval);
            showSlide(currentSlide + 1);
            startCarouselAutoplay();
        }
    });
}

function showSlide(n) {
    const slides = document.querySelectorAll('.carousel-slide');
    
    if (n >= slides.length) {
        currentSlide = 0;
    } else if (n < 0) {
        currentSlide = slides.length - 1;
    } else {
        currentSlide = n;
    }
    
    updateCarousel();
}

function updateCarousel() {
    const slides = document.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.indicator');

    slides.forEach((slide, index) => {
        slide.classList.remove('active');
        if (index === currentSlide) {
            slide.classList.add('active');
        }
    });

    indicators.forEach((indicator, index) => {
        indicator.classList.remove('active');
        if (index === currentSlide) {
            indicator.classList.add('active');
        }
    });
}

function startCarouselAutoplay() {
    carouselInterval = setInterval(() => {
        showSlide(currentSlide + 1);
    }, 5000); // Change slide every 5 seconds
}

/* ============================================
   FAQ Accordion Functionality
   ============================================ */

function initFAQ() {
    const faqQuestions = document.querySelectorAll('.faq-question');

    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetAnswer = document.getElementById(targetId);
            const faqItem = this.parentElement;

            // Toggle active state
            faqItem.classList.toggle('active');

            // Toggle answer visibility
            if (targetAnswer.style.display === 'none' || !targetAnswer.style.display) {
                targetAnswer.style.display = 'block';
                
                // Smooth scroll to question
                setTimeout(() => {
                    question.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            } else {
                targetAnswer.style.display = 'none';
            }

            // Close other open items (optional - remove if you want multiple open)
            // faqQuestions.forEach(otherQuestion => {
            //     if (otherQuestion !== question) {
            //         const otherId = otherQuestion.getAttribute('data-target');
            //         const otherAnswer = document.getElementById(otherId);
            //         otherAnswer.style.display = 'none';
            //         otherQuestion.parentElement.classList.remove('active');
            //     }
            // });
        });
    });
}

/* ============================================
   Smooth Scroll for Anchor Links
   ============================================ */

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        // Skip if href is just "#"
        if (href === '#') return;
        
        const target = document.querySelector(href);
        
        if (target) {
            e.preventDefault();
            
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

/* ============================================
   Lazy Loading Images (Optional Enhancement)
   ============================================ */

function initLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');

    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for browsers without IntersectionObserver
        images.forEach(img => {
            img.src = img.dataset.src;
        });
    }
}

/* ============================================
   Active Navigation Link Highlighting
   ============================================ */

function highlightActiveNavLink() {
    const navLinks = document.querySelectorAll('.nav-menu a');
    const sections = document.querySelectorAll('section');

    window.addEventListener('scroll', () => {
        let current = '';

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;

            if (pageYOffset >= sectionTop - 200) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    });
}

/* ============================================
   Intersection Observer for Animation on Scroll
   ============================================ */

function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all cards and items
    const elements = document.querySelectorAll(
        '.pricing-card, .description-item, .gallery-item, .faq-item, .details-card, .stats-sidebar > *'
    );

    elements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
}

// Initialize scroll animations when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScrollAnimations);
} else {
    initScrollAnimations();
}

/* ============================================
   Mobile Menu Toggle (If applicable)
   ============================================ */

function initMobileMenu() {
    const mobileToggle = document.getElementById('mobileToggle');
    const mobileMenu = document.getElementById('mobileMenu');

    if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
        });

        // Close menu when a link is clicked
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
            });
        });
    }
}

initMobileMenu();

/* ============================================
   Print-Friendly Styles Support
   ============================================ */

window.addEventListener('beforeprint', function() {
    // Hide carousel controls and auto-play elements
    const carouselControls = document.querySelectorAll('.carousel-control, .carousel-indicators');
    carouselControls.forEach(control => {
        control.style.display = 'none';
    });
});

window.addEventListener('afterprint', function() {
    // Restore carousel controls
    const carouselControls = document.querySelectorAll('.carousel-control, .carousel-indicators');
    carouselControls.forEach(control => {
        control.style.display = '';
    });
});

/* ============================================
   Utility Functions
   ============================================ */

// Debounce function for resize events
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Handle window resize
window.addEventListener('resize', debounce(() => {
    // Add any resize-specific logic here
    // For example, recalculate carousel dimensions
}, 250));

/* ============================================
   Accessibility Enhancements
   ============================================ */

// Add keyboard accessibility to buttons
document.querySelectorAll('button').forEach(button => {
    button.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            button.click();
        }
    });
});

// Add focus states for better keyboard navigation
document.addEventListener('keydown', (e) => {
    if (e.key === 'Tab') {
        document.body.classList.add('keyboard-nav');
    }
});

document.addEventListener('mousedown', () => {
    document.body.classList.remove('keyboard-nav');
});

console.log('Exhibition Details Page - JavaScript Loaded Successfully');
