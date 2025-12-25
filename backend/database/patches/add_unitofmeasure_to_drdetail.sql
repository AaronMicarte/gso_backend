-- Add UnitOfMeasureID column to deliveryreceiptdetail table
-- Run this on your live gso_db database

-- Add the column (default to 1 for existing rows which is 'pc')
ALTER TABLE deliveryreceiptdetail
  ADD COLUMN UnitOfMeasureID INT(11) NOT NULL DEFAULT 1 AFTER QuantityDelivered;

-- Add index
ALTER TABLE deliveryreceiptdetail
  ADD KEY `UnitOfMeasureID` (`UnitOfMeasureID`);

-- Add foreign key constraint
ALTER TABLE deliveryreceiptdetail
  ADD CONSTRAINT `fk_drdetail_unitofmeasure`
  FOREIGN KEY (`UnitOfMeasureID`) REFERENCES `unitofmeasure` (`UnitOfMeasureID`)
  ON DELETE RESTRICT ON UPDATE CASCADE;
