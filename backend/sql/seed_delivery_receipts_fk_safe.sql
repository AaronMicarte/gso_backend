-- FK-safe Delivery Receipt seed script
-- Generated: 2026-03-18
-- Purpose:
-- 1) Insert at least 4 delivery receipts
-- 2) Insert at least 6 assets per DR
-- 3) Guarantee DR detail FKs are consistent by deriving PropertyTypeID/UnitOfMeasureID from `articles`

START TRANSACTION;

-- Resolve users using role rules from dump data.
-- ReceivedBy is always gso.admin as requested.
SET @received_by := (
  SELECT u.UserID
  FROM `user` u
  WHERE u.Username = 'gso.admin'
  LIMIT 1
);

-- If gso.admin is missing, keep 0 so FK check fails fast on insert.
SET @received_by := COALESCE(@received_by, 0);

-- Requesters must be Department Head only:
-- - 4 = Department Head

SET @req_head_1 := (
  SELECT u.UserID
  FROM `user` u
  WHERE u.UserRoleID = 4
    AND u.IsActive = 1
  ORDER BY u.UserID ASC
  LIMIT 1
);

SET @req_head_2 := (
  SELECT u.UserID
  FROM `user` u
  WHERE u.UserRoleID = 4
    AND u.IsActive = 1
  ORDER BY u.UserID DESC
  LIMIT 1
);

-- Department-head-only mapping for requested_by.
-- If only one active department head exists, reuse the same user.
SET @req_head_2 := COALESCE(@req_head_2, @req_head_1);

SET @inspected_by := (
  SELECT u.UserID
  FROM `user` u
  WHERE u.UserRoleID = 6
  ORDER BY u.UserID ASC
  LIMIT 1
);

SET @inspected_by := COALESCE(@inspected_by, @received_by);

-- Stage DR headers.
CREATE TEMPORARY TABLE tmp_dr_headers (
  dr_number VARCHAR(50) NOT NULL PRIMARY KEY,
  iar_number VARCHAR(255) NULL,
  requested_by INT NOT NULL,
  supplier_name VARCHAR(100) NOT NULL,
  pr_number VARCHAR(50) NULL,
  po_number VARCHAR(50) NULL,
  invoice_number VARCHAR(50) NULL,
  date_received DATE NOT NULL,
  remarks TEXT NULL,
  dr_picture VARCHAR(255) NOT NULL
);

INSERT INTO tmp_dr_headers (
  dr_number,
  iar_number,
  requested_by,
  supplier_name,
  pr_number,
  po_number,
  invoice_number,
  date_received,
  remarks,
  dr_picture
) VALUES
('DR-2026-03-18-001', 'IAR-2026-03-18-001', @req_head_1, 'Tyron Corporation', 'PR-2026-CCE-1001', 'PO-2026-1001', 'INV-2026-1001', '2026-03-10', 'Batch 1 office and ICT assets for operational rollout.', 'uploads/dr/dr-2026-03-18-001.jpg'),
('DR-2026-03-18-002', 'IAR-2026-03-18-002', @req_head_1, 'Cyron Suppliers', 'PR-2026-CCE-1002', 'PO-2026-1002', 'INV-2026-1002', '2026-03-12', 'Batch 2 mixed furniture and equipment.', 'uploads/dr/dr-2026-03-18-002.jpg'),
('DR-2026-03-18-003', 'IAR-2026-03-18-003', @req_head_2, 'Los Pollos Hermanos', 'PR-2026-CCE-1003', 'PO-2026-1003', 'INV-2026-1003', '2026-03-14', 'Batch 3 admin support assets.', 'uploads/dr/dr-2026-03-18-003.jpg'),
('DR-2026-03-18-004', 'IAR-2026-03-18-004', @req_head_2, 'COVID Supplies', 'PR-2026-CCE-1004', 'PO-2026-1004', 'INV-2026-1004', '2026-03-16', 'Batch 4 additional non-expendable units.', 'uploads/dr/dr-2026-03-18-004.jpg');

-- Re-runnable cleanup for the exact DR numbers in this script.
DELETE d
FROM `deliveryreceiptdetail` d
INNER JOIN `deliveryreceipt` r ON r.DRID = d.DRID
INNER JOIN tmp_dr_headers t ON t.dr_number = r.DRNumber;

DELETE r
FROM `deliveryreceipt` r
INNER JOIN tmp_dr_headers t ON t.dr_number = r.DRNumber;

