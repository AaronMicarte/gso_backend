-- Seed Semi-Expendable articles mapped to ICS property types.
-- Safe to run multiple times.

START TRANSACTION;
SET NAMES utf8mb4;

DROP TEMPORARY TABLE IF EXISTS tmp_semi_expe_articles;

CREATE TEMPORARY TABLE tmp_semi_expe_articles (
    article_name VARCHAR(255) NOT NULL,
    property_type_name VARCHAR(255) NOT NULL,
    description VARCHAR(255) NULL,
    unit_name VARCHAR(50) NOT NULL,
    PRIMARY KEY (article_name)
);

INSERT INTO tmp_semi_expe_articles (article_name, property_type_name, description, unit_name) VALUES
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

-- Update existing matching article names.
UPDATE `articles` a
INNER JOIN tmp_semi_expe_articles t
    ON a.ArticleName COLLATE utf8mb4_unicode_ci = t.article_name COLLATE utf8mb4_unicode_ci
INNER JOIN `propertytype` pt
    ON pt.PropertyName COLLATE utf8mb4_unicode_ci = t.property_type_name COLLATE utf8mb4_unicode_ci
INNER JOIN `propertyclassification` pc
    ON pc.PropertyClassificationID = pt.PropertyClassificationID
LEFT JOIN `unitofmeasure` u
    ON u.UnitName COLLATE utf8mb4_unicode_ci = t.unit_name COLLATE utf8mb4_unicode_ci
SET
    a.Description = t.description,
    a.PropertyTypeID = pt.PropertyTypeID,
    a.DefaultUnitOfMeasureID = COALESCE(u.UnitOfMeasureID, a.DefaultUnitOfMeasureID),
    a.IsActive = 1,
    a.updated_at = NOW()
WHERE pc.DocumentType = 'ICS';

-- Insert missing rows.
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
FROM tmp_semi_expe_articles t
INNER JOIN `propertytype` pt
    ON pt.PropertyName COLLATE utf8mb4_unicode_ci = t.property_type_name COLLATE utf8mb4_unicode_ci
INNER JOIN `propertyclassification` pc
    ON pc.PropertyClassificationID = pt.PropertyClassificationID
LEFT JOIN `unitofmeasure` u
    ON u.UnitName COLLATE utf8mb4_unicode_ci = t.unit_name COLLATE utf8mb4_unicode_ci
LEFT JOIN `articles` a
    ON a.ArticleName COLLATE utf8mb4_unicode_ci = t.article_name COLLATE utf8mb4_unicode_ci
WHERE pc.DocumentType = 'ICS'
  AND a.ArticleID IS NULL;

COMMIT;
