-- Backfill inventory headers/details for generated DR dumps that were inserted
-- directly into deliveryreceipt/deliveryreceiptdetail without running DR intake.
-- Scope: DRNumber like DR-2026-03-18-%%% and currently missing propertyinventory rows.

START TRANSACTION;

SET @to_be_issued_status_id := (
    SELECT PropertyStatusID
    FROM propertystatus
    WHERE StatusName IN ('To be issued', 'For issuance', 'For Issuance', 'Available')
    ORDER BY FIELD(StatusName, 'To be issued', 'For issuance', 'For Issuance', 'Available')
    LIMIT 1
);

SET @to_be_issued_status_id := COALESCE(@to_be_issued_status_id, 1);
SET @good_condition_id := 1;
SET @year_prefix := DATE_FORMAT(CURDATE(), '%Y-');
SET @seq := (
    SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(PropertyNumber, '-', -1) AS UNSIGNED)), 0)
    FROM propertyinventorydetail
    WHERE deleted_at IS NULL
      AND PropertyNumber REGEXP '^[0-9]{4}-[0-9]{6}$'
);

INSERT INTO propertyinventory (
    InventoryNumber,
    DRID,
    IARID,
    DateReceived,
    Remarks,
    CreatedDate
)
SELECT
    CONCAT('INV-', DATE_FORMAT(NOW(), '%Y%m%d-%H%i%s'), '-', dr.DRID) AS InventoryNumber,
    dr.DRID,
    NULL,
    COALESCE(dr.DateInspected, dr.DateReceived, CURDATE()) AS DateReceived,
    CONCAT('Backfilled from generated DR #', dr.DRNumber) AS Remarks,
    NOW() AS CreatedDate
FROM deliveryreceipt dr
LEFT JOIN propertyinventory pi
    ON pi.DRID = dr.DRID
   AND pi.deleted_at IS NULL
WHERE dr.deleted_at IS NULL
  AND dr.InspectionStatus = 'inspected'
  AND dr.DRNumber LIKE 'DR-2026-03-18-%'
  AND dr.DRNumber NOT IN ('dr21', 'dr22')
  AND pi.InventoryID IS NULL;

INSERT INTO propertyinventorydetail (
    InventoryID,
    PropertyTypeID,
    PropertyNumber,
    ConditionID,
    Brand,
    ModelNumber,
    SerialNumber,
    Description,
    Quantity,
    QuantityAvailable,
    PropertyStatusID,
    UnitOfMeasureID,
    UnitCost,
    TotalCost,
    SourceIARDetailID,
    SourceDRDetailID,
    Remarks,
    CreatedDate
)
SELECT
    pi.InventoryID,
    drd.PropertyTypeID,
    CONCAT(@year_prefix, LPAD((@seq := @seq + 1), 6, '0')) AS PropertyNumber,
    @good_condition_id AS ConditionID,
    drd.Brand,
    drd.ModelNumber,
    drd.SerialNumber,
    drd.ItemDescription,
    1 AS Quantity,
    1 AS QuantityAvailable,
    @to_be_issued_status_id AS PropertyStatusID,
    drd.UnitOfMeasureID,
    drd.UnitCost,
    drd.UnitCost AS TotalCost,
    NULL AS SourceIARDetailID,
    drd.DRDetailID AS SourceDRDetailID,
    drd.ChangeReason AS Remarks,
    NOW() AS CreatedDate
FROM deliveryreceipt dr
INNER JOIN propertyinventory pi
    ON pi.DRID = dr.DRID
   AND pi.deleted_at IS NULL
INNER JOIN deliveryreceiptdetail drd
    ON drd.DRID = dr.DRID
   AND drd.deleted_at IS NULL
INNER JOIN (
    SELECT 1 AS n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
    UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10
    UNION ALL SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15
    UNION ALL SELECT 16 UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL SELECT 20
    UNION ALL SELECT 21 UNION ALL SELECT 22 UNION ALL SELECT 23 UNION ALL SELECT 24 UNION ALL SELECT 25
    UNION ALL SELECT 26 UNION ALL SELECT 27 UNION ALL SELECT 28 UNION ALL SELECT 29 UNION ALL SELECT 30
    UNION ALL SELECT 31 UNION ALL SELECT 32 UNION ALL SELECT 33 UNION ALL SELECT 34 UNION ALL SELECT 35
    UNION ALL SELECT 36 UNION ALL SELECT 37 UNION ALL SELECT 38 UNION ALL SELECT 39 UNION ALL SELECT 40
    UNION ALL SELECT 41 UNION ALL SELECT 42 UNION ALL SELECT 43 UNION ALL SELECT 44 UNION ALL SELECT 45
    UNION ALL SELECT 46 UNION ALL SELECT 47 UNION ALL SELECT 48 UNION ALL SELECT 49 UNION ALL SELECT 50
) qty
    ON qty.n <= drd.QuantityDelivered
LEFT JOIN propertyinventorydetail pid
    ON pid.SourceDRDetailID = drd.DRDetailID
   AND pid.deleted_at IS NULL
WHERE dr.deleted_at IS NULL
  AND dr.InspectionStatus = 'inspected'
  AND dr.DRNumber LIKE 'DR-2026-03-18-%'
  AND dr.DRNumber NOT IN ('dr21', 'dr22')
  AND pid.InventoryDetailID IS NULL;

COMMIT;
