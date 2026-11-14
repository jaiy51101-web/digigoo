<?php
/**
 * Admin: Blog Analytics Dashboard
 * Variables: $analytics (array from Post::getAnalytics())
 */
$analytics = $analytics ?? [];

// Safe defaults
$totalPosts    = $analytics['total_posts']     ?? 0;
$publishedPosts= $analytics['published']       ?? 0;
$draftPosts    = $analytics['drafts']          ?? 0;
$totalViews    = $analytics['total_views']     ?? 0;
$totalComments = $analytics['total_comments']  ?? 0;
$totalReactions= $analytics['total_reactions'] ?? 0;
$avgRating     = $analytics['avg_rating']      ?? 0;
$topPosts      = $analytics['top_posts']       ?? [];
$topCategories = $analytics['top_categories']  ?? [];
$viewsByMonth  = $analytics['views_by_month']  ?? [];
$postsByMonth  = $analytics['posts_by_month']  ?? [];
$reactionStats = $analytics['reaction_stats']  ?? ['like'=>0,'love'=>0,'insight'=>0,'mindblown'=>0];
$commentsByDay = $analytics['comments_by_day'] ?? [];

// Build chart data
$monthLabels  = array_column($viewsByMonth, 'month');
$monthViews   = array_column($viewsByMonth, 'views');
$monthPosts   = array_column($postsByMonth, 'count');
$catLabels    = array_column($topCategories, 'name');
$catCounts    = array_column($topCategories, 'post_count');
$dayLabels    = array_column($commentsByDay, 'day');
$dayCounts    = array_column($commentsByDay, 'count');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1">Blog Analytics</h5>
        <p class="text-muted small mb-0">Comprehensive statistics for posts, views, reactions, ratings, and reader engagement.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/blog') ?>" class="btn-dc-dark btn-dc-sm"><i class="bi bi-arrow-left me-1"></i> Back to Posts</a>
    </div>
</div>

