<!-- WhatsApp Campaign View -->
<?php
$settingModel = new Setting();
$whatsappEnabled = $settingModel->get('whatsapp_enabled', 'No') === 'Yes';
$whatsappApiKey = $settingModel->get('whatsapp_api_key', '');
$whatsappSender = $settingModel->get('whatsapp_sender', '');
$whatsappFooter = $settingModel->get('whatsapp_footer_text', 'Sent via Marketplace');

$db = Database::getInstance();
$subscriberCount = $db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE status = 'active'");
$customerCount = $db->fetchColumn("SELECT COUNT(*) FROM users WHERE phone IS NOT NULL AND phone != ''");
?>

<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h5 class="mb-1 fw-bold text-dark"><i class="bi bi-whatsapp text-success me-2"></i>WhatsApp Campaigns</h5>
        <p class="text-muted mb-0" style="font-size:0.875rem;">Design and send bulk WhatsApp marketing broadcasts to your subscribers and customers.</p>
    </div>
</div>

<?php if (!$whatsappEnabled || empty($whatsappApiKey) || empty($whatsappSender)): ?>
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4" style="border-radius:10px;">
        <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
        <div>
            <h6 class="alert-heading mb-1 fw-bold">WhatsApp Service is not configured!</h6>
            <p class="mb-0 small text-secondary">To send campaigns, please enable WhatsApp Notifications and configure your API Key and Sender Device Number in the <a href="<?= url('admin/settings') ?>" class="fw-semibold text-decoration-none">WhatsApp Settings Panel</a>.</p>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Send Form -->
    <div class="col-lg-7">
        <div class="admin-card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-send-fill text-success me-2"></i>Create WhatsApp Broadcast</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('admin/marketing/whatsapp-campaign/send') ?>" class="admin-form">
                    <?= csrfField() ?>
                    
                    <div class="form-group mb-3">
                        <label class="form-label fw-semibold small">Recipient Audience</label>
                        <select name="target_audience" class="dc-form-control" id="targetAudience" onchange="toggleAudienceFields(this.value)" required>
                            <option value="subscribers">Active Subscribers (<?= $subscriberCount ?> contacts)</option>
                            <option value="customers">Registered Customers (<?= $customerCount ?> contacts)</option>
                            <option value="test">Test Single Number</option>
                        </select>
                    </div>

                    <!-- Single Recipient Input (hidden by default) -->
                    <div class="form-group mb-3 d-none" id="singleRecipientGroup">
                        <label class="form-label fw-semibold small">Test Phone Number</label>
                        <input type="text" name="test_number" class="dc-form-control" placeholder="e.g. 9172888xxxxx" id="testNumber">
                        <small class="text-muted small">Enter number with country code (no spaces, dashes, or +).</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-semibold small">Message Template</label>
                        <textarea name="message" class="dc-form-control" rows="6" placeholder="Write your message here..." required></textarea>
                        <small class="text-muted d-block mt-1">Available placeholders: <code>{{customer_name}}</code></small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label fw-semibold small">Campaign Footer</label>
                        <input type="text" name="footer" class="dc-form-control" value="<?= htmlspecialchars($whatsappFooter) ?>" placeholder="e.g. Sent via mpwa">
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-semibold" <?= (!$whatsappEnabled || empty($whatsappApiKey) || empty($whatsappSender)) ? 'disabled' : '' ?>>
                        <i class="bi bi-whatsapp me-2"></i>Send Broadcast
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Help & Guidelines -->
    <div class="col-lg-5">
        <div class="admin-card border-0 shadow-sm" style="border-radius:12px; background-color:#f8fafc;">
            <div class="card-header bg-transparent py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle text-primary me-2"></i>How it works</h6>
            </div>
            <div class="card-body text-secondary" style="font-size:0.85rem; line-height:1.6;">
                <p>When you trigger a broadcast:</p>
                <ol class="ps-3 mb-4">
                    <li class="mb-2">The system will query all phone numbers for the selected audience segment.</li>
                    <li class="mb-2">It replaces the <code>{{customer_name}}</code> tag dynamically with the subscriber's name.</li>
                    <li class="mb-2">Messages are sent sequentially via the MPWA gateway connected to your device.</li>
                </ol>
                <div class="p-3 bg-white rounded border border-light shadow-sm">
                    <h7 class="fw-bold text-dark d-block mb-2"><i class="bi bi-shield-check text-success me-1"></i>Spam Prevention Tip</h7>
                    <p class="mb-0 small text-muted">To avoid WhatsApp account bans, ensure you only send messages to users who have opted-in or purchased from your store. Keep promotional messages spaced out and helpful.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAudienceFields(val) {
    const singleGrp = document.getElementById('singleRecipientGroup');
    const testNum = document.getElementById('testNumber');
    if (val === 'test') {
        singleGrp.classList.remove('d-none');
        testNum.setAttribute('required', 'required');
    } else {
        singleGrp.classList.add('d-none');
        testNum.removeAttribute('required');
    }
}
</script>
