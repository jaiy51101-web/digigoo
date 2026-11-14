</main>

<!-- Footer -->
<footer class="dc-footer">
    <div class="dc-footer-top">
        <div class="container">
            <div class="row g-5">
                
                <!-- Column 1: Brand & About -->
                <div class="col-lg-4 col-md-6">
                    <a href="<?= url() ?>" style="text-decoration:none; display:inline-flex; align-items:center; gap:10px; margin-bottom: 20px;">
                        <?php if (!empty($siteLogo)): ?>
                            <img src="<?= uploadUrl($siteLogo) ?>" alt="<?= htmlspecialchars($siteName ?? 'Digiws') ?>" style="height:36px; max-width:180px; object-fit:contain; flex-shrink:0;">
                        <?php else: ?>
                            <!-- Matching SVG Logo -->
                            <svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0;">
                                <defs>
                                    <linearGradient id="footerLogoGrad" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#3B82F6" />
                                        <stop offset="100%" stop-color="#8B5CF6" />
                                    </linearGradient>
                                </defs>
                                <rect x="4" y="4" width="16" height="16" rx="5" fill="url(#footerLogoGrad)" opacity="0.8" />
                                <rect x="12" y="12" width="16" height="16" rx="5" fill="url(#footerLogoGrad)" />
                                <path d="M16 10L20 14L16 18L12 14L16 10Z" fill="#FFFFFF" />
                            </svg>
                            <div class="footer-brand" style="margin-bottom:0; font-size:1.65rem;"><?= htmlspecialchars($siteName ?? 'Digiws') ?></div>
                        <?php endif; ?>
                    </a>
                    <p>Your ultimate destination for premium digital products, source codes, UI kits, and professional web development services. Quality guaranteed.</p>
                    
                    <div class="social-links">
                        <a href="https://www.facebook.com/Techiws" target="_blank" rel="noopener noreferrer" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="https://x.com/techiws" target="_blank" rel="noopener noreferrer" title="Twitter"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://www.instagram.com/techiws" target="_blank" rel="noopener noreferrer" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="https://www.youtube.com/@Techws" target="_blank" rel="noopener noreferrer" title="YouTube"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                
                <!-- Column 2: Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="<?= url() ?>">Home page</a></li>
                        <li><a href="<?= url('products') ?>">Browse Products</a></li>
                        <li><a href="<?= url('wishlist') ?>">My Wishlist</a></li>
                        <li><a href="<?= url('cart') ?>">Shopping Cart</a></li>
                        <li><a href="<?= url('login') ?>">Sign In / Register</a></li>
                        <li><a href="<?= url('sitemap') ?>">Sitemap</a></li>
                    </ul>
                </div>
                
                <!-- Column 3: All Pages / Legal -->
                <div class="col-lg-2 col-md-6">
                    <h5>Important Pages</h5>
                    <ul class="footer-links">
                        <li><a href="<?= url('about') ?>">About Us</a></li>
                        <li><a href="<?= url('contact') ?>">Contact Us</a></li>
                        <li><a href="<?= url('blog') ?>">Blog & Articles</a></li>
                        <li><a href="<?= url('page/privacy-policy') ?>">Privacy Policy</a></li>
                        <li><a href="<?= url('terms-and-conditions') ?>">Terms & Conditions</a></li>
                        <li><a href="<?= url('refund-policy') ?>">Refund Policy</a></li>
                        <li><a href="<?= url('disclaimer') ?>">Disclaimer</a></li>
                        <li><a href="<?= url('page/faq') ?>">Help & FAQ</a></li>
                    </ul>
                </div>
                
                <!-- Column 4: Newsletter -->
                <div class="col-lg-4 col-md-6">
                    <h5>Stay Connected</h5>
                    <p>Subscribe to our newsletter to receive the latest updates, special offers, and free downloads direct to your inbox.</p>
                    <form class="newsletter-form mt-3" id="newsletterForm">
                        <div class="dc-newsletter-group">
                            <input type="email" name="email" placeholder="Email address..." required>
                            <button type="submit">Subscribe</button>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
    
    <div class="dc-footer-bottom">
        <div class="container d-flex align-items-center justify-content-between flex-wrap gap-3">
            <p>&copy; <?= date('Y') ?> <strong><?= htmlspecialchars($siteName ?? 'Digiws') ?></strong>. All rights reserved.</p>
            <p style="color:#64748b;">Powered & Inspired by <span style="color:#cbd5e1; font-weight:600;">Digiws Framework</span></p>
        </div>
    </div>
