-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 03, 2021 at 09:40 PM
-- Server version: 5.5.64-MariaDB
-- PHP Version: 7.2.26

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
-- Table structure for table `data_entries`
--

DROP TABLE IF EXISTS `data_entries`;
CREATE TABLE IF NOT EXISTS `data_entries` (
  `session_id` int(11) NOT NULL,
  `entry_pid_id` int(11) NOT NULL,
  `entry_time` datetime NOT NULL,
  `entry_value` float NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='The log data from Torque Pro.';

-- --------------------------------------------------------

--
-- Table structure for table `data_headers`
--

DROP TABLE IF EXISTS `data_headers`;
CREATE TABLE IF NOT EXISTS `data_headers` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `session_start` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `data_pids`
--

DROP TABLE IF EXISTS `data_pids`;
CREATE TABLE IF NOT EXISTS `data_pids` (
  `id` int(11) NOT NULL,
  `header_id` int(11) NOT NULL,
  `pid_id` varchar(30) NOT NULL COMMENT 'Torque Pro ID for PID, starts with "k."',
  `full_name` varchar(60) NOT NULL COMMENT 'Full name from Torque Pro.',
  `short_name` varchar(30) NOT NULL COMMENT 'Short name from Torque Pro.',
  `unit` varchar(10) NOT NULL COMMENT 'Unit from Torque Pro. If userUnit from Torque Pro is not blank, this will be that unit, otherwise it will be the defaultUnit sent will be used.'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `upload_sessions`
--

DROP TABLE IF EXISTS `upload_sessions`;
CREATE TABLE IF NOT EXISTS `upload_sessions` (
  `upload_id` varchar(36) NOT NULL,
  `session_start` datetime NOT NULL,
  `data` text NOT NULL,
  `last_access` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alias` varchar(30) DEFAULT NULL COMMENT 'A screen name.',
  `upload_id` varchar(36) NOT NULL,
  `abrp_id` varchar(36) DEFAULT NULL,
  `abrp_forward` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Owner ID',
  `name` varchar(30) NOT NULL COMMENT 'Vehicle profile name from Torque Pro',
  `fuel_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Fuel type from Torque Pro',
  `fuel_cost` float NOT NULL DEFAULT '0' COMMENT 'Fuel Cost from Torque Pro.',
  `weight` float NOT NULL DEFAULT '0' COMMENT 'Weight from Torque Pro.',
  `ve` float NOT NULL DEFAULT '0' COMMENT 'Volumetric Efficiency from Torque Pro.'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_entries`
--
ALTER TABLE `data_entries`
  ADD PRIMARY KEY (`session_id`,`entry_pid_id`,`entry_time`) USING BTREE,
  ADD KEY `fk_data_entries_data_pids` (`entry_pid_id`);

--
-- Indexes for table `data_headers`
--
ALTER TABLE `data_headers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_data_headers_vehicles` (`vehicle_id`);

--
-- Indexes for table `data_pids`
--
ALTER TABLE `data_pids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unq_data_pids_header_pid` (`header_id`,`pid_id`);

--
-- Indexes for table `upload_sessions`
--
ALTER TABLE `upload_sessions`
  ADD PRIMARY KEY (`upload_id`,`session_start`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unq_users_email` (`email`),
  ADD UNIQUE KEY `unq_users_upload_id` (`upload_id`),
  ADD UNIQUE KEY `unq_users_alias` (`alias`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unq_vehicles_user_and_name` (`user_id`,`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_headers`
--
ALTER TABLE `data_headers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `data_pids`
--
ALTER TABLE `data_pids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `data_entries`
--
ALTER TABLE `data_entries`
  ADD CONSTRAINT `fk_data_entries_data_headers` FOREIGN KEY (`session_id`) REFERENCES `data_headers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_data_entries_data_pids` FOREIGN KEY (`entry_pid_id`) REFERENCES `data_pids` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `data_headers`
--
ALTER TABLE `data_headers`
  ADD CONSTRAINT `fk_data_headers_vehicles` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `data_pids`
--
ALTER TABLE `data_pids`
  ADD CONSTRAINT `fk_data_pids_data_headers` FOREIGN KEY (`header_id`) REFERENCES `data_headers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `fk_vehicles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
