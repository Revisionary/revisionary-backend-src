-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 10, 2017 at 03:03 PM
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
  `cat_name` varchar(200) NOT NULL,
  `cat_type` varchar(20) NOT NULL,
  `cat_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_ID`, `cat_name`, `cat_type`, `cat_user_ID`) VALUES
(1, 'Twelve12 Related', 'project', 1),
(2, 'Hello', 'project', 1),
(3, 'Main Pages', '8', 1),
(4, 'Portfolio Pages', '8', 1),
(5, 'Blog Pages', '8', 1);

-- --------------------------------------------------------

--
-- Table structure for table `devices`
--

CREATE TABLE `devices` (
  `device_ID` bigint(20) NOT NULL,
  `device_name` text NOT NULL,
  `device_width` mediumint(10) NOT NULL,
  `device_height` mediumint(10) NOT NULL,
  `device_rotateable` tinyint(1) NOT NULL DEFAULT '0',
  `device_color` varchar(10) DEFAULT NULL,
  `device_frame` varchar(15) DEFAULT NULL,
  `device_cat_ID` bigint(20) NOT NULL,
  `device_order` bigint(20) NOT NULL,
  `device_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `devices`
--

INSERT INTO `devices` (`device_ID`, `device_name`, `device_width`, `device_height`, `device_rotateable`, `device_color`, `device_frame`, `device_cat_ID`, `device_order`, `device_user_ID`) VALUES
(1, 'iMac 27', 2560, 1440, 0, NULL, NULL, 1, 0, 1),
(2, 'iMac 21', 1920, 1080, 0, NULL, NULL, 1, 1, 1),
(3, 'Macbook Pro 17', 1920, 1200, 0, NULL, NULL, 1, 2, 1),
(4, 'Macbook Pro 15', 1440, 900, 0, NULL, NULL, 1, 3, 1),
(5, 'Macbook Pro 13', 1280, 800, 0, NULL, NULL, 1, 4, 1),
(6, 'iPad', 768, 1024, 1, NULL, NULL, 2, 0, 1),
(7, 'iPhone 6 Plus, 6S Plus, 7 Plus', 414, 736, 1, NULL, NULL, 3, 0, 1),
(8, 'iPhone 6, 6S, 7', 375, 667, 1, NULL, NULL, 3, 1, 1),
(9, 'iPhone 5, 5C, 5S, SE', 320, 568, 1, NULL, NULL, 3, 2, 1),
(10, 'iPhone 4 & 4S', 320, 480, 1, NULL, NULL, 3, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `device_categories`
--

CREATE TABLE `device_categories` (
  `device_cat_ID` bigint(20) NOT NULL,
  `device_cat_name` varchar(20) NOT NULL,
  `device_cat_icon` varchar(20) NOT NULL,
  `device_cat_order` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `device_categories`
--

INSERT INTO `device_categories` (`device_cat_ID`, `device_cat_name`, `device_cat_icon`, `device_cat_order`) VALUES
(1, 'Desktop', 'fa-desktop', 0),
(2, 'Tablet', 'fa-tablet', 1),
(3, 'Mobile', 'fa-mobile', 2),
(4, 'Custom', 'fa-window-restore', 3);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `page_ID` bigint(20) NOT NULL,
  `page_name` varchar(200) NOT NULL,
  `page_pic` varchar(15) NOT NULL,
  `page_url` text NOT NULL,
  `page_archived` tinyint(1) NOT NULL DEFAULT '0',
  `page_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `page_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `project_ID` bigint(20) NOT NULL,
  `device_ID` bigint(20) NOT NULL,
  `parent_page_ID` bigint(20) DEFAULT NULL,
  `user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`page_ID`, `page_name`, `page_pic`, `page_url`, `page_archived`, `page_deleted`, `page_created`, `page_modified`, `project_ID`, `device_ID`, `parent_page_ID`, `user_ID`) VALUES