</footer>

<!-- Floating Action Buttons -->
<div class="dc-fab-group">
    <!-- Scroll to Top -->
    <button id="scrollToTop" class="dc-fab dc-fab-scroll" title="Scroll to Top">
        <i class="bi bi-arrow-up"></i>
    </button>

    <!-- WhatsApp Support -->
    <?php 
    $num = $whatsappNumber ?? (defined('WHATSAPP_NUMBER') ? WHATSAPP_NUMBER : '919534702513');
    $msg = $whatsappMsg ?? (defined('WHATSAPP_MESSAGE') ? urlencode(WHATSAPP_MESSAGE) : 'Hello!');
    if (!empty($num)): 
    ?>
    <a href="https://wa.me/<?= $num ?>?text=<?= $msg ?>" target="_blank" class="dc-fab dc-fab-whatsapp" title="Chat on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>
    <?php endif; ?>
</div>

<script>
// Scroll to Top Logic
const scrollBtn = document.getElementById('scrollToTop');
window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
        scrollBtn.classList.add('show');
    } else {
        scrollBtn.classList.remove('show');
    }
});

scrollBtn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
<!-- Main JS -->
<?php $jsTime = file_exists(BASE_PATH . '/assets/js/app.js') ? filemtime(BASE_PATH . '/assets/js/app.js') : time(); ?>
<script src="<?= asset('js/app.js?v=' . $jsTime) ?>" defer></script>

<script>
// Auto-hide flash messages
setTimeout(() => {
    document.querySelectorAll('.dc-toast').forEach(t => {
        t.style.transition = 'opacity 0.5s';
        t.style.opacity = '0';
        setTimeout(() => t.remove(), 500);
    });
}, 4000);
</script>

<!-- Mobile Bottom Navigation (App-like) -->
<nav class="dc-bottom-nav d-lg-none">
    <a href="<?= url() ?>" class="nav-item <?= ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php' || $_SERVER['REQUEST_URI'] === '/digiws' || $_SERVER['REQUEST_URI'] === '/digiws/') ? 'active' : '' ?>">
        <i class="bi bi-house-door"></i>
        <span>Home</span>
    </a>
    <a href="<?= url('products') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/products') ? 'active' : '' ?>">
        <i class="bi bi-grid-fill"></i>
        <span>Products</span>
    </a>
    <a href="<?= url('cart') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/cart') ? 'active' : '' ?>">
        <div class="position-relative">
            <i class="bi bi-cart3"></i>
            <?php if (cartCount() > 0): ?>
            <span class="badge rounded-pill bg-danger position-absolute" style="top:-5px; right:-10px; font-size:0.6rem; padding: 2px 5px;"><?= cartCount() ?></span>
            <?php endif; ?>
        </div>
        <span>Cart</span>
    </a>
    <a href="<?= url('wishlist') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/wishlist') ? 'active' : '' ?>">
        <i class="bi bi-heart"></i>
        <span>Wishlist</span>
    </a>
    <a href="<?= isLoggedIn() ? url('user/dashboard') : url('login') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'], '/user') || str_contains($_SERVER['REQUEST_URI'], '/login') ? 'active' : '' ?>">
        <i class="bi bi-person"></i>
        <span>Account</span>
    </a>
</nav>

