-- Create the referral_codes table
CREATE TABLE `referral_codes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `referral_codes_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add referral_code_id column to the shops table
ALTER TABLE `shops` ADD COLUMN `referral_code_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `seller_package_id`;

-- Add foreign key constraint (if needed)
ALTER TABLE `shops`
ADD CONSTRAINT `shops_referral_code_id_foreign`
FOREIGN KEY (`referral_code_id`) REFERENCES `referral_codes` (`id`)
ON DELETE SET NULL;

-- Insert some sample referral codes
INSERT INTO `referral_codes` (`code`, `usage_limit`, `used_count`, `description`, `is_active`, `created_at`, `updated_at`)
VALUES
('SELLER2023', 100, 0, 'Default seller referral code for 2023', 1, NOW(), NOW()),
('PARTNER2023', 50, 0, 'Partner referral code with limited usage', 1, NOW(), NOW()),
('PREMIUM2023', NULL, 0, 'Premium partner referral code with unlimited usage', 1, NOW(), NOW());
