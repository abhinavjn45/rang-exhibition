<?php 
    session_start();

    require_once '../../config/config.php';
    require_once '../utility_functions.php';
    require_once '../data_fetcher.php';
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit();
    }

    // Verify CSRF token
    $csrf_token = $_POST['csrf_token'] ?? null;
    if (!verify_csrf_token($csrf_token)) {
        echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh and try again.']);
        exit();
    }

    $ownwer_name = ucwords(strtolower(isset($_POST['owner_name']) ? trim($_POST['owner_name']) : ''));
    $business_name = isset($_POST['business_name']) ? trim($_POST['business_name']) : '';
    $country_code = isset($_POST['country_code']) ? trim($_POST['country_code']) : '';
    $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
    $email = strtolower(isset($_POST['email']) ? trim($_POST['email']) : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $agree_terms = isset($_POST['agree_terms']) ? (int)$_POST['agree_terms'] : 0;

    // Format phone number with space in middle (works for any length)
    $phone_length = strlen($phone_number);
    $mid_point = (int)($phone_length / 2);
    $formatted_phone = substr($phone_number, 0, $mid_point) . ' ' . substr($phone_number, $mid_point);
    $member_phone_number_data = $country_code . " " . $formatted_phone;

    if (empty($ownwer_name) || empty($business_name) || empty($country_code) || empty($phone_number) || empty($email) || empty($password) || $agree_terms !== 1) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be provided.']);
        exit();
    }

    try {
        // Existing Exhibitor
        $exhistingExhibitor = get_single_vendor_details(vendor_email: $email);
        if ($exhistingExhibitor) {
            echo json_encode(['success' => false, 'message' => 'An account with this email already exists. Please use a different email.']);
            exit();
        }

        // Register New Exhibitor
        global $con;

    } catch (\Throwable $th) {
        //throw $th;
    }
?>