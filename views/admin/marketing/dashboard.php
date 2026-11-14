<?php
/**
 * DigiWS - CRM Audience Management Dashboard
 */
$s = $stats ?? [];
$selectedCategory = $_GET['category'] ?? '';
?>

<style>
    .crm-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        background: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f1f5f9;
    }
    .crm-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(124, 58, 237, 0.1);
    }
    .crm-card-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 1.35rem;
    }
    .crm-badge-category {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .crm-badge-subscriber { background-color: #fffbeb; color: #b45309; }
    .crm-badge-active_user { background-color: #eff6ff; color: #2563eb; }
    .crm-badge-interested_user { background-color: #f5f3ff; color: #7c3aed; }
    .crm-badge-exclusive_user { background-color: #ecfdf5; color: #10b981; }
    .crm-status-active { background-color: #d1fae5; color: #065f46; }
    .crm-status-unsubscribed { background-color: #f3f4f6; color: #374151; }
    .crm-status-suspended { background-color: #fef3c7; color: #92400e; }
    .crm-status-banned { background-color: #fee2e2; color: #991b1b; }
    
    .category-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .category-tab {
        padding: 8px 16px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        font-size: 0.85rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .category-tab:hover {
        background: #f1f5f9;
        border-color: #7c3aed;
        color: #7c3aed;
    }
    .category-tab.active {
        background: #7c3aed;
        color: #ffffff;
        border-color: #7c3aed;
    }
    .email-list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }
    .email-list-item:hover {
        background: #f8fafc;
    }
    .email-list-item:last-child {
        border-bottom: none;
    }
    .btn-send-email {
        padding: 5px 12px;
        border-radius: 8px;
        border: 1px solid #dbeafe;
        background: #ffffff;
        color: #2563eb;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-send-email:hover {
        background: #eff6ff;
        border-color: #2563eb;
    }
    .btn-delete-email {
        padding: 5px 12px;
        border-radius: 8px;
        border: 1px solid #fee2e2;
        background: #ffffff;
        color: #dc2626;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-delete-email:hover {
        background: #fef2f2;
        border-color: #dc2626;
    }
</style>

<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h4 class="mb-1 fw-bold text-dark">CRM Audience & Subscriber Dashboard</h4>
        <p class="text-muted mb-0" style="font-size:0.875rem;">Unified email database, category management, and subscriber control.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/marketing/sync') ?>" class="btn btn-outline-primary btn-sm fw-bold">
            <i class="bi bi-arrow-repeat me-1"></i>Re-Sync Database
        </a>
    </div>
</div>

<!-- CRM 4 Cards Statistics -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="crm-card p-3 d-flex align-items-center gap-3">
            <div class="crm-card-icon bg-primary-subtle text-primary">
                <i class="bi bi-envelope-at-fill"></i>
            </div>
            <div>
                <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.7rem;">Total Emails</small>
                <h4 class="mb-0 fw-bold"><?= number_format($s['total_emails']) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="crm-card p-3 d-flex align-items-center gap-3">
            <div class="crm-card-icon bg-warning-subtle text-warning">
                <i class="bi bi-chat-heart-fill"></i>
            </div>
            <div>
                <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.7rem;">Subscribers</small>
                <h4 class="mb-0 fw-bold text-warning"><?= number_format($s['subscribers']) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="crm-card p-3 d-flex align-items-center gap-3">
            <div class="crm-card-icon bg-primary-subtle text-primary">
                <i class="bi bi-person-workspace"></i>
            </div>
            <div>
                <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.7rem;">Active Users</small>
                <h4 class="mb-0 fw-bold text-primary"><?= number_format($s['active_users']) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="crm-card p-3 d-flex align-items-center gap-3">
            <div class="crm-card-icon bg-success-subtle text-success">
                <i class="bi bi-gem"></i>
            </div>
            <div>
                <small class="text-muted d-block text-uppercase fw-bold" style="font-size:0.7rem;">Exclusive Users</small>
                <h4 class="mb-0 fw-bold text-success"><?= number_format($s['exclusive_users']) ?></h4>
            </div>
        </div>
    </div>
</div>

<!-- Email Database Section -->
<div class="card border-0 rounded-4 shadow-sm">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0 fw-bold text-dark">
            <i class="bi bi-database me-2 text-primary"></i>All Emails Database
        </h5>
        <span class="text-muted small" id="emailCount">Loading...</span>
    </div>
    
    <div class="card-body">
        <!-- Category Filter Tabs -->
        <div class="category-tabs">
            <a href="?category=" class="category-tab <?= empty($selectedCategory) ? 'active' : '' ?>">
                All Emails
            </a>
            <a href="?category=subscriber" class="category-tab <?= $selectedCategory === 'subscriber' ? 'active' : '' ?>">
                Subscribers
            </a>
            <a href="?category=active_user" class="category-tab <?= $selectedCategory === 'active_user' ? 'active' : '' ?>">
                Active Users
            </a>
            <a href="?category=interested_user" class="category-tab <?= $selectedCategory === 'interested_user' ? 'active' : '' ?>">
                Interested Users
            </a>
            <a href="?category=exclusive_user" class="category-tab <?= $selectedCategory === 'exclusive_user' ? 'active' : '' ?>">
                Exclusive Users
            </a>
        </div>

        <!-- Email List -->
        <div class="border rounded-3 overflow-hidden" id="emailListContainer">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                    <thead class="table-light">
                        <tr>
                            <th width="40%">Email Address</th>
                            <th width="20%">Name</th>
                            <th width="15%">Category</th>
                            <th width="15%">Status</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody id="emailListBody">
                        <?php
                        $db = Database::getInstance();
                        $where = ["ms.email LIKE '%@%.%'"];
                        $params = [];
                        
                        if (!empty($selectedCategory)) {
                            $where[] = "ms.category = ?";
                            $params[] = $selectedCategory;
                        }
                        
                        $whereSql = implode(' AND ', $where);
                        $emails = $db->fetchAll(
                            "SELECT ms.id, ms.email, ms.name, ms.category, ms.status, ms.created_at 
                             FROM marketing_subscribers ms 
                             WHERE {$whereSql} 
                             ORDER BY ms.created_at DESC",
                            $params
                        );
                        
                        if (empty($emails)):
                        ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 mb-2 d-block text-secondary"></i>
                                No emails found in this category.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($emails as $email): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-envelope text-muted"></i>
                                        <span class="fw-medium"><?= htmlspecialchars($email['email']) ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($email['name'] ?: 'Visitor') ?></td>
                                <td>
                                    <?php
                                    $catClass = 'crm-badge-' . str_replace('_user', '', $email['category']);
                                    if ($email['category'] === 'subscriber') $catClass = 'crm-badge-subscriber';
                                    ?>
                                    <span class="crm-badge-category <?= $catClass ?>">
                                        <?= ucwords(str_replace('_', ' ', $email['category'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="crm-status-<?= $email['status'] ?> crm-badge-status">
                                        <?= ucfirst($email['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn-send-email" 
                                            onclick="sendTestEmail(<?= $email['id'] ?>, '<?= htmlspecialchars($email['email']) ?>', '<?= htmlspecialchars($email['name'] ?: '') ?>')">
                                        <i class="bi bi-send me-1"></i>Send
                                    </button>
                                    <button class="btn-delete-email" 
                                            onclick="deleteEmail(<?= $email['id'] ?>, '<?= htmlspecialchars($email['email']) ?>')">
                                        <i class="bi bi-trash me-1"></i>Delete
                                    </button>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteEmail(id, email) {
    Swal.fire({
        title: "Delete Email?",
        text: `Are you sure you want to delete ${email}? This cannot be undone.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#64748b",
        confirmButtonText: "Yes, Delete",
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= url("admin/marketing/crm/delete") ?>', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "<?= csrfToken() ?>"
                },
                body: JSON.stringify({
                    subscriber_id: id
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire("Deleted", data.message, "success").then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire("Error", data.error || "Failed to delete.", "error");
                }
            })
            .catch(() => {
                Swal.fire("Error", "Server communication failure.", "error");
            });
        }
    });
}

function sendTestEmail(subscriberId, email, name) {
    Swal.fire({
        title: "Send Test Email",
        html: `
            <div class="text-start">
                <label class="form-label">Recipient Email:</label>
                <input type="email" class="form-control" id="swal-recipient-email" placeholder="name@example.com" value="${email}" required>
                <small class="text-muted d-block mt-1">Send a test email to subscriber ${name || 'visitor'}.</small>
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: "Send Test Email",
        cancelButtonText: "Cancel",
        preConfirm: () => {
            const recipientEmail = document.getElementById("swal-recipient-email").value.trim();
            if (!recipientEmail || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(recipientEmail)) {
                Swal.showValidationMessage("Please enter a valid email address.");
                return false;
            }
            return { recipient: recipientEmail };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const formData = new FormData();
            formData.append("subscriber_id", subscriberId);
            formData.append("recipient", result.value.recipient);
            fetch('<?= url("admin/marketing/crm/send-test-email") ?>', {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "<?= csrfToken() ?>"
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire("Sent", data.message, "success").then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire("Error", data.error || "Failed to send email.", "error");
                }
            })
            .catch(() => {
                Swal.fire("Error", "Server communication failure.", "error");
            });
        }
    });
}

// Update email count
document.getElementById("emailCount").textContent = "<?= number_format(count($emails ?? [])) ?> emails";
</script>