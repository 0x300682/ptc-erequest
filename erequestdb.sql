-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2024 at 04:24 AM
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
-- Database: `erequestdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `studentNo` varchar(50) NOT NULL,
  `bookingID` int(50) NOT NULL,
  `bookingDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `studentNo` varchar(50) NOT NULL,
  `studentName` text NOT NULL,
  `studentEmail` varchar(100) NOT NULL,
  `studentPassword` varchar(50) NOT NULL,
  `studentCourse` varchar(100) NOT NULL,
  `studentYear` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`studentNo`, `studentName`, `studentEmail`, `studentPassword`, `studentCourse`, `studentYear`) VALUES
('2022-7235', 'Michael G. Tababa', 'michaeltababa123@gmail.com', 'Tababa', 'BSIT', '3rd Year'),
('2022-7236', 'Daniel Reyes', 'DanielReyes@gmail.com', 'Daniel123', 'BSIT', '3rd Year'),
('2022-7237', 'hello', 'hello@gmail.com', 'Tababa', 'BSIT', '3rd Year'),
('2022-7239', 'sigh', 'sigh@gmail.com', 'Tababa', 'BSIT', '3rd Year'),
('2022-7543', 'Manuel', 'Manuel@gmail.com', 'Manuel', 'BSIT', '3rd Year');

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

CREATE TABLE `request` (
  `studentNo` varchar(50) NOT NULL,
  `requestID` int(50) NOT NULL,
  `requestDocument` varchar(100) NOT NULL,
  `requestStatus` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request_booking`
--

CREATE TABLE `request_booking` (
  `id` int(11) NOT NULL,
  `bookingID` int(11) NOT NULL,
  `requestID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`bookingID`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`studentNo`);

--
-- Indexes for table `request`
--
ALTER TABLE `request`
  ADD PRIMARY KEY (`requestID`);

--
-- Indexes for table `request_booking`
--
ALTER TABLE `request_booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookingID` (`bookingID`),
  ADD KEY `requestID` (`requestID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `bookingID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `request`
--
ALTER TABLE `request`
  MODIFY `requestID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `request_booking`
--
ALTER TABLE `request_booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `request_booking`
--
ALTER TABLE `request_booking`
  ADD CONSTRAINT `request_booking_ibfk_1` FOREIGN KEY (`bookingID`) REFERENCES `booking` (`bookingID`),
  ADD CONSTRAINT `request_booking_ibfk_2` FOREIGN KEY (`requestID`) REFERENCES `request` (`requestID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
