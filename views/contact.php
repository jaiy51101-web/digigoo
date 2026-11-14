<?php
/**
 * Digiws - Contact Page
 */
if (!defined('BASE_PATH')) exit;
?>

<style>
@media (min-width: 768px) {
    .contact-form-col {
        border-left: 1px solid var(--border-color);
        padding-left: 28px !important;
    }
}
@media (max-width: 767px) {
    .contact-form-col {
        border-top: 1px solid var(--border-color);
        padding-top: 24px !important;
        margin-top: 16px;
    }
}
</style>

<div class="dc-auth-wrapper">
    <div class="dc-auth-card" style="max-width: 850px; width: 100%;">
        
        <?php if ($flash = getFlash()): ?>
        <div class="alert alert-<?= $flash['type'] ?> mb-4">
            <i class="bi bi-<?= $flash['type']==='danger'?'exclamation-circle':'check-circle' ?>-fill me-2"></i>
            <?= ($flash['message']) ?>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Left Side: Contact Info -->
            <div class="col-md-5">
                <div class="pe-md-2">
                    <span class="dc-form-label" style="color:var(--primary); font-weight:800; font-size:0.75rem; display:block; margin-bottom:10px;">Contact Us</span>
                    <h3 style="font-family:var(--font-title); font-weight:900; letter-spacing:-0.8px; margin-bottom:12px; color:var(--text-primary);">Get In Touch</h3>
                    <p style="font-size:0.85rem; color:var(--text-muted); line-height:1.6; margin-bottom:28px;">Have a question about our products, need a custom project, or looking for support? We're here to help.</p>

                    <!-- Contact Details List -->
                    <div class="d-flex gap-3 mb-3 align-items-center">
                        <div style="width:40px; height:40px; border-radius:10px; background:rgba(37,99,235,0.06); border:1px solid rgba(37,99,235,0.1); display:flex; align-items:center; justify-content:center; font-size:1.15rem; color:var(--primary); flex-shrink:0;">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div>
                            <small class="dc-form-label" style="font-size:0.65rem; margin-bottom:1px; line-height:1;">Email Us</small>
                            <a href="mailto:<?= htmlspecialchars($siteEmail) ?>" style="font-size:0.85rem; font-weight:700; color:var(--text-primary); text-decoration:none; word-break:break-all;"><?= htmlspecialchars($siteEmail) ?></a>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-3 align-items-center">
                        <div style="width:40px; height:40px; border-radius:10px; background:rgba(34,197,94,0.06); border:1px solid rgba(34,197,94,0.1); display:flex; align-items:center; justify-content:center; font-size:1.15rem; color:#22c55e; flex-shrink:0;">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div>
                            <small class="dc-form-label" style="font-size:0.65rem; margin-bottom:1px; line-height:1;">WhatsApp Chat</small>
                            <a href="https://wa.me/<?= htmlspecialchars($whatsappNumber) ?>" style="font-size:0.85rem; font-weight:700; color:var(--text-primary); text-decoration:none;">+<?= htmlspecialchars($whatsappNumber) ?></a>
                        </div>
                    </div>

                    <?php if ($siteAddress): ?>
                    <div class="d-flex gap-3 mb-3 align-items-center">
                        <div style="width:40px; height:40px; border-radius:10px; background:rgba(124,58,237,0.06); border:1px solid rgba(124,58,237,0.1); display:flex; align-items:center; justify-content:center; font-size:1.15rem; color:#7c3aed; flex-shrink:0;">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div>
                            <small class="dc-form-label" style="font-size:0.65rem; margin-bottom:1px; line-height:1;">Location</small>
                            <span style="font-size:0.85rem; font-weight:700; color:var(--text-primary); line-height:1.3; display:block;"><?= htmlspecialchars($siteAddress) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Side: Contact Form -->
            <div class="col-md-7 contact-form-col">
                <h4 style="font-family:var(--font-title); font-weight:800; font-size:1.1rem; margin-bottom:20px; color:var(--text-primary);">Send a Message</h4>
                <form method="POST" action="<?= url('contact') ?>">
                    <?= csrfField() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="dc-form-label">Your Name</label>
                            <input type="text" name="name" class="dc-form-control" placeholder="Rahul" required style="padding:10px 14px; font-size:0.88rem;">
                        </div>
                        <div class="col-md-6">
                            <label class="dc-form-label">Email Address</label>
                            <input type="email" name="email" class="dc-form-control" placeholder="you@email.com" required style="padding:10px 14px; font-size:0.88rem;">
                        </div>
                        <div class="col-12">
                            <label class="dc-form-label">Subject</label>
                            <select name="subject" class="dc-form-control" required style="padding:10px 14px; font-size:0.88rem;">
                                <option value="General Inquiry">General Inquiry</option>
                                <option value="Pre-Sale Question">Pre-Sale Question</option>
                                <option value="Custom Project">Custom Website Development</option>
                                <option value="Support">Product Support</option>
                                <option value="Billing">Billing Issue</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="dc-form-label">Message</label>
                            <textarea name="message" class="dc-form-control" rows="4" placeholder="Tell us how we can help..." required style="padding:12px 14px; font-size:0.88rem;"></textarea>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn-dc-primary w-100 py-3" style="border-radius:var(--radius-md); font-weight:700;">
                                <i class="bi bi-send-fill me-2"></i> Send Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
