<!-- Wishlist Page -->
<div class="wishlist-page py-5">
    <div class="container">
        <div class="wishlist-header mb-5 text-center">
            <h1 class="fw-800 mb-2">My Wishlist</h1>
            <p class="text-muted">Explore the products you've saved for later.</p>
        </div>

        <?php if (empty($products)): ?>
            <div class="empty-wishlist text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-heart text-muted" style="font-size: 5rem;"></i>
                </div>
                <h3>Your wishlist is empty</h3>
                <p class="text-muted mb-4">You haven't saved any products yet. Browse our shop to find something you like!</p>
                <a href="<?= url('products') ?>" class="btn btn-primary rounded-pill px-4 py-2">
                    Browse Products
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col-xl-3 col-lg-4 col-6">
                        <?php include VIEWS_PATH . '/partials/product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
