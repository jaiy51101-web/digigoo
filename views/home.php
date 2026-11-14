<!-- ============================================
     DIGIWS - PREMIUM LUXURY HOMEPAGE
     ============================================ -->

<!-- Hero Section -->
<section class="dc-hero" style="padding: 40px 0 50px 0; background: radial-gradient(circle at top right, rgba(37, 99, 235, 0.08), transparent 45%), radial-gradient(circle at bottom left, rgba(124, 58, 237, 0.05), transparent 45%), var(--bg-primary);">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Left Side: Content & Search -->
            <div class="col-lg-6 hero-left-col" style="position:relative; z-index:10;">
                <span class="badge mb-2" style="background:rgba(37, 99, 235, 0.08); color:var(--primary); padding:8px 16px; border-radius:var(--radius-full); font-size:0.85rem; font-weight:700; font-family:var(--font-title); border:1px solid rgba(37, 99, 235, 0.15);">
                    🚀 Next-Gen Digital Marketplace
                </span>
                
                <h1 class="hero-title">
                    <span class="hero-title-line-1">Build Smarter: The Ultimate <span class="gradient-text-purple">Digital</span></span> <br>
                    <span class="hero-title-line-2">Products & <span class="gradient-text-blue">Source Code Marketplace.</span></span>
                </h1>
                
                <p style="font-size: 1.15rem; color: var(--text-secondary); line-height: 1.7; margin-bottom: 20px; max-width: 580px;">
                    Access thousands of production-ready scripts, templates, plugins, AI tools, UI kits, and graphics, expertly curated for developers and creators.
                </p>
                
                <!-- Search bar with AI suggestions -->
                <div class="mb-3 mx-auto mx-lg-0" style="max-width: 540px;">
                    <form action="<?= url('products') ?>" method="GET" class="hero-search-form-gradient">
                        <input type="text" name="q" id="heroSearchInput" placeholder="Search for e.g. &quot;NextJS Dashboard&quot;..." class="hero-search-input-custom">
                        <button type="submit" class="hero-search-btn-custom">
                            <i class="bi bi-search"></i><span class="ms-2">AI Search</span>
                        </button>
                    </form>
                </div>

                <!-- Action Buttons -->
                <div class="hero-action-buttons mb-0">
                    <a href="<?= url('products') ?>" class="btn-hero-primary-glossy"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Explore Marketplace</a>
                    <a href="<?= url('contact') ?>" class="btn-hero-outline-light"><i class="bi bi-shop me-2"></i>Become a Seller</a>
                </div>
            </div>

            <!-- Right Side: Luxury Dashboard Illustration -->
            <div class="col-lg-6 d-none d-lg-block hero-right-col" style="position:relative;">
                <!-- Ambient Glow Shapes -->
                <div style="position: absolute; top: -10%; right: 10%; width: 350px; height: 350px; background: radial-gradient(circle, rgba(37,99,235,0.06) 0%, transparent 70%); border-radius: var(--radius-full); pointer-events: none; z-index: 1;"></div>
                <div style="position: absolute; bottom: 0; left: 10%; width: 250px; height: 250px; background: radial-gradient(circle, rgba(124,58,237,0.05) 0%, transparent 70%); border-radius: var(--radius-full); pointer-events: none; z-index: 1;"></div>

                <!-- Main Glass Container -->
                <div class="dc-reveal" style="background: var(--bg-glass); border: 1px solid var(--border-glass); backdrop-filter: var(--backdrop-blur); -webkit-backdrop-filter: var(--backdrop-blur); border-radius: var(--radius-xl); padding: 24px; box-shadow: var(--shadow-lg); position: relative; z-index: 5;">
                    
                    <!-- Window Controls -->
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width: 10px; height: 10px; border-radius: 50%; background: #EF4444;"></div>
                        <div style="width: 10px; height: 10px; border-radius: 50%; background: #F59E0B;"></div>
                        <div style="width: 10px; height: 10px; border-radius: 50%; background: #10B981;"></div>
                        <div class="ms-3" style="font-size: 0.75rem; font-weight: 600; font-family: var(--font-title); color: var(--text-muted);">digiws.com/marketplace/analytics</div>
                    </div>

                    <!-- Layout: Left Analytics, Right Activity -->
                    <div class="row g-3">
                        <div class="col-8">
                            <!-- Revenue Card -->
                            <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 18px; box-shadow: var(--shadow-sm); margin-bottom: 15px;">
                                <div class="d-flex justify-content-between mb-1" style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">
                                    <span>TOTAL SALES REVENUE</span>
                                    <span style="color: var(--accent);"><i class="bi bi-arrow-up-right me-1"></i>+12.4%</span>
                                </div>
                                <h3 style="font-size: 1.8rem; font-weight: 800; color: var(--text-primary); margin-bottom: 15px;">$142,398.50</h3>
                                
                                <div class="d-flex align-items-end gap-3">
                                    <!-- Wave Line Chart Container -->
                                    <div style="flex-grow: 1; height: 100px;">
                                        <svg viewBox="0 0 300 80" class="w-100" style="height: 75px; overflow: visible;">
                                            <defs>
                                                <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                                    <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.3"/>
                                                    <stop offset="100%" stop-color="#3B82F6" stop-opacity="0"/>
                                                </linearGradient>
                                            </defs>
                                            <path d="M 0 60 C 40 40, 60 20, 100 35 C 140 50, 160 10, 200 40 C 240 70, 260 25, 300 15 L 300 80 L 0 80 Z" fill="url(#chartGradient)"/>
                                            <path d="M 0 60 C 40 40, 60 20, 100 35 C 140 50, 160 10, 200 40 C 240 70, 260 25, 300 15" fill="none" stroke="#2563EB" stroke-width="3" stroke-linecap="round"/>
                                            <circle cx="100" cy="35" r="4" fill="#2563EB" stroke="var(--card-bg)" stroke-width="1.5"/>
                                            <circle cx="200" cy="40" r="4" fill="#2563EB" stroke="var(--card-bg)" stroke-width="1.5"/>
                                            <circle cx="300" cy="15" r="4" fill="#10B981" stroke="var(--card-bg)" stroke-width="1.5"/>
                                        </svg>
                                        <!-- Labels -->
                                        <div class="d-flex justify-content-between mt-2" style="font-size: 0.6rem; color: var(--text-muted); font-weight: 600;">
                                            <span>Jan 09</span>
                                            <span>Jan 01</span>
                                            <span>Jan 20</span>
                                            <span>Dec 12</span>
                                            <span>Oct 18</span>
                                            <span>Jan 19</span>
                                            <span>Dec 20</span>
                                        </div>
                                    </div>
                                    <!-- Vertical Bars -->
                                    <div class="d-flex align-items-end gap-2" style="height: 90px; width: 45px; flex-shrink: 0; padding-bottom: 12px;">
                                        <div style="width: 14px; height: 85%; background: var(--primary); border-radius: 4px; box-shadow: 0 4px 10px rgba(37,99,235,0.3);"></div>
                                        <div style="width: 14px; height: 60%; background: var(--secondary); border-radius: 4px; box-shadow: 0 4px 10px rgba(124,58,237,0.3);"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Performance indicators -->
                            <div class="d-flex gap-3">
                                <div style="flex: 1; background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 12px; text-align: center;">
                                    <small style="font-size: 0.68rem; color: var(--text-muted); font-weight: 700; display: block; letter-spacing: 0.5px; margin-bottom: 2px;">ORDERS</small>
                                    <strong style="font-size: 1.2rem; color: var(--text-primary); font-weight: 800;">4,289</strong>
                                </div>
                                <div style="flex: 1; background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 12px; text-align: center;">
                                    <small style="font-size: 0.68rem; color: var(--text-muted); font-weight: 700; display: block; letter-spacing: 0.5px; margin-bottom: 2px;">DOWNLOADS</small>
                                    <strong style="font-size: 1.2rem; color: var(--text-primary); font-weight: 800;">12,983</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Right Panel: Floating Feed cards -->
                        <div class="col-4">
                            <!-- Floating Card 1: Product Preview -->
                            <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 12px; box-shadow: var(--shadow-sm); margin-bottom: 12px;">
                                <div style="width: 100%; aspect-ratio: 2038/1558; background: var(--primary-soft); border-radius: var(--radius-sm); margin-bottom: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 1.3rem;">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <h6 style="font-size: 0.8rem; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; line-height: 1.2;">SaaS Dashboard</h6>
                                <span style="font-size: 0.75rem; color: var(--accent); font-weight: 700;">$59.00</span>
                            </div>

                            <!-- Floating Card 2: Notification -->
                            <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 10px; box-shadow: var(--shadow-sm); display: flex; align-items: center; gap: 8px;">
                                <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary-soft); display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 0.8rem;">
                                    <i class="bi bi-lightning-fill"></i>
                                </div>
                                <div style="line-height: 1.2;">
                                    <small style="display: block; font-size: 0.68rem; font-weight: 700; color: var(--text-primary);">New Sale!</small>
                                    <span style="font-size: 0.62rem; color: var(--text-muted);">Just now</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floaters outside the container -->
                <div class="dc-hero-stat-float stat-box-1" style="right: -8%; top: -8%; left: auto; transform: none; background: var(--card-bg); border: 1px solid var(--border-color); box-shadow: var(--shadow-md); border-radius: 12px; padding: 10px 16px;">
                    <i class="bi bi-shield-check text-success" style="font-size: 1.3rem;"></i>
                    <div>
                        <strong style="font-size: 0.82rem; font-weight: 800; line-height: 1.2; margin: 0; color: var(--text-primary); display: block;">100% Secure</strong>
                        <small style="font-size: 0.65rem; color: var(--text-muted); font-weight: 600; display: block;">Verified Products</small>
                    </div>
                </div>
                <div class="dc-hero-stat-float stat-box-2" style="right: -10%; bottom: -8%; top: auto; transform: none; display: flex; align-items: center; gap: 12px; background: var(--card-bg); border: 1px solid var(--border-color); box-shadow: var(--shadow-md); border-radius: 12px; padding: 10px 16px;">
                    <i class="bi bi-award-fill text-warning" style="font-size: 1.3rem;"></i>
                    <div>
                        <strong style="font-size: 0.82rem; font-weight: 800; line-height: 1.2; margin: 0; color: var(--text-primary); display: block;">Editor's Choice</strong>
                        <small style="font-size: 0.65rem; color: var(--text-muted); font-weight: 600; display: block;">Quality templates</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Featured Categories -->
