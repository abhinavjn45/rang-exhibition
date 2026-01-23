<?php
/**
 * Instagram Feed Fetcher
 * Fetches Instagram posts (excludes reels) using Instagram Basic Display API
 */

class InstagramFetcher {
    private $accessToken;
    private $userId;
    private $cacheFile;
    private $cacheTime = 3600; // Cache for 1 hour
    
    public function __construct() {
        // TODO: Set your Instagram Access Token here
        // Get it from: https://developers.facebook.com/apps/
        $this->accessToken = 'YOUR_INSTAGRAM_ACCESS_TOKEN';
        $this->userId = 'YOUR_INSTAGRAM_USER_ID';
        $this->cacheFile = __DIR__ . '/../../cache/instagram_feed.json';
    }
    
    /**
     * Get Instagram posts (excludes reels)
     * @param int $limit Number of posts to fetch
     * @return array Posts data
     */
    public function getPosts($limit = 12) {
        // Check cache first
        if ($this->isCacheValid()) {
            return $this->getCachedPosts($limit);
        }
        
        // Fetch fresh data from Instagram
        $posts = $this->fetchFromInstagram($limit);
        
        if ($posts) {
            $this->cacheData($posts);
        }
        
        return $posts;
    }
    
    /**
     * Fetch posts from Instagram API
     */
    private function fetchFromInstagram($limit) {
        $fields = 'id,caption,media_type,media_url,permalink,thumbnail_url,timestamp,like_count,comments_count';
        $url = "https://graph.instagram.com/me/media?fields={$fields}&access_token={$this->accessToken}&limit=50";
        
        try {
            $response = @file_get_contents($url);
            
            if ($response === false) {
                error_log("Instagram API Error: Unable to fetch data");
                return [];
            }
            
            $data = json_decode($response, true);
            
            if (!isset($data['data'])) {
                error_log("Instagram API Error: Invalid response format");
                return [];
            }
            
            // Filter out reels and videos, keep only images
            $posts = array_filter($data['data'], function($post) {
                return $post['media_type'] === 'IMAGE' || $post['media_type'] === 'CAROUSEL_ALBUM';
            });
            
            // Limit results
            $posts = array_slice(array_values($posts), 0, $limit);
            
            return $posts;
            
        } catch (Exception $e) {
            error_log("Instagram Fetch Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check if cache is valid
     */
    private function isCacheValid() {
        if (!file_exists($this->cacheFile)) {
            return false;
        }
        
        $cacheAge = time() - filemtime($this->cacheFile);
        return $cacheAge < $this->cacheTime;
    }
    
    /**
     * Get cached posts
     */
    private function getCachedPosts($limit) {
        $cachedData = @file_get_contents($this->cacheFile);
        
        if ($cachedData === false) {
            return [];
        }
        
        $posts = json_decode($cachedData, true);
        return array_slice($posts, 0, $limit);
    }
    
    /**
     * Cache the data
     */
    private function cacheData($data) {
        $cacheDir = dirname($this->cacheFile);
        
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        file_put_contents($this->cacheFile, json_encode($data));
    }
    
    /**
     * Format number for display (e.g., 1.5K, 2.3M)
     */
    public static function formatNumber($num) {
        if ($num >= 1000000) {
            return round($num / 1000000, 1) . 'M';
        } elseif ($num >= 1000) {
            return round($num / 1000, 1) . 'K';
        }
        return $num;
    }
}
