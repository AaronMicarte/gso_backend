-- Mixed Delivery Receipts seed/import script
-- Purpose: create multiple Delivery Receipts containing both PPE and Semi-Expendable property articles.
-- Rules handled:
-- - If serial number exists, QuantityDelivered = 1.
-- - If no serial number exists, QuantityDelivered = QuantityRequested.
-- - Requested quantity always matches delivered quantity in this seed.
-- - Safe to import repeatedly for the same DR numbers.

START TRANSACTION;
SET NAMES utf8mb4;

DROP TEMPORARY TABLE IF EXISTS tmp_dr_headers;
DROP TEMPORARY TABLE IF EXISTS tmp_dr_items;
DROP TEMPORARY TABLE IF EXISTS tmp_dr_detail_map;
DROP TEMPORARY TABLE IF EXISTS tmp_dr_inventory_map;
DROP TEMPORARY TABLE IF EXISTS tmp_seq;
DROP TEMPORARY TABLE IF EXISTS tmp_dr_units;
DROP TEMPORARY TABLE IF EXISTS tmp_required_articles;

SET @received_by := COALESCE((
    SELECT u.UserID
    FROM `user` u
    WHERE u.Username = 'gso.admin'
    LIMIT 1
), (
    SELECT u.UserID
    FROM `user` u
    ORDER BY u.UserID ASC
    LIMIT 1
), 0);

SET @inspected_by := COALESCE((
    SELECT u.UserID
    FROM `user` u
    WHERE u.UserRoleID = 6
    ORDER BY u.UserID ASC
    LIMIT 1
), @received_by);

SET @requested_by_1 := COALESCE((
    SELECT u.UserID
    FROM `user` u
    WHERE u.UserRoleID = 4 AND u.IsActive = 1
    ORDER BY u.UserID ASC
    LIMIT 1
), @received_by);

SET @requested_by_2 := COALESCE((
    SELECT u.UserID
    FROM `user` u
    WHERE u.UserRoleID = 4 AND u.IsActive = 1
    ORDER BY u.UserID DESC
    LIMIT 1
), @requested_by_1);

-- Ensure required article master-data records exist for this mixed DR import.
CREATE TEMPORARY TABLE tmp_required_articles (
    article_name VARCHAR(255) NOT NULL PRIMARY KEY,
    property_type_name VARCHAR(255) NOT NULL,
    description VARCHAR(255) NULL,
    unit_name VARCHAR(50) NOT NULL
);

INSERT INTO tmp_required_articles (article_name, property_type_name, description, unit_name) VALUES
('Desktop Computer', 'Information and Communication Technology Equipment', 'Desktop workstation computer', 'unit'),
('Office Chair', 'Furniture and Fixtures', 'Office chair', 'unit'),
('Printer/Scanner', 'Information and Communication Technology Equipment', 'Multifunction printer and scanner', 'unit'),
('Laptop Computer', 'Information and Communication Technology Equipment', 'Portable laptop computer', 'unit'),
('Filing Cabinet', 'Furniture and Fixtures', 'Document filing cabinet', 'unit'),
('Projector', 'Information and Communication Technology Equipment', 'Presentation projector', 'unit'),
('Server Equipment', 'Information and Communication Technology Equipment', 'Server and rack equipment', 'unit'),
('Office Desk', 'Furniture and Fixtures', 'Office desk', 'unit'),
('Telephone Set', 'Communication Equipment', 'Office telephone set', 'unit'),
('Air Conditioner', 'Machinery', 'Wall mounted air conditioner', 'unit'),
('Keyboard and Mouse Bundle', 'Computer Peripherals', 'Mice and keyboard set for office and ICT use', 'set'),
('USB Flash Drive', 'Data Storage Devices', 'Portable storage device for files and transfer', 'unit'),
('Electric Kettle', 'Small Office Appliances', 'Small office appliance for pantry use', 'unit'),
('Portable Projector Kit', 'Presentation Devices', 'Portable projector with presentation accessories', 'unit'),
('Portable Speaker', 'Audio Equipment (Portable)', 'Portable audio speaker for meetings and briefings', 'unit'),
('Action Camera', 'Video Equipment (Portable)', 'Portable video capture device', 'unit'),
('Surge Protector', 'Power Protection Devices', 'Power strip with surge protection', 'unit'),
('Hand Tool Kit', 'Hand Tools and Instruments', 'Basic maintenance hand tool kit', 'set'),
('Multimeter', 'Portable Measuring Devices', 'Portable electrical measuring device', 'unit'),
('Fire Extinguisher', 'Fire Safety Devices (Portable)', 'Portable fire suppression device', 'unit'),
('Rechargeable Emergency Lamp', 'Portable Lighting Equipment', 'Rechargeable lighting device for emergency use', 'unit'),
('Labeler', 'Record Management Accessories', 'Labeling device for record management', 'unit'),
('LAN Cable Set', 'IT Cables and Adapters', 'Network cable and adapter set', 'set'),
('Cleaning Kit', 'Maintenance and Janitorial Tools', 'Cleaning kit for facilities maintenance', 'set');

