<!-- Admin Reviews -->
<div class="d-flex justify-content-between align-items-center mb-4"><div><h5 class="mb-1">Reviews & Customer Feedback</h5><p class="text-muted small mb-0">Publish genuine feedback received from customers for a product or blog post.</p></div><span class="badge text-bg-primary rounded-pill"><?= $totalReviews ?> customer reviews</span></div>

<?php if ($flash = getFlash()): ?><div class="alert alert-<?= htmlspecialchars($flash['type']) ?>"><?= htmlspecialchars($flash['message']) ?></div><?php endif; ?>

<div class="admin-card mb-4"><div class="card-body p-4">
<h6 class="mb-1"><i class="bi bi-plus-circle me-2 text-primary"></i>Add Customer Feedback</h6>
<p class="text-muted small mb-3">Enter multiple names and messages separated by commas to publish bulk feedback at once.</p>
<form method="POST" action="<?= url('admin/reviews/feedback') ?>" class="row g-3" id="feedbackForm"><?= csrfField() ?>
    <div class="col-md-3"><label class="form-label">Show on</label><select name="target_type" id="feedbackTargetType" class="form-select" required><option value="product">Product</option><option value="blog">Blog post</option></select></div>
    <div class="col-md-5"><label class="form-label">Product / Blog post</label><select name="target_id" id="feedbackTarget" class="form-select" required><?php foreach ($products as $p): ?><option value="<?= (int)$p['id'] ?>" data-type="product"><?= htmlspecialchars($p['title']) ?></option><?php endforeach; ?><?php foreach ($posts as $post): ?><option value="<?= (int)$post['id'] ?>" data-type="blog" hidden><?= htmlspecialchars($post['title']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><label class="form-label">Rating</label>
        <select name="rating" class="form-select" id="ratingSelect" required>
            <option value="auto" selected>🎲 Auto (3.0–5.0)</option>
            <option value="5">⭐ 5.0</option>
            <option value="4.5">⭐ 4.5</option>
            <option value="4">⭐ 4.0</option>
            <option value="3.5">⭐ 3.5</option>
            <option value="3">⭐ 3.0</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label">Reviewer Role <span class="text-muted">(optional)</span></label>
        <input name="reviewer_role" class="form-control" placeholder="Business owner" maxlength="100">
    </div>

    <!-- Name field with multiple support -->
    <div class="col-md-5">
        <label class="form-label d-flex justify-content-between align-items-center">
            <span><i class="bi bi-person me-1"></i>Name(s) <span class="badge bg-primary-subtle text-primary ms-1" id="nameBadge">0</span></span>
            <small class="text-muted">Comma-separated</small>
        </label>
        <input name="reviewer_name" id="nameInput" class="form-control" placeholder="rahul, amit kumar, arti kumari" required maxlength="2000">
        <div class="form-text text-muted"><i class="bi bi-info-circle me-1"></i>Example: <code>rahul, amit kumar, arti kumari</code></div>
    </div>

    <!-- Message field with multiple support -->
    <div class="col-md-7">
        <label class="form-label d-flex justify-content-between align-items-center">
            <span><i class="bi bi-chat-dots me-1"></i>Customer Message(s) <span class="badge bg-primary-subtle text-primary ms-1" id="msgBadge">0</span></span>
            <small class="text-muted">Comma-separated</small>
        </label>
        <input name="message" id="msgInput" class="form-control" placeholder="Good product, app ka service acha hai, thanks to u" required maxlength="5000">
        <div class="form-text text-muted"><i class="bi bi-info-circle me-1"></i>Example: <code>Good product, app ka service acha hai, thanks to u</code></div>
    </div>

    <!-- Preview info box -->
    <div class="col-12" id="previewBox" style="display:none;">
        <div class="alert alert-info py-2 mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-lightning-fill text-primary"></i>
            <span id="previewText"></span>
        </div>
    </div>

    <div class="col-12">
        <button class="btn btn-primary" type="submit">
            <i class="bi bi-check2-circle me-1"></i> Publish Feedback
        </button>
        <button type="button" class="btn btn-outline-secondary ms-2" onclick="document.getElementById('nameInput').value='';document.getElementById('msgInput').value='';updatePreview();">
            <i class="bi bi-x me-1"></i> Clear
        </button>
    </div>
</form>
</div></div>

<script>
(function() {
    function countItems(val) {
        if (!val.trim()) return 0;
        return val.split(',').map(s => s.trim()).filter(s => s.length >= 2).length;
    }
    function countMsgs(val) {
        if (!val.trim()) return 0;
        return val.split(',').map(s => s.trim()).filter(s => s.length >= 5).length;
    }
    function updatePreview() {
        var nameVal = document.getElementById('nameInput').value;
        var msgVal  = document.getElementById('msgInput').value;
        var nc = countItems(nameVal);
        var mc = countMsgs(msgVal);
        document.getElementById('nameBadge').textContent = nc;
        document.getElementById('msgBadge').textContent  = mc;

        var previewBox  = document.getElementById('previewBox');
        var previewText = document.getElementById('previewText');
        var rating = document.getElementById('ratingSelect').value;

        if (nc > 0 && mc > 0) {
            var total = Math.max(nc, mc);
            var ratingLabel = rating === 'auto' ? 'Auto (3.0–5.0 अलग-अलग)' : rating;
            previewText.innerHTML = '<strong>' + total + ' feedback</strong> publish होंगे — ' +
                nc + ' name' + (nc>1?'s':'') + ', ' + mc + ' message' + (mc>1?'s':'') + ', Rating: <strong>' + ratingLabel + '</strong>';
            previewBox.style.display = '';
        } else {
            previewBox.style.display = 'none';
        }
    }
    document.getElementById('nameInput').addEventListener('input', updatePreview);
    document.getElementById('msgInput').addEventListener('input', updatePreview);
    document.getElementById('ratingSelect').addEventListener('change', updatePreview);
    updatePreview();
})();
</script>


<div class="admin-card mb-4">
    <div class="card-body p-4">
        <h6 class="mb-3"><i class="bi bi-eye-fill me-2 text-primary"></i>Configure Fake Views &amp; Sales Rules</h6>
        <form method="POST" action="<?= url('admin/reviews/fake-rules') ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-4">
                <label class="form-label">Select Product</label>
                <select name="product_id" class="form-select" required>
                    <option value="">-- Choose Product --</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= (int)$p['id'] ?>"><?= htmlspecialchars($p['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Min Views / Day</label>
                <input type="number" name="fake_views_min" class="form-control" placeholder="e.g. 10" min="0" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Max Views / Day</label>
                <input type="number" name="fake_views_max" class="form-control" placeholder="e.g. 1000" min="0" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Min Sales / Day</label>
                <input type="number" name="fake_sales_min" class="form-control" placeholder="e.g. 5" min="0" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Max Sales / Day</label>
                <input type="number" name="fake_sales_max" class="form-control" placeholder="e.g. 99" min="0" required>
            </div>
            <div class="col-12">
                <button class="btn btn-primary"><i class="bi bi-sliders me-1"></i> Apply Rule</button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($configuredProducts)): ?>
