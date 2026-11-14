<?php
/**
 * Digiws - Post Model for Blog System (Extended)
 */

class Post {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Find single post by ID
     */
    public function find(int $id): ?array {
        $post = $this->db->fetch(
            "SELECT p.*, u.name as author_name, u.avatar as author_avatar,
                    bc.name as category_name, bc.slug as category_slug, bc.color as category_color, bc.icon as category_icon,
                    prod.title as promo_product_title, prod.slug as promo_product_slug, prod.sale_price as promo_product_price,
                    prod.mrp as promo_product_mrp, prod.thumbnail as promo_product_thumbnail, prod.short_description as promo_product_desc
             FROM posts p 
             LEFT JOIN users u ON p.author_id = u.id 
             LEFT JOIN blog_categories bc ON bc.id = p.blog_category_id
             LEFT JOIN products prod ON prod.id = p.promote_product_id
             WHERE p.id = ?",
            [$id]
        );
        if ($post) $post['tags'] = $this->getTags($id);
        return $post;
    }

    /**
     * Find single post by SEO slug (public - increments views)
     */
    public function findBySlug(string $slug): ?array {
        $post = $this->db->fetch(
            "SELECT p.*, u.name as author_name, u.avatar as author_avatar,
                    bc.name as category_name, bc.slug as category_slug, bc.color as category_color, bc.icon as category_icon,
                    prod.title as promo_product_title, prod.slug as promo_product_slug, prod.sale_price as promo_product_price,
                    prod.mrp as promo_product_mrp, prod.thumbnail as promo_product_thumbnail, prod.short_description as promo_product_desc
             FROM posts p 
             LEFT JOIN users u ON p.author_id = u.id 
             LEFT JOIN blog_categories bc ON bc.id = p.blog_category_id
             LEFT JOIN products prod ON prod.id = p.promote_product_id
             WHERE p.slug = ? AND p.status = 'published'",
            [$slug]
        );
        if ($post) $post['tags'] = $this->getTags($post['id']);
        return $post;
    }

