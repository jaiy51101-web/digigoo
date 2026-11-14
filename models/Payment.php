<?php
/**
 * Digiws - Payment Model
 */

class Payment {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create(array $data): int {
        return $this->db->insert('payments', [
            'order_id'         => $data['order_id'],
            'user_id'          => $data['user_id'],
            'gateway'          => $data['gateway'],
            'transaction_id'   => $data['transaction_id'] ?? null,
            'gateway_order_id' => $data['gateway_order_id'] ?? null,
            'amount'           => $data['amount'],
            'currency'         => $data['currency'] ?? 'INR',
            'status'           => $data['status'] ?? 'pending',
            'gateway_response' => $data['gateway_response'] ?? null
        ]);
    }

    public function updateStatus(int $id, string $status, ?string $transactionId = null, ?string $response = null): int {
        $data = ['status' => $status];
        if ($transactionId) $data['transaction_id'] = $transactionId;
        if ($response) $data['gateway_response'] = $response;
        return $this->db->update('payments', $data, 'id = ?', [$id]);
    }

    public function findByOrderId(int $orderId): ?array {
        return $this->db->fetch("SELECT * FROM payments WHERE order_id = ? ORDER BY id DESC LIMIT 1", [$orderId]);
    }

    public function findByGatewayOrderId(string $gatewayOrderId): ?array {
        return $this->db->fetch("SELECT * FROM payments WHERE gateway_order_id = ?", [$gatewayOrderId]);
    }
}
