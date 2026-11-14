<?php
/**
 * Digiws - Cart Model
 */

class Cart {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    private function getIdentifier(): array {
        if (isLoggedIn()) {
            return ['field' => 'user_id', 'value' => currentUserId()];
        }
        return ['field' => 'session_id', 'value' => session_id()];
    }

    public function getItems(): array {
        $id = $this->getIdentifier();
        return $this->db->fetchAll(
            "SELECT c.*, p.title, p.slug, p.thumbnail, p.sale_price, p.mrp, p.gst_percent, 
                    p.setup_option, p.setup_price, p.support_6_months, p.support_12_months, p.status as product_status
             FROM cart c 
             JOIN products p ON c.product_id = p.id 
             WHERE c.{$id['field']} = ? AND p.status = 'active'
             ORDER BY c.created_at DESC",
            [$id['value']]
        );
    }

    public function add(int $productId, int $setupIncluded = 0, int $supportMonths = 0): bool {
        $id = $this->getIdentifier();

        // Check if already in cart
        $exists = $this->db->fetch(
            "SELECT id FROM cart WHERE {$id['field']} = ? AND product_id = ?",
            [$id['value'], $productId]
        );

        if ($exists) {
            // Update options
            $this->db->update('cart', [
                'setup_included'  => $setupIncluded,
                'support_months'  => $supportMonths
            ], 'id = ?', [$exists['id']]);
        } else {
            $data = [
                'product_id'     => $productId,
                'quantity'       => 1,
                'setup_included' => $setupIncluded,
                'support_months' => $supportMonths
            ];
            $data[$id['field']] = $id['value'];
            $this->db->insert('cart', $data);
        }

        updateCartCount();
        return true;
    }

    public function remove(int $productId): bool {
        $id = $this->getIdentifier();
        $this->db->delete('cart', "{$id['field']} = ? AND product_id = ?", [$id['value'], $productId]);
        updateCartCount();
        return true;
    }

    public function updateItem(int $productId, array $data): bool {
        $id = $this->getIdentifier();
        $this->db->update('cart', $data, "{$id['field']} = ? AND product_id = ?", [$id['value'], $productId]);
        return true;
    }

    public function clear(): void {
        $id = $this->getIdentifier();
        $this->db->delete('cart', "{$id['field']} = ?", [$id['value']]);
        updateCartCount();
    }

    public function getCount(): int {
        $id = $this->getIdentifier();
        return $this->db->count('cart', "{$id['field']} = ?", [$id['value']]);
    }

    public function getTotal(): array {
        $items = $this->getItems();
        $subtotal = 0;
        $taxTotal = 0;

        foreach ($items as $item) {
            $itemPrice = (float) $item['sale_price'];

            // Add setup price if selected
            if ($item['setup_included'] && $item['setup_option']) {
                $itemPrice += (float) $item['setup_price'];
            }

            // Add support price
            if ($item['support_months'] == 6) {
                $itemPrice += (float) $item['support_6_months'];
            } elseif ($item['support_months'] == 12) {
                $itemPrice += (float) $item['support_12_months'];
            }

            $lineTotal = $itemPrice * $item['quantity'];
            $subtotal += $lineTotal;

            // Calculate GST
            $gst = calculateGST($lineTotal, (float) $item['gst_percent']);
            $taxTotal += $gst;
        }

        $discount = 0;
        $couponCode = $_SESSION['coupon_code'] ?? null;
        if ($couponCode) {
            $couponModel = new Coupon();
            $coupon = $couponModel->validate($couponCode, $subtotal, $items);
            if ($coupon) {
                $discount = $couponModel->calculateDiscount($coupon, $subtotal, $items);
            } else {
                unset($_SESSION['coupon_code'], $_SESSION['coupon_id']);
            }
        }

        $total = $subtotal + $taxTotal - $discount;

        return [
            'items'              => $items,
            'subtotal'           => round($subtotal, 2),
            'subtotal_formatted' => formatPrice($subtotal),
            'tax'                => round($taxTotal, 2),
            'tax_formatted'      => formatPrice($taxTotal),
            'discount'           => round($discount, 2),
            'discount_formatted' => formatPrice($discount),
            'total'              => round(max(0, $total), 2),
            'total_formatted'    => formatPrice(max(0, $total)),
            'coupon'             => $couponCode
        ];
    }

    /**
     * Transfer guest cart to user after login
     */
    public function transferToUser(int $userId): void {
        $sessionId = session_id();
        // Get session cart items
        $items = $this->db->fetchAll("SELECT * FROM cart WHERE session_id = ?", [$sessionId]);
        foreach ($items as $item) {
            // Check if user already has this product
            $exists = $this->db->fetch(
                "SELECT id FROM cart WHERE user_id = ? AND product_id = ?",
                [$userId, $item['product_id']]
            );
            if (!$exists) {
                $this->db->update('cart', [
                    'user_id'    => $userId,
                    'session_id' => null
                ], 'id = ?', [$item['id']]);
            } else {
                $this->db->delete('cart', 'id = ?', [$item['id']]);
            }
        }
        updateCartCount();
    }
}
