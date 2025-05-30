-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2025 at 04:35 AM
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
-- Database: `tindahan_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` tinyint(1) NOT NULL,
  `buyer` varchar(50) NOT NULL,
  `total_amount` float(10,2) NOT NULL,
  `payment_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`id`, `date`, `status`, `buyer`, `total_amount`, `payment_type`) VALUES
(20, '2025-05-30', 1, 'Ako', 20.00, 'Cash'),
(21, '2025-05-30', 0, 'asda', 30.00, 'GCash');

-- --------------------------------------------------------

--
-- Table structure for table `invoiceitem_invoice`
--

CREATE TABLE `invoiceitem_invoice` (
  `invoice_item_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoiceitem_invoice`
--

INSERT INTO `invoiceitem_invoice` (`invoice_item_id`, `invoice_id`) VALUES
(27, 20),
(28, 21);

-- --------------------------------------------------------

--
-- Table structure for table `invoice_item`
--

CREATE TABLE `invoice_item` (
  `id` int(11) NOT NULL,
  `subtotal` float(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_item`
--

INSERT INTO `invoice_item` (`id`, `subtotal`, `quantity`, `status`) VALUES
(26, 10.00, 1, 0),
(27, 20.00, 2, 1),
(28, 30.00, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `tindahan_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `quantity_sold` int(11) NOT NULL,
  `unit_price` float(10,2) NOT NULL,
  `source` varchar(50) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`tindahan_id`, `name`, `quantity_sold`, `unit_price`, `source`, `unit`, `product_id`) VALUES
(36, 'Christian Jave Hulleza', 3, 15.00, '1', 'a', 2),
(36, 'Frotus', 0, 10.00, '1', '1', 3);

-- --------------------------------------------------------

--
-- Table structure for table `product_invoiceitem`
--

CREATE TABLE `product_invoiceitem` (
  `product_id` int(11) NOT NULL,
  `invoice_item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_invoiceitem`
--

INSERT INTO `product_invoiceitem` (`product_id`, `invoice_item_id`) VALUES
(2, 26),
(3, 27),
(2, 28);

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
(23, 'Christian Jave Hulleza', 0, 1.00, 'a', '1'),
(24, 'Frotus', 8, 5.00, '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tindahan`
--

CREATE TABLE `tindahan` (
  `name` varchar(50) NOT NULL,
  `address` varchar(100) NOT NULL,
  `id` int(11) NOT NULL,
  `revenue` float(10,2) DEFAULT NULL,
  `expense` float(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tindahan`
--

INSERT INTO `tindahan` (`name`, `address`, `id`, `revenue`, `expense`) VALUES
('Christian Jave Hulleza', 'a', 36, 20.00, 17.00);

-- --------------------------------------------------------

--
-- Table structure for table `tindahan_invoice`
--

CREATE TABLE `tindahan_invoice` (
  `invoice_id` int(11) DEFAULT NULL,
  `tindahan_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tindahan_invoice`
--

INSERT INTO `tindahan_invoice` (`invoice_id`, `tindahan_id`) VALUES
(20, 36),
(21, 36);

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
('cshulleza@up.edu.ph', 'hulleza', 'Christian Jave');

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
('cshulleza@up.edu.ph', 23),
('cshulleza@up.edu.ph', 24);

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
(36, 'cshulleza@up.edu.ph');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoiceitem_invoice`
--
ALTER TABLE `invoiceitem_invoice`
  ADD KEY `invoice_item_id` (`invoice_item_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `invoice_item`
--
ALTER TABLE `invoice_item`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_tindahan_id` (`tindahan_id`);

--
-- Indexes for table `product_invoiceitem`
--
ALTER TABLE `product_invoiceitem`
  ADD KEY `product_id` (`product_id`),
  ADD KEY `invoice_item_id` (`invoice_item_id`);

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
-- Indexes for table `tindahan_invoice`
--
ALTER TABLE `tindahan_invoice`
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `tindahan_id` (`tindahan_id`);

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
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `invoice_item`
--
ALTER TABLE `invoice_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tindahan`
--
ALTER TABLE `tindahan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoiceitem_invoice`
--
ALTER TABLE `invoiceitem_invoice`
  ADD CONSTRAINT `invoiceitem_invoice_ibfk_1` FOREIGN KEY (`invoice_item_id`) REFERENCES `invoice_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoiceitem_invoice_ibfk_2` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_tindahan_id` FOREIGN KEY (`tindahan_id`) REFERENCES `tindahan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_invoiceitem`
--
ALTER TABLE `product_invoiceitem`
  ADD CONSTRAINT `product_invoiceitem_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_invoiceitem_ibfk_2` FOREIGN KEY (`invoice_item_id`) REFERENCES `invoice_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tindahan_invoice`
--
ALTER TABLE `tindahan_invoice`
  ADD CONSTRAINT `tindahan_invoice_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tindahan_invoice_ibfk_2` FOREIGN KEY (`tindahan_id`) REFERENCES `tindahan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
