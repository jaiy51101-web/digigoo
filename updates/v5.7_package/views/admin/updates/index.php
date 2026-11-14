<?php
/**
 * System Update Center View - Admin (v5.6)
 * Product ID: PRD-ISV5JQOF7S
 */
?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1 text-gray-800 fw-bold"><i class="bi bi-cloud-arrow-down-fill text-primary me-2"></i>System Update Center</h1>
        <p class="text-muted mb-0">Manage software license verification, remote update downloads, ZIP package installations, and system health.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/updates/check') ?>" class="btn btn-primary rounded-pill px-3 shadow-sm font-weight-bold" id="btnCheckUpdates">
            <i class="bi bi-arrow-repeat me-1"></i> Check For Updates
        </a>
    </div>
</div>

<!-- System Health & Diagnostics Bar -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm rounded-3 p-3 text-center bg-white h-100">
            <small class="text-muted d-block mb-1">PHP Version</small>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold fs-6">
                <?= h($diagnostics['php_version'] ?? PHP_VERSION) ?>
            </span>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm rounded-3 p-3 text-center bg-white h-100">
            <small class="text-muted d-block mb-1">cURL Extension</small>
            <?php if (!empty($diagnostics['curl_enabled'])): ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Enabled</span>
            <?php else: ?>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-bold"><i class="bi bi-x-circle-fill me-1"></i>Disabled</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm rounded-3 p-3 text-center bg-white h-100">
            <small class="text-muted d-block mb-1">ZipArchive Support</small>
            <?php if (!empty($diagnostics['zip_enabled'])): ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Enabled</span>
            <?php else: ?>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-bold"><i class="bi bi-x-circle-fill me-1"></i>Missing</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm rounded-3 p-3 text-center bg-white h-100">
            <small class="text-muted d-block mb-1">Root Writable</small>
            <?php if (!empty($diagnostics['writable'])): ?>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Writable</span>
            <?php else: ?>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i>Read-Only</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm rounded-3 p-3 text-center bg-white h-100">
            <small class="text-muted d-block mb-1">License Server</small>
            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fw-bold">
                <i class="bi bi-globe me-1"></i>Connected
            </span>
        </div>
    </div>
</div>

