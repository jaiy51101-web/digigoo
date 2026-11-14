<?php
/**
 * Digiws - Admin User/Customer Controller
 */

class AdminUserController {
    public function __construct() { requireAdmin(); }

    public function index(): void {
        $userModel = new User();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $userModel->getCustomers($page, 20);
        $customers = $result['users'];
        $totalCustomers = $result['total'];
        $pageTitle = 'Customers - Admin';
        $adminPage = 'customers';
        $contentView = VIEWS_PATH . '/admin/customers.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function show(string $id): void {
        $user = (new User())->find((int)$id);
        if (!$user) { setFlash('danger', 'Customer not found.'); redirect(url('admin/customers')); }

        $orderResult = (new Order())->getUserOrders($user['id'], 1, 20);
        $orders = $orderResult['orders'];
        
        $purchasedItems = (new Order())->getUserDownloads($user['id']);

        $pageTitle = $user['name'] . ' - Customer Detail';
        $adminPage = 'customers';
        $contentView = VIEWS_PATH . '/admin/customer-detail.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function updateStatus(string $id): void {
        if (!verifyCsrf()) { setFlash('danger', 'Invalid request.'); redirect(url('admin/customers')); }
        $status = $_POST['status'] ?? 'active';
        (new User())->update((int)$id, ['status' => $status]);
        setFlash('success', 'Customer status updated.');
        redirect(url('admin/customers/' . $id));
    }

    public function delete(string $id): void {
        if (!verifyCsrf()) { setFlash('danger', 'Invalid request.'); redirect(url('admin/customers')); }
        
        $userModel = new User();
        $customer = $userModel->find((int)$id);
        
        if (!$customer) {
            setFlash('danger', 'Customer not found.');
            redirect(url('admin/customers'));
        }
        
        // Check if customer has orders
        $orderModel = new Order();
        $userOrders = $orderModel->getUserOrders((int)$id, 1, 1);
        
        if ($userOrders['total'] > 0) {
            setFlash('danger', 'Cannot delete customer with existing orders.');
            redirect(url('admin/customers'));
        }
        
        // Delete customer
        $userModel->delete((int)$id);
        setFlash('success', 'Customer deleted successfully.');
        redirect(url('admin/customers'));
    }
}