-- Insert DR headers.
INSERT INTO `deliveryreceipt` (
  `DRNumber`,
  `IARNumber`,
  `SupplierID`,
  `RequestedBy`,
  `PRNumber`,
  `PONumber`,
  `InvoiceNumber`,
  `ReceivedBy`,
  `InspectedBy`,
  `DateReceived`,
  `DateInspected`,
  `Remarks`,
  `InspectionStatus`,
  `InspectionCompletedAt`,
  `dr_picture`,
  `CreatedDate`,
  `deleted_at`
)
SELECT
  t.dr_number,
  t.iar_number,
  s.SupplierID,
  t.requested_by,
  t.pr_number,
  t.po_number,
  t.invoice_number,
  @received_by,
  @inspected_by,
  t.date_received,
  t.date_received,
  t.remarks,
  'inspected',
  NOW(),
  t.dr_picture,
  NOW(),
  NULL
FROM tmp_dr_headers t
LEFT JOIN `supplier` s ON s.SupplierName = t.supplier_name;

-- Stage DR detail item plan. Keep article names exactly as in `articles`.
CREATE TEMPORARY TABLE tmp_dr_items (
  dr_number VARCHAR(50) NOT NULL,
  article_name VARCHAR(255) NOT NULL,
  qty_delivered INT NOT NULL,
  unit_cost DECIMAL(18,2) NOT NULL,
  brand VARCHAR(100) NULL,
  model_number VARCHAR(100) NULL,
  serial_number VARCHAR(100) NULL
);

INSERT INTO tmp_dr_items (
  dr_number,
  article_name,
  qty_delivered,
  unit_cost,
  brand,
  model_number,
  serial_number
) VALUES
('DR-2026-03-18-001', 'Office Desk', 4, 8500.00, 'Prime Office', 'OD-PRM-120', NULL),
('DR-2026-03-18-001', 'Office Chair', 8, 3200.00, 'ErgoSeat', 'CH-ERG-22', NULL),
('DR-2026-03-18-001', 'Filing Cabinet', 2, 11500.00, 'SteelSafe', 'FC-STL-4D', NULL),
('DR-2026-03-18-001', 'Desktop Computer', 6, 42500.00, 'Lenovo', 'ThinkCentre-M70', NULL),
('DR-2026-03-18-001', 'Printer/Scanner', 2, 18900.00, 'Brother', 'DCP-L5650DN', NULL),
('DR-2026-03-18-001', 'Telephone Set', 1, 2350.00, 'Panasonic', 'KX-TS880', 'TEL-2026-001'),

('DR-2026-03-18-002', 'Laptop Computer', 1, 52000.00, 'Dell', 'Latitude-5440', 'LTP-2026-001'),
('DR-2026-03-18-002', 'Projector', 1, 28500.00, 'Epson', 'EB-X06', 'PROJ-2026-001'),
('DR-2026-03-18-002', 'Air Conditioner', 4, 33800.00, 'Carrier', 'Aura-1.5HP', NULL),
('DR-2026-03-18-002', 'Office Desk', 3, 8400.00, 'Prime Office', 'OD-PRM-140', NULL),
('DR-2026-03-18-002', 'Office Chair', 6, 3150.00, 'ErgoSeat', 'CH-ERG-24', NULL),
('DR-2026-03-18-002', 'Table Fan', 5, 2450.00, 'Asahi', 'TF-16X', NULL),

('DR-2026-03-18-003', 'Desktop Computer', 4, 43800.00, 'HP', 'ProDesk-400', NULL),
('DR-2026-03-18-003', 'Server Equipment', 1, 165000.00, 'Dell EMC', 'PowerEdge-R250', 'SRV-2026-001'),
('DR-2026-03-18-003', 'Printer/Scanner', 3, 17200.00, 'Canon', 'MF273dw', NULL),
('DR-2026-03-18-003', 'Filing Cabinet', 2, 11200.00, 'SteelSafe', 'FC-STL-3D', NULL),
('DR-2026-03-18-003', 'Telephone Set', 4, 2290.00, 'Panasonic', 'KX-TS500', NULL),
('DR-2026-03-18-003', 'Calculator', 10, 650.00, 'Casio', 'DJ-120D', NULL),

