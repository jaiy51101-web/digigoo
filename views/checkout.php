<?php
$checkoutNameParts = isLoggedIn() ? explode(' ', trim(currentUser()['name']), 2) : ['', ''];
$checkoutFirstName = $checkoutNameParts[0] ?? '';
$checkoutLastName  = $checkoutNameParts[1] ?? (isLoggedIn() ? '-' : '');
?>
<!-- Checkout Page -->
<section class="dc-section" style="padding-top:30px;">
    <div class="container">
        <h2 class="mb-4"><i class="bi bi-credit-card me-2"></i>Checkout</h2>
        <div class="row g-4">
            <!-- Left Side: Billing & Payment -->
            <div class="col-lg-7">
                <!-- Billing Details -->
                <div class="dc-card mb-4">
                    <h5 class="mb-4">Billing Details</h5>
                    <form id="checkoutForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="dc-form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="dc-form-control" placeholder="John" value="<?= htmlspecialchars($checkoutFirstName) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="dc-form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="dc-form-control" placeholder="Doe" value="<?= htmlspecialchars($checkoutLastName) ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="dc-form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="dc-form-control" placeholder="john@example.com" value="<?= isLoggedIn() ? currentUser()['email'] : '' ?>" required <?= isLoggedIn() ? 'readonly' : '' ?>>
                            </div>
                            <div class="col-12">
                                <label class="dc-form-label">Phone Number (WhatsApp) <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="dc-form-control" placeholder="9876543210" value="<?= isLoggedIn() ? (currentUser()['phone'] ?? '') : '' ?>" required>
                                <small class="text-muted">Enter 10-digit mobile number for UPI payment</small>
                            </div>
                            
                            <?php if (!isLoggedIn()): ?>
                            <div class="col-12 mt-3">
                                <div class="p-3 border rounded bg-light">
                                    <label class="dc-form-label">Create Account Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="dc-form-control" placeholder="Set a password for your account" required>
                                    <small class="text-muted">An account will be automatically created for you to access your digital downloads.</small>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Payment Methods Card -->
                <div class="dc-card" id="paymentMethodsCard" style="display: <?= $cartData['total'] <= 0 ? 'none' : 'block' ?>;">
                    <h5 class="mb-3">Payment Method</h5>
                    <div class="dc-payment-methods" id="paymentMethods">

                        <?php if ($settingModel->get('payment_upi_direct_enabled', 'No') === 'Yes'): ?>
                        <div class="dc-payment-method" data-method="upi_direct" onclick="selectPayment('upi_direct', this)">
                            <input type="radio" name="payment_method" value="upi_direct" style="accent-color:var(--accent-primary);">
                            <div>
                                <h6>Direct UPI (Scan & Pay)</h6>
                                <small>Pay using QR Code & upload proof / UTR</small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($settingModel->get('payment_upi_custom_enabled', 'No') === 'Yes'): ?>
                        <div class="dc-payment-method" data-method="upi_custom" onclick="selectPayment('upi_custom', this)">
                            <input type="radio" name="payment_method" value="upi_custom" style="accent-color:var(--accent-primary);">
                            <div>
                                <h6>UPI Gateway</h6>
                                <small>Pay using any UPI app (Fast & Secure)</small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($settingModel->get('payment_paypal_enabled', 'No') === 'Yes'): ?>
                        <div class="dc-payment-method" data-method="paypal" onclick="selectPayment('paypal', this)">
                            <input type="radio" name="payment_method" value="paypal" style="accent-color:var(--accent-primary);">
                            <div>
                                <h6>PayPal</h6>
                                <small>Secure Global Payments</small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($settingModel->get('payment_razorpay_enabled', 'No') === 'Yes'): ?>
                        <div class="dc-payment-method" data-method="razorpay" onclick="selectPayment('razorpay', this)">
                            <input type="radio" name="payment_method" value="razorpay" style="accent-color:var(--accent-primary);">
                            <div>
                                <h6>Razorpay</h6>
                                <small>Cards, Net Banking, Wallets</small>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($settingModel->get('payment_stripe_enabled', 'No') === 'Yes'): ?>
                        <div class="dc-payment-method" data-method="stripe" onclick="selectPayment('stripe', this)">
                            <input type="radio" name="payment_method" value="stripe" style="accent-color:var(--accent-primary);">
                            <div>
                                <h6>Stripe</h6>
                                <small>Safe Card Payments (International)</small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Free Checkout Card -->
                <div class="dc-card text-center py-4 border border-success rounded" id="freeCheckoutCard" style="background: rgba(34, 197, 94, 0.03); display: <?= $cartData['total'] <= 0 ? 'block' : 'none' ?>;">
                    <div class="mb-3">
                        <i class="bi bi-gift-fill text-success" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="mb-1 text-success">Free Checkout</h5>
                    <p class="text-muted small mb-0 px-3">All items in your cart are free! No payment is required. Just click the complete order button below to get instant access.</p>
                </div>
            </div>

            <!-- Right Side: Order Summary & Coupon -->
            <div class="col-lg-5">
                <div class="dc-cart-summary" style="position:sticky;top:85px;">
                    <!-- Order Items (Moved to Right) -->
                    <div class="mb-4">
                        <h6 class="mb-3">Order Items</h6>
                        <?php foreach ($cartData['items'] as $item): ?>
                        <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid var(--border-light);">
                            <img src="<?= $item['thumbnail'] ? uploadUrl($item['thumbnail']) : asset('images/placeholder.png') ?>" alt="" style="width:45px;height:32px;border-radius:4px;object-fit:cover;">
                            <div class="flex-fill">
                                <h6 style="font-size:0.85rem;margin:0;"><?= htmlspecialchars($item['title']) ?></h6>
                            </div>
                            <strong style="font-size:0.85rem;"><?= formatPrice($item['sale_price']) ?></strong>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <h5 class="mt-4 mb-3">Payment Summary</h5>
                    <div class="summary-row"><span>Subtotal</span><span id="summarySubtotal"><?= formatPrice($cartData['subtotal']) ?></span></div>
                    <div class="summary-row"><span>Tax (GST)</span><span id="summaryTax"><?= formatPrice($cartData['tax']) ?></span></div>
                    
                    <div id="discountRow" class="summary-row discount" style="<?= $cartData['discount'] > 0 ? '' : 'display:none;' ?>">
                        <span>Discount (<span id="couponText"><?= $cartData['coupon'] ?? '' ?></span>) <a href="javascript:void(0)" onclick="removeCoupon()" class="ms-1 text-danger"><i class="bi bi-x-circle"></i></a></span>
                        <span id="summaryDiscount">-<?= formatPrice($cartData['discount']) ?></span>
                    </div>

                    <div class="summary-row total"><span>Total</span><span id="totalAmount"><?= formatPrice($cartData['total']) ?></span></div>

                    <!-- Coupon Code Section -->
                    <div class="mt-3">
                        <a href="javascript:void(0)" id="couponToggle" class="text-decoration-none" onclick="toggleCouponBox()">
                            <i class="bi bi-tag-fill me-1"></i> Have a coupon?
                        </a>
                        <div id="couponBox" class="mt-2" style="display:none;">
                            <div class="input-group">
                                <input type="text" id="couponInput" class="dc-form-control" placeholder="Enter coupon code" style="padding: 8px 12px;">
                                <button class="btn-dc-primary" type="button" id="applyCouponBtn" onclick="applyCoupon()">Apply</button>
                            </div>
                            <div id="couponMessage" class="mt-2" style="font-size:0.8rem;"></div>
                        </div>
                    </div>

                    <button type="button" class="btn-dc-primary w-100 mt-3" id="payBtn" onclick="processCheckout()">
                        <?php if ($cartData['total'] <= 0): ?>
                        <i class="bi bi-cloud-arrow-down me-2"></i>Complete Checkout
                        <?php else: ?>
                        <i class="bi bi-lock me-2"></i>Pay <span id="btnPayAmount"><?= formatPrice($cartData['total']) ?></span>
                        <?php endif; ?>
                    </button>

                    <p class="text-center mt-3" style="font-size:0.8rem;color:var(--text-muted);">
                        <i class="bi bi-shield-check me-1"></i>Your payment is secure and encrypted
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

            </div>
        </div>
    </div>
