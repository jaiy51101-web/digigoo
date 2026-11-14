<?php
/**
 * DigiWS — Premium Blog Tag Archive Page
 * Variables: $tag, $posts, $totalPosts, $page, $perPage, $totalPages, $popularTags, $trendingPosts
 */
$totalPages = isset($totalPages) ? $totalPages : (int)ceil(($totalPosts ?? 0) / ($perPage ?? 9));
$tagName    = $tag['name'] ?? 'Tag';
$tagSlug    = $tag['slug'] ?? '';
?>

<!-- Reading Progress Bar -->
<div class="reading-progress-container"><div class="reading-progress-bar" id="readingProgress"></div></div>

<div class="blog-page">

<!-- ══════════════════════════════════════════════════════════ -->
<!-- TAG HERO                                                  -->
<!-- ══════════════════════════════════════════════════════════ -->
<section class="cat-archive-hero" style="background:linear-gradient(135deg,#1E1B4B 0%,#312E81 50%,#0F172A 100%);">
  <div class="cat-hero-glow"></div>
  <div class="blog-container">
    <div class="cat-hero-inner">
      <!-- Breadcrumb -->
      <nav class="cat-breadcrumb">
        <a href="<?= url('blog') ?>"><i class="bi bi-house-fill"></i> Blog</a>
        <i class="bi bi-chevron-right"></i>
        <span>Tags</span>
        <i class="bi bi-chevron-right"></i>
        <span>#<?= htmlspecialchars($tagName) ?></span>
      </nav>
      <!-- Content -->
      <div class="cat-hero-content animate-fade-up">
        <div class="cat-hero-icon-wrap" style="background:rgba(255,255,255,0.15);">
          <i class="bi bi-tag-fill"></i>
        </div>
        <h1 class="cat-hero-title">#<?= htmlspecialchars($tagName) ?></h1>
        <p class="cat-hero-desc">Browse all articles tagged with <strong>#<?= htmlspecialchars($tagName) ?></strong></p>
        <div class="cat-hero-stats">
          <div class="cat-stat"><i class="bi bi-journals"></i> <?= number_format($totalPosts) ?> Articles</div>
          <div class="cat-stat-div"></div>
          <div class="cat-stat"><i class="bi bi-people-fill"></i> 50,000+ Readers</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- POPULAR TAGS STRIP                                        -->
