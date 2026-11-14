<?php
/**
 * DigiWS - Marketing Subscriber Model
 * Handles subscriber tracking, CRM categories, timeline logs, and search/filter.
 */

class MarketingSubscriber {
    private Database $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Finds a subscriber by ID
     */
    public function find(int $id): ?array {
        return $this->db->fetch("SELECT * FROM marketing_subscribers WHERE id = ?", [$id]);
    }

    /**
     * Finds a subscriber by Email
     */
    public function findByEmail(string $email): ?array {
        return $this->db->fetch("SELECT * FROM marketing_subscribers WHERE email = ?", [strtolower(trim($email))]);
    }

    /**
     * Log a CRM Timeline activity event
     */
    public function logTimelineActivity(int $subscriberId, string $type, string $desc = ''): void {
        try {
            $this->db->insert('marketing_crm_timeline', [
                'subscriber_id' => $subscriberId,
                'activity_type' => $type,
                'description'   => $desc
            ]);
        } catch (Exception $e) {
            // Silence exceptions to prevent breaking critical flows
        }
    }

    /**
     * Process public subscription (Popup / Footer newsletter form)
     */
    public function subscribe(string $email, string $source = 'footer'): array {
        $email = strtolower(trim($email));
        $existing = $this->findByEmail($email);
        
        // 1. Sync/save into legacy newsletter_subscribers for compatibility
        try {
            $legacy = $this->db->fetch("SELECT id FROM newsletter_subscribers WHERE email = ?", [$email]);
            if (!$legacy) {
                $this->db->insert('newsletter_subscribers', [
                    'email'  => $email,
                    'status' => 'active'
                ]);
            } else {
                $this->db->update('newsletter_subscribers', ['status' => 'active'], 'email = ?', [$email]);
            }
        } catch (Exception $e) {}

        // 2. Add or update master subscriber record
        if ($existing) {
            // Already in CRM: raise is_subscriber flag, keep category re-evaluation
            $this->db->update('marketing_subscribers', [
                'is_subscriber' => 1,
                'status'        => $existing['status'] === 'suspended' ? 'active' : $existing['status'] // activate if they re-subscribe
            ], 'id = ?', [$existing['id']]);
            $subId = (int)$existing['id'];
            
            // Re-log Subscription activity if they were not marked as subscriber
            if (!$existing['is_subscriber']) {
                $this->logTimelineActivity($subId, 'Subscribed', "Subscribed to newsletter via {$source}.");
            }
        } else {
            // New record: create with is_subscriber = 1
            $name = explode('@', $email)[0];
            $meta = [
                'country' => 'Global',
                'state' => 'Unknown',
                'city' => 'Unknown',
                'language' => 'en',
                'total_spent' => 0.00,
                'purchase_frequency' => 0,
                'product_count' => 0,
                'last_login' => null,
                'last_activity' => date('Y-m-d H:i:s')
            ];
            
            $subId = $this->db->insert('marketing_subscribers', [
                'email'         => $email,
                'name'          => $name,
                'status'        => 'active',
                'source'        => $source,
                'is_subscriber' => 1,
                'meta_data'     => json_encode($meta)
            ]);
            
            $this->logTimelineActivity($subId, 'Subscribed', "Subscribed to newsletter via {$source}.");
        }
        
        // Re-evaluate CRM categories & tags
        $this->syncSubscriberCategory($email);
        
        return ['success' => true, 'id' => $subId];
    }

