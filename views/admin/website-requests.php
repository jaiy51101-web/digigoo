<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold">Website Requests</h1>
    <div class="text-muted small">Manage custom project inquiries</div>
</div>

<div class="card shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #f8faff; border-bottom: 2px solid #f1f3f5;">
                    <tr>
                        <th class="ps-4 py-3" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); font-weight:800;">Client</th>
                        <th class="py-3" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); font-weight:800;">Project Scope</th>
                        <th class="py-3" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); font-weight:800;">Budget</th>
                        <th class="py-3" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); font-weight:800;">Status</th>
                        <th class="py-3" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); font-weight:800;">Received</th>
                        <th class="text-end pe-4 py-3" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); font-weight:800;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox text-muted mb-3" style="font-size:3rem; display:block; opacity:0.3;"></i>
                                <span class="text-muted">No custom website requests found.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm bg-purple-soft text-purple rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:40px; height:40px;">
                                            <?= strtoupper(substr($req['user_name'] ?? '?', 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($req['user_name'] ?: 'Unknown User') ?></div>
                                            <div class="small text-muted"><?= htmlspecialchars($req['user_email'] ?: '-') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill mb-1" style="background:rgba(139, 92, 246, 0.08); color:#8b5cf6; border:1px solid rgba(139, 92, 246, 0.1); font-weight:700;">
                                        <?= htmlspecialchars($req['website_type']) ?>
                                    </span>
                                    <div class="small text-muted text-truncate" style="max-width: 280px;" title="<?= htmlspecialchars($req['description']) ?>">
                                        <?= htmlspecialchars($req['description']) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-600 text-dark"><?= htmlspecialchars($req['budget'] ?: '—') ?></span>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm status-select border-0 bg-light rounded-pill px-3" data-id="<?= $req['id'] ?>" style="width: auto; font-weight:600; cursor:pointer;">
                                        <option value="pending" <?= $req['status'] === 'pending' ? 'selected' : '' ?>>🕒 Pending</option>
                                        <option value="contacted" <?= $req['status'] === 'contacted' ? 'selected' : '' ?>>📞 Contacted</option>
                                        <option value="resolved" <?= $req['status'] === 'resolved' ? 'selected' : '' ?>>✅ Resolved</option>
                                    </select>
                                </td>
                                <td class="text-muted small"><?= date('d M, Y', strtotime($req['created_at'])) ?></td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-2">
                                        <div class="btn-group shadow-sm rounded-pill overflow-hidden" style="border: 1px solid #eee;">
                                            <button class="btn btn-sm btn-white view-details border-0 px-3" 
                                                    data-desc="<?= htmlspecialchars($req['description']) ?>"
                                                    data-user="<?= htmlspecialchars($req['user_name']) ?>"
                                                    data-type="<?= htmlspecialchars($req['website_type']) ?>"
                                                    title="View Details">
                                                <i class="bi bi-eye text-primary"></i>
                                            </button>
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $req['user_phone'] ?? '') ?>" 
                                               target="_blank" 
                                               class="btn btn-sm btn-white border-0 border-start px-3 <?= empty($req['user_phone']) ? 'disabled opacity-50' : '' ?>"
                                               title="WhatsApp Contact">
                                                <i class="bi bi-whatsapp text-success"></i>
                                            </a>
                                        </div>
                                        <form method="POST" action="<?= url('admin/website-requests/delete/' . $req['id']) ?>" onsubmit="return confirm('Delete this request?')" class="d-inline">
                                            <?= csrfField() ?>
                                            <button type="submit" class="btn btn-sm btn-light border px-3 rounded-pill text-danger" style="height: 34px;" title="Delete Request">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:24px;">
            <div class="modal-header bg-light py-3 px-4" style="border-radius:24px 24px 0 0;">
                <h5 class="modal-title fw-800">Request Particulars</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-sm-6">
                        <label class="text-muted small text-uppercase fw-800 mb-1" style="letter-spacing:1px;">Client Name</label>
                        <div class="fw-bold h6 mb-0" id="modalUser"></div>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small text-uppercase fw-800 mb-1" style="letter-spacing:1px;">Project Type</label>
                        <div class="fw-bold h6 mb-0" id="modalType"></div>
                    </div>
                </div>
                <div class="p-4 rounded-4" style="background:#f8faff; border: 1px solid #eef1f6;">
                    <label class="text-muted small text-uppercase fw-800 mb-2 d-block" style="letter-spacing:1px;">Requirement Document</label>
                    <div id="modalDesc" style="white-space: pre-wrap; line-height:1.7; color:#4a5568;"></div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-dc-dark-sm px-4 rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.fw-800 { font-weight: 800; }
.fw-600 { font-weight: 600; }
.bg-purple-soft { background: rgba(139, 92, 246, 0.08); }
.text-purple { color: #8b5cf6; }
.btn-white { background: #fff; }
.btn-white:hover { background: #f8faff; }
.status-select:focus { box-shadow: none; border-color: transparent; }
.status-select.is-valid { border-color: #10b981 !important; background-color: rgba(16, 185, 129, 0.05) !important; }
</style>

<script>
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const id = this.getAttribute('data-id');
        const status = this.value;
        const selectEl = this;

        fetch('<?= url("admin/website-requests/status/") ?>' + id, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'status=' + status + '&csrf_token=<?= csrfToken() ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Flash success color briefly
                selectEl.classList.add('is-valid');
                setTimeout(() => selectEl.classList.remove('is-valid'), 2000);
            }
        });
    });
});

document.querySelectorAll('.view-details').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('modalUser').textContent = this.getAttribute('data-user');
        document.getElementById('modalType').textContent = this.getAttribute('data-type');
        document.getElementById('modalDesc').textContent = this.getAttribute('data-desc');
        new bootstrap.Modal(document.getElementById('detailsModal')).show();
    });
});
</script>

<style>
.bg-purple-soft { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
.text-purple { color: #8b5cf6; }
</style>