<!-- ══════════════════════════════════════════════════════════ -->
<?php if (!empty($popularTags)): ?>
<div class="cat-filter-strip">
  <div class="blog-container">
    <div class="cat-strip-scroll">
      <a href="<?= url('blog') ?>" class="cat-strip-pill"><i class="bi bi-house"></i> Blog Home</a>
      <?php foreach (array_slice($popularTags, 0, 14) as $pt): ?>
      <a href="<?= url('blog/tag/' . $pt['slug']) ?>"
         class="cat-strip-pill <?= $pt['slug'] === $tagSlug ? 'active' : '' ?>">
        #<?= htmlspecialchars($pt['name']) ?>
        <?php if (!empty($pt['post_count'])): ?>
        <span class="cat-strip-count"><?= $pt['post_count'] ?></span>
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
        <div class="archive-result-header">
          <div>
            <h2 class="archive-result-title">
              <i class="bi bi-tag-fill" style="color:#6366F1;"></i>
              #<?= htmlspecialchars($tagName) ?> Articles
            </h2>
            <p class="archive-result-meta"><?= number_format($totalPosts) ?> articles found</p>
          </div>
          <a href="<?= url('blog') ?>" class="btn-dg btn-dg-outline btn-dg-sm">
            <i class="bi bi-arrow-left"></i> All Articles
          </a>
        </div>

        <?php if (empty($posts)): ?>
        <div class="empty-state">
          <i class="bi bi-tag-fill"></i>
          <h5>No Articles Yet</h5>
          <p>No articles are tagged with <strong>#<?= htmlspecialchars($tagName) ?></strong> yet.</p>
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
            $pCatColor = $post['category_color'] ?? '#6366F1';
            $authorName = $post['author_name'] ?? 'Deepak Kumar';
          ?>
          <article class="post-card animate-fade-up" id="post-card-<?= $post['id'] ?>" style="animation-delay:<?= $i * 0.06 ?>s">
            <a href="<?= $pUrl ?>" class="post-card-thumb">
              <?php if (!empty($post['image'])): ?>
                <img src="<?= uploadUrl($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
              <?php else: ?>
                <div class="post-card-thumb-fallback">
                  <i class="bi bi-journal-richtext" style="color:<?= htmlspecialchars($pCatColor) ?>;opacity:0.4;font-size:2.5rem;"></i>
                </div>
              <?php endif; ?>
              <span class="post-cat-chip" style="color:<?= htmlspecialchars($pCatColor) ?>;border:1px solid <?= htmlspecialchars($pCatColor) ?>33;background:<?= htmlspecialchars($pCatColor) ?>15;">
                <?php if (!empty($post['category_name'])): ?>
                <i class="<?= htmlspecialchars($post['category_icon'] ?? 'bi-tag') ?> me-1"></i><?= htmlspecialchars($post['category_name']) ?>
                <?php else: ?>
                <i class="bi bi-tag-fill me-1"></i>#<?= htmlspecialchars($tagName) ?>
                <?php endif; ?>
              </span>
              <?php if (!empty($post['is_trending'])): ?>
              <span style="position:absolute;top:10px;right:10px;background:linear-gradient(135deg,#EF4444,#DC2626);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 9px;border-radius:999px;">
                <i class="bi bi-fire me-1"></i>Trending
              </span>
              <?php endif; ?>
            </a>
            <div class="post-card-body">
              <h3><a href="<?= $pUrl ?>"><?= htmlspecialchars($post['title']) ?></a></h3>
              <p><?= htmlspecialchars(truncateText($post['summary'] ?: strip_tags($post['content']), 120)) ?></p>
              <div class="post-card-meta">
                <div class="author-mini">
                  <div class="author-mini-av"><?= strtoupper(substr($authorName, 0, 1)) ?></div>
                  <span><?= htmlspecialchars($authorName) ?></span>
                </div>
                <span class="meta-sep">·</span>
                <span class="meta-chip"><i class="bi bi-calendar3"></i> <?= formatDate($post['created_at']) ?></span>
                <span class="meta-sep">·</span>
                <span class="meta-chip"><i class="bi bi-clock"></i> <?= $rt ?> min</span>
              </div>
              <div class="post-card-actions">
                <div class="action-btns">
                  <button type="button" class="action-btn" onclick="quickReact(<?= $post['id'] ?>,'like',this)" title="Like"><i class="bi bi-hand-thumbs-up"></i></button>
                  <button type="button" class="action-btn" onclick="quickBookmark(<?= $post['id'] ?>,this)" title="Bookmark"><i class="bi bi-bookmark"></i></button>
                  <button type="button" class="action-btn" onclick="quickShare('<?= $pUrl ?>','<?= htmlspecialchars(addslashes($post['title'])) ?>')" title="Share"><i class="bi bi-share"></i></button>
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
            <?php $baseUrl = url('blog/tag/' . $tagSlug) . '?'; ?>
            <?php if ($page > 1): ?>
            <a href="<?= $baseUrl ?>page=<?= $page - 1 ?>" class="pag-btn"><i class="bi bi-chevron-left"></i> Prev</a>
            <?php endif; ?>
            <div class="pag-numbers">
              <?php for ($n = max(1,$page-2); $n <= min($totalPages,$page+2); $n++): ?>
              <?php if ($n === $page): ?><span class="pag-num active"><?= $n ?></span>
              <?php else: ?><a href="<?= $baseUrl ?>page=<?= $n ?>" class="pag-num"><?= $n ?></a><?php endif; ?>
              <?php endfor; ?>
            </div>
            <?php if ($page < $totalPages): ?>
            <a href="<?= $baseUrl ?>page=<?= $page + 1 ?>" class="pag-btn">Next <i class="bi bi-chevron-right"></i></a>
            <?php endif; ?>
          </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
      </div>

      <!-- ── SIDEBAR ──────────────────────────────────────── -->
      <aside class="sidebar">
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

        <?php if (!empty($popularTags)): ?>
        <div class="sidebar-card">
          <div class="sidebar-card-title"><i class="bi bi-tags"></i> All Tags</div>
          <div class="sidebar-tags">
            <?php foreach ($popularTags as $pt): ?>
            <a href="<?= url('blog/tag/' . $pt['slug']) ?>"
               class="sidebar-tag <?= $pt['slug'] === $tagSlug ? 'active-tag' : '' ?>">
              #<?= htmlspecialchars($pt['name']) ?>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <div class="sidebar-nl">
          <h4><i class="bi bi-envelope-open-fill me-2"></i>Newsletter</h4>
          <p>Get weekly tutorials and guides delivered to your inbox.</p>
          <form class="sidebar-nl-form" action="<?= url('newsletter/subscribe') ?>" method="POST">
            <input type="email" name="email" placeholder="Your email address" required>
            <button type="submit">Subscribe Free</button>
          </form>
          <div class="sidebar-nl-note"><i class="bi bi-shield-check me-1"></i>50,000+ subscribers · No spam</div>
        </div>

        <a href="<?= url('blog') ?>" class="btn-dg btn-dg-outline" style="width:100%;justify-content:center;">
          <i class="bi bi-arrow-left"></i> Back to Blog Home
        </a>
      </aside>
    </div>
  </div>