('DR-2026-03-18-004', 'Laptop Computer', 1, 49900.00, 'Acer', 'TravelMate-P2', 'LTP-2026-002'),
('DR-2026-03-18-004', 'Office Lamp', 12, 980.00, 'Firefly', 'OFL-9W', NULL),
('DR-2026-03-18-004', 'Extension Cord', 10, 560.00, 'Royu', 'EXT-5S', NULL),
('DR-2026-03-18-004', 'Hand Tools Set', 3, 4200.00, 'Ingco', 'HTS-112', NULL),
('DR-2026-03-18-004', 'Waste Basket', 10, 420.00, 'Omni', 'WB-18L', NULL),
('DR-2026-03-18-004', 'Projector', 1, 27900.00, 'BenQ', 'MS560', 'PROJ-2026-002');

-- Insert DR details with FK-safe derivations:
-- - ArticleID by article_name
-- - PropertyTypeID from matched article row
-- - UnitOfMeasureID from matched article default UOM
INSERT INTO `deliveryreceiptdetail` (
  `DRID`,
  `ArticleID`,
  `ItemDescription`,
  `Brand`,
  `ModelNumber`,
  `SerialNumber`,
  `PropertyTypeID`,
  `QuantityDelivered`,
  `QuantityRequested`,
  `ChangeReason`,
  `UnitOfMeasureID`,
  `UnitCost`,
  `TotalCost`,
  `InspectionStatus`,
  `deleted_at`
)
SELECT
  r.DRID,
  a.ArticleID,
  CONCAT(a.ArticleName, ' (', IFNULL(i.model_number, 'N/A'), ')') AS ItemDescription,
  i.brand,
  i.model_number,
  i.serial_number AS SerialNumber,
  a.PropertyTypeID,
  CASE WHEN i.serial_number IS NOT NULL AND i.serial_number <> '' THEN 1 ELSE i.qty_delivered END,
  CASE WHEN i.serial_number IS NOT NULL AND i.serial_number <> '' THEN 1 ELSE i.qty_delivered END,
  NULL AS ChangeReason,
  COALESCE(a.DefaultUnitOfMeasureID, 2) AS UnitOfMeasureID,
  i.unit_cost,
  ((CASE WHEN i.serial_number IS NOT NULL AND i.serial_number <> '' THEN 1 ELSE i.qty_delivered END) * i.unit_cost) AS TotalCost,
  'inspected' AS InspectionStatus,
  NULL AS deleted_at
FROM tmp_dr_items i
INNER JOIN `deliveryreceipt` r ON r.DRNumber = i.dr_number
INNER JOIN `articles` a ON a.ArticleName COLLATE utf8mb4_unicode_ci = i.article_name COLLATE utf8mb4_unicode_ci;

-- Optional verification queries (safe to keep in script output):
-- 1) Ensure each generated DR has at least 6 details.
SELECT r.DRNumber, COUNT(*) AS detail_count
FROM `deliveryreceipt` r
INNER JOIN `deliveryreceiptdetail` d ON d.DRID = r.DRID
WHERE r.DRNumber IN (
  'DR-2026-03-18-001',
  'DR-2026-03-18-002',
  'DR-2026-03-18-003',
  'DR-2026-03-18-004'
)
GROUP BY r.DRNumber
ORDER BY r.DRNumber;

-- 2) Ensure article/propertytype linkage is consistent (should return zero rows).
SELECT d.DRDetailID, d.DRID, d.ArticleID, d.PropertyTypeID AS detail_property_type, a.PropertyTypeID AS article_property_type
FROM `deliveryreceiptdetail` d
INNER JOIN `articles` a ON a.ArticleID = d.ArticleID
INNER JOIN `deliveryreceipt` r ON r.DRID = d.DRID
WHERE r.DRNumber IN (
  'DR-2026-03-18-001',
  'DR-2026-03-18-002',
  'DR-2026-03-18-003',
  'DR-2026-03-18-004'
)
AND d.PropertyTypeID <> a.PropertyTypeID;

-- 3) Enforce serial-number business rule validation (should return zero rows).
SELECT d.DRDetailID, d.DRID, d.SerialNumber, d.QuantityDelivered, d.QuantityRequested
FROM `deliveryreceiptdetail` d
INNER JOIN `deliveryreceipt` r ON r.DRID = d.DRID
WHERE r.DRNumber IN (
  'DR-2026-03-18-001',
  'DR-2026-03-18-002',
  'DR-2026-03-18-003',
  'DR-2026-03-18-004'
)
AND d.SerialNumber IS NOT NULL
AND d.SerialNumber <> ''
AND (d.QuantityDelivered <> 1 OR d.QuantityRequested <> 1);

COMMIT;
