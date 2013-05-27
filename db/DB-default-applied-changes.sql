-- This file contains all the changes applied in the default instance database.
-- Please refer to DB-default-new-changes.sql to see what changes has to be applied

-- Please check rigth sql, use ; in the end of lines & -- for comments.
-- Write date with each sentence and with stack method. (last writed in the top)

--- 09-04-2013
ALTER TABLE  `orders` ADD  `type` VARCHAR( 50 ) NOT NULL AFTER  `payment_method`

--- 06-05-2013
ALTER TABLE `newsletter_archive` CHANGE `created` `created` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `newsletter_archive` ADD `updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created`;
UPDATE `newsletter_archive` SET  `updated` = `created` WHERE `updated`='0000-00-00 00:00:00';
DROP TABLE `bulletins_archive`;

-- 27-09-2012
ALTER TABLE  `menues` ADD  `position` VARCHAR( 50 ) AFTER  `type`;
ALTER TABLE  `menues` DROP INDEX  `pk_menu`;
ALTER TABLE  `menues` DROP INDEX  `name_2`;
ALTER TABLE  `menues` ADD INDEX  `position` (  `position` ( 50 ) );

-- other changes
ALTER TABLE `polls` DROP `favorite`;
ALTER TABLE `kioskos` DROP `favorite`;

-- Changes for alex branch
ALTER TABLE `kioskos` ADD `type` TINYINT NOT NULL DEFAULT '0' COMMENT '0-item, 1-subscription';
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `content_id` bigint(20) NOT NULL,
  `created` datetime NOT NULL,
  `payment_id` varchar(50) NOT NULL,
  `payment_status` varchar(150) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(200) NOT NULL,
  `params` longtext NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
ALTER TABLE `kioskos` ADD `price` DECIMAL NOT NULL DEFAULT '0';
ALTER TABLE `users` ADD `type` TINYINT NOT NULL DEFAULT '0' COMMENT '0-backend, 1-frontend' AFTER `lastname`;
ALTER TABLE `users` ADD `token` VARCHAR( 50 ) NULL DEFAULT NULL AFTER `type`;
ALTER TABLE `users` ADD `deposit` DECIMAL NOT NULL DEFAULT '0' AFTER  `type`;

