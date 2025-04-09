-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th3 10, 2025 lúc 02:27 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `shoe_store`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `full_name`, `email`, `phone`, `address`, `city`, `payment_method`, `subtotal`, `shipping`, `total`, `order_date`, `status`) VALUES
(1, 'ORD-1741604672', 'Phạm Phú Thắng', 'thang001510@gmail.com', '0766526344', 'Đà Nẵng', 'Đà Nẵng', 'cod', 9698000.00, 10000.00, 9708000.00, '2025-03-10 18:04:32', 'pending'),
(2, 'ORD-1741605658', 'Phạm Thắng', 'thang001510@gmail.com', '0766526344', 'Đà Nẵng', 'Đà Nẵng', 'cod', 4699000.00, 10000.00, 4709000.00, '2025-03-10 18:20:58', 'pending'),
(3, 'ORD-1741605792', 'Phạm Thắng', 'thang001510@gmail.com', '0766526344', 'Đà Nẵng', 'Đà Nẵng', 'bank_transfer', 4999000.00, 10000.00, 5009000.00, '2025-03-10 18:23:12', 'pending');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`) VALUES
(1, 'ORD-1741604672', 259, 'Nike Zoom Vomero 5', 4699000.00, 1),
(2, 'ORD-1741604672', 260, 'Nike Zoom Vomero 5', 4999000.00, 1),
(3, 'ORD-1741605658', 259, 'Nike Zoom Vomero 5', 4699000.00, 1),
(4, 'ORD-1741605792', 260, 'Nike Zoom Vomero 5', 4999000.00, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category`, `name`, `color`, `price`, `image_url`, `created_at`) VALUES
(259, 'Nike Zoom', 'Nike Zoom Vomero 5', 'Brown', 4699000.00, 'https://ash.vn/cdn/shop/files/bff7b01d439187a537046ff297c96e24_800x.jpg?v=1731647502', '2025-03-10 10:46:22'),
(260, 'Nike Zoom', 'Nike Zoom Vomero 5', 'White', 4999000.00, 'Link', '2025-03-10 10:46:22'),
(261, 'Nike Zoom', 'Nike Zoom GP Challenge 1', 'White', 4409000.00, 'Link', '2025-03-10 10:46:22'),
(262, 'Nike Zoom', 'Nike Zoom Vomero 5', 'Blue', 4699000.00, 'Link', '2025-03-10 10:46:22'),
(263, 'Nike Air Force', 'Nike Air Force 1 LV8', 'White', 2679000.00, 'Link', '2025-03-10 10:46:22'),
(264, 'Nike Air Force', 'Nike Air Force 1 \'07 LV8', 'Black', 2815199.00, 'Link', '2025-03-10 10:46:22'),
(265, 'Nike Air Force', 'Nike Air Force 1 \'07 LV8', 'White', 2815199.00, 'Link', '2025-03-10 10:46:22'),
(266, 'Nike Air Force', 'Nike Air Force 1 \'07 Texture', 'White', 3329000.00, 'Link', '2025-03-10 10:46:22'),
(267, 'Nike Air Force', 'Nike Air Force 1 \'07', 'White', 3239000.00, 'Link', '2025-03-10 10:46:22'),
(268, 'Nike Air Force', 'Nike Air Force 1 \'07 LV8', 'White', 2815199.00, 'Link', '2025-03-10 10:46:22'),
(269, 'Nike Air Force', 'Nike Air Force 1 \'07', 'White', 3829000.00, 'Link', '2025-03-10 10:46:22'),
(270, 'Nike Dunk Low', 'Nike Dunk Low', 'Brown', 2929000.00, 'Link', '2025-03-10 10:46:22'),
(271, 'Nike Dunk Low', 'Nike Dunk Low LX', 'Brown', 3519000.00, 'Link', '2025-03-10 10:46:22'),
(272, 'Nike Dunk Low', 'Nike Dunk Low Retro SE Leather/Suede', 'Red', 3519000.00, 'Link', '2025-03-10 10:46:22'),
(273, 'Nike Dunk Low', 'Nike Dunk Low', 'White', 2169000.00, 'Link', '2025-03-10 10:46:22'),
(274, 'Nike Dunk Low', 'Nike Dunk Low Premium', 'White', 3829000.00, 'Link', '2025-03-10 10:46:22'),
(275, 'Nike Dunk Low', 'Nike Dunk Low', 'Gray', 2419000.00, 'Link', '2025-03-10 10:46:22'),
(276, 'Nike Dunk Low', 'Nike Dunk Low Retro', 'White', 2815000.00, 'Link', '2025-03-10 10:46:22'),
(277, 'Nike Pegasus', 'Nike Pegasus 41 Blueprint', 'White', 2679000.00, 'Link', '2025-03-10 10:46:22'),
(278, 'Nike Pegasus', 'Nike Pegasus Plus', 'Blue', 5279000.00, 'Link', '2025-03-10 10:46:22'),
(279, 'Nike Pegasus', 'Nike Pegasus 41 GORE-TEX', 'Blue', 4699000.00, 'Link', '2025-03-10 10:46:22'),
(280, 'Nike Pegasus', 'Nike Pegasus 41 Premium', 'White', 4109000.00, 'Link', '2025-03-10 10:46:22'),
(281, 'Nike Pegasus', 'Nike Pegasus 41', 'Black', 3829000.00, 'Link', '2025-03-10 10:46:22'),
(282, 'Nike Pegasus', 'Nike Pegasus Trail 5', 'Blue', 3059000.00, 'Link', '2025-03-10 10:46:22'),
(283, 'Nike Pegasus', 'Nike Pegasus Trail 5', 'Gray', 4109000.00, 'Link', '2025-03-10 10:46:22'),
(284, 'ASIAN Men COSCO Navy', 'ASIAN Men COSCO Navy', 'Blue', 710.00, 'Link', '2025-03-10 10:46:22'),
(285, 'ASIAN Men COSCO Navy', 'ASIAN Men COSCO Navy', 'Silver', 710.00, 'Link', '2025-03-10 10:46:22'),
(286, 'ASIAN Men COSCO Navy', 'ASIAN Men COSCO Navy', 'Black', 710.00, 'Link', '2025-03-10 10:46:22'),
(287, 'ASIAN Men COSCO Navy', 'ASIAN Men COSCO Navy', 'Gray', 710.00, 'Link', '2025-03-10 10:46:22'),
(288, 'ASIAN Men COSCO Navy', 'ASIAN Men COSCO Navy', 'White', 710.00, 'Link', '2025-03-10 10:46:22'),
(289, 'ASIAN Men COSCO Navy', 'ASIAN Men COSCO Navy', 'Brown', 710.00, 'Link', '2025-03-10 10:46:22'),
(290, 'ASIAN Men Bouncer-01', 'Nike Air Force 1 LV8', 'Black', 573.00, 'Link', '2025-03-10 10:46:22'),
(291, 'ASIAN Men Bouncer-01', 'Nike Air Force 1 \'07 LV8', 'Black', 573.00, 'Link', '2025-03-10 10:46:22'),
(292, 'ASIAN Men Bouncer-01', 'Nike Air Force 1 \'07 LV8', 'Gray', 573.00, 'Link', '2025-03-10 10:46:22'),
(293, 'ASIAN Men Bouncer-01', 'Nike Air Force 1 \'07 LV8', 'Black', 573.00, 'Link', '2025-03-10 10:46:22'),
(294, 'Asian CRYSTAL-13-NcWHTBLU', 'Nike Dunk Low', 'White', 1343.00, 'Link', '2025-03-10 10:46:22'),
(295, 'Asian CRYSTAL-13-NcWHTBLU', 'Nike Dunk Low', 'Brown', 1343.00, 'Link', '2025-03-10 10:46:22'),
(296, 'Asian CRYSTAL-13-NcWHTBLU', 'Nike Dunk Low', 'Black', 1343.00, 'Link', '2025-03-10 10:46:22'),
(297, 'Asian CRYSTAL-13-NcWHTBLU', 'Nike Dunk Low', 'Black', 1343.00, 'Link', '2025-03-10 10:46:22'),
(298, 'Asian CRYSTAL-13-NcWHTBLU', 'Nike Dunk Low', 'Black', 1343.00, 'Link', '2025-03-10 10:46:22'),
(299, 'Asian CRYSTAL-13-NcWHTBLU', 'Nike Dunk Low', 'Black', 1343.00, 'Link', '2025-03-10 10:46:22'),
(300, 'Asian CRYSTAL-13-NcWHTBLU', 'Nike Dunk Low LX', 'White', 1343.00, 'Link', '2025-03-10 10:46:22'),
(301, 'Asian NEXON-13cL', 'Asian NEXON-13cLGREYPGREEN', 'Gray', 811.00, 'Link', '2025-03-10 10:46:22'),
(302, 'Asian NEXON-13cL', 'Asian NEXON-13cBLCKPGRN', 'Black', 811.00, 'Link', '2025-03-10 10:46:22'),
(303, 'Asian NEXON-13cL', 'Asian NEXON-13cNBLUE', 'Blue', 811.00, 'Link', '2025-03-10 10:46:22'),
(304, 'Asian NEXON-13cL', 'Asian NEXON-13cWHITETBLUE', 'White', 811.00, 'Link', '2025-03-10 10:46:22'),
(305, 'Campus 00s shoes', 'Campus 00s shoes', 'Brown', 140.00, 'Image', '2025-03-10 10:46:22'),
(306, 'Campus 00s shoes', 'Campus 00s shoes', 'Red', 140.00, 'Image', '2025-03-10 10:46:22'),
(307, 'Campus 00s shoes', 'Campus 00s shoes', 'Green', 140.00, 'Image', '2025-03-10 10:46:22'),
(308, 'Campus 00s shoes', 'Campus 00s shoes', 'Grey', 140.00, 'Image', '2025-03-10 10:46:22'),
(309, 'Campus 00s shoes', 'Campus 00s shoes', 'Orange', 140.00, 'Image', '2025-03-10 10:46:22'),
(310, 'Campus 00s shoes', 'Campus 00s shoes', 'Blue', 140.00, 'Image', '2025-03-10 10:46:22'),
(311, 'Campus 00s shoes', 'Campus 00s beta shoes', 'Gray', 140.00, 'Image', '2025-03-10 10:46:22'),
(312, 'Campus 00s shoes', 'Campus 00s shoes', 'Brown', 140.00, 'Image', '2025-03-10 10:46:22'),
(313, 'Campus ADV shoes', 'Campus ADV shoes', 'Blue', 140.00, 'Image', '2025-03-10 10:46:22'),
(314, 'Campus ADV shoes', 'Campus ADV shoes', 'Black', 140.00, 'Image', '2025-03-10 10:46:22'),
(315, 'Campus 80s shoes', 'Campus 80s shoes', 'Gray', 140.00, 'Image', '2025-03-10 10:46:22'),
(316, 'Campus 80s shoes', 'Campus 80s shoes', 'Red', 140.00, 'Image', '2025-03-10 10:46:22'),
(317, 'Campus 80s shoes', 'Campus 80s shoes', 'Green', 140.00, 'Image', '2025-03-10 10:46:22'),
(318, 'Campus 80s shoes', 'Campus 80s shoes DeWitt Originals Shoes', 'White', 140.00, 'Image', '2025-03-10 10:46:22'),
(319, 'Campus 80s shoes', 'Campus 80s shoes DeWitt Originals Shoes', 'Blue', 140.00, 'Image', '2025-03-10 10:46:22'),
(320, 'Sparx Shoe', 'Sparx Shoe SM-414 Black Grey For Kids', 'Black', 1031.00, 'Link', '2025-03-10 10:46:22'),
(321, 'Sparx Shoe', 'Sparx Shoe SM-414 Navy Blue White For Men', 'Blue', 1031.00, 'Link', '2025-03-10 10:46:22'),
(322, 'Sparx Shoe', 'Sparx Shoe SM-414 Grey Orange For Men', 'Black', 1031.00, 'Link', '2025-03-10 10:46:22'),
(323, 'Sparx Shoe', 'Sparx Shoe SM-414 Black Gold For Men', 'Black', 1031.00, 'Link', '2025-03-10 10:46:22'),
(324, 'Sparx Shoe', 'Sparx Shoe SM-680 Black Gold For Men', 'Black', 1163.00, 'Link', '2025-03-10 10:46:22'),
(325, 'Sparx Shoe', 'Sparx Shoe SM-680 Grey Red For Men', 'Gray', 1163.00, 'Link', '2025-03-10 10:46:22'),
(326, 'Sparx Shoe', 'Sparx Shoe SM-816 Black Orange For Men', 'Black', 1331.00, 'Link', '2025-03-10 10:46:22'),
(327, 'Sparx Shoe', 'Sparx Shoe SM-816 Grey Green For Men', 'Black', 1463.00, 'Link', '2025-03-10 10:46:22'),
(328, 'Sparx Shoe', 'SM-797', 'White', 1080.00, 'Link', '2025-03-10 10:46:22'),
(329, 'Sparx Shoe', 'SM-797', 'White', 1519.00, 'Link', '2025-03-10 10:46:22'),
(330, 'Sparx Shoe', 'Sparx Sports Shoe SM-911 White For Men', 'White', 1783.00, 'Link', '2025-03-10 10:46:22'),
(331, 'Sparx Shoe', 'Sparx Sports Shoe SM-911 Black For Men', 'Blue', 1776.00, 'Link', '2025-03-10 10:46:22'),
(332, 'Sparx Shoe', 'Sparx Sports Shoe SM-869 Blue For Men', 'White', 1168.00, 'Link', '2025-03-10 10:46:22'),
(333, 'Sparx Shoe', 'SM-830', 'White', 1736.00, 'Link', '2025-03-10 10:46:22'),
(334, 'Sparx Shoe', 'Sparx Shoe SM 884 Metallic Copper For Men', 'Blue', 1662.00, 'Link', '2025-03-10 10:46:22'),
(335, 'Sparx Shoe', 'SM-807', 'Black', 888.00, 'Link', '2025-03-10 10:46:22'),
(336, 'Sparx Shoe', 'Sparx Sports Shoe SM-865 White Blue For Men', 'White', 1039.00, 'Link', '2025-03-10 10:46:22'),
(337, 'Sparx Shoe', 'Sparx Sports Shoe SM-954 Black For Men', 'Blue', 1805.00, 'Link', '2025-03-10 10:46:22'),
(338, 'Sparx Shoe', 'Sparx Sports Shoe SM-816 White For Men', 'White', 1447.00, 'Link', '2025-03-10 10:46:22'),
(339, 'Sparx Shoe', 'Sparx Shoe SL 242 Pink For Men', 'Gray', 1251.00, 'Link', '2025-03-10 10:46:22'),
(340, 'Sandal Men', 'Sandal TL55', 'Black', 200000.00, 'Link', '2025-03-10 10:46:22'),
(341, 'Sandal Men', 'Sandal TL563', 'Black', 200000.00, 'Link', '2025-03-10 10:46:22'),
(342, 'Sandal Men', 'Sandal TL543', 'Black', 200000.00, 'Link', '2025-03-10 10:46:22'),
(343, 'Sandal Men', 'Sandal TL546', 'Black', 200000.00, 'Link', '2025-03-10 10:46:22'),
(344, 'Sandal Men', 'Sandal TL559', 'Black', 200000.00, 'Link', '2025-03-10 10:46:22'),
(345, 'Sandal Men', 'Sandal TL564', 'Black', 215000.00, 'Link', '2025-03-10 10:46:22'),
(346, 'Sandal Men', 'Sandal TL542', 'Black', 215000.00, 'Link', '2025-03-10 10:46:22'),
(347, 'Sandal Men', 'Sandal TL540', 'Black', 215000.00, 'Link', '2025-03-10 10:46:22'),
(348, 'Sandal Men', 'Sandal UU80', 'Black', 235000.00, 'Link', '2025-03-10 10:46:22'),
(349, 'Sandal Men', 'Sandal UU42', 'Black', 235000.00, 'Link', '2025-03-10 10:46:22'),
(350, 'Foam Slippers', 'Adidas men\'s cross strap sandals', 'Black', 50000.00, 'Link', '2025-03-10 10:46:22'),
(351, 'Foam Slippers', 'Men\'s Plastic Molded Sandals', 'Black', 79000.00, 'Link', '2025-03-10 10:46:22'),
(352, 'Foam Slippers', 'Unisex Men\'s Sandals', 'Black', 155000.00, 'Link', '2025-03-10 10:46:22'),
(353, 'Foam Slippers', 'Unisex Men\'s Sandals', 'Black', 155000.00, 'Link', '2025-03-10 10:46:22'),
(354, 'Foam Slippers', 'FM men\'s cross strap sandals with PVC strap', 'Black', 164000.00, 'Link', '2025-03-10 10:46:22'),
(355, 'Foam Slippers', 'Pathon PVC men\'s cross strap slippers', 'Black', 129000.00, 'Link', '2025-03-10 10:46:22'),
(356, 'Foam Slippers', 'Pathon men\'s cross strap slippers', 'Black', 129000.00, 'Link', '2025-03-10 10:46:22'),
(357, 'Foam Slippers', 'Pathon men\'s cross strap slippers', 'Black', 129000.00, 'Link', '2025-03-10 10:46:22'),
(358, 'Foam Slippers', 'Pathon SD120 Sandals PVC', 'Black', 129000.00, 'Link', '2025-03-10 10:46:22'),
(359, 'Foam Slippers', 'Pathon SD120 Sandals PVC', 'Black', 129000.00, 'Link', '2025-03-10 10:46:22'),
(360, 'Foam Slippers', 'Pathon SD38 Sandals PVC', 'Black', 129000.00, 'Link', '2025-03-10 10:46:22'),
(361, 'Shoe care and cleaning', 'ECOCO high quality soft bristle shoe brush', 'White', 39000.00, 'Link', '2025-03-10 10:46:22'),
(362, 'Uncategorized', 'KY LIEN High-Class Shoe Brush', 'Black', 11000.00, 'Link', '2025-03-10 10:46:22'),
(363, 'Uncategorized', 'Sneaker cleaning cloth -kc728', 'Yellow', 18000.00, 'Link', '2025-03-10 10:46:22'),
(364, 'Uncategorized', 'Sports Shoe Cleaning Tissue', 'Black', 15500.00, 'Link', '2025-03-10 10:46:22'),
(365, 'Uncategorized', 'Shoe whitening bottle', 'White', 15000.00, 'Link', '2025-03-10 10:46:22'),
(366, 'Uncategorized', 'KOSE HIGH-CLASS SHOE CLEANER', 'White', 85000.00, 'Link', '2025-03-10 10:46:22'),
(367, 'Uncategorized', 'Combo of 50 Melamine Foam Pads', 'Black', 125000.00, 'Link', '2025-03-10 10:46:22'),
(368, 'Deodorize and protect shoes', 'Silver Nano Shoe Deodorant Spray', 'Blue', 17900.00, 'Link', '2025-03-10 10:46:22'),
(369, 'Uncategorized', 'Nano Shoe Deodorant Spray', 'Black', 48000.00, 'Link', '2025-03-10 10:46:22'),
(370, 'Uncategorized', 'Ximo High Quality Shoe Deodorant Spray', 'Blue', 40000.00, 'Link', '2025-03-10 10:46:22'),
(371, 'Uncategorized', '6 Shoe Stickers', 'Black', 17280.00, 'Link', '2025-03-10 10:46:22'),
(372, 'Uncategorized', 'Set of 4 Anti-Slip High Heel Protectors', 'Black', 20000.00, 'Link', '2025-03-10 10:46:22'),
(373, 'Shoe storage', 'Shoe Storage Bag', 'Gray', 10000.00, 'Link', '2025-03-10 10:46:22'),
(374, 'Uncategorized', 'White multi-purpose shoe bag', 'White', 4000.00, 'Link', '2025-03-10 10:46:22'),
(375, 'Uncategorized', 'Yonex multi-purpose drawstring bag, waterproof', 'White', 25000.00, 'Link', '2025-03-10 10:46:22'),
(376, 'Uncategorized', 'Waterproof 3-compartment shoe bag', 'Blue', 75000.00, 'Link', '2025-03-10 10:46:22'),
(377, 'Uncategorized', 'Compact, colorful travel shoe bag', 'Red', 19000.00, 'Link', '2025-03-10 10:46:22'),
(378, 'Uncategorized', '150W UV 360? Shoe Dryer', 'White', 109000.00, 'Link', '2025-03-10 10:46:22'),
(379, 'Uncategorized', 'Shoe dryer deodorizes shoes and socks.', 'Black', 195000.00, 'Link', '2025-03-10 10:46:22'),
(380, 'Accessories to support wearing shoes', 'Bamboo Charcoal Mesh Shoe Insoles', 'White', 12490.00, 'Link', '2025-03-10 10:46:22'),
(381, 'Uncategorized', 'Deodorizing Shoe Insoles With Peppermint Essence', 'Black', 19000.00, 'Link', '2025-03-10 10:46:22'),
(382, 'Uncategorized', '4D Foling Shock Absorbing Premium Sports Shoe Insole', 'Blue', 10000.00, 'Link', '2025-03-10 10:46:22'),
(383, 'Uncategorized', '120cm sports shoe laces', 'Black', 5000.00, 'Link', '2025-03-10 10:46:22'),
(384, 'Waterproof', 'Silicone Shoe Covers', 'White', 33000.00, 'Link', '2025-03-10 10:46:22'),
(385, 'Uncategorized', 'SKSTECH Waterproof Shoe Cover', 'Blue', 25000.00, 'Link', '2025-03-10 10:46:22'),
(386, 'Uncategorized', 'Double Zipper Rain Boots', 'Black', 56000.00, 'Link', '2025-03-10 10:46:22'),
(387, 'Uncategorized', 'Rain boots, waterproof shoe covers', 'Blue', 1000.00, 'Link', '2025-03-10 10:46:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'thang1510', 'thang001510@gmail.com', '$2y$10$Eue8Y.s1b050LAHUPqpM1uWZGDxTu4BIaryFOQUbs1Et/6IQs5zpa', '2025-03-10 17:06:36', '2025-03-10 17:06:36'),
(2, 'thang04', 'thang0015101@gmail.com', '$2y$10$YTwS7pjUi85WhcuH2sJnreT7f0UCCoJxbq0so2zaZrGpuUNkni8cW', '2025-03-10 17:11:09', '2025-03-10 17:11:09');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=388;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