    /**
     * Track view (once per IP per post per day)
     */
    public function trackView(int $postId, string $ip): void {
        $ipHash = hash('sha256', $ip . date('Y-m-d'));
        $exists = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM blog_post_views WHERE post_id = ? AND ip_hash = ?",
            [$postId, $ipHash]
        );
        if (!$exists) {
            $this->db->insert('blog_post_views', ['post_id' => $postId, 'ip_hash' => $ipHash]);
            $this->db->query("UPDATE posts SET views = views + 1 WHERE id = ?", [$postId]);
        }
    }

    /**
     * Get tags for a post
     */
    public function getTags(int $postId): array {
        return $this->db->fetchAll(
            "SELECT bt.* FROM blog_tags bt 
             INNER JOIN blog_post_tags bpt ON bpt.tag_id = bt.id 
             WHERE bpt.post_id = ? ORDER BY bt.name ASC",
            [$postId]
        );
    }

    /**
     * Get all posts with rich filters (admin + public)
     */
    public function getAll(int $page = 1, int $perPage = 12, array $filters = []): array {
        $offset = ($page - 1) * $perPage;
        $where  = "1=1";
        $params = [];

        if (isset($filters['status'])) {
            $where .= " AND p.status = ?";
            $params[] = $filters['status'];
        } elseif (!isset($filters['all_status'])) {
            $where .= " AND p.status = 'published'";
        }

        if (!empty($filters['search'])) {
            $where .= " AND (p.title LIKE ? OR p.content LIKE ? OR p.summary LIKE ?)";
            $s = '%' . $filters['search'] . '%';
            $params[] = $s; $params[] = $s; $params[] = $s;
        }

        if (!empty($filters['category_slug'])) {
            $where .= " AND bc.slug = ?";
            $params[] = $filters['category_slug'];
        }

        if (!empty($filters['tag_id'])) {
            $where .= " AND EXISTS (SELECT 1 FROM blog_post_tags bpt WHERE bpt.post_id = p.id AND bpt.tag_id = ?)";
            $params[] = $filters['tag_id'];
        }

        if (!empty($filters['author_id'])) {
            $where .= " AND p.author_id = ?";
            $params[] = $filters['author_id'];
        }

        if (!empty($filters['is_featured'])) {
            $where .= " AND p.is_featured = 1";
        }

        if (!empty($filters['is_trending'])) {
            $where .= " AND p.is_trending = 1";
        }

        if (!empty($filters['is_editors_pick'])) {
            $where .= " AND p.is_editors_pick = 1";
        }

        if (!empty($filters['year'])) {
            $where .= " AND YEAR(p.created_at) = ?";
            $params[] = (int)$filters['year'];
        }
        if (!empty($filters['month'])) {
            $where .= " AND MONTH(p.created_at) = ?";
            $params[] = (int)$filters['month'];
        }

        $orderBy = "p.created_at DESC";
        if (!empty($filters['order'])) {
            $orderBy = match($filters['order']) {
                'views', 'popular', 'most_viewed' => 'p.views DESC',
                'trending'                       => 'p.is_trending DESC, p.views DESC, p.created_at DESC',
                'featured'                       => 'p.is_featured DESC, p.created_at DESC',
                'most_commented'                 => '(SELECT COUNT(*) FROM blog_comments c WHERE c.post_id = p.id AND c.status = \'approved\') DESC',
                'a-z'                            => 'p.title ASC',
                'oldest'                         => 'p.created_at ASC',
                'rating'                         => '(p.rating_sum / NULLIF(p.rating_count, 0)) DESC',
                default                          => 'p.created_at DESC',
            };
        }

        $total = (int)$this->db->fetchColumn(
            "SELECT COUNT(*) FROM posts p 
             LEFT JOIN blog_categories bc ON bc.id = p.blog_category_id
             WHERE {$where}",
            $params
        );

        $sql = "SELECT p.*, u.name as author_name, u.avatar as author_avatar,
                       bc.name as category_name, bc.slug as category_slug, bc.color as category_color, bc.icon as category_icon
                FROM posts p 
                LEFT JOIN users u ON p.author_id = u.id 
                LEFT JOIN blog_categories bc ON bc.id = p.blog_category_id
                WHERE {$where} 
                ORDER BY {$orderBy}
                LIMIT {$perPage} OFFSET {$offset}";

        return ['posts' => $this->db->fetchAll($sql, $params), 'total' => $total];
    }

    /**
     * Get featured posts
     */
    public function getFeatured(int $limit = 3): array {
        return $this->db->fetchAll(
            "SELECT p.*, u.name as author_name, bc.name as category_name, bc.slug as category_slug, bc.color as category_color
             FROM posts p 
             LEFT JOIN users u ON p.author_id = u.id 
             LEFT JOIN blog_categories bc ON bc.id = p.blog_category_id
             WHERE p.status = 'published' AND p.is_featured = 1
             ORDER BY p.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Get trending posts
     */
    public function getTrending(int $limit = 5): array {
        return $this->db->fetchAll(
            "SELECT p.*, u.name as author_name, bc.name as category_name, bc.slug as category_slug, bc.color as category_color
             FROM posts p 
             LEFT JOIN users u ON p.author_id = u.id 
             LEFT JOIN blog_categories bc ON bc.id = p.blog_category_id
             WHERE p.status = 'published' AND p.is_trending = 1
             ORDER BY p.views DESC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Get editor's picks
     */
    public function getEditorsPick(int $limit = 3): array {
        return $this->db->fetchAll(
            "SELECT p.*, u.name as author_name, bc.name as category_name, bc.slug as category_slug, bc.color as category_color
             FROM posts p 
             LEFT JOIN users u ON p.author_id = u.id 
             LEFT JOIN blog_categories bc ON bc.id = p.blog_category_id
             WHERE p.status = 'published' AND p.is_editors_pick = 1
             ORDER BY p.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Get most viewed posts
     */
    public function getMostViewed(int $limit = 5): array {
        return $this->db->fetchAll(
            "SELECT p.*, u.name as author_name, bc.name as category_name, bc.slug as category_slug, bc.color as category_color
             FROM posts p 
             LEFT JOIN users u ON p.author_id = u.id 
             LEFT JOIN blog_categories bc ON bc.id = p.blog_category_id
             WHERE p.status = 'published' AND p.views > 0
             ORDER BY p.views DESC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Get related posts (by category or recency)
     */
    public function getRelated(int $currentPostId, int $limit = 3, ?int $categoryId = null): array {
        $params = [$currentPostId];
        $catCond = '';
        if ($categoryId) {
            $catCond = "AND p.blog_category_id = ?";
            $params[] = $categoryId;
        }
        $posts = $this->db->fetchAll(
            "SELECT p.*, u.name as author_name, bc.name as category_name, bc.slug as category_slug, bc.color as category_color
             FROM posts p 
             LEFT JOIN users u ON p.author_id = u.id 
             LEFT JOIN blog_categories bc ON bc.id = p.blog_category_id
             WHERE p.id != ? AND p.status = 'published' {$catCond}
             ORDER BY p.created_at DESC LIMIT ?",
            array_merge($params, [$limit])
        );

        // Fallback if not enough related
        if (count($posts) < $limit) {
            $posts = $this->db->fetchAll(
                "SELECT p.*, u.name as author_name, bc.name as category_name, bc.slug as category_slug, bc.color as category_color
                 FROM posts p 
                 LEFT JOIN users u ON p.author_id = u.id 
                 LEFT JOIN blog_categories bc ON bc.id = p.blog_category_id
                 WHERE p.id != ? AND p.status = 'published'
                 ORDER BY p.created_at DESC LIMIT ?",
                [$currentPostId, $limit]
            );
        }
        return $posts;
    }

    /**
     * Get previous and next post navigation
     */
    public function getPrevNext(int $postId, string $createdAt): array {
        $prev = $this->db->fetch(
            "SELECT id, title, slug FROM posts WHERE status = 'published' AND created_at < ? AND id != ? ORDER BY created_at DESC LIMIT 1",
            [$createdAt, $postId]
        );
        $next = $this->db->fetch(
            "SELECT id, title, slug FROM posts WHERE status = 'published' AND created_at > ? AND id != ? ORDER BY created_at ASC LIMIT 1",
            [$createdAt, $postId]
        );
        return ['prev' => $prev, 'next' => $next];
    }

    /**
     * Get archive (months with post counts)
     */
    public function getArchive(): array {
        return $this->db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y') as year, DATE_FORMAT(created_at, '%m') as month,
                    DATE_FORMAT(created_at, '%M %Y') as label, COUNT(*) as post_count
             FROM posts WHERE status = 'published'
             GROUP BY year, month ORDER BY year DESC, month DESC"
        );
    }

    /**
     * Get RSS feed posts
     */
    public function getRss(int $limit = 20): array {
        return $this->db->fetchAll(
            "SELECT p.*, u.name as author_name, bc.name as category_name
             FROM posts p 
             LEFT JOIN users u ON p.author_id = u.id 
             LEFT JOIN blog_categories bc ON bc.id = p.blog_category_id
             WHERE p.status = 'published' 
             ORDER BY p.created_at DESC LIMIT ?",
            [$limit]
        );
    }

    /**
     * Insert new blog post
     */
    public function create(array $data): int {
        // Auto reading time
        if (!empty($data['content']) && empty($data['reading_time'])) {
            $data['reading_time'] = max(2, ceil(str_word_count(strip_tags($data['content'])) / 200));
        }
        if (!isset($data['published_at']) && ($data['status'] ?? '') === 'published') {
            $data['published_at'] = date('Y-m-d H:i:s');
        }
        return $this->db->insert('posts', $data);
    }

    /**
     * Update existing blog post
     */
    public function update(int $id, array $data): int {
        if (!empty($data['content'])) {
            $data['reading_time'] = max(2, ceil(str_word_count(strip_tags($data['content'])) / 200));
        }
        if (!isset($data['published_at']) && ($data['status'] ?? '') === 'published') {
            // Only set published_at if not already set
            $existing = $this->db->fetch("SELECT published_at FROM posts WHERE id = ?", [$id]);
            if (empty($existing['published_at'])) {
                $data['published_at'] = date('Y-m-d H:i:s');
            }
        }
        return $this->db->update('posts', $data, 'id = ?', [$id]);
    }

    /**
     * Delete blog post
     */
    public function delete(int $id): int {
        return $this->db->delete('posts', 'id = ?', [$id]);
    }

    /**
     * Get blog analytics data (comprehensive)
     */
    public function getAnalytics(): array {
        $totalPosts    = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM posts");
        $published     = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM posts WHERE status = 'published'");
        $drafts        = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM posts WHERE status = 'draft'");
        $totalViews    = (int)$this->db->fetchColumn("SELECT COALESCE(SUM(views), 0) FROM posts");
        $totalComments = (int)$this->db->fetchColumn("SELECT COUNT(*) FROM blog_comments WHERE status = 'approved'");

        // Average rating across all posts that have ratings
        $avgRatingRow = $this->db->fetch("SELECT SUM(rating_sum) as rs, SUM(rating_count) as rc FROM posts WHERE rating_count > 0");
        $avgRating = 0;
        if ($avgRatingRow && $avgRatingRow['rc'] > 0) {
            $avgRating = round($avgRatingRow['rs'] / $avgRatingRow['rc'], 1);
        }

        // Total reactions from JSON
        $allReactions = $this->db->fetchAll("SELECT reactions_json FROM posts WHERE reactions_json IS NOT NULL AND reactions_json != '{}'");
        $reactionStats = ['like' => 0, 'love' => 0, 'insight' => 0, 'mindblown' => 0];
        $totalReactions = 0;
        foreach ($allReactions as $row) {
            $r = json_decode($row['reactions_json'], true) ?? [];
            foreach ($reactionStats as $k => &$v) {
                $add = (int)($r[$k] ?? 0);
                $v += $add;
                $totalReactions += $add;
            }
        }
        unset($v);

        // Top posts with comment count and ratings
        $topPosts = $this->db->fetchAll(
            "SELECT p.id, p.title, p.slug, p.views, p.created_at, p.rating_sum, p.rating_count,
                    bc.name as category_name, COUNT(c.id) as comment_count
             FROM posts p
             LEFT JOIN blog_categories bc ON bc.id = p.blog_category_id
             LEFT JOIN blog_comments c ON c.post_id = p.id AND c.status = 'approved'
             WHERE p.status = 'published'
             GROUP BY p.id
             ORDER BY p.views DESC LIMIT 10"
        );

        // Top categories with icon
        $topCategories = $this->db->fetchAll(
            "SELECT bc.name, bc.slug, bc.color, bc.icon, COUNT(p.id) as post_count, COALESCE(SUM(p.views),0) as total_views
             FROM blog_categories bc
             LEFT JOIN posts p ON p.blog_category_id = bc.id AND p.status = 'published'
             GROUP BY bc.id ORDER BY post_count DESC LIMIT 8"
        );

        // Views by month (last 12 months from blog_post_views or fallback from posts)
        $viewsByMonth = $this->db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%b %Y') as month, COUNT(*) as views
             FROM blog_post_views
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY MIN(created_at) ASC"
        );
        if (empty($viewsByMonth)) {
            // Fallback: group by post created_at months
            $viewsByMonth = $this->db->fetchAll(
                "SELECT DATE_FORMAT(created_at, '%b %Y') as month, SUM(views) as views
                 FROM posts
                 WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                 GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                 ORDER BY MIN(created_at) ASC"
            );
        }

        // Posts created by month
        $postsByMonth = $this->db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%b %Y') as month, COUNT(*) as count
             FROM posts
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY MIN(created_at) ASC"
        );

        // Comments by day of week (DAYNAME)
        $commentsByDay = $this->db->fetchAll(
            "SELECT DAYNAME(created_at) as day, COUNT(*) as count
             FROM blog_comments
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY DAYOFWEEK(created_at), DAYNAME(created_at)
             ORDER BY DAYOFWEEK(created_at)"
        );

        $recentPosts = $this->db->fetchAll(
            "SELECT id, title, slug, views, created_at, status FROM posts ORDER BY created_at DESC LIMIT 5"
        );

        return compact(
            'totalPosts','published','drafts','totalViews','totalComments',
            'avgRating','totalReactions','reactionStats',
            'topPosts','topCategories','viewsByMonth','postsByMonth',
            'commentsByDay','recentPosts'
        );
    }

    /**
     * Bulk action
     */
    public function bulkAction(array $ids, string $action): void {
        if (empty($ids)) return;
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        match($action) {
            'publish' => $this->db->query("UPDATE posts SET status='published' WHERE id IN ({$placeholders})", $ids),
            'draft'   => $this->db->query("UPDATE posts SET status='draft' WHERE id IN ({$placeholders})", $ids),
            'delete'  => $this->db->query("DELETE FROM posts WHERE id IN ({$placeholders})", $ids),
            default   => null,
        };
    }
}
