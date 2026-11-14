<?php
/**
 * DigiWS — World-Class Premium Blog Homepage
 * Sections: Announcement → Hero → Categories → Featured → Trending → Latest+Sidebar
 *           → Editor's Pick → Downloads → AI Tools → Newsletter → Stats → Testimonials → FAQ → CTA → Trust Bar
 */

$totalPages        = isset($totalPages) ? $totalPages : (int)ceil(($totalPosts ?? 0) / ($perPage ?? 9));
$featuredPosts     = !empty($featuredPosts)     ? $featuredPosts     : [];
$trendingPosts     = !empty($trendingPosts)     ? $trendingPosts     : [];
$editorsPick       = !empty($editorsPick)       ? $editorsPick       : [];
$popularTags       = !empty($popularTags)       ? $popularTags       : [];
$popularCategories = !empty($popularCategories) ? $popularCategories : [];

/* Testimonials data */
$testimonials = [
    ['name'=>'Rahul Sharma',  'role'=>'Web Developer, Mumbai',    'av'=>'R', 'color'=>'#2563EB', 'rating'=>5,
     'quote'=>'DigiWS transformed how I learn web development. The tutorials are incredibly detailed and practical — I landed my first freelance project just 3 months after reading their PHP series.'],
    ['name'=>'Priya Singh',   'role'=>'Digital Marketer, Delhi',  'av'=>'P', 'color'=>'#10B981', 'rating'=>5,
     'quote'=>'The SEO and digital marketing content on DigiWS is the best I\'ve found in Hindi and English both. My blog traffic grew 300% after following their strategies.'],
    ['name'=>'Amit Kumar',    'role'=>'Blogger & YouTuber, Patna','av'=>'A', 'color'=>'#8B5CF6', 'rating'=>5,
     'quote'=>'I started my blog from scratch using DigiWS tutorials. Now I earn over ₹50,000/month from AdSense and affiliate marketing. Highly recommended for beginners!'],
];

/* FAQ data */
$faqs = [
    ['q'=>'How often is new content published?',         'a'=>'We publish fresh, expert articles 4–5 times per week covering SEO, AI tools, web development, WordPress, digital marketing and more.'],
    ['q'=>'Is all the content free to read?',            'a'=>'Yes! All blog articles are completely free. We also offer premium digital products and templates for those who want production-ready code.'],
    ['q'=>'Can I submit a guest article?',               'a'=>'Absolutely. We welcome quality guest contributions that help our developer and blogger community. Contact us for guest post guidelines.'],
    ['q'=>'How do I stay updated with new articles?',    'a'=>'Subscribe to our free newsletter and get weekly digests with the best articles, AI tool reviews, and exclusive free downloads delivered to your inbox.'],
    ['q'=>'Do you have video tutorials too?',            'a'=>'Yes! We have a growing library of video tutorials on our YouTube channel covering WordPress, SEO, PHP development, and digital marketing.'],
];

/* Category color/icon map for hero chips */
$chipData = [
    ['icon'=>'bi-search',     'label'=>'SEO',               'color'=>'#10B981'],
    ['icon'=>'bi-cpu',        'label'=>'AI Tools',          'color'=>'#6366F1'],
    ['icon'=>'bi-wordpress',  'label'=>'WordPress',         'color'=>'#2563EB'],
    ['icon'=>'bi-megaphone',  'label'=>'Marketing',         'color'=>'#8B5CF6'],
    ['icon'=>'bi-code-slash', 'label'=>'Programming',       'color'=>'#EF4444'],
    ['icon'=>'bi-server',     'label'=>'Hosting',           'color'=>'#F59E0B'],
];

/* Download cards */
$downloadCards = [
    ['icon'=>'bi-layout-text-window', 'color'=>'linear-gradient(135deg,#2563EB,#4F46E5)', 'title'=>'Free HTML Templates',   'desc'=>'Landing pages, SaaS interfaces & premium UI components.'],
    ['icon'=>'bi-filetype-php',        'color'=>'linear-gradient(135deg,#8B5CF6,#6366F1)', 'title'=>'PHP Source Code',       'desc'=>'Scripts, login systems, APIs and database managers.'],
    ['icon'=>'bi-palette',             'color'=>'linear-gradient(135deg,#F59E0B,#F97316)', 'title'=>'CSS Themes & UI Kits',  'desc'=>'Premium CSS frameworks, glassmorphism & dark mode kits.'],
    ['icon'=>'bi-box-seam',            'color'=>'linear-gradient(135deg,#10B981,#059669)', 'title'=>'WordPress Plugins',    'desc'=>'Free and premium WordPress plugins for every need.'],
];

/* AI Tool cards */
$aiTools = [
    ['icon'=>'bi-chat-dots-fill', 'color'=>'#10A37F', 'name'=>'ChatGPT',      'desc'=>'Best for writing & code'],
    ['icon'=>'bi-gem',            'color'=>'#4285F4', 'name'=>'Google Gemini','desc'=>'Best for research & search'],
    ['icon'=>'bi-robot',          'color'=>'#D97706', 'name'=>'Claude AI',    'desc'=>'Best for analysis & essays'],
    ['icon'=>'bi-stars',          'color'=>'#8B5CF6', 'name'=>'Midjourney',   'desc'=>'Best AI image generator'],
    ['icon'=>'bi-lightning-fill', 'color'=>'#EF4444', 'name'=>'Cursor AI',    'desc'=>'Best AI code editor'],
    ['icon'=>'bi-search',         'color'=>'#06B6D4', 'name'=>'Perplexity',   'desc'=>'Best AI search engine'],
];
?>

<!-- Reading Progress Bar -->
<div class="reading-progress-container"><div class="reading-progress-bar" id="readingProgress"></div></div>

