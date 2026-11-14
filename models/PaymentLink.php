<?php
/**
 * Digiws - PaymentLink Model
 */

class PaymentLink {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create(int $orderId): string {
        $token = bin2hex(random_bytes(16));
        $this->db->insert('payment_links', [
            'order_id'   => $orderId,
            'link_token' => $token
        ]);
        return $token;
    }

    public function findByToken(string $token): ?array {
        return $this->db->fetch("SELECT * FROM payment_links WHERE link_token = ? LIMIT 1", [$token]);
    }

    public function deleteByToken(string $token): void {
        $this->db->query("DELETE FROM payment_links WHERE link_token = ?", [$token]);
    }
}
