<!-- Single Product Page -->
<section class="dc-section" style="padding-top:30px;">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4 text-start">
            <ol class="breadcrumb justify-content-start" style="font-size:0.88rem;">
                <li class="breadcrumb-item"><a href="<?= url() ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= url('products') ?>">Products</a></li>
                <?php if (!empty($product['categories'])): ?>
                <li class="breadcrumb-item"><a href="<?= url('products/category/' . $product['categories'][0]['slug']) ?>"><?= htmlspecialchars($product['categories'][0]['name']) ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?= htmlspecialchars(truncateText($product['title'], 40)) ?></li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Image Gallery -->
            <div class="col-lg-6">
                <div class="dc-gallery">
                    <?php
                        // Detect video embed URL helper
                        $videoUrl = $product['video_url'] ?? '';
                        $videoEmbedUrl = '';
                        $videoThumb = '';
                        if ($videoUrl) {
                            // YouTube
                            if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]+)/', $videoUrl, $m)) {
                                $ytId = $m[1];
                                $videoEmbedUrl = 'https://www.youtube.com/embed/' . $ytId . '?autoplay=1&rel=0';
                                $videoThumb = 'https://img.youtube.com/vi/' . $ytId . '/hqdefault.jpg';
                            // Vimeo
                            } elseif (preg_match('/vimeo\.com\/(\d+)/', $videoUrl, $m)) {
                                $vimeoId = $m[1];
                                $videoEmbedUrl = 'https://player.vimeo.com/video/' . $vimeoId . '?autoplay=1';
                                $videoThumb = 'https://vumbnail.com/' . $vimeoId . '.jpg';
                            } else {
                                // Custom/direct video link
                                $videoEmbedUrl = $videoUrl;
                            }
                        }
                    ?>
                    <div class="main-image zoom-container" id="mainImageContainer" onclick="handleMainImageClick(event)">
                        <?php if ($product['thumbnail']): ?>
                            <img src="<?= uploadUrl($product['thumbnail']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" id="mainImg">
                            <div id="magnifier" class="magnifier"></div>
                            
                            <?php if ($product['mrp'] > $product['sale_price']): ?>
                                <div class="card-discount-tag" style="position:absolute; top:12px; left:12px; z-index:10; font-size: 0.85rem; padding:4px 12px; display:inline-block; width:max-content; border-radius:20px; font-weight:800; background:var(--accent-red,#ef4444); color:#fff; box-shadow:0 4px 10px rgba(239,68,68,0.3);">
                                    <?= round((($product['mrp'] - $product['sale_price']) / $product['mrp']) * 100) ?>% OFF
                                </div>
                            <?php endif; ?>


                            <button class="wishlist-btn-pill <?= (new Wishlist())->isInWishlist($product['id']) ? 'active' : '' ?>" 
                                    data-product-id="<?= $product['id'] ?>" 
                                    style="top:12px; right:12px; width:38px; height:38px; font-size:1.1rem; box-shadow: var(--shadow-md);">
                                <i class="bi <?= (new Wishlist())->isInWishlist($product['id']) ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                            </button>

                            <div class="btn-open-full" onclick="openLightbox()" title="View Full Image">
                                <i class="bi bi-fullscreen" style="font-size:0.75rem;"></i>
                            </div>

                            <?php if ($videoEmbedUrl): ?>
                            <!-- Main Image Play Button -->
                            <div class="main-play-overlay" onclick="openVideoModal()" title="Play Video">
                                <div class="play-icon-wrapper">
                                    <i class="bi bi-play-fill"></i>
                                </div>
                            </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center h-100"><i class="bi bi-image" style="font-size:4rem;color:var(--text-muted);"></i></div>
                        <?php endif; ?>
                    </div>

                    <!-- Thumbnail Strip (Images + Video) -->
                    <?php $hasThumbStrip = !empty($product['images']) || $videoEmbedUrl; ?>
                    <?php if ($hasThumbStrip || $product['thumbnail']): ?>
                    <div class="thumb-list">
                        <?php if ($product['thumbnail']): ?>
                        <img src="<?= uploadUrl($product['thumbnail']) ?>" alt="Main" class="active" onclick="changeMainImage(this)">
                        <?php endif; ?>
                        <?php foreach ($product['images'] as $img): ?>
                        <img src="<?= uploadUrl($img['image_path']) ?>" alt="Screenshot" onclick="changeMainImage(this)">
                        <?php endforeach; ?>
                        <?php if ($videoEmbedUrl): ?>
                        <!-- Video thumbnail with play button -->
                        <div class="thumb-video" onclick="openVideoModal()" title="Play Video">
                            <?php if ($videoThumb): ?>
                                <img src="<?= $videoThumb ?>" alt="Video">
                            <?php else: ?>
                                <div class="video-placeholder">
                                    <i class="bi bi-film" style="color:#a78bfa;font-size:1.4rem;"></i>
                                </div>
                            <?php endif; ?>
                            <!-- Play overlay -->
                            <div style="position:absolute;inset:0;background:rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;">
                                <div style="width:26px;height:26px;background:rgba(255,255,255,0.95);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.4);">
                                    <i class="bi bi-play-fill" style="color:#7c3aed;font-size:0.85rem;margin-left:2px;"></i>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="dc-product-info text-start">
                    <!-- Labels -->
                    <div class="d-flex gap-2 mb-3 flex-wrap justify-content-start">
                        <?php if ($product['is_featured']): ?><span class="card-badge badge-featured">Featured</span><?php endif; ?>
                        <?php if ($product['is_bestseller']): ?><span class="card-badge badge-bestseller">Bestseller</span><?php endif; ?>
                        <?php if ($product['is_new']): ?><span class="card-badge badge-new">New</span><?php endif; ?>
                        <?php if ($product['is_exclusive']): ?><span class="card-badge badge-exclusive">Exclusive</span><?php endif; ?>
                    </div>

                    <h1 class="product-title" style="
                        font-size: 1.45rem;
                        font-weight: 800;
                        line-height: 1.4;
                        display: -webkit-box;
                        -webkit-line-clamp: 2;
                        -webkit-box-orient: vertical;
                        overflow: hidden;
                        max-height: calc(1.45rem * 1.4 * 2);
                        margin-bottom: 10px;
                        letter-spacing: -0.3px;
                        color: var(--text-primary);
                        font-family: var(--font-title);
                    "><?= htmlspecialchars($product['title']) ?></h1>

                    <div class="product-meta justify-content-start">
                        <?php if (!empty($product['categories'])): 
                            $firstCat = reset($product['categories']);
                            ?>
                            <a href="<?= url('products/category/' . $firstCat['slug']) ?>" class="text-decoration-none me-2">
                                <span><i class="bi bi-tag me-1"></i><?= htmlspecialchars($firstCat['name']) ?></span>
                            </a>
                        <?php endif; ?>
                        <span><i class="bi bi-eye me-1"></i><?= number_format(getDynamicFakeViews($product)) ?> views</span>
                        <span><i class="bi bi-bag me-1"></i><?= number_format(getDynamicFakeSales($product)) ?> sales</span>
                        <span class="text-warning">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star<?= $i <= round($product['avg_rating']) ? '-fill' : '' ?>"></i>
                            <?php endfor; ?>
                            <?= number_format((float)($product['avg_rating'] ?? 0), 1) ?>
                            <span class="text-muted">(<?= (int)($product['reviews_count'] ?? 0) ?>)</span>
                        </span>
                    </div>

                    <!-- Price -->
                    <div class="product-price">
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <span class="price-big"><?= formatPrice($product['sale_price']) ?></span>
                            <?php if ($product['mrp'] > $product['sale_price']): ?>
                                <span class="price-mrp"><?= formatPrice($product['mrp']) ?></span>
                                <span class="price-save">Save <?= round((($product['mrp'] - $product['sale_price']) / $product['mrp']) * 100) ?>%</span>
                            <?php endif; ?>
                        </div>
                        <div class="mt-2" style="font-size:0.8rem; color:var(--text-muted); font-weight: 500;">
                            + <?= $product['gst_percent'] ?>% GST (Goods & Services Tax)
                        </div>
                    </div>

                    <!-- Short Description -->
                    <?php if ($product['short_description']): ?>
                    <p class="product-desc"><?= htmlspecialchars($product['short_description']) ?></p>
                    <?php endif; ?>

                    <!-- Variants -->
                    <form id="addToCartForm">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                        <?php if ($product['setup_option']): ?>
                        <div class="dc-variant-group">
                            <div class="group-header" onclick="toggleVariantGroup(this)">
                                <span class="group-title"><i class="bi bi-gear-wide-connected me-2 text-primary"></i>Website Setup Service</span>
                                <span class="group-status-badge badge-free">No Setup</span>
                                <i class="bi bi-chevron-down chevron-icon"></i>
                            </div>
                            <div class="group-body">
                                <div class="dc-variant-cards">
                                    <div class="variant-card selected" onclick="selectVariantCard(this, 'setup_included', 0)">
                                        <div class="card-radio-dot"></div>
                                        <div class="card-icon"><i class="bi bi-x-circle text-muted"></i></div>
                                        <div class="card-content">
                                            <div class="card-title">No Setup</div>
                                            <div class="card-desc">I will set up the website myself</div>
                                        </div>
                                        <div class="card-price">Free</div>
                                    </div>
                                    <div class="variant-card" onclick="selectVariantCard(this, 'setup_included', 1)">
                                        <div class="card-radio-dot"></div>
                                        <div class="card-icon"><i class="bi bi-cloud-check-fill text-success"></i></div>
                                        <div class="card-content">
                                            <div class="card-title">Include Setup</div>
                                            <div class="card-desc">Professional setup on your hosting within 24 hours</div>
                                        </div>
                                        <div class="card-price">+<?= formatPrice($product['setup_price']) ?></div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="setup_included" value="0">
                        </div>
                        <?php endif; ?>

                        <?php if ($product['support_6_months'] > 0 || $product['support_12_months'] > 0): ?>
                        <div class="dc-variant-group">
                            <div class="group-header" onclick="toggleVariantGroup(this)">
                                <span class="group-title"><i class="bi bi-shield-check me-2 text-primary"></i>Support Duration</span>
                                <span class="group-status-badge badge-free">No Support</span>
                                <i class="bi bi-chevron-down chevron-icon"></i>
                            </div>
                            <div class="group-body">
                                <div class="dc-variant-cards">
                                    <div class="variant-card selected" onclick="selectVariantCard(this, 'support_months', 0)">
                                        <div class="card-radio-dot"></div>
                                        <div class="card-icon"><i class="bi bi-chat-left-x text-muted"></i></div>
                                        <div class="card-content">
                                            <div class="card-title">No Support</div>
                                            <div class="card-desc">Community support via forums only</div>
                                        </div>
                                        <div class="card-price">Free</div>
                                    </div>
                                    <?php if ($product['support_6_months'] > 0): ?>
                                    <div class="variant-card" onclick="selectVariantCard(this, 'support_months', 6)">
                                        <div class="card-radio-dot"></div>
                                        <div class="card-icon"><i class="bi bi-headset text-success"></i></div>
                                        <div class="card-content">
                                            <div class="card-title">6 Months Support</div>
                                            <div class="card-desc">Priority ticket & email support from developers</div>
                                        </div>
                                        <div class="card-price">+<?= formatPrice($product['support_6_months']) ?></div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($product['support_12_months'] > 0): ?>
                                    <div class="variant-card position-relative" onclick="selectVariantCard(this, 'support_months', 12)">
                                        <span class="badge-card-popular">Best Value</span>
                                        <div class="card-radio-dot"></div>
                                        <div class="card-icon"><i class="bi bi-patch-check-fill text-warning"></i></div>
                                        <div class="card-content">
                                            <div class="card-title">12 Months Support</div>
                                            <div class="card-desc">24/7 Priority support & free bug fixes for 1 year</div>
                                        </div>
                                        <div class="card-price">+<?= formatPrice($product['support_12_months']) ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <input type="hidden" name="support_months" value="0">
                        </div>
                        <?php endif; ?>

                        <!-- Actions -->
                        <div class="d-flex gap-2 mt-4 flex-wrap">
                            <button type="submit" class="btn-dc-outline flex-fill" onclick="this.form.buy_now.value=0">
                                <i class="bi bi-bag-plus me-1"></i>Add to Cart
                            </button>
                            <button type="submit" class="btn-dc-primary flex-fill" onclick="this.form.buy_now.value=1">
                                <i class="bi bi-lightning-fill me-1"></i>Buy Now
                            </button>
                        </div>
                        <?php if ($product['preview_url']): ?>
                        <div class="mt-2">
                            <a href="<?= htmlspecialchars($product['preview_url']) ?>" class="btn-dc-info d-block w-100 text-center py-2" target="_blank" style="background:rgba(18,183,106,0.08); color:#12b76a; border:1px solid rgba(18,183,106,0.15); border-radius:12px; font-weight:600; text-decoration:none; transition:0.3s; font-size:0.95rem;">
                                <i class="bi bi-eye me-1"></i>Live Preview
                            </a>
                        </div>
                        <?php endif; ?>
                        <input type="hidden" name="buy_now" value="0">
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabs: Description, Features, Reviews -->
        <div class="mt-5">
            <ul class="nav nav-tabs" style="border-color:var(--border-color);">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabDesc" style="color:var(--text-secondary);background:transparent;border-color:transparent;">Description</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabFeatures" style="color:var(--text-secondary);background:transparent;border-color:transparent;">Features</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabReviews" style="color:var(--text-secondary);background:transparent;border-color:transparent;">Reviews (<?= (int)($product['reviews_count'] ?? 0) ?>)</button>
                </li>

            </ul>
            <div class="tab-content mt-3">
                <!-- Description -->
                <div class="tab-pane fade show active" id="tabDesc">
                    <div class="dc-card">
                        <div class="product-desc-full" style="color:var(--text-secondary);line-height:1.9;">
                            <?= $product['description'] ?: '<p>No description available.</p>' ?>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="tab-pane fade" id="tabFeatures">
                    <div class="dc-card">
                        <?php if (!empty($product['features'])): ?>
                        <ul class="dc-features-list">
                            <?php foreach ($product['features'] as $feat): ?>
                            <li><i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($feat['feature']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <p class="text-muted">No features listed.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Reviews -->
                <div class="tab-pane fade" id="tabReviews">
                    <div class="dc-card">
                        <?php
                        // Rating values have already been combined by Product::loadRatings().
                        $managedReviewsList = $managedReviews ?? [];
                        $combinedTotal = (int)($product['reviews_count'] ?? 0);

                        // Compute avg rating including managed reviews
                        $managedRatingSum = 0;
                        $managedRatingDist = [1=>0,2=>0,3=>0,4=>0,5=>0];
                        foreach ($managedReviewsList as $mr) {
                            if ($mr['rating'] === null || $mr['rating'] === '') continue;
                            $mr_r = (float)$mr['rating'];
                            $managedRatingSum += $mr_r;
                            $key = (int)round($mr_r);
                            if (isset($managedRatingDist[$key])) $managedRatingDist[$key]++;
                        }
                        $combinedAvg = (float)($product['avg_rating'] ?? 0);

                        // Combined per-star distribution
                        $combinedDist = [];
                        for ($x = 1; $x <= 5; $x++) {
                            $combinedDist[$x] = ($ratingDist[$x] ?? 0) + ($managedRatingDist[$x] ?? 0);
                        }
                        ?>
                        <!-- Rating Summary -->
                        <div class="row align-items-center mb-4">
                            <div class="col-md-4 text-center">
                                <div style="font-size:3rem;font-weight:900;"><?= number_format($combinedAvg, 1) ?></div>
                                <div class="text-warning mb-1">
                                    <?php
                                    $halfStars = round($combinedAvg * 2) / 2;
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($halfStars >= $i) echo '<i class="bi bi-star-fill"></i>';
                                        elseif ($halfStars >= $i - 0.5) echo '<i class="bi bi-star-half"></i>';
                                        else echo '<i class="bi bi-star"></i>';
                                    }
                                    ?>
                                </div>
                                <small class="text-muted"><?= $combinedTotal ?> review<?= $combinedTotal != 1 ? 's' : '' ?></small>
                            </div>
                            <div class="col-md-8">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <small style="width:16px;"><?= $i ?>★</small>
                                    <div class="progress flex-fill" style="height:6px;background:var(--bg-input);">
                                        <div class="progress-bar" style="width:<?= $combinedTotal > 0 ? round(($combinedDist[$i] / $combinedTotal) * 100) : 0 ?>%;background:var(--accent-orange);"></div>
                                    </div>
                                    <small class="text-muted" style="width:24px;"><?= $combinedDist[$i] ?></small>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Review List -->
                        <?php foreach (($managedReviews ?? []) as $review): ?>
                        <div class="dc-review">
                            <div class="review-header"><div class="avatar"><?= strtoupper(substr($review['reviewer_name'], 0, 1)) ?></div><div><strong><?= htmlspecialchars($review['reviewer_name']) ?></strong><div class="review-stars"><?php for($i=1;$i<=5;$i++): ?><i class="bi bi-star<?= $i <= round((float)$review['rating']) ? '-fill' : '' ?>"></i><?php endfor; ?> <small class="ms-1 text-muted"><?= number_format((float)$review['rating'], 1) ?></small></div></div></div>
                            <p class="review-text mb-0"><?= nl2br(htmlspecialchars($review['message'])) ?></p>
                        </div>
                        <?php endforeach; ?>
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                            <div class="dc-review">
                                <div class="review-header">
                                    <div class="avatar"><?= strtoupper(substr($review['user_name'], 0, 1)) ?></div>
                                    <div>
                                        <strong><?= htmlspecialchars($review['user_name']) ?></strong>
                                        <div class="review-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?= $i <= $review['rating'] ? '-fill' : '' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <span class="review-date ms-auto"><?= timeAgo($review['created_at']) ?></span>
                                </div>
                                <?php if ($review['title']): ?><strong><?= htmlspecialchars($review['title']) ?></strong><?php endif; ?>
                                <p class="review-text"><?= htmlspecialchars($review['comment']) ?></p>
                            </div>
                            <?php endforeach; ?>
                        <?php elseif (empty($managedReviewsList)): ?>
                            <p class="text-muted">No reviews yet. Be the first to review!</p>
                        <?php endif; ?>

                        <!-- Submit Review -->
                        <?php if (isLoggedIn() && !$hasReviewed): ?>
                        <div class="mt-4 pt-3" style="border-top:1px solid var(--border-color);">
                            <h6 class="mb-3">Write a Review</h6>
                            <form method="POST" action="<?= url('review/submit') ?>">
                                <?= csrfField() ?>
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <div class="mb-3">
                                    <label class="dc-form-label">Rating</label>
                                    <div class="d-flex gap-1" id="ratingStars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi bi-star" data-rating="<?= $i ?>" style="font-size:1.5rem;cursor:pointer;color:var(--text-muted);transition:0.2s;" onmouseover="highlightStars(<?= $i ?>)" onmouseout="resetStars()" onclick="setRating(<?= $i ?>)"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <input type="hidden" name="rating" id="ratingInput" value="5" required>
                                </div>
                                <div class="mb-3">
                                    <input type="text" name="title" class="dc-form-control" placeholder="Review Title (optional)">
                                </div>
                                <div class="mb-3">
                                    <textarea name="comment" class="dc-form-control" placeholder="Write your review..." rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn-dc-primary btn-dc-sm">Submit Review</button>
                            </form>
                        </div>
                        <?php elseif (!isLoggedIn()): ?>
                        <div class="mt-3 text-center">
                            <a href="<?= url('login') ?>" class="btn-dc-outline btn-dc-sm">Login to write a review</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>


            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
        <div class="mt-5">
            <h4 class="mb-4">Related Products</h4>
            <div class="row g-4">
                <?php foreach ($relatedProducts as $product): ?>
                <div class="col-6 col-md-3">
                    <?php include VIEWS_PATH . '/partials/product-card.php'; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Image Lightbox Modal -->
