<?php
/**
 * Admin Product Card Design Customizer View — Digiws (10 Preset Themes Engine)
 */
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-extrabold mb-1">Product Card View Design Customizer</h4>
        <p class="text-muted small mb-0">Select from 10 high-converting pre-built product card designs or manually customize every detail.</p>
    </div>
</div>

<form action="<?= url('admin/product-design/update') ?>" method="POST" id="productDesignForm">
    <?= csrfField() ?>
    
    <div class="row g-4">
        <!-- Controls Column -->
        <div class="col-lg-7">
            
            <!-- 1. 10 Theme Presets -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-palette me-2 text-primary"></i>10 Pre-built Card Designs</h5>
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold">Select Preset Theme</span>
                    </div>
                    <small class="text-muted">Choose your preferred product card layout style</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        
                        <!-- 1. Classic -->
                        <div class="col-md-6">
                            <label class="theme-preset-card p-3 border rounded-3 d-block cursor-pointer position-relative h-100 <?= ($design['card_theme'] === 'classic') ? 'border-primary bg-primary bg-opacity-10' : '' ?>">
                                <input type="radio" name="card_theme" value="classic" class="form-check-input position-absolute top-0 end-0 m-3" <?= ($design['card_theme'] === 'classic') ? 'checked' : '' ?>>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-card-text text-primary fs-5"></i>
                                    <span class="fw-bold text-dark">1. Classic DigiWS</span>
                                </div>
                                <small class="text-muted d-block" style="font-size:0.75rem;">Standard clean card with rating row & circular action icons.</small>
                            </label>
                        </div>

                        <!-- 2. Browser Frame (Image 1 style) -->
                        <div class="col-md-6">
                            <label class="theme-preset-card p-3 border rounded-3 d-block cursor-pointer position-relative h-100 <?= ($design['card_theme'] === 'browser_frame') ? 'border-primary bg-primary bg-opacity-10' : '' ?>">
                                <input type="radio" name="card_theme" value="browser_frame" class="form-check-input position-absolute top-0 end-0 m-3" <?= ($design['card_theme'] === 'browser_frame') ? 'checked' : '' ?>>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-window text-dark fs-5"></i>
                                    <span class="fw-bold text-dark">2. SaaS Browser Frame</span>
                                </div>
                                <small class="text-muted d-block" style="font-size:0.75rem;">Desktop browser dots titlebar, discount percentage pill & "Buy Now" button (Image 1 style).</small>
                            </label>
                        </div>

                        <!-- 3. Featured Pill (Image 2 style) -->
                        <div class="col-md-6">
                            <label class="theme-preset-card p-3 border rounded-3 d-block cursor-pointer position-relative h-100 <?= ($design['card_theme'] === 'featured_pill') ? 'border-primary bg-primary bg-opacity-10' : '' ?>">
                                <input type="radio" name="card_theme" value="featured_pill" class="form-check-input position-absolute top-0 end-0 m-3" <?= ($design['card_theme'] === 'featured_pill') ? 'checked' : '' ?>>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-bookmark-star-fill text-purple fs-5" style="color:#7c3aed;"></i>
                                    <span class="fw-bold text-dark">3. Featured Pill & Soft Box</span>
                                </div>
                                <small class="text-muted d-block" style="font-size:0.75rem;">"FEATURED" top pill badge, floating wishlist heart box & full-width CTA (Image 2 style).</small>
                            </label>
                        </div>

                        <!-- 4. Neo-Brutalist -->
                        <div class="col-md-6">
                            <label class="theme-preset-card p-3 border rounded-3 d-block cursor-pointer position-relative h-100 <?= ($design['card_theme'] === 'neo_brutalist') ? 'border-primary bg-primary bg-opacity-10' : '' ?>">
                                <input type="radio" name="card_theme" value="neo_brutalist" class="form-check-input position-absolute top-0 end-0 m-3" <?= ($design['card_theme'] === 'neo_brutalist') ? 'checked' : '' ?>>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-square-fill text-danger fs-5"></i>
                                    <span class="fw-bold text-dark">4. Neo-Brutalist Cyber</span>
                                </div>
                                <small class="text-muted d-block" style="font-size:0.75rem;">Bold 2.5px solid dark border, offset box-shadow & sharp geometric styling.</small>
                            </label>
                        </div>

                        <!-- 5. Cyber Glow -->
                        <div class="col-md-6">
                            <label class="theme-preset-card p-3 border rounded-3 d-block cursor-pointer position-relative h-100 <?= ($design['card_theme'] === 'cyber_glow') ? 'border-primary bg-primary bg-opacity-10' : '' ?>">
                                <input type="radio" name="card_theme" value="cyber_glow" class="form-check-input position-absolute top-0 end-0 m-3" <?= ($design['card_theme'] === 'cyber_glow') ? 'checked' : '' ?>>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-brightness-high-fill text-info fs-5"></i>
                                    <span class="fw-bold text-dark">5. Cyber Glow & Dark Neon</span>
                                </div>
                                <small class="text-muted d-block" style="font-size:0.75rem;">Glassmorphism dark theme with neon cyan glowing border on hover & gradient text.</small>
                            </label>
                        </div>

                        <!-- 6. Horizontal Split -->
                        <div class="col-md-6">
                            <label class="theme-preset-card p-3 border rounded-3 d-block cursor-pointer position-relative h-100 <?= ($design['card_theme'] === 'horizontal_split') ? 'border-primary bg-primary bg-opacity-10' : '' ?>">
                                <input type="radio" name="card_theme" value="horizontal_split" class="form-check-input position-absolute top-0 end-0 m-3" <?= ($design['card_theme'] === 'horizontal_split') ? 'checked' : '' ?>>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-layout-split text-success fs-5"></i>
                                    <span class="fw-bold text-dark">6. Horizontal Split Row</span>
                                </div>
                                <small class="text-muted d-block" style="font-size:0.75rem;">Side-by-side split layout structure for list views & product showcases.</small>
                            </label>
                        </div>

                        <!-- 7. CodeCanyon -->
                        <div class="col-md-6">
                            <label class="theme-preset-card p-3 border rounded-3 d-block cursor-pointer position-relative h-100 <?= ($design['card_theme'] === 'codecanyon') ? 'border-primary bg-primary bg-opacity-10' : '' ?>">
                                <input type="radio" name="card_theme" value="codecanyon" class="form-check-input position-absolute top-0 end-0 m-3" <?= ($design['card_theme'] === 'codecanyon') ? 'checked' : '' ?>>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-shop text-warning fs-5"></i>
                                    <span class="fw-bold text-dark">7. CodeCanyon Marketplace</span>
                                </div>
                                <small class="text-muted d-block" style="font-size:0.75rem;">Author/Category top bar, sales counter, & side-by-side "Live Demo" + "Cart" bar.</small>
                            </label>
                        </div>

                        <!-- 8. Minimalist Clean -->
                        <div class="col-md-6">
                            <label class="theme-preset-card p-3 border rounded-3 d-block cursor-pointer position-relative h-100 <?= ($design['card_theme'] === 'minimal_clean') ? 'border-primary bg-primary bg-opacity-10' : '' ?>">
                                <input type="radio" name="card_theme" value="minimal_clean" class="form-check-input position-absolute top-0 end-0 m-3" <?= ($design['card_theme'] === 'minimal_clean') ? 'checked' : '' ?>>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-slash-square text-secondary fs-5"></i>
                                    <span class="fw-bold text-dark">8. Ultra Minimalist Clean</span>
                                </div>
                                <small class="text-muted d-block" style="font-size:0.75rem;">Flat borderless layout focusing on typography & image presentation.</small>
                            </label>
                        </div>

                        <!-- 9. Overlay Hero -->
                        <div class="col-md-6">
                            <label class="theme-preset-card p-3 border rounded-3 d-block cursor-pointer position-relative h-100 <?= ($design['card_theme'] === 'overlay_hero') ? 'border-primary bg-primary bg-opacity-10' : '' ?>">
                                <input type="radio" name="card_theme" value="overlay_hero" class="form-check-input position-absolute top-0 end-0 m-3" <?= ($design['card_theme'] === 'overlay_hero') ? 'checked' : '' ?>>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-layers-fill text-primary fs-5"></i>
                                    <span class="fw-bold text-dark">9. Overlay Hero Gradient</span>
                                </div>
                                <small class="text-muted d-block" style="font-size:0.75rem;">Immersive thumbnail taking up top area with title & price embedded inside gradient.</small>
                            </label>
                        </div>

                        <!-- 10. Deluxe Ecommerce -->
                        <div class="col-md-6">
                            <label class="theme-preset-card p-3 border rounded-3 d-block cursor-pointer position-relative h-100 <?= ($design['card_theme'] === 'deluxe_ecommerce') ? 'border-primary bg-primary bg-opacity-10' : '' ?>">
                                <input type="radio" name="card_theme" value="deluxe_ecommerce" class="form-check-input position-absolute top-0 end-0 m-3" <?= ($design['card_theme'] === 'deluxe_ecommerce') ? 'checked' : '' ?>>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-bag-check-fill text-success fs-5"></i>
                                    <span class="fw-bold text-dark">10. Storefront Ecommerce Pro</span>
                                </div>
                                <small class="text-muted d-block" style="font-size:0.75rem;">Prominent discount ribbon (-40% OFF), stock pill, gold stars & dual action buttons.</small>
                            </label>
                        </div>

                    </div>
                </div>
            </div>

            <!-- 2. Feature & Element Visibility Toggles -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-sliders me-2 text-primary"></i>Element Visibility & Features</h5>
                    <small class="text-muted">Toggle individual elements on or off across all product cards</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-check form-switch p-3 bg-light rounded-3 d-flex justify-content-between align-items-center me-0">
                                <label class="form-check-label fw-semibold text-dark cursor-pointer ms-0" for="show_status_badge">
                                    <i class="bi bi-tag-fill me-2 text-primary"></i>Status Badge (Hot/Featured)
                                </label>
                                <input class="form-check-input float-none ms-2" type="checkbox" name="show_status_badge" id="show_status_badge" value="1" <?= $design['show_status_badge'] === '1' ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch p-3 bg-light rounded-3 d-flex justify-content-between align-items-center me-0">
                                <label class="form-check-label fw-semibold text-dark cursor-pointer ms-0" for="show_discount_badge">
                                    <i class="bi bi-percent me-2 text-danger"></i>Discount Percentage Badge
                                </label>
                                <input class="form-check-input float-none ms-2" type="checkbox" name="show_discount_badge" id="show_discount_badge" value="1" <?= $design['show_discount_badge'] === '1' ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch p-3 bg-light rounded-3 d-flex justify-content-between align-items-center me-0">
                                <label class="form-check-label fw-semibold text-dark cursor-pointer ms-0" for="show_wishlist_btn">
                                    <i class="bi bi-heart-fill me-2 text-danger"></i>Wishlist Heart Button
                                </label>
                                <input class="form-check-input float-none ms-2" type="checkbox" name="show_wishlist_btn" id="show_wishlist_btn" value="1" <?= $design['show_wishlist_btn'] === '1' ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch p-3 bg-light rounded-3 d-flex justify-content-between align-items-center me-0">
                                <label class="form-check-label fw-semibold text-dark cursor-pointer ms-0" for="show_quickview">
                                    <i class="bi bi-eye-fill me-2 text-info"></i>Quick View Hover Overlay
                                </label>
                                <input class="form-check-input float-none ms-2" type="checkbox" name="show_quickview" id="show_quickview" value="1" <?= $design['show_quickview'] === '1' ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch p-3 bg-light rounded-3 d-flex justify-content-between align-items-center me-0">
                                <label class="form-check-label fw-semibold text-dark cursor-pointer ms-0" for="show_live_preview">
                                    <i class="bi bi-link-45deg me-2 text-primary"></i>Live Preview Demo Link
                                </label>
                                <input class="form-check-input float-none ms-2" type="checkbox" name="show_live_preview" id="show_live_preview" value="1" <?= $design['show_live_preview'] === '1' ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch p-3 bg-light rounded-3 d-flex justify-content-between align-items-center me-0">
                                <label class="form-check-label fw-semibold text-dark cursor-pointer ms-0" for="show_rating">
                                    <i class="bi bi-star-fill me-2 text-warning"></i>Rating Stars & Reviews
                                </label>
                                <input class="form-check-input float-none ms-2" type="checkbox" name="show_rating" id="show_rating" value="1" <?= $design['show_rating'] === '1' ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch p-3 bg-light rounded-3 d-flex justify-content-between align-items-center me-0">
                                <label class="form-check-label fw-semibold text-dark cursor-pointer ms-0" for="show_sales_count">
                                    <i class="bi bi-download me-2 text-secondary"></i>Downloads / Sales Counter
                                </label>
                                <input class="form-check-input float-none ms-2" type="checkbox" name="show_sales_count" id="show_sales_count" value="1" <?= $design['show_sales_count'] === '1' ? 'checked' : '' ?>>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch p-3 bg-light rounded-3 d-flex justify-content-between align-items-center me-0">
                                <label class="form-check-label fw-semibold text-dark cursor-pointer ms-0" for="show_mrp_price">
                                    <i class="bi bi-currency-rupee me-2 text-success"></i>MRP Strikethrough Price
                                </label>
                                <input class="form-check-input float-none ms-2" type="checkbox" name="show_mrp_price" id="show_mrp_price" value="1" <?= $design['show_mrp_price'] === '1' ? 'checked' : '' ?>>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Styling Controls (Radius, Ratio, Shadow, Buttons, Colors) -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-paint-bucket me-2 text-primary"></i>Styling & Layout Options</h5>
                    <small class="text-muted">Adjust aspect ratios, border radius, shadows & CTA button styles</small>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Thumbnail Aspect Ratio</label>
                            <select name="card_aspect_ratio" id="card_aspect_ratio" class="form-select">
                                <option value="16/10" <?= ($design['card_aspect_ratio'] === '16/10') ? 'selected' : '' ?>>16:10 (Standard Widescreen)</option>
                                <option value="16/9" <?= ($design['card_aspect_ratio'] === '16/9') ? 'selected' : '' ?>>16:9 (HD Video Ratio)</option>
                                <option value="4/3" <?= ($design['card_aspect_ratio'] === '4/3') ? 'selected' : '' ?>>4:3 (Classic Ratio)</option>
                                <option value="1/1" <?= ($design['card_aspect_ratio'] === '1/1') ? 'selected' : '' ?>>1:1 (Square)</option>
                                <option value="3/2" <?= ($design['card_aspect_ratio'] === '3/2') ? 'selected' : '' ?>>3:2 (Landscape)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Card Corner Radius (<span id="radiusValue"><?= $design['card_border_radius'] ?></span>px)</label>
                            <input type="range" name="card_border_radius" id="card_border_radius" class="form-range mt-2" min="0" max="32" value="<?= $design['card_border_radius'] ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Box Shadow Style</label>
                            <select name="card_shadow" id="card_shadow" class="form-select">
                                <option value="none" <?= ($design['card_shadow'] === 'none') ? 'selected' : '' ?>>None (Flat)</option>
                                <option value="subtle" <?= ($design['card_shadow'] === 'subtle') ? 'selected' : '' ?>>Subtle Soft Shadow</option>
                                <option value="medium" <?= ($design['card_shadow'] === 'medium') ? 'selected' : '' ?>>Medium Elevated Shadow</option>
                                <option value="heavy" <?= ($design['card_shadow'] === 'heavy') ? 'selected' : '' ?>>Heavy Shadow</option>
                                <option value="glow" <?= ($design['card_shadow'] === 'glow') ? 'selected' : '' ?>>Glow Shadow</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Action Button Style</label>
                            <select name="btn_style" id="btn_style" class="form-select">
                                <option value="rounded_pill" <?= ($design['btn_style'] === 'rounded_pill') ? 'selected' : '' ?>>Rounded Pill</option>
                                <option value="square_rounded" <?= ($design['btn_style'] === 'square_rounded') ? 'selected' : '' ?>>Square Rounded (8px)</option>
                                <option value="full_width" <?= ($design['btn_style'] === 'full_width') ? 'selected' : '' ?>>Full Width Button</option>
                                <option value="icon_only" <?= ($design['btn_style'] === 'icon_only') ? 'selected' : '' ?>>Circular Icon Only</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark d-block">Accent Primary Color</label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="color" name="accent_color" id="accent_color" class="form-control form-control-color cursor-pointer" value="<?= $design['accent_color'] ?? '#6366f1' ?>" title="Choose card accent color">
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm rounded-circle p-2 color-preset-btn" style="background:#6366f1;" data-color="#6366f1"></button>
                                    <button type="button" class="btn btn-sm rounded-circle p-2 color-preset-btn" style="background:#2563eb;" data-color="#2563eb"></button>
                                    <button type="button" class="btn btn-sm rounded-circle p-2 color-preset-btn" style="background:#7c3aed;" data-color="#7c3aed"></button>
                                    <button type="button" class="btn btn-sm rounded-circle p-2 color-preset-btn" style="background:#ef4444;" data-color="#ef4444"></button>
                                    <button type="button" class="btn btn-sm rounded-circle p-2 color-preset-btn" style="background:#10b981;" data-color="#10b981"></button>
                                    <button type="button" class="btn btn-sm rounded-circle p-2 color-preset-btn" style="background:#06b6d4;" data-color="#06b6d4"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-primary px-4 py-2.5 rounded-pill fw-bold shadow-sm">
                    <i class="bi bi-check-lg me-2"></i>Save Product Card Settings
                </button>
            </div>

        </div>

        <!-- Sticky Live Preview Column -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 100px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-eye me-2 text-primary"></i>Live Interactive Preview</h5>
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold">Real-time</span>
                </div>
                <div class="card-body p-4">
                    <div style="max-width: 360px; margin: 0 auto;" id="previewContainer">
                        
                        <!-- RENDER DYNAMIC PREVIEW CARD -->
                        <?php 
                        $product = [
                            'id' => 999,
                            'title' => 'Amazing v4 Advance Perfect Panel Script | Complete Source Code',
                            'slug' => 'sample-product',
                            'category_name' => 'Scripts',
                            'sale_price' => 499.00,
                            'mrp' => 1999.00,
                            'avg_rating' => 5.0,
                            'reviews_count' => 1,
                            'preview_url' => 'https://digiws.com',
                            'video_url' => '',
                            'is_bestseller' => 1,
                            'is_featured' => 1,
                            'is_exclusive' => 0,
                            'is_new' => 0,
                            'total_sales' => 1250,
                            'created_at' => date('Y-m-d H:i:s'),
                            'thumbnail' => ''
                        ];
                        $cardDesignConfig = $design;
                        include VIEWS_PATH . '/partials/product-card.php';
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radiusInput = document.getElementById('card_border_radius');
    const radiusVal = document.getElementById('radiusValue');
    const ratioInput = document.getElementById('card_aspect_ratio');
    const shadowInput = document.getElementById('card_shadow');
    const btnStyleInput = document.getElementById('btn_style');
    const accentInput = document.getElementById('accent_color');

    // Theme Radios
    const themeRadios = document.querySelectorAll('input[name="card_theme"]');

    // Color Swatches
    document.querySelectorAll('.color-preset-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            accentInput.value = this.dataset.color;
            updatePreview();
        });
    });

    // Event Listeners for controls
    themeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.theme-preset-card').forEach(c => {
                c.classList.remove('border-primary', 'bg-primary', 'bg-opacity-10');
            });
            if (this.checked) {
                this.closest('.theme-preset-card').classList.add('border-primary', 'bg-primary', 'bg-opacity-10');
            }
            updatePreview();
        });
    });

    radiusInput.addEventListener('input', function() {
        radiusVal.textContent = this.value;
        updatePreview();
    });

    ratioInput.addEventListener('change', updatePreview);
    shadowInput.addEventListener('change', updatePreview);
    btnStyleInput.addEventListener('change', updatePreview);
    accentInput.addEventListener('input', updatePreview);

    // Toggles
    const toggleIds = [
        'show_status_badge', 'show_discount_badge', 'show_wishlist_btn',
        'show_quickview', 'show_live_preview', 'show_rating',
        'show_sales_count', 'show_mrp_price'
    ];

    toggleIds.forEach(id => {
        const checkbox = document.getElementById(id);
        if (checkbox) {
            checkbox.addEventListener('change', updatePreview);
        }
    });

    function updatePreview() {
        const selectedTheme = document.querySelector('input[name="card_theme"]:checked')?.value || 'classic';
        const card = document.querySelector('#previewContainer .dc-product-card');
        if (!card) return;

        // Theme Class
        card.className = `dc-product-card dc-reveal theme-${selectedTheme} shadow-${shadowInput.value} btn-style-${btnStyleInput.value}`;
        
        // Inline Styles
        card.style.borderRadius = radiusInput.value + 'px';
        card.style.setProperty('--user-card-accent', accentInput.value);

        // Aspect Ratio
        const thumb = card.querySelector('.card-thumb');
        if (thumb) {
            thumb.style.aspectRatio = ratioInput.value;
        }

        // Toggle visibility elements
        const showStatus = document.getElementById('show_status_badge')?.checked;
        const showDiscount = document.getElementById('show_discount_badge')?.checked;
        const showWishlist = document.getElementById('show_wishlist_btn')?.checked;
        const showQuick = document.getElementById('show_quickview')?.checked;
        const showPreview = document.getElementById('show_live_preview')?.checked;
        const showRating = document.getElementById('show_rating')?.checked;
        const showSales = document.getElementById('show_sales_count')?.checked;
        const showMrp = document.getElementById('show_mrp_price')?.checked;

        // Theme Headers Dynamic Injection/Visibility
        let browserHeader = card.querySelector('.browser-header-bar');
        if (selectedTheme === 'browser_frame') {
            if (!browserHeader) {
                const headerHtml = `
                <div class="browser-header-bar">
                    <div class="browser-dots">
                        <span class="browser-dot dot-red"></span>
                        <span class="browser-dot dot-yellow"></span>
                        <span class="browser-dot dot-green"></span>
                    </div>
                    <div class="browser-pill-badge">
                        <i class="bi bi-box-seam"></i> Product
                    </div>
                </div>`;
                card.insertAdjacentHTML('afterbegin', headerHtml);
            } else {
                browserHeader.style.display = 'flex';
            }
        } else if (browserHeader) {
            browserHeader.style.display = 'none';
        }

        let codecanyonBar = card.querySelector('.codecanyon-bar');
        if (selectedTheme === 'codecanyon') {
            if (!codecanyonBar) {
                const barHtml = `
                <div class="codecanyon-bar">
                    <span><i class="bi bi-folder2-open me-1"></i> Scripts</span>
                    <span><i class="bi bi-patch-check-fill text-primary"></i> Verified</span>
                </div>`;
                card.insertAdjacentHTML('afterbegin', barHtml);
            } else {
                codecanyonBar.style.display = 'flex';
            }
        } else if (codecanyonBar) {
            codecanyonBar.style.display = 'none';
        }

        // Badge Elements inside preview
        const statusEl = card.querySelector('.card-badge-pill, .featured-pill-badge');
        if (statusEl) statusEl.style.display = showStatus ? 'inline-block' : 'none';

        const discountEl = card.querySelector('.card-discount-badge, .deluxe-discount-ribbon, .price-discount-pill');
        if (discountEl) discountEl.style.display = showDiscount ? 'flex' : 'none';

        const wishlistEl = card.querySelector('.wishlist-btn');
        if (wishlistEl) wishlistEl.style.display = showWishlist ? 'flex' : 'none';

        const quickEl = card.querySelector('.card-hover-overlay-quickview');
        if (quickEl) quickEl.style.display = showQuick ? 'flex' : 'none';

        const liveEl = card.querySelector('.live-preview-link-btn');
        if (liveEl) liveEl.style.display = showPreview ? 'flex' : 'none';

        const ratingEl = card.querySelector('.card-meta-row .d-flex');
        if (ratingEl) ratingEl.style.display = showRating ? 'flex' : 'none';

        const salesEl = card.querySelector('.sales-count');
        if (salesEl) salesEl.style.display = showSales ? 'flex' : 'none';

        const mrpEl = card.querySelector('.price-original');
        if (mrpEl) mrpEl.style.display = showMrp ? 'inline' : 'none';
    }

    // Initial Trigger
    updatePreview();
});
</script>
