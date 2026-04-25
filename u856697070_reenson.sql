-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 21, 2026 at 04:53 AM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u856697070_reenson`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `account_number` varchar(191) NOT NULL,
  `account_details` text DEFAULT NULL,
  `account_type_id` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_transactions`
--

CREATE TABLE `account_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `account_id` int(11) NOT NULL,
  `type` enum('debit','credit') NOT NULL,
  `sub_type` enum('opening_balance','fund_transfer','deposit') DEFAULT NULL,
  `amount` decimal(22,4) NOT NULL,
  `reff_no` varchar(191) DEFAULT NULL,
  `operation_date` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `transaction_payment_id` int(11) DEFAULT NULL,
  `transfer_transaction_id` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_types`
--

CREATE TABLE `account_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `parent_account_type_id` int(11) DEFAULT NULL,
  `business_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `log_name` varchar(191) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `subject_type` varchar(191) DEFAULT NULL,
  `event` varchar(191) DEFAULT NULL,
  `business_id` int(11) DEFAULT NULL,
  `causer_id` int(11) DEFAULT NULL,
  `causer_type` varchar(191) DEFAULT NULL,
  `properties` text DEFAULT NULL,
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_id`, `subject_type`, `event`, `business_id`, `causer_id`, `causer_type`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 09:53:53', '2026-04-09 09:53:53'),
(2, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 09:55:38', '2026-04-09 09:55:38'),
(3, 'default', 'logout', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 10:12:53', '2026-04-09 10:12:53'),
(4, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 10:12:56', '2026-04-09 10:12:56'),
(5, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 10:43:44', '2026-04-09 10:43:44'),
(6, 'default', 'added', 6, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":250}}', NULL, '2026-04-09 10:53:04', '2026-04-09 10:53:04'),
(7, 'default', 'edited', 3, 'App\\User', NULL, 1, 2, 'App\\User', '{\"name\":\" Sales Attendant\"}', NULL, '2026-04-09 11:18:13', '2026-04-09 11:18:13'),
(8, 'default', 'logout', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 11:21:29', '2026-04-09 11:21:29'),
(9, 'default', 'login', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-09 11:21:34', '2026-04-09 11:21:34'),
(10, 'default', 'logout', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-09 11:21:56', '2026-04-09 11:21:56'),
(11, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 11:22:01', '2026-04-09 11:22:01'),
(12, 'default', 'logout', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 11:23:56', '2026-04-09 11:23:56'),
(13, 'default', 'login', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-09 11:23:59', '2026-04-09 11:23:59'),
(14, 'default', 'logout', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-09 11:24:13', '2026-04-09 11:24:13'),
(15, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 11:24:17', '2026-04-09 11:24:17'),
(16, 'default', 'edited', 3, 'App\\User', NULL, 1, 2, 'App\\User', '{\"name\":\" Sales Attendant\"}', NULL, '2026-04-09 11:24:31', '2026-04-09 11:24:31'),
(17, 'default', 'logout', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 11:24:50', '2026-04-09 11:24:50'),
(18, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 11:25:01', '2026-04-09 11:25:01'),
(19, 'default', 'logout', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 11:25:34', '2026-04-09 11:25:34'),
(20, 'default', 'login', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-09 11:25:38', '2026-04-09 11:25:38'),
(21, 'default', 'added', 7, 'App\\Transaction', NULL, 1, 3, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":250}}', NULL, '2026-04-09 11:26:21', '2026-04-09 11:26:21'),
(22, 'default', 'logout', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-09 11:27:07', '2026-04-09 11:27:07'),
(23, 'default', 'login', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-09 11:27:52', '2026-04-09 11:27:52'),
(24, 'default', 'logout', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-09 11:33:04', '2026-04-09 11:33:04'),
(25, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 11:33:12', '2026-04-09 11:33:12'),
(26, 'default', 'added', 2, 'App\\Contact', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 11:36:27', '2026-04-09 11:36:27'),
(27, 'default', 'added', 9, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"paid\",\"final_total\":1040}}', NULL, '2026-04-09 11:39:10', '2026-04-09 11:39:10'),
(28, 'default', 'added', 11, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"paid\",\"final_total\":400}}', NULL, '2026-04-09 11:47:26', '2026-04-09 11:47:26'),
(29, 'default', 'edited', 4, 'App\\User', NULL, 1, 2, 'App\\User', '{\"name\":\" Sales Kyaani\"}', NULL, '2026-04-09 11:57:49', '2026-04-09 11:57:49'),
(30, 'default', 'logout', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 11:57:57', '2026-04-09 11:57:57'),
(31, 'default', 'login', 4, 'App\\User', NULL, 1, 4, 'App\\User', '[]', NULL, '2026-04-09 11:58:01', '2026-04-09 11:58:01'),
(32, 'default', 'edited', 4, 'App\\User', NULL, 1, 4, 'App\\User', '{\"name\":\" Sales Kyaani\"}', NULL, '2026-04-09 11:58:24', '2026-04-09 11:58:24'),
(33, 'default', 'logout', 4, 'App\\User', NULL, 1, 4, 'App\\User', '[]', NULL, '2026-04-09 11:58:30', '2026-04-09 11:58:30'),
(34, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 11:58:33', '2026-04-09 11:58:33'),
(35, 'default', 'edited', 4, 'App\\User', NULL, 1, 2, 'App\\User', '{\"name\":\" Sales Kyaani\"}', NULL, '2026-04-09 11:58:48', '2026-04-09 11:58:48'),
(36, 'default', 'logout', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 11:58:55', '2026-04-09 11:58:55'),
(37, 'default', 'login', 4, 'App\\User', NULL, 1, 4, 'App\\User', '[]', NULL, '2026-04-09 11:58:58', '2026-04-09 11:58:58'),
(38, 'default', 'logout', 4, 'App\\User', NULL, 1, 4, 'App\\User', '[]', NULL, '2026-04-09 11:59:38', '2026-04-09 11:59:38'),
(39, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 11:59:41', '2026-04-09 11:59:41'),
(40, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 12:04:57', '2026-04-09 12:04:57'),
(41, 'default', 'added', 12, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":400}}', NULL, '2026-04-09 12:06:13', '2026-04-09 12:06:13'),
(42, 'default', 'added', 14, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":300}}', NULL, '2026-04-09 16:30:46', '2026-04-09 16:30:46'),
(43, 'default', 'added', 15, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"paid\",\"final_total\":1356}}', NULL, '2026-04-09 16:37:04', '2026-04-09 16:37:04'),
(44, 'default', 'logout', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 16:43:53', '2026-04-09 16:43:53'),
(45, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 16:48:55', '2026-04-09 16:48:55'),
(46, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-09 18:14:43', '2026-04-09 18:14:43'),
(47, 'default', 'login', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-09 18:28:53', '2026-04-09 18:28:53'),
(48, 'default', 'added', 16, 'App\\Transaction', NULL, 1, 3, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":400}}', NULL, '2026-04-09 18:30:36', '2026-04-09 18:30:36'),
(49, 'default', 'added', 17, 'App\\Transaction', NULL, 1, 3, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":400}}', NULL, '2026-04-09 19:05:41', '2026-04-09 19:05:41'),
(50, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-10 18:08:57', '2026-04-10 18:08:57'),
(51, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-10 21:48:41', '2026-04-10 21:48:41'),
(52, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-12 09:22:42', '2026-04-12 09:22:42'),
(53, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-12 09:24:42', '2026-04-12 09:24:42'),
(54, 'default', 'logout', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-12 09:58:42', '2026-04-12 09:58:42'),
(55, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-12 09:58:45', '2026-04-12 09:58:45'),
(56, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-12 10:05:10', '2026-04-12 10:05:10'),
(57, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-12 13:39:36', '2026-04-12 13:39:36'),
(58, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-12 14:02:31', '2026-04-12 14:02:31'),
(59, 'default', 'logout', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-12 15:07:37', '2026-04-12 15:07:37'),
(60, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-12 15:08:00', '2026-04-12 15:08:00'),
(61, 'default', 'logout', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-12 15:28:13', '2026-04-12 15:28:13'),
(62, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-12 15:28:40', '2026-04-12 15:28:40'),
(63, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-12 17:14:02', '2026-04-12 17:14:02'),
(64, 'default', 'login', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-12 19:18:59', '2026-04-12 19:18:59'),
(65, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-12 19:51:52', '2026-04-12 19:51:52'),
(66, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-13 00:27:26', '2026-04-13 00:27:26'),
(67, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-13 08:45:40', '2026-04-13 08:45:40'),
(68, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-13 08:54:43', '2026-04-13 08:54:43'),
(69, 'default', 'added', 19, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":250}}', NULL, '2026-04-13 09:09:46', '2026-04-13 09:09:46'),
(70, 'default', 'sell_deleted', 19, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"id\":19,\"invoice_no\":\"0007\",\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":\"250.0000\"}}', NULL, '2026-04-13 09:10:05', '2026-04-13 09:10:05'),
(71, 'default', 'added', 20, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":250}}', NULL, '2026-04-13 09:40:39', '2026-04-13 09:40:39'),
(72, 'default', 'login', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-13 13:22:39', '2026-04-13 13:22:39'),
(73, 'default', 'added', 21, 'App\\Transaction', NULL, 1, 3, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":250}}', NULL, '2026-04-13 13:23:46', '2026-04-13 13:23:46'),
(74, 'default', 'added', 22, 'App\\Transaction', NULL, 1, 3, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":500}}', NULL, '2026-04-13 13:25:09', '2026-04-13 13:25:09'),
(75, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-13 18:49:08', '2026-04-13 18:49:08'),
(76, 'default', 'login', 4, 'App\\User', NULL, 1, 4, 'App\\User', '[]', NULL, '2026-04-13 18:50:58', '2026-04-13 18:50:58'),
(77, 'default', 'added', 23, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"due\",\"final_total\":61}}', NULL, '2026-04-13 19:01:53', '2026-04-13 19:01:53'),
(78, 'default', 'payment_edited', 23, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"paid\",\"final_total\":\"61.0000\"},\"old\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"due\",\"final_total\":\"61.0000\"}}', NULL, '2026-04-13 19:02:59', '2026-04-13 19:02:59'),
(79, 'default', 'added', 24, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"paid\",\"final_total\":1650}}', NULL, '2026-04-13 19:05:14', '2026-04-13 19:05:14'),
(80, 'default', 'added', 25, 'App\\Transaction', NULL, 1, 4, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":250}}', NULL, '2026-04-13 19:05:36', '2026-04-13 19:05:36'),
(81, 'default', 'added', 26, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":300}}', NULL, '2026-04-13 19:37:04', '2026-04-13 19:37:04'),
(82, 'default', 'added', 27, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":300}}', NULL, '2026-04-13 19:39:30', '2026-04-13 19:39:30'),
(83, 'default', 'added', 28, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":250}}', NULL, '2026-04-13 19:45:19', '2026-04-13 19:45:19'),
(84, 'default', 'added', 29, 'App\\Transaction', NULL, 1, 4, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":250}}', NULL, '2026-04-13 19:48:55', '2026-04-13 19:48:55'),
(85, 'default', 'login', 4, 'App\\User', NULL, 1, 4, 'App\\User', '[]', NULL, '2026-04-13 20:06:54', '2026-04-13 20:06:54'),
(86, 'default', 'logout', 4, 'App\\User', NULL, 1, 4, 'App\\User', '[]', NULL, '2026-04-13 20:08:52', '2026-04-13 20:08:52'),
(87, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-13 20:10:14', '2026-04-13 20:10:14'),
(88, 'default', 'added', 30, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"paid\",\"final_total\":510}}', NULL, '2026-04-13 20:14:32', '2026-04-13 20:14:32'),
(89, 'default', 'added', 31, 'App\\Transaction', NULL, 1, 4, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":150}}', NULL, '2026-04-13 20:29:28', '2026-04-13 20:29:28'),
(90, 'default', 'login', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-13 20:47:23', '2026-04-13 20:47:23'),
(91, 'default', 'added', 32, 'App\\Transaction', NULL, 1, 3, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":200}}', NULL, '2026-04-13 20:48:20', '2026-04-13 20:48:20'),
(92, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-14 17:38:18', '2026-04-14 17:38:18'),
(93, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-14 17:38:52', '2026-04-14 17:38:52'),
(94, 'default', 'login', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-14 18:07:24', '2026-04-14 18:07:24'),
(95, 'default', 'added', 33, 'App\\Transaction', NULL, 1, 3, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":300}}', NULL, '2026-04-14 18:08:21', '2026-04-14 18:08:21'),
(96, 'default', 'login', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-15 11:02:34', '2026-04-15 11:02:34'),
(97, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-19 18:33:50', '2026-04-19 18:33:50'),
(98, 'default', 'added', 34, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":200}}', NULL, '2026-04-19 18:35:01', '2026-04-19 18:35:01'),
(99, 'default', 'added', 35, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":400}}', NULL, '2026-04-19 18:38:15', '2026-04-19 18:38:15'),
(100, 'default', 'added', 36, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":200}}', NULL, '2026-04-19 18:40:25', '2026-04-19 18:40:25'),
(101, 'default', 'added', 37, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"due\",\"final_total\":395}}', NULL, '2026-04-19 18:55:57', '2026-04-19 18:55:57'),
(102, 'default', 'added', 38, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"due\",\"final_total\":248}}', NULL, '2026-04-19 18:58:45', '2026-04-19 18:58:45'),
(103, 'default', 'added', 39, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":200}}', NULL, '2026-04-19 19:03:17', '2026-04-19 19:03:17'),
(104, 'default', 'added', 40, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":200}}', NULL, '2026-04-19 20:18:46', '2026-04-19 20:18:46'),
(105, 'default', 'added', 41, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":300}}', NULL, '2026-04-19 20:24:40', '2026-04-19 20:24:40'),
(106, 'default', 'added', 42, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":400}}', NULL, '2026-04-19 20:26:57', '2026-04-19 20:26:57'),
(107, 'default', 'added', 43, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":500}}', NULL, '2026-04-19 20:34:01', '2026-04-19 20:34:01'),
(108, 'default', 'added', 44, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":100}}', NULL, '2026-04-19 20:34:44', '2026-04-19 20:34:44'),
(109, 'default', 'added', 45, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"paid\",\"final_total\":60054}}', NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(110, 'default', 'added', 46, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"due\",\"final_total\":3510}}', NULL, '2026-04-19 21:41:00', '2026-04-19 21:41:00'),
(111, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-19 22:40:31', '2026-04-19 22:40:31'),
(112, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-20 08:19:08', '2026-04-20 08:19:08'),
(113, 'default', 'login', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-20 11:32:32', '2026-04-20 11:32:32'),
(114, 'default', 'logout', 3, 'App\\User', NULL, 1, 3, 'App\\User', '[]', NULL, '2026-04-20 11:38:38', '2026-04-20 11:38:38'),
(115, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-20 11:40:30', '2026-04-20 11:40:30'),
(116, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-20 12:27:23', '2026-04-20 12:27:23'),
(117, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-20 14:22:49', '2026-04-20 14:22:49'),
(118, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-20 15:47:55', '2026-04-20 15:47:55'),
(119, 'default', 'added', 47, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":750}}', NULL, '2026-04-20 15:52:45', '2026-04-20 15:52:45'),
(120, 'default', 'added', 48, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":150}}', NULL, '2026-04-20 15:54:12', '2026-04-20 15:54:12'),
(121, 'default', 'added', 49, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":150}}', NULL, '2026-04-20 15:54:12', '2026-04-20 15:54:12'),
(122, 'default', 'added', 50, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":2050}}', NULL, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(123, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-20 19:54:28', '2026-04-20 19:54:28'),
(124, 'default', 'added', 51, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"partial\",\"final_total\":7577}}', NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(125, 'default', 'edited', 46, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"due\",\"final_total\":3510},\"old\":{\"type\":\"purchase\",\"status\":\"received\",\"payment_status\":\"due\",\"final_total\":\"3510.0000\"}}', NULL, '2026-04-20 20:04:46', '2026-04-20 20:04:46'),
(126, 'default', 'added', 52, 'App\\Transaction', NULL, 1, 2, 'App\\User', '{\"attributes\":{\"type\":\"sell\",\"status\":\"final\",\"payment_status\":\"paid\",\"final_total\":840}}', NULL, '2026-04-20 20:08:25', '2026-04-20 20:08:25'),
(127, 'default', 'login', 2, 'App\\User', NULL, 1, 2, 'App\\User', '[]', NULL, '2026-04-21 07:44:51', '2026-04-21 07:44:51');

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `approvable_type` varchar(191) NOT NULL,
  `approvable_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `requested_by` bigint(20) UNSIGNED NOT NULL,
  `approval_flow_id` bigint(20) UNSIGNED DEFAULT NULL,
  `current_step` int(11) NOT NULL DEFAULT 1,
  `status` enum('pending','approved','rejected','returned') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_decisions`
--

CREATE TABLE `approval_decisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `approval_id` bigint(20) UNSIGNED NOT NULL,
  `step_number` bigint(20) UNSIGNED NOT NULL,
  `decided_by` bigint(20) UNSIGNED NOT NULL,
  `decision` enum('approved','rejected','returned') NOT NULL,
  `comment` text DEFAULT NULL,
  `decided_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_flows`
--

CREATE TABLE `approval_flows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `approvable_type` varchar(191) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_flow_steps`
--

CREATE TABLE `approval_flow_steps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `approval_flow_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `label` varchar(191) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barcodes`
--

CREATE TABLE `barcodes` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `width` double(22,4) DEFAULT NULL,
  `height` double(22,4) DEFAULT NULL,
  `paper_width` double(22,4) DEFAULT NULL,
  `paper_height` double(22,4) DEFAULT NULL,
  `top_margin` double(22,4) DEFAULT NULL,
  `left_margin` double(22,4) DEFAULT NULL,
  `row_distance` double(22,4) DEFAULT NULL,
  `col_distance` double(22,4) DEFAULT NULL,
  `stickers_in_one_row` int(11) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_continuous` tinyint(1) NOT NULL DEFAULT 0,
  `stickers_in_one_sheet` int(11) DEFAULT NULL,
  `business_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barcodes`
--

INSERT INTO `barcodes` (`id`, `name`, `description`, `width`, `height`, `paper_width`, `paper_height`, `top_margin`, `left_margin`, `row_distance`, `col_distance`, `stickers_in_one_row`, `is_default`, `is_continuous`, `stickers_in_one_sheet`, `business_id`, `created_at`, `updated_at`) VALUES
(1, '20 Labels per Sheet', 'Sheet Size: 8.5\" x 11\", Label Size: 4\" x 1\", Labels per sheet: 20', 4.0000, 1.0000, 8.5000, 11.0000, 0.5000, 0.1250, 0.0000, 0.1875, 2, 0, 0, 20, NULL, '2017-12-18 03:13:44', '2017-12-18 03:13:44'),
(2, '30 Labels per sheet', 'Sheet Size: 8.5\" x 11\", Label Size: 2.625\" x 1\", Labels per sheet: 30', 2.6250, 1.0000, 8.5000, 11.0000, 0.5000, 0.1880, 0.0000, 0.1250, 3, 0, 0, 30, NULL, '2017-12-18 03:04:39', '2017-12-18 03:10:40'),
(3, '32 Labels per sheet', 'Sheet Size: 8.5\" x 11\", Label Size: 2\" x 1.25\", Labels per sheet: 32', 2.0000, 1.2500, 8.5000, 11.0000, 0.5000, 0.2500, 0.0000, 0.0000, 4, 0, 0, 32, NULL, '2017-12-18 02:55:40', '2017-12-18 02:55:40'),
(4, '40 Labels per sheet', 'Sheet Size: 8.5\" x 11\", Label Size: 2\" x 1\", Labels per sheet: 40', 2.0000, 1.0000, 8.5000, 11.0000, 0.5000, 0.2500, 0.0000, 0.0000, 4, 0, 0, 40, NULL, '2017-12-18 02:58:40', '2017-12-18 02:58:40'),
(5, '50 Labels per Sheet', 'Sheet Size: 8.5\" x 11\", Label Size: 1.5\" x 1\", Labels per sheet: 50', 1.5000, 1.0000, 8.5000, 11.0000, 0.5000, 0.5000, 0.0000, 0.0000, 5, 0, 0, 50, NULL, '2017-12-18 02:51:10', '2017-12-18 02:51:10'),
(6, 'Continuous Rolls - 31.75mm x 25.4mm', 'Label Size: 31.75mm x 25.4mm, Gap: 3.18mm', 1.2500, 1.0000, 1.2500, 0.0000, 0.1250, 0.0000, 0.1250, 0.0000, 1, 0, 1, NULL, NULL, '2017-12-18 02:51:10', '2017-12-18 02:51:10');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(10) UNSIGNED NOT NULL,
  `contact_id` int(10) UNSIGNED NOT NULL,
  `waiter_id` int(10) UNSIGNED DEFAULT NULL,
  `table_id` int(10) UNSIGNED DEFAULT NULL,
  `correspondent_id` int(11) DEFAULT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `booking_start` datetime NOT NULL,
  `booking_end` datetime NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `booking_status` varchar(191) NOT NULL,
  `booking_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business`
--

CREATE TABLE `business` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `business_type` varchar(191) DEFAULT NULL,
  `currency_id` int(10) UNSIGNED NOT NULL,
  `start_date` date DEFAULT NULL,
  `tax_number_1` varchar(100) DEFAULT NULL,
  `tax_label_1` varchar(10) DEFAULT NULL,
  `tax_number_2` varchar(100) DEFAULT NULL,
  `tax_label_2` varchar(10) DEFAULT NULL,
  `code_label_1` varchar(191) DEFAULT NULL,
  `code_1` varchar(191) DEFAULT NULL,
  `code_label_2` varchar(191) DEFAULT NULL,
  `code_2` varchar(191) DEFAULT NULL,
  `default_sales_tax` int(10) UNSIGNED DEFAULT NULL,
  `default_profit_percent` double(5,2) NOT NULL DEFAULT 0.00,
  `owner_id` int(10) UNSIGNED NOT NULL,
  `time_zone` varchar(191) NOT NULL DEFAULT 'Asia/Kolkata',
  `fy_start_month` tinyint(4) NOT NULL DEFAULT 1,
  `accounting_method` enum('fifo','lifo','avco') NOT NULL DEFAULT 'fifo',
  `default_sales_discount` decimal(5,2) DEFAULT NULL,
  `sell_price_tax` enum('includes','excludes') NOT NULL DEFAULT 'includes',
  `logo` varchar(191) DEFAULT NULL,
  `sku_prefix` varchar(191) DEFAULT NULL,
  `enable_product_expiry` tinyint(1) NOT NULL DEFAULT 0,
  `expiry_type` enum('add_expiry','add_manufacturing') NOT NULL DEFAULT 'add_expiry',
  `on_product_expiry` enum('keep_selling','stop_selling','auto_delete') NOT NULL DEFAULT 'keep_selling',
  `stop_selling_before` int(11) NOT NULL COMMENT 'Stop selling expied item n days before expiry',
  `enable_tooltip` tinyint(1) NOT NULL DEFAULT 1,
  `purchase_in_diff_currency` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Allow purchase to be in different currency then the business currency',
  `purchase_currency_id` int(10) UNSIGNED DEFAULT NULL,
  `p_exchange_rate` decimal(20,3) NOT NULL DEFAULT 1.000,
  `transaction_edit_days` int(10) UNSIGNED NOT NULL DEFAULT 30,
  `stock_expiry_alert_days` int(10) UNSIGNED NOT NULL DEFAULT 30,
  `keyboard_shortcuts` text DEFAULT NULL,
  `pos_settings` text DEFAULT NULL,
  `weighing_scale_setting` text NOT NULL COMMENT 'used to store the configuration of weighing scale',
  `enable_brand` tinyint(1) NOT NULL DEFAULT 1,
  `enable_category` tinyint(1) NOT NULL DEFAULT 1,
  `enable_sub_category` tinyint(1) NOT NULL DEFAULT 1,
  `enable_price_tax` tinyint(1) NOT NULL DEFAULT 1,
  `enable_purchase_status` tinyint(1) DEFAULT 1,
  `enable_lot_number` tinyint(1) NOT NULL DEFAULT 0,
  `default_unit` int(11) DEFAULT NULL,
  `enable_sub_units` tinyint(1) NOT NULL DEFAULT 0,
  `enable_racks` tinyint(1) NOT NULL DEFAULT 0,
  `enable_row` tinyint(1) NOT NULL DEFAULT 0,
  `enable_position` tinyint(1) NOT NULL DEFAULT 0,
  `enable_editing_product_from_purchase` tinyint(1) NOT NULL DEFAULT 1,
  `sales_cmsn_agnt` enum('logged_in_user','user','cmsn_agnt') DEFAULT NULL,
  `item_addition_method` tinyint(1) NOT NULL DEFAULT 1,
  `enable_inline_tax` tinyint(1) NOT NULL DEFAULT 1,
  `currency_symbol_placement` enum('before','after') NOT NULL DEFAULT 'before',
  `enabled_modules` text DEFAULT NULL,
  `date_format` varchar(191) NOT NULL DEFAULT 'm/d/Y',
  `time_format` enum('12','24') NOT NULL DEFAULT '24',
  `currency_precision` tinyint(4) NOT NULL DEFAULT 2,
  `quantity_precision` tinyint(4) NOT NULL DEFAULT 2,
  `ref_no_prefixes` text DEFAULT NULL,
  `theme_color` char(20) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `enable_rp` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'rp is the short form of reward points',
  `rp_name` varchar(191) DEFAULT NULL COMMENT 'rp is the short form of reward points',
  `amount_for_unit_rp` decimal(22,4) NOT NULL DEFAULT 1.0000 COMMENT 'rp is the short form of reward points',
  `min_order_total_for_rp` decimal(22,4) NOT NULL DEFAULT 1.0000 COMMENT 'rp is the short form of reward points',
  `max_rp_per_order` int(11) DEFAULT NULL COMMENT 'rp is the short form of reward points',
  `redeem_amount_per_unit_rp` decimal(22,4) NOT NULL DEFAULT 1.0000 COMMENT 'rp is the short form of reward points',
  `min_order_total_for_redeem` decimal(22,4) NOT NULL DEFAULT 1.0000 COMMENT 'rp is the short form of reward points',
  `min_redeem_point` int(11) DEFAULT NULL COMMENT 'rp is the short form of reward points',
  `max_redeem_point` int(11) DEFAULT NULL COMMENT 'rp is the short form of reward points',
  `rp_expiry_period` int(11) DEFAULT NULL COMMENT 'rp is the short form of reward points',
  `rp_expiry_type` enum('month','year') NOT NULL DEFAULT 'year' COMMENT 'rp is the short form of reward points',
  `email_settings` text DEFAULT NULL,
  `sms_settings` text DEFAULT NULL,
  `whatsapp_settings` text DEFAULT NULL,
  `custom_labels` text DEFAULT NULL,
  `common_settings` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `digitax_api_key` varchar(191) DEFAULT NULL,
  `etims_sync_mode` enum('realtime','background','manual') NOT NULL DEFAULT 'background',
  `etims_tpin` varchar(191) DEFAULT NULL,
  `etims_enabled` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business`
--

INSERT INTO `business` (`id`, `name`, `business_type`, `currency_id`, `start_date`, `tax_number_1`, `tax_label_1`, `tax_number_2`, `tax_label_2`, `code_label_1`, `code_1`, `code_label_2`, `code_2`, `default_sales_tax`, `default_profit_percent`, `owner_id`, `time_zone`, `fy_start_month`, `accounting_method`, `default_sales_discount`, `sell_price_tax`, `logo`, `sku_prefix`, `enable_product_expiry`, `expiry_type`, `on_product_expiry`, `stop_selling_before`, `enable_tooltip`, `purchase_in_diff_currency`, `purchase_currency_id`, `p_exchange_rate`, `transaction_edit_days`, `stock_expiry_alert_days`, `keyboard_shortcuts`, `pos_settings`, `weighing_scale_setting`, `enable_brand`, `enable_category`, `enable_sub_category`, `enable_price_tax`, `enable_purchase_status`, `enable_lot_number`, `default_unit`, `enable_sub_units`, `enable_racks`, `enable_row`, `enable_position`, `enable_editing_product_from_purchase`, `sales_cmsn_agnt`, `item_addition_method`, `enable_inline_tax`, `currency_symbol_placement`, `enabled_modules`, `date_format`, `time_format`, `currency_precision`, `quantity_precision`, `ref_no_prefixes`, `theme_color`, `created_by`, `enable_rp`, `rp_name`, `amount_for_unit_rp`, `min_order_total_for_rp`, `max_rp_per_order`, `redeem_amount_per_unit_rp`, `min_order_total_for_redeem`, `min_redeem_point`, `max_redeem_point`, `rp_expiry_period`, `rp_expiry_type`, `email_settings`, `sms_settings`, `whatsapp_settings`, `custom_labels`, `common_settings`, `is_active`, `created_at`, `updated_at`, `digitax_api_key`, `etims_sync_mode`, `etims_tpin`, `etims_enabled`) VALUES
(1, 'Reenson Agrovet', NULL, 133, '2026-04-09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25.00, 1, 'Africa/Nairobi', 1, 'fifo', 0.00, 'includes', NULL, NULL, 1, 'add_expiry', 'keep_selling', 0, 1, 0, NULL, 1.000, 30, 30, '{\"pos\":{\"express_checkout\":\"shift+e\",\"pay_n_ckeckout\":\"shift+p\",\"draft\":\"shift+d\",\"cancel\":\"shift+c\",\"recent_product_quantity\":\"f2\",\"weighing_scale\":null,\"edit_discount\":\"shift+i\",\"edit_order_tax\":\"shift+t\",\"add_payment_row\":\"shift+r\",\"finalize_payment\":\"shift+f\",\"add_new_product\":\"f4\"}}', '{\"amount_rounding_method\":null,\"cmmsn_calculation_type\":\"invoice_value\",\"razor_pay_key_id\":null,\"razor_pay_key_secret\":null,\"stripe_public_key\":null,\"stripe_secret_key\":null,\"disable_draft\":\"1\",\"hide_product_suggestion\":\"1\",\"disable_discount\":\"1\",\"disable_order_tax\":\"1\",\"is_pos_subtotal_editable\":\"1\",\"disable_suspend\":\"1\",\"enable_transaction_date\":\"1\",\"disable_credit_sale_button\":\"1\",\"display_screen_heading\":null,\"cash_denominations\":null,\"enable_cash_denomination_on\":\"pos_screen\",\"disable_pay_checkout\":0,\"disable_express_checkout\":0,\"hide_recent_trans\":0}', '{\"label_prefix\":null,\"product_sku_length\":\"4\",\"qty_length\":\"3\",\"qty_length_decimal\":\"2\"}', 1, 1, 1, 1, 1, 0, NULL, 0, 0, 0, 0, 1, NULL, 1, 0, 'before', '[\"purchases\",\"add_sale\",\"pos_sale\",\"stock_transfers\",\"expenses\"]', 'm/d/Y', '24', 2, 2, '{\"purchase\":\"PO\",\"purchase_return\":null,\"purchase_requisition\":null,\"purchase_order\":null,\"stock_transfer\":\"ST\",\"stock_adjustment\":\"SA\",\"sell_return\":\"CN\",\"expense\":\"EP\",\"contacts\":\"CO\",\"purchase_payment\":\"PP\",\"sell_payment\":\"SP\",\"expense_payment\":null,\"business_location\":\"BL\",\"username\":null,\"subscription\":null,\"draft\":null,\"sales_order\":null}', 'green', NULL, 0, NULL, 1.0000, 1.0000, NULL, 1.0000, 1.0000, NULL, NULL, NULL, 'year', '{\"mail_driver\":\"smtp\",\"mail_host\":null,\"mail_port\":null,\"mail_username\":null,\"mail_password\":null,\"mail_encryption\":null,\"mail_from_address\":null,\"mail_from_name\":null}', '{\"sms_service\":\"other\",\"nexmo_key\":null,\"nexmo_secret\":null,\"nexmo_from\":null,\"twilio_sid\":null,\"twilio_token\":null,\"twilio_from\":null,\"advanta_api_key\":null,\"advanta_partner_id\":null,\"advanta_shortcode\":null,\"url\":null,\"send_to_param_name\":\"to\",\"send_to_param_type\":\"string\",\"msg_param_name\":\"text\",\"request_method\":\"post\",\"data_parameter_type\":\"form-data\",\"header_1\":null,\"header_val_1\":null,\"header_2\":null,\"header_val_2\":null,\"header_3\":null,\"header_val_3\":null,\"param_1\":null,\"param_val_1\":null,\"param_2\":null,\"param_val_2\":null,\"param_3\":null,\"param_val_3\":null,\"param_4\":null,\"param_val_4\":null,\"param_5\":null,\"param_val_5\":null,\"param_6\":null,\"param_val_6\":null,\"param_7\":null,\"param_val_7\":null,\"param_8\":null,\"param_val_8\":null,\"param_9\":null,\"param_val_9\":null,\"param_10\":null,\"param_val_10\":null}', '{\"enabled\":\"0\",\"api_provider\":\"meta\",\"access_token\":null,\"phone_number_id\":null,\"business_account_id\":null,\"verify_token\":null,\"api_url\":\"https:\\/\\/graph.facebook.com\\/v17.0\",\"twilio_sid\":null,\"twilio_token\":null,\"twilio_from\":null,\"custom_url\":null,\"custom_api_key\":null,\"custom_sender\":null,\"sale_message_template\":\"Hello {customer_name},\\r\\n\\r\\nThank you for your purchase at {business_name}!\\r\\n\\r\\nInvoice: {invoice_no}\\r\\nTotal: {total}\\r\\nDate: {date}\\r\\n\\r\\nWe appreciate your business!\",\"payment_reminder_template\":\"Hello {customer_name},\\r\\n\\r\\nThis is a friendly reminder about your pending payment.\\r\\n\\r\\nInvoice: {invoice_no}\\r\\nAmount Due: {amount_due}\\r\\nDue Date: {due_date}\\r\\n\\r\\nPlease contact us if you have any questions.\",\"followup_notification_time\":\"09:00\",\"followup_notification_frequency\":\"daily\",\"followup_notification_template\":\"Internal Reminder: Follow up with {customer_name} regarding their inquiry for {product_name}. Planned Date: {followup_date}\",\"schedule_enabled\":\"0\",\"schedule_time\":\"09:00\",\"schedule_days\":[\"monday\",\"tuesday\",\"wednesday\",\"thursday\",\"friday\"]}', '{\"payments\":{\"custom_pay_1\":\"MPESA\",\"custom_pay_2\":null,\"custom_pay_3\":null,\"custom_pay_4\":null,\"custom_pay_5\":null,\"custom_pay_6\":null,\"custom_pay_7\":null},\"contact\":{\"custom_field_1\":null,\"custom_field_2\":null,\"custom_field_3\":null,\"custom_field_4\":null,\"custom_field_5\":null,\"custom_field_6\":null,\"custom_field_7\":null,\"custom_field_8\":null,\"custom_field_9\":null,\"custom_field_10\":null},\"product\":{\"custom_field_1\":null,\"custom_field_2\":null,\"custom_field_3\":null,\"custom_field_4\":null,\"custom_field_5\":null,\"custom_field_6\":null,\"custom_field_7\":null,\"custom_field_8\":null,\"custom_field_9\":null,\"custom_field_10\":null,\"custom_field_11\":null,\"custom_field_12\":null,\"custom_field_13\":null,\"custom_field_14\":null,\"custom_field_15\":null,\"custom_field_16\":null,\"custom_field_17\":null,\"custom_field_18\":null,\"custom_field_19\":null,\"custom_field_20\":null},\"product_cf_details\":{\"1\":{\"type\":null,\"dropdown_options\":null},\"2\":{\"type\":null,\"dropdown_options\":null},\"3\":{\"type\":null,\"dropdown_options\":null},\"4\":{\"type\":null,\"dropdown_options\":null},\"5\":{\"type\":null,\"dropdown_options\":null},\"6\":{\"type\":null,\"dropdown_options\":null},\"7\":{\"type\":null,\"dropdown_options\":null},\"8\":{\"type\":null,\"dropdown_options\":null},\"9\":{\"type\":null,\"dropdown_options\":null},\"10\":{\"type\":null,\"dropdown_options\":null},\"11\":{\"type\":null,\"dropdown_options\":null},\"12\":{\"type\":null,\"dropdown_options\":null},\"13\":{\"type\":null,\"dropdown_options\":null},\"14\":{\"type\":null,\"dropdown_options\":null},\"15\":{\"type\":null,\"dropdown_options\":null},\"16\":{\"type\":null,\"dropdown_options\":null},\"17\":{\"type\":null,\"dropdown_options\":null},\"18\":{\"type\":null,\"dropdown_options\":null},\"19\":{\"type\":null,\"dropdown_options\":null},\"20\":{\"type\":null,\"dropdown_options\":null}},\"location\":{\"custom_field_1\":null,\"custom_field_2\":null,\"custom_field_3\":null,\"custom_field_4\":null},\"user\":{\"custom_field_1\":null,\"custom_field_2\":null,\"custom_field_3\":null,\"custom_field_4\":null},\"purchase\":{\"custom_field_1\":null,\"custom_field_2\":null,\"custom_field_3\":null,\"custom_field_4\":null},\"purchase_shipping\":{\"custom_field_1\":null,\"custom_field_2\":null,\"custom_field_3\":null,\"custom_field_4\":null,\"custom_field_5\":null},\"sell\":{\"custom_field_1\":null,\"custom_field_2\":null,\"custom_field_3\":null,\"custom_field_4\":null},\"shipping\":{\"custom_field_1\":null,\"custom_field_2\":null,\"custom_field_3\":null,\"custom_field_4\":null,\"custom_field_5\":null},\"types_of_service\":{\"custom_field_1\":null,\"custom_field_2\":null,\"custom_field_3\":null,\"custom_field_4\":null,\"custom_field_5\":null,\"custom_field_6\":null}}', '{\"default_credit_limit\":null,\"default_datatable_page_entries\":\"25\"}', 1, '2026-04-09 09:53:34', '2026-04-20 20:03:40', NULL, 'background', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `business_locations`
--

CREATE TABLE `business_locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` varchar(191) DEFAULT NULL,
  `name` varchar(256) NOT NULL,
  `landmark` text DEFAULT NULL,
  `country` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `zip_code` char(7) NOT NULL,
  `invoice_scheme_id` int(10) UNSIGNED NOT NULL,
  `sale_invoice_scheme_id` int(11) DEFAULT NULL,
  `invoice_layout_id` int(10) UNSIGNED NOT NULL,
  `sale_invoice_layout_id` int(11) DEFAULT NULL,
  `selling_price_group_id` int(11) DEFAULT NULL,
  `print_receipt_on_invoice` tinyint(1) DEFAULT 1,
  `receipt_printer_type` enum('browser','printer') NOT NULL DEFAULT 'browser',
  `printer_id` int(11) DEFAULT NULL,
  `mobile` varchar(191) DEFAULT NULL,
  `alternate_number` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `website` varchar(191) DEFAULT NULL,
  `featured_products` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `default_payment_accounts` text DEFAULT NULL,
  `custom_field1` varchar(191) DEFAULT NULL,
  `custom_field2` varchar(191) DEFAULT NULL,
  `custom_field3` varchar(191) DEFAULT NULL,
  `custom_field4` varchar(191) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_locations`
--

INSERT INTO `business_locations` (`id`, `business_id`, `location_id`, `name`, `landmark`, `country`, `state`, `city`, `zip_code`, `invoice_scheme_id`, `sale_invoice_scheme_id`, `invoice_layout_id`, `sale_invoice_layout_id`, `selling_price_group_id`, `print_receipt_on_invoice`, `receipt_printer_type`, `printer_id`, `mobile`, `alternate_number`, `email`, `website`, `featured_products`, `is_active`, `default_payment_accounts`, `custom_field1`, `custom_field2`, `custom_field3`, `custom_field4`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'BL0001', 'REENSON AGROVET', NULL, 'Kenya', 'MAKUENI', 'MAKUTANO', '90129', 1, 1, 1, 1, NULL, 1, 'browser', NULL, '0792811944', '0720274781', 'reensongroup@gmail.com', NULL, NULL, 1, '{\"cash\":{\"is_enabled\":\"1\"},\"card\":{\"is_enabled\":\"1\"},\"cheque\":{\"is_enabled\":\"1\"},\"bank_transfer\":{\"is_enabled\":\"1\"},\"mpesa\":{\"is_enabled\":\"1\"},\"other\":{\"is_enabled\":\"1\"},\"custom_pay_1\":{\"is_enabled\":\"1\"},\"custom_pay_2\":{\"is_enabled\":\"1\"},\"custom_pay_3\":{\"is_enabled\":\"1\"},\"custom_pay_4\":{\"is_enabled\":\"1\"},\"custom_pay_5\":{\"is_enabled\":\"1\"},\"custom_pay_6\":{\"is_enabled\":\"1\"},\"custom_pay_7\":{\"is_enabled\":\"1\"}}', NULL, NULL, NULL, NULL, NULL, '2026-04-09 09:53:35', '2026-04-09 12:17:49'),
(2, 1, 'BL0002', 'REENSON KYAANI', 'KYAANI', 'KENYA', 'MAKUENI', 'KYAANI', '100', 1, 1, 1, 1, 0, 1, 'browser', NULL, '0722977550', NULL, 'reenson@gmail.com', NULL, NULL, 1, '{\"cash\":{\"is_enabled\":\"1\"},\"card\":{\"is_enabled\":\"1\"},\"cheque\":{\"is_enabled\":\"1\"},\"bank_transfer\":{\"is_enabled\":\"1\"},\"mpesa\":{\"is_enabled\":\"1\"},\"other\":{\"is_enabled\":\"1\"},\"custom_pay_1\":{\"is_enabled\":\"1\"},\"custom_pay_2\":{\"is_enabled\":\"1\"},\"custom_pay_3\":{\"is_enabled\":\"1\"},\"custom_pay_4\":{\"is_enabled\":\"1\"},\"custom_pay_5\":{\"is_enabled\":\"1\"},\"custom_pay_6\":{\"is_enabled\":\"1\"},\"custom_pay_7\":{\"is_enabled\":\"1\"}}', NULL, NULL, NULL, NULL, NULL, '2026-04-09 11:51:38', '2026-04-09 11:51:38');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_denominations`
--

CREATE TABLE `cash_denominations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `amount` decimal(22,4) NOT NULL,
  `total_count` int(11) NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_registers`
--

CREATE TABLE `cash_registers` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('close','open') NOT NULL DEFAULT 'open',
  `closed_at` datetime DEFAULT NULL,
  `closing_amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `total_card_slips` int(11) NOT NULL DEFAULT 0,
  `total_cheques` int(11) NOT NULL DEFAULT 0,
  `denominations` text DEFAULT NULL,
  `closing_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cash_registers`
--

INSERT INTO `cash_registers` (`id`, `business_id`, `location_id`, `user_id`, `status`, `closed_at`, `closing_amount`, `total_card_slips`, `total_cheques`, `denominations`, `closing_note`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, 'open', NULL, 0.0000, 0, 0, NULL, NULL, '2026-04-09 09:59:00', '2026-04-09 09:59:49'),
(2, 1, 1, 3, 'close', '2026-04-09 14:29:07', 250.0000, 0, 0, NULL, NULL, '2026-04-09 11:26:00', '2026-04-09 11:29:07'),
(3, 1, 1, 3, 'close', '2026-04-09 14:32:59', 0.0000, 0, 0, NULL, NULL, '2026-04-09 11:29:00', '2026-04-09 11:32:59'),
(4, 1, 1, 3, 'open', NULL, 0.0000, 0, 0, NULL, NULL, '2026-04-09 18:30:00', '2026-04-09 18:30:09'),
(5, 1, 2, 4, 'open', NULL, 0.0000, 0, 0, NULL, NULL, '2026-04-13 18:51:00', '2026-04-13 18:51:46');

-- --------------------------------------------------------

--
-- Table structure for table `cash_register_transactions`
--

CREATE TABLE `cash_register_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `cash_register_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `pay_method` varchar(191) DEFAULT NULL,
  `type` enum('debit','credit') NOT NULL,
  `transaction_type` varchar(191) DEFAULT NULL,
  `transaction_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cash_register_transactions`
--

INSERT INTO `cash_register_transactions` (`id`, `cash_register_id`, `amount`, `pay_method`, `type`, `transaction_type`, `transaction_id`, `created_at`, `updated_at`) VALUES
(1, 1, 250.0000, 'cash', 'credit', 'sell', 6, '2026-04-09 10:53:04', '2026-04-09 10:53:04'),
(2, 2, 250.0000, 'cash', 'credit', 'sell', 7, '2026-04-09 11:26:21', '2026-04-09 11:26:21'),
(3, 1, 400.0000, 'cash', 'credit', 'sell', 12, '2026-04-09 12:06:13', '2026-04-09 12:06:13'),
(4, 1, 300.0000, 'cash', 'credit', 'sell', 14, '2026-04-09 16:30:46', '2026-04-09 16:30:46'),
(5, 4, 400.0000, 'cash', 'credit', 'sell', 16, '2026-04-09 18:30:36', '2026-04-09 18:30:36'),
(6, 4, 400.0000, 'cash', 'credit', 'sell', 17, '2026-04-09 19:05:41', '2026-04-09 19:05:41'),
(9, 1, 250.0000, 'cash', 'credit', 'sell', 20, '2026-04-13 09:40:39', '2026-04-13 09:40:39'),
(10, 4, 250.0000, 'cash', 'credit', 'sell', 21, '2026-04-13 13:23:46', '2026-04-13 13:23:46'),
(11, 4, 500.0000, 'cash', 'credit', 'sell', 22, '2026-04-13 13:25:09', '2026-04-13 13:25:09'),
(12, 5, 250.0000, 'cash', 'credit', 'sell', 25, '2026-04-13 19:05:36', '2026-04-13 19:05:36'),
(13, 1, 300.0000, 'cash', 'credit', 'sell', 26, '2026-04-13 19:37:04', '2026-04-13 19:37:04'),
(14, 1, 300.0000, 'cash', 'credit', 'sell', 27, '2026-04-13 19:39:30', '2026-04-13 19:39:30'),
(15, 1, 250.0000, 'cash', 'credit', 'sell', 28, '2026-04-13 19:45:19', '2026-04-13 19:45:19'),
(16, 5, 250.0000, 'cash', 'credit', 'sell', 29, '2026-04-13 19:48:55', '2026-04-13 19:48:55'),
(17, 5, 150.0000, 'cash', 'credit', 'sell', 31, '2026-04-13 20:29:28', '2026-04-13 20:29:28'),
(18, 4, 200.0000, 'cash', 'credit', 'sell', 32, '2026-04-13 20:48:20', '2026-04-13 20:48:20'),
(19, 4, 300.0000, 'cash', 'credit', 'sell', 33, '2026-04-14 18:08:21', '2026-04-14 18:08:21'),
(20, 1, 200.0000, 'cash', 'credit', 'sell', 34, '2026-04-19 18:35:01', '2026-04-19 18:35:01'),
(21, 1, 400.0000, 'cash', 'credit', 'sell', 35, '2026-04-19 18:38:15', '2026-04-19 18:38:15'),
(22, 1, 200.0000, 'cash', 'credit', 'sell', 36, '2026-04-19 18:40:25', '2026-04-19 18:40:25'),
(23, 1, 200.0000, 'cash', 'credit', 'sell', 39, '2026-04-19 19:03:17', '2026-04-19 19:03:17'),
(24, 1, 200.0000, 'cash', 'credit', 'sell', 40, '2026-04-19 20:18:46', '2026-04-19 20:18:46'),
(25, 1, 300.0000, 'cash', 'credit', 'sell', 41, '2026-04-19 20:24:40', '2026-04-19 20:24:40'),
(26, 1, 400.0000, 'cash', 'credit', 'sell', 42, '2026-04-19 20:26:57', '2026-04-19 20:26:57'),
(27, 1, 500.0000, 'cash', 'credit', 'sell', 43, '2026-04-19 20:34:01', '2026-04-19 20:34:01'),
(28, 1, 100.0000, 'cash', 'credit', 'sell', 44, '2026-04-19 20:34:44', '2026-04-19 20:34:44'),
(29, 1, 750.0000, 'cash', 'credit', 'sell', 47, '2026-04-20 15:52:45', '2026-04-20 15:52:45'),
(30, 1, 150.0000, 'cash', 'credit', 'sell', 48, '2026-04-20 15:54:12', '2026-04-20 15:54:12'),
(31, 1, 150.0000, 'cash', 'credit', 'sell', 49, '2026-04-20 15:54:12', '2026-04-20 15:54:12'),
(32, 1, 2050.0000, 'cash', 'credit', 'sell', 50, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(33, 1, 840.0000, 'cash', 'credit', 'sell', 52, '2026-04-20 20:08:25', '2026-04-20 20:08:25');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `short_code` varchar(191) DEFAULT NULL,
  `parent_id` int(11) NOT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `category_type` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(191) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categorizables`
--

CREATE TABLE `categorizables` (
  `category_id` int(11) NOT NULL,
  `categorizable_type` varchar(191) NOT NULL,
  `categorizable_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_token` varchar(64) DEFAULT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(191) NOT NULL,
  `contact_type` varchar(191) DEFAULT NULL,
  `land_mark` varchar(191) DEFAULT NULL,
  `street_name` varchar(191) DEFAULT NULL,
  `building_number` varchar(191) DEFAULT NULL,
  `additional_number` varchar(191) DEFAULT NULL,
  `supplier_business_name` varchar(191) DEFAULT NULL,
  `name` varchar(191) DEFAULT NULL,
  `prefix` varchar(191) DEFAULT NULL,
  `first_name` varchar(191) DEFAULT NULL,
  `middle_name` varchar(191) DEFAULT NULL,
  `last_name` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `contact_id` varchar(191) DEFAULT NULL,
  `is_synced` tinyint(1) NOT NULL DEFAULT 0,
  `contact_status` varchar(191) NOT NULL DEFAULT 'active',
  `tax_number` varchar(191) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `state` varchar(191) DEFAULT NULL,
  `country` varchar(191) DEFAULT NULL,
  `address_line_1` text DEFAULT NULL,
  `address_line_2` text DEFAULT NULL,
  `zip_code` varchar(191) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `mobile` varchar(191) NOT NULL,
  `landline` varchar(191) DEFAULT NULL,
  `alternate_number` varchar(191) DEFAULT NULL,
  `pay_term_number` int(11) DEFAULT NULL,
  `pay_term_type` enum('days','months') DEFAULT NULL,
  `credit_limit` decimal(22,4) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `balance` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `total_rp` int(11) NOT NULL DEFAULT 0 COMMENT 'rp is the short form of reward points',
  `total_rp_used` int(11) NOT NULL DEFAULT 0 COMMENT 'rp is the short form of reward points',
  `total_rp_expired` int(11) NOT NULL DEFAULT 0 COMMENT 'rp is the short form of reward points',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `shipping_address` text DEFAULT NULL,
  `shipping_custom_field_details` longtext DEFAULT NULL,
  `is_export` tinyint(1) NOT NULL DEFAULT 0,
  `export_custom_field_1` varchar(191) DEFAULT NULL,
  `export_custom_field_2` varchar(191) DEFAULT NULL,
  `export_custom_field_3` varchar(191) DEFAULT NULL,
  `export_custom_field_4` varchar(191) DEFAULT NULL,
  `export_custom_field_5` varchar(191) DEFAULT NULL,
  `export_custom_field_6` varchar(191) DEFAULT NULL,
  `position` varchar(191) DEFAULT NULL,
  `customer_group_id` int(11) DEFAULT NULL,
  `custom_field1` varchar(191) DEFAULT NULL,
  `custom_field2` varchar(191) DEFAULT NULL,
  `custom_field3` varchar(191) DEFAULT NULL,
  `custom_field4` varchar(191) DEFAULT NULL,
  `custom_field5` varchar(191) DEFAULT NULL,
  `custom_field6` varchar(191) DEFAULT NULL,
  `custom_field7` varchar(191) DEFAULT NULL,
  `custom_field8` varchar(191) DEFAULT NULL,
  `custom_field9` varchar(191) DEFAULT NULL,
  `custom_field10` varchar(191) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `job_token` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `order_token`, `business_id`, `type`, `contact_type`, `land_mark`, `street_name`, `building_number`, `additional_number`, `supplier_business_name`, `name`, `prefix`, `first_name`, `middle_name`, `last_name`, `email`, `contact_id`, `is_synced`, `contact_status`, `tax_number`, `city`, `state`, `country`, `address_line_1`, `address_line_2`, `zip_code`, `dob`, `mobile`, `landline`, `alternate_number`, `pay_term_number`, `pay_term_type`, `credit_limit`, `created_by`, `balance`, `total_rp`, `total_rp_used`, `total_rp_expired`, `is_default`, `shipping_address`, `shipping_custom_field_details`, `is_export`, `export_custom_field_1`, `export_custom_field_2`, `export_custom_field_3`, `export_custom_field_4`, `export_custom_field_5`, `export_custom_field_6`, `position`, `customer_group_id`, `custom_field1`, `custom_field2`, `custom_field3`, `custom_field4`, `custom_field5`, `custom_field6`, `custom_field7`, `custom_field8`, `custom_field9`, `custom_field10`, `deleted_at`, `created_at`, `updated_at`, `job_token`) VALUES
(1, NULL, 1, 'customer', NULL, NULL, NULL, NULL, NULL, NULL, 'Walk-In Customer', NULL, NULL, NULL, NULL, NULL, 'CO0001', 1, 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, 0.0000, 1, 0.0000, 0, 0, 0, 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 09:53:34', '2026-04-09 09:53:34', NULL),
(2, NULL, 1, 'supplier', 'business', NULL, NULL, NULL, NULL, 'KIMA CHEMICALS LTD', NULL, NULL, NULL, NULL, NULL, NULL, 'CO0002', 1, 'active', 'P051767030N', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0722977550', NULL, NULL, NULL, NULL, NULL, 2, 0.0000, 0, 0, 0, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 11:36:27', '2026-04-09 11:36:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cooler_agreements`
--

CREATE TABLE `cooler_agreements` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `cooler_id` int(10) UNSIGNED NOT NULL,
  `dealer_id` int(10) UNSIGNED NOT NULL,
  `agreement_date` date NOT NULL,
  `sales_volume_target` decimal(12,2) DEFAULT NULL COMMENT 'Monthly sales volume target in KES',
  `status` enum('draft','active','terminated') NOT NULL DEFAULT 'draft',
  `termination_reason` text DEFAULT NULL,
  `termination_date` date DEFAULT NULL,
  `company_signatory_name` varchar(191) DEFAULT NULL,
  `company_signature_path` varchar(191) DEFAULT NULL,
  `company_signed_at` timestamp NULL DEFAULT NULL,
  `rsm_tsm_signatory_name` varchar(191) DEFAULT NULL,
  `rsm_tsm_signature_path` varchar(191) DEFAULT NULL,
  `rsm_tsm_signed_at` timestamp NULL DEFAULT NULL,
  `dealer_signatory_name` varchar(191) DEFAULT NULL,
  `dealer_signature_path` varchar(191) DEFAULT NULL,
  `dealer_signed_at` timestamp NULL DEFAULT NULL,
  `distributor_signatory_name` varchar(191) DEFAULT NULL,
  `distributor_signature_path` varchar(191) DEFAULT NULL,
  `distributor_signed_at` timestamp NULL DEFAULT NULL,
  `generated_pdf_path` varchar(191) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cooler_assets`
--

CREATE TABLE `cooler_assets` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `asset_type` varchar(191) NOT NULL,
  `asset_number` varchar(191) NOT NULL,
  `serial_number` varchar(191) DEFAULT NULL,
  `cooler_tag` varchar(191) DEFAULT NULL,
  `status` enum('available','deployed','under_maintenance','retrieved') NOT NULL DEFAULT 'available',
  `current_dealer_id` int(10) UNSIGNED DEFAULT NULL,
  `deployment_date` date DEFAULT NULL,
  `replacement_value` decimal(12,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cooler_compliance_logs`
--

CREATE TABLE `cooler_compliance_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `dealer_id` int(10) UNSIGNED NOT NULL,
  `cooler_id` int(10) UNSIGNED DEFAULT NULL,
  `check_type` enum('sales_volume','stock_level','exclusivity','placement','document_expiry') NOT NULL,
  `status` enum('compliant','non_compliant') NOT NULL DEFAULT 'compliant',
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `checked_at` timestamp NOT NULL,
  `checked_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cooler_dealers`
--

CREATE TABLE `cooler_dealers` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `contact_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `id_number` varchar(191) NOT NULL,
  `kra_pin` varchar(191) NOT NULL,
  `postal_address` varchar(191) DEFAULT NULL,
  `phone` varchar(191) NOT NULL,
  `outlet_name` varchar(191) NOT NULL,
  `channel` enum('retail','wholesale','supermarket','kiosk') NOT NULL DEFAULT 'retail',
  `building` varchar(191) DEFAULT NULL,
  `road` varchar(191) DEFAULT NULL,
  `area` varchar(191) DEFAULT NULL,
  `years_in_business` int(11) NOT NULL DEFAULT 0,
  `brands_stocked` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`brands_stocked`)),
  `compliance_score` decimal(5,2) NOT NULL DEFAULT 100.00,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_by` int(10) UNSIGNED NOT NULL,
  `agent_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cooler_documents`
--

CREATE TABLE `cooler_documents` (
  `id` int(10) UNSIGNED NOT NULL,
  `documentable_type` varchar(191) NOT NULL,
  `documentable_id` bigint(20) UNSIGNED NOT NULL,
  `document_type` enum('id_copy','kra_pin_certificate','passport_photo','county_business_permit','certificate_of_registration','certificate_of_incorporation','tax_certificate','signed_agreement','retrieval_photo_before','retrieval_photo_during','retrieval_photo_after','acknowledgement_signature','signed_retrieval_letter','other') NOT NULL,
  `file_path` varchar(191) NOT NULL,
  `file_name` varchar(191) NOT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `mime_type` varchar(191) DEFAULT NULL,
  `thumbnail_path` varchar(191) DEFAULT NULL,
  `uploaded_by` int(10) UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `verified_by` int(10) UNSIGNED DEFAULT NULL,
  `verification_notes` text DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'EXIF data, GPS, photo phase, etc.' CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cooler_retrievals`
--

CREATE TABLE `cooler_retrievals` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `agreement_id` int(10) UNSIGNED DEFAULT NULL,
  `cooler_id` int(10) UNSIGNED NOT NULL,
  `dealer_id` int(10) UNSIGNED NOT NULL,
  `retrieval_date` date NOT NULL,
  `reason` enum('not_purchasing_from_stockist','not_stocking_to_capacity','not_displaying_sbck_products_only','unauthorized_rebrand_or_relocation','business_ownership_change','liquidation','company_discretion','other') NOT NULL,
  `reason_notes` text DEFAULT NULL,
  `authorized_staff_name` varchar(191) NOT NULL,
  `authorized_staff_id_no` varchar(191) DEFAULT NULL,
  `authorized_staff_tel` varchar(191) DEFAULT NULL,
  `customer_signature_path` varchar(191) DEFAULT NULL,
  `acknowledgement_date` timestamp NULL DEFAULT NULL,
  `gps_coordinates` varchar(191) DEFAULT NULL,
  `retrieval_letter_path` varchar(191) DEFAULT NULL,
  `status` enum('initiated','in_progress','completed') NOT NULL DEFAULT 'initiated',
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(10) UNSIGNED NOT NULL,
  `country` varchar(100) NOT NULL,
  `currency` varchar(100) NOT NULL,
  `code` varchar(25) NOT NULL,
  `symbol` varchar(25) NOT NULL,
  `thousand_separator` varchar(10) NOT NULL,
  `decimal_separator` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `country`, `currency`, `code`, `symbol`, `thousand_separator`, `decimal_separator`, `created_at`, `updated_at`) VALUES
(1, 'Albania', 'Leke', 'ALL', 'Lek', ',', '.', NULL, NULL),
(2, 'America', 'Dollars', 'USD', '$', ',', '.', NULL, NULL),
(3, 'Afghanistan', 'Afghanis', 'AF', '؋', ',', '.', NULL, NULL),
(4, 'Argentina', 'Pesos', 'ARS', '$', ',', '.', NULL, NULL),
(5, 'Aruba', 'Guilders', 'AWG', 'ƒ', ',', '.', NULL, NULL),
(6, 'Australia', 'Dollars', 'AUD', '$', ',', '.', NULL, NULL),
(7, 'Azerbaijan', 'New Manats', 'AZ', 'ман', ',', '.', NULL, NULL),
(8, 'Bahamas', 'Dollars', 'BSD', '$', ',', '.', NULL, NULL),
(9, 'Barbados', 'Dollars', 'BBD', '$', ',', '.', NULL, NULL),
(10, 'Belarus', 'Rubles', 'BYR', 'p.', ',', '.', NULL, NULL),
(11, 'Belgium', 'Euro', 'EUR', '€', ',', '.', NULL, NULL),
(12, 'Beliz', 'Dollars', 'BZD', 'BZ$', ',', '.', NULL, NULL),
(13, 'Bermuda', 'Dollars', 'BMD', '$', ',', '.', NULL, NULL),
(14, 'Bolivia', 'Bolivianos', 'BOB', '$b', ',', '.', NULL, NULL),
(15, 'Bosnia and Herzegovina', 'Convertible Marka', 'BAM', 'KM', ',', '.', NULL, NULL),
(16, 'Botswana', 'Pula\'s', 'BWP', 'P', ',', '.', NULL, NULL),
(17, 'Bulgaria', 'Leva', 'BG', 'лв', ',', '.', NULL, NULL),
(18, 'Brazil', 'Reais', 'BRL', 'R$', ',', '.', NULL, NULL),
(19, 'Britain [United Kingdom]', 'Pounds', 'GBP', '£', ',', '.', NULL, NULL),
(20, 'Brunei Darussalam', 'Dollars', 'BND', '$', ',', '.', NULL, NULL),
(21, 'Cambodia', 'Riels', 'KHR', '៛', ',', '.', NULL, NULL),
(22, 'Canada', 'Dollars', 'CAD', '$', ',', '.', NULL, NULL),
(23, 'Cayman Islands', 'Dollars', 'KYD', '$', ',', '.', NULL, NULL),
(24, 'Chile', 'Pesos', 'CLP', '$', ',', '.', NULL, NULL),
(25, 'China', 'Yuan Renminbi', 'CNY', '¥', ',', '.', NULL, NULL),
(26, 'Colombia', 'Pesos', 'COP', '$', ',', '.', NULL, NULL),
(27, 'Costa Rica', 'Colón', 'CRC', '₡', ',', '.', NULL, NULL),
(28, 'Croatia', 'Kuna', 'HRK', 'kn', ',', '.', NULL, NULL),
(29, 'Cuba', 'Pesos', 'CUP', '₱', ',', '.', NULL, NULL),
(30, 'Cyprus', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(31, 'Czech Republic', 'Koruny', 'CZK', 'Kč', ',', '.', NULL, NULL),
(32, 'Denmark', 'Kroner', 'DKK', 'kr', ',', '.', NULL, NULL),
(33, 'Dominican Republic', 'Pesos', 'DOP ', 'RD$', ',', '.', NULL, NULL),
(34, 'East Caribbean', 'Dollars', 'XCD', '$', ',', '.', NULL, NULL),
(35, 'Egypt', 'Pounds', 'EGP', '£', ',', '.', NULL, NULL),
(36, 'El Salvador', 'Colones', 'SVC', '$', ',', '.', NULL, NULL),
(37, 'England [United Kingdom]', 'Pounds', 'GBP', '£', ',', '.', NULL, NULL),
(38, 'Euro', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(39, 'Falkland Islands', 'Pounds', 'FKP', '£', ',', '.', NULL, NULL),
(40, 'Fiji', 'Dollars', 'FJD', '$', ',', '.', NULL, NULL),
(41, 'France', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(42, 'Ghana', 'Cedis', 'GHS', '¢', ',', '.', NULL, NULL),
(43, 'Gibraltar', 'Pounds', 'GIP', '£', ',', '.', NULL, NULL),
(44, 'Greece', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(45, 'Guatemala', 'Quetzales', 'GTQ', 'Q', ',', '.', NULL, NULL),
(46, 'Guernsey', 'Pounds', 'GGP', '£', ',', '.', NULL, NULL),
(47, 'Guyana', 'Dollars', 'GYD', '$', ',', '.', NULL, NULL),
(48, 'Holland [Netherlands]', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(49, 'Honduras', 'Lempiras', 'HNL', 'L', ',', '.', NULL, NULL),
(50, 'Hong Kong', 'Dollars', 'HKD', '$', ',', '.', NULL, NULL),
(51, 'Hungary', 'Forint', 'HUF', 'Ft', ',', '.', NULL, NULL),
(52, 'Iceland', 'Kronur', 'ISK', 'kr', ',', '.', NULL, NULL),
(53, 'India', 'Rupees', 'INR', '₹', ',', '.', NULL, NULL),
(54, 'Indonesia', 'Rupiahs', 'IDR', 'Rp', ',', '.', NULL, NULL),
(55, 'Iran', 'Rials', 'IRR', '﷼', ',', '.', NULL, NULL),
(56, 'Ireland', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(57, 'Isle of Man', 'Pounds', 'IMP', '£', ',', '.', NULL, NULL),
(58, 'Israel', 'New Shekels', 'ILS', '₪', ',', '.', NULL, NULL),
(59, 'Italy', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(60, 'Jamaica', 'Dollars', 'JMD', 'J$', ',', '.', NULL, NULL),
(61, 'Japan', 'Yen', 'JPY', '¥', ',', '.', NULL, NULL),
(62, 'Jersey', 'Pounds', 'JEP', '£', ',', '.', NULL, NULL),
(63, 'Kazakhstan', 'Tenge', 'KZT', 'лв', ',', '.', NULL, NULL),
(64, 'Korea [North]', 'Won', 'KPW', '₩', ',', '.', NULL, NULL),
(65, 'Korea [South]', 'Won', 'KRW', '₩', ',', '.', NULL, NULL),
(66, 'Kyrgyzstan', 'Soms', 'KGS', 'лв', ',', '.', NULL, NULL),
(67, 'Laos', 'Kips', 'LAK', '₭', ',', '.', NULL, NULL),
(68, 'Latvia', 'Lati', 'LVL', 'Ls', ',', '.', NULL, NULL),
(69, 'Lebanon', 'Pounds', 'LBP', '£', ',', '.', NULL, NULL),
(70, 'Liberia', 'Dollars', 'LRD', '$', ',', '.', NULL, NULL),
(71, 'Liechtenstein', 'Switzerland Francs', 'CHF', 'CHF', ',', '.', NULL, NULL),
(72, 'Lithuania', 'Litai', 'LTL', 'Lt', ',', '.', NULL, NULL),
(73, 'Luxembourg', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(74, 'Macedonia', 'Denars', 'MKD', 'ден', ',', '.', NULL, NULL),
(75, 'Malaysia', 'Ringgits', 'MYR', 'RM', ',', '.', NULL, NULL),
(76, 'Malta', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(77, 'Mauritius', 'Rupees', 'MUR', '₨', ',', '.', NULL, NULL),
(78, 'Mexico', 'Pesos', 'MXN', '$', ',', '.', NULL, NULL),
(79, 'Mongolia', 'Tugriks', 'MNT', '₮', ',', '.', NULL, NULL),
(80, 'Mozambique', 'Meticais', 'MZ', 'MT', ',', '.', NULL, NULL),
(81, 'Namibia', 'Dollars', 'NAD', '$', ',', '.', NULL, NULL),
(82, 'Nepal', 'Rupees', 'NPR', '₨', ',', '.', NULL, NULL),
(83, 'Netherlands Antilles', 'Guilders', 'ANG', 'ƒ', ',', '.', NULL, NULL),
(84, 'Netherlands', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(85, 'New Zealand', 'Dollars', 'NZD', '$', ',', '.', NULL, NULL),
(86, 'Nicaragua', 'Cordobas', 'NIO', 'C$', ',', '.', NULL, NULL),
(87, 'Nigeria', 'Nairas', 'NGN', '₦', ',', '.', NULL, NULL),
(88, 'North Korea', 'Won', 'KPW', '₩', ',', '.', NULL, NULL),
(89, 'Norway', 'Krone', 'NOK', 'kr', ',', '.', NULL, NULL),
(90, 'Oman', 'Rials', 'OMR', '﷼', ',', '.', NULL, NULL),
(91, 'Pakistan', 'Rupees', 'PKR', '₨', ',', '.', NULL, NULL),
(92, 'Panama', 'Balboa', 'PAB', 'B/.', ',', '.', NULL, NULL),
(93, 'Paraguay', 'Guarani', 'PYG', 'Gs', ',', '.', NULL, NULL),
(94, 'Peru', 'Nuevos Soles', 'PE', 'S/.', ',', '.', NULL, NULL),
(95, 'Philippines', 'Pesos', 'PHP', 'Php', ',', '.', NULL, NULL),
(96, 'Poland', 'Zlotych', 'PL', 'zł', ',', '.', NULL, NULL),
(97, 'Qatar', 'Rials', 'QAR', '﷼', ',', '.', NULL, NULL),
(98, 'Romania', 'New Lei', 'RO', 'lei', ',', '.', NULL, NULL),
(99, 'Russia', 'Rubles', 'RUB', 'руб', ',', '.', NULL, NULL),
(100, 'Saint Helena', 'Pounds', 'SHP', '£', ',', '.', NULL, NULL),
(101, 'Saudi Arabia', 'Riyals', 'SAR', '﷼', ',', '.', NULL, NULL),
(102, 'Serbia', 'Dinars', 'RSD', 'Дин.', ',', '.', NULL, NULL),
(103, 'Seychelles', 'Rupees', 'SCR', '₨', ',', '.', NULL, NULL),
(104, 'Singapore', 'Dollars', 'SGD', '$', ',', '.', NULL, NULL),
(105, 'Slovenia', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(106, 'Solomon Islands', 'Dollars', 'SBD', '$', ',', '.', NULL, NULL),
(107, 'Somalia', 'Shillings', 'SOS', 'S', ',', '.', NULL, NULL),
(108, 'South Africa', 'Rand', 'ZAR', 'R', ',', '.', NULL, NULL),
(109, 'South Korea', 'Won', 'KRW', '₩', ',', '.', NULL, NULL),
(110, 'Spain', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(111, 'Sri Lanka', 'Rupees', 'LKR', '₨', ',', '.', NULL, NULL),
(112, 'Sweden', 'Kronor', 'SEK', 'kr', ',', '.', NULL, NULL),
(113, 'Switzerland', 'Francs', 'CHF', 'CHF', ',', '.', NULL, NULL),
(114, 'Suriname', 'Dollars', 'SRD', '$', ',', '.', NULL, NULL),
(115, 'Syria', 'Pounds', 'SYP', '£', ',', '.', NULL, NULL),
(116, 'Taiwan', 'New Dollars', 'TWD', 'NT$', ',', '.', NULL, NULL),
(117, 'Thailand', 'Baht', 'THB', '฿', ',', '.', NULL, NULL),
(118, 'Trinidad and Tobago', 'Dollars', 'TTD', 'TT$', ',', '.', NULL, NULL),
(119, 'Turkey', 'Lira', 'TRY', 'TL', ',', '.', NULL, NULL),
(120, 'Turkey', 'Liras', 'TRL', '£', ',', '.', NULL, NULL),
(121, 'Tuvalu', 'Dollars', 'TVD', '$', ',', '.', NULL, NULL),
(122, 'Ukraine', 'Hryvnia', 'UAH', '₴', ',', '.', NULL, NULL),
(123, 'United Kingdom', 'Pounds', 'GBP', '£', ',', '.', NULL, NULL),
(124, 'United States of America', 'Dollars', 'USD', '$', ',', '.', NULL, NULL),
(125, 'Uruguay', 'Pesos', 'UYU', '$U', ',', '.', NULL, NULL),
(126, 'Uzbekistan', 'Sums', 'UZS', 'лв', ',', '.', NULL, NULL),
(127, 'Vatican City', 'Euro', 'EUR', '€', '.', ',', NULL, NULL),
(128, 'Venezuela', 'Bolivares Fuertes', 'VEF', 'Bs', ',', '.', NULL, NULL),
(129, 'Vietnam', 'Dong', 'VND', '₫', ',', '.', NULL, NULL),
(130, 'Yemen', 'Rials', 'YER', '﷼', ',', '.', NULL, NULL),
(131, 'Zimbabwe', 'Zimbabwe Dollars', 'ZWD', 'Z$', ',', '.', NULL, NULL),
(132, 'Iraq', 'Iraqi dinar', 'IQD', 'د.ع', ',', '.', NULL, NULL),
(133, 'Kenya', 'Kenyan shilling', 'KES', 'KSh', ',', '.', NULL, NULL),
(134, 'Bangladesh', 'Taka', 'BDT', '৳', ',', '.', NULL, NULL),
(135, 'Algerie', 'Algerian dinar', 'DZD', 'د.ج', ' ', '.', NULL, NULL),
(136, 'United Arab Emirates', 'United Arab Emirates dirham', 'AED', 'د.إ', ',', '.', NULL, NULL),
(137, 'Uganda', 'Uganda shillings', 'UGX', 'USh', ',', '.', NULL, NULL),
(138, 'Tanzania', 'Tanzanian shilling', 'TZS', 'TSh', ',', '.', NULL, NULL),
(139, 'Angola', 'Kwanza', 'AOA', 'Kz', ',', '.', NULL, NULL),
(140, 'Kuwait', 'Kuwaiti dinar', 'KWD', 'KD', ',', '.', NULL, NULL),
(141, 'Bahrain', 'Bahraini dinar', 'BHD', 'BD', ',', '.', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_groups`
--

CREATE TABLE `customer_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `amount` double(5,2) NOT NULL,
  `price_calculation_type` varchar(191) DEFAULT 'percentage',
  `selling_price_group_id` int(11) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dashboard_configurations`
--

CREATE TABLE `dashboard_configurations` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `created_by` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `color` varchar(191) NOT NULL,
  `configuration` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dda_destruction_log`
--

CREATE TABLE `dda_destruction_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `dda_drug_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` decimal(22,4) NOT NULL,
  `unit` varchar(191) DEFAULT NULL,
  `reason` enum('expired','damaged','contaminated','recalled','other') NOT NULL,
  `destruction_date` date NOT NULL,
  `destruction_method` varchar(191) DEFAULT NULL,
  `witness_1` int(10) UNSIGNED DEFAULT NULL,
  `witness_2` int(10) UNSIGNED DEFAULT NULL,
  `ppb_officer` varchar(191) DEFAULT NULL,
  `certificate_number` varchar(191) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dda_dispense_log`
--

CREATE TABLE `dda_dispense_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `dda_drug_id` bigint(20) UNSIGNED DEFAULT NULL,
  `prescription_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(191) NOT NULL,
  `customer_id_number` varchar(191) DEFAULT NULL,
  `customer_phone` varchar(191) DEFAULT NULL,
  `quantity` decimal(22,4) NOT NULL,
  `unit` varchar(191) DEFAULT NULL,
  `dispensed_by` int(10) UNSIGNED DEFAULT NULL,
  `verified_by` int(10) UNSIGNED DEFAULT NULL,
  `prescriber_name` varchar(191) DEFAULT NULL,
  `prescriber_license` varchar(191) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dda_drugs`
--

CREATE TABLE `dda_drugs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `class` varchar(191) NOT NULL,
  `schedule` varchar(191) NOT NULL DEFAULT 'II',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dda_prescriptions`
--

CREATE TABLE `dda_prescriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(191) DEFAULT NULL,
  `customer_id_number` varchar(191) DEFAULT NULL,
  `customer_phone` varchar(191) DEFAULT NULL,
  `prescriber_name` varchar(191) NOT NULL,
  `prescriber_license` varchar(191) DEFAULT NULL,
  `prescriber_hospital` varchar(191) DEFAULT NULL,
  `prescription_image` varchar(191) NOT NULL,
  `dispensed_by` int(10) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dda_stock_log`
--

CREATE TABLE `dda_stock_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `dda_drug_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('in','out','adjustment','destruction') NOT NULL,
  `quantity` decimal(22,4) NOT NULL,
  `reference` varchar(191) DEFAULT NULL,
  `supplier_name` varchar(191) DEFAULT NULL,
  `supplier_license` varchar(191) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recorded_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `business_id` int(11) NOT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `discount_type` varchar(191) DEFAULT NULL,
  `discount_amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `spg` varchar(100) DEFAULT NULL COMMENT 'Applicable in specified selling price group only. Use of applicable_in_spg column is discontinued',
  `applicable_in_cg` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discount_variations`
--

CREATE TABLE `discount_variations` (
  `discount_id` int(11) NOT NULL,
  `variation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_and_notes`
--

CREATE TABLE `document_and_notes` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `notable_id` int(11) NOT NULL,
  `notable_type` varchar(191) NOT NULL,
  `heading` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_private` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(191) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `followups`
--

CREATE TABLE `followups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `location_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `variation_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `customer_phone` varchar(50) NOT NULL,
  `customer_name` varchar(191) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'pending',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_sub_taxes`
--

CREATE TABLE `group_sub_taxes` (
  `group_tax_id` int(10) UNSIGNED NOT NULL,
  `tax_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_admissions`
--

CREATE TABLE `hospital_admissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `bed_id` int(10) UNSIGNED NOT NULL,
  `admitted_by` int(10) UNSIGNED NOT NULL,
  `admission_date` datetime NOT NULL,
  `discharge_date` datetime DEFAULT NULL,
  `reason_for_admission` text DEFAULT NULL,
  `status` enum('admitted','discharged') NOT NULL DEFAULT 'admitted',
  `transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_anc_visits`
--

CREATE TABLE `hospital_anc_visits` (
  `id` int(10) UNSIGNED NOT NULL,
  `pregnancy_profile_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `visit_date` date NOT NULL,
  `weight` decimal(8,2) DEFAULT NULL,
  `bp` varchar(191) DEFAULT NULL,
  `fundal_height` decimal(8,2) DEFAULT NULL,
  `fetal_presentation` varchar(191) DEFAULT NULL,
  `fetal_heart_rate` varchar(191) DEFAULT NULL,
  `tt_dose` varchar(191) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_appointments`
--

CREATE TABLE `hospital_appointments` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED DEFAULT NULL,
  `appointment_date` datetime NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_assets`
--

CREATE TABLE `hospital_assets` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `asset_code` varchar(191) NOT NULL,
  `model` varchar(191) DEFAULT NULL,
  `serial_number` varchar(191) DEFAULT NULL,
  `category` varchar(191) DEFAULT NULL,
  `location_id` int(10) UNSIGNED DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `warranty_expiry` date DEFAULT NULL,
  `status` enum('active','under_maintenance','disposed','broken') NOT NULL DEFAULT 'active',
  `next_service_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_asset_maintenance`
--

CREATE TABLE `hospital_asset_maintenance` (
  `id` int(10) UNSIGNED NOT NULL,
  `asset_id` int(10) UNSIGNED NOT NULL,
  `service_date` date NOT NULL,
  `service_type` varchar(191) NOT NULL,
  `details` text DEFAULT NULL,
  `cost` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `performed_by` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_beds`
--

CREATE TABLE `hospital_beds` (
  `id` int(10) UNSIGNED NOT NULL,
  `ward_id` int(10) UNSIGNED NOT NULL,
  `bed_number` varchar(191) NOT NULL,
  `status` enum('available','occupied','maintenance') NOT NULL DEFAULT 'available',
  `daily_rate` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_bills`
--

CREATE TABLE `hospital_bills` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED DEFAULT NULL,
  `bill_number` varchar(191) NOT NULL,
  `patient_id` int(10) UNSIGNED DEFAULT NULL,
  `patient_name` varchar(191) NOT NULL,
  `patient_phone` varchar(191) DEFAULT NULL,
  `patient_dob` varchar(191) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `nhif_number` varchar(191) DEFAULT NULL,
  `doctor_name` varchar(191) DEFAULT NULL,
  `visit_date` date NOT NULL,
  `visit_type` enum('outpatient','inpatient','emergency') NOT NULL DEFAULT 'outpatient',
  `diagnosis` text DEFAULT NULL,
  `bill_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`bill_items`)),
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `nhif_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `payment_method` enum('cash','mpesa','nhif','insurance','card') NOT NULL DEFAULT 'cash',
  `mpesa_code` varchar(191) DEFAULT NULL,
  `status` enum('draft','active','cancelled') NOT NULL DEFAULT 'active',
  `created_by` int(10) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_consultations`
--

CREATE TABLE `hospital_consultations` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `appointment_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `vitals` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vitals`)),
  `symptoms` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `prescription` text DEFAULT NULL,
  `advice` text DEFAULT NULL,
  `status` enum('ongoing','completed') NOT NULL DEFAULT 'ongoing',
  `consultation_fee` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_daily_records`
--

CREATE TABLE `hospital_daily_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `admission_id` int(10) UNSIGNED NOT NULL,
  `recorded_by` int(10) UNSIGNED NOT NULL,
  `vitals` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`vitals`)),
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_dental_procedures`
--

CREATE TABLE `hospital_dental_procedures` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `consultation_id` int(10) UNSIGNED DEFAULT NULL,
  `procedure_name` varchar(191) NOT NULL,
  `price` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `status` enum('planned','completed','cancelled') NOT NULL DEFAULT 'planned',
  `transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_dental_teeth`
--

CREATE TABLE `hospital_dental_teeth` (
  `id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `tooth_number` varchar(191) NOT NULL,
  `status` varchar(191) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_insurance_schemes`
--

CREATE TABLE `hospital_insurance_schemes` (
  `id` int(10) UNSIGNED NOT NULL,
  `insurer_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `co_payment_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `co_payment_fixed` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_insurers`
--

CREATE TABLE `hospital_insurers` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `contact_person` varchar(191) DEFAULT NULL,
  `phone` varchar(191) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_lab_requests`
--

CREATE TABLE `hospital_lab_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `test_id` int(10) UNSIGNED NOT NULL,
  `consultation_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('ordered','paid','sample_collected','completed','cancelled') NOT NULL DEFAULT 'ordered',
  `result_notes` text DEFAULT NULL,
  `result_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`result_data`)),
  `lab_tech_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_lab_tests`
--

CREATE TABLE `hospital_lab_tests` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `short_name` varchar(191) DEFAULT NULL,
  `price` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `result_unit` varchar(191) DEFAULT NULL,
  `normal_range` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_mortuary_records`
--

CREATE TABLE `hospital_mortuary_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `body_name` varchar(191) DEFAULT NULL,
  `patient_id` int(10) UNSIGNED DEFAULT NULL,
  `admitted_at` datetime NOT NULL,
  `released_at` datetime DEFAULT NULL,
  `relative_name` varchar(191) DEFAULT NULL,
  `relative_phone` varchar(191) DEFAULT NULL,
  `storage_location` varchar(191) DEFAULT NULL,
  `cause_of_death` varchar(191) DEFAULT NULL,
  `status` enum('admitted','released') NOT NULL DEFAULT 'admitted',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_nursing_notes`
--

CREATE TABLE `hospital_nursing_notes` (
  `id` int(10) UNSIGNED NOT NULL,
  `admission_id` int(10) UNSIGNED NOT NULL,
  `noted_at` datetime NOT NULL,
  `observation` text NOT NULL,
  `action_taken` text DEFAULT NULL,
  `fluid_input_ml` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fluid_output_ml` decimal(10,2) NOT NULL DEFAULT 0.00,
  `nurse_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_physio_plans`
--

CREATE TABLE `hospital_physio_plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `diagnosis` varchar(191) NOT NULL,
  `treatment_goals` text DEFAULT NULL,
  `total_sessions_planned` int(11) NOT NULL DEFAULT 1,
  `status` enum('active','completed','discontinued') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_physio_sessions`
--

CREATE TABLE `hospital_physio_sessions` (
  `id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `session_date` datetime NOT NULL,
  `exercises_performed` text DEFAULT NULL,
  `progress_notes` text DEFAULT NULL,
  `therapist_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_pregnancy_profiles`
--

CREATE TABLE `hospital_pregnancy_profiles` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `lmp_date` date DEFAULT NULL,
  `edd_date` date DEFAULT NULL,
  `gravida` int(11) DEFAULT NULL,
  `parity` int(11) DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `status` enum('ongoing','delivered','terminated') NOT NULL DEFAULT 'ongoing',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_prescriptions`
--

CREATE TABLE `hospital_prescriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `consultation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `variation_id` int(10) UNSIGNED DEFAULT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `drug_name` varchar(191) DEFAULT NULL,
  `dosage` varchar(191) DEFAULT NULL,
  `frequency` varchar(191) DEFAULT NULL,
  `duration` varchar(191) DEFAULT NULL,
  `quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `dispensed_quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `notes` text DEFAULT NULL,
  `status` enum('pending','dispensed','partially_dispensed','cancelled') NOT NULL DEFAULT 'pending',
  `dispensed_at` timestamp NULL DEFAULT NULL,
  `dispensed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_queue`
--

CREATE TABLE `hospital_queue` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `appointment_id` int(10) UNSIGNED DEFAULT NULL,
  `token_number` varchar(191) NOT NULL,
  `triage_vitals` text DEFAULT NULL,
  `triage_notes` text DEFAULT NULL,
  `current_location` enum('triage','consultation','laboratory','pharmacy','billing','completed') NOT NULL DEFAULT 'triage',
  `status` enum('waiting','serving','paused','completed') NOT NULL DEFAULT 'waiting',
  `assigned_to` int(10) UNSIGNED DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_radiography_requests`
--

CREATE TABLE `hospital_radiography_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `test_id` int(10) UNSIGNED NOT NULL,
  `consultation_id` int(10) UNSIGNED DEFAULT NULL,
  `transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('ordered','paid','completed','cancelled') NOT NULL DEFAULT 'ordered',
  `clinical_history` text DEFAULT NULL,
  `radiologist_findings` text DEFAULT NULL,
  `conclusion` text DEFAULT NULL,
  `radiologist_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_radiography_tests`
--

CREATE TABLE `hospital_radiography_tests` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `short_name` varchar(191) DEFAULT NULL,
  `type` enum('x-ray','ultrasound','ct-scan','mri','other') NOT NULL DEFAULT 'x-ray',
  `price` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_surgeries`
--

CREATE TABLE `hospital_surgeries` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_theatres`
--

CREATE TABLE `hospital_theatres` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `location` varchar(191) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_theatre_bookings`
--

CREATE TABLE `hospital_theatre_bookings` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `surgery_id` int(10) UNSIGNED NOT NULL,
  `theatre_id` int(10) UNSIGNED NOT NULL,
  `surgeon_id` int(10) UNSIGNED NOT NULL,
  `anaesthetist_id` int(10) UNSIGNED DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `started_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `pre_op_diagnosis` text DEFAULT NULL,
  `post_op_diagnosis` text DEFAULT NULL,
  `procedure_notes` text DEFAULT NULL,
  `complications` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospital_wards`
--

CREATE TABLE `hospital_wards` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_layouts`
--

CREATE TABLE `invoice_layouts` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `header_text` text DEFAULT NULL,
  `invoice_no_prefix` varchar(191) DEFAULT NULL,
  `quotation_no_prefix` varchar(191) DEFAULT NULL,
  `invoice_heading` varchar(191) DEFAULT NULL,
  `sub_heading_line1` varchar(191) DEFAULT NULL,
  `sub_heading_line2` varchar(191) DEFAULT NULL,
  `sub_heading_line3` varchar(191) DEFAULT NULL,
  `sub_heading_line4` varchar(191) DEFAULT NULL,
  `sub_heading_line5` varchar(191) DEFAULT NULL,
  `invoice_heading_not_paid` varchar(191) DEFAULT NULL,
  `invoice_heading_paid` varchar(191) DEFAULT NULL,
  `quotation_heading` varchar(191) DEFAULT NULL,
  `sub_total_label` varchar(191) DEFAULT NULL,
  `discount_label` varchar(191) DEFAULT NULL,
  `tax_label` varchar(191) DEFAULT NULL,
  `total_label` varchar(191) DEFAULT NULL,
  `round_off_label` varchar(191) DEFAULT NULL,
  `total_due_label` varchar(191) DEFAULT NULL,
  `paid_label` varchar(191) DEFAULT NULL,
  `show_client_id` tinyint(1) NOT NULL DEFAULT 0,
  `client_id_label` varchar(191) DEFAULT NULL,
  `client_tax_label` varchar(191) DEFAULT NULL,
  `date_label` varchar(191) DEFAULT NULL,
  `date_time_format` varchar(191) DEFAULT NULL,
  `show_time` tinyint(1) NOT NULL DEFAULT 1,
  `show_brand` tinyint(1) NOT NULL DEFAULT 0,
  `show_sku` tinyint(1) NOT NULL DEFAULT 1,
  `show_cat_code` tinyint(1) NOT NULL DEFAULT 1,
  `show_expiry` tinyint(1) NOT NULL DEFAULT 0,
  `show_lot` tinyint(1) NOT NULL DEFAULT 0,
  `show_image` tinyint(1) NOT NULL DEFAULT 0,
  `show_sale_description` tinyint(1) NOT NULL DEFAULT 0,
  `sales_person_label` varchar(191) DEFAULT NULL,
  `show_sales_person` tinyint(1) NOT NULL DEFAULT 0,
  `table_product_label` varchar(191) DEFAULT NULL,
  `table_qty_label` varchar(191) DEFAULT NULL,
  `table_unit_price_label` varchar(191) DEFAULT NULL,
  `table_subtotal_label` varchar(191) DEFAULT NULL,
  `cat_code_label` varchar(191) DEFAULT NULL,
  `logo` varchar(191) DEFAULT NULL,
  `show_logo` tinyint(1) NOT NULL DEFAULT 0,
  `show_business_name` tinyint(1) NOT NULL DEFAULT 0,
  `show_location_name` tinyint(1) NOT NULL DEFAULT 1,
  `show_landmark` tinyint(1) NOT NULL DEFAULT 1,
  `show_city` tinyint(1) NOT NULL DEFAULT 1,
  `show_state` tinyint(1) NOT NULL DEFAULT 1,
  `show_zip_code` tinyint(1) NOT NULL DEFAULT 1,
  `show_country` tinyint(1) NOT NULL DEFAULT 1,
  `show_mobile_number` tinyint(1) NOT NULL DEFAULT 1,
  `show_alternate_number` tinyint(1) NOT NULL DEFAULT 0,
  `show_email` tinyint(1) NOT NULL DEFAULT 0,
  `show_tax_1` tinyint(1) NOT NULL DEFAULT 1,
  `show_tax_2` tinyint(1) NOT NULL DEFAULT 0,
  `show_barcode` tinyint(1) NOT NULL DEFAULT 0,
  `show_payments` tinyint(1) NOT NULL DEFAULT 0,
  `show_customer` tinyint(1) NOT NULL DEFAULT 0,
  `customer_label` varchar(191) DEFAULT NULL,
  `commission_agent_label` varchar(191) DEFAULT NULL,
  `show_commission_agent` tinyint(1) NOT NULL DEFAULT 0,
  `show_reward_point` tinyint(1) NOT NULL DEFAULT 0,
  `highlight_color` varchar(10) DEFAULT NULL,
  `footer_text` text DEFAULT NULL,
  `module_info` text DEFAULT NULL,
  `common_settings` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `business_id` int(10) UNSIGNED NOT NULL,
  `show_letter_head` tinyint(1) NOT NULL DEFAULT 0,
  `letter_head` varchar(191) DEFAULT NULL,
  `show_qr_code` tinyint(1) NOT NULL DEFAULT 0,
  `qr_code_fields` text DEFAULT NULL,
  `design` varchar(190) DEFAULT 'classic',
  `cn_heading` varchar(191) DEFAULT NULL COMMENT 'cn = credit note',
  `cn_no_label` varchar(191) DEFAULT NULL,
  `cn_amount_label` varchar(191) DEFAULT NULL,
  `table_tax_headings` text DEFAULT NULL,
  `show_previous_bal` tinyint(1) NOT NULL DEFAULT 0,
  `prev_bal_label` varchar(191) DEFAULT NULL,
  `show_previous_balance_due` tinyint(1) NOT NULL DEFAULT 0,
  `previous_balance_due_label` varchar(191) DEFAULT NULL,
  `change_return_label` varchar(191) DEFAULT NULL,
  `product_custom_fields` text DEFAULT NULL,
  `contact_custom_fields` text DEFAULT NULL,
  `location_custom_fields` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_layouts`
--

INSERT INTO `invoice_layouts` (`id`, `name`, `header_text`, `invoice_no_prefix`, `quotation_no_prefix`, `invoice_heading`, `sub_heading_line1`, `sub_heading_line2`, `sub_heading_line3`, `sub_heading_line4`, `sub_heading_line5`, `invoice_heading_not_paid`, `invoice_heading_paid`, `quotation_heading`, `sub_total_label`, `discount_label`, `tax_label`, `total_label`, `round_off_label`, `total_due_label`, `paid_label`, `show_client_id`, `client_id_label`, `client_tax_label`, `date_label`, `date_time_format`, `show_time`, `show_brand`, `show_sku`, `show_cat_code`, `show_expiry`, `show_lot`, `show_image`, `show_sale_description`, `sales_person_label`, `show_sales_person`, `table_product_label`, `table_qty_label`, `table_unit_price_label`, `table_subtotal_label`, `cat_code_label`, `logo`, `show_logo`, `show_business_name`, `show_location_name`, `show_landmark`, `show_city`, `show_state`, `show_zip_code`, `show_country`, `show_mobile_number`, `show_alternate_number`, `show_email`, `show_tax_1`, `show_tax_2`, `show_barcode`, `show_payments`, `show_customer`, `customer_label`, `commission_agent_label`, `show_commission_agent`, `show_reward_point`, `highlight_color`, `footer_text`, `module_info`, `common_settings`, `is_default`, `business_id`, `show_letter_head`, `letter_head`, `show_qr_code`, `qr_code_fields`, `design`, `cn_heading`, `cn_no_label`, `cn_amount_label`, `table_tax_headings`, `show_previous_bal`, `prev_bal_label`, `show_previous_balance_due`, `previous_balance_due_label`, `change_return_label`, `product_custom_fields`, `contact_custom_fields`, `location_custom_fields`, `created_at`, `updated_at`) VALUES
(1, 'Default', NULL, 'Receipt No.', NULL, 'Receipt', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Subtotal', 'Discount', 'Tax', 'Total', NULL, 'Total Due', 'Total Paid', 0, NULL, NULL, 'Date', NULL, 1, 0, 0, 1, 0, 0, 0, 0, NULL, 0, 'Product', 'Quantity', 'Unit Price', 'Subtotal', NULL, NULL, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1, 0, 0, 1, 1, 'Customer', NULL, 0, 0, '#000000', NULL, NULL, '{\"proforma_heading\":null,\"sales_order_heading\":null,\"due_date_label\":null,\"total_quantity_label\":null,\"item_discount_label\":null,\"discounted_unit_price_label\":null,\"total_items_label\":null,\"num_to_word_format\":\"international\",\"tax_summary_label\":null,\"zatca_phase\":\"phase_1\"}', 1, 1, 0, NULL, 1, '[\"business_name\",\"address\",\"invoice_no\",\"invoice_datetime\",\"total_amount\",\"invoice_url\"]', 'classic', NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, '2026-04-09 09:53:34', '2026-04-09 12:30:19');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_schemes`
--

CREATE TABLE `invoice_schemes` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `scheme_type` enum('blank','year') NOT NULL,
  `number_type` varchar(100) NOT NULL DEFAULT 'sequential',
  `prefix` varchar(191) DEFAULT NULL,
  `start_number` int(11) DEFAULT NULL,
  `invoice_count` int(11) NOT NULL DEFAULT 0,
  `total_digits` int(11) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_schemes`
--

INSERT INTO `invoice_schemes` (`id`, `business_id`, `name`, `scheme_type`, `number_type`, `prefix`, `start_number`, `invoice_count`, `total_digits`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 1, 'Default', 'blank', 'sequential', '', 1, 31, 4, 1, '2026-04-09 09:53:34', '2026-04-20 20:08:25');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_cards`
--

CREATE TABLE `job_cards` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED DEFAULT NULL,
  `contact_id` int(10) UNSIGNED DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `template_id` int(10) UNSIGNED DEFAULT NULL,
  `ref_no` varchar(191) NOT NULL,
  `title` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('pending','assigned','in_progress','on_hold','completed','approved','cancelled') NOT NULL DEFAULT 'pending',
  `due_date` timestamp NULL DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `assigned_to` int(10) UNSIGNED DEFAULT NULL COMMENT 'User ID of assigned worker',
  `estimated_hours` decimal(8,2) DEFAULT NULL,
  `actual_hours` decimal(8,2) NOT NULL DEFAULT 0.00,
  `estimated_cost` decimal(12,2) DEFAULT NULL,
  `actual_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `worker_notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `location_coordinates` text DEFAULT NULL COMMENT 'GPS coordinates',
  `location_address` text DEFAULT NULL,
  `completed_by` int(10) UNSIGNED DEFAULT NULL COMMENT 'User ID who marked complete',
  `approved_by` int(10) UNSIGNED DEFAULT NULL COMMENT 'User ID who approved',
  `requires_approval` tinyint(1) NOT NULL DEFAULT 1,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `rating` int(11) DEFAULT NULL COMMENT '1-5 rating',
  `rating_feedback` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_categories`
--

CREATE TABLE `job_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `short_code` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(191) DEFAULT '#3B82F6',
  `icon` varchar(191) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_checklists`
--

CREATE TABLE `job_checklists` (
  `id` int(10) UNSIGNED NOT NULL,
  `job_id` int(10) UNSIGNED NOT NULL,
  `template_item_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `completed_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_images`
--

CREATE TABLE `job_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `job_id` int(10) UNSIGNED NOT NULL,
  `job_log_id` int(10) UNSIGNED DEFAULT NULL,
  `file_path` varchar(191) NOT NULL,
  `file_name` varchar(191) NOT NULL,
  `file_type` varchar(191) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `thumbnail_path` varchar(191) DEFAULT NULL,
  `caption` text DEFAULT NULL,
  `location_coordinates` text DEFAULT NULL COMMENT 'GPS where photo was taken',
  `taken_at` timestamp NULL DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_logs`
--

CREATE TABLE `job_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `job_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `log_type` enum('status_change','note','time_log','system','comment') NOT NULL DEFAULT 'note',
  `title` varchar(191) DEFAULT NULL,
  `content` text NOT NULL,
  `old_status` varchar(191) DEFAULT NULL,
  `new_status` varchar(191) DEFAULT NULL,
  `hours_logged` decimal(8,2) DEFAULT NULL,
  `logged_at` timestamp NULL DEFAULT NULL,
  `location_coordinates` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_notifications`
--

CREATE TABLE `job_notifications` (
  `id` int(10) UNSIGNED NOT NULL,
  `job_id` int(10) UNSIGNED DEFAULT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'User to notify',
  `contact_email` varchar(191) DEFAULT NULL COMMENT 'External email for customer notifications',
  `contact_phone` varchar(191) DEFAULT NULL COMMENT 'External phone for SMS notifications',
  `notification_type` enum('job_assigned','job_started','job_completed','job_approved','job_cancelled','requisition_approved','requisition_rejected','reminder','custom') NOT NULL,
  `title` varchar(191) NOT NULL,
  `message` text NOT NULL,
  `channel` enum('email','sms','in_app') NOT NULL DEFAULT 'in_app',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `is_sent` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` timestamp NULL DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_requisitions`
--

CREATE TABLE `job_requisitions` (
  `id` int(10) UNSIGNED NOT NULL,
  `job_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `item_name` varchar(191) NOT NULL,
  `sku` varchar(191) DEFAULT NULL,
  `unit` varchar(191) DEFAULT NULL,
  `quantity_requested` decimal(10,3) NOT NULL DEFAULT 0.000,
  `quantity_approved` decimal(10,3) DEFAULT NULL,
  `quantity_issued` decimal(10,3) NOT NULL DEFAULT 0.000,
  `unit_price` decimal(12,2) DEFAULT NULL,
  `total_cost` decimal(12,2) DEFAULT NULL,
  `status` enum('pending','approved','partially_issued','fully_issued','rejected') NOT NULL DEFAULT 'pending',
  `reason` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` int(10) UNSIGNED DEFAULT NULL,
  `issued_at` timestamp NULL DEFAULT NULL,
  `issued_by` int(10) UNSIGNED DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_signatures`
--

CREATE TABLE `job_signatures` (
  `id` int(10) UNSIGNED NOT NULL,
  `job_id` int(10) UNSIGNED NOT NULL,
  `signature_type` enum('worker','manager','customer') NOT NULL DEFAULT 'worker',
  `signature_data` varchar(191) NOT NULL COMMENT 'Base64 encoded signature',
  `signer_name` varchar(191) NOT NULL,
  `signer_email` varchar(191) DEFAULT NULL,
  `signer_phone` varchar(191) DEFAULT NULL,
  `location_coordinates` text DEFAULT NULL,
  `signed_at` timestamp NOT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_templates`
--

CREATE TABLE `job_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `estimated_hours` decimal(8,2) DEFAULT NULL,
  `estimated_cost` decimal(12,2) DEFAULT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_template_items`
--

CREATE TABLE `job_template_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `job_template_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lost_sales`
--

CREATE TABLE `lost_sales` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED DEFAULT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `variation_id` int(10) UNSIGNED DEFAULT NULL,
  `product_name` varchar(191) NOT NULL,
  `sku` varchar(191) DEFAULT NULL,
  `selling_price` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `quantity` decimal(22,4) NOT NULL DEFAULT 1.0000,
  `notes` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `file_name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_media_type` varchar(191) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(4, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(5, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(6, '2016_06_01_000004_create_oauth_clients_table', 1),
(7, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(8, '2017_07_05_071953_create_currencies_table', 1),
(9, '2017_07_05_073658_create_business_table', 1),
(10, '2017_07_22_075923_add_business_id_users_table', 1),
(11, '2017_07_23_113209_create_brands_table', 1),
(12, '2017_07_26_083429_create_permission_tables', 1),
(13, '2017_07_26_110000_create_tax_rates_table', 1),
(14, '2017_07_26_122313_create_units_table', 1),
(15, '2017_07_27_075706_create_contacts_table', 1),
(16, '2017_08_04_071038_create_categories_table', 1),
(17, '2017_08_08_115903_create_products_table', 1),
(18, '2017_08_09_061616_create_variation_templates_table', 1),
(19, '2017_08_09_061638_create_variation_value_templates_table', 1),
(20, '2017_08_10_061146_create_product_variations_table', 1),
(21, '2017_08_10_061216_create_variations_table', 1),
(22, '2017_08_19_054827_create_transactions_table', 1),
(23, '2017_08_31_073533_create_purchase_lines_table', 1),
(24, '2017_10_15_064638_create_transaction_payments_table', 1),
(25, '2017_10_31_065621_add_default_sales_tax_to_business_table', 1),
(26, '2017_11_20_051930_create_table_group_sub_taxes', 1),
(27, '2017_11_20_063603_create_transaction_sell_lines', 1),
(28, '2017_11_21_064540_create_barcodes_table', 1),
(29, '2017_11_23_181237_create_invoice_schemes_table', 1),
(30, '2017_12_25_122822_create_business_locations_table', 1),
(31, '2017_12_25_160253_add_location_id_to_transactions_table', 1),
(32, '2017_12_25_163227_create_variation_location_details_table', 1),
(33, '2018_01_04_115627_create_sessions_table', 1),
(34, '2018_01_05_112817_create_invoice_layouts_table', 1),
(35, '2018_01_06_112303_add_invoice_scheme_id_and_invoice_layout_id_to_business_locations', 1),
(36, '2018_01_08_104124_create_expense_categories_table', 1),
(37, '2018_01_08_123327_modify_transactions_table_for_expenses', 1),
(38, '2018_01_09_111005_modify_payment_status_in_transactions_table', 1),
(39, '2018_01_09_111109_add_paid_on_column_to_transaction_payments_table', 1),
(40, '2018_01_25_172439_add_printer_related_fields_to_business_locations_table', 1),
(41, '2018_01_27_184322_create_printers_table', 1),
(42, '2018_01_30_181442_create_cash_registers_table', 1),
(43, '2018_01_31_125836_create_cash_register_transactions_table', 1),
(44, '2018_02_07_173326_modify_business_table', 1),
(45, '2018_02_08_105425_add_enable_product_expiry_column_to_business_table', 1),
(46, '2018_02_08_111027_add_expiry_period_and_expiry_period_type_columns_to_products_table', 1),
(47, '2018_02_08_131118_add_mfg_date_and_exp_date_purchase_lines_table', 1),
(48, '2018_02_08_155348_add_exchange_rate_to_transactions_table', 1),
(49, '2018_02_09_124945_modify_transaction_payments_table_for_contact_payments', 1),
(50, '2018_02_12_113640_create_transaction_sell_lines_purchase_lines_table', 1),
(51, '2018_02_12_114605_add_quantity_sold_in_purchase_lines_table', 1),
(52, '2018_02_13_183323_alter_decimal_fields_size', 1),
(53, '2018_02_14_161928_add_transaction_edit_days_to_business_table', 1),
(54, '2018_02_15_161032_add_document_column_to_transactions_table', 1),
(55, '2018_02_17_124709_add_more_options_to_invoice_layouts', 1),
(56, '2018_02_19_111517_add_keyboard_shortcut_column_to_business_table', 1),
(57, '2018_02_19_121537_stock_adjustment_move_to_transaction_table', 1),
(58, '2018_02_20_165505_add_is_direct_sale_column_to_transactions_table', 1),
(59, '2018_02_21_105329_create_system_table', 1),
(60, '2018_02_23_100549_version_1_2', 1),
(61, '2018_02_23_125648_add_enable_editing_sp_from_purchase_column_to_business_table', 1),
(62, '2018_02_26_103612_add_sales_commission_agent_column_to_business_table', 1),
(63, '2018_02_26_130519_modify_users_table_for_sales_cmmsn_agnt', 1),
(64, '2018_02_26_134500_add_commission_agent_to_transactions_table', 1),
(65, '2018_02_27_121422_add_item_addition_method_to_business_table', 1),
(66, '2018_02_27_170232_modify_transactions_table_for_stock_transfer', 1),
(67, '2018_03_05_153510_add_enable_inline_tax_column_to_business_table', 1),
(68, '2018_03_06_210206_modify_product_barcode_types', 1),
(69, '2018_03_13_181541_add_expiry_type_to_business_table', 1),
(70, '2018_03_16_113446_product_expiry_setting_for_business', 1),
(71, '2018_03_19_113601_add_business_settings_options', 1),
(72, '2018_03_26_125334_add_pos_settings_to_business_table', 1),
(73, '2018_03_26_165350_create_customer_groups_table', 1),
(74, '2018_03_27_122720_customer_group_related_changes_in_tables', 1),
(75, '2018_03_29_110138_change_tax_field_to_nullable_in_business_table', 1),
(76, '2018_03_29_115502_add_changes_for_sr_number_in_products_and_sale_lines_table', 1),
(77, '2018_03_29_134340_add_inline_discount_fields_in_purchase_lines', 1),
(78, '2018_03_31_140921_update_transactions_table_exchange_rate', 1),
(79, '2018_04_03_103037_add_contact_id_to_contacts_table', 1),
(80, '2018_04_03_122709_add_changes_to_invoice_layouts_table', 1),
(81, '2018_04_09_135320_change_exchage_rate_size_in_business_table', 1),
(82, '2018_04_17_123122_add_lot_number_to_business', 1),
(83, '2018_04_17_160845_add_product_racks_table', 1),
(84, '2018_04_20_182015_create_res_tables_table', 1),
(85, '2018_04_24_105246_restaurant_fields_in_transaction_table', 1),
(86, '2018_04_24_114149_add_enabled_modules_business_table', 1),
(87, '2018_04_24_133704_add_modules_fields_in_invoice_layout_table', 1),
(88, '2018_04_27_132653_quotation_related_change', 1),
(89, '2018_05_02_104439_add_date_format_and_time_format_to_business', 1),
(90, '2018_05_02_111939_add_sell_return_to_transaction_payments', 1),
(91, '2018_05_14_114027_add_rows_positions_for_products', 1),
(92, '2018_05_14_125223_add_weight_to_products_table', 1),
(93, '2018_05_14_164754_add_opening_stock_permission', 1),
(94, '2018_05_15_134729_add_design_to_invoice_layouts', 1),
(95, '2018_05_16_183307_add_tax_fields_invoice_layout', 1),
(96, '2018_05_18_191956_add_sell_return_to_transaction_table', 1),
(97, '2018_05_21_131349_add_custom_fileds_to_contacts_table', 1),
(98, '2018_05_21_131607_invoice_layout_fields_for_sell_return', 1),
(99, '2018_05_21_131949_add_custom_fileds_and_website_to_business_locations_table', 1),
(100, '2018_05_22_123527_create_reference_counts_table', 1),
(101, '2018_05_22_154540_add_ref_no_prefixes_column_to_business_table', 1),
(102, '2018_05_24_132620_add_ref_no_column_to_transaction_payments_table', 1),
(103, '2018_05_24_161026_add_location_id_column_to_business_location_table', 1),
(104, '2018_05_25_180603_create_modifiers_related_table', 1),
(105, '2018_05_29_121714_add_purchase_line_id_to_stock_adjustment_line_table', 1),
(106, '2018_05_31_114645_add_res_order_status_column_to_transactions_table', 1),
(107, '2018_06_05_103530_rename_purchase_line_id_in_stock_adjustment_lines_table', 1),
(108, '2018_06_05_111905_modify_products_table_for_modifiers', 1),
(109, '2018_06_06_110524_add_parent_sell_line_id_column_to_transaction_sell_lines_table', 1),
(110, '2018_06_07_152443_add_is_service_staff_to_roles_table', 1),
(111, '2018_06_07_182258_add_image_field_to_products_table', 1),
(112, '2018_06_13_133705_create_bookings_table', 1),
(113, '2018_06_15_173636_add_email_column_to_contacts_table', 1),
(114, '2018_06_27_182835_add_superadmin_related_fields_business', 1),
(115, '2018_07_10_101913_add_custom_fields_to_products_table', 1),
(116, '2018_07_17_103434_add_sales_person_name_label_to_invoice_layouts_table', 1),
(117, '2018_07_17_163920_add_theme_skin_color_column_to_business_table', 1),
(118, '2018_07_24_160319_add_lot_no_line_id_to_transaction_sell_lines_table', 1),
(119, '2018_07_25_110004_add_show_expiry_and_show_lot_colums_to_invoice_layouts_table', 1),
(120, '2018_07_25_172004_add_discount_columns_to_transaction_sell_lines_table', 1),
(121, '2018_07_26_124720_change_design_column_type_in_invoice_layouts_table', 1),
(122, '2018_07_26_170424_add_unit_price_before_discount_column_to_transaction_sell_line_table', 1),
(123, '2018_07_28_103614_add_credit_limit_column_to_contacts_table', 1),
(124, '2018_08_08_110755_add_new_payment_methods_to_transaction_payments_table', 1),
(125, '2018_08_08_122225_modify_cash_register_transactions_table_for_new_payment_methods', 1),
(126, '2018_08_14_104036_add_opening_balance_type_to_transactions_table', 1),
(127, '2018_09_04_155900_create_accounts_table', 1),
(128, '2018_09_06_114438_create_selling_price_groups_table', 1),
(129, '2018_09_06_154057_create_variation_group_prices_table', 1),
(130, '2018_09_07_102413_add_permission_to_access_default_selling_price', 1),
(131, '2018_09_07_134858_add_selling_price_group_id_to_transactions_table', 1),
(132, '2018_09_10_112448_update_product_type_to_single_if_null_in_products_table', 1),
(133, '2018_09_10_152703_create_account_transactions_table', 1),
(134, '2018_09_10_173656_add_account_id_column_to_transaction_payments_table', 1),
(135, '2018_09_19_123914_create_notification_templates_table', 1),
(136, '2018_09_22_110504_add_sms_and_email_settings_columns_to_business_table', 1),
(137, '2018_09_24_134942_add_lot_no_line_id_to_stock_adjustment_lines_table', 1),
(138, '2018_09_26_105557_add_transaction_payments_for_existing_expenses', 1),
(139, '2018_09_27_111609_modify_transactions_table_for_purchase_return', 1),
(140, '2018_09_27_131154_add_quantity_returned_column_to_purchase_lines_table', 1),
(141, '2018_10_02_131401_add_return_quantity_column_to_transaction_sell_lines_table', 1),
(142, '2018_10_03_104918_add_qty_returned_column_to_transaction_sell_lines_purchase_lines_table', 1),
(143, '2018_10_03_185947_add_default_notification_templates_to_database', 1),
(144, '2018_10_09_153105_add_business_id_to_transaction_payments_table', 1),
(145, '2018_10_16_135229_create_permission_for_sells_and_purchase', 1),
(146, '2018_10_22_114441_add_columns_for_variable_product_modifications', 1),
(147, '2018_10_22_134428_modify_variable_product_data', 1),
(148, '2018_10_30_181558_add_table_tax_headings_to_invoice_layout', 1),
(149, '2018_10_31_122619_add_pay_terms_field_transactions_table', 1),
(150, '2018_10_31_161328_add_new_permissions_for_pos_screen', 1),
(151, '2018_10_31_174752_add_access_selected_contacts_only_to_users_table', 1),
(152, '2018_10_31_175627_add_user_contact_access', 1),
(153, '2018_10_31_180559_add_auto_send_sms_column_to_notification_templates_table', 1),
(154, '2018_11_02_171949_change_card_type_column_to_varchar_in_transaction_payments_table', 1),
(155, '2018_11_08_105621_add_role_permissions', 1),
(156, '2018_11_26_114135_add_is_suspend_column_to_transactions_table', 1),
(157, '2018_11_28_104410_modify_units_table_for_multi_unit', 1),
(158, '2018_11_28_170952_add_sub_unit_id_to_purchase_lines_and_sell_lines', 1),
(159, '2018_11_29_115918_add_primary_key_in_system_table', 1),
(160, '2018_12_03_185546_add_product_description_column_to_products_table', 1),
(161, '2018_12_06_114937_modify_system_table_and_users_table', 1),
(162, '2018_12_13_160007_add_custom_fields_display_options_to_invoice_layouts_table', 1),
(163, '2018_12_14_103307_modify_system_table', 1),
(164, '2018_12_18_133837_add_prev_balance_due_columns_to_invoice_layouts_table', 1),
(165, '2018_12_18_170656_add_invoice_token_column_to_transaction_table', 1),
(166, '2018_12_20_133639_add_date_time_format_column_to_invoice_layouts_table', 1),
(167, '2018_12_21_120659_add_recurring_invoice_fields_to_transactions_table', 1),
(168, '2018_12_24_154933_create_notifications_table', 1),
(169, '2019_01_08_112015_add_document_column_to_transaction_payments_table', 1),
(170, '2019_01_10_124645_add_account_permission', 1),
(171, '2019_01_16_125825_add_subscription_no_column_to_transactions_table', 1),
(172, '2019_01_28_111647_add_order_addresses_column_to_transactions_table', 1),
(173, '2019_02_13_173821_add_is_inactive_column_to_products_table', 1),
(174, '2019_02_19_103118_create_discounts_table', 1),
(175, '2019_02_21_120324_add_discount_id_column_to_transaction_sell_lines_table', 1),
(176, '2019_02_21_134324_add_permission_for_discount', 1),
(177, '2019_03_04_170832_add_service_staff_columns_to_transaction_sell_lines_table', 1),
(178, '2019_03_09_102425_add_sub_type_column_to_transactions_table', 1),
(179, '2019_03_09_124457_add_indexing_transaction_sell_lines_purchase_lines_table', 1),
(180, '2019_03_12_120336_create_activity_log_table', 1),
(181, '2019_03_15_132925_create_media_table', 1),
(182, '2019_05_08_130339_add_indexing_to_parent_id_in_transaction_payments_table', 1),
(183, '2019_05_10_132311_add_missing_column_indexing', 1),
(184, '2019_05_14_091812_add_show_image_column_to_invoice_layouts_table', 1),
(185, '2019_05_25_104922_add_view_purchase_price_permission', 1),
(186, '2019_06_17_103515_add_profile_informations_columns_to_users_table', 1),
(187, '2019_06_18_135524_add_permission_to_view_own_sales_only', 1),
(188, '2019_06_19_112058_add_database_changes_for_reward_points', 1),
(189, '2019_06_28_133732_change_type_column_to_string_in_transactions_table', 1),
(190, '2019_07_13_111420_add_is_created_from_api_column_to_transactions_table', 1),
(191, '2019_07_15_165136_add_fields_for_combo_product', 1),
(192, '2019_07_19_103446_add_mfg_quantity_used_column_to_purchase_lines_table', 1),
(193, '2019_07_22_152649_add_not_for_selling_in_product_table', 1),
(194, '2019_07_29_185351_add_show_reward_point_column_to_invoice_layouts_table', 1),
(195, '2019_08_08_162302_add_sub_units_related_fields', 1),
(196, '2019_08_26_133419_update_price_fields_decimal_point', 1),
(197, '2019_09_02_160054_remove_location_permissions_from_roles', 1),
(198, '2019_09_03_185259_add_permission_for_pos_screen', 1),
(199, '2019_09_04_163141_add_location_id_to_cash_registers_table', 1),
(200, '2019_09_04_184008_create_types_of_services_table', 1),
(201, '2019_09_06_131445_add_types_of_service_fields_to_transactions_table', 1),
(202, '2019_09_09_134810_add_default_selling_price_group_id_column_to_business_locations_table', 1),
(203, '2019_09_12_105616_create_product_locations_table', 1),
(204, '2019_09_17_122522_add_custom_labels_column_to_business_table', 1),
(205, '2019_09_18_164319_add_shipping_fields_to_transactions_table', 1),
(206, '2019_09_19_170927_close_all_active_registers', 1),
(207, '2019_09_23_161906_add_media_description_cloumn_to_media_table', 1),
(208, '2019_10_18_155633_create_account_types_table', 1),
(209, '2019_10_22_163335_add_common_settings_column_to_business_table', 1),
(210, '2019_10_29_132521_add_update_purchase_status_permission', 1),
(211, '2019_11_09_110522_add_indexing_to_lot_number', 1),
(212, '2019_11_19_170824_add_is_active_column_to_business_locations_table', 1),
(213, '2019_11_21_162913_change_quantity_field_types_to_decimal', 1),
(214, '2019_11_25_160340_modify_categories_table_for_polymerphic_relationship', 1),
(215, '2019_12_02_105025_create_warranties_table', 1),
(216, '2019_12_03_180342_add_common_settings_field_to_invoice_layouts_table', 1),
(217, '2019_12_05_183955_add_more_fields_to_users_table', 1),
(218, '2019_12_06_174904_add_change_return_label_column_to_invoice_layouts_table', 1),
(219, '2019_12_11_121307_add_draft_and_quotation_list_permissions', 1),
(220, '2019_12_12_180126_copy_expense_total_to_total_before_tax', 1),
(221, '2019_12_19_181412_make_alert_quantity_field_nullable_on_products_table', 1),
(222, '2019_12_25_173413_create_dashboard_configurations_table', 1),
(223, '2020_01_08_133506_create_document_and_notes_table', 1),
(224, '2020_01_09_113252_add_cc_bcc_column_to_notification_templates_table', 1),
(225, '2020_01_16_174818_add_round_off_amount_field_to_transactions_table', 1),
(226, '2020_01_28_162345_add_weighing_scale_settings_in_business_settings_table', 1),
(227, '2020_02_18_172447_add_import_fields_to_transactions_table', 1),
(228, '2020_03_13_135844_add_is_active_column_to_selling_price_groups_table', 1),
(229, '2020_03_16_115449_add_contact_status_field_to_contacts_table', 1),
(230, '2020_03_26_124736_add_allow_login_column_in_users_table', 1),
(231, '2020_04_13_154150_add_feature_products_column_to_business_loactions', 1),
(232, '2020_04_15_151802_add_user_type_to_users_table', 1),
(233, '2020_04_22_153905_add_subscription_repeat_on_column_to_transactions_table', 1),
(234, '2020_04_28_111436_add_shipping_address_to_contacts_table', 1),
(235, '2020_06_01_094654_add_max_sale_discount_column_to_users_table', 1),
(236, '2020_06_12_162245_modify_contacts_table', 1),
(237, '2020_06_22_103104_change_recur_interval_default_to_one', 1),
(238, '2020_07_09_174621_add_balance_field_to_contacts_table', 1),
(239, '2020_07_23_104933_change_status_column_to_varchar_in_transaction_table', 1),
(240, '2020_09_07_171059_change_completed_stock_transfer_status_to_final', 1),
(241, '2020_09_21_123224_modify_booking_status_column_in_bookings_table', 1),
(242, '2020_09_22_121639_create_discount_variations_table', 1),
(243, '2020_10_05_121550_modify_business_location_table_for_invoice_layout', 1),
(244, '2020_10_16_175726_set_status_as_received_for_opening_stock', 1),
(245, '2020_10_23_170823_add_for_group_tax_column_to_tax_rates_table', 1),
(246, '2020_11_04_130940_add_more_custom_fields_to_contacts_table', 1),
(247, '2020_11_10_152841_add_cash_register_permissions', 1),
(248, '2020_11_17_164041_modify_type_column_to_varchar_in_contacts_table', 1),
(249, '2020_12_18_181447_add_shipping_custom_fields_to_transactions_table', 1),
(250, '2020_12_22_164303_add_sub_status_column_to_transactions_table', 1),
(251, '2020_12_24_153050_add_custom_fields_to_transactions_table', 1),
(252, '2020_12_28_105403_add_whatsapp_text_column_to_notification_templates_table', 1),
(253, '2020_12_29_165925_add_model_document_type_to_media_table', 1),
(254, '2021_02_08_175632_add_contact_number_fields_to_users_table', 1),
(255, '2021_02_11_172217_add_indexing_for_multiple_columns', 1),
(256, '2021_02_23_122043_add_more_columns_to_customer_groups_table', 1),
(257, '2021_02_24_175551_add_print_invoice_permission_to_all_roles', 1),
(258, '2021_03_03_162021_add_purchase_order_columns_to_purchase_lines_and_transactions_table', 1),
(259, '2021_03_11_120229_add_sales_order_columns', 1),
(260, '2021_03_16_120705_add_business_id_to_activity_log_table', 1),
(261, '2021_03_16_153427_add_code_columns_to_business_table', 1),
(262, '2021_03_18_173308_add_account_details_column_to_accounts_table', 1),
(263, '2021_03_18_183119_add_prefer_payment_account_columns_to_transactions_table', 1),
(264, '2021_03_22_120810_add_more_types_of_service_custom_fields', 1),
(265, '2021_03_24_183132_add_shipping_export_custom_field_details_to_contacts_table', 1),
(266, '2021_03_25_170715_add_export_custom_fields_info_to_transactions_table', 1),
(267, '2021_04_15_063449_add_denominations_column_to_cash_registers_table', 1),
(268, '2021_05_22_083426_add_indexing_to_account_transactions_table', 1),
(269, '2021_07_08_065808_add_additional_expense_columns_to_transaction_table', 1),
(270, '2021_07_13_082918_add_qr_code_columns_to_invoice_layouts_table', 1),
(271, '2021_07_21_061615_add_fields_to_show_commission_agent_in_invoice_layout', 1),
(272, '2021_08_13_105549_add_crm_contact_id_to_users_table', 1),
(273, '2021_08_25_114932_add_payment_link_fields_to_transaction_payments_table', 1),
(274, '2021_09_01_063110_add_spg_column_to_discounts_table', 1),
(275, '2021_09_03_061528_modify_cash_register_transactions_table', 1),
(276, '2021_10_05_061658_add_source_column_to_transactions_table', 1),
(277, '2021_12_16_121851_add_parent_id_column_to_expense_categories_table', 1),
(278, '2022_04_14_075120_add_payment_type_column_to_transaction_payments_table', 1),
(279, '2022_04_21_083327_create_cash_denominations_table', 1),
(280, '2022_05_10_055307_add_delivery_date_column_to_transactions_table', 1),
(281, '2022_06_13_123135_add_currency_precision_and_quantity_precision_fields_to_business_table', 1),
(282, '2022_06_28_133342_add_secondary_unit_columns_to_products_sell_line_purchase_lines_tables', 1),
(283, '2022_07_13_114307_create_purchase_requisition_related_columns', 1),
(284, '2022_08_25_132707_add_service_staff_timer_fields_to_products_and_users_table', 1),
(285, '2023_01_28_114255_add_letter_head_column_to_invoice_layouts_table', 1),
(286, '2023_02_11_161510_add_event_column_to_activity_log_table', 1),
(287, '2023_02_11_161511_add_batch_uuid_column_to_activity_log_table', 1),
(288, '2023_03_02_170312_add_provider_to_oauth_clients_table', 1),
(289, '2023_03_21_122731_add_sale_invoice_scheme_id_business_table', 1),
(290, '2023_03_21_170446_add_number_type_to_invoice_scheme', 1),
(291, '2023_04_17_155216_add_custom_fields_to_products', 1),
(292, '2023_04_28_130247_add_price_type_to_group_price_table', 1),
(293, '2023_06_21_033923_add_delivery_person_in_transactions', 1),
(294, '2023_09_13_153555_add_service_staff_pin_columns_in_users', 1),
(295, '2023_09_15_154404_add_is_kitchen_order_in_transactions', 1),
(296, '2023_12_06_152840_add_contact_type_in_contacts', 1),
(297, '2024_01_01_000001_add_job_token_to_contacts_table', 1),
(298, '2024_01_01_000002_create_job_categories_table', 1),
(299, '2024_01_01_000003_create_job_templates_table', 1),
(300, '2024_01_01_000004_create_job_template_items_table', 1),
(301, '2024_01_01_000005_create_jobs_table', 1),
(302, '2024_01_01_000006_create_job_checklists_table', 1),
(303, '2024_01_01_000007_create_job_logs_table', 1),
(304, '2024_01_01_000008_create_job_requisitions_table', 1),
(305, '2024_01_01_000009_create_job_images_table', 1),
(306, '2024_01_01_000010_create_job_signatures_table', 1),
(307, '2024_01_01_000011_create_job_notifications_table', 1),
(308, '2024_01_27_000001_add_whatsapp_settings_column_to_business_table', 1),
(309, '2024_10_03_151459_modify_transaction_sell_lines_purchase_lines_table', 1),
(310, '2025_03_07_114637_add_more_addresh_column_in_contact', 1),
(311, '2025_09_19_120000_add_previous_balance_due_fields_to_invoice_layouts_table', 1),
(312, '2026_01_12_000001_create_stocktake_lines_table', 1),
(313, '2026_01_18_000001_create_mpesa_settings_table', 1),
(314, '2026_01_18_000002_create_mpesa_transactions_table', 1),
(315, '2026_01_18_000003_create_mpesa_c2b_payments_table', 1),
(316, '2026_01_18_185808_add_b2c_fields_to_mpesa_settings_table', 1),
(317, '2026_01_18_192048_add_mpesa_and_stocktake_permissions', 1),
(318, '2026_01_23_174000_add_is_synced_to_tables', 1),
(319, '2026_01_23_194000_create_cache_table', 1),
(320, '2026_01_23_194100_create_jobs_table', 1),
(321, '2026_01_23_194200_create_failed_jobs_table', 1),
(322, '2026_01_26_000001_create_pos_orders_table', 1),
(323, '2026_01_26_000002_create_followups_table', 1),
(324, '2026_01_26_000003_add_new_feature_permissions', 1),
(325, '2026_01_26_100001_add_custom_product_name_to_pos_order_lines', 1),
(326, '2026_01_26_144124_add_quantity_to_followups_table', 1),
(327, '2026_02_10_000001_add_view_profit_permission', 1),
(328, '2026_03_11_000001_create_lost_sales_table', 1),
(329, '2026_03_14_100001_add_order_token_to_contacts', 1),
(330, '2026_03_14_100002_add_payment_fields_to_pos_orders', 1),
(331, '2026_03_19_000001_add_job_permissions', 1),
(332, '2026_03_19_000002_rename_jobs_table_to_job_cards', 1),
(333, '2026_03_20_000001_add_etims_fields_to_business_table', 1),
(334, '2026_03_20_000002_add_etims_fields_to_products_table', 1),
(335, '2026_03_20_000003_add_etims_fields_to_transactions_table', 1),
(336, '2026_03_20_000004_add_etims_permissions', 1),
(337, '2026_03_21_000001_add_etims_enabled_to_business_table', 1),
(338, '2026_03_21_100001_create_cooler_dealers_table', 1),
(339, '2026_03_21_100002_create_cooler_assets_table', 1),
(340, '2026_03_21_100003_create_cooler_agreements_table', 1),
(341, '2026_03_21_100004_create_cooler_retrievals_table', 1),
(342, '2026_03_21_100005_create_cooler_documents_table', 1),
(343, '2026_03_21_100006_create_cooler_compliance_logs_table', 1),
(344, '2026_03_21_100007_add_cooler_permissions', 1),
(345, '2026_03_21_100008_add_agent_to_cooler_dealers', 1),
(346, '2026_04_20_000010_create_sync_tables', 2),
(347, '2026_04_20_000011_make_roles_business_id_nullable', 3),
(348, '2026_03_25_000001_create_patient_details_table', 4),
(349, '2026_03_25_000002_create_hospital_appointments_table', 4),
(350, '2026_03_25_000003_create_hospital_consultations_table', 4),
(351, '2026_03_25_000004_create_hospital_ipd_tables', 4),
(352, '2026_03_25_000005_create_hospital_insurance_tables', 4),
(353, '2026_03_25_000006_create_hospital_laboratory_tables', 4),
(354, '2026_03_25_000007_add_transaction_id_to_hospital_tables', 4),
(355, '2026_03_25_000008_create_hospital_queue_table', 4),
(356, '2026_03_25_000009_create_hospital_dental_tables', 4),
(357, '2026_03_25_000010_create_hospital_asset_tables', 4),
(358, '2026_03_25_000012_create_hospital_maternity_tables', 4),
(359, '2026_04_07_000001_create_pesapal_settings_table', 4),
(360, '2026_04_07_000002_create_pesapal_transactions_table', 4),
(361, '2026_04_07_000003_add_pesapal_permissions', 4),
(362, '2026_04_16_000001_create_sms_logs_table', 5),
(363, '2026_04_16_100001_create_dda_drugs_table', 5),
(364, '2026_04_16_100002_create_dda_prescriptions_table', 5),
(365, '2026_04_16_100003_create_dda_dispense_log_table', 5),
(366, '2026_04_16_100004_create_dda_stock_log_table', 5),
(367, '2026_04_16_100005_create_dda_destruction_log_table', 5),
(368, '2026_04_16_100006_add_dda_columns_to_products_table', 5),
(369, '2026_04_16_100007_add_dda_permissions', 5),
(370, '2026_04_17_200001_create_saas_features_table', 5),
(371, '2026_04_17_200002_create_saas_bundles_table', 5),
(372, '2026_04_17_200003_create_saas_subscriptions_table', 5),
(373, '2026_04_17_200004_create_saas_invoices_table', 5),
(374, '2026_04_17_200005_create_saas_hosted_accounts_table', 5),
(375, '2026_04_17_200006_create_saas_enquiries_table', 5),
(376, '2026_04_18_090001_create_saas_settings_table', 5),
(377, '2026_04_19_000001_create_approval_system_tables', 6),
(378, '2026_04_19_000001_create_hospital_prescriptions_table', 6),
(379, '2026_04_19_000001_create_parcel_stations_table', 6),
(380, '2026_04_19_000002_add_triage_vitals_to_hospital_queue', 6),
(381, '2026_04_19_000002_create_hospital_radiography_tables', 6),
(382, '2026_04_19_000002_create_parcel_routes_table', 6),
(383, '2026_04_19_000003_create_hospital_theatre_tables', 6),
(384, '2026_04_19_000003_create_parcel_pricing_rules_table', 6),
(385, '2026_04_19_000004_create_hospital_remaining_modules_tables', 6),
(386, '2026_04_19_000004_create_parcels_table', 6),
(387, '2026_04_19_000005_add_business_type_to_saas_tables', 6),
(388, '2026_04_19_000005_create_parcel_status_logs_table', 6),
(389, '2026_04_19_000006_add_business_type_to_business_table', 6),
(390, '2026_04_20_000001_create_parcel_routes_table', 6),
(391, '2026_04_20_000002_create_parcels_table', 6),
(392, '2026_04_20_000003_create_parcel_checkpoints_table', 7),
(393, '2026_04_20_000004_add_business_type_to_business_table', 7),
(394, '2026_04_20_000005_create_hospital_bills_table', 7),
(395, '2026_04_20_000006_add_onboarding_price_to_saas_settings', 7);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(77, 'App\\User', 3),
(96, 'App\\User', 4);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\User', 1),
(1, 'App\\User', 2),
(3, 'App\\User', 3),
(3, 'App\\User', 4);

-- --------------------------------------------------------

--
-- Table structure for table `mpesa_c2b_payments`
--

CREATE TABLE `mpesa_c2b_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `transaction_type` varchar(50) DEFAULT NULL,
  `trans_id` varchar(50) NOT NULL,
  `trans_time` timestamp NULL DEFAULT NULL,
  `amount` decimal(22,4) NOT NULL,
  `business_shortcode` varchar(20) DEFAULT NULL,
  `bill_ref_number` varchar(100) DEFAULT NULL,
  `org_account_balance` decimal(22,4) DEFAULT NULL,
  `msisdn` varchar(20) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `raw_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`raw_response`)),
  `status` enum('received','matched','used','unmatched') NOT NULL DEFAULT 'received',
  `matched_to_type` varchar(50) DEFAULT NULL,
  `matched_to_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mpesa_settings`
--

CREATE TABLE `mpesa_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `consumer_key` text DEFAULT NULL,
  `consumer_secret` text DEFAULT NULL,
  `passkey` text DEFAULT NULL,
  `initiator_name` varchar(191) DEFAULT NULL,
  `security_credential` text DEFAULT NULL,
  `shortcode` varchar(20) DEFAULT NULL,
  `shortcode_type` enum('paybill','till') NOT NULL DEFAULT 'paybill',
  `till_number` varchar(20) DEFAULT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `environment` enum('sandbox','production') NOT NULL DEFAULT 'sandbox',
  `callback_url` varchar(191) DEFAULT NULL,
  `validation_url` varchar(191) DEFAULT NULL,
  `confirmation_url` varchar(191) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `last_tested_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mpesa_transactions`
--

CREATE TABLE `mpesa_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('pending','paid','failed','cancelled','expired') NOT NULL DEFAULT 'pending',
  `phone` varchar(20) NOT NULL,
  `amount` decimal(22,4) NOT NULL,
  `account_reference` varchar(100) DEFAULT NULL,
  `merchant_request_id` varchar(100) DEFAULT NULL,
  `checkout_request_id` varchar(100) DEFAULT NULL,
  `mpesa_receipt_number` varchar(50) DEFAULT NULL,
  `result_code` varchar(10) DEFAULT NULL,
  `result_description` text DEFAULT NULL,
  `transaction_type` varchar(50) NOT NULL DEFAULT 'stk_push',
  `initiated_by` int(10) UNSIGNED DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `notifiable_type` varchar(191) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_templates`
--

CREATE TABLE `notification_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(11) NOT NULL,
  `template_for` varchar(191) NOT NULL,
  `email_body` text DEFAULT NULL,
  `sms_body` text DEFAULT NULL,
  `whatsapp_text` text DEFAULT NULL,
  `subject` varchar(191) DEFAULT NULL,
  `cc` varchar(191) DEFAULT NULL,
  `bcc` varchar(191) DEFAULT NULL,
  `auto_send` tinyint(1) NOT NULL DEFAULT 0,
  `auto_send_sms` tinyint(1) NOT NULL DEFAULT 0,
  `auto_send_wa_notif` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_templates`
--

INSERT INTO `notification_templates` (`id`, `business_id`, `template_for`, `email_body`, `sms_body`, `whatsapp_text`, `subject`, `cc`, `bcc`, `auto_send`, `auto_send_sms`, `auto_send_wa_notif`, `created_at`, `updated_at`) VALUES
(1, 1, 'new_sale', '<p>Dear {contact_name},</p>\n\n                    <p>Your invoice number is {invoice_number}<br />\n                    Total amount: {total_amount}<br />\n                    Paid amount: {received_amount}</p>\n\n                    <p>Thank you for shopping with us.</p>\n\n                    <p>{business_logo}</p>\n\n                    <p>&nbsp;</p>', 'Dear {contact_name}, Thank you for shopping with us. {business_name}', NULL, 'Thank you from {business_name}', NULL, NULL, 0, 0, 0, '2026-04-09 09:53:35', '2026-04-09 09:53:35'),
(2, 1, 'payment_received', '<p>Dear {contact_name},</p>\n\n                <p>We have received a payment of {received_amount}</p>\n\n                <p>{business_logo}</p>', 'Dear {contact_name}, We have received a payment of {received_amount}. {business_name}', NULL, 'Payment Received, from {business_name}', NULL, NULL, 0, 0, 0, '2026-04-09 09:53:35', '2026-04-09 09:53:35'),
(3, 1, 'payment_reminder', '<p>Dear {contact_name},</p>\n\n                    <p>This is to remind you that you have pending payment of {due_amount}. Kindly pay it as soon as possible.</p>\n\n                    <p>{business_logo}</p>', 'Dear {contact_name}, You have pending payment of {due_amount}. Kindly pay it as soon as possible. {business_name}', NULL, 'Payment Reminder, from {business_name}', NULL, NULL, 0, 0, 0, '2026-04-09 09:53:35', '2026-04-09 09:53:35'),
(4, 1, 'new_booking', '<p>Dear {contact_name},</p>\n\n                    <p>Your booking is confirmed</p>\n\n                    <p>Date: {start_time} to {end_time}</p>\n\n                    <p>Table: {table}</p>\n\n                    <p>Location: {location}</p>\n\n                    <p>{business_logo}</p>', 'Dear {contact_name}, Your booking is confirmed. Date: {start_time} to {end_time}, Table: {table}, Location: {location}', NULL, 'Booking Confirmed - {business_name}', NULL, NULL, 0, 0, 0, '2026-04-09 09:53:35', '2026-04-09 09:53:35'),
(5, 1, 'new_order', '<p>Dear {contact_name},</p>\n\n                    <p>We have a new order with reference number {order_ref_number}. Kindly process the products as soon as possible.</p>\n\n                    <p>{business_name}<br />\n                    {business_logo}</p>', 'Dear {contact_name}, We have a new order with reference number {order_ref_number}. Kindly process the products as soon as possible. {business_name}', NULL, 'New Order, from {business_name}', NULL, NULL, 0, 0, 0, '2026-04-09 09:53:35', '2026-04-09 09:53:35'),
(6, 1, 'payment_paid', '<p>Dear {contact_name},</p>\n\n                    <p>We have paid amount {paid_amount} again invoice number {order_ref_number}.<br />\n                    Kindly note it down.</p>\n\n                    <p>{business_name}<br />\n                    {business_logo}</p>', 'We have paid amount {paid_amount} again invoice number {order_ref_number}.\n                    Kindly note it down. {business_name}', NULL, 'Payment Paid, from {business_name}', NULL, NULL, 0, 0, 0, '2026-04-09 09:53:35', '2026-04-09 09:53:35'),
(7, 1, 'items_received', '<p>Dear {contact_name},</p>\n\n                    <p>We have received all items from invoice reference number {order_ref_number}. Thank you for processing it.</p>\n\n                    <p>{business_name}<br />\n                    {business_logo}</p>', 'We have received all items from invoice reference number {order_ref_number}. Thank you for processing it. {business_name}', NULL, 'Items received, from {business_name}', NULL, NULL, 0, 0, 0, '2026-04-09 09:53:35', '2026-04-09 09:53:35'),
(8, 1, 'items_pending', '<p>Dear {contact_name},<br />\n                    This is to remind you that we have not yet received some items from invoice reference number {order_ref_number}. Please process it as soon as possible.</p>\n\n                    <p>{business_name}<br />\n                    {business_logo}</p>', 'This is to remind you that we have not yet received some items from invoice reference number {order_ref_number} . Please process it as soon as possible.{business_name}', NULL, 'Items Pending, from {business_name}', NULL, NULL, 0, 0, 0, '2026-04-09 09:53:35', '2026-04-09 09:53:35'),
(9, 1, 'new_quotation', '<p>Dear {contact_name},</p>\n\n                    <p>Your quotation number is {invoice_number}<br />\n                    Total amount: {total_amount}</p>\n\n                    <p>Thank you for shopping with us.</p>\n\n                    <p>{business_logo}</p>\n\n                    <p>&nbsp;</p>', 'Dear {contact_name}, Thank you for shopping with us. {business_name}', NULL, 'Thank you from {business_name}', NULL, NULL, 0, 0, 0, '2026-04-09 09:53:35', '2026-04-09 09:53:35'),
(10, 1, 'purchase_order', '<p>Dear {contact_name},</p>\n\n                    <p>We have a new purchase order with reference number {order_ref_number}. The respective invoice is attached here with.</p>\n\n                    <p>{business_logo}</p>', 'We have a new purchase order with reference number {order_ref_number}. {business_name}', NULL, 'New Purchase Order, from {business_name}', NULL, NULL, 0, 0, 0, '2026-04-09 09:53:35', '2026-04-09 09:53:35');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `secret` varchar(100) NOT NULL,
  `provider` varchar(191) DEFAULT NULL,
  `redirect` text NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcels`
--

CREATE TABLE `parcels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `waybill_number` varchar(191) NOT NULL,
  `sender_name` varchar(191) NOT NULL,
  `sender_phone` varchar(191) NOT NULL,
  `recipient_name` varchar(191) NOT NULL,
  `recipient_phone` varchar(191) NOT NULL,
  `origin_station_id` bigint(20) UNSIGNED NOT NULL,
  `destination_station_id` bigint(20) UNSIGNED NOT NULL,
  `route_id` bigint(20) UNSIGNED DEFAULT NULL,
  `weight_kg` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `description` text DEFAULT NULL,
  `declared_value` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `charge_amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `payment_method` enum('mpesa','cash','cod') NOT NULL DEFAULT 'cash',
  `payment_status` enum('pending','paid','partially_paid') NOT NULL DEFAULT 'pending',
  `mpesa_reference` varchar(191) DEFAULT NULL,
  `collection_type` enum('pickup','delivery') NOT NULL DEFAULT 'pickup',
  `status` enum('booked','in_transit','arrived','collected','failed') NOT NULL DEFAULT 'booked',
  `booked_by_user_id` int(10) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcel_checkpoints`
--

CREATE TABLE `parcel_checkpoints` (
  `id` int(10) UNSIGNED NOT NULL,
  `parcel_id` int(10) UNSIGNED NOT NULL,
  `location` varchar(191) NOT NULL,
  `checkpoint_type` enum('booked','collected_from_sender','dispatched','arrived_at_depot','out_for_delivery','delivered','delivery_attempted','returned_to_sender','exception') NOT NULL,
  `status_note` varchar(191) DEFAULT NULL,
  `scanned_by` int(10) UNSIGNED DEFAULT NULL,
  `vehicle_reg` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcel_pricing_rules`
--

CREATE TABLE `parcel_pricing_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `route_id` bigint(20) UNSIGNED NOT NULL,
  `weight_min_kg` decimal(22,4) NOT NULL,
  `weight_max_kg` decimal(22,4) NOT NULL,
  `price_per_kg` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `flat_fee` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcel_routes`
--

CREATE TABLE `parcel_routes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `origin_station_id` bigint(20) UNSIGNED NOT NULL,
  `destination_station_id` bigint(20) UNSIGNED NOT NULL,
  `base_price_per_kg` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `min_price` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `estimated_hours` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcel_stations`
--

CREATE TABLE `parcel_stations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `town` varchar(191) NOT NULL,
  `county` varchar(191) DEFAULT NULL,
  `address` varchar(191) DEFAULT NULL,
  `contact_phone` varchar(191) DEFAULT NULL,
  `agent_user_id` int(10) UNSIGNED DEFAULT NULL,
  `is_origin_capable` tinyint(1) NOT NULL DEFAULT 1,
  `is_destination_capable` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcel_status_logs`
--

CREATE TABLE `parcel_status_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parcel_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(191) NOT NULL,
  `station_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `updated_by_user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_details`
--

CREATE TABLE `patient_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `contact_id` int(10) UNSIGNED NOT NULL,
  `insurer_id` int(10) UNSIGNED DEFAULT NULL,
  `insurance_scheme_id` int(10) UNSIGNED DEFAULT NULL,
  `insurance_card_number` varchar(191) DEFAULT NULL,
  `uhid_number` varchar(191) DEFAULT NULL,
  `blood_group` varchar(191) DEFAULT NULL,
  `allergies` varchar(191) DEFAULT NULL,
  `chronic_conditions` varchar(191) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(191) DEFAULT NULL,
  `emergency_contact_name` varchar(191) DEFAULT NULL,
  `emergency_contact_number` varchar(191) DEFAULT NULL,
  `insurance_provider` varchar(191) DEFAULT NULL,
  `insurance_policy_number` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'profit_loss_report.view', 'web', '2026-04-09 09:48:10', NULL),
(2, 'direct_sell.access', 'web', '2026-04-09 09:48:10', NULL),
(3, 'product.opening_stock', 'web', '2026-04-09 09:48:10', '2026-04-09 09:48:10'),
(4, 'crud_all_bookings', 'web', '2026-04-09 09:48:11', '2026-04-09 09:48:11'),
(5, 'crud_own_bookings', 'web', '2026-04-09 09:48:11', '2026-04-09 09:48:11'),
(6, 'access_default_selling_price', 'web', '2026-04-09 09:48:11', '2026-04-09 09:48:11'),
(7, 'purchase.payments', 'web', '2026-04-09 09:48:11', '2026-04-09 09:48:11'),
(8, 'sell.payments', 'web', '2026-04-09 09:48:11', '2026-04-09 09:48:11'),
(9, 'edit_product_price_from_sale_screen', 'web', '2026-04-09 09:48:11', '2026-04-09 09:48:11'),
(10, 'edit_product_discount_from_sale_screen', 'web', '2026-04-09 09:48:11', '2026-04-09 09:48:11'),
(11, 'roles.view', 'web', '2026-04-09 09:48:11', '2026-04-09 09:48:11'),
(12, 'roles.create', 'web', '2026-04-09 09:48:11', '2026-04-09 09:48:11'),
(13, 'roles.update', 'web', '2026-04-09 09:48:11', '2026-04-09 09:48:11'),
(14, 'roles.delete', 'web', '2026-04-09 09:48:11', '2026-04-09 09:48:11'),
(15, 'account.access', 'web', '2026-04-09 09:48:11', '2026-04-09 09:48:11'),
(16, 'discount.access', 'web', '2026-04-09 09:48:11', '2026-04-09 09:48:11'),
(17, 'view_purchase_price', 'web', '2026-04-09 09:48:12', '2026-04-09 09:48:12'),
(18, 'view_own_sell_only', 'web', '2026-04-09 09:48:12', '2026-04-09 09:48:12'),
(19, 'edit_product_discount_from_pos_screen', 'web', '2026-04-09 09:48:12', '2026-04-09 09:48:12'),
(20, 'edit_product_price_from_pos_screen', 'web', '2026-04-09 09:48:12', '2026-04-09 09:48:12'),
(21, 'access_shipping', 'web', '2026-04-09 09:48:12', '2026-04-09 09:48:12'),
(22, 'purchase.update_status', 'web', '2026-04-09 09:48:12', '2026-04-09 09:48:12'),
(23, 'list_drafts', 'web', '2026-04-09 09:48:12', '2026-04-09 09:48:12'),
(24, 'list_quotations', 'web', '2026-04-09 09:48:12', '2026-04-09 09:48:12'),
(25, 'view_cash_register', 'web', '2026-04-09 09:48:12', '2026-04-09 09:48:12'),
(26, 'close_cash_register', 'web', '2026-04-09 09:48:12', '2026-04-09 09:48:12'),
(27, 'print_invoice', 'web', '2026-04-09 09:48:13', '2026-04-09 09:48:13'),
(28, 'mpesa.access', 'web', '2026-04-09 09:48:15', '2026-04-09 09:48:15'),
(29, 'mpesa.view_transactions', 'web', '2026-04-09 09:48:15', '2026-04-09 09:48:15'),
(30, 'mpesa.manage_settings', 'web', '2026-04-09 09:48:15', '2026-04-09 09:48:15'),
(31, 'stocktake.view', 'web', '2026-04-09 09:48:15', '2026-04-09 09:48:15'),
(32, 'stocktake.manage', 'web', '2026-04-09 09:48:15', '2026-04-09 09:48:15'),
(33, 'customer_report.view', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(34, 'supplier_report.view', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(35, 'orders.view', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(36, 'orders.create', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(37, 'orders.update', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(38, 'orders.delete', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(39, 'followups.view', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(40, 'followups.create', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(41, 'followups.update', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(42, 'followups.delete', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(43, 'view_profit', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(44, 'job.view', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(45, 'job.create', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(46, 'job.update', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(47, 'job.delete', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(48, 'access_etims_report', 'web', '2026-04-09 09:48:16', '2026-04-09 09:48:16'),
(49, 'cooler.asset.view', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(50, 'cooler.asset.create', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(51, 'cooler.asset.update', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(52, 'cooler.asset.delete', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(53, 'cooler.dealer.view', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(54, 'cooler.dealer.create', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(55, 'cooler.dealer.update', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(56, 'cooler.dealer.delete', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(57, 'cooler.dealer.verify_docs', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(58, 'cooler.agreement.view', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(59, 'cooler.agreement.create', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(60, 'cooler.agreement.sign', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(61, 'cooler.agreement.terminate', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(62, 'cooler.retrieval.view', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(63, 'cooler.retrieval.initiate', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(64, 'cooler.retrieval.execute', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(65, 'cooler.document.view', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(66, 'cooler.document.download', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(67, 'cooler.document.delete', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(68, 'cooler.compliance.view', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(69, 'cooler.report.view', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(70, 'cooler.agent.portal', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(71, 'cooler.order.create', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(72, 'cooler.order.view', 'web', '2026-04-09 09:48:17', '2026-04-09 09:48:17'),
(73, 'sell.view', 'web', '2026-04-09 09:53:34', '2026-04-09 09:53:34'),
(74, 'sell.create', 'web', '2026-04-09 09:53:34', '2026-04-09 09:53:34'),
(75, 'sell.update', 'web', '2026-04-09 09:53:34', '2026-04-09 09:53:34'),
(76, 'sell.delete', 'web', '2026-04-09 09:53:34', '2026-04-09 09:53:34'),
(77, 'access_all_locations', 'web', '2026-04-09 09:53:34', '2026-04-09 09:53:34'),
(78, 'dashboard.data', 'web', '2026-04-09 09:53:34', '2026-04-09 09:53:34'),
(79, 'product.view', 'web', '2026-04-09 11:17:38', '2026-04-09 11:17:38'),
(80, 'edit_pos_payment', 'web', '2026-04-09 11:17:38', '2026-04-09 11:17:38'),
(81, 'disable_draft', 'web', '2026-04-09 11:17:38', '2026-04-09 11:17:38'),
(82, 'disable_discount', 'web', '2026-04-09 11:17:38', '2026-04-09 11:17:38'),
(83, 'disable_suspend_sale', 'web', '2026-04-09 11:17:38', '2026-04-09 11:17:38'),
(84, 'create_credit_sale', 'web', '2026-04-09 11:17:38', '2026-04-09 11:17:38'),
(85, 'disable_credit_sale', 'web', '2026-04-09 11:17:38', '2026-04-09 11:17:38'),
(86, 'disable_quotation', 'web', '2026-04-09 11:17:38', '2026-04-09 11:17:38'),
(87, 'disable_card', 'web', '2026-04-09 11:17:38', '2026-04-09 11:17:38'),
(88, 'direct_sell.update', 'web', '2026-04-09 11:23:51', '2026-04-09 11:23:51'),
(89, 'edit_sell_payment', 'web', '2026-04-09 11:23:51', '2026-04-09 11:23:51'),
(90, 'view_cash_register_product_details', 'web', '2026-04-09 11:23:51', '2026-04-09 11:23:51'),
(91, 'expense.add', 'web', '2026-04-09 11:23:51', '2026-04-09 11:23:51'),
(92, 'expense.edit', 'web', '2026-04-09 11:23:51', '2026-04-09 11:23:51'),
(93, 'supplier.view', 'web', '2026-04-09 11:23:51', '2026-04-09 11:23:51'),
(94, 'customer.view', 'web', '2026-04-09 11:23:51', '2026-04-09 11:23:51'),
(95, 'all_expense.access', 'web', '2026-04-09 11:23:51', '2026-04-09 11:23:51'),
(96, 'location.2', 'web', '2026-04-09 11:51:38', '2026-04-09 11:51:38'),
(97, 'pesapal.manage_settings', 'web', '2026-04-20 18:37:46', '2026-04-20 18:37:46'),
(98, 'pesapal.view_transactions', 'web', '2026-04-20 18:37:46', '2026-04-20 18:37:46'),
(99, 'dda.view', 'web', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(100, 'dda.manage', 'web', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(101, 'dda.dispense', 'web', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(102, 'dda.prescriptions.view', 'web', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(103, 'dda.prescriptions.upload', 'web', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(104, 'dda.reports.view', 'web', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(105, 'dda.destruction.manage', 'web', '2026-04-20 18:40:48', '2026-04-20 18:40:48');

-- --------------------------------------------------------

--
-- Table structure for table `pesapal_settings`
--

CREATE TABLE `pesapal_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `consumer_key` text DEFAULT NULL,
  `consumer_secret` text DEFAULT NULL,
  `environment` enum('sandbox','production') NOT NULL DEFAULT 'sandbox',
  `currency` varchar(10) NOT NULL DEFAULT 'KES',
  `ipn_id` varchar(100) DEFAULT NULL,
  `ipn_url` varchar(191) DEFAULT NULL,
  `callback_url` varchar(191) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `last_tested_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pesapal_transactions`
--

CREATE TABLE `pesapal_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED DEFAULT NULL,
  `order_tracking_id` varchar(100) DEFAULT NULL,
  `merchant_reference` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','failed','reversed','invalid') NOT NULL DEFAULT 'pending',
  `amount` decimal(22,4) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'KES',
  `description` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `confirmation_code` varchar(100) DEFAULT NULL,
  `payment_account` varchar(100) DEFAULT NULL,
  `status_description` text DEFAULT NULL,
  `initiated_by` int(10) UNSIGNED DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pos_orders`
--

CREATE TABLE `pos_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `location_id` bigint(20) UNSIGNED NOT NULL,
  `contact_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `ref_no` varchar(191) DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `total_amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `payment_method` varchar(191) DEFAULT NULL,
  `payment_status` varchar(191) NOT NULL DEFAULT 'pending',
  `mpesa_phone` varchar(191) DEFAULT NULL,
  `mpesa_receipt` varchar(191) DEFAULT NULL,
  `mpesa_transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pos_order_lines`
--

CREATE TABLE `pos_order_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `variation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `custom_product_name` varchar(255) DEFAULT NULL,
  `quantity` decimal(22,4) NOT NULL DEFAULT 1.0000,
  `unit_price` decimal(22,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `printers`
--

CREATE TABLE `printers` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `connection_type` enum('network','windows','linux') NOT NULL,
  `capability_profile` enum('default','simple','SP2000','TEP-200M','P822D') NOT NULL DEFAULT 'default',
  `char_per_line` varchar(191) DEFAULT NULL,
  `ip_address` varchar(191) DEFAULT NULL,
  `port` varchar(191) DEFAULT NULL,
  `path` varchar(191) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `is_dda` tinyint(1) NOT NULL DEFAULT 0,
  `dda_drug_id` bigint(20) UNSIGNED DEFAULT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `type` enum('single','variable','modifier','combo') DEFAULT NULL,
  `unit_id` int(11) UNSIGNED DEFAULT NULL,
  `secondary_unit_id` int(11) DEFAULT NULL,
  `sub_unit_ids` text DEFAULT NULL,
  `brand_id` int(10) UNSIGNED DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `sub_category_id` int(10) UNSIGNED DEFAULT NULL,
  `tax` int(10) UNSIGNED DEFAULT NULL,
  `tax_type` enum('inclusive','exclusive') NOT NULL,
  `enable_stock` tinyint(1) NOT NULL DEFAULT 0,
  `alert_quantity` decimal(22,4) DEFAULT NULL,
  `sku` varchar(191) NOT NULL,
  `barcode_type` enum('C39','C128','EAN13','EAN8','UPCA','UPCE') DEFAULT 'C128',
  `expiry_period` decimal(4,2) DEFAULT NULL,
  `expiry_period_type` enum('days','months') DEFAULT NULL,
  `enable_sr_no` tinyint(1) NOT NULL DEFAULT 0,
  `weight` varchar(191) DEFAULT NULL,
  `product_custom_field1` varchar(191) DEFAULT NULL,
  `product_custom_field2` varchar(191) DEFAULT NULL,
  `product_custom_field3` varchar(191) DEFAULT NULL,
  `product_custom_field4` varchar(191) DEFAULT NULL,
  `product_custom_field5` varchar(191) DEFAULT NULL,
  `product_custom_field6` varchar(191) DEFAULT NULL,
  `product_custom_field7` varchar(191) DEFAULT NULL,
  `product_custom_field8` varchar(191) DEFAULT NULL,
  `product_custom_field9` varchar(191) DEFAULT NULL,
  `product_custom_field10` varchar(191) DEFAULT NULL,
  `product_custom_field11` varchar(191) DEFAULT NULL,
  `product_custom_field12` varchar(191) DEFAULT NULL,
  `product_custom_field13` varchar(191) DEFAULT NULL,
  `product_custom_field14` varchar(191) DEFAULT NULL,
  `product_custom_field15` varchar(191) DEFAULT NULL,
  `product_custom_field16` varchar(191) DEFAULT NULL,
  `product_custom_field17` varchar(191) DEFAULT NULL,
  `product_custom_field18` varchar(191) DEFAULT NULL,
  `product_custom_field19` varchar(191) DEFAULT NULL,
  `product_custom_field20` varchar(191) DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `etims_item_id` varchar(191) DEFAULT NULL,
  `etims_tax_category` varchar(191) NOT NULL DEFAULT 'A',
  `etims_uom` varchar(191) NOT NULL DEFAULT 'U',
  `etims_synced` tinyint(1) NOT NULL DEFAULT 0,
  `product_description` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `preparation_time_in_minutes` int(11) DEFAULT NULL,
  `warranty_id` int(11) DEFAULT NULL,
  `is_inactive` tinyint(1) NOT NULL DEFAULT 0,
  `not_for_selling` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `is_dda`, `dda_drug_id`, `business_id`, `type`, `unit_id`, `secondary_unit_id`, `sub_unit_ids`, `brand_id`, `category_id`, `sub_category_id`, `tax`, `tax_type`, `enable_stock`, `alert_quantity`, `sku`, `barcode_type`, `expiry_period`, `expiry_period_type`, `enable_sr_no`, `weight`, `product_custom_field1`, `product_custom_field2`, `product_custom_field3`, `product_custom_field4`, `product_custom_field5`, `product_custom_field6`, `product_custom_field7`, `product_custom_field8`, `product_custom_field9`, `product_custom_field10`, `product_custom_field11`, `product_custom_field12`, `product_custom_field13`, `product_custom_field14`, `product_custom_field15`, `product_custom_field16`, `product_custom_field17`, `product_custom_field18`, `product_custom_field19`, `product_custom_field20`, `image`, `etims_item_id`, `etims_tax_category`, `etims_uom`, `etims_synced`, `product_description`, `created_by`, `preparation_time_in_minutes`, `warranty_id`, `is_inactive`, `not_for_selling`, `created_at`, `updated_at`) VALUES
(1, 'test', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'exclusive', 1, NULL, '0001', 'C128', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 09:57:03', '2026-04-09 09:57:03'),
(2, 'Pentagon 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0002', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(3, 'Pentagon 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0003', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(4, 'thunder', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0004', 'C128', NULL, NULL, 0, NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 18:25:56'),
(5, 'Escort 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0005', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(6, 'Escort 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0006', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(7, 'Aqua wet 30ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0007', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(8, 'Aqua wet 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0008', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(9, 'Bamako', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0009', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(10, 'Pressure pump', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0010', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(11, 'Classic 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0011', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(12, 'Classic 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0012', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(13, 'Bestox 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0013', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(14, 'Bestox 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0014', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(15, 'Sevin 200g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0015', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(16, 'Sevin 100g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0016', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(17, 'Sevin 50g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0017', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(18, 'Actellic Super 100g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0018', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(19, 'Actellic Super 200g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0019', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(20, 'Actellic Super 50g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0020', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(21, 'Actellic Super 500g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0021', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(22, 'Actellic Gold 50g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0022', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(23, 'Actellic Gold 100g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0023', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(24, 'Actellic Gold 200g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0024', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(25, 'Actellic Gold 500g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0025', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(26, 'Dania 10g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0026', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(27, 'Collard 10g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0027', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(28, 'Collard 25g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0028', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(29, 'Spinach 10g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0029', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(30, 'Spinach 25g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0030', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(31, 'H Managu 10g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0031', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(32, 'H Managu 25g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0032', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(33, 'Kung’u Nib', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0033', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(34, 'Healing oil', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0034', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(35, 'Grenade 20ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0035', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(36, 'Egocin Chick', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0036', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(37, 'Biotrim vet', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0037', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(38, 'Trimovet', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0038', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(39, 'Ascarex', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0039', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(40, 'ESB 3', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0040', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(41, 'Chick start', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0041', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(42, 'casvita', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0042', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(43, 'Poltricin', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0043', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(44, 'Scazone', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0044', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(45, 'Matador 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0045', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(46, 'Growers', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0046', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(47, 'Chick mash', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0047', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(48, 'Ascarten P', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0048', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(49, 'Neycidal', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0049', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(50, 'Diazole', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0050', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(51, 'Ashthene', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0051', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(52, 'Easy grow F/F 500g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0052', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(53, 'Easy grow F/F 250g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0053', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(54, 'Easy grow F/F 120g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0054', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(55, 'Easy grow F/F 1kg', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0055', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(56, 'Easy grow vegetable 1kg', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0056', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(57, 'Easy grow vegetable 500g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0057', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(58, 'Easy grow vegetable 250g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0058', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(59, 'Booster', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0059', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(60, 'Greenbery 16L', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0060', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(61, 'Greenbery 20L', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0061', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(62, 'Agrofeed Plus', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0062', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(63, 'D.C.P.', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0063', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(64, 'Alpha quad 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0064', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(65, 'Alpha quad 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0065', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(66, 'Atom 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0066', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(67, 'Atom 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0067', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(68, 'Cattle salt', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0068', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(69, 'KS 20', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0069', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(70, 'DH O.2', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0070', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(71, 'DH O.4', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0071', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(72, 'Duma', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0072', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(73, 'Okra 10g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0073', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(74, 'Profile', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0074', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(75, 'Dominex 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0075', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(76, 'Taktic 40ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0076', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(77, 'Taktic 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0077', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(78, 'Electomine', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0078', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(79, 'Delete 20ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0079', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(80, 'Delete 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0080', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(81, 'Dabotick 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0081', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(82, 'Supermec 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0082', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(83, 'Adamycn 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0083', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(84, 'Adamycn 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0084', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(85, 'Supermec 10ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0085', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(86, 'Ex-kupe 20ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0086', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(87, 'Samorine', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0087', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(88, 'Veriben B12', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0088', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(89, 'Multivitamin 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0089', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(90, 'Multivitamin 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0090', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(91, 'Vitaboost 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0091', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(92, 'Supertix 20ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0092', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(93, 'Supertix 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0093', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(94, 'Supertix 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0094', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(95, 'Triatix 20ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0095', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(96, 'Triatix 40ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0096', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(97, 'Triatix 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0097', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(98, 'Penistrep 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0098', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(99, 'Penistrep 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0099', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(100, 'Tylodoxi', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0100', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(101, 'ABZ 40ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0101', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(102, 'ABZ 120ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0102', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(103, 'ABZ 250ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0103', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(104, 'ABZ 500ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0104', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(105, 'Albafas 40ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0105', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(106, 'Albafas 120ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0106', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(107, 'Albafas 500ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0107', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(108, 'Nilzan 125ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0108', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(109, 'Wormcid liquid', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0109', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(110, 'Wormcid Plus', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0110', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(111, 'Wormcid bolus', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0111', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(112, 'S-dime bolus', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0112', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(113, 'Disetoprim', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0113', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(114, 'Syringe', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0114', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(115, 'Needle', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0115', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(116, 'Epsom salt 250g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0116', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(117, 'Epsom salt 500g', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0117', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(118, 'Ciplick 5kg', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0118', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(119, 'Ciplick 3kg', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0119', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(120, 'Ciplick 2kg', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0120', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(121, 'Ciplick 1kg', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0121', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(122, 'Redcat', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0122', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(123, 'Egocin soluble', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0123', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(124, 'Degree Max 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0124', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(125, 'Degree Max 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0125', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(126, 'Tylasin 50ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0126', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(127, 'Tylasin 100ml', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0127', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(128, 'feeder 3kg', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0128', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(129, 'feeder 1.5kg', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0129', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(130, 'drinker 3l', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0130', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(131, 'drinker 1.5l', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0131', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(132, 'Knapsack 20l', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 0.0000, '0132', 'C128', NULL, NULL, 0, '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(133, 'ALMATIX 20ML', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'exclusive', 1, NULL, '0133', 'C128', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 11:42:31', '2026-04-09 11:42:31'),
(134, 'ALMATIX 100ML', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'exclusive', 1, NULL, '0134', 'C128', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-09 11:43:40', '2026-04-09 11:43:40'),
(135, 'ENDOCURE 135MLS', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'inclusive', 1, 4.0000, '0135', 'C128', 24.00, 'months', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-19 21:37:20', '2026-04-19 21:37:20'),
(136, 'GAS 6KGS', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'exclusive', 1, NULL, '0136', 'C128', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-19 21:39:27', '2026-04-19 21:39:27'),
(137, '13', 0, NULL, 1, 'single', 1, NULL, NULL, NULL, NULL, NULL, NULL, 'exclusive', 1, 1.0000, '0137', 'C128', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'A', 'U', 0, NULL, 2, NULL, NULL, 0, 0, '2026-04-19 21:40:39', '2026-04-19 21:40:39');

-- --------------------------------------------------------

--
-- Table structure for table `product_locations`
--

CREATE TABLE `product_locations` (
  `product_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_locations`
--

INSERT INTO `product_locations` (`product_id`, `location_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(104, 1),
(105, 1),
(106, 1),
(107, 1),
(108, 1),
(109, 1),
(110, 1),
(111, 1),
(112, 1),
(113, 1),
(114, 1),
(115, 1),
(116, 1),
(117, 1),
(118, 1),
(119, 1),
(120, 1),
(121, 1),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 1),
(134, 1),
(1, 2),
(2, 2),
(3, 2),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 2),
(14, 2),
(15, 2),
(16, 2),
(17, 2),
(18, 2),
(19, 2),
(20, 2),
(21, 2),
(22, 2),
(23, 2),
(24, 2),
(25, 2),
(26, 2),
(27, 2),
(28, 2),
(29, 2),
(30, 2),
(31, 2),
(32, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(37, 2),
(38, 2),
(39, 2),
(40, 2),
(41, 2),
(42, 2),
(43, 2),
(44, 2),
(45, 2),
(46, 2),
(47, 2),
(48, 2),
(49, 2),
(50, 2),
(51, 2),
(52, 2),
(53, 2),
(54, 2),
(55, 2),
(56, 2),
(57, 2),
(58, 2),
(59, 2),
(60, 2),
(61, 2),
(62, 2),
(63, 2),
(64, 2),
(65, 2),
(66, 2),
(67, 2),
(68, 2),
(69, 2),
(70, 2),
(71, 2),
(72, 2),
(73, 2),
(74, 2),
(75, 2),
(76, 2),
(77, 2),
(78, 2),
(79, 2),
(80, 2),
(81, 2),
(82, 2),
(83, 2),
(84, 2),
(85, 2),
(86, 2),
(87, 2),
(88, 2),
(89, 2),
(90, 2),
(91, 2),
(92, 2),
(93, 2),
(94, 2),
(95, 2),
(96, 2),
(97, 2),
(98, 2),
(99, 2),
(100, 2),
(101, 2),
(102, 2),
(103, 2),
(104, 2),
(105, 2),
(106, 2),
(107, 2),
(108, 2),
(109, 2),
(110, 2),
(111, 2),
(112, 2),
(113, 2),
(114, 2),
(115, 2),
(116, 2),
(117, 2),
(118, 2),
(119, 2),
(120, 2),
(121, 2),
(122, 2),
(123, 2),
(124, 2),
(125, 2),
(126, 2),
(127, 2),
(128, 2),
(129, 2),
(130, 2),
(131, 2),
(132, 2),
(133, 2),
(134, 2),
(135, 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_racks`
--

CREATE TABLE `product_racks` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `rack` varchar(191) DEFAULT NULL,
  `row` varchar(191) DEFAULT NULL,
  `position` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variations`
--

CREATE TABLE `product_variations` (
  `id` int(10) UNSIGNED NOT NULL,
  `variation_template_id` int(11) DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `is_dummy` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variations`
--

INSERT INTO `product_variations` (`id`, `variation_template_id`, `name`, `product_id`, `is_dummy`, `created_at`, `updated_at`) VALUES
(1, NULL, 'DUMMY', 1, 1, '2026-04-09 09:57:03', '2026-04-09 09:57:03'),
(2, NULL, 'DUMMY', 2, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(3, NULL, 'DUMMY', 3, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(4, NULL, 'DUMMY', 4, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(5, NULL, 'DUMMY', 5, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(6, NULL, 'DUMMY', 6, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(7, NULL, 'DUMMY', 7, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(8, NULL, 'DUMMY', 8, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(9, NULL, 'DUMMY', 9, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(10, NULL, 'DUMMY', 10, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(11, NULL, 'DUMMY', 11, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(12, NULL, 'DUMMY', 12, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(13, NULL, 'DUMMY', 13, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(14, NULL, 'DUMMY', 14, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(15, NULL, 'DUMMY', 15, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(16, NULL, 'DUMMY', 16, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(17, NULL, 'DUMMY', 17, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(18, NULL, 'DUMMY', 18, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(19, NULL, 'DUMMY', 19, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(20, NULL, 'DUMMY', 20, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(21, NULL, 'DUMMY', 21, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(22, NULL, 'DUMMY', 22, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(23, NULL, 'DUMMY', 23, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(24, NULL, 'DUMMY', 24, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(25, NULL, 'DUMMY', 25, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(26, NULL, 'DUMMY', 26, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(27, NULL, 'DUMMY', 27, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(28, NULL, 'DUMMY', 28, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(29, NULL, 'DUMMY', 29, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(30, NULL, 'DUMMY', 30, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(31, NULL, 'DUMMY', 31, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(32, NULL, 'DUMMY', 32, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(33, NULL, 'DUMMY', 33, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(34, NULL, 'DUMMY', 34, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(35, NULL, 'DUMMY', 35, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(36, NULL, 'DUMMY', 36, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(37, NULL, 'DUMMY', 37, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(38, NULL, 'DUMMY', 38, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(39, NULL, 'DUMMY', 39, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(40, NULL, 'DUMMY', 40, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(41, NULL, 'DUMMY', 41, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(42, NULL, 'DUMMY', 42, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(43, NULL, 'DUMMY', 43, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(44, NULL, 'DUMMY', 44, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(45, NULL, 'DUMMY', 45, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(46, NULL, 'DUMMY', 46, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(47, NULL, 'DUMMY', 47, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(48, NULL, 'DUMMY', 48, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(49, NULL, 'DUMMY', 49, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(50, NULL, 'DUMMY', 50, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(51, NULL, 'DUMMY', 51, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(52, NULL, 'DUMMY', 52, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(53, NULL, 'DUMMY', 53, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(54, NULL, 'DUMMY', 54, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(55, NULL, 'DUMMY', 55, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(56, NULL, 'DUMMY', 56, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(57, NULL, 'DUMMY', 57, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(58, NULL, 'DUMMY', 58, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(59, NULL, 'DUMMY', 59, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(60, NULL, 'DUMMY', 60, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(61, NULL, 'DUMMY', 61, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(62, NULL, 'DUMMY', 62, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(63, NULL, 'DUMMY', 63, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(64, NULL, 'DUMMY', 64, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(65, NULL, 'DUMMY', 65, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(66, NULL, 'DUMMY', 66, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(67, NULL, 'DUMMY', 67, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(68, NULL, 'DUMMY', 68, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(69, NULL, 'DUMMY', 69, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(70, NULL, 'DUMMY', 70, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(71, NULL, 'DUMMY', 71, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(72, NULL, 'DUMMY', 72, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(73, NULL, 'DUMMY', 73, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(74, NULL, 'DUMMY', 74, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(75, NULL, 'DUMMY', 75, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(76, NULL, 'DUMMY', 76, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(77, NULL, 'DUMMY', 77, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(78, NULL, 'DUMMY', 78, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(79, NULL, 'DUMMY', 79, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(80, NULL, 'DUMMY', 80, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(81, NULL, 'DUMMY', 81, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(82, NULL, 'DUMMY', 82, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(83, NULL, 'DUMMY', 83, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(84, NULL, 'DUMMY', 84, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(85, NULL, 'DUMMY', 85, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(86, NULL, 'DUMMY', 86, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(87, NULL, 'DUMMY', 87, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(88, NULL, 'DUMMY', 88, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(89, NULL, 'DUMMY', 89, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(90, NULL, 'DUMMY', 90, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(91, NULL, 'DUMMY', 91, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(92, NULL, 'DUMMY', 92, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(93, NULL, 'DUMMY', 93, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(94, NULL, 'DUMMY', 94, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(95, NULL, 'DUMMY', 95, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(96, NULL, 'DUMMY', 96, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(97, NULL, 'DUMMY', 97, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(98, NULL, 'DUMMY', 98, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(99, NULL, 'DUMMY', 99, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(100, NULL, 'DUMMY', 100, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(101, NULL, 'DUMMY', 101, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(102, NULL, 'DUMMY', 102, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(103, NULL, 'DUMMY', 103, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(104, NULL, 'DUMMY', 104, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(105, NULL, 'DUMMY', 105, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(106, NULL, 'DUMMY', 106, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(107, NULL, 'DUMMY', 107, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(108, NULL, 'DUMMY', 108, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(109, NULL, 'DUMMY', 109, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(110, NULL, 'DUMMY', 110, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(111, NULL, 'DUMMY', 111, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(112, NULL, 'DUMMY', 112, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(113, NULL, 'DUMMY', 113, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(114, NULL, 'DUMMY', 114, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(115, NULL, 'DUMMY', 115, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(116, NULL, 'DUMMY', 116, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(117, NULL, 'DUMMY', 117, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(118, NULL, 'DUMMY', 118, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(119, NULL, 'DUMMY', 119, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(120, NULL, 'DUMMY', 120, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(121, NULL, 'DUMMY', 121, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(122, NULL, 'DUMMY', 122, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(123, NULL, 'DUMMY', 123, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(124, NULL, 'DUMMY', 124, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(125, NULL, 'DUMMY', 125, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(126, NULL, 'DUMMY', 126, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(127, NULL, 'DUMMY', 127, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(128, NULL, 'DUMMY', 128, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(129, NULL, 'DUMMY', 129, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(130, NULL, 'DUMMY', 130, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(131, NULL, 'DUMMY', 131, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(132, NULL, 'DUMMY', 132, 1, '2026-04-09 10:50:25', '2026-04-09 10:50:25'),
(133, NULL, 'DUMMY', 133, 1, '2026-04-09 11:42:31', '2026-04-09 11:42:31'),
(134, NULL, 'DUMMY', 134, 1, '2026-04-09 11:43:40', '2026-04-09 11:43:40'),
(135, NULL, 'DUMMY', 135, 1, '2026-04-19 21:37:20', '2026-04-19 21:37:20'),
(136, NULL, 'DUMMY', 136, 1, '2026-04-19 21:39:27', '2026-04-19 21:39:27'),
(137, NULL, 'DUMMY', 137, 1, '2026-04-19 21:40:39', '2026-04-19 21:40:39');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_lines`
--

CREATE TABLE `purchase_lines` (
  `id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variation_id` int(10) UNSIGNED NOT NULL,
  `quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `secondary_unit_quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `pp_without_discount` decimal(22,4) NOT NULL DEFAULT 0.0000 COMMENT 'Purchase price before inline discounts',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Inline discount percentage',
  `purchase_price` decimal(22,4) NOT NULL,
  `purchase_price_inc_tax` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `item_tax` decimal(22,4) NOT NULL COMMENT 'Tax for one quantity',
  `tax_id` int(10) UNSIGNED DEFAULT NULL,
  `purchase_requisition_line_id` int(11) DEFAULT NULL,
  `purchase_order_line_id` int(11) DEFAULT NULL,
  `quantity_sold` decimal(22,4) NOT NULL DEFAULT 0.0000 COMMENT 'Quanity sold from this purchase line',
  `quantity_adjusted` decimal(22,4) NOT NULL DEFAULT 0.0000 COMMENT 'Quanity adjusted in stock adjustment from this purchase line',
  `quantity_returned` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `po_quantity_purchased` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `mfg_quantity_used` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `mfg_date` date DEFAULT NULL,
  `exp_date` date DEFAULT NULL,
  `lot_number` varchar(191) DEFAULT NULL,
  `sub_unit_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_lines`
--

INSERT INTO `purchase_lines` (`id`, `transaction_id`, `product_id`, `variation_id`, `quantity`, `secondary_unit_quantity`, `pp_without_discount`, `discount_percent`, `purchase_price`, `purchase_price_inc_tax`, `item_tax`, `tax_id`, `purchase_requisition_line_id`, `purchase_order_line_id`, `quantity_sold`, `quantity_adjusted`, `quantity_returned`, `po_quantity_purchased`, `mfg_quantity_used`, `mfg_date`, `exp_date`, `lot_number`, `sub_unit_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 120.0000, 0.0000, 4.0000, 0.00, 4.0000, 4.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-09 09:57:23', '2026-04-09 10:47:01'),
(2, 2, 102, 102, 10.0000, 0.0000, 141.0000, 0.00, 141.0000, 141.0000, 0.0000, NULL, NULL, NULL, 4.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-09 10:52:17', '2026-04-13 13:23:46'),
(3, 3, 103, 103, 7.0000, 0.0000, 265.0000, 0.00, 265.0000, 265.0000, 0.0000, NULL, NULL, NULL, 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-09 10:52:26', '2026-04-19 18:38:15'),
(4, 4, 101, 101, 10.0000, 0.0000, 61.0000, 0.00, 61.0000, 61.0000, 0.0000, NULL, NULL, NULL, 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-09 10:52:32', '2026-04-19 20:34:44'),
(5, 5, 104, 104, 15.0000, 0.0000, 457.0000, 0.00, 457.0000, 457.0000, 0.0000, NULL, NULL, NULL, 2.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-09 10:52:39', '2026-04-19 20:34:01'),
(6, 8, 19, 19, 5.0000, 0.0000, 217.0000, 0.00, 217.0000, 217.0000, 0.0000, NULL, NULL, NULL, 5.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-09 11:34:23', '2026-04-19 20:24:40'),
(7, 9, 66, 66, 5.0000, 0.0000, 84.0000, 0.00, 84.0000, 84.0000, 0.0000, NULL, NULL, NULL, 3.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-09 11:39:10', '2026-04-20 20:08:25'),
(8, 9, 2, 2, 5.0000, 0.0000, 124.0000, 0.00, 124.0000, 124.0000, 0.0000, NULL, NULL, NULL, 2.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-09 11:39:10', '2026-04-20 15:57:56'),
(9, 10, 134, 134, 5.0000, 0.0000, 228.0000, 0.00, 228.0000, 228.0000, 0.0000, NULL, NULL, NULL, 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-09 11:44:01', '2026-04-09 12:06:13'),
(10, 11, 133, 133, 5.0000, 0.0000, 80.0000, 0.00, 80.0000, 80.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-09 11:47:26', '2026-04-09 11:47:26'),
(11, 15, 13, 13, 5.0000, 0.0000, 141.0000, 0.00, 141.0000, 141.0000, 0.0000, NULL, NULL, NULL, 2.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-09 16:37:04', '2026-04-19 18:40:25'),
(12, 15, 14, 14, 3.0000, 0.0000, 217.0000, 0.00, 217.0000, 217.0000, 0.0000, NULL, NULL, NULL, 3.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-09 16:37:04', '2026-04-19 20:26:57'),
(13, 23, 101, 101, 1.0000, 0.0000, 61.0000, 0.00, 61.0000, 61.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-13 19:01:53', '2026-04-13 19:01:53'),
(14, 24, 18, 18, 11.0000, 0.0000, 150.0000, 0.00, 150.0000, 150.0000, 0.0000, NULL, NULL, NULL, 3.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-13 19:05:14', '2026-04-13 19:48:55'),
(15, 30, 95, 95, 5.0000, 0.0000, 102.0000, 0.00, 102.0000, 102.0000, 0.0000, NULL, NULL, NULL, 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-13 20:14:32', '2026-04-13 20:29:28'),
(16, 37, 8, 8, 5.0000, 0.0000, 79.0000, 0.00, 79.0000, 79.0000, 0.0000, NULL, NULL, NULL, 3.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 18:55:57', '2026-04-20 15:57:56'),
(17, 38, 125, 125, 2.0000, 0.0000, 124.0000, 0.00, 124.0000, 124.0000, 0.0000, NULL, NULL, NULL, 2.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 18:58:45', '2026-04-20 20:08:25'),
(18, 45, 36, 36, 6.0000, 0.0000, 40.0000, 0.00, 40.0000, 40.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(19, 45, 37, 37, 7.0000, 0.0000, 102.0000, 0.00, 102.0000, 102.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(20, 45, 39, 39, 5.0000, 0.0000, 97.0000, 0.00, 97.0000, 97.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(21, 45, 41, 41, 5.0000, 0.0000, 78.0000, 0.00, 78.0000, 78.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(22, 45, 43, 43, 3.0000, 0.0000, 52.0000, 0.00, 52.0000, 52.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(23, 45, 45, 45, 2.0000, 0.0000, 242.0000, 0.00, 242.0000, 242.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(24, 45, 47, 47, 2.0000, 0.0000, 42.0000, 0.00, 42.0000, 42.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(25, 45, 48, 48, 7.0000, 0.0000, 12.0000, 0.00, 12.0000, 12.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(26, 45, 59, 59, 3.0000, 0.0000, 67.0000, 0.00, 67.0000, 67.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(27, 45, 54, 54, 6.0000, 0.0000, 78.0000, 0.00, 78.0000, 78.0000, 0.0000, NULL, NULL, NULL, 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-20 20:08:25'),
(28, 45, 53, 53, 4.0000, 0.0000, 122.0000, 0.00, 122.0000, 122.0000, 0.0000, NULL, NULL, NULL, 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-20 15:57:56'),
(29, 45, 52, 52, 2.0000, 0.0000, 200.0000, 0.00, 200.0000, 200.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(30, 45, 55, 55, 3.0000, 0.0000, 550.0000, 0.00, 550.0000, 550.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(31, 45, 58, 58, 1.0000, 0.0000, 122.0000, 0.00, 122.0000, 122.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(32, 45, 57, 57, 1.0000, 0.0000, 200.0000, 0.00, 200.0000, 200.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(33, 45, 56, 56, 2.0000, 0.0000, 550.0000, 0.00, 550.0000, 550.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(34, 45, 60, 60, 1.0000, 0.0000, 1550.0000, 0.00, 1550.0000, 1550.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(35, 45, 132, 132, 1.0000, 0.0000, 1300.0000, 0.00, 1300.0000, 1300.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(36, 45, 122, 122, 23.0000, 0.0000, 12.0000, 0.00, 12.0000, 12.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(37, 45, 63, 63, 0.0000, 0.0000, 92.0000, 0.00, 92.0000, 92.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(38, 45, 63, 63, 2.0000, 0.0000, 92.0000, 0.00, 92.0000, 92.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(39, 45, 66, 66, 8.0000, 0.0000, 84.0000, 0.00, 84.0000, 84.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(40, 45, 67, 67, 4.0000, 0.0000, 179.0000, 0.00, 179.0000, 179.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(41, 45, 68, 68, 79.0000, 0.0000, 10.0000, 0.00, 10.0000, 10.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(42, 45, 70, 70, 3.0000, 0.0000, 598.0000, 0.00, 598.0000, 598.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(43, 45, 2, 2, 9.0000, 0.0000, 124.0000, 0.00, 124.0000, 124.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(44, 45, 3, 3, 1.0000, 0.0000, 221.0000, 0.00, 221.0000, 221.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(45, 45, 5, 5, 4.0000, 0.0000, 226.0000, 0.00, 226.0000, 226.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(46, 45, 6, 6, 4.0000, 0.0000, 467.0000, 0.00, 467.0000, 467.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(47, 45, 8, 8, 2.0000, 0.0000, 79.0000, 0.00, 79.0000, 79.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(48, 45, 9, 9, 9.0000, 0.0000, 87.0000, 0.00, 87.0000, 87.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(49, 45, 11, 11, 6.0000, 0.0000, 152.0000, 0.00, 152.0000, 152.0000, 0.0000, NULL, NULL, NULL, 3.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-20 20:08:25'),
(50, 45, 12, 12, 5.0000, 0.0000, 287.0000, 0.00, 287.0000, 287.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(51, 45, 13, 13, 7.0000, 0.0000, 141.0000, 0.00, 141.0000, 141.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(52, 45, 14, 14, 5.0000, 0.0000, 217.0000, 0.00, 217.0000, 217.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(53, 45, 17, 17, 6.0000, 0.0000, 55.0000, 0.00, 55.0000, 55.0000, 0.0000, NULL, NULL, NULL, 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-20 20:08:25'),
(54, 45, 16, 16, 8.0000, 0.0000, 67.0000, 0.00, 67.0000, 67.0000, 0.0000, NULL, NULL, NULL, 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-20 15:57:56'),
(55, 45, 15, 15, 8.0000, 0.0000, 109.0000, 0.00, 109.0000, 109.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(56, 45, 20, 20, 5.0000, 0.0000, 97.0000, 0.00, 97.0000, 97.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(57, 45, 18, 18, 3.0000, 0.0000, 150.0000, 0.00, 150.0000, 150.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(58, 45, 22, 22, 6.0000, 0.0000, 141.0000, 0.00, 141.0000, 141.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(59, 45, 23, 23, 3.0000, 0.0000, 210.0000, 0.00, 210.0000, 210.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(60, 45, 25, 25, 3.0000, 0.0000, 750.0000, 0.00, 750.0000, 750.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(61, 45, 33, 33, 4.0000, 0.0000, 110.0000, 0.00, 110.0000, 110.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(62, 45, 34, 34, 2.0000, 0.0000, 331.0000, 0.00, 331.0000, 331.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(63, 45, 35, 35, 3.0000, 0.0000, 107.0000, 0.00, 107.0000, 107.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(64, 45, 74, 74, 3.0000, 0.0000, 280.0000, 0.00, 280.0000, 280.0000, 0.0000, NULL, NULL, NULL, 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-20 15:52:45'),
(65, 45, 75, 75, 2.0000, 0.0000, 267.0000, 0.00, 267.0000, 267.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(66, 45, 78, 78, 3.0000, 0.0000, 227.0000, 0.00, 227.0000, 227.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(67, 45, 73, 73, 4.0000, 0.0000, 41.0000, 0.00, 41.0000, 41.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(68, 45, 26, 26, 5.0000, 0.0000, 51.0000, 0.00, 51.0000, 51.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(69, 45, 28, 28, 5.0000, 0.0000, 87.0000, 0.00, 87.0000, 87.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(70, 45, 27, 27, 7.0000, 0.0000, 51.0000, 0.00, 51.0000, 51.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(71, 45, 29, 29, 4.0000, 0.0000, 51.0000, 0.00, 51.0000, 51.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(72, 45, 31, 31, 3.0000, 0.0000, 46.0000, 0.00, 46.0000, 46.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(73, 45, 32, 32, 3.0000, 0.0000, 80.0000, 0.00, 80.0000, 80.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(74, 45, 79, 79, 2.0000, 0.0000, 200.0000, 0.00, 200.0000, 200.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(75, 45, 82, 82, 2.0000, 0.0000, 182.0000, 0.00, 182.0000, 182.0000, 0.0000, NULL, NULL, NULL, 2.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-20 15:57:56'),
(76, 45, 85, 85, 9.0000, 0.0000, 44.0000, 0.00, 44.0000, 44.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(77, 45, 83, 83, 3.0000, 0.0000, 410.0000, 0.00, 410.0000, 410.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(78, 45, 84, 84, 3.0000, 0.0000, 261.0000, 0.00, 261.0000, 261.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(79, 45, 86, 86, 10.0000, 0.0000, 78.0000, 0.00, 78.0000, 78.0000, 0.0000, NULL, NULL, NULL, 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-20 20:08:25'),
(80, 45, 2, 2, 3.0000, 0.0000, 124.0000, 0.00, 124.0000, 124.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(81, 45, 3, 3, 0.0000, 0.0000, 221.0000, 0.00, 221.0000, 221.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(82, 45, 98, 98, 1.0000, 0.0000, 410.0000, 0.00, 410.0000, 410.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(83, 45, 87, 87, 3.0000, 0.0000, 87.0000, 0.00, 87.0000, 87.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(84, 45, 88, 88, 9.0000, 0.0000, 79.0000, 0.00, 79.0000, 79.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(85, 45, 89, 89, 3.0000, 0.0000, 209.0000, 0.00, 209.0000, 209.0000, 0.0000, NULL, NULL, NULL, 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-20 15:52:45'),
(86, 45, 90, 90, 2.0000, 0.0000, 147.0000, 0.00, 147.0000, 147.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(87, 45, 91, 91, 4.0000, 0.0000, 147.0000, 0.00, 147.0000, 147.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(88, 45, 92, 92, 4.0000, 0.0000, 102.0000, 0.00, 102.0000, 102.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(89, 45, 96, 96, 4.0000, 0.0000, 167.0000, 0.00, 167.0000, 167.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(90, 45, 97, 97, 1.0000, 0.0000, 267.0000, 0.00, 267.0000, 267.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(91, 45, 100, 100, 6.0000, 0.0000, 132.0000, 0.00, 132.0000, 132.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(92, 45, 101, 101, 2.0000, 0.0000, 61.0000, 0.00, 61.0000, 61.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(93, 45, 102, 102, 4.0000, 0.0000, 141.0000, 0.00, 141.0000, 141.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(94, 45, 103, 103, 3.0000, 0.0000, 265.0000, 0.00, 265.0000, 265.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(95, 45, 104, 104, 2.0000, 0.0000, 457.0000, 0.00, 457.0000, 457.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(96, 45, 105, 105, 6.0000, 0.0000, 61.0000, 0.00, 61.0000, 61.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(97, 45, 106, 106, 4.0000, 0.0000, 141.0000, 0.00, 141.0000, 141.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(98, 45, 107, 107, 2.0000, 0.0000, 457.0000, 0.00, 457.0000, 457.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(99, 45, 108, 108, 1.0000, 0.0000, 187.0000, 0.00, 187.0000, 187.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(100, 45, 109, 109, 3.0000, 0.0000, 72.0000, 0.00, 72.0000, 72.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(101, 45, 110, 110, 3.0000, 0.0000, 101.0000, 0.00, 101.0000, 101.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(102, 45, 111, 111, 18.0000, 0.0000, 12.0000, 0.00, 12.0000, 12.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(103, 45, 112, 112, 6.0000, 0.0000, 31.0000, 0.00, 31.0000, 31.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(104, 45, 113, 113, 46.0000, 0.0000, 17.0000, 0.00, 17.0000, 17.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(105, 45, 114, 114, 35.0000, 0.0000, 15.0000, 0.00, 15.0000, 15.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(106, 45, 115, 115, 22.0000, 0.0000, 7.0000, 0.00, 7.0000, 7.0000, 0.0000, NULL, NULL, NULL, 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-20 20:08:25'),
(107, 45, 116, 116, 3.0000, 0.0000, 50.0000, 0.00, 50.0000, 50.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(108, 45, 117, 117, 1.0000, 0.0000, 87.0000, 0.00, 87.0000, 87.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(109, 45, 123, 123, 3.0000, 0.0000, 41.0000, 0.00, 41.0000, 41.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(110, 45, 118, 118, 3.0000, 0.0000, 400.0000, 0.00, 400.0000, 400.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(111, 45, 119, 119, 5.0000, 0.0000, 220.0000, 0.00, 220.0000, 220.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(112, 45, 120, 120, 3.0000, 0.0000, 200.0000, 0.00, 200.0000, 200.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(113, 45, 121, 121, 4.0000, 0.0000, 87.0000, 0.00, 87.0000, 87.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(114, 45, 124, 124, 1.0000, 0.0000, 228.0000, 0.00, 228.0000, 228.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(115, 45, 125, 125, 8.0000, 0.0000, 124.0000, 0.00, 124.0000, 124.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(116, 45, 126, 126, 1.0000, 0.0000, 124.0000, 0.00, 124.0000, 124.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(117, 45, 127, 127, 2.0000, 0.0000, 228.0000, 0.00, 228.0000, 228.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(118, 45, 130, 130, 2.0000, 0.0000, 180.0000, 0.00, 180.0000, 180.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(119, 45, 131, 131, 3.0000, 0.0000, 140.0000, 0.00, 140.0000, 140.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(120, 45, 128, 128, 3.0000, 0.0000, 240.0000, 0.00, 240.0000, 240.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(121, 45, 129, 129, 3.0000, 0.0000, 140.0000, 0.00, 140.0000, 140.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(122, 45, 10, 10, 4.0000, 0.0000, 220.0000, 0.00, 220.0000, 220.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(123, 46, 136, 136, 1.0000, 0.0000, 1110.0000, 0.00, 1110.0000, 1110.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:41:00', '2026-04-20 20:04:46'),
(124, 46, 137, 137, 1.0000, 0.0000, 2400.0000, 0.00, 2400.0000, 2400.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-19 21:41:00', '2026-04-20 20:04:46'),
(125, 51, 3, 3, 4.0000, 0.0000, 181.0000, 0.00, 181.0000, 181.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(126, 51, 8, 8, 4.0000, 0.0000, 82.0000, 0.00, 82.0000, 82.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(127, 51, 124, 124, 4.0000, 0.0000, 239.0000, 0.00, 239.0000, 239.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(128, 51, 74, 74, 4.0000, 0.0000, 280.0000, 0.00, 280.0000, 280.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(129, 51, 108, 108, 3.0000, 0.0000, 247.0000, 0.00, 247.0000, 247.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(130, 51, 75, 75, 3.0000, 0.0000, 241.0000, 0.00, 241.0000, 241.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(131, 51, 95, 95, 8.0000, 0.0000, 110.0000, 0.00, 110.0000, 110.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(132, 51, 101, 101, 5.0000, 0.0000, 61.0000, 0.00, 61.0000, 61.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(133, 51, 16, 16, 9.0000, 0.0000, 80.0000, 0.00, 80.0000, 80.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(134, 51, 17, 17, 6.0000, 0.0000, 55.0000, 0.00, 55.0000, 55.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(135, 51, 82, 82, 3.0000, 0.0000, 132.0000, 0.00, 132.0000, 132.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(136, 51, 81, 81, 3.0000, 0.0000, 118.0000, 0.00, 118.0000, 118.0000, 0.0000, NULL, NULL, NULL, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, NULL, NULL, NULL, NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40');

-- --------------------------------------------------------

--
-- Table structure for table `reference_counts`
--

CREATE TABLE `reference_counts` (
  `id` int(10) UNSIGNED NOT NULL,
  `ref_type` varchar(191) NOT NULL,
  `ref_count` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reference_counts`
--

INSERT INTO `reference_counts` (`id`, `ref_type`, `ref_count`, `business_id`, `created_at`, `updated_at`) VALUES
(1, 'contacts', 2, 1, '2026-04-09 09:53:34', '2026-04-09 11:36:27'),
(2, 'business_location', 2, 1, '2026-04-09 09:53:35', '2026-04-09 11:51:38'),
(3, 'sell_payment', 32, 1, '2026-04-09 10:53:04', '2026-04-20 20:08:25'),
(4, 'purchase', 11, 1, '2026-04-09 11:39:10', '2026-04-20 20:03:40'),
(5, 'purchase_payment', 8, 1, '2026-04-09 11:39:10', '2026-04-20 20:03:40');

-- --------------------------------------------------------

--
-- Table structure for table `res_product_modifier_sets`
--

CREATE TABLE `res_product_modifier_sets` (
  `modifier_set_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL COMMENT 'Table use to store the modifier sets applicable for a product'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `res_tables`
--

CREATE TABLE `res_tables` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `business_id` int(10) UNSIGNED DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `is_service_staff` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `business_id`, `is_default`, `is_service_staff`, `created_at`, `updated_at`) VALUES
(1, 'Admin#1', 'web', 1, 1, 0, '2026-04-09 09:53:34', '2026-04-09 09:53:34'),
(2, 'Cashier#1', 'web', 1, 0, 0, '2026-04-09 09:53:34', '2026-04-09 09:53:34'),
(3, 'SALES#1', 'web', 1, 0, 0, '2026-04-09 11:17:38', '2026-04-09 11:17:38');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(104, 1),
(105, 1),
(8, 2),
(25, 2),
(26, 2),
(73, 2),
(74, 2),
(75, 2),
(76, 2),
(77, 2),
(2, 3),
(6, 3),
(8, 3),
(25, 3),
(26, 3),
(27, 3),
(73, 3),
(74, 3),
(75, 3),
(78, 3),
(79, 3),
(80, 3),
(81, 3),
(82, 3),
(83, 3),
(84, 3),
(85, 3),
(86, 3),
(87, 3),
(88, 3),
(89, 3),
(90, 3),
(91, 3),
(92, 3),
(93, 3),
(94, 3),
(95, 3);

-- --------------------------------------------------------

--
-- Table structure for table `saas_bundles`
--

CREATE TABLE `saas_bundles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `business_type` varchar(191) DEFAULT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(191) NOT NULL DEFAULT '#0f766e',
  `is_popular` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saas_bundle_features`
--

CREATE TABLE `saas_bundle_features` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bundle_id` bigint(20) UNSIGNED NOT NULL,
  `feature_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saas_enquiries`
--

CREATE TABLE `saas_enquiries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) NOT NULL,
  `cycle` varchar(191) NOT NULL,
  `hosting` varchar(191) NOT NULL,
  `feature_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`feature_ids`)),
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(191) NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saas_features`
--

CREATE TABLE `saas_features` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `key` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(191) NOT NULL DEFAULT 'fa-check',
  `category` varchar(191) NOT NULL DEFAULT 'core',
  `applicable_to` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`applicable_to`)),
  `price_monthly` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price_quarterly` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price_yearly` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price_once` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saas_hosted_accounts`
--

CREATE TABLE `saas_hosted_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `domain` varchar(191) DEFAULT NULL,
  `server_ip` varchar(191) DEFAULT NULL,
  `server_notes` varchar(191) DEFAULT NULL,
  `hosting_fee_yearly` decimal(10,2) NOT NULL DEFAULT 0.00,
  `next_renewal_date` timestamp NULL DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saas_invoices`
--

CREATE TABLE `saas_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_no` varchar(191) NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `subscription_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(191) NOT NULL DEFAULT 'KES',
  `payment_method` varchar(191) DEFAULT NULL,
  `payment_reference` varchar(191) DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'unpaid',
  `type` varchar(191) NOT NULL DEFAULT 'subscription',
  `paid_at` timestamp NULL DEFAULT NULL,
  `due_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saas_settings`
--

CREATE TABLE `saas_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `group` varchar(50) NOT NULL DEFAULT 'general',
  `type` varchar(20) NOT NULL DEFAULT 'string',
  `label` varchar(191) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `saas_settings`
--

INSERT INTO `saas_settings` (`id`, `key`, `value`, `group`, `type`, `label`, `description`, `created_at`, `updated_at`) VALUES
(1, 'trial_enabled', '1', 'trial', 'bool', 'Free Trial Enabled', 'Allow new signups to start a free trial.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(2, 'trial_days', '3', 'trial', 'int', 'Trial Length (days)', '0 = no trial. Common values: 1, 3, 7, 14.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(3, 'trial_grace_days', '3', 'trial', 'int', 'Trial Grace Period (days)', 'Extra days after trial ends before account is suspended.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(4, 'campaign_active', '0', 'campaign', 'bool', 'Campaign Banner Active', 'Show the campaign banner on pricing page.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(5, 'campaign_title', 'Limited Time: Extended 14-Day Free Trial', 'campaign', 'string', 'Campaign Title', 'Headline shown on pricing page.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(6, 'campaign_message', 'Sign up today and get double the normal trial period.', 'campaign', 'string', 'Campaign Message', 'Sub-message for the campaign banner.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(7, 'campaign_discount_percent', '0', 'campaign', 'int', 'Campaign Discount %', '0-100. Applied to all feature prices during campaign.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(8, 'mpesa_enabled', '1', 'payment', 'bool', 'M-Pesa Enabled', 'Accept M-Pesa payments at checkout.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(9, 'mpesa_mode', 'manual', 'payment', 'string', 'M-Pesa Mode', 'manual = paybill instructions only. stk = Daraja STK Push auto-prompt.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(10, 'mpesa_paybill', '4117852', 'payment', 'string', 'M-Pesa Paybill Number', 'Shown on checkout pending page.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(11, 'mpesa_till', '', 'payment', 'string', 'M-Pesa Till Number (optional)', 'Leave blank to use paybill only.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(12, 'mpesa_env', 'sandbox', 'payment', 'string', 'Daraja Environment', 'sandbox or production. Switch to production when credentials are live.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(13, 'mpesa_shortcode', '', 'payment', 'string', 'Daraja Shortcode', 'STK Push business shortcode (Paybill or Till).', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(14, 'mpesa_consumer_key', '', 'payment', 'string', 'Daraja Consumer Key', 'From developer.safaricom.co.ke app credentials.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(15, 'mpesa_consumer_secret', '', 'payment', 'string', 'Daraja Consumer Secret', 'Keep this secret — do not share.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(16, 'mpesa_passkey', '', 'payment', 'string', 'Lipa Na M-Pesa Passkey', 'Used for STK Push password generation.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(17, 'mpesa_callback_url', 'https://apexpos.co.ke/api/mpesa/callback', 'payment', 'string', 'STK Callback URL', 'Must be HTTPS and publicly reachable.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(18, 'support_phone', '+254 700 000 000', 'general', 'string', 'Support Phone', 'Shown on checkout/portal pages.', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(19, 'support_email', 'support@apexpos.co.ke', 'general', 'string', 'Support Email', '', '2026-04-20 18:40:48', '2026-04-20 18:40:48'),
(20, 'onboarding_monthly_price', '2999', 'payment', 'int', 'Monthly Subscription Price (KES)', 'Shown during onboarding activation and on pricing page.', '2026-04-20 19:00:19', '2026-04-20 19:00:19');

-- --------------------------------------------------------

--
-- Table structure for table `saas_subscriptions`
--

CREATE TABLE `saas_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `billing_cycle` varchar(191) NOT NULL,
  `hosting_type` varchar(191) NOT NULL DEFAULT 'cloud',
  `total_amount` decimal(10,2) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `status` varchar(191) NOT NULL DEFAULT 'pending',
  `starts_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `grace_ends_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saas_subscription_features`
--

CREATE TABLE `saas_subscription_features` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` bigint(20) UNSIGNED NOT NULL,
  `feature_id` bigint(20) UNSIGNED NOT NULL,
  `price_locked` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `selling_price_groups`
--

CREATE TABLE `selling_price_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sell_line_warranties`
--

CREATE TABLE `sell_line_warranties` (
  `sell_line_id` int(11) NOT NULL,
  `warranty_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` text NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('gvCnIKn7xFsYXDLdYuJHB0TaOjLkNMIv7WqJI6bo', 2, '129.222.147.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YToxMDp7czo2OiJfdG9rZW4iO3M6NDA6Ilk0VUJwS3pxc3M3RWJLUG15bHNqV3ptUFl0Z2t1ZGZzbWFaTGJmQkEiO3M6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo0NDoiaHR0cHM6Ly9yZWVuc29uLmFwZXh0ZWNoc29sdXRpb25zLmNvLmtlL3N5bmMiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc3Njc0NjY5MTt9czo0OiJ1c2VyIjthOjc6e3M6MjoiaWQiO2k6MjtzOjc6InN1cm5hbWUiO047czoxMDoiZmlyc3RfbmFtZSI7czo1OiJTdXBlciI7czo5OiJsYXN0X25hbWUiO3M6NToiQWRtaW4iO3M6NToiZW1haWwiO3M6MTk6ImFkbWluQHJlZW5zb24uY28ua2UiO3M6MTE6ImJ1c2luZXNzX2lkIjtpOjE7czo4OiJsYW5ndWFnZSI7czoyOiJlbiI7fXM6ODoiYnVzaW5lc3MiO086MTI6IkFwcFxCdXNpbmVzcyI6MzA6e3M6MTM6IgAqAGNvbm5lY3Rpb24iO3M6NToibXlzcWwiO3M6ODoiACoAdGFibGUiO3M6ODoiYnVzaW5lc3MiO3M6MTM6IgAqAHByaW1hcnlLZXkiO3M6MjoiaWQiO3M6MTA6IgAqAGtleVR5cGUiO3M6MzoiaW50IjtzOjEyOiJpbmNyZW1lbnRpbmciO2I6MTtzOjc6IgAqAHdpdGgiO2E6MDp7fXM6MTI6IgAqAHdpdGhDb3VudCI7YTowOnt9czoxOToicHJldmVudHNMYXp5TG9hZGluZyI7YjowO3M6MTA6IgAqAHBlclBhZ2UiO2k6MTU7czo2OiJleGlzdHMiO2I6MTtzOjE4OiJ3YXNSZWNlbnRseUNyZWF0ZWQiO2I6MDtzOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7czoxMzoiACoAYXR0cmlidXRlcyI7YTo4Mzp7czoyOiJpZCI7aToxO3M6NDoibmFtZSI7czoxNToiUmVlbnNvbiBBZ3JvdmV0IjtzOjEzOiJidXNpbmVzc190eXBlIjtOO3M6MTE6ImN1cnJlbmN5X2lkIjtpOjEzMztzOjEwOiJzdGFydF9kYXRlIjtzOjEwOiIyMDI2LTA0LTA5IjtzOjEyOiJ0YXhfbnVtYmVyXzEiO047czoxMToidGF4X2xhYmVsXzEiO047czoxMjoidGF4X251bWJlcl8yIjtOO3M6MTE6InRheF9sYWJlbF8yIjtOO3M6MTI6ImNvZGVfbGFiZWxfMSI7TjtzOjY6ImNvZGVfMSI7TjtzOjEyOiJjb2RlX2xhYmVsXzIiO047czo2OiJjb2RlXzIiO047czoxNzoiZGVmYXVsdF9zYWxlc190YXgiO047czoyMjoiZGVmYXVsdF9wcm9maXRfcGVyY2VudCI7ZDoyNTtzOjg6Im93bmVyX2lkIjtpOjE7czo5OiJ0aW1lX3pvbmUiO3M6MTQ6IkFmcmljYS9OYWlyb2JpIjtzOjE0OiJmeV9zdGFydF9tb250aCI7aToxO3M6MTc6ImFjY291bnRpbmdfbWV0aG9kIjtzOjQ6ImZpZm8iO3M6MjI6ImRlZmF1bHRfc2FsZXNfZGlzY291bnQiO3M6NDoiMC4wMCI7czoxNDoic2VsbF9wcmljZV90YXgiO3M6ODoiaW5jbHVkZXMiO3M6NDoibG9nbyI7TjtzOjEwOiJza3VfcHJlZml4IjtOO3M6MjE6ImVuYWJsZV9wcm9kdWN0X2V4cGlyeSI7aToxO3M6MTE6ImV4cGlyeV90eXBlIjtzOjEwOiJhZGRfZXhwaXJ5IjtzOjE3OiJvbl9wcm9kdWN0X2V4cGlyeSI7czoxMjoia2VlcF9zZWxsaW5nIjtzOjE5OiJzdG9wX3NlbGxpbmdfYmVmb3JlIjtpOjA7czoxNDoiZW5hYmxlX3Rvb2x0aXAiO2k6MTtzOjI1OiJwdXJjaGFzZV9pbl9kaWZmX2N1cnJlbmN5IjtpOjA7czoyMDoicHVyY2hhc2VfY3VycmVuY3lfaWQiO047czoxNToicF9leGNoYW5nZV9yYXRlIjtzOjU6IjEuMDAwIjtzOjIxOiJ0cmFuc2FjdGlvbl9lZGl0X2RheXMiO2k6MzA7czoyMzoic3RvY2tfZXhwaXJ5X2FsZXJ0X2RheXMiO2k6MzA7czoxODoia2V5Ym9hcmRfc2hvcnRjdXRzIjtzOjI4ODoieyJwb3MiOnsiZXhwcmVzc19jaGVja291dCI6InNoaWZ0K2UiLCJwYXlfbl9ja2Vja291dCI6InNoaWZ0K3AiLCJkcmFmdCI6InNoaWZ0K2QiLCJjYW5jZWwiOiJzaGlmdCtjIiwicmVjZW50X3Byb2R1Y3RfcXVhbnRpdHkiOiJmMiIsIndlaWdoaW5nX3NjYWxlIjpudWxsLCJlZGl0X2Rpc2NvdW50Ijoic2hpZnQraSIsImVkaXRfb3JkZXJfdGF4Ijoic2hpZnQrdCIsImFkZF9wYXltZW50X3JvdyI6InNoaWZ0K3IiLCJmaW5hbGl6ZV9wYXltZW50Ijoic2hpZnQrZiIsImFkZF9uZXdfcHJvZHVjdCI6ImY0In19IjtzOjEyOiJwb3Nfc2V0dGluZ3MiO3M6NTYyOiJ7ImFtb3VudF9yb3VuZGluZ19tZXRob2QiOm51bGwsImNtbXNuX2NhbGN1bGF0aW9uX3R5cGUiOiJpbnZvaWNlX3ZhbHVlIiwicmF6b3JfcGF5X2tleV9pZCI6bnVsbCwicmF6b3JfcGF5X2tleV9zZWNyZXQiOm51bGwsInN0cmlwZV9wdWJsaWNfa2V5IjpudWxsLCJzdHJpcGVfc2VjcmV0X2tleSI6bnVsbCwiZGlzYWJsZV9kcmFmdCI6IjEiLCJoaWRlX3Byb2R1Y3Rfc3VnZ2VzdGlvbiI6IjEiLCJkaXNhYmxlX2Rpc2NvdW50IjoiMSIsImRpc2FibGVfb3JkZXJfdGF4IjoiMSIsImlzX3Bvc19zdWJ0b3RhbF9lZGl0YWJsZSI6IjEiLCJkaXNhYmxlX3N1c3BlbmQiOiIxIiwiZW5hYmxlX3RyYW5zYWN0aW9uX2RhdGUiOiIxIiwiZGlzYWJsZV9jcmVkaXRfc2FsZV9idXR0b24iOiIxIiwiZGlzcGxheV9zY3JlZW5faGVhZGluZyI6bnVsbCwiY2FzaF9kZW5vbWluYXRpb25zIjpudWxsLCJlbmFibGVfY2FzaF9kZW5vbWluYXRpb25fb24iOiJwb3Nfc2NyZWVuIiwiZGlzYWJsZV9wYXlfY2hlY2tvdXQiOjAsImRpc2FibGVfZXhwcmVzc19jaGVja291dCI6MCwiaGlkZV9yZWNlbnRfdHJhbnMiOjB9IjtzOjIyOiJ3ZWlnaGluZ19zY2FsZV9zZXR0aW5nIjtzOjg4OiJ7ImxhYmVsX3ByZWZpeCI6bnVsbCwicHJvZHVjdF9za3VfbGVuZ3RoIjoiNCIsInF0eV9sZW5ndGgiOiIzIiwicXR5X2xlbmd0aF9kZWNpbWFsIjoiMiJ9IjtzOjEyOiJlbmFibGVfYnJhbmQiO2k6MTtzOjE1OiJlbmFibGVfY2F0ZWdvcnkiO2k6MTtzOjE5OiJlbmFibGVfc3ViX2NhdGVnb3J5IjtpOjE7czoxNjoiZW5hYmxlX3ByaWNlX3RheCI7aToxO3M6MjI6ImVuYWJsZV9wdXJjaGFzZV9zdGF0dXMiO2k6MTtzOjE3OiJlbmFibGVfbG90X251bWJlciI7aTowO3M6MTI6ImRlZmF1bHRfdW5pdCI7TjtzOjE2OiJlbmFibGVfc3ViX3VuaXRzIjtpOjA7czoxMjoiZW5hYmxlX3JhY2tzIjtpOjA7czoxMDoiZW5hYmxlX3JvdyI7aTowO3M6MTU6ImVuYWJsZV9wb3NpdGlvbiI7aTowO3M6MzY6ImVuYWJsZV9lZGl0aW5nX3Byb2R1Y3RfZnJvbV9wdXJjaGFzZSI7aToxO3M6MTU6InNhbGVzX2Ntc25fYWdudCI7TjtzOjIwOiJpdGVtX2FkZGl0aW9uX21ldGhvZCI7aToxO3M6MTc6ImVuYWJsZV9pbmxpbmVfdGF4IjtpOjA7czoyNToiY3VycmVuY3lfc3ltYm9sX3BsYWNlbWVudCI7czo2OiJiZWZvcmUiO3M6MTU6ImVuYWJsZWRfbW9kdWxlcyI7czo2NDoiWyJwdXJjaGFzZXMiLCJhZGRfc2FsZSIsInBvc19zYWxlIiwic3RvY2tfdHJhbnNmZXJzIiwiZXhwZW5zZXMiXSI7czoxMToiZGF0ZV9mb3JtYXQiO3M6NToibS9kL1kiO3M6MTE6InRpbWVfZm9ybWF0IjtzOjI6IjI0IjtzOjE4OiJjdXJyZW5jeV9wcmVjaXNpb24iO2k6MjtzOjE4OiJxdWFudGl0eV9wcmVjaXNpb24iO2k6MjtzOjE1OiJyZWZfbm9fcHJlZml4ZXMiO3M6MzQ2OiJ7InB1cmNoYXNlIjoiUE8iLCJwdXJjaGFzZV9yZXR1cm4iOm51bGwsInB1cmNoYXNlX3JlcXVpc2l0aW9uIjpudWxsLCJwdXJjaGFzZV9vcmRlciI6bnVsbCwic3RvY2tfdHJhbnNmZXIiOiJTVCIsInN0b2NrX2FkanVzdG1lbnQiOiJTQSIsInNlbGxfcmV0dXJuIjoiQ04iLCJleHBlbnNlIjoiRVAiLCJjb250YWN0cyI6IkNPIiwicHVyY2hhc2VfcGF5bWVudCI6IlBQIiwic2VsbF9wYXltZW50IjoiU1AiLCJleHBlbnNlX3BheW1lbnQiOm51bGwsImJ1c2luZXNzX2xvY2F0aW9uIjoiQkwiLCJ1c2VybmFtZSI6bnVsbCwic3Vic2NyaXB0aW9uIjpudWxsLCJkcmFmdCI6bnVsbCwic2FsZXNfb3JkZXIiOm51bGx9IjtzOjExOiJ0aGVtZV9jb2xvciI7czo1OiJncmVlbiI7czoxMDoiY3JlYXRlZF9ieSI7TjtzOjk6ImVuYWJsZV9ycCI7aTowO3M6NzoicnBfbmFtZSI7TjtzOjE4OiJhbW91bnRfZm9yX3VuaXRfcnAiO3M6NjoiMS4wMDAwIjtzOjIyOiJtaW5fb3JkZXJfdG90YWxfZm9yX3JwIjtzOjY6IjEuMDAwMCI7czoxNjoibWF4X3JwX3Blcl9vcmRlciI7TjtzOjI1OiJyZWRlZW1fYW1vdW50X3Blcl91bml0X3JwIjtzOjY6IjEuMDAwMCI7czoyNjoibWluX29yZGVyX3RvdGFsX2Zvcl9yZWRlZW0iO3M6NjoiMS4wMDAwIjtzOjE2OiJtaW5fcmVkZWVtX3BvaW50IjtOO3M6MTY6Im1heF9yZWRlZW1fcG9pbnQiO047czoxNjoicnBfZXhwaXJ5X3BlcmlvZCI7TjtzOjE0OiJycF9leHBpcnlfdHlwZSI7czo0OiJ5ZWFyIjtzOjE0OiJlbWFpbF9zZXR0aW5ncyI7czoxNjg6InsibWFpbF9kcml2ZXIiOiJzbXRwIiwibWFpbF9ob3N0IjpudWxsLCJtYWlsX3BvcnQiOm51bGwsIm1haWxfdXNlcm5hbWUiOm51bGwsIm1haWxfcGFzc3dvcmQiOm51bGwsIm1haWxfZW5jcnlwdGlvbiI6bnVsbCwibWFpbF9mcm9tX2FkZHJlc3MiOm51bGwsIm1haWxfZnJvbV9uYW1lIjpudWxsfSI7czoxMjoic21zX3NldHRpbmdzIjtzOjgwODoieyJzbXNfc2VydmljZSI6Im90aGVyIiwibmV4bW9fa2V5IjpudWxsLCJuZXhtb19zZWNyZXQiOm51bGwsIm5leG1vX2Zyb20iOm51bGwsInR3aWxpb19zaWQiOm51bGwsInR3aWxpb190b2tlbiI6bnVsbCwidHdpbGlvX2Zyb20iOm51bGwsImFkdmFudGFfYXBpX2tleSI6bnVsbCwiYWR2YW50YV9wYXJ0bmVyX2lkIjpudWxsLCJhZHZhbnRhX3Nob3J0Y29kZSI6bnVsbCwidXJsIjpudWxsLCJzZW5kX3RvX3BhcmFtX25hbWUiOiJ0byIsInNlbmRfdG9fcGFyYW1fdHlwZSI6InN0cmluZyIsIm1zZ19wYXJhbV9uYW1lIjoidGV4dCIsInJlcXVlc3RfbWV0aG9kIjoicG9zdCIsImRhdGFfcGFyYW1ldGVyX3R5cGUiOiJmb3JtLWRhdGEiLCJoZWFkZXJfMSI6bnVsbCwiaGVhZGVyX3ZhbF8xIjpudWxsLCJoZWFkZXJfMiI6bnVsbCwiaGVhZGVyX3ZhbF8yIjpudWxsLCJoZWFkZXJfMyI6bnVsbCwiaGVhZGVyX3ZhbF8zIjpudWxsLCJwYXJhbV8xIjpudWxsLCJwYXJhbV92YWxfMSI6bnVsbCwicGFyYW1fMiI6bnVsbCwicGFyYW1fdmFsXzIiOm51bGwsInBhcmFtXzMiOm51bGwsInBhcmFtX3ZhbF8zIjpudWxsLCJwYXJhbV80IjpudWxsLCJwYXJhbV92YWxfNCI6bnVsbCwicGFyYW1fNSI6bnVsbCwicGFyYW1fdmFsXzUiOm51bGwsInBhcmFtXzYiOm51bGwsInBhcmFtX3ZhbF82IjpudWxsLCJwYXJhbV83IjpudWxsLCJwYXJhbV92YWxfNyI6bnVsbCwicGFyYW1fOCI6bnVsbCwicGFyYW1fdmFsXzgiOm51bGwsInBhcmFtXzkiOm51bGwsInBhcmFtX3ZhbF85IjpudWxsLCJwYXJhbV8xMCI6bnVsbCwicGFyYW1fdmFsXzEwIjpudWxsfSI7czoxNzoid2hhdHNhcHBfc2V0dGluZ3MiO3M6MTA5OToieyJlbmFibGVkIjoiMCIsImFwaV9wcm92aWRlciI6Im1ldGEiLCJhY2Nlc3NfdG9rZW4iOm51bGwsInBob25lX251bWJlcl9pZCI6bnVsbCwiYnVzaW5lc3NfYWNjb3VudF9pZCI6bnVsbCwidmVyaWZ5X3Rva2VuIjpudWxsLCJhcGlfdXJsIjoiaHR0cHM6XC9cL2dyYXBoLmZhY2Vib29rLmNvbVwvdjE3LjAiLCJ0d2lsaW9fc2lkIjpudWxsLCJ0d2lsaW9fdG9rZW4iOm51bGwsInR3aWxpb19mcm9tIjpudWxsLCJjdXN0b21fdXJsIjpudWxsLCJjdXN0b21fYXBpX2tleSI6bnVsbCwiY3VzdG9tX3NlbmRlciI6bnVsbCwic2FsZV9tZXNzYWdlX3RlbXBsYXRlIjoiSGVsbG8ge2N1c3RvbWVyX25hbWV9LFxyXG5cclxuVGhhbmsgeW91IGZvciB5b3VyIHB1cmNoYXNlIGF0IHtidXNpbmVzc19uYW1lfSFcclxuXHJcbkludm9pY2U6IHtpbnZvaWNlX25vfVxyXG5Ub3RhbDoge3RvdGFsfVxyXG5EYXRlOiB7ZGF0ZX1cclxuXHJcbldlIGFwcHJlY2lhdGUgeW91ciBidXNpbmVzcyEiLCJwYXltZW50X3JlbWluZGVyX3RlbXBsYXRlIjoiSGVsbG8ge2N1c3RvbWVyX25hbWV9LFxyXG5cclxuVGhpcyBpcyBhIGZyaWVuZGx5IHJlbWluZGVyIGFib3V0IHlvdXIgcGVuZGluZyBwYXltZW50LlxyXG5cclxuSW52b2ljZToge2ludm9pY2Vfbm99XHJcbkFtb3VudCBEdWU6IHthbW91bnRfZHVlfVxyXG5EdWUgRGF0ZToge2R1ZV9kYXRlfVxyXG5cclxuUGxlYXNlIGNvbnRhY3QgdXMgaWYgeW91IGhhdmUgYW55IHF1ZXN0aW9ucy4iLCJmb2xsb3d1cF9ub3RpZmljYXRpb25fdGltZSI6IjA5OjAwIiwiZm9sbG93dXBfbm90aWZpY2F0aW9uX2ZyZXF1ZW5jeSI6ImRhaWx5IiwiZm9sbG93dXBfbm90aWZpY2F0aW9uX3RlbXBsYXRlIjoiSW50ZXJuYWwgUmVtaW5kZXI6IEZvbGxvdyB1cCB3aXRoIHtjdXN0b21lcl9uYW1lfSByZWdhcmRpbmcgdGhlaXIgaW5xdWlyeSBmb3Ige3Byb2R1Y3RfbmFtZX0uIFBsYW5uZWQgRGF0ZToge2ZvbGxvd3VwX2RhdGV9Iiwic2NoZWR1bGVfZW5hYmxlZCI6IjAiLCJzY2hlZHVsZV90aW1lIjoiMDk6MDAiLCJzY2hlZHVsZV9kYXlzIjpbIm1vbmRheSIsInR1ZXNkYXkiLCJ3ZWRuZXNkYXkiLCJ0aHVyc2RheSIsImZyaWRheSJdfSI7czoxMzoiY3VzdG9tX2xhYmVscyI7czoyNTMxOiJ7InBheW1lbnRzIjp7ImN1c3RvbV9wYXlfMSI6Ik1QRVNBIiwiY3VzdG9tX3BheV8yIjpudWxsLCJjdXN0b21fcGF5XzMiOm51bGwsImN1c3RvbV9wYXlfNCI6bnVsbCwiY3VzdG9tX3BheV81IjpudWxsLCJjdXN0b21fcGF5XzYiOm51bGwsImN1c3RvbV9wYXlfNyI6bnVsbH0sImNvbnRhY3QiOnsiY3VzdG9tX2ZpZWxkXzEiOm51bGwsImN1c3RvbV9maWVsZF8yIjpudWxsLCJjdXN0b21fZmllbGRfMyI6bnVsbCwiY3VzdG9tX2ZpZWxkXzQiOm51bGwsImN1c3RvbV9maWVsZF81IjpudWxsLCJjdXN0b21fZmllbGRfNiI6bnVsbCwiY3VzdG9tX2ZpZWxkXzciOm51bGwsImN1c3RvbV9maWVsZF84IjpudWxsLCJjdXN0b21fZmllbGRfOSI6bnVsbCwiY3VzdG9tX2ZpZWxkXzEwIjpudWxsfSwicHJvZHVjdCI6eyJjdXN0b21fZmllbGRfMSI6bnVsbCwiY3VzdG9tX2ZpZWxkXzIiOm51bGwsImN1c3RvbV9maWVsZF8zIjpudWxsLCJjdXN0b21fZmllbGRfNCI6bnVsbCwiY3VzdG9tX2ZpZWxkXzUiOm51bGwsImN1c3RvbV9maWVsZF82IjpudWxsLCJjdXN0b21fZmllbGRfNyI6bnVsbCwiY3VzdG9tX2ZpZWxkXzgiOm51bGwsImN1c3RvbV9maWVsZF85IjpudWxsLCJjdXN0b21fZmllbGRfMTAiOm51bGwsImN1c3RvbV9maWVsZF8xMSI6bnVsbCwiY3VzdG9tX2ZpZWxkXzEyIjpudWxsLCJjdXN0b21fZmllbGRfMTMiOm51bGwsImN1c3RvbV9maWVsZF8xNCI6bnVsbCwiY3VzdG9tX2ZpZWxkXzE1IjpudWxsLCJjdXN0b21fZmllbGRfMTYiOm51bGwsImN1c3RvbV9maWVsZF8xNyI6bnVsbCwiY3VzdG9tX2ZpZWxkXzE4IjpudWxsLCJjdXN0b21fZmllbGRfMTkiOm51bGwsImN1c3RvbV9maWVsZF8yMCI6bnVsbH0sInByb2R1Y3RfY2ZfZGV0YWlscyI6eyIxIjp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCIyIjp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCIzIjp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCI0Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCI1Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCI2Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCI3Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCI4Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCI5Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCIxMCI6eyJ0eXBlIjpudWxsLCJkcm9wZG93bl9vcHRpb25zIjpudWxsfSwiMTEiOnsidHlwZSI6bnVsbCwiZHJvcGRvd25fb3B0aW9ucyI6bnVsbH0sIjEyIjp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCIxMyI6eyJ0eXBlIjpudWxsLCJkcm9wZG93bl9vcHRpb25zIjpudWxsfSwiMTQiOnsidHlwZSI6bnVsbCwiZHJvcGRvd25fb3B0aW9ucyI6bnVsbH0sIjE1Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCIxNiI6eyJ0eXBlIjpudWxsLCJkcm9wZG93bl9vcHRpb25zIjpudWxsfSwiMTciOnsidHlwZSI6bnVsbCwiZHJvcGRvd25fb3B0aW9ucyI6bnVsbH0sIjE4Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCIxOSI6eyJ0eXBlIjpudWxsLCJkcm9wZG93bl9vcHRpb25zIjpudWxsfSwiMjAiOnsidHlwZSI6bnVsbCwiZHJvcGRvd25fb3B0aW9ucyI6bnVsbH19LCJsb2NhdGlvbiI6eyJjdXN0b21fZmllbGRfMSI6bnVsbCwiY3VzdG9tX2ZpZWxkXzIiOm51bGwsImN1c3RvbV9maWVsZF8zIjpudWxsLCJjdXN0b21fZmllbGRfNCI6bnVsbH0sInVzZXIiOnsiY3VzdG9tX2ZpZWxkXzEiOm51bGwsImN1c3RvbV9maWVsZF8yIjpudWxsLCJjdXN0b21fZmllbGRfMyI6bnVsbCwiY3VzdG9tX2ZpZWxkXzQiOm51bGx9LCJwdXJjaGFzZSI6eyJjdXN0b21fZmllbGRfMSI6bnVsbCwiY3VzdG9tX2ZpZWxkXzIiOm51bGwsImN1c3RvbV9maWVsZF8zIjpudWxsLCJjdXN0b21fZmllbGRfNCI6bnVsbH0sInB1cmNoYXNlX3NoaXBwaW5nIjp7ImN1c3RvbV9maWVsZF8xIjpudWxsLCJjdXN0b21fZmllbGRfMiI6bnVsbCwiY3VzdG9tX2ZpZWxkXzMiOm51bGwsImN1c3RvbV9maWVsZF80IjpudWxsLCJjdXN0b21fZmllbGRfNSI6bnVsbH0sInNlbGwiOnsiY3VzdG9tX2ZpZWxkXzEiOm51bGwsImN1c3RvbV9maWVsZF8yIjpudWxsLCJjdXN0b21fZmllbGRfMyI6bnVsbCwiY3VzdG9tX2ZpZWxkXzQiOm51bGx9LCJzaGlwcGluZyI6eyJjdXN0b21fZmllbGRfMSI6bnVsbCwiY3VzdG9tX2ZpZWxkXzIiOm51bGwsImN1c3RvbV9maWVsZF8zIjpudWxsLCJjdXN0b21fZmllbGRfNCI6bnVsbCwiY3VzdG9tX2ZpZWxkXzUiOm51bGx9LCJ0eXBlc19vZl9zZXJ2aWNlIjp7ImN1c3RvbV9maWVsZF8xIjpudWxsLCJjdXN0b21fZmllbGRfMiI6bnVsbCwiY3VzdG9tX2ZpZWxkXzMiOm51bGwsImN1c3RvbV9maWVsZF80IjpudWxsLCJjdXN0b21fZmllbGRfNSI6bnVsbCwiY3VzdG9tX2ZpZWxkXzYiOm51bGx9fSI7czoxNToiY29tbW9uX3NldHRpbmdzIjtzOjY3OiJ7ImRlZmF1bHRfY3JlZGl0X2xpbWl0IjpudWxsLCJkZWZhdWx0X2RhdGF0YWJsZV9wYWdlX2VudHJpZXMiOiIyNSJ9IjtzOjk6ImlzX2FjdGl2ZSI7aToxO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjYtMDQtMDkgMDk6NTM6MzQiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjYtMDQtMjAgMjA6MDM6NDAiO3M6MTU6ImRpZ2l0YXhfYXBpX2tleSI7TjtzOjE1OiJldGltc19zeW5jX21vZGUiO3M6MTA6ImJhY2tncm91bmQiO3M6MTA6ImV0aW1zX3RwaW4iO047czoxMzoiZXRpbXNfZW5hYmxlZCI7aTowO31zOjExOiIAKgBvcmlnaW5hbCI7YTo4Mzp7czoyOiJpZCI7aToxO3M6NDoibmFtZSI7czoxNToiUmVlbnNvbiBBZ3JvdmV0IjtzOjEzOiJidXNpbmVzc190eXBlIjtOO3M6MTE6ImN1cnJlbmN5X2lkIjtpOjEzMztzOjEwOiJzdGFydF9kYXRlIjtzOjEwOiIyMDI2LTA0LTA5IjtzOjEyOiJ0YXhfbnVtYmVyXzEiO047czoxMToidGF4X2xhYmVsXzEiO047czoxMjoidGF4X251bWJlcl8yIjtOO3M6MTE6InRheF9sYWJlbF8yIjtOO3M6MTI6ImNvZGVfbGFiZWxfMSI7TjtzOjY6ImNvZGVfMSI7TjtzOjEyOiJjb2RlX2xhYmVsXzIiO047czo2OiJjb2RlXzIiO047czoxNzoiZGVmYXVsdF9zYWxlc190YXgiO047czoyMjoiZGVmYXVsdF9wcm9maXRfcGVyY2VudCI7ZDoyNTtzOjg6Im93bmVyX2lkIjtpOjE7czo5OiJ0aW1lX3pvbmUiO3M6MTQ6IkFmcmljYS9OYWlyb2JpIjtzOjE0OiJmeV9zdGFydF9tb250aCI7aToxO3M6MTc6ImFjY291bnRpbmdfbWV0aG9kIjtzOjQ6ImZpZm8iO3M6MjI6ImRlZmF1bHRfc2FsZXNfZGlzY291bnQiO3M6NDoiMC4wMCI7czoxNDoic2VsbF9wcmljZV90YXgiO3M6ODoiaW5jbHVkZXMiO3M6NDoibG9nbyI7TjtzOjEwOiJza3VfcHJlZml4IjtOO3M6MjE6ImVuYWJsZV9wcm9kdWN0X2V4cGlyeSI7aToxO3M6MTE6ImV4cGlyeV90eXBlIjtzOjEwOiJhZGRfZXhwaXJ5IjtzOjE3OiJvbl9wcm9kdWN0X2V4cGlyeSI7czoxMjoia2VlcF9zZWxsaW5nIjtzOjE5OiJzdG9wX3NlbGxpbmdfYmVmb3JlIjtpOjA7czoxNDoiZW5hYmxlX3Rvb2x0aXAiO2k6MTtzOjI1OiJwdXJjaGFzZV9pbl9kaWZmX2N1cnJlbmN5IjtpOjA7czoyMDoicHVyY2hhc2VfY3VycmVuY3lfaWQiO047czoxNToicF9leGNoYW5nZV9yYXRlIjtzOjU6IjEuMDAwIjtzOjIxOiJ0cmFuc2FjdGlvbl9lZGl0X2RheXMiO2k6MzA7czoyMzoic3RvY2tfZXhwaXJ5X2FsZXJ0X2RheXMiO2k6MzA7czoxODoia2V5Ym9hcmRfc2hvcnRjdXRzIjtzOjI4ODoieyJwb3MiOnsiZXhwcmVzc19jaGVja291dCI6InNoaWZ0K2UiLCJwYXlfbl9ja2Vja291dCI6InNoaWZ0K3AiLCJkcmFmdCI6InNoaWZ0K2QiLCJjYW5jZWwiOiJzaGlmdCtjIiwicmVjZW50X3Byb2R1Y3RfcXVhbnRpdHkiOiJmMiIsIndlaWdoaW5nX3NjYWxlIjpudWxsLCJlZGl0X2Rpc2NvdW50Ijoic2hpZnQraSIsImVkaXRfb3JkZXJfdGF4Ijoic2hpZnQrdCIsImFkZF9wYXltZW50X3JvdyI6InNoaWZ0K3IiLCJmaW5hbGl6ZV9wYXltZW50Ijoic2hpZnQrZiIsImFkZF9uZXdfcHJvZHVjdCI6ImY0In19IjtzOjEyOiJwb3Nfc2V0dGluZ3MiO3M6NTYyOiJ7ImFtb3VudF9yb3VuZGluZ19tZXRob2QiOm51bGwsImNtbXNuX2NhbGN1bGF0aW9uX3R5cGUiOiJpbnZvaWNlX3ZhbHVlIiwicmF6b3JfcGF5X2tleV9pZCI6bnVsbCwicmF6b3JfcGF5X2tleV9zZWNyZXQiOm51bGwsInN0cmlwZV9wdWJsaWNfa2V5IjpudWxsLCJzdHJpcGVfc2VjcmV0X2tleSI6bnVsbCwiZGlzYWJsZV9kcmFmdCI6IjEiLCJoaWRlX3Byb2R1Y3Rfc3VnZ2VzdGlvbiI6IjEiLCJkaXNhYmxlX2Rpc2NvdW50IjoiMSIsImRpc2FibGVfb3JkZXJfdGF4IjoiMSIsImlzX3Bvc19zdWJ0b3RhbF9lZGl0YWJsZSI6IjEiLCJkaXNhYmxlX3N1c3BlbmQiOiIxIiwiZW5hYmxlX3RyYW5zYWN0aW9uX2RhdGUiOiIxIiwiZGlzYWJsZV9jcmVkaXRfc2FsZV9idXR0b24iOiIxIiwiZGlzcGxheV9zY3JlZW5faGVhZGluZyI6bnVsbCwiY2FzaF9kZW5vbWluYXRpb25zIjpudWxsLCJlbmFibGVfY2FzaF9kZW5vbWluYXRpb25fb24iOiJwb3Nfc2NyZWVuIiwiZGlzYWJsZV9wYXlfY2hlY2tvdXQiOjAsImRpc2FibGVfZXhwcmVzc19jaGVja291dCI6MCwiaGlkZV9yZWNlbnRfdHJhbnMiOjB9IjtzOjIyOiJ3ZWlnaGluZ19zY2FsZV9zZXR0aW5nIjtzOjg4OiJ7ImxhYmVsX3ByZWZpeCI6bnVsbCwicHJvZHVjdF9za3VfbGVuZ3RoIjoiNCIsInF0eV9sZW5ndGgiOiIzIiwicXR5X2xlbmd0aF9kZWNpbWFsIjoiMiJ9IjtzOjEyOiJlbmFibGVfYnJhbmQiO2k6MTtzOjE1OiJlbmFibGVfY2F0ZWdvcnkiO2k6MTtzOjE5OiJlbmFibGVfc3ViX2NhdGVnb3J5IjtpOjE7czoxNjoiZW5hYmxlX3ByaWNlX3RheCI7aToxO3M6MjI6ImVuYWJsZV9wdXJjaGFzZV9zdGF0dXMiO2k6MTtzOjE3OiJlbmFibGVfbG90X251bWJlciI7aTowO3M6MTI6ImRlZmF1bHRfdW5pdCI7TjtzOjE2OiJlbmFibGVfc3ViX3VuaXRzIjtpOjA7czoxMjoiZW5hYmxlX3JhY2tzIjtpOjA7czoxMDoiZW5hYmxlX3JvdyI7aTowO3M6MTU6ImVuYWJsZV9wb3NpdGlvbiI7aTowO3M6MzY6ImVuYWJsZV9lZGl0aW5nX3Byb2R1Y3RfZnJvbV9wdXJjaGFzZSI7aToxO3M6MTU6InNhbGVzX2Ntc25fYWdudCI7TjtzOjIwOiJpdGVtX2FkZGl0aW9uX21ldGhvZCI7aToxO3M6MTc6ImVuYWJsZV9pbmxpbmVfdGF4IjtpOjA7czoyNToiY3VycmVuY3lfc3ltYm9sX3BsYWNlbWVudCI7czo2OiJiZWZvcmUiO3M6MTU6ImVuYWJsZWRfbW9kdWxlcyI7czo2NDoiWyJwdXJjaGFzZXMiLCJhZGRfc2FsZSIsInBvc19zYWxlIiwic3RvY2tfdHJhbnNmZXJzIiwiZXhwZW5zZXMiXSI7czoxMToiZGF0ZV9mb3JtYXQiO3M6NToibS9kL1kiO3M6MTE6InRpbWVfZm9ybWF0IjtzOjI6IjI0IjtzOjE4OiJjdXJyZW5jeV9wcmVjaXNpb24iO2k6MjtzOjE4OiJxdWFudGl0eV9wcmVjaXNpb24iO2k6MjtzOjE1OiJyZWZfbm9fcHJlZml4ZXMiO3M6MzQ2OiJ7InB1cmNoYXNlIjoiUE8iLCJwdXJjaGFzZV9yZXR1cm4iOm51bGwsInB1cmNoYXNlX3JlcXVpc2l0aW9uIjpudWxsLCJwdXJjaGFzZV9vcmRlciI6bnVsbCwic3RvY2tfdHJhbnNmZXIiOiJTVCIsInN0b2NrX2FkanVzdG1lbnQiOiJTQSIsInNlbGxfcmV0dXJuIjoiQ04iLCJleHBlbnNlIjoiRVAiLCJjb250YWN0cyI6IkNPIiwicHVyY2hhc2VfcGF5bWVudCI6IlBQIiwic2VsbF9wYXltZW50IjoiU1AiLCJleHBlbnNlX3BheW1lbnQiOm51bGwsImJ1c2luZXNzX2xvY2F0aW9uIjoiQkwiLCJ1c2VybmFtZSI6bnVsbCwic3Vic2NyaXB0aW9uIjpudWxsLCJkcmFmdCI6bnVsbCwic2FsZXNfb3JkZXIiOm51bGx9IjtzOjExOiJ0aGVtZV9jb2xvciI7czo1OiJncmVlbiI7czoxMDoiY3JlYXRlZF9ieSI7TjtzOjk6ImVuYWJsZV9ycCI7aTowO3M6NzoicnBfbmFtZSI7TjtzOjE4OiJhbW91bnRfZm9yX3VuaXRfcnAiO3M6NjoiMS4wMDAwIjtzOjIyOiJtaW5fb3JkZXJfdG90YWxfZm9yX3JwIjtzOjY6IjEuMDAwMCI7czoxNjoibWF4X3JwX3Blcl9vcmRlciI7TjtzOjI1OiJyZWRlZW1fYW1vdW50X3Blcl91bml0X3JwIjtzOjY6IjEuMDAwMCI7czoyNjoibWluX29yZGVyX3RvdGFsX2Zvcl9yZWRlZW0iO3M6NjoiMS4wMDAwIjtzOjE2OiJtaW5fcmVkZWVtX3BvaW50IjtOO3M6MTY6Im1heF9yZWRlZW1fcG9pbnQiO047czoxNjoicnBfZXhwaXJ5X3BlcmlvZCI7TjtzOjE0OiJycF9leHBpcnlfdHlwZSI7czo0OiJ5ZWFyIjtzOjE0OiJlbWFpbF9zZXR0aW5ncyI7czoxNjg6InsibWFpbF9kcml2ZXIiOiJzbXRwIiwibWFpbF9ob3N0IjpudWxsLCJtYWlsX3BvcnQiOm51bGwsIm1haWxfdXNlcm5hbWUiOm51bGwsIm1haWxfcGFzc3dvcmQiOm51bGwsIm1haWxfZW5jcnlwdGlvbiI6bnVsbCwibWFpbF9mcm9tX2FkZHJlc3MiOm51bGwsIm1haWxfZnJvbV9uYW1lIjpudWxsfSI7czoxMjoic21zX3NldHRpbmdzIjtzOjgwODoieyJzbXNfc2VydmljZSI6Im90aGVyIiwibmV4bW9fa2V5IjpudWxsLCJuZXhtb19zZWNyZXQiOm51bGwsIm5leG1vX2Zyb20iOm51bGwsInR3aWxpb19zaWQiOm51bGwsInR3aWxpb190b2tlbiI6bnVsbCwidHdpbGlvX2Zyb20iOm51bGwsImFkdmFudGFfYXBpX2tleSI6bnVsbCwiYWR2YW50YV9wYXJ0bmVyX2lkIjpudWxsLCJhZHZhbnRhX3Nob3J0Y29kZSI6bnVsbCwidXJsIjpudWxsLCJzZW5kX3RvX3BhcmFtX25hbWUiOiJ0byIsInNlbmRfdG9fcGFyYW1fdHlwZSI6InN0cmluZyIsIm1zZ19wYXJhbV9uYW1lIjoidGV4dCIsInJlcXVlc3RfbWV0aG9kIjoicG9zdCIsImRhdGFfcGFyYW1ldGVyX3R5cGUiOiJmb3JtLWRhdGEiLCJoZWFkZXJfMSI6bnVsbCwiaGVhZGVyX3ZhbF8xIjpudWxsLCJoZWFkZXJfMiI6bnVsbCwiaGVhZGVyX3ZhbF8yIjpudWxsLCJoZWFkZXJfMyI6bnVsbCwiaGVhZGVyX3ZhbF8zIjpudWxsLCJwYXJhbV8xIjpudWxsLCJwYXJhbV92YWxfMSI6bnVsbCwicGFyYW1fMiI6bnVsbCwicGFyYW1fdmFsXzIiOm51bGwsInBhcmFtXzMiOm51bGwsInBhcmFtX3ZhbF8zIjpudWxsLCJwYXJhbV80IjpudWxsLCJwYXJhbV92YWxfNCI6bnVsbCwicGFyYW1fNSI6bnVsbCwicGFyYW1fdmFsXzUiOm51bGwsInBhcmFtXzYiOm51bGwsInBhcmFtX3ZhbF82IjpudWxsLCJwYXJhbV83IjpudWxsLCJwYXJhbV92YWxfNyI6bnVsbCwicGFyYW1fOCI6bnVsbCwicGFyYW1fdmFsXzgiOm51bGwsInBhcmFtXzkiOm51bGwsInBhcmFtX3ZhbF85IjpudWxsLCJwYXJhbV8xMCI6bnVsbCwicGFyYW1fdmFsXzEwIjpudWxsfSI7czoxNzoid2hhdHNhcHBfc2V0dGluZ3MiO3M6MTA5OToieyJlbmFibGVkIjoiMCIsImFwaV9wcm92aWRlciI6Im1ldGEiLCJhY2Nlc3NfdG9rZW4iOm51bGwsInBob25lX251bWJlcl9pZCI6bnVsbCwiYnVzaW5lc3NfYWNjb3VudF9pZCI6bnVsbCwidmVyaWZ5X3Rva2VuIjpudWxsLCJhcGlfdXJsIjoiaHR0cHM6XC9cL2dyYXBoLmZhY2Vib29rLmNvbVwvdjE3LjAiLCJ0d2lsaW9fc2lkIjpudWxsLCJ0d2lsaW9fdG9rZW4iOm51bGwsInR3aWxpb19mcm9tIjpudWxsLCJjdXN0b21fdXJsIjpudWxsLCJjdXN0b21fYXBpX2tleSI6bnVsbCwiY3VzdG9tX3NlbmRlciI6bnVsbCwic2FsZV9tZXNzYWdlX3RlbXBsYXRlIjoiSGVsbG8ge2N1c3RvbWVyX25hbWV9LFxyXG5cclxuVGhhbmsgeW91IGZvciB5b3VyIHB1cmNoYXNlIGF0IHtidXNpbmVzc19uYW1lfSFcclxuXHJcbkludm9pY2U6IHtpbnZvaWNlX25vfVxyXG5Ub3RhbDoge3RvdGFsfVxyXG5EYXRlOiB7ZGF0ZX1cclxuXHJcbldlIGFwcHJlY2lhdGUgeW91ciBidXNpbmVzcyEiLCJwYXltZW50X3JlbWluZGVyX3RlbXBsYXRlIjoiSGVsbG8ge2N1c3RvbWVyX25hbWV9LFxyXG5cclxuVGhpcyBpcyBhIGZyaWVuZGx5IHJlbWluZGVyIGFib3V0IHlvdXIgcGVuZGluZyBwYXltZW50LlxyXG5cclxuSW52b2ljZToge2ludm9pY2Vfbm99XHJcbkFtb3VudCBEdWU6IHthbW91bnRfZHVlfVxyXG5EdWUgRGF0ZToge2R1ZV9kYXRlfVxyXG5cclxuUGxlYXNlIGNvbnRhY3QgdXMgaWYgeW91IGhhdmUgYW55IHF1ZXN0aW9ucy4iLCJmb2xsb3d1cF9ub3RpZmljYXRpb25fdGltZSI6IjA5OjAwIiwiZm9sbG93dXBfbm90aWZpY2F0aW9uX2ZyZXF1ZW5jeSI6ImRhaWx5IiwiZm9sbG93dXBfbm90aWZpY2F0aW9uX3RlbXBsYXRlIjoiSW50ZXJuYWwgUmVtaW5kZXI6IEZvbGxvdyB1cCB3aXRoIHtjdXN0b21lcl9uYW1lfSByZWdhcmRpbmcgdGhlaXIgaW5xdWlyeSBmb3Ige3Byb2R1Y3RfbmFtZX0uIFBsYW5uZWQgRGF0ZToge2ZvbGxvd3VwX2RhdGV9Iiwic2NoZWR1bGVfZW5hYmxlZCI6IjAiLCJzY2hlZHVsZV90aW1lIjoiMDk6MDAiLCJzY2hlZHVsZV9kYXlzIjpbIm1vbmRheSIsInR1ZXNkYXkiLCJ3ZWRuZXNkYXkiLCJ0aHVyc2RheSIsImZyaWRheSJdfSI7czoxMzoiY3VzdG9tX2xhYmVscyI7czoyNTMxOiJ7InBheW1lbnRzIjp7ImN1c3RvbV9wYXlfMSI6Ik1QRVNBIiwiY3VzdG9tX3BheV8yIjpudWxsLCJjdXN0b21fcGF5XzMiOm51bGwsImN1c3RvbV9wYXlfNCI6bnVsbCwiY3VzdG9tX3BheV81IjpudWxsLCJjdXN0b21fcGF5XzYiOm51bGwsImN1c3RvbV9wYXlfNyI6bnVsbH0sImNvbnRhY3QiOnsiY3VzdG9tX2ZpZWxkXzEiOm51bGwsImN1c3RvbV9maWVsZF8yIjpudWxsLCJjdXN0b21fZmllbGRfMyI6bnVsbCwiY3VzdG9tX2ZpZWxkXzQiOm51bGwsImN1c3RvbV9maWVsZF81IjpudWxsLCJjdXN0b21fZmllbGRfNiI6bnVsbCwiY3VzdG9tX2ZpZWxkXzciOm51bGwsImN1c3RvbV9maWVsZF84IjpudWxsLCJjdXN0b21fZmllbGRfOSI6bnVsbCwiY3VzdG9tX2ZpZWxkXzEwIjpudWxsfSwicHJvZHVjdCI6eyJjdXN0b21fZmllbGRfMSI6bnVsbCwiY3VzdG9tX2ZpZWxkXzIiOm51bGwsImN1c3RvbV9maWVsZF8zIjpudWxsLCJjdXN0b21fZmllbGRfNCI6bnVsbCwiY3VzdG9tX2ZpZWxkXzUiOm51bGwsImN1c3RvbV9maWVsZF82IjpudWxsLCJjdXN0b21fZmllbGRfNyI6bnVsbCwiY3VzdG9tX2ZpZWxkXzgiOm51bGwsImN1c3RvbV9maWVsZF85IjpudWxsLCJjdXN0b21fZmllbGRfMTAiOm51bGwsImN1c3RvbV9maWVsZF8xMSI6bnVsbCwiY3VzdG9tX2ZpZWxkXzEyIjpudWxsLCJjdXN0b21fZmllbGRfMTMiOm51bGwsImN1c3RvbV9maWVsZF8xNCI6bnVsbCwiY3VzdG9tX2ZpZWxkXzE1IjpudWxsLCJjdXN0b21fZmllbGRfMTYiOm51bGwsImN1c3RvbV9maWVsZF8xNyI6bnVsbCwiY3VzdG9tX2ZpZWxkXzE4IjpudWxsLCJjdXN0b21fZmllbGRfMTkiOm51bGwsImN1c3RvbV9maWVsZF8yMCI6bnVsbH0sInByb2R1Y3RfY2ZfZGV0YWlscyI6eyIxIjp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCIyIjp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCIzIjp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCI0Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCI1Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCI2Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCI3Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCI4Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCI5Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCIxMCI6eyJ0eXBlIjpudWxsLCJkcm9wZG93bl9vcHRpb25zIjpudWxsfSwiMTEiOnsidHlwZSI6bnVsbCwiZHJvcGRvd25fb3B0aW9ucyI6bnVsbH0sIjEyIjp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCIxMyI6eyJ0eXBlIjpudWxsLCJkcm9wZG93bl9vcHRpb25zIjpudWxsfSwiMTQiOnsidHlwZSI6bnVsbCwiZHJvcGRvd25fb3B0aW9ucyI6bnVsbH0sIjE1Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCIxNiI6eyJ0eXBlIjpudWxsLCJkcm9wZG93bl9vcHRpb25zIjpudWxsfSwiMTciOnsidHlwZSI6bnVsbCwiZHJvcGRvd25fb3B0aW9ucyI6bnVsbH0sIjE4Ijp7InR5cGUiOm51bGwsImRyb3Bkb3duX29wdGlvbnMiOm51bGx9LCIxOSI6eyJ0eXBlIjpudWxsLCJkcm9wZG93bl9vcHRpb25zIjpudWxsfSwiMjAiOnsidHlwZSI6bnVsbCwiZHJvcGRvd25fb3B0aW9ucyI6bnVsbH19LCJsb2NhdGlvbiI6eyJjdXN0b21fZmllbGRfMSI6bnVsbCwiY3VzdG9tX2ZpZWxkXzIiOm51bGwsImN1c3RvbV9maWVsZF8zIjpudWxsLCJjdXN0b21fZmllbGRfNCI6bnVsbH0sInVzZXIiOnsiY3VzdG9tX2ZpZWxkXzEiOm51bGwsImN1c3RvbV9maWVsZF8yIjpudWxsLCJjdXN0b21fZmllbGRfMyI6bnVsbCwiY3VzdG9tX2ZpZWxkXzQiOm51bGx9LCJwdXJjaGFzZSI6eyJjdXN0b21fZmllbGRfMSI6bnVsbCwiY3VzdG9tX2ZpZWxkXzIiOm51bGwsImN1c3RvbV9maWVsZF8zIjpudWxsLCJjdXN0b21fZmllbGRfNCI6bnVsbH0sInB1cmNoYXNlX3NoaXBwaW5nIjp7ImN1c3RvbV9maWVsZF8xIjpudWxsLCJjdXN0b21fZmllbGRfMiI6bnVsbCwiY3VzdG9tX2ZpZWxkXzMiOm51bGwsImN1c3RvbV9maWVsZF80IjpudWxsLCJjdXN0b21fZmllbGRfNSI6bnVsbH0sInNlbGwiOnsiY3VzdG9tX2ZpZWxkXzEiOm51bGwsImN1c3RvbV9maWVsZF8yIjpudWxsLCJjdXN0b21fZmllbGRfMyI6bnVsbCwiY3VzdG9tX2ZpZWxkXzQiOm51bGx9LCJzaGlwcGluZyI6eyJjdXN0b21fZmllbGRfMSI6bnVsbCwiY3VzdG9tX2ZpZWxkXzIiOm51bGwsImN1c3RvbV9maWVsZF8zIjpudWxsLCJjdXN0b21fZmllbGRfNCI6bnVsbCwiY3VzdG9tX2ZpZWxkXzUiOm51bGx9LCJ0eXBlc19vZl9zZXJ2aWNlIjp7ImN1c3RvbV9maWVsZF8xIjpudWxsLCJjdXN0b21fZmllbGRfMiI6bnVsbCwiY3VzdG9tX2ZpZWxkXzMiOm51bGwsImN1c3RvbV9maWVsZF80IjpudWxsLCJjdXN0b21fZmllbGRfNSI6bnVsbCwiY3VzdG9tX2ZpZWxkXzYiOm51bGx9fSI7czoxNToiY29tbW9uX3NldHRpbmdzIjtzOjY3OiJ7ImRlZmF1bHRfY3JlZGl0X2xpbWl0IjpudWxsLCJkZWZhdWx0X2RhdGF0YWJsZV9wYWdlX2VudHJpZXMiOiIyNSJ9IjtzOjk6ImlzX2FjdGl2ZSI7aToxO3M6MTA6ImNyZWF0ZWRfYXQiO3M6MTk6IjIwMjYtMDQtMDkgMDk6NTM6MzQiO3M6MTA6InVwZGF0ZWRfYXQiO3M6MTk6IjIwMjYtMDQtMjAgMjA6MDM6NDAiO3M6MTU6ImRpZ2l0YXhfYXBpX2tleSI7TjtzOjE1OiJldGltc19zeW5jX21vZGUiO3M6MTA6ImJhY2tncm91bmQiO3M6MTA6ImV0aW1zX3RwaW4iO047czoxMzoiZXRpbXNfZW5hYmxlZCI7aTowO31zOjEwOiIAKgBjaGFuZ2VzIjthOjA6e31zOjg6IgAqAGNhc3RzIjthOjc6e3M6MTU6InJlZl9ub19wcmVmaXhlcyI7czo1OiJhcnJheSI7czoxNToiZW5hYmxlZF9tb2R1bGVzIjtzOjU6ImFycmF5IjtzOjE0OiJlbWFpbF9zZXR0aW5ncyI7czo1OiJhcnJheSI7czoxMjoic21zX3NldHRpbmdzIjtzOjU6ImFycmF5IjtzOjE3OiJ3aGF0c2FwcF9zZXR0aW5ncyI7czo1OiJhcnJheSI7czoxNToiY29tbW9uX3NldHRpbmdzIjtzOjU6ImFycmF5IjtzOjIyOiJ3ZWlnaGluZ19zY2FsZV9zZXR0aW5nIjtzOjU6ImFycmF5Ijt9czoxNzoiACoAY2xhc3NDYXN0Q2FjaGUiO2E6MDp7fXM6MjE6IgAqAGF0dHJpYnV0ZUNhc3RDYWNoZSI7YTowOnt9czo4OiIAKgBkYXRlcyI7YTowOnt9czoxMzoiACoAZGF0ZUZvcm1hdCI7TjtzOjEwOiIAKgBhcHBlbmRzIjthOjA6e31zOjE5OiIAKgBkaXNwYXRjaGVzRXZlbnRzIjthOjA6e31zOjE0OiIAKgBvYnNlcnZhYmxlcyI7YTowOnt9czoxMjoiACoAcmVsYXRpb25zIjthOjE6e3M6ODoiY3VycmVuY3kiO086MTI6IkFwcFxDdXJyZW5jeSI6MzA6e3M6MTM6IgAqAGNvbm5lY3Rpb24iO3M6NToibXlzcWwiO3M6ODoiACoAdGFibGUiO3M6MTA6ImN1cnJlbmNpZXMiO3M6MTM6IgAqAHByaW1hcnlLZXkiO3M6MjoiaWQiO3M6MTA6IgAqAGtleVR5cGUiO3M6MzoiaW50IjtzOjEyOiJpbmNyZW1lbnRpbmciO2I6MTtzOjc6IgAqAHdpdGgiO2E6MDp7fXM6MTI6IgAqAHdpdGhDb3VudCI7YTowOnt9czoxOToicHJldmVudHNMYXp5TG9hZGluZyI7YjowO3M6MTA6IgAqAHBlclBhZ2UiO2k6MTU7czo2OiJleGlzdHMiO2I6MTtzOjE4OiJ3YXNSZWNlbnRseUNyZWF0ZWQiO2I6MDtzOjI4OiIAKgBlc2NhcGVXaGVuQ2FzdGluZ1RvU3RyaW5nIjtiOjA7czoxMzoiACoAYXR0cmlidXRlcyI7YTo5OntzOjI6ImlkIjtpOjEzMztzOjc6ImNvdW50cnkiO3M6NToiS2VueWEiO3M6ODoiY3VycmVuY3kiO3M6MTU6IktlbnlhbiBzaGlsbGluZyI7czo0OiJjb2RlIjtzOjM6IktFUyI7czo2OiJzeW1ib2wiO3M6MzoiS1NoIjtzOjE4OiJ0aG91c2FuZF9zZXBhcmF0b3IiO3M6MToiLCI7czoxNzoiZGVjaW1hbF9zZXBhcmF0b3IiO3M6MToiLiI7czoxMDoiY3JlYXRlZF9hdCI7TjtzOjEwOiJ1cGRhdGVkX2F0IjtOO31zOjExOiIAKgBvcmlnaW5hbCI7YTo5OntzOjI6ImlkIjtpOjEzMztzOjc6ImNvdW50cnkiO3M6NToiS2VueWEiO3M6ODoiY3VycmVuY3kiO3M6MTU6IktlbnlhbiBzaGlsbGluZyI7czo0OiJjb2RlIjtzOjM6IktFUyI7czo2OiJzeW1ib2wiO3M6MzoiS1NoIjtzOjE4OiJ0aG91c2FuZF9zZXBhcmF0b3IiO3M6MToiLCI7czoxNzoiZGVjaW1hbF9zZXBhcmF0b3IiO3M6MToiLiI7czoxMDoiY3JlYXRlZF9hdCI7TjtzOjEwOiJ1cGRhdGVkX2F0IjtOO31zOjEwOiIAKgBjaGFuZ2VzIjthOjA6e31zOjg6IgAqAGNhc3RzIjthOjA6e31zOjE3OiIAKgBjbGFzc0Nhc3RDYWNoZSI7YTowOnt9czoyMToiACoAYXR0cmlidXRlQ2FzdENhY2hlIjthOjA6e31zOjg6IgAqAGRhdGVzIjthOjA6e31zOjEzOiIAKgBkYXRlRm9ybWF0IjtOO3M6MTA6IgAqAGFwcGVuZHMiO2E6MDp7fXM6MTk6IgAqAGRpc3BhdGNoZXNFdmVudHMiO2E6MDp7fXM6MTQ6IgAqAG9ic2VydmFibGVzIjthOjA6e31zOjEyOiIAKgByZWxhdGlvbnMiO2E6MDp7fXM6MTA6IgAqAHRvdWNoZXMiO2E6MDp7fXM6MTA6InRpbWVzdGFtcHMiO2I6MTtzOjk6IgAqAGhpZGRlbiI7YTowOnt9czoxMDoiACoAdmlzaWJsZSI7YTowOnt9czoxMToiACoAZmlsbGFibGUiO2E6MDp7fXM6MTA6IgAqAGd1YXJkZWQiO2E6MTp7aTowO3M6MToiKiI7fX19czoxMDoiACoAdG91Y2hlcyI7YTowOnt9czoxMDoidGltZXN0YW1wcyI7YjoxO3M6OToiACoAaGlkZGVuIjthOjE6e2k6MDtzOjI0OiJ3b29jb21tZXJjZV9hcGlfc2V0dGluZ3MiO31zOjEwOiIAKgB2aXNpYmxlIjthOjA6e31zOjExOiIAKgBmaWxsYWJsZSI7YTowOnt9czoxMDoiACoAZ3VhcmRlZCI7YToyOntpOjA7czoyOiJpZCI7aToxO3M6MjQ6Indvb2NvbW1lcmNlX2FwaV9zZXR0aW5ncyI7fX1zOjg6ImN1cnJlbmN5IjthOjU6e3M6MjoiaWQiO2k6MTMzO3M6NDoiY29kZSI7czozOiJLRVMiO3M6Njoic3ltYm9sIjtzOjM6IktTaCI7czoxODoidGhvdXNhbmRfc2VwYXJhdG9yIjtzOjE6IiwiO3M6MTc6ImRlY2ltYWxfc2VwYXJhdG9yIjtzOjE6Ii4iO31zOjE0OiJmaW5hbmNpYWxfeWVhciI7YToyOntzOjU6InN0YXJ0IjtzOjEwOiIyMDI2LTAxLTAxIjtzOjM6ImVuZCI7czoxMDoiMjAyNi0xMi0zMSI7fX0=', 1776747232);

-- --------------------------------------------------------

--
-- Table structure for table `sms_logs`
--

CREATE TABLE `sms_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `sent_by` int(10) UNSIGNED DEFAULT NULL,
  `recipient_type` varchar(191) NOT NULL DEFAULT 'manual',
  `total_sent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_failed` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stocktake_lines`
--

CREATE TABLE `stocktake_lines` (
  `id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variation_id` int(10) UNSIGNED NOT NULL,
  `system_qty` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `counted_qty` decimal(22,4) DEFAULT NULL,
  `variance` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `lot_number` varchar(191) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `counted_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustments_temp`
--

CREATE TABLE `stock_adjustments_temp` (
  `id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustment_lines`
--

CREATE TABLE `stock_adjustment_lines` (
  `id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variation_id` int(10) UNSIGNED NOT NULL,
  `quantity` decimal(22,4) NOT NULL,
  `secondary_unit_quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `unit_price` decimal(22,4) DEFAULT NULL COMMENT 'Last purchase unit price',
  `removed_purchase_line` int(11) DEFAULT NULL,
  `lot_no_line_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sync_conflicts`
--

CREATE TABLE `sync_conflicts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `model` varchar(191) NOT NULL,
  `record_id` bigint(20) UNSIGNED DEFAULT NULL,
  `server_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`server_data`)),
  `client_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`client_data`)),
  `resolution` enum('pending','server_wins','client_wins','merged') NOT NULL DEFAULT 'pending',
  `resolved_by` int(10) UNSIGNED DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sync_logs`
--

CREATE TABLE `sync_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `sync_token_id` int(10) UNSIGNED DEFAULT NULL,
  `direction` enum('pull','push') NOT NULL,
  `status` enum('success','partial','failed') NOT NULL DEFAULT 'success',
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`summary`)),
  `errors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`errors`)),
  `records_sent` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `records_received` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `conflicts` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `synced_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sync_tokens`
--

CREATE TABLE `sync_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `token` varchar(64) NOT NULL,
  `device_name` varchar(191) DEFAULT NULL,
  `device_type` varchar(191) NOT NULL DEFAULT 'browser',
  `last_pulled_at` timestamp NULL DEFAULT NULL,
  `last_pushed_at` timestamp NULL DEFAULT NULL,
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system`
--

CREATE TABLE `system` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(191) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system`
--

INSERT INTO `system` (`id`, `key`, `value`) VALUES
(1, 'db_version', '6.11'),
(2, 'default_business_active_status', '1'),
(3, 'cloud_sync_url', 'https://www.reenson.apextechsolutions.co.ke'),
(4, 'cloud_sync_token', '75a49e822a533a3c8bc977034475b5ed170b33e4');

-- --------------------------------------------------------

--
-- Table structure for table `tax_rates`
--

CREATE TABLE `tax_rates` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `amount` double(22,4) NOT NULL,
  `is_tax_group` tinyint(1) NOT NULL DEFAULT 0,
  `for_tax_group` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED DEFAULT NULL,
  `is_kitchen_order` tinyint(1) NOT NULL DEFAULT 0,
  `res_table_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'fields to restaurant module',
  `res_waiter_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'fields to restaurant module',
  `res_order_status` enum('received','cooked','served') DEFAULT NULL,
  `type` varchar(191) DEFAULT NULL,
  `sub_type` varchar(20) DEFAULT NULL,
  `status` varchar(191) NOT NULL,
  `is_synced` tinyint(1) NOT NULL DEFAULT 0,
  `offline_ref` varchar(80) DEFAULT NULL COMMENT 'Client-generated idempotency ID from offline device',
  `sub_status` varchar(191) DEFAULT NULL,
  `is_quotation` tinyint(1) NOT NULL DEFAULT 0,
  `payment_status` enum('paid','due','partial') DEFAULT NULL,
  `adjustment_type` enum('normal','abnormal') DEFAULT NULL,
  `contact_id` int(11) UNSIGNED DEFAULT NULL,
  `customer_group_id` int(11) DEFAULT NULL COMMENT 'used to add customer group while selling',
  `invoice_no` varchar(191) DEFAULT NULL,
  `ref_no` varchar(191) DEFAULT NULL,
  `source` varchar(191) DEFAULT NULL,
  `subscription_no` varchar(191) DEFAULT NULL,
  `subscription_repeat_on` varchar(191) DEFAULT NULL,
  `transaction_date` datetime NOT NULL,
  `total_before_tax` decimal(22,4) NOT NULL DEFAULT 0.0000 COMMENT 'Total before the purchase/invoice tax, this includeds the indivisual product tax',
  `tax_id` int(10) UNSIGNED DEFAULT NULL,
  `tax_amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `discount_type` enum('fixed','percentage') DEFAULT NULL,
  `discount_amount` decimal(22,4) DEFAULT 0.0000,
  `rp_redeemed` int(11) NOT NULL DEFAULT 0 COMMENT 'rp is the short form of reward points',
  `rp_redeemed_amount` decimal(22,4) NOT NULL DEFAULT 0.0000 COMMENT 'rp is the short form of reward points',
  `shipping_details` varchar(191) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `delivery_date` datetime DEFAULT NULL,
  `shipping_status` varchar(191) DEFAULT NULL,
  `delivered_to` varchar(191) DEFAULT NULL,
  `delivery_person` bigint(20) DEFAULT NULL,
  `shipping_charges` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `shipping_custom_field_1` varchar(191) DEFAULT NULL,
  `shipping_custom_field_2` varchar(191) DEFAULT NULL,
  `shipping_custom_field_3` varchar(191) DEFAULT NULL,
  `shipping_custom_field_4` varchar(191) DEFAULT NULL,
  `shipping_custom_field_5` varchar(191) DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `staff_note` text DEFAULT NULL,
  `is_export` tinyint(1) NOT NULL DEFAULT 0,
  `export_custom_fields_info` longtext DEFAULT NULL,
  `round_off_amount` decimal(22,4) NOT NULL DEFAULT 0.0000 COMMENT 'Difference of rounded total and actual total',
  `additional_expense_key_1` varchar(191) DEFAULT NULL,
  `additional_expense_value_1` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `additional_expense_key_2` varchar(191) DEFAULT NULL,
  `additional_expense_value_2` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `additional_expense_key_3` varchar(191) DEFAULT NULL,
  `additional_expense_value_3` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `additional_expense_key_4` varchar(191) DEFAULT NULL,
  `additional_expense_value_4` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `final_total` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `etims_invoice_number` varchar(191) DEFAULT NULL,
  `etims_qr_url` text DEFAULT NULL,
  `etims_signature` text DEFAULT NULL,
  `etims_sync_status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `etims_sync_error` text DEFAULT NULL,
  `etims_synced_at` timestamp NULL DEFAULT NULL,
  `expense_category_id` int(10) UNSIGNED DEFAULT NULL,
  `expense_sub_category_id` int(11) DEFAULT NULL,
  `expense_for` int(10) UNSIGNED DEFAULT NULL,
  `commission_agent` int(11) DEFAULT NULL,
  `document` varchar(191) DEFAULT NULL,
  `is_direct_sale` tinyint(1) NOT NULL DEFAULT 0,
  `is_suspend` tinyint(1) NOT NULL DEFAULT 0,
  `exchange_rate` decimal(20,3) NOT NULL DEFAULT 1.000,
  `total_amount_recovered` decimal(22,4) DEFAULT NULL COMMENT 'Used for stock adjustment.',
  `transfer_parent_id` int(11) DEFAULT NULL,
  `return_parent_id` int(11) DEFAULT NULL,
  `opening_stock_product_id` int(11) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `purchase_requisition_ids` text DEFAULT NULL,
  `prefer_payment_method` varchar(191) DEFAULT NULL,
  `prefer_payment_account` int(11) DEFAULT NULL,
  `sales_order_ids` text DEFAULT NULL,
  `purchase_order_ids` text DEFAULT NULL,
  `custom_field_1` varchar(191) DEFAULT NULL,
  `custom_field_2` varchar(191) DEFAULT NULL,
  `custom_field_3` varchar(191) DEFAULT NULL,
  `custom_field_4` varchar(191) DEFAULT NULL,
  `import_batch` int(11) DEFAULT NULL,
  `import_time` datetime DEFAULT NULL,
  `types_of_service_id` int(11) DEFAULT NULL,
  `packing_charge` decimal(22,4) DEFAULT NULL,
  `packing_charge_type` enum('fixed','percent') DEFAULT NULL,
  `service_custom_field_1` text DEFAULT NULL,
  `service_custom_field_2` text DEFAULT NULL,
  `service_custom_field_3` text DEFAULT NULL,
  `service_custom_field_4` text DEFAULT NULL,
  `service_custom_field_5` text DEFAULT NULL,
  `service_custom_field_6` text DEFAULT NULL,
  `is_created_from_api` tinyint(1) NOT NULL DEFAULT 0,
  `rp_earned` int(11) NOT NULL DEFAULT 0 COMMENT 'rp is the short form of reward points',
  `order_addresses` text DEFAULT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `recur_interval` double(22,4) DEFAULT NULL,
  `recur_interval_type` enum('days','months','years') DEFAULT NULL,
  `recur_repetitions` int(11) DEFAULT NULL,
  `recur_stopped_on` datetime DEFAULT NULL,
  `recur_parent_id` int(11) DEFAULT NULL,
  `invoice_token` varchar(191) DEFAULT NULL,
  `pay_term_number` int(11) DEFAULT NULL,
  `pay_term_type` enum('days','months') DEFAULT NULL,
  `selling_price_group_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `business_id`, `location_id`, `is_kitchen_order`, `res_table_id`, `res_waiter_id`, `res_order_status`, `type`, `sub_type`, `status`, `is_synced`, `offline_ref`, `sub_status`, `is_quotation`, `payment_status`, `adjustment_type`, `contact_id`, `customer_group_id`, `invoice_no`, `ref_no`, `source`, `subscription_no`, `subscription_repeat_on`, `transaction_date`, `total_before_tax`, `tax_id`, `tax_amount`, `discount_type`, `discount_amount`, `rp_redeemed`, `rp_redeemed_amount`, `shipping_details`, `shipping_address`, `delivery_date`, `shipping_status`, `delivered_to`, `delivery_person`, `shipping_charges`, `shipping_custom_field_1`, `shipping_custom_field_2`, `shipping_custom_field_3`, `shipping_custom_field_4`, `shipping_custom_field_5`, `additional_notes`, `staff_note`, `is_export`, `export_custom_fields_info`, `round_off_amount`, `additional_expense_key_1`, `additional_expense_value_1`, `additional_expense_key_2`, `additional_expense_value_2`, `additional_expense_key_3`, `additional_expense_value_3`, `additional_expense_key_4`, `additional_expense_value_4`, `final_total`, `etims_invoice_number`, `etims_qr_url`, `etims_signature`, `etims_sync_status`, `etims_sync_error`, `etims_synced_at`, `expense_category_id`, `expense_sub_category_id`, `expense_for`, `commission_agent`, `document`, `is_direct_sale`, `is_suspend`, `exchange_rate`, `total_amount_recovered`, `transfer_parent_id`, `return_parent_id`, `opening_stock_product_id`, `created_by`, `purchase_requisition_ids`, `prefer_payment_method`, `prefer_payment_account`, `sales_order_ids`, `purchase_order_ids`, `custom_field_1`, `custom_field_2`, `custom_field_3`, `custom_field_4`, `import_batch`, `import_time`, `types_of_service_id`, `packing_charge`, `packing_charge_type`, `service_custom_field_1`, `service_custom_field_2`, `service_custom_field_3`, `service_custom_field_4`, `service_custom_field_5`, `service_custom_field_6`, `is_created_from_api`, `rp_earned`, `order_addresses`, `is_recurring`, `recur_interval`, `recur_interval_type`, `recur_repetitions`, `recur_stopped_on`, `recur_parent_id`, `invoice_token`, `pay_term_number`, `pay_term_type`, `selling_price_group_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 1, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0003', NULL, NULL, NULL, NULL, '2026-04-09 15:06:13', 400.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 400.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 12:06:13', '2026-04-09 12:06:13'),
(2, 1, 1, 0, NULL, NULL, NULL, 'opening_stock', NULL, 'received', 1, NULL, NULL, 0, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-01 13:52:17', 141.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 1410.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, 102, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 10:52:17', '2026-04-09 10:52:17'),
(3, 1, 1, 0, NULL, NULL, NULL, 'opening_stock', NULL, 'received', 1, NULL, NULL, 0, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-01 13:52:26', 265.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 1855.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, 103, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 10:52:26', '2026-04-09 10:52:26'),
(4, 1, 1, 0, NULL, NULL, NULL, 'opening_stock', NULL, 'received', 1, NULL, NULL, 0, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-01 13:52:32', 61.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 610.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, 101, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 10:52:32', '2026-04-09 10:52:32'),
(5, 1, 1, 0, NULL, NULL, NULL, 'opening_stock', NULL, 'received', 1, NULL, NULL, 0, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-01 13:52:39', 457.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 6855.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, 104, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 10:52:39', '2026-04-09 10:52:39'),
(6, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 1, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0001', '', NULL, NULL, NULL, '2026-04-09 13:53:04', 250.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 250.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '1958a2d8f774c5d915ea613b2852714e', NULL, NULL, NULL, '2026-04-09 10:53:04', '2026-04-09 13:15:49'),
(7, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 1, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0002', '', NULL, NULL, NULL, '2026-04-09 14:26:21', 250.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 250.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, 'aa9597dcbcf9e37592923d5e7585876a', NULL, NULL, NULL, '2026-04-09 11:26:21', '2026-04-09 12:30:29'),
(8, 1, 1, 0, NULL, NULL, NULL, 'opening_stock', NULL, 'received', 1, NULL, NULL, 0, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-01 14:34:23', 217.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 1085.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, 19, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 11:34:23', '2026-04-09 11:34:23'),
(9, 1, 1, 0, NULL, NULL, NULL, 'purchase', NULL, 'received', 1, NULL, NULL, 0, 'paid', NULL, 2, NULL, NULL, '3151', NULL, NULL, NULL, '2026-04-07 14:34:00', 1040.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 1040.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 11:39:10', '2026-04-09 11:39:10'),
(10, 1, 1, 0, NULL, NULL, NULL, 'opening_stock', NULL, 'received', 1, NULL, NULL, 0, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-01 14:44:01', 228.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 1140.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, 134, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 11:44:01', '2026-04-09 11:44:01'),
(11, 1, 1, 0, NULL, NULL, NULL, 'purchase', NULL, 'received', 1, NULL, NULL, 0, 'paid', NULL, 2, NULL, NULL, '3151B', NULL, NULL, NULL, '2026-04-09 14:46:00', 400.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 400.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 11:47:26', '2026-04-09 11:47:26'),
(12, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 1, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0003', '', NULL, NULL, NULL, '2026-04-09 15:06:13', 400.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 400.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 12:06:13', '2026-04-09 12:06:13'),
(13, 1, 1, 0, NULL, NULL, NULL, 'stocktake', NULL, 'draft', 1, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 'ST-20260409-6432', NULL, NULL, NULL, '2026-04-09 15:34:06', 0.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 0.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 12:34:06', '2026-04-09 12:34:06'),
(14, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0004', '', NULL, NULL, NULL, '2026-04-09 19:30:46', 300.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 300.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '8cf4269b8bb7c88475d7c1b0f22596d3', NULL, NULL, NULL, '2026-04-09 16:30:46', '2026-04-09 16:30:46'),
(15, 1, 1, 0, NULL, NULL, NULL, 'purchase', NULL, 'received', 0, NULL, NULL, 0, 'paid', NULL, 2, NULL, NULL, '31516', NULL, NULL, NULL, '2026-04-09 19:33:00', 1356.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 1356.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 16:37:04', '2026-04-09 16:37:04'),
(16, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0005', '', NULL, NULL, NULL, '2026-04-09 21:30:36', 400.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 400.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '67eb86b23ce268d8e7a27d6200d260df', NULL, NULL, NULL, '2026-04-09 18:30:36', '2026-04-09 18:30:36'),
(17, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0006', '', NULL, NULL, NULL, '2026-04-09 22:05:41', 400.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 400.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '508ab5bcf2487aa2e9d379265197daba', NULL, NULL, NULL, '2026-04-09 19:05:41', '2026-04-09 19:05:41'),
(20, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0008', '', NULL, NULL, NULL, '2026-04-13 09:40:39', 250.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 250.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, 'dc089e0354d922c0ba1a5df8bc79ed9f', NULL, NULL, NULL, '2026-04-13 09:40:39', '2026-04-13 09:40:39'),
(21, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0009', '', NULL, NULL, NULL, '2026-04-13 13:23:46', 250.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 250.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '578db7be652716c5256e595c0be676d9', NULL, NULL, NULL, '2026-04-13 13:23:46', '2026-04-13 13:23:46'),
(22, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0010', '', NULL, NULL, NULL, '2026-04-13 13:25:09', 500.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 500.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, 'cf683b3a62c0ec5ea3b0eaaf11df7174', NULL, NULL, NULL, '2026-04-13 13:25:09', '2026-04-13 13:25:09'),
(23, 1, 2, 0, NULL, NULL, NULL, 'purchase', NULL, 'received', 0, NULL, NULL, 0, 'paid', NULL, 2, NULL, NULL, '1234', NULL, NULL, NULL, '2026-04-13 19:00:00', 61.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 61.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-04-13 19:01:53', '2026-04-13 19:02:59'),
(24, 1, 2, 0, NULL, NULL, NULL, 'purchase', NULL, 'received', 0, NULL, NULL, 0, 'paid', NULL, 2, NULL, NULL, '2345', NULL, NULL, NULL, '2026-04-13 19:03:00', 1650.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 1650.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'days', NULL, '2026-04-13 19:05:14', '2026-04-13 19:05:14'),
(25, 1, 2, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0011', '', NULL, NULL, NULL, '2026-04-13 19:05:36', 250.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 250.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '22d937d731b0bc2f8189196bd1ae9cdb', NULL, NULL, NULL, '2026-04-13 19:05:36', '2026-04-13 19:05:36'),
(26, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0012', '', NULL, NULL, NULL, '2026-04-13 19:37:04', 300.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 300.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, 'a1e74315c58f882f5b1816c608333b51', NULL, NULL, NULL, '2026-04-13 19:37:04', '2026-04-13 19:37:04'),
(27, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0013', '', NULL, NULL, NULL, '2026-04-13 19:39:30', 300.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 300.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, 'e91629f690989eb0431c05fa22910b81', NULL, NULL, NULL, '2026-04-13 19:39:30', '2026-04-13 19:39:30'),
(28, 1, 2, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0014', '', NULL, NULL, NULL, '2026-04-13 19:45:19', 250.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 250.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, 'b1d2e04a71edcc4185643fb41ace9d8c', NULL, NULL, NULL, '2026-04-13 19:45:19', '2026-04-13 19:45:19'),
(29, 1, 2, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0015', '', NULL, NULL, NULL, '2026-04-13 19:48:55', 250.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 250.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '94206b3c7f63ef5919e8a48287154bdd', NULL, NULL, NULL, '2026-04-13 19:48:55', '2026-04-13 19:48:55'),
(30, 1, 2, 0, NULL, NULL, NULL, 'purchase', NULL, 'received', 0, NULL, NULL, 0, 'paid', NULL, 2, NULL, NULL, '456', NULL, NULL, NULL, '2026-04-13 20:11:00', 510.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 510.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'days', NULL, '2026-04-13 20:14:32', '2026-04-13 20:14:32'),
(31, 1, 2, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0016', '', NULL, NULL, NULL, '2026-04-13 20:29:28', 150.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 150.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, 'b4b192bf118f9e8556e9c417d835e22a', NULL, NULL, NULL, '2026-04-13 20:29:28', '2026-04-13 20:29:28'),
(32, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0017', '', NULL, NULL, NULL, '2026-04-13 20:48:20', 200.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 200.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '6a43461912a16e4336ea614852cdbe04', NULL, NULL, NULL, '2026-04-13 20:48:20', '2026-04-13 20:48:20'),
(33, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0018', '', NULL, NULL, NULL, '2026-04-14 18:08:21', 300.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 300.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '4a250500ebe7cfd3b86161e7743fbbf6', NULL, NULL, NULL, '2026-04-14 18:08:21', '2026-04-14 18:08:21'),
(34, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0019', '', NULL, NULL, NULL, '2026-04-19 18:35:01', 200.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 200.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '8d02ec6a979f4036c708c139cfe20ea8', NULL, NULL, NULL, '2026-04-19 18:35:01', '2026-04-19 18:35:02'),
(35, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0020', '', NULL, NULL, NULL, '2026-04-19 18:38:15', 400.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 400.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, 'c8a2c7d7370ad449713d15f329281df3', NULL, NULL, NULL, '2026-04-19 18:38:15', '2026-04-19 18:38:15'),
(36, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0021', '', NULL, NULL, NULL, '2026-04-19 18:40:25', 200.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 200.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '16e5e666e4b2e503c410af330fef695b', NULL, NULL, NULL, '2026-04-19 18:40:25', '2026-04-19 18:40:25'),
(37, 1, 1, 0, NULL, NULL, NULL, 'purchase', NULL, 'received', 0, NULL, NULL, 0, 'due', NULL, 2, NULL, NULL, '11222', NULL, NULL, NULL, '2026-04-19 18:54:00', 395.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 395.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-19 18:55:57', '2026-04-19 18:55:57'),
(38, 1, 1, 0, NULL, NULL, NULL, 'purchase', NULL, 'received', 0, NULL, NULL, 0, 'due', NULL, 2, NULL, NULL, 'PO2026/0008', NULL, NULL, NULL, '2026-04-19 18:56:00', 248.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 248.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'days', NULL, '2026-04-19 18:58:45', '2026-04-19 18:58:45'),
(39, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0022', '', NULL, NULL, NULL, '2026-04-19 19:03:17', 200.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 200.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '5a4c3e0456ef215a6270bdce63dce124', NULL, NULL, NULL, '2026-04-19 19:03:17', '2026-04-19 19:03:17'),
(40, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0023', '', NULL, NULL, NULL, '2026-04-19 20:18:46', 200.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 200.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, 'c512d37134eee76fb1c1d8f436ccd165', NULL, NULL, NULL, '2026-04-19 20:18:46', '2026-04-19 20:18:46'),
(41, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0024', '', NULL, NULL, NULL, '2026-04-19 20:24:40', 300.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 300.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, 'ac917f2b2979fa87d4d4c8a3ef644018', NULL, NULL, NULL, '2026-04-19 20:24:40', '2026-04-19 20:24:40'),
(42, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0025', '', NULL, NULL, NULL, '2026-04-19 20:26:57', 400.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 400.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, 'b6346e6c72b53db9e9a7fc732818fa12', NULL, NULL, NULL, '2026-04-19 20:26:57', '2026-04-19 20:26:57'),
(43, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0026', '', NULL, NULL, NULL, '2026-04-19 20:34:01', 500.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 500.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '826e3a9f355c34d72d309604652da59d', NULL, NULL, NULL, '2026-04-19 20:34:01', '2026-04-19 20:34:01'),
(44, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0027', '', NULL, NULL, NULL, '2026-04-19 20:34:44', 100.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 100.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '104386611cc9ab35af4254be30855186', NULL, NULL, NULL, '2026-04-19 20:34:44', '2026-04-19 20:34:44'),
(45, 1, 1, 0, NULL, NULL, NULL, 'purchase', NULL, 'received', 0, NULL, NULL, 0, 'paid', NULL, 2, NULL, NULL, '5548', NULL, NULL, NULL, '2026-04-19 20:39:00', 60054.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 60054.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'days', NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(46, 1, 1, 0, NULL, NULL, NULL, 'purchase', NULL, 'received', 0, NULL, NULL, 0, 'due', NULL, 2, NULL, NULL, '5567', NULL, NULL, NULL, '2026-04-19 21:30:00', 3510.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 3510.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-19 21:41:00', '2026-04-20 20:04:46'),
(47, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0028', '', NULL, NULL, NULL, '2026-04-20 15:52:45', 750.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 750.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '0aea80eb5791b0adc4c47cc1fac55e24', NULL, NULL, NULL, '2026-04-20 15:52:45', '2026-04-20 15:52:45'),
(48, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0029', '', NULL, NULL, NULL, '2026-04-20 15:54:12', 150.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 150.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, 'c8466d88d5098f795266faa92fc0f0e5', NULL, NULL, NULL, '2026-04-20 15:54:12', '2026-04-20 15:54:12'),
(49, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0029', '', NULL, NULL, NULL, '2026-04-20 15:54:12', 150.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 150.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '647afa6217bc2d0f6010254c64d06ba8', NULL, NULL, NULL, '2026-04-20 15:54:12', '2026-04-20 15:54:12'),
(50, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0030', '', NULL, NULL, NULL, '2026-04-20 15:57:56', 2050.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 2050.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, 'ab056d4ae43114dffc2eafc0fa173611', NULL, NULL, NULL, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(51, 1, 1, 0, NULL, NULL, NULL, 'purchase', NULL, 'received', 0, NULL, NULL, 0, 'partial', NULL, 2, NULL, NULL, '5574', NULL, NULL, NULL, '2026-04-20 19:54:00', 7577.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 7577.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'days', NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(52, 1, 1, 0, NULL, NULL, NULL, 'sell', NULL, 'final', 0, NULL, NULL, 0, 'paid', NULL, 1, NULL, '0031', '', NULL, NULL, NULL, '2026-04-20 20:08:25', 840.0000, NULL, 0.0000, NULL, 0.0000, 0, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, NULL, 0.0000, 840.0000, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1.000, NULL, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.0000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 1.0000, 'days', 0, NULL, NULL, '993c0b6ab63ef9aef9757d61ebd3c73a', NULL, NULL, NULL, '2026-04-20 20:08:25', '2026-04-20 20:08:25');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_payments`
--

CREATE TABLE `transaction_payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(11) UNSIGNED DEFAULT NULL,
  `business_id` int(11) DEFAULT NULL,
  `is_return` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Used during sales to return the change',
  `amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `is_synced` tinyint(1) NOT NULL DEFAULT 0,
  `method` varchar(191) DEFAULT NULL,
  `payment_type` varchar(191) DEFAULT NULL,
  `transaction_no` varchar(191) DEFAULT NULL,
  `card_transaction_number` varchar(191) DEFAULT NULL,
  `card_number` varchar(191) DEFAULT NULL,
  `card_type` varchar(191) DEFAULT NULL,
  `card_holder_name` varchar(191) DEFAULT NULL,
  `card_month` varchar(191) DEFAULT NULL,
  `card_year` varchar(191) DEFAULT NULL,
  `card_security` varchar(5) DEFAULT NULL,
  `cheque_number` varchar(191) DEFAULT NULL,
  `bank_account_number` varchar(191) DEFAULT NULL,
  `paid_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `paid_through_link` tinyint(1) NOT NULL DEFAULT 0,
  `gateway` varchar(191) DEFAULT NULL,
  `is_advance` tinyint(1) NOT NULL DEFAULT 0,
  `payment_for` int(11) DEFAULT NULL COMMENT 'stores the contact id',
  `parent_id` int(11) DEFAULT NULL,
  `note` varchar(191) DEFAULT NULL,
  `document` varchar(191) DEFAULT NULL,
  `payment_ref_no` varchar(191) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_payments`
--

INSERT INTO `transaction_payments` (`id`, `transaction_id`, `business_id`, `is_return`, `amount`, `is_synced`, `method`, `payment_type`, `transaction_no`, `card_transaction_number`, `card_number`, `card_type`, `card_holder_name`, `card_month`, `card_year`, `card_security`, `cheque_number`, `bank_account_number`, `paid_on`, `created_by`, `paid_through_link`, `gateway`, `is_advance`, `payment_for`, `parent_id`, `note`, `document`, `payment_ref_no`, `account_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, 250.0000, 1, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 13:53:04', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0001', NULL, '2026-04-09 10:53:04', '2026-04-09 10:53:04'),
(2, 1, 1, 0, 250.0000, 1, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 14:26:21', 3, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0002', NULL, '2026-04-09 11:26:21', '2026-04-09 11:26:21'),
(3, 9, 1, 0, 1040.0000, 1, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 14:34:00', 2, 0, NULL, 0, 2, NULL, NULL, NULL, 'PP2026/0001', NULL, '2026-04-09 11:39:10', '2026-04-09 11:39:10'),
(4, 11, 1, 0, 400.0000, 1, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 14:46:00', 2, 0, NULL, 0, 2, NULL, NULL, NULL, 'PP2026/0002', NULL, '2026-04-09 11:47:26', '2026-04-09 11:47:26'),
(5, 1, 1, 0, 400.0000, 1, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 15:06:13', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0003', NULL, '2026-04-09 12:06:13', '2026-04-09 12:06:13'),
(6, 14, 1, 0, 300.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 19:30:46', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0004', NULL, '2026-04-09 16:30:46', '2026-04-09 16:30:46'),
(7, 15, 1, 0, 1356.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 19:33:00', 2, 0, NULL, 0, 2, NULL, NULL, NULL, 'PP2026/0003', NULL, '2026-04-09 16:37:04', '2026-04-09 16:37:04'),
(8, 16, 1, 0, 400.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 21:30:36', 3, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0005', NULL, '2026-04-09 18:30:36', '2026-04-09 18:30:36'),
(9, 17, 1, 0, 400.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 22:05:41', 3, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0006', NULL, '2026-04-09 19:05:41', '2026-04-09 19:05:41'),
(12, 20, 1, 0, 250.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 09:40:39', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0008', NULL, '2026-04-13 09:40:39', '2026-04-13 09:40:39'),
(13, 21, 1, 0, 250.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 13:23:46', 3, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0009', NULL, '2026-04-13 13:23:46', '2026-04-13 13:23:46'),
(14, 22, 1, 0, 500.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 13:25:09', 3, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0010', NULL, '2026-04-13 13:25:09', '2026-04-13 13:25:09'),
(15, 23, 1, 0, 61.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 19:02:00', 2, 0, NULL, 0, 2, NULL, NULL, NULL, 'PP2026/0004', NULL, '2026-04-13 19:02:59', '2026-04-13 19:02:59'),
(16, 24, 1, 0, 1650.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 19:03:00', 2, 0, NULL, 0, 2, NULL, NULL, NULL, 'PP2026/0005', NULL, '2026-04-13 19:05:14', '2026-04-13 19:05:14'),
(17, 25, 1, 0, 250.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 19:05:36', 4, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0011', NULL, '2026-04-13 19:05:36', '2026-04-13 19:05:36'),
(18, 26, 1, 0, 300.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 19:37:04', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0012', NULL, '2026-04-13 19:37:04', '2026-04-13 19:37:04'),
(19, 27, 1, 0, 300.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 19:39:30', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0013', NULL, '2026-04-13 19:39:30', '2026-04-13 19:39:30'),
(20, 28, 1, 0, 250.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 19:45:19', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0014', NULL, '2026-04-13 19:45:19', '2026-04-13 19:45:19'),
(21, 29, 1, 0, 250.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 19:48:55', 4, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0015', NULL, '2026-04-13 19:48:55', '2026-04-13 19:48:55'),
(22, 30, 1, 0, 510.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 20:11:00', 2, 0, NULL, 0, 2, NULL, NULL, NULL, 'PP2026/0006', NULL, '2026-04-13 20:14:32', '2026-04-13 20:14:32'),
(23, 31, 1, 0, 150.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 20:29:28', 4, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0016', NULL, '2026-04-13 20:29:28', '2026-04-13 20:29:28'),
(24, 32, 1, 0, 200.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-13 20:48:20', 3, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0017', NULL, '2026-04-13 20:48:20', '2026-04-13 20:48:20'),
(25, 33, 1, 0, 300.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-14 18:08:21', 3, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0018', NULL, '2026-04-14 18:08:21', '2026-04-14 18:08:21'),
(26, 34, 1, 0, 200.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-19 18:35:01', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0019', NULL, '2026-04-19 18:35:01', '2026-04-19 18:35:01'),
(27, 35, 1, 0, 400.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-19 18:38:15', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0020', NULL, '2026-04-19 18:38:15', '2026-04-19 18:38:15'),
(28, 36, 1, 0, 200.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-19 18:40:25', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0021', NULL, '2026-04-19 18:40:25', '2026-04-19 18:40:25'),
(29, 39, 1, 0, 200.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-19 19:03:17', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0022', NULL, '2026-04-19 19:03:17', '2026-04-19 19:03:17'),
(30, 40, 1, 0, 200.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-19 20:18:46', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0023', NULL, '2026-04-19 20:18:46', '2026-04-19 20:18:46'),
(31, 41, 1, 0, 300.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-19 20:24:40', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0024', NULL, '2026-04-19 20:24:40', '2026-04-19 20:24:40'),
(32, 42, 1, 0, 400.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-19 20:26:57', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0025', NULL, '2026-04-19 20:26:57', '2026-04-19 20:26:57'),
(33, 43, 1, 0, 500.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-19 20:34:01', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0026', NULL, '2026-04-19 20:34:01', '2026-04-19 20:34:01'),
(34, 44, 1, 0, 100.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-19 20:34:44', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0027', NULL, '2026-04-19 20:34:44', '2026-04-19 20:34:44'),
(35, 45, 1, 0, 60054.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-19 20:39:00', 2, 0, NULL, 0, 2, NULL, NULL, NULL, 'PP2026/0007', NULL, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(36, 47, 1, 0, 750.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 15:52:45', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0028', NULL, '2026-04-20 15:52:45', '2026-04-20 15:52:45'),
(37, 48, 1, 0, 150.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 15:54:12', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0029', NULL, '2026-04-20 15:54:12', '2026-04-20 15:54:12'),
(38, 49, 1, 0, 150.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 15:54:12', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0030', NULL, '2026-04-20 15:54:12', '2026-04-20 15:54:12'),
(39, 50, 1, 0, 2050.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 15:57:56', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0031', NULL, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(40, 51, 1, 0, 0.0076, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 19:54:00', 2, 0, NULL, 0, 2, NULL, NULL, NULL, 'PP2026/0008', NULL, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(41, 52, 1, 0, 840.0000, 0, 'cash', NULL, NULL, NULL, NULL, 'credit', NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-20 20:08:25', 2, 0, NULL, 0, 1, NULL, NULL, NULL, 'SP2026/0032', NULL, '2026-04-20 20:08:25', '2026-04-20 20:08:25');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_sell_lines`
--

CREATE TABLE `transaction_sell_lines` (
  `id` int(10) UNSIGNED NOT NULL,
  `transaction_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variation_id` int(10) UNSIGNED NOT NULL,
  `quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `secondary_unit_quantity` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `quantity_returned` decimal(20,4) NOT NULL DEFAULT 0.0000,
  `unit_price_before_discount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `unit_price` decimal(22,4) DEFAULT NULL COMMENT 'Sell price excluding tax',
  `line_discount_type` enum('fixed','percentage') DEFAULT NULL,
  `line_discount_amount` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `unit_price_inc_tax` decimal(22,4) DEFAULT NULL COMMENT 'Sell price including tax',
  `item_tax` decimal(22,4) NOT NULL COMMENT 'Tax for one quantity',
  `tax_id` int(10) UNSIGNED DEFAULT NULL,
  `discount_id` int(11) DEFAULT NULL,
  `lot_no_line_id` int(11) DEFAULT NULL,
  `sell_line_note` text DEFAULT NULL,
  `so_line_id` int(11) DEFAULT NULL,
  `so_quantity_invoiced` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `res_service_staff_id` int(11) DEFAULT NULL,
  `res_line_order_status` varchar(191) DEFAULT NULL,
  `parent_sell_line_id` int(11) DEFAULT NULL,
  `children_type` varchar(191) NOT NULL DEFAULT '' COMMENT 'Type of children for the parent, like modifier or combo',
  `sub_unit_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_sell_lines`
--

INSERT INTO `transaction_sell_lines` (`id`, `transaction_id`, `product_id`, `variation_id`, `quantity`, `secondary_unit_quantity`, `quantity_returned`, `unit_price_before_discount`, `unit_price`, `line_discount_type`, `line_discount_amount`, `unit_price_inc_tax`, `item_tax`, `tax_id`, `discount_id`, `lot_no_line_id`, `sell_line_note`, `so_line_id`, `so_quantity_invoiced`, `res_service_staff_id`, `res_line_order_status`, `parent_sell_line_id`, `children_type`, `sub_unit_id`, `created_at`, `updated_at`) VALUES
(1, 6, 102, 102, 1.0000, 0.0000, 0.0000, 250.0000, 250.0000, 'fixed', 0.0000, 250.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-09 10:53:04', '2026-04-09 10:53:04'),
(2, 7, 102, 102, 1.0000, 0.0000, 0.0000, 250.0000, 250.0000, 'fixed', 0.0000, 250.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-09 11:26:21', '2026-04-09 11:26:21'),
(3, 12, 134, 134, 1.0000, 0.0000, 0.0000, 400.0000, 400.0000, 'fixed', 0.0000, 400.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-09 12:06:13', '2026-04-09 12:06:13'),
(4, 14, 19, 19, 1.0000, 0.0000, 0.0000, 300.0000, 300.0000, 'fixed', 0.0000, 300.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-09 16:30:46', '2026-04-09 16:30:46'),
(5, 16, 14, 14, 1.0000, 0.0000, 0.0000, 400.0000, 400.0000, 'fixed', 0.0000, 400.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-09 18:30:36', '2026-04-09 18:30:36'),
(6, 17, 14, 14, 1.0000, 0.0000, 0.0000, 400.0000, 400.0000, 'fixed', 0.0000, 400.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-09 19:05:41', '2026-04-09 19:05:41'),
(9, 20, 102, 102, 1.0000, 0.0000, 0.0000, 250.0000, 250.0000, 'fixed', 0.0000, 250.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-13 09:40:39', '2026-04-13 09:40:39'),
(10, 21, 102, 102, 1.0000, 0.0000, 0.0000, 250.0000, 250.0000, 'fixed', 0.0000, 250.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-13 13:23:46', '2026-04-13 13:23:46'),
(11, 22, 104, 104, 1.0000, 0.0000, 0.0000, 500.0000, 500.0000, 'fixed', 0.0000, 500.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-13 13:25:09', '2026-04-13 13:25:09'),
(12, 25, 18, 18, 1.0000, 0.0000, 0.0000, 250.0000, 250.0000, 'fixed', 0.0000, 250.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-13 19:05:36', '2026-04-13 19:05:36'),
(13, 26, 19, 19, 1.0000, 0.0000, 0.0000, 300.0000, 300.0000, 'fixed', 0.0000, 300.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-13 19:37:04', '2026-04-13 19:37:04'),
(14, 27, 19, 19, 1.0000, 0.0000, 0.0000, 300.0000, 300.0000, 'fixed', 0.0000, 300.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-13 19:39:30', '2026-04-13 19:39:30'),
(15, 28, 18, 18, 1.0000, 0.0000, 0.0000, 250.0000, 250.0000, 'fixed', 0.0000, 250.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-13 19:45:19', '2026-04-13 19:45:19'),
(16, 29, 18, 18, 1.0000, 0.0000, 0.0000, 250.0000, 250.0000, 'fixed', 0.0000, 250.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-13 19:48:55', '2026-04-13 19:48:55'),
(17, 31, 95, 95, 1.0000, 0.0000, 0.0000, 150.0000, 150.0000, 'fixed', 0.0000, 150.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-13 20:29:28', '2026-04-13 20:29:28'),
(18, 32, 13, 13, 1.0000, 0.0000, 0.0000, 200.0000, 200.0000, 'fixed', 0.0000, 200.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-13 20:48:20', '2026-04-13 20:48:20'),
(19, 33, 19, 19, 1.0000, 0.0000, 0.0000, 300.0000, 300.0000, 'fixed', 0.0000, 300.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-14 18:08:21', '2026-04-14 18:08:21'),
(20, 34, 2, 2, 1.0000, 0.0000, 0.0000, 200.0000, 200.0000, 'fixed', 0.0000, 200.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-19 18:35:01', '2026-04-19 18:35:01'),
(21, 35, 103, 103, 1.0000, 0.0000, 0.0000, 400.0000, 400.0000, 'fixed', 0.0000, 400.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-19 18:38:15', '2026-04-19 18:38:15'),
(22, 36, 13, 13, 1.0000, 0.0000, 0.0000, 200.0000, 200.0000, 'fixed', 0.0000, 200.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-19 18:40:25', '2026-04-19 18:40:25'),
(23, 39, 8, 8, 1.0000, 0.0000, 0.0000, 200.0000, 200.0000, 'fixed', 0.0000, 200.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-19 19:03:17', '2026-04-19 19:03:17'),
(24, 40, 8, 8, 1.0000, 0.0000, 0.0000, 200.0000, 200.0000, 'fixed', 0.0000, 200.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-19 20:18:46', '2026-04-19 20:18:46'),
(25, 41, 19, 19, 1.0000, 0.0000, 0.0000, 300.0000, 300.0000, 'fixed', 0.0000, 300.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-19 20:24:40', '2026-04-19 20:24:40'),
(26, 42, 14, 14, 1.0000, 0.0000, 0.0000, 400.0000, 400.0000, 'fixed', 0.0000, 400.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-19 20:26:57', '2026-04-19 20:26:57'),
(27, 43, 104, 104, 1.0000, 0.0000, 0.0000, 500.0000, 500.0000, 'fixed', 0.0000, 500.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-19 20:34:01', '2026-04-19 20:34:01'),
(28, 44, 101, 101, 1.0000, 0.0000, 0.0000, 100.0000, 100.0000, 'fixed', 0.0000, 100.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-19 20:34:44', '2026-04-19 20:34:44'),
(29, 47, 89, 89, 1.0000, 0.0000, 0.0000, 350.0000, 350.0000, 'fixed', 0.0000, 350.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 15:52:45', '2026-04-20 15:52:45'),
(30, 47, 74, 74, 1.0000, 0.0000, 0.0000, 400.0000, 400.0000, 'fixed', 0.0000, 400.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 15:52:45', '2026-04-20 15:52:45'),
(31, 48, 66, 66, 1.0000, 0.0000, 0.0000, 150.0000, 150.0000, 'fixed', 0.0000, 150.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 15:54:12', '2026-04-20 15:54:12'),
(32, 49, 66, 66, 1.0000, 0.0000, 0.0000, 150.0000, 150.0000, 'fixed', 0.0000, 150.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 15:54:12', '2026-04-20 15:54:12'),
(33, 50, 8, 8, 1.0000, 0.0000, 0.0000, 200.0000, 200.0000, 'fixed', 0.0000, 200.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(34, 50, 11, 11, 2.0000, 0.0000, 0.0000, 200.0000, 200.0000, 'fixed', 0.0000, 200.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(35, 50, 125, 125, 1.0000, 0.0000, 0.0000, 180.0000, 180.0000, 'fixed', 0.0000, 180.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(36, 50, 2, 2, 1.0000, 0.0000, 0.0000, 200.0000, 200.0000, 'fixed', 0.0000, 200.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(37, 50, 16, 16, 1.0000, 0.0000, 0.0000, 120.0000, 120.0000, 'fixed', 0.0000, 120.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(38, 50, 53, 53, 1.0000, 0.0000, 0.0000, 250.0000, 250.0000, 'fixed', 0.0000, 250.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(39, 50, 82, 82, 2.0000, 0.0000, 0.0000, 350.0000, 350.0000, 'fixed', 0.0000, 350.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(40, 52, 66, 66, 1.0000, 0.0000, 0.0000, 150.0000, 150.0000, 'fixed', 0.0000, 150.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 20:08:25', '2026-04-20 20:08:25'),
(41, 52, 17, 17, 1.0000, 0.0000, 0.0000, 80.0000, 80.0000, 'fixed', 0.0000, 80.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 20:08:25', '2026-04-20 20:08:25'),
(42, 52, 115, 115, 1.0000, 0.0000, 0.0000, 10.0000, 10.0000, 'fixed', 0.0000, 10.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 20:08:25', '2026-04-20 20:08:25'),
(43, 52, 86, 86, 1.0000, 0.0000, 0.0000, 100.0000, 100.0000, 'fixed', 0.0000, 100.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 20:08:25', '2026-04-20 20:08:25'),
(44, 52, 54, 54, 1.0000, 0.0000, 0.0000, 120.0000, 120.0000, 'fixed', 0.0000, 120.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 20:08:25', '2026-04-20 20:08:25'),
(45, 52, 11, 11, 1.0000, 0.0000, 0.0000, 200.0000, 200.0000, 'fixed', 0.0000, 200.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 20:08:25', '2026-04-20 20:08:25'),
(46, 52, 125, 125, 1.0000, 0.0000, 0.0000, 180.0000, 180.0000, 'fixed', 0.0000, 180.0000, 0.0000, NULL, NULL, NULL, '', NULL, 0.0000, NULL, NULL, NULL, '', NULL, '2026-04-20 20:08:25', '2026-04-20 20:08:25');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_sell_lines_purchase_lines`
--

CREATE TABLE `transaction_sell_lines_purchase_lines` (
  `id` bigint(20) NOT NULL,
  `sell_line_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'id from transaction_sell_lines',
  `stock_adjustment_line_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'id from stock_adjustment_lines',
  `purchase_line_id` int(10) UNSIGNED NOT NULL COMMENT 'id from purchase_lines',
  `quantity` decimal(22,4) NOT NULL,
  `qty_returned` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_sell_lines_purchase_lines`
--

INSERT INTO `transaction_sell_lines_purchase_lines` (`id`, `sell_line_id`, `stock_adjustment_line_id`, `purchase_line_id`, `quantity`, `qty_returned`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 2, 1.0000, 0.0000, '2026-04-09 10:53:04', '2026-04-09 10:53:04'),
(2, 2, NULL, 2, 1.0000, 0.0000, '2026-04-09 11:26:21', '2026-04-09 11:26:21'),
(3, 3, NULL, 9, 1.0000, 0.0000, '2026-04-09 12:06:13', '2026-04-09 12:06:13'),
(4, 4, NULL, 6, 1.0000, 0.0000, '2026-04-09 16:30:46', '2026-04-09 16:30:46'),
(5, 5, NULL, 12, 1.0000, 0.0000, '2026-04-09 18:30:36', '2026-04-09 18:30:36'),
(6, 6, NULL, 12, 1.0000, 0.0000, '2026-04-09 19:05:41', '2026-04-09 19:05:41'),
(8, 9, NULL, 2, 1.0000, 0.0000, '2026-04-13 09:40:39', '2026-04-13 09:40:39'),
(9, 10, NULL, 2, 1.0000, 0.0000, '2026-04-13 13:23:46', '2026-04-13 13:23:46'),
(10, 11, NULL, 5, 1.0000, 0.0000, '2026-04-13 13:25:09', '2026-04-13 13:25:09'),
(11, 12, NULL, 14, 1.0000, 0.0000, '2026-04-13 19:05:36', '2026-04-13 19:05:36'),
(12, 13, NULL, 6, 1.0000, 0.0000, '2026-04-13 19:37:04', '2026-04-13 19:37:04'),
(13, 14, NULL, 6, 1.0000, 0.0000, '2026-04-13 19:39:30', '2026-04-13 19:39:30'),
(14, 15, NULL, 14, 1.0000, 0.0000, '2026-04-13 19:45:19', '2026-04-13 19:45:19'),
(15, 16, NULL, 14, 1.0000, 0.0000, '2026-04-13 19:48:55', '2026-04-13 19:48:55'),
(16, 17, NULL, 15, 1.0000, 0.0000, '2026-04-13 20:29:28', '2026-04-13 20:29:28'),
(17, 18, NULL, 11, 1.0000, 0.0000, '2026-04-13 20:48:20', '2026-04-13 20:48:20'),
(18, 19, NULL, 6, 1.0000, 0.0000, '2026-04-14 18:08:21', '2026-04-14 18:08:21'),
(19, 20, NULL, 8, 1.0000, 0.0000, '2026-04-19 18:35:01', '2026-04-19 18:35:01'),
(20, 21, NULL, 3, 1.0000, 0.0000, '2026-04-19 18:38:15', '2026-04-19 18:38:15'),
(21, 22, NULL, 11, 1.0000, 0.0000, '2026-04-19 18:40:25', '2026-04-19 18:40:25'),
(22, 23, NULL, 16, 1.0000, 0.0000, '2026-04-19 19:03:17', '2026-04-19 19:03:17'),
(23, 24, NULL, 16, 1.0000, 0.0000, '2026-04-19 20:18:46', '2026-04-19 20:18:46'),
(24, 25, NULL, 6, 1.0000, 0.0000, '2026-04-19 20:24:40', '2026-04-19 20:24:40'),
(25, 26, NULL, 12, 1.0000, 0.0000, '2026-04-19 20:26:57', '2026-04-19 20:26:57'),
(26, 27, NULL, 5, 1.0000, 0.0000, '2026-04-19 20:34:01', '2026-04-19 20:34:01'),
(27, 28, NULL, 4, 1.0000, 0.0000, '2026-04-19 20:34:44', '2026-04-19 20:34:44'),
(28, 29, NULL, 85, 1.0000, 0.0000, '2026-04-20 15:52:45', '2026-04-20 15:52:45'),
(29, 30, NULL, 64, 1.0000, 0.0000, '2026-04-20 15:52:45', '2026-04-20 15:52:45'),
(30, 31, NULL, 7, 1.0000, 0.0000, '2026-04-20 15:54:12', '2026-04-20 15:54:12'),
(31, 32, NULL, 7, 1.0000, 0.0000, '2026-04-20 15:54:12', '2026-04-20 15:54:12'),
(32, 33, NULL, 16, 1.0000, 0.0000, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(33, 34, NULL, 49, 2.0000, 0.0000, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(34, 35, NULL, 17, 1.0000, 0.0000, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(35, 36, NULL, 8, 1.0000, 0.0000, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(36, 37, NULL, 54, 1.0000, 0.0000, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(37, 38, NULL, 28, 1.0000, 0.0000, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(38, 39, NULL, 75, 2.0000, 0.0000, '2026-04-20 15:57:56', '2026-04-20 15:57:56'),
(39, 40, NULL, 7, 1.0000, 0.0000, '2026-04-20 20:08:25', '2026-04-20 20:08:25'),
(40, 41, NULL, 53, 1.0000, 0.0000, '2026-04-20 20:08:25', '2026-04-20 20:08:25'),
(41, 42, NULL, 106, 1.0000, 0.0000, '2026-04-20 20:08:25', '2026-04-20 20:08:25'),
(42, 43, NULL, 79, 1.0000, 0.0000, '2026-04-20 20:08:25', '2026-04-20 20:08:25'),
(43, 44, NULL, 27, 1.0000, 0.0000, '2026-04-20 20:08:25', '2026-04-20 20:08:25'),
(44, 45, NULL, 49, 1.0000, 0.0000, '2026-04-20 20:08:25', '2026-04-20 20:08:25'),
(45, 46, NULL, 17, 1.0000, 0.0000, '2026-04-20 20:08:25', '2026-04-20 20:08:25');

-- --------------------------------------------------------

--
-- Table structure for table `types_of_services`
--

CREATE TABLE `types_of_services` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `business_id` int(11) NOT NULL,
  `location_price_group` text DEFAULT NULL,
  `packing_charge` decimal(22,4) DEFAULT NULL,
  `packing_charge_type` enum('fixed','percent') DEFAULT NULL,
  `enable_custom_fields` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(10) UNSIGNED NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `actual_name` varchar(191) NOT NULL,
  `short_name` varchar(191) NOT NULL,
  `allow_decimal` tinyint(1) NOT NULL,
  `base_unit_id` int(11) DEFAULT NULL,
  `base_unit_multiplier` decimal(20,4) DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `business_id`, `actual_name`, `short_name`, `allow_decimal`, `base_unit_id`, `base_unit_multiplier`, `created_by`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Pieces', 'Pc(s)', 0, NULL, NULL, 1, NULL, '2026-04-09 09:53:35', '2026-04-09 09:53:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_type` varchar(191) NOT NULL DEFAULT 'user',
  `surname` char(10) DEFAULT NULL,
  `first_name` varchar(191) NOT NULL,
  `last_name` varchar(191) DEFAULT NULL,
  `username` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `password` varchar(191) DEFAULT NULL,
  `language` char(7) NOT NULL DEFAULT 'en',
  `contact_no` char(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `business_id` int(10) UNSIGNED DEFAULT NULL,
  `available_at` datetime DEFAULT NULL COMMENT 'Service staff avilable at. Calculated from product preparation_time_in_minutes',
  `paused_at` datetime DEFAULT NULL COMMENT 'Service staff available time paused at, Will be nulled on resume.',
  `max_sales_discount_percent` decimal(5,2) DEFAULT NULL,
  `allow_login` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('active','inactive','terminated') NOT NULL DEFAULT 'active',
  `is_enable_service_staff_pin` tinyint(1) NOT NULL DEFAULT 0,
  `service_staff_pin` text DEFAULT NULL,
  `crm_contact_id` int(10) UNSIGNED DEFAULT NULL,
  `is_cmmsn_agnt` tinyint(1) NOT NULL DEFAULT 0,
  `cmmsn_percent` decimal(4,2) NOT NULL DEFAULT 0.00,
  `selected_contacts` tinyint(1) NOT NULL DEFAULT 0,
  `dob` date DEFAULT NULL,
  `gender` varchar(191) DEFAULT NULL,
  `marital_status` enum('married','unmarried','divorced') DEFAULT NULL,
  `blood_group` char(10) DEFAULT NULL,
  `contact_number` char(20) DEFAULT NULL,
  `alt_number` varchar(191) DEFAULT NULL,
  `family_number` varchar(191) DEFAULT NULL,
  `fb_link` varchar(191) DEFAULT NULL,
  `twitter_link` varchar(191) DEFAULT NULL,
  `social_media_1` varchar(191) DEFAULT NULL,
  `social_media_2` varchar(191) DEFAULT NULL,
  `permanent_address` text DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `guardian_name` varchar(191) DEFAULT NULL,
  `custom_field_1` varchar(191) DEFAULT NULL,
  `custom_field_2` varchar(191) DEFAULT NULL,
  `custom_field_3` varchar(191) DEFAULT NULL,
  `custom_field_4` varchar(191) DEFAULT NULL,
  `bank_details` longtext DEFAULT NULL,
  `id_proof_name` varchar(191) DEFAULT NULL,
  `id_proof_number` varchar(191) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_type`, `surname`, `first_name`, `last_name`, `username`, `email`, `password`, `language`, `contact_no`, `address`, `remember_token`, `business_id`, `available_at`, `paused_at`, `max_sales_discount_percent`, `allow_login`, `status`, `is_enable_service_staff_pin`, `service_staff_pin`, `crm_contact_id`, `is_cmmsn_agnt`, `cmmsn_percent`, `selected_contacts`, `dob`, `gender`, `marital_status`, `blood_group`, `contact_number`, `alt_number`, `family_number`, `fb_link`, `twitter_link`, `social_media_1`, `social_media_2`, `permanent_address`, `current_address`, `guardian_name`, `custom_field_1`, `custom_field_2`, `custom_field_3`, `custom_field_4`, `bank_details`, `id_proof_name`, `id_proof_number`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'user', NULL, 'Admin', 'Reenson', 'temp_1775728414', 'temp_1775728414@reenson.co.ke', '$2y$10$/JQuU1TlI0c.9PuxkJhXKOs2rpZKlevyXK1K0Sq68hIVDxcCXui9y', 'en', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'active', 0, NULL, NULL, 0, 0.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 09:53:34', '2026-04-09 09:53:34'),
(2, 'user', NULL, 'Super', 'Admin', 'admin', 'admin@reenson.co.ke', '$2y$10$KgIvDsecohYOw99JJXrfwOMpsTCEocTnW3PQbHMqm6evw/Vw8QP7i', 'en', NULL, NULL, '3imeUHuKuXP2T27kYcize22olzDWUeUpIHetWMXrGHymSnPxWG4f7QZCSu69', 1, NULL, NULL, NULL, 1, 'active', 0, NULL, NULL, 0, 0.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-09 09:53:35', '2026-04-09 09:53:35'),
(3, 'user', NULL, 'Sales', 'Attendant', 'SALES', 'reenson@gmail.com', '$2y$10$KbiIZzBvL18mJpqvBxkC7.WGkbtJLSzlp7E5Biypqzj1cOjqCBr1q', 'en', NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, 'active', 0, NULL, NULL, 0, 0.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{\"account_holder_name\":null,\"account_number\":null,\"bank_name\":null,\"bank_code\":null,\"branch\":null,\"tax_payer_id\":null}', NULL, NULL, NULL, '2026-04-09 10:49:26', '2026-04-09 11:24:31'),
(4, 'user', NULL, 'Sales', 'Kyaani', 'KYAANI', 'reensongroup@gmail.com', '$2y$10$v1dJbHLcpZbWkFlSg74oYuRk/2/klE0eQ58fcBfy8uJlA48tZlKpG', 'en', NULL, NULL, NULL, 1, NULL, NULL, NULL, 1, 'active', 0, NULL, NULL, 0, 0.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{\"account_holder_name\":null,\"account_number\":null,\"bank_name\":null,\"bank_code\":null,\"branch\":null,\"tax_payer_id\":null}', NULL, NULL, NULL, '2026-04-09 11:57:31', '2026-04-09 11:58:48');

-- --------------------------------------------------------

--
-- Table structure for table `user_contact_access`
--

CREATE TABLE `user_contact_access` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variations`
--

CREATE TABLE `variations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `sub_sku` varchar(191) DEFAULT NULL,
  `product_variation_id` int(10) UNSIGNED NOT NULL,
  `variation_value_id` int(11) DEFAULT NULL,
  `default_purchase_price` decimal(22,4) DEFAULT NULL,
  `dpp_inc_tax` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `profit_percent` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `default_sell_price` decimal(22,4) DEFAULT NULL,
  `sell_price_inc_tax` decimal(22,4) DEFAULT NULL COMMENT 'Sell price including tax',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `combo_variations` text DEFAULT NULL COMMENT 'Contains the combo variation details'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variations`
--

INSERT INTO `variations` (`id`, `name`, `product_id`, `sub_sku`, `product_variation_id`, `variation_value_id`, `default_purchase_price`, `dpp_inc_tax`, `profit_percent`, `default_sell_price`, `sell_price_inc_tax`, `created_at`, `updated_at`, `deleted_at`, `combo_variations`) VALUES
(1, 'DUMMY', 1, '0001', 1, NULL, 4.0000, 4.0000, 25.0000, 5.0000, 5.0000, '2026-04-09 09:57:03', '2026-04-09 09:57:03', NULL, '[]'),
(2, 'DUMMY', 2, '0002', 2, NULL, 124.0000, 124.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(3, 'DUMMY', 3, '0003', 3, NULL, 181.0000, 181.0000, 120.9945, 400.0000, 400.0000, '2026-04-09 10:50:25', '2026-04-20 20:03:40', NULL, '[]'),
(4, 'DUMMY', 4, '0004', 4, NULL, 71.0000, 71.0000, 25.0000, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 18:25:56', NULL, '[]'),
(5, 'DUMMY', 5, '0005', 5, NULL, 226.0000, 226.0000, 25.0000, 350.0000, 350.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(6, 'DUMMY', 6, '0006', 6, NULL, 467.0000, 467.0000, 25.0000, 550.0000, 550.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(7, 'DUMMY', 7, '0007', 7, NULL, 35.0000, 35.0000, 25.0000, 100.0000, 100.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(8, 'DUMMY', 8, '0008', 8, NULL, 82.0000, 82.0000, 143.9024, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-20 20:03:40', NULL, '[]'),
(9, 'DUMMY', 9, '0009', 9, NULL, 87.0000, 87.0000, 25.0000, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(10, 'DUMMY', 10, '0010', 10, NULL, 220.0000, 220.0000, 25.0000, 450.0000, 450.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(11, 'DUMMY', 11, '0011', 11, NULL, 152.0000, 152.0000, 31.5789, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-19 21:30:28', NULL, '[]'),
(12, 'DUMMY', 12, '0012', 12, NULL, 287.0000, 287.0000, 25.0000, 400.0000, 400.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(13, 'DUMMY', 13, '0013', 13, NULL, 141.0000, 141.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(14, 'DUMMY', 14, '0014', 14, NULL, 217.0000, 217.0000, 25.0000, 400.0000, 400.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(15, 'DUMMY', 15, '0015', 15, NULL, 109.0000, 109.0000, 25.0000, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(16, 'DUMMY', 16, '0016', 16, NULL, 80.0000, 80.0000, 50.0000, 120.0000, 120.0000, '2026-04-09 10:50:25', '2026-04-20 20:03:40', NULL, '[]'),
(17, 'DUMMY', 17, '0017', 17, NULL, 55.0000, 55.0000, 25.0000, 80.0000, 80.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(18, 'DUMMY', 18, '0018', 18, NULL, 150.0000, 150.0000, 25.0000, 250.0000, 250.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(19, 'DUMMY', 19, '0019', 19, NULL, 217.0000, 217.0000, 25.0000, 300.0000, 300.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(20, 'DUMMY', 20, '0020', 20, NULL, 97.0000, 97.0000, 25.0000, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(21, 'DUMMY', 21, '0021', 21, NULL, 400.0000, 400.0000, 25.0000, 600.0000, 600.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(22, 'DUMMY', 22, '0022', 22, NULL, 141.0000, 141.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(23, 'DUMMY', 23, '0023', 23, NULL, 210.0000, 210.0000, 25.0000, 250.0000, 250.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(24, 'DUMMY', 24, '0024', 24, NULL, 400.0000, 400.0000, 25.0000, 450.0000, 450.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(25, 'DUMMY', 25, '0025', 25, NULL, 750.0000, 750.0000, 25.0000, 1000.0000, 1000.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(26, 'DUMMY', 26, '0026', 26, NULL, 51.0000, 51.0000, 25.0000, 100.0000, 100.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(27, 'DUMMY', 27, '0027', 27, NULL, 51.0000, 51.0000, 25.0000, 100.0000, 100.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(28, 'DUMMY', 28, '0028', 28, NULL, 87.0000, 87.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(29, 'DUMMY', 29, '0029', 29, NULL, 51.0000, 51.0000, 25.0000, 100.0000, 100.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(30, 'DUMMY', 30, '0030', 30, NULL, 90.0000, 90.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(31, 'DUMMY', 31, '0031', 31, NULL, 46.0000, 46.0000, 25.0000, 100.0000, 100.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(32, 'DUMMY', 32, '0032', 32, NULL, 80.0000, 80.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(33, 'DUMMY', 33, '0033', 33, NULL, 110.0000, 110.0000, 25.0000, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(34, 'DUMMY', 34, '0034', 34, NULL, 331.0000, 331.0000, 25.0000, 450.0000, 450.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(35, 'DUMMY', 35, '0035', 35, NULL, 107.0000, 107.0000, 25.0000, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(36, 'DUMMY', 36, '0036', 36, NULL, 40.0000, 40.0000, 150.0000, 100.0000, 100.0000, '2026-04-09 10:50:25', '2026-04-19 21:30:28', NULL, '[]'),
(37, 'DUMMY', 37, '0037', 37, NULL, 102.0000, 102.0000, 47.0588, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-19 21:30:28', NULL, '[]'),
(38, 'DUMMY', 38, '0038', 38, NULL, 120.0000, 120.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(39, 'DUMMY', 39, '0039', 39, NULL, 97.0000, 97.0000, 25.0000, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(40, 'DUMMY', 40, '0040', 40, NULL, 200.0000, 200.0000, 25.0000, 350.0000, 350.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(41, 'DUMMY', 41, '0041', 41, NULL, 78.0000, 78.0000, 25.0000, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(42, 'DUMMY', 42, '0042', 42, NULL, 106.0000, 106.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(43, 'DUMMY', 43, '0043', 43, NULL, 52.0000, 52.0000, 25.0000, 100.0000, 100.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(44, 'DUMMY', 44, '0044', 44, NULL, 83.0000, 83.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(45, 'DUMMY', 45, '0045', 45, NULL, 242.0000, 242.0000, 25.0000, 400.0000, 400.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(46, 'DUMMY', 46, '0046', 46, NULL, 38.0000, 38.0000, 25.0000, 80.0000, 80.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(47, 'DUMMY', 47, '0047', 47, NULL, 42.0000, 42.0000, 25.0000, 90.0000, 90.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(48, 'DUMMY', 48, '0048', 48, NULL, 12.0000, 12.0000, 25.0000, 30.0000, 30.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(49, 'DUMMY', 49, '0049', 49, NULL, 67.0000, 67.0000, 25.0000, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(50, 'DUMMY', 50, '0050', 50, NULL, 67.0000, 67.0000, 25.0000, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(51, 'DUMMY', 51, '0051', 51, NULL, 67.0000, 67.0000, 25.0000, 120.0000, 120.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(52, 'DUMMY', 52, '0052', 52, NULL, 200.0000, 200.0000, 100.0000, 400.0000, 400.0000, '2026-04-09 10:50:25', '2026-04-19 21:30:28', NULL, '[]'),
(53, 'DUMMY', 53, '0053', 53, NULL, 122.0000, 122.0000, 104.9180, 250.0000, 250.0000, '2026-04-09 10:50:25', '2026-04-19 21:30:28', NULL, '[]'),
(54, 'DUMMY', 54, '0054', 54, NULL, 78.0000, 78.0000, 25.0000, 120.0000, 120.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(55, 'DUMMY', 55, '0055', 55, NULL, 550.0000, 550.0000, -9.0909, 500.0000, 500.0000, '2026-04-09 10:50:25', '2026-04-19 21:30:28', NULL, '[]'),
(56, 'DUMMY', 56, '0056', 56, NULL, 550.0000, 550.0000, -18.1818, 450.0000, 450.0000, '2026-04-09 10:50:25', '2026-04-19 21:30:28', NULL, '[]'),
(57, 'DUMMY', 57, '0057', 57, NULL, 200.0000, 200.0000, 25.0000, 350.0000, 350.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(58, 'DUMMY', 58, '0058', 58, NULL, 122.0000, 122.0000, -98.3607, 2.0000, 2.0000, '2026-04-09 10:50:25', '2026-04-19 21:30:28', NULL, '[]'),
(59, 'DUMMY', 59, '0059', 59, NULL, 67.0000, 67.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(60, 'DUMMY', 60, '0060', 60, NULL, 1550.0000, 1550.0000, 25.0000, 2200.0000, 2200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(61, 'DUMMY', 61, '0061', 61, NULL, 2000.0000, 2000.0000, 25.0000, 2800.0000, 2800.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(62, 'DUMMY', 62, '0062', 62, NULL, 97.0000, 97.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(63, 'DUMMY', 63, '0063', 63, NULL, 92.0000, 92.0000, -94.5652, 5.0000, 5.0000, '2026-04-09 10:50:25', '2026-04-19 21:30:28', NULL, '[]'),
(64, 'DUMMY', 64, '0064', 64, NULL, 179.0000, 179.0000, 25.0000, 350.0000, 350.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(65, 'DUMMY', 65, '0065', 65, NULL, 68.0000, 68.0000, 25.0000, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(66, 'DUMMY', 66, '0066', 66, NULL, 84.0000, 84.0000, 78.5714, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 11:39:10', NULL, '[]'),
(67, 'DUMMY', 67, '0067', 67, NULL, 179.0000, 179.0000, 25.0000, 400.0000, 400.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(68, 'DUMMY', 68, '0068', 68, NULL, 10.0000, 10.0000, 25.0000, 30.0000, 30.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(69, 'DUMMY', 69, '0069', 69, NULL, 498.0000, 498.0000, 25.0000, 600.0000, 600.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(70, 'DUMMY', 70, '0070', 70, NULL, 598.0000, 598.0000, 25.0000, 700.0000, 700.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(71, 'DUMMY', 71, '0071', 71, NULL, 598.0000, 598.0000, 25.0000, 700.0000, 700.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(72, 'DUMMY', 72, '0072', 72, NULL, 785.0000, 785.0000, 25.0000, 1000.0000, 1000.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(73, 'DUMMY', 73, '0073', 73, NULL, 41.0000, 41.0000, 25.0000, 100.0000, 100.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(74, 'DUMMY', 74, '0074', 74, NULL, 280.0000, 280.0000, 25.0000, 400.0000, 400.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(75, 'DUMMY', 75, '0075', 75, NULL, 241.0000, 241.0000, 45.2282, 350.0000, 350.0000, '2026-04-09 10:50:25', '2026-04-20 20:03:40', NULL, '[]'),
(76, 'DUMMY', 76, '0076', 76, NULL, 102.0000, 102.0000, 25.0000, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(77, 'DUMMY', 77, '0077', 77, NULL, 187.0000, 187.0000, 25.0000, 400.0000, 400.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(78, 'DUMMY', 78, '0078', 78, NULL, 227.0000, 227.0000, 25.0000, 300.0000, 300.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(79, 'DUMMY', 79, '0079', 79, NULL, 200.0000, 200.0000, 25.0000, 350.0000, 350.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(80, 'DUMMY', 80, '0080', 80, NULL, 387.0000, 387.0000, 25.0000, 550.0000, 550.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(81, 'DUMMY', 81, '0081', 81, NULL, 118.0000, 118.0000, 27.1186, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-20 20:03:40', NULL, '[]'),
(82, 'DUMMY', 82, '0082', 82, NULL, 132.0000, 132.0000, 165.1515, 350.0000, 350.0000, '2026-04-09 10:50:25', '2026-04-20 20:03:40', NULL, '[]'),
(83, 'DUMMY', 83, '0083', 83, NULL, 410.0000, 410.0000, 25.0000, 450.0000, 450.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(84, 'DUMMY', 84, '0084', 84, NULL, 261.0000, 261.0000, 25.0000, 350.0000, 350.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(85, 'DUMMY', 85, '0085', 85, NULL, 44.0000, 44.0000, 25.0000, 100.0000, 100.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(86, 'DUMMY', 86, '0086', 86, NULL, 78.0000, 78.0000, 25.0000, 100.0000, 100.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(87, 'DUMMY', 87, '0087', 87, NULL, 87.0000, 87.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(88, 'DUMMY', 88, '0088', 88, NULL, 79.0000, 79.0000, 25.0000, 120.0000, 120.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(89, 'DUMMY', 89, '0089', 89, NULL, 209.0000, 209.0000, 25.0000, 350.0000, 350.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(90, 'DUMMY', 90, '0090', 90, NULL, 147.0000, 147.0000, 25.0000, 250.0000, 250.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(91, 'DUMMY', 91, '0091', 91, NULL, 147.0000, 147.0000, 25.0000, 250.0000, 250.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(92, 'DUMMY', 92, '0092', 92, NULL, 102.0000, 102.0000, 25.0000, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(93, 'DUMMY', 93, '0093', 93, NULL, 121.0000, 121.0000, 25.0000, 250.0000, 250.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(94, 'DUMMY', 94, '0094', 94, NULL, 200.0000, 200.0000, 25.0000, 400.0000, 400.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(95, 'DUMMY', 95, '0095', 95, NULL, 110.0000, 110.0000, 36.3636, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-20 20:03:40', NULL, '[]'),
(96, 'DUMMY', 96, '0096', 96, NULL, 167.0000, 167.0000, 25.0000, 250.0000, 250.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(97, 'DUMMY', 97, '0097', 97, NULL, 267.0000, 267.0000, 25.0000, 400.0000, 400.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(98, 'DUMMY', 98, '0098', 98, NULL, 410.0000, 410.0000, 25.0000, 450.0000, 450.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(99, 'DUMMY', 99, '0099', 99, NULL, 267.0000, 267.0000, 25.0000, 350.0000, 350.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(100, 'DUMMY', 100, '0100', 100, NULL, 132.0000, 132.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(101, 'DUMMY', 101, '0101', 101, NULL, 61.0000, 61.0000, 25.0000, 100.0000, 100.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(102, 'DUMMY', 102, '0102', 102, NULL, 141.0000, 141.0000, 25.0000, 250.0000, 250.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(103, 'DUMMY', 103, '0103', 103, NULL, 265.0000, 265.0000, 25.0000, 400.0000, 400.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(104, 'DUMMY', 104, '0104', 104, NULL, 457.0000, 457.0000, 25.0000, 500.0000, 500.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(105, 'DUMMY', 105, '0105', 105, NULL, 61.0000, 61.0000, 25.0000, 100.0000, 100.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(106, 'DUMMY', 106, '0106', 106, NULL, 141.0000, 141.0000, 25.0000, 250.0000, 250.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(107, 'DUMMY', 107, '0107', 107, NULL, 457.0000, 457.0000, 25.0000, 500.0000, 500.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(108, 'DUMMY', 108, '0108', 108, NULL, 247.0000, 247.0000, 21.4575, 300.0000, 300.0000, '2026-04-09 10:50:25', '2026-04-20 20:03:40', NULL, '[]'),
(109, 'DUMMY', 109, '0109', 109, NULL, 72.0000, 72.0000, 25.0000, 150.0000, 150.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(110, 'DUMMY', 110, '0110', 110, NULL, 101.0000, 101.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(111, 'DUMMY', 111, '0111', 111, NULL, 12.0000, 12.0000, 25.0000, 30.0000, 30.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(112, 'DUMMY', 112, '0112', 112, NULL, 31.0000, 31.0000, 25.0000, 50.0000, 50.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(113, 'DUMMY', 113, '0113', 113, NULL, 17.0000, 17.0000, 25.0000, 30.0000, 30.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(114, 'DUMMY', 114, '0114', 114, NULL, 15.0000, 15.0000, 25.0000, 30.0000, 30.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(115, 'DUMMY', 115, '0115', 115, NULL, 7.0000, 7.0000, 25.0000, 10.0000, 10.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(116, 'DUMMY', 116, '0116', 116, NULL, 50.0000, 50.0000, 25.0000, 100.0000, 100.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(117, 'DUMMY', 117, '0117', 117, NULL, 87.0000, 87.0000, 25.0000, 250.0000, 250.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(118, 'DUMMY', 118, '0118', 118, NULL, 400.0000, 400.0000, 25.0000, 500.0000, 500.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(119, 'DUMMY', 119, '0119', 119, NULL, 220.0000, 220.0000, 25.0000, 300.0000, 300.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(120, 'DUMMY', 120, '0120', 120, NULL, 200.0000, 200.0000, 25.0000, 151.0000, 151.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(121, 'DUMMY', 121, '0121', 121, NULL, 87.0000, 87.0000, 25.0000, 120.0000, 120.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(122, 'DUMMY', 122, '0122', 122, NULL, 12.0000, 12.0000, 25.0000, 20.0000, 20.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(123, 'DUMMY', 123, '0123', 123, NULL, 41.0000, 41.0000, 25.0000, 100.0000, 100.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(124, 'DUMMY', 124, '0124', 124, NULL, 239.0000, 239.0000, 67.3640, 400.0000, 400.0000, '2026-04-09 10:50:25', '2026-04-20 20:03:40', NULL, '[]'),
(125, 'DUMMY', 125, '0125', 125, NULL, 124.0000, 124.0000, 25.0000, 180.0000, 180.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(126, 'DUMMY', 126, '0126', 126, NULL, 124.0000, 124.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(127, 'DUMMY', 127, '0127', 127, NULL, 228.0000, 228.0000, 25.0000, 400.0000, 400.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(128, 'DUMMY', 128, '0128', 128, NULL, 240.0000, 240.0000, 25.0000, 350.0000, 350.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(129, 'DUMMY', 129, '0129', 129, NULL, 140.0000, 140.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(130, 'DUMMY', 130, '0130', 130, NULL, 180.0000, 180.0000, 25.0000, 300.0000, 300.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(131, 'DUMMY', 131, '0131', 131, NULL, 140.0000, 140.0000, 25.0000, 200.0000, 200.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(132, 'DUMMY', 132, '0132', 132, NULL, 1300.0000, 1300.0000, 25.0000, 2000.0000, 2000.0000, '2026-04-09 10:50:25', '2026-04-09 10:50:25', NULL, '[]'),
(133, 'DUMMY', 133, '0133', 133, NULL, 80.0000, 80.0000, 87.5000, 150.0000, 150.0000, '2026-04-09 11:42:31', '2026-04-09 11:42:31', NULL, '[]'),
(134, 'DUMMY', 134, '0134', 134, NULL, 228.0000, 228.0000, 75.4400, 400.0000, 400.0000, '2026-04-09 11:43:40', '2026-04-09 11:43:40', NULL, '[]'),
(135, 'DUMMY', 135, '0135', 135, NULL, 200.0000, 200.0000, 50.0000, 300.0000, 300.0000, '2026-04-19 21:37:20', '2026-04-19 21:37:20', NULL, '[]'),
(136, 'DUMMY', 136, '0136', 136, NULL, 1110.0000, 1110.0000, 35.1400, 1500.0000, 1500.0000, '2026-04-19 21:39:27', '2026-04-19 21:39:27', NULL, '[]'),
(137, 'DUMMY', 137, '0137', 137, NULL, 2400.0000, 2400.0000, 41.6700, 3400.0000, 3400.0000, '2026-04-19 21:40:39', '2026-04-19 21:40:39', NULL, '[]');

-- --------------------------------------------------------

--
-- Table structure for table `variation_group_prices`
--

CREATE TABLE `variation_group_prices` (
  `id` int(10) UNSIGNED NOT NULL,
  `variation_id` int(10) UNSIGNED NOT NULL,
  `price_group_id` int(10) UNSIGNED NOT NULL,
  `price_inc_tax` decimal(22,4) NOT NULL,
  `price_type` varchar(191) NOT NULL DEFAULT 'fixed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variation_location_details`
--

CREATE TABLE `variation_location_details` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `product_variation_id` int(10) UNSIGNED NOT NULL COMMENT 'id from product_variations table',
  `variation_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `qty_available` decimal(22,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `variation_location_details`
--

INSERT INTO `variation_location_details` (`id`, `product_id`, `product_variation_id`, `variation_id`, `location_id`, `qty_available`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 120.0000, '2026-04-09 09:57:23', '2026-04-09 10:47:01'),
(2, 102, 102, 102, 1, 10.0000, '2026-04-09 10:52:17', '2026-04-19 21:30:28'),
(3, 103, 103, 103, 1, 9.0000, '2026-04-09 10:52:26', '2026-04-19 21:30:28'),
(4, 101, 101, 101, 1, 16.0000, '2026-04-09 10:52:32', '2026-04-20 20:03:40'),
(5, 104, 104, 104, 1, 15.0000, '2026-04-09 10:52:39', '2026-04-19 21:30:28'),
(6, 19, 19, 19, 1, 0.0000, '2026-04-09 11:34:23', '2026-04-19 20:24:40'),
(7, 66, 66, 66, 1, 10.0000, '2026-04-09 11:39:10', '2026-04-20 20:08:25'),
(8, 2, 2, 2, 1, 15.0000, '2026-04-09 11:39:10', '2026-04-20 15:57:56'),
(9, 134, 134, 134, 1, 4.0000, '2026-04-09 11:44:01', '2026-04-09 12:06:13'),
(10, 133, 133, 133, 1, 5.0000, '2026-04-09 11:47:26', '2026-04-09 11:47:26'),
(11, 13, 13, 13, 1, 10.0000, '2026-04-09 16:37:04', '2026-04-19 21:30:28'),
(12, 14, 14, 14, 1, 5.0000, '2026-04-09 16:37:04', '2026-04-19 21:30:28'),
(13, 101, 101, 101, 2, 1.0000, '2026-04-13 19:01:53', '2026-04-13 19:01:53'),
(14, 18, 18, 18, 2, 8.0000, '2026-04-13 19:05:14', '2026-04-13 19:48:55'),
(15, 95, 95, 95, 2, 4.0000, '2026-04-13 20:14:32', '2026-04-13 20:29:28'),
(16, 8, 8, 8, 1, 8.0000, '2026-04-19 18:55:57', '2026-04-20 20:03:40'),
(17, 125, 125, 125, 1, 8.0000, '2026-04-19 18:58:45', '2026-04-20 20:08:25'),
(18, 36, 36, 36, 1, 6.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(19, 37, 37, 37, 1, 7.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(20, 39, 39, 39, 1, 5.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(21, 41, 41, 41, 1, 5.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(22, 43, 43, 43, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(23, 45, 45, 45, 1, 2.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(24, 47, 47, 47, 1, 2.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(25, 48, 48, 48, 1, 7.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(26, 59, 59, 59, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(27, 54, 54, 54, 1, 5.0000, '2026-04-19 21:30:28', '2026-04-20 20:08:25'),
(28, 53, 53, 53, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-20 15:57:56'),
(29, 52, 52, 52, 1, 2.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(30, 55, 55, 55, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(31, 58, 58, 58, 1, 1.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(32, 57, 57, 57, 1, 1.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(33, 56, 56, 56, 1, 2.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(34, 60, 60, 60, 1, 1.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(35, 132, 132, 132, 1, 1.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(36, 122, 122, 122, 1, 23.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(37, 63, 63, 63, 1, 2.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(38, 67, 67, 67, 1, 4.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(39, 68, 68, 68, 1, 79.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(40, 70, 70, 70, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(41, 3, 3, 3, 1, 5.0000, '2026-04-19 21:30:28', '2026-04-20 20:03:40'),
(42, 5, 5, 5, 1, 4.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(43, 6, 6, 6, 1, 4.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(44, 9, 9, 9, 1, 9.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(45, 11, 11, 11, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-20 20:08:25'),
(46, 12, 12, 12, 1, 5.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(47, 17, 17, 17, 1, 11.0000, '2026-04-19 21:30:28', '2026-04-20 20:08:25'),
(48, 16, 16, 16, 1, 16.0000, '2026-04-19 21:30:28', '2026-04-20 20:03:40'),
(49, 15, 15, 15, 1, 8.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(50, 20, 20, 20, 1, 5.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(51, 18, 18, 18, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(52, 22, 22, 22, 1, 6.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(53, 23, 23, 23, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(54, 25, 25, 25, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(55, 33, 33, 33, 1, 4.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(56, 34, 34, 34, 1, 2.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(57, 35, 35, 35, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(58, 74, 74, 74, 1, 6.0000, '2026-04-19 21:30:28', '2026-04-20 20:03:40'),
(59, 75, 75, 75, 1, 5.0000, '2026-04-19 21:30:28', '2026-04-20 20:03:40'),
(60, 78, 78, 78, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(61, 73, 73, 73, 1, 4.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(62, 26, 26, 26, 1, 5.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(63, 28, 28, 28, 1, 5.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(64, 27, 27, 27, 1, 7.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(65, 29, 29, 29, 1, 4.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(66, 31, 31, 31, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(67, 32, 32, 32, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(68, 79, 79, 79, 1, 2.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(69, 82, 82, 82, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-20 20:03:40'),
(70, 85, 85, 85, 1, 9.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(71, 83, 83, 83, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(72, 84, 84, 84, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(73, 86, 86, 86, 1, 9.0000, '2026-04-19 21:30:28', '2026-04-20 20:08:25'),
(74, 98, 98, 98, 1, 1.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(75, 87, 87, 87, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(76, 88, 88, 88, 1, 9.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(77, 89, 89, 89, 1, 2.0000, '2026-04-19 21:30:28', '2026-04-20 15:52:45'),
(78, 90, 90, 90, 1, 2.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(79, 91, 91, 91, 1, 4.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(80, 92, 92, 92, 1, 4.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(81, 96, 96, 96, 1, 4.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(82, 97, 97, 97, 1, 1.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(83, 100, 100, 100, 1, 6.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(84, 105, 105, 105, 1, 6.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(85, 106, 106, 106, 1, 4.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(86, 107, 107, 107, 1, 2.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(87, 108, 108, 108, 1, 4.0000, '2026-04-19 21:30:28', '2026-04-20 20:03:40'),
(88, 109, 109, 109, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(89, 110, 110, 110, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(90, 111, 111, 111, 1, 18.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(91, 112, 112, 112, 1, 6.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(92, 113, 113, 113, 1, 46.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(93, 114, 114, 114, 1, 35.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(94, 115, 115, 115, 1, 21.0000, '2026-04-19 21:30:28', '2026-04-20 20:08:25'),
(95, 116, 116, 116, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(96, 117, 117, 117, 1, 1.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(97, 123, 123, 123, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(98, 118, 118, 118, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(99, 119, 119, 119, 1, 5.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(100, 120, 120, 120, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(101, 121, 121, 121, 1, 4.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(102, 124, 124, 124, 1, 5.0000, '2026-04-19 21:30:28', '2026-04-20 20:03:40'),
(103, 126, 126, 126, 1, 1.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(104, 127, 127, 127, 1, 2.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(105, 130, 130, 130, 1, 2.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(106, 131, 131, 131, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(107, 128, 128, 128, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(108, 129, 129, 129, 1, 3.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(109, 10, 10, 10, 1, 4.0000, '2026-04-19 21:30:28', '2026-04-19 21:30:28'),
(110, 136, 136, 136, 1, 1.0000, '2026-04-19 21:41:00', '2026-04-19 21:41:00'),
(111, 137, 137, 137, 1, 1.0000, '2026-04-19 21:41:00', '2026-04-19 21:41:00'),
(112, 95, 95, 95, 1, 8.0000, '2026-04-20 20:03:40', '2026-04-20 20:03:40'),
(113, 81, 81, 81, 1, 3.0000, '2026-04-20 20:03:40', '2026-04-20 20:03:40');

-- --------------------------------------------------------

--
-- Table structure for table `variation_templates`
--

CREATE TABLE `variation_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `business_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `variation_value_templates`
--

CREATE TABLE `variation_value_templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `variation_template_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warranties`
--

CREATE TABLE `warranties` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `business_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `duration_type` enum('days','months','years') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accounts_business_id_index` (`business_id`),
  ADD KEY `accounts_account_type_id_index` (`account_type_id`),
  ADD KEY `accounts_created_by_index` (`created_by`);

--
-- Indexes for table `account_transactions`
--
ALTER TABLE `account_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_transactions_account_id_index` (`account_id`),
  ADD KEY `account_transactions_transaction_id_index` (`transaction_id`),
  ADD KEY `account_transactions_transaction_payment_id_index` (`transaction_payment_id`),
  ADD KEY `account_transactions_transfer_transaction_id_index` (`transfer_transaction_id`),
  ADD KEY `account_transactions_created_by_index` (`created_by`),
  ADD KEY `account_transactions_type_index` (`type`),
  ADD KEY `account_transactions_sub_type_index` (`sub_type`),
  ADD KEY `account_transactions_operation_date_index` (`operation_date`);

--
-- Indexes for table `account_types`
--
ALTER TABLE `account_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_types_parent_account_type_id_index` (`parent_account_type_id`),
  ADD KEY `account_types_business_id_index` (`business_id`);

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_log_log_name_index` (`log_name`);

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approvals_approvable_type_approvable_id_index` (`approvable_type`,`approvable_id`),
  ADD KEY `approvals_business_id_status_index` (`business_id`,`status`);

--
-- Indexes for table `approval_decisions`
--
ALTER TABLE `approval_decisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_decisions_approval_id_foreign` (`approval_id`);

--
-- Indexes for table `approval_flows`
--
ALTER TABLE `approval_flows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_flows_business_id_index` (`business_id`);

--
-- Indexes for table `approval_flow_steps`
--
ALTER TABLE `approval_flow_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_flow_steps_approval_flow_id_foreign` (`approval_flow_id`);

--
-- Indexes for table `barcodes`
--
ALTER TABLE `barcodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barcodes_business_id_foreign` (`business_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_contact_id_foreign` (`contact_id`),
  ADD KEY `bookings_business_id_foreign` (`business_id`),
  ADD KEY `bookings_created_by_foreign` (`created_by`),
  ADD KEY `bookings_table_id_index` (`table_id`),
  ADD KEY `bookings_waiter_id_index` (`waiter_id`),
  ADD KEY `bookings_location_id_index` (`location_id`),
  ADD KEY `bookings_booking_status_index` (`booking_status`),
  ADD KEY `bookings_correspondent_id_index` (`correspondent_id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brands_business_id_foreign` (`business_id`),
  ADD KEY `brands_created_by_foreign` (`created_by`);

--
-- Indexes for table `business`
--
ALTER TABLE `business`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_owner_id_foreign` (`owner_id`),
  ADD KEY `business_currency_id_foreign` (`currency_id`),
  ADD KEY `business_default_sales_tax_foreign` (`default_sales_tax`),
  ADD KEY `business_business_type_index` (`business_type`);

--
-- Indexes for table `business_locations`
--
ALTER TABLE `business_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_locations_business_id_index` (`business_id`),
  ADD KEY `business_locations_invoice_scheme_id_foreign` (`invoice_scheme_id`),
  ADD KEY `business_locations_invoice_layout_id_foreign` (`invoice_layout_id`),
  ADD KEY `business_locations_sale_invoice_layout_id_index` (`sale_invoice_layout_id`),
  ADD KEY `business_locations_selling_price_group_id_index` (`selling_price_group_id`),
  ADD KEY `business_locations_receipt_printer_type_index` (`receipt_printer_type`),
  ADD KEY `business_locations_printer_id_index` (`printer_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cash_denominations`
--
ALTER TABLE `cash_denominations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_denominations_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `cash_registers`
--
ALTER TABLE `cash_registers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_registers_business_id_foreign` (`business_id`),
  ADD KEY `cash_registers_user_id_foreign` (`user_id`),
  ADD KEY `cash_registers_location_id_index` (`location_id`);

--
-- Indexes for table `cash_register_transactions`
--
ALTER TABLE `cash_register_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_register_transactions_cash_register_id_foreign` (`cash_register_id`),
  ADD KEY `cash_register_transactions_transaction_id_index` (`transaction_id`),
  ADD KEY `cash_register_transactions_type_index` (`type`),
  ADD KEY `cash_register_transactions_transaction_type_index` (`transaction_type`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_business_id_foreign` (`business_id`),
  ADD KEY `categories_created_by_foreign` (`created_by`),
  ADD KEY `categories_parent_id_index` (`parent_id`);

--
-- Indexes for table `categorizables`
--
ALTER TABLE `categorizables`
  ADD KEY `categorizables_categorizable_type_categorizable_id_index` (`categorizable_type`,`categorizable_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contacts_order_token_unique` (`order_token`),
  ADD KEY `contacts_business_id_foreign` (`business_id`),
  ADD KEY `contacts_created_by_foreign` (`created_by`),
  ADD KEY `contacts_type_index` (`type`),
  ADD KEY `contacts_contact_status_index` (`contact_status`),
  ADD KEY `contacts_job_token_index` (`job_token`);

--
-- Indexes for table `cooler_agreements`
--
ALTER TABLE `cooler_agreements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cooler_agreements_cooler_id_foreign` (`cooler_id`),
  ADD KEY `cooler_agreements_created_by_foreign` (`created_by`),
  ADD KEY `cooler_agreements_business_id_status_index` (`business_id`,`status`),
  ADD KEY `cooler_agreements_dealer_id_status_index` (`dealer_id`,`status`);

--
-- Indexes for table `cooler_assets`
--
ALTER TABLE `cooler_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cooler_assets_business_id_asset_number_unique` (`business_id`,`asset_number`),
  ADD KEY `cooler_assets_current_dealer_id_foreign` (`current_dealer_id`),
  ADD KEY `cooler_assets_created_by_foreign` (`created_by`),
  ADD KEY `cooler_assets_business_id_status_index` (`business_id`,`status`);

--
-- Indexes for table `cooler_compliance_logs`
--
ALTER TABLE `cooler_compliance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cooler_compliance_logs_cooler_id_foreign` (`cooler_id`),
  ADD KEY `cooler_compliance_logs_checked_by_foreign` (`checked_by`),
  ADD KEY `cooler_compliance_logs_dealer_id_check_type_index` (`dealer_id`,`check_type`),
  ADD KEY `cooler_compliance_logs_dealer_id_status_index` (`dealer_id`,`status`),
  ADD KEY `cooler_compliance_logs_checked_at_index` (`checked_at`);

--
-- Indexes for table `cooler_dealers`
--
ALTER TABLE `cooler_dealers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cooler_dealers_contact_id_foreign` (`contact_id`),
  ADD KEY `cooler_dealers_created_by_foreign` (`created_by`),
  ADD KEY `cooler_dealers_business_id_status_index` (`business_id`,`status`),
  ADD KEY `cooler_dealers_business_id_channel_index` (`business_id`,`channel`),
  ADD KEY `cooler_dealers_agent_id_foreign` (`agent_id`);

--
-- Indexes for table `cooler_documents`
--
ALTER TABLE `cooler_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cooler_documents_documentable_type_documentable_id_index` (`documentable_type`,`documentable_id`),
  ADD KEY `cooler_documents_uploaded_by_foreign` (`uploaded_by`),
  ADD KEY `cooler_documents_verified_by_foreign` (`verified_by`),
  ADD KEY `cooler_documents_document_type_status_index` (`document_type`,`status`),
  ADD KEY `cooler_documents_expires_at_index` (`expires_at`);

--
-- Indexes for table `cooler_retrievals`
--
ALTER TABLE `cooler_retrievals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cooler_retrievals_agreement_id_foreign` (`agreement_id`),
  ADD KEY `cooler_retrievals_created_by_foreign` (`created_by`),
  ADD KEY `cooler_retrievals_business_id_status_index` (`business_id`,`status`),
  ADD KEY `cooler_retrievals_cooler_id_index` (`cooler_id`),
  ADD KEY `cooler_retrievals_dealer_id_index` (`dealer_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_groups`
--
ALTER TABLE `customer_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_groups_business_id_foreign` (`business_id`),
  ADD KEY `customer_groups_created_by_index` (`created_by`),
  ADD KEY `customer_groups_price_calculation_type_index` (`price_calculation_type`),
  ADD KEY `customer_groups_selling_price_group_id_index` (`selling_price_group_id`);

--
-- Indexes for table `dashboard_configurations`
--
ALTER TABLE `dashboard_configurations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dashboard_configurations_business_id_foreign` (`business_id`);

--
-- Indexes for table `dda_destruction_log`
--
ALTER TABLE `dda_destruction_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dda_destruction_log_business_id_foreign` (`business_id`),
  ADD KEY `dda_destruction_log_dda_drug_id_foreign` (`dda_drug_id`);

--
-- Indexes for table `dda_dispense_log`
--
ALTER TABLE `dda_dispense_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dda_dispense_log_business_id_foreign` (`business_id`),
  ADD KEY `dda_dispense_log_dda_drug_id_foreign` (`dda_drug_id`),
  ADD KEY `dda_dispense_log_prescription_id_foreign` (`prescription_id`);

--
-- Indexes for table `dda_drugs`
--
ALTER TABLE `dda_drugs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dda_prescriptions`
--
ALTER TABLE `dda_prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dda_prescriptions_business_id_foreign` (`business_id`);

--
-- Indexes for table `dda_stock_log`
--
ALTER TABLE `dda_stock_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dda_stock_log_business_id_foreign` (`business_id`),
  ADD KEY `dda_stock_log_dda_drug_id_foreign` (`dda_drug_id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discounts_business_id_index` (`business_id`),
  ADD KEY `discounts_brand_id_index` (`brand_id`),
  ADD KEY `discounts_category_id_index` (`category_id`),
  ADD KEY `discounts_location_id_index` (`location_id`),
  ADD KEY `discounts_priority_index` (`priority`),
  ADD KEY `discounts_spg_index` (`spg`);

--
-- Indexes for table `discount_variations`
--
ALTER TABLE `discount_variations`
  ADD KEY `discount_variations_discount_id_index` (`discount_id`),
  ADD KEY `discount_variations_variation_id_index` (`variation_id`);

--
-- Indexes for table `document_and_notes`
--
ALTER TABLE `document_and_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_and_notes_business_id_index` (`business_id`),
  ADD KEY `document_and_notes_notable_id_index` (`notable_id`),
  ADD KEY `document_and_notes_created_by_index` (`created_by`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_categories_business_id_foreign` (`business_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `followups`
--
ALTER TABLE `followups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `followups_business_id_index` (`business_id`),
  ADD KEY `followups_location_id_index` (`location_id`),
  ADD KEY `followups_status_index` (`status`),
  ADD KEY `followups_customer_phone_index` (`customer_phone`);

--
-- Indexes for table `group_sub_taxes`
--
ALTER TABLE `group_sub_taxes`
  ADD KEY `group_sub_taxes_group_tax_id_foreign` (`group_tax_id`),
  ADD KEY `group_sub_taxes_tax_id_foreign` (`tax_id`);

--
-- Indexes for table `hospital_admissions`
--
ALTER TABLE `hospital_admissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_admissions_business_id_foreign` (`business_id`),
  ADD KEY `hospital_admissions_patient_id_foreign` (`patient_id`),
  ADD KEY `hospital_admissions_bed_id_foreign` (`bed_id`),
  ADD KEY `hospital_admissions_admitted_by_foreign` (`admitted_by`);

--
-- Indexes for table `hospital_anc_visits`
--
ALTER TABLE `hospital_anc_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_anc_visits_pregnancy_profile_id_foreign` (`pregnancy_profile_id`),
  ADD KEY `hospital_anc_visits_doctor_id_foreign` (`doctor_id`);

--
-- Indexes for table `hospital_appointments`
--
ALTER TABLE `hospital_appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_appointments_business_id_foreign` (`business_id`),
  ADD KEY `hospital_appointments_patient_id_foreign` (`patient_id`),
  ADD KEY `hospital_appointments_doctor_id_foreign` (`doctor_id`),
  ADD KEY `hospital_appointments_created_by_foreign` (`created_by`);

--
-- Indexes for table `hospital_assets`
--
ALTER TABLE `hospital_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hospital_assets_asset_code_unique` (`asset_code`),
  ADD KEY `hospital_assets_business_id_foreign` (`business_id`);

--
-- Indexes for table `hospital_asset_maintenance`
--
ALTER TABLE `hospital_asset_maintenance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_asset_maintenance_asset_id_foreign` (`asset_id`);

--
-- Indexes for table `hospital_beds`
--
ALTER TABLE `hospital_beds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_beds_ward_id_foreign` (`ward_id`);

--
-- Indexes for table `hospital_bills`
--
ALTER TABLE `hospital_bills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hospital_bills_bill_number_unique` (`bill_number`),
  ADD KEY `hospital_bills_business_id_foreign` (`business_id`);

--
-- Indexes for table `hospital_consultations`
--
ALTER TABLE `hospital_consultations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_consultations_business_id_foreign` (`business_id`),
  ADD KEY `hospital_consultations_patient_id_foreign` (`patient_id`),
  ADD KEY `hospital_consultations_doctor_id_foreign` (`doctor_id`),
  ADD KEY `hospital_consultations_appointment_id_foreign` (`appointment_id`);

--
-- Indexes for table `hospital_daily_records`
--
ALTER TABLE `hospital_daily_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_daily_records_admission_id_foreign` (`admission_id`),
  ADD KEY `hospital_daily_records_recorded_by_foreign` (`recorded_by`);

--
-- Indexes for table `hospital_dental_procedures`
--
ALTER TABLE `hospital_dental_procedures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_dental_procedures_business_id_foreign` (`business_id`),
  ADD KEY `hospital_dental_procedures_patient_id_foreign` (`patient_id`),
  ADD KEY `hospital_dental_procedures_doctor_id_foreign` (`doctor_id`);

--
-- Indexes for table `hospital_dental_teeth`
--
ALTER TABLE `hospital_dental_teeth`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_dental_teeth_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `hospital_insurance_schemes`
--
ALTER TABLE `hospital_insurance_schemes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_insurance_schemes_insurer_id_foreign` (`insurer_id`);

--
-- Indexes for table `hospital_insurers`
--
ALTER TABLE `hospital_insurers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_insurers_business_id_foreign` (`business_id`);

--
-- Indexes for table `hospital_lab_requests`
--
ALTER TABLE `hospital_lab_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_lab_requests_business_id_foreign` (`business_id`),
  ADD KEY `hospital_lab_requests_patient_id_foreign` (`patient_id`),
  ADD KEY `hospital_lab_requests_doctor_id_foreign` (`doctor_id`),
  ADD KEY `hospital_lab_requests_test_id_foreign` (`test_id`),
  ADD KEY `hospital_lab_requests_lab_tech_id_foreign` (`lab_tech_id`);

--
-- Indexes for table `hospital_lab_tests`
--
ALTER TABLE `hospital_lab_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_lab_tests_business_id_foreign` (`business_id`);

--
-- Indexes for table `hospital_mortuary_records`
--
ALTER TABLE `hospital_mortuary_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_mortuary_records_business_id_foreign` (`business_id`),
  ADD KEY `hospital_mortuary_records_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `hospital_nursing_notes`
--
ALTER TABLE `hospital_nursing_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_nursing_notes_admission_id_foreign` (`admission_id`),
  ADD KEY `hospital_nursing_notes_nurse_id_foreign` (`nurse_id`);

--
-- Indexes for table `hospital_physio_plans`
--
ALTER TABLE `hospital_physio_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_physio_plans_business_id_foreign` (`business_id`),
  ADD KEY `hospital_physio_plans_patient_id_foreign` (`patient_id`),
  ADD KEY `hospital_physio_plans_doctor_id_foreign` (`doctor_id`);

--
-- Indexes for table `hospital_physio_sessions`
--
ALTER TABLE `hospital_physio_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_physio_sessions_plan_id_foreign` (`plan_id`),
  ADD KEY `hospital_physio_sessions_therapist_id_foreign` (`therapist_id`);

--
-- Indexes for table `hospital_pregnancy_profiles`
--
ALTER TABLE `hospital_pregnancy_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_pregnancy_profiles_business_id_foreign` (`business_id`),
  ADD KEY `hospital_pregnancy_profiles_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `hospital_prescriptions`
--
ALTER TABLE `hospital_prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_prescriptions_business_id_status_index` (`business_id`,`status`),
  ADD KEY `hospital_prescriptions_patient_id_index` (`patient_id`);

--
-- Indexes for table `hospital_queue`
--
ALTER TABLE `hospital_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_queue_business_id_foreign` (`business_id`),
  ADD KEY `hospital_queue_patient_id_foreign` (`patient_id`),
  ADD KEY `hospital_queue_assigned_to_foreign` (`assigned_to`);

--
-- Indexes for table `hospital_radiography_requests`
--
ALTER TABLE `hospital_radiography_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_radiography_requests_business_id_foreign` (`business_id`),
  ADD KEY `hospital_radiography_requests_patient_id_foreign` (`patient_id`),
  ADD KEY `hospital_radiography_requests_doctor_id_foreign` (`doctor_id`),
  ADD KEY `hospital_radiography_requests_test_id_foreign` (`test_id`),
  ADD KEY `hospital_radiography_requests_radiologist_id_foreign` (`radiologist_id`);

--
-- Indexes for table `hospital_radiography_tests`
--
ALTER TABLE `hospital_radiography_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_radiography_tests_business_id_foreign` (`business_id`);

--
-- Indexes for table `hospital_surgeries`
--
ALTER TABLE `hospital_surgeries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_surgeries_business_id_foreign` (`business_id`);

--
-- Indexes for table `hospital_theatres`
--
ALTER TABLE `hospital_theatres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_theatres_business_id_foreign` (`business_id`);

--
-- Indexes for table `hospital_theatre_bookings`
--
ALTER TABLE `hospital_theatre_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_theatre_bookings_business_id_foreign` (`business_id`),
  ADD KEY `hospital_theatre_bookings_patient_id_foreign` (`patient_id`),
  ADD KEY `hospital_theatre_bookings_surgery_id_foreign` (`surgery_id`),
  ADD KEY `hospital_theatre_bookings_theatre_id_foreign` (`theatre_id`),
  ADD KEY `hospital_theatre_bookings_surgeon_id_foreign` (`surgeon_id`),
  ADD KEY `hospital_theatre_bookings_anaesthetist_id_foreign` (`anaesthetist_id`);

--
-- Indexes for table `hospital_wards`
--
ALTER TABLE `hospital_wards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hospital_wards_business_id_foreign` (`business_id`);

--
-- Indexes for table `invoice_layouts`
--
ALTER TABLE `invoice_layouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_layouts_business_id_foreign` (`business_id`);

--
-- Indexes for table `invoice_schemes`
--
ALTER TABLE `invoice_schemes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_schemes_business_id_foreign` (`business_id`),
  ADD KEY `invoice_schemes_scheme_type_index` (`scheme_type`),
  ADD KEY `invoice_schemes_number_type_index` (`number_type`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_cards`
--
ALTER TABLE `job_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_cards_ref_no_unique` (`ref_no`),
  ADD KEY `job_cards_location_id_foreign` (`location_id`),
  ADD KEY `job_cards_contact_id_foreign` (`contact_id`),
  ADD KEY `job_cards_category_id_foreign` (`category_id`),
  ADD KEY `job_cards_template_id_foreign` (`template_id`),
  ADD KEY `job_cards_assigned_to_foreign` (`assigned_to`),
  ADD KEY `job_cards_completed_by_foreign` (`completed_by`),
  ADD KEY `job_cards_approved_by_foreign` (`approved_by`),
  ADD KEY `job_cards_created_by_foreign` (`created_by`),
  ADD KEY `job_cards_business_id_status_index` (`business_id`,`status`),
  ADD KEY `job_cards_business_id_contact_id_index` (`business_id`,`contact_id`),
  ADD KEY `job_cards_business_id_assigned_to_index` (`business_id`,`assigned_to`),
  ADD KEY `job_cards_business_id_due_date_index` (`business_id`,`due_date`),
  ADD KEY `job_cards_ref_no_index` (`ref_no`);

--
-- Indexes for table `job_categories`
--
ALTER TABLE `job_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_categories_parent_id_foreign` (`parent_id`),
  ADD KEY `job_categories_business_id_is_active_index` (`business_id`,`is_active`);

--
-- Indexes for table `job_checklists`
--
ALTER TABLE `job_checklists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_checklists_template_item_id_foreign` (`template_item_id`),
  ADD KEY `job_checklists_completed_by_foreign` (`completed_by`),
  ADD KEY `job_checklists_job_id_sort_order_index` (`job_id`,`sort_order`);

--
-- Indexes for table `job_images`
--
ALTER TABLE `job_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_images_job_log_id_foreign` (`job_log_id`),
  ADD KEY `job_images_created_by_foreign` (`created_by`),
  ADD KEY `job_images_job_id_created_at_index` (`job_id`,`created_at`);

--
-- Indexes for table `job_logs`
--
ALTER TABLE `job_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_logs_user_id_foreign` (`user_id`),
  ADD KEY `job_logs_created_by_foreign` (`created_by`),
  ADD KEY `job_logs_job_id_created_at_index` (`job_id`,`created_at`);

--
-- Indexes for table `job_notifications`
--
ALTER TABLE `job_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_notifications_job_id_foreign` (`job_id`),
  ADD KEY `job_notifications_created_by_foreign` (`created_by`),
  ADD KEY `job_notifications_user_id_is_read_index` (`user_id`,`is_read`),
  ADD KEY `job_notifications_business_id_created_at_index` (`business_id`,`created_at`),
  ADD KEY `job_notifications_contact_email_is_sent_index` (`contact_email`,`is_sent`);

--
-- Indexes for table `job_requisitions`
--
ALTER TABLE `job_requisitions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_requisitions_product_id_foreign` (`product_id`),
  ADD KEY `job_requisitions_approved_by_foreign` (`approved_by`),
  ADD KEY `job_requisitions_issued_by_foreign` (`issued_by`),
  ADD KEY `job_requisitions_created_by_foreign` (`created_by`),
  ADD KEY `job_requisitions_job_id_status_index` (`job_id`,`status`);

--
-- Indexes for table `job_signatures`
--
ALTER TABLE `job_signatures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_signatures_created_by_foreign` (`created_by`),
  ADD KEY `job_signatures_job_id_signature_type_index` (`job_id`,`signature_type`);

--
-- Indexes for table `job_templates`
--
ALTER TABLE `job_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_templates_category_id_foreign` (`category_id`),
  ADD KEY `job_templates_business_id_is_active_index` (`business_id`,`is_active`);

--
-- Indexes for table `job_template_items`
--
ALTER TABLE `job_template_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_template_items_job_template_id_sort_order_index` (`job_template_id`,`sort_order`);

--
-- Indexes for table `lost_sales`
--
ALTER TABLE `lost_sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lost_sales_business_id_foreign` (`business_id`),
  ADD KEY `lost_sales_location_id_foreign` (`location_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `media_business_id_index` (`business_id`),
  ADD KEY `media_uploaded_by_index` (`uploaded_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `mpesa_c2b_payments`
--
ALTER TABLE `mpesa_c2b_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mpesa_c2b_payments_trans_id_unique` (`trans_id`),
  ADD KEY `mpesa_c2b_payments_business_id_status_index` (`business_id`,`status`),
  ADD KEY `mpesa_c2b_payments_msisdn_index` (`msisdn`),
  ADD KEY `mpesa_c2b_payments_bill_ref_number_index` (`bill_ref_number`);

--
-- Indexes for table `mpesa_settings`
--
ALTER TABLE `mpesa_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mpesa_settings_business_id_unique` (`business_id`);

--
-- Indexes for table `mpesa_transactions`
--
ALTER TABLE `mpesa_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mpesa_transactions_transaction_id_foreign` (`transaction_id`),
  ADD KEY `mpesa_transactions_initiated_by_foreign` (`initiated_by`),
  ADD KEY `mpesa_transactions_business_id_status_index` (`business_id`,`status`),
  ADD KEY `mpesa_transactions_checkout_request_id_index` (`checkout_request_id`),
  ADD KEY `mpesa_transactions_mpesa_receipt_number_index` (`mpesa_receipt_number`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `notification_templates`
--
ALTER TABLE `notification_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_personal_access_clients_client_id_index` (`client_id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `parcels`
--
ALTER TABLE `parcels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `parcels_waybill_number_unique` (`waybill_number`),
  ADD KEY `parcels_business_id_foreign` (`business_id`),
  ADD KEY `parcels_origin_station_id_foreign` (`origin_station_id`),
  ADD KEY `parcels_destination_station_id_foreign` (`destination_station_id`),
  ADD KEY `parcels_route_id_foreign` (`route_id`),
  ADD KEY `parcels_booked_by_user_id_foreign` (`booked_by_user_id`);

--
-- Indexes for table `parcel_checkpoints`
--
ALTER TABLE `parcel_checkpoints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parcel_pricing_rules`
--
ALTER TABLE `parcel_pricing_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parcel_pricing_rules_route_id_foreign` (`route_id`);

--
-- Indexes for table `parcel_routes`
--
ALTER TABLE `parcel_routes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parcel_routes_business_id_foreign` (`business_id`),
  ADD KEY `parcel_routes_origin_station_id_foreign` (`origin_station_id`),
  ADD KEY `parcel_routes_destination_station_id_foreign` (`destination_station_id`);

--
-- Indexes for table `parcel_stations`
--
ALTER TABLE `parcel_stations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parcel_stations_business_id_foreign` (`business_id`);

--
-- Indexes for table `parcel_status_logs`
--
ALTER TABLE `parcel_status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parcel_status_logs_parcel_id_foreign` (`parcel_id`),
  ADD KEY `parcel_status_logs_station_id_foreign` (`station_id`),
  ADD KEY `parcel_status_logs_updated_by_user_id_foreign` (`updated_by_user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `patient_details`
--
ALTER TABLE `patient_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patient_details_uhid_number_unique` (`uhid_number`),
  ADD KEY `patient_details_contact_id_foreign` (`contact_id`),
  ADD KEY `patient_details_insurer_id_foreign` (`insurer_id`),
  ADD KEY `patient_details_insurance_scheme_id_foreign` (`insurance_scheme_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pesapal_settings`
--
ALTER TABLE `pesapal_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pesapal_settings_business_id_unique` (`business_id`);

--
-- Indexes for table `pesapal_transactions`
--
ALTER TABLE `pesapal_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pesapal_transactions_order_tracking_id_unique` (`order_tracking_id`),
  ADD KEY `pesapal_transactions_transaction_id_foreign` (`transaction_id`),
  ADD KEY `pesapal_transactions_initiated_by_foreign` (`initiated_by`),
  ADD KEY `pesapal_transactions_business_id_status_index` (`business_id`,`status`),
  ADD KEY `pesapal_transactions_merchant_reference_index` (`merchant_reference`);

--
-- Indexes for table `pos_orders`
--
ALTER TABLE `pos_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pos_orders_business_id_index` (`business_id`),
  ADD KEY `pos_orders_location_id_index` (`location_id`),
  ADD KEY `pos_orders_status_index` (`status`);

--
-- Indexes for table `pos_order_lines`
--
ALTER TABLE `pos_order_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pos_order_lines_order_id_index` (`order_id`);

--
-- Indexes for table `printers`
--
ALTER TABLE `printers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `printers_business_id_foreign` (`business_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_brand_id_foreign` (`brand_id`),
  ADD KEY `products_category_id_foreign` (`category_id`),
  ADD KEY `products_sub_category_id_foreign` (`sub_category_id`),
  ADD KEY `products_tax_foreign` (`tax`),
  ADD KEY `products_name_index` (`name`),
  ADD KEY `products_business_id_index` (`business_id`),
  ADD KEY `products_unit_id_index` (`unit_id`),
  ADD KEY `products_created_by_index` (`created_by`),
  ADD KEY `products_warranty_id_index` (`warranty_id`),
  ADD KEY `products_type_index` (`type`),
  ADD KEY `products_tax_type_index` (`tax_type`),
  ADD KEY `products_barcode_type_index` (`barcode_type`),
  ADD KEY `products_secondary_unit_id_index` (`secondary_unit_id`),
  ADD KEY `products_dda_drug_id_foreign` (`dda_drug_id`);

--
-- Indexes for table `product_locations`
--
ALTER TABLE `product_locations`
  ADD KEY `product_locations_product_id_index` (`product_id`),
  ADD KEY `product_locations_location_id_index` (`location_id`);

--
-- Indexes for table `product_racks`
--
ALTER TABLE `product_racks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_racks_business_id_index` (`business_id`),
  ADD KEY `product_racks_location_id_index` (`location_id`),
  ADD KEY `product_racks_product_id_index` (`product_id`);

--
-- Indexes for table `product_variations`
--
ALTER TABLE `product_variations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_variations_name_index` (`name`),
  ADD KEY `product_variations_product_id_index` (`product_id`);

--
-- Indexes for table `purchase_lines`
--
ALTER TABLE `purchase_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_lines_transaction_id_foreign` (`transaction_id`),
  ADD KEY `purchase_lines_product_id_foreign` (`product_id`),
  ADD KEY `purchase_lines_variation_id_foreign` (`variation_id`),
  ADD KEY `purchase_lines_tax_id_foreign` (`tax_id`),
  ADD KEY `purchase_lines_sub_unit_id_index` (`sub_unit_id`),
  ADD KEY `purchase_lines_lot_number_index` (`lot_number`);

--
-- Indexes for table `reference_counts`
--
ALTER TABLE `reference_counts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reference_counts_business_id_index` (`business_id`);

--
-- Indexes for table `res_product_modifier_sets`
--
ALTER TABLE `res_product_modifier_sets`
  ADD KEY `res_product_modifier_sets_modifier_set_id_foreign` (`modifier_set_id`);

--
-- Indexes for table `res_tables`
--
ALTER TABLE `res_tables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `res_tables_business_id_foreign` (`business_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `roles_business_id_foreign` (`business_id`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `saas_bundles`
--
ALTER TABLE `saas_bundles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `saas_bundles_slug_unique` (`slug`),
  ADD KEY `saas_bundles_business_type_index` (`business_type`);

--
-- Indexes for table `saas_bundle_features`
--
ALTER TABLE `saas_bundle_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saas_bundle_features_bundle_id_foreign` (`bundle_id`),
  ADD KEY `saas_bundle_features_feature_id_foreign` (`feature_id`);

--
-- Indexes for table `saas_enquiries`
--
ALTER TABLE `saas_enquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saas_features`
--
ALTER TABLE `saas_features`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `saas_features_key_unique` (`key`);

--
-- Indexes for table `saas_hosted_accounts`
--
ALTER TABLE `saas_hosted_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saas_hosted_accounts_business_id_foreign` (`business_id`);

--
-- Indexes for table `saas_invoices`
--
ALTER TABLE `saas_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `saas_invoices_invoice_no_unique` (`invoice_no`),
  ADD KEY `saas_invoices_subscription_id_foreign` (`subscription_id`),
  ADD KEY `saas_invoices_business_id_foreign` (`business_id`);

--
-- Indexes for table `saas_settings`
--
ALTER TABLE `saas_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `saas_settings_key_unique` (`key`);

--
-- Indexes for table `saas_subscriptions`
--
ALTER TABLE `saas_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saas_subscriptions_business_id_foreign` (`business_id`);

--
-- Indexes for table `saas_subscription_features`
--
ALTER TABLE `saas_subscription_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saas_subscription_features_subscription_id_foreign` (`subscription_id`),
  ADD KEY `saas_subscription_features_feature_id_foreign` (`feature_id`);

--
-- Indexes for table `selling_price_groups`
--
ALTER TABLE `selling_price_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `selling_price_groups_business_id_foreign` (`business_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD UNIQUE KEY `sessions_id_unique` (`id`);

--
-- Indexes for table `sms_logs`
--
ALTER TABLE `sms_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stocktake_lines`
--
ALTER TABLE `stocktake_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stocktake_lines_product_id_foreign` (`product_id`),
  ADD KEY `stocktake_lines_counted_by_foreign` (`counted_by`),
  ADD KEY `stocktake_lines_transaction_id_index` (`transaction_id`),
  ADD KEY `stocktake_lines_variation_id_index` (`variation_id`);

--
-- Indexes for table `stock_adjustment_lines`
--
ALTER TABLE `stock_adjustment_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_adjustment_lines_product_id_foreign` (`product_id`),
  ADD KEY `stock_adjustment_lines_variation_id_foreign` (`variation_id`),
  ADD KEY `stock_adjustment_lines_transaction_id_index` (`transaction_id`),
  ADD KEY `stock_adjustment_lines_lot_no_line_id_index` (`lot_no_line_id`);

--
-- Indexes for table `sync_conflicts`
--
ALTER TABLE `sync_conflicts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sync_conflicts_business_id_index` (`business_id`);

--
-- Indexes for table `sync_logs`
--
ALTER TABLE `sync_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sync_logs_business_id_index` (`business_id`),
  ADD KEY `sync_logs_sync_token_id_index` (`sync_token_id`);

--
-- Indexes for table `sync_tokens`
--
ALTER TABLE `sync_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sync_tokens_token_unique` (`token`),
  ADD KEY `sync_tokens_business_id_index` (`business_id`),
  ADD KEY `sync_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `system`
--
ALTER TABLE `system`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tax_rates`
--
ALTER TABLE `tax_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tax_rates_business_id_foreign` (`business_id`),
  ADD KEY `tax_rates_created_by_foreign` (`created_by`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transactions_offline_ref_unique` (`offline_ref`),
  ADD KEY `transactions_tax_id_foreign` (`tax_id`),
  ADD KEY `transactions_business_id_index` (`business_id`),
  ADD KEY `transactions_type_index` (`type`),
  ADD KEY `transactions_contact_id_index` (`contact_id`),
  ADD KEY `transactions_transaction_date_index` (`transaction_date`),
  ADD KEY `transactions_created_by_index` (`created_by`),
  ADD KEY `transactions_location_id_index` (`location_id`),
  ADD KEY `transactions_expense_for_foreign` (`expense_for`),
  ADD KEY `transactions_expense_category_id_index` (`expense_category_id`),
  ADD KEY `transactions_sub_type_index` (`sub_type`),
  ADD KEY `transactions_return_parent_id_index` (`return_parent_id`),
  ADD KEY `type` (`type`),
  ADD KEY `transactions_status_index` (`status`),
  ADD KEY `transactions_sub_status_index` (`sub_status`),
  ADD KEY `transactions_res_table_id_index` (`res_table_id`),
  ADD KEY `transactions_res_waiter_id_index` (`res_waiter_id`),
  ADD KEY `transactions_res_order_status_index` (`res_order_status`),
  ADD KEY `transactions_payment_status_index` (`payment_status`),
  ADD KEY `transactions_discount_type_index` (`discount_type`),
  ADD KEY `transactions_commission_agent_index` (`commission_agent`),
  ADD KEY `transactions_transfer_parent_id_index` (`transfer_parent_id`),
  ADD KEY `transactions_types_of_service_id_index` (`types_of_service_id`),
  ADD KEY `transactions_packing_charge_type_index` (`packing_charge_type`),
  ADD KEY `transactions_recur_parent_id_index` (`recur_parent_id`),
  ADD KEY `transactions_selling_price_group_id_index` (`selling_price_group_id`),
  ADD KEY `transactions_delivery_date_index` (`delivery_date`),
  ADD KEY `transactions_delivery_person_index` (`delivery_person`);

--
-- Indexes for table `transaction_payments`
--
ALTER TABLE `transaction_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_payments_transaction_id_foreign` (`transaction_id`),
  ADD KEY `transaction_payments_created_by_index` (`created_by`),
  ADD KEY `transaction_payments_parent_id_index` (`parent_id`),
  ADD KEY `transaction_payments_payment_type_index` (`payment_type`);

--
-- Indexes for table `transaction_sell_lines`
--
ALTER TABLE `transaction_sell_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_sell_lines_transaction_id_foreign` (`transaction_id`),
  ADD KEY `transaction_sell_lines_product_id_foreign` (`product_id`),
  ADD KEY `transaction_sell_lines_variation_id_foreign` (`variation_id`),
  ADD KEY `transaction_sell_lines_tax_id_foreign` (`tax_id`),
  ADD KEY `transaction_sell_lines_children_type_index` (`children_type`),
  ADD KEY `transaction_sell_lines_parent_sell_line_id_index` (`parent_sell_line_id`),
  ADD KEY `transaction_sell_lines_line_discount_type_index` (`line_discount_type`),
  ADD KEY `transaction_sell_lines_discount_id_index` (`discount_id`),
  ADD KEY `transaction_sell_lines_lot_no_line_id_index` (`lot_no_line_id`),
  ADD KEY `transaction_sell_lines_sub_unit_id_index` (`sub_unit_id`);

--
-- Indexes for table `transaction_sell_lines_purchase_lines`
--
ALTER TABLE `transaction_sell_lines_purchase_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sell_line_id` (`sell_line_id`),
  ADD KEY `stock_adjustment_line_id` (`stock_adjustment_line_id`),
  ADD KEY `purchase_line_id` (`purchase_line_id`);

--
-- Indexes for table `types_of_services`
--
ALTER TABLE `types_of_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `types_of_services_business_id_index` (`business_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `units_business_id_foreign` (`business_id`),
  ADD KEY `units_created_by_foreign` (`created_by`),
  ADD KEY `units_base_unit_id_index` (`base_unit_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD KEY `users_business_id_foreign` (`business_id`),
  ADD KEY `users_user_type_index` (`user_type`),
  ADD KEY `users_crm_contact_id_foreign` (`crm_contact_id`);

--
-- Indexes for table `user_contact_access`
--
ALTER TABLE `user_contact_access`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_contact_access_user_id_index` (`user_id`),
  ADD KEY `user_contact_access_contact_id_index` (`contact_id`);

--
-- Indexes for table `variations`
--
ALTER TABLE `variations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variations_product_id_foreign` (`product_id`),
  ADD KEY `variations_product_variation_id_foreign` (`product_variation_id`),
  ADD KEY `variations_name_index` (`name`),
  ADD KEY `variations_sub_sku_index` (`sub_sku`),
  ADD KEY `variations_variation_value_id_index` (`variation_value_id`);

--
-- Indexes for table `variation_group_prices`
--
ALTER TABLE `variation_group_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variation_group_prices_variation_id_foreign` (`variation_id`),
  ADD KEY `variation_group_prices_price_group_id_foreign` (`price_group_id`);

--
-- Indexes for table `variation_location_details`
--
ALTER TABLE `variation_location_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variation_location_details_location_id_foreign` (`location_id`),
  ADD KEY `variation_location_details_product_id_index` (`product_id`),
  ADD KEY `variation_location_details_product_variation_id_index` (`product_variation_id`),
  ADD KEY `variation_location_details_variation_id_index` (`variation_id`);

--
-- Indexes for table `variation_templates`
--
ALTER TABLE `variation_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variation_templates_business_id_foreign` (`business_id`);

--
-- Indexes for table `variation_value_templates`
--
ALTER TABLE `variation_value_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variation_value_templates_name_index` (`name`),
  ADD KEY `variation_value_templates_variation_template_id_index` (`variation_template_id`);

--
-- Indexes for table `warranties`
--
ALTER TABLE `warranties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warranties_business_id_index` (`business_id`),
  ADD KEY `warranties_duration_type_index` (`duration_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_transactions`
--
ALTER TABLE `account_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_types`
--
ALTER TABLE `account_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_decisions`
--
ALTER TABLE `approval_decisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_flows`
--
ALTER TABLE `approval_flows`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_flow_steps`
--
ALTER TABLE `approval_flow_steps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barcodes`
--
ALTER TABLE `barcodes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `business`
--
ALTER TABLE `business`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `business_locations`
--
ALTER TABLE `business_locations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cash_denominations`
--
ALTER TABLE `cash_denominations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_registers`
--
ALTER TABLE `cash_registers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cash_register_transactions`
--
ALTER TABLE `cash_register_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cooler_agreements`
--
ALTER TABLE `cooler_agreements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cooler_assets`
--
ALTER TABLE `cooler_assets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cooler_compliance_logs`
--
ALTER TABLE `cooler_compliance_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cooler_dealers`
--
ALTER TABLE `cooler_dealers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cooler_documents`
--
ALTER TABLE `cooler_documents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cooler_retrievals`
--
ALTER TABLE `cooler_retrievals`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `customer_groups`
--
ALTER TABLE `customer_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dashboard_configurations`
--
ALTER TABLE `dashboard_configurations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dda_destruction_log`
--
ALTER TABLE `dda_destruction_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dda_dispense_log`
--
ALTER TABLE `dda_dispense_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dda_drugs`
--
ALTER TABLE `dda_drugs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dda_prescriptions`
--
ALTER TABLE `dda_prescriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dda_stock_log`
--
ALTER TABLE `dda_stock_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_and_notes`
--
ALTER TABLE `document_and_notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followups`
--
ALTER TABLE `followups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_admissions`
--
ALTER TABLE `hospital_admissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_anc_visits`
--
ALTER TABLE `hospital_anc_visits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_appointments`
--
ALTER TABLE `hospital_appointments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_assets`
--
ALTER TABLE `hospital_assets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_asset_maintenance`
--
ALTER TABLE `hospital_asset_maintenance`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_beds`
--
ALTER TABLE `hospital_beds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_bills`
--
ALTER TABLE `hospital_bills`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_consultations`
--
ALTER TABLE `hospital_consultations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_daily_records`
--
ALTER TABLE `hospital_daily_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_dental_procedures`
--
ALTER TABLE `hospital_dental_procedures`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_dental_teeth`
--
ALTER TABLE `hospital_dental_teeth`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_insurance_schemes`
--
ALTER TABLE `hospital_insurance_schemes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_insurers`
--
ALTER TABLE `hospital_insurers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_lab_requests`
--
ALTER TABLE `hospital_lab_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_lab_tests`
--
ALTER TABLE `hospital_lab_tests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_mortuary_records`
--
ALTER TABLE `hospital_mortuary_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_nursing_notes`
--
ALTER TABLE `hospital_nursing_notes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_physio_plans`
--
ALTER TABLE `hospital_physio_plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_physio_sessions`
--
ALTER TABLE `hospital_physio_sessions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_pregnancy_profiles`
--
ALTER TABLE `hospital_pregnancy_profiles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_prescriptions`
--
ALTER TABLE `hospital_prescriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_queue`
--
ALTER TABLE `hospital_queue`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_radiography_requests`
--
ALTER TABLE `hospital_radiography_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_radiography_tests`
--
ALTER TABLE `hospital_radiography_tests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_surgeries`
--
ALTER TABLE `hospital_surgeries`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_theatres`
--
ALTER TABLE `hospital_theatres`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_theatre_bookings`
--
ALTER TABLE `hospital_theatre_bookings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospital_wards`
--
ALTER TABLE `hospital_wards`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_layouts`
--
ALTER TABLE `invoice_layouts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `invoice_schemes`
--
ALTER TABLE `invoice_schemes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_cards`
--
ALTER TABLE `job_cards`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_categories`
--
ALTER TABLE `job_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_checklists`
--
ALTER TABLE `job_checklists`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_images`
--
ALTER TABLE `job_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_logs`
--
ALTER TABLE `job_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_notifications`
--
ALTER TABLE `job_notifications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_requisitions`
--
ALTER TABLE `job_requisitions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_signatures`
--
ALTER TABLE `job_signatures`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_templates`
--
ALTER TABLE `job_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_template_items`
--
ALTER TABLE `job_template_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lost_sales`
--
ALTER TABLE `lost_sales`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=396;

--
-- AUTO_INCREMENT for table `mpesa_c2b_payments`
--
ALTER TABLE `mpesa_c2b_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mpesa_settings`
--
ALTER TABLE `mpesa_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mpesa_transactions`
--
ALTER TABLE `mpesa_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_templates`
--
ALTER TABLE `notification_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parcels`
--
ALTER TABLE `parcels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parcel_checkpoints`
--
ALTER TABLE `parcel_checkpoints`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parcel_pricing_rules`
--
ALTER TABLE `parcel_pricing_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parcel_routes`
--
ALTER TABLE `parcel_routes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parcel_stations`
--
ALTER TABLE `parcel_stations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parcel_status_logs`
--
ALTER TABLE `parcel_status_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_details`
--
ALTER TABLE `patient_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `pesapal_settings`
--
ALTER TABLE `pesapal_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pesapal_transactions`
--
ALTER TABLE `pesapal_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pos_orders`
--
ALTER TABLE `pos_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pos_order_lines`
--
ALTER TABLE `pos_order_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `printers`
--
ALTER TABLE `printers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `product_racks`
--
ALTER TABLE `product_racks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variations`
--
ALTER TABLE `product_variations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `purchase_lines`
--
ALTER TABLE `purchase_lines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `reference_counts`
--
ALTER TABLE `reference_counts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `res_tables`
--
ALTER TABLE `res_tables`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `saas_bundles`
--
ALTER TABLE `saas_bundles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saas_bundle_features`
--
ALTER TABLE `saas_bundle_features`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saas_enquiries`
--
ALTER TABLE `saas_enquiries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saas_features`
--
ALTER TABLE `saas_features`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saas_hosted_accounts`
--
ALTER TABLE `saas_hosted_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saas_invoices`
--
ALTER TABLE `saas_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saas_settings`
--
ALTER TABLE `saas_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `saas_subscriptions`
--
ALTER TABLE `saas_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saas_subscription_features`
--
ALTER TABLE `saas_subscription_features`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `selling_price_groups`
--
ALTER TABLE `selling_price_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_logs`
--
ALTER TABLE `sms_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stocktake_lines`
--
ALTER TABLE `stocktake_lines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_adjustment_lines`
--
ALTER TABLE `stock_adjustment_lines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sync_conflicts`
--
ALTER TABLE `sync_conflicts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sync_logs`
--
ALTER TABLE `sync_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sync_tokens`
--
ALTER TABLE `sync_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system`
--
ALTER TABLE `system`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tax_rates`
--
ALTER TABLE `tax_rates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `transaction_payments`
--
ALTER TABLE `transaction_payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `transaction_sell_lines`
--
ALTER TABLE `transaction_sell_lines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `transaction_sell_lines_purchase_lines`
--
ALTER TABLE `transaction_sell_lines_purchase_lines`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `types_of_services`
--
ALTER TABLE `types_of_services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_contact_access`
--
ALTER TABLE `user_contact_access`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variations`
--
ALTER TABLE `variations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `variation_group_prices`
--
ALTER TABLE `variation_group_prices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variation_location_details`
--
ALTER TABLE `variation_location_details`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `variation_templates`
--
ALTER TABLE `variation_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `variation_value_templates`
--
ALTER TABLE `variation_value_templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warranties`
--
ALTER TABLE `warranties`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approval_decisions`
--
ALTER TABLE `approval_decisions`
  ADD CONSTRAINT `approval_decisions_approval_id_foreign` FOREIGN KEY (`approval_id`) REFERENCES `approvals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `approval_flow_steps`
--
ALTER TABLE `approval_flow_steps`
  ADD CONSTRAINT `approval_flow_steps_approval_flow_id_foreign` FOREIGN KEY (`approval_flow_id`) REFERENCES `approval_flows` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `barcodes`
--
ALTER TABLE `barcodes`
  ADD CONSTRAINT `barcodes_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `brands`
--
ALTER TABLE `brands`
  ADD CONSTRAINT `brands_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `brands_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `business`
--
ALTER TABLE `business`
  ADD CONSTRAINT `business_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`),
  ADD CONSTRAINT `business_default_sales_tax_foreign` FOREIGN KEY (`default_sales_tax`) REFERENCES `tax_rates` (`id`),
  ADD CONSTRAINT `business_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `business_locations`
--
ALTER TABLE `business_locations`
  ADD CONSTRAINT `business_locations_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `business_locations_invoice_layout_id_foreign` FOREIGN KEY (`invoice_layout_id`) REFERENCES `invoice_layouts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `business_locations_invoice_scheme_id_foreign` FOREIGN KEY (`invoice_scheme_id`) REFERENCES `invoice_schemes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cash_registers`
--
ALTER TABLE `cash_registers`
  ADD CONSTRAINT `cash_registers_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cash_registers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cash_register_transactions`
--
ALTER TABLE `cash_register_transactions`
  ADD CONSTRAINT `cash_register_transactions_cash_register_id_foreign` FOREIGN KEY (`cash_register_id`) REFERENCES `cash_registers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contacts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cooler_agreements`
--
ALTER TABLE `cooler_agreements`
  ADD CONSTRAINT `cooler_agreements_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cooler_agreements_cooler_id_foreign` FOREIGN KEY (`cooler_id`) REFERENCES `cooler_assets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cooler_agreements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cooler_agreements_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `cooler_dealers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cooler_assets`
--
ALTER TABLE `cooler_assets`
  ADD CONSTRAINT `cooler_assets_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cooler_assets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cooler_assets_current_dealer_id_foreign` FOREIGN KEY (`current_dealer_id`) REFERENCES `cooler_dealers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cooler_compliance_logs`
--
ALTER TABLE `cooler_compliance_logs`
  ADD CONSTRAINT `cooler_compliance_logs_checked_by_foreign` FOREIGN KEY (`checked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cooler_compliance_logs_cooler_id_foreign` FOREIGN KEY (`cooler_id`) REFERENCES `cooler_assets` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cooler_compliance_logs_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `cooler_dealers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cooler_dealers`
--
ALTER TABLE `cooler_dealers`
  ADD CONSTRAINT `cooler_dealers_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cooler_dealers_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cooler_dealers_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cooler_dealers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cooler_documents`
--
ALTER TABLE `cooler_documents`
  ADD CONSTRAINT `cooler_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cooler_documents_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cooler_retrievals`
--
ALTER TABLE `cooler_retrievals`
  ADD CONSTRAINT `cooler_retrievals_agreement_id_foreign` FOREIGN KEY (`agreement_id`) REFERENCES `cooler_agreements` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cooler_retrievals_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cooler_retrievals_cooler_id_foreign` FOREIGN KEY (`cooler_id`) REFERENCES `cooler_assets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cooler_retrievals_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cooler_retrievals_dealer_id_foreign` FOREIGN KEY (`dealer_id`) REFERENCES `cooler_dealers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_groups`
--
ALTER TABLE `customer_groups`
  ADD CONSTRAINT `customer_groups_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dashboard_configurations`
--
ALTER TABLE `dashboard_configurations`
  ADD CONSTRAINT `dashboard_configurations_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dda_destruction_log`
--
ALTER TABLE `dda_destruction_log`
  ADD CONSTRAINT `dda_destruction_log_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dda_destruction_log_dda_drug_id_foreign` FOREIGN KEY (`dda_drug_id`) REFERENCES `dda_drugs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dda_dispense_log`
--
ALTER TABLE `dda_dispense_log`
  ADD CONSTRAINT `dda_dispense_log_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dda_dispense_log_dda_drug_id_foreign` FOREIGN KEY (`dda_drug_id`) REFERENCES `dda_drugs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `dda_dispense_log_prescription_id_foreign` FOREIGN KEY (`prescription_id`) REFERENCES `dda_prescriptions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dda_prescriptions`
--
ALTER TABLE `dda_prescriptions`
  ADD CONSTRAINT `dda_prescriptions_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dda_stock_log`
--
ALTER TABLE `dda_stock_log`
  ADD CONSTRAINT `dda_stock_log_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dda_stock_log_dda_drug_id_foreign` FOREIGN KEY (`dda_drug_id`) REFERENCES `dda_drugs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD CONSTRAINT `expense_categories_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `group_sub_taxes`
--
ALTER TABLE `group_sub_taxes`
  ADD CONSTRAINT `group_sub_taxes_group_tax_id_foreign` FOREIGN KEY (`group_tax_id`) REFERENCES `tax_rates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_sub_taxes_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `tax_rates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_admissions`
--
ALTER TABLE `hospital_admissions`
  ADD CONSTRAINT `hospital_admissions_admitted_by_foreign` FOREIGN KEY (`admitted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_admissions_bed_id_foreign` FOREIGN KEY (`bed_id`) REFERENCES `hospital_beds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_admissions_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_admissions_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_anc_visits`
--
ALTER TABLE `hospital_anc_visits`
  ADD CONSTRAINT `hospital_anc_visits_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_anc_visits_pregnancy_profile_id_foreign` FOREIGN KEY (`pregnancy_profile_id`) REFERENCES `hospital_pregnancy_profiles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_appointments`
--
ALTER TABLE `hospital_appointments`
  ADD CONSTRAINT `hospital_appointments_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_appointments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_appointments_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hospital_appointments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_assets`
--
ALTER TABLE `hospital_assets`
  ADD CONSTRAINT `hospital_assets_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_asset_maintenance`
--
ALTER TABLE `hospital_asset_maintenance`
  ADD CONSTRAINT `hospital_asset_maintenance_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `hospital_assets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_beds`
--
ALTER TABLE `hospital_beds`
  ADD CONSTRAINT `hospital_beds_ward_id_foreign` FOREIGN KEY (`ward_id`) REFERENCES `hospital_wards` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_bills`
--
ALTER TABLE `hospital_bills`
  ADD CONSTRAINT `hospital_bills_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_consultations`
--
ALTER TABLE `hospital_consultations`
  ADD CONSTRAINT `hospital_consultations_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `hospital_appointments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hospital_consultations_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_consultations_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_consultations_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_daily_records`
--
ALTER TABLE `hospital_daily_records`
  ADD CONSTRAINT `hospital_daily_records_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `hospital_admissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_daily_records_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_dental_procedures`
--
ALTER TABLE `hospital_dental_procedures`
  ADD CONSTRAINT `hospital_dental_procedures_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_dental_procedures_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_dental_procedures_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_dental_teeth`
--
ALTER TABLE `hospital_dental_teeth`
  ADD CONSTRAINT `hospital_dental_teeth_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_insurance_schemes`
--
ALTER TABLE `hospital_insurance_schemes`
  ADD CONSTRAINT `hospital_insurance_schemes_insurer_id_foreign` FOREIGN KEY (`insurer_id`) REFERENCES `hospital_insurers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_insurers`
--
ALTER TABLE `hospital_insurers`
  ADD CONSTRAINT `hospital_insurers_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_lab_requests`
--
ALTER TABLE `hospital_lab_requests`
  ADD CONSTRAINT `hospital_lab_requests_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_lab_requests_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_lab_requests_lab_tech_id_foreign` FOREIGN KEY (`lab_tech_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hospital_lab_requests_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_lab_requests_test_id_foreign` FOREIGN KEY (`test_id`) REFERENCES `hospital_lab_tests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_lab_tests`
--
ALTER TABLE `hospital_lab_tests`
  ADD CONSTRAINT `hospital_lab_tests_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_mortuary_records`
--
ALTER TABLE `hospital_mortuary_records`
  ADD CONSTRAINT `hospital_mortuary_records_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_mortuary_records_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hospital_nursing_notes`
--
ALTER TABLE `hospital_nursing_notes`
  ADD CONSTRAINT `hospital_nursing_notes_admission_id_foreign` FOREIGN KEY (`admission_id`) REFERENCES `hospital_admissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_nursing_notes_nurse_id_foreign` FOREIGN KEY (`nurse_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_physio_plans`
--
ALTER TABLE `hospital_physio_plans`
  ADD CONSTRAINT `hospital_physio_plans_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_physio_plans_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_physio_plans_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_physio_sessions`
--
ALTER TABLE `hospital_physio_sessions`
  ADD CONSTRAINT `hospital_physio_sessions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `hospital_physio_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_physio_sessions_therapist_id_foreign` FOREIGN KEY (`therapist_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_pregnancy_profiles`
--
ALTER TABLE `hospital_pregnancy_profiles`
  ADD CONSTRAINT `hospital_pregnancy_profiles_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_pregnancy_profiles_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_prescriptions`
--
ALTER TABLE `hospital_prescriptions`
  ADD CONSTRAINT `hospital_prescriptions_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_prescriptions_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_queue`
--
ALTER TABLE `hospital_queue`
  ADD CONSTRAINT `hospital_queue_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hospital_queue_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_queue_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_radiography_requests`
--
ALTER TABLE `hospital_radiography_requests`
  ADD CONSTRAINT `hospital_radiography_requests_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_radiography_requests_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_radiography_requests_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_radiography_requests_radiologist_id_foreign` FOREIGN KEY (`radiologist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hospital_radiography_requests_test_id_foreign` FOREIGN KEY (`test_id`) REFERENCES `hospital_radiography_tests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_radiography_tests`
--
ALTER TABLE `hospital_radiography_tests`
  ADD CONSTRAINT `hospital_radiography_tests_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_surgeries`
--
ALTER TABLE `hospital_surgeries`
  ADD CONSTRAINT `hospital_surgeries_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_theatres`
--
ALTER TABLE `hospital_theatres`
  ADD CONSTRAINT `hospital_theatres_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_theatre_bookings`
--
ALTER TABLE `hospital_theatre_bookings`
  ADD CONSTRAINT `hospital_theatre_bookings_anaesthetist_id_foreign` FOREIGN KEY (`anaesthetist_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hospital_theatre_bookings_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_theatre_bookings_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_theatre_bookings_surgeon_id_foreign` FOREIGN KEY (`surgeon_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_theatre_bookings_surgery_id_foreign` FOREIGN KEY (`surgery_id`) REFERENCES `hospital_surgeries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hospital_theatre_bookings_theatre_id_foreign` FOREIGN KEY (`theatre_id`) REFERENCES `hospital_theatres` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hospital_wards`
--
ALTER TABLE `hospital_wards`
  ADD CONSTRAINT `hospital_wards_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_layouts`
--
ALTER TABLE `invoice_layouts`
  ADD CONSTRAINT `invoice_layouts_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_schemes`
--
ALTER TABLE `invoice_schemes`
  ADD CONSTRAINT `invoice_schemes_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_cards`
--
ALTER TABLE `job_cards`
  ADD CONSTRAINT `job_cards_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_cards_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_cards_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_cards_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `job_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_cards_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_cards_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_cards_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_cards_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `business_locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_cards_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `job_templates` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `job_categories`
--
ALTER TABLE `job_categories`
  ADD CONSTRAINT `job_categories_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `job_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_checklists`
--
ALTER TABLE `job_checklists`
  ADD CONSTRAINT `job_checklists_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_checklists_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_checklists_template_item_id_foreign` FOREIGN KEY (`template_item_id`) REFERENCES `job_template_items` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `job_images`
--
ALTER TABLE `job_images`
  ADD CONSTRAINT `job_images_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_images_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_images_job_log_id_foreign` FOREIGN KEY (`job_log_id`) REFERENCES `job_logs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `job_logs`
--
ALTER TABLE `job_logs`
  ADD CONSTRAINT `job_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_logs_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `job_notifications`
--
ALTER TABLE `job_notifications`
  ADD CONSTRAINT `job_notifications_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_notifications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_notifications_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `job_requisitions`
--
ALTER TABLE `job_requisitions`
  ADD CONSTRAINT `job_requisitions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_requisitions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_requisitions_issued_by_foreign` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_requisitions_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_requisitions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `job_signatures`
--
ALTER TABLE `job_signatures`
  ADD CONSTRAINT `job_signatures_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_signatures_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `job_cards` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_templates`
--
ALTER TABLE `job_templates`
  ADD CONSTRAINT `job_templates_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_templates_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `job_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `job_template_items`
--
ALTER TABLE `job_template_items`
  ADD CONSTRAINT `job_template_items_job_template_id_foreign` FOREIGN KEY (`job_template_id`) REFERENCES `job_templates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lost_sales`
--
ALTER TABLE `lost_sales`
  ADD CONSTRAINT `lost_sales_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lost_sales_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `business_locations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mpesa_c2b_payments`
--
ALTER TABLE `mpesa_c2b_payments`
  ADD CONSTRAINT `mpesa_c2b_payments_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mpesa_settings`
--
ALTER TABLE `mpesa_settings`
  ADD CONSTRAINT `mpesa_settings_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mpesa_transactions`
--
ALTER TABLE `mpesa_transactions`
  ADD CONSTRAINT `mpesa_transactions_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mpesa_transactions_initiated_by_foreign` FOREIGN KEY (`initiated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `mpesa_transactions_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `parcels`
--
ALTER TABLE `parcels`
  ADD CONSTRAINT `parcels_booked_by_user_id_foreign` FOREIGN KEY (`booked_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parcels_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parcels_destination_station_id_foreign` FOREIGN KEY (`destination_station_id`) REFERENCES `parcel_stations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parcels_origin_station_id_foreign` FOREIGN KEY (`origin_station_id`) REFERENCES `parcel_stations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parcels_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `parcel_routes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `parcel_pricing_rules`
--
ALTER TABLE `parcel_pricing_rules`
  ADD CONSTRAINT `parcel_pricing_rules_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `parcel_routes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parcel_routes`
--
ALTER TABLE `parcel_routes`
  ADD CONSTRAINT `parcel_routes_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parcel_routes_destination_station_id_foreign` FOREIGN KEY (`destination_station_id`) REFERENCES `parcel_stations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parcel_routes_origin_station_id_foreign` FOREIGN KEY (`origin_station_id`) REFERENCES `parcel_stations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parcel_stations`
--
ALTER TABLE `parcel_stations`
  ADD CONSTRAINT `parcel_stations_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parcel_status_logs`
--
ALTER TABLE `parcel_status_logs`
  ADD CONSTRAINT `parcel_status_logs_parcel_id_foreign` FOREIGN KEY (`parcel_id`) REFERENCES `parcels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parcel_status_logs_station_id_foreign` FOREIGN KEY (`station_id`) REFERENCES `parcel_stations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `parcel_status_logs_updated_by_user_id_foreign` FOREIGN KEY (`updated_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_details`
--
ALTER TABLE `patient_details`
  ADD CONSTRAINT `patient_details_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_details_insurance_scheme_id_foreign` FOREIGN KEY (`insurance_scheme_id`) REFERENCES `hospital_insurance_schemes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `patient_details_insurer_id_foreign` FOREIGN KEY (`insurer_id`) REFERENCES `hospital_insurers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pesapal_settings`
--
ALTER TABLE `pesapal_settings`
  ADD CONSTRAINT `pesapal_settings_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pesapal_transactions`
--
ALTER TABLE `pesapal_transactions`
  ADD CONSTRAINT `pesapal_transactions_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesapal_transactions_initiated_by_foreign` FOREIGN KEY (`initiated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pesapal_transactions_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pos_order_lines`
--
ALTER TABLE `pos_order_lines`
  ADD CONSTRAINT `pos_order_lines_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `pos_orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `printers`
--
ALTER TABLE `printers`
  ADD CONSTRAINT `printers_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_dda_drug_id_foreign` FOREIGN KEY (`dda_drug_id`) REFERENCES `dda_drugs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_sub_category_id_foreign` FOREIGN KEY (`sub_category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_tax_foreign` FOREIGN KEY (`tax`) REFERENCES `tax_rates` (`id`),
  ADD CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variations`
--
ALTER TABLE `product_variations`
  ADD CONSTRAINT `product_variations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_lines`
--
ALTER TABLE `purchase_lines`
  ADD CONSTRAINT `purchase_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_lines_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `tax_rates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_lines_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_lines_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `res_product_modifier_sets`
--
ALTER TABLE `res_product_modifier_sets`
  ADD CONSTRAINT `res_product_modifier_sets_modifier_set_id_foreign` FOREIGN KEY (`modifier_set_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `res_tables`
--
ALTER TABLE `res_tables`
  ADD CONSTRAINT `res_tables_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saas_bundle_features`
--
ALTER TABLE `saas_bundle_features`
  ADD CONSTRAINT `saas_bundle_features_bundle_id_foreign` FOREIGN KEY (`bundle_id`) REFERENCES `saas_bundles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saas_bundle_features_feature_id_foreign` FOREIGN KEY (`feature_id`) REFERENCES `saas_features` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saas_hosted_accounts`
--
ALTER TABLE `saas_hosted_accounts`
  ADD CONSTRAINT `saas_hosted_accounts_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saas_invoices`
--
ALTER TABLE `saas_invoices`
  ADD CONSTRAINT `saas_invoices_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saas_invoices_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `saas_subscriptions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `saas_subscriptions`
--
ALTER TABLE `saas_subscriptions`
  ADD CONSTRAINT `saas_subscriptions_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saas_subscription_features`
--
ALTER TABLE `saas_subscription_features`
  ADD CONSTRAINT `saas_subscription_features_feature_id_foreign` FOREIGN KEY (`feature_id`) REFERENCES `saas_features` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saas_subscription_features_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `saas_subscriptions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `selling_price_groups`
--
ALTER TABLE `selling_price_groups`
  ADD CONSTRAINT `selling_price_groups_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stocktake_lines`
--
ALTER TABLE `stocktake_lines`
  ADD CONSTRAINT `stocktake_lines_counted_by_foreign` FOREIGN KEY (`counted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stocktake_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stocktake_lines_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stocktake_lines_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_adjustment_lines`
--
ALTER TABLE `stock_adjustment_lines`
  ADD CONSTRAINT `stock_adjustment_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_adjustment_lines_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_adjustment_lines_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sync_conflicts`
--
ALTER TABLE `sync_conflicts`
  ADD CONSTRAINT `sync_conflicts_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sync_logs`
--
ALTER TABLE `sync_logs`
  ADD CONSTRAINT `sync_logs_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sync_tokens`
--
ALTER TABLE `sync_tokens`
  ADD CONSTRAINT `sync_tokens_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sync_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tax_rates`
--
ALTER TABLE `tax_rates`
  ADD CONSTRAINT `tax_rates_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tax_rates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_expense_for_foreign` FOREIGN KEY (`expense_for`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `business_locations` (`id`),
  ADD CONSTRAINT `transactions_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `tax_rates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_payments`
--
ALTER TABLE `transaction_payments`
  ADD CONSTRAINT `transaction_payments_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_sell_lines`
--
ALTER TABLE `transaction_sell_lines`
  ADD CONSTRAINT `transaction_sell_lines_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_sell_lines_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `tax_rates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_sell_lines_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_sell_lines_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `units_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_crm_contact_id_foreign` FOREIGN KEY (`crm_contact_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variations`
--
ALTER TABLE `variations`
  ADD CONSTRAINT `variations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `variations_product_variation_id_foreign` FOREIGN KEY (`product_variation_id`) REFERENCES `product_variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variation_group_prices`
--
ALTER TABLE `variation_group_prices`
  ADD CONSTRAINT `variation_group_prices_price_group_id_foreign` FOREIGN KEY (`price_group_id`) REFERENCES `selling_price_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `variation_group_prices_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variation_location_details`
--
ALTER TABLE `variation_location_details`
  ADD CONSTRAINT `variation_location_details_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `business_locations` (`id`),
  ADD CONSTRAINT `variation_location_details_variation_id_foreign` FOREIGN KEY (`variation_id`) REFERENCES `variations` (`id`);

--
-- Constraints for table `variation_templates`
--
ALTER TABLE `variation_templates`
  ADD CONSTRAINT `variation_templates_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `variation_value_templates`
--
ALTER TABLE `variation_value_templates`
  ADD CONSTRAINT `variation_value_templates_variation_template_id_foreign` FOREIGN KEY (`variation_template_id`) REFERENCES `variation_templates` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
