<?php
/** Admin-entered feedback, for customer feedback received outside the website. */
class ManagedFeedback {
    private Database $db;
    private ?bool $tableAvailable = null;
    public function __construct() { $this->db = Database::getInstance(); }

    private function isAvailable(): bool {
        if ($this->tableAvailable !== null) return $this->tableAvailable;
        try {
            $exists = (bool)$this->db->fetch("SHOW TABLES LIKE 'managed_feedback'");
            if (!$exists) {
                $schema = BASE_PATH . '/database/managed_feedback.sql';
                if (is_readable($schema)) $this->db->getConnection()->exec((string)file_get_contents($schema));
                $exists = (bool)$this->db->fetch("SHOW TABLES LIKE 'managed_feedback'");
            }
            return $this->tableAvailable = $exists;
        } catch (Throwable $e) { return $this->tableAvailable = false; }
    }

    public function create(array $data): int {
        $id = $this->db->insert('managed_feedback', [
            'target_type' => $data['target_type'], 'target_id' => $data['target_id'],
            'reviewer_name' => $data['reviewer_name'], 'reviewer_role' => $data['reviewer_role'] ?? null,
            'rating' => $data['rating'] ?? null, 'message' => $data['message'],
            'status' => 'published', 'created_at' => date('Y-m-d H:i:s')
        ]);
        if ($id && $data['target_type'] === 'product') {
            (new Product())->updateRating((int)$data['target_id']);
        }
        return $id;
    }
    public function getFor(string $type, int $id): array {
        if (!$this->isAvailable()) return [];
        return $this->db->fetchAll('SELECT * FROM managed_feedback WHERE target_type = ? AND target_id = ? AND status = \'published\' ORDER BY created_at DESC', [$type, $id]);
    }
    public function getAll(): array {
        if (!$this->isAvailable()) return [];
        return $this->db->fetchAll("SELECT f.*, CASE WHEN f.target_type = 'product' THEN p.title ELSE po.title END AS target_title FROM managed_feedback f LEFT JOIN products p ON f.target_type = 'product' AND f.target_id = p.id LEFT JOIN posts po ON f.target_type = 'blog' AND f.target_id = po.id ORDER BY f.created_at DESC");
    }
    public function delete(int $id): int {
        if (!$this->isAvailable()) return 0;
        $fb = $this->db->fetch("SELECT target_type, target_id FROM managed_feedback WHERE id = ?", [$id]);
        $result = $this->db->delete('managed_feedback', 'id = ?', [$id]);
        if ($fb && $fb['target_type'] === 'product') {
            (new Product())->updateRating((int)$fb['target_id']);
        }
        return $result;
    }
}
