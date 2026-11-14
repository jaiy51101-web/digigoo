INSERT INTO settings (`key`, `value`, `group_name`) VALUES ('app_version', '5.7', 'system') ON DUPLICATE KEY UPDATE `value` = '5.7';
