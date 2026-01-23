/**
 * Vendor Registration Form Handler
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('vendorRegistrationForm');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirmPassword');
    const passwordToggles = document.querySelectorAll('.password-toggle');

    // Password visibility toggle
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Form validation on submit
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Check if passwords match
            if (password.value !== confirmPassword.value) {
                alert('Passwords do not match. Please try again.');
                confirmPassword.focus();
                return false;
            }

            // Check password length
            if (password.value.length < 8) {
                alert('Password must be at least 8 characters long.');
                password.focus();
                return false;
            }

            // Check terms agreement
            const agreeTerms = document.getElementById('agreeTerms');
            if (!agreeTerms.checked) {
                alert('Please agree to the Terms & Conditions and Privacy Policy.');
                agreeTerms.focus();
                return false;
            }

            // If all validations pass, submit the form
            alert('Registration form submitted! (Backend integration pending)');
            // form.submit(); // Uncomment when backend is ready
        });

        // Real-time password match validation
        confirmPassword.addEventListener('input', function() {
            if (this.value && password.value !== this.value) {
                this.setCustomValidity('Passwords do not match');
                this.style.borderColor = '#e74c3c';
            } else {
                this.setCustomValidity('');
                this.style.borderColor = '';
            }
        });
    }
});
