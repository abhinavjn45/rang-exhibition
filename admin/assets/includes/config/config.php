<?php 
    // Detect environment from host to switch DB credentials automatically
    $currentHost = $_SERVER['HTTP_HOST'] ?? '';
    $isProduction = stripos($currentHost, 'rangexhibition.com') !== false;

    if ($isProduction) {
        define('DB_HOST', 'localhost');
        define('DB_USER', 'u955229223_rang');
        define('DB_PASS', 'Rang@2025');
        define('DB_NAME', 'u955229223_rang');
    } else {
        define('DB_HOST', 'localhost');
        define('DB_USER', 'root');
        define('DB_PASS', '');
        define('DB_NAME', 'rang');
    }

    $con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if (!$con) {
        header("Location: " . get_site_option('site_url') . "error/500/");
        exit();
    }

    require_once __DIR__ . '/../functions/data_fetcher.php';

    // Set the default timezone
    date_default_timezone_set(get_site_option('timezone') ?: 'UTC');
?>