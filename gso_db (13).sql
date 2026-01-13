-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 12, 2026 at 07:37 AM
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
(1, 'REQ-2026-0001', 3, 5, 'for dota purposes', 'rejected', 1, '2026-01-11 15:54:32', 'wakay boot sige lag dota', NULL, NULL, '2026-01-11 15:44:26', '2026-01-11 15:44:26', '2026-01-11 15:54:32', NULL),
(2, 'REQ-2026-0002', 3, 5, 'para maka toon', 'received', 1, '2026-01-11 16:05:22', 'will be delivered', 1, '2026-01-11 16:06:28', '2026-01-11 16:03:29', '2026-01-11 16:03:29', '2026-01-11 16:07:14', NULL),
(3, 'REQ-2026-0003', 3, 5, 'hahaha', 'received', 1, '2026-01-11 17:11:31', 'hahaha', 1, '2026-01-11 17:11:58', '2026-01-11 17:10:59', '2026-01-11 17:10:59', '2026-01-11 17:14:39', NULL);

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
(1, 1, NULL, 'Laptop', 'pc', 1, 'for dota', 'Aaron Micarte', 'for dota', NULL, NULL, NULL, NULL, '2026-01-11 15:44:26', '2026-01-11 15:54:32', NULL),
(2, 2, NULL, 'Laptop', 'pc', 1, 'for toon', 'Cyron Micarte', 'smooth', 'PAR', NULL, NULL, 'okay ana ba dili mag dota', '2026-01-11 16:03:29', '2026-01-11 16:05:22', NULL),
(3, 3, NULL, 'Printer', 'unit', 1, 'hahaha', 'IT Department', 'hahaha', 'ICS', 3, 5, 'hajaha', '2026-01-11 17:10:59', '2026-01-11 17:11:31', NULL);

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
(1, 2, NULL, NULL, NULL, 'issuance', 'PAR', 'okay ana ba dili mag dota', 1, '2026-01-11 16:05:22', '2026-01-11 16:05:22', '2026-01-11 16:05:22', NULL),
(2, 3, NULL, 3, 5, 'issuance', 'ICS', 'hajaha', 1, '2026-01-11 17:11:31', '2026-01-11 17:11:31', '2026-01-11 17:11:31', NULL);

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
(1, 'assetrequest', 1, 'created', 3, '{\"request_number\":\"REQ-2026-0001\",\"items_count\":1,\"department\":\"IT Department\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-11 15:44:26'),
(2, 'asset_request', 1, 'rejected', 1, '{\"request_number\":\"REQ-2026-0001\",\"items_reviewed\":1,\"reason\":\"wakay boot sige lag dota\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 15:54:32'),
(3, 'assetrequest', 2, 'created', 3, '{\"request_number\":\"REQ-2026-0002\",\"items_count\":1,\"department\":\"IT Department\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-11 16:03:29'),
(4, 'asset_request', 2, 'approved', 1, '{\"request_number\":\"REQ-2026-0002\",\"items_reviewed\":1,\"notes\":\"will be delivered\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 16:05:22'),
(5, 'asset_request', 2, 'issued', 1, '\"{\\\"request_number\\\":\\\"REQ-2026-0002\\\",\\\"issuances_count\\\":1,\\\"date_issued\\\":\\\"2026-01-11\\\"}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL),
(6, 'property_issuance', 1, 'acknowledged', 3, '\"{\\\"document_number\\\":\\\"PAR-2026-0001\\\",\\\"acknowledged_by\\\":3,\\\"acknowledgment_date\\\":\\\"2026-01-11T16:07:14.571334Z\\\"}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', NULL),
(7, 'assetrequest', 3, 'created', 3, '{\"request_number\":\"REQ-2026-0003\",\"items_count\":1,\"department\":\"IT Department\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-11 17:10:59'),
(8, 'asset_request', 3, 'approved', 1, '{\"request_number\":\"REQ-2026-0003\",\"items_reviewed\":1,\"notes\":\"hahaha\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 17:11:31'),
(9, 'asset_request', 3, 'issued', 1, '\"{\\\"request_number\\\":\\\"REQ-2026-0003\\\",\\\"issuances_count\\\":1,\\\"date_issued\\\":\\\"2026-01-11\\\"}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL),
(10, 'property_issuance', 2, 'acknowledged', 3, '\"{\\\"document_number\\\":\\\"ICS-2026-0001\\\",\\\"acknowledged_by\\\":3,\\\"acknowledgment_date\\\":\\\"2026-01-11T17:14:39.449629Z\\\"}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', NULL);

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
(1, 1, 'requester', 3, 'canvas', 'public', 'signatures/asset_request_1_requester_1768146266.png', 'http://localhost:8000/storage/signatures/asset_request_1_requester_1768146266.png', 'image/png', 1000, 300, 14719, '4e33de72a160a57ba91f43fb9266217147edf2fd1a00d9407e0d8f95da60a7e5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-11 15:44:26', '2026-01-11 15:44:26'),
(2, 2, 'requester', 3, 'canvas', 'public', 'signatures/asset_request_2_requester_1768147409.png', 'http://localhost:8000/storage/signatures/asset_request_2_requester_1768147409.png', 'image/png', 1000, 300, 13858, 'cff12e5d2381cf03ffe2662a0e7d447ae1632deb6f71d8f2e279fdd55f2b198e', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-11 16:03:29', '2026-01-11 16:03:29'),
(3, 2, 'approver', 1, 'canvas', 'public', 'signatures/asset_request_2_approver_1768147522.png', 'http://localhost:8000/storage/signatures/asset_request_2_approver_1768147522.png', 'image/png', 1000, 300, 14289, '58badfec2766b1432f100619900be80ebb3f507fd163e03567f228fd3715a006', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 16:05:22', '2026-01-11 16:05:22'),
(4, 2, 'issuer', 1, 'canvas', 'public', 'signatures/asset_request_2_issuer_1768147588.png', 'http://localhost:8000/storage/signatures/asset_request_2_issuer_1768147588.png', 'image/png', 1000, 300, 15553, '3f6163f95af600ccfe2f89eca522b2d2d4da2617f5873fff29a808c7ccb1df62', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 16:06:28', '2026-01-11 16:06:28'),
(5, 3, 'requester', 3, 'canvas', 'public', 'signatures/asset_request_3_requester_1768151459.png', 'http://localhost:8000/storage/signatures/asset_request_3_requester_1768151459.png', 'image/png', 1000, 300, 12475, 'b93f40107163ff0853337d337aea461be4e4e9f83b629ce9b30dfa8ea8bf8f4b', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-11 17:10:59', '2026-01-11 17:10:59'),
(6, 3, 'approver', 1, 'canvas', 'public', 'signatures/asset_request_3_approver_1768151491.png', 'http://localhost:8000/storage/signatures/asset_request_3_approver_1768151491.png', 'image/png', 1000, 300, 12176, 'eb4efd9e98370e5f47a0bfaeebf9d4e98b03eda15396160768f5353d1bcbf7e3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 17:11:31', '2026-01-11 17:11:31'),
(7, 3, 'issuer', 1, 'canvas', 'public', 'signatures/asset_request_3_issuer_1768151518.png', 'http://localhost:8000/storage/signatures/asset_request_3_issuer_1768151518.png', 'image/png', 1000, 300, 14083, '98ff6f67e202437b40edfe75ca7d8f93f7450b0c910602e232c1c0e5326d4b0b', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-11 17:11:58', '2026-01-11 17:11:58');

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
(1, 8, 1, 5, 'ICS-2026-0001', 'good', '2026-01-11', 2, 1, 'hahaha', '2026-01-12 01:11:58', '2026-01-12 01:11:58');

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
(1, 2, 5, 'IT Department', NULL, NULL, 1, '2026-01-12 01:11:58', '2026-01-12 01:11:58', NULL);

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
(7, 1, '2026-01-12 14:05:38', 'Login', '127.0.0.1');

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
(104, '2026_01_08_230729_add_item_description_to_iar_detail_table', 22);

-- --------------------------------------------------------

--
-- Table structure for table `par`
--

CREATE TABLE `par` (
  `PARID` int(11) NOT NULL,
  `PARNumber` varchar(50) DEFAULT NULL,
  `PropertyID` int(11) NOT NULL,
  `IssuedTo` int(11) NOT NULL,
  `IssuedBy` int(11) NOT NULL,
  `DateIssued` date NOT NULL,
  `TotalCost` decimal(18,2) DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `CreatedDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 1, 4, NULL, 'good', '2026-000001', 'N/A', 1, 1, 1, 1, 8000.00, 8000.00, 1, 35, 1, 'N/A', 'N/A', 'good', '2026-01-11 23:31:21', NULL, NULL),
(2, 1, 4, NULL, 'good', '2026-000002', 'N/A', 1, 1, 1, 1, 8000.00, 8000.00, 1, 35, 1, 'N/A', 'N/A', 'good', '2026-01-11 23:31:21', NULL, NULL),
(3, 1, 3, NULL, 'good', '2026-000003', 'DTC202601', 1, 1, 1, 1, 38000.00, 38000.00, 2, 36, 1, 'Acer', 'Veriton X', 'good', '2026-01-11 23:31:21', NULL, NULL),
(4, 1, 4, NULL, 'good', '2026-000004', 'N/A', 1, 1, 1, 1, 6500.00, 6500.00, 3, 37, 1, 'N/A', 'N/A', 'good', '2026-01-11 23:31:21', NULL, NULL),
(5, 1, 4, NULL, 'good', '2026-000005', 'N/A', 1, 1, 1, 1, 6500.00, 6500.00, 3, 37, 1, 'N/A', 'N/A', 'good', '2026-01-11 23:31:21', NULL, NULL),
(6, 2, 3, NULL, 'good', '2026-000006', 'LTP202601', 1, 0, 2, 1, 42000.00, 42000.00, 4, 32, 1, 'Lenovo', 'ThinkPad E14', 'good', '2026-01-11 23:31:24', '2026-01-12 00:06:28', NULL),
(7, 2, 3, NULL, 'good', '2026-000007', 'LTP202601', 1, 1, 1, 1, 42000.00, 42000.00, 4, 32, 1, 'Lenovo', 'ThinkPad E14', 'good', '2026-01-11 23:31:25', NULL, NULL),
(8, 2, 3, NULL, 'good', '2026-000008', 'PRN202601', 1, 0, 2, 1, 18500.00, 18500.00, 5, 33, 1, 'HP', 'LaserJet Pro M404', 'good', '2026-01-11 23:31:25', '2026-01-12 01:11:58', NULL),
(9, 2, 4, NULL, 'good', '2026-000009', 'N/A', 1, 1, 1, 1, 3500.00, 3500.00, 6, 34, 1, 'N/A', 'N/A', 'good', '2026-01-11 23:31:25', NULL, NULL),
(10, 2, 4, NULL, 'good', '2026-000010', 'N/A', 1, 1, 1, 1, 3500.00, 3500.00, 6, 34, 1, 'N/A', 'N/A', 'good', '2026-01-11 23:31:25', NULL, NULL),
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
(1, 2, 1, NULL, 'Cyron Micarte', 'PAR', 'PAR-2026-0001', 'acknowledged', '2026-01-11', NULL, 'data:image/png;base64,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', '2026-01-12 00:07:14', 'okay', NULL, '2026-01-12 00:06:28'),
(2, 3, 1, 3, NULL, 'ICS', 'ICS-2026-0001', 'acknowledged', '2026-01-11', 'hahaha', 'data:image/png;base64,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', '2026-01-12 01:14:39', 'hahaha', NULL, '2026-01-12 01:11:58');

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
(1, 1, 2, 'Laptop', 'pc', 6, 1, 'okay ana ba dili mag dota', '2026-01-11 16:06:28', '2026-01-11 16:06:28', NULL),
(2, 2, 3, 'Printer', 'unit', 8, 1, 'hajaha', '2026-01-11 17:11:58', '2026-01-11 17:11:58', NULL);

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
(1, 1, 8, 5, 'http://localhost:8000/qr/scan/1?h=b2a305c61707', '2026-01-12 01:11:58');

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
('Cd4pgmgc6FYVurEojAlXZ9I4plNXEoPJxxOFlHJE', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUG1LbDk1dkx3WTJhMktrMGFqQXpMbEFqdVRTQWJQdFA3Q0NER1oxSyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvYXNzZXQtcmVxdWVzdHMvMyI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MztzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJGhsVTdRS25VYldwaHZKbkZ4WWdhWS5nSlZ6Qm54VktLVi5ranc3eEI1T2guMWpOMGNmQTYyIjt9', 1768151680),
('jP1I2TOWtdWno8bbvYqKVkAET3g6ESzTyUnZ70Kt', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiRGdtaHRzZERVZjRxRHpzVnplbEJKdFhWVVdLaUVlZ2VtTnN2UEFGRSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvYWRtaW4vaW52ZW50b3J5LzIiO3M6NToicm91dGUiO047fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRmdGdHUmhKT1BvLjVGV215YmVMTHYuWjh3WDgvSC5CaWZaTlpzSzUxdVFJc0pIVUIvay5oNiI7fQ==', 1768156759),
('ZVodEOlr7Pmj6HHK4wyBhnrEUbNCGGQEc4jbIg82', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZlpOS2VhVllKdFQ1a3VXUjlBZmdyaUIzakpTMXMzVjFZdnFqeDdoSCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvcHJvcGVydHktaXNzdWFuY2VzLzIiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRmdGdHUmhKT1BvLjVGV215YmVMTHYuWjh3WDgvSC5CaWZaTlpzSzUxdVFJc0pIVUIvay5oNiI7fQ==', 1768199789);

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
(51, 3, 'Trusted Device', '60e9d74b1d471b38ad962bf4cde63e18a6d61029137a5e0530c1dda35ade70e5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '127.0.0.1', NULL, '2026-02-10 23:06:54', '2026-01-11 23:06:54', NULL),
(52, 1, 'Trusted Device', 'f6ef5da47b0aeded99834c5cdd47f50890c09839a03c5c5e395dd83a3f74976b', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-10 23:08:12', '2026-01-11 23:08:12', NULL),
(53, 1, 'Trusted Device', '07e1529a61c959fcfebc46880decd051332d3c318b920eb2b9ffcb6111de4fab', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-10 23:15:21', '2026-01-11 23:15:21', NULL),
(54, 1, 'Trusted Device', '1e3ed13173ada47dd317d1a638f093e3d820f88df940bbafe94586cebc7459f0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-01-12 14:05:38', '2026-02-10 23:18:33', '2026-01-11 23:18:33', '2026-01-12 14:05:38');

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

INSERT INTO `user` (
  `UserID`,
  `UserRoleID`,
  `DepartmentID`,
  `Username`,
  `Email`,
  `EmailVerifiedAt`,
  `ActivationTokenHash`,
  `ActivationSentAt`,
  `ActivationExpiresAt`,
  `Password`,
  `MustChangePassword`,
  `FullName`,
  `IsActive`,
  `MFAEnabled`,
  `MFASecret`,
  `BackupCodes`,
  `CreatedDate`,
  `UpdatedDate`
) VALUES
(1, 1, 1, 'gso.admin', 'micarte.aaron08@gmail.com', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$ftgGRhJOPo.5FWmybeLLv.Z8wX8/H.BifZNZsK51uQIsJHUB/k.h6', 0, 'GSO Administrator', 1, 1, 'eyJpdiI6IndTcFYzbnlmQjd5R2dnaEk1dzNNV1E9PSIsInZhbHVlIjoiaVZKanNlUDFSS20vSXJrblptbC9DMFlnTnVJVWpsQ1ZyaTkxTDZOVjViQT0iLCJtYWMiOiI0YWMwODkzOGRmZmExNzBjMWVkNDRmNGQzYWIyMDNkNGY0NWQ1MzNmM2VmNWQzYjE0YzJmMWY3OWRhOTViYTNhIiwidGFnIjoiIn0=', 'eyJpdiI6IitaZWR2a2JoQk5UWHREbXBDOWVPdWc9PSIsInZhbHVlIjoiTEVwTVB5K0xoK000c3pFdHdWOGpnNnBtQ1k2WWsreVZLb1BjMWh3dzlscmJlWCthVWxPR3ZhNkx1VWNTZXQrUlAwNytQVUxpM1dyM1VacFc2TW9hR24zT3V0NC8vaXJJeFFMdERzVnpZNUVKTkdkdUdRWUZ4QldzNElqSVhCdE5BUlhVVnBVTGRIQ0YyeFh4d3F1RDRqZy92eGZpeURrc1ZnRm90QWM1dFpRPSIsIm1hYyI6ImQ2YjlkMDg3ZGZmNmUwZTM1ZTI2ZmZiYzk0NzU0MWU2ZTIyMjk3ZGE0YmZiYWNlYTRmYTU1ZTVmOTVhZDAzOTciLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2025-12-08 17:51:44'),
(2, 2, 1, 'gso.staff.aaronmicarte', 'aaron.micarte@gso.gov.ph', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$YU66h83wjMCOJ9LMN81o8OuWpIuFYTFQpHCxkuWDAurXbYJS9Q.Mu', 0, 'Aaron Micarte', 1, 1, 'eyJpdiI6IjlHZGd3c0dKcmxwcnBLNEdhN2pkWVE9PSIsInZhbHVlIjoiRkd3RWh6SUczdmt3NnpVU0lhUnVGazNSSmdCYkU3OCtLZzA3T1RPZW1BST0iLCJtYWMiOiI3NjliN2M0MDBjZDE4N2YyODcxMWVkMjNjODdiMmVlMDcwZDEwYWIzYTk3NzFhMDVkMjY1MTQzMDM4Y2Y0OThkIiwidGFnIjoiIn0=', 'eyJpdiI6InRLc2pkQjlRVHpZd2ZzKzZ3c0RCRmc9PSIsInZhbHVlIjoiZUlXMnFlWFZ0dkNtZmUxMVVON0VUa3lXOG1EWDNmVjdrRFUxN09TUElBR2Q1K1NNeEF1WHJjTFMyN3dNbG01alUxMGN1b0l4cE5qQmRPL1BUNGxPRU00VGFOaFVtNEd2SWpJTnlEYmhLbTFrVEk0NU51UDY1S2Z4K2RCOHhxbGVyZnkyU1lXbkp2QXFPNlZWNCtwZ0lndHVyd2ljeW4wSUdObktkajJuVkpnPSIsIm1hYyI6ImU0NDM1MWY0YzZkYTYyZmMwY2QzYWM1M2ZiYmMwZTE4MzY0NzM2OWU0YzU2YTg3ODljOWY1NzhiNWVkNWEzNWQiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2025-12-26 01:48:44'),
(3, 4, 5, 'IT_Head_Chokoy', 'custodian@gso.gov.ph', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$hlU7QKnUbWphvJnFxYgaY.gJVzBnxVKKV.kjw7xB5Oh.1jN0cfA62', 0, 'Chokoy Covid', 1, 1, 'eyJpdiI6InZzeDUwTzBpVVp5VHczZGhUUFhJSWc9PSIsInZhbHVlIjoieEZvVFpWV3JZMmFYRXJxUEZpckhpVXJwcGJaTExzMDJLaUU4MzEyeW9wVT0iLCJtYWMiOiJmYzdkMjc2YzU2MTEzYjBhMjc1NmFiNTI1NzU3ZWYxOTI2ZjBjOWYxYzMyZmI2MmU2MTUxNGFjOGY4OWY3ODFjIiwidGFnIjoiIn0=', 'eyJpdiI6IldkRlBLdlErenZzdXZjR1NRRTYrdUE9PSIsInZhbHVlIjoiRUMrVWtMSlAvTEY0cEZSQ3NyY2JBUDBsT0ZkUS81NUplTlU0b2dwWGVzZjBlUURDVUFrUWlhQWhrSjYzNXlINTd0a2srT2pOellXZXlnbjNMNno3VVV6b21GNUdoQVFBR01LclFnZTdFM1lOQmJCb2FsTy8wQjRFNkYvckFzL1JmSFV5NmtwNnd6M0ZlQktwYWtQT3ZUUWhiR2owQmdheVRxU0d3RkQ2UURFPSIsIm1hYyI6Ijg5MWZkYzdkNTlkM2MxMDkxMzdjYTIyMWU5ZjgxZTcwMmQwZDMxZThmZGEwNzA4ZjFlNWU4ZjE1ZTU0YTliMjQiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-01-11 23:06:37');

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
  MODIFY `RequestID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `assetrequestdetail`
--
ALTER TABLE `assetrequestdetail`
  MODIFY `RequestDetailID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  MODIFY `AssignmentID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `asset_audit_logs`
--
ALTER TABLE `asset_audit_logs`
  MODIFY `AuditID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `asset_request_signatures`
--
ALTER TABLE `asset_request_signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `assignedproperty`
--
ALTER TABLE `assignedproperty`
  MODIFY `PropertyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `EndUserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `IARID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventorycustodianslip`
--
ALTER TABLE `inventorycustodianslip`
  MODIFY `ICSID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loginaudit`
--
ALTER TABLE `loginaudit`
  MODIFY `LoginAuditID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `par`
--
ALTER TABLE `par`
  MODIFY `PARID` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `IssuanceDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `QRCodeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
