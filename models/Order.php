<?php
/**
 * Digiws - Order Model
 */

class Order {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array {
        $order = $this->db->fetch(
            "SELECT o.*, u.name as user_name, u.email as user_email, u.phone as user_phone 
             FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?",
            [$id]
        );
        if ($order) {
            $order['items'] = $this->getItems($id);
        }
        return $order;
    }

    public function findByOrderNumber(string $orderNumber): ?array {
        $order = $this->db->fetch(
            "SELECT o.*, u.name as user_name, u.email as user_email, u.phone as user_phone 
             FROM orders o JOIN users u ON o.user_id = u.id WHERE o.order_number = ?",
            [$orderNumber]
        );
        if ($order) {
            $order['items'] = $this->getItems($order['id']);
        }
        return $order;
    }

    public function create(array $data): int {
        return $this->db->insert('orders', [
            'order_number'   => generateOrderNumber(),
            'user_id'        => $data['user_id'],
            'subtotal'       => $data['subtotal'],
            'discount'       => $data['discount'] ?? 0,
            'tax_amount'     => $data['tax_amount'] ?? 0,
            'total'          => $data['total'],
            'coupon_id'      => $data['coupon_id'] ?? null,
            'coupon_code'    => $data['coupon_code'] ?? null,
            'status'         => 'pending',
            'payment_method' => $data['payment_method'] ?? null,
            'payment_status' => 'pending',
            'notes'          => $data['notes'] ?? null
        ]);
    }

    public function addItem(int $orderId, array $item): int {
        return $this->db->insert('order_items', [
            'order_id'       => $orderId,
            'product_id'     => $item['product_id'],
            'product_title'  => $item['product_title'],
            'price'          => $item['price'],
            'quantity'       => $item['quantity'] ?? 1,
            'setup_included' => $item['setup_included'] ?? 0,
            'setup_price'    => $item['setup_price'] ?? 0,
            'support_months' => $item['support_months'] ?? 0,
            'support_price'  => $item['support_price'] ?? 0,
            'subtotal'       => $item['subtotal'],
            'license_key'    => $item['license_key'] ?? null
        ]);
    }

    public function updateItem(int $itemId, array $data): int {
        return $this->db->update('order_items', $data, 'id = ?', [$itemId]);
    }

    public function getItems(int $orderId): array {
        return $this->db->fetchAll(
            "SELECT oi.*, p.slug, p.thumbnail, p.download_file, p.external_url, p.support_6_months, p.support_12_months 
             FROM order_items oi 
             LEFT JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?",
            [$orderId]
        );
    }

    public function update(int $id, array $data): int {
        $res = $this->db->update('orders', $data, 'id = ?', [$id]);
        $this->syncOrderToCrm($id);
        return $res;
    }

    public function updateStatus(int $id, string $status): int {
        $res = $this->db->update('orders', ['status' => $status], 'id = ?', [$id]);
        $activityType = null;
        $activityDesc = null;
        if ($status === 'completed') {
            $activityType = 'Purchased';
            $activityDesc = "Order status updated to completed.";
        } elseif ($status === 'cancelled') {
            $activityType = 'Cancelled';
            $activityDesc = "Order was cancelled.";
        }
        $this->syncOrderToCrm($id, $activityType, $activityDesc);
        if ($status === 'completed') {
            try {
                $order = $this->db->fetch("SELECT u.email FROM orders o JOIN users u ON u.id = o.user_id WHERE o.id = ?", [$id]);
                if ($order) MarketingSubscriber::triggerAutomation('order_delivered', ['email' => $order['email'], 'order_id' => $id]);
            } catch (Throwable $e) { error_log('DigiWS order-delivered automation: ' . $e->getMessage()); }
        }
        return $res;
    }

    public function updatePaymentStatus(int $id, string $status, ?string $method = null): int {
        $data = ['payment_status' => $status];
        if ($method) $data['payment_method'] = $method;
        if ($status === 'paid') {
            $defaultStatus = (new Setting())->get('after_payment_status', 'completed');
            $data['status'] = $defaultStatus;
        }
        $res = $this->db->update('orders', $data, 'id = ?', [$id]);
        
        $activityType = null;
        $activityDesc = null;
        if ($status === 'paid') {
            $activityType = 'Purchased';
            $activityDesc = "Payment successfully completed" . ($method ? " via {$method}." : ".");
        } elseif ($status === 'failed') {
            $activityType = 'Payment Failed';
            $activityDesc = "Payment transaction failed.";
        }
        $this->syncOrderToCrm($id, $activityType, $activityDesc);
        return $res;
    }

    private function syncOrderToCrm(int $orderId, ?string $activityType = null, ?string $activityDesc = null): void {
        try {
            $order = $this->db->fetch("SELECT user_id, order_number FROM orders WHERE id = ?", [$orderId]);
            if ($order && $order['user_id']) {
                $user = $this->db->fetch("SELECT email FROM users WHERE id = ?", [(int)$order['user_id']]);
                if ($user) {
                    $email = $user['email'];
                    $subModel = new MarketingSubscriber();
                    $subModel->syncSubscriberCategory($email);
                    
                    if ($activityType) {
                        $sub = $subModel->findByEmail($email);
                        if ($sub) {
                            $subModel->logTimelineActivity($sub['id'], $activityType, "Order #{$order['order_number']}: " . $activityDesc);
                        }
                    }

                    // Trigger order_placed automation on successful payment
                    if ($activityType === 'Purchased') {
                        MarketingSubscriber::triggerAutomation('order_placed', ['email' => $email, 'order_id' => $orderId]);
                    }
                }
            }
        } catch (Throwable $e) {
            error_log('syncOrderToCrm error: ' . $e->getMessage());
        }
    }

