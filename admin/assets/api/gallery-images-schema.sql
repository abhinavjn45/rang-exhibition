-- SQL Schema for Gallery Images Table
-- This table stores metadata for images uploaded to Google Drive

CREATE TABLE IF NOT EXISTS `gallery_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT 'User-provided title for the image',
  `drive_file_id` varchar(255) NOT NULL COMMENT 'Google Drive file ID',
  `drive_file_name` varchar(255) NOT NULL COMMENT 'File name in Google Drive',
  `mime_type` varchar(100) DEFAULT 'image/jpeg' COMMENT 'Image MIME type',
  `original_file_name` varchar(255) DEFAULT NULL COMMENT 'Original file name before upload',
  `file_size` bigint(20) DEFAULT 0 COMMENT 'File size in bytes',
  `upload_date` datetime DEFAULT NULL COMMENT 'Date when file was uploaded to Drive',
  `status` enum('active','inactive','deleted') DEFAULT 'active' COMMENT 'Image status',
  `display_order` int(11) DEFAULT 0 COMMENT 'Display order for gallery',
  `category` varchar(100) DEFAULT NULL COMMENT 'Optional category for organizing images',
  `tags` text DEFAULT NULL COMMENT 'Comma-separated tags',
  `description` text DEFAULT NULL COMMENT 'Optional description',
  `view_count` int(11) DEFAULT 0 COMMENT 'Number of times image was viewed',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drive_file_id` (`drive_file_id`),
  KEY `status` (`status`),
  KEY `upload_date` (`upload_date`),
  KEY `display_order` (`display_order`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Gallery images stored in Google Drive';

-- Index for faster queries
CREATE INDEX idx_gallery_search ON gallery_images(status, upload_date DESC);
CREATE INDEX idx_gallery_display ON gallery_images(status, display_order, created_at DESC);
