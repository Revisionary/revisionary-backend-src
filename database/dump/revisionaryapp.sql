-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Dec 17, 2018 at 08:45 AM
-- Server version: 8.0.13
-- PHP Version: 7.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `revisionaryapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `archives`
--

CREATE TABLE `archives` (
  `archive_ID` bigint(20) NOT NULL,
  `archive_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived_object_ID` bigint(20) NOT NULL,
  `archiver_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_ID` bigint(20) NOT NULL,
  `cat_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cat_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cat_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deletes`
--

CREATE TABLE `deletes` (
  `delete_ID` bigint(20) NOT NULL,
  `delete_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_object_ID` bigint(20) NOT NULL,
  `deleter_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `device_ID` bigint(20) NOT NULL,
  `device_width` mediumint(10) DEFAULT NULL,
  `device_height` mediumint(10) DEFAULT NULL,
  `device_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `device_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `page_ID` bigint(20) NOT NULL,
  `screen_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `page_ID` bigint(20) NOT NULL,
  `page_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_url` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_user` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_pass` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page_internalized` int(20) NOT NULL DEFAULT '0',
  `project_ID` bigint(20) NOT NULL,
  `user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_cat_connect`
--

CREATE TABLE `page_cat_connect` (
  `page_cat_connect_ID` bigint(20) NOT NULL,
  `page_cat_page_ID` bigint(20) NOT NULL,
  `page_cat_ID` bigint(20) NOT NULL,
  `page_cat_connect_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pins`
--

CREATE TABLE `pins` (
  `pin_ID` bigint(20) NOT NULL,
  `pin_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pin_private` tinyint(1) NOT NULL DEFAULT '0',
  `pin_complete` tinyint(1) NOT NULL DEFAULT '0',
  `pin_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pin_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pin_x` decimal(20,5) NOT NULL,
  `pin_y` decimal(20,5) NOT NULL,
  `pin_element_index` bigint(20) NOT NULL,
  `pin_modification_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin_modification` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pin_modification_original` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `device_ID` bigint(20) NOT NULL,
  `user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pin_comments`
--

CREATE TABLE `pin_comments` (
  `comment_ID` bigint(20) NOT NULL,
  `pin_comment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pin_ID` bigint(20) NOT NULL,
  `user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_ID` bigint(20) NOT NULL,
  `project_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_cat_connect`
--

CREATE TABLE `project_cat_connect` (
  `project_cat_connect_ID` bigint(20) NOT NULL,
  `project_cat_project_ID` bigint(20) NOT NULL,
  `project_cat_ID` bigint(20) NOT NULL,
  `project_cat_connect_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `queues`
--

CREATE TABLE `queues` (
  `queue_ID` bigint(20) NOT NULL,
  `queue_type` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `queue_object_ID` bigint(20) NOT NULL,
  `queue_PID` bigint(20) DEFAULT NULL,
  `queue_status` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `queue_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `queue_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `queue_message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `screens`
--

CREATE TABLE `screens` (
  `screen_ID` bigint(20) NOT NULL,
  `screen_name` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `screen_width` mediumint(10) NOT NULL,
  `screen_height` mediumint(10) NOT NULL,
  `screen_rotateable` tinyint(1) NOT NULL DEFAULT '0',
  `screen_color` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screen_frame` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screen_cat_ID` bigint(20) NOT NULL,
  `screen_order` bigint(20) NOT NULL,
  `screen_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `screens`
--

INSERT INTO `screens` (`screen_ID`, `screen_name`, `screen_width`, `screen_height`, `screen_rotateable`, `screen_color`, `screen_frame`, `screen_cat_ID`, `screen_order`, `screen_user_ID`) VALUES
(1, 'iMac 27', 2560, 1440, 0, NULL, NULL, 1, 0, 1),
(2, 'iMac 21', 1920, 1080, 0, NULL, NULL, 1, 1, 1),
(3, 'Macbook Pro 17', 1920, 1200, 0, NULL, NULL, 2, 2, 1),
(4, 'Macbook Pro 15', 1440, 900, 0, NULL, NULL, 2, 3, 1),
(5, 'Macbook Pro 13', 1280, 800, 0, NULL, NULL, 2, 4, 1),
(6, 'iPad', 768, 1024, 1, NULL, NULL, 3, 0, 1),
(7, 'iPhone 6 Plus, 6S Plus, 7 Plus', 414, 736, 1, NULL, NULL, 4, 0, 1),
(8, 'iPhone 6, 6S, 7', 375, 667, 1, NULL, NULL, 4, 1, 1),
(9, 'iPhone 5, 5C, 5S, SE', 320, 568, 1, NULL, NULL, 4, 2, 1),
(10, 'iPhone 4 & 4S', 320, 480, 1, NULL, NULL, 4, 3, 1),
(11, 'Custom Screen', 1440, 900, 0, NULL, NULL, 5, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `screen_categories`
--

CREATE TABLE `screen_categories` (
  `screen_cat_ID` bigint(20) NOT NULL,
  `screen_cat_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `screen_cat_icon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `screen_cat_order` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `screen_categories`
--

INSERT INTO `screen_categories` (`screen_cat_ID`, `screen_cat_name`, `screen_cat_icon`, `screen_cat_order`) VALUES
(1, 'Desktop', 'fa-desktop', 0),
(2, 'Laptop', 'fa-laptop', 1),
(3, 'Tablet', 'fa-tablet', 2),
(4, 'Mobile', 'fa-mobile', 3),
(5, 'Custom', 'fa-window-maximize', 4);

-- --------------------------------------------------------

--
-- Table structure for table `shares`
--

CREATE TABLE `shares` (
  `share_ID` bigint(20) NOT NULL,
  `share_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `shared_object_ID` bigint(20) NOT NULL,
  `share_to` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sharer_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sorting`
--

CREATE TABLE `sorting` (
  `sort_ID` bigint(20) NOT NULL,
  `sort_type` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_object_ID` bigint(20) NOT NULL,
  `sort_number` bigint(20) NOT NULL DEFAULT '0',
  `sorter_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_ID` bigint(20) NOT NULL,
  `user_name` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_first_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_last_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_picture` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_has_public_profile` tinyint(1) NOT NULL DEFAULT '0',
  `user_registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_level_ID` smallint(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_ID`, `user_name`, `user_email`, `user_password`, `user_first_name`, `user_last_name`, `user_picture`, `user_has_public_profile`, `user_level_ID`) VALUES
(1, 'bilaltas', 'bill@twelve12.com', '$2y$10$FlJ0PwBy6.5m8MXqIDMv5u.CsTW9w7bEgmlzLUCG9il6ZaN6KMmVC', 'Bilal', 'TAS', 'bill.png', 1, 1),
(2, 'ike-elimsa', 'ike@twelve12.com', '$2y$10$b/jC7podSCVz6yIIKag41.1fa67xvB2utqWWVhogD7C1wkhErLU5C', 'Ike', 'Elimsa', 'ike.png', 0, 2),
(3, 'sara-atalay', 'sara@twelve12.com', '$2y$10$thYPVobp2UtoOZUi.mxZ1enGFAIhPdJ.WS1Br.9DPTdwVCh0DCq7e', 'Sara', 'Atalay', 'sara.png', 0, 2),
(4, 'matt', 'matt@twelve12.com', '$2y$10$tgVR/dS1I6X0MECfKYeqdunBaneuqLe3laoEHz1srrj6Ob5pfc2Hi', 'Matt', '', 'matt.png', 0, 2),
(5, 'cuneyt-tas', 'cuneyttas@hotmail.com.tr', '$2y$10$9bQ9GeucyXhyo3sceXz3TOeeN47qQ6mHr7alDrHPmCCTKVIj8saTG', 'Cüneyt', 'TAŞ', 'joey.png', 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_levels`
--

CREATE TABLE `user_levels` (
  `user_level_ID` smallint(5) NOT NULL,
  `user_level_name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_level_description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_level_max_project` int(5) NOT NULL,
  `user_level_max_page` int(5) NOT NULL,
  `user_level_max_live_pin` int(5) NOT NULL,
  `user_level_max_standard_pin` int(5) NOT NULL,
  `user_level_max_client` int(5) NOT NULL,
  `user_level_max_load` int(5) NOT NULL,
  `user_level_price` float NOT NULL,
  `user_level_color` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_levels`
--

INSERT INTO `user_levels` (`user_level_ID`, `user_level_name`, `user_level_description`, `user_level_max_project`, `user_level_max_page`, `user_level_max_live_pin`, `user_level_max_standard_pin`, `user_level_max_client`, `user_level_max_load`, `user_level_price`, `user_level_color`) VALUES
(1, 'Admin', 'Admin Description', 99999, 99999, 99999, 99999, 99999, 99999, 99999, 'black'),
(2, 'Free', 'Free users', 3, 6, 30, 99999, 0, 30, 0, 'black'),
(3, 'Plus', 'Plus description', 12, 24, 99999, 99999, 3, 120, 9.99, 'green'),
(4, 'Enterprise', 'Enterprise description.', 99999, 99999, 99999, 99999, 99999, 2048, 19.99, 'gold');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `archives`
--
ALTER TABLE `archives`
  ADD PRIMARY KEY (`archive_ID`),
  ADD KEY `archives_ibfk_1` (`archiver_user_ID`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_ID`),
  ADD KEY `categories_ibfk_1` (`cat_user_ID`);

--
-- Indexes for table `deletes`
--
ALTER TABLE `deletes`
  ADD PRIMARY KEY (`delete_ID`),
  ADD KEY `deletes_ibfk_1` (`deleter_user_ID`);

--
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`device_ID`),
  ADD KEY `devices_ibfk_1` (`page_ID`),
  ADD KEY `devices_ibfk_2` (`screen_ID`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_ID`),
  ADD KEY `user_ID` (`user_ID`) USING BTREE,
  ADD KEY `project_ID` (`project_ID`) USING BTREE;

--
-- Indexes for table `page_cat_connect`
--
ALTER TABLE `page_cat_connect`
  ADD PRIMARY KEY (`page_cat_connect_ID`),
  ADD KEY `page_cat_connect_ibfk_1` (`page_cat_connect_user_ID`),
  ADD KEY `page_cat_connect_ibfk_2` (`page_cat_ID`),
  ADD KEY `page_cat_connect_ibfk_3` (`page_cat_page_ID`);

--
-- Indexes for table `pins`
--
ALTER TABLE `pins`
  ADD PRIMARY KEY (`pin_ID`),
  ADD KEY `user_ID` (`user_ID`),
  ADD KEY `device_ID` (`device_ID`) USING BTREE;

--
-- Indexes for table `pin_comments`
--
ALTER TABLE `pin_comments`
  ADD PRIMARY KEY (`comment_ID`),
  ADD KEY `pin_ID` (`pin_ID`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_ID`),
  ADD KEY `projects_ibfk_3` (`user_ID`);

--
-- Indexes for table `project_cat_connect`
--
ALTER TABLE `project_cat_connect`
  ADD PRIMARY KEY (`project_cat_connect_ID`),
  ADD KEY `project_cat_connect_ibfk_3` (`project_cat_ID`),
  ADD KEY `project_cat_connect_ibfk_1` (`project_cat_connect_user_ID`),
  ADD KEY `project_cat_connect_ibfk_2` (`project_cat_project_ID`);

--
-- Indexes for table `queues`
--
ALTER TABLE `queues`
  ADD PRIMARY KEY (`queue_ID`);

--
-- Indexes for table `screens`
--
ALTER TABLE `screens`
  ADD PRIMARY KEY (`screen_ID`) USING BTREE,
  ADD KEY `screen_cat_ID` (`screen_cat_ID`) USING BTREE,
  ADD KEY `screen_user_ID` (`screen_user_ID`) USING BTREE;

--
-- Indexes for table `screen_categories`
--
ALTER TABLE `screen_categories`
  ADD PRIMARY KEY (`screen_cat_ID`) USING BTREE;

--
-- Indexes for table `shares`
--
ALTER TABLE `shares`
  ADD PRIMARY KEY (`share_ID`),
  ADD KEY `shares_ibfk_1` (`sharer_user_ID`);

--
-- Indexes for table `sorting`
--
ALTER TABLE `sorting`
  ADD PRIMARY KEY (`sort_ID`),
  ADD KEY `sorting_ibfk_1` (`sorter_user_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_ID`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `user_email` (`user_email`),
  ADD KEY `fk_user_level` (`user_level_ID`);

--
-- Indexes for table `user_levels`
--
ALTER TABLE `user_levels`
  ADD PRIMARY KEY (`user_level_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `archives`
--
ALTER TABLE `archives`
  MODIFY `archive_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deletes`
--
ALTER TABLE `deletes`
  MODIFY `delete_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `device_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `page_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_cat_connect`
--
ALTER TABLE `page_cat_connect`
  MODIFY `page_cat_connect_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pins`
--
ALTER TABLE `pins`
  MODIFY `pin_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pin_comments`
--
ALTER TABLE `pin_comments`
  MODIFY `comment_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_cat_connect`
--
ALTER TABLE `project_cat_connect`
  MODIFY `project_cat_connect_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `queues`
--
ALTER TABLE `queues`
  MODIFY `queue_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `screens`
--
ALTER TABLE `screens`
  MODIFY `screen_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `screen_categories`
--
ALTER TABLE `screen_categories`
  MODIFY `screen_cat_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shares`
--
ALTER TABLE `shares`
  MODIFY `share_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sorting`
--
ALTER TABLE `sorting`
  MODIFY `sort_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_levels`
--
ALTER TABLE `user_levels`
  MODIFY `user_level_ID` smallint(5) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `archives`
--
ALTER TABLE `archives`
  ADD CONSTRAINT `archives_ibfk_1` FOREIGN KEY (`archiver_user_ID`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`cat_user_ID`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `deletes`
--
ALTER TABLE `deletes`
  ADD CONSTRAINT `deletes_ibfk_1` FOREIGN KEY (`deleter_user_ID`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`page_ID`) REFERENCES `pages` (`page_id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `devices_ibfk_2` FOREIGN KEY (`screen_ID`) REFERENCES `screens` (`screen_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pages_ibfk_2` FOREIGN KEY (`project_ID`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE;

--
-- Constraints for table `page_cat_connect`
--
ALTER TABLE `page_cat_connect`
  ADD CONSTRAINT `page_cat_connect_ibfk_1` FOREIGN KEY (`page_cat_connect_user_ID`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `page_cat_connect_ibfk_2` FOREIGN KEY (`page_cat_ID`) REFERENCES `categories` (`cat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `page_cat_connect_ibfk_3` FOREIGN KEY (`page_cat_page_ID`) REFERENCES `pages` (`page_id`) ON DELETE CASCADE;

--
-- Constraints for table `pins`
--
ALTER TABLE `pins`
  ADD CONSTRAINT `pins_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pins_ibfk_2` FOREIGN KEY (`device_ID`) REFERENCES `devices` (`device_id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `pin_comments`
--
ALTER TABLE `pin_comments`
  ADD CONSTRAINT `pin_comments_ibfk_1` FOREIGN KEY (`pin_ID`) REFERENCES `pins` (`pin_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pin_comments_ibfk_2` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `project_cat_connect`
--
ALTER TABLE `project_cat_connect`
  ADD CONSTRAINT `project_cat_connect_ibfk_1` FOREIGN KEY (`project_cat_connect_user_ID`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_cat_connect_ibfk_2` FOREIGN KEY (`project_cat_project_ID`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_cat_connect_ibfk_3` FOREIGN KEY (`project_cat_ID`) REFERENCES `categories` (`cat_id`) ON DELETE CASCADE;

--
-- Constraints for table `screens`
--
ALTER TABLE `screens`
  ADD CONSTRAINT `screens_ibfk_1` FOREIGN KEY (`screen_cat_ID`) REFERENCES `screen_categories` (`screen_cat_id`),
  ADD CONSTRAINT `screens_ibfk_2` FOREIGN KEY (`screen_user_ID`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `shares`
--
ALTER TABLE `shares`
  ADD CONSTRAINT `shares_ibfk_1` FOREIGN KEY (`sharer_user_ID`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `sorting`
--
ALTER TABLE `sorting`
  ADD CONSTRAINT `sorting_ibfk_1` FOREIGN KEY (`sorter_user_ID`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_level` FOREIGN KEY (`user_level_ID`) REFERENCES `user_levels` (`user_level_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
