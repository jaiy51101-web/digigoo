<?php
/**
 * Digiws - Front Controller
 * All requests are routed through this file
 */

define('BASE_PATH', __DIR__);

// Auto-redirect to installer if not installed yet
if (!file_exists(__DIR__ . '/.env') && file_exists(__DIR__ . '/install.php')) {
    header("Location: install.php");
    exit;
}

// Load core files
require_once BASE_PATH . '/includes/config.php';
require_once BASE_PATH . '/includes/Database.php';
require_once BASE_PATH . '/includes/helpers.php';
require_once BASE_PATH . '/includes/Router.php';
require_once BASE_PATH . '/includes/Security.php';

// Boot security layer (session, headers, rate limits, firewall)
Security::boot();

// Autoload models
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/models/' . $class . '.php',
        BASE_PATH . '/controllers/' . $class . '.php',
        BASE_PATH . '/includes/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Update cart count
updateCartCount();

// Initialize Router
$router = new Router(env('APP_BASE_PATH', '/'));

// ============================================
// PUBLIC ROUTES
// ============================================
$router->get('/', [HomeController::class, 'index']);
$router->get('/about', [HomeController::class, 'about']);
$router->get('/terms-and-conditions', [HomeController::class, 'terms']);
$router->get('/refund-policy', [HomeController::class, 'refundPolicy']);
$router->get('/disclaimer', [HomeController::class, 'disclaimer']);
$router->get('/contact', [HomeController::class, 'contact']);
$router->post('/contact', [HomeController::class, 'contactSubmit']);
$router->get('/services', [HomeController::class, 'services']);
$router->get('/page/{slug}', [HomeController::class, 'page']);
$router->get('/sitemap.xml', [SitemapController::class, 'index']);
$router->get('/sitemap-index.xml', [SitemapController::class, 'index']);
$router->get('/sitemap-{type}.xml', [SitemapController::class, 'show']);
$router->get('/sitemap', [HomeController::class, 'sitemapHtml']);
$router->get('/robots.txt', [SitemapController::class, 'robotsTxt']);

// Blog
$router->get('/blog', [BlogController::class, 'index']);
$router->get('/blog/category/{slug}', [BlogController::class, 'category']);
$router->get('/blog/tag/{slug}', [BlogController::class, 'tag']);
$router->get('/blog/author/{username}', [BlogController::class, 'author']);
$router->get('/blog/search', [BlogController::class, 'search']);
$router->get('/blog/archive', [BlogController::class, 'archive']);
$router->get('/blog/rss.xml', [BlogController::class, 'rss']);
$router->get('/blog/{slug}', [BlogController::class, 'show']);

// Blog AJAX
$router->post('/blog/comment/store', [BlogCommentController::class, 'store']);
$router->post('/api/blog/comment/like', [BlogCommentController::class, 'like']);
$router->post('/api/blog/bookmark/toggle', [BlogBookmarkController::class, 'toggle']);
$router->get('/api/blog/search', [BlogController::class, 'apiSearch']);
$router->post('/api/blog/rate', [BlogController::class, 'rate']);
$router->post('/api/blog/react', [BlogController::class, 'react']);

// Products
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/category/{slug}', [ProductController::class, 'byCategory']);
$router->get('/product/{slug}', [ProductController::class, 'show']);
$router->get('/search', [ProductController::class, 'search']);

// Auth
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/forgot-password', [AuthController::class, 'forgotForm']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->get('/reset-password/{token}', [AuthController::class, 'resetForm']);
$router->post('/reset-password', [AuthController::class, 'resetPassword']);

// Social Auth
$router->get('/auth/google/redirect', [AuthController::class, 'googleRedirect']);
$router->get('/auth/google/callback', [AuthController::class, 'googleCallback']);

