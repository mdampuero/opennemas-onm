-- phpMyAdmin SQL Dump
-- version 3.1.2deb1ubuntu0.2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 26-10-2010 a las 10:38:29
-- Versión del servidor: 5.0.75
-- Versión de PHP: 5.2.6-3ubuntu4.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de datos: `retrincosdb`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adodb_logsql`
--

CREATE TABLE IF NOT EXISTS `adodb_logsql` (
  `created` datetime NOT NULL,
  `sql0` varchar(250) NOT NULL,
  `sql1` text NOT NULL,
  `params` text NOT NULL,
  `tracer` text NOT NULL,
  `timer` decimal(16,6) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `advertisements`
--

CREATE TABLE IF NOT EXISTS `advertisements` (
  `pk_advertisement` bigint(20) unsigned NOT NULL auto_increment,
  `type_advertisement` smallint(2) unsigned default '1',
  `fk_content_categories` int(10) unsigned default '11',
  `path` varchar(150) default NULL,
  `url` varchar(150) default NULL,
  `type_medida` varchar(50) default NULL,
  `num_clic` int(10) default '0',
  `num_clic_count` int(10) unsigned default '0',
  `num_view` int(10) unsigned default '0',
  `with_script` smallint(1) default '0',
  `script` text,
  `overlap` tinyint(1) NOT NULL default '0' COMMENT 'Flag esconder eventos flash',
  `timeout` int(4) NOT NULL default '-1',
  PRIMARY KEY  (`pk_advertisement`),
  KEY `type_advertisement` (`type_advertisement`),
  KEY `fk_content_categories` (`fk_content_categories`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2010101322420000280 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `albums`
--

CREATE TABLE IF NOT EXISTS `albums` (
  `pk_album` bigint(20) unsigned NOT NULL,
  `subtitle` varchar(250) default NULL,
  `agency` varchar(250) default NULL,
  `fuente` varchar(250) default NULL,
  `favorite` smallint(1) default '0',
  `cover` varchar(255) default NULL,
  PRIMARY KEY  (`pk_album`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `albums_photos`
--

CREATE TABLE IF NOT EXISTS `albums_photos` (
  `pk_album` bigint(20) unsigned NOT NULL,
  `pk_photo` bigint(20) unsigned NOT NULL,
  `position` int(10) default '1',
  `description` varchar(250) default NULL,
  KEY `pk_album` (`pk_album`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `pk_article` bigint(20) unsigned NOT NULL auto_increment,
  `summary` text,
  `body` text,
  `img1` bigint(20) unsigned default NULL,
  `subtitle` varchar(250) default NULL,
  `img1_footer` varchar(250) default NULL,
  `img2` bigint(20) unsigned default NULL,
  `img2_footer` varchar(250) default NULL,
  `agency` varchar(100) default NULL,
  `fk_video` bigint(20) unsigned default NULL,
  `with_comment` smallint(1) default '1',
  `fk_video2` bigint(20) unsigned default NULL COMMENT 'video interior',
  `footer_video2` varchar(150) default NULL,
  `columns` smallint(1) default '1',
  `home_columns` smallint(1) default '1',
  `footer_video1` varchar(150) default NULL,
  `title_int` varchar(255) default NULL,
  PRIMARY KEY  (`pk_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9223372036854775807 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articles_clone`
--

CREATE TABLE IF NOT EXISTS `articles_clone` (
  `pk_original` bigint(20) NOT NULL,
  `pk_clone` bigint(20) NOT NULL,
  `params` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Table to control articles clone';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `attachments`
--

CREATE TABLE IF NOT EXISTS `attachments` (
  `pk_attachment` bigint(20) unsigned NOT NULL auto_increment,
  `title` varchar(200) character set utf8 collate utf8_spanish_ci NOT NULL,
  `path` varchar(200) NOT NULL,
  `category` int(10) NOT NULL default '1',
  PRIMARY KEY  (`pk_attachment`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2010092921510600955 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `authors`
--

CREATE TABLE IF NOT EXISTS `authors` (
  `pk_author` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `gender` varchar(50) default NULL,
  `politics` varchar(50) default NULL,
  `date_nac` datetime default NULL,
  `fk_user` int(10) NOT NULL,
  `condition` varchar(50) default NULL,
  PRIMARY KEY  (`pk_author`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=239 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `author_imgs`
--

CREATE TABLE IF NOT EXISTS `author_imgs` (
  `pk_img` bigint(20) unsigned NOT NULL auto_increment,
  `fk_author` int(10) NOT NULL,
  `fk_photo` bigint(20) unsigned NOT NULL,
  `path_img` varchar(250) NOT NULL,
  `description` varchar(250) default NULL,
  PRIMARY KEY  (`pk_img`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=582 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bulletins_archive`
