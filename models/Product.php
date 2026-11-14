<?php
/**
 * Digiws - Product Model
 */

class Product {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array {
        $product = $this->db->fetch("SELECT p.* FROM products p WHERE p.id = ?", [$id]);
        if ($product) {
            $product['categories'] = $this->getCategories($id);
            $product['category_ids'] = array_column($product['categories'], 'id');
            // Compatibility for old code that expects category_name
            $product['category_name'] = !empty($product['categories']) ? $product['categories'][0]['name'] : 'Uncategorized';
            $product['images'] = $this->getImages($id);
            $product['features'] = $this->getFeatures($id);
        }
        return $product;
    }

    public function findBySlug(string $slug): ?array {
        $product = $this->db->fetch("SELECT p.* FROM products p WHERE p.slug = ?", [$slug]);
        if ($product) {
            $id = $product['id'];
            $product['categories'] = $this->getCategories($id);
            $product['category_ids'] = array_column($product['categories'], 'id');
            // Compatibility
            $product['category_name'] = !empty($product['categories']) ? $product['categories'][0]['name'] : 'Uncategorized';
            $product['category_slug'] = !empty($product['categories']) ? $product['categories'][0]['slug'] : '';
            
            $product['images'] = $this->getImages($id);
            $product['features'] = $this->getFeatures($id);
            // Increment view count
            $this->db->query("UPDATE products SET view_count = view_count + 1 WHERE id = ?", [$id]);
        }
        return $product;
    }

    public function getAll(int $page = 1, int $perPage = 12, array $filters = []): array {
        $offset = ($page - 1) * $perPage;
        $where = "p.status = 'active'";
        $params = [];

        if (!empty($filters['category_id'])) {
            $where .= ' AND (p.category_id = ? OR pc.category_id = ?)';
            $params[] = $filters['category_id'];
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['category_slug'])) {
            $where .= ' AND c.slug = ?';
            $params[] = $filters['category_slug'];
        }
        if (!empty($filters['product_type'])) {
            $where .= ' AND p.product_type = ?';
            $params[] = $filters['product_type'];
        }
        if (!empty($filters['search'])) {
            $where .= ' AND (p.title LIKE ? OR p.short_description LIKE ? OR p.tags LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$search, $search, $search]);
        }
        if (!empty($filters['min_price'])) {
            $where .= ' AND p.sale_price >= ?';
            $params[] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $where .= ' AND p.sale_price <= ?';
            $params[] = $filters['max_price'];
        }
        if (!empty($filters['tag'])) {
            $where .= ' AND p.tags LIKE ?';
            $params[] = '%' . $filters['tag'] . '%';
        }
        if (isset($filters['is_featured'])) {
            $where .= ' AND p.is_featured = 1';
        }
        if (isset($filters['is_bestseller'])) {
            $where .= ' AND p.is_bestseller = 1';
        }
        if (!empty($filters['status'])) {
            $where = str_replace("p.status = 'active'", "p.status = ?", $where);
            $params = array_merge([$filters['status']], $params);
        }
        if (isset($filters['all_status'])) {
            $where = str_replace("p.status = 'active'", "1=1", $where);
        }

