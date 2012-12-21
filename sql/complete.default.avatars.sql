-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 03, 2012 at 01:20 PM
-- Server version: 5.1.37
-- PHP Version: 5.2.10-2ubuntu6.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `kohana_test`
--

-- --------------------------------------------------------

--
-- Table structure for table `avatars`
--

DROP TABLE IF EXISTS `avatars`;
CREATE TABLE IF NOT EXISTS `avatars` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `owner_table` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `owner_id` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `provider_id` int(10) NOT NULL,
  `provider_data` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`owner_id`),
  KEY `provider_id` (`provider_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `avatars`
--

INSERT INTO `avatars` (`id`, `owner_table`, `owner_id`, `created`, `provider_id`, `provider_data`) VALUES
(1, 'users', 0, '2012-03-03 12:53:35', 1, '1'),
(2, 'users', 0, '2012-03-03 12:53:43', 1, '2'),
(3, 'users', 0, '2012-03-03 12:53:49', 1, '3'),
(4, 'users', 0, '2012-03-03 12:53:54', 1, '4'),
(5, 'users', 0, '2012-03-03 12:54:00', 1, '5'),
(6, 'users', 0, '2012-03-03 12:54:06', 1, '6'),
(7, 'users', 0, '2012-03-03 12:54:11', 1, '7'),
(8, 'users', 0, '2012-03-03 12:54:19', 1, '8'),
(9, 'users', 0, '2012-03-03 12:54:25', 1, '9');