// Cart
$router->get('/cart', [CartController::class, 'index']);
$router->get('/cart/add-support', [CartController::class, 'addSupport']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->post('/cart/coupon', [CartController::class, 'applyCoupon']);
$router->post('/cart/remove-coupon', [CartController::class, 'removeCoupon']);
$router->get('/checkout', [CartController::class, 'checkout']);
$router->post('/checkout/process', [CartController::class, 'processCheckout']);

// Payment
$router->post('/pay/razorpay/create', [PaymentController::class, 'razorpayCreate']);
$router->post('/pay/razorpay/verify', [PaymentController::class, 'razorpayVerify']);
$router->post('/pay/stripe/create', [PaymentController::class, 'stripeCreate']);
$router->post('/pay/stripe/verify', [PaymentController::class, 'stripeVerify']);
$router->post('/pay/paypal/create', [PaymentController::class, 'paypalCreate']);
$router->get('/pay/paypal/verify', [PaymentController::class, 'paypalVerify']);

// UPI Custom Gateway
$router->post('/pay/upi-custom/create', [PaymentController::class, 'upiCreate']);
$router->post('/pay/upi-custom/webhook', [PaymentController::class, 'upiWebhook']);
$router->get('/pay/upi-custom/status', [PaymentController::class, 'upiStatus']);
$router->post('/pay/upi-custom/status', [PaymentController::class, 'upiStatus']);

// UPI Direct (Scan & Pay)
$router->post('/pay/upi-direct/verify', [PaymentController::class, 'upiDirectVerify']);

$router->get('/pay/success', [PaymentController::class, 'success']);
$router->get('/pay/failed', [PaymentController::class, 'failed']);

// Invoice Verification
$router->get('/verify-invoice/{order_number}', [UserController::class, 'verifyInvoicePublic']);

// Newsletter
$router->post('/newsletter/subscribe', [HomeController::class, 'newsletterSubscribe']);

// Reviews
$router->post('/review/submit', [ProductController::class, 'submitReview']);

// ============================================
// USER DASHBOARD ROUTES
// ============================================
$router->get('/user/dashboard', [UserController::class, 'dashboard']);
$router->get('/user/orders', [UserController::class, 'orders']);
$router->get('/user/order/{id}', [UserController::class, 'orderDetail']);
$router->get('/user/orders/invoice/{id}', [UserController::class, 'invoice']);
$router->get('/user/downloads', [UserController::class, 'downloads']);
$router->get('/user/download/{id}', [UserController::class, 'downloadFile']);
$router->get('/user/profile', [UserController::class, 'profile']);
$router->post('/user/profile', [UserController::class, 'updateProfile']);
$router->get('/user/change-password', [UserController::class, 'changePasswordForm']);
$router->post('/user/change-password', [UserController::class, 'changePassword']);

// Website Requests
$router->get('/user/website-requests', [WebsiteRequestController::class, 'userIndex']);
$router->post('/website-request/store', [WebsiteRequestController::class, 'store']);

// ============================================
// ADMIN ROUTES
// ============================================
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/login', [AdminController::class, 'loginForm']);
$router->post('/admin/login', [AdminController::class, 'login']);
$router->get('/admin/logout', [AdminController::class, 'logout']);

// Admin Website Requests
$router->get('/admin/website-requests', [WebsiteRequestController::class, 'adminIndex']);
$router->post('/admin/website-requests/status/{id}', [WebsiteRequestController::class, 'updateStatus']);
$router->post('/admin/website-requests/delete/{id}', [WebsiteRequestController::class, 'delete']);

// Admin Products
$router->get('/admin/products', [AdminProductController::class, 'index']);
$router->get('/admin/products/create', [AdminProductController::class, 'create']);
$router->post('/admin/products/store', [AdminProductController::class, 'store']);
$router->get('/admin/products/edit/{id}', [AdminProductController::class, 'edit']);
$router->post('/admin/products/update/{id}', [AdminProductController::class, 'update']);
$router->post('/admin/products/delete/{id}', [AdminProductController::class, 'delete']);

// Admin Categories
$router->get('/admin/categories', [AdminCategoryController::class, 'index']);
$router->post('/admin/categories/store', [AdminCategoryController::class, 'store']);
$router->post('/admin/categories/update/{id}', [AdminCategoryController::class, 'update']);
$router->post('/admin/categories/delete/{id}', [AdminCategoryController::class, 'delete']);

// Admin Orders
$router->get('/admin/orders', [AdminOrderController::class, 'index']);
$router->get('/admin/orders/{id}', [AdminOrderController::class, 'show']);
$router->post('/admin/orders/status/{id}', [AdminOrderController::class, 'updateStatus']);
$router->get('/admin/orders/invoice/{id}', [AdminOrderController::class, 'invoice']);
$router->post('/admin/orders/delete/{id}', [AdminOrderController::class, 'delete']);

// Admin Coupons
$router->get('/admin/coupons', [AdminCouponController::class, 'index']);
$router->get('/admin/coupons/create', [AdminCouponController::class, 'create']);
$router->post('/admin/coupons/store', [AdminCouponController::class, 'store']);
$router->get('/admin/coupons/edit/{id}', [AdminCouponController::class, 'edit']);
$router->post('/admin/coupons/update/{id}', [AdminCouponController::class, 'update']);
$router->post('/admin/coupons/delete/{id}', [AdminCouponController::class, 'delete']);

// Admin Customers
$router->get('/admin/customers', [AdminUserController::class, 'index']);
$router->get('/admin/customers/{id}', [AdminUserController::class, 'show']);
$router->post('/admin/customers/status/{id}', [AdminUserController::class, 'updateStatus']);
$router->post('/admin/customers/{id}/delete', [AdminUserController::class, 'delete']);

// Admin Reviews
$router->get('/admin/reviews', [AdminReviewController::class, 'index']);
$router->post('/admin/reviews/feedback', [AdminReviewController::class, 'storeManaged']);
$router->post('/admin/reviews/feedback/delete/{id}', [AdminReviewController::class, 'deleteManaged']);
$router->post('/admin/reviews/status/{id}', [AdminReviewController::class, 'updateStatus']);
$router->post('/admin/reviews/delete/{id}', [AdminReviewController::class, 'delete']);
$router->post('/admin/reviews/fake-rules', [AdminReviewController::class, 'storeFakeRules']);
$router->post('/admin/reviews/fake-rules/delete/{id}', [AdminReviewController::class, 'deleteFakeRules']);

// Admin Email Templates
$router->get('/admin/email-templates', [AdminEmailController::class, 'index']);
$router->get('/admin/email-templates/edit/{id}', [AdminEmailController::class, 'edit']);
$router->post('/admin/email-templates/update/{id}', [AdminEmailController::class, 'update']);
$router->post('/admin/email-templates/test', [AdminEmailController::class, 'sendTestEmail']);

// Admin Settings
$router->get('/admin/payment-settings', [AdminPaymentController::class, 'index']);
$router->post('/admin/payment-settings/save', [AdminPaymentController::class, 'save']);
$router->post('/admin/payment-settings/check', [AdminPaymentController::class, 'checkConnection']);
$router->get('/admin/settings', [AdminSettingController::class, 'index']);
$router->post('/admin/settings/update', [AdminSettingController::class, 'update']);
$router->post('/admin/settings/logo', [AdminSettingController::class, 'uploadLogo']);
$router->get('/admin/sitemap/generate', [AdminSettingController::class, 'generateSitemap']);
$router->get('/admin/sitemap/clear-cache', [AdminSettingController::class, 'clearSitemapCache']);

// Admin Update Center (v5.6)
$router->get('/admin/updates', [AdminUpdateController::class, 'index']);
$router->get('/admin/updates/check', [AdminUpdateController::class, 'check']);
$router->get('/admin/updates/download-remote', [AdminUpdateController::class, 'downloadRemote']);
$router->post('/admin/updates/upload', [AdminUpdateController::class, 'upload']);
$router->post('/admin/updates/save-license', [AdminUpdateController::class, 'saveLicense']);

// Admin Marketing System
$router->get('/admin/marketing/dashboard', [AdminMarketingController::class, 'dashboard']);
$router->post('/admin/marketing/crm/data', [AdminMarketingController::class, 'crmData']);
$router->post('/admin/marketing/crm/action', [AdminMarketingController::class, 'crmAction']);
$router->post('/admin/marketing/crm/timeline', [AdminMarketingController::class, 'crmTimeline']);
$router->post('/admin/marketing/crm/bulk-action', [AdminMarketingController::class, 'crmBulkAction']);
$router->post('/admin/marketing/crm/delete', [AdminMarketingController::class, 'crmDelete']);
$router->post('/admin/marketing/crm/send-test-email', [AdminMarketingController::class, 'crmSendTestEmail']);
$router->get('/admin/marketing/crm/export', [AdminMarketingController::class, 'crmExport']);
$router->get('/admin/marketing/audience-preview', [AdminMarketingController::class, 'audiencePreview']);
$router->get('/admin/marketing/subscribers', [AdminMarketingController::class, 'subscribers']);
$router->get('/admin/marketing/sync', [AdminMarketingController::class, 'sync']);
$router->post('/admin/marketing/lists/store', [AdminMarketingController::class, 'listStore']);
$router->get('/admin/marketing/lists/delete/{id}', [AdminMarketingController::class, 'listDelete']);
$router->post('/admin/marketing/segments/store', [AdminMarketingController::class, 'segmentStore']);
$router->get('/admin/marketing/segments/delete/{id}', [AdminMarketingController::class, 'segmentDelete']);
$router->get('/admin/marketing/smtps', [AdminMarketingController::class, 'smtps']);
$router->post('/admin/marketing/smtps/store', [AdminMarketingController::class, 'smtpStore']);
$router->get('/admin/marketing/smtps/delete/{id}', [AdminMarketingController::class, 'smtpDelete']);
$router->get('/admin/marketing/smtps/edit/{id}', [AdminMarketingController::class, 'smtpEdit']);
$router->post('/admin/marketing/smtps/update/{id}', [AdminMarketingController::class, 'smtpUpdate']);
$router->get('/admin/marketing/smtps/test/{id}', [AdminMarketingController::class, 'smtpTest']);
$router->post('/admin/marketing/smtps/send-test/{id}', [AdminMarketingController::class, 'smtpSendTest']);

// Templates & Builder (Module 3)
$router->get('/admin/marketing/templates', [AdminMarketingController::class, 'templates']);
$router->post('/admin/marketing/templates/store', [AdminMarketingController::class, 'templateStore']);
$router->get('/admin/marketing/templates/edit/{id}', [AdminMarketingController::class, 'templateEdit']);
$router->post('/admin/marketing/templates/update/{id}', [AdminMarketingController::class, 'templateUpdate']);
$router->post('/admin/marketing/templates/delete/{id}', [AdminMarketingController::class, 'templateDelete']);
$router->get('/admin/marketing/templates/builder/{id}', [AdminMarketingController::class, 'builder']);
$router->post('/admin/marketing/templates/save-builder/{id}', [AdminMarketingController::class, 'saveBuilder']);

// Contact Messages / Complaints
$router->get('/admin/contact-messages', [AdminContactController::class, 'index']);
$router->post('/admin/contact-messages/mark-read/{id}', [AdminContactController::class, 'markRead']);
$router->post('/admin/contact-messages/delete/{id}', [AdminContactController::class, 'delete']);

// Campaigns & A/B Testing (Module 4)
$router->get('/admin/marketing/campaigns', [AdminMarketingController::class, 'campaigns']);
$router->post('/admin/marketing/campaigns/store', [AdminMarketingController::class, 'campaignStore']);
$router->get('/admin/marketing/campaigns/delete/{id}', [AdminMarketingController::class, 'campaignDelete']);
$router->get('/admin/marketing/campaigns/send/{id}', [AdminMarketingController::class, 'campaignSend']);
$router->get('/admin/marketing/campaigns/resend-failed/{id}', [AdminMarketingController::class, 'campaignResendFailed']);
$router->get('/admin/marketing/campaigns/resend-all/{id}', [AdminMarketingController::class, 'campaignResendAll']);
$router->get('/admin/marketing/campaigns/stats/{id}', [AdminMarketingController::class, 'campaignStats']);
$router->post('/admin/marketing/campaigns/send-one/{id}', [AdminMarketingController::class, 'campaignSendOne']);
$router->get('/admin/marketing/queue/process', [AdminMarketingController::class, 'processQueue']);

// WhatsApp Campaigns
$router->get('/admin/marketing/whatsapp-campaign', [AdminMarketingController::class, 'whatsappCampaign']);
$router->post('/admin/marketing/whatsapp-campaign/send', [AdminMarketingController::class, 'whatsappCampaignSend']);

// Automations (Module 5)
$router->get('/admin/marketing/automations', [AdminMarketingController::class, 'automations']);
$router->post('/admin/marketing/automations/store', [AdminMarketingController::class, 'automationStore']);
$router->get('/admin/marketing/automations/delete/{id}', [AdminMarketingController::class, 'automationDelete']);
$router->get('/admin/marketing/automations/broadcast/{id}', [AdminMarketingController::class, 'automationBroadcast']);

// Admin Blog Posts
$router->get('/admin/blog', [AdminBlogController::class, 'index']);
$router->get('/admin/blog/create', [AdminBlogController::class, 'create']);
$router->post('/admin/blog/store', [AdminBlogController::class, 'store']);
$router->get('/admin/blog/edit/{id}', [AdminBlogController::class, 'edit']);
$router->post('/admin/blog/update/{id}', [AdminBlogController::class, 'update']);
$router->post('/admin/blog/delete/{id}', [AdminBlogController::class, 'delete']);
$router->post('/admin/blog/bulk-action', [AdminBlogController::class, 'bulkAction']);
$router->get('/admin/blog/analytics', [AdminBlogController::class, 'analytics']);

// Advanced Block-Based Blog Editor (Gutenberg-like)
$router->get('/admin/blog-editor/create', [BlogEditorController::class, 'editor']);
$router->get('/admin/blog-editor/edit/{id}', [BlogEditorController::class, 'editor']);
$router->post('/blog-editor/add-block', [BlogEditorController::class, 'apiAddBlock']);
$router->post('/blog-editor/update-block', [BlogEditorController::class, 'apiUpdateBlock']);
$router->post('/blog-editor/delete-block', [BlogEditorController::class, 'apiDeleteBlock']);
$router->post('/blog-editor/reorder-blocks', [BlogEditorController::class, 'apiReorderBlocks']);
$router->post('/blog-editor/upload-media', [BlogEditorController::class, 'apiUploadMedia']);
$router->get('/blog-editor/media', [BlogEditorController::class, 'apiGetMedia']);
$router->post('/blog-editor/autosave', [BlogEditorController::class, 'apiAutosave']);
$router->get('/blog-editor/seo', [BlogEditorController::class, 'apiAnalyzeSEO']);
$router->get('/blog-editor/revisions', [BlogEditorController::class, 'apiGetRevisions']);
$router->post('/blog-editor/restore', [BlogEditorController::class, 'apiRestoreRevision']);
$router->post('/blog-editor/ai-generate', [BlogEditorController::class, 'apiAIGenerate']);
$router->post('/blog-editor/save', [BlogEditorController::class, 'apiSavePost']);

// Admin Blog Categories
$router->get('/admin/blog/categories', [AdminBlogCategoryController::class, 'index']);
$router->post('/admin/blog/categories/store', [AdminBlogCategoryController::class, 'store']);
$router->post('/admin/blog/categories/update/{id}', [AdminBlogCategoryController::class, 'update']);
$router->post('/admin/blog/categories/delete/{id}', [AdminBlogCategoryController::class, 'delete']);

// Admin Blog Tags
$router->get('/admin/blog/tags', [AdminBlogTagController::class, 'index']);
$router->post('/admin/blog/tags/store', [AdminBlogTagController::class, 'store']);
$router->post('/admin/blog/tags/update/{id}', [AdminBlogTagController::class, 'update']);
$router->post('/admin/blog/tags/delete/{id}', [AdminBlogTagController::class, 'delete']);
$router->post('/admin/blog/tags/bulk-import', [AdminBlogTagController::class, 'bulkImport']);

// Admin Blog Comments
$router->get('/admin/blog/comments', [AdminBlogController::class, 'comments']);
$router->post('/admin/blog/comments/status/{id}', [AdminBlogController::class, 'commentStatus']);
$router->post('/admin/blog/comments/delete/{id}', [AdminBlogController::class, 'commentDelete']);


// API/AJAX Routes
$router->get('/api/products/search', [ApiController::class, 'searchProducts']);
$router->get('/api/cart/count', [ApiController::class, 'cartCount']);
$router->get('/api/admin/stats', [ApiController::class, 'adminStats']);
$router->get('/api/product/quickview/{id}', [ProductController::class, 'quickView']);

// Wishlist
$router->get('/wishlist', [WishlistController::class, 'index']);
$router->post('/api/wishlist/toggle', [WishlistController::class, 'toggle']);

// Dispatch request
$router->dispatch();
