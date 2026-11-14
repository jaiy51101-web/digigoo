-- DigiWS System Update SQL Migration (v5.6.1)
-- Product ID: PRD-ISV5JQOF7S

INSERT INTO settings (`key`, `value`, `group_name`) VALUES ('app_version', '5.6.1', 'system') ON DUPLICATE KEY UPDATE `value` = '5.6.1';
