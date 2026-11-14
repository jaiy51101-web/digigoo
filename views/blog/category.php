<?php
/**
 * DigiWS — Premium Blog Category Archive Page
 * Variables: $category, $posts, $totalPosts, $page, $perPage, $totalPages,
 *            $allCategories, $popularTags, $trendingPosts, $popularCategories
 */
$totalPages = isset($totalPages) ? $totalPages : (int)ceil(($totalPosts ?? 0) / ($perPage ?? 9));
$catColor   = $category['color'] ?? '#2563EB';
$catIcon    = $category['icon']  ?? 'bi-folder';
$catName    = $category['name']  ?? 'Category';
$catDesc    = $category['description'] ?? '';

$catGradients = [
    'seo-tips'          => 'linear-gradient(135deg,#10B981,#059669)',
    'ai-tools'          => 'linear-gradient(135deg,#6366F1,#4F46E5)',
    'wordpress'         => 'linear-gradient(135deg,#2563EB,#1D4ED8)',
    'hosting-servers'   => 'linear-gradient(135deg,#F59E0B,#D97706)',
    'programming'       => 'linear-gradient(135deg,#EF4444,#DC2626)',
    'digital-marketing' => 'linear-gradient(135deg,#8B5CF6,#7C3AED)',
    'web-development'   => 'linear-gradient(135deg,#06B6D4,#0891B2)',
    'blogging'          => 'linear-gradient(135deg,#EC4899,#BE185D)',
    'design-ui'         => 'linear-gradient(135deg,#F97316,#EA580C)',
    'php-tutorials'     => 'linear-gradient(135deg,#7C3AED,#6D28D9)',
    'buying-guides'     => 'linear-gradient(135deg,#0EA5E9,#0284C7)',
    'digital-products'  => 'linear-gradient(135deg,#14B8A6,#0D9488)',
];
$heroGradient = $catGradients[$category['slug'] ?? ''] ?? 'linear-gradient(135deg,#2563EB,#4F46E5)';
?>

<!-- Reading Progress Bar -->
<div class="reading-progress-container"><div class="reading-progress-bar" id="readingProgress"></div></div>

<div class="blog-page">

<!-- ══════════════════════════════════════════════════════════ -->
<!-- CATEGORY HERO                                             -->
<!-- ══════════════════════════════════════════════════════════ -->
<section class="cat-archive-hero" style="background:<?= $heroGradient ?>;">
  <div class="cat-hero-glow"></div>
  <div class="blog-container">
    <div class="cat-hero-inner">
      <!-- Breadcrumb -->
      <nav class="cat-breadcrumb">
        <a href="<?= url('blog') ?>"><i class="bi bi-house-fill"></i> Blog</a>
        <i class="bi bi-chevron-right"></i>
        <span>Category</span>
        <i class="bi bi-chevron-right"></i>
        <span><?= htmlspecialchars($catName) ?></span>
      </nav>
      <!-- Content -->
      <div class="cat-hero-content animate-fade-up">
        <div class="cat-hero-icon-wrap">
          <i class="<?= htmlspecialchars($catIcon) ?>"></i>
        </div>
        <h1 class="cat-hero-title"><?= htmlspecialchars($catName) ?></h1>
        <?php if ($catDesc): ?>
        <p class="cat-hero-desc"><?= htmlspecialchars($catDesc) ?></p>
        <?php endif; ?>
        <div class="cat-hero-stats">
          <div class="cat-stat"><i class="bi bi-journals"></i> <?= number_format($totalPosts) ?> Articles</div>
          <div class="cat-stat-div"></div>
          <div class="cat-stat"><i class="bi bi-people-fill"></i> 50,000+ Readers</div>
          <div class="cat-stat-div"></div>
          <div class="cat-stat"><i class="bi bi-star-fill"></i> Expert Verified</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- ALL CATEGORIES STRIP                                      -->
<!-- ══════════════════════════════════════════════════════════ -->
<?php if (!empty($allCategories)): ?>
<div class="cat-filter-strip">
  <div class="blog-container">
    <div class="cat-strip-scroll">
      <a href="<?= url('blog') ?>" class="cat-strip-pill">
        <i class="bi bi-grid-3x3-gap"></i> All
      </a>
      <?php foreach ($allCategories as $c): ?>
      <a href="<?= url('blog/category/' . $c['slug']) ?>"
         class="cat-strip-pill <?= $c['slug'] === ($category['slug'] ?? '') ? 'active' : '' ?>"
         style="<?= $c['slug'] === ($category['slug'] ?? '') ? "--strip-color:{$c['color']};" : '' ?>">
        <i class="<?= htmlspecialchars($c['icon'] ?? 'bi-folder') ?>"></i>
        <?= htmlspecialchars($c['name']) ?>
        <?php if (!empty($c['post_count'])): ?>
        <span class="cat-strip-count"><?= $c['post_count'] ?></span>
        <?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- MAIN CONTENT + SIDEBAR                                    -->
