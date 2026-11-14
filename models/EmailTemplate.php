<?php
/**
 * Digiws - Email Template Model
 */

class EmailTemplate {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array {
        return $this->db->fetch("SELECT * FROM email_templates WHERE id = ?", [$id]);
    }

    public function findBySlug(string $slug): ?array {
        return $this->db->fetch("SELECT * FROM email_templates WHERE slug = ?", [$slug]);
    }

    public function getAll(): array {
        return $this->db->fetchAll("SELECT * FROM email_templates ORDER BY name ASC");
    }

    public function update(int $id, array $data): int {
        return $this->db->update('email_templates', $data, 'id = ?', [$id]);
    }
}