<!-- Top Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="admin-card p-4 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:52px;height:52px;background:rgba(37,99,235,0.1);">
                <i class="bi bi-journal-text fs-4 text-primary"></i>
            </div>
            <div>
                <div class="fw-bold fs-3 lh-1"><?= number_format($totalPosts) ?></div>
                <div class="small text-muted mt-1">Total Posts</div>
                <div class="small text-success mt-1"><i class="bi bi-check-circle me-1"></i><?= $publishedPosts ?> published</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="admin-card p-4 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:52px;height:52px;background:rgba(16,185,129,0.1);">
                <i class="bi bi-eye fs-4 text-success"></i>
            </div>
            <div>
                <div class="fw-bold fs-3 lh-1"><?= number_format($totalViews) ?></div>
                <div class="small text-muted mt-1">Total Views</div>
                <div class="small text-muted mt-1">Across all posts</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="admin-card p-4 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:52px;height:52px;background:rgba(245,158,11,0.1);">
                <i class="bi bi-chat-left-text fs-4 text-warning"></i>
            </div>
            <div>
                <div class="fw-bold fs-3 lh-1"><?= number_format($totalComments) ?></div>
                <div class="small text-muted mt-1">Comments</div>
                <div class="small text-muted mt-1">Approved &amp; published</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="admin-card p-4 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:52px;height:52px;background:rgba(239,68,68,0.1);">
                <i class="bi bi-star-fill fs-4 text-danger"></i>
            </div>
            <div>
                <div class="fw-bold fs-3 lh-1"><?= number_format($avgRating, 1) ?></div>
                <div class="small text-muted mt-1">Avg. Rating</div>
                <div class="d-flex align-items-center gap-1 mt-1">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                    <i class="bi <?= $s <= round($avgRating) ? 'bi-star-fill text-warning' : 'bi-star text-muted' ?>" style="font-size:0.75rem;"></i>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="row g-4 mb-4">
    <!-- Views by Month (Line Chart) -->
    <div class="col-lg-8">
        <div class="admin-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Blog Views — Last 12 Months</h6>
                <span class="badge rounded-pill px-3 py-1 small" style="background:rgba(37,99,235,0.1);color:#2563eb;">Monthly</span>
            </div>
            <canvas id="viewsChart" style="max-height:280px;"></canvas>
        </div>
    </div>
    <!-- Reaction Distribution (Doughnut) -->
    <div class="col-lg-4">
        <div class="admin-card p-4 h-100">
            <h6 class="fw-bold mb-4"><i class="bi bi-emoji-smile me-2 text-warning"></i>Emoji Reactions</h6>
            <canvas id="reactionsChart" style="max-height:220px;"></canvas>
            <div class="d-flex flex-column gap-2 mt-3">
                <?php foreach ([
                    ['icon'=>'👍','label'=>'Like',       'val'=>$reactionStats['like']      ?? 0, 'color'=>'#2563eb'],
                    ['icon'=>'❤️','label'=>'Love',       'val'=>$reactionStats['love']      ?? 0, 'color'=>'#ef4444'],
                    ['icon'=>'💡','label'=>'Insightful', 'val'=>$reactionStats['insight']   ?? 0, 'color'=>'#f59e0b'],
                    ['icon'=>'🤯','label'=>'Mind-blown', 'val'=>$reactionStats['mindblown'] ?? 0, 'color'=>'#8b5cf6'],
                ] as $r): ?>
                <div class="d-flex align-items-center justify-content-between small">
                    <span><?= $r['icon'] ?> <?= $r['label'] ?></span>
                    <span class="fw-semibold"><?= number_format($r['val']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="row g-4 mb-4">
    <!-- Posts Created by Month (Bar) -->
    <div class="col-lg-6">
        <div class="admin-card p-4 h-100">
            <h6 class="fw-bold mb-4"><i class="bi bi-bar-chart me-2 text-success"></i>New Posts — Last 12 Months</h6>
            <canvas id="postsChart" style="max-height:240px;"></canvas>
        </div>
    </div>
    <!-- Comments by Day of Week (Bar) -->
    <div class="col-lg-6">
        <div class="admin-card p-4 h-100">
            <h6 class="fw-bold mb-4"><i class="bi bi-chat-dots me-2 text-info"></i>Comments by Day of Week</h6>
            <canvas id="commentsChart" style="max-height:240px;"></canvas>
        </div>
    </div>
</div>

<!-- Top Posts & Categories Grid -->
<div class="row g-4">
    <!-- Top 10 Posts by Views -->
    <div class="col-lg-7">
        <div class="admin-card p-0">
            <div class="p-4 border-bottom" style="border-color:var(--border-color)!important;">
                <h6 class="fw-bold mb-0"><i class="bi bi-trophy me-2 text-warning"></i>Top 10 Posts by Views</h6>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:32px;">#</th>
                            <th>Post Title</th>
                            <th style="width:80px;">Views</th>
                            <th style="width:80px;">Comments</th>
                            <th style="width:70px;">Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($topPosts)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No data available yet.</td></tr>
                    <?php else: ?>
                    <?php foreach (array_slice($topPosts, 0, 10) as $idx => $tpost): ?>
                    <tr>
                        <td>
                            <span class="fw-bold" style="color:<?= $idx < 3 ? '#f59e0b' : 'var(--text-muted)' ?>;"><?= $idx + 1 ?></span>
                        </td>
                        <td>
                            <a href="<?= url('blog/' . $tpost['slug']) ?>" target="_blank" class="text-decoration-none fw-semibold small" style="color:inherit;">
                                <?= htmlspecialchars(substr($tpost['title'], 0, 60)) ?><?= strlen($tpost['title']) > 60 ? '…' : '' ?>
                            </a>
                            <?php if (!empty($tpost['category_name'])): ?>
                            <span class="badge rounded-pill ms-1 small" style="background:rgba(37,99,235,0.08);color:#2563eb;font-size:0.7rem;"><?= htmlspecialchars($tpost['category_name']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-semibold small"><?= number_format($tpost['views'] ?? 0) ?></td>
                        <td class="text-muted small"><?= number_format($tpost['comment_count'] ?? 0) ?></td>
                        <td>
                            <?php $avgR = $tpost['rating_count'] > 0 ? round($tpost['rating_sum'] / $tpost['rating_count'], 1) : 0; ?>
                            <span class="small fw-semibold text-warning"><i class="bi bi-star-fill me-1"></i><?= $avgR ?: '—' ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Categories by Posts -->
    <div class="col-lg-5">
        <div class="admin-card p-4 h-100">
            <h6 class="fw-bold mb-4"><i class="bi bi-collection me-2 text-primary"></i>Top Categories</h6>
            <?php if (empty($topCategories)): ?>
            <p class="text-muted small text-center py-4">No category data yet.</p>
            <?php else: ?>
            <div class="d-flex flex-column gap-3">
                <?php
                $maxCount = max(array_column($topCategories, 'post_count') ?: [1]);
                foreach ($topCategories as $catIdx => $cat):
                    $pct = $maxCount > 0 ? round(($cat['post_count'] / $maxCount) * 100) : 0;
                    $catColor = $cat['color'] ?? '#2563eb';
                ?>
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <i class="<?= htmlspecialchars($cat['icon'] ?? 'bi-folder') ?>" style="color:<?= htmlspecialchars($catColor) ?>;font-size:0.95rem;"></i>
                            <span class="small fw-semibold"><?= htmlspecialchars($cat['name']) ?></span>
                        </div>
                        <span class="small text-muted"><?= number_format($cat['post_count']) ?> posts</span>
                    </div>
                    <div style="height:6px;background:rgba(37,99,235,0.08);border-radius:100px;overflow:hidden;">
                        <div style="height:100%;width:<?= $pct ?>%;background:<?= htmlspecialchars($catColor) ?>;border-radius:100px;transition:width 1s ease;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Chart.js Analytics -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
const textColor = isDark ? '#94a3b8' : '#64748b';
const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';

Chart.defaults.color = textColor;
Chart.defaults.font.family = "'Inter', 'Segoe UI', system-ui, sans-serif";
Chart.defaults.font.size = 11;

// ---- Views by Month Line Chart ----
new Chart(document.getElementById('viewsChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: <?= json_encode($monthLabels ?: ['No Data']) ?>,
        datasets: [{
            label: 'Views',
            data: <?= json_encode($monthViews ?: [0]) ?>,
            fill: true,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,0.08)',
            tension: 0.4,
            pointBackgroundColor: '#2563eb',
            pointRadius: 4,
            pointHoverRadius: 6,
            borderWidth: 2.5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: gridColor }, ticks: { color: textColor } },
            y: { grid: { color: gridColor }, ticks: { color: textColor }, beginAtZero: true }
        }
    }
});

