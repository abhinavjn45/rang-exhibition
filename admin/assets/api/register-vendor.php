<?php
session_start();

header('Content-Type: application/json');

require_once '../includes/config/config.php';
require_once '../includes/functions/utility_functions.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Validate CSRF Token
$csrf_token = isset($input['csrf_token']) ? $input['csrf_token'] : '';
if (!verify_csrf_token($csrf_token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Security validation failed. Please try again.']);
    exit();
}

// Validate and sanitize input
$owner_name = isset($input['owner_name']) ? trim($input['owner_name']) : '';
$business_name = isset($input['business_name']) ? trim($input['business_name']) : '';
$country_code = isset($input['country_code']) ? trim($input['country_code']) : '';
$phone_number = isset($input['phone_number']) ? trim($input['phone_number']) : '';
$email = isset($input['email']) ? strtolower(trim($input['email'])) : '';
$password = isset($input['password']) ? $input['password'] : '';
$agree_terms = isset($input['agree_terms']) ? (int)$input['agree_terms'] : 0;

// Validation errors
$errors = [];

// Validate owner name
if (empty($owner_name)) {
    $errors[] = 'Owner name is required';
} elseif (strlen($owner_name) < 2) {
    $errors[] = 'Owner name must be at least 2 characters';
} elseif (strlen($owner_name) > 100) {
    $errors[] = 'Owner name must not exceed 100 characters';
} elseif (!preg_match('/^[a-zA-Z\s\.\-\']+$/i', $owner_name)) {
    $errors[] = 'Owner name contains invalid characters';
}

// Validate business name
if (empty($business_name)) {
    $errors[] = 'Business name is required';
} elseif (strlen($business_name) < 3) {
    $errors[] = 'Business name must be at least 3 characters';
} elseif (strlen($business_name) > 200) {
    $errors[] = 'Business name must not exceed 200 characters';
}

// Validate phone number
if (empty($phone_number)) {
    $errors[] = 'Phone number is required';
} elseif (!preg_match('/^\d{10}$/', $phone_number)) {
    $errors[] = 'Phone number must be exactly 10 digits';
}

// Validate country code
if (empty($country_code)) {
    $errors[] = 'Country code is required';
} elseif (!preg_match('/^\+\d{1,3}$/', $country_code)) {
    $errors[] = 'Invalid country code format';
}

// Validate email
if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address';
} elseif (strlen($email) > 255) {
    $errors[] = 'Email must not exceed 255 characters';
}

// Validate password
if (empty($password)) {
    $errors[] = 'Password is required';
} elseif (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters';
} elseif (strlen($password) > 255) {
    $errors[] = 'Password must not exceed 255 characters';
}

// Check terms agreement
if (!$agree_terms) {
    $errors[] = 'You must agree to Terms & Conditions and Privacy Policy';
}

// If there are validation errors, return them
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => implode('<br>', $errors)
    ]);
    exit();
}

try {
    // Connect to database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }

    // Enable transactions
    $conn->begin_transaction();

    // Check if email already exists
    $stmt = $conn->prepare("SELECT vendor_id FROM vendors WHERE vendor_email = ? AND vendor_status != 'deleted'");
    if (!$stmt) {
        throw new Exception('Database prepare error');
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $conn->rollback();
        $stmt->close();
        $conn->close();
        
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'message' => 'This email is already registered. Please use a different email address or <a href="' . (defined('SITE_URL') ? SITE_URL : '/') . 'exhibitor-login/" style="color: #F3D02D; text-decoration: underline;">login here</a>'
        ]);
        exit();
    }

    $stmt->close();

    // Generate unique ID
    $vendor_unique_id = 'VND' . strtoupper(uniqid(mt_rand(10, 99), true));
    $vendor_unique_id = substr($vendor_unique_id, 0, 12);

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    // Create slug from business name
    $vendor_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $business_name), '-'));
    $vendor_slug = substr($vendor_slug, 0, 100);

    // Insert vendor into database
    $stmt = $conn->prepare(
        "INSERT INTO vendors (
            vendor_unique_id,
            vendor_business_fullname,
            vendor_slug,
            vendor_email,
            vendor_phone_number,
            vendor_password,
            vendor_status
        ) VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        throw new Exception('Database prepare error');
    }

    $phone_full = $country_code . $phone_number;
    $status = 'active';

    $stmt->bind_param(
        "sssssss",
        $vendor_unique_id,
        $business_name,
        $vendor_slug,
        $email,
        $phone_full,
        $hashed_password,
        $status
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to create account: ' . $stmt->error);
    }

    $vendor_id = $stmt->insert_id;
    $stmt->close();

    // Update business name with owner info if needed (optional - store as tagline)
    // You can add more fields update here if needed

    // Commit transaction
    $conn->commit();
    $conn->close();

    // Send success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Account created successfully!',
        'vendor_id' => $vendor_id,
        'vendor_unique_id' => $vendor_unique_id,
        'redirect_url' => (defined('SITE_URL') ? SITE_URL : '/') . 'exhibitor-login/'
    ]);

} catch (Exception $e) {
    // Rollback on error
    if (isset($conn) && $conn) {
        $conn->rollback();
    }

    error_log('Vendor Registration Error: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while creating your account. Please try again later or contact support.'
    ]);
}
?>
