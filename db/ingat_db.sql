-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 26, 2025 at 12:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ingat_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(250) NOT NULL,
  `updated_at` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `firstname`, `middlename`, `lastname`, `email`, `password`, `updated_at`) VALUES
(1, 'admin', 'Admin', 'test', 'testlast', 'test@gmail.com', '21232f297a57a5a743894a0e4a801fc3', '08-05-2020 07:23:45 PM');

-- --------------------------------------------------------

--
-- Table structure for table `complaintremark`
--

CREATE TABLE `complaintremark` (
  `id` int(11) NOT NULL,
  `complaint_number` varchar(50) NOT NULL,
  `status` varchar(255) NOT NULL,
  `remark` mediumtext NOT NULL,
  `remark_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `complaintremark`
--

INSERT INTO `complaintremark` (`id`, `complaint_number`, `status`, `remark`, `remark_date`) VALUES
(22, 'CMP-1737784515-1173', 'In Progress', 's', '2025-01-25 06:46:13'),
(23, 'CMP-1737784515-1173', 'Solved', 's', '2025-01-25 06:46:25'),
(24, 'CMP-1737786981-8271', 'In Progress', 'g', '2025-01-25 06:48:48'),
(25, 'CMP-1737786981-8271', 'Solved', 'j', '2025-01-25 06:49:15'),
(26, 'CMP-1737871494-2357', 'In Progress', 'sure', '2025-01-26 06:05:28'),
(27, 'CMP-1737788800-9692', 'In Progress', 's', '2025-01-26 06:53:07');

-- --------------------------------------------------------

--
-- Table structure for table `crime_types`
--

CREATE TABLE `crime_types` (
  `id` int(11) NOT NULL,
  `crime_type` varchar(255) NOT NULL,
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crime_types`
--

INSERT INTO `crime_types` (`id`, `crime_type`, `details`) VALUES
(1, 'Theft', 'Stealing property without consent'),
(2, 'Assault', 'Physical attack causing harm');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `name`, `details`, `image`, `upload_date`) VALUES
(2, 'Theft in Downtown Area', 'We want to alert our community about a recent series of thefts in the downtown area. Multiple incidents have been reported where personal belongings were stolen from parked vehicles and pedestrians. We urge everyone to stay vigilant, ensure your vehicles are securely locked, and avoid leaving valuables in plain sight. If you have any information, please contact local law enforcement immediately.', '../uploads/type1.jpg', '2024-09-21 10:54:05'),
(52, 's', 's', '../users/complaintdocs/c2.jpg', '2025-01-24 11:26:17');

-- --------------------------------------------------------

--
-- Table structure for table `tblcomplaints`
--

