-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2026 at 12:18 PM
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
(6, 'DR-GSO-2026-003', 'Government Procurement Services', 3, 'PR-GSO-2026-047', 'PO-GSO-2026-080', 'INV-GPS-2026-9012', 1, '2026-02-12', 'Non-expendable property items for GSO operations', 'pending', NULL, 'dr_pictures/gso_property_delivery.jpg', '2026-02-12 16:19:09', NULL);

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
  `InspectionStatus` enum('pending','inspected') NOT NULL DEFAULT 'pending',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deliveryreceiptdetail`
--

INSERT INTO `deliveryreceiptdetail` (`DRDetailID`, `DRID`, `ItemDescription`, `Brand`, `ModelNumber`, `SerialNumber`, `PropertyTypeID`, `QuantityDelivered`, `UnitOfMeasureID`, `UnitCost`, `TotalCost`, `InspectionStatus`, `deleted_at`) VALUES
(12, 6, 'All-in-One Printer', 'Canon', 'imageCLASS MF644Cdw', 'CAN-MF644-001', 7, 1, 2, 35000.00, 35000.00, 'pending', NULL),
(13, 6, 'Executive Desk', 'Steelcase', 'Series 1', 'STLC-S1-002', 6, 1, 2, 25000.00, 25000.00, 'pending', NULL),
(14, 6, 'Projector', 'Epson', 'EB-S41', 'EPS-EBS41-003', 7, 1, 2, 40000.00, 40000.00, 'pending', NULL);

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
(5, 'IT Department', NULL, 'IT', 1, '2025-12-05 02:07:27', NULL, NULL);

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
(1, 2, 5, 'Department Custodian', NULL, NULL, 1, '2026-02-11 22:25:58', '2026-02-11 22:25:58', NULL);

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

-- --------------------------------------------------------

--
-- Table structure for table `inspection_reports`
--

CREATE TABLE `inspection_reports` (
  `InspectionReportID` bigint(20) UNSIGNED NOT NULL,
  `DRID` int(10) UNSIGNED NOT NULL,
  `InspectedBy` int(10) UNSIGNED NOT NULL,
  `InspectorName` varchar(150) NOT NULL,
  `DateInspected` date NOT NULL,
  `Status` enum('pending','inspected') NOT NULL DEFAULT 'pending',
  `Remarks` text DEFAULT NULL,
  `CompletedAt` timestamp NULL DEFAULT NULL,
  `IPAddress` varchar(45) DEFAULT NULL,
  `UserAgent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inspection_report_items`
--

CREATE TABLE `inspection_report_items` (
  `InspectionReportDetailID` bigint(20) UNSIGNED NOT NULL,
  `InspectionReportID` bigint(20) UNSIGNED NOT NULL,
  `DRDetailID` int(11) NOT NULL,
  `Status` enum('pending','inspected') NOT NULL DEFAULT 'pending',
  `Remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(54, 7, '2026-02-12 18:33:59', 'Login', '127.0.0.1');

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
(123, '2026_02_12_000004_ensure_inspection_officer_role', 36);

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
(1, NULL, NULL, NULL, 1, '2026-02-10', NULL, 'Auto-ingested from IAR #IAR-2026-0001', '2026-02-10 22:24:23', NULL, 'INV-20260210-222423-1', NULL);

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
(1, 1, 7, NULL, 'good', '2026-000001', 'SN123', 1, 0, 2, 2, 50000.00, 50000.00, 1, 2, 1, 'Dell', 'OptiPlex7090', 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(2, 1, 7, NULL, 'good', '2026-000002', 'SN321', 1, 0, 2, 2, 50000.00, 50000.00, 2, 3, 1, 'Dell', 'OptiPlex7090', 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(3, 1, 6, NULL, 'good', '2026-000003', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(4, 1, 6, NULL, 'good', '2026-000004', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(5, 1, 6, NULL, 'good', '2026-000005', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(6, 1, 6, NULL, 'good', '2026-000006', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(7, 1, 6, NULL, 'good', '2026-000007', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(8, 1, 6, NULL, 'good', '2026-000008', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(9, 1, 6, NULL, 'good', '2026-000009', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(10, 1, 6, NULL, 'good', '2026-000010', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(11, 1, 6, NULL, 'good', '2026-000011', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(12, 1, 6, NULL, 'good', '2026-000012', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(13, 1, 6, NULL, 'good', '2026-000013', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(14, 1, 6, NULL, 'good', '2026-000014', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(15, 1, 6, NULL, 'good', '2026-000015', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(16, 1, 6, NULL, 'good', '2026-000016', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(17, 1, 6, NULL, 'good', '2026-000017', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(18, 1, 6, NULL, 'good', '2026-000018', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(19, 1, 6, NULL, 'good', '2026-000019', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(20, 1, 6, NULL, 'good', '2026-000020', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(21, 1, 6, NULL, 'good', '2026-000021', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL),
(22, 1, 6, NULL, 'good', '2026-000022', NULL, 1, 0, 2, 3, 120.00, 120.00, 3, 1, 1, NULL, NULL, 'good', '2026-02-10 22:24:23', '2026-02-11 22:25:58', NULL);

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
  `AcknowledgmentNotes` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 1, 1, 1, '2026-02-10', 102400.00, 'Auto-created on acceptance', '2026-02-10 22:24:03');

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
(14, 2, 'Technical and Scientific Equipment', 'Specialized equipment for technical, laboratory, or scientific work', '2026-02-10 18:35:54', '2026-02-10 20:57:37', NULL);

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
('3nlQ09ERQUpjoNknZgBdr7RjKI7oDQJI4CnHusWy', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiQzFnMTg5aG5GN3k4dml4MHRNWVZQUHcyNGdaSmtMVHJaMFR1d2pxMSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvaW5zcGVjdGlvbi9wZW5kaW5nLWl0ZW1zP3Blcl9wYWdlPTUwIjtzOjU6InJvdXRlIjtOO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo3O3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkZmVwU0I2SUdiT1hmN25XdndVRGIydUpqcjJGSk8wN2NrYmUxY0hGRGtOOS9oU3FudS5xUWUiO30=', 1770895069),
('I1Y3X4uJAwPSdxDfEXVtqvzFF0Mdui02vEj6droI', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSko4ZWJOQ0ZFWlB3NHBHeEpaVFNIUUNKVG0wRTFJV2JXZnM5dWRFTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9zYW5jdHVtL2NzcmYtY29va2llIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkZnRnR1JoSk9Qby41RldteWJlTEx2Llo4d1g4L0guQmlmWk5ac0s1MXVRSXNKSFVCL2suaDYiO30=', 1770892038);

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
(63, 1, 'Trusted Device', 'b6c42f2eda845d004d7deb90648db69686d53f5e8bf13d9a23bcc16527ae91ad', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-02-12 15:07:32', '2026-02-14 13:49:39', '2026-01-15 13:49:39', '2026-02-12 15:07:32'),
(64, 6, 'Trusted Device', '60d00027d78271c9f4f8829bbddd7f4ff2809f91a764ffcf743af8f8979ef0e2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '127.0.0.1', NULL, '2026-02-14 14:03:26', '2026-01-15 14:03:26', NULL),
(65, 3, 'Trusted Device', '07b75eea3cf03f22c7bf3f161a0c1947288c024a43583abd50dce6b369869d42', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '127.0.0.1', '2026-01-16 02:03:54', '2026-02-14 14:04:57', '2026-01-15 14:04:57', '2026-01-16 02:03:54'),
(66, 1, 'Trusted Device', '8520aa5965d0204554a2dd4341f98b72f0966e38e3e06044ad274a7001f0a2b9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-03-12 16:08:29', '2026-02-10 16:08:29', NULL),
(67, 3, 'Trusted Device', '16a1c8dc24c61cb974dff59feb7a25039e0b2e253c3e8eebe6bc1304dc515e83', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '127.0.0.1', NULL, '2026-03-14 15:08:58', '2026-02-12 15:08:58', NULL),
(68, 7, 'Trusted Device', 'a909e93b619a57e608ae297aee479a95ef6dad1b7a8d1ec4c07f17a6594d1c78', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '127.0.0.1', NULL, '2026-03-14 18:33:59', '2026-02-12 18:33:59', NULL);

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
(1, 1, 1, 'gso.admin', 'micarte.aaron08@gmail.com', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$ftgGRhJOPo.5FWmybeLLv.Z8wX8/H.BifZNZsK51uQIsJHUB/k.h6', 0, 'GSO Administrator', 1, 1, 'eyJpdiI6Ik4xMnJ1RzIxbC9rMUpYcDJZYTk4S3c9PSIsInZhbHVlIjoiVnk0UUlOeDQ4aFBUNitKNnduWXJINE9SUzVvS0J0NTBXQmxTbVc1WjJRZz0iLCJtYWMiOiJiNTMzYzRmOTUwMTY3MzI2OTY4OTA2OTk3OWY2OWVkMDVjYmQwZjc5NWYyZWEzOGI4M2I1ODNjMDNlYTJkOGE5IiwidGFnIjoiIn0=', 'eyJpdiI6IkJ5eWxNQkpISDNlVHg2ZUtpN2ViN2c9PSIsInZhbHVlIjoid3V2TFN0bExYWGgwR0cwWUlya1VaV0lQc2hVQjI4bGpzR3pSQkRYZlp4RUI5OU5iT0ZkTG9LSVBNWjg5V2pjRzNqYmh5S1IxamVTTzM0UkkybWY3WWpMZVhPU3MwM1JFOXlCanlrNjJ4NXRNOStUSXBnRk1kZDQrQVFwcjVva2JPMnpONFJBUzNrVzlEaWNsUk1EUDlaSmlZOGZUMFR0K0FhYklhQkd0b0prPSIsIm1hYyI6ImNjOGQ2MTYxM2VhYjZhMGFmYmNmYjNjZmVhZmY3YjJiMWNkMTk2ZGM2MzMyNTE4NTM5ZDJjMzA5OTQxNTY1MjEiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-01-15 13:47:23'),
(2, 2, 1, 'gso.staff.aaronmicarte', 'aaron.micarte@gso.gov.ph', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$YU66h83wjMCOJ9LMN81o8OuWpIuFYTFQpHCxkuWDAurXbYJS9Q.Mu', 0, 'Aaron Micarte', 1, 1, 'eyJpdiI6IjlHZGd3c0dKcmxwcnBLNEdhN2pkWVE9PSIsInZhbHVlIjoiRkd3RWh6SUczdmt3NnpVU0lhUnVGazNSSmdCYkU3OCtLZzA3T1RPZW1BST0iLCJtYWMiOiI3NjliN2M0MDBjZDE4N2YyODcxMWVkMjNjODdiMmVlMDcwZDEwYWIzYTk3NzFhMDVkMjY1MTQzMDM4Y2Y0OThkIiwidGFnIjoiIn0=', 'eyJpdiI6InRLc2pkQjlRVHpZd2ZzKzZ3c0RCRmc9PSIsInZhbHVlIjoiZUlXMnFlWFZ0dkNtZmUxMVVON0VUa3lXOG1EWDNmVjdrRFUxN09TUElBR2Q1K1NNeEF1WHJjTFMyN3dNbG01alUxMGN1b0l4cE5qQmRPL1BUNGxPRU00VGFOaFVtNEd2SWpJTnlEYmhLbTFrVEk0NU51UDY1S2Z4K2RCOHhxbGVyZnkyU1lXbkp2QXFPNlZWNCtwZ0lndHVyd2ljeW4wSUdObktkajJuVkpnPSIsIm1hYyI6ImU0NDM1MWY0YzZkYTYyZmMwY2QzYWM1M2ZiYmMwZTE4MzY0NzM2OWU0YzU2YTg3ODljOWY1NzhiNWVkNWEzNWQiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-01-14 00:21:36'),
(3, 4, 5, 'IT_Head_Chokoy', 'custodian@gso.gov.ph', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$hlU7QKnUbWphvJnFxYgaY.gJVzBnxVKKV.kjw7xB5Oh.1jN0cfA62', 0, 'Chokoy Covid', 1, 1, 'eyJpdiI6ImFvVEpuaEFUclVHYXRVSEJmV3NsYWc9PSIsInZhbHVlIjoibDM5NXorMGlPTkVqUHk1YXN0OXgzM3BWd0FoT3BkYVN0ZkdwRzkxRk9yaz0iLCJtYWMiOiJhOWI4MGQzMjUyZGMyYmJhYTNiNjI3NjVjYWI5NGYyNGIyYjUwNmFkNmM1NjAzZGM5MTE3YTIzOWVkMGQ2NDg5IiwidGFnIjoiIn0=', 'eyJpdiI6Ik1mTHF2UjNtNFlyRldRTzcwbDF5S0E9PSIsInZhbHVlIjoiNjcwdVQxeTJtNmNwOUt4Wkd5RVN1alhjcGZ5R0w5Z2g4clI4L3BvRklUbERXaGFEY1MxMHMzaGMwRGVjVmhtbXpKT1AxSlUxeUlMam9PUWRUeFBNcnpJdytsUmFubHpiSExFWEJKOXErK1dLUEtLWFZ0MDVJQ2RPYU11MHorUmRxTS8zRlpmRythNkJlUmh3ZmZUYkNScXBxRlV0YmUwN3NyUjRkQUNCazMwPSIsIm1hYyI6ImJmN2NlN2EwODAzMDNlZTEwYWZkOGRkNjQwNzYwZTE1NzY3MDEyZGMwMTZjMDRjMWQ4NzcwMzY4ZmJiYTU3NTgiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-01-15 13:46:29'),
(5, 4, 4, 'HR_Head_Tyron', 'micarte2005@gmail.com', '2026-01-14 01:50:24', NULL, NULL, NULL, '$2y$12$UjxLHSUjJQBOZYHqrB7ajuK2rIiZ1P9962RTMFifYWMoF.fH7o3Wq', 0, 'Tyron Mecarte', 1, 1, 'eyJpdiI6Ilo3U0pYYVBOd1RFZEtTbmxxUlRXUWc9PSIsInZhbHVlIjoiYXlZTWlzemI3TlFZeWI4MUpuNC9wdEpFelN0emhnbFVWL1g2Ky9lMTFOTT0iLCJtYWMiOiIwZjY3ZjA3OTUwY2M1NGQzOTczMmNhZjA1NzI5NzU4ZDQ0MGEzNDU1NTZkMzEzMmQxYzRjZmI4NzcwYzQ0YjdkIiwidGFnIjoiIn0=', 'eyJpdiI6InplOXhDZXZEeHR1QkF6WEpGYzRjdEE9PSIsInZhbHVlIjoiRGh6cXVuaHRQRDU1aGxGd2hDZGVLdmJkcWIybDNnWkpXaXF5TkdobmpzOS9iYzlNRDNUcldyQ0VZSlR2UDlDd3JvZ1lUVjVIWWJBTWJENmRKZHhva2FNZzJOc3ZaZkordUhzTkJnbkhuNEdYWnoxWDRtVkNZVGFHZkVhb2VLcHdDWVJtYzVZYno2SmJJS1h6YytiRVMwNDZBcVN0TnR4aDBJckRPSThOMmQ4PSIsIm1hYyI6ImExZDNmMzBiN2VjNzJjNzg5ZDEyYjAxMTRmNWJmZjcwZTJjYzhkMWUwNjA0OGU3MDkxNDk5NTlkYzc2NmI2YmMiLCJ0YWciOiIifQ==', '2026-01-14 01:00:26', '2026-02-10 16:52:09'),
(6, 4, 3, 'FD_Head_Cyron', 'aaya.micarte.coc@phinmaed.com', '2026-01-15 14:01:41', NULL, NULL, NULL, '$2y$12$A/XCqp8lXrNCeYvQ6lOyUu6DXxbXtJ3Z7FJ.5q39q9yASzA1EYIKS', 0, 'Cyron Micarte', 1, 1, 'eyJpdiI6Iko3ekRUVUFOMjJpa2dFUExuaWhGUFE9PSIsInZhbHVlIjoiK0ZUQyt2VmpJenJ0c25YZmpwUzNwMU0yQmVoYmdtcTFVN2pwOUZLOHBPOD0iLCJtYWMiOiI4NjFjY2U2ZTEwY2EwZjE2YTZhOWExOWEzZWVmYmZkZjdlYzFlOTdjZmRjZTMxY2I3N2RiYzI5MjIyZmNhOGUyIiwidGFnIjoiIn0=', 'eyJpdiI6IlBGTGE5dnYwdCsrVlJMbzgwZ0t0WWc9PSIsInZhbHVlIjoiY0UrYzltZHJHMG5qTE9FUDljMVpEVDJ4YnNYY0QzeGRIQUNjK0lqS3hNYzJCYVdhZUZYcm9RdnRIRHhPTkNhbnF0Wnk1bW14Y1hKdXQzNmFibndSTno3OXV4V3VMVXRFWHBvM0VCdHozRHgwTkZYaFlQcnVrOThETXFhS3p3dE9FYWhiNWdDa1IybzhaUXhwL0g1WXA4d2crOFlnZktpVnJlK2NJbWJRWDdzPSIsIm1hYyI6IjZkYTMxNDY2ZGUzOGY2ZjVhYmQ0NGZjZWNlODNhZTQ0OTVjMjAyYTMwYjE4ZGNjNDg1ODdjNmZlNTdkM2ViZWEiLCJ0YWciOiIifQ==', '2026-01-15 14:00:32', '2026-02-10 16:52:17'),
(7, 6, 1, 'IO_Abrogar', 'cycymicarte@gmail.com', '2026-02-12 18:27:18', NULL, NULL, NULL, '$2y$12$fepSB6IGbOXf7nWvwUDb2uJjr2FJO07ckbe1cHFDkN9/hSqnu.qQe', 0, 'Francismile Noah Abrogar', 1, 1, 'eyJpdiI6IlF1RW8ybzQxNWVPcy9obHNxZnJkN2c9PSIsInZhbHVlIjoiR3lzZ2VFZldMdkFocEpoRWVaU3BEMFJzRmovYWVPY1Z3MGRZaDFmc1h5VT0iLCJtYWMiOiJiNmE2ZjNmMWU4ZDliZmQ0YTExM2ExODE1MTFjNmE5MGE0Mzc5NjYzZTliOTE2NmU3MmFmNDc3YzFmZTE4YTQwIiwidGFnIjoiIn0=', 'eyJpdiI6InNoeEFiQmczdGtPRzFJeVBtTUl0L1E9PSIsInZhbHVlIjoiZlFjTFZMQ1FJSTAyK0pCNmg1OEhKVVRzYlluZGFvMC9nWk0yOW9BVGhFczMwTWlsM0psWHpKUVM4QkpVVEtTYWFqaG5kWHlqTWNwNmpvSkE5R2MrYXZyaVZUR3crOEdQbGp6ZE0zTkFPcSswR3BnRWdITHVPZ2FsMU1MQmdjeDdueVNMdDlnMWNYRGJxaFl0VC9xZFJzT2k1YUkzaVBRNFpMeDFvcFRHbkJZPSIsIm1hYyI6IjI4ZTlmMjViYTUzOWY3MjZmOWI5MjVhOGQ4ZjE0OTFiOTdlNjJjMzE4MDFhZGJjZTk3MjY4OGQwMmRlN2RjNzIiLCJ0YWciOiIifQ==', '2026-02-12 18:26:12', '2026-02-12 18:33:40');

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
(6, 'Inspection Officer', 'Performs inspection and verification of delivered items', '2026-02-12 18:12:43', NULL, NULL);

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
-- Indexes for table `inspection_reports`
--
ALTER TABLE `inspection_reports`
  ADD PRIMARY KEY (`InspectionReportID`),
  ADD UNIQUE KEY `inspection_reports_drid_unique` (`DRID`);

--
-- Indexes for table `inspection_report_items`
--
ALTER TABLE `inspection_report_items`
  ADD PRIMARY KEY (`InspectionReportDetailID`),
  ADD UNIQUE KEY `inspection_report_items_inspectionreportid_drdetailid_unique` (`InspectionReportID`,`DRDetailID`),
  ADD KEY `inspection_report_items_drdetailid_foreign` (`DRDetailID`);

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
  MODIFY `AuditID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_request_signatures`
--
ALTER TABLE `asset_request_signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignedproperty`
--
ALTER TABLE `assignedproperty`
  MODIFY `PropertyID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `condition`
--
ALTER TABLE `condition`
  MODIFY `ConditionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `deliveryreceipt`
--
ALTER TABLE `deliveryreceipt`
  MODIFY `DRID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `deliveryreceiptdetail`
--
ALTER TABLE `deliveryreceiptdetail`
  MODIFY `DRDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `disposalmethod`
--
ALTER TABLE `disposalmethod`
  MODIFY `DisposalMethodID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `enduser`
--
ALTER TABLE `enduser`
  MODIFY `EndUserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `iardetail`
--
ALTER TABLE `iardetail`
  MODIFY `IARDetailID` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `IARID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_reports`
--
ALTER TABLE `inspection_reports`
  MODIFY `InspectionReportID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inspection_report_items`
--
ALTER TABLE `inspection_report_items`
  MODIFY `InspectionReportDetailID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventorycustodianslip`
--
ALTER TABLE `inventorycustodianslip`
  MODIFY `ICSID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loginaudit`
--
ALTER TABLE `loginaudit`
  MODIFY `LoginAuditID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

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
  MODIFY `InventoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `propertyinventorydetail`
--
ALTER TABLE `propertyinventorydetail`
  MODIFY `InventoryDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `propertyissuance`
--
ALTER TABLE `propertyissuance`
  MODIFY `IssuanceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `propertyissuancedetail`
--
ALTER TABLE `propertyissuancedetail`
  MODIFY `IssuanceDetailID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `propertyreceived`
--
ALTER TABLE `propertyreceived`
  MODIFY `ReceivedID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `PropertyTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `qrcode`
--
ALTER TABLE `qrcode`
  MODIFY `QRCodeID` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `DeviceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

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
-- Constraints for table `deliveryreceipt`
--
ALTER TABLE `deliveryreceipt`
  ADD CONSTRAINT `deliveryreceipt_ibfk_2` FOREIGN KEY (`ReceivedBy`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `deliveryreceipt_requestedby_foreign` FOREIGN KEY (`RequestedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL;

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
-- Constraints for table `inspection_report_items`
--
ALTER TABLE `inspection_report_items`
  ADD CONSTRAINT `inspection_report_items_drdetailid_foreign` FOREIGN KEY (`DRDetailID`) REFERENCES `deliveryreceiptdetail` (`DRDetailID`),
  ADD CONSTRAINT `inspection_report_items_inspectionreportid_foreign` FOREIGN KEY (`InspectionReportID`) REFERENCES `inspection_reports` (`InspectionReportID`);

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
  ADD CONSTRAINT `propertytype_ibfk_2` FOREIGN KEY (`PropertyClassificationID`) REFERENCES `propertyclassification` (`PropertyClassificationID`);

--
-- Constraints for table `qrcode`
--
ALTER TABLE `qrcode`
  ADD CONSTRAINT `qrcode_ibfk_1` FOREIGN KEY (`PropertyID`) REFERENCES `assignedproperty` (`PropertyID`),
  ADD CONSTRAINT `qrcode_ibfk_2` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`);

--
-- Constraints for table `requisition_issue_slips`
--
ALTER TABLE `requisition_issue_slips`
  ADD CONSTRAINT `requisition_issue_slips_approved_by_user_id_foreign` FOREIGN KEY (`approved_by_user_id`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `requisition_issue_slips_asset_request_id_foreign` FOREIGN KEY (`asset_request_id`) REFERENCES `assetrequest` (`RequestID`),
  ADD CONSTRAINT `requisition_issue_slips_issued_by_user_id_foreign` FOREIGN KEY (`issued_by_user_id`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `requisition_issue_slips_prepared_by_user_id_foreign` FOREIGN KEY (`prepared_by_user_id`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `requisition_issue_slips_received_by_user_id_foreign` FOREIGN KEY (`received_by_user_id`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `requisition_issue_slips_requested_by_user_id_foreign` FOREIGN KEY (`requested_by_user_id`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `requisition_issue_slip_items`
--
ALTER TABLE `requisition_issue_slip_items`
  ADD CONSTRAINT `requisition_issue_slip_items_asset_request_detail_id_foreign` FOREIGN KEY (`asset_request_detail_id`) REFERENCES `assetrequestdetail` (`RequestDetailID`),
  ADD CONSTRAINT `requisition_issue_slip_items_inventory_detail_id_foreign` FOREIGN KEY (`inventory_detail_id`) REFERENCES `propertyinventorydetail` (`InventoryDetailID`),
  ADD CONSTRAINT `requisition_issue_slip_items_ris_id_foreign` FOREIGN KEY (`ris_id`) REFERENCES `requisition_issue_slips` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `requisition_issue_slip_signatures`
--
ALTER TABLE `requisition_issue_slip_signatures`
  ADD CONSTRAINT `requisition_issue_slip_signatures_ris_id_foreign` FOREIGN KEY (`ris_id`) REFERENCES `requisition_issue_slips` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requisition_issue_slip_signatures_signed_by_user_id_foreign` FOREIGN KEY (`signed_by_user_id`) REFERENCES `user` (`UserID`);

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
