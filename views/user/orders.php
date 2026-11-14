<!-- User Orders Page -->
<section class="dc-section" style="padding-top:30px;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3"><?php include VIEWS_PATH . '/user/sidebar.php'; ?></div>
            <div class="col-lg-9">
                <h4 class="mb-4">My Orders</h4>
                <?php if (empty($orders)): ?>
                <div class="dc-empty-state"><i class="bi bi-bag-x"></i><h5>No orders yet</h5><p>Your order history will appear here.</p><a href="<?= url('products') ?>" class="btn-dc-primary">Shop Now</a></div>
                <?php else: ?>
                <div class="dc-card">
                    <div class="table-responsive">
                        <table class="dc-table">
                            <thead><tr><th>Order #</th><th>Date</th><th>Items</th><th>Total</th><th>Payment</th><th>Status</th><th></th></tr></thead>
                            <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong><?= $order['order_number'] ?></strong></td>
                                <td><?= formatDate($order['created_at']) ?></td>
                                <td><?= Database::getInstance()->count('order_items', 'order_id = ?', [$order['id']]) ?></td>
                                <td><?= formatPrice($order['total']) ?></td>
                                <td><?= statusBadge($order['payment_status']) ?></td>
                                <td><?= statusBadge($order['status']) ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="<?= url('user/order/' . $order['id']) ?>" class="btn-dc-dark btn-dc-sm">Details</a>
                                        <?php if (!in_array($order['payment_status'], ['paid', 'completed'])): ?>
                                            <a href="<?= url('user/order/' . $order['id']) ?>#retryPaymentCard" class="btn btn-danger btn-sm rounded" style="padding: 2px 10px; font-size: 0.75rem; font-weight: 600;"><i class="bi bi-credit-card me-1"></i>Pay Now</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?= pagination($totalOrders, 10, $_GET['page'] ?? 1, url('user/orders')) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($successOrder)): ?>
<!-- Success Popup Modal -->
<div class="dc-success-modal-overlay" id="orderSuccessModal">
    <div class="dc-success-modal-card text-center animate__animated animate__zoomIn">
        <button class="dc-success-modal-close" onclick="closeSuccessModal()">&times;</button>
        <div class="p-4 d-flex flex-column align-items-center">
            <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.10/dist/dotlottie-wc.js" type="module"></script>
            <dotlottie-wc src="https://lottie.host/c4f5921f-038d-4033-bf7f-61fe14225baf/p9DmmamIEc.lottie" style="width: 200px; height: 200px; margin: 0 auto;" autoplay loop></dotlottie-wc>
            <h4 class="text-success fw-bold mb-1" style="font-size: 0.95rem; letter-spacing: 0.5px;">Payment Successful</h4>
            <h3 class="fw-bold mb-2 mt-1" style="font-size: 1.6rem; color: var(--text-primary);">Order Successful! 🎉</h3>
            <p class="text-muted small mb-4 px-2" style="line-height: 1.5;">Thank you for your purchase. Your order #<?= $successOrder['order_number'] ?> is being processed.</p>
            <a href="<?= url('user/order/' . $successOrder['id']) ?>" class="btn-dc-primary btn-lg w-100 py-3 text-center" style="font-size: 0.92rem; font-weight: 600; border-radius: var(--radius);">View Details</a>
        </div>
    </div>
</div>

<style>
.dc-success-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(5px);
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.dc-success-modal-card {
    background: var(--bg-surface);
    border-radius: var(--radius-lg);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
    width: 100%;
    max-width: 440px;
    position: relative;
    overflow: hidden;
    border: 1px solid var(--border-color);
}
.dc-success-modal-close {
    position: absolute;
    top: 15px;
    right: 20px;
    background: none;
    border: none;
    font-size: 1.8rem;
    color: var(--text-muted);
    cursor: pointer;
    z-index: 10;
    transition: color 0.2s ease;
}
.dc-success-modal-close:hover {
    color: var(--text-primary);
}
</style>

<script>
function closeSuccessModal() {
    const modal = document.getElementById('orderSuccessModal');
    if (modal) {
        modal.classList.add('animate__animated', 'animate__fadeOut');
        setTimeout(() => {
            modal.remove();
        }, 400);
    }
}
// Automatically clean URL parameter
if (window.history.replaceState) {
    const url = new URL(window.location.href);
    url.searchParams.delete('success_modal');
    url.searchParams.delete('order_id');
    window.history.replaceState({}, document.title, url.toString());
}
</script>
<?php endif; ?>
