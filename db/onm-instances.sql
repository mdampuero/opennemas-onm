-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Xerado en: 02 de Out de 2012 ás 15:37
-- Versión do servidor: 5.5.24
-- Versión do PHP: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `onm-instances`
--

-- --------------------------------------------------------

--
-- Estrutura da táboa `instances`
--

CREATE TABLE IF NOT EXISTS `instances` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `domains` text NOT NULL,
  `settings` text,
  `activated` tinyint(1) NOT NULL,
  `contact_mail` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `domain_name` (`domains`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- A extraer datos da táboa `instances`
--

INSERT INTO `instances` (`internal_name`, `name`, `domains`, `settings`, `activated`, `contact_mail`) VALUES
('opennemas', 'Opennemas Default instance', 'opennemas.onm', 'a:7:{s:13:"TEMPLATE_USER";s:5:"admin";s:9:"MEDIA_URL";s:0:"";s:7:"BD_TYPE";s:6:"mysqli";s:7:"BD_HOST";s:9:"localhost";s:11:"BD_DATABASE";s:9:"c-default";s:7:"BD_USER";s:4:"root";s:7:"BD_PASS";s:4:"root";}', 1, 'devs@opennemas.com');
-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Xerado en: 02 de Out de 2012 ás 15:37
-- Versión do servidor: 5.5.24
-- Versión do PHP: 5.3.10-1ubuntu3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `onm-instances`
--

-- --------------------------------------------------------

--
-- Estrutura da táboa `instances`
--

CREATE TABLE IF NOT EXISTS `instances` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `domains` text NOT NULL,
  `settings` text,
  `activated` tinyint(1) NOT NULL,
  `contact_mail` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `domain_name` (`domains`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- A extraer datos da táboa `instances`
--

INSERT INTO `instances` (`internal_name`, `name`, `domains`, `settings`, `activated`, `contact_mail`) VALUES
('opennemas', 'Opennemas Default instance', 'opennemas.onm', 'a:7:{s:13:"TEMPLATE_USER";s:5:"admin";s:9:"MEDIA_URL";s:0:"";s:7:"BD_TYPE";s:6:"mysqli";s:7:"BD_HOST";s:9:"localhost";s:11:"BD_DATABASE";s:9:"c-default";s:7:"BD_USER";s:4:"root";s:7:"BD_PASS";s:4:"root";}', 1, 'devs@opennemas.com');


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(128) NOT NULL DEFAULT '',
  `value` longtext NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `settings`
--

INSERT INTO `settings` (`name`, `value`) VALUES
('time_zone', 's:3:"334";'),
('site_language', 's:5:"es_ES";'),
('log_level', 's:6:"normal";'),
('log_enabled', 's:2:"on";'),
('log_db_enabled', 's:2:"on";');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `pk_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(100) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `sessionexpire` tinyint(2) unsigned NOT NULL DEFAULT '15',
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-backend, 1-frontend',
  `deposit` decimal(10,0) NOT NULL DEFAULT '0',
  `token` varchar(50) DEFAULT NULL,
  `authorize` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 authorized - 0 unauthorized',
  `fk_user_group` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`pk_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`pk_user`, `login`, `password`, `sessionexpire`, `email`, `name`, `type`, `deposit`, `token`, `authorize`, `fk_user_group`) VALUES
(3, 'macada', '2f575705daf41049194613e47027200b', 30, 'david.martinez@openhost.es', 'David Martinez', 0, '0', NULL, 1, 4),
(5, 'fran', '6d87cd9493f11b830bbfdf628c2c4f08', 65, 'fran@openhost.es', 'Francisco Dieguez', 0, '0', NULL, 1, 4),
(4, 'alex', '4c246829b53bc5712d52ee777c52ebe7', 60, 'alex@openhost.es', 'Alexandre Rico', 0, '0', NULL, 1, 4),
(7, 'sandra', 'bd80e7c35b56dccd2d1796cf39cd05f6', 99, 'sandra@openhost.es', 'Sandra Pereira', 0, '0', NULL, 1, 4),
(8, 'toni', 'aefe34008e63f1eb205dc4c4b8322253', 15, 'toni@openhost.es', 'Toni Martinez', 0, 0, NULL, 1, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `pk_user_group` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`pk_user_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `user_groups`
--

INSERT INTO `user_groups` (`pk_user_group`, `name`) VALUES
(4, 'Masters');


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(128) NOT NULL DEFAULT '',
  `value` longtext NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `settings`
--

INSERT INTO `settings` (`name`, `value`) VALUES
('time_zone', 's:3:"334";'),
('site_language', 's:5:"es_ES";'),
('log_level', 's:6:"normal";'),
('log_enabled', 's:2:"on";'),
('log_db_enabled', 's:2:"on";');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `pk_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(100) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `sessionexpire` tinyint(2) unsigned NOT NULL DEFAULT '15',
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-backend, 1-frontend',
  `deposit` decimal(10,0) NOT NULL DEFAULT '0',
  `token` varchar(50) DEFAULT NULL,
  `authorize` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 authorized - 0 unauthorized',
  `fk_user_group` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`pk_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`pk_user`, `login`, `password`, `sessionexpire`, `email`, `name`, `type`, `deposit`, `token`, `authorize`, `fk_user_group`) VALUES
(3, 'macada', '2f575705daf41049194613e47027200b', 30, 'david.martinez@openhost.es', 'David Martinez', 0, '0', NULL, 1, 4),
(5, 'fran', '6d87cd9493f11b830bbfdf628c2c4f08', 65, 'fran@openhost.es', 'Francisco Dieguez', 0, '0', NULL, 1, 4),
(4, 'alex', '4c246829b53bc5712d52ee777c52ebe7', 60, 'alex@openhost.es', 'Alexandre Rico', 0, '0', NULL, 1, 4),
(7, 'sandra', 'bd80e7c35b56dccd2d1796cf39cd05f6', 99, 'sandra@openhost.es', 'Sandra Pereira', 0, '0', NULL, 1, 4),
(8, 'toni', 'aefe34008e63f1eb205dc4c4b8322253', 15, 'toni@openhost.es', 'Toni Martinez', 0, 0, NULL, 1, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `pk_user_group` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`pk_user_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `user_groups`
--

INSERT INTO `user_groups` (`pk_user_group`, `name`) VALUES
(4, 'Masters');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
