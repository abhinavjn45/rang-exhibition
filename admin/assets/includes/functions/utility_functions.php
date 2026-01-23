<?php
    function ensure_session_started(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    function get_csrf_token(bool $regenerate = false): string
    {
        ensure_session_started();

        if ($regenerate || empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    function verify_csrf_token(?string $token, bool $rotateOnSuccess = false): bool
    {
        ensure_session_started();

        if (empty($_SESSION['csrf_token'])) {
            return false;
        }

        if ($token === null || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }

        if ($rotateOnSuccess) {
            get_csrf_token(true);
        }

        return true;
    }

    function csrf_input_field(): string
    {
        $token = htmlspecialchars(get_csrf_token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }




    // Function to generate a random password
    function generate_random_password($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+-=';
        $charactersLength = strlen($characters);
        $randomPassword = '';
        for ($i = 0; $i < $length; $i++) {
            $randomPassword .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomPassword;
    }

    function generate_otp($length = 6) {
        $digits = '0123456789';
        $digitsLength = strlen($digits);
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= $digits[rand(0, $digitsLength - 1)];
        }
        return $otp;
    }
?>