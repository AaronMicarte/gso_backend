-- SAMPLE INSERTS: 2 Delivery Receipts (DR)
-- Context: All items are semi/non-expendable (no consumables)
-- DR 1: All PAR (Property Acknowledgement Receipt)
-- DR 2: 1 ICS (Inventory Custodian Slip), rest PAR

-- DRID starts after last in dump (14), so 15 and 16
-- DRDetailID starts after last in dump (31), so 32+

-- DELIVERY RECEIPT 1: All PAR
INSERT INTO `deliveryreceipt` (`DRID`, `DRNumber`, `SupplierName`, `PONumber`, `InvoiceNumber`, `ReceivedBy`, `DateReceived`, `Remarks`, `dr_picture`, `CreatedDate`, `deleted_at`) VALUES
(15, 'DR-2026-010', 'TechnoWorld Inc.', 'PO-2026-010', 'INV-2026-010', 1, '2026-01-11', 'All items for PAR', '', '2026-01-11 09:00:00', NULL);

INSERT INTO `deliveryreceiptdetail` (`DRDetailID`, `DRID`, `ItemDescription`, `Brand`, `ModelNumber`, `SerialNumber`, `PropertyTypeID`, `QuantityDelivered`, `UnitOfMeasureID`, `UnitCost`, `TotalCost`, `deleted_at`) VALUES
-- Laptop (PAR, with brand/model/serial)
(32, 15, 'Laptop', 'Lenovo', 'ThinkPad E14', 'LTP202601', 3, 2, 1, 42000.00, 84000.00, NULL),
-- Printer (PAR, with brand/model/serial)
(33, 15, 'Printer', 'HP', 'LaserJet Pro M404', 'PRN202601', 3, 1, 1, 18500.00, 18500.00, NULL),
-- Office Table (PAR, no brand/model/serial needed)
(34, 15, 'Office Table', NULL, NULL, NULL, 4, 3, 1, 3500.00, 10500.00, NULL);

-- DELIVERY RECEIPT 2: 1 ICS, rest PAR
INSERT INTO `deliveryreceipt` (`DRID`, `DRNumber`, `SupplierName`, `PONumber`, `InvoiceNumber`, `ReceivedBy`, `DateReceived`, `Remarks`, `dr_picture`, `CreatedDate`, `deleted_at`) VALUES
(16, 'DR-2026-011', 'OfficePro Supplies', 'PO-2026-011', 'INV-2026-011', 1, '2026-01-11', 'Mixed PAR and ICS', '', '2026-01-11 10:30:00', NULL);

INSERT INTO `deliveryreceiptdetail` (`DRDetailID`, `DRID`, `ItemDescription`, `Brand`, `ModelNumber`, `SerialNumber`, `PropertyTypeID`, `QuantityDelivered`, `UnitOfMeasureID`, `UnitCost`, `TotalCost`, `deleted_at`) VALUES
-- Steel Cabinet (PAR, no brand/model/serial needed)
(35, 16, 'Steel Cabinet', NULL, NULL, NULL, 4, 2, 1, 8000.00, 16000.00, NULL),
-- Desktop Computer (ICS, with brand/model/serial)
(36, 16, 'Desktop Computer', 'Acer', 'Veriton X', 'DTC202601', 3, 1, 1, 38000.00, 38000.00, NULL),
-- Executive Chair (PAR, no brand/model/serial needed)
(37, 16, 'Executive Chair', NULL, NULL, NULL, 4, 2, 1, 6500.00, 13000.00, NULL);

-- Notes:
-- PropertyTypeID: 3 = IT Equipment, 4 = Furniture
-- UnitOfMeasureID: 1 = unit/piece
-- All items are semi/non-expendable (no consumables)
-- Only Desktop Computer (in DR 2) is for ICS, rest are for PAR
-- Brand/Model/Serial only filled for IT items, not for furniture