<!-- ══════════════════════════════════════════════════════════ -->
<section class="latest-section">
  <div class="blog-container">
    <div class="latest-layout">

      <!-- ── MAIN FEED ────────────────────────────────────── -->
      <div>
        <!-- Result header -->
        <div class="archive-result-header">
          <div>
            <h2 class="archive-result-title">
              <i class="<?= htmlspecialchars($catIcon) ?>" style="color:<?= htmlspecialchars($catColor) ?>;"></i>
              <?= htmlspecialchars($catName) ?> Articles
            </h2>
            <p class="archive-result-meta"><?= number_format($totalPosts) ?> articles found</p>
          </div>
          <div class="archive-sort">
            <label for="catSortSelect" class="visually-hidden">Sort by</label>
            <select id="catSortSelect" class="archive-sort-select" onchange="window.location=this.value">
              <option value="<?= url('blog/category/' . ($category['slug'] ?? '')) ?>">Latest First</option>
              <option value="<?= url('blog/category/' . ($category['slug'] ?? '')) ?>?order=views">Most Popular</option>
              <option value="<?= url('blog/category/' . ($category['slug'] ?? '')) ?>?order=trending">Trending</option>
            </select>
          </div>
        </div>

        <!-- Posts -->
        <?php if (empty($posts)): ?>
        <div class="empty-state">
          <i class="bi bi-journal-x"></i>
          <h5>No Articles Yet</h5>
          <p>We haven't published in <strong><?= htmlspecialchars($catName) ?></strong> yet. Check back soon!</p>
          <a href="<?= url('blog') ?>" class="btn-dg btn-dg-primary btn-dg-sm mt-3">
            <i class="bi bi-arrow-left"></i> Browse All Articles
          </a>
        </div>
        <?php else: ?>
        <div class="posts-grid">
          <?php foreach ($posts as $i => $post):
            $wc  = str_word_count(strip_tags($post['content'] ?? ''));
            $rt  = max(2, ceil($wc / 200));
            $pUrl = url('blog/' . $post['slug']);
            $pCatColor = $post['category_color'] ?? $catColor;
            $authorName = $post['author_name'] ?? 'Deepak Kumar';
          ?>
          <article class="post-card animate-fade-up" id="post-card-<?= $post['id'] ?>" style="animation-delay:<?= $i * 0.06 ?>s">
            <!-- Thumbnail -->
            <a href="<?= $pUrl ?>" class="post-card-thumb">
              <?php if (!empty($post['image'])): ?>
                <img src="<?= uploadUrl($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
              <?php else: ?>
                <div class="post-card-thumb-fallback">
                  <i class="bi bi-journal-richtext" style="color:<?= htmlspecialchars($pCatColor) ?>;opacity:0.4;font-size:2.5rem;"></i>
                </div>
              <?php endif; ?>
              <span class="post-cat-chip" style="color:<?= htmlspecialchars($pCatColor) ?>;border:1px solid <?= htmlspecialchars($pCatColor) ?>33;background:<?= htmlspecialchars($pCatColor) ?>15;">
                <i class="<?= htmlspecialchars($catIcon) ?> me-1"></i><?= htmlspecialchars($catName) ?>
              </span>
              <?php if (!empty($post['is_featured'])): ?>
              <span style="position:absolute;top:10px;right:10px;background:linear-gradient(135deg,#F59E0B,#F97316);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 9px;border-radius:999px;">
                <i class="bi bi-star-fill me-1"></i>Featured
              </span>
              <?php elseif (!empty($post['is_trending'])): ?>
              <span style="position:absolute;top:10px;right:10px;background:linear-gradient(135deg,#EF4444,#DC2626);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 9px;border-radius:999px;">
                <i class="bi bi-fire me-1"></i>Trending
              </span>
              <?php endif; ?>
            </a>
            <!-- Body -->
            <div class="post-card-body">
              <h3><a href="<?= $pUrl ?>"><?= htmlspecialchars($post['title']) ?></a></h3>
              <p><?= htmlspecialchars(truncateText($post['summary'] ?: strip_tags($post['content']), 120)) ?></p>
              <div class="post-card-meta">
                <div class="author-mini">
                  <div class="author-mini-av" style="background:<?= $heroGradient ?>;"><?= strtoupper(substr($authorName, 0, 1)) ?></div>
                  <span><?= htmlspecialchars($authorName) ?></span>
                </div>
                <span class="meta-sep">·</span>
                <span class="meta-chip"><i class="bi bi-calendar3"></i> <?= formatDate($post['created_at']) ?></span>
                <span class="meta-sep">·</span>
                <span class="meta-chip"><i class="bi bi-clock"></i> <?= $rt ?> min</span>
                <?php if (!empty($post['views'])): ?>
                <span class="meta-sep">·</span>
                <span class="meta-chip"><i class="bi bi-eye"></i> <?= number_format($post['views']) ?></span>
                <?php endif; ?>
              </div>
              <div class="post-card-actions">
                <div class="action-btns">
                  <button type="button" class="action-btn" onclick="quickReact(<?= $post['id'] ?>, 'like', this)" title="Like">
                    <i class="bi bi-hand-thumbs-up"></i>
                  </button>
                  <button type="button" class="action-btn" onclick="quickBookmark(<?= $post['id'] ?>, this)" title="Bookmark">
                    <i class="bi bi-bookmark"></i>
                  </button>
                  <button type="button" class="action-btn" onclick="quickShare('<?= $pUrl ?>','<?= htmlspecialchars(addslashes($post['title'])) ?>')" title="Share">
                    <i class="bi bi-share"></i>
                  </button>
                </div>
                <a href="<?= $pUrl ?>" class="read-link">Read more <i class="bi bi-arrow-right"></i></a>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="archive-pagination">
          <nav>
            <?php $baseUrl = url('blog/category/' . $category['slug']) . '?'; ?>
            <?php if ($page > 1): ?>
            <a href="<?= $baseUrl ?>page=<?= $page - 1 ?>" class="pag-btn">
              <i class="bi bi-chevron-left"></i> Prev
            </a>
            <?php endif; ?>
            <div class="pag-numbers">
              <?php for ($n = max(1,$page-2); $n <= min($totalPages,$page+2); $n++): ?>
              <?php if ($n === $page): ?>
              <span class="pag-num active"><?= $n ?></span>
              <?php else: ?>
              <a href="<?= $baseUrl ?>page=<?= $n ?>" class="pag-num"><?= $n ?></a>
              <?php endif; ?>
              <?php endfor; ?>
            </div>
            <?php if ($page < $totalPages): ?>
            <a href="<?= $baseUrl ?>page=<?= $page + 1 ?>" class="pag-btn">
              Next <i class="bi bi-chevron-right"></i>
            </a>
            <?php endif; ?>
          </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
      </div>

      <!-- ── SIDEBAR ──────────────────────────────────────── -->
      <aside class="sidebar">

        <!-- Search -->
        <div class="sidebar-card">
          <div class="sidebar-card-title"><i class="bi bi-search"></i> Search Blog</div>
          <div style="position:relative;">
            <div class="sidebar-search">
              <i class="bi bi-search"></i>
              <input type="text" class="live-search-input" placeholder="Search articles..." autocomplete="off">
            </div>
            <div class="live-search-results"></div>
          </div>
        </div>

        <!-- All Categories -->
        <?php if (!empty($allCategories)): ?>
        <div class="sidebar-card">
          <div class="sidebar-card-title"><i class="bi bi-grid-3x3-gap"></i> All Categories</div>
          <div class="sidebar-cat-list">
            <?php foreach ($allCategories as $c): ?>
            <a href="<?= url('blog/category/' . $c['slug']) ?>"
               class="sidebar-cat-item <?= $c['slug'] === ($category['slug'] ?? '') ? 'active-cat' : '' ?>">
              <span>
                <i class="<?= htmlspecialchars($c['icon'] ?? 'bi-folder') ?> me-2" style="color:<?= htmlspecialchars($c['color'] ?? '#2563EB') ?>;"></i>
                <?= htmlspecialchars($c['name']) ?>
              </span>
              <span class="cat-badge"><?= $c['post_count'] ?? 0 ?></span>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Trending Posts -->
        <?php if (!empty($trendingPosts)): ?>
        <div class="sidebar-card">
          <div class="sidebar-card-title"><i class="bi bi-fire text-danger"></i> Trending</div>
          <div class="trend-list">
            <?php foreach (array_slice($trendingPosts, 0, 5) as $i => $tp): ?>
            <a href="<?= url('blog/' . $tp['slug']) ?>" class="trend-item">
              <div class="trend-rank <?= $i < 3 ? 'top' : '' ?>"><?= str_pad($i+1,2,'0',STR_PAD_LEFT) ?></div>
              <div class="trend-thumb"><i class="bi bi-journal-text"></i></div>
              <div class="trend-info">
                <h4><?= htmlspecialchars($tp['title']) ?></h4>
                <span><i class="bi bi-eye me-1"></i><?= number_format($tp['views'] ?? 0) ?></span>
              </div>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Popular Tags -->
        <?php if (!empty($popularTags)): ?>
        <div class="sidebar-card">
          <div class="sidebar-card-title"><i class="bi bi-tags"></i> Popular Tags</div>
          <div class="sidebar-tags">
            <?php foreach (array_slice($popularTags, 0, 15) as $tag): ?>
            <a href="<?= url('blog/tag/' . $tag['slug']) ?>" class="sidebar-tag">#<?= htmlspecialchars($tag['name']) ?></a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Newsletter Mini -->
        <div class="sidebar-nl">
          <h4><i class="bi bi-envelope-open-fill me-2"></i>Newsletter</h4>
          <p>Get weekly tutorials and guides delivered to your inbox.</p>
          <form class="sidebar-nl-form" action="<?= url('newsletter/subscribe') ?>" method="POST">
            <input type="email" name="email" placeholder="Your email address" required>
            <button type="submit">Subscribe Free</button>
          </form>
          <div class="sidebar-nl-note"><i class="bi bi-shield-check me-1"></i>50,000+ subscribers · No spam</div>
        </div>

        <!-- Back to Blog -->
        <a href="<?= url('blog') ?>" class="btn-dg btn-dg-outline" style="width:100%;justify-content:center;">
          <i class="bi bi-arrow-left"></i> Back to Blog Home
        </a>

      </aside>
    </div>
  </div>
