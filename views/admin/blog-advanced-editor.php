<?php
/**
 * Advanced Block-Based Blog Editor View
 * WordPress Gutenberg + Rank Math SEO inspired
 */

$isEdit = !empty($post);
$post = $post ?? [];
$blocks = $blocks ?? [];
$categories = $categories ?? [];
$templates = $templates ?? [];
$allTags = $allTags ?? [];
?>

<style>
:root {
    --editor-max-width: 800px;
    --sidebar-width: 350px;
    --toolbar-height: 54px;
}

.blog-editor-wrapper {
    display: flex;
    gap: 20px;
    height: calc(100vh - 180px);
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
}

.editor-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.editor-canvas {
    flex: 1;
    overflow-y: auto;
    padding: 40px 20px;
    background: white;
    position: relative;
}

.editor-content {
    max-width: var(--editor-max-width);
    margin: 0 auto;
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.block {
    position: relative;
    margin-bottom: 20px;
    padding: 15px;
    border: 2px solid transparent;
    border-radius: 8px;
    transition: all 0.2s;
}

.block:hover {
    border-color: #e2e8f0;
    background: #f8fafc;
}

.block.selected {
    border-color: #3b82f6;
    background: #eff6ff;
}

.block-toolbar {
    display: none;
    position: absolute;
    top: -40px;
    left: 0;
    right: 0;
    background: white;
    border-radius: 6px;
    padding: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    z-index: 1000;
    gap: 4px;
    flex-wrap: wrap;
}

.block:hover > .block-toolbar,
.block.selected > .block-toolbar {
    display: flex;
}

.block-toolbar-btn {
    width: 28px;
    height: 28px;
    padding: 0;
    border: 1px solid #e2e8f0;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all 0.2s;
}

.block-toolbar-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}

.right-sidebar {
    width: var(--sidebar-width);
    background: white;
    border-left: 1px solid #e2e8f0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.sidebar-tabs {
    display: flex;
    gap: 0;
    border-bottom: 1px solid #e2e8f0;
    padding: 0;
    margin: 0;
}

.sidebar-tab {
    flex: 1;
    padding: 12px;
    text-align: center;
    cursor: pointer;
    border: none;
    background: none;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    border-bottom: 3px solid transparent;
    transition: all 0.2s;
}

.sidebar-tab.active {
    color: #3b82f6;
    border-bottom-color: #3b82f6;
}

.sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    display: none;
}

.sidebar-content.active {
    display: block;
}

.seo-indicator {
    padding: 12px;
    margin-bottom: 12px;
    background: #f0f9ff;
    border-left: 4px solid #0ea5e9;
    border-radius: 4px;
}

.seo-score {
    font-weight: 700;
    font-size: 24px;
    color: #0ea5e9;
    margin-bottom: 5px;
}

.quick-blocks-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 12px;
}

