-- This file contains all the changes that has to be applied
-- in the default instance database.

-- Move all the applied changes into the DB-default-applied-changes.sql
-- file whenever it's possible

-- Please check rigth sql, use ; in the end of lines & -- for comments.
-- Write date with each sentence and with stack method. (last writed in the top)

--
-- New sql for sync dysqus comments
--
CREATE TABLE IF NOT EXISTS `commentsmeta` (
  `fk_content` bigint(32) NOT NULL,
  `meta_name` varchar(255) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`fk_content`,`meta_name`),
  KEY `fk_content` (`fk_content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `comments` CHANGE `id` `id` BIGINT( 32 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `comments` CHANGE `content_id` `content_id` BIGINT( 32 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `comments` CHANGE `author` `author` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `comments` CHANGE `author_email` `author_email` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '';
ALTER TABLE `comments` CHANGE `author_ip` `author_ip` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '';
ALTER TABLE `comments` CHANGE `parent_id` `parent_id` BIGINT( 32 ) NOT NULL DEFAULT '0';

--
-- Already executed in production servers
--
ALTER TABLE translation_ids ADD `slug` VARCHAR(200) DEFAULT  '' AFTER  `type`;

-- 2013-10-08
--
-- Table structure for table `action_counters`
--
DROP TABLE IF EXISTS `action_counters`;
CREATE TABLE IF NOT EXISTS `action_counters` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `action_name` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `counter` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `action_name` (`action_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


REPLACE INTO settings (`name`, `value`) VALUES ('mail_username', 's:0:""');
REPLACE INTO settings (`name`, `value`) VALUES ('mail_sender', 's:30:"no-reply@postman.opennemas.com"');


-- 2013-07-17
ALTER TABLE `contents`
  ADD  `body` LONGTEXT NOT NULL AFTER  `description`,
  CHANGE  `description`  `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  CHANGE  `metadata`  `metadata` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  CHANGE  `slug`  `slug` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  CHANGE  `params`  `params` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
  CHANGE  `category_name`  `category_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  CHANGE  `urn_source`  `urn_source` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  CHANGE  `title`  `title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

UPDATE contents INNER JOIN articles ON contents.pk_content = articles.pk_article SET contents.body=articles.body;
UPDATE contents INNER JOIN opinions ON contents.pk_content = opinions.pk_opinion SET contents.body=opinions.body;
UPDATE contents INNER JOIN letters ON contents.pk_content = letters.pk_letter SET contents.body=letters.body;
UPDATE contents INNER JOIN static_pages ON contents.pk_content = static_pages.pk_static_page SET contents.body=static_pages.body;

ALTER TABLE  `users` ADD INDEX  `user_username` (  `username` );
ALTER TABLE  `users` ADD INDEX  `user_email` (  `email` );

ALTER TABLE `contents`
  DROP `placeholder`,
  DROP `home_placeholder`;
ALTER TABLE `articles` DROP `body`;
ALTER TABLE `letters` DROP `body`;
ALTER TABLE `opinions` DROP `body`;
ALTER TABLE `static_pages`
DROP `body`,
DROP `slug`;

ALTER TABLE `articles`
DROP `columns`,
DROP `home_columns`;

ALTER TABLE `albums`
DROP `fuente`;

ALTER TABLE `menues`
DROP `site`,
DROP `pk_father`;

ALTER TABLE  `photos`
DROP  `date` ,
DROP  `resolution` ,
DROP  `type_img` ,
DROP  `media_type` ,
DROP  `color` ;
