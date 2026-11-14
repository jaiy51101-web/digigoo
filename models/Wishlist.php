<?php
/**
 * Digiws - Wishlist Model
 */

class Wishlist {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function toggle(int $productId): array {
        $userId = currentUserId();
        $sessionId = session_id();

        $where = $userId ? "user_id = ?" : "session_id = ?";
        $param = $userId ?: $sessionId;

        $exists = $this->db->fetch(
            "SELECT id FROM wishlist WHERE product_id = ? AND {$where}",
            [$productId, $param]
        );

        if ($exists) {
            $this->db->delete('wishlist', 'id = ?', [$exists['id']]);
            return ['status' => 'removed', 'message' => 'Product removed from wishlist.'];
        } else {
            $this->db->insert('wishlist', [
                'user_id'    => $userId,
                'session_id' => $sessionId,
                'product_id' => $productId
            ]);
            return ['status' => 'added', 'message' => 'Product added to wishlist.'];
        }
    }

    public function getItems(): array {
        $userId = currentUserId();
        $sessionId = session_id();

        $where = $userId ? "w.user_id = ?" : "w.session_id = ?";
        $param = $userId ?: $sessionId;

        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM wishlist w 
             JOIN products p ON w.product_id = p.id 
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE {$where} AND p.status = 'active'
             ORDER BY w.created_at DESC",
            [$param]
        );
    }

    public function isInWishlist(int $productId): bool {
        $userId = currentUserId();
        $sessionId = session_id();

        $where = $userId ? "user_id = ?" : "session_id = ?";
        $param = $userId ?: $sessionId;

        return $this->db->exists('wishlist', "product_id = ? AND {$where}", [$productId, $param]);
    }
    
    public function getCount(): int {
        $userId = currentUserId();
        $sessionId = session_id();

        $where = $userId ? "user_id = ?" : "session_id = ?";
        $param = $userId ?: $sessionId;

        return $this->db->count('wishlist', "{$where}", [$param]);
    }
}