<div class="blog-page">

<!-- ══════════════════════════════════════════════════════════ -->
<!-- ANNOUNCEMENT BAR                                          -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="blog-announcement" id="announcementBar">
  <div class="blog-container">
    <div style="overflow:hidden; position:relative;">
      <div class="announcement-ticker" id="announcementTicker">
        <span class="ticker-badge">🔥 NEW</span>
        <span><a href="<?= url('blog/best-ai-tools-save-time-2026') ?>">Best AI Tools That Will Save You 10+ Hours Weekly</a></span>
        <span class="ticker-sep">◆</span>
        <span><a href="<?= url('blog/complete-seo-guide-beginners-2026') ?>">Complete SEO Guide for Beginners 2026</a></span>
        <span class="ticker-sep">◆</span>
        <span class="ticker-badge">FREE</span>
        <span><a href="<?= url('products') ?>">Download 300+ Free Templates & Source Code</a></span>
        <span class="ticker-sep">◆</span>
        <span><a href="<?= url('blog/wordpress-full-course-beginners') ?>">WordPress Full Course — Start Learning Today</a></span>
        <span class="ticker-sep">◆</span>
        <span class="ticker-badge">🔥 NEW</span>
        <span><a href="<?= url('blog/best-ai-tools-save-time-2026') ?>">Best AI Tools That Will Save You 10+ Hours Weekly</a></span>
        <span class="ticker-sep">◆</span>
        <span><a href="<?= url('blog/complete-seo-guide-beginners-2026') ?>">Complete SEO Guide for Beginners 2026</a></span>
        <span class="ticker-sep">◆</span>
        <span class="ticker-badge">FREE</span>
        <span><a href="<?= url('products') ?>">Download 300+ Free Templates & Source Code</a></span>
        <span class="ticker-sep">◆</span>
        <span><a href="<?= url('blog/wordpress-full-course-beginners') ?>">WordPress Full Course — Start Learning Today</a></span>
      </div>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- HERO SECTION                                              -->
<!-- ══════════════════════════════════════════════════════════ -->
<section class="blog-hero">
  <div class="blog-container">
    <div class="hero-grid">
      <!-- Left: Content -->
      <div class="hero-left animate-fade-up">
        <div class="hero-badge">
          <span class="dot"></span>
          Welcome to DigiWS Blog
        </div>
        <h1 class="hero-title">
          Learn Technology,<br>
          <span class="gradient-text">Blogging &amp; Digital</span><br>
          Marketing
        </h1>
        <p class="hero-sub">
          Daily tutorials, AI tools, blogging tips, SEO guides and premium digital resources — everything you need to grow online.
        </p>

        <!-- Smart Search -->
        <div class="hero-search-wrap">
          <div class="hero-search">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="live-search-input" placeholder="Search tutorials, guides, tips..." autocomplete="off" id="heroSearchInput">
            <button class="search-voice-btn live-voice-btn" type="button" title="Voice search"><i class="bi bi-mic-fill"></i></button>
            <button class="search-go-btn" type="button" onclick="triggerHeroSearch()">Search</button>
          </div>
          <div class="live-search-results" id="heroSearchResults"></div>
          <div class="hero-search-tags">
            <span>Trending:</span>
            <a href="<?= url('blog') ?>?q=seo">SEO</a>
            <a href="<?= url('blog') ?>?q=chatgpt">ChatGPT</a>
            <a href="<?= url('blog') ?>?q=wordpress">WordPress</a>
            <a href="<?= url('blog') ?>?q=adsense">AdSense</a>
            <a href="<?= url('blog') ?>?q=blogging">Blogging</a>
          </div>
        </div>
      </div>

      <!-- Right: Visual -->
      <div class="hero-visual">
        <div class="hero-visual-inner">
          <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=600&h=500&fit=crop" alt="Blog and Digital Marketing" class="hero-image" loading="lazy">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- POPULAR CATEGORIES GRID                                   -->
