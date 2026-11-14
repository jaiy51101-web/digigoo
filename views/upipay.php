<!-- Premium Custom CSS for Direct UPI Modal -->
<style>
#upiDirectModal .modal-content {
    border-radius: 28px !important;
    background: #ffffff;
    box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.15) !important;
    border: none !important;
    font-family: 'Outfit', 'Inter', system-ui, -apple-system, sans-serif;
    overflow: hidden;
}

#upiDirectModal .modal-header {
    background: linear-gradient(135deg, #094ebb 0%, #1e6cf4 100%) !important;
    padding: 16px 20px !important;
    border-bottom: none !important;
    border-top-left-radius: 28px !important;
    border-top-right-radius: 28px !important;
}

#upiDirectModal .logo-svg-container {
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.15);
    padding: 6px;
    border-radius: 10px;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.1);
}

.upi-amount-title {
    font-size: 0.85rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

.upi-amount-value {
    color: #e02f2f;
    font-weight: 800;
    font-size: 2.3rem;
    letter-spacing: -1px;
    margin: 2px 0 10px 0;
}

.upi-qr-card {
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    border: 1px solid #f1f5f9;
    padding: 16px;
    display: inline-block;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.upi-qr-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 40px rgba(0, 0, 0, 0.08);
}

.upi-app-box {
    background: #ffffff;
    border: 1.5px solid #f1f5f9;
    border-radius: 12px;
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.upi-app-box:hover {
    transform: translateY(-2px) scale(1.05);
    border-color: #cbd5e1;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.06);
}

.upi-app-box img {
    width: 36px;
    height: 36px;
    object-fit: contain;
}

.upi-app-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: #475569;
    margin-top: 6px;
    transition: color 0.2s ease;
}

.upi-app-item:hover .upi-app-label {
    color: #0f3ba5;
}

/* Glossy CTA Button */
.btn-verify-cta {
    background: linear-gradient(180deg, #1e6cf4 0%, #004ecc 100%);
    border: none;
    border-top: 1px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 6px 20px rgba(0, 78, 204, 0.25), inset 0 2px 4px rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    color: #ffffff;
    font-weight: 700;
    font-size: 0.95rem;
    padding: 12px 20px;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}

.btn-verify-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(0, 78, 204, 0.35), inset 0 2px 4px rgba(255, 255, 255, 0.3);
    color: #ffffff;
}

.btn-verify-cta:active {
    transform: translateY(1px);
    box-shadow: 0 4px 10px rgba(0, 78, 204, 0.2);
}

.btn-verify-cta .btn-cta-icon-wrapper {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.25s ease;
    flex-shrink: 0;
}

.btn-verify-cta .btn-cta-icon-wrapper i {
    font-size: 1.15rem;
    color: #ffffff;
}

.btn-verify-cta .btn-cta-text {
    transition: all 0.25s ease;
}

.btn-verify-cta .arrow-circle {
    background: #ffffff;
    border-radius: 50%;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #004ecc;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: all 0.25s ease;
    flex-shrink: 0;
}

.btn-verify-cta .arrow-circle i {
    font-size: 1.4rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-verify-cta:hover .arrow-circle {
    transform: translateX(3px);
}

/* Step 2 Premium Styles */
.upi-summary-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 10px 14px;
    margin-bottom: 12px;
}

.upi-input-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 14px 18px;
    margin-bottom: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
    transition: border-color 0.2s ease;
}

.upi-input-card:focus-within {
    border-color: #004ecc;
}

.step-number {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #e0e7ff;
    color: #004ecc;
    display: grid;
    place-items: center;
    font-size: 0.75rem;
    font-weight: 700;
}

.drag-drop-zone {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 14px 12px;
    text-align: center;
    cursor: pointer;
    background: #f8fafc;
    transition: all 0.2s ease;
}

.drag-drop-zone:hover {
    border-color: #004ecc;
    background: #f1f5f9;
}

.drag-drop-zone i {
    font-size: 1.5rem !important;
}

