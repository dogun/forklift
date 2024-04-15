-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 30, 2023 at 03:47 AM
-- Server version: 8.0.35
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `3d_world`
--

-- --------------------------------------------------------

--
-- Table structure for table `action_queue`
--

CREATE TABLE `action_queue` (
  `id` int UNSIGNED NOT NULL,
  `m_id` int NOT NULL,
  `m_type` enum('PRINTER','FORKLIFT','ADMIN','') NOT NULL,
  `action` varchar(128) NOT NULL,
  `content` varchar(255) NOT NULL,
  `created` timestamp NOT NULL,
  `modified` timestamp NOT NULL,
  `status` enum('READY','RUNNING','FINISHED','ERROR') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forklifts`
--

CREATE TABLE `forklifts` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `host` varchar(128) NOT NULL,
  `port` int NOT NULL,
  `status` enum('READY','RUNNING','ERROR','MAINTAIN') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created` timestamp NOT NULL,
  `modified` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='叉车';

--
-- Dumping data for table `forklifts`
--

INSERT INTO `forklifts` (`id`, `name`, `host`, `port`, `status`, `created`, `modified`) VALUES
(1, 'fl.1', '192.168.7.6', 80, 'READY', '2023-12-29 14:23:56', '2023-12-29 14:23:59');

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `id` int UNSIGNED NOT NULL,
  `m_id` int NOT NULL,
  `m_type` enum('PRINTER','FORKLIFT','QUEUE','') NOT NULL,
  `time` timestamp NOT NULL,
  `level` enum('DEBUG','INFO','WARN','ERROR','') NOT NULL,
  `log` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `printers`
--

CREATE TABLE `printers` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(128) NOT NULL,
  `host` varchar(128) NOT NULL,
  `port` int NOT NULL,
  `forklift_id` int NOT NULL,
  `status` enum('READY','RUNNING','ERROR','MAINTAIN') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `memo` varchar(255) NOT NULL,
  `created` timestamp NOT NULL,
  `modified` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='打印机';

--
-- Dumping data for table `printers`
--

INSERT INTO `printers` (`id`, `name`, `host`, `port`, `forklift_id`, `status`, `memo`, `created`, `modified`) VALUES
(1, 'NO.0', '192.168.7.3', 7126, 1, 'READY', '', '2023-12-29 14:23:09', '2023-12-29 14:23:33'),
(2, 'NO.1', '192.168.7.3', 7125, 1, 'READY', '', '2023-12-29 14:23:21', '2023-12-29 14:23:37'),
(3, 'Z603S', '192.168.7.16', 7125, 0, 'READY', '', '2023-12-29 14:23:25', '2023-12-29 14:23:40'),
(4, 'NO.2', '192.168.7.7', 7125, 1, 'READY', '', '2023-12-29 14:23:29', '2023-12-29 14:23:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `action_queue`
--
ALTER TABLE `action_queue`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forklifts`
--
ALTER TABLE `forklifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `printers`
--
ALTER TABLE `printers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `action_queue`
--
ALTER TABLE `action_queue`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `forklifts`
--
ALTER TABLE `forklifts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `printers`
--
ALTER TABLE `printers`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
