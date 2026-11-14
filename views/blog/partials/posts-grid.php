<?php
/**
 * DigiWS — Premium Post Cards Grid Partial
 * Variables: $posts
 */
if (!empty($posts)):
    $db = Database::getInstance();
    foreach ($posts as $post):
        $wc            = str_word_count(strip_tags($post['content'] ?? ''));
        $readingTime   = max(2, ceil($wc / 200));
        $postUrl       = url('blog/' . $post['slug']);
        $catColor      = $post['category_color'] ?? '#2563EB';
        $catName       = $post['category_name'] ?? 'General';
        $catIcon       = $post['category_icon'] ?? 'bi-tag';
        $authorName    = $post['author_name'] ?? 'Deepak Kumar';
        $authorInitial = strtoupper(substr($authorName, 0, 1));
        $isBookmarked  = isLoggedIn() ? (new BlogBookmark())->isBookmarked(currentUserId(), $post['id']) : false;
        $commentsCount = (int)$db->fetchColumn("SELECT COUNT(*) FROM blog_comments WHERE post_id = ? AND status = 'approved'", [$post['id']]);
?>
<article class="post-card animate-fade-up" id="post-card-<?= $post['id'] ?>">

  <!-- Thumbnail -->
  <a href="<?= $postUrl ?>" class="post-card-thumb">
    <?php if (!empty($post['image'])): ?>
      <img src="<?= uploadUrl($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
    <?php else: ?>
      <div class="post-card-thumb-fallback">
        <i class="bi bi-journal-richtext" style="color:<?= htmlspecialchars($catColor) ?>;opacity:0.45;font-size:2.5rem;"></i>
      </div>
    <?php endif; ?>
    <!-- Category chip -->
    <span class="post-cat-chip" style="color:<?= htmlspecialchars($catColor) ?>;border:1px solid <?= htmlspecialchars($catColor) ?>33;background:<?= htmlspecialchars($catColor) ?>15;">
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
    <h3><a href="<?= $postUrl ?>"><?= htmlspecialchars($post['title']) ?></a></h3>
    <p><?= htmlspecialchars(truncateText($post['summary'] ?: strip_tags($post['content']), 120)) ?></p>

    <!-- Author + meta -->
    <div class="post-card-meta">
      <div class="author-mini">
        <div class="author-mini-av" style="background:linear-gradient(135deg,<?= htmlspecialchars($catColor) ?>,#4F46E5);"><?= $authorInitial ?></div>
        <span><?= htmlspecialchars($authorName) ?></span>
      </div>
      <span class="meta-sep">·</span>
      <span class="meta-chip"><i class="bi bi-calendar3"></i> <?= formatDate($post['created_at']) ?></span>
      <span class="meta-sep">·</span>
      <span class="meta-chip"><i class="bi bi-clock"></i> <?= $readingTime ?> min</span>
      <?php if (!empty($post['views'])): ?>
      <span class="meta-sep">·</span>
      <span class="meta-chip"><i class="bi bi-eye"></i> <?= number_format($post['views']) ?></span>
      <?php endif; ?>
    </div>

    <!-- Actions -->
    <div class="post-card-actions">
      <div class="action-btns">
        <button type="button"
          class="action-btn reaction-like-btn"
          onclick="quickReact(<?= $post['id'] ?>, 'like', this)"
          title="Like this article"
          aria-label="Like">
          <i class="bi bi-hand-thumbs-up"></i>
          <?php if ($post['likes'] > 0): ?>
          <span class="like-count" style="font-size:0.72rem;margin-left:2px;"><?= number_format($post['likes']) ?></span>
          <?php endif; ?>
        </button>

        <button type="button"
          class="action-btn bookmark-toggle-btn <?= $isBookmarked ? 'liked' : '' ?>"
          onclick="quickBookmark(<?= $post['id'] ?>, this)"
          title="Bookmark"
          aria-label="Bookmark">
          <i class="bi <?= $isBookmarked ? 'bi-bookmark-fill' : 'bi-bookmark' ?>"></i>
        </button>

        <button type="button"
          class="action-btn share-post-btn"
          onclick="quickShare('<?= $postUrl ?>', '<?= htmlspecialchars(addslashes($post['title'])) ?>')"
          title="Share"
          aria-label="Share">
          <i class="bi bi-share"></i>
        </button>

        <?php if ($commentsCount > 0): ?>
        <a href="<?= $postUrl ?>#comments" class="action-btn" title="<?= $commentsCount ?> Comments">
          <i class="bi bi-chat-text"></i>
          <span style="font-size:0.72rem;margin-left:2px;"><?= $commentsCount ?></span>
        </a>
        <?php endif; ?>
      </div>

      <a href="<?= $postUrl ?>" class="read-link">
        Read more <i class="bi bi-arrow-right"></i>
      </a>
    </div>
  </div>

</article>
<?php
    endforeach;
else:
?>
<div class="empty-state" style="grid-column:1/-1;">
  <i class="bi bi-journal-x"></i>
  <h5>No articles found</h5>
  <p>Try a different filter or search keyword.</p>
  <a href="<?= url('blog') ?>" class="btn-dg btn-dg-primary btn-dg-sm mt-3">View All Articles</a>
</div>
<?php endif; ?>