<section class="dc-section dc-category-section">
    <div class="container">
        <div class="dc-section-header dc-reveal" style="margin-bottom: 8px;">
            <h2 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 0;">Explore Categories</h2>
            <div class="accent-line" style="margin-top: 6px;"></div>
        </div>
        
        <div class="category-slider-container">
            <div class="category-scroll-row">
                <!-- Original Loop -->
                <?php foreach ($categories as $cat): ?>
                <div class="category-slide-item" data-category-slug="<?= htmlspecialchars($cat['slug']) ?>">
                    <a href="<?= url('products/category/' . $cat['slug']) ?>" class="text-decoration-none">
                        <div class="dc-category-card">
                            <div class="cat-icon">
                                <i class="bi <?= htmlspecialchars($cat['icon'] ?: 'bi-box-seam') ?>"></i>
                                <span class="cat-count-badge"><?= $cat['product_count'] ?? 0 ?></span>
                            </div>
                            <h5><?= htmlspecialchars($cat['name']) ?></h5>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
                
                <!-- Duplicate Loop for Seamless Infinite Scroll -->
                <?php foreach ($categories as $cat): ?>
                <div class="category-slide-item" data-category-slug="<?= htmlspecialchars($cat['slug']) ?>">
                    <a href="<?= url('products/category/' . $cat['slug']) ?>" class="text-decoration-none">
                        <div class="dc-category-card">
                            <div class="cat-icon">
                                <i class="bi <?= htmlspecialchars($cat['icon'] ?: 'bi-box-seam') ?>"></i>
                                <span class="cat-count-badge"><?= $cat['product_count'] ?? 0 ?></span>
                            </div>
                            <h5><?= htmlspecialchars($cat['name']) ?></h5>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Auto-Slide Script -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const row = document.querySelector(".category-scroll-row");
        if (!row) return;
        
        let scrollSpeed = 0.65; // Smooth scroll speed
        let animationId = null;
        
        function step() {
            row.scrollLeft += scrollSpeed;
            // Wrap back when half scrolled (since content is duplicated)
            if (row.scrollLeft >= row.scrollWidth / 2) {
                row.scrollLeft = 0;
            }
            animationId = requestAnimationFrame(step);
        }
        
        // Start auto-scroll
        step();
        
        // Pause scrolling on mouse hover/touch to allow user interaction
        row.addEventListener("mouseenter", function() {
            cancelAnimationFrame(animationId);
        });
        row.addEventListener("mouseleave", function() {
            step();
        });
        row.addEventListener("touchstart", function() {
            cancelAnimationFrame(animationId);
        }, { passive: true });
        row.addEventListener("touchend", function() {
            step();
        }, { passive: true });
    });
    </script>
