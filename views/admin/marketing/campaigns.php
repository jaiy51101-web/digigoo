<!-- Campaigns Management Dashboard -->
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h5 class="mb-1 fw-bold text-dark">Campaigns & Email Delivery</h5>
        <p class="text-muted mb-0" style="font-size:0.875rem;">Design bulk newsletters, retry failed emails, and send to individual users.</p>
    </div>
    <div>
        <button type="button" class="btn-dc-primary btn-dc-sm" data-bs-toggle="modal" data-bs-target="#createCampaignModal">
            <i class="bi bi-plus-lg me-1"></i>Create New Campaign
        </button>
    </div>
</div>

<div class="admin-card">
    <div class="card-header bg-light py-3">
        <h6 class="mb-0 fw-bold"><i class="bi bi-envelope-check text-primary me-2"></i>Marketing Campaigns</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                <thead>
                    <tr>
                        <th>Campaign / Subject</th>
                        <th>Audience / Template</th>
                        <th>Delivery Stats</th>
                        <th>Opens / Clicks</th>
                        <th>Status</th>
                        <th style="min-width:230px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($campaigns)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-envelope-open fs-1 mb-2 text-secondary d-block"></i>
                                No campaigns yet. Create one above!
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($campaigns as $camp): ?>
                            <?php
                                $openRate  = $camp['total_subscribers'] > 0 ? round(($camp['open_count']  / $camp['total_subscribers']) * 100, 1) : 0;
                                $clickRate = $camp['total_subscribers'] > 0 ? round(($camp['click_count'] / $camp['total_subscribers']) * 100, 1) : 0;
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($camp['name']) ?></div>
                                    <small class="text-muted">Subject: &ldquo;<?= htmlspecialchars($camp['subject']) ?>&rdquo;</small>
                                </td>
                                <td>
                                    <div style="font-size:0.82rem;">
                                        <?php
                                            $targetLabels = [
                                                'all'                      => ['🌐 All Active', 'bg-success-subtle text-success'],
                                                'all_emails'               => ['📧 All Emails', 'bg-primary-subtle text-primary'],
                                                'category:subscriber'      => ['✅ Subscribers', 'bg-success-subtle text-success'],
                                                'category:active_user'     => ['⚡ Active Users', 'bg-warning-subtle text-warning'],
                                                'category:interested_user' => ['⭐ Interested', 'bg-info-subtle text-info'],
                                                'category:exclusive_user'  => ['💎 Exclusive', 'bg-primary-subtle text-primary'],
                                                'category:suspended'       => ['⏸ Suspended', 'bg-warning-subtle text-dark'],
                                                'category:banned'          => ['🚫 Banned', 'bg-danger-subtle text-danger'],
                                                'category:today'           => ["📅 Today's New", 'bg-info-subtle text-info'],
                                                'list'                     => ['📁 Mailing List', 'bg-secondary-subtle text-dark'],
                                                'segment'                  => ['🔀 Segment', 'bg-purple-subtle text-dark'],
                                            ];
                                            $tkey = $camp['target_type'];
                                            $tinfo = $targetLabels[$tkey] ?? [htmlspecialchars(ucwords(str_replace(['_',':'],' ',$tkey))), 'bg-secondary-subtle text-dark'];
                                        ?>
                                        Target: <span class="badge <?= $tinfo[1] ?>"><?= $tinfo[0] ?></span>
                                    </div>
                                    <small class="text-muted">Template: <?= htmlspecialchars($camp['template_name'] ?? 'Custom Layout') ?></small>
                                </td>
                                <td>
                                    <div style="font-size:0.82rem;">Queued: <strong><?= number_format($camp['total_subscribers']) ?></strong></div>
                                    <small class="text-success">✔ Sent: <?= number_format($camp['sent_count']) ?></small>
                                </td>
                                <td>
                                    <div style="font-size:0.82rem;">Opens: <strong><?= $openRate ?>%</strong> (<?= $camp['open_count'] ?>)</div>
                                    <div style="font-size:0.82rem;">Clicks: <strong><?= $clickRate ?>%</strong> (<?= $camp['click_count'] ?>)</div>
                                </td>
                                <td>
                                    <?php if ($camp['status'] === 'draft'): ?>
                                        <span class="badge bg-secondary">Draft</span>
                                    <?php elseif ($camp['status'] === 'scheduled'): ?>
                                        <span class="badge bg-info text-white">Scheduled</span>
                                    <?php elseif ($camp['status'] === 'sending'): ?>
                                        <span class="spinner-border spinner-border-sm text-warning me-1" style="width:10px;height:10px;"></span>
                                        <span class="badge bg-warning text-dark">Sending</span>
                                    <?php elseif ($camp['status'] === 'completed'): ?>
                                        <span class="badge bg-success">Completed</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger text-capitalize"><?= htmlspecialchars($camp['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">

                                        <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2"
                                            style="font-size:0.72rem;"
                                            onclick="openAudiencePreview('<?= htmlspecialchars($camp['target_type'], ENT_QUOTES) ?>', <?= (int)($camp['target_id'] ?? 0) ?>, 'Campaign target emails')"
                                            title="View targeted email addresses">
                                            <i class="bi bi-people me-1"></i>Emails
                                        </button>

                                        <?php if ($camp['status'] === 'draft'): ?>
                                        <!-- First-time Send -->
                                        <a href="<?= url('admin/marketing/campaigns/send/' . $camp['id']) ?>"
                                            class="btn btn-sm btn-primary py-0 px-2 text-decoration-none"
                                            style="font-size:0.72rem;"
                                            onclick="return confirm('Schedule and send this campaign to all subscribers?')">
                                            <i class="bi bi-send me-1"></i>Send
                                        </a>
                                        <?php endif; ?>

                                        <!-- View Stats + Resend Options -->
                                        <button class="btn btn-sm btn-outline-info py-0 px-2"
                                            style="font-size:0.72rem;"
                                            onclick="openStatsModal(<?= $camp['id'] ?>, '<?= htmlspecialchars(addslashes($camp['name'])) ?>')"
                                            title="View delivery stats & resend options">
                                            <i class="bi bi-bar-chart me-1"></i>Stats
                                        </button>

                                        <!-- Resend Failed -->
                                        <a href="<?= url('admin/marketing/campaigns/resend-failed/' . $camp['id']) ?>"
                                            class="btn btn-sm btn-outline-warning py-0 px-2 text-decoration-none"
                                            style="font-size:0.72rem;"
                                            title="Retry only failed emails"
                                            onclick="return confirm('Retry all FAILED emails in this campaign?')">
                                            <i class="bi bi-arrow-repeat me-1"></i>Retry Failed
                                        </a>

                                        <!-- Send to Single User -->
                                        <button class="btn btn-sm btn-outline-success py-0 px-2"
                                            style="font-size:0.72rem;"
                                            onclick="openSendOneModal(<?= $camp['id'] ?>, '<?= htmlspecialchars(addslashes($camp['name'])) ?>', '<?= htmlspecialchars(addslashes($camp['subject'])) ?>')"
                                            title="Send this campaign to a specific email">
                                            <i class="bi bi-person-check me-1"></i>Send to User
                                        </button>

                                        <!-- Resend All (dropdown) -->
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary py-0 px-2 dropdown-toggle"
                                                style="font-size:0.72rem;"
                                                data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" style="font-size:0.82rem; min-width:170px;">
                                                <li>
                                                    <a class="dropdown-item text-danger"
                                                        href="<?= url('admin/marketing/campaigns/resend-all/' . $camp['id']) ?>"
                                                        onclick="return confirm('Resend ALL emails (including already sent) in this campaign?')">
                                                        <i class="bi bi-send-arrow-up me-2"></i>Resend Entire Campaign
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger"
                                                        href="<?= url('admin/marketing/campaigns/delete/' . $camp['id']) ?>"
                                                        onclick="return confirm('Permanently delete this campaign?')">
                                                        <i class="bi bi-trash me-2"></i>Delete Campaign
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════ -->
<!-- MODAL: Create Campaign                                       -->
<!-- ════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="createCampaignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST" action="<?= url('admin/marketing/campaigns/store') ?>">
                <?= csrfField() ?>
                <div class="modal-header border-0 pt-4 px-4">
                    <h6 class="modal-title fw-bold"><i class="bi bi-megaphone-fill text-primary me-2"></i>Create New Mailing Campaign</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pt-2">
                    <div class="form-group mb-3">
                        <label class="dc-form-label">Campaign Admin Name</label>
                        <input type="text" name="name" class="dc-form-control" required placeholder="e.g. Cyber Monday Deal Alert 2026">
                    </div>
                    <div class="form-group mb-3">
                        <label class="dc-form-label">Email Subject Line</label>
                        <input type="text" name="subject" class="dc-form-control" required placeholder="e.g. 🔥 Cyber Monday Sale: Get 40% OFF!">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="dc-form-label">From Name</label>
                            <input type="text" name="from_name" class="dc-form-control" required placeholder="DigiWS Deals">
                        </div>
                        <div class="col-6">
                            <label class="dc-form-label">From Email</label>
                            <input type="email" name="from_email" class="dc-form-control" required placeholder="deals@digiws.com">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="dc-form-label">Select Email Template</label>
                        <select name="template_id" class="dc-form-control" required>
                            <option value="">-- Choose Template --</option>
                            <?php foreach ($templates as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="dc-form-label">Target Audience</label>
                        <select name="target_type" id="targetTypeSelect" class="dc-form-control" onchange="toggleAudienceDropdowns(this.value)">
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
                            <optgroup label="── Custom Targeting ──">
                                <option value="list">📁 Static Mailing List</option>
                                <option value="segment">🔀 Dynamic Segment</option>
                            </optgroup>
                        </select>
                        <div id="audienceCategoryNote" class="mt-2 d-none">
                            <div class="p-2 rounded-2 border" style="background:#eff6ff; font-size:0.78rem; color:#1d4ed8;">
                                <i class="bi bi-info-circle me-1"></i>
                                <span id="audienceCategoryNoteText">Emails will be sent to all subscribers matching this category.</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="previewSelectedCampaignAudience()">
                            <i class="bi bi-people me-1"></i>View Target Emails
                        </button>
                    </div>
                    <div class="row g-2 mb-3" id="customTargetRow">
                        <div class="col-12">
                            <input type="hidden" name="target_id" id="campaignTargetId" value="">
                            <div id="listTargetGroup" class="d-none">
                                <label class="dc-form-label">Select Mailing List</label>
                                <select id="campaignListTarget" class="dc-form-control" onchange="syncCampaignTargetId()">
                                    <?php foreach ($lists as $l): ?>
                                        <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['name']) ?> (<?= $l['subscriber_count'] ?> contacts)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div id="segmentTargetGroup" class="d-none">
                                <label class="dc-form-label">Select Dynamic Segment</label>
                                <select id="campaignSegmentTarget" class="dc-form-control" onchange="syncCampaignTargetId()">
                                    <?php foreach ($segments as $s): ?>
                                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="dc-form-label">SMTP Delivery Mode</label>
                        <select name="smtp_id" class="dc-form-control">
                            <option value="">Auto-Rotate (Recommended)</option>
                            <?php foreach ($smtps as $ch): ?>
                                <option value="<?= $ch['id'] ?>">Only use: <?= htmlspecialchars($ch['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mt-3 mb-0">
                        <label class="dc-form-label">Schedule Delivery <span class="text-muted fw-normal">(optional)</span></label>
                        <input type="datetime-local" name="scheduled_at" class="dc-form-control" min="<?= date('Y-m-d\\TH:i') ?>">
                        <small class="text-muted">Leave blank to queue the campaign for the next worker run.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Create Campaign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════ -->
<!-- Modal: Target Email List -->
<div class="modal fade" id="audiencePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4 pb-2">
                <div>
                    <h5 class="modal-title fw-bold"><i class="bi bi-people-fill text-primary me-2"></i>Target Email List</h5>
                    <p class="text-muted mb-0 mt-1" style="font-size:0.82rem;" id="audiencePreviewSub">Loading recipients...</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3" id="audiencePreviewBody"></div>
        </div>
    </div>
</div>

<!-- MODAL: Campaign Delivery Stats + Resend Controls             -->
<!-- ════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="statsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4 pb-2">
                <div>
                    <h5 class="modal-title fw-bold" id="statsModalTitle">
                        <i class="bi bi-bar-chart-line text-primary me-2"></i>Campaign Stats
                    </h5>
                    <p class="text-muted mb-0 mt-1" style="font-size:0.82rem;" id="statsModalSub">Delivery details and retry options</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3" id="statsModalBody">
                <div class="text-center py-5">
                    <span class="spinner-border text-primary"></span>
                    <p class="text-muted mt-2 mb-0">Loading campaign stats...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════ -->
<!-- MODAL: Send to Single User                                   -->
<!-- ════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="sendOneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pt-4 px-4 pb-2">
                <div>
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-person-check text-success me-2"></i>Send to Specific User
                    </h5>
                    <p class="text-muted mb-0 mt-1" style="font-size:0.82rem;" id="sendOneCampaignLabel">Campaign: —</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div id="sendOneResult" class="d-none alert mb-3"></div>

                <div class="p-3 mb-3 rounded-3 border" style="background:#f8fafc; font-size:0.8rem; color:#64748b;">
                    <strong>📧 Subject:</strong> <span id="sendOneSubjectLabel" class="text-primary fw-semibold"></span><br>
                    <span class="mt-1 d-block">The campaign template will be personalized with this recipient's name and sent immediately.</span>
                </div>

                <div class="form-group mb-3">
                    <label class="dc-form-label fw-semibold">Recipient Name</label>
                    <input type="text" id="sendOneName" class="dc-form-control" placeholder="e.g. Rahul Sharma (optional)">
                </div>
                <div class="form-group mb-0">
                    <label class="dc-form-label fw-semibold">Recipient Email Address <span class="text-danger">*</span></label>
                    <input type="email" id="sendOneEmail" class="dc-form-control" placeholder="e.g. customer@example.com">
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success btn-sm px-4" id="sendOneBtn" onclick="submitSendOne()">
                    <span id="sendOneBtnText"><i class="bi bi-send me-1"></i>Send Email Now</span>
                    <span id="sendOneBtnSpinner" class="d-none"><span class="spinner-border spinner-border-sm me-1"></span>Sending...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let activeCampaignId = null;

    // ── Toggle audience dropdowns ─────────────────────────────────
    const categoryNotes = {
        'all':                      'Emails will be sent to ALL active subscribers in the system.',
        'all_emails':               'Emails will be sent to every email address in the database (including non-active).',
        'category:subscriber':      'Targets only newsletter Subscribers (not yet registered users).',
        'category:active_user':     'Targets registered Active Users who have not purchased yet.',
        'category:interested_user': 'Targets Interested Users — showed buying intent (cart/failed order).',
        'category:exclusive_user':  'Targets Exclusive/Paying Users who have completed at least one purchase.',
        'category:suspended':       'Targets Suspended accounts — use with caution.',
        'category:banned':          'Targets Banned accounts — use with caution.',
        'category:today':           "Targets only today's newly added email addresses.",
        'list':                     'Targets subscribers in a specific static mailing list.',
        'segment':                  'Targets subscribers matching a saved dynamic segment filter.',
    };

    function toggleAudienceDropdowns(type) {
        document.getElementById('listTargetGroup').classList.toggle('d-none', type !== 'list');
        document.getElementById('segmentTargetGroup').classList.toggle('d-none', type !== 'segment');
        syncCampaignTargetId();

        const noteBox  = document.getElementById('audienceCategoryNote');
        const noteText = document.getElementById('audienceCategoryNoteText');
        const note = categoryNotes[type];
        if (note) {
            noteText.textContent = note;
            noteBox.classList.remove('d-none');
        } else {
            noteBox.classList.add('d-none');
        }
    }
    // Trigger on page load to show note for default option
    document.addEventListener('DOMContentLoaded', function() {
        const sel = document.getElementById('targetTypeSelect');
        if (sel) toggleAudienceDropdowns(sel.value);
    });

    function previewSelectedCampaignAudience() {
        const audience = document.getElementById('targetTypeSelect').value;
        const targetId = document.getElementById('campaignTargetId').value || 0;
        openAudiencePreview(audience, targetId, 'Selected campaign target');
    }

    function syncCampaignTargetId() {
        const audience = document.getElementById('targetTypeSelect')?.value;
        const targetInput = document.getElementById('campaignTargetId');
        if (!targetInput) return;
        if (audience === 'list') targetInput.value = document.getElementById('campaignListTarget')?.value || '';
        else if (audience === 'segment') targetInput.value = document.getElementById('campaignSegmentTarget')?.value || '';
        else targetInput.value = '';
    }

    function openAudiencePreview(audience, targetId, label) {
        const modal = new bootstrap.Modal(document.getElementById('audiencePreviewModal'));
        const body = document.getElementById('audiencePreviewBody');
        document.getElementById('audiencePreviewSub').textContent = label || 'Target email addresses';
        body.innerHTML = '<div class="text-center py-5"><span class="spinner-border text-primary"></span><p class="text-muted mt-2 mb-0">Loading email list...</p></div>';
        modal.show();

        const params = new URLSearchParams({ audience: audience, target_id: targetId || 0 });
        fetch('<?= url('admin/marketing/audience-preview') ?>?' + params.toString())
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(result => {
                if (!result.ok || !result.data.success) throw new Error(result.data.error || 'Unable to load recipients.');
                const recipients = result.data.recipients || [];
                document.getElementById('audiencePreviewSub').textContent = recipients.length + ' email(s) will be targeted';
                if (!recipients.length) {
                    body.innerHTML = '<div class="alert alert-warning mb-0">No valid emails match this target. Sync subscribers or choose another audience.</div>';
                    return;
                }
                const rows = recipients.map(item => '<tr><td>' + escapeAudienceHtml(item.name || '—') + '</td><td><a href="mailto:' + encodeURIComponent(item.email) + '">' + escapeAudienceHtml(item.email) + '</a></td><td><span class="badge bg-light text-dark border">' + escapeAudienceHtml(item.category || '—') + '</span></td></tr>').join('');
                body.innerHTML = '<div class="table-responsive" style="max-height:420px;"><table class="table table-sm align-middle mb-0"><thead><tr><th>Name</th><th>Email</th><th>Category</th></tr></thead><tbody>' + rows + '</tbody></table></div>';
            })
            .catch(error => { body.innerHTML = '<div class="alert alert-danger mb-0">' + escapeAudienceHtml(error.message) + '</div>'; });
    }

    function escapeAudienceHtml(value) {
        const element = document.createElement('div');
        element.textContent = String(value);
        return element.innerHTML;
    }

    // ── Campaign Stats Modal ──────────────────────────────────────
    function openStatsModal(campId, campName) {
        activeCampaignId = campId;
        document.getElementById('statsModalTitle').innerHTML =
            '<i class="bi bi-bar-chart-line text-primary me-2"></i>' + campName;
        document.getElementById('statsModalSub').textContent = 'Live delivery stats & retry controls';
        document.getElementById('statsModalBody').innerHTML =
            '<div class="text-center py-5"><span class="spinner-border text-primary"></span><p class="text-muted mt-2 mb-0">Loading...</p></div>';

        const modal = new bootstrap.Modal(document.getElementById('statsModal'));
        modal.show();

        fetch('<?= url('admin/marketing/campaigns/stats') ?>/' + campId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text().then(t => { try { return JSON.parse(t); } catch(e) { throw new Error(t.substring(0,200)); } }))
        .then(data => {
            if (!data.success) { renderStatsError(data.error); return; }
            renderStats(data);
        })
        .catch(err => renderStatsError('Error: ' + err.message));
    }

    function renderStatsError(msg) {
        document.getElementById('statsModalBody').innerHTML =
            '<div class="alert alert-danger m-3">❌ ' + msg + '</div>';
    }

    function renderStats(data) {
        const s = data.stats || {};
        const total   = parseInt(s.total   || 0);
        const sent    = parseInt(s.sent    || 0);
        const failed  = parseInt(s.failed  || 0);
        const pending = parseInt(s.pending || 0);
        const sentPct = total > 0 ? Math.round((sent/total)*100) : 0;
        const failPct = total > 0 ? Math.round((failed/total)*100) : 0;

        let failTable = '';
        if (data.failures && data.failures.length > 0) {
            failTable = `
            <div class="mt-3">
                <h6 class="fw-bold text-danger mb-2" style="font-size:0.85rem;"><i class="bi bi-x-circle me-1"></i>Failed Emails (Last 50)</h6>
                <div class="table-responsive" style="max-height:200px; overflow-y:auto;">
                <table class="table table-sm align-middle mb-0" style="font-size:0.78rem;">
                    <thead class="table-light">
                        <tr><th>Email</th><th>Reason</th><th>Attempts</th></tr>
                    </thead>
                    <tbody>` +
                    data.failures.map(f => `<tr>
                        <td class="text-primary">${f.email}</td>
                        <td class="text-danger" style="max-width:280px; word-break:break-word;">${(f.error_message || 'Unknown').substring(0,120)}</td>
                        <td>${f.attempts}</td>
                    </tr>`).join('') +
                    `</tbody></table></div></div>`;
        }

        document.getElementById('statsModalBody').innerHTML = `
        <div class="row g-3 mb-3">
            <div class="col-3">
                <div class="text-center p-3 rounded-3 border" style="background:#f8fafc;">
                    <div class="fw-bold fs-4 text-dark">${total.toLocaleString()}</div>
                    <small class="text-muted">Total Queued</small>
                </div>
            </div>
            <div class="col-3">
                <div class="text-center p-3 rounded-3 border" style="background:#f0fdf4;">
                    <div class="fw-bold fs-4 text-success">${sent.toLocaleString()}</div>
                    <small class="text-muted">✔ Sent (${sentPct}%)</small>
                </div>
            </div>
            <div class="col-3">
                <div class="text-center p-3 rounded-3 border" style="background:#fef2f2;">
                    <div class="fw-bold fs-4 text-danger">${failed.toLocaleString()}</div>
                    <small class="text-muted">✖ Failed (${failPct}%)</small>
                </div>
            </div>
            <div class="col-3">
                <div class="text-center p-3 rounded-3 border" style="background:#eff6ff;">
                    <div class="fw-bold fs-4 text-primary">${pending.toLocaleString()}</div>
                    <small class="text-muted">⏳ Pending</small>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-3">
            <div class="d-flex justify-content-between mb-1" style="font-size:0.78rem;">
                <span>Delivery Progress</span><span>${sentPct}% complete</span>
            </div>
            <div class="progress" style="height:10px; border-radius:8px;">
                <div class="progress-bar bg-success" style="width:${sentPct}%"></div>
                <div class="progress-bar bg-danger" style="width:${failPct}%"></div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex gap-2 flex-wrap mb-1">
            ${failed > 0 ? `
            <a href="<?= url('admin/marketing/campaigns/resend-failed') ?>/${activeCampaignId}"
                class="btn btn-sm btn-warning"
                onclick="return confirm('Retry ${failed} failed email(s)?')">
                <i class="bi bi-arrow-repeat me-1"></i>Retry ${failed} Failed Email${failed > 1 ? 's' : ''}
            </a>` : ''}
            <button class="btn btn-sm btn-outline-success"
                onclick="document.querySelector('#statsModal .btn-close').click(); openSendOneModal(activeCampaignId, '...', '...')">
                <i class="bi bi-person-check me-1"></i>Send to Specific User
            </button>
            <a href="<?= url('admin/marketing/campaigns/resend-all') ?>/${activeCampaignId}"
                class="btn btn-sm btn-outline-secondary"
                onclick="return confirm('Resend ENTIRE campaign to ALL subscribers again?')">
                <i class="bi bi-send-arrow-up me-1"></i>Resend Entire Campaign
            </a>
        </div>

        ${failTable}`;
    }

    // ── Send to Single User Modal ─────────────────────────────────
    function openSendOneModal(campId, campName, subject) {
        activeCampaignId = campId;
        document.getElementById('sendOneCampaignLabel').textContent = 'Campaign: ' + campName;
        document.getElementById('sendOneSubjectLabel').textContent  = subject;
        document.getElementById('sendOneEmail').value = '';
        document.getElementById('sendOneName').value  = '';
        document.getElementById('sendOneResult').className = 'd-none alert';
        document.getElementById('sendOneResult').innerHTML = '';

        const modal = new bootstrap.Modal(document.getElementById('sendOneModal'));
        modal.show();
    }

    function submitSendOne() {
        const email = document.getElementById('sendOneEmail').value.trim();
        const name  = document.getElementById('sendOneName').value.trim();

        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showSendOneResult(false, 'Please enter a valid email address.');
            return;
        }

        const btn     = document.getElementById('sendOneBtn');
        const btnText = document.getElementById('sendOneBtnText');
        const spinner = document.getElementById('sendOneBtnSpinner');
        btn.disabled = true;
        btnText.classList.add('d-none');
        spinner.classList.remove('d-none');

        const fd = new FormData();
        fd.append('csrf_token',       '<?= csrfToken() ?>');
        fd.append('recipient_email',  email);
        fd.append('recipient_name',   name || 'Valued Customer');

        fetch('<?= url('admin/marketing/campaigns/send-one') ?>/' + activeCampaignId, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(r => r.text().then(t => { try { return JSON.parse(t); } catch(e) { throw new Error('Server: ' + t.substring(0,200)); } }))
        .then(data => showSendOneResult(data.success, data.message || data.error))
        .catch(err => showSendOneResult(false, 'Error: ' + err.message))
        .finally(() => {
            btn.disabled = false;
            btnText.classList.remove('d-none');
            spinner.classList.add('d-none');
        });
    }

    function showSendOneResult(success, msg) {
        const el = document.getElementById('sendOneResult');
        el.className = 'alert ' + (success ? 'alert-success' : 'alert-danger');
        el.innerHTML = (success ? '✅ ' : '❌ ') + msg;
    }
</script>
