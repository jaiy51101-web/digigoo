<!-- Admin Contact Messages (Complaints) -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 fw-bold text-dark">Contact Messages</h4>
        <p class="text-muted mb-0 small">Customer inquiries & complaints submitted from the contact form.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/contact-messages?status=unread') ?>" class="btn btn-outline-secondary btn-sm">Unread</a>
        <a href="<?= url('admin/contact-messages?status=read') ?>" class="btn btn-outline-secondary btn-sm">Read</a>
        <a href="<?= url('admin/contact-messages') ?>" class="btn btn-outline-secondary btn-sm">All</a>
    </div>
</div>

<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-body p-0">
        <?php if (empty($messages)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                No contact messages found.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                    <thead class="table-light">
                        <tr>
                            <th width="22%">Name</th>
                            <th width="22%">Email</th>
                            <th width="18%">Subject</th>
                            <th width="23%">Message</th>
                            <th width="10%">Received</th>
                            <th width="5%">Status</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $m): ?>
                            <tr class="<?= (int)$m['is_read'] === 0 ? 'table-primary' : '' ?>">
                                <td class="fw-medium"><?= htmlspecialchars($m['name']) ?></td>
                                <td><a href="mailto:<?= htmlspecialchars($m['email']) ?>"><?= htmlspecialchars($m['email']) ?></a></td>
                                <td><?= htmlspecialchars($m['subject'] ?: '—') ?></td>
                                <td>
                                    <span class="text-muted"><?= htmlspecialchars(mb_strimwidth(strip_tags($m['message']), 0, 60, '…')) ?></span>
                                </td>
                                <td class="text-muted small"><?= date('d M Y, h:i A', strtotime($m['created_at'])) ?></td>
                                <td>
                                    <?php if ((int)$m['is_read'] === 0): ?>
                                        <span class="badge bg-warning-subtle text-warning">Unread</span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success">Read</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <?php if ((int)$m['is_read'] === 0): ?>
                                            <form method="POST" action="<?= url('admin/contact-messages/mark-read/' . $m['id']) ?>">
                                                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Mark as read">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" action="<?= url('admin/contact-messages/delete/' . $m['id']) ?>" onsubmit="return confirm('Delete this message?')">
                                            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
