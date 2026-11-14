<!-- Automated Email Workflows -->
<style>
.auto-header {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.auto-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}
.auto-stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
}
.auto-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.auto-stat-icon.primary { background: #eef2ff; color: #6366f1; }
.auto-stat-icon.success { background: #d1fae5; color: #059669; }
.auto-stat-icon.warning { background: #fef3c7; color: #d97706; }
.auto-stat-icon.info { background: #eff6ff; color: #2563eb; }
.auto-stat-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #94a3b8;
    margin-bottom: 2px;
}
.auto-stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.auto-workflow-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
    gap: 16px;
}
.auto-workflow-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.2s;
}
.auto-workflow-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}
.auto-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}
.auto-card-title {
    font-weight: 700;
    color: #0f172a;
    font-size: 0.95rem;
    margin: 0;
}
.auto-card-body {
    padding: 16px 20px;
}
.auto-meta-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}
.auto-meta-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #94a3b8;
    min-width: 70px;
}
.auto-steps {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #f1f5f9;
}
.auto-step-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    white-space: nowrap;
}
.auto-step-arrow {
    color: #cbd5e1;
    font-size: 0.8rem;
}
.auto-card-actions {
    padding: 12px 20px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    gap: 8px;
    justify-content: flex-end;
    background: #fafbfc;
}
.auto-btn {
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.auto-btn-primary {
    background: #6366f1;
    color: #ffffff;
}
.auto-btn-primary:hover {
    background: #4f46e5;
}
.auto-btn-outline {
    background: transparent;
    color: #475569;
    border: 1px solid #e2e8f0;
}
.auto-btn-outline:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}
.auto-btn-danger {
    background: transparent;
    color: #dc2626;
    border: 1px solid #fecaca;
}
.auto-btn-danger:hover {
    background: #fef2f2;
    border-color: #dc2626;
}
.auto-empty {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 60px 20px;
    text-align: center;
    color: #94a3b8;
}
.auto-empty i {
    font-size: 3rem;
    margin-bottom: 16px;
    color: #cbd5e1;
    display: block;
}
.auto-empty p {
    font-size: 0.9rem;
    margin: 0;
}
.auto-modal .modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}
.auto-modal .modal-header {
    background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%);
    color: #ffffff;
    border-radius: 16px 16px 0 0;
    padding: 20px 24px;
    border-bottom: none;
}
.auto-modal .modal-title {
    color: #ffffff;
    font-weight: 700;
}
.auto-modal .btn-close {
    filter: brightness(0) invert(1);
}
.auto-modal .modal-footer {
    border-top: 1px solid #f1f5f9;
    padding: 16px 24px;
}
.auto-modal .form-group {
    margin-bottom: 16px;
}
.auto-modal .dc-form-label {
    font-size: 0.78rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 6px;
    display: block;
}
.auto-modal .dc-form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.9rem;
    color: #334155;
    background: #ffffff;
    transition: all 0.2s;
}
.auto-modal .dc-form-control:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
}
.auto-info-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 16px;
    font-size: 0.82rem;
    color: #475569;
    margin-top: 10px;
}
.auto-info-box strong {
    color: #0f172a;
}
</style>

<div class="auto-header">
    <div>
        <h5 class="mb-1 fw-bold text-dark">Automated Email Workflows</h5>
        <p class="text-muted mb-0 small">Create event-triggered email sequences and drip campaigns.</p>
    </div>
    <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createAutomationModal">
        <i class="bi bi-plus-lg me-1"></i>Create Workflow
    </button>
</div>