</section>

</div><!-- .blog-page -->

<script>
window.addEventListener('scroll', function() {
  const h = document.documentElement;
  const pct = (h.scrollTop || document.body.scrollTop) / (h.scrollHeight - h.clientHeight) * 100;
  const bar = document.getElementById('readingProgress');
  if (bar) bar.style.width = Math.min(100, pct) + '%';
});
(function() {
  const obs = new IntersectionObserver(function(entries) {
    entries.forEach(function(e) { if (e.isIntersecting) { e.target.classList.add('in-view'); obs.unobserve(e.target); } });
  }, { threshold: 0.1 });
  document.querySelectorAll('.animate-fade-up').forEach(function(el) { obs.observe(el); });
})();
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.live-search-input').forEach(function(input) {
    const resultsEl = input.parentElement.querySelector('.live-search-results');
    if (!resultsEl) return;
    let timer;
    input.addEventListener('input', function() {
      const q = this.value.trim();
      clearTimeout(timer);
      if (q.length < 2) { resultsEl.classList.remove('show'); return; }
      timer = setTimeout(function() {
        fetch(window.appUrl('api/blog/search?q=' + encodeURIComponent(q)))
          .then(function(r) { return r.json(); })
          .then(function(d) {
            resultsEl.innerHTML = (!d.posts || !d.posts.length)
              ? '<div style="padding:14px;text-align:center;color:var(--muted);font-size:0.83rem;">No articles found.</div>'
              : d.posts.map(function(p) {
                  return '<a href="' + p.url + '" class="search-suggestion-item"><div class="suggestion-thumb"><i class="bi bi-journal"></i></div><div class="suggestion-info"><div class="suggestion-title">' + p.title + '</div></div></a>';
                }).join('');
            resultsEl.classList.add('show');
          }).catch(function() {});
      }, 280);
    });
    document.addEventListener('click', function(e) { if (!input.contains(e.target) && !resultsEl.contains(e.target)) resultsEl.classList.remove('show'); });
  });
});
function quickBookmark(postId, el) {
  if (!<?= isLoggedIn() ? 'true' : 'false' ?>) { window.location.href = window.appUrl('login'); return; }
  fetch(window.appUrl('api/blog/bookmark/toggle'), { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'post_id=' + postId })
    .then(function(r) { return r.json(); }).then(function(d) { if (d.success) { const i = el.querySelector('i'); if (i) i.className = d.bookmarked ? 'bi bi-bookmark-fill text-primary' : 'bi bi-bookmark'; } });
}
function quickReact(postId, type, el) {
  fetch(window.appUrl('api/blog/react'), { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: 'post_id=' + postId + '&reaction=' + type })
    .then(function(r) { return r.json(); }).then(function(d) { if (d.success) { d.change > 0 ? el.classList.add('active') : el.classList.remove('active'); } });
}
function quickShare(url, title) {
  if (navigator.share) { navigator.share({ title: title, url: url }).catch(function() {}); }
  else { navigator.clipboard.writeText(url).then(function() { const t = document.createElement('div'); t.textContent = '✓ Link copied!'; t.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#111827;color:#fff;padding:10px 22px;border-radius:999px;font-size:0.85rem;font-weight:600;z-index:99999;'; document.body.appendChild(t); setTimeout(function() { t.remove(); }, 2200); }); }
}
</script>
