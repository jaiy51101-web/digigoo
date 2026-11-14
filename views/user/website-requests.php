<section class="dc-section" style="padding-top:30px;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <?php include VIEWS_PATH . '/user/sidebar.php'; ?>
            </div>
            <div class="col-lg-9">
        <!-- Premium Form Card -->
        <div class="dc-card border-0 shadow-sm" style="border-radius:24px;">
            <div class="card-header-dc d-flex justify-content-between align-items-center" style="border-bottom: 1px solid var(--border-color); padding: 25px 30px;">
                <h5 class="mb-0 fw-800"><i class="bi bi-rocket-takeoff me-2 text-purple"></i>Build Your Vision</h5>
                <span class="badge bg-purple-soft text-purple px-3 py-2 rounded-pill" style="font-size:0.75rem;">Premium Service</span>
            </div>
            <div class="card-body-dc" style="padding: 30px;">
                <p class="text-muted mb-4" style="line-height:1.6;">Describe your project requirements below. Our elite engineering team will analyze your concept and provide a tailored proposal within 24 hours.</p>

                <form id="websiteRequestForm" class="premium-form-dashboard row g-4">
                    <?= csrfField() ?>
                    <div class="col-md-12">
                        <label class="form-label-dashboard">Project Category</label>
                        <div class="dashboard-input-wrapper">
                            <i class="bi bi-grid-1x2"></i>
                            <select name="website_type" class="form-select border-0 bg-transparent" required style="box-shadow:none;">
                                <option value="">Choose Category...</option>
                                <option value="E-commerce Store">E-commerce Marketplace</option>
                                <option value="SaaS Platform">SaaS Application</option>
                                <option value="Blog / News Portal">Digital Media / Blog</option>
                                <option value="Educational Portal">E-Learning Platform</option>
                                <option value="Business Portfolio">Corporate Portfolio</option>
                                <option value="Other">Custom Innovation</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label-dashboard">Target Budget (Optional)</label>
                        <div class="dashboard-input-wrapper">
                            <i class="bi bi-currency-rupee"></i>
                            <select name="budget" class="form-select border-0 bg-transparent" style="box-shadow:none;">
                                <option value="">Select Range...</option>
                                <option value="₹5,000 - ₹15,000">₹5,000 - ₹15,000</option>
                                <option value="₹15,000 - ₹50,000">₹15,000 - ₹50,000</option>
                                <option value="₹50,000 - ₹1,00,000">₹50,000 - ₹1,00,000</option>
                                <option value="Above ₹1,00,000">Above ₹1,00,000</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label-dashboard">Requirement Details</label>
                        <div class="dashboard-textarea-wrapper">
                            <textarea name="description" class="form-control border-0 bg-transparent" rows="5" placeholder="Explain your business logic, key features, and any specific design preferences..." required style="box-shadow:none; resize:none;"></textarea>
                        </div>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn-dc-primary w-100 py-3 rounded-4 shadow-accent" style="height:55px; font-weight:700;">
                            <i class="bi bi-send-check me-2"></i>Analyze & Submit Concept
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- History Card -->
        <div class="dc-card mt-5 border-0 shadow-sm" style="border-radius:24px; overflow:hidden;">
            <div class="card-header-dc d-flex align-items-center gap-2" style="background: #fcfcfc; border-bottom: 1px solid var(--border-color); padding: 20px 30px;">
                <h5 class="mb-0 fw-800"><i class="bi bi-clock-history text-muted"></i> Request History</h5>
            </div>
            <div class="card-body-dc p-0">
                <div class="table-responsive">
                    <table class="dc-table">
                        <thead>
                            <tr>
                                <th>Solution Type</th>
                                <th>Budget</th>
                                <th>Status</th>
                                <th class="text-end">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($requests)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-3"><i class="bi bi-inbox" style="font-size:2.5rem;"></i></div>
                                        <p class="text-muted mb-0">You haven't submitted any custom requests yet.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($requests as $req): ?>
                                    <tr style="cursor:pointer;">
                                        <td>
                                            <div class="fw-700 text-dark"><?= htmlspecialchars($req['website_type']) ?></div>
                                            <div class="text-truncate text-muted" style="max-width: 240px; font-size:0.8rem;"><?= htmlspecialchars($req['description']) ?></div>
                                        </td>
                                        <td class="fw-600"><?= htmlspecialchars($req['budget'] ?: '—') ?></td>
                                        <td>
                                            <?php if ($req['status'] === 'pending'): ?>
                                                <span class="badge rounded-pill" style="background: rgba(245,158,11,0.1); color:#d97706; padding: 6px 12px;">Under Review</span>
                                            <?php elseif ($req['status'] === 'contacted'): ?>
                                                <span class="badge rounded-pill" style="background: rgba(59,130,246,0.1); color:#2563eb; padding: 6px 12px;">Active Conversation</span>
                                            <?php else: ?>
                                                <span class="badge rounded-pill" style="background: rgba(16,185,129,0.1); color:#059669; padding: 6px 12px;">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end text-muted fw-500" style="font-size:0.85rem;">
                                            <?= date('M d, Y', strtotime($req['created_at'])) ?>
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
</section>

<style>
.fw-800 { font-weight: 800; }
.fw-700 { font-weight: 700; }
.fw-600 { font-weight: 600; }
.fw-500 { font-weight: 500; }
.text-purple { color: #8b5cf6 !important; }
.bg-purple-soft { background: rgba(139, 92, 246, 0.1); }
.shadow-accent { box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.4); }

.form-label-dashboard {
    display: block;
    font-size: 0.8rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    margin-bottom: 10px;
}

.dashboard-input-wrapper {
    background: #f8faff;
    border: 1px solid #eef1f6;
    border-radius: 12px;
    padding: 0 15px;
    display: flex;
    align-items: center;
    transition: 0.3s ease;
}
.dashboard-input-wrapper i { color: #8b5cf6; font-size: 1.1rem; margin-right: 12px; }
.dashboard-input-wrapper:focus-within {
    border-color: #8b5cf6;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
}

.dashboard-textarea-wrapper {
    background: #f8faff;
    border: 1px solid #eef1f6;
    border-radius: 12px;
    padding: 10px;
    transition: 0.3s ease;
}
.dashboard-textarea-wrapper:focus-within {
    border-color: #8b5cf6;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
}
</style>

<script>
document.getElementById('websiteRequestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';

    const formData = new FormData(this);
    
    fetch(window.appUrl('website-request/store'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Submitted!',
                text: data.message,
                confirmButtonColor: '#8b5cf6'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: data.message
            });
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
});
</script>
