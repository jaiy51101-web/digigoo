<?php
/**
 * Digiws - Product Controller
 */

class ProductController {

    public function index(): void {
        $productModel = new Product();
        $categoryModel = new Category();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'search'       => $_GET['q'] ?? '',
            'category_id'  => $_GET['category'] ?? '',
            'product_type' => $_GET['type'] ?? '',
            'sort'         => $_GET['sort'] ?? 'newest',
            'tag'          => $_GET['tag'] ?? '',
            'min_price'    => $_GET['min_price'] ?? '',
            'max_price'    => $_GET['max_price'] ?? ''
        ];

        $result = $productModel->getAll($page, 12, $filters);
        $products = $result['products'];
        $productModel->loadFeatures($products);
        $totalProducts = $result['total'];
        $categories = $categoryModel->getWithProductCount();

        $pageTitle = 'All Products - ' . APP_NAME;
        $metaDesc = 'Browse our collection of premium digital products, website templates, PHP scripts, themes and plugins.';

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/products.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function byCategory(string $slug): void {
        $categoryModel = new Category();
        $productModel = new Product();

        $category = $categoryModel->findBySlug($slug);
        if (!$category) {
            http_response_code(404);
            $pageTitle = '404 - Category Not Found';
            include TEMPLATES_PATH . '/header.php';
            include VIEWS_PATH . '/404.php';
            include TEMPLATES_PATH . '/footer.php';
            return;
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $filters = [
            'category_slug' => $slug,
            'sort'          => $_GET['sort'] ?? 'newest'
        ];

        $result = $productModel->getAll($page, 12, $filters);
        $products = $result['products'];
        $productModel->loadFeatures($products);
        $totalProducts = $result['total'];
        $categories = $categoryModel->getWithProductCount();

        $pageTitle = $category['name'] . ' - ' . APP_NAME;
        $metaDesc = $category['description'] ?? '';

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/products.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function show(string $slug): void {
        $productModel = new Product();
        $reviewModel = new Review();

        $product = $productModel->findBySlug($slug);
        if (!$product) {
            http_response_code(404);
            $pageTitle = '404 - Product Not Found';
            include TEMPLATES_PATH . '/header.php';
            include VIEWS_PATH . '/404.php';
            include TEMPLATES_PATH . '/footer.php';
            return;
        }

        $reviewPage = max(1, (int)($_GET['review_page'] ?? 1));
        $reviewResult = $reviewModel->getProductReviews($product['id'], $reviewPage, 10);
        $reviews = $reviewResult['reviews'];
        $totalReviews = $reviewResult['total'];
        $ratingDist = $reviewModel->getRatingDistribution($product['id']);
        $managedReviews = (new ManagedFeedback())->getFor('product', (int)$product['id']);
        $relatedProducts = (!empty($product['category_id'])) ? $productModel->getRelated($product['id'], (int)$product['category_id'], 4) : [];
        $hasReviewed = isLoggedIn() ? $reviewModel->hasReviewed(currentUserId(), $product['id']) : false;

        $pageTitle = $product['title'] . ' - ' . APP_NAME;
        $metaDesc = $product['short_description'] ?? truncateText(strip_tags($product['description'] ?? ''), 160);

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/product-single.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function search(): void {
        $query = sanitize($_GET['q'] ?? '');
        $productModel = new Product();
        $categoryModel = new Category();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $productModel->getAll($page, 12, ['search' => $query]);
        $products = $result['products'];
        $productModel->loadFeatures($products);
        $totalProducts = $result['total'];
        $categories = $categoryModel->getWithProductCount();

        $pageTitle = 'Search: ' . $query . ' - ' . APP_NAME;

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/products.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    public function submitReview(): void {
        requireLogin();
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect($_SERVER['HTTP_REFERER'] ?? url('products'));
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);
        $title = sanitize($_POST['title'] ?? '');
        $comment = sanitize($_POST['comment'] ?? '');

        if ($productId <= 0 || $rating < 1 || $rating > 5) {
            setFlash('danger', 'Invalid review data.');
            redirect($_SERVER['HTTP_REFERER'] ?? url('products'));
        }

        $reviewModel = new Review();
        if ($reviewModel->hasReviewed(currentUserId(), $productId)) {
            setFlash('warning', 'You have already reviewed this product.');
            redirect($_SERVER['HTTP_REFERER'] ?? url('products'));
        }

        $reviewModel->create([
            'product_id' => $productId,
            'user_id'    => currentUserId(),
            'rating'     => $rating,
            'title'      => $title,
            'comment'    => $comment
        ]);

        setFlash('success', 'Your review has been submitted and is pending approval.');
        redirect($_SERVER['HTTP_REFERER'] ?? url('products'));
    }

    public function quickView(string $id): void {
        header('Content-Type: application/json');
        $productId = (int)$id;
        $productModel = new Product();
        $product = $productModel->find($productId);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
            return;
        }

        $product['categories'] = $productModel->getCategories($productId);
        $firstCatName = !empty($product['categories']) ? $product['categories'][0]['name'] : 'Digital Asset';

        ob_start();
        ?>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="quickview-img-wrap rounded overflow-hidden" style="aspect-ratio: 16/9; background: #f8fafc; border: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: center;">
                    <?php if (!empty($product['thumbnail'])): ?>
                        <img src="<?= uploadUrl($product['thumbnail']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6 d-flex flex-column">
                <h4 style="font-weight: 800; color: #0f172a; margin-bottom: 4px;"><?= htmlspecialchars($product['title']) ?></h4>
                <span class="badge text-bg-light border align-self-start mb-3" style="font-size: 0.75rem; font-weight: 600; color: #475569 !important;"><?= htmlspecialchars($firstCatName) ?></span>
                
                <div class="d-flex align-items-center gap-3 mb-3" style="font-size: 0.85rem; font-weight: 600; color: #475569;">
                    <span class="text-warning"><i class="bi bi-star-fill me-1"></i><?= number_format((float)($product['avg_rating'] ?? 0.0), 1) ?></span>
                    <span><i class="bi bi-eye me-1"></i><?= number_format(getDynamicFakeViews($product)) ?> Views</span>
                    <span><i class="bi bi-download me-1"></i><?= number_format(getDynamicFakeSales($product)) ?> Sales</span>
                </div>

                <p style="font-size: 0.9rem; color: #475569; margin-bottom: 20px; line-height: 1.5; height: 4.5em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                    <?= htmlspecialchars($product['short_description'] ?? '') ?>
                </p>

                <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                    <div>
                        <span style="font-size: 1.45rem; font-weight: 800; color: #0f172a;"><?= formatPrice($product['sale_price']) ?></span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="<?= url('product/' . $product['slug']) ?>" class="btn btn-outline-primary rounded-pill btn-sm px-3" style="font-weight:600; font-size: 0.82rem;">View Full Page</a>
                        <button class="btn btn-primary rounded-pill btn-sm px-3 add-to-cart-btn" data-product-id="<?= $product['id'] ?>" style="font-weight:600; font-size: 0.82rem;"><i class="bi bi-cart-plus me-1"></i>Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $html = ob_get_clean();

        echo json_encode(['success' => true, 'html' => $html]);
    }
}
