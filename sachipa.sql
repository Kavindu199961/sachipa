-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2026 at 07:30 AM
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
-- Database: `sachipa`
--

-- --------------------------------------------------------

--
-- Table structure for table `advances`
--

CREATE TABLE `advances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `invoice_customer_id` bigint(20) UNSIGNED NOT NULL,
  `advance_amount` decimal(10,2) DEFAULT NULL,
  `due_balance` decimal(10,2) DEFAULT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `advances`
--

INSERT INTO `advances` (`id`, `invoice_id`, `invoice_customer_id`, `advance_amount`, `due_balance`, `date`, `created_at`, `updated_at`) VALUES
(9, 22, 6, 3000.00, 150.00, '2026-02-26', '2026-02-26 00:56:07', '2026-02-26 00:56:07');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Customer full name',
  `phone_number` varchar(255) DEFAULT NULL COMMENT 'Customer contact number, optional field',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id`, `name`, `phone_number`, `created_at`, `updated_at`) VALUES
(1, 'Nelundeniya', '0765645303', '2026-02-20 10:04:34', '2026-02-20 10:37:39'),
(2, 'Kegalle', '0765648325', '2026-02-20 10:37:52', '2026-02-20 10:37:52'),
(3, 'hhh', '0765648325', '2026-02-25 22:15:01', '2026-02-25 22:15:01');

-- --------------------------------------------------------

--
-- Table structure for table `fabric_cal`
--

CREATE TABLE `fabric_cal` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stick` double DEFAULT NULL COMMENT 'Stick measurement, nullable field',
  `one_rali` decimal(10,2) DEFAULT NULL COMMENT 'First Rali measurement, nullable',
  `two_rali` decimal(10,2) DEFAULT NULL COMMENT 'Second Rali measurement, nullable',
  `tree_rali` decimal(10,2) DEFAULT NULL COMMENT 'Third Rali measurement, nullable',
  `four_rali` decimal(10,2) DEFAULT NULL COMMENT 'Fourth Rali measurement, nullable',
  `ilets` decimal(10,2) DEFAULT NULL COMMENT 'Ilets measurement, nullable',
  `sum_one_four` decimal(10,2) DEFAULT NULL COMMENT 'Sum of one_rali and four_rali, nullable',
  `sum_two_tree` decimal(10,2) DEFAULT NULL COMMENT 'Sum of two_rali and tree_rali, nullable',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fabric_cal`
--

