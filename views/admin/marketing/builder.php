<!-- Advanced Visual Email Builder v2.0 -->
<style>
/* ===== BUILDER LAYOUT ===== */
.eb-root {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 90vh;
    margin: -20px;
    background: #f1f5f9;
    font-family: 'Inter', system-ui, sans-serif;
}

/* ===== TOP BAR ===== */
.eb-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #7c3aed 0%, #6366f1 100%);
    border-bottom: 1px solid #e2e8f0;
    padding: 10px 20px;
    z-index: 100;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(124, 58, 237, 0.25);
}
.eb-topbar-left { display: flex; align-items: center; gap: 14px; }
.eb-logo { font-weight: 800; color: #ffffff; font-size: 0.95rem; letter-spacing: 0.5px; }
.eb-logo span { color: #fbbf24; }
.eb-title { color: rgba(255,255,255,0.85); font-size: 0.8rem; max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.eb-topbar-right { display: flex; align-items: center; gap: 8px; }
.eb-btn { display: inline-flex; align-items: center; gap: 5px; padding: 7px 14px; border-radius: 7px; font-size: 0.8rem; font-weight: 600; cursor: pointer; border: none; transition: all 0.15s; }
.eb-btn-ghost { background: rgba(255,255,255,0.15); color: #ffffff; border: 1px solid rgba(255,255,255,0.25); }
.eb-btn-ghost:hover { background: rgba(255,255,255,0.25); color: #ffffff; border-color: rgba(255,255,255,0.4); }
.eb-btn-primary { background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; box-shadow: 0 2px 8px rgba(99,102,241,0.3); }
.eb-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(99,102,241,0.4); }
.eb-btn-success { background: linear-gradient(135deg, #10b981, #059669); color: #fff; box-shadow: 0 2px 8px rgba(16,185,129,0.3); }
.eb-btn-success:hover { transform: translateY(-1px); }
.eb-btn-sm { padding: 5px 10px; font-size: 0.75rem; }
.eb-viewport-toggle { display: flex; background: rgba(255,255,255,0.15); border-radius: 8px; padding: 3px; gap: 2px; border: 1px solid rgba(255,255,255,0.2); }
.eb-vt-btn { background: transparent; border: none; color: rgba(255,255,255,0.85); padding: 5px 10px; border-radius: 6px; cursor: pointer; font-size: 0.78rem; font-weight: 600; transition: all 0.15s; display: flex; align-items: center; gap: 4px; }
.eb-vt-btn.active { background: #ffffff; color: #6366f1; }
.eb-undo-redo { display: flex; gap: 4px; }
.eb-undo-redo button { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); color: #ffffff; width: 30px; height: 30px; border-radius: 6px; cursor: pointer; transition: all 0.15s; font-size: 0.85rem; }
.eb-undo-redo button:hover:not(:disabled) { background: rgba(255,255,255,0.25); border-color: rgba(255,255,255,0.4); }
.eb-undo-redo button:disabled { opacity: 0.3; cursor: not-allowed; }

/* ===== WORKSPACE ===== */
.eb-workspace {
    display: flex;
    flex: 1;
    overflow: hidden;
}

/* ===== LEFT SIDEBAR ===== */
.eb-left {
    width: 250px;
    background: #ffffff;
    border-right: 1px solid #e2e8f0;
    overflow-y: auto;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
}
.eb-left::-webkit-scrollbar { width: 4px; }
.eb-left::-webkit-scrollbar-track { background: #f8fafc; }
.eb-left::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }

.eb-sidebar-section { padding: 16px; border-bottom: 1px solid #e2e8f0; }
.eb-sidebar-title { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8; margin-bottom: 12px; }

/* Element Cards */
.eb-elements-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
.eb-element-card {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 8px;
    padding: 10px 6px;
    text-align: center;
    cursor: grab;
    transition: all 0.15s;
}
.eb-element-card:hover { border-color: #6366f1; background: #eef2ff; transform: translateY(-1px); }
.eb-element-card i { font-size: 1.3rem; color: #6366f1; display: block; margin-bottom: 4px; }
.eb-element-card span { font-size: 0.68rem; font-weight: 600; color: #64748b; display: block; }
.eb-element-full { grid-column: 1 / -1; }

/* Library Items */
.eb-lib-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 7px 8px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.12s;
    border-bottom: 1px solid #f1f5f9;
}
.eb-lib-item:hover { background: #f1f5f9; }
.eb-lib-item img { width: 30px; height: 30px; border-radius: 4px; object-fit: cover; flex-shrink: 0; }
.eb-lib-item-text { font-size: 0.72rem; font-weight: 600; color: #334155; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; flex: 1; }
.eb-lib-item-sub { font-size: 0.65rem; color: #64748b; }
.eb-lib-scroll { max-height: 200px; overflow-y: auto; }
.eb-lib-scroll::-webkit-scrollbar { width: 3px; }
.eb-lib-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }

/* Accordion */
.eb-acc-header { 
    display: flex; align-items: center; gap: 8px; 
    padding: 9px 0; cursor: pointer; 
    color: #475569; font-size: 0.78rem; font-weight: 600;
    border: none; background: none; width: 100%; text-align: left;
    transition: color 0.12s;
}
.eb-acc-header:hover { color: #1e293b; }
.eb-acc-header i { color: #6366f1; }
.eb-acc-header .eb-acc-arrow { margin-left: auto; font-size: 0.7rem; transition: transform 0.2s; }
.eb-acc-header.open .eb-acc-arrow { transform: rotate(180deg); }
.eb-acc-body { display: none; }
.eb-acc-body.open { display: block; padding-top: 4px; }

/* Global Settings */
.eb-global-setting { margin-bottom: 12px; }
.eb-global-setting label { font-size: 0.7rem; color: #64748b; display: block; margin-bottom: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.eb-global-setting input, .eb-global-setting select {
    width: 100%; background: #f8fafc; border: 1px solid #e2e8f0;
    color: #334155; border-radius: 6px; padding: 6px 8px; font-size: 0.78rem;
    outline: none; transition: border-color 0.15s;
}
.eb-global-setting input:focus, .eb-global-setting select:focus { border-color: #6366f1; }
.eb-color-row { display: flex; gap: 6px; align-items: center; }
.eb-color-row input[type="color"] { width: 36px; height: 30px; border-radius: 6px; cursor: pointer; padding: 2px; flex-shrink: 0; }
.eb-color-row input[type="text"] { flex: 1; }

/* ===== CENTER CANVAS ===== */
.eb-center {
    flex: 1;
    background: #0f172a;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 24px;
    gap: 16px;
}
.eb-center::-webkit-scrollbar { width: 6px; }
.eb-center::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

#emailViewport {
    transition: width 0.25s cubic-bezier(0.4,0,0.2,1);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(100,116,139,0.18);
    background: #ffffff;
}

#dropCanvas {
    min-height: 500px;
    position: relative;
}

.eb-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 400px;
    color: #94a3b8;
    text-align: center;
    padding: 40px;
    background: #ffffff;
    border-radius: 12px;
    border: 2px dashed #e2e8f0;
}
.eb-empty-state i { font-size: 3rem; margin-bottom: 16px; color: #cbd5e1; }
.eb-empty-state p { font-size: 0.9rem; }
.eb-empty-state .eb-add-hint { 
    margin-top: 12px; 
    padding: 10px 20px; 
    background: #f8fafc; 
    border-radius: 8px; 
    font-size: 0.8rem; 
    color: #64748b;
    border: 1px solid #e2e8f0;
}

/* Canvas Blocks */
.canvas-block {
    position: relative;
    border: 2px solid transparent;
    border-radius: 4px;
    transition: border-color 0.12s, background 0.12s;
    cursor: pointer;
    margin: 4px 0;
}
.canvas-block:hover { border-color: #6366f1; }
.canvas-block.selected { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }

.block-toolbar {
    position: absolute;
    top: -36px;
    right: 0;
    display: none;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 3px;
    gap: 2px;
    z-index: 20;
    box-shadow: 0 4px 16px rgba(100,116,139,0.18);
}
.canvas-block:hover .block-toolbar,
.canvas-block.selected .block-toolbar { display: flex; }
.block-toolbar button {
    background: transparent; border: none; color: #64748b;
    width: 26px; height: 26px; border-radius: 5px; cursor: pointer;
    font-size: 0.75rem; transition: all 0.12s; display: flex; align-items: center; justify-content: center;
}
.block-toolbar button:hover { background: #f1f5f9; color: #334155; }
.block-toolbar button.danger:hover { background: #fee2e2; color: #dc2626; }

.drag-over-indicator {
    height: 3px;
    background: #6366f1;
    border-radius: 2px;
    margin: -2px 0;
    display: none;
}

/* ===== RIGHT PANEL ===== */
.eb-right {
    width: 310px;
    background: #ffffff;
    border-left: 1px solid #e2e8f0;
    overflow-y: auto;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
}
.eb-right::-webkit-scrollbar { width: 4px; }
.eb-right::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }

.eb-panel-empty {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    color: #94a3b8; text-align: center; padding: 30px;
}
.eb-panel-empty i { font-size: 2.5rem; margin-bottom: 12px; color: #e2e8f0; }
.eb-panel-empty p { font-size: 0.8rem; line-height: 1.6; color: #94a3b8; }

/* Panel Tabs */
.eb-panel-tabs {
    display: flex; border-bottom: 1px solid #e2e8f0;
    background: #f8fafc; flex-shrink: 0;
}
.eb-tab {
    flex: 1; padding: 11px 6px; text-align: center;
    font-size: 0.72rem; font-weight: 700; color: #94a3b8;
    cursor: pointer; border: none; background: transparent;
    border-bottom: 2px solid transparent; transition: all 0.15s;
    text-transform: uppercase; letter-spacing: 0.5px;
}
.eb-tab.active { color: #6366f1; border-bottom-color: #6366f1; }
.eb-tab:hover:not(.active) { color: #475569; }

.eb-tab-panel { display: none; padding: 16px; flex: 1; }
.eb-tab-panel.active { display: block; }

/* Form Controls */
.eb-label { font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.6px; display: block; margin-bottom: 5px; }
.eb-input {
    width: 100%; background: #f8fafc; border: 1px solid #e2e8f0;
    color: #334155; border-radius: 7px; padding: 8px 10px; font-size: 0.8rem;
    outline: none; transition: border-color 0.15s; resize: vertical;
    font-family: inherit; box-sizing: border-box;
}
.eb-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.08); }
.eb-form-group { margin-bottom: 14px; }

/* Rich Text Toolbar */
.eb-rich-toolbar {
    display: flex; flex-wrap: wrap; gap: 3px;
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 7px 7px 0 0; padding: 5px 6px;
    border-bottom: none;
}
.eb-rich-btn {
    background: transparent; border: 1px solid #e8edf5;
    color: #64748b; width: 26px; height: 26px; border-radius: 4px;
    cursor: pointer; font-size: 0.75rem; transition: all 0.12s;
    display: flex; align-items: center; justify-content: center;
}
.eb-rich-btn:hover { background: #e8edf5; color: #334155; border-color: #cbd5e1; }
.eb-rich-btn.active { background: #6366f1; color: #fff; border-color: #6366f1; }
.eb-rich-btn.separator { width: 1px; height: 22px; background: #e2e8f0; margin: 2px 2px; cursor: default; }
.eb-rich-textarea { border-radius: 0 0 7px 7px !important; }

/* Color Row */
.eb-color-input-row { display: flex; gap: 6px; align-items: center; }
.eb-color-input-row input[type="color"] { 
    width: 34px; height: 32px; border-radius: 6px; cursor: pointer; 
    padding: 2px; background: #f8fafc; border: 1px solid #e2e8f0; flex-shrink: 0;
}
.eb-color-input-row .eb-input { flex: 1; }

/* Alignment Buttons */
.eb-align-group { display: flex; gap: 4px; }
.eb-align-btn {
    flex: 1; padding: 6px; background: #f8fafc; border: 1px solid #e2e8f0;
    color: #64748b; border-radius: 6px; cursor: pointer; font-size: 0.8rem;
    transition: all 0.12s; text-align: center;
}
.eb-align-btn:hover { border-color: #6366f1; color: #6366f1; background: #eef2ff; }
.eb-align-btn.active { background: #6366f1; color: #fff; border-color: #6366f1; }

/* Slider */
.eb-slider-row { display: flex; align-items: center; gap: 8px; }
.eb-slider { flex: 1; -webkit-appearance: none; height: 4px; background: #e2e8f0; border-radius: 2px; outline: none; cursor: pointer; }
.eb-slider::-webkit-slider-thumb { -webkit-appearance: none; width: 14px; height: 14px; border-radius: 50%; background: #6366f1; cursor: pointer; }
.eb-slider-val { font-size: 0.72rem; color: #64748b; min-width: 35px; text-align: right; }

/* Tag Pills */
.eb-tag-pills { display: flex; flex-wrap: wrap; gap: 5px; }
.eb-tag-pill {
    padding: 4px 9px; font-size: 0.68rem; font-weight: 700;
    background: #f1f5f9; border: 1px solid #e2e8f0; color: #475569;
    border-radius: 20px; cursor: pointer; font-family: monospace;
    transition: all 0.12s;
}
.eb-tag-pill:hover { border-color: #6366f1; color: #6366f1; background: #eef2ff; }

/* Link Type Toggle */
.eb-link-type { display: flex; gap: 4px; margin-bottom: 10px; }
.eb-link-type-btn {
    flex: 1; padding: 6px 4px; font-size: 0.68rem; font-weight: 700;
    background: #f8fafc; border: 1px solid #e2e8f0; color: #64748b;
    border-radius: 6px; cursor: pointer; text-align: center; transition: all 0.12s;
    text-transform: uppercase; letter-spacing: 0.3px;
}
.eb-link-type-btn.active { background: #6366f1; border-color: #6366f1; color: #fff; }

/* Toggle Switch */
.eb-toggle-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.eb-toggle-label { font-size: 0.75rem; color: #475569; font-weight: 600; }
.eb-toggle { position: relative; width: 36px; height: 20px; }
.eb-toggle input { opacity: 0; width: 0; height: 0; }
.eb-toggle-slider {
    position: absolute; top: 0; left: 0; right: 0; bottom: 0;
    background: #cbd5e1; border-radius: 20px; cursor: pointer; transition: 0.2s;
}
.eb-toggle-slider:before {
    content: ''; position: absolute; width: 14px; height: 14px;
    left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: 0.2s;
}
.eb-toggle input:checked + .eb-toggle-slider { background: #6366f1; }
.eb-toggle input:checked + .eb-toggle-slider:before { transform: translateX(16px); }

/* Social Editor */
.eb-social-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
.eb-social-icon { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0; }
.eb-social-row .eb-input { font-size: 0.72rem; padding: 5px 8px; }

/* Section Divider */
.eb-section-divider { border: none; border-top: 1px solid #e2e8f0; margin: 14px 0; }
.eb-section-sub { font-size: 0.68rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; margin-top: 14px; }

/* Image upload */
.eb-img-upload-zone {
    border: 2px dashed #cbd5e1; border-radius: 8px; padding: 16px;
    text-align: center; cursor: pointer; transition: all 0.15s; margin-bottom: 10px;
}
.eb-img-upload-zone:hover { border-color: #6366f1; background: #eef2ff; }
.eb-img-upload-zone i { font-size: 1.5rem; color: #475569; display: block; margin-bottom: 5px; }
.eb-img-upload-zone span { font-size: 0.72rem; color: #64748b; }
.eb-img-preview { width: 100%; max-height: 100px; object-fit: cover; border-radius: 6px; margin-bottom: 8px; display: none; }

/* Status Toast */
.eb-toast {
    position: fixed; bottom: 24px; right: 24px;
    background: #ffffff; border: 1px solid #e2e8f0;
    color: #334155; padding: 12px 20px; border-radius: 10px;
    font-size: 0.82rem; font-weight: 600; z-index: 9999;
    transform: translateY(80px); opacity: 0; transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
    display: flex; align-items: center; gap: 8px;
    box-shadow: 0 10px 30px rgba(100,116,139,0.2);
}
.eb-toast.show { transform: translateY(0); opacity: 1; }
.eb-toast.success { border-color: #10b981; }
.eb-toast.error { border-color: #ef4444; }

/* Padding controls */
.eb-padding-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; }
.eb-padding-item label { font-size: 0.62rem; color: #64748b; display: block; margin-bottom: 2px; }

/* Block type badge */
.eb-block-type-badge {
    position: absolute; top: -26px; left: 0;
    background: #6366f1; color: #fff; font-size: 0.6rem; font-weight: 700;
    padding: 2px 8px; border-radius: 4px 4px 0 0; letter-spacing: 0.5px;
    text-transform: uppercase; display: none;
}
.canvas-block.selected .eb-block-type-badge { display: block; }
</style>

<!-- Toast Notification -->
<div class="eb-toast" id="ebToast">
    <i class="bi bi-check-circle-fill" id="ebToastIcon"></i>
    <span id="ebToastMsg">Template saved!</span>
</div>

<!-- Link Editor Modal -->
<div class="modal fade" id="linkModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="background:#1e293b; border:1px solid #334155; color:#e2e8f0;">
            <div class="modal-header" style="border-color:#334155; padding:12px 16px;">
                <h6 class="modal-title" style="font-size:0.85rem; font-weight:700;">
                    <i class="bi bi-link-45deg text-indigo-400 me-2" style="color:#6366f1;"></i>Link Settings
                </h6>
                <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:16px;">
                <div class="eb-link-type" id="modalLinkType">
                    <button class="eb-link-type-btn active" onclick="setModalLinkType('url', this)">URL</button>
                    <button class="eb-link-type-btn" onclick="setModalLinkType('email', this)">Email</button>
                    <button class="eb-link-type-btn" onclick="setModalLinkType('phone', this)">Phone</button>
                </div>
                <div class="eb-form-group">
                    <label class="eb-label" id="modalLinkLabel">Link URL</label>
                    <input type="text" id="modalLinkInput" class="eb-input" placeholder="https://example.com">
                </div>
                <div class="eb-toggle-row">
                    <span class="eb-toggle-label">Open in new tab</span>
                    <label class="eb-toggle">
                        <input type="checkbox" id="modalLinkNewTab" checked>
                        <span class="eb-toggle-slider"></span>
                    </label>
                </div>
                <div style="display:flex; gap:8px; margin-top:14px;">
                    <button class="eb-btn eb-btn-ghost" style="flex:1; justify-content:center;" data-bs-dismiss="modal">Cancel</button>
                    <button class="eb-btn eb-btn-primary" style="flex:1; justify-content:center;" onclick="applyModalLink()">
                        <i class="bi bi-check2"></i> Apply
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="background:#0f172a; border:1px solid #334155;">
            <div class="modal-header" style="border-color:#334155; padding:12px 20px; background:#1e293b;">
                <h6 class="modal-title text-white" style="font-size:0.9rem; font-weight:700;">
                    <i class="bi bi-eye me-2" style="color:#6366f1;"></i>Email Preview
                </h6>
                <div class="d-flex align-items-center gap-2 ms-auto me-3">
                    <button class="eb-vt-btn active" onclick="setPreviewViewport('desktop', this)"><i class="bi bi-laptop"></i> Desktop</button>
                    <button class="eb-vt-btn" onclick="setPreviewViewport('mobile', this)"><i class="bi bi-phone"></i> Mobile</button>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background:#1e293b; padding:24px; max-height:80vh; overflow-y:auto;">
                <div style="display:flex; justify-content:center;">
                    <iframe id="previewIframe" style="width:700px; border:none; border-radius:8px; transition:width 0.3s;" scrolling="no"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MAIN BUILDER -->
<div class="eb-root">
    
    <!-- TOP BAR -->
    <div class="eb-topbar">
        <div class="eb-topbar-left">
            <div class="eb-logo">Digi<span>WS</span> Builder</div>
            <div class="eb-title">
                <strong><?= htmlspecialchars($template['name']) ?></strong>
                &bull; <?= htmlspecialchars($template['subject']) ?>
            </div>
        </div>
        <div class="eb-topbar-right">
            <!-- Viewport -->
            <div class="eb-viewport-toggle">
                <button class="eb-vt-btn active" onclick="setViewport('desktop', this)" id="vpDesktop">
                    <i class="bi bi-laptop"></i> Desktop
                </button>
                <button class="eb-vt-btn" onclick="setViewport('mobile', this)" id="vpMobile">
                    <i class="bi bi-phone"></i> Mobile
                </button>
            </div>
            <!-- Undo / Redo -->
            <div class="eb-undo-redo">
                <button onclick="undoAction()" id="undoBtn" title="Undo (Ctrl+Z)" disabled>
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
                <button onclick="redoAction()" id="redoBtn" title="Redo (Ctrl+Y)" disabled>
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
            <button class="eb-btn eb-btn-ghost" onclick="openPreview()">
                <i class="bi bi-eye"></i> Preview
            </button>
            <a href="<?= url('admin/marketing/templates') ?>" class="eb-btn eb-btn-ghost">
                <i class="bi bi-x-lg"></i> Close
            </a>
            <button class="eb-btn eb-btn-success" onclick="saveTemplateDesign()">
                <i class="bi bi-save2-fill"></i> Save Template
            </button>
        </div>
    </div>

    <!-- WORKSPACE -->
    <div class="eb-workspace">

        <!-- LEFT SIDEBAR -->
        <div class="eb-left">

            <!-- Elements -->
            <div class="eb-sidebar-section">
                <div class="eb-sidebar-title">Basic Elements</div>
                <div class="eb-elements-grid">
                    <div class="eb-element-card" draggable="true" data-type="header" id="drag-header">
                        <i class="bi bi-layout-text-window-reversed"></i>
                        <span>Header</span>
                    </div>
                    <div class="eb-element-card" draggable="true" data-type="text" id="drag-text">
                        <i class="bi bi-justify-left"></i>
                        <span>Text Block</span>
                    </div>
                    <div class="eb-element-card" draggable="true" data-type="button" id="drag-button">
                        <i class="bi bi-menu-button-wide"></i>
                        <span>Button</span>
                    </div>
                    <div class="eb-element-card" draggable="true" data-type="image" id="drag-image">
                        <i class="bi bi-image"></i>
                        <span>Image</span>
                    </div>
                    <div class="eb-element-card" draggable="true" data-type="video" id="drag-video">
                        <i class="bi bi-play-btn"></i>
                        <span>Video</span>
                    </div>
                    <div class="eb-element-card" draggable="true" data-type="divider" id="drag-divider">
                        <i class="bi bi-hr"></i>
                        <span>Divider</span>
                    </div>
                    <div class="eb-element-card" draggable="true" data-type="social" id="drag-social">
                        <i class="bi bi-share-fill"></i>
                        <span>Social</span>
                    </div>
                    <div class="eb-element-card" draggable="true" data-type="spacer" id="drag-spacer">
                        <i class="bi bi-arrows-expand"></i>
                        <span>Spacer</span>
                    </div>
                    <div class="eb-element-card eb-element-full" draggable="true" data-type="footer" id="drag-footer">
                        <i class="bi bi-layout-sidebar-inset"></i>
                        <span>Footer Block</span>
                    </div>
                </div>
            </div>

            <!-- Global Settings -->
            <div class="eb-sidebar-section">
                <button class="eb-acc-header open" onclick="toggleAcc(this, 'accGlobal')">
                    <i class="bi bi-sliders"></i> Global Settings
                    <i class="bi bi-chevron-down eb-acc-arrow"></i>
                </button>
                <div class="eb-acc-body open" id="accGlobal">
                    <div class="eb-global-setting">
                        <label>Email Background</label>
                        <div class="eb-color-row">
                            <input type="color" id="gBgColor" value="#F8FAFC" oninput="updateGlobalSetting('bgColor', this.value); document.getElementById('gBgColorText').value = this.value;">
                            <input type="text" id="gBgColorText" class="dc-form-control" value="#F8FAFC" oninput="updateGlobalSetting('bgColor', this.value); document.getElementById('gBgColor').value = this.value;" style="background:#0f172a; border:1px solid #334155; color:#e2e8f0; border-radius:6px; padding:5px 8px; font-size:0.75rem; flex:1; outline:none;">
                        </div>
                    </div>
                    <div class="eb-global-setting">
                        <label>Card Background</label>
                        <div class="eb-color-row">
                            <input type="color" id="gCardBg" value="#ffffff" oninput="updateGlobalSetting('cardBg', this.value); document.getElementById('gCardBgText').value = this.value;">
                            <input type="text" id="gCardBgText" style="background:#0f172a; border:1px solid #334155; color:#e2e8f0; border-radius:6px; padding:5px 8px; font-size:0.75rem; flex:1; outline:none;" value="#ffffff" oninput="updateGlobalSetting('cardBg', this.value); document.getElementById('gCardBg').value = this.value;">
                        </div>
                    </div>
                    <div class="eb-global-setting">
                        <label>Font Family</label>
                        <select id="gFontFamily" onchange="updateGlobalSetting('fontFamily', this.value)">
                            <option value="system-ui, sans-serif">System Default</option>
                            <option value="'Georgia', serif">Georgia (Serif)</option>
                            <option value="'Arial', sans-serif">Arial</option>
                            <option value="'Helvetica Neue', sans-serif">Helvetica Neue</option>
                            <option value="'Verdana', sans-serif">Verdana</option>
                        </select>
                    </div>
                    <div class="eb-global-setting">
                        <label>Max Width (px)</label>
                        <input type="number" id="gMaxWidth" value="600" min="400" max="800" step="50" onchange="updateGlobalSetting('maxWidth', this.value)">
                    </div>
                </div>
            </div>

            <!-- Library -->
            <div class="eb-sidebar-section" style="flex:1;">
                <div class="eb-sidebar-title">DigiWS Library</div>

                <!-- Products -->
                <div style="border-bottom:1px solid #334155; padding-bottom:8px; margin-bottom:8px;">
                    <button class="eb-acc-header" onclick="toggleAcc(this, 'accProd')">
                        <i class="bi bi-bag"></i> Product Cards
                        <i class="bi bi-chevron-down eb-acc-arrow"></i>
                    </button>
                    <div class="eb-acc-body eb-lib-scroll" id="accProd">
                        <?php foreach ($products as $p): ?>
                        <div class="eb-lib-item" onclick="insertProductBlock('<?= htmlspecialchars(addslashes($p['title'])) ?>', '<?= url('product/' . $p['slug']) ?>', '<?= $p['thumbnail'] ? url($p['thumbnail']) : 'https://placehold.co/150' ?>', '<?= $p['sale_price'] ?>', '<?= $p['mrp'] ?>')">
                            <img src="<?= $p['thumbnail'] ? url($p['thumbnail']) : 'https://placehold.co/150' ?>">
                            <div>
                                <div class="eb-lib-item-text"><?= htmlspecialchars($p['title']) ?></div>
                                <div class="eb-lib-item-sub">₹<?= number_format($p['sale_price']) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Coupons -->
                <div style="border-bottom:1px solid #334155; padding-bottom:8px; margin-bottom:8px;">
                    <button class="eb-acc-header" onclick="toggleAcc(this, 'accCoup')">
                        <i class="bi bi-ticket-perforated"></i> Coupons
                        <i class="bi bi-chevron-down eb-acc-arrow"></i>
                    </button>
                    <div class="eb-acc-body eb-lib-scroll" id="accCoup">
                        <?php foreach ($coupons as $c): ?>
                        <div class="eb-lib-item" onclick="insertCouponBlock('<?= htmlspecialchars(addslashes($c['code'])) ?>', '<?= $c['discount_value'] ?>', '<?= $c['discount_type'] ?>')">
                            <div style="width:30px; height:30px; background:#1e3a8a; border-radius:4px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="bi bi-tag-fill" style="color:#60a5fa; font-size:0.8rem;"></i>
                            </div>
                            <div>
                                <div class="eb-lib-item-text" style="font-family:monospace; color:#60a5fa;"><?= htmlspecialchars($c['code']) ?></div>
                                <div class="eb-lib-item-sub"><?= $c['discount_type'] === 'percentage' ? $c['discount_value'].'% Off' : '₹'.$c['discount_value'].' Off' ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Blog -->
                <div>
                    <button class="eb-acc-header" onclick="toggleAcc(this, 'accBlog')">
                        <i class="bi bi-journal-richtext"></i> Blog Posts
                        <i class="bi bi-chevron-down eb-acc-arrow"></i>
                    </button>
                    <div class="eb-acc-body eb-lib-scroll" id="accBlog">
                        <?php foreach ($blogs as $b): ?>
                        <div class="eb-lib-item" onclick="insertBlogBlock('<?= htmlspecialchars(addslashes($b['title'])) ?>', '<?= url('blog/' . $b['slug']) ?>', '<?= $b['image'] ? url($b['image']) : 'https://placehold.co/300x180' ?>')">
                            <img src="<?= $b['image'] ? url($b['image']) : 'https://placehold.co/300x180' ?>">
                            <div class="eb-lib-item-text"><?= htmlspecialchars($b['title']) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- CENTER CANVAS -->
        <div class="eb-center" id="ebCenter">
            <div id="emailViewport" style="width:600px; background:#ffffff;">
                <div id="dropCanvas"></div>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="eb-right">
            <!-- Empty State -->
            <div class="eb-panel-empty" id="panelEmpty">
                <i class="bi bi-cursor-fill"></i>
                <p>Click any block on the canvas to edit its content, style, and link settings.</p>
                <p style="margin-top:8px; color:#334155; font-size:0.7rem;">Or drag elements from the left panel to get started.</p>
            </div>

            <!-- Editor Tabs (hidden by default) -->
            <div id="panelEditor" style="display:none; flex-direction:column; flex:1;">
                <div class="eb-panel-tabs">
                    <button class="eb-tab active" onclick="switchTab('content', this)" id="tabContent">
                        <i class="bi bi-pencil-fill me-1"></i>Content
                    </button>
                    <button class="eb-tab" onclick="switchTab('style', this)" id="tabStyle">
                        <i class="bi bi-palette-fill me-1"></i>Style
                    </button>
                    <button class="eb-tab" onclick="switchTab('link', this)" id="tabLink">
                        <i class="bi bi-link-45deg me-1"></i>Link
                    </button>
                </div>

                <!-- CONTENT TAB -->
                <div class="eb-tab-panel active" id="tabPanelContent">
                    <div id="cpText">
                        <div class="eb-form-group">
                            <label class="eb-label">Block Content</label>
                            <!-- Rich Text Toolbar -->
                            <div class="eb-rich-toolbar" id="richToolbar">
                                <button class="eb-rich-btn" onclick="richCmd('bold')" title="Bold"><b>B</b></button>
                                <button class="eb-rich-btn" onclick="richCmd('italic')" title="Italic"><i>I</i></button>
                                <button class="eb-rich-btn" onclick="richCmd('underline')" title="Underline"><u>U</u></button>
                                <button class="eb-rich-btn" onclick="richCmd('strikeThrough')" title="Strikethrough" style="text-decoration:line-through;">S</button>
                                <div class="eb-rich-btn separator"></div>
                                <button class="eb-rich-btn" onclick="richCmd('justifyLeft')" title="Left"><i class="bi bi-text-left"></i></button>
                                <button class="eb-rich-btn" onclick="richCmd('justifyCenter')" title="Center"><i class="bi bi-text-center"></i></button>
                                <button class="eb-rich-btn" onclick="richCmd('justifyRight')" title="Right"><i class="bi bi-text-right"></i></button>
                                <div class="eb-rich-btn separator"></div>
                                <button class="eb-rich-btn" onclick="richCmd('insertUnorderedList')" title="Bullet List"><i class="bi bi-list-ul"></i></button>
                                <button class="eb-rich-btn" onclick="richCmd('insertOrderedList')" title="Numbered List"><i class="bi bi-list-ol"></i></button>
                                <div class="eb-rich-btn separator"></div>
                                <input type="color" title="Text Color" id="richColorPicker" style="width:22px;height:22px;border:none;background:none;cursor:pointer;padding:0;" onchange="richCmd('foreColor', this.value)">
                                <select onchange="richCmd('fontSize', this.value)" style="background:#0f172a;border:1px solid #334155;color:#94a3b8;border-radius:4px;font-size:0.68rem;padding:2px 4px;cursor:pointer;" title="Font Size">
                                    <option value="1">10px</option>
                                    <option value="2">13px</option>
                                    <option value="3" selected>16px</option>
                                    <option value="4">18px</option>
                                    <option value="5">24px</option>
                                    <option value="6">32px</option>
                                    <option value="7">48px</option>
                                </select>
                                <select onchange="richCmd('formatBlock', this.value)" style="background:#0f172a;border:1px solid #334155;color:#94a3b8;border-radius:4px;font-size:0.68rem;padding:2px 4px;cursor:pointer;" title="Heading">
                                    <option value="p">Paragraph</option>
                                    <option value="h1">H1</option>
                                    <option value="h2">H2</option>
                                    <option value="h3">H3</option>
                                    <option value="h4">H4</option>
                                </select>
                            </div>
                            <div id="richEditor"
                                contenteditable="true"
                                class="eb-input eb-rich-textarea"
                                style="min-height:120px; padding:10px; line-height:1.6; font-size:0.85rem;"
                                oninput="syncRichToBlock()"
                                onpaste="handleRichPaste(event)">
                            </div>
                        </div>

                        <!-- Personalization Tags -->
                        <div class="eb-form-group">
                            <label class="eb-label">Personalization Tags</label>
                            <div class="eb-tag-pills">
                                <span class="eb-tag-pill" onclick="insertPersonalTag('{{customer_name}}')">{{name}}</span>
                                <span class="eb-tag-pill" onclick="insertPersonalTag('{{first_name}}')">{{first_name}}</span>
                                <span class="eb-tag-pill" onclick="insertPersonalTag('{{coupon_code}}')">{{coupon}}</span>
                                <span class="eb-tag-pill" onclick="insertPersonalTag('{{order_id}}')">{{order_id}}</span>
                                <span class="eb-tag-pill" onclick="insertPersonalTag('{{site_name}}')">{{site_name}}</span>
                                <span class="eb-tag-pill" onclick="insertPersonalTag('{{site_url}}')">{{site_url}}</span>
                                <span class="eb-tag-pill" onclick="insertPersonalTag('{{current_year}}')">{{year}}</span>
                                <span class="eb-tag-pill" onclick="insertPersonalTag('{{unsubscribe}}')">{{unsub}}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Spacer Content -->
                    <div id="cpSpacer" style="display:none;">
                        <div class="eb-form-group">
                            <label class="eb-label">Spacer Height</label>
                            <div class="eb-slider-row">
                                <input type="range" class="eb-slider" id="spacerSlider" min="10" max="200" value="30" oninput="updateSpacer(this.value)">
                                <span class="eb-slider-val" id="spacerVal">30px</span>
                            </div>
                        </div>
                    </div>

                    <!-- Divider Content -->
                    <div id="cpDivider" style="display:none;">
                        <div class="eb-form-group">
                            <label class="eb-label">Divider Color</label>
                            <div class="eb-color-input-row">
                                <input type="color" id="dividerColor" value="#E2E8F0" onchange="updateBlockProp('dividerColor', this.value)">
                                <input type="text" class="eb-input" id="dividerColorText" value="#E2E8F0" oninput="updateBlockProp('dividerColor', this.value); document.getElementById('dividerColor').value=this.value;">
                            </div>
                        </div>
                        <div class="eb-form-group">
                            <label class="eb-label">Divider Thickness</label>
                            <div class="eb-slider-row">
                                <input type="range" class="eb-slider" id="dividerThick" min="1" max="8" value="1" oninput="updateBlockProp('dividerThick', this.value); document.getElementById('dividerThickVal').textContent=this.value+'px';">
                                <span class="eb-slider-val" id="dividerThickVal">1px</span>
                            </div>
                        </div>
                    </div>

                    <!-- Image Content -->
                    <div id="cpImage" style="display:none;">
                        <div class="eb-form-group">
                            <label class="eb-label">Image</label>
                            <img id="imgPreviewEl" class="eb-img-preview" src="">
                            <div class="eb-img-upload-zone" onclick="document.getElementById('imgFileInput').click()">
                                <i class="bi bi-cloud-upload"></i>
                                <span>Click to upload or drop image here</span>
                            </div>
                            <input type="file" id="imgFileInput" accept="image/*" style="display:none;" onchange="handleImgUpload(this)">
                            <label class="eb-label" style="margin-bottom:4px;">Or paste Image URL</label>
                            <input type="text" class="eb-input" id="imgUrlInput" placeholder="https://..." oninput="updateBlockProp('image_url', this.value); updateImgPreview(this.value);">
                        </div>
                        <div class="eb-form-group">
                            <label class="eb-label">Alt Text</label>
                            <input type="text" class="eb-input" id="imgAltInput" placeholder="Image description..." oninput="updateBlockProp('alt', this.value)">
                        </div>
                        <div class="eb-form-group">
                            <label class="eb-label">Image Width</label>
                            <select class="eb-input" id="imgWidthSel" onchange="updateBlockProp('imgWidth', this.value)">
                                <option value="100%">Full Width (100%)</option>
                                <option value="75%">75%</option>
                                <option value="50%">Half (50%)</option>
                                <option value="auto">Auto</option>
                            </select>
                        </div>
                    </div>

                    <!-- Video Content -->
                    <div id="cpVideo" style="display:none;">
                        <div class="eb-form-group">
                            <label class="eb-label">Thumbnail Image URL</label>
                            <input type="text" class="eb-input" id="videoThumbInput" placeholder="https://..." oninput="updateBlockProp('image_url', this.value)">
                        </div>
                        <div class="eb-form-group">
                            <label class="eb-label">Video URL (YouTube/Vimeo)</label>
                            <input type="text" class="eb-input" id="videoUrlInput" placeholder="https://youtube.com/..." oninput="updateBlockProp('url', this.value)">
                        </div>
                    </div>

                    <!-- Social Content -->
                    <div id="cpSocial" style="display:none;">
                        <label class="eb-label mb-2">Social Media Links</label>
                        <div class="eb-social-row">
                            <div class="eb-social-icon" style="background:#1877f2;"><i class="bi bi-facebook text-white"></i></div>
                            <input type="text" class="eb-input" id="socialFb" placeholder="Facebook URL" oninput="updateSocialLink('facebook', this.value)">
                        </div>
                        <div class="eb-social-row">
                            <div class="eb-social-icon" style="background:#1da1f2;"><i class="bi bi-twitter text-white"></i></div>
                            <input type="text" class="eb-input" id="socialTw" placeholder="Twitter URL" oninput="updateSocialLink('twitter', this.value)">
                        </div>
                        <div class="eb-social-row">
                            <div class="eb-social-icon" style="background:linear-gradient(135deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);"><i class="bi bi-instagram text-white"></i></div>
                            <input type="text" class="eb-input" id="socialIg" placeholder="Instagram URL" oninput="updateSocialLink('instagram', this.value)">
                        </div>
                        <div class="eb-social-row">
                            <div class="eb-social-icon" style="background:#0077b5;"><i class="bi bi-linkedin text-white"></i></div>
                            <input type="text" class="eb-input" id="socialLi" placeholder="LinkedIn URL" oninput="updateSocialLink('linkedin', this.value)">
                        </div>
                        <div class="eb-social-row">
                            <div class="eb-social-icon" style="background:#ff0000;"><i class="bi bi-youtube text-white"></i></div>
                            <input type="text" class="eb-input" id="socialYt" placeholder="YouTube URL" oninput="updateSocialLink('youtube', this.value)">
                        </div>
                        <div class="eb-social-row">
                            <div class="eb-social-icon" style="background:#25d366;"><i class="bi bi-whatsapp text-white"></i></div>
                            <input type="text" class="eb-input" id="socialWa" placeholder="WhatsApp URL" oninput="updateSocialLink('whatsapp', this.value)">
                        </div>
                        <div class="eb-form-group" style="margin-top:12px;">
                            <label class="eb-label">Icon Style</label>
                            <select class="eb-input" id="socialIconStyle" onchange="updateBlockProp('iconStyle', this.value)">
                                <option value="color">Colored</option>
                                <option value="dark">Dark</option>
                                <option value="light">Light / White</option>
                            </select>
                        </div>
                    </div>

                    <!-- Library Item -->
                    <div id="cpLibrary" style="display:none;">
                        <div class="eb-form-group">
                            <label class="eb-label">HTML Content</label>
                            <textarea class="eb-input" rows="10" id="libContentInput" oninput="updateBlockProp('content', this.value)"></textarea>
                        </div>
                    </div>
                </div>

                <!-- STYLE TAB -->
                <div class="eb-tab-panel" id="tabPanelStyle">
                    
                    <!-- Background Color -->
                    <div class="eb-form-group">
                        <label class="eb-label">Block Background Color</label>
                        <div class="eb-color-input-row">
                            <input type="color" id="styleBlockBg" value="#ffffff" oninput="updateStyle('blockBg', this.value); document.getElementById('styleBlockBgText').value=this.value;">
                            <input type="text" class="eb-input" id="styleBlockBgText" value="#ffffff" oninput="updateStyle('blockBg', this.value); document.getElementById('styleBlockBg').value=this.value;">
                        </div>
                    </div>

                    <!-- Text Color -->
                    <div class="eb-form-group" id="styleTextColorGroup">
                        <label class="eb-label">Text Color</label>
                        <div class="eb-color-input-row">
                            <input type="color" id="styleTextColor" value="#334155" oninput="updateStyle('textColor', this.value); document.getElementById('styleTextColorText').value=this.value;">
                            <input type="text" class="eb-input" id="styleTextColorText" value="#334155" oninput="updateStyle('textColor', this.value); document.getElementById('styleTextColor').value=this.value;">
                        </div>
                    </div>

                    <!-- Button Specific Styles -->
                    <div id="styleBtnGroup">
                        <div class="eb-form-group">
                            <label class="eb-label">Button Color</label>
                            <div class="eb-color-input-row">
                                <input type="color" id="styleBtnBg" value="#2563EB" oninput="updateStyle('btnBg', this.value); document.getElementById('styleBtnBgText').value=this.value;">
                                <input type="text" class="eb-input" id="styleBtnBgText" value="#2563EB" oninput="updateStyle('btnBg', this.value); document.getElementById('styleBtnBg').value=this.value;">
                            </div>
                        </div>
                        <div class="eb-form-group">
                            <label class="eb-label">Button Text Color</label>
                            <div class="eb-color-input-row">
                                <input type="color" id="styleBtnText" value="#ffffff" oninput="updateStyle('btnText', this.value);">
                                <input type="text" class="eb-input" id="styleBtnTextHex" value="#ffffff" oninput="updateStyle('btnText', this.value); document.getElementById('styleBtnText').value=this.value;">
                            </div>
                        </div>
                        <div class="eb-form-group">
                            <label class="eb-label">Button Border Radius</label>
                            <div class="eb-slider-row">
                                <input type="range" class="eb-slider" id="styleBtnRadius" min="0" max="40" value="8" oninput="updateStyle('btnRadius', this.value); document.getElementById('styleBtnRadiusVal').textContent=this.value+'px';">
                                <span class="eb-slider-val" id="styleBtnRadiusVal">8px</span>
                            </div>
                        </div>
                        <div class="eb-form-group">
                            <label class="eb-label">Button Alignment</label>
                            <div class="eb-align-group">
                                <button class="eb-align-btn" onclick="updateStyle('btnAlign', 'left'); setAlignActive(this)"><i class="bi bi-align-start"></i></button>
                                <button class="eb-align-btn active" onclick="updateStyle('btnAlign', 'center'); setAlignActive(this)"><i class="bi bi-align-center"></i></button>
                                <button class="eb-align-btn" onclick="updateStyle('btnAlign', 'right'); setAlignActive(this)"><i class="bi bi-align-end"></i></button>
                            </div>
                        </div>
                        <div class="eb-form-group">
                            <label class="eb-label">Button Full Width</label>
                            <div class="eb-toggle-row" style="margin:0;">
                                <span class="eb-toggle-label">Stretch to full width</span>
                                <label class="eb-toggle">
                                    <input type="checkbox" id="styleBtnFull" onchange="updateStyle('btnFull', this.checked)">
                                    <span class="eb-toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Text Alignment (for text/header/footer) -->
                    <div class="eb-form-group" id="styleAlignGroup">
                        <label class="eb-label">Text Alignment</label>
                        <div class="eb-align-group" id="textAlignBtns">
                            <button class="eb-align-btn" onclick="updateStyle('textAlign', 'left'); setAlignActive(this)"><i class="bi bi-text-left"></i> Left</button>
                            <button class="eb-align-btn active" onclick="updateStyle('textAlign', 'center'); setAlignActive(this)"><i class="bi bi-text-center"></i> Center</button>
                            <button class="eb-align-btn" onclick="updateStyle('textAlign', 'right'); setAlignActive(this)"><i class="bi bi-text-right"></i> Right</button>
                        </div>
                    </div>

                    <!-- Padding -->
                    <div class="eb-form-group">
                        <label class="eb-label">Padding (px)</label>
                        <div class="eb-padding-grid">
                            <div class="eb-padding-item">
                                <label>Top</label>
                                <input type="number" class="eb-input" style="padding:5px 8px;" id="padTop" value="12" min="0" max="80" oninput="updateStylePadding()">
                            </div>
                            <div class="eb-padding-item">
                                <label>Bottom</label>
                                <input type="number" class="eb-input" style="padding:5px 8px;" id="padBottom" value="12" min="0" max="80" oninput="updateStylePadding()">
                            </div>
                            <div class="eb-padding-item">
                                <label>Left</label>
                                <input type="number" class="eb-input" style="padding:5px 8px;" id="padLeft" value="12" min="0" max="80" oninput="updateStylePadding()">
                            </div>
                            <div class="eb-padding-item">
                                <label>Right</label>
                                <input type="number" class="eb-input" style="padding:5px 8px;" id="padRight" value="12" min="0" max="80" oninput="updateStylePadding()">
                            </div>
                        </div>
                    </div>

                    <!-- Border Radius -->
                    <div class="eb-form-group">
                        <label class="eb-label">Block Border Radius</label>
                        <div class="eb-slider-row">
                            <input type="range" class="eb-slider" id="styleBlockRadius" min="0" max="30" value="0" oninput="updateStyle('blockRadius', this.value); document.getElementById('styleBlockRadiusVal').textContent=this.value+'px';">
                            <span class="eb-slider-val" id="styleBlockRadiusVal">0px</span>
                        </div>
                    </div>

                    <!-- Font Size (for header/text) -->
                    <div class="eb-form-group" id="styleFontSizeGroup">
                        <label class="eb-label">Font Size</label>
                        <div class="eb-slider-row">
                            <input type="range" class="eb-slider" id="styleFontSize" min="10" max="64" value="16" oninput="updateStyle('fontSize', this.value); document.getElementById('styleFontSizeVal').textContent=this.value+'px';">
                            <span class="eb-slider-val" id="styleFontSizeVal">16px</span>
                        </div>
                    </div>
                </div>

                <!-- LINK TAB -->
                <div class="eb-tab-panel" id="tabPanelLink">
                    <div id="lpLink">
                        <div class="eb-form-group">
                            <label class="eb-label">Link Type</label>
                            <div class="eb-link-type" id="panelLinkType">
                                <button class="eb-link-type-btn active" onclick="setLinkType('url', this)">URL</button>
                                <button class="eb-link-type-btn" onclick="setLinkType('email', this)">Email</button>
                                <button class="eb-link-type-btn" onclick="setLinkType('phone', this)">Phone</button>
                            </div>
                        </div>
                        <div class="eb-form-group">
                            <label class="eb-label" id="panelLinkLabel">Link URL</label>
                            <input type="text" class="eb-input" id="panelLinkInput" placeholder="https://example.com" oninput="updateBlockProp('url', this.value)">
                        </div>
                        <div class="eb-toggle-row">
                            <span class="eb-toggle-label">Open in new tab</span>
                            <label class="eb-toggle">
                                <input type="checkbox" id="panelLinkNewTab" checked onchange="updateBlockProp('linkNewTab', this.checked)">
                                <span class="eb-toggle-slider"></span>
                            </label>
                        </div>
                        <hr class="eb-section-divider">
                        <div class="eb-form-group">
                            <label class="eb-label">Personalization Tags for Link</label>
                            <div class="eb-tag-pills">
                                <span class="eb-tag-pill" onclick="insertLinkTag('{{site_url}}')">{{site_url}}</span>
                                <span class="eb-tag-pill" onclick="insertLinkTag('{{unsubscribe}}')">{{unsub}}</span>
                                <span class="eb-tag-pill" onclick="insertLinkTag('{{track_click}}')">{{track}}</span>
                            </div>
                        </div>
                    </div>
                    <div id="lpNoLink" style="display:none; text-align:center; color:#475569; padding:30px 20px;">
                        <i class="bi bi-link-45deg" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                        <p style="font-size:0.78rem;">This block type does not have a clickable link.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ========================
// STATE
// ========================
let blocks = <?= $template['content_json'] ?: '[]' ?>;
let selectedIdx = null;
let undoStack = [];
let redoStack = [];
let isDirty = false;

let globalSettings = {
    bgColor: '#F8FAFC',
    cardBg: '#ffffff',
    fontFamily: 'system-ui, sans-serif',
    maxWidth: 600
};

// Merge saved global if stored
<?php if (!empty($template['global_settings'])): ?>
try {
    const savedGlobal = <?= $template['global_settings'] ?>;
    if (savedGlobal) Object.assign(globalSettings, savedGlobal);
} catch(e) {}
<?php endif; ?>

// ========================
// UNDO / REDO
// ========================
function pushUndo() {
    undoStack.push(JSON.stringify({ blocks, globalSettings }));
    if (undoStack.length > 50) undoStack.shift();
    redoStack = [];
    document.getElementById('undoBtn').disabled = false;
    document.getElementById('redoBtn').disabled = true;
}

function undoAction() {
    if (!undoStack.length) return;
    redoStack.push(JSON.stringify({ blocks, globalSettings }));
    const state = JSON.parse(undoStack.pop());
    blocks = state.blocks;
    globalSettings = state.globalSettings;
    document.getElementById('undoBtn').disabled = undoStack.length === 0;
    document.getElementById('redoBtn').disabled = false;
    renderCanvas();
    if (selectedIdx !== null && selectedIdx < blocks.length) selectBlock(selectedIdx);
    else clearSelection();
}

function redoAction() {
    if (!redoStack.length) return;
    undoStack.push(JSON.stringify({ blocks, globalSettings }));
    const state = JSON.parse(redoStack.pop());
    blocks = state.blocks;
    globalSettings = state.globalSettings;
    document.getElementById('redoBtn').disabled = redoStack.length === 0;
    document.getElementById('undoBtn').disabled = false;
    renderCanvas();
    if (selectedIdx !== null && selectedIdx < blocks.length) selectBlock(selectedIdx);
    else clearSelection();
}

// Keyboard shortcuts
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && !e.shiftKey && e.key === 'z') { e.preventDefault(); undoAction(); }
    if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.shiftKey && e.key === 'z'))) { e.preventDefault(); redoAction(); }
    if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); saveTemplateDesign(); }
});

// ========================
// GLOBAL SETTINGS
// ========================
function updateGlobalSetting(key, val) {
    pushUndo();
    globalSettings[key] = val;
    renderCanvas();
}

// Init global UI
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('gBgColor').value = globalSettings.bgColor;
    document.getElementById('gBgColorText').value = globalSettings.bgColor;
    document.getElementById('gCardBg').value = globalSettings.cardBg;
    document.getElementById('gCardBgText').value = globalSettings.cardBg;
    document.getElementById('gFontFamily').value = globalSettings.fontFamily;
    document.getElementById('gMaxWidth').value = globalSettings.maxWidth;
    renderCanvas();
    initDnD();
});

// ========================
// CANVAS RENDER
// ========================
function renderCanvas() {
    const canvas = document.getElementById('dropCanvas');
    const vp = document.getElementById('emailViewport');
    vp.style.backgroundColor = globalSettings.cardBg;
    vp.style.maxWidth = globalSettings.maxWidth + 'px';
    document.getElementById('ebCenter').style.background = globalSettings.bgColor;

    canvas.innerHTML = '';

    if (!blocks.length) {
        canvas.innerHTML = `
            <div class="eb-empty-state">
                <i class="bi bi-inbox"></i>
                <p><strong>Your canvas is empty</strong></p>
                <p>Drag elements from the left panel or click them to add to your email</p>
            </div>
        `;
        return;
    }

    blocks.forEach((block, idx) => {
        const wrap = document.createElement('div');
        wrap.className = 'canvas-block' + (selectedIdx === idx ? ' selected' : '');
        wrap.setAttribute('data-idx', idx);
        wrap.onclick = (e) => { e.stopPropagation(); selectBlock(idx); };

        // Apply block-level styles
        const s = block.styles || {};
        wrap.style.backgroundColor = s.blockBg || 'transparent';
        wrap.style.borderRadius = (s.blockRadius || 0) + 'px';
        if (s.padding) {
            wrap.style.padding = s.padding;
        }

        let inner = renderBlockInner(block, idx);

        wrap.innerHTML = `
            <div class="eb-block-type-badge">${block.type}</div>
            <div class="block-toolbar">
                <button onclick="moveUp(${idx},event)" title="Move Up"><i class="bi bi-arrow-up"></i></button>
                <button onclick="moveDown(${idx},event)" title="Move Down"><i class="bi bi-arrow-down"></i></button>
                <button onclick="duplicateBlock(${idx},event)" title="Duplicate"><i class="bi bi-copy"></i></button>
                <button class="danger" onclick="deleteBlock(${idx},event)" title="Delete"><i class="bi bi-trash3"></i></button>
            </div>
            ${inner}
        `;
        canvas.appendChild(wrap);
    });

    // Click outside deselect
    document.getElementById('dropCanvas').onclick = (e) => {
        if (e.target === e.currentTarget) clearSelection();
    };
}

function renderBlockInner(block, idx) {
    const s = block.styles || {};
    const textColor = s.textColor || '#334155';
    const textAlign = s.textAlign || 'left';
    const fontSize = s.fontSize ? s.fontSize + 'px' : '15px';

    if (block.type === 'header') {
        return `<div style="padding:16px; background:${s.blockBg||'#EFF6FF'}; border-radius:6px; text-align:${textAlign}; color:${textColor}; font-size:${fontSize}; font-weight:bold; font-family:${globalSettings.fontFamily};">${block.content || 'Email Header'}</div>`;
    }
    if (block.type === 'text') {
        return `<div style="font-size:${fontSize}; color:${textColor}; line-height:1.7; text-align:${textAlign}; font-family:${globalSettings.fontFamily};">${block.content || '<p>Your text goes here...</p>'}</div>`;
    }
    if (block.type === 'button') {
        const btnBg = s.btnBg || '#2563EB';
        const btnText = s.btnText || '#ffffff';
        const btnRadius = s.btnRadius !== undefined ? s.btnRadius : 8;
        const btnAlign = s.btnAlign || 'center';
        const btnFull = s.btnFull ? 'width:100%; display:block; text-align:center;' : 'display:inline-block;';
        const btnUrl = block.url || '#';
        const btnTarget = block.linkNewTab !== false ? '_blank' : '_self';
        return `<div style="text-align:${btnAlign}; padding:8px 0;">
            <a href="${btnUrl}" target="${btnTarget}" style="background:${btnBg}; color:${btnText}; padding:13px 28px; border-radius:${btnRadius}px; font-weight:bold; font-size:${fontSize}; font-family:${globalSettings.fontFamily}; cursor:pointer; ${btnFull} box-shadow:0 3px 10px rgba(0,0,0,0.15); text-decoration:none; display:${btnFull ? 'block' : 'inline-block'};">${block.content || 'Click Here'}</a>
        </div>`;
    }
    if (block.type === 'divider') {
        const dc = block.dividerColor || '#E2E8F0';
        const dt = block.dividerThick || 1;
        return `<hr style="border:none; border-top:${dt}px solid ${dc}; margin:8px 0;">`;
    }
    if (block.type === 'image') {
        const w = block.imgWidth || '100%';
        return `<div style="text-align:center;">
            <img src="${block.image_url || 'https://placehold.co/600x200'}" alt="${block.alt || ''}" style="max-width:${w}; height:auto; display:block; margin:0 auto; border-radius:6px;">
        </div>`;
    }
    if (block.type === 'video') {
        return `<div style="position:relative; text-align:center; background:#000; border-radius:8px; overflow:hidden; max-height:240px;">
            <img src="${block.image_url || 'https://placehold.co/600x300'}" style="width:100%; height:auto; opacity:0.7; display:block;">
            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); pointer-events:none;">
                <i class="bi bi-play-circle-fill" style="font-size:3.5rem; color:#fff; filter:drop-shadow(0 2px 8px rgba(0,0,0,0.5));"></i>
            </div>
        </div>`;
    }
    if (block.type === 'social') {
        const is = block.iconStyle || 'color';
        const socials = block.socials || {};
        const icons = [
            { key: 'facebook', icon: 'bi-facebook', color: '#1877f2', label: 'Facebook' },
            { key: 'twitter', icon: 'bi-twitter', color: '#1da1f2', label: 'Twitter' },
            { key: 'instagram', icon: 'bi-instagram', color: '#e1306c', label: 'Instagram' },
            { key: 'linkedin', icon: 'bi-linkedin', color: '#0077b5', label: 'LinkedIn' },
            { key: 'youtube', icon: 'bi-youtube', color: '#ff0000', label: 'YouTube' },
            { key: 'whatsapp', icon: 'bi-whatsapp', color: '#25d366', label: 'WhatsApp' },
        ];
        let iconsHtml = icons.map(ic => {
            const url = socials[ic.key] || '#';
            const color = is === 'color' ? ic.color : (is === 'dark' ? '#334155' : '#94a3b8');
            return `<a href="${url}" style="margin:0 6px; text-decoration:none;"><i class="bi ${ic.icon}" style="font-size:1.6rem; color:${color};"></i></a>`;
        }).join('');
        return `<div style="text-align:center; padding:12px 0;">${iconsHtml}</div>`;
    }
    if (block.type === 'spacer') {
        const h = block.height || 30;
        return `<div style="height:${h}px; background:repeating-linear-gradient(45deg, transparent, transparent 4px, rgba(99,102,241,0.05) 4px, rgba(99,102,241,0.05) 8px); border-radius:4px; display:flex; align-items:center; justify-content:center;">
            <span style="font-size:0.65rem; color:#94a3b8; font-weight:600; letter-spacing:1px;">SPACER · ${h}px</span>
        </div>`;
    }
    if (block.type === 'footer') {
        return `<div style="text-align:center; padding:16px; color:${textColor}; font-size:13px; line-height:1.6; font-family:${globalSettings.fontFamily}; background:${s.blockBg||'#F8FAFC'}; border-radius:6px;">${block.content || '<p>&copy; {{current_year}} DigiWS · <a href="{{unsubscribe}}">Unsubscribe</a></p>'}</div>`;
    }
    if (block.type === 'library_item') {
        return `<div>${block.content}</div>`;
    }
    return '';
}

// ========================
// ADD BLOCK
// ========================
function addNewBlock(type) {
    pushUndo();
    let b = { type, styles: {} };
    if (type === 'header') { b.content = '<h2>DigiWS Newsletter</h2>'; b.styles.textAlign = 'center'; b.styles.textColor = '#1E40AF'; }
    else if (type === 'text') { b.content = '<p>Write your email content here. You can use <strong>bold</strong>, <em>italic</em>, and other formatting.</p>'; }
    else if (type === 'button') { b.content = 'Shop Now'; b.url = '{{site_url}}'; b.styles.btnBg = '#2563EB'; b.styles.btnText = '#ffffff'; b.styles.btnAlign = 'center'; b.styles.btnRadius = 8; }
    else if (type === 'image') { b.image_url = 'https://placehold.co/600x200/EEF2FF/6366f1?text=Your+Image'; b.alt = ''; b.imgWidth = '100%'; }
    else if (type === 'video') { b.image_url = 'https://placehold.co/600x300/1e293b/6366f1?text=Video+Thumbnail'; b.url = 'https://youtube.com'; }
    else if (type === 'divider') { b.dividerColor = '#E2E8F0'; b.dividerThick = 1; }
    else if (type === 'social') { b.socials = {}; b.iconStyle = 'color'; }
    else if (type === 'spacer') { b.height = 30; }
    else if (type === 'footer') { b.content = '<p>&copy; {{current_year}} DigiWS. Premium Digital Products. &nbsp;|&nbsp; <a href="{{unsubscribe}}" style="color:#6366f1;">Unsubscribe</a></p>'; b.styles.textColor = '#64748B'; }
    blocks.push(b);
    renderCanvas();
    selectBlock(blocks.length - 1);
}

// ========================
// SELECT / DESELECT BLOCK
// ========================
function selectBlock(idx) {
    selectedIdx = idx;
    renderCanvas();

    const block = blocks[idx];
    document.getElementById('panelEmpty').style.display = 'none';
    const pe = document.getElementById('panelEditor');
    pe.style.display = 'flex';

    // Show appropriate content sub-panel
    const panels = ['cpText', 'cpSpacer', 'cpDivider', 'cpImage', 'cpVideo', 'cpSocial', 'cpLibrary'];
    panels.forEach(p => document.getElementById(p).style.display = 'none');

    const hasText = ['header','text','button','footer'].includes(block.type);
    const hasLink = ['button','image','video'].includes(block.type);

    if (block.type === 'spacer') {
        document.getElementById('cpSpacer').style.display = 'block';
        document.getElementById('spacerSlider').value = block.height || 30;
        document.getElementById('spacerVal').textContent = (block.height || 30) + 'px';
    } else if (block.type === 'divider') {
        document.getElementById('cpDivider').style.display = 'block';
        document.getElementById('dividerColor').value = block.dividerColor || '#E2E8F0';
        document.getElementById('dividerColorText').value = block.dividerColor || '#E2E8F0';
        document.getElementById('dividerThick').value = block.dividerThick || 1;
        document.getElementById('dividerThickVal').textContent = (block.dividerThick || 1) + 'px';
    } else if (block.type === 'image') {
        document.getElementById('cpImage').style.display = 'block';
        document.getElementById('imgUrlInput').value = block.image_url || '';
        document.getElementById('imgAltInput').value = block.alt || '';
        document.getElementById('imgWidthSel').value = block.imgWidth || '100%';
        updateImgPreview(block.image_url);
    } else if (block.type === 'video') {
        document.getElementById('cpVideo').style.display = 'block';
        document.getElementById('videoThumbInput').value = block.image_url || '';
        document.getElementById('videoUrlInput').value = block.url || '';
    } else if (block.type === 'social') {
        document.getElementById('cpSocial').style.display = 'block';
        const sc = block.socials || {};
        document.getElementById('socialFb').value = sc.facebook || '';
        document.getElementById('socialTw').value = sc.twitter || '';
        document.getElementById('socialIg').value = sc.instagram || '';
        document.getElementById('socialLi').value = sc.linkedin || '';
        document.getElementById('socialYt').value = sc.youtube || '';
        document.getElementById('socialWa').value = sc.whatsapp || '';
        document.getElementById('socialIconStyle').value = block.iconStyle || 'color';
    } else if (block.type === 'library_item') {
        document.getElementById('cpLibrary').style.display = 'block';
        document.getElementById('libContentInput').value = block.content || '';
    } else if (hasText) {
        document.getElementById('cpText').style.display = 'block';
        document.getElementById('richEditor').innerHTML = block.content || '';
    }

    // Style tab
    const s = block.styles || {};
    document.getElementById('styleBlockBg').value = s.blockBg || '#ffffff';
    document.getElementById('styleBlockBgText').value = s.blockBg || '#ffffff';
    document.getElementById('styleTextColor').value = s.textColor || '#334155';
    document.getElementById('styleTextColorText').value = s.textColor || '#334155';
    document.getElementById('styleBlockRadius').value = s.blockRadius || 0;
    document.getElementById('styleBlockRadiusVal').textContent = (s.blockRadius || 0) + 'px';

    // Padding
    if (s.padding) {
        const parts = s.padding.split(' ').map(v => parseInt(v) || 0);
        document.getElementById('padTop').value = parts[0] || 12;
        document.getElementById('padBottom').value = parts[2] !== undefined ? parts[2] : (parts[0] || 12);
        document.getElementById('padLeft').value = parts[3] !== undefined ? parts[3] : (parts[1] || 12);
        document.getElementById('padRight').value = parts[1] || 12;
    } else {
        ['padTop','padBottom','padLeft','padRight'].forEach(id => document.getElementById(id).value = 12);
    }

    // Show/hide style groups
    document.getElementById('styleBtnGroup').style.display = block.type === 'button' ? 'block' : 'none';
    document.getElementById('styleAlignGroup').style.display = hasText ? 'block' : 'none';
    document.getElementById('styleFontSizeGroup').style.display = (hasText || block.type === 'text') ? 'block' : 'none';
    document.getElementById('styleTextColorGroup').style.display = hasText ? 'block' : 'none';

    if (block.type === 'button') {
        document.getElementById('styleBtnBg').value = s.btnBg || '#2563EB';
        document.getElementById('styleBtnBgText').value = s.btnBg || '#2563EB';
        document.getElementById('styleBtnText').value = s.btnText || '#ffffff';
        document.getElementById('styleBtnTextHex').value = s.btnText || '#ffffff';
        document.getElementById('styleBtnRadius').value = s.btnRadius !== undefined ? s.btnRadius : 8;
        document.getElementById('styleBtnRadiusVal').textContent = (s.btnRadius !== undefined ? s.btnRadius : 8) + 'px';
        document.getElementById('styleBtnFull').checked = !!s.btnFull;
    }

    if (s.fontSize) {
        document.getElementById('styleFontSize').value = s.fontSize;
        document.getElementById('styleFontSizeVal').textContent = s.fontSize + 'px';
    }

    // Link tab
    const lpLink = document.getElementById('lpLink');
    const lpNoLink = document.getElementById('lpNoLink');
    if (hasLink || block.type === 'library_item') {
        lpLink.style.display = 'block';
        lpNoLink.style.display = 'none';
        document.getElementById('panelLinkInput').value = block.url || '';
        document.getElementById('panelLinkNewTab').checked = block.linkNewTab !== false;
    } else {
        lpLink.style.display = 'none';
        lpNoLink.style.display = 'block';
    }
}

function clearSelection() {
    selectedIdx = null;
    renderCanvas();
    document.getElementById('panelEmpty').style.display = 'flex';
    document.getElementById('panelEditor').style.display = 'none';
}

// ========================
// RICH TEXT
// ========================
function richCmd(cmd, val) {
    document.getElementById('richEditor').focus();
    document.execCommand(cmd, false, val);
    syncRichToBlock();
}

function syncRichToBlock() {
    if (selectedIdx === null) return;
    const content = document.getElementById('richEditor').innerHTML;
    blocks[selectedIdx].content = content;
    isDirty = true;
    renderCanvas();
}

function handleRichPaste(e) {
    e.preventDefault();
    const text = (e.clipboardData || window.clipboardData).getData('text/html') || (e.clipboardData || window.clipboardData).getData('text');
    document.execCommand('insertHTML', false, text);
}

function insertPersonalTag(tag) {
    const editor = document.getElementById('richEditor');
    editor.focus();
    document.execCommand('insertText', false, tag);
    syncRichToBlock();
}

// ========================
// BLOCK PROPERTY UPDATE
// ========================
function updateBlockProp(key, val) {
    if (selectedIdx === null) return;
    blocks[selectedIdx][key] = val;
    isDirty = true;
    // Debounced render
    clearTimeout(window._renderTimeout);
    window._renderTimeout = setTimeout(() => renderCanvas(), 200);
}

function updateStyle(key, val) {
    if (selectedIdx === null) return;
    if (!blocks[selectedIdx].styles) blocks[selectedIdx].styles = {};
    blocks[selectedIdx].styles[key] = val;
    isDirty = true;
    clearTimeout(window._renderTimeout);
    window._renderTimeout = setTimeout(() => renderCanvas(), 150);
}

function updateStylePadding() {
    const t = document.getElementById('padTop').value || 0;
    const b = document.getElementById('padBottom').value || 0;
    const l = document.getElementById('padLeft').value || 0;
    const r = document.getElementById('padRight').value || 0;
    updateStyle('padding', `${t}px ${r}px ${b}px ${l}px`);
}

function updateSpacer(val) {
    if (selectedIdx === null) return;
    blocks[selectedIdx].height = parseInt(val);
    document.getElementById('spacerVal').textContent = val + 'px';
    clearTimeout(window._renderTimeout);
    window._renderTimeout = setTimeout(() => renderCanvas(), 150);
}

function updateSocialLink(platform, val) {
    if (selectedIdx === null) return;
    if (!blocks[selectedIdx].socials) blocks[selectedIdx].socials = {};
    blocks[selectedIdx].socials[platform] = val;
    isDirty = true;
    clearTimeout(window._renderTimeout);
    window._renderTimeout = setTimeout(() => renderCanvas(), 300);
}

// ========================
// IMAGE UPLOAD
// ========================
function handleImgUpload(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        const dataUrl = e.target.result;
        document.getElementById('imgUrlInput').value = dataUrl;
        updateBlockProp('image_url', dataUrl);
        updateImgPreview(dataUrl);
    };
    reader.readAsDataURL(file);
}

function updateImgPreview(src) {
    const el = document.getElementById('imgPreviewEl');
    if (src) {
        el.src = src;
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}

// ========================
// LINK TYPE
// ========================
function setLinkType(type, btn) {
    btn.closest('.eb-link-type').querySelectorAll('.eb-link-type-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const labels = { url: 'Link URL', email: 'Email Address', phone: 'Phone Number' };
    const placeholders = { url: 'https://example.com', email: 'hello@example.com', phone: '+91 9876543210' };
    document.getElementById('panelLinkLabel').textContent = labels[type] || 'Link';
    document.getElementById('panelLinkInput').placeholder = placeholders[type] || '';

    // Auto prefix
    const current = document.getElementById('panelLinkInput').value;
    if (type === 'email' && !current.startsWith('mailto:')) {
        document.getElementById('panelLinkInput').value = 'mailto:' + current;
        updateBlockProp('url', 'mailto:' + current);
    } else if (type === 'phone' && !current.startsWith('tel:')) {
        document.getElementById('panelLinkInput').value = 'tel:' + current;
        updateBlockProp('url', 'tel:' + current);
    }
}

function insertLinkTag(tag) {
    const inp = document.getElementById('panelLinkInput');
    inp.value += tag;
    updateBlockProp('url', inp.value);
}

// ========================
// BLOCK OPERATIONS
// ========================
function moveUp(idx, e) {
    e.stopPropagation();
    if (idx === 0) return;
    pushUndo();
    [blocks[idx-1], blocks[idx]] = [blocks[idx], blocks[idx-1]];
    if (selectedIdx === idx) selectedIdx--;
    else if (selectedIdx === idx-1) selectedIdx++;
    renderCanvas();
}

function moveDown(idx, e) {
    e.stopPropagation();
    if (idx >= blocks.length-1) return;
    pushUndo();
    [blocks[idx+1], blocks[idx]] = [blocks[idx], blocks[idx+1]];
    if (selectedIdx === idx) selectedIdx++;
    else if (selectedIdx === idx+1) selectedIdx--;
    renderCanvas();
}

function duplicateBlock(idx, e) {
    e.stopPropagation();
    pushUndo();
    const copy = JSON.parse(JSON.stringify(blocks[idx]));
    blocks.splice(idx+1, 0, copy);
    renderCanvas();
    selectBlock(idx+1);
    showToast('Block duplicated!', 'success');
}

function deleteBlock(idx, e) {
    e.stopPropagation();
    pushUndo();
    blocks.splice(idx, 1);
    if (selectedIdx === idx) clearSelection();
    else if (selectedIdx > idx) selectedIdx--;
    renderCanvas();
    showToast('Block deleted', 'success');
}

// ========================
// DRAG & DROP
// ========================
function initDnD() {
    const canvas = document.getElementById('dropCanvas');
    canvas.addEventListener('dragover', e => e.preventDefault());
    canvas.addEventListener('drop', e => {
        e.preventDefault();
        const type = e.dataTransfer.getData('text/plain');
        if (type) addNewBlock(type);
    });

    document.querySelectorAll('.eb-element-card').forEach(card => {
        card.addEventListener('dragstart', e => {
            e.dataTransfer.setData('text/plain', card.getAttribute('data-type'));
        });
        card.addEventListener('click', () => {
            addNewBlock(card.getAttribute('data-type'));
        });
    });
}

// ========================
// LIBRARY INSERTS
// ========================
function insertProductBlock(title, url, thumbnail, salePrice, mrp) {
    pushUndo();
    const html = `
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; background:#ffffff; margin:8px 0;">
            <tr><td align="center" style="padding:16px;">
                <img src="${thumbnail}" style="max-width:200px; max-height:160px; object-fit:cover; border-radius:8px; display:block;">
            </td></tr>
            <tr><td style="padding:0 20px 20px; text-align:center;">
                <h3 style="margin:0 0 8px; font-family:sans-serif; color:#1e293b; font-size:1.1rem;">${title}</h3>
                <p style="margin:0 0 16px; font-size:1.15rem; font-weight:bold; color:#16a34a;">
                    ₹${parseFloat(salePrice).toLocaleString('en-IN')}
                    <span style="font-size:0.85rem; font-weight:normal; text-decoration:line-through; color:#64748b; margin-left:6px;">₹${parseFloat(mrp).toLocaleString('en-IN')}</span>
                </p>
                <a href="${url}" style="background:#2563eb; color:#fff; padding:10px 22px; text-decoration:none; border-radius:8px; font-weight:bold; font-size:0.9rem; display:inline-block;">View Product</a>
            </td></tr>
        </table>`;
    blocks.push({ type: 'library_item', content: html, url });
    renderCanvas();
    selectBlock(blocks.length - 1);
}

function insertCouponBlock(code, val, type) {
    pushUndo();
    const dv = type === 'percentage' ? val + '%' : '₹' + val;
    const html = `
        <table width="100%" cellpadding="0" cellspacing="0" style="border:2px dashed #3b82f6; border-radius:12px; background:#eff6ff; margin:8px 0; text-align:center;">
            <tr><td style="padding:24px;">
                <h4 style="margin:0 0 8px; font-family:sans-serif; color:#1e3a8a; font-size:0.85rem; text-transform:uppercase; letter-spacing:1.5px;">EXCLUSIVE DISCOUNT</h4>
                <h2 style="margin:0 0 12px; color:#2563eb; font-size:2rem; font-weight:800; font-family:sans-serif;">${dv} OFF</h2>
                <p style="margin:0 0 16px; font-family:sans-serif; color:#1e293b; font-size:0.9rem;">Use this code at checkout:</p>
                <div style="background:#fff; border:1px solid #bfdbfe; border-radius:8px; display:inline-block; padding:10px 28px; font-weight:bold; font-size:1.3rem; font-family:monospace; color:#1e3a8a; letter-spacing:3px;">${code}</div>
            </td></tr>
        </table>`;
    blocks.push({ type: 'library_item', content: html, url: '{{site_url}}' });
    renderCanvas();
    selectBlock(blocks.length - 1);
}

function insertBlogBlock(title, url, image) {
    pushUndo();
    const html = `
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; background:#ffffff; margin:8px 0;">
            <tr><td><img src="${image}" style="width:100%; max-height:200px; object-fit:cover; display:block;"></td></tr>
            <tr><td style="padding:16px;">
                <h3 style="margin:0 0 8px; font-family:sans-serif; color:#1e293b; font-size:1.1rem; line-height:1.4;">${title}</h3>
                <a href="${url}" style="color:#2563eb; font-weight:bold; text-decoration:none; font-size:0.88rem;">Read Post &rarr;</a>
            </td></tr>
        </table>`;
    blocks.push({ type: 'library_item', content: html, url });
    renderCanvas();
    selectBlock(blocks.length - 1);
}

// ========================
// UI HELPERS
// ========================
function switchTab(tab, btn) {
    document.querySelectorAll('.eb-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.eb-tab-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tabPanel' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.add('active');
}

function setAlignActive(btn) {
    btn.closest('.eb-align-group').querySelectorAll('.eb-align-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function setViewport(mode, btn) {
    const vp = document.getElementById('emailViewport');
    vp.style.width = mode === 'mobile' ? '390px' : '600px';
    document.querySelectorAll('.eb-vt-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
}

function setPreviewViewport(mode, btn) {
    const ifr = document.getElementById('previewIframe');
    ifr.style.width = mode === 'mobile' ? '390px' : '700px';
    document.querySelectorAll('#previewModal .eb-vt-btn').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    // Resize iframe height
    try { ifr.style.height = ifr.contentDocument.body.scrollHeight + 'px'; } catch(e) {}
}

function toggleAcc(btn, bodyId) {
    btn.classList.toggle('open');
    const body = document.getElementById(bodyId);
    body.classList.toggle('open');
}

function setModalLinkType(type, btn) {
    btn.closest('.eb-link-type').querySelectorAll('.eb-link-type-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const labels = { url: 'Link URL', email: 'Email Address', phone: 'Phone Number' };
    document.getElementById('modalLinkLabel').textContent = labels[type];
}

function applyModalLink() {
    const val = document.getElementById('modalLinkInput').value;
    const newTab = document.getElementById('modalLinkNewTab').checked;
    if (selectedIdx !== null) {
        blocks[selectedIdx].url = val;
        blocks[selectedIdx].linkNewTab = newTab;
        document.getElementById('panelLinkInput').value = val;
        renderCanvas();
    }
    bootstrap.Modal.getInstance(document.getElementById('linkModal')).hide();
}

// ========================
// PREVIEW
// ========================
function openPreview() {
    const html = compileHtml();
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
    document.getElementById('previewModal').addEventListener('shown.bs.modal', () => {
        const ifr = document.getElementById('previewIframe');
        ifr.srcdoc = `<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width"></head><body style="margin:0;padding:0;background:${globalSettings.bgColor};">${html}</body></html>`;
        setTimeout(() => {
            try { ifr.style.height = ifr.contentDocument.body.scrollHeight + 40 + 'px'; } catch(e) { ifr.style.height = '600px'; }
        }, 300);
    }, { once: true });
}

// ========================
// COMPILE HTML
// ========================
function compileHtml() {
    let out = `<div style="background-color:${globalSettings.bgColor}; padding:32px 0; font-family:${globalSettings.fontFamily}; margin:0;">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="max-width:${globalSettings.maxWidth}px; margin:0 auto; background-color:${globalSettings.cardBg}; border-radius:16px; border:1px solid #E2E8F0; overflow:hidden; box-shadow:0 4px 20px rgba(15,23,42,0.06);">
        <tr><td style="padding:0;">`;

    blocks.forEach(block => {
        const s = block.styles || {};
        const pad = s.padding || '16px 24px';
        const blockBg = s.blockBg || 'transparent';
        const textColor = s.textColor || '#334155';
        const textAlign = s.textAlign || 'left';
        const fontSize = s.fontSize ? s.fontSize + 'px' : '15px';
        const blockRadius = s.blockRadius ? s.blockRadius + 'px' : '0';

        if (block.type === 'header') {
            out += `<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr><td style="padding:${pad}; background-color:${blockBg||'#EFF6FF'}; border-radius:${blockRadius}; text-align:${textAlign}; color:${textColor}; font-size:${fontSize}; font-weight:bold; line-height:1.4;">
                    ${block.content}
                </td></tr></table>`;
        } else if (block.type === 'text') {
            out += `<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr><td style="padding:${pad}; font-size:${fontSize}; color:${textColor}; line-height:1.7; text-align:${textAlign}; background-color:${blockBg}; border-radius:${blockRadius};">
                    ${block.content}
                </td></tr></table>`;
        } else if (block.type === 'button') {
            const btnBg = s.btnBg || '#2563EB';
            const btnText = s.btnText || '#ffffff';
            const btnRadius = s.btnRadius !== undefined ? s.btnRadius : 8;
            const btnAlign = s.btnAlign || 'center';
            const btnFull = s.btnFull ? 'width:100%; display:block; box-sizing:border-box;' : 'display:inline-block;';
            out += `<table width="100%" border="0" cellpadding="0" cellspacing="0" style="background:${blockBg};">
                <tr><td align="${btnAlign}" style="padding:${pad};">
                    <a href="${block.url || '#'}" target="${block.linkNewTab !== false ? '_blank' : '_self'}"
                       style="background-color:${btnBg}; color:${btnText}; padding:14px 30px; text-decoration:none; border-radius:${btnRadius}px; font-weight:bold; font-size:${fontSize}; ${btnFull} box-shadow:0 4px 12px rgba(37,99,235,0.2);">
                        ${block.content || 'Click Here'}
                    </a>
                </td></tr></table>`;
        } else if (block.type === 'divider') {
            out += `<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr><td style="padding:4px 0;"><hr style="border:none; border-top:${block.dividerThick||1}px solid ${block.dividerColor||'#E2E8F0'}; margin:0;"></td></tr></table>`;
        } else if (block.type === 'image') {
            const w = block.imgWidth || '100%';
            out += `<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr><td align="center" style="padding:${pad}; background:${blockBg};">
                    <a href="${block.url || '#'}" target="${block.linkNewTab !== false ? '_blank' : '_self'}">
                        <img src="${block.image_url || 'https://placehold.co/600x200'}" alt="${block.alt || ''}" style="max-width:${w}; height:auto; display:block; margin:0 auto; border-radius:${blockRadius};">
                    </a>
                </td></tr></table>`;
        } else if (block.type === 'video') {
            out += `<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr><td align="center" style="padding:${pad}; background:#000; border-radius:${blockRadius};">
                    <a href="${block.url || '#'}" target="_blank" style="display:inline-block; position:relative;">
                        <img src="${block.image_url || 'https://placehold.co/600x300'}" style="max-width:100%; display:block; border-radius:${blockRadius};" alt="Play Video">
                    </a>
                </td></tr></table>`;
        } else if (block.type === 'social') {
            const sc = block.socials || {};
            const is = block.iconStyle || 'color';
            const socDefs = [
                { key: 'facebook', label: 'Facebook', colorImg: 'https://i.imgur.com/vHq06vW.png' },
                { key: 'twitter', label: 'Twitter', colorImg: 'https://i.imgur.com/bKx2S3z.png' },
                { key: 'instagram', label: 'Instagram', colorImg: 'https://i.imgur.com/tP0aYJ7.png' },
                { key: 'linkedin', label: 'LinkedIn', colorImg: 'https://i.imgur.com/0i9lFhA.png' },
            ];
            let iconsCompiled = socDefs.map(sd => {
                const url = sc[sd.key] || '{{site_url}}';
                return `<a href="${url}" style="margin:0 8px; text-decoration:none;" target="_blank"><img src="${sd.colorImg}" width="28" height="28" alt="${sd.label}" style="display:inline-block;"></a>`;
            }).join('');
            out += `<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr><td align="center" style="padding:${pad}; background:${blockBg};">${iconsCompiled}</td></tr></table>`;
        } else if (block.type === 'spacer') {
            const h = block.height || 30;
            out += `<table width="100%" border="0" cellpadding="0" cellspacing="0" style="height:${h}px; line-height:${h}px; font-size:1px;"><tr><td>&nbsp;</td></tr></table>`;
        } else if (block.type === 'footer') {
            out += `<table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr><td align="${textAlign}" style="padding:${pad}; font-size:${fontSize}; color:${textColor}; line-height:1.6; border-top:1px solid #E2E8F0; background:${blockBg||'#F8FAFC'};">
                    ${block.content}
                </td></tr></table>`;
        } else if (block.type === 'library_item') {
            out += block.content;
        }
    });

    out += `</td></tr></table></div>`;
    return out;
}

// ========================
// SAVE
// ========================
function saveTemplateDesign() {
    const html = compileHtml();
    const saveBtn = document.querySelector('.eb-btn-success');
    saveBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';
    saveBtn.disabled = true;

    // Secure modern Unicode base64 encoding to bypass server WAF / mod_security
    const payloadObj = {
        json: blocks,
        html: html,
        global_settings: globalSettings
    };
    const jsonStr = JSON.stringify(payloadObj);
    
    let base64Data = '';
    try {
        base64Data = btoa(new Uint8Array(new TextEncoder().encode(jsonStr)).reduce((data, byte) => data + String.fromCharCode(byte), ''));
    } catch(e) {
        console.error('Base64 encoding failed, fallback to deprecated unescape:', e);
        base64Data = btoa(unescape(encodeURIComponent(jsonStr)));
    }

    fetch('<?= url("admin/marketing/templates/save-builder/" . $template["id"]) ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            data: base64Data
        })
    })
    .then(r => r.text().then(text => ({ ok: r.ok, status: r.status, text })))
    .then(res => {
        if (!res.ok) {
            throw new Error('HTTP ' + res.status + ': ' + (res.text || 'No response body'));
        }
        let data;
        try {
            data = JSON.parse(res.text);
        } catch(e) {
            throw new Error('Invalid JSON response: ' + res.text.substring(0, 150));
        }
        if (data.success) {
            showToast('<i class="bi bi-check-circle-fill me-1"></i> Template saved successfully!', 'success');
            isDirty = false;
        } else {
            showToast('Error: ' + (data.error || 'Save failed'), 'error');
        }
    })
    .catch(err => {
        console.error('Save template design error:', err);
        showToast('Save failed: ' + err.message, 'error');
    })
    .finally(() => {
        saveBtn.innerHTML = '<i class="bi bi-save2-fill"></i> Save Template';
        saveBtn.disabled = false;
    });
}

// ========================
// TOAST
// ========================
function showToast(msg, type = 'success') {
    const toast = document.getElementById('ebToast');
    const icon = document.getElementById('ebToastIcon');
    const msgEl = document.getElementById('ebToastMsg');
    icon.className = type === 'success' ? 'bi bi-check-circle-fill' : 'bi bi-x-circle-fill';
    icon.style.color = type === 'success' ? '#10b981' : '#ef4444';
    msgEl.innerHTML = msg;
    toast.className = 'eb-toast show ' + type;
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => toast.classList.remove('show'), 3500);
}

// Warn on unsaved changes
window.addEventListener('beforeunload', e => {
    if (isDirty) { e.preventDefault(); e.returnValue = ''; }
});
</script>