UPDATE `articles` a
INNER JOIN tmp_required_articles t
    ON a.ArticleName COLLATE utf8mb4_unicode_ci = t.article_name COLLATE utf8mb4_unicode_ci
INNER JOIN `propertytype` pt
    ON pt.PropertyName COLLATE utf8mb4_unicode_ci = t.property_type_name COLLATE utf8mb4_unicode_ci
LEFT JOIN `unitofmeasure` u
    ON u.UnitName COLLATE utf8mb4_unicode_ci = t.unit_name COLLATE utf8mb4_unicode_ci
SET
    a.Description = t.description,
    a.PropertyTypeID = pt.PropertyTypeID,
    a.DefaultUnitOfMeasureID = COALESCE(u.UnitOfMeasureID, a.DefaultUnitOfMeasureID),
    a.IsActive = 1,
    a.updated_at = NOW();

INSERT INTO `articles` (
    `ArticleName`,
    `Description`,
    `ArticleImage`,
    `PropertyTypeID`,
    `DefaultUnitOfMeasureID`,
    `IsActive`,
    `created_at`,
    `updated_at`
)
SELECT
    t.article_name,
    t.description,
    NULL,
    pt.PropertyTypeID,
    COALESCE(u.UnitOfMeasureID, (
        SELECT UnitOfMeasureID
        FROM `unitofmeasure`
        WHERE UnitName = 'unit'
        ORDER BY UnitOfMeasureID ASC
        LIMIT 1
    )),
    1,
    NOW(),
    NOW()
FROM tmp_required_articles t
INNER JOIN `propertytype` pt
    ON pt.PropertyName COLLATE utf8mb4_unicode_ci = t.property_type_name COLLATE utf8mb4_unicode_ci
LEFT JOIN `unitofmeasure` u
    ON u.UnitName COLLATE utf8mb4_unicode_ci = t.unit_name COLLATE utf8mb4_unicode_ci
LEFT JOIN `articles` a
    ON a.ArticleName COLLATE utf8mb4_unicode_ci = t.article_name COLLATE utf8mb4_unicode_ci
WHERE a.ArticleID IS NULL;

