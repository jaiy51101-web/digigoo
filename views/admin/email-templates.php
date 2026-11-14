<!-- Admin Email Templates List -->
<div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
    <div>
        <h4 class="mb-0 fw-800 text-dark">Email Templates</h4>
        <p class="text-muted small mb-0">Customize automated system notifications with brand-aligned styling.</p>
    </div>
</div>

<!-- Test Email Card -->
<div class="card border-0 shadow-sm mb-4" style="border-radius:20px;">
    <div class="card-body p-4">
        <h5 class="fw-800 text-dark mb-2">Send Test Email</h5>
        <p class="text-muted small">Verify your SMTP settings by sending a test email to any address.</p>
        <form action="<?= url('admin/email-templates/test') ?>" method="POST" class="row g-3 align-items-center">
            <?= csrfField() ?>
            <div class="col-md-8 col-lg-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="test_email" class="form-control bg-light border-start-0" placeholder="Enter target email address (e.g. name@example.com)" required style="box-shadow:none;">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-dc-dark-sm rounded-pill px-4" style="font-size:0.9rem; padding:10px 20px;">
                    <i class="bi bi-send me-2"></i>Send Test Email
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius:20px; overflow:hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead style="background:#f8faff; border-bottom:1px solid #f1f3f5;">
                    <tr>
                        <th class="ps-4 py-3" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); font-weight:800;">Template Entity</th>
                        <th class="py-3" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); font-weight:800;">Subject Line</th>
                        <th class="py-3" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); font-weight:800;">Last Modified</th>
                        <th class="text-end pe-4 py-3" style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); font-weight:800;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($templates)): ?>
                        <tr><td colspan="4" class="text-center py-5 text-muted">No templates found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($templates as $template): ?>
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-box bg-purple-soft text-purple rounded-circle d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
                                        <i class="bi bi-envelope-paper"></i>
                                    </div>
                                    <div>
                                        <div class="fw-700 text-dark"><?= htmlspecialchars($template['name']) ?></div>
                                        <code style="font-size:0.75rem; padding:2px 6px; background:#f1f3f5; border-radius:4px;"><?= $template['slug'] ?></code>
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted" style="font-size:0.9rem; max-width:400px;"><?= htmlspecialchars($template['subject']) ?></td>
                            <td class="text-muted small"><?= formatDate($template['updated_at']) ?></td>
                            <td class="text-end pe-4">
                                <a href="<?= url('admin/email-templates/edit/' . $template['id']) ?>" class="btn btn-dc-dark-sm rounded-pill px-3 py-2" style="font-size:0.8rem;">
                                    <i class="bi bi-pencil-square me-2"></i>Configure
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<style>
.fw-800 { font-weight: 800; }
.fw-700 { font-weight: 700; }
.bg-purple-soft { background: rgba(139, 92, 246, 0.08); color: #8b5cf6; }
</style>
