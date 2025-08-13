-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2025 at 05:15 PM
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
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_admin_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('open','responded','resolved') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `user_id`, `assigned_admin_id`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 1, NULL, 'Login issues', 'I cannot login to my account since yesterday', 'open', '2025-05-15 01:23:45'),
(2, 2, NULL, 'Order not received', 'I placed order #12345 5 days ago but still not received', 'open', '2025-05-16 06:12:33'),
(3, 3, NULL, 'Payment problem', 'Payment was deducted but order not confirmed', 'open', '2025-05-17 02:45:12'),
(4, 4, NULL, 'Product damaged', 'Received damaged product in order #12346', 'resolved', '2025-05-10 08:30:22'),
(5, 5, NULL, 'Account verification', 'Need help verifying my account', 'open', '2025-05-18 03:05:17'),
(6, 6, NULL, 'Refund request', 'Requesting refund for cancelled order #12347', 'open', '2025-05-19 05:22:08'),
(7, 7, NULL, 'Password reset', 'Cannot reset my password, email not received', 'open', '2025-05-20 01:15:42'),
(8, 8, NULL, 'Shipping address change', 'Need to change shipping address for order #12348', 'resolved', '2025-05-12 09:40:35'),
(9, 9, NULL, 'Wrong item received', 'Received wrong item in order #12349', 'responded', '2025-05-21 07:33:19'),
(10, 10, NULL, 'Membership upgrade', 'Having trouble upgrading my membership', 'responded', '2025-05-22 04:10:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
