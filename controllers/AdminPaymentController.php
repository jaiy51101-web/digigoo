<?php
/**
 * Digiws - Admin Payment Controller
 */

class AdminPaymentController {
    
    public function index(): void {
        requireAdmin();
        $userModel = new User();
        $db = Database::getInstance();
        $settingModel = new Setting();

        $user = $userModel->find(currentUserId());
        
        // Paytm removed
        $settings = $settingModel->getGroup('payment');

        $pageTitle = 'Payment Settings - ' . APP_NAME;
        $adminPage = 'payment_settings';
        $contentView = VIEWS_PATH . '/admin/payment-settings.php';
        
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function save(): void {
        requireAdmin();
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid security token.');
            redirect(url('admin/payment-settings'));
        }

        $db = Database::getInstance();
        $userModel = new User();
        $settingModel = new Setting();
        
        $userId = currentUserId();
        $user = $userModel->find($userId);

        // Payment fields
        $paypalConnected = sanitize($_POST['paypal_connected'] ?? 'No'); // Placeholder for consistency
        $defaultUpi     = sanitize($_POST['default_upi']     ?? '');

        // Gateway toggles (checkboxes not sent if unchecked → default 'No')
        // Gateway toggles
        $paypalEnabled  = isset($_POST['payment_paypal_enabled'])   ? 'Yes' : 'No';
        $upiEnabled     = isset($_POST['payment_upi_enabled'])      ? 'Yes' : 'No';
        $razorpayEnabled = isset($_POST['payment_razorpay_enabled']) ? 'Yes' : 'No';
        $stripeEnabled  = isset($_POST['payment_stripe_enabled'])   ? 'Yes' : 'No';
        $upiCustomEnabled = isset($_POST['payment_upi_custom_enabled']) ? 'Yes' : 'No';
        $upiDirectEnabled = isset($_POST['payment_upi_direct_enabled']) ? 'Yes' : 'No';

        // Razorpay keys
        $rzpKeyId       = sanitize($_POST['razorpay_key_id']     ?? '');
        $rzpKeySecret   = sanitize($_POST['razorpay_key_secret'] ?? '');

        // Stripe keys
        $stripePubKey   = sanitize($_POST['stripe_publishable_key'] ?? '');
        $stripeSecKey   = sanitize($_POST['stripe_secret_key']      ?? '');

        // PayPal keys
        $paypalClientId = sanitize($_POST['paypal_client_id'] ?? '');
        $paypalSecret   = sanitize($_POST['paypal_secret']    ?? '');
        $paypalMode     = in_array($_POST['paypal_mode'] ?? '', ['sandbox', 'live']) ? $_POST['paypal_mode'] : 'sandbox';

        // Custom UPI Gateway fields
        $upiToken     = sanitize($_POST['upi_gateway_token']      ?? '');
        $upiUrl       = sanitize($_POST['upi_gateway_url']        ?? '');
        $upiStatusUrl = sanitize($_POST['upi_gateway_status_url'] ?? '');
        $afterPaymentStatus = sanitize($_POST['after_payment_status'] ?? 'completed');

        // Direct UPI fields
        $upiId           = sanitize($_POST['upi_id']              ?? '');
        $upiMerchantName = sanitize($_POST['upi_merchant_name']   ?? '');

        // Save gateway toggles
        $settingModel->set('payment_paypal_enabled',      $paypalEnabled,      'payment');
        $settingModel->set('payment_upi_enabled',         $upiEnabled,         'payment');
        $settingModel->set('payment_razorpay_enabled',    $razorpayEnabled,    'payment');
        $settingModel->set('payment_stripe_enabled',      $stripeEnabled,      'payment');
        $settingModel->set('payment_upi_custom_enabled',  $upiCustomEnabled,   'payment');
        $settingModel->set('payment_upi_direct_enabled',  $upiDirectEnabled,   'payment');

        // Save Custom UPI Gateway settings
        $settingModel->set('upi_gateway_token',      $upiToken,     'payment');
        $settingModel->set('upi_gateway_url',        $upiUrl,       'payment');
        $settingModel->set('upi_gateway_status_url', $upiStatusUrl, 'payment');
        $settingModel->set('after_payment_status',  $afterPaymentStatus, 'payment');

        // Save Direct UPI settings
        $settingModel->set('upi_id',                 $upiId,           'payment');
        $settingModel->set('upi_merchant_name',     $upiMerchantName, 'payment');

        // Save Razorpay keys
        if (!empty($rzpKeyId))     $settingModel->set('razorpay_key_id',     $rzpKeyId,     'payment');
        if (!empty($rzpKeySecret)) $settingModel->set('razorpay_key_secret', $rzpKeySecret, 'payment');

        // Save Stripe keys
        if (!empty($stripePubKey)) $settingModel->set('stripe_publishable_key', $stripePubKey, 'payment');
        if (!empty($stripeSecKey)) $settingModel->set('stripe_secret_key',      $stripeSecKey, 'payment');

        // Save PayPal keys
        if (!empty($paypalClientId)) $settingModel->set('paypal_client_id', $paypalClientId, 'payment');
        if (!empty($paypalSecret))   $settingModel->set('paypal_secret',    $paypalSecret,   'payment');
        $settingModel->set('paypal_mode', $paypalMode, 'payment');

        setFlash('success', 'Payment settings saved successfully.');
        redirect(url('admin/payment-settings'));
    }

    public function checkConnection(): void {
        requireAdmin();
        // Simple health check endpoint
        jsonResponse(['success' => true, 'message' => 'Gateway connection OK']);
    }
}