.trust-badge-container {
    font-size: 0.72rem;
    font-weight: 600;
    color: #64748b;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}

/* Animations */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

#upiDirectModal .modal-dialog {
    max-width: 440px;
    width: 100%;
    margin: 1.75rem auto;
}

/* Auto-fit and responsive scaling for all screens */
@media (max-width: 576px) {
    #upiDirectModal .modal-dialog {
        margin: 8px auto !important;
        max-width: calc(100% - 16px) !important;
        width: calc(100% - 16px) !important;
    }
    #upiDirectModal .modal-content {
        border-radius: 20px !important;
    }
    #upiDirectModal .modal-header {
        border-top-left-radius: 20px !important;
        border-top-right-radius: 20px !important;
        padding: 10px 14px !important;
    }
    #upiDirectModal .modal-header h5 {
        font-size: 1.15rem !important;
    }
    #upiDirectModal .modal-body {
        padding: 12px 14px 14px 14px !important;
    }
    .upi-amount-title {
        font-size: 0.75rem !important;
    }
    .upi-amount-value {
        font-size: 1.65rem !important;
        margin: 0px 0 6px 0 !important;
    }
    .upi-qr-card {
        padding: 10px !important;
        border-radius: 14px !important;
    }
    #upiQrCodeImg {
        width: 140px !important;
        height: 140px !important;
    }
    .upi-qr-card .position-absolute {
        width: 32px !important;
        height: 32px !important;
    }
    .upi-qr-card .position-absolute img {
        width: 20px !important;
        height: 20px !important;
    }
    .upi-app-box {
        width: 40px !important;
        height: 40px !important;
        border-radius: 8px !important;
    }
    .upi-app-box img {
        width: 24px !important;
        height: 24px !important;
    }
    .upi-app-label {
        font-size: 0.65rem !important;
        margin-top: 4px !important;
    }
    .btn-verify-cta {
        padding: 10px 16px !important;
        font-size: 0.88rem !important;
    }
    .btn-verify-cta .btn-cta-icon-wrapper {
        width: 26px !important;
        height: 26px !important;
    }
    .btn-verify-cta .btn-cta-icon-wrapper i {
        font-size: 0.95rem !important;
    }
    .btn-verify-cta .arrow-circle {
        width: 22px !important;
        height: 22px !important;
    }
    .btn-verify-cta .arrow-circle i {
        font-size: 1.15rem !important;
    }
    .trust-badge-container {
        font-size: 0.62rem !important;
        padding-top: 8px !important;
        gap: 4px !important;
    }
    .trust-badge-container svg {
        width: 28px !important;
        height: 10px !important;
    }
    .upi-summary-card {
        padding: 8px 12px !important;
        border-radius: 12px !important;
    }
    .upi-input-card {
        padding: 10px 14px !important;
        border-radius: 14px !important;
    }
    .drag-drop-zone {
        padding: 10px 8px !important;
    }
    .drag-drop-zone .fw-bold {
        font-size: 0.72rem !important;
    }
    .drag-drop-zone .text-muted {
        font-size: 0.65rem !important;
    }
}

/* Medium phones scale down */
@media (max-width: 400px) {
    #upiDirectModal .modal-body {
        padding: 10px 12px 12px 12px !important;
    }
    .upi-amount-value {
        font-size: 1.45rem !important;
        margin: 0 0 4px 0 !important;
    }
    #upiQrCodeImg {
        width: 125px !important;
        height: 125px !important;
    }
    .upi-app-box {
        width: 36px !important;
        height: 36px !important;
    }
    .upi-app-box img {
        width: 22px !important;
        height: 22px !important;
    }
    .upi-app-label {
        font-size: 0.6rem !important;
    }
    .trust-badge-container {
        font-size: 0.6rem !important;
        gap: 2px !important;
    }
    .btn-verify-cta {
        padding: 8px 12px !important;
        font-size: 0.82rem !important;
    }
    .btn-verify-cta .btn-cta-icon-wrapper {
        width: 22px !important;
        height: 22px !important;
    }
    .btn-verify-cta .btn-cta-icon-wrapper i {
        font-size: 0.85rem !important;
    }
    .btn-verify-cta .arrow-circle {
        width: 18px !important;
        height: 18px !important;
    }
    .btn-verify-cta .arrow-circle i {
        font-size: 1rem !important;
    }
}

