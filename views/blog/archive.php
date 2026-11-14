<?php
/**
 * Digiws - Blog Date Archive Page
 */
$totalPages = isset($totalPages) ? $totalPages : (int)ceil($totalPosts / $perPage);
?>

<section class="blog-hero-section">
    <div class="blog-hero-bg"></div>
    <div class="container position-relative">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8 animate-fade-up">
                <div class="blog-badge-pill"><i class="bi bi-calendar3 me-2"></i>Date Archive</div>
                <h1 class="blog-hero-title">
                    <?php if (!empty($_GET['year'])): ?>
                        Articles from <?= htmlspecialchars($_GET['month'] ?? '') ?>/<?= htmlspecialchars($_GET['year']) ?>
                    <?php else: ?>
                        Blog Post Archive
                    <?php endif; ?>
                </h1>
                <div class="blog-stats-row justify-content-center">
                    <div class="stat-pill"><i class="bi bi-journals"></i> <?= number_format($totalPosts) ?> Articles</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="blog-main-section">
    <div class="container">
        <div class="blog-two-col-layout">

            <!-- LEFT: Main Feed -->
            <div class="blog-feed-col">
                <?php if (empty($posts)): ?>
                <div class="blog-empty-state">
                    <div class="empty-icon-wrap">
                        <div class="empty-icon-ring"></div>
                        <i class="bi bi-calendar-x empty-icon"></i>
                    </div>
                    <h2 class="empty-title">Select a Month</h2>
                    <p class="empty-desc">Choose a month from the sidebar list to view published posts archive.</p>
                </div>
                <?php else: ?>
                <div class="blog-posts-grid">
                    <?php foreach ($posts as $i => $post): ?>
                    <article class="post-card" style="animation-delay:<?= $i * 0.07 ?>s">
                        <a href="<?= url('blog/' . $post['slug']) ?>" class="post-card-img-wrap">
                            <?php if ($post['image']): ?>
                                <img src="<?= uploadUrl($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="post-card-img" loading="lazy">
                            <?php else: ?>
                                <div class="post-card-img-placeholder"><i class="bi bi-journal-richtext"></i></div>
                            <?php endif; ?>
                            <div class="post-card-overlay">
                                <span class="overlay-read-btn">Read Article <i class="bi bi-arrow-right ms-1"></i></span>
                            </div>
                        </a>
                        <div class="post-card-body">
                            <div class="post-card-meta">
                                <span><i class="bi bi-calendar3 me-1"></i><?= formatDate($post['created_at']) ?></span>
                                <span class="meta-dot-sm"></span>
                                <span><i class="bi bi-clock me-1"></i><?= ($post['reading_time'] ?? 2) ?> min</span>
                            </div>
                            <h3 class="post-card-title">
                                <a href="<?= url('blog/' . $post['slug']) ?>"><?= htmlspecialchars($post['title']) ?></a>
                            </h3>
                            <p class="post-card-excerpt"><?= htmlspecialchars(truncateText($post['summary'] ?: strip_tags($post['content']), 110)) ?></p>
                            <div class="post-card-footer">
                                <div class="post-author-mini">
                                    <div class="mini-avatar"><?= strtoupper(substr($post['author_name'] ?? 'A', 0, 1)) ?></div>
                                    <span class="mini-name"><?= htmlspecialchars($post['author_name'] ?? 'Admin') ?></span>
                                </div>
                                <a href="<?= url('blog/' . $post['slug']) ?>" class="read-more-link">Read <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="blog-pagination-wrap">
                    <nav class="blog-pagination">
                        <?php 
                        $baseUrl = url('blog/archive') . '?year=' . urlencode($_GET['year'] ?? '') . '&month=' . urlencode($_GET['month'] ?? '') . '&'; 
                        ?>
                        <?php if ($page > 1): ?>
                        <a href="<?= $baseUrl ?>page=<?= $page - 1 ?>" class="pag-btn pag-prev"><i class="bi bi-chevron-left"></i> Prev</a>
                        <?php endif; ?>
                        <div class="pag-numbers">
                            <?php for ($n = 1; $n <= $totalPages; $n++): ?>
                                <?php if ($n === $page): ?><span class="pag-num active"><?= $n ?></span><?php else: ?><a href="<?= $baseUrl ?>page=<?= $n ?>" class="pag-num"><?= $n ?></a><?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <?php if ($page < $totalPages): ?>
                        <a href="<?= $baseUrl ?>page=<?= $page + 1 ?>" class="pag-btn pag-next">Next <i class="bi bi-chevron-right"></i></a>
                        <?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- RIGHT: Sidebar -->
            <aside class="blog-sidebar-col">
                <!-- Archive Months List -->
                <div class="sidebar-widget bg-white border p-4 rounded-4 shadow-sm">
                    <div class="sidebar-widget-title mb-3"><i class="bi bi-folder-fill me-2 text-primary"></i>Archive Timeline</div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($archive as $item): ?>
                        <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                            <a href="<?= url('blog/archive?year=' . $item['year'] . '&month=' . $item['month']) ?>" class="text-decoration-none text-dark small fw-bold">
                                <i class="bi bi-chevron-right me-1 text-primary small"></i> <?= htmlspecialchars($item['label']) ?>
                            </a>
                            <span class="badge bg-secondary-soft text-secondary rounded-pill"><?= $item['post_count'] ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Back Link -->
                <a href="<?= url('blog') ?>" class="sidebar-back-link d-flex align-items-center justify-content-center bg-white border p-3 rounded-4 text-decoration-none fw-bold text-secondary shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i>Back to Blog Home
                </a>
            </aside>

        </div>
    </div>
</section>

<?php if (!isset($blogCssLoaded)): $blogCssLoaded = true; ?>
<link href="<?= asset('css/blog.css') ?>" rel="stylesheet">
<?php endif; ?>
<script src="<?= asset('js/blog.js') ?>" defer></script>
