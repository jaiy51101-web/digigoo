CREATE TABLE IF NOT EXISTS `managed_feedback` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `target_type` ENUM('product','blog') NOT NULL,
    `target_id` INT UNSIGNED NOT NULL,
    `reviewer_name` VARCHAR(100) NOT NULL,
    `reviewer_role` VARCHAR(100) DEFAULT NULL,
    `rating` DECIMAL(2,1) DEFAULT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('published','hidden') NOT NULL DEFAULT 'published',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_feedback_target` (`target_type`, `target_id`),
    INDEX `idx_feedback_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
