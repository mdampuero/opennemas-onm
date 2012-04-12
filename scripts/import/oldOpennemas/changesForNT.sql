
ALTER TABLE `books` ADD `file_img` VARCHAR( 255 ) NULL ;


ALTER TABLE  `albums` CHANGE  `cover`  `cover_id` BIGINT( 255 ) NULL DEFAULT NULL;

UPDATE contents SET starttime = created WHERE starttime = '0000-00-00 00:00:00';


INSERT INTO `content_types` ( `pk_content_type`, `name`, `title` ) VALUES
(17, 'letter', 'Letters to the Editor'), (18, 'frontpage', 'Portada');


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

 
ALTER TABLE `contents` ADD `urn_source` VARCHAR( 255 ) NULL DEFAULT NULL;

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



-- 15-Nov-2011

ALTER TABLE `content_categories` ADD `params` LONGTEXT NULL ;

DROP TABLE IF EXISTS `content_types`;

CREATE TABLE IF NOT EXISTS `content_types` (
  `pk_content_type` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `title` varchar(100) NOT NULL COMMENT 'utilizado en permalink',
  `fk_template_default` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`pk_content_type`),
  KEY `fk_template_default` (`fk_template_default`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

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
(16, 'schedule', 'Agenda',NULL)
;




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




INSERT INTO `menues` (`pk_menu`, `name`, `type`, `site`, `params`, `pk_father`) VALUES
(1, 'frontpage', '', 'nuevatribuna.local', 'a:1:{s:11:"description";s:0:"";}', 0),
(2, 'opinion', '', '', NULL, NULL),
(3, 'mobile', '', '', NULL, NULL),
(4, 'album', '', '', NULL, NULL),
(5, 'video', '', '', NULL, NULL),
(6, 'poll', '', '', NULL, NULL),
(7, 'submenu Portada', '', 'nuevatribuna.local', 'a:1:{s:11:"description";s:29:"sub Menu que se ve en portada";}', 1);


--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`pk_item`, `pk_menu`, `title`, `link_name`, `type`, `position`, `pk_father`) VALUES
(1, 1, 'Portada', 'home', 'internal', 1, 0),
(2, 1, 'EspaÃ±a', 'espana', 'category', 3, 0),
(3, 1, 'Mundo', 'mundo', 'category', 4, 0),
(4, 1, 'OpiniÃ³n', 'opinion', 'internal', 2, 0),
(5, 1, 'Sociedad', 'sociedad', 'category', 6, 0),
(6, 1, 'Medio Ambiente', 'medio-ambiente', 'category', 7, 0),
(7, 1, 'Cultura | Ocio', 'cultura---ocio', 'category', 8, 0),
(8, 1, 'Entrevistas', 'entrevistas', 'category', 9, 0),
(9, 1, 'EconomÃ­a', 'economia', 'category', 5, 0),
(10, 7, 'VersiÃ³n MÃ³vil', 'mobile', 'internal', 1, 1);


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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

DROP TABLE IF EXISTS `videos`;
CREATE TABLE IF NOT EXISTS `videos` (
  `pk_video` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `video_url` varchar(255) NOT NULL,
  `information` text,
  `author_name` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`pk_video`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;




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



ALTER TABLE `contents` ADD `favorite` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `in_home`;


ALTER TABLE `users` ADD `authorize` TINYINT( 1 ) NOT NULL DEFAULT '1' COMMENT '1 authorized - 0 unauthorized' AFTER `phone`


