-- MySQL dump 10.13  Distrib 5.7.15, for Linux (x86_64)
--
-- Host: 172.17.0.7    Database: onm-instances
-- ------------------------------------------------------
-- Server version	5.6.26

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
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client` (
  `id` bigint(20) NOT NULL,
  `first_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `company` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vat_number` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `postal_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `state` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client`
--

LOCK TABLES `client` WRITE;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
/*!40000 ALTER TABLE `client` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extension`
--

DROP TABLE IF EXISTS `extension`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extension` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name` text COLLATE utf8_unicode_ci,
  `type` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(140) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `about` text COLLATE utf8_unicode_ci,
  `images` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extension`
--

LOCK TABLES `extension` WRITE;
/*!40000 ALTER TABLE `extension` DISABLE KEYS */;
/*!40000 ALTER TABLE `extension` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extension_meta`
--

DROP TABLE IF EXISTS `extension_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extension_meta` (
  `extension_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_value` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`extension_id`,`meta_key`),
  KEY `extension_id` (`extension_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extension_meta`
--

LOCK TABLES `extension_meta` WRITE;
/*!40000 ALTER TABLE `extension_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `extension_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instance_meta`
--

DROP TABLE IF EXISTS `instance_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instance_meta` (
  `instance_id` bigint(20) unsigned NOT NULL,
  `meta_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_value` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`instance_id`,`meta_key`),
  KEY `instance_id` (`instance_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instance_meta`
--

LOCK TABLES `instance_meta` WRITE;
/*!40000 ALTER TABLE `instance_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `instance_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instances`
--

DROP TABLE IF EXISTS `instances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `instances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `internal_name` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `domains` varchar(500) NOT NULL,
  `main_domain` varchar(255) NOT NULL,
  `settings` text,
  `activated` tinyint(1) NOT NULL,
  `contact_mail` varchar(255) NOT NULL,
  `domain_expire` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `contents` int(10) unsigned NOT NULL DEFAULT '0',
  `attachments` int(10) unsigned NOT NULL DEFAULT '0',
  `articles` int(10) unsigned NOT NULL DEFAULT '0',
  `opinions` int(10) unsigned NOT NULL DEFAULT '0',
  `advertisements` int(10) unsigned NOT NULL DEFAULT '0',
  `albums` int(10) unsigned NOT NULL DEFAULT '0',
  `letters` int(10) unsigned NOT NULL DEFAULT '0',
  `polls` int(10) unsigned NOT NULL DEFAULT '0',
  `photos` int(10) unsigned NOT NULL DEFAULT '0',
  `videos` int(10) unsigned NOT NULL DEFAULT '0',
  `widgets` int(10) unsigned NOT NULL DEFAULT '0',
  `static_pages` int(10) unsigned NOT NULL DEFAULT '0',
  `media_size` double NOT NULL DEFAULT '0',
  `alexa` int(10) unsigned NOT NULL,
  `page_views` bigint(20) unsigned NOT NULL DEFAULT '0',
  `emails` int(10) unsigned NOT NULL DEFAULT '0',
  `users` int(10) unsigned NOT NULL DEFAULT '0',
  `activated_modules` text,
  `support_plan` varchar(255) NOT NULL,
  `owner_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `domain_name` (`domains`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `instances`
--

LOCK TABLES `instances` WRITE;
/*!40000 ALTER TABLE `instances` DISABLE KEYS */;
INSERT INTO `instances` VALUES (1,'opennemas','Opennemas Default instance','opennemas.opennemas.localhost,opennemas.test.opennemas.com,opennemas.staging.opennemas.com','','a:7:{s:13:\"TEMPLATE_USER\";s:5:\"basic\";s:9:\"MEDIA_URL\";s:0:\"\";s:7:\"BD_TYPE\";s:6:\"mysqli\";s:7:\"BD_HOST\";s:9:\"localhost\";s:11:\"BD_DATABASE\";s:9:\"c-default\";s:7:\"BD_USER\";s:4:\"root\";s:7:\"BD_PASS\";s:4:\"root\";}',1,'devs@opennemas.com',NULL,'0000-00-00 00:00:00',NULL,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,NULL,'',NULL);
/*!40000 ALTER TABLE `instances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification`
--

DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci,
  `body` text COLLATE utf8_unicode_ci,
  `target` text COLLATE utf8_unicode_ci,
  `users` text COLLATE utf8_unicode_ci,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `creator` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `style` text COLLATE utf8_unicode_ci,
  `fixed` tinyint(1) NOT NULL DEFAULT '0',
  `forced` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification`
--

LOCK TABLES `notification` WRITE;
/*!40000 ALTER TABLE `notification` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase`
--

DROP TABLE IF EXISTS `purchase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `client` text COLLATE utf8_unicode_ci,
  `details` text COLLATE utf8_unicode_ci,
  `method` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fee` double NOT NULL DEFAULT '0',
  `total` double NOT NULL DEFAULT '0',
  `step` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `instance_id` bigint(20) NOT NULL,
  `client_id` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_id` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_id` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase`
--

LOCK TABLES `purchase` WRITE;
/*!40000 ALTER TABLE `purchase` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES ('time_zone','s:3:\"334\";'),('site_language','s:5:\"es_ES\";'),('log_level','s:6:\"normal\";'),('log_enabled','s:2:\"on\";'),('log_db_enabled','s:2:\"on\";');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_groups`
--

LOCK TABLES `user_groups` WRITE;
/*!40000 ALTER TABLE `user_groups` DISABLE KEYS */;
INSERT INTO `user_groups` VALUES (4,'Masters');
/*!40000 ALTER TABLE `user_groups` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `user_groups_privileges`
--

LOCK TABLES `user_groups_privileges` WRITE;
/*!40000 ALTER TABLE `user_groups_privileges` DISABLE KEYS */;
INSERT INTO `user_groups_privileges` VALUES (4,190);
/*!40000 ALTER TABLE `user_groups_privileges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_notification`
--

DROP TABLE IF EXISTS `user_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_notification` (
  `notification_id` int(10) unsigned NOT NULL,
  `instance_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user` text COLLATE utf8_unicode_ci NOT NULL,
  `read_date` datetime DEFAULT NULL,
  `view_date` datetime DEFAULT NULL,
  `click_date` datetime DEFAULT NULL,
  `open_date` datetime DEFAULT NULL,
  PRIMARY KEY (`notification_id`,`instance_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_notification`
--

LOCK TABLES `user_notification` WRITE;
/*!40000 ALTER TABLE `user_notification` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_notification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usermeta`
--

DROP TABLE IF EXISTS `usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usermeta` (
  `user_id` bigint(20) unsigned NOT NULL,
  `meta_key` varchar(255) NOT NULL DEFAULT '',
  `meta_value` text,
  PRIMARY KEY (`user_id`,`meta_key`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usermeta`
--

LOCK TABLES `usermeta` WRITE;
/*!40000 ALTER TABLE `usermeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `usermeta` ENABLE KEYS */;
UNLOCK TABLES;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'macada','2f575705daf41049194613e47027200b',30,'',NULL,0,'david.martinez@openhost.es','David Martinez',0,0,NULL,1,'4'),(5,'fran','6d87cd9493f11b830bbfdf628c2c4f08',65,'',NULL,0,'fran@openhost.es','Francisco Dieguez',0,0,NULL,1,'4'),(4,'alex','4c246829b53bc5712d52ee777c52ebe7',60,'',NULL,0,'alex@openhost.es','Alexandre Rico',0,0,NULL,1,'4'),(7,'sandra','bd80e7c35b56dccd2d1796cf39cd05f6',99,'',NULL,0,'sandra@openhost.es','Sandra Pereira',0,0,NULL,1,'4');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-09-15 11:50:31
