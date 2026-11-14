<!-- Admin Coupons List -->
<div class="d-flex justify-content-between align-items-center mb-4"><h5 class="mb-0">Coupons</h5><a href="<?= url('admin/coupons/create') ?>" class="btn-dc-primary btn-dc-sm"><i class="bi bi-plus-lg me-1"></i>Add Coupon</a></div>
<div class="admin-card"><div class="card-body" style="padding:0;"><div class="table-responsive"><table class="admin-table">
<thead><tr><th>Code</th><th>Product</th><th>Type</th><th>Value</th><th>Min Order</th><th>User Limit</th><th>Usage</th><th>Expires</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($coupons as $c): ?>
<tr>
<td><code style="color:var(--accent-primary);font-weight:700;"><?= $c['code'] ?></code></td>
<td>
    <?php if ($c['product_id']): ?>
        <span class="badge bg-info text-dark" style="max-width:150px; overflow:hidden; text-overflow:ellipsis; display:inline-block; vertical-align:middle;" title="<?= htmlspecialchars($c['product_title'] ?? '') ?>">
            <?= htmlspecialchars($c['product_title'] ?? 'Product ID: '.$c['product_id']) ?>
        </span>
    <?php else: ?>
        <span class="badge bg-secondary">Global (All)</span>
    <?php endif; ?>
</td>
<td><?= ucfirst($c['discount_type']) ?></td>
<td><?= $c['discount_type'] === 'percentage' ? $c['discount_value'] . '%' : formatPrice($c['discount_value']) ?></td>
<td><?= $c['min_order_amount'] > 0 ? formatPrice($c['min_order_amount']) : '—' ?></td>
<td><?= $c['user_usage_limit'] ? $c['user_usage_limit'] . ' per user' : 'Unlimited' ?></td>
<td><?= $c['used_count'] ?><?= $c['usage_limit'] ? '/' . $c['usage_limit'] : '' ?></td>
<td><?= $c['expires_at'] ? formatDate($c['expires_at']) : 'Never' ?></td>
<td><?= statusBadge($c['status']) ?></td>
<td><div class="admin-actions">
<a href="<?= url('admin/coupons/edit/' . $c['id']) ?>" class="btn-action"><i class="bi bi-pencil"></i></a>
<form method="POST" action="<?= url('admin/coupons/delete/' . $c['id']) ?>" onsubmit="return confirm('Delete?')" class="d-inline">
    <?= csrfField() ?>
    <button class="btn-action danger"><i class="bi bi-trash"></i></button>
</form>
</div></td>
</tr>
<?php endforeach; ?>
<?php if (empty($coupons)): ?><tr><td colspan="10" class="text-center text-muted py-4">No coupons</td></tr><?php endif; ?>
</tbody></table></div></div></div>
