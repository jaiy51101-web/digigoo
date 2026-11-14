<?php
/**
 * BlogMedia Model - Media library management
 * Handles file uploads, processing, and media organization
 */

class BlogMedia {
    private Database $db;
    const UPLOAD_DIR = 'uploads/blog/';
    const MAX_FILE_SIZE = 10485760; // 10MB
    const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    const ALLOWED_VIDEO_TYPES = ['video/mp4', 'video/webm', 'video/ogg'];
    const ALLOWED_DOCUMENT_TYPES = ['application/pdf'];

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Upload media file
     */
    public function uploadMedia(array $file, int $userId, ?int $postId = null): ?int {
        // Validate file
        if (!$this->validateFile($file)) {
            return null;
        }

        // Create upload directory if not exists
        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('media_') . '.' . $ext;
        $filepath = self::UPLOAD_DIR . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return null;
        }

        // Get file info
        $fileType = $this->getFileType($file['type']);
        $fileSize = filesize($filepath);

        // Get image dimensions if image
        $width = $height = null;
        if ($fileType === 'image') {
            list($width, $height) = getimagesize($filepath);
        }

        // Generate thumbnail if image
        $thumbnailPath = null;
        if ($fileType === 'image') {
            $thumbnailPath = $this->generateThumbnail($filepath);
        }

        // Convert to WebP if enabled
        $webpPath = null;
        if ($fileType === 'image' && getSetting('blog_enable_webp_conversion')) {
            $webpPath = $this->convertToWebP($filepath);
        }

        // Compress image if enabled
        if ($fileType === 'image' && getSetting('blog_enable_media_compression')) {
            $this->compressImage($filepath);
        }

