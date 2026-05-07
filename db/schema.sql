-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2026 at 01:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for table `actlog`
--

CREATE TABLE `actlog` (
  `LogID` int(11) NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `ActorID` int(11) NOT NULL,
  `Message` mediumtext NOT NULL,
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
  `AssignID` int(11) NOT NULL,
  `PropNum` char(12) NOT NULL,
  `AssignDateTime` datetime NOT NULL,
  `AssignerID` int(11) NOT NULL,
  `AssigneeID` int(11) NOT NULL,
  `ReturnDateTime` datetime DEFAULT NULL,
  `Remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `EmpID` int(11) NOT NULL,
  `EmpMail` varchar(50) NOT NULL,
  `FName` varchar(20) NOT NULL,
  `LName` varchar(20) NOT NULL,
  `Privilege` enum('Faculty','Staff','Admin','SuperAdmin') NOT NULL,
  `ActiveStatus` enum('Active','Inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actlog`
--
ALTER TABLE `actlog`
  ADD PRIMARY KEY (`LogID`),
  ADD KEY `actlog_ibfk_1` (`ActorID`);

--
-- Indexes for table `asset`
--
ALTER TABLE `asset`
  ADD PRIMARY KEY (`PropNum`);

--
-- Indexes for table `assignment`
--
ALTER TABLE `assignment`
  ADD PRIMARY KEY (`AssignID`),
  ADD KEY `assignment_ibfk_1` (`PropNum`),
  ADD KEY `assignment_ibfk_2` (`AssigneeID`),
  ADD KEY `assignment_ibfk_3` (`AssignerID`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`EmpID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actlog`
--
ALTER TABLE `actlog`
  MODIFY `LogID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assignment`
--
ALTER TABLE `assignment`
  MODIFY `AssignID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `EmpID` int(11) NOT NULL AUTO_INCREMENT;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
