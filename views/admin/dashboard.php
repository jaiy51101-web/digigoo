<!-- =============================================
     Admin Dashboard — Digiws v2
     ============================================= -->
<style>
/* ---- Dashboard-specific styles ---- */
.dash-welcome {
    background: linear-gradient(135deg, #7c3aed 0%, #3b82f6 100%);
    border-radius: 20px;
    padding: 28px 32px;
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(124,58,237,0.3);
}

.dash-welcome::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 250px; height: 250px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
}

.dash-welcome::after {
    content: '';
    position: absolute;
    bottom: -80px; left: 20%;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
}

.dash-welcome-text {
    position: relative;
    z-index: 1;
}

.dash-welcome-text h2 {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 800;
    font-size: 1.55rem;
    color: #ffffff;
    margin: 0 0 5px;
    line-height: 1.2;
}

.dash-welcome-text p {
    color: rgba(255,255,255,0.72);
    font-size: 0.875rem;
    margin: 0;
    font-weight: 500;
}

.dash-welcome-actions {
    display: flex;
    gap: 10px;
    position: relative;
    z-index: 1;
    flex-shrink: 0;
}

.dash-welcome-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 18px;
    border-radius: 10px;
    font-size: 0.845rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
    border: none;
}

.dash-welcome-btn.primary {
    background: #ffffff;
    color: #7c3aed;
    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
}

.dash-welcome-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    color: #6d28d9;
}

.dash-welcome-btn.secondary {
    background: rgba(255,255,255,0.15);
    color: #ffffff;
    border: 1px solid rgba(255,255,255,0.25);
    backdrop-filter: blur(10px);
}

.dash-welcome-btn.secondary:hover {
    background: rgba(255,255,255,0.22);
    transform: translateY(-2px);
    color: #fff;
}

/* ---- Stat Cards Grid ---- */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 18px;
    margin-bottom: 28px;
}

.stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 22px;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.16,1,0.3,1);
    box-shadow: 0 1px 4px rgba(15,23,42,0.05);
}

.stat-card::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(15,23,42,0.10);
}

.stat-card:hover::after { opacity: 1; }

.stat-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 16px;
}

.stat-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
    transition: transform 0.3s;
}

.stat-card:hover .stat-icon { transform: scale(1.08) rotate(-4deg); }

.stat-trend {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 4px 8px;
    border-radius: 20px;
}