</div>

<style>
.modal-lg { max-width: 500px; }
@media (max-width: 576px) {
    .modal-dialog { margin: 10px; }
    .modal-body { height: 80vh !important; }
}
</style>

<!-- Scripts -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="<?= asset('js/payment.js') ?>"></script>
<script>
window.onerror = function(msg, url, line) {
    alert('JS Error: ' + msg + ' at ' + url + ':' + line);
    return false;
};
const csrfToken = '<?= csrfToken() ?>';
window.csrfToken = csrfToken;
const appUrl = '<?= APP_URL ?>';
let selectedPaymentMethod = <?= $cartData['total'] <= 0 ? "'free'" : "'upi_direct'" ?>;

window.addEventListener('DOMContentLoaded', () => {
    <?php if ($cartData['total'] > 0): ?>
    let upiEl = document.querySelector('.dc-payment-method[data-method="upi_direct"]');
    if (!upiEl) {
        upiEl = document.querySelector('.dc-payment-method[data-method="upi_custom"]');
    }
    if (upiEl) {
        selectPayment(upiEl.getAttribute('data-method'), upiEl);
    } else {
        let firstEl = document.querySelector('.dc-payment-method');
        if (firstEl) selectPayment(firstEl.getAttribute('data-method'), firstEl);
    }
    <?php endif; ?>
});

