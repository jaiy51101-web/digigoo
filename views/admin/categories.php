<!-- Admin Categories -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Categories</h5>
    <button class="btn-dc-primary btn-dc-sm" data-bs-toggle="modal" data-bs-target="#categoryModal" onclick="resetCatForm()"><i class="bi bi-plus-lg me-1"></i>Add Category</button>
</div>

<div class="admin-card">
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead><tr><th>Icon</th><th>Name</th><th>Slug</th><th>Products</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><i class="bi <?= htmlspecialchars($cat['icon'] ?: 'bi-box') ?>" style="font-size:1.2rem;color:var(--accent-primary);"></i></td>
                    <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                    <td><code style="color:var(--text-muted);"><?= $cat['slug'] ?></code></td>
                    <td><?= $cat['product_count'] ?? 0 ?></td>
                    <td><?= $cat['sort_order'] ?></td>
                    <td><?= statusBadge($cat['status']) ?></td>
                    <td>
                        <div class="admin-actions">
                            <button class="btn-action" onclick="editCategory(<?= htmlspecialchars(json_encode($cat)) ?>)"><i class="bi bi-pencil"></i></button>
                            <form method="POST" action="<?= url('admin/categories/delete/' . $cat['id']) ?>" onsubmit="return confirm('Delete?')" class="d-inline">
                                <?= csrfField() ?>
                                <button class="btn-action danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:14px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);">
            <div class="modal-header" style="border-bottom:1px solid #e2e8f0; background:#f8fafc; border-top-left-radius:13px; border-top-right-radius:13px; padding:16px 24px;">
                <h5 class="modal-title" id="catModalTitle" style="font-weight:700; color:#0f172a; font-size:1.1rem;">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="font-size:0.8rem;"></button>
            </div>
            <form id="categoryForm" method="POST" action="<?= url('admin/categories/store') ?>" enctype="multipart/form-data">
                <?= csrfField() ?>
                <div class="modal-body" style="padding:24px;">
                    <div class="mb-3"><label class="dc-form-label">Name</label><input type="text" name="name" id="catName" class="dc-form-control" required></div>
                    <div class="mb-3"><label class="dc-form-label">Icon (Bootstrap Icons class)</label><input type="text" name="icon" id="catIcon" class="dc-form-control" placeholder="bi-globe"></div>
                    <div class="mb-3"><label class="dc-form-label">Description</label><textarea name="description" id="catDesc" class="dc-form-control" rows="2"></textarea></div>
                    <div class="row g-3">
                        <div class="col-6"><label class="dc-form-label">Sort Order</label><input type="number" name="sort_order" id="catOrder" class="dc-form-control" value="0"></div>
                        <div class="col-6"><label class="dc-form-label">Status</label><select name="status" id="catStatus" class="dc-form-control"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                    </div>
                    <div class="mt-3"><label class="dc-form-label">Image</label><input type="file" name="image" class="dc-form-control" accept="image/*"></div>
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
function resetCatForm() {
    document.getElementById('catModalTitle').textContent = 'Add Category';
    document.getElementById('categoryForm').action = '<?= url('admin/categories/store') ?>';
    document.getElementById('catName').value = '';
    document.getElementById('catIcon').value = '';
    document.getElementById('catDesc').value = '';
    document.getElementById('catOrder').value = '0';
    document.getElementById('catStatus').value = 'active';
}
function editCategory(cat) {
    document.getElementById('catModalTitle').textContent = 'Edit Category';
    document.getElementById('categoryForm').action = '<?= url('admin/categories/update/') ?>' + cat.id;
    document.getElementById('catName').value = cat.name;
    document.getElementById('catIcon').value = cat.icon || '';
    document.getElementById('catDesc').value = cat.description || '';
    document.getElementById('catOrder').value = cat.sort_order;
    document.getElementById('catStatus').value = cat.status;
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}
</script>