    public function getUserOrders(int $userId, int $page = 1, int $perPage = 10): array {
        $offset = ($page - 1) * $perPage;
        $total = $this->db->count('orders', 'user_id = ?', [$userId]);
        $orders = $this->db->fetchAll(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$userId, $perPage, $offset]
        );
        return ['orders' => $orders, 'total' => $total];
    }

    public function getAll(int $page = 1, int $perPage = 20, array $filters = []): array {
        $offset = ($page - 1) * $perPage;
        $where = '1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $where .= ' AND o.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['payment_status'])) {
            $where .= ' AND o.payment_status = ?';
            $params[] = $filters['payment_status'];
        }
        if (!empty($filters['search'])) {
            $where .= ' AND (o.order_number LIKE ? OR u.name LIKE ? OR u.email LIKE ?)';
            $s = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$s, $s, $s]);
        }

        $countParams = $params;
        $total = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM orders o JOIN users u ON o.user_id = u.id WHERE {$where}",
            $countParams
        );

        $params[] = $perPage;
        $params[] = $offset;
        $orders = $this->db->fetchAll(
            "SELECT o.*, u.name as user_name, u.email as user_email 
             FROM orders o JOIN users u ON o.user_id = u.id 
             WHERE {$where} ORDER BY o.created_at DESC LIMIT ? OFFSET ?",
            $params
        );

        return ['orders' => $orders, 'total' => (int)$total];
    }

    // Dashboard Stats
    public function getTotalOrders(): int {
        return $this->db->count('orders');
    }

    public function getTotalRevenue(): float {
        return (float) $this->db->fetchColumn(
            "SELECT COALESCE(SUM(total), 0) FROM orders WHERE payment_status = 'paid'"
        );
    }

    public function getMonthlyRevenue(): float {
        return (float) $this->db->fetchColumn(
            "SELECT COALESCE(SUM(total), 0) FROM orders WHERE payment_status = 'paid' AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())"
        );
    }

    public function getRecentOrders(int $limit = 10): array {
        return $this->db->fetchAll(
            "SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    public function getMonthlySalesData(int $months = 12): array {
        return $this->db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                    COUNT(*) as total_orders, 
                    COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END), 0) as revenue
             FROM orders 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH) 
             GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
             ORDER BY month ASC",
            [$months]
        );
    }

    public function getOrdersByStatus(): array {
        return $this->db->fetchAll(
            "SELECT status, COUNT(*) as count FROM orders GROUP BY status"
        );
    }

    public function getUserDownloads(int $userId): array {
        $statuses = explode(',', (new Setting())->get('downloadable_statuses', 'paid,completed'));
        $statuses = array_map('trim', $statuses);
        $statusList = implode("','", array_map('sanitize', $statuses));

        return $this->db->fetchAll(
            "SELECT oi.*, o.order_number, o.payment_status, o.status as order_status, 
                    p.slug, p.thumbnail, p.download_file, p.external_url, p.support_6_months, p.support_12_months
             FROM order_items oi 
             JOIN orders o ON oi.order_id = o.id 
             JOIN products p ON oi.product_id = p.id 
             WHERE o.user_id = ? AND (o.payment_status IN ('$statusList') OR o.status IN ('$statusList'))
             ORDER BY o.created_at DESC",
            [$userId]
        );
    }

    public function canDownload(int $userId, int $productId): bool {
        $statuses = explode(',', (new Setting())->get('downloadable_statuses', 'paid,completed'));
        $statuses = array_map('trim', $statuses);
        $statusList = implode("','", array_map('sanitize', $statuses));

        return $this->db->fetchColumn(
            "SELECT COUNT(*) FROM order_items oi 
             JOIN orders o ON oi.order_id = o.id 
             WHERE o.user_id = ? AND oi.product_id = ? AND (o.payment_status IN ('$statusList') OR o.status IN ('$statusList'))",
            [$userId, $productId]
        ) > 0;
    }

    /**
     * Calculate and store support expiration date for order items
     */
    public function activateOrderSupport(int $orderId): void {
        $order = $this->find($orderId);
        if (!$order) return;
        
        foreach ($order['items'] as $item) {
            // Find the latest active support_expires_at for this product and user
            $activeExpiry = $this->db->fetchColumn(
                "SELECT MAX(oi.support_expires_at) FROM order_items oi 
                 JOIN orders o ON oi.order_id = o.id 
                 WHERE oi.product_id = ? AND o.user_id = ? AND o.payment_status = 'paid' AND oi.support_expires_at > NOW() AND oi.id != ?",
                [$item['product_id'], $order['user_id'], $item['id']]
            );
            
            $supportMonths = (int)$item['support_months'];
            $baseTime = $activeExpiry ? strtotime($activeExpiry) : time();
            
            if ($supportMonths === 0) {
                $newExpiry = date('Y-m-d H:i:s', strtotime('+10 days', $baseTime));
            } else {
                $newExpiry = date('Y-m-d H:i:s', strtotime("+$supportMonths months", $baseTime));
            }
            
            $this->updateItem($item['id'], ['support_expires_at' => $newExpiry]);
        }
    }
}
