<!-- User Order Detail -->
<section class="dc-section" style="padding-top:30px;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3"><?php include VIEWS_PATH . '/user/sidebar.php'; ?></div>
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0">Order #<?= $order['order_number'] ?></h4>
                        <p class="text-muted small mb-0"><?= formatDate($order['created_at'], 'd M Y') ?></p>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <a href="<?= url('user/orders/invoice/' . $order['id']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm d-print-none">
                            <i class="bi bi-printer me-1"></i>Download Invoice
                        </a>
                        <?= statusBadge($order['status']) ?> <?= statusBadge($order['payment_status']) ?>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-8 col-md-12">
                        <div class="dc-payment-card mb-4">
                            <h6 class="mb-3">Order Items</h6>
                            <?php foreach ($order['items'] as $item): ?>
                            <div class="d-flex gap-3 py-3" style="border-bottom:1px solid var(--border-light);">
                                <img src="<?= $item['thumbnail'] ? uploadUrl($item['thumbnail']) : asset('images/placeholder.png') ?>" style="width:70px;height:50px;border-radius:8px;object-fit:cover;" alt="">
                                <div class="flex-fill">
                                    <h6 style="font-size:0.92rem;margin:0 0 3px;"><?= htmlspecialchars($item['product_title']) ?></h6>
                                    <small class="text-muted d-block mb-1">
                                        <?php if ($item['setup_included']): ?>Setup included · <?php endif; ?>
                                        <?php if ($item['support_months']): ?><?= $item['support_months'] ?>mo support · <?php endif; ?>
                                         <?php if (!empty($item['license_key'])): ?>
                                            <div class="mt-2 p-2 rounded bg-light border">
                                                <div class="small fw-bold text-dark"><i class="bi bi-key-fill text-warning me-1"></i> License Key: <code class="text-primary fs-6"><?= $item['license_key'] ?></code></div>
                                                <?php if (!empty($item['api_key'])): ?>
                                                    <div class="small text-muted mt-1"><i class="bi bi-shield-lock-fill text-info me-1"></i> Product Secret API Key: <code><?= $item['api_key'] ?></code></div>
                                                <?php endif; ?>
                                            </div>
                                         <?php endif; ?>
                                     </small>
                                    
                                    <?php 
                                    $allowed = explode(',', (new Setting())->get('downloadable_statuses', 'paid,completed'));
                                    $allowed = array_map('trim', $allowed);
                                    if (in_array($order['payment_status'], $allowed) || in_array($order['status'], $allowed)): 
                                    ?>
                                        <div class="mt-2 d-flex flex-wrap align-items-center gap-2">
                                            <span style="font-size:0.8rem;"><?= getSupportValidityText($item['support_expires_at'] ?? null) ?></span>
                                            
                                            <!-- Renew Support Button Trigger with inline HTML hover/click dropdown options -->
                                            <div class="btn-group">
                                                <button class="btn btn-outline-primary btn-sm py-0 px-2 d-flex align-items-center gap-1 dropdown-toggle" style="font-size:0.72rem; border-radius:6px; height:24px;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-arrow-repeat"></i> Renew Support
                                                </button>
                                                <ul class="dropdown-menu shadow-sm border" style="font-size:0.8rem;">
                                                    <li>
                                                        <a class="dropdown-item py-1" href="<?= url('cart/add-support?product_id=' . $item['product_id'] . '&months=6') ?>">
                                                            Extend 6 Months <span class="text-primary fw-semibold ms-2">(+<?= formatPrice($item['support_6_months'] ?? 299) ?>)</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item py-1" href="<?= url('cart/add-support?product_id=' . $item['product_id'] . '&months=12') ?>">
                                                            Extend 12 Months <span class="text-primary fw-semibold ms-2">(+<?= formatPrice($item['support_12_months'] ?? 499) ?>)</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="text-end d-flex flex-column align-items-end gap-1">
                                    <strong><?= formatPrice($item['subtotal']) ?></strong>
                                    <?php 
                                    $allowed = explode(',', (new Setting())->get('downloadable_statuses', 'paid,completed'));
                                    $allowed = array_map('trim', $allowed);
                                    if ((in_array($order['payment_status'], $allowed) || in_array($order['status'], $allowed)) && ($item['download_file'] || $item['external_url'])): 
                                        $downloadList = [];
                                        if ($item['download_file']) {
                                            $fileVal = trim($item['download_file']);
                                            $decoded = json_decode($fileVal, true);
                                            if (!is_array($decoded) && $fileVal !== '' && ($fileVal[0] === '[' || $fileVal[0] === '{')) {
                                                $decoded = json_decode(stripslashes($fileVal), true);
                                            }
                                            if (is_array($decoded)) {
                                                foreach ($decoded as $index => $res) {
                                                    if (is_array($res)) {
                                                        if (isset($res['url'])) {
                                                            $plat = getPlatformDetails($res['platform'] ?? 'other', $index);
                                                            $downloadList[] = [
                                                                'name'  => $plat['text'],
                                                                'icon'  => $plat['icon'],
                                                                'bg'    => $plat['bg'],
                                                                'url'   => url('user/download/' . $item['id'] . '?file_index=' . $index)
                                                            ];
                                                        }
                                                    } else {
                                                        $isUrl = (strpos((string)$res, 'http') === 0);
                                                        $plat = getPlatformDetails($isUrl ? 'other' : 'server', $index);
                                                        $downloadList[] = [
                                                            'name'  => $plat['text'],
                                                            'icon'  => $plat['icon'],
                                                            'bg'    => $plat['bg'],
                                                            'url'   => url('user/download/' . $item['id'] . '?file_index=' . $index)
                                                        ];
                                                    }
                                                }
                                            } else {
                                                $lines = array_filter(explode("\n", str_replace("\r", "", $item['download_file'])));
                                                if (count($lines) > 1) {
                                                    foreach ($lines as $index => $res) {
                                                        $res = trim($res);
                                                        $isUrl = (strpos($res, 'http') === 0);
                                                        if (!$isUrl && preg_match('/^[a-zA-Z0-9_-]{15,50}$/', $res)) {
                                                            $plat = getPlatformDetails('gdrive', $index);
                                                            $downloadList[] = [
                                                                'name'  => $plat['text'],
                                                                'icon'  => $plat['icon'],
                                                                'bg'    => $plat['bg'],
                                                                'url'   => 'https://drive.google.com/file/d/' . $res . '/view?usp=sharing'
                                                            ];
                                                        } else {
                                                            $plat = getPlatformDetails($isUrl ? 'other' : 'server', $index);
                                                            $downloadList[] = [
                                                                'name'  => $plat['text'],
                                                                'icon'  => $plat['icon'],
                                                                'bg'    => $plat['bg'],
                                                                'url'   => url('user/download/' . $item['id'] . '?file_index=' . $index)
                                                            ];
                                                        }
                                                    }
                                                } else {
                                                    $res = trim($item['download_file']);
                                                    $isUrl = (strpos($res, 'http') === 0);
                                                    if (!$isUrl && preg_match('/^[a-zA-Z0-9_-]{15,50}$/', $res)) {
                                                        $plat = getPlatformDetails('gdrive', 0);
                                                        $downloadList[] = [
                                                            'name'  => $plat['text'],
                                                            'icon'  => $plat['icon'],
                                                            'bg'    => $plat['bg'],
                                                            'url'   => 'https://drive.google.com/file/d/' . $res . '/view?usp=sharing'
                                                        ];
                                                    } else {
                                                        $plat = getPlatformDetails($isUrl ? 'other' : 'server', 0);
                                                        $downloadList[] = [
                                                            'name'  => $plat['text'],
                                                            'icon'  => $plat['icon'],
                                                            'bg'    => $plat['bg'],
                                                            'url'   => url('user/download/' . $item['id'])
                                                        ];
                                                    }
                                                }
                                            }
                                        } elseif ($item['external_url']) {
                                            $plat = getPlatformDetails('other', 0);
                                            $downloadList[] = [
                                                'name'  => 'External Link',
                                                'icon'  => $plat['icon'],
                                                'bg'    => $plat['bg'],
                                                'url'   => url('user/download/' . $item['id'])
                                            ];
                                        }
                                        foreach ($downloadList as $d):
                                    ?>
                                        <a href="<?= $d['url'] ?>" class="btn btn-sm d-flex align-items-center gap-2 px-3 py-1 text-white border-0 mt-1" style="font-size:0.72rem; font-weight:500; border-radius:6px; background-color: <?= $d['bg'] ?>; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">
                                            <i class="bi <?= $d['icon'] ?>" style="font-size: 0.85rem;"></i> <?= htmlspecialchars($d['name']) ?>
                                        </a>
                                    <?php 
                                        endforeach;
                                    endif; 
                                    ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Complete Payment Card placed here inside col-md-8 -->
                        <?php if (!$isPaid): ?>
                        <div class="dc-payment-card mt-4 border border-danger-subtle" id="retryPaymentCard">
                            <h5 class="mb-3 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Complete Payment</h5>
                            <p class="text-muted small" style="font-size:0.85rem; margin-bottom: 20px;">Your payment was not completed or failed. Select a payment method below to complete your order.</p>
                            
                            <div class="dc-payment-methods row g-3 mb-4">
                                <?php if ($settingModel->get('payment_upi_direct_enabled', 'No') === 'Yes'): ?>
                                <div class="col-md-6">
                                    <div class="dc-payment-method py-3 px-3 border rounded d-flex align-items-center gap-2 h-100" style="cursor:pointer; border-radius:12px;" onclick="selectRetryPayment('upi_direct', this)">
                                        <input type="radio" name="retry_payment_method" value="upi_direct" style="accent-color:var(--accent-primary);">
                                        <div>
                                            <h6 class="mb-0" style="font-size:0.88rem; font-weight:700;">Direct UPI (Scan & Pay)</h6>
                                            <small class="text-muted" style="font-size:0.75rem;">Pay using QR Code & upload proof</small>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($settingModel->get('payment_upi_custom_enabled', 'No') === 'Yes'): ?>
                                <div class="col-md-6">
                                    <div class="dc-payment-method py-3 px-3 border rounded d-flex align-items-center gap-2 h-100" style="cursor:pointer; border-radius:12px;" onclick="selectRetryPayment('upi_custom', this)">
                                        <input type="radio" name="retry_payment_method" value="upi_custom" style="accent-color:var(--accent-primary);">
                                        <div>
                                            <h6 class="mb-0" style="font-size:0.88rem; font-weight:700;">UPI Gateway</h6>
                                            <small class="text-muted" style="font-size:0.75rem;">Pay using any UPI app</small>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($settingModel->get('payment_paypal_enabled', 'No') === 'Yes'): ?>
                                <div class="col-md-6">
                                    <div class="dc-payment-method py-3 px-3 border rounded d-flex align-items-center gap-2 h-100" style="cursor:pointer; border-radius:12px;" onclick="selectRetryPayment('paypal', this)">
                                        <input type="radio" name="retry_payment_method" value="paypal" style="accent-color:var(--accent-primary);">
                                        <div>
                                            <h6 class="mb-0" style="font-size:0.88rem; font-weight:700;">PayPal</h6>
                                            <small class="text-muted" style="font-size:0.75rem;">Secure Global Payments</small>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($settingModel->get('payment_razorpay_enabled', 'No') === 'Yes'): ?>
                                <div class="col-md-6">
                                    <div class="dc-payment-method py-3 px-3 border rounded d-flex align-items-center gap-2 h-100" style="cursor:pointer; border-radius:12px;" onclick="selectRetryPayment('razorpay', this)">
                                        <input type="radio" name="retry_payment_method" value="razorpay" style="accent-color:var(--accent-primary);">
                                        <div>
                                            <h6 class="mb-0" style="font-size:0.88rem; font-weight:700;">Razorpay</h6>
                                            <small class="text-muted" style="font-size:0.75rem;">Cards, Net Banking, Wallets</small>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($settingModel->get('payment_stripe_enabled', 'No') === 'Yes'): ?>
                                <div class="col-md-6">
                                    <div class="dc-payment-method py-3 px-3 border rounded d-flex align-items-center gap-2 h-100" style="cursor:pointer; border-radius:12px;" onclick="selectRetryPayment('stripe', this)">
                                        <input type="radio" name="retry_payment_method" value="stripe" style="accent-color:var(--accent-primary);">
                                        <div>
                                            <h6 class="mb-0" style="font-size:0.88rem; font-weight:700;">Stripe</h6>
                                            <small class="text-muted" style="font-size:0.75rem;">Safe Card Payments</small>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <button type="button" class="btn btn-primary w-100 py-3 rounded-pill fw-bold" id="retryPayBtn" onclick="processRetryPayment()" style="font-size: 1rem; letter-spacing: 0.5px;">
                                <i class="bi bi-lock-fill me-2"></i>Pay <?= formatPrice($order['total']) ?> Now
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <!-- Sticky Sidebar Wrapper to stick both cards together -->
                        <div class="dc-sticky-sidebar">
                            <div class="dc-cart-summary">
                                <h5>Summary</h5>
                                <div class="summary-row"><span>Subtotal</span><span><?= formatPrice($order['subtotal']) ?></span></div>
                                <div class="summary-row"><span>Tax</span><span><?= formatPrice($order['tax_amount']) ?></span></div>
                                <?php if ($order['discount'] > 0): ?><div class="summary-row discount"><span>Discount</span><span>-<?= formatPrice($order['discount']) ?></span></div><?php endif; ?>
                                <div class="summary-row total"><span>Total</span><span><?= formatPrice($order['total']) ?></span></div>
                            </div>
                            
                            <div class="dc-payment-card">
                                <h5 class="mb-3" style="font-size:1.1rem; font-weight: 800; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">Order Info</h5>
                                <small class="d-block text-muted mb-2" style="font-size:0.85rem;"><strong>Date:</strong> <?= formatDate($order['created_at'], 'd M Y H:i') ?></small>
                                <small class="d-block text-muted mb-2" style="font-size:0.85rem;"><strong>Payment:</strong> <?= ucfirst($order['payment_method'] ?? 'N/A') ?></small>
                                <?php if ($order['coupon_code']): ?><small class="d-block text-muted" style="font-size:0.85rem;"><strong>Coupon:</strong> <?= $order['coupon_code'] ?></small><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php if (!$isPaid): ?>
