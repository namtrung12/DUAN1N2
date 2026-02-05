-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 06, 2025 lúc 11:30 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `du_an1`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `label` varchar(100) DEFAULT 'Nhà',
  `receiver_name` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `ward` varchar(100) DEFAULT NULL,
  `detail` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `label`, `receiver_name`, `phone`, `province`, `district`, `ward`, `detail`, `is_default`, `created_at`) VALUES
(3, 12, 'Nhà', 'Đào DU', '0984430546', 'Thanh hoá', 'Hậu Lộc', 'Đa Lộc', 'số 102', 1, '2025-11-18 11:28:44'),
(4, 6, 'Nhà', 'Vương Lâm', '0984430546', 'Thanh hoá', 'Hậu Lộc', 'Đa Lộc', '1', 1, '2025-11-19 07:38:31'),
(5, 17, 'Nhà', 'Đào DU', '0984430546', 'Thanh hoá', 'Hậu Lộc', 'Đa Lộc', 'so 33', 1, '2025-11-21 14:16:33'),
(6, 18, 'Nhà', 'Đào Du', '0984430546', 'Thanh hoá', 'TP Thanh Hóa', 'Quảng Thắng', 'adhad', 1, '2025-11-25 15:18:21'),
(7, 19, 'Nhà', 'Linh dep trai', '0333044840', 'thanh hoa', 'thanh hoa', 'o.quảng thành', '337b', 1, '2025-12-02 09:10:19'),
(8, 20, 'Nhà', 'Trịnh Chiến', '0985795608', 'Thanh Hóa', 'Yên Định', 'Định Hưng', 'Số 04, khu dân cư số 2', 1, '2025-12-02 09:13:51'),
(9, 21, 'Nhà', 'Linh dep trai', '0333044840', 'thanh hoa', 'thanh hoa', 'o.quảng thành', '333', 1, '2025-12-02 09:48:19'),
(10, 23, 'Nhà', 'c', '0333044840', 'thanh hoa', 'thanh hoa', 'o.quảng thành', 'c', 1, '2025-12-03 09:01:49'),
(11, 24, 'Nhà', 'eeee', '0393561314', 'thanh hoa ', 'phong xa', 'thanh dhu', 'fsdiufss', 1, '2025-12-03 10:12:08'),
(12, 25, 'Nhà', 'Lê Phương Hà', '0123456789', 'Thanh Hóa', 'Thanh Hóa', 'Thanh Hóa', 'hhdhdhhdhdhdhdh', 1, '2025-12-03 10:14:02'),
(13, 26, 'HẸ HẸ HẸ HẸ', 'HẸ HẸ HẸ HẸ', '0987654321', 'HÚ HÚ', 'HẸ HẸ', 'HI HI', 'BÙ LU BÙ LA', 1, '2025-12-03 14:42:50');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `size` varchar(10) DEFAULT NULL,
  `sugar_level` varchar(10) DEFAULT NULL,
  `ice_level` varchar(10) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `size`, `sugar_level`, `ice_level`, `note`, `updated_at`) VALUES
(94, 17, 5, 6, 'L', '100', '100', NULL, '2025-12-04 15:45:58'),
(95, 17, 6, 8, 'S', '100', '100', NULL, '2025-12-04 15:45:50'),
(96, 17, 7, 10, 'M', '100', '100', NULL, '2025-12-04 15:48:52'),
(97, 17, 2, 46, 'L', '100', '100', NULL, '2025-12-04 15:51:18');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_item_toppings`
--

CREATE TABLE `cart_item_toppings` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `topping_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_item_toppings`
--

INSERT INTO `cart_item_toppings` (`id`, `cart_id`, `topping_id`) VALUES
(122, 96, 5),
(123, 96, 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Trà sữa', 'tra-sua', '2025-11-12 15:17:24'),
(2, 'Cà phê', 'ca-phe', '2025-11-12 15:17:24'),
(3, 'Nước ép', 'nuoc-ep', '2025-11-12 15:17:24'),
(4, 'Trà', 'tra', '2025-11-19 07:35:26');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('percent','fixed') DEFAULT 'fixed',
  `value` int(11) NOT NULL COMMENT 'percent (e.g., 10) or fixed amount in VND',
  `max_discount` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `min_order` int(11) DEFAULT 0,
  `usage_limit` int(11) DEFAULT 1,
  `used_count` int(11) DEFAULT 0,
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `required_rank` enum('bronze','silver','gold','diamond') DEFAULT NULL COMMENT 'Rank tối thiểu để sử dụng',
  `point_cost` int(11) DEFAULT 0 COMMENT 'Số điểm cần để đổi (0 = không cần đổi)',
  `is_redeemable` tinyint(1) DEFAULT 0 COMMENT '1 = Có thể đổi bằng điểm',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Bảng mã giảm giá - usage_limit áp dụng cho cả mã thường và mã đổi điểm';

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `type`, `value`, `max_discount`, `description`, `min_order`, `usage_limit`, `used_count`, `starts_at`, `expires_at`, `status`, `required_rank`, `point_cost`, `is_redeemable`, `created_at`) VALUES
(1, 'WELCOME10', 'percent', 10, 15000.00, '', 0, 100, 21, '2025-11-12 15:17:00', '2025-12-12 15:17:00', 1, NULL, 0, 0, '2025-11-12 15:17:24'),
(3, 'BRONZE10', 'percent', 15, 20000.00, '', 50000, 0, 4, '2025-11-24 13:49:00', '2025-12-25 13:06:00', 1, 'bronze', 0, 0, '2025-11-25 13:06:22'),
(4, 'SILVER15', 'percent', 20, 35000.00, '', 100000, 0, 4, '2025-11-24 13:49:00', '2025-12-25 13:06:00', 1, 'silver', 0, 0, '2025-11-25 13:06:22'),
(5, 'GOLD20', 'percent', 25, 50000.00, '', 150000, 0, 1, '2025-11-24 13:49:00', '2025-12-25 13:06:00', 1, 'gold', 0, 0, '2025-11-25 13:06:22'),
(6, 'DIAMOND25', 'percent', 30, 70000.00, 'Tri ân thành viên', 200000, 0, 0, '2025-11-24 13:49:00', '2025-12-25 13:06:00', 1, 'diamond', 0, 0, '2025-11-25 13:06:22'),
(8, 'POINT100', 'fixed', 100000, NULL, NULL, 250000, 100, 0, '2025-11-24 13:49:00', '2026-01-24 13:06:00', 1, NULL, 300, 1, '2025-11-25 13:06:22'),
(16, 'CD8386', 'percent', 25, 50000.00, '', 150000, 100, 0, '2025-11-24 12:00:00', '2025-12-24 12:00:00', 1, NULL, 100, 1, '2025-11-25 17:58:37'),
(17, 'OPENING15', 'percent', 15, 25000.00, '', 0, 100, 0, '2025-11-25 08:00:00', '2025-12-25 08:00:00', 1, NULL, 50, 1, '2025-11-26 07:46:48'),
(20, '123123666', 'percent', 100, 80000.00, '', 100000, 3, 0, '2025-12-02 14:54:00', '2025-12-05 14:54:00', 1, 'diamond', 10, 1, '2025-12-03 14:54:05');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loyalty_points`
--

CREATE TABLE `loyalty_points` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_points` int(11) DEFAULT 0,
  `monthly_points` int(11) DEFAULT 0 COMMENT 'Điểm tháng hiện tại',
  `lifetime_points` int(11) DEFAULT 0,
  `level` enum('bronze','silver','gold','diamond') DEFAULT 'bronze',
  `current_month` varchar(7) DEFAULT NULL COMMENT 'YYYY-MM format',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `loyalty_points`
--

INSERT INTO `loyalty_points` (`id`, `user_id`, `total_points`, `monthly_points`, `lifetime_points`, `level`, `current_month`, `updated_at`) VALUES
(4, 6, 1630, 278, 1940, 'bronze', '2025-12', '2025-12-03 15:24:25'),
(10, 17, 251, 40, 351, 'bronze', '2025-12', '2025-12-03 15:23:14'),
(28, 18, 182, 52, 183, '', '2025-12', '2025-12-06 15:53:14'),
(38, 19, 18, 36, 18, 'bronze', '2025-12', '2025-12-02 09:11:20'),
(39, 20, 1173, 1778, 1173, 'silver', '2025-12', '2025-12-03 08:49:27'),
(40, 21, 6682, 6700, 6682, '', '2025-12', '2025-12-03 15:23:01'),
(53, 26, 1185, 2970, 1485, 'gold', '2025-12', '2025-12-04 07:44:33'),
(54, 23, 47, 77, 47, 'bronze', '2025-12', '2025-12-03 15:24:02'),
(57, 25, 47, 77, 47, 'bronze', '2025-12', '2025-12-03 15:23:57'),
(58, 24, 32, 64, 32, 'bronze', '2025-12', '2025-12-03 15:23:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loyalty_point_spend`
--

CREATE TABLE `loyalty_point_spend` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `points` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loyalty_rewards`
--

CREATE TABLE `loyalty_rewards` (
  `id` int(11) NOT NULL,
  `reward_name` varchar(200) NOT NULL,
  `point_cost` int(11) NOT NULL,
  `reward_type` enum('voucher','product','discount') DEFAULT 'voucher',
  `value` varchar(100) DEFAULT NULL,
  `expires_in_days` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `loyalty_rewards`
--

INSERT INTO `loyalty_rewards` (`id`, `reward_name`, `point_cost`, `reward_type`, `value`, `expires_in_days`, `status`, `created_at`) VALUES
(1, 'Voucher giảm 20k', 200, 'voucher', '20000', 30, 1, '2025-11-12 15:17:24'),
(2, 'Free topping', 50, 'product', 'free_topping', 30, 1, '2025-11-12 15:17:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loyalty_transactions`
--

