<?php
/**
 * Save Gallery Images Metadata to Database
 * This file receives image metadata from Google Drive uploads and saves it to the database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Include database configuration
require_once '../includes/config/config.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($data['images']) || !is_array($data['images'])) {
        throw new Exception('Invalid input data');
    }

    $images = $data['images'];
    
    if (empty($images)) {
        throw new Exception('No images to save');
    }

    // Start transaction
    $conn->begin_transaction();

    $insertedCount = 0;
    $errors = [];

    // Prepare insert statement
    $stmt = $conn->prepare("
        INSERT INTO gallery_images 
        (title, drive_file_id, drive_file_name, mime_type, original_file_name, file_size, upload_date, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())
    ");

    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    // Insert each image
    foreach ($images as $image) {
        try {
            // Validate required fields
            if (empty($image['title']) || empty($image['driveFileId'])) {
                $errors[] = "Missing required fields for image: " . ($image['originalFileName'] ?? 'unknown');
                continue;
            }

            $title = trim($image['title']);
            $driveFileId = trim($image['driveFileId']);
            $driveFileName = $image['driveFileName'] ?? $image['originalFileName'];
            $mimeType = $image['mimeType'] ?? 'image/jpeg';
            $originalFileName = $image['originalFileName'] ?? $driveFileName;
            $fileSize = intval($image['fileSize'] ?? 0);
            $uploadDate = $image['uploadDate'] ?? date('Y-m-d H:i:s');

            // Bind parameters and execute
            $stmt->bind_param(
                'sssssss',
                $title,
                $driveFileId,
                $driveFileName,
                $mimeType,
                $originalFileName,
                $fileSize,
                $uploadDate
            );

            if ($stmt->execute()) {
                $insertedCount++;
            } else {
                $errors[] = "Failed to insert: " . $originalFileName . " - " . $stmt->error;
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }

    $stmt->close();

    // Commit transaction if at least one image was inserted
    if ($insertedCount > 0) {
        $conn->commit();
        
        $response = [
            'success' => true,
            'message' => "Successfully saved $insertedCount image(s) to database",
            'inserted_count' => $insertedCount,
            'total_count' => count($images)
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
            $response['message'] .= ' with some errors';
        }

        echo json_encode($response);
    } else {
        $conn->rollback();
        throw new Exception('No images were inserted. Errors: ' . implode(', ', $errors));
    }

} catch (Exception $e) {
    // Rollback on error
    if (isset($conn) && $conn->connect_errno === 0) {
        $conn->rollback();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'errors' => $errors ?? []
    ]);
} finally {
    // Close connection
    if (isset($conn)) {
        $conn->close();
    }
}
?>