INSERT INTO `fabric_cal` (`id`, `customer_id`, `stick`, `one_rali`, `two_rali`, `tree_rali`, `four_rali`, `ilets`, `sum_one_four`, `sum_two_tree`, `created_at`, `updated_at`) VALUES
(6, 1, 10.5, 238.00, 272.00, 272.00, 238.00, 60.00, 816.00, 884.00, '2026-02-20 10:36:01', '2026-02-20 10:36:27'),
(7, 1, 6.5, 170.00, 170.00, 170.00, 170.00, 40.00, 816.00, 884.00, '2026-02-20 10:36:27', '2026-02-20 10:36:27'),
(10, 2, 10.5, 238.00, 272.00, 272.00, 238.00, 60.00, 4284.00, 2652.00, '2026-02-20 10:47:37', '2026-02-20 12:07:50'),
(11, 2, 6.5, 170.00, 170.00, 170.00, 170.00, 40.00, 4284.00, 2652.00, '2026-02-20 10:47:54', '2026-02-20 12:07:50'),
(12, 2, 6.5, 170.00, 170.00, 170.00, 170.00, 40.00, 4284.00, 2652.00, '2026-02-20 10:48:04', '2026-02-20 12:07:50'),
(13, 2, 6.5, 170.00, 170.00, 170.00, 170.00, 40.00, 4284.00, 2652.00, '2026-02-20 10:48:17', '2026-02-20 12:07:50'),
(14, 2, 6.5, 170.00, 170.00, 170.00, 170.00, 40.00, 4284.00, 2652.00, '2026-02-20 10:48:22', '2026-02-20 12:07:50'),
(15, 2, 3.5, 340.00, NULL, NULL, NULL, 20.00, 4284.00, 2652.00, '2026-02-20 10:48:51', '2026-02-20 12:07:50'),
(16, 2, 3.5, 340.00, NULL, NULL, NULL, 20.00, 4284.00, 2652.00, '2026-02-20 10:48:58', '2026-02-20 12:07:50'),
(17, 2, 3.5, 340.00, NULL, NULL, NULL, 20.00, 4284.00, 2652.00, '2026-02-20 10:49:03', '2026-02-20 12:07:50'),
(18, 2, 3.5, 340.00, NULL, NULL, NULL, 20.00, 4284.00, 2652.00, '2026-02-20 10:49:12', '2026-02-20 12:07:50'),
(19, 2, 3.5, 340.00, NULL, NULL, NULL, 20.00, 4284.00, 2652.00, '2026-02-20 10:49:18', '2026-02-20 12:07:50'),
(20, 2, 8, 204.00, 204.00, 204.00, 204.00, 48.00, 4284.00, 2652.00, '2026-02-20 10:49:49', '2026-02-20 12:07:50'),
(22, 2, 3.5, 170.00, 170.00, 170.00, 170.00, 40.00, 4284.00, 2652.00, '2026-02-20 12:03:06', '2026-02-20 12:07:50'),
(26, 3, 3.5, 170.00, 170.00, 170.00, 170.00, 40.00, 1088.00, 1088.00, '2026-02-25 22:30:48', '2026-02-25 22:30:58'),
(27, 3, 7.5, 102.00, 102.00, 102.00, 102.00, 24.00, 1088.00, 1088.00, '2026-02-25 22:30:48', '2026-02-25 22:30:58'),
(28, 3, 5.5, 272.00, 272.00, 272.00, 272.00, 64.00, 1088.00, 1088.00, '2026-02-25 22:30:48', '2026-02-25 22:30:58');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_customer_id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `qty` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `item_discount` decimal(10,2) DEFAULT NULL,
  `final_amount` decimal(10,2) NOT NULL,
  `final_amount_discount` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_customer_id`, `item_name`, `rate`, `qty`, `amount`, `item_discount`, `final_amount`, `final_amount_discount`, `total_amount`, `created_at`, `updated_at`) VALUES
(22, 6, 'STIIP', 350.00, 10, 3500.00, NULL, 3500.00, NULL, NULL, '2026-02-26 00:56:07', '2026-02-26 00:56:07');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_customers`
--

CREATE TABLE `invoice_customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `advanced_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_customers`
--

INSERT INTO `invoice_customers` (`id`, `name`, `phone_number`, `email`, `location`, `created_at`, `updated_at`, `invoice_id`, `advanced_id`) VALUES
(6, NULL, NULL, NULL, NULL, '2026-02-26 00:56:07', '2026-02-26 00:56:07', 22, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_18_063005_create_my_shop_details_table', 2),
(5, '2026_02_18_070518_create_stock_table', 3),
(6, '2026_02_20_150506_create_customer_and_fabric_cal_table', 4),
(7, '2026_02_26_043116_create_invoice_customers_table', 5),
(8, '2026_02_26_043207_create_invoices_table', 6),
(9, '2026_02_26_043347_create_advanceds_table', 7),
(10, '2026_02_26_043705_add_invoice_advanced_columns_to_invoice_customers_table', 8),
(11, '2026_02_26_055444_add_total_amount_to_invoices_table', 9);

-- --------------------------------------------------------

--
-- Table structure for table `my_shop_details`
--

CREATE TABLE `my_shop_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `shop_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `hotline` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `logo_image` varchar(255) DEFAULT NULL,
  `condition_1` text DEFAULT NULL,
  `condition_2` text DEFAULT NULL,
  `condition_3` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `my_shop_details`
--

INSERT INTO `my_shop_details` (`id`, `shop_name`, `description`, `address`, `hotline`, `email`, `logo_image`, `condition_1`, `condition_2`, `condition_3`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Sachipa Curtain', 'new', 'kegalle123', '0765645303', 'ceylongit@gmail.com', 'shop_logos/j9P3nQWinhZDizbM4Xh9JEOOqiV8w3d4SrC9lTOm.png', '.', '.', '.', 1, '2026-02-18 01:21:14', '2026-02-18 01:21:45');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('gfx1emgXqFUt0WCxTEblXDiEgt3ubFb4mYwx0JEC', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY29VTGpFMUkyVnFlTEZ4a3BnZVhhSFdjWDlPcU9aN2hLV0xRc284eSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9pbnZvaWNlcyI7czo1OiJyb3V0ZSI7czoxNDoiaW52b2ljZXMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1772087167);

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_code` varchar(100) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cost` decimal(15,2) DEFAULT NULL,
  `whole_sale_price` decimal(15,2) DEFAULT NULL,
  `retail_price` decimal(15,2) DEFAULT NULL,
  `vender` varchar(255) DEFAULT NULL,
  `stock_date` date DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `barcode` varchar(100) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stock`
