<?php
/**
 * Digiws - Coupon Model
 */

class Coupon {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array {
        return $this->db->fetch("SELECT * FROM coupons WHERE id = ?", [$id]);
    }

    public function findByCode(string $code): ?array {
        return $this->db->fetch("SELECT * FROM coupons WHERE code = ?", [strtoupper($code)]);
    }

    public function validate(string $code, float $orderTotal = 0, array $cartItems = []): ?array {
        $coupon = $this->findByCode($code);
        if (!$coupon) return null;
        if ($coupon['status'] !== 'active') return null;
        if ($coupon['expires_at'] && strtotime($coupon['expires_at']) < time()) return null;
        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) return null;
        
        // Per-user usage limit check
        if (isLoggedIn() && isset($coupon['user_usage_limit']) && $coupon['user_usage_limit'] !== null) {
            $userUses = (int) $this->db->fetchColumn(
                "SELECT COUNT(*) FROM orders WHERE user_id = ? AND coupon_id = ? AND payment_status = 'paid'",
                [currentUserId(), $coupon['id']]
            );
            if ($userUses >= (int)$coupon['user_usage_limit']) {
                return null;
            }
        }
        
        // Specific product check
        if (!empty($coupon['product_id']) && !empty($cartItems)) {
            $foundProduct = false;
            foreach ($cartItems as $item) {
                if ((int)$item['product_id'] === (int)$coupon['product_id']) {
                    $foundProduct = true;
                    break;
                }
            }
            if (!$foundProduct) {
                return null; // Product is not in cart
            }
        }
        
        if ($coupon['min_order_amount'] > 0 && $orderTotal < $coupon['min_order_amount']) return null;
        return $coupon;
    }

    public function calculateDiscount(array $coupon, float $subtotal, array $cartItems = []): float {
        $baseAmount = $subtotal;
        
        // If coupon applies only to a specific product, find its subtotal in cart
        if (!empty($coupon['product_id']) && !empty($cartItems)) {
            $productSubtotal = 0;
            foreach ($cartItems as $item) {
                if ((int)$item['product_id'] === (int)$coupon['product_id']) {
                    $itemPrice = (float)$item['sale_price'];
                    if ($item['setup_included'] && $item['setup_option']) {
                        $itemPrice += (float)$item['setup_price'];
                    }
                    if ($item['support_months'] == 6) {
                        $itemPrice += (float)$item['support_6_months'];
                    } elseif ($item['support_months'] == 12) {
                        $itemPrice += (float)$item['support_12_months'];
                    }
                    $productSubtotal += $itemPrice * $item['quantity'];
                }
            }
            $baseAmount = $productSubtotal;
        }
        
        if ($coupon['discount_type'] === 'percentage') {
            $discount = $baseAmount * ($coupon['discount_value'] / 100);
            if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
                $discount = $coupon['max_discount'];
            }
        } else {
            $discount = $coupon['discount_value'];
        }
        return min($discount, $baseAmount);
    }

    public function incrementUsage(int $id): void {
        $this->db->query("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?", [$id]);
    }

    public function create(array $data): int {
        return $this->db->insert('coupons', [
            'code'             => strtoupper($data['code']),
            'product_id'       => (!empty($data['product_id']) ? (int)$data['product_id'] : null),
            'discount_type'    => $data['discount_type'],
            'discount_value'   => $data['discount_value'],
            'min_order_amount' => $data['min_order_amount'] ?? 0,
            'max_discount'     => $data['max_discount'] ?? null,
            'usage_limit'      => $data['usage_limit'] ?? null,
            'user_usage_limit' => (isset($data['user_usage_limit']) && $data['user_usage_limit'] !== '' ? (int)$data['user_usage_limit'] : null),
            'expires_at'       => (!empty($data['expires_at']) ? $data['expires_at'] : null),
            'status'           => $data['status'] ?? 'active'
        ]);
    }

    public function update(int $id, array $data): int {
        if (isset($data['code'])) $data['code'] = strtoupper($data['code']);
        return $this->db->update('coupons', $data, 'id = ?', [$id]);
    }

    public function delete(int $id): int {
        return $this->db->delete('coupons', 'id = ?', [$id]);
    }

    public function getAll(int $page = 1, int $perPage = 20): array {
        $offset = ($page - 1) * $perPage;
        $total = $this->db->count('coupons');
        $coupons = $this->db->fetchAll(
            "SELECT * FROM coupons ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
        return ['coupons' => $coupons, 'total' => $total];
    }
}
