<?php
/**
 * Digiws - Blog Comment Controller (AJAX)
 */

class BlogCommentController {

    public function store(): void {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            return;
        }

        $postId    = (int)($_POST['post_id'] ?? 0);
        $content   = trim($_POST['content'] ?? '');
        $parentId  = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

        if (!$postId || strlen($content) < 5) {
            echo json_encode(['success' => false, 'message' => 'Comment too short.']);
            return;
        }

        if (strlen($content) > 2000) {
            echo json_encode(['success' => false, 'message' => 'Comment is too long (max 2000 chars).']);
            return;
        }

        // Honeypot spam check
        if (!empty($_POST['website'])) {
            echo json_encode(['success' => false, 'message' => 'Spam detected.']);
            return;
        }

        $data = [
            'post_id'   => $postId,
            'content'   => htmlspecialchars($content, ENT_QUOTES, 'UTF-8'),
            'parent_id' => $parentId,
            'status'    => 'pending',
            'ip_address'=> $_SERVER['REMOTE_ADDR'] ?? null,
        ];

        if (isLoggedIn()) {
            $data['user_id'] = currentUserId();
        } else {
            $guestName  = trim($_POST['guest_name'] ?? '');
            $guestEmail = trim($_POST['guest_email'] ?? '');
            if (empty($guestName) || strlen($guestName) < 2) {
                echo json_encode(['success' => false, 'message' => 'Name is required.']);
                return;
            }
            if (!empty($guestEmail) && !filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
                return;
            }
            $data['guest_name']  = sanitize($guestName);
            $data['guest_email'] = sanitize($guestEmail);
        }

        $commentModel = new BlogComment();
        $id = $commentModel->create($data);

        if ($id) {
            echo json_encode([
                'success' => true,
                'message' => 'Your comment has been submitted and is awaiting moderation. Thank you!'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit comment. Please try again.']);
        }
    }

    public function like(): void {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]);
            return;
        }

        $commentId = (int)($_POST['comment_id'] ?? 0);
        if (!$commentId) {
            echo json_encode(['success' => false, 'message' => 'Invalid comment.']);
            return;
        }

        $ip   = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $hash = hash('sha256', $ip . date('Y-m-d') . 'comment_like');

        $commentModel = new BlogComment();
        $liked = $commentModel->addLike($commentId, $hash);

        $comment = $commentModel->find($commentId);
        echo json_encode([
            'success' => true,
            'liked'   => $liked,
            'likes'   => $comment['likes'] ?? 0,
            'message' => $liked ? 'Liked!' : 'Already liked.',
        ]);
    }
}