CREATE TEMPORARY TABLE tmp_dr_headers (
    dr_number VARCHAR(50) NOT NULL PRIMARY KEY,
    iar_number VARCHAR(100) NOT NULL,
    requested_by INT NOT NULL,
    supplier_name VARCHAR(150) NOT NULL,
    pr_number VARCHAR(50) NOT NULL,
    po_number VARCHAR(50) NOT NULL,
    invoice_number VARCHAR(50) NOT NULL,
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
('DR-2026-04-27-001', 'IAR-2026-04-27-001', @requested_by_1, 'Tyron Corporation', 'PR-2026-MIX-1001', 'PO-2026-MIX-1001', 'INV-2026-MIX-1001', '2026-04-08', 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'uploads/dr/dr-2026-04-27-001.jpg'),
('DR-2026-04-27-002', 'IAR-2026-04-27-002', @requested_by_1, 'Cyron Suppliers', 'PR-2026-MIX-1002', 'PO-2026-MIX-1002', 'INV-2026-MIX-1002', '2026-04-12', 'Second batch of office, ICT, and maintenance items.', 'uploads/dr/dr-2026-04-27-002.jpg'),
('DR-2026-04-27-003', 'IAR-2026-04-27-003', @requested_by_2, 'Los Pollos Hermanos', 'PR-2026-MIX-1003', 'PO-2026-MIX-1003', 'INV-2026-MIX-1003', '2026-04-16', 'Mixed equipment for operations and support rooms.', 'uploads/dr/dr-2026-04-27-003.jpg'),
('DR-2026-04-27-004', 'IAR-2026-04-27-004', @requested_by_2, 'COVID Supplies', 'PR-2026-MIX-1004', 'PO-2026-MIX-1004', 'INV-2026-MIX-1004', '2026-04-20', 'Final mixed batch with serial and bulk items.', 'uploads/dr/dr-2026-04-27-004.jpg');

-- Remove any existing rows for these DR numbers so the script is re-runnable.
DELETE pid
FROM `propertyinventorydetail` pid
INNER JOIN `propertyinventory` pi ON pi.InventoryID = pid.InventoryID
INNER JOIN `deliveryreceipt` r ON r.DRID = pi.DRID
INNER JOIN tmp_dr_headers t ON t.dr_number = r.DRNumber;

DELETE pi
FROM `propertyinventory` pi
INNER JOIN `deliveryreceipt` r ON r.DRID = pi.DRID
INNER JOIN tmp_dr_headers t ON t.dr_number = r.DRNumber;

DELETE d
FROM `deliveryreceiptdetail` d
INNER JOIN `deliveryreceipt` r ON r.DRID = d.DRID
INNER JOIN tmp_dr_headers t ON t.dr_number = r.DRNumber;

DELETE r
FROM `deliveryreceipt` r
INNER JOIN tmp_dr_headers t ON t.dr_number = r.DRNumber;

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
    h.dr_number,
    h.iar_number,
    COALESCE(s.SupplierID, (SELECT SupplierID FROM `supplier` ORDER BY SupplierID ASC LIMIT 1)) AS SupplierID,
    h.requested_by,
    h.pr_number,
    h.po_number,
    h.invoice_number,
    @received_by,
    @inspected_by,
    h.date_received,
    h.date_received,
    h.remarks,
    'inspected',
    NOW(),
    h.dr_picture,
    NOW(),
    NULL
FROM tmp_dr_headers h
LEFT JOIN `supplier` s
    ON s.SupplierName COLLATE utf8mb4_unicode_ci = h.supplier_name COLLATE utf8mb4_unicode_ci;

CREATE TEMPORARY TABLE tmp_dr_items (
    item_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dr_number VARCHAR(50) NOT NULL,
    article_name VARCHAR(255) NOT NULL,
    item_description VARCHAR(255) NOT NULL,
    brand VARCHAR(100) NULL,
    model_number VARCHAR(100) NULL,
    serial_number VARCHAR(100) NULL,
    property_type_label VARCHAR(100) NOT NULL,
    unit_name VARCHAR(50) NOT NULL,
    quantity_requested INT NOT NULL,
    unit_cost DECIMAL(18,2) NOT NULL
);

INSERT INTO tmp_dr_items (
    dr_number,
    article_name,
    item_description,
    brand,
    model_number,
    serial_number,
    property_type_label,
    unit_name,
    quantity_requested,
    unit_cost
) VALUES
-- DR 1
('DR-2026-04-27-001', 'Desktop Computer', 'Lenovo ThinkCentre M70', 'Lenovo', 'M70', 'LNV2026-001', 'PPE', 'unit', 1, 42500.00),
('DR-2026-04-27-001', 'Office Chair', 'Ergonomic Mesh Chair', 'ErgoSeat', 'CH-ERG-22', 'ERGO-2026-002', 'PPE', 'unit', 1, 3200.00),
('DR-2026-04-27-001', 'Printer/Scanner', 'Brother DCP-L5650DN', 'Brother', 'DCP-L5650DN', 'BR-2026-003', 'PPE', 'unit', 1, 18900.00),
('DR-2026-04-27-001', 'Keyboard and Mouse Bundle', 'Wireless keyboard and mouse set', 'LogiTech', 'KM-2026', NULL, 'Semi Expe', 'set', 5, 1250.00),
('DR-2026-04-27-001', 'USB Flash Drive', '32GB USB 3.0 flash drive', 'SanDisk', 'UFD-32G', NULL, 'Semi Expe', 'unit', 2, 520.00),
('DR-2026-04-27-001', 'Rechargeable Emergency Lamp', 'Portable rechargeable emergency lamp', 'Firefly', 'REL-2026-A', NULL, 'Semi Expe', 'unit', 3, 980.00),

-- DR 2
('DR-2026-04-27-002', 'Laptop Computer', 'Acer TravelMate P2', 'Acer', 'TravelMate-P2', 'ACR-2026-004', 'PPE', 'unit', 1, 52000.00),
('DR-2026-04-27-002', 'Filing Cabinet', '4-Drawer Steel Filing Cabinet', 'SteelSafe', 'FC-STL-4D', NULL, 'PPE', 'unit', 2, 11500.00),
('DR-2026-04-27-002', 'Projector', 'Epson EB-X06', 'Epson', 'EB-X06', 'EPS-2026-005', 'PPE', 'unit', 1, 28500.00),
('DR-2026-04-27-002', 'Electric Kettle', '1.5L electric kettle', 'Hanabishi', 'EK-15', NULL, 'Semi Expe', 'unit', 10, 560.00),
('DR-2026-04-27-002', 'Portable Speaker', 'Portable wireless speaker', 'JBL', 'PS-BOOST', NULL, 'Semi Expe', 'unit', 4, 4200.00),
('DR-2026-04-27-002', 'Surge Protector', '6-outlet surge protector', 'Panther', 'SP-06', NULL, 'Semi Expe', 'unit', 6, 420.00),

-- DR 3
('DR-2026-04-27-003', 'Server Equipment', 'Dell EMC PowerEdge R250', 'Dell EMC', 'PowerEdge-R250', 'SRV-2026-006', 'PPE', 'unit', 1, 165000.00),
('DR-2026-04-27-003', 'Office Desk', 'Laminate Office Desk', 'Prime Office', 'OD-PRM-140', NULL, 'PPE', 'unit', 4, 8500.00),
('DR-2026-04-27-003', 'Telephone Set', 'Panasonic KX-TS880', 'Panasonic', 'KX-TS880', 'TEL-2026-007', 'PPE', 'unit', 1, 2350.00),
('DR-2026-04-27-003', 'Action Camera', 'Portable action camera', 'GoPro', 'AC-11', NULL, 'Semi Expe', 'unit', 5, 1200.00),
('DR-2026-04-27-003', 'Multimeter', 'Digital multimeter', 'Uni-T', 'UT-33D', NULL, 'Semi Expe', 'unit', 3, 650.00),
('DR-2026-04-27-003', 'Labeler', 'Handheld label maker', 'Brother', 'PT-D210', NULL, 'Semi Expe', 'unit', 8, 450.00),

-- DR 4
('DR-2026-04-27-004', 'Office Desk', 'Compact Workstation Desk', 'Prime Office', 'OD-PRM-120', NULL, 'PPE', 'unit', 3, 8400.00),
('DR-2026-04-27-004', 'Office Chair', 'Task Chair', 'ErgoSeat', 'CH-ERG-24', NULL, 'PPE', 'unit', 6, 3150.00),
('DR-2026-04-27-004', 'Air Conditioner', '1.5HP Wall Mounted AC', 'Carrier', 'Aura-1.5HP', 'CAR-2026-008', 'PPE', 'unit', 2, 33800.00),
('DR-2026-04-27-004', 'Fire Extinguisher', 'Portable fire extinguisher', 'Elide', 'FE-5LB', NULL, 'Semi Expe', 'unit', 12, 220.00),
('DR-2026-04-27-004', 'LAN Cable Set', 'RJ45 LAN cable bundle', 'Generic', 'LAN-SET-01', NULL, 'Semi Expe', 'set', 5, 980.00),
('DR-2026-04-27-004', 'Cleaning Kit', 'Janitorial cleaning kit', 'Generic', 'CK-2026', NULL, 'Semi Expe', 'set', 20, 180.00);

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
    i.item_description,
    i.brand,
    i.model_number,
    i.serial_number,
    a.PropertyTypeID,
    CASE WHEN i.serial_number IS NOT NULL AND i.serial_number <> '' THEN 1 ELSE i.quantity_requested END AS QuantityDelivered,
    i.quantity_requested AS QuantityRequested,
    NULL AS ChangeReason,
    COALESCE(u.UnitOfMeasureID, a.DefaultUnitOfMeasureID) AS UnitOfMeasureID,
    i.unit_cost AS UnitCost,
    (CASE WHEN i.serial_number IS NOT NULL AND i.serial_number <> '' THEN 1 ELSE i.quantity_requested END) * i.unit_cost AS TotalCost,
    'inspected' AS InspectionStatus,
    NULL AS deleted_at
FROM tmp_dr_items i
INNER JOIN `deliveryreceipt` r ON r.DRNumber = i.dr_number
INNER JOIN `articles` a
    ON a.ArticleName COLLATE utf8mb4_unicode_ci = i.article_name COLLATE utf8mb4_unicode_ci
LEFT JOIN `unitofmeasure` u
    ON u.UnitName COLLATE utf8mb4_unicode_ci = i.unit_name COLLATE utf8mb4_unicode_ci;

-- Create one inventory header per generated DR (re-runnable due to cleanup above).
INSERT INTO `propertyinventory` (
    `PropertyTypeID`,
    `UnitOfMeasureID`,
    `DRID`,
    `IARID`,
    `DateReceived`,
    `UnitCost`,
    `Remarks`,
    `CreatedDate`,
    `InventoryNumber`,
    `deleted_at`
)
SELECT
    NULL,
    NULL,
    r.DRID,
    NULL,
    r.DateReceived,
    NULL,
    CONCAT('Inventory record established from inspected Delivery Receipt ', r.DRNumber),
    NOW(),
    CONCAT('INV-', DATE_FORMAT(NOW(), '%Y%m%d-%H%i%s-'), r.DRID),
    NULL
FROM `deliveryreceipt` r
INNER JOIN tmp_dr_headers h ON h.dr_number = r.DRNumber;

CREATE TEMPORARY TABLE tmp_dr_inventory_map AS
SELECT
    r.DRNumber AS dr_number,
    pi.InventoryID
FROM `deliveryreceipt` r
INNER JOIN `propertyinventory` pi ON pi.DRID = r.DRID
INNER JOIN tmp_dr_headers h ON h.dr_number = r.DRNumber;

CREATE TEMPORARY TABLE tmp_dr_detail_map AS
SELECT
    i.item_id,
    r.DRID,
    d.DRDetailID,
    a.PropertyTypeID,
    d.UnitOfMeasureID,
    d.UnitCost,
    d.ItemDescription,
    d.Brand,
    d.ModelNumber,
    d.SerialNumber,
    CASE WHEN i.serial_number IS NOT NULL AND i.serial_number <> '' THEN 1 ELSE i.quantity_requested END AS unit_count
FROM tmp_dr_items i
INNER JOIN `deliveryreceipt` r ON r.DRNumber = i.dr_number
INNER JOIN `articles` a
    ON a.ArticleName COLLATE utf8mb4_unicode_ci = i.article_name COLLATE utf8mb4_unicode_ci
INNER JOIN `deliveryreceiptdetail` d
    ON d.DRID = r.DRID
   AND d.ArticleID = a.ArticleID
   AND d.ItemDescription COLLATE utf8mb4_unicode_ci = i.item_description COLLATE utf8mb4_unicode_ci
   AND COALESCE(d.Brand, '') COLLATE utf8mb4_unicode_ci = COALESCE(i.brand, '') COLLATE utf8mb4_unicode_ci
   AND COALESCE(d.ModelNumber, '') COLLATE utf8mb4_unicode_ci = COALESCE(i.model_number, '') COLLATE utf8mb4_unicode_ci
   AND COALESCE(d.SerialNumber, '') COLLATE utf8mb4_unicode_ci = COALESCE(i.serial_number, '') COLLATE utf8mb4_unicode_ci
   AND d.UnitCost = i.unit_cost;

-- Numbers table (1..500) to expand multi-quantity DR details into per-unit inventory rows.
CREATE TEMPORARY TABLE tmp_seq (n INT NOT NULL PRIMARY KEY);
INSERT INTO tmp_seq (n)
WITH RECURSIVE seq AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1
    FROM seq
    WHERE n < 500
)
SELECT n FROM seq;