</section>

</div><!-- .blog-page -->

<script>
// Reading progress
window.addEventListener('scroll', function() {
  const h = document.documentElement;
  const pct = (h.scrollTop || document.body.scrollTop) / (h.scrollHeight - h.clientHeight) * 100;
  const bar = document.getElementById('readingProgress');
  if (bar) bar.style.width = Math.min(100, pct) + '%';
});

// Scroll-in animations
(function() {
  const obs = new IntersectionObserver(function(entries) {
    entries.forEach(function(e) {
      if (e.isIntersecting) { e.target.classList.add('in-view'); obs.unobserve(e.target); }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });
  document.querySelectorAll('.animate-fade-up').forEach(function(el) { obs.observe(el); });
})();

// Live Search
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.live-search-input').forEach(function(input) {
    const resultsEl = input.closest('.sidebar-card, div[style]')?.querySelector('.live-search-results');
    if (!resultsEl) return;
    let timer;
    input.addEventListener('input', function() {
      const q = this.value.trim();
      clearTimeout(timer);
      if (q.length < 2) { resultsEl.classList.remove('show'); return; }
      timer = setTimeout(function() {
        fetch(window.appUrl('api/blog/search?q=' + encodeURIComponent(q)))
          .then(function(r) { return r.json(); })
          .then(function(data) {
            if (!data.posts || !data.posts.length) {
              resultsEl.innerHTML = '<div style="padding:14px;text-align:center;color:var(--muted);font-size:0.83rem;">No articles found.</div>';
            } else {
              resultsEl.innerHTML = data.posts.map(function(p) {
                return '<a href="' + p.url + '" class="search-suggestion-item">'
                  + '<div class="suggestion-thumb"><i class="bi bi-journal"></i></div>'
                  + '<div class="suggestion-info"><div class="suggestion-title">' + p.title + '</div>'
                  + '<div class="suggestion-meta">' + (p.category || '') + '</div></div></a>';
              }).join('');
            }
            resultsEl.classList.add('show');
          }).catch(function() {});
      }, 280);
    });
    document.addEventListener('click', function(e) {
      if (!input.contains(e.target) && !resultsEl.contains(e.target)) resultsEl.classList.remove('show');
    });
  });
});

