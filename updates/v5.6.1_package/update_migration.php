<?php
/**
 * DigiWS System Update Migration Script (v5.6.1)
 * Product ID: PRD-ISV5JQOF7S
 */
declare(strict_types=1);

try {
    if (class_exists('Database')) {
        $db = Database::getInstance();
        $db->query("INSERT INTO settings (`key`, `value`, `group_name`) VALUES ('app_version', '5.6.1', 'system') ON DUPLICATE KEY UPDATE `value` = '5.6.1'");
    }
} catch (Throwable $e) {
    // Migration handled safely
}
