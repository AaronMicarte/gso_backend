-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2026 at 05:49 PM
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
-- Table structure for table `assetrequest`
--

CREATE TABLE `assetrequest` (
  `RequestID` bigint(20) UNSIGNED NOT NULL,
  `RequestNumber` varchar(50) NOT NULL,
  `RequestedBy` int(11) NOT NULL,
  `DepartmentID` int(11) NOT NULL,
  `Purpose` text DEFAULT NULL,
  `Status` enum('pending','approved','rejected','issued','received','cancelled') NOT NULL DEFAULT 'pending',
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

--
-- Dumping data for table `assetrequest`
--

INSERT INTO `assetrequest` (`RequestID`, `RequestNumber`, `RequestedBy`, `DepartmentID`, `Purpose`, `Status`, `ReviewedBy`, `ReviewedAt`, `ReviewNotes`, `IssuedBy`, `IssuedAt`, `RequestDate`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'REQ-2026-0001', 3, 5, 'tests', 'received', 1, '2026-01-12 15:25:31', 'tests', 1, '2026-01-12 15:26:04', '2026-01-12 15:24:55', '2026-01-12 15:24:55', '2026-01-12 15:31:35', NULL),
(2, 'REQ-2026-0002', 3, 5, 'tests', 'received', 1, '2026-01-12 16:00:48', 'tests', 1, '2026-01-12 16:01:48', '2026-01-12 16:00:10', '2026-01-12 16:00:10', '2026-01-12 16:02:29', NULL);

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assetrequestdetail`
--

INSERT INTO `assetrequestdetail` (`RequestDetailID`, `RequestID`, `PropertyInventoryDetailID`, `ItemDescription`, `Unit`, `QuantityRequested`, `Purpose`, `IntendedEndUser`, `AdditionalNotes`, `PropertyClassification`, `AssignedEndUserID`, `AssignedDepartmentID`, `AdminNotes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, NULL, 'Laptop', 'unit', 1, 'tests', 'Aaron Micarte', 'tests', 'PAR', NULL, 5, 'tests', '2026-01-12 15:24:55', '2026-01-12 15:25:31', NULL),
(2, 1, NULL, 'Keyboard', 'unit', 1, 'tests', 'Aaron Micarte', 'tests', 'PAR', NULL, 5, 'tests', '2026-01-12 15:24:55', '2026-01-12 15:25:31', NULL),
(3, 1, NULL, 'Mouse', 'unit', 1, 'tests', 'Aaron Micarte', 'tests', 'PAR', NULL, 5, 'tests', '2026-01-12 15:24:55', '2026-01-12 15:25:31', NULL),
(4, 2, NULL, 'Printer 1', 'unit', 1, 'tests', 'IT Department', 'tests', 'ICS', 1, 5, 'tests', '2026-01-12 16:00:10', '2026-01-12 16:00:48', NULL);

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

--
-- Dumping data for table `asset_assignments`
--

