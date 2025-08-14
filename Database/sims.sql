-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 14, 2025 at 02:22 PM
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
-- Database: `sims`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE IF NOT EXISTS `announcements` (
  `anid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assign_student`
--

DROP TABLE IF EXISTS `assign_student`;
CREATE TABLE IF NOT EXISTS `assign_student` (
  `asid` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `section_id` int NOT NULL,
  `academic_year` int NOT NULL,
  PRIMARY KEY (`asid`),
  KEY `student_ibpk_1` (`student_id`),
  KEY `section_ibpk_1` (`section_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `assign_student`
--

INSERT INTO `assign_student` (`asid`, `student_id`, `section_id`, `academic_year`) VALUES
(1, 2, 7, 2018),
(2, 1, 14, 2018),
(3, 4, 5, 2017);

-- --------------------------------------------------------

--
-- Table structure for table `assign_teacher`
--

DROP TABLE IF EXISTS `assign_teacher`;
CREATE TABLE IF NOT EXISTS `assign_teacher` (
  `atid` int NOT NULL,
  `teacher_id` int NOT NULL,
  `section_id` int NOT NULL,
  `subject_id` int NOT NULL,
  KEY `sub_ibpk_1` (`subject_id`),
  KEY `sec_ibpk_1` (`section_id`),
  KEY `tec_ibpk_1` (`teacher_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `fid` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `message` varchar(50) NOT NULL,
  PRIMARY KEY (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

DROP TABLE IF EXISTS `marks`;
CREATE TABLE IF NOT EXISTS `marks` (
  `mid` int NOT NULL,
  `student_id` int NOT NULL,
  `section_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `result` int NOT NULL,
  `semester` int NOT NULL,
  `academic_year` varchar(9) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `stud_ibpk_1` (`student_id`),
  KEY `class_ibpk_1` (`section_id`),
  KEY `subj_ipbk_1` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 2, 'your account detail updated.', 0, '2025-08-08 15:35:31'),
(2, 2, 'your account detail updated.', 0, '2025-08-08 15:35:41'),
(3, 2, 'your account detail updated.', 0, '2025-08-08 15:36:57');

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
-- Table structure for table `role_type`
--

DROP TABLE IF EXISTS `role_type`;
CREATE TABLE IF NOT EXISTS `role_type` (
  `rid` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `abbreviation_name` varchar(30) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `role_type`
--

INSERT INTO `role_type` (`rid`, `role_name`, `abbreviation_name`, `description`, `created_at`) VALUES
(1, 'Teacher', 'TCH', 'Responsible for teaching students and managing classroom activities.', '2025-08-08 10:36:30'),
(2, 'Admin', 'ADM', 'Handles administrative tasks and system management.', '2025-08-08 10:36:30'),
(3, 'Director', 'DIR', 'Oversees school operations and strategic planning.', '2025-08-08 10:36:30'),
(4, 'Instructor', 'INSTR', 'Provides instruction and support to learners.', '2025-08-08 10:36:30');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

DROP TABLE IF EXISTS `schedule`;
CREATE TABLE IF NOT EXISTS `schedule` (
  `tid` int NOT NULL,
  `start_date` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
CREATE TABLE IF NOT EXISTS `sections` (
  `cid` int NOT NULL AUTO_INCREMENT,
  `section_name` varchar(30) NOT NULL,
  `class_type` varchar(30) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`cid`, `section_name`, `class_type`) VALUES
(1, '9A', 'GENERAL'),
(2, '9B', 'GENERAL'),
(3, '9C', 'GENERAL'),
(4, '10A', 'GENERAL'),
(5, '10B', 'GENERAL'),
(6, '10C', 'GENERAL'),
(7, '11A', 'NATURAL'),
(8, '11B', 'NATURAL'),
(9, '11C', 'NATURAL'),
(10, '11A', 'SOCIAL'),
(11, '11B', 'SOCIAL'),
(12, '11C', 'SOCIAL'),
(13, '12A', 'NATURAL'),
(14, '12B', 'NATURAL'),
(15, '12C', 'NATURAL'),
(16, '12A', 'SOCIAL'),
(17, '12B', 'SOCIAL'),
(18, '12C', 'SOCIAL');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `sid` int NOT NULL AUTO_INCREMENT,
  `student_photo` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `student_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `father_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `grand_father_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `gender` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dob` date NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `birth_place` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `nationality` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `region` int NOT NULL,
  `zone` int DEFAULT NULL,
  `woreda` int DEFAULT NULL,
  `kebele` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `mother_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `father_contact` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mother_contact` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `father_occupation` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mother_occupation` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `emergency_contact_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `blood_group` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `medical_condition` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `other_condition` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `disabilities` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `previous_school` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `previous_documents` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sid`),
  UNIQUE KEY `student_id` (`student_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`sid`, `student_photo`, `student_id`, `first_name`, `father_name`, `grand_father_name`, `gender`, `dob`, `email`, `phone`, `birth_place`, `nationality`, `region`, `zone`, `woreda`, `kebele`, `username`, `password`, `mother_name`, `father_contact`, `mother_contact`, `father_occupation`, `mother_occupation`, `emergency_contact_name`, `emergency_contact_phone`, `blood_group`, `medical_condition`, `other_condition`, `disabilities`, `previous_school`, `previous_documents`, `created_at`) VALUES
(1, '../assets/img/wallpaper.jpg', 'BSS/STU/0001/17', 'alem', 'Kebede', 'tefera', 'M', '2009-01-01', 'Abseyoum16@gmail.com', '+251909299398', 'bishoftu', 'ethiopia', 12, 89, 591, 'kebele 01', 'Mecry', 'igJzPdVP5cUymhx5zrs7NQ==', 'alem', '+251913865846', '+251913865846', 'doctor', 'doctur', 'seyoium', '+251909299398', 'A+', 'Epilepsy', '', 'Yes', 'ebenezer', '../assets/case_files/DS Outline.pdf', '2025-08-13 15:21:24'),
(2, '', 'BSS/STU/0002/17', 'abenezer', 'seyoum', 'tefera', 'M', '2009-01-01', 'Abeniseyoum16@gmail.com', '+251909299378', 'bishoftu', 'ethiopia', 12, 89, 591, 'kebele 01', 'ab', 'e3GsIIeW3jPNBqjoHG9rMw==', 'alem', '+251913865846', '+251913865846', 'doctor', 'doctur', 'seyoium', '+251909299398', 'A+', 'Diabetes', '', 'Yes', 'ebenezer', '../assets/case_files/DS Outline.pdf', '2025-08-13 15:37:38'),
(3, '../assets/img/wallpaper.jpg', 'BSS/STU/0003/17', 'alem', 'Abebe', 'tefera', 'M', '2009-01-01', 'abeniseyoum16@gmail.com', '+251909299378', 'bishoftu', 'ethiopia', 5, 34, 240, 'kebele 04', 'miki', '7JhZ6nTY0ZMD5g4DeE/W/Q==', 'alem', '+251913865846', '+251913865846', 'doctor', 'doctur', 'seyoium', '+251909299398', 'A+', 'Epilepsy', '', 'Yes', 'ebenezer', '../assets/case_files/DS Outline.pdf', '2025-08-13 15:43:56'),
(4, '../assets/img/profile.jpg', 'BSS/STU/0004/17', 'Natan', 'tes', 'ebenezer', 'M', '2005-01-01', 'abseyoum1634@gmail.com', '+251909299378', 'bishoftu', 'ethiopia', 1, 3, 21, 'kebele 05', 'nati', 'N2Wa28Ns9wvOFZ9feBSj1Q==', 'alem', '+251913865846', '+251913865846', 'doctor', 'doctur', 'tes', '+251909299398', 'A+', 'Heart Condition', '', 'No', 'ebenezer', '../assets/case_files/DS Outline.pdf', '2025-08-14 08:50:00');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `suid` int NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(30) NOT NULL,
  `abbreviation_name` varchar(30) NOT NULL,
  PRIMARY KEY (`suid`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`suid`, `subject_name`, `abbreviation_name`) VALUES
(1, 'Sidaamu`Afoo', 'SID'),
(2, 'Amharic ', 'AMH'),
(3, 'English', 'ENG'),
(4, 'Mathematics', 'MATH'),
(5, 'Biology', 'BIO'),
(6, 'Chemistry', 'CHEM'),
(7, 'Physics', 'PHYS'),
(8, 'Geography', 'GEO'),
(9, 'History', 'HIST'),
(10, 'Physical Education', 'PE'),
(11, 'Citizenship', 'CIT'),
(12, 'Information and Communication ', 'ICT'),
(13, 'Economics', 'ECON');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `uid` int NOT NULL AUTO_INCREMENT,
  `idNumber` varchar(50) NOT NULL,
  `profile_picture` varchar(250) NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `father_name` varchar(30) NOT NULL,
  `grandfather_name` varchar(30) NOT NULL,
  `gender` varchar(30) NOT NULL,
  `user_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `user_status` int NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `idNumber`, `profile_picture`, `first_name`, `father_name`, `grandfather_name`, `gender`, `user_type`, `username`, `password`, `email`, `phone`, `user_status`) VALUES
(2, 'BSS/ADM/0002/17 ', '../assets/img/pp.jpg', 'abenezer ', 'seyoum', 'mamo', 'M', '2', 'ab', 'nIvRPLWFfGH/pgidb0LY8A==', 'Abseyoum16@gmail.com', '+251909299398', 1),
(3, 'BSS/DIR/0001/17', '../assets/img/profile.jpg', 'miki', 'Abebe', 'Mohamed', 'M', '3', 'miki', '7JhZ6nTY0ZMD5g4DeE/W/Q==', 'abseyoum1634@gmail.com', '+251909299378', 1),
(4, 'BSS/TCH/0001/17', '../assets/img/man united.jpg', 'Eyu', 'tareke', 'Mohamed', 'M', '1', 'hamid', '3yFM88FVzw+XU0J2lPnUpA==', 'abseyoum1634@gmail.com', '+251909299378', 0),
(5, 'BSS/INSTR/0001/17', '../assets/img/download.jpg', 'alem', 'Kebede', 'teferaa', 'F', '4', 'simo', 'KXCtygnqkBRg46jdWFl5yQ==', 'Abeniseyoum16@gmail.com', '+251909299378', 0);

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
-- Constraints for table `assign_student`
--
ALTER TABLE `assign_student`
  ADD CONSTRAINT `section_ibpk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`cid`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `student_ibpk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`sid`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `assign_teacher`
--
ALTER TABLE `assign_teacher`
  ADD CONSTRAINT `sec_ibpk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`cid`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `sub_ibpk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`suid`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `tec_ibpk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`uid`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `marks`
--
ALTER TABLE `marks`
  ADD CONSTRAINT `class_ibpk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`cid`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `stud_ibpk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`sid`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `subj_ipbk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`suid`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `woredas`
--
ALTER TABLE `woredas`
  ADD CONSTRAINT `woreda_pk_1` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `zones`
--
ALTER TABLE `zones`
  ADD CONSTRAINT `zone_ibk_1` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