CREATE TABLE `tblcomplaints` (
  `complaint_number` varchar(50) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `crime_type_id` int(11) DEFAULT NULL,
  `weapon_id` int(11) DEFAULT NULL,
  `complaint_details` mediumtext NOT NULL,
  `complaint_file` varchar(255) DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(255) DEFAULT NULL,
  `last_updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcomplaints`
--

INSERT INTO `tblcomplaints` (`complaint_number`, `userId`, `crime_type_id`, `weapon_id`, `complaint_details`, `complaint_file`, `registered_at`, `status`, `last_updated_at`, `location`) VALUES
('CMP-1737786981-8271', 58, 1, 1, 'sd', '', '2025-01-25 06:36:21', 'Solved', '2025-01-25 14:49:15', 'Kaagapay Road Graceville, San Jose del Monte City, Bulacan, Philippines'),
('CMP-1737788800-9692', 58, 2, 1, 'help', 'c2.jpg', '2025-01-25 07:06:40', 'In Progress', '2025-01-25 15:06:40', '3711, Bagabag, Nueva Vizcaya, Philippines'),
('CMP-1737871494-2357', 58, 1, 1, 'test da', 'c2.jpg', '2025-01-26 06:04:54', 'In Progress', '2025-01-26 14:05:28', 'Asajes Loop San Manuel, San Jose del Monte City, Bulacan, Philippines'),
('CMP-1737873998-5334', 58, 1, 1, 'test da', 'c2.jpg', '2025-01-26 06:46:38', NULL, '2025-01-26 14:46:38', 'Asajes Loop San Manuel, San Jose del Monte City, Bulacan, Philippines');

-- --------------------------------------------------------

--
-- Table structure for table `userlog`
--

CREATE TABLE `userlog` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `userip` binary(16) NOT NULL,
  `loginTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout` varchar(255) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userlog`
--

INSERT INTO `userlog` (`id`, `uid`, `username`, `userip`, `loginTime`, `logout`, `status`) VALUES
(1, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-11 08:38:52', '11-12-2024 02:09:45 PM', 1),
(2, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-11 08:40:43', '2024-12-11 14:10:45', 1),
(3, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-11 08:40:56', '2024-12-11 14:12:06', 0),
(4, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-11 08:42:46', '2024-12-11 14:29:17', 0),
(5, 27, 'yogij49630@jonespal.com', 0x32313330373036343333000000000000, '2024-12-11 08:59:20', '2024-12-11 14:47:44', 0),
(6, 27, 'yogij49630@jonespal.com', 0x32313330373036343333000000000000, '2024-12-11 09:18:44', '2024-12-11 14:52:48', 0),
(7, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-11 09:22:52', '2024-12-11 14:54:12', 0),
(8, 27, 'yogij49630@jonespal.com', 0x32313330373036343333000000000000, '2024-12-11 09:24:17', '', 1),
(9, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-11 10:48:38', '', 1),
(10, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-11 10:52:29', '', 1),
(11, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-11 10:54:00', '2024-12-11 17:02:07', 0),
(12, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-11 11:33:28', '2024-12-11 17:04:03', 0),
(13, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-11 11:36:06', '2024-12-11 17:06:32', 0),
(14, 27, 'yogij49630@jonespal.com', 0x32313330373036343333000000000000, '2024-12-11 11:41:10', '2024-12-11 17:17:42', 0),
(15, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-11 11:48:27', '2024-12-11 17:23:53', 0),
(16, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-11 11:53:57', '2024-12-11 17:24:31', 0),
(17, 27, 'yogij49630@jonespal.com', 0x32313330373036343333000000000000, '2024-12-11 11:54:35', '', 1),
(18, 27, 'yogij49630@jonespal.com', 0x32313330373036343333000000000000, '2024-12-11 15:11:46', '2024-12-11 20:42:17', 0),
(19, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-11 15:12:21', '2024-12-11 20:43:45', 0),
(20, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-11 15:13:49', '2024-12-11 21:10:12', 0),
(21, 27, 'yogij49630@jonespal.com', 0x32313330373036343333000000000000, '2024-12-11 15:49:23', '', 1),
(22, 30, 'rygyma@teleg.eu', 0x32313330373036343333000000000000, '2024-12-12 03:02:29', '2024-12-12 08:47:00', 0),
(23, 31, 'kilavevy@logsmarter.net', 0x32313330373036343333000000000000, '2024-12-12 03:18:43', '2024-12-12 08:56:04', 0),
(24, 31, 'kilavevy@logsmarter.net', 0x32313330373036343333000000000000, '2024-12-12 03:26:08', '2024-12-12 08:56:22', 0),
(25, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-12 06:29:35', '', 1),
(26, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-12 06:46:26', '2024-12-12 13:10:08', 0),
(27, 28, 'kavot66895@iminko.com', 0x32313330373036343333000000000000, '2024-12-12 07:45:34', '', 1),
(28, 34, 'christinejds13@gmail.com', 0x32313330373036343333000000000000, '2024-12-12 08:12:00', '2024-12-12 13:42:29', 0),
(29, 0, 'grandeautozoldyck@gmail.com', 0x3132372e302e302e3100000000000000, '2024-12-20 00:22:58', '', 0),
(30, 0, 'cute@gmail.com', 0x3132372e302e302e3100000000000000, '2024-12-20 00:23:23', '', 0),
(31, 0, 'yogij49630@jonespal.com', 0x3132372e302e302e3100000000000000, '2024-12-20 00:23:39', '', 0),
(32, 0, 'cute@gmail.com', 0x3132372e302e302e3100000000000000, '2024-12-20 00:24:01', '', 0),
(33, 0, 'cute@gmail.com', 0x3132372e302e302e3100000000000000, '2024-12-20 00:25:39', '', 0),
(34, 40, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2024-12-20 00:36:47', '2024-12-20 06:07:03', 0),
(35, 40, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2024-12-20 00:39:49', '2024-12-20 06:09:52', 0),
(36, 40, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2024-12-20 00:46:48', '', 1),
(37, 40, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2024-12-27 11:48:03', '2024-12-27 17:18:06', 0),
(38, 40, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2024-12-28 04:01:12', '2024-12-28 09:31:26', 0),
(39, 40, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2024-12-28 04:15:43', '', 1),
(40, 40, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-02 02:55:34', '', 1),
(41, 40, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-23 07:31:29', '', 1),
(42, 40, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-24 01:01:34', '', 1),
(43, 40, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-24 01:37:29', 'January 24, 2025 05:07:06 PM', 0),
(44, 40, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-24 11:37:47', 'January 25, 2025 07:53:49 AM', 0),
(45, 40, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-25 02:23:51', 'January 25, 2025 08:23:15 AM', 0),
(46, 40, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-25 02:53:17', 'January 25, 2025 09:03:20 AM', 0),
(47, 56, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-25 03:44:32', 'January 25, 2025 09:15:53 AM', 0),
(48, 56, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-25 03:46:01', 'January 25, 2025 09:16:04 AM', 0),
(49, 56, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-25 04:33:57', 'January 25, 2025 10:10:01 AM', 0),
(50, 56, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-25 04:40:05', 'January 25, 2025 10:10:59 AM', 0),
(51, 58, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-25 05:32:50', 'January 25, 2025 11:03:01 AM', 0),
(52, 58, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-25 05:53:52', 'January 25, 2025 06:12:15 PM', 0),
(53, 58, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-25 12:42:19', 'January 25, 2025 06:15:35 PM', 0),
(54, 58, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-25 12:45:38', 'January 25, 2025 08:19:38 PM', 0),
(55, 58, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-25 14:49:42', 'January 26, 2025 10:20:50 AM', 0),
(56, 58, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-26 04:50:54', 'January 26, 2025 10:21:04 AM', 0),
(57, 58, 'grandeautozoldyck@gmail.com', 0x32313330373036343333000000000000, '2025-01-26 06:04:33', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `verification_code` varchar(6) NOT NULL,
  `contact_no` bigint(11) DEFAULT NULL,
  `address` tinytext DEFAULT NULL,
  `user_image` varchar(255) DEFAULT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(1) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(255) NOT NULL,
  `upload_id` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `middlename`, `lastname`, `user_email`, `password`, `verification_code`, `contact_no`, `address`, `user_image`, `reg_date`, `updation_date`, `status`, `is_verified`, `verification_sent_at`, `username`, `upload_id`, `reset_token`, `token_expiry`) VALUES
(58, 'Dazai', 'N.', 'Osamu', 'grandeautozoldyck@gmail.com', '$2y$10$XKO0j1xMaRsgKMFHUF5t8uhoCsvHVLHdGVaDfWwW9IkLDJVOgg/Py', '', 2342433542, 'dfdgfs', '../uploads/dazai.jpg', '2025-01-25 05:18:03', '2025-01-25 11:47:03', 1, 1, '2025-01-25 05:18:03', 'pmfia', '../uploads/Student-ID-Card.jpg', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `weapons`
--

CREATE TABLE `weapons` (
  `id` int(11) NOT NULL,
  `weapon_type` varchar(255) NOT NULL,
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weapons`
--

INSERT INTO `weapons` (`id`, `weapon_type`, `details`) VALUES
(1, 'Knife', 'Sharp object used in close combat'),
(2, 'Gun', 'Firearm capable of shooting bullets'),
(3, 'Bat', 'Blunt object often used in assaults');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `complaintremark`
--
ALTER TABLE `complaintremark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crime_types`
--
ALTER TABLE `crime_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcomplaints`
--
ALTER TABLE `tblcomplaints`
  ADD PRIMARY KEY (`complaint_number`),
  ADD UNIQUE KEY `idx_complaint_number` (`complaint_number`),
  ADD KEY `fk_complaint_crime_type` (`crime_type_id`),
  ADD KEY `fk_complaint_weapon` (`weapon_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_userId` (`userId`);

--
-- Indexes for table `userlog`
--
ALTER TABLE `userlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `weapons`
--
ALTER TABLE `weapons`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `complaintremark`
--
ALTER TABLE `complaintremark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `crime_types`
--
ALTER TABLE `crime_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `userlog`
--
ALTER TABLE `userlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `weapons`
--
ALTER TABLE `weapons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblcomplaints`
--
ALTER TABLE `tblcomplaints`
  ADD CONSTRAINT `fk_complaint_crime_type` FOREIGN KEY (`crime_type_id`) REFERENCES `crime_types` (`id`),
  ADD CONSTRAINT `fk_complaint_weapon` FOREIGN KEY (`weapon_id`) REFERENCES `weapons` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
