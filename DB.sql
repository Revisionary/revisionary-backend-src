-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 17, 2017 at 11:55 AM
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
(1, 'Hello World!', 'project', 1),
(2, 'Twelve12 Related', 'project', 1),
(3, 'Main Pages', '12', 1),
(4, 'Portfolio Pages', '12', 1),
(5, 'Blog Pages', '12', 1),
(6, 'Main Sayfalar', '13', 1);

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

INSERT INTO `pages` (`page_ID`, `page_name`, `page_pic`, `page_url`, `page_user`, `page_pass`, `page_created`, `page_modified`, `project_ID`, `device_ID`, `parent_page_ID`, `user_ID`) VALUES
(1, 'Youtube Home', 'page.jpg', 'https://www.youtube.com/', NULL, NULL, '2017-08-26 09:46:11', '2017-08-26 09:46:22', 1, 4, NULL, 1),
(2, 'Youtube Home', 'page.jpg', 'https://www.youtube.com/', NULL, NULL, '2017-08-26 09:46:11', '2017-08-26 09:47:01', 1, 1, 1, 1),
(3, 'Youtube Home', 'page.jpg', 'https://www.youtube.com/', NULL, NULL, '2017-08-26 09:46:11', '2017-08-26 09:47:48', 1, 5, 1, 1),
(4, 'Youtube Home', 'page.jpg', 'https://www.youtube.com/', NULL, NULL, '2017-08-26 09:46:11', '2017-08-26 09:48:21', 1, 6, 1, 1),
(5, 'Youtube Home', 'page.jpg', 'https://www.youtube.com/', NULL, NULL, '2017-08-26 09:46:11', '2017-08-26 09:49:00', 1, 9, 1, 1),
(6, 'Wall Street Europe', 'page.jpg', 'https://www.wsj.com/', NULL, NULL, '2017-08-26 09:57:02', '2017-08-26 09:58:48', 2, 4, NULL, 1),
(7, 'Wall Street Europe', 'page.jpg', 'https://www.wsj.com/', NULL, NULL, '2017-08-26 09:57:02', '2017-08-26 10:50:13', 2, 9, 6, 1),
(8, 'BBC Home', 'page.jpg', 'http://www.bbc.com/', NULL, NULL, '2017-08-26 10:59:28', '2017-08-26 11:00:05', 3, 4, NULL, 1),
(9, 'BBC Home', 'page.jpg', 'http://www.bbc.com/', NULL, NULL, '2017-08-26 10:59:28', '2017-08-26 11:01:16', 3, 9, 8, 1),
(10, 'Envato Home', 'page.jpg', 'https://envato.com/', NULL, NULL, '2017-08-26 11:02:33', '2017-08-26 11:04:31', 4, 4, NULL, 1),
(11, 'Envato Home', 'page.jpg', 'https://envato.com/', NULL, NULL, '2017-08-26 11:02:33', '2017-08-26 11:05:51', 4, 1, 10, 1),
(12, 'Envato Home', 'page.jpg', 'https://envato.com/', NULL, NULL, '2017-08-26 11:02:33', '2017-08-26 11:11:26', 4, 9, 10, 1),
(13, 'Bilal\'s Home', 'page.jpg', 'http://www.bilaltas.net/', NULL, NULL, '2017-08-26 11:17:37', '2017-08-26 11:18:02', 5, 4, NULL, 1),
(14, 'Bilal\'s Home', 'page.jpg', 'http://www.bilaltas.net/', NULL, NULL, '2017-08-26 11:17:37', '2017-08-26 11:18:58', 5, 1, 13, 1),
(15, 'Bilal\'s Home', 'page.jpg', 'http://www.bilaltas.net/', NULL, NULL, '2017-08-26 11:17:37', '2017-08-26 11:20:27', 5, 6, 13, 1),
(16, 'Bilal\'s Home', 'page.jpg', 'http://www.bilaltas.net/', NULL, NULL, '2017-08-26 11:17:37', '2017-08-26 11:22:58', 5, 9, 13, 1),
(45, 'Serdar\'s Home', 'page.jpg', 'http://serdarkiziltepe.com/', NULL, NULL, '2017-08-26 13:24:27', '2017-08-26 13:25:13', 11, 4, NULL, 1),
(46, 'Serdar\'s Home', 'page.jpg', 'http://serdarkiziltepe.com/', NULL, NULL, '2017-08-26 13:24:27', '2017-08-26 13:25:29', 11, 1, 45, 1),
(47, 'Serdar\'s Home', 'page.jpg', 'http://serdarkiziltepe.com/', NULL, NULL, '2017-08-26 13:24:27', '2017-08-26 13:25:46', 11, 5, 45, 1),
(48, 'Serdar\'s Home', 'page.jpg', 'http://serdarkiziltepe.com/', NULL, NULL, '2017-08-26 13:24:27', '2017-08-26 13:26:02', 11, 6, 45, 1),
(49, 'Serdar\'s Home', 'page.jpg', 'http://serdarkiziltepe.com/', NULL, NULL, '2017-08-26 13:24:27', '2017-08-26 13:26:19', 11, 9, 45, 1),
(50, 'Home Page', 'page.jpg', 'https://www.twelve12.com', NULL, NULL, '2017-08-26 15:56:25', '2017-08-26 15:56:33', 12, 4, NULL, 2),
(51, 'Cuneyt\'s Home', 'page.jpg', 'http://www.cuneyt-tas.com/', NULL, NULL, '2017-08-26 18:02:45', '2017-08-26 18:02:52', 13, 4, NULL, 5),
(52, 'Cuneyt\'s Home', 'page.jpg', 'http://www.cuneyt-tas.com/', NULL, NULL, '2017-08-26 18:02:45', '2017-08-26 18:03:54', 13, 1, 51, 5),
(53, 'Cuneyt\'s Home', 'page.jpg', 'http://www.cuneyt-tas.com/', NULL, NULL, '2017-08-26 18:02:45', '2017-08-26 18:04:04', 13, 5, 51, 5),
(54, 'Cuneyt\'s Home', 'page.jpg', 'http://www.cuneyt-tas.com/', NULL, NULL, '2017-08-26 18:02:46', '2017-08-26 18:04:14', 13, 6, 51, 5),
(55, 'Cuneyt\'s Home', 'page.jpg', 'http://www.cuneyt-tas.com/', NULL, NULL, '2017-08-26 18:02:46', '2017-08-26 18:04:25', 13, 9, 51, 5),
(56, '7Diamonds Home', 'page.jpg', 'https://dev.7diamonds.com/', NULL, NULL, '2017-08-26 18:08:07', '2017-08-26 18:08:55', 14, 4, NULL, 1),
(57, '7Diamonds Home', 'page.jpg', 'https://dev.7diamonds.com/', NULL, NULL, '2017-08-26 18:08:07', '2017-08-26 18:09:57', 14, 1, 56, 1),
(58, '7Diamonds Home', 'page.jpg', 'https://dev.7diamonds.com/', NULL, NULL, '2017-08-26 18:08:07', '2017-08-26 18:10:51', 14, 5, 56, 1),
(59, '7Diamonds Home', 'page.jpg', 'https://dev.7diamonds.com/', NULL, NULL, '2017-08-26 18:08:07', '2017-08-26 18:11:47', 14, 9, 56, 1),
(60, 'VantaQuest Home', 'page.jpg', 'http://vantaquest.twelve12.com/', NULL, NULL, '2017-08-26 18:11:48', '2017-08-26 18:12:35', 15, 4, NULL, 1),
(61, 'VantaQuest Home', 'page.jpg', 'http://vantaquest.twelve12.com/', NULL, NULL, '2017-08-26 18:11:48', '2017-08-26 18:12:48', 15, 1, 60, 1),
(62, 'VantaQuest Home', 'page.jpg', 'http://vantaquest.twelve12.com/', NULL, NULL, '2017-08-26 18:11:48', '2017-08-26 18:13:00', 15, 5, 60, 1),
(63, 'VantaQuest Home', 'page.jpg', 'http://vantaquest.twelve12.com/', NULL, NULL, '2017-08-26 18:11:48', '2017-08-26 18:13:10', 15, 6, 60, 1),
(64, 'VantaQuest Home', 'page.jpg', 'http://vantaquest.twelve12.com/', NULL, NULL, '2017-08-26 18:11:48', '2017-08-26 18:13:21', 15, 9, 60, 1),
(65, 'Auro Home', 'page.jpg', 'https://www.aurowm.com/', NULL, NULL, '2017-08-26 18:14:35', '2017-08-26 18:14:41', 16, 4, NULL, 1),
(66, 'Golden State Home', 'page.jpg', 'https://www.goldenstatewm.com/', NULL, NULL, '2017-08-26 18:15:50', '2017-08-26 18:15:59', 17, 4, NULL, 1),
(67, 'Cloud Step Home', 'page.jpg', 'https://www.cloudstep.com/', NULL, NULL, '2017-08-26 18:16:21', '2017-08-26 18:17:19', 18, 4, NULL, 1),
(68, 'Hawaii Home', 'page.jpg', 'http://www.hawaiilassi.com/', NULL, NULL, '2017-08-26 18:17:16', '2017-08-26 18:18:06', 19, 4, NULL, 1),
(69, 'Hawaii Home', 'page.jpg', 'http://www.hawaiilassi.com/', NULL, NULL, '2017-08-26 18:17:16', '2017-08-26 18:18:43', 19, 6, 68, 1),
(70, 'Juniper Home', 'page.jpg', 'http://junipercleaning.com/', NULL, NULL, '2017-08-26 18:18:42', '2017-08-26 18:19:09', 20, 4, NULL, 1),
(71, 'Cloud Compli Home', 'page.jpg', 'http://cloudcompli.com/', NULL, NULL, '2017-08-26 18:44:02', '2017-08-26 18:44:17', 21, 4, NULL, 1),
(72, 'Jova Home', 'page.jpg', 'https://jovadigital.com/', NULL, NULL, '2017-08-26 20:49:41', '2017-08-26 20:49:47', 23, 4, NULL, 1),
(73, 'Jova Home', 'page.jpg', 'https://jovadigital.com/', NULL, NULL, '2017-08-26 20:49:41', '2017-08-26 20:50:22', 23, 1, 72, 1),
(74, 'Jova Home', 'page.jpg', 'https://jovadigital.com/', NULL, NULL, '2017-08-26 20:49:41', '2017-08-26 20:50:59', 23, 9, 72, 1),
(77, 'Resume Page', 'page.jpg', 'http://www.cuneyt-tas.com/ozgecmis/', NULL, NULL, '2017-08-27 17:51:38', '2017-08-27 17:51:44', 13, 4, NULL, 1),
(78, 'My Resume', 'page.jpg', 'http://www.bilaltas.net/resume/my-resume/', NULL, NULL, '2017-08-27 18:01:15', '2017-08-27 18:01:19', 5, 4, NULL, 1),
(79, 'Contact Page', 'page.jpg', 'http://www.bilaltas.net/contact/', NULL, NULL, '2017-08-27 18:04:43', '2017-08-27 18:04:49', 5, 4, NULL, 1),
(80, 'Contact Page', 'page.jpg', 'http://www.bilaltas.net/contact/', NULL, NULL, '2017-08-27 18:04:43', '2017-08-27 18:05:04', 5, 1, 79, 1),
(81, 'Contact Page', 'page.jpg', 'http://www.bilaltas.net/contact/', NULL, NULL, '2017-08-27 18:04:43', '2017-08-27 18:05:19', 5, 9, 79, 1),
(82, 'About', 'page.jpg', 'https://www.twelve12.com/about-us/', NULL, NULL, '2017-08-27 18:09:52', '2017-08-27 18:10:01', 12, 4, NULL, 1),
(83, 'Contact', 'page.jpg', 'https://www.twelve12.com/contact/', NULL, NULL, '2017-08-27 18:12:26', '2017-08-27 18:12:32', 12, 4, NULL, 1),
(84, 'Contact', 'page.jpg', 'https://www.twelve12.com/contact/', NULL, NULL, '2017-08-27 18:12:26', '2017-08-27 18:12:49', 12, 9, 83, 1),
(85, 'inMotion', 'page.jpg', 'https://www.twelve12.com/project/inmotion/', NULL, NULL, '2017-08-27 18:13:41', '2017-08-27 18:13:48', 12, 4, NULL, 1),
(86, 'The Kitchen', 'page.jpg', 'http://www.twelve12.com/project/the-kitchen-at-westwood/', NULL, NULL, '2017-08-27 18:14:28', '2017-08-27 18:14:45', 12, 4, NULL, 1),
(87, 'Vampire Tools', 'page.jpg', 'https://www.twelve12.com/project/vampire-tools/', NULL, NULL, '2017-08-27 18:17:13', '2017-08-27 18:17:24', 12, 4, NULL, 1),
(88, 'GM Properties', 'page.jpg', 'https://www.twelve12.com/project/gm-properties/', NULL, NULL, '2017-08-27 18:17:57', '2017-08-27 18:18:12', 12, 4, NULL, 1),
(89, 'Blog 1', 'page.jpg', 'http://www.twelve12.com/blog/branding/brand-way-box/', NULL, NULL, '2017-08-27 18:20:02', '2017-08-27 18:20:13', 12, 4, NULL, 1),
(90, 'Blog 2', 'page.jpg', 'http://www.twelve12.com/blog/branding/defining-brand-twelve12/', NULL, NULL, '2017-08-27 18:20:18', '2017-08-27 18:20:39', 12, 4, NULL, 1),
(91, 'Vampire New Home', 'page.jpg', 'https://www.vampiretools.com/', NULL, NULL, '2017-08-29 12:13:51', '2017-08-29 12:14:07', 24, 4, NULL, 1);

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
(6, 84, 3, 1),
(11, 50, 3, 1),
(12, 82, 3, 1),
(13, 83, 3, 1),
(14, 85, 4, 1),
(15, 86, 4, 1),
(16, 87, 4, 1),
(17, 88, 4, 1),
(18, 89, 5, 1),
(19, 90, 5, 1),
(24, 51, 6, 1),
(25, 77, 6, 1);

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
(1, 'Youtube', 'proj.jpg', '2017-08-26 09:46:11', 1),
(2, 'TWSJ', 'proj.jpg', '2017-08-26 09:57:02', 1),
(3, 'BBC', 'proj.jpg', '2017-08-26 10:59:28', 1),
(4, 'Envato', 'proj.jpg', '2017-08-26 11:02:33', 1),
(5, 'Bilal TAS', 'proj.jpg', '2017-08-26 11:17:37', 1),
(11, 'Serdar Kızıltepe', 'proj.jpg', '2017-08-26 13:24:27', 1),
(12, 'Twelve12', 'proj.jpg', '2017-08-26 15:56:25', 2),
(13, 'Cuneyt Tas', 'proj.jpg', '2017-08-26 18:02:45', 5),
(14, '7 Diamonds', 'proj.jpg', '2017-08-26 18:08:07', 1),
(15, 'VantaQuest', 'proj.jpg', '2017-08-26 18:11:48', 1),
(16, 'Auro WM', 'proj.jpg', '2017-08-26 18:14:35', 1),
(17, 'Golden State', 'proj.jpg', '2017-08-26 18:15:50', 1),
(18, 'CloudStep', 'proj.jpg', '2017-08-26 18:16:21', 1),
(19, 'Hawaii Lassi', 'proj.jpg', '2017-08-26 18:17:16', 1),
(20, 'Juniper Cleaning', 'proj.jpg', '2017-08-26 18:18:42', 1),
(21, 'Cloud Compli', 'proj.jpg', '2017-08-26 18:44:02', 1),
(23, 'Jova Digital', 'proj.jpg', '2017-08-26 20:49:41', 1),
(24, 'Vampire Tools', 'proj.jpg', '2017-08-29 12:13:23', 1);

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
(463, 5, 1, 1),
(464, 11, 1, 1),
(465, 13, 1, 1),
(466, 12, 2, 1),
(467, 14, 2, 1),
(468, 16, 2, 1),
(469, 17, 2, 1),
(470, 21, 2, 1),
(471, 18, 2, 1),
(472, 19, 2, 1),
(473, 20, 2, 1),
(474, 15, 2, 1),
(475, 24, 2, 1),
(476, 23, 2, 1);

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
(82, 'internalize', 72, 24604, 'done', '2017-08-26 20:50:09', '2017-08-26 20:49:41', 'Internalization is complete.', 1),
(83, 'internalize', 73, 24606, 'done', '2017-08-26 20:50:48', '2017-08-26 20:49:41', 'Internalization is complete.', 1),
(84, 'internalize', 74, 24608, 'done', '2017-08-26 20:51:26', '2017-08-26 20:49:41', 'Internalization is complete.', 1),
(85, 'internalize', 75, 26572, 'done', '2017-08-27 10:53:24', '2017-08-27 10:53:12', 'Internalization is complete.', 1),
(86, 'internalize', 76, 26574, 'done', '2017-08-27 10:53:36', '2017-08-27 10:53:12', 'Internalization is complete.', 1),
(87, 'internalize', 77, 26673, 'done', '2017-08-27 11:23:48', '2017-08-27 11:23:37', 'Internalization is complete.', 1),
(88, 'internalize', 78, 26675, 'done', '2017-08-27 11:24:01', '2017-08-27 11:23:37', 'Internalization is complete.', 1),
(90, 'internalize', 76, 30472, 'done', '2017-08-27 17:50:11', '2017-08-27 17:49:59', 'Internalization is complete.', 1),
(91, 'internalize', 77, 30611, 'done', '2017-08-27 17:51:52', '2017-08-27 17:51:38', 'Internalization is complete.', 1),
(92, 'internalize', 78, 30774, 'done', '2017-08-27 18:01:28', '2017-08-27 18:01:15', 'Internalization is complete.', 1),
(93, 'internalize', 79, 30852, 'done', '2017-08-27 18:04:57', '2017-08-27 18:04:43', 'Internalization is complete.', 1),
(94, 'internalize', 80, 30854, 'done', '2017-08-27 18:05:12', '2017-08-27 18:04:43', 'Internalization is complete.', 1),
(95, 'internalize', 81, 30856, 'done', '2017-08-27 18:05:27', '2017-08-27 18:04:43', 'Internalization is complete.', 1),
(96, 'internalize', 82, 30943, 'done', '2017-08-27 18:10:13', '2017-08-27 18:09:52', 'Internalization is complete.', 1),
(97, 'internalize', 83, 30992, 'done', '2017-08-27 18:12:41', '2017-08-27 18:12:26', 'Internalization is complete.', 1),
(98, 'internalize', 84, 30994, 'done', '2017-08-27 18:12:56', '2017-08-27 18:12:26', 'Internalization is complete.', 1),
(99, 'internalize', 85, 31055, 'done', '2017-08-27 18:14:01', '2017-08-27 18:13:41', 'Internalization is complete.', 1),
(100, 'internalize', 86, 31089, 'done', '2017-08-27 18:15:01', '2017-08-27 18:14:28', 'Internalization is complete.', 1),
(101, 'internalize', 87, 31152, 'done', '2017-08-27 18:17:39', '2017-08-27 18:17:13', 'Internalization is complete.', 1),
(102, 'internalize', 88, 31196, 'done', '2017-08-27 18:18:27', '2017-08-27 18:17:57', 'Internalization is complete.', 1),
(103, 'internalize', 89, 31247, 'done', '2017-08-27 18:20:26', '2017-08-27 18:20:02', 'Internalization is complete.', 1),
(104, 'internalize', 90, 31286, 'done', '2017-08-27 18:20:53', '2017-08-27 18:20:18', 'Internalization is complete.', 1),
(105, 'internalize', 91, 5645, 'done', '2017-08-29 12:14:21', '2017-08-29 12:13:51', 'Internalization is complete.', 1),
(106, 'internalize', 92, 4995, 'done', '2017-08-30 20:50:27', '2017-08-30 20:50:09', 'Internalization is complete.', 1);

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
(3, 'project', 23, '4', 1),
(4, 'project', 23, '5', 2),
(5, 'page', 79, '5', 1),
(6, 'page', 80, '5', 1),
(7, 'page', 81, '5', 1),
(8, 'page', 83, '4', 1),
(9, 'page', 84, '4', 1),
(10, 'project', 23, '6', 1),
(11, 'project', 23, 'test@gmail.com', 1);

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
(12, 'project', 12, 1, 2),
(24, 'project', 13, 1, 5),
(273, 'page', 75, 1, 1),
(274, 'page', 76, 1, 1),
(276, 'page', 78, 1, 1),
(277, 'page', 79, 2, 1),
(278, 'page', 80, 2, 1),
(279, 'page', 81, 2, 1),
(291, 'page', 84, 4, 1),
(297, 'category', 3, 1, 1),
(298, 'page', 50, 2, 1),
(299, 'page', 82, 3, 1),
(300, 'page', 83, 4, 1),
(301, 'category', 4, 5, 1),
(302, 'page', 85, 6, 1),
(303, 'page', 86, 7, 1),
(304, 'page', 87, 8, 1),
(305, 'page', 88, 9, 1),
(306, 'category', 5, 10, 1),
(307, 'page', 89, 1, 1),
(308, 'page', 90, 2, 1),
(330, 'page', 91, 1, 1),
(403, 'category', 6, 1, 1),
(404, 'page', 51, 2, 1),
(405, 'page', 77, 3, 1),
(595, 'category', 0, 0, 1),
(596, 'project', 1, 1, 1),
(597, 'project', 2, 2, 1),
(598, 'project', 4, 3, 1),
(599, 'project', 3, 4, 1),
(600, 'category', 1, 5, 1),
(601, 'project', 5, 6, 1),
(602, 'project', 11, 7, 1),
(603, 'project', 13, 8, 1),
(604, 'category', 2, 9, 1),
(605, 'project', 12, 10, 1),
(606, 'project', 14, 11, 1),
(607, 'project', 16, 12, 1),
(608, 'project', 17, 13, 1),
(609, 'project', 21, 14, 1),
(610, 'project', 18, 15, 1),
(611, 'project', 19, 16, 1),
(612, 'project', 20, 17, 1),
(613, 'project', 15, 18, 1),
(614, 'project', 24, 19, 1),
(615, 'project', 23, 20, 1);

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
(5, 'cuneyt', 'cuneyt@twelve12.com', '$2y$10$FlJ0PwBy6.5m8MXqIDMv5u.CsTW9w7bEgmlzLUCG9il6ZaN6KMmVC', 'Cuneyt', 'Tas', 'joey.png', 0, '2017-06-25 09:28:07', 2),
(6, 'serdar-kiziltepe', 'serdar.kiziltepe@gmail.com', '$2y$10$xOHRtNIOyPEQ9zo/LdN.NuKeCrDPvu51bRczoHbbD4h13Jz..ZD6e', 'Serdar', 'Kiziltepe', NULL, 0, '2017-09-16 19:57:18', 2);

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
  `version_name` text NOT NULL,
  `version_number` float NOT NULL,
  `version_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `version_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `version_page_ID` bigint(20) NOT NULL,
  `version_user_ID` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  ADD KEY `user_ID` (`version_user_ID`),
  ADD KEY `page_ID` (`version_page_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `archives`
--
ALTER TABLE `archives`
  MODIFY `archive_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `deletes`
--
ALTER TABLE `deletes`
  MODIFY `delete_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
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
  MODIFY `page_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;
--
-- AUTO_INCREMENT for table `page_cat_connect`
--
ALTER TABLE `page_cat_connect`
  MODIFY `page_cat_connect_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT for table `project_cat_connect`
--
ALTER TABLE `project_cat_connect`
  MODIFY `project_cat_connect_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=477;
--
-- AUTO_INCREMENT for table `queues`
--
ALTER TABLE `queues`
  MODIFY `queue_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;
--
-- AUTO_INCREMENT for table `shares`
--
ALTER TABLE `shares`
  MODIFY `share_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `sorting`
--
ALTER TABLE `sorting`
  MODIFY `sort_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=616;
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
  MODIFY `version_ID` bigint(20) NOT NULL AUTO_INCREMENT;
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
  ADD CONSTRAINT `versions_ibfk_2` FOREIGN KEY (`version_user_ID`) REFERENCES `users` (`user_ID`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `versions_ibfk_3` FOREIGN KEY (`version_page_ID`) REFERENCES `pages` (`page_ID`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
