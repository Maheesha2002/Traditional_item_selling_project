-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2025 at 10:19 PM
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
-- Database: `lanka2`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `full_name`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Ravindu', 'rashmikadinal975@gmail.com', '$2y$10$F2xz6m4FJCa0heVesNYDyOAJTqJ/l5/otxzYagY6ror1lO00Zh7G.', '2025-04-10 22:23:08', '2025-04-10 22:23:08'),
(2, 'raviya', 'admin@gmail.com', '$2y$10$Wv8rw6EpoMs0K7zAw2dwaeGyrqHWHhP/ccnA/m29tUdmNWI0kXHGq', '2025-04-18 11:08:10', '2025-04-18 11:08:10');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `customer_id` varchar(50) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `customer_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(7, 'CUS69511', 'P250410998', 1, '2025-04-10 20:35:06', '2025-04-10 20:35:06'),
(11, 'CUS50858', 'P250410856', 1, '2025-04-17 13:43:28', '2025-04-17 13:43:28');

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` int(11) NOT NULL,
  `user_id` varchar(20) DEFAULT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'guest',
  `status` enum('active','closed') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_hidden` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `user_id` varchar(20) DEFAULT NULL,
  `user_type` varchar(20) NOT NULL DEFAULT 'guest',
  `message` text NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `chat_messages`
--
DELIMITER $$
CREATE TRIGGER `update_chat_timestamp` AFTER INSERT ON `chat_messages` FOR EACH ROW UPDATE chats SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.chat_id
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `chat_metadata`
--

CREATE TABLE `chat_metadata` (
  `chat_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comment_likes`
--

CREATE TABLE `comment_likes` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `customer_id` varchar(36) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('new','read','replied') NOT NULL DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` varchar(8) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `cover_photo` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `full_name`, `email`, `password`, `status`, `created_at`, `reset_token`, `reset_token_expiry`, `profile_image`, `cover_photo`, `phone`, `dob`) VALUES
('CUS08802', 'rashmika', 'rashmika@gmail.com', '$2y$10$iLEqjHO6zIieia.LLqWfXuV1wXMwQTgzsL0f0S3ajHi.edeYxKINS', 'active', '2025-04-10 16:50:31', NULL, NULL, NULL, NULL, NULL, NULL),
('CUS50858', 'dinal', 'dinal@gmail.com', '$2y$10$yZvR/qrTuzVmIURfTUP6gO7bnRW7n.TZ7XrmPf4BAwmNM5hwwjwQ.', 'active', '2025-04-10 19:23:40', NULL, NULL, NULL, NULL, NULL, NULL),
('CUS69511', 'kasun', 'kasun@gmail.com', '$2y$10$HG1ZOGhFmGLd3hZ8K5QxQucmCc5WCPsSotQ4Wq9DsXn0iN4Ujgzsy', 'active', '2025-04-10 20:33:21', NULL, NULL, NULL, NULL, NULL, NULL),
('CUS95253', 'rashmika', 'm@gmail.com', '$2y$10$GSH..sRMkT7bNDEHb38RIeo1SEFdjMFZZGbbjwk6FdjoJoCLRzwKe', 'active', '2025-04-17 12:57:41', NULL, NULL, 'uploads/profile_photos/profile_CUS95253_1745598974.jpg', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `customer_id` varchar(50) NOT NULL,
  `address_id` int(11) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `customer_id`, `address_id`, `payment_method`, `subtotal`, `shipping_fee`, `total_amount`, `status`, `payment_status`, `created_at`) VALUES
