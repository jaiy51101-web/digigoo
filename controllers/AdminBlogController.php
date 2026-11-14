<?php
/**
 * Digiws - Admin Blog Controller (Full Enterprise)
 */

class AdminBlogController {

    public function __construct() {
        requireAdmin();
    }

    /**
     * List all blog posts with filters + bulk actions
     */
    public function index(): void {
        $postModel = new Post();

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;

        $filters = ['all_status' => true];
        if (!empty($_GET['q']))        $filters['search']      = sanitize($_GET['q']);
        if (!empty($_GET['status']))   $filters['status']      = sanitize($_GET['status']);
        if (!empty($_GET['category'])) $filters['category_slug'] = sanitize($_GET['category']);

        $result     = $postModel->getAll($page, $perPage, $filters);
        $posts      = $result['posts'];
        $totalPosts = $result['total'];
        $categories = (new BlogCategory())->getAll(false);
        $pendingComments = (new BlogComment())->getPendingCount();

        $pageTitle   = 'Blog Posts — Admin';
        $adminPage   = 'blog';
        $contentView = VIEWS_PATH . '/admin/blog-list.php';

        include VIEWS_PATH . '/admin/layout.php';
    }

    /**
     * Bulk action (publish / draft / delete)
     */
    public function bulkAction(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/blog'));
        }
        $ids    = array_map('intval', $_POST['ids'] ?? []);
        $action = sanitize($_POST['bulk_action'] ?? '');

        if (empty($ids) || empty($action)) {
            setFlash('warning', 'Select posts and an action.');
            redirect(url('admin/blog'));
        }

