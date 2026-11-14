<!-- User Sidebar -->
<?php $user = currentUser(); $currentPath = $_SERVER['REQUEST_URI']; ?>
<div class="dc-dash-sidebar">
    <div class="user-info">
        <div class="user-avatar">
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?= uploadUrl($user['avatar']) ?>" alt="">
            <?php else: ?>
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            <?php endif; ?>
        </div>
        <h6><?= htmlspecialchars($user['name']) ?></h6>
        <small><?= htmlspecialchars($user['email']) ?></small>
    </div>
    <nav>
        <a href="<?= url('user/dashboard') ?>" class="nav-link <?= strpos($currentPath, 'user/dashboard') !== false ? 'active' : '' ?>"><i class="bi bi-grid"></i> Dashboard</a>
        <a href="<?= url('user/orders') ?>" class="nav-link <?= strpos($currentPath, 'user/order') !== false ? 'active' : '' ?>"><i class="bi bi-bag-check"></i> My Orders</a>
        <a href="<?= url('user/downloads') ?>" class="nav-link <?= strpos($currentPath, 'user/download') !== false ? 'active' : '' ?>"><i class="bi bi-download"></i> Downloads</a>
        <a href="<?= url('user/website-requests') ?>" class="nav-link <?= strpos($currentPath, 'user/website-request') !== false ? 'active' : '' ?>"><i class="bi bi-code-slash"></i> Website Request</a>
        <a href="<?= url('user/profile') ?>" class="nav-link <?= strpos($currentPath, 'user/profile') !== false ? 'active' : '' ?>"><i class="bi bi-person-gear"></i> Profile</a>
        <a href="<?= url('user/change-password') ?>" class="nav-link <?= strpos($currentPath, 'change-password') !== false ? 'active' : '' ?>"><i class="bi bi-key"></i> Change Password</a>
        <a href="<?= url('logout') ?>" class="nav-link" style="color:#ef4444;"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>
</div>