<div class="admin-card mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Fake Views / Day</th>
                        <th>Fake Sales / Day</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($configuredProducts as $cp): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($cp['title']) ?></strong></td>
                        <td><?= (int)$cp['fake_views_min'] ?> - <?= (int)$cp['fake_views_max'] ?> views</td>
                        <td><?= (int)$cp['fake_sales_min'] ?> - <?= (int)$cp['fake_sales_max'] ?> sales</td>
                        <td>
                            <form method="POST" action="<?= url('admin/reviews/fake-rules/delete/'.$cp['id']) ?>" onsubmit="return confirm('Remove fake rules for this product?')" class="d-inline">
                                <?= csrfField() ?>
                                <button class="btn-action danger" title="Remove Rules"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
// Merge managed feedback + real reviews into one unified list
$allRows = [];

foreach ($managedFeedback as $fb) {
    $allRows[] = [
        'source'       => 'managed',
        'id'           => $fb['id'],
        'product'      => ($fb['target_title'] ?: 'Deleted item') . ' <span class="badge text-bg-primary-subtle text-primary ms-1" style="font-size:10px;">' . ucfirst($fb['target_type']) . '</span>',
        'customer'     => $fb['reviewer_name'],
        'rating'       => (float)$fb['rating'],
        'comment'      => $fb['message'],
        'status'       => 'published',
        'created_at'   => $fb['created_at'],
    ];
}

foreach ($reviews as $r) {
    $allRows[] = [
        'source'       => 'review',
        'id'           => $r['id'],
        'product'      => htmlspecialchars(truncateText($r['product_title'], 30)),
        'customer'     => $r['user_name'],
        'rating'       => (float)$r['rating'],
        'comment'      => $r['comment'] ?? '',
        'status'       => $r['status'],
        'created_at'   => $r['created_at'],
    ];
}

