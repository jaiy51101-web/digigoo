<!-- Admin Coupon Form -->
<?php $isEdit = !empty($coupon); ?>
<div class="mb-4"><a href="<?= url('admin/coupons') ?>" class="text-muted" style="font-size:0.88rem;"><i class="bi bi-arrow-left me-1"></i>Back to Coupons</a></div>
<div class="admin-card" style="max-width:600px;">
    <div class="card-header"><h6><?= $isEdit ? 'Edit' : 'Add' ?> Coupon</h6></div>
    <div class="card-body">
        <form method="POST" action="<?= $isEdit ? url('admin/coupons/update/' . $coupon['id']) : url('admin/coupons/store') ?>" class="admin-form">
            <?= csrfField() ?>
            <div class="form-group"><label>Coupon Code</label><input type="text" name="code" class="dc-form-control" value="<?= htmlspecialchars($coupon['code'] ?? '') ?>" required style="text-transform:uppercase;"></div>
            
            <div class="form-group">
                <label>Applicable Product</label>
                <select name="product_id" class="dc-form-control">
                    <option value="">Apply to All Products (Global Coupon)</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= (int)($coupon['product_id'] ?? 0) === (int)$p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Discount Type</label><select name="discount_type" class="dc-form-control"><option value="percentage" <?= ($coupon['discount_type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Percentage</option><option value="fixed" <?= ($coupon['discount_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Fixed Amount</option></select></div>
                <div class="form-group"><label>Discount Value</label><input type="number" name="discount_value" class="dc-form-control" step="0.01" value="<?= $coupon['discount_value'] ?? '' ?>" required></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Min Order Amount (₹)</label><input type="number" name="min_order_amount" class="dc-form-control" step="0.01" value="<?= $coupon['min_order_amount'] ?? '0' ?>"></div>
                <div class="form-group"><label>Max Discount (₹)</label><input type="number" name="max_discount" class="dc-form-control" step="0.01" value="<?= $coupon['max_discount'] ?? '' ?>" placeholder="Unlimited"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label>Total Usage Limit</label><input type="number" name="usage_limit" class="dc-form-control" value="<?= $coupon['usage_limit'] ?? '' ?>" placeholder="Unlimited"></div>
                <div class="form-group"><label>Usage Limit Per User</label><input type="number" name="user_usage_limit" class="dc-form-control" value="<?= isset($coupon['user_usage_limit']) ? htmlspecialchars($coupon['user_usage_limit']) : '1' ?>" placeholder="Unlimited" min="1"></div>
            </div>
            <div class="form-group">
                <label>Expiry Date</label>
                <input type="date" name="expires_at" class="dc-form-control" value="<?= $coupon['expires_at'] ?? '' ?>">
            </div>
            <div class="form-group"><label>Status</label><select name="status" class="dc-form-control"><option value="active" <?= ($coupon['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= ($coupon['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
            <button type="submit" class="btn-dc-primary mt-2"><i class="bi bi-check-lg me-2"></i><?= $isEdit ? 'Update' : 'Create' ?> Coupon</button>
        </form>
    </div>
</div>
