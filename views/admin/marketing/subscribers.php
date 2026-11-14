<!-- Marketing Subscriber Manager -->
<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h5 class="mb-1 fw-bold text-dark">Subscribers &amp; Segmentation</h5>
        <p class="text-muted mb-0" style="font-size:0.875rem;">Organize users into static mailing lists or dynamic segments.</p>
    </div>
</div>

<?php
    $activeCategory = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : '';
    $statCards = [
        ['key' => '',                 'label' => 'Total Audience',    'value' => $audienceStats['total_audience'],   'icon' => 'bi-globe2',          'color' => '#6366f1', 'bg' => '#eef2ff'],
        ['key' => 'all_emails',       'label' => 'Total Emails',      'value' => $audienceStats['total_emails'],     'icon' => 'bi-envelope-fill',   'color' => '#0ea5e9', 'bg' => '#e0f2fe'],
        ['key' => 'subscriber',       'label' => 'Subscribers',       'value' => $audienceStats['subscribers'],      'icon' => 'bi-person-check-fill','color' => '#10b981', 'bg' => '#d1fae5'],
        ['key' => 'active_user',      'label' => 'Active Users',      'value' => $audienceStats['active_users'],     'icon' => 'bi-lightning-charge-fill', 'color' => '#f59e0b', 'bg' => '#fef3c7'],
        ['key' => 'interested_user',  'label' => 'Interested Users',  'value' => $audienceStats['interested_users'], 'icon' => 'bi-star-fill',       'color' => '#8b5cf6', 'bg' => '#ede9fe'],
        ['key' => 'exclusive_user',   'label' => 'Exclusive Users',   'value' => $audienceStats['exclusive_users'],  'icon' => 'bi-gem',             'color' => '#ec4899', 'bg' => '#fce7f3'],
        ['key' => 'suspended',        'label' => 'Suspended',         'value' => $audienceStats['suspended'],        'icon' => 'bi-pause-circle-fill','color' => '#f97316', 'bg' => '#ffedd5'],
        ['key' => 'banned',           'label' => 'Banned',            'value' => $audienceStats['banned'],           'icon' => 'bi-slash-circle-fill','color' => '#ef4444', 'bg' => '#fee2e2'],
        ['key' => 'today',            'label' => "Today's New Emails",'value' => $audienceStats['today_emails'],     'icon' => 'bi-calendar-plus-fill','color' => '#14b8a6','bg' => '#ccfbf1'],
    ];
?>

<!-- Audience Stats Cards -->
<div class="row g-3 mb-4" id="audienceStatsRow">
    <?php foreach ($statCards as $card):
        $isActive = ($card['key'] === $activeCategory) || ($card['key'] === '' && $activeCategory === '');
        $href = $card['key'] === '' 
            ? url('admin/marketing/subscribers') 
            : url('admin/marketing/subscribers') . '?category=' . $card['key'];
    ?>
    <div class="col-6 col-sm-4 col-md-3 col-lg-auto flex-lg-fill">
        <a href="<?= $href ?>" class="text-decoration-none" title="Filter by <?= $card['label'] ?>">
            <div class="stat-audience-card <?= $isActive ? 'stat-audience-card--active' : '' ?>"
                 style="--card-color:<?= $card['color'] ?>; --card-bg:<?= $card['bg'] ?>;">
                <div class="stat-audience-icon">
                    <i class="bi <?= $card['icon'] ?>"></i>
                </div>
                <div class="stat-audience-count"><?= number_format($card['value']) ?></div>
                <div class="stat-audience-label"><?= $card['label'] ?></div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<style>
.stat-audience-card {
    background: #fff;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    padding: 16px 14px 14px;
    text-align: center;
    cursor: pointer;
    transition: all 0.22s ease;
    position: relative;
    overflow: hidden;
    min-width: 110px;
}
.stat-audience-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: var(--card-color);
    border-radius: 14px 14px 0 0;
}
.stat-audience-card:hover {
    border-color: var(--card-color);
    background: var(--card-bg);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.09);
}
.stat-audience-card--active {
    border-color: var(--card-color) !important;
    background: var(--card-bg) !important;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}
