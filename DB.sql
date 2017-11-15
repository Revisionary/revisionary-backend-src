-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 15, 2017 at 08:30 PM
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
(6, 'Main Sayfalar', '13', 1),
(7, 'Test', '28', 1),
(8, 'My Pages', '5', 1);

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
(1, 'Youtube Home', 'page.jpg', 'https://www.youtube.com/', NULL, NULL, NULL, '2017-08-26 09:46:11', '2017-08-26 09:46:22', 1, 4, NULL, 1),
(2, 'Youtube Home', 'page.jpg', 'https://www.youtube.com/', NULL, NULL, NULL, '2017-08-26 09:46:11', '2017-08-26 09:47:01', 1, 1, 1, 1),
(3, 'Youtube Home', 'page.jpg', 'https://www.youtube.com/', NULL, NULL, NULL, '2017-08-26 09:46:11', '2017-08-26 09:47:48', 1, 5, 1, 1),
(4, 'Youtube Home', 'page.jpg', 'https://www.youtube.com/', NULL, NULL, NULL, '2017-08-26 09:46:11', '2017-08-26 09:48:21', 1, 6, 1, 1),
(5, 'Youtube Home', 'page.jpg', 'https://www.youtube.com/', NULL, NULL, NULL, '2017-08-26 09:46:11', '2017-08-26 09:49:00', 1, 9, 1, 1),
(6, 'Wall Street Europe', 'page.jpg', 'https://www.wsj.com/', NULL, NULL, NULL, '2017-08-26 09:57:02', '2017-08-26 09:58:48', 2, 4, NULL, 1),
(7, 'Wall Street Europe', 'page.jpg', 'https://www.wsj.com/', NULL, NULL, NULL, '2017-08-26 09:57:02', '2017-08-26 10:50:13', 2, 9, 6, 1),
(8, 'BBC Home', 'page.jpg', 'http://www.bbc.com/', NULL, NULL, NULL, '2017-08-26 10:59:28', '2017-08-26 11:00:05', 3, 4, NULL, 1),
(9, 'BBC Home', 'page.jpg', 'http://www.bbc.com/', NULL, NULL, NULL, '2017-08-26 10:59:28', '2017-08-26 11:01:16', 3, 9, 8, 1),
(10, 'Envato Home', 'page.jpg', 'https://envato.com/', NULL, NULL, NULL, '2017-08-26 11:02:33', '2017-08-26 11:04:31', 4, 4, NULL, 1),
(11, 'Envato Home', 'page.jpg', 'https://envato.com/', NULL, NULL, NULL, '2017-08-26 11:02:33', '2017-08-26 11:05:51', 4, 1, 10, 1),
(12, 'Envato Home', 'page.jpg', 'https://envato.com/', NULL, NULL, NULL, '2017-08-26 11:02:33', '2017-08-26 11:11:26', 4, 9, 10, 1),
(13, 'Bilal\'s Home', 'page.jpg', 'http://www.bilaltas.net/', NULL, NULL, NULL, '2017-08-26 11:17:37', '2017-08-26 11:18:02', 5, 4, NULL, 1),
(14, 'Bilal\'s Home', 'page.jpg', 'http://www.bilaltas.net/', NULL, NULL, NULL, '2017-08-26 11:17:37', '2017-08-26 11:18:58', 5, 1, 13, 1),
(15, 'Bilal\'s Home', 'page.jpg', 'http://www.bilaltas.net/', NULL, NULL, NULL, '2017-08-26 11:17:37', '2017-08-26 11:20:27', 5, 6, 13, 1),
(16, 'Bilal\'s Home', 'page.jpg', 'http://www.bilaltas.net/', NULL, NULL, NULL, '2017-08-26 11:17:37', '2017-08-26 11:22:58', 5, 9, 13, 1),
(45, 'Serdar\'s Home', 'page.jpg', 'http://serdarkiziltepe.com/', NULL, NULL, NULL, '2017-08-26 13:24:27', '2017-08-26 13:25:13', 11, 4, NULL, 1),
(46, 'Serdar\'s Home', 'page.jpg', 'http://serdarkiziltepe.com/', NULL, NULL, NULL, '2017-08-26 13:24:27', '2017-08-26 13:25:29', 11, 1, 45, 1),
(47, 'Serdar\'s Home', 'page.jpg', 'http://serdarkiziltepe.com/', NULL, NULL, NULL, '2017-08-26 13:24:27', '2017-08-26 13:25:46', 11, 5, 45, 1),
(48, 'Serdar\'s Home', 'page.jpg', 'http://serdarkiziltepe.com/', NULL, NULL, NULL, '2017-08-26 13:24:27', '2017-08-26 13:26:02', 11, 6, 45, 1),
(49, 'Serdar\'s Home', 'page.jpg', 'http://serdarkiziltepe.com/', NULL, NULL, NULL, '2017-08-26 13:24:27', '2017-08-26 13:26:19', 11, 9, 45, 1),
(50, 'Home Page', 'page.jpg', 'https://www.twelve12.com', NULL, NULL, NULL, '2017-08-26 15:56:25', '2017-08-26 15:56:33', 12, 4, NULL, 2),
(51, 'Cuneyt\'s Home', 'page.jpg', 'http://www.cuneyt-tas.com/', NULL, NULL, NULL, '2017-08-26 18:02:45', '2017-08-26 18:02:52', 13, 4, NULL, 5),
(52, 'Cuneyt\'s Home', 'page.jpg', 'http://www.cuneyt-tas.com/', NULL, NULL, NULL, '2017-08-26 18:02:45', '2017-08-26 18:03:54', 13, 1, 51, 5),
(53, 'Cuneyt\'s Home', 'page.jpg', 'http://www.cuneyt-tas.com/', NULL, NULL, NULL, '2017-08-26 18:02:45', '2017-08-26 18:04:04', 13, 5, 51, 5),
(54, 'Cuneyt\'s Home', 'page.jpg', 'http://www.cuneyt-tas.com/', NULL, NULL, NULL, '2017-08-26 18:02:46', '2017-08-26 18:04:14', 13, 6, 51, 5),
(55, 'Cuneyt\'s Home', 'page.jpg', 'http://www.cuneyt-tas.com/', NULL, NULL, NULL, '2017-08-26 18:02:46', '2017-08-26 18:04:25', 13, 9, 51, 5),
(56, '7Diamonds Home', 'page.jpg', 'https://dev.7diamonds.com/', NULL, NULL, NULL, '2017-08-26 18:08:07', '2017-08-26 18:08:55', 14, 4, NULL, 1),
(57, '7Diamonds Home', 'page.jpg', 'https://dev.7diamonds.com/', NULL, NULL, NULL, '2017-08-26 18:08:07', '2017-08-26 18:09:57', 14, 1, 56, 1),
(58, '7Diamonds Home', 'page.jpg', 'https://dev.7diamonds.com/', NULL, NULL, NULL, '2017-08-26 18:08:07', '2017-08-26 18:10:51', 14, 5, 56, 1),
(59, '7Diamonds Home', 'page.jpg', 'https://dev.7diamonds.com/', NULL, NULL, NULL, '2017-08-26 18:08:07', '2017-08-26 18:11:47', 14, 9, 56, 1),
(60, 'VantaQuest Home', 'page.jpg', 'http://vantaquest.twelve12.com/', NULL, NULL, NULL, '2017-08-26 18:11:48', '2017-08-26 18:12:35', 15, 4, NULL, 1),
(61, 'VantaQuest Home', 'page.jpg', 'http://vantaquest.twelve12.com/', NULL, NULL, NULL, '2017-08-26 18:11:48', '2017-08-26 18:12:48', 15, 1, 60, 1),
(62, 'VantaQuest Home', 'page.jpg', 'http://vantaquest.twelve12.com/', NULL, NULL, NULL, '2017-08-26 18:11:48', '2017-08-26 18:13:00', 15, 5, 60, 1),
(63, 'VantaQuest Home', 'page.jpg', 'http://vantaquest.twelve12.com/', NULL, NULL, NULL, '2017-08-26 18:11:48', '2017-08-26 18:13:10', 15, 6, 60, 1),
(64, 'VantaQuest Home', 'page.jpg', 'http://vantaquest.twelve12.com/', NULL, NULL, NULL, '2017-08-26 18:11:48', '2017-08-26 18:13:21', 15, 9, 60, 1),
(65, 'Auro Home', 'page.jpg', 'https://www.aurowm.com/', NULL, NULL, NULL, '2017-08-26 18:14:35', '2017-08-26 18:14:41', 16, 4, NULL, 1),
(66, 'Golden State Home', 'page.jpg', 'https://www.goldenstatewm.com/', NULL, NULL, NULL, '2017-08-26 18:15:50', '2017-08-26 18:15:59', 17, 4, NULL, 1),
(67, 'Cloud Step Home', 'page.jpg', 'https://www.cloudstep.com/', NULL, NULL, NULL, '2017-08-26 18:16:21', '2017-08-26 18:17:19', 18, 4, NULL, 1),
(68, 'Hawaii Home', 'page.jpg', 'http://www.hawaiilassi.com/', NULL, NULL, NULL, '2017-08-26 18:17:16', '2017-08-26 18:18:06', 19, 4, NULL, 1),
(69, 'Hawaii Home', 'page.jpg', 'http://www.hawaiilassi.com/', NULL, NULL, NULL, '2017-08-26 18:17:16', '2017-08-26 18:18:43', 19, 6, 68, 1),
(70, 'Juniper Home', 'page.jpg', 'http://junipercleaning.com/', NULL, NULL, NULL, '2017-08-26 18:18:42', '2017-08-26 18:19:09', 20, 4, NULL, 1),
(71, 'Cloud Compli Home', 'page.jpg', 'http://cloudcompli.com/', NULL, NULL, NULL, '2017-08-26 18:44:02', '2017-08-26 18:44:17', 21, 4, NULL, 1),
(72, 'Jova Home', 'page.jpg', 'https://jovadigital.com/', NULL, NULL, NULL, '2017-08-26 20:49:41', '2017-08-26 20:49:47', 23, 4, NULL, 1),
(73, 'Jova Home', 'page.jpg', 'https://jovadigital.com/', NULL, NULL, NULL, '2017-08-26 20:49:41', '2017-08-26 20:50:22', 23, 1, 72, 1),
(74, 'Jova Home', 'page.jpg', 'https://jovadigital.com/', NULL, NULL, NULL, '2017-08-26 20:49:41', '2017-08-26 20:50:59', 23, 9, 72, 1),
(77, 'Resume Page', 'page.jpg', 'http://www.cuneyt-tas.com/ozgecmis/', NULL, NULL, NULL, '2017-08-27 17:51:38', '2017-08-27 17:51:44', 13, 4, NULL, 1),
(78, 'My Resume', 'page.jpg', 'http://www.bilaltas.net/resume/my-resume/', NULL, NULL, NULL, '2017-08-27 18:01:15', '2017-08-27 18:01:19', 5, 4, NULL, 1),
(79, 'Contact Page', 'page.jpg', 'http://www.bilaltas.net/contact/', NULL, NULL, NULL, '2017-08-27 18:04:43', '2017-08-27 18:04:49', 5, 4, NULL, 1),
(80, 'Contact Page', 'page.jpg', 'http://www.bilaltas.net/contact/', NULL, NULL, NULL, '2017-08-27 18:04:43', '2017-08-27 18:05:04', 5, 1, 79, 1),
(81, 'Contact Page', 'page.jpg', 'http://www.bilaltas.net/contact/', NULL, NULL, NULL, '2017-08-27 18:04:43', '2017-08-27 18:05:19', 5, 9, 79, 1),
(82, 'About', 'page.jpg', 'https://www.twelve12.com/about-us/', NULL, NULL, NULL, '2017-08-27 18:09:52', '2017-08-27 18:10:01', 12, 4, NULL, 1),
(83, 'Contact', 'page.jpg', 'https://www.twelve12.com/contact/', NULL, NULL, NULL, '2017-08-27 18:12:26', '2017-08-27 18:12:32', 12, 4, NULL, 1),
(84, 'Contact', 'page.jpg', 'https://www.twelve12.com/contact/', NULL, NULL, NULL, '2017-08-27 18:12:26', '2017-08-27 18:12:49', 12, 9, 83, 1),
(85, 'inMotion', 'page.jpg', 'https://www.twelve12.com/project/inmotion/', NULL, NULL, NULL, '2017-08-27 18:13:41', '2017-08-27 18:13:48', 12, 4, NULL, 1),
(86, 'The Kitchen', 'page.jpg', 'http://www.twelve12.com/project/the-kitchen-at-westwood/', NULL, NULL, NULL, '2017-08-27 18:14:28', '2017-08-27 18:14:45', 12, 4, NULL, 1),
(87, 'Vampire Tools', 'page.jpg', 'https://www.twelve12.com/project/vampire-tools/', NULL, NULL, NULL, '2017-08-27 18:17:13', '2017-08-27 18:17:24', 12, 4, NULL, 1),
(88, 'GM Properties', 'page.jpg', 'https://www.twelve12.com/project/gm-properties/', NULL, NULL, NULL, '2017-08-27 18:17:57', '2017-08-27 18:18:12', 12, 4, NULL, 1),
(89, 'Blog 1', 'page.jpg', 'http://www.twelve12.com/blog/branding/brand-way-box/', NULL, NULL, NULL, '2017-08-27 18:20:02', '2017-08-27 18:20:13', 12, 4, NULL, 1),
(90, 'Blog 2', 'page.jpg', 'http://www.twelve12.com/blog/branding/defining-brand-twelve12/', NULL, NULL, NULL, '2017-08-27 18:20:18', '2017-08-27 18:20:39', 12, 4, NULL, 1),
(91, 'Vampire New Home', 'page.jpg', 'https://www.vampiretools.com/', NULL, NULL, NULL, '2017-08-29 12:13:51', '2017-08-29 12:14:07', 24, 4, NULL, 1),
(107, 'RF Home', 'page.jpg', 'http://recordfixer.twelve12.com/', NULL, NULL, NULL, '2017-09-17 13:42:34', '2017-09-17 13:42:40', 27, 4, NULL, 1),
(108, 'About', 'page.jpg', 'http://recordfixer.twelve12.com/about/', NULL, NULL, NULL, '2017-09-17 13:44:47', '2017-09-17 13:44:54', 27, 4, NULL, 1),
(109, 'Contact', 'page.jpg', 'http://recordfixer.twelve12.com/contact/', NULL, NULL, NULL, '2017-09-17 13:47:12', '2017-09-17 13:47:18', 27, 4, NULL, 1),
(110, 'Home', 'page.jpg', 'https://www.coolsis.com/', NULL, NULL, NULL, '2017-09-20 07:39:06', '2017-09-20 07:39:23', 28, 4, NULL, 1),
(112, 'Home', 'page.jpg', 'https://thelookfitness.com/', NULL, NULL, NULL, '2017-09-20 08:00:14', '2017-09-20 08:00:27', 30, 4, NULL, 1),
(113, 'My Resume', 'page.jpg', 'http://www.bilaltas.net/resume/my-resume/', NULL, NULL, NULL, '2017-09-24 18:25:50', '2017-09-24 18:25:58', 5, 6, 78, 1),
(114, 'My Resume', 'page.jpg', 'http://www.bilaltas.net/resume/my-resume/', NULL, NULL, NULL, '2017-09-24 18:26:31', '2017-09-24 18:26:35', 5, 10, 78, 1),
(115, 'Resume Page', 'page.jpg', 'http://www.cuneyt-tas.com/ozgecmis/', NULL, NULL, NULL, '2017-09-25 08:45:25', '2017-09-25 08:45:32', 13, 9, 77, 1),
(116, 'Home Page', 'page.jpg', 'https://www.pacificchorale.org/', NULL, NULL, NULL, '2017-09-27 17:13:02', '2017-09-27 17:13:52', 31, 4, NULL, 1),
(117, 'Home Page', 'page.jpg', 'https://www.pacificchorale.org/', NULL, NULL, NULL, '2017-09-27 17:13:02', '2017-09-27 17:15:46', 31, 1, 116, 1),
(118, 'Home Page', 'page.jpg', 'https://www.pacificchorale.org/', NULL, NULL, NULL, '2017-09-27 17:13:02', '2017-09-27 17:17:21', 31, 5, 116, 1),
(119, 'Home Page', 'page.jpg', 'https://www.pacificchorale.org/', NULL, NULL, NULL, '2017-09-27 17:13:02', '2017-09-27 17:18:48', 31, 6, 116, 1),
(120, 'Home Page', 'page.jpg', 'https://www.pacificchorale.org/', NULL, NULL, NULL, '2017-09-27 17:13:02', '2017-09-27 17:20:09', 31, 9, 116, 1),
(133, 'Home', 'page.jpg', 'http://soho.twelve12.com/', NULL, NULL, NULL, '2017-09-30 00:09:09', '2017-09-30 00:09:18', 32, 4, NULL, 1),
(134, 'Home', 'page.jpg', 'http://soho.twelve12.com/', NULL, NULL, NULL, '2017-09-30 00:09:47', '2017-09-30 00:09:56', 32, 3, 133, 1),
(170, 'Home', 'page.jpg', 'http://dnomak.com/', NULL, NULL, NULL, '2017-10-27 14:35:22', '2017-10-27 14:35:34', 37, 4, NULL, 1),
(171, 'Home', 'page.jpg', 'http://dnomak.com/', NULL, NULL, NULL, '2017-10-27 14:35:22', '2017-10-27 14:35:34', 37, 6, 170, 1),
(172, 'Home', 'page.jpg', 'http://dnomak.com/', NULL, NULL, NULL, '2017-10-27 14:36:24', '2017-10-27 14:36:33', 37, 9, 171, 1),
(173, 'Home', 'page.jpg', 'https://www.unitedstreetsofart.com/', NULL, NULL, NULL, '2017-10-29 17:17:53', '2017-10-29 17:18:06', 38, 4, NULL, 1),
(174, 'Home', 'page.jpg', 'https://www.unitedstreetsofart.com/', NULL, NULL, NULL, '2017-10-29 17:17:54', '2017-10-29 17:18:08', 38, 6, 173, 1),
(175, 'Home', 'page.jpg', 'https://www.unitedstreetsofart.com/', NULL, NULL, NULL, '2017-10-29 17:17:54', '2017-10-29 17:18:41', 38, 9, 173, 1),
(176, 'Home', 'page.jpg', 'https://www.7diamonds.com', NULL, NULL, NULL, '2017-10-29 17:33:56', '2017-10-29 17:34:09', 39, 4, NULL, 1),
(177, 'Home', 'page.jpg', 'https://www.7diamonds.com', NULL, NULL, NULL, '2017-10-29 17:33:56', '2017-10-29 17:34:09', 39, 1, 176, 1),
(178, 'Home', 'page.jpg', 'https://www.7diamonds.com', NULL, NULL, NULL, '2017-10-29 17:33:56', '2017-10-29 17:34:35', 39, 5, 176, 1),
(179, 'Home', 'page.jpg', 'https://www.7diamonds.com', NULL, NULL, NULL, '2017-10-29 17:33:56', '2017-10-29 17:34:35', 39, 6, 176, 1),
(180, 'Home', 'page.jpg', 'https://www.7diamonds.com', NULL, NULL, NULL, '2017-10-29 17:33:56', '2017-10-29 17:35:04', 39, 9, 176, 1),
(181, 'Home Page', 'page.jpg', 'http://www.cuneyt-tas.com', NULL, NULL, NULL, '2017-10-29 17:39:01', '2017-10-29 17:39:09', 13, 4, NULL, 1),
(182, 'Test Home', 'page.jpg', 'http://dev.cuneyt-tas.com/', NULL, NULL, NULL, '2017-11-12 09:29:56', '2017-11-12 09:30:05', 40, 4, NULL, 1),
(183, 'Test Home', 'page.jpg', 'http://dev.cuneyt-tas.com/', NULL, NULL, NULL, '2017-11-12 09:29:57', '2017-11-12 09:30:05', 40, 9, 182, 1),
(184, 'Intro Page', 'page.jpg', 'http://dev.web-estimator.com/', NULL, NULL, NULL, '2017-11-12 13:01:28', '2017-11-12 13:01:34', 41, 4, NULL, 1),
(185, 'Intro Page', 'page.jpg', 'http://dev.web-estimator.com/', NULL, NULL, NULL, '2017-11-12 13:01:28', '2017-11-12 13:01:34', 41, 7, 184, 1),
(187, 'WP Home', 'page.jpg', 'http://localhost/wordpress/', NULL, NULL, NULL, '2017-11-12 19:02:11', '2017-11-12 19:02:17', 42, 4, NULL, 1),
(188, 'WP Home', 'page.jpg', 'http://localhost/wordpress/', NULL, NULL, NULL, '2017-11-15 08:58:22', '2017-11-15 08:58:31', 42, 7, 187, 1);

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
(24, 51, 6, 1),
(26, 110, 7, 1),
(27, 13, 8, 1),
(28, 78, 8, 1),
(29, 79, 8, 1),
(142, 82, 3, 1),
(143, 83, 3, 1),
(144, 85, 4, 1),
(145, 86, 4, 1),
(146, 87, 4, 1),
(147, 88, 4, 1),
(148, 90, 5, 1),
(149, 89, 5, 1),
(151, 181, 6, 1),
(152, 77, 6, 1);

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