<!-- Video Player Modal -->
<?php if (!empty($videoEmbedUrl)): ?>
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background:#000;border:none;border-radius:16px;overflow:hidden;">
            <div class="modal-body p-0 position-relative" style="padding-top:0;">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close" onclick="stopVideo()" style="z-index:10;"></button>
                <div class="ratio ratio-16x9">
                    <iframe id="videoIframe" src="" frameborder="0" allowfullscreen allow="autoplay; encrypted-media" style="border-radius:0;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Image Lightbox Modal -->
<div class="modal fade" id="imageLightbox" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content" style="background:rgba(0,0,0,0.92);border:none;border-radius:16px;">
            <div class="modal-body p-3 text-center position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1060;"></button>
                <img src="" id="lightboxImg" class="img-fluid rounded-3" style="max-height: 90vh; object-fit:contain;">
            </div>
        </div>
    </div>
</div>

<style>
/* Premium Gallery Layout Styling */
.dc-gallery {
    display: flex;
    flex-direction: column;
    gap: 16px;
    width: 100%;
}

/* Main Image Box styling */
.dc-gallery .main-image {
    position: relative;
    width: 100%;
    aspect-ratio: 16/9; /* Fixed product image size to 16:9 ratio */
    background: #f8fafc; /* Clean slate background for letterboxing/pillarboxing */
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
    cursor: zoom-in;
}
.dc-gallery .main-image:hover {
    box-shadow: var(--shadow-md);
}
.dc-gallery .main-image img {
    width: 100%;
    height: 100%;
    object-fit: contain; /* Shows full screenshot/image without cropping */
    display: block;
}

