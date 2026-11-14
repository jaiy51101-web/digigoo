<?php
/**
 * Digiws - BlogTag Model
 */

class BlogTag {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll(): array {
        return $this->db->fetchAll(
            "SELECT bt.*, COUNT(bpt.post_id) as post_count 
             FROM blog_tags bt 
             LEFT JOIN blog_post_tags bpt ON bpt.tag_id = bt.id 
             GROUP BY bt.id 
             ORDER BY post_count DESC, bt.name ASC"
        );
    }

    public function getPopular(int $limit = 20): array {
        return $this->db->fetchAll(
            "SELECT bt.*, COUNT(bpt.post_id) as post_count 
             FROM blog_tags bt 
             LEFT JOIN blog_post_tags bpt ON bpt.tag_id = bt.id 
             LEFT JOIN posts p ON p.id = bpt.post_id AND p.status = 'published'
             GROUP BY bt.id 
             HAVING post_count > 0
             ORDER BY post_count DESC 
             LIMIT ?",
            [$limit]
        );
    }

    public function findBySlug(string $slug): ?array {
        return $this->db->fetch("SELECT * FROM blog_tags WHERE slug = ?", [$slug]);
    }

    public function find(int $id): ?array {
        return $this->db->fetch("SELECT * FROM blog_tags WHERE id = ?", [$id]);
    }

    public function findOrCreate(string $name): int {
        $slug = generateSlug($name);
        $existing = $this->db->fetch("SELECT id FROM blog_tags WHERE slug = ?", [$slug]);
        if ($existing) return (int)$existing['id'];
        return $this->db->insert('blog_tags', ['name' => $name, 'slug' => $slug]);
    }

    public function syncPostTags(int $postId, array $tagNames): void {
        // Remove old tags
        $this->db->delete('blog_post_tags', 'post_id = ?', [$postId]);

        foreach ($tagNames as $name) {
            $name = trim($name);
            if (empty($name)) continue;
            $tagId = $this->findOrCreate($name);
            // Insert pivot
            try {
                $this->db->insert('blog_post_tags', ['post_id' => $postId, 'tag_id' => $tagId]);
            } catch (\Exception $e) {
                // Ignore duplicate
            }
        }
    }

    public function getByPost(int $postId): array {
        return $this->db->fetchAll(
            "SELECT bt.* FROM blog_tags bt 
             INNER JOIN blog_post_tags bpt ON bpt.tag_id = bt.id 
             WHERE bpt.post_id = ?
             ORDER BY bt.name ASC",
            [$postId]
        );
    }

    public function create(array $data): int {
        return $this->db->insert('blog_tags', $data);
    }

    public function update(int $id, array $data): int {
        return $this->db->update('blog_tags', $data, 'id = ?', [$id]);
    }

    public function delete(int $id): int {
        $this->db->delete('blog_post_tags', 'tag_id = ?', [$id]);
        return $this->db->delete('blog_tags', 'id = ?', [$id]);
    }
}
