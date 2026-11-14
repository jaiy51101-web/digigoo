<?php
/**
 * HTML Sitemap View
 */
?>
<style>
    .sitemap-hero {
        background: radial-gradient(circle at 80% 20%, rgba(37, 99, 235, 0.05) 0%, transparent 50%),
                    radial-gradient(circle at 10% 80%, rgba(124, 58, 237, 0.05) 0%, transparent 50%);
        padding: 60px 0 40px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.04);
        position: relative;
    }
    .sitemap-title {
        font-size: 2.5rem;
        font-weight: 800;
        letter-spacing: -1px;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 12px;
    }
    .sitemap-subtitle {
        font-size: 1.05rem;
        color: #64748b;
        max-width: 600px;
    }
    .sitemap-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 16px;
        padding: 24px;
        height: 100%;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.01);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .sitemap-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04), 0 4px 6px -2px rgba(0,0,0,0.02);
    }
    .sitemap-card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .sitemap-card-title i {
        color: #2563eb;
    }
    .sitemap-links-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .sitemap-links-list li {
        margin-bottom: 10px;
    }
    .sitemap-link-item {
        color: #475569;
        text-decoration: none;
        font-size: 0.92rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: color 0.15s ease, padding-left 0.15s ease;
    }
    .sitemap-link-item::before {
        content: "\F285";
        font-family: "bootstrap-icons";
        font-size: 0.75rem;
        color: #94a3b8;
        transition: color 0.15s ease;
    }
    .sitemap-link-item:hover {
        color: #2563eb;
        padding-left: 4px;
    }
    .sitemap-link-item:hover::before {
        color: #2563eb;
    }
    .sitemap-stats-badge {
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 9999px;
        background: #f1f5f9;
        color: #64748b;
        font-weight: 600;
        margin-left: auto;
    }
</style>

<div class="sitemap-hero">
    <div class="container">
        <h1 class="sitemap-title">Website Sitemap</h1>
        <p class="sitemap-subtitle">Find your way around our premium digital assets marketplace. Browse products, explore blog articles, and access support pages directly.</p>
    </div>
</div>

<div class="container my-5">
    <div class="row g-4">
        <!-- 1. Main Pages -->
        <div class="col-lg-4 col-md-6">
            <div class="sitemap-card">
                <h3 class="sitemap-card-title">
                    <i class="bi bi-house-door-fill"></i> Main Pages
                </h3>
                <ul class="sitemap-links-list">
                    <?php foreach ($standardPages as $p): ?>
                        <li>
                            <a href="<?= $p['url'] ?>" class="sitemap-link-item"><?= htmlspecialchars($p['title']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- 2. Marketplace Categories -->
        <div class="col-lg-4 col-md-6">
            <div class="sitemap-card">
                <h3 class="sitemap-card-title">
                    <i class="bi bi-grid-fill"></i> Product Categories
                    <span class="sitemap-stats-badge"><?= count($categories) ?></span>
                </h3>
                <ul class="sitemap-links-list">
                    <?php if (empty($categories)): ?>
                        <li class="text-muted small">No categories found.</li>
                    <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="<?= $cat['url'] ?>" class="sitemap-link-item"><?= htmlspecialchars($cat['title']) ?></a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- 3. Blog Categories -->
        <div class="col-lg-4 col-md-6">
            <div class="sitemap-card">
                <h3 class="sitemap-card-title">
                    <i class="bi bi-journal-bookmark-fill"></i> Blog Categories
                    <span class="sitemap-stats-badge"><?= count($blogCategories) ?></span>
                </h3>
                <ul class="sitemap-links-list">
                    <?php if (empty($blogCategories)): ?>
                        <li class="text-muted small">No blog categories found.</li>
                    <?php else: ?>
                        <?php foreach ($blogCategories as $bcat): ?>
                            <li>
                                <a href="<?= $bcat['url'] ?>" class="sitemap-link-item"><?= htmlspecialchars($bcat['title']) ?></a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- 4. Other Legal & Custom Pages -->
        <?php if (!empty($customPages)): ?>
            <div class="col-lg-4 col-md-6">
                <div class="sitemap-card">
                    <h3 class="sitemap-card-title">
                        <i class="bi bi-file-earmark-text-fill"></i> Extra Information Pages
                        <span class="sitemap-stats-badge"><?= count($customPages) ?></span>
                    </h3>
                    <ul class="sitemap-links-list">
                        <?php foreach ($customPages as $p): ?>
                            <li>
                                <a href="<?= $p['url'] ?>" class="sitemap-link-item"><?= htmlspecialchars($p['title']) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <!-- 5. Active Products -->
        <div class="col-lg-4 col-md-6">
            <div class="sitemap-card">
                <h3 class="sitemap-card-title">
                    <i class="bi bi-box-seam-fill"></i> Premium Products
                    <span class="sitemap-stats-badge"><?= count($products) ?></span>
                </h3>
                <ul class="sitemap-links-list" style="max-height: 350px; overflow-y: auto; padding-right: 8px;">
                    <?php if (empty($products)): ?>
                        <li class="text-muted small">No active products found.</li>
                    <?php else: ?>
                        <?php foreach ($products as $prod): ?>
                            <li>
                                <a href="<?= $prod['url'] ?>" class="sitemap-link-item" title="<?= htmlspecialchars($prod['title']) ?>">
                                    <?= htmlspecialchars(strlen($prod['title']) > 40 ? substr($prod['title'], 0, 37) . '...' : $prod['title']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- 6. Blog Posts -->
        <div class="col-lg-4 col-md-6">
            <div class="sitemap-card">
                <h3 class="sitemap-card-title">
                    <i class="bi bi-blockquote-left"></i> Latest Articles
                    <span class="sitemap-stats-badge"><?= count($blogPosts) ?></span>
                </h3>
                <ul class="sitemap-links-list" style="max-height: 350px; overflow-y: auto; padding-right: 8px;">
                    <?php if (empty($blogPosts)): ?>
                        <li class="text-muted small">No articles found.</li>
                    <?php else: ?>
                        <?php foreach ($blogPosts as $post): ?>
                            <li>
                                <a href="<?= $post['url'] ?>" class="sitemap-link-item" title="<?= htmlspecialchars($post['title']) ?>">
                                    <?= htmlspecialchars(strlen($post['title']) > 40 ? substr($post['title'], 0, 37) . '...' : $post['title']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
