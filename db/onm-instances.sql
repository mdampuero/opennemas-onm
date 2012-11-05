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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;