function quickBookmark(postId, el) {
  if (!<?= isLoggedIn() ? 'true' : 'false' ?>) { window.location.href = window.appUrl('login'); return; }
  fetch(window.appUrl('api/blog/bookmark/toggle'), { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'post_id=' + postId })
    .then(function(r) { return r.json(); }).then(function(d) {
      if (d.success) { const i = el.querySelector('i'); if (i) i.className = d.bookmarked ? 'bi bi-bookmark-fill text-primary' : 'bi bi-bookmark'; }
    });
}
function quickReact(postId, type, el) {
  fetch(window.appUrl('api/blog/react'), { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'post_id=' + postId + '&reaction=' + type })
    .then(function(r) { return r.json(); }).then(function(d) {
      if (d.success) { d.change > 0 ? el.classList.add('active') : el.classList.remove('active'); }
    });
}
function quickShare(url, title) {
  if (navigator.share) { navigator.share({ title: title, url: url }).catch(function() {}); }
  else {
    navigator.clipboard.writeText(url).then(function() {
      const t = document.createElement('div');
      t.textContent = '✓ Link copied!';
      t.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#111827;color:#fff;padding:10px 22px;border-radius:999px;font-size:0.85rem;font-weight:600;z-index:99999;';
      document.body.appendChild(t); setTimeout(function() { t.remove(); }, 2200);
    });
  }
}
</script>
