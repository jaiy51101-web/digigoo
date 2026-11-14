<?php
/**
 * Digiws - Review Model
 */

class Review {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array {
        return $this->db->fetch("SELECT r.*, u.name as user_name, u.avatar FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.id = ?", [$id]);
    }

    public function getProductReviews(int $productId, int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        $total = $this->db->count('reviews', "product_id = ? AND status = 'approved'", [$productId]);
        $reviews = $this->db->fetchAll(
            "SELECT r.*, u.name as user_name, u.avatar 
             FROM reviews r JOIN users u ON r.user_id = u.id 
             WHERE r.product_id = ? AND r.status = 'approved' 
             ORDER BY r.created_at DESC LIMIT ? OFFSET ?",
            [$productId, $perPage, $offset]
        );
        return ['reviews' => $reviews, 'total' => $total];
    }

    public function create(array $data): int {
        return $this->db->insert('reviews', [
            'product_id' => $data['product_id'],
            'user_id'    => $data['user_id'],
            'rating'     => $data['rating'],
            'title'      => $data['title'] ?? null,
            'comment'    => $data['comment'] ?? null,
            'status'     => 'pending'
        ]);
    }

    public function updateStatus(int $id, string $status): int {
        $result = $this->db->update('reviews', ['status' => $status], 'id = ?', [$id]);
        // Update product avg rating
        $review = $this->db->fetch("SELECT product_id FROM reviews WHERE id = ?", [$id]);
        if ($review) {
            (new Product())->updateRating($review['product_id']);
        }
        return $result;
    }

    public function delete(int $id): int {
        $review = $this->db->fetch("SELECT product_id FROM reviews WHERE id = ?", [$id]);
        $result = $this->db->delete('reviews', 'id = ?', [$id]);
        if ($review) {
            (new Product())->updateRating($review['product_id']);
        }
        return $result;
    }

    public function hasReviewed(int $userId, int $productId): bool {
        return $this->db->exists('reviews', 'user_id = ? AND product_id = ?', [$userId, $productId]);
    }

    public function getAll(int $page = 1, int $perPage = 20, string $status = ''): array {
        $offset = ($page - 1) * $perPage;
        $where = '1=1';
        $params = [];
        if ($status) {
            $where .= ' AND r.status = ?';
            $params[] = $status;
        }

        $countParams = $params;
        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM reviews r WHERE {$where}", $countParams
        );

        $params[] = $perPage;
        $params[] = $offset;
        $reviews = $this->db->fetchAll(
            "SELECT r.*, u.name as user_name, p.title as product_title 
             FROM reviews r JOIN users u ON r.user_id = u.id JOIN products p ON r.product_id = p.id 
             WHERE {$where} ORDER BY r.created_at DESC LIMIT ? OFFSET ?",
            $params
        );
        return ['reviews' => $reviews, 'total' => (int)$total];
    }

    public function getRatingDistribution(int $productId): array {
        $dist = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $this->db->fetchColumn(
                "SELECT COUNT(*) FROM reviews WHERE product_id = ? AND status = 'approved' AND rating = ?",
                [$productId, $i]
            );
            $dist[$i] = (int)$count;
        }
        return $dist;
    }
}
