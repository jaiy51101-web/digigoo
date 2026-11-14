<?php
/**
 * Digiws - Admin Dashboard Controller
 */

class AdminController {

    public function loginForm(): void {
        if (isAdmin()) redirect(url('admin'));
        $pageTitle = 'Admin Login - ' . APP_NAME;
        include VIEWS_PATH . '/admin/login.php';
    }

    public function login(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/login'));
        }

        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $userModel = new User();
        $user = $userModel->authenticate($email, $password);

        if (!$user || $user['role'] !== 'admin') {
            setFlash('danger', 'Invalid credentials or insufficient permissions.');
            redirect(url('admin/login'));
        }

        $userModel->setSession($user);
        redirect(url('admin'));
    }

    public function logout(): void {
        (new User())->destroySession();
        setFlash('success', 'Logged out successfully.');
        redirect(url('admin/login'));
    }

    public function dashboard(): void {
        requireAdmin();

        $orderModel = new Order();
        $productModel = new Product();
        $userModel = new User();

        $db = Database::getInstance();
        $pendingRequests = $db->fetch("SELECT COUNT(*) as count FROM website_requests WHERE status = 'pending'")['count'] ?? 0;

        $stats = [
            'total_revenue'    => $orderModel->getTotalRevenue(),
            'monthly_revenue'  => $orderModel->getMonthlyRevenue(),
            'total_orders'     => $orderModel->getTotalOrders(),
            'total_products'   => $productModel->getTotalProducts(),
            'total_customers'  => $userModel->getTotalCustomers(),
            'pending_requests' => $pendingRequests,
        ];

        $recentOrders = $orderModel->getRecentOrders(8);
        $monthlySales = $orderModel->getMonthlySalesData(12);
        $ordersByStatus = $orderModel->getOrdersByStatus();
        $recentCustomers = $userModel->getRecentCustomers(5);

        $pageTitle = 'Admin Dashboard - ' . APP_NAME;
        include VIEWS_PATH . '/admin/layout.php';
    }
}