<?php if (!isLoggedIn()): ?>
<!-- Newsletter Popup Modal -->
<div id="newsletterModal" class="newsletter-modal-overlay" style="display: none;">
    <div class="newsletter-modal-content">
        <button id="closeNewsletterModal" class="newsletter-modal-close" aria-label="Close modal">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
        <div class="newsletter-modal-body">
            <div class="newsletter-modal-inner">
                <!-- Form View -->
                <div id="newsletterModalFormContainer">
                    <div class="limited-offer-badge">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;"><polyline points="20 12 20 22 4 22 4 12"></polyline><rect x="2" y="7" width="20" height="5"></rect><line x1="12" y1="22" x2="12" y2="7"></line><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path></svg>
                        <span>EXCLUSIVE UPDATE</span>
                    </div>
                    <h2 class="newsletter-modal-title">Join our <span class="highlight-blue">Newsletter</span></h2>
                    <p class="newsletter-modal-desc">Stay ahead of the curve. Get exclusive digital products, source codes, and development guides delivered straight to your inbox.</p>
                    
                    <form id="newsletterModalForm" class="newsletter-modal-form">
                        <div class="newsletter-input-wrapper">
                            <span class="newsletter-input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            </span>
                            <input type="email" id="newsletterModalEmail" name="email" placeholder="Enter your email address" required>
                        </div>
                        <button type="submit" class="newsletter-modal-btn">
                            <span>Subscribe Now</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </button>
                    </form>
                    <div id="newsletterModalMessage" class="newsletter-modal-message"></div>
                    
                    <div style="text-align: center;">
                        <a href="#" id="noThanksNewsletterModal" class="newsletter-no-thanks">No Thanks</a>
                    </div>
                    
                    <div class="newsletter-privacy-text">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <span>We value your privacy. Unsubscribe anytime.</span>
                    </div>

                    <!-- Trust footer -->
                    <div class="newsletter-trust-footer">
                        <div class="avatar-group">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=60&fit=crop&q=80" alt="User">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=60&fit=crop&q=80" alt="User">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=60&fit=crop&q=80" alt="User">
                        </div>
                        <div class="trust-text-stars">
                            <div class="stars">★★★★★</div>
                            <span class="trust-desc">Trusted by 15,000+ developers</span>
                        </div>
                    </div>
                </div>

                <!-- Success View -->
                <div id="newsletterModalSuccessContainer" style="display: none;">
                    <div class="success-checkmark-wrapper">
                        <svg class="success-checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                            <circle class="success-checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                            <path class="success-checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                        </svg>
                    </div>
                    <h2 class="newsletter-success-title">You're Subscribed!</h2>
                    <p class="newsletter-success-desc">Thank you for joining. Welcome to Digiws newsletter! Check your inbox for updates.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.newsletter-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(8, 10, 24, 0.4);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    font-family: 'Inter', -apple-system, sans-serif;
    padding: 20px;
}
.newsletter-modal-overlay.show {
    opacity: 1;
}
.newsletter-modal-content {
    background: var(--bg-secondary);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    width: 100%;
    max-width: 480px; /* Reduced by ~11% for 10% target reduction */
    border-radius: 20px; /* 16-20px rounded corners */
    box-shadow: var(--shadow-xl);
    position: relative;
    overflow: hidden;
    transform: translateY(24px) scale(0.95);
    opacity: 0;
    transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid var(--border-color);
}
.newsletter-modal-overlay.show .newsletter-modal-content {
    transform: translateY(0) scale(1);
    opacity: 1;
}
.newsletter-modal-close {
    position: absolute;
    top: 16px;
    right: 16px;
    background: var(--bg-tertiary);
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    color: var(--text-secondary);
    cursor: pointer;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}
.newsletter-modal-close:hover {
    background: var(--bg-primary);
    color: var(--text-primary);
    transform: scale(1.05);
}
.newsletter-modal-body {
    padding: 36px 32px 32px 32px;
}
@media (max-width: 480px) {
    .newsletter-modal-body {
        padding: 32px 20px 24px 20px;
    }
}

