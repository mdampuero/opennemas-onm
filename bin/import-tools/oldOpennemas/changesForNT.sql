
DROP TABLE IF EXISTS `content_types`;

CREATE TABLE IF NOT EXISTS `content_types` (
  `pk_content_type` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `title` varchar(100) NOT NULL COMMENT 'utilizado en permalink',
  `fk_template_default` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`pk_content_type`),
  KEY `fk_template_default` (`fk_template_default`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `content_types`
--

INSERT INTO `content_types` (`pk_content_type`, `name`, `title`, `fk_template_default`) VALUES
(1, 'article', 'Artículo', NULL),
(2, 'advertisement', 'Publicidad', NULL),
(3, 'attachment', 'Fichero', NULL),
(4, 'opinion', 'Opinión', NULL),
(5, 'event', 'Evento', NULL),
(6, 'comment', 'Comentario', NULL),
(7, 'album', 'Álbum', NULL),
(8, 'photo', 'Imagen', NULL),
(9, 'video', 'Vídeo', NULL),
(10, 'special', 'Especiales', NULL),
(11, 'poll', 'Encuesta', NULL),
(12, 'widget', 'Widget', NULL),
(13, 'static_page', 'Página estática', NULL),
(14, 'kiosko', 'Kiosko', NULL),
(15, 'book', 'Libro', NULL),
(16, 'schedule', 'Agenda', NULL),
(17, 'letter', 'Letters to the Editor', NULL),
(18, 'frontpage', 'Portadas', NULL);



-- Table structure for table `albums`
--
DROP TABLE IF EXISTS `albums`;

CREATE TABLE IF NOT EXISTS `albums` (
  `pk_album` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subtitle` varchar(250) DEFAULT NULL,
  `agency` varchar(250) DEFAULT NULL,
  `fuente` varchar(250) DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_album`),
  UNIQUE KEY `pk_album` (`pk_album`),
  KEY `pk_album_2` (`pk_album`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `albums_photos`;

CREATE TABLE IF NOT EXISTS `albums_photos` (
  `pk_album` bigint(20) unsigned NOT NULL,
  `pk_photo` bigint(20) unsigned NOT NULL,
  `position` int(10) DEFAULT '1',
  `description` varchar(250) DEFAULT NULL,
  KEY `pk_album_2` (`pk_album`,`pk_photo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `videos`;

CREATE TABLE IF NOT EXISTS `videos` (
  `pk_video` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `video_url` varchar(255) NOT NULL,
  `information` text,
  `author_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`pk_video`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
DROP TABLE IF EXISTS `books`;

CREATE TABLE IF NOT EXISTS `books` (
  `pk_book` bigint(20) unsigned NOT NULL,
  `author` varchar(250) DEFAULT NULL,
  `file` varchar(250) DEFAULT NULL,
  `editorial` varchar(250) DEFAULT NULL,
 PRIMARY KEY (`pk_book`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `kioskos`;

CREATE TABLE IF NOT EXISTS `kioskos` (
  `pk_kiosko` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `path` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `favorite` tinyint(1) NOT NULL,
  PRIMARY KEY (`pk_kiosko`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `newsletter_archive` (
  `pk_newsletter` int(11) NOT NULL AUTO_INCREMENT,
  `data` longtext,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pk_newsletter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `specials`
--
DROP TABLE IF EXISTS `specials`;
CREATE TABLE IF NOT EXISTS `specials` (
  `pk_special` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subtitle` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `pdf_path` varchar(250) CHARACTER SET utf8 DEFAULT '0',
  `img1` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_special`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `specials`
--

-- --------------------------------------------------------

--
-- Table structure for table `special_contents`
--
DROP TABLE IF EXISTS `special_contents`;
CREATE TABLE IF NOT EXISTS `special_contents` (
  `fk_content` varchar(250) CHARACTER SET utf8 NOT NULL,
  `fk_special` int(10) NOT NULL,
  `name` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `position` int(10) DEFAULT '10',
  `visible` smallint(1) DEFAULT '1',
  `type_content` varchar(100) CHARACTER SET utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `letters`;

CREATE TABLE IF NOT EXISTS `letters` (
  `pk_letter` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `body` text,
  PRIMARY KEY (`pk_letter`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


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




-- Some alter table fixes

ALTER TABLE `comments` CHANGE `pk_comment` `pk_comment` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `polls` CHANGE `pk_poll` `pk_poll` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `poll_items` CHANGE `votes` `votes` INT( 10 ) UNSIGNED NULL DEFAULT '0';
ALTER TABLE `ratings` CHANGE `pk_rating` `pk_rating` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `static_pages` CHANGE `pk_static_page` `pk_static_page` BIGINT( 20 ) NOT NULL AUTO_INCREMENT COMMENT 'BIGINT(20)';
ALTER TABLE `votes` CHANGE `pk_vote` `pk_vote` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `widgets` CHANGE `pk_widget` `pk_widget` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `articles_clone` ADD INDEX ( `pk_clone` ) ;

--

UPDATE contents SET starttime = created WHERE starttime = '0000-00-00 00:00:00';

ALTER TABLE `contents` ADD `urn_source` VARCHAR( 255 ) NULL DEFAULT NULL;
ALTER TABLE `contents` ADD `favorite` TINYINT( 1 ) NULL ;
ALTER TABLE `contents` CHANGE `permalink` `slug` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE `contents` ADD `params` LONGTEXT NULL ;
ALTER TABLE `contents` ADD `category_name` VARCHAR( 255 ) NOT NULL COMMENT 'name category';
ALTER TABLE `contents` DROP `archive` ;
ALTER TABLE `contents` DROP `paper_page` ;
ALTER TABLE `contents` ADD `with_comment` SMALLINT( 1 ) NULL DEFAULT '1';
ALTER TABLE `authors` CHANGE `gender` `blog` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;

ALTER TABLE `content_categories` ADD `params` LONGTEXT NULL ;


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


