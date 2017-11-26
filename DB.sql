-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 26, 2017 at 10:52 PM
-- Server version: 5.6.35
-- PHP Version: 7.1.8

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
-- Table structure for table `archives`
--

CREATE TABLE `archives` (
  `archive_ID` bigint(20) NOT NULL,
  `archive_type` varchar(10) NOT NULL,
  `archived_object_ID` bigint(20) NOT NULL,
  `archiver_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
(1, 'Personal', 'project', 1),
(2, 'Twelve12 Clients', 'project', 1),
(3, 'Main Pages', '11', 1),
(4, 'Blogs', '11', 1),
(5, 'Portfolio', '11', 1);

-- --------------------------------------------------------

--
-- Table structure for table `deletes`
--

CREATE TABLE `deletes` (
  `delete_ID` bigint(20) NOT NULL,
  `delete_type` varchar(10) NOT NULL,
  `deleted_object_ID` bigint(20) NOT NULL,
  `deleter_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
(3, 'Macbook Pro 17', 1920, 1200, 0, NULL, NULL, 5, 2, 1),
(4, 'Macbook Pro 15', 1440, 900, 0, NULL, NULL, 5, 3, 1),
(5, 'Macbook Pro 13', 1280, 800, 0, NULL, NULL, 5, 4, 1),
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
(2, 'Tablet', 'fa-tablet', 2),
(3, 'Mobile', 'fa-mobile', 3),
(4, 'Custom...', 'fa-window-restore', 4),
(5, 'Laptop', 'fa-laptop', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `page_ID` bigint(20) NOT NULL,
  `page_name` varchar(200) NOT NULL,
  `page_pic` varchar(15) DEFAULT NULL,
  `page_url` text NOT NULL,
  `page_user` varchar(60) DEFAULT NULL,
  `page_pass` varchar(60) DEFAULT NULL,
  `page_height` int(11) DEFAULT NULL,
  `page_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `page_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `project_ID` bigint(20) NOT NULL,
  `device_ID` bigint(20) NOT NULL,
  `parent_page_ID` bigint(20) DEFAULT NULL,
  `user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`page_ID`, `page_name`, `page_pic`, `page_url`, `page_user`, `page_pass`, `page_height`, `page_created`, `page_modified`, `project_ID`, `device_ID`, `parent_page_ID`, `user_ID`) VALUES
(1, 'BBC Home', 'page.jpg', 'http://www.bbc.com/', NULL, NULL, NULL, '2017-11-25 20:31:13', '2017-11-25 20:37:14', 1, 4, NULL, 1),
(2, 'BBC Home', NULL, 'http://www.bbc.com/', NULL, NULL, NULL, '2017-11-25 21:33:48', '2017-11-25 21:33:48', 1, 9, 1, 1),
(3, 'WSJ Home', 'page.jpg', 'https://www.wsj.com/europe', NULL, NULL, NULL, '2017-11-25 22:09:45', '2017-11-25 22:10:37', 2, 4, NULL, 1),
(4, 'WSJ Home', 'page.jpg', 'https://www.wsj.com/europe', NULL, NULL, NULL, '2017-11-25 22:09:45', '2017-11-25 22:10:49', 2, 1, 3, 1),
(5, 'WSJ Home', 'page.jpg', 'https://www.wsj.com/europe', NULL, NULL, NULL, '2017-11-25 22:09:45', '2017-11-25 22:12:56', 2, 9, 3, 1),
(6, 'Envato Home', 'page.jpg', 'https://envato.com/', NULL, NULL, NULL, '2017-11-25 22:13:47', '2017-11-25 22:19:29', 3, 4, NULL, 1),
(7, 'Envato Home', 'page.jpg', 'https://envato.com/', NULL, NULL, NULL, '2017-11-25 22:27:18', '2017-11-26 13:49:07', 3, 7, 6, 1),
(8, 'YouTube New', 'page.jpg', 'https://www.youtube.com/', NULL, NULL, NULL, '2017-11-26 10:34:45', '2017-11-26 10:38:12', 4, 4, NULL, 1),
(9, 'Dnomak', 'page.jpg', 'http://dnomak.com/', NULL, NULL, NULL, '2017-11-26 10:42:17', '2017-11-26 10:42:29', 5, 4, NULL, 1),
(10, 'Dnomak', NULL, 'http://dnomak.com/', NULL, NULL, NULL, '2017-11-26 10:42:17', '2017-11-26 10:42:17', 5, 1, 9, 1),
(11, 'Dnomak', NULL, 'http://dnomak.com/', NULL, NULL, NULL, '2017-11-26 10:42:17', '2017-11-26 10:42:17', 5, 5, 9, 1),
(12, 'Dnomak', NULL, 'http://dnomak.com/', NULL, NULL, NULL, '2017-11-26 10:42:17', '2017-11-26 10:42:17', 5, 6, 9, 1),
(13, 'Dnomak', 'page.jpg', 'http://dnomak.com/', NULL, NULL, NULL, '2017-11-26 10:42:17', '2017-11-26 10:46:47', 5, 9, 9, 1),
(15, 'WP Home', 'page.jpg', 'http://localhost/wordpress/', NULL, NULL, NULL, '2017-11-26 10:48:45', '2017-11-26 10:49:00', 6, 4, NULL, 1),
(16, 'WP Home', NULL, 'http://localhost/wordpress/', NULL, NULL, NULL, '2017-11-26 10:48:45', '2017-11-26 10:48:45', 6, 7, 15, 1),
(17, 'Cuneyt Home', 'page.jpg', 'http://www.cuneyt-tas.com/', NULL, NULL, NULL, '2017-11-26 10:52:17', '2017-11-26 10:54:20', 8, 4, NULL, 5),
(18, 'Cuneyt Resume', 'page.jpg', 'http://www.cuneyt-tas.com/ozgecmis/', NULL, NULL, NULL, '2017-11-26 10:53:29', '2017-11-26 10:53:46', 8, 4, NULL, 5),
(19, 'Cuneyt Resume', 'page.jpg', 'http://www.cuneyt-tas.com/ozgecmis/', NULL, NULL, NULL, '2017-11-26 10:53:29', '2017-11-26 10:54:10', 8, 9, 18, 5),
(20, 'Bilal Home', 'page.jpg', 'http://www.bilaltas.net', NULL, NULL, NULL, '2017-11-26 10:57:16', '2017-11-26 10:57:36', 9, 4, NULL, 1),
(21, 'Serdar\'s Home', 'page.jpg', 'http://serdarkiziltepe.com/', NULL, NULL, NULL, '2017-11-26 11:04:00', '2017-11-26 11:04:16', 10, 4, NULL, 6),
(22, 'Twelve12 Home', 'page.jpg', 'https://www.twelve12.com', NULL, NULL, NULL, '2017-11-26 11:14:00', '2017-11-26 11:14:29', 11, 4, NULL, 2),
(23, 'Twelve12 Home', 'page.jpg', 'https://www.twelve12.com', NULL, NULL, NULL, '2017-11-26 11:14:00', '2017-11-26 11:52:10', 11, 1, 22, 2),
(24, 'Twelve12 Home', 'page.jpg', 'https://www.twelve12.com', NULL, NULL, NULL, '2017-11-26 11:14:00', '2017-11-26 11:52:10', 11, 5, 22, 2),
(25, 'Twelve12 Home', 'page.jpg', 'https://www.twelve12.com', NULL, NULL, NULL, '2017-11-26 11:14:00', '2017-11-26 11:52:10', 11, 6, 22, 2),
(26, 'Twelve12 Home', 'page.jpg', 'https://www.twelve12.com', NULL, NULL, NULL, '2017-11-26 11:14:00', '2017-11-26 11:52:10', 11, 9, 22, 2),
(27, 'About', 'page.jpg', 'https://www.twelve12.com/about-us/', NULL, NULL, NULL, '2017-11-26 11:17:43', '2017-11-26 11:58:43', 11, 4, NULL, 1),
(28, 'Contact', 'page.jpg', 'https://www.twelve12.com/contact/', NULL, NULL, NULL, '2017-11-26 11:58:21', '2017-11-26 11:58:38', 11, 4, NULL, 1),
(29, 'Blog 1', 'page.jpg', 'https://www.twelve12.com/blog/branding/whats-on-your-box/', NULL, NULL, NULL, '2017-11-26 12:00:29', '2017-11-26 13:04:01', 11, 4, NULL, 1),
(30, 'Blog 2', 'page.jpg', 'https://www.twelve12.com/blog/digital-marketing/brand-recognition/', NULL, NULL, NULL, '2017-11-26 14:57:03', '2017-11-26 14:58:00', 11, 4, NULL, 1),
(31, 'Artemis', 'page.jpg', 'https://www.twelve12.com/project/artemis-therapeutics/', NULL, NULL, NULL, '2017-11-26 14:59:01', '2017-11-26 14:59:51', 11, 4, NULL, 1),
(32, 'Pacific Chorale', 'page.jpg', 'https://www.twelve12.com/project/pacific-chorale/', NULL, NULL, NULL, '2017-11-26 15:00:37', '2017-11-26 15:01:00', 11, 4, NULL, 1),
(33, 'Kata Digital', 'page.jpg', 'https://www.twelve12.com/project/kata/', NULL, NULL, NULL, '2017-11-26 15:01:41', '2017-11-26 15:02:07', 11, 4, NULL, 1),
(34, 'Vampire Tools', 'page.jpg', 'https://www.twelve12.com/project/vampire-tools/', NULL, NULL, NULL, '2017-11-26 15:02:28', '2017-11-26 15:02:59', 11, 4, NULL, 1),
(35, '7Diamonds Home', 'page.jpg', 'https://www.7diamonds.com/', NULL, NULL, NULL, '2017-11-26 15:06:05', '2017-11-26 15:06:51', 12, 4, NULL, 1),
(36, 'Golden State Home', 'page.jpg', 'https://www.goldenstatewm.com/', NULL, NULL, NULL, '2017-11-26 15:08:00', '2017-11-26 15:16:27', 13, 4, NULL, 1),
(37, 'Auro Home', 'page.jpg', 'https://www.aurowm.com/', NULL, NULL, NULL, '2017-11-26 18:38:10', '2017-11-26 18:38:24', 14, 4, NULL, 1),
(38, 'CloudCompli Home', 'page.jpg', 'http://cloudcompli.com/', NULL, NULL, NULL, '2017-11-26 19:10:54', '2017-11-26 19:39:01', 15, 4, NULL, 1);

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
(9, 22, 3, 1),
(10, 27, 3, 1),
(11, 28, 3, 1),
(12, 29, 4, 1),
(13, 30, 4, 1),
(14, 31, 5, 1),
(15, 32, 5, 1),
(16, 33, 5, 1),
(17, 34, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pins`
--

CREATE TABLE `pins` (
  `pin_ID` bigint(20) NOT NULL,
  `pin_type` varchar(10) NOT NULL,
  `pin_private` tinyint(1) NOT NULL DEFAULT '0',
  `pin_complete` tinyint(1) NOT NULL DEFAULT '0',
  `pin_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pin_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `pin_x` int(20) NOT NULL DEFAULT '10',
  `pin_y` int(20) NOT NULL DEFAULT '10',
  `pin_element_index` bigint(20) NOT NULL,
  `version_ID` bigint(20) NOT NULL,
  `user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_ID` bigint(20) NOT NULL,
  `project_name` varchar(200) NOT NULL,
  `project_pic` varchar(15) DEFAULT NULL,
  `project_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_ID`, `project_name`, `project_pic`, `project_created`, `user_ID`) VALUES
(1, 'BBC', 'proj.jpg', '2017-11-25 20:31:13', 1),
(2, 'TWSJ', 'proj.jpg', '2017-11-25 22:09:45', 1),
(3, 'Envato', 'project.jpg', '2017-11-25 22:13:39', 1),
(4, 'YouTube(!)', 'project.jpg', '2017-11-26 10:31:41', 1),
(5, 'Doğukan Güven Nomak', 'proj.jpg', '2017-11-26 10:42:17', 1),
(6, 'Local WP', 'proj.jpg', '2017-11-26 10:48:45', 1),
(8, 'Cüneyt TAŞ', 'proj.jpg', '2017-11-26 10:52:07', 5),
(9, 'Bilal TAS', 'proj.jpg', '2017-11-26 10:56:18', 1),
(10, 'Serdar Kiziltepe', 'proj.jpg', '2017-11-26 11:04:00', 6),
(11, 'Twelve12', 'project.jpg', '2017-11-26 11:10:35', 2),
(12, '7Diamonds', 'project.jpg', '2017-11-26 15:05:52', 1),
(13, 'Golden State', 'project.jpg', '2017-11-26 15:08:00', 1),
(14, 'Auro WM', 'project.jpg', '2017-11-26 18:37:48', 1),
(15, 'Cloud Compli', 'project.jpg', '2017-11-26 19:10:54', 1);

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
(49, 8, 1, 1),
(50, 9, 1, 1),
(51, 10, 1, 1),
(52, 5, 1, 1),
(53, 11, 2, 1),
(54, 12, 2, 1),
(55, 13, 2, 1),
(56, 14, 2, 1),
(57, 15, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `queues`
--

CREATE TABLE `queues` (
  `queue_ID` bigint(20) NOT NULL,
  `queue_type` varchar(60) DEFAULT NULL,
  `queue_object_ID` bigint(20) NOT NULL,
  `queue_PID` bigint(20) DEFAULT NULL,
  `queue_status` varchar(20) NOT NULL DEFAULT 'waiting',
  `queue_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `queue_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `queue_message` text,
  `user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `queues`
--

INSERT INTO `queues` (`queue_ID`, `queue_type`, `queue_object_ID`, `queue_PID`, `queue_status`, `queue_updated`, `queue_created`, `queue_message`, `user_ID`) VALUES
(1, 'internalize', 22, 11490, 'done', '2017-11-26 11:14:15', '2017-11-26 11:14:00', 'Internalization is complete.', 2),
(2, 'internalize', 23, 11492, 'done', '2017-11-26 11:14:36', '2017-11-26 11:14:00', 'Internalization is complete.', 2),
(3, 'internalize', 24, 11494, 'done', '2017-11-26 11:14:54', '2017-11-26 11:14:00', 'Internalization is complete.', 2),
(4, 'internalize', 25, 11496, 'done', '2017-11-26 11:15:09', '2017-11-26 11:14:00', 'Internalization is complete.', 2),
(5, 'internalize', 26, 11498, 'done', '2017-11-26 11:15:23', '2017-11-26 11:14:00', 'Internalization is complete.', 2),
(6, 'internalize', 27, 11650, 'done', '2017-11-26 11:17:53', '2017-11-26 11:17:43', 'Internalization is complete.', 1),
(7, 'internalize', 28, 12278, 'done', '2017-11-26 11:58:30', '2017-11-26 11:58:21', 'Internalization is complete.', 1),
(8, 'internalize', 29, 12336, 'done', '2017-11-26 12:00:39', '2017-11-26 12:00:29', 'Internalization is complete.', 1),
(9, 'internalize', 29, 12523, 'done', '2017-11-26 13:05:09', '2017-11-26 13:04:54', 'Internalization is complete.', 1),
(10, 'internalize', 29, 12824, 'done', '2017-11-26 13:06:05', '2017-11-26 13:05:58', 'Internalization is complete.', 1),
(11, 'internalize', 29, 12972, 'done', '2017-11-26 13:07:43', '2017-11-26 13:07:30', 'Internalization is complete.', 1),
(12, 'internalize', 29, 13218, 'done', '2017-11-26 13:08:47', '2017-11-26 13:08:34', 'Internalization is complete.', 1),
(13, 'internalize', 29, 13498, 'done', '2017-11-26 13:09:45', '2017-11-26 13:09:36', 'Internalization is complete.', 1),
(14, 'internalize', 29, 13675, 'done', '2017-11-26 13:11:21', '2017-11-26 13:11:10', 'Internalization is complete.', 1),
(15, 'internalize', 29, 13892, 'done', '2017-11-26 13:12:13', '2017-11-26 13:12:00', 'Internalization is complete.', 1),
(16, 'internalize', 29, 14137, 'done', '2017-11-26 13:13:09', '2017-11-26 13:12:59', 'Internalization is complete.', 1),
(17, 'internalize', 29, 14343, 'done', '2017-11-26 13:13:49', '2017-11-26 13:13:34', 'Internalization is complete.', 1),
(18, 'internalize', 29, 14560, 'done', '2017-11-26 13:14:22', '2017-11-26 13:14:13', 'Internalization is complete.', 1),
(19, 'internalize', 29, 14809, 'done', '2017-11-26 13:15:18', '2017-11-26 13:15:10', 'Internalization is complete.', 1),
(20, 'internalize', 29, 14988, 'done', '2017-11-26 13:22:25', '2017-11-26 13:22:02', 'Internalization is complete.', 1),
(21, 'internalize', 27, 15337, 'done', '2017-11-26 13:24:27', '2017-11-26 13:24:06', 'Internalization is complete.', 1),
(22, 'internalize', 27, 15574, 'error', '2017-11-26 13:28:44', '2017-11-26 13:28:22', 'Process is not working.', 1),
(23, 'internalize', 27, 15880, 'done', '2017-11-26 13:30:25', '2017-11-26 13:30:10', 'Internalization is complete.', 1),
(24, 'internalize', 27, 16131, 'done', '2017-11-26 13:34:13', '2017-11-26 13:33:46', 'Internalization is complete.', 1),
(25, 'internalize', 27, 16450, 'done', '2017-11-26 13:47:47', '2017-11-26 13:47:39', 'Internalization is complete.', 1),
(26, 'internalize', 6, 16687, 'done', '2017-11-26 13:49:46', '2017-11-26 13:49:38', 'Internalization is complete.', 1),
(27, 'internalize', 8, 17935, 'error', '2017-11-26 14:42:21', '2017-11-26 14:41:49', 'Process is not working.', 1),
(28, 'internalize', 8, 18294, 'error', '2017-11-26 14:49:53', '2017-11-26 14:49:21', 'Process is not working.', 1),
(29, 'internalize', 8, 18686, 'done', '2017-11-26 14:53:15', '2017-11-26 14:52:58', 'Internalization is complete.', 1),
(30, 'internalize', 30, 19265, 'done', '2017-11-26 14:57:15', '2017-11-26 14:57:03', 'Internalization is complete.', 1),
(31, 'internalize', 31, 19338, 'done', '2017-11-26 14:59:15', '2017-11-26 14:59:01', 'Internalization is complete.', 1),
(32, 'internalize', 32, 19421, 'done', '2017-11-26 15:00:46', '2017-11-26 15:00:37', 'Internalization is complete.', 1),
(33, 'internalize', 33, 19482, 'done', '2017-11-26 15:01:52', '2017-11-26 15:01:41', 'Internalization is complete.', 1),
(34, 'internalize', 34, 19553, 'done', '2017-11-26 15:02:38', '2017-11-26 15:02:28', 'Internalization is complete.', 1),
(35, 'internalize', 35, 19663, 'done', '2017-11-26 15:06:32', '2017-11-26 15:06:05', 'Internalization is complete.', 1),
(36, 'internalize', 36, 19840, 'error', '2017-11-26 15:08:52', '2017-11-26 15:08:00', 'Process is not working.', 1),
(37, 'internalize', 36, 20186, 'error', '2017-11-26 15:10:42', '2017-11-26 15:09:50', 'Process is not working.', 1),
(38, 'internalize', 36, 20577, 'error', '2017-11-26 15:12:42', '2017-11-26 15:11:49', 'Process is not working.', 1),
(39, 'internalize', 36, 20950, 'error', '2017-11-26 15:13:54', '2017-11-26 15:13:02', 'Process is not working.', 1),
(40, 'internalize', 36, 21259, 'done', '2017-11-26 15:16:14', '2017-11-26 15:15:51', 'Internalization is complete.', 1),
(41, 'internalize', 37, 22124, 'done', '2017-11-26 18:38:17', '2017-11-26 18:38:10', 'Internalization is complete.', 1),
(42, 'internalize', 38, 22894, 'done', '2017-11-26 19:11:11', '2017-11-26 19:10:54', 'Internalization is complete.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `shares`
--

CREATE TABLE `shares` (
  `share_ID` bigint(20) NOT NULL,
  `share_type` varchar(10) NOT NULL,
  `shared_object_ID` bigint(20) NOT NULL,
  `share_to` varchar(100) NOT NULL,
  `sharer_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `shares`
--

INSERT INTO `shares` (`share_ID`, `share_type`, `shared_object_ID`, `share_to`, `sharer_user_ID`) VALUES
(1, 'project', 8, '1', 5),
(2, 'page', 20, '5', 1),
(3, 'project', 10, '1', 6),
(4, 'page', 22, '1', 2);

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
(2, 'page', 1, 1, 1),
(4, 'page', 3, 2, 1),
(5, 'page', 4, 2, 1),
(6, 'page', 5, 2, 1),
(8, 'page', 6, 1, 1),
(10, 'page', 8, 1, 1),
(12, 'page', 9, 5, 1),
(13, 'page', 10, 5, 1),
(14, 'page', 11, 5, 1),
(15, 'page', 12, 5, 1),
(16, 'page', 13, 5, 1),
(18, 'page', 15, 6, 1),
(19, 'page', 16, 6, 1),
(22, 'page', 17, 1, 5),
(23, 'page', 18, 2, 5),
(24, 'page', 19, 2, 5),
(35, 'page', 20, 1, 1),
(42, 'category', 0, 0, 5),
(43, 'project', 8, 1, 5),
(44, 'project', 9, 2, 5),
(45, 'project', 10, 1, 6),
(46, 'page', 21, 1, 6),
(69, 'project', 11, 1, 2),
(70, 'page', 22, 1, 2),
(71, 'page', 23, 1, 2),
(72, 'page', 24, 1, 2),
(73, 'page', 25, 1, 2),
(74, 'page', 26, 1, 2),
(89, 'project', 1, 1, 1),
(90, 'project', 2, 2, 1),
(91, 'project', 3, 3, 1),
(92, 'project', 4, 4, 1),
(93, 'project', 6, 5, 1),
(94, 'category', 1, 6, 1),
(95, 'project', 8, 7, 1),
(96, 'project', 9, 8, 1),
(97, 'project', 10, 9, 1),
(98, 'project', 5, 10, 1),
(99, 'category', 2, 11, 1),
(100, 'project', 11, 12, 1),
(116, 'category', 0, 0, 1),
(117, 'category', 3, 1, 1),
(118, 'page', 22, 2, 1),
(119, 'page', 27, 3, 1),
(120, 'page', 28, 4, 1),
(121, 'category', 4, 5, 1),
(122, 'page', 29, 6, 1),
(123, 'page', 30, 7, 1),
(124, 'category', 5, 8, 1),
(125, 'page', 31, 1, 1),
(126, 'page', 32, 2, 1),
(127, 'page', 33, 3, 1),
(128, 'page', 34, 4, 1),
(129, 'project', 12, 13, 1),
(130, 'page', 35, 1, 1),
(131, 'project', 13, 14, 1),
(132, 'page', 36, 14, 1),
(133, 'project', 14, 15, 1),
(134, 'page', 37, 1, 1),
(135, 'project', 15, 16, 1),
(136, 'page', 38, 16, 1);

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
(3, 'sara', 'sara@twelve12.com', '$2y$10$FlJ0PwBy6.5m8MXqIDMv5u.CsTW9w7bEgmlzLUCG9il6ZaN6KMmVC', 'Sara', 'Atalay', 'sara.png', 0, '2017-06-17 22:28:00', 3),
(4, 'matt', 'metin@twelve12.com', '$2y$10$UcoctpiTNtf9grzFmz53lut7X4l3EBspRC4xz/Qn/qJ1VWWIq81n.', 'Matt', 'Pasaoglu', 'matt.png', 0, '2017-06-18 13:51:00', 2),
(5, 'cuneyt', 'cuneyt@twelve12.com', '$2y$10$FlJ0PwBy6.5m8MXqIDMv5u.CsTW9w7bEgmlzLUCG9il6ZaN6KMmVC', 'Cuneyt', 'Tas', 'joey.png', 0, '2017-06-25 09:28:07', 2),
(6, 'serdar-kiziltepe', 'abraham@twelve12.com', '$2y$10$xOHRtNIOyPEQ9zo/LdN.NuKeCrDPvu51bRczoHbbD4h13Jz..ZD6e', 'Serdar', 'Kiziltepe', NULL, 0, '2017-09-16 19:57:18', 2);

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

-- --------------------------------------------------------

--
-- Table structure for table `versions`
--

CREATE TABLE `versions` (
  `version_ID` bigint(20) NOT NULL,
  `version_name` varchar(100) NOT NULL DEFAULT 'Initial version',
  `version_number` bigint(20) NOT NULL DEFAULT '1',
  `version_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `version_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `page_ID` bigint(20) NOT NULL,
  `user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `versions`
--

INSERT INTO `versions` (`version_ID`, `version_name`, `version_number`, `version_created`, `version_modified`, `page_ID`, `user_ID`) VALUES
(1, 'Initial version', 1, '2017-11-25 20:31:13', '2017-11-25 20:31:13', 1, 1),
(2, 'Initial version', 1, '2017-11-25 21:33:48', '2017-11-25 21:33:48', 2, 1),
(3, 'Initial version', 1, '2017-11-25 22:09:45', '2017-11-25 22:09:45', 3, 1),
(4, 'Initial version', 1, '2017-11-25 22:09:45', '2017-11-25 22:09:45', 4, 1),
(5, 'Initial version', 1, '2017-11-25 22:09:45', '2017-11-25 22:09:45', 5, 1),
(6, 'Initial version', 1, '2017-11-25 22:13:47', '2017-11-25 22:13:47', 6, 1),
(7, 'Initial version', 1, '2017-11-25 22:27:18', '2017-11-25 22:27:18', 7, 1),
(8, 'Initial version', 1, '2017-11-26 10:34:45', '2017-11-26 10:34:45', 8, 1),
(9, 'Initial version', 1, '2017-11-26 10:42:17', '2017-11-26 10:42:17', 9, 1),
(10, 'Initial version', 1, '2017-11-26 10:42:17', '2017-11-26 10:42:17', 10, 1),
(11, 'Initial version', 1, '2017-11-26 10:42:17', '2017-11-26 10:42:17', 11, 1),
(12, 'Initial version', 1, '2017-11-26 10:42:17', '2017-11-26 10:42:17', 12, 1),
(13, 'Initial version', 1, '2017-11-26 10:42:17', '2017-11-26 10:42:17', 13, 1),
(15, 'Initial version', 1, '2017-11-26 10:48:45', '2017-11-26 10:48:45', 15, 1),
(16, 'Initial version', 1, '2017-11-26 10:48:45', '2017-11-26 10:48:45', 16, 1),
(17, 'Initial version', 1, '2017-11-26 10:52:17', '2017-11-26 10:52:17', 17, 5),
(18, 'Initial version', 1, '2017-11-26 10:53:29', '2017-11-26 10:53:29', 18, 5),
(19, 'Initial version', 1, '2017-11-26 10:53:29', '2017-11-26 10:53:29', 19, 5),
(20, 'Initial version', 1, '2017-11-26 10:57:16', '2017-11-26 10:57:16', 20, 1),
(21, 'Initial version', 1, '2017-11-26 11:04:00', '2017-11-26 11:04:00', 21, 6),
(22, 'Initial version', 1, '2017-11-26 11:14:00', '2017-11-26 11:14:00', 22, 2),
(23, 'Initial version', 1, '2017-11-26 11:14:00', '2017-11-26 11:14:00', 23, 2),
(24, 'Initial version', 1, '2017-11-26 11:14:00', '2017-11-26 11:14:00', 24, 2),
(25, 'Initial version', 1, '2017-11-26 11:14:00', '2017-11-26 11:14:00', 25, 2),
(26, 'Initial version', 1, '2017-11-26 11:14:00', '2017-11-26 11:14:00', 26, 2),
(27, 'Initial version', 1, '2017-11-26 11:17:43', '2017-11-26 11:17:43', 27, 1),
(28, 'Initial version', 1, '2017-11-26 11:58:21', '2017-11-26 11:58:21', 28, 1),
(29, 'Initial version', 1, '2017-11-26 12:00:29', '2017-11-26 12:00:29', 29, 1),
(30, 'Initial version', 1, '2017-11-26 14:57:03', '2017-11-26 14:57:03', 30, 1),
(31, 'Initial version', 1, '2017-11-26 14:59:01', '2017-11-26 14:59:01', 31, 1),
(32, 'Initial version', 1, '2017-11-26 15:00:37', '2017-11-26 15:00:37', 32, 1),
(33, 'Initial version', 1, '2017-11-26 15:01:41', '2017-11-26 15:01:41', 33, 1),
(34, 'Initial version', 1, '2017-11-26 15:02:28', '2017-11-26 15:02:28', 34, 1),
(35, 'Initial version', 1, '2017-11-26 15:06:05', '2017-11-26 15:06:05', 35, 1),
(36, 'Initial version', 1, '2017-11-26 15:08:00', '2017-11-26 15:08:00', 36, 1),
(37, 'Initial version', 1, '2017-11-26 18:38:10', '2017-11-26 18:38:10', 37, 1),
(38, 'Initial version', 1, '2017-11-26 19:10:54', '2017-11-26 19:10:54', 38, 1);

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
  ADD KEY `device_cat_ID` (`device_cat_ID`),
  ADD KEY `devices_ibfk_2` (`device_user_ID`);

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
  ADD KEY `device_ID` (`device_ID`),
  ADD KEY `pages_ibfk_1` (`user_ID`),
  ADD KEY `pages_ibfk_2` (`project_ID`);

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
  ADD KEY `version_ID` (`version_ID`);

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
-- Indexes for table `versions`
--
ALTER TABLE `versions`
  ADD PRIMARY KEY (`version_ID`),
  ADD KEY `user_ID` (`user_ID`),
  ADD KEY `page_ID` (`page_ID`);

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
  MODIFY `cat_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `deletes`
--
ALTER TABLE `deletes`
  MODIFY `delete_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `devices`
--
ALTER TABLE `devices`
  MODIFY `device_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `device_categories`
--
ALTER TABLE `device_categories`
  MODIFY `device_cat_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `page_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT for table `page_cat_connect`
--
ALTER TABLE `page_cat_connect`
  MODIFY `page_cat_connect_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `pins`
--
ALTER TABLE `pins`
  MODIFY `pin_ID` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `project_cat_connect`
--
ALTER TABLE `project_cat_connect`
  MODIFY `project_cat_connect_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
--
-- AUTO_INCREMENT for table `queues`
--
ALTER TABLE `queues`
  MODIFY `queue_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
--
-- AUTO_INCREMENT for table `shares`
--
ALTER TABLE `shares`
  MODIFY `share_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `sorting`
--
ALTER TABLE `sorting`
  MODIFY `sort_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `user_levels`
--
ALTER TABLE `user_levels`
  MODIFY `user_level_ID` smallint(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `versions`
--
ALTER TABLE `versions`
  MODIFY `version_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `archives`
--
ALTER TABLE `archives`
  ADD CONSTRAINT `archives_ibfk_1` FOREIGN KEY (`archiver_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`cat_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `deletes`
--
ALTER TABLE `deletes`
  ADD CONSTRAINT `deletes_ibfk_1` FOREIGN KEY (`deleter_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `devices`
--
ALTER TABLE `devices`
  ADD CONSTRAINT `devices_ibfk_1` FOREIGN KEY (`device_cat_ID`) REFERENCES `device_categories` (`device_cat_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `devices_ibfk_2` FOREIGN KEY (`device_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `pages_ibfk_2` FOREIGN KEY (`project_ID`) REFERENCES `projects` (`project_ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `pages_ibfk_3` FOREIGN KEY (`device_ID`) REFERENCES `devices` (`device_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `page_cat_connect`
--
ALTER TABLE `page_cat_connect`
  ADD CONSTRAINT `page_cat_connect_ibfk_1` FOREIGN KEY (`page_cat_connect_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `page_cat_connect_ibfk_2` FOREIGN KEY (`page_cat_ID`) REFERENCES `categories` (`cat_ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `page_cat_connect_ibfk_3` FOREIGN KEY (`page_cat_page_ID`) REFERENCES `pages` (`page_ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `pins`
--
ALTER TABLE `pins`
  ADD CONSTRAINT `pins_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `pins_ibfk_2` FOREIGN KEY (`version_ID`) REFERENCES `versions` (`version_ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `project_cat_connect`
--
ALTER TABLE `project_cat_connect`
  ADD CONSTRAINT `project_cat_connect_ibfk_1` FOREIGN KEY (`project_cat_connect_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `project_cat_connect_ibfk_2` FOREIGN KEY (`project_cat_project_ID`) REFERENCES `projects` (`project_ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `project_cat_connect_ibfk_3` FOREIGN KEY (`project_cat_ID`) REFERENCES `categories` (`cat_ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `shares`
--
ALTER TABLE `shares`
  ADD CONSTRAINT `shares_ibfk_1` FOREIGN KEY (`sharer_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `sorting`
--
ALTER TABLE `sorting`
  ADD CONSTRAINT `sorting_ibfk_1` FOREIGN KEY (`sorter_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_level` FOREIGN KEY (`user_level_ID`) REFERENCES `user_levels` (`user_level_ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `versions`
--
ALTER TABLE `versions`
  ADD CONSTRAINT `versions_ibfk_2` FOREIGN KEY (`user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `versions_ibfk_3` FOREIGN KEY (`page_ID`) REFERENCES `pages` (`page_ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
