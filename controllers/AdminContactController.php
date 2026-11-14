<?php
/**
 * Digiws - Admin Contact Messages (Complaints) Controller
 */

class AdminContactController {
    public function __construct() {
        requireAdmin();
    }

    public function index(): void {
        $db = Database::getInstance();

        $status = $_GET['status'] ?? '';
        $where = '';
        $params = [];
        if ($status === 'unread') {
            $where = 'WHERE is_read = 0';
        } elseif ($status === 'read') {
            $where = 'WHERE is_read = 1';
        }

        $messages = $db->fetchAll(
            "SELECT * FROM contact_messages {$where} ORDER BY created_at DESC",
            $params
        );

        $unreadCount = (int) $db->fetchColumn("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");

        $pageTitle = 'Contact Messages';
        $adminPage = 'contact_messages';
        $contentView = VIEWS_PATH . '/admin/contact-messages.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function markRead(string $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/contact-messages'));
        }
        $db = Database::getInstance();
        $db->update('contact_messages', ['is_read' => 1], 'id = ?', [(int)$id]);
        setFlash('success', 'Message marked as read.');
        redirect(url('admin/contact-messages'));
    }

    public function delete(string $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/contact-messages'));
        }
        $db = Database::getInstance();
        $db->delete('contact_messages', 'id = ?', [(int)$id]);
        setFlash('success', 'Message deleted.');
        redirect(url('admin/contact-messages'));
    }
}
