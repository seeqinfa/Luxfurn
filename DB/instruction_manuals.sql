-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2025 at 04:54 PM
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
-- Table structure for table `instruction_manuals`
--

CREATE TABLE `instruction_manuals` (
  `manualID` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `product_code` varchar(80) DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `manual_url` varchar(255) NOT NULL,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instruction_manuals`
--

INSERT INTO `instruction_manuals` (`manualID`, `product_name`, `product_code`, `keywords`, `manual_url`, `updated_at`) VALUES
(1, 'Ergo Chair', 'CHA-ERGO', 'chair ergonomic lumbar support office', '/assets/manuals/ergo_chair.pdf', '2025-08-11 00:17:22'),
(2, 'Coffee Table', 'TAB-COFF', 'table coffee glass minimalist living room', '/assets/manuals/coffee_table.pdf', '2025-08-11 00:17:22'),
(3, 'Queen Bed Frame', 'BED-QFRM', 'bed frame queen storage drawers bedroom', '/assets/manuals/queen_bed_frame.pdf', '2025-08-11 00:17:22'),
(4, 'Bookshelf Classic', 'SHE-BCLS', 'bookshelf shelf wooden 5-tier study', '/assets/manuals/bookshelf_classic.pdf', '2025-08-11 00:17:22'),
(5, 'TV Console', 'STO-TVCON', 'tv console storage low-rise cable management', '/assets/manuals/tv_console.pdf', '2025-08-11 00:17:22'),
(6, 'Recliner Seat', 'SOF-RECL', 'recliner sofa plush adjustable headrest', '/assets/manuals/recliner_seat.pdf', '2025-08-11 00:17:22'),
(7, 'Study Desk', 'TAB-STUD', 'study desk compact drawers office table', '/assets/manuals/study_desk.pdf', '2025-08-11 00:17:22'),
(8, 'Bar Stool Set', 'STL-BAR', 'bar stool set tall counter kitchen', '/assets/manuals/bar_stool_set.pdf', '2025-08-11 00:17:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `instruction_manuals`
--
ALTER TABLE `instruction_manuals`
  ADD PRIMARY KEY (`manualID`),
  ADD KEY `idx_product_name` (`product_name`),
  ADD KEY `idx_product_code` (`product_code`),
  ADD KEY `idx_updated_at` (`updated_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `instruction_manuals`
--
ALTER TABLE `instruction_manuals`
  MODIFY `manualID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
