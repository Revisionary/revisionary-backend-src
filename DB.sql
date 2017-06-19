-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 19, 2017 at 03:04 AM
-- Server version: 5.6.35
-- PHP Version: 7.1.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `revisionary_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `cat_ID` bigint(20) NOT NULL,
  `cat_type` varchar(10) NOT NULL,
  `cat_name` varchar(200) NOT NULL,
  `cat_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_ID`, `cat_type`, `cat_name`, `cat_user_ID`) VALUES
(1, 'project', 'Test Category', 1),
(2, 'project', 'Hello', 1);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_ID` bigint(20) NOT NULL,
  `project_name` varchar(200) NOT NULL,
  `project_pic` varchar(15) DEFAULT NULL,
  `project_archived` tinyint(1) NOT NULL DEFAULT '0',
  `project_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `project_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_ID`, `project_name`, `project_pic`, `project_archived`, `project_deleted`, `project_created`, `user_ID`) VALUES
(1, 'Bilal TAS', 'bilal-tas.png', 0, 0, '2017-06-18 15:21:32', 1),
(2, 'SoundCloud', 'soundcloud.png', 0, 0, '2017-06-18 15:26:34', 1),
(3, 'TWSJ', 'twsj.png', 0, 0, '2017-06-18 15:27:35', 1),
(4, 'Youtube', 'youtube.png', 0, 0, '2017-06-18 15:28:12', 1),
(5, 'Cuneyt TAS', 'cuneyt-tas.png', 0, 0, '2017-06-18 15:28:34', 4),
(6, 'BBC', 'bbc.png', 1, 0, '2017-06-18 15:29:25', 1),
(7, 'Envato', 'envato.png', 0, 0, '2017-06-18 15:29:25', 1),
(8, 'Twelve12', 'twelve12.png', 0, 0, '2017-06-18 17:54:24', 2);

-- --------------------------------------------------------

--
-- Table structure for table `project_cat_connect`
--

CREATE TABLE `project_cat_connect` (
  `project_cat_connect_ID` bigint(20) NOT NULL,
  `project_cat_project_ID` bigint(20) NOT NULL,
  `project_cat_ID` bigint(20) NOT NULL,
  `project_cat_connect_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `project_cat_connect`
--