/* Magnifying Glass Lens styling */
.magnifier {
    position: absolute;
    border: 2px solid rgba(255, 255, 255, 0.8);
    border-radius: 50%;
    cursor: none;
    width: 160px;
    height: 160px;
    display: none;
    pointer-events: none;
    z-index: 20;
    box-shadow: 0 5px 25px rgba(0,0,0,0.25), inset 0 0 15px rgba(0,0,0,0.1);
    background-repeat: no-repeat;
    background-color: #fff;
}

/* Horizontal scrollable thumbnails strip */
.thumb-list {
    display: flex;
    flex-direction: row;
    gap: 12px;
    overflow-x: auto;
    padding: 4px 2px 8px 2px;
    margin-top: 4px;
    scrollbar-width: thin;
    scrollbar-color: var(--secondary) transparent;
}
.thumb-list::-webkit-scrollbar {
    height: 5px;
}
.thumb-list::-webkit-scrollbar-track {
    background: transparent;
}
.thumb-list::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 10px;
}
.thumb-list::-webkit-scrollbar-thumb:hover {
    background: var(--text-muted);
}

/* Thumbnails */
.thumb-list img {
    width: 80px;
    height: 60px;
    object-fit: cover;
    border-radius: var(--radius-md);
    cursor: pointer;
    border: 2.5px solid var(--border-color);
    opacity: 0.7;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    flex-shrink: 0;
}
.thumb-list img:hover {
    opacity: 1;
    border-color: var(--text-muted);
    transform: translateY(-2px);
}
.thumb-list img.active {
    opacity: 1;
    border-color: var(--primary);
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
    transform: translateY(-2px);
}

