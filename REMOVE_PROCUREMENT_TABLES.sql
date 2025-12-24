-- ============================================
-- SQL SCRIPT TO REMOVE PROCUREMENT TABLES
-- Purchase Requests, Purchase Orders, Job Orders
-- ============================================
-- 
-- INSTRUCTIONS:
-- 1. Backup your database first: mysqldump -u root gso_db > backup_before_removal.sql
-- 2. Run this script in phpMyAdmin or MySQL CLI
-- 3. This will permanently delete all PR, PO, and JO data
--
-- Date: December 19, 2025
-- ============================================

USE gso_db;

-- Drop foreign key constraints first (if any exist)
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================
-- DROP JOB ORDER TABLES
-- ============================================

DROP TABLE IF EXISTS `joborderdetail`;
DROP TABLE IF EXISTS `joborder`;
DROP TABLE IF EXISTS `joborderstatus`;

-- ============================================
-- DROP PURCHASE ORDER TABLES
-- ============================================

DROP TABLE IF EXISTS `purchaseorderdetail`;
DROP TABLE IF EXISTS `purchaseorder`;
DROP TABLE IF EXISTS `purchaseorderstatus`;

-- ============================================
-- DROP PURCHASE REQUEST TABLES
-- ============================================

DROP TABLE IF EXISTS `purchaserequestdetail`;
DROP TABLE IF EXISTS `purchaserequest`;
DROP TABLE IF EXISTS `purchaserequeststatus`;

-- ============================================
-- UPDATE DELIVERY RECEIPT TABLE
-- Remove POID foreign key reference
-- ============================================

-- Check if the foreign key exists before dropping
SELECT CONSTRAINT_NAME 
INTO @fk_name
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'gso_db' 
  AND TABLE_NAME = 'deliveryreceipt' 
  AND COLUMN_NAME = 'POID'
  AND REFERENCED_TABLE_NAME = 'purchaseorder'
LIMIT 1;

SET @sql = IF(@fk_name IS NOT NULL, 
    CONCAT('ALTER TABLE `deliveryreceipt` DROP FOREIGN KEY `', @fk_name, '`'),
    'SELECT "No foreign key to drop" AS message');
    
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Keep POID column as optional text field for manual entry
-- Convert POID to VARCHAR to allow manual PO number entry
ALTER TABLE `deliveryreceipt` 
    MODIFY COLUMN `POID` VARCHAR(50) NULL COMMENT 'Manual PO Number reference (optional)';

-- ============================================
-- RE-ENABLE FOREIGN KEY CHECKS
-- ============================================

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

SELECT 'Procurement tables removed successfully!' AS Status;

-- Show remaining tables
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'gso_db' 
  AND TABLE_NAME LIKE '%purchase%' 
  OR TABLE_NAME LIKE '%job%';

-- Should return empty result if successful
