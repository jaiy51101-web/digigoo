<?php
/**
 * DigiWS - Email Content Design Templates
 * Run once to add beautifully designed email content to existing templates
 * Access: http://localhost/digiws/database/seed_email_content.php
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';

// Allow CLI and admin-only access
if (php_sapi_name() !== 'cli') {
    requireAdmin();
}

$db = Database::getInstance();

// Check if content designs already exist
$existingCount = $db->fetchColumn("SELECT COUNT(*) FROM marketing_templates WHERE body_html LIKE '<!DOCTYPE html>'");
if ($existingCount > 20) {
    die('Email content designs already exist. Skipping seed.');
}

$contentDesigns = [
    [
        'name' => 'Welcome Email - Modern Purple',
        'subject' => 'Welcome to DigiWS! Here is your 10% welcome discount',
        'category' => 'welcome',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Welcome</title><style>body{margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,sans-serif;}.container{max-width:600px;margin:0 auto;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.1);}.header{background:linear-gradient(135deg,#7c3aed,#3b82f6);padding:40px 20px;text-align:center;color:#ffffff;}.header h1{margin:0;font-size:28px;font-weight:700;}.content{padding:40px 30px;}.welcome-text{font-size:18px;color:#333333;line-height:1.6;margin-bottom:20px;}.discount-box{background:linear-gradient(135deg,#fef3c7,#fde68a);border:2px dashed #f59e0b;border-radius:12px;padding:25px;text-align:center;margin:25px 0;}.discount-code{font-size:32px;font-weight:800;color:#92400e;letter-spacing:3px;margin:10px 0;}.button{display:inline-block;padding:14px 35px;background:linear-gradient(135deg,#7c3aed,#3b82f6);color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;font-size:16px;margin:20px 0;}.footer{background-color:#f8fafc;padding:20px;text-align:center;color:#64748b;font-size:12px;}</style></head><body><div class="container"><div class="header"><h1>Welcome to DigiWS!</h1></div><div class="content"><p class="welcome-text">Hi {{customer_name}},</p><p class="welcome-text">Thank you for joining us! We are excited to have you on board. As a special welcome gift, here is your exclusive 10% discount on your first order.</p><div class="discount-box"><p style="margin:0;font-weight:600;color:#92400e;">Your Welcome Discount Code</p><div class="discount-code">WELCOME10</div><p style="margin:5px 0 0;color:#b45309;font-size:14px;">Use this code at checkout</p></div><a href="{{unsubscribe}}" class="button">Start Shopping Now</a><p class="welcome-text">If you have any questions, feel free to reach out to our support team.</p></div><div class="footer"><p>© 2024 DigiWS. All rights reserved.</p><p style="margin-top:10px;"><a href="{{unsubscribe}}" style="color:#7c3aed;text-decoration:none;">Unsubscribe</a></p></div></div></body></html>',
        'body_text' => 'Welcome to DigiWS! Use code WELCOME10 for 10% off your first order.'
    ],
    [
        'name' => 'Exclusive Offer - Premium Dark',
        'subject' => 'Exclusive Offer Just for You! Up to 50% Off Premium Products',
        'category' => 'promotional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Exclusive Offer</title><style>body{margin:0;padding:0;background-color:#0f172a;font-family:Arial,sans-serif;}.container{max-width:600px;margin:0 auto;background-color:#1e293b;border-radius:12px;overflow:hidden;}.header{background:linear-gradient(135deg,#7c3aed,#ec4899);padding:50px 20px;text-align:center;}.header h1{margin:0;font-size:32px;font-weight:800;color:#ffffff;text-shadow:0 2px 4px rgba(0,0,0,0.2);}.content{padding:40px 30px;color:#e2e8f0;}.offer-badge{background:linear-gradient(135deg,#fbbf24,#f59e0b);color:#0f172a;padding:15px 30px;border-radius:50px;display:inline-block;font-weight:800;font-size:18px;margin:20px 0;text-transform:uppercase;letter-spacing:2px;}.button{display:inline-block;padding:16px 40px;background:linear-gradient(135deg,#7c3aed,#ec4899);color:#ffffff;text-decoration:none;border-radius:10px;font-weight:700;font-size:18px;margin:25px 0;box-shadow:0 4px 15px rgba(124,58,237,0.4);}.footer{background-color:#0f172a;padding:25px;text-align:center;color:#94a3b8;font-size:12px;}</style></head><body><div class="container"><div class="header"><h1>Exclusive Offer</h1></div><div class="content" style="text-align:center;"><p style="font-size:18px;color:#cbd5e1;">Hi {{customer_name}},</p><p style="font-size:16px;line-height:1.6;">We have curated an exclusive collection just for you. Enjoy premium products at unbeatable prices.</p><div class="offer-badge">Up to 50% Off</div><a href="{{unsubscribe}}" class="button">Claim Your Offer</a><p style="color:#94a3b8;font-size:14px;">Limited time only. Offer valid while supplies last.</p></div><div class="footer"><p>© 2024 DigiWS. All rights reserved.</p><p style="margin-top:10px;"><a href="{{unsubscribe}}" style="color:#7c3aed;text-decoration:none;">Unsubscribe</a></p></div></div></body></html>',
        'body_text' => 'Exclusive Offer Just for You! Up to 50% Off Premium Products. Limited time only.'
    ],
    [
        'name' => 'Summer Sale - Hot Deals',
        'subject' => 'Summer Sale is Here! Get 30% Off on All Items',
        'category' => 'promotional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Summer Sale</title><style>body{margin:0;padding:0;background-color:#fff7ed;font-family:Arial,sans-serif;}.container{max-width:600px;margin:0 auto;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);}.header{background:linear-gradient(135deg,#f97316,#fb923c);padding:45px 20px;text-align:center;position:relative;}.header h1{margin:0;font-size:36px;font-weight:800;color:#ffffff;text-shadow:0 2px 4px rgba(0,0,0,0.1);}.sun-icon{font-size:60px;margin-bottom:10px;}.content{padding:35px 30px;text-align:center;}.sale-text{font-size:20px;color:#9a3412;font-weight:700;margin:15px 0;}.button{display:inline-block;padding:16px 40px;background:linear-gradient(135deg,#f97316,#ea580c);color:#ffffff;text-decoration:none;border-radius:10px;font-weight:700;font-size:18px;margin:20px 0;box-shadow:0 4px 15px rgba(249,115,22,0.4);}.footer{background-color:#fff7ed;padding:25px;text-align:center;color:#9a3412;font-size:12px;}</style></head><body><div class="container"><div class="header"><div class="sun-icon">☀️</div><h1>Summer Sale</h1></div><div class="content"><p style="font-size:18px;color:#c2410c;font-weight:600;">Hi {{customer_name}},</p><p class="sale-text">30% OFF on ALL items!</p><p style="color:#9a3412;font-size:16px;line-height:1.6;">Dive into our summer collection and enjoy amazing discounts. Beach ready styles, summer essentials, and more!</p><a href="{{unsubscribe}}" class="button">Shop Summer Sale</a><p style="color:#c2410c;font-size:14px;font-weight:600;">Sale ends soon. Don not miss out!</p></div><div class="footer"><p>© 2024 DigiWS. All rights reserved.</p><p style="margin-top:10px;"><a href="{{unsubscribe}}" style="color:#f97316;text-decoration:none;">Unsubscribe</a></p></div></div></body></html>',
        'body_text' => 'Summer Sale is Here! Get 30% Off on All Items. Limited time offer.'
    ],
    [
        'name' => '20% Off Big Deal',
        'subject' => 'Big Deal Alert! 20% Off Your Next Purchase',
        'category' => 'promotional',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>20% Off</title><style>body{margin:0;padding:0;background-color:#fef2f2;font-family:Arial,sans-serif;}.container{max-width:600px;margin:0 auto;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);}.header{background:linear-gradient(135deg,#dc2626,#ef4444);padding:40px 20px;text-align:center;}.header h1{margin:0;font-size:32px;font-weight:800;color:#ffffff;}.percentage{font-size:80px;font-weight:900;color:#dc2626;text-align:center;margin:20px 0;text-shadow:0 4px 8px rgba(220,38,38,0.2);}.content{padding:35px 30px;text-align:center;}.deal-text{font-size:18px;color:#991b1b;font-weight:600;margin:15px 0;}.button{display:inline-block;padding:16px 40px;background:linear-gradient(135deg,#dc2626,#b91c1c);color:#ffffff;text-decoration:none;border-radius:10px;font-weight:700;font-size:18px;margin:20px 0;box-shadow:0 4px 15px rgba(220,38,38,0.4);}.footer{background-color:#fef2f2;padding:25px;text-align:center;color:#991b1b;font-size:12px;}</style></head><body><div class="container"><div class="header"><h1>Big Deal Alert!</h1></div><div class="content"><div class="percentage">20% OFF</div><p style="font-size:18px;color:#b91c1c;font-weight:600;">Hi {{customer_name}},</p><p class="deal-text">Do not miss this amazing deal! Get 20% off on your next purchase.</p><a href="{{unsubscribe}}" class="button">Claim Your 20% Off</a><p style="color:#991b1b;font-size:14px;">Use code: BIGDEAL20 at checkout</p></div><div class="footer"><p>© 2024 DigiWS. All rights reserved.</p><p style="margin-top:10px;"><a href="{{unsubscribe}}" style="color:#dc2626;text-decoration:none;">Unsubscribe</a></p></div></div></body></html>',
        'body_text' => 'Big Deal Alert! 20% Off Your Next Purchase. Use code: BIGDEAL20'
    ],
    [
        'name' => 'New User Welcome Series',
        'subject' => 'You are In! Let us Get Started with Your Account',
        'category' => 'welcome',
        'body_html' => '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Welcome Series</title><style>body{margin:0;padding:0;background-color:#f0fdf4;font-family:Arial,sans-serif;}.container{max-width:600px;margin:0 auto;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);}.header{background:linear-gradient(135deg,#10b981,#059669);padding:40px 20px;text-align:center;}.header h1{margin:0;font-size:28px;font-weight:800;color:#ffffff;}.checkmark{font-size:60px;margin-bottom:15px;}.content{padding:35px 30px;}.welcome-text{font-size:16px;color:#065f46;line-height:1.7;margin-bottom:15px;}.feature-list{background:#f0fdf4;border-radius:10px;padding:20px;margin:20px 0;}.feature-item{padding:10px 0;border-bottom:1px solid #bbf7d0;}.feature-item:last-child{border-bottom:none;}.button{display:inline-block;padding:14px 35px;background:linear-gradient(135deg,#10b981,#059669);color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;font-size:16px;margin:20px 0;}.footer{background-color:#f0fdf4;padding:25px;text-align:center;color:#065f46;font-size:12px;}</style></head><body><div class="container"><div class="header"><div class="checkmark">✓</div><h1>You Are In!</h1></div><div class="content"><p class="welcome-text">Hi {{customer_name}},</p><p class="welcome-text">Welcome to the DigiWS family! Your account has been created successfully. Here is what you can do next:</p><div class="feature-list"><div class="feature-item">✓ Browse our premium product catalog</div><div class="feature-item">✓ Get exclusive member-only discounts</div><div class="feature-item">✓ Track your orders in real-time</div><div class="feature-item">✓ Earn rewards on every purchase</div></div><a href="{{unsubscribe}}" class="button">Explore Now</a></div><div class="footer"><p>© 2024 DigiWS. All rights reserved.</p><p style="margin-top:10px;"><a href="{{unsubscribe}}" style="color:#10b981;text-decoration:none;">Unsubscribe</a></p></div></div></body></html>',
        'body_text' => 'Welcome to DigiWS! Your account is ready. Explore premium products and earn rewards.'
    ]
];

foreach ($contentDesigns as $design) {
    $db->insert('marketing_templates', [
        'name' => $design['name'],
        'subject' => $design['subject'],
        'body_html' => $design['body_html'],
        'body_text' => $design['body_text'],
        'category' => $design['category'],
        'is_active' => 1
    ]);
}

echo "Successfully added " . count($contentDesigns) . " email content design templates!";
