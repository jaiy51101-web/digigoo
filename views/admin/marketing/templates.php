<!-- Email Templates List -->
<style>
.template-header {
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
.template-category-filter {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.template-cat-btn {
    padding: 6px 14px;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    font-size: 0.82rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}
.template-cat-btn:hover {
    background: #f1f5f9;
    border-color: #7c3aed;
    color: #7c3aed;
}
.template-cat-btn.active {
    background: #7c3aed;
    color: #ffffff;
    border-color: #7c3aed;
}
.template-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}
.template-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.2s;
    display: flex;
    flex-direction: column;
}
.template-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}
.template-preview {
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}
.template-preview i {
    font-size: 2.5rem;
    opacity: 0.9;
}
.template-preview .template-cat-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(255,255,255,0.95);
    color: #475569;
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 3px 10px;
    border-radius: 20px;
}
.template-info {
    padding: 14px 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.template-name {
    font-weight: 700;
    color: #0f172a;
    font-size: 0.9rem;
    margin-bottom: 4px;
}
.template-subject {
    color: #64748b;
    font-size: 0.78rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 10px;
}
.template-actions {
    display: flex;
    gap: 6px;
    margin-top: auto;
}
.template-actions a, .template-actions button {
    flex: 1;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 600;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    border: none;
}
.btn-template-edit {
    background: #7c3aed;
    color: #ffffff;
}
.btn-template-edit:hover {
    background: #6d28d9;
}
.btn-template-send {
    background: #2563eb;
    color: #ffffff;
}
.btn-template-send:hover {
    background: #1d4ed8;
}
.btn-template-delete {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca !important;
}
.btn-template-delete:hover {
    background: #fee2e2;
}
</style>

<div class="template-header">
    <div>
        <h5 class="mb-0 fw-bold text-dark">Email Templates</h5>
        <p class="text-muted mb-0 small"><?= count($templates) ?> templates available</p>
    </div>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <div class="template-category-filter">
            <a href="<?= url('admin/marketing/templates') ?>" class="template-cat-btn <?= empty($currentCategory) ? 'active' : '' ?>">All</a>
            <a href="<?= url('admin/marketing/templates?category=welcome') ?>" class="template-cat-btn <?= $currentCategory === 'welcome' ? 'active' : '' ?>">Welcome</a>
            <a href="<?= url('admin/marketing/templates?category=promotional') ?>" class="template-cat-btn <?= $currentCategory === 'promotional' ? 'active' : '' ?>">Promotional</a>
            <a href="<?= url('admin/marketing/templates?category=transactional') ?>" class="template-cat-btn <?= $currentCategory === 'transactional' ? 'active' : '' ?>">Transactional</a>
            <a href="<?= url('admin/marketing/templates?category=newsletter') ?>" class="template-cat-btn <?= $currentCategory === 'newsletter' ? 'active' : '' ?>">Newsletter</a>
            <a href="<?= url('admin/marketing/templates?category=winback') ?>" class="template-cat-btn <?= $currentCategory === 'winback' ? 'active' : '' ?>">Winback</a>
        </div>
        <button type="button" class="btn btn-primary btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createTemplateModal">
            <i class="bi bi-plus-lg me-1"></i>Create New
        </button>
    </div>
</div>

<div class="template-grid">
    <?php foreach ($templates as $t): ?>
        <div class="template-card">
            <div class="template-preview" style="background: linear-gradient(135deg, #7c3aed 0%, #3b82f6 100%);">
                <i class="bi bi-envelope-paper-fill text-white"></i>
                <span class="template-cat-badge"><?= htmlspecialchars($t['category'] ?? 'general') ?></span>
            </div>
            <div class="template-info">
                <div class="template-name"><?= htmlspecialchars($t['name']) ?></div>
                <div class="template-subject"><?= htmlspecialchars($t['subject'] ?? 'No subject') ?></div>
                <div class="template-actions">
                    <a href="<?= url('admin/marketing/templates/builder/' . $t['id']) ?>" class="btn-template-edit">Edit Design</a>
                    <button type="button" class="btn-template-send" onclick="sendTestEmailWithTemplate(<?= $t['id'] ?>, '<?= htmlspecialchars(addslashes($t['name'])) ?>')">
                        <i class="bi bi-send me-1"></i>Send
                    </button>
                    <form method="POST" action="<?= url('admin/marketing/templates/delete/' . $t['id']) ?>" style="flex:1;" onsubmit="return confirm('Delete this template?')">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <button type="submit" class="btn-template-delete">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal: Create Template Dialog -->
<div class="modal fade" id="createTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?= url('admin/marketing/templates/store') ?>">
                <?= csrfField() ?>
                <div class="modal-header">
                    <h6 class="modal-title fw-bold"><i class="bi bi-layout-wtf text-primary me-2"></i>Create New Email Template</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="dc-form-label">Template Name</label>
                        <input type="text" name="name" class="dc-form-control" required placeholder="e.g. Summer Sale Newsletter">
                    </div>
                    <div class="form-group mb-0">
                        <label class="dc-form-label">Email Subject</label>
                        <input type="text" name="subject" class="dc-form-control" required placeholder="e.g. Summer Sale - 30% Off Everything">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Create & Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function sendTestEmailWithTemplate(templateId, templateName) {
    Swal.fire({
        title: `Send Test Email: ${templateName || 'Email Template'}`,
        html: `
            <div class="text-start">
                <label class="form-label">Recipient Email:</label>
                <input type="email" class="form-control" id="swal-recipient-email" placeholder="name@example.com" required>
                <small class="text-muted d-block mt-1">Test the ${templateName || 'email template'} to ensure it renders correctly.</small>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Send Test Email',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const recipientEmail = document.getElementById('swal-recipient-email').value.trim();
            if (!recipientEmail || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(recipientEmail)) {
                Swal.showValidationMessage('Please enter a valid email address.');
                return false;
            }
            return { recipient: recipientEmail };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            fetch('<?= url("admin/marketing/crm/send-test-email") ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?= csrfToken() ?>',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `template_id=${templateId}&recipient=${encodeURIComponent(result.value.recipient)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Sent', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.error || 'Failed to send email.', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Server communication failure.', 'error');
            });
        }
    });
}
</script>