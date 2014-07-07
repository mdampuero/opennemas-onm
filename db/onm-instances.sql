-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 02, 2014 at 01:37 PM
-- Server version: 5.5.30
-- PHP Version: 5.5.7-1+sury.org~precise+1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `instance`
--

-- --------------------------------------------------------

--
-- Table structure for table `instances`
--

CREATE TABLE IF NOT EXISTS `instances` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `domains` text NOT NULL,
  `main_domain` varchar(255) not null,
  `settings` text,
  `activated` tinyint(1) NOT NULL,
  `contact_mail` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `domain_name` (`domains`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `instances`
--

INSERT INTO `instances` (`id`, `internal_name`, `name`, `domains`, `settings`, `activated`, `contact_mail`) VALUES
(1, 'opennemas', 'Opennemas Default instance', 'opennemas.onm', 'a:7:{s:13:"TEMPLATE_USER";s:5:"admin";s:9:"MEDIA_URL";s:0:"";s:7:"BD_TYPE";s:6:"mysqli";s:7:"BD_HOST";s:9:"localhost";s:11:"BD_DATABASE";s:9:"c-default";s:7:"BD_USER";s:4:"root";s:7:"BD_PASS";s:4:"root";}', 1, 'devs@opennemas.com');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(128) NOT NULL DEFAULT '',
  `value` longtext NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`name`, `value`) VALUES
('time_zone', 's:3:"334";'),
('site_language', 's:5:"es_ES";'),
('log_level', 's:6:"normal";'),
('log_enabled', 's:2:"on";'),
('log_db_enabled', 's:2:"on";');

-- --------------------------------------------------------

--
-- Table structure for table `usermeta`
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
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `sessionexpire` tinyint(2) unsigned NOT NULL DEFAULT '15',
  `url` varchar(255) DEFAULT '',
  `bio` text,
  `avatar_img_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-backend, 1-frontend',
  `deposit` decimal(10,0) NOT NULL DEFAULT '0',
  `token` varchar(50) DEFAULT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 activated - 0 deactivated',
  `fk_user_group` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `sessionexpire`, `url`, `bio`, `avatar_img_id`, `email`, `name`, `type`, `deposit`, `token`, `activated`, `fk_user_group`) VALUES
(3, 'macada', '2f575705daf41049194613e47027200b', 30, '', NULL, 0, 'david.martinez@openhost.es', 'David Martinez', 0, 0, NULL, 1, '4'),
(5, 'fran', '6d87cd9493f11b830bbfdf628c2c4f08', 65, '', NULL, 0, 'fran@openhost.es', 'Francisco Dieguez', 0, 0, NULL, 1, '4'),
(4, 'alex', '4c246829b53bc5712d52ee777c52ebe7', 60, '', NULL, 0, 'alex@openhost.es', 'Alexandre Rico', 0, 0, NULL, 1, '4'),
(7, 'sandra', 'bd80e7c35b56dccd2d1796cf39cd05f6', 99, '', NULL, 0, 'sandra@openhost.es', 'Sandra Pereira', 0, 0, NULL, 1, '4');

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `pk_user_group` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`pk_user_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`pk_user_group`, `name`) VALUES
(4, 'Masters');

-- --------------------------------------------------------

--
-- Table structure for table `user_groups_privileges`
--

CREATE TABLE IF NOT EXISTS `user_groups_privileges` (
  `pk_fk_user_group` int(10) unsigned NOT NULL,
  `pk_fk_privilege` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pk_fk_user_group`,`pk_fk_privilege`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