CREATE TEMPORARY TABLE tmp_dr_units (
    row_no INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    dr_number VARCHAR(50) NOT NULL,
    dr_detail_id INT NOT NULL,
    property_type_id INT NULL,
    unit_of_measure_id INT NULL,
    unit_cost DECIMAL(18,2) NULL,
    item_description VARCHAR(255) NULL,
    brand VARCHAR(100) NULL,
    model_number VARCHAR(100) NULL,
    serial_number VARCHAR(100) NULL
);

INSERT INTO tmp_dr_units (
    dr_number,
    dr_detail_id,
    property_type_id,
    unit_of_measure_id,
    unit_cost,
    item_description,
    brand,
    model_number,
    serial_number
)
SELECT
    r.DRNumber,
    m.DRDetailID,
    m.PropertyTypeID,
    m.UnitOfMeasureID,
    m.UnitCost,
    m.ItemDescription,
    m.Brand,
    m.ModelNumber,
    CASE WHEN s.n = 1 THEN m.SerialNumber ELSE NULL END AS serial_number
FROM tmp_dr_detail_map m
INNER JOIN `deliveryreceipt` r ON r.DRID = m.DRID
INNER JOIN tmp_seq s ON s.n <= m.unit_count
ORDER BY
    FIELD(r.DRNumber, 'DR-2026-04-27-001', 'DR-2026-04-27-002', 'DR-2026-04-27-003', 'DR-2026-04-27-004'),
    m.DRDetailID,
    s.n;

