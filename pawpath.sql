-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2026 at 06:08 AM
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
-- Database: `pawpath`
--

-- --------------------------------------------------------

--
-- Table structure for table `adoption_applications`
--

CREATE TABLE `adoption_applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `living_situation` enum('Apartment','House','Farm','Other') DEFAULT NULL,
  `previous_adoption` tinyint(1) DEFAULT 0,
  `why_adopt` text DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `feedback` text DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `is_withdrawn` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adoption_applications`
--

INSERT INTO `adoption_applications` (`id`, `user_id`, `pet_id`, `full_name`, `email`, `address`, `phone`, `living_situation`, `previous_adoption`, `why_adopt`, `preferred_date`, `status`, `feedback`, `reviewed_by`, `reviewed_at`, `is_withdrawn`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Md. Nurnove', 'nurnove2005@gmail.com', 'jhnb', '01727493660', 'House', 1, 'cv d df', '2026-05-28', '', NULL, NULL, NULL, 1, '2026-05-26 20:24:24', '2026-06-11 03:25:49'),
(2, 3, 1, 'fdf', 'nurnove2005@gmail.com', 'dedsxsx', '01846866454', 'House', 1, 'dsx dc dc', '2026-05-28', 'approved', 'vc fd dc cx', 2, '2026-05-30 19:41:57', 0, '2026-05-26 20:30:35', '2026-05-30 19:41:57');

-- --------------------------------------------------------

--
-- Table structure for table `eligibility_answers`
--

CREATE TABLE `eligibility_answers` (
  `id` int(11) NOT NULL,
  `attempt_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eligibility_answers`
--

INSERT INTO `eligibility_answers` (`id`, `attempt_id`, `question_id`, `answer`, `created_at`) VALUES
(1, 2, 1, 1, '2026-05-26 07:24:12'),
(2, 2, 2, 0, '2026-05-26 07:24:12'),
(3, 2, 3, 1, '2026-05-26 07:24:12'),
(4, 2, 4, 0, '2026-05-26 07:24:12'),
(5, 2, 5, 1, '2026-05-26 07:24:12'),
(6, 2, 6, 0, '2026-05-26 07:24:12'),
(7, 3, 1, 1, '2026-05-26 07:27:21'),
(8, 3, 2, 1, '2026-05-26 07:27:21'),
(9, 3, 3, 1, '2026-05-26 07:27:21'),
(10, 3, 4, 1, '2026-05-26 07:27:21'),
(11, 3, 5, 1, '2026-05-26 07:27:21'),
(12, 3, 6, 0, '2026-05-26 07:27:21');

-- --------------------------------------------------------

--
-- Table structure for table `eligibility_attempts`
--

CREATE TABLE `eligibility_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) DEFAULT 0,
  `is_eligible` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eligibility_attempts`
--

INSERT INTO `eligibility_attempts` (`id`, `user_id`, `score`, `is_eligible`, `created_at`) VALUES
(1, 1, 0, 0, '2026-05-26 06:37:23'),
(2, 1, 3, 0, '2026-05-26 07:24:12'),
(3, 1, 5, 1, '2026-05-26 07:27:21'),
(4, 2, 2, 0, '2026-05-26 07:52:16'),
(5, 3, 6, 1, '2026-05-26 20:27:41'),
(6, 3, 6, 1, '2026-05-26 20:29:54'),
(7, 4, 1, 0, '2026-06-11 04:05:34');

-- --------------------------------------------------------

--
-- Table structure for table `foster_applications`
--

CREATE TABLE `foster_applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `living_situation` enum('Apartment','House','Farm','Other') DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `feedback` text DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `is_withdrawn` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `foster_applications`
--

INSERT INTO `foster_applications` (`id`, `user_id`, `pet_id`, `full_name`, `email`, `phone`, `address`, `duration`, `living_situation`, `experience`, `status`, `feedback`, `reviewed_by`, `reviewed_at`, `is_withdrawn`, `created_at`, `updated_at`) VALUES
(1, 3, 4, 'Md. Nurnove', 'nurnove2005@gmail.com', '01727493660', 'jhnb ', '1weak', 'Apartment', 'bnm,mnbv ', 'pending', NULL, NULL, NULL, 0, '2026-05-30 19:13:20', '2026-05-30 19:13:20'),
(2, 4, 6, 'rfr', 'nurnove2005@gmail.com', '01727698790', 'refds', '1weak', 'Apartment', 'fvdcsxz', 'pending', NULL, NULL, NULL, 0, '2026-06-11 04:06:37', '2026-06-11 04:06:37');

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `shelter_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `owner_type` enum('shelter','user') NOT NULL,
  `category` enum('adoption','foster') NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('Dog','Cat','Bird','Rabbit','Other') DEFAULT 'Other',
  `age` varchar(50) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `activity_level` enum('Low','Medium','High') DEFAULT NULL,
  `health_status` varchar(100) DEFAULT NULL,
  `foster_duration` varchar(50) DEFAULT NULL,
  `status` enum('available','adopted','fostered') DEFAULT 'available',
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id`, `shelter_id`, `user_id`, `owner_type`, `category`, `name`, `type`, `age`, `gender`, `activity_level`, `health_status`, `foster_duration`, `status`, `description`, `image`, `is_deleted`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'shelter', 'adoption', 'minu', 'Cat', '2', 'Female', 'Medium', 'vaccinated', '', 'available', 'kjrefivjmlkmvfdlmclds', '1779816783_alvan-nee-ZCHj_2lJP00-unsplash.jpg', 1, '2026-05-30 19:09:09', '2026-05-26 17:33:03', '2026-05-30 19:09:09'),
(2, 1, NULL, 'shelter', 'foster', 'dsd', 'Dog', '2', 'Male', 'Low', 'vaccinated', '2weeks', 'available', 'dskcmklsdm', '1779817620_alvan-nee-brFsZ7qszSY-unsplash.jpg', 1, '2026-05-26 18:06:28', '2026-05-26 17:47:00', '2026-05-26 18:06:28'),
(3, 1, NULL, 'shelter', 'foster', 'jjbh', 'Dog', '2', 'Male', 'Low', 'vaccinated', '2weeks', 'available', 'jmlkmhgvhbnm', '1779819108_alec-favale-Ivzo69e18nk-unsplash.jpg', 0, NULL, '2026-05-26 18:11:48', '2026-05-26 18:11:48'),
(4, NULL, 1, 'user', 'foster', 'dfed', 'Dog', '2month', 'Male', 'Low', 'vaccinated', '2weeks', 'available', 'scdv cxz', '1780122248_zhang-kaiyv-2rUUGwMyXX0-unsplash.jpg', 1, '2026-06-11 03:58:39', '2026-05-30 06:24:08', '2026-06-11 03:58:39'),
(5, NULL, 2, 'user', 'foster', 'notun', 'Cat', '2month', 'Male', 'Low', 'vaccinated', '2weeks', 'available', 'sdfghjkl', '1781148828_loggawiggler-cat-61079_1920.jpg.jpeg', 0, NULL, '2026-06-11 03:33:48', '2026-06-11 03:33:48'),
(6, NULL, 1, 'user', 'foster', 'kk', 'Cat', '2month', 'Male', 'Low', 'vaccinated', '2weeks', 'available', 'sdfgvhbjnkm,l.', '1781149666_nennieinszweidrei-cat-4864605_1920.jpg.jpeg', 0, NULL, '2026-06-11 03:47:46', '2026-06-11 03:47:46');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `correct_answer` tinyint(1) NOT NULL,
  `display_order` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `question_text`, `correct_answer`, `display_order`, `is_active`) VALUES
(1, 'Do you have time for daily pet care?', 1, 1, 1),
(2, 'Can you afford pet expenses?', 1, 2, 1),
(3, 'Do you have safe living space?', 1, 3, 1),
(4, 'Do you have previous pet experience?', 1, 4, 1),
(5, 'Are you willing to adopt responsibly?', 1, 5, 1),
(6, 'Can you commit long-term?', 1, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shelters`
--

CREATE TABLE `shelters` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shelter_name` varchar(150) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shelters`
--

INSERT INTO `shelters` (`id`, `user_id`, `shelter_name`, `address`, `phone`, `created_at`, `updated_at`) VALUES
(1, 2, 'kichu ekta', 'dhaka', '01727493660', '2026-05-26 17:17:15', '2026-05-26 17:17:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','shelter','admin') DEFAULT 'user',
  `is_eligible` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_eligible`, `created_at`, `updated_at`) VALUES
(1, 'nur', 'nurnove2005@gmail.com', '$2y$10$WJDrPL8s2YWhTTZrDTuZr.cs59orOtsIvMS.2NJxi0sQg9Riwc6C6', 'user', 1, '2026-05-26 05:52:29', '2026-05-26 07:27:21'),
(2, 'hridoy', 'nurnove22875@gmail.com', '$2y$10$iRGu1/Cc5u2XKSKE6PxghenDVjjvBLHXBIyFggH8GzMUVyLxNYoOu', 'shelter', 0, '2026-05-26 07:10:16', '2026-05-26 17:15:40'),
(3, 'Admin', 'admin@gmail.com', '$2y$10$Zdp678Q5a00Nywtj4UV8dehcPD2bu5v4scX0zNIiosone4DpYZBt.', 'user', 1, '2026-05-26 20:26:46', '2026-05-26 20:27:41'),
(4, 'notun', 'new@gmail.com', '$2y$10$D5BHd8iDqMjvefuCd1U/5.RSemiQ5Fz4L0VuOJMZMiIjlOh.lyluS', 'user', 0, '2026-06-11 03:59:32', '2026-06-11 03:59:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adoption_applications`
--
ALTER TABLE `adoption_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`pet_id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `eligibility_answers`
--
ALTER TABLE `eligibility_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attempt_id` (`attempt_id`,`question_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `eligibility_attempts`
--
ALTER TABLE `eligibility_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `foster_applications`
--
ALTER TABLE `foster_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`pet_id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shelter_id` (`shelter_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shelters`
--
ALTER TABLE `shelters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adoption_applications`
--
ALTER TABLE `adoption_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `eligibility_answers`
--
ALTER TABLE `eligibility_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `eligibility_attempts`
--
ALTER TABLE `eligibility_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `foster_applications`
--
ALTER TABLE `foster_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shelters`
--
ALTER TABLE `shelters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adoption_applications`
--
ALTER TABLE `adoption_applications`
  ADD CONSTRAINT `adoption_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `adoption_applications_ibfk_2` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `adoption_applications_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `eligibility_answers`
--
ALTER TABLE `eligibility_answers`
  ADD CONSTRAINT `eligibility_answers_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `eligibility_attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `eligibility_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `eligibility_attempts`
--
ALTER TABLE `eligibility_attempts`
  ADD CONSTRAINT `eligibility_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `foster_applications`
--
ALTER TABLE `foster_applications`
  ADD CONSTRAINT `foster_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `foster_applications_ibfk_2` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `foster_applications_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`shelter_id`) REFERENCES `shelters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pets_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shelters`
--
ALTER TABLE `shelters`
  ADD CONSTRAINT `shelters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
