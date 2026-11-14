/**
 * Digiws - Payment Integration Scripts
 */

function initiatePayment(method, data) {
    if (method === 'razorpay') {
        initRazorpay(data);
    } else if (method === 'stripe') {
        window.location.href = data.checkout_url;
    } else if (method === 'paypal') {
        window.location.href = data.checkout_url;
    }
}

function initRazorpay(data) {
    const options = {
        "key": data.key,
        "amount": data.amount,
        "currency": data.currency,
        "name": data.name,
        "description": "Order #" + data.order_number,
        "image": data.logo,
        "order_id": data.razorpay_order_id,
        "handler": function (response) {
            // Verify payment on backend
            verifyRazorpayPayment(response, data.order_id);
        },
        "prefill": {
            "name": data.customer_name,
            "email": data.customer_email,
            "contact": data.customer_phone
        },
        "theme": {
            "color": "#8b5cf6"
        },
        "modal": {
            "ondismiss": function() {
                resetCheckoutButton();
                showToast('warning', 'Payment cancelled by user.');
            }
        }
    };
    
    const rzp = new Razorpay(options);
    
    rzp.on('payment.failed', function (response) {
        resetCheckoutButton();
        showToast('danger', 'Payment failed: ' + response.error.description);
        // Optionally log failed payment to backend
    });
    
    rzp.open();
}

function verifyRazorpayPayment(paymentResponse, orderId) {
    const btn = document.getElementById('payBtn') || document.getElementById('retryPayBtn');
    if (btn) {
        btn.innerHTML = '<span class="dc-spinner me-2"></span>Verifying Payment...';
    }
    
    fetch(window.appUrl('pay/razorpay/verify'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: `csrf_token=${encodeURIComponent(window.csrfToken || '')}&razorpay_payment_id=${encodeURIComponent(paymentResponse.razorpay_payment_id)}&razorpay_order_id=${encodeURIComponent(paymentResponse.razorpay_order_id)}&razorpay_signature=${encodeURIComponent(paymentResponse.razorpay_signature)}&order_id=${encodeURIComponent(orderId)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect || window.appUrl('pay/success?order=' + orderId);
        } else {
            showToast('danger', 'Payment verification failed!');
            resetCheckoutButton();
        }
    })
    .catch(() => {
        showToast('danger', 'Server error during verification.');
        resetCheckoutButton();
    });
}

function resetCheckoutButton() {
    const btn = document.getElementById('payBtn') || document.getElementById('retryPayBtn');
    if (btn) {
        btn.disabled = false;
        if (btn.id === 'retryPayBtn') {
            btn.innerHTML = `<i class="bi bi-lock-fill me-2"></i>Pay Now`;
        } else {
            btn.innerHTML = `<i class="bi bi-lock me-2"></i>Pay`;
        }
    }
}
