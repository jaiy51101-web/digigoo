<!-- User Dashboard -->
<?php $user = currentUser(); ?>
<section class="dc-section" style="padding-top:30px;">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <?php include VIEWS_PATH . '/user/sidebar.php'; ?>
            </div>

            <!-- Content -->
            <div class="col-lg-9">
                <h4 class="mb-4">Welcome back, <?= htmlspecialchars($user['name']) ?>! 👋</h4>

                <?php if (!empty($_SESSION['show_register_animation'])): ?>
                    <div class="dc-card text-center mb-4 p-5 animate__animated animate__fadeIn">
                        <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.9.10/dist/dotlottie-wc.js" type="module"></script>
                        <dotlottie-wc src="https://lottie.host/e3e698a1-d0ba-4cdb-bf3e-adf90f98234c/Dh51meHv3G.lottie" style="width: 300px; height: 300px; margin: 0 auto;" autoplay loop></dotlottie-wc>
                        <h2 class="mt-4">Account Created!</h2>
                        <p class="text-muted">Welcome to the family. Your account is ready.</p>
                        <button class="btn-dc-primary mt-3" onclick="this.parentElement.remove();">Get Started</button>
                    </div>
                    <?php unset($_SESSION['show_register_animation']); ?>
                <?php endif; ?>

                <!-- Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="dc-dash-stat">
                            <div class="stat-icon" style="background:rgba(139,92,246,0.12);color:var(--accent-primary);"><i class="bi bi-bag-check"></i></div>
                            <h3><?= $totalOrders ?></h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dc-dash-stat">
                            <div class="stat-icon" style="background:rgba(34,197,94,0.12);color:var(--accent-green);"><i class="bi bi-currency-rupee"></i></div>
                            <h3><?= formatPrice($totalSpent) ?></h3>
                            <p>Total Spent</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dc-dash-stat">
                            <div class="stat-icon" style="background:rgba(59,130,246,0.12);color:var(--accent-blue);"><i class="bi bi-download"></i></div>
                            <h3><?= $totalDownloads ?></h3>
                            <p>Downloads</p>
                        </div>
                    </div>
                </div>
                <!-- Website Request CTA -->
                <div class="dc-card mb-4 border-0 p-4" style="background: linear-gradient(135deg, #8b5cf6, #d946ef); color: white; border-radius: 16px;">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <h4 class="mb-2">Need a Custom Website?</h4>
                            <p class="mb-0 opacity-75">Tell us about your project requirements and get a professional quote from our experts.</p>
                        </div>
                        <div class="col-md-3 text-md-end mt-3 mt-md-0">
                            <a href="<?= url('user/website-requests') ?>" class="btn btn-light fw-bold text-purple px-4 py-2" style="border-radius: 10px;">
                                <i class="bi bi-rocket-takeoff me-2"></i>Request Now
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="dc-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="<?= url('user/orders') ?>" class="text-decoration-none" style="font-size:0.88rem;">View All <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <?php if (empty($recentOrders)): ?>
                        <p class="text-muted">No orders yet. <a href="<?= url('products') ?>">Start shopping</a></p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="dc-table">
                            <thead><tr><th>Order #</th><th>Date</th><th>Total</th><th>Status</th><th></th></tr></thead>
                            <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><strong><?= $order['order_number'] ?></strong></td>
                                <td><?= formatDate($order['created_at']) ?></td>
                                <td><?= formatPrice($order['total']) ?></td>
                                <td><?= statusBadge($order['status']) ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="<?= url('user/order/' . $order['id']) ?>" class="btn-dc-dark btn-dc-sm">View</a>
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