-- 22-08-2012
CREATE TABLE IF NOT EXISTS `usermeta` (
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`user_id`, `meta_key`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`)
);
DROP TABLE  `articles_clone`;

-- 16-08-2012
ALTER TABLE  `users` DROP  `address` , DROP  `phone`, DROP `online` ;

-- 14-08-2012
ALTER TABLE `newsletter_archive` ADD `title` VARCHAR( 255 ) NOT NULL AFTER `pk_newsletter`;
ALTER TABLE `newsletter_archive` ADD `sent` VARCHAR( 255 ) NOT NULL;

-- 03-08-2012
ALTER TABLE  `author_imgs` DROP PRIMARY KEY , ADD PRIMARY KEY (  `pk_img` ,  `fk_author` );

-- 13-07-2012
ALTER TABLE  `menu_items` DROP PRIMARY KEY , ADD PRIMARY KEY (  `pk_item` ,  `pk_menu` );

--11-06-2012
ALTER TABLE `authors` ADD `params` TEXT NULL DEFAULT NULL;
--09-05-2012
ALTER TABLE `newsletter_archive` ADD `html` LONGTEXT NULL DEFAULT NULL;

--20-03-2012
ALTER TABLE `books` ADD `file_img` VARCHAR( 255 ) NULL ;

--27-02-2012
INSERT INTO `privileges` (`pk_privilege`, `name`, `description`, `module`) VALUES
(173, 'LETTER_ADMIN', 'Admon. cartas', 'LETTER'),
(174, 'POLL_FAVORITE', 'Añadir a widgets', 'POLL'),
(175, 'POLL_HOME', 'Añadir al widget de portada', 'POLL');

--09-02-2012
ALTER TABLE  `albums` CHANGE  `cover`  `cover_id` BIGINT( 255 ) NULL DEFAULT NULL;

--03-02-2012
UPDATE contents SET starttime = created WHERE starttime = '0000-00-00 00:00:00';

--31-Dic-2012
INSERT INTO `content_types` ( `pk_content_type`, `name`, `title` ) VALUES
(17, 'letter', 'Letters to the Editor'), (18, 'frontpage', 'Portada');

INSERT INTO `privileges` (`pk_privilege`, `name`, `description`, `module`) VALUES

(166, 'LETTER_TRASH', 'Vaciar papelera de cartas', 'LETTER'),
(167, 'LETTER_DELETE', 'Eliminar cartas', 'LETTER'),
(168, 'LETTER_UPDATE', 'Modificar cartas', 'LETTER'),
(169, 'LETTER_SETTINGS', 'Configurar modulo de cartas', 'LETTER'),
(170, 'LETTER_AVAILABLE', 'Aprobar cartas', 'LETTER'),
(171, 'LETTER_FAVORITE', 'Gestionar Widget de cartas', 'LETTER'),
(172, 'LETTER_CREATE', 'Subir cartas', 'LETTER');


--29-Dic-2012
CREATE TABLE IF NOT EXISTS `letters` (
  `pk_letter` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(255)  DEFAULT NULL,
  `email` varchar(255)  DEFAULT NULL,
  `body` text ,
  PRIMARY KEY (`pk_letter`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `frontpages` (
   `pk_frontpage` bigint(20) NOT NULL COMMENT '',
   `date` int(11) NOT NULL COMMENT 'date as 20110720',
   `category` int(11) NOT NULL COMMENT 'category',
   `version` bigint(20) DEFAULT NULL,
   `content_positions` longtext NOT NULL COMMENT 'serialized id of contents',
   `promoted` tinyint(1) DEFAULT NULL,
   `day_frontpage` tinyint(1) DEFAULT NULL,
   `params` longtext NOT NULL COMMENT 'serialized params',
   PRIMARY KEY (`date`,`category`)

) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `privileges` (`pk_privilege`, `name`, `description`, `module`) VALUES
(165, 'IMPORT_EFE_FILE', 'Importar ficheros EFE', 'IMPORT');


-- 15-Dic-2011
ALTER TABLE `contents` ADD `urn_source` VARCHAR( 255 ) NULL DEFAULT NULL;

--2-Dic- 2011

-- Default category for epaper
INSERT INTO `content_categories` (
`pk_content_category` ,
`title` ,
`name` ,
`inmenu` ,
`posmenu` ,
`internal_category` ,
`fk_content_category` ,
`params` ,
`logo_path` ,
`color`
)
VALUES (
NULL , 'Portadas', 'portadas', '1', '10', '14', '0', NULL , NULL , '#638F38'
);


INSERT INTO `privileges` (`pk_privilege`, `name`, `description`, `module`) VALUES
(158, 'ALBUM_FAVORITE', 'Gestionar álbumes favoritos', 'ALBUM'),
(157, 'ALBUM_HOME', 'Publicar album para home', 'ALBUM'),
(156, 'VIDEO_FAVORITE', 'Gestionar Videos favoritos', 'VIDEO'),
(155, 'VIDEO_HOME', 'Publicar video en home', 'VIDEO');



-- 15-Nov-2011
-- Params for categories
ALTER TABLE `content_categories` ADD `params` LONGTEXT NULL ;

-- New modules books, specials & agenda



INSERT INTO `privileges` (`pk_privilege`, `name`, `description`, `module`) VALUES
(154, 'SCHEDULE_ADMIN', 'Gestionar la agenda ', 'SCHEDULE'),
(153, 'SCHEDULE_SETTINGS', 'Gestionar la agenda ', 'SCHEDULE'),
(152, 'SPECIAL_TRASH', 'Gestionar papelera especiales', 'SPECIAL'),
(151, 'SPECIAL_DELETE', 'Eliminar especiales', 'SPECIAL'),
(150, 'SPECIAL_UPDATE', 'Modificar especiales', 'SPECIAL'),
(149, 'SPECIAL_SETTINGS', 'Configurar modulo de especiales', 'SPECIAL'),
(148, 'SPECIAL_AVAILABLE', 'Aprobar especiales', 'SPECIAL'),
(147, 'SPECIAL_FAVORITE', 'Gestionar widget especiales', 'SPECIAL'),
(146, 'SPECIAL_CREATE', 'Crear especiales', 'SPECIAL'),
(145, 'SPECIAL_ADMIN', 'Administrar modulo de especiales ', 'SPECIAL'),
(144, 'BOOK_TRASH', 'Vaciar papelera de libros', 'BOOK'),
(143, 'BOOK_DELETE', 'Eliminar libros', 'BOOK'),
(142, 'BOOK_UPDATE', 'Modificar libros', 'BOOK'),
(141, 'BOOK_SETTINGS', 'Configurar modulo de libros', 'BOOK'),
(140, 'BOOK_AVAILABLE', 'Aprobar libros', 'BOOK'),
(139, 'BOOK_FAVORITE', 'Gestionar Widget de libros', 'BOOK'),
(138, 'BOOK_CREATE', 'Subir libros', 'BOOK'),
(137, 'BOOK_ADMIN', 'Administrar modulo de libros', 'BOOK');


-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE IF NOT EXISTS `books` (
  `pk_book` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(250) DEFAULT NULL,
  `file` varchar(250) DEFAULT NULL,
  `editorial` varchar(250) DEFAULT NULL,
    PRIMARY KEY (`pk_book`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `books`
--


-- --------------------------------------------------------

--
-- Table structure for table `specials`
--

CREATE TABLE IF NOT EXISTS `specials` (
  `pk_special` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subtitle` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `pdf_path` varchar(250) CHARACTER SET utf8 DEFAULT '0',
  `img1` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_special`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `specials`
--

-- --------------------------------------------------------

--
-- Table structure for table `special_contents`
--

CREATE TABLE IF NOT EXISTS `special_contents` (
  `fk_content` varchar(250) CHARACTER SET utf8 NOT NULL,
  `fk_special` int(10) NOT NULL,
  `name` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `position` int(10) DEFAULT '10',
  `visible` smallint(1) DEFAULT '1',
  `type_content` varchar(100) CHARACTER SET utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `special_contents`
--



----------------------------------------------------------------------------------
----------------------------------------------------------------------------------

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

-- 10-10-2012
CREATE TABLE IF NOT EXISTS `contentmeta` (
  `fk_content` bigint(20) NOT NULL,
  `meta_name` varchar(255) NOT NULL,
  `meta_value` text,
  PRIMARY KEY (`fk_content`,`meta_name`),
  KEY `fk_content` (`fk_content`)
) ENGINE = MYISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- 01-08-2012
DROP TABLE privileges;

-- 01-09-2012
UPDATE users SET name = CONCAT(name, ' ', firstname, ' ', lastname);
ALTER TABLE  `users` DROP  `firstname` , DROP  `lastname` ;