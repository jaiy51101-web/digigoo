<!-- Cart Page -->
<section class="dc-section" style="padding-top:30px;">
    <div class="container">
        <h2 class="mb-4"><i class="bi bi-bag me-2"></i>Shopping Cart</h2>

        <?php if (empty($cartData['items'])): ?>
        <div class="dc-empty-state">
            <i class="bi bi-bag-x"></i>
            <h5>Your cart is empty</h5>
            <p>Browse our products and add items to your cart.</p>
            <a href="<?= url('products') ?>" class="btn-dc-primary">Browse Products</a>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <?php foreach ($cartData['items'] as $item): ?>
                <div class="dc-cart-item" id="cart-item-<?= $item['product_id'] ?>">
                    <img src="<?= $item['thumbnail'] ? uploadUrl($item['thumbnail']) : asset('images/placeholder.png') ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                    <div class="item-info">
                        <h6><a href="<?= url('product/' . $item['slug']) ?>" style="color:var(--text-primary);"><?= htmlspecialchars($item['title']) ?></a></h6>
                        <small>
                            <?php if ($item['setup_included']): ?>
                                <span class="badge bg-purple-soft" style="background:rgba(139,92,246,0.15);color:#a78bfa;font-size:0.72rem;">+ Setup</span>
                            <?php endif; ?>
                            <?php if ($item['support_months'] > 0): ?>
                                <span class="badge bg-purple-soft" style="background:rgba(59,130,246,0.15);color:#60a5fa;font-size:0.72rem;"><?= $item['support_months'] ?>mo Support</span>
                            <?php endif; ?>
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="item-price">
                            <?php
                            $itemTotal = (float)$item['sale_price'];
                            if ($item['setup_included'] && $item['setup_option']) $itemTotal += (float)$item['setup_price'];
                            if ($item['support_months'] == 6) $itemTotal += (float)$item['support_6_months'];
                            if ($item['support_months'] == 12) $itemTotal += (float)$item['support_12_months'];
                            echo formatPrice($itemTotal);
                            ?>
                        </div>
                        <form method="POST" action="<?= url('cart/remove') ?>" class="d-inline">
                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                            <button type="submit" class="item-remove border-0 bg-transparent"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="dc-cart-summary">
                    <h5>Order Summary</h5>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span><?= formatPrice($cartData['subtotal']) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Tax (GST)</span>
                        <span><?= formatPrice($cartData['tax']) ?></span>
                    </div>
                    <?php if ($cartData['discount'] > 0): ?>
                    <div class="summary-row discount">
                        <span>Discount (<?= $cartData['coupon'] ?>)</span>
                        <span>-<?= formatPrice($cartData['discount']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span><?= formatPrice($cartData['total']) ?></span>
                    </div>

                    <!-- Coupon -->
                    <div class="mt-3 pt-3" style="border-top:1px solid var(--border-color);">
                        <?php if ($cartData['coupon']): ?>
                        <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.2);">
                            <span style="font-size:0.88rem;"><i class="bi bi-ticket-perforated me-1" style="color:var(--accent-green);"></i> <?= $cartData['coupon'] ?></span>
                            <form method="POST" action="<?= url('cart/remove-coupon') ?>" class="d-inline">
                                <button type="submit" class="border-0 bg-transparent text-danger" style="font-size:0.85rem;cursor:pointer;">Remove</button>
                            </form>
                        </div>
                        <?php else: ?>
                        <form method="POST" action="<?= url('cart/coupon') ?>" class="d-flex gap-2">
                            <input type="text" name="coupon_code" class="dc-form-control" placeholder="Coupon code" style="font-size:0.88rem;padding:10px 14px;" required>
                            <button type="submit" class="btn-dc-dark" style="white-space:nowrap;">Apply</button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <a href="<?= url('checkout') ?>" class="btn-dc-primary w-100 mt-3 text-center d-block">
                        <i class="bi bi-lock me-2"></i>Proceed to Checkout
                    </a>
                    <a href="<?= url('products') ?>" class="btn-dc-dark w-100 mt-2 text-center d-block">Continue Shopping</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
