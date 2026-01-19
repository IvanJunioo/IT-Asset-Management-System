-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2026 at 11:11 AM
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
-- Database: `itam`
--
CREATE DATABASE IF NOT EXISTS itam;
USE itam;

-- --------------------------------------------------------

--
-- Table structure for table `actlog`
--

CREATE TABLE `actlog` (
  `Timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `ActorID` char(8) NOT NULL,
  `Log` mediumtext NOT NULL,
  `Metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`Metadata`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `asset`
--

CREATE TABLE `asset` (
  `PropNum` char(12) NOT NULL,
  `SerialNum` char(12) NOT NULL,
  `ProcNum` char(12) NOT NULL,
  `PurchaseDate` date NOT NULL,
  `Specs` varchar(420) NOT NULL,
  `Remarks` tinytext NOT NULL,
  `Status` enum('Unassigned','Assigned','ToCondemn','Condemned') NOT NULL,
  `ShortDesc` text NOT NULL,
  `Price` decimal(12,2) NOT NULL,
  `URL` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignment`
--

CREATE TABLE `assignment` (
  `PropNum` char(12) NOT NULL,
  `AssignDateTime` datetime NOT NULL,
  `AssignerID` char(8) NOT NULL,
  `AssigneeID` char(8) NOT NULL,
  `ReturnDateTime` datetime DEFAULT NULL,
  `Remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `empcontact`
--

CREATE TABLE `empcontact` (
  `EmpID` char(8) NOT NULL,
  `ContactNum` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `EmpID` char(8) NOT NULL,
  `EmpMail` varchar(50) NOT NULL,
  `FName` varchar(20) NOT NULL,
  `MName` varchar(20) NOT NULL,
  `LName` varchar(20) NOT NULL,
  `Privilege` enum('Faculty','Admin','SuperAdmin') NOT NULL,
  `ActiveStatus` enum('Active','Inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actlog`
--
ALTER TABLE `actlog`
  ADD PRIMARY KEY (`Timestamp`,`ActorID`),
  ADD KEY `ActorID` (`ActorID`);

--
-- Indexes for table `asset`
--
ALTER TABLE `asset`
  ADD PRIMARY KEY (`PropNum`);

--
-- Indexes for table `assignment`
--
ALTER TABLE `assignment`
  ADD PRIMARY KEY (`PropNum`,`AssignDateTime`),
  ADD KEY `AssigneeID` (`AssigneeID`),
  ADD KEY `AssignerID` (`AssignerID`);

--
-- Indexes for table `empcontact`
--
ALTER TABLE `empcontact`
  ADD PRIMARY KEY (`EmpID`,`ContactNum`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`EmpID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `actlog`
--
ALTER TABLE `actlog`
  ADD CONSTRAINT `actlog_ibfk_1` FOREIGN KEY (`ActorID`) REFERENCES `employee` (`EmpID`);

--
-- Constraints for table `assignment`
--
ALTER TABLE `assignment`
  ADD CONSTRAINT `assignment_ibfk_1` FOREIGN KEY (`PropNum`) REFERENCES `asset` (`PropNum`),
  ADD CONSTRAINT `assignment_ibfk_2` FOREIGN KEY (`AssigneeID`) REFERENCES `employee` (`EmpID`),
  ADD CONSTRAINT `assignment_ibfk_3` FOREIGN KEY (`AssignerID`) REFERENCES `employee` (`EmpID`);

--
-- Constraints for table `empcontact`
--
ALTER TABLE `empcontact`
  ADD CONSTRAINT `empcontact_ibfk_1` FOREIGN KEY (`EmpID`) REFERENCES `employee` (`EmpID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
