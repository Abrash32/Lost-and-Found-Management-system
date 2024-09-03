-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2024 at 11:18 PM
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
-- Database: `lost_found_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin@gmail.com', 'admin001', '2024-08-15 20:09:00'),
(2, 'abc@gmail.com', '$2y$10$bfD71EeznEnok6cAA9k/IO6dm1SKWPg4Bx2hlGcBcD5Zf3YAegnkq', '2024-08-15 21:10:37'),
(3, 'admin02@gmail.com', '1234', '2024-08-21 00:43:36');

-- --------------------------------------------------------

--
-- Table structure for table `item_claims`
--

CREATE TABLE `item_claims` (
  `id` int(11) NOT NULL,
  `lost_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `claim_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `item_picture` varchar(255) NOT NULL,
  `id_card` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `item_claims`
--

INSERT INTO `item_claims` (`id`, `lost_item_id`, `user_id`, `claim_date`, `status`, `admin_note`, `item_picture`, `id_card`) VALUES
(1, 1, 1, '2024-08-15 20:04:54', 'approved', NULL, './uploads/c9f27633d1dd2bbcc44fb3da4f5b70dc.png', NULL),
(2, 1, 1, '2024-08-15 20:35:22', 'pending', NULL, './uploads/c9f27633d1dd2bbcc44fb3da4f5b70dc.png', NULL),
(3, 2, 1, '2024-08-15 20:40:48', 'pending', NULL, './uploads/753c47f4be3bcb3338b74441015f886b.png', NULL),
(4, 1, 1, '2024-08-15 20:50:18', 'approved', NULL, './uploads/619c02370d4aba3c8d91d397482eab48.png', NULL),
(5, 3, 2, '2024-08-15 20:53:34', 'pending', NULL, './uploads/a353a78de0af6f6ed62bf8add99c1d23.jpg', NULL),
(6, 1, 2, '2024-08-15 21:44:10', 'pending', NULL, './uploads/432cc9523cb982bc93a7b3f9fea518c5.jpg', NULL),
(7, 1, 2, '2024-08-15 21:45:20', 'pending', NULL, './uploads/7d46c6d194f57019bea64dbf4cbe26f2.jpg', NULL),
(8, 5, 3, '2024-08-20 16:22:15', 'approved', NULL, '', NULL),
(9, 1, 3, '2024-08-20 17:33:24', 'pending', NULL, './uploads/56ab3da710d7d1d395e6c630f10c1e5b.png', NULL),
(10, 1, 3, '2024-08-20 17:33:40', 'pending', NULL, './uploads/2997cd0a0988328adb436c9528eacaf9.png', NULL),
(11, 1, 3, '2024-08-20 19:49:28', 'pending', NULL, './uploads/87859a18b10f10bb71ee0a9cd2469d13.png', NULL),
(26, 4, 3, '2024-08-24 16:45:11', 'pending', NULL, 'Screenshot (4).png', NULL),
(27, 5, 3, '2024-08-24 16:45:23', 'approved', NULL, 'images.jpeg', NULL),
(29, 5, 3, '2024-08-24 16:50:20', 'pending', NULL, 'images.jpeg', NULL),
(30, 5, 3, '2024-08-24 16:50:58', 'approved', NULL, 'images.jpeg', NULL),
(31, 4, 3, '2024-08-24 16:51:03', 'pending', NULL, 'Screenshot (4).png', NULL),
(36, 3, 3, '2024-08-24 18:17:56', 'pending', NULL, '', NULL),
(37, 3, 3, '2024-08-24 18:18:15', 'pending', NULL, '', NULL),
(38, 3, 3, '2024-08-24 18:26:47', 'pending', NULL, '', NULL),
(42, 5, 3, '2024-08-24 21:11:44', 'pending', NULL, '689ecf195dea89a9303e1464e6af4831.jpg', NULL),
(43, 5, 3, '2024-08-24 21:13:01', 'pending', NULL, 'f0c801ca59db707b6e2901e3f01e7178.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lost_items`
--

CREATE TABLE `lost_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `item_description` text NOT NULL,
  `lost_date` date NOT NULL,
  `location` varchar(255) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `item_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `lost_items`
--

