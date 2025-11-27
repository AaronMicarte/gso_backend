-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2025 at 02:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

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
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `DepartmentID` int(11) NOT NULL,
  `DepartmentName` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loginaudit`
--

CREATE TABLE `loginaudit` (
  `LoginAuditID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `EventTime` datetime NOT NULL DEFAULT current_timestamp(),
  `Status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `UserRoleID` int(11) NOT NULL,
  `DepartmentID` int(11) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userrole`
--

CREATE TABLE `userrole` (
  `UserRoleID` int(11) NOT NULL,
  `Role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`DepartmentID`);

--
-- Indexes for table `loginaudit`
--
ALTER TABLE `loginaudit`
  ADD PRIMARY KEY (`LoginAuditID`),
  ADD KEY `fk_login_user` (`UserID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `fk_user_role` (`UserRoleID`),
  ADD KEY `fk_user_department` (`DepartmentID`);

--
-- Indexes for table `userrole`
--
ALTER TABLE `userrole`
  ADD PRIMARY KEY (`UserRoleID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loginaudit`
--
ALTER TABLE `loginaudit`
  MODIFY `LoginAuditID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userrole`
--
ALTER TABLE `userrole`
  MODIFY `UserRoleID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loginaudit`
--
ALTER TABLE `loginaudit`
  ADD CONSTRAINT `fk_login_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Dumping data for table `department`
--

INSERT IGNORE INTO `department` (`DepartmentID`, `DepartmentName`) VALUES
(1, 'GSO'),
(2, 'Human Resources'),
(3, 'PESO'),
(4, 'Accounting'),
(5, 'CSWD');
-- --------------------------------------------------------

--
-- Dumping data for table `loginaudit`
--

INSERT IGNORE INTO `loginaudit` (`LoginAuditID`, `UserID`, `EventTime`, `Status`) VALUES
(1, 1, '2025-11-26 10:00:00', 'Success'),
(2, 2, '2025-11-26 10:05:00', 'Success'),
(3, 3, '2025-11-26 10:10:00', 'Failed');

-- --------------------------------------------------------

--
-- Dumping data for table `user`
--

INSERT IGNORE INTO `user` (`UserID`, `UserRoleID`, `DepartmentID`, `Username`, `Email`, `Password`) VALUES
(1, 1, 1, 'gso.admin', 'gso.admin@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),  -- GSO Admin
(2, 2, 2, 'gso.staff', 'gso.staff@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),  -- GSO Staff
(3, 3, 5, 'gso.custodian', 'gso.custodian@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- Custodian

-- --------------------------------------------------------

--
-- Dumping data for table `userrole`
--

INSERT IGNORE INTO `userrole` (`UserRoleID`, `Role`) VALUES
(1, 'gso admin'),
(2, 'gso staff'),
(3, 'custodian');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