<!-- ══════════════════════════════════════════════════════════ -->
<section class="blog-categories-section">
  <div class="blog-container">
    <div class="section-head-row">
      <div class="section-heading" style="margin-bottom:0;">
        <div class="section-label"><i class="bi bi-grid-3x3-gap-fill"></i> Categories</div>
        <h2>Popular Categories</h2>
      </div>
      <a href="<?= url('blog') ?>" class="section-link">View All <i class="bi bi-arrow-right"></i></a>
    </div>
    <div style="margin-top:28px;">
    <div class="category-carousel-wrap">
      <div class="category-carousel" id="categoryCarousel">
        <?php
        $catColors = [
          'seo-tips'         => ['bg'=>'linear-gradient(135deg,#10B981,#059669)', 'color'=>'#10B981'],
          'ai-tools'         => ['bg'=>'linear-gradient(135deg,#6366F1,#4F46E5)', 'color'=>'#6366F1'],
          'wordpress'        => ['bg'=>'linear-gradient(135deg,#2563EB,#1D4ED8)', 'color'=>'#2563EB'],
          'hosting-servers'  => ['bg'=>'linear-gradient(135deg,#F59E0B,#D97706)', 'color'=>'#F59E0B'],
          'programming'      => ['bg'=>'linear-gradient(135deg,#EF4444,#DC2626)', 'color'=>'#EF4444'],
          'digital-marketing'=> ['bg'=>'linear-gradient(135deg,#8B5CF6,#7C3AED)', 'color'=>'#8B5CF6'],
          'web-development'  => ['bg'=>'linear-gradient(135deg,#06B6D4,#0891B2)', 'color'=>'#06B6D4'],
          'blogging'         => ['bg'=>'linear-gradient(135deg,#EC4899,#BE185D)', 'color'=>'#EC4899'],
          'design-ui'        => ['bg'=>'linear-gradient(135deg,#F97316,#EA580C)', 'color'=>'#F97316'],
          'php-tutorials'    => ['bg'=>'linear-gradient(135deg,#7C3AED,#6D28D9)', 'color'=>'#7C3AED'],
          'buying-guides'    => ['bg'=>'linear-gradient(135deg,#0EA5E9,#0284C7)', 'color'=>'#0EA5E9'],
          'digital-products' => ['bg'=>'linear-gradient(135deg,#14B8A6,#0D9488)', 'color'=>'#14B8A6'],
        ];
        $staticCats = [
          ['name'=>'SEO',              'slug'=>'seo-tips',          'icon'=>'bi-search',        'post_count'=>126],
          ['name'=>'Blogging',         'slug'=>'blogging',           'icon'=>'bi-pen',           'post_count'=>96],
          ['name'=>'WordPress',        'slug'=>'wordpress',          'icon'=>'bi-wordpress',     'post_count'=>112],
          ['name'=>'AI Tools',         'slug'=>'ai-tools',           'icon'=>'bi-cpu',           'post_count'=>86],
          ['name'=>'Digital Marketing','slug'=>'digital-marketing',  'icon'=>'bi-megaphone',     'post_count'=>78],
          ['name'=>'Hosting',          'slug'=>'hosting-servers',    'icon'=>'bi-server',        'post_count'=>64],
          ['name'=>'Programming',      'slug'=>'programming',        'icon'=>'bi-code-slash',    'post_count'=>92],
          ['name'=>'Web Development',  'slug'=>'web-development',    'icon'=>'bi-globe',         'post_count'=>72],
        ];
        $displayCats = !empty($popularCategories) ? $popularCategories : $staticCats;
        foreach ($displayCats as $cat):
          $slug = $cat['slug'] ?? '';
          $cc   = $catColors[$slug] ?? ['bg'=>'linear-gradient(135deg,#2563EB,#4F46E5)', 'color'=>'#2563EB'];
          $icon = $cat['icon'] ?? 'bi-folder';
          $cnt  = $cat['post_count'] ?? 0;
        ?>
        <a href="<?= url('blog/category/' . $slug) ?>" class="cat-card">
          <div class="cat-icon-wrap" style="background:<?= $cc['bg'] ?>;">
            <i class="<?= htmlspecialchars($icon) ?>"></i>
          </div>
          <div class="cat-name"><?= htmlspecialchars($cat['name']) ?></div>
          <?php if ($cnt): ?><div class="cat-count"><?= number_format($cnt) ?> Posts</div><?php endif; ?>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- FEATURED ARTICLES                                         -->