--

CREATE TABLE IF NOT EXISTS `bulletins_archive` (
  `pk_bulletin` int(11) NOT NULL auto_increment,
  `data` longtext character set latin1,
  `contact_list` longtext character set latin1,
  `attach_pdf` tinyint(4) default NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `cron_timestamp` datetime default NULL,
  PRIMARY KEY  (`pk_bulletin`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=317 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `pk_comment` bigint(20) unsigned NOT NULL,
  `author` varchar(150) default NULL,
  `ciudad` varchar(150) default NULL,
  `sexo` varchar(50) default NULL,
  `email` varchar(150) default NULL,
  `body` text,
  `ip` varchar(20) default NULL,
  `published` datetime default NULL,
  `fk_content` bigint(20) unsigned NOT NULL,
  PRIMARY KEY  (`pk_comment`),
  KEY `fk_content` (`fk_content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contents`
--

CREATE TABLE IF NOT EXISTS `contents` (
  `pk_content` bigint(20) unsigned NOT NULL auto_increment,
  `fk_content_type` int(10) unsigned NOT NULL,
  `title` varchar(255) character set utf8 collate utf8_spanish_ci default NULL,
  `description` text character set utf8 collate utf8_spanish_ci,
  `metadata` varchar(255) character set utf8 collate utf8_spanish_ci default NULL,
  `starttime` datetime default NULL,
  `endtime` datetime default NULL,
  `created` datetime default NULL,
  `changed` datetime default NULL,
  `content_status` int(10) unsigned default '0',
  `fk_author` int(10) unsigned default NULL COMMENT 'Clave foranea de user',
  `fk_publisher` int(10) unsigned default NULL COMMENT 'Clave foranea de user',
  `fk_user_last_editor` int(10) unsigned default NULL COMMENT 'Clave foranea de user',
  `views` int(10) unsigned default NULL,
  `archive` int(10) unsigned default NULL,
  `position` int(10) unsigned default '100',
  `frontpage` tinyint(1) default '1',
  `in_litter` tinyint(1) default '0' COMMENT '0publicado 1papelera',
  `in_home` smallint(1) default '0',
  `home_pos` int(10) default '100' COMMENT '10',
  `permalink` varchar(255) default NULL,
  `available` smallint(1) default '1',
  `placeholder` varchar(64) default NULL COMMENT 'Placeholder for a content in frontpage',
  `home_placeholder` varchar(64) default NULL COMMENT 'Placeholder for a content in home',
  `paper_page` smallint(4) default '0' COMMENT 'news page from importXML',
  PRIMARY KEY  (`pk_content`),
  KEY `fk_content_type` (`fk_content_type`),
  KEY `in_litter` (`in_litter`),
  KEY `content_status` (`content_status`),
  KEY `in_home` (`in_home`),
  KEY `frontpage` (`frontpage`),
  KEY `available` (`available`),
  KEY `starttime` (`starttime`,`endtime`),
  KEY `created` (`created`),
  FULLTEXT KEY `metadata` (`metadata`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9223372036854775807 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contents_categories`
--

CREATE TABLE IF NOT EXISTS `contents_categories` (
  `pk_fk_content` bigint(20) unsigned NOT NULL,
  `pk_fk_content_category` int(10) unsigned NOT NULL,
  `catName` varchar(250) default NULL,
  PRIMARY KEY  (`pk_fk_content`,`pk_fk_content_category`),
  KEY `pk_fk_content_category` (`pk_fk_content_category`),
  KEY `catName` (`catName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `content_categories`
--

CREATE TABLE IF NOT EXISTS `content_categories` (
  `pk_content_category` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) default NULL,
  `inmenu` int(10) default '0',
  `posmenu` int(10) default '10',
  `internal_category` smallint(1) NOT NULL default '1' COMMENT '0 no se ve, 1 se ve, 2 especial se ve pero no se modifica, 3 albumcategory, 4 categorias de kiosko.',
  `fk_content_category` int(10) default '0',
  `logo_path` varchar(200) default NULL,
  `color` varchar(10) default '#638F38',
  PRIMARY KEY  (`pk_content_category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=296 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `content_types`
--

CREATE TABLE IF NOT EXISTS `content_types` (
  `pk_content_type` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `title` varchar(100) NOT NULL COMMENT 'utilizado en permalink',
  `fk_template_default` int(10) unsigned default NULL,
  PRIMARY KEY  (`pk_content_type`),
  KEY `fk_template_default` (`fk_template_default`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `pk_evento` bigint(20) unsigned NOT NULL auto_increment,
  `fk_content_categories` int(10) unsigned NOT NULL default '8',
  `summary` text,
  `body` text,
  `img` varchar(150) default NULL,
  PRIMARY KEY  (`pk_evento`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2414 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kioskos`
--

CREATE TABLE IF NOT EXISTS `kioskos` (
  `pk_kiosko` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `path` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `favorite` tinyint(1) NOT NULL,
  PRIMARY KEY  (`pk_kiosko`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `newsletter_archive`
--

CREATE TABLE IF NOT EXISTS `newsletter_archive` (
  `pk_newsletter` int(11) NOT NULL auto_increment,
  `data` longtext,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`pk_newsletter`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opinions`
--

CREATE TABLE IF NOT EXISTS `opinions` (
  `pk_opinion` bigint(20) unsigned NOT NULL auto_increment,
  `fk_content_categories` int(10) unsigned default '7',
  `fk_author` int(10) default NULL,
  `body` text,
  `fk_author_img` int(10) default NULL,
  `with_comment` smallint(1) default '1',
  `type_opinion` varchar(150) default NULL,
  `fk_author_img_widget` int(10) default NULL,
  PRIMARY KEY  (`pk_opinion`),
  KEY `type_opinion` (`type_opinion`),
  KEY `fk_author` (`fk_author`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2010100817433600622 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pclave`
--

CREATE TABLE IF NOT EXISTS `pclave` (
  `id` int(8) unsigned NOT NULL auto_increment,
  `pclave` varchar(60) NOT NULL,
  `value` varchar(240) default NULL,
  `tipo` varchar(20) NOT NULL default 'intsearch',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=682 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `photos`
--

CREATE TABLE IF NOT EXISTS `photos` (
  `pk_photo` bigint(20) unsigned NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `path_file` varchar(150) NOT NULL,
  `date` varchar(255) default NULL,
  `size` float default NULL,
  `resolution` varchar(100) default NULL,
  `width` int(10) default NULL,
  `height` int(10) default NULL,
  `nameCat` varchar(250) default '1',
  `type_img` varchar(5) default NULL,
  `author_name` varchar(200) default NULL,
  `media_type` enum('image','graphic') NOT NULL default 'image' COMMENT 'imagen o grafico',
  `color` varchar(10) default NULL COMMENT 'BN, color',
  `address` varchar(255) default NULL,
  PRIMARY KEY  (`pk_photo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2010101922464500317 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `polls`
--

CREATE TABLE IF NOT EXISTS `polls` (
  `pk_poll` bigint(20) unsigned NOT NULL,
  `total_votes` int(11) NOT NULL default '0',
  `used_ips` longtext,
  `subtitle` varchar(150) default NULL,
  `visualization` smallint(1) default NULL,
  `favorite` tinyint(1) default NULL,
  PRIMARY KEY  (`pk_poll`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `poll_items`
--

CREATE TABLE IF NOT EXISTS `poll_items` (
  `pk_item` int(10) unsigned NOT NULL auto_increment,
  `fk_pk_poll` bigint(20) unsigned NOT NULL,
  `item` varchar(255) NOT NULL,
  `metadata` varchar(250) default NULL,
  `votes` int(10) unsigned default '0',
  PRIMARY KEY  (`pk_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `privileges`
--

CREATE TABLE IF NOT EXISTS `privileges` (
  `pk_privilege` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) default NULL,
  `module` varchar(100) character set utf8 collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`pk_privilege`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ratings`
--

CREATE TABLE IF NOT EXISTS `ratings` (
  `pk_rating` bigint(20) unsigned NOT NULL,
  `total_votes` smallint(5) unsigned NOT NULL default '0',
  `total_value` mediumint(8) unsigned NOT NULL default '0',
  `ips_count_rating` longtext,
  PRIMARY KEY  (`pk_rating`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `related_contents`
--

CREATE TABLE IF NOT EXISTS `related_contents` (
  `pk_content1` bigint(20) unsigned NOT NULL,
  `pk_content2` bigint(20) unsigned NOT NULL,
  `relationship` varchar(50) default NULL,
  `text` varchar(50) default NULL,
  `position` int(10) NOT NULL default '0',
  `posinterior` int(2) NOT NULL default '0',
  `verportada` int(2) NOT NULL default '0',
  `verinterior` int(2) NOT NULL default '0',
  KEY `pk_content1` (`pk_content1`),
  KEY `verportada` (`verportada`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `opinion_algoritm` varchar(50) NOT NULL COMMENT 'forma visualizacion front sexo, tendencia, position'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `static_pages`
--

CREATE TABLE IF NOT EXISTS `static_pages` (
  `pk_static_page` bigint(20) NOT NULL COMMENT 'BIGINT(20)',
  `body` text NOT NULL COMMENT 'HTML content for static page',
  `slug` varchar(255) NOT NULL,
  PRIMARY KEY  (`pk_static_page`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `pk_user` int(10) unsigned NOT NULL auto_increment,
  `online` tinyint(1) NOT NULL default '0',
  `login` varchar(100) default NULL,
  `password` varchar(50) default NULL,
  `sessionexpire` tinyint(2) unsigned NOT NULL default '15',
  `email` varchar(255) default NULL,
  `name` varchar(100) default NULL,
  `firstname` varchar(100) default NULL,
  `lastname` varchar(100) default NULL,
  `address` varchar(255) default NULL,
  `phone` varchar(15) default NULL,
  `fk_user_group` int(10) unsigned default NULL,
  PRIMARY KEY  (`pk_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=121 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_content_categories`
--

CREATE TABLE IF NOT EXISTS `users_content_categories` (
  `pk_fk_user` int(10) unsigned NOT NULL,
  `pk_fk_content_category` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`pk_fk_user`,`pk_fk_content_category`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `pk_user_group` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`pk_user_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_groups_privileges`
--

CREATE TABLE IF NOT EXISTS `user_groups_privileges` (
  `pk_fk_user_group` int(10) unsigned NOT NULL,
  `pk_fk_privilege` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`pk_fk_user_group`,`pk_fk_privilege`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `videos`
--

CREATE TABLE IF NOT EXISTS `videos` (
  `pk_video` bigint(20) unsigned NOT NULL auto_increment,
  `videoid` varchar(20) NOT NULL,
  `htmlcode` mediumtext,
  `author_name` varchar(200) default NULL,
  PRIMARY KEY  (`pk_video`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2010090712555800730 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `votes`
--

CREATE TABLE IF NOT EXISTS `votes` (
  `pk_vote` bigint(20) NOT NULL,
  `value_pos` smallint(4) NOT NULL default '0',
  `value_neg` smallint(4) NOT NULL default '0',
  `ips_count_vote` longtext,
  `karma` int(10) unsigned default '100',
  PRIMARY KEY  (`pk_vote`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

