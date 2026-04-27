-- Add/align Semi-Expendable (ICS) property types used by Property Types CRUD.
-- Safe to run multiple times.

UPDATE propertytype pt
JOIN propertyclassification pc ON pc.DocumentType = 'ICS'
SET
    pt.PropertyClassificationID = pc.PropertyClassificationID,
    pt.UpdatedDate = NOW()
WHERE pt.PropertyName IN (
    'Computer Peripherals',
    'Data Storage Devices',
    'Small Office Appliances',
    'Presentation Devices',
    'Audio Equipment (Portable)',
    'Video Equipment (Portable)',
    'Power Protection Devices',
    'Hand Tools and Instruments',
    'Portable Measuring Devices',
    'Fire Safety Devices (Portable)',
    'Portable Lighting Equipment',
    'Record Management Accessories',
    'IT Cables and Adapters',
    'Maintenance and Janitorial Tools'
);

INSERT INTO propertytype (
    PropertyClassificationID,
    PropertyName,
    Description,
    CreatedDate,
    UpdatedDate
)
SELECT
    pc.PropertyClassificationID,
    src.PropertyName,
    src.Description,
    NOW(),
    NOW()
FROM (
    SELECT 'Computer Peripherals' AS PropertyName, 'Mice, keyboards, webcams, headsets and similar accessories' AS Description
    UNION ALL SELECT 'Data Storage Devices', 'USB drives, external drives, memory cards'
    UNION ALL SELECT 'Small Office Appliances', 'Electric fans, kettles, coffee makers and similar items'
    UNION ALL SELECT 'Presentation Devices', 'Portable projectors, presenter tools and accessories'
    UNION ALL SELECT 'Audio Equipment (Portable)', 'Portable speakers, microphones and small audio devices'
    UNION ALL SELECT 'Video Equipment (Portable)', 'Action cameras and small portable video devices'
    UNION ALL SELECT 'Power Protection Devices', 'UPS, AVR and surge protectors'
    UNION ALL SELECT 'Hand Tools and Instruments', 'Small repair and maintenance tools'
    UNION ALL SELECT 'Portable Measuring Devices', 'Tape measures, multimeters and handheld meters'
    UNION ALL SELECT 'Fire Safety Devices (Portable)', 'Portable extinguishers and safety response kits'
    UNION ALL SELECT 'Portable Lighting Equipment', 'Emergency lamps, desk lamps and rechargeable lights'
    UNION ALL SELECT 'Record Management Accessories', 'Binding machines, labelers and filing accessories'
    UNION ALL SELECT 'IT Cables and Adapters', 'HDMI, LAN and power adapters for operational use'
    UNION ALL SELECT 'Maintenance and Janitorial Tools', 'Reusable janitorial tools and maintenance kits'
) src
JOIN propertyclassification pc ON pc.DocumentType = 'ICS'
WHERE NOT EXISTS (
    SELECT 1
    FROM propertytype existing
    WHERE existing.PropertyName = src.PropertyName
);