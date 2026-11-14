<?php
/**
 * DigiWS - Premium Email Templates Seeder
 * 25 Professional Responsive HTML Email Templates
 * Run: http://localhost/digiws/database/seed_premium_templates.php
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';

// Allow CLI and admin-only access
if (php_sapi_name() !== 'cli') {
    requireAdmin();
}

$db = Database::getInstance();

// Check if premium templates already exist
$existingCount = $db->fetchColumn("SELECT COUNT(*) FROM marketing_templates WHERE body_html LIKE '<!DOCTYPE html>'");
if ($existingCount >= 25) {
    die('Premium templates already exist. Skipping seed.');
}

function getTemplateStyles() {
    return '<style>
        body { margin: 0; padding: 0; background-color: #f4f6f9; font-family: Arial, Helvetica, sans-serif; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        .email-wrapper { width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .email-header { background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #3b82f6 100%); padding: 40px 30px; text-align: center; }
        .email-header h1 { margin: 0; font-size: 28px; font-weight: 800; color: #ffffff; line-height: 1.3; }
        .email-header p { margin: 10px 0 0; font-size: 16px; color: #e0e7ff; line-height: 1.6; }
        .email-body { padding: 35px 30px; background-color: #ffffff; }
        .email-body p { font-size: 16px; color: #334155; line-height: 1.7; margin: 0 0 15px; }
        .email-body h2 { font-size: 22px; font-weight: 700; color: #1e293b; margin: 0 0 15px; }
        .coupon-box { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 2px dashed #3b82f6; border-radius: 12px; padding: 25px; text-align: center; margin: 25px 0; }
        .coupon-label { font-size: 14px; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px; }
        .coupon-code { font-size: 32px; font-weight: 900; color: #1e40af; letter-spacing: 3px; margin: 8px 0; font-family: "Courier New", monospace; }
        .discount-badge { display: inline-block; background: linear-gradient(135deg, #dc2626, #ef4444); color: #ffffff; padding: 8px 20px; border-radius: 50px; font-size: 18px; font-weight: 800; margin: 10px 0; text-transform: uppercase; }
        .cta-button { display: inline-block; padding: 14px 35px; background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #ffffff; text-decoration: none; border-radius: 50px; font-weight: 700; font-size: 16px; margin: 20px 0; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3); }
        .cta-button:hover { background: linear-gradient(135deg, #1d4ed8, #1e40af); }
        .product-grid { display: block; width: 100%; margin: 20px 0; }
        .product-item { display: inline-block; width: 48%; margin: 1%; text-align: center; vertical-align: top; }
        .product-image { width: 100%; max-width: 200px; height: auto; border-radius: 8px; margin-bottom: 8px; }
        .product-name { font-size: 14px; font-weight: 600; color: #1e293b; margin: 4px 0; }
        .product-price { font-size: 16px; font-weight: 700; color: #2563eb; }
        .social-icons { text-align: center; margin: 25px 0; }
        .social-icons a { display: inline-block; margin: 0 8px; width: 36px; height: 36px; background-color: #f1f5f9; border-radius: 50%; text-align: center; line-height: 36px; color: #475569; text-decoration: none; font-size: 16px; }
        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 25px 0; }
        .email-footer { background-color: #f8fafc; padding: 25px 30px; text-align: center; border-top: 1px solid #e2e8f0; }
        .email-footer p { font-size: 13px; color: #64748b; line-height: 1.6; margin: 4px 0; }
        .email-footer a { color: #2563eb; text-decoration: none; font-weight: 600; }
        .expiry-text { font-size: 14px; color: #dc2626; font-weight: 600; margin-top: 10px; }
        @media only screen and (max-width: 600px) {
            .email-wrapper { width: 100% !important; border-radius: 0 !important; }
            .email-header { padding: 30px 20px !important; }
            .email-header h1 { font-size: 22px !important; }
            .email-body { padding: 25px 20px !important; }
            .product-item { width: 100% !important; display: block !important; margin: 10px 0 !important; }
            .coupon-code { font-size: 24px !important; }
            .cta-button { padding: 12px 25px !important; font-size: 14px !important; }
        }
    </style>';
}

function getHeader($title, $subtitle = '') {
    $subtitleHtml = $subtitle ? "<p>$subtitle</p>" : '';
    return '<div class="email-header">
        <h1>' . $title . '</h1>
        ' . $subtitleHtml . '
    </div>';
}

function getCouponBox($code, $discount, $expiry = '') {
    $expiryHtml = $expiry ? "<p class='expiry-text'>⏰ Expires: $expiry</p>" : '';
    return '<div class="coupon-box">
        <p class="coupon-label">Your Exclusive Coupon Code</p>
        <div class="coupon-code">' . $code . '</div>
        <div class="discount-badge">' . $discount . ' OFF</div>
        ' . $expiryHtml . '
    </div>';
}

function getCTA($text, $url) {
    return '<a href="' . $url . '" class="cta-button">' . $text . ' →</a>';
}

function getFooter() {
    return '<div class="email-footer">
        <p><strong>DigiWS</strong></p>
        <p><a href="https://digiws.com">https://digiws.com</a></p>
        <p>Support: <a href="mailto:support@digiws.com">support@digiws.com</a></p>
        <p style="margin-top: 15px; font-size: 12px; color: #94a3b8;">© {{current_year}} DigiWS. All rights reserved.</p>
    </div>';
}

function getSocialIcons() {
    return '<div class="social-icons">
        <a href="#" style="background-color: #1877f2; color: #ffffff;">f</a>
        <a href="#" style="background-color: #1da1f2; color: #ffffff;">t</a>
        <a href="#" style="background-color: #e4405f; color: #ffffff;">i</a>
        <a href="#" style="background-color: #0077b5; color: #ffffff;">in</a>
    </div>';
}

$templates = [
    // 1. Welcome Email
    [
        'name' => 'Welcome Email - Premium',
        'subject' => 'Welcome {{customer_name}} 🎉 Enjoy {{discount}} OFF on DigiWS',
        'category' => 'welcome',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Welcome to DigiWS</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#f4f6f9;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f6f9; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('Welcome to DigiWS!', 'Your Digital Product Marketplace') . '
                        <div class="email-body">
                            <p>Hi {{customer_name}},</p>
                            <p>Welcome to <strong>DigiWS</strong> - India\'s trusted Digital Product Marketplace!</p>
                            <p>Thank you for joining us. As a special welcome gift, enjoy an exclusive discount on your first purchase.</p>
                            ' . getCouponBox('{{coupon_code}}', '{{discount}}', '{{expiry_date}}') . '
                            <p>Browse thousands of premium digital products:</p>
                            <div class="product-grid">
                                <div class="product-item">
                                    <img src="https://digiws.com/assets/images/products/php-scripts.jpg" alt="PHP Scripts" class="product-image">
                                    <div class="product-name">PHP Scripts</div>
                                </div>
                                <div class="product-item">
                                    <img src="https://digiws.com/assets/images/products/wordpress-themes.jpg" alt="WordPress Themes" class="product-image">
                                    <div class="product-name">WordPress Themes</div>
                                </div>
                                <div class="product-item">
                                    <img src="https://digiws.com/assets/images/products/plugins.jpg" alt="Plugins" class="product-image">
                                    <div class="product-name">Plugins</div>
                                </div>
                                <div class="product-item">
                                    <img src="https://digiws.com/assets/images/products/ai-tools.jpg" alt="AI Tools" class="product-image">
                                    <div class="product-name">AI Tools</div>
                                </div>
                            </div>
                            ' . getCTA('Start Shopping Now', 'https://digiws.com') . '
                            <p>If you have any questions, feel free to reach out to our support team.</p>
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Welcome {{customer_name}}! Enjoy {{discount}} OFF with code {{coupon_code}}. Expires {{expiry_date}}. Shop now at https://digiws.com'
    ],
    // 2. Birthday Email
    [
        'name' => 'Birthday Email - Premium',
        'subject' => 'Happy Birthday {{customer_name}} 🎂 Your Gift is Ready',
        'category' => 'promotional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Happy Birthday!</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#fdf2f8;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fdf2f8; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('🎂 Happy Birthday!', 'A special gift just for you') . '
                        <div class="email-body" style="text-align:center;">
                            <p style="font-size:18px;">Hi {{customer_name}},</p>
                            <p style="font-size:18px; color:#9d174d; font-weight:600;">Wishing you a fantastic birthday!</p>
                            <p>Celebrate with an exclusive <strong>{{discount}}</strong> Birthday Discount.</p>
                            ' . getCouponBox('{{coupon_code}}', '{{discount}}', '{{expiry_date}}') . '
                            <p>Enjoy your special day with DigiWS premium digital products.</p>
                            ' . getCTA('Claim Your Gift', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Happy Birthday {{customer_name}}! Enjoy {{discount}} OFF with code {{coupon_code}}. Expires {{expiry_date}}.'
    ],
    // 3. Flash Sale
    [
        'name' => 'Flash Sale - Premium',
        'subject' => '⚡ Flash Sale {{discount}} OFF Ends Soon',
        'category' => 'promotional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Flash Sale</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#1e1b4b;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#1e1b4b; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper" style="background-color:#312e81;">
                        ' . getHeader('⚡ Flash Sale!', '24 Hours Only - Up to {{discount}} OFF') . '
                        <div class="email-body" style="text-align:center; color:#e0e7ff;">
                            <p style="font-size:18px; color:#fbbf24; font-weight:700;">Hello {{customer_name}},</p>
                            <p style="font-size:20px; color:#fbbf24; font-weight:800; margin:15px 0;">Flash Sale is NOW LIVE!</p>
                            <p>Use Coupon <strong style="color:#fbbf24;">{{coupon_code}}</strong></p>
                            <p>Save <strong style="color:#fbbf24;">{{discount}}</strong> on all digital products</p>
                            <p style="font-size:14px; color:#fbbf24;">⏰ Offer ends: {{expiry_date}}</p>
                            ' . getCouponBox('{{coupon_code}}', '{{discount}}', '{{expiry_date}}') . '
                            ' . getCTA('Shop Flash Sale', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider" style="border-color:#4338ca;">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Flash Sale {{discount}} OFF! Use code {{coupon_code}}. Ends {{expiry_date}}. Shop now at https://digiws.com'
    ],
    // 4. Black Friday
    [
        'name' => 'Black Friday - Premium',
        'subject' => 'Black Friday {{discount}} OFF - Massive Savings Inside',
        'category' => 'promotional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Black Friday Sale</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#0a0a0a;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#0a0a0a; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper" style="background-color:#171717;">
                        ' . getHeader('🛍️ Black Friday Mega Sale', 'The Biggest Sale of the Year') . '
                        <div class="email-body" style="text-align:center; color:#e5e5e5;">
                            <p style="font-size:20px; color:#dc2626; font-weight:800; text-transform:uppercase;">Hi {{customer_name}}</p>
                            <p style="font-size:18px; line-height:1.7;">Black Friday Mega Sale is <strong style="color:#dc2626;">LIVE</strong>.</p>
                            <p>Use <strong style="color:#fbbf24;">{{coupon_code}}</strong></p>
                            <p>Save <strong style="color:#fbbf24;">{{discount}}</strong> on every digital product</p>
                            <p style="font-size:14px; color:#a3a3a3;">⚡ Limited Time Only</p>
                            ' . getCouponBox('{{coupon_code}}', '{{discount}}') . '
                            ' . getCTA('Shop Black Friday', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider" style="border-color:#262626;">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Black Friday Mega Sale! Save {{discount}} with code {{coupon_code}}. Limited time. Shop at https://digiws.com'
    ],
    // 5. Cyber Monday
    [
        'name' => 'Cyber Monday - Premium',
        'subject' => 'Cyber Monday {{discount}} OFF - Online Exclusive',
        'category' => 'promotional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Cyber Monday</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#0f172a;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#0f172a; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper" style="background-color:#1e293b;">
                        ' . getHeader('💻 Cyber Monday', 'Online Exclusive Deals') . '
                        <div class="email-body" style="text-align:center; color:#e2e8f0;">
                            <p style="font-size:18px; color:#38bdf8; font-weight:700;">Hello {{customer_name}},</p>
                            <p style="font-size:20px; color:#38bdf8; font-weight:800; margin:15px 0;">Cyber Monday is finally here!</p>
                            <p>Unlock <strong style="color:#fbbf24;">{{discount}}</strong> using</p>
                            <p>Coupon: <strong style="color:#fbbf24;">{{coupon_code}}</strong></p>
                            ' . getCTA('Start Downloading Today', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider" style="border-color:#334155;">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Cyber Monday! Unlock {{discount}} with code {{coupon_code}}. Start downloading at https://digiws.com'
    ],
    // 6. New Year
    [
        'name' => 'New Year - Premium',
        'subject' => 'Happy New Year {{customer_name}} - {{discount}} OFF Inside',
        'category' => 'promotional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Happy New Year</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#1e1b4b;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#1e1b4b; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper" style="background-color:#312e81;">
                        ' . getHeader('🎆 Happy New Year!', 'Start with {{discount}} OFF') . '
                        <div class="email-body" style="text-align:center; color:#e0e7ff;">
                            <p style="font-size:18px; color:#fbbf24; font-weight:700;">Happy New Year {{customer_name}}!</p>
                            <p>Start your year with premium digital products.</p>
                            <p>Coupon: <strong style="color:#fbbf24;">{{coupon_code}}</strong></p>
                            <p>Discount: <strong style="color:#fbbf24;">{{discount}}</strong></p>
                            <p style="font-size:14px; color:#fbbf24;">Valid until: {{expiry_date}}</p>
                            ' . getCTA('Celebrate with {{discount}} OFF', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider" style="border-color:#4338ca;">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Happy New Year {{customer_name}}! Start with {{discount}} OFF. Code: {{coupon_code}}. Valid until {{expiry_date}}.'
    ],
    // 7. Summer Sale
    [
        'name' => 'Summer Sale - Premium',
        'subject' => 'Summer Sale {{discount}} OFF - Hot Deals Inside',
        'category' => 'promotional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Summer Sale</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#fff7ed;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fff7ed; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('☀️ Summer Sale', 'Get {{discount}} OFF Everything') . '
                        <div class="email-body" style="text-align:center;">
                            <p style="font-size:18px; color:#c2410c; font-weight:600;">Hi {{customer_name}},</p>
                            <p style="font-size:20px; color:#9a3412; font-weight:800; margin:15px 0;">Summer Sale is Live!</p>
                            <p>Get <strong>{{discount}}</strong> OFF</p>
                            <p>Coupon: <strong style="color:#f97316;">{{coupon_code}}</strong></p>
                            ' . getCTA('Shop Summer Sale', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Summer Sale! Get {{discount}} OFF with code {{coupon_code}}. Shop now at https://digiws.com'
    ],
    // 8. End Season Sale
    [
        'name' => 'End Season Sale - Premium',
        'subject' => 'Last Chance! {{discount}} OFF - Ends {{expiry_date}}',
        'category' => 'promotional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>End of Season Sale</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#fdf4ff;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fdf4ff; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('🍂 End of Season Sale', 'Last Chance to Save Big') . '
                        <div class="email-body" style="text-align:center;">
                            <p style="font-size:18px; color:#86198f; font-weight:600;">Hello {{customer_name}},</p>
                            <p>Last chance to save big!</p>
                            <p>Use <strong>{{coupon_code}}</strong></p>
                            <p>Save <strong>{{discount}}</strong></p>
                            <p style="font-size:14px; color:#c026d3; font-weight:600;">⏳ Ends: {{expiry_date}}</p>
                            ' . getCTA('Grab the Deals', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'End of Season Sale! Save {{discount}} with code {{coupon_code}}. Ends {{expiry_date}}. Shop now.'
    ],
    // 9. Clearance Sale
    [
        'name' => 'Clearance Sale - Premium',
        'subject' => 'Huge Clearance Sale - Up to {{discount}} OFF',
        'category' => 'promotional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Clearance Sale</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#fef2f2;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fef2f2; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('⚠️ Clearance Sale', 'Everything Must Go!') . '
                        <div class="email-body" style="text-align:center;">
                            <p style="font-size:18px; color:#991b1b; font-weight:600;">Hi {{customer_name}},</p>
                            <p style="font-size:20px; color:#991b1b; font-weight:800; margin:15px 0;">Huge Clearance Sale</p>
                            <p>Everything must go!</p>
                            <p>Coupon: <strong style="color:#dc2626;">{{coupon_code}}</strong></p>
                            <p>Discount: <strong style="color:#dc2626;">{{discount}}</strong></p>
                            ' . getCTA('Shop Clearance', 'https://digiws.com') . '
                            <p style="font-size:14px; color:#991b1b; font-weight:600;">⚠️ Limited stock available</p>
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Clearance Sale! Up to {{discount}} OFF. Use code {{coupon_code}}. Limited stock. Shop now.'
    ],
    // 10. Holiday Offer
    [
        'name' => 'Holiday Offer - Premium',
        'subject' => 'Happy Holidays {{customer_name}} - {{discount}} OFF Gift',
        'category' => 'promotional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Holiday Special</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#f0f9ff;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0f9ff; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('❄️ Happy Holidays!', 'Special Gift Inside for You') . '
                        <div class="email-body" style="text-align:center;">
                            <p style="font-size:18px; color:#075985; font-weight:600;">Happy Holidays {{customer_name}}!</p>
                            <p>Enjoy <strong>{{discount}}</strong> on us</p>
                            <p>Coupon: <strong style="color:#0ea5e9;">{{coupon_code}}</strong></p>
                            <p style="font-size:14px; color:#075985;">Expires: {{expiry_date}}</p>
                            ' . getCouponBox('{{coupon_code}}', '{{discount}}', '{{expiry_date}}') . '
                            ' . getCTA('Unwrap Your Gift', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Happy Holidays {{customer_name}}! Enjoy {{discount}} OFF with code {{coupon_code}}. Expires {{expiry_date}}.'
    ],
    // 11. VIP Invitation
    [
        'name' => 'VIP Invitation - Premium',
        'subject' => 'You\'re Invited! Exclusive VIP Access for {{customer_name}}',
        'category' => 'vip',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>VIP Invitation</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#1e1b4b;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#1e1b4b; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper" style="background-color:#312e81;">
                        ' . getHeader('👑 VIP Invitation', 'Exclusive Access Just for You') . '
                        <div class="email-body" style="text-align:center; color:#e0e7ff;">
                            <p style="font-size:18px; color:#fbbf24; font-weight:700;">Dear {{customer_name}},</p>
                            <p style="font-size:18px; color:#fbbf24; margin:15px 0;">Congratulations!</p>
                            <p>You\'ve been selected for DigiWS VIP Membership.</p>
                            <p>Exclusive Coupon: <strong style="color:#fbbf24;">{{coupon_code}}</strong></p>
                            <p>Enjoy <strong style="color:#fbbf24;">{{discount}}</strong></p>
                            ' . getCTA('Claim VIP Access', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider" style="border-color:#4338ca;">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'VIP Invitation! Exclusive access for {{customer_name}}. Code: {{coupon_code}}. Enjoy {{discount}}.'
    ],
    // 12. Loyalty Reward
    [
        'name' => 'Loyalty Reward - Premium',
        'subject' => '{{customer_name}} - You Earned a Loyalty Reward!',
        'category' => 'loyalty',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Loyalty Reward</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#faf5ff;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#faf5ff; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('🏆 Loyalty Reward Unlocked!', 'Thank You for Being a Valued Customer') . '
                        <div class="email-body" style="text-align:center;">
                            <p style="font-size:18px; color:#5b21b6; font-weight:700;">Hello {{customer_name}},</p>
                            <p>You\'ve earned a special loyalty reward for being a valued customer.</p>
                            <p>Reward Coupon: <strong style="color:#7c3aed;">{{coupon_code}}</strong></p>
                            <p>Discount: <strong style="color:#7c3aed;">{{discount}}</strong></p>
                            ' . getCouponBox('{{coupon_code}}', '{{discount}}') . '
                            ' . getCTA('Claim Reward', 'https://digiws.com') . '
                            <p>Thank you for shopping with DigiWS.</p>
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Loyalty Reward! Hello {{customer_name}}. Code: {{coupon_code}}. Discount: {{discount}}. Thank you!'
    ],
    // 13. Referral
    [
        'name' => 'Referral Program - Premium',
        'subject' => 'Refer & Earn {{discount}} OFF - {{customer_name}}',
        'category' => 'referral',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Referral Program</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#eff6ff;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#eff6ff; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('🎁 Refer & Earn Rewards', 'Share the Love') . '
                        <div class="email-body" style="text-align:center;">
                            <p style="font-size:18px; color:#1e40af; font-weight:600;">Hi {{customer_name}},</p>
                            <p>Invite friends to DigiWS. You both receive <strong>{{discount}}</strong>!</p>
                            <p>Your Referral Coupon: <strong style="color:#2563eb;">{{coupon_code}}</strong></p>
                            ' . getCouponBox('{{coupon_code}}', '{{discount}}') . '
                            ' . getCTA('Start Referring', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Refer friends and earn {{discount}}! Your code: {{coupon_code}}. Start referring at https://digiws.com'
    ],
    // 14. Review Request
    [
        'name' => 'Review Request - Premium',
        'subject' => 'How was your experience, {{customer_name}}?',
        'category' => 'engagement',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Review Request</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#f8fafc;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f8fafc; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('⭐ How Was Your Experience?', 'Share Your Feedback') . '
                        <div class="email-body">
                            <p>Hello {{customer_name}},</p>
                            <p>Thanks for purchasing <strong>{{product_name}}</strong>.</p>
                            <p>Please leave a review. Your feedback helps DigiWS improve and helps other customers make informed decisions.</p>
                            ' . getCTA('Write a Review', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Hi {{customer_name}}, how was your experience with {{product_name}}? Please leave a review.'
    ],
    // 15. Newsletter
    [
        'name' => 'Newsletter - Premium',
        'subject' => '{{customer_name}} - Your Monthly DigiWS Digest',
        'category' => 'newsletter',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Monthly Newsletter</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#f8fafc;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f8fafc; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('📰 Monthly Digest', 'Latest Products & Updates') . '
                        <div class="email-body">
                            <p>Hello {{customer_name}},</p>
                            <p>Welcome to this month\'s DigiWS Newsletter. Discover the latest digital products and exclusive offers.</p>
                            <div class="product-grid">
                                <div class="product-item">
                                    <img src="https://digiws.com/assets/images/products/php-scripts.jpg" alt="PHP Scripts" class="product-image">
                                    <div class="product-name">Latest PHP Scripts</div>
                                </div>
                                <div class="product-item">
                                    <img src="https://digiws.com/assets/images/products/ai-tools.jpg" alt="AI Tools" class="product-image">
                                    <div class="product-name">New AI Tools</div>
                                </div>
                                <div class="product-item">
                                    <img src="https://digiws.com/assets/images/products/templates.jpg" alt="Templates" class="product-image">
                                    <div class="product-name">Premium Templates</div>
                                </div>
                                <div class="product-item">
                                    <img src="https://digiws.com/assets/images/products/plugins.jpg" alt="Plugins" class="product-image">
                                    <div class="product-name">WordPress Plugins</div>
                                </div>
                            </div>
                            ' . getCTA('Explore All Products', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Hello {{customer_name}}, welcome to your monthly DigiWS newsletter. Discover latest products and offers.'
    ],
    // 16. Product Launch
    [
        'name' => 'Product Launch - Premium',
        'subject' => 'New Products Just Arrived - {{customer_name}}',
        'category' => 'announcement',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>New Products</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#faf5ff;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#faf5ff; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('🚀 New Products Have Arrived!', 'Be the First to Explore') . '
                        <div class="email-body">
                            <p>Hi {{customer_name}},</p>
                            <p>New Digital Products have arrived. Explore our newest:</p>
                            <p><strong>AI Tools</strong><br><strong>Templates</strong><br><strong>PHP Scripts</strong><br><strong>Source Code</strong></p>
                            <p>Available now at DigiWS.</p>
                            ' . getCTA('Explore New Products', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'New products arrived! Hi {{customer_name}}, explore AI Tools, Templates, PHP Scripts. Available now.'
    ],
    // 17. Stock Alert
    [
        'name' => 'Stock Alert - Premium',
        'subject' => 'Back in Stock! {{product_name}} - {{customer_name}}',
        'category' => 'notification',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Back in Stock</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#f0fdf4;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0fdf4; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('✅ Good News!', '{{product_name}} is Back in Stock') . '
                        <div class="email-body" style="text-align:center;">
                            <p>Hello {{customer_name}},</p>
                            <p>Great news! The item you were waiting for is now back in stock.</p>
                            <p style="font-size:18px; font-weight:700; color:#065f46;">{{product_name}}</p>
                            <p style="font-size:14px; color:#10b981;">⚡ Limited stock available</p>
                            ' . getCTA('Download Now', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Good news {{customer_name}}! {{product_name}} is back in stock. Download now at https://digiws.com'
    ],
    // 18. Abandoned Cart
    [
        'name' => 'Abandoned Cart - Premium',
        'subject' => 'You Forgot Something, {{customer_name}} - Complete Your Purchase',
        'category' => 'cart',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Abandoned Cart</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#fff1f2;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fff1f2; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('🛒 You Left Something Behind', 'Complete Your Purchase') . '
                        <div class="email-body" style="text-align:center;">
                            <p style="font-size:18px; color:#9f1239; font-weight:600;">Hi {{customer_name}},</p>
                            <p>Your cart is waiting for you! Complete your purchase today.</p>
                            ' . getCouponBox('{{coupon_code}}', '{{discount}}', '{{expiry_date}}') . '
                            <p style="font-size:14px; color:#f43f5e;">Cart expires soon. Don\'t miss out!</p>
                            ' . getCTA('Complete Your Purchase', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Hi {{customer_name}}, you left items in your cart. Complete your purchase with {{discount}} OFF using {{coupon_code}}.'
    ],
    // 19. Win Back
    [
        'name' => 'Win Back - Premium',
        'subject' => 'We Miss You {{customer_name}} - {{discount}} OFF Inside',
        'category' => 'winback',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>We Miss You</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#fef2f2;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fef2f2; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('👋 We Miss You!', 'Come Back with {{discount}} OFF') . '
                        <div class="email-body" style="text-align:center;">
                            <p style="font-size:18px; color:#9f1239; font-weight:600;">Hi {{customer_name}},</p>
                            <p>We haven\'t seen you in a while. We miss you!</p>
                            <p>Come back with <strong>{{discount}}</strong></p>
                            <p>Coupon: <strong style="color:#f43f5e;">{{coupon_code}}</strong></p>
                            <p style="font-size:14px; color:#f43f5e;">Offer ends: {{expiry_date}}</p>
                            ' . getCouponBox('{{coupon_code}}', '{{discount}}', '{{expiry_date}}') . '
                            ' . getCTA('Come Back & Save', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'We miss you {{customer_name}}! Come back with {{discount}} OFF. Code: {{coupon_code}}. Expires {{expiry_date}}.'
    ],
    // 20. Order Confirmation
    [
        'name' => 'Order Confirmation - Premium',
        'subject' => 'Order Confirmed - {{order_id}} - {{customer_name}}',
        'category' => 'transactional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Order Confirmation</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#f0fdf4;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0fdf4; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('✅ Order Confirmed!', 'Thank You for Your Purchase') . '
                        <div class="email-body">
                            <p>Hi {{customer_name}},</p>
                            <p>Thank you for your purchase! Your order has been confirmed and is being processed.</p>
                            <div class="coupon-box" style="text-align:left;">
                                <p class="coupon-label">Order Details</p>
                                <p><strong>Order ID:</strong> {{order_id}}</p>
                                <p><strong>Product:</strong> {{product_name}}</p>
                                <p><strong>Download:</strong> <a href="{{download_link}}" style="color:#2563eb;">{{download_link}}</a></p>
                                <p><strong>Invoice:</strong> <a href="{{invoice_url}}" style="color:#2563eb;">{{invoice_url}}</a></p>
                            </div>
                            <p>We will send you a notification when your order is ready.</p>
                            ' . getCTA('Download Now', '{{download_link}}') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Order confirmed! Hi {{customer_name}}, order {{order_id}} for {{product_name}}. Download: {{download_link}}'
    ],
    // 21. Payment Receipt
    [
        'name' => 'Payment Receipt - Premium',
        'subject' => 'Payment Confirmed - {{order_id}} - {{customer_name}}',
        'category' => 'transactional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Payment Receipt</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#f8fafc;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f8fafc; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('🧾 Payment Receipt', 'Transaction Confirmed') . '
                        <div class="email-body">
                            <p>Hello {{customer_name}},</p>
                            <p>Your payment has been successfully processed.</p>
                            <div class="coupon-box" style="text-align:left;">
                                <p class="coupon-label">Payment Details</p>
                                <p><strong>Order ID:</strong> {{order_id}}</p>
                                <p><strong>Status:</strong> Paid</p>
                                <p><strong>Date:</strong> {{current_date}}</p>
                            </div>
                            ' . getCTA('View Order', 'https://digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Payment confirmed! Order {{order_id}}. Thank you for shopping with DigiWS, {{customer_name}}.'
    ],
    // 22. Order Shipped (Digital Delivery)
    [
        'name' => 'Order Shipped - Premium',
        'subject' => 'Your Digital Product is Ready - {{customer_name}}',
        'category' => 'transactional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Order Shipped</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#f0f9ff;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0f9ff; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('🚚 Your Order is Ready!', 'Instant Digital Delivery') . '
                        <div class="email-body" style="text-align:center;">
                            <p style="font-size:18px; color:#075985; font-weight:600;">Hi {{customer_name}},</p>
                            <p>Your digital product is ready for download.</p>
                            <div class="coupon-box" style="text-align:center;">
                                <p class="coupon-label">Download Link</p>
                                <p style="font-size:16px; word-break:break-all;"><a href="{{download_link}}" style="color:#2563eb; font-weight:700;">{{download_link}}</a></p>
                            </div>
                            ' . getCTA('Download Now', '{{download_link}}') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Your digital product is ready! Hi {{customer_name}}, download at {{download_link}}.'
    ],
    // 23. Order Cancelled
    [
        'name' => 'Order Cancelled - Premium',
        'subject' => 'Order Cancelled - {{order_id}} - {{customer_name}}',
        'category' => 'transactional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Order Cancelled</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#fef2f2;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fef2f2; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('❌ Order Cancelled', 'Order #{{order_id}}') . '
                        <div class="email-body">
                            <p>Hello {{customer_name}},</p>
                            <p>Your order <strong>{{order_id}}</strong> has been cancelled.</p>
                            <p>Need help? Contact DigiWS Support at <a href="mailto:support@digiws.com" style="color:#dc2626;">support@digiws.com</a></p>
                            ' . getCTA('Contact Support', 'mailto:support@digiws.com') . '
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Order {{order_id}} has been cancelled. Hi {{customer_name}}, contact support@digiws.com for help.'
    ],
    // 24. Password Reset
    [
        'name' => 'Password Reset - Premium',
        'subject' => 'Reset Your Password - {{customer_name}}',
        'category' => 'transactional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Password Reset</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#f8fafc;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f8fafc; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('🔒 Password Reset', 'Secure Your Account') . '
                        <div class="email-body" style="text-align:center;">
                            <p>Hi {{customer_name}},</p>
                            <p>We received a request to reset your password.</p>
                            <p>Click the button below to create a new password:</p>
                            ' . getCTA('Reset Password', '{{reset_link}}') . '
                            <p style="font-size:14px; color:#64748b;">This link will expire in 24 hours.</p>
                            <p style="font-size:14px; color:#64748b;">If you didn\'t request this, please ignore this email.</p>
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Password reset requested. Hi {{customer_name}}, click {{reset_link}} to reset. Expires in 24 hours.'
    ],
    // 25. Email Verification
    [
        'name' => 'Email Verification - Premium',
        'subject' => 'Verify Your Email - {{customer_name}}',
        'category' => 'transactional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Verify Email</title>' . getTemplateStyles() . '</head><body style="margin:0; padding:0; background-color:#f0fdf4;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0fdf4; padding:30px 0;">
                <tr><td align="center">
                    <div class="email-wrapper">
                        ' . getHeader('✅ Verify Your Email', 'Confirm Your Account') . '
                        <div class="email-body" style="text-align:center;">
                            <p>Hi {{customer_name}},</p>
                            <p>Thank you for signing up! Please verify your email address to activate your account.</p>
                            ' . getCTA('Verify Email Address', '{{verification_link}}') . '
                            <p style="font-size:14px; color:#10b981;">This link will expire in 48 hours.</p>
                        </div>
                        ' . getSocialIcons() . '
                        <hr class="divider">
                        ' . getFooter() . '
                    </div>
                </td></tr>
            </table>
        </body></html>',
        'body_text' => 'Verify your email. Hi {{customer_name}}, click {{verification_link}} to activate. Expires in 48 hours.'
    ]
];

$inserted = 0;
foreach ($templates as $template) {
    $db->insert('marketing_templates', [
        'name' => $template['name'],
        'subject' => $template['subject'],
        'body_html' => $template['body_html'],
        'body_text' => $template['body_text'],
        'category' => $template['category'],
        'is_active' => 1
    ]);
    $inserted++;
}

echo "Successfully added $inserted premium email templates!";