<!-- Scripts -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="<?= asset('js/payment.js?v=' . (file_exists(BASE_PATH . '/assets/js/payment.js') ? filemtime(BASE_PATH . '/assets/js/payment.js') : time())) ?>"></script>
<script>
const csrfToken = '<?= csrfToken() ?>';
window.csrfToken = csrfToken;
const appUrl = '<?= APP_URL ?>';
let selectedRetryMethod = '';

// Select first payment method automatically
window.addEventListener('DOMContentLoaded', () => {
    let firstMethod = document.querySelector('[name="retry_payment_method"]');
    if (firstMethod) {
        let parent = firstMethod.closest('.dc-payment-method');
        selectRetryPayment(firstMethod.value, parent);
    }
});

function selectRetryPayment(method, el) {
    document.querySelectorAll('#retryPaymentCard .dc-payment-method').forEach(m => {
        m.classList.remove('selected');
        m.querySelector('input').checked = false;
    });
    el.classList.add('selected');
    el.querySelector('input').checked = true;
    selectedRetryMethod = method;
}

function processRetryPayment() {
    if (!selectedRetryMethod) {
        alert('Please select a payment method.');
        return;
    }

    const btn = document.getElementById('retryPayBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

    const orderId = <?= (int)$order['id'] ?>;
    const orderNumber = <?= json_encode($order['order_number']) ?>;
    const total = <?= (float)$order['total'] ?>;

    const data = {
        order_id: orderId,
        order_number: orderNumber,
        total: total
    };

    if (selectedRetryMethod === 'upi_direct') {
        openUpiDirectModal(data);
    } else if (selectedRetryMethod === 'upi_custom') {
        fetch(appUrl + '/pay/upi-custom/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'csrf_token=' + csrfToken + '&order_id=' + orderId
        })
        .then(r => r.json())
        .then(upiData => {
            if (upiData.success && upiData.payment_url) {
                window.location.href = upiData.payment_url;
            } else {
                alert(upiData.message || 'Failed to initiate UPI payment.');
                restoreRetryBtn(btn);
            }
        })
        .catch(() => {
            alert('Error reaching UPI gateway.');
            restoreRetryBtn(btn);
        });
    } else if (selectedRetryMethod === 'razorpay') {
        fetch(appUrl + '/pay/razorpay/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'csrf_token=' + csrfToken + '&order_id=' + orderId
        })
        .then(r => r.json())
        .then(rzpData => {
            if (rzpData.success) {
                initiatePayment('razorpay', { ...data, ...rzpData });
            } else {
                alert(rzpData.message || 'Failed to initiate Razorpay.');
                restoreRetryBtn(btn);
            }
        })
        .catch(() => {
            alert('Error creating Razorpay order.');
            restoreRetryBtn(btn);
        });
    } else if (selectedRetryMethod === 'paypal') {
        fetch(appUrl + '/pay/paypal/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: 'csrf_token=' + csrfToken + '&order_id=' + orderId
        })
        .then(r => r.json())
        .then(ppData => {
            if (ppData.success && ppData.checkout_url) {
                window.location.href = ppData.checkout_url;
            } else {
                alert(ppData.message || 'Failed to initiate PayPal.');
                restoreRetryBtn(btn);
            }
        })
        .catch(() => {
            alert('Error creating PayPal order.');
            restoreRetryBtn(btn);
        });
    }
}

