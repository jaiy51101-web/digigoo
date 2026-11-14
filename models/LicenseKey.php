<?php
/**
 * Digiws - License Key Model
 */

class LicenseKey {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function generate(int $productId, int $count = 1): array {
        $keys = [];
        for ($i = 0; $i < $count; $i++) {
            $key = generateLicenseKey();
            $this->db->insert('license_keys', [
                'product_id'  => $productId,
                'license_key' => $key,
                'status'      => 'available'
            ]);
            $keys[] = $key;
        }
        return $keys;
    }

    public function assignToOrder(int $productId, int $orderItemId): ?string {
        $key = $this->db->fetch(
            "SELECT * FROM license_keys WHERE product_id = ? AND status = 'available' LIMIT 1",
            [$productId]
        );
        if (!$key) {
            // Auto-generate if none available
            $newKey = generateLicenseKey();
            $this->db->insert('license_keys', [
                'product_id'    => $productId,
                'license_key'   => $newKey,
                'order_item_id' => $orderItemId,
                'status'        => 'assigned',
                'assigned_at'   => date('Y-m-d H:i:s')
            ]);
            return $newKey;
        }

        $this->db->update('license_keys', [
            'order_item_id' => $orderItemId,
            'status'        => 'assigned',
            'assigned_at'   => date('Y-m-d H:i:s')
        ], 'id = ?', [$key['id']]);

        return $key['license_key'];
    }

    public function revoke(int $id): void {
        $this->db->update('license_keys', ['status' => 'revoked'], 'id = ?', [$id]);
    }

    public function getByProduct(int $productId): array {
        return $this->db->fetchAll(
            "SELECT * FROM license_keys WHERE product_id = ? ORDER BY created_at DESC",
            [$productId]
        );
    }
}
