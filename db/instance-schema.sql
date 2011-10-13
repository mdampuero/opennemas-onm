-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 28, 2011 at 02:24 PM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-1ubuntu9.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `onm-schema`
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `advertisements`
--


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `albums`
--


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `articles`
--


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `contents`
--


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
(7, 'poll', '', '', 'a:1:{s:11:"description";s:0:"";}', 0),
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

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
(34, 5, 'MÃºsica', 'musica', 'category', 1, 0);

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `opinions`
--


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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

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
(44, '2011092623535639323.jpg', '/2011/09/26/', '2011-09-26 23:53:56', 49.83, '', 450, 300, 'sociedad', 'jpg', '', 'image', 'Color', '');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `polls`
--


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `poll_items`
--


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
(134, 'ONM_SETTINGS', 'Allow to configure system wide settings', 'ONM'),
(135, 'GROUP_CHANGE', 'Cambiar de grupo al usuario ', 'GROUP'),
(136, 'USER_CATEGORY', 'Asignar categorias al usuario ', 'USER');

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
(60, 0, 0, 'a:0:{}');

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
('site_title', 's:18:"Opennemas instance";'),
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
('video_settings', 'a:3:{s:12:"total_widget";s:1:"4";s:11:"total_front";s:1:"2";s:13:"total_gallery";s:2:"20";}');

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
(1, 0, 'admin', '75bba3adeaec86b143375d90a6d61bfd', 45, 'admin@opennemas.com', 'administrator', 'administrator', NULL, NULL, NULL, 1, 5);

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `videos`
--


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
