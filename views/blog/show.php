<?php
// Reading time estimate
$wordCount   = str_word_count(strip_tags($post['content']));
$readingTime = max(2, ceil($wordCount / 200));
$postUrl     = url('blog/' . $post['slug']);

// Structured JSON-LD for SEO
$jsonLd = json_encode([
    "@context" => "https://schema.org",
    "@type" => "Article",
    "headline" => $post['title'],
    "description" => $post['meta_description'] ?: ($post['summary'] ?: ''),
    "image" => $post['image'] ? uploadUrl($post['image']) : '',
    "author" => ["@type" => "Person", "name" => $post['author_name'] ?? 'Admin'],
    "publisher" => ["@type" => "Organization", "name" => APP_NAME, "logo" => ["@type" => "ImageObject", "url" => url('assets/images/logo.png')]],
    "datePublished" => $post['created_at'],
    "dateModified"  => $post['updated_at'] ?? $post['created_at'],
    "url" => $postUrl
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>

<script type="application/ld+json"><?= $jsonLd ?></script>

<!-- Blog Post Page - Premium Design -->
<div class="blog-post-page">

    <!-- Reading Progress Bar -->
    <div class="reading-progress" id="readingProgress"></div>

    <!-- Main Content Layout -->
    <div class="post-main-layout">
        <div class="post-layout-grid">
            
            <!-- Left Column - Content -->
            <div class="post-content-col">
                
                <!-- Top Article Section -->
                <div class="post-top-section">
                    <div class="post-breadcrumb">
                        <a href="<?= url('blog') ?>">Home</a>
                        <span>/</span>
                        <a href="<?= url('blog') ?>">Blog</a>
                        <?php if (!empty($post['category_name'])): ?>
                        <span>/</span>
                        <a href="<?= url('blog/category/' . $post['category_slug']) ?>"><?= htmlspecialchars($post['category_name']) ?></a>
                        <?php endif; ?>
                        <span>/</span>
                        <span><?= htmlspecialchars($post['title']) ?></span>
                    </div>
                    
                    <h1 class="post-hero-title"><?= htmlspecialchars($post['title']) ?></h1>
                    <p class="post-subtitle"><?= htmlspecialchars($post['summary'] ?? '') ?></p>
                    
                    <div class="post-meta-bar">
                        <div class="post-author-mini">
                            <div class="pac-avatar" style="width: 28px; height: 28px; font-size: 0.9rem;"><?= strtoupper(substr($post['author_name'] ?? 'A', 0, 1)) ?></div>
                            <span><?= htmlspecialchars($post['author_name'] ?? 'Admin') ?></span>
                        </div>
                        <div class="post-meta-item">
                            <i class="bi bi-calendar"></i>
                            <span><?= formatDate($post['created_at']) ?></span>
                        </div>
                        <div class="post-meta-item">
                            <i class="bi bi-clock"></i>
                            <span><?= $readingTime ?> min read</span>
                        </div>
                        <div class="post-meta-item">
                            <i class="bi bi-file-text"></i>
                            <span><?= number_format($wordCount) ?> words</span>
                        </div>
                        <div class="post-meta-item">
                            <i class="bi bi-eye"></i>
                            <span><?= number_format($post['views'] ?? 0) ?> views</span>
                        </div>
                        <div class="post-meta-item">
                            <i class="bi bi-chat"></i>
                            <span><?= count($comments) ?> comments</span>
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <?php if ($post['image']): ?>
                <div class="post-featured-image-container">
                    <img src="<?= uploadUrl($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="post-body-featured-img">
                    <div class="post-image-caption"><?= htmlspecialchars($post['image_caption'] ?? '') ?></div>
                </div>
                <?php endif; ?>

                <!-- Article Content -->
                <article class="post-content-body">
                    <?php
                    $content = $post['content'];
                    echo $content;
                    ?>

                    <!-- Unified Tags and Share Row (Small size, Tags left, Share right) -->
                    <div class="post-footer-meta">
                        <!-- Left Side: Tags -->
                        <div class="post-footer-tags">
                            <?php if (!empty($tags)): ?>
                                <span class="tags-label"><i class="bi bi-tags-fill"></i> Tags:</span>
                                <?php foreach ($tags as $tag): ?>
                                    <a href="<?= url('blog/tag/' . $tag['slug']) ?>" class="footer-tag-item"><?= htmlspecialchars($tag['name']) ?></a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Right Side: Share Buttons -->
                        <div class="post-footer-share">
                            <span class="share-label"><i class="bi bi-share-fill"></i> Share:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($postUrl) ?>" target="_blank" rel="noopener" class="share-icon-btn facebook" title="Share on Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($postUrl) ?>&text=<?= urlencode($post['title']) ?>" target="_blank" rel="noopener" class="share-icon-btn twitter" title="Share on X"><i class="bi bi-twitter-x"></i></a>
                            <a href="https://api.whatsapp.com/send?text=<?= urlencode($post['title'] . ' ' . $postUrl) ?>" target="_blank" rel="noopener" class="share-icon-btn whatsapp" title="Share on WhatsApp"><i class="bi bi-whatsapp"></i></a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode($postUrl) ?>&title=<?= urlencode($post['title']) ?>" target="_blank" rel="noopener" class="share-icon-btn linkedin" title="Share on LinkedIn"><i class="bi bi-linkedin"></i></a>
                            <button type="button" class="share-icon-btn copy" onclick="copyPostLink(this)" title="Copy Link"><i class="bi bi-link-45deg"></i></button>
                        </div>
                    </div>

                    <!-- Unified Comments Section inside content box -->
                    <div class="post-footer-comments">
                        <h4 class="comments-title"><i class="bi bi-chat-left-text-fill me-2"></i>Comments (<?= count($comments) + count($managedFeedback ?? []) ?>)</h4>
                        
                        <!-- Comment Form -->
                        <form id="commentSubmitForm" class="footer-comment-form">
                            <?= csrfField() ?>
                            <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                            <input type="text" name="website" style="display:none !important;">

                            <?php if (!isLoggedIn()): ?>
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <input type="text" name="guest_name" class="form-control form-control-sm rounded-3" placeholder="Name *" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="email" name="guest_email" class="form-control form-control-sm rounded-3" placeholder="Email (Optional)">
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="form-group mb-2">
                                <textarea name="content" rows="3" class="form-control form-control-sm rounded-3" placeholder="Write a comment here..." required></textarea>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 py-1.5 fw-semibold">
                                    Submit Comment <i class="bi bi-send-fill ms-1"></i>
                                </button>
                            </div>
                            <div id="commentFormResponse" class="mt-2 text-center small fw-semibold"></div>
                        </form>

                        <hr class="my-3">

                        <!-- Comments List -->
                        <div class="footer-comments-list">
                            <?php foreach (($managedFeedback ?? []) as $feedback): ?>
                            <div class="comment-item-compact"><div class="comment-avatar-compact"><?= strtoupper(substr($feedback['reviewer_name'], 0, 1)) ?></div><div class="comment-body-compact"><div class="comment-header-compact"><span class="comment-author-compact"><?= htmlspecialchars($feedback['reviewer_name']) ?></span><span class="comment-date-compact"><i class="bi bi-star-fill text-warning"></i> <?= number_format((float)$feedback['rating'], 1) ?></span></div><p class="comment-text-compact"><?= nl2br(htmlspecialchars($feedback['message'])) ?></p></div></div>
                            <?php endforeach; ?>
                            <?php if (empty($nestedComments)): ?>
                            <p class="text-center text-muted small py-2 m-0">No comments yet. Be the first to comment!</p>
                            <?php else: ?>
                                <?php foreach ($nestedComments as $comm): ?>
                                <div class="comment-item-compact" id="comment-<?= $comm['id'] ?>">
                                    <div class="comment-avatar-compact"><?= strtoupper(substr($comm['user_display_name'] ?: ($comm['guest_name'] ?: 'G'), 0, 1)) ?></div>
                                    <div class="comment-body-compact">
                                        <div class="comment-header-compact">
                                            <span class="comment-author-compact"><?= htmlspecialchars($comm['user_display_name'] ?: $comm['guest_name']) ?></span>
                                            <span class="comment-date-compact"><?= formatDate($comm['created_at']) ?></span>
                                        </div>
                                        <p class="comment-text-compact"><?= nl2br(htmlspecialchars($comm['content'])) ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>

                <!-- Post Navigation (outside article for clean separation) -->
                <div class="post-navigation">
                    <?php if (!empty($prevNext['prev'])): ?>
                    <a href="<?= url('blog/' . $prevNext['prev']['slug']) ?>" class="post-nav-card prev-card">
                        <h4><i class="bi bi-arrow-left me-1"></i> Previous Article</h4>
                        <h3><?= htmlspecialchars($prevNext['prev']['title']) ?></h3>
                    </a>
                    <?php else: ?>
                    <div></div>
                    <?php endif; ?>

                    <?php if (!empty($prevNext['next'])): ?>
                    <a href="<?= url('blog/' . $prevNext['next']['slug']) ?>" class="post-nav-card next-card">
                        <h4>Next Article <i class="bi bi-arrow-right ms-1"></i></h4>
                        <h3><?= htmlspecialchars($prevNext['next']['title']) ?></h3>
                    </a>
                    <?php else: ?>
                    <div></div>
                    <?php endif; ?>
                </div>

                <!-- YouTube Video Section -->
                <?php if (!empty($post['youtube_video_id'])): ?>
                <div class="post-youtube-section">
                    <h3><i class="bi bi-youtube me-2"></i>Watch Video Tutorial</h3>
                    <div class="youtube-video-wrapper">
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/<?= htmlspecialchars($post['youtube_video_id']) ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Newsletter CTA -->
                <div class="newsletter-cta">
                    <div class="newsletter-cta-text">
                        <h3>Subscribe to Our Newsletter</h3>
                        <p>Get the latest articles and tutorials delivered to your inbox.</p>
                    </div>
                    <form action="<?= url('newsletter/subscribe') ?>" method="POST">
                        <?= csrfField() ?>
                        <input type="email" name="email" placeholder="Enter your email address" required>
                        <button type="submit">Subscribe Now</button>
                    </form>
                </div>

                <!-- New Articles Section -->
                <?php if (!empty($relatedPosts)): ?>
                <section class="post-related-section">
                    <h3 class="related-title">New Articles</h3>
                    <div class="related-posts-grid">
                        <?php foreach (array_slice($relatedPosts, 0, 3) as $rel): ?>
                        <a href="<?= url('blog/' . $rel['slug']) ?>" class="related-card">
                            <?php if ($rel['image']): ?>
                            <img src="<?= uploadUrl($rel['image']) ?>" alt="<?= htmlspecialchars($rel['title']) ?>">
                            <?php else: ?>
                            <div style="width:100%;height:180px;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-file-earmark-text" style="font-size:2.5rem;color:rgba(255,255,255,0.6);"></i>
                            </div>
                            <?php endif; ?>
                            <div class="related-card-content">
                                <?php if (!empty($rel['category_name'])): ?>
                                <span style="display:inline-block;padding:3px 10px;border-radius:var(--radius-pill);background:var(--primary-soft);color:var(--primary);font-size:0.75rem;font-weight:700;margin-bottom:8px;"><?= htmlspecialchars($rel['category_name']) ?></span>
                                <?php endif; ?>
                                <h4><?= htmlspecialchars($rel['title']) ?></h4>
                                <div class="related-card-meta">
                                    <span><i class="bi bi-calendar"></i> <?= formatDate($rel['created_at']) ?></span>
                                    <span><i class="bi bi-eye"></i> <?= number_format($rel['views'] ?? 0) ?></span>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

            </div>

            <!-- Right Column - Sidebar -->
            <div class="post-sidebar-col">
                
                <!-- Search Blog (Premium Glass Design) -->
                <div class="sidebar-search-premium" id="sidebarSearchWidget">
                    <div class="search-premium-inner">
                        <div class="search-premium-icon">
                            <i class="bi bi-search"></i>
                        </div>
                        <h4>Search Blog</h4>
                        <p>Find articles, tutorials & guides</p>
                        <form action="<?= url('blog/search') ?>" method="GET" class="search-premium-form" id="sidebarSearchForm">
                            <div class="search-premium-input-wrap">
                                <i class="bi bi-search"></i>
                                <input type="text" name="q" placeholder="Type to search..." id="sidebarSearchInput" autocomplete="off">
                                <button type="submit"><i class="bi bi-arrow-right"></i></button>
                            </div>
                            <div class="sidebar-live-results" id="sidebarLiveResults"></div>
                        </form>
                    </div>
                </div>

                <!-- Follow Us (Premium Circular Design) -->
                <div class="sidebar-follow-premium">
                    <h4><i class="bi bi-heart-fill me-2"></i>Follow Us</h4>
                    <div class="follow-circles-row">
                        <a href="https://facebook.com/" target="_blank" rel="noopener" class="follow-circle fc-fb" title="Facebook">
                            <div class="follow-circle-icon"><i class="bi bi-facebook"></i></div>
                            <span class="follow-circle-label">Facebook</span>
                        </a>
                        <a href="https://instagram.com/" target="_blank" rel="noopener" class="follow-circle fc-ig" title="Instagram">
                            <div class="follow-circle-icon"><i class="bi bi-instagram"></i></div>
                            <span class="follow-circle-label">Instagram</span>
                        </a>
                        <a href="https://x.com/" target="_blank" rel="noopener" class="follow-circle fc-tw" title="X (Twitter)">
                            <div class="follow-circle-icon"><i class="bi bi-twitter-x"></i></div>
                            <span class="follow-circle-label">X</span>
                        </a>
                        <a href="https://youtube.com/" target="_blank" rel="noopener" class="follow-circle fc-yt" title="YouTube">
                            <div class="follow-circle-icon"><i class="bi bi-youtube"></i></div>
                            <span class="follow-circle-label">YouTube</span>
                        </a>
                    </div>
                </div>

                <!-- Trending Articles -->
                <div class="sidebar-widget">
                    <h4><i class="bi bi-fire me-2"></i>Trending Articles</h4>
                    <div class="trending-articles-list">
                        <?php if (!empty($trendingPosts)): ?>
                            <?php foreach ($trendingPosts as $tIdx => $trending): ?>
                            <a href="<?= url('blog/' . $trending['slug']) ?>" class="trending-item">
                                <span class="trending-rank"><?= $tIdx + 1 ?></span>
                                <?php if ($trending['image']): ?>
                                <img src="<?= uploadUrl($trending['image']) ?>" alt="<?= htmlspecialchars($trending['title']) ?>">
                                <?php endif; ?>
                                <div>
                                    <h5><?= htmlspecialchars($trending['title']) ?></h5>
                                    <span><i class="bi bi-eye"></i> <?= number_format($trending['views'] ?? 0) ?> views</span>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <p class="text-muted small">No trending articles yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Categories -->
                <div class="sidebar-widget">
                    <h4><i class="bi bi-grid me-2"></i>Categories</h4>
                    <div class="categories-list">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                            <a href="<?= url('blog/category/' . $cat['slug']) ?>" class="category-item">
                                <span><?= htmlspecialchars($cat['name']) ?></span>
                                <span class="badge"><?= $cat['post_count'] ?? 0 ?></span>
                            </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <p class="text-muted small">No categories available.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- About Author -->
                <div class="sidebar-widget">
                    <h4><i class="bi bi-person me-2"></i>About Author</h4>
                    <div class="post-author-card" style="padding: 24px; margin: 0; border: none; box-shadow: none;">
                        <div class="pac-avatar" style="width: 60px; height: 60px; font-size: 1.5rem;"><?= strtoupper(substr($post['author_name'] ?? 'A', 0, 1)) ?></div>
                        <div>
                            <div class="pac-name" style="font-size: 1.1rem;"><?= htmlspecialchars($post['author_name'] ?? 'Admin') ?></div>
                            <div class="pac-bio" style="font-size: 0.9rem;">A passionate writer and developer sharing professional insights on <?= APP_NAME ?>.</div>
                        </div>
                    </div>
                </div>



                <!-- Advertisement -->
                <div class="sidebar-widget">
                    <div class="ad-space">
                        Advertisement (300x250)
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
// Reading Progress Bar
window.addEventListener('scroll', function() {
    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = (winScroll / height) * 100;
    document.getElementById('readingProgress').style.width = scrolled + '%';
});

// Copy Post Link Function
function copyPostLink(btn) {
    const url = '<?= $postUrl ?>';
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            const icon = btn.querySelector('i');
            if (icon) {
                icon.className = 'bi bi-check-lg text-success';
                setTimeout(() => { icon.className = 'bi bi-link-45deg'; }, 2000);
            }
        });
    } else {
        const el = document.createElement('textarea');
        el.value = url;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        alert('Link copied!');
    }
}