<!-- ══════════════════════════════════════════════════════════ -->
<?php if (!empty($featuredPosts)): ?>
<section class="featured-section">
  <div class="blog-container">
    <div class="section-head-row">
      <div class="section-heading" style="margin-bottom:0;">
        <div class="section-label"><i class="bi bi-bookmark-star-fill"></i> Featured</div>
        <h2>Featured Articles</h2>
      </div>
      <a href="<?= url('blog') ?>?order=featured" class="section-link">View all <i class="bi bi-arrow-right"></i></a>
    </div>
    <div style="margin-top:28px;">
    <div class="featured-grid animate-stagger">
      <?php foreach (array_slice($featuredPosts, 0, 3) as $post):
        $wc  = str_word_count(strip_tags($post['content'] ?? ''));
        $rt  = max(2, ceil($wc / 200));
        $url = url('blog/' . $post['slug']);
        $catColor = $post['category_color'] ?? '#2563EB';
        $isBookmarked = isLoggedIn() ? (new BlogBookmark())->isBookmarked(currentUserId(), $post['id']) : false;
      ?>
      <article class="featured-card">
        <div class="featured-img">
          <?php if (!empty($post['image'])): ?>
            <img src="<?= uploadUrl($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
          <?php else: ?>
            <div class="featured-img-fallback">
              <i class="bi bi-journal-richtext" style="color:<?= htmlspecialchars($catColor) ?>;opacity:0.5;"></i>
            </div>
          <?php endif; ?>
          <span class="featured-cat-badge" style="color:<?= htmlspecialchars($catColor) ?>;">
            <i class="<?= htmlspecialchars($post['category_icon'] ?? 'bi-tag') ?>"></i>
            <?= htmlspecialchars($post['category_name'] ?? 'Featured') ?>
          </span>
          <button class="featured-bookmark" onclick="quickBookmark(<?= $post['id'] ?>, this)" aria-label="Bookmark">
            <i class="bi <?= $isBookmarked ? 'bi-bookmark-fill text-primary' : 'bi-bookmark' ?>"></i>
          </button>
        </div>
        <div class="featured-body">
          <h3><a href="<?= $url ?>"><?= htmlspecialchars($post['title']) ?></a></h3>
          <p><?= htmlspecialchars(truncateText($post['summary'] ?: strip_tags($post['content']), 110)) ?></p>
          <div class="featured-meta">
            <div class="author-mini">
              <div class="author-mini-av"><?= strtoupper(substr($post['author_name'] ?? 'D', 0, 1)) ?></div>
              <span><?= htmlspecialchars($post['author_name'] ?? 'Deepak Kumar') ?></span>
            </div>
            <span class="meta-sep">•</span>
            <span class="meta-chip"><i class="bi bi-clock"></i> <?= $rt ?> min read</span>
            <?php if (!empty($post['views'])): ?>
            <span class="meta-sep">•</span>
            <span class="meta-chip"><i class="bi bi-eye"></i> <?= number_format($post['views']) ?></span>
            <?php endif; ?>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- TRENDING TAGS ROW                                         -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="trending-row-section">
  <div class="blog-container">
    <div class="trending-row">
      <div class="trending-label"><i class="bi bi-fire"></i> Trending:</div>
      <?php
      $trendingTagsDisplay = !empty($popularTags) ? $popularTags : [
        ['name'=>'SEO','slug'=>'seo'], ['name'=>'ChatGPT','slug'=>'chatgpt'],
        ['name'=>'WordPress','slug'=>'wordpress'], ['name'=>'AI Tools','slug'=>'ai'],
        ['name'=>'AdSense','slug'=>'adsense'], ['name'=>'PHP','slug'=>'php'],
        ['name'=>'Blogging','slug'=>'blogging'], ['name'=>'Digital Marketing','slug'=>'digital-marketing'],
        ['name'=>'Hosting','slug'=>'hosting'], ['name'=>'Bootstrap','slug'=>'bootstrap'],
        ['name'=>'JavaScript','slug'=>'javascript'], ['name'=>'CSS','slug'=>'css'],
      ];
      foreach (array_slice($trendingTagsDisplay, 0, 14) as $tag):
      ?>
      <a href="<?= url('blog/tag/' . $tag['slug']) ?>" class="trending-tag"><?= htmlspecialchars($tag['name']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- LATEST ARTICLES + SIDEBAR                                 -->
<!-- ══════════════════════════════════════════════════════════ -->
<section class="latest-section" id="latest">
  <div class="blog-container">
    <div class="section-head-row">
      <div class="section-heading" style="margin-bottom:0;">
        <div class="section-label"><i class="bi bi-newspaper"></i> Latest Content</div>
        <h2>Latest Articles</h2>
      </div>
    </div>
    <div style="margin-top:28px;">
    <div class="latest-layout">
      <!-- Main Posts Column -->
      <div>
        <!-- Tab Filters -->
        <div class="blog-filters-row">
          <div class="tab-filters">
            <button class="tab-btn active" onclick="applySort('', this)">
              <i class="bi bi-collection"></i> Latest
            </button>
            <button class="tab-btn" onclick="applySort('views', this)">
              <i class="bi bi-eye"></i> Popular
            </button>
            <button class="tab-btn" onclick="applySort('trending', this)">
              <i class="bi bi-fire text-danger"></i> Trending
            </button>
            <button class="tab-btn" onclick="applySort('oldest', this)">
              <i class="bi bi-clock-history"></i> Newest
            </button>
            <button class="tab-btn" onclick="applySort('rating', this)">
              <i class="bi bi-star-fill text-warning"></i> Top Rated
            </button>
          </div>
          <div class="filter-count text-muted small">
            Showing <span id="currentCount"><?= count($posts ?? []) ?></span> of <?= $totalPosts ?> articles
          </div>
        </div>

        <!-- Posts Grid -->
        <div class="posts-grid" id="postsContainer">
          <?php include VIEWS_PATH . '/blog/partials/posts-grid.php'; ?>
        </div>

        <!-- Load More -->
        <div class="load-more-wrap" id="loadMoreContainer">
          <?php if (($page ?? 1) < $totalPages): ?>
          <button class="btn-dg btn-dg-outline btn-dg-lg" id="loadMoreBtn" onclick="loadMorePosts()">
            <span class="spinner-border spinner-border-sm me-2 d-none" id="loadMoreSpinner" role="status"></span>
            <i class="bi bi-arrow-down-circle me-1"></i> Load More Articles
          </button>
          <?php else: ?>
          <p class="text-muted small">You've reached the end of the archive.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Sidebar -->
      <aside class="sidebar">

        <!-- Sidebar Search -->
        <div class="sidebar-card">
          <div class="sidebar-card-title"><i class="bi bi-search"></i> Search Blog</div>
          <div style="position:relative;">
            <div class="sidebar-search">
              <i class="bi bi-search"></i>
              <input type="text" class="live-search-input" placeholder="Search articles..." autocomplete="off">
            </div>
            <div class="live-search-results w-100"></div>
          </div>
        </div>

        <!-- Trending Posts -->
        <?php if (!empty($trendingPosts)): ?>
        <div class="sidebar-card">
          <div class="sidebar-card-title"><i class="bi bi-fire text-danger"></i> Trending Articles</div>
          <div class="trend-list">
            <?php foreach (array_slice($trendingPosts, 0, 5) as $i => $tp): ?>
            <a href="<?= url('blog/' . $tp['slug']) ?>" class="trend-item">
              <div class="trend-rank <?= $i < 3 ? 'top' : '' ?>"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></div>
              <div class="trend-thumb"><i class="bi bi-journal-text"></i></div>
              <div class="trend-info">
                <h4><?= htmlspecialchars($tp['title']) ?></h4>
                <span><i class="bi bi-eye me-1"></i><?= number_format($tp['views']) ?> views</span>
              </div>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Categories -->
        <?php if (!empty($popularCategories)): ?>
        <div class="sidebar-card">
          <div class="sidebar-card-title"><i class="bi bi-grid-3x3-gap"></i> Categories</div>
          <div class="sidebar-cat-list">
            <?php foreach (array_slice($popularCategories, 0, 8) as $cat): ?>
            <a href="<?= url('blog/category/' . $cat['slug']) ?>" class="sidebar-cat-item">
              <span><i class="<?= htmlspecialchars($cat['icon'] ?? 'bi-folder') ?> me-2 text-primary"></i><?= htmlspecialchars($cat['name']) ?></span>
              <span class="cat-badge"><?= $cat['post_count'] ?? 0 ?></span>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Newsletter Mini -->
        <div class="sidebar-nl">
          <h4><i class="bi bi-envelope-open-fill me-2"></i>Newsletter</h4>
          <p>Get the latest tutorials, updates and resources straight into your inbox.</p>
          <form class="sidebar-nl-form" action="<?= url('newsletter/subscribe') ?>" method="POST" id="sidebarNlForm">
            <input type="email" name="email" placeholder="Enter your email address" required>
            <button type="submit">Subscribe Now</button>
          </form>
          <div class="sidebar-nl-note">
            <i class="bi bi-shield-check me-1"></i>Join 50,000+ readers · No spam, ever.
          </div>
        </div>

        <!-- Tags -->
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

        <!-- Ad Slot -->
        <div class="ad-slot">
          <i class="bi bi-badge-ad"></i>
          <span>ADVERTISEMENT</span>
          <a href="<?= url('products') ?>" class="btn-dg btn-dg-primary btn-dg-sm mt-2">
            <i class="bi bi-bag"></i> Explore Products
          </a>
        </div>

      </aside>
    </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- EDITOR'S PICK                                             -->
<!-- ══════════════════════════════════════════════════════════ -->
<?php if (!empty($editorsPick)): ?>
<section class="editors-section">
  <div class="blog-container">
    <div class="section-head-row">
      <div class="section-heading" style="margin-bottom:0;">
        <div class="section-label"><i class="bi bi-award-fill"></i> Editor's Choice</div>
        <h2>Editor's Pick</h2>
      </div>
      <a href="<?= url('blog') ?>?order=editors_pick" class="section-link">View all <i class="bi bi-arrow-right"></i></a>
    </div>
    <div style="margin-top:28px;">
    <div class="editors-grid animate-stagger">
      <?php foreach (array_slice($editorsPick, 0, 3) as $post):
        $wc  = str_word_count(strip_tags($post['content'] ?? ''));
        $rt  = max(2, ceil($wc / 200));
        $url = url('blog/' . $post['slug']);
      ?>
      <article class="editor-card">
        <div class="editor-card-img">
          <?php if (!empty($post['image'])): ?>
            <img src="<?= uploadUrl($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" loading="lazy">
          <?php else: ?>
            <div class="editor-card-img-fallback">
              <i class="bi bi-journal-bookmark-fill" style="color:<?= htmlspecialchars($post['category_color'] ?? '#2563EB') ?>;opacity:0.4;"></i>
            </div>
          <?php endif; ?>
          <span class="editor-badge"><i class="bi bi-award-fill"></i> Editor's Pick</span>
        </div>
        <div class="editor-card-body">
          <span class="editor-cat"><?= htmlspecialchars($post['category_name'] ?? 'Editor Pick') ?></span>
          <h3><a href="<?= $url ?>"><?= htmlspecialchars($post['title']) ?></a></h3>
          <p><?= htmlspecialchars(truncateText($post['summary'] ?: strip_tags($post['content']), 110)) ?></p>
          <div class="editor-card-footer">
            <div class="meta-chip text-muted small"><i class="bi bi-clock me-1"></i><?= $rt ?> min read</div>
            <a href="<?= $url ?>" class="read-link">Read Article <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- FREE DIGITAL DOWNLOADS                                    -->
<!-- ══════════════════════════════════════════════════════════ -->
<section class="downloads-section">
  <div class="blog-container">
    <div class="section-head-row">
      <div class="section-heading" style="margin-bottom:0;">
        <div class="section-label"><i class="bi bi-download"></i> Free Downloads</div>
        <h2>Free Digital Downloads</h2>
        <p>High-quality digital products for your projects — completely free.</p>
      </div>
      <a href="<?= url('products') ?>" class="section-link">Explore Downloads <i class="bi bi-arrow-right"></i></a>
    </div>
    <div style="margin-top:28px;">
    <div class="downloads-grid animate-stagger">
      <?php foreach ($downloadCards as $dl): ?>
      <a href="<?= url('products') ?>" class="dl-card">
        <div class="dl-icon" style="background:<?= $dl['color'] ?>;"><i class="bi <?= $dl['icon'] ?>"></i></div>
        <h4><?= $dl['title'] ?></h4>
        <p><?= $dl['desc'] ?></p>
        <div class="dl-link">Explore Downloads <i class="bi bi-arrow-right-short"></i></div>
      </a>
      <?php endforeach; ?>
    </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- AI TOOLS SECTION                                          -->
<!-- ══════════════════════════════════════════════════════════ -->
<section class="ai-section animate-fade-up">
  <div class="blog-container">
    <div class="section-head-row">
      <div class="section-heading" style="margin-bottom:0;">
        <div class="section-label"><i class="bi bi-cpu-fill"></i> AI Tools</div>
        <h2>Best AI Tools in 2026</h2>
        <p>Discover the top artificial intelligence tools that are transforming how we create, code, and grow.</p>
      </div>
      <a href="<?= url('blog/category/ai-tools') ?>" class="section-link" style="color:var(--accent);">View AI Articles <i class="bi bi-arrow-right"></i></a>
    </div>
    <div style="margin-top:32px;">
    <div class="ai-tools-grid animate-stagger">
      <?php foreach ($aiTools as $tool): ?>
      <a href="<?= url('blog') ?>?q=<?= urlencode($tool['name']) ?>" class="ai-tool-card">
        <div class="ai-tool-icon" style="background:<?= $tool['color'] ?>22;color:<?= $tool['color'] ?>;"><i class="bi <?= $tool['icon'] ?>"></i></div>
        <div class="ai-tool-info">
          <h4><?= htmlspecialchars($tool['name']) ?></h4>
          <p><?= htmlspecialchars($tool['desc']) ?></p>
        </div>
        <i class="bi bi-arrow-right ms-auto" style="color:rgba(255,255,255,0.3);"></i>
      </a>
      <?php endforeach; ?>
    </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- NEWSLETTER SECTION                                        -->
<!-- ══════════════════════════════════════════════════════════ -->
<section class="newsletter-section" id="newsletter">
  <div class="blog-container">
    <div class="nl-card animate-fade-up">
      <div class="nl-inner">
        <div>
          <div class="nl-badge"><i class="bi bi-envelope-open-fill"></i> Weekly Newsletter</div>
          <h2>Join 50,000+ Readers.<br>Get Smarter Every Week.</h2>
          <p>Get weekly tutorials, digital product reviews, code scripts, SEO tips, and launch checklists delivered straight to your inbox — for free.</p>
        </div>
        <div class="nl-form-wrap">
          <form class="nl-form" action="<?= url('newsletter/subscribe') ?>" method="POST" id="newsletterHomeForm">
            <input type="email" name="email" placeholder="Enter your email address..." required>
            <button type="submit">Subscribe <i class="bi bi-send ms-1"></i></button>
          </form>
          <div id="newsletterFormResponse" class="mt-1" style="font-size:0.84rem;font-weight:600;color:#fff;text-align:center;min-height:20px;"></div>
          <div class="nl-proof">
            <div class="nl-avatars">
              <div class="av">R</div><div class="av">P</div><div class="av">A</div><div class="av">S</div><div class="av">K</div>
            </div>
            <div class="nl-proof-text"><strong>50,000+</strong> subscribers already joined</div>
          </div>
          <div style="display:flex;gap:16px;flex-wrap:wrap;">
            <span style="font-size:0.8rem;color:rgba(255,255,255,0.6);display:flex;align-items:center;gap:5px;"><i class="bi bi-shield-check text-success"></i> No spam, ever</span>
            <span style="font-size:0.8rem;color:rgba(255,255,255,0.6);display:flex;align-items:center;gap:5px;"><i class="bi bi-gift text-warning"></i> Free resource bundle on sign-up</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- SOCIAL PROOF STATS                                        -->
<!-- ══════════════════════════════════════════════════════════ -->
<section class="stats-section">
  <div class="blog-container">
    <div class="stats-grid animate-stagger">
      <div class="stat-item">
        <div class="stat-icon-wrap"><i class="bi bi-people-fill"></i></div>
        <strong>50,000+</strong>
        <span>Monthly Readers</span>
      </div>
      <div class="stat-item">
        <div class="stat-icon-wrap" style="background:linear-gradient(135deg,#10B981,#059669);"><i class="bi bi-file-richtext-fill"></i></div>
        <strong><?= $totalPosts ?>+</strong>
        <span>Expert Articles</span>
      </div>
      <div class="stat-item">
        <div class="stat-icon-wrap" style="background:linear-gradient(135deg,#F59E0B,#D97706);"><i class="bi bi-star-fill"></i></div>
        <strong>4.9/5</strong>
        <span>Reader Rating</span>
      </div>
      <div class="stat-item">
        <div class="stat-icon-wrap" style="background:linear-gradient(135deg,#8B5CF6,#7C3AED);"><i class="bi bi-download"></i></div>
        <strong>300+</strong>
        <span>Free Downloads</span>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- TESTIMONIALS                                              -->
<!-- ══════════════════════════════════════════════════════════ -->
<section class="testimonials-section">
  <div class="blog-container">
    <div class="section-heading text-center">
      <div class="section-label" style="justify-content:center;"><i class="bi bi-chat-quote-fill"></i> Testimonials</div>
      <h2>What Our Readers Say</h2>
      <p>Trusted by thousands of developers, bloggers, and digital marketers across India and beyond.</p>
    </div>
    <div class="testimonials-grid animate-stagger">
      <?php foreach ($testimonials as $t): ?>
      <div class="testimonial-card">
        <div class="tc-stars">
          <?php for ($s=0; $s<5; $s++) echo '<i class="bi bi-star-fill"></i>'; ?>
        </div>
        <p class="tc-quote">"<?= htmlspecialchars($t['quote']) ?>"</p>
        <div class="tc-author">
          <div class="tc-av" style="background:linear-gradient(135deg,<?= $t['color'] ?>,#4F46E5);"><?= $t['av'] ?></div>
          <div>
            <div class="tc-name"><?= htmlspecialchars($t['name']) ?></div>
            <div class="tc-role"><?= htmlspecialchars($t['role']) ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- FAQ SECTION                                               -->
<!-- ══════════════════════════════════════════════════════════ -->
<section class="faq-section">
  <div class="blog-container">
    <div class="section-heading text-center">
      <div class="section-label" style="justify-content:center;"><i class="bi bi-question-circle-fill"></i> FAQ</div>
      <h2>Frequently Asked Questions</h2>
    </div>
    <div class="faq-list">
      <?php foreach ($faqs as $i => $faq): ?>
      <div class="faq-item" id="faq-<?= $i ?>">
        <button class="faq-q" onclick="toggleFaq(<?= $i ?>)" aria-expanded="false">
          <?= htmlspecialchars($faq['q']) ?>
          <i class="bi bi-chevron-down"></i>
        </button>
        <div class="faq-a">
          <div class="faq-a-inner"><?= htmlspecialchars($faq['a']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- FINAL CTA BANNER                                          -->
<!-- ══════════════════════════════════════════════════════════ -->
<section class="final-cta-section">
  <div class="blog-container">
    <div class="cta-banner animate-fade-up">
      <div class="cta-text">
        <h2>Start Learning &amp; Growing Today</h2>
        <p>Access expert tutorials, free digital downloads, AI tool guides, SEO strategies, and everything you need to build a profitable online presence.</p>
      </div>
      <div class="cta-actions">
        <a href="#latest" class="btn-dg btn-dg-white btn-dg-lg">
          <i class="bi bi-book-open-fill"></i> Read Articles
        </a>
        <a href="<?= url('products') ?>" class="btn-dg btn-dg-lg" style="background:rgba(255,255,255,0.15);color:#fff;border:1.5px solid rgba(255,255,255,0.3);">
          <i class="bi bi-download"></i> Free Downloads
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ══════════════════════════════════════════════════════════ -->
<!-- TRUST BAR                                                 -->
<!-- ══════════════════════════════════════════════════════════ -->
<div class="trust-bar">
  <div class="blog-container">
    <div class="trust-grid animate-stagger">
      <div class="trust-item">
        <div class="trust-icon-wrap"><i class="bi bi-check2-circle"></i></div>
        <div>
          <h5>Well Researched Content</h5>
          <p>In-depth and well explained articles backed by real experience.</p>
        </div>
      </div>
      <div class="trust-item">
        <div class="trust-icon-wrap" style="background:rgba(16,185,129,0.1);color:#10B981;"><i class="bi bi-translate"></i></div>
        <div>
          <h5>Easy to Understand</h5>
          <p>Simple language for better learning — beginner to advanced.</p>
        </div>
      </div>
      <div class="trust-item">
        <div class="trust-icon-wrap" style="background:rgba(139,92,246,0.1);color:#8B5CF6;"><i class="bi bi-arrow-repeat"></i></div>
        <div>
          <h5>Updated Regularly</h5>
          <p>Fresh content every week covering the latest in tech and blogging.</p>
        </div>
      </div>
      <div class="trust-item">
        <div class="trust-icon-wrap" style="background:rgba(245,158,11,0.1);color:#F59E0B;"><i class="bi bi-people-fill"></i></div>
        <div>
          <h5>Trusted by 50K+ Readers</h5>
          <p>Join our growing community of developers and digital marketers.</p>
        </div>
      </div>
    </div>
  </div>
</div>

</div><!-- .blog-page -->

<!-- ══════════════════════════════════════════════════════════ -->
<!-- SCRIPTS                                                   -->
<!-- ══════════════════════════════════════════════════════════ -->
<script>
// ── State ──────────────────────────────────────────────────
let currentPage  = <?= $page ?? 1 ?>;
let totalPages   = <?= $totalPages ?>;
let filterSort   = '';

// ── Reading Progress ───────────────────────────────────────
window.addEventListener('scroll', function() {
  const h   = document.documentElement;
  const sc  = h.scrollTop || document.body.scrollTop;
  const sh  = h.scrollHeight - h.clientHeight;
  const pct = sh > 0 ? (sc / sh) * 100 : 0;
  const bar = document.getElementById('readingProgress');
  if (bar) bar.style.width = pct + '%';
});

// ── Intersection Observer (fade-in animations) ─────────────
(function() {
  const els = document.querySelectorAll('.animate-fade-up, .animate-stagger');
  if (!els.length) return;
  const obs = new IntersectionObserver(function(entries) {
    entries.forEach(function(e) {
      if (e.isIntersecting) { e.target.classList.add('in-view'); obs.unobserve(e.target); }
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
  els.forEach(function(el) { obs.observe(el); });
})();

// ── FAQ Toggle ─────────────────────────────────────────────
function toggleFaq(i) {
  const item = document.getElementById('faq-' + i);
  if (!item) return;
  const isOpen = item.classList.contains('open');
  document.querySelectorAll('.faq-item.open').forEach(function(f) { f.classList.remove('open'); });
  if (!isOpen) item.classList.add('open');
}

// ── Hero Search Trigger ────────────────────────────────────
function triggerHeroSearch() {
  const val = document.getElementById('heroSearchInput').value.trim();
  if (val) window.location.href = window.appUrl('blog') + '?q=' + encodeURIComponent(val);
}
document.getElementById('heroSearchInput').addEventListener('keydown', function(e) {
  if (e.key === 'Enter') triggerHeroSearch();
});

// ── Live Search ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.live-search-input').forEach(function(input) {
    const wrap     = input.closest('.hero-search, .sidebar-search, div[style]')
                     || input.parentElement;
    const resultsContainer = wrap.querySelector('.live-search-results')
                     || input.parentElement.parentElement.querySelector('.live-search-results');
    if (!resultsContainer) return;
    let timer;
    input.addEventListener('input', function() {
      const q = this.value.trim();
      clearTimeout(timer);
      if (q.length < 2) { resultsContainer.classList.remove('show'); return; }
      timer = setTimeout(function() {
        fetch(window.appUrl('api/blog/search?q=' + encodeURIComponent(q)))
          .then(function(r) { return r.json(); })
          .then(function(data) {
            if (!data.posts || !data.posts.length) {
              resultsContainer.innerHTML = '<div style="padding:16px;text-align:center;color:var(--muted);font-size:0.84rem;">No articles found for "' + q + '"</div>';
            } else {
              resultsContainer.innerHTML = data.posts.map(function(p) {
                return '<a href="' + p.url + '" class="search-suggestion-item">'
                  + (p.image ? '<img src="' + p.image + '" class="suggestion-thumb" alt="">' : '<div class="suggestion-thumb"><i class="bi bi-journal"></i></div>')
                  + '<div class="suggestion-info"><div class="suggestion-title">' + p.title + '</div>'
                  + '<div class="suggestion-meta">' + (p.category ? p.category + ' · ' : '') + p.date + '</div></div>'
                  + '</a>';
              }).join('');
            }
            resultsContainer.classList.add('show');
          }).catch(function() {});
      }, 280);
    });
    document.addEventListener('click', function(e) {
      if (!input.contains(e.target) && !resultsContainer.contains(e.target)) {
        resultsContainer.classList.remove('show');
      }
    });
  });

  // ── Voice Search ─────────────────────────────────────────
  if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
    const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
    const rec = new SR(); rec.lang = 'en-IN'; rec.interimResults = false;
    document.querySelectorAll('.search-voice-btn, .live-voice-btn').forEach(function(btn) {
      const inp = btn.closest('.hero-search, .sidebar-search')?.querySelector('input')
                  || document.getElementById('heroSearchInput');
      btn.addEventListener('click', function() {
        btn.style.color = '#EF4444'; rec.start();
      });
      rec.onresult = function(ev) {
        const t = ev.results[0][0].transcript;
        inp.value = t; inp.dispatchEvent(new Event('input'));
        btn.style.color = '';
      };
      rec.onerror = rec.onend = function() { btn.style.color = ''; };
    });
  } else {
    document.querySelectorAll('.search-voice-btn, .live-voice-btn').forEach(function(b) { b.style.display = 'none'; });
  }

  // ── Newsletter Form ───────────────────────────────────────
  ['newsletterHomeForm', 'sidebarNlForm'].forEach(function(id) {
    const f  = document.getElementById(id);
    const rb = id === 'newsletterHomeForm' ? document.getElementById('newsletterFormResponse') : null;
    if (!f) return;
    f.addEventListener('submit', function(e) {
      e.preventDefault();
      if (rb) rb.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i> Subscribing...';
      fetch(f.action, { method: 'POST', body: new FormData(f) })
        .then(function(r) { return r.json(); })
        .then(function(d) {
          if (rb) rb.innerHTML = d.success ? '✓ ' + (d.message || 'Subscribed!') : '✕ ' + (d.message || 'Please try again.');
          if (d.success) f.reset();
        }).catch(function() { if (rb) rb.innerHTML = 'Error. Please try again.'; });
    });
  });
});

// ── Sort Filter ────────────────────────────────────────────
function applySort(order, el) {
  document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
  if (el) el.classList.add('active');
  filterSort  = order;
  currentPage = 1;
  fetchPosts(true);
}

// ── Fetch Posts ────────────────────────────────────────────
function fetchPosts(reset) {
  const container  = document.getElementById('postsContainer');
  const loadWrap   = document.getElementById('loadMoreContainer');
  const countEl    = document.getElementById('currentCount');
  if (!container) return;

  if (reset) {
    container.innerHTML = [1,2,3,4,5,6].map(function() {
      return '<div class="skeleton-card"><div class="skeleton-thumb"></div><div class="skeleton-body"><div class="sk-line w-80"></div><div class="sk-line w-60 mt-2"></div><div class="sk-line w-40 mt-3"></div></div></div>';
    }).join('');
  }

  const params = new URLSearchParams({ page: currentPage, order: filterSort });
  fetch(window.appUrl('blog') + '?' + params, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      if (!data.success) return;
      totalPages = data.totalPages;
      if (reset) { container.innerHTML = data.html; }
      else        { container.insertAdjacentHTML('beforeend', data.html); }
      if (loadWrap) {
        loadWrap.innerHTML = data.hasMore
          ? '<button class="btn-dg btn-dg-outline btn-dg-lg" id="loadMoreBtn" onclick="loadMorePosts()"><i class="bi bi-arrow-down-circle me-1"></i> Load More Articles</button>'
          : '<p class="text-muted small">You\'ve reached the end of the archive.</p>';
      }
      if (countEl) countEl.textContent = container.querySelectorAll('.post-card, .latest-card, article').length;
    }).catch(function(e) { console.error('Fetch error:', e); });
}

