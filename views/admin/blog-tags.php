<?php
/**
 * Admin: Blog Tags Manager
 * Variables: $tags (array), $totalPages, $page
 */
$tags = $tags ?? [];
$totalPages = $totalPages ?? 1;
$page = $page ?? 1;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1">Blog Tags</h5>
        <p class="text-muted small mb-0">Manage tags attached to blog posts for better discoverability.</p>
    </div>
    <button class="btn-dc-primary btn-dc-sm" data-bs-toggle="modal" data-bs-target="#blogTagModal" onclick="resetTagForm()">
        <i class="bi bi-plus-lg me-1"></i> Add Tag
    </button>
</div>

<!-- Flash Messages -->
<?php if ($flash = getFlash()): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Tags Table -->
<div class="admin-card">
    <div class="card-body p-0">
        <?php if (empty($tags)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-tags fs-1 d-block mb-3 opacity-25"></i>
            <h6>No tags found</h6>
            <p class="small">Add tags to improve post discoverability and filtering.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th style="width:80px;">Posts</th>
                        <th style="width:100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($tags as $tag): ?>
                <tr>
                    <td>
                        <a href="<?= url('blog/tag/' . $tag['slug']) ?>" target="_blank" class="text-decoration-none">
                            <span class="badge rounded-pill border fw-normal px-3 py-2" style="font-size:0.82rem; background: rgba(37,99,235,0.08); color: #2563eb; border-color: rgba(37,99,235,0.25) !important;">
                                #<?= htmlspecialchars($tag['name']) ?>
                            </span>
                        </a>
                    </td>
                    <td><code style="color:var(--text-muted);font-size:0.8rem;"><?= htmlspecialchars($tag['slug']) ?></code></td>
                    <td><?= (int)($tag['post_count'] ?? 0) ?></td>
                    <td>
                        <div class="admin-actions">
                            <button class="btn-action" onclick='editTag(<?= htmlspecialchars(json_encode($tag), ENT_QUOTES) ?>)' title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="<?= url('admin/blog/tags/delete/' . $tag['id']) ?>" onsubmit="return confirm('Delete this tag?')" class="d-inline">
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
                    <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">«</a></li>
                    <?php endif; ?>
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">»</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Bulk Import Card -->
<div class="admin-card mt-4">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3"><i class="bi bi-cloud-upload me-2 text-primary"></i>Bulk Import Tags</h6>
        <p class="text-muted small mb-3">Enter multiple tag names separated by commas. They will be automatically created if they do not exist.</p>
        <form method="POST" action="<?= url('admin/blog/tags/bulk-import') ?>">
            <?= csrfField() ?>
            <div class="d-flex gap-3 align-items-end">
                <div class="flex-grow-1">
                    <label class="dc-form-label">Tag Names (comma separated)</label>
                    <input type="text" name="tags" class="dc-form-control" placeholder="e.g. PHP, JavaScript, SEO, Design, AI" required>
                </div>
                <button type="submit" class="btn-dc-primary">Import Tags</button>
            </div>
        </form>
    </div>
</div>

<!-- Add / Edit Tag Modal -->
<div class="modal fade" id="blogTagModal" tabindex="-1" aria-labelledby="blogTagModalLabel">
    <div class="modal-dialog">
        <div class="modal-content" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);">
            <div class="modal-header" style="border-bottom:1px solid #e2e8f0; background:#f8fafc; border-top-left-radius:13px; border-top-right-radius:13px; padding:16px 24px;">
                <h5 class="modal-title" id="blogTagModalLabel" style="font-weight:700; color:#0f172a; font-size:1.1rem;">Add Tag</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:0.8rem;"></button>
            </div>
            <form id="blogTagForm" method="POST" action="<?= url('admin/blog/tags/store') ?>">
                <?= csrfField() ?>
                <input type="hidden" name="id" id="tagId" value="">
                <div class="modal-body" style="padding:24px;">
                    <div class="mb-3">
                        <label class="dc-form-label">Tag Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="tagName" class="dc-form-control" required placeholder="e.g. PHP Tutorials">
                    </div>
                    <div class="mb-3">
                        <label class="dc-form-label">Slug <small class="text-muted">(auto-generated)</small></label>
                        <input type="text" name="slug" id="tagSlug" class="dc-form-control" placeholder="php-tutorials">
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:16px 24px; background:#f8fafc; border-bottom-left-radius:13px; border-bottom-right-radius:13px;">
                    <button type="button" class="btn-dc-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-dc-primary">Save Tag</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetTagForm() {
    document.getElementById('blogTagModalLabel').textContent = 'Add Tag';
    document.getElementById('blogTagForm').action = '<?= url('admin/blog/tags/store') ?>';
    document.getElementById('tagId').value = '';
    document.getElementById('tagName').value = '';
    document.getElementById('tagSlug').value = '';
}
function editTag(tag) {
    document.getElementById('blogTagModalLabel').textContent = 'Edit Tag';
    document.getElementById('blogTagForm').action = '<?= url('admin/blog/tags/update/') ?>' + tag.id;
    document.getElementById('tagId').value = tag.id;
    document.getElementById('tagName').value = tag.name || '';
    document.getElementById('tagSlug').value = tag.slug || '';
    new bootstrap.Modal(document.getElementById('blogTagModal')).show();
}
</script>
