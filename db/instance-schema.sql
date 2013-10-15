-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Xerado en: 14 de Out de 2013 ás 16:38
-- Versión do servidor: 5.5.31
-- Versión do PHP: 5.3.10-1ubuntu3.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `test2`
--

-- --------------------------------------------------------

--
-- Estrutura da táboa `action_counters`
--

CREATE TABLE IF NOT EXISTS `action_counters` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `action_name` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `counter` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `action_name` (`action_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `advertisements`
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=240 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `albums`
--

CREATE TABLE IF NOT EXISTS `albums` (
  `pk_album` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subtitle` varchar(250) DEFAULT NULL,
  `agency` varchar(250) DEFAULT NULL,
  `cover_id` bigint(255) DEFAULT NULL,
  PRIMARY KEY (`pk_album`),
  UNIQUE KEY `pk_album` (`pk_album`),
  KEY `pk_album_2` (`pk_album`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=183 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `albums_photos`
--

CREATE TABLE IF NOT EXISTS `albums_photos` (
  `pk_album` bigint(20) unsigned NOT NULL,
  `pk_photo` bigint(20) unsigned NOT NULL,
  `position` int(10) DEFAULT '1',
  `description` varchar(250) DEFAULT NULL,
  KEY `pk_album_2` (`pk_album`,`pk_photo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `pk_article` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `summary` text,
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
  `footer_video1` varchar(150) DEFAULT NULL,
  `title_int` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=218 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `attachments`
--

CREATE TABLE IF NOT EXISTS `attachments` (
  `pk_attachment` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `path` varchar(200) NOT NULL,
  `category` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`pk_attachment`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=213 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `books`
--

CREATE TABLE IF NOT EXISTS `books` (
  `pk_book` bigint(20) unsigned NOT NULL,
  `author` varchar(250) DEFAULT NULL,
  `file` varchar(250) DEFAULT NULL,
  `file_img` varchar(255) DEFAULT NULL,
  `editorial` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `author` varchar(150) DEFAULT NULL,
  `author_email` varchar(100) NOT NULL DEFAULT '',
  `author_url` varchar(200) NOT NULL DEFAULT '',
  `author_ip` varchar(100) NOT NULL DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `body` text,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `agent` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `parent_id` bigint(20) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  `content_type_referenced` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `comment_content_id` (`content_id`),
  KEY `comment_status_date` (`status`,`date`),
  KEY `comment_parent_id` (`parent_id`),
  KEY `comment_date` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=224 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `contentmeta`
--

CREATE TABLE IF NOT EXISTS `contentmeta` (
  `fk_content` bigint(20) NOT NULL,
  `meta_name` varchar(255) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`fk_content`,`meta_name`),
  KEY `fk_content` (`fk_content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `contents`
--

CREATE TABLE IF NOT EXISTS `contents` (
  `pk_content` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_content_type` int(10) unsigned NOT NULL,
  `content_type_name` varchar(20) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `body` longtext NOT NULL,
  `metadata` varchar(255) DEFAULT NULL,
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
  `params` longtext,
  `category_name` varchar(255) NOT NULL,
  `favorite` tinyint(1) DEFAULT NULL,
  `urn_source` varchar(255) DEFAULT NULL,
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=241 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `contents_categories`
--

CREATE TABLE IF NOT EXISTS `contents_categories` (
  `pk_fk_content` bigint(20) unsigned NOT NULL,
  `pk_fk_content_category` int(10) unsigned NOT NULL,
  `catName` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_fk_content`,`pk_fk_content_category`),
  KEY `pk_fk_content_category` (`pk_fk_content_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `content_categories`
--

CREATE TABLE IF NOT EXISTS `content_categories` (
  `pk_content_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `inmenu` int(10) DEFAULT '0',
  `posmenu` int(10) DEFAULT '10',
  `internal_category` smallint(1) NOT NULL DEFAULT '0' COMMENT 'equal content_type & global=0 ',
  `fk_content_category` int(10) DEFAULT '0',
  `params` longtext,
  `logo_path` varchar(200) DEFAULT NULL,
  `color` varchar(10) DEFAULT '#638F38',
  PRIMARY KEY (`pk_content_category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `content_positions`
--

CREATE TABLE IF NOT EXISTS `content_positions` (
  `pk_fk_content` bigint(20) NOT NULL,
  `fk_category` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `placeholder` varchar(45) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `params` text CHARACTER SET latin1,
  `content_type` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`pk_fk_content`,`fk_category`,`position`,`placeholder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `frontpages`
--

CREATE TABLE IF NOT EXISTS `frontpages` (
  `pk_frontpage` bigint(20) NOT NULL,
  `date` int(11) NOT NULL COMMENT 'date as 20110720',
  `category` int(11) NOT NULL COMMENT 'category',
  `version` bigint(20) DEFAULT NULL,
  `content_positions` longtext NOT NULL COMMENT 'serialized id of contents',
  `promoted` tinyint(1) DEFAULT NULL,
  `day_frontpage` tinyint(1) DEFAULT NULL,
  `params` longtext NOT NULL COMMENT 'serialized params',
  PRIMARY KEY (`date`,`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `kioskos`
--

CREATE TABLE IF NOT EXISTS `kioskos` (
  `pk_kiosko` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `path` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-item, 1-subscription',
  `price` decimal(10,0) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pk_kiosko`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `letters`
--

CREATE TABLE IF NOT EXISTS `letters` (
  `pk_letter` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_letter`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=229 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `menues`
--

CREATE TABLE IF NOT EXISTS `menues` (
  `pk_menu` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `position` varchar(50) DEFAULT NULL,
  `params` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_menu`),
  UNIQUE KEY `name` (`name`),
  KEY `position` (`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `menu_items`
--

CREATE TABLE IF NOT EXISTS `menu_items` (
  `pk_item` int(11) NOT NULL AUTO_INCREMENT,
  `pk_menu` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `link_name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL COMMENT '''category'',''external'',''static'', internal''',
  `position` int(11) NOT NULL,
  `pk_father` int(11) DEFAULT NULL,
  PRIMARY KEY (`pk_item`,`pk_menu`),
  KEY `pk_item` (`pk_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `newsletter_archive`
--

CREATE TABLE IF NOT EXISTS `newsletter_archive` (
  `pk_newsletter` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `data` longtext,
  `html` longtext,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sent` varchar(255) NOT NULL,
  PRIMARY KEY (`pk_newsletter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `opinions`
--

CREATE TABLE IF NOT EXISTS `opinions` (
  `pk_opinion` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_content_categories` int(10) unsigned DEFAULT '7',
  `fk_author` int(10) DEFAULT NULL,
  `fk_author_img` int(10) DEFAULT NULL,
  `with_comment` smallint(1) DEFAULT '1',
  `type_opinion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`pk_opinion`),
  KEY `type_opinion` (`type_opinion`),
  KEY `fk_author` (`fk_author`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=185 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `content_id` bigint(20) NOT NULL,
  `created` datetime NOT NULL,
  `payment_id` varchar(50) NOT NULL,
  `payment_status` varchar(150) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(200) NOT NULL,
  `type` varchar(50) NOT NULL,
  `params` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `pclave`
--

CREATE TABLE IF NOT EXISTS `pclave` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `pclave` varchar(60) NOT NULL,
  `value` varchar(240) DEFAULT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'intsearch',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `pc_users`
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `photos`
--

CREATE TABLE IF NOT EXISTS `photos` (
  `pk_photo` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `path_file` varchar(150) NOT NULL,
  `size` float DEFAULT NULL,
  `width` int(10) DEFAULT NULL,
  `height` int(10) DEFAULT NULL,
  `nameCat` varchar(250) DEFAULT '1',
  `author_name` varchar(200) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_photo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=215 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `polls`
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

-- --------------------------------------------------------

--
-- Estrutura da táboa `poll_items`
--

CREATE TABLE IF NOT EXISTS `poll_items` (
  `pk_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_pk_poll` bigint(20) unsigned NOT NULL,
  `item` varchar(255) NOT NULL,
  `metadata` varchar(250) DEFAULT NULL,
  `votes` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`pk_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `ratings`
--

CREATE TABLE IF NOT EXISTS `ratings` (
  `pk_rating` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `total_votes` smallint(5) unsigned NOT NULL DEFAULT '0',
  `total_value` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ips_count_rating` longtext,
  PRIMARY KEY (`pk_rating`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=218 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `related_contents`
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

-- --------------------------------------------------------

--
-- Estrutura da táboa `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(128) NOT NULL DEFAULT '',
  `value` longtext NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `specials`
--

CREATE TABLE IF NOT EXISTS `specials` (
  `pk_special` int(10) unsigned NOT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `pdf_path` varchar(250) DEFAULT '0',
  `img1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`pk_special`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `special_contents`
--

CREATE TABLE IF NOT EXISTS `special_contents` (
  `fk_content` varchar(250) NOT NULL,
  `fk_special` int(10) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `position` int(10) DEFAULT '10',
  `visible` smallint(1) DEFAULT '1',
  `type_content` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `static_pages`
--

CREATE TABLE IF NOT EXISTS `static_pages` (
  `pk_static_page` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'BIGINT(20)',
  PRIMARY KEY (`pk_static_page`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=226 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `translation_ids`
--

CREATE TABLE IF NOT EXISTS `translation_ids` (
  `pk_content_old` bigint(10) NOT NULL,
  `pk_content` bigint(10) NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`pk_content_old`,`pk_content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `usermeta`
--

CREATE TABLE IF NOT EXISTS `usermeta` (
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) NOT NULL DEFAULT '',
  `meta_value` longtext,
  PRIMARY KEY (`user_id`,`meta_key`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `sessionexpire` tinyint(2) unsigned NOT NULL DEFAULT '15',
  `url` varchar(255) NOT NULL DEFAULT '',
  `bio` text NOT NULL,
  `avatar_img_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-backend, 1-frontend',
  `deposit` decimal(10,0) NOT NULL DEFAULT '0',
  `token` varchar(50) DEFAULT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 activated - 0 deactivated',
  `fk_user_group` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_username` (`username`),
  KEY `user_email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `users_content_categories`
--

CREATE TABLE IF NOT EXISTS `users_content_categories` (
  `pk_fk_user` int(10) unsigned NOT NULL,
  `pk_fk_content_category` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pk_fk_user`,`pk_fk_content_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `pk_user_group` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`pk_user_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `user_groups_privileges`
--

CREATE TABLE IF NOT EXISTS `user_groups_privileges` (
  `pk_fk_user_group` int(10) unsigned NOT NULL,
  `pk_fk_privilege` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pk_fk_user_group`,`pk_fk_privilege`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da táboa `videos`
--

CREATE TABLE IF NOT EXISTS `videos` (
  `pk_video` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `video_url` varchar(255) NOT NULL,
  `information` text,
  `author_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`pk_video`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=111 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
  `pk_vote` bigint(20) NOT NULL AUTO_INCREMENT,
  `value_pos` smallint(4) NOT NULL DEFAULT '0',
  `value_neg` smallint(4) NOT NULL DEFAULT '0',
  `ips_count_vote` longtext,
  `karma` int(10) unsigned DEFAULT '100',
  PRIMARY KEY (`pk_vote`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=224 ;

-- --------------------------------------------------------

--
-- Estrutura da táboa `widgets`
--

CREATE TABLE IF NOT EXISTS `widgets` (
  `pk_widget` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `renderlet` varchar(50) DEFAULT 'html',
  PRIMARY KEY (`pk_widget`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=241 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