// ---- Reactions Doughnut Chart ----
new Chart(document.getElementById('reactionsChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Like 👍', 'Love ❤️', 'Insightful 💡', 'Mind-blown 🤯'],
        datasets: [{
            data: [
                <?= (int)($reactionStats['like'] ?? 0) ?>,
                <?= (int)($reactionStats['love'] ?? 0) ?>,
                <?= (int)($reactionStats['insight'] ?? 0) ?>,
                <?= (int)($reactionStats['mindblown'] ?? 0) ?>
            ],
            backgroundColor: ['#2563eb','#ef4444','#f59e0b','#8b5cf6'],
            borderWidth: 0,
            hoverOffset: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false }
        },
        cutout: '65%'
    }
});

// ---- New Posts by Month Bar Chart ----
new Chart(document.getElementById('postsChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($monthLabels ?: ['No Data']) ?>,
        datasets: [{
            label: 'Posts',
            data: <?= json_encode($monthPosts ?: [0]) ?>,
            backgroundColor: 'rgba(16,185,129,0.75)',
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: textColor } },
            y: { grid: { color: gridColor }, ticks: { color: textColor, precision: 0 }, beginAtZero: true }
        }
    }
});

// ---- Comments by Day of Week Bar Chart ----
new Chart(document.getElementById('commentsChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($dayLabels ?: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']) ?>,
        datasets: [{
            label: 'Comments',
            data: <?= json_encode($dayCounts ?: [0,0,0,0,0,0,0]) ?>,
            backgroundColor: 'rgba(124,58,237,0.7)',
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: textColor } },
            y: { grid: { color: gridColor }, ticks: { color: textColor, precision: 0 }, beginAtZero: true }
        }
    }
});
</script>
