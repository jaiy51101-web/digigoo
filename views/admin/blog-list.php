<!-- Admin Blog List -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Blog Posts</h5>
    <a href="<?= url('admin/blog/create') ?>" class="btn-dc-primary btn-dc-sm">
        <i class="bi bi-plus-lg me-1"></i>Add Post
    </a>
</div>

<!-- Filters Bar -->
<div class="admin-card mb-4" style="padding: 20px;">
    <form action="<?= url('admin/blog') ?>" method="GET" class="row g-3 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="form-control border-start-0" placeholder="Search by title, content or summary..." style="box-shadow: none;">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="published" <?= ($_GET['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                <option value="draft" <?= ($_GET['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100 rounded-3" style="background: var(--primary); border: none;">Filter</button>
        </div>
        <?php if (!empty($_GET['q']) || !empty($_GET['status'])): ?>
        <div class="col-md-2">
            <a href="<?= url('admin/blog') ?>" class="btn btn-outline-secondary w-100 rounded-3">Reset</a>
        </div>
        <?php endif; ?>
    </form>
</div>

<!-- Table list -->
<div class="admin-card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <?php if ($post['image']): ?>
                                    <img src="<?= uploadUrl($post['image']) ?>" alt="thumbnail" class="rounded" style="width: 45px; height: 45px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light text-muted rounded d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; font-size: 1.1rem;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="max-width: 250px;">
                                <strong class="text-primary-dark d-block text-truncate"><?= htmlspecialchars($post['title']) ?></strong>
                                <span class="text-muted small text-truncate d-block"><?= htmlspecialchars(truncateText(strip_tags($post['content']), 50)) ?></span>
                            </td>
                            <td>
                                <a href="<?= url('blog/' . $post['slug']) ?>" target="_blank" class="small text-decoration-underline text-muted">
                                    /<?= htmlspecialchars($post['slug']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($post['author_name'] ?? 'Admin') ?></td>
                            <td>
                                <?php if ($post['status'] === 'published'): ?>
                                    <span class="status-badge-custom status-active" style="background: var(--accent-soft); color: var(--accent);">Published</span>
                                <?php else: ?>
                                    <span class="status-badge-custom status-inactive" style="background: var(--bg-secondary); color: var(--text-secondary);">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td class="small"><?= formatDate($post['created_at']) ?></td>
                            <td>
                                <div class="admin-actions">
                                    <a href="<?= url('admin/blog/edit/' . $post['id']) ?>" class="btn-action" title="Edit Post">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="<?= url('admin/blog/delete/' . $post['id']) ?>" onsubmit="return confirm('Are you sure you want to delete this blog post?')" class="d-inline">
                                        <?= csrfField() ?>
                                        <button class="btn-action danger" title="Delete Post"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-journal-x d-block mb-2" style="font-size: 2rem;"></i>
                                No blog posts found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4">
    <?= pagination($totalPosts, $perPage, $page, url('admin/blog')) ?>
</div>
