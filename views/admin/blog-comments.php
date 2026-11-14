<?php
/**
 * Admin: Blog Comments Moderation
 * Variables: $comments (array), $total, $page, $totalPages
 */
$comments   = $comments ?? [];
$total      = $total ?? 0;
$page       = $page ?? 1;
$totalPages = $totalPages ?? 1;

$statusColors = [
    'pending'  => ['bg' => 'rgba(245,158,11,0.12)', 'color' => '#d97706', 'icon' => 'bi-clock-history'],
    'approved' => ['bg' => 'rgba(16,185,129,0.12)', 'color' => '#059669', 'icon' => 'bi-check-circle-fill'],
    'spam'     => ['bg' => 'rgba(239,68,68,0.12)',  'color' => '#dc2626', 'icon' => 'bi-slash-circle-fill'],
    'rejected' => ['bg' => 'rgba(100,116,139,0.12)','color' => '#64748b', 'icon' => 'bi-x-circle-fill'],
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1">Blog Comments</h5>
        <p class="text-muted small mb-0">Moderate reader comments — approve, reject, or mark as spam.</p>
    </div>
    <div class="d-flex gap-2">
        <?php foreach (['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'spam' => 'Spam', 'rejected' => 'Rejected'] as $val => $label): ?>
        <a href="?status=<?= $val ?>" class="btn btn-sm <?= (($_GET['status'] ?? 'all') === $val) ? 'btn-primary' : 'btn-outline-secondary border' ?> rounded-pill px-3">
            <?= $label ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Flash Messages -->
<?php if ($flash = getFlash()): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <?php
    $db = Database::getInstance();
    $pendingCount  = (int)$db->fetchColumn("SELECT COUNT(*) FROM blog_comments WHERE status = 'pending'");
    $approvedCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM blog_comments WHERE status = 'approved'");
    $spamCount     = (int)$db->fetchColumn("SELECT COUNT(*) FROM blog_comments WHERE status = 'spam'");
    ?>
    <div class="col-md-4">
        <div class="admin-card p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:rgba(245,158,11,0.12);">
                <i class="bi bi-clock-history text-warning fs-5"></i>
            </div>
            <div>
                <div class="fw-bold fs-5"><?= $pendingCount ?></div>
                <div class="small text-muted">Awaiting Review</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:rgba(16,185,129,0.12);">
                <i class="bi bi-check-circle text-success fs-5"></i>
            </div>
            <div>
                <div class="fw-bold fs-5"><?= $approvedCount ?></div>
                <div class="small text-muted">Approved Live</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:rgba(239,68,68,0.12);">
                <i class="bi bi-shield-exclamation text-danger fs-5"></i>
            </div>
            <div>
                <div class="fw-bold fs-5"><?= $spamCount ?></div>
                <div class="small text-muted">Marked as Spam</div>
            </div>
        </div>
    </div>
</div>

<!-- Comments List -->
<div class="admin-card">
    <div class="card-body p-0">
        <?php if (empty($comments)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-chat-left-dots fs-1 d-block mb-3 opacity-25"></i>
            <h6>No comments found</h6>
            <p class="small">No comments matching the selected filter.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Comment</th>
                        <th style="width:130px;">Post</th>
                        <th style="width:90px;">Status</th>
                        <th style="width:120px;">Date</th>
                        <th style="width:140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($comments as $comment):
                    $statusStyle = $statusColors[$comment['status']] ?? $statusColors['pending'];
                    $authorName = $comment['user_display_name'] ?: $comment['guest_name'] ?: 'Anonymous';
                    $authorAvatar = strtoupper(substr($authorName, 0, 1));
                ?>
                <tr id="comment-row-<?= $comment['id'] ?>">
                    <td>
                        <div class="d-flex gap-3 align-items-start">
                            <!-- Avatar -->
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                 style="width:38px;height:38px;background:linear-gradient(135deg,#2563eb,#7c3aed);font-size:0.85rem;">
                                <?= $authorAvatar ?>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-semibold small"><?= htmlspecialchars($authorName) ?></span>
                                    <?php if (!empty($comment['guest_email'])): ?>
                                    <span class="text-muted small" style="font-size:0.75rem;"><?= htmlspecialchars($comment['guest_email']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($comment['parent_id'])): ?>
                                    <span class="badge rounded-pill" style="background:rgba(99,102,241,0.1);color:#6366f1;font-size:0.7rem;">↩ Reply</span>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-1 small text-secondary" style="max-width:420px;line-height:1.5;">
                                    <?= nl2br(htmlspecialchars(substr($comment['content'], 0, 200))) ?><?= strlen($comment['content']) > 200 ? '…' : '' ?>
                                </p>
                                <div class="d-flex gap-3 align-items-center mt-1">
                                    <span class="small text-muted"><i class="bi bi-hand-thumbs-up me-1"></i><?= (int)($comment['likes'] ?? 0) ?> likes</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if (!empty($comment['post_title'])): ?>
                        <a href="<?= url('blog/' . ($comment['post_slug'] ?? '#')) ?>" target="_blank" class="text-decoration-none small text-primary fw-semibold d-block" style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($comment['post_title']) ?>">
                            <?= htmlspecialchars($comment['post_title']) ?>
                        </a>
                        <?php else: ?>
                        <span class="text-muted small">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge rounded-pill px-2 py-1" style="background:<?= $statusStyle['bg'] ?>;color:<?= $statusStyle['color'] ?>;font-size:0.75rem;">
                            <i class="<?= $statusStyle['icon'] ?> me-1"></i><?= ucfirst($comment['status']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="small text-muted"><?= formatDate($comment['created_at']) ?></span>
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            <?php if ($comment['status'] !== 'approved'): ?>
                            <form method="POST" action="<?= url('admin/blog/comments/status/' . $comment['id']) ?>" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="status" value="approved">
                                <button class="btn btn-sm" style="background:rgba(16,185,129,0.1);color:#059669;border:1px solid rgba(16,185,129,0.2);border-radius:8px;padding:3px 8px;font-size:0.75rem;" title="Approve">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <?php if ($comment['status'] !== 'spam'): ?>
                            <form method="POST" action="<?= url('admin/blog/comments/status/' . $comment['id']) ?>" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="status" value="spam">
                                <button class="btn btn-sm" style="background:rgba(245,158,11,0.1);color:#d97706;border:1px solid rgba(245,158,11,0.2);border-radius:8px;padding:3px 8px;font-size:0.75rem;" title="Mark Spam">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <?php if ($comment['status'] !== 'rejected'): ?>
                            <form method="POST" action="<?= url('admin/blog/comments/status/' . $comment['id']) ?>" class="d-inline">
                                <?= csrfField() ?>
                                <input type="hidden" name="status" value="rejected">
                                <button class="btn btn-sm" style="background:rgba(100,116,139,0.1);color:#64748b;border:1px solid rgba(100,116,139,0.2);border-radius:8px;padding:3px 8px;font-size:0.75rem;" title="Reject">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            <form method="POST" action="<?= url('admin/blog/comments/delete/' . $comment['id']) ?>" onsubmit="return confirm('Permanently delete this comment?')" class="d-inline">
                                <?= csrfField() ?>
                                <button class="btn-action danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="d-flex justify-content-center py-3">
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&status=<?= $_GET['status'] ?? '' ?>">«</a></li>
                    <?php endif; ?>
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&status=<?= $_GET['status'] ?? '' ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&status=<?= $_GET['status'] ?? '' ?>">»</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
