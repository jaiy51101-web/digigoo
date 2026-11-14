<?php
$hasDiscount = (!empty($product['mrp']) && !empty($product['sale_price']) && $product['mrp'] > $product['sale_price']);
$videoUrl = $product['video_url'] ?? '';
$previewUrl = !empty($product['preview_url']) ? $product['preview_url'] : $videoUrl;
$hasLivePreview = !empty($previewUrl);

// Helper for sales count display
if (!function_exists('getDynamicFakeSales')) {
    function getDynamicFakeSales($p) {
        if (!empty($p['total_sales']) && $p['total_sales'] > 0) {
            return (int)$p['total_sales'];
        }
        return (int)(($p['id'] * 17) % 850 + 50);
    }
}

$displaySales = getDynamicFakeSales($product);
$formattedSales = $displaySales >= 1000 ? number_format($displaySales / 1000, 1) . 'k' : $displaySales;
$discountPercent = ($hasDiscount && (float)$product['mrp'] > 0) ? round((($product['mrp'] - $product['sale_price']) / $product['mrp']) * 100) : 0;

// Fetch Admin Product Card Design Settings
if (!isset($cardDesignConfig)) {
    try {
        $settingModelForCard = new Setting();
        $savedCardDesignJson = $settingModelForCard->get('product_card_design', '');
        $cardDesignConfig = !empty($savedCardDesignJson) ? json_decode($savedCardDesignJson, true) : [];
    } catch (\Throwable $e) {
        $cardDesignConfig = [];
    }
}

$showStatusBadge   = ($cardDesignConfig['show_status_badge'] ?? '1') === '1';
$showDiscountBadge = ($cardDesignConfig['show_discount_badge'] ?? '1') === '1';
$showWishlistBtn   = ($cardDesignConfig['show_wishlist_btn'] ?? '1') === '1';
$showQuickView     = ($cardDesignConfig['show_quickview'] ?? '1') === '1';
$showLivePreview   = ($cardDesignConfig['show_live_preview'] ?? '1') === '1';
$showRating        = ($cardDesignConfig['show_rating'] ?? '1') === '1';
$showSalesCount    = ($cardDesignConfig['show_sales_count'] ?? '1') === '1';
$showMrpPrice      = ($cardDesignConfig['show_mrp_price'] ?? '1') === '1';
$cardBorderRadius  = (int)($cardDesignConfig['card_border_radius'] ?? 16);
$cardAspectRatio   = $cardDesignConfig['card_aspect_ratio'] ?? '16/10';
$cardTheme         = $cardDesignConfig['card_theme'] ?? 'classic';
$cardShadow        = $cardDesignConfig['card_shadow'] ?? 'subtle';
$btnStyle          = $cardDesignConfig['btn_style'] ?? 'rounded_pill';
$accentColor       = $cardDesignConfig['accent_color'] ?? '#6366f1';
?>

<?php if (!defined('PRODUCT_CARD_CSS_LOADED')): define('PRODUCT_CARD_CSS_LOADED', true); ?>
<style>
/* =========================================================
   DIGIWS UNIVERSAL PRODUCT CARD ENGINE (10 THEME PRESETS)
   ========================================================= */
:root {
    --card-accent-default: #6366f1;
}

.dc-product-card {
    --accent: var(--user-card-accent, #6366f1);
    background: #ffffff;
    border: 1px solid #e2e8f0;
    transition: all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Shadow Styles */
.dc-product-card.shadow-none { box-shadow: none !important; }
.dc-product-card.shadow-subtle { box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05) !important; }
.dc-product-card.shadow-medium { box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important; }
.dc-product-card.shadow-heavy { box-shadow: 0 20px 40px rgba(0, 0, 0, 0.14) !important; }
.dc-product-card.shadow-glow { box-shadow: 0 8px 30px rgba(99, 102, 241, 0.2) !important; }

/* 1. CLASSIC THEME */
.dc-product-card.theme-classic:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12) !important;
    border-color: rgba(99, 102, 241, 0.3);
}

