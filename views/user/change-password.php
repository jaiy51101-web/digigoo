<!-- Change Password Page -->
<section class="dc-section" style="padding-top:30px;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3"><?php include VIEWS_PATH . '/user/sidebar.php'; ?></div>
            <div class="col-lg-9">
                <h4 class="mb-4">Change Password</h4>
                <div class="dc-card" style="max-width:500px;">
                    <form method="POST" action="<?= url('user/change-password') ?>">
                        <?= csrfField() ?>
                        <div class="mb-3">
                            <label class="dc-form-label">Current Password</label>
                            <input type="password" name="current_password" class="dc-form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="dc-form-label">New Password</label>
                            <input type="password" name="new_password" class="dc-form-control" placeholder="Min 8 characters" required minlength="8">
                        </div>
                        <div class="mb-3">
                            <label class="dc-form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="dc-form-control" required>
                        </div>
                        <button type="submit" class="btn-dc-primary"><i class="bi bi-key me-2"></i>Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