// ── Load More ──────────────────────────────────────────────
function loadMorePosts() {
  const btn = document.getElementById('loadMoreBtn');
  if (btn) { btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Loading...'; }
  currentPage++;
  fetchPosts(false);
}

// ── Bookmark ───────────────────────────────────────────────
function quickBookmark(postId, el) {
  if (!<?= isLoggedIn() ? 'true' : 'false' ?>) {
    window.location.href = window.appUrl('login');
    return;
  }
  fetch(window.appUrl('api/blog/bookmark/toggle'), {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'post_id=' + postId
  }).then(function(r) { return r.json(); }).then(function(d) {
    if (d.success) {
      const i = el.querySelector('i');
      if (i) i.className = d.bookmarked ? 'bi bi-bookmark-fill text-primary' : 'bi bi-bookmark';
    }
  });
}

// ── Quick React ────────────────────────────────────────────
function quickReact(postId, type, el) {
  fetch(window.appUrl('api/blog/react'), {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'post_id=' + postId + '&reaction=' + type
  }).then(function(r) { return r.json(); }).then(function(d) {
    if (d.success) {
      const c = el.querySelector('.like-count');
      if (c) c.textContent = d.reactions[type];
      d.change > 0 ? el.classList.add('active') : el.classList.remove('active');
    }
  });
}

// ── Quick Share ────────────────────────────────────────────
function quickShare(url, title) {
  if (navigator.share) {
    navigator.share({ title: title, url: url }).catch(function() {});
  } else {
    navigator.clipboard.writeText(url).then(function() {
      // Show brief toast
      const t = document.createElement('div');
      t.textContent = '✓ Link copied!';
      t.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#111827;color:#fff;padding:10px 22px;border-radius:999px;font-size:0.85rem;font-weight:600;z-index:99999;box-shadow:0 8px 24px rgba(0,0,0,0.2);';
      document.body.appendChild(t);
      setTimeout(function() { t.remove(); }, 2200);
    });
  }
}
</script>