<?php if (!empty($updateInfo['has_update'])): ?>
    <!-- New Update Banner -->
    <div class="alert alert-info border-info shadow-sm rounded-4 p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold text-info mb-1"><i class="bi bi-arrow-up-circle-fill me-2"></i>New Software Update Available!</h5>
                <p class="mb-1 text-dark">Latest Published Version: <span class="badge bg-primary fs-6 px-3 py-1 me-2">v<?= h($updateInfo['latest_version'] ?? '5.6.1') ?></span> (Current Installed: v<?= h($currentVersion) ?>)</p>
                <?php if (!empty($updateInfo['zip_file_attached'])): ?>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                        <i class="bi bi-file-earmark-zip-fill me-1"></i> Update ZIP Package Ready on Server
                    </span>
                <?php else: ?>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> ZIP File Pending Upload on License Server
                    </span>
                <?php endif; ?>
                <?php if (!empty($updateInfo['release_notes'])): ?>
                    <p class="small text-muted mt-2 mb-0"><strong>Release Notes:</strong> <?= h($updateInfo['release_notes']) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <?php if (!empty($updateInfo['zip_file_attached'])): ?>
                    <a href="<?= url('admin/updates/download-remote') ?>" class="btn btn-success btn-lg rounded-pill px-4 shadow-sm text-nowrap fw-bold" onclick="return confirm('Download and install remote update v<?= h($updateInfo['latest_version']) ?> now? System backup will be created automatically.');">
                        <i class="bi bi-cloud-arrow-down-fill me-2"></i> 1-Click Install Remote Update
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary rounded-pill px-4 text-nowrap" disabled title="Upload ZIP package in License Server Admin Panel first">
                        <i class="bi bi-dash-circle me-1"></i> ZIP Package Not Uploaded Yet
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Up-to-Date Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-light">
        <div class="card-body p-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success text-white p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-check-lg fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1 text-dark">Your System is Fully Up to Date</h5>
                    <p class="text-muted mb-0 small">You are running version <strong class="text-primary">v<?= h($currentVersion) ?></strong>. DigiWS license engine is actively monitoring for new security patches and feature releases.</p>
                </div>
            </div>
            <a href="<?= url('admin/updates/check') ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-repeat me-1"></i> Re-check Now
            </a>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <!-- Version & License Card -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-shield-check text-success me-2"></i>Product & License Info</h5>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">v<?= h($currentVersion) ?></span>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive mb-3">
                    <table class="table table-borderless align-middle mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted ps-0">Product Name:</td>
                                <td class="fw-bold text-end pe-0">Premium Digital Product Selling Website 5.6</td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Installed Version:</td>
                                <td class="fw-bold text-end pe-0"><span class="badge bg-primary fs-6 px-3 py-1">v<?= h($currentVersion) ?></span></td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Unique Product ID:</td>
                                <td class="text-end pe-0"><code><?= h($productId) ?></code></td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Bound Domain:</td>
                                <td class="text-end pe-0"><span class="badge bg-secondary"><?= h($licenseInfo['domain'] ?? ($_SERVER['HTTP_HOST'] ?? 'localhost')) ?></span></td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">License Status:</td>
                                <td class="text-end pe-0">
                                    <?php if (!empty($licenseInfo['valid'])): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">
                                            <i class="bi bi-patch-check-fill me-1"></i> Active & Verified
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= h($licenseInfo['message'] ?? 'License Invalid') ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr class="my-3 text-muted opacity-25">

                <!-- Save/Edit License Key Form -->
                <form action="<?= url('admin/updates/save-license') ?>" method="POST" class="mt-3">
                    <?= csrfField() ?>
                    <label class="form-label fw-semibold text-dark mb-1 small"><i class="bi bi-key-fill text-warning me-1"></i> Issued License Key</label>
                    <div class="input-group">
                        <input type="text" name="license_key" class="form-control font-monospace" value="<?= h($licenseInfo['license_key'] ?? env('LICENSE_KEY', 'MYUA-HOWM-L73Z-1TVH')) ?>" required placeholder="ENTER-YOUR-LICENSE-KEY">
                        <button type="submit" class="btn btn-outline-success font-weight-bold">
                            <i class="bi bi-save me-1"></i> Save Key
                        </button>
                    </div>
                    <small class="text-muted d-block mt-1">Updates license key in local <code>.env</code> file for instant verification.</small>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload Update Package Card -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i>Manual ZIP Package Upload</h5>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div>
                    <p class="text-muted small mb-3">Upload official DigiWS update <code>.zip</code> file to apply bug fixes, feature additions, or core updates manually.</p>
                    
                    <form action="<?= url('admin/updates/upload') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrfField() ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Update Package (.zip)</label>
                            <input type="file" name="update_zip" class="form-control form-control-lg" accept=".zip" required>
                        </div>
                        <div class="alert alert-warning border-0 bg-warning-subtle text-dark p-3 rounded-3 small mb-3">
                            <i class="bi bi-shield-lock-fill text-warning me-2 fs-6"></i>
                            <strong>Automated Safety Backup:</strong> The system will automatically create a Zip snapshot in <code>tmp/backups/</code> before extracting new code.
                        </div>
                        <button type="submit" class="btn btn-success w-100 rounded-3 py-2.5 font-weight-bold shadow-sm" onclick="return confirm('Upload and apply this update package now?');">
                            <i class="bi bi-upload me-1"></i> Upload & Apply Update Package
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Activity History -->
<div class="card border-0 shadow-sm rounded-4 mt-4 mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Update Activity History</h5>
        <span class="badge bg-secondary rounded-pill px-3"><?= count($updateHistory) ?> Logged Events</span>
    </div>
    <div class="card-body p-4">
        <?php if (!empty($updateHistory)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Timestamp</th>
                            <th>Action Type</th>
                            <th>Target Version</th>
                            <th>Status</th>
                            <th>Result Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($updateHistory as $log): ?>
                            <tr>
                                <td class="small text-muted"><?= h($log['timestamp'] ?? '') ?></td>
                                <td class="fw-semibold text-dark"><?= h($log['action'] ?? '') ?></td>
                                <td><span class="badge bg-light text-dark border">v<?= h($log['version'] ?? '5.6') ?></span></td>
                                <td>
                                    <?php if (($log['status'] ?? '') === 'Success'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Success</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Failed</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-secondary"><?= h($log['message'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4 text-muted">
                <i class="bi bi-info-circle fs-3 d-block mb-2 text-secondary"></i>
                No previous update installation logs recorded. Updates applied will appear here automatically.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Release Notes -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-journal-text text-info me-2"></i>Version 5.6 Features & Release Notes</h5>
    </div>
    <div class="card-body p-4">
        <div class="alert alert-light border rounded-3 p-3">
            <h6 class="fw-bold text-dark"><i class="bi bi-check-circle-fill text-success me-2"></i>What's New in Version 5.6:</h6>
            <ul class="mb-0 small text-secondary ps-3">
                <li>Strict Remote License Server Authentication (`MYUA-HOWM-L73Z-1TVH`).</li>
                <li>DigiWS Client SDK (v5.6) with 1-click update download & install.</li>
                <li>ZIP Upload Verification, Automated Pre-Update System Snapshots, & SQL Migration engine.</li>
                <li>Multi-currency digital product checkout & marketing automation tools.</li>
            </ul>
        </div>
    </div>
</div>
