-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 28, 2018 at 08:52 PM
-- Server version: 5.7.19
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `7learn_nashenas`
--

-- --------------------------------------------------------

--
-- Table structure for table `blocked_users`
--

DROP TABLE IF EXISTS `blocked_users`;
CREATE TABLE IF NOT EXISTS `blocked_users` (
  `id` bigint(255) NOT NULL AUTO_INCREMENT,
  `blocker_user` int(63) NOT NULL,
  `blocked_user` int(63) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sent_messages`
--

DROP TABLE IF EXISTS `sent_messages`;
CREATE TABLE IF NOT EXISTS `sent_messages` (
  `id` bigint(255) NOT NULL AUTO_INCREMENT,
  `sender_id` int(63) NOT NULL,
  `receiver_id` int(63) NOT NULL,
  `message` text CHARACTER SET utf8mb4 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(63) NOT NULL,
  `first_name` varchar(1023) CHARACTER SET utf8mb4 NOT NULL,
  `last_name` varchar(1023) CHARACTER SET utf8mb4 DEFAULT NULL,
  `username` varchar(1023) DEFAULT NULL,
  `step` varchar(63) DEFAULT NULL,
  `pay_time` varchar(63) DEFAULT NULL,
  `hash_id` varchar(512) DEFAULT NULL,
  `shown_name` varchar(512) CHARACTER SET utf8mb4 DEFAULT NULL,
  `receiver_id` varchar(512) DEFAULT NULL,
  `message_tmp` text CHARACTER SET utf8mb4,
  `answer_to` varchar(63) DEFAULT NULL,
  `on_off` tinyint(1) DEFAULT '1',
  `privacy` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `hash_id` (`hash_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
