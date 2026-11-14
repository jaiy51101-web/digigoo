<?php
/**
 * DigiWS - Marketing Segment Model
 */

class MarketingSegment {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function all(): array {
        return $this->db->fetchAll("SELECT * FROM marketing_segments ORDER BY name ASC");
    }

    public function find(int $id): ?array {
        $seg = $this->db->fetch("SELECT * FROM marketing_segments WHERE id = ?", [$id]);
        if ($seg) {
            $seg['rules'] = json_decode($seg['rules'], true);
        }
        return $seg;
    }

    public function create(string $name, array $rules): int {
        return $this->db->insert('marketing_segments', [
            'name' => trim($name),
            'rules' => json_encode($rules)
        ]);
    }

    public function update(int $id, string $name, array $rules): void {
        $this->db->update('marketing_segments', [
            'name' => trim($name),
            'rules' => json_encode($rules)
        ], 'id = ?', [$id]);
    }

    public function delete(int $id): void {
        $this->db->delete('marketing_segments', 'id = ?', [$id]);
    }
}
