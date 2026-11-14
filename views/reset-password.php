<!-- Reset Password Page -->
<div class="dc-auth-wrapper">
    <div class="dc-auth-card">
        <h2>Reset Password</h2>
        <p class="subtitle">Enter your new password</p>
        <form method="POST" action="<?= url('reset-password') ?>">
            <?= csrfField() ?>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div class="mb-3">
                <label class="dc-form-label">New Password</label>
                <input type="password" name="password" class="dc-form-control" placeholder="Min 8 characters" required minlength="8">
            </div>
            <div class="mb-3">
                <label class="dc-form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="dc-form-control" placeholder="Repeat password" required>
            </div>
            <button type="submit" class="btn-dc-primary w-100 mb-3">Reset Password</button>
        </form>
    </div>
</div>
