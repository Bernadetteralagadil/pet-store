-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2024 at 03:06 PM
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
-- Database: `petshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `accountID` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `birthdate` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`accountID`, `firstName`, `lastName`, `gender`, `birthdate`, `email`, `password`) VALUES
(1, 'Aliyah', 'May', 'female', '2003-11-16', 'aliyahmay@gmail.com', '$2y$10$/GqOtuR/tc907yXHkP2Nn.gk9K8E6jkuL2lFBjccdhnZY8Iy5pbCu');

-- --------------------------------------------------------

--
-- Table structure for table `addtocart`
--

CREATE TABLE `addtocart` (
  `cartID` int(11) NOT NULL,
  `accountID` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` varchar(255) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `totalPrice` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orderID` int(11) NOT NULL,
  `accountID` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `quantity` varchar(255) DEFAULT NULL,
  `totalPrice` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `date` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`orderID`, `accountID`, `description`, `price`, `quantity`, `totalPrice`, `status`, `date`) VALUES
(2, 1, 'Bare Kitchen Fresh Lamb Recipe Wet Dog Food 430g', '199', '2', '398.00', 'delivered', '2024-12-17'),
(3, 1, 'Bare Kitchen Premium Ham Chicken Sausages Dog Food 430g', '199', '2', '398.00', 'delivered', '2024-12-17');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `productID` int(11) NOT NULL,
  `thumbnail` blob DEFAULT NULL,
  `categories` varchar(255) DEFAULT NULL,
  `sub_categories` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `stock` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`productID`, `thumbnail`, `categories`, `sub_categories`, `name`, `description`, `price`, `stock`, `status`) VALUES
(1, 0x75706c6f6164732f363736313438383935383566662d6d6f6e616c6973612e6a7067, 'Dog', 'Secret', 'Monalisa', 'Basta', '10000', '10', 'In Stock'),
(2, 0x75706c6f6164732f363736313538616132303836372d6d6f6e616c6973612e6a7067, 'Cat', 'Accessories', 'Badet', 'Secret', '1000', '10', 'In Stock');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`accountID`);

--
-- Indexes for table `addtocart`
--
ALTER TABLE `addtocart`
  ADD PRIMARY KEY (`cartID`),
  ADD KEY `accountID` (`accountID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderID`),
  ADD KEY `accountID` (`accountID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`productID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `accountID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `addtocart`
--
ALTER TABLE `addtocart`
  MODIFY `cartID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `orderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `productID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addtocart`
--
ALTER TABLE `addtocart`
  ADD CONSTRAINT `addtocart_ibfk_1` FOREIGN KEY (`accountID`) REFERENCES `account` (`accountID`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`accountID`) REFERENCES `account` (`accountID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