        // Insert into database
        $this->db->insert('blog_media', [
            'post_id' => $postId,
            'user_id' => $userId,
            'filename' => $filename,
            'original_name' => $file['name'],
            'file_type' => $fileType,
            'mime_type' => $file['type'],
            'file_size' => $fileSize,
            'file_path' => $filepath,
            'thumbnail_path' => $thumbnailPath,
            'webp_path' => $webpPath,
            'width' => $width,
            'height' => $height,
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Validate uploaded file
     */
    private function validateFile(array $file): bool {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name']) || ($file['size'] ?? 0) > self::MAX_FILE_SIZE) {
            return false;
        }

        $allowedTypes = array_merge(
            self::ALLOWED_IMAGE_TYPES,
            self::ALLOWED_VIDEO_TYPES,
            self::ALLOWED_DOCUMENT_TYPES
        );

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = $finfo ? finfo_file($finfo, $file['tmp_name']) : false;
        if ($finfo) finfo_close($finfo);

        return is_string($mimeType) && in_array($mimeType, $allowedTypes, true);
    }

    /**
     * Determine file type
     */
    private function getFileType(string $mimeType): string {
        if (in_array($mimeType, self::ALLOWED_IMAGE_TYPES)) return 'image';
        if (in_array($mimeType, self::ALLOWED_VIDEO_TYPES)) return 'video';
        if (in_array($mimeType, self::ALLOWED_DOCUMENT_TYPES)) return 'document';
        return 'file';
    }

    /**
     * Generate thumbnail for image
     */
    private function generateThumbnail(string $filepath): ?string {
        if (!extension_loaded('gd')) return null;
        try {
            $thumbDir = dirname($filepath) . '/thumbs/';
            if (!is_dir($thumbDir)) {
                mkdir($thumbDir, 0755, true);
            }

            $thumbPath = $thumbDir . basename($filepath);
            
            // Simple image resize using GD (requires GD extension)
            $image = imagecreatefromstring(file_get_contents($filepath));
            if (!$image) return null;

            $width = imagesx($image);
            $height = imagesy($image);
            
            // Create thumbnail (300x300)
            $thumbWidth = 300;
            $thumbHeight = 300;
            $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
            
            imagecopyresampled($thumb, $image, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
            imagejpeg($thumb, $thumbPath, 90);
            
            imagedestroy($image);
            imagedestroy($thumb);

            return $thumbPath;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Convert image to WebP format
     */
    private function convertToWebP(string $filepath): ?string {
        if (!extension_loaded('gd') || !function_exists('imagewebp')) return null;
        try {
            $webpPath = str_replace(pathinfo($filepath, PATHINFO_EXTENSION), 'webp', $filepath);
            
            $image = imagecreatefromstring(file_get_contents($filepath));
            if (!$image) return null;

            imagewebp($image, $webpPath, 80);
            imagedestroy($image);

            return $webpPath;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Compress image
     */
    private function compressImage(string $filepath): bool {
        if (!extension_loaded('gd')) return false;
        try {
            $image = imagecreatefromstring(file_get_contents($filepath));
            if (!$image) return false;

            $ext = pathinfo($filepath, PATHINFO_EXTENSION);
            
            if ($ext === 'jpg' || $ext === 'jpeg') {
                imagejpeg($image, $filepath, 85);
            } elseif ($ext === 'png') {
                imagepng($image, $filepath, 8);
            }

            imagedestroy($image);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get media by ID
     */
    public function getMedia(int $mediaId): ?array {
        return $this->db->fetch(
            "SELECT * FROM blog_media WHERE id = ?",
            [$mediaId]
        );
    }

    /**
     * Get all media for a post
     */
    public function getPostMedia(int $postId): array {
        return $this->db->fetchAll(
            "SELECT * FROM blog_media WHERE post_id = ? ORDER BY upload_date DESC",
            [$postId]
        );
    }

    /**
     * Get all media library (paginated)
     */
    public function getLibrary(int $page = 1, int $perPage = 30, array $filters = []): array {
        $offset = ($page - 1) * $perPage;
        $where = "1=1";
        $params = [];

        if (!empty($filters['file_type'])) {
            $where .= " AND file_type = ?";
            $params[] = $filters['file_type'];
        }

        if (!empty($filters['search'])) {
            $where .= " AND (original_name LIKE ? OR alt_text LIKE ?)";
            $s = '%' . $filters['search'] . '%';
            $params[] = $s;
            $params[] = $s;
        }

        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM blog_media WHERE $where",
            $params
        );

        $media = $this->db->fetchAll(
            "SELECT * FROM blog_media WHERE $where ORDER BY upload_date DESC LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return [
            'media' => $media,
            'total' => $total,
            'page' => $page,
            'pages' => ceil($total / $perPage),
        ];
    }

    /**
     * Update media metadata
     */
    public function updateMedia(int $mediaId, array $data): bool {
        $allowed = ['alt_text', 'caption', 'description'];
        $updateData = array_intersect_key($data, array_flip($allowed));
        
        if (empty($updateData)) {
            return false;
        }

        return $this->db->update('blog_media', $updateData, ['id' => $mediaId]);
    }

    /**
     * Delete media
     */
    public function deleteMedia(int $mediaId): bool {
        $media = $this->getMedia($mediaId);
        if (!$media) return false;

        // Delete files
        if (file_exists($media['file_path'])) {
            unlink($media['file_path']);
        }
        if ($media['thumbnail_path'] && file_exists($media['thumbnail_path'])) {
            unlink($media['thumbnail_path']);
        }
        if ($media['webp_path'] && file_exists($media['webp_path'])) {
            unlink($media['webp_path']);
        }

        return $this->db->delete('blog_media', ['id' => $mediaId]);
    }

    /**
     * Generate auto ALT text (placeholder)
     */
    public function generateAltText(int $mediaId): string {
        $media = $this->getMedia($mediaId);
        if (!$media) return '';

        // Simple ALT text generation from filename
        $alt = str_replace(['-', '_'], ' ', pathinfo($media['original_name'], PATHINFO_FILENAME));
        return ucfirst($alt);
    }

    /**
     * Set featured image
     */
    public function setFeaturedImage(int $mediaId, int $postId): bool {
        // Remove old featured image
        $this->db->update('blog_media', ['is_featured' => 0], ['post_id' => $postId, 'is_featured' => 1]);
        
        // Set new featured image
        return $this->db->update('blog_media', ['is_featured' => 1], ['id' => $mediaId, 'post_id' => $postId]);
    }

    /**
     * Get featured image for post
     */
    public function getFeaturedImage(int $postId): ?array {
        return $this->db->fetch(
            "SELECT * FROM blog_media WHERE post_id = ? AND is_featured = 1 LIMIT 1",
            [$postId]
        );
    }
}