INSERT INTO `asset_assignments` (`AssignmentID`, `AssetRequestDetailID`, `PropertyInventoryDetailID`, `AssignedUserID`, `AssignedDepartmentID`, `AssignmentType`, `PropertyClassification`, `AdminNotes`, `AssignedBy`, `AssignedAt`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, NULL, NULL, 5, 'issuance', 'PAR', 'tests', 1, '2026-01-12 15:25:31', '2026-01-12 15:25:31', '2026-01-12 15:25:31', NULL),
(2, 2, NULL, NULL, 5, 'issuance', 'PAR', 'tests', 1, '2026-01-12 15:25:31', '2026-01-12 15:25:31', '2026-01-12 15:25:31', NULL),
(3, 3, NULL, NULL, 5, 'issuance', 'PAR', 'tests', 1, '2026-01-12 15:25:31', '2026-01-12 15:25:31', '2026-01-12 15:25:31', NULL),
(4, 4, NULL, 1, 5, 'issuance', 'ICS', 'tests', 1, '2026-01-12 16:00:48', '2026-01-12 16:00:48', '2026-01-12 16:00:48', NULL);

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
(1, 'assetrequest', 1, 'created', 3, '{\"request_number\":\"REQ-2026-0001\",\"items_count\":3,\"department\":\"IT Department\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-12 15:24:55'),
(2, 'asset_request', 1, 'approved', 1, '{\"request_number\":\"REQ-2026-0001\",\"items_reviewed\":3,\"notes\":\"tests\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 15:25:31'),
(3, 'asset_request', 1, 'issued', 1, '\"{\\\"request_number\\\":\\\"REQ-2026-0001\\\",\\\"issuances_count\\\":1,\\\"date_issued\\\":\\\"2026-01-12\\\"}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL),
(4, 'property_issuance', 1, 'acknowledged', 3, '\"{\\\"document_number\\\":\\\"PAR-2026-0001\\\",\\\"acknowledged_by\\\":3,\\\"acknowledgment_date\\\":\\\"2026-01-12T15:31:35.326946Z\\\"}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', NULL),
(5, 'assetrequest', 2, 'created', 3, '{\"request_number\":\"REQ-2026-0002\",\"items_count\":1,\"department\":\"IT Department\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-12 16:00:10'),
(6, 'asset_request', 2, 'approved', 1, '{\"request_number\":\"REQ-2026-0002\",\"items_reviewed\":1,\"notes\":\"tests\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 16:00:48'),
(7, 'asset_request', 2, 'issued', 1, '\"{\\\"request_number\\\":\\\"REQ-2026-0002\\\",\\\"issuances_count\\\":1,\\\"date_issued\\\":\\\"2026-01-12\\\"}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL),
(8, 'property_issuance', 2, 'acknowledged', 3, '\"{\\\"document_number\\\":\\\"ICS-2026-0001\\\",\\\"acknowledged_by\\\":3,\\\"acknowledgment_date\\\":\\\"2026-01-12T16:02:29.517547Z\\\"}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', NULL);

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

--
-- Dumping data for table `asset_request_signatures`
--

INSERT INTO `asset_request_signatures` (`id`, `request_id`, `role`, `signed_by_user_id`, `source`, `storage_disk`, `storage_path`, `url`, `mime`, `width`, `height`, `size_bytes`, `sha256_hash`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, 'requester', 3, 'canvas', 'public', 'signatures/asset_request_1_requester_1768231495.png', 'http://localhost:8000/storage/signatures/asset_request_1_requester_1768231495.png', 'image/png', 1000, 300, 13690, '5b7b0fc02b0a0545f87da9d22001bb953c88cc6906f8474e5e044b97f9370daf', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-12 15:24:55', '2026-01-12 15:24:55'),
(2, 1, 'approver', 1, 'canvas', 'public', 'signatures/asset_request_1_approver_1768231531.png', 'http://localhost:8000/storage/signatures/asset_request_1_approver_1768231531.png', 'image/png', 1000, 300, 14423, '0c1ed1afec02872294f1ba637dd1a9aef04a83aebea7e915270046beb6240298', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 15:25:31', '2026-01-12 15:25:31'),
(3, 1, 'issuer', 1, 'canvas', 'public', 'signatures/asset_request_1_issuer_1768231564.png', 'http://localhost:8000/storage/signatures/asset_request_1_issuer_1768231564.png', 'image/png', 1000, 300, 14636, '33bc42eef06edb6d28a80219c63f1b39e695d5b3f870ad688bcbbbfaae2b8c92', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 15:26:04', '2026-01-12 15:26:04'),
(4, 2, 'requester', 3, 'canvas', 'public', 'signatures/asset_request_2_requester_1768233610.png', 'http://localhost:8000/storage/signatures/asset_request_2_requester_1768233610.png', 'image/png', 1000, 300, 15324, '2f5ea9952cf20f159dcce545eb25dcda6ec597d0e81e45464e5e18b6a044f2a2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-12 16:00:10', '2026-01-12 16:00:10'),
(5, 2, 'approver', 1, 'canvas', 'public', 'signatures/asset_request_2_approver_1768233648.png', 'http://localhost:8000/storage/signatures/asset_request_2_approver_1768233648.png', 'image/png', 1000, 300, 15306, '14309ea505409f5ba80299d2f3e8ab36b9ab5776bd2f4915c8a154b6fe54a5e0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 16:00:48', '2026-01-12 16:00:48'),
(6, 2, 'issuer', 1, 'canvas', 'public', 'signatures/asset_request_2_issuer_1768233708.png', 'http://localhost:8000/storage/signatures/asset_request_2_issuer_1768233708.png', 'image/png', 1000, 300, 13738, 'd6050ae68128453ace11ee0e4e603b1844c83bf5ecffcbb8f1561e101fe94cf2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-12 16:01:48', '2026-01-12 16:01:48');

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
(1, 6, 1, 5, 'PAR-2026-0001', 'good', '2026-01-12', 2, 1, 'tests', '2026-01-12 23:26:04', '2026-01-12 23:26:04'),
(2, 3, 1, 5, 'PAR-2026-0001', 'good', '2026-01-12', 2, 1, 'tests', '2026-01-12 23:26:04', '2026-01-12 23:26:04'),
(3, 4, 1, 5, 'PAR-2026-0001', 'good', '2026-01-12', 2, 1, 'tests', '2026-01-12 23:26:04', '2026-01-12 23:26:04'),
(4, 8, 2, 5, 'ICS-2026-0001', 'DCP-T530DW Ink Tank Printer · A4 print speed up to 16 ipm (Black)/ 9 ipm (Colour) · WiFi, Airprint, Mopria, Wireless Direct, USB 2.0 · Auto 2-sided (Duplex)', '2026-01-12', 2, 1, 'tests', '2026-01-13 00:01:48', '2026-01-13 00:01:48');

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
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(100) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `UpdatedDate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`CategoryID`, `CategoryName`, `Description`, `IsActive`, `CreatedDate`, `UpdatedDate`, `deleted_at`) VALUES
(1, 'Information Technology', 'Categories belong to IT', 1, '2025-12-08 21:38:28', '2025-12-09 17:54:46', '2025-12-09 09:54:46'),
(2, 'Information Technology', 'IT equipment and software', 1, '2025-12-09 17:28:49', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `classification`
--

CREATE TABLE `classification` (
  `ClassificationID` int(11) NOT NULL,
  `CategoryID` int(11) NOT NULL,
  `ClassificationName` varchar(100) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `UpdatedDate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classification`
--

INSERT INTO `classification` (`ClassificationID`, `CategoryID`, `ClassificationName`, `Description`, `CreatedDate`, `UpdatedDate`, `deleted_at`) VALUES
(1, 1, 'General Assets', 'Fallback classification for property types', '2025-12-23 16:00:00', NULL, NULL);

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
(1, 'Good', 'In working condition', NULL),
(2, 'Fair', 'Minor wear but functional', NULL),
(3, 'Poor', 'Significant wear, needs repair', NULL),
(4, 'Unserviceable', 'No longer functional', NULL),
(5, 'Damaged', 'Physically damaged', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `deliveryreceipt`
--

CREATE TABLE `deliveryreceipt` (
  `DRID` int(11) NOT NULL,
  `DRNumber` varchar(50) NOT NULL,
  `SupplierName` varchar(255) DEFAULT NULL COMMENT 'Supplier name (manual entry or from supplier table)',
  `PONumber` varchar(50) DEFAULT NULL COMMENT 'Manual PO Number reference (optional)',
  `InvoiceNumber` varchar(50) DEFAULT NULL COMMENT 'Invoice number from supplier',
  `ReceivedBy` int(11) NOT NULL,
  `DateReceived` date NOT NULL,
  `Remarks` text DEFAULT NULL,
  `dr_picture` varchar(255) NOT NULL COMMENT 'Path to the uploaded DR picture file',
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deliveryreceipt`
--

INSERT INTO `deliveryreceipt` (`DRID`, `DRNumber`, `SupplierName`, `PONumber`, `InvoiceNumber`, `ReceivedBy`, `DateReceived`, `Remarks`, `dr_picture`, `CreatedDate`, `deleted_at`) VALUES
(15, 'DR-2026-010', 'TechnoWorld Inc.', 'PO-2026-010', 'INV-2026-010', 1, '2026-01-11', 'All items for PAR', '', '2026-01-11 09:00:00', NULL),
(16, 'DR-2026-011', 'OfficePro Supplies', 'PO-2026-011', 'INV-2026-011', 1, '2026-01-11', 'Mixed PAR and ICS', '', '2026-01-11 10:30:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `deliveryreceiptdetail`
--

CREATE TABLE `deliveryreceiptdetail` (
  `DRDetailID` int(11) NOT NULL,
  `DRID` int(11) NOT NULL,
  `ItemDescription` varchar(255) NOT NULL COMMENT 'Manual item description',
  `Brand` varchar(100) DEFAULT NULL,
  `ModelNumber` varchar(100) DEFAULT NULL,
  `SerialNumber` varchar(100) DEFAULT NULL,
  `PropertyTypeID` int(11) NOT NULL,
  `QuantityDelivered` int(11) NOT NULL,
  `UnitOfMeasureID` int(11) NOT NULL DEFAULT 1,
  `UnitCost` decimal(18,2) DEFAULT NULL,
  `TotalCost` decimal(18,2) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deliveryreceiptdetail`
--

INSERT INTO `deliveryreceiptdetail` (`DRDetailID`, `DRID`, `ItemDescription`, `Brand`, `ModelNumber`, `SerialNumber`, `PropertyTypeID`, `QuantityDelivered`, `UnitOfMeasureID`, `UnitCost`, `TotalCost`, `deleted_at`) VALUES
(32, 15, 'Laptop', 'Lenovo', 'ThinkPad E14', 'LTP202601', 3, 2, 1, 42000.00, 84000.00, NULL),
(33, 15, 'Printer', 'HP', 'LaserJet Pro M404', 'PRN202601', 3, 1, 1, 18500.00, 18500.00, NULL),
(34, 15, 'Office Table', NULL, NULL, NULL, 4, 3, 1, 3500.00, 10500.00, NULL),
(35, 16, 'Steel Cabinet', NULL, NULL, NULL, 4, 2, 1, 8000.00, 16000.00, NULL),
(36, 16, 'Desktop Computer', 'Acer', 'Veriton X', 'DTC202601', 3, 1, 1, 38000.00, 38000.00, NULL),
(37, 16, 'Executive Chair', NULL, NULL, NULL, 4, 2, 1, 6500.00, 13000.00, NULL);

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
(3, 'Finance Department', NULL, 'FIN', 1, '2025-12-05 02:07:27', '2025-12-11 22:27:05', '2025-12-11 14:27:05'),
(4, 'Human Resources', NULL, 'HR', 1, '2025-12-05 02:07:27', NULL, NULL),
(5, 'IT Department', NULL, 'IT', 1, '2025-12-05 02:07:27', NULL, NULL),
(6, 'General Services Office', NULL, 'GSO', 1, '2025-12-09 17:28:09', NULL, NULL),
(7, 'Administrative Office', NULL, 'ADMIN', 1, '2025-12-09 17:28:09', NULL, NULL),
(8, 'Finance Department', NULL, 'FIN', 1, '2025-12-09 17:28:09', NULL, NULL),
(9, 'Human Resources', NULL, 'HR', 1, '2025-12-09 17:28:09', NULL, NULL),
(10, 'IT Department', NULL, 'IT', 1, '2025-12-09 17:28:09', NULL, NULL),
(11, 'Molugan', 'First Barangay', 'Molu', 1, '2025-12-09 23:01:17', '2025-12-26 17:13:13', NULL);

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
(1, 2, 5, 'Aaron Micarte', NULL, NULL, 1, '2026-01-12 23:26:04', '2026-01-12 23:26:04', NULL),
(2, 2, 5, 'IT Department', NULL, NULL, 1, '2026-01-13 00:01:48', '2026-01-13 00:01:48', NULL);

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
  `Remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `iardetail`
--

INSERT INTO `iardetail` (`IARDetailID`, `IARID`, `DRDetailID`, `PropertyTypeID`, `ConditionID`, `QuantityAccepted`, `QuantityRejected`, `VerifiedBrand`, `VerifiedModel`, `VerifiedSerial`, `Remarks`) VALUES
(1, 1, 35, 4, 1, 2, 0, 'N/A', 'N/A', 'N/A', 'good'),
(2, 1, 36, 3, 1, 1, 0, 'Acer', 'Veriton X', 'DTC202601', 'good'),
(3, 1, 37, 4, 1, 2, 0, 'N/A', 'N/A', 'N/A', 'good'),
(4, 2, 32, 3, 1, 2, 0, 'Lenovo', 'ThinkPad E14', 'LTP202601', 'good'),
(5, 2, 33, 3, 1, 1, 0, 'HP', 'LaserJet Pro M404', 'PRN202601', 'good'),
(6, 2, 34, 4, 1, 3, 0, 'N/A', 'N/A', 'N/A', 'good');

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
(1, 1, 'inspected', 1, 'canvas', 'local', 'signatures/iar_1_inspected_1768145365.png', '/storage/signatures/iar_1_inspected_1768145365.png', 'image/png', 1000, 300, 11433, '07434ed77d06c78c4e938ee417ec13b4436a8c265a299257bb4f0a80a6a92582', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 15:29:25', '2026-01-11 15:29:25'),
(2, 1, 'accepted', 1, 'canvas', 'local', 'signatures/iar_1_accepted_1768145366.png', '/storage/signatures/iar_1_accepted_1768145366.png', 'image/png', 1000, 300, 11663, 'f253ff3b9390531d4af4801be4d35e64b2f0b33400417aef2d163d7ccf683763', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 15:29:25', '2026-01-11 15:29:26'),
(3, 2, 'inspected', 1, 'canvas', 'local', 'signatures/iar_2_inspected_1768145429.png', '/storage/signatures/iar_2_inspected_1768145429.png', 'image/png', 1000, 300, 11545, '7e4fb246cfa58875b01163f5a71c5cf32f4cfcd48b730c32bd26e3485fa56bd3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 15:30:29', '2026-01-11 15:30:29'),
(4, 2, 'accepted', 1, 'canvas', 'local', 'signatures/iar_2_accepted_1768145431.png', '/storage/signatures/iar_2_accepted_1768145431.png', 'image/png', 1000, 300, 11771, '5f93add6729fd602a857569da8625bbb5187bbc09b330eb387a2ac2cb7b4b659', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 15:30:29', '2026-01-11 15:30:31');

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

--
-- Dumping data for table `icsdetail`
--

INSERT INTO `icsdetail` (`ICSDetailID`, `ICSID`, `PropertyID`, `QuantityIssued`, `UnitCost`, `TotalCost`, `Remarks`) VALUES
(1, 1, 4, 1, 18500.00, 18500.00, 'tests');

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
(1, 'IAR-2026-0001', 16, 1, 1, '2026-01-11', '2026-01-11', 'good', '2026-01-11 23:29:25', NULL),
(2, 'IAR-2026-0002', 15, 1, 1, '2026-01-11', '2026-01-11', 'good', '2026-01-11 23:30:29', NULL);

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

--
-- Dumping data for table `inventorycustodianslip`
--

INSERT INTO `inventorycustodianslip` (`ICSID`, `ICSNumber`, `IssuedTo`, `IssuedBy`, `DateIssued`, `TotalCost`, `Remarks`, `CreatedDate`) VALUES
(1, 'ICS-2026-0001', 2, 1, '2026-01-12', 18500.00, 'tests', '2026-01-13 00:02:29');

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
(15, 3, '2026-01-14 00:22:47', 'Logout', '127.0.0.1');

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
(105, '2026_01_12_200000_create_pardetail_and_fix_par_header', 23);

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

--
-- Dumping data for table `par`
--

INSERT INTO `par` (`PARID`, `PARNumber`, `PropertyID`, `IssuedTo`, `IssuedBy`, `DateIssued`, `TotalCost`, `Remarks`, `CreatedDate`) VALUES
(1, 'PAR-2026-0001', NULL, 1, 1, '2026-01-12', 86500.00, 'tests', '2026-01-12 23:31:35');

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

--
-- Dumping data for table `pardetail`
--

INSERT INTO `pardetail` (`PARDetailID`, `PARID`, `PropertyID`, `QuantityIssued`, `UnitCost`, `TotalCost`, `Remarks`) VALUES
(1, 1, 1, 1, 42000.00, 42000.00, 'tests'),
(2, 1, 2, 1, 38000.00, 38000.00, 'tests'),
(3, 1, 3, 1, 6500.00, 6500.00, 'tests');

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
(1, NULL, NULL, NULL, 1, '2026-01-11', NULL, 'Auto-ingested from IAR #IAR-2026-0001', '2026-01-11 23:31:21', NULL, 'INV-20260111-233121-1', NULL),
(2, NULL, NULL, NULL, 2, '2026-01-11', NULL, 'Auto-ingested from IAR #IAR-2026-0002', '2026-01-11 23:31:24', NULL, 'INV-20260111-233124-2', NULL);

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
(1, 1, 4, NULL, 'good', '2026-000001', 'N/A', 1, 0, 1, 1, 8000.00, 8000.00, 1, 35, 1, 'N/A', 'N/A', 'good', '2026-01-11 23:31:21', '2026-01-12 23:22:01', NULL),
(2, 1, 4, NULL, 'good', '2026-000002', 'N/A', 1, 1, 1, 1, 8000.00, 8000.00, 1, 35, 1, 'N/A', 'N/A', 'good', '2026-01-11 23:31:21', NULL, NULL),
(3, 1, 3, NULL, 'good', '2026-000003', 'DTC202601', 1, -1, 2, 1, 38000.00, 38000.00, 2, 36, 1, 'Acer', 'Veriton X', 'good', '2026-01-11 23:31:21', '2026-01-12 23:26:04', NULL),
(4, 1, 4, NULL, 'good', '2026-000004', 'N/A', 1, 0, 2, 1, 6500.00, 6500.00, 3, 37, 1, 'N/A', 'N/A', 'good', '2026-01-11 23:31:21', '2026-01-12 23:26:04', NULL),
(5, 1, 4, NULL, 'good', '2026-000005', 'N/A', 1, 1, 1, 1, 6500.00, 6500.00, 3, 37, 1, 'N/A', 'N/A', 'good', '2026-01-11 23:31:21', NULL, NULL),
(6, 2, 3, NULL, 'Processor: Get at least an Intel Core i5 or its AMD equivalent for fast performance. RAM: 8GB minimum, but 16GB is better for handling many tasks at once. Storage: Choose an SSD with at least 256GB for quick access and boot times. Display: A Full HD (1080', '2026-000006', 'LTP202601', 1, -1, 2, 1, 42000.00, 42000.00, 4, 32, 1, 'Lenovo', 'ThinkPad E14', 'good', '2026-01-11 23:31:24', '2026-01-12 23:46:47', NULL),
(7, 2, 3, NULL, 'good', '2026-000007', 'LTP202601', 1, 1, 1, 1, 42000.00, 42000.00, 4, 32, 1, 'Lenovo', 'ThinkPad E14', 'good', '2026-01-11 23:31:25', NULL, NULL),
(8, 2, 3, NULL, 'DCP-T530DW Ink Tank Printer · A4 print speed up to 16 ipm (Black)/ 9 ipm (Colour) · WiFi, Airprint, Mopria, Wireless Direct, USB 2.0 · Auto 2-sided (Duplex)', '2026-000008', 'PRN202601', 1, -1, 2, 1, 18500.00, 18500.00, 5, 33, 1, 'HP', 'LaserJet Pro M404', 'good', '2026-01-11 23:31:25', '2026-01-13 00:01:48', NULL),
(9, 2, 4, NULL, 'good', '2026-000009', 'N/A', 1, 0, 1, 1, 3500.00, 3500.00, 6, 34, 1, 'N/A', 'N/A', 'good', '2026-01-11 23:31:25', '2026-01-12 23:22:01', NULL),
(10, 2, 4, NULL, 'good', '2026-000010', 'N/A', 1, 0, 1, 1, 3500.00, 3500.00, 6, 34, 1, 'N/A', 'N/A', 'good', '2026-01-11 23:31:25', '2026-01-12 23:22:01', NULL),
(11, 2, 4, NULL, 'good', '2026-000011', 'N/A', 1, 1, 1, 1, 3500.00, 3500.00, 6, 34, 1, 'N/A', 'N/A', 'good', '2026-01-11 23:31:25', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `propertyissuance`
--

CREATE TABLE `propertyissuance` (
  `IssuanceID` int(11) NOT NULL,
  `RequestID` bigint(20) UNSIGNED DEFAULT NULL,
  `IssuedBy` int(11) NOT NULL,
  `IssuedTo` int(10) UNSIGNED DEFAULT NULL,
  `IssuedToName` varchar(255) DEFAULT NULL,
  `DocumentType` varchar(10) DEFAULT NULL COMMENT 'PAR or ICS',
  `DocumentNumber` varchar(50) DEFAULT NULL,
  `Status` varchar(50) NOT NULL DEFAULT 'pending_acknowledgment' COMMENT 'pending_acknowledgment, acknowledged, completed',
  `DateIssued` date NOT NULL,
  `Remarks` text DEFAULT NULL,
  `AcknowledgmentSignature` text DEFAULT NULL COMMENT 'Base64 encoded signature',
  `AcknowledgedAt` datetime DEFAULT NULL,
  `AcknowledgmentNotes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `propertyissuance`
--

INSERT INTO `propertyissuance` (`IssuanceID`, `RequestID`, `IssuedBy`, `IssuedTo`, `IssuedToName`, `DocumentType`, `DocumentNumber`, `Status`, `DateIssued`, `Remarks`, `AcknowledgmentSignature`, `AcknowledgedAt`, `AcknowledgmentNotes`, `deleted_at`, `CreatedDate`) VALUES
(1, 1, 1, 1, 'Aaron Micarte', 'PAR', 'PAR-2026-0001', 'acknowledged', '2026-01-12', 'tests', 'data:image/png;base64,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', '2026-01-12 23:31:35', 'tests', NULL, '2026-01-12 23:26:04'),
(2, 2, 1, 2, 'IT Department', 'ICS', 'ICS-2026-0001', 'acknowledged', '2026-01-12', 'tests', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAA+gAAAEsCAYAAABQRZlvAAAQAElEQVR4AezdW8wd1X338fljfHZsB3iFOdsmEiAFq5XsXBDjGmEFpVFooEQEKSogCISQi1TQi0pECYKLXIDaSjRIUBV6RSWQSEFcBGHRguGittQUXwBSAIMaIC0mHAw+YfL6N5u19trz7P08+zCHNWu+ltcza8+eWYfP2of5z2mf8Ef+IYAAAggggAACCCCAAAIIIIBA4wInZJX+o3AEEEAAAQQQQAABBBBAAAEEEBhHoN0B+jg9ZBkEEEAAAQQQQAABBBBAAAEEWiBAgD7PIPEUAggggAACCCCAAAIIIIAAAnUJEKDXJT23HuYggAACCCCAAAIIIIAAAggg4AUI0D1Fahn6gwACCCCAAAIIIIAAAggg0CYBAvQ2jVZMbaUtCCCAAAIIIIAAAggggAACpQoQoJfKSWFlCVAOAggggAACCCCAAAIIINA1AQL0ro04/ZUACQEEEEAAAQQQQAABBBCIToAAPbohoUHtF6AHCCCAAAIIIIAAAggggMDkAgTok5uxBgLNClA7AggggAACCCCAAAIIJClAgJ7ksNIpBKYXYE0EEEAAAQQQQAABBBBoRoAAvRl3akWgqwL0GwEEEEAAAQQQQAABBEYIEKCPgGE2Agi0UYA2I4AAAggggAACCCDQXgEC9PaOHS1HAIG6BagPAQQQQAABBBBAAIEKBQjQK8SlaAQQQGASAZZFAAEEEEAAAQQQ6LYAAXq3x5/eI4BAdwToKQIIIIAAAggggEDkAgTokQ8QzUMAAQTaIUArEUAAAQQQQAABBGYVIECfVZD1EUAAAQSqF6AGBBBAAAEEEECgAwIE6B0YZLqIAAIIIDC/AM8igAACCCCAAAIxCDQeoO/YsSNbtGhRDBa0AQEEEEAAgSoEKBMBBBBAAAEEEBhLoPEAfefOndnnn3+emVn0afHixdnatWvztGnTpkzp9ttvHwuahRBAAAEEEKhGgFIRQAABBBBAIBWBxgP0NkF+9tln2YcffpinvXv3Zkr33nvvnB0LJ5xwQqZEQN+m0aWtCCCAAAJDBZiJAAIIIIAAArUJNB6gK5Ctrbc1VfTHP/4xU5o0oNep/lWmE088MVuyZElpadmyZdmf/dmf1aRKNQgggAACKQrQJwQQQAABBBDoCzQeoB87diwPZhXQTpM2b96cH8GeZt1p1rntttsypQsvvDBbs2ZNnhT4mvVO0e/Tjp9z7dCp/lUmWR89ejQrKx0+fDh77rnncv/Vq1cTrI8/5CyJAAIIIFCPALUggAACCCDQKoHGA/RZtfbs2ZMH+GY2a1FjrX/PPfdkSi+99FL2wQcf5EkBrwusXbDtpgrmlcoM6MdqaM0LffzxxwPBes3VUx0CCCCAAAINCFAlAggggAAC5Qq0PkAvl6P80hTMK40b0LvAvg3TRx99NNu2bVu2fPnyATgF66eddtrAPB4ggAACCCCAwIQCLI4AAggg0DkBAvTODXl5Hb7qqquy//iP/8jOOOOMgULNLPvLv/zLgXk8QAABBBBAAIG4BGgNAggggEB8AgTo8Y1J61q0atWqvM3r16/Pbrjhhvxn8+677758Hn8QQAABBBBAoJMCdBoBBBBAYAoBAvQp0FhlUODv/u7vsu3bt2cPPfRQ9k//9E+DT/IIAQQQQAABBBAoXYACEUAAgTQFCNDTHNdae6Xg/Nlnn82D9ForpjIEEEAAAQQQQKAKAcpEAAEEGhIgQG8InmoRQAABBBBAAAEEuilArxEYJnDiiSdmZjZVOuEEwrphpm2cx0i2cdRoMwIIJCewadOmTF+uW7ZsSa5vdAgBBBBAoFYBKmuRQBiUHzt2bOqW6xegzCxTeVMXwopRCBCgRzEMNAIBBLousHfv3kxfrnv27Mn3nHfdg/4jgAACCMQqQLvKEFAgbWbZLEH5sHaoPJU97DnmtUOAAL0d40QrEUAgYQEdOS92z8yyrVu3FmfzGAEEEEAAgbQFOtA7s9GBuXbWT5oWLVo0oEaQPsDRugcE6K0bMhqMAAKpCeiLeFifXnjhhWGzmYcAAggggAACUwo0uZqObJvZnCYowNa2gNKcJ8eY8dlnn+Vn4YWLEqSHGu3KE6C3a7xoLQIIJCawePHigR5t3rx54PHKlSsHHvMAAQQQQAABBKIVGNkws7lHzc0sD6wVYI9ccYInigE+QfoEeBEtSoAe0WDQFAQQ6J5A+KV83nnnZbt3786+/vWve4hPP/3U58kggAACCCCAQLsERh01VzD9+eefT9iZhRdXueFSBOmhRjvyBOjtGCdaiQACHRB45ZVX8l7u2rVr4EZxHEXPWfiDAAIIIIBAqwQUnCtADhtt1jtqHs4rOz8sSB+rDhaKQoAAPYphoBEIINBFgfDmcGaD16RddNFFnoSj6J6CDAIIIIAAAq0QGBacK3Cu4qj5MBDVFc43G9zOCJ+rK0894wkQoI/n1NmlLr/88qx4jWxnMeg4AiULhF+exS9sjqKXjE1xCCCAAAII1CRQDM7Nqj9qPqxr4XaGnjdLOkhXF5NIBOhJDGN1nXjyySczXSNrxhu6OmVK7qLAODu+OIrexVcGfW6LwDnnnJOFZ8G0pd20EwEEqhUYFpwXd8JX24LB0nWH+HAOn1uhxiT5+pYlQK/PupU16UPGNdyMIN1ZMEVgVgHt+HJl6OZwLh9OOYoeapBHIC6Bt956K7/7shnfjXGNDK1BoDkBbTeH15ybWdZkcC4JbW+EQXrxqLqWIUUgEDSBAD3AIDtX4OjRowMzzdgQGQDhAQIlCLibww0riqPow1SYh0BcAuHGb1wtozUIIFCXQIzBueu7gnSz/ja8WT/vlmEaj0AVAXo8vaMlpQh8+9vfHijnlFNOGXjMAwQQmEwgPL3MbP4vSY6iT2bL0gjUJRC+j5s+QlZXn6kHAQSGC8QcnLsWFz+nzObf/nDrMa1foIUBev1IXa/xiSeeyMIgff/+/V0nof8IzCQQnl5W/MIcVjBH0YepMA+BZgXC01ibbQm1I4BAkwJtCM6dT/Fsn3BHo1uGafMCBOjFMeDxUAEF6UOfYCYCCFQuoKPolVdCBQggMJOANtJnKoCVEUCgdQJ634c768yav+Z8PkSd6h4G6TpgoD7Mtw7P1S9AgF6zOdUhgEC3Bcz6p5Rt3LhxbIzVq1f7ZdesWePzZBBAoDmB8D0cbqQ31yJqRgCBugQU2Ibve7O4g3PnoiDdrL8tEvbBLcO0WQEC9Gb9y6690vLM+m/mSiuicAQ6IvDaa6+N3dMtW7b4ZQ8cOODzZBBAoDmBSd7DzbWSmhFAoGyBtgbnzqF4eZ0Z2/jOJoYpAXoMo9CaNtDQmASWLVuWmZlPMbWNtgwXCK/1MrPhC42Y+8wzz/hnil+s/gkytQhcfvnl2eLFi2upi0riFwhPFw3f4/G3nBYigMA0Am0Pzl2fw88uzTObbLtE65CqESBAr8Y1+VKvueaa8vtIiRMJHD58eGB5Mz5YB0AifKBrvVyzNmzY4LJMWybw5JNPZsVTBFvWBZpbosA555zjSwvf434mGQQQSEogPCXcrB2ntQ8bAH2PFYN0djIOk6p/HgF6/eatrbHtR4xaCz+i4WZzA3I+WEdgRTibU2MjHBSahMAUAsX3Mt+VUyCyCgItETAb3PZq+xltxSBdOxnZlmz+xUiA3vwYtKYF4e+fP/fcc61pd00Nrb0afSnog1SnurvK9djlmcYlEH7hmQ1+wY/bUm4UN64UyyFQr4BOeXU1aoPX5ZkigEA6AmaD392pbHPpM8us37dU+tXmVx4BeptHj7Z3SGB0Vw8ePDj6SZ6JRiD8wtPOlWkaFt4o7qOPPpqmCNZBAIEKBI4ePVpBqRSJAAKxCJj1A1i1KfxO1+O2p+J2SXhQoe19a2P7CdDbOGoNtXnbtm2+5vfee8/nyTQvYNb/4pjqQ7X5LtCCMQTCG8WNsTiLIIBAQwKc5t4QPNUiUIGAWX8bS8WnFpyrT0rh9eip9lH9bEMiQG/DKNFGBBYQWLp0qV8ixg9V37iOZsKdJuEXYEc56DYCSQpwmnuSw0qnOi4Qfn+LIuVtrOKp7sW+q/+kegQI0OtxTqKWRx55xPeD0/k8RRSZjp/mHsUYzNeI8AtdX4DzLTvJczt27JhkcZZFAIEKBfherBCXohFoQEABavj9bTZ4JL2BJlVepfrsKgn77uYxrUeAAL0eZ2pBoHIBs/4XR/gBW3nFyVcQVwfDG8Xt3r07rsbRGgQQQAABBBIQ0BkxYYBq1t6fU5tkOHQQwYztyUnMqliWAL0K1Q6UGX5odaC7regip7nHOUxm/S+6jRs3zm3khHO4UdyEYCyOQI0C4bXnS5YsqbFmqkIAgTIFjh075osz60Zw7jocHuRhe9+p1DslQK/Xm9oQqEyA09wroy2t4OLvJU9T8KQ3ipumDtZBIBTYt29f9vDDD4ezyI8QOP/88/0znPLuKcgg0CoBs/6OdTW8eIdzzUs5cRS9+dElQG9+DGgBApUIrFixopJyKXR8gXAvtNngF/74pUS9JI3rgMCdd96ZXX/99dkll1zSgd7O1sWXXnpptgJYGwEEGhUIv7fVkK4eQQ4dumqg8W8qEaA3JU+9CFQgYNYPAg8dOlRBDRQ5iUD4pbZhw4ZJVp132ZNPPtk/f8opp/h8ehl6FIOAjqDH0I62tMGs/znMae5tGTXaiUCWFa87D7/Du+ajo+hhn8OAPZxPvhqBE6opllJTFeANGvfILlu2zDewy18sHiGiTBmnt7vuXHTRRS6b7d+/3+fJTCjA4mMJsLNvLCa/0Fe/+lWf5zR3T0EGgegFitedR9/gihto1t/ZyDZlxdiF4gnQCyA8nF9g/fr1foFzzz3X58nEIfDpp5/G0RBakYU7s8z6X3Jl0DzxxBNlFEMZFQukUrw7gv6b3/wmlS5V2g9Oc6+Ul8IRqETAbPB7umvXnQ9DxWCYSj3zCNDrcaYWBBDomEC4t7nqL7nLL7+8Y7p0N8uy2hDY8TcbdXhm02wlsTYCCFQhEO5QV/nh97cekxCoW4AAvW7xltd36aWX+h64oyp+BhkEEKhNILwO/cUXX6ytXirqikC/n+4U9wMHDvRnkptXYOnSpf75w4cP+zwZBBCITyAMyM0Gj6TH11pa1AUBAvQujHKJfXzggQd8aVUfFfQVkZlagDu5T00304rh3vhFixbNVNaolbkOfZQM88sWcJ/1xZsGzVRP4iu7nRqJd5PuIZCcgPu8S65jdKhVAgTorRouGovAwgJm/b2/bCQu7FXFEuHe+KqCGq5Dr2LkKHOYwJlnnjlsdtTzYmscp7nHNiK0B4GeQLhD3ay//dR7lr9mPROz3hSRegQI0OtxphYEahMINwTDQLG2BlBRbQL6SRhXGT+35iSYli3wrW99yxf54x//2Oc7nBmr65zmPhYTCyHQqEC4ncTR87lDIRMZaTr3WeZUJUCAXpVswuWGexsT7mZru8YNneIZOrNq9zh/85vf7DWPJQAAEABJREFU9J3l59Y8BRkEohCY/gymKJpPIxBAAAEEGhIgQG8Ivs3V8lNrbR492l61QLgD66yzzqq0Ok5zr5SXwr8Q2L59+xe5LHvqqad8nsxkAuHZTZOtWfLSFIcAArlA+H1tVu0O9bxC/iAwpgAB+phQLNYXCO/k3p9LDgEEJKBTwTRVevPNNzUhIdBqgauuusq3nzM1PMVYmS6e5j4WDAshEIFA+H3NKdwRDAhN8AIE6J6CzDQC/NTaNGqsg0B5AuHPrXEdenmulDQoYNY7usRPrQ26LPSI09wXEpr4eVZAAAEEkhcgQE9+iMvvID+1Vr4pJSIwrQA/tzatHOshUL8Ap7nXbz5ZjSyNAAIINC9AgN78GNACBEoXMOsd7VLBK1eu1IRUg8CiRYt8LWb9MfAzK8hwHXoFqBSJQIkCnOZeImbbi6L9CCCAwBgCBOhjILEIAm0WOHjwYJub36q2h9ezVX2DuFbB0FgEOizAae4dHvyau051CCCQhgABehrjWHsvwjtf3nTTTbXXT4UIxCgQBuh13iCO69BjfDXQJgSGC3Ca+3AX5kYvQAMRQKAmAQL0mqBTqyb8qbWdO3em1r3W92f58uWt7wMdGF+A69DHt2JJBJoQ4DT3JtTLqfPEE0/MzGxO0vxyaqCUngB/EUDACRCgOwmmEwmEP7X2zjvvTLQuC9crEB7VrbdmaqtLgOvQ65KmHgSmE+A09+ncmlpLwbdZLyg/duzY0GZovpYb+iQz4xOgRQi0SIAAvUWDFWtTDx8+HGvTOtuuTz75pLN9b6rjTdwgblRf+bm1UTL1zl+xYkUWXg5Ub+3UhgACkwgo2DabPygvlqcgvTiPx90UoNcIlClAgF6mZofK4qfWOjTYdHUsgfBMhSZuEPftb3/bt3P//v0+T6Y5Ad2gUa8LM2uuEdSMAALzCrjAfL5gW+/jMJn13tNmvem8FfAkArMLUELHBAjQOzbgdBcBBKoR0MabK7nOG8S5OjnN3UkwRQABBBYWWCgw11lR+lxXKpb2+eefZ5qvafE5HiPQPgFaHJsAAXpsI9Ki9nDqZosGi6YigEDtAmb9o2t8XtbOH12FZv3Xw9atW6NrX1caNF9gbmZ54K3g+7PPPusKCf1EoFoBSp9YgAB9YjJWGCbAT60NU2l2nll/Y5DgoNmxqKt2fm6tLunx6gl/TUEb/OOtxVKpCoSvh//6r/9KtZtR98vMsmGnsruj5RwRj3r4aBwCQwVSnEmAnuKo1tQnfmqtJugpq9ENqtyqBAdOopppuAPErL9jpJraRpfKz62NtmniGd2s0az/eghfJ020hzoR6KqAmeU/k1bsvwvMOVpelOExAgh8IdDIhAC9EfY0KuWn1uIexwMHDsTdwERb18QN4hwl16E7iXim4VFTdpTFMy5NtORP//RPfbW6gaB/QKYyAe0UM7M55Zv1TmUnMJ9DwwwEEKhVYHhlBOjDXZg7hkB4J3d+am0MMBZJViAMvJq4Qdwo2DPOOGPUU8yvSaB4FN1sbrBQU1OoJiKB8DMjomYl0xR3nfkwZ83jVPZkhpqOIJCkQGkBepI6dGpsAb7sxqZiQQQqFfje977ny3/nnXd8nkxzAuHlJmrFqlWrNCF1TGDXrl0d63Ez3TUbfp25AnOlZlpFrQgggMD4Am0J0MfvEUsigIAXMOsfrdOpfv4JMskKPPLII75vbIx6ikYzutzErP9e1FH1RhtE5QgkKKDvOLP++8x1UZ+DSu4xUwScgFn/9aLXj5vPFIGmBQjQ8xHgz7QCfKBNK1fPeuGROzZQqjEP3wNm/S/7amobr9QlS5b4BTnN3VM0mimeZRS+bhpt2JiVu88PNx1zNRZDoHKBUaezm/WuM6+8AVTQWoHwc5jPttYOY5INJ0CvY1g7Ugc/tRbfQOvIXXytSrdFTd4gLlS98sor/cO3337b58k0K7By5UrfADYGPQUZBKYWUIA17GfT9P4q7hSbuhJWTFZANwk06+9Y1+sp2c7SsVYJEKC3ariGN7bJufzUWpP61B2DgDYEXTtiuUFceJq7axvT5gW0w8ysvzFo1s833zpaUIfA6tWrfTVr1qzxeTKTCYw6au5+Nm2y0li6ywJhUB5+n3fZhL43L0CA3vwYxN6CedvHT63Ny9PIk/v27csefvjhRuqm0ngETj/9dN8YTnP3FI1nikf1wo3DxhtHAyoX2LJli6/jo48+8nky4wvoPTPqqLmOiI5fEksikGV6zZj1d5bq9YULAk0LEKA3PQItr3/2n1prOUCEzb/zzjuz66+/PrvkkksibF26TTLrf8HH0Mtt27b5ZnCau6eIIsOp7lEMQyONeOaZZxqpN4VKRx0111FPpRT6SB+aEQiDcl5LzYwBtQ4KEKAPevBoBoHikaEZiipv1Q6WpCPoHex2I10Ov9TN4grQdZq7Wb9NHEVv5CUytFKd6h4+Eb6Owvnk0xfYsWNH+p0soYdmc386zcwygqkScClizlF0s/53JzwINCFAgN6EOnUmIxBbRx577LHsP//zP2NrVrLtCTcOh51y2XTHr776at+Ed955x+fJNC9QPIq+atWq5htFC2oRCK9D3717dy11trUSd9S82H599nJQoKjC41kEijtK9dqbpTzWRWAWAQL0WfRYNxcofqjlM/lThsBYZSgo37BhQ2Zm2Xe/+93s008/zdf7n//5n3zKn+4K6Ci66702aF2eafMCOopu1j9K88knnzTfKFpQiwDXoS/MrODIjKPmC0uxRFkCxWvRY9zpXlZfKSd+gdYH6ASHzb/Ili5d6hvBT615isoy999/f3bFFVdkX/7yl7Ply5fnQXnxtHbdyfbWW29doA083QWBJUuW+G5ymruniCJTPALI91kUw1J5I7gOfTTxqMBca2gnY/E9o/kkBMoSKL6++EwuS7ZXjnt/m1l+UMls7nTx4sW9hTv+t/UBesfHL4run3baab4dO3fu9Hky0wu4IPz888/PA3F9SSiZWfajH/0o+9WvfpV98MEH2aFDhwYq0c/eaSNGe4J/8pOfDDxX+4MOVWhm0fb2yiuv9G3jZnGeIppM8VT3aBpGQyoV0Oe5q4CfW8syt+E+7KilvtOUnBdTBKoU0AEOV75ed3ptusdMpxOQodncM2KGlabt12HzuzbvhLZ3mKO3zY9g+FNrxSO5zbcu3hYoCFdyQbiOdGqjzawfhL/66qt5IK4vCaVhvVm3bl326KOP5jfLeeONN4YtkuS8pjulsXJtOOuss1w2uml4mnt0jaNBWfFU9/B1BU+6AuGvbHT559bm23DXd55Suq8CehajgAJEs/5Od+000us0xrbG3ia5mY0XmId9Mev7h/O7lG99gM7R2+ZfruFPrTXfmrhacPfdd2dKep3qdHQdLdMGuFkvCNfRcBeEHz16NA+yR/XAzLK1a9fm6Tvf+U6+rDZedPOvq666atRqzJ9OYMG1ZO8WevPNN102yunpp5/u28Vp7p4imkx4g7jwdRVNA2lI6QJdP819vg13vQeUSkenQATGFCie6k6QPibcF4u57Vy5fTHLT/TeHpb0meAXOp5RGccnnf3f+gC9syMXaceLH2qRNrO0Zin4Vrr44ovz68EVhJuZv7bmpz/9aab07rvv5qej6wZu+mAa1QCzwSD8l7/8pQ/EZfuHP/whU3r88cdHFcH8VgjU28ht27b5CjnN3VNEk9ERVDPz7en6homHIJOkgF7f8224J9lpOtU6geK2ml6zxSCydZ2qsMGyMbN8+7dop2o1T0n5YUkHqVSGe26+Zd0yKU9bH6CHp1frSGLKg0XfmhFwAbg7Cq7rk8x6H0IKvpV27dqVB+DFa8JHtVg3wVh7/Gj4eeedl4VHw4tB+C233DKqCOYjMFqg8AynuRdAInzIUfQIB6XiJoU/t9aV69DNLN/pHNJqQ1wpnEcegRgEiq9LgvS5o6Kg2mz0aewyVJq75tw5CtLNzD9h1s/7mR3JtD5AD8fp8OHD4UPyNQpoj3iN1ZVa1XwBuJnlR8AVgLuj4Aqix2nAsmXLMqWtW7dmSvqAcunIkSP5kfBXXnkl42j4OJrxLmPWji+Q8DT3Wd+vdY+G7m3x8MMP111trfUVj6KbteN1VStSYpV16efW3EZ8OIRmc4P18HnyCMQgoO22sB0E6T0N956WR29O/69Z771dtOsvMTqng2Dhs23bXgnbPku+9QE61z/PMvzlrau7h7vSzj33XJeNZnr77bdnSjpyrZuxmVl+Go7Z9AG4PjQUgOsmbcUAXB9KBw8ezJSef/75TCkaDBoys4DG3hWiMytcPubp7373O988vT79g/gyvkXaMXbBBRdkGzZsyK6//vo8+ScTzBR3/oVHWBPsbue71JXr0LUhX9yI10Z48fXe+RcEANEKFL8z9XrW6zraBlfcMPVdBsVq5KQ0y3tbR9FVvitb5bl8l6atD9DDwZrlBRGWQ77dAgrEdfq4gnGzXiB+7733Zkq6O6fe/OP0UEGYAnAlBeB33XVXfmqePiyU9OGkAFyXVhCAjyOazjIaf9ebMPB182KdmplvWuw3i3vssccy3dtBZ5m4Rl977bUuO8M07lXN+mP08ccfx91YWofAAgL6HtV3ZbiYPj/1XRzOI49A7AJ63YZt1OvarP95HT6Xcl7Bs/oe9lE2SuG8WfLaTjfr25r187OU26Z1kwrQ2wSfWlvDewHodNQ6+qdAXEmBuJKZ5UfFFYh/+OGH2UIbANpwUPCtNF8AriBcAfgdd9xRR7eoA4HKBK6++mpfdsw3i9MR8+9+97u+rdddd12+c2z79u1+XpMZMxtd/YzPFHc063NqxiJZPWKB8CyJ1K5DN+ud5hryl7kRH5ZLHoE6BIa9fs0s3/aso/6m6ygG52Zz3+NltVF1hWXp7Nfwcep5AvTUR7im/oWXGhQ3MMtoggLx+Y6KzxeMm1mmN/qFF16Yb+TrA1ZJewAVfCsRgJcxSpQRu0BsN4vTEXL99KC+eF0ysyzcyffnf/7n2UMPPRQV7dKlS3171Af/oKTMl770JV+SPqv8gyzLyKclkOJ16Pq+NbOBgTKrbkN+oCIeIFCxgD6TdYlGsRqz3rZmcX4qj/W+1naz64+ZZVVs77vyda8ms/7niI6qu+e6MCVA78Iot6yPN910U6YPPyUzy/dMjntUXB8gSmEwrg8QvbFfeumllknQ3BgFwiOaZhZjE+dtkwJht0ATp7kroHU/R6hrzPXTg3p/uuTapumtt96aPfXUU8pGlTZv3lxpe3TDuLCC8DUXzq8gT5E1C6R2Hbq+f8ONeHHqu1zfw8qTEEhBQAeFFKgX+6LXvln7tguK/Qgf6z1tNniXdrNqg3NXf/Fzo0vfhQTo7lXAtDEBF5CbWR6MP/jgg/leueIbM2ygWW9PZRiI68PSbeQTjIda5KsSaMsN4sL+X3nllf5hXae5F4PyhX6OUDde1Pv5vvvu822NKXPZZZf55uzZs4nstDwAABAASURBVMfny8ykeRS9TKE0y2rzae7aeFaAEo6M3scKZsJ55BFIRUCvb+2AKvbHzDK9H7KW/zMbDMzVHbN6gnPVpaRLWDVVkremXUhJBOgpvAlSeLFpL5vrh05Jd/lwqmBcSR9oZoMBebhcmFe5SmEwruBdwTiBeChFvg6B8AuiTTeIczZVn+au976SvlR1tN7MMh0pLwbluveDC8RlGibdeNG1N8ZpeD+KYr/Kai9H0aeQbOkq4T1ciuPeli6ZWX4JWdhevafDx+QRSFFAO6D0Wtd2bdg/zTPrbedqGzZ8Lva82mtmc5qpPmr7e84TFc7Qqe4VFh9t0UkE6NHqdqxh+jkk1+Wnn346zyoY18+u6U1tZpmOjivN9wbXDpeNGzfmX/b6gFMgrkQwnpPyB4GZBU4//XRfRng9tZ85ZkaB+KZNmzIF43rfmln+awm6JEUbLXrfhkWFQbnu/RB7IB62vZhXf4vzyn7MUfSyRWcrr6q1236au9nghrzZ3GC9KjvKRSAWAX3naVt3WHt0ZomZRX9U3QXmam8W/DPrvafVx2B2bVmz/mdMHd+9tXVsnoqSCNDDDUwFhPP0l6cqErjmmmuyN954w5e+d+9ef7r666+/np+y7p8MMnqjKSkQd0kfDK+99lqwFFkEEChTIDzyP+7eaQXjw27UqPe6vrT1/h3WRh1Fd0fK2x6Uh/1Tv9xjncLv8mVOdTTVrL9hYtbPl1kPZUUh0LpGuI35sOFmNvL7PlyOPAIpCrjvQgXqZnM/r/U9aWb59rHePzEZmM09nV3tU5vnO6imZapOOgjg6lB7XD7laRIBengd6M6dO1Mer1r7dvnll+cfIma9DxOz0dN//dd/zQ4cOLBg+xSMh0fHFYwrLbgiCyAQgYBev64ZZuayrZya9duvs1xcJ7STU0kbGEpmln8O6Kj4OD9fqI2O8HKUw4cPZ20+Uu5citOqbxTn6gt/hkvztJNEU1JaAuE4V3Mderleep8Xv7u14dz0hny5vaQ0BKYTUKCu94LeE/oeHVaK3j9mve9XbVso6X01bNkq56leM5tThdqt9s95ooEZ2o5ooNpGq0wiQG9UMOHKn3zyyZl7pzd+MSDn6PjMrBQQgUC4YzCC5kzchBtvvNGvo7NczCwPxHUJipI2LpT8QoWM2egbNXbhcpQ6bhQn8g8++CAfF+WVtJNEU1JaAm36uTV9ryu4CEfgj3/8Y/iQPAIIfCGgYF2BrgJeM/ti7uBEzyvpfWVm+We+WX+q99zgGrM9UnlmvfJVb1iameWXmKrd4Xzy9QokEaCHN1hJ8UhNvS+Jfm0nn3xy/8GInFnvDa7TPXVdqz6A3KJ60+vDhoDciTBtu4Be064P4Wnibl7MUx0V15FyvUfNeveDGLe92quvFB4ZV/Cua8y7EIwPc6rjRnGuXlm7vKbauNKUlI5AW65D12sv/BzUCBQfa17ZifIQaLuAAl59luv9ou/hSfqjdcx629v6Lp5kXS2rdcx665v1AnDNLybVozYW5/O4foEkAvSQrYunQYT9LzP/3nvv5XvR9IYdlfRGVpK7AhYz803QNav+ARkEEKhNQMH4ihUrMm0EmFm+N15HxXWkXO/X+RqiDfDwrBe99xWIK3U1GB/lJatRz5U9PzztWWNSdvmUh8BCAmaDG/Zmg48XWj/i52kaArUKKFjX57iSvqeVzHrf1WY2b1t04MvM8u91s/GmWmdUoWa997HaMmqZGOabmW9Gnd+9vtKaM0kE6A888EDNbFQ3SmDYndxHLct8BBCYXUA3aNSNMvWFZWb5l7aCcd2QbaFgvFi7vqD1Rc5ZL0WZ4Y915pB7pqobxbnyi6e6m5l7immCAroHTEzdMht8vZkZN4Mbe4BYEIHRAgrWlfR97ZK+i11ywfvoEiZ/xsz8ATjVOXkJ9a/RtRvFJRGghy+TtrzQwjanlP/GN77hu/Pyyy/7PBkE2i6gANj1wWxwY9XNr2uqoFztMbNMN2jUndj1ZT6qfi2rVDwyPt86o8pifl+grhvFuRrDG4lpXp03jDNr9jWv/qaewsvKXnzxxSi6606NDRujgIFtrVCk4TzVJy3ggnd9X+u9N01nzfoBucpp4/tXZ+pO0/e2rpNcgN7WgUil3ffcc4/vij5U/AMyCCQkUPcN4hSQn3HGGflvqJr1gnJ9yQ4jVSCuFAbjOiquNOzIuJZz5ei0eJdnurBAXTeKcy0pHkWv84Zx024YurYzXVjgoosu8gvt37/f55vKKDjX50ZYvz53+G4PRdLP08N4BPTe03tw0tTGgDwe9WZaQoDejDu1IoBAywT0heiarPstuHxVUwXlCrTNegH522+/nZ+SVqzPzDLdoFHtU9IGtdKwYLy4rh6HN9nUafGaRxpPoM4bxbkWFTe09BpxzzFtt8ATTzwRVQf0ORI2SJ8v4WPyCJQgQBEIIDBEgAB9CAqzZhPQXvfZSmBtBLopUAzKR20Qm1n2ve99Lw/YFbDNssNA9/AIgzzd6b2b+tP1OrSbroTJ12rihnErV670DeUGoJ4i2YzZ4CUNoz6LkgWgY4kI0A0E2imQTIAeBoVsPDT7YgxvFLdp06ZmG0PtCEQsEAbkZr0j5cM2hM0Gj5IrKH/kkUdK69kNN9zgy9q3b5/Pk1lYoM4bxbnWFE91NxsMptxyZU7PPvtsX9zTTz/t82TSEzAbfD0N+0xKr9f0CIEpBFgFgYoEkgnQw6CQjYeKXi0Ui0BHBcKjpGaDG6+TkoRBuW7wNmrj16y8o+QLtVFH0d0yCv5dnunCAnXfKM61qMkbxrk2MK1WoIk7uYefderdqM8nPUdCAIFqBSi9uwLJBOjcPTyeFzFjEc9Y0JLyBSa9QZwLyLXhazb/UXIdjdUGsZIC5TKPki8ksXz5cr8Ip7l7igUzdd8ozjWoeBS96hvGhZ/rrg1Myxdo6k7u+uwx693p2fXKbLadka4cpgggEKUAjYpYIJkAPbx7eMTenWhaOBa642QnOk0nkxZQwOw6OM713i4oN+sH5GEZriyz/qnrCsib/BmR73//+65Z2euvv+7zZOYXaOJGca5Fes24vKbaCaRp1emtt96quorOlt/EndyXLl2aHT16dMDczLLi62tgAR4ggAAC8wrw5CwCyQToIQJBYahBHgEEqhbQqahmlpn10qhT1816zytYV9IG8DgBf9XtV/nhae56TBpfoK7AeFiL6rphXLjj9ZNPPhnWFOaVIFD3ndx1z4kjR44MtNx9Ng3M5AECCCAQk0DibUkqQDezxIerPd0Lb9rXnlbTUgTmFzCzrBiMm1n25JNPjlzRbPAouYLykQs3/MTGjRt9C1asWOHzZOYX0OnBbomLL77YZWuZFk91b3JnQS0dppJSBTZs2DBQnoLzgRk8QAABBDoo0HSXkwrQFy1a5D25k7unaCQT3rSPO7k3MgRUWoKAC8ZdUdp4nS8Yd8uZWf4TaFpeAXksR8ld+0ZNL730Uv/UwYMHfZ7M/AJN3SjOtWrt2rUum7/u/IOKMseOHauoZIptUsCMgxxN+lM3Agh0RmDBjiYVoIdBIXdyX3DsK10gvKEQ1ytWSk3hJQns2LEj004+M/Onqo8TjKt6nTGiYNwlBeWa37ak09zDI7DcLG68EWzqRnGude+//37+mnWPzcxlK5nqdV5JwRQ6R0A7CefMnHHGqlWr8teLWf91YmZccz6jK6sjgAACZQnMFqCX1YqSygmDwpdffrmkUilmVgGuV5xVkPWrElBQbmb5xurOnTvH2kAtBuMKVoo3WKqqvXWUe8MNN/hqdH2qf0BmpECTN4pzjSruFAp3tLhlmLZDoMo7uevn+YZ9JxdfP+2QopUIINBVgfCXZ1I0iDpAnxQ8vInNpOuyfLkC4Vhw075ybSltNoGtW7fmAbmZZQrK5ytNwXj4fGrBeNg3l9dRdJdno91JLDyNISD+8pe/7Buq16p/UFLGzEoqiWLmE6jqTu66FOLjjz+eU7UZ4zoHhRkIIBCdgFl3PquSCtDDV9IYQWG4OHkEEEhcwAXmL7zwwsie6uiSAhuXwiPjZt35Ygj3THOa+8iXy8ATTd4ozjVEp7q7vKYx7DRQO0iTCZR9J/eTTjop3yn54Ycf+oaYWX6/An3WsSPOs5BBAIGWCDT5s7R1ECUXoJvFshFdx/DFXUfx6GPcraV1KQps2bIl3zA1s2xUYK4NVJfCDVh5hAHOaaedplmdSN///vd9P19//XWfJzNaoOkbxbmWVXkUXfdocPUwbYeAmWV/+MMfBhprZmNdzjOwEg8QQACBhgWWLl3acAvqqz65AD3cgEj6Tu71vUamrim8aR93cp+akRXHENDry8x8MG7Wy+/Zs2fo2i4g13ToAl/MDJ9vy53Yv2j6TJPwNPeZCurQyk3fKM5R6yi6mbmHWbiTyc8sIcP3awmIFRahcTfrvw5cVdqBwxFzp8EUAQQQiFMguQA9DAq5k/v0L7oy1uSmfWUolluGNtrKLTGO0vbu3TtWQxRwK421cMcX2rhxoxdYsWKFz5MZLhDDjeJcy3StscuX+XpfuXKlK5ZpjQKT3Mndnc5eHHczy09p1w6cGptOVQgggEBpAuHPvxY/40qrJJKCkgvQCQojeWUdb0Z4o7jjD8P/5GsUMOsfRUn1A23x4sVDRc16G6Xqt9LQhZg5VODSSy/188MvRT+TzBwBs/57bc6TNc5QEGbWb4tZPz9LM84++2y/OjvAPUUlmUnv5O4C8+Lp7GqcPvs4ai4JEgIIINAOgeQCdILCOF949d60L06DplrVhQ2zI0eO5EeHtCEapln6Hp5tYFZOgNPUa2CaenWae3jEVJcRTFNOl9ZZs2ZNNN0tvvbD13M0jaQhIwUmuZO72dzrzFWw+yxUnoQAAggg0B6B5AL0kJ6gMNRoJm+WYGDTDCW11iygjVtXZTHYcfNTn/7whz/0XRz3MgK/Qgczp556qu/1/fff7/NNZXS9sas7fD27eZNOwzPU3nrrrUlXZ/kJBMa5k7t2upjN/Y7VuJcx3hM0l0URQAABBEoUSDJAD3/upkQrippCgJv2TY5WxRrakHPlhmPi5jFFYJiAzkgKjwpzFH2YUn9ebPdA0anu/dZlpd4w7pNPPgmLJl+jgDudvRiEm/Uu6SmOe41NoyoEEEAAgRIEkgzQv/a1r3maiy++2OfJ1C8Q2wZr/QJx1HjmmWe6hvDzOl5idCbcoWFmoxfswDM33nij7yVH0T3F0Ex4hPnll18eukzdM3U01dVZDOjc/HGn2mEz7rIsV67A//t//8//SgXXmZdrS2kIIIBAbAJJBuix/NxNbIPdRHti3GBtwqHpOt98882ampBGNWEg09XT291IKijjKLrTGH/6+9//fvyFK1xSR1PN+juZzPr5Wao9duzYLKuz7oQC77333tA19FmlNPRJZiKAAAIItFIgyQA9pp+7aeWrosRGa+PeFcc9AZwE06kFWLERAY5p0pbTAAAQAElEQVSij8d+yy23+AVjOgW8uJPJbPYgnaDQD3XpGXe0fL6C5a803zI8hwACCCDQToEkA3QNxbJlyzTJE6e55wz8QQCBBQT0NKe3S2EwaUcbR9EHTUY9MusFv7HtkDzllFMGmqwgcGAGDxoV0HiYWX4a+6ij5RpDBeVKjTaWyhFAAAEEKhVINkDfvHlzpXAUPr7AiSeeOP7CLIlAwwLhxm/xyOOMTWv16hxFn2z4wtfRZGtWs/T//d//5cGfK31UEOieHzUNd2CNWob54wmME5S7kvR60hi6x0wRQAABBNIVSDZAf/755/2o7dq1y+fJ1C/AbynXbz6sRjPzs88991yfJ5OKQLX94Cj6eL7hmQbjrVHfUsUdTmb9z4RxW7F+/Xq/KJ8jnmKijAvM59tJooDcbPLxmaghLIwAAgggEKVAsgF6lNodbdTZZ5/d0Z7H1e3wyBc3jRs+NqGRGRvHA0rHH3AU/TjCAv9PPfVUv8T999/v87FkdJp02JbwNR/OH5W/9NJL/VP79u3zeTILC5hZfhbDqMBcY6PAXEmlLV68WJM8XXPNNfmUPwgggAAC6QskHaCvW7fOjyDXoXuK2jPcyb128qEVnnPOOUPnM7Mv4DaMNad4tFHzup6qPIqeiu0FF1zgu/L000/7fCwZnSatQNC1R695HdF1jxeaPvDAA34R3iOeYmRG2yFmvcB82EJmlmkMlDQ2w5ZhHgIIIIBAtwSSDtBvvfVWP5p79uzxeTL1Cmij3tUY242TXLu6NuUnkro24uX1t6VH0csDWKCkNuyQVCBoZr4no47o+gUKmUmPuhdWT/7hGWeckR8pN7Ns1M/tKSBXmm8nR7gj5bnnnkvejQ4igAACCPQEkg7Q+bm13iDzFwEJvPbaa5qQRgiEQYdZP3gZsXhnZ2uH20knneT7f9ppp/l8dzPDez4qOBu+dL1zi4Gh2fivea5D749VGIybWR6Yv/322/0FgpxZ/2h5MJssAggggAACAwJJB+jqqZlpkn9p5hn+NCLAndwbYafSCQR0NMstXgxe3HymPYG//uu/7mWO/3333XeP/+W/E6jkt9Bd4SVPwyO0KjrcSaXHo1KXr0PXdfdmlm9TmFk2KhgP7XRfAn2+8LkSqpBHAAEEJhcwM7/SuN9ZfoUWZZIP0Fs0Fkk3Nbwuc9OmTUn3lc4hkLqAzk7aunWr7+aiRYt8nkyWB2/Z8X+xX9KjU90VpB9vav5fQaSumc4fzPOny9ehb9iwYR6Z3lNm/SPlMp12J9a2bdt6BR7/O+llCMdX4T8CCCCQnMDy5ct9n/T56h8kliFAT2xAY+1OeF1mrG2kXd0VCPfCmvX3znZXZOGehz9lqSOD3IhzrlkbNh4UpJv1X/M6LX+cID18z8zt+cCcpB7oaPh8HTKzTO+H+ZbhOQQQQACB6QQ++eST6VZs2VoE6C0bsBSa+/LLL6fQDfqQkEAYSLFxPf7A3nXXXX7hXbt2+XzXMzH/FvqwsSm+5hWkD1sunBfPdehhq6rP62j42WefPbIifZaYWX4WRZk7MY4ePTqyTp5AAIHyBHQZy8MPP1xegZSEwBQCBOhToLHK5AK6sZRbK/bTPl07U5yG9wIIf2M3xb7Sp+oFdKp7GIRwFL1nHh5ljfG30HutHPyrwDKcY2bhwzn5zlyHPqfnWfbmm2/mP422cePGTMlsuJVMzWzqYP2RRx4ZUjuzEECgSoG/+Zu/ya6//vrskksuqbIaykZgXgEC9Hl5eBIBBFIXCANMM0u9u6X378477/RlchS9RxHecyPG30LvtXLu33DHgp41G/1+6PJ16LJR0i9jKOkMBAXj5513Xh6M67li0vNmlj9vZpk+d5SUlixZkinp/ixKGf8QQKAxAfc99pvf/KaxNlDx/AJm5hfQZ6h/kFAm+QDdDRw3MWr+VRsevW2+Nd1swbnnnus7zpkMPQptOPdyGdeOOogJpjqKvmrVKr/GFVdc4fNdzYT33GjTJT06fXuSIN19v3Z1nIv9fuWVV/LPEH2mzBesaz0t45JOX1fau3dvpmRmPpDXskpa1qw/36yX1xi4tGzZsixMW7ZsybZs2aLVSQggMKbAp59+OuaSLNaUQBduFJd8gO4GUafz6rqSpl5M1Jtl4VEljhI084rQBmQzNVNrygI7duzw3fv3f/93nyeTZeNczx2T0yRBelevQx9nvPRZO86R9XHKmm8ZBe4uHT58OAvTnj17MiWzXjBvVvaU8syqNVixYkWmHes33XTTfC8DnitR4NChQ3lpBw4cyKf8iU+gCzeKSz5Av/XWW/NX1sGDB7PwVMx8Jn9qFQiPKr311lu11k1lCAwT0JEnN9/MXJbphAKPP/54fsRPq33wwQeadDrdcsstvv9t3JAYN0jv8nXofoDHyITBugumL7zwwswlHUBQMjP/Phqj2PQXoYeZtl1ff/317MEHH8xfG2a91wiBe3UvDu1YU+mcZSgFUlMCyQfov/jFL7KHHnoo2759e3bttdc25Uy9xwXCG8W1caP1eBf4n5iANpZdl9yXsnvMdDIBBRtujdWrV7tsZ6dmlve9rRt54wTpXIeeD/FUf1566aXMpSNHjmRK+gxS0ueSS2Hhbl443bx5c+bS0qVLM5fMbCCgC8sh3xNo899RgfvatWuztceTzlK8/fbb29zFxtp+5plnNlY3FSPgBJIP0NXR6667Lnv22WfzIF2PSc0LtHWjtXk5WoBAnAL//d//nX3pS1/KG/fxxx9nGzZsyPNd/ePOzlAw1VaDYUG665frU/Gxm8+0HAEzm7eg3bt3Zy7p1FyXFOiHSa9D0h/zu+/X5FBKXT/4wQ/yXwpwl2vO+2I4/uSHH36YKeleBvfee+/AThq9V0877bRMiV/cOI41xv/HHntsjKVYBIHyBToRoJfPRokItFcgvFmfTqtsb09ma7k2VlwJZvNvBLvlmM4v8M///M9+Ad3z48c//rF/3LXMWWedlUSXFaSb9d8fCm7C9w7XoVc7zOGOLl2LXG1tlB6bgM5S0S8F6MZleu+59IMfTBa4q19aV+9nJd2p3Mx8AK/tgvPPPz/TTT7vv//+rMv/vvWtb/nuc08VT0GmZgEC9JrBu16d7jDbdQP6H4eANlZcS3SkyeWZTi9w1VVXZY8++qgvQNdN+gcdy4QbeW3fUaH3h5n5EQzfO1yH7lkqyeBbCWvrCx0VuN92222Z0po1azIlM8uD8IU6fOzYsezVV1/NfvWrX2U/+tGPMju+nnbEaZtNlywpnXzyyZkC+4XK4nkEEJhdgAB9dkNKQKBVAuFRGC41aNXQtaKxCtLdz67pt51b0WgauaCAgvRwITPLHypQyDPH/xSXOT6L/zMK4DsjYMdW171+lHSzTiW9J5W0U03prrvuyrZu3ZqtW7cu/0k+BeGjiLS8fhVAlywpvf/++9m2bdtKCdJH1RnD/O3bt/tm6FIB/4AMAjUKEKDXiE1VWX4zG+fANVBOot6p7ihcb43x1RZulJj1Ao34WtneFul0SbXeTZXvWgo38p566qkkun/66acP9MO9j9x04EkeIIBAdAJ33HFH9vzzz2fvvPNOfod4HTlXIK70y1/+MvvOd76TnXfeeZkufzOb+92o5b75zW/G/qtIM7lrJ7MrgF8cchJM6xYgQK9bnPoQQKBxAW1kuEbo6ILLM0WgLIFwI2///v1lFdtoOb/73e+yMEjX++icc87J1q9f79sVnqHjZ5KZSSDcAYLvTJSsPI+Afh5SP5mpnfjuVwX0HndJwbtW1++D//znP+/EjUCHf3ZLgYRAtQIE6NX6UnpB4LLLLvNzfvvb3/o8GQQQQCA1AXcGgX4SKZW+KUg36x9Z0xGm//3f//Xd27dvn8+TKUcg3AFSTomUgsDkAgre33jjDb9DTu/166+/fvKCWrCGWe8zTjsjam8uFSJwXIAA/TgC/5sR0PVRzdRMrQggUIcA9zjoKafmUDzrJNyILT7XE+DvLALhjeJef/31WYpiXQRmEtDOIv1ssY6g6zKea6+9dqbyYl3Z7VzV2QOxtnHadrFeOwQI0NsxTsm0Utc/JdMZOoIAAkMFbr755ny+Arcu/0zNmWeemTuk+IcN1/pGNbxRXH21UhMCwwUUpP/sZz/LFKgrSB++VLvnrly50neg6z875yHGy7BUSQIE6CVBUszkAocOHZp8JdYoRcCsd/qWCtu0aZMmJARKE/jhD3/oy/qXf/kXn+9aJqWfWhs2dmb9z5FhzzOvPIHwOvTySqUkBBAYJhDueHj66aeHLcK8RgS6UykBenfGmp4i4AXc6VuaoRvCaEpCoCwBHWFxR1dSPQWyLKs2lzPqdPZrrrmmzd2Ksu16T7mGcaM4J8EUgWoEvvGNb/iCu3wWmEeIOGNW4o7iiPpJgB7RYHSlKRwJ6MpI088uC+gIhAvSu+ogA9f3VH5qzfXHTS+88EKX9dPnnnvO58mUIxBeh66bc5VTKqUggMAwAd3Rfth85sUhEMYRq1evjqNRY7RikkUI0CfRYtlSBJYsWVJKORQyvcD555/vV07tBla+Y2QQaFggxZ9aK5K+9NJLWTFIf++994qL8XhGgfA69FFnLsxYBasjgMAQAW5oPASl4VnhPVBSHZ8pAvSGR4XqWy+wdu1a34e7777b58k0IxB+0DXTAmpFIF0BdzlJSj+1VhwtBelm/dMMjxw5UlyExwgggECrBJYtW+bbe/HFF/s8GQTqEIgvQK+j19TRqMBXvvIVX/+vf/1rnydTn4A2qOurjZoQQCD1M1WKR3XN+gE7o1+OgNvZo9K4uacUSAhUJ7B582Zf+J49e3yeTLMC4entzbak2to7F6BXy0npCCCAAAII9AVS/qm1fi+H58IjUMOXYO4kAhdccMEki7MsAgjMIPD888/7tfnVIU/ReCY86/PLX/5y4+2pqgEE6OXKUtoYApdddplfir2SnoIMAggkKJD6T62FQxYe4dX8w4cPa0IqSSC8s/TLL79cUqkUgwACowTCnYyc5j5Kqbn577//fnOVV1wzAXrFwOUWn0Zpd9xxRxodoRcIIIAAAl7g6NGjPu8yK1eudFmmMwrcc889voTUL5nwHSWDQIMCnObeIP6Qqs36l06Z9fNDFm39LAL01g9hiR1ooChuJtQA+hdVLl269ItcloV7if1MMgggMLNAF35qLUQKTz/U/E8//TTbsWOHsqQSBPgVlBIQKQKBMQU4zX1MqAYWK973pIEmVFolAXqlvBQeCgzLp/4GG9bnWOaFP43EqaixjArtSE2gCz+1VhyzYpC+c+fO4iI8nlLga1/7ml+TU249BRkEKhNYs2aNL7srNyjzHY4oE9qbpX30XOwE6FIgpSBAHyYU2L1794RrsDgCCEwj4K7N7tKOsDBIX7169TRsrDNEILyHy4svvjhkCWYhgECZAjfeeKMvTp9r/IKC56g1I3tXYRcO7hGgu9FmWqtA+06prpWHHn90NwAAEABJREFUyhBAICEB93nXtdOTtUGl9OGHHyY0ms12JbyHSxc2UpvVpnYEskz3frjttts8xd69e7ObbrrJPyZTvYBZ+kfMi4oE6EURHiPQhAB1IoBAsgLuCLqbJttROlaLwLp163w9nObuKcggUJmAgvTly5f78h988EGfJ1OtQHhqu2rSTl9NU08E6KmPcKT9C++MyQZG9YNEDQgggAACaQjceuutviP8VKmnIINApQK64aVZ/0iumWVmgym8Xr3SxnSkcO2MDANyM+tIz7OMAL0zQ01HEahMgIIRQAABBGoSCE9zP3ToUE21Ug0CCFx99dXzInz00Ud50L5o0aJ5l+PJ8QR+//vfDyzYpct6CNAHhp4HdQmEN7r57W9/W1e11FMQCE+5PeOMMwrPxvKQdiCAAAIIhAJnnXWWf3j++ef7PBkEEKhO4JFHHslOPvnkBStQIGlmebC+4MIsMFTAzAbmh0fSB55I9AEBeqID26ZuffDBB21qblJtDX8C6u23306qb2N3hgURQACBlgn87d/+rd/4f/XVV1vWepqLQHsF3nvvvUzBYjFdeumlQztlZvl7dceOHUOfZ+Zcga5edx5KEKCHGuRrEwhP0autUiqaI6C9wXNmMqNUAQpDAAEEyha45ZZbsr/4i7/wxXbtFwJ8x8kgEInAM888kwfuCtSLAaaauHPnzjxQ5zp1aYxOstPOD7fEqaee6rKdmhKgd2q44+ws19DFOS60qhUCNBIBBDoq8Pjjj+cb/Or+0aNHsyuuuEJZEgIINCigQP3YsWN5sK5gs9gUd536ypUri091+rEuszSz3M1BmFn27rvvuoedmhKgd2q46SwC8wvcfffd8y/Asx0ToLsIIBCzwD/+4z/65v3bv/2bz5NBAIHmBVygrqPqxda4u8IPC+KLy6b82AXmxcsszSzTtfwp932+vhGgz6fDc5UKdP1DqVLcCQo/6aST/NI//elPM4J0z0GmagHKRwCBmQR0qvvixYvzMnRaKEfRcwr+IBCVgI6q6/05LFDXfLPedepRNbrixuhO92aWFQNzVSuTLgfnMiBAlwKpEQGumWuEfU6l+/fvz5YtW+bnK0j3D8gg0GIBmo5AFwT+4R/+wXeTo+ieggwC0Qm4QP3rX//60LaZ9QL1rVu3Dn2+7TNdUG42/Oi4AnOltvezjPYToJehSBlTCaxdu9avx1FbT9FI5uDBg75eM/N5MgggMFJg4ifCHWETr8wKCIwQ0FF0bfjqac5MkwIJgbgFdu3alV9rrUDdbO421wsvvJDfXyKF69T12WRmeX9GHRVXUK4U96jV2zoC9Hq9qS0Q+MpXvuIf/frXv/Z5Ms0ImFkzFVMrAokLrF+/Pu/hunXr8ul4f1gKgfEFzHqf32a96fhrsiQCCDQloEBdQauCU7O57113nbqZZdr5pp28Slu2bMli/jdOUG5m+U4K9T3mvjTVNgL0puSpFwEEEECgEwJr167N++mm+YOm/1B/kgLaiE+yY3QKgcQFXKCuo+rDuqpA9vDhw5nSnj178iPSZhblVH0Z1gezflA+aplh63VxHgF6F0c9kj5fdtllviX6sPEPyCCAAAIItFqAxtcr8NWvfjWv8MiRI9nf//3f53n+IIBA+wR0VF3B+KhAvW09MiMon2bMCNCnUWOdUgTuuOOOUsqhEAQQQKANAu5U9za0NfI20ryCwLXXXuvncKM4T0EGgdYKuEBdwfrmzZszpaVLl/oj5jF3zIygfNbxIUCfVZD1SxHQXv9SCqKQqQXcqZFuOnVBLVjRzPJWmvWm+QP+IFCRwM9+9rNs+/btWRhEVVQVxZYi0L5CfvKTn2TuEgqdkbZv3772dYIWI4DAUIHdu3dnSocOHcp0ariSAvdYk9o3tCPMHFuAAH1sKhasUoA3c5W645Vt1gtWzXrT8dZq51J6vemLTdN29oBWt0lAwfmzzz6bB+ltajdtrUigomJvvvnmvOQDBw5kl1xySUaQnnPwBwEEEGidAAF664YsrQab9YJBs940rd7RGwQQQAABBOoR+MUvfpH9/Oc/z1SbgvM777xTWRICCCCAQMsECNBbNmA0FwEEEEAAAQQQGCagyykeeuih/GyNii6pGFYt8xBAAAEEShQgQC8Rk6IQSEGgC9egpzBO9AEBBBAYJnDddddl7b2kYliPmIcAAgh0S4AAvVvjTW8RGCmwbNmy/LklS5bkU/4ggAACCCCQlACdQQABBFogQIDegkGiiQjUIXDiiSfm1axatSqf8gcBBBBAAAEExhdgSQQQQKAMAQL0MhQpA4EEBNavX5/3Yt26dfmUPwgggAACCCAQjQANQQCBjggQoHdkoOkmAgsJrF27Nl/kT/7kT/IpfxBAAAEEEECgKwL0EwEEYhEgQI9lJGgHAg0L6O6/27dvz7jzb8MDQfUIIIAAAgikJkB/EEBgbAEC9LGpWBCBtAUUnHPn37THmN4hgAACCCCQogB9QiAlAQL0lEaTviCAAAIIIIAAAggggECZApSFQK0CBOi1clMZAggggAACCCCAAAIIIOAEmCIwKECAPujBIwQQQAABBBBAAAEEEEAgDQF60ToBAvTWDRkNRgABBBBAAAEEEEAAAQSaF6AF5QsQoJdvSokIIIAAAggggAACCCCAAAKzCXRybQL0Tg47nUYAAQQQQAABBBBAAAEEuiwQZ98J0OMcF1qFAAIIIIAAAggggAACCCDQVoEp202APiUcqyGAAAIIIIAAAggggAACCCBQpsC4AXqZdVIWAggggAACCCCAAAIIIIAAAggUBCIJ0Aut4mFnBE44ofcSdNPOdJyOIoAAAggggAACCCCAAAIFgV50VJiZ3EM6FK2AmeVtM+tN8wf8QQABBBBAAAEEEEAAAQQ6KECAXsKgUwQCCCCAAAIIIIAAAggggAACswoQoM8qWP36nahh+fLlnegnnUQAAQQQQAABBBBAAAEERgkQoI+S6cz8Zjt68803ZyeeeGL2V3/1V802hNoRQAABBBBAAAEEEEAAgYYFCNAbHoDkq1+gg/fdd1929OjRTNMFFuVpBBBAAAEEEEAAAQQQQCBpAQL0pIc3/c7RQwQQQAABBBBAAAEEEEAgFQEC9FRGkn5UIUCZCCCAAAIIIIAAAggggEBtAgTotVFTEQJFAR4jgAACCCCAAAIIIIAAAn0BAvS+BTkE0hKgNwgggAACCCCAAAIIINAqAQL0Vg0XjUUgHgFaggACCCCAAAIIIIAAAuUKEKCX60lpCCBQjgClIIAAAggggAACCCDQOQEC9M4NOR1GAIEswwABBBBAAAEEEEAAgfgECNDjGxNahAACbReg/QgggAACCCCAAAIITCFAgD4FGqsggAACTQpQNwIIIIAAAggggECaAgToaY4rvUIAAQSmFWA9BBBAAAEEEEAAgYYECNAbgqdaBBBAoJsC9BoBBBBAAAEEEEBglAAB+igZ5iOAAAIItE+AFiOAAAIIIIAAAi0WIEBv8eDRdAQQQACBegWoDQEEEEAAAQQQqFKAAL1KXcpGAAEEEEBgfAGWRAABBBBAAIGOCxCgd/wFQPcRQAABBLoiQD8RQAABBBBAIHYBAvTYR4j2IYAAAggg0AYB2ogAAggggAACMwsQoM9MSAEIIIAAAgggULUA5SOAAAIIINAFAQL0LowyfUQAAQQQQACB+QR4DgEEEEAAgSgECNCjGAYagQACCCCAAALpCtAzBBBAAAEExhMgQB/PiaUQQAABBBBAAIE4BWgVAggggEAyAgToyQwlHUEAAQQQQAABBMoXoEQEEEAAgfoECNDrs6YmBBBAAAEEEEAAgUEBHiGAAAIIBAIE6AEGWQQQQAABBBBAAIGUBOgLAggg0C4BAvR2jRetRQABBBBAAAEEEIhFgHYggAACJQsQoJcMSnEIIIAAAggggAACCJQhQBkIINA9AQL07o05PUYAAQQQQAABBBBAAAEEEIhQgAA9wkGhSQgggAACCCCAAAIItFuA1iOAwDQCBOjTqLEOAggggAACCCCAAAIINCdAzQgkKkCAnujA0i0EEEAAAQQQQAABBBCYToC1EGhKgAC9KXnqRQABBBBAAAEEEEAAgS4K0GcERgoQoI+k4QkEEEAAAQQQQAABBBBAoG0CtLfNAgTobR492o4AAggggAACCCCAAAII1ClAXZUKEKBXykvhCCCAAAIIIIAAAggggAAC4wp0fTkC9K6/Aug/AggggAACCCCAAAIIINANgeh7SYAe/RDRQAQQQAABBBBAAAEEEEAAgfgFZm8hAfrshpSAAAIIIIAAAggggAACCCCAwMwC8wboM5dOAQgggAACCCCAAAIIIIAAAgggMJZAkwH6WA1kIQQQQAABBBBAAAEEEEAAAQS6IJBwgN6F4aOPCCCAAAIIIIAAAggggAACqQgQoE87kqyHAAIIIIAAAggggAACCCCAQIkCBOglYpZZFGUhgAACCCCAAAIIIIAAAgh0S4AAvVvj7XrLFAEEEEAAAQQQQAABBBBAIDIBAvTIBiSN5tALBBBAAAEEEEAAAQQQQACBSQUI0CcVY/nmBWgBAggggAACCCCAAAIIIJCgAAF6goNKl2YTYG0EEEAAAQQQQAABBBBAoAkBAvQm1KmzywL0HQEEEEAAAQQQQAABBBAYKkCAPpSFmQi0VYB2I4AAAggggAACCCCAQFsFCNDbOnK0G4EmBKgTAQQQQAABBBBAAAEEKhMgQK+MloIRQGBSAZZHAAEEEEAAAQQQQKDLAgToXR59+o5AtwToLQIIIIAAAggggAACUQsQoEc9PDQOAQTaI0BLEUAAAQQQQAABBBCYTYAAfTY/1kYAAQTqEaAWBBBAAAEEEEAAgeQFCNCTH2I6iAACCCwswBIIIIAAAggggAACzQsQoDc/BrQAAQQQSF2A/iGAAAIIIIAAAgiMIUCAPgYSiyCAAAIIxCxA2xBAAAEEEEAAgTQECNDTGEd6gQACCCBQlQDlIoAAAggggAACNQkQoNcETTUIIIAAAggME2AeAggggAACCCDgBAjQnQRTBBBAAAEE0hOgRwgggAACCCDQIgEC9BYNFk1FAAEEEEAgLgFagwACCCCAAAJlChCgl6lJWQgggAACCCBQngAlIYAAAggg0DEBAvSODTjdRQABBBBAAIGeAH8RQAABBBCITYAAPbYRoT0IIIAAAgggkIIAfUAAAQQQQGBiAQL0iclYAQEEEEAAAQQQaFqA+hFAAAEEUhQgQE9xVOkTAggggAACCCAwiwDrIoAAAgg0IkCA3gg7lSKAAAIIIIAAAt0VoOcIIIAAAsMFCNCHuzAXAQQQQAABBBBAoJ0CtBoBBBBorQABemuHjoYjgAACCCCAAAII1C9AjQgggEB1AgTo1dlSMgIIIIAAAggggAACkwmwNAIIdFqAAL3Tw0/nEUAAAQQQQAABBLokQF8RQCBuAQL0uMeH1iGAAAIIIIAAAggg0KcRjesAAADDSURBVBYB2okAAjMKEKDPCMjqCCCAAAIIIIAAAgggUIcAdSCQvgABevpjTA8RQAABBBBAAAEEEEBgIQGeRyACAQL0CAaBJiCAAAIIIIAAAggggEDaAvQOgXEECNDHUWIZBBBAAAEEEEAAAQQQQCBeAVqWiAABeiIDSTcQQAABBBBAAAEEEEAAgWoEKLUuAQL0uqSpBwEEEEAAAQQQQAABBBBAYK4Ac7wAAbqnIIMAAggggAACCCCAAAIIIJCaQJv68/8BAAD//46pVWcAAAAGSURBVAMAHwwWEcEqQOYAAAAASUVORK5CYII=', '2026-01-13 00:02:29', 'tests', NULL, '2026-01-13 00:01:48');

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
(1, 1, 1, 'Laptop', 'unit', 6, 1, 'tests', '2026-01-12 15:26:04', '2026-01-12 15:26:04', NULL),
(2, 1, 2, 'Keyboard', 'unit', 3, 1, 'tests', '2026-01-12 15:26:04', '2026-01-12 15:26:04', NULL),
(3, 1, 3, 'Mouse', 'unit', 4, 1, 'tests', '2026-01-12 15:26:04', '2026-01-12 15:26:04', NULL),
(4, 2, 4, 'Printer 1', 'unit', 8, 1, 'tests', '2026-01-12 16:01:48', '2026-01-12 16:01:48', NULL);

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

--
-- Dumping data for table `propertyreceived`
--

INSERT INTO `propertyreceived` (`ReceivedID`, `IARID`, `ReceivedBy`, `ReleasedBy`, `DateReceived`, `TotalCost`, `Remarks`, `CreatedDate`) VALUES
(1, 1, 1, 1, '2026-01-11', 67000.00, 'Auto-created on acceptance', '2026-01-11 23:29:25'),
(2, 2, 1, 1, '2026-01-11', 113000.00, 'Auto-created on acceptance', '2026-01-11 23:30:29');

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
(1, 'Available', 'In stock, ready for issuance', NULL),
(2, 'Assigned', 'Currently assigned to an end-user', NULL),
(3, 'For Repair', 'Under maintenance/repair', NULL),
(4, 'For Disposal', 'Marked for disposal', NULL),
(5, 'Disposed', 'Already disposed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `propertytransfer`
--

CREATE TABLE `propertytransfer` (
  `TransferID` int(11) NOT NULL,
  `TransferNumber` varchar(50) DEFAULT NULL,
  `RequestedBy` int(11) NOT NULL,
  `ApprovedBy` int(11) DEFAULT NULL,
  `TransferDate` date NOT NULL,
  `Status` varchar(50) DEFAULT 'Pending',
  `Remarks` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `propertytransferdetail`
--

CREATE TABLE `propertytransferdetail` (
  `TransferDetailID` int(11) NOT NULL,
  `TransferID` int(11) NOT NULL,
  `PropertyID` int(11) NOT NULL,
  `FromDepartmentID` int(11) NOT NULL,
  `ToDepartmentID` int(11) NOT NULL,
  `FromEndUserID` int(11) NOT NULL,
  `ToEndUserID` int(11) NOT NULL,
  `ConditionID` int(11) NOT NULL,
  `Remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `propertytype`
--

CREATE TABLE `propertytype` (
  `PropertyTypeID` int(11) NOT NULL,
  `ClassificationID` int(11) NOT NULL,
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

INSERT INTO `propertytype` (`PropertyTypeID`, `ClassificationID`, `PropertyClassificationID`, `PropertyName`, `Description`, `CreatedDate`, `UpdatedDate`, `deleted_at`) VALUES
(1, 1, 2, 'Office Furniture', 'Chairs, tables, cabinets and other furniture', '2025-12-23 16:00:00', NULL, NULL),
(2, 1, 2, 'Office Equipment', 'Photocopiers, shredders, projectors', '2025-12-23 16:00:00', NULL, NULL),
(3, 1, 2, 'IT Equipment', 'Desktops, laptops, servers, printers, networking equipment', '2025-12-23 16:00:00', NULL, NULL),
(4, 1, 2, 'Machinery and Equipment', 'Industrial machines, generators, HVAC, tools', '2025-12-23 16:00:00', NULL, NULL),
(5, 1, 2, 'Medical Equipment', 'Medical devices and instruments', '2025-12-23 16:00:00', NULL, NULL),
(6, 1, 1, 'Library and Reference Materials', 'Books, subscriptions, educational materials', '2025-12-23 16:00:00', NULL, NULL),
(7, 1, 1, 'Supplies and Consumables', 'Stationery, cleaning supplies, expendable items', '2025-12-23 16:00:00', NULL, NULL),
(8, 1, 1, 'Personal Protective Equipment (PPE)', 'Safety gear and single/short-use protective items', '2025-12-23 16:00:00', NULL, NULL),
(9, 1, 2, 'Other Fixed Assets', 'Miscellaneous non-expendable assets not otherwise classified', '2025-12-23 16:00:00', NULL, NULL);

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
(1, 1, 6, 5, 'http://localhost:3000/qr/scan/1?h=b2a305c61707', '2026-01-12 23:31:35'),
(2, 2, 3, 5, 'http://localhost:3000/qr/scan/2?h=0e0a774ae4d7', '2026-01-12 23:31:35'),
(3, 3, 4, 5, 'http://localhost:3000/qr/scan/3?h=45618df93cff', '2026-01-12 23:31:35'),
(4, 4, 8, 5, 'http://localhost:3000/qr/scan/4?h=da997d3c6834', '2026-01-13 00:02:29');

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
('71NvaUfaWMF5aq6kj69DoJ8TVsPxN9k6C7Zw3o2H', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUm1vVGV3VE1XTzN6YnhCbU1qazRmS25qb0hWZkp3eHo4TVpmODRJViI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9zYW5jdHVtL2NzcmYtY29va2llIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1768321381),
('d6Okbbk0ACqBe0Aez1AI5rHtbLQbxzXiLs1GZ0rn', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoic09JWFZrTzFDeFNXMHp5dm41ZWl4UUxKa2RGa1pYU3R6WVpJazJFRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvZGVwYXJ0bWVudC1oZWFkL2Rhc2hib2FyZC9hY3Rpdml0aWVzIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkaGxVN1FLblViV3BodkpuRnhZZ2FZLmdKVnpCbnhWS0tWLmtqdzd4QjVPaC4xak4wY2ZBNjIiO30=', 1768312371),
('FZMjVQhYMYnhRZbUS1NP7E2Ru9jLYOkQNrlKq0AS', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiWXQ2UVo5MEp3cDc0S3FidEw0MTVzZmJPdk9XODZsMHNocHhCdjRvNiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1768321367),
('mlrRDrY8OM05L51ojApKyBOJYetI3Gjs3NmiiQP6', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiN1o2a0dCMnR2ZUIwYTZhQTB4RjQxSUQ1ZFlJMWFlM1d2b0tHOHh6ZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Njc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvYWRtaW4vbWFzdGVyLWRhdGEvdXNlci1yb2xlcz9wZXJfcGFnZT1hbGwiO3M6NToicm91dGUiO3M6MTY6InVzZXItcm9sZXMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkZnRnR1JoSk9Qby41RldteWJlTEx2Llo4d1g4L0guQmlmWk5ac0s1MXVRSXNKSFVCL2suaDYiO30=', 1768322827),
('Q2twSXjGbca8C4HlpEizJagOZm8kNZxAG4v56rXp', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjROMDdMQXZ6RHUxR2dnRHBOOFVYWXNySnZ0VkxWakYzeXFDT2c3RyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvc2lnbmF0dXJlcy8yL2luc3BlY3RlZCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1768313556),
('zWUZoTOLikz6sxgEf7eS5BslS91n1DOXdeiR99w5', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibFoxd2kycGNxZGdVejl0T3NuSVIzQlcxbnJ2cVZJVXNhS3lIcHJ3USI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvc2lnbmF0dXJlcy8yL2FjY2VwdGVkIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1768313557);

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
  `TIN` varchar(50) DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedDate` datetime DEFAULT current_timestamp(),
  `UpdatedDate` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`SupplierID`, `SupplierName`, `Address`, `ContactPerson`, `ContactNumber`, `Email`, `TIN`, `IsActive`, `CreatedDate`, `UpdatedDate`, `deleted_at`) VALUES
(1, 'Tyron Corporation', 'Zone 7, Molugan, El Salvador City', 'Tyron Micarte', '09476971709', 'micarte2010@gmail.com', '04102010', 1, '2025-12-14 13:00:36', NULL, NULL),
(2, 'Los Pollos Hermanos', 'Albaquerque, New Mexico', 'Gus Fring', '09925077173', 'lospolloshermanos@gmail.com', '11281996', 1, '2025-12-14 13:25:54', NULL, NULL),
(3, 'Cyron Suppliers', 'Lapasan, Cagayan de Oro City, Philippines, 9000', 'Cyron Micarte', '09925077173', 'cyronmicarte@gmail.com', '12281996', 1, '2025-12-26 15:58:19', '2025-12-26 15:58:47', NULL);

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
(2, 1, 'Chrome on Windows', 'c9332e35a6ccae5c7c5b4610fef2aca432c6d38a8edccdb37909d6c885eb73a7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 OPR/124.0.0.0', '127.0.0.1', '2025-12-15 14:25:04', '2026-01-10 17:16:44', '2025-12-11 17:16:44', '2025-12-15 14:25:04'),
(3, 1, 'Chrome on Windows', 'c95708984fb23dfc9cfef266241438707610d2fe0367749f3c592a5ddd84a307', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '::1', NULL, '2026-01-22 22:52:51', '2025-12-23 22:52:51', NULL),
(4, 1, 'Chrome on Windows', 'c3d8ac332ea8932cc62c93d0b8fd7fdfa929d6881091503a760a84ed60871cdf', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '::1', '2025-12-26 01:47:11', '2026-01-24 11:43:19', '2025-12-25 11:43:19', '2025-12-26 01:47:11'),
(5, 2, 'Chrome on Windows', '5d38ec8112a241ac51bd1054287359f13db93eddbeeceedc2c00a75fa7254c62', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '::1', '2025-12-26 01:58:59', '2026-01-25 01:50:51', '2025-12-26 01:50:51', '2025-12-26 01:58:59'),
(6, 1, 'Chrome on Windows', 'f0abe4c2229c497e20f71f96a23a308a6d527600ddd09ed2b3f6ea0299b06504', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '::1', '2025-12-26 13:44:36', '2026-01-25 05:00:31', '2025-12-26 05:00:31', '2025-12-26 13:44:36'),
(7, 2, 'Chrome on Windows', '5da659491728cf8cce8b7047ae8042604fa041012793d906516d7da35c598b76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '::1', NULL, '2026-01-25 15:40:17', '2025-12-26 15:40:17', NULL),
(8, 1, 'Chrome on Windows', 'a3051dd0a8a9a066b757288fd8ac12e048ab422eb0625e2bb48b148ec43b1119', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '::1', NULL, '2026-01-25 15:42:08', '2025-12-26 15:42:08', NULL),
(9, 1, 'Chrome on Windows', 'b92e29cf3590cb2081e5e23d680c14f90394e6130a76487b47cd8fb01b800465', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-01-25 19:58:42', '2025-12-26 19:58:42', NULL),
(10, 2, 'Chrome on Windows', '70d67ccb4d54238fd7f39e5ad25e384d4994f23129b871c4504abfe1c2eac54c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-01-25 20:25:30', '2025-12-26 20:25:30', NULL),
(11, 1, 'Chrome on Windows', '007b0a3e275d0bbc1f9ca5624401a575d08c77cb7a7c8b289158ae284aa5583a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2025-12-26 21:30:25', '2026-01-25 20:37:55', '2025-12-26 20:37:55', '2025-12-26 21:30:25'),
(12, 2, 'Chrome on Windows', '94ada0a3e302afb58e6232d466783a443e42cebe1112dd4db70561c0d19e695f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-01-25 21:31:20', '2025-12-26 21:31:20', NULL),
(13, 1, 'Chrome on Windows', 'a5c72bdb09e2ee1e42d280443e63c8c271654b763fe0391e93412a492679c314', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2025-12-26 21:45:10', '2026-01-25 21:44:53', '2025-12-26 21:44:53', '2025-12-26 21:45:10'),
(14, 2, 'Chrome on Windows', 'e78fd6567203219bca58ec8d6a4adf51d41fded34533b12ec668b0c59ee7debf', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2025-12-26 22:14:13', '2026-01-25 21:46:23', '2025-12-26 21:46:23', '2025-12-26 22:14:13'),
(15, 1, 'Chrome on Windows', '2a4fbee9a3c9a96124751e83b736dbad0e70937c22df5487d294f08a99c819ed', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-01-25 22:35:58', '2025-12-26 22:35:58', NULL),
(16, 2, 'Chrome on Windows', 'c6ba2be624d0c8d1fbd27bb47776e15e631a2c05adb6de82b08e62ab3d5ba446', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-01-25 22:37:45', '2025-12-26 22:37:45', NULL),
(17, 1, 'Chrome on Windows', '5d4262ee0011e17413869e5da4a6aa4a227f6c6b192bab64a63b8f7633065ed5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-01-02 15:24:42', '2026-01-25 23:22:56', '2025-12-26 23:22:56', '2026-01-02 15:24:42'),
(18, 1, 'Chrome on Windows', '42abe6d47c3a590b2dab583288d4d0e46795465a1a730d186ecdff5e786e2a31', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-01-05 01:48:44', '2026-02-04 01:00:25', '2026-01-05 01:00:25', '2026-01-05 01:48:44'),
(20, 1, 'Chrome on Windows', '3af7083adb0565959be1f7f027638dd2ee9e8acb841e370a6ec62d856a093dee', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-04 01:58:19', '2026-01-05 01:58:19', NULL),
(22, 1, 'Chrome on Windows', '56bd465e03a68f6f7bce71bb756c0d7613181c1e958c7ffc492f40f93c639b68', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-01-05 02:01:38', '2026-02-04 02:01:09', '2026-01-05 02:01:09', '2026-01-05 02:01:38'),
(23, 3, 'Chrome on Windows', '661b7c08408ac1210c7701aab8ec10d1d57a766e9ff7bcac8e552ce9d07243c8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-01-06 01:20:13', '2026-02-04 02:02:12', '2026-01-05 02:02:12', '2026-01-06 01:20:13'),
(24, 1, 'Chrome on Windows', '2ee51f8f0f66ba138b691a048640539455e639d375ab1c52e86bb4aa730d3267', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-01-06 15:15:16', '2026-02-05 13:38:24', '2026-01-06 13:38:24', '2026-01-06 15:15:16'),
(26, 1, 'Chrome on Windows', 'f9eaeb02eb18c3e3c3ba65be589c2c4730dbd2d60e9a488b681e302a24903b21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-01-06 15:37:21', '2026-02-05 15:27:45', '2026-01-06 15:27:45', '2026-01-06 15:37:21'),
(28, 1, 'Chrome on Windows', 'b6149e5a14b683c4669509f216f9678d8b21a8ba7fe642828f60c64ef20d7fb0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-01-06 16:09:10', '2026-02-05 16:08:53', '2026-01-06 16:08:53', '2026-01-06 16:09:10'),
(30, 1, 'Chrome on Windows', 'dde1ab4e7eb4bcbfc83badc44b37910cdb22c162cffaebbae0b399a4b1c4c3b8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-05 16:16:38', '2026-01-06 16:16:38', NULL),
(31, 3, 'Chrome on Windows', '52e0cb436bf5ccc37650430c253127446352b19371e66ea7f813698234b41d6a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-05 20:32:24', '2026-01-06 20:32:24', NULL),
(32, 1, 'Chrome on Windows', 'f03f21b05baecab19f166f06a4f31f105023f276a78869dd9cc77077e1d52d1c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-01-06 20:37:24', '2026-02-05 20:33:33', '2026-01-06 20:33:33', '2026-01-06 20:37:24'),
(33, 3, 'Chrome on Windows', '2f9c885621f62598e3337678cb904854c3770216999d4f7e0ecfe4cd74c2f304', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-05 22:30:05', '2026-01-06 22:30:05', NULL),
(34, 1, 'Chrome on Windows', '3c4c1f5d3e899209c0d465f5953e2ab06acaa89c765a09108de3a9ada178d87f', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-05 23:49:42', '2026-01-06 23:49:42', NULL),
(35, 3, 'Chrome on Windows', '8c40a388046d06eb74145c85b46ed400eaaa67fffdda7bbfe1cf24407091e31d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-06 00:50:54', '2026-01-07 00:50:54', NULL),
(36, 1, 'Chrome on Windows', 'acf46ed00ad04146142ba504158875138b4d298cc2e35ee2dc25f507acb9904c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-06 18:38:01', '2026-01-07 18:38:01', NULL),
(37, 3, 'Trusted Device', '46026b980d69474966efc08c8d8e46e9ba54d1845f0cddd74995f0c06dcd5b64', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-07 15:27:27', '2026-01-08 15:27:27', NULL),
(38, 1, 'Trusted Device', 'ca297208def7dfb29420c534e502e5d4eb91797fb442964a43300bf5b98d6ece', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-01-08 22:18:51', '2026-02-07 16:12:37', '2026-01-08 16:12:37', '2026-01-08 22:18:51'),
(39, 3, 'Trusted Device', 'e32b2bb39edf7e9b1f806dbec3ce22790f9c93c125c2837474a97a1d06f39e11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-08 01:41:38', '2026-01-09 01:41:38', NULL),
(40, 1, 'Trusted Device', 'ce1db3455a8f609cb8caf75cef64fb3ecd4f178d526428a56d558a8d24928a38', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-08 01:53:01', '2026-01-09 01:53:01', NULL),
(41, 3, 'Trusted Device', 'b9b6280c8d3492730f8b5feaf7dd4a87d214f799ae965948e4569229fad77d3e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-08 01:55:58', '2026-01-09 01:55:58', NULL),
(42, 1, 'Trusted Device', 'cad3b0dfe3d9ecf4f80da6d702b611a3f117c38b36d7d41ea16e97a792f4eb74', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-01-09 14:56:59', '2026-02-08 01:56:30', '2026-01-09 01:56:30', '2026-01-09 14:56:59'),
(43, 3, 'Trusted Device', 'c3de9ff5c8c252d4de86ef3d1dac8d7849d9b0f76aa5913fab9d30605df8fcfc', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-08 18:05:33', '2026-01-09 18:05:33', NULL),
(44, 1, 'Trusted Device', 'a920b90eba445dcd64cda91b42f06cb58ed440d0f906d385ea39e45886ebcc5b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-08 18:06:58', '2026-01-09 18:06:58', NULL),
(45, 3, 'Trusted Device', 'd9aabe273e94fad890fbbdf829641d4dda199a11d2c22783ddad6c10b8305e9b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-08 19:28:45', '2026-01-09 19:28:45', NULL),
(46, 1, 'Trusted Device', 'ba7c4169ba72ec47168488baedef061896c7683bbc5469846be7c8e0e233d758', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-08 19:31:14', '2026-01-09 19:31:14', NULL),
(47, 3, 'Trusted Device', 'eae6674b11022076b0035d0d920e279095e28395d698de8a44a05ebbe2d39906', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-08 19:32:31', '2026-01-09 19:32:31', NULL),
(48, 1, 'Trusted Device', 'f967ee1375b2ea2e2d9391ad4cc43ee50955642ff38e90c46f012e7c3ddaea65', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-01-09 21:45:43', '2026-02-08 21:24:57', '2026-01-09 21:24:57', '2026-01-09 21:45:43'),
(49, 1, 'Trusted Device', '41beef924fe335f1a874fce69da6ab396d819becf1f1280d5eafa8b1b2523401', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-09 14:49:10', '2026-01-10 14:49:10', NULL),
(50, 1, 'Trusted Device', '6f7a2cd942e97c2c3103b4f5822099e2ce6f07e281b59b400a68c92e911bc19a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-10 23:03:51', '2026-01-11 23:03:51', NULL),
(51, 3, 'Trusted Device', '60e9d74b1d471b38ad962bf4cde63e18a6d61029137a5e0530c1dda35ade70e5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '127.0.0.1', '2026-01-14 00:22:39', '2026-02-10 23:06:54', '2026-01-11 23:06:54', '2026-01-14 00:22:39'),
(52, 1, 'Trusted Device', 'f6ef5da47b0aeded99834c5cdd47f50890c09839a03c5c5e395dd83a3f74976b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-10 23:08:12', '2026-01-11 23:08:12', NULL),
(53, 1, 'Trusted Device', '07e1529a61c959fcfebc46880decd051332d3c318b920eb2b9ffcb6111de4fab', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-10 23:15:21', '2026-01-11 23:15:21', NULL),
(54, 1, 'Trusted Device', '1e3ed13173ada47dd317d1a638f093e3d820f88df940bbafe94586cebc7459f0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-01-14 00:22:02', '2026-02-10 23:18:33', '2026-01-11 23:18:33', '2026-01-14 00:22:02');

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
(1, 1, 1, 'gso.admin', 'micarte.aaron08@gmail.com', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$ftgGRhJOPo.5FWmybeLLv.Z8wX8/H.BifZNZsK51uQIsJHUB/k.h6', 0, 'GSO Administrator', 1, 1, 'eyJpdiI6IndTcFYzbnlmQjd5R2dnaEk1dzNNV1E9PSIsInZhbHVlIjoiaVZKanNlUDFSS20vSXJrblptbC9DMFlnTnVJVWpsQ1ZyaTkxTDZOVjViQT0iLCJtYWMiOiI0YWMwODkzOGRmZmExNzBjMWVkNDRmNGQzYWIyMDNkNGY0NWQ1MzNmM2VmNWQzYjE0YzJmMWY3OWRhOTViYTNhIiwidGFnIjoiIn0=', 'eyJpdiI6IitaZWR2a2JoQk5UWHREbXBDOWVPdWc9PSIsInZhbHVlIjoiTEVwTVB5K0xoK000c3pFdHdWOGpnNnBtQ1k2WWsreVZLb1BjMWh3dzlscmJlWCthVWxPR3ZhNkx1VWNTZXQrUlAwNytQVUxpM1dyM1VacFc2TW9hR24zT3V0NC8vaXJJeFFMdERzVnpZNUVKTkdkdUdRWUZ4QldzNElqSVhCdE5BUlhVVnBVTGRIQ0YyeFh4d3F1RDRqZy92eGZpeURrc1ZnRm90QWM1dFpRPSIsIm1hYyI6ImQ2YjlkMDg3ZGZmNmUwZTM1ZTI2ZmZiYzk0NzU0MWU2ZTIyMjk3ZGE0YmZiYWNlYTRmYTU1ZTVmOTVhZDAzOTciLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-01-14 00:21:36'),
(2, 2, 1, 'gso.staff.aaronmicarte', 'aaron.micarte@gso.gov.ph', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$YU66h83wjMCOJ9LMN81o8OuWpIuFYTFQpHCxkuWDAurXbYJS9Q.Mu', 0, 'Aaron Micarte', 1, 1, 'eyJpdiI6IjlHZGd3c0dKcmxwcnBLNEdhN2pkWVE9PSIsInZhbHVlIjoiRkd3RWh6SUczdmt3NnpVU0lhUnVGazNSSmdCYkU3OCtLZzA3T1RPZW1BST0iLCJtYWMiOiI3NjliN2M0MDBjZDE4N2YyODcxMWVkMjNjODdiMmVlMDcwZDEwYWIzYTk3NzFhMDVkMjY1MTQzMDM4Y2Y0OThkIiwidGFnIjoiIn0=', 'eyJpdiI6InRLc2pkQjlRVHpZd2ZzKzZ3c0RCRmc9PSIsInZhbHVlIjoiZUlXMnFlWFZ0dkNtZmUxMVVON0VUa3lXOG1EWDNmVjdrRFUxN09TUElBR2Q1K1NNeEF1WHJjTFMyN3dNbG01alUxMGN1b0l4cE5qQmRPL1BUNGxPRU00VGFOaFVtNEd2SWpJTnlEYmhLbTFrVEk0NU51UDY1S2Z4K2RCOHhxbGVyZnkyU1lXbkp2QXFPNlZWNCtwZ0lndHVyd2ljeW4wSUdObktkajJuVkpnPSIsIm1hYyI6ImU0NDM1MWY0YzZkYTYyZmMwY2QzYWM1M2ZiYmMwZTE4MzY0NzM2OWU0YzU2YTg3ODljOWY1NzhiNWVkNWEzNWQiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-01-14 00:21:36'),
(3, 4, 5, 'IT_Head_Chokoy', 'custodian@gso.gov.ph', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$hlU7QKnUbWphvJnFxYgaY.gJVzBnxVKKV.kjw7xB5Oh.1jN0cfA62', 0, 'Chokoy Covid', 1, 1, 'eyJpdiI6InZzeDUwTzBpVVp5VHczZGhUUFhJSWc9PSIsInZhbHVlIjoieEZvVFpWV3JZMmFYRXJxUEZpckhpVXJwcGJaTExzMDJLaUU4MzEyeW9wVT0iLCJtYWMiOiJmYzdkMjc2YzU2MTEzYjBhMjc1NmFiNTI1NzU3ZWYxOTI2ZjBjOWYxYzMyZmI2MmU2MTUxNGFjOGY4OWY3ODFjIiwidGFnIjoiIn0=', 'eyJpdiI6IldkRlBLdlErenZzdXZjR1NRRTYrdUE9PSIsInZhbHVlIjoiRUMrVWtMSlAvTEY0cEZSQ3NyY2JBUDBsT0ZkUS81NUplTlU0b2dwWGVzZjBlUURDVUFrUWlhQWhrSjYzNXlINTd0a2srT2pOellXZXlnbjNMNno3VVV6b21GNUdoQVFBR01LclFnZTdFM1lOQmJCb2FsTy8wQjRFNkYvckFzL1JmSFV5NmtwNnd6M0ZlQktwYWtQT3ZUUWhiR2owQmdheVRxU0d3RkQ2UURFPSIsIm1hYyI6Ijg5MWZkYzdkNTlkM2MxMDkxMzdjYTIyMWU5ZjgxZTcwMmQwZDMxZThmZGEwNzA4ZjFlNWU4ZjE1ZTU0YTliMjQiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-01-14 00:23:07');

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
(6, 'Admin', 'System Administrator - Full Access', '2025-12-09 17:28:09', '2025-12-14 15:04:49', '2025-12-14 07:04:49'),
(7, 'GSO Staff', 'General Services Office Staff', '2025-12-09 17:28:09', '2025-12-14 15:04:55', '2025-12-14 07:04:55'),
(8, 'Property Custodian', 'Manages property inventory', '2025-12-09 17:28:09', '2025-12-14 15:04:59', '2025-12-14 07:04:59'),
(9, 'Department Head', 'Can approve requests', '2025-12-09 17:28:09', '2025-12-14 15:05:03', '2025-12-14 07:05:03'),
(10, 'End User', 'Regular property accountable user', '2025-12-09 17:28:09', '2025-12-14 15:05:07', '2025-12-14 07:05:07');

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
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `classification`
--
ALTER TABLE `classification`
  ADD PRIMARY KEY (`ClassificationID`),
  ADD KEY `CategoryID` (`CategoryID`);

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
  ADD KEY `ReceivedBy` (`ReceivedBy`);

--
-- Indexes for table `deliveryreceiptdetail`
--
ALTER TABLE `deliveryreceiptdetail`
  ADD PRIMARY KEY (`DRDetailID`),
  ADD KEY `DRID` (`DRID`),
  ADD KEY `PODetailID` (`ItemDescription`),
  ADD KEY `PropertyTypeID` (`PropertyTypeID`),
  ADD KEY `UnitOfMeasureID` (`UnitOfMeasureID`);

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
  ADD KEY `propertyissuance_status_index` (`Status`);

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
  ADD UNIQUE KEY `TransferNumber` (`TransferNumber`),
  ADD KEY `RequestedBy` (`RequestedBy`),
  ADD KEY `ApprovedBy` (`ApprovedBy`);

--
-- Indexes for table `propertytransferdetail`
--
ALTER TABLE `propertytransferdetail`
  ADD PRIMARY KEY (`TransferDetailID`),
  ADD KEY `TransferID` (`TransferID`),
  ADD KEY `PropertyID` (`PropertyID`),
  ADD KEY `FromDepartmentID` (`FromDepartmentID`),
  ADD KEY `ToDepartmentID` (`ToDepartmentID`),
  ADD KEY `FromEndUserID` (`FromEndUserID`),
  ADD KEY `ToEndUserID` (`ToEndUserID`),
  ADD KEY `ConditionID` (`ConditionID`);

--
-- Indexes for table `propertytype`
--
ALTER TABLE `propertytype`
  ADD PRIMARY KEY (`PropertyTypeID`),
  ADD KEY `ClassificationID` (`ClassificationID`),
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
-- AUTO_INCREMENT for table `assetrequest`
--
ALTER TABLE `assetrequest`
  MODIFY `RequestID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `assetrequestdetail`
--
ALTER TABLE `assetrequestdetail`
  MODIFY `RequestDetailID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  MODIFY `AssignmentID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `asset_audit_logs`
--
ALTER TABLE `asset_audit_logs`
  MODIFY `AuditID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `asset_request_signatures`
--
ALTER TABLE `asset_request_signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `assignedproperty`
--
ALTER TABLE `assignedproperty`
  MODIFY `PropertyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `classification`
--
ALTER TABLE `classification`
  MODIFY `ClassificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `condition`
--
ALTER TABLE `condition`
  MODIFY `ConditionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `deliveryreceipt`
--
ALTER TABLE `deliveryreceipt`
  MODIFY `DRID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `deliveryreceiptdetail`
--
ALTER TABLE `deliveryreceiptdetail`
  MODIFY `DRDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `disposalmethod`
--
ALTER TABLE `disposalmethod`
  MODIFY `DisposalMethodID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `enduser`
--
ALTER TABLE `enduser`
  MODIFY `EndUserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `iardetail`
--
ALTER TABLE `iardetail`
  MODIFY `IARDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `iar_signatures`
--
ALTER TABLE `iar_signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `icsdetail`
--
ALTER TABLE `icsdetail`
  MODIFY `ICSDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `IARID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventorycustodianslip`
--
ALTER TABLE `inventorycustodianslip`
  MODIFY `ICSID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `loginaudit`
--
ALTER TABLE `loginaudit`
  MODIFY `LoginAuditID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `par`
--
ALTER TABLE `par`
  MODIFY `PARID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pardetail`
--
ALTER TABLE `pardetail`
  MODIFY `PARDetailID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `InventoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `propertyinventorydetail`
--
ALTER TABLE `propertyinventorydetail`
  MODIFY `InventoryDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `propertyissuance`
--
ALTER TABLE `propertyissuance`
  MODIFY `IssuanceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `propertyissuancedetail`
--
ALTER TABLE `propertyissuancedetail`
  MODIFY `IssuanceDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `propertyreceived`
--
ALTER TABLE `propertyreceived`
  MODIFY `ReceivedID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `propertystatus`
--
ALTER TABLE `propertystatus`
  MODIFY `PropertyStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `propertytransfer`
--
ALTER TABLE `propertytransfer`
  MODIFY `TransferID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `propertytransferdetail`
--
ALTER TABLE `propertytransferdetail`
  MODIFY `TransferDetailID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `propertytype`
--
ALTER TABLE `propertytype`
  MODIFY `PropertyTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `qrcode`
--
ALTER TABLE `qrcode`
  MODIFY `QRCodeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `SupplierID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `trusted_devices`
--
ALTER TABLE `trusted_devices`
  MODIFY `DeviceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `unitofmeasure`
--
ALTER TABLE `unitofmeasure`
  MODIFY `UnitOfMeasureID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `userrole`
--
ALTER TABLE `userrole`
  MODIFY `UserRoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- Constraints for table `classification`
--
ALTER TABLE `classification`
  ADD CONSTRAINT `classification_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `category` (`CategoryID`);

--
-- Constraints for table `deliveryreceipt`
--
ALTER TABLE `deliveryreceipt`
  ADD CONSTRAINT `deliveryreceipt_ibfk_2` FOREIGN KEY (`ReceivedBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `deliveryreceiptdetail`
--
ALTER TABLE `deliveryreceiptdetail`
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
-- Constraints for table `iar_signatures`
--
ALTER TABLE `iar_signatures`
  ADD CONSTRAINT `iar_signatures_iar_id_foreign` FOREIGN KEY (`iar_id`) REFERENCES `inspectionacceptancereport` (`IARID`) ON DELETE CASCADE,
  ADD CONSTRAINT `iar_signatures_signed_by_user_id_foreign` FOREIGN KEY (`signed_by_user_id`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `icsdetail`
--
ALTER TABLE `icsdetail`
  ADD CONSTRAINT `icsdetail_ibfk_1` FOREIGN KEY (`ICSID`) REFERENCES `inventorycustodianslip` (`ICSID`),
  ADD CONSTRAINT `icsdetail_ibfk_2` FOREIGN KEY (`PropertyID`) REFERENCES `assignedproperty` (`PropertyID`);

--
-- Constraints for table `iirup`
--
ALTER TABLE `iirup`
  ADD CONSTRAINT `iirup_ibfk_1` FOREIGN KEY (`ReportedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `iirup_ibfk_2` FOREIGN KEY (`ApprovedBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `iirupdetail`
--
ALTER TABLE `iirupdetail`
  ADD CONSTRAINT `iirupdetail_ibfk_1` FOREIGN KEY (`IIRUPID`) REFERENCES `iirup` (`IIRUPID`),
  ADD CONSTRAINT `iirupdetail_ibfk_2` FOREIGN KEY (`PropertyID`) REFERENCES `assignedproperty` (`PropertyID`),
  ADD CONSTRAINT `iirupdetail_ibfk_3` FOREIGN KEY (`ConditionID`) REFERENCES `condition` (`ConditionID`),
  ADD CONSTRAINT `iirupdetail_ibfk_4` FOREIGN KEY (`InspectedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `iirupdetail_ibfk_5` FOREIGN KEY (`RecommendationID`) REFERENCES `iiruprecommendation` (`RecommendationID`);

--
-- Constraints for table `inspectionacceptancereport`
--
ALTER TABLE `inspectionacceptancereport`
  ADD CONSTRAINT `inspectionacceptancereport_ibfk_1` FOREIGN KEY (`DRID`) REFERENCES `deliveryreceipt` (`DRID`),
  ADD CONSTRAINT `inspectionacceptancereport_ibfk_2` FOREIGN KEY (`InspectedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `inspectionacceptancereport_ibfk_3` FOREIGN KEY (`AcceptedBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `inventorycustodianslip`
--
ALTER TABLE `inventorycustodianslip`
  ADD CONSTRAINT `inventorycustodianslip_ibfk_1` FOREIGN KEY (`IssuedTo`) REFERENCES `enduser` (`EndUserID`),
  ADD CONSTRAINT `inventorycustodianslip_ibfk_2` FOREIGN KEY (`IssuedBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `loginaudit`
--
ALTER TABLE `loginaudit`
  ADD CONSTRAINT `loginaudit_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `par`
--
ALTER TABLE `par`
  ADD CONSTRAINT `par_ibfk_1` FOREIGN KEY (`PropertyID`) REFERENCES `assignedproperty` (`PropertyID`),
  ADD CONSTRAINT `par_ibfk_2` FOREIGN KEY (`IssuedTo`) REFERENCES `enduser` (`EndUserID`),
  ADD CONSTRAINT `par_ibfk_3` FOREIGN KEY (`IssuedBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `pardetail`
--
ALTER TABLE `pardetail`
  ADD CONSTRAINT `pardetail_ibfk_1` FOREIGN KEY (`PARID`) REFERENCES `par` (`PARID`) ON DELETE CASCADE,
  ADD CONSTRAINT `pardetail_ibfk_2` FOREIGN KEY (`PropertyID`) REFERENCES `assignedproperty` (`PropertyID`);

--
-- Constraints for table `physicalcount`
--
ALTER TABLE `physicalcount`
  ADD CONSTRAINT `physicalcount_ibfk_1` FOREIGN KEY (`SessionID`) REFERENCES `physicalcountsession` (`SessionID`),
  ADD CONSTRAINT `physicalcount_ibfk_2` FOREIGN KEY (`PropertyID`) REFERENCES `assignedproperty` (`PropertyID`),
  ADD CONSTRAINT `physicalcount_ibfk_3` FOREIGN KEY (`InventoryDetailID`) REFERENCES `propertyinventorydetail` (`InventoryDetailID`),
  ADD CONSTRAINT `physicalcount_ibfk_4` FOREIGN KEY (`ConditionID`) REFERENCES `condition` (`ConditionID`),
  ADD CONSTRAINT `physicalcount_ibfk_5` FOREIGN KEY (`PerformedBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `physicalcountsession`
--
ALTER TABLE `physicalcountsession`
  ADD CONSTRAINT `physicalcountsession_ibfk_1` FOREIGN KEY (`CreatedBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `propertydisposal`
--
ALTER TABLE `propertydisposal`
  ADD CONSTRAINT `propertydisposal_ibfk_1` FOREIGN KEY (`WasteMaterialID`) REFERENCES `wastematerialreport` (`WasteMaterialID`),
  ADD CONSTRAINT `propertydisposal_ibfk_2` FOREIGN KEY (`ApprovedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `propertydisposal_ibfk_3` FOREIGN KEY (`DisposalMethodID`) REFERENCES `disposalmethod` (`DisposalMethodID`);

--
-- Constraints for table `propertydisposaldetail`
--
ALTER TABLE `propertydisposaldetail`
  ADD CONSTRAINT `propertydisposaldetail_ibfk_1` FOREIGN KEY (`DisposalID`) REFERENCES `propertydisposal` (`DisposalID`),
  ADD CONSTRAINT `propertydisposaldetail_ibfk_2` FOREIGN KEY (`WasteDetailID`) REFERENCES `wastematerialdetail` (`WasteDetailID`),
  ADD CONSTRAINT `propertydisposaldetail_ibfk_3` FOREIGN KEY (`PropertyID`) REFERENCES `assignedproperty` (`PropertyID`);

--
-- Constraints for table `propertyinventory`
--
ALTER TABLE `propertyinventory`
  ADD CONSTRAINT `fk_propertyinventory_iar` FOREIGN KEY (`IARID`) REFERENCES `inspectionacceptancereport` (`IARID`) ON DELETE SET NULL,
  ADD CONSTRAINT `propertyinventory_ibfk_1` FOREIGN KEY (`PropertyTypeID`) REFERENCES `propertytype` (`PropertyTypeID`),
  ADD CONSTRAINT `propertyinventory_ibfk_2` FOREIGN KEY (`UnitOfMeasureID`) REFERENCES `unitofmeasure` (`UnitOfMeasureID`),
  ADD CONSTRAINT `propertyinventory_ibfk_3` FOREIGN KEY (`DRID`) REFERENCES `deliveryreceipt` (`DRID`),
  ADD CONSTRAINT `propertyinventory_ibfk_4` FOREIGN KEY (`IARID`) REFERENCES `inspectionacceptancereport` (`IARID`);

--
-- Constraints for table `propertyinventorydetail`
--
ALTER TABLE `propertyinventorydetail`
  ADD CONSTRAINT `propertyinventorydetail_ibfk_1` FOREIGN KEY (`InventoryID`) REFERENCES `propertyinventory` (`InventoryID`),
  ADD CONSTRAINT `propertyinventorydetail_ibfk_2` FOREIGN KEY (`ReceivedID`) REFERENCES `propertyreceived` (`ReceivedID`),
  ADD CONSTRAINT `propertyinventorydetail_ibfk_3` FOREIGN KEY (`PropertyStatusID`) REFERENCES `propertystatus` (`PropertyStatusID`),
  ADD CONSTRAINT `propertyinventorydetail_ibfk_4` FOREIGN KEY (`ConditionID`) REFERENCES `condition` (`ConditionID`),
  ADD CONSTRAINT `propertyinventorydetail_propertytypeid_foreign` FOREIGN KEY (`PropertyTypeID`) REFERENCES `propertytype` (`PropertyTypeID`) ON DELETE SET NULL,
  ADD CONSTRAINT `propertyinventorydetail_sourcedrdetailid_foreign` FOREIGN KEY (`SourceDRDetailID`) REFERENCES `deliveryreceiptdetail` (`DRDetailID`) ON DELETE SET NULL,
  ADD CONSTRAINT `propertyinventorydetail_sourceiardetailid_foreign` FOREIGN KEY (`SourceIARDetailID`) REFERENCES `iardetail` (`IARDetailID`) ON DELETE SET NULL,
  ADD CONSTRAINT `propertyinventorydetail_unitofmeasureid_foreign` FOREIGN KEY (`UnitOfMeasureID`) REFERENCES `unitofmeasure` (`UnitOfMeasureID`) ON DELETE SET NULL;

--
-- Constraints for table `propertyissuance`
--
ALTER TABLE `propertyissuance`
  ADD CONSTRAINT `propertyissuance_ibfk_1` FOREIGN KEY (`IssuedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `propertyissuance_requestid_foreign` FOREIGN KEY (`RequestID`) REFERENCES `assetrequest` (`RequestID`) ON DELETE SET NULL;

--
-- Constraints for table `propertyissuancedetail`
--
ALTER TABLE `propertyissuancedetail`
  ADD CONSTRAINT `propertyissuancedetail_ibfk_1` FOREIGN KEY (`IssuanceID`) REFERENCES `propertyissuance` (`IssuanceID`),
  ADD CONSTRAINT `propertyissuancedetail_ibfk_2` FOREIGN KEY (`InventoryDetailID`) REFERENCES `propertyinventorydetail` (`InventoryDetailID`),
  ADD CONSTRAINT `propertyissuancedetail_requestdetailid_foreign` FOREIGN KEY (`RequestDetailID`) REFERENCES `assetrequestdetail` (`RequestDetailID`) ON DELETE SET NULL;

--
-- Constraints for table `propertyreceived`
--
ALTER TABLE `propertyreceived`
  ADD CONSTRAINT `propertyreceived_ibfk_1` FOREIGN KEY (`IARID`) REFERENCES `inspectionacceptancereport` (`IARID`),
  ADD CONSTRAINT `propertyreceived_ibfk_2` FOREIGN KEY (`ReceivedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `propertyreceived_ibfk_3` FOREIGN KEY (`ReleasedBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `propertytransfer`
--
ALTER TABLE `propertytransfer`
  ADD CONSTRAINT `propertytransfer_ibfk_1` FOREIGN KEY (`RequestedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `propertytransfer_ibfk_2` FOREIGN KEY (`ApprovedBy`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `propertytransferdetail`
--
ALTER TABLE `propertytransferdetail`
  ADD CONSTRAINT `propertytransferdetail_ibfk_1` FOREIGN KEY (`TransferID`) REFERENCES `propertytransfer` (`TransferID`),
  ADD CONSTRAINT `propertytransferdetail_ibfk_2` FOREIGN KEY (`PropertyID`) REFERENCES `assignedproperty` (`PropertyID`),
  ADD CONSTRAINT `propertytransferdetail_ibfk_3` FOREIGN KEY (`FromDepartmentID`) REFERENCES `department` (`DepartmentID`),
  ADD CONSTRAINT `propertytransferdetail_ibfk_4` FOREIGN KEY (`ToDepartmentID`) REFERENCES `department` (`DepartmentID`),
  ADD CONSTRAINT `propertytransferdetail_ibfk_5` FOREIGN KEY (`FromEndUserID`) REFERENCES `enduser` (`EndUserID`),
  ADD CONSTRAINT `propertytransferdetail_ibfk_6` FOREIGN KEY (`ToEndUserID`) REFERENCES `enduser` (`EndUserID`),
  ADD CONSTRAINT `propertytransferdetail_ibfk_7` FOREIGN KEY (`ConditionID`) REFERENCES `condition` (`ConditionID`);

--
-- Constraints for table `propertytype`
--
ALTER TABLE `propertytype`
  ADD CONSTRAINT `propertytype_ibfk_1` FOREIGN KEY (`ClassificationID`) REFERENCES `classification` (`ClassificationID`),
  ADD CONSTRAINT `propertytype_ibfk_2` FOREIGN KEY (`PropertyClassificationID`) REFERENCES `propertyclassification` (`PropertyClassificationID`);

--
-- Constraints for table `qrcode`
--
ALTER TABLE `qrcode`
  ADD CONSTRAINT `qrcode_ibfk_1` FOREIGN KEY (`PropertyID`) REFERENCES `assignedproperty` (`PropertyID`),
  ADD CONSTRAINT `qrcode_ibfk_2` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`);

--
-- Constraints for table `trusted_devices`
--
ALTER TABLE `trusted_devices`
  ADD CONSTRAINT `trusted_devices_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`UserRoleID`) REFERENCES `userrole` (`UserRoleID`),
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`);

--
-- Constraints for table `wastematerialdetail`
--
ALTER TABLE `wastematerialdetail`
  ADD CONSTRAINT `wastematerialdetail_ibfk_1` FOREIGN KEY (`WasteMaterialID`) REFERENCES `wastematerialreport` (`WasteMaterialID`),
  ADD CONSTRAINT `wastematerialdetail_ibfk_2` FOREIGN KEY (`PropertyID`) REFERENCES `assignedproperty` (`PropertyID`),
  ADD CONSTRAINT `wastematerialdetail_ibfk_3` FOREIGN KEY (`ConditionID`) REFERENCES `condition` (`ConditionID`),
  ADD CONSTRAINT `wastematerialdetail_ibfk_4` FOREIGN KEY (`DisposalMethodID`) REFERENCES `disposalmethod` (`DisposalMethodID`);

--
-- Constraints for table `wastematerialreport`
--
ALTER TABLE `wastematerialreport`
  ADD CONSTRAINT `wastematerialreport_ibfk_1` FOREIGN KEY (`ReportedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `wastematerialreport_ibfk_2` FOREIGN KEY (`ApprovedBy`) REFERENCES `user` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
