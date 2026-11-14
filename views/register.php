<?php
/**
 * Digiws - Register Page
 */
if (!defined('BASE_PATH')) exit;
?>

<div class="dc-auth-wrapper">
    <div class="dc-auth-card" style="max-width: 500px;">
        <h2>Create Account</h2>
        <p class="subtitle">Join India's premium digital marketplace</p>

        <?php if ($flash = getFlash()): ?>
        <div class="alert alert-<?= $flash['type'] ?> mb-4">
            <i class="bi bi-<?= $flash['type']==='danger'?'exclamation-circle':'check-circle' ?>-fill me-2"></i>
            <?= ($flash['message']) ?>
        </div>
        <?php endif; ?>

        <!-- Social Register -->
        <div class="dc-social-login mb-4">
            <a href="<?= url('auth/google/redirect') ?>" class="btn-social w-100">
                <svg width="18" height="18" viewBox="0 0 24 24" class="me-2">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Sign up with Google
            </a>
        </div>

        <div class="dc-divider">or register with email</div>

        <form method="POST" action="<?= url('register') ?>" id="registerForm" class="mt-4" autocomplete="off" novalidate>
            <?= csrfField() ?>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="dc-form-label">Full Name</label>
                    <input type="text" name="name" class="dc-form-control" placeholder="Rahul" required autofocus>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="dc-form-label">Phone (Optional)</label>
                    <div class="input-group">
                        <select name="country_code" class="form-select dc-form-control" style="max-width: 90px; font-size: 0.85rem; border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none;">
                            <option value="91" selected>🇮🇳 +91</option>
                            <option value="1">🇺🇸 +1</option>
                            <option value="44">🇬🇧 +44</option>
                            <option value="971">🇦🇪 +971</option>
                            <option value="62">🇮🇩 +62</option>
                            <option value="880">🇧🇩 +880</option>
                            <option value="92">🇵🇰 +92</option>
                            <option value="977">🇳🇵 +977</option>
                            <option value="60">🇲🇾 +60</option>
                            <option value="65">🇸🇬 +65</option>
                            <option value="61">🇦🇺 +61</option>
                        </select>
                        <input type="tel" name="phone" class="form-control dc-form-control" placeholder="9876543210" style="font-size: 0.85rem; border-top-left-radius: 0; border-bottom-left-radius: 0;">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="dc-form-label">Email Address</label>
                <input type="email" name="email" class="dc-form-control" placeholder="you@example.com" required>
            </div>

            <div class="mb-3">
                <label class="dc-form-label">Password</label>
                <div class="dc-password-input mb-2">
                    <input type="password" name="password" id="regPwd" class="dc-form-control" placeholder="Min 8 chars" required minlength="8" oninput="checkStrength(this.value)">
                    <button type="button" class="btn-toggle-pwd" onclick="togglePwd('regPwd','regEye')">
                        <i class="bi bi-eye" id="regEye"></i>
                    </button>
                </div>
                <!-- Strength Meter -->
                <div style="height:4px; background:#e2e8f0; border-radius:10px; overflow:hidden;">
                    <div id="pwdFill" style="height:100%; width:0%; transition:all 0.4s ease;"></div>
                </div>
                <div id="pwdLabel" style="font-size:0.75rem; font-weight:600; text-align:right; margin-top:5px; color:var(--text-muted);">Enter password</div>
            </div>

            <div class="mb-4">
                <label class="dc-form-label">Confirm Password</label>
                <div class="dc-password-input">
                    <input type="password" name="confirm_password" id="regPwd2" class="dc-form-control" placeholder="Re-enter password" required>
                    <button type="button" class="btn-toggle-pwd" onclick="togglePwd('regPwd2','regEye2')">
                        <i class="bi bi-eye" id="regEye2"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check d-flex gap-2">
                    <input type="checkbox" name="terms" id="terms" class="form-check-input" required style="margin-top:5px; flex-shrink:0;">
                    <label for="terms" class="small text-muted mb-0" style="cursor:pointer; line-height:1.5;">
                        I agree to the <a href="<?= url('page/terms-conditions') ?>" target="_blank" class="fw-bold text-primary">Terms</a> and <a href="<?= url('page/privacy-policy') ?>" target="_blank" class="fw-bold text-primary">Privacy Policy</a>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-dc-primary w-100 py-3 mb-4">
                <i class="bi bi-person-plus-fill me-2"></i> Create My Account
            </button>
        </form>

        <p class="text-center mb-0" style="font-size:0.95rem; color:var(--text-muted);">
            Already have an account? <a href="<?= url('login') ?>" class="fw-bold text-primary">Login here</a>
        </p>
    </div>
</div>

<script>
function togglePwd(inputId, iconId) {
    const inp = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    icon.className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

function checkStrength(val) {
    const fill = document.getElementById('pwdFill');
    const label = document.getElementById('pwdLabel');
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { pct: '0%', color: '#94a3b8', text: 'Enter password' },
        { pct: '25%', color: '#ef4444', text: 'Weak' },
        { pct: '50%', color: '#f59e0b', text: 'Fair' },
        { pct: '75%', color: '#3b82f6', text: 'Good' },
        { pct: '100%', color: '#22c55e', text: '💪 Strong!' },
    ];
    const lvl = levels[score] || levels[0];
    fill.style.width = lvl.pct;
    fill.style.background = lvl.color;
    label.textContent = lvl.text;
    label.style.color = lvl.color;
}
</script>
