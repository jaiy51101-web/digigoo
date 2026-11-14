<?php
// Resolve currentCategoryId
$currentCategoryId = $_GET['category'] ?? (isset($category) ? $category['id'] : '');

// If category is filtered via GET, resolve its name for title display
if (!empty($currentCategoryId) && !isset($category) && !empty($categories)) {
    foreach ($categories as $cat) {
        if ($cat['id'] == $currentCategoryId) {
            $category = $cat;
            break;
        }
    }
}

// Determine page title
$pageHeading = 'All Products';
$pageSubtitle = 'Browse our entire collection of premium digital products';

if (isset($category) && !empty($category['name'])) {
    $pageHeading = htmlspecialchars($category['name']);
    $pageSubtitle = 'Premium ' . htmlspecialchars($category['name']) . ' crafted for developers & creators';
} elseif (isset($query) && !empty($query)) {
    $pageHeading = 'Results for "' . htmlspecialchars($query) . '"';
    $pageSubtitle = 'Showing search results across our entire marketplace';
}

// Active filters for tags display
$activeFilters = [];
if (!empty($currentCategoryId) && isset($category)) {
    $activeFilters['category'] = 'Category: ' . $category['name'];
}
if (!empty($_GET['type'])) {
    $types = [
        'ready_website'  => 'Ready-made Websites',
        'custom_website' => 'Custom Websites',
        'php_script'     => 'PHP Scripts',
        'html_template'  => 'HTML Templates',
        'theme'          => 'Themes',
        'plugin'         => 'Plugins',
    ];
    $activeFilters['type'] = 'Type: ' . ($types[$_GET['type']] ?? $_GET['type']);
}
if (!empty($_GET['min_price']) || !empty($_GET['max_price'])) {
    $activeFilters['price'] = 'Price: ₹' . ($_GET['min_price'] ?? '0') . ' – ₹' . ($_GET['max_price'] ?? '∞');
}
if (!empty($_GET['q'])) $activeFilters['q'] = 'Search: "' . htmlspecialchars($_GET['q']) . '"';
?>

