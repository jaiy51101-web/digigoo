<?php
/**
 * Digiws - BlogBookmark Model
 */

class BlogBookmark {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function toggle(int $userId, int $postId): array {
        $exists = $this->db->fetch(
            "SELECT id FROM blog_bookmarks WHERE user_id = ? AND post_id = ?",
            [$userId, $postId]
        );

        if ($exists) {
            $this->db->delete('blog_bookmarks', 'user_id = ? AND post_id = ?', [$userId, $postId]);
            return ['bookmarked' => false];
        }

        $this->db->insert('blog_bookmarks', ['user_id' => $userId, 'post_id' => $postId]);
        return ['bookmarked' => true];
    }

    public function isBookmarked(int $userId, int $postId): bool {
        return (bool)$this->db->fetchColumn(
            "SELECT COUNT(*) FROM blog_bookmarks WHERE user_id = ? AND post_id = ?",
            [$userId, $postId]
        );
    }

    public function getUserBookmarks(int $userId): array {
        return $this->db->fetchAll(
            "SELECT p.*, bc.name as category_name, bk.created_at as bookmarked_at
             FROM blog_bookmarks bk
             INNER JOIN posts p ON p.id = bk.post_id
             LEFT JOIN blog_categories bc ON bc.id = p.blog_category_id
             WHERE bk.user_id = ? AND p.status = 'published'
             ORDER BY bk.created_at DESC",
            [$userId]
        );
    }
}
