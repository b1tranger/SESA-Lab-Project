-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2025 at 11:34 AM
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
  `A_ID` bigint(11) NOT NULL,
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
  `C_ID` bigint(11) NOT NULL,
  `C_Name` varchar(100) DEFAULT NULL,
  `C_Phone` varchar(20) DEFAULT NULL,
  `C_Email` varchar(100) DEFAULT NULL,
  `C_Address` varchar(255) DEFAULT NULL,
  `C_Blood` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `D_ID` bigint(11) DEFAULT NULL,
  `Amount` bigint(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donor`
--

CREATE TABLE `donor` (
  `D_ID` bigint(11) NOT NULL,
  `D_Name` varchar(100) DEFAULT NULL,
  `D_Phone` varchar(20) DEFAULT NULL,
  `D_Email` varchar(100) DEFAULT NULL,
  `D_Address` varchar(255) DEFAULT NULL,
  `D_Blood` varchar(10) DEFAULT NULL,
  `D_Payment` bigint(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `Token` varchar(50) NOT NULL,
  `User_Type` varchar(50) NOT NULL,
  `User_ID` bigint(11) DEFAULT NULL,
  `Feedback` text NOT NULL,
  `Urgency` enum('less important','important') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `P_ID` bigint(11) DEFAULT NULL,
  `offer` varchar(200) DEFAULT NULL,
  `Location` varchar(200) DEFAULT NULL,
  `Status` enum('Pending','Accepted','Completed','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `Request_ID` bigint(11) NOT NULL,
  `C_ID` bigint(11) NOT NULL,
  `P_ID` bigint(11) DEFAULT NULL,
  `A_ID` bigint(11) DEFAULT NULL,
  `Service_Type` enum('Waste','Courier','Guard','Cleaning','Quick Delivery') NOT NULL,
  `Status` enum('Pending','Accepted','Completed','Rejected') DEFAULT 'Pending',
  `Is_Bounty` enum('Yes','No') DEFAULT 'No',
  `Deadline` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_provider`
--

CREATE TABLE `service_provider` (
  `P_ID` bigint(11) NOT NULL,
  `P_Name` varchar(100) DEFAULT NULL,
  `P_Phone` varchar(20) DEFAULT NULL,
  `P_Email` varchar(100) DEFAULT NULL,
  `P_Address` varchar(255) DEFAULT NULL,
  `P_Blood` varchar(10) DEFAULT NULL,
  `P_Rank` bigint(11) DEFAULT NULL,
  `P_Record` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visitor`
--

CREATE TABLE `visitor` (
  `V_ID` bigint(10) NOT NULL,
  `V_Name` varchar(20) NOT NULL
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
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD KEY `D_ID` (`D_ID`);

--
-- Indexes for table `donor`
--
ALTER TABLE `donor`
  ADD PRIMARY KEY (`D_ID`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`Token`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD KEY `P_ID` (`P_ID`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`Request_ID`),
  ADD KEY `C_ID` (`C_ID`),
  ADD KEY `P_ID` (`P_ID`),
  ADD KEY `A_ID` (`A_ID`);

--
-- Indexes for table `service_provider`
--
ALTER TABLE `service_provider`
  ADD PRIMARY KEY (`P_ID`);

--
-- Indexes for table `visitor`
--
ALTER TABLE `visitor`
  ADD PRIMARY KEY (`V_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `A_ID` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consumer`
--
ALTER TABLE `consumer`
  MODIFY `C_ID` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donor`
--
ALTER TABLE `donor`
  MODIFY `D_ID` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `Request_ID` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_provider`
--
ALTER TABLE `service_provider`
  MODIFY `P_ID` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visitor`
--
ALTER TABLE `visitor`
  MODIFY `V_ID` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_ibfk_1` FOREIGN KEY (`D_ID`) REFERENCES `donor` (`D_ID`);

--
-- Constraints for table `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `offers_ibfk_1` FOREIGN KEY (`P_ID`) REFERENCES `service_provider` (`P_ID`);

--
-- Constraints for table `service`
--
ALTER TABLE `service`
  ADD CONSTRAINT `service_ibfk_1` FOREIGN KEY (`C_ID`) REFERENCES `consumer` (`C_ID`),
  ADD CONSTRAINT `service_ibfk_2` FOREIGN KEY (`P_ID`) REFERENCES `service_provider` (`P_ID`),
  ADD CONSTRAINT `service_ibfk_3` FOREIGN KEY (`A_ID`) REFERENCES `admin` (`A_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
