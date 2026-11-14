<!-- User Profile Page -->
<?php $profileUser = (new User())->find(currentUserId()); ?>
<section class="dc-section" style="padding-top:30px;">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3"><?php include VIEWS_PATH . '/user/sidebar.php'; ?></div>
            <div class="col-lg-9">
                <h4 class="mb-4">Profile Settings</h4>
                <div class="dc-card">
                    <form method="POST" action="<?= url('user/profile') ?>" enctype="multipart/form-data">
                        <?= csrfField() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="dc-form-label">Full Name</label>
                                <input type="text" name="name" class="dc-form-control" value="<?= htmlspecialchars($profileUser['name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="dc-form-label">Email (cannot change)</label>
                                <input type="email" class="dc-form-control" value="<?= htmlspecialchars($profileUser['email']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="dc-form-label">Phone</label>
                                <?php
                                $phoneData = splitPhone($profileUser['phone'] ?? '');
                                ?>
                                 <div class="input-group">
                                     <select name="country_code" class="form-select dc-form-control" style="max-width: 90px; font-size: 0.85rem; border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none;">
                                         <option value="91" <?= $phoneData['code'] === '91' ? 'selected' : '' ?>>🇮🇳 +91</option>
                                         <option value="1" <?= $phoneData['code'] === '1' ? 'selected' : '' ?>>🇺🇸 +1</option>
                                         <option value="44" <?= $phoneData['code'] === '44' ? 'selected' : '' ?>>🇬🇧 +44</option>
                                         <option value="971" <?= $phoneData['code'] === '971' ? 'selected' : '' ?>>🇦🇪 +971</option>
                                         <option value="62" <?= $phoneData['code'] === '62' ? 'selected' : '' ?>>🇮🇩 +62</option>
                                         <option value="880" <?= $phoneData['code'] === '880' ? 'selected' : '' ?>>🇧🇩 +880</option>
                                         <option value="92" <?= $phoneData['code'] === '92' ? 'selected' : '' ?>>🇵🇰 +92</option>
                                         <option value="977" <?= $phoneData['code'] === '977' ? 'selected' : '' ?>>🇳🇵 +977</option>
                                         <option value="60" <?= $phoneData['code'] === '60' ? 'selected' : '' ?>>🇲🇾 +60</option>
                                         <option value="65" <?= $phoneData['code'] === '65' ? 'selected' : '' ?>>🇸🇬 +65</option>
                                         <option value="61" <?= $phoneData['code'] === '61' ? 'selected' : '' ?>>🇦🇺 +61</option>
                                     </select>
                                     <input type="tel" name="phone" class="form-control dc-form-control" value="<?= htmlspecialchars($phoneData['number']) ?>" style="font-size: 0.85rem; border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                 </div>
                            </div>
                            <div class="col-md-6">
                                <label class="dc-form-label">Avatar</label>
                                <input type="file" name="avatar" class="dc-form-control" accept="image/*">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-dc-primary"><i class="bi bi-check-lg me-2"></i>Update Profile</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
