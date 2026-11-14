<?php
/**
 * Digiws - Admin Payment Settings View
 * Enhanced with API key inputs for all gateways + Paytm UPI setup
 */
$user     = $user     ?? [];
$paytmToken = $paytmToken ?? [];
$settings = $settings ?? [];

// Helper to check if a setting is 'Yes'
$enabled = fn($key) => ($settings[$key] ?? 'No') === 'Yes';
?>

<style>
.pay-gateway-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px 28px;
    margin-bottom: 20px;
    background: #fff;
    transition: box-shadow .2s;
}
.pay-gateway-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.07); }
.pay-gateway-header {
    display: flex; align-items: center;
    justify-content: space-between; margin-bottom: 20px;
}
.pay-gateway-header .gw-left {
    display: flex; align-items: center; gap: 14px;
}
.pay-gateway-header .gw-logo {
    width: 44px; height: 44px; border-radius: 10px;
    display: grid; place-items: center;
    background: #f1f5f9; font-size: 1.4rem;
    overflow: hidden;
}
.pay-gateway-header .gw-logo img { width: 100%; height: 100%; object-fit: contain; }
.pay-gateway-title { font-weight: 700; font-size: .95rem; margin: 0; color: #1e293b; }
.pay-gateway-subtitle { font-size: .78rem; color: #94a3b8; margin: 0; }
.gw-toggle-wrap { display: flex; align-items: center; gap: 10px; }
.gw-toggle-wrap .form-check-label { font-size: .82rem; font-weight: 600; color: #3b82f6; }
.badge-enabled  { background: #dcfce7; color: #16a34a; padding: 4px 10px; border-radius: 20px; font-size: .75rem; font-weight: 600; }
.badge-disabled { background: #fee2e2; color: #dc2626; padding: 4px 10px; border-radius: 20px; font-size: .75rem; font-weight: 600; }
</style>

<div class="dc-card p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1 fw-bold">Payment Gateway Configuration</h5>
            <p class="text-muted small mb-0">Connect merchant accounts to accept live payments.</p>
        </div>
        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-3">
            <i class="bi bi-shield-lock-fill me-1"></i> Secure SSL
        </span>
    </div>

    <form action="<?= url('admin/payment-settings/save') ?>" method="POST">
        <?= csrfField() ?>

        <!-- ======== CUSTOM UPI GATEWAY (New) ======== -->
        <div class="pay-gateway-card">
            <div class="pay-gateway-header">
                <div class="gw-left">
                    <div class="gw-logo" style="background:#00c853">
                        <i class="bi bi-qr-code text-white"></i>
                    </div>
                    <div>
                        <p class="pay-gateway-title">Custom UPI Gateway (pay.digiws.shop)</p>
                        <p class="pay-gateway-subtitle">Direct API integration for UPI payments</p>
                    </div>
                </div>
                <div class="gw-toggle-wrap">
                    <?php if ($enabled('payment_upi_custom_enabled')): ?>
                        <span class="badge-enabled">Active</span>
                    <?php else: ?>
                        <span class="badge-disabled">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-12">
                    <label class="dc-form-label">API User Token</label>
                    <input type="text" name="upi_gateway_token" class="dc-form-control" placeholder="96235e90..."
                           value="<?= htmlspecialchars($settings['upi_gateway_token'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="dc-form-label">Create Order API URL</label>
                    <input type="text" name="upi_gateway_url" class="dc-form-control" placeholder="https://pay.digiws.shop/api/create-order"
                           value="<?= htmlspecialchars($settings['upi_gateway_url'] ?? 'https://pay.digiws.shop/api/create-order') ?>">
                </div>
                <div class="col-md-6">
                    <label class="dc-form-label">Check Status API URL</label>
                    <input type="text" name="upi_gateway_status_url" class="dc-form-control" placeholder="https://pay.digiws.shop/api/check-order-status"
                           value="<?= htmlspecialchars($settings['upi_gateway_status_url'] ?? 'https://pay.digiws.shop/api/check-order-status') ?>">
                </div>
                <div class="col-12">
                    <label class="dc-form-label text-primary fw-bold">Webhook URL (Copy to your gateway portal)</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="<?= url('pay/upi-custom/webhook') ?>" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(this.previousElementSibling.value); alert('Copied!')">Copy</button>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="upiCustomEnabled" name="payment_upi_custom_enabled" value="Yes"
                               <?= $enabled('payment_upi_custom_enabled') ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold text-primary" for="upiCustomEnabled">Show UPI Gateway at Checkout</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======== DIRECT UPI PAYMENT (Scan & Pay) ======== -->
        <div class="pay-gateway-card">
            <div class="pay-gateway-header">
                <div class="gw-left">
                    <div class="gw-logo" style="background:#094ebb">
                        <i class="bi bi-qr-code-scan text-white"></i>
                    </div>
                    <div>
                        <p class="pay-gateway-title">Direct UPI (Scan & Pay)</p>
                        <p class="pay-gateway-subtitle">Display your static UPI QR code and manually verify UTR/screenshot proof</p>
                    </div>
                </div>
                <div class="gw-toggle-wrap">
                    <?php if ($enabled('payment_upi_direct_enabled')): ?>
                        <span class="badge-enabled">Active</span>
                    <?php else: ?>
                        <span class="badge-disabled">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="dc-form-label">UPI ID (VPA)</label>
                    <input type="text" name="upi_id" class="dc-form-control" placeholder="example@upi"
                           value="<?= htmlspecialchars($settings['upi_id'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="dc-form-label">Merchant Name</label>
                    <input type="text" name="upi_merchant_name" class="dc-form-control" placeholder="e.g. DiGiWS Store"
                           value="<?= htmlspecialchars($settings['upi_merchant_name'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="upiDirectEnabled" name="payment_upi_direct_enabled" value="Yes"
                               <?= $enabled('payment_upi_direct_enabled') ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold text-primary" for="upiDirectEnabled">Show Direct UPI at Checkout</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="pay-gateway-card">
            <div class="pay-gateway-header">
                <div class="gw-left">
                    <div class="gw-logo" style="background:#072654">
                        <span style="color:#fff;font-weight:800;font-size:.85rem">Rzp</span>
                    </div>
                    <div>
                        <p class="pay-gateway-title">Razorpay</p>
                        <p class="pay-gateway-subtitle">Credit/Debit cards, UPI, Net Banking, Wallets</p>
                    </div>
                </div>
                <div class="gw-toggle-wrap">
                    <?php if ($enabled('payment_razorpay_enabled')): ?>
                        <span class="badge-enabled">Active</span>
                    <?php else: ?>
                        <span class="badge-disabled">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="dc-form-label">Razorpay Key ID</label>
                    <input type="text" name="razorpay_key_id" class="dc-form-control" placeholder="rzp_live_XXXXXXXXXX"
                           value="<?= htmlspecialchars($settings['razorpay_key_id'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="dc-form-label">Razorpay Key Secret</label>
                    <input type="password" name="razorpay_key_secret" class="dc-form-control" placeholder="••••••••••••"
                           value="<?= htmlspecialchars($settings['razorpay_key_secret'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="razorpayEnabled" name="payment_razorpay_enabled" value="Yes"
                               <?= $enabled('payment_razorpay_enabled') ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold text-primary" for="razorpayEnabled">Show Razorpay at Checkout</label>
                    </div>
                </div>
                <div class="col-12">
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px;">
                        <details>
                            <summary style="font-weight: 700; color: #1e293b; cursor: pointer; user-select: none;">
                                <i class="bi bi-info-circle-fill text-primary me-2"></i> Razorpay Live Mode Setup Guide &amp; Webhooks (Click to expand)
                            </summary>
                            <div class="mt-3 text-muted" style="font-size: 0.85rem; line-height: 1.6;">
                                <p class="mb-2"><strong>1. Switch to Live Mode:</strong></p>
                                <ul class="ps-3 mb-3">
                                    <li>Switch your Razorpay Dashboard toggle from <strong>Test Mode</strong> to <strong>Live Mode</strong>.</li>
                                    <li>Go to <strong>Settings ➔ API Keys</strong> and click <strong>Generate Live Key</strong>.</li>
                                    <li>Copy Key ID (starts with <code>rzp_live_...</code>) and Key Secret to the fields above.</li>
                                </ul>
                                
                                <p class="mb-2"><strong>2. Critical Verification Checklist:</strong></p>
                                <ul class="ps-3 mb-3">
                                    <li><strong>SSL (HTTPS):</strong> Razorpay Live Mode will fail if your website URL does not start with <code>https://</code>.</li>
                                    <li><strong>KYC Activation:</strong> Your Razorpay account must be fully verified and approved by the Razorpay team.</li>
                                    <li><strong>Currency:</strong> Make sure your DigiWS store currency is set to <strong>INR</strong>.</li>
                                </ul>

                                <p class="mb-2"><strong>3. Webhook Configuration (Auto order update):</strong></p>
                                <ul class="ps-3 mb-0">
                                    <li>In Razorpay Dashboard, go to <strong>Settings ➔ Webhooks ➔ Add New Webhook</strong>.</li>
                                    <li>Set Webhook URL to: <code><?= url('pay/razorpay/verify') ?></code></li>
                                    <li>Select Active Events: <code>payment.authorized</code>, <code>payment.captured</code>, <code>order.paid</code></li>
                                </ul>
                            </div>
                        </details>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======== STRIPE ======== -->
        <div class="pay-gateway-card">
            <div class="pay-gateway-header">
                <div class="gw-left">
                    <div class="gw-logo" style="background:#635bff">
                        <i class="bi bi-credit-card-2-front-fill text-white"></i>
                    </div>
                    <div>
                        <p class="pay-gateway-title">Stripe</p>
                        <p class="pay-gateway-subtitle">International credit/debit cards (USD/EUR etc.)</p>
                    </div>
                </div>
                <div class="gw-toggle-wrap">
                    <?php if ($enabled('payment_stripe_enabled')): ?>
                        <span class="badge-enabled">Active</span>
                    <?php else: ?>
                        <span class="badge-disabled">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="dc-form-label">Stripe Publishable Key</label>
                    <input type="text" name="stripe_publishable_key" class="dc-form-control" placeholder="pk_live_XXXXXXXXXX"
                           value="<?= htmlspecialchars($settings['stripe_publishable_key'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="dc-form-label">Stripe Secret Key</label>
                    <input type="password" name="stripe_secret_key" class="dc-form-control" placeholder="sk_live_XXXXXXXXXX"
                           value="<?= htmlspecialchars($settings['stripe_secret_key'] ?? '') ?>">
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="stripeEnabled" name="payment_stripe_enabled" value="Yes"
                               <?= $enabled('payment_stripe_enabled') ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold text-primary" for="stripeEnabled">Show Stripe at Checkout</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======== PAYPAL ======== -->
        <div class="pay-gateway-card">
            <div class="pay-gateway-header">
                <div class="gw-left">
                    <div class="gw-logo" style="background:#003087">
                        <span style="color:#fff;font-weight:800;font-size:.85rem">PP</span>
                    </div>
                    <div>
                        <p class="pay-gateway-title">PayPal</p>
                        <p class="pay-gateway-subtitle">International PayPal payments (Sandbox / Live)</p>
                    </div>
                </div>
                <div class="gw-toggle-wrap">
                    <?php if ($enabled('payment_paypal_enabled')): ?>
                        <span class="badge-enabled">Active</span>
                    <?php else: ?>
                        <span class="badge-disabled">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="dc-form-label">PayPal Client ID</label>
                    <input type="text" name="paypal_client_id" class="dc-form-control" placeholder="AY..."
                           value="<?= htmlspecialchars($settings['paypal_client_id'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="dc-form-label">PayPal Secret</label>
                    <input type="password" name="paypal_secret" class="dc-form-control" placeholder="••••••••••••"
                           value="<?= htmlspecialchars($settings['paypal_secret'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="dc-form-label">Mode</label>
                    <select name="paypal_mode" class="dc-form-control">
                        <option value="sandbox" <?= ($settings['paypal_mode'] ?? '') === 'sandbox' ? 'selected' : '' ?>>Sandbox (Testing)</option>
                        <option value="live" <?= ($settings['paypal_mode'] ?? '') === 'live' ? 'selected' : '' ?>>Live</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch w-100">
                        <input class="form-check-input" type="checkbox" id="paypalEnabled" name="payment_paypal_enabled" value="Yes"
                               <?= $enabled('payment_paypal_enabled') ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold text-primary" for="paypalEnabled">Show PayPal at Checkout</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======== ORDER AUTOMATION (New) ======== -->
        <div class="pay-gateway-card">
            <div class="pay-gateway-header">
                <div class="gw-left">
                    <div class="gw-logo" style="background:#6366f1">
                        <i class="bi bi-gear-fill text-white"></i>
                    </div>
                    <div>
                        <p class="pay-gateway-title">Order Automation</p>
                        <p class="pay-gateway-subtitle">Control system behavior after successful payments</p>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="dc-form-label">Default Order Status After Payment</label>
                    <select name="after_payment_status" class="dc-form-control">
                        <option value="pending" <?= ($settings['after_payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="processing" <?= ($settings['after_payment_status'] ?? '') === 'processing' ? 'selected' : '' ?>>Processing</option>
                        <option value="completed" <?= ($settings['after_payment_status'] ?? 'completed') === 'completed' ? 'selected' : '' ?>>Completed (Recommended)</option>
                    </select>
                    <p class="text-muted mt-2" style="font-size:0.75rem;">
                        <i class="bi bi-info-circle me-1"></i> For digital products, 'Completed' ensures instant access to downloads.
                    </p>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="text-end mt-3">
            <button type="submit" class="btn-dc-primary px-5">
                <i class="bi bi-save2 me-2"></i> Save All Payment Settings
            </button>
        </div>
    </form>
</div>


