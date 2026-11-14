<?php
/**
 * Digiws - Website Request Controller
 */

class WebsiteRequestController {

    public function __construct() {
        requireLogin();
    }

    /**
     * Store new request
     */
    public function store(): void {
        if (!verifyCsrf()) {
            jsonResponse(['success' => false, 'message' => 'Invalid request.'], 403);
        }

        $type = sanitize($_POST['website_type'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $budget = sanitize($_POST['budget'] ?? '');

        if (empty($type) || empty($description)) {
            jsonResponse(['success' => false, 'message' => 'Please fill in all required fields.']);
        }

        $requestModel = new WebsiteRequest();
        $id = $requestModel->create([
            'user_id'      => currentUserId(),
            'website_type' => $type,
            'description'  => $description,
            'budget'       => $budget
        ]);

        if ($id) {
            jsonResponse(['success' => true, 'message' => 'Your request has been submitted! We will contact you soon.']);
        } else {
            jsonResponse(['success' => false, 'message' => 'Failed to submit request. Please try again.']);
        }
    }

    /**
     * User dashboard view of requests
     */
    public function userIndex(): void {
        $requestModel = new WebsiteRequest();
        $requests = $requestModel->getByUserId(currentUserId());

        $pageTitle = 'My Website Requests - ' . APP_NAME;
        include TEMPLATES_PATH . '/header.php';
        include VIEWS_PATH . '/user/website-requests.php';
        include TEMPLATES_PATH . '/footer.php';
    }

    /**
     * Admin view of all requests
     */
    public function adminIndex(): void {
        if (!isAdmin()) redirect(url('admin/login'));

        $requestModel = new WebsiteRequest();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $requestModel->getAll($page, 20);

        $requests = $result['requests'];
        $total = $result['total'];

        $pageTitle = 'Website Requests - Admin';
        $adminPage = 'website_requests';
        $contentView = VIEWS_PATH . '/admin/website-requests.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    /**
     * Admin update status
     */
    public function updateStatus(string $id): void {
        if (!isAdmin()) jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
        if (!verifyCsrf()) jsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 403);

        $status = sanitize($_POST['status'] ?? 'pending');
        $requestModel = new WebsiteRequest();
        $requestModel->updateStatus((int)$id, $status);

        jsonResponse(['success' => true, 'message' => 'Status updated.']);
    }

    /**
     * Admin delete request
     */
    public function delete(string $id): void {
        if (!isAdmin()) {
            setFlash('danger', 'Unauthorized access.');
            redirect(url('admin/login'));
        }

        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/website-requests'));
        }

        $db = Database::getInstance();
        $db->delete('website_requests', 'id = ?', [(int)$id]);

        setFlash('success', 'Website request deleted successfully.');
        redirect(url('admin/website-requests'));
    }
}
