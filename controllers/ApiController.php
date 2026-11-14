<?php
/**
 * Digiws - API Controller (AJAX endpoints)
 */

class ApiController {

    public function searchProducts(): void {
        $query = sanitize($_GET['q'] ?? '');
        if (strlen($query) < 2) {
            jsonResponse(['products' => []]);
        }
        $products = (new Product())->searchSuggestions($query, 6);
        // Add full URLs
        foreach ($products as &$p) {
            $p['url'] = url('product/' . $p['slug']);
            $p['thumb_url'] = $p['thumbnail'] ? uploadUrl($p['thumbnail']) : asset('images/placeholder.png');
            $p['formatted_price'] = formatPrice($p['sale_price']);
        }
        jsonResponse(['products' => $products]);
    }

    public function cartCount(): void {
        jsonResponse(['count' => cartCount()]);
    }

    public function adminStats(): void {
        requireAdmin();
        $orderModel = new Order();
        jsonResponse([
            'total_revenue'   => $orderModel->getTotalRevenue(),
            'monthly_revenue' => $orderModel->getMonthlyRevenue(),
            'total_orders'    => $orderModel->getTotalOrders(),
            'monthly_sales'   => $orderModel->getMonthlySalesData(12)
        ]);
    }
}