<!-- Main Content -->
<section class="dc-section" style="padding-top:50px; padding-bottom:60px;">
    <div class="container">

        <!-- Mobile Filter Toggle -->
        <div class="d-lg-none mb-3">
            <button class="btn-dc-outline w-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterSidebar" style="padding:12px 20px;">
                <i class="bi bi-funnel me-2"></i>Filter Products
                <?php if (!empty($activeFilters)): ?>
                    <span style="background:var(--primary);color:#fff;border-radius:var(--radius-full);padding:1px 8px;font-size:0.72rem;margin-left:6px;"><?= count($activeFilters) ?></span>
                <?php endif; ?>
            </button>
        </div>

        <div class="row g-4">
            <!-- ========================
                 SIDEBAR FILTERS
                 ======================== -->
            <div class="col-lg-3">
                <!-- Offcanvas for mobile -->
                <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="filterSidebar">
                    <div class="offcanvas-header d-lg-none">
                        <h5 class="offcanvas-title fw-800"><i class="bi bi-funnel me-2"></i>Filters</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#filterSidebar"></button>
                    </div>
                    <div class="offcanvas-body p-0">
                        <div class="dc-filters">
                            <h5><i class="bi bi-funnel"></i> Filters</h5>

                            <form id="filterForm" method="GET" action="<?= url('products') ?>">
                                <?php if (!empty($_GET['q'])): ?>
                                    <input type="hidden" name="q" value="<?= htmlspecialchars($_GET['q']) ?>">
                                <?php endif; ?>

                                <!-- Categories -->
                                <div class="filter-group">
                                    <h6>Categories</h6>
                                    <label class="filter-option">
                                        <input type="radio" name="category" value="" <?= empty($currentCategoryId) ? 'checked' : '' ?>>
                                        <span>All Categories</span>
                                        <span class="count"><?= number_format($totalProducts ?? 0) ?></span>
                                    </label>
                                    <?php foreach ($categories as $cat): ?>
                                    <label class="filter-option">
                                        <input type="radio" name="category" value="<?= $cat['id'] ?>" <?= $currentCategoryId == $cat['id'] ? 'checked' : '' ?>>
                                        <span><?= htmlspecialchars($cat['name']) ?></span>
                                        <span class="count"><?= $cat['product_count'] ?? 0 ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Product Type -->
                                <div class="filter-group">
                                    <h6>Product Type</h6>
                                    <label class="filter-option">
                                        <input type="radio" name="type" value="" <?= empty($_GET['type']) ? 'checked' : '' ?>>
                                        <span>All Types</span>
                                    </label>
                                    <?php
                                    $types = [
                                        'ready_website'  => 'Ready-made Websites',
                                        'custom_website' => 'Custom Websites',
                                        'php_script'     => 'PHP Scripts',
                                        'html_template'  => 'HTML Templates',
                                        'theme'          => 'Themes',
                                        'plugin'         => 'Plugins',
                                    ];
                                    foreach ($types as $key => $label): ?>
                                    <label class="filter-option">
                                        <input type="radio" name="type" value="<?= $key ?>" <?= ($_GET['type'] ?? '') === $key ? 'checked' : '' ?>>
                                        <span><?= $label ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Price Range -->
                                <div class="filter-group">
                                    <h6>Price Range</h6>
                                    <div class="d-flex gap-2">
                                        <input type="number" name="min_price" class="dc-form-control" placeholder="Min ₹" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>" style="padding:8px 10px;">
                                        <input type="number" name="max_price" class="dc-form-control" placeholder="Max ₹" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>" style="padding:8px 10px;">
                                    </div>
                                </div>

                                <!-- Sort (hidden on mobile sidebar to avoid duplication) -->
                                <?php if (!empty($_GET['sort'])): ?>
                                <input type="hidden" name="sort" value="<?= htmlspecialchars($_GET['sort']) ?>">
                                <?php endif; ?>

                                <!-- Filter Actions -->
                                <div class="filter-actions">
                                    <button type="submit" class="btn-dc-primary btn-dc-sm" data-bs-dismiss="offcanvas" data-bs-target="#filterSidebar">
                                        <i class="bi bi-funnel-fill"></i> Apply Filters
                                    </button>
                                    <a href="<?= url('products') ?>" class="btn-dc-dark btn-dc-sm text-center">
                                        <i class="bi bi-x-circle"></i> Clear All
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================
                 PRODUCT GRID
                 ======================== -->
            <div class="col-lg-9">

                <!-- Active Filter Tags -->
                <?php if (!empty($activeFilters)): ?>
                <div class="active-filters">
                    <?php foreach ($activeFilters as $key => $label): ?>
                    <span class="active-filter-tag">
                        <?= htmlspecialchars($label) ?>
                        <button onclick="window.location.href='<?= url('products?' . http_build_query(array_diff_key($_GET, [$key => '']))) ?>'">
                            <i class="bi bi-x"></i>
                        </button>
                    </span>
                    <?php endforeach; ?>
                    <a href="<?= url('products') ?>" style="font-size:0.78rem;font-weight:700;color:var(--text-muted);display:inline-flex;align-items:center;gap:4px;padding:4px 8px;">
                        <i class="bi bi-x-circle"></i> Clear all
                    </a>
                </div>
                <?php endif; ?>

                <!-- Sort Bar -->
                <div class="dc-sort-bar">
                    <div class="results-info">
                        <h4><?= $pageHeading ?></h4>
                        <p>Showing <?= count($products) ?> of <?= number_format($totalProducts ?? 0) ?> products</p>
                    </div>
                    <div class="sort-controls">
                        <label><i class="bi bi-sort-down me-1"></i>Sort:</label>
                        <?php
                        $currentUrl = strtok($_SERVER['REQUEST_URI'], '?');
                        $params = $_GET;
                        $sorts = [
                            'newest'     => '✦ Newest',
                            'popular'    => '🔥 Popular',
                            'price_low'  => '↑ Price Low',
                            'price_high' => '↓ Price High',
                            'rating'     => '⭐ Top Rated',
                        ];
                        ?>
                        <select class="dc-form-control" onchange="window.location.href=this.value" style="width:auto;min-width:150px;">
                            <?php foreach ($sorts as $key => $label):
                                $params['sort'] = $key;
                            ?>
                            <option value="<?= $currentUrl . '?' . http_build_query($params) ?>" <?= ($_GET['sort'] ?? 'newest') === $key ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Product Grid or Empty State -->
                <?php if (empty($products)): ?>
                <div class="dc-empty-state dc-reveal">
                    <div class="empty-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h5>No products found</h5>
                    <p>We couldn't find any products matching your criteria.<br>Try adjusting filters or search with different keywords.</p>
                    <a href="<?= url('products') ?>" class="btn-dc-primary">
                        <i class="bi bi-grid-3x3-gap me-2"></i>Browse All Products
                    </a>
                </div>

                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                    <div class="col-6 col-md-4">
                        <?php include VIEWS_PATH . '/partials/product-card.php'; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if (($totalProducts ?? 0) > 12): ?>
                <div class="mt-4 d-flex justify-content-center">
                    <?= pagination($totalProducts, 12, $page ?? 1, strtok($_SERVER['REQUEST_URI'], '?')) ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>

            </div><!-- /.col-lg-9 -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</section>

<!-- Auto-Submit Filters on Desktop Change -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    if (!filterForm) return;

    // Auto-submit form on desktop when changing filter options
    if (window.innerWidth >= 992) {
        const inputs = filterForm.querySelectorAll('input[type="radio"], input[type="number"]');
        inputs.forEach(input => {
            if (input.type === 'radio') {
                input.addEventListener('change', () => {
                    filterForm.submit();
                });
            } else if (input.type === 'number') {
                // Submit when user changes input focus
                input.addEventListener('change', () => {
                    filterForm.submit();
                });
                // Submit on Enter press
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        filterForm.submit();
                    }
                });
            }
        });
    }
});
</script>
