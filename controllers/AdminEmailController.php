<?php
/**
 * Digiws - Admin Email Controller
 */

class AdminEmailController {

    public function __construct() {
        requireAdmin();
    }

    public function index(): void {
        $templateModel = new EmailTemplate();
        $templates = $templateModel->getAll();

        $pageTitle = 'Email Templates - Admin';
        $adminPage = 'email_templates';
        $contentView = VIEWS_PATH . '/admin/email-templates.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function edit(string $id): void {
        $templateModel = new EmailTemplate();
        $template = $templateModel->find((int)$id);

        if (!$template) {
            setFlash('danger', 'Template not found.');
            redirect(url('admin/email-templates'));
        }

        $pageTitle = 'Edit Email Template - Admin';
        $adminPage = 'email_templates';
        $contentView = VIEWS_PATH . '/admin/email-template-form.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function update(string $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/email-templates'));
        }

        $templateModel = new EmailTemplate();
        $data = [
            'subject' => sanitize($_POST['subject'] ?? ''),
            'body'    => $_POST['body'] ?? ''
        ];

        if (empty($data['subject']) || empty($data['body'])) {
            setFlash('danger', 'Subject and Body are required.');
            redirect(url('admin/email-templates/edit/' . $id));
        }

        $templateModel->update((int)$id, $data);

        setFlash('success', 'Email template updated.');
        redirect(url('admin/email-templates'));
    }

    public function sendTestEmail(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/email-templates'));
        }

        $toEmail = sanitize($_POST['test_email'] ?? '');
        if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            setFlash('danger', 'Please enter a valid email address.');
            redirect(url('admin/email-templates'));
        }

        try {
            $mailer = new Mailer();
            $subject = "Test SMTP Email — Digiws Private Limited";
            $body = "<h3>SMTP Configuration Test Successful! 🎉</h3>"
                  . "<p>If you are reading this email, it means your SMTP configuration on <strong>" . APP_NAME . "</strong> is working perfectly!</p>"
                  . "<p>Sent at: " . date('Y-m-d H:i:s T') . "</p>"
                  . "<p>Thank you,<br><strong>Digiws Private Limited</strong></p>";

            $success = $mailer->send($toEmail, $subject, $body);

            if ($success) {
                setFlash('success', 'Test email sent successfully to ' . htmlspecialchars($toEmail));
            } else {
                setFlash('danger', 'Test email was not sent. Check your SMTP configuration or logs.');
            }
        } catch (Exception $e) {
            setFlash('danger', 'Email Sending Failed: ' . htmlspecialchars($e->getMessage()));
        }

        redirect(url('admin/email-templates'));
    }
}
