<!-- Admin Login -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Digiws</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
</head>
<body>
<?php $flash = getFlash(); if ($flash): ?>
<div class="dc-toast-container"><div class="dc-toast <?= $flash['type'] ?>"><i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'x-circle-fill' ?>"></i><span><?= $flash['message'] ?></span></div></div>
<?php endif; ?>

<div class="dc-auth-wrapper">
    <div class="dc-auth-card">
        <div class="text-center mb-4">
            <div style="width:60px;height:60px;border-radius:16px;background:var(--gradient-btn);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <i class="bi bi-shield-lock" style="font-size:1.5rem;color:#fff;"></i>
            </div>
            <h2>Admin Login</h2>
            <p class="subtitle">Digiws Control Panel</p>
        </div>
        <form method="POST" action="<?= url('admin/login') ?>">
            <?= csrfField() ?>
            <div class="mb-3">
                <label class="dc-form-label">Email Address</label>
                <input type="email" name="email" class="dc-form-control" placeholder="admin@digiws.com" required autofocus>
            </div>
            <div class="mb-3">
                <label class="dc-form-label">Password</label>
                <input type="password" name="password" class="dc-form-control" placeholder="Enter password" required>
            </div>
            <button type="submit" class="btn-dc-primary w-100"><i class="bi bi-box-arrow-in-right me-2"></i>Login to Admin</button>
        </form>
        <p class="text-center mt-3" style="font-size:0.82rem;color:var(--text-muted);">
            <a href="<?= url() ?>">← Back to website</a>
        </p>
    </div>
</div>

<script>setTimeout(()=>{document.querySelectorAll('.dc-toast').forEach(t=>{t.style.opacity='0';setTimeout(()=>t.remove(),500);});},4000);</script>
</body>
</html>
