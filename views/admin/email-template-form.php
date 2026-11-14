<!-- Admin Email Template Edit Form -->
<div class="mb-4 d-flex align-items-center gap-2">
    <a href="<?= url('admin/email-templates') ?>" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width:36px; height:36px;"><i class="bi bi-arrow-left"></i></a>
    <span class="text-muted small fw-600">Email Configuration</span>
</div>

<div class="card border-0 shadow-sm" style="border-radius:24px;">
    <div class="card-header bg-white py-4 px-4" style="border-bottom: 1px solid #f1f3f5; border-radius: 24px 24px 0 0;">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-purple-soft text-purple rounded-4 d-flex align-items-center justify-content-center" style="width:50px; height:50px; font-size:1.5rem;">
                <i class="bi bi-feather"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-800">Edit Template</h5>
                <p class="text-muted small mb-0"><?= htmlspecialchars($template['name']) ?></p>
            </div>
        </div>
    </div>
    <div class="card-body p-4 p-md-5">
        <form method="POST" action="<?= url('admin/email-templates/update/' . $template['id']) ?>" class="premium-admin-form">
            <?= csrfField() ?>
            
            <div class="form-group mb-5">
                <label class="form-label-dc fw-800 text-uppercase small text-muted mb-3 d-block" style="letter-spacing:1px;">Email Subject Line</label>
                <div class="input-group-premium">
                    <i class="bi bi-type-h1 text-purple"></i>
                    <input type="text" name="subject" class="form-control border-0 bg-transparent" value="<?= htmlspecialchars($template['subject']) ?>" required style="box-shadow:none; padding:12px 15px; font-weight:600;">
                </div>
            </div>

            <div class="form-group mb-5">
                <label class="form-label-dc fw-800 text-uppercase small text-muted mb-3 d-block" style="letter-spacing:1px;">Template Content (HTML Ready)</label>
                <div class="textarea-premium" style="background: #fafafa; border: 1px solid #eee; border-radius:20px; padding:20px;">
                    <textarea name="body" class="form-control border-0 bg-transparent" rows="18" required id="emailBody" style="box-shadow:none; font-family:'Courier New', monospace; line-height:1.6;"><?= htmlspecialchars($template['body']) ?></textarea>
                </div>
                
                <div class="placeholder-box mt-4 p-4 rounded-4" style="background:#f8faff; border: 1px dashed rgba(139, 92, 246, 0.3);">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="bi bi-lightning-charge-fill text-purple"></i>
                        <span class="fw-800 small text-uppercase" style="letter-spacing:1px; color:#475569;">Smart Placeholders</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <code class="tag-placeholder">{site_name}</code>
                        <code class="tag-placeholder">{customer_name}</code>
                        <code class="tag-placeholder">{order_number}</code>
                        <code class="tag-placeholder">{total}</code>
                        <code class="tag-placeholder">{new_status}</code>
                        <code class="tag-placeholder">{reset_link}</code>
                        <code class="tag-placeholder">{order_url}</code>
                    </div>
                    <p class="mt-3 mb-0 text-muted small"><i class="bi bi-info-circle me-1"></i> You can use either <code>{tag}</code> or <code>{{tag}}</code> format.</p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3 pt-3">
                <button type="submit" class="btn btn-dc-primary px-5 py-3 rounded-pill fw-bold shadow-accent" style="height:55px;">
                    <i class="bi bi-save2 me-2"></i>Publish Changes
                </button>
                <a href="<?= url('admin/email-templates') ?>" class="btn btn-light px-4 py-3 rounded-pill fw-600" style="height:55px; border:1px solid #eee;">Discard</a>
            </div>
        </form>
    </div>
</div>

<style>
.fw-800 { font-weight: 800; }
.fw-600 { font-weight: 600; }
.text-purple { color: #8b5cf6 !important; }
.bg-purple-soft { background: rgba(139, 92, 246, 0.1); }
.shadow-accent { box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.4); }

.input-group-premium {
    background: #fdfdfd;
    border: 1px solid #eef1f6;
    border-radius: 14px;
    display: flex;
    align-items: center;
    padding: 0 15px;
    transition: 0.3s ease;
}
.input-group-premium:focus-within {
    background: #fff;
    border-color: #8b5cf6;
    box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
}

.tag-placeholder {
    padding: 6px 12px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    color: #8b5cf6 !important;
    font-size: 0.85rem;
    cursor: pointer;
    transition: 0.2s;
}
.tag-placeholder:hover {
    background: #8b5cf6;
    color: #fff !important;
    border-color: #8b5cf6;
}
</style>

<!-- TinyMCE CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        tinymce.init({
            selector: '#emailBody',
            height: 480,
            plugins: 'preview code searchreplace autolink directionality visualblocks visualchars fullscreen image link media codesample table charmap anchor insertdatetime advlist lists wordcount emoticons',
            menubar: 'edit view insert format tools table',
            toolbar: 'undo redo | bold italic underline strikethrough | formatselect | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | charmap emoticons | fullscreen preview | link image media | code',
            toolbar_sticky: true,
            valid_elements: '*[*]',
            extended_valid_elements: '*[*]',
            verify_html: false, // Disable verification to preserve raw HTML tags
            setup: function (editor) {
                // Insert placeholder tag into TinyMCE editor at cursor position on click
                document.querySelectorAll('.tag-placeholder').forEach(function(el) {
                    el.addEventListener('click', function() {
                        editor.insertContent(el.textContent);
                    });
                });
            }
        });
    });
</script>