<div class="auto-stats">
    <div class="auto-stat-card">
        <div class="auto-stat-icon primary"><i class="bi bi-diagram-3"></i></div>
        <div>
            <div class="auto-stat-label">Total Workflows</div>
            <div class="auto-stat-value"><?= count($automations) ?></div>
        </div>
    </div>
    <div class="auto-stat-card">
        <div class="auto-stat-icon success"><i class="bi bi-check-circle"></i></div>
        <div>
            <div class="auto-stat-label">Active</div>
            <div class="auto-stat-value"><?= count(array_filter($automations, fn($a) => $a['status'] === 'active')) ?></div>
        </div>
    </div>
    <div class="auto-stat-card">
        <div class="auto-stat-icon warning"><i class="bi bi-pause-circle"></i></div>
        <div>
            <div class="auto-stat-label">Inactive</div>
            <div class="auto-stat-value"><?= count(array_filter($automations, fn($a) => $a['status'] !== 'active')) ?></div>
        </div>
    </div>
    <div class="auto-stat-card">
        <div class="auto-stat-icon info"><i class="bi bi-envelope-paper"></i></div>
        <div>
            <div class="auto-stat-label">Templates</div>
            <div class="auto-stat-value"><?= count($templates) ?></div>
        </div>
    </div>
</div>

<?php if (empty($automations)): ?>
    <div class="auto-empty">
        <i class="bi bi-diagram-3-fill"></i>
        <p>No automated workflows yet. Click <strong>Create Workflow</strong> to get started!</p>
    </div>