function restoreRetryBtn(btn) {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-lock me-2"></i>Pay ' + '<?= formatPrice($order['total']) ?>';
}

// Direct UPI Modal trigger variables and methods
let upiTimerInterval = null;
let upiModalInstance = null;

function copyUpiDetails(elementId) {
    const text = document.getElementById(elementId).innerText;
    const tempInput = document.createElement('textarea');
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    
    const btn = event.currentTarget || document.activeElement;
    if (btn) {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-lg text-success"></i> Copied';
        btn.classList.add('text-success');
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.classList.remove('text-success');
        }, 1500);
    }
}

function goToStep(step) {
    const s1Content = document.getElementById('upiStep1Content');
    const s2Content = document.getElementById('upiStep2Content');
    const label = document.getElementById('upiStepLabel');
    const indicator = document.getElementById('upiStepIndicator');
    const bar = document.getElementById('upiProgressBar');
    const stepperContainer = document.getElementById('upiStepperContainer');

    if (step === 1) {
        s2Content.style.display = 'none';
        s1Content.style.display = 'block';
        if (stepperContainer) stepperContainer.style.display = 'none';
        label.innerText = 'Complete Your Payment';
        indicator.innerText = 'Step 1 of 2';
        bar.style.width = '50%';
    } else {
        s1Content.style.display = 'none';
        s2Content.style.display = 'block';
        if (stepperContainer) stepperContainer.style.display = 'block';
        label.innerText = 'Verify Your Payment';
        indicator.innerText = 'Step 2 of 2';
        bar.style.width = '100%';
    }
}