(1, 'About', 'about.png', 'https://www.twelve12.com/about-us/', 0, 0, '2017-06-24 14:20:44', '2017-06-24 14:20:44', 8, 4, NULL, 1),
(2, 'Contact', 'contact.png', 'https://www.twelve12.com/contact/', 0, 0, '2017-06-24 14:20:44', '2017-06-24 14:20:44', 8, 4, NULL, 1),
(3, 'GM Properties', 'gm.png', 'https://www.twelve12.com/project/gm-properties/', 0, 0, '2017-06-24 14:20:44', '2017-06-24 14:20:44', 8, 4, NULL, 1),
(4, '128 Online', '128.png', 'https://www.twelve12.com/project/128-online-store/', 0, 0, '2017-06-24 14:20:44', '2017-06-24 14:20:44', 8, 4, NULL, 1),
(5, 'Vampire Tools', 'vampire.png', 'https://www.twelve12.com/project/vampire-tools/', 0, 0, '2017-06-24 14:20:44', '2017-06-24 14:20:44', 8, 4, NULL, 1),
(6, 'inMotion', 'inmotion.png', 'https://www.twelve12.com/project/inmotion/', 0, 0, '2017-06-24 14:20:44', '2017-06-24 14:20:44', 8, 4, NULL, 1),
(7, 'The Kitchen', 'kitchen.png', 'https://www.twelve12.com/project/the-kitchen-at-westwood/', 1, 1, '2017-06-24 14:20:44', '2017-06-24 14:20:44', 8, 4, NULL, 1),
(8, 'Blog 1', 'blog1.png', 'https://www.twelve12.com/blog/branding/brand-way-box/', 0, 0, '2017-06-24 14:20:44', '2017-06-24 14:20:44', 8, 4, NULL, 1),
(9, 'Blog 2', 'blog2.png', 'https://www.twelve12.com/blog/branding/branding-tips/', 0, 0, '2017-06-24 14:20:44', '2017-06-24 14:20:44', 8, 4, NULL, 1),
(10, 'Twelve12 Home', 'home.png', 'https://www.twelve12.com/', 0, 0, '2017-06-24 14:38:34', '2017-06-24 14:38:34', 8, 4, NULL, 2),
(11, 'Bilal\'s Home', 'bilal.png', 'http://www.bilaltas.net/', 0, 0, '2017-06-25 00:21:09', '2017-06-25 00:21:09', 1, 4, NULL, 1),
(12, 'Cüneyt\'s Site', 'cuneyt.png', 'http://www.cuneyt-tas.com/', 0, 0, '2017-06-25 11:20:36', '2017-06-25 11:20:36', 5, 4, NULL, 5),
(13, 'BBC Home', 'bbc.png', 'http://www.bbc.com/', 0, 0, '2017-06-26 08:05:02', '2017-06-26 08:05:02', 6, 4, NULL, 1),
(14, 'About', 'about.png', 'https://www.twelve12.com/about-us/', 0, 0, '2017-06-24 14:20:44', '2017-06-24 14:20:44', 8, 7, 1, 1),
(15, 'SoundCloud Home', 'soundcloud.jpg', 'https://soundcloud.com/', 0, 0, '2017-07-02 15:49:58', '2017-07-02 15:49:58', 2, 4, NULL, 1),
(16, '7Diamonds Home', '7diamonds.png', 'https://7diamonds.com', 0, 0, '2017-07-02 18:15:06', '2017-07-02 18:15:06', 9, 4, NULL, 1),
(17, 'Envato Home', 'envato.jpg', 'https://envato.com/', 0, 0, '2017-07-06 14:26:32', '2017-07-06 14:26:32', 7, 6, NULL, 1),
(18, 'Auro Home', 'aurohome.jpg', 'https://www.aurowm.com/', 0, 0, '2017-07-07 11:11:40', '2017-07-07 11:11:40', 10, 4, NULL, 1),
(19, 'Youtube Home', 'youtube.jpg', 'https://www.youtube.com/', 0, 0, '2017-07-07 16:28:49', '2017-07-07 16:28:49', 4, 4, NULL, 1),
(20, 'Auro Home Mobile', 'auro.jpg', 'https://www.aurowm.com/', 0, 0, '2017-07-08 13:14:32', '2017-07-08 13:14:32', 10, 7, 18, 1),
(21, 'Auro Tablet Home', 'auro_t.jpg', 'https://www.aurowm.com/', 0, 0, '2017-07-08 16:11:01', '2017-07-08 16:11:01', 10, 6, 18, 1),
(22, 'TWSJ Home', 'twsj.jpg', 'https://www.wsj.com/europe', 0, 0, '2017-07-08 19:06:53', '2017-07-08 19:06:53', 3, 5, NULL, 1),
(24, 'Cüneyt\'s Babasayfa', 'cuneyt.png', 'http://www.cuneyt-tas.com/', 0, 0, '2017-06-25 11:20:36', '2017-06-25 11:20:36', 5, 4, NULL, 5),
(25, 'Hawaii Home', 'hawaiih.jpg', 'http://www.hawaiilassi.com/', 0, 0, '2017-07-10 12:01:17', '2017-07-10 12:01:17', 11, 4, NULL, 4);

