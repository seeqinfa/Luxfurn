-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 12, 2025 at 10:04 AM
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
-- Table structure for table `furnitures`
--

CREATE TABLE `furnitures` (
  `furnitureID` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `category` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  `price` varchar(10) NOT NULL,
  `stock_quantity` int(10) NOT NULL,
  `image_url` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `furnitures`
--

INSERT INTO `furnitures` (`furnitureID`, `name`, `category`, `description`, `price`, `stock_quantity`, `image_url`) VALUES
(1, 'Sofa Luxe', 'Sofa', '3-seater leather sofa in dark brown', '899.00', 5, '../../img/Sofa.jpg'),
(2, 'Oak Dining Set', 'Dining', '6-seater solid oak wood dining set', '1299.00', 3, '../../img/table.jpg'),
(3, 'Ergo Chair', 'Chair', 'Ergonomic office chair with lumbar support', '199.00', 12, '../../img/ergochair.jpg'),
(4, 'Coffee Table', 'Table', 'Minimalist glass coffee table', '159.00', 7, '../../img/coffeetable.jpg'),
(5, 'Queen Bed Frame', 'Bed', 'Queen size bed frame with storage drawers', '699.00', 4, '../../img/queenbedframe.jpg'),
(6, 'Bookshelf Classic', 'Shelf', '5-tier wooden bookshelf', '249.00', 9, '../../img/bookshelf.jpg'),
(7, 'TV Console', 'Storage', 'Low-rise TV console with cable management', '399.00', 6, '../../img/tvconsole.jpg'),
(8, 'Recliner Seat', 'Sofa', 'Plush recliner with adjustable headrest', '499.00', 2, '../../img/reclinerseat.jpg'),
(9, 'Study Desk', 'Table', 'Compact study desk with side drawers', '299.00', 10, '../../img/studydesk.jpg'),
(10, 'Bar Stool Set', 'Chair', 'Set of 2 bar stools with footrest', '179.00', 8, '../../img/barstool.jpg'),
(11, 'Bed', 'Bedroom', 'bed', '1299.90', 5, '../../img/bed.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `furnitures`
--
ALTER TABLE `furnitures`
  ADD PRIMARY KEY (`furnitureID`),
  ADD UNIQUE KEY `furnitureID` (`furnitureID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
