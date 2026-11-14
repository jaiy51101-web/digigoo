<!-- Edit Template Settings -->
<div class="admin-card col-md-6 mx-auto mt-4">
    <div class="card-header bg-light py-3">
        <h6 class="mb-0 fw-bold"><i class="bi bi-gear-fill text-primary me-2"></i>Edit Template Settings</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('admin/marketing/templates/update/' . $template['id']) ?>">
            <?= csrfField() ?>
            <div class="form-group mb-3">
                <label class="dc-form-label">Template Name</label>
                <input type="text" name="name" class="dc-form-control" required value="<?= htmlspecialchars($template['name']) ?>">
            </div>
            <div class="form-group mb-4">
                <label class="dc-form-label">Subject Line</label>
                <input type="text" name="subject" class="dc-form-control" required value="<?= htmlspecialchars($template['subject'] ?? '') ?>">
            </div>
            <div class="d-flex justify-content-between">
                <a href="<?= url('admin/marketing/templates') ?>" class="btn btn-outline-secondary btn-sm">Cancel</a>
                <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>
