<?php
/**
 * Digiws - Newsletter Model
 */

class Newsletter {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function subscribe(string $email): bool {
        $exists = $this->db->exists('newsletter_subscribers', 'email = ?', [$email]);
        if ($exists) {
            $this->db->update('newsletter_subscribers', ['status' => 'active'], 'email = ?', [$email]);
            return true;
        }
        $this->db->insert('newsletter_subscribers', ['email' => $email, 'status' => 'active']);
        return true;
    }

    public function unsubscribe(string $email): bool {
        return $this->db->update('newsletter_subscribers', ['status' => 'unsubscribed'], 'email = ?', [$email]) > 0;
    }

    public function getAll(): array {
        return $this->db->fetchAll("SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC");
    }

    public function getActiveCount(): int {
        return $this->db->count('newsletter_subscribers', "status = 'active'");
    }
}