/* Ultra compact phones (like iPhone SE 1st gen) */
@media (max-width: 330px) {
    #upiDirectModal .modal-body {
        padding: 8px 10px 10px 10px !important;
    }
    .upi-amount-value {
        font-size: 1.3rem !important;
    }
    #upiQrCodeImg {
        width: 110px !important;
        height: 110px !important;
    }
    .upi-app-box {
        width: 32px !important;
        height: 32px !important;
        border-radius: 6px !important;
    }
    .upi-app-box img {
        width: 18px !important;
        height: 18px !important;
    }
    .upi-app-label {
        font-size: 0.55rem !important;
    }
    .trust-badge-container {
        font-size: 0.55rem !important;
    }
}

/* Height-based media query for shorter screens (like iPhone SE/rotated phones) */
@media (max-height: 680px) {
    #upiDirectModal .modal-dialog {
        margin: 5px auto !important;
    }
    .upi-amount-value {
        font-size: 1.4rem !important;
        margin: 0 !important;
    }
    #upiQrCodeImg {
        width: 120px !important;
        height: 120px !important;
    }
    .upi-qr-card {
        padding: 8px !important;
    }
    .upi-app-box {
        width: 36px !important;
        height: 36px !important;
    }
    .upi-app-box img {
        width: 22px !important;
        height: 22px !important;
    }
    .upi-app-label {
        font-size: 0.65rem !important;
    }
    #upiDirectModal .modal-body {
        padding: 8px 12px 12px 12px !important;
    }
    .mb-3, .my-3 {
        margin-bottom: 8px !important;
        margin-top: 8px !important;
    }
}

/* Landscape / Ultra short screens */
@media (max-height: 560px) {
    #upiDirectModal .modal-dialog {
        margin: 2px auto !important;
    }
    .upi-amount-value {
        font-size: 1.2rem !important;
        margin: 0 !important;
    }
    #upiQrCodeImg {
        width: 95px !important;
        height: 95px !important;
    }
    .upi-qr-card {
        padding: 6px !important;
    }
    .upi-app-box {
        width: 30px !important;
        height: 30px !important;
    }
    .upi-app-box img {
        width: 18px !important;
        height: 18px !important;
    }
    .upi-app-label {
        font-size: 0.58rem !important;
    }
    .trust-badge-container {
        font-size: 0.55rem !important;
        padding-top: 4px !important;
    }
    .mb-3, .my-3 {
        margin-bottom: 4px !important;
        margin-top: 4px !important;
    }
}
</style>

