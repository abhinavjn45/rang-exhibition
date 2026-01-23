<?php
/**
 * Instagram API Endpoint
 * Returns Instagram posts as JSON
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../includes/functions/instagram_fetcher.php';

try {
    $instagram = new InstagramFetcher();
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 12;
    
    $posts = $instagram->getPosts($limit);
    
    // Format response
    $response = [
        'success' => true,
        'count' => count($posts),
        'posts' => array_map(function($post) {
            return [
                'id' => $post['id'],
                'image' => $post['media_url'] ?? $post['thumbnail_url'] ?? '',
                'caption' => isset($post['caption']) ? mb_substr($post['caption'], 0, 150) : '',
                'permalink' => $post['permalink'] ?? '#',
                'likes' => InstagramFetcher::formatNumber($post['like_count'] ?? 0),
                'comments' => InstagramFetcher::formatNumber($post['comments_count'] ?? 0),
                'timestamp' => $post['timestamp'] ?? ''
            ];
        }, $posts)
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch Instagram posts',
        'message' => $e->getMessage()
    ]);
}
