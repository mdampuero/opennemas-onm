-- MySQL dump 10.13  Distrib 5.5.40, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: c-default
-- ------------------------------------------------------
-- Server version	5.5.40-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `action_counters`
--

DROP TABLE IF EXISTS `action_counters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_counters` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `action_name` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `counter` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `action_name` (`action_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `advertisements`
--

DROP TABLE IF EXISTS `advertisements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `advertisements` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `albums`
--

DROP TABLE IF EXISTS `albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `albums` (
  `pk_album` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subtitle` varchar(250) DEFAULT NULL,
  `agency` varchar(250) DEFAULT NULL,
  `cover_id` bigint(255) DEFAULT NULL,
  PRIMARY KEY (`pk_album`),
  UNIQUE KEY `pk_album` (`pk_album`),
  KEY `pk_album_2` (`pk_album`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `albums_photos`
--

DROP TABLE IF EXISTS `albums_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `albums_photos` (
  `pk_album` bigint(20) unsigned NOT NULL,
  `pk_photo` bigint(20) unsigned NOT NULL,
  `position` int(10) DEFAULT '1',
  `description` varchar(250) DEFAULT NULL,
  KEY `pk_album_2` (`pk_album`,`pk_photo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `articles` (
  `pk_article` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `summary` text,
  `img1` bigint(20) unsigned DEFAULT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `img1_footer` varchar(250) DEFAULT NULL,
  `img2` bigint(20) unsigned DEFAULT NULL,
  `img2_footer` varchar(250) DEFAULT NULL,
  `agency` varchar(100) DEFAULT NULL,
  `fk_video` bigint(20) unsigned DEFAULT NULL,
  `fk_video2` bigint(20) unsigned DEFAULT NULL COMMENT 'video interior',
  `footer_video2` varchar(150) DEFAULT NULL,
  `footer_video1` varchar(150) DEFAULT NULL,
  `title_int` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_article`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attachments`
--

DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attachments` (
  `pk_attachment` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `path` varchar(200) NOT NULL,
  `category` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`pk_attachment`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `books` (
  `pk_book` bigint(20) unsigned NOT NULL,
  `author` varchar(250) DEFAULT NULL,
  `cover_id` bigint(255) DEFAULT NULL,
  `editorial` varchar(250) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `id` bigint(32) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint(32) unsigned NOT NULL DEFAULT '0',
  `author` varchar(200) DEFAULT NULL,
  `author_email` varchar(200) DEFAULT '',
  `author_url` varchar(200) DEFAULT '',
  `author_ip` varchar(100) DEFAULT '',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `body` text,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `agent` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `parent_id` bigint(32) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  `content_type_referenced` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `comment_content_id` (`content_id`),
  KEY `comment_status_date` (`status`,`date`),
  KEY `comment_parent_id` (`parent_id`),
  KEY `comment_date` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `commentsmeta`
--

DROP TABLE IF EXISTS `commentsmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commentsmeta` (
  `fk_content` bigint(32) NOT NULL,
  `meta_name` varchar(255) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`fk_content`,`meta_name`),
  KEY `fk_content` (`fk_content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_categories`
--

DROP TABLE IF EXISTS `content_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_categories` (
  `pk_content_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `inmenu` int(10) DEFAULT '0',
  `posmenu` int(10) DEFAULT '10',
  `internal_category` smallint(1) NOT NULL DEFAULT '0' COMMENT 'equal content_type & global=0 ',
  `fk_content_category` int(10) DEFAULT '0',
  `params` text,
  `logo_path` varchar(200) DEFAULT NULL,
  `color` varchar(10) DEFAULT '#638F38',
  PRIMARY KEY (`pk_content_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_positions`
--

DROP TABLE IF EXISTS `content_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_positions` (
  `pk_fk_content` bigint(20) NOT NULL,
  `fk_category` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `placeholder` varchar(45) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `params` text CHARACTER SET latin1,
  `content_type` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`pk_fk_content`,`fk_category`,`position`,`placeholder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `content_views`
--

DROP TABLE IF EXISTS `content_views`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `content_views` (
  `pk_fk_content` bigint(20) unsigned NOT NULL,
  `views` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`pk_fk_content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contentmeta`
--

DROP TABLE IF EXISTS `contentmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contentmeta` (
  `fk_content` bigint(20) NOT NULL,
  `meta_name` varchar(255) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`fk_content`,`meta_name`),
  KEY `fk_content` (`fk_content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contents`
--

DROP TABLE IF EXISTS `contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contents` (
  `pk_content` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_content_type` int(10) unsigned NOT NULL,
  `content_type_name` varchar(20) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `body` text NOT NULL,
  `metadata` varchar(255) DEFAULT NULL,
  `starttime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `changed` datetime DEFAULT NULL,
  `content_status` int(10) unsigned DEFAULT '0',
  `fk_author` int(10) unsigned DEFAULT NULL COMMENT 'Clave foranea de user',
  `fk_publisher` int(10) unsigned DEFAULT NULL COMMENT 'Clave foranea de user',
  `fk_user_last_editor` int(10) unsigned DEFAULT NULL COMMENT 'Clave foranea de user',
  `position` int(10) unsigned DEFAULT '100',
  `frontpage` tinyint(1) DEFAULT '1',
  `in_litter` tinyint(1) DEFAULT '0' COMMENT '0publicado 1papelera',
  `in_home` smallint(1) DEFAULT '0',
  `home_pos` int(10) DEFAULT '100' COMMENT '10',
  `slug` varchar(255) DEFAULT NULL,
  `available` smallint(1) DEFAULT '1',
  `params` text,
  `category_name` varchar(255) NOT NULL,
  `favorite` tinyint(1) DEFAULT NULL,
  `urn_source` varchar(255) DEFAULT NULL,
  `with_comment` smallint(6) DEFAULT '1',
  PRIMARY KEY (`pk_content`),
  KEY `fk_content_type` (`fk_content_type`),
  KEY `in_litter` (`in_litter`),
  KEY `content_status` (`content_status`),
  KEY `in_home` (`in_home`),
  KEY `frontpage` (`frontpage`),
  KEY `available` (`available`),
  KEY `starttime` (`starttime`,`endtime`),
  KEY `created` (`created`),
  KEY `urn_source` (`urn_source`),
  FULLTEXT KEY `metadata` (`metadata`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contents_categories`
--

DROP TABLE IF EXISTS `contents_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contents_categories` (
  `pk_fk_content` bigint(20) unsigned NOT NULL,
  `pk_fk_content_category` int(10) unsigned NOT NULL,
  `catName` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_fk_content`,`pk_fk_content_category`),
  KEY `pk_fk_content_category` (`pk_fk_content_category`),
  KEY `catName` (`catName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `frontpages`
--

DROP TABLE IF EXISTS `frontpages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `frontpages` (
  `pk_frontpage` bigint(20) NOT NULL,
  `date` int(11) NOT NULL COMMENT 'date as 20110720',
  `category` int(11) NOT NULL COMMENT 'category',
  `version` bigint(20) DEFAULT NULL,
  `content_positions` text NOT NULL COMMENT 'serialized id of contents',
  `promoted` tinyint(1) DEFAULT NULL,
  `day_frontpage` tinyint(1) DEFAULT NULL,
  `params` text NOT NULL COMMENT 'serialized params',
  PRIMARY KEY (`date`,`category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kioskos`
--

DROP TABLE IF EXISTS `kioskos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kioskos` (
  `pk_kiosko` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `path` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-item, 1-subscription',
  `price` decimal(10,0) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pk_kiosko`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `letters`
--

DROP TABLE IF EXISTS `letters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `letters` (
  `pk_letter` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_letter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_items` (
  `pk_item` int(11) NOT NULL AUTO_INCREMENT,
  `pk_menu` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `link_name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL COMMENT '''category'',''external'',''static'', internal''',
  `position` int(11) NOT NULL,
  `pk_father` int(11) DEFAULT NULL,
  PRIMARY KEY (`pk_item`,`pk_menu`),
  KEY `pk_item` (`pk_item`),
  KEY `pk_menu` (`pk_menu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menues`
--

DROP TABLE IF EXISTS `menues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menues` (
  `pk_menu` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `position` varchar(50) DEFAULT NULL,
  `params` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_menu`),
  UNIQUE KEY `name` (`name`),
  KEY `position` (`position`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newsletter_archive`
--

DROP TABLE IF EXISTS `newsletter_archive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newsletter_archive` (
  `pk_newsletter` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `data` text,
  `html` longtext,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` datetime NOT NULL,
  `sent` varchar(255) NOT NULL,
  PRIMARY KEY (`pk_newsletter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `opinions`
--

DROP TABLE IF EXISTS `opinions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `opinions` (
  `pk_opinion` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fk_content_categories` int(10) unsigned DEFAULT '7',
  `fk_author` int(10) DEFAULT NULL,
  `fk_author_img` int(10) DEFAULT NULL,
  `type_opinion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`pk_opinion`),
  KEY `type_opinion` (`type_opinion`),
  KEY `fk_author` (`fk_author`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `content_id` bigint(20) NOT NULL,
  `created` datetime NOT NULL,
  `payment_id` varchar(50) NOT NULL,
  `payment_status` varchar(150) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(200) NOT NULL,
  `type` varchar(50) NOT NULL,
  `params` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pc_users`
--

DROP TABLE IF EXISTS `pc_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pc_users` (
  `pk_pc_user` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `subscription` int(11) NOT NULL,
  PRIMARY KEY (`pk_pc_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pclave`
--

DROP TABLE IF EXISTS `pclave`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pclave` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `pclave` varchar(60) NOT NULL,
  `value` varchar(240) DEFAULT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'intsearch',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `photos`
--

DROP TABLE IF EXISTS `photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `photos` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `poll_items`
--

DROP TABLE IF EXISTS `poll_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poll_items` (
  `pk_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_pk_poll` bigint(20) unsigned NOT NULL,
  `item` varchar(255) NOT NULL,
  `metadata` varchar(250) DEFAULT NULL,
  `votes` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`pk_item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `polls`
--

DROP TABLE IF EXISTS `polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `polls` (
  `pk_poll` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `total_votes` int(11) NOT NULL DEFAULT '0',
  `used_ips` text,
  `subtitle` varchar(150) DEFAULT NULL,
  `visualization` smallint(1) DEFAULT '0',
  PRIMARY KEY (`pk_poll`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ratings` (
  `pk_rating` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `total_votes` smallint(5) unsigned NOT NULL DEFAULT '0',
  `total_value` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ips_count_rating` text,
  PRIMARY KEY (`pk_rating`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `related_contents`
--

DROP TABLE IF EXISTS `related_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `related_contents` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `name` varchar(128) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `special_contents`
--

DROP TABLE IF EXISTS `special_contents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `special_contents` (
  `fk_content` varchar(250) NOT NULL,
  `fk_special` int(10) NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `position` int(10) DEFAULT '10',
  `visible` smallint(1) DEFAULT '1',
  `type_content` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `specials`
--

DROP TABLE IF EXISTS `specials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specials` (
  `pk_special` int(10) unsigned NOT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `pdf_path` varchar(250) DEFAULT '0',
  `img1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`pk_special`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `static_pages`
--

DROP TABLE IF EXISTS `static_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `static_pages` (
  `pk_static_page` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'BIGINT(20)',
  PRIMARY KEY (`pk_static_page`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `translation_ids`
--

DROP TABLE IF EXISTS `translation_ids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translation_ids` (
  `pk_content_old` bigint(10) NOT NULL,
  `pk_content` bigint(10) NOT NULL,
  `type` varchar(20) NOT NULL,
  `slug` varchar(200) DEFAULT '',
  PRIMARY KEY (`pk_content_old`,`pk_content`),
  KEY `slug` (`slug`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_groups`
--

DROP TABLE IF EXISTS `user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_groups` (
  `pk_user_group` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`pk_user_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_groups_privileges`
--

DROP TABLE IF EXISTS `user_groups_privileges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_groups_privileges` (
  `pk_fk_user_group` int(10) unsigned NOT NULL,
  `pk_fk_privilege` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pk_fk_user_group`,`pk_fk_privilege`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usermeta`
--

DROP TABLE IF EXISTS `usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usermeta` (
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) NOT NULL DEFAULT '',
  `meta_value` text,
  PRIMARY KEY (`user_id`,`meta_key`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_content_categories`
--

DROP TABLE IF EXISTS `users_content_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_content_categories` (
  `pk_fk_user` int(10) unsigned NOT NULL,
  `pk_fk_content_category` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pk_fk_user`,`pk_fk_content_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `videos` (
  `pk_video` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `video_url` varchar(255) NOT NULL,
  `information` text,
  `author_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`pk_video`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votes` (
  `pk_vote` bigint(20) NOT NULL AUTO_INCREMENT,
  `value_pos` smallint(4) NOT NULL DEFAULT '0',
  `value_neg` smallint(4) NOT NULL DEFAULT '0',
  `ips_count_vote` text,
  `karma` int(10) unsigned DEFAULT '100',
  PRIMARY KEY (`pk_vote`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `widgets`
--

DROP TABLE IF EXISTS `widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widgets` (
  `pk_widget` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `renderlet` varchar(50) DEFAULT 'html',
  PRIMARY KEY (`pk_widget`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-02-03 15:53:53