.limited-offer-badge {
    background: rgba(99, 102, 241, 0.08);
    color: var(--primary);
    padding: 6px 12px;
    border-radius: 100px;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    margin: 0 auto 16px auto;
    border: 1px solid rgba(99, 102, 241, 0.15);
}
#newsletterModalFormContainer {
    display: flex;
    flex-direction: column;
    text-align: center;
}
.newsletter-modal-title {
    font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1.25;
    margin-bottom: 10px;
    letter-spacing: -0.5px;
}
.newsletter-modal-title .highlight-blue {
    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.newsletter-modal-desc {
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin-bottom: 24px;
    line-height: 1.5;
}
.newsletter-modal-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
    width: 100%;
}
.newsletter-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
}
.newsletter-input-icon {
    position: absolute;
    left: 14px;
    color: var(--text-tertiary);
    display: flex;
    align-items: center;
}
.newsletter-input-wrapper input {
    width: 100%;
    padding: 13px 14px 13px 42px;
    border: 1.5px solid var(--border-color);
    border-radius: 12px;
    font-size: 0.92rem;
    color: var(--text-primary);
    outline: none;
    background: var(--bg-tertiary);
    transition: all 0.3s ease;
}
.newsletter-input-wrapper input:focus {
    border-color: var(--primary);
    background: var(--bg-secondary);
    box-shadow: 0 0 0 4px var(--primary-soft);
}
.newsletter-modal-btn {
    background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
    color: #ffffff;
    border: none;
    padding: 13px 24px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.2);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.newsletter-modal-btn:hover {
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
    transform: translateY(-1.5px);
}
.newsletter-modal-btn:active {
    transform: translateY(0.5px);
}
.newsletter-no-thanks {
    font-size: 0.8rem;
    color: #9ca3af;
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    margin-top: 14px;
    transition: color 0.2s ease;
}
.newsletter-no-thanks:hover {
    color: #4b5563;
    text-decoration: underline;
}
.newsletter-modal-message {
    font-size: 0.82rem;
    margin-top: 10px;
    padding: 8px 12px;
    border-radius: 8px;
    display: none;
    text-align: left;
}
.newsletter-modal-message.error {
    color: #b91c1c;
    background-color: #fef2f2;
    border: 1px solid #fee2e2;
    display: block;
}
.newsletter-privacy-text {
    font-size: 0.74rem;
    color: #9ca3af;
    text-align: center;
    margin-top: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
}

/* Trust footer style */
.newsletter-trust-footer {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding-top: 16px;
    margin-top: 16px;
    border-top: 1px solid rgba(15, 23, 42, 0.06);
}
.newsletter-trust-footer .avatar-group img {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 1.5px solid #ffffff;
    object-fit: cover;
    margin-left: -6px;
}
.newsletter-trust-footer .avatar-group img:first-child {
    margin-left: 0;
}
.trust-text-stars {
    display: flex;
    flex-direction: column;
    text-align: left;
    line-height: 1.2;
}
.stars {
    color: #fbbf24;
    font-size: 10px;
    letter-spacing: 0.5px;
}
.trust-desc {
    font-size: 0.7rem;
    font-weight: 600;
    color: #4b5563;
}

/* Success View Styling */
#newsletterModalSuccessContainer {
    text-align: center;
    padding: 20px 0;
}
.newsletter-success-title {
    font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif;
    font-size: 1.6rem;
    font-weight: 800;
    margin-bottom: 8px;
    color: #10B981;
}
.newsletter-success-desc {
    font-size: 0.9rem;
    color: #4b5563;
    margin: 0;
    line-height: 1.5;
}

/* Custom SVG checkmark drawing animation */
.success-checkmark-wrapper {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px auto;
    display: flex;
    justify-content: center;
    align-items: center;
}
.success-checkmark {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: block;
    stroke-width: 2.5;
    stroke: #10b981;
    stroke-miterlimit: 10;
    box-shadow: inset 0px 0px 0px #10b981;
    animation: fill-checkmark .4s ease-in-out .4s forwards, scale-checkmark .3s ease-in-out .9s both;
}
.success-checkmark-circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2.5;
    stroke-miterlimit: 10;
    stroke: #10b981;
    fill: none;
    animation: stroke-circle 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}
