<?php
/**
 * DigiWS - Marketing List Model
 */

class MarketingList {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function all(): array {
        return $this->db->fetchAll(
            "SELECT ml.*, COUNT(mls.subscriber_id) as subscriber_count 
             FROM marketing_lists ml 
             LEFT JOIN marketing_list_subscriber mls ON ml.id = mls.list_id 
             GROUP BY ml.id 
             ORDER BY ml.name ASC"
        );
    }

    public function find(int $id): ?array {
        return $this->db->fetch("SELECT * FROM marketing_lists WHERE id = ?", [$id]);
    }

    public function create(string $name, ?string $description = null): int {
        return $this->db->insert('marketing_lists', [
            'name' => trim($name),
            'description' => $description ? trim($description) : null
        ]);
    }

    public function update(int $id, string $name, ?string $description = null): void {
        $this->db->update('marketing_lists', [
            'name' => trim($name),
            'description' => $description ? trim($description) : null
        ], 'id = ?', [$id]);
    }

    public function delete(int $id): void {
        $this->db->delete('marketing_lists', 'id = ?', [$id]);
    }

    public function addSubscriber(int $listId, int $subscriberId): bool {
        // Prevent duplicate associations
        $exists = $this->db->exists('marketing_list_subscriber', 'list_id = ? AND subscriber_id = ?', [$listId, $subscriberId]);
        if ($exists) {
            return true;
        }
        $this->db->insert('marketing_list_subscriber', [
            'list_id' => $listId,
            'subscriber_id' => $subscriberId
        ]);
        return true;
    }

    public function removeSubscriber(int $listId, int $subscriberId): void {
        $this->db->delete('marketing_list_subscriber', 'list_id = ? AND subscriber_id = ?', [$listId, $subscriberId]);
    }

    public function getSubscribers(int $listId, int $limit = 50, int $offset = 0): array {
        return $this->db->fetchAll(
            "SELECT ms.* FROM marketing_subscribers ms 
             JOIN marketing_list_subscriber mls ON ms.id = mls.subscriber_id 
             WHERE mls.list_id = ? 
             ORDER BY ms.created_at DESC 
             LIMIT {$limit} OFFSET {$offset}",
            [$listId]
        );
    }

    public function getSubscriberCount(int $listId): int {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM marketing_list_subscriber WHERE list_id = ?",
            [$listId]
        );
    }
}
