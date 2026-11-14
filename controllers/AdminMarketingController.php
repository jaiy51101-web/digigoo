<?php
/**
 * DigiWS - Admin Marketing Controller
 */

class AdminMarketingController {
    private MarketingSubscriber $subModel;
    private MarketingList $listModel;
    private MarketingSegment $segmentModel;

    public function __construct() {
        requireAdmin();
        $this->subModel = new MarketingSubscriber();
        $this->listModel = new MarketingList();
        $this->segmentModel = new MarketingSegment();
    }

    /**
     * WhatsApp Broadcast Dashboard
     */
    public function whatsappCampaign(): void {
        $settingModel = new Setting();
        $settings = $settingModel->getAll();
        
        $pageTitle = 'WhatsApp Marketing Broadcasts - Admin';
        $adminPage = 'marketing_whatsapp_campaign';
        $contentView = VIEWS_PATH . '/admin/marketing/whatsapp-campaign.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    /**
     * Send WhatsApp Broadcast Campaign
     */
    public function whatsappCampaignSend(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request credentials.');
            redirect(url('admin/marketing/whatsapp-campaign'));
        }

        $target = sanitize($_POST['target_audience'] ?? 'test');
        $rawMessage = trim($_POST['message'] ?? '');
        $footerText = trim(sanitize($_POST['footer'] ?? ''));

        if (empty($rawMessage)) {
            setFlash('danger', 'Message body cannot be empty.');
            redirect(url('admin/marketing/whatsapp-campaign'));
        }

        $whatsapp = new WhatsAppService();
        $db = Database::getInstance();
        $recipients = [];

        if ($target === 'test') {
            $testNum = trim(sanitize($_POST['test_number'] ?? ''));
            if (!empty($testNum)) {
                $recipients[] = [
                    'name'  => 'Test User',
                    'phone' => $testNum
                ];
            }
        } elseif ($target === 'subscribers') {
            $recipients = $db->fetchAll(
                "SELECT ms.name, u.phone 
                 FROM marketing_subscribers ms
                 JOIN users u ON ms.email = u.email
                 WHERE ms.status = 'active' AND u.phone IS NOT NULL AND u.phone != ''"
            );
        } elseif ($target === 'customers') {
            $recipients = $db->fetchAll(
                "SELECT name, phone FROM users WHERE phone IS NOT NULL AND phone != ''"
            );
        }

        if (empty($recipients)) {
            setFlash('warning', 'No valid recipients found with associated phone numbers.');
            redirect(url('admin/marketing/whatsapp-campaign'));
        }

        $success = 0;
        $failed = 0;
        $errors = [];

        foreach ($recipients as $rec) {
            $phone = $rec['phone'];
            $name = $rec['name'] ?: 'Customer';
            $message = str_replace('{{customer_name}}', $name, $rawMessage);

            $res = $whatsapp->sendMessage($phone, $message, $footerText);
            if ($res['success']) {
                $success++;
            } else {
                $failed++;
                $errors[] = "Number: {$phone} - Error: " . $res['message'];
            }
        }

        if ($failed === 0) {
            setFlash('success', "WhatsApp Broadcast sent successfully to {$success} recipients!");
        } else {
            $errDetail = !empty($errors) ? '<br>Errors:<br>' . implode('<br>', array_slice($errors, 0, 5)) : '';
            setFlash('warning', "WhatsApp Broadcast complete. Sent: {$success}, Failed: {$failed}." . $errDetail);
        }

        redirect(url('admin/marketing/whatsapp-campaign'));
    }

