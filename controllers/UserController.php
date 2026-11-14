<?php
/**
 * Digiws - User Dashboard Controller
 */

class UserController {

    public function __construct() {
        requireLogin();
    }

    public function dashboard(): void {
        $orderModel = new Order();
        $userId = currentUserId();

        $result = $orderModel->getUserOrders($userId, 1, 5);
        $recentOrders = $result['orders'];
        $totalOrders = $result['total'];

        $totalSpent = Database::getInstance()->fetchColumn(
            "SELECT COALESCE(SUM(total), 0) FROM orders WHERE user_id = ? AND payment_status = 'paid'",
            [$userId]
        );

        $totalDownloads = Database::getInstance()->fetchColumn(
            "SELECT COUNT(*) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.user_id = ? AND o.payment_status = 'paid'",
            [$userId]
        );

        $pageTitle = 'Dashboard - ' . APP_NAME;
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/user/dashboard.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function orders(): void {
        $orderModel = new Order();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $orderModel->getUserOrders(currentUserId(), $page, 10);

        $orders = $result['orders'];
        $totalOrders = $result['total'];

        $successOrder = null;
        if (!empty($_GET['success_modal']) && !empty($_GET['order_id'])) {
            $orderId = (int)$_GET['order_id'];
            $order = $orderModel->find($orderId);
            if ($order && $order['user_id'] == currentUserId()) {
                $successOrder = $order;
            }
        }

        $pageTitle = 'My Orders - ' . APP_NAME;
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/user/orders.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function orderDetail(string $id): void {
        $orderModel = new Order();
        $order = $orderModel->find((int)$id);

        if (!$order || $order['user_id'] != currentUserId()) {
            setFlash('danger', 'Order not found.');
            redirect(url('user/orders'));
        }

        $payment = (new Payment())->findByOrderId($order['id']);

        // Determine if order is fully paid (used in view to show/hide retry payment section)
        $isPaid = in_array($order['payment_status'] ?? '', ['paid', 'completed']);

        // Setting model required by payment method toggles and UPI config in view
        $settingModel = new Setting();

        $pageTitle = 'Order #' . $order['order_number'] . ' - ' . APP_NAME;
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/user/order-detail.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function invoice(string $id): void {
        requireLogin();
        $orderModel = new Order();
        $order = $orderModel->find((int)$id);

        if (!$order || $order['user_id'] != currentUserId()) {
            setFlash('danger', 'Order not found.');
            redirect(url('user/orders'));
        }

        $settingModel = new Setting();
        $pageTitle = 'Invoice #' . $order['order_number'];

        include VIEWS_PATH . '/admin/invoice.php';
    }

    public function downloads(): void {
        $orderModel = new Order();
        $downloads = $orderModel->getUserDownloads(currentUserId());

        $pageTitle = 'My Downloads - ' . APP_NAME;
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/user/downloads.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function downloadFile(string $id): void {
        $orderItemId = (int)$id;
        $db = Database::getInstance();

        $item = $db->fetch(
            "SELECT oi.*, o.user_id, o.payment_status, o.status as order_status,
                    p.download_file, p.external_url, p.title 
             FROM order_items oi 
             JOIN orders o ON oi.order_id = o.id 
             JOIN products p ON oi.product_id = p.id 
             WHERE oi.id = ?",
            [$orderItemId]
        );

        if (!$item || $item['user_id'] != currentUserId()) {
            setFlash('danger', 'Access denied.');
            redirect(url('user/downloads'));
        }

        $allowedStatuses = explode(',', (new Setting())->get('downloadable_statuses', 'paid,completed'));
        $allowedStatuses = array_map('trim', $allowedStatuses);
        
        $isAuthorized = in_array($item['payment_status'], $allowedStatuses) || in_array($item['order_status'], $allowedStatuses);

        if (!$isAuthorized) {
            setFlash('warning', 'Your order is still being processed or pending payment.');
            redirect(url('user/downloads'));
        }

        // If no ZIP file is attached, fall back to the product's external_url
        if (empty($item['download_file'])) {
            if (!empty($item['external_url'])) {
                $this->triggerProductDownloadAutomation($item, $orderItemId);
                $db->query("UPDATE order_items SET download_count = download_count + 1 WHERE id = ?", [$orderItemId]);
                redirect($item['external_url']);
            }
            setFlash('warning', 'No download file available for this product.');
            redirect(url('user/downloads'));
        }

        // Parse target download file path or URL based on requested index
        $fileIndex = isset($_GET['file_index']) ? (int)$_GET['file_index'] : null;
        $targetFile = $item['download_file'];
        
        $decoded = json_decode($targetFile, true);
        if (!is_array($decoded) && ($targetFile !== '') && ($targetFile[0] === '[' || $targetFile[0] === '{')) {
            $decoded = json_decode(stripslashes($targetFile), true);
        }
        if (is_array($decoded)) {
            $idx = $fileIndex !== null ? $fileIndex : 0;
            $res = $decoded[$idx] ?? ($decoded[0] ?? null);
            if (is_array($res) && isset($res['url'])) {
                $targetFile = $res['url'];
            } else {
                $targetFile = $res;
            }
        } else {
            $lines = array_filter(explode("\n", str_replace("\r", "", $targetFile)));
            if (count($lines) > 1) {
                $idx = $fileIndex !== null ? $fileIndex : 0;
                $targetFile = $lines[$idx] ?? ($lines[0] ?? '');
            }
        }

        $targetFile = trim($targetFile);
        if (empty($targetFile)) {
            setFlash('warning', 'Requested file is empty or invalid.');
            redirect(url('user/downloads'));
        }

        // If target file is a raw Google Drive ID, convert it to a full URL
        if (strpos($targetFile, 'http') !== 0 && preg_match('/^[a-zA-Z0-9_-]{15,50}$/', $targetFile)) {
            $targetFile = 'https://drive.google.com/file/d/' . $targetFile . '/view?usp=sharing';
        }

        // Target stores an external URL (https://...) — redirect instead of serving locally
        if (strpos($targetFile, 'http') === 0) {
            $this->triggerProductDownloadAutomation($item, $orderItemId);
            $db->query("UPDATE order_items SET download_count = download_count + 1 WHERE id = ?", [$orderItemId]);
            redirect($targetFile);
        }

        $filePath = UPLOADS_PATH . '/' . $targetFile;
        if (!file_exists($filePath)) {
            setFlash('danger', 'File not found on server.');
            redirect(url('user/downloads'));
        }

        // Increment download count
        $this->triggerProductDownloadAutomation($item, $orderItemId);
        $db->query("UPDATE order_items SET download_count = download_count + 1 WHERE id = ?", [$orderItemId]);

        // Serve file
        $filename = sanitize($item['title']) . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache, must-revalidate');
        readfile($filePath);
        exit;
    }

    private function triggerProductDownloadAutomation(array $item, int $orderItemId): void {
        try {
            $user = (new User())->find(currentUserId());
            if ($user) MarketingSubscriber::triggerAutomation('product_download', ['email' => $user['email'], 'order_item_id' => $orderItemId, 'product' => ['title' => $item['title'] ?? '']]);
        } catch (Throwable $e) { error_log('DigiWS product-download automation: ' . $e->getMessage()); }
    }

    public function profile(): void {
        $user = (new User())->find(currentUserId());
        $pageTitle = 'Profile - ' . APP_NAME;
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/user/profile.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function updateProfile(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('user/profile'));
        }

        $userModel = new User();
        $name = sanitize($_POST['name'] ?? '');
        $phoneInput = sanitize($_POST['phone'] ?? '');
        $countryCode = sanitize($_POST['country_code'] ?? '91');
        $phone = cleanPhone($phoneInput, $countryCode);
        $data = ['name' => $name, 'phone' => $phone];

        // Handle avatar upload
        if (!empty($_FILES['avatar']['name'])) {
            $avatar = uploadFile($_FILES['avatar'], 'avatars');
            if ($avatar) $data['avatar'] = $avatar;
        }

        $userModel->update(currentUserId(), $data);
        $_SESSION['user_name'] = $name;
        $_SESSION['user_phone'] = $phone;
        if (isset($data['avatar'])) $_SESSION['user_avatar'] = $data['avatar'];

        setFlash('success', 'Profile updated successfully.');
        redirect(url('user/profile'));
    }

    public function changePasswordForm(): void {
        $pageTitle = 'Change Password - ' . APP_NAME;
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/user/change-password.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function changePassword(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('user/change-password'));
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $userModel = new User();
        $user = $userModel->find(currentUserId());

        if (!password_verify($currentPassword, $user['password'])) {
            setFlash('danger', 'Current password is incorrect.');
            redirect(url('user/change-password'));
        }

        if (strlen($newPassword) < 8) {
            setFlash('danger', 'New password must be at least 8 characters.');
            redirect(url('user/change-password'));
        }

        if ($newPassword !== $confirmPassword) {
            setFlash('danger', 'Passwords do not match.');
            redirect(url('user/change-password'));
        }

        $userModel->updatePassword(currentUserId(), $newPassword);
        setFlash('success', 'Password changed successfully.');
        redirect(url('user/change-password'));
    }

    public function verifyInvoicePublic(string $orderNumber): void {
        $db = Database::getInstance();
        $order = $db->fetch(
            "SELECT o.*, u.name as user_name, u.email as user_email 
             FROM orders o 
             JOIN users u ON o.user_id = u.id 
             WHERE o.order_number = ?",
            [$orderNumber]
        );

        if (!$order) {
            die('Invalid Invoice Reference.');
        }

        $order['items'] = (new Order())->getItems($order['id']);
        $settingModel = new Setting();
        
        $pageTitle = 'Invoice Verification - ' . $orderNumber;
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/invoice-verify.php';
        include TEMPLATES_PATH . '/footer.php';
    }
}
