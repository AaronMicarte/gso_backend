-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2026 at 08:54 AM
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
(1, 'Desktop Computer', 'Desktop workstation computer', NULL, 7, 2, 1, '2026-02-15 07:04:29', '2026-04-27 15:26:20', NULL),
(2, 'Laptop Computer', 'Portable laptop computer', NULL, 7, 2, 1, '2026-02-15 07:04:29', '2026-04-27 15:26:20', NULL),
(3, 'Printer/Scanner', 'Multifunction printer and scanner', NULL, 7, 2, 1, '2026-02-15 07:04:29', '2026-04-27 15:26:20', NULL),
(4, 'Office Desk', 'Office desk', NULL, 6, 2, 1, '2026-02-15 07:04:29', '2026-04-27 15:26:20', NULL),
(5, 'Office Chair', 'Office chair', NULL, 6, 2, 1, '2026-02-15 07:04:29', '2026-04-27 15:26:20', NULL),
(6, 'Filing Cabinet', 'Document filing cabinet', NULL, 6, 2, 1, '2026-02-15 07:04:29', '2026-04-27 15:26:20', NULL),
(7, 'Air Conditioner', 'Wall mounted air conditioner', NULL, 8, 2, 1, '2026-02-15 07:04:29', '2026-04-27 15:26:20', NULL),
(8, 'Projector', 'Presentation projector', NULL, 7, 2, 1, '2026-02-15 07:04:29', '2026-04-27 15:26:20', NULL),
(9, 'Telephone Set', 'Office telephone set', NULL, 2, 2, 1, '2026-02-15 07:04:29', '2026-04-27 15:26:20', NULL),
(10, 'Server Equipment', 'Server and rack equipment', NULL, 7, 2, 1, '2026-02-15 07:04:29', '2026-04-27 15:26:20', NULL),
(11, 'Calculator', 'Semi-expendable calculation device', 'article_images/QIppHOGA8MU3nN3dmVZ5pvhXpniUV41MSYlJjByE.jpg', 11, 2, 1, '2026-02-15 07:04:29', '2026-03-22 06:44:10', NULL),
(12, 'Stapler', 'Semi-expendable office tool', NULL, 11, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(13, 'Hole Puncher', 'Semi-expendable office tool', NULL, 11, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(14, 'Office Supplies Set', 'Semi-expendable office supplies bundle', NULL, 11, 3, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(15, 'Table Fan', 'Semi-expendable ventilation equipment', NULL, 12, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(16, 'Extension Cord', 'Semi-expendable electrical accessory', NULL, 12, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(17, 'Office Lamp', 'Semi-expendable lighting equipment', NULL, 11, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(18, 'Waste Basket', 'Semi-expendable office accessory', NULL, 6, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(19, 'Clock/Watch', 'Semi-expendable timekeeping device', NULL, 11, 2, 1, '2026-02-15 07:04:29', '2026-02-15 07:04:29', NULL),
(20, 'Hand Tools Set', 'Semi-expendable maintenance tools', NULL, 12, 3, 1, '2026-02-15 07:04:29', '2026-03-21 06:51:02', NULL),
(21, 'test1', 'test1', 'article_images/3WjLLOLBHePwbScWVUdIw6GFmt4iyjeCOoIZZIVM.png', 15, 2, 1, '2026-03-21 06:51:27', '2026-04-27 15:09:35', '2026-04-27 15:09:35'),
(22, 'huhu', NULL, NULL, 4, 2, 1, '2026-03-22 06:53:05', '2026-03-22 06:53:10', '2026-03-22 06:53:10'),
(23, 'Action Camera', 'Portable video capture device', NULL, 21, 2, 1, '2026-04-27 15:25:49', '2026-04-27 15:26:20', NULL),
(24, 'Electric Kettle', 'Small office appliance for pantry use', NULL, 18, 2, 1, '2026-04-27 15:25:49', '2026-04-27 15:26:20', NULL),
(25, 'Fire Extinguisher', 'Portable fire suppression device', NULL, 25, 2, 1, '2026-04-27 15:25:49', '2026-04-27 15:26:20', NULL),
(26, 'Labeler', 'Labeling device for record management', NULL, 27, 2, 1, '2026-04-27 15:25:49', '2026-04-27 15:26:20', NULL),
(27, 'Multimeter', 'Portable electrical measuring device', NULL, 24, 2, 1, '2026-04-27 15:25:49', '2026-04-27 15:26:20', NULL),
(28, 'Portable Projector Kit', 'Portable projector with presentation accessories', NULL, 19, 2, 1, '2026-04-27 15:25:49', '2026-04-27 15:26:20', NULL),
(29, 'Portable Speaker', 'Portable audio speaker for meetings and briefings', NULL, 20, 2, 1, '2026-04-27 15:25:49', '2026-04-27 15:26:20', NULL),
(30, 'Rechargeable Emergency Lamp', 'Rechargeable lighting device for emergency use', NULL, 26, 2, 1, '2026-04-27 15:25:49', '2026-04-27 15:26:20', NULL),
(31, 'Surge Protector', 'Power strip with surge protection', NULL, 22, 2, 1, '2026-04-27 15:25:49', '2026-04-27 15:26:20', NULL),
(32, 'USB Flash Drive', 'Portable storage device for files and transfer', NULL, 17, 2, 1, '2026-04-27 15:25:49', '2026-04-27 15:26:20', NULL),
(33, 'Cleaning Kit', 'Cleaning kit for facilities maintenance', NULL, 29, 3, 1, '2026-04-27 15:25:49', '2026-04-27 15:26:20', NULL),
(34, 'Hand Tool Kit', 'Basic maintenance hand tool kit', NULL, 23, 3, 1, '2026-04-27 15:25:49', '2026-04-27 15:26:20', NULL),
(35, 'Keyboard and Mouse Bundle', 'Mice and keyboard set for office and ICT use', NULL, 16, 3, 1, '2026-04-27 15:25:49', '2026-04-27 15:26:20', NULL),
(36, 'LAN Cable Set', 'Network cable and adapter set', NULL, 28, 3, 1, '2026-04-27 15:25:49', '2026-04-27 15:26:20', NULL);

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

--
-- Dumping data for table `assetrequest`
--

INSERT INTO `assetrequest` (`RequestID`, `RequestNumber`, `RequestedBy`, `DepartmentID`, `Purpose`, `Status`, `ReviewedBy`, `ReviewedAt`, `ReviewNotes`, `IssuedBy`, `IssuedAt`, `RequestDate`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'REQ-2026-DR-0088', 3, 30, 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'issued', 1, '2026-04-29 02:22:06', 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 02:22:06', '2026-04-07 16:00:00', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(2, 'REQ-2026-DR-0090', 3, 30, 'Second batch of office, ICT, and maintenance items.', 'issued', 1, '2026-04-29 04:47:24', 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-11 16:00:00', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL);

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

--
-- Dumping data for table `assetrequestdetail`
--

INSERT INTO `assetrequestdetail` (`RequestDetailID`, `RequestID`, `PropertyInventoryDetailID`, `ItemDescription`, `Unit`, `QuantityRequested`, `Purpose`, `IntendedEndUser`, `AdditionalNotes`, `PropertyClassification`, `AssignedEndUserID`, `AssignedDepartmentID`, `AdminNotes`, `IsApprovedForIssuance`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 148, 'Desktop Computer', 'unit', 1, 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'PAR', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(2, 1, 149, 'Printer/Scanner', 'unit', 1, 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'PAR', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(3, 1, 150, 'Office Chair', 'unit', 1, 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'PAR', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(4, 1, 151, 'Portable rechargeable emergency lamp', 'unit', 1, 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(5, 1, 152, 'Portable rechargeable emergency lamp', 'unit', 1, 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(6, 1, 153, 'Portable rechargeable emergency lamp', 'unit', 1, 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(7, 1, 154, '32GB USB 3.0 flash drive', 'unit', 1, 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(8, 1, 155, '32GB USB 3.0 flash drive', 'unit', 1, 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(9, 1, 156, 'Wireless keyboard and mouse set', 'set', 1, 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(10, 1, 157, 'Wireless keyboard and mouse set', 'set', 1, 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(11, 1, 158, 'Wireless keyboard and mouse set', 'set', 1, 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(12, 1, 159, 'Wireless keyboard and mouse set', 'set', 1, 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(13, 1, 160, 'Wireless keyboard and mouse set', 'set', 1, 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(14, 2, 161, 'Laptop Computer', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'PAR', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(15, 2, 162, 'Filing Cabinet', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'PAR', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(16, 2, 163, 'Filing Cabinet', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'PAR', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(17, 2, 164, 'Projector', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'PAR', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(18, 2, 165, '1.5L electric kettle', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(19, 2, 166, '1.5L electric kettle', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(20, 2, 167, '1.5L electric kettle', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(21, 2, 168, '1.5L electric kettle', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(22, 2, 169, '1.5L electric kettle', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(23, 2, 170, '1.5L electric kettle', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(24, 2, 171, '1.5L electric kettle', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(25, 2, 172, '1.5L electric kettle', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(26, 2, 173, '1.5L electric kettle', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(27, 2, 174, '1.5L electric kettle', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(28, 2, 175, 'Portable wireless speaker', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(29, 2, 176, 'Portable wireless speaker', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(30, 2, 177, 'Portable wireless speaker', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(31, 2, 178, 'Portable wireless speaker', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(32, 2, 179, '6-outlet surge protector', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(33, 2, 180, '6-outlet surge protector', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(34, 2, 181, '6-outlet surge protector', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(35, 2, 182, '6-outlet surge protector', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(36, 2, 183, '6-outlet surge protector', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(37, 2, 184, '6-outlet surge protector', 'unit', 1, 'Second batch of office, ICT, and maintenance items.', 'Chokoy Covid', 'Auto-generated from delivery receipt acknowledgment', 'ICS', 2, 30, 'Auto-generated from delivery receipt acknowledgment', 1, '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL);

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
(32, 'inventory_detail', 5, 'borrow_returned', 1, '{\"borrow_id\":1,\"borrow_number\":\"PBR-2026-0001\",\"return_id\":1,\"quantity\":1,\"condition_id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-18 13:25:11'),
(33, 'inventory_detail', 134, 'created_from_dr', 1, '{\"inventory_id\":14,\"dr_detail_id\":100,\"property_number\":\"2026-000107\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 14:54:56'),
(34, 'delivery_receipt', 42, 'created', 1, '{\"dr_number\":\"dr23\",\"supplier\":\"Los Pollos Hermanos\",\"details\":[100]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 14:54:56'),
(35, 'delivery_receipt_detail', 100, 'created', 1, '{\"dr_id\":42,\"dr_number\":\"dr23\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 14:54:56'),
(36, 'inventory_detail', 135, 'created_from_dr', 1, '{\"inventory_id\":15,\"dr_detail_id\":101,\"property_number\":\"2026-000108\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 15:17:31'),
(37, 'delivery_receipt', 43, 'created', 1, '{\"dr_number\":\"dr31\",\"supplier\":\"COVID Supplies\",\"details\":[101]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 15:17:31'),
(38, 'delivery_receipt_detail', 101, 'created', 1, '{\"dr_id\":43,\"dr_number\":\"dr31\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 15:17:31'),
(39, 'inventory_detail', 136, 'created_from_dr', 1, '{\"inventory_id\":16,\"dr_detail_id\":102,\"property_number\":\"2026-000109\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 15:22:27'),
(40, 'delivery_receipt', 44, 'created', 1, '{\"dr_number\":\"dr11\",\"supplier\":\"Cyron Suppliers\",\"details\":[102]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 15:22:27'),
(41, 'delivery_receipt_detail', 102, 'created', 1, '{\"dr_id\":44,\"dr_number\":\"dr11\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-22 15:22:27'),
(42, 'inventory_detail', 137, 'created_from_dr', 1, '{\"inventory_id\":17,\"dr_detail_id\":103,\"property_number\":\"2026-000110\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 03:40:12'),
(43, 'delivery_receipt', 45, 'created', 1, '{\"dr_number\":\"d2\",\"supplier\":\"COVID Supplies\",\"details\":[103]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 03:40:12'),
(44, 'delivery_receipt_detail', 103, 'created', 1, '{\"dr_id\":45,\"dr_number\":\"d2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 03:40:12'),
(45, 'inventory_detail', 138, 'created_from_dr', 1, '{\"inventory_id\":18,\"dr_detail_id\":104,\"property_number\":\"2026-000111\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 03:43:37'),
(46, 'delivery_receipt', 46, 'created', 1, '{\"dr_number\":\"d21\",\"supplier\":\"COVID Supplies\",\"details\":[104]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 03:43:37'),
(47, 'delivery_receipt_detail', 104, 'created', 1, '{\"dr_id\":46,\"dr_number\":\"d21\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 03:43:37'),
(48, 'property_transfer', 2, 'created', 3, '{\"transfer_number\":\"PTR-2026-0002\",\"from_department_id\":23,\"to_department_id\":30,\"items\":[30]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 03:56:24'),
(49, 'inventory_detail', 30, 'transfer_requested', 3, '{\"transfer_id\":2,\"transfer_number\":\"PTR-2026-0002\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 03:56:24'),
(50, 'property_transfer', 2, 'releasing_approved', 6, '{\"transfer_number\":\"PTR-2026-0002\",\"requested_count\":1,\"approved_count\":1,\"approved_quantities\":{\"2\":1},\"reason\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 03:57:51'),
(51, 'inventory_detail', 30, 'transfer_releasing_approved', 6, '{\"transfer_id\":2,\"transfer_number\":\"PTR-2026-0002\",\"approved_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 03:57:51'),
(52, 'property_transfer', 2, 'approved', 1, '{\"transfer_number\":\"PTR-2026-0002\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 03:58:34'),
(53, 'inventory_detail', 30, 'transfer_approved', 1, '{\"transfer_id\":2,\"transfer_number\":\"PTR-2026-0002\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 03:58:34'),
(54, 'property_transfer', 2, 'releasing_signed', 1, '{\"transfer_number\":\"PTR-2026-0002\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 03:58:56'),
(55, 'inventory_detail', 30, 'transfer_releasing_signed', 1, '{\"transfer_id\":2,\"transfer_number\":\"PTR-2026-0002\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 03:58:56'),
(56, 'property_transfer', 2, 'receiving_signed', 3, '{\"transfer_number\":\"PTR-2026-0002\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 04:00:25'),
(57, 'inventory_detail', 30, 'transfer_receiving_signed', 3, '{\"transfer_id\":2,\"transfer_number\":\"PTR-2026-0002\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 04:00:25'),
(58, 'property_transfer_item', 2, 'transferred', 3, '{\"transfer_id\":2,\"transfer_number\":\"PTR-2026-0002\",\"inventory_detail_id\":30,\"from_department_id\":23,\"to_department_id\":30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 04:00:25'),
(59, 'inventory_detail', 30, 'transfer_completed', 3, '{\"transfer_id\":2,\"transfer_number\":\"PTR-2026-0002\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 04:00:25'),
(60, 'property_transfer', 2, 'completed', 3, '{\"transfer_number\":\"PTR-2026-0002\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 04:00:25'),
(61, 'property_transfer', 3, 'created', 6, '{\"transfer_number\":\"PTR-2026-0003\",\"from_department_id\":30,\"to_department_id\":23,\"items\":[30]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 04:03:42'),
(62, 'inventory_detail', 30, 'transfer_requested', 6, '{\"transfer_id\":3,\"transfer_number\":\"PTR-2026-0003\",\"from_department_id\":30,\"to_department_id\":23,\"from_end_user_id\":2,\"to_end_user_id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 04:03:43'),
(63, 'property_transfer', 3, 'releasing_approved', 3, '{\"transfer_number\":\"PTR-2026-0003\",\"requested_count\":1,\"approved_count\":1,\"approved_quantities\":{\"3\":1},\"reason\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 04:04:35'),
(64, 'inventory_detail', 30, 'transfer_releasing_approved', 3, '{\"transfer_id\":3,\"transfer_number\":\"PTR-2026-0003\",\"approved_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 04:04:35'),
(65, 'property_transfer', 3, 'approved', 1, '{\"transfer_number\":\"PTR-2026-0003\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 04:05:17'),
(66, 'inventory_detail', 30, 'transfer_approved', 1, '{\"transfer_id\":3,\"transfer_number\":\"PTR-2026-0003\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 04:05:17'),
(67, 'property_transfer', 3, 'releasing_signed', 1, '{\"transfer_number\":\"PTR-2026-0003\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 04:05:25'),
(68, 'inventory_detail', 30, 'transfer_releasing_signed', 1, '{\"transfer_id\":3,\"transfer_number\":\"PTR-2026-0003\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-24 04:05:25'),
(69, 'property_transfer', 3, 'receiving_signed', 6, '{\"transfer_number\":\"PTR-2026-0003\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 04:06:22'),
(70, 'inventory_detail', 30, 'transfer_receiving_signed', 6, '{\"transfer_id\":3,\"transfer_number\":\"PTR-2026-0003\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 04:06:22'),
(71, 'property_transfer_item', 3, 'transferred', 6, '{\"transfer_id\":3,\"transfer_number\":\"PTR-2026-0003\",\"inventory_detail_id\":30,\"from_department_id\":30,\"to_department_id\":23}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 04:06:22'),
(72, 'inventory_detail', 30, 'transfer_completed', 6, '{\"transfer_id\":3,\"transfer_number\":\"PTR-2026-0003\",\"from_department_id\":30,\"to_department_id\":23,\"from_end_user_id\":2,\"to_end_user_id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 04:06:22'),
(73, 'property_transfer', 3, 'completed', 6, '{\"transfer_number\":\"PTR-2026-0003\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-24 04:06:22'),
(74, 'inventory_detail', 96, 'service_record_created', 1, '{\"service_record_id\":1,\"service_record_detail_id\":1,\"service_number\":\"SVR-2026-0001\",\"status\":\"pending\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-25 13:41:50'),
(75, 'asset_service_record', 1, 'created', 1, '{\"service_number\":\"SVR-2026-0001\",\"status\":\"pending\",\"details_count\":1,\"reporting_department_id\":30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-25 13:41:50'),
(76, 'property_borrow', 2, 'created', 3, '{\"borrow_number\":\"PBR-2026-0002\",\"from_department_id\":23,\"to_department_id\":30,\"due_date\":\"2026-03-31\",\"items\":[30]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-25 13:58:31'),
(77, 'inventory_detail', 30, 'borrow_requested', 3, '{\"borrow_id\":2,\"borrow_number\":\"PBR-2026-0002\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-25 13:58:31'),
(78, 'property_borrow', 3, 'created', 3, '{\"borrow_number\":\"PBR-2026-0003\",\"from_department_id\":23,\"to_department_id\":30,\"due_date\":\"2026-03-30\",\"items\":[28]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-25 14:00:36'),
(79, 'inventory_detail', 28, 'borrow_requested', 3, '{\"borrow_id\":3,\"borrow_number\":\"PBR-2026-0003\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-25 14:00:36'),
(80, 'property_borrow', 3, 'releasing_approved', 6, '{\"borrow_number\":\"PBR-2026-0003\",\"requested_count\":1,\"approved_count\":1,\"reason\":\"Okay\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-25 14:01:25'),
(81, 'property_borrow', 3, 'approved', 1, '{\"borrow_number\":\"PBR-2026-0003\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-25 14:01:58'),
(82, 'inventory_detail', 28, 'borrow_approved', 1, '{\"borrow_id\":3,\"borrow_number\":\"PBR-2026-0003\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-25 14:01:58'),
(83, 'property_borrow', 3, 'borrowed', 1, '{\"borrow_number\":\"PBR-2026-0003\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-25 14:01:58'),
(84, 'property_return', 2, 'created', 1, '{\"borrow_id\":3,\"borrow_number\":\"PBR-2026-0003\",\"items_returned\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-25 14:03:14'),
(85, 'property_borrow', 3, 'returned', 1, '{\"borrow_number\":\"PBR-2026-0003\",\"items_returned\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-25 14:03:14'),
(86, 'inventory_detail', 28, 'borrow_returned', 1, '{\"borrow_id\":3,\"borrow_number\":\"PBR-2026-0003\",\"return_id\":2,\"quantity\":1,\"condition_id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-25 14:03:14'),
(87, 'asset_service_record_detail', 1, 'updated', 1, '{\"service_record_id\":1,\"old_status\":\"pending\",\"new_status\":\"work_approved\",\"inventory_detail_id\":96}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 12:09:50'),
(88, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"pending\",\"new_status\":\"work_approved\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 12:09:50'),
(89, 'asset_service_record', 1, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"pending\",\"new_status\":\"work_approved\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 12:09:50'),
(90, 'asset_service_record_detail', 1, 'updated', 1, '{\"service_record_id\":1,\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\",\"inventory_detail_id\":96}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 12:21:17'),
(91, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 12:21:17'),
(92, 'asset_service_record', 1, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 12:21:17'),
(93, 'asset_service_record_detail', 1, 'updated', 1, '{\"service_record_id\":1,\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\",\"inventory_detail_id\":96}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 12:34:32'),
(94, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 12:34:32'),
(95, 'asset_service_record', 1, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 12:34:32'),
(96, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:39:05'),
(97, 'asset_service_record', 1, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:39:05'),
(98, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:39:25'),
(99, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:45:17'),
(100, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:45:46'),
(101, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:49:35'),
(102, 'asset_service_record_detail', 1, 'updated', 1, '{\"service_record_id\":1,\"old_status\":\"repair_in_progress\",\"new_status\":\"repair_in_progress\",\"inventory_detail_id\":96}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:49:47'),
(103, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"repair_in_progress\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:49:47'),
(104, 'asset_service_record', 1, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"repair_in_progress\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:49:47'),
(105, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:49:52'),
(106, 'asset_service_record', 1, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:49:52'),
(107, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:53:20'),
(108, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:55:45'),
(109, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:55:54'),
(110, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 15:57:55'),
(111, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 16:02:01'),
(112, 'asset_service_record_detail', 1, 'updated', 1, '{\"service_record_id\":1,\"old_status\":\"repair_in_progress\",\"new_status\":\"repair_in_progress\",\"inventory_detail_id\":96}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 16:02:19'),
(113, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"repair_in_progress\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 16:02:19'),
(114, 'asset_service_record', 1, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"repair_in_progress\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 16:02:19'),
(115, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 16:02:32'),
(116, 'asset_service_record', 1, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 16:02:32'),
(117, 'asset_service_record_detail', 1, 'updated', 1, '{\"service_record_id\":1,\"old_status\":\"repair_in_progress\",\"new_status\":\"fixed\",\"inventory_detail_id\":96}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 16:02:48'),
(118, 'asset_service_record', 1, 'updated', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"fixed\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 16:02:48'),
(119, 'asset_service_record', 1, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0001\",\"old_status\":\"verification\",\"new_status\":\"fixed\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-28 16:02:48'),
(120, 'inventory_detail', 138, 'service_record_created', 1, '{\"service_record_id\":2,\"service_record_detail_id\":2,\"service_number\":\"SVR-2026-0002\",\"status\":\"pending\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 11:12:16'),
(121, 'asset_service_record', 2, 'created', 1, '{\"service_number\":\"SVR-2026-0002\",\"status\":\"pending\",\"details_count\":1,\"report_source_id\":30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 11:12:16'),
(122, 'asset_service_record_detail', 2, 'updated', 1, '{\"service_record_id\":2,\"old_status\":\"pending\",\"new_status\":\"work_approved\",\"inventory_detail_id\":138}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 11:12:47'),
(123, 'asset_service_record', 2, 'updated', 1, '{\"service_number\":\"SVR-2026-0002\",\"old_status\":\"pending\",\"new_status\":\"work_approved\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 11:12:47'),
(124, 'asset_service_record', 2, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0002\",\"old_status\":\"pending\",\"new_status\":\"work_approved\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 11:12:47'),
(125, 'asset_service_record_detail', 2, 'updated', 1, '{\"service_record_id\":2,\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\",\"inventory_detail_id\":138}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 11:12:51'),
(126, 'asset_service_record', 2, 'updated', 1, '{\"service_number\":\"SVR-2026-0002\",\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 11:12:52'),
(127, 'asset_service_record', 2, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0002\",\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 11:12:52'),
(128, 'asset_service_record', 2, 'updated', 1, '{\"service_number\":\"SVR-2026-0002\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 11:12:56'),
(129, 'asset_service_record', 2, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0002\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 11:12:56'),
(130, 'asset_service_record_detail', 2, 'updated', 1, '{\"service_record_id\":2,\"old_status\":\"repair_in_progress\",\"new_status\":\"fixed\",\"inventory_detail_id\":138}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 11:13:02'),
(131, 'asset_service_record', 2, 'updated', 1, '{\"service_number\":\"SVR-2026-0002\",\"old_status\":\"verification\",\"new_status\":\"fixed\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 11:13:02'),
(132, 'asset_service_record', 2, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0002\",\"old_status\":\"verification\",\"new_status\":\"fixed\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 11:13:02'),
(133, 'inventory_detail', 139, 'created_from_dr', 1, '{\"inventory_id\":19,\"dr_detail_id\":105,\"property_number\":\"2026-000112\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 12:32:46'),
(134, 'inventory_detail', 140, 'created_from_dr', 1, '{\"inventory_id\":19,\"dr_detail_id\":106,\"property_number\":\"2026-000113\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 12:32:46'),
(135, 'delivery_receipt', 47, 'created', 1, '{\"dr_number\":\"DR-TEST-1\",\"supplier\":\"Los Pollos Hermanos\",\"details\":[105,106]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 12:32:46'),
(136, 'delivery_receipt_detail', 105, 'created', 1, '{\"dr_id\":47,\"dr_number\":\"DR-TEST-1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 12:32:46'),
(137, 'delivery_receipt_detail', 106, 'created', 1, '{\"dr_id\":47,\"dr_number\":\"DR-TEST-1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 12:32:46'),
(138, 'inventory_detail', 141, 'created_from_dr', 1, '{\"inventory_id\":20,\"dr_detail_id\":107,\"property_number\":\"2026-000114\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 12:52:16'),
(139, 'inventory_detail', 142, 'created_from_dr', 1, '{\"inventory_id\":20,\"dr_detail_id\":108,\"property_number\":\"2026-000115\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 12:52:16'),
(140, 'delivery_receipt', 48, 'created', 1, '{\"dr_number\":\"DR-TEST-2\",\"supplier\":\"Los Pollos Hermanos\",\"details\":[107,108]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 12:52:16'),
(141, 'delivery_receipt_detail', 107, 'created', 1, '{\"dr_id\":48,\"dr_number\":\"DR-TEST-2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 12:52:16'),
(142, 'delivery_receipt_detail', 108, 'created', 1, '{\"dr_id\":48,\"dr_number\":\"DR-TEST-2\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 12:52:16'),
(143, 'property_transfer', 4, 'created', 3, '{\"transfer_number\":\"PTR-2026-0004\",\"from_department_id\":23,\"to_department_id\":30,\"items\":[74]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:00:41'),
(144, 'inventory_detail', 74, 'transfer_requested', 3, '{\"transfer_id\":4,\"transfer_number\":\"PTR-2026-0004\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:00:41'),
(145, 'property_transfer', 4, 'releasing_approved', 6, '{\"transfer_number\":\"PTR-2026-0004\",\"requested_count\":1,\"approved_count\":1,\"approved_quantities\":{\"4\":1},\"reason\":\"okay\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:01:57'),
(146, 'inventory_detail', 74, 'transfer_releasing_approved', 6, '{\"transfer_id\":4,\"transfer_number\":\"PTR-2026-0004\",\"approved_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:01:57'),
(147, 'property_transfer', 4, 'approved', 1, '{\"transfer_number\":\"PTR-2026-0004\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:02:38'),
(148, 'inventory_detail', 74, 'transfer_approved', 1, '{\"transfer_id\":4,\"transfer_number\":\"PTR-2026-0004\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:02:38'),
(149, 'property_transfer', 4, 'releasing_signed', 1, '{\"transfer_number\":\"PTR-2026-0004\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:03:00'),
(150, 'inventory_detail', 74, 'transfer_releasing_signed', 1, '{\"transfer_id\":4,\"transfer_number\":\"PTR-2026-0004\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:03:00'),
(151, 'inventory_detail', 143, 'created_from_dr', 1, '{\"inventory_id\":21,\"dr_detail_id\":109,\"property_number\":\"2026-000116\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:15:35'),
(152, 'inventory_detail', 144, 'created_from_dr', 1, '{\"inventory_id\":21,\"dr_detail_id\":110,\"property_number\":\"2026-000117\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:15:35'),
(153, 'delivery_receipt', 49, 'created', 1, '{\"dr_number\":\"DR-TEST-3\",\"supplier\":\"Los Pollos Hermanos\",\"details\":[109,110]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:15:35'),
(154, 'delivery_receipt_detail', 109, 'created', 1, '{\"dr_id\":49,\"dr_number\":\"DR-TEST-3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:15:35'),
(155, 'delivery_receipt_detail', 110, 'created', 1, '{\"dr_id\":49,\"dr_number\":\"DR-TEST-3\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:15:35'),
(156, 'property_transfer', 5, 'created', 3, '{\"transfer_number\":\"PTR-2026-0005\",\"from_department_id\":23,\"to_department_id\":30,\"items\":[28]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:23:01'),
(157, 'inventory_detail', 28, 'transfer_requested', 3, '{\"transfer_id\":5,\"transfer_number\":\"PTR-2026-0005\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:23:01'),
(158, 'property_transfer', 5, 'releasing_approved', 6, '{\"transfer_number\":\"PTR-2026-0005\",\"requested_count\":1,\"approved_count\":1,\"approved_quantities\":{\"5\":1},\"reason\":\"okay\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:24:20'),
(159, 'inventory_detail', 28, 'transfer_releasing_approved', 6, '{\"transfer_id\":5,\"transfer_number\":\"PTR-2026-0005\",\"approved_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:24:20'),
(160, 'property_transfer', 5, 'approved', 1, '{\"transfer_number\":\"PTR-2026-0005\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:24:53'),
(161, 'inventory_detail', 28, 'transfer_approved', 1, '{\"transfer_id\":5,\"transfer_number\":\"PTR-2026-0005\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:24:53'),
(162, 'property_transfer', 5, 'releasing_signed', 1, '{\"transfer_number\":\"PTR-2026-0005\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:25:10'),
(163, 'inventory_detail', 28, 'transfer_releasing_signed', 1, '{\"transfer_id\":5,\"transfer_number\":\"PTR-2026-0005\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:25:10'),
(164, 'property_transfer', 5, 'receiving_signed', 3, '{\"transfer_number\":\"PTR-2026-0005\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:26:06'),
(165, 'inventory_detail', 28, 'transfer_receiving_signed', 3, '{\"transfer_id\":5,\"transfer_number\":\"PTR-2026-0005\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:26:06'),
(166, 'property_transfer_item', 5, 'transferred', 3, '{\"transfer_id\":5,\"transfer_number\":\"PTR-2026-0005\",\"inventory_detail_id\":28,\"from_department_id\":23,\"to_department_id\":30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:26:06'),
(167, 'inventory_detail', 28, 'transfer_completed', 3, '{\"transfer_id\":5,\"transfer_number\":\"PTR-2026-0005\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:26:06'),
(168, 'property_transfer', 5, 'completed', 3, '{\"transfer_number\":\"PTR-2026-0005\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:26:06'),
(169, 'property_borrow', 4, 'created', 3, '{\"borrow_number\":\"PBR-2026-0004\",\"from_department_id\":23,\"to_department_id\":30,\"due_date\":\"2026-04-01\",\"items\":[73]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:29:43'),
(170, 'inventory_detail', 73, 'borrow_requested', 3, '{\"borrow_id\":4,\"borrow_number\":\"PBR-2026-0004\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:29:43');
INSERT INTO `asset_audit_logs` (`AuditID`, `EntityType`, `EntityID`, `Action`, `PerformedBy`, `Details`, `IPAddress`, `UserAgent`, `CreatedAt`) VALUES
(171, 'property_borrow', 4, 'releasing_approved', 6, '{\"borrow_number\":\"PBR-2026-0004\",\"requested_count\":1,\"approved_count\":1,\"reason\":\"yes\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-03-29 13:30:53'),
(172, 'property_borrow', 4, 'approved', 1, '{\"borrow_number\":\"PBR-2026-0004\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:31:19'),
(173, 'inventory_detail', 73, 'borrow_approved', 1, '{\"borrow_id\":4,\"borrow_number\":\"PBR-2026-0004\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:31:19'),
(174, 'property_borrow', 4, 'borrowed', 1, '{\"borrow_number\":\"PBR-2026-0004\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:31:19'),
(175, 'property_return', 3, 'created', 1, '{\"borrow_id\":4,\"borrow_number\":\"PBR-2026-0004\",\"items_returned\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:34:27'),
(176, 'property_borrow', 4, 'returned', 1, '{\"borrow_number\":\"PBR-2026-0004\",\"items_returned\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:34:27'),
(177, 'inventory_detail', 73, 'borrow_returned', 1, '{\"borrow_id\":4,\"borrow_number\":\"PBR-2026-0004\",\"return_id\":3,\"quantity\":1,\"condition_id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:34:27'),
(178, 'inventory_detail', 138, 'service_record_created', 1, '{\"service_record_id\":3,\"service_record_detail_id\":3,\"service_number\":\"SVR-2026-0003\",\"status\":\"pending\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:37:47'),
(179, 'asset_service_record', 3, 'created', 1, '{\"service_number\":\"SVR-2026-0003\",\"status\":\"pending\",\"details_count\":1,\"report_source_id\":30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:37:47'),
(180, 'asset_service_record_detail', 3, 'updated', 1, '{\"service_record_id\":3,\"old_status\":\"pending\",\"new_status\":\"work_approved\",\"inventory_detail_id\":138}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:38:04'),
(181, 'asset_service_record', 3, 'updated', 1, '{\"service_number\":\"SVR-2026-0003\",\"old_status\":\"pending\",\"new_status\":\"work_approved\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:38:04'),
(182, 'asset_service_record', 3, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0003\",\"old_status\":\"pending\",\"new_status\":\"work_approved\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:38:04'),
(183, 'asset_service_record_detail', 3, 'updated', 1, '{\"service_record_id\":3,\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\",\"inventory_detail_id\":138}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:38:17'),
(184, 'asset_service_record', 3, 'updated', 1, '{\"service_number\":\"SVR-2026-0003\",\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:38:17'),
(185, 'asset_service_record', 3, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0003\",\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:38:17'),
(186, 'asset_service_record', 3, 'updated', 1, '{\"service_number\":\"SVR-2026-0003\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:38:30'),
(187, 'asset_service_record', 3, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0003\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:38:30'),
(188, 'asset_service_record_detail', 3, 'updated', 1, '{\"service_record_id\":3,\"old_status\":\"repair_in_progress\",\"new_status\":\"repair_in_progress\",\"inventory_detail_id\":138}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:38:45'),
(189, 'asset_service_record', 3, 'updated', 1, '{\"service_number\":\"SVR-2026-0003\",\"old_status\":\"verification\",\"new_status\":\"repair_in_progress\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:38:45'),
(190, 'asset_service_record', 3, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0003\",\"old_status\":\"verification\",\"new_status\":\"repair_in_progress\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:38:45'),
(191, 'asset_service_record', 3, 'updated', 1, '{\"service_number\":\"SVR-2026-0003\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:38:49'),
(192, 'asset_service_record', 3, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0003\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:38:49'),
(193, 'asset_service_record_detail', 3, 'updated', 1, '{\"service_record_id\":3,\"old_status\":\"repair_in_progress\",\"new_status\":\"fixed\",\"inventory_detail_id\":138}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:39:14'),
(194, 'asset_service_record', 3, 'updated', 1, '{\"service_number\":\"SVR-2026-0003\",\"old_status\":\"verification\",\"new_status\":\"fixed\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:39:14'),
(195, 'asset_service_record', 3, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0003\",\"old_status\":\"verification\",\"new_status\":\"fixed\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-29 13:39:14'),
(196, 'property_transfer', 6, 'created', 3, '{\"transfer_number\":\"PTR-2026-0006\",\"from_department_id\":23,\"to_department_id\":30,\"items\":[29]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-03 17:05:47'),
(197, 'inventory_detail', 29, 'transfer_requested', 3, '{\"transfer_id\":6,\"transfer_number\":\"PTR-2026-0006\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-03 17:05:47'),
(198, 'property_transfer', 6, 'approved', 1, '{\"transfer_number\":\"PTR-2026-0006\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 17:06:39'),
(199, 'inventory_detail', 29, 'transfer_approved', 1, '{\"transfer_id\":6,\"transfer_number\":\"PTR-2026-0006\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 17:06:39'),
(200, 'property_transfer', 6, 'releasing_approved', 6, '{\"transfer_number\":\"PTR-2026-0006\",\"requested_count\":1,\"approved_count\":1,\"approved_quantities\":{\"6\":1},\"reason\":\"sge\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-03 17:08:28'),
(201, 'inventory_detail', 29, 'transfer_releasing_approved', 6, '{\"transfer_id\":6,\"transfer_number\":\"PTR-2026-0006\",\"approved_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-03 17:08:28'),
(202, 'property_transfer_item', 6, 'transferred', 1, '{\"transfer_id\":6,\"transfer_number\":\"PTR-2026-0006\",\"inventory_detail_id\":29,\"from_department_id\":23,\"to_department_id\":30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 17:09:22'),
(203, 'inventory_detail', 29, 'transfer_completed', 1, '{\"transfer_id\":6,\"transfer_number\":\"PTR-2026-0006\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 17:09:22'),
(204, 'property_transfer', 6, 'completed', 1, '{\"transfer_number\":\"PTR-2026-0006\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 17:09:22'),
(205, 'property_borrow', 5, 'created', 3, '{\"borrow_number\":\"PBR-2026-0005\",\"from_department_id\":23,\"to_department_id\":30,\"due_date\":\"2026-04-06\",\"items\":[25]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-03 17:36:35'),
(206, 'inventory_detail', 25, 'borrow_requested', 3, '{\"borrow_id\":5,\"borrow_number\":\"PBR-2026-0005\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-03 17:36:35'),
(207, 'property_borrow', 5, 'approved', 1, '{\"borrow_number\":\"PBR-2026-0005\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 17:37:23'),
(208, 'inventory_detail', 25, 'borrow_approved', 1, '{\"borrow_id\":5,\"borrow_number\":\"PBR-2026-0005\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 17:37:23'),
(209, 'property_borrow', 5, 'releasing_approved', 6, '{\"borrow_number\":\"PBR-2026-0005\",\"requested_count\":1,\"approved_count\":1,\"reason\":\"ok!!!!!!!!!!!!!!!!!!!\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '2026-04-03 17:38:24'),
(210, 'property_borrow', 5, 'borrowed', 1, '{\"borrow_number\":\"PBR-2026-0005\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 17:38:56'),
(211, 'property_return', 4, 'created', 1, '{\"borrow_id\":5,\"borrow_number\":\"PBR-2026-0005\",\"items_returned\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 17:40:55'),
(212, 'property_borrow', 5, 'returned', 1, '{\"borrow_number\":\"PBR-2026-0005\",\"items_returned\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 17:40:55'),
(213, 'inventory_detail', 25, 'borrow_returned', 1, '{\"borrow_id\":5,\"borrow_number\":\"PBR-2026-0005\",\"return_id\":4,\"quantity\":1,\"condition_id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-03 17:40:55'),
(214, 'inventory_detail', 144, 'service_record_created', 1, '{\"service_record_id\":4,\"service_record_detail_id\":4,\"service_number\":\"SVR-2026-0004\",\"status\":\"pending\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:22:05'),
(215, 'asset_service_record', 4, 'created', 1, '{\"service_number\":\"SVR-2026-0004\",\"status\":\"pending\",\"details_count\":1,\"report_source_id\":30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:22:05'),
(216, 'asset_service_record_detail', 4, 'updated', 1, '{\"service_record_id\":4,\"old_status\":\"pending\",\"new_status\":\"work_approved\",\"inventory_detail_id\":144}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:22:28'),
(217, 'asset_service_record', 4, 'updated', 1, '{\"service_number\":\"SVR-2026-0004\",\"old_status\":\"pending\",\"new_status\":\"work_approved\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:22:28'),
(218, 'asset_service_record', 4, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0004\",\"old_status\":\"pending\",\"new_status\":\"work_approved\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:22:28'),
(219, 'asset_service_record_detail', 4, 'updated', 1, '{\"service_record_id\":4,\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\",\"inventory_detail_id\":144}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:22:33'),
(220, 'asset_service_record', 4, 'updated', 1, '{\"service_number\":\"SVR-2026-0004\",\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:22:33'),
(221, 'asset_service_record', 4, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0004\",\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:22:33'),
(222, 'asset_service_record', 4, 'updated', 1, '{\"service_number\":\"SVR-2026-0004\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:22:37'),
(223, 'asset_service_record', 4, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0004\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:22:37'),
(224, 'asset_service_record_detail', 4, 'updated', 1, '{\"service_record_id\":4,\"old_status\":\"repair_in_progress\",\"new_status\":\"unserviceable\",\"inventory_detail_id\":144}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:22:46'),
(225, 'asset_service_record', 4, 'updated', 1, '{\"service_number\":\"SVR-2026-0004\",\"old_status\":\"verification\",\"new_status\":\"unserviceable\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:22:46'),
(226, 'asset_service_record', 4, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0004\",\"old_status\":\"verification\",\"new_status\":\"unserviceable\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:22:46'),
(227, 'property_waste_report', 1, 'updated', 1, '{\"waste_number\":\"WST-2026-0001\",\"old_status\":\"draft\",\"new_status\":\"for_approval\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:24:29'),
(228, 'property_waste_report', 1, 'updated', 1, '{\"waste_number\":\"WST-2026-0001\",\"old_status\":\"for_approval\",\"new_status\":\"for_approval\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:33:33'),
(229, 'property_waste_report', 1, 'updated', 1, '{\"waste_number\":\"WST-2026-0001\",\"old_status\":\"for_approval\",\"new_status\":\"for_approval\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:33:34'),
(230, 'property_waste_report', 1, 'updated', 1, '{\"waste_number\":\"WST-2026-0001\",\"old_status\":\"approved\",\"new_status\":\"approved\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:41:25'),
(231, 'property_waste_report', 1, 'updated', 1, '{\"waste_number\":\"WST-2026-0001\",\"old_status\":\"approved\",\"new_status\":\"approved\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:41:26'),
(232, 'property_waste_report', 1, 'updated', 1, '{\"waste_number\":\"WST-2026-0001\",\"old_status\":\"draft\",\"new_status\":\"draft\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:54:58'),
(233, 'property_waste_report', 1, 'updated', 1, '{\"waste_number\":\"WST-2026-0001\",\"old_status\":\"draft\",\"new_status\":\"draft\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:55:12'),
(234, 'property_waste_report', 1, 'updated', 1, '{\"waste_number\":\"WST-2026-0001\",\"old_status\":\"draft\",\"new_status\":\"completed\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 09:55:17'),
(235, 'inventory_detail', 143, 'service_record_created', 1, '{\"service_record_id\":5,\"service_record_detail_id\":5,\"service_number\":\"SVR-2026-0005\",\"status\":\"pending\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 10:23:39'),
(236, 'asset_service_record', 5, 'created', 1, '{\"service_number\":\"SVR-2026-0005\",\"status\":\"pending\",\"details_count\":1,\"report_source_id\":30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 10:23:39'),
(237, 'asset_service_record_detail', 5, 'updated', 1, '{\"service_record_id\":5,\"old_status\":\"pending\",\"new_status\":\"work_approved\",\"inventory_detail_id\":143}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 10:24:02'),
(238, 'asset_service_record', 5, 'updated', 1, '{\"service_number\":\"SVR-2026-0005\",\"old_status\":\"pending\",\"new_status\":\"work_approved\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 10:24:02'),
(239, 'asset_service_record', 5, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0005\",\"old_status\":\"pending\",\"new_status\":\"work_approved\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 10:24:02'),
(240, 'asset_service_record_detail', 5, 'updated', 1, '{\"service_record_id\":5,\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\",\"inventory_detail_id\":143}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 10:24:08'),
(241, 'asset_service_record', 5, 'updated', 1, '{\"service_number\":\"SVR-2026-0005\",\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 10:24:08'),
(242, 'asset_service_record', 5, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0005\",\"old_status\":\"work_approved\",\"new_status\":\"repair_in_progress\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 10:24:08'),
(243, 'asset_service_record', 5, 'updated', 1, '{\"service_number\":\"SVR-2026-0005\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 10:24:14'),
(244, 'asset_service_record', 5, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0005\",\"old_status\":\"repair_in_progress\",\"new_status\":\"verification\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 10:24:14'),
(245, 'asset_service_record_detail', 5, 'updated', 1, '{\"service_record_id\":5,\"old_status\":\"repair_in_progress\",\"new_status\":\"unserviceable\",\"inventory_detail_id\":143}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 10:24:24'),
(246, 'asset_service_record', 5, 'updated', 1, '{\"service_number\":\"SVR-2026-0005\",\"old_status\":\"verification\",\"new_status\":\"unserviceable\",\"details_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 10:24:24'),
(247, 'asset_service_record', 5, 'status_changed', 1, '{\"service_number\":\"SVR-2026-0005\",\"old_status\":\"verification\",\"new_status\":\"unserviceable\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-05 10:24:24'),
(248, 'property_waste_report', 2, 'updated', 1, '{\"waste_number\":\"WST-2026-0002\",\"old_status\":\"draft\",\"new_status\":\"draft\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-09 09:37:28'),
(249, 'property_waste_report', 2, 'updated', 1, '{\"waste_number\":\"WST-2026-0002\",\"old_status\":\"draft\",\"new_status\":\"draft\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-09 09:37:40'),
(250, 'property_transfer_item', 4, 'transferred', 1, '{\"transfer_id\":4,\"transfer_number\":\"PTR-2026-0004\",\"inventory_detail_id\":74,\"from_department_id\":23,\"to_department_id\":30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 03:53:09'),
(251, 'inventory_detail', 74, 'transfer_completed', 1, '{\"transfer_id\":4,\"transfer_number\":\"PTR-2026-0004\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 03:53:09'),
(252, 'property_transfer', 4, 'completed', 1, '{\"transfer_number\":\"PTR-2026-0004\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 03:53:09'),
(253, 'inventory_detail', 145, 'created_from_dr', 1, '{\"inventory_id\":52,\"dr_detail_id\":141,\"property_number\":\"2026-000118\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:05:52'),
(254, 'delivery_receipt', 86, 'created', 1, '{\"dr_number\":\"drtest12321\",\"supplier\":\"COVID Supplies\",\"details\":[141]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:05:52'),
(255, 'delivery_receipt_detail', 141, 'created', 1, '{\"dr_id\":86,\"dr_number\":\"drtest12321\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 04:05:52'),
(256, 'inventory_detail', 146, 'created_from_dr', 1, '{\"inventory_id\":53,\"dr_detail_id\":142,\"property_number\":\"2026-000119\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:18:33'),
(257, 'inventory_detail', 147, 'created_from_dr', 1, '{\"inventory_id\":53,\"dr_detail_id\":143,\"property_number\":\"2026-000120\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:18:33'),
(258, 'delivery_receipt', 87, 'created', 1, '{\"dr_number\":\"dr-malinao\",\"supplier\":\"Los Pollos Hermanos\",\"details\":[142,143]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:18:33'),
(259, 'delivery_receipt_detail', 142, 'created', 1, '{\"dr_id\":87,\"dr_number\":\"dr-malinao\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:18:33'),
(260, 'delivery_receipt_detail', 143, 'created', 1, '{\"dr_id\":87,\"dr_number\":\"dr-malinao\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:18:33'),
(261, 'property_borrow', 6, 'created', 3, '{\"borrow_number\":\"PBR-2026-0006\",\"from_department_id\":23,\"to_department_id\":30,\"due_date\":\"2026-04-30\",\"items\":[47]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-20 05:24:28'),
(262, 'inventory_detail', 47, 'borrow_requested', 3, '{\"borrow_id\":6,\"borrow_number\":\"PBR-2026-0006\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-20 05:24:28'),
(263, 'property_transfer', 7, 'created', 3, '{\"transfer_number\":\"PTR-2026-0007\",\"from_department_id\":23,\"to_department_id\":30,\"items\":[48]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-20 05:24:37'),
(264, 'inventory_detail', 48, 'transfer_requested', 3, '{\"transfer_id\":7,\"transfer_number\":\"PTR-2026-0007\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-20 05:24:37'),
(265, 'property_transfer', 7, 'approved', 1, '{\"transfer_number\":\"PTR-2026-0007\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:25:24'),
(266, 'inventory_detail', 48, 'transfer_approved', 1, '{\"transfer_id\":7,\"transfer_number\":\"PTR-2026-0007\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:25:24'),
(267, 'property_transfer', 7, 'releasing_approved', 6, '{\"transfer_number\":\"PTR-2026-0007\",\"requested_count\":1,\"approved_count\":1,\"approved_quantities\":{\"7\":1},\"reason\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-20 05:26:28'),
(268, 'inventory_detail', 48, 'transfer_releasing_approved', 6, '{\"transfer_id\":7,\"transfer_number\":\"PTR-2026-0007\",\"approved_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-04-20 05:26:28'),
(269, 'property_transfer_item', 7, 'transferred', 1, '{\"transfer_id\":7,\"transfer_number\":\"PTR-2026-0007\",\"inventory_detail_id\":48,\"from_department_id\":23,\"to_department_id\":30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:26:45'),
(270, 'inventory_detail', 48, 'transfer_completed', 1, '{\"transfer_id\":7,\"transfer_number\":\"PTR-2026-0007\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:26:45'),
(271, 'property_transfer', 7, 'completed', 1, '{\"transfer_number\":\"PTR-2026-0007\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:26:45'),
(272, 'property_borrow', 6, 'approved', 1, '{\"borrow_number\":\"PBR-2026-0006\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:27:04'),
(273, 'inventory_detail', 47, 'borrow_approved', 1, '{\"borrow_id\":6,\"borrow_number\":\"PBR-2026-0006\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-20 05:27:04'),
(274, 'property_transfer', 8, 'created', 1, '{\"transfer_number\":\"PTR-2026-0008\",\"from_department_id\":23,\"to_department_id\":30,\"items\":[25]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 07:46:21'),
(275, 'inventory_detail', 25, 'transfer_requested', 1, '{\"transfer_id\":8,\"transfer_number\":\"PTR-2026-0008\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 07:46:21'),
(276, 'property_transfer', 8, 'approved', 1, '{\"transfer_number\":\"PTR-2026-0008\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:01:18'),
(277, 'inventory_detail', 25, 'transfer_approved', 1, '{\"transfer_id\":8,\"transfer_number\":\"PTR-2026-0008\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:01:18'),
(278, 'property_transfer', 8, 'releasing_approved', 1, '{\"transfer_number\":\"PTR-2026-0008\",\"requested_count\":1,\"approved_count\":1,\"approved_quantities\":{\"8\":1},\"reason\":null}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:01:51'),
(279, 'inventory_detail', 25, 'transfer_releasing_approved', 1, '{\"transfer_id\":8,\"transfer_number\":\"PTR-2026-0008\",\"approved_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:01:51'),
(280, 'property_transfer_item', 8, 'transferred', 1, '{\"transfer_id\":8,\"transfer_number\":\"PTR-2026-0008\",\"inventory_detail_id\":25,\"from_department_id\":23,\"to_department_id\":30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:02:02'),
(281, 'inventory_detail', 25, 'transfer_completed', 1, '{\"transfer_id\":8,\"transfer_number\":\"PTR-2026-0008\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:02:02'),
(282, 'property_transfer', 8, 'completed', 1, '{\"transfer_number\":\"PTR-2026-0008\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 08:02:02'),
(283, 'property_transfer', 9, 'created', 1, '{\"transfer_number\":\"PTR-2026-0009\",\"from_department_id\":30,\"to_department_id\":23,\"items\":[147]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:03:36'),
(284, 'inventory_detail', 147, 'transfer_requested', 1, '{\"transfer_id\":9,\"transfer_number\":\"PTR-2026-0009\",\"from_department_id\":30,\"to_department_id\":23,\"from_end_user_id\":2,\"to_end_user_id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:03:36'),
(285, 'property_transfer', 9, 'rejected', 1, '{\"transfer_number\":\"PTR-2026-0009\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:04:20'),
(286, 'inventory_detail', 147, 'transfer_rejected', 1, '{\"transfer_id\":9,\"transfer_number\":\"PTR-2026-0009\",\"notes\":\"basta\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:04:20'),
(287, 'property_transfer', 10, 'created', 1, '{\"transfer_number\":\"PTR-2026-0010\",\"from_department_id\":30,\"to_department_id\":23,\"items\":[147]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:05:06'),
(288, 'inventory_detail', 147, 'transfer_requested', 1, '{\"transfer_id\":10,\"transfer_number\":\"PTR-2026-0010\",\"from_department_id\":30,\"to_department_id\":23,\"from_end_user_id\":2,\"to_end_user_id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:05:06'),
(289, 'property_transfer_item', 10, 'transferred', 1, '{\"transfer_id\":10,\"transfer_number\":\"PTR-2026-0010\",\"inventory_detail_id\":147,\"from_department_id\":30,\"to_department_id\":23}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:05:32'),
(290, 'inventory_detail', 147, 'transfer_completed', 1, '{\"transfer_id\":10,\"transfer_number\":\"PTR-2026-0010\",\"from_department_id\":30,\"to_department_id\":23,\"from_end_user_id\":2,\"to_end_user_id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:05:32'),
(291, 'property_transfer', 10, 'completed', 1, '{\"transfer_number\":\"PTR-2026-0010\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:05:32'),
(292, 'property_borrow', 7, 'created', 1, '{\"borrow_number\":\"PBR-2026-0007\",\"from_department_id\":23,\"to_department_id\":30,\"due_date\":\"2026-04-26\",\"items\":[26]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:08:44'),
(293, 'inventory_detail', 26, 'borrow_requested', 1, '{\"borrow_id\":7,\"borrow_number\":\"PBR-2026-0007\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:08:44'),
(294, 'property_borrow', 7, 'borrowed', 1, '{\"borrow_number\":\"PBR-2026-0007\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:08:59'),
(295, 'property_return', 5, 'created', 1, '{\"borrow_id\":7,\"borrow_number\":\"PBR-2026-0007\",\"items_returned\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:09:24'),
(296, 'property_borrow', 7, 'returned', 1, '{\"borrow_number\":\"PBR-2026-0007\",\"items_returned\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:09:24'),
(297, 'inventory_detail', 26, 'borrow_returned', 1, '{\"borrow_id\":7,\"borrow_number\":\"PBR-2026-0007\",\"return_id\":5,\"quantity\":1,\"condition_id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:09:24'),
(298, 'property_borrow', 7, 'completed', 1, '{\"borrow_number\":\"PBR-2026-0007\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-23 13:09:32'),
(299, 'property_transfer', 11, 'created', 1, '{\"transfer_number\":\"PTR-2026-0011\",\"from_department_id\":30,\"to_department_id\":23,\"items\":[146,12,29]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 07:25:23'),
(300, 'inventory_detail', 146, 'transfer_requested', 1, '{\"transfer_id\":11,\"transfer_number\":\"PTR-2026-0011\",\"from_department_id\":30,\"to_department_id\":23,\"from_end_user_id\":2,\"to_end_user_id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 07:25:23'),
(301, 'inventory_detail', 12, 'transfer_requested', 1, '{\"transfer_id\":11,\"transfer_number\":\"PTR-2026-0011\",\"from_department_id\":30,\"to_department_id\":23,\"from_end_user_id\":2,\"to_end_user_id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 07:25:23'),
(302, 'inventory_detail', 29, 'transfer_requested', 1, '{\"transfer_id\":11,\"transfer_number\":\"PTR-2026-0011\",\"from_department_id\":30,\"to_department_id\":23,\"from_end_user_id\":2,\"to_end_user_id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 07:25:23'),
(303, 'property_transfer', 11, 'releasing_approved', 1, '{\"transfer_number\":\"PTR-2026-0011\",\"requested_count\":3,\"approved_count\":1,\"approved_quantities\":{\"11\":1,\"12\":0,\"13\":0},\"reason\":\"basta\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 07:26:33'),
(304, 'inventory_detail', 146, 'transfer_releasing_approved', 1, '{\"transfer_id\":11,\"transfer_number\":\"PTR-2026-0011\",\"approved_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 07:26:33'),
(305, 'inventory_detail', 12, 'transfer_releasing_approved', 1, '{\"transfer_id\":11,\"transfer_number\":\"PTR-2026-0011\",\"approved_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 07:26:33'),
(306, 'inventory_detail', 29, 'transfer_releasing_approved', 1, '{\"transfer_id\":11,\"transfer_number\":\"PTR-2026-0011\",\"approved_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 07:26:33'),
(307, 'property_borrow', 8, 'created', 1, '{\"borrow_number\":\"PBR-2026-0008\",\"from_department_id\":23,\"to_department_id\":30,\"due_date\":\"2026-04-25\",\"items\":[26,27,46]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 08:19:25'),
(308, 'inventory_detail', 26, 'borrow_requested', 1, '{\"borrow_id\":8,\"borrow_number\":\"PBR-2026-0008\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 08:19:25'),
(309, 'inventory_detail', 27, 'borrow_requested', 1, '{\"borrow_id\":8,\"borrow_number\":\"PBR-2026-0008\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 08:19:25'),
(310, 'inventory_detail', 46, 'borrow_requested', 1, '{\"borrow_id\":8,\"borrow_number\":\"PBR-2026-0008\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 08:19:25'),
(311, 'property_transfer', 12, 'created', 1, '{\"transfer_number\":\"PTR-2026-0012\",\"from_department_id\":23,\"to_department_id\":30,\"items\":[147,63]}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 08:20:04'),
(312, 'inventory_detail', 147, 'transfer_requested', 1, '{\"transfer_id\":12,\"transfer_number\":\"PTR-2026-0012\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 08:20:04'),
(313, 'inventory_detail', 63, 'transfer_requested', 1, '{\"transfer_id\":12,\"transfer_number\":\"PTR-2026-0012\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 08:20:04'),
(314, 'property_transfer', 12, 'releasing_approved', 1, '{\"transfer_number\":\"PTR-2026-0012\",\"requested_count\":2,\"approved_count\":1,\"approved_quantities\":{\"14\":1,\"15\":0},\"reason\":\"dili daw pwede\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 08:34:49'),
(315, 'inventory_detail', 147, 'transfer_releasing_approved', 1, '{\"transfer_id\":12,\"transfer_number\":\"PTR-2026-0012\",\"approved_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 08:34:49'),
(316, 'inventory_detail', 63, 'transfer_releasing_approved', 1, '{\"transfer_id\":12,\"transfer_number\":\"PTR-2026-0012\",\"approved_count\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 08:34:49'),
(317, 'property_borrow', 8, 'releasing_approved', 1, '{\"borrow_number\":\"PBR-2026-0008\",\"requested_count\":3,\"approved_count\":2,\"approved_quantities\":{\"8\":1,\"9\":1,\"10\":0},\"reason\":\"nawala daw isa ka extension cord,\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 11:24:40'),
(318, 'property_borrow', 8, 'borrowed', 1, '{\"borrow_number\":\"PBR-2026-0008\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 11:40:52'),
(319, 'property_return', 6, 'created', 1, '{\"borrow_id\":8,\"borrow_number\":\"PBR-2026-0008\",\"items_returned\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 11:41:23'),
(320, 'property_borrow', 8, 'partially_returned', 1, '{\"borrow_number\":\"PBR-2026-0008\",\"items_returned\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 11:41:23'),
(321, 'inventory_detail', 27, 'borrow_returned', 1, '{\"borrow_id\":8,\"borrow_number\":\"PBR-2026-0008\",\"return_id\":6,\"quantity\":1,\"condition_id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 11:41:23'),
(322, 'property_transfer_item', 14, 'transferred', 1, '{\"transfer_id\":12,\"transfer_number\":\"PTR-2026-0012\",\"inventory_detail_id\":147,\"from_department_id\":23,\"to_department_id\":30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 11:57:58'),
(323, 'inventory_detail', 147, 'transfer_completed', 1, '{\"transfer_id\":12,\"transfer_number\":\"PTR-2026-0012\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 11:57:58'),
(324, 'property_transfer_item', 15, 'transferred', 1, '{\"transfer_id\":12,\"transfer_number\":\"PTR-2026-0012\",\"inventory_detail_id\":63,\"from_department_id\":23,\"to_department_id\":30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 11:57:58'),
(325, 'inventory_detail', 63, 'transfer_completed', 1, '{\"transfer_id\":12,\"transfer_number\":\"PTR-2026-0012\",\"from_department_id\":23,\"to_department_id\":30,\"from_end_user_id\":4,\"to_end_user_id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 11:57:58'),
(326, 'property_transfer', 12, 'completed', 1, '{\"transfer_number\":\"PTR-2026-0012\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-04-24 11:57:58');

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
-- Table structure for table `asset_service_records`
--

CREATE TABLE `asset_service_records` (
  `ServiceRecordID` bigint(20) UNSIGNED NOT NULL,
  `ServiceNumber` varchar(50) NOT NULL,
  `DateReported` date NOT NULL,
  `ReportSource` int(11) DEFAULT NULL,
  `Status` enum('pending','under_review','work_approved','repair_in_progress','fixed','verification','closed','unserviceable') NOT NULL DEFAULT 'pending',
  `RecordNotes` text DEFAULT NULL,
  `FinalNotes` text DEFAULT NULL,
  `CreatedByUserID` int(11) NOT NULL,
  `UpdatedByUserID` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset_service_records`
--

INSERT INTO `asset_service_records` (`ServiceRecordID`, `ServiceNumber`, `DateReported`, `ReportSource`, `Status`, `RecordNotes`, `FinalNotes`, `CreatedByUserID`, `UpdatedByUserID`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'SVR-2026-0001', '2026-03-25', 30, 'fixed', NULL, 'Fixed by warranty helyeah?', 1, 1, '2026-03-25 13:41:50', '2026-03-28 16:02:48', NULL),
(2, 'SVR-2026-0002', '2026-03-29', 30, 'fixed', NULL, 'fixed', 1, 1, '2026-03-29 11:12:16', '2026-03-29 11:13:02', NULL),
(3, 'SVR-2026-0003', '2026-03-29', 30, 'fixed', NULL, 'tulid good', 1, 1, '2026-03-29 13:37:47', '2026-03-29 13:39:14', NULL),
(4, 'SVR-2026-0004', '2026-04-05', 30, 'unserviceable', NULL, 'wajud', 1, 1, '2026-04-05 09:22:05', '2026-04-05 09:22:46', NULL),
(5, 'SVR-2026-0005', '2026-04-05', 30, 'unserviceable', NULL, 'hahahahaha guba di na maayo', 1, 1, '2026-04-05 10:23:39', '2026-04-05 10:24:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `asset_service_records_details`
--

CREATE TABLE `asset_service_records_details` (
  `ServiceRecordDetailID` bigint(20) UNSIGNED NOT NULL,
  `ServiceRecordID` bigint(20) UNSIGNED NOT NULL,
  `InventoryDetailID` int(11) NOT NULL,
  `PropertyNumber` varchar(100) DEFAULT NULL,
  `ItemDescription` varchar(255) DEFAULT NULL,
  `ProblemDescription` text NOT NULL,
  `HandlingDecision` enum('warranty','no_warranty') DEFAULT NULL,
  `BudgetStatus` enum('pending_check','warranty_covered','budget_available','budget_unavailable') NOT NULL DEFAULT 'pending_check',
  `Status` enum('pending','under_review','work_approved','repair_in_progress','fixed','verification','closed','unserviceable') NOT NULL DEFAULT 'pending',
  `VerificationNote` varchar(4000) DEFAULT NULL,
  `AssignedVendorID` bigint(20) UNSIGNED DEFAULT NULL,
  `AssignedSupplierID` bigint(20) UNSIGNED DEFAULT NULL,
  `EstimatedCost` decimal(12,2) DEFAULT NULL,
  `ActualCost` decimal(12,2) DEFAULT NULL,
  `WarrantyClaimNumber` varchar(100) DEFAULT NULL,
  `RepairOrderNumber` varchar(100) DEFAULT NULL,
  `InvoiceAttachment` varchar(255) DEFAULT NULL,
  `SupportingAttachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`SupportingAttachments`)),
  `CreatedByUserID` int(11) NOT NULL,
  `UpdatedByUserID` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `asset_service_records_details`
--

INSERT INTO `asset_service_records_details` (`ServiceRecordDetailID`, `ServiceRecordID`, `InventoryDetailID`, `PropertyNumber`, `ItemDescription`, `ProblemDescription`, `HandlingDecision`, `BudgetStatus`, `Status`, `VerificationNote`, `AssignedVendorID`, `AssignedSupplierID`, `EstimatedCost`, `ActualCost`, `WarrantyClaimNumber`, `RepairOrderNumber`, `InvoiceAttachment`, `SupportingAttachments`, `CreatedByUserID`, `UpdatedByUserID`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 96, '2026-000092', 'Office Chair (CH-ERG-22)', 'naguba ang ligid', 'warranty', 'warranty_covered', 'fixed', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, '[]', 1, 1, '2026-03-25 13:41:50', '2026-03-28 16:02:48', NULL),
(2, 2, 138, '2026-000111', 'MSI', 'guba ang monitor', 'warranty', 'warranty_covered', 'fixed', NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, '[]', 1, 1, '2026-03-29 11:12:16', '2026-03-29 11:13:02', NULL),
(3, 3, 138, '2026-000111', 'MSI', 'monitor issue', 'warranty', 'warranty_covered', 'fixed', NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, '[]', 1, 1, '2026-03-29 13:37:47', '2026-03-29 13:39:14', NULL),
(4, 4, 144, '2026-000117', 'Brother DCP-L5650DN', 'di mo print', 'warranty', 'warranty_covered', 'unserviceable', NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, '[]', 1, 1, '2026-04-05 09:22:05', '2026-04-05 09:22:46', NULL),
(5, 5, 143, '2026-000116', 'Ergonomic Mesh Chair', 'haha', 'warranty', 'warranty_covered', 'unserviceable', NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, '[]', 1, 1, '2026-04-05 10:23:39', '2026-04-05 10:24:24', NULL);

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
(6, 6, 2, 30, 'PTR-2026-0001', 'bugnaw', '2026-03-18', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-18 19:27:57', '2026-03-18 21:04:32'),
(7, 7, 2, 30, 'PAR-2026-0003', 'Office Desk (OD-PRM-120)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(8, 31, 2, 30, 'PAR-2026-0003', 'Office Desk (OD-PRM-120)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(9, 49, 2, 30, 'PAR-2026-0003', 'Office Desk (OD-PRM-120)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(10, 64, 2, 30, 'PAR-2026-0003', 'Office Desk (OD-PRM-120)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(11, 8, 2, 30, 'PAR-2026-0003', 'Office Chair (CH-ERG-22)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(12, 32, 2, 30, 'PAR-2026-0003', 'Office Chair (CH-ERG-22)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(13, 50, 2, 30, 'PAR-2026-0003', 'Office Chair (CH-ERG-22)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(14, 65, 2, 30, 'PAR-2026-0003', 'Office Chair (CH-ERG-22)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(15, 76, 2, 30, 'PAR-2026-0003', 'Office Chair (CH-ERG-22)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(16, 84, 2, 30, 'PAR-2026-0003', 'Office Chair (CH-ERG-22)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(17, 91, 2, 30, 'PAR-2026-0003', 'Office Chair (CH-ERG-22)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(18, 96, 2, 30, 'PAR-2026-0003', 'Office Chair (CH-ERG-22)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(19, 9, 2, 30, 'PAR-2026-0003', 'Filing Cabinet (FC-STL-4D)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(20, 33, 2, 30, 'PAR-2026-0003', 'Filing Cabinet (FC-STL-4D)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(21, 10, 2, 30, 'PAR-2026-0003', 'Desktop Computer (ThinkCentre-M70)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(22, 34, 2, 30, 'PAR-2026-0003', 'Desktop Computer (ThinkCentre-M70)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(23, 51, 2, 30, 'PAR-2026-0003', 'Desktop Computer (ThinkCentre-M70)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(24, 66, 2, 30, 'PAR-2026-0003', 'Desktop Computer (ThinkCentre-M70)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(25, 77, 2, 30, 'PAR-2026-0003', 'Desktop Computer (ThinkCentre-M70)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(26, 85, 2, 30, 'PAR-2026-0003', 'Desktop Computer (ThinkCentre-M70)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(27, 11, 2, 30, 'PAR-2026-0003', 'Printer/Scanner (DCP-L5650DN)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-25 20:05:30'),
(28, 35, 2, 30, 'PAR-2026-0003', 'Printer/Scanner (DCP-L5650DN)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(29, 12, 2, 30, 'PAR-2026-0003', 'Telephone Set (KX-TS880)', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:53:00', '2026-03-22 22:53:00'),
(30, 134, 2, 30, 'PAR-2026-0004', 'ha', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 22:54:56', '2026-03-22 22:55:54'),
(31, 135, 2, 30, 'PAR-2026-0005', 'heaha', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 23:17:31', '2026-03-22 23:17:39'),
(32, 136, 2, 30, 'PAR-2026-0006', 'ha', '2026-03-22', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-22 23:22:27', '2026-03-29 15:37:00'),
(33, 137, 2, 30, 'PAR-2026-0007', 'ASUS', '2026-03-24', 2, 1, 'Auto-created from DR inspection', '2026-03-24 11:40:12', '2026-03-29 15:37:05'),
(34, 138, 2, 30, 'PAR-2026-0008', 'MSI', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:43:37', '2026-03-29 21:39:14'),
(35, 25, 2, 30, 'PTR-2026-0008', 'Laptop Computer (TravelMate-P2)', '2026-04-23', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-04-23 16:02:02'),
(36, 26, 4, 23, 'PAR-2026-0009', 'Office Lamp (OFL-9W)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(37, 45, 4, 23, 'PAR-2026-0009', 'Office Lamp (OFL-9W)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(38, 60, 4, 23, 'PAR-2026-0009', 'Office Lamp (OFL-9W)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(39, 73, 4, 23, 'PAR-2026-0009', 'Office Lamp (OFL-9W)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(40, 81, 4, 23, 'PAR-2026-0009', 'Office Lamp (OFL-9W)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(41, 88, 4, 23, 'PAR-2026-0009', 'Office Lamp (OFL-9W)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(42, 93, 4, 23, 'PAR-2026-0009', 'Office Lamp (OFL-9W)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(43, 98, 4, 23, 'PAR-2026-0009', 'Office Lamp (OFL-9W)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(44, 102, 4, 23, 'PAR-2026-0009', 'Office Lamp (OFL-9W)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(45, 106, 4, 23, 'PAR-2026-0009', 'Office Lamp (OFL-9W)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(46, 109, 4, 23, 'PAR-2026-0009', 'Office Lamp (OFL-9W)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(47, 110, 4, 23, 'PAR-2026-0009', 'Office Lamp (OFL-9W)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(48, 27, 4, 23, 'PAR-2026-0009', 'Extension Cord (EXT-5S)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(49, 46, 4, 23, 'PAR-2026-0009', 'Extension Cord (EXT-5S)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(50, 61, 4, 23, 'PAR-2026-0009', 'Extension Cord (EXT-5S)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(51, 74, 2, 30, 'PTR-2026-0004', 'Extension Cord (EXT-5S)', '2026-04-20', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-04-20 11:53:09'),
(52, 82, 4, 23, 'PAR-2026-0009', 'Extension Cord (EXT-5S)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(53, 89, 4, 23, 'PAR-2026-0009', 'Extension Cord (EXT-5S)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(54, 94, 4, 23, 'PAR-2026-0009', 'Extension Cord (EXT-5S)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(55, 99, 4, 23, 'PAR-2026-0009', 'Extension Cord (EXT-5S)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(56, 103, 4, 23, 'PAR-2026-0009', 'Extension Cord (EXT-5S)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(57, 107, 4, 23, 'PAR-2026-0009', 'Extension Cord (EXT-5S)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(58, 28, 2, 30, 'PTR-2026-0005', 'Hand Tools Set (HTS-112)', '2026-03-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-29 21:26:06'),
(59, 47, 4, 23, 'PAR-2026-0009', 'Hand Tools Set (HTS-112)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(60, 62, 4, 23, 'PAR-2026-0009', 'Hand Tools Set (HTS-112)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(61, 29, 2, 30, 'PTR-2026-0006', 'Waste Basket (WB-18L)', '2026-04-04', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-04-04 01:09:22'),
(62, 48, 2, 30, 'PTR-2026-0007', 'Waste Basket (WB-18L)', '2026-04-20', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-04-20 13:26:45'),
(63, 63, 2, 30, 'PTR-2026-0012', 'Waste Basket (WB-18L)', '2026-04-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-04-24 19:57:58'),
(64, 75, 4, 23, 'PAR-2026-0009', 'Waste Basket (WB-18L)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(65, 83, 4, 23, 'PAR-2026-0009', 'Waste Basket (WB-18L)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(66, 90, 4, 23, 'PAR-2026-0009', 'Waste Basket (WB-18L)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(67, 95, 4, 23, 'PAR-2026-0009', 'Waste Basket (WB-18L)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(68, 100, 4, 23, 'PAR-2026-0009', 'Waste Basket (WB-18L)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(69, 104, 4, 23, 'PAR-2026-0009', 'Waste Basket (WB-18L)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(70, 108, 4, 23, 'PAR-2026-0009', 'Waste Basket (WB-18L)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 11:55:01'),
(71, 30, 4, 23, 'PTR-2026-0003', 'Projector (MS560)', '2026-03-24', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-24 11:55:01', '2026-03-24 12:06:22'),
(72, 139, 2, 30, 'PAR-2026-0010', 'Lenovo ThinkCentre M70', '2026-03-29', 2, 1, 'Auto-created from DR inspection', '2026-03-29 20:32:46', '2026-03-29 20:56:46'),
(73, 140, 2, 30, 'PAR-2026-0010', 'Ergonomic Mesh Chair', '2026-03-29', 2, 1, 'Auto-created from DR inspection', '2026-03-29 20:32:46', '2026-03-29 20:56:46'),
(74, 141, 2, 30, 'PAR-2026-0011', 'Ergonomic Mesh Chair', '2026-03-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-03-29 20:52:16', '2026-03-29 20:58:25'),
(75, 142, 2, 30, 'PAR-2026-0011', 'Brother DCP-L5650DN', '2026-03-29', 2, 2, 'Auto-created from DR acknowledgment', '2026-03-29 20:52:16', '2026-04-05 18:29:04'),
(76, 143, 2, 30, 'PAR-2026-0012', 'Ergonomic Mesh Chair', '2026-03-29', 4, 2, 'Auto-created from DR inspection', '2026-03-29 21:15:35', '2026-04-05 18:24:24'),
(77, 144, 2, 30, 'PAR-2026-0012', 'Brother DCP-L5650DN', '2026-03-29', 4, 2, 'Auto-created from DR inspection', '2026-03-29 21:15:35', '2026-04-05 17:22:46'),
(78, 145, 2, 30, 'PAR-2026-0013', 'sd', '2026-04-20', 2, 1, 'Auto-created from DR inspection', '2026-04-20 12:05:52', '2026-04-20 12:09:16'),
(79, 146, 2, 30, 'PAR-2026-0014', 'Calculator mo work', '2026-04-20', 2, 1, 'Auto-created from DR inspection', '2026-04-20 13:18:33', '2026-04-20 13:20:37'),
(80, 147, 2, 30, 'PTR-2026-0012', 'bugnaw', '2026-04-24', 2, 1, 'Auto-created from DR inspection', '2026-04-20 13:18:33', '2026-04-24 19:57:58'),
(81, 207, 4, 23, 'PAR-2026-0016', 'Compact Workstation Desk', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(82, 208, 4, 23, 'PAR-2026-0016', 'Compact Workstation Desk', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(83, 209, 4, 23, 'PAR-2026-0016', 'Compact Workstation Desk', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(84, 210, 4, 23, 'PAR-2026-0016', 'Task Chair', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(85, 211, 4, 23, 'PAR-2026-0016', 'Task Chair', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(86, 212, 4, 23, 'PAR-2026-0016', 'Task Chair', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(87, 213, 4, 23, 'PAR-2026-0016', 'Task Chair', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(88, 214, 4, 23, 'PAR-2026-0016', 'Task Chair', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(89, 215, 4, 23, 'PAR-2026-0016', 'Task Chair', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(90, 216, 4, 23, 'PAR-2026-0016', '1.5HP Wall Mounted AC', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(91, 217, 4, 23, 'ICS-2026-0002', 'Portable fire extinguisher', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(92, 218, 4, 23, 'ICS-2026-0002', 'Portable fire extinguisher', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(93, 219, 4, 23, 'ICS-2026-0002', 'Portable fire extinguisher', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(94, 220, 4, 23, 'ICS-2026-0002', 'Portable fire extinguisher', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(95, 221, 4, 23, 'ICS-2026-0002', 'Portable fire extinguisher', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(96, 222, 4, 23, 'ICS-2026-0002', 'Portable fire extinguisher', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(97, 223, 4, 23, 'ICS-2026-0002', 'Portable fire extinguisher', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(98, 224, 4, 23, 'ICS-2026-0002', 'Portable fire extinguisher', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(99, 225, 4, 23, 'ICS-2026-0002', 'Portable fire extinguisher', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(100, 226, 4, 23, 'ICS-2026-0002', 'Portable fire extinguisher', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(101, 227, 4, 23, 'ICS-2026-0002', 'Portable fire extinguisher', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(102, 228, 4, 23, 'ICS-2026-0002', 'Portable fire extinguisher', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(103, 229, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(104, 230, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(105, 231, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(106, 232, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(107, 233, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(108, 234, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(109, 235, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:44', '2026-04-28 00:39:44'),
(110, 236, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(111, 237, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(112, 238, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(113, 239, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(114, 240, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(115, 241, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(116, 242, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(117, 243, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(118, 244, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(119, 245, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(120, 246, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(121, 247, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(122, 248, 4, 23, 'ICS-2026-0002', 'Janitorial cleaning kit', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(123, 249, 4, 23, 'ICS-2026-0002', 'RJ45 LAN cable bundle', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(124, 250, 4, 23, 'ICS-2026-0002', 'RJ45 LAN cable bundle', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(125, 251, 4, 23, 'ICS-2026-0002', 'RJ45 LAN cable bundle', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(126, 252, 4, 23, 'ICS-2026-0002', 'RJ45 LAN cable bundle', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(127, 253, 4, 23, 'ICS-2026-0002', 'RJ45 LAN cable bundle', '2026-04-28', 2, 1, 'Auto-created from DR inspection', '2026-04-28 00:39:45', '2026-04-28 00:39:45'),
(128, 148, 2, 30, 'PAR-2026-0015', 'Lenovo ThinkCentre M70', '2026-04-29', 2, 1, 'Auto-created from DR inspection', '2026-04-29 10:22:06', '2026-04-29 10:22:06'),
(129, 149, 2, 30, 'PAR-2026-0015', 'Brother DCP-L5650DN', '2026-04-29', 2, 1, 'Auto-created from DR inspection', '2026-04-29 10:22:06', '2026-04-29 10:22:06'),
(130, 150, 2, 30, 'PAR-2026-0015', 'Ergonomic Mesh Chair', '2026-04-29', 2, 1, 'Auto-created from DR inspection', '2026-04-29 10:22:06', '2026-04-29 10:22:06'),
(131, 151, 2, 30, 'ICS-2026-0001', 'Portable rechargeable emergency lamp', '2026-04-29', 2, 1, 'Auto-created from DR inspection', '2026-04-29 10:22:06', '2026-04-29 10:22:06'),
(132, 152, 2, 30, 'ICS-2026-0001', 'Portable rechargeable emergency lamp', '2026-04-29', 2, 1, 'Auto-created from DR inspection', '2026-04-29 10:22:06', '2026-04-29 10:22:06'),
(133, 153, 2, 30, 'ICS-2026-0001', 'Portable rechargeable emergency lamp', '2026-04-29', 2, 1, 'Auto-created from DR inspection', '2026-04-29 10:22:06', '2026-04-29 10:22:06'),
(134, 154, 2, 30, 'ICS-2026-0001', '32GB USB 3.0 flash drive', '2026-04-29', 2, 1, 'Auto-created from DR inspection', '2026-04-29 10:22:06', '2026-04-29 10:22:06'),
(135, 155, 2, 30, 'ICS-2026-0001', '32GB USB 3.0 flash drive', '2026-04-29', 2, 1, 'Auto-created from DR inspection', '2026-04-29 10:22:06', '2026-04-29 10:22:06'),
(136, 156, 2, 30, 'ICS-2026-0001', 'Wireless keyboard and mouse set', '2026-04-29', 2, 1, 'Auto-created from DR inspection', '2026-04-29 10:22:06', '2026-04-29 10:22:06'),
(137, 157, 2, 30, 'ICS-2026-0001', 'Wireless keyboard and mouse set', '2026-04-29', 2, 1, 'Auto-created from DR inspection', '2026-04-29 10:22:06', '2026-04-29 10:22:06'),
(138, 158, 2, 30, 'ICS-2026-0001', 'Wireless keyboard and mouse set', '2026-04-29', 2, 1, 'Auto-created from DR inspection', '2026-04-29 10:22:06', '2026-04-29 10:22:06'),
(139, 159, 2, 30, 'ICS-2026-0001', 'Wireless keyboard and mouse set', '2026-04-29', 2, 1, 'Auto-created from DR inspection', '2026-04-29 10:22:06', '2026-04-29 10:22:06'),
(140, 160, 2, 30, 'ICS-2026-0001', 'Wireless keyboard and mouse set', '2026-04-29', 2, 1, 'Auto-created from DR inspection', '2026-04-29 10:22:06', '2026-04-29 10:22:06'),
(141, 161, 2, 30, 'PAR-2026-0017', 'Acer TravelMate P2', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:23', '2026-04-29 12:47:23'),
(142, 162, 2, 30, 'PAR-2026-0017', '4-Drawer Steel Filing Cabinet', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:23', '2026-04-29 12:47:23'),
(143, 163, 2, 30, 'PAR-2026-0017', '4-Drawer Steel Filing Cabinet', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:23', '2026-04-29 12:47:23'),
(144, 164, 2, 30, 'PAR-2026-0017', 'Epson EB-X06', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:23', '2026-04-29 12:47:23'),
(145, 165, 2, 30, 'ICS-2026-0003', '1.5L electric kettle', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:23', '2026-04-29 12:47:23'),
(146, 166, 2, 30, 'ICS-2026-0003', '1.5L electric kettle', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:23', '2026-04-29 12:47:23'),
(147, 167, 2, 30, 'ICS-2026-0003', '1.5L electric kettle', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:23', '2026-04-29 12:47:23'),
(148, 168, 2, 30, 'ICS-2026-0003', '1.5L electric kettle', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:23', '2026-04-29 12:47:23'),
(149, 169, 2, 30, 'ICS-2026-0003', '1.5L electric kettle', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:23', '2026-04-29 12:47:23'),
(150, 170, 2, 30, 'ICS-2026-0003', '1.5L electric kettle', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24'),
(151, 171, 2, 30, 'ICS-2026-0003', '1.5L electric kettle', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24'),
(152, 172, 2, 30, 'ICS-2026-0003', '1.5L electric kettle', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24'),
(153, 173, 2, 30, 'ICS-2026-0003', '1.5L electric kettle', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24'),
(154, 174, 2, 30, 'ICS-2026-0003', '1.5L electric kettle', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24'),
(155, 175, 2, 30, 'ICS-2026-0003', 'Portable wireless speaker', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24'),
(156, 176, 2, 30, 'ICS-2026-0003', 'Portable wireless speaker', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24'),
(157, 177, 2, 30, 'ICS-2026-0003', 'Portable wireless speaker', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24'),
(158, 178, 2, 30, 'ICS-2026-0003', 'Portable wireless speaker', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24'),
(159, 179, 2, 30, 'ICS-2026-0003', '6-outlet surge protector', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24'),
(160, 180, 2, 30, 'ICS-2026-0003', '6-outlet surge protector', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24'),
(161, 181, 2, 30, 'ICS-2026-0003', '6-outlet surge protector', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24'),
(162, 182, 2, 30, 'ICS-2026-0003', '6-outlet surge protector', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24'),
(163, 183, 2, 30, 'ICS-2026-0003', '6-outlet surge protector', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24'),
(164, 184, 2, 30, 'ICS-2026-0003', '6-outlet surge protector', '2026-04-29', 2, 1, 'Auto-created from DR acknowledgment', '2026-04-29 12:47:24', '2026-04-29 12:47:24');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('gsoiams-cache-fcm_oauth_access_token', 's:1024:\"ya29.c.c0AZ4bNpbeDR0U2fUb74ba9CbLSk45D9aWRDD71nXKwBoLeaoRZzV-RInDDk_hgm24sUNMTB0W0eGjCrSawSTWBCOnqEafpsF42eeTPjuNDbMOfkQIu9-8A4S48OLxxGAepNHe0xymgCiIhsL-3o8G8yJIlQb_ynN7dX_evZcP26zFYkMcqNzl0crU4srT1UDplljbnHq2QCZPLMI-WYfBdcaQMSzdEu63iHj3--3k_P9N9Jrva4SND3Ygyf6bmqA4GtTv-p0oT6wbaJ4pq14B83-yKafzOxaeXCmJC-VSYwMcGZ9BzU5tO_aTnsITN2KNIR5Sl0Ax819-adJRNBY4P_Uj_5SZVuAUxVrh9pLs7kTT8216o9Gzb5ZUG385AYVqMapJw3jWfwYg9ddvayIvjbxnM8WgJwrB-orlk5UzwaMFm0BecOri-kdtmtzons9F89p2iRXfUB0b4XU7du1uQmF_yMcxrof9xusckb5gkSvr1rQg03v7ev8Fjv16Zbscrop3yR_xzgnmvb1oqjx_oi7JZX2Of3tO2l5eFsojnl3O_F285Wqu1ijxMwxR4mYko1jJa53Q2OUIZwXvOJ8_vd3Y7oxehVZvJQJw4r8-4M45pUebyF7UnRlk4vR41Rryl3i5pQ9eFFU13Q-ORBb8UIbqkjln4Jt624uwmObtUyFtwYBVXpwXsMIoRFlX9dkaOqxxvM3sMhg5u-3Smga0owprhZtfiRlU_2UbS2S9wuBpSmi6wjya9QlZ9XX5eRbQgRRQxJ3ouXRztvXxjBq-iFbv3wienQosRlebwVY-WO5pbU4-eJQvn0WUreQ6lU0srsBiaZdiswn-I3tu6mVpy_ovMkfI766MIlphtXYQYXykrRtQnIzku5O3pmjWQ42UFUr7pIBlOXJ3WYOUowj5k7hSi8lmz159Sj2Q2WFYf2eFlpU7knB108Xef1Yvfrz8VakJidgyWXb2bqr8u4hVn6hOd4qypr3nyqV4bImlXsw3QhicBy0myyq\";', 1777311465);

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
(2, 'Unserviceable', 'Unserviceable Assets', NULL),
(6, 'Under Repair', 'Under Repair Assets', NULL);

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
(29, 'DR-2026-03-18-001', 'IAR-2026-0014', 1, 3, 'PR-2026-CCE-1001', 'PO-2026-1001', 'INV-2026-1001', 1, 7, '2026-03-10', '2026-03-10', 'Batch 1 office and ICT assets for operational rollout.', 'inspected', '2026-03-18 09:54:47', 'uploads/dr/dr-2026-03-18-001.jpg', '2026-03-18 17:54:47', NULL),
(30, 'DR-2026-03-18-003', 'IAR-2026-0015', 2, 6, 'PR-2026-CCE-1003', 'PO-2026-1003', 'INV-2026-1003', 1, 7, '2026-03-14', '2026-03-14', 'Batch 3 admin support assets.', 'inspected', '2026-03-18 09:54:47', 'uploads/dr/dr-2026-03-18-003.jpg', '2026-03-18 17:54:47', NULL),
(31, 'DR-2026-03-18-002', 'IAR-2026-0016', 3, 3, 'PR-2026-CCE-1002', 'PO-2026-1002', 'INV-2026-1002', 1, 7, '2026-03-12', '2026-03-12', 'Batch 2 mixed furniture and equipment.', 'inspected', '2026-03-18 09:54:47', 'uploads/dr/dr-2026-03-18-002.jpg', '2026-03-18 17:54:47', NULL),
(32, 'DR-2026-03-18-004', 'IAR-2026-0017', 4, 6, 'PR-2026-CCE-1004', 'PO-2026-1004', 'INV-2026-1004', 1, 7, '2026-03-16', '2026-03-16', 'Batch 4 additional non-expendable units.', 'inspected', '2026-03-18 09:54:47', 'uploads/dr/dr-2026-03-18-004.jpg', '2026-03-18 17:54:47', NULL),
(40, 'dr21', 'IAR-2026-0005', 3, 3, '1', '1', '1', 1, 1, '2026-03-18', '2026-03-18', 'sad', 'inspected', '2026-03-18 10:11:05', 'dr_pictures/ZCC8NMHmpSaSUl9O6WbxtQZIm9wYgBnaQNi0mXe9.png', '2026-03-18 18:11:05', NULL),
(41, 'dr22', 'IAR-2026-0001', 2, 6, '3', '3', '3', 1, 1, '2026-03-18', '2026-03-18', 'we', 'inspected', '2026-03-18 11:27:56', 'dr_pictures/gHOqAKCuNMHyVT13glRYsaes5wMmO3pAGIARLY4l.jpg', '2026-03-18 19:27:56', NULL),
(42, 'dr23', 'IAR-2026-0006', 2, 3, '3', '4', '4', 1, 1, '2026-03-22', '2026-03-22', 'ds', 'inspected', '2026-03-22 14:54:56', 'dr_pictures/AzdGFPTBXXgUF62m5y0yQJd6Z38WeRgfkIaZxLQf.jpg', '2026-03-22 22:54:56', NULL),
(43, 'dr31', 'IAR-2026-0007', 4, 3, '5', '5', '5', 1, 1, '2026-03-22', '2026-03-22', '5', 'inspected', '2026-03-22 15:17:31', 'dr_pictures/CQn88MMQBbwlG3OBttAne3naz8bfi6HwDF849iDg.jpg', '2026-03-22 23:17:31', NULL),
(44, 'dr11', 'IAR-2026-0008', 3, 3, '11', '11', '11', 1, 1, '2026-03-22', '2026-03-22', '11', 'inspected', '2026-03-22 15:22:27', 'dr_pictures/ZBSE7AsuIuAXZITn68CGbjUEUUrkiRlaqWb7G6Ex.jpg', '2026-03-22 23:22:27', NULL),
(45, 'd2', 'IAR-2026-0009', 4, 3, '32', '23', '2', 1, 1, '2026-03-24', '2026-03-24', NULL, 'inspected', '2026-03-24 03:40:12', 'dr_pictures/qMvtv2bSafZ0yrIUNpW9EjRumuBhbAXjPDyxQYxT.jpg', '2026-03-24 11:40:12', NULL),
(46, 'd21', 'IAR-2026-0010', 4, 3, '23', '3', '33', 1, 1, '2026-03-24', '2026-03-24', NULL, 'inspected', '2026-03-24 03:43:37', 'dr_pictures/wFNpkmmlKZ9aDolSpQry3JwUS5JFf07HNaFMN7ib.jpg', '2026-03-24 11:43:37', NULL),
(47, 'DR-TEST-1', 'IAR-2026-0011', 2, 3, '123', '123', '123', 1, 1, '2026-03-29', '2026-03-29', 'good', 'inspected', '2026-03-29 12:32:46', 'dr_pictures/dR0esOLyOu1rEc99ZlPPjLv2aiZ97HxfEtDnIftp.jpg', '2026-03-29 20:32:46', NULL),
(48, 'DR-TEST-2', 'IAR-2026-0012', 2, 3, '1234', '1234', '1234', 1, 1, '2026-03-29', '2026-03-29', 'good', 'inspected', '2026-03-29 12:52:16', 'dr_pictures/wKQBtIWcHe6fC5r5mUmOrOQsPaEHJ2vzywpvV7uE.jpg', '2026-03-29 20:52:16', NULL),
(49, 'DR-TEST-3', 'IAR-2026-0013', 2, 3, '12345', '12345', '12345', 1, 1, '2026-03-29', '2026-03-29', 'goods', 'inspected', '2026-03-29 13:15:35', 'dr_pictures/ajpqfmjfe3z72YbrQ3Zv14X9eMFsjI9XUMgMZTwI.jpg', '2026-03-29 21:15:35', NULL),
(50, 'DR-AUTO-IAR-202604031931021', 'IAR-2026-0018', 1, 1, 'PR-AUTO-202604031931021', 'PO-AUTO-202604031931021', 'INV-AUTO-202604031931021', 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated validation DR', 'inspected', '2026-04-03 11:31:03', 'dr_pictures/auto-test.png', '2026-04-03 19:31:03', NULL),
(51, 'DR-AUTO-IAR-202604031931032', 'IAR-2026-0019', 1, 1, 'PR-AUTO-202604031931032', 'PO-AUTO-202604031931032', 'INV-AUTO-202604031931032', 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated validation DR', 'inspected', '2026-04-03 11:31:03', 'dr_pictures/auto-test.png', '2026-04-03 19:31:03', NULL),
(52, 'DR-AUTO-IAR-202604031931033', 'IAR-2026-0020', 1, 1, 'PR-AUTO-202604031931033', 'PO-AUTO-202604031931033', 'INV-AUTO-202604031931033', 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated validation DR', 'inspected', '2026-04-03 11:31:03', 'dr_pictures/auto-test.png', '2026-04-03 19:31:03', NULL),
(53, 'DR-AUTO-IAR-202604031931121', 'IAR-2026-0021', 1, 1, 'PR-AUTO-202604031931121', 'PO-AUTO-202604031931121', 'INV-AUTO-202604031931121', 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated validation DR', 'inspected', '2026-04-03 11:31:12', 'dr_pictures/auto-test.png', '2026-04-03 19:31:12', NULL),
(54, 'DR-AUTO-IAR-202604031931122', 'IAR-2026-0022', 1, 1, 'PR-AUTO-202604031931122', 'PO-AUTO-202604031931122', 'INV-AUTO-202604031931122', 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated validation DR', 'inspected', '2026-04-03 11:31:12', 'dr_pictures/auto-test.png', '2026-04-03 19:31:12', NULL),
(55, 'DR-AUTO-IAR-202604031931123', 'IAR-2026-0023', 1, 1, 'PR-AUTO-202604031931123', 'PO-AUTO-202604031931123', 'INV-AUTO-202604031931123', 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated validation DR', 'inspected', '2026-04-03 11:31:12', 'dr_pictures/auto-test.png', '2026-04-03 19:31:12', NULL),
(86, 'drtest12321', 'IAR-2026-0024', 4, 3, '123321', '123321', '123321', 1, 1, '2026-04-20', '2026-04-20', NULL, 'inspected', '2026-04-20 04:05:52', 'dr_pictures/o9EmiY3BcgIh83pMyoU0jTpKdEGqikRuRQrynyND.jpg', '2026-04-20 12:05:52', NULL),
(87, 'dr-malinao', 'IAR-2026-0025', 2, 3, '9', '9', '9', 1, 1, '2026-04-20', '2026-04-20', 'All Good', 'inspected', '2026-04-20 05:18:33', 'dr_pictures/QEiBaouWafXgedWdr5Y9nkamflPx8wLrj8CUwdAB.jpg', '2026-04-20 13:18:33', NULL),
(88, 'DR-2026-04-27-001', 'IAR-2026-04-27-001', 1, 3, 'PR-2026-MIX-1001', 'PO-2026-MIX-1001', 'INV-2026-MIX-1001', 1, 7, '2026-04-08', '2026-04-08', 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'inspected', '2026-04-27 15:26:20', 'uploads/dr/dr-2026-04-27-001.jpg', '2026-04-27 23:26:20', NULL),
(89, 'DR-2026-04-27-003', 'IAR-2026-04-27-003', 2, 6, 'PR-2026-MIX-1003', 'PO-2026-MIX-1003', 'INV-2026-MIX-1003', 1, 7, '2026-04-16', '2026-04-16', 'Mixed equipment for operations and support rooms.', 'inspected', '2026-04-27 15:26:20', 'uploads/dr/dr-2026-04-27-003.jpg', '2026-04-27 23:26:20', NULL),
(90, 'DR-2026-04-27-002', 'IAR-2026-04-27-002', 3, 3, 'PR-2026-MIX-1002', 'PO-2026-MIX-1002', 'INV-2026-MIX-1002', 1, 7, '2026-04-12', '2026-04-12', 'Second batch of office, ICT, and maintenance items.', 'inspected', '2026-04-27 15:26:20', 'uploads/dr/dr-2026-04-27-002.jpg', '2026-04-27 23:26:20', NULL),
(91, 'DR-2026-04-27-004', 'IAR-2026-04-27-004', 4, 6, 'PR-2026-MIX-1004', 'PO-2026-MIX-1004', 'INV-2026-MIX-1004', 1, 7, '2026-04-20', '2026-04-20', 'Final mixed batch with serial and bulk items.', 'inspected', '2026-04-27 15:26:20', 'uploads/dr/dr-2026-04-27-004.jpg', '2026-04-27 23:26:20', NULL);

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
(99, 41, 7, 'bugnaw', 'he', 'hah', NULL, 8, 1, 1, NULL, 2, 2333.00, 2333.00, 'inspected', NULL),
(100, 42, 11, 'ha', NULL, NULL, NULL, 11, 1, 1, NULL, 2, 122.97, 122.97, 'inspected', NULL),
(101, 43, 11, 'heaha', NULL, NULL, NULL, 11, 1, 1, NULL, 2, 123321.00, 123321.00, 'inspected', NULL),
(102, 44, 11, 'ha', NULL, NULL, NULL, 11, 1, 1, NULL, 2, 23.00, 23.00, 'inspected', NULL),
(103, 45, 2, 'ASUS', 'ASUS', 'ROG', '231', 7, 1, 1, NULL, 2, 30000.00, 30000.00, 'inspected', NULL),
(104, 46, 2, 'MSI', 'MSI', 'MSI', '32', 7, 1, 1, NULL, 2, 25000.00, 25000.00, 'inspected', NULL),
(105, 47, 1, 'Lenovo ThinkCentre M70', 'Lenovo', 'M70', 'LNV2026-001', 7, 1, 1, NULL, 2, 42500.00, 42500.00, 'inspected', NULL),
(106, 47, 5, 'Ergonomic Mesh Chair', 'ErgoSeat', 'CH-ERG-22', 'ERGO-2026-002', 6, 1, 1, NULL, 2, 3200.00, 3200.00, 'inspected', NULL),
(107, 48, 5, 'Ergonomic Mesh Chair', 'ErgoSeat', 'CH-ERG-22', 'ERGO-2026-002', 6, 1, 1, NULL, 2, 3200.00, 3200.00, 'inspected', NULL),
(108, 48, 5, 'Brother DCP-L5650DN', 'Brother', 'DCP-L5650DN', 'BR-2026-003', 6, 1, 1, NULL, 2, 18900.00, 18900.00, 'inspected', NULL),
(109, 49, 5, 'Ergonomic Mesh Chair', 'ErgoSeat', 'CH-ERG-22', 'ERGO-2026-002', 6, 1, 1, NULL, 2, 3200.00, 3200.00, 'inspected', NULL),
(110, 49, 3, 'Brother DCP-L5650DN', 'Brother', 'DCP-L5650DN', 'BR-2026-003', 7, 1, 1, NULL, 2, 18900.00, 18900.00, 'inspected', NULL),
(141, 86, 7, 'sd', 's', 'd', '1', 8, 1, 1, NULL, 2, 2400.00, 2400.00, 'inspected', NULL),
(142, 87, 11, 'Calculator mo work', 'brand x', 'brand y', 'brand z', 11, 1, 1, NULL, 2, 9998.90, 9998.90, 'inspected', NULL),
(143, 87, 7, 'bugnaw', 'aircon x', 'aircon y', 'aircon z', 8, 1, 1, NULL, 2, 19000.00, 19000.00, 'inspected', NULL),
(144, 88, 1, 'Lenovo ThinkCentre M70', 'Lenovo', 'M70', 'LNV2026-001', 7, 1, 1, NULL, 2, 42500.00, 42500.00, 'inspected', NULL),
(145, 90, 2, 'Acer TravelMate P2', 'Acer', 'TravelMate-P2', 'ACR-2026-004', 7, 1, 1, NULL, 2, 52000.00, 52000.00, 'inspected', NULL),
(146, 88, 3, 'Brother DCP-L5650DN', 'Brother', 'DCP-L5650DN', 'BR-2026-003', 7, 1, 1, NULL, 2, 18900.00, 18900.00, 'inspected', NULL),
(147, 89, 4, 'Laminate Office Desk', 'Prime Office', 'OD-PRM-140', NULL, 6, 4, 4, NULL, 2, 8500.00, 34000.00, 'inspected', NULL),
(148, 91, 4, 'Compact Workstation Desk', 'Prime Office', 'OD-PRM-120', NULL, 6, 3, 3, NULL, 2, 8400.00, 25200.00, 'inspected', NULL),
(149, 88, 5, 'Ergonomic Mesh Chair', 'ErgoSeat', 'CH-ERG-22', 'ERGO-2026-002', 6, 1, 1, NULL, 2, 3200.00, 3200.00, 'inspected', NULL),
(150, 91, 5, 'Task Chair', 'ErgoSeat', 'CH-ERG-24', NULL, 6, 6, 6, NULL, 2, 3150.00, 18900.00, 'inspected', NULL),
(151, 90, 6, '4-Drawer Steel Filing Cabinet', 'SteelSafe', 'FC-STL-4D', NULL, 6, 2, 2, NULL, 2, 11500.00, 23000.00, 'inspected', NULL),
(152, 91, 7, '1.5HP Wall Mounted AC', 'Carrier', 'Aura-1.5HP', 'CAR-2026-008', 8, 1, 2, NULL, 2, 33800.00, 33800.00, 'inspected', NULL),
(153, 90, 8, 'Epson EB-X06', 'Epson', 'EB-X06', 'EPS-2026-005', 7, 1, 1, NULL, 2, 28500.00, 28500.00, 'inspected', NULL),
(154, 89, 9, 'Panasonic KX-TS880', 'Panasonic', 'KX-TS880', 'TEL-2026-007', 2, 1, 1, NULL, 2, 2350.00, 2350.00, 'inspected', NULL),
(155, 89, 10, 'Dell EMC PowerEdge R250', 'Dell EMC', 'PowerEdge-R250', 'SRV-2026-006', 7, 1, 1, NULL, 2, 165000.00, 165000.00, 'inspected', NULL),
(156, 89, 23, 'Portable action camera', 'GoPro', 'AC-11', NULL, 21, 5, 5, NULL, 2, 1200.00, 6000.00, 'inspected', NULL),
(157, 90, 24, '1.5L electric kettle', 'Hanabishi', 'EK-15', NULL, 18, 10, 10, NULL, 2, 560.00, 5600.00, 'inspected', NULL),
(158, 91, 25, 'Portable fire extinguisher', 'Elide', 'FE-5LB', NULL, 25, 12, 12, NULL, 2, 220.00, 2640.00, 'inspected', NULL),
(159, 89, 26, 'Handheld label maker', 'Brother', 'PT-D210', NULL, 27, 8, 8, NULL, 2, 450.00, 3600.00, 'inspected', NULL),
(160, 89, 27, 'Digital multimeter', 'Uni-T', 'UT-33D', NULL, 24, 3, 3, NULL, 2, 650.00, 1950.00, 'inspected', NULL),
(161, 90, 29, 'Portable wireless speaker', 'JBL', 'PS-BOOST', NULL, 20, 4, 4, NULL, 2, 4200.00, 16800.00, 'inspected', NULL),
(162, 88, 30, 'Portable rechargeable emergency lamp', 'Firefly', 'REL-2026-A', NULL, 26, 3, 3, NULL, 2, 980.00, 2940.00, 'inspected', NULL),
(163, 90, 31, '6-outlet surge protector', 'Panther', 'SP-06', NULL, 22, 6, 6, NULL, 2, 420.00, 2520.00, 'inspected', NULL),
(164, 88, 32, '32GB USB 3.0 flash drive', 'SanDisk', 'UFD-32G', NULL, 17, 2, 2, NULL, 2, 520.00, 1040.00, 'inspected', NULL),
(165, 91, 33, 'Janitorial cleaning kit', 'Generic', 'CK-2026', NULL, 29, 20, 20, NULL, 3, 180.00, 3600.00, 'inspected', NULL),
(166, 88, 35, 'Wireless keyboard and mouse set', 'LogiTech', 'KM-2026', NULL, 16, 5, 5, NULL, 3, 1250.00, 6250.00, 'inspected', NULL),
(167, 91, 36, 'RJ45 LAN cable bundle', 'Generic', 'LAN-SET-01', NULL, 28, 5, 5, NULL, 3, 980.00, 4900.00, 'inspected', NULL);

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
  `IconKey` varchar(50) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disposalmethod`
--

INSERT INTO `disposalmethod` (`DisposalMethodID`, `MethodName`, `Description`, `IconKey`, `deleted_at`) VALUES
(1, 'Public Auction', 'Sold through public bidding', 'badge-dollar-sign', NULL),
(2, 'Donation', 'Donated to qualified institutions', 'gift', NULL),
(3, 'Destruction', 'Physically destroyed', 'trash-2', NULL),
(4, 'Transfer to COA', 'Transferred to Commission on Audit', 'trash-2', NULL),
(5, 'Condemnation', 'Officially condemned and disposed', 'gavel', NULL);

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

--
-- Dumping data for table `inspectionacceptancereport`
--

INSERT INTO `inspectionacceptancereport` (`IARID`, `IARNumber`, `DRID`, `InspectedBy`, `AcceptedBy`, `DateInspected`, `DateAccepted`, `Remarks`, `CreatedDate`, `deleted_at`) VALUES
(1, 'IAR-2026-0014', 29, 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated for existing DR', '2026-04-03 19:29:58', NULL),
(2, 'IAR-2026-0015', 30, 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated for existing DR', '2026-04-03 19:29:58', NULL),
(3, 'IAR-2026-0016', 31, 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated for existing DR', '2026-04-03 19:29:58', NULL),
(4, 'IAR-2026-0017', 32, 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated for existing DR', '2026-04-03 19:29:58', NULL),
(5, 'IAR-2026-0005', 40, 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated for existing DR', '2026-04-03 19:29:58', NULL),
(6, 'IAR-2026-0001', 41, 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated for existing DR', '2026-04-03 19:29:58', NULL),
(7, 'IAR-2026-0006', 42, 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated for existing DR', '2026-04-03 19:29:58', NULL),
(8, 'IAR-2026-0007', 43, 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated for existing DR', '2026-04-03 19:29:58', NULL),
(9, 'IAR-2026-0008', 44, 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated for existing DR', '2026-04-03 19:29:58', NULL),
(10, 'IAR-2026-0009', 45, 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated for existing DR', '2026-04-03 19:29:58', NULL),
(11, 'IAR-2026-0010', 46, 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated for existing DR', '2026-04-03 19:29:58', NULL),
(12, 'IAR-2026-0011', 47, 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated for existing DR', '2026-04-03 19:29:58', NULL),
(13, 'IAR-2026-0012', 48, 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated for existing DR', '2026-04-03 19:29:58', NULL),
(14, 'IAR-2026-0013', 49, 1, 1, '2026-04-03', '2026-04-03', 'Auto-generated for existing DR', '2026-04-03 19:29:58', NULL);

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
(22, 1, '2026-03-21 13:56:20', 'Login', '127.0.0.1'),
(23, 1, '2026-03-21 14:39:29', 'Logout', '127.0.0.1'),
(24, 1, '2026-03-21 14:39:51', 'Login', '127.0.0.1'),
(25, 1, '2026-03-21 22:40:02', 'Login', '127.0.0.1'),
(26, 1, '2026-03-22 14:31:10', 'Login', '127.0.0.1'),
(27, 1, '2026-03-22 17:05:24', 'Logout', '127.0.0.1'),
(28, 3, '2026-03-22 17:42:23', 'Login', '127.0.0.1'),
(29, 3, '2026-03-22 17:42:35', 'Logout', '127.0.0.1'),
(30, 1, '2026-03-22 18:08:24', 'Login', '127.0.0.1'),
(31, 1, '2026-03-22 20:38:09', 'Login', '127.0.0.1'),
(32, 6, '2026-03-22 20:39:17', 'Login', '127.0.0.1'),
(33, 6, '2026-03-22 20:56:08', 'Logout', '127.0.0.1'),
(34, 6, '2026-03-22 20:59:06', 'Login', '127.0.0.1'),
(35, 6, '2026-03-22 20:59:15', 'Logout', '127.0.0.1'),
(36, 3, '2026-03-22 21:09:58', 'Login', '127.0.0.1'),
(37, 3, '2026-03-22 21:10:07', 'Logout', '127.0.0.1'),
(38, 3, '2026-03-22 21:11:39', 'Login', '127.0.0.1'),
(39, 3, '2026-03-23 14:21:13', 'Login', '127.0.0.1'),
(40, 1, '2026-03-23 14:28:25', 'Login', '127.0.0.1'),
(41, 1, '2026-03-23 14:55:26', 'Login', '127.0.0.1'),
(42, 1, '2026-03-23 14:56:45', 'Login', '127.0.0.1'),
(43, 3, '2026-03-23 18:19:18', 'Login', '127.0.0.1'),
(44, 1, '2026-03-24 10:22:24', 'Login', '127.0.0.1'),
(45, 1, '2026-03-24 11:06:53', 'Login', '127.0.0.1'),
(46, 3, '2026-03-24 11:29:53', 'Login', '127.0.0.1'),
(47, 3, '2026-03-24 11:57:04', 'Logout', '127.0.0.1'),
(48, 6, '2026-03-24 11:57:25', 'Login', '127.0.0.1'),
(49, 6, '2026-03-24 11:59:46', 'Logout', '127.0.0.1'),
(50, 3, '2026-03-24 11:59:57', 'Login', '127.0.0.1'),
(51, 3, '2026-03-24 12:01:48', 'Logout', '127.0.0.1'),
(52, 6, '2026-03-24 12:02:02', 'Login', '127.0.0.1'),
(53, 6, '2026-03-24 12:03:51', 'Logout', '127.0.0.1'),
(54, 3, '2026-03-24 12:04:01', 'Login', '127.0.0.1'),
(55, 3, '2026-03-24 12:05:34', 'Logout', '127.0.0.1'),
(56, 6, '2026-03-24 12:05:48', 'Login', '127.0.0.1'),
(57, 6, '2026-03-24 13:39:45', 'Logout', '127.0.0.1'),
(58, 1, '2026-03-24 13:40:58', 'Login', '127.0.0.1'),
(59, 3, '2026-03-24 14:35:46', 'Login', '127.0.0.1'),
(60, 1, '2026-03-24 23:24:19', 'Login', '127.0.0.1'),
(61, 1, '2026-03-25 13:27:50', 'Login', '127.0.0.1'),
(62, 3, '2026-03-25 13:32:30', 'Login', '127.0.0.1'),
(63, 3, '2026-03-25 21:04:33', 'Logout', '127.0.0.1'),
(64, 1, '2026-03-25 21:22:19', 'Logout', '127.0.0.1'),
(65, 1, '2026-03-25 21:29:37', 'Login', '127.0.0.1'),
(66, 1, '2026-03-25 21:30:30', 'Login', '127.0.0.1'),
(67, 3, '2026-03-25 21:32:39', 'Login', '127.0.0.1'),
(68, 1, '2026-03-25 21:40:27', 'Login', '127.0.0.1'),
(69, 1, '2026-03-25 21:44:45', 'Login', '127.0.0.1'),
(70, 3, '2026-03-25 21:58:43', 'Logout', '127.0.0.1'),
(71, 3, '2026-03-25 21:58:49', 'Login', '127.0.0.1'),
(72, 3, '2026-03-25 21:59:39', 'Logout', '127.0.0.1'),
(73, 3, '2026-03-25 21:59:45', 'Login', '127.0.0.1'),
(74, 3, '2026-03-25 22:00:47', 'Logout', '127.0.0.1'),
(75, 6, '2026-03-25 22:00:55', 'Login', '127.0.0.1'),
(76, 6, '2026-03-25 22:02:20', 'Logout', '127.0.0.1'),
(77, 3, '2026-03-25 22:02:26', 'Login', '127.0.0.1'),
(78, 1, '2026-03-25 22:16:17', 'Login', '127.0.0.1'),
(79, 1, '2026-03-26 12:41:33', 'FailedLogin', '127.0.0.1'),
(80, 1, '2026-03-26 12:41:35', 'FailedLogin', '127.0.0.1'),
(81, 1, '2026-03-26 12:41:38', 'FailedLogin', '127.0.0.1'),
(82, 1, '2026-03-26 12:41:41', 'FailedLogin', '127.0.0.1'),
(83, 1, '2026-03-26 12:41:44', 'FailedLogin', '127.0.0.1'),
(84, 1, '2026-03-26 12:43:33', 'FailedLogin', '127.0.0.1'),
(85, 1, '2026-03-26 12:43:41', 'FailedLogin', '127.0.0.1'),
(86, 1, '2026-03-26 12:44:05', 'FailedLogin', '127.0.0.1'),
(87, 1, '2026-03-28 17:42:58', 'Login', '127.0.0.1'),
(88, 1, '2026-03-28 23:07:42', 'Login', '127.0.0.1'),
(89, 3, '2026-03-29 00:03:30', 'Login', '127.0.0.1'),
(90, 1, '2026-03-29 15:21:35', 'Login', '127.0.0.1'),
(91, 3, '2026-03-29 15:21:45', 'Login', '127.0.0.1'),
(92, 1, '2026-03-29 20:19:08', 'Logout', '127.0.0.1'),
(93, 3, '2026-03-29 20:19:11', 'Logout', '127.0.0.1'),
(94, 1, '2026-03-29 20:26:07', 'Login', '127.0.0.1'),
(95, 3, '2026-03-29 20:26:20', 'Login', '127.0.0.1'),
(96, 1, '2026-03-29 20:26:51', 'Logout', '127.0.0.1'),
(97, 3, '2026-03-29 20:27:55', 'Logout', '127.0.0.1'),
(98, 1, '2026-03-29 20:29:01', 'Login', '127.0.0.1'),
(99, 3, '2026-03-29 20:29:12', 'Login', '127.0.0.1'),
(100, 3, '2026-03-29 20:43:20', 'Logout', '127.0.0.1'),
(101, 1, '2026-03-29 20:44:47', 'Logout', '127.0.0.1'),
(102, 3, '2026-03-29 20:48:36', 'Login', '127.0.0.1'),
(103, 1, '2026-03-29 20:48:42', 'Login', '127.0.0.1'),
(104, 3, '2026-03-29 21:00:53', 'Logout', '127.0.0.1'),
(105, 6, '2026-03-29 21:01:07', 'Login', '127.0.0.1'),
(106, 6, '2026-03-29 21:03:18', 'Logout', '127.0.0.1'),
(107, 3, '2026-03-29 21:03:32', 'Login', '127.0.0.1'),
(108, 1, '2026-03-29 21:10:55', 'Logout', '127.0.0.1'),
(109, 3, '2026-03-29 21:10:56', 'Logout', '127.0.0.1'),
(110, 3, '2026-03-29 21:12:07', 'Login', '127.0.0.1'),
(111, 1, '2026-03-29 21:12:13', 'Login', '127.0.0.1'),
(112, 3, '2026-03-29 21:23:12', 'Logout', '127.0.0.1'),
(113, 6, '2026-03-29 21:23:25', 'Login', '127.0.0.1'),
(114, 6, '2026-03-29 21:25:15', 'Logout', '127.0.0.1'),
(115, 3, '2026-03-29 21:25:28', 'Login', '127.0.0.1'),
(116, 3, '2026-03-29 21:29:58', 'Logout', '127.0.0.1'),
(117, 6, '2026-03-29 21:30:08', 'Login', '127.0.0.1'),
(118, 6, '2026-03-29 21:31:30', 'Logout', '127.0.0.1'),
(119, 3, '2026-03-29 21:31:45', 'Login', '127.0.0.1'),
(120, 1, '2026-04-03 19:17:21', 'Login', '127.0.0.1'),
(121, 3, '2026-04-03 21:23:47', 'Login', '127.0.0.1'),
(122, 3, '2026-04-04 01:07:11', 'Logout', '127.0.0.1'),
(123, 6, '2026-04-04 01:07:24', 'Login', '127.0.0.1'),
(124, 6, '2026-04-04 01:09:37', 'Logout', '127.0.0.1'),
(125, 3, '2026-04-04 01:09:43', 'Login', '127.0.0.1'),
(126, 3, '2026-04-04 01:37:42', 'Logout', '127.0.0.1'),
(127, 6, '2026-04-04 01:37:49', 'Login', '127.0.0.1'),
(128, 6, '2026-04-04 01:38:29', 'Logout', '127.0.0.1'),
(129, 3, '2026-04-04 01:38:34', 'Login', '127.0.0.1'),
(130, 3, '2026-04-04 01:39:58', 'Logout', '127.0.0.1'),
(131, 6, '2026-04-04 01:40:06', 'Login', '127.0.0.1'),
(132, 6, '2026-04-04 01:40:29', 'Logout', '127.0.0.1'),
(133, 3, '2026-04-04 01:40:35', 'Login', '127.0.0.1'),
(134, 1, '2026-04-04 21:53:17', 'Login', '127.0.0.1'),
(135, 3, '2026-04-04 23:53:50', 'Login', '127.0.0.1'),
(136, 3, '2026-04-05 00:00:45', 'Logout', '127.0.0.1'),
(137, 3, '2026-04-05 00:01:03', 'FailedLogin', '127.0.0.1'),
(138, 3, '2026-04-05 00:06:18', 'FailedLogin', '127.0.0.1'),
(139, 3, '2026-04-05 00:06:27', 'Login', '127.0.0.1'),
(140, 3, '2026-04-05 00:06:43', 'Logout', '127.0.0.1'),
(141, 3, '2026-04-05 00:07:38', 'Login', '127.0.0.1'),
(142, 3, '2026-04-05 00:07:49', 'Logout', '127.0.0.1'),
(143, 3, '2026-04-05 00:07:59', 'Login', '127.0.0.1'),
(144, 3, '2026-04-05 00:34:30', 'Logout', '127.0.0.1'),
(145, 6, '2026-04-05 00:34:36', 'Login', '127.0.0.1'),
(146, 6, '2026-04-05 00:59:32', 'Logout', '127.0.0.1'),
(147, 3, '2026-04-05 01:00:16', 'FailedLogin', '127.0.0.1'),
(148, 3, '2026-04-05 01:00:33', 'FailedLogin', '127.0.0.1'),
(149, 3, '2026-04-05 01:05:54', 'Login', '127.0.0.1'),
(150, 3, '2026-04-05 01:06:44', 'Logout', '127.0.0.1'),
(151, 3, '2026-04-05 01:07:40', 'FailedLogin', '127.0.0.1'),
(152, 3, '2026-04-05 01:08:22', 'Login', '127.0.0.1'),
(153, 3, '2026-04-05 01:08:39', 'Logout', '127.0.0.1'),
(154, 1, '2026-04-05 13:55:30', 'Login', '127.0.0.1'),
(155, 3, '2026-04-05 17:23:13', 'Login', '127.0.0.1'),
(156, 1, '2026-04-06 17:53:23', 'FailedLogin', '127.0.0.1'),
(157, 1, '2026-04-06 17:55:44', 'Login', '127.0.0.1'),
(158, 1, '2026-04-09 14:28:51', 'Login', '127.0.0.1'),
(159, 3, '2026-04-09 19:36:46', 'Login', '127.0.0.1'),
(160, 1, '2026-04-10 20:42:25', 'Login', '127.0.0.1'),
(161, 1, '2026-04-14 15:17:29', 'Login', '127.0.0.1'),
(162, 1, '2026-04-14 20:59:26', 'Login', '127.0.0.1'),
(163, 1, '2026-04-15 20:28:10', 'Login', '127.0.0.1'),
(164, 1, '2026-04-16 20:12:23', 'Login', '127.0.0.1'),
(165, 1, '2026-04-17 19:41:46', 'Login', '127.0.0.1'),
(166, 1, '2026-04-19 15:39:18', 'Login', '127.0.0.1'),
(167, 1, '2026-04-20 10:18:23', 'Login', '127.0.0.1'),
(168, 2, '2026-04-20 10:22:03', 'Login', '127.0.0.1'),
(169, 2, '2026-04-20 10:22:54', 'Logout', '127.0.0.1'),
(170, 3, '2026-04-20 10:23:22', 'Login', '127.0.0.1'),
(171, 3, '2026-04-20 13:25:39', 'Logout', '127.0.0.1'),
(172, 6, '2026-04-20 13:26:01', 'Login', '127.0.0.1'),
(173, 1, '2026-04-22 21:07:37', 'Login', '127.0.0.1'),
(174, 1, '2026-04-23 15:28:01', 'Login', '127.0.0.1'),
(175, 3, '2026-04-23 15:28:21', 'Login', '127.0.0.1'),
(176, 3, '2026-04-23 15:49:52', 'Logout', '127.0.0.1'),
(177, 6, '2026-04-23 15:49:57', 'Login', '127.0.0.1'),
(178, 6, '2026-04-23 15:50:30', 'Logout', '127.0.0.1'),
(179, 3, '2026-04-23 15:50:37', 'Login', '127.0.0.1'),
(180, 1, '2026-04-23 19:53:00', 'Login', '127.0.0.1'),
(181, 3, '2026-04-23 19:53:10', 'Login', '127.0.0.1'),
(182, 1, '2026-04-24 14:12:54', 'Login', '127.0.0.1'),
(183, 3, '2026-04-24 15:26:52', 'Login', '127.0.0.1'),
(184, 1, '2026-04-25 19:51:21', 'Login', '127.0.0.1'),
(185, 3, '2026-04-25 22:48:18', 'Login', '127.0.0.1'),
(186, 1, '2026-04-27 16:37:55', 'Login', '127.0.0.1'),
(187, 1, '2026-04-27 22:57:28', 'Login', '127.0.0.1'),
(188, 3, '2026-04-27 23:33:59', 'Login', '127.0.0.1'),
(189, 1, '2026-04-29 10:21:26', 'Login', '127.0.0.1'),
(190, 1, '2026-04-29 12:33:12', 'Login', '127.0.0.1'),
(191, 1, '2026-04-30 14:18:49', 'Login', '127.0.0.1');

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
(146, '2026_03_03_210449_add_iar_number_to_delivery_receipt_table', 51),
(147, '2026_03_21_000001_add_article_image_to_articles_table', 52),
(148, '2026_03_22_000201_create_user_push_tokens_table', 53),
(149, '2026_03_22_000202_create_user_notifications_table', 54),
(150, '2026_03_22_000203_fix_notification_userid_foreign_keys', 54),
(151, '2026_03_23_000300_deduplicate_trusted_devices', 55),
(152, '2026_03_24_000300_create_repair_vendors_and_asset_service_records', 56),
(153, '2026_03_25_140000_update_asset_service_record_details_for_workflow', 57),
(154, '2026_03_25_170000_drop_redundant_columns_from_asset_service_record_details', 58),
(155, '2026_03_25_200000_refactor_asset_service_report_source_to_department', 59),
(156, '2026_03_28_000000_adjust_asset_service_statuses', 60),
(157, '2026_03_28_000001_add_verification_note_to_service_record_details', 61),
(158, '2026_03_28_000002_rename_verification_to_fixed_in_asset_service_statuses', 62),
(159, '2026_03_28_000003_add_final_notes_to_service_record_details', 63),
(160, '2026_03_28_000004_add_final_notes_to_asset_service_records', 64),
(161, '2026_03_28_000005_remove_final_notes_from_service_record_details', 65),
(162, '2026_03_28_000006_restore_verification_enum_in_asset_service_statuses', 66),
(163, '2026_03_29_000001_update_handling_decision_to_warranty_no_warranty', 67),
(164, '2026_03_29_000002_add_under_repair_condition', 67),
(165, '2026_04_03_193500_add_unique_indexes_to_iar_number_columns', 68),
(166, '2026_04_05_080000_create_property_waste_reports_table', 69),
(167, '2026_04_05_081000_rename_unrepairable_to_unserviceable_in_service_statuses', 69),
(168, '2026_04_05_100000_simplify_property_waste_status_flow', 70),
(169, '2026_04_05_101000_enforce_simplified_property_waste_status_enum', 71),
(170, '2026_04_09_130000_create_property_card_entries_table', 72),
(171, '2026_04_14_150000_create_rsmi_reports_tables', 73),
(172, '2026_04_14_180000_rename_rsmi_tables_remove_signature_columns', 74),
(173, '2026_04_16_150000_create_rpc_inventory_tables', 75),
(174, '2026_04_23_000001_enforce_gso_requesters_on_property_transfer_and_borrow', 76),
(175, '2026_04_23_000002_align_requester_triggers_with_receiving_department', 77),
(176, '2026_04_23_000003_add_completed_status_to_propertyborrow', 78),
(177, '2026_04_24_000001_add_approved_quantity_to_transfer_and_borrow_items', 79),
(178, '2026_04_25_000005_add_ris_item_id_to_propertyissuancedetail', 80);

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
  `Status` enum('pending','approved','rejected','borrowed','partially_returned','returned','completed','cancelled') NOT NULL DEFAULT 'pending',
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
(1, 'PBR-2026-0001', 6, 30, 23, 2, 4, 'Explain the temporary use case.', 1, '2026-03-26', 'approved', 1, 'ok', 3, '2026-03-18 13:19:45', 'returned', 1, '2026-03-18 13:20:42', '2026-03-18 13:20:42', NULL, '2026-03-18 13:18:19', '2026-03-18 13:25:11', NULL),
(2, 'PBR-2026-0002', 3, 23, 30, 4, 2, 'Hulam Projector', 1, '2026-03-31', 'pending', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, '2026-03-25 13:58:31', '2026-03-25 13:58:31', NULL),
(3, 'PBR-2026-0003', 3, 23, 30, 4, 2, 'Hulam Hand Tools Set', 1, '2026-03-30', 'approved', 1, 'Okay', 6, '2026-03-25 14:01:25', 'returned', 1, '2026-03-25 14:01:58', '2026-03-25 14:01:58', NULL, '2026-03-25 14:00:36', '2026-03-25 14:03:14', NULL),
(4, 'PBR-2026-0004', 3, 23, 30, 4, 2, 'hulam', 1, '2026-04-01', 'approved', 1, 'yes', 6, '2026-03-29 13:30:53', 'returned', 1, '2026-03-29 13:31:19', '2026-03-29 13:31:19', NULL, '2026-03-29 13:29:43', '2026-03-29 13:34:27', NULL),
(5, 'PBR-2026-0005', 3, 23, 30, 4, 2, NULL, 1, '2026-04-06', 'approved', 1, 'ok!!!!!!!!!!!!!!!!!!!', 6, '2026-04-03 17:38:24', 'returned', 1, '2026-04-03 17:37:23', '2026-04-03 17:38:56', NULL, '2026-04-03 17:36:35', '2026-04-03 17:40:55', NULL),
(6, 'PBR-2026-0006', 3, 23, 30, 4, 2, 'll', 1, '2026-04-30', 'pending', NULL, NULL, NULL, NULL, 'approved', 1, '2026-04-20 05:27:04', NULL, NULL, '2026-04-20 05:24:28', '2026-04-20 05:27:04', NULL),
(7, 'PBR-2026-0007', 3, 23, 30, 4, 2, 'basta', 1, '2026-04-26', 'pending', NULL, NULL, NULL, NULL, 'completed', 1, '2026-04-23 13:08:44', '2026-04-23 13:08:59', NULL, '2026-04-23 13:08:44', '2026-04-23 13:09:32', NULL),
(8, 'PBR-2026-0008', 3, 23, 30, 4, 2, NULL, 3, '2026-04-25', 'approved', 2, 'nawala daw isa ka extension cord,', 1, '2026-04-24 11:24:40', 'partially_returned', 1, '2026-04-24 08:19:25', '2026-04-24 11:40:52', NULL, '2026-04-24 08:19:25', '2026-04-24 11:41:23', NULL);

--
-- Triggers `propertyborrow`
--
DELIMITER $$
CREATE TRIGGER `trg_propertyborrow_requester_role_check` BEFORE INSERT ON `propertyborrow` FOR EACH ROW BEGIN
        DECLARE requester_department_id INT;

        SELECT u.DepartmentID
        INTO requester_department_id
        FROM `user` u
        WHERE u.UserID = NEW.RequestedByUserID
        LIMIT 1;

        IF requester_department_id IS NULL OR requester_department_id <> NEW.ToDepartmentID THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'PBR requester must belong to borrowing department';
        END IF;
    END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_propertyborrow_requester_role_check_update` BEFORE UPDATE ON `propertyborrow` FOR EACH ROW BEGIN
        DECLARE requester_department_id INT;

        IF NEW.RequestedByUserID <> OLD.RequestedByUserID OR NEW.ToDepartmentID <> OLD.ToDepartmentID THEN
            SELECT u.DepartmentID
            INTO requester_department_id
            FROM `user` u
            WHERE u.UserID = NEW.RequestedByUserID
            LIMIT 1;

            IF requester_department_id IS NULL OR requester_department_id <> NEW.ToDepartmentID THEN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'PBR requester must belong to borrowing department';
            END IF;
        END IF;
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `propertyborrowitems`
--

CREATE TABLE `propertyborrowitems` (
  `BorrowItemID` bigint(20) UNSIGNED NOT NULL,
  `BorrowID` bigint(20) UNSIGNED NOT NULL,
  `InventoryDetailID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 1,
  `ApprovedQuantity` int(10) UNSIGNED DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `propertyborrowitems`
--

INSERT INTO `propertyborrowitems` (`BorrowItemID`, `BorrowID`, `InventoryDetailID`, `Quantity`, `ApprovedQuantity`, `Remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 5, 1, NULL, NULL, '2026-03-18 13:18:19', '2026-03-18 13:18:19', NULL),
(2, 2, 30, 1, NULL, NULL, '2026-03-25 13:58:31', '2026-03-25 13:58:31', NULL),
(3, 3, 28, 1, NULL, NULL, '2026-03-25 14:00:36', '2026-03-25 14:00:36', NULL),
(4, 4, 73, 1, NULL, NULL, '2026-03-29 13:29:43', '2026-03-29 13:29:43', NULL),
(5, 5, 25, 1, NULL, NULL, '2026-04-03 17:36:35', '2026-04-03 17:36:35', NULL),
(6, 6, 47, 1, NULL, NULL, '2026-04-20 05:24:28', '2026-04-20 05:24:28', NULL),
(7, 7, 26, 1, NULL, NULL, '2026-04-23 13:08:44', '2026-04-23 13:08:44', NULL),
(8, 8, 26, 1, 1, NULL, '2026-04-24 08:19:25', '2026-04-24 11:24:40', NULL),
(9, 8, 27, 1, 1, NULL, '2026-04-24 08:19:25', '2026-04-24 11:24:40', NULL),
(10, 8, 46, 1, 0, 'Shortfall reason: nawala daw isa ka extension cord,', '2026-04-24 08:19:25', '2026-04-24 11:24:40', NULL);

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
(10, NULL, NULL, 32, NULL, '2026-03-16', NULL, 'Backfilled from generated DR #DR-2026-03-18-004', '2026-03-18 19:41:22', NULL, 'INV-20260318-194122-32', NULL),
(14, NULL, NULL, 42, NULL, '2026-03-22', NULL, 'Auto-ingested from DR #dr23', '2026-03-22 22:54:56', NULL, 'INV-20260322-225456-42', NULL),
(15, NULL, NULL, 43, NULL, '2026-03-22', NULL, 'Auto-ingested from DR #dr31', '2026-03-22 23:17:31', NULL, 'INV-20260322-231731-43', NULL),
(16, NULL, NULL, 44, NULL, '2026-03-22', NULL, 'Auto-ingested from DR #dr11', '2026-03-22 23:22:27', NULL, 'INV-20260322-232227-44', NULL),
(17, NULL, NULL, 45, NULL, '2026-03-24', NULL, 'Auto-ingested from DR #d2', '2026-03-24 11:40:12', NULL, 'INV-20260324-114012-45', NULL),
(18, NULL, NULL, 46, NULL, '2026-03-24', NULL, 'Auto-ingested from DR #d21', '2026-03-24 11:43:37', NULL, 'INV-20260324-114337-46', NULL),
(19, NULL, NULL, 47, NULL, '2026-03-29', NULL, 'Auto-ingested from DR #DR-TEST-1', '2026-03-29 20:32:46', NULL, 'INV-20260329-203246-47', NULL),
(20, NULL, NULL, 48, NULL, '2026-03-29', NULL, 'Auto-ingested from DR #DR-TEST-2', '2026-03-29 20:52:16', NULL, 'INV-20260329-205216-48', NULL),
(21, NULL, NULL, 49, NULL, '2026-03-29', NULL, 'Auto-ingested from DR #DR-TEST-3', '2026-03-29 21:15:35', NULL, 'INV-20260329-211535-49', NULL),
(52, NULL, NULL, 86, NULL, '2026-04-20', NULL, 'Auto-ingested from DR #drtest12321', '2026-04-20 12:05:52', NULL, 'INV-20260420-120552-86', NULL),
(53, NULL, NULL, 87, NULL, '2026-04-20', NULL, 'All Good', '2026-04-20 13:18:33', NULL, 'INV-20260420-131833-87', NULL),
(54, NULL, NULL, 88, NULL, '2026-04-08', NULL, 'Inventory record established from inspected Delivery Receipt DR-2026-04-27-001', '2026-04-27 23:26:20', NULL, 'INV-20260427-232620-88', NULL),
(55, NULL, NULL, 90, NULL, '2026-04-12', NULL, 'Inventory record established from inspected Delivery Receipt DR-2026-04-27-002', '2026-04-27 23:26:20', NULL, 'INV-20260427-232620-90', NULL),
(56, NULL, NULL, 89, NULL, '2026-04-16', NULL, 'Inventory record established from inspected Delivery Receipt DR-2026-04-27-003', '2026-04-27 23:26:20', NULL, 'INV-20260427-232620-89', NULL),
(57, NULL, NULL, 91, NULL, '2026-04-20', NULL, 'Inventory record established from inspected Delivery Receipt DR-2026-04-27-004', '2026-04-27 23:26:20', NULL, 'INV-20260427-232620-91', NULL);

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
(5, 5, 7, NULL, 'smsmsmsm', '2026-000001', NULL, 1, 0, 2, 2, 100.00, 100.00, NULL, 98, 1, 's', 's', NULL, '2026-03-18 18:11:05', '2026-03-29 20:55:46', NULL),
(6, 6, 8, NULL, 'bugnaw', '2026-000002', NULL, 1, 0, 2, 2, 2333.00, 2333.00, NULL, 99, 1, 'he', 'hah', NULL, '2026-03-18 19:27:56', '2026-03-18 19:44:40', NULL),
(7, 7, 6, NULL, 'Office Desk (OD-PRM-120)', '2026-000003', NULL, 1, 0, 2, 2, 8500.00, 8500.00, NULL, 63, 1, 'Prime Office', 'OD-PRM-120', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(8, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000004', NULL, 1, 0, 2, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(9, 7, 6, NULL, 'Filing Cabinet (FC-STL-4D)', '2026-000005', NULL, 1, 0, 2, 2, 11500.00, 11500.00, NULL, 65, 1, 'SteelSafe', 'FC-STL-4D', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(10, 7, 7, NULL, 'Desktop Computer (ThinkCentre-M70)', '2026-000006', NULL, 1, 0, 2, 2, 42500.00, 42500.00, NULL, 66, 1, 'Lenovo', 'ThinkCentre-M70', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(11, 7, 7, NULL, 'Printer/Scanner (DCP-L5650DN)', '2026-000007', NULL, 1, 0, 2, 2, 18900.00, 18900.00, NULL, 67, 1, 'Brother', 'DCP-L5650DN', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(12, 7, 2, NULL, 'Telephone Set (KX-TS880)', '2026-000008', 'TEL-2026-001', 1, 0, 2, 2, 2350.00, 2350.00, NULL, 68, 1, 'Panasonic', 'KX-TS880', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
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
(25, 10, 7, NULL, 'Laptop Computer (TravelMate-P2)', '2026-000021', 'LTP-2026-002', 1, 0, 2, 2, 49900.00, 49900.00, NULL, 81, 1, 'Acer', 'TravelMate-P2', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(26, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000022', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(27, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000023', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(28, 10, 12, NULL, 'Hand Tools Set (HTS-112)', '2026-000024', NULL, 1, 0, 2, 3, 4200.00, 4200.00, NULL, 84, 1, 'Ingco', 'HTS-112', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(29, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000025', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(30, 10, 7, NULL, 'Projector (MS560)', '2026-000026', 'PROJ-2026-002', 1, 0, 2, 2, 27900.00, 27900.00, NULL, 86, 1, 'BenQ', 'MS560', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(31, 7, 6, NULL, 'Office Desk (OD-PRM-120)', '2026-000027', NULL, 1, 0, 2, 2, 8500.00, 8500.00, NULL, 63, 1, 'Prime Office', 'OD-PRM-120', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(32, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000028', NULL, 1, 0, 2, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(33, 7, 6, NULL, 'Filing Cabinet (FC-STL-4D)', '2026-000029', NULL, 1, 0, 2, 2, 11500.00, 11500.00, NULL, 65, 1, 'SteelSafe', 'FC-STL-4D', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(34, 7, 7, NULL, 'Desktop Computer (ThinkCentre-M70)', '2026-000030', NULL, 1, 0, 2, 2, 42500.00, 42500.00, NULL, 66, 1, 'Lenovo', 'ThinkCentre-M70', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(35, 7, 7, NULL, 'Printer/Scanner (DCP-L5650DN)', '2026-000031', NULL, 1, 0, 2, 2, 18900.00, 18900.00, NULL, 67, 1, 'Brother', 'DCP-L5650DN', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(36, 8, 7, NULL, 'Desktop Computer (ProDesk-400)', '2026-000032', NULL, 1, 1, 1, 2, 43800.00, 43800.00, NULL, 75, 1, 'HP', 'ProDesk-400', NULL, '2026-03-18 19:41:22', NULL, NULL),
(37, 8, 7, NULL, 'Printer/Scanner (MF273dw)', '2026-000033', NULL, 1, 1, 1, 2, 17200.00, 17200.00, NULL, 77, 1, 'Canon', 'MF273dw', NULL, '2026-03-18 19:41:22', NULL, NULL),
(38, 8, 6, NULL, 'Filing Cabinet (FC-STL-3D)', '2026-000034', NULL, 1, 1, 1, 2, 11200.00, 11200.00, NULL, 78, 1, 'SteelSafe', 'FC-STL-3D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(39, 8, 2, NULL, 'Telephone Set (KX-TS500)', '2026-000035', NULL, 1, 1, 1, 2, 2290.00, 2290.00, NULL, 79, 1, 'Panasonic', 'KX-TS500', NULL, '2026-03-18 19:41:22', NULL, NULL),
(40, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000036', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(41, 9, 8, NULL, 'Air Conditioner (Aura-1.5HP)', '2026-000037', NULL, 1, 1, 1, 2, 33800.00, 33800.00, NULL, 71, 1, 'Carrier', 'Aura-1.5HP', NULL, '2026-03-18 19:41:22', NULL, NULL),
(42, 9, 6, NULL, 'Office Desk (OD-PRM-140)', '2026-000038', NULL, 1, 1, 1, 2, 8400.00, 8400.00, NULL, 72, 1, 'Prime Office', 'OD-PRM-140', NULL, '2026-03-18 19:41:22', NULL, NULL),
(43, 9, 6, NULL, 'Office Chair (CH-ERG-24)', '2026-000039', NULL, 1, 1, 1, 2, 3150.00, 3150.00, NULL, 73, 1, 'ErgoSeat', 'CH-ERG-24', NULL, '2026-03-18 19:41:22', NULL, NULL),
(44, 9, 12, NULL, 'Table Fan (TF-16X)', '2026-000040', NULL, 1, 1, 1, 2, 2450.00, 2450.00, NULL, 74, 1, 'Asahi', 'TF-16X', NULL, '2026-03-18 19:41:22', NULL, NULL),
(45, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000041', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(46, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000042', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(47, 10, 12, NULL, 'Hand Tools Set (HTS-112)', '2026-000043', NULL, 1, 0, 2, 3, 4200.00, 4200.00, NULL, 84, 1, 'Ingco', 'HTS-112', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(48, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000044', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(49, 7, 6, NULL, 'Office Desk (OD-PRM-120)', '2026-000045', NULL, 1, 0, 2, 2, 8500.00, 8500.00, NULL, 63, 1, 'Prime Office', 'OD-PRM-120', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(50, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000046', NULL, 1, 0, 2, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(51, 7, 7, NULL, 'Desktop Computer (ThinkCentre-M70)', '2026-000047', NULL, 1, 0, 2, 2, 42500.00, 42500.00, NULL, 66, 1, 'Lenovo', 'ThinkCentre-M70', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(52, 8, 7, NULL, 'Desktop Computer (ProDesk-400)', '2026-000048', NULL, 1, 1, 1, 2, 43800.00, 43800.00, NULL, 75, 1, 'HP', 'ProDesk-400', NULL, '2026-03-18 19:41:22', NULL, NULL),
(53, 8, 7, NULL, 'Printer/Scanner (MF273dw)', '2026-000049', NULL, 1, 1, 1, 2, 17200.00, 17200.00, NULL, 77, 1, 'Canon', 'MF273dw', NULL, '2026-03-18 19:41:22', NULL, NULL),
(54, 8, 2, NULL, 'Telephone Set (KX-TS500)', '2026-000050', NULL, 1, 1, 1, 2, 2290.00, 2290.00, NULL, 79, 1, 'Panasonic', 'KX-TS500', NULL, '2026-03-18 19:41:22', NULL, NULL),
(55, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000051', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(56, 9, 8, NULL, 'Air Conditioner (Aura-1.5HP)', '2026-000052', NULL, 1, 1, 1, 2, 33800.00, 33800.00, NULL, 71, 1, 'Carrier', 'Aura-1.5HP', NULL, '2026-03-18 19:41:22', NULL, NULL),
(57, 9, 6, NULL, 'Office Desk (OD-PRM-140)', '2026-000053', NULL, 1, 1, 1, 2, 8400.00, 8400.00, NULL, 72, 1, 'Prime Office', 'OD-PRM-140', NULL, '2026-03-18 19:41:22', NULL, NULL),
(58, 9, 6, NULL, 'Office Chair (CH-ERG-24)', '2026-000054', NULL, 1, 1, 1, 2, 3150.00, 3150.00, NULL, 73, 1, 'ErgoSeat', 'CH-ERG-24', NULL, '2026-03-18 19:41:22', NULL, NULL),
(59, 9, 12, NULL, 'Table Fan (TF-16X)', '2026-000055', NULL, 1, 1, 1, 2, 2450.00, 2450.00, NULL, 74, 1, 'Asahi', 'TF-16X', NULL, '2026-03-18 19:41:22', NULL, NULL),
(60, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000056', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(61, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000057', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(62, 10, 12, NULL, 'Hand Tools Set (HTS-112)', '2026-000058', NULL, 1, 0, 2, 3, 4200.00, 4200.00, NULL, 84, 1, 'Ingco', 'HTS-112', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(63, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000059', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(64, 7, 6, NULL, 'Office Desk (OD-PRM-120)', '2026-000060', NULL, 1, 0, 2, 2, 8500.00, 8500.00, NULL, 63, 1, 'Prime Office', 'OD-PRM-120', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(65, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000061', NULL, 1, 0, 2, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(66, 7, 7, NULL, 'Desktop Computer (ThinkCentre-M70)', '2026-000062', NULL, 1, 0, 2, 2, 42500.00, 42500.00, NULL, 66, 1, 'Lenovo', 'ThinkCentre-M70', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(67, 8, 7, NULL, 'Desktop Computer (ProDesk-400)', '2026-000063', NULL, 1, 1, 1, 2, 43800.00, 43800.00, NULL, 75, 1, 'HP', 'ProDesk-400', NULL, '2026-03-18 19:41:22', NULL, NULL),
(68, 8, 2, NULL, 'Telephone Set (KX-TS500)', '2026-000064', NULL, 1, 1, 1, 2, 2290.00, 2290.00, NULL, 79, 1, 'Panasonic', 'KX-TS500', NULL, '2026-03-18 19:41:22', NULL, NULL),
(69, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000065', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(70, 9, 8, NULL, 'Air Conditioner (Aura-1.5HP)', '2026-000066', NULL, 1, 1, 1, 2, 33800.00, 33800.00, NULL, 71, 1, 'Carrier', 'Aura-1.5HP', NULL, '2026-03-18 19:41:22', NULL, NULL),
(71, 9, 6, NULL, 'Office Chair (CH-ERG-24)', '2026-000067', NULL, 1, 1, 1, 2, 3150.00, 3150.00, NULL, 73, 1, 'ErgoSeat', 'CH-ERG-24', NULL, '2026-03-18 19:41:22', NULL, NULL),
(72, 9, 12, NULL, 'Table Fan (TF-16X)', '2026-000068', NULL, 1, 1, 1, 2, 2450.00, 2450.00, NULL, 74, 1, 'Asahi', 'TF-16X', NULL, '2026-03-18 19:41:22', NULL, NULL),
(73, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000069', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(74, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000070', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(75, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000071', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(76, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000072', NULL, 1, 0, 2, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(77, 7, 7, NULL, 'Desktop Computer (ThinkCentre-M70)', '2026-000073', NULL, 1, 0, 2, 2, 42500.00, 42500.00, NULL, 66, 1, 'Lenovo', 'ThinkCentre-M70', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(78, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000074', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(79, 9, 6, NULL, 'Office Chair (CH-ERG-24)', '2026-000075', NULL, 1, 1, 1, 2, 3150.00, 3150.00, NULL, 73, 1, 'ErgoSeat', 'CH-ERG-24', NULL, '2026-03-18 19:41:22', NULL, NULL),
(80, 9, 12, NULL, 'Table Fan (TF-16X)', '2026-000076', NULL, 1, 1, 1, 2, 2450.00, 2450.00, NULL, 74, 1, 'Asahi', 'TF-16X', NULL, '2026-03-18 19:41:22', NULL, NULL),
(81, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000077', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(82, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000078', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(83, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000079', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(84, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000080', NULL, 1, 0, 2, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(85, 7, 7, NULL, 'Desktop Computer (ThinkCentre-M70)', '2026-000081', NULL, 1, 0, 2, 2, 42500.00, 42500.00, NULL, 66, 1, 'Lenovo', 'ThinkCentre-M70', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(86, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000082', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(87, 9, 6, NULL, 'Office Chair (CH-ERG-24)', '2026-000083', NULL, 1, 1, 1, 2, 3150.00, 3150.00, NULL, 73, 1, 'ErgoSeat', 'CH-ERG-24', NULL, '2026-03-18 19:41:22', NULL, NULL),
(88, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000084', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(89, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000085', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(90, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000086', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(91, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000087', NULL, 1, 0, 2, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(92, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000088', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(93, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000089', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(94, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000090', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(95, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000091', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(96, 7, 6, NULL, 'Office Chair (CH-ERG-22)', '2026-000092', NULL, 1, 0, 2, 2, 3200.00, 3200.00, NULL, 64, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-18 19:41:22', '2026-03-22 22:53:00', NULL),
(97, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000093', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(98, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000094', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(99, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000095', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(100, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000096', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(101, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000097', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(102, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000098', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(103, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000099', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(104, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000100', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(105, 8, 11, NULL, 'Calculator (DJ-120D)', '2026-000101', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 80, 1, 'Casio', 'DJ-120D', NULL, '2026-03-18 19:41:22', NULL, NULL),
(106, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000102', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(107, 10, 12, NULL, 'Extension Cord (EXT-5S)', '2026-000103', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 83, 1, 'Royu', 'EXT-5S', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(108, 10, 6, NULL, 'Waste Basket (WB-18L)', '2026-000104', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 85, 1, 'Omni', 'WB-18L', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(109, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000105', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(110, 10, 11, NULL, 'Office Lamp (OFL-9W)', '2026-000106', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 82, 1, 'Firefly', 'OFL-9W', NULL, '2026-03-18 19:41:22', '2026-03-24 11:55:01', NULL),
(134, 14, 11, NULL, 'ha', '2026-000107', NULL, 1, 0, 2, 2, 122.97, 122.97, NULL, 100, 1, NULL, NULL, NULL, '2026-03-22 22:54:56', '2026-03-22 22:55:54', NULL),
(135, 15, 11, NULL, 'heaha', '2026-000108', NULL, 1, 0, 2, 2, 123321.00, 123321.00, NULL, 101, 1, NULL, NULL, NULL, '2026-03-22 23:17:31', '2026-03-22 23:17:39', NULL),
(136, 16, 11, NULL, 'ha', '2026-000109', NULL, 1, 0, 2, 2, 23.00, 23.00, NULL, 102, 1, NULL, NULL, NULL, '2026-03-22 23:22:27', '2026-03-22 23:23:28', NULL),
(137, 17, 7, NULL, 'ASUS', '2026-000110', '231', 1, 0, 2, 2, 30000.00, 30000.00, NULL, 103, 1, 'ASUS', 'ROG', NULL, '2026-03-24 11:40:12', '2026-03-24 11:49:38', NULL),
(138, 18, 7, NULL, 'MSI', '2026-000111', '32', 1, 0, 2, 2, 25000.00, 25000.00, NULL, 104, 1, 'MSI', 'MSI', NULL, '2026-03-24 11:43:37', '2026-03-29 21:39:14', NULL),
(139, 19, 7, NULL, 'Lenovo ThinkCentre M70', '2026-000112', 'LNV2026-001', 1, 0, 2, 2, 42500.00, 42500.00, NULL, 105, 1, 'Lenovo', 'M70', NULL, '2026-03-29 20:32:46', '2026-03-29 20:56:46', NULL),
(140, 19, 6, NULL, 'Ergonomic Mesh Chair', '2026-000113', 'ERGO-2026-002', 1, 0, 2, 2, 3200.00, 3200.00, NULL, 106, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-29 20:32:46', '2026-03-29 20:56:46', NULL),
(141, 20, 6, NULL, 'Ergonomic Mesh Chair', '2026-000114', 'ERGO-2026-002', 1, 0, 2, 2, 3200.00, 3200.00, NULL, 107, 1, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-29 20:52:16', '2026-03-29 20:58:25', NULL),
(142, 20, 6, NULL, 'Brother DCP-L5650DN', '2026-000115', 'BR-2026-003', 1, 0, 2, 2, 18900.00, 18900.00, NULL, 108, 2, 'Brother', 'DCP-L5650DN', NULL, '2026-03-29 20:52:16', '2026-04-05 18:29:04', NULL),
(143, 21, 6, NULL, 'Ergonomic Mesh Chair', '2026-000116', 'ERGO-2026-002', 1, 0, 4, 2, 3200.00, 3200.00, NULL, 109, 2, 'ErgoSeat', 'CH-ERG-22', NULL, '2026-03-29 21:15:35', '2026-04-05 18:24:24', NULL),
(144, 21, 7, NULL, 'Brother DCP-L5650DN', '2026-000117', 'BR-2026-003', 1, 0, 4, 2, 18900.00, 18900.00, NULL, 110, 2, 'Brother', 'DCP-L5650DN', NULL, '2026-03-29 21:15:35', '2026-04-05 17:22:46', NULL),
(145, 52, 8, NULL, 'sd', '2026-000118', '1', 1, 0, 2, 2, 2400.00, 2400.00, NULL, 141, 1, 's', 'd', NULL, '2026-04-20 12:05:52', '2026-04-20 12:09:16', NULL),
(146, 53, 11, NULL, 'Calculator mo work', '2026-000119', 'brand z', 1, 0, 2, 2, 9998.90, 9998.90, NULL, 142, 1, 'brand x', 'brand y', NULL, '2026-04-20 13:18:33', '2026-04-20 13:20:37', NULL),
(147, 53, 8, NULL, 'bugnaw', '2026-000120', 'aircon z', 1, 0, 2, 2, 19000.00, 19000.00, NULL, 143, 1, 'aircon x', 'aircon y', NULL, '2026-04-20 13:18:33', '2026-04-20 13:20:37', NULL),
(148, 54, 7, NULL, 'Lenovo ThinkCentre M70', '2026-000121', 'LNV2026-001', 1, 0, 2, 2, 42500.00, 42500.00, NULL, 144, 1, 'Lenovo', 'M70', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 10:22:06', NULL),
(149, 54, 7, NULL, 'Brother DCP-L5650DN', '2026-000122', 'BR-2026-003', 1, 0, 2, 2, 18900.00, 18900.00, NULL, 146, 1, 'Brother', 'DCP-L5650DN', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 10:22:06', NULL),
(150, 54, 6, NULL, 'Ergonomic Mesh Chair', '2026-000123', 'ERGO-2026-002', 1, 0, 2, 2, 3200.00, 3200.00, NULL, 149, 1, 'ErgoSeat', 'CH-ERG-22', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 10:22:06', NULL),
(151, 54, 26, NULL, 'Portable rechargeable emergency lamp', '2026-000124', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 162, 1, 'Firefly', 'REL-2026-A', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 10:22:06', NULL),
(152, 54, 26, NULL, 'Portable rechargeable emergency lamp', '2026-000125', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 162, 1, 'Firefly', 'REL-2026-A', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 10:22:06', NULL),
(153, 54, 26, NULL, 'Portable rechargeable emergency lamp', '2026-000126', NULL, 1, 0, 2, 2, 980.00, 980.00, NULL, 162, 1, 'Firefly', 'REL-2026-A', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 10:22:06', NULL),
(154, 54, 17, NULL, '32GB USB 3.0 flash drive', '2026-000127', NULL, 1, 0, 2, 2, 520.00, 520.00, NULL, 164, 1, 'SanDisk', 'UFD-32G', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 10:22:06', NULL),
(155, 54, 17, NULL, '32GB USB 3.0 flash drive', '2026-000128', NULL, 1, 0, 2, 2, 520.00, 520.00, NULL, 164, 1, 'SanDisk', 'UFD-32G', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 10:22:06', NULL),
(156, 54, 16, NULL, 'Wireless keyboard and mouse set', '2026-000129', NULL, 1, 0, 2, 3, 1250.00, 1250.00, NULL, 166, 1, 'LogiTech', 'KM-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 10:22:06', NULL),
(157, 54, 16, NULL, 'Wireless keyboard and mouse set', '2026-000130', NULL, 1, 0, 2, 3, 1250.00, 1250.00, NULL, 166, 1, 'LogiTech', 'KM-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 10:22:06', NULL),
(158, 54, 16, NULL, 'Wireless keyboard and mouse set', '2026-000131', NULL, 1, 0, 2, 3, 1250.00, 1250.00, NULL, 166, 1, 'LogiTech', 'KM-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 10:22:06', NULL),
(159, 54, 16, NULL, 'Wireless keyboard and mouse set', '2026-000132', NULL, 1, 0, 2, 3, 1250.00, 1250.00, NULL, 166, 1, 'LogiTech', 'KM-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 10:22:06', NULL),
(160, 54, 16, NULL, 'Wireless keyboard and mouse set', '2026-000133', NULL, 1, 0, 2, 3, 1250.00, 1250.00, NULL, 166, 1, 'LogiTech', 'KM-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 10:22:06', NULL),
(161, 55, 7, NULL, 'Acer TravelMate P2', '2026-000134', 'ACR-2026-004', 1, 0, 2, 2, 52000.00, 52000.00, NULL, 145, 1, 'Acer', 'TravelMate-P2', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:23', NULL),
(162, 55, 6, NULL, '4-Drawer Steel Filing Cabinet', '2026-000135', NULL, 1, 0, 2, 2, 11500.00, 11500.00, NULL, 151, 1, 'SteelSafe', 'FC-STL-4D', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:23', NULL),
(163, 55, 6, NULL, '4-Drawer Steel Filing Cabinet', '2026-000136', NULL, 1, 0, 2, 2, 11500.00, 11500.00, NULL, 151, 1, 'SteelSafe', 'FC-STL-4D', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:23', NULL),
(164, 55, 7, NULL, 'Epson EB-X06', '2026-000137', 'EPS-2026-005', 1, 0, 2, 2, 28500.00, 28500.00, NULL, 153, 1, 'Epson', 'EB-X06', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:23', NULL),
(165, 55, 18, NULL, '1.5L electric kettle', '2026-000138', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 157, 1, 'Hanabishi', 'EK-15', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:23', NULL),
(166, 55, 18, NULL, '1.5L electric kettle', '2026-000139', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 157, 1, 'Hanabishi', 'EK-15', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:23', NULL),
(167, 55, 18, NULL, '1.5L electric kettle', '2026-000140', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 157, 1, 'Hanabishi', 'EK-15', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:23', NULL),
(168, 55, 18, NULL, '1.5L electric kettle', '2026-000141', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 157, 1, 'Hanabishi', 'EK-15', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:23', NULL),
(169, 55, 18, NULL, '1.5L electric kettle', '2026-000142', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 157, 1, 'Hanabishi', 'EK-15', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:23', NULL),
(170, 55, 18, NULL, '1.5L electric kettle', '2026-000143', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 157, 1, 'Hanabishi', 'EK-15', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:23', NULL),
(171, 55, 18, NULL, '1.5L electric kettle', '2026-000144', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 157, 1, 'Hanabishi', 'EK-15', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:24', NULL),
(172, 55, 18, NULL, '1.5L electric kettle', '2026-000145', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 157, 1, 'Hanabishi', 'EK-15', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:24', NULL),
(173, 55, 18, NULL, '1.5L electric kettle', '2026-000146', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 157, 1, 'Hanabishi', 'EK-15', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:24', NULL),
(174, 55, 18, NULL, '1.5L electric kettle', '2026-000147', NULL, 1, 0, 2, 2, 560.00, 560.00, NULL, 157, 1, 'Hanabishi', 'EK-15', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:24', NULL),
(175, 55, 20, NULL, 'Portable wireless speaker', '2026-000148', NULL, 1, 0, 2, 2, 4200.00, 4200.00, NULL, 161, 1, 'JBL', 'PS-BOOST', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:24', NULL),
(176, 55, 20, NULL, 'Portable wireless speaker', '2026-000149', NULL, 1, 0, 2, 2, 4200.00, 4200.00, NULL, 161, 1, 'JBL', 'PS-BOOST', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:24', NULL),
(177, 55, 20, NULL, 'Portable wireless speaker', '2026-000150', NULL, 1, 0, 2, 2, 4200.00, 4200.00, NULL, 161, 1, 'JBL', 'PS-BOOST', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:24', NULL),
(178, 55, 20, NULL, 'Portable wireless speaker', '2026-000151', NULL, 1, 0, 2, 2, 4200.00, 4200.00, NULL, 161, 1, 'JBL', 'PS-BOOST', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:24', NULL),
(179, 55, 22, NULL, '6-outlet surge protector', '2026-000152', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 163, 1, 'Panther', 'SP-06', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:24', NULL),
(180, 55, 22, NULL, '6-outlet surge protector', '2026-000153', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 163, 1, 'Panther', 'SP-06', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:24', NULL),
(181, 55, 22, NULL, '6-outlet surge protector', '2026-000154', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 163, 1, 'Panther', 'SP-06', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:24', NULL),
(182, 55, 22, NULL, '6-outlet surge protector', '2026-000155', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 163, 1, 'Panther', 'SP-06', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:24', NULL),
(183, 55, 22, NULL, '6-outlet surge protector', '2026-000156', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 163, 1, 'Panther', 'SP-06', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:24', NULL),
(184, 55, 22, NULL, '6-outlet surge protector', '2026-000157', NULL, 1, 0, 2, 2, 420.00, 420.00, NULL, 163, 1, 'Panther', 'SP-06', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-29 12:47:24', NULL),
(185, 56, 6, NULL, 'Laminate Office Desk', '2026-000158', NULL, 1, 1, 1, 2, 8500.00, 8500.00, NULL, 147, 1, 'Prime Office', 'OD-PRM-140', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(186, 56, 6, NULL, 'Laminate Office Desk', '2026-000159', NULL, 1, 1, 1, 2, 8500.00, 8500.00, NULL, 147, 1, 'Prime Office', 'OD-PRM-140', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(187, 56, 6, NULL, 'Laminate Office Desk', '2026-000160', NULL, 1, 1, 1, 2, 8500.00, 8500.00, NULL, 147, 1, 'Prime Office', 'OD-PRM-140', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(188, 56, 6, NULL, 'Laminate Office Desk', '2026-000161', NULL, 1, 1, 1, 2, 8500.00, 8500.00, NULL, 147, 1, 'Prime Office', 'OD-PRM-140', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(189, 56, 2, NULL, 'Panasonic KX-TS880', '2026-000162', 'TEL-2026-007', 1, 1, 1, 2, 2350.00, 2350.00, NULL, 154, 1, 'Panasonic', 'KX-TS880', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(190, 56, 7, NULL, 'Dell EMC PowerEdge R250', '2026-000163', 'SRV-2026-006', 1, 1, 1, 2, 165000.00, 165000.00, NULL, 155, 1, 'Dell EMC', 'PowerEdge-R250', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(191, 56, 21, NULL, 'Portable action camera', '2026-000164', NULL, 1, 1, 1, 2, 1200.00, 1200.00, NULL, 156, 1, 'GoPro', 'AC-11', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(192, 56, 21, NULL, 'Portable action camera', '2026-000165', NULL, 1, 1, 1, 2, 1200.00, 1200.00, NULL, 156, 1, 'GoPro', 'AC-11', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(193, 56, 21, NULL, 'Portable action camera', '2026-000166', NULL, 1, 1, 1, 2, 1200.00, 1200.00, NULL, 156, 1, 'GoPro', 'AC-11', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(194, 56, 21, NULL, 'Portable action camera', '2026-000167', NULL, 1, 1, 1, 2, 1200.00, 1200.00, NULL, 156, 1, 'GoPro', 'AC-11', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(195, 56, 21, NULL, 'Portable action camera', '2026-000168', NULL, 1, 1, 1, 2, 1200.00, 1200.00, NULL, 156, 1, 'GoPro', 'AC-11', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(196, 56, 27, NULL, 'Handheld label maker', '2026-000169', NULL, 1, 1, 1, 2, 450.00, 450.00, NULL, 159, 1, 'Brother', 'PT-D210', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(197, 56, 27, NULL, 'Handheld label maker', '2026-000170', NULL, 1, 1, 1, 2, 450.00, 450.00, NULL, 159, 1, 'Brother', 'PT-D210', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(198, 56, 27, NULL, 'Handheld label maker', '2026-000171', NULL, 1, 1, 1, 2, 450.00, 450.00, NULL, 159, 1, 'Brother', 'PT-D210', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(199, 56, 27, NULL, 'Handheld label maker', '2026-000172', NULL, 1, 1, 1, 2, 450.00, 450.00, NULL, 159, 1, 'Brother', 'PT-D210', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(200, 56, 27, NULL, 'Handheld label maker', '2026-000173', NULL, 1, 1, 1, 2, 450.00, 450.00, NULL, 159, 1, 'Brother', 'PT-D210', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(201, 56, 27, NULL, 'Handheld label maker', '2026-000174', NULL, 1, 1, 1, 2, 450.00, 450.00, NULL, 159, 1, 'Brother', 'PT-D210', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(202, 56, 27, NULL, 'Handheld label maker', '2026-000175', NULL, 1, 1, 1, 2, 450.00, 450.00, NULL, 159, 1, 'Brother', 'PT-D210', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(203, 56, 27, NULL, 'Handheld label maker', '2026-000176', NULL, 1, 1, 1, 2, 450.00, 450.00, NULL, 159, 1, 'Brother', 'PT-D210', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(204, 56, 24, NULL, 'Digital multimeter', '2026-000177', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 160, 1, 'Uni-T', 'UT-33D', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(205, 56, 24, NULL, 'Digital multimeter', '2026-000178', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 160, 1, 'Uni-T', 'UT-33D', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(206, 56, 24, NULL, 'Digital multimeter', '2026-000179', NULL, 1, 1, 1, 2, 650.00, 650.00, NULL, 160, 1, 'Uni-T', 'UT-33D', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', NULL, NULL),
(207, 57, 6, NULL, 'Compact Workstation Desk', '2026-000180', NULL, 1, 0, 2, 2, 8400.00, 8400.00, NULL, 148, 1, 'Prime Office', 'OD-PRM-120', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(208, 57, 6, NULL, 'Compact Workstation Desk', '2026-000181', NULL, 1, 0, 2, 2, 8400.00, 8400.00, NULL, 148, 1, 'Prime Office', 'OD-PRM-120', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(209, 57, 6, NULL, 'Compact Workstation Desk', '2026-000182', NULL, 1, 0, 2, 2, 8400.00, 8400.00, NULL, 148, 1, 'Prime Office', 'OD-PRM-120', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(210, 57, 6, NULL, 'Task Chair', '2026-000183', NULL, 1, 0, 2, 2, 3150.00, 3150.00, NULL, 150, 1, 'ErgoSeat', 'CH-ERG-24', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(211, 57, 6, NULL, 'Task Chair', '2026-000184', NULL, 1, 0, 2, 2, 3150.00, 3150.00, NULL, 150, 1, 'ErgoSeat', 'CH-ERG-24', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(212, 57, 6, NULL, 'Task Chair', '2026-000185', NULL, 1, 0, 2, 2, 3150.00, 3150.00, NULL, 150, 1, 'ErgoSeat', 'CH-ERG-24', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(213, 57, 6, NULL, 'Task Chair', '2026-000186', NULL, 1, 0, 2, 2, 3150.00, 3150.00, NULL, 150, 1, 'ErgoSeat', 'CH-ERG-24', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(214, 57, 6, NULL, 'Task Chair', '2026-000187', NULL, 1, 0, 2, 2, 3150.00, 3150.00, NULL, 150, 1, 'ErgoSeat', 'CH-ERG-24', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(215, 57, 6, NULL, 'Task Chair', '2026-000188', NULL, 1, 0, 2, 2, 3150.00, 3150.00, NULL, 150, 1, 'ErgoSeat', 'CH-ERG-24', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(216, 57, 8, NULL, '1.5HP Wall Mounted AC', '2026-000189', 'CAR-2026-008', 1, 0, 2, 2, 33800.00, 33800.00, NULL, 152, 1, 'Carrier', 'Aura-1.5HP', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(217, 57, 25, NULL, 'Portable fire extinguisher', '2026-000190', NULL, 1, 0, 2, 2, 220.00, 220.00, NULL, 158, 1, 'Elide', 'FE-5LB', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(218, 57, 25, NULL, 'Portable fire extinguisher', '2026-000191', NULL, 1, 0, 2, 2, 220.00, 220.00, NULL, 158, 1, 'Elide', 'FE-5LB', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(219, 57, 25, NULL, 'Portable fire extinguisher', '2026-000192', NULL, 1, 0, 2, 2, 220.00, 220.00, NULL, 158, 1, 'Elide', 'FE-5LB', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(220, 57, 25, NULL, 'Portable fire extinguisher', '2026-000193', NULL, 1, 0, 2, 2, 220.00, 220.00, NULL, 158, 1, 'Elide', 'FE-5LB', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(221, 57, 25, NULL, 'Portable fire extinguisher', '2026-000194', NULL, 1, 0, 2, 2, 220.00, 220.00, NULL, 158, 1, 'Elide', 'FE-5LB', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(222, 57, 25, NULL, 'Portable fire extinguisher', '2026-000195', NULL, 1, 0, 2, 2, 220.00, 220.00, NULL, 158, 1, 'Elide', 'FE-5LB', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(223, 57, 25, NULL, 'Portable fire extinguisher', '2026-000196', NULL, 1, 0, 2, 2, 220.00, 220.00, NULL, 158, 1, 'Elide', 'FE-5LB', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(224, 57, 25, NULL, 'Portable fire extinguisher', '2026-000197', NULL, 1, 0, 2, 2, 220.00, 220.00, NULL, 158, 1, 'Elide', 'FE-5LB', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(225, 57, 25, NULL, 'Portable fire extinguisher', '2026-000198', NULL, 1, 0, 2, 2, 220.00, 220.00, NULL, 158, 1, 'Elide', 'FE-5LB', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(226, 57, 25, NULL, 'Portable fire extinguisher', '2026-000199', NULL, 1, 0, 2, 2, 220.00, 220.00, NULL, 158, 1, 'Elide', 'FE-5LB', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(227, 57, 25, NULL, 'Portable fire extinguisher', '2026-000200', NULL, 1, 0, 2, 2, 220.00, 220.00, NULL, 158, 1, 'Elide', 'FE-5LB', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(228, 57, 25, NULL, 'Portable fire extinguisher', '2026-000201', NULL, 1, 0, 2, 2, 220.00, 220.00, NULL, 158, 1, 'Elide', 'FE-5LB', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(229, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000202', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(230, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000203', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(231, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000204', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(232, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000205', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(233, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000206', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(234, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000207', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(235, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000208', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:44', NULL),
(236, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000209', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(237, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000210', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(238, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000211', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(239, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000212', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(240, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000213', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(241, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000214', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(242, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000215', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(243, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000216', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(244, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000217', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(245, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000218', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(246, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000219', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(247, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000220', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(248, 57, 29, NULL, 'Janitorial cleaning kit', '2026-000221', NULL, 1, 0, 2, 3, 180.00, 180.00, NULL, 165, 1, 'Generic', 'CK-2026', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(249, 57, 28, NULL, 'RJ45 LAN cable bundle', '2026-000222', NULL, 1, 0, 2, 3, 980.00, 980.00, NULL, 167, 1, 'Generic', 'LAN-SET-01', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(250, 57, 28, NULL, 'RJ45 LAN cable bundle', '2026-000223', NULL, 1, 0, 2, 3, 980.00, 980.00, NULL, 167, 1, 'Generic', 'LAN-SET-01', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(251, 57, 28, NULL, 'RJ45 LAN cable bundle', '2026-000224', NULL, 1, 0, 2, 3, 980.00, 980.00, NULL, 167, 1, 'Generic', 'LAN-SET-01', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(252, 57, 28, NULL, 'RJ45 LAN cable bundle', '2026-000225', NULL, 1, 0, 2, 3, 980.00, 980.00, NULL, 167, 1, 'Generic', 'LAN-SET-01', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL),
(253, 57, 28, NULL, 'RJ45 LAN cable bundle', '2026-000226', NULL, 1, 0, 2, 3, 980.00, 980.00, NULL, 167, 1, 'Generic', 'LAN-SET-01', 'Recorded from approved delivery and inspection documentation', '2026-04-27 23:26:20', '2026-04-28 00:39:45', NULL);

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
(7, NULL, 41, 1, NULL, NULL, 4, 'Cyron Micarte', 'PAR', 'PAR-2026-0002', 'acknowledged', '2026-03-18', 'Auto-created from DR acknowledgment', NULL, '2026-03-18 19:44:40', 1, NULL, NULL, '2026-03-18 19:44:40'),
(8, NULL, 29, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0003', 'acknowledged', '2026-03-22', 'Auto-created from DR acknowledgment', NULL, '2026-03-22 22:53:00', 1, NULL, NULL, '2026-03-22 22:53:00'),
(9, NULL, 42, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0004', 'acknowledged', '2026-03-22', 'Auto-created from DR acknowledgment', NULL, '2026-03-22 22:55:54', 1, NULL, NULL, '2026-03-22 22:55:54'),
(10, NULL, 43, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0005', 'acknowledged', '2026-03-22', 'Auto-created from DR acknowledgment', NULL, '2026-03-22 23:17:39', 1, NULL, NULL, '2026-03-22 23:17:39'),
(11, NULL, 44, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0006', 'acknowledged', '2026-03-22', 'Auto-created from DR acknowledgment', NULL, '2026-03-22 23:23:28', 1, NULL, NULL, '2026-03-22 23:23:28'),
(12, NULL, 45, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0007', 'acknowledged', '2026-03-24', 'Auto-created from DR inspection', NULL, '2026-03-24 11:49:38', 1, NULL, NULL, '2026-03-24 11:49:14'),
(13, NULL, 46, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0008', 'acknowledged', '2026-03-24', 'Auto-created from DR acknowledgment', NULL, '2026-03-24 11:51:19', 1, NULL, NULL, '2026-03-24 11:51:19'),
(14, NULL, 32, 1, NULL, NULL, 4, 'Cyron Micarte', 'PAR', 'PAR-2026-0009', 'acknowledged', '2026-03-24', 'Auto-created from DR acknowledgment', NULL, '2026-03-24 11:55:01', 1, NULL, NULL, '2026-03-24 11:55:01'),
(15, NULL, 47, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0010', 'acknowledged', '2026-03-29', 'Auto-created from DR inspection', NULL, '2026-03-29 20:56:46', 1, NULL, NULL, '2026-03-29 20:56:22'),
(16, NULL, 48, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0011', 'acknowledged', '2026-03-29', 'Auto-created from DR acknowledgment', NULL, '2026-03-29 20:58:25', 1, NULL, NULL, '2026-03-29 20:58:25'),
(17, NULL, 49, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0012', 'acknowledged', '2026-03-29', 'Auto-created from DR inspection', NULL, '2026-03-29 21:19:44', 1, NULL, NULL, '2026-03-29 21:19:21'),
(18, NULL, 86, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0013', 'acknowledged', '2026-04-20', 'Auto-created from DR inspection', NULL, '2026-04-20 12:09:16', 1, NULL, NULL, '2026-04-20 12:08:50'),
(19, NULL, 87, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0014', 'acknowledged', '2026-04-20', 'Auto-created from DR inspection', NULL, '2026-04-20 13:20:37', 1, NULL, NULL, '2026-04-20 13:20:14'),
(20, 1, 88, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0015', 'acknowledged', '2026-04-08', 'Auto-created from DR inspection', NULL, '2026-04-29 10:22:06', 1, NULL, NULL, '2026-04-27 23:35:54'),
(21, 1, 88, 1, NULL, NULL, 2, 'Chokoy Covid', 'ICS', 'ICS-2026-0001', 'acknowledged', '2026-04-08', 'Auto-created from DR inspection', NULL, '2026-04-29 10:22:06', 1, NULL, NULL, '2026-04-27 23:35:54'),
(22, NULL, 91, 1, NULL, NULL, 4, 'Cyron Micarte', 'PAR', 'PAR-2026-0016', 'acknowledged', '2026-04-20', 'Auto-created from DR inspection', NULL, '2026-04-28 00:39:44', 1, NULL, NULL, '2026-04-27 23:42:36'),
(23, NULL, 91, 1, NULL, NULL, 4, 'Cyron Micarte', 'ICS', 'ICS-2026-0002', 'acknowledged', '2026-04-20', 'Auto-created from DR inspection', NULL, '2026-04-28 00:39:44', 1, NULL, NULL, '2026-04-27 23:42:36'),
(24, 2, 90, 1, NULL, NULL, 2, 'Chokoy Covid', 'PAR', 'PAR-2026-0017', 'acknowledged', '2026-04-29', 'Auto-created from DR acknowledgment', NULL, '2026-04-29 12:47:23', 1, NULL, NULL, '2026-04-29 12:47:23'),
(25, 2, 90, 1, NULL, NULL, 2, 'Chokoy Covid', 'ICS', 'ICS-2026-0003', 'acknowledged', '2026-04-29', 'Auto-created from DR acknowledgment', NULL, '2026-04-29 12:47:23', 1, NULL, NULL, '2026-04-29 12:47:23'),
(26, NULL, 89, 1, NULL, NULL, 4, 'Cyron Micarte', 'PAR', 'PAR-2026-0018', 'pending_acknowledgment', '2026-04-16', 'Auto-created from DR inspection', NULL, NULL, NULL, NULL, NULL, '2026-04-29 14:20:50'),
(27, NULL, 89, 1, NULL, NULL, 4, 'Cyron Micarte', 'ICS', 'ICS-2026-0004', 'pending_acknowledgment', '2026-04-16', 'Auto-created from DR inspection', NULL, NULL, NULL, NULL, NULL, '2026-04-29 14:20:50');

-- --------------------------------------------------------

--
-- Table structure for table `propertyissuancedetail`
--

CREATE TABLE `propertyissuancedetail` (
  `IssuanceDetailID` int(11) NOT NULL,
  `IssuanceID` int(11) NOT NULL,
  `RequestDetailID` bigint(20) UNSIGNED DEFAULT NULL,
  `ris_item_id` bigint(20) UNSIGNED DEFAULT NULL,
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

INSERT INTO `propertyissuancedetail` (`IssuanceDetailID`, `IssuanceID`, `RequestDetailID`, `ris_item_id`, `ItemDescription`, `Unit`, `InventoryDetailID`, `QuantityIssued`, `Remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(5, 5, NULL, NULL, 'smsmsmsm', 'unit', 5, 1, 'Auto-created from DR inspection', '2026-03-18 10:11:05', '2026-03-18 10:11:05', NULL),
(7, 7, NULL, NULL, 'bugnaw', 'unit', 6, 1, 'Auto-created from DR acknowledgment', '2026-03-18 11:44:40', '2026-03-18 11:44:40', NULL),
(8, 8, NULL, NULL, 'Office Desk', 'unit', 7, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(9, 8, NULL, NULL, 'Office Desk', 'unit', 31, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(10, 8, NULL, NULL, 'Office Desk', 'unit', 49, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(11, 8, NULL, NULL, 'Office Desk', 'unit', 64, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(12, 8, NULL, NULL, 'Office Chair', 'unit', 8, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(13, 8, NULL, NULL, 'Office Chair', 'unit', 32, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(14, 8, NULL, NULL, 'Office Chair', 'unit', 50, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(15, 8, NULL, NULL, 'Office Chair', 'unit', 65, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(16, 8, NULL, NULL, 'Office Chair', 'unit', 76, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(17, 8, NULL, NULL, 'Office Chair', 'unit', 84, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(18, 8, NULL, NULL, 'Office Chair', 'unit', 91, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(19, 8, NULL, NULL, 'Office Chair', 'unit', 96, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(20, 8, NULL, NULL, 'Filing Cabinet', 'unit', 9, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(21, 8, NULL, NULL, 'Filing Cabinet', 'unit', 33, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(22, 8, NULL, NULL, 'Desktop Computer', 'unit', 10, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(23, 8, NULL, NULL, 'Desktop Computer', 'unit', 34, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(24, 8, NULL, NULL, 'Desktop Computer', 'unit', 51, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(25, 8, NULL, NULL, 'Desktop Computer', 'unit', 66, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(26, 8, NULL, NULL, 'Desktop Computer', 'unit', 77, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(27, 8, NULL, NULL, 'Desktop Computer', 'unit', 85, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(28, 8, NULL, NULL, 'Printer/Scanner', 'unit', 11, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(29, 8, NULL, NULL, 'Printer/Scanner', 'unit', 35, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(30, 8, NULL, NULL, 'Telephone Set', 'unit', 12, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:53:00', '2026-03-22 14:53:00', NULL),
(31, 9, NULL, NULL, 'Calculator', 'unit', 134, 1, 'Auto-created from DR acknowledgment', '2026-03-22 14:55:54', '2026-03-22 14:55:54', NULL),
(32, 10, NULL, NULL, 'Calculator', 'unit', 135, 1, 'Auto-created from DR acknowledgment', '2026-03-22 15:17:39', '2026-03-22 15:17:39', NULL),
(33, 11, NULL, NULL, 'Calculator', 'unit', 136, 1, 'Auto-created from DR acknowledgment', '2026-03-22 15:23:28', '2026-03-22 15:23:28', NULL),
(34, 12, NULL, NULL, 'Laptop Computer', 'unit', 137, 1, 'Auto-created from DR inspection', '2026-03-24 03:49:14', '2026-03-24 03:49:14', NULL),
(35, 13, NULL, NULL, 'Laptop Computer', 'unit', 138, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:51:19', '2026-03-24 03:51:19', NULL),
(36, 14, NULL, NULL, 'Laptop Computer', 'unit', 25, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(37, 14, NULL, NULL, 'Office Lamp', 'unit', 26, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(38, 14, NULL, NULL, 'Office Lamp', 'unit', 45, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(39, 14, NULL, NULL, 'Office Lamp', 'unit', 60, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(40, 14, NULL, NULL, 'Office Lamp', 'unit', 73, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(41, 14, NULL, NULL, 'Office Lamp', 'unit', 81, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(42, 14, NULL, NULL, 'Office Lamp', 'unit', 88, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(43, 14, NULL, NULL, 'Office Lamp', 'unit', 93, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(44, 14, NULL, NULL, 'Office Lamp', 'unit', 98, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(45, 14, NULL, NULL, 'Office Lamp', 'unit', 102, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(46, 14, NULL, NULL, 'Office Lamp', 'unit', 106, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(47, 14, NULL, NULL, 'Office Lamp', 'unit', 109, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(48, 14, NULL, NULL, 'Office Lamp', 'unit', 110, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(49, 14, NULL, NULL, 'Extension Cord', 'unit', 27, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(50, 14, NULL, NULL, 'Extension Cord', 'unit', 46, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(51, 14, NULL, NULL, 'Extension Cord', 'unit', 61, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(52, 14, NULL, NULL, 'Extension Cord', 'unit', 74, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(53, 14, NULL, NULL, 'Extension Cord', 'unit', 82, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(54, 14, NULL, NULL, 'Extension Cord', 'unit', 89, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(55, 14, NULL, NULL, 'Extension Cord', 'unit', 94, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(56, 14, NULL, NULL, 'Extension Cord', 'unit', 99, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(57, 14, NULL, NULL, 'Extension Cord', 'unit', 103, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(58, 14, NULL, NULL, 'Extension Cord', 'unit', 107, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(59, 14, NULL, NULL, 'Hand Tools Set', 'set', 28, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(60, 14, NULL, NULL, 'Hand Tools Set', 'set', 47, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(61, 14, NULL, NULL, 'Hand Tools Set', 'set', 62, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(62, 14, NULL, NULL, 'Waste Basket', 'unit', 29, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(63, 14, NULL, NULL, 'Waste Basket', 'unit', 48, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(64, 14, NULL, NULL, 'Waste Basket', 'unit', 63, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(65, 14, NULL, NULL, 'Waste Basket', 'unit', 75, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(66, 14, NULL, NULL, 'Waste Basket', 'unit', 83, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(67, 14, NULL, NULL, 'Waste Basket', 'unit', 90, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(68, 14, NULL, NULL, 'Waste Basket', 'unit', 95, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(69, 14, NULL, NULL, 'Waste Basket', 'unit', 100, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(70, 14, NULL, NULL, 'Waste Basket', 'unit', 104, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(71, 14, NULL, NULL, 'Waste Basket', 'unit', 108, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(72, 14, NULL, NULL, 'Projector', 'unit', 30, 1, 'Auto-created from DR acknowledgment', '2026-03-24 03:55:01', '2026-03-24 03:55:01', NULL),
(73, 15, NULL, NULL, 'Desktop Computer', 'unit', 139, 1, 'Auto-created from DR inspection', '2026-03-29 12:56:22', '2026-03-29 12:56:22', NULL),
(74, 15, NULL, NULL, 'Office Chair', 'unit', 140, 1, 'Auto-created from DR inspection', '2026-03-29 12:56:22', '2026-03-29 12:56:22', NULL),
(75, 16, NULL, NULL, 'Office Chair', 'unit', 141, 1, 'Auto-created from DR acknowledgment', '2026-03-29 12:58:25', '2026-03-29 12:58:25', NULL),
(76, 16, NULL, NULL, 'Office Chair', 'unit', 142, 1, 'Auto-created from DR acknowledgment', '2026-03-29 12:58:25', '2026-03-29 12:58:25', NULL),
(77, 17, NULL, NULL, 'Office Chair', 'unit', 143, 1, 'Auto-created from DR inspection', '2026-03-29 13:19:21', '2026-03-29 13:19:21', NULL),
(78, 17, NULL, NULL, 'Printer/Scanner', 'unit', 144, 1, 'Auto-created from DR inspection', '2026-03-29 13:19:21', '2026-03-29 13:19:21', NULL),
(79, 18, NULL, NULL, 'Air Conditioner', 'unit', 145, 1, 'Auto-created from DR inspection', '2026-04-20 04:08:50', '2026-04-20 04:08:50', NULL),
(80, 19, NULL, NULL, 'Calculator', 'unit', 146, 1, 'Auto-created from DR inspection', '2026-04-20 05:20:14', '2026-04-20 05:20:14', NULL),
(81, 19, NULL, NULL, 'Air Conditioner', 'unit', 147, 1, 'Auto-created from DR inspection', '2026-04-20 05:20:14', '2026-04-20 05:20:14', NULL),
(82, 20, 1, 1, 'Desktop Computer', 'unit', 148, 1, 'Auto-created from DR inspection', '2026-04-27 15:35:54', '2026-04-29 02:22:06', NULL),
(83, 20, 2, 2, 'Printer/Scanner', 'unit', 149, 1, 'Auto-created from DR inspection', '2026-04-27 15:35:54', '2026-04-29 02:22:06', NULL),
(84, 20, 3, 3, 'Office Chair', 'unit', 150, 1, 'Auto-created from DR inspection', '2026-04-27 15:35:54', '2026-04-29 02:22:06', NULL),
(85, 21, 4, 4, 'Portable rechargeable emergency lamp', 'unit', 151, 1, 'Auto-created from DR inspection', '2026-04-27 15:35:54', '2026-04-29 02:22:06', NULL),
(86, 21, 5, 5, 'Portable rechargeable emergency lamp', 'unit', 152, 1, 'Auto-created from DR inspection', '2026-04-27 15:35:54', '2026-04-29 02:22:06', NULL),
(87, 21, 6, 6, 'Portable rechargeable emergency lamp', 'unit', 153, 1, 'Auto-created from DR inspection', '2026-04-27 15:35:54', '2026-04-29 02:22:06', NULL),
(88, 21, 7, 7, '32GB USB 3.0 flash drive', 'unit', 154, 1, 'Auto-created from DR inspection', '2026-04-27 15:35:54', '2026-04-29 02:22:06', NULL),
(89, 21, 8, 8, '32GB USB 3.0 flash drive', 'unit', 155, 1, 'Auto-created from DR inspection', '2026-04-27 15:35:54', '2026-04-29 02:22:06', NULL),
(90, 21, 9, 9, 'Wireless keyboard and mouse set', 'set', 156, 1, 'Auto-created from DR inspection', '2026-04-27 15:35:54', '2026-04-29 02:22:06', NULL),
(91, 21, 10, 10, 'Wireless keyboard and mouse set', 'set', 157, 1, 'Auto-created from DR inspection', '2026-04-27 15:35:54', '2026-04-29 02:22:06', NULL),
(92, 21, 11, 11, 'Wireless keyboard and mouse set', 'set', 158, 1, 'Auto-created from DR inspection', '2026-04-27 15:35:54', '2026-04-29 02:22:06', NULL),
(93, 21, 12, 12, 'Wireless keyboard and mouse set', 'set', 159, 1, 'Auto-created from DR inspection', '2026-04-27 15:35:54', '2026-04-29 02:22:06', NULL),
(94, 21, 13, 13, 'Wireless keyboard and mouse set', 'set', 160, 1, 'Auto-created from DR inspection', '2026-04-27 15:35:54', '2026-04-29 02:22:06', NULL),
(95, 22, NULL, NULL, 'Office Desk', 'unit', 207, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(96, 22, NULL, NULL, 'Office Desk', 'unit', 208, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(97, 22, NULL, NULL, 'Office Desk', 'unit', 209, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(98, 22, NULL, NULL, 'Office Chair', 'unit', 210, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(99, 22, NULL, NULL, 'Office Chair', 'unit', 211, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(100, 22, NULL, NULL, 'Office Chair', 'unit', 212, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(101, 22, NULL, NULL, 'Office Chair', 'unit', 213, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(102, 22, NULL, NULL, 'Office Chair', 'unit', 214, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(103, 22, NULL, NULL, 'Office Chair', 'unit', 215, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(104, 22, NULL, NULL, 'Air Conditioner', 'unit', 216, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(105, 23, NULL, NULL, 'Portable fire extinguisher', 'unit', 217, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(106, 23, NULL, NULL, 'Portable fire extinguisher', 'unit', 218, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(107, 23, NULL, NULL, 'Portable fire extinguisher', 'unit', 219, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(108, 23, NULL, NULL, 'Portable fire extinguisher', 'unit', 220, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(109, 23, NULL, NULL, 'Portable fire extinguisher', 'unit', 221, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(110, 23, NULL, NULL, 'Portable fire extinguisher', 'unit', 222, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(111, 23, NULL, NULL, 'Portable fire extinguisher', 'unit', 223, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(112, 23, NULL, NULL, 'Portable fire extinguisher', 'unit', 224, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(113, 23, NULL, NULL, 'Portable fire extinguisher', 'unit', 225, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(114, 23, NULL, NULL, 'Portable fire extinguisher', 'unit', 226, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(115, 23, NULL, NULL, 'Portable fire extinguisher', 'unit', 227, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(116, 23, NULL, NULL, 'Portable fire extinguisher', 'unit', 228, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(117, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 229, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(118, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 230, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(119, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 231, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(120, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 232, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(121, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 233, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(122, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 234, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(123, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 235, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(124, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 236, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(125, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 237, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(126, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 238, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(127, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 239, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(128, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 240, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(129, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 241, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(130, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 242, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(131, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 243, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(132, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 244, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(133, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 245, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(134, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 246, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(135, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 247, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(136, 23, NULL, NULL, 'Janitorial cleaning kit', 'set', 248, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(137, 23, NULL, NULL, 'RJ45 LAN cable bundle', 'set', 249, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(138, 23, NULL, NULL, 'RJ45 LAN cable bundle', 'set', 250, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(139, 23, NULL, NULL, 'RJ45 LAN cable bundle', 'set', 251, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(140, 23, NULL, NULL, 'RJ45 LAN cable bundle', 'set', 252, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(141, 23, NULL, NULL, 'RJ45 LAN cable bundle', 'set', 253, 1, 'Auto-created from DR inspection', '2026-04-27 15:42:36', '2026-04-27 15:42:36', NULL),
(142, 24, 14, 14, 'Laptop Computer', 'unit', 161, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:23', '2026-04-29 04:47:24', NULL),
(143, 24, 15, 15, 'Filing Cabinet', 'unit', 162, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:23', '2026-04-29 04:47:24', NULL),
(144, 24, 16, 16, 'Filing Cabinet', 'unit', 163, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:23', '2026-04-29 04:47:24', NULL),
(145, 24, 17, 17, 'Projector', 'unit', 164, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:23', '2026-04-29 04:47:24', NULL),
(146, 25, 18, 18, '1.5L electric kettle', 'unit', 165, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:23', '2026-04-29 04:47:24', NULL),
(147, 25, 19, 19, '1.5L electric kettle', 'unit', 166, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:23', '2026-04-29 04:47:24', NULL),
(148, 25, 20, 20, '1.5L electric kettle', 'unit', 167, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:23', '2026-04-29 04:47:24', NULL),
(149, 25, 21, 21, '1.5L electric kettle', 'unit', 168, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:23', '2026-04-29 04:47:24', NULL),
(150, 25, 22, 22, '1.5L electric kettle', 'unit', 169, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:23', '2026-04-29 04:47:24', NULL),
(151, 25, 23, 23, '1.5L electric kettle', 'unit', 170, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:23', '2026-04-29 04:47:24', NULL),
(152, 25, 24, 24, '1.5L electric kettle', 'unit', 171, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(153, 25, 25, 25, '1.5L electric kettle', 'unit', 172, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(154, 25, 26, 26, '1.5L electric kettle', 'unit', 173, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(155, 25, 27, 27, '1.5L electric kettle', 'unit', 174, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(156, 25, 28, 28, 'Portable wireless speaker', 'unit', 175, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(157, 25, 29, 29, 'Portable wireless speaker', 'unit', 176, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(158, 25, 30, 30, 'Portable wireless speaker', 'unit', 177, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(159, 25, 31, 31, 'Portable wireless speaker', 'unit', 178, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(160, 25, 32, 32, '6-outlet surge protector', 'unit', 179, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(161, 25, 33, 33, '6-outlet surge protector', 'unit', 180, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(162, 25, 34, 34, '6-outlet surge protector', 'unit', 181, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(163, 25, 35, 35, '6-outlet surge protector', 'unit', 182, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(164, 25, 36, 36, '6-outlet surge protector', 'unit', 183, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(165, 25, 37, 37, '6-outlet surge protector', 'unit', 184, 1, 'Auto-created from DR acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(166, 26, NULL, NULL, 'Office Desk', 'unit', 185, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(167, 26, NULL, NULL, 'Office Desk', 'unit', 186, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(168, 26, NULL, NULL, 'Office Desk', 'unit', 187, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(169, 26, NULL, NULL, 'Office Desk', 'unit', 188, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(170, 26, NULL, NULL, 'Telephone Set', 'unit', 189, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(171, 26, NULL, NULL, 'Server Equipment', 'unit', 190, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(172, 27, NULL, NULL, 'Portable action camera', 'unit', 191, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(173, 27, NULL, NULL, 'Portable action camera', 'unit', 192, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(174, 27, NULL, NULL, 'Portable action camera', 'unit', 193, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(175, 27, NULL, NULL, 'Portable action camera', 'unit', 194, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(176, 27, NULL, NULL, 'Portable action camera', 'unit', 195, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(177, 27, NULL, NULL, 'Handheld label maker', 'unit', 196, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(178, 27, NULL, NULL, 'Handheld label maker', 'unit', 197, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(179, 27, NULL, NULL, 'Handheld label maker', 'unit', 198, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(180, 27, NULL, NULL, 'Handheld label maker', 'unit', 199, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(181, 27, NULL, NULL, 'Handheld label maker', 'unit', 200, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(182, 27, NULL, NULL, 'Handheld label maker', 'unit', 201, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(183, 27, NULL, NULL, 'Handheld label maker', 'unit', 202, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(184, 27, NULL, NULL, 'Handheld label maker', 'unit', 203, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(185, 27, NULL, NULL, 'Digital multimeter', 'unit', 204, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(186, 27, NULL, NULL, 'Digital multimeter', 'unit', 205, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL),
(187, 27, NULL, NULL, 'Digital multimeter', 'unit', 206, 1, 'Auto-created from DR inspection', '2026-04-29 06:20:50', '2026-04-29 06:20:50', NULL);

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
(1, 'PBR-RET-2026-0001', 1, 1, '2026-03-18 13:25:11', 'done', '2026-03-18 13:25:11', '2026-03-18 13:25:11', NULL),
(2, 'PBR-RET-2026-0002', 3, 1, '2026-03-25 14:03:14', 'Returned in good condition.', '2026-03-25 14:03:14', '2026-03-25 14:03:14', NULL),
(3, 'PBR-RET-2026-0003', 4, 1, '2026-03-29 13:34:27', 'ty', '2026-03-29 13:34:27', '2026-03-29 13:34:27', NULL),
(4, 'PBR-RET-2026-0004', 5, 1, '2026-04-03 17:40:55', 'ohye', '2026-04-03 17:40:55', '2026-04-03 17:40:55', NULL),
(5, 'PBR-RET-2026-0005', 7, 1, '2026-04-23 13:09:24', 'goods.', '2026-04-23 13:09:24', '2026-04-23 13:09:24', NULL),
(6, 'PBR-RET-2026-0006', 8, 1, '2026-04-24 11:41:23', NULL, '2026-04-24 11:41:23', '2026-04-24 11:41:23', NULL);

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
(1, 1, 1, 5, 1, 1, NULL, NULL, '2026-03-18 13:25:11', '2026-03-18 13:25:11', NULL),
(2, 2, 3, 28, 1, 1, NULL, NULL, '2026-03-25 14:03:14', '2026-03-25 14:03:14', NULL),
(3, 3, 4, 73, 1, 1, NULL, NULL, '2026-03-29 13:34:27', '2026-03-29 13:34:27', NULL),
(4, 4, 5, 25, 1, 1, NULL, NULL, '2026-04-03 17:40:55', '2026-04-03 17:40:55', NULL),
(5, 5, 7, 26, 1, 1, NULL, NULL, '2026-04-23 13:09:24', '2026-04-23 13:09:24', NULL),
(6, 6, 9, 27, 1, 1, NULL, NULL, '2026-04-24 11:41:23', '2026-04-24 11:41:23', NULL);

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
(1, 'PTR-2026-0001', 3, 23, 30, 4, 2, NULL, 1, 'approved', 1, NULL, 6, '2026-03-18 12:55:49', 'completed', 1, '2026-03-18 12:56:48', 1, '2026-03-18 13:04:32', '2026-03-18 13:02:47', '2026-03-18 13:03:15', NULL, '2026-03-18 12:53:37', '2026-03-18 13:04:32', NULL),
(2, 'PTR-2026-0002', 3, 23, 30, 4, 2, 'jj', 1, 'approved', 1, NULL, 6, '2026-03-24 03:57:51', 'completed', 1, '2026-03-24 03:58:34', 3, '2026-03-24 04:00:25', '2026-03-24 03:58:56', '2026-03-24 04:00:25', NULL, '2026-03-24 03:56:24', '2026-03-24 04:00:25', NULL),
(3, 'PTR-2026-0003', 6, 30, 23, 2, 4, NULL, 1, 'approved', 1, NULL, 3, '2026-03-24 04:04:35', 'completed', 1, '2026-03-24 04:05:16', 6, '2026-03-24 04:06:22', '2026-03-24 04:05:25', '2026-03-24 04:06:22', NULL, '2026-03-24 04:03:42', '2026-03-24 04:06:22', NULL),
(4, 'PTR-2026-0004', 3, 23, 30, 4, 2, 'extension', 1, 'approved', 1, 'okay', 6, '2026-03-29 13:01:57', 'completed', 1, '2026-03-29 13:02:38', 1, '2026-04-20 03:53:09', '2026-03-29 13:03:00', '2026-04-20 03:53:09', NULL, '2026-03-29 13:00:41', '2026-04-20 03:53:09', NULL),
(5, 'PTR-2026-0005', 3, 23, 30, 4, 2, 'hulam', 1, 'approved', 1, 'okay', 6, '2026-03-29 13:24:20', 'completed', 1, '2026-03-29 13:24:53', 3, '2026-03-29 13:26:06', '2026-03-29 13:25:10', '2026-03-29 13:26:06', NULL, '2026-03-29 13:23:01', '2026-03-29 13:26:06', NULL),
(6, 'PTR-2026-0006', 3, 23, 30, 4, 2, NULL, 1, 'approved', 1, 'sge', 6, '2026-04-03 17:08:28', 'completed', 1, '2026-04-03 17:06:39', 1, '2026-04-03 17:09:22', '2026-04-03 17:09:22', '2026-04-03 17:09:22', NULL, '2026-04-03 17:05:47', '2026-04-03 17:09:22', NULL),
(7, 'PTR-2026-0007', 3, 23, 30, 4, 2, 'kk', 1, 'approved', 1, NULL, 6, '2026-04-20 05:26:28', 'completed', 1, '2026-04-20 05:25:24', 1, '2026-04-20 05:26:45', '2026-04-20 05:26:45', '2026-04-20 05:26:45', NULL, '2026-04-20 05:24:37', '2026-04-20 05:26:45', NULL),
(8, 'PTR-2026-0008', 3, 23, 30, 4, 2, 'balhin', 1, 'approved', 1, NULL, 1, '2026-04-23 08:01:51', 'completed', 1, '2026-04-23 08:01:18', 1, '2026-04-23 08:02:02', '2026-04-23 08:02:02', '2026-04-23 08:02:02', NULL, '2026-04-23 07:46:21', '2026-04-23 08:02:02', NULL),
(9, 'PTR-2026-0009', 6, 30, 23, 2, 4, 'basta', 1, 'pending', NULL, NULL, NULL, NULL, 'rejected', 1, '2026-04-23 13:04:20', NULL, NULL, NULL, NULL, 'basta', '2026-04-23 13:03:36', '2026-04-23 13:04:20', NULL),
(10, 'PTR-2026-0010', 6, 30, 23, 2, 4, 'hahahehe', 1, 'pending', NULL, NULL, NULL, NULL, 'completed', 1, '2026-04-23 13:05:06', 1, '2026-04-23 13:05:32', '2026-04-23 13:05:32', '2026-04-23 13:05:32', 'humana goods na', '2026-04-23 13:05:06', '2026-04-23 13:05:32', NULL),
(11, 'PTR-2026-0011', 6, 30, 23, 2, 4, NULL, 3, 'approved', 1, 'basta', 1, '2026-04-24 07:26:33', 'approved', 1, '2026-04-24 07:25:23', NULL, NULL, NULL, NULL, NULL, '2026-04-24 07:25:23', '2026-04-24 07:26:33', NULL),
(12, 'PTR-2026-0012', 3, 23, 30, 4, 2, NULL, 2, 'approved', 1, 'dili daw pwede', 1, '2026-04-24 08:34:49', 'completed', 1, '2026-04-24 08:20:04', 1, '2026-04-24 11:57:58', '2026-04-24 11:57:58', '2026-04-24 11:57:58', 'good!', '2026-04-24 08:20:04', '2026-04-24 11:57:58', NULL);

--
-- Triggers `propertytransfer`
--
DELIMITER $$
CREATE TRIGGER `trg_propertytransfer_requester_role_check` BEFORE INSERT ON `propertytransfer` FOR EACH ROW BEGIN
        DECLARE requester_department_id INT;

        SELECT u.DepartmentID
        INTO requester_department_id
        FROM `user` u
        WHERE u.UserID = NEW.RequestedByUserID
        LIMIT 1;

        IF requester_department_id IS NULL OR requester_department_id <> NEW.ToDepartmentID THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'PTR requester must belong to receiving department';
        END IF;
    END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_propertytransfer_requester_role_check_update` BEFORE UPDATE ON `propertytransfer` FOR EACH ROW BEGIN
        DECLARE requester_department_id INT;

        IF NEW.RequestedByUserID <> OLD.RequestedByUserID OR NEW.ToDepartmentID <> OLD.ToDepartmentID THEN
            SELECT u.DepartmentID
            INTO requester_department_id
            FROM `user` u
            WHERE u.UserID = NEW.RequestedByUserID
            LIMIT 1;

            IF requester_department_id IS NULL OR requester_department_id <> NEW.ToDepartmentID THEN
                SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'PTR requester must belong to receiving department';
            END IF;
        END IF;
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `propertytransferdetails`
--

CREATE TABLE `propertytransferdetails` (
  `TransferItemID` bigint(20) UNSIGNED NOT NULL,
  `TransferID` bigint(20) UNSIGNED NOT NULL,
  `InventoryDetailID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 1,
  `ApprovedQuantity` int(10) UNSIGNED DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `propertytransferdetails`
--

INSERT INTO `propertytransferdetails` (`TransferItemID`, `TransferID`, `InventoryDetailID`, `Quantity`, `ApprovedQuantity`, `Remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 6, 1, NULL, NULL, '2026-03-18 12:53:37', '2026-03-18 12:53:37', NULL),
(2, 2, 30, 1, NULL, NULL, '2026-03-24 03:56:24', '2026-03-24 03:56:24', NULL),
(3, 3, 30, 1, NULL, NULL, '2026-03-24 04:03:42', '2026-03-24 04:03:42', NULL),
(4, 4, 74, 1, NULL, NULL, '2026-03-29 13:00:41', '2026-03-29 13:00:41', NULL),
(5, 5, 28, 1, NULL, NULL, '2026-03-29 13:23:01', '2026-03-29 13:23:01', NULL),
(6, 6, 29, 1, NULL, NULL, '2026-04-03 17:05:47', '2026-04-03 17:05:47', NULL),
(7, 7, 48, 1, NULL, NULL, '2026-04-20 05:24:37', '2026-04-20 05:24:37', NULL),
(8, 8, 25, 1, NULL, NULL, '2026-04-23 07:46:21', '2026-04-23 07:46:21', NULL),
(9, 9, 147, 1, NULL, NULL, '2026-04-23 13:03:36', '2026-04-23 13:03:36', NULL),
(10, 10, 147, 1, NULL, NULL, '2026-04-23 13:05:06', '2026-04-23 13:05:06', NULL),
(11, 11, 146, 1, NULL, NULL, '2026-04-24 07:25:23', '2026-04-24 07:25:23', NULL),
(12, 11, 12, 1, NULL, 'Shortfall reason: basta', '2026-04-24 07:25:23', '2026-04-24 07:26:33', '2026-04-24 07:26:33'),
(13, 11, 29, 1, NULL, 'Shortfall reason: basta', '2026-04-24 07:25:23', '2026-04-24 07:26:33', '2026-04-24 07:26:33'),
(14, 12, 147, 1, NULL, NULL, '2026-04-24 08:20:04', '2026-04-24 08:20:04', NULL),
(15, 12, 63, 1, 0, 'Shortfall reason: dili daw pwede', '2026-04-24 08:20:04', '2026-04-24 08:34:49', NULL);

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
(15, 1, 'test - 1', 'test', '2026-02-14 12:56:17', '2026-04-03 23:52:51', '2026-04-03 15:52:51'),
(16, 1, 'Computer Peripherals', 'Mice, keyboards, webcams, headsets and similar accessories', '2026-04-27 17:57:26', '2026-04-27 17:57:26', NULL),
(17, 1, 'Data Storage Devices', 'USB drives, external drives, memory cards', '2026-04-27 17:57:26', '2026-04-27 17:57:26', NULL),
(18, 1, 'Small Office Appliances', 'Electric fans, kettles, coffee makers and similar items', '2026-04-27 17:57:26', '2026-04-27 17:57:26', NULL),
(19, 1, 'Presentation Devices', 'Portable projectors, presenter tools and accessories', '2026-04-27 17:57:26', '2026-04-27 17:57:26', NULL),
(20, 1, 'Audio Equipment (Portable)', 'Portable speakers, microphones and small audio devices', '2026-04-27 17:57:26', '2026-04-27 17:57:26', NULL),
(21, 1, 'Video Equipment (Portable)', 'Action cameras and small portable video devices', '2026-04-27 17:57:26', '2026-04-27 17:57:26', NULL),
(22, 1, 'Power Protection Devices', 'UPS, AVR and surge protectors', '2026-04-27 17:57:26', '2026-04-27 17:57:26', NULL),
(23, 1, 'Hand Tools and Instruments', 'Small repair and maintenance tools', '2026-04-27 17:57:26', '2026-04-27 17:57:26', NULL),
(24, 1, 'Portable Measuring Devices', 'Tape measures, multimeters and handheld meters', '2026-04-27 17:57:26', '2026-04-27 17:57:26', NULL),
(25, 1, 'Fire Safety Devices (Portable)', 'Portable extinguishers and safety response kits', '2026-04-27 17:57:26', '2026-04-27 17:57:26', NULL),
(26, 1, 'Portable Lighting Equipment', 'Emergency lamps, desk lamps and rechargeable lights', '2026-04-27 17:57:26', '2026-04-27 17:57:26', NULL),
(27, 1, 'Record Management Accessories', 'Binding machines, labelers and filing accessories', '2026-04-27 17:57:26', '2026-04-27 17:57:26', NULL),
(28, 1, 'IT Cables and Adapters', 'HDMI, LAN and power adapters for operational use', '2026-04-27 17:57:26', '2026-04-27 17:57:26', NULL),
(29, 1, 'Maintenance and Janitorial Tools', 'Reusable janitorial tools and maintenance kits', '2026-04-27 17:57:26', '2026-04-27 17:57:26', NULL),
(31, 1, 'Books', 'Reference and instructional materials used for learning and office use.', '2026-04-27 18:00:37', '2026-04-27 23:12:45', '2026-04-27 15:12:45');

-- --------------------------------------------------------

--
-- Table structure for table `property_card_entries`
--

CREATE TABLE `property_card_entries` (
  `EntryID` bigint(20) UNSIGNED NOT NULL,
  `InventoryDetailID` int(11) NOT NULL,
  `EntryDate` datetime NOT NULL,
  `EntryType` varchar(40) NOT NULL,
  `ReceiptQty` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `IssueQty` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ReferenceNo` varchar(100) DEFAULT NULL,
  `OfficeOfficer` varchar(255) DEFAULT NULL,
  `UnitCost` decimal(15,2) DEFAULT NULL,
  `Amount` decimal(15,2) DEFAULT NULL,
  `Remarks` text DEFAULT NULL,
  `SourceType` varchar(40) NOT NULL,
  `SourceID` bigint(20) UNSIGNED DEFAULT NULL,
  `SourceKey` varchar(100) NOT NULL,
  `CreatedBy` bigint(20) UNSIGNED DEFAULT NULL,
  `CreatedAt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_card_entries`
--

INSERT INTO `property_card_entries` (`EntryID`, `InventoryDetailID`, `EntryDate`, `EntryType`, `ReceiptQty`, `IssueQty`, `ReferenceNo`, `OfficeOfficer`, `UnitCost`, `Amount`, `Remarks`, `SourceType`, `SourceID`, `SourceKey`, `CreatedBy`, `CreatedAt`) VALUES
(1, 14, '2026-03-18 19:41:22', 'acquisition', 1, 0, 'IAR-2026-0015', 'GSO', 165000.00, 165000.00, 'Opening entry from PAR/IAR source', 'opening', 14, 'opening:14', NULL, '2026-04-14 19:37:22'),
(2, 5, '2026-03-18 18:11:05', 'acquisition', 1, 0, 'PAR-2026-0001', 'GSO', 100.00, 100.00, 'Auto-ingested from DR #dr21', 'opening', 5, 'opening:5', NULL, '2026-04-20 12:21:06'),
(3, 5, '2026-03-18 21:18:19', 'borrow', 0, 1, 'PBR-2026-0001', 'Borrowing office', 100.00, 100.00, 'Explain the temporary use case.', 'audit', 25, 'audit:25', 6, '2026-03-18 21:18:19'),
(4, 5, '2026-03-18 21:25:11', 'borrow_return', 1, 0, 'PBR-RET-2026-0001', 'Returned to accountable office', 100.00, 100.00, 'done', 'audit', 32, 'audit:32', 1, '2026-03-18 21:25:11'),
(5, 8, '2026-03-18 19:41:22', 'acquisition', 1, 0, 'IAR-2026-0014', 'Supply and Property Unit', 3200.00, 3200.00, 'Opening entry from IAR/Inventory', 'opening', 8, 'opening:8', NULL, '2026-04-10 00:48:55'),
(6, 9, '2026-03-18 19:41:22', 'acquisition', 1, 0, 'PAR-2026-0003', 'GSO', 11500.00, 11500.00, 'Opening entry from PAR/IAR source', 'opening', 9, 'opening:9', NULL, '2026-04-10 00:52:24'),
(7, 139, '2026-03-29 20:32:46', 'acquisition', 1, 0, 'PAR-2026-0010', 'GSO', 42500.00, 42500.00, 'Auto-ingested from DR #DR-TEST-1', 'opening', 139, 'opening:139', NULL, '2026-04-20 12:21:36'),
(8, 7, '2026-03-18 19:41:22', 'acquisition', 1, 0, 'PAR-2026-0003', 'GSO', 8500.00, 8500.00, 'Opening entry from PAR/IAR source', 'opening', 7, 'opening:7', NULL, '2026-04-15 20:40:26'),
(9, 74, '2026-03-18 19:41:22', 'acquisition', 1, 0, 'PAR-2026-0009', 'GSO', 560.00, 560.00, 'Backfilled from generated DR #DR-2026-03-18-004', 'opening', 74, 'opening:74', NULL, '2026-04-20 11:53:09'),
(10, 74, '2026-04-20 11:53:09', 'transfer', 0, 1, 'PTR-2026-0004', 'Department #30 / EndUser #2', 560.00, 560.00, 'extension', 'audit', 251, 'audit:251', 1, '2026-04-20 11:53:09'),
(11, 145, '2026-04-20 12:05:52', 'acquisition', 1, 0, 'PAR-2026-0013', 'GSO', 2400.00, 2400.00, 'Auto-ingested from DR #drtest12321', 'opening', 145, 'opening:145', NULL, '2026-04-20 12:20:12'),
(12, 143, '2026-03-29 21:15:35', 'acquisition', 1, 0, 'PAR-2026-0012', 'GSO', 3200.00, 3200.00, 'Auto-ingested from DR #DR-TEST-3', 'opening', 143, 'opening:143', NULL, '2026-04-20 12:20:31'),
(13, 140, '2026-03-29 20:32:46', 'acquisition', 1, 0, 'PAR-2026-0010', 'GSO', 3200.00, 3200.00, 'good', 'opening', 140, 'opening:140', NULL, '2026-04-20 12:51:17'),
(14, 146, '2026-04-20 13:18:33', 'acquisition', 1, 0, 'PAR-2026-0014', 'GSO', 9998.90, 9998.90, 'All Good', 'opening', 146, 'opening:146', NULL, '2026-04-24 15:26:33'),
(15, 147, '2026-04-20 13:18:33', 'acquisition', 1, 0, 'PAR-2026-0014', 'GSO', 19000.00, 19000.00, 'All Good', 'opening', 147, 'opening:147', NULL, '2026-04-24 19:57:58'),
(16, 47, '2026-03-18 19:41:22', 'acquisition', 1, 0, 'PAR-2026-0009', 'GSO', 4200.00, 4200.00, 'Batch 4 additional non-expendable units.', 'opening', 47, 'opening:47', NULL, '2026-04-20 13:27:04'),
(17, 47, '2026-04-20 13:24:28', 'borrow', 0, 1, 'PBR-2026-0006', 'Borrowing office', 4200.00, 4200.00, 'll', 'audit', 262, 'audit:262', 3, '2026-04-20 13:24:28'),
(18, 48, '2026-03-18 19:41:22', 'acquisition', 1, 0, 'PAR-2026-0009', 'GSO', 420.00, 420.00, 'Batch 4 additional non-expendable units.', 'opening', 48, 'opening:48', NULL, '2026-04-20 13:26:45'),
(19, 48, '2026-04-20 13:26:45', 'transfer', 0, 1, 'PTR-2026-0007', 'Department #30 / EndUser #2', 420.00, 420.00, 'kk', 'audit', 270, 'audit:270', 1, '2026-04-20 13:26:45'),
(20, 12, '2026-03-18 19:41:22', 'acquisition', 1, 0, 'PAR-2026-0003', 'GSO', 2350.00, 2350.00, 'Batch 1 office and ICT assets for operational rollout.', 'opening', 12, 'opening:12', NULL, '2026-04-24 15:26:33'),
(21, 25, '2026-03-18 19:41:22', 'acquisition', 1, 0, 'PAR-2026-0009', 'GSO', 49900.00, 49900.00, 'Batch 4 additional non-expendable units.', 'opening', 25, 'opening:25', NULL, '2026-04-23 16:02:02'),
(22, 25, '2026-04-04 01:36:35', 'borrow', 0, 1, 'PBR-2026-0005', 'Borrowing office', 49900.00, 49900.00, 'Released under borrow transaction', 'audit', 206, 'audit:206', 3, '2026-04-04 01:36:35'),
(23, 25, '2026-04-04 01:40:55', 'borrow_return', 1, 0, 'PBR-RET-2026-0004', 'Returned to accountable office', 49900.00, 49900.00, 'ohye', 'audit', 213, 'audit:213', 1, '2026-04-04 01:40:55'),
(24, 25, '2026-04-23 16:02:02', 'transfer', 0, 1, 'PTR-2026-0008', 'Department #30 / EndUser #2', 49900.00, 49900.00, 'balhin', 'audit', 281, 'audit:281', 1, '2026-04-23 16:02:02'),
(25, 147, '2026-04-23 21:05:32', 'transfer', 0, 1, 'PTR-2026-0010', 'Department #23 / EndUser #4', 19000.00, 19000.00, 'hahahehe', 'audit', 290, 'audit:290', 1, '2026-04-23 21:05:32'),
(26, 26, '2026-03-18 19:41:22', 'acquisition', 1, 0, 'PAR-2026-0009', 'GSO', 980.00, 980.00, 'Batch 4 additional non-expendable units.', 'opening', 26, 'opening:26', NULL, '2026-04-24 16:19:25'),
(27, 26, '2026-04-23 21:08:44', 'borrow', 0, 1, 'PBR-2026-0007', 'Borrowing office', 980.00, 980.00, 'basta', 'audit', 293, 'audit:293', 1, '2026-04-23 21:08:44'),
(28, 26, '2026-04-23 21:09:24', 'borrow_return', 1, 0, 'PBR-RET-2026-0005', 'Returned to accountable office', 980.00, 980.00, 'goods.', 'audit', 297, 'audit:297', 1, '2026-04-23 21:09:24'),
(29, 29, '2026-03-18 19:41:22', 'acquisition', 1, 0, 'PAR-2026-0009', 'GSO', 420.00, 420.00, 'Batch 4 additional non-expendable units.', 'opening', 29, 'opening:29', NULL, '2026-04-24 15:26:33'),
(30, 29, '2026-04-04 01:09:22', 'transfer', 0, 1, 'PTR-2026-0006', 'Department #30 / EndUser #2', 420.00, 420.00, 'Transferred to receiving office', 'audit', 203, 'audit:203', 1, '2026-04-04 01:09:22'),
(31, 26, '2026-04-24 16:19:25', 'borrow', 0, 1, 'PBR-2026-0008', 'Borrowing office', 980.00, 980.00, 'Released under borrow transaction', 'audit', 308, 'audit:308', 1, '2026-04-24 16:19:25'),
(32, 27, '2026-03-18 19:41:22', 'acquisition', 1, 0, 'PAR-2026-0009', 'GSO', 560.00, 560.00, 'Batch 4 additional non-expendable units.', 'opening', 27, 'opening:27', NULL, '2026-04-24 19:41:23'),
(33, 27, '2026-04-24 16:19:25', 'borrow', 0, 1, 'PBR-2026-0008', 'Borrowing office', 560.00, 560.00, 'Released under borrow transaction', 'audit', 309, 'audit:309', 1, '2026-04-24 16:19:25'),
(34, 46, '2026-03-18 19:41:22', 'acquisition', 1, 0, 'PAR-2026-0009', 'GSO', 560.00, 560.00, 'Batch 4 additional non-expendable units.', 'opening', 46, 'opening:46', NULL, '2026-04-24 16:19:25'),
(35, 46, '2026-04-24 16:19:25', 'borrow', 0, 1, 'PBR-2026-0008', 'Borrowing office', 560.00, 560.00, 'Released under borrow transaction', 'audit', 310, 'audit:310', 1, '2026-04-24 16:19:25'),
(36, 63, '2026-03-18 19:41:22', 'acquisition', 1, 0, 'PAR-2026-0009', 'GSO', 420.00, 420.00, 'Batch 4 additional non-expendable units.', 'opening', 63, 'opening:63', NULL, '2026-04-24 19:57:58'),
(37, 27, '2026-04-24 19:41:23', 'borrow_return', 1, 0, 'PBR-RET-2026-0006', 'Returned to accountable office', 560.00, 560.00, 'Returned from borrow transaction', 'audit', 321, 'audit:321', 1, '2026-04-24 19:41:23'),
(38, 147, '2026-04-24 19:57:58', 'transfer', 0, 1, 'PTR-2026-0012', 'Department #30 / EndUser #2', 19000.00, 19000.00, 'Transferred to receiving office', 'audit', 323, 'audit:323', 1, '2026-04-24 19:57:58'),
(39, 63, '2026-04-24 19:57:58', 'transfer', 0, 1, 'PTR-2026-0012', 'Department #30 / EndUser #2', 420.00, 420.00, 'Shortfall reason: dili daw pwede', 'audit', 325, 'audit:325', 1, '2026-04-24 19:57:58');

-- --------------------------------------------------------

--
-- Table structure for table `property_waste_items`
--

CREATE TABLE `property_waste_items` (
  `PropertyWasteItemID` bigint(20) UNSIGNED NOT NULL,
  `PropertyWasteReportID` bigint(20) UNSIGNED NOT NULL,
  `ServiceRecordDetailID` bigint(20) UNSIGNED DEFAULT NULL,
  `InventoryDetailID` int(11) NOT NULL,
  `PropertyNumber` varchar(80) DEFAULT NULL,
  `ItemDescription` varchar(255) DEFAULT NULL,
  `ConditionID` int(11) DEFAULT NULL,
  `DisposalMethodID` int(11) DEFAULT NULL,
  `DisposalMethodNote` text DEFAULT NULL,
  `Status` enum('pending_method','method_selected','for_disposal','disposed') NOT NULL DEFAULT 'pending_method',
  `ActionedByUserID` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_waste_items`
--

INSERT INTO `property_waste_items` (`PropertyWasteItemID`, `PropertyWasteReportID`, `ServiceRecordDetailID`, `InventoryDetailID`, `PropertyNumber`, `ItemDescription`, `ConditionID`, `DisposalMethodID`, `DisposalMethodNote`, `Status`, `ActionedByUserID`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 4, 144, '2026-000117', 'Brother DCP-L5650DN', 2, 3, NULL, 'disposed', 1, '2026-04-05 09:22:46', '2026-04-05 09:55:17', NULL),
(2, 2, 5, 143, '2026-000116', 'Ergonomic Mesh Chair', 2, 3, NULL, 'method_selected', 1, '2026-04-05 10:24:24', '2026-04-09 09:37:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `property_waste_reports`
--

CREATE TABLE `property_waste_reports` (
  `PropertyWasteReportID` bigint(20) UNSIGNED NOT NULL,
  `WasteNumber` varchar(30) NOT NULL,
  `SourceServiceRecordID` bigint(20) UNSIGNED DEFAULT NULL,
  `DepartmentID` int(11) DEFAULT NULL,
  `Status` enum('draft','completed','cancelled') NOT NULL DEFAULT 'draft',
  `Notes` text DEFAULT NULL,
  `CreatedByUserID` int(11) DEFAULT NULL,
  `UpdatedByUserID` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `property_waste_reports`
--

INSERT INTO `property_waste_reports` (`PropertyWasteReportID`, `WasteNumber`, `SourceServiceRecordID`, `DepartmentID`, `Status`, `Notes`, `CreatedByUserID`, `UpdatedByUserID`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'WST-2026-0001', 4, 30, 'completed', 'Auto-created from service record unserviceable outcome.', 1, 1, '2026-04-05 09:22:46', '2026-04-05 09:55:17', NULL),
(2, 'WST-2026-0002', 5, 30, 'draft', 'Auto-created from service record unserviceable outcome.', 1, 1, '2026-04-05 10:24:24', '2026-04-05 10:24:24', NULL);

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
(6, 6, 6, 30, 'http://localhost:8000/qr/scan/6?h=b67ece7330f6', '2026-03-18 21:04:32'),
(7, 7, 7, 30, 'http://localhost:3000/qr/scan/7?h=be5deb3717e3', '2026-03-22 22:53:00'),
(8, 8, 31, 30, 'http://localhost:3000/qr/scan/8?h=983167e13eaf', '2026-03-22 22:53:00'),
(9, 9, 49, 30, 'http://localhost:3000/qr/scan/9?h=597644aeb803', '2026-03-22 22:53:00'),
(10, 10, 64, 30, 'http://localhost:3000/qr/scan/10?h=d779f6862b95', '2026-03-22 22:53:00'),
(11, 11, 8, 30, 'http://localhost:3000/qr/scan/11?h=12f6416459ec', '2026-03-22 22:53:00'),
(12, 12, 32, 30, 'http://localhost:3000/qr/scan/12?h=f4ccb2ccf863', '2026-03-22 22:53:00'),
(13, 13, 50, 30, 'http://localhost:3000/qr/scan/13?h=40104f265d90', '2026-03-22 22:53:00'),
(14, 14, 65, 30, 'http://localhost:3000/qr/scan/14?h=1ebd50d5370b', '2026-03-22 22:53:00'),
(15, 15, 76, 30, 'http://localhost:3000/qr/scan/15?h=6a1a8bdc96cf', '2026-03-22 22:53:00'),
(16, 16, 84, 30, 'http://localhost:3000/qr/scan/16?h=3119ef8e8daa', '2026-03-22 22:53:00'),
(17, 17, 91, 30, 'http://localhost:3000/qr/scan/17?h=06cd4f81be7e', '2026-03-22 22:53:00'),
(18, 18, 96, 30, 'http://localhost:3000/qr/scan/18?h=1c92449fec47', '2026-03-22 22:53:00'),
(19, 19, 9, 30, 'http://localhost:3000/qr/scan/19?h=63afafb41655', '2026-03-22 22:53:00'),
(20, 20, 33, 30, 'http://localhost:3000/qr/scan/20?h=a19f866389f0', '2026-03-22 22:53:00'),
(21, 21, 10, 30, 'http://localhost:3000/qr/scan/21?h=12a952e8dc01', '2026-03-22 22:53:00'),
(22, 22, 34, 30, 'http://localhost:3000/qr/scan/22?h=031482b03d11', '2026-03-22 22:53:00'),
(23, 23, 51, 30, 'http://localhost:3000/qr/scan/23?h=6e465a5c7bf0', '2026-03-22 22:53:00'),
(24, 24, 66, 30, 'http://localhost:3000/qr/scan/24?h=5385ed6d0260', '2026-03-22 22:53:00'),
(25, 25, 77, 30, 'http://localhost:3000/qr/scan/25?h=7f73298841d6', '2026-03-22 22:53:00'),
(26, 26, 85, 30, 'http://localhost:3000/qr/scan/26?h=3d36c6fca545', '2026-03-22 22:53:00'),
(27, 27, 11, 30, 'http://localhost:3000/qr/scan/27?h=0a46d32f47b3', '2026-03-22 22:53:00'),
(28, 28, 35, 30, 'http://localhost:3000/qr/scan/28?h=582856aaeb93', '2026-03-22 22:53:00'),
(29, 29, 12, 30, 'http://localhost:3000/qr/scan/29?h=67b7e9a4b1f3', '2026-03-22 22:53:00'),
(30, 30, 134, 30, 'http://localhost:3000/qr/scan/30?h=d6896e269f53', '2026-03-22 22:54:56'),
(31, 31, 135, 30, 'http://localhost:3000/qr/scan/31?h=ec40047ce8f9', '2026-03-22 23:17:31'),
(32, 32, 136, 30, 'http://localhost:3000/qr/scan/32?h=f7d1d50763e3', '2026-03-22 23:22:27'),
(33, 33, 137, 30, 'http://localhost:3000/qr/scan/33?h=1fd2068b4c4b', '2026-03-24 11:40:12'),
(34, 34, 138, 30, 'http://localhost:3000/qr/scan/34?h=c54deffee3ca', '2026-03-24 11:43:37'),
(35, 35, 25, 30, 'http://localhost:8000/qr/scan/35?h=c4d4216993be', '2026-04-23 16:02:02'),
(36, 36, 26, 23, 'http://localhost:3000/qr/scan/36?h=ce464a891374', '2026-03-24 11:55:01'),
(37, 37, 45, 23, 'http://localhost:3000/qr/scan/37?h=aec340323004', '2026-03-24 11:55:01'),
(38, 38, 60, 23, 'http://localhost:3000/qr/scan/38?h=ebab3c505b56', '2026-03-24 11:55:01'),
(39, 39, 73, 23, 'http://localhost:3000/qr/scan/39?h=dcf9cac0f3f6', '2026-03-24 11:55:01'),
(40, 40, 81, 23, 'http://localhost:3000/qr/scan/40?h=116f7c7f1e62', '2026-03-24 11:55:01'),
(41, 41, 88, 23, 'http://localhost:3000/qr/scan/41?h=bd121e766ed1', '2026-03-24 11:55:01'),
(42, 42, 93, 23, 'http://localhost:3000/qr/scan/42?h=479c8d8c55ce', '2026-03-24 11:55:01'),
(43, 43, 98, 23, 'http://localhost:3000/qr/scan/43?h=3b97c4650685', '2026-03-24 11:55:01'),
(44, 44, 102, 23, 'http://localhost:3000/qr/scan/44?h=4c2eebf5b925', '2026-03-24 11:55:01'),
(45, 45, 106, 23, 'http://localhost:3000/qr/scan/45?h=c4807b78f58a', '2026-03-24 11:55:01'),
(46, 46, 109, 23, 'http://localhost:3000/qr/scan/46?h=46cc4d5d18b8', '2026-03-24 11:55:01'),
(47, 47, 110, 23, 'http://localhost:3000/qr/scan/47?h=8193cece10b6', '2026-03-24 11:55:01'),
(48, 48, 27, 23, 'http://localhost:3000/qr/scan/48?h=f858bdc6ffb7', '2026-03-24 11:55:01'),
(49, 49, 46, 23, 'http://localhost:3000/qr/scan/49?h=ba6ca30d5d9e', '2026-03-24 11:55:01'),
(50, 50, 61, 23, 'http://localhost:3000/qr/scan/50?h=8dd76c6008ad', '2026-03-24 11:55:01'),
(51, 51, 74, 30, 'http://localhost:8000/qr/scan/51?h=63e28350135b', '2026-04-20 11:53:09'),
(52, 52, 82, 23, 'http://localhost:3000/qr/scan/52?h=557b693125d1', '2026-03-24 11:55:01'),
(53, 53, 89, 23, 'http://localhost:3000/qr/scan/53?h=9f1a640c9b2e', '2026-03-24 11:55:01'),
(54, 54, 94, 23, 'http://localhost:3000/qr/scan/54?h=d51c5749e227', '2026-03-24 11:55:01'),
(55, 55, 99, 23, 'http://localhost:3000/qr/scan/55?h=d60e48b9a3b6', '2026-03-24 11:55:01'),
(56, 56, 103, 23, 'http://localhost:3000/qr/scan/56?h=22583fa890b1', '2026-03-24 11:55:01'),
(57, 57, 107, 23, 'http://localhost:3000/qr/scan/57?h=db1ae690b777', '2026-03-24 11:55:01'),
(58, 58, 28, 30, 'http://localhost:8000/qr/scan/58?h=996fb9369fea', '2026-03-29 21:26:06'),
(59, 59, 47, 23, 'http://localhost:3000/qr/scan/59?h=9e96df6f8440', '2026-03-24 11:55:01'),
(60, 60, 62, 23, 'http://localhost:3000/qr/scan/60?h=b8543217fe36', '2026-03-24 11:55:01'),
(61, 61, 29, 30, 'http://localhost:8000/qr/scan/61?h=744b1bc5086e', '2026-04-04 01:09:22'),
(62, 62, 48, 30, 'http://localhost:8000/qr/scan/62?h=03faa7ec35cf', '2026-04-20 13:26:45'),
(63, 63, 63, 30, 'http://localhost:8000/qr/scan/63?h=2f77cc15bd27', '2026-04-24 19:57:58'),
(64, 64, 75, 23, 'http://localhost:3000/qr/scan/64?h=bcb450fe5267', '2026-03-24 11:55:01'),
(65, 65, 83, 23, 'http://localhost:3000/qr/scan/65?h=c7d2a5dfc3d6', '2026-03-24 11:55:01'),
(66, 66, 90, 23, 'http://localhost:3000/qr/scan/66?h=b7d672f941a6', '2026-03-24 11:55:01'),
(67, 67, 95, 23, 'http://localhost:3000/qr/scan/67?h=df16520d2b83', '2026-03-24 11:55:01'),
(68, 68, 100, 23, 'http://localhost:3000/qr/scan/68?h=12ccf659a8e3', '2026-03-24 11:55:01'),
(69, 69, 104, 23, 'http://localhost:3000/qr/scan/69?h=1cf6a5be8b10', '2026-03-24 11:55:01'),
(70, 70, 108, 23, 'http://localhost:3000/qr/scan/70?h=9f2026238513', '2026-03-24 11:55:01'),
(71, 71, 30, 23, 'http://localhost:8000/qr/scan/71?h=e31ab4a4cb99', '2026-03-25 13:29:48'),
(72, 72, 139, 30, 'http://localhost:3000/qr/scan/72?h=36e7b0289d2d', '2026-03-29 20:32:46'),
(73, 73, 140, 30, 'http://localhost:3000/qr/scan/73?h=a10430160727', '2026-03-29 20:32:46'),
(74, 74, 141, 30, 'http://localhost:3000/qr/scan/74?h=9c41b1d9b307', '2026-03-29 20:52:16'),
(75, 75, 142, 30, 'http://localhost:3000/qr/scan/75?h=e7bd4826a260', '2026-03-29 20:52:16'),
(76, 76, 143, 30, 'http://localhost:3000/qr/scan/76?h=d25ec7b8e9e0', '2026-03-29 21:15:35'),
(77, 77, 144, 30, 'http://localhost:3000/qr/scan/77?h=ceff6ffd2404', '2026-03-29 21:15:35'),
(78, 78, 145, 30, 'http://localhost:3000/qr/scan/78?h=c7a6f64e7fd5', '2026-04-20 12:05:52'),
(79, 79, 146, 30, 'http://localhost:3000/qr/scan/79?h=5de5d2a4c59c', '2026-04-20 13:18:33'),
(80, 80, 147, 30, 'http://localhost:8000/qr/scan/80?h=2e3d18602c10', '2026-04-24 19:57:58'),
(81, 81, 207, 23, 'http://localhost:3000/qr/scan/81?h=b2526f83304b', '2026-04-28 00:39:44'),
(82, 82, 208, 23, 'http://localhost:3000/qr/scan/82?h=777ac1dad350', '2026-04-28 00:39:44'),
(83, 83, 209, 23, 'http://localhost:3000/qr/scan/83?h=04ce8d2e155f', '2026-04-28 00:39:44'),
(84, 84, 210, 23, 'http://localhost:3000/qr/scan/84?h=6603e9d2a087', '2026-04-28 00:39:44'),
(85, 85, 211, 23, 'http://localhost:3000/qr/scan/85?h=70d3c6f6195e', '2026-04-28 00:39:44'),
(86, 86, 212, 23, 'http://localhost:3000/qr/scan/86?h=665539b39e88', '2026-04-28 00:39:44'),
(87, 87, 213, 23, 'http://localhost:3000/qr/scan/87?h=a581beb1a26a', '2026-04-28 00:39:44'),
(88, 88, 214, 23, 'http://localhost:3000/qr/scan/88?h=802ec510101a', '2026-04-28 00:39:44'),
(89, 89, 215, 23, 'http://localhost:3000/qr/scan/89?h=b7a89596371b', '2026-04-28 00:39:44'),
(90, 90, 216, 23, 'http://localhost:3000/qr/scan/90?h=020ce102d32b', '2026-04-28 00:39:44'),
(91, 91, 217, 23, 'http://localhost:3000/qr/scan/91?h=9401fe054faa', '2026-04-28 00:39:44'),
(92, 92, 218, 23, 'http://localhost:3000/qr/scan/92?h=961cd6e13670', '2026-04-28 00:39:44'),
(93, 93, 219, 23, 'http://localhost:3000/qr/scan/93?h=febf894aa979', '2026-04-28 00:39:44'),
(94, 94, 220, 23, 'http://localhost:3000/qr/scan/94?h=4aa8c1f458f3', '2026-04-28 00:39:44'),
(95, 95, 221, 23, 'http://localhost:3000/qr/scan/95?h=9dfc500ecdab', '2026-04-28 00:39:44'),
(96, 96, 222, 23, 'http://localhost:3000/qr/scan/96?h=203e999f0823', '2026-04-28 00:39:44'),
(97, 97, 223, 23, 'http://localhost:3000/qr/scan/97?h=04905db8e868', '2026-04-28 00:39:44'),
(98, 98, 224, 23, 'http://localhost:3000/qr/scan/98?h=a7dbb17f36b5', '2026-04-28 00:39:44'),
(99, 99, 225, 23, 'http://localhost:3000/qr/scan/99?h=c1c7d9e59270', '2026-04-28 00:39:44'),
(100, 100, 226, 23, 'http://localhost:3000/qr/scan/100?h=ccdfbb2d69ec', '2026-04-28 00:39:44'),
(101, 101, 227, 23, 'http://localhost:3000/qr/scan/101?h=838c5033edbe', '2026-04-28 00:39:44'),
(102, 102, 228, 23, 'http://localhost:3000/qr/scan/102?h=bcbe67324215', '2026-04-28 00:39:44'),
(103, 103, 229, 23, 'http://localhost:3000/qr/scan/103?h=60ada56e2509', '2026-04-28 00:39:44'),
(104, 104, 230, 23, 'http://localhost:3000/qr/scan/104?h=4c4a9cf19b40', '2026-04-28 00:39:44'),
(105, 105, 231, 23, 'http://localhost:3000/qr/scan/105?h=f875f35897a7', '2026-04-28 00:39:44'),
(106, 106, 232, 23, 'http://localhost:3000/qr/scan/106?h=b6187e958a7f', '2026-04-28 00:39:44'),
(107, 107, 233, 23, 'http://localhost:3000/qr/scan/107?h=d992ab9088b4', '2026-04-28 00:39:44'),
(108, 108, 234, 23, 'http://localhost:3000/qr/scan/108?h=241f6e84f089', '2026-04-28 00:39:44'),
(109, 109, 235, 23, 'http://localhost:3000/qr/scan/109?h=4a00263431c1', '2026-04-28 00:39:44'),
(110, 110, 236, 23, 'http://localhost:3000/qr/scan/110?h=de257a7182f0', '2026-04-28 00:39:45'),
(111, 111, 237, 23, 'http://localhost:3000/qr/scan/111?h=3eff609250f4', '2026-04-28 00:39:45'),
(112, 112, 238, 23, 'http://localhost:3000/qr/scan/112?h=150a59d6b0a0', '2026-04-28 00:39:45'),
(113, 113, 239, 23, 'http://localhost:3000/qr/scan/113?h=383da3f1bd24', '2026-04-28 00:39:45'),
(114, 114, 240, 23, 'http://localhost:3000/qr/scan/114?h=961a02c284f9', '2026-04-28 00:39:45'),
(115, 115, 241, 23, 'http://localhost:3000/qr/scan/115?h=07aa65f8482f', '2026-04-28 00:39:45'),
(116, 116, 242, 23, 'http://localhost:3000/qr/scan/116?h=2bb763b9ab18', '2026-04-28 00:39:45'),
(117, 117, 243, 23, 'http://localhost:3000/qr/scan/117?h=499f2f2e0e7f', '2026-04-28 00:39:45'),
(118, 118, 244, 23, 'http://localhost:3000/qr/scan/118?h=a68bc378ca69', '2026-04-28 00:39:45'),
(119, 119, 245, 23, 'http://localhost:3000/qr/scan/119?h=d50f970ee9cd', '2026-04-28 00:39:45'),
(120, 120, 246, 23, 'http://localhost:3000/qr/scan/120?h=8f35ac958ba1', '2026-04-28 00:39:45'),
(121, 121, 247, 23, 'http://localhost:3000/qr/scan/121?h=abd6665ccf28', '2026-04-28 00:39:45'),
(122, 122, 248, 23, 'http://localhost:3000/qr/scan/122?h=fd1528156535', '2026-04-28 00:39:45'),
(123, 123, 249, 23, 'http://localhost:3000/qr/scan/123?h=8cad8f3eb137', '2026-04-28 00:39:45'),
(124, 124, 250, 23, 'http://localhost:3000/qr/scan/124?h=34c5fd1ecf54', '2026-04-28 00:39:45'),
(125, 125, 251, 23, 'http://localhost:3000/qr/scan/125?h=e7bc2bad9767', '2026-04-28 00:39:45'),
(126, 126, 252, 23, 'http://localhost:3000/qr/scan/126?h=d398e03b5481', '2026-04-28 00:39:45'),
(127, 127, 253, 23, 'http://localhost:3000/qr/scan/127?h=4a63d34ede2a', '2026-04-28 00:39:45'),
(128, 128, 148, 30, 'http://localhost:3000/qr/scan/128?h=3fcda366d08b', '2026-04-29 10:22:06'),
(129, 129, 149, 30, 'http://localhost:3000/qr/scan/129?h=a26286064ed6', '2026-04-29 10:22:06'),
(130, 130, 150, 30, 'http://localhost:3000/qr/scan/130?h=716b73eb6bf0', '2026-04-29 10:22:06'),
(131, 131, 151, 30, 'http://localhost:3000/qr/scan/131?h=c22c3e01c609', '2026-04-29 10:22:06'),
(132, 132, 152, 30, 'http://localhost:3000/qr/scan/132?h=7708993a12fe', '2026-04-29 10:22:06'),
(133, 133, 153, 30, 'http://localhost:3000/qr/scan/133?h=17d5bd80fdbe', '2026-04-29 10:22:06'),
(134, 134, 154, 30, 'http://localhost:3000/qr/scan/134?h=4ff8999e9b63', '2026-04-29 10:22:06'),
(135, 135, 155, 30, 'http://localhost:3000/qr/scan/135?h=02d6b9443a79', '2026-04-29 10:22:06'),
(136, 136, 156, 30, 'http://localhost:3000/qr/scan/136?h=28db46e6066e', '2026-04-29 10:22:06'),
(137, 137, 157, 30, 'http://localhost:3000/qr/scan/137?h=51ac2706150e', '2026-04-29 10:22:06'),
(138, 138, 158, 30, 'http://localhost:3000/qr/scan/138?h=771a88a1a832', '2026-04-29 10:22:06'),
(139, 139, 159, 30, 'http://localhost:3000/qr/scan/139?h=be7e756402b4', '2026-04-29 10:22:06'),
(140, 140, 160, 30, 'http://localhost:3000/qr/scan/140?h=5e25974b7429', '2026-04-29 10:22:06'),
(141, 141, 161, 30, 'http://localhost:3000/qr/scan/141?h=31ef2a29cbe0', '2026-04-29 12:47:23'),
(142, 142, 162, 30, 'http://localhost:3000/qr/scan/142?h=a40eb360ab73', '2026-04-29 12:47:23'),
(143, 143, 163, 30, 'http://localhost:3000/qr/scan/143?h=79302f9dc954', '2026-04-29 12:47:23'),
(144, 144, 164, 30, 'http://localhost:3000/qr/scan/144?h=f67466285cce', '2026-04-29 12:47:23'),
(145, 145, 165, 30, 'http://localhost:3000/qr/scan/145?h=76e36e0867f9', '2026-04-29 12:47:23'),
(146, 146, 166, 30, 'http://localhost:3000/qr/scan/146?h=650e8e865601', '2026-04-29 12:47:23'),
(147, 147, 167, 30, 'http://localhost:3000/qr/scan/147?h=709945b54213', '2026-04-29 12:47:23'),
(148, 148, 168, 30, 'http://localhost:3000/qr/scan/148?h=4167c4a92767', '2026-04-29 12:47:23'),
(149, 149, 169, 30, 'http://localhost:3000/qr/scan/149?h=bb68cf85e150', '2026-04-29 12:47:23'),
(150, 150, 170, 30, 'http://localhost:3000/qr/scan/150?h=b6a101a67686', '2026-04-29 12:47:24'),
(151, 151, 171, 30, 'http://localhost:3000/qr/scan/151?h=23cdea53f870', '2026-04-29 12:47:24'),
(152, 152, 172, 30, 'http://localhost:3000/qr/scan/152?h=3c9af264dee5', '2026-04-29 12:47:24'),
(153, 153, 173, 30, 'http://localhost:3000/qr/scan/153?h=f7e265eb40bb', '2026-04-29 12:47:24'),
(154, 154, 174, 30, 'http://localhost:3000/qr/scan/154?h=43464ddc4683', '2026-04-29 12:47:24'),
(155, 155, 175, 30, 'http://localhost:3000/qr/scan/155?h=61afa6199437', '2026-04-29 12:47:24'),
(156, 156, 176, 30, 'http://localhost:3000/qr/scan/156?h=40e29d0cdbe2', '2026-04-29 12:47:24'),
(157, 157, 177, 30, 'http://localhost:3000/qr/scan/157?h=20cb12dd3d20', '2026-04-29 12:47:24'),
(158, 158, 178, 30, 'http://localhost:3000/qr/scan/158?h=9e0d6a537716', '2026-04-29 12:47:24'),
(159, 159, 179, 30, 'http://localhost:3000/qr/scan/159?h=e20430874602', '2026-04-29 12:47:24'),
(160, 160, 180, 30, 'http://localhost:3000/qr/scan/160?h=67c086aa66f1', '2026-04-29 12:47:24'),
(161, 161, 181, 30, 'http://localhost:3000/qr/scan/161?h=70875c4ed4a0', '2026-04-29 12:47:24'),
(162, 162, 182, 30, 'http://localhost:3000/qr/scan/162?h=71ba65495fe3', '2026-04-29 12:47:24'),
(163, 163, 183, 30, 'http://localhost:3000/qr/scan/163?h=4a22616564c0', '2026-04-29 12:47:24'),
(164, 164, 184, 30, 'http://localhost:3000/qr/scan/164?h=4964c24e0a27', '2026-04-29 12:47:24');

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

--
-- Dumping data for table `requisition_issue_slips`
--

INSERT INTO `requisition_issue_slips` (`id`, `asset_request_id`, `ris_number`, `entity_name`, `fund_cluster`, `division`, `responsibility_center_code`, `purpose`, `status`, `requested_by_user_id`, `requested_at`, `approved_by_user_id`, `approved_at`, `prepared_by_user_id`, `prepared_at`, `issued_by_user_id`, `issued_at`, `received_by_user_id`, `received_at`, `remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '2026-04-0001', 'Chokoy Covid', 'AUTO', 'BJMP', 'BJMP', 'Mixed PPE and semi-expendable assets for administrative and ICT use.', 'issued', 3, '2026-04-08 00:00:00', 1, '2026-04-29 10:22:06', 1, '2026-04-29 10:22:06', 1, '2026-04-29 10:22:06', NULL, NULL, 'Auto-generated from delivery receipt acknowledgment', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(2, 2, '2026-04-29-2026', 'Chokoy Covid', 'AUTO', 'BJMP', 'BJMP', 'Second batch of office, ICT, and maintenance items.', 'issued', 3, '2026-04-12 00:00:00', 1, '2026-04-29 12:47:24', 1, '2026-04-29 12:47:24', 1, '2026-04-29 12:47:24', NULL, NULL, 'Auto-generated from delivery receipt acknowledgment', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL);

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

--
-- Dumping data for table `requisition_issue_slip_items`
--

INSERT INTO `requisition_issue_slip_items` (`id`, `ris_id`, `asset_request_detail_id`, `inventory_detail_id`, `stock_no`, `unit`, `description`, `quantity_requested`, `stock_available`, `quantity_issued`, `issue_remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 148, '2026-000121', 'unit', 'Desktop Computer', 1, 1, 1, 'Issued in full', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(2, 1, 2, 149, '2026-000122', 'unit', 'Printer/Scanner', 1, 1, 1, 'Issued in full', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(3, 1, 3, 150, '2026-000123', 'unit', 'Office Chair', 1, 1, 1, 'Issued in full', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(4, 1, 4, 151, '2026-000124', 'unit', 'Portable rechargeable emergency lamp', 1, 1, 1, 'Issued in full', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(5, 1, 5, 152, '2026-000125', 'unit', 'Portable rechargeable emergency lamp', 1, 1, 1, 'Issued in full', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(6, 1, 6, 153, '2026-000126', 'unit', 'Portable rechargeable emergency lamp', 1, 1, 1, 'Issued in full', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(7, 1, 7, 154, '2026-000127', 'unit', '32GB USB 3.0 flash drive', 1, 1, 1, 'Issued in full', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(8, 1, 8, 155, '2026-000128', 'unit', '32GB USB 3.0 flash drive', 1, 1, 1, 'Issued in full', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(9, 1, 9, 156, '2026-000129', 'set', 'Wireless keyboard and mouse set', 1, 1, 1, 'Issued in full', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(10, 1, 10, 157, '2026-000130', 'set', 'Wireless keyboard and mouse set', 1, 1, 1, 'Issued in full', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(11, 1, 11, 158, '2026-000131', 'set', 'Wireless keyboard and mouse set', 1, 1, 1, 'Issued in full', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(12, 1, 12, 159, '2026-000132', 'set', 'Wireless keyboard and mouse set', 1, 1, 1, 'Issued in full', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(13, 1, 13, 160, '2026-000133', 'set', 'Wireless keyboard and mouse set', 1, 1, 1, 'Issued in full', '2026-04-29 02:22:06', '2026-04-29 02:22:06', NULL),
(14, 2, 14, 161, '2026-000134', 'unit', 'Laptop Computer', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(15, 2, 15, 162, '2026-000135', 'unit', 'Filing Cabinet', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(16, 2, 16, 163, '2026-000136', 'unit', 'Filing Cabinet', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(17, 2, 17, 164, '2026-000137', 'unit', 'Projector', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(18, 2, 18, 165, '2026-000138', 'unit', '1.5L electric kettle', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(19, 2, 19, 166, '2026-000139', 'unit', '1.5L electric kettle', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(20, 2, 20, 167, '2026-000140', 'unit', '1.5L electric kettle', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(21, 2, 21, 168, '2026-000141', 'unit', '1.5L electric kettle', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(22, 2, 22, 169, '2026-000142', 'unit', '1.5L electric kettle', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(23, 2, 23, 170, '2026-000143', 'unit', '1.5L electric kettle', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(24, 2, 24, 171, '2026-000144', 'unit', '1.5L electric kettle', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(25, 2, 25, 172, '2026-000145', 'unit', '1.5L electric kettle', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(26, 2, 26, 173, '2026-000146', 'unit', '1.5L electric kettle', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(27, 2, 27, 174, '2026-000147', 'unit', '1.5L electric kettle', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(28, 2, 28, 175, '2026-000148', 'unit', 'Portable wireless speaker', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(29, 2, 29, 176, '2026-000149', 'unit', 'Portable wireless speaker', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(30, 2, 30, 177, '2026-000150', 'unit', 'Portable wireless speaker', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(31, 2, 31, 178, '2026-000151', 'unit', 'Portable wireless speaker', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(32, 2, 32, 179, '2026-000152', 'unit', '6-outlet surge protector', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(33, 2, 33, 180, '2026-000153', 'unit', '6-outlet surge protector', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(34, 2, 34, 181, '2026-000154', 'unit', '6-outlet surge protector', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(35, 2, 35, 182, '2026-000155', 'unit', '6-outlet surge protector', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(36, 2, 36, 183, '2026-000156', 'unit', '6-outlet surge protector', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL),
(37, 2, 37, 184, '2026-000157', 'unit', '6-outlet surge protector', 1, 1, 1, 'Issued in full', '2026-04-29 04:47:24', '2026-04-29 04:47:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rpc_inventory_cycles`
--

CREATE TABLE `rpc_inventory_cycles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cycle_label` varchar(50) NOT NULL,
  `start_year` smallint(5) UNSIGNED NOT NULL,
  `end_year` smallint(5) UNSIGNED NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `started_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `created_by_user_id` int(11) NOT NULL,
  `updated_by_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpc_inventory_cycles`
--

INSERT INTO `rpc_inventory_cycles` (`id`, `cycle_label`, `start_year`, `end_year`, `status`, `started_at`, `closed_at`, `created_by_user_id`, `updated_by_user_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Inventory Cycle 2026-2027', 2026, 2027, 'active', '2026-04-19 16:05:57', NULL, 1, NULL, '2026-04-19 08:05:57', '2026-04-19 08:05:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rpc_inventory_scans`
--

CREATE TABLE `rpc_inventory_scans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rpc_inventory_cycle_id` bigint(20) UNSIGNED NOT NULL,
  `inventory_detail_id` bigint(20) UNSIGNED NOT NULL,
  `property_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rpc_type` varchar(10) NOT NULL,
  `property_classification_id` int(10) UNSIGNED DEFAULT NULL,
  `condition_id` int(11) NOT NULL,
  `scanned_at` datetime NOT NULL,
  `scanned_by_user_id` int(11) NOT NULL,
  `end_user_id` int(10) UNSIGNED DEFAULT NULL,
  `department_id` int(10) UNSIGNED DEFAULT NULL,
  `article` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `property_number` varchar(100) DEFAULT NULL,
  `unit_of_measure` varchar(50) DEFAULT NULL,
  `unit_value` decimal(15,2) DEFAULT NULL,
  `quantity_per_card` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `quantity_per_count` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `remarks` varchar(255) DEFAULT NULL,
  `accountable_officer` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpc_inventory_scans`
--

INSERT INTO `rpc_inventory_scans` (`id`, `rpc_inventory_cycle_id`, `inventory_detail_id`, `property_id`, `rpc_type`, `property_classification_id`, `condition_id`, `scanned_at`, `scanned_by_user_id`, `end_user_id`, `department_id`, `article`, `description`, `property_number`, `unit_of_measure`, `unit_value`, `quantity_per_card`, `quantity_per_count`, `remarks`, `accountable_officer`, `created_at`, `updated_at`) VALUES
(3, 1, 14, 0, 'RPCPPE', 2, 1, '2026-04-19 18:02:09', 1, NULL, 23, 'Server Equipment', 'Server Equipment (PowerEdge-R250)', '2026-000010', 'unit', 165000.00, 1, 1, NULL, 'Cyron Micarte', '2026-04-19 10:02:09', '2026-04-19 10:02:09'),
(5, 1, 15, 0, 'RPCPPE', 2, 1, '2026-04-19 21:08:39', 1, NULL, 23, 'Printer/Scanner', 'Printer/Scanner (MF273dw)', '2026-000011', 'unit', 17200.00, 1, 1, NULL, 'Cyron Micarte', '2026-04-19 13:08:39', '2026-04-19 13:08:39'),
(6, 1, 12, 29, 'RPCPPE', 2, 1, '2026-04-20 13:28:25', 1, 2, 30, 'Telephone Set', 'Telephone Set (KX-TS880)', '2026-000008', 'unit', 2350.00, 1, 1, NULL, 'Chokoy Covid', '2026-04-20 05:28:25', '2026-04-20 05:28:25');

-- --------------------------------------------------------

--
-- Table structure for table `rsmi_header`
--

CREATE TABLE `rsmi_header` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `report_date` date NOT NULL,
  `serial_no` varchar(20) NOT NULL,
  `serial_year` smallint(5) UNSIGNED NOT NULL,
  `serial_month` tinyint(3) UNSIGNED NOT NULL,
  `serial_sequence` int(10) UNSIGNED NOT NULL,
  `entity_name` varchar(255) NOT NULL,
  `fund_cluster` varchar(50) DEFAULT NULL,
  `created_by_user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rsmi_header`
--

INSERT INTO `rsmi_header` (`id`, `report_date`, `serial_no`, `serial_year`, `serial_month`, `serial_sequence`, `entity_name`, `fund_cluster`, `created_by_user_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '2026-03-18', '0001-03-2026', 2026, 3, 1, 'CITY GOVERNMENT OF EL SALVADOR', '', 1, '2026-04-14 09:04:25', '2026-04-14 09:04:25', NULL),
(2, '2026-03-22', '0002-03-2026', 2026, 3, 2, 'CITY GOVERNMENT OF EL SALVADOR', '', 1, '2026-04-14 09:04:25', '2026-04-14 09:04:25', NULL),
(3, '2026-03-24', '0003-03-2026', 2026, 3, 3, 'CITY GOVERNMENT OF EL SALVADOR', '', 1, '2026-04-14 09:04:26', '2026-04-14 09:04:26', NULL),
(4, '2026-03-29', '0004-03-2026', 2026, 3, 4, 'CITY GOVERNMENT OF EL SALVADOR', '', 1, '2026-04-14 09:04:26', '2026-04-14 09:04:26', NULL),
(5, '2026-04-20', '0001-04-2026', 2026, 4, 1, 'CITY GOVERNMENT OF EL SALVADOR', '', 1, '2026-04-20 04:12:27', '2026-04-20 04:12:27', NULL),
(6, '2026-04-08', '0002-04-2026', 2026, 4, 2, 'CITY GOVERNMENT OF EL SALVADOR', '', 1, '2026-04-27 16:41:28', '2026-04-27 16:41:28', NULL),
(7, '2026-04-29', '0003-04-2026', 2026, 4, 3, 'CITY GOVERNMENT OF EL SALVADOR', '', 1, '2026-04-29 04:47:38', '2026-04-29 04:47:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rsmi_items`
--

CREATE TABLE `rsmi_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rsmi_header_id` bigint(20) UNSIGNED NOT NULL,
  `ris_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ris_no` varchar(20) DEFAULT NULL,
  `responsibility_center_code` varchar(50) DEFAULT NULL,
  `stock_no` varchar(80) NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `unit` varchar(50) NOT NULL DEFAULT 'N/A',
  `quantity_issued` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rsmi_items`
--

INSERT INTO `rsmi_items` (`id`, `rsmi_header_id`, `ris_id`, `ris_no`, `responsibility_center_code`, `stock_no`, `item_description`, `unit`, `quantity_issued`, `created_at`, `updated_at`) VALUES
(1690, 3, NULL, 'N/A', '', '2026-000021', 'Laptop Computer', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1691, 3, NULL, 'N/A', '', '2026-000022', 'Office Lamp', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1692, 3, NULL, 'N/A', '', '2026-000023', 'Extension Cord', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1693, 3, NULL, 'N/A', '', '2026-000024', 'Hand Tools Set', 'set', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1694, 3, NULL, 'N/A', '', '2026-000025', 'Waste Basket', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1695, 3, NULL, 'N/A', '', '2026-000026', 'Projector', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1696, 3, NULL, 'N/A', '', '2026-000041', 'Office Lamp', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1697, 3, NULL, 'N/A', '', '2026-000042', 'Extension Cord', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1698, 3, NULL, 'N/A', '', '2026-000043', 'Hand Tools Set', 'set', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1699, 3, NULL, 'N/A', '', '2026-000044', 'Waste Basket', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1700, 3, NULL, 'N/A', '', '2026-000056', 'Office Lamp', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1701, 3, NULL, 'N/A', '', '2026-000057', 'Extension Cord', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1702, 3, NULL, 'N/A', '', '2026-000058', 'Hand Tools Set', 'set', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1703, 3, NULL, 'N/A', '', '2026-000059', 'Waste Basket', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1704, 3, NULL, 'N/A', '', '2026-000069', 'Office Lamp', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1705, 3, NULL, 'N/A', '', '2026-000070', 'Extension Cord', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1706, 3, NULL, 'N/A', '', '2026-000071', 'Waste Basket', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1707, 3, NULL, 'N/A', '', '2026-000077', 'Office Lamp', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1708, 3, NULL, 'N/A', '', '2026-000078', 'Extension Cord', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1709, 3, NULL, 'N/A', '', '2026-000079', 'Waste Basket', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1710, 3, NULL, 'N/A', '', '2026-000084', 'Office Lamp', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1711, 3, NULL, 'N/A', '', '2026-000085', 'Extension Cord', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1712, 3, NULL, 'N/A', '', '2026-000086', 'Waste Basket', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1713, 3, NULL, 'N/A', '', '2026-000089', 'Office Lamp', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1714, 3, NULL, 'N/A', '', '2026-000090', 'Extension Cord', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1715, 3, NULL, 'N/A', '', '2026-000091', 'Waste Basket', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1716, 3, NULL, 'N/A', '', '2026-000094', 'Office Lamp', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1717, 3, NULL, 'N/A', '', '2026-000095', 'Extension Cord', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1718, 3, NULL, 'N/A', '', '2026-000096', 'Waste Basket', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1719, 3, NULL, 'N/A', '', '2026-000098', 'Office Lamp', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1720, 3, NULL, 'N/A', '', '2026-000099', 'Extension Cord', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1721, 3, NULL, 'N/A', '', '2026-000100', 'Waste Basket', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1722, 3, NULL, 'N/A', '', '2026-000102', 'Office Lamp', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1723, 3, NULL, 'N/A', '', '2026-000103', 'Extension Cord', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1724, 3, NULL, 'N/A', '', '2026-000104', 'Waste Basket', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1725, 3, NULL, 'N/A', '', '2026-000105', 'Office Lamp', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1726, 3, NULL, 'N/A', '', '2026-000106', 'Office Lamp', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1727, 3, NULL, 'N/A', '', '2026-000110', 'Laptop Computer', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1728, 3, NULL, 'N/A', '', '2026-000111', 'Laptop Computer', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1729, 4, NULL, 'N/A', '', '2026-000112', 'Desktop Computer', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1730, 4, NULL, 'N/A', '', '2026-000113', 'Office Chair', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1731, 4, NULL, 'N/A', '', '2026-000114', 'Office Chair', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1732, 4, NULL, 'N/A', '', '2026-000115', 'Office Chair', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1733, 4, NULL, 'N/A', '', '2026-000116', 'Office Chair', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1734, 4, NULL, 'N/A', '', '2026-000117', 'Printer/Scanner', 'unit', 1, '2026-04-22 13:27:55', '2026-04-22 13:27:55'),
(1906, 2, NULL, 'N/A', '', '2026-000003', 'Office Desk', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1907, 2, NULL, 'N/A', '', '2026-000004', 'Office Chair', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1908, 2, NULL, 'N/A', '', '2026-000005', 'Filing Cabinet', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1909, 2, NULL, 'N/A', '', '2026-000006', 'Desktop Computer', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1910, 2, NULL, 'N/A', '', '2026-000007', 'Printer/Scanner', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1911, 2, NULL, 'N/A', '', '2026-000008', 'Telephone Set', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1912, 2, NULL, 'N/A', '', '2026-000027', 'Office Desk', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1913, 2, NULL, 'N/A', '', '2026-000028', 'Office Chair', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1914, 2, NULL, 'N/A', '', '2026-000029', 'Filing Cabinet', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1915, 2, NULL, 'N/A', '', '2026-000030', 'Desktop Computer', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1916, 2, NULL, 'N/A', '', '2026-000031', 'Printer/Scanner', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1917, 2, NULL, 'N/A', '', '2026-000045', 'Office Desk', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1918, 2, NULL, 'N/A', '', '2026-000046', 'Office Chair', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1919, 2, NULL, 'N/A', '', '2026-000047', 'Desktop Computer', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1920, 2, NULL, 'N/A', '', '2026-000060', 'Office Desk', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1921, 2, NULL, 'N/A', '', '2026-000061', 'Office Chair', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1922, 2, NULL, 'N/A', '', '2026-000062', 'Desktop Computer', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1923, 2, NULL, 'N/A', '', '2026-000072', 'Office Chair', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1924, 2, NULL, 'N/A', '', '2026-000073', 'Desktop Computer', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1925, 2, NULL, 'N/A', '', '2026-000080', 'Office Chair', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1926, 2, NULL, 'N/A', '', '2026-000081', 'Desktop Computer', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1927, 2, NULL, 'N/A', '', '2026-000087', 'Office Chair', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1928, 2, NULL, 'N/A', '', '2026-000092', 'Office Chair', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1929, 2, NULL, 'N/A', '', '2026-000107', 'Calculator', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1930, 2, NULL, 'N/A', '', '2026-000108', 'Calculator', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1931, 2, NULL, 'N/A', '', '2026-000109', 'Calculator', 'unit', 1, '2026-04-22 13:36:22', '2026-04-22 13:36:22'),
(1932, 1, NULL, 'N/A', '', '2026-000001', 'smsmsmsm', 'unit', 1, '2026-04-22 13:36:23', '2026-04-22 13:36:23'),
(1933, 1, NULL, 'N/A', '', '2026-000002', 'bugnaw', 'unit', 1, '2026-04-22 13:36:23', '2026-04-22 13:36:23'),
(3802, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000134', 'Laptop Computer', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3803, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000135', 'Filing Cabinet', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3804, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000136', 'Filing Cabinet', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3805, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000137', 'Projector', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3806, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000138', '1.5L electric kettle', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3807, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000139', '1.5L electric kettle', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3808, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000140', '1.5L electric kettle', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3809, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000141', '1.5L electric kettle', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3810, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000142', '1.5L electric kettle', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3811, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000143', '1.5L electric kettle', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3812, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000144', '1.5L electric kettle', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3813, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000145', '1.5L electric kettle', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3814, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000146', '1.5L electric kettle', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3815, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000147', '1.5L electric kettle', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3816, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000148', 'Portable wireless speaker', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3817, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000149', 'Portable wireless speaker', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3818, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000150', 'Portable wireless speaker', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3819, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000151', 'Portable wireless speaker', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3820, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000152', '6-outlet surge protector', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3821, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000153', '6-outlet surge protector', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3822, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000154', '6-outlet surge protector', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3823, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000155', '6-outlet surge protector', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3824, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000156', '6-outlet surge protector', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3825, 7, 2, '2026-04-29-2026', 'BJMP', '2026-000157', '6-outlet surge protector', 'unit', 1, '2026-04-29 06:18:45', '2026-04-29 06:18:45'),
(3826, 5, NULL, 'N/A', '', '2026-000118', 'Air Conditioner', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3827, 5, NULL, 'N/A', '', '2026-000119', 'Calculator', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3828, 5, NULL, 'N/A', '', '2026-000120', 'Air Conditioner', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3829, 5, NULL, 'N/A', '', '2026-000180', 'Office Desk', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3830, 5, NULL, 'N/A', '', '2026-000181', 'Office Desk', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3831, 5, NULL, 'N/A', '', '2026-000182', 'Office Desk', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3832, 5, NULL, 'N/A', '', '2026-000183', 'Office Chair', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3833, 5, NULL, 'N/A', '', '2026-000184', 'Office Chair', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3834, 5, NULL, 'N/A', '', '2026-000185', 'Office Chair', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3835, 5, NULL, 'N/A', '', '2026-000186', 'Office Chair', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3836, 5, NULL, 'N/A', '', '2026-000187', 'Office Chair', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3837, 5, NULL, 'N/A', '', '2026-000188', 'Office Chair', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3838, 5, NULL, 'N/A', '', '2026-000189', 'Air Conditioner', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3839, 5, NULL, 'N/A', '', '2026-000190', 'Fire Extinguisher', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3840, 5, NULL, 'N/A', '', '2026-000191', 'Fire Extinguisher', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3841, 5, NULL, 'N/A', '', '2026-000192', 'Fire Extinguisher', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3842, 5, NULL, 'N/A', '', '2026-000193', 'Fire Extinguisher', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3843, 5, NULL, 'N/A', '', '2026-000194', 'Fire Extinguisher', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3844, 5, NULL, 'N/A', '', '2026-000195', 'Fire Extinguisher', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3845, 5, NULL, 'N/A', '', '2026-000196', 'Fire Extinguisher', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3846, 5, NULL, 'N/A', '', '2026-000197', 'Fire Extinguisher', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3847, 5, NULL, 'N/A', '', '2026-000198', 'Fire Extinguisher', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3848, 5, NULL, 'N/A', '', '2026-000199', 'Fire Extinguisher', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3849, 5, NULL, 'N/A', '', '2026-000200', 'Fire Extinguisher', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3850, 5, NULL, 'N/A', '', '2026-000201', 'Fire Extinguisher', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3851, 5, NULL, 'N/A', '', '2026-000202', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3852, 5, NULL, 'N/A', '', '2026-000203', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3853, 5, NULL, 'N/A', '', '2026-000204', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3854, 5, NULL, 'N/A', '', '2026-000205', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3855, 5, NULL, 'N/A', '', '2026-000206', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3856, 5, NULL, 'N/A', '', '2026-000207', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3857, 5, NULL, 'N/A', '', '2026-000208', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3858, 5, NULL, 'N/A', '', '2026-000209', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3859, 5, NULL, 'N/A', '', '2026-000210', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3860, 5, NULL, 'N/A', '', '2026-000211', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3861, 5, NULL, 'N/A', '', '2026-000212', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3862, 5, NULL, 'N/A', '', '2026-000213', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3863, 5, NULL, 'N/A', '', '2026-000214', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3864, 5, NULL, 'N/A', '', '2026-000215', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3865, 5, NULL, 'N/A', '', '2026-000216', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3866, 5, NULL, 'N/A', '', '2026-000217', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3867, 5, NULL, 'N/A', '', '2026-000218', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3868, 5, NULL, 'N/A', '', '2026-000219', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3869, 5, NULL, 'N/A', '', '2026-000220', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3870, 5, NULL, 'N/A', '', '2026-000221', 'Cleaning Kit', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3871, 5, NULL, 'N/A', '', '2026-000222', 'LAN Cable Set', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3872, 5, NULL, 'N/A', '', '2026-000223', 'LAN Cable Set', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3873, 5, NULL, 'N/A', '', '2026-000224', 'LAN Cable Set', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3874, 5, NULL, 'N/A', '', '2026-000225', 'LAN Cable Set', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3875, 5, NULL, 'N/A', '', '2026-000226', 'LAN Cable Set', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3876, 6, 1, '2026-04-0001', 'BJMP', '2026-000121', 'Desktop Computer', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3877, 6, 1, '2026-04-0001', 'BJMP', '2026-000122', 'Printer/Scanner', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3878, 6, 1, '2026-04-0001', 'BJMP', '2026-000123', 'Office Chair', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3879, 6, 1, '2026-04-0001', 'BJMP', '2026-000124', 'Portable rechargeable emergency lamp', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3880, 6, 1, '2026-04-0001', 'BJMP', '2026-000125', 'Portable rechargeable emergency lamp', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3881, 6, 1, '2026-04-0001', 'BJMP', '2026-000126', 'Portable rechargeable emergency lamp', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3882, 6, 1, '2026-04-0001', 'BJMP', '2026-000127', '32GB USB 3.0 flash drive', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3883, 6, 1, '2026-04-0001', 'BJMP', '2026-000128', '32GB USB 3.0 flash drive', 'unit', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3884, 6, 1, '2026-04-0001', 'BJMP', '2026-000129', 'Wireless keyboard and mouse set', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3885, 6, 1, '2026-04-0001', 'BJMP', '2026-000130', 'Wireless keyboard and mouse set', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3886, 6, 1, '2026-04-0001', 'BJMP', '2026-000131', 'Wireless keyboard and mouse set', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3887, 6, 1, '2026-04-0001', 'BJMP', '2026-000132', 'Wireless keyboard and mouse set', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47'),
(3888, 6, 1, '2026-04-0001', 'BJMP', '2026-000133', 'Wireless keyboard and mouse set', 'set', 1, '2026-04-29 06:18:47', '2026-04-29 06:18:47');

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
('WufvPNMS6Bq8RRWUWISDpeVZRTuez9NX1MV7dUWB', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUThHMERJdEtsWTJCZ1ZTdWRXZ1haQjZRSm5GQUxlMkFDZEFWZFlRSCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hcGkvbm90aWZpY2F0aW9ucz9wZXJfcGFnZT0xMDAiO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRmdGdHUmhKT1BvLjVGV215YmVMTHYuWjh3WDgvSC5CaWZaTlpzSzUxdVFJc0pIVUIvay5oNiI7fQ==', 1777532029);

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
(7, 2, 'Chrome on Windows', '5da659491728cf8cce8b7047ae8042604fa041012793d906516d7da35c598b76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '::1', NULL, '2026-01-25 15:40:17', '2025-12-26 15:40:17', NULL),
(16, 2, 'Chrome on Windows', 'c6ba2be624d0c8d1fbd27bb47776e15e631a2c05adb6de82b08e62ab3d5ba446', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-01-25 22:37:45', '2025-12-26 22:37:45', NULL),
(58, 5, 'Trusted Device', 'a1e9e29d449dca75b2cd9e4501485744923a5ae5f736e349a20344e7ba8b4fe8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '127.0.0.1', NULL, '2026-02-13 01:57:07', '2026-01-14 01:57:07', NULL),
(62, 5, 'Trusted Device', 'e51948396fbedb111750410a126b66113dc57fc9becd9cbaad47c8d3192d09c2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-02-14 13:48:52', '2026-01-15 13:48:52', NULL),
(63, 1, 'Trusted Device', 'b6c42f2eda845d004d7deb90648db69686d53f5e8bf13d9a23bcc16527ae91ad', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '127.0.0.1', '2026-02-13 22:53:24', '2026-02-14 13:49:39', '2026-01-15 13:49:39', '2026-02-13 22:53:24'),
(72, 7, 'Trusted Device', '642de790709ab5971be725309f5f512eaba73a0ba03652b2273ad5afa1a101ca', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '127.0.0.1', '2026-02-15 17:27:48', '2026-03-16 03:06:09', '2026-02-14 03:06:09', '2026-02-15 17:27:48'),
(75, 7, 'Trusted Device', '893427bf27a08ffa4fb82a1cda655b1480802abcbd45f4f1984c1fda7378771d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '127.0.0.1', NULL, '2026-03-18 21:45:14', '2026-02-16 21:45:14', NULL),
(82, 1, 'Trusted Device', 'aaa20777586ad3f1fd3ccf8af7303b0edbb80624e9e90f7a2e63950d16730fa6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '127.0.0.1', '2026-02-18 21:47:15', '2026-03-19 21:32:15', '2026-02-17 21:32:15', '2026-02-18 21:47:15'),
(86, 1, 'Trusted Device', '92b86588f1a81d8f632b3484925ab65694afa1335866b980e6f6e563d670ed4d', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '127.0.0.1', '2026-03-18 17:45:44', '2026-04-02 22:47:37', '2026-03-03 22:47:37', '2026-03-18 17:45:44'),
(88, 11, 'Trusted Device', '3f2dd3dd519fead880cf455caaee905b837d58dec6dcb3284c2b211a80cd1ec2', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '127.0.0.1', NULL, '2026-04-13 12:50:12', '2026-03-14 12:50:12', NULL),
(100, 1, 'Trusted Device', '340e489e56f08186ec207bbf385c0f7abdea8556f449f7479e1d971b5dd79b86', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '127.0.0.1', '2026-04-24 14:12:54', '2026-04-24 22:16:17', '2026-03-22 18:08:24', '2026-04-24 14:12:54'),
(102, 3, 'Trusted Device', '4ec51ff6aaf34553b731f25b7793edea9310c4ad99355860f569aae99b93bb91', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', '127.0.0.1', '2026-04-27 23:33:59', '2026-05-05 01:05:54', '2026-04-05 01:05:54', '2026-04-27 23:33:59'),
(103, 1, 'Trusted Device', 'fce22b5d6579263504c6cec01d2e1b1f0cf39d115a6c0ac927c65aa16ef1cc45', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '127.0.0.1', '2026-04-30 14:18:49', '2026-05-25 19:51:21', '2026-04-25 19:51:21', '2026-04-30 14:18:49');

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
(1, 1, 49, 'gso.admin', 'micarte.aaron08@gmail.com', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$ftgGRhJOPo.5FWmybeLLv.Z8wX8/H.BifZNZsK51uQIsJHUB/k.h6', 0, 'Nilo Sabayanon', 1, 1, 'eyJpdiI6ImJvcFBFdSttOHlXRGNGeVN1MytvQ2c9PSIsInZhbHVlIjoiaE56YzA0VTE1YnhwNlp0YldLY0ZYYmpHUERZa0dJb2wvcm1Id3lndFRlVT0iLCJtYWMiOiI2OGJhYjIzYTNlZDA5NDI4NjQ4MDAxMjg4NmE2OTMxZmFkNTVkNjI3ZDc2N2Y0MjY0MzQxMWM4MjZlOGZkMTM1IiwidGFnIjoiIn0=', 'eyJpdiI6Im9iWlVLSmo4ZEhkWXBSVG04MzlnakE9PSIsInZhbHVlIjoiZzNtRjlLNEhVS3FJd0VVUTd4cVNXa1BLVUlPeG9CYnMvWmZULyt1R001MVBTTzhTaE5DM2tFamNQblFkdStTL2ZUc1JLY3dEWmoyVENxdmVFQ3ZCdkcyMENaVHMxM3lUWWg0UFcwMUdBa2ovQktFeFREZFFnRkJlY1hwbENkeHpoeXZSQmljUGxYdnJ6RGFBU3RHcHE4bTREcmEzTDM5d0JkWGs5VUR3bGNjPSIsIm1hYyI6IjkxZGY5OTAyZGU1NDY4YzgxNjg5ZWM3OTg1MWZhOGE3NmE1MTE4YzNkZTZjZjFjNjI3MjllZGQ1MDI4NzJiMjkiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-04-27 23:29:36'),
(2, 2, 49, 'gso.staff.aaronmicarte', 'aaron.micarte@gso.gov.ph', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$YU66h83wjMCOJ9LMN81o8OuWpIuFYTFQpHCxkuWDAurXbYJS9Q.Mu', 0, 'Aaron Micarte', 1, 0, 'eyJpdiI6IjlHZGd3c0dKcmxwcnBLNEdhN2pkWVE9PSIsInZhbHVlIjoiRkd3RWh6SUczdmt3NnpVU0lhUnVGazNSSmdCYkU3OCtLZzA3T1RPZW1BST0iLCJtYWMiOiI3NjliN2M0MDBjZDE4N2YyODcxMWVkMjNjODdiMmVlMDcwZDEwYWIzYTk3NzFhMDVkMjY1MTQzMDM4Y2Y0OThkIiwidGFnIjoiIn0=', 'eyJpdiI6InRLc2pkQjlRVHpZd2ZzKzZ3c0RCRmc9PSIsInZhbHVlIjoiZUlXMnFlWFZ0dkNtZmUxMVVON0VUa3lXOG1EWDNmVjdrRFUxN09TUElBR2Q1K1NNeEF1WHJjTFMyN3dNbG01alUxMGN1b0l4cE5qQmRPL1BUNGxPRU00VGFOaFVtNEd2SWpJTnlEYmhLbTFrVEk0NU51UDY1S2Z4K2RCOHhxbGVyZnkyU1lXbkp2QXFPNlZWNCtwZ0lndHVyd2ljeW4wSUdObktkajJuVkpnPSIsIm1hYyI6ImU0NDM1MWY0YzZkYTYyZmMwY2QzYWM1M2ZiYmMwZTE4MzY0NzM2OWU0YzU2YTg3ODljOWY1NzhiNWVkNWEzNWQiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-03-19 10:00:47'),
(3, 4, 30, 'BJMP@Tyron', 'custodian@gso.gov.ph', '2025-12-05 02:07:29', NULL, NULL, NULL, '$2y$12$O5.33/PqZ19/QvReS4c2YenCpG8LSbDqnk/1voMGXBc6StDYf0ly2', 0, 'Chokoy Covid', 1, 1, 'eyJpdiI6ImFvVEpuaEFUclVHYXRVSEJmV3NsYWc9PSIsInZhbHVlIjoibDM5NXorMGlPTkVqUHk1YXN0OXgzM3BWd0FoT3BkYVN0ZkdwRzkxRk9yaz0iLCJtYWMiOiJhOWI4MGQzMjUyZGMyYmJhYTNiNjI3NjVjYWI5NGYyNGIyYjUwNmFkNmM1NjAzZGM5MTE3YTIzOWVkMGQ2NDg5IiwidGFnIjoiIn0=', 'eyJpdiI6Ik1mTHF2UjNtNFlyRldRTzcwbDF5S0E9PSIsInZhbHVlIjoiNjcwdVQxeTJtNmNwOUt4Wkd5RVN1alhjcGZ5R0w5Z2g4clI4L3BvRklUbERXaGFEY1MxMHMzaGMwRGVjVmhtbXpKT1AxSlUxeUlMam9PUWRUeFBNcnpJdytsUmFubHpiSExFWEJKOXErK1dLUEtLWFZ0MDVJQ2RPYU11MHorUmRxTS8zRlpmRythNkJlUmh3ZmZUYkNScXBxRlV0YmUwN3NyUjRkQUNCazMwPSIsIm1hYyI6ImJmN2NlN2EwODAzMDNlZTEwYWZkOGRkNjQwNzYwZTE1NzY3MDEyZGMwMTZjMDRjMWQ4NzcwMzY4ZmJiYTU3NTgiLCJ0YWciOiIifQ==', '2025-12-05 02:07:29', '2026-04-20 11:55:52'),
(5, 4, 87, 'HR_Head_Tyron', 'micarte2005@gmail.com', '2026-01-14 01:50:24', NULL, NULL, NULL, '$2y$12$UjxLHSUjJQBOZYHqrB7ajuK2rIiZ1P9962RTMFifYWMoF.fH7o3Wq', 0, 'Tyron Mecarte', 1, 1, 'eyJpdiI6Ilo3U0pYYVBOd1RFZEtTbmxxUlRXUWc9PSIsInZhbHVlIjoiYXlZTWlzemI3TlFZeWI4MUpuNC9wdEpFelN0emhnbFVWL1g2Ky9lMTFOTT0iLCJtYWMiOiIwZjY3ZjA3OTUwY2M1NGQzOTczMmNhZjA1NzI5NzU4ZDQ0MGEzNDU1NTZkMzEzMmQxYzRjZmI4NzcwYzQ0YjdkIiwidGFnIjoiIn0=', 'eyJpdiI6InplOXhDZXZEeHR1QkF6WEpGYzRjdEE9PSIsInZhbHVlIjoiRGh6cXVuaHRQRDU1aGxGd2hDZGVLdmJkcWIybDNnWkpXaXF5TkdobmpzOS9iYzlNRDNUcldyQ0VZSlR2UDlDd3JvZ1lUVjVIWWJBTWJENmRKZHhva2FNZzJOc3ZaZkordUhzTkJnbkhuNEdYWnoxWDRtVkNZVGFHZkVhb2VLcHdDWVJtYzVZYno2SmJJS1h6YytiRVMwNDZBcVN0TnR4aDBJckRPSThOMmQ4PSIsIm1hYyI6ImExZDNmMzBiN2VjNzJjNzg5ZDEyYjAxMTRmNWJmZjcwZTJjYzhkMWUwNjA0OGU3MDkxNDk5NTlkYzc2NmI2YmMiLCJ0YWciOiIifQ==', '2026-01-14 01:00:26', '2026-04-05 00:59:51'),
(6, 4, 23, 'FD_Head_Cyron', 'aaya.micarte.coc@phinmaed.com', '2026-01-15 14:01:41', NULL, NULL, NULL, '$2y$12$A/XCqp8lXrNCeYvQ6lOyUu6DXxbXtJ3Z7FJ.5q39q9yASzA1EYIKS', 0, 'Cyron Micarte', 1, 0, NULL, NULL, '2026-01-15 14:00:32', '2026-03-22 20:58:51'),
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
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `UserNotificationID` bigint(20) UNSIGNED NOT NULL,
  `UserID` int(11) NOT NULL,
  `event_type` varchar(80) NOT NULL,
  `title` varchar(160) NOT NULL,
  `message` text NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_notifications`
--

INSERT INTO `user_notifications` (`UserNotificationID`, `UserID`, `event_type`, `title`, `message`, `payload`, `status`, `error_message`, `sent_at`, `read_at`, `created_at`, `updated_at`) VALUES
(6, 6, 'delivery_receipt.acknowledged', 'Delivery Receipt Acknowledged', 'Delivery receipt DR-2026-03-18-004 has been acknowledged and inventory is ready.', '\"{\\\"dr_id\\\":\\\"32\\\",\\\"dr_number\\\":\\\"DR-2026-03-18-004\\\",\\\"status\\\":\\\"acknowledged\\\",\\\"link\\\":\\\"\\/department_head\\/delivery-receipts\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-03-24 03:55:01', '2026-03-24 03:55:01'),
(10, 1, 'property_transfer.created', 'New Property Transfer Request', 'Transfer PTR-2026-0006 was submitted by Chokoy Covid.', '\"{\\\"transfer_id\\\":\\\"6\\\",\\\"transfer_number\\\":\\\"PTR-2026-0006\\\",\\\"status\\\":\\\"pending\\\",\\\"link\\\":\\\"\\/property-transfer\\/6\\\"}\"', 'sent', NULL, '2026-04-03 17:05:53', NULL, '2026-04-03 17:05:47', '2026-04-03 17:05:53'),
(11, 2, 'property_transfer.created', 'New Property Transfer Request', 'Transfer PTR-2026-0006 was submitted by Chokoy Covid.', '\"{\\\"transfer_id\\\":\\\"6\\\",\\\"transfer_number\\\":\\\"PTR-2026-0006\\\",\\\"status\\\":\\\"pending\\\",\\\"link\\\":\\\"\\/property-transfer\\/6\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:05:53', '2026-04-03 17:05:53'),
(12, 6, 'property_transfer.created', 'New Property Transfer Request', 'Transfer PTR-2026-0006 was submitted by Chokoy Covid.', '\"{\\\"transfer_id\\\":\\\"6\\\",\\\"transfer_number\\\":\\\"PTR-2026-0006\\\",\\\"status\\\":\\\"pending\\\",\\\"link\\\":\\\"\\/property-transfer\\/6\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:05:53', '2026-04-03 17:05:53'),
(13, 2, 'property_transfer.approved', 'Property Transfer Approved', 'Transfer PTR-2026-0006 was approved by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"6\\\",\\\"transfer_number\\\":\\\"PTR-2026-0006\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/6\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:06:39', '2026-04-03 17:06:39'),
(15, 6, 'property_transfer.approved', 'Property Transfer Approved', 'Transfer PTR-2026-0006 was approved by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"6\\\",\\\"transfer_number\\\":\\\"PTR-2026-0006\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/6\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:06:40', '2026-04-03 17:06:40'),
(17, 1, 'property_transfer.releasing_approved', 'Transfer Cleared By Releasing Department', 'Transfer PTR-2026-0006 was approved by the releasing department.', '\"{\\\"transfer_id\\\":\\\"6\\\",\\\"transfer_number\\\":\\\"PTR-2026-0006\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/6\\\"}\"', 'sent', NULL, '2026-04-03 17:08:30', NULL, '2026-04-03 17:08:28', '2026-04-03 17:08:30'),
(18, 2, 'property_transfer.releasing_approved', 'Transfer Cleared By Releasing Department', 'Transfer PTR-2026-0006 was approved by the releasing department.', '\"{\\\"transfer_id\\\":\\\"6\\\",\\\"transfer_number\\\":\\\"PTR-2026-0006\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/6\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:08:30', '2026-04-03 17:08:30'),
(21, 2, 'property_transfer.completed', 'Property Transfer Completed', 'Transfer PTR-2026-0006 was finalized by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"6\\\",\\\"transfer_number\\\":\\\"PTR-2026-0006\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/6\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:09:22', '2026-04-03 17:09:22'),
(23, 6, 'property_transfer.completed', 'Property Transfer Completed', 'Transfer PTR-2026-0006 was finalized by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"6\\\",\\\"transfer_number\\\":\\\"PTR-2026-0006\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/6\\\"}\"', 'sent', NULL, '2026-04-03 17:09:24', NULL, '2026-04-03 17:09:22', '2026-04-03 17:09:24'),
(25, 1, 'property_borrow.created', 'New Property Borrow Request', 'Borrow request PBR-2026-0005 was submitted by Chokoy Covid.', '\"{\\\"borrow_id\\\":\\\"5\\\",\\\"borrow_number\\\":\\\"PBR-2026-0005\\\",\\\"status\\\":\\\"pending\\\",\\\"link\\\":\\\"\\/property-borrow\\/5\\\"}\"', 'sent', NULL, '2026-04-03 17:36:37', NULL, '2026-04-03 17:36:35', '2026-04-03 17:36:37'),
(26, 2, 'property_borrow.created', 'New Property Borrow Request', 'Borrow request PBR-2026-0005 was submitted by Chokoy Covid.', '\"{\\\"borrow_id\\\":\\\"5\\\",\\\"borrow_number\\\":\\\"PBR-2026-0005\\\",\\\"status\\\":\\\"pending\\\",\\\"link\\\":\\\"\\/property-borrow\\/5\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:36:37', '2026-04-03 17:36:37'),
(27, 6, 'property_borrow.created', 'New Property Borrow Request', 'Borrow request PBR-2026-0005 was submitted by Chokoy Covid.', '\"{\\\"borrow_id\\\":\\\"5\\\",\\\"borrow_number\\\":\\\"PBR-2026-0005\\\",\\\"status\\\":\\\"pending\\\",\\\"link\\\":\\\"\\/property-borrow\\/5\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:36:37', '2026-04-03 17:36:37'),
(28, 2, 'property_borrow.approved', 'Property Borrow Approved', 'Borrow request PBR-2026-0005 was approved by GSO/Admin and is awaiting releasing review.', '\"{\\\"borrow_id\\\":\\\"5\\\",\\\"borrow_number\\\":\\\"PBR-2026-0005\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/5\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:37:23', '2026-04-03 17:37:23'),
(30, 6, 'property_borrow.approved', 'Property Borrow Approved', 'Borrow request PBR-2026-0005 was approved by GSO/Admin and is awaiting releasing review.', '\"{\\\"borrow_id\\\":\\\"5\\\",\\\"borrow_number\\\":\\\"PBR-2026-0005\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/5\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:37:24', '2026-04-03 17:37:24'),
(32, 1, 'property_borrow.releasing_approved', 'Borrow Request Cleared By Releasing Department', 'Borrow request PBR-2026-0005 was approved by the releasing department.', '\"{\\\"borrow_id\\\":\\\"5\\\",\\\"borrow_number\\\":\\\"PBR-2026-0005\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/5\\\"}\"', 'sent', NULL, '2026-04-03 17:38:25', NULL, '2026-04-03 17:38:24', '2026-04-03 17:38:25'),
(33, 2, 'property_borrow.releasing_approved', 'Borrow Request Cleared By Releasing Department', 'Borrow request PBR-2026-0005 was approved by the releasing department.', '\"{\\\"borrow_id\\\":\\\"5\\\",\\\"borrow_number\\\":\\\"PBR-2026-0005\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/5\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:38:25', '2026-04-03 17:38:25'),
(36, 2, 'property_borrow.borrowed', 'Property Borrow Released', 'Borrow request PBR-2026-0005 was finalized and marked as borrowed.', '\"{\\\"borrow_id\\\":\\\"5\\\",\\\"borrow_number\\\":\\\"PBR-2026-0005\\\",\\\"status\\\":\\\"borrowed\\\",\\\"link\\\":\\\"\\/property-borrow\\/5\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:38:56', '2026-04-03 17:38:56'),
(38, 6, 'property_borrow.borrowed', 'Property Borrow Released', 'Borrow request PBR-2026-0005 was finalized and marked as borrowed.', '\"{\\\"borrow_id\\\":\\\"5\\\",\\\"borrow_number\\\":\\\"PBR-2026-0005\\\",\\\"status\\\":\\\"borrowed\\\",\\\"link\\\":\\\"\\/property-borrow\\/5\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:38:58', '2026-04-03 17:38:58'),
(40, 2, 'property_borrow.returned', 'Borrow Fully Returned', 'Borrow request PBR-2026-0005 has been fully returned.', '\"{\\\"borrow_id\\\":\\\"5\\\",\\\"borrow_number\\\":\\\"PBR-2026-0005\\\",\\\"status\\\":\\\"returned\\\",\\\"link\\\":\\\"\\/property-borrow\\/5\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:40:55', '2026-04-03 17:40:55'),
(42, 6, 'property_borrow.returned', 'Borrow Fully Returned', 'Borrow request PBR-2026-0005 has been fully returned.', '\"{\\\"borrow_id\\\":\\\"5\\\",\\\"borrow_number\\\":\\\"PBR-2026-0005\\\",\\\"status\\\":\\\"returned\\\",\\\"link\\\":\\\"\\/property-borrow\\/5\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-03 17:40:56', '2026-04-03 17:40:56'),
(44, 2, 'asset_service_record.created', 'Asset Service Record Created', 'Service record SVR-2026-0004 has been created.', '\"{\\\"service_record_id\\\":\\\"4\\\",\\\"service_number\\\":\\\"SVR-2026-0004\\\",\\\"status\\\":\\\"pending\\\",\\\"report_source_id\\\":\\\"30\\\",\\\"link\\\":\\\"\\/service-records\\/4\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-05 09:22:05', '2026-04-05 09:22:05'),
(46, 2, 'asset_service_record.updated', 'Asset Service Record Updated', 'Service record SVR-2026-0004 status changed from pending to work_approved.', '\"{\\\"service_record_id\\\":\\\"4\\\",\\\"service_number\\\":\\\"SVR-2026-0004\\\",\\\"old_status\\\":\\\"pending\\\",\\\"status\\\":\\\"work_approved\\\",\\\"report_source_id\\\":\\\"30\\\",\\\"link\\\":\\\"\\/service-records\\/4\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-05 09:22:28', '2026-04-05 09:22:28'),
(48, 2, 'asset_service_record.updated', 'Asset Service Record Updated', 'Service record SVR-2026-0004 status changed from work_approved to repair_in_progress.', '\"{\\\"service_record_id\\\":\\\"4\\\",\\\"service_number\\\":\\\"SVR-2026-0004\\\",\\\"old_status\\\":\\\"work_approved\\\",\\\"status\\\":\\\"repair_in_progress\\\",\\\"report_source_id\\\":\\\"30\\\",\\\"link\\\":\\\"\\/service-records\\/4\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-05 09:22:33', '2026-04-05 09:22:33'),
(50, 2, 'asset_service_record.updated', 'Asset Service Record Updated', 'Service record SVR-2026-0004 status changed from repair_in_progress to verification.', '\"{\\\"service_record_id\\\":\\\"4\\\",\\\"service_number\\\":\\\"SVR-2026-0004\\\",\\\"old_status\\\":\\\"repair_in_progress\\\",\\\"status\\\":\\\"verification\\\",\\\"report_source_id\\\":\\\"30\\\",\\\"link\\\":\\\"\\/service-records\\/4\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-05 09:22:37', '2026-04-05 09:22:37'),
(52, 2, 'asset_service_record.updated', 'Asset Service Record Updated', 'Service record SVR-2026-0004 status changed from verification to unserviceable.', '\"{\\\"service_record_id\\\":\\\"4\\\",\\\"service_number\\\":\\\"SVR-2026-0004\\\",\\\"old_status\\\":\\\"verification\\\",\\\"status\\\":\\\"unserviceable\\\",\\\"report_source_id\\\":\\\"30\\\",\\\"link\\\":\\\"\\/service-records\\/4\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-05 09:22:46', '2026-04-05 09:22:46'),
(54, 2, 'asset_service_record.created', 'Asset Service Record Created', 'Service record SVR-2026-0005 has been created.', '\"{\\\"service_record_id\\\":\\\"5\\\",\\\"service_number\\\":\\\"SVR-2026-0005\\\",\\\"status\\\":\\\"pending\\\",\\\"report_source_id\\\":\\\"30\\\",\\\"link\\\":\\\"\\/service-records\\/5\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-05 10:23:39', '2026-04-05 10:23:39'),
(56, 2, 'asset_service_record.updated', 'Asset Service Record Updated', 'Service record SVR-2026-0005 status changed from pending to work_approved.', '\"{\\\"service_record_id\\\":\\\"5\\\",\\\"service_number\\\":\\\"SVR-2026-0005\\\",\\\"old_status\\\":\\\"pending\\\",\\\"status\\\":\\\"work_approved\\\",\\\"report_source_id\\\":\\\"30\\\",\\\"link\\\":\\\"\\/service-records\\/5\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-05 10:24:02', '2026-04-05 10:24:02'),
(58, 2, 'asset_service_record.updated', 'Asset Service Record Updated', 'Service record SVR-2026-0005 status changed from work_approved to repair_in_progress.', '\"{\\\"service_record_id\\\":\\\"5\\\",\\\"service_number\\\":\\\"SVR-2026-0005\\\",\\\"old_status\\\":\\\"work_approved\\\",\\\"status\\\":\\\"repair_in_progress\\\",\\\"report_source_id\\\":\\\"30\\\",\\\"link\\\":\\\"\\/service-records\\/5\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-05 10:24:08', '2026-04-05 10:24:08'),
(60, 2, 'asset_service_record.updated', 'Asset Service Record Updated', 'Service record SVR-2026-0005 status changed from repair_in_progress to verification.', '\"{\\\"service_record_id\\\":\\\"5\\\",\\\"service_number\\\":\\\"SVR-2026-0005\\\",\\\"old_status\\\":\\\"repair_in_progress\\\",\\\"status\\\":\\\"verification\\\",\\\"report_source_id\\\":\\\"30\\\",\\\"link\\\":\\\"\\/service-records\\/5\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-05 10:24:14', '2026-04-05 10:24:14'),
(62, 2, 'asset_service_record.updated', 'Asset Service Record Updated', 'Service record SVR-2026-0005 status changed from verification to unserviceable.', '\"{\\\"service_record_id\\\":\\\"5\\\",\\\"service_number\\\":\\\"SVR-2026-0005\\\",\\\"old_status\\\":\\\"verification\\\",\\\"status\\\":\\\"unserviceable\\\",\\\"report_source_id\\\":\\\"30\\\",\\\"link\\\":\\\"\\/service-records\\/5\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-05 10:24:24', '2026-04-05 10:24:24'),
(64, 2, 'property_transfer.completed', 'Property Transfer Completed', 'Transfer PTR-2026-0004 was finalized by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"4\\\",\\\"transfer_number\\\":\\\"PTR-2026-0004\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/4\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-20 03:53:09', '2026-04-20 03:53:09'),
(66, 6, 'property_transfer.completed', 'Property Transfer Completed', 'Transfer PTR-2026-0004 was finalized by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"4\\\",\\\"transfer_number\\\":\\\"PTR-2026-0004\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/4\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-20 03:53:11', '2026-04-20 03:53:11'),
(68, 2, 'delivery_receipt.created', 'Delivery Receipt Created', 'Delivery receipt drtest12321 has been created and inspected.', '\"{\\\"dr_id\\\":\\\"86\\\",\\\"dr_number\\\":\\\"drtest12321\\\",\\\"status\\\":\\\"inspected\\\",\\\"link\\\":\\\"\\/procurement\\/delivery-receipts\\/86\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-20 04:05:52', '2026-04-20 04:05:52'),
(72, 2, 'delivery_receipt.created', 'Delivery Receipt Created', 'Delivery receipt dr-malinao has been created and inspected.', '\"{\\\"dr_id\\\":\\\"87\\\",\\\"dr_number\\\":\\\"dr-malinao\\\",\\\"status\\\":\\\"inspected\\\",\\\"link\\\":\\\"\\/procurement\\/delivery-receipts\\/87\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-20 05:18:33', '2026-04-20 05:18:33'),
(77, 2, 'property_borrow.created', 'New Property Borrow Request', 'Borrow request PBR-2026-0006 was submitted by Chokoy Covid.', '\"{\\\"borrow_id\\\":\\\"6\\\",\\\"borrow_number\\\":\\\"PBR-2026-0006\\\",\\\"status\\\":\\\"pending\\\",\\\"link\\\":\\\"\\/property-borrow\\/6\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-20 05:24:30', '2026-04-20 05:24:30'),
(78, 6, 'property_borrow.created', 'New Property Borrow Request', 'Borrow request PBR-2026-0006 was submitted by Chokoy Covid.', '\"{\\\"borrow_id\\\":\\\"6\\\",\\\"borrow_number\\\":\\\"PBR-2026-0006\\\",\\\"status\\\":\\\"pending\\\",\\\"link\\\":\\\"\\/property-borrow\\/6\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-20 05:24:30', '2026-04-20 05:24:30'),
(80, 2, 'property_transfer.created', 'New Property Transfer Request', 'Transfer PTR-2026-0007 was submitted by Chokoy Covid.', '\"{\\\"transfer_id\\\":\\\"7\\\",\\\"transfer_number\\\":\\\"PTR-2026-0007\\\",\\\"status\\\":\\\"pending\\\",\\\"link\\\":\\\"\\/property-transfer\\/7\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-20 05:24:39', '2026-04-20 05:24:39'),
(81, 6, 'property_transfer.created', 'New Property Transfer Request', 'Transfer PTR-2026-0007 was submitted by Chokoy Covid.', '\"{\\\"transfer_id\\\":\\\"7\\\",\\\"transfer_number\\\":\\\"PTR-2026-0007\\\",\\\"status\\\":\\\"pending\\\",\\\"link\\\":\\\"\\/property-transfer\\/7\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-20 05:24:39', '2026-04-20 05:24:39'),
(82, 2, 'property_transfer.approved', 'Property Transfer Approved', 'Transfer PTR-2026-0007 was approved by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"7\\\",\\\"transfer_number\\\":\\\"PTR-2026-0007\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/7\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-20 05:25:24', '2026-04-20 05:25:24'),
(84, 6, 'property_transfer.approved', 'Property Transfer Approved', 'Transfer PTR-2026-0007 was approved by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"7\\\",\\\"transfer_number\\\":\\\"PTR-2026-0007\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/7\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-20 05:25:26', '2026-04-20 05:25:26'),
(87, 2, 'property_transfer.releasing_approved', 'Transfer Cleared By Releasing Department', 'Transfer PTR-2026-0007 was approved by the releasing department.', '\"{\\\"transfer_id\\\":\\\"7\\\",\\\"transfer_number\\\":\\\"PTR-2026-0007\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/7\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-20 05:26:31', '2026-04-20 05:26:31'),
(90, 2, 'property_transfer.completed', 'Property Transfer Completed', 'Transfer PTR-2026-0007 was finalized by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"7\\\",\\\"transfer_number\\\":\\\"PTR-2026-0007\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/7\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-20 05:26:45', '2026-04-20 05:26:45'),
(92, 6, 'property_transfer.completed', 'Property Transfer Completed', 'Transfer PTR-2026-0007 was finalized by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"7\\\",\\\"transfer_number\\\":\\\"PTR-2026-0007\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/7\\\"}\"', 'sent', NULL, '2026-04-20 05:26:46', NULL, '2026-04-20 05:26:45', '2026-04-20 05:26:46'),
(94, 2, 'property_borrow.approved', 'Property Borrow Approved', 'Borrow request PBR-2026-0006 was approved by GSO/Admin and is awaiting releasing review.', '\"{\\\"borrow_id\\\":\\\"6\\\",\\\"borrow_number\\\":\\\"PBR-2026-0006\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/6\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-20 05:27:04', '2026-04-20 05:27:04'),
(96, 6, 'property_borrow.approved', 'Property Borrow Approved', 'Borrow request PBR-2026-0006 was approved by GSO/Admin and is awaiting releasing review.', '\"{\\\"borrow_id\\\":\\\"6\\\",\\\"borrow_number\\\":\\\"PBR-2026-0006\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/6\\\"}\"', 'sent', NULL, '2026-04-20 05:27:09', NULL, '2026-04-20 05:27:04', '2026-04-20 05:27:09'),
(98, 2, 'property_transfer.created', 'New Property Transfer Request', 'Transfer PTR-2026-0008 was submitted by GSO Administrator.', '\"{\\\"transfer_id\\\":\\\"8\\\",\\\"transfer_number\\\":\\\"PTR-2026-0008\\\",\\\"status\\\":\\\"pending\\\",\\\"link\\\":\\\"\\/property-transfer\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-23 07:46:21', '2026-04-23 07:46:21'),
(100, 6, 'property_transfer.created', 'New Property Transfer Request', 'Transfer PTR-2026-0008 was submitted by GSO Administrator.', '\"{\\\"transfer_id\\\":\\\"8\\\",\\\"transfer_number\\\":\\\"PTR-2026-0008\\\",\\\"status\\\":\\\"pending\\\",\\\"link\\\":\\\"\\/property-transfer\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-23 07:46:24', '2026-04-23 07:46:24'),
(101, 2, 'property_transfer.approved', 'Property Transfer Approved', 'Transfer PTR-2026-0008 was approved by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"8\\\",\\\"transfer_number\\\":\\\"PTR-2026-0008\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-23 08:01:18', '2026-04-23 08:01:18'),
(103, 6, 'property_transfer.approved', 'Property Transfer Approved', 'Transfer PTR-2026-0008 was approved by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"8\\\",\\\"transfer_number\\\":\\\"PTR-2026-0008\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/8\\\"}\"', 'sent', NULL, '2026-04-23 08:01:19', NULL, '2026-04-23 08:01:18', '2026-04-23 08:01:19'),
(105, 2, 'property_transfer.releasing_approved', 'Transfer Cleared By Releasing Department', 'Transfer PTR-2026-0008 was approved by the releasing department.', '\"{\\\"transfer_id\\\":\\\"8\\\",\\\"transfer_number\\\":\\\"PTR-2026-0008\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-23 08:01:51', '2026-04-23 08:01:51'),
(107, 6, 'property_transfer.releasing_approved', 'Transfer Cleared By Releasing Department', 'Transfer PTR-2026-0008 was approved by the releasing department.', '\"{\\\"transfer_id\\\":\\\"8\\\",\\\"transfer_number\\\":\\\"PTR-2026-0008\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/8\\\"}\"', 'sent', NULL, '2026-04-23 08:01:53', NULL, '2026-04-23 08:01:51', '2026-04-23 08:01:53'),
(109, 2, 'property_transfer.completed', 'Property Transfer Completed', 'Transfer PTR-2026-0008 was finalized by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"8\\\",\\\"transfer_number\\\":\\\"PTR-2026-0008\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-23 08:02:02', '2026-04-23 08:02:02'),
(111, 6, 'property_transfer.completed', 'Property Transfer Completed', 'Transfer PTR-2026-0008 was finalized by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"8\\\",\\\"transfer_number\\\":\\\"PTR-2026-0008\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/8\\\"}\"', 'sent', NULL, '2026-04-23 08:02:03', NULL, '2026-04-23 08:02:02', '2026-04-23 08:02:03'),
(113, 2, 'property_transfer.created', 'New Property Transfer Request (Auto-Approved)', 'Transfer PTR-2026-0009 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"transfer_id\\\":\\\"9\\\",\\\"transfer_number\\\":\\\"PTR-2026-0009\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/9\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-23 13:03:36', '2026-04-23 13:03:36'),
(115, 6, 'property_transfer.created', 'New Property Transfer Request (Auto-Approved)', 'Transfer PTR-2026-0009 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"transfer_id\\\":\\\"9\\\",\\\"transfer_number\\\":\\\"PTR-2026-0009\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/9\\\"}\"', 'sent', NULL, '2026-04-23 13:03:38', NULL, '2026-04-23 13:03:36', '2026-04-23 13:03:38'),
(116, 2, 'property_transfer.rejected', 'Property Transfer Rejected', 'Transfer PTR-2026-0009 was rejected by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"9\\\",\\\"transfer_number\\\":\\\"PTR-2026-0009\\\",\\\"status\\\":\\\"rejected\\\",\\\"link\\\":\\\"\\/property-transfer\\/9\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-23 13:04:20', '2026-04-23 13:04:20'),
(118, 6, 'property_transfer.rejected', 'Property Transfer Rejected', 'Transfer PTR-2026-0009 was rejected by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"9\\\",\\\"transfer_number\\\":\\\"PTR-2026-0009\\\",\\\"status\\\":\\\"rejected\\\",\\\"link\\\":\\\"\\/property-transfer\\/9\\\"}\"', 'sent', NULL, '2026-04-23 13:04:22', NULL, '2026-04-23 13:04:20', '2026-04-23 13:04:22'),
(119, 6, 'property_transfer.rejected', 'Your Transfer Request Was Rejected', 'Transfer PTR-2026-0009 was rejected by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"9\\\",\\\"transfer_number\\\":\\\"PTR-2026-0009\\\",\\\"status\\\":\\\"rejected\\\",\\\"link\\\":\\\"\\/property-transfer\\/9\\\"}\"', 'sent', NULL, '2026-04-23 13:04:23', NULL, '2026-04-23 13:04:22', '2026-04-23 13:04:23'),
(120, 2, 'property_transfer.created', 'New Property Transfer Request (Auto-Approved)', 'Transfer PTR-2026-0010 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"transfer_id\\\":\\\"10\\\",\\\"transfer_number\\\":\\\"PTR-2026-0010\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/10\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-23 13:05:06', '2026-04-23 13:05:06'),
(122, 6, 'property_transfer.created', 'New Property Transfer Request (Auto-Approved)', 'Transfer PTR-2026-0010 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"transfer_id\\\":\\\"10\\\",\\\"transfer_number\\\":\\\"PTR-2026-0010\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/10\\\"}\"', 'sent', NULL, '2026-04-23 13:05:07', NULL, '2026-04-23 13:05:06', '2026-04-23 13:05:07'),
(123, 2, 'property_transfer.completed', 'Property Transfer Completed', 'Transfer PTR-2026-0010 was finalized by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"10\\\",\\\"transfer_number\\\":\\\"PTR-2026-0010\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/10\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-23 13:05:32', '2026-04-23 13:05:32'),
(125, 6, 'property_transfer.completed', 'Property Transfer Completed', 'Transfer PTR-2026-0010 was finalized by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"10\\\",\\\"transfer_number\\\":\\\"PTR-2026-0010\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/10\\\"}\"', 'sent', NULL, '2026-04-23 13:05:33', NULL, '2026-04-23 13:05:32', '2026-04-23 13:05:33'),
(126, 6, 'property_transfer.completed', 'Your Transfer Was Completed', 'Transfer PTR-2026-0010 has been completed.', '\"{\\\"transfer_id\\\":\\\"10\\\",\\\"transfer_number\\\":\\\"PTR-2026-0010\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/10\\\"}\"', 'sent', NULL, '2026-04-23 13:05:34', NULL, '2026-04-23 13:05:33', '2026-04-23 13:05:34'),
(127, 2, 'property_borrow.created', 'New Property Borrow Request (Auto-Approved)', 'Borrow request PBR-2026-0007 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"borrow_id\\\":\\\"7\\\",\\\"borrow_number\\\":\\\"PBR-2026-0007\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/7\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-23 13:08:44', '2026-04-23 13:08:44'),
(129, 6, 'property_borrow.created', 'New Property Borrow Request (Auto-Approved)', 'Borrow request PBR-2026-0007 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"borrow_id\\\":\\\"7\\\",\\\"borrow_number\\\":\\\"PBR-2026-0007\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/7\\\"}\"', 'sent', NULL, '2026-04-23 13:08:46', NULL, '2026-04-23 13:08:44', '2026-04-23 13:08:46'),
(130, 2, 'property_borrow.borrowed', 'Property Borrow Released', 'Borrow request PBR-2026-0007 was marked as delivered.', '\"{\\\"borrow_id\\\":\\\"7\\\",\\\"borrow_number\\\":\\\"PBR-2026-0007\\\",\\\"status\\\":\\\"borrowed\\\",\\\"link\\\":\\\"\\/property-borrow\\/7\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-23 13:08:59', '2026-04-23 13:08:59'),
(132, 6, 'property_borrow.borrowed', 'Property Borrow Released', 'Borrow request PBR-2026-0007 was marked as delivered.', '\"{\\\"borrow_id\\\":\\\"7\\\",\\\"borrow_number\\\":\\\"PBR-2026-0007\\\",\\\"status\\\":\\\"borrowed\\\",\\\"link\\\":\\\"\\/property-borrow\\/7\\\"}\"', 'sent', NULL, '2026-04-23 13:09:00', NULL, '2026-04-23 13:08:59', '2026-04-23 13:09:00'),
(134, 2, 'property_borrow.returned', 'Borrow Fully Returned', 'Borrow request PBR-2026-0007 has been fully returned.', '\"{\\\"borrow_id\\\":\\\"7\\\",\\\"borrow_number\\\":\\\"PBR-2026-0007\\\",\\\"status\\\":\\\"returned\\\",\\\"link\\\":\\\"\\/property-borrow\\/7\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-23 13:09:24', '2026-04-23 13:09:24'),
(136, 6, 'property_borrow.returned', 'Borrow Fully Returned', 'Borrow request PBR-2026-0007 has been fully returned.', '\"{\\\"borrow_id\\\":\\\"7\\\",\\\"borrow_number\\\":\\\"PBR-2026-0007\\\",\\\"status\\\":\\\"returned\\\",\\\"link\\\":\\\"\\/property-borrow\\/7\\\"}\"', 'sent', NULL, '2026-04-23 13:09:27', NULL, '2026-04-23 13:09:24', '2026-04-23 13:09:27'),
(138, 2, 'property_borrow.completed', 'Property Borrow Completed', 'Borrow request PBR-2026-0007 has been marked as completed.', '\"{\\\"borrow_id\\\":\\\"7\\\",\\\"borrow_number\\\":\\\"PBR-2026-0007\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-borrow\\/7\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-23 13:09:32', '2026-04-23 13:09:32'),
(140, 6, 'property_borrow.completed', 'Property Borrow Completed', 'Borrow request PBR-2026-0007 has been marked as completed.', '\"{\\\"borrow_id\\\":\\\"7\\\",\\\"borrow_number\\\":\\\"PBR-2026-0007\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-borrow\\/7\\\"}\"', 'sent', NULL, '2026-04-23 13:09:33', NULL, '2026-04-23 13:09:32', '2026-04-23 13:09:33'),
(142, 2, 'property_transfer.created', 'New Property Transfer Request (Auto-Approved)', 'Transfer PTR-2026-0011 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"transfer_id\\\":\\\"11\\\",\\\"transfer_number\\\":\\\"PTR-2026-0011\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/11\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 07:25:23', '2026-04-24 07:25:23'),
(143, 3, 'property_transfer.created', 'New Property Transfer Request (Auto-Approved)', 'Transfer PTR-2026-0011 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"transfer_id\\\":\\\"11\\\",\\\"transfer_number\\\":\\\"PTR-2026-0011\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/11\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 07:25:23', '2026-04-24 07:25:23'),
(144, 6, 'property_transfer.created', 'New Property Transfer Request (Auto-Approved)', 'Transfer PTR-2026-0011 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"transfer_id\\\":\\\"11\\\",\\\"transfer_number\\\":\\\"PTR-2026-0011\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/11\\\"}\"', 'sent', NULL, '2026-04-24 07:25:25', NULL, '2026-04-24 07:25:23', '2026-04-24 07:25:25'),
(145, 2, 'property_transfer.releasing_approved', 'Transfer Cleared By Releasing Department', 'Transfer PTR-2026-0011 was approved by the releasing department.', '\"{\\\"transfer_id\\\":\\\"11\\\",\\\"transfer_number\\\":\\\"PTR-2026-0011\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/11\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 07:26:33', '2026-04-24 07:26:33'),
(146, 3, 'property_transfer.releasing_approved', 'Transfer Cleared By Releasing Department', 'Transfer PTR-2026-0011 was approved by the releasing department.', '\"{\\\"transfer_id\\\":\\\"11\\\",\\\"transfer_number\\\":\\\"PTR-2026-0011\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/11\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 07:26:33', '2026-04-24 07:26:33'),
(147, 6, 'property_transfer.releasing_approved', 'Transfer Cleared By Releasing Department', 'Transfer PTR-2026-0011 was approved by the releasing department.', '\"{\\\"transfer_id\\\":\\\"11\\\",\\\"transfer_number\\\":\\\"PTR-2026-0011\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/11\\\"}\"', 'sent', NULL, '2026-04-24 07:26:34', NULL, '2026-04-24 07:26:33', '2026-04-24 07:26:34'),
(148, 6, 'property_transfer.releasing_approved', 'Transfer Cleared By Releasing Department', 'Transfer PTR-2026-0011 was approved by the releasing department and is awaiting signatures.', '\"{\\\"transfer_id\\\":\\\"11\\\",\\\"transfer_number\\\":\\\"PTR-2026-0011\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/11\\\"}\"', 'sent', NULL, '2026-04-24 07:26:35', NULL, '2026-04-24 07:26:34', '2026-04-24 07:26:35'),
(149, 2, 'property_borrow.created', 'New Property Borrow Request (Auto-Approved)', 'Borrow request PBR-2026-0008 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 08:19:25', '2026-04-24 08:19:25'),
(150, 3, 'property_borrow.created', 'New Property Borrow Request (Auto-Approved)', 'Borrow request PBR-2026-0008 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 08:19:25', '2026-04-24 08:19:25'),
(151, 6, 'property_borrow.created', 'New Property Borrow Request (Auto-Approved)', 'Borrow request PBR-2026-0008 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'sent', NULL, '2026-04-24 08:19:27', NULL, '2026-04-24 08:19:25', '2026-04-24 08:19:27'),
(152, 2, 'property_transfer.created', 'New Property Transfer Request (Auto-Approved)', 'Transfer PTR-2026-0012 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"transfer_id\\\":\\\"12\\\",\\\"transfer_number\\\":\\\"PTR-2026-0012\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/12\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 08:20:04', '2026-04-24 08:20:04'),
(153, 3, 'property_transfer.created', 'New Property Transfer Request (Auto-Approved)', 'Transfer PTR-2026-0012 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"transfer_id\\\":\\\"12\\\",\\\"transfer_number\\\":\\\"PTR-2026-0012\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/12\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 08:20:04', '2026-04-24 08:20:04'),
(154, 6, 'property_transfer.created', 'New Property Transfer Request (Auto-Approved)', 'Transfer PTR-2026-0012 was submitted by GSO Administrator and auto-approved by GSO workflow.', '\"{\\\"transfer_id\\\":\\\"12\\\",\\\"transfer_number\\\":\\\"PTR-2026-0012\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/12\\\"}\"', 'sent', NULL, '2026-04-24 08:20:05', NULL, '2026-04-24 08:20:04', '2026-04-24 08:20:05'),
(155, 2, 'property_transfer.releasing_approved', 'Transfer Cleared By Releasing Department', 'Transfer PTR-2026-0012 was approved by the releasing department.', '\"{\\\"transfer_id\\\":\\\"12\\\",\\\"transfer_number\\\":\\\"PTR-2026-0012\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/12\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 08:34:49', '2026-04-24 08:34:49'),
(156, 3, 'property_transfer.releasing_approved', 'Transfer Cleared By Releasing Department', 'Transfer PTR-2026-0012 was approved by the releasing department.', '\"{\\\"transfer_id\\\":\\\"12\\\",\\\"transfer_number\\\":\\\"PTR-2026-0012\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/12\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 08:34:49', '2026-04-24 08:34:49'),
(157, 6, 'property_transfer.releasing_approved', 'Transfer Cleared By Releasing Department', 'Transfer PTR-2026-0012 was approved by the releasing department.', '\"{\\\"transfer_id\\\":\\\"12\\\",\\\"transfer_number\\\":\\\"PTR-2026-0012\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/12\\\"}\"', 'failed', 'No active push token or FCM disabled', NULL, NULL, '2026-04-24 08:34:49', '2026-04-24 08:34:49'),
(158, 3, 'property_transfer.releasing_approved', 'Transfer Cleared By Releasing Department', 'Transfer PTR-2026-0012 was approved by the releasing department and is awaiting signatures.', '\"{\\\"transfer_id\\\":\\\"12\\\",\\\"transfer_number\\\":\\\"PTR-2026-0012\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-transfer\\/12\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 08:34:49', '2026-04-24 08:34:49'),
(159, 2, 'property_borrow.releasing_approved', 'Borrow Request Cleared By Releasing Department', 'Borrow request PBR-2026-0008 was approved by the releasing department.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 11:24:40', '2026-04-24 11:24:40'),
(160, 3, 'property_borrow.releasing_approved', 'Borrow Request Cleared By Releasing Department', 'Borrow request PBR-2026-0008 was approved by the releasing department.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 11:24:40', '2026-04-24 11:24:40'),
(161, 6, 'property_borrow.releasing_approved', 'Borrow Request Cleared By Releasing Department', 'Borrow request PBR-2026-0008 was approved by the releasing department.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'sent', NULL, '2026-04-24 11:24:43', NULL, '2026-04-24 11:24:40', '2026-04-24 11:24:43'),
(162, 3, 'property_borrow.releasing_approved', 'Borrow Request Cleared By Releasing Department', 'Borrow request PBR-2026-0008 was approved by releasing department and is waiting for final GSO action.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"approved\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 11:24:43', '2026-04-24 11:24:43'),
(163, 2, 'property_borrow.borrowed', 'Property Borrow Released', 'Borrow request PBR-2026-0008 was marked as delivered.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"borrowed\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 11:40:52', '2026-04-24 11:40:52'),
(164, 3, 'property_borrow.borrowed', 'Property Borrow Released', 'Borrow request PBR-2026-0008 was marked as delivered.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"borrowed\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 11:40:52', '2026-04-24 11:40:52'),
(165, 6, 'property_borrow.borrowed', 'Property Borrow Released', 'Borrow request PBR-2026-0008 was marked as delivered.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"borrowed\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'sent', NULL, '2026-04-24 11:40:54', NULL, '2026-04-24 11:40:52', '2026-04-24 11:40:54'),
(166, 3, 'property_borrow.borrowed', 'Borrow Request Delivered', 'Borrow request PBR-2026-0008 is now marked as delivered.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"borrowed\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 11:40:54', '2026-04-24 11:40:54'),
(167, 2, 'property_borrow.partially_returned', 'Borrow Partially Returned', 'Borrow request PBR-2026-0008 has a partial return recorded.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"partially_returned\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 11:41:23', '2026-04-24 11:41:23'),
(168, 3, 'property_borrow.partially_returned', 'Borrow Partially Returned', 'Borrow request PBR-2026-0008 has a partial return recorded.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"partially_returned\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 11:41:23', '2026-04-24 11:41:23'),
(169, 6, 'property_borrow.partially_returned', 'Borrow Partially Returned', 'Borrow request PBR-2026-0008 has a partial return recorded.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"partially_returned\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'sent', NULL, '2026-04-24 11:41:25', NULL, '2026-04-24 11:41:23', '2026-04-24 11:41:25'),
(170, 3, 'property_borrow.partially_returned', 'Borrow Partial Return Recorded', 'Borrow request PBR-2026-0008 is now partially returned.', '\"{\\\"borrow_id\\\":\\\"8\\\",\\\"borrow_number\\\":\\\"PBR-2026-0008\\\",\\\"status\\\":\\\"partially_returned\\\",\\\"link\\\":\\\"\\/property-borrow\\/8\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 11:41:25', '2026-04-24 11:41:25'),
(171, 2, 'property_transfer.completed', 'Property Transfer Completed', 'Transfer PTR-2026-0012 was finalized by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"12\\\",\\\"transfer_number\\\":\\\"PTR-2026-0012\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/12\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 11:57:58', '2026-04-24 11:57:58'),
(172, 3, 'property_transfer.completed', 'Property Transfer Completed', 'Transfer PTR-2026-0012 was finalized by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"12\\\",\\\"transfer_number\\\":\\\"PTR-2026-0012\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/12\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 11:57:58', '2026-04-24 11:57:58'),
(173, 6, 'property_transfer.completed', 'Property Transfer Completed', 'Transfer PTR-2026-0012 was finalized by GSO/Admin.', '\"{\\\"transfer_id\\\":\\\"12\\\",\\\"transfer_number\\\":\\\"PTR-2026-0012\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/12\\\"}\"', 'sent', NULL, '2026-04-24 11:57:59', NULL, '2026-04-24 11:57:58', '2026-04-24 11:57:59'),
(174, 3, 'property_transfer.completed', 'Your Transfer Was Completed', 'Transfer PTR-2026-0012 has been completed.', '\"{\\\"transfer_id\\\":\\\"12\\\",\\\"transfer_number\\\":\\\"PTR-2026-0012\\\",\\\"status\\\":\\\"completed\\\",\\\"link\\\":\\\"\\/property-transfer\\/12\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-24 11:57:59', '2026-04-24 11:57:59'),
(175, 6, 'delivery_receipt.acknowledged', 'Delivery Receipt Acknowledged', 'Delivery receipt DR-2026-04-27-004 has been acknowledged and inventory is ready.', '\"{\\\"dr_id\\\":\\\"91\\\",\\\"dr_number\\\":\\\"DR-2026-04-27-004\\\",\\\"status\\\":\\\"acknowledged\\\",\\\"link\\\":\\\"\\/department_head\\/delivery-receipts\\\"}\"', 'sent', NULL, '2026-04-27 16:39:48', NULL, '2026-04-27 16:39:45', '2026-04-27 16:39:48'),
(176, 3, 'delivery_receipt.acknowledged', 'Delivery Receipt Acknowledged', 'Delivery receipt DR-2026-04-27-001 has been acknowledged and inventory is ready.', '\"{\\\"dr_id\\\":\\\"88\\\",\\\"dr_number\\\":\\\"DR-2026-04-27-001\\\",\\\"status\\\":\\\"acknowledged\\\",\\\"link\\\":\\\"\\/department_head\\/delivery-receipts\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-29 02:22:06', '2026-04-29 02:22:06'),
(177, 3, 'delivery_receipt.acknowledged', 'Delivery Receipt Acknowledged', 'Delivery receipt DR-2026-04-27-002 has been acknowledged and inventory is ready.', '\"{\\\"dr_id\\\":\\\"90\\\",\\\"dr_number\\\":\\\"DR-2026-04-27-002\\\",\\\"status\\\":\\\"acknowledged\\\",\\\"link\\\":\\\"\\/department_head\\/delivery-receipts\\\"}\"', 'failed', 'No active push token', NULL, NULL, '2026-04-29 04:47:24', '2026-04-29 04:47:24');

-- --------------------------------------------------------

--
-- Table structure for table `user_push_tokens`
--

CREATE TABLE `user_push_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `UserID` int(11) NOT NULL,
  `token` varchar(512) NOT NULL,
  `platform` varchar(20) NOT NULL DEFAULT 'web',
  `device_name` varchar(120) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_push_tokens`
--

INSERT INTO `user_push_tokens` (`id`, `UserID`, `token`, `platform`, `device_name`, `is_active`, `last_used_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'eIxXBRQyxYtixroaQxv-ep:APA91bGwtBzOsEHHDfytvQ8UwKfx41l47jyN7UpIKJOzwvzyjxIUOyLU4S1-OriFDLOpFwZIbpxsfbrZOVginsjoE8LqWPkZxXEf8wQSDa0HF4rMgo_8ofA', 'web', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 0, '2026-03-22 14:51:52', '2026-03-22 14:51:52', '2026-04-03 17:05:49'),
(2, 1, 'eIxXBRQyxYtixroaQxv-ep:APA91bGivlK62QLG-cnzkqyeE36Bun02PQh80ZTObmoklq1Vfw-vaRJfdj-UVp5aM98YR0RglHn9LglUKoa-XX37-zdBe__2Md_gM9btTHC9P1UYOucoXTo', 'web', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 0, '2026-03-22 14:51:52', '2026-03-22 14:51:52', '2026-04-03 17:05:50'),
(3, 1, 'eIxXBRQyxYtixroaQxv-ep:APA91bGxWwIi2G3SqgYYPiu0KagnNx-OUJDH2Y0DC_9qIKJS-O2AWOtW28W_baguyi62YI8uh13XWQs7O6lz-eeYK29s1Hd06IO-Ldjpb0S5lZ68KuzOCtk', 'web', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 0, '2026-03-22 14:52:48', '2026-03-22 14:52:48', '2026-04-03 17:05:51'),
(4, 1, 'eIxXBRQyxYtixroaQxv-ep:APA91bEVfXSCtXEdUqPGRw1IjCl7XH-FMEJAxNJ0uOFJ2k8yJlEqEsy6qPMi0HLY2GhDr_IcdcUvYaPc21KwIcXW02as2_yR2MVSIVomOPogYPxX8yskWY4', 'web', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 1, '2026-04-29 06:20:57', '2026-03-22 14:52:48', '2026-04-29 06:20:57'),
(5, 3, 'fm4yL6R5AUVBckN0vkCdOQ:APA91bHmMQCxL5xmXmgD6YmfOouPQo2JHakGtvIru3dg77GcvGfNihplo0i1QVEFuLLjHNjku-TsK3y4A1Zswqp7twRF6U8rYewdZlShiC-oKF1gYDTouWc', 'web', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.', 0, '2026-03-25 16:28:43', '2026-03-22 15:23:01', '2026-03-29 12:56:49'),
(6, 1, 'e2IUQgH-XpmjgfRktMqytD:APA91bEhcBUMbmgznKUz5-2Uh3ArJr9U7K0qfTqrsWBHdIs4zWyU5FGfxRedObq8Euw2fsSwHDO23kZ1CKKzJPE6LO0B4nAbsLpQYbFvj8f30o5cB3GTIpU', 'web', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 0, '2026-03-25 13:31:10', '2026-03-25 13:31:10', '2026-04-03 17:05:52'),
(7, 1, 'e2IUQgH-XpmjgfRktMqytD:APA91bFy1L3AtZ33aBGwe7fOomPNtOIY9KKJWkYoeQo55ewNVrXFIllRt2DchzmYyMTdpZD_WZdn0NARWdvp3LxkZo0i5e321uN3wF1G_Dj9zNiB4PIH61o', 'web', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, '2026-03-25 14:01:41', '2026-03-25 13:31:10', '2026-03-25 14:01:41'),
(8, 6, 'fm4yL6R5AUVBckN0vkCdOQ:APA91bHWwS9DhEMtQP70CLVDiGNA4MxtySBMrtrpySest_P1vuv-vNG4j7K9C3dieSK_fOZPyi3N6NP764UKB3m1B_v8mOupvOlyWcUTrJHOiRBthNO93PM', 'web', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.', 1, '2026-04-23 07:50:09', '2026-03-28 16:03:51', '2026-04-23 07:50:09');

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `VendorID` bigint(20) UNSIGNED NOT NULL,
  `VendorName` varchar(100) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `ContactPerson` varchar(100) DEFAULT NULL,
  `ContactNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT 1,
  `CreatedDate` datetime DEFAULT NULL,
  `UpdatedDate` datetime DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`VendorID`, `VendorName`, `Address`, `ContactPerson`, `ContactNumber`, `Email`, `IsActive`, `CreatedDate`, `UpdatedDate`, `deleted_at`) VALUES
(1, 'Andre Repair', 'secrte', 'Andre Clement Louie S. Malinao', '09925077173', 'andremalinao@gmail.com', 1, '2026-03-25 00:03:37', NULL, NULL);

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
-- Indexes for table `asset_service_records`
--
ALTER TABLE `asset_service_records`
  ADD PRIMARY KEY (`ServiceRecordID`),
  ADD UNIQUE KEY `asset_service_records_servicenumber_unique` (`ServiceNumber`),
  ADD KEY `asset_service_records_datereported_index` (`DateReported`),
  ADD KEY `asset_service_records_createdbyuserid_foreign` (`CreatedByUserID`),
  ADD KEY `asset_service_records_updatedbyuserid_foreign` (`UpdatedByUserID`),
  ADD KEY `asset_service_records_status_reportsource_index` (`Status`,`ReportSource`),
  ADD KEY `asset_service_records_reportsource_foreign` (`ReportSource`);

--
-- Indexes for table `asset_service_records_details`
--
ALTER TABLE `asset_service_records_details`
  ADD PRIMARY KEY (`ServiceRecordDetailID`),
  ADD KEY `asset_service_records_details_servicerecordid_status_index` (`ServiceRecordID`,`Status`),
  ADD KEY `asset_service_records_details_inventorydetailid_index` (`InventoryDetailID`),
  ADD KEY `asset_service_records_details_assignedvendorid_index` (`AssignedVendorID`),
  ADD KEY `asset_service_records_details_createdbyuserid_foreign` (`CreatedByUserID`),
  ADD KEY `asset_service_records_details_updatedbyuserid_foreign` (`UpdatedByUserID`),
  ADD KEY `asset_service_records_details_assignedsupplierid_index` (`AssignedSupplierID`);

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
  ADD UNIQUE KEY `ux_deliveryreceipt_iarnumber` (`IARNumber`),
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
  ADD UNIQUE KEY `ux_inspectionacceptancereport_iarnumber` (`IARNumber`),
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
  ADD KEY `propertyissuancedetail_requestdetailid_foreign` (`RequestDetailID`),
  ADD KEY `propertyissuancedetail_ris_item_id_index` (`ris_item_id`);

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
-- Indexes for table `property_card_entries`
--
ALTER TABLE `property_card_entries`
  ADD PRIMARY KEY (`EntryID`),
  ADD UNIQUE KEY `property_card_entries_sourcekey_unique` (`SourceKey`),
  ADD KEY `idx_pc_entries_detail_date` (`InventoryDetailID`,`EntryDate`),
  ADD KEY `idx_pc_entries_source` (`SourceType`,`SourceID`);

--
-- Indexes for table `property_waste_items`
--
ALTER TABLE `property_waste_items`
  ADD PRIMARY KEY (`PropertyWasteItemID`),
  ADD UNIQUE KEY `property_waste_items_servicerecorddetailid_unique` (`ServiceRecordDetailID`),
  ADD KEY `property_waste_items_propertywastereportid_foreign` (`PropertyWasteReportID`),
  ADD KEY `property_waste_items_inventorydetailid_foreign` (`InventoryDetailID`),
  ADD KEY `property_waste_items_conditionid_foreign` (`ConditionID`),
  ADD KEY `property_waste_items_disposalmethodid_foreign` (`DisposalMethodID`),
  ADD KEY `property_waste_items_actionedbyuserid_foreign` (`ActionedByUserID`);

--
-- Indexes for table `property_waste_reports`
--
ALTER TABLE `property_waste_reports`
  ADD PRIMARY KEY (`PropertyWasteReportID`),
  ADD UNIQUE KEY `property_waste_reports_wastenumber_unique` (`WasteNumber`),
  ADD KEY `property_waste_reports_sourceservicerecordid_foreign` (`SourceServiceRecordID`),
  ADD KEY `property_waste_reports_departmentid_foreign` (`DepartmentID`),
  ADD KEY `property_waste_reports_createdbyuserid_foreign` (`CreatedByUserID`),
  ADD KEY `property_waste_reports_updatedbyuserid_foreign` (`UpdatedByUserID`);

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
-- Indexes for table `rpc_inventory_cycles`
--
ALTER TABLE `rpc_inventory_cycles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rpc_inventory_cycles_year_unique` (`start_year`,`end_year`),
  ADD UNIQUE KEY `rpc_inventory_cycles_cycle_label_unique` (`cycle_label`),
  ADD KEY `rpc_inventory_cycles_status_index` (`status`),
  ADD KEY `rpc_inventory_cycles_created_by_user_id_foreign` (`created_by_user_id`),
  ADD KEY `rpc_inventory_cycles_updated_by_user_id_foreign` (`updated_by_user_id`);

--
-- Indexes for table `rpc_inventory_scans`
--
ALTER TABLE `rpc_inventory_scans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rpc_inventory_cycle_detail_unique` (`rpc_inventory_cycle_id`,`inventory_detail_id`),
  ADD KEY `rpc_inventory_cycle_type_idx` (`rpc_inventory_cycle_id`,`rpc_type`),
  ADD KEY `rpc_inventory_scans_property_number_index` (`property_number`),
  ADD KEY `rpc_inventory_scans_condition_id_foreign` (`condition_id`),
  ADD KEY `rpc_inventory_scans_scanned_by_user_id_foreign` (`scanned_by_user_id`);

--
-- Indexes for table `rsmi_header`
--
ALTER TABLE `rsmi_header`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rsmi_serial_unique` (`serial_year`,`serial_month`,`serial_sequence`),
  ADD UNIQUE KEY `rsmi_reports_report_date_unique` (`report_date`),
  ADD UNIQUE KEY `rsmi_reports_serial_no_unique` (`serial_no`),
  ADD KEY `rsmi_reports_report_date_index` (`report_date`),
  ADD KEY `rsmi_reports_created_by_user_id_foreign` (`created_by_user_id`);

--
-- Indexes for table `rsmi_items`
--
ALTER TABLE `rsmi_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rsmi_report_items_stock_no_index` (`stock_no`),
  ADD KEY `rsmi_report_items_ris_id_foreign` (`ris_id`),
  ADD KEY `rsmi_items_rsmi_header_id_index` (`rsmi_header_id`);

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
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`UserNotificationID`),
  ADD KEY `user_notifications_userid_created_at_index` (`UserID`,`created_at`),
  ADD KEY `user_notifications_userid_read_at_index` (`UserID`,`read_at`);

--
-- Indexes for table `user_push_tokens`
--
ALTER TABLE `user_push_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_push_tokens_userid_is_active_index` (`UserID`,`is_active`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`VendorID`),
  ADD KEY `vendor_vendorname_index` (`VendorName`),
  ADD KEY `vendor_isactive_index` (`IsActive`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `ArticleID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `assetrequest`
--
ALTER TABLE `assetrequest`
  MODIFY `RequestID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `assetrequestdetail`
--
ALTER TABLE `assetrequestdetail`
  MODIFY `RequestDetailID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  MODIFY `AssignmentID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_audit_logs`
--
ALTER TABLE `asset_audit_logs`
  MODIFY `AuditID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=327;

--
-- AUTO_INCREMENT for table `asset_request_signatures`
--
ALTER TABLE `asset_request_signatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_service_records`
--
ALTER TABLE `asset_service_records`
  MODIFY `ServiceRecordID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `asset_service_records_details`
--
ALTER TABLE `asset_service_records_details`
  MODIFY `ServiceRecordDetailID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `assignedproperty`
--
ALTER TABLE `assignedproperty`
  MODIFY `PropertyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `condition`
--
ALTER TABLE `condition`
  MODIFY `ConditionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `deliveryreceipt`
--
ALTER TABLE `deliveryreceipt`
  MODIFY `DRID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `deliveryreceiptdetail`
--
ALTER TABLE `deliveryreceiptdetail`
  MODIFY `DRDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;

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
  MODIFY `EndUserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

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
  MODIFY `IARID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

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
  MODIFY `BorrowID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `propertyborrowitems`
--
ALTER TABLE `propertyborrowitems`
  MODIFY `BorrowItemID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `propertyclassification`
--
ALTER TABLE `propertyclassification`
  MODIFY `PropertyClassificationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `propertyinventory`
--
ALTER TABLE `propertyinventory`
  MODIFY `InventoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `propertyinventorydetail`
--
ALTER TABLE `propertyinventorydetail`
  MODIFY `InventoryDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=275;

--
-- AUTO_INCREMENT for table `propertyissuance`
--
ALTER TABLE `propertyissuance`
  MODIFY `IssuanceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `propertyissuancedetail`
--
ALTER TABLE `propertyissuancedetail`
  MODIFY `IssuanceDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT for table `propertyreceived`
--
ALTER TABLE `propertyreceived`
  MODIFY `ReceivedID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `propertyreturn`
--
ALTER TABLE `propertyreturn`
  MODIFY `ReturnID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `propertyreturnitemconditions`
--
ALTER TABLE `propertyreturnitemconditions`
  MODIFY `ReturnItemConditionID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `propertyreturnitems`
--
ALTER TABLE `propertyreturnitems`
  MODIFY `ReturnItemID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `propertystatus`
--
ALTER TABLE `propertystatus`
  MODIFY `PropertyStatusID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `propertytransfer`
--
ALTER TABLE `propertytransfer`
  MODIFY `TransferID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `propertytransferdetails`
--
ALTER TABLE `propertytransferdetails`
  MODIFY `TransferItemID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `propertytype`
--
ALTER TABLE `propertytype`
  MODIFY `PropertyTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `property_card_entries`
--
ALTER TABLE `property_card_entries`
  MODIFY `EntryID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `property_waste_items`
--
ALTER TABLE `property_waste_items`
  MODIFY `PropertyWasteItemID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `property_waste_reports`
--
ALTER TABLE `property_waste_reports`
  MODIFY `PropertyWasteReportID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `qrcode`
--
ALTER TABLE `qrcode`
  MODIFY `QRCodeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `requisition_issue_slips`
--
ALTER TABLE `requisition_issue_slips`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `requisition_issue_slip_items`
--
ALTER TABLE `requisition_issue_slip_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `rpc_inventory_cycles`
--
ALTER TABLE `rpc_inventory_cycles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rpc_inventory_scans`
--
ALTER TABLE `rpc_inventory_scans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rsmi_header`
--
ALTER TABLE `rsmi_header`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rsmi_items`
--
ALTER TABLE `rsmi_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3889;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `SupplierID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `trusted_devices`
--
ALTER TABLE `trusted_devices`
  MODIFY `DeviceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

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
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `UserNotificationID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT for table `user_push_tokens`
--
ALTER TABLE `user_push_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vendor`
--
ALTER TABLE `vendor`
  MODIFY `VendorID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- Constraints for table `asset_service_records`
--
ALTER TABLE `asset_service_records`
  ADD CONSTRAINT `asset_service_records_createdbyuserid_foreign` FOREIGN KEY (`CreatedByUserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `asset_service_records_reportsource_foreign` FOREIGN KEY (`ReportSource`) REFERENCES `department` (`DepartmentID`) ON DELETE SET NULL,
  ADD CONSTRAINT `asset_service_records_updatedbyuserid_foreign` FOREIGN KEY (`UpdatedByUserID`) REFERENCES `user` (`UserID`) ON DELETE SET NULL;

--
-- Constraints for table `asset_service_records_details`
--
ALTER TABLE `asset_service_records_details`
  ADD CONSTRAINT `asset_service_records_details_assignedvendorid_foreign` FOREIGN KEY (`AssignedVendorID`) REFERENCES `vendor` (`VendorID`) ON DELETE SET NULL,
  ADD CONSTRAINT `asset_service_records_details_createdbyuserid_foreign` FOREIGN KEY (`CreatedByUserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `asset_service_records_details_inventorydetailid_foreign` FOREIGN KEY (`InventoryDetailID`) REFERENCES `propertyinventorydetail` (`InventoryDetailID`),
  ADD CONSTRAINT `asset_service_records_details_servicerecordid_foreign` FOREIGN KEY (`ServiceRecordID`) REFERENCES `asset_service_records` (`ServiceRecordID`) ON DELETE CASCADE,
  ADD CONSTRAINT `asset_service_records_details_updatedbyuserid_foreign` FOREIGN KEY (`UpdatedByUserID`) REFERENCES `user` (`UserID`) ON DELETE SET NULL;

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
-- Constraints for table `propertyissuancedetail`
--
ALTER TABLE `propertyissuancedetail`
  ADD CONSTRAINT `propertyissuancedetail_ris_item_id_foreign` FOREIGN KEY (`ris_item_id`) REFERENCES `requisition_issue_slip_items` (`id`) ON DELETE SET NULL;

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

--
-- Constraints for table `property_card_entries`
--
ALTER TABLE `property_card_entries`
  ADD CONSTRAINT `fk_pc_entries_inventory_detail` FOREIGN KEY (`InventoryDetailID`) REFERENCES `propertyinventorydetail` (`InventoryDetailID`) ON DELETE CASCADE;

--
-- Constraints for table `property_waste_items`
--
ALTER TABLE `property_waste_items`
  ADD CONSTRAINT `property_waste_items_actionedbyuserid_foreign` FOREIGN KEY (`ActionedByUserID`) REFERENCES `user` (`UserID`) ON DELETE SET NULL,
  ADD CONSTRAINT `property_waste_items_conditionid_foreign` FOREIGN KEY (`ConditionID`) REFERENCES `condition` (`ConditionID`) ON DELETE SET NULL,
  ADD CONSTRAINT `property_waste_items_disposalmethodid_foreign` FOREIGN KEY (`DisposalMethodID`) REFERENCES `disposalmethod` (`DisposalMethodID`) ON DELETE SET NULL,
  ADD CONSTRAINT `property_waste_items_inventorydetailid_foreign` FOREIGN KEY (`InventoryDetailID`) REFERENCES `propertyinventorydetail` (`InventoryDetailID`) ON DELETE CASCADE,
  ADD CONSTRAINT `property_waste_items_propertywastereportid_foreign` FOREIGN KEY (`PropertyWasteReportID`) REFERENCES `property_waste_reports` (`PropertyWasteReportID`) ON DELETE CASCADE,
  ADD CONSTRAINT `property_waste_items_servicerecorddetailid_foreign` FOREIGN KEY (`ServiceRecordDetailID`) REFERENCES `asset_service_records_details` (`ServiceRecordDetailID`) ON DELETE SET NULL;

--
-- Constraints for table `property_waste_reports`
--
ALTER TABLE `property_waste_reports`
  ADD CONSTRAINT `property_waste_reports_createdbyuserid_foreign` FOREIGN KEY (`CreatedByUserID`) REFERENCES `user` (`UserID`) ON DELETE SET NULL,
  ADD CONSTRAINT `property_waste_reports_departmentid_foreign` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`) ON DELETE SET NULL,
  ADD CONSTRAINT `property_waste_reports_sourceservicerecordid_foreign` FOREIGN KEY (`SourceServiceRecordID`) REFERENCES `asset_service_records` (`ServiceRecordID`) ON DELETE SET NULL,
  ADD CONSTRAINT `property_waste_reports_updatedbyuserid_foreign` FOREIGN KEY (`UpdatedByUserID`) REFERENCES `user` (`UserID`) ON DELETE SET NULL;

--
-- Constraints for table `rpc_inventory_cycles`
--
ALTER TABLE `rpc_inventory_cycles`
  ADD CONSTRAINT `rpc_inventory_cycles_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `rpc_inventory_cycles_updated_by_user_id_foreign` FOREIGN KEY (`updated_by_user_id`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `rpc_inventory_scans`
--
ALTER TABLE `rpc_inventory_scans`
  ADD CONSTRAINT `rpc_inventory_scans_condition_id_foreign` FOREIGN KEY (`condition_id`) REFERENCES `condition` (`ConditionID`),
  ADD CONSTRAINT `rpc_inventory_scans_rpc_inventory_cycle_id_foreign` FOREIGN KEY (`rpc_inventory_cycle_id`) REFERENCES `rpc_inventory_cycles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rpc_inventory_scans_scanned_by_user_id_foreign` FOREIGN KEY (`scanned_by_user_id`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `rsmi_header`
--
ALTER TABLE `rsmi_header`
  ADD CONSTRAINT `rsmi_reports_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `user` (`UserID`);

--
-- Constraints for table `rsmi_items`
--
ALTER TABLE `rsmi_items`
  ADD CONSTRAINT `rsmi_items_rsmi_header_id_foreign` FOREIGN KEY (`rsmi_header_id`) REFERENCES `rsmi_header` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rsmi_report_items_ris_id_foreign` FOREIGN KEY (`ris_id`) REFERENCES `requisition_issue_slips` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `user_notifications_userid_foreign` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `user_push_tokens`
--
ALTER TABLE `user_push_tokens`
  ADD CONSTRAINT `user_push_tokens_userid_foreign` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
