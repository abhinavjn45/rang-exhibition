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
    <title>Forgot Password - <?= htmlspecialchars(get_site_option('site_fullname')) ?></title>
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
                            <a href="<?= get_site_option('site_url') ?>vendor-forgot-password/" class="breadcrumb-link">
                                <span>Forgot Password</span>
                            </a>
                        </li>
                    </ol>
                    <div class="gallery-hero-content">
                        <h1 class='gallery-hero-title'>Reset Your Password</h1>
                        <p class='gallery-hero-subtitle'>Enter your email address to receive a password reset link</p>
                    </div>
                </nav>
            </div>
        </section>

        <!-- Registration Form Section -->
        <section class="vendor-registration-section">
            <div class="container-lg">
                <div class="registration-wrapper">
                    <div class="registration-card" style="max-width: 500px; margin: 0 auto;">
                        <div class="registration-header">
                            <h2 class="registration-title">Forgot Password?</h2>
                            <p class="registration-subtitle">We'll help you reset your password</p>
                        </div>

                        <form class="registration-form" id="forgotPasswordForm" method="POST">
                            <!-- Email with OTP Button Inside -->
                            <div class="form-group">
                                <label for="forgotEmail" class="form-label">
                                    <i class="fas fa-envelope"></i>
                                    Email Address
                                    <span class="required">*</span>
                                </label>
                                <div class="email-input-wrapper">
                                    <input 
                                        type="email" 
                                        id="forgotEmail" 
                                        name="email"
                                        class="form-control" 
                                        placeholder="your.email@example.com"
                                        required
                                    >
                                    <button type="button" id="sendOtpBtn" class="btn-send-otp-inside">
                                        Send OTP
                                    </button>
                                </div>
                            </div>

                            <!-- OTP Input -->
                            <div class="form-group" id="otpGroup" style="display: none;">
                                <label for="otpCode" class="form-label">
                                    <i class="fas fa-shield-alt"></i>
                                    Enter OTP
                                    <span class="required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="otpCode" 
                                    name="otp"
                                    class="form-control" 
                                    placeholder="Enter 6-digit OTP"
                                    maxlength="6"
                                    pattern="[0-9]{6}"
                                >
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn-submit-registration">
                                <i class="fas fa-envelope"></i>
                                Send Reset Link
                            </button>

                            <!-- Back to Login -->
                            <div class="form-footer">
                                <p class="login-prompt">
                                    Remember your password? 
                                    <a href="<?= get_site_option('site_url') ?>exhibitor-login/" class="login-link">Back to Login</a>
                                </p>
                            </div>
                        </form>
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

    <!-- Styles for OTP Button Inside Email Field -->
    <style>
        .email-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .email-input-wrapper .form-control {
            padding-right: 45px;
        }

        .btn-send-otp-inside {
            position: absolute;
            right: 8px;
            background: transparent;
            border: none;
            color: #F3D02D;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            padding: 8px 12px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            white-space: nowrap;
        }

        .btn-send-otp-inside:hover {
            color: #E0BE25;
            transform: scale(1.05);
        }

        .btn-send-otp-inside:active {
            transform: scale(0.98);
        }

        .btn-send-otp-inside:disabled {
            color: #D3D3D3;
            cursor: not-allowed;
            transform: none;
            opacity: 0.7;
        }
    </style>

    <!-- Forgot Password Form Script -->
    <script>
        let otpSent = false;
        let otpTimer = 0;

        const sendOtpBtn = document.getElementById('sendOtpBtn');
        const forgotEmail = document.getElementById('forgotEmail');
        const otpGroup = document.getElementById('otpGroup');
        const otpCode = document.getElementById('otpCode');
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');

        // Send OTP button click handler
        sendOtpBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const email = forgotEmail.value.trim();
            
            // Validate email
            if (!email) {
                alert('Please enter your email address');
                return;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address');
                return;
            }
            
            // Send OTP (replace with actual backend integration)
            alert('OTP sent to: ' + email);
            otpSent = true;
            
            // Show OTP input field
            otpGroup.style.display = 'block';
            otpCode.focus();
            
            // Disable button and start timer (60 seconds)
            sendOtpBtn.disabled = true;
            otpTimer = 60;
            
            const timerInterval = setInterval(() => {
                otpTimer--;
                sendOtpBtn.innerHTML = `<i class="fas fa-clock"></i> Resend in ${otpTimer}s`;
                
                if (otpTimer <= 0) {
                    clearInterval(timerInterval);
                    sendOtpBtn.disabled = false;
                    sendOtpBtn.innerHTML = `<i class="fas fa-paper-plane"></i> Resend OTP`;
                    otpSent = false;
                }
            }, 1000);
        });

        // Form submit handler
        forgotPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!otpSent) {
                alert('Please send OTP first');
                return;
            }
            
            const otp = otpCode.value.trim();
            const email = forgotEmail.value.trim();
            
            if (!otp) {
                alert('Please enter the OTP');
                return;
            }
            
            if (otp.length !== 6 || isNaN(otp)) {
                alert('OTP must be 6 digits');
                return;
            }
            
            // Submit form (replace with actual backend integration)
            alert('Password reset link will be sent to: ' + email + '\n\nOTP: ' + otp + '\n\nPlease check your inbox and spam folder.');
            // You can also reset the form here
            // this.reset();
        });
    </script>
</body>
</html>
<?php } ?>