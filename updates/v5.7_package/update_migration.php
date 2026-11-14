<?php
try {
    if (class_exists("Database")) {
        $db = Database::getInstance();
        $db->query("INSERT INTO settings (`key`, `value`, `group_name`) VALUES ('app_version', '5.7', 'system') ON DUPLICATE KEY UPDATE `value` = '5.7'");
        $db->query("INSERT INTO settings (`key`, `value`, `group_name`) VALUES ('product_card_design', '{\"card_theme\":\"classic\",\"show_discount_badge\":\"1\",\"show_status_badge\":\"1\",\"show_wishlist_btn\":\"1\",\"show_quickview\":\"1\",\"show_live_preview\":\"1\",\"show_rating\":\"1\",\"show_sales_count\":\"1\",\"show_mrp_price\":\"1\",\"card_border_radius\":\"16\",\"card_aspect_ratio\":\"16/10\",\"card_shadow\":\"subtle\",\"btn_style\":\"rounded_pill\",\"accent_color\":\"#6366f1\"}', 'design') ON DUPLICATE KEY UPDATE `key`=`key`");
    }
} catch (Throwable $e) {}