CREATE TABLE `loyalty_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `type` enum('earn','redeem') NOT NULL,
  `points` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `loyalty_transactions`
--

INSERT INTO `loyalty_transactions` (`id`, `user_id`, `order_id`, `type`, `points`, `description`, `created_at`) VALUES
(1, 6, NULL, 'earn', 4, 'Earn from order #4', '2025-11-22 10:46:14'),
(2, 6, NULL, 'earn', 231, 'Earn from order #6', '2025-11-24 08:43:38'),
(3, 6, NULL, 'redeem', 200, 'Đổi phần thưởng: Voucher giảm 20k', '2025-11-24 08:44:23'),
(4, 6, NULL, 'earn', 5, 'Earn from order #7', '2025-11-24 08:45:42'),
(5, 6, NULL, 'earn', 5, 'Earn from order #9', '2025-11-24 08:51:54'),
(6, 6, NULL, 'earn', 231, 'Earn from order #8', '2025-11-24 08:51:59'),
(7, 6, NULL, 'earn', 3, 'Earn from order #11', '2025-11-25 09:36:10'),
(8, 17, NULL, 'earn', 4, 'Earn from order #13', '2025-11-25 09:50:03'),
(9, 6, NULL, 'earn', 5, 'Earn from order #12', '2025-11-25 09:50:09'),
(10, 17, NULL, 'earn', 6, 'Earn from order #15', '2025-11-25 10:12:43'),
(11, 17, NULL, 'earn', 6, 'Earn from order #16', '2025-11-25 10:12:47'),
(12, 17, 3, 'earn', 10, 'Earn from order #3', '2025-11-25 10:42:58'),
(13, 6, NULL, 'redeem', 100, 'Đổi mã giảm giá: POINT50', '2025-11-25 13:24:02'),
(14, 6, 23, 'earn', 47, 'Tích điểm từ đơn hàng #23', '2025-11-25 14:40:59'),
(15, 6, 22, 'earn', 58, 'Tích điểm từ đơn hàng #22', '2025-11-25 14:41:01'),
(16, 6, 21, 'earn', 92, 'Tích điểm từ đơn hàng #21', '2025-11-25 14:41:02'),
(17, 6, 20, 'earn', 45, 'Tích điểm từ đơn hàng #20', '2025-11-25 14:41:04'),
(18, 6, 19, 'earn', 28, 'Tích điểm từ đơn hàng #19', '2025-11-25 14:41:06'),
(19, 6, 18, 'earn', 42, 'Tích điểm từ đơn hàng #18', '2025-11-25 14:41:08'),
(20, 6, 16, 'earn', 42, 'Tích điểm từ đơn hàng #16', '2025-11-25 14:41:11'),
(21, 6, 6, 'earn', 76, 'Tích điểm từ đơn hàng #6', '2025-11-25 14:41:14'),
(22, 17, 2, 'earn', 47, 'Tích điểm từ đơn hàng #2', '2025-11-25 14:41:24'),
(23, 17, 1, 'earn', 54, 'Tích điểm từ đơn hàng #1', '2025-11-25 14:41:28'),
(24, 6, 5, 'earn', 52, 'Tích điểm từ đơn hàng #5', '2025-11-25 14:41:33'),
(25, 6, 25, 'earn', 65, 'Tích điểm từ đơn hàng #25', '2025-11-25 14:50:44'),
(26, 6, 24, 'earn', 47, 'Tích điểm từ đơn hàng #24', '2025-11-25 14:50:46'),
(27, 18, 32, 'earn', 32, 'Tích điểm từ đơn hàng #32', '2025-11-26 10:40:39'),
(28, 18, NULL, 'redeem', 1, 'Đổi mã giảm giá: POINT10', '2025-11-26 10:46:32'),
(29, 18, 40, 'earn', 26, 'Tích điểm từ đơn hàng #40', '2025-11-27 13:06:53'),
(30, 18, 34, 'earn', 27, 'Tích điểm từ đơn hàng #34', '2025-11-27 14:33:17'),
(31, 18, 41, 'earn', 26, 'Tích điểm từ đơn hàng #41', '2025-11-27 14:34:20'),
(32, 17, 31, 'earn', 60, 'Tích điểm từ đơn hàng #31', '2025-11-27 14:34:34'),
(33, 6, 29, 'earn', 64, 'Tích điểm từ đơn hàng #29', '2025-11-27 14:34:49'),
(34, 6, 44, 'earn', 54, 'Tích điểm từ đơn hàng #44', '2025-11-27 17:17:31'),
(35, 17, 45, 'earn', 21, 'Tích điểm từ đơn hàng #45', '2025-11-27 17:18:21'),
(36, 6, 46, 'earn', 466, 'Tích điểm từ đơn hàng #46', '2025-11-28 08:37:02'),
(37, 17, 47, 'earn', 103, 'Tích điểm từ đơn hàng #47', '2025-11-28 10:13:04'),
(38, 19, 49, 'earn', 18, 'Tích điểm từ đơn hàng #49', '2025-12-02 09:11:20'),
(39, 20, 50, 'earn', 605, 'Tích điểm từ đơn hàng #50', '2025-12-02 09:14:45'),
(40, 21, 51, 'earn', 18, 'Tích điểm từ đơn hàng #51', '2025-12-02 14:36:35'),
(41, 6, 48, 'earn', 101, 'Tích điểm từ đơn hàng #48', '2025-12-02 14:36:53'),
(42, 20, 52, 'earn', 568, 'Tích điểm từ đơn hàng #52', '2025-12-03 08:49:27'),
(43, 6, 61, 'earn', 17, 'Tích điểm từ đơn hàng #61', '2025-12-03 09:14:40'),
(44, 6, 60, 'earn', 17, 'Tích điểm từ đơn hàng #60', '2025-12-03 09:14:51'),
(45, 6, 59, 'earn', 22, 'Tích điểm từ đơn hàng #59', '2025-12-03 09:15:12'),
(46, 6, 58, 'earn', 20, 'Tích điểm từ đơn hàng #58', '2025-12-03 09:15:15'),
(47, 21, 54, 'earn', 2772, 'Tích điểm từ đơn hàng #54', '2025-12-03 09:16:22'),
(48, 21, 53, 'earn', 1099, 'Tích điểm từ đơn hàng #53', '2025-12-03 09:16:27'),
(49, 17, 67, 'earn', 20, 'Tích điểm từ đơn hàng #67', '2025-12-03 14:43:36'),
(50, 17, NULL, 'redeem', 100, 'Đổi mã giảm giá: CD8386', '2025-12-03 14:45:24'),
(51, 6, NULL, 'redeem', 10, 'Đổi mã giảm giá: 123123666', '2025-12-03 14:56:20'),
(52, 21, 72, 'earn', 201, 'Tích điểm từ đơn hàng #72', '2025-12-03 15:22:00'),
(53, 21, 71, 'earn', 357, 'Tích điểm từ đơn hàng #71', '2025-12-03 15:22:09'),
(54, 6, 70, 'earn', 54, 'Tích điểm từ đơn hàng #70', '2025-12-03 15:22:20'),
(55, 26, 69, 'earn', 1485, 'Tích điểm từ đơn hàng #69', '2025-12-03 15:22:31'),
(56, 23, 55, 'earn', 30, 'Tích điểm từ đơn hàng #55', '2025-12-03 15:22:45'),
(57, 21, 63, 'earn', 2235, 'Tích điểm từ đơn hàng #63', '2025-12-03 15:23:01'),
(58, 17, 68, 'earn', 20, 'Tích điểm từ đơn hàng #68', '2025-12-03 15:23:14'),
(59, 25, 64, 'earn', 30, 'Tích điểm từ đơn hàng #64', '2025-12-03 15:23:39'),
(60, 24, 66, 'earn', 32, 'Tích điểm từ đơn hàng #66', '2025-12-03 15:23:51'),
(61, 25, 65, 'earn', 17, 'Tích điểm từ đơn hàng #65', '2025-12-03 15:23:57'),
(62, 23, 56, 'earn', 17, 'Tích điểm từ đơn hàng #56', '2025-12-03 15:24:02'),
(63, 6, 27, 'earn', 47, 'Tích điểm từ đơn hàng #27', '2025-12-03 15:24:25'),
(64, 26, NULL, 'redeem', 300, 'Đổi mã giảm giá: POINT100', '2025-12-04 07:44:33'),
(65, 18, 78, 'earn', 26, 'Tích điểm từ đơn hàng #78', '2025-12-06 15:33:53'),
(66, 18, 79, 'earn', 26, 'Tích điểm từ đơn hàng #79', '2025-12-06 15:53:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loyalty_user_rewards`
--

CREATE TABLE `loyalty_user_rewards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reward_id` int(11) NOT NULL,
  `code` varchar(100) DEFAULT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  `expires_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `loyalty_user_rewards`
--

INSERT INTO `loyalty_user_rewards` (`id`, `user_id`, `reward_id`, `code`, `is_used`, `expires_at`, `created_at`) VALUES
(1, 6, 1, 'RW4F34239D', 0, '2025-12-24 02:44:23', '2025-11-24 08:44:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `order_id`, `is_read`, `created_at`) VALUES
(1, 18, 'order_delivering', 'Đơn hàng đang được giao', 'Đơn hàng #000079 đang được giao đến bạn. Vui lòng chú ý điện thoại!', 79, 1, '2025-12-06 08:52:44'),
(2, 18, 'order_cancelled', 'Đơn hàng đã bị hủy', 'Đơn hàng #000080 đã bị hủy. Lý do: Hết nguyên liệu rồi', 80, 1, '2025-12-06 08:54:04'),
(3, 18, 'order_cancelled', 'Đơn hàng đã bị hủy', 'Đơn hàng #000081 đã bị hủy. Lý do: không muốn bán', 81, 1, '2025-12-06 08:57:45'),
(4, 18, 'order_cancelled', 'Đơn hàng đã bị hủy', 'Đơn hàng #000082 đã bị hủy. Lý do: Thích', 82, 1, '2025-12-06 08:59:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `shipping_zone_id` int(11) DEFAULT NULL,
  `subtotal` int(11) NOT NULL,
  `shipping_fee` int(11) DEFAULT 0,
  `discount` int(11) DEFAULT 0,
  `total` int(11) NOT NULL,
  `payment_method` enum('cod','vnpay','momo','card','wallet') DEFAULT 'cod',
  `status` enum('pending','processing','preparing','shipped','delivering','completed','cancelled') DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `vnpay_transaction_id` varchar(50) DEFAULT NULL,
  `note` varchar(500) DEFAULT NULL,
  `cancel_reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `address_id`, `coupon_id`, `shipping_zone_id`, `subtotal`, `shipping_fee`, `discount`, `total`, `payment_method`, `status`, `payment_status`, `vnpay_transaction_id`, `note`, `cancel_reason`, `created_at`, `updated_at`) VALUES