-- --------------------------------------------------------

--
-- Table structure for table `page_cat_connect`
--

CREATE TABLE `page_cat_connect` (
  `page_cat_connect_ID` bigint(20) NOT NULL,
  `page_cat_page_ID` bigint(20) NOT NULL,
  `page_cat_ID` bigint(20) NOT NULL,
  `page_cat_connect_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `page_cat_connect`
--

INSERT INTO `page_cat_connect` (`page_cat_connect_ID`, `page_cat_page_ID`, `page_cat_ID`, `page_cat_connect_user_ID`) VALUES
(7, 7, 4, 1),
(84, 14, 3, 1),
(293, 10, 3, 1),
(294, 1, 3, 1),
(295, 2, 3, 1),
(296, 6, 4, 1),
(297, 5, 4, 1),
(298, 3, 4, 1),
(299, 4, 4, 1),
(300, 8, 5, 1),
(301, 9, 5, 1);

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
(2, 'SoundCloud', 'soundcloud.png', 1, 0, '2017-06-18 15:26:34', 1),
(3, 'TWSJ', 'twsj.png', 0, 1, '2017-06-18 15:27:35', 1),
(4, 'Youtube', 'youtube.png', 0, 0, '2017-06-18 15:28:12', 1),
(5, 'Cuneyt TAS', 'cuneyt-tas.png', 0, 0, '2017-06-18 15:28:34', 5),
(6, 'BBC', 'bbc.png', 0, 0, '2017-06-18 15:29:25', 1),
(7, 'Envato', 'envato.png', 0, 0, '2017-06-18 15:29:25', 1),
(8, 'Twelve12', 'twelve12.png', 0, 0, '2017-06-18 17:54:24', 2),
(9, '7Diamonds Dev', '7diamonds.png', 0, 0, '2017-06-29 04:11:01', 1),
(10, 'Auro WM', 'auro.jpg', 0, 0, '2017-07-07 11:11:08', 1),
(11, 'Hawaii Lassi', 'hawaii.jpg', 0, 0, '2017-07-10 12:00:36', 4);

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
(282, 1, 2, 1),
(283, 5, 2, 1),
(284, 10, 1, 1),
(285, 8, 1, 1),
(286, 9, 1, 1);

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
(1, 'project', 8, '3', 2),
(2, 'page', 10, '1', 2),
(3, 'project', 5, '1', 5);

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

--
-- Dumping data for table `sorting`
--

INSERT INTO `sorting` (`sort_ID`, `sort_type`, `sort_object_ID`, `sort_number`, `sorter_user_ID`) VALUES
(961, 'page', 14, 1, 1),
(1415, 'page', 12, 1, 1),
(1416, 'page', 24, 2, 1),
(1509, 'category', 3, 1, 1),
(1510, 'page', 10, 2, 1),
(1511, 'page', 1, 3, 1),
(1512, 'page', 2, 4, 1),
(1513, 'category', 4, 5, 1),
(1514, 'page', 6, 6, 1),
(1515, 'page', 5, 7, 1),
(1516, 'page', 3, 8, 1),
(1517, 'page', 4, 9, 1),
(1518, 'category', 5, 10, 1),
(1519, 'page', 8, 11, 1),
(1520, 'page', 9, 12, 1),
(1587, 'category', 0, 0, 1),
(1588, 'project', 6, 1, 1),
(1589, 'project', 4, 2, 1),
(1590, 'project', 7, 3, 1),
(1591, 'category', 2, 4, 1),
(1592, 'project', 1, 5, 1),
(1593, 'project', 5, 6, 1),
(1594, 'category', 1, 7, 1),
(1595, 'project', 10, 8, 1),
(1596, 'project', 8, 9, 1),
(1597, 'project', 9, 10, 1);

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
(3, 'sara', 'sara@twelve12.com', '$2y$10$UcoctpiTNtf9grzFmz53lut7X4l3EBspRC4xz/Qn/qJ1VWWIq81n.', 'Sara', 'Atalay', 'sara.png', 0, '2017-06-17 22:28:00', 3),
(4, 'matt', 'metin@twelve12.com', '$2y$10$UcoctpiTNtf9grzFmz53lut7X4l3EBspRC4xz/Qn/qJ1VWWIq81n.', 'Matt', 'Pasaoglu', 'matt.png', 0, '2017-06-18 13:51:00', 2),
(5, 'joey-goksu', 'joey@twelve12.com', '$2y$10$bAgxLbJhGEkO4HLXknHcPeZuGqRotBlb5G2qpCx4D0ARhRoveidMq', 'Joey', 'Goksu', 'joey.png', 0, '2017-06-25 09:28:07', 2);

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
-- Indexes for table `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`device_ID`),
  ADD KEY `device_cat_ID` (`device_cat_ID`),
  ADD KEY `user_ID` (`device_user_ID`);

