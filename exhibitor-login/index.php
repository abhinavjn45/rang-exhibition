<?php 
    session_start();

    require_once '../admin/assets/includes/config/config.php';
    require_once '../admin/assets/includes/functions/data_fetcher.php';

    if (isset($_SESSION['vendor_unique_id']) || isset($_GET['admin_unique_id'])) {
        header('Location: ' . get_site_option('site_url'));
        exit();
    } else {
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exhibitor Login - <?= htmlspecialchars(get_site_option('site_fullname')) ?> - <?= htmlspecialchars(get_site_option('site_tagline')) ?></title>
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
                            <a href="<?= get_site_option('site_url') ?>exhibitor-login/" class="breadcrumb-link">
                                <span>Exhibitor Login</span>
                            </a>
                        </li>
                    </ol>
                    <div class="gallery-hero-content">
                        <h1 class='gallery-hero-title'>Exhibitor Login</h1>
                        <p class='gallery-hero-subtitle'>Access your exhibitor account and manage your profile and products.</p>
                    </div>
                </nav>
            </div>
        </section>

        <!-- Registration Form Section -->
        <section class="vendor-registration-section">
            <div class="container-lg">
                <div class="registration-wrapper">
                    <div class="registration-card">
                        <div class="registration-header">
                            <h2 class="registration-title">Login to your Exhibitor Account</h2>
                            <p class="registration-subtitle">Fill in your details to access your exhibitor account</p>
                        </div>

                        <form class="registration-form" id="exhibitorLoginForm" method="POST">
                            <!-- Email -->
                            <div class="form-group">
                                <label for="emailAddress" class="form-label">
                                    <i class="fas fa-envelope"></i>
                                    Email Address
                                    <span class="required">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="emailAddress" 
                                    name="email"
                                    class="form-control" 
                                    placeholder="your.email@example.com"
                                    required
                                >
                            </div>

                            <!-- Password -->
                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Password
                                    <span class="required">*</span>
                                </label>
                                <div class="password-input-wrapper">
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password"
                                        class="form-control" 
                                        placeholder="Enter your password"
                                        minlength="6"
                                        required
                                    >
                                    <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="form-group" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <input 
                                        type="checkbox" 
                                        id="rememberMe" 
                                        name="remember_me"
                                        class="checkbox-input"
                                    >
                                    <label for="rememberMe" style="font-size: 14px; color: #666666; margin: 0; cursor: pointer;">
                                        Remember me
                                    </label>
                                </div>
                                <a href="<?= get_site_option('site_url') ?>vendor-forgot-password/" style="font-size: 14px; color: #F3D02D; text-decoration: none; font-weight: 600;">
                                    Forgot Password?
                                </a>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn-submit-registration">
                                <i class="fas fa-sign-in-alt"></i>
                                Sign In
                            </button>

                            <!-- Sign Up Link -->
                            <div class="form-footer">
                                <p class="login-prompt">
                                    Don't have an account? 
                                    <a href="<?= get_site_option('site_url') ?>exhibitor-registration/" class="login-link">Register here</a>
                                </p>
                            </div>
                        </form>
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
    <script src="../assets/js/modal-handler.js"></script>
    <script src="../assets/js/vendor-directory.js"></script>
    <script src="../assets/js/vendor-registration.js"></script>

    <!-- WhatsApp Balloon -->
    <?php require_once '../admin/assets/elements/user-side/whatsapp_balloon.php'; ?>
</body>
</html>
<?php } ?>