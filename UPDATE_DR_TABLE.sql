-- ============================================
-- UPDATE DELIVERY RECEIPT TABLE FOR SIMPLIFIED WORKFLOW
-- Add SupplierName and change POID to PONumber
-- ============================================
-- Date: December 20, 2025
-- ============================================

USE gso_db;

-- Add SupplierName column to deliveryreceipt
ALTER TABLE `deliveryreceipt`
ADD COLUMN `SupplierName` VARCHAR(255) NULL COMMENT 'Supplier name (manual entry or from supplier table)' AFTER `DRNumber`;

-- Change POID to PONumber (varchar for manual entry)
ALTER TABLE `deliveryreceipt`
CHANGE COLUMN `POID` `PONumber` VARCHAR(50) NULL COMMENT 'Manual PO Number reference (optional)';

-- Drop the old foreign key constraint if it exists
-- (This should have been done by the removal script, but just in case)
SET @fk_name = (SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = 'gso_db' AND TABLE_NAME = 'deliveryreceipt'
                AND COLUMN_NAME = 'PONumber' AND REFERENCED_TABLE_NAME = 'purchaseorder' LIMIT 1);

SET @sql = IF(@fk_name IS NOT NULL,
    CONCAT('ALTER TABLE `deliveryreceipt` DROP FOREIGN KEY `', @fk_name, '`'),
    'SELECT "No foreign key to drop" AS message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update deliveryreceiptdetail to remove PODetailID foreign key
-- Change PODetailID to optional text field for manual item description

-- Drop foreign key for PODetailID FIRST
SET @fk_name2 = (SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = 'gso_db' AND TABLE_NAME = 'deliveryreceiptdetail'
                 AND COLUMN_NAME = 'PODetailID' AND REFERENCED_TABLE_NAME = 'purchaseorderdetail' LIMIT 1);

SET @sql2 = IF(@fk_name2 IS NOT NULL,
    CONCAT('ALTER TABLE `deliveryreceiptdetail` DROP FOREIGN KEY `', @fk_name2, '`'),
    'SELECT "No foreign key to drop for detail" AS message');

PREPARE stmt2 FROM @sql2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- Now change the column
ALTER TABLE `deliveryreceiptdetail`
CHANGE COLUMN `PODetailID` `ItemDescription` VARCHAR(255) NOT NULL COMMENT 'Manual item description';

-- ============================================
-- VERIFICATION
-- ============================================

SELECT 'Delivery Receipt table updated successfully!' AS Status;

-- Show updated deliveryreceipt structure
DESCRIBE deliveryreceipt;

-- Show updated deliveryreceiptdetail structure
DESCRIBE deliveryreceiptdetail;

-- Confirm supplier table still exists
SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'gso_db' AND TABLE_NAME = 'supplier';