.quick-block-btn {
    padding: 8px;
    text-align: center;
    font-size: 11px;
    border: 1px solid #e2e8f0;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.quick-block-btn:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}

.floating-toolbar {
    position: fixed;
    background: white;
    border-radius: 8px;
    padding: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 2000;
    display: none;
    gap: 2px;
    flex-wrap: wrap;
    max-width: 400px;
}

.floating-toolbar.active {
    display: flex;
}

.floating-toolbar-btn {
    width: 28px;
    height: 28px;
    padding: 0;
    border: 1px solid #e2e8f0;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all 0.2s;
}

.floating-toolbar-btn:hover {
    background: #f1f5f9;
    border-color: #3b82f6;
    color: #3b82f6;
}

.floating-toolbar-btn.active {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.form-group-compact {
    margin-bottom: 12px;
}

.form-group-compact label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 4px;
    color: #334155;
}

.form-group-compact input,
.form-group-compact textarea,
.form-group-compact select {
    width: 100%;
    padding: 6px 8px;
    font-size: 12px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-family: inherit;
}

.tabs-horizontal {
    display: flex;
    gap: 12px;
    margin-bottom: 15px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 12px;
}

.tab-link {
    padding: 8px 12px;
    font-size: 12px;
    border: none;
    background: none;
    cursor: pointer;
    color: #64748b;
    font-weight: 600;
    transition: all 0.2s;
    border-bottom: 2px solid transparent;
    margin-bottom: -12px;
}

.tab-link.active {
    color: #3b82f6;
    border-bottom-color: #3b82f6;
}

@media (max-width: 1400px) {
    .blog-editor-wrapper {
        flex-direction: column;
    }
    
    .right-sidebar {
        width: 100%;
        max-height: 300px;
    }
}
</style>

<div class="blog-editor-wrapper">
    <!-- MAIN EDITOR -->
    <div class="editor-main">
        <!-- Top Control Bar -->
        <div class="admin-card" style="margin: 12px; padding: 12px 15px; border-radius: 8px;">
            <div class="d-flex justify-content-between align-items-center" style="gap: 12px;">
                <div>
                    <a href="<?= url('admin/blog') ?>" class="text-muted" style="font-size: 0.85rem; text-decoration: none;">
                        <i class="bi bi-arrow-left me-1"></i>Back
                    </a>
                </div>
                
                <div class="d-flex gap-2 align-items-center">
                    <!-- Autosave Status -->
                    <span id="autosaveStatus" class="text-muted" style="font-size: 0.8rem;">
                        <i class="bi bi-hourglass-split"></i> Autosaving...
                    </span>
                </div>

                <div class="d-flex gap-2">
                    <!-- Template Selector -->
                    <select id="templateSelector" class="form-control form-control-sm" style="max-width: 150px; font-size: 0.85rem;">
                        <option value="">Select Template...</option>
                        <?php foreach ($templates as $template): ?>
                            <option value="<?= $template['id'] ?>"><?= htmlspecialchars($template['name']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Publish Status -->
                    <select id="postStatus" class="form-control form-control-sm" style="max-width: 120px; font-size: 0.85rem;">
                        <option value="draft" <?= ($post['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= ($post['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="scheduled" <?= ($post['status'] ?? '') === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                    </select>

                    <!-- Save Button -->
                    <button onclick="savePost()" class="btn btn-primary btn-sm" style="font-size: 0.85rem;">
                        <i class="bi bi-check2"></i> Save
                    </button>
                </div>
            </div>
        </div>

        <!-- Editor Canvas -->
        <div class="editor-canvas">
            <div class="editor-content">
                <form id="editorForm">
                    <!-- Title Input -->
                    <input type="hidden" id="postId" value="<?= $post['id'] ?? '' ?>">
                    
                    <div style="margin-bottom: 20px;">
                        <input type="text" id="postTitle" class="form-control form-control-lg" 
                               placeholder="Enter article title..." style="font-size: 2rem; font-weight: 700; border: none; padding: 0 0 10px 0;"
                               value="<?= htmlspecialchars($post['title'] ?? '') ?>" required>
                    </div>

                    <!-- Summary -->
                    <div style="margin-bottom: 30px;">
                        <textarea id="postSummary" class="form-control" rows="2"
                                  placeholder="Brief summary for search results..."
                                  style="border: 1px solid #e2e8f0; font-size: 0.95rem;">
<?= htmlspecialchars($post['summary'] ?? '') ?></textarea>
                    </div>

                    <!-- Blocks Container -->
                    <div id="blocksContainer">
                        <?php foreach ($blocks as $block): ?>
                            <div class="block" data-block-id="<?= $block['id'] ?>" data-block-type="<?= htmlspecialchars($block['block_type']) ?>">
                                <div class="block-toolbar">
                                    <button type="button" class="block-toolbar-btn" title="Move Up" onclick="moveBlockUp(this)">
                                        <i class="bi bi-arrow-up"></i>
                                    </button>
                                    <button type="button" class="block-toolbar-btn" title="Move Down" onclick="moveBlockDown(this)">
                                        <i class="bi bi-arrow-down"></i>
                                    </button>
                                    <button type="button" class="block-toolbar-btn" title="Duplicate" onclick="duplicateBlock(this)">
                                        <i class="bi bi-files"></i>
                                    </button>
                                    <button type="button" class="block-toolbar-btn" title="Delete" onclick="deleteBlock(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <div class="block-content">
                                    <?php
                                    $blockType = $block['block_type'];
                                    $blockData = $block['block_data'];
                                    
                                    if ($blockType === 'paragraph'):
                                    ?>
                                        <textarea class="form-control block-editor" placeholder="Start typing..."><?= htmlspecialchars($blockData['content'] ?? '') ?></textarea>
                                    <?php elseif (in_array($blockType, ['heading', 'heading1', 'heading2', 'heading3', 'heading4', 'heading5', 'heading6'])): ?>
                                        <input type="text" class="form-control block-editor" placeholder="Heading text..." value="<?= htmlspecialchars($blockData['content'] ?? '') ?>">
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Add Block Button -->
                    <div style="text-align: center; margin-top: 30px; padding: 20px; border: 2px dashed #e2e8f0; border-radius: 8px;">
                        <button type="button" onclick="showBlockMenu()" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Add Block
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="right-sidebar">
        <!-- Sidebar Tabs -->
        <div class="sidebar-tabs">
            <button class="sidebar-tab active" onclick="switchSidebarTab('seo')">
                <i class="bi bi-search"></i> SEO
            </button>
            <button class="sidebar-tab" onclick="switchSidebarTab('publishing')">
                <i class="bi bi-calendar"></i> Publish
            </button>
            <button class="sidebar-tab" onclick="switchSidebarTab('ai')">
                <i class="bi bi-stars"></i> AI
            </button>
            <button class="sidebar-tab" onclick="switchSidebarTab('revisions')">
                <i class="bi bi-clock-history"></i> History
            </button>
        </div>

        <!-- SEO Tab -->
        <div id="seoTab" class="sidebar-content active">
            <div class="seo-indicator">
                <div class="seo-score" id="seoScore">--</div>
                <small>SEO Score</small>
            </div>

            <div class="form-group-compact">
                <label>Focus Keyword</label>
                <input type="text" id="focusKeyword" class="form-control" placeholder="Primary keyword...">
            </div>

            <div class="form-group-compact">
                <label>Meta Title (60 chars max)</label>
                <input type="text" id="metaTitle" class="form-control" placeholder="SEO title..." maxlength="60">
                <small class="text-muted d-block mt-1"><span id="metaTitleCount">0</span>/60</small>
            </div>

            <div class="form-group-compact">
                <label>Meta Description (160 chars max)</label>
                <textarea id="metaDescription" class="form-control" rows="3" placeholder="Meta description..." maxlength="160"></textarea>
                <small class="text-muted d-block mt-1"><span id="metaDescCount">0</span>/160</small>
            </div>

            <div class="form-group-compact">
                <label>URL Slug</label>
                <input type="text" id="postSlug" class="form-control" placeholder="post-url-slug">
            </div>

            <button onclick="analyzeSEO()" class="btn btn-primary btn-sm w-100" style="margin-top: 12px;">
                <i class="bi bi-lightning"></i> Analyze SEO
            </button>

            <div id="seoIssues" style="margin-top: 15px; font-size: 0.85rem;"></div>
        </div>

        <!-- Publishing Tab -->
        <div id="publishingTab" class="sidebar-content">
            <div class="form-group-compact">
                <label>Featured Image</label>
                <div id="featuredImagePreview" style="width: 100%; height: 120px; background: #f1f5f9; border-radius: 6px; margin-bottom: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                    <small>Click to upload</small>
                </div>
            </div>

            <div class="form-group-compact">
                <label>Category</label>
                <select id="postCategory" class="form-control">
                    <option value="">Select category...</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($post['blog_category_id'] ?? '') === $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group-compact">
                <label>Tags</label>
                <input type="text" id="postTags" class="form-control" placeholder="tag1, tag2, tag3...">
            </div>

            <div class="form-group-compact">
                <label>Publish Date</label>
                <input type="datetime-local" id="publishDate" class="form-control">
            </div>

            <div class="form-group-compact">
                <div class="form-check">
                    <input type="checkbox" id="isFeatured" class="form-check-input">
                    <label class="form-check-label" for="isFeatured">Featured Post</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" id="isSticky" class="form-check-input">
                    <label class="form-check-label" for="isSticky">Sticky Post</label>
                </div>
            </div>
        </div>

        <!-- AI Tab -->
        <div id="aiTab" class="sidebar-content">
            <div class="tabs-horizontal">
                <button class="tab-link active" onclick="switchAIAction('generate')">Generate</button>
                <button class="tab-link" onclick="switchAIAction('improve')">Improve</button>
                <button class="tab-link" onclick="switchAIAction('translate')">Translate</button>
            </div>

            <div id="aiGeneratePanel" class="ai-panel">
                <div class="form-group-compact">
                    <label>Article Type</label>
                    <select id="aiArticleType" class="form-control">
                        <option value="blog">Blog Post</option>
                        <option value="tutorial">Tutorial</option>
                        <option value="guide">Guide</option>
                        <option value="review">Review</option>
                        <option value="listicle">Listicle</option>
                    </select>
                </div>

                <div class="form-group-compact">
                    <label>Topic/Keywords</label>
                    <input type="text" id="aiTopic" class="form-control" placeholder="What to write about...">
                </div>

                <button onclick="aiGenerate()" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-sparkles"></i> Generate Content
                </button>
            </div>

            <div id="aiImprovePanel" class="ai-panel" style="display: none;">
                <div class="form-group-compact">
                    <label>Improvement Type</label>
                    <select id="aiImprovementType" class="form-control">
                        <option value="readability">Improve Readability</option>
                        <option value="seo">Optimize for SEO</option>
                        <option value="tone">Adjust Tone</option>
                        <option value="length">Expand Content</option>
                    </select>
                </div>

                <button onclick="aiImprove()" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-brush"></i> Improve
                </button>
            </div>
        </div>

        <!-- Revisions Tab -->
        <div id="revisionsTab" class="sidebar-content">
            <div id="revisionsList" style="font-size: 0.85rem;"></div>
        </div>
    </div>
</div>

<!-- Block Menu Modal -->
<div class="modal fade" id="blockMenuModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Block</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="quick-blocks-grid">
                    <button onclick="addBlock('paragraph')" class="quick-block-btn"><i class="bi bi-file-text"></i> Paragraph</button>
                    <button onclick="addBlock('heading1')" class="quick-block-btn"><i class="bi bi-type-h1"></i> Heading 1</button>
                    <button onclick="addBlock('heading2')" class="quick-block-btn"><i class="bi bi-type-h2"></i> Heading 2</button>
                    <button onclick="addBlock('heading3')" class="quick-block-btn"><i class="bi bi-type-h3"></i> Heading 3</button>
                    <button onclick="addBlock('image')" class="quick-block-btn"><i class="bi bi-image"></i> Image</button>
                    <button onclick="addBlock('gallery')" class="quick-block-btn"><i class="bi bi-images"></i> Gallery</button>
                    <button onclick="addBlock('video')" class="quick-block-btn"><i class="bi bi-play-circle"></i> Video</button>
                    <button onclick="addBlock('youtube')" class="quick-block-btn"><i class="bi bi-youtube"></i> YouTube</button>
                    <button onclick="addBlock('quote')" class="quick-block-btn"><i class="bi bi-quote"></i> Quote</button>
                    <button onclick="addBlock('code')" class="quick-block-btn"><i class="bi bi-code"></i> Code</button>
                    <button onclick="addBlock('table')" class="quick-block-btn"><i class="bi bi-table"></i> Table</button>
                    <button onclick="addBlock('button')" class="quick-block-btn"><i class="bi bi-cursor-fill"></i> Button</button>
                    <button onclick="addBlock('alert')" class="quick-block-btn"><i class="bi bi-info-circle"></i> Alert</button>
                    <button onclick="addBlock('faq')" class="quick-block-btn"><i class="bi bi-question-circle"></i> FAQ</button>
                    <button onclick="addBlock('download')" class="quick-block-btn"><i class="bi bi-download"></i> Download</button>
                    <button onclick="addBlock('product')" class="quick-block-btn"><i class="bi bi-box-seam"></i> Product</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initializeEditor();
    startAutosave();
    loadRevisions();
});

function initializeEditor() {
    // Update character counts
    document.getElementById('metaTitle').addEventListener('input', e => {
        document.getElementById('metaTitleCount').textContent = e.target.value.length;
    });
    document.getElementById('metaDescription').addEventListener('input', e => {
        document.getElementById('metaDescCount').textContent = e.target.value.length;
    });

    // Block selection
    document.querySelectorAll('.block').forEach(block => {
        block.addEventListener('click', () => {
            document.querySelectorAll('.block').forEach(b => b.classList.remove('selected'));
            block.classList.add('selected');
        });
    });
}

function addBlock(type) {
    const container = document.getElementById('blocksContainer');
    const index = container.children.length;
    const blockId = 'block_' + Date.now();

    const blockHTML = `
        <div class="block" data-block-id="${blockId}" data-block-type="${type}">
            <div class="block-toolbar">
                <button type="button" class="block-toolbar-btn" title="Move Up" onclick="moveBlockUp(this)">
                    <i class="bi bi-arrow-up"></i>
                </button>
                <button type="button" class="block-toolbar-btn" title="Move Down" onclick="moveBlockDown(this)">
                    <i class="bi bi-arrow-down"></i>
                </button>
                <button type="button" class="block-toolbar-btn" title="Duplicate" onclick="duplicateBlock(this)">
                    <i class="bi bi-files"></i>
                </button>
                <button type="button" class="block-toolbar-btn" title="Delete" onclick="deleteBlock(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="block-content">
                <textarea class="form-control block-editor" placeholder="Enter text..."></textarea>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', blockHTML);
    bootstrap.Modal.getInstance(document.getElementById('blockMenuModal')).hide();
}

function showBlockMenu() {
    new bootstrap.Modal(document.getElementById('blockMenuModal')).show();
}

function moveBlockUp(btn) {
    const block = btn.closest('.block');
    block.previousElementSibling?.insertAdjacentElement('beforebegin', block);
}

function moveBlockDown(btn) {
    const block = btn.closest('.block');
    block.nextElementSibling?.insertAdjacentElement('afterend', block);
}

function duplicateBlock(btn) {
    const block = btn.closest('.block');
    const clone = block.cloneNode(true);
    clone.id = 'block_' + Date.now();
    block.insertAdjacentElement('afterend', clone);
}

function deleteBlock(btn) {
    if (confirm('Delete this block?')) {
        btn.closest('.block').remove();
    }
}

function switchSidebarTab(tab) {
    document.querySelectorAll('.sidebar-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.sidebar-content').forEach(c => c.classList.remove('active'));
    
    event.target.closest('.sidebar-tab').classList.add('active');
    document.getElementById(tab + 'Tab').classList.add('active');
}

function switchAIAction(action) {
    document.querySelectorAll('.tab-link').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.ai-panel').forEach(p => p.style.display = 'none');
    
    event.target.classList.add('active');
    document.getElementById('ai' + capitalizeFirst(action) + 'Panel').style.display = 'block';
}

function analyzeSEO() {
    const postId = document.getElementById('postId').value;
    fetch(`<?= url('blog-editor/seo') ?>?post_id=${postId}`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('seoScore').textContent = data.seo_score;
            renderSEOIssues(data.issues);
        });
}

function renderSEOIssues(issues) {
    const html = issues.map(issue => `
        <div class="alert alert-${issue.type === 'critical' ? 'danger' : 'warning'} py-2 px-3 mb-2" role="alert">
            ${issue.message}
        </div>
    `).join('');
    document.getElementById('seoIssues').innerHTML = html;
}

function startAutosave() {
    setInterval(() => {
        savePost(true);
    }, parseInt('<?= getSetting("blog_autosave_interval", 30) ?>') * 1000);
}

function savePost(isAutosave = false) {
    const postId = document.getElementById('postId').value;
    const blocks = collectBlocks();
    const formData = {
        post_id: postId,
        title: document.getElementById('postTitle').value,
        summary: document.getElementById('postSummary').value,
        blocks: blocks,
        meta: {
            focus_keyword: document.getElementById('focusKeyword').value,
            meta_title: document.getElementById('metaTitle').value,
            meta_description: document.getElementById('metaDescription').value,
        },
        status: document.getElementById('postStatus').value,
    };

    fetch(`<?= url('blog-editor/save') ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?= csrf() ?>',
        },
        body: JSON.stringify(formData),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (!postId) {
                document.getElementById('postId').value = data.post_id;
            }
            const status = isAutosave ? 'Autosaved' : 'Saved';
            document.getElementById('autosaveStatus').textContent = `✓ ${status}`;
            setTimeout(() => {
                document.getElementById('autosaveStatus').textContent = 'Auto-save enabled';
            }, 3000);
        }
    });
}