(107, 'ORD000000001', 'CUS08802', 22, 'cod', 5500.00, 350.00, 5850.00, 'shipped', 'pending', '2025-04-10 18:22:37'),
(108, 'ORD000000002', 'CUS08802', 22, 'cod', 10000.00, 350.00, 10350.00, 'processing', 'pending', '2025-04-10 18:27:40'),
(109, 'ORD000000003', 'CUS08802', 22, 'cod', 3800.00, 350.00, 4150.00, 'pending', 'pending', '2025-04-10 18:35:59'),
(110, 'ORD000000004', 'CUS50858', 23, 'cod', 4000.00, 350.00, 4350.00, 'processing', 'pending', '2025-04-10 19:57:11'),
(111, 'ORD000000005', 'CUS50858', 23, 'cod', 76000.00, 350.00, 76350.00, 'processing', 'pending', '2025-04-10 19:59:42'),
(112, 'ORD000000006', 'CUS50858', 23, 'cod', 960.00, 350.00, 1310.00, 'pending', 'pending', '2025-04-17 13:24:55'),
(113, 'ORD000000007', 'CUS50858', 23, 'cod', 480.00, 350.00, 830.00, 'pending', 'pending', '2025-04-17 13:37:01'),
(114, 'ORD000000008', 'CUS95253', 24, 'cod', 8400.00, 350.00, 8750.00, 'delivered', 'paid', '2025-04-18 05:30:02'),
(115, 'ORD000000009', 'CUS95253', 24, 'cod', 14400.00, 350.00, 14750.00, 'delivered', 'paid', '2025-04-25 16:24:54'),
(116, 'ORD000000010', 'CUS95253', 24, 'cod', 990.00, 350.00, 1340.00, 'processing', 'pending', '2025-04-25 16:43:16');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `seller_id` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `item_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `seller_id`, `quantity`, `price`, `item_total`) VALUES
(94, 'ORD000000001', 'P250410718', 'SLR6808', 1, 5500.00, 5500.00),
(95, 'ORD000000002', 'P250410998', 'SLR6808', 1, 10000.00, 10000.00),
(96, 'ORD000000003', 'P250410856', 'SLR6808', 1, 3800.00, 3800.00),
(97, 'ORD000000004', 'P250410685', 'SLR6255', 5, 800.00, 4000.00),
(98, 'ORD000000005', 'P250410685', 'SLR6255', 95, 800.00, 76000.00),
(99, 'ORD000000006', 'P250410919', 'SLR8257', 2, 480.00, 960.00),
(100, 'ORD000000007', 'P250410919', 'SLR8257', 1, 480.00, 480.00),
(101, 'ORD000000008', 'P250410424', 'SLR6808', 1, 5000.00, 5000.00),
(102, 'ORD000000008', 'P250410929', 'SLR8257', 1, 3200.00, 3200.00),
(103, 'ORD000000008', 'P250410622', 'SLR6255', 1, 200.00, 200.00),
(104, 'ORD000000009', 'P250410856', 'SLR6808', 2, 3800.00, 7600.00),
(105, 'ORD000000009', 'P250410424', 'SLR6808', 1, 5000.00, 5000.00),
(106, 'ORD000000009', 'P250410365', 'SLR6255', 1, 1800.00, 1800.00),
(107, 'ORD000000010', 'P250410208', 'SLR6808', 1, 990.00, 990.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` varchar(10) NOT NULL,
  `seller_id` varchar(10) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `main_category` varchar(50) NOT NULL,
  `sub_category` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `offer_price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive','deleted') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `seller_id`, `product_name`, `main_category`, `sub_category`, `price`, `offer_price`, `quantity`, `weight`, `description`, `created_at`, `updated_at`, `status`) VALUES
('P250410110', 'SLR8257', 'SN causal leather shoe ', 'Leather Products', 'Leather Footwear', 1500.00, 1400.00, 500, 200.00, '👞 SN Casual Leather Shoe – Comfortable and Stylish Everyday Footwear\r\nThe SN Casual Leather Shoe is designed to offer both comfort and style for your everyday activities. Made from high-quality leather, these shoes are perfect for casual outings, office wear, or relaxed weekends. With a modern design and durable construction, these shoes promise long-lasting comfort with every step.\r\n\r\n🔸 Features:\r\n\r\nMaterial: Premium genuine leather, offering both breathability and durability\r\n\r\nCategory: Leather Products → Leather Footwear\r\n\r\nDesign: Casual yet elegant, featuring a sleek silhouette with a comfortable fit\r\n\r\nWeight: Approx. 200g – Lightweight and easy to wear all day\r\n\r\nIdeal For: Daily wear, office use, and casual outings\r\n\r\nColor: Classic shades to complement any outfit\r\n\r\n💰 Price: LKR 1,500\r\n🎉 Offer Price: LKR 1,400\r\n📦 In Stock: 500 units\r\n\r\nStep into comfort and style with the SN Casual Leather Shoe, designed to provide you with the perfect balance of fashion and functionality.', '2025-04-10 20:50:56', '2025-04-10 20:50:56', 'active'),
('P250410117', 'SLR6255', 'White tea ', 'Ceylon Tea', 'White Tea', 500.00, 450.00, 1000, 500.00, '🍃 White Tea – A Delicate Ceylon Tea Experience\r\nImmerse yourself in the delicate and subtle flavor of White Tea, a premium offering from the heart of Sri Lanka. Known for its light and refined taste, this tea is harvested from young tea buds, making it a sought-after choice for tea enthusiasts who appreciate elegance and health benefits.\r\n\r\n🔸 Features:\r\n\r\nAuthentic Ceylon White Tea – Handpicked from the finest tea estates in Sri Lanka\r\n\r\nCategory: Ceylon Tea → White Tea\r\n\r\nMaterial: Pure white tea leaves, minimally processed to preserve their delicate flavor\r\n\r\nWeight: Approx. 500g – Perfect for multiple brewing sessions\r\n\r\nIdeal For: Tea connoisseurs, those seeking a light, healthy beverage, or anyone who enjoys a soothing and calming drink\r\n\r\n💰 Price: LKR 500\r\n🎉 Offer Price: LKR 450\r\n📦 In Stock: 1000 units\r\n\r\nEnjoy the purity of White Tea, a gentle and nourishing drink that rejuvenates the body and mind.', '2025-04-10 19:42:25', '2025-04-10 19:42:25', 'active'),
('P250410121', 'SLR6808', 'Brass lamps ', 'Brass Items', 'Brass Lamps', 8000.00, 7800.00, 250, 500.00, '🪔 Brass Lamps – Timeless Elegance for Your Home\r\nIlluminate your space with the warm, classic glow of these Brass Lamps, expertly crafted to add a touch of sophistication and traditional charm to any room. Made from high-quality brass, these lamps bring both beauty and functionality to your home or office.\r\n\r\n🔸 Features:\r\nElegant Brass Construction – Durable and stunning, designed to last for generations\r\n\r\nCategory: Brass Items → Brass Lamps\r\n\r\nWeight: Approx. 500g – Solid and sturdy, yet easy to move\r\n\r\nIdeal For: Living rooms, bedrooms, festive décor, or as a thoughtful gift\r\n\r\n💰 Price: LKR 8000\r\n🎉 Offer Price: LKR 7800\r\n📦 In Stock: 250 units\r\n\r\nBring home a piece of timeless artistry with these finely crafted Brass Lamps – the perfect way to brighten up your space with a touch of elegance.', '2025-04-10 17:38:19', '2025-04-10 17:38:19', 'active'),
('P250410125', 'SLR6255', 'Variety Flavored Tea Bags', 'Ceylon Tea', 'Flavored Tea', 1800.00, 1500.00, 100, 250.00, '🍃 Flavored Tea – A Delightful Twist to Traditional Ceylon Tea\r\nSavor the unique and refreshing taste of Flavored Tea, an exquisite blend of high-quality Ceylon tea leaves with aromatic natural flavors. Sourced from the renowned tea gardens of Sri Lanka, this tea offers a perfect balance of traditional tea taste with a delightful infusion of flavor, making it a wonderful choice for those looking to explore new tea experiences.\r\n\r\n🔸 Features:\r\n\r\nPremium Ceylon Tea – Blended with natural flavors for a refreshing twist\r\n\r\nCategory: Ceylon Tea → Flavored Tea\r\n\r\nMaterial: A blend of high-quality Ceylon tea leaves with aromatic natural flavoring\r\n\r\nWeight: Approx. 250g – Ideal for multiple brews and sharing with friends or family\r\n\r\nIdeal For: Tea lovers who enjoy a flavorful tea experience or want to enjoy something unique and exciting\r\n\r\n💰 Price: LKR 1,800\r\n🎉 Offer Price: LKR 1,500\r\n📦 In Stock: 100 units\r\n\r\nIndulge in the delightful and aromatic Flavored Tea, a perfect blend of tradition and innovation that is sure to elevate your tea time.\r\n\r\n', '2025-04-10 19:44:44', '2025-04-10 19:44:44', 'active'),
('P250410139', 'SLR6808', 'Bathik clothing', 'Batik Products', 'Batik Clothing', 3700.00, 3200.00, 100, 100.00, '👗 Batik Clothing – Wearable Art with Sri Lankan Soul\r\nStep into elegance with this handcrafted Batik Clothing piece, where tradition meets contemporary fashion. Made using time-honored wax-resist dyeing techniques, this garment brings out the vibrant beauty and uniqueness of Sri Lankan batik art.\r\n\r\n🔸 Features:\r\nAuthentic Batik Design – Each piece is one-of-a-kind, featuring bold patterns and rich colors\r\n\r\nCategory: Batik Products → Batik Clothing\r\n\r\nMaterial: Lightweight, breathable fabric perfect for tropical climates\r\n\r\nWeight: Approx. 100g – Comfortable for everyday wear\r\n\r\nIdeal For: Casual outings, cultural events, or stylish gifting\r\n\r\n💰 Price: LKR 3700\r\n🎉 Offer Price: LKR 3200\r\n📦 In Stock: 100 units\r\n\r\nCelebrate tradition in style – make a statement with this uniquely Sri Lankan creation that blends heritage with elegance.\r\n\r\n', '2025-04-10 17:20:47', '2025-04-10 17:20:47', 'active'),
('P250410176', 'SLR8257', 'Chrysoberyl Cat\'s Eye ring', 'Ceylon Gems', 'Cat\'s Eye', 10000.00, 9000.00, 10, 30.00, '💍 Chrysoberyl Cat\'s Eye Ring – A Rare Gemstone with Mystical Appeal\r\nCapture the mystique of the Chrysoberyl Cat\'s Eye, a striking gemstone known for its captivating optical effect that resembles the slit-eye of a cat. This stunning ring, featuring a high-quality Chrysoberyl Cat\'s Eye gemstone, is a true symbol of elegance and uniqueness. Sourced from Sri Lanka, known for its exceptional gemstones, this ring is perfect for those who appreciate rare and eye-catching jewelry.\r\n\r\n🔸 Features:\r\n\r\nChrysoberyl Cat\'s Eye – A rare and mesmerizing gemstone with the iconic \"cat\'s eye\" effect, also known as chatoyancy\r\n\r\nCategory: Ceylon Gems → Cat\'s Eye\r\n\r\nMaterial: High-quality Chrysoberyl gemstone with the distinctive cat\'s eye phenomenon\r\n\r\nWeight: Approx. 30g – A perfect fit for daily wear or special occasions\r\n\r\nIdeal For: Gemstone collectors, those seeking unique jewelry, or anyone who admires rare and mysterious stones\r\n\r\n💰 Price: LKR 10,000\r\n🎉 Offer Price: LKR 9,000\r\n📦 In Stock: 10 units\r\n\r\nAdd an aura of mystery and beauty to your collection with the Chrysoberyl Cat\'s Eye Ring—a true masterpiece that captures the essence of nature’s most intriguing gemstone.\r\n\r\n', '2025-04-10 20:41:07', '2025-04-10 20:41:07', 'active'),
('P250410178', 'SLR6808', 'green tea', 'Ceylon Tea', 'Green Tea', 1000.00, 900.00, 200, 100.00, '🍃 Green Tea – A Refreshing Ceylon Tea Experience\r\nEnjoy the natural goodness of Ceylon Green Tea, a refreshing and healthy beverage that rejuvenates both body and mind. Sourced from the pristine tea gardens of Sri Lanka, this green tea is rich in antioxidants and perfect for those who prefer a lighter, smoother tea with every sip.\r\n\r\n🔸 Features:\r\nAuthentic Ceylon Green Tea – Handpicked from Sri Lanka’s finest plantations\r\n\r\nCategory: Ceylon Tea → Green Tea\r\n\r\nMaterial: High-quality green tea leaves, minimally processed to retain freshness and nutrients\r\n\r\nWeight: Approx. 100g – Ideal for daily tea brewing\r\n\r\nIdeal For: Health-conscious individuals, light tea drinkers, or as a calming drink for relaxation\r\n\r\n💰 Price: LKR 1,000\r\n🎉 Offer Price: LKR 900\r\n📦 In Stock: 200 units\r\n\r\nRejuvenate your senses with the pure and healthy Ceylon Green Tea, a perfect choice for every tea lover.', '2025-04-10 18:19:06', '2025-04-10 18:19:06', 'active'),
('P250410195', 'SLR8257', 'Valuable Silverware', 'Silver Crafts', 'Silver Tableware', 5000.00, 4800.00, 100, 150.00, '🍽️ Valuable Silverware – Elevate Your Dining Experience\r\nIntroducing the Valuable Silverware, a sophisticated addition to your dining table that combines elegance with functionality. Crafted from high-quality silver, this tableware is designed to impress your guests and enhance every meal. Whether you\'re hosting a special event or enjoying an intimate dinner, this silverware set brings timeless beauty to your dining experience.\r\n\r\n🔸 Features:\r\n\r\nMaterial: Premium silver, renowned for its durability, beauty, and classic appeal\r\n\r\nCategory: Silver Crafts → Silver Tableware\r\n\r\nDesign: Elegant and intricate, perfect for both formal and casual dining occasions\r\n\r\nWeight: Approx. 150g – Light yet sturdy enough for everyday use\r\n\r\nIdeal For: Special dinners, celebrations, and as a thoughtful gift for someone special\r\n\r\n💰 Price: LKR 500\r\n🎉 Offer Price: LKR 4800\r\n📦 In Stock: 100 units\r\n\r\nAdd a touch of class to your dining table with the Valuable Silverware—a perfect blend of tradition and sophistication.\r\n\r\n', '2025-04-10 20:45:45', '2025-04-10 20:45:45', 'active'),
('P250410208', 'SLR6808', 'mats', 'Cane Products', 'Cane Mats', 1000.00, 990.00, 399, 500.00, '🧘 Cane Mats – Natural Comfort and Durability\r\nEnhance your living space or outdoor area with these beautifully handcrafted Cane Mats. Made from premium cane material, these mats offer both comfort and durability, perfect for any setting, whether for relaxation, dining, or decoration.\r\n\r\n🔸 Features:\r\nEco-Friendly Cane Material – Durable, natural, and sustainable\r\n\r\nCategory: Cane Products → Cane Mats\r\n\r\nMaterial: High-quality cane woven with precision for strength and style\r\n\r\nWeight: Approx. 500g – Lightweight and easy to move, yet sturdy enough for daily use\r\n\r\nIdeal For: Living rooms, outdoor areas, yoga spaces, or as a decorative floor piece\r\n\r\n💰 Price: LKR 1000\r\n🎉 Offer Price: LKR 990\r\n📦 In Stock: 400 units\r\n\r\nBring natural elegance and functionality into your home or garden with these beautiful Cane Mats – a timeless addition to any space.', '2025-04-10 17:58:30', '2025-04-25 16:43:49', 'active'),
('P250410232', 'SLR8257', 'Certified Natural Star Sapphire', 'Ceylon Gems', 'Star Sapphires', 1400.00, NULL, 3, 20.00, '💎 Certified Natural Star Sapphire – A True Celestial Beauty\r\nDiscover the captivating allure of the Certified Natural Star Sapphire, a rare and beautiful gemstone that showcases nature’s brilliance. This exceptional star sapphire, sourced from Sri Lanka’s famed gemstone mines, features a stunning star-like effect, also known as asterism, visible under direct light. Perfect for collectors or those seeking a unique and natural gemstone, the star sapphire is a true gem of the heavens.\r\n\r\n🔸 Features:\r\n\r\nCertified Natural Star Sapphire – Authentic star sapphire, certified for its quality and origin\r\n\r\nCategory: Ceylon Gems → Star Sapphires\r\n\r\nMaterial: High-quality natural sapphire with a mesmerizing star effect\r\n\r\nWeight: Approx. 20g – Ideal for setting in rings, necklaces, or as a standalone gemstone\r\n\r\nIdeal For: Jewelry enthusiasts, collectors, or those looking for an extraordinary gemstone to treasure\r\n\r\n💰 Price: LKR 1,400\r\n🎉 Offer Price: (Optional)\r\n📦 In Stock: 3 units\r\n\r\nEmbrace the natural wonder of the Certified Natural Star Sapphire, a rare and beautiful gemstone that reflects the stars\' celestial glow.', '2025-04-10 20:39:45', '2025-04-10 20:39:45', 'active'),
('P250410262', 'SLR6808', 'ornaments', 'Brass Items', 'Brass Ornaments', 9900.00, 9000.00, 200, 750.00, '✨ Brass Ornaments – The Perfect Blend of Tradition and Elegance\r\nAdd a touch of timeless beauty to your home with these exquisite Brass Ornaments. Crafted from high-quality brass, each piece is a perfect blend of traditional craftsmanship and modern elegance, making them a standout addition to any space.\r\n\r\n🔸 Features:\r\nPremium Brass Craftsmanship – Handcrafted with precision and attention to detail\r\n\r\nCategory: Brass Items → Brass Ornaments\r\n\r\nMaterial: Solid, durable brass with a polished finish that shines with sophistication\r\n\r\nWeight: Approx. 750g – Solid and impressive, yet versatile for display\r\n\r\nIdeal For: Home décor, office spaces, gifting, and collectors\r\n\r\n💰 Price: LKR 9900\r\n🎉 Offer Price: LKR 9000\r\n📦 In Stock: 200 units\r\n\r\nElevate your surroundings with the elegance of brass, bringing both charm and a cultural touch to your environment.', '2025-04-10 17:41:31', '2025-04-10 17:41:31', 'active'),
('P250410263', 'SLR6255', 'Suburban Curry Seasoning Blend', 'Spices', 'Curry Blends', 900.00, 800.00, 500, 180.00, '🌿 Suburban Curry Seasoning Blend – A Taste of Tradition\r\nEnhance your cooking with the Suburban Curry Seasoning Blend, a premium, aromatic blend of spices that captures the essence of traditional Sri Lankan curry. Perfect for both home cooks and seasoned chefs, this curry blend combines the finest spices to create rich, flavorful dishes that transport you to the heart of Sri Lankan culinary heritage. Whether you\'re preparing a classic curry or experimenting with new recipes, this seasoning blend adds depth and authenticity to every dish.\r\n\r\n🔸 Features:\r\n\r\nAuthentic Sri Lankan Curry Blend – Crafted from the finest local spices to bring out the true taste of Sri Lankan curries\r\n\r\nCategory: Spices → Curry Blends\r\n\r\nMaterial: A perfectly balanced mix of cumin, coriander, turmeric, fennel, and other traditional spices\r\n\r\nWeight: Approx. 180g – Ideal for multiple uses, bringing rich flavors to your meals\r\n\r\nIdeal For: Curry lovers, home cooks, or anyone looking to add an authentic Sri Lankan flavor to their cooking\r\n\r\n💰 Price: LKR 900\r\n🎉 Offer Price: LKR 800 (Optional)\r\n📦 In Stock: 500 units\r\n\r\nSpice up your meals with the Suburban Curry Seasoning Blend, a must-have for anyone passionate about Sri Lankan cuisine. Add flavor and authenticity to your curries, soups, and stews with this exquisite blend!', '2025-04-10 20:04:13', '2025-04-10 20:04:13', 'active'),
('P250410274', 'SLR6255', 'Cinnamon Essential Oils', 'Ceylon Cinnamon', 'Cinnamon Oil', 4000.00, 3200.00, 100, 50.00, '🌿 Cinnamon Essential Oils – Pure Ceylon Cinnamon Essence\r\nDiscover the therapeutic power of Cinnamon Essential Oil, distilled from the finest Ceylon Cinnamon. Known for its soothing and invigorating properties, this essential oil is perfect for aromatherapy, massage, and even as a natural ingredient in skincare products. The warm, spicy fragrance of this oil helps to promote relaxation, uplift mood, and cleanse the air, making it an essential addition to your wellness routine.\r\n\r\n🔸 Features:\r\n\r\n100% Pure Ceylon Cinnamon Essential Oil – Sourced from the best cinnamon farms in Sri Lanka\r\n\r\nCategory: Ceylon Cinnamon → Cinnamon Oil\r\n\r\nMaterial: 100% natural cinnamon oil, carefully extracted to preserve its aromatic and therapeutic benefits\r\n\r\nWeight: Approx. 50g – Perfect size for aromatherapy use or as a natural remedy\r\n\r\nIdeal For: Aromatherapy, skin care, massage, or adding a natural fragrance to your home or workspace\r\n\r\n💰 Price: LKR 4,000\r\n🎉 Offer Price: LKR 3,200 (Optional)\r\n📦 In Stock: 100 units\r\n\r\nEmbrace the natural, invigorating properties of Cinnamon Essential Oil and experience the soothing warmth of pure Ceylon Cinnamon in every drop. Ideal for those seeking a natural remedy or simply a luxurious aromatic experience.', '2025-04-10 20:09:29', '2025-04-10 20:09:29', 'active'),
('P250410289', 'SLR6808', 'cane basket', 'Cane Products', 'Cane Baskets', 5500.00, 5000.00, 500, 250.00, '🧺 Cane Basket – Handcrafted Elegance for Storage and Décor\r\nBring a touch of natural beauty and practicality into your home with this beautifully crafted Cane Basket. Made from high-quality cane, this versatile piece is perfect for storage, organization, or as an eye-catching decorative element in any room.\r\n\r\n🔸 Features:\r\nHandwoven Cane Construction – Strong, durable, and eco-friendly material\r\n\r\nCategory: Cane Products → Cane Baskets\r\n\r\nMaterial: Premium cane with a smooth finish for added charm and longevity\r\n\r\nWeight: Approx. 250g – Light enough for easy handling, sturdy for everyday use\r\n\r\nIdeal For: Storage, organization, or as a stylish home décor piece\r\n\r\n💰 Price: LKR 5500\r\n🎉 Offer Price: LKR 5000\r\n📦 In Stock: 500 units\r\n\r\nOrganize in style with this elegant Cane Basket, designed to add a touch of tradition and eco-friendly charm to your home.\r\n\r\n', '2025-04-10 17:56:08', '2025-04-10 17:56:08', 'active'),
('P250410332', 'SLR6808', 'religious items', 'Brass Items', 'Brass Religious Items', 5000.00, 4800.00, 500, 700.00, '🙏 Brass Religious Items – Sacred Elegance for Your Spiritual Space\r\nEnhance your sacred space with these beautifully crafted Brass Religious Items, designed to bring both reverence and beauty to your home or temple. Made from high-quality brass, each item reflects traditional craftsmanship and spiritual significance, perfect for prayer, meditation, and decoration.\r\n\r\n🔸 Features:\r\nExquisite Brass Craftsmanship – Handcrafted to perfection with intricate details\r\n\r\nCategory: Brass Items → Brass Religious Items\r\n\r\nMaterial: Premium brass with a polished finish for lasting beauty\r\n\r\nWeight: Approx. 700g – Solid and meaningful, ideal for ceremonial use\r\n\r\nIdeal For: Temples, altars, prayer spaces, or as a thoughtful spiritual gift\r\n\r\n💰 Price: LKR 5000\r\n🎉 Offer Price: LKR 4800\r\n📦 In Stock: 500 units\r\n\r\nBring sacredness and serenity into your space with these timeless Brass Religious Items – a perfect blend of tradition, spirituality, and elegance.', '2025-04-10 17:48:35', '2025-04-10 17:48:35', 'active'),
('P250410365', 'SLR6255', 'Cinnamon Powder Pouch', 'Ceylon Cinnamon', 'Cinnamon Powder', 1800.00, NULL, 99, 100.00, '🌿 Cinnamon Powder Pouch – The Essence of Ceylon Cinnamon\r\nUnlock the full flavor potential of your cooking with Cinnamon Powder Pouch. Made from premium, hand-picked Ceylon Cinnamon, this finely ground powder brings the perfect balance of sweet and spicy flavors to your dishes. Whether you\'re baking, making savory curries, or creating warm beverages, this cinnamon powder enhances your recipes with its authentic, rich taste.\r\n\r\n🔸 Features:\r\n\r\nPure Ceylon Cinnamon Powder – Sourced directly from Sri Lanka’s finest cinnamon farms\r\n\r\nCategory: Ceylon Cinnamon → Cinnamon Powder\r\n\r\nMaterial: Finely ground, high-quality cinnamon, carefully processed to preserve its aroma and flavor\r\n\r\nWeight: Approx. 100g – Convenient size for regular use in your kitchen\r\n\r\nIdeal For: Baking, curries, beverages (like chai or mulled wine), and as a natural sweetener in desserts\r\n\r\n💰 Price: LKR 1,800\r\n🎉 Offer Price: [Optional]\r\n📦 In Stock: 100 units\r\n\r\nAdd the authentic, aromatic taste of Ceylon Cinnamon Powder to your pantry and elevate your culinary creations with the finest cinnamon available. Perfect for those who love the rich, true taste of Ceylon.\r\n\r\n', '2025-04-10 20:07:48', '2025-04-25 16:26:03', 'active'),
('P250410374', 'SLR6255', 'Real Ceylon Cinnamon', 'Spices', 'Cinnamon', 2800.00, 2300.00, 500, 50.00, '🌿 Cinnamon – The Warmth of Sri Lanka\'s Finest Spice\r\nExperience the natural warmth and aromatic richness of Cinnamon, a premium spice sourced from the heart of Sri Lanka, known globally for its distinct and flavorful essence. This high-quality cinnamon is carefully harvested and prepared to bring out the perfect balance of sweetness and spice, making it an essential ingredient in both savory and sweet dishes.\r\n\r\n🔸 Features:\r\n\r\nPremium Ceylon Cinnamon – Sourced from Sri Lanka’s finest cinnamon plantations\r\n\r\nCategory: Spices → Cinnamon\r\n\r\nMaterial: High-quality ground cinnamon, packed to retain flavor and freshness\r\n\r\nWeight: Approx. 1000g – Ideal for long-term use, whether for cooking or baking\r\n\r\nIdeal For: Culinary enthusiasts, those who enjoy adding depth to dishes, or as a gift for spice lovers\r\n\r\n💰 Price: LKR 2,800\r\n🎉 Offer Price: LKR 2,300 (Optional)\r\n📦 In Stock: 500 units\r\n\r\nEnhance your cooking experience with Cinnamon, a versatile and aromatic spice perfect for adding a fragrant, warming note to your meals, desserts, and drinks.', '2025-04-10 19:48:21', '2025-04-10 19:48:21', 'active'),
('P250410386', 'SLR6808', 'Bathik cushion covers ', 'Batik Products', 'Batik Cushion Covers', 1000.00, NULL, 500, 100.00, '🛋️ Batik Cushion Covers – Add a Touch of Tradition to Your Home\r\nTransform your living space with these Batik Cushion Covers, a perfect fusion of traditional Sri Lankan craftsmanship and contemporary décor. Each cover is beautifully hand-dyed using authentic batik techniques, bringing vibrant colors and intricate patterns to your home.\r\n\r\n🔸 Features:\r\nHandcrafted Batik Art – Traditional wax-resist dyeing method ensures unique, one-of-a-kind designs\r\n\r\nCategory: Batik Products → Batik Cushion Covers\r\n\r\nMaterial: Soft, durable fabric designed for comfort and long-lasting use\r\n\r\nWeight: Approx. 100g – Light and easy to handle\r\n\r\nIdeal For: Sofas, beds, chairs, or as thoughtful cultural gifts\r\n\r\n💰 Price: LKR 1000\r\n📦 In Stock: 500 units\r\n\r\nBring elegance and color to your interiors with these vibrant, handcrafted cushion covers – a true testament to Sri Lankan artistic heritage.', '2025-04-10 17:30:35', '2025-04-10 17:30:35', 'active'),
('P250410394', 'SLR8257', 'pink ceylon sapphire ring', 'Ceylon Gems', 'Pink Sapphires', 20000.00, 19800.00, 20, 20.00, '💍 Pink Ceylon Sapphire Ring – A Touch of Elegance and Romance\r\nIndulge in the captivating charm of the Pink Ceylon Sapphire Ring, a true symbol of elegance and romance. Sourced from the renowned sapphire mines of Sri Lanka, this stunning ring features a beautifully cut pink sapphire that sparkles with a soft, radiant glow. Perfect for engagements, special occasions, or as a statement piece in any jewelry collection, this ring is a timeless treasure.\r\n\r\n🔸 Features:\r\n\r\nAuthentic Ceylon Pink Sapphire – Sourced from Sri Lanka, known for its rare and exceptional pink sapphires\r\n\r\nCategory: Ceylon Gems → Pink Sapphires\r\n\r\nMaterial: High-quality, natural pink sapphire with a soft, radiant hue\r\n\r\nWeight: Approx. 20g – Comfortable yet substantial for daily wear\r\n\r\nIdeal For: Engagements, anniversary gifts, or as a unique addition to your jewelry collection\r\n\r\n💰 Price: LKR 20,000\r\n🎉 Offer Price: LKR 19,800 (Optional)\r\n📦 In Stock: 20 units\r\n\r\nLet the Pink Ceylon Sapphire Ring enchant you with its delicate beauty, offering a perfect balance of luxury, elegance, and timeless appeal.', '2025-04-10 20:38:29', '2025-04-10 20:38:29', 'active'),
('P250410395', 'SLR6255', 'Palm Jaggery Kithul Hakuru ', 'Kithul Products', 'Kithul Jaggery', 500.00, 480.00, 100, 500.00, '🌿 Palm Jaggery Kithul Hakuru – The Pure Taste of Nature\r\nIndulge in the authentic, wholesome sweetness of Palm Jaggery Kithul Hakuru, a traditional Sri Lankan delicacy made from the sap of the Kithul palm tree. This pure, unrefined jaggery is a healthier alternative to processed sugars, offering a rich flavor and natural sweetness to your recipes. Ideal for sweetening tea, desserts, or enjoying on its own, Kithul Hakuru is a must-have in your kitchen for a taste of Sri Lankan heritage.\r\n\r\n🔸 Features:\r\n\r\nAuthentic Kithul Palm Jaggery – Made from 100% pure Kithul sap, hand-harvested from the Kithul palm tree\r\n\r\nCategory: Kithul Products → Kithul Jaggery\r\n\r\nMaterial: Pure Kithul sap, no artificial additives or preservatives, offering a naturally rich sweetness\r\n\r\nWeight: Approx. 500g – Perfect for home use or as a thoughtful gift\r\n\r\nIdeal For: Health-conscious individuals, fans of natural sweeteners, and those seeking authentic Sri Lankan flavors\r\n\r\n💰 Price: LKR 500\r\n🎉 Offer Price: LKR 480 (Optional)\r\n📦 In Stock: 100 units\r\n\r\nEnjoy the pure, traditional sweetness of Palm Jaggery Kithul Hakuru and embrace the natural goodness that comes from the heart of Sri Lanka. A perfect addition to your daily life or as a gift to those who appreciate authentic, healthy alternatives.', '2025-04-10 20:25:41', '2025-04-10 20:25:41', 'active'),
('P250410413', 'SLR6808', 'coconut shell jewelry', 'Traditional Jewelry', 'Coconut Shell Jewelry', 850.00, 800.00, 400, 100.00, '🥥 Coconut Shell Jewelry – Sustainable Beauty and Tradition\r\nCelebrate eco-friendly elegance with our Coconut Shell Jewelry, handcrafted with natural coconut shells. These pieces not only showcase the beauty of nature but also carry a rich cultural heritage, perfect for adding a touch of rustic charm to any outfit.\r\n\r\n🔸 Features:\r\nEco-Friendly Craftsmanship – Made from natural coconut shells, creating unique and sustainable jewelry\r\n\r\nCategory: Traditional Jewelry → Coconut Shell Jewelry\r\n\r\nMaterial: Premium coconut shell, polished for a smooth finish with intricate designs\r\n\r\nWeight: Approx. 100g – Lightweight and comfortable for everyday wear\r\n\r\nIdeal For: Casual wear, nature-inspired fashion, or as a unique gift\r\n\r\n💰 Price: LKR 850\r\n🎉 Offer Price: LKR 800\r\n📦 In Stock: 400 units\r\n\r\nEmbrace sustainable style with our Coconut Shell Jewelry, designed to bring natural elegance and cultural charm to your collection.', '2025-04-10 18:14:28', '2025-04-10 18:14:28', 'active'),
('P250410415', 'SLR6808', 'decorative items', 'Cane Products', 'Cane Decorative Items', 15000.00, 14500.00, 150, 700.00, '🌿 Cane Decorative Items – Handcrafted Beauty for Your Home\r\nAdd a touch of natural elegance to your home with these exquisite Cane Decorative Items. Handcrafted using traditional techniques, each piece is a unique work of art that brings both functionality and beauty to any space in your home.\r\n\r\n🔸 Features:\r\nPremium Cane Craftsmanship – Expertly handwoven for intricate detail and lasting durability\r\n\r\nCategory: Cane Products → Cane Decorative Items\r\n\r\nMaterial: High-quality cane, designed for both visual appeal and sturdiness\r\n\r\nWeight: Approx. 700g – Lightweight yet solid, ideal for display in any room\r\n\r\nIdeal For: Living rooms, bedrooms, office spaces, or as a thoughtful gift\r\n\r\n💰 Price: LKR 15,000\r\n🎉 Offer Price: LKR 14,500\r\n📦 In Stock: 150 units\r\n\r\nBring the warmth and natural charm of handcrafted cane décor into your home with these stunning Cane Decorative Items – perfect for adding an artistic touch to any space.', '2025-04-10 18:02:30', '2025-04-10 18:02:30', 'active'),
('P250410424', 'SLR6808', 'Decorative masks', 'Traditional Masks', 'Decorative Masks', 5700.00, 5000.00, 148, 250.00, '🎨 Decorative Traditional Mask – Art That Tells a Story\r\nAdd elegance and culture to your living space with this beautifully crafted Decorative Mask, inspired by Sri Lanka\'s vibrant traditional art. Designed to bring character and charm to any setting, this mask is perfect for those who appreciate handcrafted detail and cultural beauty.\r\n\r\n🔸 Features:\r\nStylized Traditional Design – A modern decorative take on Sri Lankan mask heritage\r\n\r\nCategory: Traditional Masks → Decorative Masks\r\n\r\nMaterial: Expertly hand-carved and painted wood\r\n\r\nWeight: Approx. 250g – durable and display-friendly\r\n\r\nIdeal For: Wall décor, art collections, cultural gifts, and interior design\r\n\r\n💰 Price: LKR 5700\r\n🎉 Offer Price: LKR 5000\r\n📦 In Stock: 150 units\r\n\r\nTransform your space with the bold colors and intricate artistry of Sri Lanka\'s mask-making tradition — a timeless decorative treasure.\r\n\r\n', '2025-04-10 17:16:28', '2025-04-25 16:26:03', 'active'),
('P250410425', 'SLR6255', 'Kithul Flour 100g ', 'Kithul Products', 'Kithul Flour', 1300.00, 1200.00, 500, 100.00, '🌿 Kithul Flour 100g – Nature’s Gift in Every Grain\r\nDiscover the wholesome goodness of Kithul Flour, a traditional flour made from the sap of the Kithul palm tree. This gluten-free, nutrient-rich flour is a versatile ingredient that can be used in a variety of recipes, from baking to cooking. Whether you\'re making pancakes, cakes, or traditional Sri Lankan desserts, Kithul Flour offers a naturally sweet flavor and a healthier alternative to regular flour.\r\n\r\n🔸 Features:\r\n\r\nAuthentic Kithul Flour – Made from 100% natural Kithul sap, ensuring purity and rich taste\r\n\r\nCategory: Kithul Products → Kithul Flour\r\n\r\nMaterial: Pure Kithul sap, processed into flour for easy use in cooking and baking\r\n\r\nWeight: Approx. 100g – Convenient size for testing or incorporating into recipes\r\n\r\nIdeal For: Gluten-free diet enthusiasts, health-conscious individuals, or anyone looking to explore traditional Sri Lankan ingredients\r\n\r\n💰 Price: LKR 1300\r\n🎉 Offer Price: LKR 1200 (Optional)\r\n📦 In Stock: 500 units\r\n\r\nBring the natural sweetness and rich flavor of Kithul Flour into your kitchen. It’s the perfect choice for healthier baking and cooking, allowing you to enjoy the essence of Sri Lanka in every bite!\r\n\r\n', '2025-04-10 20:29:41', '2025-04-10 20:29:41', 'active'),
('P250410433', 'SLR6255', 'Virgin Coconut Oil', 'Coconut Products', 'Coconut Oil', 1400.00, 1200.00, 50, 750.00, '🥥 Virgin Coconut Oil – Pure and Natural Ceylon Goodness\r\nExperience the natural benefits of Virgin Coconut Oil, extracted from fresh coconuts grown in the sun-kissed fields of Sri Lanka. This cold-pressed, unrefined oil is packed with essential nutrients, offering a variety of health and beauty benefits. Perfect for cooking, skincare, and hair care, this versatile oil is a must-have for your daily routine.\r\n\r\n🔸 Features:\r\n\r\n100% Pure Virgin Coconut Oil – Cold-pressed from fresh, organic coconuts to retain maximum nutrients\r\n\r\nCategory: Coconut Products → Coconut Oil\r\n\r\nMaterial: 100% natural, unrefined coconut oil with no additives or preservatives\r\n\r\nWeight: Approx. 750g – Ideal for regular use in cooking, skincare, and hair care\r\n\r\nIdeal For: Health-conscious individuals, those seeking a natural skincare remedy, or for use in traditional cooking\r\n\r\n💰 Price: LKR 1,400\r\n🎉 Offer Price: LKR 1,200 (Optional)\r\n📦 In Stock: 50 units\r\n\r\nNourish your body and skin with the goodness of Virgin Coconut Oil – a versatile and natural product that enhances your health and beauty regimen.\r\n\r\n', '2025-04-10 20:13:03', '2025-04-10 20:13:03', 'active'),
('P250410439', 'SLR6808', 'Kitchenware', 'Brass Items', 'Brass Kitchenware', 2500.00, 2300.00, 300, 300.00, '🍽️ Brass Kitchenware – Elegant and Functional\r\nUpgrade your kitchen with these beautifully crafted Brass Kitchenware pieces, combining durability and elegance in one. Made from high-quality brass, each item is designed to enhance your cooking experience while adding a touch of tradition to your kitchen.\r\n\r\n🔸 Features:\r\nPremium Brass Construction – Sturdy, long-lasting, and perfect for daily use\r\n\r\nCategory: Brass Items → Brass Kitchenware\r\n\r\nMaterial: Solid brass with a polished finish for both style and strength\r\n\r\nWeight: Approx. 300g – Light enough for convenience, durable enough for longevity\r\n\r\nIdeal For: Cooking, serving, or as decorative kitchen accents\r\n\r\n💰 Price: LKR 2500\r\n🎉 Offer Price: LKR 2300\r\n📦 In Stock: 300 units\r\n\r\nBring home the elegance and functionality of brass in your kitchen – the perfect blend of heritage and practicality.', '2025-04-10 17:44:39', '2025-04-10 17:44:39', 'active'),
('P250410484', 'SLR8257', 'Handicraft Products ', 'Other', '', 250.00, NULL, 100, 500.00, '🎨 Handicraft Products – Unique Artisanal Creations\r\nDiscover the charm of handmade Handicraft Products, each piece crafted with care and attention to detail. These versatile items are perfect for adding a touch of tradition, elegance, and personal flair to your home, office, or as thoughtful gifts for loved ones. Made by skilled artisans, these handicrafts reflect the rich cultural heritage and creativity of Sri Lanka.\r\n\r\n🔸 Features:\r\n\r\nMaterial: High-quality materials, skillfully crafted into unique pieces\r\n\r\nCategory: Other Products → Handicrafts\r\n\r\nIdeal For: Home décor, office embellishment, gifts for special occasions\r\n\r\nWeight: Approx. 500g – Easy to display and carry\r\n\r\n💰 Price: LKR 250\r\n🎉 Offer Price: LKR 230 (Optional)\r\n📦 In Stock: 100 units\r\n\r\nEmbrace the beauty of traditional craftsmanship with these Handicraft Products that bring a personal touch to every space.\r\n\r\n', '2025-04-10 21:02:02', '2025-04-10 21:09:16', 'active'),
('P250410503', 'SLR6255', 'Green Cardamom', 'Spices', 'Cardamom', 1200.00, 1000.00, 800, 200.00, '🌿 Green Cardamom – The Queen of Spices\r\nAdd a touch of exotic flavor with Green Cardamom, a premium spice known for its aromatic, sweet, and slightly spicy taste. This high-quality cardamom is sourced from the lush plantations of Sri Lanka, ensuring that every pod contains the purest, most fragrant essence. A staple in both savory dishes and sweet desserts, this cardamom enhances any recipe with its unique flavor and fragrance.\r\n\r\n🔸 Features:\r\n\r\nPremium Ceylon Green Cardamom – Sourced from Sri Lanka’s top plantations\r\n\r\nCategory: Spices → Cardamom\r\n\r\nMaterial: Whole green cardamom pods, packed to preserve their freshness and aromatic qualities\r\n\r\nWeight: Approx. 200g – Perfect for daily use or for special culinary creations\r\n\r\nIdeal For: Cooking, baking, or making fragrant teas and drinks\r\n\r\n💰 Price: LKR 1,200\r\n🎉 Offer Price: LKR 1,000 (Optional)\r\n📦 In Stock: 800 units\r\n\r\nBring the sweet, fragrant flavor of Green Cardamom into your kitchen and elevate your cooking and beverages with the finest Sri Lankan spice.', '2025-04-10 19:55:27', '2025-04-10 19:55:27', 'active'),
('P250410528', 'SLR8257', 'SUREKHA BLUE SAPPHIRE FULLSET', 'Ceylon Gems', 'Blue Sapphires', 22000.00, 20000.00, 5, 50.00, '💎 SUREKHA BLUE SAPPHIRE FULLSET – The Timeless Elegance of Ceylon Sapphires\r\nExperience the unparalleled beauty and luxury of SUREKHA BLUE SAPPHIRE FULLSET, a stunning collection of genuine Ceylon Blue Sapphires. Sourced from Sri Lanka, the world-renowned origin of the finest sapphires, this set offers the perfect blend of tradition, elegance, and exquisite craftsmanship. Each sapphire is handpicked for its clarity, color, and brilliance, making this full set a timeless addition to your jewelry collection.\r\n\r\n🔸 Features:\r\n\r\nAuthentic Ceylon Blue Sapphires – Sourced directly from the legendary sapphire mines of Sri Lanka\r\n\r\nCategory: Ceylon Gems → Blue Sapphires\r\n\r\nMaterial: Natural blue sapphires with a brilliant deep blue hue\r\n\r\nWeight: Approx. 50g – Ideal for creating exquisite jewelry pieces\r\n\r\nIdeal For: Gemstone collectors, luxury jewelry enthusiasts, or as a perfect gift for special occasions\r\n\r\n💰 Price: LKR 22,000\r\n🎉 Offer Price: LKR 20,000 (Optional)\r\n📦 In Stock: 5 units\r\n\r\nIndulge in the luxurious charm of SUREKHA BLUE SAPPHIRE FULLSET and elevate your collection with the mesmerizing brilliance of genuine Ceylon Blue Sapphires.\r\n\r\n', '2025-04-10 20:37:04', '2025-04-10 20:37:04', 'active'),
('P250410532', 'SLR6255', 'Ceylon Kithul Honey', 'Kithul Products', 'Kithul Honey', 1300.00, 1250.00, 200, 100.00, '🍯 Ceylon Kithul Honey – Pure Sweetness from Nature\r\nIndulge in the natural sweetness of Ceylon Kithul Honey, a premium honey harvested from the nectar of the Kithul palm tree. This raw, unprocessed honey is renowned for its rich flavor, smooth texture, and numerous health benefits. Sourced from the pristine environments of Sri Lanka, it’s a perfect addition to your daily diet, whether used in beverages, desserts, or as a healthy spread.\r\n\r\n🔸 Features:\r\n\r\nAuthentic Ceylon Kithul Honey – Pure, raw honey harvested from the nectar of the Kithul palm tree in Sri Lanka\r\n\r\nCategory: Kithul Products → Kithul Honey\r\n\r\nMaterial: 100% natural Kithul nectar, packed with essential vitamins and antioxidants\r\n\r\nWeight: Approx. 100g – Convenient for daily use or as a gift\r\n\r\nIdeal For: Health-conscious individuals, natural sweetener lovers, or anyone looking for a healthier alternative to processed sugar\r\n\r\n💰 Price: LKR 1300\r\n🎉 Offer Price: LKR 1250 (Optional)\r\n📦 In Stock: 200 units\r\n\r\nExperience the golden richness of Ceylon Kithul Honey and bring the authentic taste of Sri Lanka into your home. Its natural sweetness and health benefits make it a perfect choice for daily consumption.', '2025-04-10 20:31:02', '2025-04-10 20:31:02', 'active'),
('P250410539', 'SLR8257', 'Leather Traveling Bag ', 'Leather Products', 'Leather Bags', 4800.00, 4500.00, 100, 500.00, '👜 Leather Traveling Bag – Stylish and Durable for Your Journeys\r\nThe Leather Traveling Bag is the ultimate combination of style, durability, and practicality. Crafted from premium leather, this bag is designed to accompany you on both short trips and long travels. Its spacious interior, coupled with sleek exterior detailing, ensures that you travel in style while keeping your essentials organized.\r\n\r\n🔸 Features:\r\n\r\nMaterial: High-quality genuine leather, known for its durability and timeless appeal\r\n\r\nCategory: Leather Products → Leather Bags\r\n\r\nDesign: Classic and spacious design with comfortable handles and a sturdy zipper closure\r\n\r\nWeight: Approx. 500g – Lightweight yet durable for all your travel needs\r\n\r\nIdeal For: Travelers, frequent flyers, or as a stylish weekender bag\r\n\r\nColor: Rich, natural leather tones that develop a beautiful patina over time\r\n\r\n💰 Price: LKR 4,800\r\n🎉 Offer Price: LKR 4,500\r\n📦 In Stock: 100 units\r\n\r\nWhether you\'re heading on a weekend getaway or a longer journey, the Leather Traveling Bag provides the perfect blend of convenience and elegance for your travels.', '2025-04-10 20:49:43', '2025-04-10 20:49:43', 'active'),
('P250410564', 'SLR6255', 'Kithul Palm Treacle', 'Kithul Products', 'Kithul Treacle', 1400.00, 1350.00, 500, 700.00, '🍯 Kithul Palm Treacle – A Natural Sweetener from Sri Lanka\r\nExperience the natural sweetness and rich flavor of Kithul Palm Treacle, a premium product harvested from the sap of the Kithul palm tree. Known for its authentic taste and health benefits, this treacle is an excellent alternative to refined sugars. Perfect for sweetening desserts, beverages, or drizzling over pancakes, Kithul Palm Treacle adds a deliciously unique touch to your culinary creations.\r\n\r\n🔸 Features:\r\n\r\nAuthentic Kithul Palm Treacle – Made from 100% pure Kithul sap, hand-harvested to preserve its natural flavors and nutrients\r\n\r\nCategory: Kithul Products → Kithul Treacle\r\n\r\nMaterial: Pure Kithul sap with no additives or preservatives, a healthier and natural option for sweetening your food\r\n\r\nWeight: Approx. 700g – Ideal for home use or as a gift for those who appreciate natural sweeteners\r\n\r\nIdeal For: Health-conscious individuals, those looking for natural sugar alternatives, and fans of Sri Lankan traditional products\r\n\r\n💰 Price: LKR 1,400\r\n🎉 Offer Price: LKR 1,350 (Optional)\r\n📦 In Stock: 500 units\r\n\r\nAdd a touch of Sri Lankan tradition to your kitchen with Kithul Palm Treacle. A versatile, naturally sweet syrup that enhances your food with a rich, authentic flavor while offering the goodness of nature.', '2025-04-10 20:20:31', '2025-04-10 20:20:31', 'active'),
('P250410578', 'SLR6808', 'silvere filigree', 'Traditional Jewelry', 'Silver Filigree', 2500.00, NULL, 200, 500.00, '🌟 Silver Filigree – Intricate Craftsmanship and Timeless Elegance\r\nDiscover the beauty of Silver Filigree, a stunning jewelry piece that blends delicate artistry with traditional craftsmanship. Each item is meticulously handcrafted with fine silver wire, creating intricate patterns that exude both elegance and charm, perfect for any occasion.\r\n\r\n🔸 Features:\r\nExquisite Silver Craftsmanship – Handcrafted with precision, showcasing fine filigree detailing\r\n\r\nCategory: Traditional Jewelry → Silver Filigree\r\n\r\nMaterial: Premium silver, polished to perfection for a gleaming finish\r\n\r\nWeight: Approx. 500g – Solid and elegant, with a weight that reflects its quality\r\n\r\nIdeal For: Special occasions, formal events, or as a unique gift\r\n\r\n💰 Price: LKR 2,500\r\n📦 In Stock: 200 units\r\n\r\nEmbrace the timeless elegance of Silver Filigree and make a statement with these beautifully crafted jewelry pieces, perfect for those who appreciate intricate design and traditional craftsmanship.', '2025-04-10 18:08:29', '2025-04-10 18:08:29', 'active'),
('P250410589', 'SLR8257', 'Export Potential of Clayware ', 'Traditional Pottery', 'Clay Cookware', 500.00, 480.00, 600, 20.00, '🍲 Export Potential of Clayware – Traditional Clay Cookware for Culinary Excellence\r\nThe Export Potential of Clayware offers premium handcrafted clay cookware that brings a rustic and authentic touch to your kitchen. Made using traditional techniques, this clay cookware is perfect for those who appreciate the charm and flavor-enhancing properties of clay. Ideal for slow-cooked meals and an earthy cooking experience, this cookware is designed to elevate any culinary endeavor.\r\n\r\n🔸 Features:\r\n\r\nMaterial: Handcrafted clay, designed for heat retention and flavor enhancement\r\n\r\nCategory: Traditional Pottery → Clay Cookware\r\n\r\nIdeal For: Slow cooking, stews, curries, and all-natural cooking methods\r\n\r\nWeight: Approx. 20g – Lightweight and easy to handle\r\n\r\nHandmade: Each piece is uniquely crafted with attention to detail\r\n\r\nDurable: Designed for long-lasting use, easy to maintain\r\n\r\n💰 Price: LKR 500\r\n🎉 Offer Price: LKR 480\r\n📦 In Stock: 600 units\r\n\r\nBring the timeless tradition of clay cooking into your home with the Export Potential of Clayware and enjoy the natural flavor infusion that only clay can provide.\r\n\r\n', '2025-04-10 20:55:33', '2025-04-10 20:55:33', 'active'),
('P250410593', 'SLR6808', 'cane furniture', 'Cane Products', 'Cane Furniture', 50000.00, 48000.00, 100, 10000.00, '🪑 Cane Furniture – Elegant Craftsmanship for Your Home\r\nAdd natural beauty and timeless elegance to your home with this meticulously handcrafted Cane Furniture. Combining traditional weaving techniques with modern design, these pieces offer both comfort and style, making them perfect for any living space.\r\n\r\n🔸 Features:\r\nHigh-Quality Cane Material – Strong, durable, and lightweight, ideal for long-lasting use\r\n\r\nCategory: Cane Products → Cane Furniture\r\n\r\nMaterial: Premium cane with a smooth finish, designed for both aesthetics and comfort\r\n\r\nWeight: Approx. 10,000g – Sturdy and reliable, yet easy to move\r\n\r\nIdeal For: Living rooms, patios, balconies, or as a statement piece in any home\r\n\r\n💰 Price: LKR 50,000\r\n🎉 Offer Price: LKR 48,000\r\n📦 In Stock: 100 units\r\n\r\nEmbrace the natural charm of handcrafted cane furniture – a perfect fusion of heritage, functionality, and design for any space in your home.', '2025-04-10 17:52:57', '2025-04-10 17:52:57', 'active'),
('P250410612', 'SLR6808', 'kolam mask', 'Traditional Masks', 'Kolam Masks', 4800.00, 4300.00, 100, 255.00, '🎭 Kolam Mask – A Celebration of Sri Lankan Folk Art\r\nImmerse yourself in the rich tradition of Sri Lankan folk theatre with this stunning Kolam Mask – a masterpiece that reflects the humor, drama, and storytelling of the island\'s cultural heritage.\r\n\r\n🔸 Features:\r\nTraditional Design – Inspired by characters from Sri Lanka’s ancient Kolam dance drama\r\n\r\nCategory: Traditional Masks\r\n\r\nMaterial: Expertly hand-carved and hand-painted wood\r\n\r\nWeight: Approx. 255g – sturdy yet easy to display\r\n\r\nPerfect For: Cultural décor, collectors, themed events, and art lovers\r\n\r\n💰 Price: LKR 4800\r\n🎉 Offer Price: LKR 4300\r\n📦 In Stock: 100 units\r\n\r\nOwn a piece of folklore that tells a story with every curve and color. Ideal as a unique gift or a centerpiece for your traditional collection.', '2025-04-10 16:58:44', '2025-04-10 16:58:44', 'active'),
('P250410622', 'SLR6255', ' Planet\'s Pick', 'Coconut Products', 'Coir Products', 250.00, 200.00, 999, 300.00, '🌍 Planet\'s Pick – Eco-Friendly Coir Products for a Sustainable Future\r\nIntroducing Planet\'s Pick, an eco-conscious collection of Coir Products designed to promote sustainability and protect the environment. Made from natural coir fibers sourced from coconut husks, these products are biodegradable, durable, and ideal for various uses in gardening, home décor, and more. By choosing Planet\'s Pick, you contribute to reducing environmental impact while enjoying the practical benefits of natural materials.\r\n\r\n🔸 Features:\r\n\r\nEco-Friendly Coir Products – Made from sustainable coconut coir, offering an environmentally responsible alternative to synthetic materials\r\n\r\nCategory: Coir Products\r\n\r\nMaterial: Natural coir fibers, harvested from the husks of coconuts, ensuring strength and durability\r\n\r\nWeight: Approx. 300g – Lightweight and easy to handle for various applications\r\n\r\nIdeal For: Eco-conscious individuals, gardeners, and those seeking sustainable alternatives for everyday use\r\n\r\n💰 Price: LKR 250\r\n🎉 Offer Price: LKR 200 (Optional)\r\n📦 In Stock: 1,000 units\r\n\r\nSupport a greener planet with Planet\'s Pick – the perfect choice for sustainable living. Whether for your garden or home décor, these natural coir products offer versatility and durability without compromising the environment.\r\n\r\n', '2025-04-10 20:14:55', '2025-04-18 05:38:37', 'active'),
('P250410638', 'SLR6808', 'Bathik table linen', 'Batik Products', 'Batik Table Linen', 3500.00, NULL, 250, 250.00, '🍽️ Batik Table Linen – Elevate Your Dining with Artful Elegance\r\nBring a touch of traditional charm to your dining experience with this beautifully handcrafted Batik Table Linen. Merging functionality with the timeless beauty of Sri Lankan batik art, it’s perfect for those who appreciate style and culture at the table.\r\n\r\n🔸 Features:\r\nAuthentic Batik Design – Hand-dyed using traditional wax-resist techniques\r\n\r\nCategory: Batik Products → Batik Table Linen\r\n\r\nMaterial: Durable and easy-to-clean fabric with rich, vibrant patterns\r\n\r\nWeight: Approx. 250g – Lightweight yet sturdy\r\n\r\nIdeal For: Everyday use, festive meals, gifting, or enhancing your table décor\r\n\r\n💰 Price: LKR 3500\r\n📦 In Stock: 250 units\r\n\r\nTurn every meal into a celebration of culture and color with this elegant batik table linen – a blend of heritage and modern living.', '2025-04-10 17:26:44', '2025-04-10 17:26:44', 'active'),
('P250410655', 'SLR8257', 'Arts and Crafts', 'Traditional Pottery', 'Ceremonial Pottery', 1200.00, NULL, 600, 300.00, '🎨 Arts and Crafts – Exquisite Ceremonial Pottery\r\nDiscover the beauty and tradition of Sri Lankan craftsmanship with the Arts and Crafts ceremonial pottery. Designed with intricate artistry, this piece is perfect for special occasions, cultural ceremonies, or as a decorative showpiece. Each item is crafted with care to bring both functionality and elegance to your home.\r\n\r\n🔸 Features:\r\n\r\nMaterial: High-quality ceramic clay for durability and aesthetic appeal\r\n\r\nCategory: Traditional Pottery → Ceremonial Pottery\r\n\r\nIdeal For: Special occasions, home décor, or cultural ceremonies\r\n\r\nWeight: Approx. 300g – Easy to handle and display\r\n\r\nArtistic Design: Carefully crafted with detailed patterns, showcasing the rich heritage of Sri Lankan pottery\r\n\r\n💰 Price: LKR 1,200\r\n🎉 Offer Price: LKR 1,000 (Optional)\r\n📦 In Stock: 600 units\r\n\r\nAdd a touch of tradition and elegance to your home or ceremony with Arts and Crafts ceremonial pottery – a perfect blend of art and culture.', '2025-04-10 20:59:16', '2025-04-10 20:59:16', 'active'),
('P250410678', 'SLR6808', 'raksha mask', 'Traditional Masks', 'Raksha Masks', 5000.00, 4500.00, 500, 100.00, '🛡️ Raksha Mask – Traditional Sri Lankan Craftsmanship\r\nAdd a touch of Sri Lankan heritage to your space with this beautifully handcrafted Raksha Mask, a symbol of protection and vibrant cultural expression.\r\n\r\n🔸 Features:\r\nAuthentic Traditional Design – Represents ancient Sri Lankan demons used in cultural rituals and dances\r\n\r\nCategory: Traditional Masks → Raksha Masks\r\n\r\nMaterial: Carefully hand-painted wood with intricate detailing\r\n\r\nDimensions & Weight: Approx. 100g – lightweight and easy to hang\r\n\r\nIdeal For: Home décor, cultural gifts, collectors, and art enthusiasts\r\n\r\n💰 Price: LKR 5000\r\n🎉 Offer Price: LKR 4500 (Limited Time!)\r\n📦 In Stock: 500 units\r\n\r\nBring home a piece of Lankan tradition that wards off evil and adds artistic flair to any setting.', '2025-04-10 16:56:05', '2025-04-10 16:56:05', 'active');
INSERT INTO `products` (`product_id`, `seller_id`, `product_name`, `main_category`, `sub_category`, `price`, `offer_price`, `quantity`, `weight`, `description`, `created_at`, `updated_at`, `status`) VALUES
('P250410685', 'SLR6255', 'peper', 'Spices', 'Pepper', 800.00, NULL, 50, 250.00, '🌶️ Pepper – The King of Spices\r\nAdd a burst of flavor to your meals with Pepper, a must-have spice in every kitchen. Known as the \"King of Spices,\" this premium-quality black pepper is carefully sourced from the best farms in Sri Lanka, renowned for producing some of the finest pepper in the world. Whether you\'re seasoning your dishes or enhancing your culinary creations, this pepper is a perfect choice for any kitchen.\r\n\r\n🔸 Features:\r\n\r\nPremium Ceylon Pepper – Sourced from the finest Sri Lankan plantations\r\n\r\nCategory: Spices → Pepper\r\n\r\nMaterial: High-quality whole black peppercorns, freshly packed to preserve flavor\r\n\r\nWeight: Approx. 250g – Ideal for daily cooking or as a gift for spice enthusiasts\r\n\r\nIdeal For: Cooking, seasoning, or adding an extra kick to your favorite dishes\r\n\r\n💰 Price: LKR 800\r\n🎉 Offer Price: LKR 750 (Optional)\r\n📦 In Stock: 100 units\r\n\r\nBring a touch of spice to your meals with the finest Pepper, and enjoy the true essence of Sri Lankan flavor in every dish.', '2025-04-10 19:46:20', '2025-04-10 20:01:54', 'active'),
('P250410688', 'SLR8257', 'Leather Straps Archives ', 'Leather Products', 'Leather Accessories', 800.00, 750.00, 500, 10.00, '👜 Leather Straps Archives – Premium Quality Leather Straps for Versatile Use\r\nThe Leather Straps Archives offers high-quality leather straps, perfect for a wide range of uses from crafting to fashion accessories. Made from durable leather, these straps are designed to provide both strength and style, whether for bags, wallets, or other leather projects. Ideal for crafters and DIY enthusiasts, these straps offer versatility and a touch of luxury.\r\n\r\n🔸 Features:\r\n\r\nMaterial: Durable, high-quality genuine leather\r\n\r\nCategory: Leather Products → Leather Accessories\r\n\r\nDesign: Smooth finish with sturdy stitching for durability\r\n\r\nWeight: Approx. 10g – Lightweight for easy handling\r\n\r\nIdeal For: Crafting, bag repairs, fashion accessories, or leather goods production\r\n\r\nLength: Perfect for customization, cut to your desired size\r\n\r\n💰 Price: LKR 800\r\n🎉 Offer Price: LKR 750\r\n📦 In Stock: 500 units\r\n\r\nElevate your projects with the Leather Straps Archives, offering premium leather straps that combine style and functionality.', '2025-04-10 20:54:04', '2025-04-10 20:54:04', 'active'),
('P250410718', 'SLR6808', 'Bathik wall hanging', 'Batik Products', 'Batik Wall Hangings', 6000.00, 5500.00, 249, 300.00, '🖼️ Batik Wall Hanging – A Touch of Tradition for Your Walls\r\nEnhance your home or office with this stunning Batik Wall Hanging, a perfect blend of art, culture, and creativity. Handcrafted using traditional Sri Lankan batik techniques, each piece tells a story through color, texture, and design.\r\n\r\n🔸 Features:\r\nHandmade Batik Artwork – Created using authentic wax-resist dyeing on high-quality fabric\r\n\r\nCategory: Batik Products → Batik Wall Hangings\r\n\r\nUnique Design – Vibrant patterns and motifs inspired by nature, mythology, and local culture\r\n\r\nWeight: Approx. 300g – Lightweight and easy to hang\r\n\r\nIdeal For: Living rooms, offices, cultural spaces, or as a thoughtful gift\r\n\r\n💰 Price: LKR 6000\r\n🎉 Offer Price: LKR 5500\r\n📦 In Stock: 250 units\r\n\r\nBring warmth and heritage to your walls with this eye-catching batik masterpiece – a timeless expression of Sri Lankan craftsmanship.', '2025-04-10 17:24:05', '2025-04-10 18:24:32', 'active'),
('P250410738', 'SLR8257', 'Glazed Ceramic pot ', 'Traditional Pottery', 'Glazed Pottery', 2600.00, 2400.00, 50, 750.00, '🏺 Glazed Ceramic Pot – Elegant Traditional Pottery\r\nEnhance your space with the beauty and charm of the Glazed Ceramic Pot. This exquisite pottery is carefully glazed to achieve a polished, lustrous finish, making it a perfect addition to any home or garden. Ideal for holding plants or simply as a stunning decorative piece, it embodies the skillful craftsmanship of Sri Lankan traditional pottery.\r\n\r\n🔸 Features:\r\n\r\nMaterial: Premium ceramic clay, beautifully glazed for a glossy finish\r\n\r\nCategory: Traditional Pottery → Glazed Pottery\r\n\r\nIdeal For: Home décor, gardens, or as a gift for pottery lovers\r\n\r\nWeight: Approx. 750g – Sturdy yet easy to handle\r\n\r\nDesign: Featuring intricate glaze details, each piece is unique, adding sophistication to your surroundings\r\n\r\n💰 Price: LKR 2,600\r\n🎉 Offer Price: LKR 2,400 (Optional)\r\n📦 In Stock: 50 units\r\n\r\nBring timeless elegance to your space with the Glazed Ceramic Pot – a beautiful work of art that blends tradition and style.', '2025-04-10 21:00:39', '2025-04-10 21:00:39', 'active'),
('P250410775', 'SLR8257', 'Ceylob_Leather_wallet', 'Leather Products', 'Leather Wallets', 1300.00, NULL, 100, 20.00, '👜 Ceylob Leather Wallet – Stylish and Durable Leather Accessory\r\nThe Ceylob Leather Wallet combines practicality with elegance, offering a premium leather accessory for everyday use. Crafted from high-quality leather, this wallet is designed to hold your cards, cash, and other essentials in style, while ensuring long-lasting durability. Perfect for those who appreciate refined, simple accessories.\r\n\r\n🔸 Features:\r\n\r\nMaterial: Genuine leather, providing a soft and smooth texture\r\n\r\nCategory: Leather Products → Leather Accessories\r\n\r\nDesign: Sleek and compact design, ideal for daily use\r\n\r\nWeight: Approx. 20g – Lightweight and convenient to carry\r\n\r\nIdeal For: Men and women looking for a stylish wallet with ample storage\r\n\r\nCompartments: Multiple card slots and a cash pocket\r\n\r\n💰 Price: LKR 1,300\r\n🎉 Offer Price: LKR 1,200\r\n📦 In Stock: 100 units\r\n\r\nKeep your essentials secure and organized in style with the Ceylob Leather Wallet, the perfect blend of form and function.\r\n\r\n', '2025-04-10 20:52:20', '2025-04-10 20:52:36', 'active'),
('P250410788', 'SLR8257', 'Red Clay Curry Pot', 'Traditional Pottery', 'Clay Cookware', 150.00, 120.00, 600, 230.00, '🍲 Red Clay Curry Pot – Traditional Sri Lankan Cookware\r\nExperience the authenticity of Sri Lankan cooking with the Red Clay Curry Pot. This traditional clay pot is perfect for preparing aromatic curries, stews, and other Sri Lankan delicacies. Made from high-quality red clay, it retains heat efficiently and ensures even cooking, enhancing the flavors of your dish.\r\n\r\n🔸 Features:\r\n\r\nMaterial: Durable red clay, perfect for slow cooking\r\n\r\nCategory: Traditional Pottery → Clay Cookware\r\n\r\nIdeal For: Curry lovers, cooking enthusiasts, and those who appreciate traditional cookware\r\n\r\nWeight: Approx. 230g – Conveniently lightweight for easy handling\r\n\r\nTraditional Design: Adds an authentic touch to your kitchen\r\n\r\nHeat Retention: The clay’s natural properties make it ideal for slow-cooked dishes\r\n\r\n💰 Price: LKR 150\r\n🎉 Offer Price: LKR 120\r\n📦 In Stock: 600 units\r\n\r\nEnhance your culinary experience with the Red Clay Curry Pot, a must-have in every kitchen for preparing delicious and flavorful meals.', '2025-04-10 20:57:47', '2025-04-10 20:57:47', 'active'),
('P250410828', 'SLR6255', ' Coconut Shell Elephant ', 'Coconut Products', 'Coconut Shell Crafts', 1300.00, 100.00, 100, 100.00, '🐘 Coconut Shell Elephant – Handcrafted Elegance in Every Detail\r\nAdd a touch of nature and artistry to your space with the Coconut Shell Elephant. Skillfully crafted from sustainably sourced coconut shells, this unique decorative piece embodies the perfect blend of traditional craftsmanship and eco-friendly materials. Ideal as a gift or a home accent, this elephant figurine brings a sense of elegance and cultural richness to any setting.\r\n\r\n🔸 Features:\r\n\r\nHandcrafted Coconut Shell Elephant – Made from 100% natural coconut shells, each piece is carefully carved by skilled artisans to showcase the beauty of traditional craftsmanship\r\n\r\nCategory: Coconut Products → Coconut Shell Crafts\r\n\r\nMaterial: High-quality, sustainably sourced coconut shells, creating a durable and eco-friendly décor item\r\n\r\nWeight: Approx. 100g – Lightweight yet durable for easy display or gifting\r\n\r\nIdeal For: Nature lovers, collectors of unique home décor, and eco-conscious individuals seeking sustainable art pieces\r\n\r\n💰 Price: LKR 1,300\r\n🎉 Offer Price: LKR 100 (Optional)\r\n📦 In Stock: 100 units\r\n\r\nBring home a piece of nature’s beauty with the Coconut Shell Elephant, a stunning symbol of artistry and eco-conscious design. Perfect for adding charm to your home or gifting to someone special.\r\n\r\n', '2025-04-10 20:17:01', '2025-04-10 20:17:01', 'active'),
('P250410852', 'SLR8257', '4MM Lara Gent\'s Chain', 'Silver Crafts', 'Silver Jewelry', 400.00, 280.00, 100, 20.00, '🔗 4MM Lara Gent\'s Chain – Elegant Silver Jewelry for Men\r\nIntroducing the 4MM Lara Gent\'s Chain, a sleek and stylish piece of silver jewelry designed for the modern man. Crafted with precision, this chain is made from high-quality silver, offering a polished and refined look. Whether worn alone or paired with a pendant, it adds sophistication to any outfit. The 4mm width gives it a perfect balance of boldness and subtlety, making it an ideal accessory for both casual and formal occasions.\r\n\r\n🔸 Features:\r\n\r\nMaterial: Premium quality silver, ensuring durability and a lasting shine\r\n\r\nCategory: Silver Crafts → Silver Jewelry\r\n\r\nDesign: Simple, elegant 4mm width with smooth, sleek links\r\n\r\nWeight: Approx. 20g – Lightweight yet sturdy for daily wear\r\n\r\nIdeal For: Men looking for versatile, stylish jewelry that complements a variety of outfits\r\n\r\n💰 Price: LKR 400\r\n🎉 Offer Price: LKR 280\r\n📦 In Stock: 100 units\r\n\r\nAdd a touch of elegance and sophistication to your accessory collection with the 4MM Lara Gent\'s Chain—a timeless piece designed to enhance your personal style.\r\n\r\n', '2025-04-10 20:42:34', '2025-04-10 20:42:34', 'active'),
('P250410854', 'SLR8257', 'Oxidised Silver Elegant Jewellery Set', 'Silver Crafts', 'Silver Ornaments', 2800.00, 2500.00, 50, 150.00, '💍 Oxidised Silver Elegant Jewellery Set – Timeless Beauty and Grace\r\nDiscover the allure of timeless elegance with the Oxidised Silver Elegant Jewellery Set. This exquisite set features a carefully crafted design, perfect for those who appreciate classic beauty with a touch of modern sophistication. Made from high-quality oxidised silver, this jewellery set is perfect for any special occasion or everyday elegance. The oxidised finish gives it a vintage appeal, making it a versatile accessory to complement both traditional and contemporary outfits.\r\n\r\n🔸 Features:\r\n\r\nMaterial: Premium oxidised silver, known for its durability and unique finish\r\n\r\nCategory: Silver Crafts → Silver Ornaments\r\n\r\nDesign: Intricate craftsmanship with a vintage touch, ideal for adding sophistication to your look\r\n\r\nWeight: Approx. 150g – Substantial and elegant without being overly heavy\r\n\r\nIdeal For: Special occasions, weddings, parties, or for those who love unique and beautiful jewellery\r\n\r\n💰 Price: LKR 2800\r\n🎉 Offer Price: LKR 2500\r\n📦 In Stock: 50 units\r\n\r\nElevate your jewellery collection with the Oxidised Silver Elegant Jewellery Set—a versatile, beautiful set that adds a graceful touch to any ensemble.', '2025-04-10 20:44:10', '2025-04-10 20:44:10', 'active'),
('P250410856', 'SLR6808', 'sanni mask', 'Traditional Masks', 'Sanni Masks', 4000.00, 3800.00, 98, 200.00, '😈 Sanni Mask – Symbol of Healing & Tradition\r\nStep into the mystical world of ancient Sri Lankan rituals with this handcrafted Sanni Mask, a powerful representation of folklore and spiritual healing. Traditionally used in exorcism ceremonies (Sanni Yakuma), these masks are both visually striking and culturally significant.\r\n\r\n🔸 Features:\r\nAuthentic Traditional Design – Represents one of the 18 \"Sanni Yaka\" demons from Sri Lankan healing rituals\r\n\r\nCategory: Traditional Masks → Sanni Masks\r\n\r\nHandmade Artistry – Crafted and painted by skilled artisans using traditional techniques\r\n\r\nMaterial & Weight: Lightweight wood, approx. 200g\r\n\r\nPerfect For: Cultural décor, collectors, spiritual spaces, or heritage-themed gifts\r\n\r\n💰 Price: LKR 4000\r\n🎉 Offer Price: LKR 3800\r\n📦 In Stock: 100 units\r\n\r\nBring home a piece of history and tradition – a stunning conversation piece with deep cultural roots.\r\n\r\n', '2025-04-10 17:11:29', '2025-04-25 16:26:03', 'active'),
('P250410864', 'SLR8257', ' Silver Palm Leaf Book Covers', 'Silver Crafts', 'Silver Religious Items', 8000.00, 7800.00, 100, 400.00, '📖 Silver Palm Leaf Book Covers – A Touch of Tradition and Elegance\r\nThe Silver Palm Leaf Book Covers are exquisite, handcrafted pieces that blend the timeless beauty of silver with the rich cultural heritage of palm leaf designs. Ideal for safeguarding your treasured books or religious scriptures, these covers add a unique and elegant touch to your collection. Each piece is meticulously crafted to reflect the intricate patterns of palm leaves, symbolizing protection and wisdom.\r\n\r\n🔸 Features:\r\n\r\nMaterial: High-quality silver with intricate palm leaf designs, showcasing fine craftsmanship\r\n\r\nCategory: Silver Crafts → Silver Religious Items\r\n\r\nDesign: Elegant and traditional, perfect for religious texts, personal diaries, or cherished books\r\n\r\nWeight: Approx. 400g – Solid and durable for long-lasting protection\r\n\r\nIdeal For: Religious book protection, special gifts, and collectors of unique religious items\r\n\r\n💰 Price: LKR 8,000\r\n🎉 Offer Price: LKR 7,800\r\n📦 In Stock: 100 units\r\n\r\nAdd a meaningful and beautiful cover to your cherished books with the Silver Palm Leaf Book Covers, combining culture, elegance, and durability.', '2025-04-10 20:48:05', '2025-04-10 20:48:05', 'active'),
('P250410919', 'SLR8257', 'Unique Artisanal Creations', 'Other', '', 500.00, 480.00, 100, 150.00, '🎨 Handicraft Products – Unique Artisanal Creations\r\nDiscover the charm of handmade Handicraft Products, each piece crafted with care and attention to detail. These versatile items are perfect for adding a touch of tradition, elegance, and personal flair to your home, office, or as thoughtful gifts for loved ones. Made by skilled artisans, these handicrafts reflect the rich cultural heritage and creativity of Sri Lanka.\r\n\r\n🔸 Features:\r\n\r\nMaterial: High-quality materials, skillfully crafted into unique pieces\r\n\r\nCategory: Other Products → Handicrafts\r\n\r\nIdeal For: Home décor, office embellishment, gifts for special occasions\r\n\r\nWeight: Approx. 500g – Easy to display and carry\r\n\r\n💰 Price: LKR 250\r\n🎉 Offer Price: LKR 230 (Optional)\r\n📦 In Stock: 100 units\r\n\r\nEmbrace the beauty of traditional craftsmanship with these Handicraft Products that bring a personal touch to every space.\r\n\r\n', '2025-04-10 21:13:29', '2025-04-10 21:13:29', 'active'),
('P250410929', 'SLR8257', 'kolam mask', 'Traditional Masks', 'Kolam Masks', 4000.00, 3200.00, 19, 20.00, '🎭 Kolam Mask – A Vibrant Representation of Sri Lankan Culture\r\nThe Kolam Mask is a traditional piece of craftsmanship that reflects the rich cultural heritage of Sri Lanka. Known for its vibrant colors and intricate designs, this mask is typically used in cultural performances to depict various characters. Made from high-quality materials, it showcases the unique artistry of Sri Lankan artisans, bringing a touch of tradition and beauty to any space.\r\n\r\n🔸 Features:\r\n\r\nMaterial: Premium quality wood and painted with vibrant, long-lasting colors\r\n\r\nCategory: Traditional Masks → Kolam Masks\r\n\r\nIdeal For: Cultural enthusiasts, home décor, collectors, or as a unique gift\r\n\r\nWeight: Approx. 20g – Lightweight and easy to hang or display\r\n\r\n💰 Price: LKR 4,000\r\n🎉 Offer Price: LKR 3,200 (Optional)\r\n📦 In Stock: 20 units\r\n\r\nBring home the colorful and meaningful Kolam Mask, a piece that carries both cultural significance and artistic flair.\r\n\r\n', '2025-04-10 21:15:28', '2025-04-18 05:38:37', 'active'),
('P250410954', 'SLR6255', ' NutriFlair Organic Ceylon Cinnamon Supplement', 'Ceylon Cinnamon', 'Cinnamon Supplements', 25000.00, 24500.00, 100, 1200.00, '🌿 NutriFlair Organic Ceylon Cinnamon Supplement – Premium Health Support\r\nElevate your health and wellness routine with NutriFlair Organic Ceylon Cinnamon Supplement. Sourced from the finest Ceylon Cinnamon found in Sri Lanka, this supplement is packed with powerful antioxidants and anti-inflammatory properties to support your overall health. Rich in natural compounds, it helps to promote a healthy metabolism, support heart health, and regulate blood sugar levels. Incorporate this premium supplement into your daily regimen for a natural boost.\r\n\r\n🔸 Features:\r\n\r\nOrganic Ceylon Cinnamon – Pure, high-quality cinnamon grown in Sri Lanka, known for its authenticity and health benefits\r\n\r\nCategory: Ceylon Cinnamon → Cinnamon Supplements\r\n\r\nMaterial: Organic cinnamon powder encapsulated in easy-to-swallow capsules, ensuring maximum absorption and potency\r\n\r\nWeight: Approx. 1200g – Provides a sufficient supply for long-term health support\r\n\r\nIdeal For: Those looking to support healthy metabolism, regulate blood sugar levels, or enhance overall wellness\r\n\r\n💰 Price: LKR 25,000\r\n🎉 Offer Price: LKR 24,500 (Optional)\r\n📦 In Stock: 100 units\r\n\r\nBoost your health with the natural goodness of Ceylon Cinnamon in every capsule. NutriFlair Organic Ceylon Cinnamon Supplement is the perfect choice for anyone seeking the health benefits of this powerful, organic spice.', '2025-04-10 20:10:58', '2025-04-10 20:10:58', 'active'),
('P250410956', 'SLR6255', 'Ceylon Cinnamon Sticks', 'Ceylon Cinnamon', 'Cinnamon Sticks', 2000.00, 1800.00, 500, 250.00, '🌿 Ceylon Cinnamon Sticks – Pure and Authentic Spice\r\nBring the rich, aromatic flavor of Ceylon Cinnamon Sticks into your kitchen and elevate your cooking with one of the world\'s most treasured spices. Known for their high quality and distinct, sweet-spicy flavor, these cinnamon sticks are carefully sourced from Sri Lanka\'s renowned cinnamon plantations. Perfect for adding depth to your curries, stews, desserts, and even beverages like tea or mulled wine.\r\n\r\n🔸 Features:\r\n\r\nPure Ceylon Cinnamon Sticks – Sourced directly from Sri Lanka’s finest cinnamon farms\r\n\r\nCategory: Ceylon Cinnamon → Cinnamon Sticks\r\n\r\nMaterial: Whole, unbroken cinnamon sticks, packed fresh to preserve their natural flavor and aroma\r\n\r\nWeight: Approx. 250g – Ideal for regular use or for adding that authentic cinnamon touch to your dishes\r\n\r\nIdeal For: Flavoring curries, desserts, teas, or for creating fragrant infusions\r\n\r\n💰 Price: LKR 2,000\r\n🎉 Offer Price: LKR 1,800 (Optional)\r\n📦 In Stock: 500 units\r\n\r\nAdd the warmth and distinctive flavor of Ceylon Cinnamon Sticks to your pantry and experience the true taste of this premium spice from Sri Lanka in every dish.', '2025-04-10 20:06:17', '2025-04-10 20:06:17', 'active'),
('P250410967', 'SLR6808', 'beaded jewelry', 'Traditional Jewelry', 'Beaded Jewelry', 1000.00, 990.00, 500, 200.00, '🌸 Beaded Jewelry – Colorful Elegance for Every Occasion\r\nAdd a pop of color and charm to your look with our handcrafted Beaded Jewelry. Carefully designed with vibrant beads, each piece is a statement of style, tradition, and elegance. Perfect for casual or formal wear, this jewelry is sure to enhance any outfit.\r\n\r\n🔸 Features:\r\nHandcrafted Design – Intricate beading techniques that create unique and stunning pieces\r\n\r\nCategory: Traditional Jewelry → Beaded Jewelry\r\n\r\nMaterial: High-quality, colorful beads that shine with vibrant hues\r\n\r\nWeight: Approx. 200g – Lightweight and comfortable to wear\r\n\r\nIdeal For: Casual outfits, festivals, special occasions, or as a thoughtful gift\r\n\r\n💰 Price: LKR 1,000\r\n🎉 Offer Price: LKR 990\r\n📦 In Stock: 500 units\r\n\r\nBrighten up your jewelry collection with these elegant Beaded Jewelry pieces, designed to add a burst of color and tradition to any wardrobe.', '2025-04-10 18:11:47', '2025-04-10 18:11:47', 'active'),
('P250410975', 'SLR6808', 'black tea ', 'Ceylon Tea', 'Black Tea', 2500.00, 2000.00, 500, 100.00, '🍃 Black Tea – A Rich and Bold Ceylon Tea Experience\r\nIndulge in the rich flavors of authentic Ceylon Black Tea, carefully handpicked from the lush plantations of Sri Lanka. Known for its bold taste and smooth aroma, this premium tea is perfect for those who appreciate a full-bodied cup to start their day or unwind in the evening.\r\n\r\n🔸 Features:\r\nAuthentic Ceylon Tea – Sourced from the renowned tea plantations of Sri Lanka\r\n\r\nCategory: Ceylon Tea → Black Tea\r\n\r\nMaterial: High-quality tea leaves, processed to maintain their rich flavor and aroma\r\n\r\nWeight: Approx. 100g – Perfect for daily brewing\r\n\r\nIdeal For: Enjoying with or without milk, morning or evening tea rituals\r\n\r\n💰 Price: LKR 2,500\r\n🎉 Offer Price: LKR 2,000\r\n📦 In Stock: 500 units\r\n\r\nExperience the authentic taste of Ceylon Black Tea and savor the bold, invigorating flavor in every cup.\r\n\r\n\r\n', '2025-04-10 18:17:10', '2025-04-10 18:17:10', 'active'),
('P250410983', 'SLR6255', 'Tetra Coconut Milk/Cream', 'Coconut Products', 'Coconut Milk & Cream', 2400.00, 2300.00, 150, 500.00, '🥥 Tetra Coconut Milk/Cream – The Rich, Creamy Essence of Coconuts\r\nIndulge in the natural richness and smooth texture of Tetra Coconut Milk/Cream, perfect for elevating your culinary creations. Made from the finest coconuts, this premium product brings the authentic taste of Ceylon to your kitchen. Whether you’re preparing curries, desserts, smoothies, or beverages, this coconut milk/cream adds depth and flavor to your dishes with every drop.\r\n\r\n🔸 Features:\r\n\r\nAuthentic Ceylon Coconut Milk/Cream – Made from fresh, high-quality coconuts, carefully extracted to maintain the creamy texture and natural flavor\r\n\r\nCategory: Coconut Products → Coconut Milk & Cream\r\n\r\nMaterial: 100% pure coconut milk/cream, with no artificial additives or preservatives\r\n\r\nWeight: Approx. 500g – Convenient and ready-to-use for your cooking needs\r\n\r\nIdeal For: Home cooks, professional chefs, and anyone looking to add a deliciously creamy coconut flavor to their meals\r\n\r\n💰 Price: LKR 2,400\r\n🎉 Offer Price: LKR 2,300 (Optional)\r\n📦 In Stock: 150 units\r\n\r\nEnhance your recipes with the Tetra Coconut Milk/Cream – the perfect choice for rich, velvety coconut flavor. Whether for savory dishes or sweet treats, this product brings the true taste of Sri Lanka right to your kitchen.\r\n\r\n', '2025-04-10 20:18:27', '2025-04-10 20:18:27', 'active'),
('P250410997', 'SLR8257', 'Pottery of Sri Lanka', 'Traditional Pottery', 'Decorative Pottery', 1600.00, 1400.00, 100, 45.00, '🌿 Pottery of Sri Lanka – Handcrafted Decorative Pottery for Your Home\r\nDiscover the artistry and heritage of Sri Lankan craftsmanship with the Pottery of Sri Lanka. This collection of handcrafted decorative pottery pieces is designed to bring a touch of traditional elegance to any home. Made using centuries-old techniques, each piece is a unique work of art that reflects the rich cultural heritage of Sri Lanka.\r\n\r\n🔸 Features:\r\n\r\nMaterial: High-quality clay, crafted with traditional methods\r\n\r\nCategory: Traditional Pottery → Decorative Pottery\r\n\r\nIdeal For: Home decor, gifting, and adding a rustic touch to your living space\r\n\r\nWeight: Approx. 45g – Lightweight and perfect for display\r\n\r\nHandmade: Each piece is meticulously handmade, showcasing skilled craftsmanship\r\n\r\nUnique Design: Adds a traditional yet elegant vibe to any space\r\n\r\n💰 Price: LKR 1,600\r\n🎉 Offer Price: LKR 1,400\r\n📦 In Stock: 100 units\r\n\r\nBring the timeless beauty of Sri Lankan pottery into your home with the Pottery of Sri Lanka collection, a perfect blend of tradition and artistry.', '2025-04-10 20:56:35', '2025-04-10 20:56:35', 'active'),
('P250410998', 'SLR6808', 'kandyan jewelry', 'Traditional Jewelry', 'Kandyan Jewelry', 10000.00, NULL, 199, 250.00, '💎 Kandyan Jewelry – A Glimpse of Sri Lanka\'s Rich Tradition\r\nCelebrate Sri Lanka\'s cultural heritage with this stunning Kandyan Jewelry, meticulously crafted to reflect the elegance and grace of traditional Kandyan designs. With intricate detailing and timeless beauty, each piece is a masterpiece that speaks to centuries of craftsmanship and royal legacy.\r\n\r\n🔸 Features:\r\nAuthentic Kandyan Design – Handcrafted with intricate patterns and traditional motifs\r\n\r\nCategory: Traditional Jewelry → Kandyan Jewelry\r\n\r\nMaterial: High-quality metals and stones, polished for a radiant finish\r\n\r\nWeight: Approx. 250g – Solid yet comfortable to wear\r\n\r\nIdeal For: Weddings, festivals, cultural events, or as a treasured heirloom\r\n\r\n💰 Price: LKR 10,000\r\n📦 In Stock: 200 units\r\n\r\nAdd a touch of royal elegance and cultural richness to your jewelry collection with these exquisite Kandyan Jewelry pieces – a true representation of Sri Lankan heritage.', '2025-04-10 18:05:20', '2025-04-10 18:28:02', 'active'),
('P250425799', 'SLR8578', 'asdsad', 'Traditional Masks', 'Kolam Masks', 500.00, 300.00, 100, 500.00, 'afdasfdaf', '2025-04-25 16:27:45', '2025-04-25 16:27:45', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `product_comments`
--

CREATE TABLE `product_comments` (
  `comment_id` int(11) NOT NULL,
  `product_id` varchar(36) NOT NULL,
  `customer_id` varchar(36) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_comments`
