<?php
/**
 * Digiws - Payment Controller
 */

class PaymentController {

    private function paymentSettings(): array {
        return (new Setting())->getGroup('payment');
    }

    private function razorpayCredentials(): array {
        $settings = $this->paymentSettings();
        return [
            'key_id' => trim($settings['razorpay_key_id'] ?? '') ?: RAZORPAY_KEY_ID,
            'secret' => trim($settings['razorpay_key_secret'] ?? '') ?: RAZORPAY_KEY_SECRET,
        ];
    }

    private function requirePaymentCsrf(): void {
        if (!verifyCsrf()) jsonResponse(['success' => false, 'message' => 'Invalid security token.'], 403);
    }

    private function ownedPendingPayment(int $orderId, string $gateway, string $gatewayOrderId): ?array {
        $order = (new Order())->find($orderId);
        if (!$order || (int)$order['user_id'] !== currentUserId() || $order['payment_status'] === 'paid') return null;
        $payment = (new Payment())->findByGatewayOrderId($gatewayOrderId);
        if (!$payment || (int)$payment['order_id'] !== $orderId || $payment['gateway'] !== $gateway || $payment['status'] !== 'pending') return null;
        return $order;
    }

    // ==================== RAZORPAY ====================
    public function razorpayCreate(): void {
        requireLogin();
        $this->requirePaymentCsrf();
        $orderId = (int)($_POST['order_id'] ?? 0);
        $order = (new Order())->find($orderId);

        if (!$order || $order['user_id'] != currentUserId()) {
            jsonResponse(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $credentials = $this->razorpayCredentials();
        if (empty($credentials['key_id']) || empty($credentials['secret'])) {
            jsonResponse(['success' => false, 'message' => 'Razorpay is not configured.'], 500);
        }

        $amountInPaise = (int)($order['total'] * 100);
        $receiptId = $order['order_number'];

        // Create Razorpay order via API
        $apiData = json_encode([
            'amount'   => $amountInPaise,
            'currency' => 'INR',
            'receipt'  => $receiptId
        ]);

        $ch = curl_init('https://api.razorpay.com/v1/orders');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $apiData,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_USERPWD        => $credentials['key_id'] . ':' . $credentials['secret'],
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30
        ]);
        $response = curl_exec($ch);
        $curlErr = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            error_log("Razorpay Connection Fail cURL Error: " . $curlErr);
            jsonResponse([
                'success' => false,
                'message' => 'Connection to Razorpay API failed. cURL Error: ' . $curlErr
            ], 500);
        }

        $result = json_decode($response, true);

        if ($httpCode !== 200 || empty($result['id'])) {
            $errorDesc = $result['error']['description'] ?? 'Unknown Razorpay Error';
            $errorCode = $result['error']['code'] ?? 'UNKNOWN_CODE';
            error_log("Razorpay API Error: Code=$errorCode, Description=$errorDesc. Response: " . $response);
            
            jsonResponse([
                'success' => false,
                'message' => 'Failed to create payment order. Error: ' . $errorDesc,
                'debug' => [
                    'http_code' => $httpCode,
                    'error_code' => $errorCode,
                    'error_description' => $errorDesc,
                    'raw_response' => $result
                ]
            ], 500);
        }

        // Save payment record
        (new Payment())->create([
            'order_id'         => $orderId,
            'user_id'          => currentUserId(),
            'gateway'          => 'razorpay',
            'gateway_order_id' => $result['id'],
            'amount'           => $order['total'],
            'currency'         => 'INR',
            'status'           => 'pending'
        ]);

        $settingModel = new Setting();
        $siteFavicon = $settingModel->get('site_favicon', '');
        $siteLogo = $settingModel->get('site_logo', '');
        $logoUrl = '';
        if (!empty($siteFavicon)) {
            $logoUrl = uploadUrl($siteFavicon);
        } elseif (!empty($siteLogo)) {
            $logoUrl = uploadUrl($siteLogo);
        }

