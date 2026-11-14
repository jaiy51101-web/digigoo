<!-- Admin Order Detail -->
<div class="mb-4"><a href="<?= url('admin/orders') ?>" class="text-muted" style="font-size:0.88rem;"><i class="bi bi-arrow-left me-1"></i>Back to Orders</a></div>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Order #<?= $order['order_number'] ?></h5>
    <div class="d-flex gap-2">
        <?= statusBadge($order['status']) ?> <?= statusBadge($order['payment_status']) ?>
        <a href="<?= url('admin/orders/invoice/' . $order['id']) ?>" class="btn-dc-outline btn-dc-sm" target="_blank"><i class="bi bi-file-earmark-pdf me-1"></i>Invoice</a>
    </div>
</div>
<div class="row g-4">
    <div class="col-md-8">
        <div class="admin-card mb-4">
            <div class="card-header"><h6>Order Items</h6></div>
            <div class="card-body" style="padding:0;">
                <table class="admin-table">
                    <thead><tr><th>Product</th><th>Price</th><th>Extras</th><th>Subtotal</th><th>License</th></tr></thead>
                    <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><div class="product-cell"><img src="<?= $item['thumbnail'] ? uploadUrl($item['thumbnail']) : asset('images/placeholder.png') ?>" alt=""><div><h6><?= htmlspecialchars($item['product_title']) ?></h6></div></div></td>
                        <td><?= formatPrice($item['price']) ?></td>
                        <td><?php if ($item['setup_included']): ?>Setup: <?= formatPrice($item['setup_price']) ?><br><?php endif; ?><?php if ($item['support_months']): ?>Support <?= $item['support_months'] ?>mo: <?= formatPrice($item['support_price']) ?><?php endif; ?></td>
                        <td><strong><?= formatPrice($item['subtotal']) ?></strong></td>
                        <td><?= $item['license_key'] ? '<code style="color:var(--accent-primary);font-size:0.78rem;">' . $item['license_key'] . '</code>' : '—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Update Status -->
        <div class="admin-card">
            <div class="card-header"><h6>Update Status</h6></div>
            <div class="card-body">
                <form method="POST" action="<?= url('admin/orders/status/' . $order['id']) ?>" class="row g-3 align-items-end">
                    <?= csrfField() ?>
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-bold mb-1">Order Status</label>
                        <select name="status" class="dc-form-control">
                            <?php foreach (['pending','processing','completed','cancelled','refunded'] as $s): ?>
                            <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-bold mb-1">Payment Status</label>
                        <select name="payment_status" class="dc-form-control">
                            <?php foreach (['pending','paid','failed'] as $p): ?>
                            <option value="<?= $p ?>" <?= $order['payment_status'] === $p ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn-dc-primary btn-dc-sm w-100 py-2">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="dc-cart-summary mb-4">
            <h5>Summary</h5>
            <div class="summary-row"><span>Subtotal</span><span><?= formatPrice($order['subtotal']) ?></span></div>
            <div class="summary-row"><span>Tax</span><span><?= formatPrice($order['tax_amount']) ?></span></div>
            <?php if ($order['discount'] > 0): ?><div class="summary-row discount"><span>Discount</span><span>-<?= formatPrice($order['discount']) ?></span></div><?php endif; ?>
            <div class="summary-row total"><span>Total</span><span><?= formatPrice($order['total']) ?></span></div>
        </div>
        <div class="admin-card">
            <div class="card-header"><h6>Customer</h6></div>
            <div class="card-body">
                <p class="mb-1"><strong><?= htmlspecialchars($order['user_name']) ?></strong></p>
                <p class="text-muted mb-2" style="font-size:0.88rem;"><?= htmlspecialchars($order['user_email']) ?></p>
                <div class="mb-2" style="font-size:0.88rem;">
                    <span class="text-muted">Payment:</span> <span class="badge bg-light text-dark border"><?= ucfirst(str_replace('_', ' ', $order['payment_method'] ?? 'N/A')) ?></span>
                </div>
                
                <?php if ($order['payment_method'] === 'upi_direct' && $payment): ?>
                    <div class="mt-3 p-3 border-0 rounded-3" style="font-size:0.82rem; background-color: #f8fafc !important;">
                        <div class="fw-bold text-primary mb-2 d-flex align-items-center gap-1">
                            <i class="bi bi-shield-check"></i> UPI Verification Details
                        </div>
                        <div class="mb-2">
                            <span class="text-secondary">UTR/Txn ID:</span> 
                            <?php if (!empty(trim($payment['transaction_id'] ?? ''))): ?>
                                <code class="px-2 py-1 bg-white border rounded text-dark fw-bold" style="font-size: 0.8rem;"><?= htmlspecialchars($payment['transaction_id']) ?></code>
                            <?php else: ?>
                                <span class="text-muted" style="font-style: italic;">Not Provided</span>
                            <?php endif; ?>
                        </div>
                        <?php 
                        $resp = json_decode($payment['gateway_response'] ?? '', true);
                        if (!empty($resp['screenshot'])): 
                        ?>
                            <div class="mt-3">
                                <a href="<?= uploadUrl($resp['screenshot']) ?>" target="_blank" class="btn btn-sm text-white border-0 w-100 py-2 d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #094ebb 0%, #1e6cf4 100%); border-radius: 8px; font-weight: 600; box-shadow: 0 4px 10px rgba(30, 108, 244, 0.15); transition: opacity 0.2s;">
                                    <i class="bi bi-image"></i> View Screenshot
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($order['coupon_code']): ?>
                    <div class="mt-2" style="font-size:0.88rem;">
                        <span class="text-muted">Coupon:</span> <span class="badge bg-info-subtle text-info border border-info-subtle"><?= htmlspecialchars($order['coupon_code']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="text-muted mt-2" style="font-size:0.88rem;">
                    <i class="bi bi-calendar3 me-1"></i><?= formatDate($order['created_at'], 'd M Y H:i') ?>
                </div>
            </div>
        </div>
    </div>
</div>