--

INSERT INTO `product_comments` (`comment_id`, `product_id`, `customer_id`, `parent_id`, `comment`, `created_at`, `updated_at`) VALUES
(146, 'P250410424', 'CUS08802', NULL, 'good', '2025-04-10 17:32:16', '2025-04-10 17:32:16'),
(147, 'P250410424', 'CUS50858', NULL, 'quality badu', '2025-04-10 20:32:36', '2025-04-10 20:32:36'),
(148, 'P250410424', 'CUS50858', 146, 'mila kiyada', '2025-04-10 20:32:42', '2025-04-10 20:32:42'),
(149, 'P250410484', 'CUS50858', NULL, 'mru', '2025-04-17 15:06:33', '2025-04-17 15:06:33'),
(150, 'P250410424', 'CUS95253', NULL, 'hello', '2025-04-25 16:23:39', '2025-04-25 16:23:39'),
(151, 'P250410424', 'CUS95253', 150, 'huththoo', '2025-04-25 16:23:45', '2025-04-25 16:23:45');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` varchar(10) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_path`, `is_primary`, `upload_date`) VALUES
(354, 'P250410678', 'uploads/products/P2504106783_67f7f82541ca1.jpg', 0, '2025-04-10 16:56:05'),
(355, 'P250410678', 'uploads/products/P2504106783_67f7f8254235d.jpg', 0, '2025-04-10 16:56:05'),
(356, 'P250410612', 'uploads/products/P2504106121_67f7f8c45921b.jpg', 0, '2025-04-10 16:58:44'),
(357, 'P250410612', 'uploads/products/P2504106121_67f7f8c4594ba.jpg', 0, '2025-04-10 16:58:44'),
(358, 'P250410856', 'uploads/products/P2504108569_67f7fbc1ebd31.jpg', 0, '2025-04-10 17:11:29'),
(359, 'P250410424', 'uploads/products/P2504104249_67f7fcec02313.jpg', 0, '2025-04-10 17:16:28'),
(360, 'P250410424', 'uploads/products/P2504104249_67f7fcec02d46.jpg', 0, '2025-04-10 17:16:28'),
(361, 'P250410139', 'uploads/products/P2504101397_67f7fdef60a25.jpg', 0, '2025-04-10 17:20:47'),
(362, 'P250410139', 'uploads/products/P2504101397_67f7fdef60bf0.jpg', 0, '2025-04-10 17:20:47'),
(363, 'P250410718', 'uploads/products/P2504107184_67f7feb58a511.jpg', 0, '2025-04-10 17:24:05'),
(364, 'P250410718', 'uploads/products/P2504107184_67f7feb58ac79.jpg', 0, '2025-04-10 17:24:05'),
(365, 'P250410718', 'uploads/products/P2504107184_67f7feb58aecb.jpg', 0, '2025-04-10 17:24:05'),
(366, 'P250410638', 'uploads/products/P2504106385_67f7ff544a8d2.jpg', 0, '2025-04-10 17:26:44'),
(367, 'P250410638', 'uploads/products/P2504106385_67f7ff544aaad.jpg', 0, '2025-04-10 17:26:44'),
(368, 'P250410386', 'uploads/products/P2504103865_67f8003ba69ae.jpeg', 0, '2025-04-10 17:30:35'),
(369, 'P250410386', 'uploads/products/P2504103865_67f8003ba70b5.jpg', 0, '2025-04-10 17:30:35'),
(370, 'P250410121', 'uploads/products/P2504101215_67f8020b7aefe.jpg', 0, '2025-04-10 17:38:19'),
(371, 'P250410121', 'uploads/products/P2504101215_67f8020b7b0cf.jpg', 0, '2025-04-10 17:38:19'),
(372, 'P250410121', 'uploads/products/P2504101215_67f8020b7cbd2.jpg', 0, '2025-04-10 17:38:19'),
(373, 'P250410121', 'uploads/products/P2504101215_67f8020b7cffe.jpg', 0, '2025-04-10 17:38:19'),
(374, 'P250410262', 'uploads/products/P2504102623_67f802cb70bc8.jpg', 0, '2025-04-10 17:41:31'),
(375, 'P250410262', 'uploads/products/P2504102623_67f802cb70e25.jpg', 0, '2025-04-10 17:41:31'),
(376, 'P250410439', 'uploads/products/P2504104395_67f80387ea219.jpg', 0, '2025-04-10 17:44:39'),
(377, 'P250410439', 'uploads/products/P2504104395_67f80387ea95f.jpg', 0, '2025-04-10 17:44:39'),
(378, 'P250410332', 'uploads/products/P2504103320_67f80473244e1.jpg', 0, '2025-04-10 17:48:35'),
(379, 'P250410332', 'uploads/products/P2504103320_67f80473246f6.jpg', 0, '2025-04-10 17:48:35'),
(380, 'P250410593', 'uploads/products/P2504105934_67f80579c86a6.jpg', 0, '2025-04-10 17:52:57'),
(381, 'P250410593', 'uploads/products/P2504105934_67f80579c8884.jpg', 0, '2025-04-10 17:52:57'),
(382, 'P250410593', 'uploads/products/P2504105934_67f80579c8a3b.jpg', 0, '2025-04-10 17:52:57'),
(383, 'P250410289', 'uploads/products/P2504102899_67f8063802c2e.jpg', 0, '2025-04-10 17:56:08'),
(384, 'P250410289', 'uploads/products/P2504102899_67f806380338e.jpg', 0, '2025-04-10 17:56:08'),
(385, 'P250410289', 'uploads/products/P2504102899_67f8063803585.jpg', 0, '2025-04-10 17:56:08'),
(386, 'P250410289', 'uploads/products/P2504102899_67f806380371b.jpg', 0, '2025-04-10 17:56:08'),
(387, 'P250410208', 'uploads/products/P2504102086_67f806c6a7c47.jpg', 0, '2025-04-10 17:58:30'),
(388, 'P250410208', 'uploads/products/P2504102086_67f806c6a808b.jpg', 0, '2025-04-10 17:58:30'),
(389, 'P250410415', 'uploads/products/P2504104152_67f807b687200.jpg', 0, '2025-04-10 18:02:30'),
(390, 'P250410415', 'uploads/products/P2504104152_67f807b68742e.jpg', 0, '2025-04-10 18:02:30'),
(391, 'P250410998', 'uploads/products/P2504109987_67f80860d1126.png', 0, '2025-04-10 18:05:20'),
(392, 'P250410998', 'uploads/products/P2504109987_67f80860d19ec.jpg', 0, '2025-04-10 18:05:20'),
(393, 'P250410578', 'uploads/products/P2504105784_67f8091dee55e.jpg', 0, '2025-04-10 18:08:29'),
(394, 'P250410578', 'uploads/products/P2504105784_67f8091dee7a4.jpg', 0, '2025-04-10 18:08:29'),
(395, 'P250410578', 'uploads/products/P2504105784_67f8091dee928.jpg', 0, '2025-04-10 18:08:29'),
(396, 'P250410967', 'uploads/products/P2504109675_67f809e3dfdeb.jpg', 0, '2025-04-10 18:11:47'),
(397, 'P250410967', 'uploads/products/P2504109675_67f809e3e0062.jpg', 0, '2025-04-10 18:11:47'),
(398, 'P250410413', 'uploads/products/P2504104136_67f80a841cb05.jpg', 0, '2025-04-10 18:14:28'),
(399, 'P250410413', 'uploads/products/P2504104136_67f80a841cd10.jpg', 0, '2025-04-10 18:14:28'),
(400, 'P250410975', 'uploads/products/P2504109758_67f80b2660a34.jpg', 0, '2025-04-10 18:17:10'),
(401, 'P250410975', 'uploads/products/P2504109758_67f80b2661375.jpg', 0, '2025-04-10 18:17:10'),
(402, 'P250410178', 'uploads/products/P2504101785_67f80b9a5dc32.jpg', 0, '2025-04-10 18:19:06'),
(403, 'P250410117', 'uploads/products/P2504101179_67f81f212d0e9.jpg', 0, '2025-04-10 19:42:25'),
(404, 'P250410117', 'uploads/products/P2504101179_67f81f212ddb1.jpg', 0, '2025-04-10 19:42:25'),
(405, 'P250410125', 'uploads/products/P2504101253_67f81facbaad2.jpg', 0, '2025-04-10 19:44:44'),
(406, 'P250410125', 'uploads/products/P2504101253_67f81facbae37.jpg', 0, '2025-04-10 19:44:44'),
(407, 'P250410685', 'uploads/products/P2504106856_67f8200c1fb30.jpg', 0, '2025-04-10 19:46:20'),
(408, 'P250410374', 'uploads/products/P2504103747_67f82085a5894.jpg', 0, '2025-04-10 19:48:21'),
(409, 'P250410374', 'uploads/products/P2504103747_67f82085a5bb7.jpg', 0, '2025-04-10 19:48:21'),
(410, 'P250410503', 'uploads/products/P2504105033_67f8222f3d46d.jpg', 0, '2025-04-10 19:55:27'),
(411, 'P250410263', 'uploads/products/P2504102630_67f8243d57041.jpg', 0, '2025-04-10 20:04:13'),
(412, 'P250410263', 'uploads/products/P2504102630_67f8243d57aad.jpg', 0, '2025-04-10 20:04:13'),
(413, 'P250410956', 'uploads/products/P2504109566_67f824b99d472.jpg', 0, '2025-04-10 20:06:17'),
(414, 'P250410365', 'uploads/products/P2504103651_67f825148c138.jpg', 0, '2025-04-10 20:07:48'),
(415, 'P250410274', 'uploads/products/P2504102746_67f82579df6bc.jpg', 0, '2025-04-10 20:09:29'),
(416, 'P250410954', 'uploads/products/P2504109549_67f825d289b26.jpg', 0, '2025-04-10 20:10:58'),
(417, 'P250410433', 'uploads/products/P2504104330_67f8264f1f656.jpg', 0, '2025-04-10 20:13:03'),
(418, 'P250410433', 'uploads/products/P2504104330_67f8264f1faf1.jpg', 0, '2025-04-10 20:13:03'),
(419, 'P250410622', 'uploads/products/P2504106229_67f826bfe1932.jpg', 0, '2025-04-10 20:14:55'),
(420, 'P250410828', 'uploads/products/P2504108281_67f8273d2a668.jpg', 0, '2025-04-10 20:17:01'),
(421, 'P250410828', 'uploads/products/P2504108281_67f8273d2a90e.jpg', 0, '2025-04-10 20:17:01'),
(422, 'P250410983', 'uploads/products/P2504109835_67f82793e8d7f.jpg', 0, '2025-04-10 20:18:27'),
(423, 'P250410983', 'uploads/products/P2504109835_67f82793e9804.jpg', 0, '2025-04-10 20:18:27'),
(424, 'P250410564', 'uploads/products/P2504105640_67f8280f9697d.jpg', 0, '2025-04-10 20:20:31'),
(426, 'P250410395', 'uploads/products/P2504103955_67f8294523a2b.jpg', 0, '2025-04-10 20:25:41'),
(427, 'P250410425', 'uploads/products/P2504104252_67f82a351519c.png', 0, '2025-04-10 20:29:41'),
(428, 'P250410532', 'uploads/products/P2504105324_67f82a8603289.jpg', 0, '2025-04-10 20:31:02'),
(429, 'P250410528', 'uploads/products/P2504105289_67f82bf0b6beb.jpg', 0, '2025-04-10 20:37:04'),
(430, 'P250410394', 'uploads/products/P2504103944_67f82c45aedea.jpg', 0, '2025-04-10 20:38:29'),
(431, 'P250410232', 'uploads/products/P2504102320_67f82c91aa554.jpeg', 0, '2025-04-10 20:39:45'),
(432, 'P250410176', 'uploads/products/P2504101761_67f82ce33d266.jpg', 0, '2025-04-10 20:41:07'),
(433, 'P250410852', 'uploads/products/P2504108522_67f82d3a4ac4c.jpg', 0, '2025-04-10 20:42:34'),
(434, 'P250410854', 'uploads/products/P2504108547_67f82d9a93fd4.jpg', 0, '2025-04-10 20:44:10'),
(435, 'P250410195', 'uploads/products/P2504101956_67f82df986b15.jpg', 0, '2025-04-10 20:45:45'),
(436, 'P250410864', 'uploads/products/P2504108640_67f82e8561099.jpg', 0, '2025-04-10 20:48:05'),
(437, 'P250410539', 'uploads/products/P2504105390_67f82ee7d1669.jpg', 0, '2025-04-10 20:49:43'),
(438, 'P250410110', 'uploads/products/P2504101101_67f82f3096921.jpg', 0, '2025-04-10 20:50:56'),
(439, 'P250410775', 'uploads/products/P2504107754_67f82f8468013.jpg', 0, '2025-04-10 20:52:20'),
(440, 'P250410688', 'uploads/products/P2504106887_67f82fecf1f90.jpg', 0, '2025-04-10 20:54:04'),
(441, 'P250410589', 'uploads/products/P2504105897_67f83045c0827.jpg', 0, '2025-04-10 20:55:33'),
(442, 'P250410997', 'uploads/products/P2504109975_67f830834e4df.jpg', 0, '2025-04-10 20:56:35'),
(443, 'P250410788', 'uploads/products/P2504107887_67f830cb29a95.jpg', 0, '2025-04-10 20:57:47'),
(444, 'P250410655', 'uploads/products/P2504106556_67f83124f415c.jpg', 0, '2025-04-10 20:59:17'),
(445, 'P250410738', 'uploads/products/P2504107381_67f83177bac53.jpg', 0, '2025-04-10 21:00:39'),
(446, 'P250410484', 'uploads/products/P2504104849_67f831cae9ca2.jpg', 0, '2025-04-10 21:02:02'),
(447, 'P250410919', 'uploads/products/P2504109194_67f834797da7d.jpg', 0, '2025-04-10 21:13:29'),
(448, 'P250410929', 'uploads/products/P250410929_67f835401a89a.jpg', 0, '2025-04-10 21:16:48'),
(449, 'P250425799', 'uploads/products/P2504257991_680bb8012e69c.jpg', 0, '2025-04-25 16:27:45'),
(450, 'P250425799', 'uploads/products/P2504257991_680bb8012e99d.jpg', 0, '2025-04-25 16:27:45');

