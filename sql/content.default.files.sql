-- phpMyAdmin SQL Dump
-- version 3.2.2.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 03, 2012 at 01:19 PM
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
-- Table structure for table `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pertain_table` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `pertain_id` int(10) NOT NULL,
  `filename_local` text COLLATE utf8_unicode_ci NOT NULL,
  `filename_user` text COLLATE utf8_unicode_ci NOT NULL,
  `filetype` int(2) NOT NULL,
  `size` int(20) NOT NULL,
  `mimetype` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `pertain_table`, `pertain_id`, `filename_local`, `filename_user`, `filetype`, `size`, `mimetype`, `created`, `updated`, `user_id`) VALUES
(1, 'avatars', 1, 'avatar1.png', 'avatar1.png', 11, 80524, 'image/png', '2012-03-03 12:53:35', '0000-00-00 00:00:00', 0),
(2, 'avatars', 2, 'avatar2.png', 'avatar2.png', 11, 80848, 'image/png', '2012-03-03 12:53:43', '0000-00-00 00:00:00', 0),
(3, 'avatars', 3, 'avatar3.png', 'avatar3.png', 11, 81789, 'image/png', '2012-03-03 12:53:49', '0000-00-00 00:00:00', 0),
(4, 'avatars', 4, 'avatar4.png', 'avatar4.png', 11, 81142, 'image/png', '2012-03-03 12:53:54', '0000-00-00 00:00:00', 0),
(5, 'avatars', 5, 'avatar5.png', 'avatar5.png', 11, 81290, 'image/png', '2012-03-03 12:54:00', '0000-00-00 00:00:00', 0),
(6, 'avatars', 6, 'avatar6.png', 'avatar6.png', 11, 81346, 'image/png', '2012-03-03 12:54:06', '0000-00-00 00:00:00', 0),
(7, 'avatars', 7, 'avatar7.png', 'avatar7.png', 11, 81714, 'image/png', '2012-03-03 12:54:11', '0000-00-00 00:00:00', 0),
(8, 'avatars', 8, 'avatar8.png', 'avatar8.png', 11, 81804, 'image/png', '2012-03-03 12:54:19', '0000-00-00 00:00:00', 0),
(9, 'avatars', 9, 'avatar9.png', 'avatar9.png', 11, 81483, 'image/png', '2012-03-03 12:54:25', '0000-00-00 00:00:00', 0);
