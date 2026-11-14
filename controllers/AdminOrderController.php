<?php
/**
 * Digiws - Admin Order Controller
 */

class AdminOrderController {
    public function __construct() { requireAdmin(); }

    public function index(): void {
        $orderModel = new Order();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'status'         => $_GET['status'] ?? '',
            'payment_status' => $_GET['payment_status'] ?? '',
            'search'         => $_GET['q'] ?? ''
        ];

        $result = $orderModel->getAll($page, 20, $filters);
        $orders = $result['orders'];
        $totalOrders = $result['total'];

        $pageTitle = 'Orders - Admin';
        $adminPage = 'orders';
        $contentView = VIEWS_PATH . '/admin/orders.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function show(string $id): void {
        $order = (new Order())->find((int)$id);
        if (!$order) {
            setFlash('danger', 'Order not found.');
            redirect(url('admin/orders'));
        }

        $payment = (new Payment())->findByOrderId($order['id']);

        $pageTitle = 'Order #' . $order['order_number'] . ' - Admin';
        $adminPage = 'orders';
        $contentView = VIEWS_PATH . '/admin/order-detail.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function updateStatus(string $id): void {
        if (!verifyCsrf()) { setFlash('danger', 'Invalid request.'); redirect(url('admin/orders')); }

        $status = $_POST['status'] ?? '';
        $paymentStatus = $_POST['payment_status'] ?? '';
        
        $orderModel = new Order();
        $productModel = new Product();
        $paymentModel = new Payment();

        $oldOrder = $orderModel->find((int)$id);
        if (!$oldOrder) {
            setFlash('danger', 'Order not found.');
            redirect(url('admin/orders'));
        }

        $wasPaid = in_array($oldOrder['payment_status'] ?? '', ['paid', 'completed']);
        $wasFailed = (($oldOrder['payment_status'] ?? '') === 'failed');

        // Update order status
        if ($status) {
            $orderModel->updateStatus((int)$id, $status);
        }
        
        // Update payment status if provided
        if ($paymentStatus) {
            $orderModel->updatePaymentStatus((int)$id, $paymentStatus);
            
            // Also update the payments table status
            $payment = $paymentModel->findByOrderId((int)$id);
            if ($payment) {
                $payStatus = 'pending';
                if ($paymentStatus === 'paid') $payStatus = 'success';
                elseif ($paymentStatus === 'failed') $payStatus = 'failed';
                
                $paymentModel->updateStatus($payment['id'], $payStatus);
            } else {
                // If payment record doesn't exist, create it on transitions
                if ($paymentStatus === 'paid') {
                    $paymentModel->create([
                        'order_id'       => (int)$id,
                        'user_id'        => $oldOrder['user_id'],
                        'gateway'        => $oldOrder['payment_method'] ?? 'manual',
                        'transaction_id' => 'ADMIN-' . strtoupper(uniqid()),
                        'amount'         => $oldOrder['total'],
                        'status'         => 'success'
                    ]);
                }
            }
        }

        // Fetch fresh order details and send email notifications
        $order = $orderModel->find((int)$id);
        if ($order) {
            try {
                $user = (new User())->find($order['user_id']);
                $mailer = new Mailer();

                $isPaidNow = in_array($order['payment_status'] ?? '', ['paid', 'completed']);
                $isFailedNow = (($order['payment_status'] ?? '') === 'failed');

                // Case 1: Payment status changed to paid just now
                if ($isPaidNow && !$wasPaid) {
                    // Activate support validity
                    $orderModel->activateOrderSupport((int)$id);
                    
                    // Increment product sales
                    foreach ($order['items'] as $item) {
                        $productModel->incrementSales($item['product_id'], $item['quantity']);
                    }
                    
                    // Send confirmation + downloads ready emails
                    $mailer->sendOrderConfirmation($user, $order);
                    $mailer->sendDownloadsReady($user, $order);

                    // Send WhatsApp order confirmation
                    $settingModel = new Setting();
                    if ($settingModel->get('whatsapp_enabled') === 'Yes') {
                        try {
                            $whatsapp = new WhatsAppService();
                            $whatsapp->sendOrderConfirmation($user, $order);
                        } catch (Exception $e) {
                            error_log('DigiWS WhatsApp alert dispatch failure: ' . $e->getMessage());
                        }
                    }
                }
                // Case 2: Payment status changed to failed just now
                elseif ($isFailedNow && !$wasFailed) {
                    $mailer->sendPaymentFailed($user, $order);
                }

                // Case 3: Order status changed (e.g. pending, completed, cancelled, refunded)
                if ($status && $status !== $oldOrder['status']) {
                    $mailer->sendOrderStatus($user, $order);

                    // Send WhatsApp status update
                    $settingModel = new Setting();
                    if ($settingModel->get('whatsapp_enabled') === 'Yes') {
                        try {
                            $whatsapp = new WhatsAppService();
                            $whatsapp->sendOrderStatusUpdate($user, $order, $status);
                        } catch (Exception $e) {
                            error_log('DigiWS WhatsApp status alert dispatch failure: ' . $e->getMessage());
                        }
                    }
                }
            } catch (Exception $e) {
                // Don't crash on email failure
            }
        }

        setFlash('success', 'Order and Payment status updated.');
        redirect(url('admin/orders/' . $id));
    }

    public function invoice(string $id): void {
        $order = (new Order())->find((int)$id);
        if (!$order) {
            setFlash('danger', 'Order not found.');
            redirect(url('admin/orders'));
        }

        $settingModel = new Setting();
        $pageTitle = 'Invoice #' . $order['order_number'];

        include VIEWS_PATH . '/admin/invoice.php';
    }

    public function delete(string $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/orders'));
        }

        $orderModel = new Order();
        $order = $orderModel->find((int)$id);
        if (!$order) {
            setFlash('danger', 'Order not found.');
            redirect(url('admin/orders'));
        }

        $db = Database::getInstance();
        $db->beginTransaction();
        try {
            $db->delete('order_items', 'order_id = ?', [$order['id']]);
            $db->delete('payments', 'order_id = ?', [$order['id']]);
            $db->delete('orders', 'id = ?', [$order['id']]);
            $db->commit();
            setFlash('success', 'Order deleted successfully.');
        } catch (Exception $e) {
            $db->rollback();
            setFlash('danger', 'Failed to delete order.');
        }

        redirect(url('admin/orders'));
    }
}