SET @prop_year := YEAR(CURDATE());
SET @prop_counter := COALESCE((
    SELECT MAX(CAST(SUBSTRING_INDEX(pid.PropertyNumber, '-', -1) AS UNSIGNED))
    FROM `propertyinventorydetail` pid
    WHERE pid.PropertyNumber LIKE CONCAT(@prop_year, '-%')
), 0);

-- Insert per-unit inventory detail rows with gapless sequential PropertyNumber.
INSERT INTO `propertyinventorydetail` (
    `InventoryID`,
    `PropertyTypeID`,
    `ReceivedID`,
    `Description`,
    `PropertyNumber`,
    `SerialNumber`,
    `Quantity`,
    `QuantityAvailable`,
    `PropertyStatusID`,
    `UnitOfMeasureID`,
    `UnitCost`,
    `TotalCost`,
    `SourceIARDetailID`,
    `SourceDRDetailID`,
    `ConditionID`,
    `Brand`,
    `ModelNumber`,
    `Remarks`,
    `CreatedDate`,
    `UpdatedDate`,
    `deleted_at`
)
SELECT
    im.InventoryID,
    u.property_type_id,
    NULL,
    u.item_description,
    CONCAT(@prop_year, '-', LPAD((@prop_counter := @prop_counter + 1), 6, '0')),
    u.serial_number,
    1,
    1,
    1,
    u.unit_of_measure_id,
    u.unit_cost,
    u.unit_cost,
    NULL,
    u.dr_detail_id,
    1,
    u.brand,
    u.model_number,
    'Recorded from approved delivery and inspection documentation',
    NOW(),
    NULL,
    NULL
FROM tmp_dr_units u
INNER JOIN tmp_dr_inventory_map im ON im.dr_number = u.dr_number
ORDER BY u.row_no;

COMMIT;