<?php else: ?>
    <div class="auto-workflow-grid">
        <?php foreach ($automations as $aut): ?>
            <?php
                $steps = json_decode($aut['steps_json'] ?? '[]', true);
                $targetAudience = $aut['target_audience'] ?? 'all';
                foreach ($steps as $step) {
                    if (isset($step['_meta']['target_audience'])) {
                        $targetAudience = $step['_meta']['target_audience'];
                        break;
                    }
                }

                $audienceLabels = [
                    'all'                      => ['🌐 All Active', 'bg-success-subtle text-success'],
                    'all_emails'               => ['📧 All Emails', 'bg-primary-subtle text-primary'],
                    'category:subscriber'      => ['✅ Subscribers', 'bg-success-subtle text-success'],
                    'category:active_user'     => ['⚡ Active Users', 'bg-warning-subtle text-warning'],
                    'category:interested_user' => ['⭐ Interested', 'bg-info-subtle text-info'],
                    'category:exclusive_user'  => ['💎 Exclusive', 'bg-primary-subtle text-primary'],
                    'category:suspended'       => ['⏸ Suspended', 'bg-warning-subtle text-dark'],
                    'category:banned'          => ['🚫 Banned', 'bg-danger-subtle text-danger'],
                    'category:today'           => ["📅 Today's New", 'bg-info-subtle text-info'],
                ];
                $audInfo = $audienceLabels[$targetAudience] ?? [htmlspecialchars(ucwords(str_replace(['_',':'],' ',$targetAudience))), 'bg-secondary-subtle text-dark'];

                $t = $aut['trigger_event'];
                $triggerBadge = 'bg-secondary';
                $triggerLabel = $t;
                if ($t === 'user_signup') { $triggerBadge = 'bg-primary'; $triggerLabel = '🔔 User Registration'; }
                elseif ($t === 'order_placed') { $triggerBadge = 'bg-success'; $triggerLabel = '🛒 Order Placed'; }
                elseif ($t === 'new_product_published') { $triggerBadge = 'bg-warning text-dark'; $triggerLabel = '📦 New Product'; }
                elseif ($t === 'manual_broadcast') { $triggerBadge = 'bg-info text-dark'; $triggerLabel = '📢 Manual Broadcast'; }
            ?>
            <div class="auto-workflow-card">
                <div class="auto-card-header">
                    <div>
                        <div class="auto-card-title"><?= htmlspecialchars($aut['name']) ?></div>
                        <div class="text-muted small" style="font-size:0.75rem;">Created <?= date('Y-m-d', strtotime($aut['created_at'])) ?></div>
                    </div>
                    <div>
                        <?php if ($aut['status'] === 'active'): ?>
                            <span class="badge bg-success-subtle text-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="auto-card-body">
                    <div class="auto-meta-row">
                        <span class="auto-meta-label">Trigger</span>
                        <span class="badge <?= $triggerBadge ?>"><?= $triggerLabel ?></span>
                    </div>
                    <div class="auto-meta-row">
                        <span class="auto-meta-label">Audience</span>
                        <span class="badge <?= $audInfo[1] ?>"><?= $audInfo[0] ?></span>
                    </div>
                    <div class="auto-meta-row">
                        <span class="auto-meta-label">Emails Sent</span>
                        <span class="badge bg-light text-dark border"><i class="bi bi-envelope-fill me-1 text-success"></i> <?= (int)($aut['sent_count'] ?? 0) ?> Sent</span>
                    </div>
                    <div class="auto-steps">
                        <span class="auto-step-badge">🔔 Trigger</span>
                        <i class="bi bi-arrow-right auto-step-arrow"></i>
                        <?php foreach ($steps as $idx => $step): ?>
                            <?php if (isset($step['_meta'])) continue; ?>
                            <?php if ($step['action'] === 'wait'): ?>
                                <span class="auto-step-badge">⏱ Wait <?= $step['duration_hours'] ?>h</span>
                            <?php elseif ($step['action'] === 'send_email'): ?>
                                <?php
                                    $tempName = 'Email Design';
                                    foreach ($templates as $tp) {
                                        if ($tp['id'] == $step['template_id']) {
                                            $tempName = $tp['name'];
                                            break;
                                        }
                                    }
                                ?>
                                <span class="auto-step-badge" style="background:#eef2ff; color:#6366f1; border-color:#c7d2fe;">📧 <?= htmlspecialchars($tempName) ?></span>
                            <?php endif; ?>
                            <?php if ($idx < count($steps) - 1): ?>
                                <i class="bi bi-arrow-right auto-step-arrow"></i>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="auto-card-actions">
                    <button type="button" class="auto-btn" style="background:#eef2ff;color:#4f46e5;"
                        onclick="openAutomationAudiencePreview('<?= htmlspecialchars($targetAudience, ENT_QUOTES) ?>', 'Workflow target emails')">
                        <i class="bi bi-people"></i> View Emails
                    </button>
                    <?php if ($aut['trigger_event'] === 'manual_broadcast'): ?>
                        <a href="<?= url('admin/marketing/automations/broadcast/' . $aut['id']) ?>"
                           class="auto-btn auto-btn-primary"
                           onclick="return confirm('Send this broadcast now to the selected audience?')">
                            <i class="bi bi-send"></i> Send Now
                        </a>
                    <?php endif; ?>
                    <a href="<?= url('admin/marketing/automations/delete/' . $aut['id']) ?>"
                       class="auto-btn auto-btn-danger"
                       onclick="return confirm('Delete this automation workflow?')">
                        <i class="bi bi-trash"></i> Delete
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Modal: Create Workflow -->
<div class="modal fade auto-modal" id="createAutomationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?= url('admin/marketing/automations/store') ?>">
                <?= csrfField() ?>
                <div class="modal-header">
                    <div>
                        <h6 class="modal-title"><i class="bi bi-diagram-3-fill me-2"></i>Create Automated Drip Sequence</h6>
                        <p style="color: rgba(255,255,255,0.85); font-size:0.8rem; margin:4px 0 0;">Configure a trigger event, target audience and email template.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="dc-form-label">Workflow Name</label>
                                <input type="text" name="name" class="dc-form-control" required placeholder="e.g. New User Welcoming Drip">
                            </div>
                            <div class="form-group mb-3">
                                <label class="dc-form-label">Trigger System Event</label>
                                <select name="trigger_event" id="triggerEventSelect" class="dc-form-control" required onchange="onTriggerChange(this.value)">
                                    <option value="user_signup">🔔 On User Registration</option>
                                    <option value="order_placed">🛒 On Order Placed</option>
                                    <option value="order_delivered">Order Delivered</option>
                                    <option value="product_download">Product Download</option>
                                    <option value="password_reset">Password Reset Request</option>
                                    <option value="subscription_expiry">Subscription Expiry</option>
                                    <option value="custom_event">Custom Event</option>
                                    <option value="new_product_published">📦 On New Product Published</option>
                                    <option value="manual_broadcast">📢 Manual Broadcast</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="dc-form-label">Email Template</label>
                                <select name="template_id" class="dc-form-control" required>
                                    <option value="">-- Select Template --</option>
                                    <?php foreach ($templates as $tp): ?>
                                        <option value="<?= $tp['id'] ?>"><?= htmlspecialchars($tp['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-0">
                                <label class="dc-form-label">Status</label>
                                <select name="status" class="dc-form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="dc-form-label">Target Audience</label>
                                <select name="target_audience" id="targetAudienceSelect" class="dc-form-control" onchange="onAudienceChange(this.value)">
                                    <optgroup label="── All Subscribers ──">
                                        <option value="all">🌐 All Active Subscribers</option>
                                        <option value="all_emails">📧 All Emails (Total)</option>
                                    </optgroup>
                                    <optgroup label="── By Email Category ──">
                                        <option value="category:subscriber">✅ Subscribers Only</option>
                                        <option value="category:active_user">⚡ Active Users</option>
                                        <option value="category:interested_user">⭐ Interested Users</option>
                                        <option value="category:exclusive_user">💎 Exclusive / Paying Users</option>
                                        <option value="category:suspended">⏸ Suspended Users</option>
                                        <option value="category:banned">🚫 Banned Users</option>
                                        <option value="category:today">📅 Today's New Emails</option>
                                    </optgroup>
                                </select>
                                <div id="audienceInfoBox" class="auto-info-box">
                                    <i class="bi bi-info-circle me-1"></i>
                                    <span id="audienceInfoText">Emails will be sent to ALL active subscribers in the system.</span>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="previewSelectedAutomationAudience()">
                                    <i class="bi bi-people me-1"></i>View Target Emails
                                </button>
                            </div>
                            <div class="auto-info-box">
                                <strong><i class="bi bi-lightbulb-fill text-warning me-1"></i>How this trigger works:</strong>
                                <span id="triggerInfoText" style="display:block; margin-top:6px;">When a new user creates an account, this workflow fires and sends the selected email template to the configured audience.</span>
                            </div>
                            <div class="form-group mt-3 mb-0">
                                <label class="dc-form-label">SMTP Delivery Mode</label>
                                <select name="smtp_id" class="dc-form-control">
                                    <option value="">Auto-Rotate (Recommended)</option>
                                    <?php foreach ($smtps as $ch): ?>
                                        <option value="<?= $ch['id'] ?>">Only use: <?= htmlspecialchars($ch['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Create Workflow</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="automationAudiencePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4 pb-2">
                <div>
                    <h5 class="modal-title fw-bold"><i class="bi bi-people-fill text-primary me-2"></i>Target Email List</h5>
                    <p class="text-muted mb-0 mt-1" style="font-size:0.82rem;" id="automationAudiencePreviewSub">Loading recipients...</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3" id="automationAudiencePreviewBody"></div>
        </div>
    </div>
</div>

<script>
    const audienceNotes = {
        'all':                      'Emails will be sent to ALL active subscribers in the system.',
        'all_emails':               'Emails will be sent to every email address in the database (including non-active).',
        'category:subscriber':      'Targets only newsletter Subscribers (not yet registered users).',
        'category:active_user':     'Targets registered Active Users who have not purchased yet.',
        'category:interested_user': 'Targets Interested Users — showed buying intent (cart/failed order).',
        'category:exclusive_user':  'Targets Exclusive/Paying Users who have completed at least one purchase.',
        'category:suspended':       'Targets Suspended accounts — use with caution.',
        'category:banned':          'Targets Banned accounts — use with caution.',
        'category:today':           "Targets only today's newly added email addresses.",
    };

    const triggerNotes = {
        'user_signup':            'Fires when a new user creates an account. Sends the email to the selected audience immediately on registration.',
        'order_placed':           'Fires when a successful order/purchase is completed. Useful for thank-you and upsell drips.',
        'new_product_published':  'Fires when a new product is published on the store. Notify your audience of new arrivals.',
        'order_delivered':        'Fires when an order is marked completed or delivered.',
        'product_download':       'Fires when a customer starts downloading a purchased product.',
        'password_reset':         'Fires when a registered customer requests a password reset.',
        'subscription_expiry':    'Runs daily and fires on the support/subscription expiry date.',
        'custom_event':           'Can be fired by custom application code through MarketingSubscriber::triggerAutomation().',
        'manual_broadcast':       'Does NOT fire automatically. Use the "Send Now" button in the workflow list to manually trigger a one-time broadcast to the selected audience.',
    };

    function onAudienceChange(val) {
        const note = audienceNotes[val] || 'Emails will be sent to subscribers matching this audience filter.';
        document.getElementById('audienceInfoText').textContent = note;
    }

    function previewSelectedAutomationAudience() {
        openAutomationAudiencePreview(document.getElementById('targetAudienceSelect').value, 'Selected workflow target');
    }

    function openAutomationAudiencePreview(audience, label) {
        const modal = new bootstrap.Modal(document.getElementById('automationAudiencePreviewModal'));
        const body = document.getElementById('automationAudiencePreviewBody');
        document.getElementById('automationAudiencePreviewSub').textContent = label || 'Target email addresses';
        body.innerHTML = '<div class="text-center py-5"><span class="spinner-border text-primary"></span><p class="text-muted mt-2 mb-0">Loading email list...</p></div>';
        modal.show();

        fetch('<?= url('admin/marketing/audience-preview') ?>?' + new URLSearchParams({ audience: audience }))
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(result => {
                if (!result.ok || !result.data.success) throw new Error(result.data.error || 'Unable to load recipients.');
                const recipients = result.data.recipients || [];
                document.getElementById('automationAudiencePreviewSub').textContent = recipients.length + ' email(s) will be targeted';
                if (!recipients.length) {
                    body.innerHTML = '<div class="alert alert-warning mb-0">No valid emails match this target. Sync subscribers or choose another audience.</div>';
                    return;
                }
                const rows = recipients.map(item => '<tr><td>' + escapeAutomationAudienceHtml(item.name || '—') + '</td><td><a href="mailto:' + encodeURIComponent(item.email) + '">' + escapeAutomationAudienceHtml(item.email) + '</a></td><td><span class="badge bg-light text-dark border">' + escapeAutomationAudienceHtml(item.category || '—') + '</span></td></tr>').join('');
                body.innerHTML = '<div class="table-responsive" style="max-height:420px;"><table class="table table-sm align-middle mb-0"><thead><tr><th>Name</th><th>Email</th><th>Category</th></tr></thead><tbody>' + rows + '</tbody></table></div>';
            })
            .catch(error => { body.innerHTML = '<div class="alert alert-danger mb-0">' + escapeAutomationAudienceHtml(error.message) + '</div>'; });
    }

    function escapeAutomationAudienceHtml(value) {
        const element = document.createElement('div');
        element.textContent = String(value);
        return element.innerHTML;
    }

    function onTriggerChange(val) {
        const note = triggerNotes[val] || 'This trigger fires based on a system event.';
        document.getElementById('triggerInfoText').textContent = note;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const audSel = document.getElementById('targetAudienceSelect');
        const trigSel = document.getElementById('triggerEventSelect');
        if (audSel) onAudienceChange(audSel.value);
        if (trigSel) onTriggerChange(trigSel.value);
    });
</script>
