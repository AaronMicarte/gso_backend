-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2026 at 06:57 AM
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
  `ArticleImage` varchar(255) DEFAULT NULL,
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

INSERT INTO `articles` (`ArticleID`, `ArticleName`, `Description`, `ArticleImage`, `PropertyTypeID`, `DefaultUnitOfMeasureID`, `IsActive`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Desktop Computer', 'Non-expendable desktop computer hardware', NULL, 7, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(2, 'Laptop Computer', 'Non-expendable laptop computer', NULL, 7, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(3, 'Printer/Scanner', 'Non-expendable printing and scanning equipment', NULL, 7, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(4, 'Office Desk', 'Non-expendable office furniture - desk', NULL, 6, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(5, 'Office Chair', 'Non-expendable office furniture - chair', NULL, 6, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(6, 'Filing Cabinet', 'Non-expendable office storage furniture', NULL, 6, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(7, 'Air Conditioner', 'Non-expendable cooling equipment', NULL, 8, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(8, 'Projector', 'Non-expendable presentation equipment', NULL, 7, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(9, 'Telephone Set', 'Non-expendable communication equipment', NULL, 2, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(10, 'Server Equipment', 'Non-expendable server hardware', NULL, 7, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(11, 'Calculator', 'Semi-expendable calculation device', NULL, 11, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(12, 'Stapler', 'Semi-expendable office tool', NULL, 11, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(13, 'Hole Puncher', 'Semi-expendable office tool', NULL, 11, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(14, 'Office Supplies Set', 'Semi-expendable office supplies bundle', NULL, 11, 3, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(15, 'Table Fan', 'Semi-expendable ventilation equipment', NULL, 12, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(16, 'Extension Cord', 'Semi-expendable electrical accessory', NULL, 12, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(17, 'Office Lamp', 'Semi-expendable lighting equipment', NULL, 11, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(18, 'Waste Basket', 'Semi-expendable office accessory', NULL, 6, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(19, 'Clock/Watch', 'Semi-expendable timekeeping device', NULL, 11, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(20, 'Hand Tools Set', 'Semi-expendable maintenance tools', NULL, 12, 3, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL);

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
(5, 'inventory_detail', 5, 'created_from_dr', 1, '{\"inventory_id\":5,\"dr_detail_id\":98,\"property_number\":\"2026-000001\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 10:11:05'),
(6, 'delivery_receipt', 40, 'created', 1, '{\"dr_number\":\"dr21\",\"supplier\":\"Cyron Suppliers\",\"details\":[98]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 10:11:05'),
(7, 'delivery_receipt_detail', 98, 'created', 1, '{\"dr_id\":40,\"dr_number\":\"dr21\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 10:11:05'),
(8, 'inventory_detail', 6, 'created_from_dr', 1, '{\"inventory_id\":6,\"dr_detail_id\":99,\"property_number\":\"2026-000002\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 11:27:57'),
(9, 'delivery_receipt', 41, 'created', 1, '{\"dr_number\":\"dr22\",\"supplier\":\"Los Pollos Hermanos\",\"details\":[99]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 11:27:57'),
(10, 'delivery_receipt_detail', 99, 'created', 1, '{\"dr_id\":41,\"dr_number\":\"dr22\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 11:27:57'),
(11, 'property_transfer', 1, 'created', 3, '{\"transfer_number\":\"PTR-2026-0001\",\"from_department_id\":23,\"to_department_id\":30,\"items\":[6]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 12:53:37'),
(12, 'inventory_detail', 6, 'transfer_requested', 3, '{\"transfer_id\":1,\"transfer_number\":\"PTR-2026-0001\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 12:53:37'),
(13, 'property_transfer', 1, 'releasing_approved', 6, '{\"transfer_number\":\"PTR-2026-0001\",\"requested_count\":1,\"approved_count\":1,\"approved_quantities\":{\"1\":1},\"reason\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 12:55:49'),
(14, 'inventory_detail', 6, 'transfer_releasing_approved', 6, '{\"transfer_id\":1,\"transfer_number\":\"PTR-2026-0001\",\"approved_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 12:55:49'),
(15, 'property_transfer', 1, 'approved', 1, '{\"transfer_number\":\"PTR-2026-0001\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 12:56:48'),
(16, 'inventory_detail', 6, 'transfer_approved', 1, '{\"transfer_id\":1,\"transfer_number\":\"PTR-2026-0001\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 12:56:48'),
(17, 'property_transfer', 1, 'releasing_signed', 1, '{\"transfer_number\":\"PTR-2026-0001\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 13:02:47'),
(18, 'inventory_detail', 6, 'transfer_releasing_signed', 1, '{\"transfer_id\":1,\"transfer_number\":\"PTR-2026-0001\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 13:02:47'),
(19, 'property_transfer', 1, 'receiving_signed', 3, '{\"transfer_number\":\"PTR-2026-0001\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 13:03:15'),
(20, 'inventory_detail', 6, 'transfer_receiving_signed', 3, '{\"transfer_id\":1,\"transfer_number\":\"PTR-2026-0001\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 13:03:15'),
(21, 'property_transfer_item', 1, 'transferred', 1, '{\"transfer_id\":1,\"transfer_number\":\"PTR-2026-0001\",\"inventory_detail_id\":6,\"from_department_id\":23,\"to_department_id\":30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 13:04:32'),
(22, 'inventory_detail', 6, 'transfer_completed', 1, '{\"transfer_id\":1,\"transfer_number\":\"PTR-2026-0001\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 13:04:32'),
(23, 'property_transfer', 1, 'completed', 1, '{\"transfer_number\":\"PTR-2026-0001\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 13:04:32'),
(24, 'property_borrow', 1, 'created', 6, '{\"borrow_number\":\"PBR-2026-0001\",\"from_department_id\":30,\"to_department_id\":23,\"due_date\":\"2026-03-26\",\"items\":[5]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 13:18:19'),
(25, 'inventory_detail', 5, 'borrow_requested', 6, '{\"borrow_id\":1,\"borrow_number\":\"PBR-2026-0001\",\"from_department_id\":30,\"to_department_id\":23,\"from_end_user_id\":2,\"to_end_user_id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 13:18:19'),
(26, 'property_borrow', 1, 'releasing_approved', 3, '{\"borrow_number\":\"PBR-2026-0001\",\"requested_count\":1,\"approved_count\":1,\"reason\":\"ok\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-18 13:19:45'),
(27, 'property_borrow', 1, 'approved', 1, '{\"borrow_number\":\"PBR-2026-0001\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 13:20:42'),
(28, 'inventory_detail', 5, 'borrow_approved', 1, '{\"borrow_id\":1,\"borrow_number\":\"PBR-2026-0001\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 13:20:42'),
(29, 'property_borrow', 1, 'borrowed', 1, '{\"borrow_number\":\"PBR-2026-0001\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 13:20:42'),
(30, 'property_return', 1, 'created', 1, '{\"borrow_id\":1,\"borrow_number\":\"PBR-2026-0001\",\"items_returned\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 13:25:11'),
(31, 'property_borrow', 1, 'returned', 1, '{\"borrow_number\":\"PBR-2026-0001\",\"items_returned\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 13:25:11'),
(32, 'inventory_detail', 5, 'borrow_returned', 1, '{\"borrow_id\":1,\"borrow_number\":\"PBR-2026-0001\",\"return_id\":1,\"quantity\":1,\"condition_id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 13:25:11');

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
(5, 5, 2, 30, 'PAR-2026-0001', 'smsmsmsm', '2026-03-18', 2, 1, 'Auto-created from DR inspection', '2026-03-18 18:11:05', '2026-03-18 19:44:26'),
(6, 6, 2, 30, 'PTR-2026-0001', 'bugnaw', '2026-03-18', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-18 19:27:57', '2026-03-18 21:04:32');

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
  `IARNumber` varchar(255) DEFAULT NULL COMMENT 'Auto-generated IAR number for tracking',
  `SupplierID` int(11) DEFAULT NULL,
  `RequestedBy` int(11) DEFAULT NULL,
  `PRNumber` varchar(50) DEFAULT NULL,
  `PONumber` varchar(50) DEFAULT NULL COMMENT 'Manual PO Number reference (optional)',
  `InvoiceNumber` varchar(50) DEFAULT NULL COMMENT 'Invoice number from supplier',
  `ReceivedBy` int(11) NOT NULL,
  `InspectedBy` int(10) UNSIGNED DEFAULT NULL,
  `DateReceived` date NOT NULL,
  `DateInspected` date DEFAULT NULL,
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

INSERT INTO `deliveryreceipt` (`DRID`, `DRNumber`, `IARNumber`, `SupplierID`, `RequestedBy`, `PRNumber`, `PONumber`, `InvoiceNumber`, `ReceivedBy`, `InspectedBy`, `DateReceived`, `DateInspected`, `Remarks`, `InspectionStatus`, `InspectionCompletedAt`, `dr_picture`, `CreatedDate`, `deleted_at`) VALUES
(29, 'DR-2026-03-18-001', 'IAR-2026-03-18-001', 1, 3, 'PR-2026-CCE-1001', 'PO-2026-1001', 'INV-2026-1001', 1, 7, '2026-03-10', '2026-03-10', 'Batch 1 office and ICT assets for operational rollout.', 'inspected', '2026-03-18 09:54:47', 'uploads/dr/dr-2026-03-18-001.jpg', '2026-03-18 17:54:47', NULL),
(30, 'DR-2026-03-18-003', 'IAR-2026-03-18-003', 2, 6, 'PR-2026-CCE-1003', 'PO-2026-1003', 'INV-2026-1003', 1, 7, '2026-03-14', '2026-03-14', 'Batch 3 admin support assets.', 'inspected', '2026-03-18 09:54:47', 'uploads/dr/dr-2026-03-18-003.jpg', '2026-03-18 17:54:47', NULL),
(31, 'DR-2026-03-18-002', 'IAR-2026-03-18-002', 3, 3, 'PR-2026-CCE-1002', 'PO-2026-1002', 'INV-2026-1002', 1, 7, '2026-03-12', '2026-03-12', 'Batch 2 mixed furniture and equipment.', 'inspected', '2026-03-18 09:54:47', 'uploads/dr/dr-2026-03-18-002.jpg', '2026-03-18 17:54:47', NULL),
(32, 'DR-2026-03-18-004', 'IAR-2026-03-18-004', 4, 6, 'PR-2026-CCE-1004', 'PO-2026-1004', 'INV-2026-1004', 1, 7, '2026-03-16', '2026-03-16', 'Batch 4 additional non-expendable units.', 'inspected', '2026-03-18 09:54:47', 'uploads/dr/dr-2026-03-18-004.jpg', '2026-03-18 17:54:47', NULL),
(40, 'dr21', 'IAR-2026-0005', 3, 3, '1', '1', '1', 1, 1, '2026-03-18', '2026-03-18', 'sad', 'inspected', '2026-03-18 10:11:05', 'dr_pictures/ZCC8NMHmpSaSUl9O6WbxtQZIm9wYgBnaQNi0mXe9.png', '2026-03-18 18:11:05', NULL),
(41, 'dr22', 'IAR-2026-0001', 2, 6, '3', '3', '3', 1, 1, '2026-03-18', '2026-03-18', 'we', 'inspected', '2026-03-18 11:27:56', 'dr_pictures/gHOqAKCuNMHyVT13glRYsaes5wMmO3pAGIARLY4l.jpg', '2026-03-18 19:27:56', NULL);

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
  `QuantityRequested` int(11) DEFAULT NULL,
  `ChangeReason` varchar(255) DEFAULT NULL,
  `UnitOfMeasureID` int(11) NOT NULL DEFAULT 1,
  `UnitCost` decimal(18,2) DEFAULT NULL,
  `TotalCost` decimal(18,2) DEFAULT NULL,
  `InspectionStatus` enum('pending','inspected') NOT NULL DEFAULT 'pending',
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deliveryreceiptdetail`
--

INSERT INTO `deliveryreceiptdetail` (`DRDetailID`, `DRID`, `ArticleID`, `ItemDescription`, `Brand`, `ModelNumber`, `SerialNumber`, `PropertyTypeID`, `QuantityDelivered`, `QuantityRequested`, `ChangeReason`, `UnitOfMeasureID`, `UnitCost`, `TotalCost`, `InspectionStatus`, `deleted_at`) VALUES
(63, 29, 4, 'Office Desk (OD-PRM-120)', 'Prime Office', 'OD-PRM-120', NULL, 6, 4, 4, NULL, 2, 8500.00, 34000.00, 'inspected', NULL),
(64, 29, 5, 'Office Chair (CH-ERG-22)', 'ErgoSeat', 'CH-ERG-22', NULL, 6, 8, 8, NULL, 2, 3200.00, 25600.00, 'inspected', NULL),
(65, 29, 6, 'Filing Cabinet (FC-STL-4D)', 'SteelSafe', 'FC-STL-4D', NULL, 6, 2, 2, NULL, 2, 11500.00, 23000.00, 'inspected', NULL),
(66, 29, 1, 'Desktop Computer (ThinkCentre-M70)', 'Lenovo', 'ThinkCentre-M70', NULL, 7, 6, 6, NULL, 2, 42500.00, 255000.00, 'inspected', NULL),
(67, 29, 3, 'Printer/Scanner (DCP-L5650DN)', 'Brother', 'DCP-L5650DN', NULL, 7, 2, 2, NULL, 2, 18900.00, 37800.00, 'inspected', NULL),
(68, 29, 9, 'Telephone Set (KX-TS880)', 'Panasonic', 'KX-TS880', 'TEL-2026-001', 2, 1, 1, NULL, 2, 2350.00, 2350.00, 'inspected', NULL),
(69, 31, 2, 'Laptop Computer (Latitude-5440)', 'Dell', 'Latitude-5440', 'LTP-2026-001', 7, 1, 1, NULL, 2, 52000.00, 52000.00, 'inspected', NULL),
(70, 31, 8, 'Projector (EB-X06)', 'Epson', 'EB-X06', 'PROJ-2026-001', 7, 1, 1, NULL, 2, 28500.00, 28500.00, 'inspected', NULL),
(71, 31, 7, 'Air Conditioner (Aura-1.5HP)', 'Carrier', 'Aura-1.5HP', NULL, 8, 4, 4, NULL, 2, 33800.00, 135200.00, 'inspected', NULL),
(72, 31, 4, 'Office Desk (OD-PRM-140)', 'Prime Office', 'OD-PRM-140', NULL, 6, 3, 3, NULL, 2, 8400.00, 25200.00, 'inspected', NULL),
(73, 31, 5, 'Office Chair (CH-ERG-24)', 'ErgoSeat', 'CH-ERG-24', NULL, 6, 6, 6, NULL, 2, 3150.00, 18900.00, 'inspected', NULL),
(74, 31, 15, 'Table Fan (TF-16X)', 'Asahi', 'TF-16X', NULL, 12, 5, 5, NULL, 2, 2450.00, 12250.00, 'inspected', NULL),
(75, 30, 1, 'Desktop Computer (ProDesk-400)', 'HP', 'ProDesk-400', NULL, 7, 4, 4, NULL, 2, 43800.00, 175200.00, 'inspected', NULL),
(76, 30, 10, 'Server Equipment (PowerEdge-R250)', 'Dell EMC', 'PowerEdge-R250', 'SRV-2026-001', 7, 1, 1, NULL, 2, 165000.00, 165000.00, 'inspected', NULL),
(77, 30, 3, 'Printer/Scanner (MF273dw)', 'Canon', 'MF273dw', NULL, 7, 3, 3, NULL, 2, 17200.00, 51600.00, 'inspected', NULL),
(78, 30, 6, 'Filing Cabinet (FC-STL-3D)', 'SteelSafe', 'FC-STL-3D', NULL, 6, 2, 2, NULL, 2, 11200.00, 22400.00, 'inspected', NULL),
(79, 30, 9, 'Telephone Set (KX-TS500)', 'Panasonic', 'KX-TS500', NULL, 2, 4, 4, NULL, 2, 2290.00, 9160.00, 'inspected', NULL),
(80, 30, 11, 'Calculator (DJ-120D)', 'Casio', 'DJ-120D', NULL, 11, 10, 10, NULL, 2, 650.00, 6500.00, 'inspected', NULL),
(81, 32, 2, 'Laptop Computer (TravelMate-P2)', 'Acer', 'TravelMate-P2', 'LTP-2026-002', 7, 1, 1, NULL, 2, 49900.00, 49900.00, 'inspected', NULL),
(82, 32, 17, 'Office Lamp (OFL-9W)', 'Firefly', 'OFL-9W', NULL, 11, 12, 12, NULL, 2, 980.00, 11760.00, 'inspected', NULL),
(83, 32, 16, 'Extension Cord (EXT-5S)', 'Royu', 'EXT-5S', NULL, 12, 10, 10, NULL, 2, 560.00, 5600.00, 'inspected', NULL),
(84, 32, 20, 'Hand Tools Set (HTS-112)', 'Ingco', 'HTS-112', NULL, 12, 3, 3, NULL, 3, 4200.00, 12600.00, 'inspected', NULL),
(85, 32, 18, 'Waste Basket (WB-18L)', 'Omni', 'WB-18L', NULL, 6, 10, 10, NULL, 2, 420.00, 4200.00, 'inspected', NULL),
(86, 32, 8, 'Projector (MS560)', 'BenQ', 'MS560', 'PROJ-2026-002', 7, 1, 1, NULL, 2, 27900.00, 27900.00, 'inspected', NULL),
(98, 40, 8, 'smsmsmsm', 's', 's', NULL, 7, 1, 1, NULL, 2, 100.00, 100.00, 'inspected', NULL),
(99, 41, 7, 'bugnaw', 'he', 'hah', NULL, 8, 1, 1, NULL, 2, 2333.00, 2333.00, 'inspected', NULL);

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
(23, 'Accounting', NULL, 'ACCOUNTING', 1, '2026-02-25 15:21:25', NULL, NULL),
(24, 'Admin', NULL, 'ADMIN', 1, '2026-02-25 15:21:25', NULL, NULL),
(25, 'Agriculture', NULL, 'AGRICULTURE', 1, '2026-02-25 15:21:25', NULL, NULL),
(26, 'Assessor', NULL, 'ASSESSOR', 1, '2026-02-25 15:21:26', NULL, NULL),
(27, 'BAC', NULL, 'BAC', 1, '2026-02-25 15:21:26', NULL, NULL),
(28, 'BFP', NULL, 'BFP', 1, '2026-02-25 15:21:26', NULL, NULL),
(30, 'BJMP', NULL, 'BJMP', 1, '2026-02-25 15:21:26', NULL, NULL),
(31, 'BPLO', NULL, 'BPLO', 1, '2026-02-25 15:21:26', NULL, NULL),
(32, 'Budget', NULL, 'BUDGET', 1, '2026-02-25 15:21:26', NULL, NULL),
(33, 'CCE', NULL, 'CCE', 1, '2026-02-25 15:21:26', NULL, NULL),
(34, 'CCR', NULL, 'CCR', 1, '2026-02-25 15:21:26', NULL, NULL),
(35, 'CDRRMO', NULL, 'CDRRMO', 1, '2026-02-25 15:21:26', NULL, NULL),
(36, 'CEEDO', NULL, 'CEEDO', 1, '2026-02-25 15:21:26', NULL, NULL),
(37, 'CESHUDO', NULL, 'CESHUDO', 1, '2026-02-25 15:21:26', NULL, NULL),
(38, 'CLENRO', NULL, 'CLENRO', 1, '2026-02-25 15:21:26', NULL, NULL),
(39, 'COA', NULL, 'COA', 1, '2026-02-25 15:21:26', NULL, NULL),
(40, 'COMELEC', NULL, 'COMELEC', 1, '2026-02-25 15:21:26', NULL, NULL),
(41, 'CPDC', NULL, 'CPDC', 1, '2026-02-25 15:21:26', NULL, NULL),
(42, 'CSWDO', NULL, 'CSWDO', 1, '2026-02-25 15:21:26', NULL, NULL),
(43, 'OSCA', NULL, 'OSCA', 1, '2026-02-25 15:21:26', NULL, NULL),
(44, 'PDAW', NULL, 'PDAW', 1, '2026-02-25 15:21:26', NULL, NULL),
(45, 'DEPED', NULL, 'DEPED', 1, '2026-02-25 15:21:26', NULL, NULL),
(46, 'DILG', NULL, 'DILG', 1, '2026-02-25 15:21:26', NULL, NULL),
(47, 'Engineering', NULL, 'ENGINEERING', 1, '2026-02-25 15:21:26', NULL, NULL),
(48, 'FM Radio', NULL, 'FM_RADIO', 1, '2026-02-25 15:21:26', NULL, NULL),
(49, 'GSO', NULL, 'GSO', 1, '2026-02-25 15:21:26', NULL, NULL),
(50, 'Park and Plaza', NULL, 'PARK_AND_PLAZA', 1, '2026-02-25 15:21:26', NULL, NULL),
(51, 'Sound System', NULL, 'SOUND_SYSTEM', 1, '2026-02-25 15:21:26', NULL, NULL),
(52, 'Gym', NULL, 'GYM', 1, '2026-02-25 15:21:26', NULL, NULL),
(53, 'Health Office', NULL, 'HEALTH_OFFICE', 1, '2026-02-25 15:21:26', NULL, NULL),
(54, 'HRMO', NULL, 'HRMO', 1, '2026-02-25 15:21:26', NULL, NULL),
(55, 'IAS', NULL, 'IAS', 1, '2026-02-25 15:21:26', NULL, NULL),
(56, 'Legal Office', NULL, 'LEGAL_OFFICE', 1, '2026-02-25 15:21:26', NULL, NULL),
(58, 'Mayor\'s Office', NULL, 'MAYORS_OFFICE', 1, '2026-02-25 15:21:26', NULL, NULL),
(59, 'MCTC', NULL, 'MCTC', 1, '2026-02-25 15:21:26', NULL, NULL),
(60, 'Motorpool Nutrition', NULL, 'MOTORPOOL_NUTRITION', 1, '2026-02-25 15:21:26', NULL, NULL),
(61, 'OCBO', NULL, 'OCBO', 1, '2026-02-25 15:21:26', NULL, NULL),
(62, 'PESO', NULL, 'PESO', 1, '2026-02-25 15:21:26', NULL, NULL),
(63, 'PNP', NULL, 'PNP', 1, '2026-02-25 15:21:26', NULL, NULL),
(64, 'POPCOM', NULL, 'POPCOM', 1, '2026-02-25 15:21:26', NULL, NULL),
(65, 'Postal', NULL, 'POSTAL', 1, '2026-02-25 15:21:26', NULL, NULL),
(66, 'Prosecutor', NULL, 'PROSECUTOR', 1, '2026-02-25 15:21:26', NULL, NULL),
(67, 'Sanitary', NULL, 'SANITARY', 1, '2026-02-25 15:21:26', NULL, NULL),
(68, 'SP', NULL, 'SP', 1, '2026-02-25 15:21:26', NULL, NULL),
(69, 'TIMU', NULL, 'TIMU', 1, '2026-02-25 15:21:26', NULL, NULL),
(70, 'Tourism', NULL, 'TOURISM', 1, '2026-02-25 15:21:26', NULL, NULL),
(71, 'Treasury', NULL, 'TREASURY', 1, '2026-02-25 15:21:26', NULL, NULL),
(72, 'Veterinary', NULL, 'VETERINARY', 1, '2026-02-25 15:21:26', NULL, NULL),
(73, 'Vice Mayor\'s Office', NULL, 'VICE_MAYORS_OFFICE', 1, '2026-02-25 15:21:26', NULL, NULL),
(74, 'Waterworks', NULL, 'WATERWORKS', 1, '2026-02-25 15:21:26', NULL, NULL),
(75, 'C1', NULL, 'C1', 1, '2026-02-25 15:21:26', NULL, NULL),
(76, 'C2', NULL, 'C2', 1, '2026-02-25 15:21:26', NULL, NULL),
(77, 'C3', NULL, 'C3', 1, '2026-02-25 15:21:26', NULL, NULL),
(78, 'C4', NULL, 'C4', 1, '2026-02-25 15:21:26', NULL, NULL),
(79, 'C5', NULL, 'C5', 1, '2026-02-25 15:21:26', NULL, NULL),
(80, 'C6', NULL, 'C6', 1, '2026-02-25 15:21:26', NULL, NULL),
(81, 'C7', NULL, 'C7', 1, '2026-02-25 15:21:26', NULL, NULL),
(82, 'C8', NULL, 'C8', 1, '2026-02-25 15:21:26', NULL, NULL),
(83, 'C9', NULL, 'C9', 1, '2026-02-25 15:21:26', NULL, NULL),
(84, 'C10', NULL, 'C10', 1, '2026-02-25 15:21:26', NULL, NULL),
(85, 'K', NULL, 'K', 1, '2026-02-25 15:21:26', NULL, NULL),
(86, 'ABC', NULL, 'ABC', 1, '2026-02-25 15:21:26', NULL, NULL),
(87, 'BLDG MNT', NULL, 'BLDG_MNT', 1, '2026-02-25 15:21:26', NULL, NULL),
(89, 'NBI', NULL, 'NBI', 1, '2026-02-25 15:21:26', NULL, NULL),
(90, 'ECCO', NULL, 'ECCO', 1, '2026-02-25 15:21:26', NULL, NULL),
(91, 'Poblacion', NULL, 'POBLACION', 1, '2026-02-25 15:21:26', NULL, NULL),
(92, 'Molugan', NULL, 'MOLUGAN', 1, '2026-02-25 15:21:26', NULL, NULL),
(93, 'Amoros', NULL, 'AMOROS', 1, '2026-02-25 15:21:26', NULL, NULL),
(94, 'Sambulawan', NULL, 'SAMBULAWAN', 1, '2026-02-25 15:21:26', NULL, NULL),
(95, 'Taytay', NULL, 'TAYTAY', 1, '2026-02-25 15:21:26', NULL, NULL),
(96, 'Himaya', NULL, 'HIMAYA', 1, '2026-02-25 15:21:26', NULL, NULL),
(97, 'Ulaliman', NULL, 'ULALIMAN', 1, '2026-02-25 15:21:26', NULL, NULL),
(98, 'Bolobolo (Pedro S Baculio)', NULL, 'BOLOBOLO_PEDRO_S', 1, '2026-02-25 15:21:26', NULL, NULL),
(99, 'Sinaloc', NULL, 'SINALOC', 1, '2026-02-25 15:21:26', NULL, NULL),
(100, 'Calongonan (San Francisco de Asis)', NULL, 'CALONGONAN_SF', 1, '2026-02-25 15:21:26', NULL, NULL),
(101, 'Hinigdaan', NULL, 'HINIGDAAN', 1, '2026-02-25 15:21:26', NULL, NULL),
(102, 'Kalabaylabay', NULL, 'KALABAYLABAY', 1, '2026-02-25 15:21:26', NULL, NULL),
(103, 'Bolsong', NULL, 'BOLSONG', 1, '2026-02-25 15:21:26', NULL, NULL),
(104, 'Quibonbon', NULL, 'QUIBONBON', 1, '2026-02-25 15:21:26', NULL, NULL),
(105, 'Cogon', NULL, 'COGON', 1, '2026-02-25 15:21:26', NULL, NULL),
(106, 'BIR', NULL, 'BIR', 1, '2026-02-25 16:01:51', NULL, NULL),
(107, 'Lying-In', NULL, 'LYING_IN', 1, '2026-02-25 16:01:51', NULL, NULL),
(108, 'Market', NULL, 'MARKET', 1, '2026-02-25 16:01:51', NULL, NULL),
(109, 'PAO', NULL, 'PAO', 1, '2026-02-25 16:01:51', NULL, NULL);

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
(2, 2, 30, 'Chokoy Covid', NULL, NULL, 1, '2026-02-13 12:21:18', '2026-02-25 17:12:15', NULL),
(4, 2, 23, 'Cyron Micarte', NULL, NULL, 1, '2026-02-16 21:47:19', '2026-02-25 15:31:42', NULL),
(5, 2, 49, 'GSO Property Custodian', NULL, NULL, 1, '2026-02-17 17:35:35', '2026-02-25 15:31:42', NULL),
(6, 2, 24, 'Admin Office Custodian', NULL, NULL, 1, '2026-02-17 17:35:35', '2026-02-25 15:31:42', NULL),
(7, 2, 54, 'HR Department Custodian', NULL, NULL, 1, '2026-02-17 17:35:35', '2026-02-25 15:31:42', NULL),
(8, 2, 53, 'Health Department Custodian', NULL, NULL, 1, '2026-02-17 17:35:35', '2026-02-25 15:31:42', NULL);

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
(1, 1, '2026-03-18 17:45:44', 'Login', '127.0.0.1'),
(2, 1, '2026-03-18 17:46:07', 'Logout', '127.0.0.1'),
(3, 1, '2026-03-18 17:46:53', 'Login', '127.0.0.1'),
(4, 3, '2026-03-18 20:52:45', 'Login', '127.0.0.1'),
(5, 3, '2026-03-18 20:53:49', 'Logout', '127.0.0.1'),
(6, 6, '2026-03-18 20:53:58', 'FailedLogin', '127.0.0.1'),
(7, 6, '2026-03-18 20:54:09', 'Login', '127.0.0.1'),
(8, 6, '2026-03-18 21:01:41', 'Logout', '127.0.0.1'),
(9, 3, '2026-03-18 21:01:59', 'Login', '127.0.0.1'),
(10, 3, '2026-03-18 21:07:16', 'Logout', '127.0.0.1'),
(11, 6, '2026-03-18 21:07:56', 'Login', '127.0.0.1'),
(12, 6, '2026-03-18 21:18:59', 'Logout', '127.0.0.1'),
(13, 3, '2026-03-18 21:19:17', 'Login', '127.0.0.1'),
(14, 3, '2026-03-18 21:22:01', 'Logout', '127.0.0.1'),
(15, 6, '2026-03-18 21:22:28', 'Login', '127.0.0.1'),
(16, 1, '2026-03-19 10:00:28', 'Login', '127.0.0.1'),
(17, 1, '2026-03-19 18:12:56', 'Login', '127.0.0.1'),
(18, 1, '2026-03-19 18:13:15', 'Logout', '127.0.0.1'),
(19, 1, '2026-03-19 18:14:15', 'Login', '127.0.0.1'),
(20, 1, '2026-03-20 21:43:41', 'Login', '127.0.0.1'),
(21, 3, '2026-03-20 21:48:16', 'Login', '127.0.0.1'),
(22, 1, '2026-03-21 13:56:20', 'Login', '127.0.0.1');

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
(132, '2026_02_17_000010_add_releasing_review_fields_to_propertytransfer', 41),
(133, '2026_02_20_000001_add_property_type_and_unit_to_articles', 42),
(134, '2026_02_23_000001_drop_tin_from_supplier_table', 42),
(135, '2026_02_24_000010_create_property_borrow_tables', 42),
(136, '2026_02_24_000020_create_property_return_tables', 43),
(137, '2026_02_24_000021_add_partially_returned_status_to_propertyborrow', 43),
(138, '2026_02_24_000022_drop_property_borrow_signature_and_return_columns', 44),
(139, '2026_02_24_000023_create_property_return_item_conditions', 45),
(140, '2026_02_24_000024_add_primary_condition_to_return_items', 46),
(141, '2026_02_24_000023_add_condition_to_property_return_items', 47),
(142, '2026_02_25_000001_merge_dr_inspection_fields', 48),
(143, '2026_02_25_000002_simplify_dr_detail_fields', 48),
(144, '2026_02_25_000003_add_supplier_fk_to_deliveryreceipt', 49),
(145, '2026_02_25_000004_update_dr_supplier_unique_indexes', 50),
(146, '2026_03_03_210449_add_iar_number_to_delivery_receipt_table', 51);

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
-- Table structure for table `propertyborrow`
--

CREATE TABLE `propertyborrow` (
  `BorrowID` bigint(20) UNSIGNED NOT NULL,
  `BorrowNumber` varchar(50) NOT NULL,
  `RequestedByUserID` int(11) NOT NULL,
  `FromDepartmentID` int(11) NOT NULL,
  `ToDepartmentID` int(11) NOT NULL,
  `FromEndUserID` int(11) DEFAULT NULL,
  `ToEndUserID` int(11) DEFAULT NULL,
  `Purpose` text DEFAULT NULL,
  `RequestedItemsCount` int(10) UNSIGNED DEFAULT NULL,
  `DueDate` date NOT NULL,
  `ReleasingDecision` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `ReleasingApprovedCount` int(10) UNSIGNED DEFAULT NULL,
  `ReleasingDecisionReason` text DEFAULT NULL,
  `ReleasingReviewedByUserID` int(11) DEFAULT NULL,
  `ReleasingReviewedAt` timestamp NULL DEFAULT NULL,
  `Status` enum('pending','approved','rejected','borrowed','partially_returned','returned','cancelled') NOT NULL DEFAULT 'pending',
  `ApprovedByUserID` int(11) DEFAULT NULL,
  `ApprovedAt` timestamp NULL DEFAULT NULL,
  `BorrowedAt` timestamp NULL DEFAULT NULL,
  `GsoNotes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `propertyborrow`
--

INSERT INTO `propertyborrow` (`BorrowID`, `BorrowNumber`, `RequestedByUserID`, `FromDepartmentID`, `ToDepartmentID`, `FromEndUserID`, `ToEndUserID`, `Purpose`, `RequestedItemsCount`, `DueDate`, `ReleasingDecision`, `ReleasingApprovedCount`, `ReleasingDecisionReason`, `ReleasingReviewedByUserID`, `ReleasingReviewedAt`, `Status`, `ApprovedByUserID`, `ApprovedAt`, `BorrowedAt`, `GsoNotes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PBR-2026-0001', 6, 30, 23, 2, 4, 'Explain the temporary use case.', 1, '2026-03-26', 'approved', 1, 'ok', 3, '2026-03-18 13:19:45', 'returned', 1, '2026-03-18 13:20:42', '2026-03-18 13:20:42', NULL, '2026-03-18 13:18:19', '2026-03-18 13:25:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `propertyborrowitems`
--

CREATE TABLE `propertyborrowitems` (
  `BorrowItemID` bigint(20) UNSIGNED NOT NULL,
  `BorrowID` bigint(20) UNSIGNED NOT NULL,
  `InventoryDetailID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 1,
  `Remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `propertyborrowitems`
--

INSERT INTO `propertyborrowitems` (`BorrowItemID`, `BorrowID`, `InventoryDetailID`, `Quantity`, `Remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 5, 1, NULL, '2026-03-18 13:18:19', '2026-03-18 13:18:19', NULL);

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
(5, NULL, NULL, 40, NULL, '2026-03-18', NULL, 'Auto-ingested from DR #dr21', '2026-03-18 18:11:05', NULL, 'INV-20260318-181105-40', NULL),
(6, NULL, NULL, 41, NULL, '2026-03-18', NULL, 'Auto-ingested from DR #dr22', '2026-03-18 19:27:56', NULL, 'INV-20260318-192756-41', NULL),
(7, NULL, NULL, 29, NULL, '2026-03-10', NULL, 'Backfilled from generated DR #DR-2026-03-18-001', '2026-03-18 19:41:22', NULL, 'INV-20260318-194122-29', NULL),
(8, NULL, NULL, 30, NULL, '2026-03-14', NULL, 'Backfilled from generated DR #DR-2026-03-18-003', '2026-03-18 19:41:22', NULL, 'INV-20260318-194122-30', NULL),
(9, NULL, NULL, 31, NULL, '2026-03-12', NULL, 'Backfilled from generated DR #DR-2026-03-18-002', '2026-03-18 19:41:22', NULL, 'INV-20260318-194122-31', NULL),
(10, NULL, NULL, 32, NULL, '2026-03-16', NULL, 'Backfilled from generated DR #DR-2026-03-18-004', '2026-03-18 19:41:22', NULL, 'INV-20260318-194122-32', NULL);

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
(5, 5, 7, NULL, 'smsmsmsm', '2026-000001', NULL, 1, 0, 2, 2, 100.00, 100.00, NULL, 98, 1, 's', 's', NULL, '2026-03-18 18:11:05', '2026-03-18 19:44:26', NULL),
(6, 6, 8, NULL, 'bugnaw', '2026-000002', NULL, 1, 0, 2, 2, 2333.00, 2333.00, NULL, 99, 1, 'he', 'hah', NULL, '2026-03-18 19:27:56', '2026-03-18 19:44:40', NULL),
(7, 7, 6, NULL, 'Office Desk (OD-PRM-120)', '2026-000003', NULL, 1, 1, 1, 2, 8500.00, 8500.00, NULL, 63, 1, 'Prime Office', 'OD-PRM-120', NULL, '2026-03-18 19:41:22', NULL, NULL),
(8, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000004', NULL, 1, 1, 1, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', NULL, NULL),
(9, 7, 6, NULL, 'Filing Cabinet (FC-STL-4D)', '2026-000005', NULL, 1, 1, 1, 2, 11500.00, 11500.00, NULL, 65, 1, 'SteelSafe', 'FC-STL-4D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(10, 7, 7, NULL, 'Desktop Computer (ThinkCentre-M70)', '2026-000006', NULL, 1, 1, 1, 2, 42500.00, 42500.00, NULL, 66, 1, 'Lenovo', 'ThinkCentre-M70', NULL, '2026-03-18 19:41:22', NULL, NULL),
(11, 7, 7, NULL, 'Printer/Scanner (DCP-L5650DN)', '2026-000007', NULL, 1, 1, 1, 2, 18900.00, 18900.00, NULL, 67, 1, 'Brother', 'DCP-L5650DN', NULL, '2026-03-18 19:41:22', NULL, NULL),
(12, 7, 2, NULL, 'Telephone Set (KX-TS880)', '2026-000008', 'TEL-2026-001', 1, 1, 1, 2, 2350.00, 2350.00, NULL, 68, 1, 'Panasonic', 'KX-TS880', NULL, '2026-03-18 19:41:22', NULL, NULL),
(13, 8, 7, NULL, 'Desktop Computer (ProDesk-400)', '2026-000009', NULL, 1, 1, 1, 2, 43800.00, 43800.00, NULL, 75, 1, 'HP', 'ProDesk-400', NULL, '2026-03-18 19:41:22', NULL, NULL),
(14, 8, 7, NULL, 'Server Equipment (PowerEdge-R250)', '2026-000010', 'SRV-2026-001', 1, 1, 1, 2, 165000.00, 165000.00, NULL, 76, 1, 'Dell EMC', 'PowerEdge-R250', NULL, '2026-03-18 19:41:22', NULL, NULL),
(15, 8, 7, NULL, 'Printer/Scanner (MF273dw)', '2026-000011', NULL, 1, 1, 1, 2, 17200.00, 17200.00, NULL, 77, 1, 'Canon', 'MF273dw', NULL, '2026-03-18 19:41:22', NULL, NULL),
(16, 8, 6, NULL, 'Filing Cabinet (FC-STL-3D)', '2026-000012', NULL, 1, 1, 1, 2, 11200.00, 11200.00, NULL, 78, 1, 'SteelSafe', 'FC-STL-3D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(17, 8, 2, NULL, 'Telephone Set (KX-TS500)', '2026-000013', NULL, 1, 1, 1, 2, 2290.00, 2290.00, NULL, 79, 1, 'Panasonic', 'KX-TS500', NULL, '2026-03-18 19:41:22', NULL, NULL),
(18, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000014', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(19, 9, 7, NULL, 'Laptop Computer (Latitude-5440)', '2026-000015', 'LTP-2026-001', 1, 1, 1, 2, 52000.00, 52000.00, NULL, 69, 1, 'Dell', 'Latitude-5440', NULL, '2026-03-18 19:41:22', NULL, NULL),
(20, 9, 7, NULL, 'Projector (EB-X06)', '2026-000016', 'PROJ-2026-001', 1, 1, 1, 2, 28500.00, 28500.00, NULL, 70, 1, 'Epson', 'EB-X06', NULL, '2026-03-18 19:41:22', NULL, NULL),
(21, 9, 8, NULL, 'Air Conditioner (Aura-1.5HP)', '2026-000017', NULL, 1, 1, 1, 2, 33800.00, 33800.00, NULL, 71, 1, 'Carrier', 'Aura-1.5HP', NULL, '2026-03-18 19:41:22', NULL, NULL),
(22, 9, 6, NULL, 'Office Desk (OD-PRM-140)', '2026-000018', NULL, 1, 1, 1, 2, 8400.00, 8400.00, NULL, 72, 1, 'Prime Office', 'OD-PRM-140', NULL, '2026-03-18 19:41:22', NULL, NULL),
(23, 9, 6, NULL, 'Office Chair (CH-ERG-24)', '2026-000019', NULL, 1, 1, 1, 2, 3150.00, 3150.00, NULL, 73, 1, 'ErgoSeat', 'CH-ERG-24', NULL, '2026-03-18 19:41:22', NULL, NULL),
(24, 9, 12, NULL, 'Table Fan (TF-16X)', '2026-000020', NULL, 1, 1, 1, 2, 2450.00, 2450.00, NULL, 74, 1, 'Asahi', 'TF-16X', NULL, '2026-03-18 19:41:22', NULL, NULL),
(25, 10, 7, NULL, 'Laptop Computer (TravelMate-P2)', '2026-000021', 'LTP-2026-002', 1, 1, 1, 2, 49900.00, 49900.00, NULL, 81, 1, 'Acer', 'TravelMate-P2', NULL, '2026-03-18 19:41:22', NULL, NULL),
(26, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000022', NULL, 1, 1, 1, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', NULL, NULL),
(27, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000023', NULL, 1, 1, 1, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', NULL, NULL),
(28, 10, 12, NULL, 'Hand Tools Set (HTS-112)', '2026-000024', NULL, 1, 1, 1, 3, 4200.00, 4200.00, NULL, 84, 1, 'Ingco', 'HTS-112', NULL, '2026-03-18 19:41:22', NULL, NULL),
(29, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000025', NULL, 1, 1, 1, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', NULL, NULL),
(30, 10, 7, NULL, 'Projector (MS560)', '2026-000026', 'PROJ-2026-002', 1, 1, 1, 2, 27900.00, 27900.00, NULL, 86, 1, 'BenQ', 'MS560', NULL, '2026-03-18 19:41:22', NULL, NULL),
(31, 7, 6, NULL, 'Office Desk (OD-PRM-120)', '2026-000027', NULL, 1, 1, 1, 2, 8500.00, 8500.00, NULL, 63, 1, 'Prime Office', 'OD-PRM-120', NULL, '2026-03-18 19:41:22', NULL, NULL),
(32, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000028', NULL, 1, 1, 1, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', NULL, NULL),
(33, 7, 6, NULL, 'Filing Cabinet (FC-STL-4D)', '2026-000029', NULL, 1, 1, 1, 2, 11500.00, 11500.00, NULL, 65, 1, 'SteelSafe', 'FC-STL-4D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(34, 7, 7, NULL, 'Desktop Computer (ThinkCentre-M70)', '2026-000030', NULL, 1, 1, 1, 2, 42500.00, 42500.00, NULL, 66, 1, 'Lenovo', 'ThinkCentre-M70', NULL, '2026-03-18 19:41:22', NULL, NULL),
(35, 7, 7, NULL, 'Printer/Scanner (DCP-L5650DN)', '2026-000031', NULL, 1, 1, 1, 2, 18900.00, 18900.00, NULL, 67, 1, 'Brother', 'DCP-L5650DN', NULL, '2026-03-18 19:41:22', NULL, NULL),
(36, 8, 7, NULL, 'Desktop Computer (ProDesk-400)', '2026-000032', NULL, 1, 1, 1, 2, 43800.00, 43800.00, NULL, 75, 1, 'HP', 'ProDesk-400', NULL, '2026-03-18 19:41:22', NULL, NULL),
(37, 8, 7, NULL, 'Printer/Scanner (MF273dw)', '2026-000033', NULL, 1, 1, 1, 2, 17200.00, 17200.00, NULL, 77, 1, 'Canon', 'MF273dw', NULL, '2026-03-18 19:41:22', NULL, NULL),
(38, 8, 6, NULL, 'Filing Cabinet (FC-STL-3D)', '2026-000034', NULL, 1, 1, 1, 2, 11200.00, 11200.00, NULL, 78, 1, 'SteelSafe', 'FC-STL-3D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(39, 8, 2, NULL, 'Telephone Set (KX-TS500)', '2026-000035', NULL, 1, 1, 1, 2, 2290.00, 2290.00, NULL, 79, 1, 'Panasonic', 'KX-TS500', NULL, '2026-03-18 19:41:22', NULL, NULL),
(40, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000036', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(41, 9, 8, NULL, 'Air Conditioner (Aura-1.5HP)', '2026-000037', NULL, 1, 1, 1, 2, 33800.00, 33800.00, NULL, 71, 1, 'Carrier', 'Aura-1.5HP', NULL, '2026-03-18 19:41:22', NULL, NULL),
(42, 9, 6, NULL, 'Office Desk (OD-PRM-140)', '2026-000038', NULL, 1, 1, 1, 2, 8400.00, 8400.00, NULL, 72, 1, 'Prime Office', 'OD-PRM-140', NULL, '2026-03-18 19:41:22', NULL, NULL),
(43, 9, 6, NULL, 'Office Chair (CH-ERG-24)', '2026-000039', NULL, 1, 1, 1, 2, 3150.00, 3150.00, NULL, 73, 1, 'ErgoSeat', 'CH-ERG-24', NULL, '2026-03-18 19:41:22', NULL, NULL),
(44, 9, 12, NULL, 'Table Fan (TF-16X)', '2026-000040', NULL, 1, 1, 1, 2, 2450.00, 2450.00, NULL, 74, 1, 'Asahi', 'TF-16X', NULL, '2026-03-18 19:41:22', NULL, NULL),
(45, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000041', NULL, 1, 1, 1, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', NULL, NULL),
(46, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000042', NULL, 1, 1, 1, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', NULL, NULL),
(47, 10, 12, NULL, 'Hand Tools Set (HTS-112)', '2026-000043', NULL, 1, 1, 1, 3, 4200.00, 4200.00, NULL, 84, 1, 'Ingco', 'HTS-112', NULL, '2026-03-18 19:41:22', NULL, NULL),
(48, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000044', NULL, 1, 1, 1, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', NULL, NULL),
(49, 7, 6, NULL, 'Office Desk (OD-PRM-120)', '2026-000045', NULL, 1, 1, 1, 2, 8500.00, 8500.00, NULL, 63, 1, 'Prime Office', 'OD-PRM-120', NULL, '2026-03-18 19:41:22', NULL, NULL),
(50, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000046', NULL, 1, 1, 1, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', NULL, NULL),
(51, 7, 7, NULL, 'Desktop Computer (ThinkCentre-M70)', '2026-000047', NULL, 1, 1, 1, 2, 42500.00, 42500.00, NULL, 66, 1, 'Lenovo', 'ThinkCentre-M70', NULL, '2026-03-18 19:41:22', NULL, NULL),
(52, 8, 7, NULL, 'Desktop Computer (ProDesk-400)', '2026-000048', NULL, 1, 1, 1, 2, 43800.00, 43800.00, NULL, 75, 1, 'HP', 'ProDesk-400', NULL, '2026-03-18 19:41:22', NULL, NULL),
(53, 8, 7, NULL, 'Printer/Scanner (MF273dw)', '2026-000049', NULL, 1, 1, 1, 2, 17200.00, 17200.00, NULL, 77, 1, 'Canon', 'MF273dw', NULL, '2026-03-18 19:41:22', NULL, NULL),
(54, 8, 2, NULL, 'Telephone Set (KX-TS500)', '2026-000050', NULL, 1, 1, 1, 2, 2290.00, 2290.00, NULL, 79, 1, 'Panasonic', 'KX-TS500', NULL, '2026-03-18 19:41:22', NULL, NULL),
(55, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000051', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(56, 9, 8, NULL, 'Air Conditioner (Aura-1.5HP)', '2026-000052', NULL, 1, 1, 1, 2, 33800.00, 33800.00, NULL, 71, 1, 'Carrier', 'Aura-1.5HP', NULL, '2026-03-18 19:41:22', NULL, NULL),
(57, 9, 6, NULL, 'Office Desk (OD-PRM-140)', '2026-000053', NULL, 1, 1, 1, 2, 8400.00, 8400.00, NULL, 72, 1, 'Prime Office', 'OD-PRM-140', NULL, '2026-03-18 19:41:22', NULL, NULL),
(58, 9, 6, NULL, 'Office Chair (CH-ERG-24)', '2026-000054', NULL, 1, 1, 1, 2, 3150.00, 3150.00, NULL, 73, 1, 'ErgoSeat', 'CH-ERG-24', NULL, '2026-03-18 19:41:22', NULL, NULL),
(59, 9, 12, NULL, 'Table Fan (TF-16X)', '2026-000055', NULL, 1, 1, 1, 2, 2450.00, 2450.00, NULL, 74, 1, 'Asahi', 'TF-16X', NULL, '2026-03-18 19:41:22', NULL, NULL),
(60, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000056', NULL, 1, 1, 1, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', NULL, NULL),
(61, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000057', NULL, 1, 1, 1, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', NULL, NULL),
(62, 10, 12, NULL, 'Hand Tools Set (HTS-112)', '2026-000058', NULL, 1, 1, 1, 3, 4200.00, 4200.00, NULL, 84, 1, 'Ingco', 'HTS-112', NULL, '2026-03-18 19:41:22', NULL, NULL),
(63, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000059', NULL, 1, 1, 1, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', NULL, NULL),
(64, 7, 6, NULL, 'Office Desk (OD-PRM-120)', '2026-000060', NULL, 1, 1, 1, 2, 8500.00, 8500.00, NULL, 63, 1, 'Prime Office', 'OD-PRM-120', NULL, '2026-03-18 19:41:22', NULL, NULL),
(65, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000061', NULL, 1, 1, 1, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', NULL, NULL),
(66, 7, 7, NULL, 'Desktop Computer (ThinkCentre-M70)', '2026-000062', NULL, 1, 1, 1, 2, 42500.00, 42500.00, NULL, 66, 1, 'Lenovo', 'ThinkCentre-M70', NULL, '2026-03-18 19:41:22', NULL, NULL),
(67, 8, 7, NULL, 'Desktop Computer (ProDesk-400)', '2026-000063', NULL, 1, 1, 1, 2, 43800.00, 43800.00, NULL, 75, 1, 'HP', 'ProDesk-400', NULL, '2026-03-18 19:41:22', NULL, NULL),
(68, 8, 2, NULL, 'Telephone Set (KX-TS500)', '2026-000064', NULL, 1, 1, 1, 2, 2290.00, 2290.00, NULL, 79, 1, 'Panasonic', 'KX-TS500', NULL, '2026-03-18 19:41:22', NULL, NULL),
(69, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000065', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(70, 9, 8, NULL, 'Air Conditioner (Aura-1.5HP)', '2026-000066', NULL, 1, 1, 1, 2, 33800.00, 33800.00, NULL, 71, 1, 'Carrier', 'Aura-1.5HP', NULL, '2026-03-18 19:41:22', NULL, NULL),
(71, 9, 6, NULL, 'Office Chair (CH-ERG-24)', '2026-000067', NULL, 1, 1, 1, 2, 3150.00, 3150.00, NULL, 73, 1, 'ErgoSeat', 'CH-ERG-24', NULL, '2026-03-18 19:41:22', NULL, NULL),
(72, 9, 12, NULL, 'Table Fan (TF-16X)', '2026-000068', NULL, 1, 1, 1, 2, 2450.00, 2450.00, NULL, 74, 1, 'Asahi', 'TF-16X', NULL, '2026-03-18 19:41:22', NULL, NULL),
(73, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000069', NULL, 1, 1, 1, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', NULL, NULL),
(74, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000070', NULL, 1, 1, 1, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', NULL, NULL),
(75, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000071', NULL, 1, 1, 1, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', NULL, NULL),
(76, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000072', NULL, 1, 1, 1, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', NULL, NULL),
(77, 7, 7, NULL, 'Desktop Computer (ThinkCentre-M70)', '2026-000073', NULL, 1, 1, 1, 2, 42500.00, 42500.00, NULL, 66, 1, 'Lenovo', 'ThinkCentre-M70', NULL, '2026-03-18 19:41:22', NULL, NULL),
(78, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000074', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(79, 9, 6, NULL, 'Office Chair (CH-ERG-24)', '2026-000075', NULL, 1, 1, 1, 2, 3150.00, 3150.00, NULL, 73, 1, 'ErgoSeat', 'CH-ERG-24', NULL, '2026-03-18 19:41:22', NULL, NULL),
(80, 9, 12, NULL, 'Table Fan (TF-16X)', '2026-000076', NULL, 1, 1, 1, 2, 2450.00, 2450.00, NULL, 74, 1, 'Asahi', 'TF-16X', NULL, '2026-03-18 19:41:22', NULL, NULL),
(81, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000077', NULL, 1, 1, 1, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', NULL, NULL),
(82, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000078', NULL, 1, 1, 1, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', NULL, NULL),
(83, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000079', NULL, 1, 1, 1, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', NULL, NULL),
(84, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000080', NULL, 1, 1, 1, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', NULL, NULL),
(85, 7, 7, NULL, 'Desktop Computer (ThinkCentre-M70)', '2026-000081', NULL, 1, 1, 1, 2, 42500.00, 42500.00, NULL, 66, 1, 'Lenovo', 'ThinkCentre-M70', NULL, '2026-03-18 19:41:22', NULL, NULL),
(86, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000082', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(87, 9, 6, NULL, 'Office Chair (CH-ERG-24)', '2026-000083', NULL, 1, 1, 1, 2, 3150.00, 3150.00, NULL, 73, 1, 'ErgoSeat', 'CH-ERG-24', NULL, '2026-03-18 19:41:22', NULL, NULL),
(88, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000084', NULL, 1, 1, 1, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', NULL, NULL),
(89, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000085', NULL, 1, 1, 1, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', NULL, NULL),
(90, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000086', NULL, 1, 1, 1, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', NULL, NULL),
(91, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000087', NULL, 1, 1, 1, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', NULL, NULL),
(92, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000088', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(93, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000089', NULL, 1, 1, 1, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', NULL, NULL),
(94, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000090', NULL, 1, 1, 1, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', NULL, NULL),
(95, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000091', NULL, 1, 1, 1, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', NULL, NULL),
(96, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000092', NULL, 1, 1, 1, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', NULL, NULL),
(97, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000093', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(98, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000094', NULL, 1, 1, 1, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', NULL, NULL),
(99, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000095', NULL, 1, 1, 1, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', NULL, NULL),
(100, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000096', NULL, 1, 1, 1, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', NULL, NULL),
(101, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000097', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(102, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000098', NULL, 1, 1, 1, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', NULL, NULL),
(103, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000099', NULL, 1, 1, 1, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', NULL, NULL),
(104, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000100', NULL, 1, 1, 1, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', NULL, NULL),
(105, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000101', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(106, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000102', NULL, 1, 1, 1, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', NULL, NULL),
(107, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000103', NULL, 1, 1, 1, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', NULL, NULL),
(108, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000104', NULL, 1, 1, 1, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', NULL, NULL),
(109, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000105', NULL, 1, 1, 1, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', NULL, NULL),
(110, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000106', NULL, 1, 1, 1, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', NULL, NULL);

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
(5, NULL, 40, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0001', 'acknowledged', '2026-03-18', 'Auto-created from DR inspection', NULL, '2026-03-18 19:44:26', 1, NULL, NULL, '2026-03-18 18:11:05'),
(7, NULL, 41, 1, NULL, NULL, 4, 'Cyron Micarte', 'PAR', 'PAR-2026-0002', 'acknowledged', '2026-03-18', 'Auto-created from DR acknowledgment', NULL, '2026-03-18 19:44:40', 1, NULL, NULL, '2026-03-18 19:44:40');

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
(5, 5, NULL, 'smsmsmsm', 'unit', 5, 1, 'Auto-created from DR inspection', '2026-03-18 10:11:05', '2026-03-18 10:11:05', NULL),
(7, 7, NULL, 'bugnaw', 'unit', 6, 1, 'Auto-created from DR acknowledgment', '2026-03-18 11:44:40', '2026-03-18 11:44:40', NULL);

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
-- Table structure for table `propertyreturn`
--

CREATE TABLE `propertyreturn` (
  `ReturnID` bigint(20) UNSIGNED NOT NULL,
  `ReturnNumber` varchar(50) NOT NULL,
  `BorrowID` bigint(20) UNSIGNED NOT NULL,
  `ReturnedByUserID` int(11) NOT NULL,
  `ReturnedAt` timestamp NULL DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `propertyreturn`
--

INSERT INTO `propertyreturn` (`ReturnID`, `ReturnNumber`, `BorrowID`, `ReturnedByUserID`, `ReturnedAt`, `Notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PBR-RET-2026-0001', 1, 1, '2026-03-18 13:25:11', 'done', '2026-03-18 13:25:11', '2026-03-18 13:25:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `propertyreturnitemconditions`
--

CREATE TABLE `propertyreturnitemconditions` (
  `ReturnItemConditionID` bigint(20) UNSIGNED NOT NULL,
  `ReturnItemID` bigint(20) UNSIGNED NOT NULL,
  `ConditionLabel` varchar(50) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 1,
  `Notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `propertyreturnitems`
--

CREATE TABLE `propertyreturnitems` (
  `ReturnItemID` bigint(20) UNSIGNED NOT NULL,
  `ReturnID` bigint(20) UNSIGNED NOT NULL,
  `BorrowItemID` bigint(20) UNSIGNED NOT NULL,
  `InventoryDetailID` int(11) NOT NULL,
  `QuantityReturned` int(11) NOT NULL DEFAULT 1,
  `ConditionID` int(11) DEFAULT NULL,
  `PrimaryConditionID` bigint(20) UNSIGNED DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `propertyreturnitems`
--

INSERT INTO `propertyreturnitems` (`ReturnItemID`, `ReturnID`, `BorrowItemID`, `InventoryDetailID`, `QuantityReturned`, `ConditionID`, `PrimaryConditionID`, `Remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 5, 1, 1, NULL, NULL, '2026-03-18 13:25:11', '2026-03-18 13:25:11', NULL);

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
(1, 'PTR-2026-0001', 3, 23, 30, 4, 2, NULL, 1, 'approved', 1, NULL, 6, '2026-03-18 12:55:49', 'completed', 1, '2026-03-18 12:56:48', 1, '2026-03-18 13:04:32', '2026-03-18 13:02:47', '2026-03-18 13:03:15', NULL, '2026-03-18 12:53:37', '2026-03-18 13:04:32', NULL);

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
(1, 1, 6, 1, NULL, '2026-03-18 12:53:37', '2026-03-18 12:53:37', NULL);

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
(5, 5, 5, 30, 'http://localhost:3000/qr/scan/5?h=6b87dcb1a9e5', '2026-03-18 18:11:05'),
(6, 6, 6, 30, 'http://localhost:8000/qr/scan/6?h=b67ece7330f6', '2026-03-18 21:04:32');

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
('mCMb5VydiX3VOOaqJqmECL4REMvWGEnKMUCcPHVJ', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiekJSMFRiTFUyYnFsVFpiV3ZkYVFvS3NpWXBXNnhIaGtXcENQSWlvQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzM6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvYWRtaW4vbWFzdGVyLWRhdGEvdW5pdHMtb2YtbWVhc3VyZT9wZXJfcGFnZT1hbGwiO3M6NToicm91dGUiO3M6MjI6InVuaXRzLW9mLW1lYXN1cmUuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjYwOiIkMnkkMTIkZnRnR1JoSk9Qby41RldteWJlTEx2Llo4d1g4L0guQmlmWk5ac0s1MXVRSXNKSFVCL2suaDYiO30=', 1774072603);

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
(3, 'Cyron Suppliers', 'Lapasan, Cagayan de Oro City, Philippines, 9000', 'Cyron Micarte', '09925077173', 'cyronmicarte@gmail.com', 1, '2025-12-26 15:58:19', '2025-12-26 15:58:47', NULL),
(4, 'COVID Supplies', NULL, NULL, NULL, NULL, 1, '2026-02-26 00:39:20', NULL, NULL);

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
(83, 3, 'Trusted Device', '1d477b525bb9536a45317116234d4dc76c9591f97ff36fdd68079ad2383d7cc1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '127.0.0.1', NULL, '2026-03-20 21:56:29', '2026-02-18 21:56:29', NULL),
(84, 3, 'Trusted Device', '2c08e0fe7b7c7eb36021b27ce8614143407b5a044868e7737c0d2a8ef43db52a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '127.0.0.1', '2026-03-03 21:36:37', '2026-03-28 23:52:39', '2026-02-26 23:52:39', '2026-03-03 21:36:37'),
(85, 1, 'Trusted Device', 'f03b6fb7d6b5f62c9f756dbeb6bdbc644441cf13b196ac505575527c771e434e', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '127.0.0.1', '2026-03-03 20:47:01', '2026-03-28 23:55:06', '2026-02-26 23:55:06', '2026-03-03 20:47:01'),
(86, 1, 'Trusted Device', '92b86588f1a81d8f632b3484925ab65694afa1335866b980e6f6e563d670ed4d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '127.0.0.1', '2026-03-18 17:45:44', '2026-04-02 22:47:37', '2026-03-03 22:47:37', '2026-03-18 17:45:44'),
(87, 3, 'Trusted Device', 'a6c3e3c1261a9b539ea4a47e6511694036382d051906d8f54aef7b55e1d5b1fe', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '127.0.0.1', '2026-03-04 00:33:22', '2026-04-02 22:58:48', '2026-03-03 22:58:48', '2026-03-04 00:33:22'),
(88, 11, 'Trusted Device', '3f2dd3dd519fead880cf455caaee905b837d58dec6dcb3284c2b211a80cd1ec2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-04-13 12:50:12', '2026-03-14 12:50:12', NULL),
(89, 1, 'Trusted Device', '532431dc84f9490cb1ac4d1fd256aa5adf11c00a381c7eae64abcd0b746739c9', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-04-13 14:15:58', '2026-03-14 14:15:58', NULL),
(90, 1, 'Trusted Device', '6b6025c2a36980fbfc03e04c04bc8e3a0348d152d68b44a421d4efaaa4afbd7c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '127.0.0.1', '2026-03-19 18:12:56', '2026-04-17 17:46:53', '2026-03-18 17:46:53', '2026-03-19 18:12:56'),
(91, 3, 'Trusted Device', '2c528d8a573bd0c230a3da1f56d8861f8f9dffc207732e06d1c6ad121c39e1ad', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '127.0.0.1', NULL, '2026-04-17 20:52:45', '2026-03-18 20:52:45', NULL),
(92, 6, 'Trusted Device', '8df7d5b5565caefa77e810de881b5ac57dc0ed30630b1191136bedf62b0abca5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '127.0.0.1', NULL, '2026-04-17 20:54:09', '2026-03-18 20:54:09', NULL),
(93, 3, 'Trusted Device', 'd4f7187767cc20565c467497549bcc8153e39ddff5174ab4629512fc42501887', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '127.0.0.1', NULL, '2026-04-17 21:01:59', '2026-03-18 21:01:59', NULL),
(94, 6, 'Trusted Device', '08db6305b526db26e1cbc8b30c50aa1de55b949e3e5da221257c5a0fc05ba806', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '127.0.0.1', NULL, '2026-04-17 21:07:57', '2026-03-18 21:07:57', NULL),
(95, 3, 'Trusted Device', '573b7d24af3153004e233879098a0b91086333bda8f403cc035de6f2fce26731', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '127.0.0.1', NULL, '2026-04-17 21:19:17', '2026-03-18 21:19:17', NULL),
(96, 6, 'Trusted Device', 'c7b27861e5c1e571ce5bd081864517c7f6184ffde03852b880202e7d9db9afcd', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '127.0.0.1', NULL, '2026-04-17 21:22:28', '2026-03-18 21:22:28', NULL),
(97, 1, 'Trusted Device', 'eef699df9668886f95e449f37065008c1e02168d7017414dab3f4d5038e9e431', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '127.0.0.1', '2026-03-21 13:56:20', '2026-04-18 18:14:15', '2026-03-19 18:14:15', '2026-03-21 13:56:20'),
(98, 3, 'Trusted Device', '297d885ccb698dc6bd7ece0af703158fcff45a1af4858da1ecf53670e1e8a6b6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '127.0.0.1', NULL, '2026-04-19 21:48:16', '2026-03-20 21:48:16', NULL);

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
(1, 1, 49, 'gso.admin', 'micarte.aaron08@gmail.com', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$ftgGRhJOPo.5FWmybeLLv.Z8wX8/H.BifZNZsK51uQIsJHUB/k.h6', 0, 'GSO Administrator', 1, 1, 'eyJpdiI6ImJvcFBFdSttOHlXRGNGeVN1MytvQ2c9PSIsInZhbHVlIjoiaE56YzA0VTE1YnhwNlp0YldLY0ZYYmpHUERZa0dJb2wvcm1Id3lndFRlVT0iLCJtYWMiOiI2OGJhYjIzYTNlZDA5NDI4NjQ4MDAxMjg4NmE2OTMxZmFkNTVkNjI3ZDc2N2Y0MjY0MzQxMWM4MjZlOGZkMTM1IiwidGFnIjoiIn0=', 'eyJpdiI6Im9iWlVLSmo4ZEhkWXBSVG04MzlnakE9PSIsInZhbHVlIjoiZzNtRjlLNEhVS3FJd0VVUTd4cVNXa1BLVUlPeG9CYnMvWmZULyt1R001MVBTTzhTaE5DM2tFamNQblFkdStTL2ZUc1JLY3dEWmoyVENxdmVFQ3ZCdkcyMENaVHMxM3lUWWg0UFcwMUdBa2ovQktFeFREZFFnRkJlY1hwbENkeHpoeXZSQmljUGxYdnJ6RGFBU3RHcHE4bTREcmEzTDM5d0JkWGs5VUR3bGNjPSIsIm1hYyI6IjkxZGY5OTAyZGU1NDY4YzgxNjg5ZWM3OTg1MWZhOGE3NmE1MTE4YzNkZTZjZjFjNjI3MjllZGQ1MDI4NzJiMjkiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-02-26 23:54:52'),
(2, 2, 49, 'gso.staff.aaronmicarte', 'aaron.micarte@gso.gov.ph', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$YU66h83wjMCOJ9LMN81o8OuWpIuFYTFQpHCxkuWDAurXbYJS9Q.Mu', 0, 'Aaron Micarte', 1, 0, 'eyJpdiI6IjlHZGd3c0dKcmxwcnBLNEdhN2pkWVE9PSIsInZhbHVlIjoiRkd3RWh6SUczdmt3NnpVU0lhUnVGazNSSmdCYkU3OCtLZzA3T1RPZW1BST0iLCJtYWMiOiI3NjliN2M0MDBjZDE4N2YyODcxMWVkMjNjODdiMmVlMDcwZDEwYWIzYTk3NzFhMDVkMjY1MTQzMDM4Y2Y0OThkIiwidGFnIjoiIn0=', 'eyJpdiI6InRLc2pkQjlRVHpZd2ZzKzZ3c0RCRmc9PSIsInZhbHVlIjoiZUlXMnFlWFZ0dkNtZmUxMVVON0VUa3lXOG1EWDNmVjdrRFUxN09TUElBR2Q1K1NNeEF1WHJjTFMyN3dNbG01alUxMGN1b0l4cE5qQmRPL1BUNGxPRU00VGFOaFVtNEd2SWpJTnlEYmhLbTFrVEk0NU51UDY1S2Z4K2RCOHhxbGVyZnkyU1lXbkp2QXFPNlZWNCtwZ0lndHVyd2ljeW4wSUdObktkajJuVkpnPSIsIm1hYyI6ImU0NDM1MWY0YzZkYTYyZmMwY2QzYWM1M2ZiYmMwZTE4MzY0NzM2OWU0YzU2YTg3ODljOWY1NzhiNWVkNWEzNWQiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-03-19 10:00:47'),
(3, 4, 30, 'IT_Head_Chokoy', 'custodian@gso.gov.ph', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$hlU7QKnUbWphvJnFxYgaY.gJVzBnxVKKV.kjw7xB5Oh.1jN0cfA62', 0, 'Chokoy Covid', 1, 1, 'eyJpdiI6ImFvVEpuaEFUclVHYXRVSEJmV3NsYWc9PSIsInZhbHVlIjoibDM5NXorMGlPTkVqUHk1YXN0OXgzM3BWd0FoT3BkYVN0ZkdwRzkxRk9yaz0iLCJtYWMiOiJhOWI4MGQzMjUyZGMyYmJhYTNiNjI3NjVjYWI5NGYyNGIyYjUwNmFkNmM1NjAzZGM5MTE3YTIzOWVkMGQ2NDg5IiwidGFnIjoiIn0=', 'eyJpdiI6Ik1mTHF2UjNtNFlyRldRTzcwbDF5S0E9PSIsInZhbHVlIjoiNjcwdVQxeTJtNmNwOUt4Wkd5RVN1alhjcGZ5R0w5Z2g4clI4L3BvRklUbERXaGFEY1MxMHMzaGMwRGVjVmhtbXpKT1AxSlUxeUlMam9PUWRUeFBNcnpJdytsUmFubHpiSExFWEJKOXErK1dLUEtLWFZ0MDVJQ2RPYU11MHorUmRxTS8zRlpmRythNkJlUmh3ZmZUYkNScXBxRlV0YmUwN3NyUjRkQUNCazMwPSIsIm1hYyI6ImJmN2NlN2EwODAzMDNlZTEwYWZkOGRkNjQwNzYwZTE1NzY3MDEyZGMwMTZjMDRjMWQ4NzcwMzY4ZmJiYTU3NTgiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-02-26 11:36:09'),
(5, 4, 54, 'HR_Head_Tyron', 'micarte2005@gmail.com', '2026-01-14 01:50:24', NULL, NULL, NULL, '$2y$12$UjxLHSUjJQBOZYHqrB7ajuK2rIiZ1P9962RTMFifYWMoF.fH7o3Wq', 0, 'Tyron Mecarte', 1, 1, 'eyJpdiI6Ilo3U0pYYVBOd1RFZEtTbmxxUlRXUWc9PSIsInZhbHVlIjoiYXlZTWlzemI3TlFZeWI4MUpuNC9wdEpFelN0emhnbFVWL1g2Ky9lMTFOTT0iLCJtYWMiOiIwZjY3ZjA3OTUwY2M1NGQzOTczMmNhZjA1NzI5NzU4ZDQ0MGEzNDU1NTZkMzEzMmQxYzRjZmI4NzcwYzQ0YjdkIiwidGFnIjoiIn0=', 'eyJpdiI6InplOXhDZXZEeHR1QkF6WEpGYzRjdEE9PSIsInZhbHVlIjoiRGh6cXVuaHRQRDU1aGxGd2hDZGVLdmJkcWIybDNnWkpXaXF5TkdobmpzOS9iYzlNRDNUcldyQ0VZSlR2UDlDd3JvZ1lUVjVIWWJBTWJENmRKZHhva2FNZzJOc3ZaZkordUhzTkJnbkhuNEdYWnoxWDRtVkNZVGFHZkVhb2VLcHdDWVJtYzVZYno2SmJJS1h6YytiRVMwNDZBcVN0TnR4aDBJckRPSThOMmQ4PSIsIm1hYyI6ImExZDNmMzBiN2VjNzJjNzg5ZDEyYjAxMTRmNWJmZjcwZTJjYzhkMWUwNjA0OGU3MDkxNDk5NTlkYzc2NmI2YmMiLCJ0YWciOiIifQ==', '2026-01-14 01:00:26', '2026-02-25 15:31:42'),
(6, 4, 23, 'FD_Head_Cyron', 'aaya.micarte.coc@phinmaed.com', '2026-01-15 14:01:41', NULL, NULL, NULL, '$2y$12$A/XCqp8lXrNCeYvQ6lOyUu6DXxbXtJ3Z7FJ.5q39q9yASzA1EYIKS', 0, 'Cyron Micarte', 1, 1, 'eyJpdiI6IjNoQ1dZTlhsazVlYU80Si9GQjUwN1E9PSIsInZhbHVlIjoiS0loT3QxSW1kckJUVXpjQW9hQ20vdlRXQ3lpQk5iN1F3bjJVemtqWTVKOD0iLCJtYWMiOiI1MDU2YmJhZjk3OTFjYjQyZTZhNTQ3MjRkNjdmODJjNjYyNzQ1YjRiZjE1MWI0N2ZiNjExNzkwMjg3NTcwYTNiIiwidGFnIjoiIn0=', 'eyJpdiI6Ii9UTXkzNng3cUJYeXJwUzhXZWdYZVE9PSIsInZhbHVlIjoiM2VqL3hvWWZFUlZWM2sxK2xSM3JUOStSNlJYYmpkR010RHJ4UDU4MFFJKzc2QU5TZmRiTDU2QnRPbjUyRk5yTzRhMWhYc0VTSnVITzRmK01mSkYzUENkUThwd0gwcEV2Yk02Ym10cWF2ZkMxRk1wNE8xamRmaDYzclRXejg4eVJSK2l5elkyY2J6dTRZRnllWkFCcVBBK2o0ZHVVUnBaS0FTNlFLVG5ZN2lnPSIsIm1hYyI6ImM3ZTRhNzBhOWRiYTg5ZWYwN2I0ZmY5OWRiZDY2MmIzYTE4ZTNjYzQ4NmQyZWFjZGMxZjE0ZjU3NWI0MDczZjUiLCJ0YWciOiIifQ==', '2026-01-15 14:00:32', '2026-03-04 00:34:21'),
(7, 6, 49, 'IO_Abrogar', 'cycymicarte@gmail.com', '2026-02-12 18:27:18', NULL, NULL, NULL, '$2y$12$fepSB6IGbOXf7nWvwUDb2uJjr2FJO07ckbe1cHFDkN9/hSqnu.qQe', 0, 'Francismile Noah Abrogar', 1, 0, 'eyJpdiI6IlF1RW8ybzQxNWVPcy9obHNxZnJkN2c9PSIsInZhbHVlIjoiR3lzZ2VFZldMdkFocEpoRWVaU3BEMFJzRmovYWVPY1Z3MGRZaDFmc1h5VT0iLCJtYWMiOiJiNmE2ZjNmMWU4ZDliZmQ0YTExM2ExODE1MTFjNmE5MGE0Mzc5NjYzZTliOTE2NmU3MmFmNDc3YzFmZTE4YTQwIiwidGFnIjoiIn0=', 'eyJpdiI6InNoeEFiQmczdGtPRzFJeVBtTUl0L1E9PSIsInZhbHVlIjoiZlFjTFZMQ1FJSTAyK0pCNmg1OEhKVVRzYlluZGFvMC9nWk0yOW9BVGhFczMwTWlsM0psWHpKUVM4QkpVVEtTYWFqaG5kWHlqTWNwNmpvSkE5R2MrYXZyaVZUR3crOEdQbGp6ZE0zTkFPcSswR3BnRWdITHVPZ2FsMU1MQmdjeDdueVNMdDlnMWNYRGJxaFl0VC9xZFJzT2k1YUkzaVBRNFpMeDFvcFRHbkJZPSIsIm1hYyI6IjI4ZTlmMjViYTUzOWY3MjZmOWI5MjVhOGQ4ZjE0OTFiOTdlNjJjMzE4MDFhZGJjZTk3MjY4OGQwMmRlN2RjNzIiLCJ0YWciOiIifQ==', '2026-02-12 18:26:12', '2026-02-25 15:31:42');

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
  ADD UNIQUE KEY `ux_deliveryreceipt_supplierid_invoice` (`SupplierID`,`InvoiceNumber`),
  ADD UNIQUE KEY `ux_deliveryreceipt_supplierid_po` (`SupplierID`,`PONumber`),
  ADD KEY `POID` (`PONumber`),
  ADD KEY `ReceivedBy` (`ReceivedBy`),
  ADD KEY `deliveryreceipt_requestedby_index` (`RequestedBy`),
  ADD KEY `deliveryreceipt_supplierid_index` (`SupplierID`);

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
-- Indexes for table `propertyborrow`
--
ALTER TABLE `propertyborrow`
  ADD PRIMARY KEY (`BorrowID`),
  ADD UNIQUE KEY `propertyborrow_borrownumber_unique` (`BorrowNumber`),
  ADD KEY `propertyborrow_status_fromdepartmentid_index` (`Status`,`FromDepartmentID`),
  ADD KEY `propertyborrow_status_todepartmentid_index` (`Status`,`ToDepartmentID`),
  ADD KEY `propertyborrow_requestedbyuserid_index` (`RequestedByUserID`),
  ADD KEY `propertyborrow_duedate_index` (`DueDate`),
  ADD KEY `propertyborrow_fromdepartmentid_foreign` (`FromDepartmentID`),
  ADD KEY `propertyborrow_todepartmentid_foreign` (`ToDepartmentID`),
  ADD KEY `propertyborrow_fromenduserid_foreign` (`FromEndUserID`),
  ADD KEY `propertyborrow_toenduserid_foreign` (`ToEndUserID`),
  ADD KEY `propertyborrow_releasingreviewedbyuserid_foreign` (`ReleasingReviewedByUserID`),
  ADD KEY `propertyborrow_approvedbyuserid_foreign` (`ApprovedByUserID`);

--
-- Indexes for table `propertyborrowitems`
--
ALTER TABLE `propertyborrowitems`
  ADD PRIMARY KEY (`BorrowItemID`),
  ADD KEY `propertyborrowitems_inventorydetailid_index` (`InventoryDetailID`),
  ADD KEY `propertyborrowitems_borrowid_foreign` (`BorrowID`);

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
-- Indexes for table `propertyreturn`
--
ALTER TABLE `propertyreturn`
  ADD PRIMARY KEY (`ReturnID`),
  ADD UNIQUE KEY `propertyreturn_returnnumber_unique` (`ReturnNumber`),
  ADD KEY `propertyreturn_borrowid_index` (`BorrowID`),
  ADD KEY `propertyreturn_returnedbyuserid_index` (`ReturnedByUserID`),
  ADD KEY `propertyreturn_returnedat_index` (`ReturnedAt`);

--
-- Indexes for table `propertyreturnitemconditions`
--
ALTER TABLE `propertyreturnitemconditions`
  ADD PRIMARY KEY (`ReturnItemConditionID`),
  ADD KEY `propertyreturnitemconditions_returnitemid_index` (`ReturnItemID`),
  ADD KEY `propertyreturnitemconditions_conditionlabel_index` (`ConditionLabel`);

--
-- Indexes for table `propertyreturnitems`
--
ALTER TABLE `propertyreturnitems`
  ADD PRIMARY KEY (`ReturnItemID`),
  ADD KEY `propertyreturnitems_borrowitemid_index` (`BorrowItemID`),
  ADD KEY `propertyreturnitems_inventorydetailid_index` (`InventoryDetailID`),
  ADD KEY `propertyreturnitems_returnid_foreign` (`ReturnID`),
  ADD KEY `propertyreturnitems_primaryconditionid_foreign` (`PrimaryConditionID`),
  ADD KEY `propertyreturnitems_conditionid_index` (`ConditionID`);

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
  MODIFY `AuditID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `asset_request_signatures`
--
ALTER TABLE `asset_request_signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignedproperty`
--
ALTER TABLE `assignedproperty`
  MODIFY `PropertyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `condition`
--
ALTER TABLE `condition`
  MODIFY `ConditionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `deliveryreceipt`
--
ALTER TABLE `deliveryreceipt`
  MODIFY `DRID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `deliveryreceiptdetail`
--
ALTER TABLE `deliveryreceiptdetail`
  MODIFY `DRDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

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
  MODIFY `IARDetailID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `iar_signatures`
--
ALTER TABLE `iar_signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `inventorycustodianslip`
--
ALTER TABLE `inventorycustodianslip`
  MODIFY `ICSID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loginaudit`
--
ALTER TABLE `loginaudit`
  MODIFY `LoginAuditID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

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
-- AUTO_INCREMENT for table `propertyborrow`
--
ALTER TABLE `propertyborrow`
  MODIFY `BorrowID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `propertyborrowitems`
--
ALTER TABLE `propertyborrowitems`
  MODIFY `BorrowItemID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `InventoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `propertyinventorydetail`
--
ALTER TABLE `propertyinventorydetail`
  MODIFY `InventoryDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `propertyissuance`
--
ALTER TABLE `propertyissuance`
  MODIFY `IssuanceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `propertyissuancedetail`
--
ALTER TABLE `propertyissuancedetail`
  MODIFY `IssuanceDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `propertyreceived`
--
ALTER TABLE `propertyreceived`
  MODIFY `ReceivedID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `propertyreturn`
--
ALTER TABLE `propertyreturn`
  MODIFY `ReturnID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `propertyreturnitemconditions`
--
ALTER TABLE `propertyreturnitemconditions`
  MODIFY `ReturnItemConditionID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `propertyreturnitems`
--
ALTER TABLE `propertyreturnitems`
  MODIFY `ReturnItemID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `propertystatus`
--
ALTER TABLE `propertystatus`
  MODIFY `PropertyStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `propertytransfer`
--
ALTER TABLE `propertytransfer`
  MODIFY `TransferID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `propertytransferdetails`
--
ALTER TABLE `propertytransferdetails`
  MODIFY `TransferItemID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `propertytype`
--
ALTER TABLE `propertytype`
  MODIFY `PropertyTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `qrcode`
--
ALTER TABLE `qrcode`
  MODIFY `QRCodeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `SupplierID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `trusted_devices`
--
ALTER TABLE `trusted_devices`
  MODIFY `DeviceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `unitofmeasure`
--
ALTER TABLE `unitofmeasure`
  MODIFY `UnitOfMeasureID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `userrole`
--
ALTER TABLE `userrole`
  MODIFY `UserRoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
  ADD CONSTRAINT `articles_defaultuom_foreign` FOREIGN KEY (`DefaultUnitOfMeasureID`) REFERENCES `unitofmeasure` (`UnitOfMeasureID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `articles_propertytype_foreign` FOREIGN KEY (`PropertyTypeID`) REFERENCES `propertytype` (`PropertyTypeID`);

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
  ADD CONSTRAINT `deliveryreceipt_requestedby_foreign` FOREIGN KEY (`RequestedBy`) REFERENCES `user` (`UserID`) ON DELETE SET NULL,
  ADD CONSTRAINT `deliveryreceipt_supplierid_foreign` FOREIGN KEY (`SupplierID`) REFERENCES `supplier` (`SupplierID`) ON DELETE SET NULL;

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
-- Constraints for table `propertyborrow`
--
ALTER TABLE `propertyborrow`
  ADD CONSTRAINT `propertyborrow_approvedbyuserid_foreign` FOREIGN KEY (`ApprovedByUserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `propertyborrow_fromdepartmentid_foreign` FOREIGN KEY (`FromDepartmentID`) REFERENCES `department` (`DepartmentID`),
  ADD CONSTRAINT `propertyborrow_fromenduserid_foreign` FOREIGN KEY (`FromEndUserID`) REFERENCES `enduser` (`EndUserID`),
  ADD CONSTRAINT `propertyborrow_releasingreviewedbyuserid_foreign` FOREIGN KEY (`ReleasingReviewedByUserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `propertyborrow_requestedbyuserid_foreign` FOREIGN KEY (`RequestedByUserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `propertyborrow_todepartmentid_foreign` FOREIGN KEY (`ToDepartmentID`) REFERENCES `department` (`DepartmentID`),
  ADD CONSTRAINT `propertyborrow_toenduserid_foreign` FOREIGN KEY (`ToEndUserID`) REFERENCES `enduser` (`EndUserID`);

--
-- Constraints for table `propertyborrowitems`
--
ALTER TABLE `propertyborrowitems`
  ADD CONSTRAINT `propertyborrowitems_borrowid_foreign` FOREIGN KEY (`BorrowID`) REFERENCES `propertyborrow` (`BorrowID`) ON DELETE CASCADE,
  ADD CONSTRAINT `propertyborrowitems_inventorydetailid_foreign` FOREIGN KEY (`InventoryDetailID`) REFERENCES `propertyinventorydetail` (`InventoryDetailID`);

--
-- Constraints for table `propertyreturn`
--
ALTER TABLE `propertyreturn`
  ADD CONSTRAINT `propertyreturn_borrowid_foreign` FOREIGN KEY (`BorrowID`) REFERENCES `propertyborrow` (`BorrowID`) ON DELETE CASCADE,
  ADD CONSTRAINT `propertyreturn_returnedbyuserid_foreign` FOREIGN KEY (`ReturnedByUserID`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `propertyreturnitemconditions`
--
ALTER TABLE `propertyreturnitemconditions`
  ADD CONSTRAINT `propertyreturnitemconditions_returnitemid_foreign` FOREIGN KEY (`ReturnItemID`) REFERENCES `propertyreturnitems` (`ReturnItemID`) ON DELETE CASCADE;

--
-- Constraints for table `propertyreturnitems`
--
ALTER TABLE `propertyreturnitems`
  ADD CONSTRAINT `propertyreturnitems_borrowitemid_foreign` FOREIGN KEY (`BorrowItemID`) REFERENCES `propertyborrowitems` (`BorrowItemID`),
  ADD CONSTRAINT `propertyreturnitems_conditionid_foreign` FOREIGN KEY (`ConditionID`) REFERENCES `condition` (`ConditionID`),
  ADD CONSTRAINT `propertyreturnitems_inventorydetailid_foreign` FOREIGN KEY (`InventoryDetailID`) REFERENCES `propertyinventorydetail` (`InventoryDetailID`),
  ADD CONSTRAINT `propertyreturnitems_primaryconditionid_foreign` FOREIGN KEY (`PrimaryConditionID`) REFERENCES `propertyreturnitemconditions` (`ReturnItemConditionID`) ON DELETE SET NULL,
  ADD CONSTRAINT `propertyreturnitems_returnid_foreign` FOREIGN KEY (`ReturnID`) REFERENCES `propertyreturn` (`ReturnID`) ON DELETE CASCADE;

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
