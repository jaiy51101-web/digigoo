<?php
/**
 * Digiws - Cart Controller
 */

class CartController {

    public function index(): void {
        $cartModel = new Cart();
        $cartData = $cartModel->getTotal();

        $pageTitle = 'Shopping Cart - ' . APP_NAME;
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/cart.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function add(): void {
        $productId = (int)($_POST['product_id'] ?? 0);
        $setupIncluded = (int)($_POST['setup_included'] ?? 0);
        $supportMonths = (int)($_POST['support_months'] ?? 0);

        if ($productId <= 0) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                jsonResponse(['success' => false, 'message' => 'Invalid product.']);
            }
            setFlash('danger', 'Invalid product.');
            redirect(url('products'));
        }

        // Verify product exists and is active
        $product = (new Product())->find($productId);
        if (!$product || $product['status'] !== 'active') {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                jsonResponse(['success' => false, 'message' => 'Product not available.']);
            }
            setFlash('danger', 'Product not available.');
            redirect(url('products'));
        }

        $cartModel = new Cart();
        $cartModel->add($productId, $setupIncluded, $supportMonths);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            jsonResponse([
                'success' => true,
                'message' => 'Product added to cart!',
                'cart_count' => cartCount()
            ]);
        }

        setFlash('success', 'Product added to cart!');
        redirect(url('cart'));
    }

    public function update(): void {
        $productId = (int)($_POST['product_id'] ?? 0);
        $data = [];

        if (isset($_POST['setup_included'])) $data['setup_included'] = (int)$_POST['setup_included'];
        if (isset($_POST['support_months'])) $data['support_months'] = (int)$_POST['support_months'];
        if (isset($_POST['quantity'])) $data['quantity'] = max(1, (int)$_POST['quantity']);

        $cartModel = new Cart();
        $cartModel->updateItem($productId, $data);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $cartData = $cartModel->getTotal();
            jsonResponse([
                'success' => true,
                'message' => 'Cart updated.',
                'cart' => $cartData
            ]);
        }

        setFlash('success', 'Cart updated.');
        redirect(url('cart'));
    }

    public function remove(): void {
        $productId = (int)($_POST['product_id'] ?? 0);
        $cartModel = new Cart();
        $cartModel->remove($productId);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $cartData = $cartModel->getTotal();
            jsonResponse([
                'success' => true,
                'message' => 'Item removed from cart.',
                'cart_count' => cartCount(),
                'cart' => $cartData
            ]);
        }

        setFlash('success', 'Item removed from cart.');
        redirect(url('cart'));
    }

    public function applyCoupon(): void {
        $code = sanitize($_POST['coupon_code'] ?? '');
        if (empty($code)) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                jsonResponse(['success' => false, 'message' => 'Please enter a coupon code.']);
            }
            setFlash('danger', 'Please enter a coupon code.');
            redirect(url('cart'));
        }

        $cartModel = new Cart();
        $cartData = $cartModel->getTotal();
        $couponModel = new Coupon();
        $coupon = $couponModel->validate($code, $cartData['subtotal'], $cartData['items'] ?? []);

        if (!$coupon) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                jsonResponse(['success' => false, 'message' => 'Invalid or expired coupon code.']);
            }
            setFlash('danger', 'Invalid or expired coupon code.');
            redirect(url('cart'));
        }

        $_SESSION['coupon_code'] = $coupon['code'];
        $_SESSION['coupon_id'] = $coupon['id'];

        $cartData = $cartModel->getTotal();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            jsonResponse([
                'success' => true,
                'message' => 'Coupon applied successfully!',
                'cart' => $cartData
            ]);
        }

        setFlash('success', 'Coupon applied: ' . formatPrice($cartData['discount']) . ' discount!');
        redirect(url('cart'));
    }

    public function removeCoupon(): void {
        unset($_SESSION['coupon_code'], $_SESSION['coupon_id']);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $cartModel = new Cart();
            jsonResponse([
                'success' => true,
                'message' => 'Coupon removed.',
                'cart' => $cartModel->getTotal()
            ]);
        }

        setFlash('success', 'Coupon removed.');
        redirect(url('cart'));
    }

    public function checkout(): void {
        // requireLogin(); // Guest checkout allowed

        $cartModel = new Cart();
        $cartData = $cartModel->getTotal();

        if (empty($cartData['items'])) {
            setFlash('warning', 'Your cart is empty.');
            redirect(url('products'));
        }

        $settingModel = new Setting();
        $pageTitle = 'Checkout - ' . APP_NAME;

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/checkout.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function processCheckout(): void {
        if (!verifyCsrf()) {
            jsonResponse(['success' => false, 'message' => 'Invalid request.'], 403);
        }

        $cartModel = new Cart();
        $cartData = $cartModel->getTotal();

        if (empty($cartData['items'])) {
            jsonResponse(['success' => false, 'message' => 'Cart is empty.']);
        }

        $userModel = new User();
        $db = Database::getInstance();

        // Guest Checkout / Auto-registration
        if (!isLoggedIn()) {
            $firstName = sanitize($_POST['first_name'] ?? '');
            $lastName = sanitize($_POST['last_name'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $phoneInput = sanitize($_POST['phone'] ?? '');
            $countryCode = sanitize($_POST['country_code'] ?? '91');
            $phone = cleanPhone($phoneInput, $countryCode);
            $password = $_POST['password'] ?? '';

            if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password)) {
                jsonResponse(['success' => false, 'message' => 'Please fill in all required fields.']);
            }

            if ($userModel->emailExists($email)) {
                jsonResponse(['success' => false, 'message' => 'An account with this email already exists. Please login.']);
            }

            $userId = $userModel->create([
                'name'     => $firstName . ' ' . $lastName,
                'email'    => $email,
                'password' => $password,
                'phone'    => $phone
            ]);

            $user = $userModel->find($userId);
            $userModel->setSession($user);
        } else {
            // Update phone if provided
            $phoneInput = sanitize($_POST['phone'] ?? '');
            $countryCode = sanitize($_POST['country_code'] ?? '91');
            $phone = cleanPhone($phoneInput, $countryCode);
            if ($phone) {
                $userModel->update(currentUserId(), ['phone' => $phone]);
            }
        }

        $paymentMethod = sanitize($_POST['payment_method'] ?? 'razorpay');

        // Security check and auto-completion for free orders
        if ($cartData['total'] <= 0) {
            $paymentMethod = 'free';
        } elseif ($paymentMethod === 'free') {
            // Prevent fraud (trying to checkout with 'free' method when total is > 0)
            jsonResponse(['success' => false, 'message' => 'Invalid payment method for this order.']);
        }

        $db->beginTransaction();

        try {
            $orderModel = new Order();
            $licenseModel = new LicenseKey();

            // Create order
            $orderId = $orderModel->create([
                'user_id'        => currentUserId(),
                'subtotal'       => $cartData['subtotal'],
                'discount'       => $cartData['discount'],
                'tax_amount'     => $cartData['tax'],
                'total'          => $cartData['total'],
                'coupon_id'      => $_SESSION['coupon_id'] ?? null,
                'coupon_code'    => $cartData['coupon'] ?? null,
                'payment_method' => $paymentMethod
            ]);

            $order = $orderModel->find($orderId);

            // Add order items
            foreach ($cartData['items'] as $item) {
                $itemPrice = (float)$item['sale_price'];
                $setupPrice = 0;
                $supportPrice = 0;

                if ($item['setup_included'] && $item['setup_option']) {
                    $setupPrice = (float)$item['setup_price'];
                }
                if ($item['support_months'] == 6) {
                    $supportPrice = (float)$item['support_6_months'];
                } elseif ($item['support_months'] == 12) {
                    $supportPrice = (float)$item['support_12_months'];
                }

                $lineTotal = ($itemPrice + $setupPrice + $supportPrice) * $item['quantity'];

                // Add order item first
                $orderItemId = $orderModel->addItem($orderId, [
                    'product_id'     => $item['product_id'],
                    'product_title'  => $item['title'],
                    'price'          => $itemPrice,
                    'quantity'       => $item['quantity'],
                    'setup_included' => $item['setup_included'],
                    'setup_price'    => $setupPrice,
                    'support_months' => $item['support_months'],
                    'support_price'  => $supportPrice,
                    'subtotal'       => $lineTotal,
                    'license_key'    => null
                ]);

                // Assign license key to the NEW order item
                $licenseKey = $licenseModel->assignToOrder($item['product_id'], $orderItemId);

                // Update the order item with the assigned key
                $orderModel->updateItem($orderItemId, ['license_key' => $licenseKey]);
            }

            // Increment coupon usage
            if (!empty($_SESSION['coupon_id'])) {
                (new Coupon())->incrementUsage($_SESSION['coupon_id']);
            }

            // If it is a free order, mark payment paid and activate support
            if ($paymentMethod === 'free') {
                try {
                    $orderModel->updatePaymentStatus($orderId, 'paid', 'free');
                } catch (Throwable $e) {}

                try {
                    $orderModel->activateOrderSupport($orderId);
                } catch (Throwable $e) {}

                try {
                    (new PaymentController())->triggerDigiwsLicenseGeneration($orderId);
                } catch (Throwable $e) {}

                try {
                    (new Payment())->create([
                        'order_id'       => $orderId,
                        'user_id'        => currentUserId(),
                        'gateway'        => 'free',
                        'amount'         => 0.00,
                        'status'         => 'success',
                        'transaction_id' => 'FREE-' . strtoupper(uniqid())
                    ]);
                } catch (Throwable $e) {}

                try {
                    $productModel = new Product();
                    foreach ($cartData['items'] as $item) {
                        $productModel->incrementSales($item['product_id'], $item['quantity']);
                    }
                } catch (Throwable $e) {}

                try {
                    (new Cart())->clear();
                } catch (Throwable $e) {}
            }

            $paymentUrl = null;

            $db->commit();

            // Send order placement / confirmation email
            try {
                $user = (new User())->find(currentUserId());
                $updatedOrder = $orderModel->find($orderId);
                if ($user && $updatedOrder) {
                    $mailer = new Mailer();
                    $mailer->sendOrderConfirmation($user, $updatedOrder);
                    if ($paymentMethod === 'free') {
                        $mailer->sendDownloadsReady($user, $updatedOrder);
                    }
                }
            } catch (Throwable $e) {
                error_log('Free order mailer error: ' . $e->getMessage());
            }

            jsonResponse([
                'success'      => true,
                'order_id'     => $orderId,
                'order_number' => $order['order_number'],
                'total'        => $cartData['total'],
                'payment_url'  => $paymentUrl,
                'redirect_url' => url('user/orders?success_modal=1&order_id=' . $orderId),
                'message'      => 'Order created.'
            ]);

        } catch (Throwable $e) {
            $db->rollback();
            jsonResponse(['success' => false, 'message' => 'Failed to create order. ' . ($e->getMessage())], 500);
        }
    }

    /**
     * Add product support renewal option to cart and redirect to checkout
     */
    public function addSupport(): void {
        $productId = (int)($_GET['product_id'] ?? 0);
        $months = (int)($_GET['months'] ?? 6);
        
        if ($productId <= 0 || ($months !== 6 && $months !== 12)) {
            setFlash('danger', 'Invalid support renewal parameters.');
            redirect(url('user/orders'));
        }
        
        // Verify product exists
        $product = (new Product())->find($productId);
        if (!$product || $product['status'] !== 'active') {
            setFlash('danger', 'Product not available.');
            redirect(url('user/orders'));
        }
        
        $cartModel = new Cart();
        // Remove product from cart first if it exists to override setup / months settings
        $cartModel->remove($productId);
        
        // Add item to cart with setup=0 and support_months=6 or 12
        $cartModel->add($productId, 0, $months);
        
        setFlash('success', 'Support renewal for ' . htmlspecialchars($product['title']) . ' added to your cart.');
        redirect(url('checkout'));
    }
}
