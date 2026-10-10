ALTER TABLE property_issuance_assignments
    ADD COLUMN IF NOT EXISTS office_name VARCHAR(255) NULL AFTER position_office,
    ADD COLUMN IF NOT EXISTS is_department_head TINYINT(1) NOT NULL DEFAULT 0 AFTER office_name;