INSERT INTO `lost_items` (`id`, `user_id`, `item_name`, `item_description`, `lost_date`, `location`, `contact`, `item_image`, `created_at`) VALUES
(1, 1, 'Book', 'PHY Text Book', '2024-08-07', 'At FCLT II', '09162542641', 'desktop_image.png', '2024-08-15 19:31:37'),
(2, 1, 'Key', 'WIth an FUK Holder', '2024-08-27', 'At DLC', '0916250000', 'gender.png', '2024-08-15 19:45:33'),
(3, 1, 'Shoe', 'Back Shoe', '2024-08-27', 'At Library', '0916250000', 'OIP (1).jpg', '2024-08-15 19:47:07'),
(4, 1, 'Calculator', 'Scientific Caculator', '2024-08-20', 'At Exam Hall In New FCLT', '09111101010', 'comparing F & N.png', '2024-08-15 19:48:07'),
(5, 3, 'laptop', 'laptop572572', '2024-08-19', 'abuja', '46628286', '13-y.jpg', '2024-08-20 16:00:53');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `item_description` text NOT NULL,
  `lost_date` date NOT NULL,
  `location` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `item_image` varchar(255) DEFAULT NULL,
  `report_status` enum('pending','reviewed','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`id`, `user_id`, `item_name`, `item_description`, `lost_date`, `location`, `contact`, `item_image`, `report_status`, `created_at`) VALUES
(3, 3, 'laptop', 'laptop hp144', '2024-08-19', 'lagos', '5673772', 'Screenshot (1).png', 'resolved', '2024-08-20 15:32:06'),
(4, 3, 'food', 'foodflask big', '2024-08-20', 'oyo', '09022773757', 'Screenshot (4).png', 'resolved', '2024-08-20 15:35:58'),
(5, 3, 'phone ', 'item p33', '2024-08-18', 'gombe', '77226264', 'images.jpeg', 'reviewed', '2024-08-20 16:21:52'),
(6, 3, 'lol', 'lol', '2024-08-19', 'baba', '525572257', 'grunge-red-lost-and-found-word-rubber-seal-stamp-on-wthie-background-2RDN6XA (1).jpg', 'reviewed', '2024-08-20 17:20:20'),
(7, 3, 'pillow', 'pillow case', '2024-08-12', 'tnfure', '3747277', 'download.jpeg', 'resolved', '2024-08-20 23:13:30'),
(8, 1, 'gun', 'ak47', '2024-08-21', 'station', '5673772', 'Image.png', 'pending', '2024-08-20 23:54:58'),
(9, 2, 'human', 'a tall girl', '2024-12-08', 'gate 3', '562721819', 'png-clipart-thumbnail-use-case-diagram-actor-angle-furniture-thumbnail.png', 'resolved', '2024-08-24 16:20:54'),
(10, 3, 'tablet', 'an tcl tablet', '2024-10-07', 'kashere', '2682336882', 'png-clipart-thumbnail-use-case-diagram-actor-angle-furniture-thumbnail.png', 'reviewed', '2024-08-24 18:09:01'),
(11, 3, 'joshua', 'a short boy', '2024-12-08', 'lagos', '8313831', 'png-clipart-thumbnail-use-case-diagram-actor-angle-furniture-thumbnail.png', 'resolved', '2024-08-24 18:30:50');

-- --------------------------------------------------------

--
-- Table structure for table `user_reg`
--

CREATE TABLE `user_reg` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `department` varchar(100) NOT NULL,
  `matric` varchar(20) NOT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_reg`
--

INSERT INTO `user_reg` (`id`, `name`, `username`, `password`, `email`, `phone`, `department`, `matric`, `profile_photo`, `created_at`) VALUES
(1, 'xyz', 'xyz@gmail.com', '1234', 'xyz@gmail.com', '090111', 'Computer Science', 'CSC/2024/12345', 'fraud days.png', '2024-08-15 18:02:51'),
(2, 'abc', 'abc@gmail.com', '1234', 'abc@gmail.com', '09099998', 'Computer Science', 'CSC/2024/0222', 'uploads/Rashhh.jpg', '2024-08-15 20:51:52'),
(3, 'DSC_6108.JPG', 'faith', 'user', 'faith@gmail.com', '123577', 'accounting', 'fuku/sci/19/acc/0234', 'uploads/DSC_6108.JPG', '2024-08-20 12:50:09'),
(4, 'ss.jpg', 'Gabriel', '1234', 'g@gmail.com', '09087246262', 'Computer science', 'fuku/sci/19/com/0114', 'uploads/ss.jpg', '2024-08-20 13:00:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `item_claims`
--
ALTER TABLE `item_claims`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lost_item_id` (`lost_item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lost_items`
--
ALTER TABLE `lost_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_reg`
--
ALTER TABLE `user_reg`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `item_claims`
--
ALTER TABLE `item_claims`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `lost_items`
--
ALTER TABLE `lost_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_reg`
--
ALTER TABLE `user_reg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `item_claims`
--
ALTER TABLE `item_claims`
  ADD CONSTRAINT `item_claims_ibfk_1` FOREIGN KEY (`lost_item_id`) REFERENCES `lost_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_claims_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user_reg` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lost_items`
--
ALTER TABLE `lost_items`
  ADD CONSTRAINT `lost_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_reg` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_reg` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
