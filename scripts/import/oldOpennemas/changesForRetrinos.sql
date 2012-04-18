-- Changes in video table

ALTER TABLE `videos` CHANGE `videoid` `video_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;
ALTER TABLE `videos` CHANGE `htmlcode` `information` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

-- New Feature for enable/disable users

ALTER TABLE `users` ADD `authorize` TINYINT( 1 ) NOT NULL DEFAULT '1' COMMENT '1 authorized - 0 unauthorized' AFTER `phone`;

-- Create table for settings
-- Drop content into it

DROP  TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `name` varchar(128) NOT NULL DEFAULT '',
    `value` longtext NOT NULL,
    PRIMARY KEY (`name`)
);

INSERT INTO `settings` VALUES ( 'opinion_algoritm', 's:8:"position";');

-- Table structure for table `menues`
-- Drop content into it

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

-- Dumping data for table `menues`

INSERT INTO `menues` (`pk_menu`, `name`, `type`, `site`, `params`, `pk_father`) VALUES
(1, 'frontpage', '', ' ', NULL, NULL),
(2, 'opinion', '', ' ', NULL, NULL),
(3, 'mobile', '', '', NULL, NULL),
(4, 'album', '', ' ', NULL, NULL),
(5, 'video', '', ' ', NULL, NULL),
(6, 'poll', '', '', NULL, NULL);

-- Table structure for table menu_items
-- Drop content into it

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

-- Some alter table fixes
ALTER TABLE `albums` DROP `favorite` ;
ALTER TABLE `albums` AUTO_INCREMENT =1;
ALTER TABLE `albums` ADD INDEX ( `pk_album` ) ;
ALTER TABLE `albums` CHANGE `pk_album` `pk_album` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `articles_clone` ADD INDEX ( `pk_clone` ) ;
ALTER TABLE `contents` ADD `favorite` TINYINT( 1 ) NULL ;
ALTER TABLE `contents` CHANGE `permalink` `slug` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;
ALTER TABLE `contents` ADD `params` LONGTEXT NULL ;
ALTER TABLE `contents` ADD `category_name` VARCHAR( 255 ) NOT NULL COMMENT 'name category';
ALTER TABLE `contents` DROP `archive` ;
ALTER TABLE `contents` DROP `paper_page` ;
ALTER TABLE `comments` CHANGE `pk_comment` `pk_comment` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `kioskos` CHANGE `pk_kiosko` `pk_kiosko` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `polls` ADD `with_comment` SMALLINT( 1 ) NULL DEFAULT '1';
ALTER TABLE `polls` CHANGE `pk_poll` `pk_poll` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `poll_items` CHANGE `votes` `votes` INT( 10 ) UNSIGNED NULL DEFAULT '0';
ALTER TABLE `ratings` CHANGE `pk_rating` `pk_rating` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `static_pages` CHANGE `pk_static_page` `pk_static_page` BIGINT( 20 ) NOT NULL AUTO_INCREMENT COMMENT 'BIGINT(20)';
ALTER TABLE `videos` DROP COLUMN `favorite`;
ALTER TABLE `votes` CHANGE `pk_vote` `pk_vote` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;
ALTER TABLE `widgets` CHANGE `pk_widget` `pk_widget` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ;

-- Drop and recreate table users
-- Table structure for table users
-- Dumping data for table users

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `pk_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `online` tinyint(1) NOT NULL DEFAULT '0',
  `login` varchar(100) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `sessionexpire` tinyint(2) unsigned NOT NULL DEFAULT '15',
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `authorize` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 authorized - 0 unauthorized',
  `fk_user_group` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`pk_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=133 ;

INSERT INTO `users` (`pk_user`, `online`, `login`, `password`, `sessionexpire`, `email`, `name`, `firstname`, `lastname`, `address`, `phone`, `authorize`, `fk_user_group`) VALUES
(3, 0, 'macada', '2f575705daf41049194613e47027200b', 30, 'david.martinez@openhost.es', 'David', 'Martinez', 'Carballo', ' ', ' ', 1, 4),
(5, 0, 'fran', '6d87cd9493f11b830bbfdf628c2c4f08', 65, 'fran@openhost.es', 'Francisco ', 'DiÃ©guez', 'Souto', ' ', ' ', 1, 4),
(4, 0, 'alex', '4c246829b53bc5712d52ee777c52ebe7', 60, 'alex@openhost.es', 'Alexandre', 'Rico', '', '', '', 1, 4),
(7, 0, 'sandra', 'bd80e7c35b56dccd2d1796cf39cd05f6', 99, 'sandra@openhost.es', 'Sandra', 'Pereira', '', '', '', 1, 4),
(132, 0, 'admin', '75bba3adeaec86b143375d90a6d61bfd', 45, 'admin@opennemas.com', 'administrator', 'administrator', NULL, NULL, NULL, 1, 5);

-- Drop and recreate table user_groups
-- Table structure for table `user_groups`
-- Dumping data for table `user_groups`

DROP  TABLE IF EXISTS `user_groups`;
CREATE TABLE IF NOT EXISTS `user_groups` (
  `pk_user_group` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`pk_user_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