--

INSERT INTO `stock` (`id`, `item_code`, `item_name`, `description`, `cost`, `whole_sale_price`, `retail_price`, `vender`, `stock_date`, `quantity`, `barcode`, `user_id`, `created_at`, `updated_at`) VALUES
(1, NULL, 'food -3.5', NULL, 250.00, NULL, 350.00, NULL, '2026-02-18', 52, NULL, 1, '2026-02-18 01:48:23', '2026-02-18 02:38:06'),
(2, NULL, 'food 34', NULL, 250.00, NULL, 454.00, NULL, '2026-02-18', 23, NULL, 1, '2026-02-18 01:51:37', '2026-02-18 02:40:05');

-- --------------------------------------------------------

--
-- Table structure for table `today_items`
--

CREATE TABLE `today_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_code` varchar(100) DEFAULT NULL,
  `item_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `cost` decimal(15,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_cost` decimal(15,2) NOT NULL,
  `stock_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `selection_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'sachipa', 'sachipa@gmail.com', NULL, '$2y$12$RH..kYHohw7W6kQ2ZDiqneLrh0ZqtyhPXfAXRalWerA6vcsQAWIr2', NULL, '2026-02-18 00:35:22', '2026-02-18 00:35:22'),
(2, 'Test User', 'test@example.com', NULL, '$2y$12$RUshamzZssLcH5O43aEGnuG6eEh39B/EQdOViiioruByoEykJoGl2', NULL, '2026-02-18 00:35:23', '2026-02-18 00:35:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advances`
--
ALTER TABLE `advances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `advances_invoice_id_foreign` (`invoice_id`),
  ADD KEY `advances_invoice_customer_id_foreign` (`invoice_customer_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fabric_cal`
--
ALTER TABLE `fabric_cal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fabric_cal_customer_id_index` (`customer_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoices_invoice_customer_id_foreign` (`invoice_customer_id`);

--
-- Indexes for table `invoice_customers`
--
ALTER TABLE `invoice_customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_customers_invoice_id_foreign` (`invoice_id`),
  ADD KEY `invoice_customers_advanced_id_foreign` (`advanced_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `my_shop_details`
--
ALTER TABLE `my_shop_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `my_shop_details_user_id_index` (`user_id`),
  ADD KEY `my_shop_details_shop_name_index` (`shop_name`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stock_barcode_unique` (`barcode`),
  ADD KEY `stock_item_code_index` (`item_code`),
  ADD KEY `stock_item_name_index` (`item_name`),
  ADD KEY `stock_vender_index` (`vender`),
  ADD KEY `stock_stock_date_index` (`stock_date`),
  ADD KEY `stock_user_id_index` (`user_id`),
  ADD KEY `stock_barcode_index` (`barcode`);

--
-- Indexes for table `today_items`
--
ALTER TABLE `today_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advances`
--
ALTER TABLE `advances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fabric_cal`
--
ALTER TABLE `fabric_cal`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `invoice_customers`
--
ALTER TABLE `invoice_customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `my_shop_details`
--
ALTER TABLE `my_shop_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock`
--
ALTER TABLE `stock`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `today_items`
--
ALTER TABLE `today_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advances`
--
ALTER TABLE `advances`
  ADD CONSTRAINT `advances_invoice_customer_id_foreign` FOREIGN KEY (`invoice_customer_id`) REFERENCES `invoice_customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `advances_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fabric_cal`
--
ALTER TABLE `fabric_cal`
  ADD CONSTRAINT `fabric_cal_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_invoice_customer_id_foreign` FOREIGN KEY (`invoice_customer_id`) REFERENCES `invoice_customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_customers`
--
ALTER TABLE `invoice_customers`
  ADD CONSTRAINT `invoice_customers_advanced_id_foreign` FOREIGN KEY (`advanced_id`) REFERENCES `advances` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoice_customers_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `my_shop_details`
--
ALTER TABLE `my_shop_details`
  ADD CONSTRAINT `my_shop_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `stock_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
