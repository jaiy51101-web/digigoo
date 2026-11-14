<!-- Marketing SMTP Accounts -->
<style>
.smtp-header {
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
.smtp-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}
.smtp-stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
}
.smtp-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.smtp-stat-icon.primary { background: #eef2ff; color: #6366f1; }
.smtp-stat-icon.success { background: #d1fae5; color: #059669; }
.smtp-stat-icon.warning { background: #fef3c7; color: #d97706; }
.smtp-stat-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #94a3b8;
    margin-bottom: 2px;
}
.smtp-stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
}
.smtp-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 16px;
}
.smtp-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.2s;
}
.smtp-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}
.smtp-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}
.smtp-card-title {
    font-weight: 700;
    color: #0f172a;
    font-size: 0.95rem;
    margin: 0;
}
.smtp-card-body {
    padding: 16px 20px;
}
.smtp-meta-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}
.smtp-meta-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #94a3b8;
    min-width: 70px;
}
.smtp-limit-bar {
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
    margin-top: 6px;
}
.smtp-limit-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s;
}
.smtp-card-actions {
    padding: 12px 20px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    gap: 8px;
    justify-content: flex-end;
    background: #fafbfc;
    flex-wrap: wrap;
}
.smtp-btn {
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
.smtp-btn-primary { background: #6366f1; color: #ffffff; }
.smtp-btn-primary:hover { background: #4f46e5; }
.smtp-btn-outline { background: transparent; color: #475569; border: 1px solid #e2e8f0; }
.smtp-btn-outline:hover { background: #f1f5f9; border-color: #cbd5e1; }
.smtp-btn-success { background: transparent; color: #059669; border: 1px solid #bbf7d0; }
.smtp-btn-success:hover { background: #f0fdf4; border-color: #059669; }
.smtp-btn-danger { background: transparent; color: #dc2626; border: 1px solid #fecaca; }
.smtp-btn-danger:hover { background: #fef2f2; border-color: #dc2626; }
.smtp-empty {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 60px 20px;
    text-align: center;
    color: #94a3b8;
}
.smtp-empty i { font-size: 3rem; margin-bottom: 16px; color: #cbd5e1; display: block; }
.smtp-provider-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.72rem;
    font-weight: 600;
}
</style>

<div class="smtp-header">
    <div>
        <h5 class="mb-1 fw-bold text-dark">Marketing SMTP Accounts</h5>
        <p class="text-muted mb-0 small">Manage SMTP channels, priority rotation, sending limits, and failover routing.</p>
    </div>
    <button class="btn btn-primary btn-sm fw-bold" onclick="processQueueNow(this)">
        <i class="bi bi-send-check me-1"></i>Process Email Queue
    </button>
</div>

<div class="smtp-stats">
    <div class="smtp-stat-card">
        <div class="smtp-stat-icon primary"><i class="bi bi-server"></i></div>
        <div>
            <div class="smtp-stat-label">Total Channels</div>
            <div class="smtp-stat-value"><?= count($smtps) ?></div>
        </div>
    </div>
    <div class="smtp-stat-card">
        <div class="smtp-stat-icon success"><i class="bi bi-check-circle"></i></div>
        <div>
            <div class="smtp-stat-label">Active</div>
            <div class="smtp-stat-value"><?= count(array_filter($smtps, fn($s) => $s['status'] === 'active')) ?></div>
        </div>
    </div>
    <div class="smtp-stat-card">
        <div class="smtp-stat-icon warning"><i class="bi bi-pause-circle"></i></div>
        <div>
            <div class="smtp-stat-label">Inactive</div>
            <div class="smtp-stat-value"><?= count(array_filter($smtps, fn($s) => $s['status'] !== 'active')) ?></div>
        </div>
    </div>
</div>

<?php if (empty($smtps)): ?>
    <div class="smtp-empty">
        <i class="bi bi-hdd-network"></i>
        <p>No SMTP accounts added yet. Add your first rotation channel below.</p>
    </div>
<?php else: ?>
    <div class="smtp-grid">
        <?php foreach ($smtps as $s): ?>
            <?php
                $prov = strtolower($s['provider']);
                $providerColors = [
                    'sendgrid' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'border' => '#bfdbfe'],
                    'mailgun' => ['bg' => '#fef3c7', 'text' => '#92400e', 'border' => '#fde68a'],
                    'brevo' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'border' => '#bfdbfe'],
                    'ses' => ['bg' => '#f3f4f6', 'text' => '#374151', 'border' => '#d1d5db'],
                    'postmark' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'border' => '#fecaca'],
                    'smtp2go' => ['bg' => '#d1fae5', 'text' => '#065f46', 'border' => '#a7f3d0'],
                ];
                $pColor = $providerColors[$prov] ?? ['bg' => '#f1f5f9', 'text' => '#475569', 'border' => '#e2e8f0'];
                $dailyPercent = $s['daily_limit'] > 0 ? min(100, round(($s['used_today'] / $s['daily_limit']) * 100)) : 0;
                $hourlyPercent = $s['hourly_limit'] > 0 ? min(100, round(($s['used_hour'] / $s['hourly_limit']) * 100)) : 0;
                $dailyColor = $dailyPercent > 80 ? '#dc2626' : ($dailyPercent > 50 ? '#f59e0b' : '#10b981');
                $hourlyColor = $hourlyPercent > 80 ? '#dc2626' : ($hourlyPercent > 50 ? '#f59e0b' : '#10b981');
            ?>
            <div class="smtp-card">
                <div class="smtp-card-header">
                    <div>
                        <div class="smtp-card-title"><?= htmlspecialchars($s['name']) ?></div>
                        <div class="text-muted small" style="font-size:0.75rem;"><?= htmlspecialchars($s['host']) ?>:<?= $s['port'] ?> (<?= strtoupper($s['encryption']) ?>)</div>
                    </div>
                    <?php if ($s['status'] === 'active'): ?>
                        <span class="badge bg-success-subtle text-success">Active</span>
                    <?php elseif ($s['status'] === 'inactive'): ?>
                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                    <?php else: ?>
                        <span class="badge bg-danger-subtle text-danger">Failed</span>
                    <?php endif; ?>
                </div>
                <div class="smtp-card-body">
                    <div class="smtp-meta-row">
                        <span class="smtp-meta-label">Provider</span>
                        <span class="smtp-provider-badge" style="background:<?= $pColor['bg'] ?>; color:<?= $pColor['text'] ?>; border:1px solid <?= $pColor['border'] ?>;">
                            <?= htmlspecialchars(ucfirst($s['provider'])) ?>
                        </span>
                    </div>
                    <div class="smtp-meta-row">
                        <span class="smtp-meta-label">From</span>
                        <span class="small text-dark"><?= htmlspecialchars($s['from_name']) ?> &lt;<?= htmlspecialchars($s['from_email']) ?>&gt;</span>
                    </div>
                    <div class="smtp-meta-row">
                        <span class="smtp-meta-label">Priority</span>
                        <span class="badge bg-light text-dark border"><?= $s['priority'] ?></span>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Daily: <strong class="text-dark"><?= $s['used_today'] ?><?= $s['daily_limit'] > 0 ? ' / '.$s['daily_limit'] : ' / ∞' ?></strong></span>
                            <span style="color:<?= $dailyColor ?>;"><?= $dailyPercent ?>%</span>
                        </div>
                        <div class="smtp-limit-bar">
                            <div class="smtp-limit-fill" style="width:<?= $dailyPercent ?>%; background:<?= $dailyColor ?>;"></div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Hourly: <strong class="text-dark"><?= $s['used_hour'] ?><?= $s['hourly_limit'] > 0 ? ' / '.$s['hourly_limit'] : ' / ∞' ?></strong></span>
                            <span style="color:<?= $hourlyColor ?>;"><?= $hourlyPercent ?>%</span>
                        </div>
                        <div class="smtp-limit-bar">
                            <div class="smtp-limit-fill" style="width:<?= $hourlyPercent ?>%; background:<?= $hourlyColor ?>;"></div>
                        </div>
                    </div>
                </div>
                <div class="smtp-card-actions">
                    <button class="smtp-btn smtp-btn-outline" onclick="openEditModal(<?= $s['id'] ?>)" title="Edit">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <button class="smtp-btn smtp-btn-success" onclick="testConnection(<?= $s['id'] ?>, this)" title="Ping">
                        <i class="bi bi-wifi"></i> Ping
                    </button>
                    <button class="smtp-btn smtp-btn-primary" onclick="openTestEmailModal(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['name'])) ?>')" title="Test Email">
                        <i class="bi bi-send"></i> Test
                    </button>
                    <a href="<?= url('admin/marketing/smtps/delete/' . $s['id']) ?>" class="smtp-btn smtp-btn-danger" onclick="return confirm('Delete this SMTP profile?')" title="Delete">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div id="smtpResultBox" class="d-none mt-3 p-3 rounded border" style="font-size:0.875rem;"></div>

<!-- Add SMTP Card -->
<div class="admin-card mt-4">
    <div class="card-header bg-light py-3">
        <h6 class="mb-0 fw-bold"><i class="bi bi-plus-square text-primary me-2"></i>Add SMTP Channel</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('admin/marketing/smtps/store') ?>">
            <?= csrfField() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="dc-form-label">Profile Account Name</label>
                        <input type="text" name="name" class="dc-form-control" required placeholder="e.g. SES Marketing Primary">
                    </div>
                    <div class="form-group mb-3">
                        <label class="dc-form-label">Provider Service</label>
                        <select name="provider" id="providerSelect" class="dc-form-control" onchange="applyProviderPreset(this.value)">
                            <option value="custom">Custom SMTP Server</option>
                            <option value="sendgrid">SendGrid Relay</option>
                            <option value="mailgun">Mailgun API</option>
                            <option value="brevo">Brevo (Sendinblue)</option>
                            <option value="ses">Amazon SES</option>
                            <option value="smtp2go">SMTP2GO</option>
                            <option value="postmark">Postmark</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="dc-form-label">SMTP Host</label>
                            <input type="text" name="host" id="smtpHost" class="dc-form-control" required placeholder="smtp.example.com">
                        </div>
                        <div class="col-4">
                            <label class="dc-form-label">Port</label>
                            <input type="number" name="port" id="smtpPort" class="dc-form-control" required value="587">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="dc-form-label">Username</label>
                        <input type="text" name="username" id="smtpUser" class="dc-form-control" placeholder="SMTP Username/API Key">
                    </div>
                    <div class="form-group mb-3">
                        <label class="dc-form-label">Password</label>
                        <div class="position-relative">
                            <input type="password" name="password" id="smtpPass" class="dc-form-control pe-5" placeholder="SMTP Password/Secret">
                            <button type="button" class="btn btn-sm position-absolute end-0 top-0 h-100 px-2 text-muted border-0 bg-transparent" onclick="togglePass()">
                                <i class="bi bi-eye" id="passEyeIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="dc-form-label">Encryption</label>
                            <select name="encryption" id="smtpEncryption" class="dc-form-control">
                                <option value="tls">TLS (STARTTLS)</option>
                                <option value="ssl">SSL (Implicit)</option>
                                <option value="none">None</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="dc-form-label">Priority (1=highest)</label>
                            <input type="number" name="priority" class="dc-form-control" required value="1" min="1" max="100">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="dc-form-label">Daily Limit</label>
                            <input type="number" name="daily_limit" class="dc-form-control" value="0">
                            <small class="text-muted" style="font-size:0.7rem;">0 = unlimited</small>
                        </div>
                        <div class="col-6">
                            <label class="dc-form-label">Hourly Limit</label>
                            <input type="number" name="hourly_limit" class="dc-form-control" value="0">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="dc-form-label">Sender Name ("From Name")</label>
                        <input type="text" name="from_name" class="dc-form-control" required placeholder="e.g. DigiWS Deals">
                    </div>
                    <div class="form-group mb-3">
                        <label class="dc-form-label">Sender Email ("From Email")</label>
                        <input type="email" name="from_email" class="dc-form-control" required placeholder="e.g. newsletter@yourdomain.com">
                    </div>
                    <div class="form-group mb-3">
                        <label class="dc-form-label">Status</label>
                        <select name="status" class="dc-form-control">
                            <option value="active">Active (Include in rotation)</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus me-1"></i>Add SMTP Profile</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit SMTP Modal -->
<div class="modal fade" id="editSmtpModal" tabindex="-1" aria-labelledby="editSmtpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4 pb-2">
                <div>
                    <h5 class="modal-title fw-bold" id="editSmtpModalLabel">
                        <i class="bi bi-pencil-square text-primary me-2"></i>Edit SMTP Profile
                    </h5>
                    <p class="text-muted mb-0 mt-1" style="font-size:0.82rem;">Update credentials without deleting and re-creating the profile.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div id="editSmtpResult" class="d-none alert mb-3"></div>
                <div class="alert alert-warning d-flex align-items-start gap-2 py-2 mb-3" style="font-size:0.8rem;">
                    <i class="bi bi-exclamation-triangle-fill text-warning mt-1"></i>
                    <div>
                        <strong>Auth failure 535? Common fixes:</strong><br>
                        &bull; <strong>Gmail</strong>: Use an <a href="https://myaccount.google.com/apppasswords" target="_blank">App Password</a> (not your Gmail password).<br>
                        &bull; <strong>cPanel hosting</strong>: Use the full email as username (e.g. <code>no-reply@yourdomain.com</code>).<br>
                        &bull; <strong>SendGrid</strong>: Username = <code>apikey</code>, Password = your API key.<br>
                        &bull; <strong>Outlook/M365</strong>: Enable SMTP AUTH in Microsoft 365 admin.
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-12"><label class="dc-form-label">Profile Name</label><input type="text" id="edit_name" class="dc-form-control" placeholder="Profile name"></div>
                    <div class="col-8"><label class="dc-form-label">SMTP Host</label><input type="text" id="edit_host" class="dc-form-control" placeholder="smtp.example.com"></div>
                    <div class="col-4"><label class="dc-form-label">Port</label><input type="number" id="edit_port" class="dc-form-control" value="587"></div>
                    <div class="col-6">
                        <label class="dc-form-label">Encryption</label>
                        <select id="edit_encryption" class="dc-form-control">
                            <option value="tls">TLS (STARTTLS) — Port 587</option>
                            <option value="ssl">SSL (Implicit) — Port 465</option>
                            <option value="none">None — Port 25</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="dc-form-label">Status</label>
                        <select id="edit_status" class="dc-form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-6"><label class="dc-form-label">Username</label><input type="text" id="edit_username" class="dc-form-control" placeholder="Full email or 'apikey'" autocomplete="off"></div>
                    <div class="col-6">
                        <label class="dc-form-label fw-semibold text-danger">New Password <small class="text-muted fw-normal">(leave blank to keep current)</small></label>
                        <div class="position-relative">
                            <input type="password" id="edit_password" class="dc-form-control pe-5" placeholder="New password / App Password" autocomplete="new-password">
                            <button type="button" class="btn btn-sm position-absolute end-0 top-0 h-100 px-2 text-muted border-0 bg-transparent" onclick="toggleEditPass()">
                                <i class="bi bi-eye" id="editPassEyeIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-6"><label class="dc-form-label">Sender Name</label><input type="text" id="edit_from_name" class="dc-form-control" placeholder="DigiWS Deals"></div>
                    <div class="col-6"><label class="dc-form-label">Sender Email</label><input type="email" id="edit_from_email" class="dc-form-control" placeholder="no-reply@digiws.com"></div>
                    <div class="col-4"><label class="dc-form-label">Daily Limit</label><input type="number" id="edit_daily_limit" class="dc-form-control" value="0"><small class="text-muted" style="font-size:0.7rem;">0 = unlimited</small></div>
                    <div class="col-4"><label class="dc-form-label">Hourly Limit</label><input type="number" id="edit_hourly_limit" class="dc-form-control" value="0"></div>
                    <div class="col-4"><label class="dc-form-label">Priority</label><input type="number" id="edit_priority" class="dc-form-control" value="1" min="1" max="100"></div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm px-4" id="saveSmtpEditBtn" onclick="saveSmtpEdit()">
                    <span id="saveSmtpBtnText"><i class="bi bi-floppy me-1"></i>Save Changes</span>
                    <span id="saveSmtpBtnSpinner" class="d-none"><span class="spinner-border spinner-border-sm me-1"></span>Saving...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1" aria-labelledby="testEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div>
                    <h5 class="modal-title fw-bold" id="testEmailModalLabel">
                        <i class="bi bi-send text-primary me-2"></i>Send Live Test Email
                    </h5>
                    <p class="text-muted mb-0 mt-1" style="font-size:0.83rem;" id="testEmailSmtpLabel">—</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div id="testEmailResult" class="d-none alert mb-3" role="alert"></div>
                <div class="form-group mb-3">
                    <label class="dc-form-label">Recipient Email Address</label>
                    <input type="email" id="testEmailInput" class="dc-form-control" placeholder="Enter your email to receive the test..." autocomplete="email">
                    <small class="text-muted" style="font-size:0.75rem;">A professionally designed test email will be delivered to this address.</small>
                </div>
                <div class="p-3 rounded-3 border" style="background:#f8fafc; font-size:0.8rem; color:#64748b;">
                    <div class="fw-semibold text-dark mb-1">📧 Test Email Preview:</div>
                    <div>Subject: <span class="text-primary fw-bold">[DigiWS] SMTP Test Email</span></div>
                    <div class="mt-1">Content: HTML branded test with SMTP profile info, connection details, timestamp.</div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm px-4" id="sendTestEmailBtn" onclick="sendTestEmail()">
                    <span id="sendTestBtnText"><i class="bi bi-send me-1"></i>Send Test Email</span>
                    <span id="sendTestBtnSpinner" class="d-none"><span class="spinner-border spinner-border-sm me-1"></span>Sending...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let activeSmtpId = null;

    function applyProviderPreset(provider) {
        const presets = {
            sendgrid : { host: 'smtp.sendgrid.net',                       port: 587, enc: 'tls', user: 'apikey' },
            mailgun  : { host: 'smtp.mailgun.org',                         port: 587, enc: 'tls', user: '' },
            brevo    : { host: 'smtp-relay.brevo.com',                     port: 587, enc: 'tls', user: '' },
            ses      : { host: 'email-smtp.us-east-1.amazonaws.com',       port: 587, enc: 'tls', user: '' },
            smtp2go  : { host: 'mail.smtp2go.com',                         port: 587, enc: 'tls', user: '' },
            postmark : { host: 'smtp.postmarkapp.com',                     port: 587, enc: 'tls', user: '' },
            custom   : { host: '',                                          port: 587, enc: 'tls', user: '' }
        };
        const p = presets[provider] || presets.custom;
        document.getElementById('smtpHost').value       = p.host;
        document.getElementById('smtpPort').value       = p.port;
        document.getElementById('smtpEncryption').value = p.enc;
        document.getElementById('smtpUser').value       = p.user;
    }

    function togglePass() {
        const input = document.getElementById('smtpPass');
        const icon  = document.getElementById('passEyeIcon');
        if (input.type === 'password') { input.type = 'text'; icon.className = 'bi bi-eye-slash'; }
        else { input.type = 'password'; icon.className = 'bi bi-eye'; }
    }

    function showResult(success, message) {
        const box = document.getElementById('smtpResultBox');
        box.className = 'd-block mt-3 p-3 rounded border';
        box.style.background = success ? '#f0fdf4' : '#fef2f2';
        box.style.borderColor = success ? '#bbf7d0' : '#fecaca';
        box.innerHTML = '<span style="color:' + (success ? '#16a34a' : '#dc2626') + '; font-weight:600;">' + (success ? '✅ ' : '❌ ') + message + '</span>';
        box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        setTimeout(() => { box.className = 'd-none'; }, 7000);
    }

    function testConnection(smtpId, btn) {
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        fetch('<?= url('admin/marketing/smtps/test') ?>/' + smtpId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => { showResult(data.success, data.success ? data.message : data.error); })
        .catch(err => showResult(false, 'Request failed: ' + err))
        .finally(() => { btn.disabled = false; btn.innerHTML = origHtml; });
    }

    function openTestEmailModal(smtpId, smtpName) {
        activeSmtpId = smtpId;
        document.getElementById('testEmailSmtpLabel').textContent = 'SMTP: ' + smtpName;
        document.getElementById('testEmailInput').value = '';
        document.getElementById('testEmailResult').className = 'd-none alert';
        document.getElementById('testEmailResult').innerHTML = '';
        const modal = new bootstrap.Modal(document.getElementById('testEmailModal'));
        modal.show();
    }

    function sendTestEmail() {
        const email = document.getElementById('testEmailInput').value.trim();
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showModalResult(false, 'Please enter a valid email address.');
            return;
        }
        const btn = document.getElementById('sendTestEmailBtn');
        const btnText = document.getElementById('sendTestBtnText');
        const spinner = document.getElementById('sendTestBtnSpinner');
        btn.disabled = true; btnText.classList.add('d-none'); spinner.classList.remove('d-none');
        const formData = new FormData();
        formData.append('test_email', email);
        formData.append('csrf_token', '<?= csrfToken() ?>');
        fetch('<?= url('admin/marketing/smtps/send-test') ?>/' + activeSmtpId, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.text().then(text => { try { return JSON.parse(text); } catch (e) { throw new Error('Server returned non-JSON: ' + text.substring(0, 200)); } }))
        .then(data => { showModalResult(data.success, data.success ? data.message : data.error); })
        .catch(err => showModalResult(false, 'Error: ' + err.message))
        .finally(() => { btn.disabled = false; btnText.classList.remove('d-none'); spinner.classList.add('d-none'); });
    }

    function showModalResult(success, message) {
        const el = document.getElementById('testEmailResult');
        el.className = 'alert ' + (success ? 'alert-success' : 'alert-danger');
        el.innerHTML = (success ? '✅ ' : '❌ ') + message;
    }

    function processQueueNow(btn) {
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Processing...';
        fetch('<?= url('admin/marketing/queue/process') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text().then(text => { try { return JSON.parse(text); } catch (e) { throw new Error('Non-JSON: ' + text.substring(0, 200)); } }))
        .then(data => {
            const r = data.result || {};
            showResult(true, 'Queue processed: Sent ' + (r.success || 0) + ', Failed ' + (r.failed || 0) + '. ' + (r.message || ''));
        })
        .catch(err => showResult(false, 'Queue error: ' + err.message))
        .finally(() => { btn.disabled = false; btn.innerHTML = origHtml; });
    }

    let activeEditSmtpId = null;
    function openEditModal(smtpId) {
        activeEditSmtpId = smtpId;
        document.getElementById('editSmtpResult').className = 'd-none alert';
        document.getElementById('editSmtpResult').innerHTML = '';
        document.getElementById('edit_password').value = '';
        fetch('<?= url('admin/marketing/smtps/edit') ?>/' + smtpId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) { alert('Could not load SMTP: ' + data.error); return; }
            const s = data.smtp;
            document.getElementById('edit_name').value         = s.name        || '';
            document.getElementById('edit_host').value         = s.host        || '';
            document.getElementById('edit_port').value         = s.port        || 587;
            document.getElementById('edit_username').value     = s.username    || '';
            document.getElementById('edit_from_name').value    = s.from_name   || '';
            document.getElementById('edit_from_email').value   = s.from_email  || '';
            document.getElementById('edit_daily_limit').value  = s.daily_limit || 0;
            document.getElementById('edit_hourly_limit').value = s.hourly_limit|| 0;
            document.getElementById('edit_priority').value     = s.priority    || 1;
            document.getElementById('edit_encryption').value   = s.encryption  || 'tls';
            document.getElementById('edit_status').value       = s.status      || 'active';
            const modal = new bootstrap.Modal(document.getElementById('editSmtpModal'));
            modal.show();
        })
        .catch(err => alert('Failed to load SMTP profile: ' + err));
    }

    function toggleEditPass() {
        const input = document.getElementById('edit_password');
        const icon  = document.getElementById('editPassEyeIcon');
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
    }

    function saveSmtpEdit() {
        const btn = document.getElementById('saveSmtpEditBtn');
        const btnText = document.getElementById('saveSmtpBtnText');
        const spinner = document.getElementById('saveSmtpBtnSpinner');
        btn.disabled = true; btnText.classList.add('d-none'); spinner.classList.remove('d-none');
        const formData = new FormData();
        formData.append('csrf_token',   '<?= csrfToken() ?>');
        formData.append('name',         document.getElementById('edit_name').value);
        formData.append('host',         document.getElementById('edit_host').value);
        formData.append('port',         document.getElementById('edit_port').value);
        formData.append('username',     document.getElementById('edit_username').value);
        formData.append('password',     document.getElementById('edit_password').value);
        formData.append('from_name',    document.getElementById('edit_from_name').value);
        formData.append('from_email',   document.getElementById('edit_from_email').value);
        formData.append('daily_limit',  document.getElementById('edit_daily_limit').value);
        formData.append('hourly_limit', document.getElementById('edit_hourly_limit').value);
        formData.append('priority',     document.getElementById('edit_priority').value);
        formData.append('encryption',   document.getElementById('edit_encryption').value);
        formData.append('status',       document.getElementById('edit_status').value);
        fetch('<?= url('admin/marketing/smtps/update') ?>/' + activeEditSmtpId, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.text().then(text => { try { return JSON.parse(text); } catch (e) { throw new Error('Non-JSON: ' + text.substring(0, 300)); } }))
        .then(data => {
            const el = document.getElementById('editSmtpResult');
            el.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
            el.innerHTML = (data.success ? '✅ ' : '❌ ') + (data.message || data.error);
            if (data.success) setTimeout(() => { location.reload(); }, 1200);
        })
        .catch(err => {
            const el = document.getElementById('editSmtpResult');
            el.className = 'alert alert-danger';
            el.innerHTML = '❌ Error: ' + err.message;
        })
        .finally(() => { btn.disabled = false; btnText.classList.remove('d-none'); spinner.classList.add('d-none'); });
    }
</script>
