<?php
/**
 * Admin: Blog Categories Manager
 * Variables: $categories (array)
 */
$categories = $categories ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1">Blog Categories</h5>
        <p class="text-muted small mb-0">Manage categories used to organise your blog posts.</p>
    </div>
    <button class="btn-dc-primary btn-dc-sm" data-bs-toggle="modal" data-bs-target="#blogCatModal" onclick="resetBlogCatForm()">
        <i class="bi bi-plus-lg me-1"></i> Add Category
    </button>
</div>

<!-- Flash Messages -->
<?php if ($flash = getFlash()): ?>
<div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($flash['message']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Categories Table -->
<div class="admin-card">
    <div class="card-body p-0">
        <?php if (empty($categories)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-collection fs-1 d-block mb-3 opacity-25"></i>
            <h6>No categories yet</h6>
            <p class="small">Create your first blog category to start organising posts.</p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:50px;">Icon</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th style="width:60px;">Color</th>
                        <th style="width:80px;">Posts</th>
                        <th style="width:80px;">Order</th>
                        <th style="width:90px;">Status</th>
                        <th style="width:100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td>
                        <div style="width:36px;height:36px;border-radius:10px;background:<?= htmlspecialchars($cat['color'] ?? '#2563eb') ?>20;display:flex;align-items:center;justify-content:center;">
                            <i class="<?= htmlspecialchars($cat['icon'] ?? 'bi-folder') ?>" style="color:<?= htmlspecialchars($cat['color'] ?? '#2563eb') ?>;font-size:1.1rem;"></i>
                        </div>
                    </td>
                    <td><strong><?= htmlspecialchars($cat['name']) ?></strong><?php if (!empty($cat['description'])): ?><br><span class="small text-muted"><?= htmlspecialchars(substr($cat['description'], 0, 60)) ?><?= strlen($cat['description']) > 60 ? '…' : '' ?></span><?php endif; ?></td>
                    <td><code style="color:var(--text-muted);font-size:0.8rem;"><?= htmlspecialchars($cat['slug']) ?></code></td>
                    <td>
                        <div style="width:24px;height:24px;border-radius:6px;background:<?= htmlspecialchars($cat['color'] ?? '#2563eb') ?>;border:1px solid rgba(0,0,0,0.1);" title="<?= htmlspecialchars($cat['color'] ?? '') ?>"></div>
                    </td>
                    <td><?= (int)($cat['post_count'] ?? 0) ?></td>
                    <td><?= (int)($cat['sort_order'] ?? 0) ?></td>
                    <td><?= statusBadge($cat['status'] ?? 'active') ?></td>
                    <td>
                        <div class="admin-actions">
                            <button class="btn-action" onclick='editBlogCategory(<?= htmlspecialchars(json_encode($cat), ENT_QUOTES) ?>)' title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="<?= url('admin/blog/categories/delete/' . $cat['id']) ?>" onsubmit="return confirm('Delete this category? Posts in it will be uncategorised.')" class="d-inline">
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
        <?php endif; ?>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="blogCatModal" tabindex="-1" aria-labelledby="blogCatModalLabel">
    <div class="modal-dialog">
        <div class="modal-content" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);">
            <div class="modal-header" style="border-bottom:1px solid #e2e8f0; background:#f8fafc; border-top-left-radius:13px; border-top-right-radius:13px; padding:16px 24px;">
                <h5 class="modal-title" id="blogCatModalLabel" style="font-weight:700; color:#0f172a; font-size:1.1rem;">Add Blog Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:0.8rem;"></button>
            </div>
            <form id="blogCatForm" method="POST" action="<?= url('admin/blog/categories/store') ?>">
                <?= csrfField() ?>
                <input type="hidden" name="id" id="blogCatId" value="">
                <div class="modal-body" style="padding:24px;">
                    <div class="mb-3">
                        <label class="dc-form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="blogCatName" class="dc-form-control" required placeholder="e.g. Web Development">
                    </div>
                    <div class="mb-3">
                        <label class="dc-form-label">Slug <small class="text-muted">(auto-generated if blank)</small></label>
                        <input type="text" name="slug" id="blogCatSlug" class="dc-form-control" placeholder="e.g. web-development">
                    </div>
                    <div class="mb-3">
                        <label class="dc-form-label">Icon <small class="text-muted">(Bootstrap Icons class)</small></label>
                        <input type="text" name="icon" id="blogCatIcon" class="dc-form-control" placeholder="bi-code-slash">
                        <div class="mt-2">
                            <small class="text-muted">Common: </small>
                            <?php foreach (['bi-code-slash','bi-graph-up','bi-palette','bi-phone','bi-globe','bi-shield-check','bi-cpu','bi-lightbulb','bi-journal-text','bi-cart3'] as $ico): ?>
                            <button type="button" class="btn btn-sm btn-light border me-1 mb-1 px-2 py-1" onclick="document.getElementById('blogCatIcon').value='<?= $ico ?>'; document.getElementById('blogCatIconPreview').className='<?= $ico ?> me-1';" style="font-size:0.75rem;border-radius:6px;">
                                <i class="<?= $ico ?>"></i>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="dc-form-label">Accent Color</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" name="color" id="blogCatColor" class="form-control form-control-color rounded-3" value="#2563eb" style="width:44px;height:38px;">
                                <input type="text" id="blogCatColorText" class="dc-form-control flex-grow-1" value="#2563eb" oninput="document.getElementById('blogCatColor').value=this.value">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="dc-form-label">Sort Order</label>
                            <input type="number" name="sort_order" id="blogCatOrder" class="dc-form-control" value="0" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="dc-form-label">Description</label>
                        <textarea name="description" id="blogCatDesc" class="dc-form-control" rows="2" placeholder="Short category description..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="dc-form-label">Status</label>
                        <select name="status" id="blogCatStatus" class="dc-form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid #e2e8f0; padding:16px 24px; background:#f8fafc; border-bottom-left-radius:13px; border-bottom-right-radius:13px;">
                    <button type="button" class="btn-dc-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-dc-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Sync color picker ↔ text input
document.getElementById('blogCatColor').addEventListener('input', function() {
    document.getElementById('blogCatColorText').value = this.value;
});

function resetBlogCatForm() {
    document.getElementById('blogCatModalLabel').textContent = 'Add Blog Category';
    document.getElementById('blogCatForm').action = '<?= url('admin/blog/categories/store') ?>';
    document.getElementById('blogCatId').value = '';
    document.getElementById('blogCatName').value = '';
    document.getElementById('blogCatSlug').value = '';
    document.getElementById('blogCatIcon').value = '';
    document.getElementById('blogCatDesc').value = '';
    document.getElementById('blogCatOrder').value = '0';
    document.getElementById('blogCatStatus').value = 'active';
    document.getElementById('blogCatColor').value = '#2563eb';
    document.getElementById('blogCatColorText').value = '#2563eb';
}

function editBlogCategory(cat) {
    document.getElementById('blogCatModalLabel').textContent = 'Edit Blog Category';
    document.getElementById('blogCatForm').action = '<?= url('admin/blog/categories/update/') ?>' + cat.id;
    document.getElementById('blogCatId').value = cat.id;
    document.getElementById('blogCatName').value = cat.name || '';
    document.getElementById('blogCatSlug').value = cat.slug || '';
    document.getElementById('blogCatIcon').value = cat.icon || '';
    document.getElementById('blogCatDesc').value = cat.description || '';
    document.getElementById('blogCatOrder').value = cat.sort_order || 0;
    document.getElementById('blogCatStatus').value = cat.status || 'active';
    const color = cat.color || '#2563eb';
    document.getElementById('blogCatColor').value = color;
    document.getElementById('blogCatColorText').value = color;
    new bootstrap.Modal(document.getElementById('blogCatModal')).show();
}
</script>