.success-checkmark-check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    stroke-width: 3.5;
    stroke: #ffffff;
    animation: stroke-check 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}

@keyframes stroke-circle {
    100% {
        stroke-dashoffset: 0;
    }
}
@keyframes stroke-check {
    100% {
        stroke-dashoffset: 0;
    }
}
@keyframes fill-checkmark {
    100% {
        box-shadow: inset 0px 0px 0px 45px #10b981;
    }
}
@keyframes scale-checkmark {
    0%, 100% {
        transform: none;
    }
    50% {
        transform: scale3d(1.1, 1.1, 1);
    }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("newsletterModal");
    if (!modal) return;

    const closeBtn = document.getElementById("closeNewsletterModal");
    const noThanksBtn = document.getElementById("noThanksNewsletterModal");
    const form = document.getElementById("newsletterModalForm");
    const emailInput = document.getElementById("newsletterModalEmail");
    const msgDiv = document.getElementById("newsletterModalMessage");
    const formContainer = document.getElementById("newsletterModalFormContainer");
    const successContainer = document.getElementById("newsletterModalSuccessContainer");

    const isSubscribed = localStorage.getItem("newsletter_popup_subscribed") || document.cookie.includes("newsletter_subscribed=true");
    const isDismissed = sessionStorage.getItem("newsletter_popup_dismissed");
    const isShown = sessionStorage.getItem("newsletter_popup_shown");

    let popupTimer = null;
    let exitIntentTriggered = false;

    // Helper to display popup
    function triggerPopup() {
        // Double check state before triggering
        const alreadySub = localStorage.getItem("newsletter_popup_subscribed") || document.cookie.includes("newsletter_subscribed=true");
        const alreadyDismissed = sessionStorage.getItem("newsletter_popup_dismissed");
        const alreadyShown = sessionStorage.getItem("newsletter_popup_shown");

        if (alreadySub || alreadyDismissed || alreadyShown) {
            return;
        }

        // Set session variables so it won't show again on subsequent page loads in the same visit
        sessionStorage.setItem("newsletter_popup_shown", "true");

        if (popupTimer) {
            clearTimeout(popupTimer);
        }

        modal.style.display = "flex";
        // Trigger reflow to start transition
        modal.offsetHeight;
        modal.classList.add("show");
    }

    // Dismiss logic
    function dismissModal() {
        modal.classList.remove("show");
        sessionStorage.setItem("newsletter_popup_dismissed", "true");
        sessionStorage.setItem("newsletter_popup_shown", "true");
        setTimeout(() => {
            modal.style.display = "none";
        }, 400);
    }

    if (closeBtn) closeBtn.addEventListener("click", dismissModal);
    if (noThanksBtn) {
        noThanksBtn.addEventListener("click", function(e) {
            e.preventDefault();
            dismissModal();
        });
    }

    modal.addEventListener("click", function(e) {
        if (e.target === modal) {
            dismissModal();
        }
    });

    // Timing trigger: randomly between 5 and 8 seconds of the first page load
    if (!isSubscribed && !isDismissed && !isShown) {
        const randomDelay = Math.floor(Math.random() * (8000 - 5000 + 1)) + 5000;
        popupTimer = setTimeout(triggerPopup, randomDelay);

        // Exit intent trigger for desktop users (mouse leaves window top)
        document.addEventListener("mouseleave", function(e) {
            if (exitIntentTriggered) return;
            // Check if cursor is close to the top edge (typically < 50px)
            if (e.clientY < 50) {
                exitIntentTriggered = true;
                triggerPopup();
            }
        });
    }

    // Submit handler with client-side validation
    if (form) {
        form.addEventListener("submit", function(e) {
            e.preventDefault();
            const email = emailInput.value.trim();
            
            // Client side email regex validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                msgDiv.textContent = "Please enter a valid email address.";
                msgDiv.className = "newsletter-modal-message error";
                msgDiv.style.display = "block";
                return;
            }

            msgDiv.className = "newsletter-modal-message";
            msgDiv.style.display = "none";

            fetch("<?= url('newsletter/subscribe') ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: new URLSearchParams({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Set permanent flags (localStorage and 10 year cookie)
                    localStorage.setItem("newsletter_popup_subscribed", "true");
                    
                    const expiry = new Date();
                    expiry.setTime(expiry.getTime() + (3650 * 24 * 60 * 60 * 1000)); // 10 years
                    document.cookie = "newsletter_subscribed=true; expires=" + expiry.toUTCString() + "; path=/; SameSite=Lax";

                    // Hide form container and show success container
                    formContainer.style.display = "none";
                    successContainer.style.display = "block";

                    // Automatically fade out and hide modal after success animation completes
                    setTimeout(() => {
                        modal.classList.remove("show");
                        setTimeout(() => {
                            modal.style.display = "none";
                        }, 400);
                    }, 4000);
                } else {
                    msgDiv.textContent = data.message || "Failed to subscribe. Please try again.";
                    msgDiv.className = "newsletter-modal-message error";
                    msgDiv.style.display = "block";
                }
            })
            .catch(err => {
                msgDiv.textContent = "Subscription error. Please try again.";
                msgDiv.className = "newsletter-modal-message error";
                msgDiv.style.display = "block";
            });
        });
    }

    // Integrate footer email subscription form to set subscription cookie/storage flags as well
    const footerForm = document.getElementById("newsletterForm");
    if (footerForm) {
        let footerMsg = document.getElementById("footerNewsletterMessage");
        if (!footerMsg) {
            footerMsg = document.createElement("div");
            footerMsg.id = "footerNewsletterMessage";
            footerMsg.className = "mt-2 small";
            footerMsg.style.fontWeight = "500";
            footerForm.after(footerMsg);
        }
        
        footerForm.addEventListener("submit", function(e) {
            e.preventDefault();
            const footerEmailInput = footerForm.querySelector("input[name='email']");
            const email = footerEmailInput ? footerEmailInput.value.trim() : "";
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                footerMsg.style.color = "#f87171";
                footerMsg.textContent = "Please enter a valid email address.";
                footerMsg.style.display = "block";
                return;
            }
            
            footerMsg.style.display = "none";
            footerMsg.style.color = "#cbd5e1";
            footerMsg.textContent = "Subscribing...";
            footerMsg.style.display = "block";
            
            fetch("<?= url('newsletter/subscribe') ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: new URLSearchParams({ email: email, source: "footer" })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    footerMsg.style.color = "#4ade80";
                    footerMsg.textContent = data.message || "Subscribed successfully!";
                    if (footerEmailInput) footerEmailInput.value = "";
                    
                    // Mark as subscribed to prevent popup triggers
                    localStorage.setItem("newsletter_popup_subscribed", "true");
                    
                    const expiry = new Date();
                    expiry.setTime(expiry.getTime() + (3650 * 24 * 60 * 60 * 1000));
                    document.cookie = "newsletter_subscribed=true; expires=" + expiry.toUTCString() + "; path=/; SameSite=Lax";
                } else {
                    footerMsg.style.color = "#f87171";
                    footerMsg.textContent = data.message || "Failed to subscribe.";
                }
            })
            .catch(err => {
                footerMsg.style.color = "#f87171";
                footerMsg.textContent = "Error subscribing. Please try again.";
            });
        });
    }
});
</script>
<?php endif; ?>

