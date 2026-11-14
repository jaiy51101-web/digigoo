<!-- Admin Blog Form (Create/Edit) -->
<?php 
    $isEdit = !empty($post); 
    // Ensure lists are initialized
    $categories = $categories ?? [];
    $products = $products ?? [];
    $allTags = $allTags ?? [];
    $postTags = $postTags ?? [];
    $tagNames = [];
    foreach ($postTags as $t) {
        $tagNames[] = $t['name'];
    }
    $tagsString = implode(', ', $tagNames);
?>
<div class="mb-4 d-flex align-items-center justify-content-between">
    <a href="<?= url('admin/blog') ?>" class="text-muted" style="font-size:0.88rem; text-decoration: none;">
        <i class="bi bi-arrow-left me-1"></i>Back to Blog Posts
    </a>
    <span class="text-muted small">Current Time: <?= date('Y-m-d H:i') ?></span>
</div>

<form method="POST" action="<?= $isEdit ? url('admin/blog/update/' . $post['id']) : url('admin/blog/store') ?>" enctype="multipart/form-data" class="admin-form" id="blogForm">
    <?= csrfField() ?>
    
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            
            <!-- Tab Headers -->
            <ul class="nav nav-tabs mb-4 border-bottom" id="blogFormTabs" role="tablist" style="gap: 5px;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active border-0 fw-semibold px-4 py-2.5 rounded-top-3" id="content-tab" data-bs-toggle="tab" data-bs-target="#content-pane" type="button" role="tab" aria-controls="content-pane" aria-selected="true" style="font-family: var(--font-title); font-size: 0.95rem;">
                        <i class="bi bi-file-earmark-text me-2"></i>Write Article
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link border-0 fw-semibold px-4 py-2.5 rounded-top-3" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo-pane" type="button" role="tab" aria-controls="seo-pane" aria-selected="false" style="font-family: var(--font-title); font-size: 0.95rem;">
                        <i class="bi bi-search me-2"></i>SEO & Metadata
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link border-0 fw-semibold px-4 py-2.5 rounded-top-3" id="promo-tab" data-bs-toggle="tab" data-bs-target="#promo-pane" type="button" role="tab" aria-controls="promo-pane" aria-selected="false" style="font-family: var(--font-title); font-size: 0.95rem;">
                        <i class="bi bi-bag-check me-2"></i>Product Promotion
                    </button>
                </li>
            </ul>

            <!-- Tab Content Panes -->
            <div class="tab-content" id="blogFormTabContent">
                
                <!-- Write Article Pane -->
                <div class="tab-pane fade show active" id="content-pane" role="tabpanel" aria-labelledby="content-tab">
                    
                    <!-- Content Builder Helper Toolbar -->
                    <div class="admin-card mb-3 bg-light border-dashed">
                        <div class="card-body p-3">
                            <span class="fw-bold small text-secondary d-block mb-2">
                                <i class="bi bi-magic me-1 text-primary"></i> Quick Content Inserter (TOC Friendly)
                            </span>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary border-0 bg-white shadow-sm" onclick="insertTemplate('h2')">
                                    <i class="bi bi-type-h2 me-1"></i> Heading 2 (H2)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary border-0 bg-white shadow-sm" onclick="insertTemplate('h3')">
                                    <i class="bi bi-type-h3 me-1"></i> Heading 3 (H3)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary border-0 bg-white shadow-sm" onclick="insertTemplate('h4')">
                                    <i class="bi bi-type-h4 me-1"></i> Heading 4 (H4)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success border-0 bg-white shadow-sm" onclick="insertTemplate('highlight')">
                                    <i class="bi bi-highlight me-1 text-warning"></i> Info Highlight Box
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger border-0 bg-white shadow-sm" onclick="insertTemplate('alert')">
                                    <i class="bi bi-exclamation-triangle me-1 text-danger"></i> Warning Alert Box
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-dark border-0 bg-white shadow-sm" onclick="insertTemplate('table')">
                                    <i class="bi bi-table me-1"></i> Comparison Table
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info border-0 bg-white shadow-sm" onclick="insertTemplate('list')">
                                    <i class="bi bi-list-check me-1"></i> Checklist
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary border-0 bg-white shadow-sm" onclick="insertTemplate('image_caption')">
                                    <i class="bi bi-image me-1"></i> Image with Caption
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary border-0 bg-white shadow-sm" onclick="insertTemplate('promo_card')">
                                    <i class="bi bi-cart-plus me-1 text-primary"></i> Product Callout Card
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="admin-card mb-4">
                        <div class="card-header"><h6>Article Body</h6></div>
                        <div class="card-body">
                            <div class="form-group mb-3">
                                <label class="fw-semibold mb-1">Post Title *</label>
                                <input type="text" name="title" id="postTitle" class="dc-form-control" value="<?= htmlspecialchars($post['title'] ?? '') ?>" placeholder="e.g. How to Build a Digital Product Marketplace in PHP" required style="border-radius: var(--radius-md);">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="fw-semibold mb-1">Short Excerpt / Summary *</label>
                                <textarea name="summary" class="dc-form-control" rows="3" placeholder="A catchy brief intro of this post (used in search results & social share cards)..." required style="border-radius: var(--radius-md); font-size: 0.9rem;"><?= htmlspecialchars($post['summary'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="fw-semibold mb-2">Content Editor *</label>
                                <textarea name="content" class="dc-form-control" rows="15" id="blogContent" placeholder="Write article content here..."><?= $post['content'] ?? '' ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Pane -->
                <div class="tab-pane fade" id="seo-pane" role="tabpanel" aria-labelledby="seo-tab">
                    <div class="admin-card mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h6>SEO Meta Fields & Google Preview</h6>
                            <span class="badge bg-primary-soft text-primary px-2.5 py-1.5 rounded-pill" style="font-size:0.75rem;"><i class="bi bi-google me-1"></i>Search Optimized</span>
                        </div>
                        <div class="card-body">
                            <!-- Focus Keyword -->
                            <div class="form-group mb-3">
                                <label class="fw-semibold mb-1">Focus Keyword</label>
                                <input type="text" name="focus_keyword" id="seoFocusKeyword" class="dc-form-control" value="<?= htmlspecialchars($post['focus_keyword'] ?? '') ?>" placeholder="e.g. php digital marketplace" style="border-radius: var(--radius-md);">
                                <small class="text-muted">The main search query you want this blog post to rank for.</small>
                            </div>

                            <!-- Custom URL Slug -->
                            <div class="form-group mb-3">
                                <label class="fw-semibold mb-1">Custom URL Slug</label>
                                <input type="text" name="slug" id="seoSlug" class="dc-form-control" value="<?= htmlspecialchars($post['slug'] ?? '') ?>" placeholder="how-to-build-digital-marketplace" style="border-radius: var(--radius-md);">
                                <small class="text-muted">Unique friendly URL path. Leave blank to generate automatically from title.</small>
                            </div>

                            <!-- SEO Meta Title -->
                            <div class="form-group mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="fw-semibold mb-1">Meta Title</label>
                                    <span id="titleCounter" class="text-muted small">0 / 60</span>
                                </div>
                                <input type="text" name="meta_title" id="seoMetaTitle" class="dc-form-control" value="<?= htmlspecialchars($post['meta_title'] ?? '') ?>" placeholder="Catchy title for search results..." style="border-radius: var(--radius-md);">
                                <small class="text-muted">Recommended: 50-60 characters. Shows as blue link in Google.</small>
                            </div>
                            
                            <!-- SEO Meta Description -->
                            <div class="form-group mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="fw-semibold mb-1">Meta Description</label>
                                    <span id="descCounter" class="text-muted small">0 / 160</span>
                                </div>
                                <textarea name="meta_description" id="seoMetaDescription" class="dc-form-control" rows="3" placeholder="Enter snippet description that convinces searchers to click..." style="border-radius: var(--radius-md); font-size: 0.9rem;"><?= htmlspecialchars($post['meta_description'] ?? '') ?></textarea>
                                <small class="text-muted">Recommended: 150-160 characters. Shows in search snippets.</small>
                            </div>

                            <!-- SEO Schema Type -->
                            <div class="form-group mb-3">
                                <label class="fw-semibold mb-1">JSON-LD Schema Type</label>
                                <select name="schema_type" class="dc-form-control" style="border-radius: var(--radius-md);">
                                    <option value="Article" <?= ($post['schema_type'] ?? 'Article') === 'Article' ? 'selected' : '' ?>>Article (General)</option>
                                    <option value="TechArticle" <?= ($post['schema_type'] ?? '') === 'TechArticle' ? 'selected' : '' ?>>Tech Article (Code/Tutorial)</option>
                                    <option value="BlogPosting" <?= ($post['schema_type'] ?? '') === 'BlogPosting' ? 'selected' : '' ?>>Blog Posting (Informational)</option>
                                    <option value="NewsArticle" <?= ($post['schema_type'] ?? '') === 'NewsArticle' ? 'selected' : '' ?>>News Article</option>
                                </select>
                            </div>

                            <!-- Canonical URL & Robots -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="fw-semibold mb-1">Canonical URL</label>
                                        <input type="url" name="canonical_url" class="dc-form-control" value="<?= htmlspecialchars($post['canonical_url'] ?? '') ?>" placeholder="https://external-domain.com/original-article" style="border-radius: var(--radius-md);">
                                        <small class="text-muted">Leave empty to use current post URL as canonical.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="fw-semibold mb-1">Robots Settings</label>
                                        <select name="robots" class="dc-form-control" style="border-radius: var(--radius-md);">
                                            <option value="index,follow" <?= ($post['robots'] ?? 'index,follow') === 'index,follow' ? 'selected' : '' ?>>Index, Follow (Default)</option>
                                            <option value="noindex,follow" <?= ($post['robots'] ?? '') === 'noindex,follow' ? 'selected' : '' ?>>No-Index, Follow</option>
                                            <option value="noindex,nofollow" <?= ($post['robots'] ?? '') === 'noindex,nofollow' ? 'selected' : '' ?>>No-Index, No-Follow (Private)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Open Graph Social Preview Settings -->
                            <h6 class="mb-3 text-secondary"><i class="bi bi-share-fill me-2"></i>Social Media Preview (Facebook / X)</h6>
                            
                            <div class="form-group mb-3">
                                <label class="fw-semibold mb-1">Social Title (OG Title)</label>
                                <input type="text" name="og_title" class="dc-form-control" value="<?= htmlspecialchars($post['og_title'] ?? '') ?>" placeholder="Custom title when shared on socials..." style="border-radius: var(--radius-md);">
                            </div>
                            <div class="form-group mb-3">
                                <label class="fw-semibold mb-1">Social Description (OG Desc)</label>
                                <textarea name="og_description" class="dc-form-control" rows="2" placeholder="Custom description when shared on socials..." style="border-radius: var(--radius-md); font-size: 0.9rem;"><?= htmlspecialchars($post['og_description'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="fw-semibold mb-2">Social Sharing Image (OG Image)</label>
                                <?php if ($isEdit && !empty($post['og_image'])): ?>
                                <div class="mb-2">
                                    <img src="<?= uploadUrl($post['og_image']) ?>" style="max-height: 80px; border-radius: 6px;">
                                </div>
                                <?php endif; ?>
                                <input type="file" name="og_image" class="dc-form-control" accept="image/*" style="border-radius: var(--radius-md);">
                                <small class="text-muted">Best format: 1200x630px JPG/PNG.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Promotion Pane -->
                <div class="tab-pane fade" id="promo-pane" role="tabpanel" aria-labelledby="promo-tab">
                    <div class="admin-card mb-4">
                        <div class="card-header">
                            <h6>Marketplace Product Ad / Backlink Promotion</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                <i class="bi bi-lightbulb-fill text-warning me-1"></i>
                                Select a digital product from your marketplace. This product will be showcased in a stylish, highly conversions-optimized advertisement card at the bottom of the article, and can also be injected inline into the content to drive direct contextual backlink conversions.
                            </p>
                            
                            <div class="form-group mb-4">
                                <label class="fw-semibold mb-2">Select Product to Promote</label>
                                <select name="promote_product_id" id="promoteProductId" class="dc-form-control" style="border-radius: var(--radius-md);">
                                    <option value="">-- No Product Promoted (None) --</option>
                                    <?php foreach ($products as $prod): ?>
                                    <option value="<?= $prod['id'] ?>" <?= ($post['promote_product_id'] ?? '') == $prod['id'] ? 'selected' : '' ?>>
                                        [<?= htmlspecialchars(ucwords(str_replace('_', ' ', $prod['product_type']))) ?>] <?= htmlspecialchars($prod['title']) ?> (₹<?= number_format($prod['sale_price'], 2) ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="p-3 rounded-4 bg-light border">
                                <span class="fw-bold small text-secondary d-block mb-2">How it displays:</span>
                                <div class="text-muted small">
                                    1. A visually stunning, conversion-optimized callout banner will automatically render at the end of this blog post.<br>
                                    2. You can also click the <strong>"Product Callout Card"</strong> button in the editor helper to place a conversion widget anywhere in the body text.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            
            <!-- Publish Option -->
            <div class="admin-card mb-4">
                <div class="card-header"><h6>Publish Status</h6></div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label class="fw-semibold mb-1">Status</label>
                        <select name="status" class="dc-form-control" style="border-radius: var(--radius-md);">
                            <option value="draft" <?= ($post['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="fw-semibold mb-1">Scheduled Publish Date (Optional)</label>
                        <input type="datetime-local" name="scheduled_at" class="dc-form-control" value="<?= !empty($post['scheduled_at']) ? date('Y-m-d\TH:i', strtotime($post['scheduled_at'])) : '' ?>" style="border-radius: var(--radius-md); font-size:0.88rem;">
                        <small class="text-muted small d-block mt-1">Leave empty to publish instantly.</small>
                    </div>
                    
                    <button type="submit" class="btn-dc-primary w-100 mt-2 py-2.5 rounded-3 fw-bold" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); border: none; font-size: 0.92rem;">
                        <i class="bi bi-check-lg me-2"></i><?= $isEdit ? 'Update Post' : 'Save & Publish' ?>
                    </button>
                </div>
            </div>

            <!-- Blog Category selection -->
            <div class="admin-card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6>Blog Category</h6>
                    <a href="<?= url('admin/blog/categories') ?>" class="small text-decoration-none" target="_blank"><i class="bi bi-plus-circle me-1"></i>Manage</a>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="fw-semibold mb-2">Category Selection *</label>
                        <select name="blog_category_id" class="dc-form-control" style="border-radius: var(--radius-md);" required>
                            <option value="">-- Choose Blog Category --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($post['blog_category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tags Input -->
            <div class="admin-card mb-4">
                <div class="card-header"><h6>Keywords / Tags</h6></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="fw-semibold mb-1">Tags (Comma-separated)</label>
                        <input type="text" name="tags" class="dc-form-control" value="<?= htmlspecialchars($tagsString) ?>" placeholder="e.g. PHP, SEO, Tutorial, Speed" style="border-radius: var(--radius-md);">
                        <small class="text-muted">Users can filter blog articles by tags.</small>
                    </div>
                </div>
            </div>

            <!-- Media/Featured Image -->
            <div class="admin-card mb-4">
                <div class="card-header"><h6>Featured Image</h6></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="fw-semibold mb-2">Upload Header Image</label>
                        <?php if ($isEdit && $post['image']): ?>
                        <div class="mb-3 p-2 border rounded-3 bg-light text-center">
                            <img src="<?= uploadUrl($post['image']) ?>" style="max-height: 140px; border-radius: 8px; max-width: 100%; object-fit: contain;">
                            <small class="text-muted d-block mt-2">Current featured image</small>
                        </div>
                        <?php endif; ?>
                        
                        <input type="file" name="image" class="dc-form-control" accept="image/*" style="border-radius: var(--radius-md);">
                        <small class="text-muted d-block mt-2">Recommended: 1200x600 pixels (aspect ratio 2:1).</small>
                    </div>
                </div>
            </div>

            <!-- Display Settings -->
            <div class="admin-card mb-4">
                <div class="card-header"><h6>Display Settings</h6></div>
                <div class="card-body">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" <?= !empty($post['is_featured']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="isFeatured">Featured Post</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_trending" id="isTrending" <?= !empty($post['is_trending']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="isTrending">Trending Post</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_editors_pick" id="isEditorsPick" <?= !empty($post['is_editors_pick']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="isEditorsPick">Editor's Pick</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="allow_comments" id="allowComments" <?= ($post['allow_comments'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="allowComments">Allow User Comments</label>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</form>

<!-- TinyMCE CDN (WordPress Classic Editor style) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        tinymce.init({
            selector: '#blogContent',
            height: 520,
            plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview | insertfile image media link anchor codesample | code',
            toolbar_sticky: true,
            autosave_ask_before_unload: true,
            autosave_interval: '30s',
            image_advtab: true,
            // Upload handler integrating directly with '/blog-editor/upload-media'
            images_upload_handler: function (blobInfo, progress) {
                return new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.withCredentials = true;
                    xhr.open('POST', '<?= url('blog-editor/upload-media') ?>');

                    // Send CSRF token in header
                    xhr.setRequestHeader('X-CSRF-Token', '<?= csrfToken() ?>');

                    xhr.upload.onprogress = (e) => {
                        progress(e.loaded / e.total * 100);
                    };

                    xhr.onload = () => {
                        if (xhr.status === 403) {
                            reject({ message: 'HTTP Error: ' + xhr.status + ' (Unauthorized)', remove: true });
                            return;
                        }

                        if (xhr.status < 200 || xhr.status >= 300) {
                            reject('HTTP Error: ' + xhr.status);
                            return;
                        }

                        const json = JSON.parse(xhr.responseText);

                        if (!json || typeof json.media !== 'object' || typeof json.media.file_path !== 'string') {
                            reject('Invalid JSON: ' + xhr.responseText);
                            return;
                        }

                        // Resolve with the fully qualified public image URL
                        const relativePath = json.media.file_path;
                        const cleanPath = relativePath.startsWith('uploads/') ? relativePath.substring(8) : relativePath;
                        const finalUrl = '<?= UPLOADS_URL ?>/' + cleanPath.replace(/^\//, '');
                        
                        resolve(finalUrl);
                    };

                    xhr.onerror = () => {
                        reject('Image upload failed due to a network error.');
                    };

                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    formData.append('post_id', '<?= $isEdit ? $post['id'] : 0 ?>');
                    formData.append('csrf_token', '<?= csrfToken() ?>');

                    xhr.send(formData);
                });
            },
            content_style: 'body { font-family:Inter,Helvetica,Arial,sans-serif; font-size:16px; line-height:1.6; }'
        });

        // SEO Character counters
        const seoMetaTitle = document.getElementById('seoMetaTitle');
        const seoMetaDescription = document.getElementById('seoMetaDescription');
        const titleCounter = document.getElementById('titleCounter');
        const descCounter = document.getElementById('descCounter');

        function updateCounters() {
            if (seoMetaTitle) {
                const titleLen = seoMetaTitle.value.length;
                titleCounter.textContent = titleLen + " / 60";
                titleCounter.className = (titleLen >= 50 && titleLen <= 60) ? "text-success small fw-semibold" : "text-muted small";
            }
            if (seoMetaDescription) {
                const descLen = seoMetaDescription.value.length;
                descCounter.textContent = descLen + " / 160";
                descCounter.className = (descLen >= 140 && descLen <= 160) ? "text-success small fw-semibold" : "text-muted small";
            }
        }

        if (seoMetaTitle) seoMetaTitle.addEventListener('input', updateCounters);
        if (seoMetaDescription) seoMetaDescription.addEventListener('input', updateCounters);
        updateCounters();
    });

    // Function to insert customized HTML snippets into TinyMCE at the cursor position
    function insertTemplate(type) {
        let htmlString = '';
        switch(type) {
            case 'h2':
                htmlString = '<h2>Section Heading (H2)</h2><p>Start writing paragraph here...</p>';
                break;
            case 'h3':
                htmlString = '<h3>Subsection Heading (H3)</h3><p>Start writing subsection paragraph here...</p>';
                break;
            case 'h4':
                htmlString = '<h4>Sub-subsection Heading (H4)</h4>';
                break;
            case 'highlight':
                htmlString = `
                <div class="blog-highlight-box info-box" style="background:#eff6ff; border-left:4px solid #2563eb; padding:18px 24px; border-radius:8px; margin:24px 0; font-size:0.95rem; line-height: 1.6;">
                    <strong>ℹ️ Key Tip / Resource Info:</strong>
                    <p style="margin:8px 0 0 0; color:#374151;">Add key information, code snippets, or useful tips that provide quick value to the reader here.</p>
                </div>`;
                break;
            case 'alert':
                htmlString = `
                <div class="blog-highlight-box warning-box" style="background:#fffbeb; border-left:4px solid #d97706; padding:18px 24px; border-radius:8px; margin:24px 0; font-size:0.95rem; line-height: 1.6;">
                    <strong>⚠️ Cautionary Warning:</strong>
                    <p style="margin:8px 0 0 0; color:#374151;">Write important warnings or error prevention steps that readers should keep in mind.</p>
                </div>`;
                break;
            case 'table':
                htmlString = `
                <table style="width:100%; border-collapse:collapse; margin:20px 0;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="border:1px solid #e2e8f0; padding:12px; text-align:left;">Feature / Criteria</th>
                            <th style="border:1px solid #e2e8f0; padding:12px; text-align:left;">Standard Plan</th>
                            <th style="border:1px solid #e2e8f0; padding:12px; text-align:left;">Pro Enterprise</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border:1px solid #e2e8f0; padding:12px; font-weight:bold;">Custom Setup</td>
                            <td style="border:1px solid #e2e8f0; padding:12px;">No, Self Setup</td>
                            <td style="border:1px solid #e2e8f0; padding:12px; color:#16a34a;">Yes, Done for You</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #e2e8f0; padding:12px; font-weight:bold;">Support Access</td>
                            <td style="border:1px solid #e2e8f0; padding:12px;">Email support</td>
                            <td style="border:1px solid #e2e8f0; padding:12px;">Priority WhatsApp & Discord</td>
                        </tr>
                    </tbody>
                </table>`;
                break;
            case 'list':
                htmlString = `
                <ul style="list-style:none; padding-left:0; margin:16px 0;">
                    <li style="margin-bottom:10px;"><i class="bi bi-patch-check-fill text-success me-2"></i> <strong>Step 1:</strong> Download the codebase zip file</li>
                    <li style="margin-bottom:10px;"><i class="bi bi-patch-check-fill text-success me-2"></i> <strong>Step 2:</strong> Import the SQL schema file in phpMyAdmin</li>
                    <li style="margin-bottom:10px;"><i class="bi bi-patch-check-fill text-success me-2"></i> <strong>Step 3:</strong> Configure DB connection keys in config files</li>
                </ul>`;
                break;
            case 'image_caption':
                htmlString = `
                <figure class="image" style="margin:24px 0; text-align:center;">
                    <img src="https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1000&auto=format&fit=crop&q=80" alt="Sample Code Screenshot" style="max-width:100%; border-radius:8px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
                    <figcaption style="font-style:italic; font-size:0.85rem; color:#64748b; margin-top:8px;">Figure 1: Clean code layout displaying modular folder structure</figcaption>
                </figure>`;
                break;
            case 'promo_card':
                const selectedProdOpt = document.getElementById('promoteProductId');
                const prodName = selectedProdOpt && selectedProdOpt.selectedIndex > 0 ? selectedProdOpt.options[selectedProdOpt.selectedIndex].text : "Our Featured Script Package";
                
                htmlString = `
                <div class="blog-product-promo-box" style="background:linear-gradient(135deg, #eff6ff 0%, #f5f3ff 100%); border:1px solid #c7d2fe; border-radius:12px; padding:24px; margin:30px 0; display:flex; flex-direction:column; gap:12px; line-height: 1.6;">
                    <span style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.8px; font-weight:800; color:#4f46e5;"><i class="bi bi-star-fill"></i> Highly Recommended Script</span>
                    <h4 style="margin:0; font-weight:800; font-size:1.2rem; color:#1e1b4b;">Get the Premium Version: ${prodName}</h4>
                    <p style="margin:0; font-size:0.9rem; color:#475569;">Supercharge your digital marketplace with this fully tested script. Access premium features, free installation, and lifetime updates.</p>
                    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-top:6px;">
                        <span style="font-weight:800; font-size:1.25rem; color:#0f172a;">Instant Setup Enabled</span>
                        <a href="${window.appUrl('products')}" class="btn" style="background:#4f46e5; color:#ffffff; font-weight:700; border-radius:8px; padding:8px 16px; font-size:0.88rem; text-decoration:none;" target="_blank">Buy & Download Script <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>`;
                break;
        }

        // Insert into TinyMCE
        if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
            tinymce.activeEditor.insertContent(htmlString);
        }
    }
</script>

<style>
/* Scoped overrides for the tabs inside admin design */
.nav-tabs .nav-link {
    color: var(--text-secondary);
    background: transparent;
    border: 1px solid transparent;
}
.nav-tabs .nav-link:hover {
    color: var(--primary);
    background: rgba(37, 99, 235, 0.03);
}
.nav-tabs .nav-link.active {
    color: var(--primary) !important;
    background: var(--bg-surface) !important;
    border-bottom: 2px solid var(--primary) !important;
}
/* Ensure the editor fits nicely inside our container */
.tox-tinymce {
    border-radius: 12px !important;
    border-color: var(--border-color) !important;
    box-shadow: var(--shadow-sm);
}
.border-dashed {
    border: 1px dashed var(--border-color);
}
</style>
