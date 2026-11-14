<?php
/**
 * Blog Editor Controller - Advanced Block-Based Editor
 * Handles all block editor operations, API endpoints for AJAX
 */

class BlogEditorController {

    /**
     * Show advanced blog editor
     */
    public function editor(): void {
        $postId = (int)($_GET['id'] ?? 0);
        $post = null;
        $blocks = [];
        $media = [];

        if ($postId) {
            $post = (new Post())->find($postId);
            if (!$post) {
                setFlash('danger', 'Post not found.');
                redirect(url('admin/blog'));
            }
            $blocks = (new BlogBlock())->getPostBlocks($postId);
            $media = (new BlogMedia())->getPostMedia($postId);
        }

        $categories = (new BlogCategory())->getAll(false);
        $templates = $this->db->fetchAll("SELECT * FROM blog_templates WHERE is_active = 1");
        $allTags = (new BlogTag())->getAll();

        $pageTitle = $postId ? 'Edit Article — Advanced Editor' : 'Create Article — Advanced Editor';
        $adminPage = 'blog';
        $contentView = VIEWS_PATH . '/admin/blog-advanced-editor.php';

        include VIEWS_PATH . '/admin/layout.php';
    }

    /**
     * API: Add block
     */
    public function apiAddBlock(): void {
        if (!verifyCsrf() || !isAjax()) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $postId = (int)($data['post_id'] ?? 0);
        $index = (int)($data['index'] ?? 0);
        $blockType = sanitize($data['block_type'] ?? '');
        $blockData = $data['block_data'] ?? [];

        if (!$postId || empty($blockType)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid parameters']);
            exit;
        }

        $blockId = (new BlogBlock())->addBlock($postId, $index, $blockType, $blockData);

        echo json_encode([
            'success' => true,
            'block_id' => $blockId,
            'message' => 'Block added successfully',
        ]);
        exit;
    }