/* 2. BROWSER SAAS FRAME THEME (IMAGE 1 INSPIRED) */
.dc-product-card.theme-browser_frame {
    border: 2px solid #cbd5e1;
    background: #ffffff;
}
.dc-product-card.theme-browser_frame .browser-header-bar {
    background: #0f172a;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    z-index: 5;
}
.dc-product-card.theme-browser_frame .browser-dots {
    display: flex;
    gap: 6px;
}
.dc-product-card.theme-browser_frame .browser-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}
.dc-product-card.theme-browser_frame .browser-dot.dot-red { background: #ef4444; }
.dc-product-card.theme-browser_frame .browser-dot.dot-yellow { background: #f59e0b; }
.dc-product-card.theme-browser_frame .browser-dot.dot-green { background: #10b981; }
.dc-product-card.theme-browser_frame .browser-pill-badge {
    background: rgba(255,255,255,0.12);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 3px 12px;
    border-radius: 50px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.dc-product-card.theme-browser_frame:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 35px rgba(15, 23, 42, 0.2) !important;
    border-color: #6366f1;
}

/* 3. FEATURED PILL THEME (IMAGE 2 INSPIRED) */
.dc-product-card.theme-featured_pill {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}
.dc-product-card.theme-featured_pill .featured-pill-badge {
    position: absolute;
    top: 14px;
    left: 14px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #ffffff;
    font-size: 0.68rem;
    font-weight: 900;
    letter-spacing: 0.8px;
    padding: 6px 16px;
    border-radius: 50px;
    text-transform: uppercase;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
    z-index: 4;
}
.dc-product-card.theme-featured_pill .wishlist-standalone-box {
    position: absolute;
    top: 14px;
    right: 14px;
    width: 38px;
    height: 38px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    border: 1px solid #f1f5f9;
    z-index: 4;
    cursor: pointer;
    transition: all 0.2s ease;
}
.dc-product-card.theme-featured_pill .wishlist-standalone-box:hover,
.dc-product-card.theme-featured_pill .wishlist-standalone-box.active {
    color: #ef4444;
    background: #fee2e2;
    border-color: #fecaca;
    transform: scale(1.08);
}
.dc-product-card.theme-featured_pill:hover {
    transform: translateY(-6px);
    box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1) !important;
    background: #ffffff;
}

/* 4. NEO-BRUTALIST CYBER THEME */
.dc-product-card.theme-neo_brutalist {
    border: 2.5px solid #0f172a !important;
    box-shadow: 5px 5px 0px #0f172a !important;
    border-radius: 8px !important;
}
.dc-product-card.theme-neo_brutalist:hover {
    transform: translate(-3px, -3px);
    box-shadow: 8px 8px 0px #0f172a !important;
}
.dc-product-card.theme-neo_brutalist .card-badge-pill {
    border: 2px solid #0f172a;
    box-shadow: 2px 2px 0px #0f172a;
    border-radius: 4px !important;
}

/* 5. CYBER GLOW & DARK NEON THEME */
.dc-product-card.theme-cyber_glow {
    background: rgba(15, 23, 42, 0.95);
    border: 1px solid rgba(99, 102, 241, 0.3);
    color: #f8fafc;
}
.dc-product-card.theme-cyber_glow .card-title a { color: #f8fafc !important; }
.dc-product-card.theme-cyber_glow .card-title a:hover { color: #818cf8 !important; }
.dc-product-card.theme-cyber_glow .meta-cat { color: #94a3b8 !important; }
.dc-product-card.theme-cyber_glow .price-sale { color: #38bdf8 !important; }
.dc-product-card.theme-cyber_glow .price-original { color: #64748b !important; }
.dc-product-card.theme-cyber_glow:hover {
    transform: translateY(-6px);
    border-color: #38bdf8;
    box-shadow: 0 0 25px rgba(56, 189, 248, 0.35) !important;
}

/* 6. HORIZONTAL SPLIT THEME */
.dc-product-card.theme-horizontal_split {
    flex-direction: row;
}
.dc-product-card.theme-horizontal_split .card-thumb {
    width: 42%;
    flex-shrink: 0;
}
.dc-product-card.theme-horizontal_split .card-info {
    width: 58%;
}
@media (max-width: 576px) {
    .dc-product-card.theme-horizontal_split { flex-direction: column; }
    .dc-product-card.theme-horizontal_split .card-thumb,
    .dc-product-card.theme-horizontal_split .card-info { width: 100%; }
}

/* 7. CODECANYON MARKETPLACE THEME */
.dc-product-card.theme-codecanyon {
    border-top: 4px solid var(--accent, #6366f1);
}
.dc-product-card.theme-codecanyon .codecanyon-bar {
    background: #f1f5f9;
    padding: 6px 12px;
    font-size: 0.72rem;
    font-weight: 700;
    color: #475569;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* 8. MINIMAL CLEAN THEME */
.dc-product-card.theme-minimal_clean {
    border: 1px solid #f1f5f9;
    box-shadow: none !important;
}
.dc-product-card.theme-minimal_clean:hover {
    border-color: #cbd5e1;
    transform: translateY(-4px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.06) !important;
}

/* 9. OVERLAY HERO THEME */
.dc-product-card.theme-overlay_hero .card-thumb {
    position: relative;
}
.dc-product-card.theme-overlay_hero .hero-overlay-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.4) 60%, transparent 100%);
    padding: 16px;
    color: #ffffff;
    z-index: 3;
}
.dc-product-card.theme-overlay_hero .hero-overlay-content a {
    color: #ffffff !important;
    text-shadow: 0 1px 3px rgba(0,0,0,0.5);
}

/* 10. DELUXE ECOMMERCE THEME */
.dc-product-card.theme-deluxe_ecommerce {
    border: 1px solid #e2e8f0;
}
.dc-product-card.theme-deluxe_ecommerce .deluxe-discount-ribbon {
    position: absolute;
    top: 12px;
    left: 0;
    background: #ef4444;
    color: #ffffff;
    font-size: 0.7rem;
    font-weight: 800;
    padding: 4px 12px;
    border-top-right-radius: 50px;
    border-bottom-right-radius: 50px;
    box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
    z-index: 4;
}

/* SHARED CARD UI ELEMENTS */
.card-thumb {
    position: relative;
    overflow: hidden;
    background: #f8fafc;
}
.card-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}
.dc-product-card:hover .card-thumb img {
    transform: scale(1.08);
}
.card-hover-overlay-quickview {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 3;
}
.card-thumb:hover .card-hover-overlay-quickview {
    opacity: 1;
}
.quick-view-btn {
    background: #fff;
    color: #0f172a;
    font-weight: 700;
    font-size: 0.72rem;
    padding: 7px 18px;
    border-radius: 50px;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.08);
    transform: translateY(12px);
    transition: all 0.3s ease;
    cursor: pointer;
    border: none;
}
.card-thumb:hover .quick-view-btn {
    transform: translateY(0);
}
.quick-view-btn:hover {
    background: var(--accent, #6366f1);
    color: #fff;
}

.card-badge-pill {
    position: absolute;
    top: 12px;
    left: 12px;
    padding: 4px 12px;
    border-radius: 50px;
    color: #fff;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    z-index: 4;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
.card-discount-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    color: #fff;
    font-size: 0.6rem;
    font-weight: 800;
    z-index: 4;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    background: #ef4444;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    line-height: 1;
}
.card-like-btn {
    position: absolute;
    top: 52px;
    right: 10px;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(226, 232, 240, 0.8);
    color: #64748b;
    font-size: 0.78rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 4;
    transition: all 0.3s ease;
}
.card-like-btn:hover, .card-like-btn.active {
    background: #fee2e2;
    color: #ef4444;
    border-color: #fecaca;
    transform: scale(1.1);
}

.card-info {
    padding: 16px !important;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.card-title {
    font-weight: 700;
    font-size: 0.95rem;
    line-height: 1.35;
    margin-bottom: 4px !important;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 2.5em;
}
.card-title a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.3s;
}
.card-title a:hover {
    color: var(--accent, #6366f1);
}
.meta-cat {
    color: #94a3b8;
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 6px !important;
    display: block;
}
.card-meta-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px !important;
    font-size: 0.82rem;
}
.rating-stars {
    color: #f59e0b;
    font-size: 0.85rem;
}
.sales-count {
    color: #94a3b8;
    display: flex;
    align-items: center;
    gap: 4px;
    font-weight: 500;
}
.card-divider {
    border: 0;
    border-top: 1px solid #e2e8f0;
    margin: 6px 0 10px 0 !important;
    opacity: 0.8;
}
.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    gap: 8px;
    padding-top: 0 !important;
}
.price-group {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.price-sale {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    white-space: nowrap;
}
.price-original {
    font-size: 0.78rem;
    font-weight: 600;
    color: #94a3b8;
    text-decoration: line-through;
}
.price-discount-pill {
    font-size: 0.68rem;
    font-weight: 800;
    color: #ef4444;
    background: #fee2e2;
    padding: 2px 8px;
    border-radius: 50px;
}

/* BUTTON STYLES */
.btn-style-rounded_pill .add-to-cart-btn,
.btn-style-rounded_pill .buy-now-btn {
    border-radius: 50px !important;
}
.btn-style-square_rounded .add-to-cart-btn,
.btn-style-square_rounded .buy-now-btn {
    border-radius: 8px !important;
}
.btn-style-full_width .card-footer {
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
}
.btn-style-full_width .card-actions {
    width: 100%;
}
.btn-style-full_width .add-to-cart-btn,
.btn-style-full_width .buy-now-btn {
    width: 100% !important;
    border-radius: 10px !important;
}

.add-to-cart-btn, .buy-now-btn {
    padding: 8px 16px;
    background: var(--accent, #6366f1);
    color: #fff;
    border: none;
    font-weight: 700;
    font-size: 0.82rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}
.add-to-cart-btn:hover, .buy-now-btn:hover {
    opacity: 0.92;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.45);
}

.live-preview-link-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0ea5e9, #6366f1);
    color: #fff;
    font-size: 0.95rem;
    text-decoration: none;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
}
.live-preview-link-btn:hover {
    transform: scale(1.1);
    color: #fff;
}

/* DARK MODE OVERRIDES */
[data-theme="dark"] .dc-product-card {
    background: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}
[data-theme="dark"] .dc-product-card .card-title a { color: #f8fafc !important; }
[data-theme="dark"] .dc-product-card .price-sale { color: #f8fafc !important; }
[data-theme="dark"] .dc-product-card .card-divider { border-top-color: rgba(255, 255, 255, 0.1) !important; }
[data-theme="dark"] .dc-product-card.theme-featured_pill { background: #0f172a !important; }
[data-theme="dark"] .dc-product-card.theme-featured_pill .wishlist-standalone-box { background: #1e293b; border-color: rgba(255,255,255,0.1); color: #94a3b8; }
</style>
<?php endif; ?>

<div class="dc-product-card dc-reveal theme-<?= htmlspecialchars($cardTheme) ?> shadow-<?= htmlspecialchars($cardShadow) ?> btn-style-<?= htmlspecialchars($btnStyle) ?>" id="product-<?= $product['id'] ?>" style="border-radius: <?= $cardBorderRadius ?>px; --user-card-accent: <?= htmlspecialchars($accentColor) ?>;">

    <?php if ($cardTheme === 'browser_frame'): ?>
        <!-- BROWSER HEADER BAR (IMAGE 1 STYLE) -->
        <div class="browser-header-bar">
            <div class="browser-dots">
                <span class="browser-dot dot-red"></span>
                <span class="browser-dot dot-yellow"></span>
                <span class="browser-dot dot-green"></span>
            </div>
            <div class="browser-pill-badge">
                <i class="bi bi-box-seam"></i> Product
            </div>
        </div>
    <?php endif; ?>

    <?php if ($cardTheme === 'codecanyon'): ?>
        <!-- CODECANYON HEADER BAR -->
        <div class="codecanyon-bar">
            <span><i class="bi bi-folder2-open me-1"></i> <?= htmlspecialchars($product['category_name'] ?? 'Script') ?></span>
            <span><i class="bi bi-patch-check-fill text-primary"></i> Verified</span>
        </div>
    <?php endif; ?>

    <!-- THUMBNAIL CONTAINER -->
    <a href="<?= url('product/' . $product['slug']) ?>" class="card-thumb" style="aspect-ratio: <?= htmlspecialchars($cardAspectRatio) ?>;">
        
        <?php if ($cardTheme === 'featured_pill' && $showStatusBadge): ?>
            <!-- FEATURED PILL (IMAGE 2 STYLE) -->
            <span class="featured-pill-badge">FEATURED</span>
        <?php elseif ($cardTheme === 'deluxe_ecommerce' && $showDiscountBadge && $hasDiscount): ?>
            <span class="deluxe-discount-ribbon">-<?= $discountPercent ?>% OFF</span>
        <?php elseif ($showStatusBadge): ?>
            <?php if(!empty($product['is_bestseller'])): ?>
            <span class="card-badge-pill" style="background:#ef4444;">Hot</span>
            <?php elseif(!empty($product['is_exclusive'])): ?>
            <span class="card-badge-pill" style="background:#8b5cf6;">Premium</span>
            <?php elseif(!empty($product['is_featured'])): ?>
            <span class="card-badge-pill" style="background:#10b981;">Featured</span>
            <?php elseif(!empty($product['is_new'])): ?>
            <span class="card-badge-pill" style="background:#2563eb;">New</span>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if($showDiscountBadge && $hasDiscount && $cardTheme !== 'deluxe_ecommerce' && $cardTheme !== 'browser_frame'): ?>
        <span class="card-discount-badge">-<?= $discountPercent ?>%</span>
        <?php endif; ?>
        
        <?php if($showWishlistBtn): ?>
            <?php if($cardTheme === 'featured_pill'): ?>
            <div class="wishlist-standalone-box wishlist-btn <?= is_in_wishlist($product['id']) ? 'active' : '' ?>" data-product-id="<?= $product['id'] ?>" title="Wishlist">
                <i class="bi <?= is_in_wishlist($product['id']) ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i>
            </div>
            <?php else: ?>
            <button class="card-like-btn wishlist-btn <?= is_in_wishlist($product['id']) ? 'active' : '' ?>" data-product-id="<?= $product['id'] ?>" title="Wishlist" aria-label="Wishlist">
                <i class="bi <?= is_in_wishlist($product['id']) ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
            </button>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if($showQuickView): ?>
        <div class="card-hover-overlay-quickview">
            <span class="quick-view-btn quickview-trigger-btn" data-product-id="<?= $product['id'] ?>">Quick View</span>
        </div>
        <?php endif; ?>

        <?php if(!empty($product['thumbnail'])): ?>
            <img src="<?= uploadUrl($product['thumbnail']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" class="product-img" loading="lazy" decoding="async" width="320" height="200">
        <?php else: ?>
            <div class="d-flex align-items-center justify-content-center h-100" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); min-height: 180px;">
                <i class="bi bi-laptop text-white opacity-75" style="font-size: 3rem;"></i>
            </div>
        <?php endif; ?>

        <?php if($cardTheme === 'overlay_hero'): ?>
            <div class="hero-overlay-content">
                <h5 class="fw-bold fs-6 mb-1">
                    <a href="<?= url('product/' . $product['slug']) ?>"><?= htmlspecialchars($product['title']) ?></a>
                </h5>
                <div class="fw-extrabold text-info fs-5"><?= formatPrice($product['sale_price']) ?></div>
            </div>
        <?php endif; ?>
    </a>

    <!-- CARD INFO BODY -->
    <div class="card-info">
        <?php if($cardTheme !== 'overlay_hero'): ?>
        <h5 class="card-title">
            <a href="<?= url('product/' . $product['slug']) ?>"><?= htmlspecialchars($product['title']) ?></a>
        </h5>
        <?php endif; ?>

        <span class="meta-cat"><?= htmlspecialchars($product['category_name'] ?? 'Template') ?></span>
        
        <?php if($showRating || ($showSalesCount && $displaySales > 0)): ?>
        <div class="card-meta-row">
            <?php if($showRating): ?>
            <div class="d-flex align-items-center gap-1">
                <span class="rating-stars"><i class="bi bi-star-fill"></i></span>
                <span style="color:#f59e0b; font-weight:700;"><?= number_format((float)($product['avg_rating'] ?? 5.0), 1) ?></span>
                <span class="rating-count text-muted" style="font-size:0.75rem;">(<?= (int)($product['reviews_count'] ?? 1) ?>)</span>
            </div>
            <?php else: ?>
            <div></div>
            <?php endif; ?>

            <?php if($showSalesCount && $displaySales > 0): ?>
            <span class="sales-count"><i class="bi bi-download"></i> <?= $formattedSales ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <hr class="card-divider">

        <div class="card-footer">
            <div class="price-group">
                <span class="price-sale"><?= formatPrice($product['sale_price']) ?></span>
                <?php if($showMrpPrice && $hasDiscount): ?>
                    <span class="price-original"><?= formatPrice($product['mrp']) ?></span>
                <?php endif; ?>
                <?php if($cardTheme === 'browser_frame' && $showDiscountBadge && $hasDiscount): ?>
                    <span class="price-discount-pill">-<?= $discountPercent ?>%</span>
                <?php endif; ?>
            </div>
            
            <div class="card-actions d-flex align-items-center gap-2">
                <?php if($showLivePreview && $hasLivePreview): ?>
                <a href="<?= htmlspecialchars($previewUrl) ?>" target="_blank" rel="noopener noreferrer" class="live-preview-link-btn" title="Live Preview" aria-label="Live Preview">
                    <i class="bi bi-link-45deg"></i>
                </a>
                <?php endif; ?>

                <button class="add-to-cart-btn" data-product-id="<?= $product['id'] ?>" title="Add to Cart" aria-label="Add to Cart">
                    <i class="bi bi-cart"></i>
                    <?php if($cardTheme === 'browser_frame' || $cardTheme === 'featured_pill' || $btnStyle === 'full_width'): ?>
                        <span><?= ($cardTheme === 'browser_frame') ? 'Buy Now' : 'Add' ?></span>
                    <?php endif; ?>
                </button>
            </div>
        </div>
    </div>
</div>
