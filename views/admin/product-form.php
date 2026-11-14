<!-- Admin Product Form (Create/Edit) -->
<?php 
    $isEdit = !empty($product); 
    $downloadType = 'file';
    if ($isEdit && !empty($product['download_file'])) {
        $fileVal = trim($product['download_file']);
        $decoded = json_decode($fileVal, true);
        if (!is_array($decoded) && $fileVal !== '' && ($fileVal[0] === '[' || $fileVal[0] === '{')) {
            $decoded = json_decode(stripslashes($fileVal), true);
        }
        if (is_array($decoded)) {
            $firstItem = $decoded[0] ?? '';
            $checkStr = '';
            if (is_array($firstItem)) {
                $checkStr = $firstItem['url'] ?? '';
            } else {
                $checkStr = (string)$firstItem;
            }
            if (strpos($checkStr, 'http') === 0) {
                $downloadType = 'url';
            }
        } elseif (strpos($fileVal, 'http') === 0) {
            $downloadType = 'url';
        }
    }
?>
<div class="mb-4">
    <a href="<?= url('admin/products') ?>" class="text-muted" style="font-size:0.88rem;"><i class="bi bi-arrow-left me-1"></i>Back to Products</a>
</div>

<form method="POST" action="<?= $isEdit ? url('admin/products/update/' . $product['id']) : url('admin/products/store') ?>" enctype="multipart/form-data" class="admin-form">
    <?= csrfField() ?>
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <div class="admin-card mb-4">
                <div class="card-header"><h6>Product Information</h6></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Product Title *</label>
                        <input type="text" name="title" class="dc-form-control" value="<?= htmlspecialchars($product['title'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Short Description</label>
                        <textarea name="short_description" class="dc-form-control" rows="2"><?= htmlspecialchars($product['short_description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Full Description</label>
                        <textarea name="description" class="dc-form-control" rows="8" id="productDesc"><?= $product['description'] ?? '' ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="admin-card mb-4">
                <div class="card-header"><h6>Pricing</h6></div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group"><label>MRP (₹)</label><input type="number" name="mrp" class="dc-form-control" step="0.01" value="<?= $product['mrp'] ?? '' ?>"></div>
                        <div class="form-group"><label>Sale Price (₹) *</label><input type="number" name="sale_price" class="dc-form-control" step="0.01" value="<?= $product['sale_price'] ?? '' ?>" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>GST %</label><input type="number" name="gst_percent" class="dc-form-control" step="0.01" value="<?= $product['gst_percent'] ?? '18' ?>"></div>
                        <div class="form-group"><label>Product Type</label>
                            <select name="product_type" class="dc-form-control">
                                <?php $pts = ['ready_website'=>'Ready-made Website','custom_website'=>'Custom Website','php_script'=>'PHP Script','html_template'=>'HTML Template','theme'=>'Theme','plugin'=>'Plugin','other'=>'Other'];
                                foreach ($pts as $k=>$v): ?>
                                <option value="<?= $k ?>" <?= ($product['product_type'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="flex:1;">
                            <label class="d-flex align-items-center justify-content-between">
                                <span><i class="bi bi-key-fill text-warning me-1"></i> DigiWS License Server Product ID</span>
                                <?php if (!empty($product['digiws_product_id'])): ?>
                                    <span class="badge bg-success" style="font-size:0.75rem;"><i class="bi bi-check-circle me-1"></i> Active Linked ID: <?= htmlspecialchars($product['digiws_product_id']) ?></span>
                                <?php endif; ?>
                            </label>
                            <input type="text" name="digiws_product_id" class="dc-form-control" value="<?= htmlspecialchars($product['digiws_product_id'] ?? '') ?>" placeholder="e.g. PRD-BFVRD0BGJB or PRD-Y61HM3IJ0W">
                            <small class="text-muted">Enter Product ID from DigiWS License Server (https://license.digiws.com) to auto-generate license keys on purchase.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Variants -->
            <div class="admin-card mb-4">
                <div class="card-header"><h6>Variants & Options</h6></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="d-flex align-items-center gap-2">
                            <input type="checkbox" name="setup_option" value="1" <?= ($product['setup_option'] ?? 0) ? 'checked' : '' ?> style="accent-color:var(--accent-primary);">
                            Offer Website Setup Service
                        </label>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Setup Price (₹)</label><input type="number" name="setup_price" class="dc-form-control" step="0.01" value="<?= $product['setup_price'] ?? '0' ?>"></div>
                        <div></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>6 Months Support Price (₹)</label><input type="number" name="support_6_months" class="dc-form-control" step="0.01" value="<?= $product['support_6_months'] ?? '0' ?>"></div>
                        <div class="form-group"><label>12 Months Support Price (₹)</label><input type="number" name="support_12_months" class="dc-form-control" step="0.01" value="<?= $product['support_12_months'] ?? '0' ?>"></div>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="admin-card mb-4">
                <div class="card-header"><h6>Features List</h6></div>
                <div class="card-body">
                    <div class="form-group mb-2">
                        <label class="form-label font-bold small mb-1">Key Features (Type and press Enter or Comma to add)</label>
                        <div id="features-pills-container" class="d-flex flex-wrap gap-2 mb-3">
                            <!-- Visual tag pills will be dynamically generated here -->
                        </div>
                        <input type="text" id="featureTagInput" class="dc-form-control" placeholder="e.g. Modern & Responsive Design, SEO Optimized...">
                        <small class="text-muted mt-1 d-block">You can type a feature and hit Comma or Enter, or paste a comma-separated list to automatically split them into separate features.</small>
                    </div>
                </div>
            </div>

            <!-- Media -->
            <div class="admin-card mb-4">
                <div class="card-header"><h6>Media</h6></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Thumbnail Image</label>
                        <?php if ($isEdit && $product['thumbnail']): ?>
                        <div class="mb-2 p-2 border rounded-3 bg-light d-inline-block">
                            <img src="<?= uploadUrl($product['thumbnail']) ?>" style="height:80px;border-radius:8px; display:block;">
                            <label class="d-flex align-items-center gap-2 mt-2 text-danger fw-bold" style="font-size:0.8rem; cursor:pointer;">
                                <input type="checkbox" name="delete_thumbnail" value="1" style="accent-color:#ef4444;"> Remove Thumbnail
                            </label>
                        </div>
                        <?php endif; ?>
                        <input type="file" name="thumbnail" class="dc-form-control" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Setup Video / Guide URL (YouTube/Vimeo)</label>
                        <input type="url" name="video_url" class="dc-form-control" value="<?= htmlspecialchars($product['video_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=...">
                    </div>
                    <div class="form-group">
                        <label>Screenshots (multiple)</label>
                        <?php if ($isEdit && !empty($product['images'])): ?>
                        <div class="d-flex gap-2 mb-2 flex-wrap">
                            <?php foreach ($product['images'] as $img): ?>
                            <div class="p-1 border rounded bg-light text-center">
                                <img src="<?= uploadUrl($img['image_path']) ?>" style="height:60px;border-radius:4px;display:block;margin-bottom:4px;">
                                <label class="d-block text-danger fw-bold m-0" style="font-size:0.75rem;cursor:pointer;">
                                    <input type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>" style="accent-color:#ef4444;"> Delete
                                </label>
                            </div>
                            <?php endforeach; ?>
                            <small class="text-muted d-block w-100">Check to delete selected images.</small>
                        </div>
                        <?php endif; ?>
                        <input type="file" name="screenshots[]" class="dc-form-control" accept="image/*" multiple>
                    </div>
                    <!-- End Media Card Body -->
                </div>
            </div>

            <!-- Download Resources -->
            <div class="admin-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Download Resources (Multiple Platforms & Links)</h6>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addResourceRow()">
                        <i class="bi bi-plus-circle me-1"></i> Add Resource
                    </button>
                </div>
                <div class="card-body">
                    <div id="resources-list-container" class="d-flex flex-column gap-3">
                        <!-- Resource rows will be added here dynamically -->
                    </div>
                    <small class="text-muted mt-2 d-block">
                        Add direct files (ZIP/RAR) or links from Google Drive, Dropbox, MEGA, OneDrive, etc.
                    </small>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Publish -->
            <div class="admin-card mb-4">
                <div class="card-header"><h6>Publish</h6></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="dc-form-control">
                            <option value="draft" <?= ($product['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="active" <?= ($product['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-dc-primary w-100 mt-2"><i class="bi bi-check-lg me-2"></i><?= $isEdit ? 'Update Product' : 'Create Product' ?></button>
                </div>
            </div>

            <!-- Category -->
            <div class="admin-card mb-4">
                <div class="card-header"><h6>Categories</h6></div>
                <div class="card-body">
                    <div class="category-scroll-list" style="max-height: 200px; overflow-y: auto; padding: 10px; border: 1px solid #eee; border-radius: 8px;">
                        <?php foreach ($categories as $cat): ?>
                        <label class="d-flex align-items-center gap-2 mb-2" style="cursor:pointer;">
                            <input type="checkbox" name="category_ids[]" value="<?= $cat['id'] ?>" 
                                <?= in_array($cat['id'], $product['category_ids'] ?? []) ? 'checked' : '' ?>
                                style="accent-color:var(--accent-primary);">
                            <span style="font-size:0.9rem;"><?= htmlspecialchars($cat['name']) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <small class="text-muted mt-2 d-block">Select one or more categories.</small>
                </div>
            </div>

            <!-- Labels -->
            <div class="admin-card mb-4">
                <div class="card-header"><h6>Labels</h6></div>
                <div class="card-body">
                    <label class="d-flex align-items-center gap-2 mb-2"><input type="checkbox" name="is_featured" value="1" <?= ($product['is_featured'] ?? 0) ? 'checked' : '' ?> style="accent-color:var(--accent-primary);"> Featured</label>
                    <label class="d-flex align-items-center gap-2 mb-2"><input type="checkbox" name="is_bestseller" value="1" <?= ($product['is_bestseller'] ?? 0) ? 'checked' : '' ?> style="accent-color:var(--accent-orange);"> Bestseller</label>
                    <label class="d-flex align-items-center gap-2 mb-2"><input type="checkbox" name="is_new" value="1" <?= ($product['is_new'] ?? 0) ? 'checked' : '' ?> style="accent-color:var(--accent-green);"> New</label>
                    <label class="d-flex align-items-center gap-2"><input type="checkbox" name="is_exclusive" value="1" <?= ($product['is_exclusive'] ?? 0) ? 'checked' : '' ?> style="accent-color:var(--accent-pink);"> Exclusive</label>
                </div>
            </div>

            <!-- URLs & Tags -->
            <div class="admin-card mb-4">
                <div class="card-header"><h6>URLs & Tags</h6></div>
                <div class="card-body">
                    <div class="form-group"><label>Live Preview URL</label><input type="url" name="preview_url" class="dc-form-control" value="<?= htmlspecialchars($product['preview_url'] ?? '') ?>" placeholder="https://..."></div>
                    <div class="form-group"><label>External URL</label><input type="url" name="external_url" class="dc-form-control" value="<?= htmlspecialchars($product['external_url'] ?? '') ?>" placeholder="https://..."></div>
                    <div class="form-group"><label>Tags (comma separated)</label><input type="text" name="tags" class="dc-form-control" value="<?= htmlspecialchars($product['tags'] ?? '') ?>" placeholder="php, template, ecommerce"></div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Features Tag Input Box
const featurePillsContainer = document.getElementById('features-pills-container');
const featureTagInput = document.getElementById('featureTagInput');
let featuresList = [];

// Initialize existing features
<?php
$existingFeatures = [];
if ($isEdit && !empty($product['features'])) {
    $existingFeatures = $product['features'];
}
?>
featuresList = <?= json_encode(array_column($existingFeatures, 'feature')) ?>;

function renderFeaturePills() {
    if (!featurePillsContainer) return;
    featurePillsContainer.innerHTML = '';
    if (featuresList.length === 0) {
        featurePillsContainer.innerHTML = '<span class="text-muted small">No features added yet. Type below.</span>';
        return;
    }
    featuresList.forEach((feature, index) => {
        const pill = document.createElement('span');
        pill.className = 'badge bg-primary text-white p-2 d-inline-flex align-items-center gap-2';
        pill.style.borderRadius = '20px';
        pill.style.fontSize = '0.82rem';
        pill.style.fontWeight = '500';
        pill.innerHTML = `
            ${escapeHtml(feature)}
            <input type="hidden" name="features[]" value="${escapeHtml(feature)}">
            <i class="bi bi-x-circle-fill" onclick="removeFeature(${index})" style="cursor:pointer; font-size:0.9rem; opacity:0.8; transition:opacity 0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.8"></i>
        `;
        featurePillsContainer.appendChild(pill);
    });
}

function removeFeature(index) {
    featuresList.splice(index, 1);
    renderFeaturePills();
}

function addFeaturesFromString(str) {
    const parts = str.split(',').map(p => p.trim()).filter(p => p.length > 0);
    parts.forEach(part => {
        if (!featuresList.includes(part)) {
            featuresList.push(part);
        }
    });
    renderFeaturePills();
}

if (featureTagInput) {
    featureTagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            const val = this.value.trim();
            if (val) {
                addFeaturesFromString(val);
                this.value = '';
            }
        }
    });

    featureTagInput.addEventListener('blur', function() {
        const val = this.value.trim();
        if (val) {
            addFeaturesFromString(val);
            this.value = '';
        }
    });

    // Detect pasting comma-separated values
    featureTagInput.addEventListener('paste', function(e) {
        setTimeout(() => {
            const val = this.value.trim();
            if (val.includes(',')) {
                addFeaturesFromString(val);
                this.value = '';
            }
        }, 10);
    });
}

function escapeHtml(text) {
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Initial render of features
renderFeaturePills();


// Download Resources Multiple Platforms Row Builder
let resourceIndex = 0;

function addResourceRow(existing = null) {
    const container = document.getElementById('resources-list-container');
    if (!container) return;
    const rowId = `resource-row-${resourceIndex}`;
    const row = document.createElement('div');
    row.className = 'p-3 border rounded-3 bg-white position-relative resource-row shadow-sm';
    row.id = rowId;
    
    const platform = existing ? existing.platform : 'gdrive';
    const url = existing ? existing.url : '';
    
    row.innerHTML = `
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label font-bold small mb-1">Platform</label>
                <select name="resources[${resourceIndex}][platform]" class="dc-form-control platform-select" onchange="toggleResourceRowInput('${rowId}', this.value)" style="height:38px; border-radius:6px; font-size:0.85rem;">
                    <option value="gdrive" ${platform === 'gdrive' ? 'selected' : ''}>Google Drive</option>
                    <option value="dropbox" ${platform === 'dropbox' ? 'selected' : ''}>Dropbox</option>
                    <option value="mega" ${platform === 'mega' ? 'selected' : ''}>MEGA</option>
                    <option value="onedrive" ${platform === 'onedrive' ? 'selected' : ''}>OneDrive</option>
                    <option value="server" ${platform === 'server' ? 'selected' : ''}>Direct File Upload</option>
                    <option value="other" ${platform === 'other' ? 'selected' : ''}>Other / Custom URL</option>
                </select>
            </div>
            
            <div class="col-md-7 input-col">
                <!-- Inputs will be toggled here -->
            </div>
            
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-1" onclick="document.getElementById('${rowId}').remove()" style="height:38px; border-radius:6px; font-size:0.85rem;">
                    <i class="bi bi-trash"></i> Delete
                </button>
            </div>
        </div>
    `;
    
    container.appendChild(row);
    
    // Inject the correct input fields and configure them
    const inputCol = row.querySelector('.input-col');
    if (platform === 'server') {
        if (url) {
            // Show existing file path & target checkbox
            inputCol.innerHTML = `
                <div class="d-flex align-items-center justify-content-between p-2 border rounded bg-light" style="height:38px;">
                    <span class="text-truncate small fw-semibold" title="${escapeHtml(url)}" style="font-size:0.8rem; color:#475569;">
                        <i class="bi bi-file-earmark-zip-fill text-primary me-1"></i> ${escapeHtml(url.split('/').pop())}
                    </span>
                    <input type="hidden" name="resources[${resourceIndex}][existing_file]" value="${escapeHtml(url)}">
                </div>
            `;
        } else {
            // Show new file input
            inputCol.innerHTML = `
                <label class="form-label font-bold small mb-1">Select ZIP File</label>
                <input type="file" name="resource_file_${resourceIndex}" class="dc-form-control" accept=".zip,.rar,.gz,.tar,.7z" required style="height:38px; padding:6px 12px; font-size:0.8rem;">
            `;
        }
    } else {
        // Show URL input
        inputCol.innerHTML = `
            <label class="form-label font-bold small mb-1">Download Link URL</label>
            <input type="url" name="resources[${resourceIndex}][url]" class="dc-form-control" value="${escapeHtml(url)}" placeholder="https://..." required style="height:38px; font-size:0.82rem;">
        `;
    }
    
    resourceIndex++;
}

function toggleResourceRowInput(rowId, val) {
    const row = document.getElementById(rowId);
    const inputCol = row.querySelector('.input-col');
    const index = rowId.split('-').pop();
    
    if (val === 'server') {
        inputCol.innerHTML = `
            <label class="form-label font-bold small mb-1">Select ZIP File</label>
            <input type="file" name="resource_file_${index}" class="dc-form-control" accept=".zip,.rar,.gz,.tar,.7z" required style="height:38px; padding:6px 12px; font-size:0.8rem;">
        `;
    } else {
        inputCol.innerHTML = `
            <label class="form-label font-bold small mb-1">Download Link URL</label>
            <input type="url" name="resources[${index}][url]" class="dc-form-control" placeholder="https://..." required style="height:38px; font-size:0.82rem;">
        `;
    }
}

// Load existing resources if any
document.addEventListener("DOMContentLoaded", function() {
    <?php
    $resourcesJson = [];
    if ($isEdit && !empty($product['download_file'])) {
        $fileVal = trim($product['download_file']);
        $decoded = json_decode($fileVal, true);
        if (!is_array($decoded) && $fileVal !== '' && ($fileVal[0] === '[' || $fileVal[0] === '{')) {
            $decoded = json_decode(stripslashes($fileVal), true);
        }
        if (is_array($decoded)) {
            foreach ($decoded as $item) {
                if (is_array($item)) {
                    if (isset($item['url'])) {
                        $resourcesJson[] = $item;
                    }
                } else {
                    $isUrl = (strpos((string)$item, 'http') === 0);
                    $resourcesJson[] = [
                        'platform' => $isUrl ? 'other' : 'server',
                        'url'      => $item
                    ];
                }
            }
        } else {
            $lines = array_filter(explode("\n", str_replace("\r", "", $product['download_file'])));
            foreach ($lines as $line) {
                $line = trim($line);
                $isUrl = (strpos($line, 'http') === 0);
                if (!$isUrl && preg_match('/^[a-zA-Z0-9_-]{15,50}$/', $line)) {
                    $resourcesJson[] = [
                        'platform' => 'gdrive',
                        'url'      => 'https://drive.google.com/file/d/' . $line . '/view?usp=sharing'
                    ];
                } else {
                    $resourcesJson[] = [
                        'platform' => $isUrl ? 'other' : 'server',
                        'url'      => $line
                    ];
                }
            }
        }
    }
    ?>
    const existingRes = <?= json_encode($resourcesJson) ?>;
    if (existingRes && existingRes.length > 0) {
        existingRes.forEach(res => addResourceRow(res));
    } else {
        // Add one default row
        addResourceRow();
    }
});
</script>