    /**
     * Compute and update CRM categories, flags, activity metrics, and tags
     */
    public function syncSubscriberCategory(string $email): void {
        $email = strtolower(trim($email));
        $existing = $this->findByEmail($email);
        
        // Find if user account exists
        $user = $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
        
        $isSubscriber = $existing ? (int)$existing['is_subscriber'] : 0;
        $isRegistered = $user ? 1 : 0;
        $isInterested = $existing ? (int)$existing['is_interested'] : 0;
        $isExclusive = $existing ? (int)$existing['is_exclusive'] : 0;
        
        $userId = $user ? (int)$user['id'] : null;
        $name = $user ? $user['name'] : ($existing ? $existing['name'] : explode('@', $email)[0]);
        $status = $existing ? $existing['status'] : 'active';
        $source = $existing ? $existing['source'] : ($user ? 'registration' : 'website');
        
        // Sync status from users if suspended or banned
        if ($user) {
            if ($user['status'] === 'banned' || $user['status'] === 'suspended') {
                $status = $user['status'];
            }
        }
        
        // Evaluate order history if user/email exists
        $totalSpent = 0.00;
        $purchaseFreq = 0;
        $productCount = 0;
        $lastLogin = $user['last_login'] ?? null;
        
        if ($userId) {
            // Count successful orders
            $completedOrders = $this->db->fetchAll(
                "SELECT id, total FROM orders WHERE user_id = ? AND payment_status = 'paid' AND status = 'completed'",
                [$userId]
            );
            
            if (count($completedOrders) > 0) {
                $isExclusive = 1; // 1 successful purchase keeps them Exclusive forever
            }
            
            // If they are exclusive, total spent matches all successful orders
            $totalSpent = (float) $this->db->fetchColumn(
                "SELECT SUM(total) FROM orders WHERE user_id = ? AND payment_status = 'paid'",
                [$userId]
            ) ?: 0.00;
            
            $purchaseFreq = (int) $this->db->fetchColumn(
                "SELECT COUNT(*) FROM orders WHERE user_id = ? AND payment_status = 'paid'",
                [$userId]
            ) ?: 0;
            
            $productCount = (int) $this->db->fetchColumn(
                "SELECT SUM(oi.quantity) FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.user_id = ? AND o.payment_status = 'paid'",
                [$userId]
            ) ?: 0;
            
            // Check if they are interested (if not exclusive, have they shown buying interest?)
            if (!$isExclusive) {
                // Check if they have ANY order with payment_status failed/pending or status cancelled/pending
                $interestCount = (int) $this->db->fetchColumn(
                    "SELECT COUNT(*) FROM orders WHERE user_id = ? AND (payment_status IN ('failed', 'pending') OR status IN ('cancelled', 'pending'))",
                    [$userId]
                );
                if ($interestCount > 0) {
                    $isInterested = 1;
                }
            }
        }
        
        // Priority Engine to compute Display Category
        $category = 'subscriber';
        if ($isExclusive) {
            $category = 'exclusive_user';
        } elseif ($isInterested) {
            $category = 'interested_user';
        } elseif ($isRegistered) {
            $category = 'active_user';
        } elseif ($isSubscriber) {
            $category = 'subscriber';
        }
        
        // Compute CRM Marketing tags
        $tags = [];
        if ($status === 'suspended') {
            $tags[] = 'Suspended';
        }
        
        if ($isExclusive) {
            $tags[] = 'Customer';
            if ($purchaseFreq > 2) {
                $tags[] = 'Returning Customer';
            }
            if ($totalSpent > 5000) {
                $tags[] = 'VIP';
            }
        } elseif ($isInterested) {
            $tags[] = 'Interested';
        } elseif ($isRegistered) {
            $tags[] = 'New Lead';
        } else {
            $tags[] = 'Subscriber';
        }
        
        // Inactive tag check: no activity for 30 days
        $meta = $existing && $existing['meta_data'] ? json_decode($existing['meta_data'], true) : [];
        $lastActivity = $meta['last_activity'] ?? date('Y-m-d H:i:s');
        if (strtotime($lastActivity) < strtotime('-30 days')) {
            $tags[] = 'Inactive';
        }
        
        $crmTags = implode(', ', $tags);
        
        // Merge meta data
        $meta = array_merge([
            'country' => 'Global',
            'state' => 'Unknown',
            'city' => 'Unknown',
            'language' => 'en',
        ], $meta, [
            'total_spent'        => $totalSpent,
            'purchase_frequency' => $purchaseFreq,
            'product_count'      => $productCount,
            'last_login'         => $lastLogin,
            'last_activity'      => $lastActivity
        ]);
        
        $fields = [
            'email'         => $email,
            'name'          => $name,
            'status'        => $status,
            'source'        => $source,
            'user_id'       => $userId,
            'category'      => $category,
            'is_subscriber' => $isSubscriber,
            'is_registered' => $isRegistered,
            'is_interested' => $isInterested,
            'is_exclusive'  => $isExclusive,
            'crm_tags'      => $crmTags,
            'meta_data'     => json_encode($meta)
        ];
        
        if ($existing) {
            $this->db->update('marketing_subscribers', $fields, 'id = ?', [$existing['id']]);
        } else {
            $this->db->insert('marketing_subscribers', $fields);
        }
    }

