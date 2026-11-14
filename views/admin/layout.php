<?php
/**
 * Admin Layout Template — Digiws v2
 */
$currentPath = $_SERVER['REQUEST_URI'];
$adminPage   = $adminPage ?? 'dashboard';

// Determine page display name for breadcrumb
$pageTitles = [
    'dashboard'       => 'Dashboard',
    'products'        => 'Products',
    'categories'      => 'Categories',
    'orders'          => 'Orders',
    'coupons'         => 'Coupons',
    'customers'       => 'Customers',
    'reviews'         => 'Reviews',
    'email_templates' => 'Email Templates',
    'payment_settings'=> 'Payment Settings',
    'settings'        => 'Settings',
    'website_requests'=> 'Website Requests',
    'blog'            => 'Blog Posts',
    'contact_messages'=> 'Contact Messages',
];
$currentPageLabel = $pageTitles[$adminPage] ?? ucfirst(str_replace('_',' ',$adminPage));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin — Digiws' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="admin-body">

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="admin-wrapper">
    <!-- ====================================================
         SIDEBAR
         ==================================================== -->
    <aside class="admin-sidebar" id="adminSidebar">

        <!-- Brand / Logo Button -->
        <button class="sidebar-brand-btn" id="sidebarBrandBtn" title="Toggle Menu" aria-label="Toggle sidebar" data-title="Digiws Admin">
            <div class="sidebar-logo-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"/>
                    <line x1="12" y1="22" x2="12" y2="15.5"/>
                    <polyline points="22 8.5 12 15.5 2 8.5"/>
                </svg>
            </div>
            <div class="sidebar-brand-text">
                <h4>Digiws</h4>
                <span>Admin Panel</span>
            </div>
            <span class="sidebar-menu-icon" id="sidebarMenuIcon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </button>

        <!-- Navigation -->
        <nav class="sidebar-nav" id="sidebarNav">

            <!-- Main -->
            <div class="nav-group">
                <span class="nav-group-label">Overview</span>
                <a href="<?= url('admin') ?>" class="sidebar-link <?= $adminPage === 'dashboard' ? 'active' : '' ?>" data-title="Dashboard" data-icon="dashboard">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- Catalog -->
            <div class="nav-group">
                <span class="nav-group-label">Catalog</span>
                <a href="<?= url('admin/products') ?>" class="sidebar-link <?= $adminPage === 'products' ? 'active' : '' ?>" data-title="Products" data-icon="products">
                    <i class="bi bi-box-seam"></i>
                    <span>Products</span>
                </a>
                <a href="<?= url('admin/categories') ?>" class="sidebar-link <?= $adminPage === 'categories' ? 'active' : '' ?>" data-title="Categories" data-icon="categories">
                    <i class="bi bi-tags"></i>
                    <span>Categories</span>
                </a>
                <a href="<?= url('admin/product-design') ?>" class="sidebar-link <?= $adminPage === 'product_design' ? 'active' : '' ?>" data-title="Product Card Design" data-icon="design">
                    <i class="bi bi-palette"></i>
                    <span>Product Card Design</span>
                </a>
                <a href="<?= url('admin/website-requests') ?>" class="sidebar-link <?= $adminPage === 'website_requests' ? 'active' : '' ?>" data-title="Custom Orders" data-icon="orders">
                    <i class="bi bi-code-slash"></i>
                    <span>Custom Orders</span>
                    <?php
                    try {
                        $db2 = Database::getInstance();
                        $cnt = $db2->fetch("SELECT COUNT(*) as c FROM website_requests WHERE status='pending'")['c'] ?? 0;
                        if ($cnt > 0) echo "<span class=\"nav-badge\">$cnt</span>";
                    } catch(Exception $e) {}
                    ?>
                </a>
            </div>
            <div class="nav-divider"></div>

            <!-- Content / Blog -->
            <div class="nav-group">
                <span class="nav-group-label">Blog CMS</span>
                <a href="<?= url('admin/blog') ?>" class="sidebar-link <?= $adminPage === 'blog' ? 'active' : '' ?>" data-title="Blog Posts" data-icon="blog">
                    <i class="bi bi-newspaper"></i>
                    <span>Blog Posts</span>
                </a>
                <a href="<?= url('admin/blog/categories') ?>" class="sidebar-link <?= $adminPage === 'blog_categories' ? 'active' : '' ?>" data-title="Blog Categories" data-icon="blog-categories">
                    <i class="bi bi-collection"></i>
                    <span>Categories</span>
                </a>
                <a href="<?= url('admin/blog/tags') ?>" class="sidebar-link <?= $adminPage === 'blog_tags' ? 'active' : '' ?>" data-title="Blog Tags" data-icon="blog-tags">
                    <i class="bi bi-tags"></i>
                    <span>Tags</span>
                </a>
                <a href="<?= url('admin/blog/comments') ?>" class="sidebar-link <?= $adminPage === 'blog_comments' ? 'active' : '' ?>" data-title="Blog Comments" data-icon="comments">
                    <i class="bi bi-chat-left-dots"></i>
                    <span>Comments</span>
                    <?php
                    try {
                        $db2 = Database::getInstance();
                        $cnt = $db2->fetchColumn("SELECT COUNT(*) FROM blog_comments WHERE status='pending'");
                        if ($cnt > 0) echo "<span class=\"nav-badge\">{$cnt}</span>";
                    } catch(Exception $e) {}
                    ?>
                </a>
                <a href="<?= url('admin/blog/analytics') ?>" class="sidebar-link <?= $adminPage === 'blog_analytics' ? 'active' : '' ?>" data-title="Blog Analytics" data-icon="analytics">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Analytics</span>
                </a>
            </div>
            <div class="nav-divider"></div>

            <!-- Sales -->
            <div class="nav-group">
                <span class="nav-group-label">Sales</span>
                <a href="<?= url('admin/orders') ?>" class="sidebar-link <?= $adminPage === 'orders' ? 'active' : '' ?>" data-title="Orders" data-icon="orders">
                    <i class="bi bi-bag-check"></i>
                    <span>Orders</span>
                </a>
                <a href="<?= url('admin/coupons') ?>" class="sidebar-link <?= $adminPage === 'coupons' ? 'active' : '' ?>" data-title="Coupons" data-icon="coupons">
                    <i class="bi bi-ticket-perforated"></i>
                    <span>Coupons</span>
                </a>
            </div>
            <div class="nav-divider"></div>

            <!-- Marketing Automation -->
            <div class="nav-group">
                <span class="nav-group-label">Marketing</span>
                <a href="<?= url('admin/marketing/dashboard') ?>" class="sidebar-link <?= str_starts_with($adminPage, 'marketing_') && $adminPage === 'marketing_dashboard' ? 'active' : '' ?>" data-title="Marketing Dashboard" data-icon="marketing">
                    <i class="bi bi-megaphone"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= url('admin/marketing/subscribers') ?>" class="sidebar-link <?= $adminPage === 'marketing_subscribers' ? 'active' : '' ?>" data-title="Subscribers" data-icon="subscribers">
                    <i class="bi bi-people"></i>
                    <span>Subscribers</span>
                </a>
                <a href="<?= url('admin/marketing/campaigns') ?>" class="sidebar-link <?= $adminPage === 'marketing_campaigns' ? 'active' : '' ?>" data-title="Campaigns" data-icon="campaigns">
                    <i class="bi bi-envelope-check"></i>
                    <span>Campaigns</span>
                </a>
                <a href="<?= url('admin/marketing/templates') ?>" class="sidebar-link <?= $adminPage === 'marketing_templates' ? 'active' : '' ?>" data-title="Templates" data-icon="templates">
                    <i class="bi bi-layout-wtf"></i>
                    <span>Templates</span>
                </a>
                <a href="<?= url('admin/marketing/automations') ?>" class="sidebar-link <?= $adminPage === 'marketing_automations' ? 'active' : '' ?>" data-title="Automations" data-icon="automations">
                    <i class="bi bi-diagram-3"></i>
                    <span>Automations</span>
                </a>
                <a href="<?= url('admin/marketing/smtps') ?>" class="sidebar-link <?= $adminPage === 'marketing_smtps' ? 'active' : '' ?>" data-title="SMTP Accounts" data-icon="smtps">
                    <i class="bi bi-server"></i>
                    <span>SMTP Accounts</span>
                </a>
                <a href="<?= url('admin/marketing/whatsapp-campaign') ?>" class="sidebar-link <?= $adminPage === 'marketing_whatsapp_campaign' ? 'active' : '' ?>" data-title="WhatsApp Campaign" data-icon="whatsapp">
                    <i class="bi bi-whatsapp"></i>
                    <span>WhatsApp Campaign</span>
                </a>
            </div>
            <div class="nav-divider"></div>

            <!-- Support -->
            <div class="nav-group">
                <span class="nav-group-label">Support</span>
                <a href="<?= url('admin/contact-messages') ?>" class="sidebar-link <?= $adminPage === 'contact_messages' ? 'active' : '' ?>" data-title="Contact Messages" data-icon="messages">
                    <i class="bi bi-envelope-open"></i>
                    <span>Contact Messages</span>
                    <?php
                    try {
                        $db2 = Database::getInstance();
                        $cnt = $db2->fetchColumn("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
                        if ($cnt > 0) echo "<span class=\"nav-badge\">{$cnt}</span>";
                    } catch(Exception $e) {}
                    ?>
                </a>
            </div>
            <div class="nav-divider"></div>

            <!-- People -->
            <div class="nav-group">
                <span class="nav-group-label">People</span>
                <a href="<?= url('admin/customers') ?>" class="sidebar-link <?= $adminPage === 'customers' ? 'active' : '' ?>" data-title="Customers" data-icon="customers">
                    <i class="bi bi-people"></i>
                    <span>Customers</span>
                </a>
                <a href="<?= url('admin/reviews') ?>" class="sidebar-link <?= $adminPage === 'reviews' ? 'active' : '' ?>" data-title="Reviews" data-icon="reviews">
                    <i class="bi bi-star"></i>
                    <span>Reviews</span>
                </a>
            </div>
            <div class="nav-divider"></div>

            <!-- System -->
            <div class="nav-group">
                <span class="nav-group-label">System</span>
                <a href="<?= url('admin/email-templates') ?>" class="sidebar-link <?= $adminPage === 'email_templates' ? 'active' : '' ?>" data-title="Email Templates" data-icon="email-templates">
                    <i class="bi bi-envelope-paper"></i>
                    <span>Email Templates</span>
                </a>
                <a href="<?= url('admin/payment-settings') ?>" class="sidebar-link <?= $adminPage === 'payment_settings' ? 'active' : '' ?>" data-title="Payment Settings" data-icon="payment">
                    <i class="bi bi-credit-card-2-front"></i>
                    <span>Payment Settings</span>
                </a>
                <a href="<?= url('admin/settings') ?>" class="sidebar-link <?= $adminPage === 'settings' ? 'active' : '' ?>" data-title="Settings" data-icon="settings">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
                <a href="<?= url('admin/updates') ?>" class="sidebar-link <?= $adminPage === 'system_updates' ? 'active' : '' ?>" data-title="Update Center" data-icon="updates">
                    <i class="bi bi-cloud-arrow-up-fill text-primary"></i>
                    <span>Update Center</span>
                </a>
            </div>
        </nav>

        <!-- Footer -->
        <div class="sidebar-footer">
            <a href="<?= url() ?>" class="sidebar-link" target="_blank" rel="noopener" data-title="View Store" data-icon="marketing">
                <i class="bi bi-globe2"></i>
                <span>View Store</span>
            </a>
            <a href="<?= url('admin/logout') ?>" class="sidebar-link logout-link" data-title="Logout" data-icon="logout">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- ====================================================
         MAIN CONTENT
         ==================================================== -->
    <div class="admin-main">

        <!-- Topbar -->
        <header class="admin-topbar">
            <div class="topbar-left">
                <!-- Mobile toggle -->
                <button class="sidebar-toggle" onclick="openSidebar()" aria-label="Open menu">
                    <i class="bi bi-list"></i>
                </button>

                <div>
                    <h5 class="topbar-title"><?= $currentPageLabel ?></h5>
                    <div class="topbar-breadcrumb d-none d-md-flex">
                        <i class="bi bi-house" style="font-size:0.75rem;"></i>
                        <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
                        <span><?= $currentPageLabel ?></span>
                    </div>
                </div>
            </div>

            <div class="topbar-right">
                <a href="<?= url() ?>" class="topbar-icon-btn" target="_blank" title="View Store">
                    <i class="bi bi-globe2"></i>
                </a>
                <a href="<?= url('admin/settings') ?>" class="topbar-icon-btn" title="Settings">
                    <i class="bi bi-gear"></i>
                </a>
                <a href="<?= url('admin/settings') ?>" class="topbar-profile" title="Admin Profile">
                    <div class="profile-avatar">
                        <?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?>
                    </div>
                    <span class="profile-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
                    <i class="bi bi-chevron-down" style="font-size:0.65rem; color:var(--text-muted); margin-left:2px;"></i>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php $flash = getFlash(); if ($flash): ?>
        <div class="dc-toast-container">
            <div class="dc-toast <?= $flash['type'] ?>">
                <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill' ?>"></i>
                <span><?= $flash['message'] ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Page Content -->
        <div class="admin-content">
            <?php
            if ($adminPage === 'dashboard') {
                include VIEWS_PATH . '/admin/dashboard.php';
            } elseif (isset($contentView) && file_exists($contentView)) {
                include $contentView;
            }
            ?>
        </div>
    </div><!-- /.admin-main -->
</div><!-- /.admin-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('js/admin.js') ?>"></script>
<script>
// Toast auto-dismiss
setTimeout(() => {
    document.querySelectorAll('.dc-toast').forEach(t => {
        t.style.transition = 'opacity 0.5s';
        t.style.opacity = '0';
        setTimeout(() => t.remove(), 500);
    });
}, 4000);

// Sidebar open/close for mobile
function openSidebar() {
    document.getElementById('adminSidebar').classList.add('show');
    document.getElementById('sidebarOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    document.getElementById('adminSidebar').classList.remove('show');
    document.getElementById('sidebarOverlay').classList.remove('show');
    document.body.style.overflow = '';
}

// Keep collapsed-sidebar tooltips outside the scrollable navigation so they
// remain visible beside every icon.
(() => {
    const sidebar = document.getElementById('adminSidebar');
    const tooltip = document.createElement('div');
    tooltip.className = 'admin-menu-tooltip';
    tooltip.setAttribute('role', 'tooltip');
    document.body.appendChild(tooltip);

    const hideTooltip = () => tooltip.classList.remove('is-visible');

    sidebar.querySelectorAll('[data-title]').forEach((item) => {
        item.addEventListener('mouseenter', () => {
            if (!sidebar.classList.contains('collapsed')) return;

            const rect = item.getBoundingClientRect();
            tooltip.textContent = item.dataset.title;
            tooltip.style.left = `${rect.right + 10}px`;
            tooltip.style.top = `${rect.top + (rect.height / 2)}px`;
            tooltip.classList.add('is-visible');
        });
        item.addEventListener('mouseleave', hideTooltip);
        item.addEventListener('focus', () => {
            if (!sidebar.classList.contains('collapsed')) return;

            const rect = item.getBoundingClientRect();
            tooltip.textContent = item.dataset.title;
            tooltip.style.left = `${rect.right + 10}px`;
            tooltip.style.top = `${rect.top + (rect.height / 2)}px`;
            tooltip.classList.add('is-visible');
        });
        item.addEventListener('blur', hideTooltip);
    });

    sidebar.addEventListener('scroll', hideTooltip, { passive: true });
})();
</script>
</body>
</html>
