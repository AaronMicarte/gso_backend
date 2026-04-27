-- Link issuance details to RIS items and backfill existing rows for accurate RSMI sourcing.
ALTER TABLE propertyissuancedetail
    ADD COLUMN ris_item_id BIGINT UNSIGNED NULL AFTER RequestDetailID,
    ADD INDEX propertyissuancedetail_ris_item_id_index (ris_item_id),
    ADD CONSTRAINT propertyissuancedetail_ris_item_id_foreign
        FOREIGN KEY (ris_item_id)
        REFERENCES requisition_issue_slip_items(id)
        ON DELETE SET NULL;

UPDATE propertyissuancedetail pid
INNER JOIN propertyissuance pi
    ON pi.IssuanceID = pid.IssuanceID
   AND pi.deleted_at IS NULL
SET pid.ris_item_id = (
    SELECT rii.id
    FROM requisition_issue_slips ris
    INNER JOIN requisition_issue_slip_items rii
        ON rii.ris_id = ris.id
       AND rii.deleted_at IS NULL
       AND rii.asset_request_detail_id = pid.RequestDetailID
    WHERE ris.deleted_at IS NULL
      AND ris.asset_request_id = pi.RequestID
    ORDER BY
        (CASE WHEN rii.inventory_detail_id = pid.InventoryDetailID THEN 1 ELSE 0 END) DESC,
        ris.id DESC,
        rii.id ASC
    LIMIT 1
)
WHERE pid.deleted_at IS NULL
  AND pid.ris_item_id IS NULL
  AND pid.RequestDetailID IS NOT NULL;