    /**
     * Re-sync and re-evaluate all unique emails in the system into unified master records
     */
    public function syncExisting(): array {
        // Collect all distinct emails in lower case
        $emails = [];
        
        try {
            $users = $this->db->fetchAll("SELECT email FROM users");
            foreach ($users as $u) {
                $emails[strtolower(trim($u['email']))] = true;
            }
        } catch (Exception $e) {}
        
        try {
            $newsletters = $this->db->fetchAll("SELECT email FROM newsletter_subscribers");
            foreach ($newsletters as $n) {
                $emails[strtolower(trim($n['email']))] = true;
            }
        } catch (Exception $e) {}
        
        try {
            $subs = $this->db->fetchAll("SELECT email FROM marketing_subscribers");
            foreach ($subs as $s) {
                $emails[strtolower(trim($s['email']))] = true;
            }
        } catch (Exception $e) {}
        
        $synced = 0;
        foreach ($emails as $email => $val) {
            // Determine starting subscriber flag
            $existing = $this->findByEmail($email);
            $legacyNewsletter = $this->db->fetch("SELECT id FROM newsletter_subscribers WHERE email = ?", [$email]);
            
            $isSub = ($existing && $existing['is_subscriber']) || $legacyNewsletter ? 1 : 0;
            
            if (!$existing) {
                $this->db->insert('marketing_subscribers', [
                    'email'         => $email,
                    'name'          => explode('@', $email)[0],
                    'status'        => 'active',
                    'source'        => $legacyNewsletter ? 'footer' : 'registration',
                    'is_subscriber' => $isSub
                ]);
            } else {
                $this->db->update('marketing_subscribers', ['is_subscriber' => $isSub], 'id = ?', [$existing['id']]);
            }
            
            $this->syncSubscriberCategory($email);
            $synced++;
        }
        
        return ['total' => $synced];
    }

    /**
     * Compiles dynamic segment filter rules into SQL WHERE clause
     */
    public function compileRules(array $rules): array {
        $whereClauses = ["email LIKE '%@%.%'"];
        $params = [];

        foreach ($rules as $rule) {
            $field = $rule['field'] ?? '';
            $operator = $rule['operator'] ?? 'equals';
            $value = $rule['value'] ?? '';

            $sqlField = '';
            $isNumeric = false;

            if (in_array($field, ['email', 'name', 'status', 'source', 'category', 'crm_tags', 'is_subscriber', 'is_registered', 'is_interested', 'is_exclusive', 'created_at'])) {
                $sqlField = "`{$field}`";
            } else {
                // Treats as JSON meta_data field
                if (in_array($field, ['total_spent', 'purchase_frequency', 'product_count'])) {
                    $sqlField = "CAST(JSON_UNQUOTE(JSON_EXTRACT(meta_data, '$.{$field}')) AS DECIMAL(10,2))";
                    $isNumeric = true;
                } else {
                    $sqlField = "JSON_UNQUOTE(JSON_EXTRACT(meta_data, '$.{$field}'))";
                }
            }

            $clause = '';
            switch ($operator) {
                case 'equals':
                    $clause = "{$sqlField} = ?";
                    $params[] = $value;
                    break;
                case 'not_equals':
                    $clause = "{$sqlField} != ?";
                    $params[] = $value;
                    break;
                case 'contains':
                    $clause = "{$sqlField} LIKE ?";
                    $params[] = "%{$value}%";
                    break;
                case 'starts_with':
                    $clause = "{$sqlField} LIKE ?";
                    $params[] = "{$value}%";
                    break;
                case 'ends_with':
                    $clause = "{$sqlField} LIKE ?";
                    $params[] = "%{$value}";
                    break;
                case 'greater_than':
                    $clause = "{$sqlField} > ?";
                    $params[] = $isNumeric ? (float)$value : $value;
                    break;
                case 'less_than':
                    $clause = "{$sqlField} < ?";
                    $params[] = $isNumeric ? (float)$value : $value;
                    break;
            }

            if (!empty($clause)) {
                $whereClauses[] = $clause;
            }
        }

        return [
            'where' => implode(' AND ', $whereClauses),
            'params' => $params
        ];
    }

    /**
     * Get subscriber count based on segment rules
     */
    public function getSegmentCount(array $rules): int {
        $compiled = $this->compileRules($rules);
        $sql = "SELECT COUNT(*) FROM marketing_subscribers WHERE {$compiled['where']}";
        return (int) $this->db->fetchColumn($sql, $compiled['params']);
    }

    /**
     * Fetch subscribers belonging to a dynamic segment
     */
    public function getSegmentSubscribers(array $rules, int $limit = 50, int $offset = 0): array {
        $compiled = $this->compileRules($rules);
        $sql = "SELECT * FROM marketing_subscribers WHERE {$compiled['where']} ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}";
        return $this->db->fetchAll($sql, $compiled['params']);
    }

