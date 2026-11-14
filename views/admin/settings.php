<!-- Admin Settings -->
<?php $s = $settings ?? []; ?>
<div class="mb-4"><h5>Site Settings</h5></div>

<div class="settings-tabs">
    <button class="tab-btn active" onclick="switchTab('logo-favicon',this)">Logo & Favicon</button>
    <button class="tab-btn" onclick="switchTab('general',this)">General</button>
    <button class="tab-btn" onclick="switchTab('seo',this)">SEO</button>
    <button class="tab-btn" onclick="switchTab('payment',this)">Payment</button>
    <button class="tab-btn" onclick="switchTab('email',this)">Email (SMTP)</button>
    <button class="tab-btn" onclick="switchTab('whatsapp-integration',this)">WhatsApp Integration</button>
    <button class="tab-btn" onclick="switchTab('social',this)">Social Login</button>
    <button class="tab-btn" onclick="switchTab('support',this)">Support</button>
    <button class="tab-btn" onclick="switchTab('pages',this)">Pages</button>
    <button class="tab-btn" onclick="switchTab('orders',this)">Orders & Downloads</button>
    <button class="tab-btn" onclick="switchTab('sitemap',this)">Sitemap Manager</button>
    <button class="tab-btn" onclick="switchTab('tools',this)">Tools</button>
</div>

<!-- Logo & Favicon -->
<div class="settings-panel active" id="panel-logo-favicon">
    <div class="admin-card mt-3">
        <div class="card-header"><h6>Logo & Favicon</h6></div>
        <div class="card-body">
            <form method="POST" action="<?= url('admin/settings/logo') ?>" enctype="multipart/form-data" class="d-flex gap-4 flex-wrap">
                <?= csrfField() ?>
                <div>
                    <label class="dc-form-label">Site Logo</label>
                    <?php if(!empty($s['general']['site_logo'])): ?>
                        <div class="mb-2"><img src="<?= uploadUrl($s['general']['site_logo']) ?>" style="height:40px;"></div>
                    <?php endif; ?>
                    <input type="file" name="site_logo" class="dc-form-control" accept="image/*">
                </div>
                <div>
                    <label class="dc-form-label">Favicon</label>
                    <?php if(!empty($s['general']['site_favicon'])): ?>
                        <div class="mb-2"><img src="<?= uploadUrl($s['general']['site_favicon']) ?>" style="height:32px;"></div>
                    <?php endif; ?>
                    <input type="file" name="site_favicon" class="dc-form-control" accept="image/*,.ico">
                </div>
                <div class="align-self-end"><button type="submit" class="btn-dc-primary btn-dc-sm">Upload</button></div>
            </form>
        </div>
    </div>
</div>

<form method="POST" action="<?= url('admin/settings/update') ?>" class="admin-form">
<?= csrfField() ?>

<!-- General -->
<div class="settings-panel" id="panel-general">
    <div class="admin-card"><div class="card-body">
        <div class="form-row"><div class="form-group"><label>Site Name</label><input type="text" name="site_name" class="dc-form-control" value="<?= htmlspecialchars($s['general']['site_name'] ?? '') ?>"></div><div class="form-group"><label>Tagline</label><input type="text" name="site_tagline" class="dc-form-control" value="<?= htmlspecialchars($s['general']['site_tagline'] ?? '') ?>"></div></div>
        <div class="form-row"><div class="form-group"><label>Email</label><input type="email" name="site_email" class="dc-form-control" value="<?= htmlspecialchars($s['general']['site_email'] ?? '') ?>"></div><div class="form-group"><label>Phone</label><input type="text" name="site_phone" class="dc-form-control" value="<?= htmlspecialchars($s['general']['site_phone'] ?? '') ?>"></div></div>
        <div class="form-group"><label>Address</label><input type="text" name="site_address" class="dc-form-control" value="<?= htmlspecialchars($s['general']['site_address'] ?? '') ?>"></div>
        <div class="form-group mt-3">
            <label class="fw-bold text-primary"><i class="bi bi-shield-lock me-1"></i> DigiWS License Server Domain URL (Live Server)</label>
            <input type="url" name="license_server_url" class="dc-form-control" value="<?= htmlspecialchars($s['general']['license_server_url'] ?? 'http://localhost/licances') ?>" placeholder="e.g. https://license.yourdomain.com">
            <small class="text-muted">Enter your Live Server domain URL. E-Commerce purchases on DIGIWS will call this URL to generate license keys.</small>
        </div>
    </div></div>
