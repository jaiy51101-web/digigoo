<?php
/**
 * Digiws - Redesigned Premium Floating Glass Header
 */
$settingModel = new Setting();
$siteName = $settingModel->get('site_name', 'Digiws');
$siteLogo = $settingModel->get('site_logo', '');
$siteFavicon = $settingModel->get('site_favicon', '');
$whatsappNumber = $settingModel->get('whatsapp_number', env('WHATSAPP_NUMBER', '919534702513'));
$whatsappMsg = urlencode($settingModel->get('whatsapp_message', 'Hello!'));
$sitePhone = $settingModel->get('site_phone', '+91 9534702513');
$metaTitle = $pageTitle ?? $settingModel->get('meta_title', APP_NAME);
$metaDescription = $metaDesc ?? $settingModel->get('meta_description', '');
$flash = getFlash();
$navCategories = (new Category())->getAll(true);

// Fix canonical to avoid double prefixing
$parsedAppUrl = parse_url(APP_URL);
$schemeHost = ($parsedAppUrl['scheme'] ?? 'http') . '://' . ($parsedAppUrl['host'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost') . (isset($parsedAppUrl['port']) ? ':' . $parsedAppUrl['port'] : '');
$canonicalUrl = $schemeHost . ($_SERVER['REQUEST_URI'] ?? '/');
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <script>
        (function() {
            let theme = localStorage.getItem('theme_override');
            if (!theme) {
                const hour = new Date().getHours();
                theme = (hour >= 5 && hour < 17) ? 'light' : 'dark';
            }
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <?php 
    $gscCode = $settingModel->get('seo_google_search_console', '-jzwKvTWEpxQy58vZt07OLFpS9dIfqmX3_NTIABLPyI');
    if (!empty($gscCode)): 
    ?>
    <meta name="google-site-verification" content="<?= htmlspecialchars($gscCode) ?>" />
    <?php endif; ?>
    
    <?php 
    $gaId = $settingModel->get('seo_google_analytics', '');
    if (!empty($gaId)): 
    ?>
    <!-- Google Tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($gaId) ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?= htmlspecialchars($gaId) ?>');
    </script>
    <?php endif; ?>

    <?php 
    $adsenseId = $settingModel->get('seo_google_adsense', '');
    if (!empty($adsenseId)): 
    ?>
    <!-- Google AdSense -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?= htmlspecialchars($adsenseId) ?>" crossorigin="anonymous"></script>
    <?php endif; ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($metaTitle) ?></title>
    <meta name="title" content="<?= htmlspecialchars($metaTitle) ?>">
    <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($settingModel->get('meta_keywords', 'digital products, php scripts, themes, templates')) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($metaTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($metaDescription) ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars($siteName) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <?php if ($siteLogo): ?>
    <meta property="og:image" content="<?= uploadUrl($siteLogo) ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($metaTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($metaDescription) ?>">
    
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">

    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.14/dist/dotlottie-wc.js" type="module"></script>

    <?php if ($siteFavicon): ?>
    <link rel="icon" href="<?= uploadUrl($siteFavicon) ?>">
    <?php else: ?>
    <link rel="icon" href="<?= uploadUrl('site/brand_logo.webp') ?>">
    <?php endif; ?>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom Design CSS -->
    <?php $cssTime = file_exists(BASE_PATH . '/assets/css/style.css') ? filemtime(BASE_PATH . '/assets/css/style.css') : time(); ?>
    <link href="<?= asset('css/style.css?v=' . $cssTime) ?>" rel="stylesheet">
    <?php if (str_contains($_SERVER['REQUEST_URI'], '/blog') || str_contains($_SERVER['REQUEST_URI'], '/admin/blog') || str_contains($_SERVER['REQUEST_URI'], '/admin/blog/categories') || str_contains($_SERVER['REQUEST_URI'], '/admin/blog/tags') || str_contains($_SERVER['REQUEST_URI'], '/admin/blog/comments')): ?>
        <?php $blogCssTime = file_exists(BASE_PATH . '/assets/css/blog.css') ? filemtime(BASE_PATH . '/assets/css/blog.css') : time(); ?>
        <link href="<?= asset('css/blog.css?v=' . $blogCssTime) ?>" rel="stylesheet">
    <?php endif; ?>
    
    <script>
        window.APP_BASE_PATH = '<?= rtrim(defined("APP_BASE_PATH") ? APP_BASE_PATH : env("APP_BASE_PATH", ""), "/") ?>';
        // Auto-detect subdirectory on localhost/IP testing
        if (location.hostname === 'localhost' || location.hostname === '127.0.0.1' || location.hostname.startsWith('192.168.')) {
            const pathParts = location.pathname.split('/');
            if (pathParts.length > 2 && pathParts[1] !== 'blog') {
                window.APP_BASE_PATH = '/' + pathParts[1];
            }
        }
        window.appUrl = function(path) {
            return window.APP_BASE_PATH + '/' + path.replace(/^\//, '');
        };
    </script>
    <?php 
    $customHeader = $settingModel->get('seo_header_code', '');
    if (!empty($customHeader)) {
        echo $customHeader;
    }
    ?>
</head>
<body class="<?= isAdmin() ? 'admin-logged-in' : '' ?>">

    <!-- Cursor Glow (AK GRAF system) -->
    <div class="cursor-glow" id="cursorGlow"></div>

    <!-- Aurora Background Layer (AK GRAF system) -->
    <div class="aurora-bg" aria-hidden="true">
        <div class="aurora-layer"></div>
        <div class="aurora-layer"></div>
        <div class="aurora-layer"></div>
        <div class="aurora-layer"></div>
    </div>

<!-- Mobile Drawer Overlay -->
<div class="dc-mobile-drawer-overlay" id="drawerOverlay"></div>

<!-- Mobile Drawer -->
<div class="dc-mobile-drawer" id="mobileDrawer">
    <div class="drawer-header">
        <span class="brand-name"><?= $siteName ?></span>
        <button class="btn-close-drawer" id="closeDrawer">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="drawer-body">
        <span class="drawer-section-label">Navigation</span>
        <a href="<?= url() ?>" class="drawer-menu-item"><i class="bi bi-house-door"></i> Home</a>
        <a href="<?= url('products') ?>" class="drawer-menu-item"><i class="bi bi-grid"></i> Products</a>
        <a href="<?= url('services') ?>" class="drawer-menu-item"><i class="bi bi-gear"></i> Services</a>
        <a href="<?= url('blog') ?>" class="drawer-menu-item"><i class="bi bi-newspaper"></i> Blog</a>
        <a href="<?= url('contact') ?>" class="drawer-menu-item"><i class="bi bi-chat-dots"></i> Contact Us</a>
        
        <span class="drawer-section-label mt-4">Personal</span>
        <a href="<?= url('wishlist') ?>" class="drawer-menu-item"><i class="bi bi-heart"></i> Wishlist</a>
        <a href="<?= url('cart') ?>" class="drawer-menu-item"><i class="bi bi-cart3"></i> My Cart</a>
        <a href="<?= isLoggedIn() ? url('user/dashboard') : url('login') ?>" class="drawer-menu-item"><i class="bi bi-person"></i> <?= isLoggedIn() ? 'My Account' : 'Login / Register' ?></a>
        
        <?php if (isAdmin()): ?>
        <a href="<?= url('admin') ?>" class="drawer-menu-item"><i class="bi bi-speedometer2"></i> Admin Panel</a>
        <?php endif; ?>

        <div class="mt-5 p-3 rounded-4" style="background:var(--bg-secondary); border:1px solid var(--border-color);">
            <p class="small text-muted mb-2">Need help?</p>
            <a href="https://wa.me/<?= $whatsappNumber ?>" class="btn btn-success w-100 rounded-pill py-2">
                <i class="bi bi-whatsapp me-2"></i> WhatsApp Support
            </a>
        </div>
    </div>
</div>

<!-- Header -->
<header class="dc-header">
    <div class="header-top">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between w-100">
                <!-- Logo -->
                <a href="<?= url() ?>" class="navbar-brand" style="display:flex; align-items:center; gap:10px; flex-direction:row !important; text-decoration:none;">
                    <?php if (!empty($siteLogo)): ?>
                        <img src="<?= uploadUrl($siteLogo) ?>" alt="<?= htmlspecialchars($siteName) ?>" style="height:36px; max-width:180px; object-fit:contain; flex-shrink:0;">
                    <?php else: ?>
                        <!-- Custom SVG Logo -->
                        <svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0;">
                            <defs>
                                <linearGradient id="logoGrad" x1="0" y1="0" x2="32" y2="32" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#2563EB" />
                                    <stop offset="100%" stop-color="#7C3AED" />
                                </linearGradient>
                            </defs>
                            <rect x="4" y="4" width="16" height="16" rx="5" fill="url(#logoGrad)" opacity="0.8" />
                            <rect x="12" y="12" width="16" height="16" rx="5" fill="url(#logoGrad)" />
                            <path d="M16 10L20 14L16 18L12 14L16 10Z" fill="#FFFFFF" />
                        </svg>
                        <div class="brand-text">
                            <div class="brand-name" style="margin-bottom:0; font-size:1.45rem; line-height:1.1; color:var(--text-primary); font-weight:700;"><?= htmlspecialchars($siteName) ?></div>
                            <div class="brand-tagline" style="margin-top:2px; font-size:0.62rem; line-height:1; letter-spacing:0.8px; color:var(--text-muted); text-transform:uppercase;">Digital Marketplace</div>
                        </div>
                    <?php endif; ?>
                </a>

                <!-- Combined Search Bar (Desktop Only) -->
                <div class="header-search-container d-none d-lg-block dc-search">
                    <form action="<?= url('products') ?>" method="GET" class="search-group">
                        <input type="text" name="q" id="searchInput" placeholder="Search for premium products..." autocomplete="off" style="padding-left: 16px;">
                        <button type="submit" class="search-submit">
                            <i class="bi bi-search"></i>
                        </button>
                        <div class="search-results" id="searchResults"></div>
                    </form>
                </div>

                <!-- Actions -->
                <div class="header-actions">
                    <!-- Dark/Light Theme Toggle -->
                    <button class="nav-icon-circle" id="themeToggleBtn" aria-label="Toggle theme" title="Toggle Light/Dark Theme" style="border: none; background: transparent; cursor: pointer; color: var(--text-primary); font-size: 1.2rem; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-moon" id="themeToggleIcon"></i>
                    </button>

                    <!-- Wishlist -->
                    <a href="<?= url('wishlist') ?>" class="nav-icon-circle" id="wishlistBtn">
                        <i class="bi bi-heart"></i>
                        <?php if (wishlistCount() > 0): ?>
                        <span class="badge" id="wishlistBadge"><?= wishlistCount() ?></span>
                        <?php endif; ?>
                    </a>

                    <!-- Cart -->
                    <a href="<?= url('cart') ?>" class="nav-icon-circle" id="cartBtn">
                        <i class="bi bi-cart3"></i>
                        <?php if (cartCount() > 0): ?>
                        <span class="badge" id="cartBadge"><?= cartCount() ?></span>
                        <?php endif; ?>
                    </a>

                    <!-- Account / Sign In -->
                    <?php if (isLoggedIn()): ?>
                        <div class="dropdown d-none d-lg-block">
                            <a class="btn-signin-outline dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                <span><?= htmlspecialchars(truncateText($_SESSION['user_name'] ?? '', 10)) ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow-lg border-0" style="margin-top:15px; border-radius:var(--radius-md);">
                                <li><span class="dropdown-item-text fw-bold mb-1"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></span></li>
                                <li><hr class="dropdown-divider opacity-50"></li>
                                <?php if (isAdmin()): ?>
                                <li><a class="dropdown-item py-2" href="<?= url('admin') ?>"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item py-2" href="<?= url('user/dashboard') ?>"><i class="bi bi-grid me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item py-2" href="<?= url('user/orders') ?>"><i class="bi bi-bag-check me-2"></i>My Orders</a></li>
                                <li><hr class="dropdown-divider opacity-50"></li>
                                <li><a class="dropdown-item py-2 text-danger" href="<?= url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= url('login') ?>" class="btn-signin-outline d-none d-lg-block">
                            <i class="bi bi-person"></i> Sign In
                        </a>
                    <?php endif; ?>

                    <!-- Mobile Menu Toggle -->
                    <button class="d-lg-none" id="mobileMenuToggle">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>

    <!-- Header Bottom Navigation -->
    <div class="header-nav-row d-none d-lg-block">
        <div class="container d-flex align-items-center">
            <!-- All Categories Sidebar Toggle -->
            <div class="dropdown">
                <button class="all-cat-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-grid-3x3-gap-fill"></i> All Categories
                </button>
                <ul class="dropdown-menu dropdown-menu-dark shadow-lg border-0" style="min-width: 250px; border-radius: 0 0 var(--radius-md) var(--radius-md);">
                    <?php foreach ($navCategories as $cat): ?>
                    <li><a class="dropdown-item py-2 px-4" href="<?= url('products/category/' . $cat['slug']) ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Nav Links -->
            <nav class="nav-links-row">
                <a href="<?= url() ?>" class="<?= ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php' || $_SERVER['REQUEST_URI'] === '/digiws' || $_SERVER['REQUEST_URI'] === '/digiws/') ? 'active' : '' ?>">Home</a>
                <a href="<?= url('products') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'], '/products') ? 'active' : '' ?>">Products</a>
                <a href="<?= url('services') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'], '/services') ? 'active' : '' ?>">Services</a>
                <a href="<?= url('blog') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'], '/blog') ? 'active' : '' ?>">Blog</a>
                <a href="<?= url('contact') ?>" class="<?= str_contains($_SERVER['REQUEST_URI'], '/contact') ? 'active' : '' ?>">Contact</a>
            </nav>

            <div class="header-nav-right ms-auto d-flex align-items-center gap-4">
                <!-- WhatsApp Support Button -->
                <?php if ($whatsappNumber): ?>
                <div class="header-support-info">
                    <a href="https://wa.me/<?= $whatsappNumber ?>" target="_blank">
                        <i class="bi bi-whatsapp" style="color:#25D366;"></i> Support
                    </a>
                </div>
                <?php endif; ?>


                <!-- Language Switcher -->
                <div class="dropdown header-lang-switcher">
                    <button class="btn-lang dropdown-toggle" type="button" data-bs-toggle="dropdown" style="background:transparent; border:none; color:var(--text-primary); font-family:var(--font-title); font-weight:600; font-size:0.85rem; display:flex; align-items:center;">
                        <?php if (($_COOKIE['currency'] ?? 'INR') === 'USD'): ?>
                        <img src="https://flagcdn.com/w20/us.png" alt="USA" class="me-2" style="height:12px;"> EN / USD
                        <?php else: ?>
                        <img src="https://flagcdn.com/w20/in.png" alt="India" class="me-2" style="height:12px;"> EN / INR
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow border-0" style="border-radius:var(--radius-md);">
                        <li><a class="dropdown-item py-2" href="#" onclick="setCurrency('INR'); return false;"><img src="https://flagcdn.com/w20/in.png" alt="India" class="me-2" style="height:12px;"> EN / INR</a></li>
                        <li><a class="dropdown-item py-2" href="#" onclick="setCurrency('USD'); return false;"><img src="https://flagcdn.com/w20/us.png" alt="USA" class="me-2" style="height:12px;"> English (US)</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Nav (Collapsed) -->
    <div class="d-lg-none" id="mobileNav" style="display:none; padding: 15px; border-top: 1px solid var(--border-color); background:var(--bg-surface);">
        <div class="dc-search-mobile mb-3 dc-search">
            <form action="<?= url('products') ?>" method="GET" class="search-group">
                <input type="text" name="q" class="mobile-search" placeholder="Search..." autocomplete="off" style="width:100%; border:1px solid var(--border-color); padding: 10px 15px; border-radius: var(--radius-full); background:var(--bg-secondary); outline:none;">
                <div class="search-results" id="searchResultsMobile"></div>
            </form>
        </div>
        <div class="d-flex flex-column gap-2">
            <a href="<?= url() ?>" class="nav-link py-2 border-bottom">Home</a>
            <a href="<?= url('products') ?>" class="nav-link py-2 border-bottom">Products</a>
            <a href="<?= url('blog') ?>" class="nav-link py-2 border-bottom">Blog</a>
            <a href="<?= url('contact') ?>" class="nav-link py-2 border-bottom">Contact</a>
            
            <div class="mt-3">
                <p class="text-muted small fw-bold mb-2">LANGUAGE & REGION</p>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="setCurrency('INR')"><img src="https://flagcdn.com/w20/in.png" alt="India" class="me-1" style="height:10px;"> INR</button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="setCurrency('USD')"><img src="https://flagcdn.com/w20/us.png" alt="USA" class="me-1" style="height:10px;"> USD</button>
                </div>
            </div>

            <div class="mt-3 p-3 bg-light rounded-3 d-flex align-items-center justify-content-between border border-primary-subtle" style="border-radius:var(--radius-md) !important;">
                <div>
                    <p class="mb-0 small text-muted">Direct contact?</p>
                    <p class="mb-0 fw-bold">Call Support</p>
                </div>
                <a href="tel:<?= str_replace(' ', '', $sitePhone) ?>" class="btn btn-primary btn-sm rounded-pill" style="background-color: var(--primary); border: none; font-family:var(--font-title); font-weight:600;">
                    <i class="bi bi-telephone-fill"></i> Call
                </a>
            </div>

            <?php if ($whatsappNumber): ?>
            <div class="mt-3 p-3 bg-light rounded-3 d-flex align-items-center justify-content-between" style="border-radius:var(--radius-md) !important;">
                <div>
                    <p class="mb-0 small text-muted">Need help?</p>
                    <p class="mb-0 fw-bold">WhatsApp Support</p>
                </div>
                <a href="https://wa.me/<?= $whatsappNumber ?>?text=<?= $whatsappMsg ?>" class="btn btn-success btn-sm rounded-pill" style="font-family:var(--font-title); font-weight:600;">
                    <i class="bi bi-whatsapp"></i> Chat
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

<!-- Flash Messages -->
<?php if ($flash): ?>
<div class="dc-toast-container" style="position:fixed; top:20px; right:20px; z-index:9999; display:flex; flex-direction:column; gap:10px;">
    <div class="dc-toast <?= $flash['type'] ?>" style="background:var(--bg-surface); border:1px solid var(--border-color); border-left:4px solid <?= $flash['type'] === 'success' ? 'var(--accent)' : 'var(--danger)' ?>; padding:16px 24px; border-radius:var(--radius-md); box-shadow:var(--shadow-md); display:flex; align-items:center; gap:12px; min-width:300px; animation: slideIn 0.3s ease;">
        <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : ($flash['type'] === 'danger' ? 'x-circle-fill' : 'exclamation-circle-fill') ?>" style="color:<?= $flash['type'] === 'success' ? 'var(--accent)' : 'var(--danger)' ?>; font-size:1.2rem;"></i>
        <span style="font-family:var(--font-body); font-size:0.9rem; font-weight:600; color:var(--text-primary);"><?= $flash['message'] ?></span>
    </div>
</div>
<style>
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
</style>
<?php endif; ?>

<main>

<script>
function setCurrency(currency) {
    document.cookie = "currency=" + currency + "; path=/; max-age=" + (365*24*60*60);
    window.location.reload();
}

if (!document.cookie.includes('currency=')) {
    fetch('https://ipapi.co/json/')
      .then(res => res.json())
      .then(data => {
          if (data && data.country_code) {
              var newCurrency = (data.country_code === 'IN') ? 'INR' : 'USD';
              document.cookie = "currency=" + newCurrency + "; path=/; max-age=" + (365*24*60*60);
              if (newCurrency === 'USD') window.location.reload();
          }
      }).catch(err => console.log('IP detect error:', err));
}
</script>
