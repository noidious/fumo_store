-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2025 at 01:47 AM
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
-- Database: `fumo_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','completed','abandoned') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `user_id`, `session_id`, `created_at`, `updated_at`, `status`) VALUES
(1, 1, NULL, '2024-11-15 06:44:42', '2024-11-16 16:39:17', 'active'),
(2, 1, NULL, '2024-11-15 06:47:04', '2024-11-16 16:39:17', 'active'),
(3, 1, NULL, '2024-11-15 06:47:14', '2024-11-16 16:39:17', 'active'),
(4, 1, NULL, '2024-11-15 06:48:29', '2024-11-16 16:39:17', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `price`) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `town` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `fname`, `lname`, `title`, `address`, `town`, `zipcode`, `phone`, `user_id`, `profile_image`) VALUES
(10, 'User', 'Guest', '', '', '', '', '', 17, 'uploads/17_profile_691bc0c9e6e6f.jpg'),
(11, 'dion', 'ongaria', '', 'ph', 'ph', '1234', '1234567891011', 16, NULL),
(12, 'fuo', 'support', 'admin', 'support@fumostore.com', 'fumo town', '1234', '09696967454', 18, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `item_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `sell_price` decimal(10,2) NOT NULL,
  `img_path` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orderinfo`
--

CREATE TABLE `orderinfo` (
  `orderinfo_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `date_placed` datetime NOT NULL,
  `date_shipped` datetime DEFAULT current_timestamp(),
  `shipping` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderinfo`
--

INSERT INTO `orderinfo` (`orderinfo_id`, `customer_id`, `date_placed`, `date_shipped`, `shipping`) VALUES
(19, 1, '2024-11-17 17:18:54', '2024-11-17 17:18:54', 10.00),
(20, 1, '2024-11-17 17:27:48', '2024-11-17 17:27:48', 10.00),
(21, 1, '2024-11-17 17:35:49', '2024-11-17 17:35:49', 10.00),
(22, 1, '2024-11-17 18:09:08', '2024-11-17 18:09:08', 10.00),
(23, 1, '2024-11-17 18:11:41', '2024-11-17 18:11:41', 10.00),
(24, 1, '2024-11-18 17:54:49', '2024-11-18 17:54:49', 10.00),
(25, 1, '2024-11-19 10:33:37', '2024-11-19 10:33:37', 10.00),
(26, 3, '2024-11-19 23:20:39', '2024-11-19 23:20:39', 10.00),
(27, 4, '2024-11-20 00:00:59', '2024-11-20 00:00:59', 10.00),
(28, 5, '2024-11-21 17:49:42', '2024-11-21 17:49:42', 10.00),
(29, 1, '2024-11-24 11:33:13', '2024-11-24 11:33:13', 10.00),
(30, 6, '2024-11-25 01:08:02', '2024-11-25 01:08:02', 10.00),
(31, 7, '2024-11-25 11:53:20', '2024-11-25 11:53:20', 10.00),
(32, 8, '2024-11-25 14:30:05', '2024-11-25 14:30:05', 10.00),
(33, 9, '2025-09-08 16:10:57', '2025-09-08 16:10:57', 10.00),
(34, 10, '2025-11-18 03:52:37', '2025-11-18 03:52:37', 10.00),
(35, 10, '2025-11-18 08:42:54', '2025-11-18 08:42:54', 10.00),
(36, 10, '2025-11-18 08:43:40', '2025-11-18 08:43:40', 10.00),
(37, 10, '2025-11-18 15:12:08', '2025-11-18 15:12:08', 10.00),
(38, 10, '2025-11-18 15:14:04', '2025-11-18 15:14:04', 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `orderline`
--

CREATE TABLE `orderline` (
  `orderline_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `orderinfo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderline`
--

