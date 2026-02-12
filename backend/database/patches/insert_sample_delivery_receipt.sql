-- Sample SQL to populate a Delivery Receipt (and details)
-- DateReceived: 2026-02-12
-- Adjust UserIDs / PropertyTypeID / UnitOfMeasureID if they differ in your DB.

START TRANSACTION;

INSERT INTO `deliveryreceipt`
    (`DRNumber`, `SupplierName`, `RequestedBy`, `PRNumber`, `PONumber`, `InvoiceNumber`, `ReceivedBy`, `DateReceived`, `Remarks`, `CreatedDate`, `dr_picture`)
VALUES
    ('DR-20260212-001', 'Acme Office Supplies', 3, 'PR-2026-0001', 'PO-2026-1001', 'INV-2026-9001', 1, '2026-02-12', 'Delivered complete; verify quantities', NOW(), 'dr_pictures/sample-dr-02122026.jpg');

-- Insert DR line items (uses LAST_INSERT_ID() for DRID)
INSERT INTO `deliveryreceiptdetail`
    (`DRID`, `ItemDescription`, `Brand`, `ModelNumber`, `SerialNumber`, `PropertyTypeID`, `QuantityDelivered`, `UnitOfMeasureID`, `UnitCost`, `TotalCost`)
VALUES
    (LAST_INSERT_ID(), 'Printer Toner Cartridge - Black', NULL, NULL, NULL, 9, 5, 1, 1500.00, 1500.00 * 5),
    (LAST_INSERT_ID(), 'Ergonomic Office Chair', 'ComfortSeat', 'CS-200', NULL, 1, 2, 1, 4500.00, 4500.00 * 2);

COMMIT;

-- Quick verify
SELECT dr.DRID, dr.DRNumber, dr.SupplierName, dr.DateReceived, dr.ReceivedBy, dr.dr_picture
FROM deliveryreceipt dr
WHERE dr.DRNumber = 'DR-20260212-001';

SELECT d.DRDetailID, d.DRID, d.ItemDescription, d.QuantityDelivered, d.UnitCost, d.TotalCost
FROM deliveryreceiptdetail d
JOIN deliveryreceipt r ON r.DRID = d.DRID
WHERE r.DRNumber = 'DR-20260212-001';