-- --------------------------------------------------------

--
-- Table structure for table `product_likes`
--

CREATE TABLE `product_likes` (
  `id` int(11) NOT NULL,
  `product_id` varchar(36) NOT NULL,
  `customer_id` varchar(36) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_likes`
--

INSERT INTO `product_likes` (`id`, `product_id`, `customer_id`, `created_at`) VALUES
(38, 'P250410424', 'CUS08802', '2025-04-10 17:32:09'),
(39, 'P250410998', 'CUS08802', '2025-04-10 18:27:28'),
(40, 'P250410612', 'CUS08802', '2025-04-10 19:22:26'),
(41, 'P250410856', 'CUS50858', '2025-04-10 19:32:27'),
(42, 'P250410424', 'CUS50858', '2025-04-10 20:32:48'),
(43, 'P250410424', 'CUS69511', '2025-04-10 20:34:42'),
(44, 'P250410208', 'CUS69511', '2025-04-10 21:02:44'),
(45, 'P250410589', 'CUS50858', '2025-04-17 13:47:14'),
(46, 'P250410788', 'CUS50858', '2025-04-17 15:00:59'),
(47, 'P250410856', 'CUS95253', '2025-04-18 05:27:26'),
(48, 'P250410424', 'CUS95253', '2025-04-25 16:23:47');

-- --------------------------------------------------------

--
-- Table structure for table `product_ratings`
--

CREATE TABLE `product_ratings` (
  `id` int(11) NOT NULL,
  `product_id` varchar(36) NOT NULL,
  `customer_id` varchar(36) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_ratings`
--

INSERT INTO `product_ratings` (`id`, `product_id`, `customer_id`, `rating`, `review`, `created_at`, `updated_at`) VALUES
(46, 'P250410856', 'CUS50858', 3, 'supiri', '2025-04-10 19:32:32', '2025-04-10 20:21:06'),
(47, 'P250410433', 'CUS50858', 3, 'mru', '2025-04-10 20:20:52', '2025-04-10 20:20:52'),
(48, 'P250410424', 'CUS50858', 3, 'supiri', '2025-04-10 20:32:29', '2025-04-10 20:32:29'),
(49, 'P250410424', 'CUS69511', 3, '', '2025-04-10 20:34:38', '2025-04-10 21:23:14'),
(50, 'P250410612', 'CUS69511', 2, '', '2025-04-10 20:34:49', '2025-04-10 20:34:49'),
(51, 'P250410998', 'CUS69511', 2, '', '2025-04-10 20:35:02', '2025-04-10 20:35:02'),
(52, 'P250410929', 'CUS69511', 3, '', '2025-04-10 21:23:26', '2025-04-10 21:23:26'),
(53, 'P250410484', 'CUS50858', 2, '2121', '2025-04-17 15:06:16', '2025-04-17 15:06:16'),
(54, 'P250410856', 'CUS95253', 3, 'mru', '2025-04-18 05:27:49', '2025-04-18 05:28:01'),
(55, 'P250410424', 'CUS95253', 3, 'mmm', '2025-04-18 05:28:11', '2025-04-25 16:23:54'),
(56, 'P250410484', 'CUS95253', 3, 'ccc', '2025-04-22 13:17:35', '2025-04-22 13:17:35');

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `seller_id` varchar(10) NOT NULL,
  `customer_id` varchar(10) DEFAULT NULL,
  `shop_name` varchar(100) NOT NULL,
  `main_category` varchar(50) NOT NULL,
  `street_address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `business_name` varchar(100) DEFAULT NULL,
  `business_reg_no` varchar(50) DEFAULT NULL,
  `business_description` text DEFAULT NULL,
  `business_doc_path` varchar(255) DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `profile_photo` varchar(255) DEFAULT NULL,
  `cover_photo` varchar(255) DEFAULT NULL,
  `notification_seen` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`seller_id`, `customer_id`, `shop_name`, `main_category`, `street_address`, `city`, `district`, `province`, `business_name`, `business_reg_no`, `business_description`, `business_doc_path`, `registration_date`, `status`, `profile_photo`, `cover_photo`, `notification_seen`) VALUES
('SLR6255', 'CUS50858', 'dinal_shop', 'Ceylon Gems', 'kandy', 'kandy', 'Kandy', 'Central', '', '', '', NULL, '2025-04-10 19:29:02', 'approved', 'uploads/sellers/SLR6255_profile_1744313417_rt.jpg', 'uploads/sellers/SLR6255_cover_1744313417_alponso-batiks-wall-hangings.jpg', 0),
('SLR6808', 'CUS08802', 'craft_shop', 'Batik Products', 'kandy road', 'kandy', 'Kandy', 'Central', 'traditional_bathik', '16523589VX', 'iam selling all traditional item .if i have 10 year expirience', 'uploads/business_docs/doc_67f7f745c398d.pdf', '2025-04-10 16:52:21', 'approved', NULL, NULL, 0),
('SLR8257', 'CUS69511', 'kasun_shop', 'Ceylon Gems', 'welimada', 'welimada', 'Badulla', 'Uva', NULL, NULL, NULL, NULL, '2025-04-10 20:34:09', 'approved', NULL, NULL, 0),
('SLR8578', 'CUS95253', 'Yak_shop', 'Batik Products', 'kandy', 'kandy', 'Badulla', 'Uva', '', '', '', NULL, '2025-04-18 05:46:56', 'approved', 'uploads/sellers/SLR8578_profile_1744955406_rt.jpg', 'uploads/sellers/SLR8578_cover_1744955406_www.reallygreatsite.com (2).jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_addresses`
--

CREATE TABLE `shipping_addresses` (
  `address_id` int(11) NOT NULL,
  `customer_id` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_addresses`
--

INSERT INTO `shipping_addresses` (`address_id`, `customer_id`, `full_name`, `email`, `phone`, `address_line1`, `address_line2`, `city`, `province`, `postal_code`, `is_default`, `created_at`) VALUES
(22, 'CUS08802', 'ranjith thennakoon', 'maleesha@gmail.com', '0713020319', '16/a hippola, thalathuoya', '', 'kandy', 'Central', '36851', 1, '2025-04-10 18:22:27'),
(23, 'CUS50858', 'dinal rashmika', 'dinal@gmail.com', '0701717599', 'thalathuoya', '', 'kandy', 'Central', '46512', 1, '2025-04-10 19:57:07'),
(24, 'CUS95253', 'rashmika', 'm@gmail.com', '0468953014', 'mm', 'dfgdfg', 'dfgdfg', 'Uva', '345345', 1, '2025-04-18 05:29:49');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `wishlist_id` int(11) NOT NULL,
  `customer_id` varchar(50) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`wishlist_id`, `customer_id`, `product_id`, `added_at`) VALUES
(446, 'CUS08802', 'P250410386', '2025-04-10 18:19:38'),
(448, 'CUS50858', 'P250410685', '2025-04-10 20:00:23'),
(449, 'CUS95253', 'P250410856', '2025-04-17 13:12:03'),
(454, 'CUS50858', 'P250410589', '2025-04-17 15:00:54'),
(456, 'CUS95253', 'P250410424', '2025-04-25 16:23:29'),
(457, 'CUS95253', 'P250410967', '2025-04-25 16:24:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_id` (`chat_id`);

--
-- Indexes for table `chat_metadata`
--
ALTER TABLE `chat_metadata`
  ADD PRIMARY KEY (`chat_id`);

--
-- Indexes for table `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_comment_like` (`comment_id`,`customer_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `address_id` (`address_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `product_comments`
--
ALTER TABLE `product_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_likes`
--
ALTER TABLE `product_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`product_id`,`customer_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_rating` (`product_id`,`customer_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`seller_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `shipping_addresses`
--
ALTER TABLE `shipping_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD UNIQUE KEY `unique_wishlist_item` (`customer_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=920;

--
-- AUTO_INCREMENT for table `comment_likes`
--
ALTER TABLE `comment_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `product_comments`
--
ALTER TABLE `product_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=152;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=451;

--
-- AUTO_INCREMENT for table `product_likes`
--
ALTER TABLE `product_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `product_ratings`
--
ALTER TABLE `product_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `shipping_addresses`
--
ALTER TABLE `shipping_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=458;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_metadata`
--
ALTER TABLE `chat_metadata`
  ADD CONSTRAINT `chat_metadata_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comment_likes`
--
ALTER TABLE `comment_likes`
  ADD CONSTRAINT `comment_likes_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `product_comments` (`comment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_likes_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `shipping_addresses` (`address_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `order_items_ibfk_3` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`seller_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`seller_id`);

--
-- Constraints for table `product_comments`
--
ALTER TABLE `product_comments`
  ADD CONSTRAINT `product_comments_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_comments_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `product_comments` (`comment_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_likes`
--
ALTER TABLE `product_likes`
  ADD CONSTRAINT `product_likes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_likes_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `product_ratings`
--
ALTER TABLE `product_ratings`
  ADD CONSTRAINT `product_ratings_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_ratings_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `sellers_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `shipping_addresses`
--
ALTER TABLE `shipping_addresses`
  ADD CONSTRAINT `shipping_addresses_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
