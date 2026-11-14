<!-- Admin Customer Detail View -->
<style>
.customer-header {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.customer-avatar-large {
    width: 72px;
    height: 72px;
    background: linear-gradient(135deg, #7c3aed, #3b82f6);
    color: white;
    font-size: 2rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(124, 58, 237, 0.3);
    flex-shrink: 0;
}
.customer-info h2 {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 4px;
}
.customer-info .customer-meta {
    color: #64748b;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.customer-stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}
.stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 20px 24px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.stat-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #94a3b8;
}
.stat-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
}
.section-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 20px;
}
.section-header {
    padding: 18px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.section-title {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.section-body {
    padding: 0;
}
.section-body .table-responsive {
    overflow-x: auto;
}
.detail-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}
.detail-table th {
    background: #f8fafc;
    padding: 12px 18px;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
    text-align: left;
    white-space: nowrap;
}
.detail-table td {
    padding: 14px 18px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
}
.detail-table tr:last-child td {
    border-bottom: none;
}
.detail-table tr:hover td {
    background: #f8fafc;
}
.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-success { background: #d1fae5; color: #065f46; }
.badge-secondary { background: #f3f4f6; color: #374151; }
.badge-danger { background: #fee2e2; color: #991b1b; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-info { background: #eff6ff; color: #1d4ed8; }
.badge-primary { background: #eef2ff; color: #4338ca; }
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #64748b;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 12px;
    transition: color 0.2s;
}
.back-link:hover {
    color: #7c3aed;
}
.empty-state {
    padding: 40px 20px;
    text-align: center;
    color: #94a3b8;
}
.empty-state i {
    font-size: 2.5rem;
    opacity: 0.4;
    display: block;
    margin-bottom: 10px;
}
</style>

<a href="<?= url('admin/customers') ?>" class="back-link">
    <i class="bi bi-arrow-left"></i> Back to Customers
</a>

<div class="customer-header">
    <div class="d-flex align-items-center gap-4">
        <div class="customer-avatar-large">
            <?= strtoupper(substr($user['name'], 0, 1)) ?>
        </div>
        <div class="customer-info">
            <h2><?= htmlspecialchars($user['name']) ?></h2>
            <div class="customer-meta">
                <i class="bi bi-envelope"></i>
                <span><?= htmlspecialchars($user['email']) ?></span>
            </div>
            <?php if (!empty($user['phone'])): ?>
                <div class="customer-meta">
                    <i class="bi bi-telephone"></i>
                    <span><?= htmlspecialchars($user['phone']) ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <form method="POST" action="<?= url('admin/customers/status/' . $user['id']) ?>" class="d-flex gap-2 align-items-center">
        <?= csrfField() ?>
        <span class="small fw-semibold text-muted">Status:</span>
        <select name="status" class="dc-form-control dc-form-control-sm" style="width:150px;">
            <option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            <option value="banned" <?= $user['status'] === 'banned' ? 'selected' : '' ?>>Banned</option>
        </select>
        <button type="submit" class="btn-dc-primary btn-dc-sm">Update</button>
    </form>
</div>

<div class="customer-stats-row">
    <div class="stat-card">
        <span class="stat-label">Total Orders</span>
        <span class="stat-value"><?= count($orders) ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Total Spent</span>
        <span class="stat-value"><?= formatPrice(array_sum(array_column($orders, 'total'))) ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Signed Up</span>
        <span class="stat-value" style="font-size:1.1rem;"><?= formatDate($user['created_at'], 'd M Y') ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label">User Role</span>
        <span class="stat-value" style="font-size:1.1rem;text-transform:capitalize;"><?= htmlspecialchars($user['role'] ?? 'User') ?></span>
    </div>
</div>

<!-- Order History -->
<div class="section-card">
    <div class="section-header">
        <h6 class="section-title"><i class="bi bi-bag me-2 text-primary"></i>Order History</h6>
    </div>
    <div class="section-body">
        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <i class="bi bi-cart-x"></i>
                <p>No orders found for this customer.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Order Status</th>
                            <th>Payment</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong><?= $order['order_number'] ?></strong></td>
                                <td><?= formatDate($order['created_at'], 'd M Y') ?></td>
                                <td><strong><?= formatPrice($order['total']) ?></strong></td>
                                <td><span class="badge badge-<?= $order['status'] === 'completed' ? 'success' : ($order['status'] === 'cancelled' ? 'danger' : 'warning') ?>"><?= ucfirst($order['status']) ?></span></td>
                                <td><span class="badge badge-<?= $order['payment_status'] === 'paid' ? 'success' : ($order['payment_status'] === 'failed' ? 'danger' : 'warning') ?>"><?= ucfirst($order['payment_status']) ?></span></td>
                                <td class="text-end">
                                    <a href="<?= url('admin/orders/' . $order['id']) ?>" class="btn btn-sm btn-outline-primary">View Order</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Support Subscriptions -->
<div class="section-card">
    <div class="section-header">
        <h6 class="section-title"><i class="bi bi-shield-check me-2 text-primary"></i>Product Support & Validity</h6>
    </div>
    <div class="section-body">
        <?php if (empty($purchasedItems)): ?>
            <div class="empty-state">
                <i class="bi bi-shield-slash"></i>
                <p>No active or past support subscriptions found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="detail-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Order #</th>
                            <th>Purchased On</th>
                            <th>Support Tier</th>
                            <th>Expires On</th>
                            <th>Remaining Validity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchasedItems as $item): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($item['product_title']) ?></strong></td>
                                <td><code><?= $item['order_number'] ?></code></td>
                                <td><?= formatDate($item['created_at'], 'd M Y') ?></td>
                                <td>
                                    <?php if ($item['support_months'] == 0): ?>
                                        <span class="badge badge-secondary">No Support (10 days free)</span>
                                    <?php else: ?>
                                        <span class="badge badge-primary"><?= $item['support_months'] ?> Months Support</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $item['support_expires_at'] ? formatDate($item['support_expires_at'], 'd M Y H:i') : '<span class="text-muted">N/A</span>' ?>
                                </td>
                                <td>
                                    <?= getSupportValidityText($item['support_expires_at'] ?? null) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
