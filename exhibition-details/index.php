<?php 
    session_start();

    require_once '../admin/assets/includes/config/config.php';
    require_once '../admin/assets/includes/functions/data_fetcher.php';

    if (!isset($_GET['exhibition']) || empty($_GET['exhibition'])) {
        // Redirect to 404 if no exhibition slug provided
        header("Location: ./404.php");
        exit();
    } else {
        $exhibition_slug = $_GET['exhibition'];
        $exhibition_data = get_event_details(null, $exhibition_slug);

        if (!$exhibition_data) {
            // Redirect to 404 if exhibition not found
            header("Location: ./404.php");
            exit();
        } else  {
            $venue_name = $exhibition_data['venue_name'];
                if (!empty($venue_name) && $venue_name !== NULL) {
                    $venue_name = htmlspecialchars($venue_name);
                    $has_venue_map = true;
                    $venue_iframe = $exhibition_data['venue_map_iframe'];
                    $venue_link = $exhibition_data['venue_map_link'];
                } else {
                    $venue_name = "To Be Announced";
                    $has_venue_map = false;
                    $venue_iframe = "";
                    $venue_link = "";
                }
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($exhibition_data['event_name']) ?> - <?= htmlspecialchars(get_site_option('site_fullname')) . " - " . htmlspecialchars(get_site_option('site_tagline')) ?></title>

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/favicon-16x16.png">
    <link rel="manifest" href="<?= get_site_option('dashboard_url') ?>assets/uploads/images/logos/favicon/site.webmanifest">
    <meta name="theme-color" content="#ffffff">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/style_home.css">
    <link rel="stylesheet" href="../assets/css/exhibition-details.css">
    <link rel="stylesheet" href="../assets/css/venue-modal.css">
    <link rel="stylesheet" href="../assets/css/gallery-modal.css">
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
                            <a href="<?= get_site_option('site_url') ?>exhibition-details/" class="breadcrumb-link">
                                <span>Exhibitions</span>
                            </a>
                        </li>
                        <li class="breadcrumb-separator">
                            <i class="fas fa-chevron-right"></i>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="javascript:void(0);" class="breadcrumb-link">
                                <span><?= htmlspecialchars($exhibition_data['event_name']) ?></span>
                            </a>
                        </li>
                    </ol>
                </nav>
            </div>
        </section>

        <!-- Exhibition Details Section -->
        <section class="exhibition-details-section">
            <div class="container-lg">
                <div class="row g-5 align-items-stretch">
                    <!-- Left Column: Exhibition Info -->
                    <div class="col-lg-8">
                        <div class="details-card">
                            <!-- Carousel -->
                            <div class="carousel-container mb-4">
                                <div class="carousel-wrapper" id="carouselWrapper">
                                    <?= load_single_exhibition_carousel($exhibition_data['event_city']) ?>
                                </div>
                                
                                <!-- Navigation Buttons -->
                                <button class="carousel-btn carousel-btn-prev" id="carouselPrev" aria-label="Previous slide">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="carousel-btn carousel-btn-next" id="carouselNext" aria-label="Next slide">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            
                            <h2 class="u-section-title details-title"><?php echo htmlspecialchars($exhibition_data['event_name']); ?></h2>
                            
                            <div class="exhibition-meta">
                                <div class="meta-item">
                                    <span class="meta-icon"><i class="fas fa-calendar-alt"></i></span>
                                    <div class="meta-content">
                                        <span class="meta-label">Dates</span>
                                        <span class="meta-value"><?php echo date('M d', strtotime($exhibition_data['event_from'])) . ' - ' . date('M d, Y', strtotime($exhibition_data['event_to'])); ?></span>
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-icon"><i class="fas fa-map-marker-alt"></i></span>
                                    <div class="meta-content">
                                        <span class="meta-label" id="openUploadModal">Venue (Click to view on map)</span>
                                        <span class="meta-value">
                                            <?php if ($has_venue_map): ?>
                                                <a href="javascript:void(0);" class="venue-name-ellipsis" id="openUploadModal1" style="text-decoration: none; color: #333333;">
                                                    <?= $venue_name ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="venue-name-ellipsis"><?= $venue_name ?></span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-icon"><i class="fas fa-clock"></i></span>
                                    <div class="meta-content">
                                        <span class="meta-label">Duration</span>
                                        <span class="meta-value"><?php echo ceil((strtotime($exhibition_data['event_to']) - strtotime($exhibition_data['event_from'])) / (60 * 60 * 24)); ?> Days</span>
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-icon"><i class="fas fa-users"></i></span>
                                    <div class="meta-content">
                                        <span class="meta-label">Expected Visitors</span>
                                        <span class="meta-value"><?php echo htmlspecialchars($exhibition_data['event_capacity'] ?? '2,000+'); ?> Attendees</span>
                                    </div>
                                </div>
                            </div>

                            <div class="exhibition-description">
                                <h3 class="sub-section-title">Why <?= htmlspecialchars($exhibition_data['city_name']) ?>?</h3>
                                <?php if (!empty($exhibition_data['city_why'])): ?>
                                    <p><?php echo nl2br(htmlspecialchars($exhibition_data['city_why'])); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="faq-item">
                                <button class="faq-question" data-target="faq-1">
                                    <span class="faq-text"><?= $exhibition_data['event_faqs_title'] ?></span>
                                    <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                                </button>
                                <div class="faq-answer" id="faq-1" style="display: none;">
                                    <?= $exhibition_data['event_faqs'] ?>
                                </div>
                            </div>

                            <div class="exhibition-highlights" style="margin-top: 30px;">
                                <h3 class="sub-section-title">Exhibition Highlights</h3>
                                <ul class="highlights-list">
                                    <?= $exhibition_data['event_highlights'] ?>
                                </ul>
                            </div>

                            <div class="cta-buttons">
                                <a href="<?= $exhibition_data['event_form_link'] ?>" target="_blank" class="u-btn-primary">Book Now</a>
                                <a href="#pricing" class="u-btn-secondary">View Pricing</a>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Quick Stats -->
                    <div class="col-lg-4">
                        <div class="stats-sidebar">
                            <div class="stats-card">
                                <h3 class="stats-title">Exhibition Stats</h3>
                                
                                <div class="stat-item">
                                    <div class="stat-number">55+</div>
                                    <div class="stat-label">Participating Exhibitors</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">100+</div>
                                    <div class="stat-label">Fashion Collections</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">2000+</div>
                                    <div class="stat-label">Expected Attendees</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">Countless</div>
                                    <div class="stat-label">Memories</div>
                                </div>
                            </div>

                            <!-- <div class="info-card highlight-card">
                                <div class="highlight-badge">Early Bird Offer</div>
                                <h4 class="highlight-title">Get 30% Off</h4>
                                <p class="highlight-text">Book your tickets before March 1st and save up to 30% on all ticket categories.</p>
                                <a href="#booking" class="u-btn-primary btn-full-width">Claim Offer</a>
                            </div> -->

                            <div class="info-card">
                                <h4 class="card-title">Venue Layout</h4>
                                <img src="<?= get_site_option('dashboard_url') ?>assets/uploads/images/venue-layouts/<?= $exhibition_data['venue_layout'] ?>" alt="Venue Layout" class="venue-layout-image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gallery Section -->
        <section class="gallery-section">
            <div class="container-lg">
                <h2 class="u-section-title section-title text-center mb-5">Exhibition Gallery</h2>
                
                <div class="gallery-grid">
                    <?= load_single_exhibition_gallery($exhibition_data['event_id']) ?>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section class="pricing-section" id="pricing">
            <div class="container-lg">
                <h2 class="u-section-title section-title text-center mb-5">Stall Plans</h2>
                
                <div class="pricing-grid">
                    <?= load_single_exhibition_plans($exhibition_data['event_id']) ?>
                </div>

                <div class="pricing-note">
                    <p><i class="fas fa-info-circle"></i> Exhibitors can ask for additional requirements on prior notice.</p>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section">
            <div class="container-lg">
                <h2 class="u-section-title section-title text-center mb-5">Frequently Asked Questions</h2>
                
                <div class="faq-container">
                    <div class="faq-item">
                        <button class="faq-question" data-target="faq-2">
                            <span class="faq-text">Is there any discounts available?</span>
                            <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                        </button>
                        <div class="faq-answer" id="faq-2" style="display: none;">
                            <p>Yes, we offer early booking and first-timer discounts. Additionally, group discounts are available. Prices are negotiable according to size, location, and type of stall.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" data-target="faq-3">
                            <span class="faq-text">What are the timings for the exhibition?</span>
                            <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                        </button>
                        <div class="faq-answer" id="faq-3" style="display: none;">
                            <p>The exhibition runs on both days from 10:00 AM to 09:00 PM. (FOR VISITORS) We recommend arriving early to avoid crowds and to fully enjoy all attractions. (FOR EXHIBITORS) Coming 2 Hours before the scheduled time can help you out in setting up the stall on time.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" data-target="faq-4">
                            <span class="faq-text">Is photography allowed inside the exhibition?</span>
                            <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                        </button>
                        <div class="faq-answer" id="faq-4" style="display: none;">
                            <p>Personal photography is allowed in most areas of the exhibition. However, professional photography and videography require special permission. Please check with our staff at the entrance for specific guidelines and designated photography zones.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" data-target="faq-5">
                            <span class="faq-text">What if any exhibitor have additional requirements?</span>
                            <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                        </button>
                        <div class="faq-answer" id="faq-5" style="display: none;">
                            <p>An additional setup or requirement must be informed to the organising team at least 7 days prior, and the charges are to be paid on the spot.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <button class="faq-question" data-target="faq-6">
                            <span class="faq-text">Can we use Poster or Standee for our stall?</span>
                            <span class="faq-icon"><i class="fas fa-chevron-down"></i></span>
                        </button>
                        <div class="faq-answer" id="faq-6" style="display: none;">
                            <p>Yes, you can use any promotional printing material only in your allocated area.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-final-section">
            <div class="container-lg">
                <div class="cta-content">
                    <h2 class="u-section-title cta-title">Don't Miss Out!</h2>
                    <p class="cta-subtitle">Join us at <?= get_site_option('site_title') ?> for an unforgettable experience filled with creativity, inspiration, and excitement. Secure your spot today!</p>
                    <div class="cta-buttons">
                        <a href="<?= $exhibition_data['event_form_link'] ?>" target="_blank" class="u-btn-primary">Book Your Stall Now</a>
                        <a href="javascript:void(0);" class="u-btn-secondary">Learn More</a>
                    </div>
                </div>
            </div>
        </section>

        <div class="upload-modal" id="uploadModal">
            <div class="upload-modal-overlay" id="uploadModalOverlay"></div>
            <div class="upload-modal-content">
                <div class="upload-modal-header">
                    <div class="upload-modal-header-text">
                        <h2 class="upload-modal-title">
                            <?= htmlspecialchars(ucwords($exhibition_data['venue_name'])) ?>
                        </h2>
                        <p class="upload-modal-subtitle">View Venue Location on Map for directions</p>
                    </div>
                    <button class="upload-modal-close" id="closeUploadModal" aria-label="Close modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="upload-modal-body">
                    <div class="venue-map-container" style="margin-bottom: 20px; width: 100%; height: 300px; overflow: hidden; border-radius: 8px;">
                        <?= $venue_iframe ?>
                    </div>
                    <a href="<?= htmlspecialchars($exhibition_data['venue_map_link']) ?>" target="_blank" class="u-btn-primary btn-full-width">
                        <i class="fas fa-directions"></i> View Directions
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php require_once '../admin/assets/elements/user-side/footer.php'; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/exhibition-carousel.js"></script>
    <script src="../assets/js/exhibition-details.js"></script>
    <script src="../assets/js/gallery-modal.js"></script>
    <script src="../assets/js/exhibition-venue-modal.js"></script>

    <!-- WhatsApp balloon -->
    <?php require_once '../admin/assets/elements/user-side/whatsapp_balloon.php'; ?>
</body>
</html>
<?php } } ?>