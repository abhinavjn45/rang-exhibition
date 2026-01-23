<?php 
    session_start();

    require_once './admin/assets/includes/config/config.php';
    require_once './admin/assets/includes/functions/data_fetcher.php';
?>
<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= get_site_option('site_title') . " - " . get_site_option('site_tagline') ?></title>

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/site.webmanifest">
    <meta name="theme-color" content="#ffffff">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?= get_site_option('site_title') ?>">
    <meta property="og:description" content="<?= get_site_option('site_tagline') ?>">
    <meta property="og:image" content="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/android-chrome-512x512.png">
    <meta property="og:url" content="<?= get_site_option('site_url') ?>">
    <meta property="og:type" content="website">

    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= get_site_option('site_title') ?>">
    <meta name="twitter:description" content="<?= get_site_option('site_tagline') ?>">
    <meta name="twitter:image" content="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/android-chrome-512x512.png">

    <!-- CDNs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= get_site_option('site_url') ?>assets/css/global.css">
    <link rel="stylesheet" href="<?= get_site_option('site_url') ?>assets/css/style_home.css">
</head>
<body class="h-full">
    <div class="main-wrapper">
        <!-- Floating Navigation Starts Here -->
        <?php require_once './admin/assets/elements/user-side/navbar.php'; ?>

        <!-- Hero Section -->
        <section class="hero-section" id="home" style="background-image: url('<?= get_site_option('dashboard_url') ?>assets/uploads/images/website-required/<?= get_hero_section_data('background_image') ?>');">
            <div class="hero-container">
                <div class="hero-left">
                    <h1 id="hero-title">
                        <?= get_hero_section_data('main_heading') ?>
                    </h1>
                    <h2 id="hero-subtitle">
                        <?= get_hero_section_data('sub_heading') ?>
                    </h2>
                    <p id="hero-paragraph">
                        <?= get_hero_section_data('hero_paragraph') ?>
                    </p>
                    <div class="hero-cta-buttons">
                        <a href="<?= get_hero_section_data('primary_button_link') ?>" class="u-btn-primary" id="hero-cta-primary"><?= get_hero_section_data('primary_button_text') ?></a> 
                        <a href="<?= get_hero_section_data('secondary_button_link') ?>" class="u-btn-secondary hero-btn"><?= get_hero_section_data('secondary_button_text') ?></a>
                    </div>

                    <?php 
                        if (get_hero_section_data('show_category_carousel') === 'on') {
                    ?>
                    <!-- Category Carousel -->
                    <div class='category-carousel'>
                        <div class='category-track-wrapper'>
                            <div class='category-track'>
                                <?= category_pills_carousel(); ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </section>
   
        <!-- Trust Signal Carousel -->
        <section class="trust-section">
            <div class="carousel-track-container">
                <div class="carousel-track">

                  <!-- Card 1 -->
                  <div class="trust-signal-card">
                      <div class="trust-signal-icon">
                          <i class="fas fa-star"></i>
                      </div>
                      <div class="trust-signal-content">
                          <h3 class="trust-signal-number">50+ Exhibitions</h3>
                          <p class="trust-signal-text">Successfully Executed</p>
                      </div>
                  </div>

                  <!-- Card 2 -->
                  <div class="trust-signal-card">
                      <div class="trust-signal-icon">
                          <i class="fas fa-shop"></i>
                      </div>
                      <div class="trust-signal-content">
                          <h3 class="trust-signal-number">500+ Exhibitors</h3>
                          <p class="trust-signal-text">Across India</p>
                      </div>
                  </div>

                  <!-- Card 3 -->
                  <div class="trust-signal-card">
                      <div class="trust-signal-icon">
                          <i class="fas fa-users"></i>
                      </div>
                      <div class="trust-signal-content">
                          <h3 class="trust-signal-number">25K+ Attendees</h3>
                          <p class="trust-signal-text">Engaged across our exhibitions</p>
                      </div>
                  </div>
                  
                  <!-- Card 4 -->
                  <div class="trust-signal-card">
                      <div class="trust-signal-icon">
                          <i class="fas fa-check-circle"></i>
                      </div>
                      <div class="trust-signal-content">
                          <h3 class="trust-signal-number">100% On-Time</h3>
                          <p class="trust-signal-text">Perfect execution record</p>
                      </div>
                  </div>
                  
                  <!-- Card 5 -->
                  <div class="trust-signal-card">
                      <div class="trust-signal-icon">
                          <i class="fas fa-user-tie"></i>
                      </div>
                      <div class="trust-signal-content">
                          <h3 class="trust-signal-number">Expert Team</h3>
                          <p class="trust-signal-text">Certified event professionals</p>
                      </div>
                  </div>

                  <!-- Duplicate cards for seamless loop -->
                  <div class="trust-signal-card">
                      <div class="trust-signal-icon">
                          <i class="fas fa-star"></i>
                      </div>
                      <div class="trust-signal-content">
                          <h3 class="trust-signal-number">50+ Events</h3>
                          <p class="trust-signal-text">Successfully delivered experiences</p>
                      </div>
                  </div>

                  <div class="trust-signal-card">
                      <div class="trust-signal-icon">
                          <i class="fas fa-star"></i>
                      </div>
                      <div class="trust-signal-content">
                          <h3 class="trust-signal-number">500+ Exhibitors</h3>
                          <p class="trust-signal-text">Across India</p>
                      </div>
                  </div>

                  <div class="trust-signal-card">
                      <div class="trust-signal-icon">
                          <i class="fas fa-users"></i>
                      </div>
                      <div class="trust-signal-content">
                          <h3 class="trust-signal-number">10K+ Attendees</h3>
                          <p class="trust-signal-text">Engaged across our events</p>
                      </div>
                  </div>

                  <div class="trust-signal-card">
                      <div class="trust-signal-icon">
                          <i class="fas fa-check-circle"></i>
                      </div>
                      <div class="trust-signal-content">
                          <h3 class="trust-signal-number">100% On-Time</h3>
                          <p class="trust-signal-text">Perfect execution record</p>
                      </div>
                  </div>
                  <div class="trust-signal-card">
                      <div class="trust-signal-icon">
                          <i class="fas fa-user-tie"></i>
                      </div>
                      <div class="trust-signal-content">
                          <h3 class="trust-signal-number">Expert Team</h3>
                          <p class="trust-signal-text">Certified event professionals</p>
                      </div>
                  </div>
                </div>
            </div>
        </section>
   
        <!-- Upcoming Events Section -->
        <section class="events-section" id="upcoming-events">
            <div class="events-container">
                <div class="events-header">
                    <h2 class="events-title" id="events-title">
                        Upcoming Exhibitions
                    </h2>
                    <p class="events-description" id="events-description">
                        Join us at our upcoming most colourful lifestyle & fashion exhibitions of <?= date('Y') ?>.
                    </p>
                </div>
                <div class="events-carousel-section" id="upcoming-events">
                    <div class="events-carousel-wrapper">
                        <div class="carousel-nav prev" id="prevBtn">
                            <i class="fas fa-chevron-left"></i>
                        </div>
                        <div class="events-carousel">
                            <div class="carnival-string" id="stringTrack">
                                <svg viewbox="0 0 3000 120" preserveaspectratio="none" style="width: 100%; height: 100%;">
                                    <defs>
                                        <filter id="ropeBlur">
                                            <feGaussianBlur in="SourceGraphic" stdDeviation="1" />
                                        </filter>
                                    </defs>
                                    <!-- Main rope line spanning full width -->
                                    <path d="M 0,30 Q 300,20 600,32 Q 900,20 1200,35 Q 1500,20 1800,32 Q 2100,20 2400,35 Q 2700,20 3000,30" stroke="#a89968" stroke-width="4" fill="none" stroke-linecap="round" filter="url(#ropeBlur)" />
                                    <!-- Rope shadow/texture -->
                                    <path d="M 0,31 Q 300,21 600,33 Q 900,21 1200,36 Q 1500,21 1800,33 Q 2100,21 2400,36 Q 2700,21 3000,31" stroke="#8b7355" stroke-width="2" fill="none" opacity="0.7" stroke-linecap="round" />
                                    <!-- Rope highlight -->
                                    <path d="M 0,29 Q 300,19 600,31 Q 900,19 1200,34 Q 1500,19 1800,31 Q 2100,19 2400,34 Q 2700,19 3000,29" stroke="#d4c5a0" stroke-width="1" fill="none" opacity="0.5" stroke-linecap="round" />
                                </svg>
                            </div>
                            
                            <div class="events-cards-track" id="eventsTrack">
                                <?= get_all_events('upcoming-events', null, 'event_from', 'ASC', 8); ?>
                            </div>
                        </div>
                        <div class="carousel-nav next" id="nextBtn">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </div>
                <div class="events-footer">
                    <a href="#all-events" class="u-btn-primary">View All Events</a>
                </div>
            </div>
        </section>

        <!-- Featured Event Section (draggable carousel) -->
        <section class="featured-event-section">
            <div class="featured-carousel" id="featuredCarousel">
                <div class="featured-track" id="featuredTrack">
                    <?= get_featured_events('homepage') ?>
                    <!-- Duplicate <article class="featured-slide">...</article> to add more slides -->
                </div>
                <div class="featured-dots" id="featuredDots" aria-label="Featured slides navigation"></div>
            </div>
        </section>

        <!-- Energy Section -->
        <section class="energy-section" id="gallery">
            <!-- Section Header -->
            <div class="energy-header">
                <h2 class="energy-title">The Energy Speaks For Itself</h2>
                <p class="energy-subtitle">Living Proof</p>
            </div>
            
            <!-- Floating Memory Frames - Strip 1 -->
            <div class="frames-container">
                <div class="frames-strip">
                    <?= load_gallery('homepage-bottom', 'gallery_id', 'DESC', 8) ?>
                </div>
            </div>
            
            <!-- Ambient Floating Quotes -->
            <div class="ambient-quotes">
                <div class="floating-quote">
                    "Unmatched energy."
                </div>
                <div class="floating-quote">
                    "Best exhibition crowd we've seen."
                </div>
                <div class="floating-quote">
                    "An experience like no other."
                </div>
            </div>
            
            <!-- CTA -->
            <div class="energy-cta-wrapper">
                <a href="<?= get_site_option('site_url') ?>gallery/" class="energy-cta">Experience It Live</a>
            </div>
        </section>

        <!-- Two Paths Section -->
        <section class="two-paths-section">
            <!-- Section Header -->
            <div class="section-header">
                <h2 class="section-title">Two Paths. One Experience.</h2>
                <p class="section-subtitle">Choose Your Journey</p>
            </div>
            
            <!-- Paths Container -->
            <div class="paths-container">
                <!-- Central Divider -->
                <div class="central-divider"></div>
                
                <!-- Path 1: For Visitors -->
                <div class="path">
                    <div class="path-content">
                        <div class="path-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="path-heading">For Visitors</h3>
                        <p class="path-subheading">Step into a world of colour, culture, and creativity</p>
                        <ul class="value-points">
                            <li class="value-point">Discover premium brands and lifestyle experiences</li>
                            <li class="value-point">Immerse yourself in curated exhibitions and activations</li>
                            <li class="value-point">Connect with creators, innovators, and tastemakers</li>
                        </ul>
                        <a href="#" class="path-cta">Explore Events</a>
                    </div>
                </div>
                
                <!-- Path 2: For Exhibitors -->
                <div class="path">
                    <div class="path-content">
                        <div class="path-icon">
                            <i class="fas fa-shop"></i>
                        </div>
                        <h3 class="path-heading">For Exhibitors</h3>
                        <p class="path-subheading">Showcase your brand where the crowd gathers</p>
                        <ul class="value-points">
                            <li class="value-point">Reach thousands of high-intent lifestyle enthusiasts</li>
                            <li class="value-point">Amplify brand visibility in premium exhibition spaces</li>
                            <li class="value-point">Drive sales and partnerships at India's finest events</li>
                        </ul>
                        <a href="#" class="path-cta">Book Stall Now</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Instagram Grid -->
        <section class="instagram-section" id="instagram">
            <div class="instagram-container">
                <!-- Section Header -->
                <div class="instagram-header">
                    <div class="instagram-icon">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <h2 class="instagram-title">Follow Our Journey</h2>
                    <p class="instagram-subtitle">@rangexhibition</p>
                    <a href="https://www.instagram.com/rangexhibition" target="_blank" class="instagram-follow-btn">
                        Follow Us <i class="fab fa-instagram"></i>
                    </a>
                </div>

                <!-- Instagram Grid -->
                <!-- <div class="instagram-grid" id="instagramGrid"> -->
                    <!-- Posts will be dynamically loaded here -->
                    <!-- <div class="instagram-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading Instagram posts...</p>
                    </div>
                </div> -->
            </div>
        </section>

        <!-- Footer Section -->
        <?php require_once './admin/assets/elements/user-side/footer.php'; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile menu toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const toggleIcon = mobileToggle.querySelector('i');

        mobileToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('active');
            if (mobileMenu.classList.contains('active')) {
                toggleIcon.classList.remove('fa-bars');
                toggleIcon.classList.add('fa-times');
            } else {
                toggleIcon.classList.remove('fa-times');
                toggleIcon.classList.add('fa-bars');
            }
        });

        // Close menu when clicking on a link
        const mobileMenuLinks = mobileMenu.querySelectorAll('a');
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
                toggleIcon.classList.remove('fa-times');
                toggleIcon.classList.add('fa-bars');
            });
        });

        // Countdown Timer
        function initCountdownTimer() {
            const eventDate = new Date('January 17, 2026').getTime();
            
            function updateTimer() {
                const now = new Date().getTime();
                const timeLeft = eventDate - now;
                
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                
                const daysEl = document.getElementById('days');
                const hoursEl = document.getElementById('hours');
                const minutesEl = document.getElementById('minutes');
                const secondsEl = document.getElementById('seconds');
                
                if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
                if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
                if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
                if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
                
                // Stop if event date has passed
                if (timeLeft < 0) {
                    if (daysEl) daysEl.textContent = '00';
                    if (hoursEl) hoursEl.textContent = '00';
                    if (minutesEl) minutesEl.textContent = '00';
                    if (secondsEl) secondsEl.textContent = '00';
                    clearInterval(timerInterval);
                }
            }
            
            // Update immediately on load
            updateTimer();
            
            // Update every second
            const timerInterval = setInterval(updateTimer, 1000);
        }
        
        // Initialize timer when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                initFeaturedCarousel();
            });
        } else {
            initFeaturedCarousel();
        }

            // Featured carousel (drag + dots)
            function initFeaturedCarousel() {
                const track = document.getElementById('featuredTrack');
                const dotsContainer = document.getElementById('featuredDots');
                if (!track || !dotsContainer) return;

                const slides = Array.from(track.querySelectorAll('.featured-slide'));
                if (slides.length === 0) return;

                const dots = [];
                let currentIndex = 0;
                let autoplayTimer = null;
                const AUTOPLAY_DELAY = 5000;
                if (slides.length > 1) {
                    slides.forEach((_, idx) => {
                        const dot = document.createElement('button');
                        dot.type = 'button';
                        dot.className = 'featured-dot' + (idx === 0 ? ' active' : '');
                        dot.setAttribute('aria-label', `Go to slide ${idx + 1}`);
                        dot.addEventListener('click', () => scrollToSlide(idx));
                        dots.push(dot);
                        dotsContainer.appendChild(dot);
                    });
                } else {
                    dotsContainer.style.display = 'none';
                }

                let isDragging = false;
                let startX = 0;
                let startScroll = 0;

                track.addEventListener('pointerdown', (e) => {
                    isDragging = true;
                    track.setPointerCapture(e.pointerId);
                    startX = e.clientX;
                    startScroll = track.scrollLeft;
                    track.classList.add('is-dragging');
                    e.preventDefault();
                    stopAutoplay();
                });

                track.addEventListener('pointermove', (e) => {
                    if (!isDragging) return;
                    e.preventDefault();
                    const delta = e.clientX - startX;
                    track.scrollLeft = startScroll - delta;
                });

                ['pointerup', 'pointerleave', 'pointercancel'].forEach((evt) => {
                    track.addEventListener(evt, (e) => {
                        if (track.hasPointerCapture(e.pointerId)) {
                            track.releasePointerCapture(e.pointerId);
                        }
                        isDragging = false;
                        track.classList.remove('is-dragging');
                        startAutoplay();
                    });
                });

                function setActiveDot(idx) {
                    currentIndex = idx;
                    dots.forEach((dot, i) => dot.classList.toggle('active', i === idx));
                }

                function scrollToSlide(idx) {
                    const target = slides[idx];
                    if (!target) return;
                    track.scrollTo({ left: target.offsetLeft, behavior: 'smooth' });
                    setActiveDot(idx);
                }

                function updateActiveDot() {
                    if (slides.length <= 1) return;
                    let closestIdx = 0;
                    let minDistance = Number.POSITIVE_INFINITY;
                    const currentScroll = track.scrollLeft;

                    slides.forEach((slide, idx) => {
                        const distance = Math.abs(slide.offsetLeft - currentScroll);
                        if (distance < minDistance) {
                            minDistance = distance;
                            closestIdx = idx;
                        }
                    });

                    setActiveDot(closestIdx);
                }

                track.addEventListener('scroll', () => {
                    window.requestAnimationFrame(updateActiveDot);
                });

                function startAutoplay() {
                    if (slides.length <= 1) return;
                    stopAutoplay();
                    autoplayTimer = setInterval(() => {
                        const nextIndex = (currentIndex + 1) % slides.length;
                        scrollToSlide(nextIndex);
                    }, AUTOPLAY_DELAY);
                }

                function stopAutoplay() {
                    if (autoplayTimer) {
                        clearInterval(autoplayTimer);
                        autoplayTimer = null;
                    }
                }

                track.addEventListener('mouseenter', stopAutoplay);
                track.addEventListener('mouseleave', startAutoplay);

                updateActiveDot();
                startAutoplay();
            }

        // Events carousel functionality
        const track = document.getElementById('eventsTrack');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        let currentIndex = 0;
        const cardsVisible = 4;
        const originalCards = Array.from(track.children);
        const totalOriginalCards = originalCards.length;

        // Clone last 4 cards and add to beginning for seamless backward scroll
            const lastFourCards = originalCards.slice(-4);
        lastFourCards.reverse().forEach(card => {
        track.insertBefore(card.cloneNode(true), track.firstChild);
        });
        lastFourCards.reverse(); // Restore order

            // Keep track of how many clones were prepended (could be < 4 if fewer originals)
            const prependedClonesCount = lastFourCards.length;

        // Clone all cards and add to end for seamless forward scroll
        originalCards.forEach(card => {
        track.appendChild(card.cloneNode(true));
        });

        // Start at position 4 (where first original card is after prepending 4 clones)
            // Start at the first original card after the prepended clones
            currentIndex = prependedClonesCount;

        function getCardWidth() {
        const firstCard = track.children[0];
        if (!firstCard) return 0;
        return firstCard.offsetWidth;
        }

        function getGap() {
        const trackStyle = window.getComputedStyle(track);
        const gap = trackStyle.gap || '30px';
        return parseInt(gap);
        }

        function updateCarousel(useTransition = true) {
        if (!useTransition) {
            track.style.transition = 'none';
        }
        const cardWidth = getCardWidth();
        const gap = getGap();
        const offset = -currentIndex * (cardWidth + gap);
        track.style.transform = `translateX(${offset}px)`;
        if (!useTransition) {
            setTimeout(() => {
            track.style.transition = 'transform 0.5s ease';
            }, 50);
        }
        }

        // Initialize carousel position
        updateCarousel(false);

        nextBtn.addEventListener('click', () => {
        currentIndex++;
        updateCarousel();
        
        // When reaching the cloned cards at the end, seamlessly loop back
                if (currentIndex >= totalOriginalCards + prependedClonesCount) {
            setTimeout(() => {
                        currentIndex = prependedClonesCount;
            updateCarousel(false);
            }, 500);
        }
        });

        prevBtn.addEventListener('click', () => {
        currentIndex--;
        updateCarousel();
        
        // When going before the original cards, jump to cloned cards at the end
                if (currentIndex < prependedClonesCount) {
            setTimeout(() => {
                        currentIndex = totalOriginalCards + prependedClonesCount - 1;
            updateCarousel(false);
            }, 500);
        }
        });

        // Reset carousel position on resize
        let resizeTimer;
        window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
                    currentIndex = prependedClonesCount;
            updateCarousel(false);
            setTimeout(() => {
            track.style.transition = 'transform 0.5s ease';
            }, 50);
        }, 250);
        });

            // Autoplay for events carousel
            let autoplayInterval;
            const autoplayDelay = 3000; // milliseconds

            function startAutoplay() {
                stopAutoplay();
                autoplayInterval = setInterval(() => {
                    // Advance by one card; existing logic handles seamless looping
                    nextBtn.click();
                }, autoplayDelay);
            }

            function stopAutoplay() {
                if (autoplayInterval) {
                    clearInterval(autoplayInterval);
                    autoplayInterval = null;
                }
            }

            // Pause autoplay on hover over the carousel area
            const carouselElement = document.querySelector('.events-carousel');
            if (carouselElement) {
                carouselElement.addEventListener('mouseenter', stopAutoplay);
                carouselElement.addEventListener('mouseleave', startAutoplay);
            }

            // Start autoplay after initial positioning
            startAutoplay();

        // Instagram Feed Loader
        async function loadInstagramFeed() {
            const gridContainer = document.getElementById('instagramGrid');
            
            try {
                const response = await fetch('./admin/assets/api/instagram.php?limit=12');
                const data = await response.json();
                
                if (data.success && data.posts && data.posts.length > 0) {
                    // Clear loading state
                    gridContainer.innerHTML = '';
                    
                    // Create post elements
                    data.posts.forEach(post => {
                        const postElement = createInstagramPost(post);
                        gridContainer.appendChild(postElement);
                    });
                } else {
                    // Show error state
                    gridContainer.innerHTML = `
                        <div class="instagram-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <p>Unable to load Instagram posts at the moment.</p>
                            <p><a href="https://www.instagram.com/rang_exhibition/" target="_blank" class="instagram-follow-btn" style="margin-top: 20px;">Visit Our Instagram</a></p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Instagram feed error:', error);
                gridContainer.innerHTML = `
                    <div class="instagram-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Unable to load Instagram posts.</p>
                        <p><a href="https://www.instagram.com/rang_exhibition/" target="_blank" class="instagram-follow-btn" style="margin-top: 20px;">Visit Our Instagram</a></p>
                    </div>
                `;
            }
        }
        
        function createInstagramPost(post) {
            const postDiv = document.createElement('a');
            postDiv.href = post.permalink;
            postDiv.target = '_blank';
            postDiv.className = 'instagram-post';
            postDiv.rel = 'noopener noreferrer';
            
            postDiv.innerHTML = `
                <img src="${post.image}" alt="Instagram post" class="instagram-post-image" loading="lazy">
                <div class="instagram-post-overlay">
                    <div class="instagram-post-stats">
                        <div class="instagram-post-stat">
                            <i class="fas fa-heart"></i>
                            <span>${post.likes}</span>
                        </div>
                        <div class="instagram-post-stat">
                            <i class="fas fa-comment"></i>
                            <span>${post.comments}</span>
                        </div>
                    </div>
                    ${post.caption ? `<p class="instagram-post-caption">${escapeHtml(post.caption)}</p>` : ''}
                </div>
            `;
            
            return postDiv;
        }
        
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
        
        // Load Instagram feed when page loads
        if (document.getElementById('instagramGrid')) {
            loadInstagramFeed();
        }
    </script>
    <!-- WhatsApp balloon -->
    <?php require_once './admin/assets/elements/user-side/whatsapp_balloon.php'; ?>
</body>
</html>