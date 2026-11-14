<?php
/**
 * Digiws - Admin Review Controller
 */

class AdminReviewController {
    public function __construct() { requireAdmin(); }

    public function index(): void {
        $reviewModel = new Review();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $status = $_GET['status'] ?? '';
        $result = $reviewModel->getAll($page, 20, $status);
        $reviews = $result['reviews'];
        $totalReviews = $result['total'];
        $managedFeedback = (new ManagedFeedback())->getAll();
        $products = (new Product())->getAll(1, 250)['products'] ?? [];
        $posts = (new Post())->getAll(1, 250, ['all_status' => true])['posts'] ?? [];
        
        $db = Database::getInstance();
        try {
            $configuredProducts = $db->fetchAll("SELECT id, title, fake_views_min, fake_views_max, fake_sales_min, fake_sales_max FROM products WHERE fake_views_min IS NOT NULL OR fake_sales_min IS NOT NULL ORDER BY title ASC");
        } catch (Exception $e) {
            try {
                $db->query("ALTER TABLE products 
                    ADD COLUMN fake_views_min INT DEFAULT NULL,
                    ADD COLUMN fake_views_max INT DEFAULT NULL,
                    ADD COLUMN fake_sales_min INT DEFAULT NULL,
                    ADD COLUMN fake_sales_max INT DEFAULT NULL
                ");
                $configuredProducts = $db->fetchAll("SELECT id, title, fake_views_min, fake_views_max, fake_sales_min, fake_sales_max FROM products WHERE fake_views_min IS NOT NULL OR fake_sales_min IS NOT NULL ORDER BY title ASC");
            } catch (Exception $ex) {
                $configuredProducts = [];
                error_log("Failed auto-migration for products fake columns: " . $ex->getMessage());
            }
        }

        $pageTitle = 'Reviews - Admin';
        $adminPage = 'reviews';
        $contentView = VIEWS_PATH . '/admin/reviews.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function storeManaged(): void {
        if (!verifyCsrf()) { setFlash('danger', 'Invalid request.'); redirect(url('admin/reviews')); }
        $type      = $_POST['target_type'] ?? '';
        $targetId  = (int)($_POST['target_id'] ?? 0);
        $ratingMode = $_POST['rating'] ?? 'auto';
        $role      = sanitize($_POST['reviewer_role'] ?? '');

        if (!in_array($type, ['product', 'blog'], true) || !$targetId) {
            setFlash('danger', 'Select a valid target.'); redirect(url('admin/reviews'));
        }
        $exists = $type === 'product' ? (new Product())->find($targetId) : (new Post())->find($targetId);
        if (!$exists) { setFlash('danger', 'Selected item was not found.'); redirect(url('admin/reviews')); }

        // Parse comma-separated names and messages
        $rawNames    = $_POST['reviewer_name'] ?? '';
        $rawMessages = $_POST['message'] ?? '';

        $names    = array_map('trim', explode(',', $rawNames));
        $messages = array_map('trim', explode(',', $rawMessages));

        // Remove empty entries
        $names    = array_filter($names, fn($v) => strlen($v) >= 2);
        $messages = array_filter($messages, fn($v) => strlen($v) >= 5);

        if (empty($names) || empty($messages)) {
            setFlash('danger', 'Enter at least one valid name (min 2 chars) and message (min 5 chars).'); redirect(url('admin/reviews'));
        }

        // Available auto-ratings between 3.0 and 5.0 in 0.5 steps
        $autoRatings = [3.0, 3.5, 4.0, 4.5, 5.0];

        $names    = array_values($names);
        $messages = array_values($messages);
        $count    = max(count($names), count($messages));
        $feedbackModel = new ManagedFeedback();

        for ($i = 0; $i < $count; $i++) {
            $entryName    = $names[$i % count($names)];
            $entryMessage = $messages[$i % count($messages)];

            if ($ratingMode === 'auto') {
                // Pick a pseudo-random rating from the pool, cycling so each gets a different one
                $entryRating = $autoRatings[$i % count($autoRatings)];
            } else {
                $entryRating = (float)$ratingMode;
                if ($entryRating < 1 || $entryRating > 5) { $entryRating = 5.0; }
            }

            $feedbackModel->create([
                'target_type'   => $type,
                'target_id'     => $targetId,
                'reviewer_name' => sanitize($entryName),
                'reviewer_role' => $role,
                'rating'        => $entryRating,
                'message'       => sanitize($entryMessage),
            ]);
        }

        $total = $count;
        setFlash('success', $total . ' customer feedback' . ($total > 1 ? 's' : '') . ' published successfully.');
        redirect(url('admin/reviews'));
    }

    public function deleteManaged(string $id): void {
        if (!verifyCsrf()) { setFlash('danger', 'Invalid request.'); redirect(url('admin/reviews')); }
        (new ManagedFeedback())->delete((int)$id); setFlash('success', 'Customer feedback deleted.'); redirect(url('admin/reviews'));
    }

    public function updateStatus(string $id): void {
        if (!verifyCsrf()) { setFlash('danger', 'Invalid request.'); redirect(url('admin/reviews')); }
        $status = $_POST['status'] ?? 'pending';
        (new Review())->updateStatus((int)$id, $status);
        setFlash('success', 'Review status updated.');
        redirect(url('admin/reviews'));
    }

    public function delete(string $id): void {
        (new Review())->delete((int)$id);
        setFlash('success', 'Review deleted.');
        redirect(url('admin/reviews'));
    }

    public function storeFakeRules(): void {
        if (!verifyCsrf()) { setFlash('danger', 'Invalid request.'); redirect(url('admin/reviews')); }
        
        $productId = (int)($_POST['product_id'] ?? 0);
        $viewsMin = $_POST['fake_views_min'] !== '' ? (int)$_POST['fake_views_min'] : null;
        $viewsMax = $_POST['fake_views_max'] !== '' ? (int)$_POST['fake_views_max'] : null;
        $salesMin = $_POST['fake_sales_min'] !== '' ? (int)$_POST['fake_sales_min'] : null;
        $salesMax = $_POST['fake_sales_max'] !== '' ? (int)$_POST['fake_sales_max'] : null;

        if (!$productId) {
            setFlash('danger', 'Select a valid product.'); redirect(url('admin/reviews'));
        }

        (new Product())->update($productId, [
            'fake_views_min' => $viewsMin,
            'fake_views_max' => $viewsMax,
            'fake_sales_min' => $salesMin,
            'fake_sales_max' => $salesMax
        ]);

        setFlash('success', 'Fake views and sales rule applied to product.');
        redirect(url('admin/reviews'));
    }

    public function deleteFakeRules(string $id): void {
        if (!verifyCsrf()) { setFlash('danger', 'Invalid request.'); redirect(url('admin/reviews')); }
        
        (new Product())->update((int)$id, [
            'fake_views_min' => null,
            'fake_views_max' => null,
            'fake_sales_min' => null,
            'fake_sales_max' => null
        ]);

        setFlash('success', 'Fake views and sales rule removed.');
        redirect(url('admin/reviews'));
    }
}
