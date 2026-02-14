-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 01, 2025 at 08:33 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `learnify`
--

-- --------------------------------------------------------

--
-- Table structure for table `boards`
--

CREATE TABLE `boards` (
  `id` int(11) NOT NULL,
  `board_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `boards`
--

INSERT INTO `boards` (`id`, `board_name`) VALUES
(1, 'Sindh'),
(2, 'Punjab'),
(3, 'KPK'),
(4, 'Federal'),
(5, 'Balochistan');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`) VALUES
(7, '9'),
(8, '10'),
(9, '11'),
(10, '12');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` int(11) NOT NULL,
  `district_name` varchar(100) NOT NULL,
  `board` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `district_name`, `board`) VALUES
(1, 'Lahore', 'Punjab'),
(3, 'Nawabshah', 'Sindh'),
(4, 'Hyderabad', 'Sindh'),
(5, 'Karachi', 'Sindh'),
(6, 'Queeta', 'Balochistan');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `class_id`) VALUES
(7, 'English', 7),
(8, 'Chemistry', 8),
(9, 'Mathematics', 7),
(11, 'Computer', 9);

-- --------------------------------------------------------

--
-- Table structure for table `subject_details`
--

CREATE TABLE `subject_details` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `district_id` int(11) DEFAULT NULL,
  `heading` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `type` enum('notes','books','video','past_paper','guess_paper') NOT NULL,
  `front_image` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `video_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject_details`
--

INSERT INTO `subject_details` (`id`, `class_id`, `subject_id`, `district_id`, `heading`, `description`, `keywords`, `type`, `front_image`, `file`, `video_link`) VALUES
(1, 8, 8, NULL, 'Essay Writing ', 'This is English Lecture', 'English, Lecture, ', '', 'img_6886115b4df17.jpg', NULL, 'https://youtu.be/E2SFJu8MMjo?si=2eLHOzLNW2XYUk0e'),
(2, 7, 9, NULL, 'Essay Writing ', 'hello', 'English, Lecture, ', '', 'img_6886123286586.jpg', NULL, 'https://youtu.be/E2SFJu8MMjo?si=2eLHOzLNW2XYUk0e'),
(3, 7, 7, NULL, 'Essay Writing ', 'this is english', 'English, Lecture, ', 'books', 'img_688612c76422d.jpg', 'file_688612c76456f.pdf', NULL),
(4, 8, 8, 4, 'Essay Writing ', 'chemistry', 'PastPaper,Chemistry', 'past_paper', 'img_688619c26d3fe.jpg', NULL, NULL),
(5, 8, 8, NULL, 'punjab', 'chemistry', ' mkmkkkkl', '', 'books', 'img_68894d3837708.jpg', 'file_68894d3838c64.docx'),
(6, 7, 7, NULL, 'punjab', 'chemistry', ' mkmkkkkl', '', 'books', NULL, NULL),
(7, 7, 7, NULL, 'chemistry', 'nkkkllk', 'English', 'notes', 'img_68894e0db8703.jpg', 'file_68894e0db8b84.docx', NULL),
(8, 7, 7, 4, 'english', 'it in english notes', 'English', 'past_paper', 'img_688952aa58b1b.jpg', NULL, NULL),
(9, 9, 11, NULL, 'Computer science', 'jjbc npo d', 'webdevelopment', 'video', 'img_68895341003a9.jpg', NULL, 'http://localhost/Projects/Learnify-Dahboard/includes/views/Subject/addSubjectDetails.php'),
(10, 9, 11, 6, 'Computer science', 'mdaaaaaaaa', 'webdevelopment', 'past_paper', 'img_6889538d19b05.jpg', NULL, NULL),
(11, 7, 7, 4, 'Computer science', 'ddddddddddddddd', 'webdevelopment', 'past_paper', 'img_6889547aecd6a.jpg', NULL, NULL),
(12, 8, 8, 1, 'Computer science', 'yyyyyyyyyy', 'webdevelopment', 'past_paper', 'img_6889572b671d1.jpg', NULL, NULL),
(13, 7, 7, NULL, 'Computer science', 'uuuuuuuhhhhhhhhhh999999999999gbbbbbbbb', 'webdevelopment', 'notes', 'img_688b68e39d862.png', 'file_688b68e39e809.pdf', NULL),
(14, 8, 8, 6, 'Computer science', 'bhfhkj', 'webdevelopment', 'past_paper', 'img_688c58c3e20ca.png', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `boards`
--
ALTER TABLE `boards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `subject_details`
--
ALTER TABLE `subject_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `district_id` (`district_id`);

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
-- AUTO_INCREMENT for table `boards`
--
ALTER TABLE `boards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `subject_details`
--
ALTER TABLE `subject_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subject_details`
--
ALTER TABLE `subject_details`
  ADD CONSTRAINT `subject_details_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_details_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subject_details_ibfk_4` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
