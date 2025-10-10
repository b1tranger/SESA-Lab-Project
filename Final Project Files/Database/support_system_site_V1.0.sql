-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 25, 2025 at 02:42 PM
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
-- Database: `supporthero`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `A_ID` int(11) NOT NULL,
  `A_Name` varchar(100) DEFAULT NULL,
  `A_Phone` varchar(20) DEFAULT NULL,
  `A_Email` varchar(100) DEFAULT NULL,
  `A_Address` varchar(255) DEFAULT NULL,
  `A_Blood` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `consumer`
--

CREATE TABLE `consumer` (
  `C_ID` int(11) NOT NULL,
  `C_Name` varchar(100) DEFAULT NULL,
  `C_Phone` varchar(20) DEFAULT NULL,
  `C_Email` varchar(100) DEFAULT NULL,
  `C_Address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donor`
--

CREATE TABLE `donor` (
  `D_ID` int(11) NOT NULL,
  `D_Name` varchar(100) DEFAULT NULL,
  `D_Phone` varchar(20) DEFAULT NULL,
  `D_Email` varchar(100) DEFAULT NULL,
  `D_Address` varchar(255) DEFAULT NULL,
  `D_Blood` varchar(10) DEFAULT NULL,
  `D_Payment` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `Request_ID` int(11) NOT NULL,
  `C_ID` int(11) NOT NULL,
  `P_ID` int(11) DEFAULT NULL,
  `A_ID` int(11) DEFAULT NULL,
  `Service_Type` enum('Waste','Courier','Guard','Cleaning','Quick Delivery') NOT NULL,
  `Status` varchar(20) DEFAULT 'Pending',
  `Is_Bounty` enum('Yes','No') DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `volunteer`
--

CREATE TABLE `volunteer` (
  `P_ID` int(11) NOT NULL,
  `P_Name` varchar(100) DEFAULT NULL,
  `P_Phone` varchar(20) DEFAULT NULL,
  `P_Email` varchar(100) DEFAULT NULL,
  `P_Address` varchar(255) DEFAULT NULL,
  `P_Blood` varchar(10) DEFAULT NULL,
  `P_Rank` int(11) DEFAULT NULL,
  `P_Record` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`A_ID`);

--
-- Indexes for table `consumer`
--
ALTER TABLE `consumer`
  ADD PRIMARY KEY (`C_ID`);

--
-- Indexes for table `donor`
--
ALTER TABLE `donor`
  ADD PRIMARY KEY (`D_ID`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`Request_ID`),
  ADD KEY `C_ID` (`C_ID`),
  ADD KEY `P_ID` (`P_ID`),
  ADD KEY `A_ID` (`A_ID`);

--
-- Indexes for table `volunteer`
--
ALTER TABLE `volunteer`
  ADD PRIMARY KEY (`P_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `A_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consumer`
--
ALTER TABLE `consumer`
  MODIFY `C_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donor`
--
ALTER TABLE `donor`
  MODIFY `D_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `Request_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `volunteer`
--
ALTER TABLE `volunteer`
  MODIFY `P_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `service_ibfk_1` FOREIGN KEY (`C_ID`) REFERENCES `consumer` (`C_ID`),
  ADD CONSTRAINT `service_ibfk_2` FOREIGN KEY (`P_ID`) REFERENCES `volunteer` (`P_ID`),
  ADD CONSTRAINT `service_ibfk_3` FOREIGN KEY (`A_ID`) REFERENCES `admin` (`A_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