    /**
     * Trigger a marketing automation event to queue drip emails for matching subscribers
     */
    public static function triggerAutomation(string $event, array $context): void {
        // Keep this public compatibility entry point for existing controllers,
        // but delegate to the durable queue-only dispatcher.
        (new AutomationService())->trigger($event, $context);
        return;

        $db = Database::getInstance();
        try {
            // Find all active automations with this trigger_event
            $automations = $db->fetchAll(
                "SELECT * FROM marketing_automations WHERE trigger_event = ? AND status = 'active'",
                [$event]
            );

            if (empty($automations)) {
                return;
            }

            foreach ($automations as $automation) {
                // Extract meta & template from steps_json
                $steps = json_decode($automation['steps_json'] ?? '[]', true);
                $meta = [];
                $templateId = null;
                $waitHours = 0;

                foreach ($steps as $step) {
                    if (isset($step['_meta'])) {
                        $meta = $step['_meta'];
                    }
                    if (isset($step['action']) && $step['action'] === 'wait') {
                        $waitHours = (int)($step['duration_hours'] ?? 0);
                    }
                    if (isset($step['action']) && $step['action'] === 'send_email') {
                        $templateId = (int)($step['template_id'] ?? 0);
                    }
                }

                if (!$templateId) {
                    continue;
                }

                $smtpId = !empty($meta['smtp_id']) ? (int)$meta['smtp_id'] : null;

                // Fetch template HTML
                $template = $db->fetch("SELECT content_html, subject FROM marketing_templates WHERE id = ?", [$templateId]);
                if (!$template || empty($template['content_html'])) {
                    continue;
                }
                $htmlTemplate = $template['content_html'];
                $subject = $template['subject'] ?? $automation['name'];

                // Determine list of subscribers for this event
                $subscribers = [];
                
                if ($event === 'new_product_published') {
                    // Send to the audience configured in $meta['target_audience']
                    $targetAudience = $meta['target_audience'] ?? 'all';
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
                } else {
                    // For user_signup or order_placed, it's a single subscriber!
                    $email = strtolower(trim($context['email'] ?? ''));
                    if (empty($email)) {
                        continue;
                    }
                    $sub = $db->fetch("SELECT id, email, name FROM marketing_subscribers WHERE email = ?", [$email]);
                    if ($sub) {
                        $subscribers[] = $sub;
                    }
                }

                if (empty($subscribers)) {
                    continue;
                }

                // Queue all emails
                $db->beginTransaction();
                try {
                    foreach ($subscribers as $sub) {
                        // Check if already queued for this automation and subscriber to avoid duplicate sending
                        $exists = $db->fetchColumn(
                            "SELECT COUNT(*) FROM marketing_queue WHERE automation_id = ? AND subscriber_id = ?",
                            [$automation['id'], $sub['id']]
                        );
                        if ($exists > 0) {
                            continue; // Prevent duplicate queues for same automation subscriber
                        }

                        $body = str_replace(
                            ['{{customer_name}}', '{{email}}', '{{unsubscribe}}'],
                            [
                                htmlspecialchars($sub['name'] ?? 'Valued Customer'),
                                htmlspecialchars($sub['email']),
                                url('unsubscribe?email=' . urlencode($sub['email']))
                            ],
                            $htmlTemplate
                        );

                        // Support product details placeholders if provided
                        if ($event === 'new_product_published' && isset($context['product'])) {
                            $product = $context['product'];
                            $body = str_replace(
                                ['{{product_title}}', '{{product_price}}', '{{product_url}}'],
                                [
                                    htmlspecialchars($product['title'] ?? ''),
                                    sprintf('$%.2f', $product['sale_price'] ?? 0),
                                    url('product/' . ($product['slug'] ?? ''))
                                ],
                                $body
                            );
                            $subject = str_replace(
                                ['{{product_title}}'],
                                [htmlspecialchars($product['title'] ?? '')],
                                $subject
                            );
                        }

                        $scheduledAt = null;
                        if ($waitHours > 0) {
                            $scheduledAt = date('Y-m-d H:i:s', strtotime("+{$waitHours} hours"));
                        }

                        $db->insert('marketing_queue', [
                            'automation_id' => $automation['id'],
                            'subscriber_id' => $sub['id'],
                            'email'         => $sub['email'],
                            'subject'       => $subject,
                            'body_html'     => $body,
                            'smtp_id'       => $smtpId,
                            'status'        => 'pending',
                            'priority'      => 10,
                            'scheduled_at'  => $scheduledAt
                        ]);
                    }
                    $db->commit();

                    // Process queue async
                    if (class_exists('QueueProcessor')) {
                        (new QueueProcessor())->processBatchAsync();
                    }
                } catch (Throwable $e) {
                    $db->rollback();
                }
            }
        } catch (Throwable $e) {
            // Silence trigger errors to not break checkout or registration
        }
    }
}