<!-- Quick View Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true" style="backdrop-filter: blur(8px); z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: 1px solid rgba(0, 0, 0, 0.08); box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); background: #ffffff;">
            <div class="modal-header border-0 pb-0" style="padding: 20px 24px 0 24px;">
                <h5 class="modal-title" style="font-weight: 800; font-size: 1.2rem; color: #0f172a;">Product Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-size: 0.8em; opacity: 0.6; padding: 10px;"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <!-- Content will be injected dynamically via AJAX -->
                <div class="d-flex justify-content-center align-items-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.addEventListener("click", function(e) {
        const trigger = e.target.closest(".quickview-trigger-btn");
        if (trigger) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = trigger.getAttribute("data-product-id");
            if (!productId) return;
            
            const modalEl = document.getElementById("quickViewModal");
            const modalBody = modalEl.querySelector(".modal-body");
            
            modalBody.innerHTML = `
                <div class="d-flex justify-content-center align-items-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width: 2.5rem; height: 2.5rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            const modalInstance = new bootstrap.Modal(modalEl);
            modalInstance.show();
            
            fetch('<?= url("api/product/quickview") ?>/' + productId)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        modalBody.innerHTML = data.html;
                        
                        // Bind Cart Click action
                        const newCartBtn = modalBody.querySelector(".add-to-cart-btn");
                        if (newCartBtn) {
                            newCartBtn.addEventListener("click", function() {
                                const pid = this.getAttribute("data-product-id");
                                if (typeof addToCart === "function") {
                                    addToCart(pid);
                                } else {
                                    // Fallback form post
                                    const form = document.createElement("form");
                                    form.method = "POST";
                                    form.action = '<?= url("cart/add") ?>';
                                    const input = document.createElement("input");
                                    input.type = "hidden";
                                    input.name = "product_id";
                                    input.value = pid;
                                    form.appendChild(input);
                                    document.body.appendChild(form);
                                    form.submit();
                                }
                            });
                        }
                    } else {
                        modalBody.innerHTML = `<div class="alert alert-danger mb-0">${data.message || 'Error loading product preview.'}</div>`;
                    }
                })
                .catch(err => {
                    console.error("Quickview fetch error:", err);
                    modalBody.innerHTML = `<div class="alert alert-danger mb-0">Failed to fetch product preview details. Please try again.</div>`;
                });
        }
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const themeToggleIcon = document.getElementById('themeToggleIcon');
    const html = document.documentElement;

    function setTheme(theme, isManual = false) {
        html.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        if (isManual) {
            localStorage.setItem('theme_override', theme);
        }
        
        // Update Icon
        if (theme === 'dark') {
            if (themeToggleIcon) {
                themeToggleIcon.className = 'bi bi-moon-stars-fill';
            }
            if (themeToggleBtn) {
                themeToggleBtn.setAttribute('title', 'Switch to Light Theme');
            }
        } else {
            if (themeToggleIcon) {
                themeToggleIcon.className = 'bi bi-sun-fill';
            }
            if (themeToggleBtn) {
                themeToggleBtn.setAttribute('title', 'Switch to Dark Theme');
            }
        }
    }

    // Toggle click
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const current = html.getAttribute('data-theme') || 'dark';
            const next = current === 'dark' ? 'light' : 'dark';
            setTheme(next, true);
        });
    }

    // Set initial icon on load
    const currentTheme = html.getAttribute('data-theme') || 'dark';
    setTheme(currentTheme, false);

    // Keyboard shortcut 'D' or 'd' to toggle theme
    document.addEventListener('keydown', function(e) {
        if (e.key === 'd' || e.key === 'D') {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                return;
            }
            e.preventDefault();
            if (themeToggleBtn) {
                themeToggleBtn.click();
            }
        }
    });

    // Cursor Glow tracking (AK GRAF system)
    const cursorGlow = document.getElementById('cursorGlow');
    if (cursorGlow) {
        document.addEventListener('mousemove', function(e) {
            cursorGlow.style.left = e.clientX + 'px';
            cursorGlow.style.top = e.clientY + 'px';
        });
    }
});
</script>

<?php 
$customFooter = $settingModel->get('seo_footer_code', '');
if (!empty($customFooter)) {
    echo $customFooter;
}
?>
</body>
</html>