</section>

<!-- Featured Products (Editor's Choice) -->
<?php if (!empty($featured)): ?>
<section class="dc-section">
    <div class="container">
        <div class="dc-section-header dc-reveal">
            <h2>Featured Products</h2>
            <p>Handpicked premium digital assets offering exceptional visual quality and clean markup</p>
            <div class="accent-line"></div>
        </div>
        <div class="row g-3 dc-reveal-stagger">
            <?php foreach ($featured as $product): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <?php include VIEWS_PATH . '/partials/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?= url('products') ?>" class="btn-dc-outline" style="padding: 12px 24px;">
                View All Products <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Trending Products (Bestsellers) -->
<?php if (!empty($bestsellers)): ?>
<section class="dc-section" style="background:var(--bg-secondary); padding: 20px 0;">
    <div class="container">
        <div class="dc-section-header dc-reveal" style="margin-bottom: 12px;">
            <h2 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 0;">Trending Solutions</h2>
            <p style="font-size: 0.95rem; margin-bottom: 0;">The highest converting templates, automation scripts, and PHP scripts on the market</p>
            <div class="accent-line" style="margin-top: 6px;"></div>
        </div>
        <div class="row g-3 dc-reveal-stagger">
            <?php foreach ($bestsellers as $product): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <?php include VIEWS_PATH . '/partials/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Customer Testimonials -->
<section class="dc-section" style="background:var(--bg-secondary); padding: 20px 0;">
    <div class="container">
        <div class="dc-section-header dc-reveal" style="margin-bottom: 12px;">
            <h2 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 0;">Trusted by Creators Worldwide</h2>
            <p style="font-size: 0.95rem; margin-bottom: 0;">Here is what software architects, design specialists, and founders say about DigiWS</p>
            <div class="accent-line" style="margin-top: 6px;"></div>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="dc-testimonial">
                    <div class="stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p>"Amazing quality products! The website templates saved me weeks of development time. Highly recommended for any developer or business owner."</p>
                    <div class="author">
                        <div style="width:44px;height:44px;border-radius:50%;background:rgba(37,99,235,0.1);display:flex;align-items:center;justify-content:center;color:var(--primary);font-weight:700;font-family:var(--font-title);">R</div>
                        <div>
                            <h6>Rahul Sharma</h6>
                            <small>Web Developer</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dc-testimonial">
                    <div class="stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p>"Excellent support team and clean code quality. The PHP scripts are well-documented and easy to customize. Will definitely buy again!"</p>
                    <div class="author">
                        <div style="width:44px;height:44px;border-radius:50%;background:rgba(124,58,237,0.1);display:flex;align-items:center;justify-content:center;color:var(--secondary);font-weight:700;font-family:var(--font-title);">P</div>
                        <div>
                            <h6>Priya Patel</h6>
                            <small>Startup Founder</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dc-testimonial">
                    <div class="stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i></div>
                    <p>"Best marketplace for digital products in India. Fair pricing, instant downloads, and the custom website service is top-notch."</p>
                    <div class="author">
                        <div style="width:44px;height:44px;border-radius:50%;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;color:var(--accent);font-weight:700;font-family:var(--font-title);">A</div>
                        <div>
                            <h6>Amit Kumar</h6>
                            <small>Business Owner</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA Banner -->
<section class="dc-cta dc-section" style="padding: 30px 0;">
    <div class="container">
        <div class="dc-cta-inner">
            <h2 style="font-size:1.8rem; margin-bottom: 10px;">Ready to Accelerate Your Projects? 🚀</h2>
            <p style="margin-bottom: 20px;">Join thousands of software engineers, digital creators, and entrepreneurs scaling their operations with DigiWS templates and scripts.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="<?= url('products') ?>" class="btn-dc-primary" style="padding: 14px 28px;">
                    <i class="bi bi-cart3 me-2"></i> Browse Marketplace
                </a>
                <a href="<?= url('contact') ?>" class="btn-dc-outline" style="padding: 14px 28px;">
                    <i class="bi bi-chat-dots me-2"></i> Consult Our Team
                </a>
            </div>
        </div>
    </div>
</section>

<!-- New Arrivals (Recently Added) -->
<?php if (!empty($newProducts)): ?>
<section class="dc-section" style="background:var(--bg-secondary); border-top:1px solid var(--border-color); padding: 20px 0;">
    <div class="container">
        <div class="dc-section-header dc-reveal" style="margin-bottom: 12px;">
            <h2 style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.5px; margin-bottom: 0;">New Releases</h2>
            <p style="font-size: 0.95rem; margin-bottom: 0;">Fresh digital products and code templates freshly added to our catalog</p>
            <div class="accent-line" style="margin-top: 6px;"></div>
        </div>
        <div class="row g-3">
            <?php foreach ($newProducts as $product): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <?php include VIEWS_PATH . '/partials/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