        $orderBy = 'p.created_at DESC';
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_low': $orderBy = 'p.sale_price ASC'; break;
                case 'price_high': $orderBy = 'p.sale_price DESC'; break;
                case 'popular': $orderBy = 'p.total_sales DESC'; break;
                case 'rating': $orderBy = 'p.avg_rating DESC'; break;
                case 'newest': $orderBy = 'p.created_at DESC'; break;
                case 'name': $orderBy = 'p.title ASC'; break;
            }
        }

        try {
            $countParams = $params;
            $total = $this->db->fetchColumn(
                "SELECT COUNT(DISTINCT p.id) FROM products p 
                 LEFT JOIN product_categories pc ON p.id = pc.product_id
                 LEFT JOIN categories c ON pc.category_id = c.id 
                 WHERE {$where}",
                $countParams
            );

            $params[] = $perPage;
            $params[] = $offset;
            $products = $this->db->fetchAll(
                "SELECT DISTINCT p.*, 
                 (SELECT name FROM categories c3 JOIN product_categories pc3 ON c3.id = pc3.category_id WHERE pc3.product_id = p.id LIMIT 1) as category_name,
                 (SELECT slug FROM categories c4 JOIN product_categories pc4 ON c4.id = pc4.category_id WHERE pc4.product_id = p.id LIMIT 1) as category_slug
                 FROM products p 
                 LEFT JOIN product_categories pc ON p.id = pc.product_id
                 LEFT JOIN categories c ON pc.category_id = c.id 
                 WHERE {$where} ORDER BY {$orderBy} LIMIT ? OFFSET ?",
                $params
            );
        } catch (\Exception $e) {
            // Fallback: product_categories table might not exist yet - use legacy joins
            // Strip out any pc.category_id references from $where
            $where = preg_replace('/ AND \(p\.category_id = \? OR pc\.category_id = \?\)/', ' AND p.category_id = ?', $where);
            // Remove duplicate param if we added two for category_id
            if (!empty($filters['category_id'])) {
                // Remove one of the two category_id params
                $firstIdx = array_search($filters['category_id'], $params);
                if ($firstIdx !== false) {
                    array_splice($params, $firstIdx + 1, 1);
                }
            }
            $countParams = $params;
            $total = $this->db->fetchColumn(
                "SELECT COUNT(DISTINCT p.id) FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 WHERE {$where}",
                $countParams
            );
            $params[] = $perPage;
            $params[] = $offset;
            $products = $this->db->fetchAll(
                "SELECT DISTINCT p.*, c.name as category_name, c.slug as category_slug
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id
                 WHERE {$where} ORDER BY {$orderBy} LIMIT ? OFFSET ?",
                $params
            );
        }

        return ['products' => $products, 'total' => (int)$total];
    }

    public function getFeatured(int $limit = 8): array {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.status = 'active' AND p.is_featured = 1 
             ORDER BY p.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    public function getBestsellers(int $limit = 8): array {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.status = 'active' AND p.is_bestseller = 1 
             ORDER BY p.total_sales DESC LIMIT ?",
            [$limit]
        );
    }

    public function getNew(int $limit = 8): array {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.status = 'active' AND p.is_new = 1 
             ORDER BY p.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    public function getRelated(int $productId, ?int $categoryId, int $limit = 4): array {
        if (!$categoryId) return [];
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.status = 'active' AND p.id != ? AND p.category_id = ? 
             ORDER BY RAND() LIMIT ?",
            [$productId, $categoryId, $limit]
        );
    }

    public function ensureDigiwsColumn(): void {
        try {
            $res = $this->db->fetch("SHOW COLUMNS FROM products LIKE 'digiws_product_id'");
            if (empty($res)) {
                $this->db->query("ALTER TABLE products ADD COLUMN digiws_product_id VARCHAR(64) NULL AFTER product_type");
            }
        } catch (\Throwable $e) {
            error_log('Auto alter table error: ' . $e->getMessage());
        }
    }

    public function create(array $data): int {
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);
        
        if (isset($data['digiws_product_id'])) {
            $this->ensureDigiwsColumn();
        }

        $data['slug'] = uniqueSlug($data['title'], 'products');
        $productId = $this->db->insert('products', $data);
        
        if ($productId && !empty($categoryIds)) {
            $this->syncCategories($productId, $categoryIds);
        }
        
        return $productId;
    }

    public function update(int $id, array $data): int {
        if (isset($data['category_ids'])) {
            $this->syncCategories($id, $data['category_ids']);
            unset($data['category_ids']);
        }
        
        if (isset($data['digiws_product_id'])) {
            $this->ensureDigiwsColumn();
        }

        if (isset($data['title'])) {
            $data['slug'] = uniqueSlug($data['title'], 'products', $id);
        }
        return $this->db->update('products', $data, 'id = ?', [$id]);
    }

    public function getCategories(int $productId): array {
        try {
            return $this->db->fetchAll(
                "SELECT c.* FROM categories c 
                 JOIN product_categories pc ON c.id = pc.category_id 
                 WHERE pc.product_id = ?",
                [$productId]
            );
        } catch (\Exception $e) {
            // Fallback: product_categories table might not exist yet
            $product = $this->db->fetch("SELECT category_id FROM products WHERE id = ?", [$productId]);
            if ($product && $product['category_id']) {
                $cat = $this->db->fetch("SELECT * FROM categories WHERE id = ?", [$product['category_id']]);
                return $cat ? [$cat] : [];
            }
            return [];
        }
    }

    public function syncCategories(int $productId, array $categoryIds): void {
        try {
            $this->db->delete('product_categories', 'product_id = ?', [$productId]);
            foreach ($categoryIds as $catId) {
                $this->db->insert('product_categories', [
                    'product_id' => $productId,
                    'category_id' => (int)$catId
                ]);
            }
            // Also update legacy category_id with the first one
            if (!empty($categoryIds)) {
                $this->db->update('products', ['category_id' => (int)$categoryIds[0]], 'id = ?', [$productId]);
            }
        } catch (\Exception $e) {
            // Fallback: update legacy category_id if pivot table doesn't exist
            if (!empty($categoryIds)) {
                $this->db->update('products', ['category_id' => (int)$categoryIds[0]], 'id = ?', [$productId]);
            }
        }
    }

    public function delete(int $id): int {
        // Get images to delete files
        $images = $this->getImages($id);
        foreach ($images as $img) {
            deleteFile($img['image_path']);
        }
        $product = $this->find($id);
        if ($product && $product['thumbnail']) {
            deleteFile($product['thumbnail']);
        }
        if ($product && $product['download_file']) {
            deleteFile($product['download_file']);
        }
        return $this->db->delete('products', 'id = ?', [$id]);
    }

    // Images
    public function getImages(int $productId): array {
        return $this->db->fetchAll(
            "SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order",
            [$productId]
        );
    }

    public function addImage(int $productId, string $path, int $order = 0): int {
        return $this->db->insert('product_images', [
            'product_id' => $productId,
            'image_path' => $path,
            'sort_order' => $order
        ]);
    }

    public function deleteImage(int $imageId): void {
        $img = $this->db->fetch("SELECT * FROM product_images WHERE id = ?", [$imageId]);
        if ($img) {
            deleteFile($img['image_path']);
            $this->db->delete('product_images', 'id = ?', [$imageId]);
        }
    }

    // Features
    public function getFeatures(int $productId): array {
        return $this->db->fetchAll(
            "SELECT * FROM product_features WHERE product_id = ? ORDER BY sort_order",
            [$productId]
        );
    }

    public function syncFeatures(int $productId, array $features): void {
        $this->db->delete('product_features', 'product_id = ?', [$productId]);
        foreach ($features as $i => $feature) {
            if (trim($feature) === '') continue;
            $this->db->insert('product_features', [
                'product_id' => $productId,
                'feature'    => trim($feature),
                'sort_order' => $i
            ]);
        }
    }

    public function updateRating(int $productId): void {
        // Get reviews sum and count from reviews table
        $reviewData = $this->db->fetch(
            "SELECT COUNT(*) as `count`, SUM(rating) as `sum` 
             FROM reviews 
             WHERE product_id = ? AND status = 'approved'",
            [$productId]
        );
        $reviewCount = (int)($reviewData['count'] ?? 0);
        $reviewSum = (float)($reviewData['sum'] ?? 0.0);

        // Get reviews sum and count from managed_feedback table
        $managedCount = 0;
        $managedSum = 0.0;
        try {
            $managedData = $this->db->fetch(
                "SELECT COUNT(*) as `count`, SUM(rating) as `sum` 
                 FROM managed_feedback 
                 WHERE target_type = 'product' AND status = 'published' AND rating IS NOT NULL AND target_id = ?",
                [$productId]
            );
            $managedCount = (int)($managedData['count'] ?? 0);
            $managedSum = (float)($managedData['sum'] ?? 0.0);
        } catch (Throwable $e) {
            // Table might not exist yet
        }

        $totalCount = $reviewCount + $managedCount;
        $totalSum = $reviewSum + $managedSum;
        $avg = $totalCount > 0 ? round($totalSum / $totalCount, 2) : 0.00;

        $this->db->update('products', ['avg_rating' => $avg], 'id = ?', [$productId]);
    }

    public function incrementSales(int $productId, int $qty = 1): void {
        $this->db->query("UPDATE products SET total_sales = total_sales + ? WHERE id = ?", [$qty, $productId]);
    }

    public function getTotalProducts(): int {
        return $this->db->count('products');
    }

    public function searchSuggestions(string $query, int $limit = 5): array {
        return $this->db->fetchAll(
            "SELECT id, title, slug, thumbnail, sale_price FROM products 
             WHERE status = 'active' AND (title LIKE ? OR tags LIKE ?) 
             ORDER BY total_sales DESC LIMIT ?",
            ['%' . $query . '%', '%' . $query . '%', $limit]
        );
    }

    /**
     * Load features for a list of products in bulk to avoid N+1 queries
     */
    public function loadFeatures(array &$products): void {
        if (empty($products)) return;
        
        $ids = array_column($products, 'id');
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $allFeatures = $this->db->fetchAll(
            "SELECT * FROM product_features WHERE product_id IN ($placeholders) ORDER BY product_id, sort_order",
            $ids
        );
        
        $featuresByProduct = [];
        foreach ($allFeatures as $feature) {
            $featuresByProduct[$feature['product_id']][] = $feature['feature'];
        }
        
        foreach ($products as &$product) {
            $product['features'] = $featuresByProduct[$product['id']] ?? [];
        }
    }

    /**
     * Add the current rating average and review count to product cards in one
     * batch. This includes approved on-site reviews and published feedback
     * entered by an administrator.
     */
    public function loadRatings(array &$products): void {
        if (empty($products)) return;

        $ids = array_values(array_unique(array_map('intval', array_column($products, 'id'))));
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $ratingsByProduct = [];

        $approvedReviews = $this->db->fetchAll(
            "SELECT product_id, COUNT(*) AS review_count, AVG(rating) AS average_rating
             FROM reviews
             WHERE status = 'approved' AND product_id IN ($placeholders)
             GROUP BY product_id",
            $ids
        );
        foreach ($approvedReviews as $review) {
            $ratingsByProduct[(int)$review['product_id']] = [
                'count' => (int)$review['review_count'],
                'sum'   => (float)$review['average_rating'] * (int)$review['review_count'],
            ];
        }

        try {
            $managedFeedback = $this->db->fetchAll(
                "SELECT target_id AS product_id, COUNT(*) AS review_count, AVG(rating) AS average_rating
                 FROM managed_feedback
                 WHERE target_type = 'product' AND status = 'published'
                   AND rating IS NOT NULL AND target_id IN ($placeholders)
                 GROUP BY target_id",
                $ids
            );
            foreach ($managedFeedback as $review) {
                $productId = (int)$review['product_id'];
                $ratingsByProduct[$productId] ??= ['count' => 0, 'sum' => 0.0];
                $ratingsByProduct[$productId]['count'] += (int)$review['review_count'];
                $ratingsByProduct[$productId]['sum'] += (float)$review['average_rating'] * (int)$review['review_count'];
            }
        } catch (Throwable $e) {
            // Older installations may not have the managed_feedback table yet.
        }

        foreach ($products as &$product) {
            $rating = $ratingsByProduct[(int)$product['id']] ?? null;
            $product['reviews_count'] = $rating['count'] ?? 0;
            $product['avg_rating'] = $rating && $rating['count'] > 0
                ? round($rating['sum'] / $rating['count'], 2)
                : (float)($product['avg_rating'] ?? 0);
        }
        unset($product);
    }
}
