-- Inventory detail editable fields support
-- Ensures propertyinventorydetail columns used by PUT /api/inventory/details/{id}
-- are present and documented for Description, Brand, ModelNumber, and SerialNumber.

ALTER TABLE `propertyinventorydetail`
  MODIFY COLUMN `Description` varchar(255) DEFAULT NULL COMMENT 'Item description (editable)',
  MODIFY COLUMN `Brand` varchar(100) DEFAULT NULL COMMENT 'Brand (editable)',
  MODIFY COLUMN `ModelNumber` varchar(100) DEFAULT NULL COMMENT 'Model number (editable)',
  MODIFY COLUMN `SerialNumber` varchar(100) DEFAULT NULL COMMENT 'Serial number (editable)';
