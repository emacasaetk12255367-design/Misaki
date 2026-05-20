-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2026 at 02:31 AM
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
-- Database: `misaki`
--

-- --------------------------------------------------------

--
-- Table structure for table `addon`
--

CREATE TABLE `addon` (
  `addon_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addon`
--

INSERT INTO `addon` (`addon_id`, `name`, `price`, `is_active`) VALUES
(1, 'Printed Photo', 5.00, 1),
(2, 'Acrylic Dedication', 5.00, 1),
(3, 'Fairy Light', 20.00, 1),
(4, 'Letter', 25.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin_user`
--

CREATE TABLE `admin_user` (
  `admin_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(60) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_user`
--

INSERT INTO `admin_user` (`admin_id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$MpWnrvxc9C6FL1uReQxUS.zdxzjNcr0ksRxRdhqGXyTHR8.xfASiO', '2026-05-08 16:19:55');

-- --------------------------------------------------------

--
-- Table structure for table `color_collection`
--

CREATE TABLE `color_collection` (
  `color_id` int(10) UNSIGNED NOT NULL,
  `collection_name` varchar(80) NOT NULL COMMENT 'e.g. Sakura, Crimson',
  `hex_code` varchar(7) NOT NULL COMMENT 'e.g. #ff3aa1',
  `hero_word` varchar(40) NOT NULL DEFAULT 'blooms' COMMENT 'The italic em word in hero h1',
  `bg_image` varchar(255) DEFAULT NULL COMMENT 'path relative to site root, e.g. images/home-background/file.jpg',
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `color_collection`
--

INSERT INTO `color_collection` (`color_id`, `collection_name`, `hex_code`, `hero_word`, `bg_image`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Crimson', '#ff2d55', 'blooms', NULL, 0, 1, '2026-05-20 06:11:56'),
(2, 'Sunrise', '#ff8a00', 'glows', NULL, 1, 1, '2026-05-20 06:11:56'),
(3, 'Mimosa', '#ffd400', 'shines', NULL, 2, 1, '2026-05-20 06:11:56'),
(4, 'Matcha', '#3ddc6b', 'grows', NULL, 3, 1, '2026-05-20 06:11:56'),
(5, 'Lagoon', '#00d4d4', 'flows', NULL, 4, 1, '2026-05-20 06:11:56'),
(6, 'Cobalt', '#2b5bff', 'drifts', NULL, 5, 1, '2026-05-20 06:11:56'),
(7, 'Iris', '#a64bff', 'dreams', NULL, 6, 1, '2026-05-20 06:11:56'),
(8, 'Sakura', '#ff3aa1', 'unfurls', NULL, 7, 1, '2026-05-20 06:11:56'),
(9, 'Crimson', '#ff2d55', 'blooms', NULL, 0, 1, '2026-05-20 06:12:11'),
(10, 'Sunrise', '#ff8a00', 'glows', NULL, 1, 1, '2026-05-20 06:12:11'),
(11, 'Mimosa', '#ffd400', 'shines', NULL, 2, 1, '2026-05-20 06:12:11'),
(12, 'Matcha', '#3ddc6b', 'grows', NULL, 3, 1, '2026-05-20 06:12:11'),
(13, 'Lagoon', '#00d4d4', 'flows', NULL, 4, 1, '2026-05-20 06:12:11'),
(14, 'Cobalt', '#2b5bff', 'drifts', NULL, 5, 1, '2026-05-20 06:12:11'),
(15, 'Iris', '#a64bff', 'dreams', NULL, 6, 1, '2026-05-20 06:12:11'),
(16, 'Sakura', '#ff3aa1', 'unfurls', NULL, 7, 1, '2026-05-20 06:12:11'),
(17, 'Crimson', '#ff2d55', 'blooms', NULL, 0, 1, '2026-05-20 06:13:24'),
(18, 'Sunrise', '#ff8a00', 'glows', NULL, 1, 1, '2026-05-20 06:13:24'),
(19, 'Mimosa', '#ffd400', 'shines', NULL, 2, 1, '2026-05-20 06:13:24'),
(20, 'Matcha', '#3ddc6b', 'grows', NULL, 3, 1, '2026-05-20 06:13:24'),
(21, 'Lagoon', '#00d4d4', 'flows', NULL, 4, 1, '2026-05-20 06:13:24'),
(22, 'Cobalt', '#2b5bff', 'drifts', NULL, 5, 1, '2026-05-20 06:13:24'),
(23, 'Iris', '#a64bff', 'dreams', NULL, 6, 1, '2026-05-20 06:13:24'),
(24, 'Sakura', '#ff3aa1', 'unfurls', NULL, 7, 1, '2026-05-20 06:13:24');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_collection`
--

CREATE TABLE `gallery_collection` (
  `gallery_id` int(10) UNSIGNED NOT NULL,
  `key_slug` varchar(80) NOT NULL COMMENT 'URL-safe key, e.g. eternal-roses',
  `name` varchar(120) NOT NULL,
  `tag` varchar(80) NOT NULL DEFAULT '' COMMENT 'e.g. Preserved · Forever',
  `description` text NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_collection`
--

INSERT INTO `gallery_collection` (`gallery_id`, `key_slug`, `name`, `tag`, `description`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'eternal-roses', 'Eternal Roses', 'Preserved · Forever', 'Roses sealed at their fullest hour — a vow that will not wilt.', 0, 1, '2026-05-20 06:11:56'),
(2, 'tulip-tranquility', 'Tulip Tranquility', 'Soft · Spring', 'A slow exhale in pastel — tulips folded for stillness.', 1, 1, '2026-05-20 06:11:56'),
(3, 'dazzling-daisy', 'Dazzling Daisy', 'Bright · Joyful', 'Small suns wrapped in ribbon — joy you can hold.', 2, 1, '2026-05-20 06:11:56'),
(4, 'money-bouquet', 'Money Bouquet', 'Folded · Fortune', 'Currency folded into petals — a wish for abundance, gifted gently.', 3, 1, '2026-05-20 06:11:56'),
(5, 'round-bouquet', 'Round Bouquet', 'Classic · Whole', 'A perfect circle — completeness offered in both hands.', 4, 1, '2026-05-20 06:11:56'),
(6, 'additional-blooms', 'Additional Blooms', 'Bespoke · Rare', 'One-of-one studies — orchids, peonies, and the unnamed.', 5, 1, '2026-05-20 06:11:56'),
(19, 'ewan', 'amen', 'nyak', 'qawdf', 1, 1, '2026-05-20 07:49:59');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_slide`
--

CREATE TABLE `gallery_slide` (
  `slide_id` int(10) UNSIGNED NOT NULL,
  `gallery_id` int(10) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL COMMENT 'path relative to site root',
  `caption` varchar(255) NOT NULL DEFAULT '',
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_slide`
--

INSERT INTO `gallery_slide` (`slide_id`, `gallery_id`, `image_path`, `caption`, `sort_order`) VALUES
(1, 6, 'https://images.unsplash.com/photo-1487530811176-3780de880c2d?w=1600&q=80', 'Bespoke, by quiet request.', 2),
(2, 6, 'https://images.unsplash.com/photo-1455659817273-f96807779a8a?w=1600&q=80', 'For the once, and the only.', 1),
(3, 6, 'https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=1600&q=80', 'A study no one else will hold.', 0),
(4, 3, 'https://images.unsplash.com/photo-1444930694458-01babe71870b?w=1600&q=80', 'Carried like a small celebration.', 2),
(5, 3, 'https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=1600&q=80', 'White petals, gold hearts.', 1),
(6, 3, 'https://images.unsplash.com/photo-1464982326199-86f1f6f1a6a4?w=1600&q=80', 'A field, distilled into a hand.', 0),
(7, 1, 'https://images.unsplash.com/photo-1455659817273-f96807779a8a?w=1600&q=80', 'Crimson, eternal, quiet.', 3),
(8, 1, 'https://images.unsplash.com/photo-1496062031456-07b8f162a322?w=1600&q=80', 'A glass vow against forgetting.', 2),
(9, 1, 'https://images.unsplash.com/photo-1561181286-d3fee7d55364?w=1600&q=80', 'Velvet petals, preserved by hand.', 1),
(10, 1, 'https://images.unsplash.com/photo-1518895949257-7621c3c786d7?w=1600&q=80', 'A single bloom, held in time.', 0),
(11, 4, 'https://images.unsplash.com/photo-1526304640581-d334cdbbf45e?w=1600&q=80', 'A bouquet that pays its own respects.', 2),
(12, 4, 'https://images.unsplash.com/photo-1579621970795-87facc2f976d?w=1600&q=80', 'Each crease, a small fortune.', 1),
(13, 4, 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=1600&q=80', 'Folded by hand, given with intent.', 0),
(14, 5, 'https://images.unsplash.com/photo-1455659817273-f96807779a8a?w=1600&q=80', 'Bound in silk, bound in care.', 2),
(15, 5, 'https://images.unsplash.com/photo-1507290439931-a861b5a38200?w=1600&q=80', 'The circle, the oldest promise.', 1),
(16, 5, 'https://images.unsplash.com/photo-1469371670807-013ccf25f16a?w=1600&q=80', 'A whole, gathered without seam.', 0),
(17, 2, 'https://images.unsplash.com/photo-1526045431048-f857369baa09?w=1600&q=80', 'Pastels gathered like memory.', 2),
(18, 2, 'https://images.unsplash.com/photo-1487070183336-b863922373d4?w=1600&q=80', 'Each stem, a measured breath.', 1),
(19, 2, 'https://images.unsplash.com/photo-1520763185298-1b434c919102?w=1600&q=80', 'Morning light on linen petals.', 0),
(32, 19, 'images/gallery/gallery_1779234634_6a0cf74a30bef.png', 'ewan', 0),
(33, 19, 'images/gallery/gallery_1779234650_6a0cf75ac0cbb.png', 'nyak', 1);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `order_id` int(10) UNSIGNED NOT NULL,
  `receipt_number` varchar(30) DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `delivery_name` varchar(120) DEFAULT NULL,
  `delivery_phone` varchar(20) DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `address_label` enum('Home','Someone Else') DEFAULT 'Home',
  `status` enum('pending','paid','fulfilled','cancelled') NOT NULL DEFAULT 'paid',
  `payment_method` enum('cash','gcash') NOT NULL DEFAULT 'cash',
  `payment_proof` varchar(255) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `estimated_completion` date DEFAULT NULL,
  `ready_notified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`order_id`, `receipt_number`, `user_id`, `delivery_name`, `delivery_phone`, `delivery_address`, `address_label`, `status`, `payment_method`, `payment_proof`, `total`, `estimated_completion`, `ready_notified`, `created_at`) VALUES
(21, 'MSK-20260517-0021', 1, 'fitzgerald aclan', '9303060298', '1583 A. Mendoza St. Brgy Carmona Makati City, Manila, Metro Manila', 'Someone Else', 'fulfilled', 'gcash', 'images/receipts/receipt_1779033576_6a09e5e84f251.jpg', 1005.00, '2026-05-18', 1, '2026-05-17 23:59:36');

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `order_item_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `qty` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`order_item_id`, `order_id`, `product_id`, `qty`, `unit_price`, `line_total`) VALUES
(23, 21, 15, 1, 1000.00, 1005.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_item_addon`
--

CREATE TABLE `order_item_addon` (
  `order_item_id` int(10) UNSIGNED NOT NULL,
  `addon_id` int(10) UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_item_addon`
--

INSERT INTO `order_item_addon` (`order_item_id`, `addon_id`, `unit_price`) VALUES
(23, 1, 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(120) NOT NULL,
  `name` varchar(120) NOT NULL,
  `jp_name` varchar(60) NOT NULL DEFAULT '',
  `type_id` int(10) UNSIGNED NOT NULL,
  `color_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK to color_collection',
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `badge` varchar(40) DEFAULT NULL,
  `description` text NOT NULL,
  `sales` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 50,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `slug`, `name`, `jp_name`, `type_id`, `color_id`, `price`, `image`, `badge`, `description`, `sales`, `stock`, `is_visible`, `created_at`) VALUES
(15, 'yes', 'Nike Air Jordan', 'tiao', 2, NULL, 1000.00, 'images/prod_1779032211_6a09e093dbe0f.jpg', 'Bestseller', 'water', 1, 9, 1, '2026-05-17 23:36:51'),
(17, 'awad', 'Nike Air Jordan', 'nigga', 1, 15, 123.00, 'images/prod_1779228966_6a0ce126dc92f.png', NULL, 'ewan', 0, 47, 1, '2026-05-20 06:16:06');

-- --------------------------------------------------------

--
-- Table structure for table `product_type`
--

CREATE TABLE `product_type` (
  `type_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_type`
--

INSERT INTO `product_type` (`type_id`, `name`) VALUES
(2, 'Arrangements'),
(1, 'Bouquets'),
(3, 'Dried'),
(4, 'Seasonal');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `review_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL CHECK (`rating` between 1 and 5),
  `body` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `key` varchar(80) NOT NULL,
  `value` text NOT NULL,
  `label` varchar(120) NOT NULL COMMENT 'Human-readable label for admin UI',
  `group` varchar(60) NOT NULL DEFAULT 'general' COMMENT 'Tab group in admin UI',
  `type` enum('text','textarea','color','url','email','tel') NOT NULL DEFAULT 'text',
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`key`, `value`, `label`, `group`, `type`, `sort_order`) VALUES
('about_body', 'Misaki was founded on the quiet belief that flowers are most beautiful when they are most themselves — a philosophy borrowed from ikebana and the wabi-sabi tradition.\r\n\r\nEvery arrangement is hand-tied in our small studio with seasonal blooms sourced weekly from local growers. We do not chase trends; we follow the bloom calendar.', 'About Body Text', 'pages', 'textarea', 3),
('about_eyebrow', '店について', 'About Page Eyebrow', 'pages', 'text', 1),
('about_font_color', '#1c1917', 'About Body Font Color', 'pages', 'color', 31),
('about_font_family', 'Inter, sans-serif', 'About Body Font Family', 'pages', 'text', 32),
('about_font_size', '1rem', 'About Body Font Size', 'pages', 'text', 30),
('about_heading', 'About the studio', 'About Page Heading', 'pages', 'text', 2),
('brand_jp', 'handcrafted · 美咲', 'Brand Japanese Subtitle', 'branding', 'text', 2),
('brand_name', 'MISAKI', 'Brand Name (Logo Text)', 'branding', 'text', 1),
('brand_quote_jp', '花のように静かに · like flowers, quietly', 'Footer Japanese Quote', 'branding', 'text', 4),
('brand_tagline', 'Handcrafted floral studio rooted in quiet ritual and seasonal bloom.', 'Brand Tagline (Footer)', 'branding', 'textarea', 3),
('color_border', '#ddd6cc', 'Border', 'colors', 'color', 7),
('color_card_bg', '#fbf8f3', 'Card Background', 'colors', 'color', 8),
('color_cream', '#f7f2ea', 'Background (Cream)', 'colors', 'color', 1),
('color_cream_dk', '#ede7dc', 'Background Dark Variant', 'colors', 'color', 9),
('color_ink', '#1c1917', 'Text (Ink Dark)', 'colors', 'color', 2),
('color_muted_fg', '#78716c', 'Muted Text', 'colors', 'color', 6),
('color_sage', '#6b8f6c', 'Accent (Sage Mid)', 'colors', 'color', 4),
('color_sage_deep', '#3d5a3e', 'Accent (Sage Deep)', 'colors', 'color', 3),
('color_sage_lt', '#c4d9c4', 'Accent (Sage Light)', 'colors', 'color', 5),
('contact_email', 'hello@misaki.lorem', 'Contact Email', 'contact', 'email', 1),
('contact_instagram', '@misaki.handcrafted', 'Instagram Handle', 'contact', 'text', 3),
('contact_phone', '+00 000 0000', 'Contact Phone', 'contact', 'tel', 2),
('footer_font_color', '#f7f2ea', 'Footer Font Color', 'footer', 'color', 11),
('footer_font_family', 'Inter, sans-serif', 'Footer Font Family', 'footer', 'text', 12),
('footer_font_size', '0.875rem', 'Footer Font Size', 'footer', 'text', 10),
('footer_link_1_text', 'Privacy Policy', 'Footer Legal Link 1 Text', 'footer', 'text', 1),
('footer_link_2_text', 'Terms of Service', 'Footer Legal Link 2 Text', 'footer', 'text', 2),
('footer_link_2_url', 'legal/terms.php', 'Footer Legal Link 2 URL', 'footer', 'url', 3),
('gallery_eyebrow', 'Misaki Atelier', 'Gallery Eyebrow Label', 'pages', 'text', 20),
('gallery_font_color', '#1c1917', 'Gallery Heading Font Color', 'pages', 'color', 24),
('gallery_font_family', 'Cormorant Garamond, serif', 'Gallery Heading Font Family', 'pages', 'text', 25),
('gallery_font_size', 'clamp(2.5rem,5vw,3.75rem)', 'Gallery Heading Font Size', 'pages', 'text', 23),
('gallery_heading', 'Gallery', 'Gallery Page Heading', 'pages', 'text', 21),
('gallery_images', '[]', 'Gallery Images JSON', 'homepage', 'textarea', 20),
('gallery_subtext', 'A quiet diary of arrangements through the seasons. Click any image to zoom.', 'Gallery Sub-text', 'pages', 'textarea', 22),
('gallery_tagline', '— A quiet study of bloom, thread, and patience.', 'Gallery Tagline (italic line under title)', 'pages', 'text', 21),
('gcash_name', 'Misaki Floral', 'GCash Account Name', 'contact', 'text', 5),
('gcash_number', '0912 345 6789', 'GCash Number', 'contact', 'tel', 4),
('header_font_color', '#1c1917', 'Header Brand Color', 'branding', 'color', 11),
('header_font_family', 'Cormorant Garamond, serif', 'Header Brand Font Family', 'branding', 'text', 12),
('header_font_size', '1.15rem', 'Header Brand Font Size', 'branding', 'text', 10),
('hero_bg_image', 'images/home-background/homebg_1778946390_6a08915616cd3.jpg', 'Hero Background Image', 'homepage', 'url', 10),
('hero_cta_primary', 'Shop blooms', 'Hero Primary Button', 'homepage', 'text', 4),
('hero_cta_secondary', 'Gallery', 'Hero Secondary Button', 'homepage', 'text', 5),
('hero_eyebrow', '美咲 · MISAKI', 'Hero Eyebrow Text', 'homepage', 'text', 1),
('hero_font_color', '#000000', 'Hero Heading Font Color', 'homepage', 'color', 12),
('hero_font_family', '', 'Hero Heading Font Family', 'homepage', 'text', 13),
('hero_font_size', '', 'Hero Heading Font Size', 'homepage', 'text', 11),
('hero_heading', 'Blooms made with <em>quiet intention</em>', 'Hero Heading (HTML)', 'homepage', 'textarea', 2),
('hero_subtext', 'Handcrafted floral arrangements rooted in seasonal rhythm and wabi-sabi beauty.', 'Hero Subtext', 'homepage', 'textarea', 3),
('meta_description', 'Handcrafted floral arrangements with quiet ritual and seasonal bloom.', 'Default Meta Description', 'seo', 'textarea', 1),
('meta_og_title', 'Misaki Handcrafted — Floral Studio', 'Open Graph Title', 'seo', 'text', 2),
('shop_eyebrow', '店舗', 'Shop Eyebrow Label', 'pages', 'text', 10),
('shop_font_color', '#1c1917', 'Shop Heading Font Color', 'pages', 'color', 14),
('shop_font_family', 'Cormorant Garamond, serif', 'Shop Heading Font Family', 'pages', 'text', 15),
('shop_font_size', 'clamp(2.5rem,5vw,3.75rem)', 'Shop Heading Font Size', 'pages', 'text', 13),
('shop_heading', 'Shop', 'Shop Page Heading', 'pages', 'text', 11),
('shop_subtext', 'Seasonal blooms, dried botanicals and ikebana arrangements — each made by hand.', 'Shop Sub-text', 'pages', 'textarea', 12);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `address` text DEFAULT NULL,
  `address_label` enum('Home','Someone Else') DEFAULT 'Home',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `email`, `phone`, `password_hash`, `full_name`, `address`, `address_label`, `created_at`) VALUES
(1, 'kristiamaeorbe@gmail.com', '09303060298', '$2y$10$iV4EOCqTrfLZocrL0.1GEOA70fEmJVu3saigF9jMoAC.e7tUyQMjK', 'fitzgerald Almadin', '1583 A. Mendoza St. Brgy Carmona Makati City', 'Home', '2026-05-09 11:43:18'),
(2, 'raidensantos17@gmail.com', NULL, '$2y$10$lGhA40t2JArfRB5TzEYmPuQKD/7E1oBjbKiXDaMECZ5nGv/URomOq', 'Raiden Santos', NULL, 'Home', '2026-05-11 12:32:18'),
(3, 'esdfsdfs@yahoo.com', '09303060298', '$2y$10$luged279krNncnYXRIuD0uZ5qpEF5rD2TQXPTzOC2ckfwrmCAJKKi', 'Emily Sicatsss111', 'ewfwefwe', 'Home', '2026-05-11 12:51:39');

-- --------------------------------------------------------

--
-- Table structure for table `user_address`
--

CREATE TABLE `user_address` (
  `address_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `label` enum('Home','Someone Else') NOT NULL DEFAULT 'Home',
  `full_name` varchar(120) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address_text` text NOT NULL,
  `city` varchar(80) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_address`
--

INSERT INTO `user_address` (`address_id`, `user_id`, `label`, `full_name`, `phone`, `address_text`, `city`, `is_default`, `created_at`) VALUES
(1, 1, 'Someone Else', 'fitzgerald aclan', '9303060298', '1583 A. Mendoza St. Brgy Carmona Makati City', 'Manila', 1, '2026-05-14 08:08:30'),
(2, 3, 'Home', 'Emily Sicatsss111', '09303060298', 'ewfwefwe', 'Manila', 1, '2026-05-14 08:08:30'),
(8, 1, 'Home', 'sefsfsef', 'sefefss', 'sffsf', 'Quezon City', 0, '2026-05-14 09:17:56'),
(9, 1, 'Someone Else', 'awdawdadwad', 'aawdadawdawd', 'qwwfwfrw3', 'Navotas', 0, '2026-05-14 09:18:09'),
(11, 1, 'Home', 'awdawdadawd', 'awfawfawf', 'fawfasafvsda', 'San Juan', 0, '2026-05-14 09:18:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addon`
--
ALTER TABLE `addon`
  ADD PRIMARY KEY (`addon_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `admin_user`
--
ALTER TABLE `admin_user`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `color_collection`
--
ALTER TABLE `color_collection`
  ADD PRIMARY KEY (`color_id`);

--
-- Indexes for table `gallery_collection`
--
ALTER TABLE `gallery_collection`
  ADD PRIMARY KEY (`gallery_id`),
  ADD UNIQUE KEY `key_slug` (`key_slug`);

--
-- Indexes for table `gallery_slide`
--
ALTER TABLE `gallery_slide`
  ADD PRIMARY KEY (`slide_id`),
  ADD KEY `fk_slide_gallery` (`gallery_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `receipt_number` (`receipt_number`),
  ADD KEY `fk_order_user` (`user_id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `fk_oi_order` (`order_id`),
  ADD KEY `fk_oi_product` (`product_id`);

--
-- Indexes for table `order_item_addon`
--
ALTER TABLE `order_item_addon`
  ADD PRIMARY KEY (`order_item_id`,`addon_id`),
  ADD KEY `fk_oia_addon` (`addon_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_product_type` (`type_id`),
  ADD KEY `fk_product_color` (`color_id`);

--
-- Indexes for table `product_type`
--
ALTER TABLE `product_type`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `uniq_review_per_order_product` (`order_id`,`product_id`),
  ADD KEY `fk_review_product` (`product_id`),
  ADD KEY `fk_review_user` (`user_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_address`
--
ALTER TABLE `user_address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `fk_ua_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addon`
--
ALTER TABLE `addon`
  MODIFY `addon_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `admin_user`
--
ALTER TABLE `admin_user`
  MODIFY `admin_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `color_collection`
--
ALTER TABLE `color_collection`
  MODIFY `color_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `gallery_collection`
--
ALTER TABLE `gallery_collection`
  MODIFY `gallery_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `gallery_slide`
--
ALTER TABLE `gallery_slide`
  MODIFY `slide_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `order_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `product_type`
--
ALTER TABLE `product_type`
  MODIFY `type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `review_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_address`
--
ALTER TABLE `user_address`
  MODIFY `address_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gallery_slide`
--
ALTER TABLE `gallery_slide`
  ADD CONSTRAINT `fk_slide_gallery` FOREIGN KEY (`gallery_id`) REFERENCES `gallery_collection` (`gallery_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_oi_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `order_item_addon`
--
ALTER TABLE `order_item_addon`
  ADD CONSTRAINT `fk_oia_addon` FOREIGN KEY (`addon_id`) REFERENCES `addon` (`addon_id`),
  ADD CONSTRAINT `fk_oia_oi` FOREIGN KEY (`order_item_id`) REFERENCES `order_item` (`order_item_id`) ON DELETE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_color` FOREIGN KEY (`color_id`) REFERENCES `color_collection` (`color_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_product_type` FOREIGN KEY (`type_id`) REFERENCES `product_type` (`type_id`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `fk_review_order` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_review_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `user_address`
--
ALTER TABLE `user_address`
  ADD CONSTRAINT `fk_ua_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
