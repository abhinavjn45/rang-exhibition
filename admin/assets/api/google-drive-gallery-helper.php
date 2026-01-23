<?php
/**
 * Google Drive Gallery Helper
 * Functions to retrieve and display images from Google Drive
 */

class GoogleDriveGallery {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    /**
     * Get all gallery images
     * @param array $options Filter options (status, category, limit, offset)
     * @return array Array of images
     */
    public function getImages($options = []) {
        $status = $options['status'] ?? 'active';
        $category = $options['category'] ?? null;
        $limit = $options['limit'] ?? 100;
        $offset = $options['offset'] ?? 0;
        $orderBy = $options['orderBy'] ?? 'display_order, created_at DESC';
        
        $sql = "SELECT * FROM gallery_images WHERE status = ?";
        $params = [$status];
        $types = 's';
        
        if ($category) {
            $sql .= " AND category = ?";
            $params[] = $category;
            $types .= 's';
        }
        
        $sql .= " ORDER BY $orderBy LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $images = [];
        
        while ($row = $result->fetch_assoc()) {
            $images[] = $this->formatImage($row);
        }
        
        $stmt->close();
        return $images;
    }
    
    /**
     * Get single image by ID
     * @param int $id Image ID
     * @return array|null Image data or null
     */
    public function getImageById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM gallery_images WHERE id = ? AND status = 'active'");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $image = $result->fetch_assoc();
        $stmt->close();
        
        return $image ? $this->formatImage($image) : null;
    }
    
    /**
     * Get image by Drive File ID
     * @param string $driveFileId Google Drive File ID
     * @return array|null Image data or null
     */
    public function getImageByDriveId($driveFileId) {
        $stmt = $this->conn->prepare("SELECT * FROM gallery_images WHERE drive_file_id = ? AND status = 'active'");
        $stmt->bind_param('s', $driveFileId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $image = $result->fetch_assoc();
        $stmt->close();
        
        return $image ? $this->formatImage($image) : null;
    }
    
    /**
     * Format image data and add Google Drive URLs
     * @param array $image Raw image data from database
     * @return array Formatted image data
     */
    private function formatImage($image) {
        $driveFileId = $image['drive_file_id'];
        
        // Google Drive URLs
        $image['drive_view_url'] = "https://drive.google.com/file/d/{$driveFileId}/view";
        $image['drive_download_url'] = "https://drive.google.com/uc?export=download&id={$driveFileId}";
        $image['drive_thumbnail_url'] = "https://drive.google.com/thumbnail?id={$driveFileId}&sz=w400";
        $image['drive_preview_url'] = "https://drive.google.com/uc?export=view&id={$driveFileId}";
        
        // Parse tags if exists
        if (!empty($image['tags'])) {
            $image['tags_array'] = array_map('trim', explode(',', $image['tags']));
        } else {
            $image['tags_array'] = [];
        }
        
        return $image;
    }
    
    /**
     * Update image details
     * @param int $id Image ID
     * @param array $data Data to update
     * @return bool Success status
     */
    public function updateImage($id, $data) {
        $allowedFields = ['title', 'category', 'tags', 'description', 'display_order', 'status'];
        $updates = [];
        $params = [];
        $types = '';
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = ?";
                $params[] = $data[$field];
                $types .= 's';
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql = "UPDATE gallery_images SET " . implode(', ', $updates) . " WHERE id = ?";
        $params[] = $id;
        $types .= 'i';
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }
    
    /**
     * Delete image (soft delete)
     * @param int $id Image ID
     * @return bool Success status
     */
    public function deleteImage($id) {
        $stmt = $this->conn->prepare("UPDATE gallery_images SET status = 'deleted' WHERE id = ?");
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }
    
    /**
     * Increment view count
     * @param int $id Image ID
     */
    public function incrementViewCount($id) {
        $stmt = $this->conn->prepare("UPDATE gallery_images SET view_count = view_count + 1 WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Get gallery statistics
     * @return array Statistics
     */
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total_images,
                    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_images,
                    SUM(file_size) as total_size,
                    SUM(view_count) as total_views
                FROM gallery_images";
        
        $result = $this->conn->query($sql);
        $stats = $result->fetch_assoc();
        
        // Format file size
        $stats['total_size_formatted'] = $this->formatFileSize($stats['total_size'] ?? 0);
        
        return $stats;
    }
    
    /**
     * Format file size
     * @param int $bytes File size in bytes
     * @return string Formatted size
     */
    private function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Search images
     * @param string $query Search query
     * @param array $options Additional options
     * @return array Array of images
     */
    public function searchImages($query, $options = []) {
        $limit = $options['limit'] ?? 50;
        $offset = $options['offset'] ?? 0;
        
        $sql = "SELECT * FROM gallery_images 
                WHERE status = 'active' 
                AND (title LIKE ? OR description LIKE ? OR tags LIKE ?)
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";
        
        $searchTerm = "%{$query}%";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sssii', $searchTerm, $searchTerm, $searchTerm, $limit, $offset);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $images = [];
        
        while ($row = $result->fetch_assoc()) {
            $images[] = $this->formatImage($row);
        }
        
        $stmt->close();
        return $images;
    }
}

// Example usage:
/*
require_once 'includes/config/config.php';
$gallery = new GoogleDriveGallery($conn);

// Get all images
$images = $gallery->getImages(['limit' => 20]);

// Get images by category
$categoryImages = $gallery->getImages(['category' => 'events', 'limit' => 10]);

// Search images
$searchResults = $gallery->searchImages('wedding');

// Get single image
$image = $gallery->getImageById(1);

// Update image
$gallery->updateImage(1, [
    'title' => 'Updated Title',
    'category' => 'venues',
    'tags' => 'wedding, outdoor, garden'
]);

// Delete image
$gallery->deleteImage(1);
*/
?>
