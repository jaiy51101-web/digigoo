<!-- Downloads Page -->
<section class="dc-section" style="padding-top:30px;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3"><?php include VIEWS_PATH . '/user/sidebar.php'; ?></div>
            <div class="col-lg-9">
                <h4 class="mb-4">My Downloads</h4>
                <?php if (empty($downloads)): ?>
                <div class="dc-empty-state"><i class="bi bi-cloud-download"></i><h5>No downloads yet</h5><p>Your purchased products will appear here for download.</p><a href="<?= url('products') ?>" class="btn-dc-primary">Browse Products</a></div>
                <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($downloads as $item): ?>
                    <div class="col-12">
                        <div class="dc-card d-flex gap-3 align-items-center">
                            <img src="<?= $item['thumbnail'] ? uploadUrl($item['thumbnail']) : asset('images/placeholder.png') ?>" style="width:70px;height:50px;border-radius:8px;object-fit:cover;" alt="">
                            <div class="flex-fill">
                                <h6 style="font-size:0.92rem;margin:0 0 3px;"><?= htmlspecialchars($item['product_title']) ?></h6>
                                <small class="text-muted d-block mb-1">Order: <?= $item['order_number'] ?> · Downloads: <?= $item['download_count'] ?></small>
                                <?php if ($item['license_key']): ?><small class="text-muted d-block mb-1">License: <code style="color:var(--accent-primary);"><?= $item['license_key'] ?></code></small><?php endif; ?>
                                
                                <div class="mt-2 d-flex flex-wrap align-items-center gap-2">
                                    <span style="font-size:0.8rem;"><?= getSupportValidityText($item['support_expires_at'] ?? null) ?></span>
                                    
                                    <!-- Renew Support Button Trigger -->
                                    <div class="btn-group">
                                        <button class="btn btn-outline-primary btn-sm py-0 px-2 d-flex align-items-center gap-1 dropdown-toggle" style="font-size:0.72rem; border-radius:6px; height:24px;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-arrow-repeat"></i> Renew
                                        </button>
                                        <ul class="dropdown-menu shadow-sm border" style="font-size:0.8rem;">
                                            <li>
                                                <a class="dropdown-item py-1" href="<?= url('cart/add-support?product_id=' . $item['product_id'] . '&months=6') ?>">
                                                    Extend 6 Months <span class="text-primary fw-semibold ms-2">(+<?= formatPrice($item['support_6_months'] ?? 299) ?>)</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-1" href="<?= url('cart/add-support?product_id=' . $item['product_id'] . '&months=12') ?>">
                                                    Extend 12 Months <span class="text-primary fw-semibold ms-2">(+<?= formatPrice($item['support_12_months'] ?? 499) ?>)</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="ms-auto d-flex flex-column gap-2 align-items-end" style="max-width: 280px;">
                                <?php
                                $downloadList = [];
                                if ($item['download_file']) {
                                    $fileVal = trim($item['download_file']);
                                    $decoded = json_decode($fileVal, true);
                                    if (!is_array($decoded) && $fileVal !== '' && ($fileVal[0] === '[' || $fileVal[0] === '{')) {
                                        $decoded = json_decode(stripslashes($fileVal), true);
                                    }
                                    if (is_array($decoded)) {
                                        foreach ($decoded as $index => $res) {
                                            if (is_array($res)) {
                                                if (isset($res['url'])) {
                                                    $plat = getPlatformDetails($res['platform'] ?? 'other', $index);
                                                    $downloadList[] = [
                                                        'name'  => $plat['text'],
                                                        'icon'  => $plat['icon'],
                                                        'bg'    => $plat['bg'],
                                                        'url'   => url('user/download/' . $item['id'] . '?file_index=' . $index)
                                                    ];
                                                }
                                            } else {
                                                // Backward compatibility string
                                                $isUrl = (strpos((string)$res, 'http') === 0);
                                                $plat = getPlatformDetails($isUrl ? 'other' : 'server', $index);
                                                $downloadList[] = [
                                                    'name'  => $plat['text'],
                                                    'icon'  => $plat['icon'],
                                                    'bg'    => $plat['bg'],
                                                    'url'   => url('user/download/' . $item['id'] . '?file_index=' . $index)
                                                ];
                                            }
                                        }
                                    } else {
                                        // Newline-separated list
                                        $lines = array_filter(explode("\n", str_replace("\r", "", $item['download_file'])));
                                        if (count($lines) > 1) {
                                            foreach ($lines as $index => $res) {
                                                $res = trim($res);
                                                $isUrl = (strpos($res, 'http') === 0);
                                                if (!$isUrl && preg_match('/^[a-zA-Z0-9_-]{15,50}$/', $res)) {
                                                    $plat = getPlatformDetails('gdrive', $index);
                                                    $downloadList[] = [
                                                        'name'  => $plat['text'],
                                                        'icon'  => $plat['icon'],
                                                        'bg'    => $plat['bg'],
                                                        'url'   => 'https://drive.google.com/file/d/' . $res . '/view?usp=sharing'
                                                    ];
                                                } else {
                                                    $plat = getPlatformDetails($isUrl ? 'other' : 'server', $index);
                                                    $downloadList[] = [
                                                        'name'  => $plat['text'],
                                                        'icon'  => $plat['icon'],
                                                        'bg'    => $plat['bg'],
                                                        'url'   => url('user/download/' . $item['id'] . '?file_index=' . $index)
                                                    ];
                                                }
                                            }
                                        } else {
                                            // Single file
                                            $res = trim($item['download_file']);
                                            $isUrl = (strpos($res, 'http') === 0);
                                            if (!$isUrl && preg_match('/^[a-zA-Z0-9_-]{15,50}$/', $res)) {
                                                $plat = getPlatformDetails('gdrive', 0);
                                                $downloadList[] = [
                                                    'name'  => $plat['text'],
                                                    'icon'  => $plat['icon'],
                                                    'bg'    => $plat['bg'],
                                                    'url'   => 'https://drive.google.com/file/d/' . $res . '/view?usp=sharing'
                                                ];
                                            } else {
                                                $plat = getPlatformDetails($isUrl ? 'other' : 'server', 0);
                                                $downloadList[] = [
                                                    'name'  => $plat['text'],
                                                    'icon'  => $plat['icon'],
                                                    'bg'    => $plat['bg'],
                                                    'url'   => url('user/download/' . $item['id'])
                                                ];
                                            }
                                        }
                                    }
                                } elseif ($item['external_url']) {
                                    $plat = getPlatformDetails('other', 0);
                                    $downloadList[] = [
                                        'name'  => 'External Link',
                                        'icon'  => $plat['icon'],
                                        'bg'    => $plat['bg'],
                                        'url'   => url('user/download/' . $item['id'])
                                    ];
                                }
                                ?>
                                <?php foreach ($downloadList as $d): ?>
                                <a href="<?= $d['url'] ?>" class="btn btn-sm d-flex align-items-center gap-2 px-3 py-2 text-white border-0" style="font-size:0.78rem; font-weight: 500; border-radius: 8px; background-color: <?= $d['bg'] ?>; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">
                                    <i class="bi <?= $d['icon'] ?>" style="font-size: 0.95rem;"></i> <?= htmlspecialchars($d['name']) ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
