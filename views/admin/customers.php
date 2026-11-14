<!-- Admin Customers -->
<style>
.customer-search-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.customer-search-input {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 16px;
    font-size: 0.9rem;
    width: 320px;
    max-width: 100%;
    outline: none;
    transition: border-color 0.2s;
}
.customer-search-input:focus {
    border-color: #7c3aed;
}
.customer-table-wrapper {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}
.customer-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}
.customer-table thead th {
    background: #f8fafc;
    padding: 14px 18px;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
    text-align: left;
    white-space: nowrap;
}
.customer-table tbody td {
    padding: 16px 18px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.customer-table tbody tr:last-child td {
    border-bottom: none;
}
.customer-table tbody tr:hover {
    background: #f8fafc;
}
.customer-avatar {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #7c3aed, #3b82f6);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.customer-name {
    font-weight: 600;
    color: #0f172a;
}
.customer-email {
    color: #475569;
    font-size: 0.85rem;
}
.customer-phone {
    color: #475569;
    font-size: 0.85rem;
}
.status-badge {
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
.status-active {
    background: #d1fae5;
    color: #065f46;
}
.status-inactive {
    background: #f3f4f6;
    color: #374151;
}
.status-banned {
    background: #fee2e2;
    color: #991b1b;
}
.btn-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    border: 1px solid transparent;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    font-size: 0.9rem;
}
.btn-view {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}
.btn-view:hover {
    background: #dbeafe;
    border-color: #3b82f6;
}
.btn-delete {
    background: #fef2f2;
    color: #dc2626;
    border-color: #fecaca;
}
.btn-delete:hover {
    background: #fee2e2;
    border-color: #dc2626;
}
.pagination-wrapper {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid #e2e8f0;
    background: #ffffff;
}
.pagination-info {
    font-size: 0.85rem;
    color: #64748b;
}
</style>

<div class="customer-search-bar">
    <div>
        <h5 class="mb-0 fw-bold text-dark">Customers</h5>
        <p class="text-muted mb-0 small">Manage and view all registered customers</p>
    </div>
    <form method="GET" action="<?= url('admin/customers') ?>" class="d-flex gap-2">
        <input type="text" name="search" class="customer-search-input" placeholder="Search customers..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit" class="btn btn-primary btn-sm fw-bold">Search</button>
    </form>
</div>

<div class="customer-table-wrapper">
    <div class="table-responsive">
        <table class="customer-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 mb-2 d-block text-secondary"></i>
                            No customers found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $c): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="customer-avatar">
                                        <?= strtoupper(substr($c['name'], 0, 1)) ?>
                                    </div>
                                    <span class="customer-name"><?= htmlspecialchars($c['name']) ?></span>
                                </div>
                            </td>
                            <td><span class="customer-email"><?= htmlspecialchars($c['email']) ?></span></td>
                            <td><span class="customer-phone"><?= htmlspecialchars($c['phone'] ?? '—') ?></span></td>
                            <td>
                                <span class="status-badge status-<?= $c['status'] ?>">
                                    <?= ucfirst($c['status']) ?>
                                </span>
                            </td>
                            <td><?= formatDate($c['created_at']) ?></td>
                            <td>
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="<?= url('admin/customers/' . $c['id']) ?>" class="btn-icon btn-view" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form method="POST" action="<?= url('admin/customers/' . $c['id'] . '/delete') ?>" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this customer? This action cannot be undone.');">
                                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                        <button type="submit" class="btn-icon btn-delete" title="Delete Customer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($totalCustomers > 20): ?>
        <div class="pagination-wrapper">
            <span class="pagination-info">
                Showing <?= count($customers) ?> of <?= number_format($totalCustomers) ?> customers
            </span>
            <?= pagination($totalCustomers, 20, $_GET['page'] ?? 1, url('admin/customers')) ?>
        </div>
    <?php endif; ?>
</div>
