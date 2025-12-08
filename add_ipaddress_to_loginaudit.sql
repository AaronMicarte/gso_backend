-- =====================================================
-- ADD IPAddress COLUMN TO loginaudit TABLE
-- Run this in phpMyAdmin or MySQL CLI
-- =====================================================
-- This is CRITICAL for proper security auditing
-- Always log the client IP on login/logout events
-- =====================================================

-- Add IPAddress column if it doesn't exist
ALTER TABLE `loginaudit` 
ADD COLUMN IF NOT EXISTS `IPAddress` VARCHAR(45) NULL AFTER `Status`;

-- Verify the change
DESCRIBE `loginaudit`;