<!-- Direct UPI Modal -->
<div class="modal fade" id="upiDirectModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="upiDirectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <!-- Modal Header with Professional Blue Gradient -->
            <div class="modal-header border-0 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="logo-svg-container">
                        <img src="<?= uploadUrl('site/brand_logo.webp') ?>" alt="DiGiWS" style="width: 28px; height: 28px; object-fit: contain;">
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold m-0 text-white" style="font-size: 1.4rem; letter-spacing: -0.5px; font-family: 'Outfit', 'Inter', sans-serif;">DiGiWS</h5>
                        <p class="m-0 text-white-50 small mt-0.5" style="font-size:0.75rem; font-weight: 500; opacity: 0.9;">Secured by UPI Auto-Verify</p>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div style="width: 1px; height: 28px; background: rgba(255,255,255,0.3); margin-right: 15px;"></div>
                    <button type="button" class="btn-close btn-close-white shadow-none opacity-100" data-bs-dismiss="modal" aria-label="Close" onclick="clearInterval(upiTimerInterval);" style="font-size: 1.15rem;"></button>
                </div>
            </div>
            
            <div class="modal-body" style="padding: 16px 24px 20px 24px;">
                <!-- Stepper Progress Bar (only visible in Step 2) -->
                <div class="mb-3" id="upiStepperContainer" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-2" style="font-size: 0.8rem; font-weight: 600; color: #64748b;">
                        <span id="upiStepLabel">Verify Your Payment</span>
                        <span id="upiStepIndicator">Step 2 of 2</span>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 10px; background-color: #f1f5f9;">
                        <div class="progress-bar" id="upiProgressBar" role="progressbar" style="width: 100%; background: linear-gradient(90deg, #1e6cf4 0%, #004ecc 100%); border-radius: 10px; transition: width 0.4s ease;"></div>
                    </div>
                </div>

                <!-- Hidden Order Ref field for dynamic script updating -->
                <span id="upiModalOrderNum" class="d-none"></span>

                <!-- STEP 1: Complete Your Payment (Scan & Pay) -->
                <div id="upiStep1Content">
                    <!-- Amount and Reference Header -->
                    <div class="text-center mt-2">
                        <div class="upi-amount-title">Total Amount</div>
                        <h2 class="upi-amount-value" id="upiModalAmount">₹0.00</h2>
                    </div>

                    <!-- QR Code Card -->
                    <div class="text-center mb-3">
                        <div class="upi-qr-card">
                            <div class="position-relative d-inline-block">
                                <img id="upiQrCodeImg" src="" alt="UPI QR Code" style="width: 200px; height: 200px; object-fit: contain;">
                            </div>
                        </div>
                        <!-- UPI ID Display below QR Code -->
                        <div class="mt-2 text-center">
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-monospace" style="font-size: 0.82rem; letter-spacing: 0.5px; border-color: #cbd5e1 !important; color: #334155 !important;">
                                <i class="bi bi-wallet2 me-1.5 text-primary"></i> <span id="upiDisplayAddress"><?= htmlspecialchars($settingModel->get('upi_id', '8210092602@okbizaxis')) ?></span>
                            </span>
                        </div>
                    </div>

                    <!-- Timer Countdown Section -->
                    <div class="d-flex justify-content-center align-items-center gap-2 mb-3">
                        <!-- Red clock icon -->
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="#dc3545" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 6V12L16 14" stroke="#dc3545" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="fw-semibold text-muted" style="font-size: 0.95rem; color: #475569 !important;">Session Expires in</span>
                        <span class="badge bg-danger text-white px-2.5 py-1.5 rounded-2 fw-bold" id="upiTimer" style="font-size: 0.95rem; background-color: #dc3545 !important;">05:00</span>
                    </div>

                    <!-- Separator Line & Heading -->
                    <div class="d-flex align-items-center my-3">
                        <div class="flex-grow-1" style="height: 1px; background-color: #e2e8f0;"></div>
                        <span class="px-3 fw-bold" style="font-size: 0.72rem; letter-spacing: 1px; color: #0f3ba5 !important; font-family: 'Outfit', 'Inter', sans-serif;">SCAN & PAY USING ANY UPI APP</span>
                        <div class="flex-grow-1" style="height: 1px; background-color: #e2e8f0;"></div>
                    </div>

                    <!-- Supported Apps row with vertical separators -->
                    <div class="row g-0 text-center justify-content-center mb-3">
                        <div class="col-3 upi-app-item" style="border-right: 1px solid #e2e8f0;">
                            <div class="upi-app-box mx-auto">
                                <img src="<?= uploadUrl('site/gpay.jpg') ?>" alt="GPay">
                            </div>
                            <div class="upi-app-label">GPay</div>
                        </div>
                        <div class="col-3 upi-app-item" style="border-right: 1px solid #e2e8f0;">
                            <div class="upi-app-box mx-auto">
                                <img src="<?= uploadUrl('site/phonepe.png') ?>" alt="PhonePe">
                            </div>
                            <div class="upi-app-label">PhonePe</div>
                        </div>
                        <div class="col-3 upi-app-item" style="border-right: 1px solid #e2e8f0;">
                            <div class="upi-app-box mx-auto">
                                <img src="<?= uploadUrl('site/paytm.png') ?>" alt="Paytm">
                            </div>
                            <div class="upi-app-label">Paytm</div>
                        </div>
                        <div class="col-3 upi-app-item">
                            <div class="upi-app-box mx-auto">
                                <img src="<?= uploadUrl('site/bhim.png') ?>" alt="BHIM">
                            </div>
                            <div class="upi-app-label">BHIM</div>
                        </div>
                    </div>

                    <!-- Submit & Verify Payment CTA Button with circular badge icon -->
                    <button type="button" class="btn-verify-cta btn-glossy-blue shadow-sm mt-3" id="btnNextStep" onclick="goToStep(2)">
                        <div class="d-flex align-items-center justify-content-center rounded-circle btn-cta-icon-wrapper" style="background: rgba(255,255,255,0.18);">
                            <i class="bi bi-shield-fill-check"></i>
                        </div>
                        <span class="mx-auto btn-cta-text">Submit & Verify Payment</span>
                        <div class="arrow-circle">
                            <i class="bi bi-arrow-right-short"></i>
                        </div>
                    </button>
                </div>

                <!-- STEP 2: Verify Your Payment (UTR & Screenshot Upload) -->
                <div id="upiStep2Content" style="display: none;">
                    <form id="upiVerifyForm" enctype="multipart/form-data">
                        <input type="hidden" name="order_id" id="upiModalOrderIdInput">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        
                        <!-- Mini summary card -->
                        <div class="d-flex justify-content-between align-items-center upi-summary-card">
                            <div>
                                <span class="text-muted" style="font-size: 0.75rem; font-weight:600;">Order Number:</span>
                                <div class="fw-bold text-dark" id="upiSummaryOrderNum">DWXXXXXX</div>
                            </div>
                            <div class="text-end">
                                <span class="text-muted" style="font-size: 0.75rem; font-weight:600;">Total Amount:</span>
                                <div class="fw-bold text-primary" id="upiSummaryAmount" style="font-weight:700;">₹0.00</div>
                            </div>
                        </div>

                        <!-- Info hint: only one required -->
                        <div class="d-flex align-items-start gap-2 mb-2 px-1" style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:8px 12px;">
                            <i class="bi bi-info-circle-fill text-primary" style="font-size:0.9rem;flex-shrink:0;margin-top:2px;"></i>
                            <span style="font-size:0.78rem;color:#1e40af;font-weight:600;line-height:1.4;">Provide <u>at least one</u>: UTR number <em>or</em> screenshot. Your order stays in <strong>Processing</strong> until verified by admin.</span>
                        </div>

                        <!-- UTR Number input -->
                        <div class="upi-input-card">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="step-number" style="background:#dbeafe;color:#1d4ed8;font-size:0.65rem;font-weight:800;">OPT<br>1</div>
                                <label class="dc-form-label fw-bold m-0 text-dark" style="font-size: 0.88rem;">Enter UTR / Transaction ID <span class="text-muted fw-normal" style="font-size:0.75rem;">(optional)</span></label>
                            </div>
                            <input type="text" name="utr_number" id="upiUtrInput" class="form-control shadow-none" placeholder="12-digit transaction ID (e.g. 6182...)" style="border-radius: 12px; border-color: #cbd5e1; padding: 10px 14px; font-size:0.88rem; font-family:'Outfit','Inter',sans-serif;">
                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">Usually visible in transaction details inside your payment app.</small>
                        </div>

                        <!-- OR Divider -->
                        <div class="d-flex align-items-center gap-2 my-1">
                            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
                            <span class="badge rounded-pill px-3 py-1 fw-bold" style="background:#f1f5f9;color:#64748b;font-size:0.78rem;border:1px solid #e2e8f0;">OR</span>
                            <div style="flex:1;height:1px;background:#e2e8f0;"></div>
                        </div>

                        <!-- Screenshot upload -->
                        <div class="upi-input-card">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="step-number" style="background:#dcfce7;color:#15803d;font-size:0.65rem;font-weight:800;">OPT<br>2</div>
                                <label class="dc-form-label fw-bold m-0 text-dark" style="font-size: 0.88rem;">Upload Payment Screenshot <span class="text-muted fw-normal" style="font-size:0.75rem;">(optional)</span></label>
                            </div>
                            
                            <!-- Drag and drop zone -->
                            <div id="upiDropZone" class="drag-drop-zone">
                                <i class="bi bi-cloud-arrow-up text-muted" style="font-size: 1.5rem; color: #1e6cf4 !important;"></i>
                                <div class="fw-bold text-dark mt-1" style="font-size: 0.8rem;">Click to browse or drag screenshot here</div>
                                <div class="text-muted mt-0.5" style="font-size: 0.7rem;">PNG or JPG formats supported</div>
                                <input type="file" name="screenshot" id="upiScreenshotInput" class="d-none" accept="image/jpeg,image/png">
                                <div id="fileSelectedIndicator" class="mt-2 text-success fw-bold d-none" style="font-size:0.75rem;">
                                    <i class="bi bi-file-earmark-check-fill me-1"></i> <span id="selectedFileName">screenshot.jpg</span>
                                </div>
                            </div>
                        </div>

                        <!-- Stepper Navigation -->
                        <div class="d-flex gap-3 mt-3">
                            <button type="button" class="btn btn-outline-secondary py-2.5 px-3 d-flex align-items-center justify-content-center" style="border-radius: 14px; border-color: #cbd5e1; color: #64748b;" onclick="goToStep(1)" title="Go back to QR code">
                                <i class="bi bi-arrow-left" style="font-size: 1.1rem;"></i>
                            </button>
                            <button type="submit" class="btn flex-fill py-2.5 text-white fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" id="upiVerifyBtn" style="background: linear-gradient(180deg, #1e6cf4 0%, #004ecc 100%); border: 0; border-radius: 14px; font-size: 0.95rem; border-top: 1px solid rgba(255,255,255,0.25);">
                                <i class="bi bi-shield-check" style="font-size: 1.1rem;"></i>Verify Payment
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Footer Trust Badges with vertical separators and minimal icons -->
                <div class="trust-badge-container d-flex justify-content-between align-items-center mt-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-shield-check text-primary" style="font-size: 1.1rem; color: #0d6efd !important;"></i>
                        <span>100% Secure</span>
                    </div>
                    <div style="width: 1px; height: 16px; background-color: #e2e8f0;"></div>
                    <div class="d-flex align-items-center gap-2">
                        <!-- UPI Auto-Verify badge with custom clean SVG -->
                        <svg class="me-1" width="36" height="12" viewBox="0 0 45 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;">
                            <path d="M1.3 14h2.4l1.8-8H3.1l-1.8 8zm7.1-8v4.4c0 1.1-.9 2-2 2H4l1.8-8.4h2.4L7.1 9.8h.3l.5-3.8h2.2zm4.5 4.1c0 1.5-1.2 2.7-2.8 2.7H8.3l1.8-8.4h1.5c.8 0 1.5.5 1.5 1.5 0 .8-.3 1.4-.8 1.7l1.1 2.5zm-2.2-3.1c.4 0 .7-.2.7-.6 0-.4-.3-.6-.7-.6h-.8l-.3 1.2h.8z" fill="#0056b3"/>
                            <path d="M14.6 2h3.2l-2.6 12h-3.2L14.6 2z" fill="#f15a24"/>
                            <path d="M16.4 2h3.2l-2.6 12h-3.2L16.4 2z" fill="#097939"/>
                        </svg>
                        <span>UPI Auto-Verify</span>
                    </div>
                    <div style="width: 1px; height: 16px; background-color: #e2e8f0;"></div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-lightning-charge text-dark" style="font-size: 1.1rem; color: #334155 !important;"></i>
                        <span>Instant Confirmation</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