        jsonResponse([
            'success'           => true,
            'razorpay_order_id' => $result['id'],
            'amount'            => $amountInPaise,
            'currency'          => 'INR',
            'key'               => $credentials['key_id'],
            'order_number'      => $order['order_number'],
            'name'              => $settingModel->get('site_name', 'DigiWS Private Limited'),
            'logo'              => $logoUrl,
            'customer_name'     => $order['user_name'] ?? '',
            'customer_email'    => $order['user_email'] ?? '',
            'customer_phone'    => $order['user_phone'] ?? ''
        ]);
    }

    public function razorpayVerify(): void {
        requireLogin();
        $this->requirePaymentCsrf();

        $razorpayPaymentId = $_POST['razorpay_payment_id'] ?? '';
        $razorpayOrderId = $_POST['razorpay_order_id'] ?? '';
        $razorpaySignature = $_POST['razorpay_signature'] ?? '';
        $orderId = (int)($_POST['order_id'] ?? 0);

        $credentials = $this->razorpayCredentials();
        $order = $this->ownedPendingPayment($orderId, 'razorpay', $razorpayOrderId);
        if (!$order || empty($razorpayPaymentId) || empty($razorpaySignature) || empty($credentials['secret'])) {
            jsonResponse(['success' => false, 'message' => 'Invalid Razorpay payment verification request.'], 400);
        }
        // Verify signature only after confirming the gateway order belongs to this user/order.
        $expectedSignature = hash_hmac('sha256', $razorpayOrderId . '|' . $razorpayPaymentId, $credentials['secret']);

        if (hash_equals($expectedSignature, $razorpaySignature)) {
            $this->completePayment($orderId, 'razorpay', $razorpayPaymentId, $razorpayOrderId);
            (new Cart())->clear();
            jsonResponse(['success' => true, 'redirect' => url('pay/success?order=' . $orderId)]);
        } else {
            $this->failPayment($orderId, 'razorpay', $razorpayOrderId);
            jsonResponse(['success' => false, 'redirect' => url('pay/failed?order=' . $orderId)]);
        }
    }

    // ==================== STRIPE ====================
    public function stripeCreate(): void {
        requireLogin();
        $orderId = (int)($_POST['order_id'] ?? 0);
        $order = (new Order())->find($orderId);

        if (!$order || $order['user_id'] != currentUserId()) {
            jsonResponse(['success' => false, 'message' => 'Order not found.'], 404);
        }

        if (empty(STRIPE_SECRET_KEY)) {
            jsonResponse(['success' => false, 'message' => 'Stripe is not configured.'], 500);
        }

        $amountInCents = (int)($order['total'] * 100);

        $ch = curl_init('https://api.stripe.com/v1/payment_intents');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'amount'   => $amountInCents,
                'currency' => 'inr',
                'metadata' => ['order_id' => $orderId, 'order_number' => $order['order_number']]
            ]),
            CURLOPT_USERPWD => STRIPE_SECRET_KEY . ':'
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (empty($result['client_secret'])) {
            jsonResponse(['success' => false, 'message' => 'Failed to create payment intent.'], 500);
        }

        (new Payment())->create([
            'order_id'         => $orderId,
            'user_id'          => currentUserId(),
            'gateway'          => 'stripe',
            'gateway_order_id' => $result['id'],
            'amount'           => $order['total'],
            'status'           => 'pending'
        ]);

        jsonResponse([
            'success'       => true,
            'client_secret' => $result['client_secret'],
            'publishable_key' => STRIPE_PUBLISHABLE_KEY
        ]);
    }

    public function stripeVerify(): void {
        requireLogin();
        $paymentIntentId = $_POST['payment_intent_id'] ?? '';
        $orderId = (int)($_POST['order_id'] ?? 0);

        $ch = curl_init('https://api.stripe.com/v1/payment_intents/' . $paymentIntentId);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => STRIPE_SECRET_KEY . ':'
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (($result['status'] ?? '') === 'succeeded') {
            $this->completePayment($orderId, 'stripe', $paymentIntentId, $result['id']);
            (new Cart())->clear();
            jsonResponse(['success' => true, 'redirect' => url('pay/success?order=' . $orderId)]);
        } else {
            $this->failPayment($orderId, 'stripe', $result['id'] ?? '');
            jsonResponse(['success' => false, 'redirect' => url('pay/failed?order=' . $orderId)]);
        }
    }


    // ==================== PAYPAL ====================
    public function paypalCreate(): void {
        requireLogin();
        $this->requirePaymentCsrf();
        $orderId = (int)($_POST['order_id'] ?? 0);
        $order = (new Order())->find($orderId);

        if (!$order || $order['user_id'] != currentUserId()) {
            jsonResponse(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $settings = $this->paymentSettings();
        $clientId = trim($settings['paypal_client_id'] ?? '') ?: PAYPAL_CLIENT_ID;
        $secret = trim($settings['paypal_secret'] ?? '') ?: PAYPAL_SECRET;
        $mode = $settings['paypal_mode'] ?? PAYPAL_MODE;
        $currency = strtoupper($settings['currency'] ?? 'USD');
        $supportedCurrencies = ['AUD','BRL','CAD','CHF','CZK','DKK','EUR','GBP','HKD','HUF','ILS','JPY','MXN','MYR','NOK','NZD','PHP','PLN','SEK','SGD','THB','TWD','USD'];
        
        $payPalAmount = $order['total'];
        $payPalCurrency = $currency;

        if ($currency === 'INR' || !in_array($currency, $supportedCurrencies, true)) {
            $payPalCurrency = 'USD';
            $payPalAmount = $order['total'] * getUsdExchangeRate();
        }

        if (empty($clientId) || empty($secret)) {
            jsonResponse(['success' => false, 'message' => 'PayPal is not configured.'], 500);
        }

        $baseUrl = $mode === 'sandbox' 
            ? 'https://api-m.sandbox.paypal.com' 
            : 'https://api-m.paypal.com';

        $ch = curl_init($baseUrl . '/v1/oauth2/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
            CURLOPT_USERPWD        => $clientId . ':' . $secret,
            CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 30
        ]);
        $tokenResponse = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $accessToken = $tokenResponse['access_token'] ?? '';
        if (empty($accessToken)) {
            jsonResponse(['success' => false, 'message' => 'PayPal authentication failed.'], 500);
        }

        $ch = curl_init($baseUrl . '/v2/checkout/orders');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'intent' => 'CAPTURE',
                'application_context' => [
                    'return_url'   => url("pay/paypal/verify?order_id={$orderId}"),
                    'cancel_url'   => url("pay/failed?order={$orderId}"),
                    'landing_page' => 'BILLING',
                    'user_action'  => 'PAY_NOW'
                ],
                'purchase_units' => [[
                    'reference_id' => $order['order_number'],
                    'amount' => [
                        'currency_code' => $payPalCurrency,
                        'value' => number_format($payPalAmount, 2, '.', '')
                    ]
                ]]
            ]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ], CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 30
        ]);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (empty($result['id'])) {
            $errDetail = '';
            if (!empty($result['message'])) {
                $errDetail = ': ' . $result['message'];
                if (!empty($result['details'])) {
                    $details = [];
                    foreach ($result['details'] as $d) {
                        $details[] = ($d['issue'] ?? '') . ' (' . ($d['field'] ?? '') . ')';
                    }
                    $errDetail .= ' - ' . implode(', ', $details);
                }
            }
            jsonResponse(['success' => false, 'message' => 'Failed to create PayPal order' . $errDetail], 500);
        }

        $checkoutUrl = '';
        foreach ($result['links'] ?? [] as $link) {
            if ($link['rel'] === 'approve') {
                $checkoutUrl = $link['href'];
                break;
            }
        }

        if (empty($checkoutUrl)) {
            jsonResponse(['success' => false, 'message' => 'Failed to retrieve PayPal checkout URL.'], 500);
        }

        (new Payment())->create([
            'order_id'         => $orderId,
            'user_id'          => currentUserId(),
            'gateway'          => 'paypal',
            'gateway_order_id' => $result['id'],
            'amount'           => $payPalAmount,
            'currency'         => $payPalCurrency,
            'status'           => 'pending'
        ]);

        jsonResponse([
            'success'         => true, 
            'paypal_order_id' => $result['id'], 
            'checkout_url'    => $checkoutUrl
        ]);
    }

    public function paypalVerify(): void {
        requireLogin();
        $paypalOrderId = $_GET['token'] ?? '';
        $orderId = (int)($_GET['order_id'] ?? 0);

        $settings = $this->paymentSettings();
        $clientId = trim($settings['paypal_client_id'] ?? '') ?: PAYPAL_CLIENT_ID;
        $secret = trim($settings['paypal_secret'] ?? '') ?: PAYPAL_SECRET;
        $mode = $settings['paypal_mode'] ?? PAYPAL_MODE;

        $baseUrl = $mode === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        $order = $this->ownedPendingPayment($orderId, 'paypal', $paypalOrderId);
        if (!$order || empty($paypalOrderId) || empty($clientId) || empty($secret)) {
            redirect(url('pay/failed?order=' . $orderId));
            return;
        }
        // Get token
        $ch = curl_init($baseUrl . '/v1/oauth2/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
            CURLOPT_USERPWD        => $clientId . ':' . $secret,
            CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 30
        ]);
        $tokenResponse = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $accessToken = $tokenResponse['access_token'] ?? '';

        if (empty($accessToken)) { $this->failPayment($orderId, 'paypal', $paypalOrderId); redirect(url('pay/failed?order=' . $orderId)); return; }
        // Capture payment
        $ch = curl_init($baseUrl . '/v2/checkout/orders/' . $paypalOrderId . '/capture');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => '{}',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ], CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 30
        ]);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (($result['status'] ?? '') === 'COMPLETED') {
            $captureId = $result['purchase_units'][0]['payments']['captures'][0]['id'] ?? '';
            $this->completePayment($orderId, 'paypal', $captureId, $paypalOrderId);
            (new Cart())->clear();
            jsonResponse(['success' => true, 'redirect' => url('pay/success?order=' . $orderId)]);
        } else {
            $this->failPayment($orderId, 'paypal', $paypalOrderId);
            jsonResponse(['success' => false, 'redirect' => url('pay/failed?order=' . $orderId)]);
        }
    }

    // Paytm gateway removed as per user request

    // ==================== COMMON ====================
    private function completePayment(int $orderId, string $gateway, string $txnId, string $gatewayOrderId = ''): void {
        $orderModel = new Order();
        $paymentModel = new Payment();
        $productModel = new Product();

        $existingOrder = $orderModel->find($orderId);
        if (!$existingOrder || $existingOrder['payment_status'] === 'paid') return;
        // Update order
        $orderModel->updatePaymentStatus($orderId, 'paid', $gateway);

        // Activate support validity for all items in the order
        $orderModel->activateOrderSupport($orderId);

        // Update payment record
        $payment = $paymentModel->findByOrderId($orderId);
        if ($payment) {
            $paymentModel->updateStatus($payment['id'], 'success', $txnId, json_encode(['gateway_order_id' => $gatewayOrderId]));
        } else {
            // Create payment record if it doesn't exist (e.g. for Digiws redirect)
            $paymentModel->create([
                'order_id'         => $orderId,
                'user_id'          => $orderModel->find($orderId)['user_id'],
                'gateway'          => $gateway,
                'transaction_id'   => $txnId,
                'gateway_order_id' => $gatewayOrderId,
                'amount'           => $orderModel->find($orderId)['total'],
                'status'           => 'success'
            ]);
        }

        // Increment product sales
        $order = $orderModel->find($orderId);
        if ($order) {
            foreach ($order['items'] as $item) {
                $productModel->incrementSales($item['product_id'], $item['quantity']);
            }

            // Send confirmation email + downloads ready email
            try {
                $user = (new User())->find($order['user_id']);
                $mailer = new Mailer();
                $mailer->sendOrderConfirmation($user, $order);
                $mailer->sendDownloadsReady($user, $order);
            } catch (Exception $e) {
                // Don't fail on email error
            }

            // Send WhatsApp confirmation
            $settingModel = new Setting();
            if ($settingModel->get('whatsapp_enabled') === 'Yes') {
                try {
                    $whatsapp = new WhatsAppService();
                    $whatsapp->sendOrderConfirmation($user, $order);
                } catch (Exception $e) {
                    error_log('DigiWS WhatsApp alert dispatch failure: ' . $e->getMessage());
                }
            }

            // Trigger automatic License Key & API Key generation on Central License Server
            $this->triggerDigiwsLicenseGeneration($orderId);
        }
    }

    public function triggerDigiwsLicenseGeneration(int $orderId): void {
        try {
            $orderModel = new Order();
            $order = $orderModel->find($orderId);
            if (!$order || empty($order['items'])) return;

            $userModel = new User();
            $user = $userModel->find($order['user_id']);
            if (!$user) return;

            $productModel = new Product();
            $db = Database::getInstance();

            foreach ($order['items'] as $item) {
                $product = $productModel->find($item['product_id']);
                if (!$product) continue;

                $digiwsProductId = !empty($product['digiws_product_id'])
                    ? $product['digiws_product_id']
                    : (!empty($product['product_id']) ? $product['product_id'] : ($product['slug'] ?? 'PRD-BFVRD0BGJB'));

                $settingModel = new Setting();
                $host = $_SERVER['HTTP_HOST'] ?? '';
                $defaultServer = (strpos($host, 'digiws.com') !== false) ? 'https://license.digiws.com' : 'http://localhost/licances';
                $licenseServerUrl = rtrim($settingModel->get('license_server_url', $defaultServer), '/');
                $apiUrl = $licenseServerUrl . '/api/license/generate-order';

                $payload = json_encode([
                    'product_id'       => $digiwsProductId,
                    'customer_email'   => $user['email'],
                    'customer_name'    => $user['name'],
                    'customer_mobile'  => $user['phone'] ?? '',
                    'license_type'     => 'lifetime',
                    'max_activations' => 1,
                    'order_number'    => $order['order_number'],
                    'amount'          => $order['total'],
                ]);

                $ch = curl_init($apiUrl);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST           => true,
                    CURLOPT_POSTFIELDS     => $payload,
                    CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
                    CURLOPT_TIMEOUT        => 15,
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);

                $response = curl_exec($ch);
                curl_close($ch);

                if ($response) {
                    $json = json_decode($response, true);
                    if (!empty($json['success']) && !empty($json['license_key'])) {
                        $licenseKey = $json['license_key'];
                        $apiKey = $json['api_key'] ?? ($json['product']['api_secret_key'] ?? '');

                        try {
                            $db->query("ALTER TABLE order_items ADD COLUMN api_key VARCHAR(100) NULL AFTER license_key");
                        } catch (\Throwable $e) {}

                        $db->query("UPDATE order_items SET license_key = ?, api_key = ? WHERE id = ?", [$licenseKey, $apiKey, $item['id']]);

                        $db->query(
                            "INSERT INTO license_keys (product_id, license_key, order_item_id, status, assigned_at) VALUES (?, ?, ?, 'assigned', NOW()) ON DUPLICATE KEY UPDATE status='assigned', assigned_at=NOW()",
                            [$item['product_id'], $licenseKey, $item['id']]
                        );
                    }
                }
            }
        } catch (\Throwable $e) {
            error_log('DigiWS License Server link error: ' . $e->getMessage());
        }
    }

    private function failPayment(int $orderId, string $gateway, string $gatewayOrderId): void {
        $orderModel = new Order();
        $paymentModel = new Payment();

        $orderModel->updatePaymentStatus($orderId, 'failed');

        $payment = $paymentModel->findByOrderId($orderId);
        if ($payment) {
            $paymentModel->updateStatus($payment['id'], 'failed', null, json_encode(['gateway_order_id' => $gatewayOrderId]));
        }

        // Send payment failed email
        $order = $orderModel->find($orderId);
        if ($order) {
            try {
                $user = (new User())->find($order['user_id']);
                $mailer = new Mailer();
                $mailer->sendPaymentFailed($user, $order);
            } catch (Exception $e) {
                // Don't fail on email error
            }
        }
    }

    public function success(): void {
        (new Cart())->clear();
        $orderId = (int)($_GET['order'] ?? 0);
        $_SESSION['show_order_animation'] = $orderId;
        redirect(url('user/orders?success_modal=1&order_id=' . $orderId));
    }

    public function failed(): void {
        $orderId = (int)($_GET['order'] ?? 0);
        $order = (new Order())->find($orderId);

        $pageTitle = 'Payment Failed - ' . APP_NAME;
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/payment-failed.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    /**
     * UPI Gateway: Create Order
     */
    public function upiCreate(): void {
        requireLogin();
        if (!verifyCsrf()) {
            jsonResponse(['success' => false, 'message' => 'Invalid security token.'], 403);
        }

        $orderId = (int)($_POST['order_id'] ?? 0);
        $orderModel = new Order();
        $order = $orderModel->find($orderId);

        if (!$order || $order['user_id'] != currentUserId()) {
            jsonResponse(['success' => false, 'message' => 'Order not found.'], 404);
        }

        $settingModel = new Setting();
        $settings = $settingModel->getGroup('payment');
        
        $apiUrl    = $settings['upi_gateway_url']   ?? 'https://pay.digiws.shop/api/create-order';
        $userToken = $settings['upi_gateway_token'] ?? '';

        if (empty($userToken)) {
            jsonResponse(['success' => false, 'message' => 'UPI Gateway not configured properly.'], 500);
        }

        // Prepare Payload
        $payload = [
            'customer_mobile' => $order['user_phone'] ?? '0000000000',
            'user_token'      => $userToken,
            'amount'          => $order['total'],
            'order_id'        => $order['order_number'],
            'redirect_url'    => url('pay/upi-custom/status?order=' . $orderId),
            'remark1'         => 'Order #' . $order['order_number'],
            'remark2'         => APP_NAME
        ];

        // Call Gateway API
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if ($responseData && isset($responseData['status']) && $responseData['status'] == true) {
            // Update order with custom gateway mark
            $orderModel->update($orderId, ['payment_method' => 'upi_custom']);
            
            jsonResponse([
                'success'     => true,
                'payment_url' => $responseData['result']['payment_url']
            ]);
        } else {
            jsonResponse([
                'success' => false, 
                'message' => $responseData['message'] ?? 'Gateway Error: ' . $response
            ]);
        }
    }

    /**
     * UPI Gateway: Check Status
     */
    public function upiStatus(): void {
        $orderId = (int)($_GET['order'] ?? $_POST['order_id'] ?? 0);
        $orderModel = new Order();
        $order = $orderModel->find($orderId);

        if (!$order) {
            die('Order not found.');
        }

        if ($order['payment_status'] === 'completed') {
            redirect(url('pay/success?order=' . $orderId));
            return;
        }

        $settingModel = new Setting();
        $settings = $settingModel->getGroup('payment');
        $statusUrl = $settings['upi_gateway_status_url'] ?? 'https://pay.digiws.shop/api/check-order-status';
        $userToken = $settings['upi_gateway_token'] ?? '';

        // Call Status API
        $payload = [
            'user_token' => $userToken,
            'order_id'   => $order['order_number']
        ];

        $ch = curl_init($statusUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $statusData = json_decode($response, true);
        $statusStr = strtoupper($statusData['status'] ?? '');

        if ($statusData && ($statusStr === 'COMPLETED' || $statusStr === 'SUCCESS')) {
            // Success!
            $this->completePayment($orderId, 'upi_custom', $statusData['result']['utr'] ?? '', $order['order_number']);

            if (isAjax()) {
                (new Cart())->clear();
                echo 'success';
            } else {
                (new Cart())->clear();
                redirect(url('pay/success?order=' . $orderId));
            }
        } else {
            if (isAjax()) {
                echo 'PENDING';
            } else {
                // If not success, we wait or show pending page
                if (isset($statusData['status']) && $statusData['status'] === 'FAILED') {
                    $this->failPayment($orderId, 'upi_custom', $order['order_number']);
                    redirect(url('pay/failed?order=' . $orderId));
                } else {
                    // Show a simple waiting template
                    $pageTitle = 'Payment Pending - ' . APP_NAME;
                    include TEMPLATES_PATH . '/header.php';
                    echo '<div class="container py-5 text-center">';
                    echo '<div class="spinner-border text-primary mb-3"></div>';
                    echo '<h3>Waiting for Payment Confirmation...</h3>';
                    echo '<p>Please do not close this window. We are verifying your transaction.</p>';
                    echo '<script>setTimeout(() => location.reload(), 5000);</script>';
                    echo '</div>';
                    include TEMPLATES_PATH . '/footer.php';
                }
            }
        }
    }

    /**
     * UPI Gateway: Webhook Handler
     */
    public function upiWebhook(): void {
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true) ?: $_POST;
        $statusStr = strtoupper($data['status'] ?? '');

        if (isset($data['order_id'])) {
            $orderNumber = $data['order_id'];
            $db = Database::getInstance();
            $order = $db->fetch("SELECT * FROM orders WHERE order_number = ?", [$orderNumber]);

            if ($order) {
                if ($statusStr === 'COMPLETED' || $statusStr === 'SUCCESS') {
                    if ($order['payment_status'] !== 'paid' && $order['payment_status'] !== 'completed') {
                        $this->completePayment($order['id'], 'upi_custom', $data['utr'] ?? $data['txn_id'] ?? '', $orderNumber);
                        echo 'OK';
                        return;
                    }
                } elseif ($statusStr === 'FAILED') {
                    if ($order['payment_status'] !== 'failed') {
                        $this->failPayment($order['id'], 'upi_custom', $orderNumber);
                        echo 'OK';
                        return;
                    }
                }
            }
        }
        echo 'Invalid Webhook';
    }

    /**
     * Direct UPI: Verify payment (screenshot/UTR upload)
     */
    public function upiDirectVerify(): void {
        ob_start(); // Prevent any stray output from breaking JSON

        // AJAX-aware auth check — return JSON instead of redirect
        if (!isLoggedIn()) {
            ob_end_clean();
            jsonResponse(['success' => false, 'message' => 'Session expired. Please login and try again.'], 401);
        }

        try {
            $orderId = (int)($_POST['order_id'] ?? 0);
            $utr     = trim(sanitize($_POST['utr_number'] ?? ''));

            if ($orderId <= 0) {
                ob_end_clean();
                jsonResponse(['success' => false, 'message' => 'Invalid order ID.'], 400);
            }

            $orderModel = new Order();
            $order = $orderModel->find($orderId);

            if (!$order || (int)$order['user_id'] !== (int)currentUserId()) {
                ob_end_clean();
                jsonResponse(['success' => false, 'message' => 'Order not found or access denied.'], 404);
            }

            // Handle optional file upload
            $screenshot = null;
            if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
                $screenshot = uploadFile($_FILES['screenshot'], 'payment_proofs');
                if ($screenshot === null) {
                    ob_end_clean();
                    jsonResponse(['success' => false, 'message' => 'Screenshot upload failed. Please use a valid JPG or PNG image.'], 422);
                }
            } elseif (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] !== UPLOAD_ERR_NO_FILE) {
                // A file was chosen but had an upload error
                $uploadErrors = [
                    UPLOAD_ERR_INI_SIZE   => 'File is too large (server limit).',
                    UPLOAD_ERR_FORM_SIZE  => 'File is too large.',
                    UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
                    UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                ];
                $errCode = $_FILES['screenshot']['error'];
                $errMsg  = $uploadErrors[$errCode] ?? 'File upload error (code ' . $errCode . ').';
                ob_end_clean();
                jsonResponse(['success' => false, 'message' => $errMsg], 422);
            }

            // At least one of UTR or screenshot is required
            if (empty($utr) && empty($screenshot)) {
                ob_end_clean();
                jsonResponse(['success' => false, 'message' => 'Please enter a UTR number or upload a payment screenshot.'], 400);
            }

            // Update order to processing / pending verification
            $orderModel->update($orderId, [
                'status'         => 'processing',
                'payment_status' => 'pending',
                'payment_method' => 'upi_direct',
            ]);

            // Create payment record
            $paymentModel = new Payment();
            $paymentModel->create([
                'order_id'         => $orderId,
                'user_id'          => currentUserId(),
                'gateway'          => 'upi_direct',
                'transaction_id'   => $utr ?: null,
                'amount'           => $order['total'],
                'status'           => 'pending',
                'gateway_response' => json_encode(['utr' => $utr, 'screenshot' => $screenshot]),
            ]);

            // Clear the cart
            (new Cart())->clear();

            ob_end_clean();
            jsonResponse(['success' => true, 'redirect' => url('pay/success?order=' . $orderId)]);

        } catch (Throwable $e) {
            ob_end_clean();
            jsonResponse(['success' => false, 'message' => 'Verification failed: ' . $e->getMessage()], 500);
        }
    }
}
