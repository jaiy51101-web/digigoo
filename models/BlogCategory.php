<?php
/**
 * Digiws - BlogCategory Model
 */

class BlogCategory {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(bool $activeOnly = true): array {
        $where = $activeOnly ? "WHERE status = 'active'" : "";
        return $this->db->fetchAll(
            "SELECT *, (SELECT COUNT(*) FROM posts p WHERE p.blog_category_id = bc.id AND p.status = 'published') as post_count
             FROM blog_categories bc {$where} ORDER BY sort_order ASC, name ASC"
        );
    }

    public function findBySlug(string $slug): ?array {
        return $this->db->fetch(
            "SELECT * FROM blog_categories WHERE slug = ? AND status = 'active'",
            [$slug]
        );
    }

    public function find(int $id): ?array {
        return $this->db->fetch("SELECT * FROM blog_categories WHERE id = ?", [$id]);
    }

    public function getPopular(int $limit = 8): array {
        return $this->db->fetchAll(
            "SELECT bc.*, COUNT(p.id) as post_count 
             FROM blog_categories bc
             LEFT JOIN posts p ON p.blog_category_id = bc.id AND p.status = 'published'
             WHERE bc.status = 'active'
             GROUP BY bc.id
             ORDER BY post_count DESC
             LIMIT ?",
            [$limit]
        );
    }

    public function create(array $data): int {
        return $this->db->insert('blog_categories', $data);
    }

    public function update(int $id, array $data): int {
        return $this->db->update('blog_categories', $data, 'id = ?', [$id]);
    }

    public function delete(int $id): int {
        // Unlink posts from this category
        $this->db->update('posts', ['blog_category_id' => null], 'blog_category_id = ?', [$id]);
        return $this->db->delete('blog_categories', 'id = ?', [$id]);
    }
}