INSERT INTO `user_groups` (`pk_user_group`, `name`) VALUES
(4, 'Masters'),
(5, 'Administrador'),
(6, 'Usuarios');

-- Drop and recreate table users_content_categories
-- Table structure for table users_content_categories

DROP  TABLE IF EXISTS `users_content_categories`;
CREATE TABLE IF NOT EXISTS `users_content_categories` (
  `pk_fk_user` int(10) unsigned NOT NULL,
  `pk_fk_content_category` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pk_fk_user`,`pk_fk_content_category`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Drop and recreate table user_groups_privileges
-- Table structure for table user_groups_privileges
-- Dumping data for table user_groups_privileges

DROP  TABLE IF EXISTS `user_groups_privileges`;
CREATE TABLE IF NOT EXISTS `user_groups_privileges` (
  `pk_fk_user_group` int(10) unsigned NOT NULL,
  `pk_fk_privilege` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pk_fk_user_group`,`pk_fk_privilege`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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

-- Drop and recreate table user_groups_privileges
-- Table structure for table user_groups_privileges
-- Dumping data for table user_groups_privileges

DROP  TABLE IF EXISTS `privileges`;
CREATE TABLE IF NOT EXISTS `privileges` (
  `pk_privilege` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `module` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`pk_privilege`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=174 ;

INSERT INTO `privileges` (`pk_privilege`, `name`, `description`, `module`) VALUES
(1, 'CATEGORY_ADMIN', 'Listado de secciones', 'CATEGORY'),
(2, 'CATEGORY_AVAILABLE', 'Aprobar SecciÃ³n', 'CATEGORY'),
(3, 'CATEGORY_UPDATE', 'Modificar SecciÃ³n', 'CATEGORY'),
(4, 'CATEGORY_DELETE', 'Eliminar SecciÃ³n', 'CATEGORY'),
(5, 'CATEGORY_CREATE', 'Crear SecciÃ³n', 'CATEGORY'),
(6, 'ARTICLE_ADMIN', 'Listados de ArtÃ­culos', 'ARTICLE'),
(7, 'ARTICLE_FRONTPAGE', 'AdministraciÃ³n de portadas', 'ARTICLE'),
(8, 'ARTICLE_PENDINGS', 'Listar noticias pendientes', 'ARTICLE'),
(9, 'ARTICLE_AVAILABLE', 'Aprobar Noticia', 'ARTICLE'),
(10, 'ARTICLE_UPDATE', 'Modificar ArtÃ­culo', 'ARTICLE'),
(11, 'ARTICLE_DELETE', 'Eliminar ArtÃ­culo', 'ARTICLE'),
(12, 'ARTICLE_CREATE', 'Crear ArtÃ­culo', 'ARTICLE'),
(13, 'ARTICLE_ARCHIVE', 'Recuperar/Archivar ArtÃ­culos de/a hemeroteca', 'ARTICLE'),
(14, 'ARTICLE_CLONE', 'Clonar ArtÃ­culo', 'ARTICLE'),
(15, 'ARTICLE_HOME', 'GestiÃ³n portada Home de artÃ­culos', 'ARTICLE'),
(16, 'ARTICLE_TRASH', 'gestiÃ³n papelera ArtÃ­culo', 'ARTICLE'),
(17, 'ARTICLE_ARCHIVE_ADMI', 'Listado de hemeroteca', 'ARTICLE'),
(18, 'ADVERTISEMENT_ADMIN', 'Listado de  publicidad', 'ADVERTISEMENT'),
(19, 'ADVERTISEMENT_AVAILA', 'Aprobar publicidad', 'ADVERTISEMENT'),
(20, 'ADVERTISEMENT_UPDATE', 'Modificar publicidad', 'ADVERTISEMENT'),
(21, 'ADVERTISEMENT_DELETE', 'Eliminar publicidad', 'ADVERTISEMENT'),
(22, 'ADVERTISEMENT_CREATE', 'Crear publicidad', 'ADVERTISEMENT'),
(23, 'ADVERTISEMENT_TRASH', 'gestiÃ³n papelera publicidad', 'ADVERTISEMENT'),
(24, 'ADVERTISEMENT_HOME', 'gestiÃ³n de publicidad en Home', 'ADVERTISEMENT'),
(26, 'OPINION_ADMIN', 'Listado de  opiniÃ³n', 'OPINION'),
(27, 'OPINION_FRONTPAGE', 'Portada Opinion', 'OPINION'),
(28, 'OPINION_AVAILABLE', 'Aprobar OpiniÃ³n', 'OPINION'),
(29, 'OPINION_UPDATE', 'Modificar OpiniÃ³n', 'OPINION'),
(30, 'OPINION_HOME', 'Publicar widgets home OpiniÃ³n', 'OPINION'),
(31, 'OPINION_DELETE', 'Eliminar OpiniÃ³n', 'OPINION'),
(32, 'OPINION_CREATE', 'Crear OpiniÃ³n', 'OPINION'),
(33, 'OPINION_TRASH', 'gestion papelera OpiniÃ³n', 'OPINION'),
(34, 'COMMENT_ADMIN', 'Listado de comentarios', 'COMMENT'),
(35, 'COMMENT_POLL', 'Gestionar Comentarios de encuestas', 'COMMENT'),
(36, 'COMMENT_HOME', 'Gestionar Comentarios de Home', 'COMMENT'),
(37, 'COMMENT_AVAILABLE', 'Aprobar/Rechazar Comentario', 'COMMENT'),
(38, 'COMMENT_UPDATE', 'Modificar Comentario', 'COMMENT'),
(39, 'COMMENT_DELETE', 'Eliminar Comentario', 'COMMENT'),
(40, 'COMMENT_CREATE', 'Crear Comentario', 'COMMENT'),
(41, 'COMMENT_TRASH', 'gestiÃ³n papelera Comentarios', 'COMMENT'),
(42, 'ALBUM_ADMIN', 'Listado de Ã¡lbumes', 'ALBUM'),
(43, 'ALBUM_AVAILABLE', 'Aprobar Album', 'ALBUM'),
(44, 'ALBUM_UPDATE', 'Modificar Album', 'ALBUM'),
(45, 'ALBUM_DELETE', 'Eliminar Album', 'ALBUM'),
(46, 'ALBUM_CREATE', 'Crear Album', 'ALBUM'),
(47, 'ALBUM_TRASH', 'gestion papelera Album', 'ALBUM'),
(48, 'VIDEO_ADMIN', 'Listado de videos', 'VIDEO'),
(49, 'VIDEO_AVAILABLE', 'Aprobar video', 'VIDEO'),
(50, 'VIDEO_UPDATE', 'Modificar video', 'VIDEO'),
(51, 'VIDEO_DELETE', 'Eliminar video', 'VIDEO'),
(52, 'VIDEO_CREATE', 'Crear video', 'VIDEO'),
(53, 'VIDEO_TRASH', 'gestiÃ³n papelera video', 'VIDEO'),
(60, 'IMAGE_ADMIN', 'Listado de imÃ¡genes', 'IMAGE'),
(61, 'IMAGE_AVAILABLE', 'Aprobar Imagen', 'IMAGE'),
(62, 'IMAGE_UPDATE', 'Modificar Imagen', 'IMAGE'),
(63, 'IMAGE_DELETE', 'Eliminar Imagen', 'IMAGE'),
(64, 'IMAGE_CREATE', 'Subir Imagen', 'IMAGE'),
(65, 'IMAGE_TRASH', 'gestiÃ³n papelera Imagen', 'IMAGE'),
(66, 'STATIC_ADMIN', 'Listado pÃ¡ginas estÃ¡ticas', 'STATIC'),
(67, 'STATIC_AVAILABLE', 'Aprobar PÃ¡gina EstÃ¡tica', 'STATIC'),
(68, 'STATIC_UPDATE', 'Modificar PÃ¡gina EstÃ¡tica', 'STATIC'),
(69, 'STATIC_DELETE', 'Eliminar PÃ¡gina EstÃ¡tica', 'STATIC'),
(70, 'STATIC_CREATE', 'Crear PÃ¡gina EstÃ¡tica', 'STATIC'),
(71, 'KIOSKO_ADMIN', 'Listar PÃ¡gina Papel', 'KIOSKO'),
(72, 'KIOSKO_AVAILABLE', 'Aprobar PÃ¡gina Papel', 'KIOSKO'),
(73, 'KIOSKO_UPDATE', 'Modificar PÃ¡gina Papel', 'KIOSKO'),
(74, 'KIOSKO_DELETE', 'Eliminar PÃ¡gina Papel', 'KIOSKO'),
(75, 'KIOSKO_CREATE', 'Crear PÃ¡gina Papel', 'KIOSKO'),
(76, 'KIOSKO_HOME', 'Incluir en portada como favorito', 'KIOSKO'),
(77, 'POLL_ADMIN', 'Listado encuestas', 'POLL'),
(78, 'POLL_AVAILABLE', 'Aprobar Encuesta', 'POLL'),
(79, 'POLL_UPDATE', 'Modificar Encuesta', 'POLL'),
(80, 'POLL_DELETE', 'Eliminar Encuesta', 'POLL'),
(81, 'POLL_CREATE', 'Crear Encuesta', 'POLL'),
(82, 'AUTHOR_ADMIN', 'Listado autores OpiniÃ³n', 'AUTHOR'),
(83, 'AUTHOR_UPDATE', 'Modificar Autor', 'AUTHOR'),
(84, 'AUTHOR_DELETE', 'Eliminar Autor', 'AUTHOR'),
(85, 'AUTHOR_CREATE', 'Crear Autor', 'AUTHOR'),
(86, 'USER_ADMIN', 'Listado de usuarios', 'USER'),
(87, 'USER_UPDATE', 'Modificar Usuario', 'USER'),
(88, 'USER_DELETE', 'Eliminar Usuario', 'USER'),
(89, 'USER_CREATE', 'Crear Usuario', 'USER'),
(90, 'PCLAVE_ADMIN', 'Listado de palabras clave', 'PCLAVE'),
(91, 'PCLAVE_UPDATE', 'Modificar Palabra Clave', 'PCLAVE'),
(92, 'PCLAVE_DELETE', 'Eliminar Palabra Clave', 'PCLAVE'),
(93, 'PCLAVE_CREATE', 'Crear Palabra Clave', 'PCLAVE'),
(95, 'GROUP_ADMIN', 'Grupo usuarios Admin', 'GROUP'),
(96, 'GROUP_UPDATE', 'Modificar Grupo Usuarios', 'GROUP'),
(97, 'GROUP_DELETE', 'Eliminar Grupo Usuarios', 'GROUP'),
(98, 'GROUP_ADMIN', 'Listado de Grupo Usuarios', 'GROUP'),
(99, 'GROUP_CREATE', 'Crear Grupo Usuarios', 'GROUP'),
(100, 'PRIVILEGE_UPDATE', 'Modificar Privilegio', 'PRIVILEGE'),
(101, 'PRIVILEGE_DELETE', 'Eliminar Privilegio', 'PRIVILEGE'),
(102, 'PRIVILEGE_ADMIN', 'Listado de Privilegios', 'PRIVILEGE'),
(103, 'PRIVILEGE_CREATE', 'Crear Privilegio', 'PRIVILEGE'),
(104, 'FILE_ADMIN', 'Listado de ficheros y portadas', 'FILE'),
(105, 'FILE_FRONTS', 'GestiÃ³n de portadas', 'FILE'),
(106, 'FILE_UPDATE', 'Modificar Fichero', 'FILE'),
(107, 'FILE_DELETE', 'Eliminar Fichero', 'FILE'),
(108, 'FILE_CREATE', 'Crear Fichero', 'FILE'),
(109, 'MENU_CREATE', 'Crear menu', 'MENU'),
(110, 'BADLINK_ADMIN', 'Control Link Admin', 'BADLINK'),
(111, 'STATS_ADMIN', 'Admin EstadÃ­sticas', 'STATS'),
(112, 'NEWSLETTER_ADMIN', 'AdministraciÃ³n del boletÃ­n', 'NEWSLETTER'),
(113, 'BACKEND_ADMIN', 'ConfiguraciÃ³n de backend', 'BACKEND'),
(114, 'CACHE_TPL_ADMIN', 'GestiÃ³n de CachÃ©s Portadas', 'CACHE'),
(115, 'SEARCH_ADMIN', 'Utilidades: bÃºsqueda avanzada', 'SEARCH'),
(116, 'TRASH_ADMIN', 'GestiÃ³n papelera', 'TRASH'),
(117, 'WIDGET_ADMIN', 'Listado de widgets', 'WIDGET'),
(118, 'WIDGET_AVAILABLE', 'Aprobar Widget', 'WIDGET'),
(119, 'WIDGET_UPDATE', 'Modificar Widget', 'WIDGET'),
(120, 'WIDGET_DELETE', 'Eliminar Widget', 'WIDGET'),
(121, 'WIDGET_CREATE', 'Crear Widget', 'WIDGET'),
(122, 'MENU_ADMIN', 'Listado de menus', 'MENU'),
(123, 'MENU_AVAILABLE', 'Leer menu', 'MENU'),
(124, 'MENU_UPDATE', 'Modificar menu', 'MENU'),
(125, 'IMPORT_ADMIN', 'Importar', 'IMPORT'),
(126, 'IMPORT_EPRESS', 'Importar EuropaPress', 'IMPORT'),
(127, 'IMPORT_XML', 'Importar XML', 'IMPORT'),
(128, 'IMPORT_EFE', 'Importar EFE', 'IMPORT'),
(129, 'CACHE_APC_ADMIN', 'Gestion cache de APC', 'CACHE'),
(130, 'ONM_CONFIG', 'Configurar Onm', 'ONM'),
(131, 'ONM_MANAGER', 'Gestionar Onm', 'ONM'),
(132, 'CONTENT_OTHER_UPDATE', 'Poder modificar contenido de otros usuarios', 'CONTENT'),
(133, 'CONTENT_OTHER_DELETE', 'Poder eliminar contenido de otros usuarios', 'CONTENT'),
(134, 'ONM_SETTINGS', 'Allow to configure system wide settings', 'ONM'),
(135, 'GROUP_CHANGE', ' Cambiar de grupo al usuario ', 'GROUP'),
(165, 'IMPORT_EFE_FILE', 'Importar ficheros EFE', 'IMPORT'),
(164, 'MENU_DELETE', 'Eliminar menu', 'MENU'),
(163, 'VIDEO_SETTINGS', 'Configurar módulo de video', 'VIDEO'),
(162, 'CATEGORY_SETTINGS', 'Configurar módulo de categorias', 'CATEGORY'),
(161, 'OPINION_SETTINGS', 'Configurar módulo de opinion', 'OPINION'),
(160, 'POLL_SETTINGS', 'Configurar módulos de encuestas', 'POLL'),
(159, 'ALBUM_SETTINGS', 'Configurar módulo de álbumes', 'ALBUM'),
(158, 'ALBUM_FAVORITE', 'Gestionar álbumes favoritos', 'ALBUM'),
(157, 'ALBUM_HOME', 'Publicar album para home', 'ALBUM'),
(156, 'VIDEO_FAVORITE', 'Gestionar Videos favoritos', 'VIDEO'),
(155, 'VIDEO_HOME', 'Publicar video en home', 'VIDEO'),
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
(137, 'BOOK_ADMIN', 'Administrar modulo de libros', 'BOOK'),
(166, 'LETTER_TRASH', 'Vaciar papelera de cartas', 'LETTER'),
(167, 'LETTER_DELETE', 'Eliminar cartas', 'LETTER'),
(168, 'LETTER_UPDATE', 'Modificar cartas', 'LETTER'),
(169, 'LETTER_SETTINGS', 'Configurar modulo de cartas', 'LETTER'),
(170, 'LETTER_AVAILABLE', 'Aprobar cartas', 'LETTER'),
(171, 'LETTER_FAVORITE', 'Gestionar Widget de cartas', 'LETTER'),
(172, 'LETTER_CREATE', 'Subir cartas', 'LETTER'),
(173, 'LETTER_ADMIN', 'Admon. cartas', 'LETTER');

-- Add new content type Book

INSERT INTO `content_types` (`pk_content_type` , `name` , `title` , `fk_template_default`)
VALUES (15 , 'book', 'libro', NULL);

-- Create tables letters and frontpages

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

-- Table structure for table `specials`

CREATE TABLE IF NOT EXISTS `specials` (
  `pk_special` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subtitle` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `pdf_path` varchar(250) CHARACTER SET utf8 DEFAULT '0',
  `img1` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pk_special`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Table structure for table `special_contents`

CREATE TABLE IF NOT EXISTS `special_contents` (
  `fk_content` varchar(250) CHARACTER SET utf8 NOT NULL,
  `fk_special` int(10) NOT NULL,
  `name` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `position` int(10) DEFAULT '10',
  `visible` smallint(1) DEFAULT '1',
  `type_content` varchar(100) CHARACTER SET utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Table structure for table `books`

CREATE TABLE IF NOT EXISTS `books` (
  `pk_book` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author` varchar(250) DEFAULT NULL,
  `file` varchar(250) DEFAULT NULL,
  `editorial` varchar(250) DEFAULT NULL,
    PRIMARY KEY (`pk_book`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Table structure for table `content_positions`

CREATE TABLE IF NOT EXISTS `content_positions` (
  `pk_fk_content` bigint(20) NOT NULL,
  `fk_category` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `placeholder` varchar(45) NOT NULL DEFAULT '',
  `params` text,
  `content_type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`pk_fk_content`,`fk_category`,`position`,`placeholder`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Table structure for table `pc_users`

CREATE TABLE IF NOT EXISTS `pc_users` (
  `pk_pc_user` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `subscription` int(11) NOT NULL,
  PRIMARY KEY (`pk_pc_user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- Table structure for table `translation_ids`

CREATE TABLE IF NOT EXISTS `translation_ids` (
  `pk_content_old` bigint(10) NOT NULL,
  `pk_content` bigint(10) NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`pk_content_old`,`pk_content`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
