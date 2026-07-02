-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2026 at 04:15 PM
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
-- Database: `laundry_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `full_name`, `phone`, `email`, `address`, `created_at`) VALUES
(1, 'Roberto Tan', '09171234501', 'roberto@email.com', '123 Main St, Barangay 1', '2026-07-01 12:30:28'),
(2, 'Liza Cruz', '09171234502', 'liza@email.com', '45 Rizal Ave, Barangay 2', '2026-07-01 12:30:28'),
(3, 'Miguel Reyes', '09171234503', 'miguel@email.com', '78 Mabini St, Barangay 3', '2026-07-01 12:30:28'),
(4, 'Sofia Lim', '09171234504', 'sofia@email.com', '12 Bonifacio St, Barangay 1', '2026-07-01 12:30:28'),
(5, 'Diego Garcia', '09171234505', 'diego@email.com', '34 Aguinaldo St, Barangay 4', '2026-07-01 12:30:28'),
(6, 'Isabella Santos', '09171234506', 'isabella@email.com', '56 Luna St, Barangay 2', '2026-07-01 12:30:28'),
(7, 'Gabriel Mendoza', '09171234507', 'gabriel@email.com', '89 Del Pilar St, Barangay 3', '2026-07-01 12:30:28'),
(8, 'Natalie Torres', '09171234508', 'natalie@email.com', '23 Quezon St, Barangay 1', '2026-07-01 12:30:28'),
(9, 'Andres Bautista', '09171234509', 'andres@email.com', '67 Osmeña St, Barangay 5', '2026-07-01 12:30:28'),
(10, 'Patricia Cruz', '09171234510', 'patricia@email.com', '90 Roxas St, Barangay 2', '2026-07-01 12:30:28');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `weight_kg` decimal(5,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','ready','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `service_id`, `weight_kg`, `total_price`, `status`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 3.50, 175.00, 'completed', 'Regular weekly laundry', 1, '2026-07-01 12:32:02', '2026-07-01 12:32:02'),
(2, 2, 2, 2.00, 240.00, 'ready', 'Wedding dress - handle with care', 2, '2026-07-01 12:32:02', '2026-07-01 12:32:02'),
(3, 3, 1, 5.00, 250.00, 'processing', 'Heavy blankets included', 1, '2026-07-01 12:32:02', '2026-07-01 12:32:02'),
(4, 4, 3, 1.50, 60.00, 'pending', 'Office uniforms only', 3, '2026-07-01 12:32:02', '2026-07-01 12:32:02'),
(5, 5, 4, 2.50, 200.00, 'completed', 'Urgent - needed for trip', 2, '2026-07-01 12:32:02', '2026-07-01 12:32:02'),
(6, 6, 1, 4.00, 200.00, 'ready', 'Mixed colors and whites', 1, '2026-07-01 12:32:02', '2026-07-01 12:32:02'),
(7, 7, 5, 3.00, 210.00, 'processing', 'Curtain cleaning', 4, '2026-07-01 12:32:02', '2026-07-01 12:32:02'),
(8, 8, 2, 1.00, 120.00, 'pending', 'Silk blouse', 3, '2026-07-01 12:32:02', '2026-07-01 12:32:02'),
(9, 9, 1, 6.50, 325.00, 'completed', 'Family laundry', 2, '2026-07-01 12:32:02', '2026-07-01 12:32:02'),
(10, 10, 4, 1.50, 120.00, 'ready', 'Quick turnaround needed', 1, '2026-07-01 12:32:02', '2026-07-01 12:32:02'),
(11, 2, 1, 2.00, 100.00, 'pending', 'Weekly service', 3, '2026-07-01 12:32:02', '2026-07-01 12:32:02'),
(12, 5, 3, 3.00, 120.00, 'processing', 'Business shirts', 4, '2026-07-01 12:32:02', '2026-07-01 12:32:02');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price_per_kg` decimal(10,2) NOT NULL,
  `estimated_hours` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `service_name`, `description`, `price_per_kg`, `estimated_hours`, `is_active`) VALUES
(1, 'Wash & Fold', 'Regular washing and folding service', 50.00, 24, 1),
(2, 'Dry Clean', 'Professional dry cleaning for delicate fabrics', 120.00, 48, 1),
(3, 'Iron Press', 'Steam ironing and pressing', 40.00, 12, 1),
(4, 'Express Wash', 'Same-day washing service', 80.00, 4, 1),
(5, 'Bedding & Linens', 'Washing of bedsheets, curtains, tablecloths', 70.00, 36, 1),
(6, 'Shoe Cleaning', 'Deep cleaning and restoration of shoes', 200.00, 72, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `username`, `password_hash`, `role`, `phone`, `created_at`) VALUES
(1, 'Maria Santos', 'maria@freshfold.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '09171234567', '2026-07-01 12:29:32'),
(2, 'Juan Dela Cruz', 'juan@freshfold.com', 'juan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', '09181234567', '2026-07-01 12:29:32'),
(3, 'Ana Reyes', 'ana@freshfold.com', 'ana', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', '09191234567', '2026-07-01 12:29:32'),
(4, 'Pedro Lim', 'pedro@freshfold.com', 'pedro', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', '09201234567', '2026-07-01 12:29:32'),
(5, 'Carmen Garcia', 'carmen@freshfold.com', 'carmen', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', '09211234567', '2026-07-01 12:29:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
