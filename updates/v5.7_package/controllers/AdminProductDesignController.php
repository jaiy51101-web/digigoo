<?php
/**
 * Digiws - Admin Product View Design Controller
 */

class AdminProductDesignController {

    public function __construct() {
        requireAdmin();
    }

    public function index(): void {
        $settingModel = new Setting();
        
        // Default product design configuration
        $defaultDesign = [
            'card_theme'           => 'classic',
            'show_discount_badge'  => '1',
            'show_status_badge'    => '1',
            'show_wishlist_btn'    => '1',
            'show_quickview'       => '1',
            'show_live_preview'    => '1',
            'show_rating'          => '1',
            'show_sales_count'     => '1',
            'show_mrp_price'       => '1',
            'card_border_radius'   => '16',
            'card_aspect_ratio'    => '16/10',
            'card_shadow'          => 'subtle',
            'btn_style'            => 'rounded_pill',
            'accent_color'         => '#6366f1',
        ];

        $savedJson = $settingModel->get('product_card_design', '');
        $design = !empty($savedJson) ? json_decode($savedJson, true) : [];
        $design = array_merge($defaultDesign, is_array($design) ? $design : []);

        $pageTitle = 'Product View Design Customizer - Admin';
        $adminPage = 'product_design';
        $contentView = VIEWS_PATH . '/admin/product-design.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function update(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid security token.');
            redirect(url('admin/product-design'));
        }

        $settingModel = new Setting();

        $design = [
            'card_theme'          => sanitize($_POST['card_theme'] ?? 'classic'),
            'show_discount_badge' => isset($_POST['show_discount_badge']) ? '1' : '0',
            'show_status_badge'   => isset($_POST['show_status_badge']) ? '1' : '0',
            'show_wishlist_btn'   => isset($_POST['show_wishlist_btn']) ? '1' : '0',
            'show_quickview'      => isset($_POST['show_quickview']) ? '1' : '0',
            'show_live_preview'   => isset($_POST['show_live_preview']) ? '1' : '0',
            'show_rating'         => isset($_POST['show_rating']) ? '1' : '0',
            'show_sales_count'    => isset($_POST['show_sales_count']) ? '1' : '0',
            'show_mrp_price'      => isset($_POST['show_mrp_price']) ? '1' : '0',
            'card_border_radius'  => sanitize($_POST['card_border_radius'] ?? '16'),
            'card_aspect_ratio'   => sanitize($_POST['card_aspect_ratio'] ?? '16/10'),
            'card_shadow'         => sanitize($_POST['card_shadow'] ?? 'subtle'),
            'btn_style'           => sanitize($_POST['btn_style'] ?? 'rounded_pill'),
            'accent_color'        => sanitize($_POST['accent_color'] ?? '#6366f1'),
        ];

        $settingModel->set('product_card_design', json_encode($design), 'design');

        setFlash('success', 'Product card design settings updated successfully.');
        redirect(url('admin/product-design'));
    }
}
