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
    <title>Gallery - <?= htmlspecialchars(get_site_option('site_fullname')) . " - " . htmlspecialchars(get_site_option('site_tagline')) ?></title>

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
    <link rel="stylesheet" href="../assets/css/style_home.css">
    <link rel="stylesheet" href="../assets/css/gallery.css">
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
                            <a href="<?= get_site_option('site_url') ?>gallery/" class="breadcrumb-link">
                                <span>Our Gallery</span>
                            </a>
                        </li>
                    </ol>
                    <div class="gallery-hero-content">
                        <h1 class="gallery-hero-title">Our Gallery</h1>
                        <p class="gallery-hero-subtitle">Explore our collection of memories, highlights, and special moments captured at our exhibitions across different cities.</p>
                    </div>
                </nav>
            </div>
        </section>

        <!-- Gallery Statistics -->
        <section class="gallery-stats-section">
            <div class="container-lg">
                <div class="gallery-stats">
                    <div class="stat-item">
                        <div class="stat-number">
                            <?= 
                                get_total_numbers('events', 
                                    [
                                        'column' => 'event_status',
                                        'operator' => '!=',
                                        'value' => 'deleted'
                                    ]);
                            ?>
                        </div>
                        <div class="stat-label">Events</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">
                            <?= 
                                get_total_numbers('cities', 
                                    [
                                        'column' => 'city_status',
                                        'operator' => '!=',
                                        'value' => 'deleted'
                                    ]);
                            ?>
                        </div>
                        <div class="stat-label">Cities</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">
                            <?= 
                                get_total_numbers('venues', 
                                    [
                                        'column' => 'venue_status',
                                        'operator' => '!=',
                                        'value' => 'deleted'
                                    ]);
                            ?>
                        </div>
                        <div class="stat-label">Venues</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">
                            <?= 
                                get_total_numbers('gallery', 
                                    [
                                        'column' => 'gallery_status',
                                        'operator' => '=',
                                        'value' => 'published'
                                    ]);
                            ?>
                        </div>
                        <div class="stat-label">Images</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gallery Section -->
        <section class="gallery-main-section">
            <div class="container-lg">
                <div class="gallery-container" id="galleryGrid">
                    <?= load_gallery('gallery-page', 'gallery_id', 'DESC', 20) ?>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="gallery-cta-section">
            <div class="container-lg">
                <div class="cta-content">
                    <h2 class="u-section-title cta-title">Have Memories to Share?</h2>
                    <p class="cta-subtitle">If you have captured amazing moments at our exhibitions, we'd love to feature them in our gallery. Get in touch with us!</p>
                    <div class="cta-buttons">
                        <a href="javascript:void(0);" class="u-btn-primary" id="openUploadModal">Send Us Your Photos</a>
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
                        <h2 class="upload-modal-title">Share Your Moments</h2>
                        <p class="upload-modal-subtitle">Help us capture the essence of our exhibitions by sharing your favorite photos</p>
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
    <script src="../assets/js/gallery-modal.js"></script>
    <script src="../assets/js/nav.js"></script>
    <script src="../assets/js/gallery-upload-modal.js"></script>

    <!-- WhatsApp Balloon -->
    <?php require_once '../admin/assets/elements/user-side/whatsapp_balloon.php'; ?>
</body>
</html>