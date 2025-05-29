-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2025 at 04:08 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tindahan_system_1`
--

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `tindahan_id` int(11) DEFAULT NULL,
  `stock_id` int(11) DEFAULT NULL,
  `quantity_sold` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`tindahan_id`, `stock_id`, `quantity_sold`, `unit_price`) VALUES
(17, 10, 10, 13.50),
(19, 14, 10, 15.00),
(19, 11, 1, 1.00),
(19, 15, 1, 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `unit` varchar(50) NOT NULL,
  `source` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`id`, `name`, `quantity`, `price`, `unit`, `source`) VALUES
(10, 'Stock 2', 10, 1.00, '1', '1'),
(11, 'Jave', 100, 10.50, 'cm', 'Me'),
(14, 'Stock 1', 100, 10.00, 'unit', 'source 2'),
(15, 'Stock 2', 3, 1.00, '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tindahan`
--

CREATE TABLE `tindahan` (
  `name` varchar(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tindahan`
--

INSERT INTO `tindahan` (`name`, `address`, `id`) VALUES
('Store 1', 'Address', 17),
('Store 3', '', 19),
('Store 1', 'Store 3', 21),
('Store 2', '', 28);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`email`, `password`, `username`) VALUES
('a@email.com', 'password', 'Username'),
('a@up.edu.ph', 'hulleza04', 'A'),
('cshulleza@up.edu.ph', 'hulleza', 'Hello'),
('hello@gmail.com', 'hello1', 'Christian Jave'),
('jave@up.edu.ph', 'hulleza', 'Christian Jave');

-- --------------------------------------------------------

--
-- Table structure for table `user_stock`
--

CREATE TABLE `user_stock` (
  `email` varchar(50) DEFAULT NULL,
  `stock_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_stock`
--

INSERT INTO `user_stock` (`email`, `stock_id`) VALUES
('jave@up.edu.ph', 10),
('cshulleza@up.edu.ph', 11),
('cshulleza@up.edu.ph', 14),
('cshulleza@up.edu.ph', 15);

-- --------------------------------------------------------

--
-- Table structure for table `user_tindahan`
--

CREATE TABLE `user_tindahan` (
  `tindahan_id` int(11) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_tindahan`
--

INSERT INTO `user_tindahan` (`tindahan_id`, `email`) VALUES
(17, 'jave@up.edu.ph'),
(19, 'cshulleza@up.edu.ph'),
(21, 'a@email.com'),
(28, 'a@email.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD KEY `tindahan_id` (`tindahan_id`),
  ADD KEY `product_id` (`stock_id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`,`name`);

--
-- Indexes for table `tindahan`
--
ALTER TABLE `tindahan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`email`,`password`);

--
-- Indexes for table `user_stock`
--
ALTER TABLE `user_stock`
  ADD KEY `email` (`email`),
  ADD KEY `stock_id` (`stock_id`);

--
-- Indexes for table `user_tindahan`
--
ALTER TABLE `user_tindahan`
  ADD KEY `tindahan_id` (`tindahan_id`),
  ADD KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tindahan`
--
ALTER TABLE `tindahan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`tindahan_id`) REFERENCES `tindahan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`stock_id`) REFERENCES `stock` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_stock`
--
ALTER TABLE `user_stock`
  ADD CONSTRAINT `user_stock_ibfk_1` FOREIGN KEY (`email`) REFERENCES `user` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_stock_ibfk_2` FOREIGN KEY (`stock_id`) REFERENCES `stock` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_tindahan`
--
ALTER TABLE `user_tindahan`
  ADD CONSTRAINT `user_tindahan_ibfk_1` FOREIGN KEY (`tindahan_id`) REFERENCES `tindahan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_tindahan_ibfk_2` FOREIGN KEY (`email`) REFERENCES `user` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
