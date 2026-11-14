<?php
// ====== Digiws Integrated Logic ======
date_default_timezone_set("Asia/Kolkata");

require_once '../includes/config.php';
require_once '../includes/Database.php';
require_once '../includes/helpers.php';

$db = Database::getInstance();

try {
    $link_token = isset($_GET["token"]) ? $_GET["token"] : '';
    if (empty($link_token)) {
        die("Invalid payment link");
    }

    $result = $db->fetch("SELECT order_id, created_at FROM payment_links WHERE link_token = ? LIMIT 1", [$link_token]);

    if (!$result) {
        die("Invalid or expired payment link");
    }

    $order_id = $result['order_id'];
    $created_at = strtotime($result['created_at']);
    $current_time = time();

    if (($current_time - $created_at) > (10 * 60)) { // 10 minutes expiry
        die("Payment link has expired");
    }

    $res_p = $db->fetch("SELECT * FROM orders where id = ?", [$order_id]);
    if (!$res_p) {
        die("Order not found");
    }

    $amount = (float)$res_p['total'];
    $order_num = $res_p['order_number'];
    // In this app, we use the Admin's merchant connection (since it's a single-vendor store)
    $admin_data = $db->fetch("SELECT * FROM users where role = 'admin' LIMIT 1") ?: [];
    
    $user_token = $admin_data['user_token'] ?? 'default_admin';
    $default_upi = $admin_data['default_upi'] ?? 'paytm@upi';
    $redirect_url = url('user/orders');
    $cxrkalwaremark = "Order #$order_num";  // remark
    $cxrbytectxnref = $order_num;          // reference

    $res_p_upi = null;
    try {
        $res_p_upi = $db->fetch("SELECT * FROM paytm_tokens where user_token = ? ORDER BY id DESC LIMIT 1", [$user_token]);
    } catch (Exception $e) {
        // Table might be missing, ignore and use fallback
    }

    $upi_id = !empty($res_p_upi['Upiid']) ? $res_p_upi['Upiid'] : (!empty($default_upi) ? $default_upi : 'paytm@upi');

    $unitId = APP_NAME;
    $color_theme = '#6366f1'; // Premium brand indigo

    $userlogo = asset('images/logo.png');
    if (!empty($admin_data["avatar"])) {
        $userlogo = uploadUrl($admin_data["avatar"]);
    }

    // Standard UPI link
    $orders = "upi://pay?pa=" . rawurlencode($upi_id) .
        "&pn=" . rawurlencode($unitId) .
        "&am=" . number_format($amount, 2, '.', '') .
        "&tn=" . rawurlencode($cxrkalwaremark) .
        "&tr=" . rawurlencode($cxrbytectxnref);

    $paytm_sign = "AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr";
    $paytmintent = "paytmmp://cash_wallet?pa=" . rawurlencode($upi_id) .
        "&pn=" . rawurlencode($unitId) .
        "&am=" . number_format($amount, 2, '.', '') .
        "&cu=INR&tn=" . rawurlencode($cxrkalwaremark) .
        "&tr=" . rawurlencode($cxrbytectxnref) .
        "&mc=4722&sign=" . rawurlencode($paytm_sign) .
        "&featuretype=money_transfer";

    $safe_js = [
        'redirect_url' => $redirect_url,
        'upi_link'     => $orders,
        'qrCodeUrl'    => '',
        'amount'       => number_format($amount, 2, '.', ''),
        'merchant'     => $unitId,
        'byteTx'       => $cxrkalwaremark,
        'paytmintent'  => $paytmintent,
        'upi_id'       => $upi_id,
        'color_theme'  => $color_theme,
        'userlogo'     => $userlogo,
        'order_id'     => $order_id
    ];

} catch (Exception $e) {
    error_log($e->getMessage());
    die("Something went wrong. Please try again later.");
}
// ====== END: PHP LOGIC ======
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Secure Payment - <?php echo htmlspecialchars($unitId); ?></title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<link rel="icon" href="<?php echo htmlspecialchars($userlogo); ?>" />

<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Outfit',sans-serif; }

body {
    background-color: #f1f5f9;
    display: flex; justify-content: center; align-items: center;
    min-height: 100vh; padding: 20px;
    background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
    background-size: 24px 24px;
}

.payment-card {
    width: 100%; max-width: 800px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1), 0 0 0 1px rgba(0,0,0,0.05);
    display: flex; overflow: hidden;
    animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes slideUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }

