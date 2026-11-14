<?php
/**
 * Digiws - Admin Coupon Controller
 */

class AdminCouponController {
    public function __construct() { requireAdmin(); }

    public function index(): void {
        $result = (new Coupon())->getAll(max(1, (int)($_GET['page'] ?? 1)));
        $coupons = $result['coupons'];
        $totalCoupons = $result['total'];
        $pageTitle = 'Coupons - Admin';
        $adminPage = 'coupons';
        $contentView = VIEWS_PATH . '/admin/coupons.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function create(): void {
        $coupon = null;
        $products = Database::getInstance()->fetchAll("SELECT id, title FROM products WHERE status = 'active' ORDER BY title ASC");
        $pageTitle = 'Add Coupon - Admin';
        $adminPage = 'coupons';
        $contentView = VIEWS_PATH . '/admin/coupon-form.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function store(): void {
        if (!verifyCsrf()) { setFlash('danger', 'Invalid request.'); redirect(url('admin/coupons')); }
        (new Coupon())->create([
            'code'             => $_POST['code'] ?? '',
            'product_id'       => !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null,
            'discount_type'    => $_POST['discount_type'] ?? 'percentage',
            'discount_value'   => (float)($_POST['discount_value'] ?? 0),
            'min_order_amount' => (float)($_POST['min_order_amount'] ?? 0),
            'max_discount'     => !empty($_POST['max_discount']) ? (float)$_POST['max_discount'] : null,
            'usage_limit'      => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
            'user_usage_limit' => (isset($_POST['user_usage_limit']) && $_POST['user_usage_limit'] !== '') ? (int)$_POST['user_usage_limit'] : null,
            'expires_at'       => $_POST['expires_at'] ?? null,
            'status'           => $_POST['status'] ?? 'active'
        ]);
        setFlash('success', 'Coupon created.');
        redirect(url('admin/coupons'));
    }

    public function edit(string $id): void {
        $coupon = (new Coupon())->find((int)$id);
        if (!$coupon) { setFlash('danger', 'Coupon not found.'); redirect(url('admin/coupons')); }
        $products = Database::getInstance()->fetchAll("SELECT id, title FROM products WHERE status = 'active' ORDER BY title ASC");
        $pageTitle = 'Edit Coupon - Admin';
        $adminPage = 'coupons';
        $contentView = VIEWS_PATH . '/admin/coupon-form.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function update(string $id): void {
        if (!verifyCsrf()) { setFlash('danger', 'Invalid request.'); redirect(url('admin/coupons')); }
        (new Coupon())->update((int)$id, [
            'code'             => $_POST['code'] ?? '',
            'product_id'       => !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null,
            'discount_type'    => $_POST['discount_type'] ?? 'percentage',
            'discount_value'   => (float)($_POST['discount_value'] ?? 0),
            'min_order_amount' => (float)($_POST['min_order_amount'] ?? 0),
            'max_discount'     => !empty($_POST['max_discount']) ? (float)$_POST['max_discount'] : null,
            'usage_limit'      => !empty($_POST['usage_limit']) ? (int)$_POST['usage_limit'] : null,
            'user_usage_limit' => (isset($_POST['user_usage_limit']) && $_POST['user_usage_limit'] !== '') ? (int)$_POST['user_usage_limit'] : null,
            'expires_at'       => $_POST['expires_at'] ?? null,
            'status'           => $_POST['status'] ?? 'active'
        ]);
        setFlash('success', 'Coupon updated.');
        redirect(url('admin/coupons'));
    }

    public function delete(string $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/coupons'));
        }
        (new Coupon())->delete((int)$id);
        setFlash('success', 'Coupon deleted.');
        redirect(url('admin/coupons'));
    }
}
