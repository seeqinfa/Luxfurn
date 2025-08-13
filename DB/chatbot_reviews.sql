-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 08, 2025 at 02:48 PM
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
-- Database: `luxfurn`
--

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_reviews`
--

CREATE TABLE `chatbot_reviews` (
  `reviewID` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text NOT NULL,
  `admin_comment` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chatbot_reviews`
--

INSERT INTO `chatbot_reviews` (`reviewID`, `user_id`, `rating`, `comment`, `admin_comment`, `created_at`) VALUES
(1, 1, 5, 'Very helpful chatbot! It answered my questions instantly.', 'Glad this user had a great experience.', '2025-08-01 10:15:00'),
(2, 2, 4, 'Pretty good, but it gave me the wrong info once.', 'Review FAQ on shipping.', '2025-08-02 14:30:00'),
(3, 6, 3, 'It works, but sometimes repeats the same thing.', 'Improve context handling.', '2025-08-03 09:20:00'),
(4, 8, 3, 'Fast replies but not always relevant.', 'Intent tuning needed.', '2025-08-04 17:45:00'),
(5, 9, 2, 'Could not help me with my order issue.', 'Escalate order queries sooner.', '2025-08-05 11:10:00'),
(6, 10, 1, 'Gave wrong answers every time.', 'Check KB integration.', '2025-08-06 08:50:00'),
(7, 1, 4, 'Easy to use and quick.', 'test3', '2025-08-07 13:00:00'),
(8, 2, 5, 'Saved me time finding a product.', 'test2', '2025-08-07 19:25:00'),
(9, 6, 2, 'Doesn’t understand certain phrases.', 'Add training examples.', '2025-08-08 09:15:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chatbot_reviews`
--
ALTER TABLE `chatbot_reviews`
  ADD PRIMARY KEY (`reviewID`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chatbot_reviews`
--
ALTER TABLE `chatbot_reviews`
  MODIFY `reviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chatbot_reviews`
--
ALTER TABLE `chatbot_reviews`
  ADD CONSTRAINT `fk_cb_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