(1, 17, 5, NULL, NULL, 98000, 10000, 0, 108000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-11-25 10:20:52', '2025-11-27 12:26:35'),
(2, 17, 5, NULL, NULL, 85000, 10000, 0, 95000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-11-25 10:25:09', '2025-11-27 12:26:35'),
(3, 17, 5, NULL, NULL, 90000, 10000, 0, 100000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-11-25 10:33:06', '2025-11-27 12:26:35'),
(4, 6, 4, NULL, NULL, 30000, 10000, 0, 40000, 'cod', 'cancelled', 'pending', NULL, NULL, NULL, '2025-11-25 12:29:55', '2025-11-25 12:32:48'),
(5, 6, 4, NULL, NULL, 90000, 15000, 0, 105000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-11-25 12:33:03', '2025-11-27 12:26:35'),
(6, 6, 4, NULL, NULL, 188000, 15000, 50000, 153000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-11-25 13:49:35', '2025-11-27 12:26:35'),
(16, 6, 4, NULL, NULL, 120000, 15000, 50000, 85000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-11-25 14:23:46', '2025-11-27 12:26:35'),
(18, 6, 4, NULL, NULL, 120000, 15000, 50000, 85000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-11-25 14:26:35', '2025-11-27 12:26:35'),
(19, 6, 4, NULL, NULL, 42000, 15000, 0, 57000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-11-25 14:28:25', '2025-11-27 12:26:35'),
(20, 6, 4, NULL, NULL, 126000, 15000, 50000, 91000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-11-25 14:31:24', '2025-11-27 12:26:35'),
(21, 6, 4, NULL, NULL, 170000, 15000, 0, 185000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-11-25 14:38:47', '2025-11-27 12:26:35'),
(22, 6, 4, 4, NULL, 120000, 15000, 18000, 117000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-11-25 14:39:02', '2025-11-27 12:26:35'),
(23, 6, 4, NULL, NULL, 129000, 15000, 50000, 94000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-11-25 14:39:18', '2025-11-27 12:26:35'),
(24, 6, 4, NULL, NULL, 129000, 15000, 50000, 94000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-11-25 14:45:35', '2025-11-27 12:26:35'),
(25, 6, 4, 3, NULL, 129000, 15000, 12900, 131100, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-11-25 14:50:30', '2025-11-27 12:26:35'),
(26, 18, 6, 1, NULL, 108000, 15000, 10800, 112200, 'cod', 'cancelled', 'pending', NULL, NULL, NULL, '2025-11-25 15:18:37', '2025-11-27 12:35:39'),
(27, 6, 4, 4, NULL, 100000, 15000, 20000, 95000, 'wallet', 'completed', 'pending', NULL, NULL, NULL, '2025-11-25 17:59:36', '2025-12-03 15:24:25'),
(28, 6, 4, 4, NULL, 126000, 15000, 25200, 115800, 'wallet', 'cancelled', 'pending', NULL, NULL, NULL, '2025-11-26 07:24:18', '2025-11-26 07:24:39'),
(29, 6, 4, 5, NULL, 153000, 15000, 38250, 129750, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-11-26 08:21:45', '2025-11-27 14:34:49'),
(30, 6, 4, 4, NULL, 109000, 15000, 21800, 102200, 'wallet', 'cancelled', 'pending', NULL, NULL, NULL, '2025-11-26 08:22:57', '2025-11-26 08:23:07'),
(31, 17, 5, 1, NULL, 118000, 15000, 11800, 121200, 'cod', 'completed', 'pending', NULL, NULL, NULL, '2025-11-26 10:30:38', '2025-11-27 14:34:34'),
(32, 18, 6, 1, 1, 60000, 10000, 6000, 64000, 'wallet', 'completed', 'pending', NULL, NULL, NULL, '2025-11-26 10:36:45', '2025-11-26 10:40:39'),
(33, 18, 6, NULL, 1, 510000, 10000, 1020000, -500000, 'wallet', 'cancelled', 'pending', NULL, NULL, NULL, '2025-11-26 10:48:42', '2025-11-27 12:34:38'),
(34, 18, 6, 1, 1, 51000, 10000, 5100, 55900, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-11-27 12:03:27', '2025-11-27 14:33:17'),
(35, 18, 6, 1, 1, 60000, 10000, 6000, 64000, 'wallet', 'cancelled', 'pending', NULL, NULL, NULL, '2025-11-27 12:36:09', '2025-11-27 12:37:52'),
(39, 18, 6, 1, 1, 51000, 10000, 5100, 55900, 'cod', 'cancelled', 'pending', NULL, NULL, NULL, '2025-11-27 12:51:47', '2025-11-27 12:56:34'),
(40, 18, 6, 1, 1, 47000, 10000, 4700, 52300, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-11-27 12:56:04', '2025-11-27 13:06:53'),
(41, 18, 6, 1, 1, 47000, 10000, 4700, 52300, 'cod', 'completed', 'pending', NULL, NULL, NULL, '2025-11-27 14:16:57', '2025-11-27 14:34:20'),
(44, 6, 4, NULL, NULL, 94000, 15000, 0, 109000, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-11-27 17:15:07', '2025-11-27 17:17:31'),
(45, 17, 5, 1, NULL, 30000, 15000, 3000, 42000, 'cod', 'completed', 'pending', NULL, NULL, NULL, '2025-11-27 17:17:53', '2025-11-27 17:18:21'),
(46, 6, 4, NULL, NULL, 918000, 15000, 0, 933000, 'vnpay', 'completed', 'paid', '15307554', NULL, NULL, '2025-11-28 08:35:32', '2025-11-28 08:37:02'),
(47, 17, 5, 3, NULL, 216000, 15000, 25000, 206000, 'cod', 'completed', 'pending', NULL, NULL, NULL, '2025-11-28 10:12:43', '2025-11-28 10:13:04'),
(48, 6, 4, NULL, NULL, 188000, 15000, 0, 203000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-12-02 07:45:09', '2025-12-02 14:36:53'),
(49, 19, 7, 1, NULL, 25000, 15000, 2500, 37500, 'cod', 'completed', 'pending', NULL, NULL, NULL, '2025-12-02 09:10:29', '2025-12-02 09:11:20'),
(50, 20, 8, 1, NULL, 1210000, 15000, 15000, 1210000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-12-02 09:14:00', '2025-12-02 09:14:45'),
(51, 21, 9, 1, NULL, 25000, 15000, 2500, 37500, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-12-02 09:48:26', '2025-12-02 14:36:35'),
(52, 20, 8, NULL, NULL, 1122000, 15000, 0, 1137000, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-12-03 08:48:08', '2025-12-03 08:49:27'),
(53, 21, 9, NULL, NULL, 2184000, 15000, 0, 2199000, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-12-03 08:54:30', '2025-12-03 09:16:27'),
(54, 21, 9, 1, NULL, 5544000, 15000, 15000, 5544000, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-12-03 08:55:35', '2025-12-03 09:16:22'),
(55, 23, 10, NULL, NULL, 45000, 15000, 0, 60000, 'momo', 'completed', 'pending', NULL, NULL, NULL, '2025-12-03 09:01:56', '2025-12-03 15:22:45'),
(56, 23, 10, NULL, NULL, 20000, 15000, 0, 35000, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-12-03 09:05:13', '2025-12-03 15:24:02'),
(57, 6, 4, NULL, NULL, 30000, 15000, 0, 45000, 'cod', 'cancelled', 'pending', NULL, NULL, NULL, '2025-12-03 09:06:32', '2025-12-03 09:06:35'),
(58, 6, 4, NULL, NULL, 25000, 15000, 0, 40000, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-12-03 09:06:54', '2025-12-03 09:15:15'),
(59, 6, 4, NULL, NULL, 30000, 15000, 0, 45000, 'momo', 'completed', 'pending', NULL, NULL, NULL, '2025-12-03 09:07:30', '2025-12-03 09:15:12'),
(60, 6, 4, NULL, NULL, 20000, 15000, 0, 35000, 'momo', 'completed', 'pending', NULL, NULL, NULL, '2025-12-03 09:09:43', '2025-12-03 09:14:51'),
(61, 6, 4, NULL, NULL, 20000, 15000, 0, 35000, 'momo', 'completed', 'pending', NULL, NULL, NULL, '2025-12-03 09:10:28', '2025-12-03 09:14:40'),
(62, 21, 9, NULL, NULL, 100000, 15000, 0, 115000, 'cod', 'cancelled', 'pending', NULL, NULL, NULL, '2025-12-03 10:08:54', '2025-12-03 10:10:49'),
(63, 21, 9, NULL, NULL, 4455000, 15000, 0, 4470000, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-12-03 10:09:50', '2025-12-03 15:23:01'),
(64, 25, 12, 1, NULL, 50000, 15000, 5000, 60000, 'vnpay', 'completed', 'pending', NULL, NULL, NULL, '2025-12-03 10:14:13', '2025-12-03 15:23:39'),
(65, 25, 12, NULL, NULL, 20000, 15000, 0, 35000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-12-03 10:15:01', '2025-12-03 15:23:57'),
(66, 24, 11, NULL, NULL, 50000, 15000, 0, 65000, 'cod', 'completed', 'paid', NULL, NULL, NULL, '2025-12-03 10:43:40', '2025-12-03 15:23:51'),
(67, 17, 5, NULL, NULL, 26000, 15000, 0, 41000, 'cod', 'completed', 'pending', NULL, NULL, NULL, '2025-12-03 14:39:25', '2025-12-03 14:43:36'),
(68, 17, 5, NULL, NULL, 26000, 15000, 0, 41000, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-12-03 14:40:25', '2025-12-03 15:23:14'),
(69, 26, 13, 1, NULL, 2970000, 15000, 15000, 2970000, 'cod', 'completed', 'paid', NULL, 'THẰNG LÀO CÓ TIỀN, THÌ NẠP TIỀN VÀO DONATE CHO TAO\r\nÍT THÌ 5 QUẢ TRẤNG, NHỀU THÌ 10 TÊN LỬA, CHÚNG MÀY HIỂU CHƯA', NULL, '2025-12-03 14:45:21', '2025-12-03 15:22:31'),
(70, 6, 4, 3, NULL, 110000, 15000, 16500, 108500, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-12-03 14:51:14', '2025-12-03 15:22:20'),
(71, 21, 9, NULL, NULL, 700000, 15000, 0, 715000, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-12-03 14:54:10', '2025-12-03 15:22:09'),
(72, 21, 9, NULL, NULL, 387000, 15000, 0, 402000, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-12-03 14:57:06', '2025-12-03 15:22:00'),
(73, 6, 4, NULL, NULL, 124000, 15000, 0, 139000, 'cod', 'cancelled', 'pending', NULL, NULL, NULL, '2025-12-03 14:57:14', '2025-12-03 14:59:19'),
(74, 21, 9, 4, NULL, 135000, 15000, 27000, 123000, 'wallet', 'cancelled', 'paid', NULL, NULL, NULL, '2025-12-03 15:17:34', '2025-12-03 15:21:47'),
(75, 17, 5, 3, NULL, 120000, 15000, 18000, 117000, 'cod', '', 'pending', NULL, NULL, NULL, '2025-12-04 15:13:23', '2025-12-06 15:28:49'),
(76, 6, 4, NULL, NULL, 225000, 15000, 0, 240000, 'wallet', '', 'paid', NULL, NULL, NULL, '2025-12-04 16:38:37', '2025-12-06 15:28:43'),
(77, 18, 6, 1, 1, 45000, 10000, 4500, 50500, 'wallet', '', 'paid', NULL, NULL, NULL, '2025-12-06 15:27:52', '2025-12-06 15:28:32'),
(78, 18, 6, 1, 1, 47000, 10000, 4700, 52300, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-12-06 15:33:16', '2025-12-06 15:33:53'),
(79, 18, 6, 1, 1, 47000, 10000, 4700, 52300, 'wallet', 'completed', 'paid', NULL, NULL, NULL, '2025-12-06 15:52:22', '2025-12-06 15:53:14'),
(80, 18, 6, 1, 1, 47000, 10000, 4700, 52300, 'cod', 'cancelled', 'pending', NULL, NULL, 'Hết nguyên liệu rồi', '2025-12-06 15:53:28', '2025-12-06 15:54:04'),
(81, 18, 6, 1, 1, 47000, 10000, 4700, 52300, 'wallet', 'cancelled', 'paid', NULL, NULL, 'không muốn bán', '2025-12-06 15:57:15', '2025-12-06 15:57:45'),
(82, 18, 6, 1, 1, 200000, 10000, 15000, 195000, 'wallet', 'cancelled', 'paid', NULL, NULL, 'Thích', '2025-12-06 15:58:43', '2025-12-06 15:59:03');

--
-- Bẫy `orders`
--
DELIMITER $$
CREATE TRIGGER `trg_order_completed` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
  DECLARE v_points_per_vnd INT DEFAULT 2000;
  DECLARE v_points INT DEFAULT 0;
  DECLARE v_user INT;
  DECLARE v_current_month VARCHAR(7);
  DECLARE v_user_month VARCHAR(7);
  DECLARE v_monthly_points INT;

  IF NEW.status = 'completed' AND OLD.status <> 'completed' THEN
    SET v_user = NEW.user_id;
    SET v_current_month = DATE_FORMAT(NOW(), '%Y-%m');
    
    -- Tính điểm
    SET v_points = FLOOR(NEW.total / v_points_per_vnd);

    IF v_points > 0 THEN
      -- Ghi log tích điểm
      INSERT INTO loyalty_transactions (user_id, order_id, type, points, description)
      VALUES (v_user, NEW.id, 'earn', v_points, CONCAT('Tích điểm từ đơn hàng #', NEW.id));

      -- Tạo hoặc lấy thông tin loyalty
      INSERT INTO loyalty_points (user_id, total_points, lifetime_points, monthly_points, current_month, level, updated_at)
      VALUES (v_user, v_points, v_points, v_points, v_current_month, 'bronze', NOW())
      ON DUPLICATE KEY UPDATE
        total_points = total_points + v_points,
        lifetime_points = lifetime_points + v_points,
        updated_at = NOW();

      -- Lấy tháng hiện tại của user
      SELECT current_month, monthly_points INTO v_user_month, v_monthly_points
      FROM loyalty_points WHERE user_id = v_user;

      -- Kiểm tra nếu sang tháng mới -> Reset monthly_points
      IF v_user_month IS NULL OR v_user_month <> v_current_month THEN
        -- Reset điểm tháng về điểm vừa tích
        UPDATE loyalty_points
        SET 
          monthly_points = v_points,
          current_month = v_current_month
        WHERE user_id = v_user;
        
        SET v_monthly_points = v_points;
      ELSE
        -- Cộng điểm vào tháng hiện tại
        UPDATE loyalty_points
        SET monthly_points = monthly_points + v_points
        WHERE user_id = v_user;
        
        SET v_monthly_points = v_monthly_points + v_points;
      END IF;

      -- Cập nhật cấp độ dựa trên điểm THÁNG HIỆN TẠI
      UPDATE loyalty_points
      SET level = CASE
          WHEN v_monthly_points >= 5000 THEN 'platinum'
          WHEN v_monthly_points >= 2500 THEN 'gold'
          WHEN v_monthly_points >= 1000 THEN 'silver'
          ELSE 'bronze'
      END
      WHERE user_id = v_user;

    END IF;
  END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_refund_wallet` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
  -- Nếu đơn vừa chuyển sang trạng thái cancelled
  IF NEW.status = 'cancelled'
     AND OLD.status <> 'cancelled'
     AND NEW.payment_method IN ('vnpay','momo','card') THEN

      -- Tạo ví nếu chưa tồn tại
      INSERT INTO wallets (user_id, balance)
      VALUES (NEW.user_id, 0)
      ON DUPLICATE KEY UPDATE user_id = user_id;

      -- Cộng tiền vào ví
      UPDATE wallets
      SET balance = balance + NEW.total
      WHERE user_id = NEW.user_id;

      -- Ghi lịch sử
      INSERT INTO wallet_transactions (wallet_id, order_id, type, amount, description)
      VALUES (
        (SELECT id FROM wallets WHERE user_id = NEW.user_id),
        NEW.id,
        'refund',
        NEW.total,
        CONCAT('Hoàn tiền khi hủy đơn #', NEW.id)
      );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_size_id` int(11) NOT NULL,
  `ice_level` int(11) DEFAULT 100 COMMENT 'Lượng đá (0-100%)',
  `sugar_level` int(11) DEFAULT 100 COMMENT 'Lượng đường (0-100%)',
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` int(11) NOT NULL,
  `total_price` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Chi tiết đơn hàng với tùy chọn lượng đá và đường';

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_size_id`, `ice_level`, `sugar_level`, `quantity`, `unit_price`, `total_price`, `created_at`) VALUES
(1, 1, 2, 5, 100, 100, 1, 25000, 25000, '2025-11-25 10:20:52'),
(2, 1, 3, 7, 100, 100, 1, 30000, 30000, '2025-11-25 10:20:52'),
(3, 1, 1, 1, 100, 100, 1, 25000, 43000, '2025-11-25 10:20:52'),
(4, 2, 1, 1, 100, 100, 1, 25000, 30000, '2025-11-25 10:25:09'),
(5, 2, 2, 4, 100, 100, 1, 20000, 20000, '2025-11-25 10:25:09'),
(6, 2, 3, 7, 100, 100, 1, 30000, 35000, '2025-11-25 10:25:09'),
(7, 3, 1, 1, 100, 100, 1, 25000, 35000, '2025-11-25 10:33:06'),
(8, 3, 3, 7, 100, 100, 1, 30000, 35000, '2025-11-25 10:33:06'),
(9, 3, 2, 4, 100, 100, 1, 20000, 20000, '2025-11-25 10:33:06'),
(10, 4, 4, 12, 100, 100, 1, 25000, 30000, '2025-11-25 12:29:55'),
(11, 5, 4, 12, 100, 100, 3, 25000, 90000, '2025-11-25 12:33:03'),
(12, 6, 3, 9, 100, 100, 4, 42000, 188000, '2025-11-25 13:49:35'),
(22, 16, 2, 6, 100, 100, 4, 30000, 120000, '2025-11-25 14:23:46'),
(24, 18, 2, 6, 100, 100, 4, 30000, 120000, '2025-11-25 14:26:35'),
(25, 19, 3, 9, 100, 100, 1, 42000, 42000, '2025-11-25 14:28:25'),
(26, 20, 3, 9, 100, 100, 3, 42000, 126000, '2025-11-25 14:31:24'),
(27, 21, 4, 12, 100, 100, 5, 25000, 170000, '2025-11-25 14:38:47'),
(28, 22, 2, 6, 100, 100, 4, 30000, 120000, '2025-11-25 14:39:02'),
(29, 23, 1, 3, 100, 100, 3, 38000, 129000, '2025-11-25 14:39:18'),
(30, 24, 1, 3, 100, 100, 3, 38000, 129000, '2025-11-25 14:45:35'),
(31, 25, 1, 3, 100, 100, 3, 38000, 129000, '2025-11-25 14:50:30'),
(32, 26, 3, 8, 100, 100, 3, 36000, 108000, '2025-11-25 15:18:37'),
(33, 27, 1, 3, 100, 100, 2, 38000, 100000, '2025-11-25 17:59:36'),
(34, 28, 3, 9, 100, 100, 3, 42000, 126000, '2025-11-26 07:24:18'),
(35, 29, 3, 9, 100, 100, 3, 42000, 153000, '2025-11-26 08:21:45'),
(36, 30, 4, 10, 100, 100, 1, 15000, 15000, '2025-11-26 08:22:57'),
(37, 30, 3, 9, 100, 100, 2, 42000, 94000, '2025-11-26 08:22:57'),
(38, 31, 1, 3, 100, 100, 2, 38000, 76000, '2025-11-26 10:30:38'),
(39, 31, 3, 9, 100, 100, 1, 42000, 42000, '2025-11-26 10:30:38'),
(40, 32, 2, 6, 100, 100, 2, 30000, 60000, '2025-11-26 10:36:45'),
(41, 33, 3, 9, 100, 100, 10, 42000, 510000, '2025-11-26 10:48:42'),
(42, 34, 3, 9, 100, 100, 1, 42000, 51000, '2025-11-27 12:03:27'),
(43, 35, 2, 6, 100, 100, 2, 30000, 60000, '2025-11-27 12:36:09'),
(47, 39, 3, 9, 100, 100, 1, 42000, 51000, '2025-11-27 12:51:47'),
(48, 40, 3, 9, 100, 100, 1, 42000, 47000, '2025-11-27 12:56:04'),
(49, 41, 3, 9, 100, 100, 1, 42000, 47000, '2025-11-27 14:16:57'),
(50, 44, 3, 9, 100, 100, 2, 42000, 94000, '2025-11-27 17:15:07'),
(51, 45, 2, 6, 100, 100, 1, 30000, 30000, '2025-11-27 17:17:53'),
(52, 46, 3, 9, 100, 100, 18, 42000, 918000, '2025-11-28 08:35:32'),
(53, 47, 2, 6, 100, 100, 1, 30000, 30000, '2025-11-28 10:12:43'),
(54, 47, 1, 3, 100, 100, 1, 38000, 43000, '2025-11-28 10:12:43'),
(55, 47, 1, 3, 100, 100, 1, 38000, 56000, '2025-11-28 10:12:43'),
(56, 47, 3, 8, 100, 100, 1, 36000, 40000, '2025-11-28 10:12:43'),
(57, 47, 3, 9, 75, 75, 1, 42000, 47000, '2025-11-28 10:12:43'),
(58, 48, 3, 9, 100, 100, 4, 42000, 188000, '2025-12-02 07:45:09'),
(59, 49, 7, 19, 50, 50, 1, 20000, 25000, '2025-12-02 09:10:29'),
(60, 50, 9, 27, 100, 25, 22, 35000, 1210000, '2025-12-02 09:14:00'),
(61, 51, 1, 1, 100, 100, 1, 25000, 25000, '2025-12-02 09:48:26'),
(62, 52, 3, 9, 100, 100, 22, 42000, 1122000, '2025-12-03 08:48:08'),
(63, 53, 1, 3, 100, 100, 39, 38000, 2184000, '2025-12-03 08:54:30'),
(64, 54, 1, 3, 100, 100, 99, 38000, 5544000, '2025-12-03 08:55:35'),
(65, 55, 9, 25, 100, 100, 1, 25000, 25000, '2025-12-03 09:01:56'),
(66, 55, 7, 19, 100, 100, 1, 20000, 20000, '2025-12-03 09:01:56'),
(67, 56, 7, 19, 100, 100, 1, 20000, 20000, '2025-12-03 09:05:13'),
(68, 57, 7, 20, 100, 100, 1, 25000, 30000, '2025-12-03 09:06:32'),
(69, 58, 6, 17, 100, 100, 1, 25000, 25000, '2025-12-03 09:06:54'),
(70, 59, 3, 7, 100, 100, 1, 30000, 30000, '2025-12-03 09:07:30'),
(71, 60, 2, 4, 100, 100, 1, 20000, 20000, '2025-12-03 09:09:43'),
(72, 61, 6, 16, 100, 100, 1, 20000, 20000, '2025-12-03 09:10:28'),
(73, 62, 9, 26, 100, 100, 2, 30000, 100000, '2025-12-03 10:08:54'),
(74, 63, 9, 25, 100, 100, 99, 25000, 4455000, '2025-12-03 10:09:50'),
(75, 64, 9, 26, 100, 100, 1, 30000, 50000, '2025-12-03 10:14:13'),
(76, 65, 7, 19, 100, 100, 1, 20000, 20000, '2025-12-03 10:15:01'),
(77, 66, 1, 2, 0, 25, 1, 32000, 50000, '2025-12-03 10:43:40'),
(78, 67, 7, 19, 100, 100, 1, 20000, 26000, '2025-12-03 14:39:25'),
(79, 68, 7, 19, 100, 100, 1, 20000, 26000, '2025-12-03 14:40:25'),
(80, 69, 7, 19, 75, 0, 99, 20000, 2970000, '2025-12-03 14:45:21'),
(81, 70, 5, 13, 50, 50, 3, 25000, 90000, '2025-12-03 14:51:14'),
(82, 70, 7, 19, 100, 100, 1, 20000, 20000, '2025-12-03 14:51:14'),
(83, 71, 7, 20, 75, 75, 28, 25000, 700000, '2025-12-03 14:54:10'),
(84, 72, 1, 1, 75, 75, 9, 25000, 387000, '2025-12-03 14:57:06'),
(85, 73, 7, 20, 100, 100, 4, 25000, 124000, '2025-12-03 14:57:14'),
(86, 74, 9, 25, 100, 100, 3, 25000, 135000, '2025-12-03 15:17:34'),
(87, 75, 5, 14, 100, 100, 4, 30000, 120000, '2025-12-04 15:13:23'),
(88, 76, 5, 15, 100, 100, 5, 35000, 175000, '2025-12-04 16:38:37'),
(89, 76, 7, 21, 100, 100, 1, 30000, 50000, '2025-12-04 16:38:37'),
(90, 77, 7, 20, 100, 100, 1, 25000, 45000, '2025-12-06 15:27:52'),
(91, 78, 3, 9, 100, 100, 1, 42000, 47000, '2025-12-06 15:33:16'),
(92, 79, 3, 9, 100, 100, 1, 42000, 47000, '2025-12-06 15:52:22'),
(93, 80, 3, 9, 100, 100, 1, 42000, 47000, '2025-12-06 15:53:28'),
(94, 81, 3, 9, 100, 100, 1, 42000, 47000, '2025-12-06 15:57:15'),
(95, 82, 5, 15, 100, 100, 5, 35000, 200000, '2025-12-06 15:58:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_item_toppings`
--

CREATE TABLE `order_item_toppings` (
  `id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `topping_id` int(11) NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_item_toppings`
--

INSERT INTO `order_item_toppings` (`id`, `order_item_id`, `topping_id`, `price`) VALUES
(12, 3, 1, 5000),
(13, 3, 2, 7000),
(14, 3, 3, 6000),
(15, 4, 5, 5000),
(16, 6, 5, 5000),
(17, 7, 5, 5000),
(18, 7, 1, 5000),
(19, 8, 5, 5000),
(20, 10, 5, 5000),
(21, 11, 5, 5000),
(22, 12, 5, 5000),
(23, 27, 5, 5000),
(24, 27, 4, 4000),
(25, 29, 1, 5000),
(26, 30, 1, 5000),
(27, 31, 1, 5000),
(28, 33, 2, 7000),
(29, 33, 1, 5000),
(30, 35, 5, 5000),
(31, 35, 4, 4000),
(32, 37, 5, 5000),
(33, 41, 5, 5000),
(34, 41, 4, 4000),
(35, 42, 5, 5000),
(36, 42, 4, 4000),
(43, 47, 5, 5000),
(44, 47, 4, 4000),
(45, 48, 5, 5000),
(46, 49, 5, 5000),
(47, 50, 5, 5000),
(48, 52, 5, 5000),
(49, 52, 4, 4000),
(50, 54, 1, 5000),
(51, 55, 1, 5000),
(52, 55, 2, 7000),
(53, 55, 3, 6000),
(54, 56, 4, 4000),
(55, 57, 5, 5000),
(56, 58, 5, 5000),
(57, 59, 1, 5000),
(58, 60, 5, 5000),
(59, 60, 3, 6000),
(60, 60, 4, 4000),
(61, 60, 1, 5000),
(62, 62, 5, 5000),
(63, 62, 4, 4000),
(64, 63, 2, 7000),
(65, 63, 3, 6000),
(66, 63, 1, 5000),
(67, 64, 2, 7000),
(68, 64, 3, 6000),
(69, 64, 1, 5000),
(70, 68, 1, 5000),
(71, 73, 5, 5000),
(72, 73, 3, 6000),
(73, 73, 4, 4000),
(74, 73, 1, 5000),
(75, 74, 5, 5000),
(76, 74, 3, 6000),
(77, 74, 4, 4000),
(78, 74, 1, 5000),
(79, 75, 5, 5000),
(80, 75, 3, 6000),
(81, 75, 4, 4000),
(82, 75, 1, 5000),
(83, 77, 2, 7000),
(84, 77, 3, 6000),
(85, 77, 1, 5000),
(86, 78, 3, 6000),
(87, 79, 3, 6000),
(88, 80, 3, 6000),
(89, 80, 4, 4000),
(90, 81, 1, 5000),
(91, 84, 2, 7000),
(92, 84, 3, 6000),
(93, 84, 1, 5000),
(94, 85, 3, 6000),
(95, 86, 5, 5000),
(96, 86, 3, 6000),
(97, 86, 4, 4000),
(98, 86, 1, 5000),
(99, 89, 5, 5000),
(100, 89, 3, 6000),
(101, 89, 4, 4000),
(102, 89, 1, 5000),
(103, 90, 5, 5000),
(104, 90, 3, 6000),
(105, 90, 4, 4000),
(106, 90, 1, 5000),
(107, 91, 5, 5000),
(108, 92, 5, 5000),
(109, 93, 5, 5000),
(110, 94, 5, 5000),
(111, 95, 1, 5000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Bảng sản phẩm với soft delete - deleted_at NULL = chưa xóa, NOT NULL = đã xóa';

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `image`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Trà sữa truyền thống', 'tra-sua-tran-chau', 'Trà sữa béo, trân châu dai', 'products/1764032654-6925008e9fbf6.png', 1, '2025-11-12 15:17:24', '2025-11-25 08:04:14', NULL),
(2, 2, 'Cà phê sữa đá', 'ca-phe-sua-da', 'Cà phê rang xay, sữa đặc', 'products/1764032609-69250061e8438.jpg', 1, '2025-11-12 15:17:24', '2025-11-25 08:03:29', NULL),
(3, 3, 'Nước ép cam', 'nuoc-ep-cam', 'Nước ép cam tươi', 'products/1764032829-6925013d924c8.png', 1, '2025-11-12 15:17:24', '2025-11-25 08:07:09', NULL),
(4, 4, 'Trà Chanh', NULL, 'Trà chanh tươi mát', 'products/1764048022-69253c96bb7a3.jpg', 1, '2025-11-25 12:20:22', '2025-11-25 12:20:22', NULL),
(5, 2, 'Cà phê Muối', NULL, 'Món &amp;quot;trend&amp;quot; cực mạnh, lớp kem mặn béo hòa quyện vị đắng.', 'products/1764635753-692e3469bec21.webp', 1, '2025-12-02 07:35:53', '2025-12-03 14:49:23', NULL),
(6, 3, 'Nước ép Dứa', NULL, 'Nước ép dứa thơm lừng, giàu Vitamin C.', 'products/1764635866-692e34da558fd.png', 1, '2025-12-02 07:37:46', '2025-12-02 07:37:46', NULL),
(7, 4, 'Trà Đào Cam Sả', NULL, 'Vị thanh ngọt của đào kết hợp hương sả thơm lừng.', 'products/1764635974-692e3546e40ae.jpg', 1, '2025-12-02 07:39:34', '2025-12-02 07:39:34', NULL),
(8, 1, 'Trà Sữa Nướng', NULL, '', 'products/1764636152-692e35f80ab68.jpg', 0, '2025-12-02 07:42:32', '2025-12-02 07:43:34', '2025-12-02 00:43:34'),
(9, 1, 'Trà Sữa Nướng', NULL, 'Vị trà nướng đậm đà, hơi khói, rất được ưa chuộng hiện nay.', 'products/1764636278-692e367696c75.jpg', 1, '2025-12-02 07:44:38', '2025-12-02 07:44:38', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `price` int(11) NOT NULL COMMENT 'in cents or VND as integer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_sizes`
--

INSERT INTO `product_sizes` (`id`, `product_id`, `size_id`, `price`) VALUES
(1, 1, 1, 25000),
(2, 1, 2, 32000),
(3, 1, 3, 38000),
(4, 2, 1, 20000),
(5, 2, 2, 25000),
(6, 2, 3, 30000),
(7, 3, 1, 30000),
(8, 3, 2, 36000),
(9, 3, 3, 42000),
(10, 4, 1, 15000),
(11, 4, 2, 20000),
(12, 4, 3, 25000),
(13, 5, 1, 25000),
(14, 5, 2, 30000),
(15, 5, 3, 35000),
(16, 6, 1, 20000),
(17, 6, 2, 25000),
(18, 6, 3, 30000),
(19, 7, 1, 20000),
(20, 7, 2, 25000),
(21, 7, 3, 30000),
(22, 8, 1, 25000),
(23, 8, 2, 30000),
(24, 8, 3, 35000),
(25, 9, 1, 25000),
(26, 9, 2, 30000),
(27, 9, 3, 35000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_toppings`
--

CREATE TABLE `product_toppings` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `topping_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_toppings`
--

INSERT INTO `product_toppings` (`id`, `product_id`, `topping_id`) VALUES
(15, 4, 5),
(16, 4, 1),
(17, 4, 4),
(21, 1, 1),
(22, 1, 2),
(23, 1, 3),
(24, 6, 5),
(25, 6, 3),
(26, 6, 4),
(27, 7, 5),
(28, 7, 1),
(29, 7, 3),
(30, 7, 4),
(31, 8, 5),
(32, 8, 1),
(33, 8, 3),
(34, 8, 4),
(35, 9, 5),
(36, 9, 1),
(37, 9, 3),
(38, 9, 4),
(41, 3, 5),
(42, 3, 4),
(43, 5, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1 COMMENT '1: Hiện, 0: Ẩn',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `order_id`, `rating`, `comment`, `status`, `created_at`) VALUES
(1, 17, 3, NULL, 5, 'ngon voãi', 1, '2025-11-25 10:02:01'),
(2, 6, 1, 25, 5, 'ngon', 1, '2025-11-25 16:35:36'),
(3, 19, 7, 49, 1, 'như c...', 0, '2025-12-02 09:12:03'),
(4, 20, 9, 50, 5, 'Đồ uống ngon, giá hạt dẻ phù hợp với sv. Mà ai sv mà cần mua đồ công nghệ thì qua https://techhubstore.io.vn/ nha\r\n', 1, '2025-12-02 09:17:09'),
(5, 21, 1, 51, 5, 'ngon quá sốp ơi', 1, '2025-12-03 08:51:20'),
(6, 17, 7, 67, 5, 'ok', 1, '2025-12-03 14:44:47'),
(7, 6, 7, 70, 5, 'ngon', 1, '2025-12-04 13:25:54'),
(8, 6, 5, 70, 5, 'tuyệt vời\r\n', 1, '2025-12-04 13:36:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT 'user,admin,staff',
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'user', 'Normal customer'),
(2, 'admin', 'Administrator');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `settings`
--

CREATE TABLE `settings` (
  `k` varchar(100) NOT NULL,
  `v` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `settings`
--

INSERT INTO `settings` (`k`, `v`) VALUES
('banner_1', 'assets/uploads/banners/1764641397-692e4a750ad17.png'),
('banner_2', 'assets/uploads/banners/1764641397-692e4a750c173.png'),
('banner_3', 'assets/uploads/banners/1764641397-692e4a750d27c.png'),
('contact_email', 'chilldrink@gmail.com'),
('contact_phone', '19003636'),
('default_shipping_fee', '15000'),
('points_per_vnd', '2000'),
('site_address', 'Đường Võ Nguyên Giáp, Thành phố Thanh Hóa'),
('site_logo', 'assets/uploads/settings/1764062129-692573b12a20b.png'),
('site_name', 'Chill Drink');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shipping_zones`
--

CREATE TABLE `shipping_zones` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `province` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `base_fee` int(11) DEFAULT 15000,
  `per_km_fee` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `shipping_zones`
--

INSERT INTO `shipping_zones` (`id`, `name`, `province`, `district`, `base_fee`, `per_km_fee`, `created_at`) VALUES
(1, 'Zone A', 'Thanh Hóa', 'TP Thanh Hóa', 10000, 0, '2025-11-12 15:17:24'),
(2, 'Zone B', 'Hà Nội', 'Hoàn Kiếm', 15000, 0, '2025-11-12 15:17:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sizes`
--

CREATE TABLE `sizes` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `multiplier` decimal(5,2) DEFAULT 1.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sizes`
--

INSERT INTO `sizes` (`id`, `name`, `multiplier`, `created_at`) VALUES
(1, 'S', 1.00, '2025-11-12 15:17:24'),
(2, 'M', 1.25, '2025-11-12 15:17:24'),
(3, 'L', 1.50, '2025-11-12 15:17:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `toppings`
--

CREATE TABLE `toppings` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `toppings`
--

INSERT INTO `toppings` (`id`, `name`, `price`, `status`, `created_at`) VALUES
(1, 'Trân châu đen', 5000, 1, '2025-11-12 15:17:24'),
(2, 'Pudding trứng', 7000, 1, '2025-11-12 15:17:24'),
(3, 'Thạch phô mai', 6000, 1, '2025-11-12 15:17:24'),
(4, 'Thạch trái cây', 4000, 1, '2025-11-12 15:17:24'),
(5, 'Nha đam', 5000, 1, '2025-11-19 07:36:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `transaction_ref` varchar(255) DEFAULT NULL,
  `method` enum('vnpay','momo','card') DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `status` enum('pending','success','failed') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL DEFAULT 1,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `avatar`, `phone`, `password`, `is_active`, `created_at`, `updated_at`) VALUES
(6, 2, ' Vương Lâm', 'duongdudao2k2@gmail.com', 'assets/uploads/avatar_6_1764831376.jpg', '0984430546', '$2y$10$qGpQy9gtlM59YP1ob/ofVOnAZncevVnqwsZxy1oAHHkCmiE1spuOO', 1, '2025-11-17 13:03:30', '2025-12-05 07:32:28'),
(12, 2, 'Admin', 'admin@example.com', NULL, '0123456789', '$2y$10$82Vl3vjOIXyMDoUInr3oPOlGu5yveZ3jM7XleHewDq9Fc2pkHCl52', 1, '2025-11-17 14:10:52', '2025-11-17 14:10:52'),
(13, 1, 'Staff', 'staff@example.com', NULL, '0987654321', '$2y$10$WjPj93ORYW4FzLA1GxofdOHx8jAL02Brx/hzRhuHyniOXuxgdTL3O', 1, '2025-11-17 14:10:52', '2025-12-06 15:29:40'),
(16, 1, 'Tran Van C', 'c@example.com', NULL, '0987000003', '$2y$10$1N45TgG4qA3Kwb6uuXZOd.lGmFxSvxz/qKvqNFPxXtZc6Qjwnc8dG', 0, '2025-11-17 14:10:52', '2025-11-21 14:12:06'),
(17, 1, 'Hàn Lập', 'tft4502@gmail.com', 'assets/uploads/avatar_17_1764835316.jpg', '0987654321', '$2y$10$L6DGoXqGkv2y21DIY.doKu9m2rAnc2Pd0BmvzIRowp.7gGg3ObM0i', 1, '2025-11-21 14:14:43', '2025-12-06 15:29:40'),
(18, 1, 'Khách', 'user@gmail.com', NULL, '0987654321', '$2y$10$Wh8k1rVghHVrPD7k1n3uMOytllYYTh6xGGNR2gBGkRKLj718.eXpi', 1, '2025-11-25 15:10:28', '2025-11-25 15:10:28'),
(19, 1, 'okok', 'nguyenvanlinh25062006@gmail.com', NULL, '0333044840', '$2y$10$fI0z8m6f2JlvxrlRtps/SOVWTIT8FixWdorAel5d6hnhuwS2BkoOC', 0, '2025-12-02 09:07:50', '2025-12-02 09:46:21'),
(20, 1, 'Trịnh Chiến', 'chientr33@gmail.com', NULL, '0985795608', '$2y$10$Td4nxkD.xcM4R39ZyS38ReUAAVbAFKeoXbZIfVxT1/HjNF.SLaJTa', 1, '2025-12-02 09:11:57', '2025-12-02 09:11:57'),
(21, 1, 'Lệ Phi Vũ', 'nguyenvanlinh250626@gmail.com', NULL, '0333044840', '$2y$10$dOJufh9NYWuRhZlIs6w1MO9Srm0c.6xredRonHKnoKbUkQwFhHr/q', 1, '2025-12-02 09:47:11', '2025-12-03 15:16:24'),
(22, 1, 'Nguyễn Hoàng Sơn', 'hson97805@gmail.com', NULL, '0974658853', '$2y$10$eyGlpG6obDqIgEuw2MAlNe8Uu.rJnhRHKlBSKqZkHt74dzNovaPqW', 1, '2025-12-03 08:57:33', '2025-12-03 08:57:33'),
(23, 1, 'jack', 'admin2@cinehub.com', NULL, '0333044840', '$2y$10$gD48qr.1IzbYhuCflnjoxuC8Int9yKdLxQBcmotNePCvBP.sVOCYi', 1, '2025-12-03 09:00:29', '2025-12-03 09:00:29'),
(24, 1, 'eeee', 'le3221981@gmail.com', NULL, '0393561314', '$2y$10$padcXiaLPcGBGoe0rzrduusU0zdF9cgOo2szlxyidzQnkC7WiLcfa', 1, '2025-12-03 10:10:04', '2025-12-03 10:10:04'),
(25, 1, 'Lê Phương Hà', 'phuongha9112006@gmail.com', NULL, '0123456789', '$2y$10$ACKO9ITRVL3gmenpwPF.L.djwrKKy5T1nOakebjlHNFBZrn5tMeyO', 1, '2025-12-03 10:10:31', '2025-12-03 10:10:31'),
(26, 1, 'Hẹ hẹ hẹ', 'vanq26800@gmail.com', NULL, '0978914708', '$2y$10$5PamespvF.kX/2Zn3igDC.P.gw7qkYZ1HMqxCYkUq2VkCOaik.442', 1, '2025-12-03 14:41:57', '2025-12-03 14:41:57'),
(27, 2, 'Đỗ Minh', 'dovanminhb2@gmail.com', NULL, '0945606336', '$2y$10$2ydDEDdPfnCcArjDf3O3F.m7dbavoTFQKHvNGNGAskCLrSTDl5TDK', 1, '2025-12-03 14:59:57', '2025-12-03 15:08:54');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_coupon_usage`
--

CREATE TABLE `user_coupon_usage` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `used_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_coupon_usage`
--

INSERT INTO `user_coupon_usage` (`id`, `user_id`, `coupon_id`, `order_id`, `discount_amount`, `used_at`) VALUES
(1, 6, 7, 24, 50000.00, '2025-11-25 14:45:35'),
(2, 18, 19, 33, 1020000.00, '2025-11-26 10:48:42');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_redeemed_coupons`
--

CREATE TABLE `user_redeemed_coupons` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `points_spent` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `redeemed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_redeemed_coupons`
--

INSERT INTO `user_redeemed_coupons` (`id`, `user_id`, `coupon_id`, `points_spent`, `created_at`, `redeemed_at`) VALUES
(3, 17, 16, 100, '2025-12-03 14:45:24', '2025-12-03 14:45:24'),
(4, 6, 20, 10, '2025-12-03 14:56:20', '2025-12-03 14:56:20'),
(5, 26, 8, 300, '2025-12-04 07:44:33', '2025-12-04 07:44:33');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wallets`
--

CREATE TABLE `wallets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `balance` int(11) DEFAULT 0 COMMENT 'Số dư ví (VND)',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `wallets`
--

INSERT INTO `wallets` (`id`, `user_id`, `balance`, `updated_at`) VALUES
(1, 6, 86117750, '2025-12-04 16:38:37'),
(2, 12, 0, '2025-11-18 09:34:31'),
(3, 17, 9959000, '2025-12-03 14:41:24'),
(4, 18, 1228600, '2025-12-06 15:59:03'),
(5, 20, 50000, '2025-12-03 09:13:45'),
(6, 21, 199390000, '2025-12-03 15:17:34'),
(7, 23, 5000000, '2025-12-03 09:14:02'),
(13, 25, 50000000, '2025-12-03 10:22:27'),
(14, 26, 0, '2025-12-03 14:43:02'),
(15, 27, 0, '2025-12-03 15:00:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` int(11) NOT NULL,
  `wallet_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `type` enum('deposit','withdraw','payment','refund') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `wallet_transactions`
--

INSERT INTO `wallet_transactions` (`id`, `wallet_id`, `user_id`, `order_id`, `type`, `amount`, `description`, `transaction_id`, `created_at`) VALUES
(1, 4, 18, 40, 'withdraw', -52300.00, 'Thanh toán đơn hàng #000040', NULL, '2025-11-27 05:56:04'),
(2, 1, 6, 44, 'withdraw', -109000.00, 'Thanh toán đơn hàng #000044', NULL, '2025-11-27 10:15:16'),
(3, NULL, 17, NULL, 'deposit', 10000000.00, 'Nạp tiền qua VNPay', '15307385', '2025-11-28 00:29:12'),
(4, NULL, 20, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-02 02:18:53'),
(5, NULL, 20, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-02 02:19:46'),
(6, NULL, 6, NULL, 'deposit', 100000.00, 'Nạp tiền vào ví qua bank', NULL, '2025-12-02 07:39:27'),
(7, 5, 20, 52, 'withdraw', -1137000.00, 'Thanh toán đơn hàng #000052', NULL, '2025-12-03 01:48:37'),
(8, NULL, 21, NULL, 'deposit', 10000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:53:36'),
(9, NULL, 21, NULL, 'deposit', 10000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:53:52'),
(10, 6, 21, 54, 'withdraw', -5544000.00, 'Thanh toán đơn hàng #000054', NULL, '2025-12-03 01:56:11'),
(11, 6, 21, 53, 'withdraw', -2199000.00, 'Thanh toán đơn hàng #000053', NULL, '2025-12-03 01:56:17'),
(12, NULL, 21, NULL, 'deposit', 10000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:56:37'),
(13, NULL, 21, NULL, 'deposit', 10000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:56:43'),
(14, NULL, 21, NULL, 'deposit', 10000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:56:50'),
(15, NULL, 21, NULL, 'deposit', 10000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:56:56'),
(16, NULL, 21, NULL, 'deposit', 10000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:57:06'),
(17, NULL, 21, NULL, 'deposit', 10000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:57:30'),
(18, NULL, 21, NULL, 'deposit', 10000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:57:36'),
(19, NULL, 21, NULL, 'deposit', 10000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:57:42'),
(20, NULL, 21, NULL, 'deposit', 10000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:57:48'),
(21, NULL, 21, NULL, 'deposit', 10000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:57:54'),
(22, NULL, 20, NULL, 'deposit', 10000000.00, 'Nạp tiền vào ví qua bank', NULL, '2025-12-03 01:58:24'),
(23, NULL, 21, NULL, 'deposit', 20000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:59:12'),
(24, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:59:18'),
(25, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:59:26'),
(26, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:59:33'),
(27, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:59:39'),
(28, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:59:50'),
(29, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 01:59:56'),
(30, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 02:00:04'),
(31, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 02:00:10'),
(32, NULL, 23, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 02:03:53'),
(33, 7, 23, 56, 'withdraw', -35000.00, 'Thanh toán đơn hàng #000056', NULL, '2025-12-03 02:05:13'),
(36, 1, 6, 58, 'withdraw', -40000.00, 'Thanh toán đơn hàng #000058', NULL, '2025-12-03 02:06:54'),
(40, NULL, 23, NULL, 'deposit', 5000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 02:11:19'),
(41, NULL, 23, NULL, 'deposit', 5000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 02:11:27'),
(42, NULL, 20, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua bank', NULL, '2025-12-03 02:11:33'),
(43, NULL, 20, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua bank', NULL, '2025-12-03 02:11:38'),
(44, NULL, 20, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua bank', NULL, '2025-12-03 02:11:42'),
(45, NULL, 20, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua bank', NULL, '2025-12-03 02:11:46'),
(46, NULL, 20, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua bank', NULL, '2025-12-03 02:11:50'),
(47, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 02:11:52'),
(48, NULL, 20, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua bank', NULL, '2025-12-03 02:11:53'),
(49, NULL, 20, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua bank', NULL, '2025-12-03 02:11:57'),
(50, NULL, 20, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua bank', NULL, '2025-12-03 02:12:00'),
(51, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền vào ví qua momo', NULL, '2025-12-03 02:12:01'),
(52, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền qua VNPay', '15316415', '2025-12-03 02:12:45'),
(53, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền qua VNPay', '15316419', '2025-12-03 02:14:15'),
(54, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền qua VNPay', '15316423', '2025-12-03 02:15:07'),
(55, 6, 21, 63, 'withdraw', -4470000.00, 'Thanh toán đơn hàng #000063', NULL, '2025-12-03 03:09:50'),
(56, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền qua VNPay', '15316552', '2025-12-03 03:10:40'),
(57, NULL, 25, NULL, 'deposit', 50000000.00, 'Nạp tiền qua VNPay', '15316577', '2025-12-03 03:22:27'),
(58, 3, 17, 68, 'withdraw', -41000.00, 'Thanh toán đơn hàng #000068', NULL, '2025-12-03 07:41:24'),
(59, 1, 6, 70, 'withdraw', -108500.00, 'Thanh toán đơn hàng #000070', NULL, '2025-12-03 07:51:14'),
(60, 6, 21, 71, 'withdraw', -715000.00, 'Thanh toán đơn hàng #000071', NULL, '2025-12-03 07:54:10'),
(61, NULL, 21, NULL, 'deposit', 5000000.00, 'Nạp tiền qua VNPay', '15317209', '2025-12-03 07:55:52'),
(62, NULL, 21, NULL, 'deposit', 50000000.00, 'Nạp tiền qua VNPay', '15317212', '2025-12-03 07:56:43'),
(63, 6, 21, 72, 'withdraw', -402000.00, 'Thanh toán đơn hàng #000072', NULL, '2025-12-03 07:57:06'),
(64, NULL, 6, NULL, 'deposit', 30000000.00, 'Nạp tiền qua VNPay', '15317221', '2025-12-03 07:58:56'),
(65, 6, 21, 74, 'withdraw', -123000.00, 'Thanh toán đơn hàng #000074', NULL, '2025-12-03 08:17:34'),
(66, 1, 6, 76, 'withdraw', -240000.00, 'Thanh toán đơn hàng #000076', NULL, '2025-12-04 09:38:37'),
(67, 4, 18, 77, 'withdraw', -50500.00, 'Thanh toán đơn hàng #000077', NULL, '2025-12-06 08:27:52'),
(68, 4, 18, 78, 'withdraw', -52300.00, 'Thanh toán đơn hàng #000078', NULL, '2025-12-06 08:33:16'),
(69, 4, 18, 79, 'withdraw', -52300.00, 'Thanh toán đơn hàng #000079', NULL, '2025-12-06 08:52:22'),
(70, 4, 18, 81, 'withdraw', -52300.00, 'Thanh toán đơn hàng #000081', NULL, '2025-12-06 08:57:15'),
(71, 4, 18, 81, 'refund', 52300.00, 'Hoàn tiền đơn hàng #000081 - Lý do: không muốn bán', NULL, '2025-12-06 08:57:45'),
(72, 4, 18, 82, 'withdraw', -195000.00, 'Thanh toán đơn hàng #000082', NULL, '2025-12-06 08:58:43'),
(73, 4, 18, 82, 'refund', 195000.00, 'Hoàn tiền đơn hàng #000082 - Lý do: Thích', NULL, '2025-12-06 08:59:03');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_addresses_user` (`user_id`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cart_user` (`user_id`),
  ADD KEY `idx_cart_product` (`product_id`);

--
-- Chỉ mục cho bảng `cart_item_toppings`
--
ALTER TABLE `cart_item_toppings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `topping_id` (`topping_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Chỉ mục cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_fav_user` (`user_id`),
  ADD KEY `fk_fav_product` (`product_id`);

--
-- Chỉ mục cho bảng `loyalty_points`
--
ALTER TABLE `loyalty_points`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `loyalty_point_spend`
--
ALTER TABLE `loyalty_point_spend`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lps_user` (`user_id`),
  ADD KEY `fk_lps_order` (`order_id`);

--
-- Chỉ mục cho bảng `loyalty_rewards`
--
ALTER TABLE `loyalty_rewards`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `loyalty_transactions`
--
ALTER TABLE `loyalty_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lt_user` (`user_id`),
  ADD KEY `fk_lt_order` (`order_id`);

--
-- Chỉ mục cho bảng `loyalty_user_rewards`
--
ALTER TABLE `loyalty_user_rewards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lur_user` (`user_id`),
  ADD KEY `fk_lur_reward` (`reward_id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_address` (`address_id`),
  ADD KEY `fk_orders_coupon` (`coupon_id`),
  ADD KEY `fk_orders_shipping` (`shipping_zone_id`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_status` (`status`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_oi_order` (`order_id`),
  ADD KEY `fk_oi_product` (`product_id`),
  ADD KEY `fk_oi_ps` (`product_size_id`);

--
-- Chỉ mục cho bảng `order_item_toppings`
--
ALTER TABLE `order_item_toppings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_oit_item` (`order_item_id`),
  ADD KEY `fk_oit_topping` (`topping_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_products_category` (`category_id`),
  ADD KEY `idx_products_name` (`name`),
  ADD KEY `idx_products_deleted_at` (`deleted_at`);

--
-- Chỉ mục cho bảng `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ps_product` (`product_id`),
  ADD KEY `fk_ps_size` (`size_id`);

--
-- Chỉ mục cho bảng `product_toppings`
--
ALTER TABLE `product_toppings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pt_product` (`product_id`),
  ADD KEY `fk_pt_topping` (`topping_id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rev_product` (`product_id`),
  ADD KEY `fk_reviews_order` (`order_id`),
  ADD KEY `idx_reviews_user_product_order` (`user_id`,`product_id`,`order_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`k`);

--
-- Chỉ mục cho bảng `shipping_zones`
--
ALTER TABLE `shipping_zones`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `toppings`
--
ALTER TABLE `toppings`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tx_order` (`order_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- Chỉ mục cho bảng `user_coupon_usage`
--
ALTER TABLE `user_coupon_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_coupon` (`user_id`,`coupon_id`),
  ADD KEY `idx_order` (`order_id`);

--
-- Chỉ mục cho bảng `user_redeemed_coupons`
--
ALTER TABLE `user_redeemed_coupons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_urc_user` (`user_id`),
  ADD KEY `fk_urc_coupon` (`coupon_id`);

--
-- Chỉ mục cho bảng `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_wallet_id` (`wallet_id`),
  ADD KEY `idx_order_id` (`order_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT cho bảng `cart_item_toppings`
--
ALTER TABLE `cart_item_toppings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `loyalty_points`
--
ALTER TABLE `loyalty_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT cho bảng `loyalty_point_spend`
--
ALTER TABLE `loyalty_point_spend`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `loyalty_rewards`
--
ALTER TABLE `loyalty_rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `loyalty_transactions`
--
ALTER TABLE `loyalty_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT cho bảng `loyalty_user_rewards`
--
ALTER TABLE `loyalty_user_rewards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT cho bảng `order_item_toppings`
--
ALTER TABLE `order_item_toppings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `product_toppings`
--
ALTER TABLE `product_toppings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `shipping_zones`
--
ALTER TABLE `shipping_zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `sizes`
--
ALTER TABLE `sizes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `toppings`
--
ALTER TABLE `toppings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `user_coupon_usage`
--
ALTER TABLE `user_coupon_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `user_redeemed_coupons`
--
ALTER TABLE `user_redeemed_coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `fk_addresses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `cart_item_toppings`
--
ALTER TABLE `cart_item_toppings`
  ADD CONSTRAINT `cart_item_toppings_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_item_toppings_ibfk_2` FOREIGN KEY (`topping_id`) REFERENCES `toppings` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_fav_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fav_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `loyalty_points`
--
ALTER TABLE `loyalty_points`
  ADD CONSTRAINT `fk_lp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `loyalty_point_spend`
--
ALTER TABLE `loyalty_point_spend`
  ADD CONSTRAINT `fk_lps_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_lps_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `loyalty_transactions`
--
ALTER TABLE `loyalty_transactions`
  ADD CONSTRAINT `fk_lt_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_lt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `loyalty_user_rewards`
--
ALTER TABLE `loyalty_user_rewards`
  ADD CONSTRAINT `fk_lur_reward` FOREIGN KEY (`reward_id`) REFERENCES `loyalty_rewards` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_lur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_address` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_shipping` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_oi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_oi_ps` FOREIGN KEY (`product_size_id`) REFERENCES `product_sizes` (`id`);

--
-- Các ràng buộc cho bảng `order_item_toppings`
--
ALTER TABLE `order_item_toppings`
  ADD CONSTRAINT `fk_oit_item` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_oit_topping` FOREIGN KEY (`topping_id`) REFERENCES `toppings` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `fk_ps_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ps_size` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `product_toppings`
--
ALTER TABLE `product_toppings`
  ADD CONSTRAINT `fk_pt_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pt_topping` FOREIGN KEY (`topping_id`) REFERENCES `toppings` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_rev_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rev_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_tx_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `user_redeemed_coupons`
--
ALTER TABLE `user_redeemed_coupons`
  ADD CONSTRAINT `fk_urc_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_urc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `fk_wallet_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Sự kiện
--
CREATE DEFINER=`root`@`localhost` EVENT `monthly_loyalty_reset` ON SCHEDULE EVERY 1 MONTH STARTS '2025-12-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL reset_monthly_points()$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
