<?php 
    session_start();

    require_once '../admin/assets/includes/config/config.php';
    require_once '../admin/assets/includes/functions/data_fetcher.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?= htmlspecialchars(get_site_option('site_fullname')) . " - " . htmlspecialchars(get_site_option('site_tagline')) ?></title>

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/site.webmanifest">
    <meta name="theme-color" content="#ffffff">

    <!-- CDNs -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/gallery.css">
    <link rel="stylesheet" href="../assets/css/cities.css">
    <link rel="stylesheet" href="../assets/css/exhibition-details.css">
    <link rel="stylesheet" href="../assets/css/about-us.css">
</head>
<body>
    <div class="main-wrapper">
        <!-- Navigation -->
        <?php require_once '../admin/assets/elements/user-side/navbar.php'; ?>

        <!-- Breadcrumb Section -->
        <section class="breadcrumb-section">
            <div class="container-lg">
                <nav class="breadcrumb-nav" aria-label="breadcrumb">
                    <ol class="breadcrumb-list">
                        <li class="breadcrumb-item">
                            <a href="<?= get_site_option('site_url') ?>" class="breadcrumb-link">
                                <i class="fas fa-home"></i>
                                <span>Home</span>
                            </a>
                        </li>
                        <li class="breadcrumb-separator">
                            <i class="fas fa-chevron-right"></i>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?= get_site_option('site_url') ?>about-us/" class="breadcrumb-link">
                                <span>About Us</span>
                            </a>
                        </li>
                    </ol>
                    <div class="gallery-hero-content">
                        <h1 class="gallery-hero-title">About Us</h1>
                        <p class="gallery-hero-subtitle">Discover more about <?= htmlspecialchars(get_site_option('site_fullname')) ?>, our mission, vision, and the team dedicated to bringing you unforgettable art experiences.</p>
                    </div>
                </nav>
            </div>
        </section>

        <!-- Who we are Section -->
        <section class="who-we-are-section">
            <div class="container-lg">
                <div class="who-we-are-container">
                    <!-- Left Side: Content (50%) -->
                    <div class="who-we-are-content">
                        <h2 class="who-we-are-title">Who We Are?</h2>
                        <p class="who-we-are-subtitle">We are more than just an exhibition platform</p>
                        
                        <div class="who-we-are-description">
                            <p>Rang is more than just a name; it is the spirit of our event. We transform ordinary exhibition spaces into vibrant festivals of style, bringing together the rich heritage of Indian craftsmanship and the bold strokes of contemporary fashion.</p>
                            <ul class="who-we-are-list">
                                <li>Celebrating art & culture</li>
                                <li>World-class exhibitions nationwide</li>
                                <li>Creativity meets commerce</li>
                                <li>Nurturing emerging artists</li>
                                <li>Fostering meaningful connections</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Right Side: Features (50%) -->
                    <div class="who-we-are-visual">
                        <div class="who-we-are-features">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-lightbulb"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Innovative Curation</h4>
                                    <p>Carefully curated experiences that showcase emerging and established artists</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Global Reach</h4>
                                    <p>Bringing international standards and local flavors to exhibitions across multiple cities</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Community Focused</h4>
                                    <p>Creating spaces for meaningful connections between artists, creators, and art enthusiasts</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="feature-content">
                                    <h4>Excellence & Quality</h4>
                                    <p>Committed to delivering premium experiences with attention to every detail</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section class="description-section">
            <div class="container-lg">
                <div class="description-content">
                    <h2 class="u-section-title section-title mb-4">Why Attend This Exhibition?</h2>
                    
                    <div class="description-grid">
                        <div class="description-item" style="opacity: 1; transform: translateY(0px); transition: opacity 0.6s, transform 0.6s;">
                            <div class="description-icon">
                                <i class="fas fa-paint-brush"></i>
                            </div>
                            <h3 class="description-title">Creative Showcase</h3>
                            <p>Witness the convergence of art, design, and innovation through curated collections from renowned and emerging designers.</p>
                        </div>

                        <div class="description-item" style="opacity: 1; transform: translateY(0px); transition: opacity 0.6s, transform 0.6s;">
                            <div class="description-icon">
                                <i class="fas fa-network-wired"></i>
                            </div>
                            <h3 class="description-title">Networking Opportunities</h3>
                            <p>Connect with industry professionals, fellow enthusiasts, and potential collaborators in the fashion and lifestyle space.</p>
                        </div>

                        <div class="description-item" style="opacity: 1; transform: translateY(0px); transition: opacity 0.6s, transform 0.6s;">
                            <div class="description-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h3 class="description-title">Exclusive Previews</h3>
                            <p>Get first access to upcoming collections, limited edition pieces, and exclusive merchandise before public release.</p>
                        </div>

                        <div class="description-item" style="opacity: 1; transform: translateY(0px); transition: opacity 0.6s, transform 0.6s;">
                            <div class="description-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <h3 class="description-title">Industry Recognition</h3>
                            <p>Be part of a prestigious event that celebrates excellence and innovation in the fashion and lifestyle industry.</p>
                        </div>

                        <div class="description-item" style="opacity: 1; transform: translateY(0px); transition: opacity 0.6s, transform 0.6s;">
                            <div class="description-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <h3 class="description-title">Learning &amp; Workshops</h3>
                            <p>Participate in interactive workshops, seminars, and masterclasses led by industry experts and renowned designers.</p>
                        </div>

                        <div class="description-item" style="opacity: 1; transform: translateY(0px); transition: opacity 0.6s, transform 0.6s;">
                            <div class="description-icon">
                                <i class="fas fa-camera"></i>
                            </div>
                            <h3 class="description-title">Instagram-Worthy Moments</h3>
                            <p>Capture stunning photos and videos at beautifully designed installations and exhibition backdrops.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Timeline Section - Our Journey -->
        <section class="timeline-section">
            <div class="container-lg">
                <h2 class="u-section-title timeline-title">Our Journey</h2>
                <p class="timeline-subtitle">Follow the path of your exhibition experience</p>
                
                <div class="timeline-container">
                    <div class="timeline-road"></div>
                    
                    <div class="timeline-items">
                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>Founded</h4>
                                <p>Our vision begins</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>Launch</h4>
                                <p>First exhibition</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>Growth</h4>
                                <p>Expanding horizons</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>Global</h4>
                                <p>Multi-city presence</p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker">
                                <i class="fas fa-crown"></i>
                            </div>
                            <div class="timeline-content">
                                <h4>Excellence</h4>
                                <p>Industry leader</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vision & Mission Section -->
        <section class="vision-mission-section">
            <div class="container-lg">
                <h2 class="u-section-title vision-mission-title">Our Vision & Mission</h2>
                <p class="vision-mission-subtitle">Guiding principles that drive our passion</p>
                
                <div class="vision-mission-container">
                    <!-- Vision Card -->
                    <div class="vision-card">
                        <div class="vm-icon-wrapper">
                            <div class="vm-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                        <h3 class="vm-title">Our Vision</h3>
                        <p class="vm-description">
                            To become the leading platform for lifestyle exhibitions, transforming how people discover and experience art, fashion, and design across India and beyond.
                        </p>
                    </div>

                    <!-- Mission Card -->
                    <div class="mission-card">
                        <div class="vm-icon-wrapper">
                            <div class="vm-icon">
                                <i class="fas fa-bullseye"></i>
                            </div>
                        </div>
                        <h3 class="vm-title">Our Mission</h3>
                        <p class="vm-description">
                            To curate exceptional exhibition experiences that connect creators with audiences, foster cultural appreciation, and empower businesses to thrive in the lifestyle industry.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section class="team-section">
            <div class="container-lg">
                <h2 class="u-section-title team-title">Meet Our Dedicated Team</h2>
                <p class="team-subtitle">The creative minds behind our exhibitions</p>
                
                <div class="team-grid">
                    <!-- Team Member 1 -->
                    <div class="team-card">
                        <div class="team-card-image">
                            <img src="<?= get_site_option('dashboard_url') ?>assets/uploads/images/team/member-1.jpg" alt="Team Member" class="member-image">
                        </div>
                        <div class="team-card-content">
                            <h4 class="member-name">Sarah Johnson</h4>
                            <p class="member-designation">Founder & Creative Director</p>
                            <div class="member-socials">
                                <a href="#" class="social-link" title="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="social-link" title="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Team Member 2 -->
                    <div class="team-card">
                        <div class="team-card-image">
                            <img src="<?= get_site_option('dashboard_url') ?>assets/uploads/images/team/member-2.jpg" alt="Team Member" class="member-image">
                        </div>
                        <div class="team-card-content">
                            <h4 class="member-name">Michael Chen</h4>
                            <p class="member-designation">Curator & Brand Manager</p>
                            <div class="member-socials">
                                <a href="#" class="social-link" title="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="social-link" title="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Team Member 3 -->
                    <div class="team-card">
                        <div class="team-card-image">
                            <img src="<?= get_site_option('dashboard_url') ?>assets/uploads/images/team/member-3.jpg" alt="Team Member" class="member-image">
                        </div>
                        <div class="team-card-content">
                            <h4 class="member-name">Emily Williams</h4>
                            <p class="member-designation">Event Coordinator</p>
                            <div class="member-socials">
                                <a href="#" class="social-link" title="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="social-link" title="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Team Member 4 -->
                    <div class="team-card">
                        <div class="team-card-image">
                            <img src="<?= get_site_option('dashboard_url') ?>assets/uploads/images/team/member-4.jpg" alt="Team Member" class="member-image">
                        </div>
                        <div class="team-card-content">
                            <h4 class="member-name">David Martinez</h4>
                            <p class="member-designation">Marketing Specialist</p>
                            <div class="member-socials">
                                <a href="#" class="social-link" title="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="social-link" title="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Team Member 5 -->
                    <div class="team-card">
                        <div class="team-card-image">
                            <img src="<?= get_site_option('dashboard_url') ?>assets/uploads/images/team/member-5.jpg" alt="Team Member" class="member-image">
                        </div>
                        <div class="team-card-content">
                            <h4 class="member-name">David Martinez</h4>
                            <p class="member-designation">Marketing Specialist</p>
                            <div class="member-socials">
                                <a href="#" class="social-link" title="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="social-link" title="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="gallery-cta-section">
            <div class="container-lg">
                <div class="cta-content">
                    <h2 class="u-section-title cta-title">Want us in Your City?</h2>
                    <p class="cta-subtitle">Help us bring our exhibitions to your city by sharing your interest with us.</p>
                    <div class="cta-buttons">
                        <a href="javascript:void(0);" class="u-btn-primary" id="openUploadModal">Suggest Your City</a>
                        <a href="<?= get_site_option('site_url') ?>cities/" class="u-btn-secondary">Explore Upcoming Exhibitions</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <?php require_once '../admin/assets/elements/user-side/footer.php'; ?>

        <!-- Upload Photos Modal -->
        <div class="upload-modal" id="uploadModal">
            <div class="upload-modal-overlay" id="uploadModalOverlay"></div>
            <div class="upload-modal-content">
                <div class="upload-modal-header">
                    <div class="upload-modal-header-text">
                        <h2 class="upload-modal-title">Want us in Your City?</h2>
                        <p class="upload-modal-subtitle">Help us bring our exhibitions to your city by sharing your interest with us.</p>
                    </div>
                    <button class="upload-modal-close" id="closeUploadModal" aria-label="Close modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="upload-modal-body">
                    <div class="coming-soon-badge">
                        <i class="fas fa-clock"></i>
                        <span>Coming Soon</span>
                    </div>
                    <form class="upload-form" disabled>
                        <div class="form-group">
                            <label for="uploaderName" class="form-label">Your Name</label>
                            <input type="text" id="uploaderName" class="form-control" placeholder="Enter your name" disabled>
                        </div>
                        <div class="form-group">
                            <label for="uploaderEmail" class="form-label">Email Address</label>
                            <input type="email" id="uploaderEmail" class="form-control" placeholder="your.email@example.com" disabled>
                        </div>
                        <div class="form-group">
                            <label for="eventName" class="form-label">Event/Exhibition Name</label>
                            <input type="text" id="eventName" class="form-control" placeholder="Which exhibition did you attend?" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Upload Photos</label>
                            <div class="file-upload-area" disabled>
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p class="file-upload-text">Drag & drop your photos here or click to browse</p>
                                <p class="file-upload-hint">Supported formats: JPG, PNG, HEIC (Max 10 files, 5MB each)</p>
                                <input type="file" id="photoFiles" class="file-input" multiple accept="image/*" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="photoDescription" class="form-label">Description (Optional)</label>
                            <textarea id="photoDescription" class="form-control" rows="3" placeholder="Tell us about your photos..." disabled></textarea>
                        </div>
                        <button type="submit" class="u-btn-primary btn-full-width" disabled>
                            <i class="fas fa-paper-plane"></i> Submit Photos
                        </button>
                    </form>
                    <div class="coming-soon-message">
                        <i class="fas fa-info-circle"></i>
                        <p>This feature is under development and will be available soon. Stay tuned!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/nav.js"></script>
    <script src="../assets/js/gallery-upload-modal.js"></script>

    <!-- WhatsApp Balloon -->
    <?php require_once '../admin/assets/elements/user-side/whatsapp_balloon.php'; ?>
</body>
</html>