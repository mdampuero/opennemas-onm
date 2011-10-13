--13-October-2011
INSERT INTO `privileges` (
`pk_privilege` ,
`name` ,
`description` ,
`module`
)
VALUES (135, 'GROUP_CHANGE', 'Cambiar de grupo al usuario ', 'GROUP'),
  ('136', 'USER_CATEGORY', 'Asignar categorias al usuario ', 'USER');

-- 29-september-2011
-- CREATE MASTERS GROUP

UPDATE  `user_groups` SET `pk_user_group` = '8' WHERE `pk_user_group` ='4';
INSERT INTO `user_groups` (
`pk_user_group` ,
`name`
)
VALUES (
4 , 'Masters'
);
UPDATE `users` SET `fk_user_group` = '4' WHERE `users`.`fk_user_group` ='4';
UPDATE `users` SET `fk_user_group` = '4' WHERE `users`.`login` ='macada';
UPDATE `users` SET `fk_user_group` = '4' WHERE `users`.`login` ='alex';
UPDATE `users` SET `fk_user_group` = '4' WHERE `users`.`login` ='fran';
UPDATE `users` SET `fk_user_group` = '4' WHERE `users`.`login` ='sandra';

--27-September - 2011
ALTER TABLE `albums` AUTO_INCREMENT =1;
ALTER TABLE `albums` ADD INDEX ( `pk_album` ) ;
ALTER TABLE `albums` CHANGE `pk_album` `pk_album` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;

ALTER TABLE `articles_clone` ADD INDEX ( `pk_clone` ) ;
ALTER TABLE `comments` CHANGE `pk_comment` `pk_comment` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `contents` CHANGE `permalink` `slug` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE `contents` ADD `params` LONGTEXT NULL ;
ALTER TABLE `contents` ADD `category_name` VARCHAR( 255 ) NOT NULL COMMENT 'name category';
ALTER TABLE `contents` ADD `favorite` TINYINT( 1 ) NULL ;
ALTER TABLE `contents` DROP `archive` ;
ALTER TABLE `contents` DROP `paper_page` ;


INSERT INTO `content_types` (`pk_content_type` , `name` , `title` , `fk_template_default`)
VALUES (15 , 'book', 'libro', NULL);

ALTER TABLE `kioskos` CHANGE `pk_kiosko` `pk_kiosko` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `polls` CHANGE `pk_poll` `pk_poll` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `ratings` CHANGE `pk_rating` `pk_rating` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `static_pages` CHANGE `pk_static_page` `pk_static_page` BIGINT( 20 ) NOT NULL AUTO_INCREMENT COMMENT 'BIGINT(20)';

INSERT INTO  `user_groups` (`pk_user_group` ,`name`) VALUES ('4', 'Masters');

ALTER TABLE `votes` CHANGE `pk_vote` `pk_vote` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `widgets` CHANGE `pk_widget` `pk_widget` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;

ALTER TABLE `albums` DROP `favorite` ;
 

----------------------------------------------------------------------------------
ALTER TABLE `contents` ADD `favorite` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `in_home`;
ALTER TABLE `videos` DROP COLUMN `favorite`;



-- 5-September-2011
ALTER TABLE `videos` ADD `favorite` TINYINT( 1 ) NOT NULL AFTER `information`;

-- 26-August-2011
ALTER TABLE `poll_items` CHANGE `votes` `votes` INT( 10 ) UNSIGNED NULL DEFAULT '0';
ALTER TABLE `polls` ADD `with_comment` SMALLINT( 1 ) NULL DEFAULT '1';
-- 22-August-2011

INSERT INTO `privileges` ( `name`, `description`, `module`) VALUES
( 'CATEGORY_SETTINGS', 'Cambiar config de secciones', 'CATEGORY'),
( 'ALBUM_SETTINGS', 'Cambiar config de album', 'ALBUM'),
( 'VIDEO_SETTINGS', 'Cambiar config de video', 'VIDEO'),
( 'OPINION_SETTINGS', 'Cambiar config de opinion', 'OPINION');


--27-July-2011

DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE IF NOT EXISTS `menu_items` (
  `pk_item` int(11) NOT NULL AUTO_INCREMENT,
  `pk_menu` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `link_name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL COMMENT '''category'',''external'',''static'', internal''',
  `position` int(11) NOT NULL,
  `pk_father` int(11) DEFAULT NULL,
  PRIMARY KEY (`pk_item`),
  KEY `pk_item` (`pk_item`),
  KEY `pk_menu` (`pk_menu`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `menues`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `menues`
--

INSERT INTO `menues` (`pk_menu`, `name`, `type`, `site`, `params`, `pk_father`) VALUES
(1, 'frontpage', '', ' ', NULL, NULL),
(2, 'opinion', '', ' ', NULL, NULL),
(3, 'mobile', '', '', NULL, NULL),
(4, 'album', '', ' ', NULL, NULL),
(5, 'video', '', ' ', NULL, NULL);



-- 20-July -2011
ALTER TABLE `menues` CHANGE `categories` `site` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

CREATE TABLE `nueva-tribuna`.`menu_items` (
`pk_item` INT NOT NULL ,
`pk_menu` INT NOT NULL ,
`title` VARCHAR( 255 ) NOT NULL ,
`link_name` VARCHAR( 255 ) NOT NULL ,
`type` VARCHAR( 255 ) NOT NULL COMMENT '''category'',''external'',''static'', internal''',
`position` INT NOT NULL ,
`pk_father` INT NULL
) ENGINE = MYISAM ;

-- --------------------------------------------------------
-- 19-July-2011
--Config changes for LOG
INSERT INTO `nuevatribuna`.`settings` (`name` ,`value`)
VALUES ('log_level', 's:6:"normal";');
INSERT INTO `nuevatribuna`.`settings` (`name` ,`value`)
VALUES ('log_enabled', 'b:1;');
INSERT INTO `nuevatribuna`.`settings` (`name` ,`value`)
VALUES ('log_db_enabled', 'b:1;');


-- --------------------------------------------------------
-- 15-July-2011
-- Add new ONM_SETTINGS privilege to the database and adding it to the Admnistrator grupo
INSERT INTO `privileges` ( `name` , `description` , `module` ) VALUES ( 'ONM_SETTINGS', 'Allow to configure system wide settings', 'ONM' );
INSERT INTO `user_groups_privileges` ( `pk_fk_user_group` , `pk_fk_privilege` ) VALUES ( '5', '32');

-- Create Menu table for frontpage menu manager


-- --------------------------------------------------------
-- 13-July-2011
--
-- Table structure for table `menues`
--

CREATE TABLE IF NOT EXISTS `menues` (
  `pk_menu` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `categories` text NOT NULL,
  `params` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_menu`),
  UNIQUE KEY `name` (`name`),
  KEY `name_2` (`name`),
  KEY `pk_menu` (`pk_menu`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `menues`
--

INSERT INTO `menues` (`pk_menu`, `name`, `type`, `categories`, `params`) VALUES
(1, 'frontpage', '', '', NULL),
(2, 'opinion', '', '', NULL),
(3, 'mobile', '', '', NULL),
(4, 'album', '', '', NULL),
(5, 'video', '', '', NULL),
(6, 'poll', '', '', NULL);


-- Create table for settings
DROP  TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
 `name` varchar(128) NOT NULL DEFAULT '',
 `value` longtext NOT NULL,
 PRIMARY KEY (`name`)
)
INSERT INTO `settings` VALUES ( 'opinion_algoritm', 's:8:"position";');

--Save video object in database
ALTER TABLE `videos` CHANGE `videoid` `video_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `videos` CHANGE `htmlcode` `information` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

--Documentation : date (dd-mm-yyyy) - changes in DB.

ALTER TABLE `videos` ADD `favorite` SMALLINT( 1 ) NULL DEFAULT '0';

--New Feature for enable/disable users

ALTER TABLE `users` ADD `authorize` TINYINT( 1 ) NOT NULL DEFAULT '1' COMMENT '1 authorized - 0 unauthorized' AFTER `phone`
