-- MFA (Multi-Factor Authentication) Database Updates
-- Run this SQL script to add MFA support to your existing database

-- 1. Add MFA fields to the existing 'user' table
ALTER TABLE `user`
ADD COLUMN `MFASecret` TEXT NULL AFTER `Password`,
ADD COLUMN `MFAEnabled` TINYINT(1) NOT NULL DEFAULT 0 AFTER `MFASecret`,
ADD COLUMN `BackupCodes` TEXT NULL AFTER `MFAEnabled`;

-- 2. Create the trusted_devices table for device-based MFA skipping
CREATE TABLE IF NOT EXISTS `trusted_devices` (
  `DeviceID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserID` BIGINT UNSIGNED NOT NULL,
  `DeviceToken` VARCHAR(255) NOT NULL,
  `DeviceName` VARCHAR(255) NULL,
  `UserAgent` VARCHAR(500) NULL,
  `IPAddress` VARCHAR(50) NULL,
  `CreatedDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `ExpiresAt` TIMESTAMP NULL,
  `LastUsedAt` TIMESTAMP NULL,
  PRIMARY KEY (`DeviceID`),
  UNIQUE KEY `trusted_devices_device_token_unique` (`DeviceToken`),
  KEY `trusted_devices_user_id_index` (`UserID`),
  KEY `trusted_devices_device_token_index` (`DeviceToken`),
  CONSTRAINT `trusted_devices_user_id_foreign` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Verify the changes
-- Run these queries to verify the columns were added:
-- DESCRIBE `user`;
-- DESCRIBE `trusted_devices`;
