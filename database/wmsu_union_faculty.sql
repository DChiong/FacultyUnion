-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2026 at 01:28 PM
-- Server version: 12.2.2-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wmsu_union_faculty`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_content`
--

CREATE TABLE `about_content` (
  `id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `p1` text NOT NULL,
  `p2` text NOT NULL,
  `p3` text NOT NULL,
  `image_path` varchar(255) DEFAULT 'img/about.jpg',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_content`
--

INSERT INTO `about_content` (`id`, `section_name`, `heading`, `p1`, `p2`, `p3`, `image_path`, `updated_at`) VALUES
(1, 'about_union', 'Upholding Faculty Rights and Academic Freedom', 'The WMSU Faculty Union is a united and independent organization dedicated to protecting the rights and welfare of the academic personnel.\nOur union serves as a strong collective voice, striving to ensure equitable access to professional development.\nWe are committed to defending academic freedom and fostering solidarity.', '', '', 'img/about.jpg', '2026-04-20 11:23:20');

-- --------------------------------------------------------

--
-- Table structure for table `about_topics`
--

CREATE TABLE `about_topics` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `about_topics`
--

INSERT INTO `about_topics` (`id`, `title`, `content`, `image_path`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 'FACULTY MANUAL', 'The Western Mindanao State University stands with the mandate of\r\nserving a wider number of people in a more pluralistic social, economic\r\nand cultural setting. It stands with the philosophy that education\r\nremains to be the most potent tool for change in the process of socioeconomic development and shall serve as a lead institution in the\r\npromotion of the same. In the fulfilment of its educational mandate,\r\nthe Western Mindanao State University performs a four-pronged\r\nfunction relevant to social needs and standards to include instruction, research,\r\nextension, and production.\r\nIn terms of instruction, the university offers curricular programs that suit to the\r\nneeds of the diverse sectors of society with focus on the development potentials of\r\nindustries. It constantly initiates the review and revision of course offerings, and the\r\nupdating of program contents in order to ensure that the programs offered are\r\nreflective of the needs of the times. To be able to realize this, fully committed and\r\npassionate faculty need to perform the necessary measures for the holistic development\r\nof the WMSU graduates, thus, it is imperative that the faculty be guided and\r\nacquainted by the rules, and policies relevant to the execution of their duties and\r\nresponsibilities.\r\nThis manual contains guidelines and information relevant to the faculty which\r\nwere culled from the University Code, resolutions approved by the Board of Regents,\r\ndecisions of the University Academic Council, Executive Orders, and memoranda\r\nfrom the Office of the President, and relevant documents from various units of the\r\nUniversity and some government agencies.', '', 0, 1, '2026-08-12 02:22:03', '2026-08-12 02:22:03');

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `target` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_videos`
--

CREATE TABLE `admin_videos` (
  `id` int(11) NOT NULL,
  `video_title` varchar(255) NOT NULL,
  `video_type` enum('youtube','raw') NOT NULL,
  `video_source` text NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_videos`
--

INSERT INTO `admin_videos` (`id`, `video_title`, `video_type`, `video_source`, `thumbnail`, `created_at`) VALUES
(1, 'TEAM UNITY WITH CARE 2 0 ', 'youtube', 'https://www.youtube.com/embed/_f48t-J88yU', NULL, '2026-04-22 09:19:59'),
(2, '3 PHILBRITISIH INSURANCE', 'youtube', 'https://www.youtube.com/embed/2U4VXtLQWyk', NULL, '2026-04-23 01:46:25'),
(3, ' Financial Literacy', 'youtube', 'https://www.youtube.com/embed/z6HTNoqnhqs', NULL, '2026-04-23 01:47:47'),
(4, 'favourite', 'youtube', 'https://youtube.com/embed/Tm8LGxTLtQk?si=YA1R2gFdfpJh00bm', NULL, '2026-07-20 20:43:45');

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

CREATE TABLE `awards` (
  `id` int(11) NOT NULL,
  `award_title` varchar(255) NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `award_image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `award_year` year(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `awards`
--

INSERT INTO `awards` (`id`, `award_title`, `recipient_name`, `award_image`, `description`, `award_year`, `created_at`) VALUES
(4, 'awarding', 'ccs', 'uploads/awards/1776824266_476080601_1180706744062389_5378603482475314303_n.jpg', 'srdytgihopioiuytjfjxhcvjkjopouyudtkghv', '2026', '2026-04-22 02:17:46'),
(5, 'art award', 'ccs', 'uploads/awards/1776912028_visual_01.jpg', 'hkkjggggggggggggggggggggggggggggggggggg', '2026', '2026-04-22 02:18:53'),
(6, 'service awards', 'chiong', 'uploads/awards/1784179902_faculty seal.jpg', 'wow', '2026', '2026-07-16 05:31:42'),
(7, 'first honor', 'chiong', 'uploads/awards/1784582039_faculty seal.jpg', 'wowowow', '2024', '2026-07-20 20:39:19'),
(8, 'art award', 'chiong', 'uploads/awards/1784586886_faculty seal.jpg', 'artsss', '2026', '2026-07-20 22:34:46');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_info`
--

CREATE TABLE `contact_info` (
  `id` int(11) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(100) NOT NULL,
  `hours` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `facebook_url` varchar(255) NOT NULL,
  `facebook_name` varchar(255) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_info`
--

INSERT INTO `contact_info` (`id`, `address`, `phone`, `hours`, `email`, `facebook_url`, `facebook_name`, `updated_at`) VALUES
(1, 'Faculty Union Office, Western Mindanao State University, Normal Rd, Zamboanga City, 7000', '+63 9938603707', 'Mon - Fri: 8:00 AM - 5:00 PM', 'facultyunion@wmsu.edu.ph', 'https://www.facebook.com/WMSUFacultyUnion', 'WMSU Faculty Union', '2026-07-27 01:26:58');

-- --------------------------------------------------------

--
-- Table structure for table `dynamic_pages`
--

CREATE TABLE `dynamic_pages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `dynamic_pages`
--

INSERT INTO `dynamic_pages` (`id`, `title`, `content`, `image_path`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'news', 'about news', 'admins/uploads/pages/1785344930_faculty seal.jpg', 1, 1, '2026-07-29 17:08:50', '2026-08-12 10:07:45'),
(5, 'acreditation', 'acreditation', '', 2, 1, '2026-08-12 10:01:43', '2026-08-12 10:07:52');

-- --------------------------------------------------------

--
-- Table structure for table `dynamic_posts`
--

CREATE TABLE `dynamic_posts` (
  `id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `dynamic_posts`
--

INSERT INTO `dynamic_posts` (`id`, `page_id`, `title`, `content`, `image_path`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'WMSU FACULTY UNION | LUPANG HINIRANG PHOTO SHOOT', 'A meaningful morning of unity, patriotism, and service as the WMSU Faculty Union Officers came together for the Lupang Hinirang Photo Shoot at the WMSU Open Field.\r\nOur heartfelt gracias to the WMSU Public Affairs Office (PAO) for making this special activity possible. 🙏🇵🇭\r\nSalute and highest respect to our Madam VPAA (Doc Bossing Bern) and Madam President (Madam Bossing Doc Carla) for your leadership, support, and commitment to our beloved Western Mindanao State University. ❤️\r\nMost of all, ever thankful to all WMSU Faculty Union Officers who willingly participated and stood together even under the heat of the sun.\r\n☀️🇵🇭 Your presence, cooperation, and unwavering support truly reflect the spirit of unity and solidarity that makes our Faculty Union strong.\r\nToday, we proudly stand not only as faculty leaders, but as WMSUans united in love and service to our University and our country.', 'admins/uploads/posts/1786510488_770617614_28298261319770359_4306333096064401260_n.jpg', 1, '2026-07-29 17:23:53', '2026-08-12 04:54:48'),
(8, 1, 'pickleball', 'The Legends of WMSU Pickleball 🏓🔥\r\nThe culminating activity of the WMSU Summer Pickleball Class, where skills were sharpened, friendships were built, and every match was played with heart.\r\nNice game, mga bossing! 👏💪\r\nNow, it\'s time to prepare for the ultimate Faculty Union (FU) Pickleball Challenge during the WMSU Foundation Day this July.\r\nGame on! See you on the court! 🏆🏓', 'admins/uploads/posts/1786184857_730140455_27762028953393601_6528176154680109012_n.jpg', 1, '2026-07-29 17:57:13', '2026-08-08 10:27:37'),
(10, 5, 'seminar', 'another', '', 1, '2026-08-12 10:03:10', '2026-08-12 10:03:10'),
(14, 5, 'meetings', 'about funds', '', 1, '2026-08-12 14:03:30', '2026-08-12 14:03:30');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `event_start_date` date NOT NULL,
  `banner_path` varchar(255) DEFAULT 'img/event-default.jpg',
  `description` text NOT NULL,
  `event_dates` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `event_time` varchar(100) NOT NULL,
  `admission` varchar(100) DEFAULT 'Free Entry',
  `features` varchar(255) DEFAULT NULL,
  `highlights` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `subtitle`, `event_start_date`, `banner_path`, `description`, `event_dates`, `location`, `event_time`, `admission`, `features`, `highlights`, `created_at`) VALUES
(1, 'WMSU FACULTY UNION | LUPANG HINIRANG PHOTO SHOOT', '', '2026-08-12', 'img/1786509603_770617614_28298261319770359_4306333096064401260_n.jpg', 'WMSU FACULTY UNION | LUPANG HINIRANG PHOTO SHOOT\r\nA meaningful morning of unity, patriotism, and service as the WMSU Faculty Union Officers came together for the Lupang Hinirang Photo Shoot at the WMSU Open Field.\r\nOur heartfelt gracias to the WMSU Public Affairs Office (PAO) for making this special activity possible. 🙏🇵🇭\r\nSalute and highest respect to our Madam VPAA (Doc Bossing Bern) and Madam President (Madam Bossing Doc Carla) for your leadership, support, and commitment to our beloved Western Mindanao State University. ❤️\r\nMost of all, ever thankful to all WMSU Faculty Union Officers who willingly participated and stood together even under the heat of the sun.\r\n☀️🇵🇭 Your presence, cooperation, and unwavering support truly reflect the spirit of unity and solidarity that makes our Faculty Union strong.\r\nToday, we proudly stand not only as faculty leaders, but as WMSUans united in love and service to our University and our country.\r\nOne WMSU. One Faculty Union. One Heart for the Philippines. 🇵🇭❤️💙', '2026-08-12', 'Faculty Union Hall', '9:00AM - 12:00PM', '', NULL, '', '2026-04-21 22:25:25'),
(2, 'Faculty Training & Skills Workshop', '', '2028-12-09', 'img/1784266194_faculty seal.jpg', 'Training on new tools, software, or academic skills.', '2028-12-09', 'Faculty Union Hall', '9:00am - 8:00pm', '', 'dfghjklxcvbnm', '', '2026-04-22 00:55:02'),
(3, 'CONTEST', '', '2028-05-13', 'img/1784375252_1778622556_singer_1.jpg', 'iugfdsaiuhgfdsauytfd', '2028-05-13', 'Faculty Union Hall', '9:00am - 8:00pm', '', NULL, '', '2026-05-12 21:49:16'),
(4, 'graduation', '', '2026-09-16', 'img/1784181055_faculty seal.jpg', 'congratss', '2026-09-16', 'kcc', '9:00am - 8:00pm', '', NULL, '', '2026-07-16 05:50:55'),
(5, 'FACULTY SEMINAR', '', '2026-09-16', 'img/1784582264_485853484_1622968875024570_727232630580192803_n.jpg', 'ABOUT FACULTY', '2026-09-16', 'wmsu', '9:00am - 8:00pm', '', NULL, '', '2026-07-20 20:41:31'),
(6, 'seminar', '', '2020-09-17', 'img/1784586810_faculty seal.jpg', 'seminar for faculty', '2020-09-17', 'wmsu', '9:00am - 8:00pm', '', NULL, '', '2026-07-20 22:33:30');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `active_check` varchar(255) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `title`, `url`, `active_check`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'HOME', 'index.php#home', 'index.php', 9, 1, '2026-08-12 12:42:52', '2026-08-13 05:28:03'),
(2, 'ABOUT US', 'index.php#about', '', 1, 1, '2026-08-12 12:42:52', '2026-08-12 12:47:43'),
(3, 'EVENTS', 'index.php#events', '', 2, 1, '2026-08-12 12:42:52', '2026-08-12 12:47:50'),
(4, 'AWARDS', 'index.php#awards', '', 3, 1, '2026-08-12 12:42:52', '2026-08-12 12:47:57'),
(5, 'VIDEOS', 'index.php#videos', '', 4, 1, '2026-08-12 12:42:52', '2026-08-12 12:48:03'),
(6, 'CONTACT', 'index.php#footer', '', 5, 1, '2026-08-12 12:42:52', '2026-08-12 12:48:10'),
(7, 'OFFICERS', 'officers.php', 'officers.php', 6, 1, '2026-08-12 12:42:52', '2026-08-12 13:27:55'),
(8, 'NEWS', 'view_page.php?id=1', 'view_page.php', 7, 1, '2026-08-12 12:55:17', '2026-08-12 12:57:12'),
(9, 'ACREDITATION', 'view_page.php?id=5', 'view_page.php', 8, 1, '2026-08-12 12:55:17', '2026-08-12 13:06:41');

-- --------------------------------------------------------

--
-- Table structure for table `objectives`
--

CREATE TABLE `objectives` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `objectives`
--

INSERT INTO `objectives` (`id`, `content`, `sort_order`) VALUES
(1, 'To defend and advance academic freedom and shared governance in WMSU;', 1),
(2, 'To foster solidarity, collaboration, linkages, partnership, and sense of community;', 2),
(3, 'To promote faculty participation in WMSU’s institutional governance;', 3),
(4, 'To advance the rights and welfare of the academic personnel;', 4),
(5, 'To promote fair environment and protect faculty from arbitrary decisions;', 5),
(6, 'To improve the status and conditions of faculty members.', 6),
(7, 'To defend and advance academic freedom and shared governance in WMSU;', 1),
(8, 'To foster solidarity, collaboration, linkages, partnership, and sense of community;', 2),
(9, 'To promote faculty participation in WMSU’s institutional governance;', 3),
(10, 'To advance the rights and welfare of the academic personnel;', 4),
(11, 'To promote fair environment and protect faculty from arbitrary decisions;', 5),
(14, 'To improve the status and conditions of faculty members.', 0);

-- --------------------------------------------------------

--
-- Table structure for table `officers`
--

CREATE TABLE `officers` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `position` varchar(100) NOT NULL,
  `department_acronym` varchar(20) DEFAULT NULL,
  `category` enum('Executive','Finance') DEFAULT 'Executive',
  `rank` int(11) DEFAULT 0,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `officers`
--

INSERT INTO `officers` (`id`, `full_name`, `position`, `department_acronym`, `category`, `rank`, `profile_picture`) VALUES
(1, 'Dr. Adrian P. Semorlans', 'President', 'CLA', 'Executive', 1, 'admins/uploads/awards/officers/officer_1784181695_6a5873bf9709f.jpg'),
(2, 'Prof. Harry Subibi', 'Vice President', 'CTE', 'Executive', 2, 'admins/uploads/awards/officers/officer_1784181711_6a5873cf8cd1f.jpg'),
(3, 'Prof. Evelyn Angeles', 'Secretary', 'COE', 'Executive', 3, NULL),
(4, 'Dr. Cheryl Barredo', 'Treasurer', 'CLA', 'Executive', 4, NULL),
(5, 'Prof. Erwin Alonzo', 'Auditor', 'CSM', 'Executive', 5, NULL),
(6, 'Prof. Victor Pagal', 'PIO', 'ESU', 'Executive', 6, NULL),
(7, 'Dr. Mervyn Garingo', 'Project Manager', 'CTE', 'Executive', 7, NULL),
(8, 'Prof. Patrick Brown', 'Finance Officer I', 'CHE', 'Finance', 8, NULL),
(9, 'Prof. Mai Gonzales', 'Finance Officer II', 'CN', 'Finance', 9, NULL),
(10, 'Sheldon Cooper', 'Supervisor', 'CCJE', 'Finance', 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `layout` varchar(50) DEFAULT 'standard',
  `status` enum('draft','published') DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `layout`, `status`, `created_at`, `updated_at`) VALUES
(1, 'officers', 'officersss', '', 'officers', 'published', '2026-07-29 06:20:32', '2026-07-29 09:58:48');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `status` enum('draft','published') DEFAULT 'draft',
  `publish_date` datetime DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `category_id`, `status`, `publish_date`, `author_id`, `thumbnail`, `created_at`, `updated_at`) VALUES
(1, 'sad', 'sad-2980f', '', NULL, 'published', '2022-02-22 00:00:00', 2, '', '2026-07-29 06:34:47', '2026-07-29 08:52:38');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'general',
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_group`, `setting_key`, `setting_value`) VALUES
(1, 'general', 'site_name', 'Faculty Union CMS'),
(2, 'general', 'site_logo', 'assets/img/logo.png'),
(3, 'contact', 'contact_address', 'Faculty Union Office, Western Mindanao State University, Normal Rd, Zamboanga City, 7000'),
(4, 'contact', 'contact_phone', '+63 62 991 1040'),
(5, 'contact', 'contact_email', 'facultyunion@wmsu.edu.ph'),
(6, 'homepage', 'hero_title', 'Welcome to WMSU Faculty Union'),
(7, 'homepage', 'hero_subtitle', 'Upholding Faculty Rights and Academic Freedom'),
(8, 'homepage', 'about_text', 'The WMSU Faculty Union is a united and independent organization dedicated to protecting the rights and welfare of the academic personnel.'),
(9, 'homepage', 'vision_text', 'A united and independent faculty union that cares for the rights and welfare of the WMSU FACULTY.'),
(10, 'homepage', 'mission_text', 'To defend and advance academic freedom and shared governance in WMSU.');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) NOT NULL,
  `logo_path` varchar(255) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `site_name`, `logo_path`, `updated_at`) VALUES
(1, 'Faculty Union', 'img/1784245345_faculty seal.jpg', '2026-07-20 22:16:13');

-- --------------------------------------------------------

--
-- Table structure for table `union_info`
--

CREATE TABLE `union_info` (
  `id` int(11) NOT NULL,
  `vision` text NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `union_info`
--

INSERT INTO `union_info` (`id`, `vision`, `updated_at`) VALUES
(1, 'A united and independent faculty union that cares for the rights and welfare of the WMSU FACULTY, with strong collective voice gearing towards equitable access to professional development, opportunities, and healthy working environment. ', '2026-07-27 00:28:49');

-- --------------------------------------------------------

--
-- Table structure for table `union_objectives`
--

CREATE TABLE `union_objectives` (
  `id` int(11) NOT NULL,
  `objective_text` varchar(500) NOT NULL,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `union_officers`
--

CREATE TABLE `union_officers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(100) NOT NULL,
  `department_code` varchar(50) DEFAULT NULL,
  `category` enum('executive','finance') DEFAULT 'executive',
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `union_vision_mission`
--

CREATE TABLE `union_vision_mission` (
  `id` int(11) NOT NULL,
  `type` enum('vision','mission') NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(2, 'admin', '$2y$10$s9VysicSbjyyhrYsvNt0NelAZHsU/bTg5K2kn7vfV6wKETzTv.WgG', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_content`
--
ALTER TABLE `about_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_name` (`section_name`);

--
-- Indexes for table `about_topics`
--
ALTER TABLE `about_topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `admin_videos`
--
ALTER TABLE `admin_videos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `awards`
--
ALTER TABLE `awards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `contact_info`
--
ALTER TABLE `contact_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dynamic_pages`
--
ALTER TABLE `dynamic_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dynamic_posts`
--
ALTER TABLE `dynamic_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_page_id` (`page_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `objectives`
--
ALTER TABLE `objectives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `officers`
--
ALTER TABLE `officers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `union_info`
--
ALTER TABLE `union_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `union_objectives`
--
ALTER TABLE `union_objectives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `union_officers`
--
ALTER TABLE `union_officers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `union_vision_mission`
--
ALTER TABLE `union_vision_mission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_content`
--
ALTER TABLE `about_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `about_topics`
--
ALTER TABLE `about_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_videos`
--
ALTER TABLE `admin_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `awards`
--
ALTER TABLE `awards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_info`
--
ALTER TABLE `contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dynamic_pages`
--
ALTER TABLE `dynamic_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `dynamic_posts`
--
ALTER TABLE `dynamic_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `objectives`
--
ALTER TABLE `objectives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `officers`
--
ALTER TABLE `officers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `union_info`
--
ALTER TABLE `union_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `union_objectives`
--
ALTER TABLE `union_objectives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `union_officers`
--
ALTER TABLE `union_officers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `union_vision_mission`
--
ALTER TABLE `union_vision_mission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dynamic_posts`
--
ALTER TABLE `dynamic_posts`
  ADD CONSTRAINT `fk_page_id` FOREIGN KEY (`page_id`) REFERENCES `dynamic_pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
