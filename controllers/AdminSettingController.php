<?php
/**
 * Digiws - Admin Settings Controller
 */

class AdminSettingController {
    public function __construct() { requireAdmin(); }

    public function index(): void {
        $settingModel = new Setting();
        $settings = $settingModel->getAll();
        
        $sitemapService = new SitemapService();
        $sitemapStats = $sitemapService->getStats();
        
        $pageTitle = 'Settings - Admin';
        $adminPage = 'settings';
        $contentView = VIEWS_PATH . '/admin/settings.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function update(): void {
        if (!verifyCsrf()) { setFlash('danger', 'Invalid request.'); redirect(url('admin/settings')); }

        $settingModel = new Setting();
        
        $groupMapping = [
            'site_name' => 'general',
            'site_tagline' => 'general',
            'site_email' => 'general',
            'site_phone' => 'general',
            'site_address' => 'general',
            'meta_title' => 'seo',
            'meta_description' => 'seo',
            'meta_keywords' => 'seo',
            'seo_google_search_console' => 'seo',
            'seo_google_analytics' => 'seo',
            'seo_google_adsense' => 'seo',
            'seo_header_code' => 'seo',
            'seo_footer_code' => 'seo',
            'currency' => 'payment',
            'currency_symbol' => 'payment',
            'razorpay_key_id' => 'payment',
            'razorpay_key_secret' => 'payment',
            'stripe_publishable_key' => 'payment',
            'stripe_secret_key' => 'payment',
            'paypal_client_id' => 'payment',
            'paypal_secret' => 'payment',
            'paypal_mode' => 'payment',
            'upi_id' => 'payment',
            'upi_merchant_name' => 'payment',
            'payment_upi_direct_enabled' => 'payment',
            'smtp_host' => 'email',
            'smtp_port' => 'email',
            'smtp_username' => 'email',
            'smtp_password' => 'email',
            'smtp_encryption' => 'email',
            'smtp_from_email' => 'email',
            'smtp_from_name' => 'email',
            'whatsapp_number' => 'support',
            'whatsapp_message' => 'support',
            'whatsapp_enabled' => 'whatsapp',
            'whatsapp_api_key' => 'whatsapp',
            'whatsapp_sender' => 'whatsapp',
            'whatsapp_order_completed_msg' => 'whatsapp',
            'whatsapp_order_status_msg' => 'whatsapp',
            'whatsapp_footer_text' => 'whatsapp',
            'google_client_id' => 'social',
            'google_client_secret' => 'social',
            'facebook_app_id' => 'social',
            'facebook_app_secret' => 'social',
            'privacy_policy' => 'pages',
            'terms_conditions' => 'pages',
            'refund_policy' => 'pages',
            'after_payment_status' => 'orders',
            'downloadable_statuses' => 'orders',
            'sitemap_auto_generation' => 'sitemap',
            'sitemap_include_images' => 'sitemap',
            'sitemap_include_videos' => 'sitemap',
            'sitemap_include_news' => 'sitemap',
            'sitemap_ping_engines' => 'sitemap'
        ];

        foreach ($groupMapping as $key => $group) {
            if ($key === 'payment_upi_direct_enabled') {
                $value = isset($_POST['payment_upi_direct_enabled']) ? 'Yes' : 'No';
                $settingModel->set($key, $value, $group);
            } elseif ($key === 'whatsapp_enabled') {
                $value = isset($_POST['whatsapp_enabled']) ? 'Yes' : 'No';
                $settingModel->set($key, $value, $group);
            } elseif (isset($_POST[$key])) {
                $value = $_POST[$key];
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                $settingModel->set($key, $value, $group);
            }
        }

        setFlash('success', 'Settings updated successfully.');
        redirect(url('admin/settings'));
    }

    public function uploadLogo(): void {
        if (!verifyCsrf()) { setFlash('danger', 'Invalid request.'); redirect(url('admin/settings')); }

        $settingModel = new Setting();
        $errors = [];

        if (!empty($_FILES['site_logo']['name'])) {
            $file = $_FILES['site_logo'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Logo upload failed. PHP error code: " . $file['error'];
            } else {
                if (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $realMime = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);
                } else {
                    $realMime = mime_content_type($file['tmp_name']) ?: $file['type'];
                }
                
                $logo = uploadFile($file, 'site', [
                    'image/jpeg', 'image/png', 'image/webp', 'image/gif', 
                    'image/svg+xml', 'text/xml', 'text/plain', 
                    'image/x-icon', 'image/vnd.microsoft.icon', 'image/x-ico',
                    'image/svg'
                ]);
                
                if ($logo) {
                    $settingModel->set('site_logo', $logo, 'general');
                } else {
                    $errors[] = "Logo upload rejected. Detected MIME: '" . $realMime . "'. Ext: '" . pathinfo($file['name'], PATHINFO_EXTENSION) . "'";
                }
            }
        }

        if (!empty($_FILES['site_favicon']['name'])) {
            $file = $_FILES['site_favicon'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = "Favicon upload failed. PHP error code: " . $file['error'];
            } else {
                if (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $realMime = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);
                } else {
                    $realMime = mime_content_type($file['tmp_name']) ?: $file['type'];
                }
                
                $favicon = uploadFile($file, 'site', [
                    'image/x-icon', 'image/png', 'image/svg+xml', 'image/jpeg', 'image/webp',
                    'image/vnd.microsoft.icon', 'image/x-ico', 'text/xml', 'text/plain',
                    'image/svg'
                ]);
                
                if ($favicon) {
                    $settingModel->set('site_favicon', $favicon, 'general');
                } else {
                    $errors[] = "Favicon upload rejected. Detected MIME: '" . $realMime . "'";
                }
            }
        }

        if (!empty($errors)) {
            setFlash('danger', implode('<br>', $errors));
        } else {
            setFlash('success', 'Logo/Favicon uploaded successfully.');
        }
        redirect(url('admin/settings'));
    }

    public function generateSitemap(): void {
        $sitemapService = new SitemapService();
        $result = $sitemapService->generate();
        if ($result) {
            setFlash('success', 'Sitemap updated successfully! Statically compiled sitemap.xml generated in the root directory.');
        } else {
            setFlash('danger', 'Failed to generate sitemap. Check logs for details.');
        }
        redirect(url('admin/settings'));
    }

    public function clearSitemapCache(): void {
        $sitemapService = new SitemapService();
        $sitemapService->clearCache();
        setFlash('success', 'Sitemap cache cleared successfully.');
        redirect(url('admin/settings'));
    }
}