// Live Search in Sidebar
(function() {
    const searchInput = document.getElementById('sidebarSearchInput');
    const resultsBox = document.getElementById('sidebarLiveResults');
    let debounceTimer;

    if (!searchInput || !resultsBox) return;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (q.length < 2) {
            resultsBox.classList.remove('active');
            resultsBox.innerHTML = '';
            return;
        }
        debounceTimer = setTimeout(() => {
            fetch('<?= url('blog/api/search') ?>?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(data => {
                    if (data.posts && data.posts.length > 0) {
                        let html = '';
                        data.posts.forEach(p => {
                            html += `<a href="${p.url}" class="live-result-item">`;
                            if (p.image) {
                                html += `<img src="${p.image}" alt="">`;
                            } else {
                                html += `<div style="width:50px;height:50px;border-radius:8px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="bi bi-file-text" style="color:var(--muted);"></i></div>`;
                            }
                            html += `<div class="lr-info"><h5>${p.title}</h5><span>${p.category ? p.category + ' · ' : ''}${p.date}</span></div>`;
                            html += `</a>`;
                        });
                        resultsBox.innerHTML = html;
                        resultsBox.classList.add('active');
                    } else {
                        resultsBox.innerHTML = '<div style="padding:16px;text-align:center;color:var(--muted);font-size:0.88rem;">No results found</div>';
                        resultsBox.classList.add('active');
                    }
                })
                .catch(() => {
                    resultsBox.classList.remove('active');
                });
        }, 300);
    });

    // Close results on click outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
            resultsBox.classList.remove('active');
        }
    });
})();

// Comment Form Submission
document.addEventListener("DOMContentLoaded", function() {
    const commentForm = document.getElementById('commentSubmitForm');
    const commentResponse = document.getElementById('commentFormResponse');
    
    if (commentForm && commentResponse) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            commentResponse.textContent = "Submitting comment...";
            commentResponse.className = "mt-2 text-center small fw-semibold text-muted";
            
            const formData = new FormData(commentForm);
            fetch('<?= url('blog/comment/store') ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    commentResponse.textContent = data.message || "Comment submitted successfully!";
                    commentResponse.className = "mt-2 text-center small fw-semibold text-success";
                    commentForm.reset();
                    // Reload page after a short delay to display the new comment
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    commentResponse.textContent = data.message || "Failed to submit comment.";
                    commentResponse.className = "mt-2 text-center small fw-semibold text-danger";
                }
            })
            .catch(err => {
                commentResponse.textContent = "Error occurred. Please try again.";
                commentResponse.className = "mt-2 text-center small fw-semibold text-danger";
            });
        });
    }
});
</script>
