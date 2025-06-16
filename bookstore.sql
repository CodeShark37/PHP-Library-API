-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2024 at 11:50 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookstore`
--

-- --------------------------------------------------------

--
-- Table structure for table `author`
--

CREATE TABLE `author` (
  `author_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `author`
--

INSERT INTO `author` (`author_id`, `name`, `last_name`, `country`) VALUES
(1, 'Scott David ', 'Allen', 'Canada'),
(4, 'Test Author', 'Apitest', 'Apicountry'),
(5, 'Robert T.', 'Kiyosaky', 'United States of America');

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `book_id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `isbn` varchar(100) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `publisher` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`book_id`, `title`, `isbn`, `year`, `publisher`) VALUES
(1, 'Porque a Justiça social não é a Justiça Bíblica', '9876476483', 2020, 'Vida Nova'),
(5, 'Porque os Ricos ficam ainda mais Ricos', '9846486421', 2021, 'Alta Books'),
(18, 'TestBook', '9846436421', 2021, 'Alta Books');

-- --------------------------------------------------------

--
-- Table structure for table `book_author`
--

CREATE TABLE `book_author` (
  `book_author_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `author_order` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `book_author`
--

INSERT INTO `book_author` (`book_author_id`, `book_id`, `author_id`, `author_order`) VALUES
(1, 1, 1, 1),
(2, 1, 5, 2),
(3, 18, 4, 1),
(4, 18, 1, 2),
(8, 5, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

CREATE TABLE `cart_item` (
  `item_id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `qtd` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('buyed','saved') DEFAULT NULL,
  `delivery_address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `credit_card` varchar(20) DEFAULT NULL,
  `job` varchar(100) DEFAULT NULL,
  `bi_passport` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`user_id`, `name`, `last_name`, `country`, `credit_card`, `job`, `bi_passport`, `address`, `phone`) VALUES
(3, 'Test', 'Client', 'Angola', '89787988', 'Teacher', '00649786LA42', 'Luanda,Morro Bento', '943566222');

-- --------------------------------------------------------

--
-- Table structure for table `client_scientific_area`
--

CREATE TABLE `client_scientific_area` (
  `user_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `preference_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `client_scientific_area`
--

INSERT INTO `client_scientific_area` (`user_id`, `area_id`, `preference_reason`) VALUES
(3, 3, 'preferência '),
(3, 5, 'Area de Estudo');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(80, ''),
(8, 'admin'),
(62, 'admin@api.com'),
(4, 'author'),
(63, 'bigboss@api.com'),
(82, 'deleteuser@api.com'),
(78, 'good@api.com'),
(2, 'group_admin'),
(7, 'group_moderator'),
(9, 'moderator'),
(1, 'super_admin'),
(64, 'test@api.com'),
(3, 'user'),
(5, 'user_1');

-- --------------------------------------------------------

--
-- Table structure for table `role_inheritance`
--

CREATE TABLE `role_inheritance` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `inherited_role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `role_inheritance`
--

INSERT INTO `role_inheritance` (`id`, `role_id`, `inherited_role_id`) VALUES
(3, 9, 1),
(5, 3, 4),
(11, 62, 8);

-- --------------------------------------------------------

--
-- Table structure for table `role_resources`
--

CREATE TABLE `role_resources` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `resource_type` varchar(255) NOT NULL,
  `resource_id` varchar(255) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `allowed` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `role_resources`
--

INSERT INTO `role_resources` (`id`, `role_id`, `resource_type`, `resource_id`, `action`, `allowed`) VALUES
(27, 8, '*', '*', '*', 1),
(28, 8, 'article', '*', '*', 1),
(29, 8, 'article', '*', 'view', 1),
(30, 8, 'article', '1', '*', 1),
(31, 8, 'article', '1', 'delete', 1),
(32, 2, '*', '*', '*', 1),
(33, 3, 'book', '3', 'view', 1),
(34, 3, 'book', '4', 'view', 1),
(35, 3, 'book', '5', 'view', 0),
(38, 7, 'article', '*', '*', 1),
(39, 9, 'article', '*', '*', 1),
(40, 4, 'post', '*', '*', 1),
(42, 4, 'article', '*', '*', 1),
(44, 4, 'post', '*', 'view', 1),
(45, 3, 'user', '*', 'view', 1),
(46, 3, 'user', '*', 'delete', 1),
(47, 3, 'user', '*', 'update', 1);

-- --------------------------------------------------------

--
-- Table structure for table `scientific_area`
--

CREATE TABLE `scientific_area` (
  `area_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `scientific_area`
--

INSERT INTO `scientific_area` (`area_id`, `name`) VALUES
(1, 'Matemática'),
(2, 'Informática'),
(3, 'Religião'),
(4, 'Finanças'),
(5, 'Gestão Financeira');

-- --------------------------------------------------------

--
-- Table structure for table `specific_roles`
--

CREATE TABLE `specific_roles` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `specific_role_id` int(11) NOT NULL,
  `resource_type` varchar(255) NOT NULL,
  `resource_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `specific_roles`
--

INSERT INTO `specific_roles` (`id`, `role_id`, `specific_role_id`, `resource_type`, `resource_id`) VALUES
(3, 9, 1, 'forum', '1'),
(4, 9, 4, 'article', '2'),
(8, 5, 4, 'post', '3'),
(11, 63, 3, 'user', '2'),
(12, 64, 3, 'user', '3'),
(40, 78, 3, 'user', '34'),
(43, 82, 3, 'user', '37');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(40) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(60) NOT NULL,
  `role_id` int(11) NOT NULL,
  `create_time` timestamp NULL DEFAULT current_timestamp(),
  `token` varchar(400) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `email`, `role_id`, `create_time`, `token`) VALUES
(1, 'Joshua Newman', 'Boss37', 'admin@api.com', 62, '2024-05-07 23:23:48', NULL),
(2, 'BigBoss', 'BossN38', 'BigBoss@api.com', 63, '2024-05-07 23:25:37', NULL),
(3, 'TestBoss', 'BossN38Test', 'Test@api.com', 64, '2024-05-07 23:36:34', NULL),
(34, 'goodBossUpdated2', 'N38Test', 'Good@api.com', 78, '2024-05-08 02:00:33', NULL),
(37, 'DeleteUoss', 'N38Test', 'DeleteUser@api.com', 82, '2024-07-03 16:06:29', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `author`
--
ALTER TABLE `author`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `title_UNIQUE` (`title`),
  ADD UNIQUE KEY `isbn_UNIQUE` (`isbn`);

--
-- Indexes for table `book_author`
--
ALTER TABLE `book_author`
  ADD PRIMARY KEY (`book_author_id`),
  ADD KEY `book_id_idx` (`book_id`),
  ADD KEY `author_id_idx` (`author_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `client_scientific_area`
--
ALTER TABLE `client_scientific_area`
  ADD PRIMARY KEY (`user_id`,`area_id`),
  ADD KEY `area_id` (`area_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_inheritance`
--
ALTER TABLE `role_inheritance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `inherited_role_id` (`inherited_role_id`);

--
-- Indexes for table `role_resources`
--
ALTER TABLE `role_resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `scientific_area`
--
ALTER TABLE `scientific_area`
  ADD PRIMARY KEY (`area_id`);

--
-- Indexes for table `specific_roles`
--
ALTER TABLE `specific_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `specific_role_id` (`specific_role_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `author`
--
ALTER TABLE `author`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `book`
--
ALTER TABLE `book`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `book_author`
--
ALTER TABLE `book_author`
  MODIFY `book_author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book_author`
--
ALTER TABLE `book_author`
  ADD CONSTRAINT `author_id` FOREIGN KEY (`author_id`) REFERENCES `author` (`author_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `book_id` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