--
-- Indexes for table `device_categories`
--
ALTER TABLE `device_categories`
  ADD PRIMARY KEY (`device_cat_ID`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_ID`),
  ADD KEY `user_ID` (`user_ID`),
  ADD KEY `project_ID` (`project_ID`),
  ADD KEY `device_ID` (`device_ID`);

--
-- Indexes for table `page_cat_connect`
--
ALTER TABLE `page_cat_connect`
  ADD PRIMARY KEY (`page_cat_connect_ID`),
  ADD KEY `page_cat_connect_user_ID` (`page_cat_connect_user_ID`),
  ADD KEY `page_cat_ID` (`page_cat_ID`),
  ADD KEY `page_cat_page_ID` (`page_cat_page_ID`);

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
  MODIFY `cat_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `device_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `device_categories`
--
ALTER TABLE `device_categories`
  MODIFY `device_cat_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `page_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `page_cat_connect`
--
ALTER TABLE `page_cat_connect`
  MODIFY `page_cat_connect_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=302;
--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `project_cat_connect`
--
ALTER TABLE `project_cat_connect`
  MODIFY `project_cat_connect_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=287;
--
-- AUTO_INCREMENT for table `shares`
--
ALTER TABLE `shares`
  MODIFY `share_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `sorting`
--
ALTER TABLE `sorting`
  MODIFY `sort_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1598;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
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
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`device_cat_ID`) REFERENCES `device_categories` (`device_cat_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `devices_ibfk_2` FOREIGN KEY (`device_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `pages_ibfk_2` FOREIGN KEY (`project_ID`) REFERENCES `projects` (`project_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `pages_ibfk_3` FOREIGN KEY (`device_ID`) REFERENCES `devices` (`device_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `page_cat_connect`
--
ALTER TABLE `page_cat_connect`
  ADD CONSTRAINT `page_cat_connect_ibfk_1` FOREIGN KEY (`page_cat_connect_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `page_cat_connect_ibfk_2` FOREIGN KEY (`page_cat_ID`) REFERENCES `categories` (`cat_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `page_cat_connect_ibfk_3` FOREIGN KEY (`page_cat_page_ID`) REFERENCES `pages` (`page_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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