function openUpiDirectModal(data) {
    if (upiTimerInterval) clearInterval(upiTimerInterval);
    goToStep(1);

    const formattedAmount = '₹' + parseFloat(data.total).toFixed(2);
    document.getElementById('upiModalAmount').innerText = formattedAmount;
    document.getElementById('upiSummaryAmount').innerText = formattedAmount;
    
    document.getElementById('upiModalOrderNum').innerText = data.order_number;
    document.getElementById('upiSummaryOrderNum').innerText = data.order_number;
    document.getElementById('upiModalOrderIdInput').value = data.order_id;
    
    document.getElementById('upiUtrInput').value = '';
    document.getElementById('upiScreenshotInput').value = '';
    
    const indicator = document.getElementById('fileSelectedIndicator');
    if (indicator) indicator.classList.add('d-none');
    
    const verifyBtn = document.getElementById('upiVerifyBtn');
    verifyBtn.disabled = false;
    verifyBtn.innerHTML = '<i class="bi bi-shield-check me-2"></i>Verify Payment';
    
    const upiId = <?= json_encode($settingModel->get('upi_id', '')) ?>;
    const upiName = <?= json_encode($settingModel->get('upi_merchant_name', 'Digiws')) ?>;
    const upiUri = `upi://pay?pa=${encodeURIComponent(upiId)}&pn=${encodeURIComponent(upiName)}&am=${parseFloat(data.total).toFixed(2)}&cu=INR`;
    document.getElementById('upiQrCodeImg').src = `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(upiUri)}`;
    
    const idEl = document.getElementById('upiIdValue');
    if (idEl) idEl.innerText = upiId;
    const displayAddressEl = document.getElementById('upiDisplayAddress');
    if (displayAddressEl) displayAddressEl.innerText = upiId;
    const phoneEl = document.getElementById('upiPhoneValue');
    if (phoneEl) phoneEl.innerText = upiId.split('@')[0];
    
    let timeLeft = 300;
    const timerEl = document.getElementById('upiTimer');
    timerEl.innerText = "05:00";
    
    upiTimerInterval = setInterval(() => {
        timeLeft--;
        if (timeLeft <= 0) {
            clearInterval(upiTimerInterval);
            timerEl.innerText = "EXPIRED";
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = "QR Code Expired. Please close and try again.";
        } else {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerEl.innerText = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
    }, 1000);

    const btn = document.getElementById('retryPayBtn');
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-lock me-2"></i>Pay ' + '<?= formatPrice($order['total']) ?>';

    if (!upiModalInstance) {
        upiModalInstance = new bootstrap.Modal(document.getElementById('upiDirectModal'));
    }
    upiModalInstance.show();
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('upiVerifyForm');
    if (form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const utrVal = document.getElementById('upiUtrInput').value.trim();
            const screenshotInput = document.getElementById('upiScreenshotInput');
            const fileSelected = screenshotInput.files && screenshotInput.files.length > 0;
            
            if (!utrVal && !fileSelected) {
                alert('Please enter a UTR number or upload a screenshot.');
                return;
            }
            
            const verifyBtn = document.getElementById('upiVerifyBtn');
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = '<span class="dc-spinner dc-spinner-sm me-2"></span>Verifying...';
            
            const formData = new FormData(form);
            
            fetch(appUrl + '/pay/upi-direct/verify', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (upiTimerInterval) clearInterval(upiTimerInterval);
                    if (upiModalInstance) upiModalInstance.hide();
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Verification failed.');
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = '<i class="bi bi-shield-check me-2"></i>Verify Payment';
                }
            })
            .catch(err => {
                console.error(err);
                alert('Something went wrong during verification.');
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<i class="bi bi-shield-check me-2"></i>Verify Payment';
            });
        });
    }

    // Drag and Drop Zone logic
    const dropZone = document.getElementById('upiDropZone');
    const fileInput = document.getElementById('upiScreenshotInput');
    const fileIndicator = document.getElementById('fileSelectedIndicator');
    const fileNameText = document.getElementById('selectedFileName');

    if (dropZone && fileInput) {
        dropZone.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                fileNameText.innerText = fileInput.files[0].name;
                fileIndicator.classList.remove('d-none');
            }
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#6366f1';
            dropZone.style.background = '#eef2ff';
        });

        ['dragleave', 'dragend'].forEach(type => {
            dropZone.addEventListener(type, () => {
                dropZone.style.borderColor = '#cbd5e1';
                dropZone.style.background = '#f8fafc';
            });
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#cbd5e1';
            dropZone.style.background = '#f8fafc';

            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                fileNameText.innerText = e.dataTransfer.files[0].name;
                fileIndicator.classList.remove('d-none');
            }
        });
    }
});
</script>

<?php include VIEWS_PATH . '/upipay.php'; ?>
<?php endif; ?>

<style>
.dc-sticky-sidebar {
    position: sticky;
    top: 90px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    z-index: 10;
}
.dc-payment-card {
    background: var(--bg-surface, #ffffff);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: var(--radius-lg, 16px);
    padding: 24px;
    box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.04));
}
.dc-cart-summary {
    position: static !important;
    width: 100% !important;
}
.dc-payment-method {
    transition: all 0.2s ease-in-out;
    border: 1.5px solid var(--border-color, #cbd5e1) !important;
}
.dc-payment-method.selected {
    border-color: var(--primary, #2563EB) !important;
    background-color: rgba(37, 99, 235, 0.04) !important;
}
.dc-payment-method:hover:not(.selected) {
    border-color: var(--primary-hover, #1d4ed8) !important;
    background-color: rgba(37, 99, 235, 0.02) !important;
}
</style>
</section>
