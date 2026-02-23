-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2026 at 03:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gso_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `ArticleID` bigint(20) UNSIGNED NOT NULL,
  `ArticleName` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `PropertyTypeID` int(11) NOT NULL DEFAULT 13,
  `DefaultUnitOfMeasureID` int(11) NOT NULL DEFAULT 2,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`ArticleID`, `ArticleName`, `Description`, `PropertyTypeID`, `DefaultUnitOfMeasureID`, `IsActive`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Desktop Computer', 'Non-expendable desktop computer hardware', 7, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(2, 'Laptop Computer', 'Non-expendable laptop computer', 7, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(3, 'Printer/Scanner', 'Non-expendable printing and scanning equipment', 7, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(4, 'Office Desk', 'Non-expendable office furniture - desk', 6, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(5, 'Office Chair', 'Non-expendable office furniture - chair', 6, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(6, 'Filing Cabinet', 'Non-expendable office storage furniture', 6, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(7, 'Air Conditioner', 'Non-expendable cooling equipment', 8, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(8, 'Projector', 'Non-expendable presentation equipment', 7, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(9, 'Telephone Set', 'Non-expendable communication equipment', 2, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(10, 'Server Equipment', 'Non-expendable server hardware', 7, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(11, 'Calculator', 'Semi-expendable calculation device', 11, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(12, 'Stapler', 'Semi-expendable office tool', 11, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(13, 'Hole Puncher', 'Semi-expendable office tool', 11, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(14, 'Office Supplies Set', 'Semi-expendable office supplies bundle', 11, 3, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(15, 'Table Fan', 'Semi-expendable ventilation equipment', 12, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(16, 'Extension Cord', 'Semi-expendable electrical accessory', 12, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(17, 'Office Lamp', 'Semi-expendable lighting equipment', 11, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(18, 'Waste Basket', 'Semi-expendable office accessory', 6, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(19, 'Clock/Watch', 'Semi-expendable timekeeping device', 11, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(20, 'Hand Tools Set', 'Semi-expendable maintenance tools', 12, 3, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `assetrequest`
--

