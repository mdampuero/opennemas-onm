-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 29, 2011 at 02:35 PM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-1ubuntu9.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `onm-default`
--

-- --------------------------------------------------------

--
-- Table structure for table `adodb_logsql`
--

CREATE TABLE IF NOT EXISTS `adodb_logsql` (
  `created` datetime NOT NULL,
  `sql0` varchar(250) NOT NULL,
  `sql1` text NOT NULL,
  `params` text NOT NULL,
  `tracer` text NOT NULL,
  `timer` decimal(16,6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `adodb_logsql`
--

INSERT INTO `adodb_logsql` (`created`, `sql0`, `sql1`, `params`, `tracer`, `timer`) VALUES
('2011-09-29 12:35:01', '80.553439691', 'SELECT pk_fk_content_category FROM users_content_categories WHERE pk_fk_user = ?', '', 'ERROR: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ''?'' at line 1<br>idealgallego.local/admin/controllers/acl/user.php', '0.000111'),
('2011-09-29 13:08:21', '80.553439691', 'SELECT pk_fk_content_category FROM users_content_categories WHERE pk_fk_user = ?', '', 'ERROR: You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ''?'' at line 1<br>idealgallego.local/admin/controllers/acl/user.php', '0.000280');

-- --------------------------------------------------------

--
-- Table structure for table `advertisements`
--

CREATE TABLE IF NOT EXISTS `advertisements` (
  `pk_advertisement` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type_advertisement` smallint(2) unsigned DEFAULT '1',
  `fk_content_categories` varchar(255) DEFAULT '',
  `path` varchar(150) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `type_medida` varchar(50) DEFAULT NULL,
  `num_clic` int(10) DEFAULT '0',
  `num_clic_count` int(10) unsigned DEFAULT '0',
  `num_view` int(10) unsigned DEFAULT '0',
  `with_script` smallint(1) DEFAULT '0',
  `script` text,
  `overlap` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Flag esconder eventos flash',
  `timeout` int(4) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`pk_advertisement`),
  KEY `type_advertisement` (`type_advertisement`),
  KEY `fk_content_categories` (`fk_content_categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=163 ;

--
-- Dumping data for table `advertisements`
--

INSERT INTO `advertisements` (`pk_advertisement`, `type_advertisement`, `fk_content_categories`, `path`, `url`, `type_medida`, `num_clic`, `num_clic_count`, `num_view`, `with_script`, `script`, `overlap`, `timeout`) VALUES
(127, 1, '0,26,25,27,28,29,24,23,22', '115', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(128, 2, '0,26,25,27,28,29,24,23,22', '126', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(129, 50, '0', '124', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(130, 5, '0,4,26,25,27,28,29,24,23,22', '123', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(131, 6, '0,4,26,25,27,28,29,24,23,22', '117', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(132, 11, '0,4,26,25,27,28,29,24,23,22', '111', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(133, 3, '0,26,25,27,28,29,24,23,22', '123', 'http://www.openhost.es', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(134, 110, '0,4,26,25,27,28,29,24,23,22', '118', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(135, 4, '0', '114', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(136, 32, '0,4,26,25,27,28,29,24,23,22', '119', 'http://openhost.es', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(137, 101, '0', '115', 'http://www.retrincos.es', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(138, 102, '0,4,26,25,27,28,29,24,23,22', '118', 'http://www.retrincos.es', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(139, 104, '0', '123', 'http://retrincos.es', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(140, 103, '0,4,26,25,27,28,29,24,23,22', '111', 'http://www.retrincos.es', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(141, 601, '4', '115', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(142, 602, '4', '114', 'http://www.retrincos.es', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(143, 602, '4', '119', 'http://www.openhost.es', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(144, 605, '4', '116', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(145, 609, '4', '115', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(146, 610, '4', '118', 'http://www.retrincos.es', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(147, 702, '4', '114', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(148, 701, '4', '115', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(149, 603, '4', '119', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(150, 250, '0,4,26,25,27,28,29,24,23,22', '123', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(151, 202, '0', '126', 'http://www.retrincos.es', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(152, 403, '0,4,26,25,27,28,29,24,23,22', '111', 'http://www.retrincos.es', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(153, 401, '0,4,26,25,27,28,29,24,23,22', '115', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(154, 402, '0,4,26,25,27,28,29,24,23,22', '114', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(155, 501, '0', '115', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(156, 109, '0', '115', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 1, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(157, 1, '0,4,26,25,27,28,29,24,23,22', '123', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(158, 502, '0,4,26,25,27,28,29,24,23,22', '118', 'http://www.retrincos.es', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(159, 703, '4', '119', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(160, 303, '0,4,26,25,27,28,29,24,23,22', '116', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(161, 301, '0,4,26,25,27,28,29,24,23,22', '123', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(162, 302, '0,4,26,25,27,28,29,24,23,22', '117', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4);

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE IF NOT EXISTS `albums` (
  `pk_album` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subtitle` varchar(250) DEFAULT NULL,
  `agency` varchar(250) DEFAULT NULL,
  `fuente` varchar(250) DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_album`),
  UNIQUE KEY `pk_album` (`pk_album`),
  KEY `pk_album_2` (`pk_album`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=183 ;

--
-- Dumping data for table `albums`
--

INSERT INTO `albums` (`pk_album`, `subtitle`, `agency`, `fuente`, `cover`) VALUES
(89, '0', 'onm agency', '0', '/2011/09/23//300-240-2011092811273278571.jpg'),
(90, '0', 'onm agency', '0', '/2011/09/26//300-240-2011092811294155907.jpg'),
(91, '0', 'onm agency', '0', '/2011/09/26//300-240-2011092811251054333.jpg'),
(92, '0', 'onm-agency', '0', '/2011/09/26//300-240-2011092811215682275.jpg'),
(93, '0', 'onm agency', '0', '/2011/09/26//300-240-2011092811260925322.jpg'),
(94, '', 'onm agency', '', '/2011/09/26//300-240-2011092811323734490.jpg'),
(181, '', 'onm agency', '', '/2011/09/29//300-240-2011092910563796697.jpg'),
(182, '', 'onm agency', '', '/2011/09/29//300-240-2011092910563818425.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `albums_photos`
--

CREATE TABLE IF NOT EXISTS `albums_photos` (
  `pk_album` bigint(20) unsigned NOT NULL,
  `pk_photo` bigint(20) unsigned NOT NULL,
  `position` int(10) DEFAULT '1',
  `description` varchar(250) DEFAULT NULL,
  KEY `pk_album_2` (`pk_album`,`pk_photo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `albums_photos`
--

INSERT INTO `albums_photos` (`pk_album`, `pk_photo`, `position`, `description`) VALUES
(89, 3, 5, ''),
(90, 38, 2, ''),
(93, 24, 8, 'uricatos, arena, beige'),
(91, 15, 6, ''),
(91, 42, 1, 'gris, bola'),
(91, 43, 2, 'aire, viento, '),
(91, 18, 3, ''),
(91, 41, 4, 'verde, clorofila'),
(91, 20, 5, ''),
(92, 27, 7, 'perro, deporte, balÃ³n'),
(92, 26, 6, 'perro, gafas, sol'),
(92, 28, 5, 'zorro, dormir'),
(92, 24, 4, 'uricatos, arena, beige'),
(92, 23, 3, ''),
(93, 23, 7, ''),
(93, 22, 6, ''),
(93, 27, 5, 'perro, deporte, balÃ³n'),
(93, 26, 4, 'perro, gafas, sol'),
(93, 30, 3, ''),
(93, 29, 2, ''),
(93, 28, 1, 'zorro, dormir'),
(92, 22, 8, ''),
(92, 30, 2, ''),
(92, 29, 1, ''),
(89, 4, 4, ''),
(89, 34, 1, ''),
(89, 35, 2, ''),
(89, 36, 3, ''),
(89, 5, 6, ''),
(90, 37, 1, ''),
(90, 7, 3, ''),
(90, 33, 4, ''),
(94, 25, 1, 'rojo, mariquitas'),
(94, 31, 2, 'pato, agua'),
(94, 16, 3, ''),
(94, 22, 4, ''),
(94, 17, 5, ''),
(181, 172, 1, 'pin-up art'),
(181, 174, 2, 'pin-up art'),
(181, 171, 3, 'pin-up art'),
(181, 173, 4, 'pin-up art'),
(181, 180, 5, 'pin-up art'),
(181, 179, 6, 'pin-up art'),
(181, 177, 7, 'pin-up art'),
(181, 178, 8, 'pin-up art'),
(181, 175, 9, 'pin-up art'),
(181, 176, 10, 'pin-up art'),
(182, 172, 1, 'pin-up art'),
(182, 174, 2, 'pin-up art'),
(182, 171, 3, 'pin-up art'),
(182, 173, 4, 'pin-up art'),
(182, 180, 5, 'pin-up art'),
(182, 179, 6, 'pin-up art'),
(182, 177, 7, 'pin-up art'),
(182, 178, 8, 'pin-up art'),
(182, 175, 9, 'pin-up art'),
(182, 176, 10, 'pin-up art');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `pk_article` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `summary` text,
  `body` text,
  `img1` bigint(20) unsigned DEFAULT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `img1_footer` varchar(250) DEFAULT NULL,
  `img2` bigint(20) unsigned DEFAULT NULL,
  `img2_footer` varchar(250) DEFAULT NULL,
  `agency` varchar(100) DEFAULT NULL,
  `fk_video` bigint(20) unsigned DEFAULT NULL,
  `with_comment` smallint(1) DEFAULT '1',
  `fk_video2` bigint(20) unsigned DEFAULT NULL COMMENT 'video interior',
  `footer_video2` varchar(150) DEFAULT NULL,
  `columns` smallint(1) DEFAULT '1',
  `home_columns` smallint(1) DEFAULT '1',
  `footer_video1` varchar(150) DEFAULT NULL,
  `title_int` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=168 ;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`pk_article`, `summary`, `body`, `img1`, `subtitle`, `img1_footer`, `img2`, `img2_footer`, `agency`, `fk_video`, `with_comment`, `fk_video2`, `footer_video2`, `columns`, `home_columns`, `footer_video1`, `title_int`) VALUES
(10, '', '', 0, 'ECONOMIA', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, '"Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit..."'),
(11, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>', 7, 'ECONOMIA', '', 0, '', 'EFE', 0, 1, 0, '', 0, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(12, '<p>Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>', 25, 'ECONOMIA', 'rojo, mariquitas', 0, '', 'EFE', 0, 1, 0, '', 0, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(13, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.', '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>\r\n<p>Cras neque nulla, dapibus id tempor et, scelerisque ut arcu. Cras  convallis justo vitae quam tempor euismod. In blandit placerat congue.  Suspendisse tempor mi et nisi faucibus congue. Curabitur in lobortis  quam. Nam blandit arcu vel metus sollicitudin adipiscing. Donec nisl  quam, tristique sit amet pharetra vitae, congue eu magna. Nulla a  lacinia turpis. Quisque viverra, purus sit amet auctor convallis, velit  ligula tempus augue, ac porta nisi risus non urna. Nulla vestibulum quam  sit amet lorem auctor ac gravida quam placerat. Morbi nec libero  tortor. Vivamus vel erat a erat ultricies pharetra.</p>\r\n</div>', 0, 'ECONOMIA', '', 0, '', 'EFE', 0, 1, 0, '', 0, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(14, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>', 168, 'ECONOMIA', 'luces naranjas', 170, 'luz en la ciudad', 'EFE', 0, 1, 0, '', 0, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...'),
(46, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.', '<p>&nbsp;</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>', 41, 'ECONOMIA', 'verde, clorofila', 31, 'pato, agua', 'EFE', 0, 1, 0, '', 0, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(47, '', '', 0, 'ECONOMIA', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...'),
(48, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>', 40, 'ECONOMIA', 'ipad, tablet', 44, 'mac, ipad, portatil', 'EFE', 0, 1, 0, '', 0, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(49, '', '', 0, 'ECONOMIA', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(50, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>', 23, 'ECONOMIA', '', 31, 'pato, agua', 'EFE', 0, 1, 0, '', 0, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...'),
(51, '<div id="lipsum">Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.\r\n<p>Cras neque nulla, dapibus id tempor et, scelerisque ut arcu. Cras  convallis justo vitae quam tempor euismod. In blandit placerat congue.  Suspendisse tempor mi et nisi faucibus congue. Curabitur in lobortis  quam. Nam blandit arcu vel metus sollicitudin adipiscing. Donec nisl  quam, tristique sit amet pharetra vitae, congue eu magna. Nulla a  lacinia turpis. Quisque viverra, purus sit amet auctor convallis, velit  ligula tempus augue, ac porta nisi risus non urna. Nulla vestibulum quam  sit amet lorem auctor ac gravida quam placerat. Morbi nec libero  tortor. Vivamus vel erat a erat ultricies pharetra.</p>\r\n</div>', '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>\r\n<p>Cras neque nulla, dapibus id tempor et, scelerisque ut arcu. Cras  convallis justo vitae quam tempor euismod. In blandit placerat congue.  Suspendisse tempor mi et nisi faucibus congue. Curabitur in lobortis  quam. Nam blandit arcu vel metus sollicitudin adipiscing. Donec nisl  quam, tristique sit amet pharetra vitae, congue eu magna. Nulla a  lacinia turpis. Quisque viverra, purus sit amet auctor convallis, velit  ligula tempus augue, ac porta nisi risus non urna. Nulla vestibulum quam  sit amet lorem auctor ac gravida quam placerat. Morbi nec libero  tortor. Vivamus vel erat a erat ultricies pharetra.</p>\r\n</div>', 33, 'ECONOMIA', '', 36, '', 'EFE', 0, 1, 0, '', 0, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(52, 'Nam  lobortis nibh eu ante molestie nec condimentum justo semper.  Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet,  pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum  porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem  velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus  accumsan sem  ipsum.', '<p>Nam  lobortis nibh eu ante molestie nec condimentum justo semper.  Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet,  pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum  porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem  velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus  accumsan sem  ipsum.Nam  lobortis nibh eu ante molestie nec condimentum justo semper.  Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet,  pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum  porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem  velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus  accumsan sem  ipsum.</p>\r\n<p>Nam  lobortis nibh eu ante molestie nec condimentum justo semper.  Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet,  pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum  porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem  velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus  accumsan sem  ipsum.Nam  lobortis nibh eu ante molestie nec condimentum justo semper.  Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet,  pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum  porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem  velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus  accumsan sem  ipsum.Nam  lobortis nibh eu ante molestie nec condimentum justo semper.  Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet,  pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum  porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem  velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus  accumsan sem  ipsum.</p>', 0, 'ECONOMIA', '', 0, '', 'EFE', 0, 1, 0, '', 0, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...'),
(53, 'Nam  lobortis nibh eu ante molestie nec condimentum justo semper.  Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet,  pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum  porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem  velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus  accumsan sem  ipsum.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>', 0, 'ECONOMIA', '', 0, '', 'EFE', 0, 1, 0, '', 0, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(61, '<div id="lipsum">Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</div>', '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>\r\n<p>Cras neque nulla, dapibus id tempor et, scelerisque ut arcu. Cras  convallis justo vitae quam tempor euismod. In blandit placerat congue.  Suspendisse tempor mi et nisi faucibus congue. Curabitur in lobortis  quam. Nam blandit arcu vel metus sollicitudin adipiscing. Donec nisl  quam, tristique sit amet pharetra vitae, congue eu magna. Nulla a  lacinia turpis. Quisque viverra, purus sit amet auctor convallis, velit  ligula tempus augue, ac porta nisi risus non urna. Nulla vestibulum quam  sit amet lorem auctor ac gravida quam placerat. Morbi nec libero  tortor. Vivamus vel erat a erat ultricies pharetra.</p>\r\n</div>', 22, 'LOREN IPSUM', '', 0, '', 'opennemas.com', 0, 0, 0, '', 0, 1, NULL, 'Nam viverra auctor orci id accumsan'),
(62, '', '', 0, 'DEPORTES', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(63, '', '', 0, 'CULTURA', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...'),
(64, '', '', 0, 'DEPORTES', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(65, '', '', 0, 'CULTURA', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...'),
(66, '', '', 0, 'CULTURA', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(67, '', '', 0, 'POLITICA', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(68, 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,   vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend   bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,   congue id ante. Donec non sapien purus', '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,   vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend   bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,   congue id ante. Donec non sapien purusCurabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,   vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend   bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,   congue id ante. Donec non sapien purusCurabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,   vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend   bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,   congue id ante. Donec non sapien purus</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,   vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend   bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,   congue id ante. Donec non sapien purusCurabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,   vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend   bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,   congue id ante. Donec non sapien purus</p>', 0, 'SOCIEDAD', '', 0, '', 'EFE', 0, 1, 0, '', 0, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...'),
(69, '', '', 0, 'DEPORTES', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(70, '', '', 0, 'DEPORTES', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(71, '', '', 0, 'CULTURA', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(72, '', '', 0, 'POLITICA', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(73, '', '', 0, 'DEPORTES', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(74, '', '', 0, 'CULTURA', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(75, '', '', 0, 'POLITICA', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(76, '', '', 0, 'SOCIEDAD', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...'),
(77, '', '', 0, 'DEPORTES', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(78, '', '', 0, 'DEPORTES', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(79, '', '', 0, 'CULTURA', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(80, '', '', 0, 'POLITICA', '', 0, '', 'EFE', 0, 1, 0, '', 1, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(81, '', '', 44, 'DEPORTES', 'mac, ipad, portatil', 0, '', 'EFE', 0, 1, 0, '', 0, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(82, '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus.</p>', '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>', 42, 'LOREN IPSUM', 'gris, bola', 0, '', 'Agencia EFE', 0, 1, 0, '', 0, 1, NULL, 'Nam viverra auctor orci id accumsan.'),
(84, '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>', '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>', 0, 'LOREN IPSUM', '', 29, 'perro, negro, cachorro', 'Agencia EFE', 0, 1, 0, '', 0, 1, NULL, 'Nam viverra auctor orci id accumsan.'),
(85, '<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>', '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>', 0, 'LOREN IPSUM', '', 0, '', 'Agencia onm', 0, 1, 0, '', 0, 1, NULL, 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna, vel ullamcorper dui lacus ut urna');
INSERT INTO `articles` (`pk_article`, `summary`, `body`, `img1`, `subtitle`, `img1_footer`, `img2`, `img2_footer`, `agency`, `fk_video`, `with_comment`, `fk_video2`, `footer_video2`, `columns`, `home_columns`, `footer_video1`, `title_int`) VALUES
(86, '&nbsp;Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis. <br />', '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>', 40, 'CURABITUR VIVERRA, NEQUE AC DAPIBUS IACULIS, DUI TORTOR DAPIBUS URNA, VEL ULLAMCORPER DUI LACUS UT URNA. NULLA SAPIEN LOREM, GRAVIDA ELEIFEND BIBENDUM A, TEMPOR ET MI. NULLAM ANTE JUSTO, INTERDUM AT INTERDUM VEL, CONGUE ID ANTE. DONEC NON SAPIEN PURU', 'ipad, tablet', 0, '', 'Agencia onm', 0, 1, 0, '', 0, 1, NULL, 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna, vel ullamcorper dui lacus ut urna. '),
(87, 'Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.', '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>', 20, 'LOREN IPSUM', '', 0, '', 'Agencia onm', 0, 1, 0, '', 0, 1, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(167, 'Suspendisse sollicitudin turpis sit amet nisl volutpat  tincidunt.  Phasellus pellentesque pulvinar rutrum. Ut interdum malesuada  nunc vel  viverra. Ut porta facilisis neque, a vestibulum sem volutpat   adipiscing. Praesent at rhoncus nisi. Nulla eget quam neque, porta   molestie tellus. Mauris sit amet massa lectus. Suspendisse potenti.   Donec augue elit, suscipit eu pellentesque vitae, ornare cursus libero.   Donec vestibulum, augue at accumsan sodales, metus ante suscipit quam,   non iaculis leo magna porttitor tortor.', '<div>\r\n<p>Suspendisse sollicitudin turpis sit amet nisl volutpat  tincidunt. Phasellus pellentesque pulvinar rutrum. Ut interdum malesuada  nunc vel viverra. Ut porta facilisis neque, a vestibulum sem volutpat  adipiscing. Praesent at rhoncus nisi. Nulla eget quam neque, porta  molestie tellus. Mauris sit amet massa lectus. Suspendisse potenti.  Donec augue elit, suscipit eu pellentesque vitae, ornare cursus libero.  Donec vestibulum, augue at accumsan sodales, metus ante suscipit quam,  non iaculis leo magna porttitor tortor.</p>\r\n<p>Sed id orci eu tortor accumsan lobortis. Curabitur pretium turpis  vitae tellus vestibulum vel tempor nunc sollicitudin. Pellentesque ac  lacus a diam mattis posuere quis quis odio. Phasellus convallis purus at  ligula auctor eget ultricies justo ultricies. Phasellus varius  malesuada tellus, sit amet rutrum dui lobortis ut. Duis tristique  feugiat orci, a congue turpis pretium quis. Maecenas tempor, nisl  molestie ultricies aliquam, ipsum metus semper sem, pharetra pharetra  est erat quis eros. Cum sociis natoque penatibus et magnis dis  parturient montes, nascetur ridiculus mus. Integer egestas, nisi quis  gravida placerat, turpis libero semper eros, sed faucibus tellus orci  placerat diam.</p>\r\n</div>', 0, 'Loren Ipsum', '', 32, '', 'opennemas.com', 0, 0, 0, '', 1, 1, NULL, 'Suspendisse sollicitudin turpis ');

-- --------------------------------------------------------

--
-- Table structure for table `articles_clone`
--

CREATE TABLE IF NOT EXISTS `articles_clone` (
  `pk_original` bigint(20) NOT NULL,
  `pk_clone` bigint(20) NOT NULL,
  `params` text,
  KEY `pk_original` (`pk_original`),
  KEY `pk_clone` (`pk_clone`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table to control articles clone';

--
-- Dumping data for table `articles_clone`
--


-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE IF NOT EXISTS `attachments` (
  `pk_attachment` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `path` varchar(200) NOT NULL,
  `category` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`pk_attachment`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `attachments`
--


-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE IF NOT EXISTS `authors` (
  `pk_author` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `politics` varchar(50) DEFAULT NULL,
  `date_nac` datetime DEFAULT NULL,
  `fk_user` int(10) DEFAULT '0',
  `condition` varchar(255) DEFAULT NULL,
  `blog` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_author`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Opinion Authors' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`pk_author`, `name`, `politics`, `date_nac`, `fk_user`, `condition`, `blog`) VALUES
(3, 'Cartas al Director', '', '0000-00-00 00:00:00', 0, '', ''),
(2, 'Director', NULL, NULL, 0, NULL, NULL),
(1, 'Editorial', NULL, NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `author_imgs`
--

CREATE TABLE IF NOT EXISTS `author_imgs` (
  `pk_img` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_author` int(10) NOT NULL,
  `fk_photo` bigint(20) unsigned NOT NULL,
  `path_img` varchar(250) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`pk_img`),
  KEY `pk_img` (`pk_img`,`fk_author`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `author_imgs`
--


-- --------------------------------------------------------

--
-- Table structure for table `bulletins_archive`
--

CREATE TABLE IF NOT EXISTS `bulletins_archive` (
  `pk_bulletin` int(11) NOT NULL AUTO_INCREMENT,
  `data` longtext CHARACTER SET latin1,
  `contact_list` longtext CHARACTER SET latin1,
  `attach_pdf` tinyint(4) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cron_timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`pk_bulletin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `bulletins_archive`
--


-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `pk_comment` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(150) DEFAULT NULL,
  `ciudad` varchar(150) DEFAULT NULL,
  `sexo` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `body` text,
  `ip` varchar(20) DEFAULT NULL,
  `published` datetime DEFAULT NULL,
  `fk_content` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`pk_comment`),
  KEY `fk_content` (`fk_content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `comments`
--


-- --------------------------------------------------------

--
-- Table structure for table `contents`
--

CREATE TABLE IF NOT EXISTS `contents` (
  `pk_content` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_content_type` int(10) unsigned NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_spanish_ci,
  `metadata` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci DEFAULT NULL,
  `starttime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `changed` datetime DEFAULT NULL,
  `content_status` int(10) unsigned DEFAULT '0',
  `fk_author` int(10) unsigned DEFAULT NULL COMMENT 'Clave foranea de user',
  `fk_publisher` int(10) unsigned DEFAULT NULL COMMENT 'Clave foranea de user',
  `fk_user_last_editor` int(10) unsigned DEFAULT NULL COMMENT 'Clave foranea de user',
  `views` int(10) unsigned DEFAULT NULL,
  `position` int(10) unsigned DEFAULT '100',
  `frontpage` tinyint(1) DEFAULT '1',
  `in_litter` tinyint(1) DEFAULT '0' COMMENT '0publicado 1papelera',
  `in_home` smallint(1) DEFAULT '0',
  `home_pos` int(10) DEFAULT '100' COMMENT '10',
  `slug` varchar(255) DEFAULT NULL,
  `available` smallint(1) DEFAULT '1',
  `placeholder` varchar(64) DEFAULT NULL COMMENT 'Placeholder for a content in frontpage',
  `home_placeholder` varchar(64) DEFAULT NULL COMMENT 'Placeholder for a content in home',
  `params` longtext,
  `category_name` varchar(255) NOT NULL COMMENT 'name category',
  `favorite` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`pk_content`),
  KEY `fk_content_type` (`fk_content_type`),
  KEY `in_litter` (`in_litter`),
  KEY `content_status` (`content_status`),
  KEY `in_home` (`in_home`),
  KEY `frontpage` (`frontpage`),
  KEY `available` (`available`),
  KEY `starttime` (`starttime`,`endtime`),
  KEY `created` (`created`),
  FULLTEXT KEY `metadata` (`metadata`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=183 ;

--
-- Dumping data for table `contents`
--

INSERT INTO `contents` (`pk_content`, `fk_content_type`, `title`, `description`, `metadata`, `starttime`, `endtime`, `created`, `changed`, `content_status`, `fk_author`, `fk_publisher`, `fk_user_last_editor`, `views`, `position`, `frontpage`, `in_litter`, `in_home`, `home_pos`, `slug`, `available`, `placeholder`, `home_placeholder`, `params`, `category_name`, `favorite`) VALUES
(1, 8, '2010071123304980257.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-09-26 23:41:01', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010071123304980257-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(2, 8, '2010071123304911590.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-09-26 23:41:01', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010071123304911590-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(3, 8, 'motorcycle-off-road-587-22.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-09-26 23:41:01', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'motorcycle-off-road-587-22-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(4, 8, 'motorcycle-off-road-587-2.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-09-26 23:41:01', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'motorcycle-off-road-587-2-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(5, 8, 'motorcycle-off-road-588-2.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-09-26 23:41:01', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'motorcycle-off-road-588-2-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(6, 8, 'swimming-photography-652-2.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-09-26 23:41:01', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'swimming-photography-652-2-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(7, 8, 'swimming-photography-652-6.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-09-26 23:41:01', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'swimming-photography-652-6-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(8, 8, 'swimming-photography-652-8.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-09-26 23:41:01', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'swimming-photography-652-8-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(9, 8, '2010051323061367337.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-09-26 23:41:01', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010051323061367337-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(10, 1, '"Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit..."', '', ',neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-23 20:30:48', 1, 7, 0, 7, 3, 1, 1, 0, 0, 100, '-neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_1_0', 'placeholder_0_1', NULL, 'economia', NULL),
(11, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ...', 'economÃ­a, neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 14:41:48', 1, 7, 0, 7, 1, 2, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_0', 'placeholder_0_1', NULL, 'economia', NULL),
(12, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa....', 'economÃ­a, neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 21:45:01', 1, 7, 0, 7, 1, 3, 1, 0, 1, 1, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_2_1', 'placeholder_2_3', NULL, 'economia', NULL),
(13, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...', '\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa....', 'economÃ­a, neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 14:41:48', 1, 7, 0, 7, 1, 1, 1, 0, 0, 100, '', 1, 'placeholder_2_1', 'placeholder_0_1', NULL, 'economia', NULL),
(14, 1, 'Nam viverra auctor orci id accumsan.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa....', 'economÃ­a, neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-29 10:50:53', 1, 7, 0, 7, 2, 1, 1, 0, 1, 1, 'nam-viverra-auctor-orci-id-accumsan', 1, 'placeholder_highlighted_0', 'placeholder_highlighted_0', NULL, 'economia', NULL),
(15, 8, 'stock-photo-speed_rev100.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:42:32', '2011-09-29 12:50:50', 1, 7, 0, 5, 1, 2, 0, 0, 0, 100, 'stock-photo-speedrev100-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'economia', NULL),
(16, 8, 'samsung-galaxy-tab-1-wallpaper.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:42:32', '2011-09-29 12:50:50', 1, 7, 0, 5, 1, 2, 0, 0, 0, 100, 'samsung-galaxy-tab-1-wallpaper-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'economia', NULL),
(17, 8, 'samsung-galaxy-s-official-21.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:42:32', '2011-09-29 12:50:50', 1, 7, 0, 5, 1, 2, 0, 0, 0, 100, 'samsung-galaxy-s-official-21-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'economia', NULL),
(18, 8, 'galaxy-640x480-1.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:44:05', '2011-09-26 23:48:34', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'galaxy-640x480-1-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', NULL),
(19, 8, 'galaxy-eso-593-8.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:44:05', '2011-09-26 23:48:34', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'galaxy-eso-593-8-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', NULL),
(20, 8, 'm31_ware.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:44:05', '2011-09-26 23:48:34', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'm31ware-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', NULL),
(21, 8, 'motorcycle-off-road-587-22.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:44:05', '2011-09-26 23:48:34', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'motorcycle-off-road-587-22-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', NULL),
(22, 8, 'animal-photography-868-2.jpg', '', 'loros rojo, ojos, pico', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:45:50', '2011-09-26 23:48:34', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'animal-photography-868-2-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', NULL),
(23, 8, 'animal-photography-868-4.jpg', '', 'lobo, blanco, nieve', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:45:51', '2011-09-26 23:48:34', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'animal-photography-868-4-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', NULL),
(24, 8, 'animal-photography-868-8.jpg', 'uricatos, arena, beige', 'uricatos, arena, beige', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:45:51', '2011-09-26 23:48:34', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'animal-photography-868-8-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', NULL),
(25, 8, 'animal-photography-868-16.jpg', 'rojo, mariquitas', 'rojo, mariquitas', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:45:51', '2011-09-26 23:48:34', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'animal-photography-868-16-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', NULL),
(26, 8, 'fun-animals-742-2.jpg', 'perro, gafas, sol', 'perro, gafas, sol', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:45:51', '2011-09-26 23:48:34', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'fun-animals-742-2-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', NULL),
(27, 8, 'fun-animals-742-4.jpg', 'perro, deporte, balÃ³n', 'perro, deporte, balÃ³n', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:45:51', '2011-09-26 23:48:34', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'fun-animals-742-4-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', NULL),
(28, 8, 'fun-animals-742-8.jpg', 'zorro, dormir', 'zorro, dormir', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:45:52', '2011-09-26 23:48:34', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'fun-animals-742-8-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', NULL),
(29, 8, 'fun-animals-742-12.jpg', 'perro, negro, cachorro', 'perro, negro, cachorro', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:45:52', '2011-09-26 23:48:34', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'fun-animals-742-12-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', NULL),
(30, 8, 'fun-animals-742-14.jpg', 'perro, dico, cesped', 'perro, dico, cesped', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:45:52', '2011-09-26 23:48:34', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'fun-animals-742-14-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', NULL),
(31, 8, 'fun-animals-742-38.jpg', 'pato, agua', 'pato, agua', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:45:52', '2011-09-26 23:48:34', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'fun-animals-742-38-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', NULL),
(32, 8, 'diving-photography-662-2.jpg', '', 'deportes, piscina, nadar, salto trampolin', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:50:33', '2011-09-26 23:52:45', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'diving-photography-662-2-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(33, 8, 'diving-photography-662-8.jpg', '', 'deportes, piscina, nadar, salto trampolin', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:50:33', '2011-09-26 23:52:45', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'diving-photography-662-8-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(34, 8, 'motorcycle-off-road-587-2.jpg', '', 'deportes, moto, desierto, cross', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:50:34', '2011-09-26 23:52:45', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'motorcycle-off-road-587-2-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(35, 8, 'motorcycle-off-road-587-22.jpg', '', 'deportes, moto, desierto, cross', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:50:34', '2011-09-26 23:52:45', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'motorcycle-off-road-587-22-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(36, 8, 'motorcycle-off-road-588-2.jpg', '', 'deportes, moto, saltos, cross', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:50:34', '2011-09-26 23:52:45', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'motorcycle-off-road-588-2-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(37, 8, 'swimming-photography-652-2.jpg', '', 'piscina, nadador, agua', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:50:34', '2011-09-26 23:52:45', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'swimming-photography-652-2-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(38, 8, 'swimming-photography-652-8.jpg', '', 'piscina, nadador, salto, agua', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:50:34', '2011-09-26 23:52:45', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'swimming-photography-652-8-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(39, 8, 'Want-a-Free-Xbox-Buy-a-Laptop-for-College2.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:50:34', '2011-09-26 23:52:45', 0, 7, 0, 7, 1, 2, 0, 1, 0, 100, 'want-a-free-xbox-buy-a-laptop-for-college2-jpg', 0, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(40, 8, '1004ipad_hometimes-420-90.jpg', 'ipad, tablet', 'ipad, tablet', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:53:56', '2011-09-26 23:55:45', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '1004ipadhometimes-420-90-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(41, 8, 'stock-photo-abstract-galaxy-perfect-background-with-space-for-text-or-image_rev100.jpg', 'verde, clorofila', 'verde, clorofila', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:53:56', '2011-09-26 23:55:45', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'stock-photo-abstract-galaxy-perfect-background-with-space-for-text-or-imagerev100-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(42, 8, 'stock-photo-close-up-of-newton-s-cradle-d-render_rev100.jpg', 'gris, bola', 'gris, bola', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:53:56', '2011-09-26 23:55:45', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'stock-photo-close-up-of-newton-s-cradle-d-renderrev100-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(43, 8, 'stock-photo-ecological-breeze_rev100.jpg', 'aire, viento, ', 'aire, viento, ', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:53:56', '2011-09-26 23:55:45', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'stock-photo-ecological-breezerev100-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(44, 8, 'Want-a-Free-Xbox-Buy-a-Laptop-for-College2.jpg', 'mac, ipad, portatil', 'mac, ipad, portatil', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-26 23:53:56', '2011-09-26 23:55:45', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'want-a-free-xbox-buy-a-laptop-for-college2-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(45, 7, 'jumping', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue. ', 'jumping', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 11:41:24', '2011-09-27 11:41:24', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'jumping', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(46, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '&nbsp;\r\nCurabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut...', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 14:43:09', 1, 7, 0, 7, 1, 1, 1, 0, 1, 1, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_2_3', 'placeholder_1_0', NULL, 'economia', NULL),
(47, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 14:43:39', 1, 7, 0, 7, 1, 1, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_2_2', 'placeholder_0_1', NULL, 'economia', NULL),
(48, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ...', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 14:43:38', 1, 7, 0, 7, 1, 1, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_4', 'placeholder_0_1', NULL, 'economia', NULL),
(49, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 14:43:40', 1, 7, 0, 7, 2, 2, 1, 0, 1, 1, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_2_1', 'placeholder_0_5', NULL, 'economia', NULL),
(54, 4, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 14:47:18', '2011-09-27 14:47:18', 1, 7, 7, 7, 4, 1, 0, 0, 1, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(50, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 14:45:29', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'economia', NULL),
(51, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:12:04', 1, 7, 0, 7, 1, 1, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_0', 'placeholder_0_1', NULL, 'economia', NULL),
(52, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...', '\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa....', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:12:04', 1, 7, 0, 7, 2, 1, 1, 0, 1, 1, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_1', 'placeholder_2_1', NULL, 'cultura', NULL),
(53, 1, 'Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa....', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 21:43:00', 1, 7, 0, 7, 3, 1, 1, 0, 1, 2, 'fusce-rutrum-porttitor-urna-aliquet-imperdiet-dolor-fringilla-eu', 1, 'placeholder_0_0', 'placeholder_2_1', NULL, 'economia', NULL),
(55, 4, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 14:47:18', '2011-09-27 14:47:18', 1, 7, 7, 7, 2, 1, 0, 0, 1, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(56, 4, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 14:47:54', '2011-09-27 14:47:54', 1, 7, 7, 7, 5, 1, 0, 0, 1, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(57, 4, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 15:04:18', '2011-09-27 15:04:19', 1, 7, 7, 7, 1, 1, 0, 0, 1, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(58, 4, 'Nam viverra auctor orci id accumsan.', '', 'nam, viverra, auctor, orci, id, accumsan', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 15:04:46', '2011-09-27 15:04:46', 1, 7, 7, 7, 2, 1, 0, 0, 1, 100, 'nam-viverra-auctor-orci-id-accumsan', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(59, 4, 'Nam viverra auctor orci id accumsan.', '', 'nam, viverra, auctor, orci, id, accumsan', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 15:04:46', '2011-09-27 15:04:46', 1, 7, 7, 7, 1, 1, 0, 0, 1, 100, 'nam-viverra-auctor-orci-id-accumsan', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(60, 4, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 15:05:02', '2011-09-27 15:05:02', 1, 7, 7, 7, 3, 1, 0, 0, 1, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(61, 1, 'Nam viverra auctor orci id accumsan', '\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa....', 'sociedad, nam, viverra, auctor, orci, id, accumsan, unknown', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 15:06:26', '2011-09-27 21:44:33', 1, 7, 0, 7, 1, 2, 1, 0, 1, 2, 'nam-viverra-auctor-orci-id-accumsan', 1, 'placeholder_2_1', 'placeholder_2_3', NULL, 'sociedad', NULL),
(62, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:23:55', 1, 7, 0, 7, 3, 1, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(63, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:23:57', 1, 7, 0, 7, 3, 1, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_2_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(64, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:23:57', 1, 7, 0, 7, 4, 1, 1, 0, 1, 1, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_2_2', 'placeholder_2_0', NULL, 'deportes', NULL),
(65, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:23:58', 1, 7, 0, 7, 1, 1, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(66, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:33', 1, 7, 0, 7, 1, 1, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_2_2', 'placeholder_0_1', NULL, 'cultura', NULL),
(67, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:34', 1, 7, 0, 7, 1, 1, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_1_3', 'placeholder_0_1', NULL, 'politica', NULL),
(68, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...', 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,   vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend   bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,   congue id ante. Donec non sapien purusCurabitur viverra, neque ac...', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 21:58:37', 1, 7, 0, 7, 1, 1, 1, 0, 1, 2, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_2_1', 'placeholder_0_5', NULL, 'sociedad', NULL),
(69, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:37', 1, 7, 0, 7, 1, 2, 1, 0, 1, 2, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_1', 'placeholder_2_2', NULL, 'deportes', NULL),
(70, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:38', 1, 7, 0, 7, 1, 100, 0, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, '0', 'placeholder_0_1', NULL, 'deportes', NULL),
(71, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:39', 1, 7, 0, 7, 1, 1, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_2_0', 'placeholder_0_1', NULL, 'cultura', NULL),
(72, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:40', 1, 7, 0, 7, 1, 1, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_1_2', 'placeholder_0_1', NULL, 'politica', NULL),
(73, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:41', 1, 7, 0, 7, 1, 1, 1, 0, 1, 3, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_highlighted_0', 'placeholder_2_1', NULL, 'deportes', NULL),
(74, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:42', 1, 7, 0, 7, 1, 2, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(75, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:42', 1, 7, 0, 7, 1, 1, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_3', 'placeholder_0_1', NULL, 'politica', NULL),
(76, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:43', 1, 7, 0, 7, 1, 1, 1, 0, 2, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_2', 'placeholder_0_1', NULL, 'sociedad', NULL),
(77, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:44', 1, 7, 0, 7, 1, 100, 0, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, '0', 'placeholder_0_1', NULL, 'deportes', NULL),
(78, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:45', 1, 7, 0, 7, 1, 1, 1, 0, 1, 1, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_1_3', 'placeholder_0_4', NULL, 'deportes', NULL),
(79, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:45', 1, 7, 0, 7, 1, 2, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_2_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(80, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:46', 1, 7, 0, 7, 4, 1, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_0_4', 'placeholder_0_1', NULL, 'politica', NULL),
(81, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '...', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 21:32:34', 1, 7, 0, 7, 1, 1, 1, 0, 1, 1, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_2_1', 'placeholder_2_2', NULL, 'deportes', NULL),
(82, 1, 'Nam viverra auctor orci id accumsan.', 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut...', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 15:25:40', '2011-09-27 21:44:05', 1, 7, 0, 7, 19, 1, 1, 0, 1, 2, 'nam-viverra-auctor-orci-id-accumsan', 1, 'placeholder_highlighted_0', 'placeholder_0_1', NULL, 'sociedad', NULL),
(84, 1, 'Nam viverra auctor orci id accumsan.', 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut...', 'sociedad, nam, viverra, auctor, orci, id, accumsan, unknown', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 15:25:40', '2011-09-27 21:57:56', 1, 7, 0, 7, 1, 1, 1, 0, 0, 100, 'nam-viverra-auctor-orci-id-accumsan', 1, 'placeholder_1_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(85, 1, 'Nam viverra auctor orci id accumsan.', 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut...', 'nam, viverra, auctor, orci, id, accumsan', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 15:25:40', '2011-09-27 15:33:14', 1, 7, 0, 7, 1, 1, 1, 0, 1, 1, 'nam-viverra-auctor-orci-id-accumsan', 1, 'placeholder_1_0', 'placeholder_0_1', NULL, 'politica', NULL),
(86, 1, 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna, vel ullamcorper dui lacus ut urna', 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut...', 'neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 15:25:40', '2011-09-27 15:33:14', 1, 7, 0, 7, 2, 1, 1, 0, 0, 100, 'curabitur-viverra-neque-ac-dapibus-iaculis-dui-tortor-dapibus-urna-vel-ullamcorper-dui-lacus-ut-urna', 1, 'placeholder_0_0', 'placeholder_0_1', NULL, 'politica', NULL),
(87, 1, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut...', 'nam, viverra, auctor, orci, id, accumsan', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 15:25:40', '2011-09-27 15:33:14', 1, 7, 0, 7, 2, 1, 1, 0, 1, 1, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'placeholder_2_0', 'placeholder_0_3', NULL, 'politica', NULL),
(88, 7, 'dfgf', '', 'dfgf', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-27 23:31:57', '2011-09-27 23:31:57', 0, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'dfgf', 0, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(89, 7, 'Etiam sed venenatis libero.', 'Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. ', 'etiam, sed, venenatis, libero, donec, augue, mauris, sdf', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 10:05:55', '2011-09-28 11:30:14', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'etiam-sed-venenatis-libero', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', 1),
(90, 7, 'Donec ultricies tincidunt ultrices', 'Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum.', 'donec, ultricies, tincidunt, ultrices', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 10:05:55', '2011-09-28 11:30:15', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'donec-ultricies-tincidunt-ultrices', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(91, 7, 'Donec ultricies tincidunt ultrices. ', ' Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum', 'donec, ultricies, tincidunt, ultrices, pruebasdf', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 10:31:05', '2011-09-28 11:25:10', 0, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'donec-ultricies-tincidunt-ultrices', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', 0),
(92, 7, 'Nam lobortis nibh eu ante molestie nec condimentum justo semper. ', 'Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero.  ', 'nam, lobortis, nibh, eu, molestie, nec, condimentum, justo, semper, mascotas, divertidas', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 11:21:56', '2011-09-28 11:22:46', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'nam-lobortis-nibh-eu-ante-molestie-nec-condimentum-justo-semper', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(93, 7, 'Donec augue mauris ', 'Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero.  ', 'donec, augue, mauris, nam, lobortism, justo, semper, lobortis, nibh, eu, molestie, nec, condimentum, mascotas, divertidas', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 11:21:56', '2011-09-28 11:26:09', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'donec-augue-mauris', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', 1),
(94, 7, 'Vivamus accumsan sem ipsum.', 'Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum.', 'vivamus, accumsan, sem, ipsum', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 11:32:37', '2011-09-28 11:32:37', 0, 7, 7, 7, 3, 2, 0, 0, 0, 100, 'vivamus-accumsan-sem-ipsum', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'fotos-de-hoy', 1),
(95, 9, 'Sheeped Away', 'Sheeped Away', 'sheeped, away', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 12:47:30', '2011-09-28 12:47:30', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'sheeped-away', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'curiosidades', 1),
(96, 9, '13957 people dancing thriller in Mexico 1 of 2 HQ', '13957 people dancing thriller in Mexico 1 of 2 HQ', 'people, dancing, thriller, in, mexico, of, hq, ', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 12:48:51', '2011-09-28 12:48:51', 1, 7, 7, 7, 3, 2, 0, 0, 0, 100, '13957-people-dancing-thriller-in-mexico-1-of-2-hq', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'musica', NULL),
(97, 9, 'Audi S3 Model 2009', 'Audi S3 Model 2009', 'audi, s3, model', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 12:51:11', '2011-09-28 12:51:11', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'audi-s3-model-2009', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(98, 9, 'Real Scenes: Detroit', 'http://vimeo.com/27476225', 'real, scenes, detroit', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 12:51:28', '2011-09-28 12:51:28', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'real-scenes-detroit', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', 0),
(99, 9, 'Coldplay - Every Teardrop Is A Waterfall', 'cColdplay - Every Teardrop Is A Waterfall', 'coldplay, every, teardrop, is, waterfall', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 12:52:27', '2011-09-28 12:52:27', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'coldplay-every-teardrop-is-a-waterfall', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'musica', 1),
(100, 9, 'Marilyn Monroe Happy Birthday Mr President', 'Marilyn Monroe Happy Birthday Mr President', 'marilyn, monroe, happy, birthday, mr, president', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 12:55:57', '2011-09-28 12:55:57', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'marilyn-monroe-happy-birthday-mr-president', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(101, 9, 'Trailer Millennium: Los hombres que no amaban a las mujeres', 'Trailer Millennium: Los hombres que no amaban a las mujeres', 'Trailer, Millennium', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 12:56:40', '2011-09-28 12:56:40', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'trailer-millennium-los-hombres-que-no-amaban-a-las-mujeres', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(102, 9, 'Mystique', 'mystique', 'mystique', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 12:57:25', '2011-09-28 12:57:25', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'mystique', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', 1),
(103, 9, 'How to Get Fit - P90X Video with Tony Horton!', 'p90x', 'how, to, get, fit, p90x, video, with, tony, horton', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 12:58:48', '2011-09-28 12:58:48', 1, 7, 7, 7, 5, 2, 0, 0, 0, 100, 'how-to-get-fit-p90x-video-with-tony-horton', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', 1),
(104, 9, 'Larry Johnson, la ''abuela'' mÃ¡s famosa de la NBA', 'Larry Johnson, la ''abuela'' mÃ¡s famosa de la NBA', 'larry, johnson, abuela, mÃ¡s, famosa, nba', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 13:04:58', '2011-09-28 13:04:58', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'larry-johnson-la-abuela-mas-famosa-de-la-nba', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(105, 9, 'Video de la cabaÃ±a argentina', 'Video de la cabaÃ±a argentina', 'video, cabaÃ±a, argentina, deo, caba', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 13:10:34', '2011-09-28 13:10:34', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'video-de-la-cabana-argentina', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'curiosidades', NULL),
(106, 9, 'Ð¤ÐµÐ¹ÐµÑ€Ð²ÐµÑ€Ðº Ñ€Ð°Ð·Ð±ÑƒÑˆÐµÐ²Ð°Ð»ÑÑ', 'video con servicio de Rutube', 'rutube', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 13:11:10', '2011-09-28 13:11:10', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, '', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(107, 9, 'Pau entra por la puerta grande en el club de los 10.000', 'Pau entra por la puerta grande en el club de los 10.000', 'pau, entra, puerta, grande, club, 000', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 13:14:00', '2011-09-28 13:14:00', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'pau-entra-por-la-puerta-grande-en-el-club-de-los-10-000', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(108, 9, 'Camelos.Semos. Jonathan. TÃº si que vales.', 'Camelos.Semos. Jonathan. TÃº si que vales.', 'camelos, semos, jonathan, tÃº, vales', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 13:14:30', '2011-09-28 13:14:30', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'camelos-semos-jonathan-tu-si-que-vales', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'curiosidades', NULL),
(109, 9, 'X men first class reviewed by rotten tomatoes on infomania', 'X men first class reviewed by rotten tomatoes on infomania', 'men, first, class, reviewed, by, rotten, tomatoes, on, infomania, neither, user, id, nor, item, is, specified, for, rss, feed', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 13:16:05', '2011-09-28 13:16:05', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'x-men-first-class-reviewed-by-rotten-tomatoes-on-infomania', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(110, 9, 'Discurso de Ãlex de la Iglesia en los Goya 2011', 'Discurso de Ãlex de la Iglesia en los Goya 2011', 'discurso, Ã¡lex, iglesia, goya', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 13:16:18', '2011-09-28 13:16:18', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'discurso-de-alex-de-la-iglesia-en-los-goya-2011', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(111, 8, '2009031213025692478.swf', 'pubicidad Habitat galego', 'pubicidad, habitat, galego', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 22:58:27', '2011-09-28 23:09:41', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2009031213025692478-swf', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(112, 8, '2009031319391656026.gif', 'pubicidad turismo de galicia', 'pubicidad, turismo, galicia', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 22:58:28', '2011-09-28 23:09:41', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2009031319391656026-gif', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(113, 8, '2009031420225242693.gif', 'pubicidad turismo de galicia', 'pubicidad, turismo, galicia', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 22:58:28', '2011-09-28 23:09:41', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2009031420225242693-gif', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(114, 8, '2011050112380435916.jpg', 'solidaria', 'solidaria', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 22:58:28', '2011-09-28 23:09:41', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2011050112380435916-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(115, 8, '2011091915514950772.jpg', 'gestor de contenidos para periÃ³dicos digitales', 'gestor, contenidos, periÃ³dicos, digitales, periodicos', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 22:58:28', '2011-09-28 23:09:41', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2011091915514950772-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(116, 8, '2011091915514971387.jpg', 'gestor de contenidos para periÃ³dicos digitales', 'gestor, contenidos, periÃ³dicos, digitales', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 22:58:28', '2011-09-28 23:09:41', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2011091915514971387-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL);
INSERT INTO `contents` (`pk_content`, `fk_content_type`, `title`, `description`, `metadata`, `starttime`, `endtime`, `created`, `changed`, `content_status`, `fk_author`, `fk_publisher`, `fk_user_last_editor`, `views`, `position`, `frontpage`, `in_litter`, `in_home`, `home_pos`, `slug`, `available`, `placeholder`, `home_placeholder`, `params`, `category_name`, `favorite`) VALUES
(117, 8, '2011091915515137977.jpg', 'gestor de contenidos para periÃ³dicos digitales', 'gestor, contenidos, periÃ³dicos, digitales', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 22:58:29', '2011-09-28 23:09:41', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2011091915515137977-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(118, 8, 'eu.gal.png', 'dominio .gal', 'dominio, gal', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-09-28 23:09:41', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'eu-gal-png', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(119, 8, '2010052617333374336.jpg', 'opennemas, gestor de contenidos para periÃ³dicos digitales', 'opennemas, gestor, contenidos, periÃ³dicos, digitales', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-09-28 23:09:41', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052617333374336-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(120, 8, '2010052617333370388.jpg', 'opennemas, gestor de contenidos para periÃ³dicos digitales', 'opennemas, gestor, contenidos, periÃ³dicos, digitales', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-09-28 23:09:41', 0, 7, 0, 7, 1, 2, 0, 1, 0, 100, '2010052617333370388-jpg', 0, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(121, 8, '2010052617333365419.jpg', 'opennemas, gestor de contenidos para periÃ³dicos digitales', 'opennemas, gestor, contenidos, periÃ³dicos, digitales', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-09-28 23:09:41', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052617333365419-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(122, 8, '2010052616215175024.jpg', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-09-28 23:09:25', 0, 7, 0, 7, 1, 2, 0, 1, 0, 100, '2010052616215175024-jpg', 0, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(123, 8, '2010052616215169156.jpg', 'opennemas, gestor de contenidos para periÃ³dicos digitales', 'opennemas, gestor, contenidos, periÃ³dicos, digitales', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-09-28 23:09:41', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052616215169156-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(124, 8, '2010052616215165008.jpg', 'opennemas, gestor de contenidos para periÃ³dicos digitales', 'opennemas, gestor, contenidos, periÃ³dicos, digitales', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-09-28 23:09:41', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052616215165008-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(125, 8, '2010052616215162000.jpg', 'opennemas, gestor de contenidos para periÃ³dicos digitales', 'opennemas, gestor, contenidos, periÃ³dicos, digitales', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-09-28 23:09:41', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052616215162000-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(126, 8, '2010052616215164250.jpg', 'opennemas, gestor de contenidos para periÃ³dicos digitales', 'opennemas, gestor, contenidos, periÃ³dicos, digitales', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-09-28 23:09:41', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052616215164250-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'publicidad', NULL),
(127, 2, 'Publicidad Portada top left', '', 'publicidad, portada, top, left', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:13:50', '2011-09-28 23:13:50', 0, 7, 7, 7, 27, 2, 0, 0, 0, 100, 'publicidad-portada-top-left', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(128, 2, 'Publicidad Portada top right', '', 'publicidad, portada, top, right', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:14:50', '2011-09-28 23:14:50', 0, 7, 7, 7, 44, 2, 0, 0, 0, 100, 'publicidad-portada-top-right', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(129, 2, 'Publicidad Portada intersticial', '', 'publicidad, portada, intersticial', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:15:49', '2011-09-28 23:15:49', 0, 7, 7, 7, 39, 2, 0, 0, 0, 100, 'publicidad-portada-intersticial', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(130, 2, 'Publicidad Portada botton left', '', 'publicidad, portada, botton, left', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:16:43', '2011-09-28 23:16:43', 0, 7, 7, 7, 45, 2, 0, 0, 0, 100, 'publicidad-portada-botton-left', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(131, 2, 'Publicidad Portada botton right', '', 'publicidad, portada, botton, right', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:17:47', '2011-09-28 23:17:47', 0, 7, 7, 7, 45, 2, 0, 0, 0, 100, 'publicidad-portada-botton-right', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(132, 2, 'Publicidad Portada columna1', '', 'publicidad, portada, columna1', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:19:00', '2011-09-28 23:19:00', 0, 7, 7, 7, 45, 2, 0, 0, 0, 100, 'publicidad-portada-columna1', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(133, 2, 'Publicidad Portada middle left', '', 'publicidad, portada, middle, left, botton', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:19:44', '2011-09-28 23:19:44', 0, 7, 7, 7, 45, 2, 0, 0, 0, 100, 'publicidad-portada-middle-left', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(134, 2, 'Publicidad Portada middle right', '', 'publicidad, portada, middle, right', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:20:24', '2011-09-28 23:20:24', 0, 7, 7, 7, 10, 2, 0, 0, 0, 100, 'publicidad-portada-middle-right', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(135, 2, 'Publicidad Portada middle right', '', 'publicidad, portada, middle, right', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:20:48', '2011-09-28 23:20:48', 0, 7, 7, 7, 45, 2, 0, 0, 0, 100, 'publicidad-portada-middle-right', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(136, 2, 'Publicidad Portada columna3', '', 'publicidad, portada, columna3', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:23:16', '2011-09-28 23:23:16', 0, 7, 7, 7, 45, 2, 0, 0, 0, 100, 'publicidad-portada-columna3', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(137, 2, 'Publicidad inner top left', '', 'publicidad, inner, top, left', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:24:19', '2011-09-28 23:24:19', 0, 7, 7, 7, 10, 2, 0, 0, 0, 100, 'publicidad-inner-top-left', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(138, 2, 'Publicidad inner top right', '', 'publicidad, inner, top, right', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:25:19', '2011-09-28 23:25:19', 0, 7, 7, 7, 11, 2, 0, 0, 0, 100, 'publicidad-inner-top-right', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(139, 2, 'Publicidad inner robapage', '', 'publicidad, inner, robapage', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:26:10', '2011-09-28 23:26:10', 0, 7, 7, 7, 10, 2, 0, 0, 0, 100, 'publicidad-inner-robapage', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(140, 2, 'Publicidad inner columna', '', 'publicidad, inner, columna', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:27:20', '2011-09-28 23:27:20', 0, 7, 7, 7, 10, 2, 0, 0, 0, 100, 'publicidad-inner-columna', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(141, 2, 'Publicidad opinion top left', '', 'publicidad, opinion, top, left', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:28:18', '2011-09-28 23:28:18', 0, 7, 7, 7, 6, 2, 0, 0, 0, 100, 'publicidad-opinion-top-left', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'opinion', NULL),
(142, 2, 'Publicidad opinion top right', '', 'publicidad, opinion, top, right', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:28:56', '2011-09-28 23:28:56', 0, 7, 7, 7, 3, 2, 0, 0, 0, 100, 'publicidad-opinion-top-right', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'opinion', NULL),
(143, 2, 'Publicidad opinion column', '', 'publicidad, opinion, column, top, left', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:29:47', '2011-09-28 23:29:47', 0, 7, 7, 7, 4, 2, 0, 0, 0, 100, 'publicidad-opinion-column', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'opinion', NULL),
(144, 2, 'Publicidad opinion column3', '', 'publicidad, opinion, column3', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:30:45', '2011-09-28 23:30:45', 0, 7, 7, 7, 6, 2, 0, 0, 0, 100, 'publicidad-opinion-column3', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'opinion', NULL),
(145, 2, 'Publicidad opinion bottom left', '', 'publicidad, opinion, bottom, left', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:31:31', '2011-09-28 23:31:31', 0, 7, 7, 7, 6, 2, 0, 0, 0, 100, 'publicidad-opinion-bottom-left', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'opinion', NULL),
(146, 2, 'Publicidad opinion bottom right', '', 'publicidad, opinion, bottom, right', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:32:19', '2011-09-28 23:32:19', 0, 7, 7, 7, 6, 2, 0, 0, 0, 100, 'publicidad-opinion-bottom-right', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'opinion', NULL),
(147, 2, 'Publicidad opinion inner top right', '', 'publicidad, opinion, inner, top, right', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:33:12', '2011-09-28 23:33:12', 0, 7, 7, 7, 4, 2, 0, 0, 0, 100, 'publicidad-opinion-inner-top-right', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'opinion', NULL),
(148, 2, 'Publicidad opinion inner top left', '', 'publicidad, opinion, inner, top, left', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:33:49', '2011-09-28 23:33:49', 0, 7, 7, 7, 4, 2, 0, 0, 0, 100, 'publicidad-opinion-inner-top-left', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'opinion', NULL),
(149, 2, 'Publicidad opinion inner columna', '', 'publicidad, opinion, inner, columna', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:34:30', '2011-09-28 23:34:30', 0, 7, 7, 7, 6, 2, 0, 0, 0, 100, 'publicidad-opinion-inner-columna', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'opinion', NULL),
(150, 2, 'publi video top left', '', 'publi, video, top, left', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:35:30', '2011-09-28 23:35:30', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'publi-video-top-left', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(151, 2, 'publi video top right', '', 'publi, video, top, right', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:36:06', '2011-09-28 23:36:06', 0, 7, 7, 7, 9, 2, 0, 0, 0, 100, 'publi-video-top-right', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(152, 2, 'publi gallery column', '', 'publi, gallery, column', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:36:59', '2011-09-28 23:36:59', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'publi-gallery-column', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(153, 2, 'publi gallery top left', '', 'publi, gallery, top, left', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:37:49', '2011-09-28 23:37:49', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'publi-gallery-top-left', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(154, 2, 'publi gallery top right', '', 'publi, gallery, top, right', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:38:48', '2011-09-28 23:38:48', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'publi-gallery-top-right', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(155, 2, 'publi gallery inner top', '', 'publi, gallery, inner, top', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:39:21', '2011-09-28 23:39:21', 0, 7, 7, 7, 2, 2, 0, 0, 0, 100, 'publi-gallery-inner-top', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(156, 2, 'Publicidad inner bottom left', '', 'publicidad, inner, bottom, left', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:43:15', '2011-09-28 23:43:15', 0, 7, 7, 7, 7, 2, 0, 0, 0, 100, 'publicidad-inner-bottom-left', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(157, 2, 'Publicidad Portada top left', '', 'publicidad, portada, top, left', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:44:24', '2011-09-28 23:44:24', 0, 7, 7, 7, 19, 2, 0, 0, 0, 100, 'publicidad-portada-top-left', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(158, 2, 'publi gallery inner top right', '', 'publi, gallery, inner, top, right', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:46:06', '2011-09-28 23:46:06', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'publi-gallery-inner-top-right', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(159, 2, 'Publicidad opinion inner column', '', 'publicidad, opinion, inner, column', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:48:57', '2011-09-28 23:48:57', 0, 7, 7, 7, 3, 2, 0, 0, 0, 100, 'publicidad-opinion-inner-column', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'opinion', NULL),
(160, 2, 'publi video column', '', 'publi, video, column', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:50:26', '2011-09-28 23:50:26', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'publi-video-column', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(161, 2, 'publi video inner top left', '', 'publi, video, inner, top, left', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:51:28', '2011-09-28 23:51:28', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'publi-video-inner-top-left', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(162, 2, 'publi video inner top right', '', 'publi, video, inner, top, right', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:52:17', '2011-09-28 23:52:17', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'publi-video-inner-top-right', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, '', NULL),
(163, 11, 'Curabitur lacinia mi a elit ullamcorper non lacinia nisl mollis', '', 'curabitur, lacinia, elit, ullamcorper, non, nisl, mollis', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:54:46', '2011-09-29 00:07:17', 1, 7, 0, 7, 7, 2, 0, 0, 0, 100, 'curabitur-lacinia-mi-a-elit-ullamcorper-non-lacinia-nisl-mollis', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(164, 11, 'Maecenas adipiscing tortor commodo enim accumsan volutpat', '', 'maecenas, adipiscing, tortor, commodo, enim, accumsan, volutpat, phasellus, pellentesque', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:57:02', '2011-09-28 23:57:02', 0, 7, 7, 7, 20, 2, 0, 0, 0, 100, 'maecenas-adipiscing-tortor-commodo-enim-accumsan-volutpat', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(165, 11, 'Nam sed dui sagittis eros faucibus congue', '', 'nam, sed, dui, sagittis, eros, faucibus, congue, no, tal, vez', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:58:11', '2011-09-28 23:58:11', 0, 7, 7, 7, 9, 2, 0, 0, 0, 100, 'nam-sed-dui-sagittis-eros-faucibus-congue', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'deportes', NULL),
(166, 11, 'Phasellus quis massa eros, id ullamcorper urna. ', '', 'phasellus, quis, massa, eros, id, ullamcorper, urna, fusce, tortor, caesar, totor', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-28 23:59:57', '2011-09-28 23:59:57', 0, 7, 7, 7, 25, 2, 0, 0, 0, 100, 'phasellus-quis-massa-eros-id-ullamcorper-urna', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL),
(167, 1, 'Suspendisse sollicitudin turpis sit amet nisl volutpat tincidunt', '', 'deportes, suspendisse, sollicitudin, turpis, sit, amet, nisl, volutpat, tincidunt', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:43:15', '2011-09-29 10:43:20', 1, 7, 0, 7, 1, 1, 1, 0, 1, 1, 'suspendisse-sollicitudin-turpis-sit-amet-nisl-volutpat-tincidunt', 1, 'placeholder_0_3', 'placeholder_1_1', NULL, 'deportes', NULL),
(168, 8, '_stock-photo-speed_rev100.jpg', 'luces naranjas', 'luces, naranjas', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:46:40', '2011-09-29 10:50:02', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'stock-photo-speedrev100-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(169, 8, 'stock-photo-long-exposure-of-a-funfair-ride-at-night_rev100.jpg', 'luces de colores', 'luces, colores', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:46:40', '2011-09-29 10:50:02', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'stock-photo-long-exposure-of-a-funfair-ride-at-nightrev100-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(170, 8, 'stock-photo-modern-urban-landscape-at-night_rev100.jpg', 'luz en la ciudad', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:46:40', '2011-09-29 10:50:02', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'stock-photo-modern-urban-landscape-at-nightrev100-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(171, 8, '2010052523330582501.jpg', 'pin-up art', 'pin, up, art', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:48:18', '2011-09-29 10:50:02', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052523330582501-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(172, 8, '2010052523330591659.jpg', 'pin-up art', 'pin, up, art', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:48:19', '2011-09-29 10:50:02', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052523330591659-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(173, 8, '2010052523330611552.jpg', 'pin-up art', 'pin, up, art', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:48:19', '2011-09-29 10:50:02', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052523330611552-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(174, 8, '2010052523330622662.jpg', 'pin-up art', 'pin, up, art', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:48:19', '2011-09-29 10:50:02', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052523330622662-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(175, 8, '2010052523341367151.jpg', 'pin-up art', 'pin, up, art', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:48:19', '2011-09-29 10:50:02', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052523341367151-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(176, 8, '2010052523341385802.jpg', 'pin-up art', 'pin, up, art', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:48:19', '2011-09-29 10:50:02', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052523341385802-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(177, 8, '2010052523341421820.jpg', 'pin-up art', 'pin, up, art', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:48:19', '2011-09-29 10:50:02', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052523341421820-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(178, 8, '2010052523341496489.jpg', 'pin-up art', 'pin, up, art', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:48:19', '2011-09-29 10:50:02', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052523341496489-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(179, 8, '2010052523354098074.jpg', 'pin-up art', 'pin, up, art', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:48:20', '2011-09-29 10:50:02', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052523354098074-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(180, 8, '2010052523354121641.jpg', 'pin-up art', 'pin, up, art', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:48:20', '2011-09-29 10:50:02', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, '2010052523354121641-jpg', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'cultura', NULL),
(181, 7, 'pin-up girls', 'Sed id orci eu tortor accumsan lobortis. Curabitur pretium turpis vitae tellus vestibulum vel tempor nunc sollicitudin. Pellentesque ac lacus a diam mattis posuere quis quis odio. Phasellus convallis purus at ligula auctor eget ultricies justo ultricies. ', 'pin, up, girls', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:56:37', '2011-09-29 10:56:37', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'pin-up-girls', 1, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', 1),
(182, 7, 'pin-up girls', 'Sed id orci eu tortor accumsan lobortis. Curabitur pretium turpis vitae tellus vestibulum vel tempor nunc sollicitudin. Pellentesque ac lacus a diam mattis posuere quis quis odio. Phasellus convallis purus at ligula auctor eget ultricies justo ultricies. ', 'pin, up, girls', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-09-29 10:56:38', '2011-09-29 10:57:37', 0, 7, 7, 7, 1, 2, 0, 1, 0, 100, 'pin-up-girls', 0, 'placeholder_0_1', 'placeholder_0_1', NULL, 'sociedad', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contents_categories`
--

CREATE TABLE IF NOT EXISTS `contents_categories` (
  `pk_fk_content` bigint(20) unsigned NOT NULL,
  `pk_fk_content_category` int(10) unsigned NOT NULL,
  `catName` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_fk_content`,`pk_fk_content_category`),
  KEY `pk_fk_content_category` (`pk_fk_content_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `contents_categories`
--

INSERT INTO `contents_categories` (`pk_fk_content`, `pk_fk_content_category`, `catName`) VALUES
(1, 22, 'deportes'),
(2, 22, 'deportes'),
(3, 22, 'deportes'),
(4, 22, 'deportes'),
(5, 22, 'deportes'),
(6, 22, 'deportes'),
(7, 22, 'deportes'),
(8, 22, 'deportes'),
(9, 22, 'deportes'),
(10, 23, 'economia'),
(11, 23, 'economia'),
(12, 23, 'economia'),
(13, 23, 'economia'),
(14, 23, 'economia'),
(15, 23, 'economia'),
(16, 23, 'economia'),
(17, 23, 'economia'),
(18, 31, 'fotos-de-hoy'),
(19, 31, 'fotos-de-hoy'),
(20, 31, 'fotos-de-hoy'),
(21, 31, 'fotos-de-hoy'),
(22, 31, 'fotos-de-hoy'),
(23, 31, 'fotos-de-hoy'),
(24, 31, 'fotos-de-hoy'),
(25, 31, 'fotos-de-hoy'),
(26, 31, 'fotos-de-hoy'),
(27, 31, 'fotos-de-hoy'),
(28, 31, 'fotos-de-hoy'),
(29, 31, 'fotos-de-hoy'),
(30, 31, 'fotos-de-hoy'),
(31, 31, 'fotos-de-hoy'),
(32, 22, 'deportes'),
(33, 22, 'deportes'),
(34, 22, 'deportes'),
(35, 22, 'deportes'),
(36, 22, 'deportes'),
(37, 22, 'deportes'),
(38, 22, 'deportes'),
(39, 22, 'deportes'),
(40, 26, 'sociedad'),
(41, 26, 'sociedad'),
(42, 26, 'sociedad'),
(43, 26, 'sociedad'),
(44, 26, 'sociedad'),
(45, 22, 'deportes'),
(46, 26, 'sociedad'),
(47, 23, 'economia'),
(48, 26, 'sociedad'),
(49, 23, 'economia'),
(68, 26, 'sociedad'),
(51, 25, 'cultura'),
(52, 26, 'sociedad'),
(53, 23, 'economia'),
(62, 22, 'deportes'),
(63, 25, 'cultura'),
(64, 22, 'deportes'),
(65, 25, 'cultura'),
(66, 25, 'cultura'),
(67, 24, 'politica'),
(54, 0, ''),
(55, 0, ''),
(56, 0, ''),
(57, 0, ''),
(58, 0, ''),
(59, 0, ''),
(60, 0, ''),
(61, 26, 'sociedad'),
(69, 22, 'deportes'),
(70, 22, 'deportes'),
(71, 25, 'cultura'),
(72, 24, 'politica'),
(73, 22, 'deportes'),
(74, 25, 'cultura'),
(75, 24, 'politica'),
(76, 26, 'sociedad'),
(77, 22, 'deportes'),
(78, 22, 'deportes'),
(79, 25, 'cultura'),
(80, 24, 'politica'),
(81, 22, 'deportes'),
(82, 26, 'sociedad'),
(83, 21, 'internacional'),
(84, 26, 'sociedad'),
(85, 24, 'politica'),
(86, 24, 'politica'),
(87, 24, 'politica'),
(88, 26, 'sociedad'),
(89, 22, 'deportes'),
(90, 22, 'deportes'),
(91, 26, 'sociedad'),
(92, 26, 'sociedad'),
(93, 26, 'sociedad'),
(94, 31, 'fotos-de-hoy'),
(95, 30, 'curiosidades'),
(96, 27, 'musica'),
(97, 26, 'sociedad'),
(98, 26, 'sociedad'),
(99, 27, 'musica'),
(100, 26, 'sociedad'),
(101, 26, 'sociedad'),
(102, 26, 'sociedad'),
(103, 22, 'deportes'),
(104, 22, 'deportes'),
(105, 30, 'curiosidades'),
(106, 30, 'curiosidades'),
(107, 22, 'deportes'),
(108, 30, 'curiosidades'),
(109, 26, 'sociedad'),
(110, 26, 'sociedad'),
(111, 2, 'publicidad'),
(112, 2, 'publicidad'),
(113, 2, 'publicidad'),
(114, 2, 'publicidad'),
(115, 2, 'publicidad'),
(116, 2, 'publicidad'),
(117, 2, 'publicidad'),
(118, 2, 'publicidad'),
(119, 2, 'publicidad'),
(120, 2, 'publicidad'),
(121, 2, 'publicidad'),
(122, 2, 'publicidad'),
(123, 2, 'publicidad'),
(124, 2, 'publicidad'),
(125, 2, 'publicidad'),
(126, 2, 'publicidad'),
(127, 0, ''),
(128, 0, ''),
(129, 0, ''),
(130, 0, ''),
(131, 0, ''),
(132, 0, ''),
(133, 0, ''),
(134, 0, ''),
(135, 0, ''),
(136, 0, ''),
(137, 0, ''),
(138, 0, ''),
(139, 0, ''),
(140, 0, ''),
(141, 4, 'opinion'),
(142, 4, 'opinion'),
(143, 4, 'opinion'),
(144, 4, 'opinion'),
(145, 4, 'opinion'),
(146, 4, 'opinion'),
(147, 4, 'opinion'),
(148, 4, 'opinion'),
(149, 4, 'opinion'),
(150, 0, ''),
(151, 0, ''),
(152, 0, ''),
(153, 0, ''),
(154, 0, ''),
(155, 0, ''),
(156, 0, ''),
(157, 0, ''),
(158, 0, ''),
(159, 4, 'opinion'),
(160, 0, ''),
(161, 0, ''),
(162, 0, ''),
(163, 26, 'sociedad'),
(164, 25, 'cultura'),
(165, 22, 'deportes'),
(166, 26, 'sociedad'),
(167, 22, 'deportes'),
(168, 25, 'cultura'),
(169, 25, 'cultura'),
(170, 25, 'cultura'),
(171, 25, 'cultura'),
(172, 25, 'cultura'),
(173, 25, 'cultura'),
(174, 25, 'cultura'),
(175, 25, 'cultura'),
(176, 25, 'cultura'),
(177, 25, 'cultura'),
(178, 25, 'cultura'),
(179, 25, 'cultura'),
(180, 25, 'cultura'),
(181, 26, 'sociedad'),
(182, 26, 'sociedad');

-- --------------------------------------------------------

--
-- Table structure for table `content_categories`
--

CREATE TABLE IF NOT EXISTS `content_categories` (
  `pk_content_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `inmenu` int(10) DEFAULT '0',
  `posmenu` int(10) DEFAULT '10',
  `internal_category` smallint(1) NOT NULL DEFAULT '0' COMMENT 'equal content_type & global=0 ',
  `fk_content_category` int(10) DEFAULT '0',
  `logo_path` varchar(200) DEFAULT NULL,
  `color` varchar(10) DEFAULT '#638F38',
  PRIMARY KEY (`pk_content_category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Dumping data for table `content_categories`
--

INSERT INTO `content_categories` (`pk_content_category`, `title`, `name`, `inmenu`, `posmenu`, `internal_category`, `fk_content_category`, `logo_path`, `color`) VALUES
(1, 'fotografias', 'photo', 0, 0, 0, 0, NULL, '#B0113A'),
(2, 'publicidad', 'publicidad', 0, 0, 0, 0, NULL, '#B0113A'),
(3, 'ALBUM', 'album', 0, 0, 0, 0, NULL, '#B0113A'),
(4, 'OPINIÃ“N', 'opinion', 0, 0, 0, 0, NULL, '#B0113A'),
(5, 'comentarios', 'comment', 0, 0, 0, 0, NULL, '#B0113A'),
(6, 'videos', 'video', 0, 0, 0, 0, NULL, '#B0113A'),
(7, 'author', 'author', 0, 0, 0, 0, NULL, '#B0113A'),
(8, 'PORTADA', 'portada', 0, 0, 0, 0, NULL, '#B0113A'),
(20, 'UNKNOWN', 'unknown', 0, 0, 0, 0, NULL, '#B0113A'),
(22, 'Deportes', 'deportes', 1, 10, 1, 0, '', ''),
(23, 'EconomÃ­a', 'economia', 1, 10, 1, 0, '', ''),
(24, 'PolÃ­tica', 'politica', 1, 10, 1, 0, '', ''),
(25, 'Cultura', 'cultura', 1, 10, 1, 0, '', ''),
(26, 'Sociedad', 'sociedad', 1, 10, 1, 0, '', ''),
(27, 'MÃºsica', 'musica', 1, 10, 1, 25, '', ''),
(28, 'Cine', 'cine', 1, 10, 1, 25, '', ''),
(29, 'TelevisiÃ³n', 'television', 1, 10, 1, 25, '', ''),
(30, 'Curiosidades', 'curiosidades', 1, 10, 9, 0, '', ''),
(31, 'Fotos de Hoy', 'fotos-de-hoy', 1, 10, 7, 0, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `content_positions`
--

CREATE TABLE IF NOT EXISTS `content_positions` (
  `pk_fk_content` bigint(20) NOT NULL,
  `fk_category` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `placeholder` varchar(45) NOT NULL DEFAULT '',
  `params` text,
  `content_type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`pk_fk_content`,`fk_category`,`position`,`placeholder`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `content_positions`
--

INSERT INTO `content_positions` (`pk_fk_content`, `fk_category`, `position`, `placeholder`, `params`, `content_type`) VALUES
(56, 26, 5, 'placeholder_1_3', NULL, 'Opinion'),
(59, 26, 4, 'placeholder_1_3', NULL, 'Opinion'),
(57, 26, 3, 'placeholder_1_3', NULL, 'Opinion'),
(54, 26, 2, 'placeholder_1_3', NULL, 'Opinion'),
(59, 25, 4, 'placeholder_1_1', NULL, 'Opinion'),
(57, 25, 3, 'placeholder_1_1', NULL, 'Opinion'),
(59, 23, 5, 'placeholder_1_1', NULL, 'Opinion'),
(54, 23, 4, 'placeholder_1_1', NULL, 'Opinion'),
(56, 23, 3, 'placeholder_1_1', NULL, 'Opinion'),
(60, 23, 2, 'placeholder_1_1', NULL, 'Opinion'),
(55, 23, 1, 'placeholder_1_1', NULL, 'Opinion'),
(58, 25, 2, 'placeholder_1_1', NULL, 'Opinion'),
(60, 25, 1, 'placeholder_1_1', NULL, 'Opinion'),
(60, 26, 1, 'placeholder_1_3', NULL, 'Opinion'),
(58, 24, 3, 'placeholder_2_4', NULL, 'Opinion'),
(56, 24, 2, 'placeholder_2_4', NULL, 'Opinion'),
(54, 24, 1, 'placeholder_2_4', NULL, 'Opinion'),
(59, 24, 2, 'placeholder_2_3', NULL, 'Opinion'),
(60, 24, 1, 'placeholder_2_3', NULL, 'Opinion'),
(58, 22, 3, 'placeholder_1_1', NULL, 'Opinion'),
(59, 22, 2, 'placeholder_1_1', NULL, 'Opinion'),
(60, 22, 1, 'placeholder_1_1', NULL, 'Opinion'),
(58, 26, 6, 'placeholder_1_3', NULL, 'Opinion'),
(58, 0, 3, 'placeholder_1_4', NULL, 'Opinion'),
(60, 0, 2, 'placeholder_1_4', NULL, 'Opinion'),
(59, 0, 1, 'placeholder_1_4', NULL, 'Opinion'),
(54, 0, 3, 'placeholder_1_3', NULL, 'Opinion'),
(55, 0, 2, 'placeholder_1_3', NULL, 'Opinion'),
(56, 0, 1, 'placeholder_1_3', NULL, 'Opinion');

-- --------------------------------------------------------

--
-- Table structure for table `content_types`
--

CREATE TABLE IF NOT EXISTS `content_types` (
  `pk_content_type` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `title` varchar(100) NOT NULL COMMENT 'utilizado en permalink',
  `fk_template_default` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`pk_content_type`),
  KEY `fk_template_default` (`fk_template_default`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `content_types`
--

INSERT INTO `content_types` (`pk_content_type`, `name`, `title`, `fk_template_default`) VALUES
(1, 'article', 'Artículo', NULL),
(2, 'advertisement', 'Publicidad', NULL),
(3, 'attachment', 'Fichero', NULL),
(4, 'opinion', 'Opinión', NULL),
(15, 'book', 'Libro', NULL),
(6, 'comment', 'Comentario', NULL),
(7, 'album', 'Álbum', NULL),
(8, 'photo', 'Imagen', NULL),
(9, 'video', 'Vídeo', NULL),
(10, 'specials', 'Especiales', NULL),
(11, 'poll', 'Encuesta', NULL),
(13, 'static_page', 'Página estática', NULL),
(14, 'kiosko', 'Kiosko', NULL),
(12, 'widget', 'Widget', NULL),
(5, 'event', 'Evento', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kioskos`
--

CREATE TABLE IF NOT EXISTS `kioskos` (
  `pk_kiosko` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `path` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `favorite` tinyint(1) NOT NULL,
  PRIMARY KEY (`pk_kiosko`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `kioskos`
--


-- --------------------------------------------------------

--
-- Table structure for table `menues`
--

CREATE TABLE IF NOT EXISTS `menues` (
  `pk_menu` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `site` text NOT NULL,
  `params` varchar(255) DEFAULT NULL,
  `pk_father` int(11) DEFAULT NULL,
  PRIMARY KEY (`pk_menu`),
  UNIQUE KEY `name` (`name`),
  KEY `name_2` (`name`),
  KEY `pk_menu` (`pk_menu`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `menues`
--

INSERT INTO `menues` (`pk_menu`, `name`, `type`, `site`, `params`, `pk_father`) VALUES
(1, 'frontpage', '', 'idealgallego.local', 'a:1:{s:11:"description";s:0:"";}', 0),
(2, 'opinion', '', 'idealgallego.local', 'a:1:{s:11:"description";s:0:"";}', 0),
(3, 'mobile', '', '', 'a:1:{s:11:"description";s:0:"";}', 0),
(4, 'album', '', 'idealgallego.local', 'a:1:{s:11:"description";s:0:"";}', 0),
(5, 'video', '', 'idealgallego.local', 'a:1:{s:11:"description";s:0:"";}', 0),
(7, 'encuesta', '', 'idealgallego.local', 'a:1:{s:11:"description";s:0:"";}', 0),
(8, 'subHome', '', 'idealgallego.local', 'a:1:{s:11:"description";s:0:"";}', 21);

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE IF NOT EXISTS `menu_items` (
  `pk_item` int(11) NOT NULL AUTO_INCREMENT,
  `pk_menu` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `link_name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL COMMENT '''category'',''external'',''static'', internal''',
  `position` int(11) NOT NULL,
  `pk_father` int(11) DEFAULT NULL,
  PRIMARY KEY (`pk_item`),
  KEY `pk_item` (`pk_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=41 ;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`pk_item`, `pk_menu`, `title`, `link_name`, `type`, `position`, `pk_father`) VALUES
(1, 1, 'Sociedad', 'sociedad', 'category', 8, 0),
(22, 4, 'Fotos de Hoy', 'fotos-de-hoy', 'albumCategory', 1, 0),
(3, 1, 'Deportes', 'deportes', 'category', 2, 0),
(4, 1, 'Cultura', 'cultura', 'category', 3, 0),
(5, 1, 'EconomÃ­a', 'economia', 'category', 6, 0),
(6, 2, 'PolÃ­tica', 'politica', 'category', 1, 0),
(7, 2, 'Cultura', 'cultura', 'category', 2, 0),
(8, 2, 'Sociedad', 'sociedad', 'category', 3, 0),
(9, 2, 'EconomÃ­a', 'economia', 'category', 4, 0),
(10, 2, 'Deportes', 'deportes', 'category', 5, 0),
(11, 2, 'PolÃ­tica', 'politica', 'category', 1, 0),
(12, 2, 'Cultura', 'cultura', 'category', 2, 0),
(13, 2, 'Sociedad', 'sociedad', 'category', 3, 0),
(14, 2, 'EconomÃ­a', 'economia', 'category', 4, 0),
(15, 2, 'Deportes', 'deportes', 'category', 5, 0),
(19, 1, 'PolÃ­tica', 'politica', 'category', 4, 0),
(21, 1, 'Portada', 'home', 'internal', 1, 0),
(23, 4, 'Sociedad', 'sociedad', 'category', 2, 0),
(24, 4, 'Deportes', 'deportes', 'category', 3, 0),
(37, 8, 'opinion', 'opinion', 'internal', 3, 21),
(26, 8, 'album', 'album', 'internal', 1, 21),
(27, 8, 'video', 'video', 'internal', 2, 21),
(28, 8, 'mobile', 'mobile', 'internal', 4, 21),
(29, 8, 'encuesta', 'encuesta', 'internal', 5, 21),
(31, 5, 'Curiosidades', 'curiosidades', 'videoCategory', 2, 0),
(32, 5, 'Deportes', 'deportes', 'category', 3, 0),
(33, 5, 'Sociedad', 'sociedad', 'category', 4, 0),
(34, 5, 'MÃºsica', 'musica', 'category', 1, 0),
(38, 7, 'Sociedad', 'sociedad', 'category', 1, 0),
(39, 7, 'Deportes', 'deportes', 'category', 2, 0),
(40, 7, 'Cultura', 'cultura', 'category', 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_archive`
--

CREATE TABLE IF NOT EXISTS `newsletter_archive` (
  `pk_newsletter` int(11) NOT NULL AUTO_INCREMENT,
  `data` longtext,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pk_newsletter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `newsletter_archive`
--


-- --------------------------------------------------------

--
-- Table structure for table `opinions`
--

CREATE TABLE IF NOT EXISTS `opinions` (
  `pk_opinion` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_content_categories` int(10) unsigned DEFAULT '7',
  `fk_author` int(10) DEFAULT NULL,
  `body` text,
  `fk_author_img` int(10) DEFAULT NULL,
  `with_comment` smallint(1) DEFAULT '1',
  `type_opinion` varchar(150) DEFAULT NULL,
  `fk_author_img_widget` int(10) DEFAULT NULL,
  PRIMARY KEY (`pk_opinion`),
  KEY `type_opinion` (`type_opinion`),
  KEY `fk_author` (`fk_author`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=61 ;

--
-- Dumping data for table `opinions`
--

INSERT INTO `opinions` (`pk_opinion`, `fk_content_categories`, `fk_author`, `body`, `fk_author_img`, `with_comment`, `type_opinion`, `fk_author_img_widget`) VALUES
(54, 7, 0, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<h4>"Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit</h4>', NULL, 1, '1', NULL),
(55, 7, 0, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<h4>"Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit</h4>', NULL, 1, '1', NULL),
(56, 7, 0, '<div id="lipsum">\r\n<p>&nbsp;Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>\r\n<p>Cras neque nulla, dapibus id tempor et, scelerisque ut arcu. Cras  convallis justo vitae quam tempor euismod. In blandit placerat congue.  Suspendisse tempor mi et nisi faucibus congue. Curabitur in lobortis  quam. Nam blandit arcu vel metus sollicitudin adipiscing. Donec nisl  quam, tristique sit amet pharetra vitae, congue eu magna. Nulla a  lacinia turpis. Quisque viverra, purus sit amet auctor convallis, velit  ligula tempus augue, ac porta nisi risus non urna. Nulla vestibulum quam  sit amet lorem auctor ac gravida quam placerat. Morbi nec libero  tortor. Vivamus vel erat a erat ultricies pharetra.</p>\r\n</div>', NULL, 1, '1', NULL),
(57, 7, 3, '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>', NULL, 1, '0', NULL),
(58, 7, 3, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>\r\n<p>Cras neque nulla, dapibus id tempor et, scelerisque ut arcu. Cras  convallis justo vitae quam tempor euismod. In blandit placerat congue.  Suspendisse tempor mi et nisi faucibus congue. Curabitur in lobortis  quam. Nam blandit arcu vel metus sollicitudin adipiscing. Donec nisl  quam, tristique sit amet pharetra vitae, congue eu magna. Nulla a  lacinia turpis. Quisque viverra, purus sit amet auctor convallis, velit  ligula tempus augue, ac porta nisi risus non urna. Nulla vestibulum quam  sit amet lorem auctor ac gravida quam placerat. Morbi nec libero  tortor. Vivamus vel erat a erat ultricies pharetra.</p>\r\n</div>', NULL, 1, '0', NULL),
(59, 7, 3, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>\r\n<p>Cras neque nulla, dapibus id tempor et, scelerisque ut arcu. Cras  convallis justo vitae quam tempor euismod. In blandit placerat congue.  Suspendisse tempor mi et nisi faucibus congue. Curabitur in lobortis  quam. Nam blandit arcu vel metus sollicitudin adipiscing. Donec nisl  quam, tristique sit amet pharetra vitae, congue eu magna. Nulla a  lacinia turpis. Quisque viverra, purus sit amet auctor convallis, velit  ligula tempus augue, ac porta nisi risus non urna. Nulla vestibulum quam  sit amet lorem auctor ac gravida quam placerat. Morbi nec libero  tortor. Vivamus vel erat a erat ultricies pharetra.</p>\r\n</div>', NULL, 1, '0', NULL),
(60, 7, 3, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>\r\n<p>Cras neque nulla, dapibus id tempor et, scelerisque ut arcu. Cras  convallis justo vitae quam tempor euismod. In blandit placerat congue.  Suspendisse tempor mi et nisi faucibus congue. Curabitur in lobortis  quam. Nam blandit arcu vel metus sollicitudin adipiscing. Donec nisl  quam, tristique sit amet pharetra vitae, congue eu magna. Nulla a  lacinia turpis. Quisque viverra, purus sit amet auctor convallis, velit  ligula tempus augue, ac porta nisi risus non urna. Nulla vestibulum quam  sit amet lorem auctor ac gravida quam placerat. Morbi nec libero  tortor. Vivamus vel erat a erat ultricies pharetra.</p>\r\n</div>', NULL, 1, '0', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pclave`
--

CREATE TABLE IF NOT EXISTS `pclave` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `pclave` varchar(60) NOT NULL,
  `value` varchar(240) DEFAULT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'intsearch',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pclave`
--


-- --------------------------------------------------------

--
-- Table structure for table `pc_users`
--

CREATE TABLE IF NOT EXISTS `pc_users` (
  `pk_pc_user` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `subscription` int(11) NOT NULL,
  PRIMARY KEY (`pk_pc_user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pc_users`
--


-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE IF NOT EXISTS `photos` (
  `pk_photo` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `path_file` varchar(150) NOT NULL,
  `date` varchar(255) DEFAULT NULL,
  `size` float DEFAULT NULL,
  `resolution` varchar(100) DEFAULT NULL,
  `width` int(10) DEFAULT NULL,
  `height` int(10) DEFAULT NULL,
  `nameCat` varchar(250) DEFAULT '1',
  `type_img` varchar(5) DEFAULT NULL,
  `author_name` varchar(200) DEFAULT NULL,
  `media_type` enum('image','graphic') NOT NULL DEFAULT 'image' COMMENT 'imagen o grafico',
  `color` varchar(10) DEFAULT NULL COMMENT 'BN, color',
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_photo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=181 ;

--
-- Dumping data for table `photos`
--

INSERT INTO `photos` (`pk_photo`, `name`, `path_file`, `date`, `size`, `resolution`, `width`, `height`, `nameCat`, `type_img`, `author_name`, `media_type`, `color`, `address`) VALUES
(1, '2011092318382330129.jpg', '/2011/09/23/', '1316795903', 96.85, NULL, 970, 290, 'deportes', 'jpg', '', 'image', NULL, NULL),
(2, '2011092318382331408.jpg', '/2011/09/23/', '1316795903', 33.38, NULL, 330, 220, 'deportes', 'jpg', '', 'image', NULL, NULL),
(3, '2011092318382331939.jpg', '/2011/09/23/', '1316795903', 219.44, NULL, 1280, 800, 'deportes', 'jpg', '', 'image', NULL, NULL),
(4, '2011092318382332447.jpg', '/2011/09/23/', '1316795903', 137.45, NULL, 1280, 800, 'deportes', 'jpg', '', 'image', NULL, NULL),
(5, '2011092318382332932.jpg', '/2011/09/23/', '1316795903', 118.97, NULL, 1280, 800, 'deportes', 'jpg', '', 'image', NULL, NULL),
(6, '2011092318382333413.jpg', '/2011/09/23/', '1316795903', 112.11, NULL, 1024, 768, 'deportes', 'jpg', '', 'image', NULL, NULL),
(7, '2011092318382333983.jpg', '/2011/09/23/', '1316795903', 134.95, NULL, 1024, 683, 'deportes', 'jpg', '', 'image', NULL, NULL),
(8, '2011092318382334739.jpg', '/2011/09/23/', '1316795903', 107.59, NULL, 1024, 685, 'deportes', 'jpg', '', 'image', NULL, NULL),
(9, '2011092318382335269.jpg', '/2011/09/23/', '1316795903', 28.32, NULL, 300, 240, 'deportes', 'jpg', '', 'image', NULL, NULL),
(15, '2011092623423270713.jpg', '/2011/09/26/', '2011-09-26 23:42:32', 45.13, '', 654, 323, 'economia', 'jpg', '', 'image', 'Color', ''),
(16, '2011092623423279130.jpg', '/2011/09/26/', '2011-09-26 23:42:32', 206.35, '', 1200, 1024, 'economia', 'jpg', '', 'image', 'Color', ''),
(17, '2011092623423295793.jpg', '/2011/09/26/', '2011-09-26 23:42:32', 211.94, '', 1024, 768, 'economia', 'jpg', '', 'image', 'Color', ''),
(18, '2011092623440566243.jpg', '/2011/09/26/', '2011-09-26 23:44:05', 44.07, '', 640, 480, 'fotos-de-hoy', 'jpg', '', 'image', 'Color', ''),
(19, '2011092623440586277.jpg', '/2011/09/26/', '2011-09-26 23:44:05', 37.6, '', 560, 560, 'fotos-de-hoy', 'jpg', '', 'image', 'Color', ''),
(20, '2011092623440593500.jpg', '/2011/09/26/', '2011-09-26 23:44:05', 45.4, '', 597, 399, 'fotos-de-hoy', 'jpg', '', 'image', 'Color', ''),
(21, '2011092623440599480.jpg', '/2011/09/26/', '2011-09-26 23:44:05', 219.44, '', 1280, 800, 'fotos-de-hoy', 'jpg', '', 'image', 'Color', ''),
(22, '2011092623455095851.jpg', '/2011/09/26/', '2011-09-26 23:45:50', 176.37, '', 1920, 1200, 'fotos-de-hoy', 'jpg', '', 'image', 'Color', ''),
(23, '2011092623455119453.jpg', '/2011/09/26/', '2011-09-26 23:45:51', 135.96, '', 1920, 1200, 'fotos-de-hoy', 'jpg', '', 'image', 'Color', ''),
(24, '2011092623455140386.jpg', '/2011/09/26/', '2011-09-26 23:45:51', 261.95, '', 1920, 1200, 'fotos-de-hoy', 'jpg', '', 'image', 'Color', ''),
(25, '2011092623455162385.jpg', '/2011/09/26/', '2011-09-26 23:45:51', 191.55, '', 1920, 1200, 'fotos-de-hoy', 'jpg', '', 'image', 'Color', ''),
(26, '2011092623455184671.jpg', '/2011/09/26/', '2011-09-26 23:45:51', 76.36, '', 1024, 768, 'fotos-de-hoy', 'jpg', '', 'image', 'Color', ''),
(27, '2011092623455194628.jpg', '/2011/09/26/', '2011-09-26 23:45:51', 141.22, '', 1024, 768, 'fotos-de-hoy', 'jpg', '', 'image', 'Color', ''),
(28, '2011092623455246677.jpg', '/2011/09/26/', '2011-09-26 23:45:52', 101.43, '', 1024, 768, 'fotos-de-hoy', 'jpg', '', 'image', 'Color', ''),
(29, '2011092623455220326.jpg', '/2011/09/26/', '2011-09-26 23:45:52', 34.27, '', 1024, 768, 'fotos-de-hoy', 'jpg', '', 'image', 'Color', ''),
(30, '2011092623455229628.jpg', '/2011/09/26/', '2011-09-26 23:45:52', 55.13, '', 1024, 768, 'fotos-de-hoy', 'jpg', '', 'image', 'Color', ''),
(31, '2011092623455239081.jpg', '/2011/09/26/', '2011-09-26 23:45:52', 86.71, '', 1024, 768, 'fotos-de-hoy', 'jpg', '', 'image', 'Color', ''),
(32, '2011092623503366144.jpg', '/2011/09/26/', '2011-09-26 23:50:33', 137.37, '', 1440, 900, 'deportes', 'jpg', '', 'image', 'Color', ''),
(33, '2011092623503393155.jpg', '/2011/09/26/', '2011-09-26 23:50:33', 42.8, '', 990, 633, 'deportes', 'jpg', '', 'image', 'Color', ''),
(34, '2011092623503412663.jpg', '/2011/09/26/', '2011-09-26 23:50:34', 137.45, '', 1280, 800, 'deportes', 'jpg', '', 'image', 'Color', ''),
(35, '2011092623503419708.jpg', '/2011/09/26/', '2011-09-26 23:50:34', 219.44, '', 1280, 800, 'deportes', 'jpg', '', 'image', 'Color', ''),
(36, '2011092623503437565.jpg', '/2011/09/26/', '2011-09-26 23:50:34', 118.97, '', 1280, 800, 'deportes', 'jpg', '', 'image', 'Color', ''),
(37, '2011092623503454494.jpg', '/2011/09/26/', '2011-09-26 23:50:34', 112.11, '', 1024, 768, 'deportes', 'jpg', '', 'image', 'Color', ''),
(38, '2011092623503464732.jpg', '/2011/09/26/', '2011-09-26 23:50:34', 107.59, '', 1024, 685, 'deportes', 'jpg', '', 'image', 'Color', ''),
(39, '2011092623503474229.jpg', '/2011/09/26/', '2011-09-26 23:50:34', 49.83, '', 450, 300, 'deportes', 'jpg', '', 'image', 'Color', ''),
(40, '2011092623535613757.jpg', '/2011/09/26/', '2011-09-26 23:53:56', 22.01, '', 420, 295, 'sociedad', 'jpg', '', 'image', 'Color', ''),
(41, '2011092623535618935.jpg', '/2011/09/26/', '2011-09-26 23:53:56', 47.35, '', 654, 323, 'sociedad', 'jpg', '', 'image', 'Color', ''),
(42, '2011092623535626059.jpg', '/2011/09/26/', '2011-09-26 23:53:56', 23.94, '', 654, 323, 'sociedad', 'jpg', '', 'image', 'Color', ''),
(43, '2011092623535632579.jpg', '/2011/09/26/', '2011-09-26 23:53:56', 43.99, '', 654, 323, 'sociedad', 'jpg', '', 'image', 'Color', ''),
(44, '2011092623535639323.jpg', '/2011/09/26/', '2011-09-26 23:53:56', 49.83, '', 450, 300, 'sociedad', 'jpg', '', 'image', 'Color', ''),
(111, '2011092822582794033.swf', '/2011/09/28/', '2011-09-28 22:58:27', 33.39, '', 300, 300, 'publicidad', 'swf', '', 'image', 'Color', ''),
(112, '2011092822582830993.gif', '/2011/09/28/', '2011-09-28 22:58:28', 9.75, '', 120, 90, 'publicidad', 'gif', '', 'image', 'Color', ''),
(113, '2011092822582856321.gif', '/2011/09/28/', '2011-09-28 22:58:28', 31.44, '', 234, 90, 'publicidad', 'gif', '', 'image', 'Color', ''),
(114, '2011092822582889662.jpg', '/2011/09/28/', '2011-09-28 22:58:28', 20.63, '', 234, 90, 'publicidad', 'jpg', '', 'image', 'Color', ''),
(115, '2011092822582896422.jpg', '/2011/09/28/', '2011-09-28 22:58:28', 24.88, '', 728, 90, 'publicidad', 'jpg', '', 'image', 'Color', ''),
(116, '2011092822582899076.jpg', '/2011/09/28/', '2011-09-28 22:58:28', 45.27, '', 300, 250, 'publicidad', 'jpg', '', 'image', 'Color', ''),
(117, '2011092822582942101.jpg', '/2011/09/28/', '2011-09-28 22:58:29', 14.61, '', 234, 90, 'publicidad', 'jpg', '', 'image', 'Color', ''),
(118, '2011092823065889359.png', '/2011/09/28/', '2011-09-28 23:06:58', 5.43, '', 284, 139, 'publicidad', 'png', '', 'image', 'Color', ''),
(119, '2011092823065820527.jpg', '/2011/09/28/', '2011-09-28 23:06:58', 48.16, '', 300, 300, 'publicidad', 'jpg', '', 'image', 'Color', ''),
(120, '2011092823065825091.jpg', '/2011/09/28/', '2011-09-28 23:06:58', 39.35, '', 400, 400, 'publicidad', 'jpg', '', 'image', 'Color', ''),
(121, '2011092823065829582.jpg', '/2011/09/28/', '2011-09-28 23:06:58', 44.77, '', 800, 600, 'publicidad', 'jpg', '', 'image', 'Color', ''),
(122, '2011092823065836215.jpg', '/2011/09/28/', '2011-09-28 23:06:58', 49.91, '', 160, 600, 'publicidad', 'jpg', '', 'image', 'Color', ''),
(123, '2011092823065838659.jpg', '/2011/09/28/', '2011-09-28 23:06:58', 29.62, '', 728, 90, 'publicidad', 'jpg', '', 'image', 'Color', ''),
(124, '2011092823065841147.jpg', '/2011/09/28/', '2011-09-28 23:06:58', 51.13, '', 800, 600, 'publicidad', 'jpg', '', 'image', 'Color', ''),
(125, '2011092823065847911.jpg', '/2011/09/28/', '2011-09-28 23:06:58', 13.3, '', 300, 60, 'publicidad', 'jpg', '', 'image', 'Color', ''),
(126, '2011092823065851033.jpg', '/2011/09/28/', '2011-09-28 23:06:58', 14.61, '', 234, 90, 'publicidad', 'jpg', '', 'image', 'Color', ''),
(168, '2011092910464053185.jpg', '/2011/09/29/', '2011-09-29 10:46:40', 28.03, '', 652, 186, 'cultura', 'jpg', '', 'image', 'Color', ''),
(169, '2011092910464066501.jpg', '/2011/09/29/', '2011-09-29 10:46:40', 39.72, '', 654, 130, 'cultura', 'jpg', '', 'image', 'Color', ''),
(170, '2011092910464070031.jpg', '/2011/09/29/', '2011-09-29 10:46:40', 74.79, '', 654, 323, 'cultura', 'jpg', '', 'image', 'Color', ''),
(171, '2011092910481893666.jpg', '/2011/09/29/', '2011-09-29 10:48:18', 163.2, '', 1291, 904, 'cultura', 'jpg', '', 'image', 'Color', ''),
(172, '2011092910481911030.jpg', '/2011/09/29/', '2011-09-29 10:48:19', 86.41, '', 1000, 956, 'cultura', 'jpg', '', 'image', 'Color', ''),
(173, '2011092910481925098.jpg', '/2011/09/29/', '2011-09-29 10:48:19', 75.03, '', 1200, 833, 'cultura', 'jpg', '', 'image', 'Color', ''),
(174, '2011092910481942502.jpg', '/2011/09/29/', '2011-09-29 10:48:19', 108.88, '', 1576, 1050, 'cultura', 'jpg', '', 'image', 'Color', ''),
(175, '2011092910481961045.jpg', '/2011/09/29/', '2011-09-29 10:48:19', 238.26, '', 850, 1200, 'cultura', 'jpg', '', 'image', 'Color', ''),
(176, '2011092910481972297.jpg', '/2011/09/29/', '2011-09-29 10:48:19', 100.38, '', 800, 1145, 'cultura', 'jpg', '', 'image', 'Color', ''),
(177, '2011092910481983500.jpg', '/2011/09/29/', '2011-09-29 10:48:19', 97.94, '', 800, 1193, 'cultura', 'jpg', '', 'image', 'Color', ''),
(178, '2011092910481993878.jpg', '/2011/09/29/', '2011-09-29 10:48:19', 91.22, '', 800, 1205, 'cultura', 'jpg', '', 'image', 'Color', ''),
(179, '2011092910482043660.jpg', '/2011/09/29/', '2011-09-29 10:48:20', 87.28, '', 800, 1212, 'cultura', 'jpg', '', 'image', 'Color', ''),
(180, '2011092910482014796.jpg', '/2011/09/29/', '2011-09-29 10:48:20', 57.67, '', 800, 1091, 'cultura', 'jpg', '', 'image', 'Color', '');

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE IF NOT EXISTS `polls` (
  `pk_poll` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `total_votes` int(11) NOT NULL DEFAULT '0',
  `used_ips` longtext,
  `subtitle` varchar(150) DEFAULT NULL,
  `visualization` smallint(1) DEFAULT '0',
  `with_comment` smallint(1) DEFAULT '1',
  PRIMARY KEY (`pk_poll`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=167 ;

--
-- Dumping data for table `polls`
--

INSERT INTO `polls` (`pk_poll`, `total_votes`, `used_ips`, `subtitle`, `visualization`, `with_comment`) VALUES
(163, 4, 'a:1:{i:0;a:2:{s:2:"ip";s:9:"127.0.1.1";s:5:"count";i:4;}}', 'Suspendisse nisl ', 0, 1),
(164, 16, 'a:1:{i:0;a:2:{s:2:"ip";s:9:"127.0.1.1";s:5:"count";i:16;}}', 'Phasellus pellentesque ', 1, 1),
(165, 7, 'a:1:{i:0;a:2:{s:2:"ip";s:9:"127.0.1.1";s:5:"count";i:7;}}', 'faucibus congue', 0, 1),
(166, 21, 'a:1:{i:0;a:2:{s:2:"ip";s:9:"127.0.1.1";s:5:"count";i:21;}}', 'Fusce a tortor tortor.', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `poll_items`
--

CREATE TABLE IF NOT EXISTS `poll_items` (
  `pk_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_pk_poll` bigint(20) unsigned NOT NULL,
  `item` varchar(255) NOT NULL,
  `metadata` varchar(250) DEFAULT NULL,
  `votes` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`pk_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `poll_items`
--

INSERT INTO `poll_items` (`pk_item`, `fk_pk_poll`, `item`, `metadata`, `votes`) VALUES
(23, 163, 'ullamcorper', NULL, 1),
(22, 163, 'lacinia', NULL, 2),
(21, 163, 'curabitur', NULL, 0),
(7, 164, 'Phasellus', 'phasellus', 3),
(8, 164, ' pellentesque ', 'pellentesque', 4),
(9, 164, 'Maecenas', 'maecenas', 2),
(10, 164, 'tortor', 'tortor', 6),
(11, 164, 'commodo', 'commodo', 1),
(12, 165, 'Si', 'si', 3),
(13, 165, 'No', 'no', 3),
(14, 165, 'Tal Vez', 'tal, vez', 1),
(15, 166, 'phasellus', 'phasellus', 5),
(16, 166, 'massa', 'massa', 6),
(17, 166, 'eros', 'eros', 1),
(18, 166, 'caesar', 'caesar', 2),
(19, 166, 'ullamcorper', 'ullamcorper', 4),
(20, 166, 'totor', 'totor', 3);

-- --------------------------------------------------------

--
-- Table structure for table `privileges`
--

CREATE TABLE IF NOT EXISTS `privileges` (
  `pk_privilege` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `module` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`pk_privilege`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=135 ;

--
-- Dumping data for table `privileges`
--

INSERT INTO `privileges` (`pk_privilege`, `name`, `description`, `module`) VALUES
(1, 'CATEGORY_ADMIN', 'Listado de secciones', 'CATEGORY'),
(2, 'CATEGORY_AVAILABLE', 'Aprobar SecciÃ³n', 'CATEGORY'),
(3, 'CATEGORY_UPDATE', 'Modificar SecciÃ³n', 'CATEGORY'),
(4, 'CATEGORY_DELETE', 'Eliminar SecciÃ³n', 'CATEGORY'),
(5, 'CATEGORY_CREATE', 'Crear SecciÃ³n', 'CATEGORY'),
(6, 'ARTICLE_ADMIN', 'Listados de ArtÃ­culos', 'ARTICLE'),
(7, 'ARTICLE_FRONTPAGE', 'AdministraciÃ³n de portadas', 'ARTICLE'),
(8, 'ARTICLE_PENDINGS', 'Listar noticias pendientes', 'ARTICLE'),
(9, 'ARTICLE_AVAILABLE', 'Aprobar Noticia', 'ARTICLE'),
(10, 'ARTICLE_UPDATE', 'Modificar ArtÃ­culo', 'ARTICLE'),
(11, 'ARTICLE_DELETE', 'Eliminar ArtÃ­culo', 'ARTICLE'),
(12, 'ARTICLE_CREATE', 'Crear ArtÃ­culo', 'ARTICLE'),
(13, 'ARTICLE_ARCHIVE', 'Recuperar/Archivar ArtÃ­culos de/a hemeroteca', 'ARTICLE'),
(14, 'ARTICLE_CLONE', 'Clonar ArtÃ­culo', 'ARTICLE'),
(15, 'ARTICLE_HOME', 'GestiÃ³n portada Home de artÃ­culos', 'ARTICLE'),
(16, 'ARTICLE_TRASH', 'gestiÃ³n papelera ArtÃ­culo', 'ARTICLE'),
(17, 'ARTICLE_ARCHIVE_ADMI', 'Listado de hemeroteca', 'ARTICLE'),
(18, 'ADVERTISEMENT_ADMIN', 'Listado de  publicidad', 'ADVERTISEMENT'),
(19, 'ADVERTISEMENT_AVAILA', 'Aprobar publicidad', 'ADVERTISEMENT'),
(20, 'ADVERTISEMENT_UPDATE', 'Modificar publicidad', 'ADVERTISEMENT'),
(21, 'ADVERTISEMENT_DELETE', 'Eliminar publicidad', 'ADVERTISEMENT'),
(22, 'ADVERTISEMENT_CREATE', 'Crear publicidad', 'ADVERTISEMENT'),
(23, 'ADVERTISEMENT_TRASH', 'gestiÃ³n papelera publicidad', 'ADVERTISEMENT'),
(24, 'ADVERTISEMENT_HOME', 'gestiÃ³n de publicidad en Home', 'ADVERTISEMENT'),
(26, 'OPINION_ADMIN', 'Listado de  opiniÃ³n', 'OPINION'),
(27, 'OPINION_FRONTPAGE', 'Portada Opinion', 'OPINION'),
(28, 'OPINION_AVAILABLE', 'Aprobar OpiniÃ³n', 'OPINION'),
(29, 'OPINION_UPDATE', 'Modificar OpiniÃ³n', 'OPINION'),
(30, 'OPINION_HOME', 'Publicar widgets home OpiniÃ³n', 'OPINION'),
(31, 'OPINION_DELETE', 'Eliminar OpiniÃ³n', 'OPINION'),
(32, 'OPINION_CREATE', 'Crear OpiniÃ³n', 'OPINION'),
(33, 'OPINION_TRASH', 'gestion papelera OpiniÃ³n', 'OPINION'),
(34, 'COMMENT_ADMIN', 'Listado de comentarios', 'COMMENT'),
(35, 'COMMENT_POLL', 'Gestionar Comentarios de encuestas', 'COMMENT'),
(36, 'COMMENT_HOME', 'Gestionar Comentarios de Home', 'COMMENT'),
(37, 'COMMENT_AVAILABLE', 'Aprobar/Rechazar Comentario', 'COMMENT'),
(38, 'COMMENT_UPDATE', 'Modificar Comentario', 'COMMENT'),
(39, 'COMMENT_DELETE', 'Eliminar Comentario', 'COMMENT'),
(40, 'COMMENT_CREATE', 'Crear Comentario', 'COMMENT'),
(41, 'COMMENT_TRASH', 'gestiÃ³n papelera Comentarios', 'COMMENT'),
(42, 'ALBUM_ADMIN', 'Listado de Ã¡lbumes', 'ALBUM'),
(43, 'ALBUM_AVAILABLE', 'Aprobar Album', 'ALBUM'),
(44, 'ALBUM_UPDATE', 'Modificar Album', 'ALBUM'),
(45, 'ALBUM_DELETE', 'Eliminar Album', 'ALBUM'),
(46, 'ALBUM_CREATE', 'Crear Album', 'ALBUM'),
(47, 'ALBUM_TRASH', 'gestion papelera Album', 'ALBUM'),
(48, 'VIDEO_ADMIN', 'Listado de videos', 'VIDEO'),
(49, 'VIDEO_AVAILABLE', 'Aprobar video', 'VIDEO'),
(50, 'VIDEO_UPDATE', 'Modificar video', 'VIDEO'),
(51, 'VIDEO_DELETE', 'Eliminar video', 'VIDEO'),
(52, 'VIDEO_CREATE', 'Crear video', 'VIDEO'),
(53, 'VIDEO_TRASH', 'gestiÃ³n papelera video', 'VIDEO'),
(133, 'CONTENT_OTHER_DELETE', 'Poder eliminar contenido de otros usuarios', 'CONTENT'),
(130, 'ONM_CONFIG', 'Configurar Onm', 'ONM'),
(131, 'ONM_MANAGER', 'Gestionar Onm', 'ONM'),
(132, 'CONTENT_OTHER_UPDATE', 'Poder modificar contenido de otros usuarios', 'CONTENT'),
(129, 'CACHE_APC_ADMIN', 'Gestion cache de APC', 'CACHE'),
(60, 'IMAGE_ADMIN', 'Listado de imÃ¡genes', 'IMAGE'),
(61, 'IMAGE_AVAILABLE', 'Aprobar Imagen', 'IMAGE'),
(62, 'IMAGE_UPDATE', 'Modificar Imagen', 'IMAGE'),
(63, 'IMAGE_DELETE', 'Eliminar Imagen', 'IMAGE'),
(64, 'IMAGE_CREATE', 'Subir Imagen', 'IMAGE'),
(65, 'IMAGE_TRASH', 'gestiÃ³n papelera Imagen', 'IMAGE'),
(66, 'STATIC_ADMIN', 'Listado pÃ¡ginas estÃ¡ticas', 'STATIC'),
(67, 'STATIC_AVAILABLE', 'Aprobar PÃ¡gina EstÃ¡tica', 'STATIC'),
(68, 'STATIC_UPDATE', 'Modificar PÃ¡gina EstÃ¡tica', 'STATIC'),
(69, 'STATIC_DELETE', 'Eliminar PÃ¡gina EstÃ¡tica', 'STATIC'),
(70, 'STATIC_CREATE', 'Crear PÃ¡gina EstÃ¡tica', 'STATIC'),
(71, 'KIOSKO_ADMIN', 'Listar PÃ¡gina Papel', 'KIOSKO'),
(72, 'KIOSKO_AVAILABLE', 'Aprobar PÃ¡gina Papel', 'KIOSKO'),
(73, 'KIOSKO_UPDATE', 'Modificar PÃ¡gina Papel', 'KIOSKO'),
(74, 'KIOSKO_DELETE', 'Eliminar PÃ¡gina Papel', 'KIOSKO'),
(75, 'KIOSKO_CREATE', 'Crear PÃ¡gina Papel', 'KIOSKO'),
(76, 'KIOSKO_HOME', 'Incluir en portada como favorito', 'KIOSKO'),
(77, 'POLL_ADMIN', 'Listado encuestas', 'POLL'),
(78, 'POLL_AVAILABLE', 'Aprobar Encuesta', 'POLL'),
(79, 'POLL_UPDATE', 'Modificar Encuesta', 'POLL'),
(80, 'POLL_DELETE', 'Eliminar Encuesta', 'POLL'),
(81, 'POLL_CREATE', 'Crear Encuesta', 'POLL'),
(82, 'AUTHOR_ADMIN', 'Listado autores OpiniÃ³n', 'AUTHOR'),
(83, 'AUTHOR_UPDATE', 'Modificar Autor', 'AUTHOR'),
(84, 'AUTHOR_DELETE', 'Eliminar Autor', 'AUTHOR'),
(85, 'AUTHOR_CREATE', 'Crear Autor', 'AUTHOR'),
(86, 'USER_ADMIN', 'Listado de usuarios', 'USER'),
(87, 'USER_UPDATE', 'Modificar Usuario', 'USER'),
(88, 'USER_DELETE', 'Eliminar Usuario', 'USER'),
(89, 'USER_CREATE', 'Crear Usuario', 'USER'),
(90, 'PCLAVE_ADMIN', 'Listado de palabras clave', 'PCLAVE'),
(91, 'PCLAVE_UPDATE', 'Modificar Palabra Clave', 'PCLAVE'),
(92, 'PCLAVE_DELETE', 'Eliminar Palabra Clave', 'PCLAVE'),
(93, 'PCLAVE_CREATE', 'Crear Palabra Clave', 'PCLAVE'),
(95, 'GROUP_ADMIN', 'Grupo usuarios Admin', 'GROUP'),
(96, 'GROUP_UPDATE', 'Modificar Grupo Usuarios', 'GROUP'),
(97, 'GROUP_DELETE', 'Eliminar Grupo Usuarios', 'GROUP'),
(98, 'GROUP_ADMIN', 'Listado de Grupo Usuarios', 'GROUP'),
(99, 'GROUP_CREATE', 'Crear Grupo Usuarios', 'GROUP'),
(100, 'PRIVILEGE_UPDATE', 'Modificar Privilegio', 'PRIVILEGE'),
(101, 'PRIVILEGE_DELETE', 'Eliminar Privilegio', 'PRIVILEGE'),
(102, 'PRIVILEGE_ADMIN', 'Listado de Privilegios', 'PRIVILEGE'),
(103, 'PRIVILEGE_CREATE', 'Crear Privilegio', 'PRIVILEGE'),
(104, 'FILE_ADMIN', 'Listado de ficheros y portadas', 'FILE'),
(105, 'FILE_FRONTS', 'GestiÃ³n de portadas', 'FILE'),
(106, 'FILE_UPDATE', 'Modificar Fichero', 'FILE'),
(107, 'FILE_DELETE', 'Eliminar Fichero', 'FILE'),
(108, 'FILE_CREATE', 'Crear Fichero', 'FILE'),
(117, 'WIDGET_ADMIN', 'Listado de widgets', 'WIDGET'),
(110, 'BADLINK_ADMIN', 'Control Link Admin', 'BADLINK'),
(111, 'STATS_ADMIN', 'Admin EstadÃ­sticas', 'STATS'),
(112, 'NEWSLETTER_ADMIN', 'AdministraciÃ³n del boletÃ­n', 'NEWSLETTER'),
(113, 'BACKEND_ADMIN', 'ConfiguraciÃ³n de backend', 'BACKEND'),
(114, 'CACHE_TPL_ADMIN', 'GestiÃ³n de CachÃ©s Portadas', 'CACHE'),
(115, 'SEARCH_ADMIN', 'Utilidades: bÃºsqueda avanzada', 'SEARCH'),
(116, 'TRASH_ADMIN', 'GestiÃ³n papelera', 'TRASH'),
(118, 'WIDGET_AVAILABLE', 'Aprobar Widget', 'WIDGET'),
(119, 'WIDGET_UPDATE', 'Modificar Widget', 'WIDGET'),
(120, 'WIDGET_DELETE', 'Eliminar Widget', 'WIDGET'),
(121, 'WIDGET_CREATE', 'Crear Widget', 'WIDGET'),
(122, 'MENU_ADMIN', 'Listado de menus', 'MENU'),
(123, 'MENU_AVAILABLE', 'Leer menu', 'MENU'),
(124, 'MENU_UPDATE', 'Modificar menu', 'MENU'),
(125, 'IMPORT_ADMIN', 'Listado de menus', 'IMPORT'),
(126, 'IMPORT_EPRESS', 'Leer Widget', 'IMPORT'),
(127, 'IMPORT_XML', 'Modificar Widget', 'IMPORT'),
(128, 'IMPORT_EFE', 'Eliminar Widget', 'IMPORT'),
(134, 'ONM_SETTINGS', 'Allow to configure system wide settings', 'ONM');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE IF NOT EXISTS `ratings` (
  `pk_rating` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `total_votes` smallint(5) unsigned NOT NULL DEFAULT '0',
  `total_value` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ips_count_rating` longtext,
  PRIMARY KEY (`pk_rating`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=104 ;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`pk_rating`, `total_votes`, `total_value`, `ips_count_rating`) VALUES
(82, 0, 0, 'a:0:{}'),
(80, 0, 0, 'a:0:{}'),
(10, 0, 0, 'a:0:{}'),
(87, 0, 0, 'a:0:{}'),
(63, 0, 0, 'a:0:{}'),
(62, 0, 0, 'a:0:{}'),
(14, 0, 0, 'a:0:{}'),
(52, 0, 0, 'a:0:{}'),
(86, 0, 0, 'a:0:{}'),
(54, 0, 0, 'a:0:{}'),
(58, 0, 0, 'a:0:{}'),
(103, 0, 0, 'a:0:{}'),
(56, 0, 0, 'a:0:{}'),
(60, 0, 0, 'a:0:{}'),
(64, 0, 0, 'a:0:{}'),
(49, 0, 0, 'a:0:{}'),
(53, 0, 0, 'a:0:{}'),
(96, 0, 0, 'a:0:{}'),
(55, 0, 0, 'a:0:{}');

-- --------------------------------------------------------

--
-- Table structure for table `related_contents`
--

CREATE TABLE IF NOT EXISTS `related_contents` (
  `pk_content1` bigint(20) unsigned NOT NULL,
  `pk_content2` bigint(20) unsigned NOT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `text` varchar(50) DEFAULT NULL,
  `position` int(10) NOT NULL DEFAULT '0',
  `posinterior` int(2) NOT NULL DEFAULT '0',
  `verportada` int(2) NOT NULL DEFAULT '0',
  `verinterior` int(2) NOT NULL DEFAULT '0',
  KEY `pk_content1` (`pk_content1`),
  KEY `verportada` (`verportada`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `related_contents`
--

INSERT INTO `related_contents` (`pk_content1`, `pk_content2`, `relationship`, `text`, `position`, `posinterior`, `verportada`, `verinterior`) VALUES
(82, 58, NULL, NULL, 0, 2, 0, 1),
(82, 46, NULL, NULL, 0, 1, 0, 1),
(82, 58, NULL, NULL, 3, 0, 1, 0),
(82, 46, NULL, NULL, 2, 0, 1, 0),
(82, 61, NULL, NULL, 1, 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(128) NOT NULL DEFAULT '',
  `value` longtext NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`name`, `value`) VALUES
('site_title', 's:23:"Opennemas instance demo";'),
('site_description', 's:117:"InformaciÃ³n continua y anÃ¡lisis para una ciudadanÃ­a comprometida con los valores de libertad, igualdad y justicia.";'),
('europapress_server_auth', 'a:3:{s:6:"server";s:0:"";s:8:"username";s:0:"";s:8:"password";s:0:"";}'),
('site_keywords', 's:117:"InformaciÃ³n continua y anÃ¡lisis para una ciudadanÃ­a comprometida con los valores de libertad, igualdad y justicia.";'),
('time_zone', 's:3:"330";'),
('site_language', 's:5:"en_US";'),
('mail_server', 's:9:"localhost";'),
('mail_username', 's:9:"webmaster";'),
('mail_password', 's:0:"";'),
('google_maps_api_key', 's:86:"ABQIAAAA_RE85FLaf_hXdhkxaS463hQC49KlvU2s_1jV47V5-i8q6UJ2IBQiAxw97Jt7tEWzuIY513Qutp-Cqg";'),
('google_custom_search_api_key', 's:33:"015934879308878545076:fvw-rbc1ipq";'),
('facebook', 'a:2:{s:7:"api_key";s:1:" ";s:10:"secret_key";s:1:" ";}'),
('google_analytics', 'a:2:{s:7:"api_key";s:1:" ";s:11:"base_domain";s:1:" ";}'),
('piwik', 'a:2:{s:7:"page_id";s:1:" ";s:10:"server_url";s:1:" ";}'),
('recaptcha', 'a:2:{s:10:"public_key";s:40:"6LfpY8ISAAAAAAuChcU2Agdwg8YzhprxZZ55B7Is";s:11:"private_key";s:40:"6LfpY8ISAAAAAAuChcU2Agdwg8YzhprxZZ55B7Is";}'),
('items_per_page', 's:2:"20";'),
('refresh_interval', 's:3:"900";'),
('advertisements_enabled', 'b:0;'),
('log_level', 's:6:"normal";'),
('log_enabled', 'b:1;'),
('log_db_enabled', 'b:1;'),
('newsletter_maillist', 'a:2:{s:4:"name";s:8:"Openhost";s:5:"email";s:30:"newsletter@lists.opennemas.com";}'),
('site_agency', 's:13:"opennemas.com";'),
('activated_modules', 'a:32:{i:0;s:11:"ADS_MANAGER";i:1;s:15:"ADVANCED_SEARCH";i:2;s:13:"ALBUM_MANAGER";i:3;s:15:"ARTICLE_MANAGER";i:4;s:13:"CACHE_MANAGER";i:5;s:16:"CATEGORY_MANAGER";i:6;s:15:"COMMENT_MANAGER";i:7;s:20:"EUROPAPRESS_IMPORTER";i:8;s:12:"FILE_MANAGER";i:9;s:17:"FRONTPAGE_MANAGER";i:10;s:13:"IMAGE_MANAGER";i:11;s:15:"KEYWORD_MANAGER";i:12;s:14:"KIOSKO_MANAGER";i:13;s:20:"LINK_CONTROL_MANAGER";i:14;s:12:"MENU_MANAGER";i:15;s:13:"MYSQL_MANAGER";i:16;s:18:"NEWSLETTER_MANAGER";i:17;s:14:"ONM_STATISTICS";i:18;s:15:"OPINION_MANAGER";i:19;s:12:"PAPER_IMPORT";i:20;s:17:"PHP_CACHE_MANAGER";i:21;s:12:"POLL_MANAGER";i:22;s:17:"PRIVILEGE_MANAGER";i:23;s:16:"SETTINGS_MANAGER";i:24;s:20:"STATIC_PAGES_MANAGER";i:25;s:21:"SYSTEM_UPDATE_MANAGER";i:26;s:13:"TRASH_MANAGER";i:27;s:18:"USER_GROUP_MANAGER";i:28;s:12:"USER_MANAGER";i:29;s:13:"VIDEO_MANAGER";i:30;s:14:"WIDGET_MANAGER";i:31;s:7:"LOG_SQL";}'),
('europapress_sync_from_limit', 's:6:"604800";'),
('album_settings', 'a:5:{s:12:"total_widget";s:1:"4";s:10:"crop_width";s:3:"300";s:11:"crop_height";s:3:"240";s:11:"total_front";s:1:"2";s:9:"time_last";s:2:"10";}'),
('video_settings', 'a:3:{s:12:"total_widget";s:1:"4";s:11:"total_front";s:1:"2";s:13:"total_gallery";s:2:"20";}'),
('poll_settings', 'a:3:{s:9:"typeValue";s:7:"percent";s:9:"widthPoll";s:3:"600";s:10:"heightPoll";s:3:"500";}');

-- --------------------------------------------------------

--
-- Table structure for table `static_pages`
--

CREATE TABLE IF NOT EXISTS `static_pages` (
  `pk_static_page` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'BIGINT(20)',
  `body` text NOT NULL COMMENT 'HTML content for static page',
  `slug` varchar(255) NOT NULL,
  PRIMARY KEY (`pk_static_page`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `static_pages`
--


-- --------------------------------------------------------

--
-- Table structure for table `translation_ids`
--

CREATE TABLE IF NOT EXISTS `translation_ids` (
  `pk_content_old` bigint(10) NOT NULL,
  `pk_content` bigint(10) NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`pk_content_old`,`pk_content`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `translation_ids`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `pk_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `online` tinyint(1) NOT NULL DEFAULT '0',
  `login` varchar(100) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `sessionexpire` tinyint(2) unsigned NOT NULL DEFAULT '15',
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `authorize` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 authorized - 0 unauthorized',
  `fk_user_group` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`pk_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=133 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`pk_user`, `online`, `login`, `password`, `sessionexpire`, `email`, `name`, `firstname`, `lastname`, `address`, `phone`, `authorize`, `fk_user_group`) VALUES
(7, 0, 'sandra', 'bd80e7c35b56dccd2d1796cf39cd05f6', 45, 'sandra@openhost.es', 'sandra', 'pereira', 'alvarez', '', '', 1, 5),
(3, 0, 'macada', '2f575705daf41049194613e47027200b', 30, 'david.martinez@openhost.es', 'David', 'Martinez', 'Carballo', ' ', ' ', 1, 4),
(5, 0, 'fran', '6d87cd9493f11b830bbfdf628c2c4f08', 65, 'fran@openhost.es', 'Francisco ', 'DiÃ©guez', 'Souto', ' ', ' ', 1, 4),
(4, 0, 'alex', '4c246829b53bc5712d52ee777c52ebe7', 60, 'alex@openhost.es', 'Alexandre', 'Rico', '', '', '', 1, 4),
(132, 0, 'admin', '75bba3adeaec86b143375d90a6d61bfd', 45, 'admin@opennemas.com', 'administrator', 'administrator', NULL, NULL, NULL, 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `users_content_categories`
--

CREATE TABLE IF NOT EXISTS `users_content_categories` (
  `pk_fk_user` int(10) unsigned NOT NULL,
  `pk_fk_content_category` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pk_fk_user`,`pk_fk_content_category`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_content_categories`
--


-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `pk_user_group` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`pk_user_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`pk_user_group`, `name`) VALUES
(4, 'Masters'),
(5, 'Administrador');

-- --------------------------------------------------------

--
-- Table structure for table `user_groups_privileges`
--

CREATE TABLE IF NOT EXISTS `user_groups_privileges` (
  `pk_fk_user_group` int(10) unsigned NOT NULL,
  `pk_fk_privilege` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pk_fk_user_group`,`pk_fk_privilege`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_groups_privileges`
--


-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE IF NOT EXISTS `videos` (
  `pk_video` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `video_url` varchar(255) NOT NULL,
  `information` text,
  `author_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`pk_video`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=111 ;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`pk_video`, `video_url`, `information`, `author_name`) VALUES
(95, 'http://vimeo.com/28551506', 'a:8:{s:5:"title";s:12:"Sheeped Away";s:9:"thumbnail";s:50:"http://b.vimeocdn.com/ts/190/563/190563238_640.jpg";s:8:"embedUrl";s:120:"http://vimeo.com/moogaloop.swf?clip_id=28551506&server=vimeo.com&fullscreen=1&show_title=1&show_byline=1&show_portrait=1";s:9:"embedHTML";s:862:"<object width=''560'' height=''349''>\n                        <param name=''movie'' value=''http://vimeo.com/moogaloop.swf?clip_id=28551506&server=vimeo.com&fullscreen=1&show_title=1&show_byline=1&show_portrait=1''></param>\n                        <param name=''allowFullScreen'' value=''true''></param>\n                        <param name=''allowscriptaccess'' value=''always''></param>\n                        <param name=''wmode'' value=''transparent''></param>\n                        <embed\n                            src=''http://vimeo.com/moogaloop.swf?clip_id=28551506&server=vimeo.com&fullscreen=1&show_title=1&show_byline=1&show_portrait=1'' type=''application/x-shockwave-flash''\n                            allowscriptaccess=''always'' allowfullscreen=''true''\n                            width=''560'' height=''349''>\n                        </embed>\n                    </object>";s:3:"FLV";s:85:"http://www.vimeo.com/moogaloop/play/clip:28551506/6b29df37464822294ae10a1ec74241ea/6/";s:11:"downloadUrl";s:85:"http://www.vimeo.com/moogaloop/play/clip:28551506/6b29df37464822294ae10a1ec74241ea/6/";s:7:"service";s:5:"Vimeo";s:8:"duration";s:3:"322";}', 'Vimeo'),
(96, 'http://www.youtube.com/watch?v=f7z8ZiRcQ9Q&feature=fvwrel', 'a:8:{s:5:"title";s:50:"13 957 people dancing thriller in Mexico 1 of 2 HQ";s:9:"thumbnail";s:39:"http://i.ytimg.com/vi/f7z8ZiRcQ9Q/0.jpg";s:8:"embedUrl";s:63:"http://www.youtube.com/v/f7z8ZiRcQ9Q?f=videos&app=youtube_gdata";s:9:"embedHTML";s:647:"<object width=''560'' height=''349''>\n                    <param name=''movie'' value=''http://www.youtube.com/v/f7z8ZiRcQ9Q?f=videos&app=youtube_gdata''>\n                    <param name=''allowFullScreen'' value=''true''>\n                    <param name=''allowscriptaccess'' value=''always''>\n                    <param name=''wmode'' value=''transparent''>\n                    <embed\n                        src=''http://www.youtube.com/v/f7z8ZiRcQ9Q?f=videos&app=youtube_gdata'' type=''application/x-shockwave-flash''\n                        allowscriptaccess=''always'' allowfullscreen=''true''\n                        width=''560'' height=''349''>\n                </object>";s:3:"FLV";s:63:"http://www.youtube.com/v/f7z8ZiRcQ9Q?f=videos&app=youtube_gdata";s:11:"downloadUrl";s:63:"http://www.youtube.com/v/f7z8ZiRcQ9Q?f=videos&app=youtube_gdata";s:7:"service";s:7:"Youtube";s:8:"duration";s:3:"125";}', 'Youtube'),
(97, 'http://www.youtube.com/watch?v=8HYP34nSaaY&feature=feedrec_grec_index', 'a:8:{s:5:"title";s:18:"Audi S3 Model 2009";s:9:"thumbnail";s:39:"http://i.ytimg.com/vi/8HYP34nSaaY/0.jpg";s:8:"embedUrl";s:63:"http://www.youtube.com/v/8HYP34nSaaY?f=videos&app=youtube_gdata";s:9:"embedHTML";s:647:"<object width=''560'' height=''349''>\n                    <param name=''movie'' value=''http://www.youtube.com/v/8HYP34nSaaY?f=videos&app=youtube_gdata''>\n                    <param name=''allowFullScreen'' value=''true''>\n                    <param name=''allowscriptaccess'' value=''always''>\n                    <param name=''wmode'' value=''transparent''>\n                    <embed\n                        src=''http://www.youtube.com/v/8HYP34nSaaY?f=videos&app=youtube_gdata'' type=''application/x-shockwave-flash''\n                        allowscriptaccess=''always'' allowfullscreen=''true''\n                        width=''560'' height=''349''>\n                </object>";s:3:"FLV";s:63:"http://www.youtube.com/v/8HYP34nSaaY?f=videos&app=youtube_gdata";s:11:"downloadUrl";s:63:"http://www.youtube.com/v/8HYP34nSaaY?f=videos&app=youtube_gdata";s:7:"service";s:7:"Youtube";s:8:"duration";s:2:"34";}', 'Youtube'),
(98, 'http://vimeo.com/27476225', 'a:8:{s:5:"title";s:20:"Real Scenes: Detroit";s:9:"thumbnail";s:50:"http://b.vimeocdn.com/ts/182/511/182511900_640.jpg";s:8:"embedUrl";s:120:"http://vimeo.com/moogaloop.swf?clip_id=27476225&server=vimeo.com&fullscreen=1&show_title=1&show_byline=1&show_portrait=1";s:9:"embedHTML";s:862:"<object width=''560'' height=''349''>\n                        <param name=''movie'' value=''http://vimeo.com/moogaloop.swf?clip_id=27476225&server=vimeo.com&fullscreen=1&show_title=1&show_byline=1&show_portrait=1''></param>\n                        <param name=''allowFullScreen'' value=''true''></param>\n                        <param name=''allowscriptaccess'' value=''always''></param>\n                        <param name=''wmode'' value=''transparent''></param>\n                        <embed\n                            src=''http://vimeo.com/moogaloop.swf?clip_id=27476225&server=vimeo.com&fullscreen=1&show_title=1&show_byline=1&show_portrait=1'' type=''application/x-shockwave-flash''\n                            allowscriptaccess=''always'' allowfullscreen=''true''\n                            width=''560'' height=''349''>\n                        </embed>\n                    </object>";s:3:"FLV";s:85:"http://www.vimeo.com/moogaloop/play/clip:27476225/9d1a38d6c937b2df51fd1b7b4e11ed46/9/";s:11:"downloadUrl";s:85:"http://www.vimeo.com/moogaloop/play/clip:27476225/9d1a38d6c937b2df51fd1b7b4e11ed46/9/";s:7:"service";s:5:"Vimeo";s:8:"duration";s:4:"1138";}', 'Vimeo'),
(99, 'http://www.youtube.com/watch?v=fyMhvkC3A84', 'a:8:{s:5:"title";s:40:"Coldplay - Every Teardrop Is A Waterfall";s:9:"thumbnail";s:39:"http://i.ytimg.com/vi/fyMhvkC3A84/0.jpg";s:8:"embedUrl";s:63:"http://www.youtube.com/v/fyMhvkC3A84?f=videos&app=youtube_gdata";s:9:"embedHTML";s:647:"<object width=''560'' height=''349''>\n                    <param name=''movie'' value=''http://www.youtube.com/v/fyMhvkC3A84?f=videos&app=youtube_gdata''>\n                    <param name=''allowFullScreen'' value=''true''>\n                    <param name=''allowscriptaccess'' value=''always''>\n                    <param name=''wmode'' value=''transparent''>\n                    <embed\n                        src=''http://www.youtube.com/v/fyMhvkC3A84?f=videos&app=youtube_gdata'' type=''application/x-shockwave-flash''\n                        allowscriptaccess=''always'' allowfullscreen=''true''\n                        width=''560'' height=''349''>\n                </object>";s:3:"FLV";s:63:"http://www.youtube.com/v/fyMhvkC3A84?f=videos&app=youtube_gdata";s:11:"downloadUrl";s:63:"http://www.youtube.com/v/fyMhvkC3A84?f=videos&app=youtube_gdata";s:7:"service";s:7:"Youtube";s:8:"duration";s:3:"250";}', 'Youtube'),
(100, 'http://www.youtube.com/watch?v=iH3oOVKt0WI', 'a:8:{s:5:"title";s:42:"Marilyn Monroe Happy Birthday Mr President";s:9:"thumbnail";s:39:"http://i.ytimg.com/vi/iH3oOVKt0WI/0.jpg";s:8:"embedUrl";s:63:"http://www.youtube.com/v/iH3oOVKt0WI?f=videos&app=youtube_gdata";s:9:"embedHTML";s:647:"<object width=''560'' height=''349''>\n                    <param name=''movie'' value=''http://www.youtube.com/v/iH3oOVKt0WI?f=videos&app=youtube_gdata''>\n                    <param name=''allowFullScreen'' value=''true''>\n                    <param name=''allowscriptaccess'' value=''always''>\n                    <param name=''wmode'' value=''transparent''>\n                    <embed\n                        src=''http://www.youtube.com/v/iH3oOVKt0WI?f=videos&app=youtube_gdata'' type=''application/x-shockwave-flash''\n                        allowscriptaccess=''always'' allowfullscreen=''true''\n                        width=''560'' height=''349''>\n                </object>";s:3:"FLV";s:63:"http://www.youtube.com/v/iH3oOVKt0WI?f=videos&app=youtube_gdata";s:11:"downloadUrl";s:63:"http://www.youtube.com/v/iH3oOVKt0WI?f=videos&app=youtube_gdata";s:7:"service";s:7:"Youtube";s:8:"duration";s:2:"29";}', 'Youtube'),
(101, 'http://www.youtube.com/watch?v=DUx4CPEhSD8', 'a:8:{s:5:"title";s:59:"Trailer Millennium: Los hombres que no amaban a las mujeres";s:9:"thumbnail";s:39:"http://i.ytimg.com/vi/DUx4CPEhSD8/0.jpg";s:8:"embedUrl";s:63:"http://www.youtube.com/v/DUx4CPEhSD8?f=videos&app=youtube_gdata";s:9:"embedHTML";s:647:"<object width=''560'' height=''349''>\n                    <param name=''movie'' value=''http://www.youtube.com/v/DUx4CPEhSD8?f=videos&app=youtube_gdata''>\n                    <param name=''allowFullScreen'' value=''true''>\n                    <param name=''allowscriptaccess'' value=''always''>\n                    <param name=''wmode'' value=''transparent''>\n                    <embed\n                        src=''http://www.youtube.com/v/DUx4CPEhSD8?f=videos&app=youtube_gdata'' type=''application/x-shockwave-flash''\n                        allowscriptaccess=''always'' allowfullscreen=''true''\n                        width=''560'' height=''349''>\n                </object>";s:3:"FLV";s:63:"http://www.youtube.com/v/DUx4CPEhSD8?f=videos&app=youtube_gdata";s:11:"downloadUrl";s:63:"http://www.youtube.com/v/DUx4CPEhSD8?f=videos&app=youtube_gdata";s:7:"service";s:7:"Youtube";s:8:"duration";s:3:"125";}', 'Youtube'),
(102, 'http://vimeo.com/4794462', 'a:8:{s:5:"title";s:8:"Mystique";s:9:"thumbnail";s:49:"http://b.vimeocdn.com/ts/131/835/13183512_640.jpg";s:8:"embedUrl";s:119:"http://vimeo.com/moogaloop.swf?clip_id=4794462&server=vimeo.com&fullscreen=1&show_title=1&show_byline=1&show_portrait=1";s:9:"embedHTML";s:860:"<object width=''560'' height=''349''>\n                        <param name=''movie'' value=''http://vimeo.com/moogaloop.swf?clip_id=4794462&server=vimeo.com&fullscreen=1&show_title=1&show_byline=1&show_portrait=1''></param>\n                        <param name=''allowFullScreen'' value=''true''></param>\n                        <param name=''allowscriptaccess'' value=''always''></param>\n                        <param name=''wmode'' value=''transparent''></param>\n                        <embed\n                            src=''http://vimeo.com/moogaloop.swf?clip_id=4794462&server=vimeo.com&fullscreen=1&show_title=1&show_byline=1&show_portrait=1'' type=''application/x-shockwave-flash''\n                            allowscriptaccess=''always'' allowfullscreen=''true''\n                            width=''560'' height=''349''>\n                        </embed>\n                    </object>";s:3:"FLV";s:84:"http://www.vimeo.com/moogaloop/play/clip:4794462/16de187d327ab74f8267b53add963be7/1/";s:11:"downloadUrl";s:84:"http://www.vimeo.com/moogaloop/play/clip:4794462/16de187d327ab74f8267b53add963be7/1/";s:7:"service";s:5:"Vimeo";s:8:"duration";s:2:"45";}', 'Vimeo'),
(103, 'http://www.youtube.com/watch?v=OIyjnyHj1mk', 'a:8:{s:5:"title";s:45:"How to Get Fit - P90X Video with Tony Horton!";s:9:"thumbnail";s:39:"http://i.ytimg.com/vi/OIyjnyHj1mk/0.jpg";s:8:"embedUrl";s:63:"http://www.youtube.com/v/OIyjnyHj1mk?f=videos&app=youtube_gdata";s:9:"embedHTML";s:647:"<object width=''560'' height=''349''>\n                    <param name=''movie'' value=''http://www.youtube.com/v/OIyjnyHj1mk?f=videos&app=youtube_gdata''>\n                    <param name=''allowFullScreen'' value=''true''>\n                    <param name=''allowscriptaccess'' value=''always''>\n                    <param name=''wmode'' value=''transparent''>\n                    <embed\n                        src=''http://www.youtube.com/v/OIyjnyHj1mk?f=videos&app=youtube_gdata'' type=''application/x-shockwave-flash''\n                        allowscriptaccess=''always'' allowfullscreen=''true''\n                        width=''560'' height=''349''>\n                </object>";s:3:"FLV";s:63:"http://www.youtube.com/v/OIyjnyHj1mk?f=videos&app=youtube_gdata";s:11:"downloadUrl";s:63:"http://www.youtube.com/v/OIyjnyHj1mk?f=videos&app=youtube_gdata";s:7:"service";s:7:"Youtube";s:8:"duration";s:2:"89";}', 'Youtube'),
(104, 'http://www.marca.com/tv/?v=9JoC4PE1rp8', 'a:8:{s:5:"title";s:48:"Larry Johnson, la ''abuela'' mÃ¡s famosa de la NBA";s:9:"thumbnail";s:73:"http://estaticos.marca.com/consolamultimedia/elementos/2011/07/16/382.jpg";s:8:"embedUrl";s:91:"http://www.marca.com/componentes/flash/embed.swf?ba=0&cvol=1&bt=1&lg=1&vID=9JoC4PE1rp8&ba=1";s:9:"embedHTML";s:1215:"<object\n                    width=''560'' height=''349''\n                    classid=''clsid:d27cdb6e-ae6d-11cf-96b8-444553540000''\n                    codebase=''http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0''>\n                    <param name=''movie'' value=''http://estaticos.marca.com/multimedia/reproductores/newPlayer.swf''>\n                    <param name=''quality'' value=''high''>\n                    <param name=''allowFullScreen'' value=''true''>\n                    <param name=''wmode'' value=''transparent''>\n                    <param name=''FlashVars'' value=''ba=1&amp;cvol=1&amp;bt=1&amp;lg=0&amp;width=560&amp;height=349&amp;vID=9JoC4PE1rp8''>\n                    <embed\n                        width=''560'' height=''349''\n                        src=''http://estaticos03.marca.com/multimedia/reproductores/newPlayer.swf''\n                        quality=''high''\n                        flashvars=''ba=1&amp;cvol=1&amp;bt=1&amp;lg=0&amp;vID=9JoC4PE1rp8'' allowfullscreen=''true''\n                        type=''application/x-shockwave-flash''\n                        pluginspage=''http://www.macromedia.com/go/getflashplayer''\n                        wmode=''transparent''>\n                </object>";s:3:"FLV";s:65:"http://cachevideos.marca.com/estaticos/2011/07/16/110716larry.flv";s:11:"downloadUrl";N;s:7:"service";s:5:"Marca";s:8:"duration";N;}', 'Marca'),
(105, 'http://11870.com/pro/la-cabana-argentina/videos/25f8deec', 'a:8:{s:5:"title";s:34:"vÃƒÂ­deo de La CabaÃƒÂ±a Argentina";s:9:"thumbnail";s:78:"http://m1.11870.com/multimedia/videos/vlp_22c16ba2bb50b4adfe67271f4fa8a345.jpg";s:8:"embedUrl";s:348:"http://m0.11870.com/multimedia/11870/player.swf?file=http://m0.11870.com/multimedia/videos/22c16ba2bb50b4adfe67271f4fa8a345.mp4&image=http://m1.11870.com/multimedia/videos/vlp_22c16ba2bb50b4adfe67271f4fa8a345.jpg&logo=http://m0.11870.com/multimedia/11870/embed_watermark.png&icons=false&logo=http://m0.11870.com/multimedia/11870/embed_watermark.png";s:9:"embedHTML";s:1190:"<object\n                    type=''application/x-shockwave-flash''\n                    data=''http://11870.com/multimedia/11870/player.swf''\n                    width=''560'' height=''349''\n                    bgcolor=''#000000''>\n                    <param name=''movie'' value=''http://m0.11870.com/multimedia/11870/player.swf?file=http://m0.11870.com/multimedia/videos/22c16ba2bb50b4adfe67271f4fa8a345.mp4&image=http://m1.11870.com/multimedia/videos/vlp_22c16ba2bb50b4adfe67271f4fa8a345.jpg&logo=http://m0.11870.com/multimedia/11870/embed_watermark.png&icons=false&logo=http://m0.11870.com/multimedia/11870/embed_watermark.png'' />\n                    <param name=''allowfullscreen'' value=''true''>\n                    <param name=''allowscriptaccess'' value=''always''>\n                    <param name=''seamlesstabbing'' value=''true''>\n                    <param name=''wmode'' value=''window''>\n                    <param name=''flashvars'' value=''file=http://m0.11870.com/multimedia/videos/22c16ba2bb50b4adfe67271f4fa8a345.mp4&image=http://m1.11870.com/multimedia/videos/vlp_22c16ba2bb50b4adfe67271f4fa8a345.jpg&logo=http://m0.11870.com/multimedia/11870/embed_watermark.png&icons=false''>\n                </object>";s:3:"FLV";s:74:"http://m0.11870.com/multimedia/videos/22c16ba2bb50b4adfe67271f4fa8a345.mp4";s:11:"downloadUrl";s:74:"http://m0.11870.com/multimedia/videos/22c16ba2bb50b4adfe67271f4fa8a345.mp4";s:7:"service";s:5:"11870";s:8:"duration";N;}', '11870'),
(106, 'http://rutube.ru/tracks/4436308.html?v=da5ede8f5aa5832e74b8afec8bd1818f', 'a:8:{s:5:"title";s:43:"Ð¤ÐµÐ¹ÐµÑ€Ð²ÐµÑ€Ðº Ñ€Ð°Ð·Ð±ÑƒÑˆÐµÐ²Ð°Ð»ÑÑ";s:9:"thumbnail";s:72:"http://img.rutube.ru/thumbs/da/5e/da5ede8f5aa5832e74b8afec8bd1818f-2.jpg";s:8:"embedUrl";s:55:"http://video.rutube.ru/da5ede8f5aa5832e74b8afec8bd1818f";s:9:"embedHTML";s:647:"<object width=''560'' height=''349''>\n                    <param name=''movie'' value=''http://video.rutube.ru/da5ede8f5aa5832e74b8afec8bd1818f''></param>\n                    <param name=''wmode'' value=''window''></param>\n                    <param name=''allowFullScreen'' value=''true''></param>\n                    <embed\n                        src=''http://video.rutube.ru/da5ede8f5aa5832e74b8afec8bd1818f''\n                        type=''application/x-shockwave-flash''\n                        wmode=''window''\n                        width=''560'' height=''349''\n                        allowFullScreen=''true''>\n                    </embed>\n                </object>";s:3:"FLV";s:57:"http://bl.rutube.ru/da5ede8f5aa5832e74b8afec8bd1818f.iflv";s:11:"downloadUrl";N;s:7:"service";s:6:"Rutube";s:8:"duration";N;}', 'Rutube'),
(107, 'http://www.marca.com/tv/?v=DN23wG8c1Rj', 'a:8:{s:5:"title";s:55:"Pau entra por la puerta grande en el club de los 10.000";s:9:"thumbnail";s:67:"http://www.marca.com/consolamultimedia/elementos/2009/01/03/306.jpg";s:8:"embedUrl";s:91:"http://www.marca.com/componentes/flash/embed.swf?ba=0&cvol=1&bt=1&lg=1&vID=DN23wG8c1Rj&ba=1";s:9:"embedHTML";s:1215:"<object\n                    width=''560'' height=''349''\n                    classid=''clsid:d27cdb6e-ae6d-11cf-96b8-444553540000''\n                    codebase=''http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0''>\n                    <param name=''movie'' value=''http://estaticos.marca.com/multimedia/reproductores/newPlayer.swf''>\n                    <param name=''quality'' value=''high''>\n                    <param name=''allowFullScreen'' value=''true''>\n                    <param name=''wmode'' value=''transparent''>\n                    <param name=''FlashVars'' value=''ba=1&amp;cvol=1&amp;bt=1&amp;lg=0&amp;width=560&amp;height=349&amp;vID=DN23wG8c1Rj''>\n                    <embed\n                        width=''560'' height=''349''\n                        src=''http://estaticos03.marca.com/multimedia/reproductores/newPlayer.swf''\n                        quality=''high''\n                        flashvars=''ba=1&amp;cvol=1&amp;bt=1&amp;lg=0&amp;vID=DN23wG8c1Rj'' allowfullscreen=''true''\n                        type=''application/x-shockwave-flash''\n                        pluginspage=''http://www.macromedia.com/go/getflashplayer''\n                        wmode=''transparent''>\n                </object>";s:3:"FLV";s:72:"http://cachevideos.elmundo.es/cr4ip/ES/marca/2009/01/03/090103lakers.flv";s:11:"downloadUrl";N;s:7:"service";s:5:"Marca";s:8:"duration";N;}', 'Marca'),
(108, 'http://www.dailymotion.com/video/x7u5kn_parkour-dayyy_sport', 'N;', 'Dalealplay'),
(109, 'http://www.metacafe.com/watch/6541907/x_men_first_class_reviewed_by_rotten_tomatoes_on_infomania/?source=playlist', 'a:8:{s:5:"title";s:58:"X men first class reviewed by rotten tomatoes on infomania";s:9:"thumbnail";s:41:"http://www.metacafe.com/thumb/6541907.jpg";s:8:"embedUrl";s:102:"http://www.metacafe.com/fplayer/6541907/x_men_first_class_reviewed_by_rotten_tomatoes_on_infomania.swf";s:9:"embedHTML";s:477:"<embed\n                                    src=''http://www.metacafe.com/fplayer/6541907/x_men_first_class_reviewed_by_rotten_tomatoes_on_infomania.swf''\n                                    width=''560'' height=''349''\n                                    wmode=''transparent''\n                                    pluginspage=''http://www.macromedia.com/go/getflashplayer''\n                                    type=''application/x-shockwave-flash''>\n                                </embed>";s:3:"FLV";s:0:"";s:11:"downloadUrl";N;s:7:"service";s:8:"Metacafe";s:8:"duration";N;}', 'Metacafe'),
(110, 'http://www.youtube.com/watch?v=HjAg4pWxW0A', 'a:8:{s:5:"title";s:48:"Discurso de Ãlex de la Iglesia en los Goya 2011";s:9:"thumbnail";s:39:"http://i.ytimg.com/vi/HjAg4pWxW0A/0.jpg";s:8:"embedUrl";s:63:"http://www.youtube.com/v/HjAg4pWxW0A?f=videos&app=youtube_gdata";s:9:"embedHTML";s:647:"<object width=''560'' height=''349''>\n                    <param name=''movie'' value=''http://www.youtube.com/v/HjAg4pWxW0A?f=videos&app=youtube_gdata''>\n                    <param name=''allowFullScreen'' value=''true''>\n                    <param name=''allowscriptaccess'' value=''always''>\n                    <param name=''wmode'' value=''transparent''>\n                    <embed\n                        src=''http://www.youtube.com/v/HjAg4pWxW0A?f=videos&app=youtube_gdata'' type=''application/x-shockwave-flash''\n                        allowscriptaccess=''always'' allowfullscreen=''true''\n                        width=''560'' height=''349''>\n                </object>";s:3:"FLV";s:63:"http://www.youtube.com/v/HjAg4pWxW0A?f=videos&app=youtube_gdata";s:11:"downloadUrl";s:63:"http://www.youtube.com/v/HjAg4pWxW0A?f=videos&app=youtube_gdata";s:7:"service";s:7:"Youtube";s:8:"duration";s:3:"397";}', 'Youtube');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
  `pk_vote` bigint(20) NOT NULL AUTO_INCREMENT,
  `value_pos` smallint(4) NOT NULL DEFAULT '0',
  `value_neg` smallint(4) NOT NULL DEFAULT '0',
  `ips_count_vote` longtext,
  `karma` int(10) unsigned DEFAULT '100',
  PRIMARY KEY (`pk_vote`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `votes`
--


-- --------------------------------------------------------

--
-- Table structure for table `widgets`
--

CREATE TABLE IF NOT EXISTS `widgets` (
  `pk_widget` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `renderlet` varchar(50) DEFAULT 'html',
  PRIMARY KEY (`pk_widget`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `widgets`
--
