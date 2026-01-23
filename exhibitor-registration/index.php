<?php 
    session_start();

    require_once '../admin/assets/includes/config/config.php';
    require_once '../admin/assets/includes/functions/data_fetcher.php';
    require_once '../admin/assets/includes/functions/utility_functions.php';
    require_once '../admin/assets/includes/functions/user_auth.php';


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
    <title>New Exhibitor Registration - <?= htmlspecialchars(get_site_option('site_fullname')) ?> - <?= htmlspecialchars(get_site_option('site_tagline')) ?></title>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                            <a href="<?= get_site_option('site_url') ?>exhibitor-registration/" class="breadcrumb-link">
                                <span>Exhibitor Registration</span>
                            </a>
                        </li>
                    </ol>
                    <div class="gallery-hero-content">
                        <h1 class='gallery-hero-title'>Exhibitor Registration</h1>
                        <p class='gallery-hero-subtitle'>Join our network of exhibitors and showcase your products to a wider audience!</p>
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
                            <h2 class="registration-title">Create Your Exhibitor Account</h2>
                            <p class="registration-subtitle">Fill in your details to join our exhibitor network</p>
                        </div>

                        <form class="registration-form" id="vendorRegistrationForm" method="POST">
                            <!-- CSRF Token -->
                            <?= csrf_input_field(); ?>

                            <!-- Owner Name -->
                            <div class="form-group">
                                <label for="ownerName" class="form-label">
                                    <i class="fas fa-user"></i>
                                    Owner Full Name
                                    <span class="required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="ownerName" 
                                    name="owner_name"
                                    class="form-control" 
                                    placeholder="Enter your full name"
                                    required
                                >
                            </div>

                            <!-- Business Name -->
                            <div class="form-group">
                                <label for="businessName" class="form-label">
                                    <i class="fas fa-building"></i>
                                    Business Full Name
                                    <span class="required">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="businessName" 
                                    name="business_name"
                                    class="form-control" 
                                    placeholder="Enter your business name"
                                    required
                                >
                            </div>

                            <!-- Phone Number with Country Code -->
                            <div class="form-group">
                                <label for="phoneNumber" class="form-label">
                                    <i class="fas fa-phone"></i>
                                    Phone Number
                                    <span class="required">*</span>
                                </label>
                                <div class="phone-input-group">
                                    <select id="countryCode" name="country_code" class="country-code-select" required>
                                        <option value="+91" selected>🇮🇳 +91</option>
                                        <option value="+1">🇺🇸 +1</option>
                                        <option value="+44">🇬🇧 +44</option>
                                        <option value="+971">🇦🇪 +971</option>
                                        <option value="+61">🇦🇺 +61</option>
                                        <option value="+86">🇨🇳 +86</option>
                                        <option value="+33">🇫🇷 +33</option>
                                        <option value="+49">🇩🇪 +49</option>
                                        <option value="+81">🇯🇵 +81</option>
                                        <option value="+65">🇸🇬 +65</option>
                                    </select>
                                    <input 
                                        type="tel" 
                                        id="phoneNumber" 
                                        name="phone_number"
                                        class="form-control phone-number-input" 
                                        placeholder="Enter phone number"
                                        pattern="[0-9]{10}"
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label for="emailAddress" class="form-label">
                                    <i class="fas fa-envelope"></i>
                                    Email Address
                                    <span class="required">*</span>
                                </label>
                                <div class="email-validation-wrapper">
                                    <input 
                                        type="email" 
                                        id="emailAddress" 
                                        name="email"
                                        class="form-control" 
                                        placeholder="your.email@example.com"
                                        required
                                    >
                                    <span id="emailValidationIcon" class="email-validation-icon"></span>
                                </div>
                                <small id="emailValidationMessage" class="email-validation-message"></small>
                            </div>

                            <!-- Password -->
                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Password
                                    <span class="required">*</span>
                                </label>
                                <div class="password-input-wrapper">
                                    <i class="fas fa-info-circle password-info-icon" id="passwordInfoIcon" role="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Minimum 8 chars: uppercase, lowercase, numbers & special characters"></i>
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password"
                                        class="form-control" 
                                        placeholder="Create a strong password"
                                        minlength="8"
                                        required
                                    >
                                    <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-group">
                                <label for="confirmPassword" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Confirm Password
                                    <span class="required">*</span>
                                </label>
                                <div class="password-input-wrapper">
                                    <input 
                                        type="password" 
                                        id="confirmPassword" 
                                        name="confirm_password"
                                        class="form-control" 
                                        placeholder="Re-enter your password"
                                        minlength="8"
                                        required
                                    >
                                    <span id="passwordMatchIcon" class="password-match-icon"></span>
                                    <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small id="passwordMismatchMessage" class="password-mismatch-message"></small>
                            </div>

                            <!-- Terms & Conditions -->
                            <div class="form-group">
                                <div class="terms-checkbox">
                                    <input 
                                        type="checkbox" 
                                        id="agreeTerms" 
                                        name="agree_terms"
                                        class="checkbox-input"
                                        required
                                    >
                                    <label for="agreeTerms" class="checkbox-label">
                                        I agree to the 
                                        <a href="<?= get_site_option('site_url') ?>policies/terms-and-conditions/" target="_blank" class="policy-link">Terms & Conditions</a> 
                                        and 
                                        <a href="<?= get_site_option('site_url') ?>policies/privacy-policy/" target="_blank" class="policy-link">Privacy Policy</a>
                                        <span class="required">*</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn-submit-registration">
                                <i class="fas fa-user-plus"></i>
                                Create Account
                            </button>

                            <!-- Already have account -->
                            <div class="form-footer">
                                <p class="login-prompt">
                                    Already have an account? 
                                    <a href="<?= get_site_option('site_url') ?>exhibitor-login/" class="login-link">Login here</a>
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
    <script src="../assets/js/vendor-registration.js"></script>

    <!-- Email Validation Script -->
    <script>
        // Initialize password info tooltip with click trigger
        const passwordInfoIcon = document.getElementById('passwordInfoIcon');
        const passwordTooltip = new bootstrap.Tooltip(passwordInfoIcon, {
            trigger: 'click'
        });

        // Close tooltip when clicking elsewhere
        document.addEventListener('click', function(event) {
            if (event.target !== passwordInfoIcon && !passwordInfoIcon.contains(event.target)) {
                passwordTooltip.hide();
            }
        });

        // Real-time password matching
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirmPassword');
        const passwordMatchIcon = document.getElementById('passwordMatchIcon');

        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const passwordMismatchMessage = document.getElementById('passwordMismatchMessage');

            if (!confirmPassword) {
                passwordMatchIcon.innerHTML = '';
                passwordMismatchMessage.className = 'password-mismatch-message';
                passwordMismatchMessage.textContent = '';
                return;
            }

            if (password === confirmPassword && password.length >= 8) {
                passwordMatchIcon.innerHTML = '<i class="fas fa-check"></i>';
                passwordMatchIcon.className = 'password-match-icon match';
                passwordMismatchMessage.className = 'password-mismatch-message';
                passwordMismatchMessage.textContent = '';
            } else if (confirmPassword.length > 0) {
                passwordMatchIcon.innerHTML = '<i class="fas fa-times"></i>';
                passwordMatchIcon.className = 'password-match-icon mismatch';
                passwordMismatchMessage.className = 'password-mismatch-message show';
                passwordMismatchMessage.textContent = 'Passwords do not match';
            }
        }

        passwordInput.addEventListener('input', checkPasswordMatch);
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);

        const emailInput = document.getElementById('emailAddress');
        const emailValidationIcon = document.getElementById('emailValidationIcon');
        const emailValidationMessage = document.getElementById('emailValidationMessage');
        let emailCheckTimeout;

        function isValidEmailFormat(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function updateEmailValidationIcon(status) {
            emailValidationIcon.className = 'email-validation-icon';
            emailValidationMessage.className = 'email-validation-message';
            
            if (status === 'valid') {
                emailValidationIcon.innerHTML = '<i class="fas fa-check"></i>';
                emailValidationIcon.classList.add('valid');
                emailValidationMessage.textContent = '';
            } else if (status === 'exists') {
                emailValidationIcon.innerHTML = '<i class="fas fa-exclamation-circle"></i>';
                emailValidationIcon.classList.add('exists');
                emailValidationMessage.textContent = 'Similar Email Already Exists, use some other email';
                emailValidationMessage.classList.add('show');
            } else if (status === 'invalid') {
                emailValidationIcon.innerHTML = '<i class="fas fa-times"></i>';
                emailValidationIcon.classList.add('invalid');
                emailValidationMessage.textContent = '';
            } else if (status === 'checking') {
                emailValidationIcon.innerHTML = '<i class="fas fa-spinner"></i>';
                emailValidationIcon.classList.add('checking');
                emailValidationMessage.textContent = '';
            }
        }

        async function checkEmailInDatabase(email) {
            try {
                const response = await fetch('../admin/assets/includes/functions/ajax-handlers/exhibitor-email-check.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ email: email })
                });

                const data = await response.json();
                return data.exists ? 'exists' : 'valid';
            } catch (error) {
                console.error('Error checking email:', error);
                return null;
            }
        }

        emailInput.addEventListener('input', function() {
            const email = this.value.trim();

            clearTimeout(emailCheckTimeout);

            if (!email) {
                emailValidationIcon.innerHTML = '';
                emailValidationMessage.className = 'email-validation-message';
                emailValidationMessage.textContent = '';
                return;
            }

            if (!isValidEmailFormat(email)) {
                updateEmailValidationIcon('invalid');
                return;
            }

            updateEmailValidationIcon('checking');

            emailCheckTimeout = setTimeout(async () => {
                const status = await checkEmailInDatabase(email);
                if (status) {
                    updateEmailValidationIcon(status);
                }
            }, 500);
        });
    </script>

    <!-- Form Registration Handler -->
    <script>
        const registrationForm = document.getElementById('vendorRegistrationForm');

        if (registrationForm) {
            registrationForm.onsubmit = function(e) {
                e.preventDefault();

                const submitBtn = document.querySelector('.btn-submit-registration');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';

                const exhibitorName = document.getElementById('ownerName').value.trim();
                const businessName = document.getElementById('businessName').value.trim();
                const countryCode = document.getElementById('countryCode').value.trim();
                const phoneNumber = document.getElementById('phoneNumber').value.trim();
                const email = document.getElementById('emailAddress').value.trim().toLowerCase();
                const password = document.getElementById('password').value;
                const agreeTerms = document.getElementById('agreeTerms').checked ? 1 : 0;
                const csrfToken = document.querySelector('input[name="csrf_token"]').value;

                if (!exhibitorName || !businessName || !countryCode || !phoneNumber || !email || !password || !agreeTerms || !csrfToken) {
                    showErrorAlert('Please fill in all required fields and agree to the terms.', 'Incomplete Form');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
                    return;
                }

                const formData = new FormData();

                formData.append('owner_name', exhibitorName);
                formData.append('business_name', businessName);
                formData.append('country_code', countryCode);
                formData.append('phone_number', phoneNumber);
                formData.append('email', email);
                formData.append('password', password);
                formData.append('agree_terms', agreeTerms);
                formData.append('csrf_token', csrfToken);

                fetch('../admin/assets/includes/functions/ajax-handlers/register-vendor.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccessAlert('Your exhibitor account has been created successfully! You can now log in using your email and password.', 'Registration Successful');
                    } else {
                        showErrorAlert(data.message || 'An error occurred while creating your account. Please try again.', 'Registration Failed');
                    }
                })
                .catch(error => {
                    showErrorAlert('An unexpected error occurred. Please try again later.', 'Registration Error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
                });
            };
        }

        // Custom Sweetalert functions
        function showSuccessAlert(message, title = 'Success!') {
            Swal.fire({
                icon: 'success',
                title: title,
                html: message,
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'Continue',
                didClose: () => {
                    window.location.href = '<?= get_site_option('site_url') ?>exhibitor-login/';
                }
            });
        }

        function showErrorAlert(message, title = 'Error!') {
            Swal.fire({
                icon: 'error',
                title: title,
                html: message,
                confirmButtonText: 'Try Again'
            });
        }

        function showWarningAlert(message, title = 'Warning!') {
            Swal.fire({
                icon: 'warning',
                title: title,
                html: message,
                confirmButtonText: 'OK'
            });
        }

        function showLoadingAlert() {
            Swal.fire({
                icon: 'info',
                title: 'Processing...',
                html: '<i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #F3D02D;"></i><p style="margin-top: 12px;">Creating your account, please wait...</p>',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // Form Validation
        function validateForm() {
            const ownerName = document.getElementById('ownerName').value.trim();
            const businessName = document.getElementById('businessName').value.trim();
            const countryCode = document.getElementById('countryCode').value.trim();
            const phoneNumber = document.getElementById('phoneNumber').value.trim();
            const email = document.getElementById('emailAddress').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const agreeTerms = document.getElementById('agreeTerms').checked;
            const emailValidationIcon = document.getElementById('emailValidationIcon');

            // Check owner name
            if (!ownerName || ownerName.length < 2) {
                showWarningAlert('Please enter a valid owner name (minimum 2 characters)', 'Invalid Owner Name');
                return false;
            }

            // Check business name
            if (!businessName || businessName.length < 3) {
                showWarningAlert('Please enter a valid business name (minimum 3 characters)', 'Invalid Business Name');
                return false;
            }

            // Check phone number
            if (!phoneNumber || phoneNumber.length !== 10 || !/^\d+$/.test(phoneNumber)) {
                showWarningAlert('Please enter a valid 10-digit phone number', 'Invalid Phone Number');
                return false;
            }

            // Check email
            if (!email || !isValidEmailFormat(email)) {
                showWarningAlert('Please enter a valid email address', 'Invalid Email');
                return false;
            }

            // Check if email exists (check icon status)
            if (emailValidationIcon.classList.contains('exists')) {
                showWarningAlert('This email is already registered. Please use a different email address', 'Email Already Exists');
                return false;
            }

            // Check password
            if (password.length < 8) {
                showWarningAlert('Password must be at least 8 characters long', 'Weak Password');
                return false;
            }

            // Check password strength
            const passwordStrength = checkPasswordStrength(password);
            if (passwordStrength.score < 2) {
                showWarningAlert('Password is too weak. Please use a combination of uppercase, lowercase, numbers, and special characters', 'Weak Password');
                return false;
            }

            // Check password match
            if (password !== confirmPassword) {
                showWarningAlert('Passwords do not match. Please try again', 'Password Mismatch');
                return false;
            }

            // Check terms agreement
            if (!agreeTerms) {
                showWarningAlert('Please agree to the Terms & Conditions and Privacy Policy to continue', 'Terms Not Accepted');
                return false;
            }

            return true;
        }

        function checkPasswordStrength(password) {
            let score = 0;
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/\d/.test(password)) score++;
            if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) score++;
            
            return { score: score, password: password };
        }

        function isValidEmailFormat(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // Form Submission
        registrationForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.querySelector('.btn-submit-registration');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';

            // Validate form
            if (!validateForm()) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
                return;
            }

            // Show loading alert
            showLoadingAlert();

            const exhibitorName = document.getElementById('ownerName').value.trim();
            const businessName = document.getElementById('businessName').value.trim();
            const countryCode = document.getElementById('countryCode').value.trim();
            const phoneNumber = document.getElementById('phoneNumber').value.trim();
            const email = document.getElementById('emailAddress').value.trim().toLowerCase();
            const password = document.getElementById('password').value;
            const agreeTerms = document.getElementById('agreeTerms').checked ? 1 : 0;
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            if (!exhibitorName || !businessName || !countryCode || !phoneNumber || !email || !password || !agreeTerms || !csrfToken) {
                showErrorAlert('Please fill in all required fields and agree to the terms.', 'Incomplete Form');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
                return;
            }

            const formData = new FormData();

            formData.append('owner_name', exhibitorName);
            formData.append('business_name', businessName);
            formData.append('country_code', countryCode);
            formData.append('phone_number', phoneNumber);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('agree_terms', agreeTerms);
            formData.append('csrf_token', csrfToken);

            fetch('../admin/assets/includes/functions/ajax-handlers/register-vendor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessAlert('Your exhibitor account has been created successfully! You can now log in using your email and password.', 'Registration Successful');
                } else {
                    showErrorAlert(data.message || 'An error occurred while creating your account. Please try again.', 'Registration Failed');
                }
            })
            .catch(error => {
                showErrorAlert('An unexpected error occurred. Please try again later.', 'Registration Error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
            });
        });
    </script>
    <?php require_once '../admin/assets/elements/user-side/whatsapp_balloon.php'; ?>
</body>
</html>
<?php } ?>