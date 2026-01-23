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
    <?php
        if (!isset($_GET['vendor'])) {
            echo "<title>All Vendors - " . htmlspecialchars(get_site_option('site_fullname')) . " - " . htmlspecialchars(get_site_option('site_tagline')) . "</title>";
        } else {
            $vendor_slug = htmlspecialchars($_GET['vendor']);
            $vendor_data = get_single_vendor_details(vendor_slug:$vendor_slug);
            
            echo "<title>" . ucwords(strtolower(htmlspecialchars($vendor_data['vendor_business_fullname']))) . " - " . htmlspecialchars(get_site_option('site_fullname')) . "</title>";
        }
    ?>
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
    <link rel="stylesheet" href="../assets/css/vendors.css">
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
                            <a href="<?= get_site_option('site_url') ?>vendors/" class="breadcrumb-link">
                                <span>Our Vendors</span>
                            </a>
                        </li>
                    </ol>
                    <div class="gallery-hero-content">
                        <?php 
                            if (!isset($_GET['vendor'])) {
                                echo "
                                    <h1 class='gallery-hero-title'>Our Vendors</h1>
                                    <p class='gallery-hero-subtitle'>Discover more about <?= htmlspecialchars(get_site_option('site_fullname')) ?>, our mission, vision, and the team dedicated to bringing you unforgettable art experiences.</p>
                                ";
                            } else {
                                echo "
                                    <h1 class='gallery-hero-title'>" . ucwords(strtolower(htmlspecialchars($vendor_data['vendor_business_fullname']))) . "</h1>
                                    <p class='gallery-hero-subtitle'>" . htmlspecialchars($vendor_data['vendor_business_tagline']) . "</p>
                                ";
                            }
                        ?>
                    </div>
                </nav>
            </div>
        </section>

        <?php 
            if (!isset($_GET['vendor'])) {
                require_once '../admin/assets/pages/vendors/all-vendors/index.php';
            } else {
                require_once '../admin/assets/pages/vendors/single-vendor.php';
            }
        ?>

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
    <script src="../assets/js/modal-handler.js"></script>
    <script src="../assets/js/vendor-directory.js"></script>

    <!-- WhatsApp Balloon -->
    <?php require_once '../admin/assets/elements/user-side/whatsapp_balloon.php'; ?>
</body>
</html>