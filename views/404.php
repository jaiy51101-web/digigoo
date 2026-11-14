<!-- 404 Page -->
<?php
$pageTitle = '404 - Page Not Found';
include TEMPLATES_PATH . '/header.php';
?>

<section class="dc-section position-relative overflow-hidden d-flex align-items-center" style="min-height: 80vh; padding: 60px 0;">
    <!-- Background abstract glow shapes -->
    <div class="position-absolute top-10 start-10 translate-middle-x rounded-circle" style="width: 300px; height: 300px; background: rgba(37, 99, 235, 0.06); filter: blur(80px); pointer-events: none; z-index: 1;"></div>
    <div class="position-absolute bottom-10 end-10 translate-middle-x rounded-circle" style="width: 300px; height: 300px; background: rgba(124, 58, 237, 0.06); filter: blur(80px); pointer-events: none; z-index: 1;"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <!-- Beautiful Interactive SVG Visual -->
                <div class="error-visual mb-4" style="animation: errorFloat 4s ease-in-out infinite;">
                    <svg width="240" height="240" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: block; margin: 0 auto;">
                        <defs>
                            <linearGradient id="glowGrad404" x1="0" y1="0" x2="240" y2="240" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#2563EB" />
                                <stop offset="100%" stop-color="#7C3AED" />
                            </linearGradient>
                            <filter id="shadow404" x="-20%" y="-20%" width="140%" height="140%">
                                <feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#2563EB" flood-opacity="0.25" />
                            </filter>
                        </defs>
                        <!-- Orbit Rings (Spinning) -->
                        <circle cx="120" cy="120" r="100" stroke="url(#glowGrad404)" stroke-width="1.5" stroke-dasharray="10 15" stroke-opacity="0.3" class="error-orbit-1" />
                        <circle cx="120" cy="120" r="80" stroke="url(#glowGrad404)" stroke-width="1" stroke-dasharray="40 10" stroke-opacity="0.5" class="error-orbit-2" />
                        
                        <!-- Floating particles -->
                        <circle cx="50" cy="80" r="4" fill="#2563EB" opacity="0.6" style="animation: errorParticle 3s ease-in-out infinite;" />
                        <circle cx="190" cy="160" r="6" fill="#7C3AED" opacity="0.8" style="animation: errorParticle 4s ease-in-out infinite 1s;" />
                        <circle cx="170" cy="60" r="3" fill="#10B981" opacity="0.7" style="animation: errorParticle 2.5s ease-in-out infinite 0.5s;" />

                        <!-- Center Glowing Plate -->
                        <circle cx="120" cy="120" r="60" fill="url(#glowGrad404)" opacity="0.1" filter="url(#shadow404)" class="error-pulse" />
                        
                        <!-- Huge Sleek Typography 404 inside SVG -->
                        <text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" font-family="var(--font-title)" font-size="64" font-weight="900" fill="url(#glowGrad404)" style="letter-spacing: -2px; filter: drop-shadow(0px 4px 8px rgba(0,0,0,0.1));">404</text>
                    </svg>
                </div>

                <!-- Glassmorphic Card Panel -->
                <div class="dc-card border p-5 mb-4 shadow-lg text-center" style="border-radius: var(--radius-xl); background: var(--bg-glass); border-color: var(--border-glass) !important; backdrop-filter: var(--backdrop-blur); -webkit-backdrop-filter: var(--backdrop-blur); margin-top: -20px;">
                    <h2 class="display-6 mb-3" style="font-weight: 800; color: var(--text-primary); letter-spacing: -1px; font-family: var(--font-title);">
                        Oops! Page Lost in Space
                    </h2>
                    <p class="text-secondary mx-auto mb-4" style="max-width: 540px; font-size: 1.1rem; line-height: 1.7;">
                        We searched everywhere, but we couldn't find the page you're looking for. It may have been moved, renamed, or is temporarily offline.
                    </p>

                    <!-- Beautiful Search Bar inside 404 page -->
                    <form action="<?= url('products') ?>" method="GET" class="mb-4 mx-auto" style="max-width: 500px;">
                        <div class="input-group shadow-sm border rounded-pill overflow-hidden p-1" style="border-color: var(--border-color) !important; background: var(--bg-surface);">
                            <span class="input-group-text bg-transparent border-0 ps-3 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="q" id="search404Input" class="form-control bg-transparent border-0 px-2 py-2" placeholder="Search premium websites, scripts, plugins..." style="font-size: 0.95rem; outline: none; box-shadow: none; color: var(--text-primary);">
                            <button class="btn rounded-pill px-4 border-0" type="submit" id="search404Btn" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: #fff; font-weight: 600; font-family: var(--font-title); font-size: 0.9rem;">
                                Search
                            </button>
                        </div>
                    </form>

                    <!-- Popular Category Tags -->
                    <div class="mb-4">
                        <span class="d-block small text-muted mb-2 fw-semibold" style="letter-spacing: 0.5px; text-transform: uppercase;">Or browse popular categories:</span>
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <a href="<?= url('products/category/ready-made-websites') ?>" class="badge-cat-404">Ready-made Websites</a>
                            <a href="<?= url('products/category/php-scripts') ?>" class="badge-cat-404">PHP Scripts</a>
                            <a href="<?= url('products/category/custom-websites') ?>" class="badge-cat-404">Custom Websites</a>
                            <a href="<?= url('products') ?>" class="badge-cat-404">All Products</a>
                        </div>
                    </div>

                    <!-- Navigation Action Buttons -->
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mt-2">
                        <a href="<?= url() ?>" class="btn-dc-primary px-4 py-3 rounded-pill shadow" id="btnHome404" style="text-decoration: none;">
                            <i class="bi bi-house-door me-2"></i>Return Home
                        </a>
                        <a href="<?= url('contact') ?>" class="btn-dc-outline px-4 py-3 rounded-pill border" id="btnContact404" style="text-decoration: none; border-color: var(--border-color) !important; background: transparent; color: var(--text-primary);">
                            <i class="bi bi-chat-dots me-2"></i>Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 404 Styles & Animations -->
<style>
.badge-cat-404 {
    background: var(--bg-secondary);
    color: var(--text-secondary);
    padding: 8px 16px;
    border-radius: var(--radius-full);
    font-size: 0.85rem;
    font-weight: 500;
    transition: var(--transition);
    border: 1px solid var(--border-color);
}
.badge-cat-404:hover {
    background: var(--primary-soft);
    color: var(--primary);
    border-color: var(--primary);
    transform: translateY(-1px);
}
@keyframes errorFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}
@keyframes errorParticle {
    0%, 100% { transform: translateY(0) scale(1); opacity: 0.6; }
    50% { transform: translateY(-15px) scale(1.1); opacity: 0.9; }
}
.error-orbit-1 {
    transform-origin: center;
    animation: rotateOrbit 25s linear infinite;
}
.error-orbit-2 {
    transform-origin: center;
    animation: rotateOrbit 18s linear infinite reverse;
}
@keyframes rotateOrbit {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.error-pulse {
    transform-origin: center;
    animation: pulseGlow 3s ease-in-out infinite alternate;
}
@keyframes pulseGlow {
    from { transform: scale(0.98); opacity: 0.08; }
    to { transform: scale(1.02); opacity: 0.15; }
}
</style>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