INSERT INTO `project_cat_connect` (`project_cat_connect_ID`, `project_cat_project_ID`, `project_cat_ID`, `project_cat_connect_user_ID`) VALUES
(1, 1, 1, 1),
(2, 7, 1, 1),
(5, 8, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shares`
--

CREATE TABLE `shares` (
  `share_ID` bigint(20) NOT NULL,
  `share_type` varchar(10) NOT NULL,
  `shared_object_ID` bigint(20) NOT NULL,
  `share_to` varchar(20) NOT NULL,
  `sharer_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `shares`
--

INSERT INTO `shares` (`share_ID`, `share_type`, `shared_object_ID`, `share_to`, `sharer_user_ID`) VALUES
(1, 'project', 8, '1', 2);

-- --------------------------------------------------------

--
-- Table structure for table `sorting`
--

CREATE TABLE `sorting` (
  `sort_ID` bigint(20) NOT NULL,
  `sort_type` varchar(15) NOT NULL,
  `sort_object_ID` bigint(20) NOT NULL,
  `sort_number` bigint(20) NOT NULL DEFAULT '0',
  `sorter_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_ID` bigint(20) NOT NULL,
  `user_name` varchar(25) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_first_name` varchar(20) NOT NULL,
  `user_last_name` varchar(20) NOT NULL,
  `user_picture` varchar(15) DEFAULT NULL,
  `user_has_public_profile` tinyint(1) NOT NULL DEFAULT '0',
  `user_registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_level_ID` smallint(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_ID`, `user_name`, `user_email`, `user_password`, `user_first_name`, `user_last_name`, `user_picture`, `user_has_public_profile`, `user_registered`, `user_level_ID`) VALUES
(1, 'bilaltas', 'bilaltas@me.com', '$2y$10$FlJ0PwBy6.5m8MXqIDMv5u.CsTW9w7bEgmlzLUCG9il6ZaN6KMmVC', 'Bilal', 'TAS', 'bill.png', 1, '2017-03-31 22:18:00', 1),
(2, 'ike', 'ike@twelve12.com', '$2y$10$UcoctpiTNtf9grzFmz53lut7X4l3EBspRC4xz/Qn/qJ1VWWIq81n.', 'Ike', 'Elimsa', 'ike.png', 0, '2017-06-17 22:28:00', 4),
(3, 'sara', 'sara@twelve12.com', '$2y$10$a7njFOCmPx.hJVvunpqeBe6HskuWJiubX0rawUDxvpP.s3VX4/CI2', 'Sara', 'Atalay', 'sara.png', 0, '2017-06-17 22:28:00', 3),
(4, 'cuneyt-tas', 'cuneyttas@hotmail.com.tr', '$2y$10$XIXCUmV38NyLeh13EiqynuuywixfNkWOt6.S5Llezkz1E1WBQQVDa', 'Cüneyt', 'TAŞ', NULL, 0, '2017-06-18 13:51:00', 2);

-- --------------------------------------------------------

--
-- Table structure for table `user_levels`
--

CREATE TABLE `user_levels` (
  `user_level_ID` smallint(5) NOT NULL,
  `user_level_name` varchar(10) NOT NULL,
  `user_level_description` text NOT NULL,
  `user_level_max_project` int(5) NOT NULL,
  `user_level_max_page` int(5) NOT NULL,
  `user_level_max_live_pin` int(5) NOT NULL,
  `user_level_max_standard_pin` int(5) NOT NULL,
  `user_level_max_client` int(5) NOT NULL,
  `user_level_max_load` int(5) NOT NULL,
  `user_level_price` float NOT NULL,
  `user_level_color` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_ID`),
  ADD KEY `project_cat_user_ID` (`cat_user_ID`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_ID`),
  ADD KEY `user_ID` (`user_ID`);

--
-- Indexes for table `project_cat_connect`
--
ALTER TABLE `project_cat_connect`
  ADD PRIMARY KEY (`project_cat_connect_ID`),
  ADD KEY `project_cat_user_ID` (`project_cat_connect_user_ID`),
  ADD KEY `project_cat_project_ID` (`project_cat_project_ID`),
  ADD KEY `project_cat_ID` (`project_cat_ID`);

--
-- Indexes for table `shares`
--
ALTER TABLE `shares`
  ADD PRIMARY KEY (`share_ID`),
  ADD KEY `user_ID` (`sharer_user_ID`);

--
-- Indexes for table `sorting`
--
ALTER TABLE `sorting`
  ADD PRIMARY KEY (`sort_ID`),
  ADD KEY `sorter_user_ID` (`sorter_user_ID`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `project_cat_connect`
--
ALTER TABLE `project_cat_connect`
  MODIFY `project_cat_connect_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `shares`
--
ALTER TABLE `shares`
  MODIFY `share_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `sorting`
--
ALTER TABLE `sorting`
  MODIFY `sort_ID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `user_levels`
--
ALTER TABLE `user_levels`
  MODIFY `user_level_ID` smallint(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`cat_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `project_cat_connect`
--
ALTER TABLE `project_cat_connect`
  ADD CONSTRAINT `project_cat_connect_ibfk_1` FOREIGN KEY (`project_cat_connect_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `project_cat_connect_ibfk_2` FOREIGN KEY (`project_cat_project_ID`) REFERENCES `projects` (`project_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `project_cat_connect_ibfk_3` FOREIGN KEY (`project_cat_ID`) REFERENCES `categories` (`cat_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `shares`
--
ALTER TABLE `shares`
  ADD CONSTRAINT `shares_ibfk_1` FOREIGN KEY (`sharer_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `sorting`
--
ALTER TABLE `sorting`
  ADD CONSTRAINT `sorting_ibfk_1` FOREIGN KEY (`sorter_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_level` FOREIGN KEY (`user_level_ID`) REFERENCES `user_levels` (`user_level_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