    /**
     * Marketing Dashboard
     */
    public function dashboard(): void {
        $db = Database::getInstance();
        
        $stats = [
            'total_audience'    => $db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE email LIKE '%@%.%'") ?: 0,
            'total_emails'      => $db->fetchColumn("SELECT COUNT(DISTINCT email) FROM marketing_subscribers WHERE email LIKE '%@%.%'") ?: 0,
            'subscribers'       => $db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE category = 'subscriber' AND email LIKE '%@%.%'") ?: 0,
            'active_users'      => $db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE category = 'active_user' AND email LIKE '%@%.%'") ?: 0,
            'interested_users'  => $db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE category = 'interested_user' AND email LIKE '%@%.%'") ?: 0,
            'exclusive_users'   => $db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE category = 'exclusive_user' AND email LIKE '%@%.%'") ?: 0,
            'suspended_users'   => $db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE status = 'suspended' AND email LIKE '%@%.%'") ?: 0,
            'banned_users'      => $db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE status = 'banned' AND email LIKE '%@%.%'") ?: 0,
            'today_emails'      => $db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE DATE(created_at) = CURDATE() AND email LIKE '%@%.%'") ?: 0,
            'lists_count'       => $db->fetchColumn("SELECT COUNT(*) FROM marketing_lists") ?: 0,
            'segments_count'    => $db->fetchColumn("SELECT COUNT(*) FROM marketing_segments") ?: 0,
            'queue_pending'     => $db->fetchColumn("SELECT COUNT(*) FROM marketing_queue WHERE status = 'pending'") ?: 0,
        ];

        $lists = $this->listModel->all();
        $segments = $this->segmentModel->all();

        // Recent logs
        $recentLogs = $db->fetchAll(
            "SELECT ml.*, ms.email, ms.name FROM marketing_logs ml 
             JOIN marketing_subscribers ms ON ml.subscriber_id = ms.id 
             ORDER BY ml.created_at DESC LIMIT 10"
        );

        $pageTitle = 'CRM Audience Management - Admin';
        $adminPage = 'marketing_dashboard';
        $contentView = VIEWS_PATH . '/admin/marketing/dashboard.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    /**
     * Lists all subscribers with dynamic rule filtering and category stats
     */
    public function subscribers(): void {
        $db = Database::getInstance();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Audience Stats for the top cards
        $audienceStats = [
            'total_audience'    => (int)($db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE email LIKE '%@%.%'") ?: 0),
            'total_emails'      => (int)($db->fetchColumn("SELECT COUNT(DISTINCT email) FROM marketing_subscribers WHERE email LIKE '%@%.%'") ?: 0),
            'subscribers'       => (int)($db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE category = 'subscriber' AND email LIKE '%@%.%'") ?: 0),
            'active_users'      => (int)($db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE category = 'active_user' AND email LIKE '%@%.%'") ?: 0),
            'interested_users'  => (int)($db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE category = 'interested_user' AND email LIKE '%@%.%'") ?: 0),
            'exclusive_users'   => (int)($db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE category = 'exclusive_user' AND email LIKE '%@%.%'") ?: 0),
            'suspended'         => (int)($db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE status = 'suspended' AND email LIKE '%@%.%'") ?: 0),
            'banned'            => (int)($db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE status = 'banned' AND email LIKE '%@%.%'") ?: 0),
            'today_emails'      => (int)($db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE DATE(created_at) = CURDATE() AND email LIKE '%@%.%'") ?: 0),
        ];

        // Category filter from stat card clicks
        $categoryFilter = isset($_GET['category']) ? sanitize($_GET['category']) : '';

        // Dynamic Filtering (filter builder rules)
        $rules = [];
        if (isset($_GET['rules']) && is_array($_GET['rules'])) {
            foreach ($_GET['rules'] as $rule) {
                if (!empty($rule['field']) && !empty($rule['operator'])) {
                    $rules[] = [
                        'field' => sanitize($rule['field']),
                        'operator' => sanitize($rule['operator']),
                        'value' => sanitize($rule['value'] ?? '')
                    ];
                }
            }
        }

        // If category filter is active, prepend it as a rule (unless already set)
        $effectiveRules = $rules;
        if ($categoryFilter !== '') {
            // Map category shorthand → SQL field+value
            $catRuleMap = [
                'subscriber'      => ['field' => 'category',   'operator' => 'equals',   'value' => 'subscriber'],
                'active_user'     => ['field' => 'category',   'operator' => 'equals',   'value' => 'active_user'],
                'interested_user' => ['field' => 'category',   'operator' => 'equals',   'value' => 'interested_user'],
                'exclusive_user'  => ['field' => 'category',   'operator' => 'equals',   'value' => 'exclusive_user'],
                'suspended'       => ['field' => 'status',     'operator' => 'equals',   'value' => 'suspended'],
                'banned'          => ['field' => 'status',     'operator' => 'equals',   'value' => 'banned'],
                'today'           => ['field' => 'created_at', 'operator' => 'contains', 'value' => date('Y-m-d')],
            ];
            if (isset($catRuleMap[$categoryFilter])) {
                array_unshift($effectiveRules, $catRuleMap[$categoryFilter]);
            }
        }

        $totalSubscribers = $this->subModel->getSegmentCount($effectiveRules);
        $subscribers = $this->subModel->getSegmentSubscribers($effectiveRules, $limit, $offset);
        $totalPages = ceil($totalSubscribers / $limit);

        $lists = $this->listModel->all();
        $segments = $this->segmentModel->all();

        $pageTitle = 'Subscribers - Marketing';
        $adminPage = 'marketing_subscribers';
        $contentView = VIEWS_PATH . '/admin/marketing/subscribers.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    /**
     * Trigger Database Sync
     */
    public function sync(): void {
        $stats = $this->subModel->syncExisting();
        setFlash('success', 'Subscriber sync completed! Synced ' . (int)($stats['total'] ?? 0) . ' email records.');
        redirect(url('admin/marketing/dashboard'));
    }

    /**
     * Return the recipients for an audience before a campaign or automation is sent.
     */
    public function audiencePreview(): void {
        try {
            $audience = trim((string)($_GET['audience'] ?? 'all'));
            // Support values saved by older campaign screens and values that
            // have been URL-encoded by a browser or proxy more than once.
            for ($i = 0; $i < 2; $i++) {
                $decoded = rawurldecode($audience);
                if ($decoded === $audience) break;
                $audience = $decoded;
            }
            $audience = strtolower(str_replace([' ', '-'], '_', $audience));
            if (str_starts_with($audience, 'category_')) {
                $audience = 'category:' . substr($audience, 9);
            }
            $audience = [
                '' => 'all',
                'active' => 'all',
                'all_active' => 'all',
                'all_active_subscribers' => 'all',
                'all_subscribers' => 'all',
                'emails' => 'all_emails',
                'all_email' => 'all_emails',
                'all_emails_total' => 'all_emails',
                'subscriber' => 'category:subscriber',
                'active_user' => 'category:active_user',
                'interested_user' => 'category:interested_user',
                'exclusive_user' => 'category:exclusive_user',
                'suspended' => 'category:suspended',
                'banned' => 'category:banned',
                'today' => 'category:today',
            ][$audience] ?? $audience;
            $targetId = (int)($_GET['target_id'] ?? 0);
            $allowedAudiences = [
                'all', 'all_emails', 'list', 'segment',
                'category:subscriber', 'category:active_user',
                'category:interested_user', 'category:exclusive_user',
                'category:suspended', 'category:banned', 'category:today',
            ];
            if (!in_array($audience, $allowedAudiences, true)) {
                jsonResponse(['success' => false, 'error' => 'Invalid target audience.'], 400);
            }

            // Include existing registered users and newsletter contacts in the preview.
            $subModel = new MarketingSubscriber();
            $subModel->syncExisting();
            $db = Database::getInstance();
            $subscribers = [];

            if ($audience === 'all') {
                $subscribers = $db->fetchAll("SELECT id, email, name, status, category FROM marketing_subscribers WHERE status = 'active' AND email LIKE '%@%.%'");
            } elseif ($audience === 'all_emails') {
                $subscribers = $db->fetchAll("SELECT id, email, name, status, category FROM marketing_subscribers WHERE email LIKE '%@%.%'");
            } elseif ($audience === 'list') {
                if ($targetId <= 0) jsonResponse(['success' => false, 'error' => 'Please select a mailing list first.'], 400);
                $subscribers = $db->fetchAll(
                    "SELECT ms.id, ms.email, ms.name, ms.status, ms.category FROM marketing_subscribers ms
                     JOIN marketing_list_subscriber mls ON ms.id = mls.subscriber_id
                     WHERE mls.list_id = ? AND ms.status = 'active' AND ms.email LIKE '%@%.%'",
                    [$targetId]
                );
            } elseif ($audience === 'segment') {
                if ($targetId <= 0) jsonResponse(['success' => false, 'error' => 'Please select a dynamic segment first.'], 400);
                $segment = $db->fetch('SELECT rules FROM marketing_segments WHERE id = ?', [$targetId]);
                if (!$segment) jsonResponse(['success' => false, 'error' => 'Selected segment was not found.'], 404);
                $subscribers = $subModel->getSegmentSubscribers(json_decode($segment['rules'], true) ?: [], 1000, 0);
            } elseif ($audience === 'category:today') {
                $subscribers = $db->fetchAll("SELECT id, email, name, status, category FROM marketing_subscribers WHERE DATE(created_at) = CURDATE() AND email LIKE '%@%.%'");
            } elseif ($audience === 'category:suspended' || $audience === 'category:banned') {
                $status = substr($audience, 9);
                $subscribers = $db->fetchAll("SELECT id, email, name, status, category FROM marketing_subscribers WHERE status = ? AND email LIKE '%@%.%'", [$status]);
            } else {
                $category = substr($audience, 9);
                $subscribers = $db->fetchAll("SELECT id, email, name, status, category FROM marketing_subscribers WHERE category = ? AND status = 'active' AND email LIKE '%@%.%'", [$category]);
            }

            $recipients = [];
            foreach ($subscribers as $subscriber) {
                $email = trim((string)($subscriber['email'] ?? ''));
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;
                $recipients[$email] = [
                    'name' => (string)($subscriber['name'] ?? ''),
                    'email' => $email,
                    'status' => (string)($subscriber['status'] ?? ''),
                    'category' => (string)($subscriber['category'] ?? ''),
                ];
            }

            jsonResponse([
                'success' => true,
                'total' => count($recipients),
                'recipients' => array_values($recipients),
            ]);
        } catch (Throwable $e) {
            error_log('Marketing audience preview failed: ' . $e->getMessage());
            jsonResponse(['success' => false, 'error' => 'Unable to load the target email list.'], 500);
        }
    }

    /**
     * Create List Action
     */
    public function listStore(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/marketing/dashboard'));
        }

        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');

        if (empty($name)) {
            setFlash('danger', 'List name is required.');
        } else {
            $this->listModel->create($name, $desc);
            setFlash('success', 'Email List created successfully.');
        }
        redirect(url('admin/marketing/subscribers'));
    }

    /**
     * Delete List Action
     */
    public function listDelete(string $id): void {
        $this->listModel->delete((int)$id);
        setFlash('success', 'Email List deleted successfully.');
        redirect(url('admin/marketing/subscribers'));
    }

    /**
     * Create/Store Segment
     */
    public function segmentStore(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/marketing/subscribers'));
        }

        $name = trim($_POST['name'] ?? '');
        $rulesInput = $_POST['rules'] ?? [];
        $rules = [];

        foreach ($rulesInput as $r) {
            if (!empty($r['field'])) {
                $rules[] = [
                    'field' => sanitize($r['field']),
                    'operator' => sanitize($r['operator']),
                    'value' => sanitize($r['value'] ?? '')
                ];
            }
        }

        if (empty($name)) {
            setFlash('danger', 'Segment name is required.');
        } elseif (empty($rules)) {
            setFlash('danger', 'At least one filter rule is required.');
        } else {
            $this->segmentModel->create($name, $rules);
            setFlash('success', 'Dynamic Segment created successfully.');
        }
        redirect(url('admin/marketing/subscribers'));
    }

    /**
     * Delete Segment
     */
    public function segmentDelete(string $id): void {
        $this->segmentModel->delete((int)$id);
        setFlash('success', 'Dynamic Segment deleted successfully.');
        redirect(url('admin/marketing/subscribers'));
    }

    /**
     * List all SMTP profiles
     */
    public function smtps(): void {
        $db = Database::getInstance();
        $smtps = $db->fetchAll("SELECT * FROM marketing_smtps ORDER BY name ASC");

        $pageTitle = 'SMTP Accounts - Marketing';
        $adminPage = 'marketing_smtps';
        $contentView = VIEWS_PATH . '/admin/marketing/smtps.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    /**
     * Store new SMTP Profile
     */
    public function smtpStore(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/marketing/smtps'));
        }

        $db = Database::getInstance();
        
        $name = trim($_POST['name'] ?? '');
        $provider = trim($_POST['provider'] ?? 'custom');
        $host = trim($_POST['host'] ?? '');
        $port = (int)($_POST['port'] ?? 587);
        $user = trim($_POST['username'] ?? '');
        $pass = trim($_POST['password'] ?? '');
        $encryption = trim($_POST['encryption'] ?? 'tls');
        $fromEmail = trim($_POST['from_email'] ?? '');
        $fromName = trim($_POST['from_name'] ?? '');
        $daily = (int)($_POST['daily_limit'] ?? 0);
        $hourly = (int)($_POST['hourly_limit'] ?? 0);
        $priority = (int)($_POST['priority'] ?? 1);
        $status = trim($_POST['status'] ?? 'active');

        if (empty($name) || empty($host) || empty($fromEmail)) {
            setFlash('danger', 'Name, Host, and From Email are required fields.');
        } else {
            $db->insert('marketing_smtps', [
                'name' => $name,
                'provider' => $provider,
                'host' => $host,
                'port' => $port,
                'username' => $user,
                'password' => $pass,
                'encryption' => $encryption,
                'from_email' => $fromEmail,
                'from_name' => $fromName,
                'daily_limit' => $daily,
                'hourly_limit' => $hourly,
                'priority' => $priority,
                'status' => $status
            ]);
            setFlash('success', 'SMTP Account added successfully.');
        }
        redirect(url('admin/marketing/smtps'));
    }

    /**
     * Return SMTP profile as JSON for edit modal
     */
    public function smtpEdit(string $id): void {
        ob_start();
        $db = Database::getInstance();
        $smtp = $db->fetch("SELECT id, name, provider, host, port, encryption, username, from_email, from_name, daily_limit, hourly_limit, priority, status FROM marketing_smtps WHERE id = ?", [(int)$id]);
        ob_end_clean();
        header('Content-Type: application/json');
        if ($smtp) {
            echo json_encode(['success' => true, 'smtp' => $smtp]);
        } else {
            echo json_encode(['success' => false, 'error' => 'SMTP profile not found.']);
        }
        exit;
    }

    /**
     * Update SMTP profile credentials
     */
    public function smtpUpdate(string $id): void {
        ob_start();
        $db = Database::getInstance();
        try {
            $data = [
                'name'        => trim($_POST['name'] ?? ''),
                'host'        => trim($_POST['host'] ?? ''),
                'port'        => (int)($_POST['port'] ?? 587),
                'username'    => trim($_POST['username'] ?? ''),
                'encryption'  => trim($_POST['encryption'] ?? 'tls'),
                'from_email'  => trim($_POST['from_email'] ?? ''),
                'from_name'   => trim($_POST['from_name'] ?? ''),
                'daily_limit' => (int)($_POST['daily_limit'] ?? 0),
                'hourly_limit'=> (int)($_POST['hourly_limit'] ?? 0),
                'priority'    => (int)($_POST['priority'] ?? 1),
                'status'      => trim($_POST['status'] ?? 'active'),
            ];
            // Only update password if a new one was provided
            $newPass = trim($_POST['password'] ?? '');
            if (!empty($newPass)) {
                $data['password'] = $newPass;
            }
            if (empty($data['name']) || empty($data['host']) || empty($data['from_email'])) {
                $res = ['success' => false, 'error' => 'Name, Host, and From Email are required.'];
            } else {
                $db->update('marketing_smtps', $data, 'id = ?', [(int)$id]);
                $res = ['success' => true, 'message' => 'SMTP profile updated successfully.'];
            }
        } catch (Throwable $e) {
            $res = ['success' => false, 'error' => $e->getMessage()];
        }
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($res);
        exit;
    }

    /**
     * Delete SMTP Profile
     */
    public function smtpDelete(string $id): void {
        $db = Database::getInstance();
        $db->delete('marketing_smtps', 'id = ?', [(int)$id]);
        setFlash('success', 'SMTP Account deleted successfully.');
        redirect(url('admin/marketing/smtps'));
    }

    /**
     * Run SMTP Socket Connection Test — returns JSON
     */
    public function smtpTest(string $id): void {
        ob_start();
        try {
            $mailer = new MarketingMailer();
            $res = $mailer->testConnection((int)$id);
        } catch (Throwable $e) {
            $res = ['success' => false, 'error' => $e->getMessage()];
        }
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($res);
        exit;
    }

    /**
     * Send a live test email through a specific SMTP profile — returns JSON
     */
    public function smtpSendTest(string $id): void {
        ob_start();
        try {
            $toEmail = trim($_POST['test_email'] ?? '');
            if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                $res = ['success' => false, 'error' => 'Please provide a valid recipient email address.'];
            } else {
                $mailer = new MarketingMailer();
                $res = $mailer->sendTestEmail((int)$id, $toEmail);
            }
        } catch (Throwable $e) {
            $res = ['success' => false, 'error' => 'Server exception: ' . $e->getMessage()];
        }
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($res);
        exit;
    }

    /**
     * Manually trigger queue processing — returns JSON
     */
    public function processQueue(): void {
        ob_start();
        try {
            require_once INCLUDES_PATH . '/QueueProcessor.php';
            $processor = new QueueProcessor();
            $result = $processor->process(30);
            $res = ['success' => true, 'result' => $result];
        } catch (Throwable $e) {
            $res = ['success' => false, 'error' => $e->getMessage()];
        }
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($res);
        exit;
    }

    // ====================================================
    // TEMPLATES & BUILDER (MODULE 3)
    // ====================================================

    public function templates(): void {
        $db = Database::getInstance();
        $currentCategory = trim($_GET['category'] ?? '');
        
        if (!empty($currentCategory)) {
            $templates = $db->fetchAll("SELECT * FROM marketing_templates WHERE category = ? ORDER BY name ASC", [$currentCategory]);
        } else {
            $templates = $db->fetchAll("SELECT * FROM marketing_templates ORDER BY name ASC");
        }

        $pageTitle = 'Email Templates - Marketing';
        $adminPage = 'marketing_templates';
        $contentView = VIEWS_PATH . '/admin/marketing/templates.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function templateStore(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/marketing/templates'));
        }

        $db = Database::getInstance();
        $name = trim($_POST['name'] ?? '');
        $subject = trim($_POST['subject'] ?? '');

        if (empty($name)) {
            setFlash('danger', 'Template name is required.');
            redirect(url('admin/marketing/templates'));
        }

        // Initialize default empty builder blocks
        $defaultJson = json_encode([
            ['type' => 'header', 'content' => '<h2>DigiWS Newsletter</h2>', 'styles' => ['textAlign' => 'center', 'textColor' => '#1E40AF']],
            ['type' => 'text', 'content' => '<p>Hello {{customer_name}}, welcome to our newsletter!</p>', 'styles' => ['textAlign' => 'center']],
            ['type' => 'button', 'content' => 'Explore Marketplace', 'url' => url(''), 'styles' => ['btnBg' => '#6366f1', 'btnText' => '#ffffff', 'btnAlign' => 'center', 'btnRadius' => 8]],
            ['type' => 'footer', 'content' => '<p>&copy; {{current_year}} DigiWS. Premium Digital Products. | <a href="{{unsubscribe}}">Unsubscribe</a></p>', 'styles' => ['textColor' => '#64748B', 'textAlign' => 'center']]
        ]);
        $defaultHtml = '<div style="font-family:sans-serif; max-width:600px; margin:0 auto; padding:20px; border:1px solid #e2e8f0; border-radius:12px;">'
                     . '<h2 style="color:#2563eb;">DigiWS Marketplace</h2>'
                     . '<p>Hello {{customer_name}}, welcome to our newsletter!</p>'
                     . '<div style="text-align:center; margin:24px 0;"><a href="'.url('').'" style="background:#2563eb; color:#ffffff; padding:12px 24px; text-decoration:none; border-radius:8px; font-weight:bold;">Explore Marketplace</a></div>'
                     . '<hr style="border:none; border-top:1px solid #e2e8f0; margin:24px 0;">'
                     . '<p style="font-size:0.8rem; color:#64748b;">You are receiving this email because you subscribed to DigiWS. <a href="{{unsubscribe}}">Unsubscribe</a></p>'
                     . '</div>';

        $id = $db->insert('marketing_templates', [
            'name' => $name,
            'subject' => $subject,
            'content_json' => $defaultJson,
            'content_html' => $defaultHtml
        ]);

        setFlash('success', 'Template created! Opening visual editor.');
        redirect(url('admin/marketing/templates/builder/' . $id));
    }

    public function templateEdit(string $id): void {
        $db = Database::getInstance();
        $template = $db->fetch("SELECT * FROM marketing_templates WHERE id = ?", [(int)$id]);

        $pageTitle = 'Edit Template Info - Marketing';
        $adminPage = 'marketing_templates';
        $contentView = VIEWS_PATH . '/admin/marketing/template_edit.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function templateUpdate(string $id): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/marketing/templates'));
        }

        $db = Database::getInstance();
        $name = trim($_POST['name'] ?? '');
        $subject = trim($_POST['subject'] ?? '');

        if (empty($name)) {
            setFlash('danger', 'Template name is required.');
            redirect(url('admin/marketing/templates/edit/' . $id));
        }

        $db->update('marketing_templates', [
            'name' => $name,
            'subject' => $subject
        ], 'id = ?', [(int)$id]);

        setFlash('success', 'Template info updated.');
        redirect(url('admin/marketing/templates'));
    }

    public function templateDelete(string $id): void {
        $db = Database::getInstance();
        $db->delete('marketing_templates', 'id = ?', [(int)$id]);
        setFlash('success', 'Template deleted successfully.');
        redirect(url('admin/marketing/templates'));
    }

    public function builder(string $id): void {
        $db = Database::getInstance();
        $template = $db->fetch("SELECT * FROM marketing_templates WHERE id = ?", [(int)$id]);
        if (!$template) {
            setFlash('danger', 'Template not found.');
            redirect(url('admin/marketing/templates'));
        }

        // Fetch products, coupons, and blogs to allow dragging/picking them
        $products = $db->fetchAll("SELECT id, title, slug, mrp, sale_price, thumbnail FROM products WHERE status = 'active' ORDER BY id DESC LIMIT 20");
        $coupons = $db->fetchAll("SELECT id, code, discount_value, discount_type, expires_at FROM coupons WHERE status = 'active' ORDER BY id DESC LIMIT 20");
        $blogs = $db->fetchAll("SELECT id, title, slug, image FROM posts WHERE status = 'published' ORDER BY id DESC LIMIT 20");

        $pageTitle = 'Visual Drag & Drop Email Builder';
        $adminPage = 'marketing_templates';
        $contentView = VIEWS_PATH . '/admin/marketing/builder.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function saveBuilder(string $id): void {
        try {
            $rawBody = file_get_contents('php://input');
            $rawInput = json_decode($rawBody, true);
            if (!is_array($rawInput) || json_last_error() !== JSON_ERROR_NONE) {
                jsonResponse(['success' => false, 'error' => 'Invalid JSON body.'], 400);
            }

            // Support base64 decoded data to bypass server WAF/mod_security.
            if (isset($rawInput['data'])) {
                if (!is_string($rawInput['data'])) {
                    jsonResponse(['success' => false, 'error' => 'Invalid encoded payload.'], 400);
                }
                $decodedStr = base64_decode($rawInput['data'], true);
                $input = $decodedStr === false ? null : json_decode($decodedStr, true);
            } else {
                $input = $rawInput;
            }

            if (!is_array($input) || json_last_error() !== JSON_ERROR_NONE) {
                jsonResponse(['success' => false, 'error' => 'Invalid payload data.'], 400);
            }

            $templateId = (int)$id;
            $db = Database::getInstance();
            if (!$db->exists('marketing_templates', 'id = ?', [$templateId])) {
                jsonResponse(['success' => false, 'error' => 'Template not found.'], 404);
            }

            $contentJson = json_encode($input['json'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $globalSettings = isset($input['global_settings'])
                ? json_encode($input['global_settings'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : null;
            if ($contentJson === false || (isset($input['global_settings']) && $globalSettings === false)) {
                jsonResponse(['success' => false, 'error' => 'Unable to encode template data.'], 422);
            }

            $updateData = [
                'content_json' => $contentJson,
                'content_html' => is_string($input['html'] ?? null) ? $input['html'] : ''
            ];
            // Older installations may not yet have this optional builder column.
            // Do not block saving the template's content in that case.
            $hasGlobalSettingsColumn = $db->fetch("SHOW COLUMNS FROM marketing_templates LIKE 'global_settings'") !== null;
            if ($globalSettings !== null && $hasGlobalSettingsColumn) {
                $updateData['global_settings'] = $globalSettings;
            }
            $db->update('marketing_templates', $updateData, 'id = ?', [$templateId]);

            jsonResponse(['success' => true]);
        } catch (Throwable $e) {
            error_log('Template builder save failed: ' . $e->getMessage());
            jsonResponse(['success' => false, 'error' => 'Unable to save the template. Please try again.'], 500);
        }
    }

    // ====================================================
    // CAMPAIGNS & A/B TESTING (MODULE 4)
    // ====================================================

    public function campaigns(): void {
        $db = Database::getInstance();
        $campaigns = $db->fetchAll(
            "SELECT mc.*, mt.name as template_name 
             FROM marketing_campaigns mc 
             LEFT JOIN marketing_templates mt ON mc.template_id = mt.id 
             ORDER BY mc.created_at DESC"
        );

        $templates = $db->fetchAll("SELECT id, name FROM marketing_templates ORDER BY name ASC");
        $lists = $db->fetchAll(
            "SELECT ml.*, COUNT(mls.subscriber_id) as subscriber_count 
             FROM marketing_lists ml 
             LEFT JOIN marketing_list_subscriber mls ON ml.id = mls.list_id 
             GROUP BY ml.id"
        );
        $segments = $db->fetchAll("SELECT id, name FROM marketing_segments ORDER BY name ASC");
        $smtps = $db->fetchAll("SELECT id, name FROM marketing_smtps WHERE status = 'active' ORDER BY name ASC");

        $pageTitle = 'Campaigns - Marketing';
        $adminPage = 'marketing_campaigns';
        $contentView = VIEWS_PATH . '/admin/marketing/campaigns.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function campaignStore(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/marketing/campaigns'));
        }

        $db = Database::getInstance();
        $name = trim($_POST['name'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $fromName = trim($_POST['from_name'] ?? '');
        $fromEmail = trim($_POST['from_email'] ?? '');
        $templateId = (int)($_POST['template_id'] ?? 0);
        $targetType = trim($_POST['target_type'] ?? 'all');
        $targetId = !empty($_POST['target_id']) ? (int)$_POST['target_id'] : null;
        $smtpId = !empty($_POST['smtp_id']) ? (int)$_POST['smtp_id'] : null;
        $scheduledAt = trim($_POST['scheduled_at'] ?? '');
        if ($scheduledAt !== '') {
            $scheduledTimestamp = strtotime($scheduledAt);
            if ($scheduledTimestamp === false) {
                $scheduledAt = '';
            } else {
                $scheduledAt = date('Y-m-d H:i:s', $scheduledTimestamp);
            }
        }

        // Validate allowed target_type values
        $allowedTargetTypes = [
            'all', 'all_emails', 'list', 'segment',
            'category:subscriber', 'category:active_user',
            'category:interested_user', 'category:exclusive_user',
            'category:suspended', 'category:banned', 'category:today',
        ];
        if (!in_array($targetType, $allowedTargetTypes)) {
            $targetType = 'all';
        }

        if (empty($name) || empty($subject) || empty($fromName) || empty($fromEmail) || $templateId <= 0) {
            setFlash('danger', 'Please fill in all required fields.');
        } else {
            $db->insert('marketing_campaigns', [
                'name' => $name,
                'subject' => $subject,
                'from_name' => $fromName,
                'from_email' => $fromEmail,
                'template_id' => $templateId,
                'target_type' => $targetType,
                'target_id' => $targetId,
                'smtp_id' => $smtpId,
                'scheduled_at' => $scheduledAt ?: null,
                'status' => 'draft'
            ]);
            setFlash('success', 'Mailing campaign created successfully as Draft.');
        }
        redirect(url('admin/marketing/campaigns'));
    }

    public function campaignDelete(string $id): void {
        $db = Database::getInstance();
        $db->delete('marketing_campaigns', 'id = ?', [(int)$id]);
        setFlash('success', 'Campaign deleted successfully.');
        redirect(url('admin/marketing/campaigns'));
    }

    /**
     * Dispatch campaign queue builder
     */
    public function campaignSend(string $id): void {
        $db = Database::getInstance();
        $campaign = $db->fetch("SELECT * FROM marketing_campaigns WHERE id = ?", [(int)$id]);

        if (!$campaign) {
            setFlash('danger', 'Campaign not found.');
            redirect(url('admin/marketing/campaigns'));
        }

        // Normalize target_type for backwards compatibility with older campaign target types
        $targetType = $campaign['target_type'];
        $targetType = [
            '' => 'all',
            'active' => 'all',
            'all_active' => 'all',
            'all_active_subscribers' => 'all',
            'all_subscribers' => 'all',
            'emails' => 'all_emails',
            'all_email' => 'all_emails',
            'all_emails_total' => 'all_emails',
            'subscriber' => 'category:subscriber',
            'active_user' => 'category:active_user',
            'interested_user' => 'category:interested_user',
            'exclusive_user' => 'category:exclusive_user',
            'suspended' => 'category:suspended',
            'banned' => 'category:banned',
            'today' => 'category:today',
        ][$targetType] ?? $targetType;
        $campaign['target_type'] = $targetType;

        // Allow resending campaigns that are not in draft by clearing old queue entries
        $resendableStatuses = ['scheduled', 'sending', 'completed', 'failed'];
        if (in_array($campaign['status'], $resendableStatuses)) {
            // Delete old pending/failed queue entries for this campaign before re-queuing
            $db->query(
                "DELETE FROM marketing_queue WHERE campaign_id = ? AND status IN ('pending', 'failed')",
                [(int)$id]
            );
        }

        $scheduledAt = !empty($campaign['scheduled_at'])
            ? $campaign['scheduled_at']
            : date('Y-m-d H:i:s');

        // Retrieve target subscribers
        $subscribers = [];
        $subModel = new MarketingSubscriber();
        // Keep the marketing audience in sync with registered users and legacy
        // newsletter contacts before resolving a campaign target.
        try {
            $subModel->syncExisting();
        } catch (Throwable $e) {
            error_log('Campaign audience sync failed: ' . $e->getMessage());
        }

        if ($campaign['target_type'] === 'all') {
            // All active subscribers
            $subscribers = $db->fetchAll("SELECT id, email, name FROM marketing_subscribers WHERE status = 'active' AND email LIKE '%@%.%'");

        } elseif ($campaign['target_type'] === 'all_emails') {
            // Every email in the system regardless of status
            $subscribers = $db->fetchAll("SELECT id, email, name FROM marketing_subscribers WHERE email LIKE '%@%.%'");

        } elseif (str_starts_with($campaign['target_type'], 'category:')) {
            // Email category targeting
            $cat = substr($campaign['target_type'], 9); // strip 'category:'
            if ($cat === 'today') {
                $subscribers = $db->fetchAll(
                    "SELECT id, email, name FROM marketing_subscribers WHERE DATE(created_at) = CURDATE() AND email LIKE '%@%.%'"
                );
            } elseif ($cat === 'suspended') {
                $subscribers = $db->fetchAll(
                    "SELECT id, email, name FROM marketing_subscribers WHERE status = 'suspended' AND email LIKE '%@%.%'"
                );
            } elseif ($cat === 'banned') {
                $subscribers = $db->fetchAll(
                    "SELECT id, email, name FROM marketing_subscribers WHERE status = 'banned' AND email LIKE '%@%.%'"
                );
            } else {
                // subscriber, active_user, interested_user, exclusive_user
                $subscribers = $db->fetchAll(
                    "SELECT id, email, name FROM marketing_subscribers WHERE category = ? AND status = 'active' AND email LIKE '%@%.%'",
                    [$cat]
                );
            }

        } elseif ($campaign['target_type'] === 'list') {
            $subscribers = $db->fetchAll(
                "SELECT ms.id, ms.email, ms.name FROM marketing_subscribers ms 
                 JOIN marketing_list_subscriber mls ON ms.id = mls.subscriber_id 
                 WHERE mls.list_id = ? AND ms.status = 'active'",
                [$campaign['target_id']]
            );
        } elseif ($campaign['target_type'] === 'segment') {
            $segment = $db->fetch("SELECT rules FROM marketing_segments WHERE id = ?", [$campaign['target_id']]);
            if ($segment) {
                $rules = json_decode($segment['rules'], true);
                $subscribers = $subModel->getSegmentSubscribers($rules, 1000000, 0); // Max safe batch
            }
        }

        if (empty($subscribers)) {
            setFlash('danger', 'Selected target audience is empty. No emails queued.');
            redirect(url('admin/marketing/campaigns'));
        }

        // Fetch template code
        $template = $db->fetch("SELECT content_html FROM marketing_templates WHERE id = ?", [$campaign['template_id']]);
        $htmlTemplate = $template['content_html'] ?? '';

        // Add recipients to queue
        $db->beginTransaction();
        try {
            foreach ($subscribers as $sub) {
                $email = trim((string)($sub['email'] ?? ''));
                // Do not queue malformed addresses, unsubscribed contacts, or
                // an already-created campaign-recipient pair.
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                $alreadyQueued = (int)$db->fetchColumn(
                    "SELECT COUNT(*) FROM marketing_queue WHERE campaign_id = ? AND subscriber_id = ?",
                    [$campaign['id'], $sub['id']]
                );
                if ($alreadyQueued > 0) {
                    continue;
                }

                // Personalize variables
                $body = str_replace(
                    ['{{customer_name}}', '{{email}}', '{{unsubscribe}}'],
                    [
                        htmlspecialchars($sub['name'] ?? 'Mailing List Member'),
                        htmlspecialchars($email),
                        url('unsubscribe?email=' . urlencode($email))
                    ],
                    $htmlTemplate
                );

                $db->insert('marketing_queue', [
                    'campaign_id' => $campaign['id'],
                    'subscriber_id' => $sub['id'],
                    'email' => $email,
                    'subject' => $campaign['subject'],
                    'body_html' => $body,
                    'smtp_id' => $campaign['smtp_id'],
                    'status' => 'pending',
                    'priority' => 10,
                    'scheduled_at' => $scheduledAt
                ]);
            }

            // Update campaign status to scheduled
            $db->update('marketing_campaigns', [
                'status' => 'scheduled',
                'total_subscribers' => (int)$db->fetchColumn(
                    "SELECT COUNT(*) FROM marketing_queue WHERE campaign_id = ?",
                    [$campaign['id']]
                ),
                'scheduled_at' => $scheduledAt
            ], 'id = ?', [$campaign['id']]);

            $db->commit();
            setFlash('success', 'Success! ' . count($subscribers) . ' emails added to transmission queue.');

            // Call queue worker in background — only if shell functions are available
            if (class_exists('QueueProcessor')) {
                try {
                    $disabledFunctions = array_map('trim', explode(',', ini_get('disable_functions')));
                    $canRunAsync = !in_array('popen', $disabledFunctions) && !in_array('pclose', $disabledFunctions)
                                  && function_exists('popen') && function_exists('pclose');
                    if ($canRunAsync) {
                        $worker = new QueueProcessor();
                        $worker->processBatchAsync();
                    }
                } catch(Throwable $ex) {}
            }

        } catch (Throwable $e) {
            $db->rollback();
            setFlash('danger', 'Error preparing mailing queue: ' . $e->getMessage());
        }

        redirect(url('admin/marketing/campaigns'));
    }

    // ──────────────────────────────────────────────────────────────
    // CAMPAIGN: Re-queue all failed/stuck emails (retry deliveries)
    // Also handles the case where the queue is completely empty
    // (campaign was never successfully dispatched before).
    // ──────────────────────────────────────────────────────────────
    public function campaignResendFailed(string $id): void {
        $db = Database::getInstance();
        $campaign = $db->fetch("SELECT * FROM marketing_campaigns WHERE id = ?", [(int)$id]);

        if (!$campaign) {
            setFlash('danger', 'Campaign not found.');
            redirect(url('admin/marketing/campaigns'));
        }

        // Normalize target_type for backwards compatibility with older campaign target types
        $targetType = $campaign['target_type'];
        $targetType = [
            '' => 'all',
            'active' => 'all',
            'all_active' => 'all',
            'all_active_subscribers' => 'all',
            'all_subscribers' => 'all',
            'emails' => 'all_emails',
            'all_email' => 'all_emails',
            'all_emails_total' => 'all_emails',
            'subscriber' => 'category:subscriber',
            'active_user' => 'category:active_user',
            'interested_user' => 'category:interested_user',
            'exclusive_user' => 'category:exclusive_user',
            'suspended' => 'category:suspended',
            'banned' => 'category:banned',
            'today' => 'category:today',
        ][$targetType] ?? $targetType;
        $campaign['target_type'] = $targetType;

        // Count how many queue entries exist for this campaign (any status)
        $totalInQueue = (int)$db->fetchColumn(
            "SELECT COUNT(*) FROM marketing_queue WHERE campaign_id = ?", [(int)$id]
        );

        if ($totalInQueue === 0) {
            // ── SCENARIO 1: Queue is completely empty ──────────────────────
            // The campaign was never successfully queued (e.g. original send
            // always failed). Build the queue fresh from the campaign settings.
            $subscribers = [];
            $subModel = new MarketingSubscriber();

            if ($campaign['target_type'] === 'all') {
                $subscribers = $db->fetchAll(
                    "SELECT id, email, name FROM marketing_subscribers WHERE status = 'active' AND email LIKE '%@%.%'"
                );
            } elseif ($campaign['target_type'] === 'all_emails') {
                $subscribers = $db->fetchAll(
                    "SELECT id, email, name FROM marketing_subscribers WHERE email LIKE '%@%.%'"
                );
            } elseif (str_starts_with($campaign['target_type'], 'category:')) {
                $cat = substr($campaign['target_type'], 9);
                if ($cat === 'today') {
                    $subscribers = $db->fetchAll(
                        "SELECT id, email, name FROM marketing_subscribers WHERE DATE(created_at) = CURDATE() AND email LIKE '%@%.%'"
                    );
                } elseif ($cat === 'suspended') {
                    $subscribers = $db->fetchAll(
                        "SELECT id, email, name FROM marketing_subscribers WHERE status = 'suspended' AND email LIKE '%@%.%'"
                    );
                } elseif ($cat === 'banned') {
                    $subscribers = $db->fetchAll(
                        "SELECT id, email, name FROM marketing_subscribers WHERE status = 'banned' AND email LIKE '%@%.%'"
                    );
                } else {
                    $subscribers = $db->fetchAll(
                        "SELECT id, email, name FROM marketing_subscribers WHERE category = ? AND status = 'active' AND email LIKE '%@%.%'",
                        [$cat]
                    );
                }
            } elseif ($campaign['target_type'] === 'list') {
                $subscribers = $db->fetchAll(
                    "SELECT ms.id, ms.email, ms.name FROM marketing_subscribers ms 
                     JOIN marketing_list_subscriber mls ON ms.id = mls.subscriber_id 
                     WHERE mls.list_id = ? AND ms.status = 'active'",
                    [$campaign['target_id']]
                );
            } elseif ($campaign['target_type'] === 'segment') {
                $segment = $db->fetch(
                    "SELECT rules FROM marketing_segments WHERE id = ?", [$campaign['target_id']]
                );
                if ($segment) {
                    $rules = json_decode($segment['rules'], true);
                    $subscribers = $subModel->getSegmentSubscribers($rules, 1000000, 0);
                }
            }

            if (empty($subscribers)) {
                setFlash('danger', 'No active subscribers found for this campaign\'s target audience.');
                redirect(url('admin/marketing/campaigns'));
            }

            // Fetch template HTML
            $template    = $db->fetch(
                "SELECT content_html FROM marketing_templates WHERE id = ?", [$campaign['template_id']]
            );
            $htmlTemplate = $template['content_html'] ?? '';

            // Build queue entries inside a transaction
            $db->beginTransaction();
            try {
                foreach ($subscribers as $sub) {
                    $body = str_replace(
                        ['{{customer_name}}', '{{email}}', '{{unsubscribe}}'],
                        [
                            htmlspecialchars($sub['name'] ?? 'Mailing List Member'),
                            htmlspecialchars($sub['email']),
                            url('unsubscribe?email=' . urlencode($sub['email']))
                        ],
                        $htmlTemplate
                    );
                    $db->insert('marketing_queue', [
                        'campaign_id'   => $campaign['id'],
                        'subscriber_id' => $sub['id'],
                        'email'         => $sub['email'],
                        'subject'       => $campaign['subject'],
                        'body_html'     => $body,
                        'smtp_id'       => $campaign['smtp_id'],
                        'status'        => 'pending',
                        'priority'      => 10
                    ]);
                }
                $db->update('marketing_campaigns', [
                    'status'            => 'scheduled',
                    'total_subscribers' => count($subscribers),
                    'scheduled_at'      => date('Y-m-d H:i:s')
                ], 'id = ?', [$campaign['id']]);
                $db->commit();
                $count = count($subscribers);
            } catch (Throwable $e) {
                $db->rollback();
                setFlash('danger', 'Error building queue: ' . $e->getMessage());
                redirect(url('admin/marketing/campaigns'));
            }

        } else {
            // ── SCENARIO 2 / 3: Queue entries exist ────────────────────────
            // Reset only unsent (failed/pending/stuck-sending) entries
            $affected = $db->query(
                "UPDATE marketing_queue 
                 SET status = 'pending', attempts = 0, error_message = NULL 
                 WHERE campaign_id = ? AND status IN ('failed', 'pending', 'sending')",
                [(int)$id]
            );
            $count = $affected->rowCount();

            if ($count === 0) {
                // ── SCENARIO 3: All entries already sent ──────────────────
                $sentCount = (int)$db->fetchColumn(
                    "SELECT COUNT(*) FROM marketing_queue WHERE campaign_id = ? AND status = 'sent'",
                    [(int)$id]
                );
                setFlash('success', "All {$sentCount} email(s) for this campaign have already been delivered successfully.");
                redirect(url('admin/marketing/campaigns'));
            }

            $db->update('marketing_campaigns', ['status' => 'scheduled'], 'id = ?', [(int)$id]);
        }

        // ── Process the queue inline (no cron / background worker needed) ──
        try {
            require_once INCLUDES_PATH . '/QueueProcessor.php';
            $processor = new QueueProcessor();
            $result    = $processor->process(50);
            $sent      = $result['success'] ?? 0;
            $failed    = $result['failed']  ?? 0;
            setFlash('success', "Campaign processing complete: {$sent} sent, {$failed} failed out of {$count} email(s). Refresh to see updated stats.");
        } catch (Throwable $e) {
            setFlash('success', "{$count} email(s) queued. Processing error: " . $e->getMessage());
        }

        redirect(url('admin/marketing/campaigns'));
    }


    // ──────────────────────────────────────────────────────────────
    // CAMPAIGN: Resend entire campaign (reset all to pending)
    // ──────────────────────────────────────────────────────────────
    public function campaignResendAll(string $id): void {
        $db = Database::getInstance();
        $campaign = $db->fetch("SELECT * FROM marketing_campaigns WHERE id = ?", [(int)$id]);

        if (!$campaign) {
            setFlash('danger', 'Campaign not found.');
            redirect(url('admin/marketing/campaigns'));
        }

        $stmt = $db->query(
            "UPDATE marketing_queue SET status = 'pending', attempts = 0, error_message = NULL 
             WHERE campaign_id = ?",
            [(int)$id]
        );
        $count = $stmt->rowCount();

        if ($count > 0) {
            $db->update('marketing_campaigns', ['status' => 'scheduled'], 'id = ?', [(int)$id]);
            setFlash('success', "Full resend queued! All {$count} email(s) will be retransmitted.");
        } else {
            setFlash('info', 'No queue entries found for this campaign. Please send it first.');
        }

        redirect(url('admin/marketing/campaigns'));
    }

    // ──────────────────────────────────────────────────────────────
    // CAMPAIGN: Get delivery stats for a campaign (JSON for modal)
    // ──────────────────────────────────────────────────────────────
    public function campaignStats(string $id): void {
        ob_start();
        $db = Database::getInstance();
        try {
            $campaign = $db->fetch("SELECT * FROM marketing_campaigns WHERE id = ?", [(int)$id]);
            if (!$campaign) {
                $res = ['success' => false, 'error' => 'Campaign not found.'];
            } else {
                $stats = $db->fetch(
                    "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status='sent' THEN 1 ELSE 0 END) as sent,
                        SUM(CASE WHEN status='failed' THEN 1 ELSE 0 END) as failed,
                        SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) as pending
                     FROM marketing_queue WHERE campaign_id = ?",
                    [(int)$id]
                );
                // Recent failures with reason
                $failures = $db->fetchAll(
                    "SELECT email, error_message, attempts, updated_at 
                     FROM marketing_queue WHERE campaign_id = ? AND status = 'failed' 
                     ORDER BY updated_at DESC LIMIT 50",
                    [(int)$id]
                );
                $res = [
                    'success'  => true,
                    'campaign' => $campaign,
                    'stats'    => $stats,
                    'failures' => $failures
                ];
            }
        } catch (Throwable $e) {
            $res = ['success' => false, 'error' => $e->getMessage()];
        }
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($res);
        exit;
    }

    // ──────────────────────────────────────────────────────────────
    // CAMPAIGN: Send campaign email to a SINGLE specific email address
    // ──────────────────────────────────────────────────────────────
    public function campaignSendOne(string $id): void {
        ob_start();
        $db = Database::getInstance();
        try {
            $toEmail  = trim($_POST['recipient_email'] ?? '');
            $toName   = trim($_POST['recipient_name']  ?? 'Valued Customer');

            if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                $res = ['success' => false, 'error' => 'Please enter a valid email address.'];
            } else {
                $campaign = $db->fetch("SELECT * FROM marketing_campaigns WHERE id = ?", [(int)$id]);
                if (!$campaign) {
                    $res = ['success' => false, 'error' => 'Campaign not found.'];
                } else {
                    // Fetch template HTML
                    $template = $db->fetch(
                        "SELECT content_html FROM marketing_templates WHERE id = ?",
                        [$campaign['template_id']]
                    );
                    $htmlTemplate = $template['content_html'] ?? '';

                    if (empty($htmlTemplate)) {
                        $res = ['success' => false, 'error' => 'Campaign template has no content.'];
                    } else {
                        // Personalize
                        $body = str_replace(
                            ['{{customer_name}}', '{{email}}', '{{unsubscribe}}', '{{site_url}}'],
                            [
                                htmlspecialchars($toName),
                                htmlspecialchars($toEmail),
                                url('unsubscribe?email=' . urlencode($toEmail)),
                                defined('APP_URL') ? APP_URL : ''
                            ],
                            $htmlTemplate
                        );

                        // Override from_name/from_email from campaign if set
                        $smtpId = $campaign['smtp_id'] ?: null;
                        $mailer = new MarketingMailer();
                        $result = $mailer->send($toEmail, $campaign['subject'], $body, $smtpId);

                        if ($result['success']) {
                            // Check if recipient is a subscriber
                            $sub = $db->fetch("SELECT id FROM marketing_subscribers WHERE email = ?", [strtolower(trim($toEmail))]);
                            $subId = $sub ? (int)$sub['id'] : null;

                            // Log to queue table for tracking
                            $db->insert('marketing_queue', [
                                'campaign_id'   => $campaign['id'],
                                'subscriber_id' => $subId,
                                'email'         => $toEmail,
                                'subject'       => $campaign['subject'],
                                'body_html'     => $body,
                                'smtp_id'       => $smtpId,
                                'status'        => 'sent',
                                'priority'      => 5,
                                'attempts'      => 1,
                            ]);
                            $db->query(
                                "UPDATE marketing_campaigns SET sent_count = sent_count + 1 WHERE id = ?",
                                [(int)$id]
                            );
                            $res = ['success' => true, 'message' => "Email sent successfully to <strong>{$toEmail}</strong>."];
                        } else {
                            $res = ['success' => false, 'error' => $result['error']];
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            $res = ['success' => false, 'error' => $e->getMessage()];
        }
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($res);
        exit;
    }

    // ====================================================
    // AUTOMATIONS (MODULE 5)
    // ====================================================

    public function automations(): void {
        $db = Database::getInstance();
        $automations = $db->fetchAll(
            "SELECT a.*, COALESCE(q.sent_count, 0) as sent_count 
             FROM marketing_automations a
             LEFT JOIN (
                 SELECT automation_id, COUNT(*) as sent_count 
                 FROM marketing_queue 
                 WHERE status = 'sent' 
                 GROUP BY automation_id
             ) q ON a.id = q.automation_id
             ORDER BY a.name ASC"
        );
        $templates = $db->fetchAll("SELECT id, name FROM marketing_templates ORDER BY name ASC");
        $smtps = $db->fetchAll("SELECT id, name FROM marketing_smtps WHERE status = 'active' ORDER BY name ASC");

        $pageTitle = 'Automations - Marketing';
        $adminPage = 'marketing_automations';
        $contentView = VIEWS_PATH . '/admin/marketing/automations.php';
        include VIEWS_PATH . '/admin/layout.php';
    }

    public function automationStore(): void {
        if (!verifyCsrf()) {
            setFlash('danger', 'Invalid request.');
            redirect(url('admin/marketing/automations'));
        }

        $db = Database::getInstance();
        $name = trim($_POST['name'] ?? '');
        $trigger = trim($_POST['trigger_event'] ?? '');
        $templateId = (int)($_POST['template_id'] ?? 0);
        $status = trim($_POST['status'] ?? 'inactive');
        $targetAudience = trim($_POST['target_audience'] ?? 'all');
        $smtpId = !empty($_POST['smtp_id']) ? (int)$_POST['smtp_id'] : null;
        $allowedTriggers = ['user_registration', 'user_signup', 'purchase_completed', 'order_placed', 'order_delivered', 'product_download', 'password_reset', 'subscription_expiry', 'new_product_published', 'custom_event', 'manual_broadcast'];
        if (!in_array($trigger, $allowedTriggers, true)) $trigger = '';

        // Validate target_audience
        $allowedAudiences = [
            'all', 'all_emails',
            'category:subscriber', 'category:active_user',
            'category:interested_user', 'category:exclusive_user',
            'category:suspended', 'category:banned', 'category:today',
        ];
        if (!in_array($targetAudience, $allowedAudiences)) {
            $targetAudience = 'all';
        }

        if (empty($name) || empty($trigger) || $templateId <= 0) {
            setFlash('danger', 'All fields are required.');
        } else {
            // Build visual drip layout steps; embed meta (target_audience, smtp_id) in first step
            $steps = [
                ['_meta' => ['target_audience' => $targetAudience, 'smtp_id' => $smtpId]],
                ['action' => 'wait', 'duration_hours' => 0],
                ['action' => 'send_email', 'template_id' => $templateId]
            ];

            $db->insert('marketing_automations', [
                'name'          => $name,
                'trigger_event' => $trigger,
                'status'        => $status,
                'steps_json'    => json_encode($steps)
            ]);
            setFlash('success', 'Automated drip campaign created.');
        }
        redirect(url('admin/marketing/automations'));
    }

    public function automationDelete(string $id): void {
        $db = Database::getInstance();
        $db->delete('marketing_automations', 'id = ?', [(int)$id]);
        setFlash('success', 'Automation deleted.');
        redirect(url('admin/marketing/automations'));
    }

    /**
     * Manual Broadcast: Send automation's email to its configured audience immediately
     */
    public function automationBroadcast(string $id): void {
        $db = Database::getInstance();
        $automation = $db->fetch("SELECT * FROM marketing_automations WHERE id = ?", [(int)$id]);

        if (!$automation || $automation['trigger_event'] !== 'manual_broadcast') {
            setFlash('danger', 'Automation not found or is not a manual broadcast type.');
            redirect(url('admin/marketing/automations'));
        }

        // Extract meta from steps_json
        $steps = json_decode($automation['steps_json'] ?? '[]', true);
        $meta = [];
        $templateId = null;
        foreach ($steps as $step) {
            if (isset($step['_meta'])) {
                $meta = $step['_meta'];
            }
            if (isset($step['action']) && $step['action'] === 'send_email') {
                $templateId = (int)($step['template_id'] ?? 0);
            }
        }

        $targetAudience = $meta['target_audience'] ?? 'all';
        $smtpId = !empty($meta['smtp_id']) ? (int)$meta['smtp_id'] : null;

        if (!$templateId) {
            setFlash('danger', 'No email template configured for this automation.');
            redirect(url('admin/marketing/automations'));
        }

        // Fetch template HTML
        $template = $db->fetch("SELECT content_html, subject FROM marketing_templates WHERE id = ?", [$templateId]);
        if (!$template || empty($template['content_html'])) {
            setFlash('danger', 'Selected email template has no content.');
            redirect(url('admin/marketing/automations'));
        }
        $htmlTemplate = $template['content_html'];
        $subject = $template['subject'] ?? $automation['name'];

        // Resolve subscribers by audience category
        $subscribers = [];
        try {
            (new MarketingSubscriber())->syncExisting();
        } catch (Throwable $e) {
            error_log('Automation broadcast audience sync failed: ' . $e->getMessage());
        }
        if ($targetAudience === 'all') {
            $subscribers = $db->fetchAll(
                "SELECT id, email, name FROM marketing_subscribers WHERE status = 'active' AND email LIKE '%@%.%'"
            );
        } elseif ($targetAudience === 'all_emails') {
            $subscribers = $db->fetchAll(
                "SELECT id, email, name FROM marketing_subscribers WHERE email LIKE '%@%.%'"
            );
        } elseif (str_starts_with($targetAudience, 'category:')) {
            $cat = substr($targetAudience, 9);
            if ($cat === 'today') {
                $subscribers = $db->fetchAll(
                    "SELECT id, email, name FROM marketing_subscribers WHERE DATE(created_at) = CURDATE() AND email LIKE '%@%.%'"
                );
            } elseif ($cat === 'suspended') {
                $subscribers = $db->fetchAll(
                    "SELECT id, email, name FROM marketing_subscribers WHERE status = 'suspended' AND email LIKE '%@%.%'"
                );
            } elseif ($cat === 'banned') {
                $subscribers = $db->fetchAll(
                    "SELECT id, email, name FROM marketing_subscribers WHERE status = 'banned' AND email LIKE '%@%.%'"
                );
            } else {
                $subscribers = $db->fetchAll(
                    "SELECT id, email, name FROM marketing_subscribers WHERE category = ? AND status = 'active' AND email LIKE '%@%.%'",
                    [$cat]
                );
            }
        }

        if (empty($subscribers)) {
            setFlash('danger', 'No subscribers found for the selected target audience.');
            redirect(url('admin/marketing/automations'));
        }

        // Queue all emails
        $db->beginTransaction();
        try {
            foreach ($subscribers as $sub) {
                $body = str_replace(
                    ['{{customer_name}}', '{{email}}', '{{unsubscribe}}'],
                    [
                        htmlspecialchars($sub['name'] ?? 'Valued Customer'),
                        htmlspecialchars($sub['email']),
                        url('unsubscribe?email=' . urlencode($sub['email']))
                    ],
                    $htmlTemplate
                );
                $db->insert('marketing_queue', [
                    'automation_id' => $automation['id'],
                    'subscriber_id' => $sub['id'],
                    'email'         => $sub['email'],
                    'subject'       => $subject,
                    'body_html'     => $body,
                    'smtp_id'       => $smtpId,
                    'status'        => 'pending',
                    'priority'      => 10
                ]);
            }
            $db->commit();

            // Attempt async processing
            if (class_exists('QueueProcessor')) {
                try {
                    $disabledFunctions = array_map('trim', explode(',', ini_get('disable_functions')));
                    $canRunAsync = !in_array('popen', $disabledFunctions) && !in_array('pclose', $disabledFunctions)
                                  && function_exists('popen') && function_exists('pclose');
                    if ($canRunAsync) {
                        (new QueueProcessor())->processBatchAsync();
                    }
                } catch (Throwable $ex) {}
            }

            setFlash('success', 'Broadcast queued! ' . count($subscribers) . ' email(s) added to the transmission queue.');
        } catch (Throwable $e) {
            $db->rollback();
            setFlash('danger', 'Error queuing broadcast: ' . $e->getMessage());
        }

        redirect(url('admin/marketing/automations'));
    }

    /**
     * CRM DataTables server-side processor
     */
    public function crmData(): void {
        header('Content-Type: application/json');
        $db = Database::getInstance();

        $draw = (int)($_POST['draw'] ?? 1);
        $start = (int)($_POST['start'] ?? 0);
        $length = (int)($_POST['length'] ?? 10);
        $searchValue = trim($_POST['search']['value'] ?? '');

        // Filters
        $category = trim($_POST['category'] ?? '');
        $status = trim($_POST['status'] ?? '');
        $source = trim($_POST['source'] ?? '');
        $regStart = trim($_POST['reg_start'] ?? '');
        $regEnd = trim($_POST['reg_end'] ?? '');
        $purchaseStart = trim($_POST['purchase_start'] ?? '');
        $purchaseEnd = trim($_POST['purchase_end'] ?? '');
        $paymentStatus = trim($_POST['payment_status'] ?? '');
        $country = trim($_POST['country'] ?? '');

        $where = ["ms.email LIKE '%@%.%'"];
        $params = [];

        if (!empty($category)) {
            $where[] = "ms.category = ?";
            $params[] = $category;
        }
        if (!empty($status)) {
            $where[] = "ms.status = ?";
            $params[] = $status;
        }
        if (!empty($source)) {
            $where[] = "ms.source = ?";
            $params[] = $source;
        }
        if (!empty($regStart)) {
            $where[] = "DATE(ms.created_at) >= ?";
            $params[] = $regStart;
        }
        if (!empty($regEnd)) {
            $where[] = "DATE(ms.created_at) <= ?";
            $params[] = $regEnd;
        }
        if (!empty($paymentStatus)) {
            $where[] = "EXISTS (SELECT 1 FROM orders WHERE user_id = ms.user_id AND payment_status = ?)";
            $params[] = $paymentStatus;
        }
        if (!empty($country)) {
            $where[] = "JSON_UNQUOTE(JSON_EXTRACT(ms.meta_data, '$.country')) = ?";
            $params[] = $country;
        }

        // Purchase Date Range
        if (!empty($purchaseStart) || !empty($purchaseEnd)) {
            $subWhere = "user_id = ms.user_id";
            if (!empty($purchaseStart)) {
                $subWhere .= " AND DATE(created_at) >= ?";
                $params[] = $purchaseStart;
            }
            if (!empty($purchaseEnd)) {
                $subWhere .= " AND DATE(created_at) <= ?";
                $params[] = $purchaseEnd;
            }
            $where[] = "EXISTS (SELECT 1 FROM orders WHERE {$subWhere})";
        }

        // Global Search (Email, Name, User ID, Order ID, Phone Number, Product Name)
        if (!empty($searchValue)) {
            $searchCond = "(ms.email LIKE ? OR ms.name LIKE ? OR ms.user_id = ? 
                OR EXISTS (SELECT 1 FROM orders WHERE user_id = ms.user_id AND order_number LIKE ?) 
                OR EXISTS (SELECT 1 FROM users WHERE id = ms.user_id AND phone LIKE ?) 
                OR EXISTS (SELECT 1 FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.user_id = ms.user_id AND oi.product_title LIKE ?))";
            
            $where[] = $searchCond;
            $s = "%{$searchValue}%";
            $params[] = $s;
            $params[] = $s;
            $params[] = is_numeric($searchValue) ? (int)$searchValue : -1;
            $params[] = $s;
            $params[] = $s;
            $params[] = $s;
        }

        $whereSql = implode(' AND ', $where);

        $totalRecords = $db->fetchColumn("SELECT COUNT(*) FROM marketing_subscribers WHERE email LIKE '%@%.%'");
        $filteredRecords = $db->fetchColumn(
            "SELECT COUNT(DISTINCT ms.id) FROM marketing_subscribers ms WHERE {$whereSql}",
            $params
        );

        $orderColumnIdx = (int)($_POST['order'][0]['column'] ?? 0);
        $orderDir = strtoupper($_POST['order'][0]['dir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        
        $columnsMap = [
            0 => 'ms.id',
            1 => 'ms.name',
            2 => 'ms.email',
            3 => 'ms.category',
            4 => 'ms.status',
            5 => 'ms.id',
            6 => 'ms.id',
            7 => 'ms.created_at',
            8 => 'ms.id'
        ];
        $orderField = $columnsMap[$orderColumnIdx] ?? 'ms.id';

        $data = $db->fetchAll(
            "SELECT ms.*, u.phone FROM marketing_subscribers ms 
             LEFT JOIN users u ON ms.user_id = u.id
             WHERE {$whereSql} 
             ORDER BY {$orderField} {$orderDir} 
             LIMIT {$length} OFFSET {$start}",
            $params
        );

        $results = [];
        foreach ($data as $row) {
            $meta = json_decode($row['meta_data'], true) ?: [];
            
            $results[] = [
                'id'            => $row['id'],
                'checkbox'      => '<input type="checkbox" class="sub-checkbox btn-dc-check" value="'.$row['id'].'">',
                'name'          => htmlspecialchars($row['name'] ?? 'Visitor'),
                'email'         => htmlspecialchars($row['email']),
                'category'      => $row['category'],
                'status'        => $row['status'],
                'product_count' => $meta['product_count'] ?? 0,
                'last_activity' => $meta['last_activity'] ?? $row['created_at'],
                'created_at'    => $row['created_at'],
                'phone'         => htmlspecialchars($row['phone'] ?? '-')
            ];
        }

        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => (int)$totalRecords,
            'recordsFiltered' => (int)$filteredRecords,
            'data'            => $results
        ]);
        exit;
    }

    /**
     * Individual Subscriber actions
     */
    public function crmAction(): void {
        header('Content-Type: application/json');
        if (!verifyCsrf()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request.']);
            exit;
        }

        $id = (int)($_POST['subscriber_id'] ?? 0);
        $action = trim($_POST['action'] ?? '');

        if ($id <= 0 || empty($action)) {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters.']);
            exit;
        }

        $db = Database::getInstance();
        $sub = $this->subModel->find($id);

        if (!$sub) {
            echo json_encode(['success' => false, 'error' => 'Subscriber not found.']);
            exit;
        }

        $email = $sub['email'];
        $userId = $sub['user_id'];

        try {
            if ($action === 'suspend') {
                $db->update('marketing_subscribers', ['status' => 'suspended'], 'id = ?', [$id]);
                $db->update('newsletter_subscribers', ['status' => 'unsubscribed'], 'email = ?', [$email]);
                
                if ($userId) {
                    $db->update('users', ['status' => 'suspended'], 'id = ?', [$userId]);
                }
                
                $this->subModel->logTimelineActivity($id, 'Suspended', "Account status changed to suspended by administrator.");
                $res = ['success' => true, 'message' => 'Subscriber suspended successfully.'];
                
            } elseif ($action === 'ban') {
                $db->update('marketing_subscribers', ['status' => 'banned'], 'id = ?', [$id]);
                
                if ($userId) {
                    $db->update('users', ['status' => 'banned'], 'id = ?', [$userId]);
                }
                
                $this->subModel->logTimelineActivity($id, 'Banned', "Account status changed to banned by administrator.");
                $res = ['success' => true, 'message' => 'User banned successfully.'];
                
            } elseif ($action === 'activate') {
                $db->update('marketing_subscribers', ['status' => 'active'], 'id = ?', [$id]);
                $db->update('newsletter_subscribers', ['status' => 'active'], 'email = ?', [$email]);
                
                if ($userId) {
                    $db->update('users', ['status' => 'active'], 'id = ?', [$userId]);
                }
                
                $this->subModel->logTimelineActivity($id, 'Activated', "Account status changed to active by administrator.");
                $res = ['success' => true, 'message' => 'Account activated successfully.'];
                
            } elseif ($action === 'delete') {
                if ($userId) {
                    $res = ['success' => false, 'error' => 'Registered users cannot be deleted. Please suspend or ban them instead.'];
                } else {
                    $db->delete('marketing_subscribers', 'id = ?', [$id]);
                    $db->delete('newsletter_subscribers', 'email = ?', [$email]);
                    $res = ['success' => true, 'message' => 'Subscriber deleted successfully.'];
                }
            } else {
                $res = ['success' => false, 'error' => 'Unknown action.'];
            }
            
            if ($action !== 'delete') {
                $this->subModel->syncSubscriberCategory($email);
            }
            
        } catch (Throwable $e) {
            $res = ['success' => false, 'error' => $e->getMessage()];
        }

        echo json_encode($res);
        exit;
    }

    /**
     * Delete a subscriber/email directly
     */
    public function crmDelete(): void {
        header('Content-Type: application/json');
        if (!verifyCsrf()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request.']);
            exit;
        }

        $payload = json_decode(file_get_contents('php://input'), true);
        $id = isset($payload['subscriber_id']) ? (int)$payload['subscriber_id'] : 0;
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid subscriber ID.']);
            exit;
        }

        $db = Database::getInstance();
        $sub = $this->subModel->find($id);
        if (!$sub) {
            echo json_encode(['success' => false, 'error' => 'Email not found.']);
            exit;
        }

        try {
            $email = $sub['email'];
            $userId = $sub['user_id'] ?? null;

            if ($userId) {
                echo json_encode(['success' => false, 'error' => 'Registered users cannot be deleted. Please suspend or ban them instead.']);
                exit;
            }

            $db->delete('marketing_subscribers', 'id = ?', [$id]);
            $db->delete('newsletter_subscribers', 'email = ?', [$email]);

            $this->subModel->logTimelineActivity($id, 'Deleted', "Email deleted from database by administrator.");

            echo json_encode(['success' => true, 'message' => 'Email deleted successfully.']);
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Send test email to subscriber
     */
    public function crmSendTestEmail(): void {
        header('Content-Type: application/json');
        if (!verifyCsrf()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request.']);
            exit;
        }

        $payload = $_POST;
        if (empty($payload)) {
            $raw = file_get_contents('php://input');
            if (str_starts_with(trim($raw), '{')) {
                $payload = json_decode($raw, true) ?: [];
            } else {
                parse_str($raw, $payload);
            }
        }
        $payload = $payload ?: [];
        $subscriberId = isset($payload['subscriber_id']) ? (int)$payload['subscriber_id'] : 0;
        $templateId = isset($payload['template_id']) ? (int)$payload['template_id'] : 0;
        $recipient = trim($payload['recipient'] ?? '');

        if (empty($recipient) || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Invalid recipient email.']);
            exit;
        }

        $db = Database::getInstance();
        $mailer = new MarketingMailer();

        // Template-based test email
        if ($templateId > 0) {
            $template = $db->fetch("SELECT name, subject, content_html FROM marketing_templates WHERE id = ?", [$templateId]);
            if (!$template) {
                echo json_encode(['success' => false, 'error' => 'Template not found.']);
                exit;
            }

            $subject = $template['subject'] ?: $template['name'];
            $body = $template['content_html'] ?: '';

            $body = str_replace(
                ['{{customer_name}}', '{{email}}', '{{unsubscribe}}'],
                ['Valued Customer', htmlspecialchars($recipient), url('unsubscribe?email=' . urlencode($recipient))],
                $body
            );

            $mailResult = $mailer->send($recipient, $subject, $body);

            if ($mailResult['success']) {
                echo json_encode(['success' => true, 'message' => "Template test email sent successfully to {$recipient} via SMTP: " . ($mailResult['smtp_name'] ?? 'Default')]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to send email: ' . ($mailResult['error'] ?? 'Please check server configuration.')]);
            }
            exit;
        }

        // Existing subscriber-based test email
        if ($subscriberId <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters.']);
            exit;
        }

        $sub = $this->subModel->find($subscriberId);
        if (!$sub) {
            echo json_encode(['success' => false, 'error' => 'Subscriber not found.']);
            exit;
        }

        try {
            $subject = "Test Email from DigiWS";
            $body = "This is a test email sent to {$recipient} from your DigiWS marketing dashboard.<br><br>" .
                    "Subscriber: " . htmlspecialchars($sub['name']) . " (" . htmlspecialchars($sub['email']) . ")<br>" .
                    "Template: N/A<br><br>" .
                    "If this is spam, please ignore.";

            $mailResult = $mailer->send($recipient, $subject, $body);

            if ($mailResult['success']) {
                $this->subModel->logTimelineActivity($subscriberId, 'Email Sent', "Test email sent to {$recipient}");
                echo json_encode(['success' => true, 'message' => "Test email sent successfully to {$recipient} via SMTP: " . ($mailResult['smtp_name'] ?? 'Default')]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to send email: ' . ($mailResult['error'] ?? 'Please check server configuration.')]);
            }
        } catch (Throwable $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Fetch Subscriber Activity Timeline
     */
    public function crmTimeline(): void {
        header('Content-Type: application/json');
        $id = (int)($_POST['subscriber_id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid subscriber ID.']);
            exit;
        }

        $db = Database::getInstance();
        $sub = $this->subModel->find($id);
        if (!$sub) {
            echo json_encode(['success' => false, 'error' => 'Subscriber not found.']);
            exit;
        }

        $timeline = $db->fetchAll(
            "SELECT * FROM marketing_crm_timeline WHERE subscriber_id = ? ORDER BY created_at DESC",
            [$id]
        );

        echo json_encode(['success' => true, 'timeline' => $timeline, 'subscriber' => $sub]);
        exit;
    }

    /**
     * Process bulk commands
     */
    public function crmBulkAction(): void {
        header('Content-Type: application/json');
        if (!verifyCsrf()) {
            echo json_encode(['success' => false, 'error' => 'Invalid request.']);
            exit;
        }

        $ids = $_POST['subscriber_ids'] ?? [];
        $action = trim($_POST['action'] ?? '');

        if (empty($ids) || !is_array($ids) || empty($action)) {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters.']);
            exit;
        }

        $db = Database::getInstance();
        $successCount = 0;
        $errors = [];

        foreach ($ids as $id) {
            $id = (int)$id;
            $sub = $this->subModel->find($id);
            if (!$sub) continue;

            $email = $sub['email'];
            $userId = $sub['user_id'];

            try {
                if ($action === 'suspend') {
                    $db->update('marketing_subscribers', ['status' => 'suspended'], 'id = ?', [$id]);
                    $db->update('newsletter_subscribers', ['status' => 'unsubscribed'], 'email = ?', [$email]);
                    if ($userId) {
                        $db->update('users', ['status' => 'suspended'], 'id = ?', [$userId]);
                    }
                    $this->subModel->logTimelineActivity($id, 'Suspended', "Bulk suspended by administrator.");
                    $successCount++;
                    
                } elseif ($action === 'ban') {
                    $db->update('marketing_subscribers', ['status' => 'banned'], 'id = ?', [$id]);
                    if ($userId) {
                        $db->update('users', ['status' => 'banned'], 'id = ?', [$userId]);
                    }
                    $this->subModel->logTimelineActivity($id, 'Banned', "Bulk banned by administrator.");
                    $successCount++;
                    
                } elseif ($action === 'activate') {
                    $db->update('marketing_subscribers', ['status' => 'active'], 'id = ?', [$id]);
                    $db->update('newsletter_subscribers', ['status' => 'active'], 'email = ?', [$email]);
                    if ($userId) {
                        $db->update('users', ['status' => 'active'], 'id = ?', [$userId]);
                    }
                    $this->subModel->logTimelineActivity($id, 'Activated', "Bulk activated by administrator.");
                    $successCount++;
                    
                } elseif ($action === 'delete') {
                    if ($userId) {
                        $errors[] = "{$email} could not be deleted (registered user).";
                    } else {
                        $db->delete('marketing_subscribers', 'id = ?', [$id]);
                        $db->delete('newsletter_subscribers', 'email = ?', [$email]);
                        $successCount++;
                    }
                }
                
                if ($action !== 'delete') {
                    $this->subModel->syncSubscriberCategory($email);
                }
            } catch (Throwable $e) {
                $errors[] = "Error processing {$email}: " . $e->getMessage();
            }
        }

        echo json_encode([
            'success' => true,
            'message' => "Successfully processed {$successCount} records.",
            'errors'  => $errors
        ]);
        exit;
    }

    /**
     * Custom exporter (CSV / Excel / PDF)
     */
    public function crmExport(): void {
        $format = trim($_GET['format'] ?? 'csv');
        $db = Database::getInstance();

        // Filters
        $category = trim($_GET['category'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $source = trim($_GET['source'] ?? '');
        $regStart = trim($_GET['reg_start'] ?? '');
        $regEnd = trim($_GET['reg_end'] ?? '');
        $purchaseStart = trim($_GET['purchase_start'] ?? '');
        $purchaseEnd = trim($_GET['purchase_end'] ?? '');
        $paymentStatus = trim($_GET['payment_status'] ?? '');
        $country = trim($_GET['country'] ?? '');
        $searchValue = trim($_GET['search'] ?? '');

        $where = ["ms.email LIKE '%@%.%'"];
        $params = [];

        if (!empty($category)) {
            $where[] = "ms.category = ?";
            $params[] = $category;
        }
        if (!empty($status)) {
            $where[] = "ms.status = ?";
            $params[] = $status;
        }
        if (!empty($source)) {
            $where[] = "ms.source = ?";
            $params[] = $source;
        }
        if (!empty($regStart)) {
            $where[] = "DATE(ms.created_at) >= ?";
            $params[] = $regStart;
        }
        if (!empty($regEnd)) {
            $where[] = "DATE(ms.created_at) <= ?";
            $params[] = $regEnd;
        }
        if (!empty($paymentStatus)) {
            $where[] = "EXISTS (SELECT 1 FROM orders WHERE user_id = ms.user_id AND payment_status = ?)";
            $params[] = $paymentStatus;
        }
        if (!empty($country)) {
            $where[] = "JSON_UNQUOTE(JSON_EXTRACT(ms.meta_data, '$.country')) = ?";
            $params[] = $country;
        }
        if (!empty($purchaseStart) || !empty($purchaseEnd)) {
            $subWhere = "user_id = ms.user_id";
            if (!empty($purchaseStart)) {
                $subWhere .= " AND DATE(created_at) >= ?";
                $params[] = $purchaseStart;
            }
            if (!empty($purchaseEnd)) {
                $subWhere .= " AND DATE(created_at) <= ?";
                $params[] = $purchaseEnd;
            }
            $where[] = "EXISTS (SELECT 1 FROM orders WHERE {$subWhere})";
        }

        if (!empty($searchValue)) {
            $searchCond = "(ms.email LIKE ? OR ms.name LIKE ? OR ms.user_id = ? 
                OR EXISTS (SELECT 1 FROM orders WHERE user_id = ms.user_id AND order_number LIKE ?) 
                OR EXISTS (SELECT 1 FROM users WHERE id = ms.user_id AND phone LIKE ?) 
                OR EXISTS (SELECT 1 FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.user_id = ms.user_id AND oi.product_title LIKE ?))";
            
            $where[] = $searchCond;
            $s = "%{$searchValue}%";
            $params[] = $s;
            $params[] = $s;
            $params[] = is_numeric($searchValue) ? (int)$searchValue : -1;
            $params[] = $s;
            $params[] = $s;
            $params[] = $s;
        }

        $whereSql = implode(' AND ', $where);

        $data = $db->fetchAll(
            "SELECT ms.*, u.phone FROM marketing_subscribers ms 
             LEFT JOIN users u ON ms.user_id = u.id
             WHERE {$whereSql} 
             ORDER BY ms.id DESC",
            $params
        );

        $filename = "crm_audience_" . date('Ymd_His');

        if ($format === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Name', 'Email Address', 'Category', 'Status', 'Source', 'Tags', 'Product Count', 'Total Spent', 'Joined Date']);
            
            foreach ($data as $row) {
                $meta = json_decode($row['meta_data'], true) ?: [];
                fputcsv($output, [
                    $row['name'],
                    $row['email'],
                    str_replace('_', ' ', ucwords($row['category'])),
                    ucfirst($row['status']),
                    ucfirst($row['source']),
                    $row['crm_tags'],
                    $meta['product_count'] ?? 0,
                    $meta['total_spent'] ?? 0.00,
                    $row['created_at']
                ]);
            }
            fclose($output);
            exit;
            
        } elseif ($format === 'excel') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
            
            echo '<table border="1">';
            echo '<tr>
                    <th style="background-color:#7c3aed; color:#ffffff;">Name</th>
                    <th style="background-color:#7c3aed; color:#ffffff;">Email Address</th>
                    <th style="background-color:#7c3aed; color:#ffffff;">Category</th>
                    <th style="background-color:#7c3aed; color:#ffffff;">Status</th>
                    <th style="background-color:#7c3aed; color:#ffffff;">Source</th>
                    <th style="background-color:#7c3aed; color:#ffffff;">Tags</th>
                    <th style="background-color:#7c3aed; color:#ffffff;">Product Count</th>
                    <th style="background-color:#7c3aed; color:#ffffff;">Total Spent</th>
                    <th style="background-color:#7c3aed; color:#ffffff;">Joined Date</th>
                  </tr>';
            
            foreach ($data as $row) {
                $meta = json_decode($row['meta_data'], true) ?: [];
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '<td>' . htmlspecialchars(str_replace('_', ' ', ucwords($row['category']))) . '</td>';
                echo '<td>' . htmlspecialchars(ucfirst($row['status'])) . '</td>';
                echo '<td>' . htmlspecialchars(ucfirst($row['source'])) . '</td>';
                echo '<td>' . htmlspecialchars($row['crm_tags'] ?? '') . '</td>';
                echo '<td>' . (int)($meta['product_count'] ?? 0) . '</td>';
                echo '<td>' . (float)($meta['total_spent'] ?? 0.00) . '</td>';
                echo '<td>' . htmlspecialchars($row['created_at']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            exit;
            
        } elseif ($format === 'pdf') {
            if (!class_exists('TCPDF')) {
                require_once INCLUDES_PATH . '/tcpdf/tcpdf.php';
            }
            
            $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetCreator('Digiws System');
            $pdf->SetTitle('CRM Audience Export');
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(10, 15, 10);
            $pdf->SetAutoPageBreak(TRUE, 15);
            $pdf->AddPage();
            
            $html = '<h2>CRM Audience List</h2>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0" width="100%">';
            $html .= '<tr style="background-color:#7c3aed; color:#ffffff; font-weight:bold;">
                        <th width="15%">Name</th>
                        <th width="20%">Email</th>
                        <th width="15%">Category</th>
                        <th width="10%">Status</th>
                        <th width="10%">Source</th>
                        <th width="10%">Spent</th>
                        <th width="20%">Joined</th>
                      </tr>';
            
            foreach ($data as $row) {
                $meta = json_decode($row['meta_data'], true) ?: [];
                $categoryName = str_replace('_', ' ', ucwords($row['category']));
                $statusName = ucfirst($row['status']);
                $sourceName = ucfirst($row['source']);
                $totalSpent = formatPrice($meta['total_spent'] ?? 0.00);
                
                $html .= '<tr>
                            <td>' . htmlspecialchars($row['name']) . '</td>
                            <td>' . htmlspecialchars($row['email']) . '</td>
                            <td>' . htmlspecialchars($categoryName) . '</td>
                            <td>' . htmlspecialchars($statusName) . '</td>
                            <td>' . htmlspecialchars($sourceName) . '</td>
                            <td>' . htmlspecialchars($totalSpent) . '</td>
                            <td>' . htmlspecialchars($row['created_at']) . '</td>
                          </tr>';
            }
            $html .= '</table>';
            
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output($filename . '.pdf', 'D');
            exit;
        }
    }
}