</div>

<!-- SEO -->
<div class="settings-panel" id="panel-seo"><div class="admin-card"><div class="card-body">
    <div class="form-group"><label>Meta Title</label><input type="text" name="meta_title" class="dc-form-control" value="<?= htmlspecialchars($s['seo']['meta_title'] ?? 'Digiws - Premium Digital Products & Web Services') ?>" placeholder="Digiws - Premium Digital Products & Web Services"></div>
    <div class="form-group"><label>Meta Description</label><textarea name="meta_description" class="dc-form-control" rows="3" placeholder="Buy premium digital products, website templates, PHP scripts, themes and plugins. Professional web development services."><?= htmlspecialchars($s['seo']['meta_description'] ?? 'Buy premium digital products, website templates, PHP scripts, themes and plugins. Professional web development services.') ?></textarea></div>
    <div class="form-group"><label>Meta Keywords</label><textarea name="meta_keywords" class="dc-form-control" rows="3" placeholder="Build Smarter: The Ultimate Digital Products & Source Code Marketplace. Download premium source codes, scripts, templates, plugins, AI tools, and digital assets with instant access, secure delivery, and lifetime usability for your next project."><?= htmlspecialchars($s['seo']['meta_keywords'] ?? 'Build Smarter: The Ultimate Digital Products & Source Code Marketplace. Download premium source codes, scripts, templates, plugins, AI tools, and digital assets with instant access, secure delivery, and lifetime usability for your next project.') ?></textarea></div>
    
    <h6 class="mt-4 mb-3 text-primary fw-bold">Search Engine & Analytics Integrations</h6>
    <div class="form-group">
        <label>Google Search Console Verification Code</label>
        <input type="text" name="seo_google_search_console" class="dc-form-control" value="<?= htmlspecialchars($s['seo']['seo_google_search_console'] ?? '') ?>" placeholder="e.g. -jzwKvTWEpxQy58vZt07OLFpS9dIfqmX3_NTIABLPyI">
        <small class="text-muted">Enter only the verification code shown in your Google Search Console settings.</small>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Google Analytics Measurement ID</label>
            <input type="text" name="seo_google_analytics" class="dc-form-control" value="<?= htmlspecialchars($s['seo']['seo_google_analytics'] ?? '') ?>" placeholder="e.g. G-XXXXXXXXXX">
            <small class="text-muted">Your Google Analytics G-XXXX ID.</small>
        </div>
        <div class="form-group">
            <label>Google AdSense Publisher ID</label>
            <input type="text" name="seo_google_adsense" class="dc-form-control" value="<?= htmlspecialchars($s['seo']['seo_google_adsense'] ?? '') ?>" placeholder="e.g. ca-pub-XXXXXXXXXXXXXXXX">
            <small class="text-muted">Your Google AdSense ca-pub-XXXX ID.</small>
        </div>
    </div>
    <h6 class="mt-4 mb-3 text-primary fw-bold">Custom Codes & Scripts</h6>
    <div class="form-group">
        <label>Custom Header Code (inside &lt;head&gt;)</label>
        <textarea name="seo_header_code" class="dc-form-control" rows="4" placeholder="&lt;script&gt;...&lt;/script&gt; or custom verification meta tags"><?= htmlspecialchars($s['seo']['seo_header_code'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
        <label>Custom Footer Code (before &lt;/body&gt;)</label>
        <textarea name="seo_footer_code" class="dc-form-control" rows="4" placeholder="&lt;script&gt;...&lt;/script&gt;"><?= htmlspecialchars($s['seo']['seo_footer_code'] ?? '') ?></textarea>
    </div>
</div></div></div>

<!-- Payment -->
<div class="settings-panel" id="panel-payment"><div class="admin-card"><div class="card-body">
    <h6 class="mb-3">Currency</h6>
    <div class="form-row"><div class="form-group"><label>Currency Code</label><input type="text" name="currency" class="dc-form-control" value="<?= $s['payment']['currency'] ?? 'INR' ?>"></div><div class="form-group"><label>Currency Symbol</label><input type="text" name="currency_symbol" class="dc-form-control" value="<?= $s['payment']['currency_symbol'] ?? '₹' ?>"></div></div>
    <h6 class="mb-3 mt-3">Razorpay</h6>
    <div class="form-row"><div class="form-group"><label>Key ID</label><input type="text" name="razorpay_key_id" class="dc-form-control" value="<?= $s['payment']['razorpay_key_id'] ?? '' ?>"></div><div class="form-group"><label>Key Secret</label><input type="password" name="razorpay_key_secret" class="dc-form-control" value="<?= $s['payment']['razorpay_key_secret'] ?? '' ?>"></div></div>
    <h6 class="mb-3 mt-3">Stripe</h6>
    <div class="form-row"><div class="form-group"><label>Publishable Key</label><input type="text" name="stripe_publishable_key" class="dc-form-control" value="<?= $s['payment']['stripe_publishable_key'] ?? '' ?>"></div><div class="form-group"><label>Secret Key</label><input type="password" name="stripe_secret_key" class="dc-form-control" value="<?= $s['payment']['stripe_secret_key'] ?? '' ?>"></div></div>
    <h6 class="mb-3 mt-3">PayPal</h6>
    <div class="form-row"><div class="form-group"><label>Client ID</label><input type="text" name="paypal_client_id" class="dc-form-control" value="<?= $s['payment']['paypal_client_id'] ?? '' ?>"></div><div class="form-group"><label>Secret</label><input type="password" name="paypal_secret" class="dc-form-control" value="<?= $s['payment']['paypal_secret'] ?? '' ?>"></div></div>
    <div class="form-group"><label>Mode</label><select name="paypal_mode" class="dc-form-control" style="max-width:200px;"><option value="sandbox" <?= ($s['payment']['paypal_mode'] ?? '') === 'sandbox' ? 'selected' : '' ?>>Sandbox</option><option value="live" <?= ($s['payment']['paypal_mode'] ?? '') === 'live' ? 'selected' : '' ?>>Live</option></select></div>
    
    <h6 class="mb-3 mt-4">Custom UPI (Scan & Pay)</h6>
    <div class="form-row">
        <div class="form-group"><label>UPI ID (VPA)</label><input type="text" name="upi_id" class="dc-form-control" value="<?= $s['payment']['upi_id'] ?? '' ?>" placeholder="yourname@upi"></div>
        <div class="form-group"><label>Merchant Name</label><input type="text" name="upi_merchant_name" class="dc-form-control" value="<?= $s['payment']['upi_merchant_name'] ?? '' ?>" placeholder="Digital Store"></div>
    </div>
    <div class="form-group mt-2">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="upiDirectEnabled" name="payment_upi_direct_enabled" value="Yes" <?= ($s['payment']['payment_upi_direct_enabled'] ?? 'No') === 'Yes' ? 'checked' : '' ?>>
            <label class="form-check-label fw-bold text-primary" for="upiDirectEnabled">Enable Direct UPI at Checkout (Scan QR & Verify UTR/Screenshot)</label>
        </div>
    </div>
</div></div></div>

<!-- Email -->
<div class="settings-panel" id="panel-email"><div class="admin-card"><div class="card-body">
    <div class="form-row"><div class="form-group"><label>SMTP Host</label><input type="text" name="smtp_host" class="dc-form-control" value="<?= $s['email']['smtp_host'] ?? '' ?>"></div><div class="form-group"><label>Port</label><input type="text" name="smtp_port" class="dc-form-control" value="<?= $s['email']['smtp_port'] ?? '587' ?>"></div></div>
    <div class="form-row"><div class="form-group"><label>Username</label><input type="text" name="smtp_username" class="dc-form-control" value="<?= $s['email']['smtp_username'] ?? '' ?>"></div><div class="form-group"><label>Password</label><input type="password" name="smtp_password" class="dc-form-control" value="<?= $s['email']['smtp_password'] ?? '' ?>"></div></div>
    <div class="form-row"><div class="form-group"><label>From Email</label><input type="email" name="smtp_from_email" class="dc-form-control" value="<?= $s['email']['smtp_from_email'] ?? '' ?>"></div><div class="form-group"><label>From Name</label><input type="text" name="smtp_from_name" class="dc-form-control" value="<?= $s['email']['smtp_from_name'] ?? '' ?>"></div></div>
    <div class="form-group"><label>Encryption</label><select name="smtp_encryption" class="dc-form-control" style="max-width:200px;"><option value="tls" <?= ($s['email']['smtp_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option><option value="ssl" <?= ($s['email']['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option></select></div>
</div></div></div>

<!-- WhatsApp Integration -->
<div class="settings-panel" id="panel-whatsapp-integration"><div class="admin-card"><div class="card-body">
    <h6 class="mb-3">WhatsApp Notification Integration (MPWA)</h6>
    <div class="form-group mb-3">
        <label class="form-check-label d-flex align-items-center gap-2" style="font-weight: 500; cursor: pointer;">
            <input type="checkbox" name="whatsapp_enabled" value="Yes" <?= ($s['whatsapp']['whatsapp_enabled'] ?? 'No') === 'Yes' ? 'checked' : '' ?> style="width: 18px; height: 18px;">
            Enable WhatsApp Notifications
        </label>
        <small class="text-muted d-block mt-1">If checked, customers will receive their download links and purchase details on WhatsApp.</small>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label>API Key</label>
            <input type="password" name="whatsapp_api_key" class="dc-form-control" value="<?= htmlspecialchars($s['whatsapp']['whatsapp_api_key'] ?? '') ?>" placeholder="Enter api_key">
        </div>
        <div class="form-group">
            <label>Sender Device Number</label>
            <input type="text" name="whatsapp_sender" class="dc-form-control" value="<?= htmlspecialchars($s['whatsapp']['whatsapp_sender'] ?? '') ?>" placeholder="e.g. 9172888xxxxx">
        </div>
    </div>
    
    <div class="form-group mb-3">
        <label>Order Completed Message Template</label>
        <textarea name="whatsapp_order_completed_msg" class="dc-form-control" rows="5" placeholder="Enter message template"><?= htmlspecialchars($s['whatsapp']['whatsapp_order_completed_msg'] ?? "Hello {{customer_name}},\n\nThank you for your purchase!\nYour order #{{order_id}} is complete.\n\n*Items Purchased & Download Links:*\n{{product_details}}\n\nYou can also download your files from the dashboard here:\n{{download_url}}") ?></textarea>
        <small class="text-muted d-block mt-1">Available placeholders: <code>{{customer_name}}</code>, <code>{{order_id}}</code>, <code>{{product_details}}</code>, <code>{{download_url}}</code>, <code>{{amount}}</code></small>
    </div>

    <div class="form-group mb-3">
        <label>Order Status Update Message Template</label>
        <textarea name="whatsapp_order_status_msg" class="dc-form-control" rows="4" placeholder="Enter status update template"><?= htmlspecialchars($s['whatsapp']['whatsapp_order_status_msg'] ?? "Hello {{customer_name}},\n\nYour order *#{{order_id}}* status has been updated to: *{{order_status}}*.\n\nTrack order details here:\n{{order_detail_url}}") ?></textarea>
        <small class="text-muted d-block mt-1">Available placeholders: <code>{{customer_name}}</code>, <code>{{order_id}}</code>, <code>{{order_status}}</code>, <code>{{order_detail_url}}</code></small>
    </div>

    <div class="form-group">
        <label>Message Footer</label>
        <input type="text" name="whatsapp_footer_text" class="dc-form-control" value="<?= htmlspecialchars($s['whatsapp']['whatsapp_footer_text'] ?? 'Sent via Marketplace') ?>" placeholder="e.g. Sent via mpwa">
    </div>
</div></div></div>

<!-- Social -->
<div class="settings-panel" id="panel-social"><div class="admin-card"><div class="card-body">
    <h6 class="mb-3">Google OAuth</h6>
    <div class="form-row"><div class="form-group"><label>Client ID</label><input type="text" name="google_client_id" class="dc-form-control" value="<?= $s['social']['google_client_id'] ?? '' ?>"></div><div class="form-group"><label>Client Secret</label><input type="password" name="google_client_secret" class="dc-form-control" value="<?= $s['social']['google_client_secret'] ?? '' ?>"></div></div>
    <h6 class="mb-3 mt-3">Facebook OAuth</h6>
    <div class="form-row"><div class="form-group"><label>App ID</label><input type="text" name="facebook_app_id" class="dc-form-control" value="<?= $s['social']['facebook_app_id'] ?? '' ?>"></div><div class="form-group"><label>App Secret</label><input type="password" name="facebook_app_secret" class="dc-form-control" value="<?= $s['social']['facebook_app_secret'] ?? '' ?>"></div></div>
</div></div></div>

<!-- Support -->
<div class="settings-panel" id="panel-support"><div class="admin-card"><div class="card-body">
    <div class="form-group"><label>WhatsApp Number (with country code)</label><input type="text" name="whatsapp_number" class="dc-form-control" value="<?= $s['support']['whatsapp_number'] ?? '' ?>" placeholder="919876543210"></div>
    <div class="form-group"><label>WhatsApp Default Message</label><input type="text" name="whatsapp_message" class="dc-form-control" value="<?= htmlspecialchars($s['support']['whatsapp_message'] ?? '') ?>"></div>
</div></div></div>

<!-- Pages -->
<div class="settings-panel" id="panel-pages"><div class="admin-card"><div class="card-body">
    <div class="form-group"><label>Privacy Policy</label><textarea name="privacy_policy" class="dc-form-control" rows="8"><?= htmlspecialchars($s['pages']['privacy_policy'] ?? '') ?></textarea></div>
    <div class="form-group"><label>Terms & Conditions</label><textarea name="terms_conditions" class="dc-form-control" rows="8"><?= htmlspecialchars($s['pages']['terms_conditions'] ?? '') ?></textarea></div>
    <div class="form-group"><label>Refund Policy</label><textarea name="refund_policy" class="dc-form-control" rows="8"><?= htmlspecialchars($s['pages']['refund_policy'] ?? '') ?></textarea></div>
</div></div></div>

<!-- Orders & Downloads -->
<div class="settings-panel" id="panel-orders"><div class="admin-card"><div class="card-body">
    <h6 class="mb-3">Order Automation</h6>
    <div class="form-group">
        <label>Status After Payment Success</label>
        <select name="after_payment_status" class="dc-form-control" style="max-width:300px;">
            <option value="pending" <?= ($s['orders']['after_payment_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="processing" <?= ($s['orders']['after_payment_status'] ?? '') === 'processing' ? 'selected' : '' ?>>Processing</option>
            <option value="completed" <?= ($s['orders']['after_payment_status'] ?? 'completed') === 'completed' ? 'selected' : '' ?>>Completed</option>
        </select>
        <small class="text-muted d-block mt-1">Which status should be assigned to the order once payment is confirmed?</small>
    </div>

    <h6 class="mb-3 mt-4">Download Access Control</h6>
    <div class="form-group">
        <label>Downloadable Payment Statuses</label>
        <input type="text" name="downloadable_statuses" class="dc-form-control" value="<?= htmlspecialchars($s['orders']['downloadable_statuses'] ?? 'paid') ?>" placeholder="paid,success">
        <small class="text-muted d-block mt-1">Comma-separated payment statuses that allow user downloads (e.g. <code>paid,success</code>).</small>
    </div>
</div></div></div>

<!-- Sitemap Manager -->
<div class="settings-panel" id="panel-sitemap">
    <div class="admin-card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center bg-light">
            <h6 class="mb-0">Automatic XML Sitemap Configuration</h6>
            <div>
                <a href="<?= url('admin/sitemap/generate') ?>" class="btn-dc-primary btn-dc-sm me-2 text-decoration-none"><i class="bi bi-arrow-repeat me-1"></i>Rebuild Sitemap</a>
                <a href="<?= url('admin/sitemap/clear-cache') ?>" class="btn-dc-danger btn-dc-sm text-decoration-none"><i class="bi bi-trash3 me-1"></i>Clear Cache</a>
            </div>
        </div>
        <div class="card-body">
            <!-- Sitemap Status Summary -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="p-3 border rounded text-center bg-light">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size:0.75rem;">Total URL Count</small>
                        <h4 class="mb-0 text-primary fw-bold"><?= number_format($sitemapStats['url_count']) ?></h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded text-center bg-light">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size:0.75rem;">Last Generated Time</small>
                        <h4 class="mb-0 text-success fw-bold" style="font-size:1.15rem;"><?= htmlspecialchars($sitemapStats['last_generated']) ?></h4>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded text-center bg-light">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size:0.75rem;">Robots.txt Status</small>
                        <h4 class="mb-0 text-info fw-bold" style="font-size:1.15rem;">Automated</h4>
                    </div>
                </div>
            </div>

            <!-- Configuration Toggles -->
            <div class="form-row">
                <div class="form-group">
                    <label>Enable Auto Generation</label>
                    <select name="sitemap_auto_generation" class="dc-form-control">
                        <option value="Yes" <?= ($s['sitemap']['sitemap_auto_generation'] ?? 'Yes') === 'Yes' ? 'selected' : '' ?>>Yes (Rebuilds automatically on changes)</option>
                        <option value="No" <?= ($s['sitemap']['sitemap_auto_generation'] ?? 'Yes') === 'No' ? 'selected' : '' ?>>No (Manual generation only)</option>
                    </select>
                    <small class="text-muted">Trigger rebuild when products, categories, or blogs change.</small>
                </div>
                <div class="form-group">
                    <label>Enable Image Sitemap</label>
                    <select name="sitemap_include_images" class="dc-form-control">
                        <option value="Yes" <?= ($s['sitemap']['sitemap_include_images'] ?? 'Yes') === 'Yes' ? 'selected' : '' ?>>Yes (Include images inside product & blog URLs)</option>
                        <option value="No" <?= ($s['sitemap']['sitemap_include_images'] ?? 'Yes') === 'No' ? 'selected' : '' ?>>No</option>
                    </select>
                    <small class="text-muted">Adds Google Image schema tags for product & blog pictures.</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Enable Video Sitemap</label>
                    <select name="sitemap_include_videos" class="dc-form-control">
                        <option value="Yes" <?= ($s['sitemap']['sitemap_include_videos'] ?? 'Yes') === 'Yes' ? 'selected' : '' ?>>Yes (Include product demo videos)</option>
                        <option value="No" <?= ($s['sitemap']['sitemap_include_videos'] ?? 'Yes') === 'No' ? 'selected' : '' ?>>No</option>
                    </select>
                    <small class="text-muted">Adds Google Video schema tags for media files.</small>
                </div>
                <div class="form-group">
                    <label>Enable News Sitemap</label>
                    <select name="sitemap_include_news" class="dc-form-control">
                        <option value="Yes" <?= ($s['sitemap']['sitemap_include_news'] ?? 'Yes') === 'Yes' ? 'selected' : '' ?>>Yes (Include news articles schema)</option>
                        <option value="No" <?= ($s['sitemap']['sitemap_include_news'] ?? 'Yes') === 'No' ? 'selected' : '' ?>>No</option>
                    </select>
                    <small class="text-muted">Enables dynamic news posts inclusion.</small>
                </div>
            </div>

            <div class="form-group">
                <label>Ping Search Engines on Changes</label>
                <select name="sitemap_ping_engines" class="dc-form-control" style="max-width:300px;">
                    <option value="Yes" <?= ($s['sitemap']['sitemap_ping_engines'] ?? 'Yes') === 'Yes' ? 'selected' : '' ?>>Yes (Ping Google and Bing on rebuilds)</option>
                    <option value="No" <?= ($s['sitemap']['sitemap_ping_engines'] ?? 'Yes') === 'No' ? 'selected' : '' ?>>No</option>
                </select>
                <small class="text-muted d-block mt-1">Sends search console indexing requests automatically.</small>
            </div>

            <!-- List of generated sitemaps -->
            <h6 class="mt-4 mb-3">Generated Sitemap Files</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" style="font-size:0.88rem;">
                    <thead class="bg-light">
                        <tr>
                            <th>Sitemap File</th>
                            <th>URLs Indexed</th>
                            <th>File Size</th>
                            <th>Last Compiled</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sitemapStats['files'])): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">No sitemaps compiled yet. Click <strong>Rebuild Sitemap</strong> above to generate files.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sitemapStats['files'] as $f): ?>
                                <tr>
                                    <td>
                                        <a href="<?= url($f['name']) ?>" target="_blank" class="fw-bold text-decoration-none">
                                            <i class="bi bi-file-earmark-code text-primary me-1"></i><?= htmlspecialchars($f['name']) ?>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= number_format($f['urls']) ?> URLs</span></td>
                                    <td><?= htmlspecialchars($f['size']) ?></td>
                                    <td><?= htmlspecialchars($f['time']) ?></td>
                                    <td>
                                        <a href="<?= url($f['name']) ?>" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2 text-decoration-none" style="font-size:0.75rem;"><i class="bi bi-eye"></i> View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Tools -->
<div class="settings-panel" id="panel-tools"><div class="admin-card"><div class="card-body">
    <h6>Sitemap Generator</h6>
    <p class="text-muted" style="font-size:0.88rem;">Generate sitemap.xml for search engines.</p>
    <a href="<?= url('admin/sitemap/generate') ?>" class="btn-dc-primary btn-dc-sm"><i class="bi bi-globe me-1"></i>Generate Sitemap</a>
</div></div></div>

<div class="mt-4"><button type="submit" class="btn-dc-primary"><i class="bi bi-check-lg me-2"></i>Save Settings</button></div>
</form>

<script>
function switchTab(id, btn) {
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-' + id).classList.add('active');
    btn.classList.add('active');
}
</script>