function selectPayment(method, el) {
    document.querySelectorAll('.dc-payment-method').forEach(m => { m.classList.remove('selected'); m.querySelector('input').checked = false; });
    el.classList.add('selected');
    el.querySelector('input').checked = true;
    selectedPaymentMethod = method;
}

function toggleCouponBox() {
    const box = document.getElementById('couponBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

function applyCoupon() {
    const code = document.getElementById('couponInput').value;
    const btn = document.getElementById('applyCouponBtn');
    const msg = document.getElementById('couponMessage');

    if (!code) return;

    btn.disabled = true;
    btn.innerHTML = '<span class="dc-spinner dc-spinner-sm"></span>';

    fetch(appUrl + '/cart/coupon', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: 'csrf_token=' + csrfToken + '&coupon_code=' + code
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerText = 'Apply';

        if (data.success) {
            updateSummary(data.cart);
            msg.className = 'mt-2 text-success';
            msg.innerText = data.message;
            document.getElementById('couponBox').style.display = 'none';
            document.getElementById('couponToggle').style.display = 'none';
        } else {
            msg.className = 'mt-2 text-danger';
            msg.innerText = data.message;
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerText = 'Apply';
        alert('Failed to apply coupon.');
    });
}

function removeCoupon() {
    fetch(appUrl + '/cart/remove-coupon', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: 'csrf_token=' + csrfToken
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            updateSummary(data.cart);
            document.getElementById('couponToggle').style.display = 'block';
            document.getElementById('couponMessage').innerText = '';
            document.getElementById('couponInput').value = '';
        }
    });
}

function restorePayButton(btn) {
    btn.disabled = false;
    if (selectedPaymentMethod === 'free') {
        btn.innerHTML = '<i class="bi bi-cloud-arrow-down me-2"></i>Complete Checkout';
    } else {
        btn.innerHTML = '<i class="bi bi-lock me-2"></i>Pay <span id="btnPayAmount">' + document.getElementById('totalAmount').innerText + '</span>';
    }
}

function updateSummary(cart) {
    document.getElementById('summarySubtotal').innerText = cart.subtotal_formatted;
    document.getElementById('summaryTax').innerText = cart.tax_formatted;
    document.getElementById('totalAmount').innerText = cart.total_formatted;

    const pmCard = document.getElementById('paymentMethodsCard');
    const freeCard = document.getElementById('freeCheckoutCard');
    const payBtn = document.getElementById('payBtn');

    if (cart.total <= 0) {
        if (pmCard) pmCard.style.display = 'none';
        if (freeCard) freeCard.style.display = 'block';
        payBtn.innerHTML = '<i class="bi bi-cloud-arrow-down me-2"></i>Complete Checkout';
        selectedPaymentMethod = 'free';
    } else {
        if (pmCard) pmCard.style.display = 'block';
        if (freeCard) freeCard.style.display = 'none';
        payBtn.innerHTML = '<i class="bi bi-lock me-2"></i>Pay <span id="btnPayAmount">' + cart.total_formatted + '</span>';
        
        // Restore default selected method if it was set to free
        if (selectedPaymentMethod === 'free') {
            let upiEl = document.querySelector('.dc-payment-method[data-method="upi_direct"]');
            if (!upiEl) {
                upiEl = document.querySelector('.dc-payment-method[data-method="upi_custom"]');
            }
            if (upiEl) {
                selectPayment(upiEl.getAttribute('data-method'), upiEl);
            } else {
                let firstEl = document.querySelector('.dc-payment-method');
                if (firstEl) selectPayment(firstEl.getAttribute('data-method'), firstEl);
            }
        }
    }

    const discRow = document.getElementById('discountRow');
    if (cart.discount > 0) {
        discRow.style.display = 'flex';
        document.getElementById('summaryDiscount').innerText = '-' + cart.discount_formatted;
        document.getElementById('couponText').innerText = cart.coupon;
    } else {
        discRow.style.display = 'none';
    }
}

