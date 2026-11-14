<?php
/**
 * Digiws - BlogComment Model
 */

class BlogComment {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getByPost(int $postId): array {
        return $this->db->fetchAll(
            "SELECT bc.*, u.name as user_display_name, u.avatar as user_avatar
             FROM blog_comments bc
             LEFT JOIN users u ON u.id = bc.user_id
             WHERE bc.post_id = ? AND bc.status = 'approved' AND bc.parent_id IS NULL
             ORDER BY bc.created_at ASC",
            [$postId]
        );
    }

    public function getReplies(int $commentId): array {
        return $this->db->fetchAll(
            "SELECT bc.*, u.name as user_display_name, u.avatar as user_avatar
             FROM blog_comments bc
             LEFT JOIN users u ON u.id = bc.user_id
             WHERE bc.parent_id = ? AND bc.status = 'approved'
             ORDER BY bc.created_at ASC",
            [$commentId]
        );
    }

    public function getAll(int $page = 1, int $perPage = 20, array $filters = []): array {
        $offset = ($page - 1) * $perPage;
        $where  = "1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $where .= " AND bc.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['post_id'])) {
            $where .= " AND bc.post_id = ?";
            $params[] = $filters['post_id'];
        }

        $total = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM blog_comments bc WHERE {$where}", $params);
        $sql   = "SELECT bc.*, u.name as user_display_name, p.title as post_title
                  FROM blog_comments bc
                  LEFT JOIN users u ON u.id = bc.user_id
                  LEFT JOIN posts p ON p.id = bc.post_id
                  WHERE {$where}
                  ORDER BY bc.created_at DESC
                  LIMIT {$perPage} OFFSET {$offset}";

        return ['comments' => $this->db->fetchAll($sql, $params), 'total' => $total];
    }

    public function getRecent(int $limit = 5): array {
        return $this->db->fetchAll(
            "SELECT bc.*, p.title as post_title, p.slug as post_slug
             FROM blog_comments bc
             LEFT JOIN posts p ON p.id = bc.post_id
             WHERE bc.status = 'approved'
             ORDER BY bc.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    public function create(array $data): int {
        return $this->db->insert('blog_comments', $data);
    }

    public function find(int $id): ?array {
        return $this->db->fetch("SELECT * FROM blog_comments WHERE id = ?", [$id]);
    }

    public function updateStatus(int $id, string $status): int {
        return $this->db->update('blog_comments', ['status' => $status], 'id = ?', [$id]);
    }

    public function delete(int $id): int {
        return $this->db->delete('blog_comments', 'id = ?', [$id]);
    }

    public function addLike(int $commentId, string $ipHash): bool {
        // Check if already liked
        $exists = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM blog_comment_likes WHERE comment_id = ? AND ip_hash = ?",
            [$commentId, $ipHash]
        );
        if ($exists) return false;

        $this->db->insert('blog_comment_likes', ['comment_id' => $commentId, 'ip_hash' => $ipHash]);
        $this->db->update('blog_comments', [], 'id = ?', [$commentId]);
        // Increment likes counter
        $this->db->query("UPDATE blog_comments SET likes = likes + 1 WHERE id = ?", [$commentId]);
        return true;
    }

    public function getPendingCount(): int {
        return (int)$this->db->fetchColumn("SELECT COUNT(*) FROM blog_comments WHERE status = 'pending'");
    }
}
