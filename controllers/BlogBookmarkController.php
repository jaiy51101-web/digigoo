<?php
/**
 * Digiws - Blog Bookmark Controller (AJAX)
 */

class BlogBookmarkController {

    public function toggle(): void {
        header('Content-Type: application/json');

        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login to bookmark articles.', 'login' => true]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]);
            return;
        }

        $postId = (int)($_POST['post_id'] ?? 0);
        if (!$postId) {
            echo json_encode(['success' => false, 'message' => 'Invalid post.']);
            return;
        }

        $bookmarkModel = new BlogBookmark();
        $result = $bookmarkModel->toggle(currentUserId(), $postId);

        echo json_encode([
            'success'    => true,
            'bookmarked' => $result['bookmarked'],
            'message'    => $result['bookmarked'] ? 'Article bookmarked!' : 'Bookmark removed.',
        ]);
    }
}