    /**
     * API: Update block
     */
    public function apiUpdateBlock(): void {
        if (!verifyCsrf() || !isAjax()) {
            http_response_code(403);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $blockId = (int)($data['block_id'] ?? 0);
        $blockData = $data['block_data'] ?? [];

        if (!$blockId) {
            http_response_code(400);
            exit;
        }

        (new BlogBlock())->updateBlock($blockId, $blockData);

        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * API: Delete block
     */
    public function apiDeleteBlock(): void {
        if (!verifyCsrf() || !isAjax()) {
            http_response_code(403);
            exit;
        }

        $blockId = (int)($_POST['block_id'] ?? 0);
        if (!$blockId) {
            http_response_code(400);
            exit;
        }

        (new BlogBlock())->deleteBlock($blockId);
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * API: Reorder blocks
     */
    public function apiReorderBlocks(): void {
        if (!verifyCsrf() || !isAjax()) {
            http_response_code(403);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $postId = (int)($data['post_id'] ?? 0);
        $blockOrder = $data['block_order'] ?? [];

        if (!$postId) {
            http_response_code(400);
            exit;
        }

        (new BlogBlock())->reorderBlocks($postId, $blockOrder);
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * API: Upload media
     */
    public function apiUploadMedia(): void {
        if (!verifyCsrf()) {
            http_response_code(403);
            exit;
        }

        if (empty($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            exit;
        }

        $postId = (int)($_POST['post_id'] ?? 0);
        $userId = getCurrentUser()['id'];

        $mediaId = (new BlogMedia())->uploadMedia($_FILES['file'], $userId, $postId);

        if (!$mediaId) {
            http_response_code(400);
            echo json_encode(['error' => 'Upload failed']);
            exit;
        }

        $media = (new BlogMedia())->getMedia($mediaId);
        echo json_encode(['success' => true, 'media' => $media]);
        exit;
    }

    /**
     * API: Get media library
     */
    public function apiGetMedia(): void {
        $page = (int)($_GET['page'] ?? 1);
        $postId = (int)($_GET['post_id'] ?? 0);

        $filters = [];
        if (!empty($_GET['type'])) {
            $filters['file_type'] = sanitize($_GET['type']);
        }
        if (!empty($_GET['search'])) {
            $filters['search'] = sanitize($_GET['search']);
        }

        $result = (new BlogMedia())->getLibrary($page, 30, $filters);
        echo json_encode($result);
        exit;
    }

    /**
     * API: Autosave post
     */
    public function apiAutosave(): void {
        if (!verifyCsrf() || !isAjax()) {
            http_response_code(403);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $postId = (int)($data['post_id'] ?? 0);
        $title = sanitize($data['title'] ?? '');
        $summary = sanitize($data['summary'] ?? '');
        $blocks = $data['blocks'] ?? [];
        $meta = $data['meta'] ?? [];

        if (!$postId || empty($title)) {
            http_response_code(400);
            exit;
        }

        $userId = getCurrentUser()['id'];
        $revision = new BlogRevision();
        
        // Create autosave revision
        $revisionId = $revision->createRevision(
            $postId,
            $title,
            $summary,
            $blocks,
            $meta,
            $userId,
            'auto',
            null,
            true
        );

        // Clean old autosaves
        $revision->cleanOldAutosaves($postId, 10);

        echo json_encode([
            'success' => true,
            'revision_id' => $revisionId,
            'message' => 'Autosaved at ' . date('H:i'),
        ]);
        exit;
    }

    /**
     * API: Analyze SEO
     */
    public function apiAnalyzeSEO(): void {
        $postId = (int)($_GET['post_id'] ?? 0);

        if (!$postId) {
            http_response_code(400);
            exit;
        }

        $seoAnalysis = (new BlogSEO())->analyzePost($postId);
        echo json_encode($seoAnalysis);
        exit;
    }

    /**
     * API: Get revisions
     */
    public function apiGetRevisions(): void {
        $postId = (int)($_GET['post_id'] ?? 0);

        if (!$postId) {
            http_response_code(400);
            exit;
        }

        $revisions = (new BlogRevision())->getPostRevisions($postId);
        echo json_encode(['revisions' => $revisions]);
        exit;
    }

    /**
     * API: Restore revision
     */
    public function apiRestoreRevision(): void {
        if (!verifyCsrf() || !isAjax()) {
            http_response_code(403);
            exit;
        }

        $revisionId = (int)($_POST['revision_id'] ?? 0);
        $userId = getCurrentUser()['id'];

        if (!$revisionId) {
            http_response_code(400);
            exit;
        }

        $success = (new BlogRevision())->restoreRevision($revisionId, $userId);
        echo json_encode(['success' => $success]);
        exit;
    }

    /**
     * API: Generate AI content
     */
    public function apiAIGenerate(): void {
        if (!verifyCsrf() || !isAjax()) {
            http_response_code(403);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $prompt = sanitize($data['prompt'] ?? '');
        $action = sanitize($data['action'] ?? '');

        if (empty($prompt) || empty($action)) {
            http_response_code(400);
            exit;
        }

        // Call AI service (placeholder - integrate with OpenAI, Claude, etc.)
        $aiContent = $this->callAIService($action, $prompt);

        echo json_encode([
            'success' => !!$aiContent,
            'content' => $aiContent,
        ]);
        exit;
    }

    /**
     * API: Save post
     */
    public function apiSavePost(): void {
        if (!verifyCsrf() || !isAjax()) {
            http_response_code(403);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $postId = (int)($data['post_id'] ?? 0);
        $title = sanitize($data['title'] ?? '');
        $summary = sanitize($data['summary'] ?? '');
        $blocks = $data['blocks'] ?? [];
        $meta = $data['meta'] ?? [];
        $status = in_array($data['status'] ?? '', ['draft', 'published', 'scheduled']) ? $data['status'] : 'draft';

        if (!$title || empty($blocks)) {
            http_response_code(400);
            echo json_encode(['error' => 'Title and content required']);
            exit;
        }

        $userId = getCurrentUser()['id'];
        $revision = new BlogRevision();

        try {
            if ($postId) {
                // Update existing post
                $post = (new Post())->find($postId);
                if (!$post) {
                    http_response_code(404);
                    exit;
                }

                // Delete old blocks
                $this->db->delete('blog_post_blocks', ['post_id' => $postId]);

                // Insert new blocks
                $blockModel = new BlogBlock();
                foreach ($blocks as $index => $block) {
                    $blockModel->addBlock(
                        $postId,
                        $index,
                        $block['type'],
                        $block['data'] ?? [],
                        $block['attributes'] ?? null
                    );
                }

                // Update post
                (new Post())->update($postId, [
                    'title' => $title,
                    'summary' => $summary,
                    'status' => $status,
                    'word_count' => (new BlogBlock())->calculateWordCount($postId),
                ]);

                // Create revision
                $revision->createRevision(
                    $postId,
                    $title,
                    $summary,
                    $blocks,
                    $meta,
                    $userId,
                    'manual'
                );

                $message = 'Post updated successfully';
            } else {
                // Create new post
                $slug = uniqueSlug($title, 'posts');

                $postId = $this->db->insert('posts', [
                    'title' => $title,
                    'slug' => $slug,
                    'summary' => $summary,
                    'content' => json_encode($blocks),
                    'status' => $status,
                    'author_id' => $userId,
                ]);

                // Insert blocks
                $blockModel = new BlogBlock();
                foreach ($blocks as $index => $block) {
                    $blockModel->addBlock(
                        $postId,
                        $index,
                        $block['type'],
                        $block['data'] ?? [],
                        $block['attributes'] ?? null
                    );
                }

                // Create first revision
                $revision->createRevision(
                    $postId,
                    $title,
                    $summary,
                    $blocks,
                    $meta,
                    $userId,
                    'manual'
                );

                $message = 'Post created successfully';
            }

            // Analyze SEO
            (new BlogSEO())->analyzePost($postId);

            echo json_encode([
                'success' => true,
                'post_id' => $postId,
                'message' => $message,
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Call AI service for content generation
     */
    private function callAIService(string $action, string $prompt): ?string {
        // Placeholder implementation
        // In production, integrate with OpenAI, Claude, or other AI services

        $aiProvider = getSetting('blog_ai_provider');
        $aiKey = getSetting('blog_ai_api_key');

        if (empty($aiProvider) || empty($aiKey)) {
            return null;
        }

        // Example integration with OpenAI
        if ($aiProvider === 'openai') {
            return $this->callOpenAI($prompt, $aiKey);
        }

        return null;
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI(string $prompt, string $apiKey): ?string {
        // Implementation would go here
        return null;
    }

    // Database helper
    private Database $db;

    public function __construct() {
        requireAdmin();
        $this->db = Database::getInstance();
    }
}
