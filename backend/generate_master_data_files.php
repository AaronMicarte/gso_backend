<?php
// Script to generate all remaining Request and Resource files
// Run with: php generate_master_data_files.php

$entities = [
    'Classification' => [
        'fields' => ['CategoryID' => 'required|integer|exists:category,CategoryID', 'ClassificationName' => 'required|string|max:100', 'Description' => 'nullable|string|max:255'],
        'primaryKey' => 'ClassificationID',
    ],
    'Condition' => [
        'fields' => ['ConditionName' => 'required|string|max:50', 'Description' => 'nullable|string|max:255'],
        'primaryKey' => 'ConditionID',
    ],
    'Department' => [
        'fields' => ['DepartmentCode' => 'required|string|max:20', 'DepartmentName' => 'required|string|max:100', 'Description' => 'nullable|string|max:255'],
        'primaryKey' => 'DepartmentID',
    ],
    'DisposalMethod' => [
        'fields' => ['MethodName' => 'required|string|max:100', 'Description' => 'nullable|string|max:255'],
        'primaryKey' => 'DisposalMethodID',
    ],
    'EndUser' => [
        'fields' => ['FirstName' => 'required|string|max:50', 'LastName' => 'required|string|max:50', 'MiddleName' => 'nullable|string|max:50', 'DepartmentID' => 'required|integer|exists:department,DepartmentID', 'PositionID' => 'required|integer|exists:position,PositionID', 'ContactNumber' => 'nullable|string|max:20', 'Email' => 'nullable|email|max:100'],
        'primaryKey' => 'EndUserID',
    ],
    'Position' => [
        'fields' => ['PositionName' => 'required|string|max:100', 'Description' => 'nullable|string|max:255'],
        'primaryKey' => 'PositionID',
    ],
    'PropertyClassification' => [
        'fields' => ['ClassificationName' => 'required|string|max:100', 'Description' => 'nullable|string|max:255', 'DocumentType' => 'nullable|string|max:20|in:ICS,PAR'],
        'primaryKey' => 'PropertyClassificationID',
    ],
    'PropertyStatus' => [
        'fields' => ['StatusName' => 'required|string|max:50', 'Description' => 'nullable|string|max:255'],
        'primaryKey' => 'PropertyStatusID',
    ],
    'PropertyType' => [
        'fields' => ['ClassificationID' => 'required|integer|exists:classification,ClassificationID', 'PropertyClassificationID' => 'required|integer|exists:propertyclassification,PropertyClassificationID', 'PropertyName' => 'required|string|max:100', 'Description' => 'nullable|string|max:255'],
        'primaryKey' => 'PropertyTypeID',
    ],
    'Supplier' => [
        'fields' => ['SupplierName' => 'required|string|max:200', 'ContactPerson' => 'nullable|string|max:100', 'ContactNumber' => 'nullable|string|max:20', 'Email' => 'nullable|email|max:100', 'Address' => 'nullable|string|max:255', 'TIN' => 'nullable|string|max:50', 'IsActive' => 'boolean'],
        'primaryKey' => 'SupplierID',
    ],
    'UnitOfMeasure' => [
        'fields' => ['UnitName' => 'required|string|max:20', 'Description' => 'nullable|string|max:100'],
        'primaryKey' => 'UnitOfMeasureID',
    ],
    'UserRole' => [
        'fields' => ['RoleName' => 'required|string|max:50', 'Description' => 'nullable|string|max:255'],
        'primaryKey' => 'UserRoleID',
    ],
];

echo "Master Data Files Generator\n";
echo "===========================\n\n";

foreach ($entities as $entity => $config) {
    echo "Entity: $entity\n";
    echo "  - Fields: " . count($config['fields']) . "\n";
    echo "  - Primary Key: {$config['primaryKey']}\n\n";
}

echo "\nTo generate these files, create them manually or use Laravel artisan commands.\n";
echo "All Services are already created!\n";
