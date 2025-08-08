-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 24, 2025 at 12:41 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cims`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

DROP TABLE IF EXISTS `appointment`;
CREATE TABLE IF NOT EXISTS `appointment` (
  `aid` int NOT NULL AUTO_INCREMENT,
  `appointment_date` date NOT NULL,
  `case_id` int NOT NULL,
  `reason_id` int NOT NULL,
  `record_date` date NOT NULL,
  `is_confirmed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`aid`),
  KEY `case_id` (`case_id`),
  KEY `reason_id` (`reason_id`)
) ENGINE=InnoDB AUTO_INCREMENT=186 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`aid`, `appointment_date`, `case_id`, `reason_id`, `record_date`, `is_confirmed`) VALUES
(181, '2017-09-11', 43, 5, '2018-09-11', 0),
(183, '2017-09-12', 43, 3, '2017-09-11', 0),
(184, '2025-03-06', 46, 3, '2017-09-11', 0),
(185, '2017-09-11', 46, 5, '2017-09-11', 0);

-- --------------------------------------------------------

--
-- Table structure for table `assigned_judges`
--

DROP TABLE IF EXISTS `assigned_judges`;
CREATE TABLE IF NOT EXISTS `assigned_judges` (
  `ajd` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `case_id` int NOT NULL,
  `judge_type` varchar(30) NOT NULL,
  PRIMARY KEY (`ajd`),
  UNIQUE KEY `unique_judge_case` (`user_id`,`case_id`),
  UNIQUE KEY `user_case_unique` (`user_id`,`case_id`),
  KEY `user_id` (`user_id`),
  KEY `case_id` (`case_id`)
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `assigned_judges`
--

INSERT INTO `assigned_judges` (`ajd`, `user_id`, `case_id`, `judge_type`) VALUES
(213, 20, 43, 'primary'),
(214, 20, 46, 'primary');

-- --------------------------------------------------------

--
-- Table structure for table `attach_files`
--

DROP TABLE IF EXISTS `attach_files`;
CREATE TABLE IF NOT EXISTS `attach_files` (
  `fid` int NOT NULL AUTO_INCREMENT,
  `case_id` int NOT NULL,
  `file` varchar(350) NOT NULL,
  `record_date` date NOT NULL,
  PRIMARY KEY (`fid`),
  KEY `file_ibfk_1` (`case_id`),
  KEY `appointment_ibfk_1` (`record_date`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attach_files`
--

INSERT INTO `attach_files` (`fid`, `case_id`, `file`, `record_date`) VALUES
(36, 43, '../assets/case_files/version 2.pdf', '2025-05-19'),
(37, 43, '../assets/case_files/version 4.pdf', '2025-05-19'),
(38, 46, '../assets/case_files/version 2.pdf', '2025-05-19');

-- --------------------------------------------------------

--
-- Table structure for table `case`
--

DROP TABLE IF EXISTS `case`;
CREATE TABLE IF NOT EXISTS `case` (
  `cid` int NOT NULL AUTO_INCREMENT,
  `case_id` varchar(200) NOT NULL,
  `plaintiff` varchar(30) NOT NULL,
  `defendant` varchar(20) NOT NULL,
  `case_type` int NOT NULL,
  `decision` varchar(20) NOT NULL,
  `case_status` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ethiopian_date` date NOT NULL,
  `distributed_date` date NOT NULL,
  PRIMARY KEY (`cid`),
  UNIQUE KEY `case_id_2` (`case_id`),
  UNIQUE KEY `case_id_3` (`case_id`),
  KEY `case_id` (`case_id`),
  KEY `case_type_ibfk_1` (`case_type`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `case`
--

INSERT INTO `case` (`cid`, `case_id`, `plaintiff`, `defendant`, `case_type`, `decision`, `case_status`, `created_at`, `ethiopian_date`, `distributed_date`) VALUES
(43, 'YWC/CIV/000001/17', 'Mubark ', 'Hamid', 1, 'Modified', '8', '2025-05-19 11:03:14', '2017-09-11', '2017-09-11'),
(44, 'YWC/CIV/000002/17', 'Befikr dereje', 'Abenezer seyoum', 1, '', '0', '2025-05-19 11:03:42', '2017-09-11', '0000-00-00'),
(45, 'YWC/FAM/000001/17', 'Miki mamo', 'Abinet geremew', 4, '', '1', '2025-05-19 11:04:12', '2017-09-11', '0000-00-00'),
(46, 'YWC/FAM/000002/17', 'Kabods', 'Abinet', 4, '', '5', '2025-05-19 15:14:53', '2017-09-11', '2017-09-11');

-- --------------------------------------------------------

--
-- Table structure for table `case_info`
--

DROP TABLE IF EXISTS `case_info`;
CREATE TABLE IF NOT EXISTS `case_info` (
  `kid` int NOT NULL AUTO_INCREMENT,
  `case_id` int NOT NULL,
  `first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `father_name` varchar(50) NOT NULL,
  `grandfather_name` varchar(50) NOT NULL,
  `gender` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `region` varchar(20) NOT NULL,
  `zone` varchar(30) NOT NULL,
  `woreda` varchar(30) NOT NULL,
  `kebele` varchar(20) NOT NULL,
  `wogen` varchar(20) NOT NULL,
  `argument_money` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `judgement_money` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `litigant_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `file_pages` int NOT NULL,
  `file` varchar(350) NOT NULL,
  PRIMARY KEY (`kid`),
  KEY `case_id` (`case_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `case_info`
--

INSERT INTO `case_info` (`kid`, `case_id`, `first_name`, `father_name`, `grandfather_name`, `gender`, `email`, `region`, `zone`, `woreda`, `kebele`, `wogen`, `argument_money`, `judgement_money`, `litigant_type`, `phone`, `file_pages`, `file`) VALUES
(43, 43, 'Mubarek', '', '', '', 'Abseyoum16@gmail.com', '7', '39', '258', 'Kebele 04', 'Individual', '50', '50', 'Defendant', '+251909299378', 7, ''),
(44, 43, 'Hamid', '', '', '', 'Abseyoum1621@gmail.com', '3', '25', '182', 'Kebele 01', 'Individual', '12', '16', 'Plaintiff', '+251909299398', 7, '../assets/case_files/version 1.pdf'),
(45, 44, 'Abenezer ', '', '', '', 'Abeniseyoum16@gmail.com', '1', '2', '15', 'Kebele 04', 'Organization', '12', '16', 'Defendant', '+251909299378', 0, ''),
(46, 45, 'Abenezer ', '', '', '', 'Abeniseyoum16@gmail.com', '12', '88', '588', 'Kebele 04', 'Individual', '50', '100', 'Plaintiff', '+251909299378', 5, '../assets/case_files/version 1.pdf'),
(47, 45, 'Shumye', '', '', '', 'Abseyoum16@gmail.com', '12', '90', '594', '01', 'Individual', '12', '100', 'Defendant', '+251909299398', 0, ''),
(48, 45, 'Abenezer ', '', '', '', 'Abseyoum16@gmail.com', '6', '36', '249', 'Kebele 04', 'Individual', '50', '122', 'Defendant', '+251909299378', 0, ''),
(49, 45, 'Shumye', 'Abebe', 'Arficho', 'M', 'Abseyoum16@gmail.com', '6', '35', '244', 'Kebele 01', 'Organization', '50', '16', 'Defendant', '+251909299398', 0, ''),
(50, 46, 'Shumye', 'Kebede', 'Aster', 'M', 'Abeniseyoum16@gmail.com', '1', '2', '16', 'Kebele 01', 'Individual', '50', '16', 'Defendant', '+251909299398', 4, ''),
(51, 46, 'Aster', 'Seyoum', 'Tefera', 'M', 'Abseyoum16@gmail.com', '8', '50', '380', 'Kebele 04', 'Individual', '50', '16', 'Plaintiff', '+251909299378', 4, '../assets/case_files/version 1.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `case_type`
--

DROP TABLE IF EXISTS `case_type`;
CREATE TABLE IF NOT EXISTS `case_type` (
  `ctid` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `abbreviation_name` varchar(30) NOT NULL,
  PRIMARY KEY (`ctid`),
  UNIQUE KEY `abbreviation_name` (`abbreviation_name`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `case_type`
--

INSERT INTO `case_type` (`ctid`, `name`, `abbreviation_name`) VALUES
(1, 'Crime Case', 'CIV'),
(2, 'Civil Case', 'CRM'),
(3, 'Labour Case', 'LAB'),
(4, 'Family Case', 'FAM');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `fid` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `subject` varchar(50) NOT NULL,
  `message` varchar(50) NOT NULL,
  PRIMARY KEY (`fid`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`fid`, `full_name`, `email`, `subject`, `message`) VALUES
(25, 'Scsd', 'Abseyoum16@gmail.com', 'Ds', 'Asdcfsda'),
(26, 'Scsd', 'Abseyoum16@gmail.com', 'Ds', 'Asdcfsda'),
(27, 'Scsd', 'Abseyoum16@gmail.com', 'Ds', 'Asdcfsda'),
(28, 'Scsd', 'Abseyoum16@gmail.com', 'Ds', 'Asdcfsda'),
(29, 'Scsd', 'Abseyoum16@gmail.com', 'Ds', 'Asdcfsda');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=302 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(265, 19, 'your account detail updated.', 0, '2025-05-19 10:10:39'),
(251, 7, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 07:19:02'),
(252, 9, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 07:19:02'),
(253, 6, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 08:19:07'),
(254, 7, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 08:19:07'),
(255, 9, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 08:19:07'),
(256, 6, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 08:29:46'),
(257, 7, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 08:29:46'),
(258, 9, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 08:29:46'),
(259, 6, 'An appointment has been scheduled for Case ID: YWC/CV/000001/17.', 0, '2025-05-19 08:36:24'),
(260, 6, 'An appointment has been scheduled for Case ID: YWC/CV/000001/17.', 0, '2025-05-19 08:37:01'),
(249, 9, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 07:15:26'),
(250, 6, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 07:19:02'),
(248, 7, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 07:15:26'),
(266, 20, 'your account detail updated.', 0, '2025-05-19 10:18:51'),
(267, 21, 'your account detail updated.', 0, '2025-05-19 10:20:32'),
(37, 10, 'A new case (ID: YWC/AV/000002/17) has been transferred to you.', 0, '2025-05-13 12:52:23'),
(264, 6, 'your account detail updated.', 0, '2025-05-19 09:45:45'),
(263, 18, 'your account detail updated.', 0, '2025-05-19 09:37:09'),
(244, 6, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 07:07:22'),
(247, 6, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 07:15:26'),
(246, 9, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 07:07:22'),
(245, 7, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 07:07:22'),
(243, 18, 'A decision inserted successfully  for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 21:34:51'),
(227, 12, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 20:41:20'),
(228, 15, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 20:41:20'),
(242, 15, 'A decision inserted successfully  for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 21:34:51'),
(230, 12, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 20:41:42'),
(231, 15, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 20:41:42'),
(241, 12, 'A decision inserted successfully  for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 21:34:51'),
(224, 12, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 19:59:39'),
(225, 15, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 19:59:39'),
(239, 7, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-18 21:30:27'),
(221, 12, 'Case ID:  has been confirmed by the judge.', 0, '2025-05-18 19:51:35'),
(222, 15, 'Case ID:  has been confirmed by the judge.', 0, '2025-05-18 19:51:35'),
(240, 9, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-18 21:30:27'),
(238, 6, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-18 21:30:27'),
(219, 15, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 19:50:45'),
(213, 15, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 19:44:22'),
(218, 12, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 19:50:45'),
(210, 15, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 19:44:03'),
(237, 9, 'New case assigned to you: Case ID - YWC/LAB/000002/17.', 0, '2025-05-18 21:27:56'),
(207, 15, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 19:30:14'),
(183, 3, 'your account detail updated.', 1, '2025-05-18 11:38:12'),
(184, 4, 'your account detail updated.', 0, '2025-05-18 11:42:42'),
(262, 6, 'your account detail updated.', 0, '2025-05-19 09:35:22'),
(180, 9, 'A new file has been updated for Case ID: 1.', 0, '2025-05-18 10:29:48'),
(181, 3, 'your account detail updated.', 1, '2025-05-18 11:30:44'),
(236, 7, 'New case assigned to you: Case ID - YWC/LAB/000002/17.', 0, '2025-05-18 21:27:56'),
(178, 7, 'An appointment has been scheduled for Case ID: YWC/TM/000001/17.', 0, '2025-05-18 03:08:08'),
(177, 5, 'An appointment has been scheduled for Case ID: YWC/TM/000001/17.', 1, '2025-05-18 03:08:08'),
(143, 3, 'your account detail updated.', 1, '2025-05-16 21:09:52'),
(261, 6, 'A decision inserted successfully  for Case ID: YWC/CV/000001/17.', 0, '2025-05-19 08:40:10'),
(216, 15, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 19:50:20'),
(204, 15, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 19:19:12'),
(172, 4, 'Case ID: YWC/LAB/000001/17 is now ready for distribution.', 0, '2025-05-18 01:27:22'),
(173, 4, 'Case ID: YWC/LAB/000001/17 is now ready for distribution.', 0, '2025-05-18 01:31:03'),
(171, 4, 'Case ID: YWC/LAB/000001/17 is now ready for distribution.', 0, '2025-05-18 01:26:20'),
(161, 7, 'A decision inserted successfully  for Case ID: 31.', 1, '2025-05-16 21:47:54'),
(163, 9, 'A new file has been updated for Case ID: 1.', 0, '2025-05-16 21:55:48'),
(164, 9, 'An update has been sucessfully for Case ID: YWC/CV/000001/17.', 0, '2025-05-16 21:56:23'),
(201, 15, 'your account detail updated.', 0, '2025-05-18 19:17:12'),
(158, 7, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 1, '2025-05-16 21:47:17'),
(136, 11, 'An update has been sucessfully for Case ID: YWC/CV/000001/17.', 0, '2025-05-15 08:04:06'),
(185, 3, 'your account detail updated.', 1, '2025-05-18 11:51:35'),
(182, 3, 'your account detail updated.', 1, '2025-05-18 11:33:29'),
(155, 7, 'A new file has been attached and file pages updated for Case ID: 31.', 1, '2025-05-16 21:45:59'),
(235, 6, 'New case assigned to you: Case ID - YWC/LAB/000002/17.', 0, '2025-05-18 21:27:56'),
(234, 5, 'Case ID: YWC/LAB/000002/17 is now ready for distribution.', 0, '2025-05-18 21:25:33'),
(169, 4, 'Case ID: YWC/LAB/000001/17 is now ready for distribution.', 0, '2025-05-18 00:56:06'),
(170, 4, 'Case ID: YWC/LAB/000001/17 is now ready for distribution.', 0, '2025-05-18 01:00:43'),
(233, 4, 'Case ID: YWC/LAB/000002/17 is now ready for distribution.', 0, '2025-05-18 21:25:33'),
(199, 15, 'New case assigned to you: Case ID - YWC/FAM/000002/17.', 0, '2025-05-18 19:13:30'),
(215, 12, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-18 19:50:20'),
(168, 4, 'Case ID: YWC/LAB/000001/17 is now ready for distribution.', 0, '2025-05-18 00:53:25'),
(152, 7, 'Case ID:  has been confirmed by the judge.', 1, '2025-05-16 21:42:40'),
(175, 7, 'An appointment has been scheduled for Case ID: YWC/TM/000001/17.', 0, '2025-05-18 03:07:11'),
(188, 0, 'new case assign to you Case ID: 1.', 0, '2025-05-18 12:16:40'),
(133, 11, 'An update has been sucessfully for Case ID: YWC/CV/000001/17.', 0, '2025-05-14 21:27:54'),
(167, 7, 'An appointment has been scheduled for Case ID: YWC/TM/000001/17.', 0, '2025-05-17 11:02:56'),
(174, 5, 'An appointment has been scheduled for Case ID: YWC/TM/000001/17.', 1, '2025-05-18 03:07:11'),
(268, 22, 'your account detail updated.', 0, '2025-05-19 10:25:27'),
(269, 23, 'your account detail updated.', 1, '2025-05-19 10:39:01'),
(270, 20, 'New case assigned to you: Case ID - YWC/CRM/000001/17.', 0, '2025-05-19 10:40:14'),
(271, 23, 'Case ID: YWC/CIV/000001/17 is now ready for distribution.', 0, '2025-05-19 10:52:23'),
(272, 23, 'Case ID: YWC/CIV/000001/17 is now ready for distribution.', 0, '2025-05-19 10:53:46'),
(273, 23, 'Case ID: YWC/LAB/000002/17 is now ready for distribution.', 0, '2025-05-19 10:58:28'),
(274, 21, 'New case assigned to you: Case ID - YWC/LAB/000002/17.', 0, '2025-05-19 11:01:02'),
(275, 21, 'A decision inserted successfully  for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 11:06:45'),
(276, 21, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 11:14:32'),
(277, 21, 'A new file has been attached and file pages updated for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 11:15:55'),
(278, 21, 'A new file has been attached and file pages updated for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 11:16:01'),
(279, 21, 'An appointment has been scheduled for Case ID: YWC/LAB/000002/17.', 0, '2025-05-19 11:18:55'),
(280, 20, 'An update has been sucessfully for Case ID: YWC/CRM/000001/17.', 0, '2025-05-19 13:55:05'),
(281, 20, 'An update has been sucessfully for Case ID: YWC/CRM/000001/17.', 0, '2025-05-19 13:55:18'),
(282, 20, 'An update has been sucessfully for Case ID: YWC/CRM/000001/17.', 0, '2025-05-19 13:55:26'),
(283, 20, 'An update has been sucessfully for Case ID: YWC/CRM/000001/17.', 0, '2025-05-19 13:57:18'),
(284, 20, 'An update has been sucessfully for Case ID: YWC/CRM/000001/17.', 0, '2025-05-19 13:57:50'),
(285, 23, 'Case ID: YWC/CIV/000001/17 is now ready for distribution.', 0, '2025-05-19 14:06:53'),
(286, 20, 'New case assigned to you: Case ID - YWC/CIV/000001/17.', 0, '2025-05-19 14:45:07'),
(287, 20, 'An appointment has been scheduled for Case ID: YWC/CIV/000001/17.', 0, '2025-05-19 14:49:20'),
(288, 20, 'A new file has been attached and file pages updated for Case ID: YWC/CIV/000001/17.', 0, '2025-05-19 14:49:42'),
(289, 20, 'A decision inserted successfully  for Case ID: YWC/CIV/000001/17.', 0, '2025-05-19 15:02:50'),
(290, 20, 'A decision inserted successfully  for Case ID: YWC/CIV/000001/17.', 0, '2025-05-19 15:04:39'),
(291, 20, 'A decision inserted successfully  for Case ID: YWC/CIV/000001/17.', 0, '2025-05-19 15:06:23'),
(292, 23, 'Case ID: YWC/FAM/000001/17 is now ready for distribution.', 0, '2025-05-19 15:14:47'),
(293, 23, 'Case ID: YWC/FAM/000001/17 is now ready for distribution.', 0, '2025-05-19 15:18:00'),
(294, 23, 'Case ID: YWC/FAM/000001/17 is now ready for distribution.', 0, '2025-05-19 15:20:47'),
(295, 20, 'An appointment has been scheduled for Case ID: YWC/CIV/000001/17.', 0, '2025-05-19 15:45:45'),
(296, 20, 'An appointment has been scheduled for Case ID: YWC/CIV/000001/17.', 0, '2025-05-19 16:04:41'),
(297, 20, 'A new file has been attached and file pages updated for Case ID: YWC/CIV/000001/17.', 0, '2025-05-19 16:49:34'),
(298, 23, 'Case ID: YWC/FAM/000002/17 is now ready for distribution.', 0, '2025-05-19 18:17:36'),
(299, 20, 'New case assigned to you: Case ID - YWC/FAM/000002/17.', 0, '2025-05-19 18:18:46'),
(300, 20, 'A new file has been attached and file pages updated for Case ID: YWC/FAM/000002/17.', 0, '2025-05-19 18:25:05'),
(301, 20, 'An appointment has been scheduled for Case ID: YWC/FAM/000002/17.', 0, '2025-05-19 18:26:22');

-- --------------------------------------------------------

--
-- Table structure for table `reason`
--

DROP TABLE IF EXISTS `reason`;
CREATE TABLE IF NOT EXISTS `reason` (
  `rid` int NOT NULL AUTO_INCREMENT,
  `appointment_reason` varchar(50) NOT NULL,
  PRIMARY KEY (`rid`),
  KEY `appointment_reason` (`appointment_reason`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reason`
--

INSERT INTO `reason` (`rid`, `appointment_reason`) VALUES
(1, ' To receive a response'),
(4, 'To give an order'),
(3, 'To hear a complaint'),
(2, 'To investigate'),
(5, 'To make a decision');

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

DROP TABLE IF EXISTS `regions`;
CREATE TABLE IF NOT EXISTS `regions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `name`) VALUES
(1, 'Addis Ababa'),
(2, 'Afar'),
(3, 'Amhara'),
(4, 'Benishangul-Gumuz'),
(14, 'Central Ethiopia Region'),
(5, 'Dire Dawa'),
(6, 'Gambela'),
(7, 'Harari'),
(8, 'Oromia'),
(9, 'Sidama'),
(11, 'Somali'),
(12, 'South Ethiopian Region'),
(10, 'South West Region'),
(13, 'Tigray');

-- --------------------------------------------------------

--
-- Table structure for table `resolutions`
--

DROP TABLE IF EXISTS `resolutions`;
CREATE TABLE IF NOT EXISTS `resolutions` (
  `resolution_id` int NOT NULL AUTO_INCREMENT,
  `case_id` int NOT NULL,
  `who_won` varchar(50) DEFAULT NULL,
  `decision_details` text,
  `resolution_date` date DEFAULT NULL,
  PRIMARY KEY (`resolution_id`),
  KEY `resolutions_ibfk_1` (`case_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `resolutions`
--

INSERT INTO `resolutions` (`resolution_id`, `case_id`, `who_won`, `decision_details`, `resolution_date`) VALUES
(11, 43, 'Plantiff', 'Modified', '2017-09-11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int NOT NULL AUTO_INCREMENT,
  `idNumber` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `profile_pic` varchar(250) NOT NULL,
  `first_name` varchar(20) NOT NULL,
  `father_name` varchar(50) NOT NULL,
  `gfather_name` varchar(50) NOT NULL,
  `gender` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(45) NOT NULL,
  `user_type` varchar(20) NOT NULL,
  `user_status` int NOT NULL,
  `is_logged` int NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `idNumber` (`idNumber`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `idNumber`, `profile_pic`, `first_name`, `father_name`, `gfather_name`, `gender`, `email`, `username`, `password`, `phone`, `user_type`, `user_status`, `is_logged`) VALUES
(18, 'YWCIM/0015/17', '../assets/img/profile.jpg', 'Shumye', 'Seyoum', 'Tefera', 'M', 'Abseyoum16@gmail.com', 'hamid', '3yFM88FVzw+XU0J2lPnUpA==', '+251909299398', 'Admin', 0, 0),
(19, 'YWCIM/0016/17', '../assets/img/hero_1.jpg', 'Mubarek', 'Sabri', 'Mohamed', 'M', 'Abeniseyoum16@gmail.', 'Muba', 'ejVUaY3UR38ce2v3KDhH7Q==', '+251909299378', 'Law_officer', 0, 0),
(20, 'YWCIM/0017/17', '../assets/img/image.png', 'Abinet', 'Geremew', 'Ata', 'M', 'Abinetgeremew6@gmail', 'ABI', 'v9Kqvv9lwaCylujlSnqRfg==', '+251909299398', 'Judge', 0, 0),
(21, 'YWCIM/0018/17', '../assets/img/img_3.jpg', 'Ebenezer ', 'Seyoum', 'Tefera', 'M', 'Ebenezer16@gmail.com', 'Ebi', 'xvMDOKF4lfolixvdIrKz3g==', '+251909299378', 'Judge', 0, 0),
(22, 'YWCIM/0019/17', '../assets/img/team-2.jpg', 'Abreham', 'Amanuel', 'Ssss', 'M', 'Abresh16@gmail.com', 'Abr', 'OMOifO5cOLX/POQXjDZCiA==', '+251909299378', 'Judge', 0, 0),
(23, 'YWCIM/0020/17', '../assets/img/profile.jpg', 'Mihret', 'Fikru', 'Africh', 'F', 'Hamid16@gmail.com', 'Mercy', 'hefhRtC6hg/Z1P9CukIEDA==', '+251909299398', 'Case_distributer', 0, 0),
(25, 'YWCIM/0021/17', '../assets/img/wallpaper.jpg', 'Shumye', 'Abebe', 'Tefera', 'F', 'Abseyoum16@gmail.com', 'Ac', 'c4n2TP1QDVnPT4XCu9WR7e5OVV6idjl1da3YBXDmDro=', '+251909299378', 'Judge', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `woredas`
--

DROP TABLE IF EXISTS `woredas`;
CREATE TABLE IF NOT EXISTS `woredas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `zone_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zone_ibk_1` (`zone_id`)
) ENGINE=InnoDB AUTO_INCREMENT=705 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `woredas`
--

INSERT INTO `woredas` (`id`, `name`, `zone_id`) VALUES
(3, 'Woreda 01', 1),
(4, 'Woreda 02', 1),
(5, 'Woreda 03', 1),
(6, 'Woreda 04', 1),
(7, 'Woreda 05', 1),
(8, 'Woreda 06', 1),
(9, 'Woreda 07', 1),
(10, 'Woreda 08', 1),
(11, 'Woreda 09', 1),
(12, 'Woreda 10', 1),
(13, 'Woreda 01', 2),
(14, 'Woreda 02', 2),
(15, 'Woreda 03', 2),
(16, 'Woreda 04', 2),
(17, 'Woreda 05', 2),
(18, 'Woreda 06', 2),
(19, 'Woreda 07', 2),
(20, 'Woreda 01', 3),
(21, 'Woreda 02', 3),
(22, 'Woreda 03', 3),
(23, 'Woreda 04', 3),
(24, 'Woreda 05', 3),
(25, 'Woreda 06', 3),
(26, 'Woreda 01', 4),
(27, 'Woreda 02', 4),
(28, 'Woreda 03', 4),
(29, 'Woreda 04', 4),
(30, 'Woreda 05', 4),
(31, 'Woreda 06', 4),
(32, 'Woreda 07', 4),
(33, 'Woreda 08', 4),
(34, 'Woreda 01', 5),
(35, 'Woreda 02', 5),
(36, 'Woreda 03', 5),
(37, 'Woreda 04', 5),
(38, 'Woreda 05', 5),
(39, 'Woreda 06', 5),
(40, 'Woreda 07', 5),
(41, 'Woreda 08', 5),
(42, 'Woreda 09', 5),
(43, 'Woreda 01', 6),
(44, 'Woreda 02', 6),
(45, 'Woreda 03', 6),
(46, 'Woreda 04', 6),
(47, 'Woreda 05', 6),
(48, 'Woreda 06', 6),
(49, 'Woreda 01', 7),
(50, 'Woreda 02', 7),
(51, 'Woreda 03', 7),
(52, 'Woreda 04', 7),
(53, 'Woreda 05', 7),
(54, 'Woreda 06', 7),
(55, 'Woreda 07', 7),
(56, 'Woreda 08', 7),
(57, 'Woreda 09', 7),
(58, 'Woreda 10', 7),
(59, 'Woreda 01', 8),
(60, 'Woreda 02', 8),
(61, 'Woreda 03', 8),
(62, 'Woreda 04', 8),
(63, 'Woreda 05', 8),
(64, 'Woreda 06', 8),
(65, 'Woreda 07', 8),
(66, 'Woreda 08', 8),
(67, 'Woreda 09', 8),
(68, 'Woreda 10', 8),
(69, 'Woreda 01', 9),
(70, 'Woreda 02', 9),
(71, 'Woreda 03', 9),
(72, 'Woreda 04', 9),
(73, 'Woreda 05', 9),
(74, 'Woreda 06', 9),
(75, 'Woreda 07', 9),
(76, 'Woreda 08', 9),
(77, 'Woreda 09', 9),
(78, 'Woreda 01', 10),
(79, 'Woreda 02', 10),
(80, 'Woreda 03', 10),
(81, 'Woreda 04', 10),
(82, 'Woreda 05', 10),
(83, 'Woreda 06', 10),
(84, 'Woreda 07', 10),
(85, 'Woreda 08', 10),
(86, 'Woreda 09', 10),
(87, 'Woreda 10', 10),
(88, 'Woreda 01', 11),
(89, 'Woreda 02', 11),
(90, 'Woreda 03', 11),
(91, 'Woreda 04', 11),
(92, 'Woreda 05', 11),
(93, 'Woreda 06', 11),
(94, 'Asayita', 12),
(95, 'Afambo', 12),
(96, 'Dubti', 12),
(97, 'Mille', 12),
(98, 'Chifra', 12),
(99, 'Gewane', 12),
(100, 'Awra', 13),
(101, 'Gulina', 13),
(102, 'Yalo', 13),
(103, 'Ewa', 13),
(104, 'Telalak', 14),
(105, 'Dalifage', 14),
(106, 'Hadaleala', 14),
(107, 'Dewe', 14),
(108, 'Artuma Fursi', 14),
(109, 'Amibara', 15),
(110, 'Argoba Special Woreda', 15),
(111, 'Bure Mudaytu', 15),
(112, 'Abala', 16),
(113, 'Berahle', 16),
(114, 'Megale', 16),
(115, 'Dallol', 16),
(116, 'Koneba', 16),
(117, 'Ankasha Guagusa', 17),
(118, 'Banja Shekudad', 17),
(119, 'Faggeta Lekoma', 17),
(120, 'Guangua', 17),
(121, 'Jawi', 17),
(122, 'Dangila Town', 17),
(123, 'Injibara Town', 17),
(124, 'Zigem', 17),
(125, 'Bahir Dar City', 18),
(126, 'Dembia', 19),
(127, 'Gondar Zuria', 19),
(128, 'Lay Armachiho', 19),
(129, 'Chilga', 19),
(130, 'Metemma', 19),
(131, 'Tach Armachiho', 19),
(132, 'Wegera', 19),
(133, 'Debark Town', 19),
(134, 'Gondar Town', 19),
(135, 'Bibugn', 20),
(136, 'Debre Markos Town', 20),
(137, 'Dejen', 20),
(138, 'Enemay', 20),
(139, 'Enarj Enawga', 20),
(140, 'Goncha Siso Enese', 20),
(141, 'Hulet Ej Enese', 20),
(142, 'Machakel', 20),
(143, 'Shebel Berenta', 20),
(144, 'Sinan', 20),
(145, 'Basoliben', 20),
(146, 'Dabat', 21),
(147, 'Debark', 21),
(148, 'Janamora', 21),
(149, 'Telemt', 21),
(150, 'Beyeda', 21),
(151, 'Adi Arkay', 21),
(152, 'Angolalla Tera', 22),
(153, 'Ankober', 22),
(154, 'Antsokiyana Gemza', 22),
(155, 'Basona Werana', 22),
(156, 'Debre Berhan Town', 22),
(157, 'Efratana Gidim', 22),
(158, 'Hagere Mariamna Kesem', 22),
(159, 'Kewet', 22),
(160, 'Menze Gera Midir', 22),
(161, 'Menze Keya Gebreal', 22),
(162, 'Moretna Jiru', 22),
(163, 'Siyadebrina Wayu', 22),
(164, 'Guba Lafto', 23),
(165, 'Habru', 23),
(166, 'Meket', 23),
(167, 'Weldiya Town', 23),
(168, 'Bugna', 23),
(169, 'Kobo', 23),
(170, 'Lasta', 23),
(171, 'Raya Kobo', 23),
(172, 'Debre Tabor Town', 24),
(173, 'Ebenat', 24),
(174, 'Farta', 24),
(175, 'Fogera', 24),
(176, 'Libo Kemekem', 24),
(177, 'Simada', 24),
(178, 'Tach Gayint', 24),
(179, 'Lay Gayint', 24),
(180, 'Dera', 24),
(181, 'Este', 24),
(182, 'Ambasel', 25),
(183, 'Dessie Town', 25),
(184, 'Dessie Zuria', 25),
(185, 'Kalu', 25),
(186, 'Kombolcha Town', 25),
(187, 'Kutaber', 25),
(188, 'Legambo', 25),
(189, 'Legahida', 25),
(190, 'Tenta', 25),
(191, 'Worebabo', 25),
(192, 'Were Ilu', 25),
(193, 'Tehuledere', 25),
(194, 'Sekota', 26),
(195, 'Dehana', 26),
(196, 'Zikuala', 26),
(197, 'Sahle Selassie', 26),
(198, 'Abergele', 26),
(199, 'Bahir Dar Zuria', 27),
(200, 'Bure', 27),
(201, 'Debre Markos Zuria', 27),
(202, 'Dembecha', 27),
(203, 'Finote Selam Town', 27),
(204, 'Jabi Tehnan', 27),
(205, 'Mecha', 27),
(206, 'Sekela', 27),
(207, 'South Achefer', 27),
(208, 'North Achefer', 27),
(209, 'Yilmana Densa', 27),
(210, 'Gog', 28),
(211, 'Guba', 28),
(212, 'Mandura', 28),
(213, 'Metekel', 28),
(214, 'Pawe Special', 28),
(215, 'Bati', 29),
(216, 'Jilee Dhummuugaa', 29),
(217, 'Artuma Fursi', 29),
(218, 'Dewa Chefa', 29),
(219, 'Kombolcha Town', 29),
(220, 'Asosa Town', 30),
(221, 'Asosa', 30),
(222, 'Bambasi', 30),
(223, 'Homosha', 30),
(224, 'Kurmuk', 30),
(225, 'Oda Buldigilu', 30),
(226, 'Sherkole', 30),
(227, 'Agalo Meti', 31),
(228, 'Belo Jiganfoy', 31),
(229, 'Kamashi', 31),
(230, 'Menge', 31),
(231, 'Yaso', 31),
(232, 'Mao Komo Special Woreda', 32),
(233, 'Dangur', 33),
(234, 'Debati', 33),
(235, 'Guba', 33),
(236, 'Mandura', 33),
(237, 'Pawe Special Woreda', 33),
(238, 'Bulen', 33),
(239, 'Wombera', 33),
(240, 'Dire Dawa City', 34),
(241, 'Gurgura', 34),
(242, 'Wahil', 34),
(243, 'Gambella Zuria', 35),
(244, 'Abobo', 35),
(245, 'Gog', 35),
(246, 'Jor', 35),
(247, 'Dimma', 35),
(248, 'Godere', 36),
(249, 'Mengesh', 36),
(250, 'Gambella Town', 37),
(251, 'Akobo', 38),
(252, 'Jikawo', 38),
(253, 'Lare', 38),
(254, 'Makuey', 38),
(255, 'Itang Special Woreda', 38),
(256, 'Amir Nur', 39),
(257, 'Abadir', 39),
(258, 'Aboker', 39),
(259, 'Hakim', 39),
(260, 'Shenkor', 39),
(261, 'Jugal', 39),
(262, 'Sofi', 39),
(263, 'Erer', 39),
(264, 'Dire Teyara', 39),
(265, 'Asella Town', 40),
(266, 'Dodota', 40),
(267, 'Dera', 40),
(268, 'Hetosa', 40),
(269, 'Lude Hitosa', 40),
(270, 'Tiyo', 40),
(271, 'Digelu Tijo', 40),
(272, 'Limuna Bilbilo', 40),
(273, 'Sude', 40),
(274, 'Merti', 40),
(275, 'Seru', 40),
(276, 'Robe', 40),
(277, 'Sire', 40),
(278, 'Shirka', 40),
(279, 'Jeju', 40),
(280, 'Munessa', 40),
(281, 'Bokoji', 40),
(282, 'Goba Town', 41),
(283, 'Robe Town', 41),
(284, 'Agarfa', 41),
(285, 'Berbere', 41),
(286, 'Delo Menna', 41),
(287, 'Dinsho', 41),
(288, 'Gassera', 41),
(289, 'Goba', 41),
(290, 'Goro', 41),
(291, 'Harena Buluk', 41),
(292, 'Legehida', 41),
(293, 'Meda Welabu', 41),
(294, 'Rayitu', 41),
(295, 'Sinana', 41),
(296, 'Yabelo Town', 42),
(297, 'Arero', 42),
(298, 'Dire', 42),
(299, 'Dubluk', 42),
(300, 'Gomole', 42),
(301, 'Miyo', 42),
(302, 'Moyale', 42),
(303, 'Teltelle', 42),
(304, 'Yabelo', 42),
(305, 'Bedele Town', 43),
(306, 'Bedele', 43),
(307, 'Chewaka', 43),
(308, 'Dabo Hana', 43),
(309, 'Didessa', 43),
(310, 'Gechi', 43),
(311, 'Babille', 44),
(312, 'Bedeno', 44),
(313, 'Chinaksen', 44),
(314, 'Deder', 44),
(315, 'Fedis', 44),
(316, 'Girawa', 44),
(317, 'Gola Oda', 44),
(318, 'Goro Gutu', 44),
(319, 'Haro Maya', 44),
(320, 'Jarso', 44),
(321, 'Kersa', 44),
(322, 'Kombolcha', 44),
(323, 'Kurfa Chele', 44),
(324, 'Meta', 44),
(325, 'Melka Belo', 44),
(326, 'Adama Town', 45),
(327, 'Batu (Zeway)', 45),
(328, 'Boset', 45),
(329, 'Dugda', 45),
(330, 'Fentale', 45),
(331, 'Lome', 45),
(332, 'Adami Tulu Jido Kombolcha', 45),
(333, 'Diga', 46),
(334, 'Guto Wayu', 46),
(335, 'Gida Ayana', 46),
(336, 'Leka Dulecha', 46),
(337, 'Sibu Sire', 46),
(338, 'Sasiga', 46),
(339, 'Wayu Tuka', 46),
(340, 'Nekemte Town', 46),
(341, 'Adola', 47),
(342, 'Ana Sora', 47),
(343, 'Bore', 47),
(344, 'Dama', 47),
(345, 'Dugda Dawa', 47),
(346, 'Gasera', 47),
(347, 'Goro Dola', 47),
(348, 'Hambela Wamena', 47),
(349, 'Liben', 47),
(350, 'Odo Shakiso', 47),
(351, 'Abay Chomen', 48),
(352, 'Amuru', 48),
(353, 'Abe Dongoro', 48),
(354, 'Begi', 48),
(355, 'Guduru', 48),
(356, 'Hababo Guduru', 48),
(357, 'Jardega Jarte', 48),
(358, 'Jimma Rare', 48),
(359, 'Shambu Town', 48),
(360, 'Alge Sachi', 49),
(361, 'Ale', 49),
(362, 'Bicho', 49),
(363, 'Bure', 49),
(364, 'Darimu', 49),
(365, 'Didu', 49),
(366, 'Gambella Zuria', 49),
(367, 'Hurumu', 49),
(368, 'Metu Town', 49),
(369, 'Supena Sodo', 49),
(370, 'Yayo', 49),
(371, 'Agaro Town', 50),
(372, 'Dedo', 50),
(373, 'Gera', 50),
(374, 'Gomma', 50),
(375, 'Guma', 50),
(376, 'Kersa', 50),
(377, 'Limu Kosa', 50),
(378, 'Limu Seka', 50),
(379, 'Mana', 50),
(380, 'Omo Nada', 50),
(381, 'Sekoru', 50),
(382, 'Setema', 50),
(383, 'Shebe Senbo', 50),
(384, 'Sigmo', 50),
(385, 'Dale Wabera', 51),
(386, 'Hawa Gelan', 51),
(387, 'Jimma Horo', 51),
(388, 'Lalo Kile', 51),
(389, 'Mendi Town', 51),
(390, 'Sayo', 51),
(391, 'Yemalogi Welele', 51),
(392, 'Abichu Gna', 52),
(393, 'Aleltu', 52),
(394, 'Bereh', 52),
(395, 'Degem', 52),
(396, 'Debre Libanos', 52),
(397, 'Fiche Town', 52),
(398, 'Girar Jarso', 52),
(399, 'Hagere Mariam', 52),
(400, 'Kuyu', 52),
(401, 'Liban Jawi', 52),
(402, 'Mulona Sululta', 52),
(403, 'Wara Jarso', 52),
(404, 'Wuchale', 52),
(405, 'Becho', 53),
(406, 'Dawo', 53),
(407, 'Elu', 53),
(408, 'Kersa Malima', 53),
(409, 'Saden Sodo', 53),
(410, 'Tole', 53),
(411, 'Waliso Town', 53),
(412, 'Wonchi', 53),
(413, 'Adaba', 54),
(414, 'Dodola', 54),
(415, 'Gedeb Asasa', 54),
(416, 'Kofele', 54),
(417, 'Kore', 54),
(418, 'Nensebo', 54),
(419, 'Shala', 54),
(420, 'Shashamane Town', 54),
(421, 'Shashamene Rural', 54),
(422, 'Siraro', 54),
(423, 'Abaya', 55),
(424, 'Bule Hora', 55),
(425, 'Dugda Dawa', 55),
(426, 'Gelana', 55),
(427, 'Hambela Wamena', 55),
(428, 'Kercha', 55),
(429, 'Malka Soda', 55),
(430, 'Suro Berguda', 55),
(431, 'Asebe Teferi Town', 56),
(432, 'Boke', 56),
(433, 'Chiro', 56),
(434, 'Doba', 56),
(435, 'Gemachis', 56),
(436, 'Habro', 56),
(437, 'Hawi Gudina', 56),
(438, 'Meiso', 56),
(439, 'Mesela', 56),
(440, 'Ambo Town', 57),
(441, 'Bako Tibe', 57),
(442, 'Dendi', 57),
(443, 'Ejere', 57),
(444, 'Elu Gelan', 57),
(445, 'Gindeberet', 57),
(446, 'Jeldu', 57),
(447, 'Meta Robi', 57),
(448, 'Toke Kutaye', 57),
(449, 'Tikur Inchini', 57),
(450, 'Wonchi', 57),
(451, 'Ayra Guliso', 58),
(452, 'Begi', 58),
(453, 'Boji Dirmeji', 58),
(454, 'Boji Chokorsa', 58),
(455, 'Gimbi', 58),
(456, 'Guliso', 58),
(457, 'Kiltu Kara', 58),
(458, 'Lalo Asabi', 58),
(459, 'Mana Sibu', 58),
(460, 'Najo Town', 58),
(461, 'Aleta Chuko', 59),
(462, 'Aleta Wendo', 59),
(463, 'Dara', 59),
(464, 'Hula', 59),
(465, 'Loka Abaya', 59),
(466, 'Bursa', 59),
(467, 'Chere', 59),
(468, 'Hawassa City', 60),
(469, 'Boricha', 61),
(470, 'Dale', 61),
(471, 'Shebedino', 61),
(472, 'Wondo Genet', 61),
(473, 'Malga', 62),
(474, 'Hula', 62),
(475, 'Bona Zuria', 62),
(476, 'Gorche', 63),
(477, 'Aroresa', 63),
(478, 'Bensa', 63),
(479, 'Chuko', 63),
(480, 'Beshasha', 64),
(481, 'Mizan Aman Town', 64),
(482, 'Sheko', 64),
(483, 'Bench', 64),
(484, 'Dima', 64),
(485, 'Duna', 65),
(486, 'Kemba', 65),
(487, 'Loma', 65),
(488, 'Misha', 65),
(489, 'Tarcha Town', 65),
(490, 'Bita', 66),
(491, 'Decha', 66),
(492, 'Gimbo', 66),
(493, 'Gesha', 66),
(494, 'Masha', 66),
(495, 'Shebe', 66),
(496, 'Tello', 66),
(497, 'Dima', 66),
(498, 'Andracha', 67),
(499, 'Denbi Uddo', 67),
(500, 'Jore', 67),
(501, 'Sheka', 67),
(502, 'Anuak', 68),
(503, 'Bure', 68),
(504, 'Gambela', 68),
(505, 'Jikawo', 68),
(506, 'Lare', 68),
(507, 'Nuer', 68),
(508, 'Pochalla', 68),
(509, 'Kontta', 69),
(510, 'Hamer', 69),
(511, 'Dime', 69),
(512, 'Surma', 69),
(513, 'Afdem', 70),
(514, 'Babile', 70),
(515, 'Chifra', 70),
(516, 'Dembel', 70),
(517, 'Gursum', 70),
(518, 'Haromaya', 70),
(519, 'Jijiga Town', 70),
(520, 'Kebri Dehar', 70),
(521, 'Kebri Dehar Town', 70),
(522, 'Meyu Muluke', 70),
(523, 'Awbare', 71),
(524, 'Gursum', 71),
(525, 'Jijiga', 71),
(526, 'Kebri Dehar', 71),
(527, 'Mustahil', 71),
(528, 'Babile', 72),
(529, 'Chifra', 72),
(530, 'Dembel', 72),
(531, 'Gursum', 72),
(532, 'Haromaya', 72),
(533, 'Jijiga Town', 72),
(534, 'Kebri Dehar', 72),
(535, 'Afdem', 73),
(536, 'Babile', 73),
(537, 'Chifra', 73),
(538, 'Dembel', 73),
(539, 'Gursum', 73),
(540, 'Haromaya', 73),
(541, 'Awbare', 74),
(542, 'Gursum', 74),
(543, 'Jijiga', 74),
(544, 'Kebri Dehar', 74),
(545, 'Bedeno', 75),
(546, 'Dawe', 75),
(547, 'Qabri Dehar', 75),
(548, 'Dolo', 76),
(549, 'Feysel', 76),
(550, 'Korahe', 76),
(551, 'Afder', 77),
(552, 'Hargele', 77),
(553, 'Moyale', 77),
(554, 'Dolo', 78),
(555, 'Doolo', 78),
(556, 'Moyale', 78),
(557, 'Doolo', 79),
(558, 'Moyale', 79),
(559, 'Ale', 80),
(560, 'Bena', 80),
(561, 'Bena-Tsemay', 80),
(562, 'Ari', 81),
(563, 'Dimmu', 81),
(564, 'Mela', 81),
(565, 'Sheka', 81),
(566, 'Basketo', 82),
(567, 'Gamu', 82),
(568, 'Kucha', 82),
(569, 'Burji', 83),
(570, 'Gergera', 83),
(571, 'Guma', 83),
(572, 'Gumo', 83),
(573, 'Gamo', 84),
(574, 'Gofa', 84),
(575, 'Kucha', 84),
(576, 'Sawla', 84),
(577, 'Yabelo', 84),
(578, 'Gardulla', 85),
(579, 'Bena', 85),
(580, 'Tsemay', 85),
(581, 'Dilla', 86),
(582, 'Bule Hora', 86),
(583, 'Yirgacheffe', 86),
(584, 'Kebel', 86),
(585, 'Gofa', 87),
(586, 'Kucha', 87),
(587, 'Sawla', 87),
(588, 'Konso', 88),
(589, 'Derashe', 88),
(590, 'Amaro', 88),
(591, 'Koore', 89),
(592, 'Dillo', 89),
(593, 'Sawla', 89),
(594, 'Jinka', 90),
(595, 'Maji', 90),
(596, 'Omo', 90),
(597, 'Wolayita', 91),
(598, 'Sodo', 91),
(599, 'Kucha', 91),
(600, 'Damot', 91),
(601, 'Hossana', 91),
(602, 'Atsbi', 92),
(603, 'Enda-Mohoni', 92),
(604, 'Kelela', 92),
(605, 'Adigrat', 92),
(606, 'Mekelle', 93),
(607, 'Alamata', 93),
(608, 'Degua Tembien', 93),
(609, 'Irob', 93),
(610, 'Mekelle Town', 94),
(611, 'Kilte Awlaelo', 94),
(612, 'Raya Azebo', 94),
(613, 'Tselemt', 95),
(614, 'Zana', 95),
(615, 'Samre', 95),
(616, 'Shire', 95),
(617, 'Hawzen', 96),
(618, 'Wukro', 96),
(619, 'Tigray', 96),
(620, 'Lasta', 97),
(621, 'Aksum', 97),
(622, 'Humera', 97),
(623, 'Tigre', 97),
(624, 'Buee Town Administration', 98),
(625, 'Buta Jira Town Administration', 98),
(626, 'Enseno Town Administration', 98),
(627, 'Meskane', 98),
(628, 'Misrak Meskan', 98),
(629, 'Sodo', 98),
(630, 'South Sodo', 98),
(631, 'Buee Town Administration', 98),
(632, 'Buta Jira Town Administration', 98),
(633, 'Enseno Town Administration', 98),
(634, 'Meskane', 98),
(635, 'Misrak Meskan', 98),
(636, 'Sodo', 98),
(637, 'South Sodo', 98),
(638, 'Arekit City Administration', 99),
(639, 'Cheha', 99),
(640, 'Emdebir City Administration', 99),
(641, 'Endegagn', 99),
(642, 'Enor', 99),
(643, 'Enor Ener Meger', 99),
(644, 'Ezja', 99),
(645, 'Gedebano Gutazer Welene', 99),
(646, 'Geta', 99),
(647, 'Gumer', 99),
(648, 'Gunchere City Administration', 99),
(649, 'Muhurna Aklil', 99),
(650, 'Welkite City Administration', 99),
(651, 'Abeshege', 99),
(652, 'Agena City Administration', 99),
(653, 'Gombora', 100),
(654, 'Homecho Town Administration', 100),
(655, 'Hossana Town Administration', 100),
(656, 'Jajura Town Administration', 100),
(657, 'Lemo', 100),
(658, 'Mierab Badawacho', 100),
(659, 'Mierab Soro', 100),
(660, 'Mierab Soro Town Administration', 100),
(661, 'Misha', 100),
(662, 'Misrak Badewacho', 100),
(663, 'Shashogo', 100),
(664, 'Shone Town Administration', 100),
(665, 'Soro', 100),
(666, 'Atoți Ulo', 101),
(667, 'Qulito City Administration', 101),
(668, 'Wera Dijo', 101),
(669, 'Wera Zuriya', 101),
(670, 'Adilo', 102),
(671, 'Angacha', 102),
(672, 'Angacha City Administration', 102),
(673, 'Damboya', 102),
(674, 'Denboya City Administration', 102),
(675, 'Doyogena', 102),
(676, 'Doyogena City Administration', 102),
(677, 'Durame City Administration', 102),
(678, 'Hadero City Administration', 102),
(679, 'Hadero Tunto', 102),
(680, 'Mudula City Administration', 102),
(681, 'Qacha Bira', 102),
(682, 'Qedida Gamela', 102),
(683, 'Shinshicho City Administration', 102),
(684, 'Mareqo Special Wereda', 103),
(685, 'Qebena Special Wereda', 104),
(686, 'Alem Gebeya City Administration', 105),
(687, 'Alicho Weriro', 105),
(688, 'Dalocha', 105),
(689, 'Dalocha City Administration', 105),
(690, 'Hulbereg', 105),
(691, 'Kibet City Administration', 105),
(692, 'Lanfro', 105),
(693, 'Mierab Azerernet', 105),
(694, 'Misrak Azerernet', 105),
(695, 'Misrak Silti', 105),
(696, 'Mito', 105),
(697, 'Sankura', 105),
(698, 'Silti', 105),
(699, 'Tora City Administration', 105),
(700, 'Mudula Town Administration', 106),
(701, 'Deri Saja', 107),
(702, 'Fofa', 107),
(703, 'Saja City Administration', 107),
(704, 'Toba', 107);

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

DROP TABLE IF EXISTS `zones`;
CREATE TABLE IF NOT EXISTS `zones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `region_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `region_ibk_1` (`region_id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`id`, `name`, `region_id`) VALUES
(1, 'Addis Ketema', 1),
(2, 'Akaki Kality', 1),
(3, 'Arada', 1),
(4, 'Bole', 1),
(5, 'Gullele', 1),
(6, 'Kirkos', 1),
(7, 'Kolfe Keranio', 1),
(8, 'Lideta', 1),
(9, 'Nifas Silk-Lafto', 1),
(10, 'Yeka', 1),
(11, 'Lemi Kura', 1),
(12, 'Awsi Rasu', 2),
(13, 'Fantena Rasu', 2),
(14, 'Gabi Rasu', 2),
(15, 'Hari Rasu', 2),
(16, 'Kilbati Rasu', 2),
(17, 'Awi', 3),
(18, 'Bahir Dar City', 3),
(19, 'Central Gondar', 3),
(20, 'East Gojjam', 3),
(21, 'North Gondar', 3),
(22, 'North Shewa', 3),
(23, 'North Wollo', 3),
(24, 'South Gondar', 3),
(25, 'South Wollo', 3),
(26, 'Wag Hemra', 3),
(27, 'West Gojjam', 3),
(28, 'West Gondar', 3),
(29, 'Oromia Special Zone', 3),
(30, 'Asosa', 4),
(31, 'Kamashi', 4),
(32, 'Mao Ena', 4),
(33, 'Metekel', 4),
(34, 'Dire Dawa City Administration', 5),
(35, 'Agnuak', 6),
(36, 'Majang', 6),
(37, 'GAMBELLA TOWN ADMIN', 6),
(38, 'Nuer', 6),
(39, 'Harari', 7),
(40, 'Arsi', 8),
(41, 'Bale', 8),
(42, 'Borena', 8),
(43, 'Buno Bedele', 8),
(44, 'East Hararghe', 8),
(45, 'East Shewa', 8),
(46, 'East Welega', 8),
(47, 'Guji', 8),
(48, 'Horo Guduru Welega', 8),
(49, 'Illu Aba Bor', 8),
(50, 'Jimma', 8),
(51, 'Kelem Welega', 8),
(52, 'North Shewa', 8),
(53, 'Southwest Shewa', 8),
(54, 'West Arsi', 8),
(55, 'West Guji', 8),
(56, 'West Hararghe', 8),
(57, 'West Shewa', 8),
(58, 'West Welega', 8),
(59, 'Debubawi Sidama Zone', 9),
(60, 'Hawassa City Administration', 9),
(61, 'Mehal Sidama Zone', 9),
(62, 'Misrak Sidama Zone', 9),
(63, 'Semen Sidama Zone', 9),
(64, 'Bench Sheko', 10),
(65, 'Dawuro', 10),
(66, 'Keffa', 10),
(67, 'Sheka', 10),
(68, 'West Omo', 10),
(69, 'Kontta Special', 10),
(70, 'Sitti', 11),
(71, 'Fafan', 11),
(72, 'Jarar', 11),
(73, 'Nogob', 11),
(74, 'Erer', 11),
(75, 'Shabelle', 11),
(76, 'Korahe', 11),
(77, 'Afder', 11),
(78, 'Liben', 11),
(79, 'Doolo', 11),
(80, 'Ale', 12),
(81, 'Ari', 12),
(82, 'Basketo', 12),
(83, 'Burji', 12),
(84, 'Gamo Zone', 12),
(85, 'Gardulla', 12),
(86, 'Gedeo', 12),
(87, 'Gofa', 12),
(88, 'Konso', 12),
(89, 'Koore', 12),
(90, 'South Omo', 12),
(91, 'Wolayita', 12),
(92, 'Central Zone', 13),
(93, 'East Zone', 13),
(94, 'Mekelle', 13),
(95, 'North West', 13),
(96, 'South East Zone', 13),
(97, 'South Zone', 13),
(98, 'East Gurage', 14),
(99, 'Gurage Zone', 14),
(100, 'Hadiya Zone', 14),
(101, 'Halaba', 14),
(102, 'Kembata Zone', 14),
(103, 'Mareqo Special Wereda', 14),
(104, 'Qebena Special Wereda', 14),
(105, 'Silte Zone', 14),
(106, 'Tembaro Special Wereda', 14),
(107, 'Yem', 14);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appoint_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `case` (`cid`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `reason_ibfk_1` FOREIGN KEY (`reason_id`) REFERENCES `reason` (`rid`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `assigned_judges`
--
ALTER TABLE `assigned_judges`
  ADD CONSTRAINT `caseuser_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `case` (`cid`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `usercase_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`uid`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `attach_files`
--
ALTER TABLE `attach_files`
  ADD CONSTRAINT `file_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `case` (`cid`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `case`
--
ALTER TABLE `case`
  ADD CONSTRAINT `case_type_ibfk_1` FOREIGN KEY (`case_type`) REFERENCES `case_type` (`ctid`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `case_info`
--
ALTER TABLE `case_info`
  ADD CONSTRAINT `case_info_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `case` (`cid`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `resolutions`
--
ALTER TABLE `resolutions`
  ADD CONSTRAINT `resolutions_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `case` (`cid`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `woredas`
--
ALTER TABLE `woredas`
  ADD CONSTRAINT `zone_ibk_1` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `zones`
--
ALTER TABLE `zones`
  ADD CONSTRAINT `region_ibk_1` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
