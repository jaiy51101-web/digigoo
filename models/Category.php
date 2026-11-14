<?php
/**
 * Digiws - Category Model
 */

class Category {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array {
        return $this->db->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
    }

    public function findBySlug(string $slug): ?array {
        return $this->db->fetch("SELECT * FROM categories WHERE slug = ?", [$slug]);
    }

    public function getAll(bool $activeOnly = false): array {
        $where = $activeOnly ? "WHERE status = 'active'" : '';
        return $this->db->fetchAll("SELECT * FROM categories {$where} ORDER BY sort_order ASC, name ASC");
    }

    public function getWithProductCount(): array {
        return $this->db->fetchAll(
            "SELECT c.*, COUNT(p.id) as product_count 
             FROM categories c 
             LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active' 
             WHERE c.status = 'active' 
             GROUP BY c.id 
             ORDER BY c.sort_order ASC"
        );
    }

    public function create(array $data): int {
        $data['slug'] = uniqueSlug($data['name'], 'categories');
        return $this->db->insert('categories', $data);
    }

    public function update(int $id, array $data): int {
        if (isset($data['name'])) {
            $data['slug'] = uniqueSlug($data['name'], 'categories', $id);
        }
        return $this->db->update('categories', $data, 'id = ?', [$id]);
    }

    public function delete(int $id): int {
        return $this->db->delete('categories', 'id = ?', [$id]);
    }

    public function getParentCategories(): array {
        return $this->db->fetchAll(
            "SELECT * FROM categories WHERE parent_id IS NULL AND status = 'active' ORDER BY sort_order ASC"
        );
    }

    public function getSubcategories(int $parentId): array {
        return $this->db->fetchAll(
            "SELECT * FROM categories WHERE parent_id = ? AND status = 'active' ORDER BY sort_order ASC",
            [$parentId]
        );
    }
}
