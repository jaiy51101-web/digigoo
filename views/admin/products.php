<!-- Admin Products List -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Products (<?= $totalProducts ?>)</h5>
    <a href="<?= url('admin/products/create') ?>" class="btn-dc-primary btn-dc-sm"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
</div>

<div class="admin-card">
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Sales</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <div class="product-cell">
                            <img src="<?= $p['thumbnail'] ? uploadUrl($p['thumbnail']) : asset('images/placeholder.png') ?>" alt="">
                            <div>
                                <h6><?= htmlspecialchars(truncateText($p['title'], 40)) ?></h6>
                                <small><?= $p['product_type'] ?></small>
                                <?php if (!empty($p['digiws_product_id'])): ?>
                                    <br><span class="badge bg-light text-primary border mt-1" style="font-size:0.72rem;"><i class="bi bi-key-fill text-warning me-1"></i><?= htmlspecialchars($p['digiws_product_id']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
                    <td>
                        <strong><?= formatPrice($p['sale_price']) ?></strong>
                        <?php if ($p['mrp'] > $p['sale_price']): ?><br><small class="text-muted"><s><?= formatPrice($p['mrp']) ?></s></small><?php endif; ?>
                    </td>
                    <td><?= $p['total_sales'] ?></td>
                    <td><?= statusBadge($p['status']) ?></td>
                    <td>
                        <div class="admin-actions">
                            <a href="<?= url('admin/products/edit/' . $p['id']) ?>" class="btn-action" title="Edit"><i class="bi bi-pencil"></i></a>
                            <a href="<?= url('product/' . $p['slug']) ?>" class="btn-action" target="_blank" title="View"><i class="bi bi-eye"></i></a>
                            <form method="POST" action="<?= url('admin/products/delete/' . $p['id']) ?>" onsubmit="return confirm('Delete this product?')" class="d-inline">
                                <?= csrfField() ?>
                                <button type="submit" class="btn-action danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?><tr><td colspan="6" class="text-center text-muted py-4">No products found</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= pagination($totalProducts, 15, $_GET['page'] ?? 1, url('admin/products')) ?>
