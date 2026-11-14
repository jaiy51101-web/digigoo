<!-- Forgot Password Page -->
<div class="dc-auth-wrapper">
    <div class="dc-auth-card">
        <h2>Forgot Password</h2>
        <p class="subtitle">Enter your email to receive a reset link</p>
        <form method="POST" action="<?= url('forgot-password') ?>">
            <?= csrfField() ?>
            <div class="mb-3">
                <label class="dc-form-label">Email Address</label>
                <input type="email" name="email" class="dc-form-control" placeholder="your@email.com" required autofocus>
            </div>
            <button type="submit" class="btn-dc-primary w-100 mb-3">Send Reset Link</button>
        </form>
        <p class="text-center" style="font-size:0.9rem;color:var(--text-muted);"><a href="<?= url('login') ?>">Back to Login</a></p>
    </div>
</div>
