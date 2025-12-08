-- Run this in phpMyAdmin or MySQL CLI to create the trusted_devices table

CREATE TABLE IF NOT EXISTS `trusted_devices` (
  `TrustedDeviceID` INT AUTO_INCREMENT PRIMARY KEY,
  `UserID` INT NOT NULL,
  `DeviceName` VARCHAR(255) NOT NULL,
  `DeviceToken` VARCHAR(255) NOT NULL,
  `UserAgent` TEXT NULL,
  `IPAddress` VARCHAR(45) NULL,
  `LastUsedAt` DATETIME NULL,
  `ExpiresAt` DATETIME NOT NULL,
  `CreatedAt` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`UserID`) REFERENCES `user`(`UserID`) ON DELETE CASCADE,
  INDEX `idx_device_token` (`DeviceToken`),
  INDEX `idx_user_id` (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
