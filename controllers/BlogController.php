<?php
/**
 * Digiws - Public Blog Controller (Full Enterprise)
 */

class BlogController {

    /**
     * Blog homepage — featured, trending, latest, categories
     */
    public function index(): void {
        $postModel     = new Post();
        $categoryModel = new BlogCategory();
        $tagModel      = new BlogTag();

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 9;

        $filters = [];
        if (!empty($_GET['q']))        $filters['search'] = sanitize($_GET['q']);
        if (!empty($_GET['category'])) $filters['category_slug'] = sanitize($_GET['category']);
        if (!empty($_GET['tag'])) {
            $t = $tagModel->findBySlug(sanitize($_GET['tag']));
            if ($t) $filters['tag_id'] = $t['id'];
        }
        if (!empty($_GET['order']))    $filters['order'] = sanitize($_GET['order']);

        $result     = $postModel->getAll($page, $perPage, $filters);
        $posts      = $result['posts'];
        $totalPosts = $result['total'];
        $totalPages = (int)ceil($totalPosts / $perPage);

        // AJAX request for Load More / Infinite Scroll
        if (isAjax()) {
            header('Content-Type: application/json');
            ob_start();
            include VIEWS_PATH . '/blog/partials/posts-grid.php';
            $html = ob_get_clean();
            echo json_encode([
                'success' => true,
                'html' => $html,
                'page' => $page,
                'totalPages' => $totalPages,
                'hasMore' => $page < $totalPages
            ]);
            exit;
        }

        $featuredPosts  = $postModel->getFeatured(3);
        $trendingPosts  = $postModel->getTrending(5);
        $editorsPick    = $postModel->getEditorsPick(3);
        $mostViewed     = $postModel->getMostViewed(5);
        $popularCategories = $categoryModel->getPopular(8);
        $popularTags    = $tagModel->getPopular(20);
        $recentComments = (new BlogComment())->getRecent(5);

        $pageTitle = 'Blog — Tutorials, Guides & Tips | ' . APP_NAME;
        $metaDesc  = 'Read expert articles, PHP tutorials, web development guides, SEO tips, and digital product reviews on ' . APP_NAME;

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/blog/index.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    /**
     * Single article page
     */
    public function show(string $slug): void {
        $postModel = new Post();
        $post      = $postModel->findBySlug($slug);

        if (!$post) {
            http_response_code(404);
            include VIEWS_PATH . '/404.php';
            return;
        }

        // Process dynamic product promotion insertion
        if ($post) {
            $promotedProduct = null;
            if (!empty($post['promote_product_id'])) {
                $promotedProduct = (new Product())->find((int)$post['promote_product_id']);
            }

            if ($promotedProduct) {
                // Generate the HTML for the product promo card
                $promoThumb = !empty($promotedProduct['thumbnail']) ? uploadUrl($promotedProduct['thumbnail']) : '';
                $promoTitle = htmlspecialchars($promotedProduct['title']);
                $promoUrl = url('product/' . $promotedProduct['slug']);
                $promoPrice = formatPrice($promotedProduct['sale_price']);
                $promoMrp = (!empty($promotedProduct['mrp']) && $promotedProduct['mrp'] > $promotedProduct['sale_price']) ? '<span style="text-decoration: line-through; color: var(--text-muted); font-size: 0.75rem; margin-left: 6px;">' . formatPrice($promotedProduct['mrp']) . '</span>' : '';
                
                $promoThumbHtml = '';
                if ($promoThumb) {
                    $promoThumbHtml = '<img src="' . $promoThumb . '" alt="' . $promoTitle . '" style="width: 58px; height: 58px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color); flex-shrink: 0;">';
                }

                $promoCardHtml = '
                <div class="blog-product-promo">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        ' . $promoThumbHtml . '
                        <div>
                            <span style="font-size: 0.65rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 0.8px; display: block; margin-bottom: 2px;">Promoted Product</span>
                            <h6 style="margin: 0 0 4px 0; font-size: 0.95rem; font-weight: 700;"><a href="' . $promoUrl . '" style="color: var(--text-primary); text-decoration: none;">' . $promoTitle . '</a></h6>
                            <div style="display: flex; align-items: center; font-size: 0.8rem;">
                                <span style="font-weight: 700; color: var(--text-primary);">' . $promoPrice . '</span>
                                ' . $promoMrp . '
                            </div>
                        </div>
                    </div>
                    <a href="' . $promoUrl . '" class="btn-dc-primary btn-dc-sm" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; padding: 8px 16px; border-radius: 6px; font-size: 0.82rem; font-weight: 600; flex-shrink: 0; white-space: nowrap;">
                        <i class="bi bi-cart3"></i> Buy Now
                    </a>
                </div>';

                // Insert into content exactly 3 times: Top, Center, Bottom
                $content = $post['content'];
                $paragraphs = explode('</p>', $content);
                
                // Clean trailing empty element if any
                if (empty(end($paragraphs))) {
                    array_pop($paragraphs);
                }
                
                $totalParagraphs = count($paragraphs);

                if ($totalParagraphs >= 3) {
                    $topIndex = 0; // After first paragraph
                    $bottomIndex = $totalParagraphs - 1; // After last paragraph
                    $centerIndex = floor($bottomIndex / 2); // Middle paragraph

                    // Append promo card to selected positions
                    $paragraphs[$bottomIndex] .= $promoCardHtml;
                    if ($centerIndex > $topIndex && $centerIndex < $bottomIndex) {
                        $paragraphs[$centerIndex] .= $promoCardHtml;
                    } else {
                        // If center falls on boundary, insert it in between
                        $paragraphs[max(1, $bottomIndex - 1)] .= $promoCardHtml;
                    }
                    $paragraphs[$topIndex] .= $promoCardHtml;

                    $post['content'] = implode('</p>', $paragraphs) . '</p>';
                } else {
                    // For short posts, show at beginning, middle, and end
                    $post['content'] = $promoCardHtml . $content . $promoCardHtml;
                }
            }
        }

        // Track view
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $postModel->trackView($post['id'], $ip);

        // Related posts
        $relatedPosts = $postModel->getRelated($post['id'], 3, $post['blog_category_id'] ?? null);

        // Prev/Next
        $prevNext = $postModel->getPrevNext($post['id'], $post['created_at']);

        // Comments
        $commentModel = new BlogComment();
        $comments     = $commentModel->getByPost($post['id']);
        $managedFeedback = (new ManagedFeedback())->getFor('blog', (int)$post['id']);
        // Build nested comments
        $nestedComments = [];
        foreach ($comments as $comment) {
            $comment['replies'] = $commentModel->getReplies($comment['id']);
            $nestedComments[]   = $comment;
        }

        // Bookmark status
        $isBookmarked = false;
        if (isLoggedIn()) {
            $isBookmarked = (new BlogBookmark())->isBookmarked(currentUserId(), $post['id']);
        }

        // Popular tags
        $popularTags = (new BlogTag())->getPopular(15);

        // Tags for this post
        $tags = $post['tags'] ?? [];

        // Trending posts for sidebar
        $trendingPosts = $postModel->getTrending(5);
        // Fallback: if no posts are marked as trending, use most viewed
        if (empty($trendingPosts)) {
            $trendingPosts = $postModel->getMostViewed(5);
        }

        // Categories for sidebar
        $categoryModel = new BlogCategory();
        $categories = $categoryModel->getAll();

        $pageTitle  = ($post['meta_title'] ?: $post['title']) . ' — ' . APP_NAME;
        $metaDesc   = $post['meta_description'] ?: ($post['summary'] ?: truncateText(strip_tags($post['content']), 160));
        $metaKeywords = $post['focus_keyword'] ?? '';

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/blog/show.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    /**
     * Category archive
     */
    public function category(string $slug): void {
        $categoryModel = new BlogCategory();
        $category      = $categoryModel->findBySlug($slug);

        if (!$category) {
            http_response_code(404);
            include VIEWS_PATH . '/404.php';
            return;
        }

        $postModel  = new Post();
        $tagModel   = new BlogTag();
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $perPage    = 9;

        $result     = $postModel->getAll($page, $perPage, ['category_slug' => $slug]);
        $posts      = $result['posts'];
        $totalPosts = $result['total'];
        $totalPages = (int)ceil($totalPosts / $perPage);
        $popularTags= $tagModel->getPopular(15);
        $allCategories = $categoryModel->getAll();

        $pageTitle = $category['meta_title'] ?: ($category['name'] . ' — Blog | ' . APP_NAME);
        $metaDesc  = $category['meta_description'] ?: ('Browse all ' . $category['name'] . ' articles on ' . APP_NAME);

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/blog/category.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    /**
     * Tag archive
     */
    public function tag(string $slug): void {
        $tagModel = new BlogTag();
        $tag      = $tagModel->findBySlug($slug);

        if (!$tag) {
            http_response_code(404);
            include VIEWS_PATH . '/404.php';
            return;
        }

        $postModel  = new Post();
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $perPage    = 9;

        $result     = $postModel->getAll($page, $perPage, ['tag_id' => $tag['id']]);
        $posts      = $result['posts'];
        $totalPosts = $result['total'];
        $totalPages = (int)ceil($totalPosts / $perPage);
        $popularTags= $tagModel->getPopular(15);

        $pageTitle = '#' . $tag['name'] . ' Articles — Blog | ' . APP_NAME;
        $metaDesc  = 'Browse all articles tagged with ' . $tag['name'] . ' on ' . APP_NAME;

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/blog/tag.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    /**
     * Author archive
     */
    public function author(string $username): void {
        $db = Database::getInstance();
        $author = $db->fetch(
            "SELECT id, name, email, avatar, created_at FROM users WHERE name = ? AND status = 'active'",
            [$username]
        );

        if (!$author) {
            http_response_code(404);
            include VIEWS_PATH . '/404.php';
            return;
        }

        $postModel  = new Post();
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $perPage    = 9;

        $result     = $postModel->getAll($page, $perPage, ['author_id' => $author['id']]);
        $posts      = $result['posts'];
        $totalPosts = $result['total'];
        $totalPages = (int)ceil($totalPosts / $perPage);

        $pageTitle = 'Articles by ' . $author['name'] . ' — Blog | ' . APP_NAME;
        $metaDesc  = 'Read all articles written by ' . $author['name'] . ' on ' . APP_NAME;

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/blog/author.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    /**
     * Archive page (year/month)
     */
    public function archive(): void {
        $postModel = new Post();
        $archive   = $postModel->getArchive();

        $year  = !empty($_GET['year'])  ? (int)$_GET['year']  : null;
        $month = !empty($_GET['month']) ? (int)$_GET['month'] : null;

        $posts      = [];
        $totalPosts = 0;
        $totalPages = 1;
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $perPage    = 9;

        if ($year) {
            $filters = ['year' => $year];
            if ($month) $filters['month'] = $month;
            $result     = $postModel->getAll($page, $perPage, $filters);
            $posts      = $result['posts'];
            $totalPosts = $result['total'];
            $totalPages = (int)ceil($totalPosts / $perPage);
        }

        $pageTitle = 'Blog Archive — ' . APP_NAME;
        $metaDesc  = 'Browse all blog articles by date on ' . APP_NAME;

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/blog/archive.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    /**
     * Search page
     */
    public function search(): void {
        $postModel  = new Post();
        $tagModel   = new BlogTag();
        $categoryModel = new BlogCategory();

        $q          = sanitize($_GET['q'] ?? '');
        $catSlug    = sanitize($_GET['category'] ?? '');
        $tagSlug    = sanitize($_GET['tag'] ?? '');
        $page       = max(1, (int)($_GET['page'] ?? 1));
        $perPage    = 9;

        $filters = [];
        if ($q)      $filters['search']        = $q;
        if ($catSlug) $filters['category_slug'] = $catSlug;
        if ($tagSlug) {
            $t = $tagModel->findBySlug($tagSlug);
            if ($t) $filters['tag_id'] = $t['id'];
        }

        $result     = $postModel->getAll($page, $perPage, $filters);
        $posts      = $result['posts'];
        $totalPosts = $result['total'];
        $totalPages = (int)ceil($totalPosts / $perPage);
        $popularTags= $tagModel->getPopular(15);
        $allCategories = $categoryModel->getAll();

        $pageTitle = 'Search Results — Blog | ' . APP_NAME;
        $metaDesc  = 'Search results for: ' . $q;

        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/blog/search.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    /**
     * AJAX live search API
     */
    public function apiSearch(): void {
        header('Content-Type: application/json');
        $q = sanitize($_GET['q'] ?? '');
        if (strlen($q) < 2) {
            echo json_encode(['posts' => []]);
            return;
        }
        $postModel = new Post();
        $result    = $postModel->getAll(1, 5, ['search' => $q]);
        $posts     = array_map(fn($p) => [
            'title'    => $p['title'],
            'slug'     => $p['slug'],
            'url'      => url('blog/' . $p['slug']),
            'image'    => $p['image'] ? uploadUrl($p['image']) : null,
            'category' => $p['category_name'] ?? null,
            'date'     => formatDate($p['created_at']),
        ], $result['posts']);
        echo json_encode(['posts' => $posts]);
    }

    /**
     * RSS 2.0 Feed
     */
    public function rss(): void {
        $postModel = new Post();
        $posts     = $postModel->getRss(20);
        $siteName  = APP_NAME;
        $siteUrl   = APP_URL;

        header('Content-Type: application/rss+xml; charset=UTF-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        echo "<channel>\n";
        echo "<title>" . htmlspecialchars($siteName . ' Blog') . "</title>\n";
        echo "<link>{$siteUrl}/blog</link>\n";
        echo "<description>Latest articles from " . htmlspecialchars($siteName) . "</description>\n";
        echo '<atom:link href="' . $siteUrl . '/blog/rss.xml" rel="self" type="application/rss+xml" />' . "\n";
        echo "<language>en-us</language>\n";
        echo "<lastBuildDate>" . date(DATE_RSS) . "</lastBuildDate>\n";

        foreach ($posts as $post) {
            $postUrl = $siteUrl . '/blog/' . $post['slug'];
            echo "<item>\n";
            echo "<title>" . htmlspecialchars($post['title']) . "</title>\n";
            echo "<link>{$postUrl}</link>\n";
            echo "<guid isPermaLink=\"true\">{$postUrl}</guid>\n";
            echo "<pubDate>" . date(DATE_RSS, strtotime($post['created_at'])) . "</pubDate>\n";
            echo "<author>" . htmlspecialchars($post['author_name'] ?? 'Admin') . "</author>\n";
            if ($post['category_name']) {
                echo "<category>" . htmlspecialchars($post['category_name']) . "</category>\n";
            }
            $desc = $post['summary'] ?: truncateText(strip_tags($post['content']), 300);
            echo "<description><![CDATA[" . $desc . "]]></description>\n";
            echo "</item>\n";
        }
        echo "</channel>\n</rss>";
        exit;
    }

    /**
     * AJAX save star rating
     */
    public function rate(): void {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $postId = (int)($_POST['post_id'] ?? 0);
        $rating = (int)($_POST['rating'] ?? 0);

        if (!$postId || $rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'message' => 'Invalid rating values.']);
            exit;
        }

        $sessionKey = 'rated_post_' . $postId;
        if (isset($_SESSION[$sessionKey])) {
            echo json_encode(['success' => false, 'message' => 'You have already rated this post.']);
            exit;
        }

        $postModel = new Post();
        $post = $postModel->find($postId);
        if (!$post) {
            echo json_encode(['success' => false, 'message' => 'Post not found.']);
            exit;
        }

        $db = Database::getInstance();
        $db->query("UPDATE posts SET rating_sum = rating_sum + ?, rating_count = rating_count + 1 WHERE id = ?", [$rating, $postId]);
        
        $_SESSION[$sessionKey] = true;

        $updatedPost = $postModel->find($postId);
        $avg = $updatedPost['rating_count'] > 0 ? round($updatedPost['rating_sum'] / $updatedPost['rating_count'], 1) : 0;

        echo json_encode([
            'success' => true,
            'message' => 'Thank you for rating!',
            'average' => $avg,
            'count' => $updatedPost['rating_count']
        ]);
        exit;
    }

    /**
     * AJAX save emoji reaction
     */
    public function react(): void {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            exit;
        }

        $postId = (int)($_POST['post_id'] ?? 0);
        $reaction = trim($_POST['reaction'] ?? '');

        $allowedReactions = ['like', 'love', 'insight', 'mindblown'];
        if (!$postId || !in_array($reaction, $allowedReactions)) {
            echo json_encode(['success' => false, 'message' => 'Invalid reaction.']);
            exit;
        }

        $postModel = new Post();
        $post = $postModel->find($postId);
        if (!$post) {
            echo json_encode(['success' => false, 'message' => 'Post not found.']);
            exit;
        }

        $reactions = json_decode($post['reactions_json'] ?? '{}', true);
        if (!is_array($reactions)) {
            $reactions = [];
        }
        foreach ($allowedReactions as $r) {
            if (!isset($reactions[$r])) $reactions[$r] = 0;
        }

        $sessionKey = 'reacted_' . $postId . '_' . $reaction;
        $increment = 1;
        if (isset($_SESSION[$sessionKey])) {
            $reactions[$reaction] = max(0, $reactions[$reaction] - 1);
            unset($_SESSION[$sessionKey]);
            $increment = -1;
        } else {
            $reactions[$reaction]++;
            $_SESSION[$sessionKey] = true;
        }

        $jsonStr = json_encode($reactions);
        $db = Database::getInstance();
        $db->query("UPDATE posts SET reactions_json = ? WHERE id = ?", [$jsonStr, $postId]);

        if ($reaction === 'like') {
            $db->query("UPDATE posts SET likes = ? WHERE id = ?", [$reactions['like'], $postId]);
        }

        echo json_encode([
            'success' => true,
            'reactions' => $reactions,
            'type' => $reaction,
            'change' => $increment
        ]);
        exit;
    }
}
