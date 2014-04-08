-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Xerado en: 14 de Out de 2013 ás 16:39
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

--
-- A extraer datos da táboa `advertisements`
--

INSERT INTO `advertisements` (`pk_advertisement`, `type_advertisement`, `fk_content_categories`, `path`, `url`, `type_medida`, `num_clic`, `num_clic_count`, `num_view`, `with_script`, `script`, `overlap`, `timeout`) VALUES
(128, 2, '0,25,27,29,28,24,26,23,22', '126', 'http://www.opennemas.com', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(129, 50, '0', '124', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(130, 5, '0,25,27,29,28,24,26,23,22', '123', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 1268, 0, 1, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPjwhLS0NCmdvb2dsZV9hZF9jbGllbnQgPSAiY2EtcHViLTc2OTQwNzM5ODM4MTYyMDQiOw0KLyogT25tLTcyOHg5MCBMZWFkZXJib2FyZCAqLw0KZ29vZ2xlX2FkX3Nsb3QgPSAiMjcyMTc3NTA3NyI7DQpnb29nbGVfYWRfd2lkdGggPSA3Mjg7DQpnb29nbGVfYWRfaGVpZ2h0ID0gOTA7DQovLy0tPg0KPC9zY3JpcHQ+DQo8c2NyaXB0IHR5cGU9InRleHQvamF2YXNjcmlwdCINCnNyYz0iaHR0cDovL3BhZ2VhZDIuZ29vZ2xlc3luZGljYXRpb24uY29tL3BhZ2VhZC9zaG93X2Fkcy5qcyI+DQo8L3NjcmlwdD4=', 0, 4),
(239, 193, '0,25,27,29,28,24,26,23,22', '', 'http://www.opennemas.com', 'NULL', 0, 18, 0, 1, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPjwhLS0NCmdvb2dsZV9hZF9jbGllbnQgPSAiY2EtcHViLTc2OTQwNzM5ODM4MTYyMDQiOw0KLyogT25tLTEyMHg2MDAtU2t5Y3JhcGVyICovDQpnb29nbGVfYWRfc2xvdCA9ICIyNDA3NDA1Njk2IjsNCmdvb2dsZV9hZF93aWR0aCA9IDEyMDsNCmdvb2dsZV9hZF9oZWlnaHQgPSA2MDA7DQovLy0tPg0KPC9zY3JpcHQ+DQo8c2NyaXB0IHR5cGU9InRleHQvamF2YXNjcmlwdCINCnNyYz0iaHR0cDovL3BhZ2VhZDIuZ29vZ2xlc3luZGljYXRpb24uY29tL3BhZ2VhZC9zaG93X2Fkcy5qcyI+DQo8L3NjcmlwdD4=', 0, 4),
(131, 6, '0,25,27,29,28,24,26,23,22', '117', 'http://www.opennemas.com', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(132, 32, '0,25,27,29,28,24,26,23,22', '116', 'http://www.opennemas.com', 'NULL', 0, 2, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPg0KdmFyIHVyaSA9ICdodHRwOi8vaW1wZXMudHJhZGVkb3VibGVyLmNvbS9pbXA/dHlwZShpbWcpZygxOTY2MTE0NClhKDIwMjMzMzApJyArIG5ldyBTdHJpbmcgKE1hdGgucmFuZG9tKCkpLnN1YnN0cmluZyAoMiwgMTEpOw0KZG9jdW1lbnQud3JpdGUoJzxhIGhyZWY9Imh0dHA6Ly9jbGsudHJhZGVkb3VibGVyLmNvbS9jbGljaz9wPTgwODIwJmE9MjAyMzMzMCZnPTE5NjYxMTQ0IiB0YXJnZXQ9Il9CTEFOSyI+PGltZyBzcmM9IicrdXJpKyciIGJvcmRlcj0wPjwvYT4nKTsNCjwvc2NyaXB0Pg==', 0, 4),
(133, 3, '0,25,27,29,28,24,26,23,22', '115', 'http://www.opennemas.com', 'NULL', 0, 1, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(134, 110, '0,25,27,29,28,24,26,23,22', '126', 'http://www.opennemas.com', 'NULL', 0, 1, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(135, 4, '0,25,27,29,28,24,26,23,22', '126', 'http://www.opennemas.com', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(136, 31, '0,25,27,28,24,26,23,22', '119', 'http://www.opennemas.com', 'NULL', 0, 863, 0, 1, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPjwhLS0NCmdvb2dsZV9hZF9jbGllbnQgPSAiY2EtcHViLTc2OTQwNzM5ODM4MTYyMDQiOw0KLyogT25tLTMwMHgyNTAgLSBNZWRpdW0gUmVjdGFuZ2xlIC0gIzEgKi8NCmdvb2dsZV9hZF9zbG90ID0gIjkwNTUwMDYyNzAiOw0KZ29vZ2xlX2FkX3dpZHRoID0gMzAwOw0KZ29vZ2xlX2FkX2hlaWdodCA9IDI1MDsNCi8vLS0+DQo8L3NjcmlwdD4NCjxzY3JpcHQgdHlwZT0idGV4dC9qYXZhc2NyaXB0Ig0Kc3JjPSJodHRwOi8vcGFnZWFkMi5nb29nbGVzeW5kaWNhdGlvbi5jb20vcGFnZWFkL3Nob3dfYWRzLmpzIj4NCjwvc2NyaXB0Pg==', 0, 4),
(238, 703, '4', '', 'http://www.opennemas.com', 'NULL', 0, 8, 0, 1, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPjwhLS0NCmdvb2dsZV9hZF9jbGllbnQgPSAiY2EtcHViLTc2OTQwNzM5ODM4MTYyMDQiOw0KLyogT25tLTMwMHgyNTAgLSBNZWRpdW0gUmVjdGFuZ2xlIC0gIzEgKi8NCmdvb2dsZV9hZF9zbG90ID0gIjkwNTUwMDYyNzAiOw0KZ29vZ2xlX2FkX3dpZHRoID0gMzAwOw0KZ29vZ2xlX2FkX2hlaWdodCA9IDI1MDsNCi8vLS0+DQo8L3NjcmlwdD4NCjxzY3JpcHQgdHlwZT0idGV4dC9qYXZhc2NyaXB0Ig0Kc3JjPSJodHRwOi8vcGFnZWFkMi5nb29nbGVzeW5kaWNhdGlvbi5jb20vcGFnZWFkL3Nob3dfYWRzLmpzIj4NCjwvc2NyaXB0Pg==', 0, 4),
(137, 101, '0,25,27,29,28,24,26,23,22', '115', 'http://www.retrincos.info', 'NULL', 0, 104, 0, 1, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPjwhLS0NCmdvb2dsZV9hZF9jbGllbnQgPSAiY2EtcHViLTc2OTQwNzM5ODM4MTYyMDQiOw0KLyogT25tLTcyOHg5MCBMZWFkZXJib2FyZCAqLw0KZ29vZ2xlX2FkX3Nsb3QgPSAiMjcyMTc3NTA3NyI7DQpnb29nbGVfYWRfd2lkdGggPSA3Mjg7DQpnb29nbGVfYWRfaGVpZ2h0ID0gOTA7DQovLy0tPg0KPC9zY3JpcHQ+DQo8c2NyaXB0IHR5cGU9InRleHQvamF2YXNjcmlwdCINCnNyYz0iaHR0cDovL3BhZ2VhZDIuZ29vZ2xlc3luZGljYXRpb24uY29tL3BhZ2VhZC9zaG93X2Fkcy5qcyI+DQo8L3NjcmlwdD4=', 0, 4),
(138, 102, '0,25,27,29,28,24,26,23,22', '117', 'http://www.opennemas.com', 'NULL', 0, 4, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPg0KdmFyIHVyaSA9ICdodHRwOi8vaW1wZXMudHJhZGVkb3VibGVyLmNvbS9pbXA/dHlwZShpbWcpZygxOTYyMzYzNilhKDIwMjMzMzApJyArIG5ldyBTdHJpbmcgKE1hdGgucmFuZG9tKCkpLnN1YnN0cmluZyAoMiwgMTEpOw0KZG9jdW1lbnQud3JpdGUoJzxhIGhyZWY9Imh0dHA6Ly9jbGsudHJhZGVkb3VibGVyLmNvbS9jbGljaz9wPTgwODIwJmE9MjAyMzMzMCZnPTE5NjIzNjM2IiB0YXJnZXQ9Il9CTEFOSyI+PGltZyBzcmM9IicrdXJpKyciIGJvcmRlcj0wPjwvYT4nKTsNCjwvc2NyaXB0Pg==', 0, 4),
(139, 104, '0,25,27,28,24,26,23,22', '123', 'http://retrincos.info', 'NULL', 0, 104, 0, 1, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPjwhLS0NCmdvb2dsZV9hZF9jbGllbnQgPSAiY2EtcHViLTc2OTQwNzM5ODM4MTYyMDQiOw0KLyogT25tLTQ2OHg2MC1yb2JhcMOhZ2luYSAqLw0KZ29vZ2xlX2FkX3Nsb3QgPSAiNzc1NTkzNTI5NCI7DQpnb29nbGVfYWRfd2lkdGggPSA0Njg7DQpnb29nbGVfYWRfaGVpZ2h0ID0gNjA7DQovLy0tPg0KPC9zY3JpcHQ+DQo8c2NyaXB0IHR5cGU9InRleHQvamF2YXNjcmlwdCINCnNyYz0iaHR0cDovL3BhZ2VhZDIuZ29vZ2xlc3luZGljYXRpb24uY29tL3BhZ2VhZC9zaG93X2Fkcy5qcyI+DQo8L3NjcmlwdD4=', 0, 4),
(141, 601, '4', '115', 'http://www.opennemas.com', 'NULL', 0, 9, 0, 1, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPjwhLS0NCmdvb2dsZV9hZF9jbGllbnQgPSAiY2EtcHViLTc2OTQwNzM5ODM4MTYyMDQiOw0KLyogT25tLTcyOHg5MCBMZWFkZXJib2FyZCAqLw0KZ29vZ2xlX2FkX3Nsb3QgPSAiMjcyMTc3NTA3NyI7DQpnb29nbGVfYWRfd2lkdGggPSA3Mjg7DQpnb29nbGVfYWRfaGVpZ2h0ID0gOTA7DQovLy0tPg0KPC9zY3JpcHQ+DQo8c2NyaXB0IHR5cGU9InRleHQvamF2YXNjcmlwdCINCnNyYz0iaHR0cDovL3BhZ2VhZDIuZ29vZ2xlc3luZGljYXRpb24uY29tL3BhZ2VhZC9zaG93X2Fkcy5qcyI+DQo8L3NjcmlwdD4=', 0, 4),
(143, 602, '4', '126', 'http://www.opennemas.com', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(144, 605, '4', '116', 'http://www.opennemas.com', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(145, 609, '4', '115', 'http://www.opennemas.com', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(146, 610, '4', '126', 'http://www.opennemas.com', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(147, 702, '4', '126', 'http://www.opennemas.com', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(148, 701, '4', '115', 'http://www.opennemas.com', 'NULL', 0, 5, 0, 1, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPjwhLS0NCmdvb2dsZV9hZF9jbGllbnQgPSAiY2EtcHViLTc2OTQwNzM5ODM4MTYyMDQiOw0KLyogT25tLTcyOHg5MCBMZWFkZXJib2FyZCAqLw0KZ29vZ2xlX2FkX3Nsb3QgPSAiMjcyMTc3NTA3NyI7DQpnb29nbGVfYWRfd2lkdGggPSA3Mjg7DQpnb29nbGVfYWRfaGVpZ2h0ID0gOTA7DQovLy0tPg0KPC9zY3JpcHQ+DQo8c2NyaXB0IHR5cGU9InRleHQvamF2YXNjcmlwdCINCnNyYz0iaHR0cDovL3BhZ2VhZDIuZ29vZ2xlc3luZGljYXRpb24uY29tL3BhZ2VhZC9zaG93X2Fkcy5qcyI+DQo8L3NjcmlwdD4=', 0, 4),
(149, 603, '4', '119', 'http://www.opennemas.com', 'NULL', 0, 4, 0, 1, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPjwhLS0NCmdvb2dsZV9hZF9jbGllbnQgPSAiY2EtcHViLTc2OTQwNzM5ODM4MTYyMDQiOw0KLyogT25tLTMwMHgyNTAgLSBNZWRpdW0gUmVjdGFuZ2xlIC0gIzEgKi8NCmdvb2dsZV9hZF9zbG90ID0gIjkwNTUwMDYyNzAiOw0KZ29vZ2xlX2FkX3dpZHRoID0gMzAwOw0KZ29vZ2xlX2FkX2hlaWdodCA9IDI1MDsNCi8vLS0+DQo8L3NjcmlwdD4NCjxzY3JpcHQgdHlwZT0idGV4dC9qYXZhc2NyaXB0Ig0Kc3JjPSJodHRwOi8vcGFnZWFkMi5nb29nbGVzeW5kaWNhdGlvbi5jb20vcGFnZWFkL3Nob3dfYWRzLmpzIj4NCjwvc2NyaXB0Pg==', 0, 4),
(156, 109, '0,25,27,29,28,24,26,23,22', '115', 'http://www.opennemas.com', 'NULL', 0, 21, 0, 1, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPjwhLS0NCmdvb2dsZV9hZF9jbGllbnQgPSAiY2EtcHViLTc2OTQwNzM5ODM4MTYyMDQiOw0KLyogT25tLTcyOHg5MCBMZWFkZXJib2FyZCAqLw0KZ29vZ2xlX2FkX3Nsb3QgPSAiMjcyMTc3NTA3NyI7DQpnb29nbGVfYWRfd2lkdGggPSA3Mjg7DQpnb29nbGVfYWRfaGVpZ2h0ID0gOTA7DQovLy0tPg0KPC9zY3JpcHQ+DQo8c2NyaXB0IHR5cGU9InRleHQvamF2YXNjcmlwdCINCnNyYz0iaHR0cDovL3BhZ2VhZDIuZ29vZ2xlc3luZGljYXRpb24uY29tL3BhZ2VhZC9zaG93X2Fkcy5qcyI+DQo8L3NjcmlwdD4=', 0, 4),
(157, 1, '0,25,27,28,24,26,23,22', '123', 'http://www.openhost.es/es/opennemas', 'NULL', 0, 894, 0, 1, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPjwhLS0NCmdvb2dsZV9hZF9jbGllbnQgPSAiY2EtcHViLTc2OTQwNzM5ODM4MTYyMDQiOw0KLyogT25tLTcyOHg5MCBMZWFkZXJib2FyZCAqLw0KZ29vZ2xlX2FkX3Nsb3QgPSAiMjcyMTc3NTA3NyI7DQpnb29nbGVfYWRfd2lkdGggPSA3Mjg7DQpnb29nbGVfYWRfaGVpZ2h0ID0gOTA7DQovLy0tPg0KPC9zY3JpcHQ+DQo8c2NyaXB0IHR5cGU9InRleHQvamF2YXNjcmlwdCINCnNyYz0iaHR0cDovL3BhZ2VhZDIuZ29vZ2xlc3luZGljYXRpb24uY29tL3BhZ2VhZC9zaG93X2Fkcy5qcyI+DQo8L3NjcmlwdD4=', 0, 4),
(159, 705, '4', '119', 'http://www.opennemas.com', 'NULL', 0, 0, 0, 0, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPi8qIEPDs2RpZ28gamF2YXNjcmlwdCAqLzwvc2NyaXB0Pg==', 0, 4),
(224, 103, '0,25,27,29,28,24,26,23,22', '116', 'http://openhost.es/opennemas', 'NULL', 0, 19, 0, 1, 'PHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiPjwhLS0NCmdvb2dsZV9hZF9jbGllbnQgPSAiY2EtcHViLTc2OTQwNzM5ODM4MTYyMDQiOw0KLyogSWRlYWxHYWxsZWdvIC0gMzAweDI1MCBNZWRpdW0gUmVjdGFuZ2xlIC0gIzEgKi8NCmdvb2dsZV9hZF9zbG90ID0gIjcyNjQyNjk1MjEiOw0KZ29vZ2xlX2FkX3dpZHRoID0gMzAwOw0KZ29vZ2xlX2FkX2hlaWdodCA9IDI1MDsNCi8vLS0+DQo8L3NjcmlwdD4NCjxzY3JpcHQgdHlwZT0idGV4dC9qYXZhc2NyaXB0Ig0Kc3JjPSJodHRwOi8vcGFnZWFkMi5nb29nbGVzeW5kaWNhdGlvbi5jb20vcGFnZWFkL3Nob3dfYWRzLmpzIj4NCjwvc2NyaXB0Pg==', 0, 4);

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

--
-- A extraer datos da táboa `albums`
--

INSERT INTO `albums` (`pk_album`, `subtitle`, `agency`, `cover_id`) VALUES
(89, '0', 'onm agency', 3),
(90, '0', 'onm agency', 7),
(91, '0', 'onm agency', 15),
(92, '0', 'onm-agency', 29),
(93, '0', 'onm agency', 30),
(94, '0', 'onm agency', 16),
(181, '0', 'onm agency', 180);

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

--
-- A extraer datos da táboa `albums_photos`
--

INSERT INTO `albums_photos` (`pk_album`, `pk_photo`, `position`, `description`) VALUES
(89, 3, 5, ''),
(90, 37, 1, ''),
(93, 27, 5, 'perro, deporte, balÃ³n'),
(91, 41, 4, 'verde, clorofila'),
(91, 43, 2, 'aire, viento, '),
(91, 42, 1, 'gris, bola'),
(91, 18, 3, ''),
(92, 27, 7, 'perro, deporte, balÃ³n'),
(92, 26, 6, 'perro, gafas, sol'),
(92, 28, 5, 'zorro, dormir'),
(92, 24, 4, 'uricatos, arena, beige'),
(92, 23, 3, ''),
(93, 30, 3, ''),
(93, 26, 4, 'perro, gafas, sol'),
(93, 28, 1, 'zorro, dormir'),
(93, 29, 2, ''),
(92, 22, 8, ''),
(92, 30, 2, ''),
(92, 29, 1, ''),
(89, 4, 4, ''),
(89, 36, 3, ''),
(89, 35, 2, ''),
(89, 34, 1, ''),
(90, 38, 2, ''),
(90, 7, 3, ''),
(94, 17, 5, ''),
(94, 31, 2, 'pato, agua'),
(94, 25, 1, 'rojo, mariquitas'),
(181, 171, 3, 'pin-up art'),
(181, 173, 4, 'pin-up art'),
(181, 180, 5, 'pin-up art'),
(181, 179, 6, 'pin-up art'),
(181, 177, 7, 'pin-up art'),
(181, 172, 1, 'pin-up art'),
(181, 174, 2, 'pin-up art'),
(181, 178, 8, 'pin-up art'),
(181, 175, 9, 'pin-up art'),
(94, 16, 3, ''),
(93, 22, 6, ''),
(93, 23, 7, ''),
(93, 24, 8, 'uricatos, arena, beige'),
(91, 20, 5, ''),
(91, 15, 6, ''),
(89, 5, 6, ''),
(90, 33, 4, ''),
(94, 22, 4, ''),
(181, 176, 10, 'pin-up art');

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

--
-- A extraer datos da táboa `articles`
--

INSERT INTO `articles` (`pk_article`, `summary`, `img1`, `subtitle`, `img1_footer`, `img2`, `img2_footer`, `agency`, `fk_video`, `with_comment`, `fk_video2`, `footer_video2`, `footer_video1`, `title_int`) VALUES
(10, '<p>Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n', 41, 'ECONOMIA', 'verde, clorofila', 0, '', 'EFE', 0, 1, 0, '', NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet.'),
(11, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.', 7, 'ECONOMIA', '', 0, '', 'opennemas', 0, 1, 0, '', NULL, 'Morbi venenatis laoreet justo, nec vestibulum mi sodales sit amet'),
(12, '<p>Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>', 25, 'ECONOMIA', 'rojo, mariquitas', 0, '', 'EFE', 0, 1, 0, '', NULL, 'Cras metus dui, elementum id convallis vitae, feugiat nec nulla.'),
(13, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci.</p>\r\n', 170, 'ECONOMIA', 'luz en la ciudad', 0, '', 'EFE', 0, 1, 0, '', NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(14, '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa.&nbsp;</p>', 168, 'ECONOMIA', 'luces naranjas', 170, 'luz en la ciudad', 'EFE', 0, 1, 0, '', NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...'),
(46, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis.', 41, 'ECONOMIA', 'verde, clorofila', 31, 'pato, agua', 'EFE', 0, 1, NULL, NULL, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(47, '<p>Nam lobortis leo ut quam suscipit malesuada accumsan massa posuere. Aliquam non ante nec sem convallis rutrum in in purus. Nam id magna ante. Vestibulum egestas ultrices ultricies. Morbi luctus ante eu metus dapibus a rhoncus felis blandit. Quisque vitae nulla quis lacus gravida feugiat. Phasellus consequat est aliquet lectus blandit a consequat purus viverra. Vestibulum a neque arcu.</p>\r\n', 169, 'ECONOMIA', 'luces de colores', 0, '', 'opennemas', 0, 1, 0, '', NULL, ' Pellentesque vehicula tortor venenatis eros ultricies iaculis quis aliquet nunc.'),
(48, 'Sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci.&nbsp;', 40, 'ECONOMIA', 'ipad, tablet', 44, 'mac, ipad, portatil', 'opennemas', 0, 1, 0, '', NULL, 'Maecenas vitae nisi dui, nec commodo magna.'),
(49, 'Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.', 0, 'ECONOMIA', '', 0, '', 'EFE', 0, 1, 0, '', NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(50, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.', 23, 'ECONOMIA', '', 31, 'pato, agua', 'EFE', 0, 1, 0, '', NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...'),
(51, '<div id="lipsum">Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.</div>', 33, 'ECONOMIA', '', 36, '', 'EFE', 0, 1, NULL, NULL, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(52, 'Nam  lobortis nibh eu ante molestie nec condimentum justo semper.  Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet,  pharetra  eget libero. Donec ultricies tincidunt ultrices.', 0, 'ECONOMIA', '', 0, '', 'EFE', 0, 1, NULL, NULL, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...'),
(53, 'Nam  lobortis nibh eu ante molestie nec condimentum justo semper.  Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet,  pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum  porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem  velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus  accumsan sem  ipsum.', 0, 'ECONOMIA', '', 0, '', 'EFE', 0, 1, 0, '', NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(61, '<div id="lipsum">Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna.</div>', 22, 'LOREN IPSUM', '', 0, '', 'opennemas.com', 0, 0, 0, '', NULL, ' Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing.'),
(62, 'Curabitur in est ipsum, et pulvinar diam. In hac habitasse platea dictumst. Praesent tincidunt tincidunt tortor, in consectetur nulla tristique vitae. Pellentesque quis est in neque lobortis consectetur. Quisque vestibulum eros nec libero aliquam consectetur. Morbi quis nisl nunc, sed vulputate lorem.', 0, 'DEPORTES', '', 0, '', 'opennemas', 0, 1, 0, '', NULL, 'Curabitur in est ipsum, et pulvinar diam.'),
(63, '', 0, 'CULTURA', '', 0, '', 'EFE', 0, 1, 0, '', NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...'),
(64, 'Praesent eros turpis, cursus placerat cursus id, aliquam sit amet urna. Etiam non nibh in nisl aliquam fermentum eu a eros. Nulla mi odio, sodales vel porta ut, porttitor vulputate dui.', 0, 'DEPORTES', '', 168, 'luces naranjas', 'EFE', 0, 1, 0, '', NULL, 'Nulla mi odio, sodales vel porta ut, porttitor vulputate dui.'),
(65, '', 0, 'CULTURA', '', 0, '', 'EFE', 0, 1, 0, '', NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...'),
(66, '', 0, 'CULTURA', '', 0, '', 'EFE', 0, 1, 0, '', NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(67, 'Phasellus blandit, eros vitae dictum fringilla, augue augue cursus metus, id auctor nisi ligula id tellus. Phasellus nunc mauris, molestie vel gravida nec, condimentum quis turpis.&nbsp;', 42, 'PHASELLUS NUNC MAURIS, MOLESTIE VEL GRAVIDA NEC, CONDIMENTUM QUIS TURPIS.', 'gris, bola', 42, 'gris, bola', 'opennemas.com', 0, 1, NULL, NULL, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(68, 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,   vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend   bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,   congue id ante. Donec non sapien purus', 0, 'SOCIEDAD', '', 170, 'luz en la ciudad', 'opennemas', 0, 1, 0, '', NULL, 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna, vel ullamcorper dui lacus ut urna'),
(69, 'Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla.&nbsp;', 0, 'DEPORTES', '', 0, '', 'EFE', 0, 1, NULL, NULL, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(70, '', 0, 'DEPORTES', '', 0, '', 'EFE', 0, 1, 0, '', NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(71, '<p>Cras blandit condimentum facilisis. Ut quam nibh, convallis non iaculis pharetra, mattis non odio. Phasellus ipsum metus, viverra in imperdiet et, dictum sit amet libero. Cras vel erat purus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;</p>\r\n', 0, 'UT QUAM NIBH, CONVALLIS NON IACULIS PHARETRA, MATTIS NON ODIO. ', '', 23, '', 'EFE', 0, 1, 0, '', NULL, 'Consectetur, adipisci velit'),
(72, '<p>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin id nisl nisi. Duis eget nisl ipsum, ut rhoncus erat. Pellentesque felis tellus, aliquet iaculis convallis eget, imperdiet ac risus. Phasellus eu enim lacus, id porta velit.</p>', 0, 'POLITICA', '', 0, '', 'EFE', 0, 1, NULL, NULL, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(73, 'Curabitur vel diam mauris. Praesent pharetra volutpat molestie. Nunc eget elit placerat urna ultricies tincidunt. Mauris et nibh elementum velit lobortis mollis eget eu odio. Sed convallis leo ut odio euismod vestibulum. Morbi aliquam hendrerit interdum. Nam eu felis ornare nisi condimentum accumsan. Curabitur bibendum accumsan varius. Praesent blandit, magna eu condimentum sollicitudin, odio eros gravida diam, posuere hendrerit mauris erat nec ligula. Sed quis eros quis urna cursus rutrum in tempus eros.', 7, 'PRAESENT PHARETRA VOLUTPAT MOLESTIE.', '', 0, '', 'EFE', 0, 1, NULL, NULL, NULL, 'Nunc eget elit placerat urna ultricies tincidunt.'),
(74, 'Aliquam varius tincidunt laoreet. Vivamus odio neque, vestibulum in posuere a, malesuada vitae leo. Aliquam id est ante. Nunc urna purus, interdum ut sollicitudin sed, adipiscing vitae lorem. Mauris porta quam quis tortor fermentum tempus.', 40, 'NUNC URNA PURUS, INTERDUM UT SOLLICITUDIN SED, ADIPISCING VITAE LOREM.', 'ipad, tablet', 40, 'ipad, tablet', 'opennemas.com', 0, 1, NULL, NULL, NULL, 'Nam sit amet dolor elit, ut auctor sapien.'),
(75, 'Curabitur sit amet nisi vehicula enim faucibus porttitor a a turpis. Aliquam aliquet, tortor vel tempus blandit, metus lorem sodales lorem, a vehicula metus eros quis massa.', 169, 'POLITICA', 'luces de colores', 44, 'mac, ipad, portatil', 'EFE', 0, 1, NULL, NULL, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(76, '', 0, 'SOCIEDAD', '', 0, '', 'opennemas', 0, 1, 0, '', NULL, 'Duis vitae eros sed sem venenatis consequat imperdiet nec quam.'),
(77, '<p>Morbi aliquam hendrerit interdum. Nam eu felis ornare nisi condimentum accumsan. Curabitur bibendum accumsan varius. Praesent blandit, magna eu condimentum sollicitudin, odio eros gravida diam, posuere hendrerit mauris erat nec ligula. Sed quis eros quis urna cursus rutrum in tempus eros.</p>\r\n', 6, 'DEPORTES', '', 33, '', 'EFE', 0, 1, 0, '', NULL, 'Iipsum quia dolor sit amet, consectetur, adipisci velit'),
(78, '', 0, 'DEPORTES', '', 0, '', 'EFE', 0, 1, 0, '', NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(79, 'Maecenas dui elit, vestibulum tincidunt aliquam sit amet, aliquam vel nunc. Nulla dolor mi, faucibus sed tempus eget, fermentum ac leo. Aliquam eget ligula a velit luctus sollicitudin quis in lacus. Etiam id bibendum mi. Curabitur ultricies lacinia rhoncus. Duis vel tellus in augue vulputate hendrerit.', 170, 'DUIS VEL TELLUS IN AUGUE VULPUTATE HENDRERIT', 'luz en la ciudad', 170, 'luz en la ciudad', 'EFE', 0, 1, NULL, NULL, NULL, 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit'),
(80, '<p>Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n', 40, 'POLITICA', 'ipad, tablet', 0, '', 'EFE', 0, 1, 0, '', NULL, 'Dolorem ipsum quia dolor sit amet, consectetur, adipisci velit.'),
(81, '<p>Nullam ac eros quis ligula imperdiet rhoncus ut eget velit. Aenean porta lacinia risus, vel faucibus orci vehicula vitae. Sed consequat sagittis nisl sit amet vehicula. Phasellus id nulla nec felis tempus sollicitudin sed non justo.</p>\r\n', 36, 'DEPORTES', '', 0, '', 'opennemas', 0, 1, 0, '', NULL, 'Cras iaculis venenatis laoreet. Quisque vitae nunc purus'),
(82, '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus.</p>', 42, 'LOREN IPSUM', 'gris, bola', 0, '', 'Agencia EFE', 0, 1, 0, '', NULL, 'Nam viverra auctor orci id accumsan.'),
(84, '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>', 0, 'LOREN IPSUM', '', 29, 'perro, negro, cachorro', 'Agencia EFE', 0, 1, 0, '', NULL, 'Nam viverra auctor orci id accumsan.'),
(85, '<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper.</p>', 0, 'LOREN IPSUM', '', 0, '', 'Agencia onm', 0, 1, 0, '', NULL, 'Donec neque metus, scelerisque sit amet porttitor vel, adipiscing pellentesque felis'),
(86, '&nbsp;Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis. <br />', 40, 'CURABITUR VIVERRA, NEQUE AC DAPIBUS IACULIS, DUI TORTOR DAPIBUS URNA.', 'ipad, tablet', 0, '', 'Agencia onm', 0, 1, NULL, NULL, NULL, 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna, vel ullamcorper dui lacus ut urna. '),
(87, 'Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor.&nbsp;', 20, 'LOREN IPSUM', '', 0, '', 'Agencia onm', 0, 1, 0, '', NULL, 'Aenean vehicula condimentum dui, at vehicula risus porttitor quis'),
(167, 'Suspendisse sollicitudin turpis sit amet nisl volutpat tincidunt. Phasellus pellentesque pulvinar rutrum. Ut interdum malesuada nunc vel viverra. Ut porta facilisis neque, a vestibulum sem volutpat adipiscing. Praesent at rhoncus nisi. Nulla eget quam neque, porta molestie tellus. Mauris sit amet massa lectus. Suspendisse potenti. Donec augue elit, suscipit eu pellentesque vitae, ornare cursus libero. Donec vestibulum, augue at accumsan sodales, metus ante suscipit quam, non iaculis leo magna porttitor tortor.', 33, 'LOREN IPSUM', '', 32, '', 'opennemas.com', 0, 0, 0, '', NULL, 'Suspendisse sollicitudin turpis '),
(210, '<p>Nullam sodales, arcu at posuere gravida, ipsum odio ornare mauris, in facilisis nibh magna in lacus. Suspendisse quis tincidunt mauris. Phasellus eu hendrerit eros. Praesent ornare enim quis purus faucibus mollis.</p>', 174, 'PHASELLUS EU HENDRERIT EROS.', '', 169, 'luces de colores', 'onoso.opennemas.com', 0, 1, NULL, NULL, NULL, 'Praesent ornare enim quis purus faucibus mollis.'),
(215, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi nibh tellus, lobortis aliquam egestas ac, facilisis sit amet justo. Nulla sodales velit massa. Integer ut arcu ut enim sollicitudin faucibus.', 20, 'VESTIBULUM SAPIEN ARCU, ORNARE UT BIBENDUM SED, PELLENTESQUE SIT AMET RISUS', '', 16, '', 'onoso.opennemas.com', 0, 1, NULL, NULL, NULL, 'Fusce vel libero justo, quis hendrerit elit. Phasellus quis dolor leo. Nam nisl mi, venenatis sit amet te'),
(216, '<p>Sed est tortor, fringilla in consectetur sed, aliquet at risus. Cras vel nunc vitae nunc lacinia convallis in eu sapien. Aliquam vestibulum augue tellus, a pellentesque ante. Ut non odio nec orci euismod ornare sed porta leo. Sed bibendum justo sit amet dolor mollis quis egestas purus iaculis. Nulla metus neque, hendrerit vel porta vel, luctus non mi. Maecenas ipsum purus, consequat ac porttitor at, laoreet ac eros.</p>\r\n', 170, 'NULLA METUS NEQUE, HENDRERIT VEL PORTA VEL, LUCTUS NON MI.', '', 170, '', 'opennemas.com', 0, 1, 0, '', NULL, 'Sed bibendum justo sit amet dolor mollis quis egestas purus iaculis'),
(217, 'Curabitur sit amet nisi vehicula enim faucibus porttitor a a turpis. Aliquam aliquet, tortor vel tempus blandit, metus lorem sodales lorem, a vehicula metus eros quis massa. Sed dolor justo, aliquet vel fringilla ut, ullamcorper ut justo. Cras pulvinar tempor erat et pharetra.&nbsp;', 29, 'Sed dolor justo, aliquet vel fringilla ut, ullamcorper ut justo.', '0', 20, '', 'onoso.opennemas.com', 0, 1, NULL, NULL, NULL, 'Vivamus at arcu nibh. Suspendisse sit amet ipsum ligula, nec cursus dolor. ');

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

--
-- A extraer datos da táboa `attachments`
--

INSERT INTO `attachments` (`pk_attachment`, `title`, `path`, `category`) VALUES
(209, 'prueba', '/2011/10/06/bin_laden.jpg', 0),
(212, 'testing upload files', '/2011/10/06/git_cheat_sheet_white.pdf', 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


--
-- A extraer datos da táboa `comments`
--

INSERT INTO `comments` (`id`, `content_id`, `author`, `author_email`, `author_url`, `author_ip`, `date`, `body`, `status`, `agent`, `type`, `parent_id`, `user_id`, `content_type_referenced`) VALUES
(1, 184, 'John Doe', 'autor@ejemplo.com', '', '77.209.125.150', '2013-06-17 13:51:43', 'Este es un comentario de prueba', 'accepted', '', '', 0, 0, 'opinion');

--
-- Estrutura da táboa `commentsmeta`
--

CREATE TABLE IF NOT EXISTS `commentsmeta` (
  `fk_content` bigint(32) NOT NULL,
  `meta_name` varchar(255) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`fk_content`,`meta_name`),
  KEY `fk_content` (`fk_content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

--
-- A extraer datos da táboa `contentmeta`
--

INSERT INTO `contentmeta` (`fk_content`, `meta_name`, `meta_value`) VALUES
(54, 'summary', '<p>Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing.</p>\r\n'),
(55, 'summary', '<p>Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.&nbsp;<span style="line-height: 1.6em;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum.</span></p>\r\n'),
(56, 'summary', '<p>Nunc ac ante at nisi sagittis posuere. In ac viverra lorem. Nullam eu quam odio. Etiam blandit elit vitae sem tincidunt sodales. Proin consectetur tempus sem et gravida. Aliquam non velit a arcu ornare lobortis vitae quis libero. In posuere dui vitae erat posuere at commodo erat egestas. Sed tempus egestas nisl, eget dictum felis rhoncus sed.</p>\r\n'),
(57, 'summary', '<p>Curabitur eu magna eget nisi tincidunt lobortis non vitae purus. In semper, tellus et pellentesque viverra, ligula erat pellentesque ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta ac massa.</p>\r\n'),
(58, 'summary', '<p>Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n'),
(59, 'summary', '<p>Donec non sapien purus. Pellentesque dignissim elementum arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat, elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula risus porttitor quis.</p>\r\n'),
(60, 'summary', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum.</p>\r\n'),
(183, 'summary', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum.</p>\r\n'),
(184, 'summary', '<p>Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum.Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero. Donec ultricies tincidunt ultrices.</p>\r\n');

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

--
-- A extraer datos da táboa `contents`
--

INSERT INTO `contents` (`pk_content`, `fk_content_type`, `content_type_name`, `title`, `description`, `body`, `metadata`, `starttime`, `endtime`, `created`, `changed`, `content_status`, `fk_author`, `fk_publisher`, `fk_user_last_editor`, `views`, `position`, `frontpage`, `in_litter`, `in_home`, `home_pos`, `slug`, `available`, `params`, `category_name`, `favorite`, `urn_source`) VALUES
(3, 8, 'photo', 'motorcycle-off-road-587-22.jpg', '', '', '', '2011-09-23 18:44:09', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-10-11 19:17:12', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'motorcycle-off-road-587-22-jpg', 1, NULL, 'deportes', NULL, NULL),
(4, 8, 'photo', 'motorcycle-off-road-587-2.jpg', '', '', '', '2011-09-23 18:44:09', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-10-11 19:17:12', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'motorcycle-off-road-587-2-jpg', 1, NULL, 'deportes', NULL, NULL),
(5, 8, 'photo', 'motorcycle-off-road-588-2.jpg', '', '', '', '2011-09-23 18:44:09', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-10-11 19:17:12', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'motorcycle-off-road-588-2-jpg', 1, NULL, 'deportes', NULL, NULL),
(6, 8, 'photo', 'swimming-photography-652-2.jpg', '', '', '', '2011-09-23 18:44:09', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-10-11 19:17:12', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'swimming-photography-652-2-jpg', 1, NULL, 'deportes', NULL, NULL),
(7, 8, 'photo', 'swimming-photography-652-6.jpg', '', '', '', '2011-09-23 18:44:09', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-10-11 19:17:12', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'swimming-photography-652-6-jpg', 1, NULL, 'deportes', NULL, NULL),
(8, 8, 'photo', 'swimming-photography-652-8.jpg', '', '', '', '2011-09-23 18:44:09', '0000-00-00 00:00:00', '2011-09-23 18:44:09', '2011-10-11 19:17:12', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'swimming-photography-652-8-jpg', 1, NULL, 'deportes', NULL, NULL),
(10, 1, 'article', 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet.', 'Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales...', '<p>Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<p>Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<p>Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<p>Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,economÃ­a,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2013-04-18 09:58:42', 1, 7, 0, 3, 6, 1, 0, 0, 0, 100, '-neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'a:13:{s:14:"agencyBulletin";s:0:"";s:15:"imageHomeFooter";s:0:"";s:9:"imageHome";s:0:"";s:9:"titleSize";s:0:"";s:13:"imagePosition";s:0:"";s:9:"titleHome";s:0:"";s:13:"titleHomeSize";s:0:"";s:12:"subtitleHome";s:0:"";s:11:"summaryHome";s:0:"";s:17:"imageHomePosition";s:0:"";s:11:"withGallery";s:0:"";s:14:"withGalleryInt";s:0:"";s:15:"withGalleryHome";s:0:"";}', 'economia', NULL, NULL),
(11, 1, 'article', 'Morbi venenatis laoreet justo, nec vestibulum mi sodales sit amet', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ...', '<p>Cras suscipit malesuada odio vitae scelerisque. Morbi venenatis laoreet justo, nec vestibulum mi sodales sit amet. Nam lacinia pharetra tincidunt. Fusce pellentesque, massa eu suscipit tempus, metus diam ullamcorper lacus, ac iaculis quam risus euismod elit. Nam hendrerit rutrum ante quis iaculis. Vivamus quis metus at ante sodales rhoncus. Morbi ut posuere arcu.</p>\r\n<p>Duis nec quam eget leo mollis tempus. Sed vestibulum sodales nisi, at feugiat nunc pellentesque id. Maecenas quis quam sit amet felis varius malesuada in at nunc. Integer egestas nulla pretium turpis porta at pulvinar orci volutpat. Etiam mattis tincidunt lorem gravida commodo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam non metus leo. Curabitur odio libero, iaculis sit amet bibendum nec, ultricies posuere arcu. Maecenas gravida, nisi et facilisis aliquet, tortor lectus commodo turpis, ut condimentum enim justo sed elit. In volutpat, ante eu consequat adipiscing, sem ante imperdiet nisl, et laoreet neque tortor a dolor. Pellentesque scelerisque tincidunt leo, in commodo quam rhoncus vitae. Nunc sed massa et massa facilisis feugiat. Nunc diam arcu, aliquam et convallis quis, molestie eu justo. Etiam sapien neque, pretium ac euismod mollis, pellentesque vitae sapien. Donec suscipit diam ac sapien scelerisque at venenatis mi cursus. Suspendisse nisi purus, hendrerit non hendrerit non, placerat nec nunc.</p>\r\n<p>Duis convallis varius sollicitudin. Nam aliquam, turpis ut congue euismod, ligula sem condimentum enim, varius sagittis elit justo et urna. Donec pellentesque fringilla malesuada. Etiam ultricies, velit at posuere tempus, ligula purus vestibulum tellus, ut vehicula felis lectus sit amet nisl. Nullam mollis molestie arcu, in volutpat mauris malesuada vel. Nunc urna ipsum, viverra eu accumsan id, consectetur at sem. Aenean ullamcorper euismod iaculis. Duis tristique gravida condimentum. Mauris blandit arcu id nisi rutrum in dictum nulla malesuada. Curabitur a enim a metus eleifend auctor. Sed pretium ultrices eros, non cursus odio venenatis ut. Vivamus faucibus hendrerit eros id tincidunt.</p>\r\n<p>Quisque et augue ante. Proin in viverra odio. Vestibulum lectus turpis, lacinia nec porttitor id, accumsan quis neque. Vivamus sed ligula metus. Duis consectetur lorem quis felis eleifend varius. Proin a eros et libero tempor mattis consequat id felis. Aenean consequat lacinia semper. Mauris auctor mollis tellus, et pulvinar justo varius at.</p>', 'economÃ­a, morbi, venenatis, laoreet, justo, nec, vestibulum, sodales, sit, amet, neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, consectetur, adipisci, velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2013-06-17 18:03:08', 1, 7, 0, 3, 8, 2, 0, 0, 0, 100, 'morbi-venenatis-laoreet-justo-nec-vestibulum-mi-sodales-sit-amet', 1, NULL, 'economia', NULL, NULL),
(12, 1, 'article', 'Cras metus dui, elementum id convallis vitae, feugiat nec nulla.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa....', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>', 'economÃ­a, neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-13 01:54:54', 1, 7, 0, 3, 5, 1, 1, 0, 1, 1, 'cras-metus-dui-elementum-id-convallis-vitae-feugiat-nec-nulla', 1, NULL, 'economia', NULL, NULL),
(13, 1, 'article', 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...', '\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet...', '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna, vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel, congue id ante. Donec non sapien purus. Pellentesque dignissim elementum arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat, elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula risus porttitor quis.</p>\r\n\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum.</p>\r\n\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum et tristique ut, luctus ut enim. Praesent non neque eget est convallis vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus. In semper, tellus et pellentesque viverra, ligula erat pellentesque ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros, blandit nec placerat et, luctus et purus. Praesent quis elit turpis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n\r\n<p>Cras neque nulla, dapibus id tempor et, scelerisque ut arcu. Cras convallis justo vitae quam tempor euismod. In blandit placerat congue. Suspendisse tempor mi et nisi faucibus congue. Curabitur in lobortis quam. Nam blandit arcu vel metus sollicitudin adipiscing. Donec nisl quam, tristique sit amet pharetra vitae, congue eu magna. Nulla a lacinia turpis. Quisque viverra, purus sit amet auctor convallis, velit ligula tempus augue, ac porta nisi risus non urna. Nulla vestibulum quam sit amet lorem auctor ac gravida quam placerat. Morbi nec libero tortor. Vivamus vel erat a erat ultricies pharetra.</p>\r\n</div>\r\n', 'economÃ­a,neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2013-04-18 09:57:07', 1, 7, 0, 3, 3, 3, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'a:13:{s:14:"agencyBulletin";s:0:"";s:15:"imageHomeFooter";s:0:"";s:9:"imageHome";s:0:"";s:9:"titleSize";s:0:"";s:13:"imagePosition";s:0:"";s:9:"titleHome";s:0:"";s:13:"titleHomeSize";s:0:"";s:12:"subtitleHome";s:0:"";s:11:"summaryHome";s:0:"";s:17:"imageHomePosition";s:0:"";s:11:"withGallery";s:0:"";s:14:"withGalleryInt";s:0:"";s:15:"withGalleryHome";s:0:"";}', 'economia', NULL, NULL),
(14, 1, 'article', 'Nam viverra auctor orci id accumsan.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa....', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>', 'economÃ­a, neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-13 01:43:58', 1, 7, 0, 3, 338, 1, 1, 0, 1, 1, 'nam-viverra-auctor-orci-id-accumsan', 1, NULL, 'economia', NULL, NULL),
(15, 8, 'photo', 'stock-photo-speed_rev100.jpg', '', '', '', '2011-09-26 23:42:32', '0000-00-00 00:00:00', '2011-09-26 23:42:32', '2011-10-11 19:17:03', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'stock-photo-speedrev100-jpg', 1, NULL, 'economia', NULL, NULL),
(16, 8, 'photo', 'samsung-galaxy-tab-1-wallpaper.jpg', '', '', '', '2011-09-26 23:42:32', '0000-00-00 00:00:00', '2011-09-26 23:42:32', '2011-10-11 19:17:03', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'samsung-galaxy-tab-1-wallpaper-jpg', 1, NULL, 'economia', NULL, NULL),
(17, 8, 'photo', 'samsung-galaxy-s-official-21.jpg', '', '', '', '2011-09-26 23:42:32', '0000-00-00 00:00:00', '2011-09-26 23:42:32', '2011-10-11 19:17:03', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'samsung-galaxy-s-official-21-jpg', 1, NULL, 'economia', NULL, NULL),
(18, 8, 'photo', 'galaxy-640x480-1.jpg', '', '', '', '2011-09-26 23:44:05', '0000-00-00 00:00:00', '2011-09-26 23:44:05', '2011-10-11 19:17:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'galaxy-640x480-1-jpg', 1, NULL, 'fotos-de-hoy', NULL, NULL),
(19, 8, 'photo', 'galaxy-eso-593-8.jpg', '', '', '', '2011-09-26 23:44:05', '0000-00-00 00:00:00', '2011-09-26 23:44:05', '2011-10-11 19:17:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'galaxy-eso-593-8-jpg', 1, NULL, 'fotos-de-hoy', NULL, NULL),
(20, 8, 'photo', 'm31_ware.jpg', '', '', '', '2011-09-26 23:44:05', '0000-00-00 00:00:00', '2011-09-26 23:44:05', '2011-10-11 19:17:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'm31ware-jpg', 1, NULL, 'fotos-de-hoy', NULL, NULL),
(21, 8, 'photo', 'motorcycle-off-road-587-22.jpg', '', '', '', '2011-09-26 23:44:05', '0000-00-00 00:00:00', '2011-09-26 23:44:05', '2011-10-11 19:17:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'motorcycle-off-road-587-22-jpg', 1, NULL, 'fotos-de-hoy', NULL, NULL),
(22, 8, 'photo', 'animal-photography-868-2.jpg', '', '', 'loros rojo, ojos, pico', '2011-09-26 23:45:50', '0000-00-00 00:00:00', '2011-09-26 23:45:50', '2011-10-11 19:17:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'animal-photography-868-2-jpg', 1, NULL, 'fotos-de-hoy', NULL, NULL),
(23, 8, 'photo', 'animal-photography-868-4.jpg', '', '', 'lobo, blanco, nieve', '2011-09-26 23:45:51', '0000-00-00 00:00:00', '2011-09-26 23:45:51', '2011-10-11 19:17:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'animal-photography-868-4-jpg', 1, NULL, 'fotos-de-hoy', NULL, NULL),
(24, 8, 'photo', 'animal-photography-868-8.jpg', 'uricatos, arena, beige', '', 'uricatos, arena, beige', '2011-09-26 23:45:51', '0000-00-00 00:00:00', '2011-09-26 23:45:51', '2011-10-11 19:17:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'animal-photography-868-8-jpg', 1, NULL, 'fotos-de-hoy', NULL, NULL),
(25, 8, 'photo', 'animal-photography-868-16.jpg', 'rojo, mariquitas', '', 'rojo, mariquitas', '2011-09-26 23:45:51', '0000-00-00 00:00:00', '2011-09-26 23:45:51', '2011-10-11 19:17:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'animal-photography-868-16-jpg', 1, NULL, 'fotos-de-hoy', NULL, NULL),
(26, 8, 'photo', 'fun-animals-742-2.jpg', 'perro, gafas, sol', '', 'perro, gafas, sol', '2011-09-26 23:45:51', '0000-00-00 00:00:00', '2011-09-26 23:45:51', '2011-10-11 19:17:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'fun-animals-742-2-jpg', 1, NULL, 'fotos-de-hoy', NULL, NULL),
(27, 8, 'photo', 'fun-animals-742-4.jpg', 'perro, deporte, balÃ³n', '', 'perro, deporte, balÃ³n', '2011-09-26 23:45:51', '0000-00-00 00:00:00', '2011-09-26 23:45:51', '2011-10-11 19:17:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'fun-animals-742-4-jpg', 1, NULL, 'fotos-de-hoy', NULL, NULL),
(28, 8, 'photo', 'fun-animals-742-8.jpg', 'zorro, dormir', '', 'zorro, dormir', '2011-09-26 23:45:52', '0000-00-00 00:00:00', '2011-09-26 23:45:52', '2011-10-11 19:17:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'fun-animals-742-8-jpg', 1, NULL, 'fotos-de-hoy', NULL, NULL),
(29, 8, 'photo', 'fun-animals-742-12.jpg', 'perro, negro, cachorro', '', 'perro, negro, cachorro', '2011-09-26 23:45:52', '0000-00-00 00:00:00', '2011-09-26 23:45:52', '2011-10-11 19:17:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'fun-animals-742-12-jpg', 1, NULL, 'fotos-de-hoy', NULL, NULL),
(30, 8, 'photo', 'fun-animals-742-14.jpg', 'perro, dico, cesped', '', 'perro, dico, cesped', '2011-09-26 23:45:52', '0000-00-00 00:00:00', '2011-09-26 23:45:52', '2011-10-11 19:17:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'fun-animals-742-14-jpg', 1, NULL, 'fotos-de-hoy', NULL, NULL),
(31, 8, 'photo', 'fun-animals-742-38.jpg', 'pato, agua', '', 'pato, agua', '2011-09-26 23:45:52', '0000-00-00 00:00:00', '2011-09-26 23:45:52', '2011-10-11 19:17:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'fun-animals-742-38-jpg', 1, NULL, 'fotos-de-hoy', NULL, NULL),
(32, 8, 'photo', 'diving-photography-662-2.jpg', '', '', 'deportes, piscina, nadar, salto trampolin', '2011-09-26 23:50:33', '0000-00-00 00:00:00', '2011-09-26 23:50:33', '2011-10-11 19:17:12', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'diving-photography-662-2-jpg', 1, NULL, 'deportes', NULL, NULL),
(33, 8, 'photo', 'diving-photography-662-8.jpg', '', '', 'deportes, piscina, nadar, salto trampolin', '2011-09-26 23:50:33', '0000-00-00 00:00:00', '2011-09-26 23:50:33', '2011-10-11 19:17:12', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'diving-photography-662-8-jpg', 1, NULL, 'deportes', NULL, NULL),
(34, 8, 'photo', 'motorcycle-off-road-587-2.jpg', '', '', 'deportes, moto, desierto, cross', '2011-09-26 23:50:34', '0000-00-00 00:00:00', '2011-09-26 23:50:34', '2011-10-11 19:17:12', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'motorcycle-off-road-587-2-jpg', 1, NULL, 'deportes', NULL, NULL),
(35, 8, 'photo', 'motorcycle-off-road-587-22.jpg', '', '', 'deportes, moto, desierto, cross', '2011-09-26 23:50:34', '0000-00-00 00:00:00', '2011-09-26 23:50:34', '2011-10-11 19:17:12', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'motorcycle-off-road-587-22-jpg', 1, NULL, 'deportes', NULL, NULL),
(36, 8, 'photo', 'motorcycle-off-road-588-2.jpg', '', '', 'deportes, moto, saltos, cross', '2011-09-26 23:50:34', '0000-00-00 00:00:00', '2011-09-26 23:50:34', '2011-10-11 19:17:12', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'motorcycle-off-road-588-2-jpg', 1, NULL, 'deportes', NULL, NULL),
(37, 8, 'photo', 'swimming-photography-652-2.jpg', '', '', 'piscina, nadador, agua', '2011-09-26 23:50:34', '0000-00-00 00:00:00', '2011-09-26 23:50:34', '2011-10-11 19:17:12', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'swimming-photography-652-2-jpg', 1, NULL, 'deportes', NULL, NULL),
(38, 8, 'photo', 'swimming-photography-652-8.jpg', '', '', 'piscina, nadador, salto, agua', '2011-09-26 23:50:34', '0000-00-00 00:00:00', '2011-09-26 23:50:34', '2011-10-11 19:17:12', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'swimming-photography-652-8-jpg', 1, NULL, 'deportes', NULL, NULL),
(40, 8, 'photo', '1004ipad_hometimes-420-90.jpg', 'ipad, tablet', '', 'ipad, tablet', '2011-09-26 23:53:56', '0000-00-00 00:00:00', '2011-09-26 23:53:56', '2011-10-11 19:16:41', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '1004ipadhometimes-420-90-jpg', 1, NULL, 'sociedad', NULL, NULL),
(41, 8, 'photo', 'stock-photo-abstract-galaxy-perfect-background-with-space-for-text-or-image_rev100.jpg', 'verde, clorofila', '', 'verde, clorofila', '2011-09-26 23:53:56', '0000-00-00 00:00:00', '2011-09-26 23:53:56', '2011-10-11 19:16:41', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'stock-photo-abstract-galaxy-perfect-background-with-space-for-text-or-imagerev100-jpg', 1, NULL, 'sociedad', NULL, NULL),
(42, 8, 'photo', 'stock-photo-close-up-of-newton-s-cradle-d-render_rev100.jpg', 'gris, bola', '', 'gris, bola', '2011-09-26 23:53:56', '0000-00-00 00:00:00', '2011-09-26 23:53:56', '2011-10-11 19:16:41', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'stock-photo-close-up-of-newton-s-cradle-d-renderrev100-jpg', 1, NULL, 'sociedad', NULL, NULL),
(43, 8, 'photo', 'stock-photo-ecological-breeze_rev100.jpg', 'aire, viento, ', '', 'aire, viento, ', '2011-09-26 23:53:56', '0000-00-00 00:00:00', '2011-09-26 23:53:56', '2011-10-11 19:16:41', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'stock-photo-ecological-breezerev100-jpg', 1, NULL, 'sociedad', NULL, NULL),
(44, 8, 'photo', 'Want-a-Free-Xbox-Buy-a-Laptop-for-College2.jpg', 'mac, ipad, portatil', '', 'mac, ipad, portatil', '2011-09-26 23:53:56', '0000-00-00 00:00:00', '2011-09-26 23:53:56', '2011-10-11 19:16:41', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'want-a-free-xbox-buy-a-laptop-for-college2-jpg', 1, NULL, 'sociedad', NULL, NULL),
(45, 7, 'album', 'jumping', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue. ', '', 'jumping', '2011-09-27 11:41:24', '0000-00-00 00:00:00', '2011-09-27 11:41:24', '2011-09-27 11:41:24', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'jumping', 1, NULL, 'deportes', NULL, NULL),
(46, 1, 'article', 'Dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '\r\nCurabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut...', '<p>Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-11 19:20:08', 1, 7, 0, 3, 7, 1, 1, 0, 1, 1, 'dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, NULL, 'sociedad', NULL, NULL),
(47, 1, 'article', ' Pellentesque vehicula tortor venenatis eros ultricies iaculis quis aliquet nunc.', 'Morbi gravida varius orci iaculis semper. Aenean nisi ipsum, convallis at suscipit non, ornare ac lorem. Cras sed leo turpis, eu lacinia orci. Donec magna nunc, suscipit vitae condimentum sed, tincidunt nec tellus. Quisque convallis porta urna nec suscipit. Nulla eu tortor commodo nulla dignissim dignissim eget et mi. Duis...', '<p>Morbi gravida varius orci iaculis semper. Aenean nisi ipsum, convallis at suscipit non, ornare ac lorem. Cras sed leo turpis, eu lacinia orci. Donec magna nunc, suscipit vitae condimentum sed, tincidunt nec tellus. Quisque convallis porta urna nec suscipit. Nulla eu tortor commodo nulla dignissim dignissim eget et mi. Duis vitae eros sed sem venenatis consequat imperdiet nec quam. Nunc commodo consectetur malesuada.</p>\r\n\r\n<p>Nam lobortis leo ut quam suscipit malesuada accumsan massa posuere. Aliquam non ante nec sem convallis rutrum in in purus. Nam id magna ante. Vestibulum egestas ultrices ultricies. Morbi luctus ante eu metus dapibus a rhoncus felis blandit. Quisque vitae nulla quis lacus gravida feugiat. Phasellus consequat est aliquet lectus blandit a consequat purus viverra. Vestibulum a neque arcu.</p>\r\n\r\n<p>Proin non lacus vitae libero ultrices ultricies. Ut elit magna, pretium nec condimentum nec, vestibulum eget felis. Aliquam erat volutpat. Pellentesque vehicula tortor venenatis eros ultricies iaculis quis aliquet nunc. Nulla eros nunc, faucibus nec adipiscing vel, blandit eget libero. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed ut vulputate dui. Phasellus condimentum nulla eget nisl gravida dignissim pretium libero pulvinar. Curabitur quis malesuada sapien. Nunc arcu mauris, tristique at hendrerit sit amet, dictum sit amet libero. Nunc accumsan sem placerat est imperdiet feugiat gravida massa mattis. Suspendisse ac tellus libero. Nam placerat egestas odio, placerat rutrum risus vestibulum ut. Vestibulum pretium pharetra vehicula.</p>\r\n\r\n<p>Integer luctus diam ipsum, non ullamcorper nisi. Mauris malesuada molestie dui, ut lobortis quam rhoncus a. Cras elementum metus ut libero fermentum vulputate. Fusce molestie felis erat. Aliquam eu nulla velit, at pellentesque nulla. Quisque id velit sapien. Integer blandit vulputate mattis. Phasellus dignissim tempor lorem, dapibus porttitor lectus hendrerit ac. Maecenas lacinia justo non ligula luctus convallis. Ut vehicula nunc id erat tincidunt sagittis. Pellentesque non nibh nec urna ultricies ullamcorper. Etiam quis ligula quam, sit amet facilisis ipsum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Ut pretium leo ac quam congue euismod. Vestibulum sed nisi risus.</p>\r\n', 'economÃ­a,pellentesque,vehicula,tortor,venenatis,eros,ultricies,iaculis,quis,aliquet,nunc,neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2013-04-18 09:55:32', 1, 7, 0, 3, 4, 1, 1, 0, 0, 100, '-pellentesque-vehicula-tortor-venenatis-eros-ultricies-iaculis-quis-aliquet-nunc', 1, 'a:13:{s:14:"agencyBulletin";s:0:"";s:15:"imageHomeFooter";s:0:"";s:9:"imageHome";s:0:"";s:9:"titleSize";s:0:"";s:13:"imagePosition";s:0:"";s:9:"titleHome";s:0:"";s:13:"titleHomeSize";s:0:"";s:12:"subtitleHome";s:0:"";s:11:"summaryHome";s:0:"";s:17:"imageHomePosition";s:0:"";s:11:"withGallery";s:0:"";s:14:"withGalleryInt";s:0:"";s:15:"withGalleryHome";s:0:"";}', 'economia', NULL, NULL),
(48, 1, 'article', 'Maecenas vitae nisi dui, nec commodo magna.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ...', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing.</p>\r\n<p>Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>', 'sociedad, maecenas, vitae, nisi, dui, nec, commodo, magna, neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-13 02:01:00', 1, 7, 0, 3, 2, 1, 1, 0, 0, 100, 'maecenas-vitae-nisi-dui-nec-commodo-magna', 1, NULL, 'sociedad', NULL, NULL),
(49, 1, 'article', 'Quia dolor sit amet, consectetur, adipisci velit', 'Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum...', '<p>Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-13 01:45:54', 1, 7, 0, 3, 5, 1, 1, 0, 1, 2, 'quia-dolor-sit-amet-consectetur-adipisci-velit', 1, NULL, 'economia', NULL, NULL),
(54, 4, 'opinion', 'Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing.', '', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. &nbsp;Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<h4>&quot;Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit</h4>\r\n', 'curabitur,tristique,augue,non,diam,tincidunt,ut,aliquet,nulla,adipiscing', '2011-09-27 14:47:18', '0000-00-00 00:00:00', '2011-09-27 14:47:18', '2013-04-18 05:20:57', 1, 7, 7, 0, 10, 1, 0, 0, 1, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, NULL, '0', NULL, NULL),
(50, 1, 'article', 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...', '', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam   magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis   libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae   nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam   ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing   lacinia fringilla urna elementum. Cras metus dui, elementum id   convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur   tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam   convallis ipsum id diam sodales vulputate. Vestibulum venenatis   elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 14:45:29', 1, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, NULL, 'economia', NULL, NULL),
(51, 1, 'article', 'Et qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa....', '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>\r\n<p>Cras neque nulla, dapibus id tempor et, scelerisque ut arcu. Cras  convallis justo vitae quam tempor euismod. In blandit placerat congue.  Suspendisse tempor mi et nisi faucibus congue. Curabitur in lobortis  quam. Nam blandit arcu vel metus sollicitudin adipiscing. Donec nisl  quam, tristique sit amet pharetra vitae, congue eu magna. Nulla a  lacinia turpis. Quisque viverra, purus sit amet auctor convallis, velit  ligula tempus augue, ac porta nisi risus non urna. Nulla vestibulum quam  sit amet lorem auctor ac gravida quam placerat. Morbi nec libero  tortor. Vivamus vel erat a erat ultricies pharetra.</p>\r\n</div>', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-11 20:26:41', 1, 7, 0, 3, 3, 1, 1, 0, 0, 100, 'et-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, NULL, 'cultura', NULL, NULL);
INSERT INTO `contents` (`pk_content`, `fk_content_type`, `content_type_name`, `title`, `description`, `body`, `metadata`, `starttime`, `endtime`, `created`, `changed`, `content_status`, `fk_author`, `fk_publisher`, `fk_user_last_editor`, `views`, `position`, `frontpage`, `in_litter`, `in_home`, `home_pos`, `slug`, `available`, `params`, `category_name`, `favorite`, `urn_source`) VALUES
(52, 1, 'article', 'Ipsum quia dolor sit amet, consectetur, adipisci velit...', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa....', '<p>Nam  lobortis nibh eu ante molestie nec condimentum justo semper.  Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet,  pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum  porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem  velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus  accumsan sem  ipsum.Nam  lobortis nibh eu ante molestie nec condimentum justo semper.  Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet,  pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum  porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem  velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus  accumsan sem  ipsum.</p>\r\n<p>Nam  lobortis nibh eu ante molestie nec condimentum justo semper.  Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet,  pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum  porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem  velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus  accumsan sem  ipsum.Nam  lobortis nibh eu ante molestie nec condimentum justo semper.  Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet,  pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum  porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem  velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus  accumsan sem  ipsum.Nam  lobortis nibh eu ante molestie nec condimentum justo semper.  Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet,  pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum  porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem  velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus  accumsan sem  ipsum.</p>', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-11 19:20:32', 1, 7, 0, 3, 74, 1, 1, 0, 1, 1, 'ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, NULL, 'sociedad', NULL, NULL),
(53, 1, 'article', 'Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa....', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-11 20:26:41', 1, 7, 0, 7, 4, 1, 1, 0, 1, 2, 'fusce-rutrum-porttitor-urna-aliquet-imperdiet-dolor-fringilla-eu', 1, NULL, 'economia', NULL, NULL),
(55, 4, 'opinion', 'Fusce aliquam magna a augue mollis id suscipit quam tincidunt.', '', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<h4>&quot;Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit</h4>\r\n', 'fusce,aliquam,magna,augue,mollis,id,suscipit,quam,tincidunt', '2011-09-27 14:47:18', '0000-00-00 00:00:00', '2011-09-27 14:47:18', '2013-04-18 05:21:45', 1, 7, 7, 0, 2, 1, 0, 0, 1, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, NULL, '0', NULL, NULL),
(56, 4, 'opinion', 'Sed tempus egestas nisl, eget dictum felis rhoncus sed.', '', '<div id="lipsum">\r\n<p>Curabitur sit amet nisi vehicula enim faucibus porttitor a a turpis. Aliquam aliquet, tortor vel tempus blandit, metus lorem sodales lorem, a vehicula metus eros quis massa. Sed dolor justo, aliquet vel fringilla ut, ullamcorper ut justo. Cras pulvinar tempor erat et pharetra. Etiam vitae mauris sed nibh tincidunt volutpat vitae ac lorem. Nunc ac ante at nisi sagittis posuere. In ac viverra lorem. Nullam eu quam odio. Etiam blandit elit vitae sem tincidunt sodales. Proin consectetur tempus sem et gravida. Aliquam non velit a arcu ornare lobortis vitae quis libero. In posuere dui vitae erat posuere at commodo erat egestas. Sed tempus egestas nisl, eget dictum felis rhoncus sed. Phasellus blandit, eros vitae dictum fringilla, augue augue cursus metus, id auctor nisi ligula id tellus. Phasellus nunc mauris, molestie vel gravida nec, condimentum quis turpis.</p>\r\n\r\n<p>Vivamus at arcu nibh. Suspendisse sit amet ipsum ligula, nec cursus dolor. Nunc sit amet libero ante, tempor egestas lorem. Nullam dictum tincidunt risus id aliquet. Quisque feugiat gravida purus ut sodales. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam porta lobortis tempus. Aliquam imperdiet scelerisque odio non gravida. Integer eleifend lacus nec purus interdum ultricies. Donec egestas porttitor sem non feugiat. Aenean posuere venenatis nunc non tincidunt.</p>\r\n\r\n<p>Pellentesque eu lectus dui, quis pulvinar tellus. Phasellus id orci quam, fermentum facilisis dui. Vivamus a augue sit amet est bibendum aliquam ac ac nunc. Sed elementum gravida nisl, eget iaculis tellus eleifend et. Fusce sed mi orci, non porttitor diam. Phasellus vitae laoreet lectus. Aliquam pellentesque mattis nunc, at condimentum metus dignissim et. Praesent pulvinar, urna vel accumsan placerat, dui nunc pretium velit, sit amet scelerisque augue sapien in purus. Sed fermentum, dui eu ornare tincidunt, erat purus pretium elit, sed cursus dui enim in quam. Donec a arcu augue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nulla rutrum quam sed nisi consequat venenatis.</p>\r\n\r\n<p>Aliquam purus quam, semper vel molestie eu, convallis vitae urna. Aliquam ante massa, dignissim sed tempor imperdiet, elementum nec mauris. Donec ultrices, nunc ac semper adipiscing, mi tortor interdum felis, eu porta sapien est et massa. Vivamus consequat est vel sapien pellentesque non ornare ipsum imperdiet. Fusce pulvinar nunc non quam adipiscing lacinia. In eget massa nunc. Nullam felis nibh, placerat a imperdiet sed, faucibus eu velit. Sed in tincidunt elit.</p>\r\n</div>\r\n', 'sed,tempus,egestas,nisl,eget,dictum,felis,rhoncus', '2011-09-27 14:47:54', '0000-00-00 00:00:00', '2011-09-27 14:47:54', '2013-06-17 18:03:08', 1, 7, 7, 3, 35, 1, 0, 0, 1, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, NULL, '0', NULL, NULL),
(57, 4, 'opinion', 'Fusce elementum odio et felis aliquam tincidunt porta ac massa.', '', '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna, vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel, congue id ante. Donec non sapien purus. Pellentesque dignissim elementum arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat, elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula risus porttitor quis.</p>\r\n\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum.</p>\r\n\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum et tristique ut, luctus ut enim. Praesent non neque eget est convallis vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus. In semper, tellus et pellentesque viverra, ligula erat pellentesque ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros, blandit nec placerat et, luctus et purus. Praesent quis elit turpis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n', 'fusce,elementum,odio,et,felis,aliquam,tincidunt,porta,ac,massa', '2011-09-27 15:04:18', '0000-00-00 00:00:00', '2011-09-27 15:04:18', '2013-04-18 05:19:31', 1, 7, 7, 0, 3, 1, 0, 0, 1, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, NULL, '0', 1, NULL),
(58, 4, 'opinion', 'Nam viverra auctor orci id accumsan.', '', '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna, vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel, congue id ante. Donec non sapien purus. Pellentesque dignissim elementum arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat, elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula risus porttitor quis.</p>\r\n\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum.</p>\r\n\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum et tristique ut, luctus ut enim. Praesent non neque eget est convallis vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus. In semper, tellus et pellentesque viverra, ligula erat pellentesque ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros, blandit nec placerat et, luctus et purus. Praesent quis elit turpis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n\r\n<p>Cras neque nulla, dapibus id tempor et, scelerisque ut arcu. Cras convallis justo vitae quam tempor euismod. In blandit placerat congue. Suspendisse tempor mi et nisi faucibus congue. Curabitur in lobortis quam. Nam blandit arcu vel metus sollicitudin adipiscing. Donec nisl quam, tristique sit amet pharetra vitae, congue eu magna. Nulla a lacinia turpis. Quisque viverra, purus sit amet auctor convallis, velit ligula tempus augue, ac porta nisi risus non urna. Nulla vestibulum quam sit amet lorem auctor ac gravida quam placerat. Morbi nec libero tortor. Vivamus vel erat a erat ultricies pharetra.</p>\r\n</div>\r\n', 'nam,viverra,auctor,orci,id,accumsan', '2011-09-27 15:04:46', '0000-00-00 00:00:00', '2011-09-27 15:04:46', '2013-04-18 05:17:44', 1, 7, 7, 0, 30, 100, 0, 0, 1, 100, 'nam-viverra-auctor-orci-id-accumsan', 1, NULL, '0', 1, NULL),
(59, 4, 'opinion', 'Aenean vehicula condimentum dui.', '', '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna, vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel, congue id ante. Donec non sapien purus. Pellentesque dignissim elementum arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat, elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula risus porttitor quis.</p>\r\n\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum.</p>\r\n\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum et tristique ut, luctus ut enim. Praesent non neque eget est convallis vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus. In semper, tellus et pellentesque viverra, ligula erat pellentesque ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros, blandit nec placerat et, luctus et purus. Praesent quis elit turpis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n\r\n<p>Cras neque nulla, dapibus id tempor et, scelerisque ut arcu. Cras convallis justo vitae quam tempor euismod. In blandit placerat congue. Suspendisse tempor mi et nisi faucibus congue. Curabitur in lobortis quam. Nam blandit arcu vel metus sollicitudin adipiscing. Donec nisl quam, tristique sit amet pharetra vitae, congue eu magna. Nulla a lacinia turpis. Quisque viverra, purus sit amet auctor convallis, velit ligula tempus augue, ac porta nisi risus non urna. Nulla vestibulum quam sit amet lorem auctor ac gravida quam placerat. Morbi nec libero tortor. Vivamus vel erat a erat ultricies pharetra.</p>\r\n</div>\r\n', 'aenean,vehicula,condimentum,dui', '2011-09-27 15:04:46', '0000-00-00 00:00:00', '2011-09-27 15:04:46', '2013-04-18 05:18:38', 1, 7, 7, 0, 3, 1, 0, 0, 1, 100, 'nam-viverra-auctor-orci-id-accumsan', 1, NULL, '0', 1, NULL),
(60, 4, 'opinion', 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet.', '', '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna, vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel, congue id ante. Donec non sapien purus. Pellentesque dignissim elementum arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat, elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula risus porttitor quis.</p>\r\n\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum.</p>\r\n\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum et tristique ut, luctus ut enim. Praesent non neque eget est convallis vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus. In semper, tellus et pellentesque viverra, ligula erat pellentesque ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros, blandit nec placerat et, luctus et purus. Praesent quis elit turpis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>\r\n\r\n<p>Cras neque nulla, dapibus id tempor et, scelerisque ut arcu. Cras convallis justo vitae quam tempor euismod. In blandit placerat congue. Suspendisse tempor mi et nisi faucibus congue. Curabitur in lobortis quam. Nam blandit arcu vel metus sollicitudin adipiscing. Donec nisl quam, tristique sit amet pharetra vitae, congue eu magna. Nulla a lacinia turpis. Quisque viverra, purus sit amet auctor convallis, velit ligula tempus augue, ac porta nisi risus non urna. Nulla vestibulum quam sit amet lorem auctor ac gravida quam placerat. Morbi nec libero tortor. Vivamus vel erat a erat ultricies pharetra.</p>\r\n</div>\r\n', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet', '2011-09-27 15:05:02', '0000-00-00 00:00:00', '2011-09-27 15:05:02', '2013-06-17 18:03:08', 1, 7, 7, 3, 21, 1, 0, 0, 1, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, NULL, '0', NULL, NULL),
(61, 1, 'article', ' Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa....', '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam  magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis  libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae  nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam  ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing  lacinia fringilla urna elementum. Cras metus dui, elementum id  convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur  tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam  convallis ipsum id diam sodales vulputate. Vestibulum venenatis  elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>\r\n<p>Cras neque nulla, dapibus id tempor et, scelerisque ut arcu. Cras  convallis justo vitae quam tempor euismod. In blandit placerat congue.  Suspendisse tempor mi et nisi faucibus congue. Curabitur in lobortis  quam. Nam blandit arcu vel metus sollicitudin adipiscing. Donec nisl  quam, tristique sit amet pharetra vitae, congue eu magna. Nulla a  lacinia turpis. Quisque viverra, purus sit amet auctor convallis, velit  ligula tempus augue, ac porta nisi risus non urna. Nulla vestibulum quam  sit amet lorem auctor ac gravida quam placerat. Morbi nec libero  tortor. Vivamus vel erat a erat ultricies pharetra.</p>\r\n</div>', 'sociedad, curabitur, tristique, augue, non, diam, tincidunt, ut, aliquet, nulla, adipiscing, nam, viverra, auctor, orci, id, accumsan, unknown', '2011-09-27 15:06:26', '0000-00-00 00:00:00', '2011-09-27 15:06:26', '2013-06-17 18:03:08', 1, 7, 0, 3, 63, 2, 0, 0, 1, 2, '-curabitur-tristique-augue-non-diam-tincidunt-ut-aliquet-nulla-adipiscing', 1, NULL, 'sociedad', NULL, NULL),
(62, 1, 'article', 'Quisque vestibulum eros nec libero aliquam consectetur.', 'Curabitur in est ipsum, et pulvinar diam. In hac habitasse platea dictumst. Praesent tincidunt tincidunt tortor, in consectetur nulla tristique vitae. Pellentesque quis est in neque lobortis consectetur. Quisque vestibulum eros nec libero aliquam consectetur. Morbi quis nisl nunc, sed vulputate lorem.\r\nAliquam pharetra turpis tellus. Nullam vulputate eleifend diam...', '<p>Curabitur in est ipsum, et pulvinar diam. In hac habitasse platea dictumst. Praesent tincidunt tincidunt tortor, in consectetur nulla tristique vitae. Pellentesque quis est in neque lobortis consectetur. Quisque vestibulum eros nec libero aliquam consectetur. Morbi quis nisl nunc, sed vulputate lorem.</p>\r\n<p>Aliquam pharetra turpis tellus. Nullam vulputate eleifend diam non porta. Fusce suscipit pharetra auctor. Fusce cursus massa nec nisl lobortis auctor. Sed scelerisque nulla ac nulla dignissim faucibus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse potenti. Morbi tristique vehicula pharetra. Aenean sed risus metus. Aenean nec pharetra sem. Maecenas tortor sem, sollicitudin et auctor vel, faucibus quis lorem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aenean feugiat, lorem et pellentesque pulvinar, diam ligula cursus massa, vel vehicula libero risus interdum ligula.</p>\r\n<p>Proin euismod bibendum lorem, non tempus felis tristique at. Donec tincidunt dolor ac magna rutrum non euismod risus pharetra. Nullam eget purus eros, et malesuada tortor. Duis vitae ligula lorem, ac auctor dolor. Quisque id nulla ante, quis mollis nunc. In adipiscing mattis justo, et vehicula ipsum porta auctor. Sed sit amet felis odio. Vivamus vitae neque nec orci euismod ornare. Integer luctus, dui eu tristique pellentesque, eros risus iaculis turpis, id pellentesque metus enim eu leo. Donec dapibus congue condimentum. Nunc a risus quis urna accumsan luctus. Donec at eros magna. Vestibulum vel lacus ut metus iaculis malesuada in vitae lectus. Nulla imperdiet magna vitae ante vehicula adipiscing nec vitae risus. Mauris sagittis, risus non cursus ultrices, est libero ornare eros, at ultricies nunc sem sed lorem. Sed dapibus nulla eget tortor auctor adipiscing.</p>\r\n<p>Nam tincidunt dapibus felis, in mattis nisl scelerisque non. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin lorem massa, cursus vel suscipit sit amet, bibendum vitae erat. Suspendisse sit amet condimentum lacus. Nulla tincidunt dolor ut neque mollis tempor sit amet et sapien. Donec libero urna, luctus hendrerit hendrerit nec, porttitor nec mauris. Donec dictum ultricies erat id lacinia. Nulla facilisi. Phasellus pretium purus a ipsum convallis id scelerisque quam bibendum. Pellentesque elit nibh, tincidunt ac tempor eget, viverra nec metus. Vivamus mattis mi ut orci facilisis quis vestibulum ipsum facilisis. Nullam non dui massa. Ut in consectetur metus. Donec pretium nisi a magna dictum egestas. Cras id nunc at nisi vehicula rutrum.</p>\r\n<p>Proin semper magna a ligula lacinia lobortis pharetra tellus pulvinar. Integer suscipit aliquam odio, id condimentum lectus pharetra eget. Quisque tempus lacus et felis mollis dictum. Donec ante leo, feugiat a placerat nec, ullamcorper eget neque. Vivamus faucibus auctor urna, vel adipiscing erat posuere a. Integer malesuada semper purus in ornare. Donec libero lectus, viverra id condimentum eu, pharetra a mi. Quisque dapibus elit vitae turpis ullamcorper consequat. Praesent tellus eros, ullamcorper sit amet feugiat eget, egestas vitae mi. Maecenas tincidunt, mauris non aliquet tincidunt, lacus sapien rutrum enim, id venenatis massa nulla et tortor.</p>', 'deportes, curabitur, in, est, ipsum, et, pulvinar, diam, neque, porro, quisquam, qui, dolorem, quia, dolor, sit, amet, consectetur, adipisci, velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-13 01:49:05', 1, 7, 0, 3, 3, 1, 1, 0, 0, 100, 'quisque-vestibulum-eros-nec-libero-aliquam-consectetur', 1, NULL, 'deportes', NULL, NULL),
(63, 1, 'article', 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...', '', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:23:57', 1, 7, 0, 7, 3, 1, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, NULL, 'cultura', NULL, NULL),
(64, 1, 'article', 'Nulla mi odio, sodales vel porta ut, porttitor vulputate dui.', 'Praesent mi est, sodales quis pellentesque non, viverra non neque. Nunc imperdiet, dui euismod convallis accumsan, augue elit hendrerit risus, vel tempus eros est quis felis. Praesent eros turpis, cursus placerat cursus id, aliquam sit amet urna. Etiam non nibh in nisl aliquam fermentum eu a eros. Nulla mi...', '<p>Praesent mi est, sodales quis pellentesque non, viverra non neque. Nunc imperdiet, dui euismod convallis accumsan, augue elit hendrerit risus, vel tempus eros est quis felis. Praesent eros turpis, cursus placerat cursus id, aliquam sit amet urna. Etiam non nibh in nisl aliquam fermentum eu a eros. Nulla mi odio, sodales vel porta ut, porttitor vulputate dui. Suspendisse elementum, nisi aliquet ultrices porttitor, sapien purus euismod libero, ut pellentesque tortor ante a ante. Maecenas in leo eu tellus ullamcorper viverra viverra a orci.</p>\r\n<p>Maecenas cursus, odio scelerisque euismod bibendum, nunc turpis sollicitudin velit, in faucibus urna nisi a sem. In tempus ligula id dolor pulvinar ac consectetur ipsum volutpat. Quisque vulputate, nisi ac ornare dapibus, metus sapien porttitor massa, vitae euismod nulla lorem vel odio. Pellentesque vulputate elit sit amet lorem fermentum aliquet. Nullam dapibus egestas metus, sed hendrerit lorem euismod eget. Phasellus hendrerit ligula eget turpis facilisis rutrum. Morbi ultricies leo et nibh placerat ut dapibus mauris suscipit. Duis tempor lectus accumsan risus blandit porta. Integer porttitor ligula ut est rhoncus ut fringilla velit aliquet. Sed ante nulla, viverra non dignissim at, laoreet et diam. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nullam ornare, lectus sit amet egestas bibendum, mauris orci rutrum lectus, vitae faucibus nulla mi et neque. Mauris nec magna leo.</p>\r\n<p>Curabitur pretium lacus ut purus rutrum semper. Fusce vel purus at turpis viverra pretium. Phasellus pharetra erat vitae dui ornare dictum. Aenean nec magna eros. Phasellus eu urna justo. Curabitur sagittis diam nibh, eu consequat odio. Maecenas ligula diam, dictum in aliquet sed, tempor scelerisque arcu. Aliquam quam tortor, ultricies at varius et, tristique sed ligula. Praesent tincidunt sodales nulla, quis placerat nulla luctus adipiscing. Pellentesque lacinia, nulla nec aliquet sollicitudin, nisi elit bibendum dolor, vel malesuada risus lectus ut sem. Nulla in sem id velit vulputate rhoncus. Nulla facilisi. Vestibulum vel dolor odio. In congue porta tempor. Suspendisse luctus, elit ac consectetur porttitor, orci ipsum accumsan nunc, id imperdiet erat ante sit amet diam. Praesent aliquam cursus turpis at lobortis.</p>', 'deportes, nulla, odio, sodales, vel, porta, ut, porttitor, vulputate, dui, neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-13 01:34:47', 1, 7, 0, 3, 24, 1, 1, 0, 1, 1, 'nulla-mi-odio-sodales-vel-porta-ut-porttitor-vulputate-dui', 1, NULL, 'deportes', NULL, NULL),
(67, 1, 'article', 'Sed fermentum, dui eu ornare tincidunt, erat purus pretium elit, sed cursus dui enim in quam.', 'Curabitur sit amet nisi vehicula enim faucibus porttitor a a turpis. Aliquam aliquet, tortor vel tempus blandit, metus lorem sodales lorem, a vehicula metus eros quis massa. Sed dolor justo, aliquet vel fringilla ut, ullamcorper ut justo. Cras pulvinar tempor erat et pharetra. Etiam vitae mauris sed nibh tincidunt...', '<p>Curabitur sit amet nisi vehicula enim faucibus porttitor a a turpis. Aliquam aliquet, tortor vel tempus blandit, metus lorem sodales lorem, a vehicula metus eros quis massa. Sed dolor justo, aliquet vel fringilla ut, ullamcorper ut justo. Cras pulvinar tempor erat et pharetra. Etiam vitae mauris sed nibh tincidunt volutpat vitae ac lorem. Nunc ac ante at nisi sagittis posuere. In ac viverra lorem. Nullam eu quam odio. Etiam blandit elit vitae sem tincidunt sodales. Proin consectetur tempus sem et gravida. Aliquam non velit a arcu ornare lobortis vitae quis libero. In posuere dui vitae erat posuere at commodo erat egestas. Sed tempus egestas nisl, eget dictum felis rhoncus sed. Phasellus blandit, eros vitae dictum fringilla, augue augue cursus metus, id auctor nisi ligula id tellus. Phasellus nunc mauris, molestie vel gravida nec, condimentum quis turpis.&nbsp;</p>\r\n<p>Pellentesque eu lectus dui, quis pulvinar tellus. Phasellus id orci quam, fermentum facilisis dui. Vivamus a augue sit amet est bibendum aliquam ac ac nunc. Sed elementum gravida nisl, eget iaculis tellus eleifend et. Fusce sed mi orci, non porttitor diam. Phasellus vitae laoreet lectus. Aliquam pellentesque mattis nunc, at condimentum metus dignissim et. Praesent pulvinar, urna vel accumsan placerat, dui nunc pretium velit, sit amet scelerisque augue sapien in purus. Sed fermentum, dui eu ornare tincidunt, erat purus pretium elit, sed cursus dui enim in quam. Donec a arcu augue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nulla rutrum quam sed nisi consequat venenatis.</p>\r\n<p>&nbsp;</p>', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-11 20:04:07', 1, 7, 0, 3, 3, 1, 1, 0, 0, 100, 'sed-fermentum-dui-eu-ornare-tincidunt-erat-purus-pretium-elit-sed-cursus-dui-enim-in-quam', 1, NULL, 'politica', NULL, NULL),
(68, 1, 'article', 'Curabitur viverra, neque ac dapibus iaculis', 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,   vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend   bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,   congue id ante. Donec non sapien purusCurabitur viverra, neque ac...', '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,   vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend   bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,   congue id ante. Donec non sapien purusCurabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,   vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend   bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,   congue id ante. Donec non sapien purusCurabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,   vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend   bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,   congue id ante. Donec non sapien purus</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,   vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend   bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,   congue id ante. Donec non sapien purusCurabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,   vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend   bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,   congue id ante. Donec non sapien purus</p>', 'sociedad, curabitur, viverra, neque, ac, dapibus, iaculis, dui, tortor, urna, vel, ullamcorper, lacus, ut, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-13 01:59:01', 1, 7, 0, 3, 5, 1, 1, 0, 1, 1, 'curabitur-viverra-neque-ac-dapibus-iaculis', 1, NULL, 'sociedad', NULL, NULL),
(69, 1, 'article', 'Sit amet, consectetur, adipisci velit', 'Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla.\r\nCras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur...', '<p>Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla.</p>\r\n<p>Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla.Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla.Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla.Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla.</p>\r\n<p>Vestibulum venenatis elementum nulla.Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla.Cras metus dui, elementum id convallis vitae, feugiat nec nulla.</p>', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-11 19:21:58', 1, 7, 0, 3, 1, 2, 1, 0, 1, 1, 'sit-amet-consectetur-adipisci-velit', 1, NULL, 'deportes', NULL, NULL),
(70, 1, 'article', 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:38', 1, 7, 0, 7, 8, 100, 0, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, NULL, 'deportes', NULL, NULL),
(71, 1, 'article', 'Consectetur, adipisci velit', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas dui elit, vestibulum tincidunt aliquam sit amet, aliquam vel nunc. Nulla dolor mi, faucibus sed tempus eget, fermentum ac leo. Aliquam eget ligula a velit luctus sollicitudin quis in lacus. Etiam id bibendum mi. Curabitur ultricies lacinia rhoncus. Duis vel tellus...', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas dui elit, vestibulum tincidunt aliquam sit amet, aliquam vel nunc. Nulla dolor mi, faucibus sed tempus eget, fermentum ac leo. Aliquam eget ligula a velit luctus sollicitudin quis in lacus. Etiam id bibendum mi. Curabitur ultricies lacinia rhoncus. Duis vel tellus in augue vulputate hendrerit. Phasellus nisl leo, congue sit amet mollis nec, blandit pretium dui. Aenean iaculis adipiscing tortor non pulvinar. Quisque pharetra adipiscing neque, vitae tristique ligula sodales sed. Cras blandit condimentum facilisis. Ut quam nibh, convallis non iaculis pharetra, mattis non odio. Phasellus ipsum metus, viverra in imperdiet et, dictum sit amet libero. Cras vel erat purus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;</p>\r\n\r\n<p>Sed dictum tincidunt suscipit. Proin a tellus non lacus luctus sollicitudin. Sed turpis quam, tempor aliquam rhoncus vitae, dignissim a enim. Nulla facilisi. Praesent vel risus ac orci ultrices feugiat. Pellentesque malesuada rutrum nulla, sed rutrum nulla commodo quis. Duis egestas interdum velit, eget hendrerit justo porta ut. Nulla mollis auctor ante, eget semper quam interdum et. Integer laoreet urna a odio imperdiet a viverra urna pellentesque. Quisque eget nulla eu lacus varius porttitor. Nam sit amet dolor elit, ut auctor sapien. Sed urna purus, rutrum in sollicitudin a, condimentum tincidunt turpis.</p>\r\n\r\n<p>Aliquam varius tincidunt laoreet. Vivamus odio neque, vestibulum in posuere a, malesuada vitae leo. Aliquam id est ante. Nunc urna purus, interdum ut sollicitudin sed, adipiscing vitae lorem. Mauris porta quam quis tortor fermentum tempus. Suspendisse pellentesque leo risus. Nullam sed nulla tellus. Vestibulum adipiscing justo ac odio feugiat eget lacinia purus malesuada. Quisque sed mauris quis nunc consequat ornare a nec nibh. Vestibulum convallis eros at sapien sagittis porttitor. Aenean lacus lacus, feugiat vel euismod nec, imperdiet ut justo. Quisque tincidunt eros a magna pretium a ornare urna dignissim. Phasellus ut orci ligula, nec venenatis lacus. Nullam egestas accumsan facilisis. Vestibulum nunc erat, consequat a euismod sed, malesuada nec sapien.</p>\r\n', 'cultura,consectetur,adipisci,velit,neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2013-04-18 10:17:50', 1, 7, 0, 3, 1, 1, 1, 0, 0, 100, 'consectetur-adipisci-velit', 1, 'a:13:{s:14:"agencyBulletin";s:0:"";s:15:"imageHomeFooter";s:0:"";s:9:"imageHome";s:0:"";s:9:"titleSize";s:0:"";s:13:"imagePosition";s:0:"";s:9:"titleHome";s:0:"";s:13:"titleHomeSize";s:0:"";s:12:"subtitleHome";s:0:"";s:11:"summaryHome";s:0:"";s:17:"imageHomePosition";s:0:"";s:11:"withGallery";s:0:"";s:14:"withGalleryInt";s:0:"";s:15:"withGalleryHome";s:0:"";}', 'cultura', NULL, NULL);
INSERT INTO `contents` (`pk_content`, `fk_content_type`, `content_type_name`, `title`, `description`, `body`, `metadata`, `starttime`, `endtime`, `created`, `changed`, `content_status`, `fk_author`, `fk_publisher`, `fk_user_last_editor`, `views`, `position`, `frontpage`, `in_litter`, `in_home`, `home_pos`, `slug`, `available`, `params`, `category_name`, `favorite`, `urn_source`) VALUES
(72, 1, 'article', 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.', '&nbsp;\r\n\r\nProin id nisl nisi. Duis eget nisl ipsum, ut rhoncus erat. Pellentesque felis tellus, aliquet iaculis convallis eget, imperdiet ac risus. Phasellus eu enim lacus, id porta velit. Curabitur commodo ante vitae arcu fermentum faucibus. Aenean rhoncus, purus elementum posuere consequat, tortor erat auctor nibh, euismod mollis nibh metus...', '<p><span style="font-family: ''Times New Roman''; font-size: small;">&nbsp;</span></p>\r\n<div style="color: #000000; font-family: Arial, Verdana; font-size: 13px; background-image: initial; background-attachment: initial; background-origin: initial; background-clip: initial; background-color: #ffffff; background-position: initial initial; background-repeat: initial initial; margin: 8px;">\r\n<p>Proin id nisl nisi. Duis eget nisl ipsum, ut rhoncus erat. Pellentesque felis tellus, aliquet iaculis convallis eget, imperdiet ac risus. Phasellus eu enim lacus, id porta velit. Curabitur commodo ante vitae arcu fermentum faucibus. Aenean rhoncus, purus elementum posuere consequat, tortor erat auctor nibh, euismod mollis nibh metus id neque. Pellentesque sem magna, placerat quis mattis in, porttitor non arcu. Pellentesque id justo ipsum, at iaculis purus. Proin vel urna ligula. Praesent gravida convallis velit, sit amet egestas neque semper eget. Nam euismod massa eget odio interdum scelerisque. Sed non nibh risus, et mattis ligula. Aenean cursus euismod augue in tristique.</p>\r\n<p>Ut metus nunc, molestie vel tincidunt vel, elementum eget nulla. In risus elit, eleifend ac consequat ut, pellentesque sit amet risus. In eget mi in sapien scelerisque rutrum at sed mauris. Phasellus ullamcorper urna porta dui varius egestas ultricies ipsum tempus. Mauris justo turpis, lobortis id consectetur non, rhoncus pharetra quam. Duis id fermentum neque. Nulla facilisi. In hac habitasse platea dictumst. Morbi nec augue leo. Fusce sed orci elit, ac aliquam nunc. Etiam elementum, velit sit amet posuere posuere, mi sem fringilla purus, eu faucibus ligula sem eget massa. In euismod viverra est quis porta. Morbi faucibus aliquet elit id pretium. Cras nulla libero, ornare ut dictum sit amet, venenatis at turpis. Maecenas non fermentum lorem.&nbsp;</p>\r\n<p>Aliquam et turpis sed justo pulvinar euismod vel et massa. Mauris dictum aliquam aliquam. Sed porttitor convallis orci et sodales. Vivamus tempus varius scelerisque. Sed mi nibh, hendrerit sed commodo a, pretium id tortor. Sed vel magna eu mi condimentum convallis. Fusce at metus erat, eu gravida dui. Donec consequat suscipit eros sed euismod. In accumsan massa vel leo aliquet ac auctor arcu laoreet. Nulla hendrerit nisl quis lorem condimentum non imperdiet nulla consectetur. Mauris ante justo, cursus quis facilisis malesuada, dapibus nec mi. Donec id dolor quis urna feugiat interdum. Vestibulum ullamcorper commodo justo sed lacinia.&nbsp;</p>\r\n<p>Proin placerat mauris lobortis orci dignissim iaculis. Curabitur eget purus a erat pretium fringilla non non leo. Fusce imperdiet purus sit amet neque aliquet imperdiet venenatis sit amet ipsum. Maecenas vehicula justo a leo consequat non molestie mi varius. Integer vitae orci et erat viverra ultrices. Sed purus nulla, sagittis egestas placerat eget, eleifend eu est. Fusce in egestas eros. Aliquam libero augue, laoreet ac bibendum sit amet, euismod suscipit ante. Nam eget dui nunc, vel pellentesque justo. Etiam rhoncus molestie placerat. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Cras malesuada libero id erat cursus feugiat. Praesent non magna ut nulla tempus sagittis quis non mauris.</p>\r\n</div>\r\n<p>&nbsp;</p>', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-11 20:07:20', 1, 7, 0, 3, 2, 1, 1, 0, 0, 100, 'pellentesque-habitant-morbi-tristique-senectus-et-netus-et-malesuada-fames-ac-turpis-egestas', 1, NULL, 'politica', NULL, NULL),
(73, 1, 'article', 'Nunc eget elit placerat urna ultricies tincidunt.', 'Ut odio quam, luctus vitae congue vitae, congue sit amet turpis. Mauris lobortis, nisi et consequat adipiscing, mauris metus luctus purus, vel vehicula dolor lectus at magna. Donec fermentum semper libero eu scelerisque. Fusce mattis sagittis eros, eu suscipit elit suscipit eget. Nunc rhoncus quam sed eros rhoncus eleifend...', '<p>Ut odio quam, luctus vitae congue vitae, congue sit amet turpis. Mauris lobortis, nisi et consequat adipiscing, mauris metus luctus purus, vel vehicula dolor lectus at magna. Donec fermentum semper libero eu scelerisque. Fusce mattis sagittis eros, eu suscipit elit suscipit eget. Nunc rhoncus quam sed eros rhoncus eleifend eu eu dui. Donec dapibus laoreet augue, et commodo augue pellentesque et. Pellentesque volutpat scelerisque ultricies. Fusce ut tempor diam. Nunc purus sapien, ornare at aliquet in, eleifend vel mi.</p>\r\n<p>Duis placerat, enim nec tincidunt condimentum, ipsum orci malesuada erat, quis vestibulum ligula orci vel tellus. Sed pulvinar ullamcorper turpis, eu congue nisl congue non. Pellentesque varius, ante dignissim laoreet consectetur, purus mi sodales augue, ut eleifend erat turpis vitae turpis. Vestibulum hendrerit augue sed nunc cursus vulputate. Nullam in erat sit amet diam tempus porttitor. Donec venenatis ligula a purus elementum dignissim. Ut vitae lacus a purus pellentesque pellentesque eget ut est.</p>\r\n<p>Curabitur vel diam mauris. Praesent pharetra volutpat molestie. Nunc eget elit placerat urna ultricies tincidunt. Mauris et nibh elementum velit lobortis mollis eget eu odio. Sed convallis leo ut odio euismod vestibulum. Morbi aliquam hendrerit interdum. Nam eu felis ornare nisi condimentum accumsan. Curabitur bibendum accumsan varius. Praesent blandit, magna eu condimentum sollicitudin, odio eros gravida diam, posuere hendrerit mauris erat nec ligula. Sed quis eros quis urna cursus rutrum in tempus eros.</p>\r\n<p>Vestibulum eget velit dui. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nulla facilisi. Duis non feugiat lacus. Ut nec tempor orci. Aenean gravida vulputate ultricies. Sed eu est imperdiet neque viverra porta. Aliquam porta tempor hendrerit. Integer ut arcu ac mauris interdum dictum. Suspendisse varius egestas est, nec mattis nunc elementum a. Nullam molestie semper nunc, vitae tincidunt nibh rhoncus et. Aenean condimentum porttitor velit, vitae mattis tellus feugiat pharetra.</p>\r\n<p>Duis luctus, turpis ut elementum vulputate, eros mauris luctus nulla, sed feugiat ipsum urna non lectus. Quisque quam magna, pharetra id viverra ut, tempus vel arcu. Proin sodales porta magna, sit amet hendrerit quam bibendum id. Aliquam vel justo metus, eget ullamcorper tellus. Vestibulum quis orci id urna pellentesque accumsan. Aliquam ornare fringilla tristique. Ut fermentum elementum commodo. Vestibulum eu nisl orci, a ullamcorper erat. Aliquam iaculis felis ac neque ornare at fermentum enim fringilla. Suspendisse mattis eleifend velit quis tincidunt. Aliquam bibendum purus eget sem accumsan eu accumsan neque varius. Proin sodales, magna id gravida euismod, orci libero accumsan ligula, non tincidunt tortor sapien at felis.</p>', 'deportes, nunc, eget, elit, placerat, urna, ultricies, tincidunt, neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-11 20:33:42', 1, 7, 0, 3, 4, 1, 1, 0, 1, 3, 'nunc-eget-elit-placerat-urna-ultricies-tincidunt', 1, NULL, 'deportes', NULL, NULL),
(74, 1, 'article', 'Nam sit amet dolor elit, ut auctor sapien.', 'Sed dictum tincidunt suscipit. Proin a tellus non lacus luctus sollicitudin. Sed turpis quam, tempor aliquam rhoncus vitae, dignissim a enim. Nulla facilisi. Praesent vel risus ac orci ultrices feugiat. Pellentesque malesuada rutrum nulla, sed rutrum nulla commodo quis. Duis egestas interdum velit, eget hendrerit justo porta ut. Nulla...', '<p>Sed dictum tincidunt suscipit. Proin a tellus non lacus luctus sollicitudin. Sed turpis quam, tempor aliquam rhoncus vitae, dignissim a enim. Nulla facilisi. Praesent vel risus ac orci ultrices feugiat. Pellentesque malesuada rutrum nulla, sed rutrum nulla commodo quis. Duis egestas interdum velit, eget hendrerit justo porta ut. Nulla mollis auctor ante, eget semper quam interdum et. Integer laoreet urna a odio imperdiet a viverra urna pellentesque. Quisque eget nulla eu lacus varius porttitor. Nam sit amet dolor elit, ut auctor sapien. Sed urna purus, rutrum in sollicitudin a, condimentum tincidunt turpis.</p>\r\n<p>Aliquam varius tincidunt laoreet. Vivamus odio neque, vestibulum in posuere a, malesuada vitae leo. Aliquam id est ante. Nunc urna purus, interdum ut sollicitudin sed, adipiscing vitae lorem. Mauris porta quam quis tortor fermentum tempus. Suspendisse pellentesque leo risus. Nullam sed nulla tellus. Vestibulum adipiscing justo ac odio feugiat eget lacinia purus malesuada. Quisque sed mauris quis nunc consequat ornare a nec nibh. Vestibulum convallis eros at sapien sagittis porttitor. Aenean lacus lacus, feugiat vel euismod nec, imperdiet ut justo. Quisque tincidunt eros a magna pretium a ornare urna dignissim. Phasellus ut orci ligula, nec venenatis lacus. Nullam egestas accumsan facilisis. Vestibulum nunc erat, consequat a euismod sed, malesuada nec sapien.</p>\r\n<p>Nam tincidunt egestas lorem, non pretium mauris euismod sed. Aenean aliquam rutrum ipsum at vulputate. Donec aliquam massa ac elit adipiscing sit amet tincidunt massa euismod. Proin accumsan euismod lacus, vitae tempus justo dictum at. Sed venenatis consectetur metus, a elementum lorem volutpat ut. Mauris id neque mi, eu commodo eros. Quisque pulvinar semper mauris, quis posuere eros viverra id. Donec auctor mauris sed quam dictum molestie. Donec convallis nisi ut mauris egestas auctor. Fusce egestas lorem in ante vestibulum laoreet. Aliquam et risus lectus. Ut iaculis lacinia orci, ut posuere lorem dapibus et. Donec tincidunt cursus risus, in sodales ante tincidunt id. Sed id nibh nibh. Integer tempus augue vitae arcu mollis vitae ultrices eros malesuada.</p>\r\n<p>Nulla nisl ipsum, rutrum a luctus eu, congue quis velit. Integer pulvinar mattis semper. Ut rhoncus fermentum diam in aliquam. Etiam hendrerit dui a odio convallis commodo id vitae dui. Duis massa neque, ullamcorper in sodales et, adipiscing sed lectus. Donec hendrerit sodales erat vitae ultrices. Sed eget vestibulum lacus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>', 'cultura, nam, sit, amet, dolor, elit, ut, auctor, sapien, neque, porro, quisquam, est, qui, dolorem, ipsum, quia, consectetur, adipisci, velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-11 20:26:41', 1, 7, 0, 3, 3, 1, 1, 0, 0, 100, 'nam-sit-amet-dolor-elit-ut-auctor-sapien', 1, NULL, 'cultura', NULL, NULL),
(75, 1, 'article', 'Etiam blandit elit vitae sem tincidunt sodales.', 'Curabitur sit amet nisi vehicula enim faucibus porttitor a a turpis. Aliquam aliquet, tortor vel tempus blandit, metus lorem sodales lorem, a vehicula metus eros quis massa. Sed dolor justo, aliquet vel fringilla ut, ullamcorper ut justo. Cras pulvinar tempor erat et pharetra. Etiam vitae mauris sed nibh tincidunt...', '<p>Curabitur sit amet nisi vehicula enim faucibus porttitor a a turpis. Aliquam aliquet, tortor vel tempus blandit, metus lorem sodales lorem, a vehicula metus eros quis massa. Sed dolor justo, aliquet vel fringilla ut, ullamcorper ut justo. Cras pulvinar tempor erat et pharetra. Etiam vitae mauris sed nibh tincidunt volutpat vitae ac lorem. Nunc ac ante at nisi sagittis posuere. In ac viverra lorem. Nullam eu quam odio. Etiam blandit elit vitae sem tincidunt sodales. Proin consectetur tempus sem et gravida. Aliquam non velit a arcu ornare lobortis vitae quis libero. In posuere dui vitae erat posuere at commodo erat egestas. Sed tempus egestas nisl, eget dictum felis rhoncus sed. Phasellus blandit, eros vitae dictum fringilla, augue augue cursus metus, id auctor nisi ligula id tellus. Phasellus nunc mauris, molestie vel gravida nec, condimentum quis turpis.&nbsp;</p>\r\n<p>Vivamus at arcu nibh. Suspendisse sit amet ipsum ligula, nec cursus dolor. Nunc sit amet libero ante, tempor egestas lorem. Nullam dictum tincidunt risus id aliquet. Quisque feugiat gravida purus ut sodales. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam porta lobortis tempus. Aliquam imperdiet scelerisque odio non gravida. Integer eleifend lacus nec purus interdum ultricies. Donec egestas porttitor sem non feugiat. Aenean posuere venenatis nunc non tincidunt.&nbsp;</p>\r\n<p>Pellentesque eu lectus dui, quis pulvinar tellus. Phasellus id orci quam, fermentum facilisis dui. Vivamus a augue sit amet est bibendum aliquam ac ac nunc. Sed elementum gravida nisl, eget iaculis tellus eleifend et. Fusce sed mi orci, non porttitor diam. Phasellus vitae laoreet lectus. Aliquam pellentesque mattis nunc, at condimentum metus dignissim et. Praesent pulvinar, urna vel accumsan placerat, dui nunc pretium velit, sit amet scelerisque augue sapien in purus. Sed fermentum, dui eu ornare tincidunt, erat purus pretium elit, sed cursus dui enim in quam. Donec a arcu augue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nulla rutrum quam sed nisi consequat venenatis.</p>', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-11 20:02:12', 1, 7, 0, 3, 3, 1, 1, 0, 0, 100, 'etiam-blandit-elit-vitae-sem-tincidunt-sodales', 1, NULL, 'politica', NULL, NULL),
(76, 1, 'article', 'Duis vitae eros sed sem venenatis consequat imperdiet nec quam.', 'Morbi gravida varius orci iaculis semper. Aenean nisi ipsum, convallis at suscipit non, ornare ac lorem. Cras sed leo turpis, eu lacinia orci. Donec magna nunc, suscipit vitae condimentum sed, tincidunt nec tellus. Quisque convallis porta urna nec suscipit. Nulla eu tortor commodo nulla dignissim dignissim eget et mi....', '<p>Morbi gravida varius orci iaculis semper. Aenean nisi ipsum, convallis at suscipit non, ornare ac lorem. Cras sed leo turpis, eu lacinia orci. Donec magna nunc, suscipit vitae condimentum sed, tincidunt nec tellus. Quisque convallis porta urna nec suscipit. Nulla eu tortor commodo nulla dignissim dignissim eget et mi. Duis vitae eros sed sem venenatis consequat imperdiet nec quam. Nunc commodo consectetur malesuada.</p>\r\n<p>Nam lobortis leo ut quam suscipit malesuada accumsan massa posuere. Aliquam non ante nec sem convallis rutrum in in purus. Nam id magna ante. Vestibulum egestas ultrices ultricies. Morbi luctus ante eu metus dapibus a rhoncus felis blandit. Quisque vitae nulla quis lacus gravida feugiat. Phasellus consequat est aliquet lectus blandit a consequat purus viverra. Vestibulum a neque arcu.</p>\r\n<p>Proin non lacus vitae libero ultrices ultricies. Ut elit magna, pretium nec condimentum nec, vestibulum eget felis. Aliquam erat volutpat. Pellentesque vehicula tortor venenatis eros ultricies iaculis quis aliquet nunc. Nulla eros nunc, faucibus nec adipiscing vel, blandit eget libero. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Sed ut vulputate dui. Phasellus condimentum nulla eget nisl gravida dignissim pretium libero pulvinar. Curabitur quis malesuada sapien. Nunc arcu mauris, tristique at hendrerit sit amet, dictum sit amet libero. Nunc accumsan sem placerat est imperdiet feugiat gravida massa mattis. Suspendisse ac tellus libero. Nam placerat egestas odio, placerat rutrum risus vestibulum ut. Vestibulum pretium pharetra vehicula.</p>\r\n<p>Integer luctus diam ipsum, non ullamcorper nisi. Mauris malesuada molestie dui, ut lobortis quam rhoncus a. Cras elementum metus ut libero fermentum vulputate. Fusce molestie felis erat. Aliquam eu nulla velit, at pellentesque nulla. Quisque id velit sapien. Integer blandit vulputate mattis. Phasellus dignissim tempor lorem, dapibus porttitor lectus hendrerit ac. Maecenas lacinia justo non ligula luctus convallis. Ut vehicula nunc id erat tincidunt sagittis. Pellentesque non nibh nec urna ultricies ullamcorper. Etiam quis ligula quam, sit amet facilisis ipsum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Ut pretium leo ac quam congue euismod. Vestibulum sed nisi risus.</p>', 'sociedad, duis, vitae, eros, sed, sem, venenatis, consequat, imperdiet, nec, quam, neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-13 01:57:49', 1, 7, 0, 3, 2, 1, 1, 0, 2, 100, 'duis-vitae-eros-sed-sem-venenatis-consequat-imperdiet-nec-quam', 1, NULL, 'sociedad', NULL, NULL),
(77, 1, 'article', 'Iipsum quia dolor sit amet, consectetur, adipisci velit', 'Morbi aliquam hendrerit interdum. Nam eu felis ornare nisi condimentum accumsan. Curabitur bibendum accumsan varius. Praesent blandit, magna eu condimentum sollicitudin, odio eros gravida diam, posuere hendrerit mauris erat nec ligula. Sed quis eros quis urna cursus rutrum in tempus eros.\r\n\r\nMorbi aliquam hendrerit interdum. Nam eu felis ornare nisi condimentum...', '<p>Morbi aliquam hendrerit interdum. Nam eu felis ornare nisi condimentum accumsan. Curabitur bibendum accumsan varius. Praesent blandit, magna eu condimentum sollicitudin, odio eros gravida diam, posuere hendrerit mauris erat nec ligula. Sed quis eros quis urna cursus rutrum in tempus eros.</p>\r\n\r\n<p>Morbi aliquam hendrerit interdum. Nam eu felis ornare nisi condimentum accumsan. Curabitur bibendum accumsan varius. Praesent blandit, magna eu condimentum sollicitudin, odio eros gravida diam, posuere hendrerit mauris erat nec ligula. Sed quis eros quis urna cursus rutrum in tempus eros.Morbi aliquam hendrerit interdum. Nam eu felis ornare nisi condimentum accumsan. Curabitur bibendum accumsan varius. Praesent blandit, magna eu condimentum sollicitudin, odio eros gravida diam, posuere hendrerit mauris erat nec ligula. Sed quis eros quis urna cursus rutrum in tempus eros.Morbi aliquam hendrerit interdum. Nam eu felis ornare nisi condimentum accumsan. Curabitur bibendum accumsan varius. Praesent blandit, magna eu condimentum sollicitudin, odio eros gravida diam, posuere hendrerit mauris erat nec ligula. Sed quis eros quis urna cursus rutrum in tempus eros.</p>\r\n\r\n<p>Morbi aliquam hendrerit interdum. Nam eu felis ornare nisi condimentum accumsan. Curabitur bibendum accumsan varius. Praesent blandit, magna eu condimentum sollicitudin, odio eros gravida diam, posuere hendrerit mauris erat nec ligula. Sed quis eros quis urna cursus rutrum in tempus eros.</p>\r\n\r\n<p>Morbi aliquam hendrerit interdum. Nam eu felis ornare nisi condimentum accumsan. Curabitur bibendum accumsan varius. Praesent blandit, magna eu condimentum sollicitudin, odio eros gravida diam, posuere hendrerit mauris erat nec ligula. Sed quis eros quis urna cursus rutrum in tempus eros.</p>\r\n', 'iipsum,quia,dolor,sit,amet,consectetur,adipisci,velit,deportes,neque,porro,quisquam,est,qui,dolorem,ipsum', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2013-04-18 10:08:09', 1, 7, 0, 3, 1, 100, 0, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'a:13:{s:14:"agencyBulletin";s:0:"";s:15:"imageHomeFooter";s:0:"";s:9:"imageHome";s:0:"";s:9:"titleSize";s:0:"";s:13:"imagePosition";s:0:"";s:9:"titleHome";s:0:"";s:13:"titleHomeSize";s:0:"";s:12:"subtitleHome";s:0:"";s:11:"summaryHome";s:0:"";s:17:"imageHomePosition";s:0:"";s:11:"withGallery";s:0:"";s:14:"withGalleryInt";s:0:"";s:15:"withGalleryHome";s:0:"";}', 'deportes', NULL, NULL),
(78, 1, 'article', 'Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit', '', '', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-09-27 15:28:45', 1, 7, 0, 7, 4, 1, 1, 0, 0, 1, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, NULL, 'deportes', NULL, NULL),
(79, 1, 'article', 'Etiam id bibendum mi', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas dui elit, vestibulum tincidunt aliquam sit amet, aliquam vel nunc. Nulla dolor mi, faucibus sed tempus eget, fermentum ac leo. Aliquam eget ligula a velit luctus sollicitudin quis in lacus. Etiam id bibendum mi. Curabitur ultricies lacinia rhoncus. Duis vel...', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas dui elit, vestibulum tincidunt aliquam sit amet, aliquam vel nunc. Nulla dolor mi, faucibus sed tempus eget, fermentum ac leo. Aliquam eget ligula a velit luctus sollicitudin quis in lacus. Etiam id bibendum mi. Curabitur ultricies lacinia rhoncus. Duis vel tellus in augue vulputate hendrerit. Phasellus nisl leo, congue sit amet mollis nec, blandit pretium dui. Aenean iaculis adipiscing tortor non pulvinar. Quisque pharetra adipiscing neque, vitae tristique ligula sodales sed. Cras blandit condimentum facilisis. Ut quam nibh, convallis non iaculis pharetra, mattis non odio. Phasellus ipsum metus, viverra in imperdiet et, dictum sit amet libero. Cras vel erat purus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;</p>\r\n<p>Sed dictum tincidunt suscipit. Proin a tellus non lacus luctus sollicitudin. Sed turpis quam, tempor aliquam rhoncus vitae, dignissim a enim. Nulla facilisi. Praesent vel risus ac orci ultrices feugiat. Pellentesque malesuada rutrum nulla, sed rutrum nulla commodo quis. Duis egestas interdum velit, eget hendrerit justo porta ut. Nulla mollis auctor ante, eget semper quam interdum et. Integer laoreet urna a odio imperdiet a viverra urna pellentesque. Quisque eget nulla eu lacus varius porttitor. Nam sit amet dolor elit, ut auctor sapien. Sed urna purus, rutrum in sollicitudin a, condimentum tincidunt turpis.</p>\r\n<p>Aliquam varius tincidunt laoreet. Vivamus odio neque, vestibulum in posuere a, malesuada vitae leo. Aliquam id est ante. Nunc urna purus, interdum ut sollicitudin sed, adipiscing vitae lorem. Mauris porta quam quis tortor fermentum tempus. Suspendisse pellentesque leo risus. Nullam sed nulla tellus. Vestibulum adipiscing justo ac odio feugiat eget lacinia purus malesuada. Quisque sed mauris quis nunc consequat ornare a nec nibh. Vestibulum convallis eros at sapien sagittis porttitor. Aenean lacus lacus, feugiat vel euismod nec, imperdiet ut justo. Quisque tincidunt eros a magna pretium a ornare urna dignissim. Phasellus ut orci ligula, nec venenatis lacus. Nullam egestas accumsan facilisis. Vestibulum nunc erat, consequat a euismod sed, malesuada nec sapien.</p>', 'neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2011-10-11 20:26:41', 1, 7, 0, 3, 2, 5, 1, 0, 0, 100, 'etiam-id-bibendum-mi', 1, NULL, 'cultura', NULL, NULL),
(80, 1, 'article', 'Dolorem ipsum quia dolor sit amet, consectetur, adipisci velit.', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet...', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n', 'dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit,polÃ­tica,neque,porro,quisquam,est,qui', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2013-04-18 09:50:55', 1, 7, 0, 3, 5, 1, 1, 0, 0, 100, 'neque-porro-quisquam-est-qui-dolorem-ipsum-quia-dolor-sit-amet-consectetur-adipisci-velit', 1, 'a:13:{s:14:"agencyBulletin";s:0:"";s:15:"imageHomeFooter";s:0:"";s:9:"imageHome";s:0:"";s:9:"titleSize";s:0:"";s:13:"imagePosition";s:0:"";s:9:"titleHome";s:0:"";s:13:"titleHomeSize";s:0:"";s:12:"subtitleHome";s:0:"";s:11:"summaryHome";s:0:"";s:17:"imageHomePosition";s:0:"";s:11:"withGallery";s:0:"";s:14:"withGalleryInt";s:0:"";s:15:"withGalleryHome";s:0:"";}', 'politica', NULL, NULL),
(81, 1, 'article', 'Cras iaculis venenatis laoreet. Quisque vitae nunc purus', 'Phasellus vehicula neque et dolor tristique pretium. Vestibulum molestie malesuada eros ac tristique. Phasellus vitae faucibus ligula. Nulla lacus velit, lacinia vitae accumsan at, consectetur at nisl. Proin tempor diam id eros volutpat laoreet. Donec euismod venenatis ornare. Aliquam at neque sem, quis blandit nisi. Quisque id ipsum ante. Praesent...', '<p>Phasellus vehicula neque et dolor tristique pretium. Vestibulum molestie malesuada eros ac tristique. Phasellus vitae faucibus ligula. Nulla lacus velit, lacinia vitae accumsan at, consectetur at nisl. Proin tempor diam id eros volutpat laoreet. Donec euismod venenatis ornare. Aliquam at neque sem, quis blandit nisi. Quisque id ipsum ante. Praesent dictum malesuada metus, ac dapibus nisi vehicula sed. Aliquam consectetur ullamcorper tempor. Nulla facilisi. Morbi adipiscing sagittis justo vel elementum. Duis ac metus augue, in cursus enim. Cras quis risus a felis pharetra ullamcorper. Nulla molestie adipiscing arcu, convallis volutpat lorem fringilla eu. Curabitur euismod nunc nec risus lacinia consectetur.</p>\r\n\r\n<p>Nullam ac eros quis ligula imperdiet rhoncus ut eget velit. Aenean porta lacinia risus, vel faucibus orci vehicula vitae. Sed consequat sagittis nisl sit amet vehicula. Phasellus id nulla nec felis tempus sollicitudin sed non justo. Cras iaculis venenatis laoreet. Quisque vitae nunc purus. Donec porta hendrerit sem, vitae lacinia felis blandit at. Nunc leo massa, venenatis quis luctus ac, placerat quis ipsum. Fusce feugiat luctus tortor at semper. Donec orci nisl, egestas in tincidunt et, accumsan id lorem. Sed lorem dolor, dapibus tincidunt posuere in, tincidunt pharetra ante. Pellentesque varius tellus id ante gravida eu faucibus lacus elementum. Nulla semper, nisl nec pellentesque mollis, tortor elit dignissim metus, at consectetur eros felis eu risus.</p>\r\n\r\n<p>Pellentesque blandit sem non neque blandit vitae varius orci volutpat. Cras varius scelerisque gravida. Mauris leo sem, feugiat eget aliquet eget, ullamcorper eget felis. Nulla bibendum, orci quis cursus accumsan, mi sapien malesuada libero, vitae suscipit enim tellus vitae dui. Aliquam urna purus, congue vel mattis sit amet, aliquet a dolor. Pellentesque in dui id risus blandit tristique ac nec felis. Donec nisl justo, volutpat fermentum aliquet nec, gravida sed tellus. Sed eu dolor augue, ac adipiscing nisl. Cras tempus euismod magna, pharetra pulvinar nisi euismod quis. Nunc vitae turpis sem, nec scelerisque diam. Proin iaculis velit sit amet enim blandit eget commodo velit lacinia.</p>\r\n\r\n<p>Fusce eleifend posuere lectus, id pharetra mauris auctor quis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Cras lacinia aliquet nunc nec commodo. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi egestas volutpat diam, ut cursus augue euismod ut. Curabitur porta, magna vitae bibendum porttitor, lorem nunc elementum nisi, iaculis vulputate massa tellus nec nunc. Ut aliquet, nisl eget lacinia eleifend, nulla ante rutrum nibh, sit amet posuere diam magna a felis. Nullam rhoncus quam in ipsum dignissim varius. In vel tellus enim. Etiam nulla dolor, rhoncus in dignissim id, suscipit et nisi. Pellentesque ligula mauris, eleifend vel commodo quis, ornare ac erat. Integer ultricies, quam vel egestas ornare, neque neque vehicula augue, eu pharetra arcu odio posuere nunc. Nulla ullamcorper sem et lorem ultrices volutpat. In congue est id mauris tempor sagittis. Donec gravida feugiat rutrum.</p>\r\n', 'deportes,cras,iaculis,venenatis,laoreet,quisque,vitae,nunc,purus,neque,porro,quisquam,est,qui,dolorem,ipsum,quia,dolor,sit,amet,consectetur,adipisci,velit', '2011-09-23 20:51:11', '0000-00-00 00:00:00', '2011-09-23 20:51:11', '2013-04-18 10:11:19', 1, 7, 0, 3, 3, 1, 1, 0, 1, 3, 'cras-iaculis-venenatis-laoreet-quisque-vitae-nunc-purus', 1, 'a:13:{s:14:"agencyBulletin";s:0:"";s:15:"imageHomeFooter";s:0:"";s:9:"imageHome";s:0:"";s:9:"titleSize";s:0:"";s:13:"imagePosition";s:0:"";s:9:"titleHome";s:0:"";s:13:"titleHomeSize";s:0:"";s:12:"subtitleHome";s:0:"";s:11:"summaryHome";s:0:"";s:17:"imageHomePosition";s:0:"";s:11:"withGallery";s:0:"";s:14:"withGalleryInt";s:0:"";s:15:"withGalleryHome";s:0:"";}', 'deportes', NULL, NULL),
(82, 1, 'article', 'Nam viverra auctor orci id accumsan.', 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut...', '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>', '', '2011-09-27 15:25:40', '0000-00-00 00:00:00', '2011-09-27 15:25:40', '2013-06-17 18:03:08', 1, 7, 0, 3, 44, 1, 0, 0, 1, 2, 'nam-viverra-auctor-orci-id-accumsan', 1, NULL, 'sociedad', NULL, NULL),
(84, 1, 'article', 'Nam viverra auctor orci id accumsan.', 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut...', '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>', 'sociedad, nam, viverra, auctor, orci, id, accumsan, unknown', '2011-09-27 15:25:40', '0000-00-00 00:00:00', '2011-09-27 15:25:40', '2013-06-17 18:03:08', 1, 7, 0, 3, 9, 1, 0, 0, 0, 100, 'nam-viverra-auctor-orci-id-accumsan', 1, NULL, 'sociedad', NULL, NULL),
(85, 1, 'article', 'Donec neque metus, scelerisque sit amet porttitor vel', 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut...', '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>', 'polÃ­tica, donec, neque, metus, scelerisque, sit, amet, porttitor, vel, adipiscing, pellentesque, felis, nam, viverra, auctor, orci, id, accumsan', '2011-09-27 15:25:40', '0000-00-00 00:00:00', '2011-09-27 15:25:40', '2013-06-17 18:03:08', 1, 7, 0, 3, 3, 1, 0, 0, 1, 1, 'donec-neque-metus-scelerisque-sit-amet-porttitor-vel', 1, NULL, 'politica', NULL, NULL),
(86, 1, 'article', 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna, vel ullamcorper dui lacus ut urna', 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut...', '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>\r\n<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>', 'neque, porro, quisquam, est, qui, dolorem, ipsum, quia, dolor, sit, amet, consectetur, adipisci, velit', '2011-09-27 15:25:40', '0000-00-00 00:00:00', '2011-09-27 15:25:40', '2012-10-01 12:46:05', 1, 7, 0, 7, 22, 1, 0, 0, 1, 1, 'curabitur-viverra-neque-ac-dapibus-iaculis-dui-tortor-dapibus-urna-vel-ullamcorper-dui-lacus-ut-urna', 1, NULL, 'politica', NULL, NULL),
(87, 1, 'article', 'Aenean vehicula condimentum dui, at vehicula risus porttitor quis', 'Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut...', '<p>Curabitur viverra, neque ac dapibus iaculis, dui tortor dapibus urna,  vel ullamcorper dui lacus ut urna. Nulla sapien lorem, gravida eleifend  bibendum a, tempor et mi. Nullam ante justo, interdum at interdum vel,  congue id ante. Donec non sapien purus. Pellentesque dignissim elementum  arcu ut porta. Duis et enim felis. Maecenas risus arcu, fermentum  pellentesque dapibus sed, sollicitudin pretium dolor. Praesent placerat,  elit ut fringilla venenatis, sem turpis porttitor orci, sed convallis  sapien tortor in libero. Aenean vehicula condimentum dui, at vehicula  risus porttitor quis.</p>\r\n<p>Donec neque metus, scelerisque sit amet porttitor vel, adipiscing  pellentesque felis. Nunc orci metus, dictum id viverra a, ultricies a  dui. Vestibulum faucibus luctus turpis, ac lacinia magna semper a. Nam  lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse  potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra  eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor  urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut  sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem  ipsum.</p>\r\n<p>Nam viverra auctor orci id accumsan. Praesent libero tortor, condimentum  et tristique ut, luctus ut enim. Praesent non neque eget est convallis  vehicula. Integer turpis nisl, bibendum non rutrum ornare, faucibus at  sapien. Curabitur eu magna eget nisi tincidunt lobortis non vitae purus.  In semper, tellus et pellentesque viverra, ligula erat pellentesque  ligula, quis porta tortor elit vel tortor. Pellentesque vel tincidunt  lorem. Integer in erat lectus. In accumsan dictum ipsum, id molestie  felis rhoncus et. Fusce elementum odio et felis aliquam tincidunt porta  ac massa. Suspendisse at magna justo, a tempor lorem. Morbi ligula eros,  blandit nec placerat et, luctus et purus. Praesent quis elit turpis.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas.</p>', 'polÃ­tica, aenean, vehicula, condimentum, dui, at, risus, porttitor, quis, nam, viverra, auctor, orci, id, accumsan', '2011-09-27 15:25:40', '0000-00-00 00:00:00', '2011-09-27 15:25:40', '2012-10-01 12:46:05', 1, 7, 0, 7, 10, 1, 0, 0, 1, 1, 'aenean-vehicula-condimentum-dui-at-vehicula-risus-porttitor-quis', 1, NULL, 'politica', NULL, NULL),
(88, 7, 'album', 'dfgf', '', '', 'dfgf', '2011-09-27 23:31:57', '0000-00-00 00:00:00', '2011-09-27 23:31:57', '2011-09-27 23:31:57', 0, 7, 0, 7, 1, 2, 0, 0, 0, 100, 'dfgf', 0, NULL, 'sociedad', NULL, NULL),
(89, 7, 'album', 'Etiam sed venenatis libero.', 'Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. ', '', 'etiam, sed, venenatis, libero, donec, augue, mauris, sdf', '2011-09-28 10:05:55', '0000-00-00 00:00:00', '2011-09-28 10:05:55', '2011-10-04 18:28:45', 1, 7, 0, 16, 3, 2, 0, 0, 0, 100, 'etiam-sed-venenatis-libero', 1, NULL, 'deportes', 1, NULL),
(90, 7, 'album', 'Donec ultricies tincidunt ultrices', 'Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum.', '', 'donec, ultricies, tincidunt, ultrices', '2011-09-28 10:05:55', '0000-00-00 00:00:00', '2011-09-28 10:05:55', '2011-10-04 18:28:57', 1, 7, 0, 16, 1, 2, 0, 0, 1, 100, 'donec-ultricies-tincidunt-ultrices', 1, NULL, 'deportes', 1, NULL);
INSERT INTO `contents` (`pk_content`, `fk_content_type`, `content_type_name`, `title`, `description`, `body`, `metadata`, `starttime`, `endtime`, `created`, `changed`, `content_status`, `fk_author`, `fk_publisher`, `fk_user_last_editor`, `views`, `position`, `frontpage`, `in_litter`, `in_home`, `home_pos`, `slug`, `available`, `params`, `category_name`, `favorite`, `urn_source`) VALUES
(91, 7, 'album', 'Donec ultricies tincidunt ultrices. ', ' Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum', '', 'donec, ultricies, tincidunt, ultrices, pruebasdf', '2011-09-28 10:31:05', '0000-00-00 00:00:00', '2011-09-28 10:31:05', '2011-10-04 18:28:02', 0, 7, 0, 16, 1, 2, 0, 0, 1, 100, 'donec-ultricies-tincidunt-ultrices', 1, NULL, 'sociedad', 0, NULL),
(92, 7, 'album', 'Nam lobortis nibh eu ante molestie nec condimentum justo semper. ', 'Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero.  ', '', 'nam, lobortis, nibh, eu, molestie, nec, condimentum, justo, semper, mascotas, divertidas', '2011-09-28 11:21:56', '0000-00-00 00:00:00', '2011-09-28 11:21:56', '2011-09-28 11:22:46', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'nam-lobortis-nibh-eu-ante-molestie-nec-condimentum-justo-semper', 1, NULL, 'sociedad', NULL, NULL),
(93, 7, 'album', 'Donec augue mauris ', 'Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero.  ', '', 'donec, augue, mauris, nam, lobortism, justo, semper, lobortis, nibh, eu, molestie, nec, condimentum, mascotas, divertidas', '2011-09-28 11:21:56', '0000-00-00 00:00:00', '2011-09-28 11:21:56', '2013-04-03 20:53:04', 0, 7, 7, 3, 4, 2, 0, 0, 1, 100, 'donec-augue-mauris', 1, NULL, 'sociedad', 1, NULL),
(94, 7, 'album', 'Vivamus accumsan sem ipsum.', 'Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum.', '', 'vivamus, accumsan, sem, ipsum', '2011-09-28 11:32:37', '0000-00-00 00:00:00', '2011-09-28 11:32:37', '2011-10-05 23:58:48', 0, 7, 7, 7, 16, 2, 0, 0, 0, 100, 'vivamus-accumsan-sem-ipsum', 1, NULL, 'fotos-de-hoy', 1, NULL),
(95, 9, 'video', 'Sheeped Away', 'Sheeped Away', '', 'sheeped, away', '2011-09-28 12:47:30', '0000-00-00 00:00:00', '2011-09-28 12:47:30', '2011-09-28 12:47:30', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'sheeped-away', 1, NULL, 'curiosidades', 1, NULL),
(96, 9, 'video', '13957 people dancing thriller in Mexico 1 of 2 HQ', '13957 people dancing thriller in Mexico 1 of 2 HQ', '', 'people, dancing, thriller, in, mexico, of, hq, ', '2011-09-28 12:48:51', '0000-00-00 00:00:00', '2011-09-28 12:48:51', '2011-09-28 12:48:51', 1, 7, 7, 7, 5, 2, 0, 0, 0, 100, '13957-people-dancing-thriller-in-mexico-1-of-2-hq', 1, NULL, 'musica', NULL, NULL),
(97, 9, 'video', 'Audi S3 Model 2009', 'Audi S3 Model 2009', '', 'audi, s3, model', '2011-09-28 12:51:11', '0000-00-00 00:00:00', '2011-09-28 12:51:11', '2011-09-28 12:51:11', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'audi-s3-model-2009', 1, NULL, 'sociedad', NULL, NULL),
(98, 9, 'video', 'Real Scenes: Detroit', 'http://vimeo.com/27476225', '', 'real, scenes, detroit', '2011-09-28 12:51:28', '0000-00-00 00:00:00', '2011-09-28 12:51:28', '2011-09-28 12:51:28', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'real-scenes-detroit', 1, NULL, 'sociedad', 0, NULL),
(99, 9, 'video', 'Coldplay - Every Teardrop Is A Waterfall', 'cColdplay - Every Teardrop Is A Waterfall', '', 'coldplay, every, teardrop, is, waterfall', '2011-09-28 12:52:27', '0000-00-00 00:00:00', '2011-09-28 12:52:27', '2011-09-28 12:52:27', 1, 7, 7, 7, 35, 2, 0, 0, 0, 100, 'coldplay-every-teardrop-is-a-waterfall', 1, NULL, 'musica', 1, NULL),
(100, 9, 'video', 'Marilyn Monroe Happy Birthday Mr President', 'Marilyn Monroe Happy Birthday Mr President', '', 'marilyn, monroe, happy, birthday, mr, president', '2011-09-28 12:55:57', '0000-00-00 00:00:00', '2011-09-28 12:55:57', '2011-09-28 12:55:57', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'marilyn-monroe-happy-birthday-mr-president', 1, NULL, 'sociedad', NULL, NULL),
(101, 9, 'video', 'Trailer Millennium: Los hombres que no amaban a las mujeres', 'Trailer Millennium: Los hombres que no amaban a las mujeres', '', 'Trailer, Millennium', '2011-09-28 12:56:40', '0000-00-00 00:00:00', '2011-09-28 12:56:40', '2011-09-28 12:56:40', 1, 7, 7, 7, 2, 2, 0, 0, 0, 100, 'trailer-millennium-los-hombres-que-no-amaban-a-las-mujeres', 1, NULL, 'sociedad', NULL, NULL),
(102, 9, 'video', 'Mystique', 'mystique', '', 'mystique', '2011-09-28 12:57:25', '0000-00-00 00:00:00', '2011-09-28 12:57:25', '2013-04-03 20:53:04', 1, 7, 7, 3, 7, 2, 0, 0, 1, 100, 'mystique', 1, NULL, 'sociedad', 1, NULL),
(103, 9, 'video', 'How to Get Fit - P90X Video with Tony Horton!', 'p90x', '', 'how, to, get, fit, p90x, video, with, tony, horton', '2011-09-28 12:58:48', '0000-00-00 00:00:00', '2011-09-28 12:58:48', '2011-09-28 12:58:48', 1, 7, 7, 7, 13, 2, 0, 0, 0, 100, 'how-to-get-fit-p90x-video-with-tony-horton', 1, NULL, 'sociedad', 1, NULL),
(104, 9, 'video', 'Larry Johnson, la ''abuela'' mÃ¡s famosa de la NBA', 'Larry Johnson, la ''abuela'' mÃ¡s famosa de la NBA', '', 'larry, johnson, abuela, mÃ¡s, famosa, nba', '2011-09-28 13:04:58', '0000-00-00 00:00:00', '2011-09-28 13:04:58', '2011-09-28 13:04:58', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'larry-johnson-la-abuela-mas-famosa-de-la-nba', 1, NULL, 'deportes', NULL, NULL),
(105, 9, 'video', 'Video de la cabaÃ±a argentina', 'Video de la cabaÃ±a argentina', '', 'video, cabaÃ±a, argentina, deo, caba', '2011-09-28 13:10:34', '0000-00-00 00:00:00', '2011-09-28 13:10:34', '2011-09-28 13:10:34', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'video-de-la-cabana-argentina', 1, NULL, 'curiosidades', NULL, NULL),
(106, 9, 'video', 'video con servicio de Rutube', 'video con servicio de Rutube', '', 'rutube', '2011-09-28 13:11:10', '0000-00-00 00:00:00', '2011-09-28 13:11:10', '2011-09-28 13:11:10', 1, 7, 7, 7, 1, 2, 0, 0, 0, 100, '', 1, NULL, 'sociedad', NULL, NULL),
(107, 9, 'video', 'Pau entra por la puerta grande en el club de los 10.000', 'Pau entra por la puerta grande en el club de los 10.000', '', 'pau, entra, puerta, grande, club, 000', '2011-09-28 13:14:00', '0000-00-00 00:00:00', '2011-09-28 13:14:00', '2012-07-27 11:18:30', 1, 7, 7, 7, 7, 2, 0, 0, 1, 100, 'pau-entra-por-la-puerta-grande-en-el-club-de-los-10-000', 1, NULL, 'deportes', NULL, NULL),
(108, 9, 'video', 'Camelos.Semos. Jonathan. TÃº si que vales.', 'Camelos.Semos. Jonathan. TÃº si que vales.', '', 'camelos, semos, jonathan, tÃº, vales', '2012-07-30 10:17:50', '0000-00-00 00:00:00', '2011-09-28 13:14:30', '2011-09-28 13:14:30', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'camelos-semos-jonathan-tu-si-que-vales', 0, NULL, 'curiosidades', 0, NULL),
(109, 9, 'video', 'X men first class reviewed by rotten tomatoes on infomania', 'X men first class reviewed by rotten tomatoes on infomania', '', 'men, first, class, reviewed, by, rotten, tomatoes, on, infomania, neither, user, id, nor, item, is, specified, for, rss, feed', '2011-09-28 13:16:05', '0000-00-00 00:00:00', '2011-09-28 13:16:05', '2013-06-17 16:03:30', 0, 7, 7, 5, 5, 2, 0, 1, 0, 100, 'x-men-first-class-reviewed-by-rotten-tomatoes-on-infomania', 0, NULL, 'sociedad', NULL, NULL),
(110, 9, 'video', 'Discurso de Ãlex de la Iglesia en los Goya 2011', 'Discurso de Ãlex de la Iglesia en los Goya 2011', '', 'discurso, Ã¡lex, iglesia, goya', '2011-09-28 13:16:18', '0000-00-00 00:00:00', '2011-09-28 13:16:18', '2013-04-03 20:53:04', 1, 7, 7, 3, 105, 2, 0, 0, 1, 100, 'discurso-de-alex-de-la-iglesia-en-los-goya-2011', 1, NULL, 'sociedad', NULL, NULL),
(115, 8, 'photo', '2011091915514950772.jpg', 'gestor de contenidos para periÃ³dicos digitales', '', 'gestor, contenidos, periÃ³dicos, digitales, periodicos', '2011-09-28 22:58:28', '0000-00-00 00:00:00', '2011-09-28 22:58:28', '2011-10-11 20:51:55', 1, 7, 0, 3, 2, 2, 0, 0, 0, 100, '2011091915514950772-jpg', 1, NULL, 'publicidad', NULL, NULL),
(116, 8, 'photo', '2011091915514971387.jpg', 'gestor de contenidos para periÃ³dicos digitales', '', 'gestor, contenidos, periÃ³dicos, digitales', '2011-09-28 22:58:28', '0000-00-00 00:00:00', '2011-09-28 22:58:28', '2011-10-11 20:51:55', 1, 7, 0, 3, 3, 2, 0, 0, 0, 100, '2011091915514971387-jpg', 1, NULL, 'publicidad', NULL, NULL),
(117, 8, 'photo', '2011091915515137977.jpg', 'gestor de contenidos para periÃ³dicos digitales', '', 'gestor, contenidos, periÃ³dicos, digitales', '2011-09-28 22:58:29', '0000-00-00 00:00:00', '2011-09-28 22:58:29', '2011-10-11 20:51:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '2011091915515137977-jpg', 1, NULL, 'publicidad', NULL, NULL),
(119, 8, 'photo', '2010052617333374336.jpg', 'opennemas, gestor de contenidos para periÃ³dicos digitales', '', 'opennemas, gestor, contenidos, periÃ³dicos, digitales', '2011-09-28 23:06:58', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-10-11 20:51:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '2010052617333374336-jpg', 1, NULL, 'publicidad', NULL, NULL),
(121, 8, 'photo', '2010052617333365419.jpg', 'opennemas, gestor de contenidos para periÃ³dicos digitales', '', 'opennemas, gestor, contenidos, periÃ³dicos, digitales', '2011-09-28 23:06:58', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-10-11 20:51:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '2010052617333365419-jpg', 1, NULL, 'publicidad', NULL, NULL),
(123, 8, 'photo', '2010052616215169156.jpg', 'opennemas, gestor de contenidos para periÃ³dicos digitales', '', 'opennemas, gestor, contenidos, periÃ³dicos, digitales', '2011-09-28 23:06:58', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-10-11 20:51:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '2010052616215169156-jpg', 1, NULL, 'publicidad', NULL, NULL),
(124, 8, 'photo', '2010052616215165008.jpg', 'opennemas, gestor de contenidos para periÃ³dicos digitales', '', 'opennemas, gestor, contenidos, periÃ³dicos, digitales', '2011-09-28 23:06:58', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-10-11 20:51:55', 1, 7, 0, 3, 3, 2, 0, 0, 0, 100, '2010052616215165008-jpg', 1, NULL, 'publicidad', NULL, NULL),
(125, 8, 'photo', '2010052616215162000.jpg', 'opennemas, gestor de contenidos para periÃ³dicos digitales', '', 'opennemas, gestor, contenidos, periÃ³dicos, digitales', '2011-09-28 23:06:58', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-10-11 20:51:55', 1, 7, 0, 3, 2, 2, 0, 0, 0, 100, '2010052616215162000-jpg', 1, NULL, 'publicidad', NULL, NULL),
(126, 8, 'photo', '2010052616215164250.jpg', 'opennemas, gestor de contenidos para periÃ³dicos digitales', '', 'opennemas, gestor, contenidos, periÃ³dicos, digitales', '2011-09-28 23:06:58', '0000-00-00 00:00:00', '2011-09-28 23:06:58', '2011-10-11 20:51:55', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '2010052616215164250-jpg', 1, NULL, 'publicidad', NULL, NULL),
(128, 2, 'advertisement', 'Top right leaderboard - Frontpage - 234x90 - Openhost Ads', '', '', 'top,right,leaderboard,frontpage,234x90,openhost,ads', '2011-09-28 23:14:50', '0000-00-00 00:00:00', '2011-09-28 23:14:50', '2013-04-18 09:42:56', 1, 7, 7, 3, 7745, 2, 0, 0, 0, 100, 'publicidad-portada-top-right', 1, 'a:2:{s:5:"width";s:3:"234";s:6:"height";s:2:"90";}', '0', NULL, NULL),
(129, 2, 'advertisement', 'Publicidad Portada intersticial', '', '', 'publicidad, portada, intersticial', '2011-09-28 23:15:49', '0000-00-00 00:00:00', '2011-09-28 23:15:49', '2011-10-18 01:13:16', 0, 7, 7, 3, 691, 2, 0, 0, 0, 100, 'publicidad-portada-intersticial', 0, NULL, '', NULL, NULL),
(130, 2, 'advertisement', 'Bottom left leaderboard - Frontpage - 728x90 - Openhost Ads', '', '', 'bottom,left,leaderboard,frontpage,728x90,openhost,ads', '2011-09-28 23:16:43', '0000-00-00 00:00:00', '2011-09-28 23:16:43', '2013-04-18 05:07:30', 1, 7, 7, 3, 1430, 2, 0, 0, 0, 100, 'publicidad-portada-botton-left', 1, 'a:2:{s:5:"width";s:3:"728";s:6:"height";s:2:"90";}', '0', NULL, NULL),
(131, 2, 'advertisement', 'Bottom right Leaderboard - Frontpage - 234x90 - Openhost Ads', '', '', 'bottom,right,leaderboard,frontpage,234x90,openhost,ads', '2011-09-28 23:17:47', '0000-00-00 00:00:00', '2011-09-28 23:17:47', '2013-04-18 09:41:52', 1, 7, 7, 3, 1430, 2, 0, 0, 0, 100, 'publicidad-portada-botton-right', 1, 'a:2:{s:5:"width";s:3:"234";s:6:"height";s:2:"90";}', '0', NULL, NULL),
(132, 2, 'advertisement', 'Medium Rectangle - Pos2 Col3 - Frontpage - 300x250 - Openhost Ads', '', '', 'publicidad, portada, columna1', '2011-09-28 23:19:00', '0000-00-00 00:00:00', '2011-09-28 23:19:00', '2013-04-18 05:54:20', 1, 7, 7, 3, 1371, 2, 0, 0, 0, 100, 'publicidad-portada-columna1', 1, 'a:2:{s:5:"width";s:3:"300";s:6:"height";s:3:"250";}', '0', NULL, NULL),
(133, 2, 'advertisement', 'Center left leaderboard - Frontpage - 728x90 - Openhost Ads', '', '', 'center,left,leaderboard,frontpage,728x90,openhost,ads', '2011-09-28 23:19:44', '0000-00-00 00:00:00', '2011-09-28 23:19:44', '2013-04-18 05:59:00', 1, 7, 7, 3, 1393, 2, 0, 0, 0, 100, 'publicidad-portada-middle-left', 1, 'a:2:{s:5:"width";s:3:"728";s:6:"height";s:2:"90";}', '0', NULL, NULL),
(134, 2, 'advertisement', 'Bottom right leaderboard - Inner Article - 234x90 - Openhost Ads', '', '', 'bottom,right,leaderboard,inner,article,234x90,openhost,ads', '2011-09-28 23:20:24', '0000-00-00 00:00:00', '2011-09-28 23:20:24', '2013-04-18 05:50:31', 1, 7, 7, 3, 836, 2, 0, 0, 0, 100, 'publicidad-portada-middle-right', 1, 'a:2:{s:5:"width";s:3:"234";s:6:"height";s:2:"90";}', '0', NULL, NULL),
(135, 2, 'advertisement', 'Center rigth leaderboard - Frontpage - 234x90 - Openhost Ads', '', '', 'center,rigth,leaderboard,frontpage,234x90,openhost,ads', '2011-09-28 23:20:48', '0000-00-00 00:00:00', '2011-09-28 23:20:48', '2013-04-18 05:04:19', 1, 7, 7, 3, 1397, 2, 0, 0, 0, 100, 'publicidad-portada-middle-right', 1, 'a:2:{s:5:"width";s:3:"234";s:6:"height";s:2:"90";}', '0', NULL, NULL),
(136, 2, 'advertisement', 'Medium Rectangle - Pos1 Col3 - Frontpage - 300x250 - Openhost Ads', '', '', 'medium,rectangle,pos2,col3,frontpage,300x250,openhost,ads', '2011-09-28 23:23:16', '0000-00-00 00:00:00', '2011-09-28 23:23:16', '2013-04-18 05:55:37', 1, 7, 7, 3, 1398, 2, 0, 0, 0, 100, 'publicidad-portada-columna3', 1, 'a:2:{s:5:"width";s:3:"300";s:6:"height";s:3:"250";}', '0', NULL, NULL),
(137, 2, 'advertisement', 'Top left leaderboard - Inner Article - 728x90 - Openhost Ads', '', '', 'top,left,leaderboard,frontpage,728x90,openhost,ads', '2011-09-28 23:24:19', '0000-00-00 00:00:00', '2011-09-28 23:24:19', '2013-04-18 05:00:27', 1, 7, 7, 3, 866, 2, 0, 0, 0, 100, 'publicidad-inner-top-left', 1, 'a:2:{s:5:"width";s:3:"728";s:6:"height";s:2:"90";}', '0', NULL, NULL),
(138, 2, 'advertisement', 'Top right leaderboard - Inner Article - 234x90 - Openhost Ads', '', '', 'top,right,leaderboard,inner,article,234x90,openhost,ads', '2011-09-28 23:25:19', '0000-00-00 00:00:00', '2011-09-28 23:25:19', '2013-04-18 04:31:36', 1, 7, 7, 3, 836, 2, 0, 0, 0, 100, 'publicidad-inner-top-right', 1, 'a:2:{s:5:"width";s:3:"234";s:6:"height";s:2:"90";}', '0', NULL, NULL),
(139, 2, 'advertisement', 'Full banner - Inner Article - 468x60 - Openhost Ads', '', '', 'full,banner,inner,article,468x60,openhost,ads', '2011-09-28 23:26:10', '0000-00-00 00:00:00', '2011-09-28 23:26:10', '2013-04-18 09:40:22', 1, 7, 7, 3, 839, 2, 0, 0, 0, 100, 'publicidad-inner-robapage', 1, 'a:2:{s:5:"width";s:3:"468";s:6:"height";s:2:"60";}', '0', NULL, NULL),
(141, 2, 'advertisement', 'Top left leaderboard - Frontpage Opinion - 728x90 - Openhost Ads', '', '', 'top,left,leaderboard,frontpage,opinion,728x90,openhost,ads', '2011-09-28 23:28:18', '0000-00-00 00:00:00', '2011-09-28 23:28:18', '2013-04-18 06:02:09', 1, 7, 7, 3, 172, 2, 0, 0, 0, 100, 'publicidad-opinion-top-left', 1, 'a:2:{s:5:"width";s:3:"728";s:6:"height";s:2:"90";}', 'opinion', NULL, NULL),
(143, 2, 'advertisement', 'Top right leaderboard - Opinion Frontpage - 234x90 - Openhost Ads', '', '', 'top,right,leaderboard,opinion,frontpage,234x90,openhost,ads', '2011-09-28 23:29:47', '0000-00-00 00:00:00', '2011-09-28 23:29:47', '2013-04-18 09:10:49', 1, 7, 7, 3, 82, 2, 0, 0, 0, 100, 'publicidad-opinion-column', 1, 'a:2:{s:5:"width";s:3:"234";s:6:"height";s:2:"90";}', 'opinion', NULL, NULL),
(144, 2, 'advertisement', 'Medium Rectangle - Pos2 - Opinion Frontpage - 300x250 - Openhost Ads', '', '', 'medium,rectangle,pos2,opinion,frontpage,300x250,openhost,ads', '2011-09-28 23:30:45', '0000-00-00 00:00:00', '2011-09-28 23:30:45', '2013-04-18 09:27:43', 1, 7, 7, 3, 172, 2, 0, 0, 0, 100, 'publicidad-opinion-column3', 1, 'a:2:{s:5:"width";s:3:"300";s:6:"height";s:3:"250";}', 'opinion', NULL, NULL),
(145, 2, 'advertisement', 'Bottom Left leaderboard - Opinion Frontpage - 728x90 - Openhost Ads', '', '', 'bottom,left,leaderboard,opinion,frontpage,728x90,openhost,ads', '2011-09-28 23:31:31', '0000-00-00 00:00:00', '2011-09-28 23:31:31', '2013-04-18 09:45:08', 1, 7, 7, 3, 172, 2, 0, 0, 0, 100, 'publicidad-opinion-bottom-left', 1, 'a:2:{s:5:"width";s:3:"728";s:6:"height";s:2:"90";}', 'opinion', NULL, NULL),
(146, 2, 'advertisement', 'Bottom right leaderboard - Frontpage Opinion - 300x250 - Openhost Ads', '', '', 'bottom,right,leaderboard,frontpage,opinion,300x250,openhost,ads', '2011-09-28 23:32:19', '0000-00-00 00:00:00', '2011-09-28 23:32:19', '2013-04-18 06:14:58', 1, 7, 7, 3, 172, 2, 0, 0, 0, 100, 'publicidad-opinion-bottom-right', 1, 'a:2:{s:5:"width";s:3:"234";s:6:"height";s:2:"90";}', 'opinion', NULL, NULL),
(147, 2, 'advertisement', 'Top right leaderboard - Inner Opinion - 234x90 - Openhost Ads', '', '', 'top,right,leaderboard,inner,opinion,234x90,openhost,ads', '2011-09-28 23:33:12', '0000-00-00 00:00:00', '2011-09-28 23:33:12', '2013-04-18 06:10:14', 1, 7, 7, 3, 333, 2, 0, 0, 0, 100, 'publicidad-opinion-inner-top-right', 1, 'a:2:{s:5:"width";s:3:"234";s:6:"height";s:2:"90";}', 'opinion', NULL, NULL),
(148, 2, 'advertisement', 'Top left leaderboard - Inner Opinion - 728x90 - Openhost Ads', '', '', 'top,left,leaderboard,inner,opinion,728x90,openhost,ads', '2011-09-28 23:33:49', '0000-00-00 00:00:00', '2011-09-28 23:33:49', '2013-04-18 06:08:48', 1, 7, 7, 3, 337, 2, 0, 0, 0, 100, 'publicidad-opinion-inner-top-left', 1, 'a:2:{s:5:"width";s:3:"728";s:6:"height";s:2:"90";}', 'opinion', NULL, NULL),
(149, 2, 'advertisement', 'Medium Rectangle - Pos1 - Opinion Frontpage - 300x250 - Openhost Ads', '', '', 'medium,rectangle,pos1,opinion,frontpage,300x250,openhost,ads', '2011-09-28 23:34:30', '0000-00-00 00:00:00', '2011-09-28 23:34:30', '2013-04-18 09:30:05', 1, 7, 7, 3, 172, 2, 0, 0, 0, 100, 'publicidad-opinion-inner-columna', 1, 'a:2:{s:5:"width";s:3:"300";s:6:"height";s:3:"250";}', 'opinion', NULL, NULL),
(156, 2, 'advertisement', 'Bottom left leaderboard - Inner Article - 300x250 - Openhost Ads', '', '', 'bottom,left,leaderboard,inner,article,300x250,openhost,ads', '2011-09-28 23:43:15', '0000-00-00 00:00:00', '2011-09-28 23:43:15', '2013-04-18 04:59:09', 1, 7, 7, 3, 863, 2, 0, 0, 0, 100, 'publicidad-inner-bottom-left', 1, 'a:2:{s:5:"width";s:3:"728";s:6:"height";s:2:"90";}', '0', NULL, NULL),
(157, 2, 'advertisement', 'Top left leaderboard - Frontpage - 728x90 - Openhost Ads', '', '', 'top,left,leaderboard,frontpage,728x90,openhost,ads', '2011-09-28 23:44:24', '0000-00-00 00:00:00', '2011-09-28 23:44:24', '2013-04-18 04:31:03', 1, 7, 7, 3, 7309, 2, 0, 0, 0, 100, 'publicidad-portada-top-left', 1, 'a:2:{s:5:"width";s:3:"728";s:6:"height";s:2:"90";}', '0', NULL, NULL),
(159, 2, 'advertisement', 'Medium Rectangle - Pos2 - Inner Opinion - 300x250 - Openhost Ads', '', '', 'medium,rectangle,pos2,inner,opinion,300x250,openhost,ads', '2011-09-28 23:48:57', '0000-00-00 00:00:00', '2011-09-28 23:48:57', '2013-04-18 06:04:05', 1, 7, 7, 3, 332, 2, 0, 0, 0, 100, 'publicidad-opinion-inner-column', 1, 'a:2:{s:5:"width";s:3:"300";s:6:"height";s:3:"250";}', 'opinion', NULL, NULL),
(163, 11, 'poll', 'Curabitur lacinia mi a elit ullamcorper non lacinia nisl mollis', '', '', 'curabitur, lacinia, elit, ullamcorper, non, nisl, mollis', '2011-09-28 23:54:46', '0000-00-00 00:00:00', '2011-09-28 23:54:46', '2011-09-29 00:07:17', 1, 7, 0, 7, 14, 2, 0, 0, 0, 100, 'curabitur-lacinia-mi-a-elit-ullamcorper-non-lacinia-nisl-mollis', 1, NULL, 'sociedad', NULL, NULL),
(164, 11, 'poll', 'Maecenas adipiscing tortor commodo enim accumsan volutpat', '', '', 'maecenas, adipiscing, tortor, commodo, enim, accumsan, volutpat, phasellus, pellentesque', '2011-09-28 23:57:02', '0000-00-00 00:00:00', '2011-09-28 23:57:02', '2011-09-28 23:57:02', 0, 7, 7, 7, 37, 2, 0, 0, 1, 100, 'maecenas-adipiscing-tortor-commodo-enim-accumsan-volutpat', 1, NULL, 'cultura', NULL, NULL),
(165, 11, 'poll', 'Nam sed dui sagittis eros faucibus congue', '', '', 'nam, sed, dui, sagittis, eros, faucibus, congue, no, tal, vez', '2011-09-28 23:58:11', '0000-00-00 00:00:00', '2011-09-28 23:58:11', '2011-09-28 23:58:11', 0, 7, 7, 7, 16, 2, 0, 0, 0, 100, 'nam-sed-dui-sagittis-eros-faucibus-congue', 1, NULL, 'deportes', 1, NULL),
(166, 11, 'poll', 'Phasellus quis massa eros, id ullamcorper urna. ', '', '', 'phasellus, quis, massa, eros, id, ullamcorper, urna, fusce, tortor, caesar, totor', '2011-09-28 23:59:57', '0000-00-00 00:00:00', '2011-09-28 23:59:57', '2011-09-28 23:59:57', 0, 7, 7, 7, 41, 2, 0, 0, 0, 100, 'phasellus-quis-massa-eros-id-ullamcorper-urna', 1, NULL, 'sociedad', NULL, NULL),
(167, 1, 'article', 'Suspendisse sollicitudin turpis sit amet nisl volutpat tincidunt', '\r\nSuspendisse sollicitudin turpis sit amet nisl volutpat tincidunt. Phasellus pellentesque pulvinar rutrum. Ut interdum malesuada nunc vel viverra. Ut porta facilisis neque, a vestibulum sem volutpat adipiscing. Praesent at rhoncus nisi. Nulla eget quam neque, porta molestie tellus. Mauris sit amet massa lectus. Suspendisse potenti. Donec augue elit, suscipit eu...', '<div>\r\n<p>Suspendisse sollicitudin turpis sit amet nisl volutpat tincidunt. Phasellus pellentesque pulvinar rutrum. Ut interdum malesuada nunc vel viverra. Ut porta facilisis neque, a vestibulum sem volutpat adipiscing. Praesent at rhoncus nisi. Nulla eget quam neque, porta molestie tellus. Mauris sit amet massa lectus. Suspendisse potenti. Donec augue elit, suscipit eu pellentesque vitae, ornare cursus libero. Donec vestibulum, augue at accumsan sodales, metus ante suscipit quam, non iaculis leo magna porttitor tortor.</p>\r\n<p>Sed id orci eu tortor accumsan lobortis. Curabitur pretium turpis vitae tellus vestibulum vel tempor nunc sollicitudin. Pellentesque ac lacus a diam mattis posuere quis quis odio. Phasellus convallis purus at ligula auctor eget ultricies justo ultricies. Phasellus varius malesuada tellus, sit amet rutrum dui lobortis ut. Duis tristique feugiat orci, a congue turpis pretium quis. Maecenas tempor, nisl molestie ultricies aliquam, ipsum metus semper sem, pharetra pharetra est erat quis eros. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Integer egestas, nisi quis gravida placerat, turpis libero semper eros, sed faucibus tellus orci placerat diam.</p>\r\n</div>', 'deportes, suspendisse, sollicitudin, turpis, sit, amet, nisl, volutpat, tincidunt', '2011-09-29 10:43:15', '0000-00-00 00:00:00', '2011-09-29 10:43:15', '2013-06-17 18:03:08', 1, 7, 0, 3, 78, 1, 0, 0, 1, 1, 'suspendisse-sollicitudin-turpis-sit-amet-nisl-volutpat-tincidunt', 1, 'a:3:{s:11:"withGallery";s:0:"";s:14:"withGalleryInt";s:0:"";s:15:"withGalleryHome";s:0:"";}', 'deportes', NULL, NULL),
(168, 8, 'photo', '_stock-photo-speed_rev100.jpg', 'luces naranjas', '', 'luces, naranjas', '2011-09-29 10:46:40', '0000-00-00 00:00:00', '2011-09-29 10:46:40', '2011-10-11 19:17:29', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'stock-photo-speedrev100-jpg', 1, NULL, 'cultura', NULL, NULL),
(169, 8, 'photo', 'stock-photo-long-exposure-of-a-funfair-ride-at-night_rev100.jpg', 'luces de colores', '', 'luces, colores', '2011-09-29 10:46:40', '0000-00-00 00:00:00', '2011-09-29 10:46:40', '2011-10-11 19:17:29', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'stock-photo-long-exposure-of-a-funfair-ride-at-nightrev100-jpg', 1, NULL, 'cultura', NULL, NULL),
(170, 8, 'photo', 'stock-photo-modern-urban-landscape-at-night_rev100.jpg', 'luz en la ciudad', '', '', '2011-09-29 10:46:40', '0000-00-00 00:00:00', '2011-09-29 10:46:40', '2011-10-11 19:17:29', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, 'stock-photo-modern-urban-landscape-at-nightrev100-jpg', 1, NULL, 'cultura', NULL, NULL),
(171, 8, 'photo', '2010052523330582501.jpg', 'pin-up art', '', 'pin, up, art', '2011-09-29 10:48:18', '0000-00-00 00:00:00', '2011-09-29 10:48:18', '2011-10-11 19:17:29', 1, 7, 0, 3, 4, 2, 0, 0, 0, 100, '2010052523330582501-jpg', 1, NULL, 'cultura', NULL, NULL),
(172, 8, 'photo', '2010052523330591659.jpg', 'pin-up art', '', 'pin, up, art', '2011-09-29 10:48:19', '0000-00-00 00:00:00', '2011-09-29 10:48:19', '2011-10-11 19:17:29', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '2010052523330591659-jpg', 1, NULL, 'cultura', NULL, NULL),
(173, 8, 'photo', '2010052523330611552.jpg', 'pin-up art', '', 'pin, up, art', '2011-09-29 10:48:19', '0000-00-00 00:00:00', '2011-09-29 10:48:19', '2011-10-11 19:17:29', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '2010052523330611552-jpg', 1, NULL, 'cultura', NULL, NULL),
(174, 8, 'photo', '2010052523330622662.jpg', 'pin-up art', '', 'pin, up, art', '2011-09-29 10:48:19', '0000-00-00 00:00:00', '2011-09-29 10:48:19', '2011-10-11 19:17:29', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '2010052523330622662-jpg', 1, NULL, 'cultura', NULL, NULL),
(175, 8, 'photo', '2010052523341367151.jpg', 'pin-up art', '', 'pin, up, art', '2011-09-29 10:48:19', '0000-00-00 00:00:00', '2011-09-29 10:48:19', '2011-10-11 19:17:29', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '2010052523341367151-jpg', 1, NULL, 'cultura', NULL, NULL),
(176, 8, 'photo', '2010052523341385802.jpg', 'pin-up art', '', 'pin, up, art', '2011-09-29 10:48:19', '0000-00-00 00:00:00', '2011-09-29 10:48:19', '2011-10-11 19:17:29', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '2010052523341385802-jpg', 1, NULL, 'cultura', NULL, NULL),
(177, 8, 'photo', '2010052523341421820.jpg', 'pin-up art', '', 'pin, up, art', '2011-09-29 10:48:19', '0000-00-00 00:00:00', '2011-09-29 10:48:19', '2011-10-11 19:17:29', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '2010052523341421820-jpg', 1, NULL, 'cultura', NULL, NULL),
(178, 8, 'photo', '2010052523341496489.jpg', 'pin-up art', '', 'pin, up, art', '2011-09-29 10:48:19', '0000-00-00 00:00:00', '2011-09-29 10:48:19', '2011-10-11 19:17:29', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '2010052523341496489-jpg', 1, NULL, 'cultura', NULL, NULL),
(179, 8, 'photo', '2010052523354098074.jpg', 'pin-up art', '', 'pin, up, art', '2011-09-29 10:48:20', '0000-00-00 00:00:00', '2011-09-29 10:48:20', '2011-10-11 19:17:29', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '2010052523354098074-jpg', 1, NULL, 'cultura', NULL, NULL),
(180, 8, 'photo', '2010052523354121641.jpg', 'pin-up art', '', 'pin, up, art', '2011-09-29 10:48:20', '0000-00-00 00:00:00', '2011-09-29 10:48:20', '2011-10-11 19:17:29', 1, 7, 0, 3, 1, 2, 0, 0, 0, 100, '2010052523354121641-jpg', 1, NULL, 'cultura', NULL, NULL),
(181, 7, 'album', 'pin-up girls', 'Sed id orci eu tortor accumsan lobortis. Curabitur pretium turpis vitae tellus vestibulum vel tempor nunc sollicitudin. Pellentesque ac lacus a diam mattis posuere quis quis odio. Phasellus convallis purus at ligula auctor eget ultricies justo ultricies. ', '', 'pin, up, girls', '2011-09-29 10:56:37', '0000-00-00 00:00:00', '2011-09-29 10:56:37', '2013-04-03 20:53:04', 0, 7, 7, 3, 45, 2, 0, 0, 1, 100, 'pin-up-girls', 1, NULL, 'sociedad', 1, NULL),
(217, 1, 'article', 'Vivamus at arcu nibh. Suspendisse sit amet ipsum ligula, nec cursus dolor. ', '', '<p>Etiam blandit elit vitae sem tincidunt sodales. Proin consectetur tempus sem et gravida. Aliquam non velit a arcu ornare lobortis vitae quis libero. In posuere dui vitae erat posuere at commodo erat egestas. Sed tempus egestas nisl, eget dictum felis rhoncus sed. Phasellus blandit, eros vitae dictum fringilla, augue augue cursus metus, id auctor nisi ligula id tellus. Phasellus nunc mauris, molestie vel gravida nec, condimentum quis turpis.</p>\r\n<p>Vivamus at arcu nibh. Suspendisse sit amet ipsum ligula, nec cursus dolor. Nunc sit amet libero ante, tempor egestas lorem. Nullam dictum tincidunt risus id aliquet. Quisque feugiat gravida purus ut sodales. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam porta lobortis tempus. Aliquam imperdiet scelerisque odio non gravida. Integer eleifend lacus nec purus interdum ultricies. Donec egestas porttitor sem non feugiat. Aenean posuere venenatis nunc non tincidunt.</p>\r\n<p>Pellentesque eu lectus dui, quis pulvinar tellus. Phasellus id orci quam, fermentum facilisis dui. Vivamus a augue sit amet est bibendum aliquam ac ac nunc. Sed elementum gravida nisl, eget iaculis tellus eleifend et. Fusce sed mi orci, non porttitor diam. Phasellus vitae laoreet lectus. Aliquam pellentesque mattis nunc, at condimentum metus dignissim et. Praesent pulvinar, urna vel accumsan placerat, dui nunc pretium velit, sit amet scelerisque augue sapien in purus. Sed fermentum, dui eu ornare tincidunt, erat purus pretium elit, sed cursus dui enim in quam. Donec a arcu augue. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nulla rutrum quam sed nisi consequat venenatis.</p>\r\n<p>Aliquam purus quam, semper vel molestie eu, convallis vitae urna. Aliquam ante massa, dignissim sed tempor imperdiet, elementum nec mauris. Donec ultrices, nunc ac semper adipiscing, mi tortor interdum felis, eu porta sapien est et massa. Vivamus consequat est vel sapien pellentesque non ornare ipsum imperdiet. Fusce pulvinar nunc non quam adipiscing lacinia. In eget massa nunc. Nullam felis nibh, placerat a imperdiet sed, faucibus eu velit. Sed in tincidunt elit.</p>', 'polÃ­tica, vivamus, at, arcu, nibh, suspendisse, sit, amet, ipsum, ligula, nec, cursus, dolor', '2011-10-11 19:59:50', '0000-00-00 00:00:00', '2011-10-11 19:59:50', '2012-10-01 12:47:38', 1, 3, 3, 7, 6, 1, 0, 0, 2, 100, 'vivamus-at-arcu-nibh-suspendisse-sit-amet-ipsum-ligula-nec-cursus-dolor', 1, NULL, 'politica', NULL, NULL),
(183, 4, 'opinion', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', '', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue.</p>\r\n\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce aliquam magna a augue mollis id suscipit quam tincidunt. Etiam sed venenatis libero. Nunc pretium justo nec tortor consequat bibendum. Maecenas vitae nisi dui, nec commodo magna. Proin sit amet ipsum felis. Aliquam ultrices fermentum massa. Donec ultricies erat sit amet purus adipiscing lacinia fringilla urna elementum. Cras metus dui, elementum id convallis vitae, feugiat nec nulla. Vivamus id nibh orci. Curabitur tristique augue non diam tincidunt ut aliquet nulla adipiscing. Nam convallis ipsum id diam sodales vulputate. Vestibulum venenatis elementum nulla. Duis a mauris nec sem aliquam placerat ut at augue</p>\r\n', 'lorem,ipsum,dolor,sit,amet,consectetur,adipiscing,elit', '2011-10-05 12:46:14', '0000-00-00 00:00:00', '2011-10-05 12:46:14', '2013-04-18 05:13:28', 1, 7, 7, 0, 2, 1, 0, 0, 1, 100, 'lorem-ipsum-dolor-sit-amet-consectetur-adipiscing-elit', 1, NULL, '0', NULL, NULL),
(184, 4, 'opinion', 'Donec ultricies tincidunt ultrices.', '', '<p>Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum.Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum.Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti.</p>\r\n\r\n<p>Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum.Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti. Donec augue mauris, mattis quis aliquam sit amet, pharetra eget libero. Donec ultricies tincidunt ultrices. Fusce rutrum porttitor urna, aliquet imperdiet dolor fringilla eu. Fusce ac sem velit, ut sollicitudin mauris. Morbi molestie semper diam. Vivamus accumsan sem ipsum.Nam lobortis nibh eu ante molestie nec condimentum justo semper. Suspendisse potenti.</p>\r\n', 'donec,ultricies,tincidunt,ultrices', '2011-10-05 12:47:01', '0000-00-00 00:00:00', '2011-10-05 12:47:01', '2013-06-17 18:03:08', 1, 7, 7, 3, 12, 1, 0, 0, 1, 100, 'donec-ultricies-tincidunt-ultrices', 1, NULL, '0', NULL, NULL),
(186, 12, 'widget', 'AllHeadlines (2cols)', '', '', '', '2011-10-05 13:24:39', '0000-00-00 00:00:00', '2011-10-05 13:24:39', '2012-10-01 13:02:14', 0, 5, 5, 7, 1, 2, 0, 0, 1, 100, 'allheadlines-2cols', 1, NULL, '', NULL, NULL),
(192, 12, 'widget', 'Comentarios-ultimos', '', '', '', '2011-10-05 13:25:48', '0000-00-00 00:00:00', '2011-10-05 13:25:48', '2012-10-01 13:02:37', 0, 5, 5, 7, 1, 2, 0, 0, 1, 100, 'comentarios-ultimos', 1, NULL, '', NULL, NULL),
(193, 12, 'widget', 'Noticias-ultimas comentadas', '', '', '', '2011-10-05 13:25:56', '0000-00-00 00:00:00', '2011-10-05 13:25:56', '2013-06-17 18:03:08', 0, 5, 5, 3, 1, 2, 0, 0, 1, 100, 'noticias-ultimas-comentadas', 1, NULL, '', NULL, NULL),
(215, 1, 'article', 'Fusce vel libero justo, quis hendrerit elit. Phasellus quis dolor leo. Nam nisl mi, venenatis sit amet te', 'Fusce vel libero justo, quis hendrerit elit. Phasellus quis dolor leo. Nam nisl mi, venenatis sit amet tempor quis, vulputate facilisis arcu. Nullam ante tellus, placerat quis tempus ac, dignissim vitae nunc. Vivamus tincidunt pharetra libero in consequat.\r\nInteger vel orci risus, non ultricies nisl. Vestibulum sapien arcu, ornare ut...', '<p>Fusce vel libero justo, quis hendrerit elit. Phasellus quis dolor leo. Nam nisl mi, venenatis sit amet tempor quis, vulputate facilisis arcu. Nullam ante tellus, placerat quis tempus ac, dignissim vitae nunc. Vivamus tincidunt pharetra libero in consequat.</p>\r\n<p>Integer vel orci risus, non ultricies nisl. Vestibulum sapien arcu, ornare ut bibendum sed, pellentesque sit amet risus. Sed fringilla, ipsum vel mollis tempor, tortor justo tempor lectus, auctor porttitor dui tellus eu eros. Etiam dui urna, pharetra eget pretium ut, tempor consequat risus. Suspendisse tempus consectetur luctus. Suspendisse mattis est in nibh eleifend vulputate. Sed nec leo in ligula facilisis pulvinar. Maecenas fermentum risus non felis lacinia sit amet lacinia leo bibendum. In hac habitasse platea dictumst.</p>\r\n<p>Nam felis lorem, pulvinar consequat consequat ut, sodales ac quam. Ut vulputate mattis elit, eget aliquet libero viverra accumsan. Integer eu lacus erat. Phasellus fermentum, sapien a porttitor facilisis, augue augue molestie sapien, ac egestas lectus lacus ut neque. Praesent mauris erat, posuere et aliquet eget, hendrerit vel eros. Sed et sem augue. Integer feugiat sem nec justo ornare ultrices quis nec magna. Nulla commodo dictum dolor, id dignissim enim ornare porttitor. Donec tempor, mi id faucibus vulputate, est erat gravida nunc, id vestibulum mauris massa nec quam. Quisque consectetur fringilla porttitor. Vivamus vitae euismod tellus. Fusce id ante ac odio ullamcorper aliquam.</p>\r\n<p>Aenean dui orci, fringilla sed fringilla sed, aliquet at eros. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum at lorem velit. Fusce et arcu libero. Fusce rhoncus tempor lacus. Nunc placerat lectus in velit condimentum accumsan. Morbi mollis nisl sit amet ante pharetra sed volutpat leo pulvinar. Fusce id massa sit amet eros iaculis faucibus bibendum quis neque. Cras massa odio, rutrum id tincidunt ac, feugiat sit amet neque. Nullam metus sem, accumsan quis ornare vel, vehicula nec mauris. Vestibulum non massa sem, venenatis mattis purus.</p>\r\n<p>Sed eget dui a quam gravida ullamcorper vel at mauris. Donec et velit eu est faucibus imperdiet. Donec fringilla tristique nulla a sagittis. Ut mollis pulvinar sem ut pretium. Nullam semper molestie arcu, ut cursus odio iaculis et. Nam eleifend, diam ac convallis luctus, nibh mauris faucibus elit, nec eleifend dolor quam ac dolor. Sed nisl felis, sollicitudin at accumsan at, consequat in lacus. Nunc quis turpis ipsum. Donec volutpat orci eu mi pellentesque malesuada. Mauris commodo ligula a elit blandit sed consequat lectus fermentum. Donec a posuere dolor. Integer at risus sed justo faucibus auctor ut non ipsum. Quisque tincidunt consectetur aliquam. Aliquam dignissim condimentum arcu ut vulputate. In bibendum mi vitae erat lacinia consequat. Donec malesuada ultrices magna eget commodo.&nbsp;</p>', 'sociedad, fusce, vel, libero, justo, quis, hendrerit, elit, phasellus, dolor, leo, nam, nisl, venenatis, sit, amet, desconocido', '2011-10-11 19:44:08', '0000-00-00 00:00:00', '2011-10-11 19:44:08', '2013-06-17 18:03:08', 1, 3, 3, 3, 4, 1, 0, 0, 2, 100, 'fusce-vel-libero-justo-quis-hendrerit-elit-phasellus-quis-dolor-leo-nam-nisl-mi-venenatis-sit-amet-te', 1, NULL, 'sociedad', NULL, NULL),
(195, 12, 'widget', 'Album -favoritos', '', '', '', '2011-10-05 13:26:14', '0000-00-00 00:00:00', '2011-10-05 13:26:14', '2013-06-17 18:03:08', 0, 5, 5, 3, 1, 2, 0, 0, 1, 100, 'album-favoritos', 1, NULL, '', NULL, NULL),
(200, 12, 'widget', 'MostSeeingVotedCommentedContent', '', '', '', '2011-10-05 13:27:03', '0000-00-00 00:00:00', '2011-10-05 13:27:03', '2013-06-17 18:03:08', 0, 5, 5, 3, 1, 2, 0, 0, 1, 100, 'mostseeingvotedcommentedcontent', 1, NULL, '', NULL, NULL),
(202, 12, 'widget', 'OpinionAuthorList **', 'Solo se visualiza en opinion', '', '', '2011-10-05 13:27:55', '0000-00-00 00:00:00', '2011-10-05 13:27:55', '2012-10-01 13:05:09', 0, 5, 5, 7, 2, 2, 0, 0, 1, 100, 'opinionauthorlist', 1, NULL, '', NULL, NULL),
(216, 1, 'article', 'Sed bibendum justo sit amet dolor mollis quis egestas purus iaculis', 'Aenean pharetra bibendum tortor, quis dapibus lectus porta id. Suspendisse potenti. Etiam feugiat neque ut augue vulputate volutpat. Integer laoreet, nulla nec venenatis molestie, enim leo accumsan metus, non mollis metus eros nec quam. Suspendisse potenti. Morbi eros lectus, cursus eget consequat quis, posuere hendrerit velit. Donec eget libero non...', '<p>Aenean pharetra bibendum tortor, quis dapibus lectus porta id. Suspendisse potenti. Etiam feugiat neque ut augue vulputate volutpat. Integer laoreet, nulla nec venenatis molestie, enim leo accumsan metus, non mollis metus eros nec quam. Suspendisse potenti. Morbi eros lectus, cursus eget consequat quis, posuere hendrerit velit. Donec eget libero non lorem tincidunt placerat at sit amet lorem. Aliquam bibendum, sapien sit amet rhoncus blandit, felis leo volutpat magna, quis luctus mi mauris vitae mauris. Donec congue elit et purus pharetra sollicitudin. Fusce fringilla mollis erat et rhoncus. Nulla imperdiet, arcu nec placerat bibendum, erat magna sagittis est, ac cursus nisi felis ut enim. Vivamus turpis felis, facilisis vel aliquam sed, adipiscing a augue. Nullam tortor justo, semper ac cursus vel, facilisis et lorem. Nunc posuere tincidunt rutrum.</p>\r\n\r\n<p>Suspendisse massa dolor, luctus non mollis nec, blandit vitae sem. Vivamus ut feugiat velit. Aliquam erat volutpat. Etiam gravida euismod tellus quis consequat. Morbi aliquet dolor a tortor consectetur id porttitor eros lacinia. Sed est tortor, fringilla in consectetur sed, aliquet at risus. Cras vel nunc vitae nunc lacinia convallis in eu sapien. Aliquam vestibulum augue tellus, a pellentesque ante. Ut non odio nec orci euismod ornare sed porta leo. Sed bibendum justo sit amet dolor mollis quis egestas purus iaculis. Nulla metus neque, hendrerit vel porta vel, luctus non mi. Maecenas ipsum purus, consequat ac porttitor at, laoreet ac eros.</p>\r\n\r\n<p>Curabitur hendrerit, felis vel pulvinar auctor, augue turpis ullamcorper nulla, ut pharetra tortor mi ac elit. Aliquam sagittis luctus volutpat. Phasellus imperdiet tempor feugiat. Vivamus quis tempus metus. Suspendisse augue arcu, cursus eu egestas ut, dictum in mi. Morbi sollicitudin enim ut est sodales eget commodo massa posuere. Nullam sit amet velit id libero viverra ultricies. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum aliquam, ipsum fermentum tempor ultricies, nibh sem venenatis urna, id tincidunt erat mi at nisl. Vestibulum nec justo sed quam suscipit vehicula ac id lectus. Pellentesque ac odio ac mi malesuada accumsan vitae id orci.</p>\r\n\r\n<p>Aenean molestie tortor sit amet magna sollicitudin placerat. Maecenas in placerat mauris. Duis suscipit, nunc in blandit sollicitudin, tortor nulla congue lacus, quis dictum nisl augue nec nulla. Donec hendrerit mollis sem et ultrices. Nunc a mi fringilla leo rhoncus volutpat. Proin luctus, odio id vehicula lacinia, ligula quam lobortis neque, vitae ultricies nibh enim a dui. Pellentesque luctus commodo ligula, a laoreet dui viverra in. Vestibulum accumsan sollicitudin tellus non feugiat. Etiam et dolor vel sapien mollis venenatis at id velit.</p>\r\n', 'economÃ­a,sed,bibendum,justo,sit,amet,dolor,mollis,quis,egestas,purus,iaculis', '2011-10-11 19:49:43', '0000-00-00 00:00:00', '2011-10-11 19:49:43', '2013-06-17 18:03:08', 1, 3, 3, 3, 23, 1, 0, 0, 2, 100, 'sed-bibendum-justo-sit-amet-dolor-mollis-quis-egestas-purus-iaculis', 1, 'a:14:{s:14:"agencyBulletin";s:0:"";s:15:"imageHomeFooter";s:0:"";s:9:"imageHome";s:0:"";s:9:"titleSize";s:2:"26";s:13:"imagePosition";s:5:"right";s:9:"titleHome";s:0:"";s:13:"titleHomeSize";s:2:"26";s:12:"subtitleHome";s:0:"";s:11:"summaryHome";s:0:"";s:17:"imageHomePosition";s:5:"right";s:11:"withGallery";s:0:"";s:14:"withGalleryInt";s:0:"";s:15:"withGalleryHome";s:0:"";s:16:"only_subscribers";s:0:"";}', 'economia', NULL, NULL),
(206, 12, 'widget', 'PastHeadlinesMostViewed', '', '', '', '2011-10-05 13:28:37', '0000-00-00 00:00:00', '2011-10-05 13:28:37', '2013-06-17 18:03:08', 0, 5, 5, 3, 1, 2, 0, 0, 1, 100, 'pastheadlinesmostviewed', 1, NULL, '', NULL, NULL),
(209, 3, 'attachment', 'prueba', 'prueba', '', 'prueba', '2011-10-06 12:45:06', '0000-00-00 00:00:00', '2011-10-06 12:45:06', '2011-10-06 12:45:06', 0, 4, 4, 4, 1, 2, 0, 0, 0, 100, 'prueba', 1, NULL, '', NULL, NULL),
(210, 1, 'article', 'Praesent ornare enim quis purus faucibus mollis.', 'Nullam sodales, arcu at posuere gravida, ipsum odio ornare mauris, in facilisis nibh magna in lacus. Suspendisse quis tincidunt mauris. Phasellus eu hendrerit eros. Praesent ornare enim quis purus faucibus mollis. Proin volutpat aliquam orci ac congue. Morbi id vehicula dui. Donec sed tempus nunc. Cras erat nunc, fringilla...', '<p>Nullam sodales, arcu at posuere gravida, ipsum odio ornare mauris, in facilisis nibh magna in lacus. Suspendisse quis tincidunt mauris. Phasellus eu hendrerit eros. Praesent ornare enim quis purus faucibus mollis. Proin volutpat aliquam orci ac congue. Morbi id vehicula dui. Donec sed tempus nunc. Cras erat nunc, fringilla sit amet vehicula et, tincidunt eget lorem. Nulla bibendum laoreet egestas.</p>\r\n<p>Fusce non ante nisi. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Sed imperdiet quam et nibh dignissim eu tincidunt augue bibendum. Pellentesque risus nulla, consequat sit amet vehicula eu, facilisis in nisl. Aliquam non sem augue, et pulvinar tellus. Donec pretium faucibus molestie. Vestibulum vitae mauris pulvinar nibh commodo aliquet.&nbsp;</p>\r\n<p>Proin volutpat aliquam orci ac congue. Morbi id vehicula dui. Donec sed tempus nunc. Cras erat nunc, fringilla sit amet vehicula et, tincidunt eget lorem. Nulla bibendum laoreet egestas.</p>\r\n<p>Fusce non ante nisi. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Sed imperdiet quam et nibh dignissim eu tincidunt augue bibendum. Pellentesque risus nulla, consequat sit amet vehicula eu, facilisis in nisl. Aliquam non sem augue, et pulvinar tellus. Donec pretium faucibus molestie. Vestibulum vitae mauris pulvinar nibh commodo aliquet. Morbi ac nibh sit amet quam lobortis posuere eu in nunc. Donec sed ullamcorper lectus. Morbi pharetra augue tincidunt tellus mattis egestas. In pulvinar suscipit nisi in auctor. Vestibulum blandit eleifend neque vitae auctor. Sed non lorem sed dolor pharetra hendrerit. Sed molestie ipsum nec lorem aliquet sit amet aliquam massa egestas.</p>\r\n<p>Aliquam non sem augue, et pulvinar tellus. Donec pretium faucibus molestie. Vestibulum vitae mauris pulvinar nibh commodo aliquet. Morbi ac nibh sit amet quam lobortis posuere eu in nunc. Donec sed ullamcorper lectus. Morbi pharetra augue tincidunt tellus mattis egestas. In pulvinar suscipit nisi in auctor. Vestibulum blandit eleifend neque vitae auctor. Sed non lorem sed dolor pharetra hendrerit. Sed molestie ipsum nec lorem aliquet sit amet aliquam massa egestas.</p>', 'sociedad, praesent, ornare, enim, quis, purus, faucibus, mollis', '2011-10-06 13:40:14', '0000-00-00 00:00:00', '2011-10-06 13:40:14', '2012-10-01 12:46:05', 1, 4, 4, 7, 11, 1, 0, 0, 2, 100, 'praesent-ornare-enim-quis-purus-faucibus-mollis', 1, NULL, 'sociedad', NULL, NULL),
(212, 3, 'attachment', 'testing upload files', 'testing upload files', '', 'testing, upload, files', '2011-10-06 23:04:54', '0000-00-00 00:00:00', '2011-10-06 23:04:54', '2011-10-06 23:04:54', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'testing-upload-files', 1, NULL, '', NULL, NULL),
(224, 2, 'advertisement', 'Medium Rectangle - Pos1 - Inner Article - 300x250 - Openhost Ads', '', '', 'medium,rectangle,pos1,inner,article,300x250,openhost,ads', '2011-10-18 01:14:09', '0000-00-00 00:00:00', '2011-10-18 01:14:09', '2013-04-18 04:33:07', 1, 3, 3, 3, 7152, 2, 0, 0, 0, 100, 'publicidad-inner-top-right-columna1', 1, 'a:2:{s:5:"width";s:3:"300";s:6:"height";s:3:"250";}', '0', NULL, NULL),
(225, 13, 'static_page', 'PÃ¡gina estÃ¡tica', '', '<p>P&aacute;gina est&aacute;tica de ejemplo</p>\r\n', 'pagina,estatica', '2012-07-23 09:01:27', '0000-00-00 00:00:00', '2012-07-23 09:01:27', '2013-06-17 16:14:25', 0, 7, 7, 5, 4, 2, 0, 0, 0, 100, 'estatica', 1, NULL, '0', NULL, NULL),
(226, 12, 'widget', 'Videos mÃ¡s votados, comentados', '', '', '', '2012-07-30 10:43:22', '0000-00-00 00:00:00', '2012-07-30 10:43:22', '2013-04-03 20:53:04', 0, 7, 7, 3, 1, 2, 0, 0, 0, 100, 'videos-mas-votados-comentados', 1, NULL, '0', NULL, NULL),
(227, 17, 'letter', 'El problema con esta postura es que ignora u oculta varios hechos esenciales', '', '<p>Uno es que su &eacute;xito como pa&iacute;s exportador se debe a una situaci&oacute;n de dominio sobre su propia clase trabajadora y sobre otros pa&iacute;ses que bien podr&iacute;a definirse como explotaci&oacute;n. Puesto que este tipo de terminolog&iacute;a raramente aparece en los medios, siento la necesidad de explicar el significado de tal t&eacute;rmino. A explota a B cuando A vive mejor a costa de B, que vive peor. A y B pueden ser clases sociales o pa&iacute;ses. Pues bien, comencemos por clases. El complejo exportador alem&aacute;n ha basado su &eacute;xito (que ha repercutido en una explosi&oacute;n de sus beneficios) en parte en que ha evitado que la clase trabajadora alemana sea beneficiaria del incremento de su productividad. Como bien ha dicho Mark Weisbrot, el Estado y el mundo empresarial alemanes no han permitido un aumento de los salarios paralelo al crecimiento de su productividad. La mayor&iacute;a de este crecimiento ha enriquecido las rentas del capital, y no las del trabajo. En realidad, estas &uacute;ltimas, como porcentaje de todas las rentas, han disminuido. Al capital le ha ido muy bien a costa de que al mundo del trabajo no le haya ido tan bien como podr&iacute;a o deber&iacute;a haberle ido.</p>', '', '2012-07-31 14:46:40', '0000-00-00 00:00:00', '2012-07-31 14:46:40', '2012-07-31 14:50:56', 1, 0, 0, 7, 15, 1, 0, 0, 0, 100, 'el-problema-con-esta-postura-es-que-ignora-u-oculta-varios-hechos-esenciales', 1, 'a:1:{s:2:"ip";s:9:"127.0.0.1";}', '0', NULL, NULL);
INSERT INTO `contents` (`pk_content`, `fk_content_type`, `content_type_name`, `title`, `description`, `body`, `metadata`, `starttime`, `endtime`, `created`, `changed`, `content_status`, `fk_author`, `fk_publisher`, `fk_user_last_editor`, `views`, `position`, `frontpage`, `in_litter`, `in_home`, `home_pos`, `slug`, `available`, `params`, `category_name`, `favorite`, `urn_source`) VALUES
(228, 17, 'letter', 'La economÃ­a alemana no es un ejemplo', '', '<p>El problema con esta postura es que ignora u oculta varios hechos esenciales. Uno es que su &eacute;xito como pa&iacute;s exportador se debe a una situaci&oacute;n de dominio sobre su propia clase trabajadora y sobre otros pa&iacute;ses que bien podr&iacute;a definirse como explotaci&oacute;n. Puesto que este tipo de terminolog&iacute;a raramente aparece en los medios, siento la necesidad de explicar el significado de tal t&eacute;rmino. A explota a B cuando A vive mejor a costa de B, que vive peor. A y B pueden ser clases sociales o pa&iacute;ses. Pues bien, comencemos por clases. El complejo exportador alem&aacute;n ha basado su &eacute;xito (que ha repercutido en una explosi&oacute;n de sus beneficios) en parte en que ha evitado que la clase trabajadora alemana sea beneficiaria del incremento de su productividad. Como bien ha dicho Mark Weisbrot, el Estado y el mundo empresarial alemanes no han permitido un aumento de los salarios paralelo al crecimiento de su productividad. La mayor&iacute;a de este crecimiento ha enriquecido las rentas del capital, y no las del trabajo. En realidad, estas &uacute;ltimas, como porcentaje de todas las rentas, han disminuido. Al capital le ha ido muy bien a costa de que al mundo del trabajo no le haya ido tan bien como podr&iacute;a o deber&iacute;a haberle ido.</p>', '', '2012-07-31 14:46:51', '0000-00-00 00:00:00', '2012-07-31 14:46:51', '2012-07-31 14:50:32', 1, 0, 0, 7, 14, 1, 0, 0, 0, 100, 'la-economia-alemana-no-es-un-ejemplo', 1, 'a:1:{s:2:"ip";s:9:"127.0.0.1";}', '0', NULL, NULL),
(229, 12, 'widget', 'Albumes - Ultimos publicados', '', '', '', '2012-10-01 13:05:32', '0000-00-00 00:00:00', '2012-10-01 13:05:32', '2012-10-01 13:05:32', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'albumes-ultimos-publicados', 1, NULL, '0', NULL, NULL),
(230, 12, 'widget', 'Noticias - Ãšltimas', '', '', '', '2012-10-01 13:05:50', '0000-00-00 00:00:00', '2012-10-01 13:05:50', '2012-10-01 13:05:50', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'noticias-ultimas', 1, NULL, '0', NULL, NULL),
(231, 12, 'widget', 'Hemeroteca Calendario', '', '', '', '2012-10-01 13:06:06', '0000-00-00 00:00:00', '2012-10-01 13:06:06', '2012-10-01 13:06:06', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'hemeroteca-calendario', 1, NULL, '0', NULL, NULL),
(232, 12, 'widget', 'Facebook - botÃ³n like', '', '', '', '2012-10-01 13:06:30', '0000-00-00 00:00:00', '2012-10-01 13:06:30', '2012-10-01 13:06:30', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'facebook-boton-like', 1, NULL, '0', NULL, NULL),
(233, 12, 'widget', 'Facebook like with faces', '', '', '', '2012-10-01 13:06:42', '0000-00-00 00:00:00', '2012-10-01 13:06:42', '2012-10-01 13:06:42', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'facebook-like-with-faces', 1, NULL, '0', NULL, NULL),
(235, 12, 'widget', 'Opinion- otras del autor **', 'Solo se visualiza en opinion del autor', '', '', '2012-10-01 13:09:22', '0000-00-00 00:00:00', '2012-10-01 13:09:22', '2012-10-01 13:09:22', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'opinion-otras-del-autor', 1, NULL, '0', NULL, NULL),
(236, 12, 'widget', 'Opinion - Lista de opiniones favoritas', '', '', '', '2012-10-01 13:09:37', '0000-00-00 00:00:00', '2012-10-01 13:09:37', '2012-10-01 13:09:37', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'opinion-lista-de-opiniones-favoritas', 1, NULL, '0', NULL, NULL),
(237, 12, 'widget', 'Noticias de Hoy', '', '', '', '2012-10-01 13:09:57', '0000-00-00 00:00:00', '2012-10-01 13:09:57', '2012-10-01 13:09:57', 0, 7, 7, 7, 1, 2, 0, 0, 0, 100, 'noticias-de-hoy', 1, NULL, '0', NULL, NULL),
(238, 2, 'advertisement', 'Medium Rectangle - Pos1 - Inner Opinion - 300x250 - Openhost Ads', '', '', 'medium,rectangle,pos1,inner,opinion,300x250,openhost,ads', '2013-04-18 06:05:41', '0000-00-00 00:00:00', '2013-04-18 06:05:41', '2013-04-18 06:05:41', 1, 3, 3, 3, 9, 2, 0, 0, 0, 100, 'medium-rectangle-pos1-inner-opinion-300x250-openhost-ads', 1, 'a:2:{s:5:"width";s:3:"300";s:6:"height";s:3:"250";}', 'opinion', NULL, NULL),
(239, 2, 'advertisement', 'Body skycraper - Inner Article - 120x600 - Openhost Ads', '', '', 'body,skycraper,inner,article,120x600,openhost,ads', '2013-04-18 06:40:33', '0000-00-00 00:00:00', '2013-04-18 06:40:33', '2013-04-18 06:41:53', 1, 3, 3, 3, 19, 2, 0, 0, 0, 100, 'body-skycraper-inner-article-120x600-openhost-ads', 1, 'a:2:{s:5:"width";s:3:"120";s:6:"height";s:3:"600";}', '0', NULL, NULL),
(240, 12, 'widget', 'Twitter HTML Search ', '', '', 'twitter,html,search,opennemas', '2013-06-17 17:38:43', '0000-00-00 00:00:00', '2013-06-17 17:38:43', '2013-06-17 18:54:23', 0, 3, 3, 3, 1, 2, 0, 0, 0, 100, 'twitter-html-search-opennemas', 1, NULL, '0', NULL, NULL);

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

--
-- A extraer datos da táboa `contents_categories`
--

INSERT INTO `contents_categories` (`pk_fk_content`, `pk_fk_content_category`, `catName`) VALUES
(3, 22, 'deportes'),
(4, 22, 'deportes'),
(5, 22, 'deportes'),
(6, 22, 'deportes'),
(7, 22, 'deportes'),
(8, 22, 'deportes'),
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
(67, 24, 'politica'),
(54, 0, '0'),
(55, 0, '0'),
(56, 0, '0'),
(57, 0, '0'),
(58, 0, '0'),
(59, 0, '0'),
(60, 0, '0'),
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
(115, 2, 'publicidad'),
(116, 2, 'publicidad'),
(117, 2, 'publicidad'),
(119, 2, 'publicidad'),
(121, 2, 'publicidad'),
(123, 2, 'publicidad'),
(124, 2, 'publicidad'),
(125, 2, 'publicidad'),
(126, 2, 'publicidad'),
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
(141, 4, 'opinion'),
(143, 4, 'opinion'),
(144, 4, 'opinion'),
(145, 4, 'opinion'),
(146, 4, 'opinion'),
(147, 4, 'opinion'),
(148, 4, 'opinion'),
(149, 4, 'opinion'),
(156, 0, ''),
(157, 0, ''),
(159, 4, 'opinion'),
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
(183, 0, '0'),
(184, 0, '0'),
(216, 23, 'economia'),
(186, 0, ''),
(217, 24, 'politica'),
(192, 0, ''),
(193, 0, ''),
(218, 4, 'opinion'),
(195, 0, ''),
(219, 25, 'cultura'),
(200, 0, ''),
(220, 25, 'cultura'),
(202, 0, ''),
(206, 0, ''),
(221, 25, 'cultura'),
(215, 26, 'sociedad'),
(209, 0, ''),
(210, 26, 'sociedad'),
(212, 0, ''),
(222, 26, 'sociedad'),
(223, 23, 'economia'),
(224, 0, ''),
(225, 0, '0'),
(226, 0, '0'),
(227, 0, '0'),
(228, 0, '0'),
(229, 0, '0'),
(230, 0, '0'),
(231, 0, '0'),
(232, 0, '0'),
(233, 0, '0'),
(235, 0, '0'),
(236, 0, '0'),
(237, 0, '0'),
(238, 4, 'opinion'),
(239, 0, '0'),
(240, 0, '0');

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

--
-- A extraer datos da táboa `content_categories`
--

INSERT INTO `content_categories` (`pk_content_category`, `title`, `name`, `inmenu`, `posmenu`, `internal_category`, `fk_content_category`, `params`, `logo_path`, `color`) VALUES
(1, 'fotografias', 'photo', 0, 0, 0, 0, NULL, NULL, ''),
(2, 'publicidad', 'publicidad', 0, 0, 0, 0, NULL, NULL, ''),
(3, 'ALBUM', 'album', 0, 0, 0, 0, NULL, NULL, ''),
(4, 'OPINIÃ“N', 'opinion', 0, 0, 0, 0, NULL, NULL, ''),
(5, 'comentarios', 'comment', 0, 0, 0, 0, NULL, NULL, ''),
(6, 'videos', 'video', 0, 0, 0, 0, NULL, NULL, ''),
(7, 'author', 'author', 0, 0, 0, 0, NULL, NULL, ''),
(8, 'PORTADA', 'portada', 0, 0, 0, 0, NULL, NULL, ''),
(20, 'UNKNOWN', 'unknown', 0, 0, 0, 0, NULL, NULL, ''),
(22, 'Deportes', 'deportes', 1, 10, 1, 0, NULL, '', ''),
(23, 'EconomÃ­a', 'economia', 1, 10, 1, 0, NULL, '', ''),
(24, 'PolÃ­tica', 'politica', 1, 10, 1, 0, NULL, '', ''),
(25, 'Cultura', 'cultura', 1, 10, 1, 0, NULL, '', ''),
(26, 'Sociedad', 'sociedad', 1, 10, 1, 0, NULL, '', ''),
(27, 'MÃºsica', 'musica', 1, 10, 1, 25, NULL, '', ''),
(28, 'Cine', 'cine', 1, 10, 1, 25, NULL, '', ''),
(29, 'TelevisiÃ³n', 'television', 1, 10, 1, 25, NULL, '', ''),
(30, 'Curiosidades', 'curiosidades', 1, 10, 9, 0, NULL, '', ''),
(31, 'Fotos de Hoy', 'fotos-de-hoy', 1, 10, 7, 0, NULL, '', ''),
(32, 'Portadas', 'portadas', 1, 10, 14, 0, NULL, '', '');

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

--
-- A extraer datos da táboa `content_positions`
--

INSERT INTO `content_positions` (`pk_fk_content`, `fk_category`, `position`, `placeholder`, `params`, `content_type`) VALUES
(193, 26, 0, 'placeholder_2_1', NULL, 'Widget'),
(61, 26, 0, 'placeholder_2_0', NULL, 'Article'),
(86, 24, 0, 'placeholder_2_3', NULL, 'Article'),
(60, 23, 0, 'placeholder_1_3', NULL, 'Opinion'),
(59, 23, 0, 'placeholder_0_3', NULL, 'Opinion'),
(206, 23, 0, 'placeholder_2_1', NULL, 'Widget'),
(217, 24, 0, 'placeholder_1_3', NULL, 'Article'),
(52, 26, 1, 'placeholder_1_1', NULL, 'Article'),
(85, 24, 0, 'placeholder_0_3', NULL, 'Article'),
(54, 24, 1, 'placeholder_2_1', NULL, 'Opinion'),
(58, 24, 0, 'placeholder_2_1', NULL, 'Opinion'),
(60, 24, 1, 'placeholder_2_0', NULL, 'Opinion'),
(78, 22, 1, 'placeholder_2_3', NULL, 'Article'),
(64, 22, 0, 'placeholder_2_3', NULL, 'Article'),
(81, 22, 0, 'placeholder_1_3', NULL, 'Article'),
(61, 0, 0, 'placeholder_0_3', NULL, 'Article'),
(12, 23, 0, 'placeholder_2_0', NULL, 'Article'),
(183, 24, 0, 'placeholder_2_0', NULL, 'Opinion'),
(74, 25, 0, 'placeholder_0_3', NULL, 'Article'),
(75, 24, 1, 'placeholder_1_0', NULL, 'Article'),
(200, 25, 0, 'placeholder_2_3', NULL, 'Widget'),
(70, 22, 1, 'placeholder_0_3', NULL, 'Article'),
(60, 22, 0, 'placeholder_0_3', NULL, 'Opinion'),
(87, 24, 0, 'placeholder_1_0', NULL, 'Article'),
(80, 24, 1, 'placeholder_0_0', NULL, 'Article'),
(10, 23, 0, 'placeholder_1_1', NULL, 'Article'),
(49, 23, 1, 'placeholder_1_0', NULL, 'Article'),
(47, 23, 0, 'placeholder_1_0', NULL, 'Article'),
(200, 23, 1, 'placeholder_0_1', NULL, 'Widget'),
(76, 26, 0, 'placeholder_1_1', NULL, 'Article'),
(210, 26, 2, 'placeholder_1_0', NULL, 'Article'),
(72, 24, 0, 'placeholder_0_0', NULL, 'Article'),
(206, 0, 0, 'placeholder_2_2', NULL, 'Widget'),
(193, 0, 0, 'placeholder_2_1', NULL, 'Widget'),
(200, 0, 1, 'placeholder_2_0', NULL, 'Widget'),
(240, 0, 0, 'placeholder_2_0', NULL, 'Widget'),
(85, 0, 4, 'placeholder_1_0', NULL, 'Article'),
(84, 0, 3, 'placeholder_1_0', NULL, 'Article'),
(60, 0, 2, 'placeholder_1_0', NULL, 'Opinion'),
(56, 0, 1, 'placeholder_1_0', NULL, 'Opinion'),
(62, 22, 0, 'placeholder_2_2', NULL, 'Article'),
(71, 25, 0, 'placeholder_1_3', NULL, 'Article'),
(51, 25, 0, 'placeholder_2_0', NULL, 'Article'),
(13, 23, 0, 'placeholder_0_1', NULL, 'Article'),
(56, 26, 1, 'placeholder_1_0', NULL, 'Opinion'),
(60, 26, 0, 'placeholder_1_0', NULL, 'Opinion'),
(215, 0, 0, 'placeholder_0_1', NULL, 'Article'),
(67, 24, 0, 'placeholder_2_columns', NULL, 'Article'),
(184, 0, 0, 'placeholder_1_0', NULL, 'Opinion'),
(14, 23, 0, 'placeholder_2_columns', NULL, 'Article'),
(82, 26, 2, 'placeholder_0_0', NULL, 'Article'),
(68, 26, 0, 'placeholder_0_0', NULL, 'Article'),
(200, 22, 0, 'placeholder_2_0', NULL, 'Widget'),
(59, 22, 0, 'placeholder_1_1', NULL, 'Opinion'),
(54, 25, 0, 'placeholder_1_0', NULL, 'Opinion'),
(82, 0, 1, 'placeholder_0_0', NULL, 'Article'),
(53, 23, 0, 'placeholder_0_0', NULL, 'Article'),
(56, 23, 0, 'placeholder_2_3', NULL, 'Opinion'),
(46, 26, 1, 'placeholder_0_0', NULL, 'Article'),
(48, 26, 0, 'placeholder_2_columns', NULL, 'Article'),
(84, 26, 0, 'placeholder_0_3', NULL, 'Article'),
(215, 26, 0, 'placeholder_1_3', NULL, 'Article'),
(200, 26, 0, 'placeholder_2_3', NULL, 'Widget'),
(167, 22, 0, 'placeholder_0_0', NULL, 'Article'),
(77, 22, 0, 'placeholder_1_0', NULL, 'Article'),
(73, 22, 0, 'placeholder_2_columns', NULL, 'Article'),
(60, 25, 1, 'placeholder_1_0', NULL, 'Opinion'),
(86, 25, 0, 'placeholder_0_1', NULL, 'Article'),
(79, 25, 0, 'placeholder_2_columns', NULL, 'Article'),
(167, 0, 0, 'placeholder_0_0', NULL, 'Article'),
(216, 0, 0, 'placeholder_2_columns', NULL, 'Article'),
(11, 0, 0, 'placeholder_1_3', NULL, 'Article'),
(195, 0, 0, 'placeholder_2_3', NULL, 'Widget');

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

--
-- A extraer datos da táboa `letters`
--

INSERT INTO `letters` (`pk_letter`, `author`, `email`) VALUES
(227, 'sdfssdfd', 'support@opennemas.com'),
(228, 'dsf sdfsdf sdfsd', 'support@opennemas.com');

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

--
-- A extraer datos da táboa `menues`
--

INSERT INTO `menues` (`pk_menu`, `name`, `type`, `position`, `params`) VALUES
(1, 'frontpage', '', '', 'a:1:{s:11:"description";s:0:"";}'),
(2, 'opinion', '', NULL, 'a:1:{s:11:"description";s:0:"";}'),
(3, 'mobile', '', NULL, 'a:1:{s:11:"description";s:0:"";}'),
(4, 'album', '', NULL, 'a:1:{s:11:"description";s:0:"";}'),
(5, 'video', '', NULL, 'a:1:{s:11:"description";s:0:"";}'),
(7, 'encuesta', '', NULL, 'a:1:{s:11:"description";s:0:"";}'),
(8, 'subHome', '', NULL, 'a:1:{s:11:"description";s:0:"";}'),
(9, 'statics', '', NULL, 'a:1:{s:11:"description";s:0:"";}');

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

--
-- A extraer datos da táboa `menu_items`
--

INSERT INTO `menu_items` (`pk_item`, `pk_menu`, `title`, `link_name`, `type`, `position`, `pk_father`) VALUES
(22, 4, 'Fotos de Hoy', 'fotos-de-hoy', 'albumCategory', 1, 0),
(22, 1, 'Deportes', 'deportes', 'category', 7, 0),
(1, 1, 'Sociedad', 'sociedad', 'category', 6, 0),
(6, 2, 'PolÃ­tica', 'politica', 'category', 1, 0),
(7, 2, 'Cultura', 'cultura', 'category', 2, 0),
(8, 2, 'Sociedad', 'sociedad', 'category', 3, 0),
(9, 2, 'EconomÃ­a', 'economia', 'category', 4, 0),
(10, 2, 'Deportes', 'deportes', 'category', 5, 0),
(23, 4, 'Sociedad', 'sociedad', 'category', 2, 0),
(24, 4, 'Deportes', 'deportes', 'category', 3, 0),
(37, 8, 'opinion', 'opinion', 'internal', 1, 21),
(31, 5, 'Curiosidades', 'curiosidades', 'videoCategory', 2, 0),
(32, 5, 'Deportes', 'deportes', 'category', 3, 0),
(33, 5, 'Sociedad', 'sociedad', 'category', 4, 0),
(34, 5, 'MÃºsica', 'musica', 'category', 1, 0),
(38, 7, 'Sociedad', 'sociedad', 'category', 1, 0),
(39, 7, 'Deportes', 'deportes', 'category', 2, 0),
(40, 7, 'Cultura', 'cultura', 'category', 3, 0),
(41, 8, 'album', 'album', 'internal', 3, 21),
(42, 8, 'video', 'video', 'internal', 4, 21),
(43, 8, 'poll', 'encuesta', 'internal', 5, 21),
(44, 8, 'letter', 'cartas-al-director', 'internal', 6, 21),
(5, 1, 'EconomÃ­a', 'economia', 'category', 5, 0),
(19, 1, 'PolÃ­tica', 'politica', 'category', 4, 0),
(4, 1, 'OpiniÃ³n', 'opinion', 'internal', 3, 0),
(21, 1, 'Portada', 'home', 'internal', 1, 0),
(25, 1, 'Cultura', 'cultura', 'category', 8, 0);

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

--
-- A extraer datos da táboa `opinions`
--

INSERT INTO `opinions` (`pk_opinion`, `fk_content_categories`, `fk_author`, `fk_author_img`, `with_comment`, `type_opinion`) VALUES
(54, 7, 0, 0, 1, '1'),
(55, 7, 0, 0, 1, '1'),
(56, 7, 0, 0, 1, '1'),
(57, 7, 3, 0, 1, '0'),
(58, 7, 3, 0, 1, '0'),
(59, 7, 3, 0, 1, '0'),
(60, 7, 3, 0, 1, '0'),
(183, 7, 2, 0, 1, '2'),
(184, 7, 2, 0, 1, '2');

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

--
-- A extraer datos da táboa `photos`
--

INSERT INTO `photos` (`pk_photo`, `name`, `path_file`, `size`, `width`, `height`, `nameCat`, `author_name`, `address`) VALUES
(3, '2011092318382331939.jpg', '/2011/09/23/', 219.44, 1280, 800, 'deportes', '', NULL),
(4, '2011092318382332447.jpg', '/2011/09/23/', 137.45, 1280, 800, 'deportes', '', NULL),
(5, '2011092318382332932.jpg', '/2011/09/23/', 118.97, 1280, 800, 'deportes', '', NULL),
(6, '2011092318382333413.jpg', '/2011/09/23/', 112.11, 1024, 768, 'deportes', '', NULL),
(7, '2011092318382333983.jpg', '/2011/09/23/', 134.95, 1024, 683, 'deportes', '', NULL),
(8, '2011092318382334739.jpg', '/2011/09/23/', 107.59, 1024, 685, 'deportes', '', NULL),
(15, '2011092623423270713.jpg', '/2011/09/26/', 45.13, 654, 323, 'economia', '', ''),
(16, '2011092623423279130.jpg', '/2011/09/26/', 206.35, 1200, 1024, 'economia', '', ''),
(17, '2011092623423295793.jpg', '/2011/09/26/', 211.94, 1024, 768, 'economia', '', ''),
(18, '2011092623440566243.jpg', '/2011/09/26/', 44.07, 640, 480, 'fotos-de-hoy', '', ''),
(19, '2011092623440586277.jpg', '/2011/09/26/', 37.6, 560, 560, 'fotos-de-hoy', '', ''),
(20, '2011092623440593500.jpg', '/2011/09/26/', 45.4, 597, 399, 'fotos-de-hoy', '', ''),
(21, '2011092623440599480.jpg', '/2011/09/26/', 219.44, 1280, 800, 'fotos-de-hoy', '', ''),
(22, '2011092623455095851.jpg', '/2011/09/26/', 176.37, 1920, 1200, 'fotos-de-hoy', '', ''),
(23, '2011092623455119453.jpg', '/2011/09/26/', 135.96, 1920, 1200, 'fotos-de-hoy', '', ''),
(24, '2011092623455140386.jpg', '/2011/09/26/', 261.95, 1920, 1200, 'fotos-de-hoy', '', ''),
(25, '2011092623455162385.jpg', '/2011/09/26/', 191.55, 1920, 1200, 'fotos-de-hoy', '', ''),
(26, '2011092623455184671.jpg', '/2011/09/26/', 76.36, 1024, 768, 'fotos-de-hoy', '', ''),
(27, '2011092623455194628.jpg', '/2011/09/26/', 141.22, 1024, 768, 'fotos-de-hoy', '', ''),
(28, '2011092623455246677.jpg', '/2011/09/26/', 101.43, 1024, 768, 'fotos-de-hoy', '', ''),
(29, '2011092623455220326.jpg', '/2011/09/26/', 34.27, 1024, 768, 'fotos-de-hoy', '', ''),
(30, '2011092623455229628.jpg', '/2011/09/26/', 55.13, 1024, 768, 'fotos-de-hoy', '', ''),
(31, '2011092623455239081.jpg', '/2011/09/26/', 86.71, 1024, 768, 'fotos-de-hoy', '', ''),
(32, '2011092623503366144.jpg', '/2011/09/26/', 137.37, 1440, 900, 'deportes', '', ''),
(33, '2011092623503393155.jpg', '/2011/09/26/', 42.8, 990, 633, 'deportes', '', ''),
(34, '2011092623503412663.jpg', '/2011/09/26/', 137.45, 1280, 800, 'deportes', '', ''),
(35, '2011092623503419708.jpg', '/2011/09/26/', 219.44, 1280, 800, 'deportes', '', ''),
(36, '2011092623503437565.jpg', '/2011/09/26/', 118.97, 1280, 800, 'deportes', '', ''),
(37, '2011092623503454494.jpg', '/2011/09/26/', 112.11, 1024, 768, 'deportes', '', ''),
(38, '2011092623503464732.jpg', '/2011/09/26/', 107.59, 1024, 685, 'deportes', '', ''),
(39, '2011092623503474229.jpg', '/2011/09/26/', 49.83, 450, 300, 'deportes', '', ''),
(40, '2011092623535613757.jpg', '/2011/09/26/', 22.01, 420, 295, 'sociedad', '', ''),
(41, '2011092623535618935.jpg', '/2011/09/26/', 47.35, 654, 323, 'sociedad', '', ''),
(42, '2011092623535626059.jpg', '/2011/09/26/', 23.94, 654, 323, 'sociedad', '', ''),
(43, '2011092623535632579.jpg', '/2011/09/26/', 43.99, 654, 323, 'sociedad', '', ''),
(44, '2011092623535639323.jpg', '/2011/09/26/', 49.83, 450, 300, 'sociedad', '', ''),
(115, '2011092822582896422.jpg', '/2011/09/28/', 24.88, 728, 90, 'publicidad', '', ''),
(116, '2011092822582899076.jpg', '/2011/09/28/', 45.27, 300, 250, 'publicidad', '', ''),
(117, '2011092822582942101.jpg', '/2011/09/28/', 14.61, 234, 90, 'publicidad', '', ''),
(119, '2011092823065820527.jpg', '/2011/09/28/', 48.16, 300, 300, 'publicidad', '', ''),
(120, '2011092823065825091.jpg', '/2011/09/28/', 39.35, 400, 400, 'publicidad', '', ''),
(121, '2011092823065829582.jpg', '/2011/09/28/', 44.77, 800, 600, 'publicidad', '', ''),
(122, '2011092823065836215.jpg', '/2011/09/28/', 49.91, 160, 600, 'publicidad', '', ''),
(123, '2011092823065838659.jpg', '/2011/09/28/', 29.62, 728, 90, 'publicidad', '', ''),
(124, '2011092823065841147.jpg', '/2011/09/28/', 51.13, 800, 600, 'publicidad', '', ''),
(125, '2011092823065847911.jpg', '/2011/09/28/', 13.3, 300, 60, 'publicidad', '', ''),
(126, '2011092823065851033.jpg', '/2011/09/28/', 14.61, 234, 90, 'publicidad', '', ''),
(168, '2011092910464053185.jpg', '/2011/09/29/', 28.03, 652, 186, 'cultura', '', ''),
(169, '2011092910464066501.jpg', '/2011/09/29/', 39.72, 654, 130, 'cultura', '', ''),
(170, '2011092910464070031.jpg', '/2011/09/29/', 74.79, 654, 323, 'cultura', '', ''),
(171, '2011092910481893666.jpg', '/2011/09/29/', 163.2, 1291, 904, 'cultura', '', ''),
(172, '2011092910481911030.jpg', '/2011/09/29/', 86.41, 1000, 956, 'cultura', '', ''),
(173, '2011092910481925098.jpg', '/2011/09/29/', 75.03, 1200, 833, 'cultura', '', ''),
(174, '2011092910481942502.jpg', '/2011/09/29/', 108.88, 1576, 1050, 'cultura', '', ''),
(175, '2011092910481961045.jpg', '/2011/09/29/', 238.26, 850, 1200, 'cultura', '', ''),
(176, '2011092910481972297.jpg', '/2011/09/29/', 100.38, 800, 1145, 'cultura', '', ''),
(177, '2011092910481983500.jpg', '/2011/09/29/', 97.94, 800, 1193, 'cultura', '', ''),
(178, '2011092910481993878.jpg', '/2011/09/29/', 91.22, 800, 1205, 'cultura', '', ''),
(179, '2011092910482043660.jpg', '/2011/09/29/', 87.28, 800, 1212, 'cultura', '', ''),
(180, '2011092910482014796.jpg', '/2011/09/29/', 57.67, 800, 1091, 'cultura', '', ''),
(214, '2011100818575131578.png', '/2011/10/08/', 19.29, 400, 240, 'sociedad', '', '');

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

--
-- A extraer datos da táboa `polls`
--

INSERT INTO `polls` (`pk_poll`, `total_votes`, `used_ips`, `subtitle`, `visualization`, `with_comment`) VALUES
(163, 5, 'a:2:{i:0;a:2:{s:2:"ip";s:9:"127.0.1.1";s:5:"count";i:4;}i:1;a:2:{s:2:"ip";s:14:"178.139.12.224";s:5:"count";i:1;}}', 'Suspendisse nisl ', 0, 1),
(164, 19, 'a:4:{i:0;a:2:{s:2:"ip";s:9:"127.0.1.1";s:5:"count";i:16;}i:1;a:2:{s:2:"ip";s:11:"95.16.86.69";s:5:"count";i:1;}i:2;a:2:{s:2:"ip";s:14:"83.165.253.141";s:5:"count";i:1;}i:3;a:2:{s:2:"ip";s:9:"127.0.0.1";s:5:"count";i:1;}}', 'Phasellus pellentesque ', 1, 1),
(165, 8, 'a:2:{i:0;a:2:{s:2:"ip";s:9:"127.0.1.1";s:5:"count";i:7;}i:1;a:2:{s:2:"ip";s:11:"95.16.86.69";s:5:"count";i:1;}}', 'faucibus congue', 0, 1),
(166, 23, 'a:3:{i:0;a:2:{s:2:"ip";s:9:"127.0.1.1";s:5:"count";i:21;}i:1;a:2:{s:2:"ip";s:14:"91.116.137.243";s:5:"count";i:1;}i:2;a:2:{s:2:"ip";s:11:"95.16.86.29";s:5:"count";i:1;}}', 'Fusce a tortor tortor.', 1, 1);

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

--
-- A extraer datos da táboa `poll_items`
--

INSERT INTO `poll_items` (`pk_item`, `fk_pk_poll`, `item`, `metadata`, `votes`) VALUES
(23, 163, 'ullamcorper', NULL, 1),
(22, 163, 'lacinia', NULL, 3),
(21, 163, 'curabitur', NULL, 0),
(7, 164, 'Phasellus', 'phasellus', 3),
(8, 164, ' pellentesque ', 'pellentesque', 5),
(9, 164, 'Maecenas', 'maecenas', 3),
(10, 164, 'tortor', 'tortor', 6),
(11, 164, 'commodo', 'commodo', 2),
(12, 165, 'Si', 'si', 3),
(13, 165, 'No', 'no', 3),
(14, 165, 'Tal Vez', 'tal, vez', 2),
(15, 166, 'phasellus', 'phasellus', 5),
(16, 166, 'massa', 'massa', 6),
(17, 166, 'eros', 'eros', 2),
(18, 166, 'caesar', 'caesar', 2),
(19, 166, 'ullamcorper', 'ullamcorper', 5),
(20, 166, 'totor', 'totor', 3);

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

--
-- A extraer datos da táboa `related_contents`
--

INSERT INTO `related_contents` (`pk_content1`, `pk_content2`, `relationship`, `text`, `position`, `posinterior`, `verportada`, `verinterior`) VALUES
(82, 58, NULL, NULL, 0, 2, 0, 1),
(82, 46, NULL, NULL, 0, 1, 0, 1),
(82, 58, NULL, NULL, 3, 0, 1, 0),
(82, 46, NULL, NULL, 2, 0, 1, 0),
(82, 61, NULL, NULL, 1, 0, 1, 0),
(210, 46, NULL, NULL, 1, 0, 1, 0),
(210, 61, NULL, NULL, 0, 1, 0, 1),
(210, 46, NULL, NULL, 0, 2, 0, 1),
(210, 52, NULL, NULL, 0, 3, 0, 1),
(210, 48, NULL, NULL, 0, 4, 0, 1),
(215, 69, NULL, NULL, 0, 3, 0, 1),
(215, 62, NULL, NULL, 0, 2, 0, 1),
(215, 167, NULL, NULL, 0, 1, 0, 1),
(216, 82, NULL, NULL, 0, 2, 0, 1),
(216, 210, NULL, NULL, 0, 1, 0, 1),
(216, 52, NULL, NULL, 3, 0, 1, 0),
(216, 46, NULL, NULL, 2, 3, 1, 1),
(216, 61, NULL, NULL, 1, 0, 1, 0),
(217, 46, NULL, NULL, 1, 0, 1, 0),
(217, 52, NULL, NULL, 2, 0, 1, 0),
(217, 46, NULL, NULL, 0, 1, 0, 1),
(75, 46, NULL, NULL, 1, 0, 1, 0),
(75, 52, NULL, NULL, 2, 0, 1, 0),
(75, 46, NULL, NULL, 0, 1, 0, 1),
(72, 217, NULL, NULL, 1, 0, 1, 0),
(72, 86, NULL, NULL, 2, 0, 1, 0),
(72, 85, NULL, NULL, 3, 0, 1, 0),
(72, 86, NULL, NULL, 0, 1, 0, 1),
(72, 85, NULL, NULL, 0, 2, 0, 1),
(72, 87, NULL, NULL, 0, 3, 0, 1),
(51, 10, NULL, NULL, 1, 0, 1, 0),
(51, 52, NULL, NULL, 2, 0, 1, 0),
(71, 46, NULL, NULL, 0, 3, 0, 1),
(71, 49, NULL, NULL, 0, 2, 0, 1),
(71, 47, NULL, NULL, 0, 1, 0, 1),
(71, 167, NULL, NULL, 3, 0, 1, 0),
(71, 215, NULL, NULL, 2, 0, 1, 0),
(71, 216, NULL, NULL, 1, 0, 1, 0),
(79, 60, NULL, NULL, 1, 0, 1, 0),
(79, 58, NULL, NULL, 2, 0, 1, 0),
(79, 57, NULL, NULL, 3, 0, 1, 0),
(79, 59, NULL, NULL, 0, 1, 0, 1),
(79, 60, NULL, NULL, 0, 2, 0, 1),
(85, 46, NULL, NULL, 0, 2, 0, 1),
(85, 12, NULL, NULL, 0, 1, 0, 1),
(85, 87, NULL, NULL, 4, 0, 1, 0),
(85, 49, NULL, NULL, 3, 0, 1, 0),
(85, 14, NULL, NULL, 2, 0, 1, 0),
(85, 85, NULL, NULL, 1, 0, 1, 0),
(85, 86, NULL, NULL, 0, 3, 0, 1),
(14, 53, NULL, NULL, 2, 0, 1, 0),
(14, 14, NULL, NULL, 1, 0, 1, 0),
(14, 64, NULL, NULL, 3, 0, 1, 0),
(68, 210, NULL, NULL, 1, 0, 1, 0),
(49, 46, NULL, NULL, 0, 3, 0, 1),
(49, 68, NULL, NULL, 0, 2, 0, 1),
(49, 61, NULL, NULL, 0, 1, 0, 1),
(49, 82, NULL, NULL, 2, 0, 1, 0),
(49, 210, NULL, NULL, 1, 0, 1, 0),
(68, 68, NULL, NULL, 2, 0, 1, 0),
(61, 210, NULL, NULL, 1, 0, 1, 0),
(61, 84, NULL, NULL, 2, 0, 1, 0),
(61, 167, NULL, NULL, 0, 1, 0, 1),
(61, 85, NULL, NULL, 0, 2, 0, 1),
(61, 84, NULL, NULL, 0, 3, 0, 1),
(48, 61, NULL, NULL, 1, 0, 1, 0),
(48, 68, NULL, NULL, 2, 0, 1, 0),
(48, 46, NULL, NULL, 3, 0, 1, 0),
(48, 84, NULL, NULL, 0, 1, 0, 1),
(48, 210, NULL, NULL, 0, 2, 0, 1),
(48, 215, NULL, NULL, 0, 3, 0, 1);

-- --------------------------------------------------------

--
-- Estrutura da táboa `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `name` varchar(128) NOT NULL DEFAULT '',
  `value` longtext NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- A extraer datos da táboa `settings`
--

INSERT INTO `settings` (`name`, `value`) VALUES
('facebook_page', 's:52:"http://www.facebook.com/pages/OpenNemas/282535299100";'),
('twitter_page', 's:28:"http://twitter.com/opennemas";'),
('facebook_id', 's:12:"282535299100";'),
('site_footer', 's:219:"<p><strong>Plataforma Opennemas - CMS for digital newspapers</strong><br />\r\nCarretera Cabeanca - Boveda (priorato) s/n<br />\r\nBoveda, Amoeiro<br />\r\n32980, Ourense<br />\r\nTelf: +34 988980045<br />\r\nOpenHost, S.L.</p>\r\n";'),
('site_title', 's:84:"Opennemas newspapers - CMS periodico digital - Online service for digital newspapers";'),
('site_description', 's:84:"Opennemas newspapers - CMS periodico digital - Online service for digital newspapers";'),
('europapress_server_auth', 'a:3:{s:6:"server";s:0:"";s:8:"username";s:0:"";s:8:"password";s:0:"";}'),
('site_keywords', 's:74:"CMS, openNemas, servicio, online, periÃ³dico, digital, service, newspapers";'),
('time_zone', 's:3:"335";'),
('site_language', 's:5:"es_ES";'),
('mail_server', 's:9:"localhost";'),
('mail_username', 's:0:"";'),
('mail_password', 's:0:"";'),
('newsletter_sender', 's:30:"no-reply@postman.opennemas.com"'),
('max_mailing', 's:1:"0"'),
('last_invoice', 's:19:"2013-07-28 10:00:00"'),
('google_maps_api_key', 's:86:"ABQIAAAA_RE85FLaf_hXdhkxaS463hQC49KlvU2s_1jV47V5-i8q6UJ2IBQiAxw97Jt7tEWzuIY513Qutp-Cqg";'),
('google_custom_search_api_key', 's:0:"";'),
('facebook', 'a:2:{s:7:"api_key";s:1:" ";s:10:"secret_key";s:1:" ";}'),
('google_analytics', 'a:1:{s:7:"api_key";s:1:" ";}'),
('piwik', 'a:3:{s:7:"page_id";s:0:"";s:10:"server_url";s:0:"";s:10:"token_auth";s:0:"";}'),
('recaptcha', 'a:2:{s:10:"public_key";s:40:"6LfpY8ISAAAAAAuChcU2Agdwg8YzhprxZZ55B7Is";s:11:"private_key";s:40:"6LfpY8ISAAAAAAuChcU2Agdwg8YzhprxZZ55B7Is";}'),
('items_per_page', 's:2:"20";'),
('refresh_interval', 's:3:"900";'),
('advertisements_enabled', 'b:0;'),
('log_level', 's:6:"normal";'),
('log_enabled', 's:2:"on";'),
('log_db_enabled', 's:2:"on";'),
('newsletter_maillist', 'a:2:{s:4:"name";s:8:"Openhost";s:5:"email";s:30:"newsletter@lists.opennemas.com";}'),
('site_agency', 's:13:"opennemas.com";'),
('activated_modules', 'a:17:{i:0;s:15:"ADVANCED_SEARCH";i:1;s:15:"ARTICLE_MANAGER";i:2;s:13:"CACHE_MANAGER";i:3;s:16:"CATEGORY_MANAGER";i:4;s:15:"COMMENT_MANAGER";i:5;s:12:"FILE_MANAGER";i:6;s:17:"FRONTPAGE_MANAGER";i:7;s:13:"IMAGE_MANAGER";i:8;s:15:"KEYWORD_MANAGER";i:9;s:15:"LIBRARY_MANAGER";i:10;s:12:"MENU_MANAGER";i:11;s:15:"OPINION_MANAGER";i:12;s:16:"SETTINGS_MANAGER";i:13;s:20:"STATIC_PAGES_MANAGER";i:14;s:13:"TRASH_MANAGER";i:15;s:17:"USERVOICE_SUPPORT";i:16;s:14:"WIDGET_MANAGER";}'),
('album_settings', 'a:6:{s:12:"total_widget";s:1:"4";s:10:"crop_width";s:3:"300";s:11:"crop_height";s:3:"240";s:14:"orderFrontpage";s:8:"favorite";s:9:"time_last";s:3:"100";s:11:"total_front";s:1:"6";}'),
('video_settings', 'a:3:{s:12:"total_widget";s:1:"4";s:11:"total_front";s:1:"2";s:13:"total_gallery";s:2:"20";}'),
('poll_settings', 'a:3:{s:9:"typeValue";s:7:"percent";s:9:"widthPoll";s:3:"600";s:10:"heightPoll";s:3:"500";}'),
('opinion_settings', 'a:2:{s:14:"total_director";s:1:"2";s:15:"total_editorial";s:1:"3";}'),
('image_front_thumb_size', 'a:2:{s:5:"width";s:3:"350";s:6:"height";s:3:"250";}'),
('image_inner_thumb_size', 'a:2:{s:5:"width";s:3:"480";s:6:"height";s:3:"250";}'),
('image_thumb_size', 'a:2:{s:5:"width";s:3:"140";s:6:"height";s:3:"100";}'),
('favico', 's:0:"";'),
('site_name', 's:19:"Opennemas newspaper";'),
('site_logo', 's:0:"";'),
('webmastertools_bing', 's:0:"";'),
('section_settings', 'a:1:{s:9:"allowLogo";s:1:"1";}'),
('webmastertools_google', 's:0:"";'),
('site_color', 's:0:"";'),
('continue', 's:1:"1";'),
('max_session_lifetime', 's:2:"30";'),
('frontpage_0_last_saved', 's:24:"2013-06-17T18:03:08+0200";'),
('ojd', 'a:1:{s:7:"page_id";s:0:"";}'),
('paypal_mail', 's:0:"";'),
('onm_digest_user', 's:0:"";'),
('youtube_page', 's:0:"";'),
('google_news_name', 's:0:"";'),
('google_page', 's:0:"";'),
('mobile_logo', 's:0:"";'),
('onm_digest_pass', 's:0:"";'),
('comscore', 'a:1:{s:7:"page_id";s:0:"";}');

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

--
-- A extraer datos da táboa `static_pages`
--

INSERT INTO `static_pages` (`pk_static_page`) VALUES
(225);

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

--
-- A extraer datos da táboa `usermeta`
--

INSERT INTO `usermeta` (`user_id`, `meta_key`, `meta_value`) VALUES
(135, 'user_language', 'default'),
(132, 'user_language', 'default'),
(136, 'user_language', 'default'),
(7, 'user_language', 'default'),
(137, 'user_language', 'default');

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

--
-- A extraer datos da táboa `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `sessionexpire`, `url`, `bio`, `avatar_img_id`, `email`, `name`, `type`, `deposit`, `token`, `activated`, `fk_user_group`) VALUES
(3, 'convallis-vitae', '4ee78fcf5fa4d511213d004f69453d80', 15, '', '', 0, 'convallis@opennemas.com', 'Convallis Vitae', 0, 0, NULL, 0, '3'),
(2, 'director', '4ee78fcf5fa47511213d004f69453d80', 15, '', '', 0, 'director@opennemas.com', 'Director', 0, 0, NULL, 0, '3'),
(1, 'editorial', '0dc4de960c0e39e92e8d7136239e77ed', 15, '', '', 0, 'editorial@opennemas.com', 'Editorial', 0, 0, NULL, 0, '3'),
(4, 'macada', '2f575705daf41049194613e47027200b', 30, '', '', 0, 'david.martinez@openhost.es', 'David Martinez', 0, 0, NULL, 1, '4'),
(5, 'fran', '6d87cd9493f11b830bbfdf628c2c4f08', 65, '', '', 0, 'fran@openhost.es', 'Francisco Dieguez', 0, 0, NULL, 1, '4'),
(6, 'alex', '4c246829b53bc5712d52ee777c52ebe7', 60, '', '', 0, 'alex@openhost.es', 'Alexandre Rico', 0, 0, NULL, 1, '4'),
(7, 'sandra', 'bd80e7c35b56dccd2d1796cf39cd05f6', 99, '', '', 0, 'sandra@openhost.es', 'Sandra Pereira', 0, 0, NULL, 1, '4');

-- --------------------------------------------------------

--
-- Estrutura da táboa `users_content_categories`
--

CREATE TABLE IF NOT EXISTS `users_content_categories` (
  `pk_fk_user` int(10) unsigned NOT NULL,
  `pk_fk_content_category` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pk_fk_user`,`pk_fk_content_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- A extraer datos da táboa `users_content_categories`
--

INSERT INTO `users_content_categories` (`pk_fk_user`, `pk_fk_content_category`) VALUES
(7, 0),
(132, 0),
(133, 0),
(133, 22),
(133, 23),
(133, 24),
(133, 25),
(133, 26),
(133, 27),
(133, 28),
(133, 29),
(133, 30),
(133, 31),
(134, 0),
(134, 22),
(134, 23),
(134, 24),
(134, 25),
(134, 26),
(134, 27),
(134, 28),
(134, 29),
(134, 30),
(134, 31),
(135, 0),
(136, 0),
(136, 22),
(136, 23),
(136, 24),
(136, 25),
(136, 26),
(136, 27),
(136, 28),
(136, 29),
(136, 30),
(136, 31),
(136, 32),
(137, 0),
(137, 22),
(137, 23),
(137, 24),
(137, 25),
(137, 26),
(137, 27),
(137, 28),
(137, 29),
(137, 30),
(137, 31);

-- --------------------------------------------------------

--
-- Estrutura da táboa `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `pk_user_group` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`pk_user_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- A extraer datos da táboa `user_groups`
--

INSERT INTO `user_groups` (`pk_user_group`, `name`) VALUES
(3, 'Autores'),
(4, 'Masters'),
(5, 'Administrador'),
(6, 'Usuarios');

-- --------------------------------------------------------

--
-- Estrutura da táboa `user_groups_privileges`
--

CREATE TABLE IF NOT EXISTS `user_groups_privileges` (
  `pk_fk_user_group` int(10) unsigned NOT NULL,
  `pk_fk_privilege` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pk_fk_user_group`,`pk_fk_privilege`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- A extraer datos da táboa `user_groups_privileges`
--

INSERT INTO `user_groups_privileges` (`pk_fk_user_group`, `pk_fk_privilege`) VALUES
(6, 1),
(6, 2),
(6, 3),
(6, 4),
(6, 5),
(6, 6),
(6, 7),
(6, 8),
(6, 9),
(6, 10),
(6, 11),
(6, 12),
(6, 13),
(6, 15),
(6, 16),
(6, 17),
(6, 26),
(6, 27),
(6, 28),
(6, 29),
(6, 30),
(6, 31),
(6, 32),
(6, 33),
(6, 34),
(6, 35),
(6, 36),
(6, 37),
(6, 38),
(6, 39),
(6, 40),
(6, 41),
(6, 60),
(6, 61),
(6, 62),
(6, 63),
(6, 64),
(6, 65),
(6, 66),
(6, 67),
(6, 68),
(6, 69),
(6, 70),
(6, 82),
(6, 83),
(6, 84),
(6, 85),
(6, 104),
(6, 105),
(6, 106),
(6, 107),
(6, 108),
(6, 109),
(6, 114),
(6, 115),
(6, 116),
(6, 117),
(6, 118),
(6, 122),
(6, 123),
(6, 124),
(6, 132),
(6, 133),
(6, 134),
(6, 161),
(6, 162),
(6, 164);

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

--
-- A extraer datos da táboa `videos`
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

--
-- A extraer datos da táboa `widgets`
--

INSERT INTO `widgets` (`pk_widget`, `content`, `renderlet`) VALUES
(230, 'ArticleLast', 'intelligentwidget'),
(186, 'AllHeadlines', 'intelligentwidget'),
(229, 'AlbumLast', 'intelligentwidget'),
(192, 'LatestComments', 'intelligentwidget'),
(193, 'LatestCommentsNew', 'intelligentwidget'),
(195, 'LatestOpinions', 'intelligentwidget'),
(200, 'MostSeeingVotedCommentedContent', 'intelligentwidget'),
(202, 'OpinionAuthorList', 'intelligentwidget'),
(206, 'PastHeadlinesMostViewed', 'intelligentwidget'),
(226, 'MostViewedCommentedVotedVideos', 'intelligentwidget'),
(231, 'CalendarNewslibrary', 'intelligentwidget'),
(232, 'FacebookButton', 'intelligentwidget'),
(233, 'Facebook', 'intelligentwidget'),
(235, 'OpinionByauthor', 'intelligentwidget'),
(236, 'OpinionFavorite', 'intelligentwidget'),
(237, 'TodayNews', 'intelligentwidget'),
(240, '<div class="social-follow"><a class="twitter-follow-button" data-lang="pt" data-show-count="true" data-size="small" href="https://twitter.com/Opennemas">Follow @Opennemas</a> <script type="text/javascript">// <![CDATA[\r\n!function(d,s,id){ var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){ js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs); } }(document,"script","twitter-wjs");\r\n// ]]></script></div>\r\n\r\n<a class="twitter-timeline" href="https://twitter.com/search?q=opennemas+OR+%40opennemas+OR+%23opennemas" data-widget-id="346651300093640705">Opennemas Newspapers Tweets</a>\r\n<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?''http'':''https'';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>\r\n', 'html');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