INSERT INTO `orderline` (`orderline_id`, `order_id`, `product_id`, `quantity`, `price`, `orderinfo_id`) VALUES
(2, 1, 1, 2, 0.00, NULL),
(3, 1, 11, 1, 0.00, NULL),
(4, 2, 1, 1, 0.00, NULL),
(5, 3, 10, 1, 0.00, NULL),
(6, 3, 4, 2, 0.00, NULL),
(7, 4, 2, 1, 0.00, NULL),
(8, 6, 1, 1, 0.00, NULL),
(9, 7, 1, 1, 0.00, NULL),
(10, 8, 3, 1, 0.00, NULL),
(11, 15, 1, 1, 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `shipping` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `total`, `order_date`, `status`, `customer_id`, `shipping`) VALUES
(36, 610.00, '2025-11-18 00:42:54', 'Delivered', 10, 10.00),
(37, 310.00, '2025-11-18 00:43:40', 'Delivered', 10, 10.00),
(38, 300.00, '2025-11-18 07:12:08', 'Delivered', 10, 10.00),
(39, 310.00, '2025-11-18 07:14:04', 'pending', 10, 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_detail_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(20, 36, 15, 1, 300.00),
(21, 36, 16, 1, 300.00),
(22, 37, 15, 1, 300.00),
(23, 38, 15, 1, 300.00),
(24, 39, 15, 1, 300.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `stock`, `image`) VALUES
(15, 'Cirno ', 'The Cirno Fumo plush is a soft, chibi-style collectible that captures the playful ice fairy from Touhou Project in adorable detail, complete with her blue dress, ice wings, and signature bow.', 1100.00, 36, '../item/images/691b89458bea5_cirno-prod.jpg'),
(16, 'Reimu', 'A cuddly companion made for you!', 300.00, 39, '../item/images/691b8a498e9b2_reimu-prod.jpg'),
(17, 'Sanae', 'A cuddly companion made for you!', 300.00, 40, '../item/images/691b8ae83a941_sanae-prod.jpg'),
(18, 'Sakuya', 'A cuddly companion made for you!', 300.00, 40, '../item/images/1763413277_sakuya-prod1.jpg'),
(19, 'Youmu', 'A cuddly companion made for you!', 300.00, 40, '../item/images/691b8d72d2786_youmu-prod.jpeg'),
(20, 'Marisa Kirisame', 'The Marisa Kirisame Fumo plush is a soft, chibi-style collectible featuring the iconic Touhou magician in her signature outfit, complete with her black witch hat and golden braids.', 1000.00, 46, '../item/images/691d60f984a68_marisa-prod.jpg'),
(21, 'Reisen', 'The Reisen Udongein Inaba Fumo plush is a soft, collectible plushie that captures the moon rabbit’s cool demeanor and iconic look from the Touhou Project series.', 720.00, 42, '../item/images/691d67438b378_reisen_prod.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `ID` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`ID`, `product_id`, `image`) VALUES
(31, 15, '../item/images/691b8945d7eac_cirno-prod2.jpg'),
(32, 15, '../item/images/1763412308_cirno-prod1.jpg'),
(33, 15, '../item/images/1763412333_cirno-prod1.jpg'),
(34, 16, '../item/images/691b8a4a1bba1_reimu-prod2.jpg'),
(35, 16, '../item/images/1763412606_reimu-prod1.jpg'),
(36, 16, '../item/images/1763412647_reimu-prod3.jpg'),
(37, 17, '../item/images/1763412725_sanae-prod2.jpeg'),
(38, 17, '../item/images/1763412736_sanae-prod3.jpg'),
(39, 18, '../item/images/1763413237_sakuya-prod1.jpg'),
(40, 18, '../item/images/1763413248_sakuya-prod2.jpg'),
(41, 19, '../item/images/691b8d72d57a8_youmu-prod2.jpg'),
(42, 20, '../item/images/691d60f986c37_marisa_prod1.jpg'),
(43, 20, '../item/images/691d60f987ff1_marisa_prod2.jpg'),
(44, 20, '../item/images/691d60f989bca_marisa_prod3.jpg'),
(45, 20, '../item/images/691d60f98b1d9_marisa_prod4.jpg'),
(46, 20, '../item/images/691d60f98c5f5_marisa_prod5.jpg'),
(47, 15, '../item/images/1763533340_fumo-banner-right-image-04.jpg'),
(48, 21, '../item/images/691d67438ce10_reisen_prod.jpg'),
(49, 21, '../item/images/691d67438e877_reisen_prod1.jpg'),
(50, 21, '../item/images/691d6743905ce_reisen_prod2.jpg'),
(51, 21, '../item/images/691d674391d7f_reisen_prod3.jpg'),
(52, 21, '../item/images/691d674393cc9_reisen_prod4.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `review_content` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`review_id`, `product_id`, `customer_id`, `review_content`, `rating`, `review_date`) VALUES
(1, 2, 1, 'nice smell', 5, '2024-11-19 09:58:50'),
(2, 5, 1, 'good product but the delivery is **** ****', 1, '2024-11-19 10:05:25'),
(3, 4, 1, 'very demure, ***** *****', 3, '2024-11-19 10:34:28'),
(14, 9, 4, 'hahahahaha yoko', 1, '2024-11-21 09:04:27'),
(15, 4, 7, '***** so perfect', 1, '2024-11-25 14:33:27'),
(17, 16, 10, 'Fumo', 5, '2025-11-18 08:46:39');

-- --------------------------------------------------------

--
-- Stand-in structure for view `salesperorder`
-- (See below for the actual view)
--
CREATE TABLE `salesperorder` (
`orderId` int(11)
,`total` decimal(42,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `stock_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'user',
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role`, `last_login`) VALUES
(14, 'dion@ongaria', '$2y$10$posSGXAc49wj7RXqDn8Z0Ob/swwDVWK96Owyd3bULBuhtKdW.6BWS', 'admin', '2025-11-19 13:56:59'),
(16, 'dionongaria@gmail.com', '$2y$10$JcXOyMOKV2bfLBYJNe8i0OGZPDzWcze7sf7RAs8A0ta/oB01GQeN6', 'user', '2025-11-18 05:10:12'),
(17, 'andreijosef8@gmail.com', '$2y$10$TesWcwlUvBy7YY2wwUk6Q.lUPfQsnATFG33GsBpOEUAvs5WK.E7Su', 'user', '2025-11-18 15:13:48'),
(18, 'support@fumostore.com', '$2y$10$z2dpcH1LdPpOyMzyIkY6rupRs5ahxC/jkU3v9CCM/BpacC708CycG', 'admin', '2025-11-18 09:22:11');

-- --------------------------------------------------------

--
-- Structure for view `salesperorder`
--
DROP TABLE IF EXISTS `salesperorder`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `salesperorder`  AS SELECT `od`.`order_id` AS `orderId`, sum(`p`.`price` * `od`.`quantity`) AS `total` FROM ((`orders` `o` join `order_details` `od` on(`o`.`order_id` = `od`.`order_id`)) join `products` `p` on(`od`.`product_id` = `p`.`product_id`)) GROUP BY `od`.`order_id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `orderinfo`
--
ALTER TABLE `orderinfo`
  ADD PRIMARY KEY (`orderinfo_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `orderline`
--
ALTER TABLE `orderline`
  ADD PRIMARY KEY (`orderline_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_orderinfo_id` (`orderinfo_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`stock_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `fk_product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orderinfo`
--
ALTER TABLE `orderinfo`
  MODIFY `orderinfo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `orderline`
--
ALTER TABLE `orderline`
  MODIFY `orderline_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
