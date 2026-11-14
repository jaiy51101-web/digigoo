<!-- Admin Orders -->
<div class="d-flex justify-content-between align-items-center mb-4"><h5 class="mb-0">Orders (<?= $totalOrders ?>)</h5></div>
<div class="admin-card">
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td><a href="<?= url('admin/orders/' . $o['id']) ?>" style="font-weight:600;">#<?= $o['order_number'] ?></a></td>
                    <td><?= htmlspecialchars($o['user_name']) ?><br><small class="text-muted"><?= htmlspecialchars($o['user_email']) ?></small></td>
                    <td><strong><?= formatPrice($o['total']) ?></strong></td>
                    <td><?= statusBadge($o['payment_status']) ?></td>
                    <td><?= statusBadge($o['status']) ?></td>
                    <td><?= formatDate($o['created_at']) ?></td>
                    <td>
                        <div class="admin-actions">
                            <a href="<?= url('admin/orders/' . $o['id']) ?>" class="btn-action" title="View"><i class="bi bi-eye"></i></a>
                            <form method="POST" action="<?= url('admin/orders/delete/' . $o['id']) ?>" onsubmit="return confirm('Are you sure you want to delete this order?')" class="d-inline">
                                <?= csrfField() ?>
                                <button type="submit" class="btn-action danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?><tr><td colspan="7" class="text-center text-muted py-4">No orders</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= pagination($totalOrders, 20, $_GET['page'] ?? 1, url('admin/orders')) ?>