function collectBlocks() {
    return Array.from(document.querySelectorAll('.block')).map((block, i) => ({
        index: i,
        type: block.dataset.blockType,
        data: {
            content: block.querySelector('.block-editor')?.value || '',
        },
    }));
}

function loadRevisions() {
    const postId = document.getElementById('postId').value;
    if (!postId) return;

    fetch(`<?= url('blog-editor/revisions') ?>?post_id=${postId}`)
        .then(r => r.json())
        .then(data => {
            const html = data.revisions.map(rev => `
                <div class="alert alert-light py-2 px-3 mb-2 border-start border-3 cursor-pointer" onclick="restoreRevision(${rev.id})">
                    <small class="d-block text-muted">${rev.created_at}</small>
                    <small class="fw-bold">${rev.title.substring(0, 40)}...</small>
                </div>
            `).join('');
            document.getElementById('revisionsList').innerHTML = html || '<small class="text-muted">No revisions yet</small>';
        });
}

function restoreRevision(revisionId) {
    if (confirm('Restore this revision?')) {
        fetch(`<?= url('blog-editor/restore') ?>`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': '<?= csrf() ?>',
            },
            body: `revision_id=${revisionId}`,
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        });
    }
}

function aiGenerate() {
    const topic = document.getElementById('aiTopic').value;
    const type = document.getElementById('aiArticleType').value;

    if (!topic) {
        alert('Please enter a topic');
        return;
    }

    // Call AI API
    alert('AI content generation coming soon! This will integrate with OpenAI, Claude, or similar service.');
}

function aiImprove() {
    alert('Content improvement coming soon!');
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
</script>