/* Video Thumbnail container */
.thumb-video {
    width: 80px;
    height: 60px;
    flex-shrink: 0;
    border-radius: var(--radius-md) !important;
    border: 2.5px solid var(--border-color) !important;
    overflow: hidden;
    position: relative;
    cursor: pointer;
    opacity: 0.8;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.thumb-video:hover {
    opacity: 1;
    border-color: var(--secondary) !important;
    transform: translateY(-2px) !important;
}
.thumb-video img, .thumb-video .video-placeholder {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.thumb-video .video-placeholder {
    background: linear-gradient(135deg,#1a1a2e,#16213e);
    display: flex;
    align-items: center;
    justify-content: center;
}

.zoom-container { position: relative; overflow:hidden; }
.btn-open-full {
    position: absolute;
    bottom: 10px;
    right: 10px;
    width: 30px;
    height: 30px;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(4px);
    color: #fff;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 15;
    transition: 0.2s;
    border: 1px solid rgba(255,255,255,0.15);
    font-size: 0.75rem;
}
.btn-open-full:hover { background: var(--accent-primary); }

/* Main Image Play Button Overlay */
.main-play-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 12;
    transition: background 0.3s;
}
.main-play-overlay:hover {
    background: rgba(0,0,0,0.05);
}
.main-play-overlay .play-icon-wrapper {
    width: 70px;
    height: 70px;
    background: rgba(0,0,0,0.6);
    border: 2px solid rgba(255,255,255,0.8);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    transition: transform 0.3s, background 0.3s;
    pointer-events: none; /* Let hover trigger on wrapper */
}
.main-play-overlay:hover .play-icon-wrapper {
    transform: scale(1.1);
    background: var(--accent-primary);
    border-color: #fff;
}
.main-play-overlay .play-icon-wrapper i {
    color: #fff;
    font-size: 2.2rem;
    margin-left: 5px;
}

/* Price Section Highlight */
.product-price {
    margin-bottom: 20px !important;
    width: 100% !important;
}

.product-price .price-big {
    font-size: 1.8rem !important; /* 10% smaller */
    font-weight: 850 !important; /* Bold highlight */
    color: var(--primary) !important; /* Bold brand color */
    letter-spacing: -0.5px !important;
    line-height: 1 !important;
}

.product-price .price-mrp {
    font-size: 1.08rem !important; /* 10% smaller */
    font-weight: 600 !important;
    text-decoration: line-through !important;
    color: var(--text-muted) !important;
}

.product-price .price-save {
    font-size: 0.76rem !important; /* 10% smaller */
    font-weight: 800 !important; /* Bold */
    color: #10B981 !important; /* Green text */
    background: #E6FBF3 !important; /* Light green background */
    padding: 3px 8px !important;
    border-radius: var(--radius-full) !important;
    border: 1px solid rgba(16, 185, 129, 0.2) !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
    display: inline-block !important;
}

/* Mobile Responsive Fixes */
@media (max-width: 768px) {
    .dc-gallery .main-image {
        height: auto !important;
        aspect-ratio: 16/9 !important;
        padding: 0 !important;
        background: #f8fafc !important;
    }
    #mainImg {
        width: 100% !important;
        height: 100% !important;
        object-fit: contain !important;
        display: block;
    }
    .thumb-list img, .thumb-video {
        width: 65px !important;
        height: 50px !important;
        border-width: 2px !important;
    }
    .magnifier {
        display: none !important;
    }
    .product-price {
        margin-bottom: 15px !important;
    }
    .product-price .price-big {
        font-size: 1.35rem !important; /* 10% smaller */
    }
    .product-price .price-mrp {
        font-size: 0.9rem !important; /* 10% smaller */
    }
    .product-price .price-save {
        font-size: 0.68rem !important; /* 10% smaller */
        padding: 2px 6px !important;
    }
    .card-discount-tag {
        font-size: 0.72rem !important;
        padding: 3px 8px !important;
        top: 8px !important;
        left: 8px !important;
    }
    .wishlist-btn-pill {
        top: 8px !important;
        right: 8px !important;
        width: 34px !important;
        height: 34px !important;
        font-size: 1rem !important;
    }
    .product-title {
        font-size: 1.4rem !important;
        line-height: 1.3 !important;
        margin-top: 10px;
    }
    .main-play-overlay .play-icon-wrapper {
        width: 50px;
        height: 50px;
    }
    .main-play-overlay .play-icon-wrapper i {
        font-size: 1.5rem;
        margin-left: 3px;
    }
    .dc-product-info p.product-desc {
        font-size: 0.9rem !important;
    }
    .btn-dc-primary, .btn-dc-outline, .btn-dc-info, .btn-dc-dark {
        font-size: 0.9rem !important;
        padding: 10px 12px !important;
    }
    .variant-btn {
        font-size: 0.82rem;
        padding: 6px 10px;
    }
}
</style>

<script>
function changeMainImage(el) {
    const mainImg = document.getElementById('mainImg');
    if (mainImg) {
        mainImg.style.opacity = '0.3';
        setTimeout(() => {
            mainImg.src = el.src;
            mainImg.style.opacity = '1';
        }, 120);
    }
    document.querySelectorAll('.thumb-list img').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
    setTimeout(initZoom, 150);
}

/**
 * Improved Image Zoom (Magnifier) Logic
 */
function initZoom() {
    const container = document.getElementById('mainImageContainer');
    const img = document.getElementById('mainImg');
    const magnifier = document.getElementById('magnifier');

    if (!container || !img || !magnifier) return;

    container.onmousemove = function(e) {
        const rect = img.getBoundingClientRect();
        const containerRect = container.getBoundingClientRect();
        
        // Mouse position relative to the actual image element
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        // Only show zoom when cursor is over the image bounds (ignores empty space for aspect-ratio matching)
        if (x >= 0 && x <= rect.width && y >= 0 && y <= rect.height) {
            magnifier.style.display = "block";
            magnifier.style.backgroundImage = `url('${img.src}')`;
            
            const zoom = 2.5;
            magnifier.style.backgroundSize = `${rect.width * zoom}px ${rect.height * zoom}px`;

            const magWidth = magnifier.offsetWidth / 2;
            const magHeight = magnifier.offsetHeight / 2;

            // Position relative to the parent container
            const containerX = e.clientX - containerRect.left;
            const containerY = e.clientY - containerRect.top;

            magnifier.style.left = `${containerX - magWidth}px`;
            magnifier.style.top = `${containerY - magHeight}px`;

            const posX = (x / rect.width) * 100;
            const posY = (y / rect.height) * 100;
            magnifier.style.backgroundPosition = `${posX}% ${posY}%`;
        } else {
            magnifier.style.display = "none";
        }
    };

    container.onmouseleave = function() {
        magnifier.style.display = "none";
    };
}

document.addEventListener('DOMContentLoaded', initZoom);

function handleMainImageClick(event) {
    if (event.target.closest('.wishlist-btn-pill') || event.target.closest('.main-play-overlay') || event.target.closest('.btn-open-full')) {
        return;
    }
    openLightbox();
}

function openLightbox() {
    const mainImg = document.getElementById('mainImg');
    const lightboxImg = document.getElementById('lightboxImg');
    const modal = new bootstrap.Modal(document.getElementById('imageLightbox'));
    if (mainImg && lightboxImg) {
        lightboxImg.src = mainImg.src;
        modal.show();
    }
}

function openVideoModal() {
    const iframe = document.getElementById('videoIframe');
    const videoModal = document.getElementById('videoModal');
    if (!iframe || !videoModal) return;
    iframe.src = '<?= addslashes($videoEmbedUrl ?? '') ?>';
    const modal = new bootstrap.Modal(videoModal);
    modal.show();
    // Stop video on modal close
    videoModal.addEventListener('hidden.bs.modal', stopVideo, { once: true });
}

function stopVideo() {
    const iframe = document.getElementById('videoIframe');
    if (iframe) iframe.src = '';
}

function selectVariant(btn, name, value) {
    btn.closest('.dc-variant-options').querySelectorAll('.variant-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.querySelector(`input[name="${name}"]`).value = value;
}

function selectVariantCard(card, name, value) {
    const cardsContainer = card.closest('.dc-variant-cards');
    cardsContainer.querySelectorAll('.variant-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    document.querySelector(`input[name="${name}"]`).value = value;

    // Update status badge on parent group header
    const group = card.closest('.dc-variant-group');
    const badge = group.querySelector('.group-status-badge');
    const title = card.querySelector('.card-title').textContent;
    const priceText = card.querySelector('.card-price').textContent.toLowerCase();
    const isFree = priceText === 'free';

    badge.textContent = title;
    if (isFree) {
        badge.className = 'group-status-badge badge-free';
    } else {
        badge.className = 'group-status-badge badge-paid';
    }
}

function toggleVariantGroup(header) {
    const group = header.closest('.dc-variant-group');
    group.classList.toggle('expanded');
}

let selectedRating = 5;
function highlightStars(n) {
    document.querySelectorAll('#ratingStars i').forEach((s, i) => {
        s.className = i < n ? 'bi bi-star-fill' : 'bi bi-star';
        s.style.color = i < n ? 'var(--accent-orange)' : 'var(--text-muted)';
    });
}
function resetStars() { highlightStars(selectedRating); }
function setRating(n) { selectedRating = n; document.getElementById('ratingInput').value = n; highlightStars(n); }
highlightStars(5);
</script>