--
-- Dumping data for table `pins`
--

INSERT INTO `pins` (`pin_ID`, `pin_type`, `pin_private`, `pin_complete`, `pin_created`, `pin_modified`, `pin_x`, `pin_y`, `pin_element_index`, `version_ID`, `user_ID`) VALUES
(1, 'standard', 0, 0, '2017-11-15 10:03:42', '2017-11-15 10:03:42', 400, 600, 0, 2, 1),
(2, 'live', 0, 0, '2017-11-15 10:03:42', '2017-11-15 10:10:45', 500, 600, 0, 2, 1),
(3, 'standard', 1, 0, '2017-11-15 10:03:42', '2017-11-15 10:10:45', 600, 600, 0, 2, 1),
(4, 'live', 1, 0, '2017-11-15 10:03:42', '2017-11-15 10:10:45', 700, 600, 0, 2, 1),
(5, 'standard', 0, 1, '2017-11-15 10:03:42', '2017-11-15 10:20:31', 400, 700, 0, 2, 1),
(6, 'live', 0, 1, '2017-11-15 10:03:42', '2017-11-15 10:20:34', 500, 700, 0, 2, 1),
(7, 'standard', 1, 1, '2017-11-15 10:03:42', '2017-11-15 10:20:37', 600, 700, 0, 2, 1),
(8, 'live', 1, 1, '2017-11-15 10:03:42', '2017-11-15 10:20:39', 700, 700, 0, 2, 1);

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
(24, 'Vampire Tools', 'proj.jpg', '2017-08-29 12:13:23', 1),
(27, 'Record Fixer', 'proj.jpg', '2017-09-17 13:42:13', 1),
(28, 'Coolsis', 'proj.jpg', '2017-09-20 07:39:00', 1),
(30, 'The Look Fitness', 'proj.jpg', '2017-09-20 08:00:14', 1),
(31, 'Pacific Chorale', 'proj.jpg', '2017-09-27 17:11:49', 1),
(32, 'Soho Taco', 'proj.jpg', '2017-09-30 00:09:09', 1),
(37, 'Doğukan', 'proj.jpg', '2017-10-27 14:35:22', 1),
(38, 'US Art', 'proj.jpg', '2017-10-29 17:17:38', 1),
(39, '7 Diamonds New', 'proj.jpg', '2017-10-29 17:33:56', 1),
(40, 'Cuneyt TEST', 'proj.jpg', '2017-11-12 09:29:56', 1),
(41, 'Web Estimator (CHECK!)', 'proj.jpg', '2017-11-12 13:01:28', 1),
(42, 'WP Test', 'proj.jpg', '2017-11-12 13:03:12', 1);

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
(1231, 13, 1, 1),
(1232, 5, 1, 1),
(1233, 11, 1, 1),
(1234, 12, 2, 1),
(1235, 14, 2, 1),
(1236, 17, 2, 1),
(1237, 16, 2, 1),
(1238, 21, 2, 1),
(1239, 18, 2, 1),
(1240, 19, 2, 1),
(1241, 20, 2, 1),
(1242, 15, 2, 1),
(1243, 24, 2, 1),
(1244, 28, 2, 1),
(1245, 23, 2, 1),
(1246, 27, 2, 1),
(1247, 30, 2, 1),
(1248, 31, 2, 1),
(1249, 32, 2, 1),
(1252, 38, 2, 1),
(1253, 39, 2, 1),
(1254, 40, 2, 1),
(1255, 41, 2, 1),
(1256, 42, 2, 1);

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
(81, 'internalize', 147, 12363, 'done', '2017-10-21 18:54:20', '2017-10-21 18:53:59', 'Internalization is complete.', 1),
(82, 'internalize', 148, 12365, 'done', '2017-10-21 18:54:18', '2017-10-21 18:53:59', 'Internalization is complete.', 1),
(83, 'internalize', 149, 12367, 'done', '2017-10-21 18:54:39', '2017-10-21 18:53:59', 'Internalization is complete.', 1),
(84, 'internalize', 150, 12369, 'done', '2017-10-21 18:54:39', '2017-10-21 18:53:59', 'Internalization is complete.', 1),
(85, 'internalize', 151, 13086, 'done', '2017-10-21 19:09:28', '2017-10-21 19:09:06', 'Internalization is complete.', 1),
(86, 'internalize', 152, 13088, 'done', '2017-10-21 19:09:19', '2017-10-21 19:09:06', 'Internalization is complete.', 1),
(87, 'internalize', 153, 13090, 'done', '2017-10-21 19:09:34', '2017-10-21 19:09:06', 'Internalization is complete.', 1),
(88, 'internalize', 154, 13092, 'done', '2017-10-21 19:09:41', '2017-10-21 19:09:06', 'Internalization is complete.', 1),
(89, 'internalize', 155, 13094, 'done', '2017-10-21 19:09:49', '2017-10-21 19:09:06', 'Internalization is complete.', 1),
(90, 'internalize', 156, 13096, 'done', '2017-10-21 19:09:54', '2017-10-21 19:09:06', 'Internalization is complete.', 1),
(91, 'internalize', 157, 13346, 'done', '2017-10-21 19:10:55', '2017-10-21 19:10:42', 'Internalization is complete.', 1),
(92, 'internalize', 158, 13348, 'done', '2017-10-21 19:10:56', '2017-10-21 19:10:42', 'Internalization is complete.', 1),
(93, 'internalize', 159, 13350, 'done', '2017-10-21 19:11:09', '2017-10-21 19:10:42', 'Internalization is complete.', 1),
(94, 'internalize', 158, 16065, 'error', '2017-10-22 14:07:53', '2017-10-22 14:00:58', 'Process is not working.', 1),
(95, 'internalize', 158, 16075, 'error', '2017-10-22 14:08:06', '2017-10-22 14:08:05', 'Process is not working.', 1),
(96, 'internalize', 158, 16082, 'error', '2017-10-22 14:08:17', '2017-10-22 14:08:16', 'Process is not working.', 1),
(97, 'internalize', 160, 16188, 'done', '2017-10-22 14:13:12', '2017-10-22 14:12:54', 'Internalization is complete.', 1),
(98, 'internalize', 161, 16190, 'done', '2017-10-22 14:13:08', '2017-10-22 14:12:54', 'Internalization is complete.', 1),
(99, 'internalize', 162, 16192, 'done', '2017-10-22 14:13:23', '2017-10-22 14:12:55', 'Internalization is complete.', 1),
(100, 'internalize', 163, 16194, 'done', '2017-10-22 14:13:34', '2017-10-22 14:12:55', 'Internalization is complete.', 1),
(101, 'internalize', 164, 16723, 'done', '2017-10-22 14:28:44', '2017-10-22 14:28:35', 'Internalization is complete.', 1),
(102, 'internalize', 165, 16939, 'done', '2017-10-22 14:42:05', '2017-10-22 14:41:15', 'Internalization is complete.', 1),
(103, 'internalize', 166, 19435, 'done', '2017-10-22 20:41:07', '2017-10-22 20:40:59', 'Internalization is complete.', 1),
(104, 'internalize', 167, 19437, 'done', '2017-10-22 20:41:07', '2017-10-22 20:40:59', 'Internalization is complete.', 1),
(105, 'internalize', 168, 19439, 'done', '2017-10-22 20:41:13', '2017-10-22 20:40:59', 'Internalization is complete.', 1),
(106, 'internalize', 169, 19441, 'done', '2017-10-22 20:41:13', '2017-10-22 20:40:59', 'Internalization is complete.', 1),
(107, 'internalize', 170, 5234, 'done', '2017-10-27 14:35:38', '2017-10-27 14:35:22', 'Internalization is complete.', 1),
(108, 'internalize', 171, 5236, 'done', '2017-10-27 14:35:38', '2017-10-27 14:35:22', 'Internalization is complete.', 1),
(109, 'internalize', 172, 5355, 'done', '2017-10-27 14:36:35', '2017-10-27 14:36:24', 'Internalization is complete.', 1),
(110, 'internalize', 173, 10934, 'done', '2017-10-29 17:18:30', '2017-10-29 17:17:53', 'Internalization is complete.', 1),
(111, 'internalize', 174, 10936, 'done', '2017-10-29 17:18:32', '2017-10-29 17:17:54', 'Internalization is complete.', 1),
(112, 'internalize', 175, 10938, 'done', '2017-10-29 17:19:05', '2017-10-29 17:17:54', 'Internalization is complete.', 1),
(113, 'internalize', 176, 11192, 'done', '2017-10-29 17:34:22', '2017-10-29 17:33:56', 'Internalization is complete.', 1),
(114, 'internalize', 177, 11194, 'done', '2017-10-29 17:34:22', '2017-10-29 17:33:56', 'Internalization is complete.', 1),
(115, 'internalize', 178, 11196, 'done', '2017-10-29 17:34:47', '2017-10-29 17:33:56', 'Internalization is complete.', 1),
(116, 'internalize', 179, 11198, 'done', '2017-10-29 17:34:47', '2017-10-29 17:33:56', 'Internalization is complete.', 1),
(117, 'internalize', 180, 11200, 'done', '2017-10-29 17:35:19', '2017-10-29 17:33:56', 'Internalization is complete.', 1),
(118, 'internalize', 181, 11457, 'done', '2017-10-29 17:39:17', '2017-10-29 17:39:01', 'Internalization is complete.', 1),
(119, 'internalize', 50, 27905, 'done', '2017-11-11 16:21:49', '2017-11-11 16:21:25', 'Internalization is complete.', 1),
(120, 'internalize', 50, 28416, 'done', '2017-11-11 17:01:15', '2017-11-11 17:00:48', 'Internalization is complete.', 1),
(121, 'internalize', 82, 28890, 'done', '2017-11-11 17:03:18', '2017-11-11 17:03:02', 'Internalization is complete.', 1),
(122, 'internalize', 82, 29011, 'done', '2017-11-11 17:04:45', '2017-11-11 17:04:26', 'Internalization is complete.', 1),
(123, 'internalize', 133, 29978, 'done', '2017-11-11 17:37:24', '2017-11-11 17:36:57', 'Internalization is complete.', 1),
(124, 'internalize', 116, 30291, 'done', '2017-11-11 17:41:28', '2017-11-11 17:40:39', 'Internalization is complete.', 1),
(125, 'internalize', 182, 2738, 'done', '2017-11-12 09:30:09', '2017-11-12 09:29:56', 'Internalization is complete.', 1),
(126, 'internalize', 183, 2740, 'done', '2017-11-12 09:30:09', '2017-11-12 09:29:57', 'Internalization is complete.', 1),
(127, 'internalize', 184, 4578, 'done', '2017-11-12 13:01:36', '2017-11-12 13:01:28', 'Internalization is complete.', 1),
(128, 'internalize', 185, 4580, 'done', '2017-11-12 13:01:36', '2017-11-12 13:01:28', 'Internalization is complete.', 1),
(129, 'internalize', 186, 4686, 'error', '2017-11-12 13:03:30', '2017-11-12 13:03:20', 'Process is not working.', 1),
(130, 'internalize', 186, 4927, 'done', '2017-11-12 13:06:55', '2017-11-12 13:06:47', 'Internalization is complete.', 1),
(131, 'internalize', 187, 7172, 'done', '2017-11-12 19:02:20', '2017-11-12 19:02:11', 'Internalization is complete.', 1),
(132, 'internalize', 188, 3332, 'done', '2017-11-15 08:58:34', '2017-11-15 08:58:22', 'Internalization is complete.', 1),
(133, 'internalize', 188, 3966, 'done', '2017-11-15 09:43:28', '2017-11-15 09:43:18', 'Internalization is complete.', 1),
(134, 'internalize', 187, 4039, 'done', '2017-11-15 09:45:39', '2017-11-15 09:45:29', 'Internalization is complete.', 1),
(135, 'internalize', 188, 4565, 'done', '2017-11-15 17:26:56', '2017-11-15 17:26:44', 'Internalization is complete.', 1);

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
(3, 'project', 23, '4', 1),
(4, 'project', 23, '5', 2),
(5, 'page', 79, '5', 1),
(8, 'page', 83, '4', 1),
(10, 'project', 23, '6', 1),
(11, 'project', 23, 'test@gmail.com', 1),
(58, 'project', 12, '6', 1),
(59, 'page', 116, '2', 1),
(60, 'page', 116, 'test@gmail.com', 1),
(61, 'project', 31, '3', 1),
(62, 'project', 32, '5', 1),
(65, 'project', 37, 'me@dnomak.com', 1);

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
(278, 'page', 80, 2, 1),
(279, 'page', 81, 2, 1),
(291, 'page', 84, 4, 1),
(298, 'page', 50, 2, 1),
(330, 'page', 91, 1, 1),
(404, 'page', 51, 2, 1),
(645, 'page', 107, 1, 1),
(646, 'page', 108, 2, 1),
(647, 'page', 109, 3, 1),
(739, 'category', 7, 1, 1),
(740, 'page', 110, 2, 1),
(1040, 'category', 8, 1, 1),
(1041, 'page', 13, 2, 1),
(1042, 'page', 78, 3, 1),
(1043, 'page', 79, 4, 1),
(1095, 'page', 116, 1, 1),
(1096, 'page', 117, 1, 1),
(1097, 'page', 118, 1, 1),
(1098, 'page', 119, 1, 1),
(1099, 'page', 120, 1, 1),
(1143, 'page', 133, 2, 1),
(1589, 'project', 3, 1, 1),
(1590, 'project', 2, 2, 1),
(1591, 'project', 4, 3, 1),
(1592, 'project', 1, 4, 1),
(1593, 'category', 1, 5, 1),
(1594, 'project', 13, 6, 1),
(1595, 'project', 5, 7, 1),
(1596, 'project', 11, 8, 1),
(1597, 'category', 2, 9, 1),
(1598, 'project', 12, 10, 1),
(1599, 'project', 14, 11, 1),
(1600, 'project', 17, 12, 1),
(1601, 'project', 16, 13, 1),
(1602, 'project', 21, 14, 1),
(1603, 'project', 18, 15, 1),
(1604, 'project', 19, 16, 1),
(1605, 'project', 20, 17, 1),
(1606, 'project', 15, 18, 1),
(1607, 'project', 24, 19, 1),
(1608, 'project', 28, 20, 1),
(1609, 'project', 23, 21, 1),
(1610, 'project', 27, 22, 1),
(1611, 'project', 30, 23, 1),
(1612, 'project', 31, 24, 1),
(1613, 'project', 32, 25, 1),
(1700, 'project', 37, 5, 1),
(1714, 'category', 3, 1, 1),
(1715, 'page', 82, 2, 1),
(1716, 'page', 83, 3, 1),
(1717, 'category', 4, 4, 1),
(1718, 'page', 85, 5, 1),
(1719, 'page', 86, 6, 1),
(1720, 'page', 87, 7, 1),
(1721, 'page', 88, 8, 1),
(1722, 'category', 5, 9, 1),
(1723, 'page', 90, 10, 1),
(1724, 'page', 89, 11, 1),
(1725, 'project', 38, 27, 1),
(1726, 'page', 173, 1, 1),
(1727, 'page', 174, 1, 1),
(1728, 'page', 175, 1, 1),
(1729, 'project', 39, 28, 1),
(1731, 'category', 0, 0, 1),
(1732, 'category', 6, 1, 1),
(1733, 'page', 181, 2, 1),
(1734, 'page', 77, 3, 1),
(1735, 'project', 40, 29, 1),
(1736, 'project', 41, 30, 1),
(1737, 'project', 42, 31, 1),
(1739, 'page', 187, 1, 1);

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
(2, 'Initial version', 1, '2017-11-12 19:02:11', '2017-11-15 09:40:45', 187, 1),
(3, 'Initial version', 1, '2017-11-15 08:58:22', '2017-11-15 09:40:47', 188, 1);

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
  MODIFY `cat_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `deletes`
--
ALTER TABLE `deletes`
  MODIFY `delete_ID` bigint(20) NOT NULL AUTO_INCREMENT;
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
  MODIFY `page_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=189;
--
-- AUTO_INCREMENT for table `page_cat_connect`
--
ALTER TABLE `page_cat_connect`
  MODIFY `page_cat_connect_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;
--
-- AUTO_INCREMENT for table `pins`
--
ALTER TABLE `pins`
  MODIFY `pin_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
--
-- AUTO_INCREMENT for table `project_cat_connect`
--
ALTER TABLE `project_cat_connect`
  MODIFY `project_cat_connect_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1257;
--
-- AUTO_INCREMENT for table `queues`
--
ALTER TABLE `queues`
  MODIFY `queue_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;
--
-- AUTO_INCREMENT for table `shares`
--
ALTER TABLE `shares`
  MODIFY `share_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;
--
-- AUTO_INCREMENT for table `sorting`
--
ALTER TABLE `sorting`
  MODIFY `sort_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1740;
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
  MODIFY `version_ID` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
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
