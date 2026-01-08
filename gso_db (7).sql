-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 07, 2026 at 02:50 PM
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
  `Status` enum('pending','approved','rejected','issued','cancelled') NOT NULL DEFAULT 'pending',
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
(1, 'REQ-2026-0001', 3, 5, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-05 19:12:31', '2026-01-05 19:12:31', '2026-01-05 19:12:31', NULL),
(5, 'REQ-2026-0002', 3, 5, 'para maka dota ko', 'issued', 1, '2026-01-06 16:39:34', 'DULA WELL!!!!', 1, '2026-01-07 13:42:55', '2026-01-06 15:48:38', '2026-01-06 15:48:38', '2026-01-07 13:42:55', NULL);

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
(1, 1, NULL, 'Laptop', 'unit', 1, 'para maka dota', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-05 19:12:31', '2026-01-05 19:12:31', NULL),
(5, 5, NULL, 'Laptop', 'unit', 1, 'para maka dota ko', 'Aaron Micarte', 'kanang high end unta', 'PAR', NULL, NULL, 'ok', '2026-01-06 15:48:38', '2026-01-06 16:39:34', NULL),
(6, 5, NULL, 'Keyboard', 'pc', 1, 'para maka dota ko', 'Aaron Micarte', 'basta', 'PAR', NULL, NULL, 'ok', '2026-01-06 15:48:38', '2026-01-06 16:39:34', NULL),
(7, 5, NULL, 'Mouse', 'unit', 1, 'para maka click kos dota', 'Aaron Micarte', 'kanang dili guba', 'PAR', NULL, NULL, 'ok', '2026-01-06 15:48:38', '2026-01-06 16:39:34', NULL);

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
(1, 'assetrequest', 1, 'created', 3, '{\"request_number\":\"REQ-2026-0001\",\"items_count\":1,\"department\":\"IT Department\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-05 19:12:31'),
(2, 'assetrequest', 2, 'created', 3, '{\"request_number\":\"REQ-2026-0002\",\"items_count\":1,\"department\":\"IT Department\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-05 19:12:53'),
(3, 'assetrequest', 3, 'created', 3, '{\"request_number\":\"REQ-2026-0003\",\"items_count\":1,\"department\":\"IT Department\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-05 19:20:03'),
(4, 'assetrequest', 4, 'created', 3, '{\"request_number\":\"REQ-2026-0004\",\"items_count\":1,\"department\":\"IT Department\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-05 19:24:17'),
(5, 'assetrequest', 5, 'created', 3, '{\"request_number\":\"REQ-2026-0002\",\"items_count\":3,\"department\":\"IT Department\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-06 15:48:38'),
(6, 'asset_request', 5, 'approved', 1, '{\"request_number\":\"REQ-2026-0002\",\"items_reviewed\":3,\"notes\":\"DULA WELL!!!!\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-06 16:39:34'),
(7, 'asset_request', 5, 'issued', 1, '\"{\\\"request_number\\\":\\\"REQ-2026-0002\\\",\\\"issuances_count\\\":1,\\\"date_issued\\\":\\\"2026-01-07\\\"}\"', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL);

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
(5, 'DR_TEST_02', 'MICARTE DELIVERY', 'abcd123', '123454321', 1, '2025-12-25', 'good i guess', '', '2025-12-25 13:21:34', '2025-12-29 15:47:10'),
(6, 'DR_TEST_03', 'TYRON DELIVERY', 'dcba321', '543212345', 1, '2025-12-26', 'okeh rah', '', '2025-12-26 14:36:20', '2025-12-29 15:46:58'),
(7, 'DR_TEST_04', 'CYRON DELIVERY', 'hif1h29481', '512512312', 1, '2025-12-27', 'good', '', '2025-12-27 21:08:58', '2025-12-29 15:47:03'),
(8, 'DR_TEST_05', 'Supplier_01', '1234wqdasd', '124124124214', 1, '2025-12-27', 'Everything looks good', '', '2025-12-27 22:57:17', '2025-12-29 15:46:50'),
(9, 'DR_TEST_06', 'Supplier_01', 'sfajfj123123', '521512321', 1, '2025-12-27', 'good', '', '2025-12-27 23:03:01', '2025-12-29 15:46:40'),
(10, 'test1', 'MICARTE DELIVERY', 'abcd123', '124124124214', 1, '2025-12-28', 'good', '', '2025-12-28 20:42:55', NULL),
(11, 'DR_TEST_12', 'MICARTE DELIVERY', 'dcba321', '1234', 1, '2025-12-29', 'good everything madafakas', '', '2025-12-29 22:08:28', NULL),
(12, 'e', 'e', 'e', 'e', 1, '2026-01-02', 'e', 'dr_pictures/Pr45BlD7qGjIL0hcGuVzwJCxj70oKSceFr38YcjQ.png', '2026-01-02 17:18:30', NULL),
(13, 'q', 'q', 'q', 'q', 1, '2026-01-02', 'q', 'dr_pictures/fgX0xF83EBodfG8myW2mAlcEAz2xzuLxBV1uau4M.png', '2026-01-02 17:34:35', NULL),
(14, 'DR_TEST_13', 'MICARTE DELIVERY', 'sdfsdf123', '1231231', 1, '2026-01-06', 'good', 'dr_pictures/nqriMxXdkW9n13m5HYAEKoafJ7vwz9RxlAtI3KAM.jpg', '2026-01-06 16:28:19', NULL);

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
(11, 5, 'Laptop', NULL, NULL, NULL, 3, 1, 1, 30000.00, 30000.00, NULL),
(12, 5, 'Keyboard', NULL, NULL, NULL, 3, 1, 1, 499.00, 499.00, NULL),
(13, 5, 'Mouse', NULL, NULL, NULL, 3, 1, 1, 299.00, 299.00, '2025-12-25 16:58:25'),
(14, 5, 'Charger', NULL, NULL, NULL, 3, 1, 1, 999.99, 999.99, NULL),
(15, 5, 'Mouse', NULL, NULL, NULL, 3, 1, 1, 799.00, 799.00, NULL),
(16, 6, 'Item1', NULL, NULL, NULL, 3, 5, 1, 230.00, 1150.00, NULL),
(17, 6, 'Item2', NULL, NULL, NULL, 6, 2, 1, 500.00, 1000.00, NULL),
(18, 7, 'Shabu', NULL, NULL, NULL, 7, 50, 7, 1499.00, 74950.00, NULL),
(19, 8, 'Marijuana', NULL, NULL, NULL, 5, 250, 7, 199.00, 49750.00, NULL),
(20, 8, 'Printer', NULL, NULL, NULL, 3, 5, 1, 699.00, 3495.00, NULL),
(21, 9, 'item1', NULL, NULL, NULL, 6, 1, 3, 300.00, 300.00, NULL),
(22, 9, 'item2', NULL, NULL, NULL, 4, 1, 7, 500.00, 500.00, NULL),
(23, 10, 'laptop', 'asus', 'vivo', 'asus123', 3, 1, 1, 30000.00, 30000.00, NULL),
(24, 10, 'shabu', NULL, NULL, NULL, 7, 30, 6, 599.00, 17970.00, NULL),
(25, 11, 'Laptop', 'Asus', 'Vivobook', '12345asus', 3, 1, 1, 32000.00, 32000.00, '2025-12-29 14:09:03'),
(26, 11, 'Laptops', 'Asus', 'Vivobook', '12345asus', 3, 1, 1, 35000.00, 35000.00, NULL),
(27, 12, 'e', NULL, NULL, NULL, 7, 5, 7, 555.00, 2775.00, NULL),
(28, 13, 'q', NULL, NULL, NULL, 7, 3, 7, 333.00, 999.00, NULL),
(29, 14, 'item 1', NULL, NULL, NULL, 2, 1, 2, 99.00, 99.00, NULL),
(30, 14, 'item 2', NULL, NULL, NULL, 2, 1, 2, 99.00, 99.00, NULL),
(31, 14, 'item 3', NULL, NULL, NULL, 2, 1, 2, 99.00, 99.00, NULL);

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
(1, 1, 2, 'John Doe', '09123456789', 'john.doe@example.com', 1, '2026-01-06 23:14:32', '2026-01-06 23:14:32', NULL),
(2, 2, 2, 'Jane Smith', '09987654321', 'jane.smith@example.com', 1, '2026-01-06 23:14:32', '2026-01-06 23:14:32', NULL),
(3, 3, 5, 'Mike Johnson', '09111222333', 'mike.johnson@example.com', 1, '2026-01-06 23:14:32', '2026-01-06 23:14:32', NULL),
(4, 2, 5, 'Sarah Wilson', '09444555666', 'sarah.wilson@example.com', 1, '2026-01-06 23:14:32', '2026-01-06 23:14:32', NULL),
(5, 4, 4, 'Robert Brown', '09777888999', 'robert.brown@example.com', 1, '2026-01-06 23:14:32', '2026-01-06 23:14:32', NULL);

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
(80, 7, 26, 3, 1, 1, 0, 'Asus', 'Vivobook', '12345asus', 'good'),
(83, 8, 27, 7, 1, 5, 0, NULL, NULL, NULL, 'goods'),
(90, 9, 29, 2, 1, 1, 0, NULL, NULL, NULL, 'good'),
(91, 9, 30, 2, 1, 1, 0, NULL, NULL, NULL, 'good'),
(92, 9, 31, 2, 1, 1, 0, NULL, NULL, NULL, 'good');

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
(6, 5, 'inspected', 1, 'canvas', 'local', 'signatures/iar_5_inspected_1766847869.png', '/storage/signatures/iar_5_inspected_1766847869.png', 'image/png', 1000, 300, 15669, 'b708e3c4679cef754850907e71dee77f5cf71438bcd5c4e8cf0090ec9bd2e883', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 15:04:29', '2025-12-27 15:04:29'),
(7, 5, 'accepted', 1, 'canvas', 'local', 'signatures/iar_5_accepted_1766847871.png', '/storage/signatures/iar_5_accepted_1766847871.png', 'image/png', 1000, 300, 17044, '62a9a27af03d7c9f729f8e1e1181376b08b9dfb90c96b30069b00c418ffe0135', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 15:04:29', '2025-12-27 15:04:31'),
(9, 6, 'inspected', 1, 'canvas', 'local', 'signatures/iar_6_inspected_1766926552.png', '/storage/signatures/iar_6_inspected_1766926552.png', 'image/png', 1000, 300, 16129, '1dd838575fb0199d8e9f5b8b912fb2cd3674086d447bbdba12a78363b6ae000f', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-28 12:55:53', '2025-12-28 12:55:53'),
(10, 6, 'accepted', 1, 'canvas', 'local', 'signatures/iar_6_accepted_1766932133.png', '/storage/signatures/iar_6_accepted_1766932133.png', 'image/png', 1000, 300, 17496, '3993e66859f77916c662b53b00519e5192475d75f2ebcfeff0587ced10cb0ff9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-28 14:28:53', '2025-12-28 14:28:53'),
(11, 7, 'inspected', 1, 'canvas', 'local', 'signatures/iar_7_inspected_1767017427.png', '/storage/signatures/iar_7_inspected_1767017427.png', 'image/png', 1000, 300, 16930, '42a8a18d2cf87549c8114dcbab3f51e48a8a747d64ce1fa469233918f434461c', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-29 14:10:29', '2025-12-29 14:10:29'),
(12, 7, 'accepted', 1, 'canvas', 'local', 'signatures/iar_7_accepted_1767017490.png', '/storage/signatures/iar_7_accepted_1767017490.png', 'image/png', 1000, 300, 16311, '7e6e02aa09a70e697d2afde442b413ce7c1769b1774f652c7687824ec4cf7461', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-29 14:11:30', '2025-12-29 14:11:30'),
(13, 8, 'inspected', 1, 'canvas', 'local', 'signatures/iar_8_inspected_1767683509.png', '/storage/signatures/iar_8_inspected_1767683509.png', 'image/png', 1000, 300, 15726, '7997fbf35e1e8919fd78b8615639b928e7c79643d50705272f5ade3097813998', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-06 07:11:49', '2026-01-06 07:11:49'),
(14, 8, 'accepted', 1, 'canvas', 'local', 'signatures/iar_8_accepted_1767683560.png', '/storage/signatures/iar_8_accepted_1767683560.png', 'image/png', 1000, 300, 15727, '3a42d808c023d36c11859c8b112bfd7b37440e83335dbac43a1082ec792d3563', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-06 07:12:40', '2026-01-06 07:12:40'),
(15, 9, 'inspected', 1, 'canvas', 'local', 'signatures/iar_9_inspected_1767688198.png', '/storage/signatures/iar_9_inspected_1767688198.png', 'image/png', 1000, 300, 11497, '2eded498b4860c3493c98021f738d55d9009df2bd6e6a22119532f8a25991a25', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-06 08:29:58', '2026-01-06 08:29:58'),
(16, 9, 'accepted', 1, 'canvas', 'local', 'signatures/iar_9_accepted_1767688241.png', '/storage/signatures/iar_9_accepted_1767688241.png', 'image/png', 1000, 300, 12688, 'fbd91cdc25294fbc08aaa73b122ee79774c149f04116606ea6ec8cba9f61d780', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-06 08:30:41', '2026-01-06 08:30:41');

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
(5, 'IAR-2025-0005', 9, 1, 1, '2025-12-27', '2025-12-27', 'good', '2025-12-27 23:04:28', '2025-12-29 15:45:54'),
(6, 'IAR-2025-0006', 10, 1, 1, '2025-12-05', '2025-12-28', 'good', '2025-12-28 20:55:52', '2025-12-29 15:48:41'),
(7, 'IAR-2025-0007', 11, 1, 1, '2025-12-29', '2025-12-29', 'good ig uess', '2025-12-29 22:10:27', NULL),
(8, 'IAR-2026-0002', 12, 1, 1, '2026-01-06', '2026-01-06', 'okay tanan', '2026-01-06 15:11:49', NULL),
(9, 'IAR-2026-0003', 14, 1, 1, '2026-01-06', '2026-01-06', 'good', '2026-01-06 16:29:58', NULL);

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
(109, 1, '2025-12-27 15:12:12', 'Login', '127.0.0.1'),
(110, 1, '2025-12-27 22:44:51', 'Login', '127.0.0.1'),
(111, 1, '2025-12-28 14:45:06', 'Login', '127.0.0.1'),
(112, 1, '2025-12-28 20:14:10', 'Login', '127.0.0.1'),
(113, 1, '2025-12-29 21:08:52', 'Login', '127.0.0.1'),
(114, 1, '2025-12-30 01:59:34', 'Logout', '127.0.0.1'),
(115, 1, '2025-12-30 01:59:43', 'Login', '127.0.0.1'),
(116, 1, '2025-12-30 16:30:03', 'Login', '127.0.0.1'),
(117, 1, '2025-12-30 19:48:19', 'Login', '127.0.0.1'),
(118, 1, '2025-12-31 23:17:21', 'Login', '127.0.0.1'),
(119, 1, '2026-01-02 15:24:42', 'Login', '127.0.0.1'),
(120, 3, '2026-01-05 00:59:43', 'Login', '127.0.0.1'),
(121, 3, '2026-01-05 00:59:55', 'Login', '127.0.0.1'),
(122, 1, '2026-01-05 01:00:25', 'Login', '127.0.0.1'),
(123, 1, '2026-01-05 01:01:49', 'Login', '127.0.0.1'),
(124, 1, '2026-01-05 01:02:02', 'Logout', '127.0.0.1'),
(125, 3, '2026-01-05 01:02:10', 'Login', '127.0.0.1'),
(126, 1, '2026-01-05 01:25:18', 'Login', '127.0.0.1'),
(127, 1, '2026-01-05 01:42:00', 'Login', '127.0.0.1'),
(128, 1, '2026-01-05 01:42:12', 'Logout', '127.0.0.1'),
(129, 1, '2026-01-05 01:42:21', 'Login', '127.0.0.1'),
(130, 1, '2026-01-05 01:42:35', 'Logout', '127.0.0.1'),
(131, 3, '2026-01-05 01:42:46', 'Login', '127.0.0.1'),
(132, 3, '2026-01-05 01:42:57', 'Login', '127.0.0.1'),
(133, 3, '2026-01-05 01:47:11', 'Login', '127.0.0.1'),
(134, 3, '2026-01-05 01:47:56', 'Login', '127.0.0.1'),
(135, 1, '2026-01-05 01:48:44', 'Login', '127.0.0.1'),
(136, 3, '2026-01-05 01:49:03', 'Login', '127.0.0.1'),
(137, 3, '2026-01-05 01:55:41', 'Login', '127.0.0.1'),
(138, 3, '2026-01-05 01:57:01', 'Login', '127.0.0.1'),
(139, 3, '2026-01-05 01:57:19', 'Logout', '127.0.0.1'),
(140, 1, '2026-01-05 01:58:19', 'Login', '127.0.0.1'),
(141, 1, '2026-01-05 01:58:30', 'Logout', '127.0.0.1'),
(142, 3, '2026-01-05 01:58:47', 'Login', '127.0.0.1'),
(143, 3, '2026-01-05 01:58:55', 'Logout', '127.0.0.1'),
(144, 1, '2026-01-05 02:01:09', 'Login', '127.0.0.1'),
(145, 1, '2026-01-05 02:01:18', 'Logout', '127.0.0.1'),
(146, 1, '2026-01-05 02:01:38', 'Login', '127.0.0.1'),
(147, 1, '2026-01-05 02:01:46', 'Logout', '127.0.0.1'),
(148, 3, '2026-01-05 02:02:12', 'Login', '127.0.0.1'),
(149, 3, '2026-01-05 02:02:23', 'Logout', '127.0.0.1'),
(150, 3, '2026-01-05 02:02:28', 'Login', '127.0.0.1'),
(151, 3, '2026-01-05 02:02:39', 'Logout', '127.0.0.1'),
(152, 3, '2026-01-06 00:15:41', 'FailedLogin', '127.0.0.1'),
(153, 3, '2026-01-06 01:20:14', 'Login', '127.0.0.1'),
(154, 1, '2026-01-06 13:38:23', 'Login', '127.0.0.1'),
(155, 1, '2026-01-06 15:05:15', 'Logout', '127.0.0.1'),
(156, 1, '2026-01-06 15:06:54', 'Login', '127.0.0.1'),
(157, 1, '2026-01-06 15:14:52', 'Logout', '127.0.0.1'),
(158, 1, '2026-01-06 15:15:16', 'Login', '127.0.0.1'),
(159, 1, '2026-01-06 15:15:22', 'Logout', '127.0.0.1'),
(160, 3, '2026-01-06 15:15:43', 'Login', '127.0.0.1'),
(161, 3, '2026-01-06 15:27:18', 'Logout', '127.0.0.1'),
(162, 1, '2026-01-06 15:27:45', 'Login', '127.0.0.1'),
(163, 1, '2026-01-06 15:34:41', 'Logout', '127.0.0.1'),
(164, 1, '2026-01-06 15:36:08', 'FailedLogin', '127.0.0.1'),
(165, 1, '2026-01-06 15:36:26', 'FailedLogin', '127.0.0.1'),
(166, 1, '2026-01-06 15:37:21', 'Login', '127.0.0.1'),
(167, 1, '2026-01-06 15:39:36', 'Logout', '127.0.0.1'),
(168, 1, '2026-01-06 15:39:52', 'FailedLogin', '127.0.0.1'),
(169, 1, '2026-01-06 15:41:39', 'FailedLogin', '127.0.0.1'),
(170, 1, '2026-01-06 15:41:46', 'FailedLogin', '127.0.0.1'),
(171, 3, '2026-01-06 15:57:48', 'Login', '127.0.0.1'),
(172, 3, '2026-01-06 16:08:36', 'Logout', '127.0.0.1'),
(173, 1, '2026-01-06 16:08:53', 'Login', '127.0.0.1'),
(174, 1, '2026-01-06 16:09:02', 'Logout', '127.0.0.1'),
(175, 1, '2026-01-06 16:09:10', 'Login', '127.0.0.1'),
(176, 1, '2026-01-06 16:15:19', 'Logout', '127.0.0.1'),
(177, 3, '2026-01-06 16:15:39', 'Login', '127.0.0.1'),
(178, 3, '2026-01-06 16:16:21', 'Logout', '127.0.0.1'),
(179, 1, '2026-01-06 16:16:38', 'Login', '127.0.0.1'),
(180, 3, '2026-01-06 20:32:23', 'Login', '127.0.0.1'),
(181, 3, '2026-01-06 20:33:09', 'Logout', '127.0.0.1'),
(182, 1, '2026-01-06 20:33:33', 'Login', '127.0.0.1'),
(183, 1, '2026-01-06 20:37:17', 'Logout', '127.0.0.1'),
(184, 1, '2026-01-06 20:37:24', 'Login', '127.0.0.1'),
(185, 1, '2026-01-06 22:29:36', 'Logout', '127.0.0.1'),
(186, 3, '2026-01-06 22:30:04', 'Login', '127.0.0.1'),
(187, 3, '2026-01-06 23:49:05', 'Logout', '127.0.0.1'),
(188, 1, '2026-01-06 23:49:42', 'Login', '127.0.0.1'),
(189, 1, '2026-01-07 00:50:30', 'Logout', '127.0.0.1'),
(190, 3, '2026-01-07 00:50:54', 'Login', '127.0.0.1'),
(191, 1, '2026-01-07 18:38:01', 'Login', '127.0.0.1');

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
(101, '2026_01_07_000003_update_propertyissuance_add_freetext_users', 19);

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
(1, NULL, NULL, NULL, 7, '2025-12-29', NULL, 'Auto-ingested from IAR #IAR-2025-0007', '2025-12-29 23:50:55', NULL, 'INV-20251229-235055-7', NULL),
(2, NULL, NULL, NULL, 8, '2026-01-06', NULL, 'Auto-ingested from IAR #IAR-2026-0002', '2026-01-06 15:13:56', NULL, 'INV-20260106-151356-8', NULL),
(3, NULL, NULL, NULL, 9, '2026-01-06', NULL, 'Auto-ingested from IAR #IAR-2026-0003', '2026-01-06 16:31:09', NULL, 'INV-20260106-163109-9', NULL);

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
(1, 1, 3, NULL, 'Lost in neon streets, I hear echoes of things I can’t remember, shadows stretching longer than the night itself, my heartbeat syncing with a broken streetlamp flicker, everything melting into one slow hum, I laugh at nothing, and the sky feels too heavy,', NULL, '12345asus', 1, 1, 1, 1, 35000.00, 35000.00, 80, 26, 1, 'Asus', 'Vivobook', 'good', '2025-12-29 23:50:55', '2025-12-30 20:40:16', NULL),
(2, 2, 7, NULL, NULL, NULL, NULL, 1, 1, 1, 7, 555.00, 555.00, 83, 27, 1, NULL, NULL, 'goods', '2026-01-06 15:13:56', NULL, NULL),
(3, 2, 7, NULL, NULL, NULL, NULL, 1, 1, 1, 7, 555.00, 555.00, 83, 27, 1, NULL, NULL, 'goods', '2026-01-06 15:13:56', NULL, NULL),
(4, 2, 7, NULL, NULL, NULL, NULL, 1, 1, 1, 7, 555.00, 555.00, 83, 27, 1, NULL, NULL, 'goods', '2026-01-06 15:13:56', NULL, NULL),
(5, 2, 7, NULL, NULL, NULL, NULL, 1, 1, 1, 7, 555.00, 555.00, 83, 27, 1, NULL, NULL, 'goods', '2026-01-06 15:13:56', NULL, NULL),
(6, 2, 7, NULL, NULL, NULL, NULL, 1, 1, 1, 7, 555.00, 555.00, 83, 27, 1, NULL, NULL, 'goods', '2026-01-06 15:13:56', NULL, NULL),
(7, 3, 2, NULL, NULL, NULL, NULL, 1, 1, 1, 2, 99.00, 99.00, 90, 29, 1, NULL, NULL, 'good', '2026-01-06 16:31:10', NULL, NULL),
(8, 3, 2, NULL, NULL, NULL, NULL, 1, 1, 1, 2, 99.00, 99.00, 91, 30, 1, NULL, NULL, 'good', '2026-01-06 16:31:10', NULL, NULL),
(9, 3, 2, NULL, NULL, NULL, NULL, 1, 1, 1, 2, 99.00, 99.00, 92, 31, 1, NULL, NULL, 'good', '2026-01-06 16:31:10', NULL, NULL);

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
(1, 5, 1, NULL, 'Aaron Micarte', 'PAR', 'PAR-2026-0001', 'pending_acknowledgment', '2026-01-07', 'helyeeee', NULL, NULL, NULL, NULL, '2026-01-07 21:42:55');

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
(1, 1, 5, 'Laptop', 'unit', NULL, 1, 'ok', '2026-01-07 13:42:55', '2026-01-07 13:42:55', NULL),
(2, 1, 6, 'Keyboard', 'pc', NULL, 1, 'ok', '2026-01-07 13:42:55', '2026-01-07 13:42:55', NULL),
(3, 1, 7, 'Mouse', 'unit', NULL, 1, 'ok', '2026-01-07 13:42:55', '2026-01-07 13:42:55', NULL);

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
(4, 7, 1, 1, '2025-12-29', 35000.00, 'Auto-created on acceptance', '2025-12-29 22:11:31'),
(5, 8, 1, 1, '2026-01-06', 2775.00, 'Auto-created on acceptance', '2026-01-06 15:12:41'),
(6, 9, 1, 1, '2026-01-06', 297.00, 'Auto-created on acceptance', '2026-01-06 16:30:42');

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
('7P6nnSN0pyePyUwhot8Bq1b77vCK0pZIgEmUVnHH', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidE9oajQxWFd4a2x4S21oZ2pOa3B2dFJnVVJMYzVhSTkxaTU3cWczbyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NjM6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvcHJvcGVydHktaXNzdWFuY2VzP3BhZ2U9MSZwZXJfcGFnZT0xNSI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJGZ0Z0dSaEpPUG8uNUZXbXliZUxMdi5aOHdYOC9ILkJpZlpOWnNLNTF1UUlzSkhVQi9rLmg2Ijt9', 1767793684);

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
(36, 1, 'Chrome on Windows', 'acf46ed00ad04146142ba504158875138b4d298cc2e35ee2dc25f507acb9904c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-06 18:38:01', '2026-01-07 18:38:01', NULL);

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
  `Password` varchar(255) NOT NULL,
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

INSERT INTO `user` (`UserID`, `UserRoleID`, `DepartmentID`, `Username`, `Email`, `Password`, `FullName`, `IsActive`, `MFAEnabled`, `MFASecret`, `BackupCodes`, `CreatedDate`, `UpdatedDate`) VALUES
(1, 1, 1, 'gso.admin', 'micarte.aaron08@gmail.com', '$2y$12$ftgGRhJOPo.5FWmybeLLv.Z8wX8/H.BifZNZsK51uQIsJHUB/k.h6', 'GSO Administrator', 1, 1, 'eyJpdiI6IndTcFYzbnlmQjd5R2dnaEk1dzNNV1E9PSIsInZhbHVlIjoiaVZKanNlUDFSS20vSXJrblptbC9DMFlnTnVJVWpsQ1ZyaTkxTDZOVjViQT0iLCJtYWMiOiI0YWMwODkzOGRmZmExNzBjMWVkNDRmNGQzYWIyMDNkNGY0NWQ1MzNmM2VmNWQzYjE0YzJmMWY3OWRhOTViYTNhIiwidGFnIjoiIn0=', 'eyJpdiI6IitaZWR2a2JoQk5UWHREbXBDOWVPdWc9PSIsInZhbHVlIjoiTEVwTVB5K0xoK000c3pFdHdWOGpnNnBtQ1k2WWsreVZLb1BjMWh3dzlscmJlWCthVWxPR3ZhNkx1VWNTZXQrUlAwNytQVUxpM1dyM1VacFc2TW9hR24zT3V0NC8vaXJJeFFMdERzVnpZNUVKTkdkdUdRWUZ4QldzNElqSVhCdE5BUlhVVnBVTGRIQ0YyeFh4d3F1RDRqZy92eGZpeURrc1ZnRm90QWM1dFpRPSIsIm1hYyI6ImQ2YjlkMDg3ZGZmNmUwZTM1ZTI2ZmZiYzk0NzU0MWU2ZTIyMjk3ZGE0YmZiYWNlYTRmYTU1ZTVmOTVhZDAzOTciLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2025-12-08 17:51:44'),
(2, 2, 1, 'gso.staff.aaronmicarte', 'aaron.micarte@gso.gov.ph', '$2y$12$YU66h83wjMCOJ9LMN81o8OuWpIuFYTFQpHCxkuWDAurXbYJS9Q.Mu', 'Aaron Micarte', 1, 1, 'eyJpdiI6IjlHZGd3c0dKcmxwcnBLNEdhN2pkWVE9PSIsInZhbHVlIjoiRkd3RWh6SUczdmt3NnpVU0lhUnVGazNSSmdCYkU3OCtLZzA3T1RPZW1BST0iLCJtYWMiOiI3NjliN2M0MDBjZDE4N2YyODcxMWVkMjNjODdiMmVlMDcwZDEwYWIzYTk3NzFhMDVkMjY1MTQzMDM4Y2Y0OThkIiwidGFnIjoiIn0=', 'eyJpdiI6InRLc2pkQjlRVHpZd2ZzKzZ3c0RCRmc9PSIsInZhbHVlIjoiZUlXMnFlWFZ0dkNtZmUxMVVON0VUa3lXOG1EWDNmVjdrRFUxN09TUElBR2Q1K1NNeEF1WHJjTFMyN3dNbG01alUxMGN1b0l4cE5qQmRPL1BUNGxPRU00VGFOaFVtNEd2SWpJTnlEYmhLbTFrVEk0NU51UDY1S2Z4K2RCOHhxbGVyZnkyU1lXbkp2QXFPNlZWNCtwZ0lndHVyd2ljeW4wSUdObktkajJuVkpnPSIsIm1hYyI6ImU0NDM1MWY0YzZkYTYyZmMwY2QzYWM1M2ZiYmMwZTE4MzY0NzM2OWU0YzU2YTg3ODljOWY1NzhiNWVkNWEzNWQiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2025-12-26 01:48:44'),
(3, 4, 5, 'IT_Head_Chokoy', 'custodian@gso.gov.ph', '$2y$12$hlU7QKnUbWphvJnFxYgaY.gJVzBnxVKKV.kjw7xB5Oh.1jN0cfA62', 'Chokoy Covid', 1, 1, 'eyJpdiI6IitjNisxbWw2NGZpQ1prL0lmOVhoMnc9PSIsInZhbHVlIjoiQ0JicTV1WW1lUWlPQkY2cytjZ3dPdFRxa3JYckR1S0FrNWllVU9FWHVJOD0iLCJtYWMiOiJhZGI5YmVmZjBkYThmZmNhMzM3M2IzNjkzY2VkMmI2ZDNlN2U2Mzc3ZWJkMTViNjk4OTVhNGFmZDYyZmY4MDYwIiwidGFnIjoiIn0=', 'eyJpdiI6IlRUSHo4SUNybHNJK0RqN3h3bWF1R3c9PSIsInZhbHVlIjoiRm5GNFNVUXErL2F5TWxHSWQ4V0lDOEJEb1BnQ2hlT3piN2cxRFVVZUY4WUs0cDF3anFWeU5uL28xaVBXWjR4Y0p0UGRjcVp5eEczWXlBVTNDeENOdlFYZHpFQ0JJV2VwaFdyeTNaVFVpZTBxM0I4WEtVMU1tTGhiVk51aHdodWRYVmZ3SEVJT2FZMG1hdHVEQ1Y2RitwY0M5VUlSUi9BNFRGWDRIakZsb1BFPSIsIm1hYyI6IjA1M2NkZDMwODAxMTM4N2M5ZjYzYTU2ZTRjZTU3YTU4NzMwZmQ0MTg2MTA1ZmZlM2YxNGQ5ODU4NWZkZDZjMDYiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-01-05 01:56:45');

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
-- Indexes for table `asset_audit_logs`
--
ALTER TABLE `asset_audit_logs`
  ADD PRIMARY KEY (`AuditID`),
  ADD KEY `asset_audit_logs_entitytype_entityid_index` (`EntityType`,`EntityID`),
  ADD KEY `asset_audit_logs_performedby_index` (`PerformedBy`),
  ADD KEY `asset_audit_logs_createdat_index` (`CreatedAt`);

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
  MODIFY `RequestID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `assetrequestdetail`
--
ALTER TABLE `assetrequestdetail`
  MODIFY `RequestDetailID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `asset_audit_logs`
--
ALTER TABLE `asset_audit_logs`
  MODIFY `AuditID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `assignedproperty`
--
ALTER TABLE `assignedproperty`
  MODIFY `PropertyID` int(11) NOT NULL AUTO_INCREMENT;

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
  MODIFY `DRID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `deliveryreceiptdetail`
--
ALTER TABLE `deliveryreceiptdetail`
  MODIFY `DRDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

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
  MODIFY `EndUserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `iardetail`
--
ALTER TABLE `iardetail`
  MODIFY `IARDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `iar_signatures`
--
ALTER TABLE `iar_signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
  MODIFY `IARID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `inventorycustodianslip`
--
ALTER TABLE `inventorycustodianslip`
  MODIFY `ICSID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loginaudit`
--
ALTER TABLE `loginaudit`
  MODIFY `LoginAuditID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=192;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

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
  MODIFY `InventoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `propertyinventorydetail`
--
ALTER TABLE `propertyinventorydetail`
  MODIFY `InventoryDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `propertyissuance`
--
ALTER TABLE `propertyissuance`
  MODIFY `IssuanceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `propertyissuancedetail`
--
ALTER TABLE `propertyissuancedetail`
  MODIFY `IssuanceDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `propertyreceived`
--
ALTER TABLE `propertyreceived`
  MODIFY `ReceivedID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `QRCodeID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `SupplierID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `trusted_devices`
--
ALTER TABLE `trusted_devices`
  MODIFY `DeviceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

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
-- Constraints for table `asset_audit_logs`
--
ALTER TABLE `asset_audit_logs`
  ADD CONSTRAINT `asset_audit_logs_performedby_foreign` FOREIGN KEY (`PerformedBy`) REFERENCES `user` (`UserID`);

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