        (new Post())->bulkAction($ids, $action);
        setFlash('success', ucfirst($action) . ' applied to ' . count($ids) . ' post(s).');
        redirect(url('admin/blog'));
    }

    /**
     * Create post form
     */
    public function create(): void {
        $post       = null;
        $categories = (new BlogCategory())->getAll(false);
        $allTags    = (new BlogTag())->getAll();
        $products   = (new Product())->getAll(1, 100)['products'] ?? [];

        $pageTitle   = 'Add Blog Post — Admin';
        $adminPage   = 'blog';
        $contentView = VIEWS_PATH . '/admin/blog-form.php';

        include VIEWS_PATH . '/admin/layout.php';
    }

    /**
     * Store new blog post
     */
    public function store(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/blog/create'));
        }

        $title   = sanitize($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $summary = sanitize($_POST['summary'] ?? '');
        $status  = in_array($_POST['status'] ?? '', ['published','draft','scheduled']) ? $_POST['status'] : 'draft';

        if (empty($title)) {
            setFlash('danger', 'Title is required.');
            redirect(url('admin/blog/create'));
        }
        if (empty($content)) {
            setFlash('danger', 'Content is required.');
            redirect(url('admin/blog/create'));
        }

        $slug = uniqueSlug($title, 'posts');

        $data = [
            'title'            => $title,
            'slug'             => sanitize($_POST['slug'] ?? $slug) ?: $slug,
            'content'          => $content,
            'summary'          => $summary,
            'status'           => $status,
            'author_id'        => currentUserId(),
            'blog_category_id' => !empty($_POST['blog_category_id']) ? (int)$_POST['blog_category_id'] : null,
            'promote_product_id' => !empty($_POST['promote_product_id']) ? (int)$_POST['promote_product_id'] : null,
            'meta_title'       => sanitize($_POST['meta_title'] ?? ''),
            'meta_description' => sanitize($_POST['meta_description'] ?? ''),
            'meta_keywords'    => sanitize($_POST['meta_keywords'] ?? ''),
            'focus_keyword'    => sanitize($_POST['focus_keyword'] ?? ''),
            'canonical_url'    => sanitize($_POST['canonical_url'] ?? ''),
            'robots'           => sanitize($_POST['robots'] ?? 'index,follow'),
            'schema_type'      => sanitize($_POST['schema_type'] ?? 'Article'),
            'og_title'         => sanitize($_POST['og_title'] ?? ''),
            'og_description'   => sanitize($_POST['og_description'] ?? ''),
            'is_featured'      => isset($_POST['is_featured']) ? 1 : 0,
            'is_trending'      => isset($_POST['is_trending']) ? 1 : 0,
            'is_editors_pick'  => isset($_POST['is_editors_pick']) ? 1 : 0,
            'allow_comments'   => isset($_POST['allow_comments']) ? 1 : 0,
            'scheduled_at'     => !empty($_POST['scheduled_at']) ? sanitize($_POST['scheduled_at']) : null,
        ];

        // Handle custom slug
        if (!empty($_POST['slug'])) {
            $customSlug = generateSlug($_POST['slug']);
            $data['slug'] = uniqueSlug($customSlug, 'posts');
        }

        // Featured image
        if (!empty($_FILES['image']['name'])) {
            $imagePath = uploadFile($_FILES['image'], 'blog');
            if ($imagePath) {
                $data['image'] = $imagePath;
            } else {
                setFlash('danger', 'Failed to upload featured image.');
                redirect(url('admin/blog/create'));
            }
        }

        // OG Image
        if (!empty($_FILES['og_image']['name'])) {
            $ogPath = uploadFile($_FILES['og_image'], 'blog');
            if ($ogPath) $data['og_image'] = $ogPath;
        }

        $postModel = new Post();
        $postId    = $postModel->create($data);

        // Sync tags
        $tagNames = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
        if (!empty($tagNames)) {
            (new BlogTag())->syncPostTags($postId, $tagNames);
            $data['tags_cache'] = implode(', ', $tagNames);
            $postModel->update($postId, ['tags_cache' => $data['tags_cache']]);
        }

        setFlash('success', 'Blog post created successfully!');
        redirect(url('admin/blog'));
    }

    /**
     * Edit post form
     */
    public function edit(int $id): void {
        $postModel = new Post();
        $post      = $postModel->find($id);

        if (!$post) {
            setFlash('danger', 'Post not found.');
            redirect(url('admin/blog'));
        }

        $categories = (new BlogCategory())->getAll(false);
        $allTags    = (new BlogTag())->getAll();
        $postTags   = $post['tags'] ?? [];
        $products   = (new Product())->getAll(1, 100)['products'] ?? [];

        $pageTitle   = 'Edit Post — Admin';
        $adminPage   = 'blog';
        $contentView = VIEWS_PATH . '/admin/blog-form.php';

        include VIEWS_PATH . '/admin/layout.php';
    }

    /**
     * Update blog post
     */
    public function update(int $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/blog'));
        }

        $postModel = new Post();
        $post      = $postModel->find($id);

        if (!$post) {
            setFlash('danger', 'Post not found.');
            redirect(url('admin/blog'));
        }

        $title   = sanitize($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $summary = sanitize($_POST['summary'] ?? '');
        $status  = in_array($_POST['status'] ?? '', ['published','draft','scheduled']) ? $_POST['status'] : 'draft';

        if (empty($title) || empty($content)) {
            setFlash('danger', 'Title and content are required.');
            redirect(url("admin/blog/edit/{$id}"));
        }

        // Slug management
        $slug = $post['slug'];
        if (!empty($_POST['slug'])) {
            $newSlug = generateSlug($_POST['slug']);
            if ($newSlug !== $post['slug']) {
                $slug = uniqueSlug($newSlug, 'posts', $id);
            }
        } elseif ($title !== $post['title']) {
            $slug = uniqueSlug($title, 'posts', $id);
        }

        $data = [
            'title'            => $title,
            'slug'             => $slug,
            'content'          => $content,
            'summary'          => $summary,
            'status'           => $status,
            'blog_category_id' => !empty($_POST['blog_category_id']) ? (int)$_POST['blog_category_id'] : null,
            'promote_product_id' => !empty($_POST['promote_product_id']) ? (int)$_POST['promote_product_id'] : null,
            'meta_title'       => sanitize($_POST['meta_title'] ?? ''),
            'meta_description' => sanitize($_POST['meta_description'] ?? ''),
            'meta_keywords'    => sanitize($_POST['meta_keywords'] ?? ''),
            'focus_keyword'    => sanitize($_POST['focus_keyword'] ?? ''),
            'canonical_url'    => sanitize($_POST['canonical_url'] ?? ''),
            'robots'           => sanitize($_POST['robots'] ?? 'index,follow'),
            'schema_type'      => sanitize($_POST['schema_type'] ?? 'Article'),
            'og_title'         => sanitize($_POST['og_title'] ?? ''),
            'og_description'   => sanitize($_POST['og_description'] ?? ''),
            'is_featured'      => isset($_POST['is_featured']) ? 1 : 0,
            'is_trending'      => isset($_POST['is_trending']) ? 1 : 0,
            'is_editors_pick'  => isset($_POST['is_editors_pick']) ? 1 : 0,
            'allow_comments'   => isset($_POST['allow_comments']) ? 1 : 0,
            'scheduled_at'     => !empty($_POST['scheduled_at']) ? sanitize($_POST['scheduled_at']) : null,
        ];

        if (!empty($_FILES['image']['name'])) {
            $imagePath = uploadFile($_FILES['image'], 'blog');
            if ($imagePath) {
                if ($post['image']) deleteFile($post['image']);
                $data['image'] = $imagePath;
            }
        }

        if (!empty($_FILES['og_image']['name'])) {
            $ogPath = uploadFile($_FILES['og_image'], 'blog');
            if ($ogPath) {
                if (!empty($post['og_image'])) deleteFile($post['og_image']);
                $data['og_image'] = $ogPath;
            }
        }

        $postModel->update($id, $data);

        // Sync tags
        $tagNames = array_filter(array_map('trim', explode(',', $_POST['tags'] ?? '')));
        (new BlogTag())->syncPostTags($id, $tagNames);
        $postModel->update($id, ['tags_cache' => implode(', ', $tagNames)]);

        setFlash('success', 'Blog post updated successfully!');
        redirect(url('admin/blog'));
    }

    /**
     * Delete blog post
     */
    public function delete(int $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/blog'));
        }

        $postModel = new Post();
        $post      = $postModel->find($id);

        if (!$post) {
            setFlash('danger', 'Post not found.');
            redirect(url('admin/blog'));
        }

        if ($post['image']) deleteFile($post['image']);
        $postModel->delete($id);

        setFlash('success', 'Blog post deleted.');
        redirect(url('admin/blog'));
    }

    /**
     * Comments moderation
     */
    public function comments(): void {
        $commentModel = new BlogComment();
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;

        $filters = [];
        if (!empty($_GET['status'])) $filters['status'] = sanitize($_GET['status']);

        $result   = $commentModel->getAll($page, $perPage, $filters);
        $comments = $result['comments'];
        $total    = $result['total'];
        $totalPages = (int)ceil($total / $perPage);

        $pageTitle   = 'Blog Comments — Admin';
        $adminPage   = 'blog_comments';
        $contentView = VIEWS_PATH . '/admin/blog-comments.php';

        include VIEWS_PATH . '/admin/layout.php';
    }

    /**
     * Update comment status
     */
    public function commentStatus(int $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/blog/comments'));
        }
        $status = sanitize($_POST['status'] ?? 'approved');
        (new BlogComment())->updateStatus($id, $status);
        setFlash('success', 'Comment status updated.');
        redirect(url('admin/blog/comments'));
    }

    /**
     * Delete a comment
     */
    public function commentDelete(int $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/blog/comments'));
        }
        (new BlogComment())->delete($id);
        setFlash('success', 'Comment deleted.');
        redirect(url('admin/blog/comments'));
    }

    /**
     * Blog analytics dashboard
     */
    public function analytics(): void {
        $analytics = (new Post())->getAnalytics();

        $pageTitle   = 'Blog Analytics — Admin';
        $adminPage   = 'blog_analytics';
        $contentView = VIEWS_PATH . '/admin/blog-analytics.php';

        include VIEWS_PATH . '/admin/layout.php';
    }
}
