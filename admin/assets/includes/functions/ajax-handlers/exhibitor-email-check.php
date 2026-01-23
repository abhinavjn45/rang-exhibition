<?php
    header('Content-Type: application/json');

    require_once '../../config/config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $email = isset($input['email']) ? trim($input['email']) : '';

        if (!$email) {
            echo json_encode(['success' => false, 'error' => 'Email is required']);
            exit();
        }

        try {
            if ($con->connect_error) {
                echo json_encode(['success' => false, 'error' => 'Database connection failed']);
                exit();
            }

            $stmt = $con->prepare("SELECT vendor_id FROM vendors WHERE vendor_email = ? AND vendor_status != 'deleted'");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            $exists = $result->num_rows > 0;

            echo json_encode([
                'success' => true,
                'exists' => $exists,
                'email' => $email
            ]);

            $stmt->close();
            $con->close();
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Error checking email']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    }
?>