CREATE TABLE `assetrequest` (
  `RequestID` bigint(20) UNSIGNED NOT NULL,
  `RequestNumber` varchar(50) NOT NULL,
  `RequestedBy` int(11) NOT NULL,
  `DepartmentID` int(11) NOT NULL,
  `Purpose` text DEFAULT NULL,
  `Status` enum('pending','approved','waiting_for_issuance','rejected','issued','received','cancelled') NOT NULL DEFAULT 'pending',
  `ReviewedBy` int(11) DEFAULT NULL,
  `ReviewedAt` timestamp NULL DEFAULT NULL,
  `ReviewNotes` text DEFAULT NULL,
  `IssuedBy` int(11) DEFAULT NULL,
  `IssuedAt` timestamp NULL DEFAULT NULL,
  `RequestDate` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assetrequestdetail`
--

CREATE TABLE `assetrequestdetail` (
  `RequestDetailID` bigint(20) UNSIGNED NOT NULL,
  `RequestID` bigint(20) UNSIGNED NOT NULL,
  `PropertyInventoryDetailID` int(11) DEFAULT NULL,
  `ItemDescription` varchar(255) DEFAULT NULL,
  `Unit` varchar(50) DEFAULT NULL,
  `QuantityRequested` int(11) NOT NULL,
  `Purpose` text NOT NULL,
  `IntendedEndUser` varchar(255) DEFAULT NULL,
  `AdditionalNotes` text DEFAULT NULL,
  `PropertyClassification` varchar(50) DEFAULT NULL,
  `AssignedEndUserID` int(11) DEFAULT NULL,
  `AssignedDepartmentID` int(11) DEFAULT NULL,
  `AdminNotes` text DEFAULT NULL,
  `IsApprovedForIssuance` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_assignments`
--

CREATE TABLE `asset_assignments` (
  `AssignmentID` bigint(20) UNSIGNED NOT NULL,
  `AssetRequestDetailID` bigint(20) UNSIGNED DEFAULT NULL,
  `PropertyInventoryDetailID` bigint(20) UNSIGNED DEFAULT NULL,
  `AssignedUserID` bigint(20) UNSIGNED DEFAULT NULL,
  `AssignedDepartmentID` bigint(20) UNSIGNED DEFAULT NULL,
  `AssignmentType` enum('request','issuance') NOT NULL,
  `PropertyClassification` enum('ICS','PAR') DEFAULT NULL,
  `AdminNotes` text DEFAULT NULL,
  `AssignedBy` bigint(20) UNSIGNED NOT NULL,
  `AssignedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset_audit_logs`
--

CREATE TABLE `asset_audit_logs` (
  `AuditID` bigint(20) UNSIGNED NOT NULL,
  `EntityType` varchar(50) NOT NULL,
  `EntityID` int(11) NOT NULL,
  `Action` varchar(100) NOT NULL,
  `PerformedBy` int(11) NOT NULL,
  `Details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`Details`)),
  `IPAddress` varchar(50) DEFAULT NULL,
  `UserAgent` text DEFAULT NULL,
  `CreatedAt` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset_audit_logs`
--

INSERT INTO `asset_audit_logs` (`AuditID`, `EntityType`, `EntityID`, `Action`, `PerformedBy`, `Details`, `IPAddress`, `UserAgent`, `CreatedAt`) VALUES
(1, 'deliveryreceiptdetail', 27, 'Inspection update', 7, '{\"dr_id\":13,\"iar_id\":5,\"reason\":\"forgot to input maybe\",\"changes\":{\"Brand\":[null,\"BrandX\"],\"ModelNumber\":[null,\"ModelX\"]}}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '2026-02-15 11:53:12'),
(2, 'property_transfer', 2, 'created', 6, '{\"transfer_number\":\"PTR-2026-0001\",\"from_department_id\":5,\"to_department_id\":3,\"items\":[14]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-16 13:59:30'),
(3, 'property_transfer', 2, 'approved', 1, '{\"transfer_number\":\"PTR-2026-0001\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-16 17:39:20'),
(4, 'property_transfer', 9, 'created', 6, '{\"transfer_number\":\"PTR-2026-0004\",\"from_department_id\":5,\"to_department_id\":3,\"items\":[15]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-17 09:06:05'),
(5, 'property_transfer', 13, 'created', 6, '{\"transfer_number\":\"PTR-2026-0004\",\"from_department_id\":5,\"to_department_id\":3,\"items\":[15]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-17 09:47:06'),
(6, 'property_transfer', 13, 'releasing_approved', 3, '{\"transfer_number\":\"PTR-2026-0004\",\"requested_count\":1,\"approved_count\":1,\"approved_quantities\":{\"18\":1},\"reason\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 13:02:34'),
(7, 'property_transfer', 13, 'approved', 1, '{\"transfer_number\":\"PTR-2026-0004\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 13:04:02'),
(8, 'property_transfer', 13, 'releasing_signed', 1, '{\"transfer_number\":\"PTR-2026-0004\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 13:44:56'),
(9, 'property_transfer', 13, 'receiving_signed', 6, '{\"transfer_number\":\"PTR-2026-0004\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-17 13:47:10'),
(10, 'property_transfer_item', 18, 'transferred', 1, '{\"transfer_id\":13,\"transfer_number\":\"PTR-2026-0004\",\"inventory_detail_id\":15,\"from_department_id\":5,\"to_department_id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 13:47:34'),
(11, 'property_transfer', 13, 'completed', 1, '{\"transfer_number\":\"PTR-2026-0004\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-17 13:47:34');

-- --------------------------------------------------------

--
-- Table structure for table `asset_request_signatures`
--

CREATE TABLE `asset_request_signatures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(20) NOT NULL COMMENT 'requester | approver | issuer',
  `signed_by_user_id` int(11) NOT NULL,
  `source` varchar(20) NOT NULL COMMENT 'canvas|upload|typed',
  `storage_disk` varchar(32) NOT NULL DEFAULT 'public',
  `storage_path` varchar(512) NOT NULL,
  `url` varchar(512) DEFAULT NULL,
  `mime` varchar(50) NOT NULL,
  `width` int(10) UNSIGNED DEFAULT NULL,
  `height` int(10) UNSIGNED DEFAULT NULL,
  `size_bytes` int(10) UNSIGNED DEFAULT NULL,
  `sha256_hash` char(64) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignedproperty`
--

CREATE TABLE `assignedproperty` (
  `PropertyID` int(11) NOT NULL,
  `InventoryDetailID` int(11) NOT NULL,
  `EndUserID` int(11) NOT NULL,
  `DepartmentID` int(11) NOT NULL,
  `PropertyCardNumber` varchar(50) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `AssignedDate` date NOT NULL,
  `PropertyStatusID` int(11) NOT NULL,
  `ConditionID` int(11) NOT NULL,
  `Remarks` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `UpdatedDate` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignedproperty`
--

INSERT INTO `assignedproperty` (`PropertyID`, `InventoryDetailID`, `EndUserID`, `DepartmentID`, `PropertyCardNumber`, `Description`, `AssignedDate`, `PropertyStatusID`, `ConditionID`, `Remarks`, `CreatedDate`, `UpdatedDate`) VALUES
(26, 1, 2, 5, 'PAR-2026-0001', 'good', '2026-02-14', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-14 03:03:37', '2026-02-16 20:57:23'),
(27, 2, 2, 5, 'PAR-2026-0001', 'good', '2026-02-14', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-14 03:03:37', '2026-02-14 03:03:37'),
(28, 3, 2, 5, 'PAR-2026-0005', 'Monitor 24\"', '2026-02-18', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-14 03:23:17', '2026-02-18 23:31:58'),
(29, 4, 2, 5, 'PAR-2026-0005', 'HDMI Cable', '2026-02-18', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-14 03:23:17', '2026-02-18 23:31:58'),
(30, 5, 2, 5, 'PAR-2026-0005', 'HDMI Cable', '2026-02-18', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-14 03:23:17', '2026-02-18 23:31:59'),
(31, 6, 2, 5, 'PAR-2026-0005', 'HDMI Cable', '2026-02-18', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-14 03:23:17', '2026-02-18 23:31:59'),
(32, 7, 2, 5, 'PAR-2026-0005', 'HDMI Cable', '2026-02-18', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-14 03:23:17', '2026-02-18 23:31:59'),
(33, 8, 2, 5, 'PAR-2026-0005', 'HDMI Cable', '2026-02-18', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-14 03:23:17', '2026-02-18 23:31:59'),
(34, 9, 2, 5, 'PAR-2026-0005', 'HDMI Cable', '2026-02-18', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-14 03:23:17', '2026-02-18 23:31:59'),
(35, 10, 2, 5, 'PAR-2026-0005', 'HDMI Cable', '2026-02-18', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-14 03:23:17', '2026-02-18 23:31:59'),
(36, 11, 2, 5, 'PAR-2026-0005', 'HDMI Cable', '2026-02-18', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-14 03:23:17', '2026-02-18 23:31:59'),
(37, 12, 2, 5, 'PAR-2026-0005', 'HDMI Cable', '2026-02-18', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-14 03:23:17', '2026-02-18 23:31:59'),
(38, 13, 2, 5, 'PAR-2026-0005', 'HDMI Cable', '2026-02-18', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-14 03:23:17', '2026-02-18 23:31:59'),
(39, 14, 2, 5, 'PAR-2026-0002', 'maka bugnaw sa egg', '2026-02-16', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-15 17:28:48', '2026-02-16 15:26:49'),
(40, 15, 4, 3, 'PTR-2026-0004', 'Office Chair', '2026-02-17', 2, 1, 'Auto-created on inspection', '2026-02-15 21:00:58', '2026-02-17 21:47:34'),
(41, 16, 2, 5, NULL, 'Desk Lamp', '2026-02-15', 1, 1, 'Auto-created on inspection', '2026-02-15 21:00:58', '2026-02-15 21:00:58'),
(42, 17, 2, 5, NULL, 'Desk Lamp', '2026-02-15', 1, 1, 'Auto-created on inspection', '2026-02-15 21:00:58', '2026-02-15 21:00:58'),
(43, 18, 2, 5, NULL, 'Desk Lamp', '2026-02-15', 1, 1, 'Auto-created on inspection', '2026-02-15 21:00:58', '2026-02-15 21:00:58'),
(44, 19, 2, 5, NULL, 'Desk Lamp', '2026-02-15', 1, 1, 'Auto-created on inspection', '2026-02-15 21:00:58', '2026-02-15 21:00:58'),
(45, 20, 4, 3, NULL, 'basta', '2026-02-16', 1, 1, 'Auto-created on inspection', '2026-02-16 21:47:19', '2026-02-16 21:47:19'),
(46, 21, 4, 3, NULL, 'basta', '2026-02-16', 1, 1, 'Auto-created on inspection', '2026-02-16 21:47:19', '2026-02-16 21:47:19'),
(47, 22, 4, 3, NULL, 'basta', '2026-02-16', 1, 1, 'Auto-created on inspection', '2026-02-16 21:47:19', '2026-02-16 21:47:19'),
(48, 23, 4, 3, NULL, 'basta', '2026-02-16', 1, 1, 'Auto-created on inspection', '2026-02-16 21:47:19', '2026-02-16 21:47:19');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `condition`
--

CREATE TABLE `condition` (
  `ConditionID` int(11) NOT NULL,
  `ConditionName` varchar(50) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `condition`
--

INSERT INTO `condition` (`ConditionID`, `ConditionName`, `Description`, `deleted_at`) VALUES
(1, 'Serviceable', 'Serviceable Assets', NULL),
(2, 'Unserviceable', 'Unserviceable Assets', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `deliveryreceipt`
--

CREATE TABLE `deliveryreceipt` (
  `DRID` int(11) NOT NULL,
  `DRNumber` varchar(50) NOT NULL,
  `SupplierName` varchar(255) DEFAULT NULL COMMENT 'Supplier name (manual entry or from supplier table)',
  `RequestedBy` int(11) DEFAULT NULL,
  `PRNumber` varchar(50) DEFAULT NULL,
  `PONumber` varchar(50) DEFAULT NULL COMMENT 'Manual PO Number reference (optional)',
  `InvoiceNumber` varchar(50) DEFAULT NULL COMMENT 'Invoice number from supplier',
  `ReceivedBy` int(11) NOT NULL,
  `DateReceived` date NOT NULL,
  `Remarks` text DEFAULT NULL,
  `InspectionStatus` enum('pending','inspected') NOT NULL DEFAULT 'pending',
  `InspectionCompletedAt` timestamp NULL DEFAULT NULL,
  `dr_picture` varchar(255) NOT NULL COMMENT 'Path to the uploaded DR picture file',
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deliveryreceipt`
--

INSERT INTO `deliveryreceipt` (`DRID`, `DRNumber`, `SupplierName`, `RequestedBy`, `PRNumber`, `PONumber`, `InvoiceNumber`, `ReceivedBy`, `DateReceived`, `Remarks`, `InspectionStatus`, `InspectionCompletedAt`, `dr_picture`, `CreatedDate`, `deleted_at`) VALUES
(7, 'DR-2026-002', 'Tech Supplies Inc.', 3, 'PR-002', 'PO-002', 'INV-002', 3, '2026-02-14', 'Wireless peripherals for office use', 'inspected', '2026-02-13 18:03:36', 'dr_pictures/sample1.png', '2026-02-14 02:00:49', NULL),
(8, 'DR-2026-003', 'Office Depot', 3, 'PR-003', 'PO-003', 'INV-003', 3, '2026-02-13', 'Stationery and office supplies', 'inspected', '2026-02-13 19:07:37', 'dr_pictures/sample2.png', '2026-02-14 02:00:49', NULL),
(9, 'DR-2026-004', 'Global Electronics', 3, 'PR-004', 'PO-004', 'INV-004', 3, '2026-02-12', 'IT equipment for department', 'inspected', '2026-02-13 19:23:16', 'dr_pictures/sample3.png', '2026-02-14 02:00:49', NULL),
(10, 'DR-2026-005', 'Stationery World', 3, 'PR-005', 'PO-005', 'INV-005', 3, '2026-02-11', 'Office furniture', 'inspected', '2026-02-15 13:00:58', 'dr_pictures/sample4.png', '2026-02-14 02:00:49', NULL),
(11, 'DR-2026-006', 'Furniture Co.', 3, 'PR-006', 'PO-006', 'INV-006', 3, '2026-02-10', 'Filing cabinets and whiteboards', 'pending', NULL, 'dr_pictures/sample5.png', '2026-02-14 02:00:49', NULL),
(12, 'DR-2026-007', 'IT Solutions Ltd.', 3, 'PR-007', 'PO-007', 'INV-007', 3, '2026-02-09', 'Laptop accessories', 'pending', NULL, 'dr_pictures/sample6.png', '2026-02-14 02:00:49', NULL),
(13, 'dr21', 'as', 3, '1', '1', '1', 1, '2026-02-14', 'good', 'pending', NULL, 'dr_pictures/kjFLcgwgy0GWHXrIO1KdwSGSjQc6leDTT9ezmBls.jpg', '2026-02-14 14:14:58', NULL),
(14, 'dr123', 'Cyron Suppliers', 3, '3', '3', '3', 1, '2026-02-15', 'goods', 'inspected', '2026-02-15 09:28:48', 'dr_pictures/4IhAGiGgGk8S5e37H1egfX4tnY0UhS5lz3q8Zys6.jpg', '2026-02-15 17:27:07', NULL),
(15, 'DR321', 'Cyron Suppliers', 6, '123', '123', '123', 1, '2026-02-16', 'good', 'inspected', '2026-02-16 13:47:19', 'dr_pictures/EB4Z6EMSxmPRtZvKuFfmICLI0gsADI6XWFFEdvzA.jpg', '2026-02-16 21:44:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `deliveryreceiptdetail`
--

CREATE TABLE `deliveryreceiptdetail` (
  `DRDetailID` int(11) NOT NULL,
  `DRID` int(11) NOT NULL,
  `ArticleID` bigint(20) UNSIGNED DEFAULT NULL,
  `ItemDescription` varchar(255) NOT NULL COMMENT 'Manual item description',
  `Brand` varchar(100) DEFAULT NULL,
  `ModelNumber` varchar(100) DEFAULT NULL,
  `SerialNumber` varchar(100) DEFAULT NULL,
  `PropertyTypeID` int(11) NOT NULL,
  `QuantityDelivered` int(11) NOT NULL,
  `UnitOfMeasureID` int(11) NOT NULL DEFAULT 1,
  `UnitCost` decimal(18,2) DEFAULT NULL,
  `TotalCost` decimal(18,2) DEFAULT NULL,
  `InspectionStatus` enum('pending','inspected') NOT NULL DEFAULT 'pending',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deliveryreceiptdetail`
--

INSERT INTO `deliveryreceiptdetail` (`DRDetailID`, `DRID`, `ArticleID`, `ItemDescription`, `Brand`, `ModelNumber`, `SerialNumber`, `PropertyTypeID`, `QuantityDelivered`, `UnitOfMeasureID`, `UnitCost`, `TotalCost`, `InspectionStatus`, `deleted_at`) VALUES
(15, 7, NULL, 'Wireless Mouse', 'Logitech', 'MX Master 3', 'LOG-MX3-001', 7, 1, 2, 500.00, 500.00, 'inspected', NULL),
(16, 7, NULL, 'Keyboard', 'Logitech', 'MX Keys', 'LOG-MXK-002', 7, 1, 2, 800.00, 800.00, 'inspected', NULL),
(17, 8, NULL, 'Printer Paper', 'Staples', 'A4-80GSM', NULL, 8, 20, 5, 200.00, 4000.00, 'inspected', NULL),
(18, 8, NULL, 'Ink Cartridges', 'HP', 'HP-564', NULL, 8, 15, 2, 300.00, 4500.00, 'inspected', NULL),
(19, 9, NULL, 'Monitor 24\"', 'Dell', 'S2421H', 'DEL-S2421H-001', 7, 1, 2, 15000.00, 15000.00, 'inspected', NULL),
(20, 9, NULL, 'HDMI Cable', 'Belkin', 'AV10115', NULL, 7, 10, 2, 200.00, 2000.00, 'inspected', NULL),
(21, 10, NULL, 'Office Chair', 'Herman Miller', 'Aeron', 'HM-AERON-001', 6, 1, 2, 5000.00, 5000.00, 'inspected', NULL),
(22, 10, NULL, 'Desk Lamp', 'IKEA', 'FLEKKE', NULL, 6, 6, 2, 1000.00, 6000.00, 'inspected', NULL),
(23, 11, NULL, 'Filing Cabinet', 'Hon', 'HFC42', 'HON-HFC42-001', 6, 1, 2, 8000.00, 8000.00, 'pending', NULL),
(24, 11, NULL, 'Whiteboard', 'Quartet', 'QRT-792', NULL, 6, 2, 2, 3000.00, 6000.00, 'pending', NULL),
(25, 12, NULL, 'Laptop Stand', 'Rain Design', 'mStand', 'RD-MST-001', 7, 1, 2, 1500.00, 1500.00, 'pending', NULL),
(26, 12, NULL, 'USB Hub', 'Anker', '7-in-1', NULL, 7, 9, 2, 400.00, 3600.00, 'pending', NULL),
(27, 13, NULL, 'Table', 'BrandX', 'ModelX', NULL, 6, 10, 7, 100.00, 1000.00, 'pending', NULL),
(28, 14, 7, 'maka bugnaw', 'ambot', 'ambotnumber', '123', 6, 1, 2, 200.00, 200.00, 'inspected', NULL),
(29, 15, 11, 'basta', 'haha', 'hehe', NULL, 11, 4, 2, 999.00, 3996.00, 'inspected', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `DepartmentID` int(11) NOT NULL,
  `DepartmentName` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `DepartmentCode` varchar(20) DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `UpdatedDate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`DepartmentID`, `DepartmentName`, `Description`, `DepartmentCode`, `IsActive`, `CreatedDate`, `UpdatedDate`, `deleted_at`) VALUES
(1, 'General Services Office', NULL, 'GSO', 1, '2025-12-05 02:07:27', NULL, NULL),
(2, 'Administrative Office', NULL, 'ADMIN', 1, '2025-12-05 02:07:27', NULL, NULL),
(3, 'Finance Department', NULL, 'FIN', 1, '2025-12-05 02:07:27', '2026-02-17 17:30:23', NULL),
(4, 'Human Resources', NULL, 'HR', 1, '2025-12-05 02:07:27', NULL, NULL),
(5, 'IT Department', NULL, 'IT', 1, '2025-12-05 02:07:27', NULL, NULL),
(17, 'Department of Health', 'Health responsible', 'DOH', 1, '2026-02-14 12:19:13', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `disposalmethod`
--

CREATE TABLE `disposalmethod` (
  `DisposalMethodID` int(11) NOT NULL,
  `MethodName` varchar(100) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disposalmethod`
--

INSERT INTO `disposalmethod` (`DisposalMethodID`, `MethodName`, `Description`, `deleted_at`) VALUES
(1, 'Public Auction', 'Sold through public bidding', NULL),
(2, 'Donation', 'Donated to qualified institutions', NULL),
(3, 'Destruction', 'Physically destroyed', NULL),
(4, 'Transfer to COA', 'Transferred to Commission on Audit', NULL),
(5, 'Condemnation', 'Officially condemned and disposed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `enduser`
--

CREATE TABLE `enduser` (
  `EndUserID` int(11) NOT NULL,
  `PositionID` int(11) NOT NULL,
  `DepartmentID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `ContactNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `UpdatedDate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enduser`
--

INSERT INTO `enduser` (`EndUserID`, `PositionID`, `DepartmentID`, `FullName`, `ContactNumber`, `Email`, `IsActive`, `CreatedDate`, `UpdatedDate`, `deleted_at`) VALUES
(2, 2, 5, 'Chokoy Covid', NULL, NULL, 1, '2026-02-13 12:21:18', '2026-02-13 12:21:18', NULL),
(4, 2, 3, 'Cyron Micarte', NULL, NULL, 1, '2026-02-16 21:47:19', '2026-02-16 21:47:19', NULL),
(5, 2, 1, 'GSO Property Custodian', NULL, NULL, 1, '2026-02-17 17:35:35', NULL, NULL),
(6, 2, 2, 'Admin Office Custodian', NULL, NULL, 1, '2026-02-17 17:35:35', NULL, NULL),
(7, 2, 4, 'HR Department Custodian', NULL, NULL, 1, '2026-02-17 17:35:35', NULL, NULL),
(8, 2, 17, 'Health Department Custodian', NULL, NULL, 1, '2026-02-17 17:35:35', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `iardetail`
--

CREATE TABLE `iardetail` (
  `IARDetailID` int(11) NOT NULL,
  `IARID` int(11) NOT NULL,
  `DRDetailID` int(11) NOT NULL,
  `PropertyTypeID` int(11) NOT NULL,
  `ConditionID` int(11) NOT NULL,
  `QuantityAccepted` int(11) NOT NULL,
  `QuantityRejected` int(11) DEFAULT 0,
  `VerifiedBrand` varchar(100) DEFAULT NULL,
  `VerifiedModel` varchar(100) DEFAULT NULL,
  `VerifiedSerial` varchar(100) DEFAULT NULL,
  `NoSerial` tinyint(1) NOT NULL DEFAULT 0,
  `Remarks` text DEFAULT NULL,
  `change_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `iardetail`
--

INSERT INTO `iardetail` (`IARDetailID`, `IARID`, `DRDetailID`, `PropertyTypeID`, `ConditionID`, `QuantityAccepted`, `QuantityRejected`, `VerifiedBrand`, `VerifiedModel`, `VerifiedSerial`, `NoSerial`, `Remarks`, `change_reason`) VALUES
(1, 1, 15, 7, 1, 1, 0, 'Logitech', 'MX Master 3', 'LOG-MX3-001', 0, 'good', NULL),
(2, 1, 16, 7, 1, 1, 0, 'Logitech', 'MX Keys', 'LOG-MXK-002', 0, 'good', NULL),
(3, 2, 17, 8, 1, 20, 0, 'Staples', 'A4-80GSM', NULL, 1, 'good', NULL),
(4, 2, 18, 8, 1, 15, 0, 'HP', 'HP-564', NULL, 1, 'good', NULL),
(5, 3, 19, 7, 1, 1, 0, 'Dell', 'S2421H', 'DEL-S2421H-001', 0, 'good', NULL),
(6, 3, 20, 7, 1, 10, 0, 'Belkin', 'AV10115', NULL, 1, 'good', NULL),
(7, 4, 28, 6, 1, 1, 0, 'ambot', 'ambotnumber', '123', 0, NULL, NULL),
(8, 5, 27, 6, 1, 9, 0, 'BrandX', 'ModelX', NULL, 1, NULL, 'forgot to input maybe'),
(9, 5, 27, 6, 2, 0, 1, 'BrandX', 'ModelX', NULL, 1, 'putol isa ka tiil XD', 'forgot to input maybe'),
(19, 6, 21, 6, 1, 1, 0, 'Herman Miller', 'Aeron', 'HM-AERON-001', 0, NULL, NULL),
(20, 6, 22, 6, 1, 4, 0, 'IKEA', 'FLEKKE', NULL, 1, NULL, NULL),
(21, 6, 22, 6, 2, 0, 2, 'IKEA', 'FLEKKE', NULL, 1, 'guba ang hehe', NULL),
(22, 7, 29, 11, 1, 4, 0, 'haha', 'hehe', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `iar_signatures`
--

CREATE TABLE `iar_signatures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `iar_id` int(11) NOT NULL,
  `role` varchar(20) NOT NULL,
  `signed_by_user_id` int(11) NOT NULL,
  `source` varchar(20) NOT NULL,
  `storage_disk` varchar(32) NOT NULL DEFAULT 'public',
  `storage_path` varchar(512) NOT NULL,
  `url` varchar(512) DEFAULT NULL,
  `mime` varchar(50) NOT NULL,
  `width` int(10) UNSIGNED DEFAULT NULL,
  `height` int(10) UNSIGNED DEFAULT NULL,
  `size_bytes` int(10) UNSIGNED DEFAULT NULL,
  `sha256_hash` char(64) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `iar_signatures`
--

INSERT INTO `iar_signatures` (`id`, `iar_id`, `role`, `signed_by_user_id`, `source`, `storage_disk`, `storage_path`, `url`, `mime`, `width`, `height`, `size_bytes`, `sha256_hash`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'inspected', 1, 'canvas', 'local', 'signatures/iar_1_inspected_1770733443.png', '/storage/signatures/iar_1_inspected_1770733443.png', 'image/png', 1000, 300, 14150, 'a55b84fb417ae714ef8a1037fda4522c595af34e50a287267739a4c62be22d97', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 14:24:03', '2026-02-10 14:24:03'),
(2, 1, 'accepted', 1, 'canvas', 'local', 'signatures/iar_1_accepted_1770733445.png', '/storage/signatures/iar_1_accepted_1770733445.png', 'image/png', 1000, 300, 14635, '40495eb52623fa5e4618bcbf51a5e11af78997828e9e36eb9b2c8844317e356d', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 14:24:03', '2026-02-10 14:24:05');

-- --------------------------------------------------------

--
-- Table structure for table `icsdetail`
--

CREATE TABLE `icsdetail` (
  `ICSDetailID` int(11) NOT NULL,
  `ICSID` int(11) NOT NULL,
  `PropertyID` int(11) NOT NULL,
  `QuantityIssued` int(11) NOT NULL,
  `UnitCost` decimal(18,2) DEFAULT NULL,
  `TotalCost` decimal(18,2) DEFAULT NULL,
  `Remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `iirup`
--

CREATE TABLE `iirup` (
  `IIRUPID` int(11) NOT NULL,
  `IIRUPNumber` varchar(50) DEFAULT NULL,
  `ReportedBy` int(11) NOT NULL,
  `ApprovedBy` int(11) DEFAULT NULL,
  `InspectionNo` varchar(50) DEFAULT NULL,
  `DateInspected` date NOT NULL,
  `DateApproved` date DEFAULT NULL,
  `AttachmentPath` varchar(255) DEFAULT NULL,
  `Status` varchar(50) DEFAULT 'Pending',
  `Remarks` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `iirupdetail`
--

CREATE TABLE `iirupdetail` (
  `IIRUPDetailID` int(11) NOT NULL,
  `IIRUPID` int(11) NOT NULL,
  `PropertyID` int(11) NOT NULL,
  `ConditionID` int(11) NOT NULL,
  `InspectedBy` int(11) NOT NULL,
  `RecommendationID` int(11) NOT NULL,
  `Remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `iiruprecommendation`
--

CREATE TABLE `iiruprecommendation` (
  `RecommendationID` int(11) NOT NULL,
  `RecommendationName` varchar(100) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `iiruprecommendation`
--

INSERT INTO `iiruprecommendation` (`RecommendationID`, `RecommendationName`, `Description`, `deleted_at`) VALUES
(1, 'For Disposal', 'Recommended for disposal', NULL),
(2, 'For Repair', 'Recommended for repair/rehabilitation', NULL),
(3, 'For Transfer', 'Recommended for transfer to other office', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inspectionacceptancereport`
--

CREATE TABLE `inspectionacceptancereport` (
  `IARID` int(11) NOT NULL,
  `IARNumber` varchar(50) DEFAULT NULL,
  `DRID` int(11) NOT NULL,
  `InspectedBy` int(11) NOT NULL,
  `AcceptedBy` int(11) DEFAULT NULL,
  `DateInspected` date NOT NULL,
  `DateAccepted` date DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inspectionacceptancereport`
--

INSERT INTO `inspectionacceptancereport` (`IARID`, `IARNumber`, `DRID`, `InspectedBy`, `AcceptedBy`, `DateInspected`, `DateAccepted`, `Remarks`, `CreatedDate`, `deleted_at`) VALUES
(1, 'IAR-2026-0001', 7, 7, 3, '2026-02-14', '2026-02-14', 'good', '2026-02-14 02:03:34', NULL),
(2, 'IAR-2026-0002', 8, 7, NULL, '2026-02-14', NULL, 'good', '2026-02-14 03:07:34', NULL),
(3, 'IAR-2026-0003', 9, 7, 3, '2026-02-14', '2026-02-18', 'good', '2026-02-14 03:23:14', NULL),
(4, 'IAR-2026-0004', 14, 7, 3, '2026-02-15', '2026-02-16', 'a', '2026-02-15 17:28:46', NULL),
(5, 'IAR-2026-0005', 13, 7, NULL, '2026-02-15', NULL, 'good', '2026-02-15 19:53:12', NULL),
(6, 'IAR-2026-0006', 10, 7, NULL, '2026-02-15', NULL, 'good', '2026-02-15 19:55:03', NULL),
(7, 'IAR-2026-0007', 15, 7, NULL, '2026-02-16', NULL, 'good', '2026-02-16 21:47:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventorycustodianslip`
--

CREATE TABLE `inventorycustodianslip` (
  `ICSID` int(11) NOT NULL,
  `ICSNumber` varchar(50) DEFAULT NULL,
  `IssuedTo` int(11) NOT NULL,
  `IssuedBy` int(11) NOT NULL,
  `DateIssued` date NOT NULL,
  `TotalCost` decimal(18,2) DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loginaudit`
--

CREATE TABLE `loginaudit` (
  `LoginAuditID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `EventTime` datetime DEFAULT current_timestamp(),
  `Status` varchar(50) DEFAULT NULL,
  `IPAddress` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loginaudit`
--

INSERT INTO `loginaudit` (`LoginAuditID`, `UserID`, `EventTime`, `Status`, `IPAddress`) VALUES
(1, 3, '2026-01-11 23:05:33', 'FailedLogin', '127.0.0.1'),
(2, 1, '2026-01-11 23:07:51', 'Logout', '127.0.0.1'),
(3, 1, '2026-01-11 23:15:02', 'Logout', '127.0.0.1'),
(4, 1, '2026-01-11 23:16:14', 'TestLogin', '127.0.0.1'),
(5, 1, '2026-01-11 23:18:22', 'Logout', '127.0.0.1'),
(6, 1, '2026-01-11 23:18:33', 'Login', '127.0.0.1'),
(7, 1, '2026-01-12 14:05:38', 'Login', '127.0.0.1'),
(8, 1, '2026-01-12 15:45:22', 'Login', '127.0.0.1'),
(9, 3, '2026-01-12 18:01:31', 'Login', '127.0.0.1'),
(10, 3, '2026-01-12 21:16:17', 'Login', '127.0.0.1'),
(11, 1, '2026-01-13 21:25:36', 'Login', '127.0.0.1'),
(12, 3, '2026-01-13 21:25:57', 'Login', '127.0.0.1'),
(13, 1, '2026-01-14 00:22:02', 'Login', '127.0.0.1'),
(14, 3, '2026-01-14 00:22:39', 'Login', '127.0.0.1'),
(15, 3, '2026-01-14 00:22:47', 'Logout', '127.0.0.1'),
(16, 1, '2026-01-14 01:42:24', 'Login', '127.0.0.1'),
(17, 1, '2026-01-14 01:42:36', 'Logout', '127.0.0.1'),
(18, 1, '2026-01-14 01:43:44', 'Login', '127.0.0.1'),
(19, 5, '2026-01-14 01:51:23', 'FailedLogin', '127.0.0.1'),
(20, 5, '2026-01-14 01:51:41', 'Login', '127.0.0.1'),
(21, 5, '2026-01-14 01:56:14', 'Logout', '127.0.0.1'),
(22, 3, '2026-01-14 01:56:33', 'Login', '127.0.0.1'),
(23, 3, '2026-01-14 01:56:53', 'Logout', '127.0.0.1'),
(24, 5, '2026-01-14 01:57:07', 'Login', '127.0.0.1'),
(25, 1, '2026-01-15 12:48:37', 'Login', '127.0.0.1'),
(26, 1, '2026-01-15 12:48:48', 'Logout', '127.0.0.1'),
(27, 1, '2026-01-15 12:54:47', 'Login', '127.0.0.1'),
(28, 3, '2026-01-15 13:46:43', 'FailedLogin', '127.0.0.1'),
(29, 3, '2026-01-15 13:46:50', 'Login', '127.0.0.1'),
(30, 1, '2026-01-15 13:47:35', 'Login', '127.0.0.1'),
(31, 1, '2026-01-15 13:47:44', 'Logout', '127.0.0.1'),
(32, 5, '2026-01-15 13:48:01', 'FailedLogin', '127.0.0.1'),
(33, 5, '2026-01-15 13:48:41', 'FailedLogin', '127.0.0.1'),
(34, 5, '2026-01-15 13:48:52', 'Login', '127.0.0.1'),
(35, 5, '2026-01-15 13:49:01', 'Logout', '127.0.0.1'),
(36, 1, '2026-01-15 13:49:39', 'Login', '127.0.0.1'),
(37, 3, '2026-01-15 14:03:01', 'Logout', '127.0.0.1'),
(38, 6, '2026-01-15 14:03:26', 'Login', '127.0.0.1'),
(39, 6, '2026-01-15 14:03:50', 'Logout', '127.0.0.1'),
(40, 3, '2026-01-15 14:04:57', 'Login', '127.0.0.1'),
(41, 1, '2026-01-15 23:44:25', 'Login', '127.0.0.1'),
(42, 3, '2026-01-16 02:03:54', 'Login', '127.0.0.1'),
(43, 1, '2026-02-09 19:58:43', 'Login', '127.0.0.1'),
(44, 1, '2026-02-10 16:08:29', 'Login', '127.0.0.1'),
(45, 1, '2026-02-10 18:08:00', 'Login', '127.0.0.1'),
(46, 1, '2026-02-11 19:57:35', 'Login', '127.0.0.1'),
(47, 1, '2026-02-11 21:14:44', 'Login', '127.0.0.1'),
(48, 1, '2026-02-12 02:33:50', 'Login', '127.0.0.1'),
(49, 1, '2026-02-12 15:07:32', 'Login', '127.0.0.1'),
(50, 3, '2026-02-12 15:08:18', 'FailedLogin', '127.0.0.1'),
(51, 3, '2026-02-12 15:08:25', 'FailedLogin', '127.0.0.1'),
(52, 3, '2026-02-12 15:08:58', 'Login', '127.0.0.1'),
(53, 3, '2026-02-12 18:27:28', 'Logout', '127.0.0.1'),
(54, 7, '2026-02-12 18:33:59', 'Login', '127.0.0.1'),
(55, 7, '2026-02-13 01:48:18', 'FailedLogin', '127.0.0.1'),
(56, 7, '2026-02-13 01:48:29', 'FailedLogin', '127.0.0.1'),
(57, 7, '2026-02-13 01:48:41', 'Login', '127.0.0.1'),
(58, 1, '2026-02-13 01:52:50', 'Login', '127.0.0.1'),
(59, 1, '2026-02-13 12:07:39', 'Login', '127.0.0.1'),
(60, 7, '2026-02-13 12:07:56', 'Login', '127.0.0.1'),
(61, 1, '2026-02-13 16:33:57', 'Login', '127.0.0.1'),
(62, 7, '2026-02-13 16:55:24', 'Login', '127.0.0.1'),
(63, 7, '2026-02-13 18:28:45', 'Logout', '127.0.0.1'),
(64, 3, '2026-02-13 18:29:08', 'Login', '127.0.0.1'),
(65, 1, '2026-02-13 22:53:24', 'Login', '127.0.0.1'),
(66, 3, '2026-02-13 23:15:52', 'Login', '127.0.0.1'),
(67, 3, '2026-02-14 01:47:23', 'Login', '127.0.0.1'),
(68, 1, '2026-02-14 01:47:33', 'Login', '127.0.0.1'),
(69, 3, '2026-02-14 02:02:12', 'Logout', '127.0.0.1'),
(70, 7, '2026-02-14 02:02:31', 'Login', '127.0.0.1'),
(71, 7, '2026-02-14 02:18:50', 'Logout', '127.0.0.1'),
(72, 3, '2026-02-14 02:19:13', 'Login', '127.0.0.1'),
(73, 3, '2026-02-14 03:05:42', 'Logout', '127.0.0.1'),
(74, 7, '2026-02-14 03:06:09', 'Login', '127.0.0.1'),
(75, 1, '2026-02-14 12:08:42', 'Login', '127.0.0.1'),
(76, 1, '2026-02-14 13:08:14', 'Logout', '127.0.0.1'),
(77, 1, '2026-02-14 14:10:38', 'Login', '127.0.0.1'),
(78, 1, '2026-02-15 13:49:55', 'Login', '127.0.0.1'),
(79, 1, '2026-02-15 16:55:03', 'Login', '127.0.0.1'),
(80, 7, '2026-02-15 17:27:48', 'Login', '127.0.0.1'),
(81, 1, '2026-02-16 14:46:00', 'Login', '127.0.0.1'),
(82, 3, '2026-02-16 15:23:26', 'Login', '127.0.0.1'),
(83, 1, '2026-02-16 16:23:43', 'Login', '127.0.0.1'),
(84, 3, '2026-02-16 16:28:20', 'Login', '127.0.0.1'),
(85, 1, '2026-02-16 17:23:38', 'Login', '127.0.0.1'),
(86, 3, '2026-02-16 17:23:55', 'Login', '127.0.0.1'),
(87, 3, '2026-02-16 21:44:35', 'Logout', '127.0.0.1'),
(88, 7, '2026-02-16 21:44:41', 'FailedLogin', '127.0.0.1'),
(89, 7, '2026-02-16 21:45:14', 'Login', '127.0.0.1'),
(90, 7, '2026-02-16 21:47:33', 'Logout', '127.0.0.1'),
(91, 6, '2026-02-16 21:48:28', 'Login', '127.0.0.1'),
(92, 1, '2026-02-17 01:36:34', 'Login', '127.0.0.1'),
(93, 3, '2026-02-17 01:37:10', 'Login', '127.0.0.1'),
(94, 3, '2026-02-17 01:39:45', 'Logout', '127.0.0.1'),
(95, 6, '2026-02-17 01:40:18', 'Login', '127.0.0.1'),
(96, 3, '2026-02-17 17:03:58', 'Login', '127.0.0.1'),
(97, 6, '2026-02-17 17:04:55', 'Login', '127.0.0.1'),
(98, 3, '2026-02-17 21:02:55', 'Logout', '127.0.0.1'),
(99, 1, '2026-02-17 21:03:25', 'Login', '127.0.0.1'),
(100, 1, '2026-02-17 21:04:50', 'Logout', '127.0.0.1'),
(101, 3, '2026-02-17 21:05:11', 'FailedLogin', '127.0.0.1'),
(102, 3, '2026-02-17 21:05:28', 'Login', '127.0.0.1'),
(103, 3, '2026-02-17 21:31:38', 'Logout', '127.0.0.1'),
(104, 1, '2026-02-17 21:32:15', 'Login', '127.0.0.1'),
(105, 1, '2026-02-18 21:24:36', 'FailedLogin', '127.0.0.1'),
(106, 1, '2026-02-18 21:24:40', 'FailedLogin', '127.0.0.1'),
(107, 1, '2026-02-18 21:24:42', 'FailedLogin', '127.0.0.1'),
(108, 1, '2026-02-18 21:24:45', 'FailedLogin', '127.0.0.1'),
(109, 1, '2026-02-18 21:24:47', 'FailedLogin', '127.0.0.1'),
(110, 1, '2026-02-18 21:47:10', 'FailedLogin', '127.0.0.1'),
(111, 1, '2026-02-18 21:47:15', 'Login', '127.0.0.1'),
(112, 6, '2026-02-18 21:54:24', 'FailedLogin', '127.0.0.1'),
(113, 3, '2026-02-18 21:56:29', 'Login', '127.0.0.1'),
(114, 3, '2026-02-18 21:56:36', 'Logout', '127.0.0.1'),
(115, 3, '2026-02-18 22:01:11', 'Login', '127.0.0.1'),
(116, 3, '2026-02-18 22:08:00', 'Login', '127.0.0.1'),
(117, 3, '2026-02-18 22:22:26', 'Logout', '127.0.0.1'),
(118, 6, '2026-02-18 22:23:03', 'Login', '127.0.0.1'),
(119, 1, '2026-02-19 00:30:23', 'Login', '127.0.0.1'),
(120, 1, '2026-02-20 15:34:45', 'Login', '127.0.0.1'),
(121, 1, '2026-02-20 17:00:24', 'Logout', '127.0.0.1'),
(122, 7, '2026-02-20 17:00:36', 'Login', '127.0.0.1');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_12_08_000000_fix_backup_codes_column', 1),
(2, '2025_12_08_000001_fix_trusted_devices_columns', 2),
(3, '2025_12_08_221000_add_soft_deletes_to_all_master_data_tables', 3),
(4, '2025_12_15_002331_fix_purchaserequestdetail_columns', 4),
(5, '2025_12_26_215407_make_accepted_by_nullable_in_inspection_acceptance_reports_table', 5),
(6, '2025_12_27_000001_create_iar_signatures_table', 6),
(7, '2025_12_28_140000_add_brand_model_serial_to_deliveryreceiptdetail', 7),
(9, '2025_12_28_140001_add_verified_columns_to_iardetail', 8),
(10, '2025_12_28_140002_add_missing_columns_to_propertyinventorydetail', 9),
(11, '2025_12_29_223854_add_deleted_at_to_propertyinventory_table', 10),
(12, '2025_12_29_234422_add_soft_deletes_to_inspectionacceptancereport_table', 11),
(13, '2025_12_28_000005_fix_inventory_keys_and_indexes', 12),
(14, '2025_12_28_000001_add_traceability_fks_to_propertyinventorydetail', 13),
(15, '2025_12_28_000002_add_inventorydetail_to_qrcode', 13),
(20, '2026_01_06_999999_drop_asset_tables', 14),
(21, '2026_01_04_100000_create_asset_request_workflow', 15),
(22, '0001_01_01_000000_create_users_table', 1),
(23, '0001_01_01_000001_create_cache_table', 1),
(24, '0001_01_01_000002_create_jobs_table', 1),
(25, '2024_01_01_000000_create_personal_access_tokens_table', 1),
(26, '', 16),
(27, '', 16),
(28, '', 16),
(29, '', 16),
(30, '', 16),
(31, '', 16),
(32, '', 16),
(33, '', 16),
(34, '', 16),
(35, '', 16),
(36, '', 16),
(37, '', 16),
(38, '', 16),
(39, '', 16),
(40, '', 16),
(41, '', 16),
(42, '', 16),
(43, '', 16),
(44, '', 16),
(45, '', 16),
(46, '', 16),
(47, '', 16),
(48, '', 16),
(49, '', 16),
(50, '2025_11_25_165649_create_departments_table', 16),
(51, '2025_11_25_165708_create_userroles_table', 16),
(52, '2025_11_25_165722_create_users_table', 16),
(53, '2025_11_25_165736_create_loginaudits_table', 16),
(54, '2025_12_05_000001_add_ipaddress_to_loginaudit_table', 16),
(55, '2025_12_05_120000_add_mfa_fields_to_users_table', 16),
(56, '2025_12_05_120001_create_trusted_devices_table', 16),
(57, '2025_12_08_220319_add_soft_deletes_to_category_table', 16),
(58, '2025_12_08_220749_add_soft_deletes_to_master_data_tables', 16),
(59, '2025_12_09_222950_add_description_to_department_table', 16),
(60, '2025_12_14_000000_create_purchase_order_tables', 16),
(61, '2025_12_24_000001_add_unit_of_measure_to_deliveryreceiptdetail', 16),
(62, '2025_12_28_000003_add_soft_deletes_to_inventory_tables', 16),
(63, '2025_12_28_000004_fix_inventory_schema_architecture', 16),
(64, '2025_12_28_000006_add_tracking_fields_to_procurement_and_inventory', 16),
(65, '2025_12_29_000000_add_supplier_invoice_unique_to_deliveryreceipt', 16),
(66, '2025_12_29_000001_add_supplier_po_unique_to_deliveryreceipt', 16),
(67, '2025_12_30_004842_add_item_description_to_iardetail_table', 16),
(68, '2025_12_30_010341_populate_item_description_in_propertyinventorydetail', 16),
(69, '2026_01_02_153325_add_dr_picture_to_deliveryreceipt_table', 16),
(70, '2026_01_04_000001_create_asset_requests_table', 16),
(71, '2026_01_04_000002_create_asset_issuance_table', 16),
(72, '2026_01_04_000003_create_asset_audit_logs_table', 16),
(73, '2026_01_04_100000_create_asset_issuance_workflow_tables', 16),
(74, '2025_11_25_165649_create_departments_table', 16),
(75, '2025_11_25_165708_create_userroles_table', 16),
(76, '2025_11_25_165722_create_users_table', 16),
(77, '2025_11_25_165736_create_loginaudits_table', 16),
(78, '2025_12_05_000001_add_ipaddress_to_loginaudit_table', 16),
(79, '2025_12_05_120000_add_mfa_fields_to_users_table', 16),
(80, '2025_12_05_120001_create_trusted_devices_table', 16),
(81, '2025_12_08_220319_add_soft_deletes_to_category_table', 16),
(82, '2025_12_08_220749_add_soft_deletes_to_master_data_tables', 16),
(83, '2025_12_09_222950_add_description_to_department_table', 16),
(84, '2025_12_14_000000_create_purchase_order_tables', 16),
(85, '2025_12_24_000001_add_unit_of_measure_to_deliveryreceiptdetail', 16),
(86, '2025_12_28_000003_add_soft_deletes_to_inventory_tables', 16),
(87, '2025_12_28_000004_fix_inventory_schema_architecture', 16),
(88, '2025_12_28_000006_add_tracking_fields_to_procurement_and_inventory', 16),
(89, '2025_12_29_000000_add_supplier_invoice_unique_to_deliveryreceipt', 16),
(90, '2025_12_29_000001_add_supplier_po_unique_to_deliveryreceipt', 16),
(91, '2025_12_30_004842_add_item_description_to_iardetail_table', 16),
(92, '2025_12_30_010341_populate_item_description_in_propertyinventorydetail', 16),
(93, '2026_01_02_153325_add_dr_picture_to_deliveryreceipt_table', 16),
(94, '2026_01_04_000001_create_asset_requests_table', 16),
(95, '2026_01_04_000002_create_asset_issuance_table', 16),
(96, '2026_01_04_000003_create_asset_audit_logs_table', 16),
(97, '2026_01_04_100000_create_asset_issuance_workflow_tables', 16),
(98, '2026_01_06_000002_add_asset_request_workflow_fields', 17),
(99, '2026_01_07_000001_update_propertyissuance_for_asset_request_workflow', 18),
(100, '2026_01_07_000002_update_propertyissuancedetail_for_asset_request_workflow', 18),
(101, '2026_01_07_000003_update_propertyissuance_add_freetext_users', 19),
(102, '2026_01_07_215230_create_asset_request_signatures_table', 20),
(103, '2026_01_08_000000_create_asset_assignments_table', 21),
(104, '2026_01_08_230729_add_item_description_to_iar_detail_table', 22),
(105, '2026_01_12_200000_create_pardetail_and_fix_par_header', 23),
(106, '2026_01_15_120000_create_requisition_issue_slips_tables', 24),
(107, '2026_01_13_000000_add_activation_fields_to_user_table', 25),
(108, '2026_01_17_000001_add_is_approved_for_issuance_to_assetrequestdetail', 26),
(109, '2026_01_17_224052_add_acknowledged_by_to_propertyissuance_table', 26),
(110, '2026_01_19_000000_add_waiting_for_issuance_to_assetrequest_status', 26),
(111, '2026_02_10_000000_add_requested_fields_to_deliveryreceipt', 26),
(112, '2026_02_10_120000_add_requires_verification_to_propertytype_table', 27),
(114, '2026_02_10_131000_drop_classification_from_propertytype', 29),
(115, '2026_02_10_140000_drop_requires_verification_from_propertytype_table', 30),
(116, '2026_02_10_150000_update_property_status_names', 31),
(117, '2026_02_11_000001_add_drid_and_issuer_signature_to_propertyissuance', 32),
(118, '2026_02_11_000002_add_requested_by_user_id_to_deliveryreceipt', 33),
(119, '2026_02_12_000001_normalize_deliveryreceipt_requestedby_fk', 34),
(120, '2026_02_12_000002_drop_requestedbylegacy_from_deliveryreceipt', 35),
(121, '2026_02_12_000002_create_inspection_reports_tables', 36),
(122, '2026_02_12_000003_add_inspection_officer_role', 36),
(123, '2026_02_12_000004_ensure_inspection_officer_role', 36),
(124, '2026_02_12_000005_add_no_serial_to_inspection_report_items', 37),
(125, '2026_02_12_000006_add_no_serial_to_iardetail', 37),
(126, '2026_02_13_000001_add_acknowledged_by_to_propertyissuance_table', 37),
(127, '2026_02_15_000001_create_articles_table', 38),
(128, '2026_02_15_000002_add_article_id_to_deliveryreceiptdetail', 38),
(129, '2026_02_16_000010_create_property_transfer_tables', 39),
(130, '2026_02_16_000020_rename_property_transfer_tables', 39),
(131, '2026_02_17_000001_backfill_property_transfer_custodians', 40),
(132, '2026_02_17_000010_add_releasing_review_fields_to_propertytransfer', 41);

-- --------------------------------------------------------

--
-- Table structure for table `par`
--

CREATE TABLE `par` (
  `PARID` int(11) NOT NULL,
  `PARNumber` varchar(50) DEFAULT NULL,
  `PropertyID` int(11) DEFAULT NULL,
  `IssuedTo` int(11) NOT NULL,
  `IssuedBy` int(11) NOT NULL,
  `DateIssued` date NOT NULL,
  `TotalCost` decimal(18,2) DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pardetail`
--

CREATE TABLE `pardetail` (
  `PARDetailID` int(10) UNSIGNED NOT NULL,
  `PARID` int(11) NOT NULL,
  `PropertyID` int(11) NOT NULL,
  `QuantityIssued` int(11) NOT NULL,
  `UnitCost` decimal(18,2) DEFAULT NULL,
  `TotalCost` decimal(18,2) DEFAULT NULL,
  `Remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `physicalcount`
--

CREATE TABLE `physicalcount` (
  `PhysicalCountID` int(11) NOT NULL,
  `SessionID` int(11) NOT NULL,
  `PropertyID` int(11) NOT NULL,
  `InventoryDetailID` int(11) NOT NULL,
  `ConditionID` int(11) NOT NULL,
  `PerformedBy` int(11) NOT NULL,
  `CountDate` datetime DEFAULT current_timestamp(),
  `ExpectedQuantity` int(11) NOT NULL,
  `QuantityCounted` int(11) NOT NULL,
  `Variance` int(11) DEFAULT NULL,
  `Remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `physicalcountsession`
--

CREATE TABLE `physicalcountsession` (
  `SessionID` int(11) NOT NULL,
  `SessionName` varchar(100) NOT NULL,
  `CreatedBy` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date DEFAULT NULL,
  `Status` varchar(50) DEFAULT 'In Progress',
  `Remarks` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `PositionID` int(11) NOT NULL,
  `PositionName` varchar(100) NOT NULL,
  `PositionDescription` varchar(255) DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `UpdatedDate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `position`
--

INSERT INTO `position` (`PositionID`, `PositionName`, `PositionDescription`, `CreatedDate`, `UpdatedDate`, `deleted_at`) VALUES
(1, 'Manager', 'Manage', '2025-12-26 17:14:09', NULL, NULL),
(2, 'Staff', 'Regular Staff Member', '2026-01-06 23:14:32', '2026-01-06 23:14:32', NULL),
(3, 'Supervisor', 'Supervisor', '2026-01-06 23:14:32', '2026-01-06 23:14:32', NULL),
(4, 'Officer', 'Department Officer', '2026-01-06 23:14:32', '2026-01-06 23:14:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `propertyclassification`
--

CREATE TABLE `propertyclassification` (
  `PropertyClassificationID` int(11) NOT NULL,
  `ClassificationName` varchar(50) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `DocumentType` varchar(20) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `propertyclassification`
--

INSERT INTO `propertyclassification` (`PropertyClassificationID`, `ClassificationName`, `Description`, `DocumentType`, `deleted_at`) VALUES
(1, 'Semi-Expendable', 'Items with shorter lifespan, lower value threshold', 'ICS', NULL),
(2, 'Non-Expendable/PPE', 'Property, Plant & Equipment with longer lifespan', 'PAR', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `propertydisposal`
--

CREATE TABLE `propertydisposal` (
  `DisposalID` int(11) NOT NULL,
  `DisposalNumber` varchar(50) DEFAULT NULL,
  `WasteMaterialID` int(11) NOT NULL,
  `ApprovedBy` int(11) NOT NULL,
  `DisposalDate` date NOT NULL,
  `DisposalMethodID` int(11) NOT NULL,
  `Remarks` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `propertydisposaldetail`
--

CREATE TABLE `propertydisposaldetail` (
  `DisposalDetailID` int(11) NOT NULL,
  `DisposalID` int(11) NOT NULL,
  `WasteDetailID` int(11) NOT NULL,
  `PropertyID` int(11) NOT NULL,
  `Remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `propertyinventory`
--

CREATE TABLE `propertyinventory` (
  `InventoryID` int(11) NOT NULL,
  `PropertyTypeID` int(11) DEFAULT NULL,
  `UnitOfMeasureID` int(11) DEFAULT NULL,
  `DRID` int(11) DEFAULT NULL,
  `IARID` int(11) DEFAULT NULL,
  `DateReceived` date DEFAULT NULL,
  `UnitCost` decimal(18,2) DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `UpdatedDate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `InventoryNumber` varchar(50) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `propertyinventory`
--

INSERT INTO `propertyinventory` (`InventoryID`, `PropertyTypeID`, `UnitOfMeasureID`, `DRID`, `IARID`, `DateReceived`, `UnitCost`, `Remarks`, `CreatedDate`, `UpdatedDate`, `InventoryNumber`, `deleted_at`) VALUES
(1, NULL, NULL, NULL, 1, '2026-02-14', NULL, 'Auto-ingested from IAR #IAR-2026-0001', '2026-02-14 03:03:36', NULL, 'INV-20260214-030336-1', NULL),
(2, NULL, NULL, NULL, 3, '2026-02-14', NULL, 'Auto-ingested from IAR #IAR-2026-0003', '2026-02-14 03:23:17', NULL, 'INV-20260214-032317-3', NULL),
(3, NULL, NULL, NULL, 4, '2026-02-15', NULL, 'Auto-ingested from IAR #IAR-2026-0004', '2026-02-15 17:28:48', NULL, 'INV-20260215-172848-4', NULL),
(4, NULL, NULL, NULL, 6, '2026-02-15', NULL, 'Auto-ingested from IAR #IAR-2026-0006', '2026-02-15 21:00:58', NULL, 'INV-20260215-210058-6', NULL),
(5, NULL, NULL, NULL, 7, '2026-02-16', NULL, 'Auto-ingested from IAR #IAR-2026-0007', '2026-02-16 21:47:19', NULL, 'INV-20260216-214719-7', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `propertyinventorydetail`
--

CREATE TABLE `propertyinventorydetail` (
  `InventoryDetailID` int(11) NOT NULL,
  `InventoryID` int(11) NOT NULL,
  `PropertyTypeID` int(11) DEFAULT NULL,
  `ReceivedID` int(11) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL COMMENT 'Item Description',
  `PropertyNumber` varchar(50) DEFAULT NULL,
  `SerialNumber` varchar(100) DEFAULT NULL,
  `Quantity` int(11) DEFAULT 1,
  `QuantityAvailable` int(11) DEFAULT 1,
  `PropertyStatusID` int(11) NOT NULL,
  `UnitOfMeasureID` int(11) DEFAULT NULL,
  `UnitCost` decimal(18,2) DEFAULT NULL,
  `TotalCost` decimal(18,2) DEFAULT NULL,
  `SourceIARDetailID` int(11) DEFAULT NULL,
  `SourceDRDetailID` int(11) DEFAULT NULL,
  `ConditionID` int(11) NOT NULL,
  `Brand` varchar(100) DEFAULT NULL,
  `ModelNumber` varchar(100) DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `UpdatedDate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `propertyinventorydetail`
--

INSERT INTO `propertyinventorydetail` (`InventoryDetailID`, `InventoryID`, `PropertyTypeID`, `ReceivedID`, `Description`, `PropertyNumber`, `SerialNumber`, `Quantity`, `QuantityAvailable`, `PropertyStatusID`, `UnitOfMeasureID`, `UnitCost`, `TotalCost`, `SourceIARDetailID`, `SourceDRDetailID`, `ConditionID`, `Brand`, `ModelNumber`, `Remarks`, `CreatedDate`, `UpdatedDate`, `deleted_at`) VALUES
(1, 1, 7, NULL, 'good', '2026-000001', 'LOG-MX3-001', 1, 0, 2, 2, 500.00, 500.00, 1, 15, 1, 'Logitech', 'MX Master 3', 'good', '2026-02-14 03:03:36', '2026-02-14 03:03:36', NULL),
(2, 1, 7, NULL, 'good', '2026-000002', 'LOG-MXK-002', 1, 0, 2, 2, 800.00, 800.00, 2, 16, 1, 'Logitech', 'MX Keys', 'good', '2026-02-14 03:03:36', '2026-02-14 03:03:37', NULL),
(3, 2, 7, NULL, 'Monitor 24\"', '2026-000003', 'DEL-S2421H-001', 1, 0, 2, 2, 15000.00, 15000.00, 5, 19, 1, 'Dell', 'S2421H', 'good', '2026-02-14 03:23:17', '2026-02-18 23:31:58', NULL),
(4, 2, 7, NULL, 'HDMI Cable', '2026-000004', NULL, 1, 0, 2, 2, 200.00, 200.00, 6, 20, 1, 'Belkin', 'AV10115', 'good', '2026-02-14 03:23:17', '2026-02-18 23:31:58', NULL),
(5, 2, 7, NULL, 'HDMI Cable', '2026-000005', NULL, 1, 0, 2, 2, 200.00, 200.00, 6, 20, 1, 'Belkin', 'AV10115', 'good', '2026-02-14 03:23:17', '2026-02-18 23:31:59', NULL),
(6, 2, 7, NULL, 'HDMI Cable', '2026-000006', NULL, 1, 0, 2, 2, 200.00, 200.00, 6, 20, 1, 'Belkin', 'AV10115', 'good', '2026-02-14 03:23:17', '2026-02-18 23:31:59', NULL),
(7, 2, 7, NULL, 'HDMI Cable', '2026-000007', NULL, 1, 0, 2, 2, 200.00, 200.00, 6, 20, 1, 'Belkin', 'AV10115', 'good', '2026-02-14 03:23:17', '2026-02-18 23:31:59', NULL),
(8, 2, 7, NULL, 'HDMI Cable', '2026-000008', NULL, 1, 0, 2, 2, 200.00, 200.00, 6, 20, 1, 'Belkin', 'AV10115', 'good', '2026-02-14 03:23:17', '2026-02-18 23:31:59', NULL),
(9, 2, 7, NULL, 'HDMI Cable', '2026-000009', NULL, 1, 0, 2, 2, 200.00, 200.00, 6, 20, 1, 'Belkin', 'AV10115', 'good', '2026-02-14 03:23:17', '2026-02-18 23:31:59', NULL),
(10, 2, 7, NULL, 'HDMI Cable', '2026-000010', NULL, 1, 0, 2, 2, 200.00, 200.00, 6, 20, 1, 'Belkin', 'AV10115', 'good', '2026-02-14 03:23:17', '2026-02-18 23:31:59', NULL),
(11, 2, 7, NULL, 'HDMI Cable', '2026-000011', NULL, 1, 0, 2, 2, 200.00, 200.00, 6, 20, 1, 'Belkin', 'AV10115', 'good', '2026-02-14 03:23:17', '2026-02-18 23:31:59', NULL),
(12, 2, 7, NULL, 'HDMI Cable', '2026-000012', NULL, 1, 0, 2, 2, 200.00, 200.00, 6, 20, 1, 'Belkin', 'AV10115', 'good', '2026-02-14 03:23:17', '2026-02-18 23:31:59', NULL),
(13, 2, 7, NULL, 'HDMI Cable', '2026-000013', NULL, 1, 0, 2, 2, 200.00, 200.00, 6, 20, 1, 'Belkin', 'AV10115', 'good', '2026-02-14 03:23:17', '2026-02-18 23:31:59', NULL),
(14, 3, 6, NULL, 'maka bugnaw sa egg', '2026-000014', '123', 1, 0, 2, 2, 200.00, 200.00, 7, 28, 1, 'ambot', 'ambotnumber', NULL, '2026-02-15 17:28:48', '2026-02-16 15:26:49', NULL),
(15, 4, 6, NULL, 'Office Chair', '2026-000015', 'HM-AERON-001', 1, 1, 1, 2, 5000.00, 5000.00, 19, 21, 1, 'Herman Miller', 'Aeron', NULL, '2026-02-15 21:00:58', NULL, NULL),
(16, 4, 6, NULL, 'Desk Lamp', '2026-000016', NULL, 1, 1, 1, 2, 1000.00, 1000.00, 20, 22, 1, 'IKEA', 'FLEKKE', NULL, '2026-02-15 21:00:58', NULL, NULL),
(17, 4, 6, NULL, 'Desk Lamp', '2026-000017', NULL, 1, 1, 1, 2, 1000.00, 1000.00, 20, 22, 1, 'IKEA', 'FLEKKE', NULL, '2026-02-15 21:00:58', NULL, NULL),
(18, 4, 6, NULL, 'Desk Lamp', '2026-000018', NULL, 1, 1, 1, 2, 1000.00, 1000.00, 20, 22, 1, 'IKEA', 'FLEKKE', NULL, '2026-02-15 21:00:58', NULL, NULL),
(19, 4, 6, NULL, 'Desk Lamp', '2026-000019', NULL, 1, 1, 1, 2, 1000.00, 1000.00, 20, 22, 1, 'IKEA', 'FLEKKE', NULL, '2026-02-15 21:00:58', NULL, NULL),
(20, 5, 11, NULL, 'basta', '2026-000020', NULL, 1, 1, 1, 2, 999.00, 999.00, 22, 29, 1, 'haha', 'hehe', NULL, '2026-02-16 21:47:19', NULL, NULL),
(21, 5, 11, NULL, 'basta', '2026-000021', NULL, 1, 1, 1, 2, 999.00, 999.00, 22, 29, 1, 'haha', 'hehe', NULL, '2026-02-16 21:47:19', NULL, NULL),
(22, 5, 11, NULL, 'basta', '2026-000022', NULL, 1, 1, 1, 2, 999.00, 999.00, 22, 29, 1, 'haha', 'hehe', NULL, '2026-02-16 21:47:19', NULL, NULL),
(23, 5, 11, NULL, 'basta', '2026-000023', NULL, 1, 1, 1, 2, 999.00, 999.00, 22, 29, 1, 'haha', 'hehe', NULL, '2026-02-16 21:47:19', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `propertyissuance`
--

CREATE TABLE `propertyissuance` (
  `IssuanceID` int(11) NOT NULL,
  `RequestID` bigint(20) UNSIGNED DEFAULT NULL,
  `DRID` int(10) UNSIGNED DEFAULT NULL,
  `IssuedBy` int(11) NOT NULL,
  `IssuerSignature` text DEFAULT NULL,
  `IssuerSignatureSource` varchar(20) DEFAULT NULL,
  `IssuedTo` int(10) UNSIGNED DEFAULT NULL,
  `IssuedToName` varchar(255) DEFAULT NULL,
  `DocumentType` varchar(10) DEFAULT NULL COMMENT 'PAR or ICS',
  `DocumentNumber` varchar(50) DEFAULT NULL,
  `Status` varchar(50) NOT NULL DEFAULT 'pending_acknowledgment' COMMENT 'pending_acknowledgment, acknowledged, completed',
  `DateIssued` date NOT NULL,
  `Remarks` text DEFAULT NULL,
  `AcknowledgmentSignature` text DEFAULT NULL COMMENT 'Base64 encoded signature',
  `AcknowledgedAt` datetime DEFAULT NULL,
  `AcknowledgedBy` int(10) UNSIGNED DEFAULT NULL,
  `AcknowledgmentNotes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `propertyissuance`
--

INSERT INTO `propertyissuance` (`IssuanceID`, `RequestID`, `DRID`, `IssuedBy`, `IssuerSignature`, `IssuerSignatureSource`, `IssuedTo`, `IssuedToName`, `DocumentType`, `DocumentNumber`, `Status`, `DateIssued`, `Remarks`, `AcknowledgmentSignature`, `AcknowledgedAt`, `AcknowledgedBy`, `AcknowledgmentNotes`, `deleted_at`, `CreatedDate`) VALUES
(1, NULL, 7, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0001', 'acknowledged', '2026-02-14', 'Auto-created from IAR acknowledgement', NULL, '2026-02-14 00:00:00', 3, NULL, NULL, '2026-02-14 03:03:36'),
(2, NULL, 14, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0002', 'acknowledged', '2026-02-16', 'Auto-created from IAR acknowledgement', NULL, '2026-02-16 00:00:00', 3, NULL, NULL, '2026-02-16 15:26:49'),
(3, NULL, 8, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0003', 'pending_acknowledgment', '2026-02-14', 'Auto-created from inspection completion', NULL, NULL, NULL, NULL, NULL, '2026-02-16 17:51:05'),
(4, NULL, 15, 7, NULL, NULL, 4, 'Cyron Micarte', 'PAR', 'PAR-2026-0004', 'pending_acknowledgment', '2026-02-16', 'Auto-created from inspection completion', NULL, NULL, NULL, NULL, NULL, '2026-02-16 21:47:19'),
(5, NULL, 9, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0005', 'acknowledged', '2026-02-18', 'Auto-created from IAR acknowledgement', NULL, '2026-02-18 00:00:00', 3, NULL, NULL, '2026-02-18 23:31:58');

-- --------------------------------------------------------

--
-- Table structure for table `propertyissuancedetail`
--

CREATE TABLE `propertyissuancedetail` (
  `IssuanceDetailID` int(11) NOT NULL,
  `IssuanceID` int(11) NOT NULL,
  `RequestDetailID` bigint(20) UNSIGNED DEFAULT NULL,
  `ItemDescription` varchar(255) DEFAULT NULL,
  `Unit` varchar(50) DEFAULT NULL,
  `InventoryDetailID` int(11) DEFAULT NULL,
  `QuantityIssued` int(11) NOT NULL,
  `Remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `propertyissuancedetail`
--

INSERT INTO `propertyissuancedetail` (`IssuanceDetailID`, `IssuanceID`, `RequestDetailID`, `ItemDescription`, `Unit`, `InventoryDetailID`, `QuantityIssued`, `Remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, NULL, 'Wireless Mouse', 'unit', 1, 1, 'Auto-created from IAR acknowledgement', '2026-02-13 19:03:36', '2026-02-13 19:03:36', NULL),
(2, 1, NULL, 'Keyboard', 'unit', 2, 1, 'Auto-created from IAR acknowledgement', '2026-02-13 19:03:37', '2026-02-13 19:03:37', NULL),
(3, 2, NULL, 'maka bugnaw', 'unit', 14, 1, 'Auto-created from IAR acknowledgement', '2026-02-16 07:26:49', '2026-02-16 07:26:49', NULL),
(4, 3, NULL, 'Printer Paper', NULL, NULL, 20, 'Auto-created from inspection completion', '2026-02-16 09:51:05', '2026-02-16 09:51:05', NULL),
(5, 3, NULL, 'Ink Cartridges', NULL, NULL, 15, 'Auto-created from inspection completion', '2026-02-16 09:51:05', '2026-02-16 09:51:05', NULL),
(6, 4, NULL, 'basta', 'unit', 20, 1, 'Auto-created from inspection completion', '2026-02-16 13:47:19', '2026-02-16 13:47:19', NULL),
(7, 4, NULL, 'basta', 'unit', 21, 1, 'Auto-created from inspection completion', '2026-02-16 13:47:19', '2026-02-16 13:47:19', NULL),
(8, 4, NULL, 'basta', 'unit', 22, 1, 'Auto-created from inspection completion', '2026-02-16 13:47:19', '2026-02-16 13:47:19', NULL),
(9, 4, NULL, 'basta', 'unit', 23, 1, 'Auto-created from inspection completion', '2026-02-16 13:47:20', '2026-02-16 13:47:20', NULL),
(10, 5, NULL, 'Monitor 24\"', 'unit', 3, 1, 'Auto-created from IAR acknowledgement', '2026-02-18 15:31:58', '2026-02-18 15:31:58', NULL),
(11, 5, NULL, 'HDMI Cable', 'unit', 4, 1, 'Auto-created from IAR acknowledgement', '2026-02-18 15:31:58', '2026-02-18 15:31:58', NULL),
(12, 5, NULL, 'HDMI Cable', 'unit', 5, 1, 'Auto-created from IAR acknowledgement', '2026-02-18 15:31:58', '2026-02-18 15:31:58', NULL),
(13, 5, NULL, 'HDMI Cable', 'unit', 6, 1, 'Auto-created from IAR acknowledgement', '2026-02-18 15:31:59', '2026-02-18 15:31:59', NULL),
(14, 5, NULL, 'HDMI Cable', 'unit', 7, 1, 'Auto-created from IAR acknowledgement', '2026-02-18 15:31:59', '2026-02-18 15:31:59', NULL),
(15, 5, NULL, 'HDMI Cable', 'unit', 8, 1, 'Auto-created from IAR acknowledgement', '2026-02-18 15:31:59', '2026-02-18 15:31:59', NULL),
(16, 5, NULL, 'HDMI Cable', 'unit', 9, 1, 'Auto-created from IAR acknowledgement', '2026-02-18 15:31:59', '2026-02-18 15:31:59', NULL),
(17, 5, NULL, 'HDMI Cable', 'unit', 10, 1, 'Auto-created from IAR acknowledgement', '2026-02-18 15:31:59', '2026-02-18 15:31:59', NULL),
(18, 5, NULL, 'HDMI Cable', 'unit', 11, 1, 'Auto-created from IAR acknowledgement', '2026-02-18 15:31:59', '2026-02-18 15:31:59', NULL),
(19, 5, NULL, 'HDMI Cable', 'unit', 12, 1, 'Auto-created from IAR acknowledgement', '2026-02-18 15:31:59', '2026-02-18 15:31:59', NULL),
(20, 5, NULL, 'HDMI Cable', 'unit', 13, 1, 'Auto-created from IAR acknowledgement', '2026-02-18 15:31:59', '2026-02-18 15:31:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `propertyreceived`
--

CREATE TABLE `propertyreceived` (
  `ReceivedID` int(11) NOT NULL,
  `IARID` int(11) NOT NULL,
  `ReceivedBy` int(11) NOT NULL,
  `ReleasedBy` int(11) NOT NULL,
  `DateReceived` date NOT NULL,
  `TotalCost` decimal(18,2) DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `propertystatus`
--

CREATE TABLE `propertystatus` (
  `PropertyStatusID` int(11) NOT NULL,
  `StatusName` varchar(50) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `propertystatus`
--

INSERT INTO `propertystatus` (`PropertyStatusID`, `StatusName`, `Description`, `deleted_at`) VALUES
(1, 'For issuance', 'In stock, ready for issuance', NULL),
(2, 'Issued', 'Currently assigned to an end-user', NULL),
(3, 'For Repair', 'Under maintenance/repair', NULL),
(4, 'For Disposal', 'Marked for disposal', NULL),
(5, 'Disposed', 'Already disposed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `propertytransfer`
--

CREATE TABLE `propertytransfer` (
  `TransferID` bigint(20) UNSIGNED NOT NULL,
  `TransferNumber` varchar(50) NOT NULL,
  `RequestedByUserID` int(11) NOT NULL,
  `FromDepartmentID` int(11) NOT NULL,
  `ToDepartmentID` int(11) NOT NULL,
  `FromEndUserID` int(11) DEFAULT NULL,
  `ToEndUserID` int(11) DEFAULT NULL,
  `Reason` text DEFAULT NULL,
  `RequestedItemsCount` int(10) UNSIGNED DEFAULT NULL,
  `ReleasingDecision` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `ReleasingApprovedCount` int(10) UNSIGNED DEFAULT NULL,
  `ReleasingDecisionReason` text DEFAULT NULL,
  `ReleasingReviewedByUserID` int(11) DEFAULT NULL,
  `ReleasingReviewedAt` timestamp NULL DEFAULT NULL,
  `Status` enum('pending','approved','rejected','completed','cancelled') NOT NULL DEFAULT 'pending',
  `ApprovedByUserID` int(11) DEFAULT NULL,
  `ApprovedAt` timestamp NULL DEFAULT NULL,
  `CompletedByUserID` int(11) DEFAULT NULL,
  `CompletedAt` timestamp NULL DEFAULT NULL,
  `BorrowerSignedAt` timestamp NULL DEFAULT NULL,
  `BorrowingSignedAt` timestamp NULL DEFAULT NULL,
  `GsoNotes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `propertytransfer`
--

INSERT INTO `propertytransfer` (`TransferID`, `TransferNumber`, `RequestedByUserID`, `FromDepartmentID`, `ToDepartmentID`, `FromEndUserID`, `ToEndUserID`, `Reason`, `RequestedItemsCount`, `ReleasingDecision`, `ReleasingApprovedCount`, `ReleasingDecisionReason`, `ReleasingReviewedByUserID`, `ReleasingReviewedAt`, `Status`, `ApprovedByUserID`, `ApprovedAt`, `CompletedByUserID`, `CompletedAt`, `BorrowerSignedAt`, `BorrowingSignedAt`, `GsoNotes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(10, 'PTR-2026-0001', 3, 5, 2, 2, 6, 'Office relocation and equipment redistribution for improved workflow efficiency', NULL, 'pending', NULL, NULL, NULL, NULL, 'approved', 1, '2026-02-16 09:30:46', NULL, NULL, NULL, NULL, 'Transfer approved pending completion of physical handover documentation', '2026-02-15 09:30:46', '2026-02-16 09:30:46', NULL),
(11, 'PTR-2026-0002', 1, 2, 17, 6, 8, 'Equipment transfer to support health department operations and compliance requirements', NULL, 'pending', NULL, NULL, NULL, NULL, 'completed', 1, '2026-02-14 09:30:46', 1, '2026-02-16 09:30:46', '2026-02-16 10:30:46', '2026-02-16 11:30:46', 'Transfer completed successfully with all required signatures obtained and inventory updated', '2026-02-13 09:30:46', '2026-02-16 09:30:46', NULL),
(12, 'PTR-2026-0003', 2, 17, 1, 8, 5, 'IT equipment consolidation for better maintenance and technical support coverage', NULL, 'pending', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-16 09:30:46', '2026-02-16 09:30:46', NULL),
(13, 'PTR-2026-0004', 6, 5, 3, 2, 4, 'Office Chair', NULL, 'approved', 1, NULL, 3, '2026-02-17 13:02:34', 'completed', 1, '2026-02-17 13:04:02', 1, '2026-02-17 13:47:34', '2026-02-17 13:44:56', '2026-02-17 13:47:10', 'Done', '2026-02-17 09:47:06', '2026-02-17 13:47:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `propertytransferdetails`
--

CREATE TABLE `propertytransferdetails` (
  `TransferItemID` bigint(20) UNSIGNED NOT NULL,
  `TransferID` bigint(20) UNSIGNED NOT NULL,
  `InventoryDetailID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 1,
  `Remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `propertytransferdetails`
--

INSERT INTO `propertytransferdetails` (`TransferItemID`, `TransferID`, `InventoryDetailID`, `Quantity`, `Remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(10, 10, 1, 1, 'Working condition, includes original packaging and documentation', '2026-02-15 09:30:46', '2026-02-15 09:30:46', NULL),
(11, 10, 2, 1, 'Excellent condition, all keys functional, includes USB receiver', '2026-02-15 09:30:46', '2026-02-15 09:30:46', NULL),
(12, 11, 3, 1, 'Perfect working condition with stand and all cables included', '2026-02-13 09:30:46', '2026-02-16 09:30:46', NULL),
(13, 11, 4, 1, 'New condition, 6ft length, supports 4K resolution', '2026-02-13 09:30:46', '2026-02-16 09:30:46', NULL),
(14, 11, 5, 1, 'Good condition, tested and verified working properly', '2026-02-13 09:30:46', '2026-02-16 09:30:46', NULL),
(15, 12, 6, 1, 'Standard condition, functional with no visible damage', '2026-02-16 09:30:46', '2026-02-16 09:30:46', NULL),
(16, 12, 7, 1, 'Serviceable condition, requires careful handling during transport', '2026-02-16 09:30:46', '2026-02-16 09:30:46', NULL),
(17, 12, 14, 1, 'Operational condition, includes warranty documentation and service manual', '2026-02-16 09:30:46', '2026-02-16 09:30:46', NULL),
(18, 13, 15, 1, NULL, '2026-02-17 09:47:06', '2026-02-17 09:47:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `propertytype`
--

CREATE TABLE `propertytype` (
  `PropertyTypeID` int(11) NOT NULL,
  `PropertyClassificationID` int(11) NOT NULL,
  `PropertyName` varchar(100) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `UpdatedDate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `propertytype`
--

INSERT INTO `propertytype` (`PropertyTypeID`, `PropertyClassificationID`, `PropertyName`, `Description`, `CreatedDate`, `UpdatedDate`, `deleted_at`) VALUES
(1, 2, 'Agricultural and Forestry Equipment', 'Equipment used for farming, gardening, and forestry operations', '2026-02-10 18:35:54', '2026-02-10 20:14:24', NULL),
(2, 2, 'Communication Equipment', 'Devices for transmitting and receiving information (e.g., radios, telephones)', '2026-02-10 18:35:54', '2026-02-10 20:14:24', NULL),
(3, 2, 'Computer Software', 'Licensed software applications and operating systems', '2026-02-10 18:35:54', '2026-02-10 18:35:54', NULL),
(4, 2, 'Construction and Heavy Equipment', 'Large machinery for construction and earth-moving activities', '2026-02-10 18:35:54', '2026-02-10 20:14:24', NULL),
(5, 2, 'Disaster Response and Rescue Equipment', 'Specialized tools and equipment for emergency and rescue operations', '2026-02-10 18:35:54', '2026-02-10 20:14:24', NULL),
(6, 2, 'Furniture and Fixtures', 'Office and institutional furniture such as desks, chairs, cabinets', '2026-02-10 18:35:54', '2026-02-10 18:35:54', NULL),
(7, 2, 'Information and Communication Technology Equipment', 'IT hardware like computers, laptops, printers, and network devices', '2026-02-10 18:35:54', '2026-02-10 20:14:24', NULL),
(8, 2, 'Machinery', 'Mechanical devices and machines used in operations', '2026-02-10 18:35:54', '2026-02-10 20:14:24', NULL),
(9, 2, 'Marine and Fishery Equipment', 'Equipment for marine, aquatic, and fishery activities', '2026-02-10 18:35:54', '2026-02-10 20:14:24', NULL),
(10, 2, 'Medical Equipment', 'Devices and instruments for medical and healthcare use', '2026-02-10 18:35:54', '2026-02-10 20:14:24', NULL),
(11, 2, 'Office Equipment', 'Non-IT office machines such as typewriters, calculators, shredders', '2026-02-10 18:35:54', '2026-02-10 20:14:24', NULL),
(12, 2, 'Other Machinery and Equipment', 'Miscellaneous machinery not classified elsewhere', '2026-02-10 18:35:54', '2026-02-10 20:14:24', NULL),
(13, 2, 'Other Property, Plant and Equipment', 'Other tangible assets with long useful life', '2026-02-10 18:35:54', '2026-02-10 20:14:24', NULL),
(14, 2, 'Technical and Scientific Equipment', 'Specialized equipment for technical, laboratory, or scientific work', '2026-02-10 18:35:54', '2026-02-10 20:57:37', NULL),
(15, 1, 'test - 1', 'test', '2026-02-14 12:56:17', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `qrcode`
--

CREATE TABLE `qrcode` (
  `QRCodeID` int(11) NOT NULL,
  `PropertyID` int(11) NOT NULL,
  `InventoryDetailID` int(10) UNSIGNED DEFAULT NULL,
  `DepartmentID` int(11) NOT NULL,
  `QRCodeData` varchar(255) DEFAULT NULL,
  `DateGenerated` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qrcode`
--

INSERT INTO `qrcode` (`QRCodeID`, `PropertyID`, `InventoryDetailID`, `DepartmentID`, `QRCodeData`, `DateGenerated`) VALUES
(1, 28, 3, 5, 'http://localhost:3000/qr/scan/28?h=582856aaeb93', '2026-02-14 03:23:17'),
(2, 29, 4, 5, 'http://localhost:3000/qr/scan/29?h=67b7e9a4b1f3', '2026-02-14 03:23:17'),
(3, 30, 5, 5, 'http://localhost:3000/qr/scan/30?h=d6896e269f53', '2026-02-14 03:23:17'),
(4, 31, 6, 5, 'http://localhost:3000/qr/scan/31?h=ec40047ce8f9', '2026-02-14 03:23:17'),
(5, 32, 7, 5, 'http://localhost:3000/qr/scan/32?h=f7d1d50763e3', '2026-02-14 03:23:17'),
(6, 33, 8, 5, 'http://localhost:3000/qr/scan/33?h=1fd2068b4c4b', '2026-02-14 03:23:17'),
(7, 34, 9, 5, 'http://localhost:3000/qr/scan/34?h=c54deffee3ca', '2026-02-14 03:23:17'),
(8, 35, 10, 5, 'http://localhost:3000/qr/scan/35?h=c4d4216993be', '2026-02-14 03:23:17'),
(9, 36, 11, 5, 'http://localhost:3000/qr/scan/36?h=ce464a891374', '2026-02-14 03:23:17'),
(10, 37, 12, 5, 'http://localhost:3000/qr/scan/37?h=aec340323004', '2026-02-14 03:23:17'),
(11, 38, 13, 5, 'http://localhost:3000/qr/scan/38?h=ebab3c505b56', '2026-02-14 03:23:17'),
(12, 26, 1, 5, 'http://localhost:3000/qr/scan/26?h=3d36c6fca545', '2026-02-15 14:24:06'),
(13, 27, 2, 5, 'http://localhost:3000/qr/scan/27?h=0a46d32f47b3', '2026-02-15 14:24:06'),
(14, 39, 14, 5, 'http://localhost:3000/qr/scan/39?h=dcf9cac0f3f6', '2026-02-15 17:28:48'),
(15, 40, 15, 3, 'http://localhost:8000/qr/scan/40?h=116f7c7f1e62', '2026-02-17 21:47:34'),
(16, 41, 16, 5, 'http://localhost:3000/qr/scan/41?h=bd121e766ed1', '2026-02-15 21:00:58'),
(17, 42, 17, 5, 'http://localhost:3000/qr/scan/42?h=479c8d8c55ce', '2026-02-15 21:00:58'),
(18, 43, 18, 5, 'http://localhost:3000/qr/scan/43?h=3b97c4650685', '2026-02-15 21:00:58'),
(19, 44, 19, 5, 'http://localhost:3000/qr/scan/44?h=4c2eebf5b925', '2026-02-15 21:00:58'),
(20, 45, 20, 3, 'http://localhost:3000/qr/scan/45?h=c4807b78f58a', '2026-02-16 21:47:19'),
(21, 46, 21, 3, 'http://localhost:3000/qr/scan/46?h=46cc4d5d18b8', '2026-02-16 21:47:19'),
(22, 47, 22, 3, 'http://localhost:3000/qr/scan/47?h=8193cece10b6', '2026-02-16 21:47:19'),
(23, 48, 23, 3, 'http://localhost:3000/qr/scan/48?h=f858bdc6ffb7', '2026-02-16 21:47:19');

-- --------------------------------------------------------

--
-- Table structure for table `requisition_issue_slips`
--

CREATE TABLE `requisition_issue_slips` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `asset_request_id` bigint(20) UNSIGNED NOT NULL,
  `ris_number` varchar(20) NOT NULL,
  `entity_name` varchar(255) NOT NULL,
  `fund_cluster` varchar(50) NOT NULL,
  `division` varchar(255) NOT NULL,
  `responsibility_center_code` varchar(50) NOT NULL,
  `purpose` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'for_issuance',
  `requested_by_user_id` int(11) NOT NULL,
  `requested_at` datetime NOT NULL,
  `approved_by_user_id` int(11) NOT NULL,
  `approved_at` datetime NOT NULL,
  `prepared_by_user_id` int(11) NOT NULL,
  `prepared_at` datetime NOT NULL,
  `issued_by_user_id` int(11) DEFAULT NULL,
  `issued_at` datetime DEFAULT NULL,
  `received_by_user_id` int(11) DEFAULT NULL,
  `received_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_issue_slip_items`
--

CREATE TABLE `requisition_issue_slip_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ris_id` bigint(20) UNSIGNED NOT NULL,
  `asset_request_detail_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_detail_id` int(11) DEFAULT NULL,
  `stock_no` varchar(80) NOT NULL DEFAULT 'N/A',
  `unit` varchar(50) NOT NULL DEFAULT 'N/A',
  `description` varchar(255) NOT NULL DEFAULT 'N/A',
  `quantity_requested` int(10) UNSIGNED NOT NULL,
  `stock_available` tinyint(1) NOT NULL DEFAULT 0,
  `quantity_issued` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `issue_remarks` varchar(255) NOT NULL DEFAULT '-',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_issue_slip_signatures`
--

CREATE TABLE `requisition_issue_slip_signatures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ris_id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(30) NOT NULL,
  `signed_by_user_id` int(11) NOT NULL,
  `source` varchar(20) NOT NULL,
  `storage_disk` varchar(32) NOT NULL DEFAULT 'public',
  `storage_path` varchar(512) NOT NULL,
  `url` varchar(512) DEFAULT NULL,
  `mime` varchar(50) NOT NULL DEFAULT 'image/png',
  `width` int(10) UNSIGNED DEFAULT NULL,
  `height` int(10) UNSIGNED DEFAULT NULL,
  `size_bytes` int(10) UNSIGNED DEFAULT NULL,
  `sha256_hash` char(64) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('dUnfLgjLW6vK20Fz6ddc1H9hEswcXmP8yxpsbc2R', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZDluem5hdXRBREZWSnFWcWx2M0duSTc0V2JIMVNyRDdsdXdDTU1SOSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvdXNlciI7czo1OiJyb3V0ZSI7Tjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NztzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJGZlcFNCNklHYk9YZjduV3Z3VURiMnVKanIyRkpPMDdja2JlMWNIRkRrTjkvaFNxbnUucVFlIjt9', 1771578048);

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `SupplierID` int(11) NOT NULL,
  `SupplierName` varchar(100) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `ContactPerson` varchar(100) DEFAULT NULL,
  `ContactNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `UpdatedDate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`SupplierID`, `SupplierName`, `Address`, `ContactPerson`, `ContactNumber`, `Email`, `IsActive`, `CreatedDate`, `UpdatedDate`, `deleted_at`) VALUES
(1, 'Tyron Corporation', 'Zone 7, Molugan, El Salvador City', 'Tyron Micarte', '09476971709', 'micarte2010@gmail.com', 1, '2025-12-14 13:00:36', NULL, NULL),
(2, 'Los Pollos Hermanos', 'Albaquerque, New Mexico', 'Gus Fring', '09925077173', 'lospolloshermanos@gmail.com', 1, '2025-12-14 13:25:54', NULL, NULL),
(3, 'Cyron Suppliers', 'Lapasan, Cagayan de Oro City, Philippines, 9000', 'Cyron Micarte', '09925077173', 'cyronmicarte@gmail.com', 1, '2025-12-26 15:58:19', '2025-12-26 15:58:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `trusted_devices`
--

CREATE TABLE `trusted_devices` (
  `DeviceID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `DeviceName` varchar(255) NOT NULL,
  `DeviceToken` varchar(255) NOT NULL,
  `UserAgent` text DEFAULT NULL,
  `IPAddress` varchar(45) DEFAULT NULL,
  `LastUsedAt` datetime DEFAULT NULL,
  `ExpiresAt` datetime NOT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trusted_devices`
--

INSERT INTO `trusted_devices` (`DeviceID`, `UserID`, `DeviceName`, `DeviceToken`, `UserAgent`, `IPAddress`, `LastUsedAt`, `ExpiresAt`, `CreatedDate`, `UpdatedAt`) VALUES
(5, 2, 'Chrome on Windows', '5d38ec8112a241ac51bd1054287359f13db93eddbeeceedc2c00a75fa7254c62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '::1', '2025-12-26 01:58:59', '2026-01-25 01:50:51', '2025-12-26 01:50:51', '2025-12-26 01:58:59'),
(7, 2, 'Chrome on Windows', '5da659491728cf8cce8b7047ae8042604fa041012793d906516d7da35c598b76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '::1', NULL, '2026-01-25 15:40:17', '2025-12-26 15:40:17', NULL),
(10, 2, 'Chrome on Windows', '70d67ccb4d54238fd7f39e5ad25e384d4994f23129b871c4504abfe1c2eac54c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-01-25 20:25:30', '2025-12-26 20:25:30', NULL),
(12, 2, 'Chrome on Windows', '94ada0a3e302afb58e6232d466783a443e42cebe1112dd4db70561c0d19e695f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-01-25 21:31:20', '2025-12-26 21:31:20', NULL),
(14, 2, 'Chrome on Windows', 'e78fd6567203219bca58ec8d6a4adf51d41fded34533b12ec668b0c59ee7debf', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2025-12-26 22:14:13', '2026-01-25 21:46:23', '2025-12-26 21:46:23', '2025-12-26 22:14:13'),
(16, 2, 'Chrome on Windows', 'c6ba2be624d0c8d1fbd27bb47776e15e631a2c05adb6de82b08e62ab3d5ba446', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-01-25 22:37:45', '2025-12-26 22:37:45', NULL),
(23, 3, 'Chrome on Windows', '661b7c08408ac1210c7701aab8ec10d1d57a766e9ff7bcac8e552ce9d07243c8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-01-06 01:20:13', '2026-02-04 02:02:12', '2026-01-05 02:02:12', '2026-01-06 01:20:13'),
(31, 3, 'Chrome on Windows', '52e0cb436bf5ccc37650430c253127446352b19371e66ea7f813698234b41d6a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-05 20:32:24', '2026-01-06 20:32:24', NULL),
(33, 3, 'Chrome on Windows', '2f9c885621f62598e3337678cb904854c3770216999d4f7e0ecfe4cd74c2f304', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-05 22:30:05', '2026-01-06 22:30:05', NULL),
(35, 3, 'Chrome on Windows', '8c40a388046d06eb74145c85b46ed400eaaa67fffdda7bbfe1cf24407091e31d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-06 00:50:54', '2026-01-07 00:50:54', NULL),
(37, 3, 'Trusted Device', '46026b980d69474966efc08c8d8e46e9ba54d1845f0cddd74995f0c06dcd5b64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-07 15:27:27', '2026-01-08 15:27:27', NULL),
(39, 3, 'Trusted Device', 'e32b2bb39edf7e9b1f806dbec3ce22790f9c93c125c2837474a97a1d06f39e11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-08 01:41:38', '2026-01-09 01:41:38', NULL),
(41, 3, 'Trusted Device', 'b9b6280c8d3492730f8b5feaf7dd4a87d214f799ae965948e4569229fad77d3e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-08 01:55:58', '2026-01-09 01:55:58', NULL),
(43, 3, 'Trusted Device', 'c3de9ff5c8c252d4de86ef3d1dac8d7849d9b0f76aa5913fab9d30605df8fcfc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-08 18:05:33', '2026-01-09 18:05:33', NULL),
(45, 3, 'Trusted Device', 'd9aabe273e94fad890fbbdf829641d4dda199a11d2c22783ddad6c10b8305e9b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-08 19:28:45', '2026-01-09 19:28:45', NULL),
(47, 3, 'Trusted Device', 'eae6674b11022076b0035d0d920e279095e28395d698de8a44a05ebbe2d39906', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-08 19:32:31', '2026-01-09 19:32:31', NULL),
(51, 3, 'Trusted Device', '60e9d74b1d471b38ad962bf4cde63e18a6d61029137a5e0530c1dda35ade70e5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '127.0.0.1', '2026-01-14 00:22:39', '2026-02-10 23:06:54', '2026-01-11 23:06:54', '2026-01-14 00:22:39'),
(56, 5, 'Trusted Device', 'ebd44e7fff4edd2d4b2ba6788921da6d4ea579a82cf44d8bd90b78aa28c21f01', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '127.0.0.1', NULL, '2026-02-13 01:51:41', '2026-01-14 01:51:41', NULL),
(57, 3, 'Trusted Device', 'cb5e463b2ed9df21777a8fb8bad06c95e437467e2b6ef215288e801c4fea3c45', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '127.0.0.1', NULL, '2026-02-13 01:56:33', '2026-01-14 01:56:33', NULL),
(58, 5, 'Trusted Device', 'a1e9e29d449dca75b2cd9e4501485744923a5ae5f736e349a20344e7ba8b4fe8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '127.0.0.1', NULL, '2026-02-13 01:57:07', '2026-01-14 01:57:07', NULL),
(60, 3, 'Trusted Device', 'd70b1db8c799585ba7e837c7da7e4f8e3aab0ca3239316f10a9a260f53b79a03', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '127.0.0.1', NULL, '2026-02-14 13:46:50', '2026-01-15 13:46:50', NULL),
(61, 1, 'Trusted Device', '8d5213b0804c6ef80465e9ea7d97261311891c509bfd0addd22f02d7c1e5c928', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-14 13:47:35', '2026-01-15 13:47:35', NULL),
(62, 5, 'Trusted Device', 'e51948396fbedb111750410a126b66113dc57fc9becd9cbaad47c8d3192d09c2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-14 13:48:52', '2026-01-15 13:48:52', NULL),
(63, 1, 'Trusted Device', 'b6c42f2eda845d004d7deb90648db69686d53f5e8bf13d9a23bcc16527ae91ad', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-02-13 22:53:24', '2026-02-14 13:49:39', '2026-01-15 13:49:39', '2026-02-13 22:53:24'),
(64, 6, 'Trusted Device', '60d00027d78271c9f4f8829bbddd7f4ff2809f91a764ffcf743af8f8979ef0e2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '127.0.0.1', NULL, '2026-02-14 14:03:26', '2026-01-15 14:03:26', NULL),
(65, 3, 'Trusted Device', '07b75eea3cf03f22c7bf3f161a0c1947288c024a43583abd50dce6b369869d42', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '127.0.0.1', '2026-01-16 02:03:54', '2026-02-14 14:04:57', '2026-01-15 14:04:57', '2026-01-16 02:03:54'),
(66, 1, 'Trusted Device', '8520aa5965d0204554a2dd4341f98b72f0966e38e3e06044ad274a7001f0a2b9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '127.0.0.1', '2026-02-14 12:08:42', '2026-03-12 16:08:29', '2026-02-10 16:08:29', '2026-02-14 12:08:42'),
(67, 3, 'Trusted Device', '16a1c8dc24c61cb974dff59feb7a25039e0b2e253c3e8eebe6bc1304dc515e83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '127.0.0.1', NULL, '2026-03-14 15:08:58', '2026-02-12 15:08:58', NULL),
(68, 7, 'Trusted Device', 'a909e93b619a57e608ae297aee479a95ef6dad1b7a8d1ec4c07f17a6594d1c78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '127.0.0.1', '2026-02-13 16:55:24', '2026-03-14 18:33:59', '2026-02-12 18:33:59', '2026-02-13 16:55:24'),
(69, 3, 'Trusted Device', '3b2cde6c2b953b9c278f7a1d84273a4628c6d2b7d5a4bbce49733c0c60e080c4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '127.0.0.1', '2026-02-14 01:47:23', '2026-03-15 18:29:08', '2026-02-13 18:29:08', '2026-02-14 01:47:23'),
(70, 7, 'Trusted Device', '2c0525b19f7270a6e7f3657a158b285e01996b4edf204d627b46defae1b3ae6d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '127.0.0.1', NULL, '2026-03-16 02:02:31', '2026-02-14 02:02:31', NULL),
(71, 3, 'Trusted Device', '82746d46bbd7ce40036f8f295e288bcdb3227d5e7191d8d0d05f1b5d1d7cc054', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '127.0.0.1', NULL, '2026-03-16 02:19:13', '2026-02-14 02:19:13', NULL),
(72, 7, 'Trusted Device', '642de790709ab5971be725309f5f512eaba73a0ba03652b2273ad5afa1a101ca', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '127.0.0.1', '2026-02-15 17:27:48', '2026-03-16 03:06:09', '2026-02-14 03:06:09', '2026-02-15 17:27:48'),
(73, 1, 'Trusted Device', 'cfd5365b942e96cf46e6f5e8874a318ecf0425ea20d81ccfadf29081d23c1539', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '127.0.0.1', '2026-02-17 01:36:34', '2026-03-16 14:10:38', '2026-02-14 14:10:38', '2026-02-17 01:36:34'),
(74, 3, 'Trusted Device', '6e3bdf8e9a3935ad9c927d30414a029fca01aec8b5ff079e4b53a11622133b83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '127.0.0.1', '2026-02-16 17:23:55', '2026-03-18 15:23:26', '2026-02-16 15:23:26', '2026-02-16 17:23:55'),
(75, 7, 'Trusted Device', '893427bf27a08ffa4fb82a1cda655b1480802abcbd45f4f1984c1fda7378771d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '127.0.0.1', NULL, '2026-03-18 21:45:14', '2026-02-16 21:45:14', NULL),
(76, 6, 'Trusted Device', 'c224bbf6d2ebacb016811f062d9bd7783290b02044ea36684305cae16415c45b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '127.0.0.1', NULL, '2026-03-18 21:48:28', '2026-02-16 21:48:28', NULL),
(77, 3, 'Trusted Device', '26c14e5e8e09721d3b47658d66741052aaceef50c58d394bcdef833ab2e50462', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '127.0.0.1', NULL, '2026-03-19 01:37:10', '2026-02-17 01:37:10', NULL),
(78, 6, 'Trusted Device', '1e20d0b1d2b88c1e4f4b9c7964be85f9a4658559c3823c96720b550f496490da', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '127.0.0.1', '2026-02-17 17:04:55', '2026-03-19 01:40:18', '2026-02-17 01:40:18', '2026-02-17 17:04:55'),
(79, 3, 'Trusted Device', '03ac85edb880ece22e03fc6e066e07d5a309769c6533917f9f5c1a8ab98eb833', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-03-19 17:03:58', '2026-02-17 17:03:58', NULL),
(80, 1, 'Trusted Device', '33197b1264d30fbb0c44a9ded474c191a0f689407265cdd39a803c9a667474a2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-03-19 21:03:25', '2026-02-17 21:03:25', NULL),
(81, 3, 'Trusted Device', '42cc87f13c1f7053027d05e7c49d998988304b1468f72621ab50bf7ba273d6cd', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-03-19 21:05:28', '2026-02-17 21:05:28', NULL),
(82, 1, 'Trusted Device', 'aaa20777586ad3f1fd3ccf8af7303b0edbb80624e9e90f7a2e63950d16730fa6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '127.0.0.1', '2026-02-18 21:47:15', '2026-03-19 21:32:15', '2026-02-17 21:32:15', '2026-02-18 21:47:15'),
(83, 3, 'Trusted Device', '1d477b525bb9536a45317116234d4dc76c9591f97ff36fdd68079ad2383d7cc1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '127.0.0.1', NULL, '2026-03-20 21:56:29', '2026-02-18 21:56:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `unitofmeasure`
--

CREATE TABLE `unitofmeasure` (
  `UnitOfMeasureID` int(11) NOT NULL,
  `UnitName` varchar(50) NOT NULL,
  `Description` varchar(100) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unitofmeasure`
--

INSERT INTO `unitofmeasure` (`UnitOfMeasureID`, `UnitName`, `Description`, `deleted_at`) VALUES
(1, 'pc', 'Piece', NULL),
(2, 'unit', 'Unit', NULL),
(3, 'set', 'Set', NULL),
(4, 'lot', 'Lot', NULL),
(5, 'ream', 'Ream', NULL),
(6, 'box', 'Box', NULL),
(7, 'pack', 'Pack', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `UserRoleID` int(11) NOT NULL,
  `DepartmentID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `EmailVerifiedAt` datetime DEFAULT NULL,
  `ActivationTokenHash` varchar(64) DEFAULT NULL,
  `ActivationSentAt` datetime DEFAULT NULL,
  `ActivationExpiresAt` datetime DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `MustChangePassword` tinyint(1) NOT NULL DEFAULT 1,
  `FullName` varchar(100) DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `MFAEnabled` tinyint(1) NOT NULL DEFAULT 0,
  `MFASecret` varchar(255) DEFAULT NULL,
  `BackupCodes` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `UpdatedDate` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `UserRoleID`, `DepartmentID`, `Username`, `Email`, `EmailVerifiedAt`, `ActivationTokenHash`, `ActivationSentAt`, `ActivationExpiresAt`, `Password`, `MustChangePassword`, `FullName`, `IsActive`, `MFAEnabled`, `MFASecret`, `BackupCodes`, `CreatedDate`, `UpdatedDate`) VALUES
(1, 1, 1, 'gso.admin', 'micarte.aaron08@gmail.com', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$ftgGRhJOPo.5FWmybeLLv.Z8wX8/H.BifZNZsK51uQIsJHUB/k.h6', 0, 'GSO Administrator', 1, 0, 'eyJpdiI6Ik4xMnJ1RzIxbC9rMUpYcDJZYTk4S3c9PSIsInZhbHVlIjoiVnk0UUlOeDQ4aFBUNitKNnduWXJINE9SUzVvS0J0NTBXQmxTbVc1WjJRZz0iLCJtYWMiOiJiNTMzYzRmOTUwMTY3MzI2OTY4OTA2OTk3OWY2OWVkMDVjYmQwZjc5NWYyZWEzOGI4M2I1ODNjMDNlYTJkOGE5IiwidGFnIjoiIn0=', 'eyJpdiI6IkJ5eWxNQkpISDNlVHg2ZUtpN2ViN2c9PSIsInZhbHVlIjoid3V2TFN0bExYWGgwR0cwWUlya1VaV0lQc2hVQjI4bGpzR3pSQkRYZlp4RUI5OU5iT0ZkTG9LSVBNWjg5V2pjRzNqYmh5S1IxamVTTzM0UkkybWY3WWpMZVhPU3MwM1JFOXlCanlrNjJ4NXRNOStUSXBnRk1kZDQrQVFwcjVva2JPMnpONFJBUzNrVzlEaWNsUk1EUDlaSmlZOGZUMFR0K0FhYklhQkd0b0prPSIsIm1hYyI6ImNjOGQ2MTYxM2VhYjZhMGFmYmNmYjNjZmVhZmY3YjJiMWNkMTk2ZGM2MzMyNTE4NTM5ZDJjMzA5OTQxNTY1MjEiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-02-18 22:00:47'),
(2, 2, 1, 'gso.staff.aaronmicarte', 'aaron.micarte@gso.gov.ph', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$YU66h83wjMCOJ9LMN81o8OuWpIuFYTFQpHCxkuWDAurXbYJS9Q.Mu', 0, 'Aaron Micarte', 1, 1, 'eyJpdiI6IjlHZGd3c0dKcmxwcnBLNEdhN2pkWVE9PSIsInZhbHVlIjoiRkd3RWh6SUczdmt3NnpVU0lhUnVGazNSSmdCYkU3OCtLZzA3T1RPZW1BST0iLCJtYWMiOiI3NjliN2M0MDBjZDE4N2YyODcxMWVkMjNjODdiMmVlMDcwZDEwYWIzYTk3NzFhMDVkMjY1MTQzMDM4Y2Y0OThkIiwidGFnIjoiIn0=', 'eyJpdiI6InRLc2pkQjlRVHpZd2ZzKzZ3c0RCRmc9PSIsInZhbHVlIjoiZUlXMnFlWFZ0dkNtZmUxMVVON0VUa3lXOG1EWDNmVjdrRFUxN09TUElBR2Q1K1NNeEF1WHJjTFMyN3dNbG01alUxMGN1b0l4cE5qQmRPL1BUNGxPRU00VGFOaFVtNEd2SWpJTnlEYmhLbTFrVEk0NU51UDY1S2Z4K2RCOHhxbGVyZnkyU1lXbkp2QXFPNlZWNCtwZ0lndHVyd2ljeW4wSUdObktkajJuVkpnPSIsIm1hYyI6ImU0NDM1MWY0YzZkYTYyZmMwY2QzYWM1M2ZiYmMwZTE4MzY0NzM2OWU0YzU2YTg3ODljOWY1NzhiNWVkNWEzNWQiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-01-14 00:21:36'),
(3, 4, 5, 'IT_Head_Chokoy', 'custodian@gso.gov.ph', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$hlU7QKnUbWphvJnFxYgaY.gJVzBnxVKKV.kjw7xB5Oh.1jN0cfA62', 0, 'Chokoy Covid', 1, 0, 'eyJpdiI6ImFvVEpuaEFUclVHYXRVSEJmV3NsYWc9PSIsInZhbHVlIjoibDM5NXorMGlPTkVqUHk1YXN0OXgzM3BWd0FoT3BkYVN0ZkdwRzkxRk9yaz0iLCJtYWMiOiJhOWI4MGQzMjUyZGMyYmJhYTNiNjI3NjVjYWI5NGYyNGIyYjUwNmFkNmM1NjAzZGM5MTE3YTIzOWVkMGQ2NDg5IiwidGFnIjoiIn0=', 'eyJpdiI6Ik1mTHF2UjNtNFlyRldRTzcwbDF5S0E9PSIsInZhbHVlIjoiNjcwdVQxeTJtNmNwOUt4Wkd5RVN1alhjcGZ5R0w5Z2g4clI4L3BvRklUbERXaGFEY1MxMHMzaGMwRGVjVmhtbXpKT1AxSlUxeUlMam9PUWRUeFBNcnpJdytsUmFubHpiSExFWEJKOXErK1dLUEtLWFZ0MDVJQ2RPYU11MHorUmRxTS8zRlpmRythNkJlUmh3ZmZUYkNScXBxRlV0YmUwN3NyUjRkQUNCazMwPSIsIm1hYyI6ImJmN2NlN2EwODAzMDNlZTEwYWZkOGRkNjQwNzYwZTE1NzY3MDEyZGMwMTZjMDRjMWQ4NzcwMzY4ZmJiYTU3NTgiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-02-18 22:00:52'),
(5, 4, 4, 'HR_Head_Tyron', 'micarte2005@gmail.com', '2026-01-14 01:50:24', NULL, NULL, NULL, '$2y$12$UjxLHSUjJQBOZYHqrB7ajuK2rIiZ1P9962RTMFifYWMoF.fH7o3Wq', 0, 'Tyron Mecarte', 1, 1, 'eyJpdiI6Ilo3U0pYYVBOd1RFZEtTbmxxUlRXUWc9PSIsInZhbHVlIjoiYXlZTWlzemI3TlFZeWI4MUpuNC9wdEpFelN0emhnbFVWL1g2Ky9lMTFOTT0iLCJtYWMiOiIwZjY3ZjA3OTUwY2M1NGQzOTczMmNhZjA1NzI5NzU4ZDQ0MGEzNDU1NTZkMzEzMmQxYzRjZmI4NzcwYzQ0YjdkIiwidGFnIjoiIn0=', 'eyJpdiI6InplOXhDZXZEeHR1QkF6WEpGYzRjdEE9PSIsInZhbHVlIjoiRGh6cXVuaHRQRDU1aGxGd2hDZGVLdmJkcWIybDNnWkpXaXF5TkdobmpzOS9iYzlNRDNUcldyQ0VZSlR2UDlDd3JvZ1lUVjVIWWJBTWJENmRKZHhva2FNZzJOc3ZaZkordUhzTkJnbkhuNEdYWnoxWDRtVkNZVGFHZkVhb2VLcHdDWVJtYzVZYno2SmJJS1h6YytiRVMwNDZBcVN0TnR4aDBJckRPSThOMmQ4PSIsIm1hYyI6ImExZDNmMzBiN2VjNzJjNzg5ZDEyYjAxMTRmNWJmZjcwZTJjYzhkMWUwNjA0OGU3MDkxNDk5NTlkYzc2NmI2YmMiLCJ0YWciOiIifQ==', '2026-01-14 01:00:26', '2026-02-10 16:52:09'),
(6, 4, 3, 'FD_Head_Cyron', 'aaya.micarte.coc@phinmaed.com', '2026-01-15 14:01:41', NULL, NULL, NULL, '$2y$12$A/XCqp8lXrNCeYvQ6lOyUu6DXxbXtJ3Z7FJ.5q39q9yASzA1EYIKS', 0, 'Cyron Micarte', 1, 0, 'eyJpdiI6Iko3ekRUVUFOMjJpa2dFUExuaWhGUFE9PSIsInZhbHVlIjoiK0ZUQyt2VmpJenJ0c25YZmpwUzNwMU0yQmVoYmdtcTFVN2pwOUZLOHBPOD0iLCJtYWMiOiI4NjFjY2U2ZTEwY2EwZjE2YTZhOWExOWEzZWVmYmZkZjdlYzFlOTdjZmRjZTMxY2I3N2RiYzI5MjIyZmNhOGUyIiwidGFnIjoiIn0=', 'eyJpdiI6IlBGTGE5dnYwdCsrVlJMbzgwZ0t0WWc9PSIsInZhbHVlIjoiY0UrYzltZHJHMG5qTE9FUDljMVpEVDJ4YnNYY0QzeGRIQUNjK0lqS3hNYzJCYVdhZUZYcm9RdnRIRHhPTkNhbnF0Wnk1bW14Y1hKdXQzNmFibndSTno3OXV4V3VMVXRFWHBvM0VCdHozRHgwTkZYaFlQcnVrOThETXFhS3p3dE9FYWhiNWdDa1IybzhaUXhwL0g1WXA4d2crOFlnZktpVnJlK2NJbWJRWDdzPSIsIm1hYyI6IjZkYTMxNDY2ZGUzOGY2ZjVhYmQ0NGZjZWNlODNhZTQ0OTVjMjAyYTMwYjE4ZGNjNDg1ODdjNmZlNTdkM2ViZWEiLCJ0YWciOiIifQ==', '2026-01-15 14:00:32', '2026-02-18 22:00:44'),
(7, 6, 1, 'IO_Abrogar', 'cycymicarte@gmail.com', '2026-02-12 18:27:18', NULL, NULL, NULL, '$2y$12$fepSB6IGbOXf7nWvwUDb2uJjr2FJO07ckbe1cHFDkN9/hSqnu.qQe', 0, 'Francismile Noah Abrogar', 1, 0, 'eyJpdiI6IlF1RW8ybzQxNWVPcy9obHNxZnJkN2c9PSIsInZhbHVlIjoiR3lzZ2VFZldMdkFocEpoRWVaU3BEMFJzRmovYWVPY1Z3MGRZaDFmc1h5VT0iLCJtYWMiOiJiNmE2ZjNmMWU4ZDliZmQ0YTExM2ExODE1MTFjNmE5MGE0Mzc5NjYzZTliOTE2NmU3MmFmNDc3YzFmZTE4YTQwIiwidGFnIjoiIn0=', 'eyJpdiI6InNoeEFiQmczdGtPRzFJeVBtTUl0L1E9PSIsInZhbHVlIjoiZlFjTFZMQ1FJSTAyK0pCNmg1OEhKVVRzYlluZGFvMC9nWk0yOW9BVGhFczMwTWlsM0psWHpKUVM4QkpVVEtTYWFqaG5kWHlqTWNwNmpvSkE5R2MrYXZyaVZUR3crOEdQbGp6ZE0zTkFPcSswR3BnRWdITHVPZ2FsMU1MQmdjeDdueVNMdDlnMWNYRGJxaFl0VC9xZFJzT2k1YUkzaVBRNFpMeDFvcFRHbkJZPSIsIm1hYyI6IjI4ZTlmMjViYTUzOWY3MjZmOWI5MjVhOGQ4ZjE0OTFiOTdlNjJjMzE4MDFhZGJjZTk3MjY4OGQwMmRlN2RjNzIiLCJ0YWciOiIifQ==', '2026-02-12 18:26:12', '2026-02-18 22:00:56');

-- --------------------------------------------------------

--
-- Table structure for table `userrole`
--

CREATE TABLE `userrole` (
  `UserRoleID` int(11) NOT NULL,
  `RoleName` varchar(50) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `UpdatedDate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userrole`
--

INSERT INTO `userrole` (`UserRoleID`, `RoleName`, `Description`, `CreatedDate`, `UpdatedDate`, `deleted_at`) VALUES
(1, 'Admin', 'System Administrator - Full Access', '2025-12-05 02:07:28', NULL, NULL),
(2, 'GSO Staff', 'General Services Office Staff', '2025-12-05 02:07:28', NULL, NULL),
(3, 'Property Custodian', 'Manages property inventory', '2025-12-05 02:07:28', NULL, NULL),
(4, 'Department Head', 'Can approve requests', '2025-12-05 02:07:28', NULL, NULL),
(5, 'End User', 'Regular property accountable user', '2025-12-05 02:07:28', NULL, NULL),
(6, 'Inspection Officer', 'Performs inspection and verification of delivered items', '2026-02-12 18:12:43', NULL, NULL),
(11, 'admin-1', 'test', '2026-02-14 12:19:53', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wastematerialdetail`
--

CREATE TABLE `wastematerialdetail` (
  `WasteDetailID` int(11) NOT NULL,
  `WasteMaterialID` int(11) NOT NULL,
  `PropertyID` int(11) NOT NULL,
  `ConditionID` int(11) NOT NULL,
  `DisposalMethodID` int(11) DEFAULT NULL,
  `Remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wastematerialreport`
--

CREATE TABLE `wastematerialreport` (
  `WasteMaterialID` int(11) NOT NULL,
  `WMRNumber` varchar(50) DEFAULT NULL,
  `ReportedBy` int(11) NOT NULL,
  `ApprovedBy` int(11) DEFAULT NULL,
  `DateReported` date NOT NULL,
  `DateApproved` date DEFAULT NULL,
  `Status` varchar(50) DEFAULT 'Pending',
  `Remarks` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`ArticleID`),
  ADD UNIQUE KEY `articles_articlename_unique` (`ArticleName`),
  ADD KEY `articles_articlename_index` (`ArticleName`),
  ADD KEY `articles_isactive_index` (`IsActive`),
  ADD KEY `articles_propertytype_index` (`PropertyTypeID`),
  ADD KEY `articles_defaultuom_index` (`DefaultUnitOfMeasureID`);

--
-- Indexes for table `assetrequest`
--
ALTER TABLE `assetrequest`
  ADD PRIMARY KEY (`RequestID`),
  ADD UNIQUE KEY `assetrequest_requestnumber_unique` (`RequestNumber`),
  ADD KEY `assetrequest_status_index` (`Status`),
  ADD KEY `assetrequest_requestedby_index` (`RequestedBy`),
  ADD KEY `assetrequest_departmentid_index` (`DepartmentID`),
  ADD KEY `assetrequest_requestdate_index` (`RequestDate`),
  ADD KEY `assetrequest_reviewedby_foreign` (`ReviewedBy`),
  ADD KEY `assetrequest_issuedby_foreign` (`IssuedBy`);

--
-- Indexes for table `assetrequestdetail`
--
ALTER TABLE `assetrequestdetail`
  ADD PRIMARY KEY (`RequestDetailID`),
  ADD KEY `assetrequestdetail_requestid_index` (`RequestID`),
  ADD KEY `assetrequestdetail_propertyinventorydetailid_index` (`PropertyInventoryDetailID`),
  ADD KEY `assetrequestdetail_assignedenduserid_index` (`AssignedEndUserID`),
  ADD KEY `assetrequestdetail_assigneddepartmentid_index` (`AssignedDepartmentID`);

--
-- Indexes for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  ADD PRIMARY KEY (`AssignmentID`),
  ADD KEY `asset_assignments_assetrequestdetailid_foreign` (`AssetRequestDetailID`);

--
-- Indexes for table `asset_audit_logs`
--
ALTER TABLE `asset_audit_logs`
  ADD PRIMARY KEY (`AuditID`),
  ADD KEY `asset_audit_logs_entitytype_entityid_index` (`EntityType`,`EntityID`),
  ADD KEY `asset_audit_logs_performedby_index` (`PerformedBy`),
  ADD KEY `asset_audit_logs_createdat_index` (`CreatedAt`);

--
-- Indexes for table `asset_request_signatures`
--
ALTER TABLE `asset_request_signatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asset_request_signatures_request_id_role_unique` (`request_id`,`role`),
  ADD KEY `asset_request_signatures_signed_by_user_id_index` (`signed_by_user_id`),
  ADD KEY `asset_request_signatures_sha256_hash_index` (`sha256_hash`);

--
-- Indexes for table `assignedproperty`
--
ALTER TABLE `assignedproperty`
  ADD PRIMARY KEY (`PropertyID`),
  ADD KEY `InventoryDetailID` (`InventoryDetailID`),
  ADD KEY `EndUserID` (`EndUserID`),
  ADD KEY `DepartmentID` (`DepartmentID`),
  ADD KEY `PropertyStatusID` (`PropertyStatusID`),
  ADD KEY `ConditionID` (`ConditionID`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `condition`
--
ALTER TABLE `condition`
  ADD PRIMARY KEY (`ConditionID`);

--
-- Indexes for table `deliveryreceipt`
--
ALTER TABLE `deliveryreceipt`
  ADD PRIMARY KEY (`DRID`),
  ADD UNIQUE KEY `ux_deliveryreceipt_drnumber` (`DRNumber`),
  ADD KEY `POID` (`PONumber`),
  ADD KEY `ReceivedBy` (`ReceivedBy`),
  ADD KEY `deliveryreceipt_requestedby_index` (`RequestedBy`);

--
-- Indexes for table `deliveryreceiptdetail`
--
ALTER TABLE `deliveryreceiptdetail`
  ADD PRIMARY KEY (`DRDetailID`),
  ADD KEY `DRID` (`DRID`),
  ADD KEY `PODetailID` (`ItemDescription`),
  ADD KEY `PropertyTypeID` (`PropertyTypeID`),
  ADD KEY `UnitOfMeasureID` (`UnitOfMeasureID`),
  ADD KEY `deliveryreceiptdetail_articleid_index` (`ArticleID`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`DepartmentID`);

--
-- Indexes for table `disposalmethod`
--
ALTER TABLE `disposalmethod`
  ADD PRIMARY KEY (`DisposalMethodID`);

--
-- Indexes for table `enduser`
--
ALTER TABLE `enduser`
  ADD PRIMARY KEY (`EndUserID`),
  ADD KEY `PositionID` (`PositionID`),
  ADD KEY `DepartmentID` (`DepartmentID`);

--
-- Indexes for table `iardetail`
--
ALTER TABLE `iardetail`
  ADD PRIMARY KEY (`IARDetailID`),
  ADD KEY `IARID` (`IARID`),
  ADD KEY `DRDetailID` (`DRDetailID`),
  ADD KEY `PropertyTypeID` (`PropertyTypeID`),
  ADD KEY `ConditionID` (`ConditionID`);

--
-- Indexes for table `iar_signatures`
--
ALTER TABLE `iar_signatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `iar_signatures_iar_id_role_unique` (`iar_id`,`role`),
  ADD KEY `iar_signatures_signed_by_user_id_index` (`signed_by_user_id`),
  ADD KEY `iar_signatures_sha256_hash_index` (`sha256_hash`);

--
-- Indexes for table `icsdetail`
--
ALTER TABLE `icsdetail`
  ADD PRIMARY KEY (`ICSDetailID`),
  ADD KEY `ICSID` (`ICSID`),
  ADD KEY `PropertyID` (`PropertyID`);

--
-- Indexes for table `iirup`
--
ALTER TABLE `iirup`
  ADD PRIMARY KEY (`IIRUPID`),
  ADD UNIQUE KEY `IIRUPNumber` (`IIRUPNumber`),
  ADD KEY `ReportedBy` (`ReportedBy`),
  ADD KEY `ApprovedBy` (`ApprovedBy`);

--
-- Indexes for table `iirupdetail`
--
ALTER TABLE `iirupdetail`
  ADD PRIMARY KEY (`IIRUPDetailID`),
  ADD KEY `IIRUPID` (`IIRUPID`),
  ADD KEY `PropertyID` (`PropertyID`),
  ADD KEY `ConditionID` (`ConditionID`),
  ADD KEY `InspectedBy` (`InspectedBy`),
  ADD KEY `RecommendationID` (`RecommendationID`);

--
-- Indexes for table `iiruprecommendation`
--
ALTER TABLE `iiruprecommendation`
  ADD PRIMARY KEY (`RecommendationID`);

--
-- Indexes for table `inspectionacceptancereport`
--
ALTER TABLE `inspectionacceptancereport`
  ADD PRIMARY KEY (`IARID`),
  ADD UNIQUE KEY `IARNumber` (`IARNumber`),
  ADD KEY `DRID` (`DRID`),
  ADD KEY `InspectedBy` (`InspectedBy`),
  ADD KEY `AcceptedBy` (`AcceptedBy`);

--
-- Indexes for table `inventorycustodianslip`
--
ALTER TABLE `inventorycustodianslip`
  ADD PRIMARY KEY (`ICSID`),
  ADD UNIQUE KEY `ICSNumber` (`ICSNumber`),
  ADD KEY `IssuedTo` (`IssuedTo`),
  ADD KEY `IssuedBy` (`IssuedBy`);

--
-- Indexes for table `loginaudit`
--
ALTER TABLE `loginaudit`
  ADD PRIMARY KEY (`LoginAuditID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `par`
--
ALTER TABLE `par`
  ADD PRIMARY KEY (`PARID`),
  ADD UNIQUE KEY `PARNumber` (`PARNumber`),
  ADD KEY `PropertyID` (`PropertyID`),
  ADD KEY `IssuedTo` (`IssuedTo`),
  ADD KEY `IssuedBy` (`IssuedBy`);

--
-- Indexes for table `pardetail`
--
ALTER TABLE `pardetail`
  ADD PRIMARY KEY (`PARDetailID`),
  ADD UNIQUE KEY `pardetail_parid_propertyid_unique` (`PARID`,`PropertyID`),
  ADD KEY `pardetail_parid_index` (`PARID`),
  ADD KEY `pardetail_propertyid_index` (`PropertyID`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `physicalcount`
--
ALTER TABLE `physicalcount`
  ADD PRIMARY KEY (`PhysicalCountID`),
  ADD KEY `SessionID` (`SessionID`),
  ADD KEY `PropertyID` (`PropertyID`),
  ADD KEY `InventoryDetailID` (`InventoryDetailID`),
  ADD KEY `ConditionID` (`ConditionID`),
  ADD KEY `PerformedBy` (`PerformedBy`);

--
-- Indexes for table `physicalcountsession`
--
ALTER TABLE `physicalcountsession`
  ADD PRIMARY KEY (`SessionID`),
  ADD KEY `CreatedBy` (`CreatedBy`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`PositionID`);

--
-- Indexes for table `propertyclassification`
--
ALTER TABLE `propertyclassification`
  ADD PRIMARY KEY (`PropertyClassificationID`);

--
-- Indexes for table `propertydisposal`
--
ALTER TABLE `propertydisposal`
  ADD PRIMARY KEY (`DisposalID`),
  ADD UNIQUE KEY `DisposalNumber` (`DisposalNumber`),
  ADD KEY `WasteMaterialID` (`WasteMaterialID`),
  ADD KEY `ApprovedBy` (`ApprovedBy`),
  ADD KEY `DisposalMethodID` (`DisposalMethodID`);

--
-- Indexes for table `propertydisposaldetail`
--
ALTER TABLE `propertydisposaldetail`
  ADD PRIMARY KEY (`DisposalDetailID`),
  ADD KEY `DisposalID` (`DisposalID`),
  ADD KEY `WasteDetailID` (`WasteDetailID`),
  ADD KEY `PropertyID` (`PropertyID`);

--
-- Indexes for table `propertyinventory`
--
ALTER TABLE `propertyinventory`
  ADD PRIMARY KEY (`InventoryID`),
  ADD UNIQUE KEY `inventory_inventorynumber_unique` (`InventoryNumber`),
  ADD KEY `PropertyTypeID` (`PropertyTypeID`),
  ADD KEY `UnitOfMeasureID` (`UnitOfMeasureID`),
  ADD KEY `DRID` (`DRID`),
  ADD KEY `IARID` (`IARID`);

--
-- Indexes for table `propertyinventorydetail`
--
ALTER TABLE `propertyinventorydetail`
  ADD PRIMARY KEY (`InventoryDetailID`),
  ADD UNIQUE KEY `PropertyNumber` (`PropertyNumber`),
  ADD KEY `InventoryID` (`InventoryID`),
  ADD KEY `ReceivedID` (`ReceivedID`),
  ADD KEY `PropertyStatusID` (`PropertyStatusID`),
  ADD KEY `ConditionID` (`ConditionID`),
  ADD KEY `propertyinventorydetail_propertytypeid_foreign` (`PropertyTypeID`),
  ADD KEY `propertyinventorydetail_unitofmeasureid_foreign` (`UnitOfMeasureID`),
  ADD KEY `propertyinventorydetail_sourceiardetailid_foreign` (`SourceIARDetailID`),
  ADD KEY `propertyinventorydetail_sourcedrdetailid_foreign` (`SourceDRDetailID`);

--
-- Indexes for table `propertyissuance`
--
ALTER TABLE `propertyissuance`
  ADD PRIMARY KEY (`IssuanceID`),
  ADD KEY `IssuedBy` (`IssuedBy`),
  ADD KEY `IssuedTo` (`IssuedTo`),
  ADD KEY `propertyissuance_requestid_foreign` (`RequestID`),
  ADD KEY `propertyissuance_documentnumber_index` (`DocumentNumber`),
  ADD KEY `propertyissuance_status_index` (`Status`),
  ADD KEY `propertyissuance_drid_index` (`DRID`);

--
-- Indexes for table `propertyissuancedetail`
--
ALTER TABLE `propertyissuancedetail`
  ADD PRIMARY KEY (`IssuanceDetailID`),
  ADD KEY `IssuanceID` (`IssuanceID`),
  ADD KEY `InventoryDetailID` (`InventoryDetailID`),
  ADD KEY `propertyissuancedetail_requestdetailid_foreign` (`RequestDetailID`);

--
-- Indexes for table `propertyreceived`
--
ALTER TABLE `propertyreceived`
  ADD PRIMARY KEY (`ReceivedID`),
  ADD KEY `IARID` (`IARID`),
  ADD KEY `ReceivedBy` (`ReceivedBy`),
  ADD KEY `ReleasedBy` (`ReleasedBy`);

--
-- Indexes for table `propertystatus`
--
ALTER TABLE `propertystatus`
  ADD PRIMARY KEY (`PropertyStatusID`);

--
-- Indexes for table `propertytransfer`
--
ALTER TABLE `propertytransfer`
  ADD PRIMARY KEY (`TransferID`),
  ADD UNIQUE KEY `propertytransfer_transfernumber_unique` (`TransferNumber`),
  ADD KEY `propertytransfer_status_fromdepartmentid_index` (`Status`,`FromDepartmentID`),
  ADD KEY `propertytransfer_status_todepartmentid_index` (`Status`,`ToDepartmentID`),
  ADD KEY `propertytransfer_requestedbyuserid_index` (`RequestedByUserID`),
  ADD KEY `propertytransfer_fromdepartmentid_foreign` (`FromDepartmentID`),
  ADD KEY `propertytransfer_todepartmentid_foreign` (`ToDepartmentID`),
  ADD KEY `propertytransfer_fromenduserid_foreign` (`FromEndUserID`),
  ADD KEY `propertytransfer_toenduserid_foreign` (`ToEndUserID`),
  ADD KEY `propertytransfer_approvedbyuserid_foreign` (`ApprovedByUserID`),
  ADD KEY `propertytransfer_completedbyuserid_foreign` (`CompletedByUserID`),
  ADD KEY `propertytransfer_releasingreviewedbyuserid_foreign` (`ReleasingReviewedByUserID`);

--
-- Indexes for table `propertytransferdetails`
--
ALTER TABLE `propertytransferdetails`
  ADD PRIMARY KEY (`TransferItemID`),
  ADD KEY `propertytransferdetails_inventorydetailid_index` (`InventoryDetailID`),
  ADD KEY `propertytransferdetails_transferid_foreign` (`TransferID`);

--
-- Indexes for table `propertytype`
--
ALTER TABLE `propertytype`
  ADD PRIMARY KEY (`PropertyTypeID`),
  ADD KEY `PropertyClassificationID` (`PropertyClassificationID`);

--
-- Indexes for table `qrcode`
--
ALTER TABLE `qrcode`
  ADD PRIMARY KEY (`QRCodeID`),
  ADD KEY `PropertyID` (`PropertyID`),
  ADD KEY `DepartmentID` (`DepartmentID`),
  ADD KEY `qrcode_inventorydetailid_index` (`InventoryDetailID`);

--
-- Indexes for table `requisition_issue_slips`
--
ALTER TABLE `requisition_issue_slips`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `requisition_issue_slips_asset_request_id_unique` (`asset_request_id`),
  ADD UNIQUE KEY `requisition_issue_slips_ris_number_unique` (`ris_number`),
  ADD KEY `requisition_issue_slips_asset_request_id_index` (`asset_request_id`),
  ADD KEY `requisition_issue_slips_status_index` (`status`),
  ADD KEY `requisition_issue_slips_requested_by_user_id_foreign` (`requested_by_user_id`),
  ADD KEY `requisition_issue_slips_approved_by_user_id_foreign` (`approved_by_user_id`),
  ADD KEY `requisition_issue_slips_prepared_by_user_id_foreign` (`prepared_by_user_id`),
  ADD KEY `requisition_issue_slips_issued_by_user_id_foreign` (`issued_by_user_id`),
  ADD KEY `requisition_issue_slips_received_by_user_id_foreign` (`received_by_user_id`);

--
-- Indexes for table `requisition_issue_slip_items`
--
ALTER TABLE `requisition_issue_slip_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_issue_slip_items_ris_id_index` (`ris_id`),
  ADD KEY `requisition_issue_slip_items_inventory_detail_id_index` (`inventory_detail_id`),
  ADD KEY `requisition_issue_slip_items_asset_request_detail_id_foreign` (`asset_request_detail_id`);

--
-- Indexes for table `requisition_issue_slip_signatures`
--
ALTER TABLE `requisition_issue_slip_signatures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `requisition_issue_slip_signatures_ris_id_role_unique` (`ris_id`,`role`),
  ADD KEY `requisition_issue_slip_signatures_signed_by_user_id_index` (`signed_by_user_id`),
  ADD KEY `requisition_issue_slip_signatures_sha256_hash_index` (`sha256_hash`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`SupplierID`);

--
-- Indexes for table `trusted_devices`
--
ALTER TABLE `trusted_devices`
  ADD PRIMARY KEY (`DeviceID`),
  ADD KEY `idx_device_token` (`DeviceToken`),
  ADD KEY `idx_user_id` (`UserID`);

--
-- Indexes for table `unitofmeasure`
--
ALTER TABLE `unitofmeasure`
  ADD PRIMARY KEY (`UnitOfMeasureID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `UserRoleID` (`UserRoleID`),
  ADD KEY `DepartmentID` (`DepartmentID`);

--
-- Indexes for table `userrole`
--
ALTER TABLE `userrole`
  ADD PRIMARY KEY (`UserRoleID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `wastematerialdetail`
--
ALTER TABLE `wastematerialdetail`
  ADD PRIMARY KEY (`WasteDetailID`),
  ADD KEY `WasteMaterialID` (`WasteMaterialID`),
  ADD KEY `PropertyID` (`PropertyID`),
  ADD KEY `ConditionID` (`ConditionID`),
  ADD KEY `DisposalMethodID` (`DisposalMethodID`);

--
-- Indexes for table `wastematerialreport`
--
ALTER TABLE `wastematerialreport`
  ADD PRIMARY KEY (`WasteMaterialID`),
  ADD UNIQUE KEY `WMRNumber` (`WMRNumber`),
  ADD KEY `ReportedBy` (`ReportedBy`),
  ADD KEY `ApprovedBy` (`ApprovedBy`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `ArticleID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `assetrequest`
--
ALTER TABLE `assetrequest`
  MODIFY `RequestID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assetrequestdetail`
--
ALTER TABLE `assetrequestdetail`
  MODIFY `RequestDetailID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  MODIFY `AssignmentID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_audit_logs`
--
ALTER TABLE `asset_audit_logs`
  MODIFY `AuditID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `asset_request_signatures`
--
ALTER TABLE `asset_request_signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignedproperty`
--
ALTER TABLE `assignedproperty`
  MODIFY `PropertyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `condition`
--
ALTER TABLE `condition`
  MODIFY `ConditionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `deliveryreceipt`
--
ALTER TABLE `deliveryreceipt`
  MODIFY `DRID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `deliveryreceiptdetail`
--
ALTER TABLE `deliveryreceiptdetail`
  MODIFY `DRDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `disposalmethod`
--
ALTER TABLE `disposalmethod`
  MODIFY `DisposalMethodID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `enduser`
--
ALTER TABLE `enduser`
  MODIFY `EndUserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `iardetail`
--
ALTER TABLE `iardetail`
  MODIFY `IARDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `iar_signatures`
--
ALTER TABLE `iar_signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `icsdetail`
--
ALTER TABLE `icsdetail`
  MODIFY `ICSDetailID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `iirup`
--
ALTER TABLE `iirup`
  MODIFY `IIRUPID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `iirupdetail`
--
ALTER TABLE `iirupdetail`
  MODIFY `IIRUPDetailID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `iiruprecommendation`
--
ALTER TABLE `iiruprecommendation`
  MODIFY `RecommendationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inspectionacceptancereport`
--
ALTER TABLE `inspectionacceptancereport`
  MODIFY `IARID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `inventorycustodianslip`
--
ALTER TABLE `inventorycustodianslip`
  MODIFY `ICSID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loginaudit`
--
ALTER TABLE `loginaudit`
  MODIFY `LoginAuditID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `par`
--
ALTER TABLE `par`
  MODIFY `PARID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pardetail`
--
ALTER TABLE `pardetail`
  MODIFY `PARDetailID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `physicalcount`
--
ALTER TABLE `physicalcount`
  MODIFY `PhysicalCountID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `physicalcountsession`
--
ALTER TABLE `physicalcountsession`
  MODIFY `SessionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `position`
--
ALTER TABLE `position`
  MODIFY `PositionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `propertyclassification`
--
ALTER TABLE `propertyclassification`
  MODIFY `PropertyClassificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `propertydisposal`
--
ALTER TABLE `propertydisposal`
  MODIFY `DisposalID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `propertydisposaldetail`
--
ALTER TABLE `propertydisposaldetail`
  MODIFY `DisposalDetailID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `propertyinventory`
--
ALTER TABLE `propertyinventory`
  MODIFY `InventoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `propertyinventorydetail`
--
ALTER TABLE `propertyinventorydetail`
  MODIFY `InventoryDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `propertyissuance`
--
ALTER TABLE `propertyissuance`
  MODIFY `IssuanceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `propertyissuancedetail`
--
ALTER TABLE `propertyissuancedetail`
  MODIFY `IssuanceDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `propertyreceived`
--
ALTER TABLE `propertyreceived`
  MODIFY `ReceivedID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `propertystatus`
--
ALTER TABLE `propertystatus`
  MODIFY `PropertyStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `propertytransfer`
--
ALTER TABLE `propertytransfer`
  MODIFY `TransferID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `propertytransferdetails`
--
ALTER TABLE `propertytransferdetails`
  MODIFY `TransferItemID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `propertytype`
--
ALTER TABLE `propertytype`
  MODIFY `PropertyTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `qrcode`
--
ALTER TABLE `qrcode`
  MODIFY `QRCodeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `requisition_issue_slips`
--
ALTER TABLE `requisition_issue_slips`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_issue_slip_items`
--
ALTER TABLE `requisition_issue_slip_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_issue_slip_signatures`
--
ALTER TABLE `requisition_issue_slip_signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `SupplierID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `trusted_devices`
--
ALTER TABLE `trusted_devices`
  MODIFY `DeviceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `unitofmeasure`
--
ALTER TABLE `unitofmeasure`
  MODIFY `UnitOfMeasureID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `userrole`
--
ALTER TABLE `userrole`
  MODIFY `UserRoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wastematerialdetail`
--
ALTER TABLE `wastematerialdetail`
  MODIFY `WasteDetailID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wastematerialreport`
--
ALTER TABLE `wastematerialreport`
  MODIFY `WasteMaterialID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_propertytype_foreign` FOREIGN KEY (`PropertyTypeID`) REFERENCES `propertytype` (`PropertyTypeID`),
  ADD CONSTRAINT `articles_defaultuom_foreign` FOREIGN KEY (`DefaultUnitOfMeasureID`) REFERENCES `unitofmeasure` (`UnitOfMeasureID`) ON UPDATE CASCADE;

--
-- Constraints for table `assetrequest`
--
ALTER TABLE `assetrequest`
  ADD CONSTRAINT `assetrequest_departmentid_foreign` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`),
  ADD CONSTRAINT `assetrequest_issuedby_foreign` FOREIGN KEY (`IssuedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `assetrequest_requestedby_foreign` FOREIGN KEY (`RequestedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `assetrequest_reviewedby_foreign` FOREIGN KEY (`ReviewedBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `assetrequestdetail`
--
ALTER TABLE `assetrequestdetail`
  ADD CONSTRAINT `assetrequestdetail_propertyinventorydetailid_foreign` FOREIGN KEY (`PropertyInventoryDetailID`) REFERENCES `propertyinventorydetail` (`InventoryDetailID`),
  ADD CONSTRAINT `assetrequestdetail_requestid_foreign` FOREIGN KEY (`RequestID`) REFERENCES `assetrequest` (`RequestID`) ON DELETE CASCADE;

--
-- Constraints for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  ADD CONSTRAINT `asset_assignments_assetrequestdetailid_foreign` FOREIGN KEY (`AssetRequestDetailID`) REFERENCES `assetrequestdetail` (`RequestDetailID`);

--
-- Constraints for table `asset_audit_logs`
--
ALTER TABLE `asset_audit_logs`
  ADD CONSTRAINT `asset_audit_logs_performedby_foreign` FOREIGN KEY (`PerformedBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `asset_request_signatures`
--
ALTER TABLE `asset_request_signatures`
  ADD CONSTRAINT `asset_request_signatures_request_id_foreign` FOREIGN KEY (`request_id`) REFERENCES `assetrequest` (`RequestID`) ON DELETE CASCADE,
  ADD CONSTRAINT `asset_request_signatures_signed_by_user_id_foreign` FOREIGN KEY (`signed_by_user_id`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `assignedproperty`
--
ALTER TABLE `assignedproperty`
  ADD CONSTRAINT `assignedproperty_ibfk_1` FOREIGN KEY (`InventoryDetailID`) REFERENCES `propertyinventorydetail` (`InventoryDetailID`),
  ADD CONSTRAINT `assignedproperty_ibfk_2` FOREIGN KEY (`EndUserID`) REFERENCES `enduser` (`EndUserID`),
  ADD CONSTRAINT `assignedproperty_ibfk_3` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`),
  ADD CONSTRAINT `assignedproperty_ibfk_4` FOREIGN KEY (`PropertyStatusID`) REFERENCES `propertystatus` (`PropertyStatusID`),
  ADD CONSTRAINT `assignedproperty_ibfk_5` FOREIGN KEY (`ConditionID`) REFERENCES `condition` (`ConditionID`);

--
-- Constraints for table `deliveryreceipt`
--
ALTER TABLE `deliveryreceipt`
  ADD CONSTRAINT `deliveryreceipt_ibfk_2` FOREIGN KEY (`ReceivedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `deliveryreceipt_requestedby_foreign` FOREIGN KEY (`RequestedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL;

--
-- Constraints for table `deliveryreceiptdetail`
--
ALTER TABLE `deliveryreceiptdetail`
  ADD CONSTRAINT `deliveryreceiptdetail_articleid_foreign` FOREIGN KEY (`ArticleID`) REFERENCES `articles` (`ArticleID`) ON DELETE SET NULL,
  ADD CONSTRAINT `deliveryreceiptdetail_ibfk_1` FOREIGN KEY (`DRID`) REFERENCES `deliveryreceipt` (`DRID`),
  ADD CONSTRAINT `deliveryreceiptdetail_ibfk_3` FOREIGN KEY (`PropertyTypeID`) REFERENCES `propertytype` (`PropertyTypeID`),
  ADD CONSTRAINT `fk_drdetail_unitofmeasure` FOREIGN KEY (`UnitOfMeasureID`) REFERENCES `unitofmeasure` (`UnitOfMeasureID`) ON UPDATE CASCADE;

--
-- Constraints for table `enduser`
--
ALTER TABLE `enduser`
  ADD CONSTRAINT `enduser_ibfk_1` FOREIGN KEY (`PositionID`) REFERENCES `position` (`PositionID`),
  ADD CONSTRAINT `enduser_ibfk_2` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`);

--
-- Constraints for table `iardetail`
--
ALTER TABLE `iardetail`
  ADD CONSTRAINT `iardetail_ibfk_1` FOREIGN KEY (`IARID`) REFERENCES `inspectionacceptancereport` (`IARID`),
  ADD CONSTRAINT `iardetail_ibfk_2` FOREIGN KEY (`DRDetailID`) REFERENCES `deliveryreceiptdetail` (`DRDetailID`),
  ADD CONSTRAINT `iardetail_ibfk_3` FOREIGN KEY (`PropertyTypeID`) REFERENCES `propertytype` (`PropertyTypeID`),
  ADD CONSTRAINT `iardetail_ibfk_4` FOREIGN KEY (`ConditionID`) REFERENCES `condition` (`ConditionID`);

--
-- Constraints for table `icsdetail`
--
ALTER TABLE `icsdetail`
  ADD CONSTRAINT `icsdetail_ibfk_1` FOREIGN KEY (`ICSID`) REFERENCES `inventorycustodianslip` (`ICSID`),
  ADD CONSTRAINT `icsdetail_ibfk_2` FOREIGN KEY (`PropertyID`) REFERENCES `assignedproperty` (`PropertyID`);

--
-- Constraints for table `propertytransfer`
--
ALTER TABLE `propertytransfer`
  ADD CONSTRAINT `propertytransfer_approvedbyuserid_foreign` FOREIGN KEY (`ApprovedByUserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `propertytransfer_completedbyuserid_foreign` FOREIGN KEY (`CompletedByUserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `propertytransfer_fromdepartmentid_foreign` FOREIGN KEY (`FromDepartmentID`) REFERENCES `department` (`DepartmentID`),
  ADD CONSTRAINT `propertytransfer_fromenduserid_foreign` FOREIGN KEY (`FromEndUserID`) REFERENCES `enduser` (`EndUserID`),
  ADD CONSTRAINT `propertytransfer_releasingreviewedbyuserid_foreign` FOREIGN KEY (`ReleasingReviewedByUserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `propertytransfer_requestedbyuserid_foreign` FOREIGN KEY (`RequestedByUserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `propertytransfer_todepartmentid_foreign` FOREIGN KEY (`ToDepartmentID`) REFERENCES `department` (`DepartmentID`),
  ADD CONSTRAINT `propertytransfer_toenduserid_foreign` FOREIGN KEY (`ToEndUserID`) REFERENCES `enduser` (`EndUserID`);

--
-- Constraints for table `propertytransferdetails`
--
ALTER TABLE `propertytransferdetails`
  ADD CONSTRAINT `propertytransferdetails_inventorydetailid_foreign` FOREIGN KEY (`InventoryDetailID`) REFERENCES `propertyinventorydetail` (`InventoryDetailID`),
  ADD CONSTRAINT `propertytransferdetails_transferid_foreign` FOREIGN KEY (`TransferID`) REFERENCES `propertytransfer` (`TransferID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
