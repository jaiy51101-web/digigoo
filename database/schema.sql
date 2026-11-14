-- DigiWS Full Base Schema
SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE IF NOT EXISTS `blog_analytics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `analytics_date` date NOT NULL,
  `views` int(10) unsigned DEFAULT 0,
  `unique_visitors` int(10) unsigned DEFAULT 0,
  `sessions` int(10) unsigned DEFAULT 0,
  `bounce_rate` decimal(5,2) DEFAULT 0.00,
  `avg_time_on_page` int(10) unsigned DEFAULT 0 COMMENT 'in seconds',
  `scroll_depth` decimal(3,2) DEFAULT 0.00 COMMENT 'percentage',
  `clicks` int(10) unsigned DEFAULT 0,
  `ctr` decimal(5,2) DEFAULT 0.00 COMMENT 'Click Through Rate',
  `top_referrer` varchar(255) DEFAULT NULL,
  `top_keyword` varchar(255) DEFAULT NULL,
  `traffic_source` varchar(100) DEFAULT 'organic' COMMENT 'organic, direct, referral, social, etc',
  `device_type` varchar(50) DEFAULT 'desktop' COMMENT 'desktop, mobile, tablet',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_analytics` (`post_id`,`analytics_date`),
  KEY `idx_post` (`post_id`),
  KEY `idx_date` (`analytics_date`),
  CONSTRAINT `blog_analytics_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_bookmarks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_bookmark` (`user_id`,`post_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_post` (`post_id`),
  CONSTRAINT `blog_bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_bookmarks_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'bi-folder',
  `color` varchar(20) DEFAULT '#2563eb',
  `image` varchar(255) DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `post_count` int(10) unsigned DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `meta_title` varchar(200) DEFAULT NULL,
  `meta_description` varchar(300) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_status` (`status`),
  KEY `idx_parent` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_comment_likes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(10) unsigned NOT NULL,
  `ip_hash` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`comment_id`,`ip_hash`),
  KEY `idx_comment` (`comment_id`),
  CONSTRAINT `blog_comment_likes_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `blog_comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `guest_email` varchar(191) DEFAULT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','rejected','spam') DEFAULT 'pending',
  `likes` int(10) unsigned DEFAULT 0,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_post` (`post_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `blog_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_content_quality` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `seo_score` int(10) unsigned DEFAULT 0,
  `readability_score` int(10) unsigned DEFAULT 0,
  `grammar_score` int(10) unsigned DEFAULT 0,
  `ai_detection_score` int(10) unsigned DEFAULT 0,
  `human_score` decimal(3,2) DEFAULT 0.00,
  `duplicate_content_check` tinyint(1) DEFAULT 0,
  `spam_score` int(10) unsigned DEFAULT 0,
  `eeat_score` int(10) unsigned DEFAULT 0,
  `google_discover_score` int(10) unsigned DEFAULT 0,
  `accessibility_score` int(10) unsigned DEFAULT 0,
  `overall_score` int(10) unsigned DEFAULT 0,
  `issues` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`issues`)),
  `recommendations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recommendations`)),
  `last_checked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_quality` (`post_id`),
  CONSTRAINT `blog_content_quality_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_faqs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `question` text NOT NULL,
  `answer` longtext NOT NULL,
  `faq_order` int(10) unsigned DEFAULT 0,
  `is_auto_generated` tinyint(1) DEFAULT 0,
  `ai_suggested` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_post` (`post_id`),
  CONSTRAINT `blog_faqs_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_internal_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_post_id` int(10) unsigned NOT NULL,
  `to_post_id` int(10) unsigned NOT NULL,
  `anchor_text` varchar(255) DEFAULT NULL,
  `link_type` varchar(50) DEFAULT 'internal' COMMENT 'internal, external, affiliate',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_link` (`from_post_id`,`to_post_id`),
  KEY `idx_from_post` (`from_post_id`),
  KEY `idx_to_post` (`to_post_id`),
  CONSTRAINT `blog_internal_links_ibfk_1` FOREIGN KEY (`from_post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_internal_links_ibfk_2` FOREIGN KEY (`to_post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_media` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL COMMENT 'image, video, pdf, etc',
  `mime_type` varchar(100) NOT NULL,
  `file_size` int(10) unsigned NOT NULL COMMENT 'in bytes',
  `file_path` varchar(500) NOT NULL,
  `thumbnail_path` varchar(500) DEFAULT NULL,
  `webp_path` varchar(500) DEFAULT NULL,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `source_url` varchar(500) DEFAULT NULL COMMENT 'for third-party images',
  `source_type` varchar(50) DEFAULT 'local' COMMENT 'local, unsplash, pexels, pixabay',
  `is_featured` tinyint(1) DEFAULT 0,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_post` (`post_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_type` (`file_type`),
  KEY `idx_featured` (`is_featured`),
  CONSTRAINT `blog_media_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `blog_media_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_post_blocks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `block_index` int(10) unsigned NOT NULL,
  `block_type` varchar(50) NOT NULL,
  `block_data` longtext NOT NULL COMMENT 'JSON data for block content',
  `attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Block-specific attributes' CHECK (json_valid(`attributes`)),
  `is_hidden` tinyint(1) DEFAULT 0,
  `is_collapsed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_post` (`post_id`),
  KEY `idx_post_index` (`post_id`,`block_index`),
  KEY `idx_block_type` (`block_type`),
  CONSTRAINT `blog_post_blocks_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_post_meta` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `meta_key` varchar(100) NOT NULL,
  `meta_value` longtext DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_post` (`post_id`),
  KEY `idx_key` (`meta_key`),
  CONSTRAINT `blog_post_meta_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_post_tags` (
  `post_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`post_id`,`tag_id`),
  KEY `idx_post` (`post_id`),
  KEY `idx_tag` (`tag_id`),
  CONSTRAINT `blog_post_tags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_post_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `blog_tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_post_views` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `ip_hash` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_post` (`post_id`),
  KEY `idx_ip_post` (`post_id`,`ip_hash`),
  CONSTRAINT `blog_post_views_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_related_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `related_post_id` int(10) unsigned NOT NULL,
  `relation_type` varchar(50) DEFAULT 'manual' COMMENT 'manual, auto_category, auto_tag, auto_ai',
  `relevance_score` decimal(3,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_relation` (`post_id`,`related_post_id`),
  KEY `idx_post` (`post_id`),
  KEY `idx_related_post` (`related_post_id`),
  CONSTRAINT `blog_related_posts_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_related_posts_ibfk_2` FOREIGN KEY (`related_post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_revisions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `revision_number` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `content_snapshot` longtext DEFAULT NULL COMMENT 'JSON of blocks',
  `meta_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'SEO and metadata snapshot' CHECK (json_valid(`meta_snapshot`)),
  `author_id` int(10) unsigned NOT NULL,
  `revision_type` varchar(50) DEFAULT 'auto' COMMENT 'auto, manual, scheduled',
  `change_summary` text DEFAULT NULL,
  `is_autosave` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_revision` (`post_id`,`revision_number`),
  KEY `idx_post` (`post_id`),
  KEY `idx_author` (`author_id`),
  KEY `idx_type` (`revision_type`),
  CONSTRAINT `blog_revisions_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_revisions_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_schemas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `schema_type` varchar(100) NOT NULL COMMENT 'Article, NewsArticle, BlogPosting, FAQPage, etc',
  `schema_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`schema_data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_post` (`post_id`),
  KEY `idx_type` (`schema_type`),
  CONSTRAINT `blog_schemas_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_seo_reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `report_date` date NOT NULL,
  `seo_score` int(10) unsigned DEFAULT 0,
  `readability_score` int(10) unsigned DEFAULT 0,
  `google_discover_score` int(10) unsigned DEFAULT 0,
  `ai_content_score` int(10) unsigned DEFAULT 0,
  `eeat_score` int(10) unsigned DEFAULT 0,
  `keyword_density` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`keyword_density`)),
  `reading_time` int(10) unsigned DEFAULT 0,
  `word_count` int(10) unsigned DEFAULT 0,
  `heading_structure_valid` tinyint(1) DEFAULT 0,
  `alt_text_coverage` decimal(3,2) DEFAULT 0.00,
  `broken_links_count` int(10) unsigned DEFAULT 0,
  `issues` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of issues found' CHECK (json_valid(`issues`)),
  `suggestions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array of optimization suggestions' CHECK (json_valid(`suggestions`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_post` (`post_id`),
  KEY `idx_date` (`report_date`),
  CONSTRAINT `blog_seo_reports_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `post_count` int(10) unsigned DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_count` (`post_count`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `template_type` varchar(50) NOT NULL COMMENT 'how-to, tutorial, guide, listicle, news, review, case-study, etc',
  `blocks_template` longtext NOT NULL COMMENT 'JSON array of starter blocks',
  `meta_template` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Suggested meta, keywords, etc' CHECK (json_valid(`meta_template`)),
  `ai_prompt` text DEFAULT NULL COMMENT 'AI generation prompt',
  `is_active` tinyint(1) DEFAULT 1,
  `usage_count` int(10) unsigned DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_type` (`template_type`),
  KEY `idx_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_workflow_state` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `workflow_state` varchar(50) NOT NULL COMMENT 'draft, pending_review, seo_review, editor_approval, scheduled, published, archived',
  `assigned_to` int(10) unsigned DEFAULT NULL COMMENT 'reviewer/editor user_id',
  `notes` text DEFAULT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_post` (`post_id`),
  KEY `idx_state` (`workflow_state`),
  KEY `idx_assigned` (`assigned_to`),
  CONSTRAINT `blog_workflow_state_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_workflow_state_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `setup_included` tinyint(1) DEFAULT 0,
  `support_months` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_parent` (`parent_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('fixed','percentage') DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `expires_at` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `idx_code` (`code`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `license_keys` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `license_key` varchar(100) NOT NULL,
  `order_item_id` int(10) unsigned DEFAULT NULL,
  `status` enum('available','assigned','revoked') DEFAULT 'available',
  `assigned_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `license_key` (`license_key`),
  KEY `idx_product` (`product_id`),
  KEY `idx_status` (`status`),
  KEY `idx_key` (`license_key`),
  KEY `order_item_id` (`order_item_id`),
  CONSTRAINT `license_keys_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `license_keys_ibfk_2` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `managed_feedback` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `target_type` enum('product','blog') NOT NULL,
  `target_id` int(10) unsigned NOT NULL,
  `reviewer_name` varchar(100) NOT NULL,
  `reviewer_role` varchar(100) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('published','hidden') NOT NULL DEFAULT 'published',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_feedback_target` (`target_type`,`target_id`),
  KEY `idx_feedback_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `marketing_automation_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `automation_id` int(10) unsigned DEFAULT NULL,
  `subscriber_id` int(10) unsigned DEFAULT NULL,
  `event_name` varchar(100) NOT NULL,
  `event_key` varchar(128) DEFAULT NULL,
  `step` varchar(64) NOT NULL,
  `message` text NOT NULL,
  `context_json` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_automation_event` (`event_name`,`created_at`),
  KEY `idx_automation_rule` (`automation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `marketing_campaigns` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `template_id` int(10) unsigned DEFAULT NULL,
  `status` varchar(50) DEFAULT 'draft',
  `sent_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `marketing_crm_timeline` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(10) unsigned NOT NULL,
  `activity_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `marketing_lists` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `marketing_segments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `rules_json` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `marketing_subscribers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `status` enum('active','unsubscribed','bounced') DEFAULT 'active',
  `source` varchar(100) DEFAULT 'footer',
  `category` varchar(100) DEFAULT 'lead',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `marketing_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT 'general',
  `content_html` longtext DEFAULT NULL,
  `content_json` longtext DEFAULT NULL,
  `body_html` longtext DEFAULT NULL,
  `body_text` longtext DEFAULT NULL,
  `global_settings` longtext DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) NOT NULL,
  `status` enum('active','unsubscribed') DEFAULT 'active',
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `product_title` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `setup_included` tinyint(1) DEFAULT 0,
  `setup_price` decimal(10,2) DEFAULT 0.00,
  `support_months` int(11) DEFAULT 0,
  `support_price` decimal(10,2) DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `license_key` varchar(100) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(30) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `coupon_id` int(10) unsigned DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled','refunded') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `idx_user` (`user_id`),
  KEY `idx_order_number` (`order_number`),
  KEY `idx_status` (`status`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `coupon_id` (`coupon_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `otp_verifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `identifier` varchar(191) NOT NULL,
  `channel` enum('email','whatsapp') NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `attempts` int(11) DEFAULT 0,
  `max_attempts` int(11) DEFAULT 5,
  `resend_count` int(11) DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_identifier` (`identifier`),
  KEY `idx_expires` (`expires_at`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `content` longtext DEFAULT NULL,
  `meta_title` varchar(200) DEFAULT NULL,
  `meta_description` varchar(300) DEFAULT NULL,
  `status` enum('active','draft') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `gateway` varchar(30) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `gateway_order_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'INR',
  `status` enum('pending','success','failed','refunded') DEFAULT 'pending',
  `gateway_response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_transaction` (`transaction_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(280) NOT NULL,
  `summary` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author_id` int(10) unsigned NOT NULL,
  `blog_category_id` int(10) unsigned DEFAULT NULL,
  `promote_product_id` int(10) unsigned DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `meta_keywords` varchar(500) DEFAULT NULL,
  `status` enum('draft','published','scheduled') DEFAULT 'draft',
  `is_featured` tinyint(1) DEFAULT 0,
  `is_trending` tinyint(1) DEFAULT 0,
  `is_editors_pick` tinyint(1) DEFAULT 0,
  `allow_comments` tinyint(1) DEFAULT 1,
  `views` int(10) unsigned DEFAULT 0,
  `likes` int(10) unsigned DEFAULT 0,
  `reading_time` int(10) unsigned DEFAULT 0,
  `og_image` varchar(255) DEFAULT NULL,
  `featured_image_id` int(10) unsigned DEFAULT NULL,
  `table_of_contents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`table_of_contents`)),
  `ai_used` tinyint(1) DEFAULT 0,
  `ai_prompt` text DEFAULT NULL,
  `word_count` int(10) unsigned DEFAULT 0,
  `paragraph_count` int(10) unsigned DEFAULT 0,
  `heading_count` int(10) unsigned DEFAULT 0,
  `readability_grade` varchar(10) DEFAULT NULL,
  `featured_image_social` varchar(255) DEFAULT NULL,
  `is_sticky` tinyint(1) DEFAULT 0,
  `product_promotion_ids` varchar(500) DEFAULT NULL,
  `seo_score` int(10) unsigned DEFAULT 0,
  `last_editor_id` int(10) unsigned DEFAULT NULL,
  `editor_notes` text DEFAULT NULL,
  `og_title` varchar(200) DEFAULT NULL,
  `og_description` varchar(300) DEFAULT NULL,
  `canonical_url` varchar(500) DEFAULT NULL,
  `robots` varchar(50) DEFAULT 'index,follow',
  `schema_type` varchar(50) DEFAULT 'Article',
  `focus_keyword` varchar(200) DEFAULT NULL,
  `tags_cache` varchar(500) DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `rating_sum` int(11) DEFAULT 0,
  `rating_count` int(11) DEFAULT 0,
  `reactions_json` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_author` (`author_id`),
  KEY `idx_promote_product` (`promote_product_id`),
  KEY `idx_blog_category` (`blog_category_id`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_trending` (`is_trending`),
  KEY `idx_views` (`views`),
  KEY `idx_published_at` (`published_at`),
  KEY `idx_featured_image` (`featured_image_id`),
  KEY `idx_sticky` (`is_sticky`),
  KEY `idx_seo_score` (`seo_score`),
  KEY `idx_ai_used` (`ai_used`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`promote_product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_categories` (
  `product_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `product_features` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `feature` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `product_features_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `product_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(280) NOT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `sort_order` int(10) unsigned DEFAULT 0,
  `thumbnail` varchar(255) DEFAULT NULL,
  `preview_url` varchar(500) DEFAULT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `download_file` varchar(255) DEFAULT NULL,
  `external_url` varchar(500) DEFAULT NULL,
  `mrp` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_percent` decimal(5,2) DEFAULT 18.00,
  `product_type` enum('ready_website','custom_website','php_script','html_template','theme','plugin','other') DEFAULT 'other',
  `digiws_product_id` varchar(64) DEFAULT NULL,
  `setup_option` tinyint(1) DEFAULT 0,
  `setup_price` decimal(10,2) DEFAULT 0.00,
  `support_6_months` decimal(10,2) DEFAULT 0.00,
  `support_12_months` decimal(10,2) DEFAULT 0.00,
  `tags` varchar(500) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_bestseller` tinyint(1) DEFAULT 0,
  `is_new` tinyint(1) DEFAULT 0,
  `is_exclusive` tinyint(1) DEFAULT 0,
  `status` enum('active','draft') DEFAULT 'draft',
  `total_sales` int(10) unsigned DEFAULT 0,
  `avg_rating` decimal(3,2) DEFAULT 0.00,
  `view_count` int(10) unsigned DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_category` (`category_id`),
  KEY `idx_status` (`status`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_bestseller` (`is_bestseller`),
  KEY `idx_type` (`product_type`),
  KEY `idx_price` (`sale_price`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (`rating` between 1 and 5),
  `title` varchar(200) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `security_audit_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `event` varchar(100) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_event` (`event`),
  KEY `idx_ip` (`ip_address`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) DEFAULT 'general',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `idx_key` (`setting_key`),
  KEY `idx_group` (`setting_group`)
) ENGINE=InnoDB AUTO_INCREMENT=294 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `google_id` varchar(100) DEFAULT NULL,
  `facebook_id` varchar(100) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `whatsapp_verified` tinyint(1) DEFAULT 0,
  `verified_at` datetime DEFAULT NULL,
  `verify_token` varchar(100) DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `user_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `website_requests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `website_type` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `budget` varchar(100) DEFAULT NULL,
  `status` enum('pending','contacted','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_session` (`session_id`),
  KEY `idx_product` (`product_id`),
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;

-- Missing Marketing Tables
CREATE TABLE IF NOT EXISTS marketing_list_subscriber (
        list_id INT UNSIGNED NOT NULL,
        subscriber_id INT UNSIGNED NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (list_id, subscriber_id),
        INDEX (subscriber_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS marketing_smtps (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        provider VARCHAR(50) DEFAULT 'custom',
        host VARCHAR(255) NOT NULL,
        port INT NOT NULL DEFAULT 587,
        encryption VARCHAR(20) DEFAULT 'tls',
        username VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        from_email VARCHAR(255) NOT NULL,
        from_name VARCHAR(255) NOT NULL,
        daily_limit INT DEFAULT 1000,
        hourly_limit INT DEFAULT 100,
        sent_today INT DEFAULT 0,
        sent_this_hour INT DEFAULT 0,
        last_sent_at TIMESTAMP NULL DEFAULT NULL,
        last_reset_day DATE NULL DEFAULT NULL,
        last_reset_hour DATETIME NULL DEFAULT NULL,
        priority INT DEFAULT 1,
        status ENUM('active','inactive','error') DEFAULT 'active',
        error_count INT DEFAULT 0,
        last_error TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS marketing_automations (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        trigger_event VARCHAR(100) NOT NULL,
        segment_id INT UNSIGNED DEFAULT NULL,
        list_id INT UNSIGNED DEFAULT NULL,
        delay_value INT DEFAULT 0,
        delay_unit ENUM('minutes','hours','days') DEFAULT 'minutes',
        smtp_id INT UNSIGNED DEFAULT NULL,
        email_template_id INT UNSIGNED DEFAULT NULL,
        template_id INT UNSIGNED DEFAULT NULL,
        subject VARCHAR(255) DEFAULT NULL,
        body TEXT DEFAULT NULL,
        status ENUM('active','paused','draft') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS marketing_queue (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        campaign_id INT UNSIGNED DEFAULT NULL,
        automation_id INT UNSIGNED DEFAULT NULL,
        subscriber_id INT UNSIGNED NOT NULL,
        smtp_id INT UNSIGNED DEFAULT NULL,
        template_id INT UNSIGNED DEFAULT NULL,
        email_template_id INT UNSIGNED DEFAULT NULL,
        subject VARCHAR(255) DEFAULT NULL,
        body LONGTEXT DEFAULT NULL,
        event_key VARCHAR(128) DEFAULT NULL,
        scheduled_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        processing_started_at TIMESTAMP NULL DEFAULT NULL,
        sent_at TIMESTAMP NULL DEFAULT NULL,
        status ENUM('pending','sending','sent','failed','cancelled') DEFAULT 'pending',
        attempts INT DEFAULT 0,
        error_message TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (campaign_id),
        INDEX (automation_id),
        INDEX (subscriber_id),
        INDEX (status),
        INDEX (scheduled_at),
        INDEX (processing_started_at),
        UNIQUE KEY uq_marketing_queue_event (event_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS marketing_logs (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        campaign_id INT UNSIGNED DEFAULT NULL,
        automation_id INT UNSIGNED DEFAULT NULL,
        queue_id INT UNSIGNED DEFAULT NULL,
        subscriber_id INT UNSIGNED NOT NULL,
        smtp_id INT UNSIGNED DEFAULT NULL,
        type VARCHAR(50) DEFAULT 'email_sent',
        status VARCHAR(50) DEFAULT 'sent',
        details TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (campaign_id),
        INDEX (automation_id),
        INDEX (subscriber_id),
        INDEX (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
