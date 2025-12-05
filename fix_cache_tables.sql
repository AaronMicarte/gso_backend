-- Fix cache tables for Laravel RateLimiter
-- Run this in phpMyAdmin or mysql CLI

-- Drop incorrect tables if they exist
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `cache_locks`;

-- Create cache table with correct column name
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create cache_locks table with correct column name
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Show tables to verify
SHOW TABLES;
