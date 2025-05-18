-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2025 at 02:23 PM
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
-- Database: `healthcare_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(9, 'Alexa', '$2y$10$7kukGEKeittRQLSNFQKPIu8FsWQ2fHxazi2/R8riVZmC2GbZbpcPO');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `doctor` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `fullName`, `email`, `phone`, `doctor`, `date`, `time`, `message`, `created_at`) VALUES
(10, 'Appey', 'mansanas@apple.com', '0999999999', 'Dr. Renelyn - Dermatologist', '2025-05-12', '16:40:00', 'masakit ngipin', '2025-05-12 08:40:08'),
(11, 'Appey', 'mansanas@apple.com', '0999999999', 'Dr. Renelyn - Dermatologist', '2025-05-12', '16:42:00', 'masakit ngipin nya', '2025-05-12 08:41:05'),
(12, 'Appey', 'mansanas@apple.com', '0999999999', 'Dr. Thea - Pediatrician', '2025-05-12', '16:50:00', 'kulang sa bitamina', '2025-05-12 08:48:32'),
(13, 'Appey', 'mansanas@apple.com', '0999999999', 'Dr. Alexa - Cardiologist', '2025-05-24', '20:25:00', 'hindi makahinga at masikip damit', '2025-05-17 11:25:42'),
(14, 'Appey', 'mansanas@apple.com', '0999999999', 'Dr. Alexa - Cardiologist', '2025-05-18', '19:26:00', 'dadas', '2025-05-17 11:26:33'),
(15, 'Appey', 'mansanas@apple.com', '0999999999', 'Dr. Alexa - Cardiologist', '2025-05-24', '19:41:00', 'kabag', '2025-05-17 11:39:35'),
(16, 'Appey', 'mansanas@apple.com', '0999999999', 'Dr. Alexa - Cardiologist', '2025-05-20', '19:40:00', 'sadasdad', '2025-05-17 11:39:51'),
(17, 'Appey', 'mansanas@apple.com', '0999999999', 'Dr. Alexa - Cardiologist', '2025-05-20', '19:43:00', 'adsad', '2025-05-17 11:42:36'),
(18, 'Appey', 'mansanas@apple.com', '0999999999', 'Dr. Thea - Pediatrician', '2025-05-26', '19:48:00', 'dasdsa', '2025-05-17 11:45:43');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `contactName` varchar(255) NOT NULL,
  `contactEmail` varchar(255) NOT NULL,
  `contactMessage` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `contactName`, `contactEmail`, `contactMessage`, `created_at`) VALUES
(1, 'Alexa', '0323-3701@lspu.edu.ph', 'Goodmorning', '2025-04-28 02:20:57'),
(2, 'Alexa', '0323-3701@lspu.edu.ph', 'Hi', '2025-05-05 03:42:39');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `user_id`) VALUES
(4, 'Dr. Alexa - Cardiologist', 15),
(5, 'Dr. Thea - Pediatrician', 16),
(6, 'Dr. Renelyn - Dermatologist', 17);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_name`, `email`, `message`, `sent_at`) VALUES
(1, 'Alexa', NULL, 'Hello', '2025-05-05 11:53:45');

-- --------------------------------------------------------

--
-- Table structure for table `message_replies`
--

CREATE TABLE `message_replies` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `reply_text` text NOT NULL,
  `replied_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message_replies`
--

INSERT INTO `message_replies` (`id`, `message_id`, `reply_text`, `replied_at`) VALUES
(1, 1, 'Hi', '2025-05-05 12:56:51');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Doctor'),
(3, 'Patient');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` int(11) NOT NULL,
  `age` int(11) NOT NULL,
  `address` text NOT NULL,
  `birthday` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `role`, `age`, `address`, `birthday`, `email`, `password`, `created_at`, `updated_at`, `phone`) VALUES
(2, 'Thea Lopez', 3, 21, 'San Lucas', '2004-01-14', 'thea@gmail.com', '$2y$10$6Xazg7116XLo3gLqLPy30epRxCeiXqQx0OFXCsXggJfExYTIW6JKi', '2025-04-28 03:39:42', '2025-05-12 07:32:45', '9938261901'),
(3, 'Alexa', 3, 20, 'San Pablo', '2005-01-17', '0323-3701@lspu.edu.ph', '$2y$10$HrvVN42Ie3nO4bh3v/Oisu81xbpW9fuP6tz3Cs0WotEJrv3.b0Ycu', '2025-05-06 12:20:41', '2025-05-12 07:33:08', '0953287752'),
(6, 'Appey', 3, 21, 'Taga Putol', '2003-10-15', 'mansanas@apple.com', '$2y$10$tIyHk/vREGf/aEjgWtgmBein6oBkQxXPUpiGmRpjuxThNGTCK0udC', '2025-05-11 11:31:49', '2025-05-12 07:33:12', '0999999999'),
(15, 'Dr. Alexa - Cardiologist', 2, 40, '123 Heart Ave', '1984-01-01', 'alexa@healthcare.com', '$2y$10$fhkz0PM5ksuocuutgSyupumfhMNc28QV/senWG5SnXTPD55zt5DZC', '2025-05-12 08:28:15', '2025-05-12 08:28:15', '0912345678'),
(16, 'Dr. Thea - Pediatrician', 2, 38, '456 Kids St', '1986-02-02', 'thea@healthcare.com', '$2y$10$9UQ08qPEGmu8O6QuGWW01.7.IJzDwwyGCC0h1.JCMzHoI2ALGbhpi', '2025-05-12 08:28:15', '2025-05-12 08:28:15', '0923456789'),
(17, 'Dr. Renelyn - Dermatologist', 2, 35, '789 Skin Blvd', '1989-03-03', 'renelyn@healthcare.com', '$2y$10$GXFdCSLyK3aghozFQp/d8.woqRCahsJ83JefG4aFkvAPt2wY60HZC', '2025-05-12 08:28:15', '2025-05-12 08:28:15', '0934567890'),
(18, 'Admin', 1, 30, 'Admin HQ', '1994-04-04', 'admin@healthcare.com', '$2y$10$r0vlTIxrV8pxvyluI0DjM.OlYtW/fygZa.0SqMhkXajBE/nH.DIdC', '2025-05-12 08:28:15', '2025-05-12 08:28:15', '0999999999'),
(21, 'Appey', 1, 1, 'Taga Putol', '2025-05-17', 'mansanas@apple1.com', '$2y$10$fUzN8QjaUXtwYQwIrK/zB.F7VhkZZoECMDjkwUJNqyl0CKdjUR5bC', '2025-05-17 11:17:19', '2025-05-17 11:17:19', '0999999999');

-- --------------------------------------------------------

--
-- Table structure for table `user_appointments`
--

CREATE TABLE `user_appointments` (
  `id` int(11) NOT NULL,
  `client_name` varchar(100) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_doctor_name` (`doctor`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message_replies`
--
ALTER TABLE `message_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_role` (`role`);

--
-- Indexes for table `user_appointments`
--
ALTER TABLE `user_appointments`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `message_replies`
--
ALTER TABLE `message_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_appointments`
--
ALTER TABLE `user_appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_doctor_name` FOREIGN KEY (`doctor`) REFERENCES `doctors` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `message_replies`
--
ALTER TABLE `message_replies`
  ADD CONSTRAINT `message_replies_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_role` FOREIGN KEY (`role`) REFERENCES `roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