/* Section 1: Payment Details & UPI ID */
.pay-details-sec {
    flex: 1;
    background: #0f172a;
    color: #ffffff;
    padding: 40px;
    position: relative;
    display: flex; flex-direction: column;
}
.pay-details-sec::before {
    content:''; position:absolute; inset:0;
    background: linear-gradient(135deg, rgba(99,102,241,0.2) 0%, rgba(168,85,247,0.1) 100%);
    pointer-events: none;
}

.merchant-logo {
    width: 56px; height: 56px; border-radius: 12px;
    object-fit: contain; background: #fff; padding: 4px;
    margin-bottom: 16px; position: relative; z-index: 2;
}

.merchant-name { font-size: 1.2rem; font-weight: 500; color: #cbd5e1; position: relative; z-index: 2; margin-bottom: 4px; display:flex; align-items:center; gap:6px; }
.merchant-name i { color:#10b981; font-size:1rem; }

.amount-display {
    font-size: 3rem; font-weight: 800; line-height: 1; letter-spacing: -1px;
    margin-bottom: 30px; position: relative; z-index: 2; color: #fff;
}

.instruction-box {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px; padding: 20px;
    position: relative; z-index: 2; margin-bottom: 24px;
}
.instruction-box h4 { font-size: 1.1rem; font-weight: 600; margin-bottom: 12px; color: #e2e8f0; }
.instruction-list { list-style: none; }
.instruction-list li { font-size: 0.95rem; color: #cbd5e1; margin-bottom: 10px; display: flex; gap: 10px; }
.instruction-list li i { color: #8b5cf6; margin-top:2px; }

.upi-id-box {
    background: #1e293b; border: 1px solid #334155;
    border-radius: 12px; padding: 16px; position: relative; z-index: 2;
    display: flex; flex-direction: column; gap: 8px;
}
.upi-label { font-size: 0.85rem; color: #94a3b8; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; }
.upi-val-row { display: flex; align-items: center; justify-content: space-between; }
.upi-val { font-size: 1.1rem; font-weight: 700; color: #fff; word-break: break-all; padding-right: 10px; }
.btn-copy { 
    background: #6366f1; color: #fff; border: none; padding: 8px 16px;
    border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s; white-space: nowrap;
}
.btn-copy:hover { background: #4f46e5; box-shadow: 0 4px 12px rgba(99,102,241,0.4); }

/* Section 2: QR Code & Logos */
.pay-qr-sec {
    flex: 1;
    background: #ffffff;
    padding: 40px;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
}

.timer-box {
    font-size: 1rem; font-weight: 700; color: #ef4444;
    background: #fef2f2; padding: 8px 20px; border-radius: 20px; margin-bottom: 30px;
    display: inline-flex; align-items: center; gap: 8px; border: 1px solid #fca5a5;
}

.qr-wrapper {
    background: #ffffff;
    padding: 20px; border-radius: 24px;
    border: 3px dashed #e2e8f0;
    position: relative; margin-bottom: 30px;
    transition: all 0.3s;
}
.qr-wrapper:hover { border-color: #6366f1; border-style: solid; box-shadow: 0 20px 40px -10px rgba(99,102,241,0.2); transform: translateY(-5px); }
#qr-img { width: 220px; height: 220px; display: block; border-radius: 12px; }
.qr-center-logo {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
    width: 50px; height: 50px; background: #fff; border-radius: 14px; padding: 5px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.scan-text { font-size: 1rem; color: #475569; font-weight: 600; margin-bottom: 20px; text-align: center; }

.supported-apps { display: flex; justify-content: center; gap: 16px; margin-bottom: 24px; }
.supported-apps img { width: 40px; height: 40px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.2s; }
.supported-apps img:hover { transform: scale(1.1); }

.mobile-intent-btn {
    width: 100%; padding: 14px;
    background: #10b981; color: #fff; border: none; border-radius: 12px;
    font-size: 1.05rem; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 10px;
    cursor: pointer; transition: all 0.2s; box-shadow: 0 10px 15px -3px rgba(16,185,129,0.3);
}
.mobile-intent-btn:hover { background: #059669; transform: translateY(-2px); }

.success-screen, .failure-screen {
    position: absolute; inset: 0; background: #ffffff; z-index: 100;
    display: none; flex-direction: column; align-items: center; justify-content: center; text-align: center;
}
.status-icon { font-size: 80px; margin-bottom: 20px; }
.status-success { color: #10b981; }
.status-error { color: #ef4444; }
.status-title { font-size: 2rem; font-weight: 800; color: #0f172a; margin-bottom: 10px; }
.status-desc { font-size: 1rem; color: #64748b; }

@media (max-width: 768px) {
    .payment-card { flex-direction: column; }
    .pay-details-sec { padding: 30px 20px; }
    .pay-qr-sec { padding: 30px 20px; }
    .amount-display { font-size: 2.5rem; margin-bottom: 20px; }
    #qr-img { width: 180px; height: 180px; }
}
</style>
</head>
<body oncontextmenu="return false;" style="user-select:none;">

<div class="payment-card">
    <!-- ROW/COLUMN 1: Instructions & Details -->
    <div class="pay-details-sec">
        <img src="<?php echo htmlspecialchars($userlogo, ENT_QUOTES); ?>" class="merchant-logo" alt="Logo">
        <div class="merchant-name"><?php echo htmlspecialchars($unitId); ?> <i class="fas fa-check-circle"></i></div>
        
        <div class="amount-display">₹<?php echo number_format($amount, 2); ?></div>

        <div class="instruction-box">
            <h4>How to pay?</h4>
            <ul class="instruction-list">
                <li><i class="far fa-check-circle"></i> Open any UPI App (GPay, Paytm, PhonePe)</li>
                <li><i class="far fa-check-circle"></i> Scan the QR code shown on the screen</li>
                <li><i class="far fa-check-circle"></i> Or copy the UPI ID below and pay</li>
                <li><i class="far fa-check-circle"></i> Do not refresh the page until payment is done</li>
            </ul>
        </div>

        <div class="upi-id-box">
            <div class="upi-label">Merchant UPI ID</div>
            <div class="upi-val-row">
                <span class="upi-val"><?php echo htmlspecialchars($upi_id); ?></span>
                <button class="btn-copy" onclick="copyUpiId()">Copy ID</button>
            </div>
        </div>
    </div>

    <!-- ROW/COLUMN 2: QR & Supported Apps -->
    <div class="pay-qr-sec">
        <div class="timer-box"><i class="fas fa-info-circle"></i> Complete within <span id="timeout">05:00</span></div>

        <div class="qr-wrapper">
            <img id="qr-img" src="" alt="UPI QR Code">
            <img src="<?php echo htmlspecialchars($userlogo, ENT_QUOTES); ?>" class="qr-center-logo" alt="logo">
        </div>

        <div class="scan-text">Scan with any supported UPI App</div>

        <div class="supported-apps">
            <!-- GPay -->
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyVO9LUWF81Ov6LZR50eDNu5rNFCpkn0LwYQ&s" alt="Google Pay" title="Google Pay">
            <!-- PhonePe -->
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTo4x8kSTmPUq4PFzl4HNT0gObFuEhivHOFYg&s" alt="PhonePe" title="PhonePe">
            <!-- Paytm -->
            <img src="https://w7.pngwing.com/pngs/305/719/png-transparent-paytm-ecommerce-shopping-social-icons-circular-color-icon-thumbnail.png" alt="Paytm" title="Paytm">
            <!-- BHIM / UPI -->
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRSouM4icV33KEDtJakZiySZN3HH2LPfv3-BA&s" alt="BHIM UPI" title="BHIM UPI">
        </div>

        <!-- Render Intent Button immediately for mobile users -->
        <button class="mobile-intent-btn" onclick="openPaytmIntent('<?php echo htmlspecialchars($paytmintent, ENT_QUOTES); ?>','<?php echo htmlspecialchars($orders, ENT_QUOTES); ?>')">
            <i class="fas fa-bolt"></i> Pay directly via UPI App
        </button>

    </div>

    <!-- Status Screens -->
    <div class="success-screen" id="successScreen">
        <div class="status-icon status-success"><i class="fas fa-check-circle"></i></div>
        <h1 class="status-title">Payment Successful</h1>
        <p class="status-desc">Redirecting to dashboard...</p>
    </div>

    <div class="failure-screen" id="failureScreen">
        <div class="status-icon status-error"><i class="fas fa-times-circle"></i></div>
        <h1 class="status-title">Payment Failed</h1>
        <p class="status-desc failure-msg">Redirecting...</p>
    </div>
</div>

<script>
const SERVER = <?php echo json_encode($safe_js, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>;

document.addEventListener('DOMContentLoaded', () => {
    // Generate QR using QRious
    const qr = new QRious({
        value: SERVER.upi_link,
        size: 400,
        level: 'H'
    });
    document.getElementById('qr-img').src = qr.toDataURL();

    // Signal parent that we are ready (hides loading spinner)
    if (window.parent) {
        window.parent.postMessage({ type: 'payment_page_ready' }, '*');
    }
});

// Timer
function upiCountdown(elm, minute, second) {
    document.getElementById(elm).innerHTML = minute + ":" + (second < 10 ? "0" + second : second);
    function startTimer() {
        var presentTime = document.getElementById(elm).innerHTML;
        var timeArray = presentTime.split(/[:]+/);
        var m = parseInt(timeArray[0],10);
        var s = parseInt(timeArray[1],10) - 1;
        if (s < 0) { s = 59; m = m - 1; }
        if (m < 0) {
            Swal.fire({ title: 'Timeout', text: 'Payment session expired', icon: 'error' })
            .then(() => { window.location.href = SERVER.redirect_url; });
            return;
        }
        document.getElementById(elm).innerHTML = m + ":" + (s < 10 ? "0" + s : s);
        setTimeout(startTimer, 1000);
    }
    startTimer();
}
upiCountdown("timeout", 5, 0);

// Copy UPI
function copyUpiId() {
    const upi = SERVER.upi_id;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(upi).then(()=>{
            Swal.fire({icon: 'success', title: 'Copied', text:'UPI ID Copied to clipboard', toast:true, position:'top-end', showConfirmButton:false, timer:3000});
        });
    } else {
        const ta = document.createElement("textarea"); ta.value = upi; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove();
        Swal.fire({icon: 'success', title: 'Copied', text:'UPI ID Copied', toast:true, position:'top-end', showConfirmButton:false, timer:3000});
    }
}

function isIOS() { return /iPhone|iPad|iPod/i.test(navigator.userAgent); }
function isAndroid() { return /Android/i.test(navigator.userAgent); }
function isMobileUA() { return isIOS() || isAndroid(); }

function openPaytmIntent(paytmIntentUrl, fallbackUpi) {
    const upiLink = Math.random() > 0.5 ? SERVER.upi_link : fallbackUpi;
    if (isIOS()) {
        window.location.href = SERVER.upi_link;
        setTimeout(()=>{
            Swal.fire({ icon: 'info', title: 'Open UPI App', html: 'If app did not open, copy UPI ID to pay manually.', confirmButtonText: 'Copy ID' })
            .then((res)=>{ if(res.isConfirmed) copyUpiId(); });
        }, 1500);
        return;
    }
    if (isAndroid() && SERVER.paytmintent) {
        const start = Date.now();
        window.location.href = SERVER.paytmintent;
        setTimeout(()=>{ if (Date.now() - start < 1400) window.location.href = SERVER.upi_link; }, 1000);
        return;
    }
    if (!isMobileUA()) {
        Swal.fire({ icon: 'info', title: 'Desktop Browser Detected', text: 'Please scan the QR code to pay.', confirmButtonText: 'Got It' });
    } else {
        window.location.href = SERVER.upi_link;
    }
}

// Polling
var checkinterval;
function check() {
    $.ajax({
        type: 'post',
        url: '<?php echo url('pay/paytm/status'); ?>',
        data: { order_id: SERVER.order_id },
        success: function (data) {
            data = (data || '').trim();
            if (data === 'success') {
                clearInterval(checkinterval);
                document.getElementById('successScreen').style.display = 'flex';
                setTimeout(()=> window.location.href = SERVER.redirect_url, 2500);
            } else if (data === 'FAILURE' || data === 'FAILED') {
                clearInterval(checkinterval);
                document.getElementById('failureScreen').style.display = 'flex';
                if(data==='FAILED') document.querySelector('.failure-msg').innerHTML = 'Transaction failed. Amount mismatch.';
                setTimeout(()=> window.location.href = SERVER.redirect_url, 3500);
            } else if (data !== 'PENDING' && data !== '') {
                clearInterval(checkinterval);
                document.getElementById('failureScreen').style.display = 'flex';
                document.querySelector('.failure-msg').innerHTML = data;
                setTimeout(()=> window.location.href = SERVER.redirect_url, 3500);
            }
        }
    });
}
checkinterval = setInterval(check, 2000);
</script>
</body>
</html>
