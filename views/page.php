<!-- CMS Page Template -->
<section class="dc-section" style="padding-top:40px;">
    <div class="container">
        <div class="dc-card" style="max-width:900px; margin:0 auto; padding: 40px;">
            <h1 class="mb-4" style="font-weight:800;"><?= htmlspecialchars($pageTitle) ?></h1>
            <div class="page-content" style="color:var(--text-secondary);line-height:1.8;">
                <?php
                if (!empty($pageContent)) {
                    // Output the raw CMS driven HTML content (admin controlled)
                    echo $pageContent;
                } else {
                    echo "<p>Content not available.</p>";
                }
                ?>
            </div>
            
            <?php if (strpos($_SERVER['REQUEST_URI'], 'refund-policy') !== false): ?>
                <div class="mt-5 p-4 rounded" style="background:rgba(236, 72, 153, 0.05); border: 1px solid rgba(236, 72, 153, 0.2);">
                    <h5 class="text-pink"><i class="bi bi-info-circle me-2"></i>Digital Products Disclaimer</h5>
                    <p class="mb-0 text-muted" style="font-size:0.9rem;">Due to the nature of digital goods (software, scripts, templates), which are instantly downloaded, we generally do not offer refunds once the download link is accessed. Please review our documentation or live previews carefully before making a purchase. If you experience technical issues, our support team is available 24/7 to resolve them.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
.page-content h2, .page-content h3 { color: var(--text-primary); margin-top: 1.5em; margin-bottom: 0.5em; font-weight: 700; }
.page-content p { margin-bottom: 1.2em; }
.page-content ul { padding-left: 20px; margin-bottom: 1.5em; }
.page-content li { margin-bottom: 8px; }
.page-content a { color: var(--accent-primary); text-decoration: underline; }
.page-content a:hover { color: var(--accent-secondary); }
</style>
