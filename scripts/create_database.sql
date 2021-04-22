-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 22, 2021 at 01:03 PM
-- Server version: 5.5.64-MariaDB
-- PHP Version: 7.2.26

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `torque_test`
--
CREATE DATABASE IF NOT EXISTS `torque_test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `torque_test`;

-- --------------------------------------------------------

--
-- Table structure for table `key_info`
--

DROP TABLE IF EXISTS `key_info`;
CREATE TABLE IF NOT EXISTS `key_info` (
  `session_id` int(11) NOT NULL,
  `key_id` varchar(10) NOT NULL,
  `long_name` varchar(60) NOT NULL,
  `short_name` varchar(30) NOT NULL,
  `default_unit` varchar(10) NOT NULL,
  `user_unit` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `raw_logs`
--

DROP TABLE IF EXISTS `raw_logs`;
CREATE TABLE IF NOT EXISTS `raw_logs` (
  `session_id` int(11) NOT NULL,
  `time` varchar(15) CHARACTER SET utf8 NOT NULL,
  `key_id` varchar(10) CHARACTER SET utf8 NOT NULL,
  `key_value` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `session_header`
--

DROP TABLE IF EXISTS `session_header`;
CREATE TABLE IF NOT EXISTS `session_header` (
  `int_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `v` varchar(1) CHARACTER SET utf8 NOT NULL,
  `session` varchar(15) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `int_id` int(11) NOT NULL,
  `eml` varchar(255) NOT NULL,
  `id` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_info`
--

DROP TABLE IF EXISTS `vehicle_info`;
CREATE TABLE IF NOT EXISTS `vehicle_info` (
  `session_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_profile`
--

DROP TABLE IF EXISTS `vehicle_profile`;
CREATE TABLE IF NOT EXISTS `vehicle_profile` (
  `int_id` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `name` int(11) NOT NULL,
  `fuel_type` tinyint(4) NOT NULL,
  `fuel_cost` float NOT NULL,
  `weight` float NOT NULL,
  `ve` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `key_info`
--
ALTER TABLE `key_info`
  ADD PRIMARY KEY (`session_id`,`key_id`),
  ADD KEY `IDX_KI_SESS` (`session_id`);

--
-- Indexes for table `raw_logs`
--
ALTER TABLE `raw_logs`
  ADD PRIMARY KEY (`session_id`,`time`),
  ADD KEY `IDX_DATA_SID` (`session_id`);

--
-- Indexes for table `session_header`
--
ALTER TABLE `session_header`
  ADD PRIMARY KEY (`int_id`),
  ADD UNIQUE KEY `v` (`user_id`,`v`,`session`) USING BTREE,
  ADD KEY `IDX_SH_UID` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`int_id`),
  ADD UNIQUE KEY `eml` (`eml`,`id`),
  ADD UNIQUE KEY `eml_2` (`eml`,`id`);

--
-- Indexes for table `vehicle_info`
--
ALTER TABLE `vehicle_info`
  ADD PRIMARY KEY (`session_id`,`vehicle_id`);

--
-- Indexes for table `vehicle_profile`
--
ALTER TABLE `vehicle_profile`
  ADD PRIMARY KEY (`int_id`),
  ADD UNIQUE KEY `owner` (`owner`,`name`,`fuel_type`,`fuel_cost`,`weight`,`ve`),
  ADD KEY `IDX_VP_OWN` (`owner`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `session_header`
--
ALTER TABLE `session_header`
  MODIFY `int_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `int_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vehicle_profile`
--
ALTER TABLE `vehicle_profile`
  MODIFY `int_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `key_info`
--
ALTER TABLE `key_info`
  ADD CONSTRAINT `FK_KI_SESS` FOREIGN KEY (`session_id`) REFERENCES `session_header` (`int_id`);

--
-- Constraints for table `session_header`
--
ALTER TABLE `session_header`
  ADD CONSTRAINT `FK_SH_UID` FOREIGN KEY (`user_id`) REFERENCES `users` (`int_id`);

--
-- Constraints for table `vehicle_info`
--
ALTER TABLE `vehicle_info`
  ADD CONSTRAINT `FK_SESS_ID` FOREIGN KEY (`session_id`) REFERENCES `session_header` (`int_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vehicle_profile`
--
ALTER TABLE `vehicle_profile`
  ADD CONSTRAINT `FK_VP_OWN` FOREIGN KEY (`owner`) REFERENCES `users` (`int_id`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
