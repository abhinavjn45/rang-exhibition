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
    <title>Contact Us - <?= htmlspecialchars(get_site_option('site_fullname')) . " - " . htmlspecialchars(get_site_option('site_tagline')) ?></title>

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
    <link rel="stylesheet" href="../assets/css/contact-us.css">
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
                            <a href="<?= get_site_option('site_url') ?>contact-us/" class="breadcrumb-link">
                                <span>Contact Us</span>
                            </a>
                        </li>
                    </ol>
                    <div class="gallery-hero-content">
                        <h1 class="gallery-hero-title">Contact Us</h1>
                        <p class="gallery-hero-subtitle">We're here to help and answer any questions you may have. Reach out to us and we'll respond as soon as we can.</p>
                    </div>
                </nav>
            </div>
        </section>

        <!-- 2. Why Reach Out -->
        <section class="contact-reasons-section">
            <div class="container-lg">
                <h2 class="contact-reasons-title">What Can We Help You With?</h2>
                <div class="contact-reasons-list">
                    <div class="reason-item">
                        <i class="fas fa-store"></i>
                        <h3>Book a Stall</h3>
                        <p>Exhibit your products and connect with our audience</p>
                    </div>
                    <div class="reason-item">
                        <i class="fas fa-calendar-alt"></i>
                        <h3>Plan an Event</h3>
                        <p>Host a special event or workshop at our exhibitions</p>
                    </div>
                    <div class="reason-item">
                        <i class="fas fa-handshake"></i>
                        <h3>Brand Collaboration</h3>
                        <p>Partner with us to reach art and design enthusiasts</p>
                    </div>
                    <div class="reason-item">
                        <i class="fas fa-comments"></i>
                        <h3>General Enquiry</h3>
                        <p>Have a question? We'd love to hear from you</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Primary Contact Form -->
        <section class="contact-form-section">
            <div class="container-lg">
                <div class="contact-form-wrapper">
                    <h2 class="contact-form-title">Tell Us About Your Requirement</h2>
                    <form class="contact-form" id="contactForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contactName" class="form-label">Your Name</label>
                                <input type="text" id="contactName" name="name" class="form-control" placeholder="Enter your full name" required>
                            </div>
                            <div class="form-group">
                                <label for="contactEmail" class="form-label">Email Address</label>
                                <input type="email" id="contactEmail" name="email" class="form-control" placeholder="your.email@example.com" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="contactPhone" class="form-label">Phone Number</label>
                                <input type="tel" id="contactPhone" name="phone" class="form-control" placeholder="+91 xxxxx xxxxx">
                            </div>
                            <div class="form-group">
                                <label for="contactInterest" class="form-label">I'm interested in</label>
                                <select id="contactInterest" name="interest" class="form-control" required>
                                    <option value="">Select an option</option>
                                    <option value="book-stall">Book a Stall</option>
                                    <option value="plan-event">Plan an Event</option>
                                    <option value="collaboration">Brand Collaboration</option>
                                    <option value="general">General Enquiry</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="contactCity" class="form-label">City / Location</label>
                            <input type="text" id="contactCity" name="city" class="form-control" placeholder="Which city are you in?">
                        </div>

                        <div class="form-group full-width">
                            <label for="contactMessage" class="form-label">Tell Us More</label>
                            <textarea id="contactMessage" name="message" class="form-control" rows="5" placeholder="Share any details about your requirement..." required></textarea>
                        </div>

                        <button type="submit" class="contact-form-btn">
                            <i class="fas fa-paper-plane"></i> Submit Enquiry
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- 4. Quick Contact Options -->
        <section class="contact-quick-section">
            <div class="container-lg">
                <h2 class="contact-quick-title">Prefer a Faster Way?</h2>
                <div class="contact-quick-options">
                    <div class="quick-card">
                        <div class="quick-card-icon"><i class="fas fa-phone"></i></div>
                        <p class="quick-card-label">Call Us</p>
                        <a href="tel:+917688995560" class="quick-card-value">+91 76889 95560</a>
                    </div>
                    <div class="quick-card">
                        <div class="quick-card-icon"><i class="fas fa-envelope"></i></div>
                        <p class="quick-card-label">Email Us</p>
                        <a href="mailto:rangexhibition.india@gmail.com" class="quick-card-value">Click to Email</a>
                    </div>
                    <div class="quick-card">
                        <div class="quick-card-icon"><i class="fab fa-whatsapp"></i></div>
                        <p class="quick-card-label">WhatsApp</p>
                        <a href="https://api.whatsapp.com/send?phone=+917688995560&text=Hello!%20Can%20I%20get%20more%20info%20on%20this?" target="_blank" class="quick-card-value">Chat with us</a>
                    </div>
                    <div class="quick-card">
                        <div class="quick-card-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <p class="quick-card-label">Address</p>
                        <p class="quick-card-value">A101- Civil Lines, Jaipur, Rajasthan, 302006</p>
                    </div>
                    <div class="quick-card quick-card-social">
                        <div class="quick-card-icon"><i class="fas fa-share-alt"></i></div>
                        <p class="quick-card-label">Social Media</p>
                        <div class="quick-social-links" aria-label="Social media profiles">
                            <a href="https://www.instagram.com/rangexhibition" target="_blank" class="quick-social-link" title="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" target="_blank" class="quick-social-link" title="Facebook"><i class="fab fa-youtube"></i></a>
                            <a href="https://whatsapp.com/channel/0029VbBNgfOGzzKbGXSuqD2Y" target="_blank" class="quick-social-link" title="LinkedIn"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. Office / Presence Section -->
        <section class="contact-office-section">
            <div class="container-lg">
                <h2 class="contact-office-title">Where We Operate From</h2>
                <div class="contact-office-content">
                    <div class="office-text">
                        <p class="office-description">Rang Exhibitions is headquartered in Jaipur, India, and we proudly serve clients across multiple cities. Our presence in these locations allows us to bring our unique exhibitions closer to art and design enthusiasts nationwide.</p>
                    </div>
                    <div class="city-grid" aria-label="Cities we operate in">
                        <div class="city-card">
                            <div class="city-icon" aria-hidden="true">
                                <img src="../admin/assets/uploads/images/icons/jaipur.png" alt="Hawa Mahal icon" loading="lazy">
                            </div>
                            <p class="city-name">Jaipur</p>
                        </div>
                        <div class="city-card">
                            <div class="city-icon" aria-hidden="true">
                                <img src="../admin/assets/uploads/images/icons/udaipur.png" alt="Hawa Mahal icon" loading="lazy">
                            </div>
                            <p class="city-name">Udaipur</p>
                        </div>
                        <div class="city-card">
                            <div class="city-icon" aria-hidden="true">
                                <img src="../admin/assets/uploads/images/icons/jodhpur.png" alt="Hawa Mahal icon" loading="lazy">
                            </div>
                            <p class="city-name">Jodhpur</p>
                        </div>
                        <div class="city-card">
                            <div class="city-icon" aria-hidden="true">
                                <img src="../admin/assets/uploads/images/icons/bhilwara.png" alt="Hawa Mahal icon" loading="lazy">
                            </div>
                            <p class="city-name">Bhilwara</p>
                        </div>
                        <div class="city-card">
                            <div class="city-icon" aria-hidden="true">
                                <img src="../admin/assets/uploads/images/icons/ajmer.png" alt="Hawa Mahal icon" loading="lazy">
                            </div>
                            <p class="city-name">Ajmer</p>
                        </div>
                        <div class="city-card">
                            <div class="city-icon" aria-hidden="true">
                                <img src="../admin/assets/uploads/images/icons/bikaner.png" alt="Hawa Mahal icon" loading="lazy">
                            </div>
                            <p class="city-name">Bikaner</p>
                        </div>
                        <div class="city-card">
                            <div class="city-icon" aria-hidden="true">
                                <img src="../admin/assets/uploads/images/icons/kota.png" alt="Hawa Mahal icon" loading="lazy">
                            </div>
                            <p class="city-name">Kota</p>
                        </div>
                        <div class="city-card">
                            <div class="city-icon" aria-hidden="true">
                                <img src="../admin/assets/uploads/images/icons/kishangarh.png" alt="Taj Mahal icon" loading="lazy">
                            </div>
                            <p class="city-name">Kishangarh</p>
                        </div>
                        <div class="city-card">
                            <div class="city-icon" aria-hidden="true">
                                <img src="../admin/assets/uploads/images/icons/delhi.png" alt="India Gate icon" loading="lazy">
                            </div>
                            <p class="city-name">New Delhi</p>
                        </div>
                        <div class="city-card">
                            <div class="city-icon" aria-hidden="true">
                                <img src="../admin/assets/uploads/images/icons/mumbai.png" alt="Gateway of India icon" loading="lazy">
                            </div>
                            <p class="city-name">Mumbai</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. Reassurance & Expectations -->
        <section class="contact-process-section">
            <div class="container-lg">
                <h2 class="contact-process-title">What Happens After You Contact Us?</h2>
                <div class="contact-process-steps">
                    <div class="process-step">
                        <div class="step-number">1</div>
                        <h3 class="step-title">We Review Your Requirement</h3>
                        <p class="step-description">Our team carefully reads your enquiry and understands what you're looking for.</p>
                    </div>
                    <div class="process-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <div class="process-step">
                        <div class="step-number">2</div>
                        <h3 class="step-title">Our Team Connects With You</h3>
                        <p class="step-description">We reach out via phone or email to discuss details and answer your questions.</p>
                    </div>
                    <div class="process-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                    <div class="process-step">
                        <div class="step-number">3</div>
                        <h3 class="step-title">We Plan Together</h3>
                        <p class="step-description">Together, we map out next steps and create a plan that works for you.</p>
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
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/nav.js"></script>

    <!-- WhatsApp Balloon -->
    <?php require_once '../admin/assets/elements/user-side/whatsapp_balloon.php'; ?>
</body>
</html>