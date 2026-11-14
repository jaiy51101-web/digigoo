<?php
/**
 * Digiws - Login Page
 */
if (!defined('BASE_PATH')) exit;
?>

<div class="dc-auth-wrapper">
    <div class="dc-auth-card">
        <h2>Sign In</h2>
        <p class="subtitle">Welcome back to <?= htmlspecialchars(APP_NAME) ?></p>

        <?php if ($flash = getFlash()): ?>
        <div class="alert alert-<?= $flash['type'] ?> mb-4">
            <i class="bi bi-<?= $flash['type']==='danger'?'exclamation-circle':'check-circle' ?>-fill me-2"></i>
            <?= htmlspecialchars($flash['message']) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('login') ?>" class="mt-4">
            <?= csrfField() ?>

            <!-- Email Address -->
            <div class="mb-3">
                <label class="dc-form-label">Email Address</label>
                <div class="position-relative">
                    <input type="email" name="email" class="dc-form-control" placeholder="your@email.com" required autofocus>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="dc-form-label mb-0">Password</label>
                    <a href="<?= url('forgot-password') ?>" class="small fw-bold text-primary">Forgot?</a>
                </div>
                <div class="position-relative dc-password-input">
                    <input type="password" name="password" id="loginPwd" class="dc-form-control" placeholder="••••••••" required>
                    <button type="button" class="btn-toggle-pwd" onclick="togglePwd('loginPwd','loginEye')">
                        <i class="bi bi-eye" id="loginEye"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="mb-4 d-flex align-items-center">
                <input type="checkbox" name="remember" id="remember" class="form-check-input me-2">
                <label for="remember" class="small text-muted mb-0" style="cursor:pointer;">Remember me for 30 days</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-dc-primary w-100 py-3 mb-4">
                <i class="bi bi-box-arrow-in-right me-2"></i> Sign In to Digiws
            </button>
        </form>

        <!-- Divider -->
        <div class="dc-divider mb-3">or continue with</div>

        <!-- Social Logins (1 line, side-by-side, small size) -->
        <div class="d-flex gap-2 mb-4">
            <!-- Google Login -->
            <a href="<?= url('auth/google/redirect') ?>" class="btn-social flex-fill d-inline-flex align-items-center justify-content-center py-2" style="font-size: 0.85rem; padding: 10px 12px;">
                <svg width="16" height="16" viewBox="0 0 24 24" class="me-2">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Google
            </a>

            <!-- Facebook Login -->
            <a href="<?= url('auth/facebook/redirect') ?>" class="btn-social flex-fill d-inline-flex align-items-center justify-content-center py-2" style="font-size: 0.85rem; padding: 10px 12px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="#1877F2" class="me-2">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Facebook
            </a>
        </div>

        <p class="text-center mb-0" style="font-size:0.95rem; color:var(--text-muted);">
            Don't have an account? <a href="<?= url('register') ?>" class="fw-bold text-primary">Create free</a>
        </p>
    </div>
</div>

<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