// Sort newest first
usort($allRows, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
?>

<div class="admin-card"><div class="card-body" style="padding:0;">
<div class="px-4 pt-3 pb-2 d-flex justify-content-between align-items-center border-bottom">
    <h6 class="mb-0"><i class="bi bi-star-half me-2 text-warning"></i>All Reviews & Published Feedback</h6>
    <span class="badge text-bg-secondary rounded-pill"><?= count($allRows) ?> total</span>
</div>
<div class="table-responsive"><table class="admin-table">
<thead><tr><th>Product / Target</th><th>Customer</th><th>Rating</th><th>Comment / Message</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
<tbody>
<?php if (empty($allRows)): ?>
    <tr><td colspan="7" class="text-center text-muted py-5">
        <i class="bi bi-chat-square-text" style="font-size:2rem;opacity:.3;display:block;margin-bottom:8px;"></i>
        No reviews yet. Add customer feedback using the form above.
    </td></tr>
<?php endif; ?>
<?php foreach ($allRows as $row): ?>
<tr>
    <td><?= $row['product'] ?></td>
    <td>
        <span class="d-flex align-items-center gap-2">
            <span class="avatar-xs rounded-circle bg-primary-subtle text-primary fw-bold d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px;font-size:12px;flex-shrink:0;">
                <?= strtoupper(substr($row['customer'], 0, 1)) ?>
            </span>
            <?= htmlspecialchars($row['customer']) ?>
        </span>
    </td>
    <td>
        <span class="text-warning">
            <?php
            $stars = round($row['rating'] * 2) / 2;
            for ($i = 1; $i <= 5; $i++) {
                if ($stars >= $i) echo '<i class="bi bi-star-fill"></i>';
                elseif ($stars >= $i - 0.5) echo '<i class="bi bi-star-half"></i>';
                else echo '<i class="bi bi-star"></i>';
            }
            ?>
        </span>
        <small class="text-muted ms-1"><?= number_format($row['rating'], 1) ?></small>
    </td>
    <td title="<?= htmlspecialchars($row['comment']) ?>"><?= htmlspecialchars(truncateText($row['comment'], 55)) ?></td>
    <td>
        <?php if ($row['status'] === 'published'): ?>
            <span class="badge text-bg-success">✓ Published</span>
        <?php elseif ($row['status'] === 'approved'): ?>
            <span class="badge text-bg-success">✓ Approved</span>
        <?php elseif ($row['status'] === 'rejected'): ?>
            <span class="badge text-bg-danger">✗ Rejected</span>
        <?php else: ?>
            <span class="badge text-bg-warning text-dark">⏳ Pending</span>
        <?php endif; ?>
    </td>
    <td class="text-muted" style="white-space:nowrap;"><?= timeAgo($row['created_at']) ?></td>
    <td>
        <div class="admin-actions">
        <?php if ($row['source'] === 'review'): ?>
            <?php if ($row['status'] !== 'approved'): ?>
                <form method="POST" action="<?= url('admin/reviews/status/'.$row['id']) ?>" class="d-inline">
                    <?= csrfField() ?><input type="hidden" name="status" value="approved">
                    <button class="btn-action" title="Approve"><i class="bi bi-check-lg"></i></button>
                </form>
            <?php endif; ?>
            <?php if ($row['status'] !== 'rejected'): ?>
                <form method="POST" action="<?= url('admin/reviews/status/'.$row['id']) ?>" class="d-inline">
                    <?= csrfField() ?><input type="hidden" name="status" value="rejected">
                    <button class="btn-action" title="Reject"><i class="bi bi-x-lg"></i></button>
                </form>
            <?php endif; ?>
            <form method="POST" action="<?= url('admin/reviews/delete/'.$row['id']) ?>" onsubmit="return confirm('Delete this review?')" class="d-inline">
                <button class="btn-action danger" title="Delete"><i class="bi bi-trash"></i></button>
            </form>
        <?php else: ?>
            <form method="POST" action="<?= url('admin/reviews/feedback/delete/'.$row['id']) ?>" onsubmit="return confirm('Delete this feedback?')" class="d-inline">
                <?= csrfField() ?>
                <button class="btn-action danger" title="Delete"><i class="bi bi-trash"></i></button>
            </form>
        <?php endif; ?>
        </div>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div></div>
<script>
document.addEventListener('DOMContentLoaded', function () { const type = document.getElementById('feedbackTargetType'), target = document.getElementById('feedbackTarget'); function filterTargets(){ [...target.options].forEach(o => { o.hidden = o.dataset.type !== type.value; }); const visible = [...target.options].find(o => !o.hidden); if (visible) target.value = visible.value; } type.addEventListener('change', filterTargets); filterTargets(); });
</script>