function processCheckout() {
    console.log('processCheckout called');
    const form = document.getElementById('checkoutForm');
    if (!form.checkValidity()) {
        alert('Please fill all required fields correctly.');
        form.reportValidity();
        return;
    }

    const btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="dc-spinner me-2"></span>Processing...';

    const formData = new FormData(form);
    formData.append('csrf_token', csrfToken);
    formData.append('payment_method', selectedPaymentMethod);

    fetch(appUrl + '/checkout/process', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.total <= 0 || selectedPaymentMethod === 'free') {
                window.location.href = data.redirect_url || (appUrl + '/pay/success?order=' + data.order_id);
            } else if (selectedPaymentMethod === 'upi_direct') {
                openUpiDirectModal(data);
            } else if (selectedPaymentMethod === 'upi_custom') {
                // Call upiCreate to get redirected to the gateway
                fetch(appUrl + '/pay/upi-custom/create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body: 'csrf_token=' + csrfToken + '&order_id=' + data.order_id
                })
                .then(r => r.json())
                .then(upiData => {
                    if (upiData.success && upiData.payment_url) {
                        window.location.href = upiData.payment_url;
                    } else {
                        alert(upiData.message || 'Failed to initiate UPI payment.');
                        restorePayButton(btn);
                    }
                })
                .catch(() => {
                    alert('Error reaching UPI gateway.');
                    restorePayButton(btn);
                });
            } else if (selectedPaymentMethod === 'razorpay') {
                // For Razorpay, we need to create a Razorpay order first
                fetch(appUrl + '/pay/razorpay/create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body: 'csrf_token=' + csrfToken + '&order_id=' + data.order_id
                })
                .then(r => r.json())
                .then(rzpData => {
                    if (rzpData.success) {
                        initiatePayment('razorpay', { ...data, ...rzpData });
                    } else {
                        alert(rzpData.message || 'Failed to initiate Razorpay.');
                        restorePayButton(btn);
                    }
                })
                .catch(() => {
                    alert('Error creating Razorpay order.');
                    restorePayButton(btn);
                });
            } else if (selectedPaymentMethod === 'paypal') {
                // For PayPal, we fetch the REST checkout order URL and redirect to it
                fetch(appUrl + '/pay/paypal/create', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body: 'csrf_token=' + csrfToken + '&order_id=' + data.order_id
                })
                .then(r => r.json())
                .then(ppData => {
                    if (ppData.success && ppData.checkout_url) {
                        window.location.href = ppData.checkout_url;
                    } else {
                        alert(ppData.message || 'Failed to initiate PayPal.');
                        restorePayButton(btn);
                    }
                })
                .catch(() => {
                    alert('Error creating PayPal order.');
                    restorePayButton(btn);
                });
            } else {
                window.location.href = data.redirect_url || (appUrl + '/user/orders?success_modal=1&order_id=' + data.order_id);
            }
        } else {
            if (selectedPaymentMethod === 'free') {
                window.location.href = appUrl + '/user/orders';
            } else {
                alert(data.message || 'Failed to create order.');
                restorePayButton(btn);
            }
        }
    })
    .catch((err) => {
        console.error(err);
        if (selectedPaymentMethod === 'free') {
            window.location.href = appUrl + '/user/orders';
        } else {
            alert('Something went wrong. Please try again.');
            restorePayButton(btn);
        }
    });
}

const upiId = <?= json_encode($settingModel->get('upi_id', '')) ?>;
const upiName = <?= json_encode($settingModel->get('upi_merchant_name', 'Digiws')) ?>;
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
    
    // Reset to step 1
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

    const payBtn = document.getElementById('payBtn');
    payBtn.disabled = false;
    payBtn.innerHTML = '<i class="bi bi-lock me-2"></i>Pay ' + document.getElementById('totalAmount').innerText;

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
