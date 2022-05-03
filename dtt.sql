-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 03, 2022 at 06:39 PM
-- Server version: 8.0.27
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dtt`
--

-- --------------------------------------------------------

--
-- Table structure for table `facility`
--

DROP TABLE IF EXISTS `facility`;
CREATE TABLE IF NOT EXISTS `facility` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `doc` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facility`
--

INSERT INTO `facility` (`id`, `name`, `doc`) VALUES
(18, 'Apple', '2022-05-03'),
(19, 'Microsoft', '2001-04-29'),
(20, 'Micromax', '2022-05-03');

-- --------------------------------------------------------

--
-- Table structure for table `factagjun`
--

DROP TABLE IF EXISTS `factagjun`;
CREATE TABLE IF NOT EXISTS `factagjun` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `fac_id` int NOT NULL,
  `tag_id` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `fac_id` (`fac_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `factagjun`
--

INSERT INTO `factagjun` (`ID`, `fac_id`, `tag_id`) VALUES
(32, 18, 16),
(33, 18, 15),
(34, 18, 14),
(38, 19, 17),
(39, 19, 18),
(40, 20, 16),
(41, 20, 15),
(42, 20, 14);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
CREATE TABLE IF NOT EXISTS `location` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fac_id` int NOT NULL,
  `city` varchar(20) NOT NULL,
  `address` varchar(30) NOT NULL,
  `zipcode` int NOT NULL,
  `countrycode` int NOT NULL,
  `Phone` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `facilityid` (`fac_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `fac_id`, `city`, `address`, `zipcode`, `countrycode`, `Phone`) VALUES
(13, 18, 'California', '66 north colorado', 3322, 1, '98977223'),
(14, 19, 'New York', '6 North brooklyn', 686664, 1, '989994323'),
(15, 20, 'New York', '79 chinatown', 686664, 1, '989994323');

-- --------------------------------------------------------

--
-- Table structure for table `securitytokens`
--

DROP TABLE IF EXISTS `securitytokens`;
CREATE TABLE IF NOT EXISTS `securitytokens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `token` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `securitytokens`
--

INSERT INTO `securitytokens` (`id`, `token`, `time`) VALUES
(16, 'd459692572c26caf3289cb16d6a5a082', '2022-05-03 16:26:24');

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
CREATE TABLE IF NOT EXISTS `tag` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tagname` varchar(25) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniquname` (`tagname`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `tagname`) VALUES
(16, ' colourful'),
(15, ' Yummy'),
(17, 'colorful'),
(14, 'cool'),
(18, 'perfect');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `factagjun`
--
ALTER TABLE `factagjun`
  ADD CONSTRAINT `FK_fac` FOREIGN KEY (`fac_id`) REFERENCES `facility` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_tag` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `location`
--
ALTER TABLE `location`
  ADD CONSTRAINT `FK_location` FOREIGN KEY (`fac_id`) REFERENCES `facility` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Events
--
DROP EVENT IF EXISTS `tokendeleter`$$
CREATE DEFINER=`root`@`localhost` EVENT `tokendeleter` ON SCHEDULE EVERY 1 HOUR STARTS '2022-04-30 19:03:58' ON COMPLETION NOT PRESERVE ENABLE DO DELETE from securitytokens
WHERE time < CURRENT_TIMESTAMP$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