.stat-trend.up { color: #047857; background: rgba(16,185,129,0.1); }
.stat-trend.neutral { color: #6d28d9; background: rgba(124,58,237,0.08); }

.stat-value {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 1.9rem;
    font-weight: 900;
    color: #0f172a;
    line-height: 1;
    margin-bottom: 5px;
    letter-spacing: -0.5px;
}

.stat-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Card color themes */
.sc-revenue  .stat-icon { background: rgba(124,58,237,0.09); color: #7c3aed; }
.sc-monthly  .stat-icon { background: rgba(59,130,246,0.09); color: #3b82f6; }
.sc-orders   .stat-icon { background: rgba(16,185,129,0.09); color: #10b981; }
.sc-requests .stat-icon { background: rgba(245,158,11,0.09); color: #f59e0b; }
.sc-products .stat-icon { background: rgba(6,182,212,0.09);  color: #06b6d4; }
.sc-customers.stat-icon { background: rgba(236,72,153,0.09); color: #ec4899; }

.sc-revenue:hover  { border-color: #7c3aed; }
.sc-monthly:hover  { border-color: #3b82f6; }
.sc-orders:hover   { border-color: #10b981; }
.sc-requests:hover { border-color: #f59e0b; }
.sc-products:hover { border-color: #06b6d4; }
.sc-customers:hover { border-color: #ec4899; }

.sc-customers .stat-icon { background: rgba(236,72,153,0.09); color: #ec4899; }

/* ---- Glowing top accent on each card ---- */
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 18px 18px 0 0;
    opacity: 0;
    transition: opacity 0.3s;
}

.stat-card:hover::before { opacity: 1; }
.sc-revenue::before  { background: linear-gradient(90deg, #7c3aed, #8b5cf6); }
.sc-monthly::before  { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
.sc-orders::before   { background: linear-gradient(90deg, #10b981, #34d399); }
.sc-requests::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.sc-products::before { background: linear-gradient(90deg, #06b6d4, #22d3ee); }
.sc-customers::before{ background: linear-gradient(90deg, #ec4899, #f472b6); }

/* ---- Bottom area cards ---- */
.dash-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 22px;
}

@media (min-width: 900px) {
    .dash-grid { grid-template-columns: 5fr 3fr; }
}

/* Recent order row inline */
.order-row-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.ors-pending    { background: rgba(245,158,11,0.10); color: #b45309; }
.ors-processing { background: rgba(59,130,246,0.10); color: #1d4ed8; }
.ors-completed  { background: rgba(16,185,129,0.10); color: #047857; }
.ors-failed, .ors-cancelled { background: rgba(239,68,68,0.10); color: #b91c1c; }
.ors-paid       { background: rgba(124,58,237,0.10); color: #6d28d9; }

/* Customer list items */
.cust-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 0;
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.2s;
}

.cust-item:last-child { border-bottom: none; }

.cust-avatar {
    width: 38px; height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed, #3b82f6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 0.82rem;
    flex-shrink: 0;
}

.cust-name {
    font-size: 0.875rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
    line-height: 1.3;
}

.cust-email {
    font-size: 0.76rem;
    color: #94a3b8;
    display: block;
}

/* Doughnut legend */
.donut-legend {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 16px;
}

.donut-legend-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.8rem;
}

.donut-legend-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-right: 8px;
}

.donut-legend-label {
    display: flex;
    align-items: center;
    font-weight: 600;
    color: #475569;
    flex: 1;
}

.donut-legend-count {
    font-weight: 800;
    color: #0f172a;
}

/* Quick links section */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 12px;
    margin-bottom: 28px;
}

.quick-action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 18px 12px;
    border-radius: 14px;
    background: #fff;
    border: 1px solid #e2e8f0;
    text-decoration: none;
    transition: all 0.25s;
    cursor: pointer;
    text-align: center;
}

.quick-action-btn:hover {
    transform: translateY(-3px);
    border-color: #7c3aed;
    box-shadow: 0 8px 20px rgba(124,58,237,0.12);
}

.quick-action-btn i {
    font-size: 1.45rem;
}

.quick-action-btn span {
    font-size: 0.76rem;
    font-weight: 700;
    color: #475569;
    line-height: 1.2;
}

.qa-purple i { color: #7c3aed; }
.qa-blue   i { color: #3b82f6; }
.qa-green  i { color: #10b981; }
.qa-amber  i { color: #f59e0b; }
.qa-cyan   i { color: #06b6d4; }
.qa-pink   i { color: #ec4899; }
</style>

<?php
$greeting = 'Good ' . (date('H') < 12 ? 'Morning' : (date('H') < 17 ? 'Afternoon' : 'Evening'));
$adminName = $_SESSION['user_name'] ?? 'Admin';
?>

<!-- Welcome Banner -->
<div class="dash-welcome">
    <div class="dash-welcome-text">
        <h2><?= $greeting ?>, <?= htmlspecialchars($adminName) ?>! 👋</h2>
        <p>Here's what's happening in your store today — <?= date('l, F j, Y') ?></p>
    </div>
    <div class="dash-welcome-actions">
        <a href="<?= url('admin/products/create') ?>" class="dash-welcome-btn primary">
            <i class="bi bi-plus-lg"></i> Add Product
        </a>
        <a href="<?= url('admin/orders') ?>" class="dash-welcome-btn secondary">
            <i class="bi bi-bag-check"></i> View Orders
        </a>
    </div>
</div>

<!-- ---- Stat Cards ---- -->
<div class="stats-grid">
    <div class="stat-card sc-revenue" onclick="location.href='<?= url('admin/orders') ?>'" title="Total Revenue">
        <div class="stat-card-top">
            <div class="stat-icon"><i class="bi bi-currency-rupee"></i></div>
            <div class="stat-trend neutral"><i class="bi bi-graph-up-arrow"></i> All time</div>
        </div>
        <div class="stat-value"><?= formatPrice($stats['total_revenue']) ?></div>
        <div class="stat-label">Total Revenue</div>
    </div>

    <div class="stat-card sc-monthly" onclick="location.href='<?= url('admin/orders') ?>'" title="Monthly Revenue">
        <div class="stat-card-top">
            <div class="stat-icon"><i class="bi bi-calendar-month"></i></div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> This month</div>
        </div>
        <div class="stat-value"><?= formatPrice($stats['monthly_revenue']) ?></div>
        <div class="stat-label">Monthly Revenue</div>
    </div>

    <div class="stat-card sc-orders" onclick="location.href='<?= url('admin/orders') ?>'" title="Total Orders">
        <div class="stat-card-top">
            <div class="stat-icon"><i class="bi bi-bag-check"></i></div>
            <div class="stat-trend neutral"><i class="bi bi-box"></i> Lifetime</div>
        </div>
        <div class="stat-value"><?= number_format($stats['total_orders']) ?></div>
        <div class="stat-label">Total Orders</div>
    </div>

    <div class="stat-card sc-requests" onclick="location.href='<?= url('admin/website-requests') ?>'" title="Pending Custom Orders">
        <div class="stat-card-top">
            <div class="stat-icon"><i class="bi bi-code-slash"></i></div>
            <?php if($stats['pending_requests'] > 0): ?>
            <div class="stat-trend" style="background:rgba(245,158,11,0.12);color:#b45309;"><i class="bi bi-clock"></i> Pending</div>
            <?php endif; ?>
        </div>
        <div class="stat-value"><?= number_format($stats['pending_requests']) ?></div>
        <div class="stat-label">Custom Orders</div>
    </div>

    <div class="stat-card sc-products" onclick="location.href='<?= url('admin/products') ?>'" title="Products Listed">
        <div class="stat-card-top">
            <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
            <div class="stat-trend neutral"><i class="bi bi-collection"></i> Active</div>
        </div>
        <div class="stat-value"><?= number_format($stats['total_products']) ?></div>
        <div class="stat-label">Products Listed</div>
    </div>

    <div class="stat-card sc-customers" onclick="location.href='<?= url('admin/customers') ?>'" title="Total Customers">
        <div class="stat-card-top">
            <div class="stat-icon"><i class="bi bi-people"></i></div>
            <div class="stat-trend up"><i class="bi bi-person-plus"></i> Growing</div>
        </div>
        <div class="stat-value"><?= number_format($stats['total_customers']) ?></div>
        <div class="stat-label">Total Customers</div>
    </div>
</div>

<!-- ---- Quick Actions ---- -->
<div class="quick-actions">
    <a href="<?= url('admin/products/create') ?>" class="quick-action-btn qa-purple">
        <i class="bi bi-plus-square"></i><span>New Product</span>
    </a>
    <a href="<?= url('admin/orders') ?>" class="quick-action-btn qa-blue">
        <i class="bi bi-bag-check"></i><span>All Orders</span>
    </a>
    <a href="<?= url('admin/coupons/create') ?>" class="quick-action-btn qa-green">
        <i class="bi bi-ticket-perforated"></i><span>New Coupon</span>
    </a>
    <a href="<?= url('admin/customers') ?>" class="quick-action-btn qa-cyan">
        <i class="bi bi-people"></i><span>Customers</span>
    </a>
    <a href="<?= url('admin/website-requests') ?>" class="quick-action-btn qa-amber">
        <i class="bi bi-code-slash"></i><span>Requests</span>
    </a>
    <a href="<?= url('admin/settings') ?>" class="quick-action-btn qa-pink">
        <i class="bi bi-gear"></i><span>Settings</span>
    </a>
</div>

<!-- ---- Charts + Tables ---- -->
<div class="row g-4 mb-4">
    <!-- Revenue Chart -->
    <div class="col-lg-8">
        <div class="admin-card h-100" style="margin-bottom:0;">
            <div class="card-header">
                <h6><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Revenue Overview</h6>
                <a href="<?= url('admin/orders') ?>" style="font-size:0.82rem;font-weight:700;color:#7c3aed;text-decoration:none;">
                    View All <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Donut -->
    <div class="col-lg-4">
        <div class="admin-card h-100" style="margin-bottom:0;">
            <div class="card-header">
                <h6><i class="bi bi-pie-chart me-2" style="color:#f59e0b;"></i>Order Status</h6>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height:220px;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
                <?php
                $statusColors = ['#F59E0B','#3B82F6','#10B981','#EF4444','#7C3AED','#64748B'];
                $i = 0;
                ?>
                <div class="donut-legend">
                    <?php foreach($ordersByStatus as $st): ?>
                    <div class="donut-legend-item">
                        <div class="donut-legend-label">
                            <div class="donut-legend-dot" style="background:<?= $statusColors[$i % count($statusColors)] ?>;"></div>
                            <?= ucfirst($st['status']) ?>
                        </div>
                        <span class="donut-legend-count"><?= number_format($st['count']) ?></span>
                    </div>
                    <?php $i++; endforeach; ?>
                    <?php if(empty($ordersByStatus)): ?>
                    <p class="text-muted text-center py-2" style="font-size:0.82rem;">No orders yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders Table -->
    <div class="col-lg-8">
        <div class="admin-card" style="margin-bottom:0;">
            <div class="card-header">
                <h6><i class="bi bi-clock-history me-2" style="color:#10b981;"></i>Recent Orders</h6>
                <a href="<?= url('admin/orders') ?>" style="font-size:0.82rem;font-weight:700;color:#7c3aed;text-decoration:none;">
                    View All <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div style="padding:0;">
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr onclick="location.href='<?= url('admin/orders/' . $order['id']) ?>'" style="cursor:pointer;">
                                <td>
                                    <span style="font-weight:800;color:#7c3aed;font-size:0.875rem;">#<?= $order['order_number'] ?></span>
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:9px;">
                                        <div class="cust-avatar" style="width:30px;height:30px;font-size:0.72rem;">
                                            <?= strtoupper(substr($order['user_name'] ?? 'U', 0, 1)) ?>
                                        </div>
                                        <span style="font-weight:600;color:#0f172a;"><?= htmlspecialchars($order['user_name']) ?></span>
                                    </div>
                                </td>
                                <td><strong style="color:#0f172a;font-size:0.9rem;"><?= formatPrice($order['total']) ?></strong></td>
                                <td>
                                    <span class="order-row-status ors-<?= $order['status'] ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td style="color:#94a3b8;font-size:0.82rem;"><?= timeAgo($order['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentOrders)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5" style="color:#94a3b8;">
                                    <i class="bi bi-bag-x" style="font-size:2rem;display:block;margin-bottom:8px;opacity:0.4;"></i>
                                    No orders yet
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Customers -->
    <div class="col-lg-4">
        <div class="admin-card" style="margin-bottom:0;">
            <div class="card-header">
                <h6><i class="bi bi-people me-2" style="color:#ec4899;"></i>New Customers</h6>
                <a href="<?= url('admin/customers') ?>" style="font-size:0.82rem;font-weight:700;color:#7c3aed;text-decoration:none;">
                    View All <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body" style="padding:8px 24px;">
                <?php foreach ($recentCustomers as $cust): ?>
                <div class="cust-item">
                    <div class="cust-avatar">
                        <?= strtoupper(substr($cust['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p class="cust-name"><?= htmlspecialchars($cust['name']) ?></p>
                        <span class="cust-email"><?= htmlspecialchars($cust['email']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($recentCustomers)): ?>
                <div class="text-center py-4" style="color:#94a3b8;">
                    <i class="bi bi-people" style="font-size:1.8rem;display:block;margin-bottom:8px;opacity:0.4;"></i>
                    <span style="font-size:0.82rem;">No customers yet</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- =============================================
     CHARTS JAVASCRIPT
     ============================================= -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const salesData   = <?= json_encode($monthlySales) ?>;
    const labels      = salesData.map(d => d.month);
    const revenues    = salesData.map(d => parseFloat(d.revenue));
    const orderCounts = salesData.map(d => parseInt(d.total_orders));

    // ---- Revenue Chart ----
    const revCanvas = document.getElementById('revenueChart');
    if (revCanvas) {
        const ctx = revCanvas.getContext('2d');

        const purpleGrad = ctx.createLinearGradient(0, 0, 0, 280);
        purpleGrad.addColorStop(0, 'rgba(124,58,237,0.20)');
        purpleGrad.addColorStop(1, 'rgba(124,58,237,0.00)');

        const blueGrad = ctx.createLinearGradient(0, 0, 0, 280);
        blueGrad.addColorStop(0, 'rgba(59,130,246,0.18)');
        blueGrad.addColorStop(1, 'rgba(59,130,246,0.00)');

        new Chart(revCanvas, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Revenue (₹)',
                        data: revenues,
                        borderColor: '#7C3AED',
                        backgroundColor: purpleGrad,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.45,
                        pointBackgroundColor: '#7C3AED',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Orders',
                        data: orderCounts,
                        borderColor: '#3B82F6',
                        backgroundColor: blueGrad,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.45,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            color: '#64748B',
                            font: { weight: '600', size: 12, family: 'Inter' },
                            boxWidth: 10,
                            boxHeight: 10,
                            borderRadius: 5,
                            padding: 16,
                            usePointStyle: true,
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#94a3b8',
                        padding: 12,
                        borderRadius: 10,
                        titleFont: { weight: '700', family: 'Inter' },
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#94A3B8', font: { family: 'Inter', size: 11 } },
                        grid: { color: 'rgba(148,163,184,0.06)', drawBorder: false }
                    },
                    y: {
                        ticks: { color: '#94A3B8', font: { family: 'Inter', size: 11 } },
                        grid: { color: 'rgba(148,163,184,0.06)', drawBorder: false }
                    }
                }
            }
        });
    }

    // ---- Order Status Doughnut ----
    const statusData   = <?= json_encode($ordersByStatus) ?>;
    const statusLabels = statusData.map(d => d.status.charAt(0).toUpperCase() + d.status.slice(1));
    const statusCounts = statusData.map(d => parseInt(d.count));
    const statusColors = ['#F59E0B','#3B82F6','#10B981','#EF4444','#7C3AED','#64748B'];

    const statusCanvas = document.getElementById('orderStatusChart');
    if (statusCanvas) {
        new Chart(statusCanvas, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: statusColors,
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverBorderWidth: 3,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#94a3b8',
                        padding: 12,
                        borderRadius: 10,
                        titleFont: { weight: '700', family: 'Inter' },
                    }
                },
                cutout: '74%'
            }
        });
    }
});
</script>