.stat-audience-icon {
    font-size: 1.5rem;
    color: var(--card-color);
    margin-bottom: 6px;
}
.stat-audience-count {
    font-size: 1.55rem;
    font-weight: 700;
    color: #111827;
    line-height: 1.2;
}
.stat-audience-label {
    font-size: 0.72rem;
    color: #6b7280;
    font-weight: 500;
    margin-top: 3px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.stat-audience-card--active .stat-audience-label {
    color: var(--card-color);
    font-weight: 700;
}
</style>

<?php if ($activeCategory !== ''): ?>
<div class="alert alert-info d-flex align-items-center gap-2 mb-4 py-2" style="font-size:0.875rem;">
    <i class="bi bi-funnel-fill"></i>
    <span>
        Showing emails filtered by: <strong><?= ucwords(str_replace('_', ' ', $activeCategory)) ?></strong>
    </span>
    <a href="<?= url('admin/marketing/subscribers') ?>" class="btn btn-sm btn-outline-secondary ms-auto">
        <i class="bi bi-x-lg me-1"></i>Clear Filter
    </a>
</div>
<?php endif; ?>

<!-- Tabs -->
<ul class="nav nav-tabs mb-4" id="subscriberTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="subscribers-tab" data-bs-toggle="tab" data-bs-target="#subscribers" type="button" role="tab"><i class="bi bi-people me-2"></i>All Subscribers</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="lists-tab" data-bs-toggle="tab" data-bs-target="#lists" type="button" role="tab"><i class="bi bi-folder2-open me-2"></i>Email Lists</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="segments-tab" data-bs-toggle="tab" data-bs-target="#segments" type="button" role="tab"><i class="bi bi-diagram-3 me-2"></i>Dynamic Segments</button>
    </li>
</ul>

<div class="tab-content" id="subscriberTabContent">
    <!-- Tab 1: All Subscribers & Filter Builder -->
    <div class="tab-pane fade show active" id="subscribers" role="tabpanel">
        <!-- Filter Builder Card -->
        <div class="admin-card mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-funnel text-primary me-2"></i>Dynamic Filter Builder</h6>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="addFilterRow()"><i class="bi bi-plus-lg me-1"></i>Add Rule</button>
                    <?php if (!empty($rules)): ?>
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#saveSegmentModal"><i class="bi bi-save me-1"></i>Save as Segment</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="<?= url('admin/marketing/subscribers') ?>" id="filterForm">
                    <?php if ($activeCategory !== ''): ?>
                        <input type="hidden" name="category" value="<?= htmlspecialchars($activeCategory) ?>">
                    <?php endif; ?>
                    <div id="filterRowsContainer">
                        <?php if (empty($rules)): ?>
                            <div class="row g-2 mb-2 filter-row align-items-center">
                                <div class="col-md-3">
                                    <select name="rules[0][field]" class="dc-form-control field-select">
                                        <option value="status">Status</option>
                                        <option value="category">Category</option>
                                        <option value="source">Source</option>
                                        <option value="country">Country</option>
                                        <option value="total_spent">Total Spending</option>
                                        <option value="purchase_frequency">Order Count</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="rules[0][operator]" class="dc-form-control">
                                        <option value="equals">Equals</option>
                                        <option value="not_equals">Does Not Equal</option>
                                        <option value="contains">Contains</option>
                                        <option value="greater_than">Greater Than</option>
                                        <option value="less_than">Less Than</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="rules[0][value]" class="dc-form-control" placeholder="Value...">
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeFilterRow(this)"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($rules as $idx => $r): ?>
                                <div class="row g-2 mb-2 filter-row align-items-center">
                                    <div class="col-md-3">
                                        <select name="rules[<?= $idx ?>][field]" class="dc-form-control field-select">
                                            <option value="status" <?= $r['field'] === 'status' ? 'selected' : '' ?>>Status</option>
                                            <option value="category" <?= $r['field'] === 'category' ? 'selected' : '' ?>>Category</option>
                                            <option value="source" <?= $r['field'] === 'source' ? 'selected' : '' ?>>Source</option>
                                            <option value="country" <?= $r['field'] === 'country' ? 'selected' : '' ?>>Country</option>
                                            <option value="total_spent" <?= $r['field'] === 'total_spent' ? 'selected' : '' ?>>Total Spending</option>
                                            <option value="purchase_frequency" <?= $r['field'] === 'purchase_frequency' ? 'selected' : '' ?>>Order Count</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="rules[<?= $idx ?>][operator]" class="dc-form-control">
                                            <option value="equals" <?= $r['operator'] === 'equals' ? 'selected' : '' ?>>Equals</option>
                                            <option value="not_equals" <?= $r['operator'] === 'not_equals' ? 'selected' : '' ?>>Does Not Equal</option>
                                            <option value="contains" <?= $r['operator'] === 'contains' ? 'selected' : '' ?>>Contains</option>
                                            <option value="greater_than" <?= $r['operator'] === 'greater_than' ? 'selected' : '' ?>>Greater Than</option>
                                            <option value="less_than" <?= $r['operator'] === 'less_than' ? 'selected' : '' ?>>Less Than</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="rules[<?= $idx ?>][value]" class="dc-form-control" value="<?= htmlspecialchars($r['value']) ?>" placeholder="Value...">
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button type="button" class="btn btn-outline-danger" onclick="removeFilterRow(this)"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="mt-3 text-end">
                        <?php if (!empty($rules)): ?>
                            <a href="<?= url('admin/marketing/subscribers') ?><?= $activeCategory ? '?category=' . $activeCategory : '' ?>" class="btn btn-outline-secondary me-2">Clear Filters</a>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Subscribers Table Card -->
        <div class="admin-card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 fw-bold">
                    <?php if ($activeCategory !== ''): ?>
                        <i class="bi bi-funnel-fill text-primary me-2"></i>
                        <?= ucwords(str_replace('_', ' ', $activeCategory)) ?> — 
                    <?php endif; ?>
                    Contact Records (<strong><?= number_format($totalSubscribers) ?></strong> matches)
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                        <thead>
                            <tr>
                                <th>Subscriber</th>
                                <th>Status</th>
                                <th>Category</th>
                                <th>Source</th>
                                <th>Geo / Language</th>
                                <th>Spent / Orders</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($subscribers)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-people fs-1 mb-2 text-secondary d-block"></i>
                                        No subscribers match your search filters.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($subscribers as $sub): ?>
                                    <?php 
                                        $meta = json_decode($sub['meta_data'] ?? '{}', true);
                                        $subCategory = $sub['category'] ?? 'subscriber';
                                        $categoryLabels = [
                                            'subscriber'      => ['label' => 'Subscriber',      'class' => 'bg-success-subtle text-success'],
                                            'active_user'     => ['label' => 'Active User',      'class' => 'bg-warning-subtle text-warning'],
                                            'interested_user' => ['label' => 'Interested',       'class' => 'bg-info-subtle text-info'],
                                            'exclusive_user'  => ['label' => 'Exclusive',        'class' => 'bg-primary-subtle text-primary'],
                                        ];
                                        $catInfo = $categoryLabels[$subCategory] ?? ['label' => ucwords(str_replace('_',' ',$subCategory)), 'class' => 'bg-secondary-subtle text-secondary'];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($sub['name'] ?? 'Mailing List Member') ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($sub['email']) ?></small>
                                        </td>
                                        <td>
                                            <?php if ($sub['status'] === 'active'): ?>
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                            <?php elseif ($sub['status'] === 'suspended'): ?>
                                                <span class="badge bg-warning-subtle text-warning">Suspended</span>
                                            <?php elseif ($sub['status'] === 'unsubscribed'): ?>
                                                <span class="badge bg-secondary-subtle text-secondary">Unsubscribed</span>
                                            <?php elseif ($sub['status'] === 'banned'): ?>
                                                <span class="badge bg-danger-subtle text-danger">Banned</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($sub['status']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?= url('admin/marketing/subscribers') ?>?category=<?= urlencode($subCategory) ?>"
                                               class="badge <?= $catInfo['class'] ?> text-decoration-none" title="Filter by this category">
                                                <?= $catInfo['label'] ?>
                                            </a>
                                        </td>
                                        <td><span class="text-capitalize"><?= htmlspecialchars(str_replace('_', ' ', $sub['source'])) ?></span></td>
                                        <td>
                                            <i class="bi bi-geo-alt-fill text-muted me-1"></i><?= htmlspecialchars($meta['country'] ?? 'Global') ?> 
                                            <small class="text-muted">(<?= htmlspecialchars($meta['language'] ?? 'en') ?>)</small>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-success">₹<?= number_format($meta['total_spent'] ?? 0, 2) ?></span>
                                            <small class="text-muted d-block">(<?= $meta['purchase_frequency'] ?? 0 ?> orders)</small>
                                        </td>
                                        <td><?= date('Y-m-d H:i', strtotime($sub['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="p-3 border-top d-flex justify-content-between align-items-center">
                        <small class="text-muted">Showing page <?= $page ?> of <?= $totalPages ?></small>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page - 1 ?><?= $activeCategory ? '&category=' . $activeCategory : '' ?><?= !empty($rules) ? '&' . http_build_query(['rules' => $rules]) : '' ?>">Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $page === $i ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?><?= $activeCategory ? '&category=' . $activeCategory : '' ?><?= !empty($rules) ? '&' . http_build_query(['rules' => $rules]) : '' ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page + 1 ?><?= $activeCategory ? '&category=' . $activeCategory : '' ?><?= !empty($rules) ? '&' . http_build_query(['rules' => $rules]) : '' ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tab 2: Email Lists -->
    <div class="tab-pane fade" id="lists" role="tabpanel">
        <div class="row g-4">
            <!-- Lists Table -->
            <div class="col-md-8">
                <div class="admin-card">
                    <div class="card-header bg-light py-3">
                        <h6 class="mb-0 fw-bold">Static Mailing Lists</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                                <thead>
                                    <tr>
                                        <th>List Details</th>
                                        <th>Contacts</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($lists)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">No custom lists created. Create one on the right.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($lists as $l): ?>
                                            <tr>
                                                <td>
                                                    <div class="fw-bold text-dark"><?= htmlspecialchars($l['name']) ?></div>
                                                    <small class="text-muted"><?= htmlspecialchars($l['description'] ?? 'No description') ?></small>
                                                </td>
                                                <td><span class="badge bg-primary"><?= number_format($l['subscriber_count']) ?> contacts</span></td>
                                                <td>
                                                    <a href="<?= url('admin/marketing/lists/delete/' . $l['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this list?')"><i class="bi bi-trash me-1"></i>Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create List Form -->
            <div class="col-md-4">
                <div class="admin-card">
                    <div class="card-header bg-light py-3">
                        <h6 class="mb-0 fw-bold">Create Static List</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= url('admin/marketing/lists/store') ?>">
                            <?= csrfField() ?>
                            <div class="form-group mb-3">
                                <label class="dc-form-label">List Name</label>
                                <input type="text" name="name" class="dc-form-control" required placeholder="e.g. VIP Newsletter, Premium Buyers">
                            </div>
                            <div class="form-group mb-3">
                                <label class="dc-form-label">Description</label>
                                <textarea name="description" class="dc-form-control" rows="3" placeholder="Explain list target audience..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Create List</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 3: Dynamic Segments -->
    <div class="tab-pane fade" id="segments" role="tabpanel">
        <div class="admin-card col-md-10 mx-auto">
            <div class="card-header bg-light py-3">
                <h6 class="mb-0 fw-bold">Dynamic Segments (Auto-evaluates)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                        <thead>
                            <tr>
                                <th>Segment Name</th>
                                <th>Dynamic Filter Rules Summary</th>
                                <th>Compiled SQL Preview</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($segments)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No dynamic segments saved yet. Use the filter builder tab to generate a segment.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($segments as $seg): ?>
                                    <?php 
                                        $rulesArr = json_decode($seg['rules'], true);
                                    ?>
                                    <tr>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars($seg['name']) ?></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?php foreach ($rulesArr as $rule): ?>
                                                    <span class="badge bg-light text-dark border">
                                                        <?= htmlspecialchars($rule['field']) ?> (<?= htmlspecialchars($rule['operator']) ?>) &ldquo;<?= htmlspecialchars($rule['value']) ?>&rdquo;
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <code style="font-size:0.75rem;">
                                                <?php
                                                    $subComp = new MarketingSubscriber();
                                                    $comp = $subComp->compileRules($rulesArr);
                                                    echo htmlspecialchars(substr($comp['where'], 4));
                                                ?>
                                            </code>
                                        </td>
                                        <td>
                                            <a href="<?= url('admin/marketing/segments/delete/' . $seg['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this segment?')"><i class="bi bi-trash me-1"></i>Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Save Segment Dialog -->
<div class="modal fade" id="saveSegmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?= url('admin/marketing/segments/store') ?>">
                <?= csrfField() ?>
                
                <!-- Forward filter rules from search -->
                <?php if (!empty($rules)): ?>
                    <?php foreach ($rules as $idx => $r): ?>
                        <input type="hidden" name="rules[<?= $idx ?>][field]" value="<?= htmlspecialchars($r['field']) ?>">
                        <input type="hidden" name="rules[<?= $idx ?>][operator]" value="<?= htmlspecialchars($r['operator']) ?>">
                        <input type="hidden" name="rules[<?= $idx ?>][value]" value="<?= htmlspecialchars($r['value']) ?>">
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="modal-header">
                    <h6 class="modal-title fw-bold" id="exampleModalLabel"><i class="bi bi-save2 text-primary me-2"></i>Save Filter Builder Rules as Segment</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted" style="font-size:0.82rem;">This saves your current filter configuration as a dynamic segment. Any new subscribers registering that match these conditions will be added automatically.</p>
                    <div class="form-group">
                        <label class="dc-form-label">Segment Name</label>
                        <input type="text" name="name" class="dc-form-control" required placeholder="e.g. Inactive Indian Buyers, Bestselling Products Customers">
                    </div>
                </div>
                <div class="modal-body bg-light border-top">
                    <small class="fw-bold text-dark d-block mb-1">MAPPED FILTERS:</small>
                    <ul class="mb-0 text-muted" style="font-size:0.8rem; padding-left:16px;">
                        <?php foreach ($rules as $r): ?>
                            <li>Field: <code><?= htmlspecialchars($r['field']) ?></code> | Condition: <strong><?= htmlspecialchars($r['operator']) ?></strong> | Value: <code>"<?= htmlspecialchars($r['value']) ?>"</code></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Save Segment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let filterIndex = <?= count($rules) > 0 ? count($rules) : 1 ?>;

    function addFilterRow() {
        const container = document.getElementById('filterRowsContainer');
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 filter-row align-items-center';
        row.innerHTML = `
            <div class="col-md-3">
                <select name="rules[${filterIndex}][field]" class="dc-form-control field-select">
                    <option value="status">Status</option>
                    <option value="category">Category</option>
                    <option value="source">Source</option>
                    <option value="country">Country</option>
                    <option value="total_spent">Total Spending</option>
                    <option value="purchase_frequency">Order Count</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="rules[${filterIndex}][operator]" class="dc-form-control">
                    <option value="equals">Equals</option>
                    <option value="not_equals">Does Not Equal</option>
                    <option value="contains">Contains</option>
                    <option value="greater_than">Greater Than</option>
                    <option value="less_than">Less Than</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="rules[${filterIndex}][value]" class="dc-form-control" placeholder="Value...">
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-outline-danger" onclick="removeFilterRow(this)"><i class="bi bi-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
        filterIndex++;
    }

    function removeFilterRow(btn) {
        const rows = document.querySelectorAll('.filter-row');
        if (rows.length > 1) {
            btn.closest('.filter-row').remove();
        } else {
            alert('At least one filter rule is required.');
        }
    }

    // Handles hash-based tab navigation
    document.addEventListener("DOMContentLoaded", function() {
        const hash = window.location.hash;
        if (hash) {
            const tabEl = document.querySelector(`button[data-bs-target="${hash}"]`);
            if (tabEl) {
                bootstrap.Tab.getOrCreateInstance(tabEl).show();
            }
        }
    });
</script>
