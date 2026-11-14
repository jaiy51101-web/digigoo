<?php
/**
 * Digiws - Website Request Model
 */

class WebsiteRequest {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }


    public function create(array $data): int {
        return $this->db->insert('website_requests', [
            'user_id'      => $data['user_id'],
            'website_type' => $data['website_type'],
            'description'  => $data['description'],
            'budget'       => $data['budget'] ?? null,
            'status'       => 'pending'
        ]);
    }

    public function getAll(int $page = 1, int $perPage = 20): array {
        $offset = ($page - 1) * $perPage;
        $total = $this->db->count('website_requests');
        $requests = $this->db->fetchAll(
            "SELECT wr.*, u.name as user_name, u.email as user_email, u.phone as user_phone 
             FROM website_requests wr 
             LEFT JOIN users u ON wr.user_id = u.id 
             ORDER BY wr.created_at DESC LIMIT " . (int)$perPage . " OFFSET " . (int)$offset
        );
        return ['requests' => $requests, 'total' => $total];
    }

    public function find(int $id): ?array {
        return $this->db->fetch(
            "SELECT wr.*, u.name as user_name, u.email as user_email, u.phone as user_phone 
             FROM website_requests wr 
             LEFT JOIN users u ON wr.user_id = u.id 
             WHERE wr.id = ?",
            [$id]
        );
    }

    public function updateStatus(int $id, string $status): int {
        return $this->db->update('website_requests', ['status' => $status], 'id = ?', [$id]);
    }

    public function getByUserId(int $userId): array {
        return $this->db->fetchAll(
            "SELECT * FROM website_requests WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
    }
}
