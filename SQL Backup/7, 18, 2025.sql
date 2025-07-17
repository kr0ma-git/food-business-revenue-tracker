-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2025 at 06:04 PM
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
-- Database: `edds_revenue_tracker_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `customer_address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `full_name`, `contact_number`, `customer_address`) VALUES
(1, 'Test Customer', '09158593141', 'Cebu'),
(100, 'test', '09187384901', '123 fish st.'),
(101, 'tester', '09187384901', '123 fish st.'),
(102, 'testing', '123', '123');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `price`) VALUES
(1, 'Stuffed Egg', 20.00),
(2, 'Pork Chop', 60.00),
(3, 'Ngohiong', 14.00),
(4, 'Achara Tub', 45.00);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `payment_type` enum('Cash','GCash') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_deleted` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `customer_id`, `product_id`, `quantity`, `payment_type`, `created_at`, `is_deleted`) VALUES
(1, 1, 2, 2, 'Cash', '2025-07-17 21:50:58', 0),
(3, 1, 3, 12, 'Cash', '2025-07-17 22:21:42', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','cashier') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_disabled` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `role`, `created_at`, `is_disabled`) VALUES
(1, 'Edd', 'Admin1', 'testAdmin@gmail.com', '$2a$10$J3a5lAFkeKVsOMlRCHIzz.VkPIflvOhIwb6ze3RQTTQqcBjOKj0yy', 'admin', '2025-07-12 10:59:58', 0),
(2, 'User2', 'Test', 'testUser@gmail.com', '$2y$10$X2VobmlmhT068d0NkfgCOerSxB5rCMjWY1kSD5BnDt1OCUY7osnLa', 'cashier', '2025-07-13 12:56:28', 0),
(3, 'User3', 'Tester', 'testUser3@gmail.com', '$2y$10$MJPHf0bNxJBpXfYjfNy5seCxEBzPE/FZO1BhdiIH4HA/aRiefDmDO', 'admin', '2025-07-13 13:29:38', 0),
(4, 'test', 'tes', 'test@gmail.com', '$2y$10$plwXYddP4DlorQ5eQrZC8eZdNcOuULpQszhtx/FkTp05V8N3MMZnu', 'cashier', '2025-07-17 07:57:09', 0),
(5, 'test5', 'tester', 'testing5@gmail.com', '$2y$10$J222Tn2sp0gNz83l1Piu0eF9kjepEvNz45lZry21B6t4mUT6Uuj8.', 'cashier', '2025-07-17 07:57:41', 0),
(6, 'testing6', 'test', 'testemail6@gmail.com', '$2y$10$nlHFpJGVMsPiS5IKAQOUJuqWkyBbrEME09UOeOJu7rBMqzwFJkIxq', 'cashier', '2025-07-17 07:57:57', 0),
(7, 'test', 'tester', 'testUser55@gmail.com', '$2y$10$M3kv66wF.1QLNkKDJ46VC.yT1q391GxXXmWGnICT69rQq5SYhH75G', 'cashier', '2025-07-17 07:58:17', 0),
(8, 'tester7', 'testing', '123test@gmail.com', '$2y$10$sv32zu7VTbUEOSd1GyH.uOZ9n6z1CVFmF3sOthP.vuqBsFWzqN0Py', 'admin', '2025-07-17 07:58:55', 0),
(9, 'test9', 'tester', 'test9@gmail.com', '$2y$10$39s02tuzNjV9cG2OknIBw.xBJoEIXOwuYNruuRUssmBfNemxuEiJm', 'cashier', '2025-07-17 07:59:16', 0),
(10, 'User10', 'test10', 'test10@gmail.com', '$2y$10$sR/5rnbShv0MlI0137RnZ.pt8oQ4G/E/rUHyX0G/ayDW.YU7y5zMa', 'cashier', '2025-07-17 07:59:39', 0),
(11, 'test11', 'tester11', 'test11@gmail.com', '$2y$10$gaVyS1Xse1EEqRbykqf.a.eCmzVveQBREP5HHy7WOgVWXwSV.b6nO', 'cashier', '2025-07-17 08:00:14', 0),
(12, 'tester12', 'tester12', 'test12@gmail.com', '$2y$10$nwMVBj7FEkJT57aPFgfmH.Np3BX43iHgjrIUO/bt6STX4XJ/ezKR6', 'cashier', '2025-07-17 08:01:00', 0),
(13, 'test13', 'tester13', 'test13@gmail.com', '$2y$10$Zm6gUzuFMsjKdf1Bzyu/g.15BW3TkiFgn6CJ2SvY4zlu4sTth5wsW', 'cashier', '2025-07-17 08:01:18', 0),
(14, 'test14', 'tester14', 'test14@gmail.com', '$2y$10$.dDprW538Me6lp2qlg/6P.HHyhthzgupWNlkwr9Ma4nwaEP43mFF.', 'cashier', '2025-07-17 08:01:32', 0),
(15, 'test15', 'tester15', 'test15@gmail.com', '$2y$10$ED8YQ7peb.EB4FjsxGbB4eee0ZJOhuN6LeDuWC5d2CaXhbevU3i7K', 'cashier', '2025-07-17 08:01:46', 0),
(16, 'test16', 'tester16', 'test16@gmail.com', '$2y$10$211VeC9j1rgoLBPugkAlk.vZ7ILLuaO6XCylIzP7rxfF8NrWGGacS', 'cashier', '2025-07-17 08:01:59', 0),
(17, 'test17', 'tester17', 'test17@gmail.com', '$2y$10$2xZn4afhJDB95Uk8eVzR9uKubI225ubBxge./NMR7Rc9mQzKq8gyy', 'cashier', '2025-07-17 08:02:14', 0),
(18, 'test18', 'tester18', 'test18@gmail.com', '$2y$10$duF.dmJwbjGBeQB20G4bL.dMxPkaV9MZO1B86Awn2/UHAEdwwjIbS', 'cashier', '2025-07-17 08:02:28', 0),
(19, 'test19', 'tester19', 'test19@gmail.com', '$2y$10$QKkdJ1.nkwrHSVbT5Bz2r.caTbbKfOy1LQCmFx.mp8JJQxEAidxLq', 'cashier', '2025-07-17 08:02:38', 0),
(20, 'test20', 'tester20', 'test20@gmail.com', '$2y$10$fmRunSMulfnyOS6V.LqCT.3JbBoegDLFcqq.RPkk/nL1ZM8iMBdyG', 'cashier', '2025-07-17 08:02:59', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
